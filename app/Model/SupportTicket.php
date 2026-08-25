<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use SoftDeletes;

    protected $table = 'support_tickets';
    
    protected $fillable = [
        'ticket_number',
        'user_id',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'assigned_to',
        'created_by',
        'last_replied_at',
    ];

    protected $dates = [
        'last_replied_at',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'last_replied_at' => 'datetime',
    ];

    /**
     * Get the user who created/owns the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who created the ticket entry
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assigned support agent
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get all replies/messages for this ticket
     */
    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    /**
     * Get attachments for this ticket
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'ticket_id');
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for tickets assigned to a specific user
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope for tickets created by a specific user
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope for tickets owned by a specific user
     */
    public function scopeOwnedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if ticket is open
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Check if ticket is assigned
     */
    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    /**
     * Generate a unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TICKET-';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        
        return $prefix . $date . '-' . $random;
    }
}