<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = "Productos";

     protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'precio_compra',
        'precio_venta',
        'existencia',
        'min_existencia',
        'proveedor_id',
        'categoria'
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'existencia' => 'integer',
        'min_existencia' => 'integer'
    ];

    /**
     * Relación con el proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con los detalles de venta
     */
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Relación con los detalles de compra
     */
    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Scope para productos con stock bajo
     */
    public function scopeStockBajo($query)
    {
        return $query->whereColumn('existencia', '<=', 'min_existencia');
    }

    /**
     * Scope para productos agotados
     */
    public function scopeAgotados($query)
    {
        return $query->where('existencia', 0);
    }

    /**
     * Scope para productos disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('existencia', '>', 0);
    }

    /**
     * Scope para filtrar por categoría
     */
    public function scopeCategoria($query, $categoria)
    {
        if ($categoria) {
            return $query->where('categoria', $categoria);
        }
        return $query;
    }

    /**
     * Scope para filtrar por proveedor
     */
    public function scopeProveedor($query, $proveedor_id)
    {
        if ($proveedor_id) {
            return $query->where('proveedor_id', $proveedor_id);
        }
        return $query;
    }

    /**
     * Verificar si el stock es bajo
     */
    public function getStockBajoAttribute()
    {
        return $this->existencia > 0 && $this->existencia <= $this->min_existencia;
    }

    /**
     * Verificar si el producto está agotado
     */
    public function getAgotadoAttribute()
    {
        return $this->existencia == 0;
    }


}
