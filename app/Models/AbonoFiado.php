<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbonoFiado extends Model
{
    use HasFactory;

    protected $table = "AbonoFiado";

    protected $fillable = [
        'fiado_id',
        'monto',
        'notas'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el fiado
     */
    public function fiado()
    {
        return $this->belongsTo(Fiado::class);
    }
}