<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categorias = Categoria::select('*');
            
            return DataTables::of($categorias)                
                ->addColumn('action', function($cat) {
                    return '<button class="btn btn-sm btn-warning edit" data-id="'.$cat->id.'">
                                <i class="fas fa-edit"></i>
                            </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('categorias.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'nombre' => 'required|string|max:100|unique:categorias_producto,nombre',                
            ], [
                'nombre.unique' => 'Ya existe una categoría con este nombre',
            ]);          

            $categoria= Categoria::create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada correctamente',
                'data' => $categoria
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoria: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $categoria = Categoria::findOrFail($id);
        return response()->json($categoria);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $categoria = Categoria::findOrFail($id);
            
            $request->validate([
                'nombre' => 'required|string|max:100|unique:categorias_producto,nombre,' . $id,              
            ], [
                'nombre.unique' => 'Ya existe una cateogoría con este nombre',
            ]);

            $categoria->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente'
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
   
    
    public function buscar(Request $request)
    {
        $term = $request->get('term');
        
        $categorias = Categoria::
                                where('nombre', 'like', '%'.$term.'%')        
                                ->get()
                                ->map(function($cat) {
                                    return [
                                        'id' => $cat->id,
                                        'value' => $cat->id,
                                        'text' => $cat->nombre 
                                    ];
                                });
            
        return response()->json($categorias);
    }
}
