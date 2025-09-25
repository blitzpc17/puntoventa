<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
//se comenta porque no se usaron las valicaicones
/*use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;*/
use Illuminate\Validation\Rule;

class ProductsImport implements ToModel, WithHeadingRow//, WithValidation, WithBatchInserts, WithChunkReading
{
   public function model(array $row)
    {
        return new Product([
            'codigo'        => $row['CODIGO'] ?? $row['CODIGO'] ?? null,                    
            'nombre'       => $row['NOMBRE'] ?? $row['NOMBRE'] ??   null,
            'descripcion'       => $row['DESCRIPCION'] ?? $row['DESCRIPCION'] ??   null,
            'precioVenta'       => $row['PRECIO_VENTA'] ?? $row['PRECIO_VENTA'] ?? 0,
            'precioCompra'       => $row['PRECIO_COMPRA'] ?? $row['PRECIO_COMPRA'] ?? 0,
            'cantidad'       => $row['CANTIDAD'] ?? $row['CANTIDAD'] ?? 0,
            'existenciaMinima'    => $row['EXISTENCIA_MINIMA'] ?? $row['EXISTENCIA_MINIMA'] ?? 0,
        ]);
    }
/*
    public function rules(): array
    {
        return [
            '*.codigo' => 'required|string|max:50',
            '*.proveedor' => 'required|string',
            '*.categoria' => 'required|string',
            '*.nombre' => 'required|string|max:100',
            '*.descripcion' => 'string|max:350' ,
            '*.precioVenta' => 'required|numeric',
            '*.precioCompra' => 'required|numeric',
            '*.cantidad' => 'required|numeric',
            '*.existenciaMinima' => 'numeric'
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function customValidationMessages()
    {
        return [
            'codigo.required' => 'El codigo es obligatorio.',
            'codigo.string' => 'El codigo solo acepta texto.',
            'codigo.max'=> 'El codigo no debe superar los 50 carácteres.',
            'proveedor.required' => 'El proveedor es Obligatorio',
            'proveedor.string' => 'El proveedor debe ser texto.',
            'categoria.required' => 'La categoría es obligatoría.',
            'categoria.string' => 'La categoría debe ser texto',
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.string' => 'El nombre solo acepta texto.',
            'nombre.max' => 'El nombre no debe superar los 100 carácteres.',
            'descripcion.string' => 'La descripcion debe ser texto.',
            'descripcion.max' => 'La descripción no debe exceder los 350 carácteres.',
            'precioVenta.required' => 'El precio de venta es obligatorio.',
            'precioVenta.numeric' => 'El precio de venta solo acepta números.',
            'precioCompra.required' => 'El precio de compra es obligatorio.',
            'precioCompra.numeric' => 'El precio de compra solo acepta números.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.numeric' => 'La cantidad solo acepta números',          
            'existenciaMinima.numeric' => 'La existencia minima solo acepta números.',
        ];
    }*/
}
