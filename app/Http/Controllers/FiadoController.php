<?php

namespace App\Http\Controllers;

use App\Models\Fiado;
use App\Models\AbonoFiado;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class FiadoController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $fiados = Fiado::with(['cliente', 'venta'])->select('*');
        
        // Aplicar filtros
        if ($request->has('estado')) {
            if ($request->estado == 'vencido') {
                $fiados->where('estado', 'pendiente')
                      ->where('fecha_limite', '<', now());
            } else {
                $fiados->where('estado', $request->estado);
            }
        }
        
        if ($request->has('cliente_id') && $request->cliente_id) {
            $fiados->where('cliente_id', $request->cliente_id);
        }
        
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $fiados->whereDate('created_at', '>=', $request->fecha_desde);
        }
        
        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $fiados->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        
        return DataTables::of($fiados)
            ->addColumn('cliente_nombre', function($fiado) {
                return $fiado->cliente->nombre;
            })
            ->addColumn('folio_venta', function($fiado) {
                return $fiado->venta->folio;
            })
            ->addColumn('monto_formateado', function($fiado) {
                return '$' . number_format($fiado->monto_total, 2);
            })
            ->addColumn('saldo_formateado', function($fiado) {
                return '$' . number_format($fiado->saldo_pendiente, 2);
            })
            ->addColumn('fecha_limite_formateada', function($fiado) {
                return $fiado->fecha_limite->format('d/m/Y');
            })
            ->addColumn('dias_restantes', function($fiado) {
                if ($fiado->estado == 'pagado') return '-';
                
                $dias = now()->diffInDays($fiado->fecha_limite, false);
                if ($dias < 0) {
                    return '<span class="badge bg-danger">Vencido</span>';
                }
                return $dias . ' días';
            })
            ->addColumn('estado_label', function($fiado) {
                if ($fiado->esta_vencido) {
                    return '<span class="badge bg-danger">Vencido</span>';
                }
                $badges = [
                    'pendiente' => 'warning',
                    'pagado' => 'success'
                ];
                return '<span class="badge bg-'.$badges[$fiado->estado].'">'.ucfirst($fiado->estado).'</span>';
            })
            ->addColumn('action', function($fiado) {
                $botones = '<button class="btn btn-sm btn-info btn-view" data-id="'.$fiado->id.'" title="Ver">
                            <i class="fas fa-eye"></i>
                        </button>';
                
                if ($fiado->estado === 'pendiente') {
                    $botones .= '<button class="btn btn-sm btn-success btn-abonar ms-1" data-id="'.$fiado->id.'" title="Abonar">
                            <i class="fas fa-money-bill-wave"></i>
                        </button>';
                }
                
                return $botones;
            })
            ->rawColumns(['dias_restantes', 'estado_label', 'action'])
            ->with([
                'total_fiados' => Fiado::count(),
                'fiados_pendientes' => Fiado::where('estado', 'pendiente')->count(),
                'fiados_pagados' => Fiado::where('estado', 'pagado')->count(),
                'fiados_vencidos' => Fiado::where('estado', 'pendiente')
                    ->where('fecha_limite', '<', now())
                    ->count(),
                'saldo_pendiente' => Fiado::where('estado', 'pendiente')->sum('saldo_pendiente'),
                'monto_vencido' => Fiado::where('estado', 'pendiente')
                    ->where('fecha_limite', '<', now())
                    ->sum('saldo_pendiente')
            ])
            ->make(true);
    }
    
    $clientes = Cliente::activos()->get();
    return view('fiados.index', compact('clientes'));
}

public function show($id)
{
    $fiado = Fiado::with(['cliente', 'venta', 'abonos'])->findOrFail($id);
    return response()->json($fiado);
}

public function getAbonos($id)
{
    $abonos = AbonoFiado::where('fiado_id', $id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($abono) {
            return [
                'fecha' => $abono->created_at->format('d/m/Y H:i'),
                'monto' => $abono->monto,
                'notas' => $abono->notas
            ];
        });
        
    return response()->json($abonos);
}

    public function storeAbono(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'monto' => 'required|numeric|min:0.01',
                'notas' => 'nullable|string'
            ]);

            $fiado = Fiado::findOrFail($id);
            
            if ($fiado->estado !== 'pendiente') {
                throw new \Exception("Este fiado ya está pagado");
            }
            
            if ($request->monto > $fiado->saldo_pendiente) {
                throw new \Exception("El monto excede el saldo pendiente");
            }

            $fiado->registrarAbono($request->monto, $request->notas);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Abono registrado correctamente',
                'nuevo_saldo' => $fiado->saldo_pendiente
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar abono: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFiadosCliente($clienteId)
    {
        $fiados = Fiado::with('venta')
            ->where('cliente_id', $clienteId)
            ->where('estado', 'pendiente')
            ->get();
            
        return response()->json($fiados);
    }
}