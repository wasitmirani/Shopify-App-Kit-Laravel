<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomIcon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'url',
    ];
}
