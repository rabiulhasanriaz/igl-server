<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\SupportTicket;
use App\Model\SupportTicketReply;
use App\Model\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminTicketController extends Controller
{
    /**
     * Display all support tickets for admin
     */
   public function index(Request $request)
{
    $query = SupportTicket::with(['user', 'assignedAgent']);
    
    // Search functionality
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('ticket_number', 'like', "%{$search}%")
              ->orWhere('subject', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
    
    // Filter by status
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }
    
    // Filter by priority
    if ($request->has('priority') && $request->priority != '') {
        $query->where('priority', $request->priority);
    }
    
    // Filter by category
    if ($request->has('category') && $request->category != '') {
        $query->where('category', $request->category);
    }
    
    // Filter by assigned agent
    if ($request->has('assigned_to') && $request->assigned_to != '') {
        if ($request->assigned_to == 'unassigned') {
            $query->whereNull('assigned_to');
        } else {
            $query->where('assigned_to', $request->assigned_to);
        }
    }
    
    $tickets = $query->orderBy('created_at', 'desc')->paginate(20);
    
    // Get all admin users for assign dropdown
    $admins = User::where('role', 1)->get();
    
    // Get unique categories for filter
    $categories = SupportTicket::distinct()->pluck('category');
    
    // Get statistics for display
    $stats = [
        'total' => SupportTicket::count(),
        'open' => SupportTicket::where('status', 'open')->count(),
        'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
        'unassigned' => SupportTicket::whereNull('assigned_to')->count(),
        'assigned_to_me' => SupportTicket::where('assigned_to', Auth::id())->count(),
        'high_priority' => SupportTicket::whereIn('priority', ['high', 'urgent'])->count(),
    ];
    
    return view('admin.support.tickets', compact('tickets', 'admins', 'categories', 'stats'));
}
    /**
     * Display ticket details for admin
     */
    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'assignedAgent', 'replies.user'])
            ->findOrFail($id);
        
        // Get all admin users for assign dropdown
        $admins = User::where('role', 1)->get();
        
        return view('admin.support.show', compact('ticket', 'admins'));
    }

    /**
     * Assign ticket to admin/staff
     */
    public function assignTicket(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        $ticket = SupportTicket::findOrFail($id);
        
        $ticket->update([
            'assigned_to' => $request->assigned_to,
            'status' => 'in_progress',
        ]);
        
        // Add internal note about assignment
        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => 'Ticket assigned to staff member.',
            'is_internal_note' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Ticket assigned successfully.');
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:open,in_progress,on_hold,resolved,closed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        $ticket = SupportTicket::findOrFail($id);
        $oldStatus = $ticket->status;
        
        $ticket->update(['status' => $request->status]);
        
        // Add internal note about status change
        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => "Ticket status changed from {$oldStatus} to {$request->status}.",
            'is_internal_note' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Ticket status updated successfully.');
    }

    /**
     * Update ticket priority
     */
    public function updatePriority(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        $ticket = SupportTicket::findOrFail($id);
        $oldPriority = $ticket->priority;
        
        $ticket->update(['priority' => $request->priority]);
        
        // Add internal note about priority change
        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => "Ticket priority changed from {$oldPriority} to {$request->priority}.",
            'is_internal_note' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Ticket priority updated successfully.');
    }

    /**
     * Add admin reply to ticket
     */
    public function storeReply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:5',
            'is_internal_note' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the errors below.');
        }

        $ticket = SupportTicket::findOrFail($id);

        // Update ticket last replied time
        $ticket->update([
            'last_replied_at' => now(),
            'status' => 'in_progress',
        ]);

        // Create reply
        SupportTicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_internal_note' => $request->is_internal_note ?? false,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return redirect()->back()
            ->with('success', 'Reply added successfully.');
    }

    /**
     * Delete ticket (admin only)
     */
    public function destroy($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('admin.support.tickets')
            ->with('success', 'Ticket deleted successfully.');
    }

    /**
     * Get ticket statistics for admin dashboard
     */
    public function getStats()
    {
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'unassigned' => SupportTicket::whereNull('assigned_to')->count(),
            'assigned_to_me' => SupportTicket::where('assigned_to', Auth::id())->count(),
        ];

        // Weekly ticket statistics
        $weeklyStats = SupportTicket::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Priority distribution
        $priorityStats = SupportTicket::select(
                'priority',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('priority')
            ->get();

        return response()->json([
            'stats' => $stats,
            'weeklyStats' => $weeklyStats,
            'priorityStats' => $priorityStats,
        ]);
    }

    /**
     * Get tickets assigned to current admin
     */
    public function myAssignedTickets(Request $request)
    {
        $query = SupportTicket::where('assigned_to', Auth::id())
            ->with(['user']);
            
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get statistics for my tickets
        $stats = [
            'total' => SupportTicket::where('assigned_to', Auth::id())->count(),
            'open' => SupportTicket::where('assigned_to', Auth::id())->where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('assigned_to', Auth::id())->where('status', 'in_progress')->count(),
            'high_priority' => SupportTicket::where('assigned_to', Auth::id())->whereIn('priority', ['high', 'urgent'])->count(),
        ];
        
        return view('admin.support.my-tickets', compact('tickets', 'stats'));
    }

    /**
     * Get unassigned tickets
     */
    public function unassignedTickets(Request $request)
    {
        $query = SupportTicket::whereNull('assigned_to')
            ->with(['user']);
            
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority != 'all') {
            $query->where('priority', $request->priority);
        }
        
        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get all admin users for assign dropdown
        $admins = User::where('role', 1)->get();
        
        // Get statistics for unassigned tickets
        $stats = [
            'total' => $tickets->total(),
            'open' => SupportTicket::whereNull('assigned_to')->where('status', 'open')->count(),
            'pending' => SupportTicket::whereNull('assigned_to')->where('status', '!=', 'closed')->count(),
            'urgent' => SupportTicket::whereNull('assigned_to')->where('priority', 'urgent')->count(),
        ];
        
        return view('admin.support.unassigned-tickets', compact('tickets', 'admins', 'stats'));
    }

    /**
     * Bulk assign tickets
     */
    public function bulkAssign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
            'assigned_to' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updatedCount = SupportTicket::whereIn('id', $request->ticket_ids)
            ->update([
                'assigned_to' => $request->assigned_to,
                'status' => 'in_progress',
            ]);
            
        // Add internal notes for each ticket
        foreach ($request->ticket_ids as $ticketId) {
            SupportTicketReply::create([
                'ticket_id' => $ticketId,
                'user_id' => Auth::id(),
                'message' => 'Ticket assigned via bulk action.',
                'is_internal_note' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} tickets assigned successfully.",
        ]);
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
            'status' => 'required|in:open,in_progress,on_hold,resolved,closed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updatedCount = SupportTicket::whereIn('id', $request->ticket_ids)
            ->update(['status' => $request->status]);
            
        // Add internal notes for each ticket
        foreach ($request->ticket_ids as $ticketId) {
            SupportTicketReply::create([
                'ticket_id' => $ticketId,
                'user_id' => Auth::id(),
                'message' => "Ticket status updated to {$request->status} via bulk action.",
                'is_internal_note' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} tickets status updated successfully.",
        ]);
    }

    /**
     * Bulk update priority
     */
    public function bulkUpdatePriority(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updatedCount = SupportTicket::whereIn('id', $request->ticket_ids)
            ->update(['priority' => $request->priority]);
            
        // Add internal notes for each ticket
        foreach ($request->ticket_ids as $ticketId) {
            SupportTicketReply::create([
                'ticket_id' => $ticketId,
                'user_id' => Auth::id(),
                'message' => "Ticket priority updated to {$request->priority} via bulk action.",
                'is_internal_note' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} tickets priority updated successfully.",
        ]);
    }

    /**
     * Bulk delete tickets
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:support_tickets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $deletedCount = SupportTicket::whereIn('id', $request->ticket_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} tickets deleted successfully.",
        ]);
    }
}
