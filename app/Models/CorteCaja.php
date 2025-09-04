<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorteCaja extends Model
{
    use HasFactory;

    protected $table ="cortes_caja";

    protected $fillable = [
       // 'usuario_id',
        'fecha',
        'monto_inicial',
        'monto_final',
        'ventas_efectivo',
        'ventas_tarjeta',
        'ventas_fiado',
        'total_ventas',
        'diferencia',
        'estado',
        'notas'
    ];

    protected $casts = [
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'ventas_efectivo' => 'decimal:2',
        'ventas_tarjeta' => 'decimal:2',
        'ventas_fiado' => 'decimal:2',
        'total_ventas' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para cortes del día
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }

    /**
     * Scope para cortes abiertos
     */
    public function scopeAbiertos($query)
    {
        return $query->where('estado', 'abierto');
    }

    /**
     * Calcular el total esperado en caja
     */
    public function getTotalEsperadoAttribute()
    {
        return $this->monto_inicial + $this->ventas_efectivo;
    }

    /**
     * Calcular la diferencia
     */
    public function calcularDiferencia()
    {
        return $this->monto_final - $this->total_esperado;
    }

    /**
     * Verificar si el corte está abierto
     */
    public function getEstaAbiertoAttribute()
    {
        return $this->estado === 'abierto';
    }
}