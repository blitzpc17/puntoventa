<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "clientes";

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'direccion',
        'rfc',
        'limite_fiado',
        'estado'
    ];

    protected $casts = [
        'limite_fiado' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con las ventas
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Relación con los fiados
     */
    public function fiados()
    {
        return $this->hasMany(Fiado::class);
    }

    /**
     * Scope para clientes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para buscar clientes
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('rfc', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
    }

    /**
     * Obtener el saldo pendiente total del cliente
     */
    public function getSaldoPendienteAttribute()
    {
        return $this->fiados()->where('estado', 'pendiente')->sum('saldo_pendiente');
    }

    /**
     * Verificar si el cliente puede fiadar más
     */
    public function getPuedeFiadarAttribute()
    {
        if ($this->limite_fiado <= 0) {
            return false;
        }

        $saldoPendiente = $this->saldo_pendiente;
        return $saldoPendiente < $this->limite_fiado;
    }

    /**
     * Obtener el disponible para fiado
     */
    public function getDisponibleFiadoAttribute()
    {
        if ($this->limite_fiado <= 0) {
            return 0;
        }

        return max(0, $this->limite_fiado - $this->saldo_pendiente);
    }
}