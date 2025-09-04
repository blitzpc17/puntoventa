<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fiado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "Fiados";

    protected $fillable = [
        'cliente_id',
        'venta_id',
        'monto_total',
        'saldo_pendiente',
        'estado',
        'fecha_limite',
        'notas'
    ];

    protected $casts = [
        'monto_total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_limite' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con la venta
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    /**
     * Relación con los abonos
     */
    public function abonos()
    {
        return $this->hasMany(AbonoFiado::class);
    }

    /**
     * Scope para fiados pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * Scope para fiados vencidos
     */
    public function scopeVencidos($query)
    {
        return $query->where('estado', 'pendiente')
                    ->where('fecha_limite', '<', now());
    }

    /**
     * Verificar si el fiado está vencido
     */
    public function getEstaVencidoAttribute()
    {
        return $this->estado === 'pendiente' && $this->fecha_limite < now();
    }

    /**
     * Registrar un abono
     */
    public function registrarAbono($monto, $notas = null)
    {
        $this->saldo_pendiente -= $monto;

        if ($this->saldo_pendiente <= 0) {
            $this->estado = 'pagado';
            $this->saldo_pendiente = 0;
        }

        $this->save();

        // Crear registro de abono
        AbonoFiado::create([
            'fiado_id' => $this->id,
            'monto' => $monto,
            'notas' => $notas
        ]);

        return $this;
    }
}