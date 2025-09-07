<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $table = 'about';

    protected $fillable = [
        'content',
        'milestones',
        'updated_by_user_id',
    ];

    protected $casts = [
        'milestones' => 'array',
    ];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function getSingle()
    {
        return self::first() ?: new self();
    }
}