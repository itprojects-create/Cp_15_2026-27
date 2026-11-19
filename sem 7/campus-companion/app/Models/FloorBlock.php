<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorBlock extends Model
{
    protected $fillable = [
        'floor_id',
        'block_number',
        'title',
        'class_room_id',
        'x_grid',
        'y_grid',
        'width_grid',
        'height_grid',
        'color'
    ];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }
}
