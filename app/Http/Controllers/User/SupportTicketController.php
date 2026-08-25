<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\SupportTicket;
use App\Model\SupportTicketReply;
use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the support tickets.
     */
    public function index()
    {
        $user = Auth::user();
        $tickets = SupportTicket::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('user.support.tickets', compact('tickets'));
    }

    /**
     * Show the form for creating a new support ticket.
     */
    public function create()
    {
        return view('user.support.create');
    }

    /**
     * Store a newly created support ticket in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255|min:5',
            'category' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string|min:10',
        ], [
            'subject.required' => 'Please enter a subject for your ticket',
            'subject.min' => 'Subject must be at least 5 characters',
            'category.required' => 'Please select a category',
            'priority.required' => 'Please select a priority level',
            'description.required' => 'Please provide a description of your issue',
            'description.min' => 'Description must be at least 10 characters',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        $user = Auth::user();

        try {
            $ticket = SupportTicket::create([
                'ticket_number' => SupportTicket::generateTicketNumber(),
                'user_id' => $user->id,
                'subject' => $request->subject,
                'category' => $request->category,
                'priority' => $request->priority,
                'description' => $request->description,
                'status' => 'open',
                'created_by' => $user->id,
            ]);

            return redirect()->route('user.support.tickets.show', $ticket->id)
                ->with('success', 'Support ticket created successfully. Ticket ID: ' . $ticket->ticket_number);
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create ticket. Please try again.');
        }
    }

    /**
     * Display the specified support ticket.
     */
    public function show($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)
            ->with(['replies.user', 'assignedAgent'])
            ->findOrFail($id);

        return view('user.support.show', compact('ticket'));
    }

    /**
     * Store a new reply for the support ticket.
     */
    public function storeReply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:5',
        ], [
            'message.required' => 'Please enter your message',
            'message.min' => 'Message must be at least 5 characters',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        try {
            // Update ticket last replied time
            $ticket->update([
                'last_replied_at' => now(),
                'status' => $ticket->status === 'closed' ? 'open' : ($ticket->status === 'open' ? 'in_progress' : $ticket->status),
            ]);

            // Create reply
            SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => $request->message,
                'is_internal_note' => false,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
            ]);

            return redirect()->back()
                ->with('success', 'Reply added successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add reply. Please try again.');
        }
    }

    /**
     * Close a support ticket.
     */
    public function closeTicket($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        if ($ticket->status !== 'closed') {
            $ticket->update(['status' => 'closed']);
            
            // Add automatic reply
            SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => 'Ticket closed by user.',
                'is_internal_note' => false,
                'ip_address' => request()->ip(),
            ]);

            return redirect()->back()
                ->with('success', 'Ticket closed successfully.');
        }

        return redirect()->back()
            ->with('info', 'Ticket is already closed.');
    }

    /**
     * Reopen a closed support ticket.
     */
    public function reopenTicket($id)
    {
        $user = Auth::user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
            
            // Add automatic reply
            SupportTicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'message' => 'Ticket reopened by user.',
                'is_internal_note' => false,
                'ip_address' => request()->ip(),
            ]);

            return redirect()->back()
                ->with('success', 'Ticket reopened successfully.');
        }

        return redirect()->back()
            ->with('info', 'Ticket is already open.');
    }

    /**
     * Get ticket statistics for dashboard.
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total' => SupportTicket::where('user_id', $user->id)->count(),
            'open' => SupportTicket::where('user_id', $user->id)->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'closed' => SupportTicket::where('user_id', $user->id)->where('status', 'closed')->count(),
        ];

        return response()->json($stats);
    }
}