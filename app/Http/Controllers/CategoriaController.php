<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    public function index()
    {
        return view('categorias.index');
    }

    public function getCategorias()
    {
        $categorias = Categoria::all();
        return response()->json(['data' => $categorias]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_productos,nombre'
        ]);

        try {
            $categoria = Categoria::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Categoría creada exitosamente',
                'data' => $categoria
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Categoria $categoria): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $categoria
        ]);
    }

    public function update(Request $request, Categoria $categoria): JsonResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categoria_productos,nombre,' . $categoria->id
        ]);

        try {
            $categoria->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada exitosamente',
                'data' => $categoria
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Categoria $categoria): JsonResponse
    {
        try {
            $categoria->delete();
            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }
}
