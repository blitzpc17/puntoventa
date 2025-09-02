<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Fiado;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $ventas = Venta::with('cliente')->select('*');
            return DataTables::of($ventas)
                ->addColumn('action', function($venta) {
                    return '<a href="'.route('ventas.show', $venta->id).'" class="btn btn-sm btn-info">Ver</a>';
                })
                ->addColumn('cliente_nombre', function($venta) {
                    return $venta->cliente ? $venta->cliente->nombre : 'Cliente no registrado';
                })
                ->make(true);
        }
        
        return view('ventas.index');
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('ventas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'tipo_pago' => 'required|in:efectivo,tarjeta,fiado',
                'productos' => 'required|array',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1'
            ]);
            
            // Calcular total
            $total = 0;
            foreach ($request->productos as $productoData) {
                $producto = Producto::find($productoData['id']);
                $subtotal = $producto->precio_venta * $productoData['cantidad'];
                $total += $subtotal;
            }
            
            // Crear venta
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'total' => $total,
                'tipo_pago' => $request->tipo_pago,
                'fecha' => now()
            ]);
            
            // Crear detalles de venta y actualizar existencias
            foreach ($request->productos as $productoData) {
                $producto = Producto::find($productoData['id']);
                
                // Verificar existencia
                if ($producto->existencia < $productoData['cantidad']) {
                    throw new \Exception("No hay suficiente stock para el producto: " . $producto->nombre);
                }
                
                $subtotal = $producto->precio_venta * $productoData['cantidad'];
                
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio' => $producto->precio_venta,
                    'subtotal' => $subtotal
                ]);
                
                // Actualizar existencia
                $producto->existencia -= $productoData['cantidad'];
                $producto->save();
            }
            
            // Si es fiado, registrar en la tabla de fiados
            if ($request->tipo_pago == 'fiado') {
                Fiado::create([
                    'cliente_id' => $request->cliente_id,
                    'venta_id' => $venta->id,
                    'monto' => $total,
                    'saldo_pendiente' => $total,
                    'estado' => 'pendiente',
                    'fecha_limite' => now()->addDays(30)
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta_id' => $venta->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la venta: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto'])->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }
}