<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "ventas";

    protected $fillable = [
        'cliente_id',
        //'usuario_id',
        'folio',
        'total',
        'efectivo',
        'cambio',
        'tipo_pago',
        'estado',
        'fecha'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'efectivo' => 'decimal:2',
        'cambio' => 'decimal:2',
        'fecha' => 'datetime',
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
     * Relación con el usuario (vendedor)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con los detalles de venta
     */
    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Relación con fiados
     */
    public function fiado()
    {
        return $this->hasOne(Fiado::class);
    }

    /**
     * Scope para ventas del día
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }

    /**
     * Scope para ventas por tipo de pago
     */
    public function scopePorTipoPago($query, $tipo)
    {
        return $query->where('tipo_pago', $tipo);
    }

    /**
     * Generar folio automático
     */
    public static function generarFolio()
    {
        $ultimaVenta = self::orderBy('id', 'desc')->first();
        $numero = $ultimaVenta ? $ultimaVenta->id + 1 : 1;
        return 'VENT-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Obtener el total de productos vendidos
     */
    public function getTotalProductosAttribute()
    {
        return $this->detalles->sum('cantidad');
    }

    /**
     * Verificar si la venta es a crédito (fiado)
     */
    public function getEsFiadoAttribute()
    {
        return $this->tipo_pago === 'fiado';
    }
}