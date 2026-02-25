<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_enabled',
        'name',
        'layout',
        'header_text',
        'position',
        'icons_per_row_desktop',
        'icons_per_row_mobile',
        'header_text_settings',
        'manual_placement_id',
        'size',
        'color_settings',
        'typography_settings',
        'block_size',
        'goes_up',
        'goes_down',
        'space_between_blocks',
    ];
    protected $casts = [
        'header_text_settings' => 'array',
        'color_settings' => 'array',
        'typography_settings' => 'array',
    ];

    public function icons()
    {
        return $this->hasMany(Icon::class, 'block_id', 'id');
    }

    public function appIcons()
    {
        //        info("type BLOCK",[$this->all()]);
        return $this->hasMany(Icon::class, 'block_id', 'id')->where('icon_type', 'app-icon');
    }

    public function customIcons()
    {
        //        info("type BLOCK",[$this->all()]);
        return $this->hasMany(Icon::class, 'block_id', 'id')->where('icon_type', 'custom');
    }

    public function selected_products()
    {
        return $this->belongsToMany(Product::class, 'selected_products', 'block_id', 'product_id')->withTimestamps();
    }

    public function selected_collections()
    {
        return $this->belongsToMany(Collection::class, 'selected_collections', 'block_id', 'collection_id')->withTimestamps();
    }
}
