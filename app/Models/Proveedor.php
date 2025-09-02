<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "Proveedores";

    protected $fillable = [
        'nombre',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'rfc',
        'notas'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con los productos
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Relación con las compras
     */
    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    /**
     * Scope para buscar proveedores
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where('nombre', 'like', "%{$search}%")
                        ->orWhere('contacto', 'like', "%{$search}%")
                        ->orWhere('rfc', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
        }
        return $query;
    }

    /**
     * Scope para proveedores activos
     */
    public function scopeActivos($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Obtener el total de compras del proveedor
     */
    public function getTotalComprasAttribute()
    {
        return $this->compras()->sum('total');
    }

    /**
     * Obtener la última compra del proveedor
     */
    public function getUltimaCompraAttribute()
    {
        return $this->compras()->latest()->first();
    }

    /**
     * Verificar si el proveedor tiene productos asociados
     */
    public function getTieneProductosAttribute()
    {
        return $this->productos()->count() > 0;
    }

    /**
     * Verificar si el proveedor tiene compras asociadas
     */
    public function getTieneComprasAttribute()
    {
        return $this->compras()->count() > 0;
    }
}