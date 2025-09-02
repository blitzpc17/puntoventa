<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Fiado;
use DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function getData(Request $request)
    {
        // Ventas de hoy
        $ventasHoy = Venta::whereDate('fecha', today())->sum('total');
        
        // Productos con baja existencia
        $productosBajos = Producto::whereColumn('existencia', '<=', 'min_existencia')->count();
        
        // Fiados pendientes
        $fiadosPendientes = Fiado::where('estado', 'pendiente')->sum('saldo_pendiente');
        
        // Total de clientes
        $totalClientes = Cliente::count();
        
        // Ventas de la última semana
        $ventasUltimaSemana = Venta::select(
            DB::raw('DATE(fecha) as fecha'),
            DB::raw('SUM(total) as total')
        )
        ->whereDate('fecha', '>=', Carbon::today()->subDays(7))
        ->groupBy('fecha')
        ->orderBy('fecha')
        ->get();
        
        // Preparar datos para el gráfico
        $labels = [];
        $values = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::today()->subDays($i)->format('d M');
            
            $venta = $ventasUltimaSemana->firstWhere('fecha', $date);
            $values[] = $venta ? $venta->total : 0;
        }
        
        // Productos más vendidos (últimos 30 días)
        $productosPopulares = DB::table('detalle_ventas')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendido')
            )
            ->where('ventas.fecha', '>=', Carbon::today()->subDays(30))
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();
        
        // Ventas recientes (últimas 5)
        $ventasRecientes = Venta::with('cliente')
            ->orderBy('fecha', 'desc')
            ->limit(5)
            ->get()
            ->map(function($venta) {
                return [
                    'id' => $venta->id,
                    'cliente_nombre' => $venta->cliente ? $venta->cliente->nombre : null,
                    'total' => $venta->total,
                    'fecha' => $venta->fecha
                ];
            });
        
        // Productos con stock bajo
        $stockBajo = Producto::whereColumn('existencia', '<=', 'min_existencia')
            ->orderBy('existencia', 'asc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'ventas_hoy' => (float) $ventasHoy,
            'productos_bajos' => $productosBajos,
            'fiados_pendientes' => (float) $fiadosPendientes,
            'total_clientes' => $totalClientes,
            'ventas_ultima_semana' => [
                'labels' => $labels,
                'values' => $values
            ],
            'productos_populares' => $productosPopulares,
            'ventas_recientes' => $ventasRecientes,
            'stock_bajo' => $stockBajo
        ]);
    }
}