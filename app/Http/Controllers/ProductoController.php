<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsLayoutExport;
use App\Imports\ProductsImport;


class ProductoController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        // Para solicitudes simples (como el select de ajuste de stock)
        if ($request->has('simple')) {
            $productos = Producto::select('id', 'nombre', 'existencia')->get();
            return response()->json([
                'data' => $productos
            ]);
        }
        
        // Código original para DataTables
        $productos = Producto::with('proveedor')->select('*');
        
        return DataTables::of($productos)
            ->addColumn('estado_stock', function($producto) {
                if ($producto->existencia == 0) {
                    return '<span class="badge bg-danger">Agotado</span>';
                } elseif ($producto->existencia <= $producto->min_existencia) {
                    return '<span class="badge bg-warning">Bajo</span>';
                } else {
                    return '<span class="badge bg-success">Disponible</span>';
                }
            })
            ->addColumn('proveedor_nombre', function($producto) {
                return $producto->proveedor ? $producto->proveedor->nombre : 'N/A';
            })
            ->addColumn('action', function($producto) {
                return '<button class="btn btn-sm btn-warning edit" data-id="'.$producto->id.'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete" data-id="'.$producto->id.'">
                            <i class="fas fa-trash"></i>
                        </button>';
            })
            ->rawColumns(['estado_stock', 'action'])
            ->make(true);
    }
    
    $proveedores = Proveedor::all();
    return view('productos.index', compact('proveedores'));
}

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'codigo' => 'nullable|string|max:50|unique:productos,codigo',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'existencia' => 'required|integer|min:0',
                'min_existencia' => 'required|integer|min:0',
                'proveedor_id' => 'nullable|exists:proveedores,id',
                'categoria' => 'nullable|string|max:50'
            ]);

            // Generar código automático si no se proporciona
            $codigo = $request->codigo;
            if (empty($codigo)) {
                $codigo = 'PROD-' . strtoupper(uniqid());
            }

            $producto = Producto::create([
                'codigo' => $codigo,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precio_compra' => $request->precio_compra,
                'precio_venta' => $request->precio_venta,
                'existencia' => $request->existencia,
                'min_existencia' => $request->min_existencia,
                'proveedor_id' => $request->proveedor_id,
                'categoria' => $request->categoria
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto creado correctamente',
                'data' => $producto
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $producto = Producto::with('proveedor')->findOrFail($id);
        return response()->json($producto);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $producto = Producto::findOrFail($id);
            
            $request->validate([
                'codigo' => 'nullable|string|max:50|unique:productos,codigo,' . $id,
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string',
                'precio_compra' => 'required|numeric|min:0',
                'precio_venta' => 'required|numeric|min:0',
                'existencia' => 'required|integer|min:0',
                'min_existencia' => 'required|integer|min:0',
                'proveedor_id' => 'nullable|exists:proveedores,id',
                'categoria' => 'nullable|string|max:50'
            ]);

            $producto->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $producto = Producto::findOrFail($id);
            
            // Verificar si el producto tiene ventas o compras asociadas
            $ventasCount = DB::table('detalle_ventas')->where('producto_id', $id)->count();
            $comprasCount = DB::table('detalle_compras')->where('producto_id', $id)->count();
            
            if ($ventasCount > 0 || $comprasCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el producto porque tiene registros asociados'
                ], 400);
            }
            
            $producto->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function buscar(Request $request)
    {
        $term = $request->get('term');
        
        //agregar mensaje si 
        $productos = Producto::where('id', $term)           
            //->where('existencia', '>', 0)
            ->first();
            /*->map(function($producto) {
                return [
                    'id' => $producto->id,
                    'value' => $producto->nombre,
                    'codigo' => $producto->codigo,
                    'precio_venta' => $producto->precio_venta,
                    'precio_compra' => $producto->precio_compra,
                    'existencia' => $producto->existencia,
                    'text' => $producto->nombre . ' (' . $producto->codigo . ') - $' . number_format($producto->precio_venta, 2) . ' - Stock: ' . $producto->existencia
                ];
            });*/
        return response()->json($productos);
    }
    
    public function getByBarcode(Request $request)
    {
        $codigo = $request->get('codigo');
        
        $producto = Producto::where('codigo', $codigo)->first();
        
        if ($producto) {
            return response()->json([
                'success' => true,
                'data' => $producto
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
    }
    
    public function updateStock(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $producto = Producto::findOrFail($id);
            
            $request->validate([
                'cantidad' => 'required|integer',
                'tipo' => 'required|in:incrementar,decrementar,ajustar'
            ]);
            
            switch ($request->tipo) {
                case 'incrementar':
                    $producto->existencia += $request->cantidad;
                    break;
                case 'decrementar':
                    if ($producto->existencia < $request->cantidad) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No hay suficiente stock para decrementar'
                        ], 400);
                    }
                    $producto->existencia -= $request->cantidad;
                    break;
                case 'ajustar':
                    if ($request->cantidad < 0) {
                        return response()->json([
                            'success' => false,
                            'message' => 'El stock no puede ser negativo'
                        ], 400);
                    }
                    $producto->existencia = $request->cantidad;
                    break;
            }
            
            $producto->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Stock actualizado correctamente',
                'nuevo_stock' => $producto->existencia
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searching(Request $request)
    {
        $query = Producto::query();
        
        if ($request->has('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%')
                ->orWhere('descripcion', 'like', '%' . $request->search . '%')
                ->orWhere('codigo', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->select('id', DB::RAW("CONCAT(codigo,' - ',nombre ) as text"))
                        ->orderBy('nombre')
                        ->get();
        
        return response()->json($products);
    }

    public function downloadLayout(){
          return Excel::download(new ProductsLayoutExport, 'layout_productos.xlsx');
    }

    public function previewView(){
        return view('productos.preview-import');
    }

    public function dataPreview(Request $request)
    {
        $request->validate([
            'layout' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('layout');
            $data = Excel::toCollection(new ProductsImport(), $file);
            
            if ($data->isEmpty()) {
                return response()->json([
                    'code' => 400,
                    'success' => false,
                    'error' => 'El archivo no contiene hojas de cálculo.'
                ], 400);
            }

            $rows = $data->first();
            
            if ($rows->isEmpty()) {
                return response()->json([
                    'code' => 400,
                    'success' => false,
                    'error' => 'La hoja de cálculo está vacía.'
                ], 400);
            }

         
            return response()->json([
                'code' => 200,
                'success' => true,           
                'products' => $rows,//$dataRows,
                'filename' => $file->getClientOriginalName(),
                'total_rows' => $rows->count(),//$dataRows->count(),
                'message' => 'Visualización preliminar.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en dataPreview: ' . $e->getMessage());
            
            return response()->json([
                'code' => 500,
                'success' => false,
                'error' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadMasivo(Request $request){
        
        DB::beginTransaction();

        try {
            $productos = collect(json_decode($request->products, true));
            $tamanoLote = 100;
            
            $lotes = $productos->chunk($tamanoLote);
            $totalProcesados = 0;
            
            foreach ($lotes as $lote) {
                $productosLote = $lote->map(function ($producto) {
                    $data = [
                        "codigo" => $producto['codigo'],  // Usar corchetes en lugar de ->
                        "proveedor_Id" => $producto['proveedor'],
                        "categoriaId" => $producto['categoria'],
                        "nombre" => $producto['nombre'],
                        "descripcion" => $producto['descripcion'],
                        "precio_compra" => $producto['precio_compra'],
                        "precio_venta" => $producto['precio_venta'],
                        "existencia" => $producto['cantidad'],
                        "min_existencia" => $producto['existencia_minima']
                    ];
                    return array_merge($data, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });
                
                Producto::upsert(
                    $productosLote->toArray(),
                    ['codigo'],
                    ['codigo','proveedor_Id', 'categoriaId', 'nombre', 'descripcion', 'precio_compra', 'precio_venta', 'existencia', 'min_existencia', 'updated_at']
                );
                
                $totalProcesados += $lote->count();
            }
            
            DB::commit();

            dd([
                'message' => 'UPSERT por lotes completado',
                'total_procesados' => $totalProcesados,
                'lotes_procesados' => $lotes->count()
            ]);
            
            return response()->json([
                'message' => 'UPSERT por lotes completado',
                'total_procesados' => $totalProcesados,
                'lotes_procesados' => $lotes->count()
            ]);
            
        } catch (\Exception $e) {
             \Log::error('Error en upsert: ' . $e->getMessage());
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }




}