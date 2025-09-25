<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $proveedores = Proveedor::activos()->select('*');
            
            return DataTables::of($proveedores)
                ->addColumn('compras_count', function($proveedor) {
                    return $proveedor->compras->count();
                })
                ->addColumn('productos_count', function($proveedor) {
                    return $proveedor->productos->count();
                })
                ->addColumn('ultima_compra', function($proveedor) {
                    return $proveedor->ultima_compra ? 
                        $proveedor->ultima_compra->created_at->format('d/m/Y') : 
                        'Nunca';
                })
                ->addColumn('action', function($proveedor) {
                    return '<button class="btn btn-sm btn-warning edit" data-id="'.$proveedor->id.'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete" data-id="'.$proveedor->id.'">
                                <i class="fas fa-trash"></i>
                            </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('proveedores.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'nombre' => 'required|string|max:100|unique:proveedores,nombre',
                'contacto' => 'nullable|string|max:100',
                'telefono' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100',
                'direccion' => 'nullable|string|max:255',
                'rfc' => 'nullable|string|max:13|unique:proveedores,rfc',
                'notas' => 'nullable|string'
            ], [
                'nombre.unique' => 'Ya existe un proveedor con este nombre',
                'rfc.unique' => 'Ya existe un proveedor con este RFC'
            ]);

            $proveedor = Proveedor::create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor creado correctamente',
                'data' => $proveedor
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el proveedor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $proveedor = Proveedor::with(['productos', 'compras'])->findOrFail($id);
        return response()->json($proveedor);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $proveedor = Proveedor::findOrFail($id);
            
            $request->validate([
                'nombre' => 'required|string|max:100|unique:proveedores,nombre,' . $id,
                'contacto' => 'nullable|string|max:100',
                'telefono' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100',
                'direccion' => 'nullable|string|max:255',
                'rfc' => 'nullable|string|max:13|unique:proveedores,rfc,' . $id,
                'notas' => 'nullable|string'
            ], [
                'nombre.unique' => 'Ya existe un proveedor con este nombre',
                'rfc.unique' => 'Ya existe un proveedor con este RFC'
            ]);

            $proveedor->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor actualizado correctamente'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el proveedor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $proveedor = Proveedor::findOrFail($id);
            
            // Verificar si el proveedor tiene productos o compras asociadas
            if ($proveedor->tiene_productos) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el proveedor porque tiene productos asociados'
                ], 400);
            }
            
            if ($proveedor->tiene_compras) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el proveedor porque tiene compras asociadas'
                ], 400);
            }
            
            $proveedor->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proveedor eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el proveedor: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function buscar(Request $request)
    {
        $term = $request->get('term');
        
        $proveedores = Proveedor::activos()
            ->where('nombre', 'like', '%'.$term.'%')
            ->orWhere('rfc', 'like', '%'.$term.'%')
            ->get()
            ->map(function($proveedor) {
                return [
                    'id' => $proveedor->id,
                    'value' => $proveedor->nombre,
                    'text' => $proveedor->nombre . ' (' . $proveedor->rfc . ')'
                ];
            });
            
        return response()->json($proveedores);
    }
    
    public function getProductos($id)
    {
        $proveedor = Proveedor::with('productos')->findOrFail($id);
        return response()->json($proveedor->productos);
    }
    
    public function getCompras($id)
    {
        $proveedor = Proveedor::with(['compras' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->findOrFail($id);
        
        return response()->json($proveedor->compras);
    }

    public function all(){
        $proveedores = Proveedor::all()
          ->map(function($prov) {
                return [
                    'id' => $prov->id,
                    'text' => $prov->nombre 
                ];
            });
        $data = array("success"=>true, "data"=>$proveedores);
        return response()->json($data);
    }





}