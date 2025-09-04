<?php

namespace App\Http\Controllers;

use App\Models\CorteCaja;
use App\Models\Venta;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CorteCajaController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $cortes = CorteCaja::with('usuario')->select('*');
        
        // Aplicar filtros
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $cortes->whereDate('fecha', '>=', $request->fecha_desde);
        }
        
        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $cortes->whereDate('fecha', '<=', $request->fecha_hasta);
        }
        
        if ($request->has('estado') && $request->estado) {
            $cortes->where('estado', $request->estado);
        }
        
        if ($request->has('usuario_id') && $request->usuario_id) {
            $cortes->where('usuario_id', $request->usuario_id);
        }
        
        return DataTables::of($cortes)
            ->addColumn('usuario_nombre', function($corte) {
                return $corte->usuario->name;
            })
            ->addColumn('fecha_formateada', function($corte) {
                return $corte->fecha->format('d/m/Y');
            })
            ->addColumn('monto_inicial_formateado', function($corte) {
                return '$' . number_format($corte->monto_inicial, 2);
            })
            ->addColumn('monto_final_formateado', function($corte) {
                return $corte->monto_final ? '$' . number_format($corte->monto_final, 2) : 'N/A';
            })
            ->addColumn('ventas_efectivo_formateado', function($corte) {
                return '$' . number_format($corte->ventas_efectivo, 2);
            })
            ->addColumn('ventas_tarjeta_formateado', function($corte) {
                return '$' . number_format($corte->ventas_tarjeta, 2);
            })
            ->addColumn('ventas_fiado_formateado', function($corte) {
                return '$' . number_format($corte->ventas_fiado, 2);
            })
            ->addColumn('total_ventas_formateado', function($corte) {
                return '$' . number_format($corte->total_ventas, 2);
            })
            ->addColumn('diferencia_formateado', function($corte) {
                if ($corte->diferencia === null) return 'N/A';
                $clase = $corte->diferencia == 0 ? 'success' : 
                        ($corte->diferencia > 0 ? 'warning' : 'danger');
                return '<span class="badge bg-'.$clase.'">$' . number_format($corte->diferencia, 2) . '</span>';
            })
            ->addColumn('estado_label', function($corte) {
                $badges = [
                    'abierto' => 'warning',
                    'cerrado' => 'success'
                ];
                return '<span class="badge bg-'.$badges[$corte->estado].'">'.ucfirst($corte->estado).'</span>';
            })
            ->addColumn('action', function($corte) {
                return '<a href="' . route('corte-caja.show', $corte->id) . '" class="btn btn-sm btn-info btn-view" data-id="'.$corte->id.'" title="Ver">
                            <i class="fas fa-eye"></i>
                        </a>';
            })
            ->rawColumns(['diferencia_formateado', 'estado_label', 'action'])
            ->with([
                'cortes_hoy' => CorteCaja::whereDate('fecha', today())->count(),
                'cortes_abiertos' => CorteCaja::where('estado', 'abierto')->count(),
                'ventas_totales' => CorteCaja::where('estado', 'cerrado')->sum('total_ventas'),
                'diferencia_promedio' => CorteCaja::where('estado', 'cerrado')->avg('diferencia')
            ])
            ->make(true);
    }
    
    $usuarios = [];//User::all();
    return view('corte-caja.index', compact('usuarios'));
}

    public function create()
    {
        // Verificar si ya hay un corte abierto
        $corteAbierto = CorteCaja:://where('usuario_id', Auth::id())
            where('estado', 'abierto')
            ->first();
        
        // Obtener último corte cerrado
        $ultimoCorte = CorteCaja:://where('usuario_id', Auth::id())
            where('estado', 'cerrado')
            ->orderBy('created_at', 'desc')
            ->first();
        
        return view('corte-caja.create', compact('corteAbierto', 'ultimoCorte'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'monto_inicial' => 'required|numeric|min:0'
            ]);

            $corte = CorteCaja::create([
                'usuario_id' => Auth::id(),
                'fecha' => now(),
                'monto_inicial' => $request->monto_inicial,
                'monto_final' => $request->monto_inicial,
                'ventas_efectivo' => 0,
                'ventas_tarjeta' => 0,
                'ventas_fiado' => 0,
                'total_ventas' => 0,
                'diferencia' => 0,
                'estado' => 'abierto',
                'notas' => $request->notas
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Corte de caja iniciado correctamente',
                'corte_id' => $corte->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al iniciar corte de caja: ' . $e->getMessage()
            ], 500);
        }
    }



    public function show($id)
    {
        $corte = CorteCaja::with('usuario')->findOrFail($id);
        
        // Obtener ventas del día del corte
        $ventas = Venta::with('cliente')
            ->whereDate('fecha', $corte->fecha)
            ->where('estado', 'completada')
            ->orderBy('fecha', 'asc')
            ->get();

        // Calcular resumen estadístico
        $resumen = [
            'total_ventas_count' => $ventas->count(),
            'ventas_efectivo_count' => $ventas->where('tipo_pago', 'efectivo')->count(),
            'ventas_tarjeta_count' => $ventas->where('tipo_pago', 'tarjeta')->count(),
            'ventas_fiado_count' => $ventas->where('tipo_pago', 'fiado')->count(),
            'clientes_atendidos' => $ventas->pluck('cliente_id')->unique()->count(),
        ];

        return view('corte-caja.show', compact('corte', 'ventas', 'resumen'));
    }
/*
    public function cerrar(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'monto_final' => 'required|numeric|min:0',
                'notas' => 'nullable|string'
            ]);

            $corte = CorteCaja::findOrFail($id);
            
            if ($corte->estado !== 'abierto') {
                throw new \Exception("Este corte de caja ya está cerrado");
            }

            // Obtener ventas del día
            $ventas = Venta::whereDate('fecha', $corte->fecha)
                ->where('estado', 'completada')
                ->get();
                
            $corte->update([
                'monto_final' => $request->monto_final,
                'ventas_efectivo' => $ventas->where('tipo_pago', 'efectivo')->sum('total'),
                'ventas_tarjeta' => $ventas->where('tipo_pago', 'tarjeta')->sum('total'),
                'ventas_fiado' => $ventas->where('tipo_pago', 'fiado')->sum('total'),
                'total_ventas' => $ventas->sum('total'),
                'diferencia' => $request->monto_final - ($corte->monto_inicial + $ventas->where('tipo_pago', 'efectivo')->sum('total')),
                'estado' => 'cerrado',
                'notas' => $request->notas
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Corte de caja cerrado correctamente',
                'diferencia' => $corte->diferencia
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar corte de caja: ' . $e->getMessage()
            ], 500);
        }
    }*/

    public function cerrar(Request $request, $id)
{
    DB::beginTransaction();
    
    try {
        $request->validate([
            'monto_final' => 'required|numeric|min:0',
            'notas' => 'nullable|string'
        ]);

        $corte = CorteCaja::findOrFail($id);
        
        if ($corte->estado !== 'abierto') {
            throw new \Exception("Este corte de caja ya está cerrado");
        }

        // Obtener ventas del día para calcular totales reales
        $ventas = Venta::whereDate('fecha', $corte->fecha)
            ->where('estado', 'completada')
            ->get();
            
        $corte->update([
            'monto_final' => $request->monto_final,
            'ventas_efectivo' => $ventas->where('tipo_pago', 'efectivo')->sum('total'),
            'ventas_tarjeta' => $ventas->where('tipo_pago', 'tarjeta')->sum('total'),
            'ventas_fiado' => $ventas->where('tipo_pago', 'fiado')->sum('total'),
            'total_ventas' => $ventas->sum('total'),
            'diferencia' => $request->monto_final - ($corte->monto_inicial + $ventas->where('tipo_pago', 'efectivo')->sum('total')),
            'estado' => 'cerrado',
            'notas' => $request->notas
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Corte de caja cerrado correctamente',
            'diferencia' => $corte->diferencia
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Error al cerrar corte de caja: ' . $e->getMessage()
        ], 500);
    }
}

    public function getCorteAbierto()
    {
        $corte = CorteCaja:://where('usuario_id', Auth::id())
            where('estado', 'abierto')
            ->first();
            
        return response()->json($corte);
    }
}