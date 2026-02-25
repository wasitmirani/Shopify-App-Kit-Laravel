<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    public const FREE_PLAN = 'STARTER';

    public function plan_features()
    {
        return $this->hasMany(PlanFeature::class, 'plan_id', 'id');
    }
}
