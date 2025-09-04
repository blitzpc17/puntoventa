<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Fiado;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $ventas = Venta::with(['cliente', 'usuario'])->select('*');
        
        // Aplicar filtros
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $ventas->whereDate('fecha', '>=', $request->fecha_desde);
        }
        
        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $ventas->whereDate('fecha', '<=', $request->fecha_hasta);
        }
        
        if ($request->has('tipo_pago') && $request->tipo_pago) {
            $ventas->where('tipo_pago', $request->tipo_pago);
        }
        
        if ($request->has('cliente_id') && $request->cliente_id) {
            $ventas->where('cliente_id', $request->cliente_id);
        }
        
        if ($request->has('solo_hoy') && $request->solo_hoy) {
            $ventas->whereDate('fecha', today());
        }
        
        // Exportar datos
        if ($request->has('exportar')) {
            return $this->exportarVentas($ventas->get(), $request->formato);
        }
        
        return DataTables::of($ventas)
            ->addColumn('cliente_nombre', function($venta) {
                return $venta->cliente ? $venta->cliente->nombre : 'Cliente no registrado';
            })
            ->addColumn('vendedor', function($venta) {
                return $venta->usuario ? $venta->usuario->name : 'N/A';
            })
            ->addColumn('fecha_formateada', function($venta) {
                return $venta->fecha->format('d/m/Y H:i');
            })
            ->addColumn('total_formateado', function($venta) {
                return '$' . number_format($venta->total, 2);
            })
            ->addColumn('total_productos', function($venta) {
                return $venta->detalles->sum('cantidad');
            })
            ->addColumn('tipo_pago_label', function($venta) {
                $badges = [
                    'efectivo' => 'success',
                    'tarjeta' => 'primary',
                    'fiado' => 'warning'
                ];
                return '<span class="badge bg-'.$badges[$venta->tipo_pago].'">'.ucfirst($venta->tipo_pago).'</span>';
            })
            ->addColumn('action', function($venta) {
                return '<a href="' . route('ventas.show', $venta->id) . '" class="btn btn-sm btn-info" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . route('ventas.imprimir', $venta->id) . '" class="btn btn-sm btn-secondary" title="Imprimir" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" 
                                data-id="' . $venta->id . '" 
                                data-folio="' . $venta->folio . '" 
                                data-cliente="' . ($venta->cliente ? $venta->cliente->nombre : 'Cliente no registrado') . '" 
                                data-total="' . $venta->total . '" 
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>';
            })
            ->rawColumns(['tipo_pago_label', 'action'])
            ->with([
                'ventas_hoy' => Venta::whereDate('fecha', today())->count(),
                'total_hoy' => Venta::whereDate('fecha', today())->sum('total'),
                'ventas_mes' => Venta::whereMonth('fecha', now()->month)->count(),
                'total_mes' => Venta::whereMonth('fecha', now()->month)->sum('total'),
                'total_general' => $ventas->sum('total')
            ])
            ->make(true);
    }
    
    $clientes = Cliente::activos()->get();
    return view('ventas.index', compact('clientes'));
}

private function exportarVentas($ventas, $formato)
{
    // Lógica de exportación (Excel, PDF, CSV)
    // Implementar según tu preferencia usando Laravel Excel o similar
}

    public function create()
    {
        $clientes = Cliente::activos()->get();
        return view('ventas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'cliente_id' => 'nullable|exists:clientes,id',
                'tipo_pago' => 'required|in:efectivo,tarjeta,fiado',
                'efectivo' => 'required_if:tipo_pago,efectivo|numeric|min:0',
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1'
            ]);

            // Calcular total y verificar stock
            $total = 0;
            foreach ($request->productos as $productoData) {
                $producto = Producto::find($productoData['id']);
                
                if ($producto->existencia < $productoData['cantidad']) {
                    throw new \Exception("Stock insuficiente para: " . $producto->nombre);
                }
                
                $subtotal = $producto->precio_venta * $productoData['cantidad'];
                $total += $subtotal;
            }

            // Validar fiado
            if ($request->tipo_pago === 'fiado') {
                if (!$request->cliente_id) {
                    throw new \Exception("Se requiere cliente para ventas a fiado");
                }
                
                $cliente = Cliente::find($request->cliente_id);
                if (!$cliente->puede_fiadar) {
                    throw new \Exception("El cliente no puede realizar más fiados");
                }
                
                if ($total > $cliente->disponible_fiado) {
                    throw new \Exception("El monto excede el límite disponible de fiado");
                }
            }

            // Validar efectivo
            if ($request->tipo_pago === 'efectivo') {
                if ($request->efectivo < $total) {
                    throw new \Exception("El efectivo recibido es menor al total");
                }
            }

            // Crear la venta
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
             //   'usuario_id' => Auth::id(),
                'folio' => Venta::generarFolio(),
                'total' => $total,
                'efectivo' => $request->efectivo ?? 0,
                'cambio' => ($request->efectivo ?? 0) - $total,
                'tipo_pago' => $request->tipo_pago,
                'estado' => 'completada',
                'fecha' => now()
            ]);

            // Crear detalles de venta y actualizar stock
            foreach ($request->productos as $productoData) {
                $producto = Producto::find($productoData['id']);
                $subtotal = $producto->precio_venta * $productoData['cantidad'];

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $productoData['cantidad'],
                    'precio' => $producto->precio_venta,
                    'subtotal' => $subtotal
                ]);

                // Actualizar stock
                $producto->existencia -= $productoData['cantidad'];
                $producto->save();
            }

            // Crear fiado si es necesario
            if ($request->tipo_pago === 'fiado') {
                Fiado::create([
                    'cliente_id' => $request->cliente_id,
                    'venta_id' => $venta->id,
                    'monto_total' => $total,
                    'saldo_pendiente' => $total,
                    'estado' => 'pendiente',
                    'fecha_limite' => now()->addDays(30)
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta_id' => $venta->id,
                'cambio' => $venta->cambio
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
        $venta = Venta::with(['cliente', 'usuario', 'detalles.producto', 'fiado'])->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    public function imprimir($id)
    {
        $venta = Venta::with([
        'cliente',
        'usuario',
        'detalles.producto',
        'fiado'
    ])->findOrFail($id);

    return view('ventas.imprimir', compact('venta'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $venta = Venta::with(['detalles', 'fiado'])->findOrFail($id);
            
            // Revertir stock
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $producto->existencia += $detalle->cantidad;
                $producto->save();
            }
            
            // Eliminar fiado si existe
            if ($venta->fiado) {
                $venta->fiado->delete();
            }
            
            $venta->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la venta: ' . $e->getMessage()
            ], 500);
        }
    }
}