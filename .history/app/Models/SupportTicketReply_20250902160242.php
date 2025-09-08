<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicketReply extends Model
{
    use HasFactory;
    protected $guarded = [];


    // İlişkiler
    public function supportTicket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Boot method - ticket'ı güncellemek için
    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->supportTicket()->update([
                'last_reply_at' => $reply->created_at,
                'status' => $reply->is_admin_reply ? 'cevaplandi' : 'acik'
            ]);
        });
    }
}