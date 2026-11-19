<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partida extends Model
{
    protected $fillable = [
        'jugadorX_id',
        'jugadorO_id',
        'tablero',
        'turno',
        'estado',
        'ganador_id',
        'resultado',
    ];

    public function jugadorX(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugadorX_id')->withTrashed();
    }

    public function jugadorO(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jugadorO_id')->withTrashed();
    }

    public function ganador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ganador_id')->withTrashed();
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(Movimiento::class);
    }
}