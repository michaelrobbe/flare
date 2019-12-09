<?php

namespace App\Flare\Models;

use Illuminate\Database\Eloquent\Model;
use App\Flare\Models\Item;

class EquippedItem extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'character_id',
        'position',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function item() {
        return $this->belongsTo(Item::class);
    }
}