<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorCount extends Model
{
    use HasFactory;

    protected $table = 'visitor_counts';

    protected $fillable = [
        'user_id',
        'month',
        'count',
        'created_at',
        'updated_at'
    ];
}
