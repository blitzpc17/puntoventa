<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compra extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "compras";

    protected $fillable = [
        'proveedor_id',
        'folio',
        'total',
        'fecha',
        'estado',
        'notas'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con el proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con los detalles de compra
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Scope para compras activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'completada');
    }

    /**
     * Scope para buscar compras
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('folio', 'like', "%{$search}%")
                    ->orWhereHas('proveedor', function($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
    }

    /**
     * Generar folio automático
     */
    public static function generarFolio()
    {
        $ultimaCompra = self::orderBy('id', 'desc')->first();
        $numero = $ultimaCompra ? $ultimaCompra->id + 1 : 1;
        return 'COMP-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calcular el total de la compra
     */
    public function calcularTotal()
    {
        return $this->detalles->sum('subtotal');
    }
}