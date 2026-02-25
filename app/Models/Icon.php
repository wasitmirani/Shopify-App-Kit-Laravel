<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use function info;

class Icon extends Model
{
    use HasFactory;

    protected $fillable = [
        'block_id',
        'icon_id',
        'icon_type',
        'title',
        'subtitle',
        'show_link',
        'link',
        'open_to_new_tab',
        'show_condition',
        'tags',
        'position',
    ];

    public function appIcon()
    {
        return $this->belongsTo(AppIcon::class, 'icon_id', 'id');
    }

    public function customIcon()
    {
        return $this->belongsTo(CustomIcon::class, 'icon_id', 'id');
    }

    public function icon()
    {
        //        info("--ICON",[$this->icon->icon_type]);
        return $this->belongsTo(AppIcon::class, 'icon_id', 'id');
        //        $this->scopeIcon1()
        return $this->scopeIcon1($this);
        //        return $this->belongsTo(CustomIcon::class,'icon_id','id');
    }

    public function scopeIcon1($query)
    {
        info('SCOPE TYPE', [$this->icon_type]);

        return $query
            ->when($this->type === 'agents', static fn ($q) => $q->with('agentProfile'))
            ->when($this->type === 'school', static fn ($q) => $q->with('schoolProfile'))
            ->when($this->type === 'academy', static fn ($q) => $q->with('academyProfile'), static fn ($q) => $q->with('institutionProfile'));
    }
}
