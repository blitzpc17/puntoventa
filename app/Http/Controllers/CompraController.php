<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $compras = Compra::with('proveedor')->select('*');
            
            return DataTables::of($compras)
                ->addColumn('proveedor_nombre', function($compra) {
                    return $compra->proveedor ? $compra->proveedor->nombre : 'N/A';
                })
                ->addColumn('fecha_formateada', function($compra) {
                    return $compra->fecha->format('d/m/Y');
                })
                ->addColumn('total_formateado', function($compra) {
                    return '$' . number_format($compra->total, 2);
                })
                ->addColumn('estado_label', function($compra) {
                    $badge = $compra->estado == 'completada' 
                        ? '<span class="badge bg-success">Completada</span>'
                        : '<span class="badge bg-warning">Pendiente</span>';
                    return $badge;
                })
                ->addColumn('action', function($compra) {
                    return '<a href="' . route('compras.show', $compra->id) . '" class="btn btn-sm btn-info" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete" data-id="'.$compra->id.'" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>';
                })
                ->rawColumns(['estado_label', 'action'])
                ->make(true);
        }
        
        $proveedores = Proveedor::activos()->get();
        return view('compras.index', compact('proveedores'));
    }

    public function create()
    {
        /*$proveedores = Proveedor::activos()->get();
        $productos = Producto::select('id', 'nombre', 'precio_compra', 'existencia')->get();
        
        return view('compras.create', compact('proveedores', 'productos'));*/

        $proveedores = Proveedor::activos()->get();
        return view('compras.create', compact('proveedores'));


    }

    public function store(Request $request)
{
    DB::beginTransaction();
    
    try {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'fecha' => 'required|date',
            'productos' => 'required|array|min:1',
            'productos.*.id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio' => 'required|numeric|min:0',
            'notas' => 'nullable|string'
        ]);

        // Crear la compra
        $compra = Compra::create([
            'proveedor_id' => $request->proveedor_id,
            'folio' => Compra::generarFolio(),
            'fecha' => $request->fecha,
            'total' => 0,
            'estado' => 'completada',
            'notas' => $request->notas
        ]);

        $total = 0;

        // Crear detalles de compra
        foreach ($request->productos as $productoData) {
            $subtotal = $productoData['cantidad'] * $productoData['precio'];
            $total += $subtotal;

            DetalleCompra::create([
                'compra_id' => $compra->id,
                'producto_id' => $productoData['id'],
                'cantidad' => $productoData['cantidad'],
                'precio' => $productoData['precio'],
                'subtotal' => $subtotal
            ]);

            // Actualizar existencia y precio de compra
            $producto = Producto::find($productoData['id']);
            $producto->existencia += $productoData['cantidad'];
            $producto->precio_compra = $productoData['precio'];
            $producto->save();
        }

        // Actualizar total
        $compra->total = $total;
        $compra->save();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Compra registrada correctamente',
            'compra_id' => $compra->id
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al registrar la compra: ' . $e->getMessage()
        ], 500);
    }
}

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'detalles.producto'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $compra = Compra::with('detalles')->findOrFail($id);
            
            // Revertir existencias de productos
            foreach ($compra->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $producto->existencia -= $detalle->cantidad;
                $producto->save();
            }
            
            $compra->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compra eliminada correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la compra: ' . $e->getMessage()
            ], 500);
        }
    }


    





}