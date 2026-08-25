<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicketReply extends Model
{
    use SoftDeletes;

    protected $table = 'support_ticket_replies';
    
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal_note',
        'ip_address',
        'user_agent',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_internal_note' => 'boolean',
    ];

    /**
     * Get the ticket this reply belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the user who posted this reply
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get attachments for this reply
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class, 'reply_id');
    }
}