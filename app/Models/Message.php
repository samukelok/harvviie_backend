<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'type',
        'status',
    ];

    // Type constants
    const TYPE_CONTACT = 'contact';
    const TYPE_SERVICE_REQUEST = 'service_request';

    // Status constants
    const STATUS_NEW = 'new';
    const STATUS_READ = 'read';
    const STATUS_CLOSED = 'closed';

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByType($query, $type)
    {
        if ($type) {
            return $query->where('type', $type);
        }
        return $query;
    }
}