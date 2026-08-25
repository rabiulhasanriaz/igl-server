<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketAttachment extends Model
{
    use SoftDeletes;

    protected $table = 'support_ticket_attachments';
    
    protected $fillable = [
        'ticket_id',
        'reply_id',
        'user_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
    ];

    protected $dates = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the ticket this attachment belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    /**
     * Get the reply this attachment belongs to (if attached to a reply)
     */
    public function reply(): BelongsTo
    {
        return $this->belongsTo(SupportTicketReply::class, 'reply_id');
    }

    /**
     * Get the user who uploaded this attachment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}