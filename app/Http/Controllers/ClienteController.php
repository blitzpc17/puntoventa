<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clientes = Cliente::activos()->select('*');
            
            return DataTables::of($clientes)
                ->addColumn('saldo_pendiente', function($cliente) {
                    return '$' . number_format($cliente->saldo_pendiente, 2);
                })
                ->addColumn('disponible_fiado', function($cliente) {
                    return '$' . number_format($cliente->disponible_fiado, 2);
                })
                ->addColumn('estado_label', function($cliente) {
                    $badge = $cliente->estado == 'activo' 
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                    return $badge;
                })
                ->addColumn('action', function($cliente) {
                    return '<button class="btn btn-sm btn-info view" data-id="'.$cliente->id.'" title="Ver">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning edit" data-id="'.$cliente->id.'" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete" data-id="'.$cliente->id.'" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>';
                })
                ->rawColumns(['estado_label', 'action'])
                ->make(true);
        }
        
        return view('clientes.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $request->validate([
                'nombre' => 'required|string|max:100',
                'telefono' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100|unique:clientes,email',
                'direccion' => 'nullable|string|max:255',
                'rfc' => 'nullable|string|max:13|unique:clientes,rfc',
                'limite_fiado' => 'required|numeric|min:0',
                'estado' => 'required|in:activo,inactivo'
            ], [
                'email.unique' => 'Ya existe un cliente con este email',
                'rfc.unique' => 'Ya existe un cliente con este RFC'
            ]);

            $cliente = Cliente::create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente creado correctamente',
                'data' => $cliente
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $cliente = Cliente::with(['ventas', 'fiados'])->findOrFail($id);
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $cliente = Cliente::findOrFail($id);
            
            $request->validate([
                'nombre' => 'required|string|max:100',
                'telefono' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100|unique:clientes,email,' . $id,
                'direccion' => 'nullable|string|max:255',
                'rfc' => 'nullable|string|max:13|unique:clientes,rfc,' . $id,
                'limite_fiado' => 'required|numeric|min:0',
                'estado' => 'required|in:activo,inactivo'
            ], [
                'email.unique' => 'Ya existe un cliente con este email',
                'rfc.unique' => 'Ya existe un cliente con este RFC'
            ]);

            $cliente->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado correctamente'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $cliente = Cliente::findOrFail($id);
            
            // Verificar si el cliente tiene ventas o fiados asociados
            if ($cliente->ventas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el cliente porque tiene ventas asociadas'
                ], 400);
            }
            
            if ($cliente->fiados()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el cliente porque tiene fiados asociados'
                ], 400);
            }
            
            $cliente->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado correctamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function buscar(Request $request)
    {
        $term = $request->get('term');
        
        $clientes = Cliente::activos()
            ->where('nombre', 'like', '%'.$term.'%')
            ->orWhere('rfc', 'like', '%'.$term.'%')
            ->get()
            ->map(function($cliente) {
                return [
                    'id' => $cliente->id,
                    'value' => $cliente->nombre,
                    'text' => $cliente->nombre . ' (' . $cliente->rfc . ') - Límite: $' . number_format($cliente->limite_fiado, 2),
                    'limite_fiado' => $cliente->limite_fiado,
                    'saldo_pendiente' => $cliente->saldo_pendiente
                ];
            });
            
        return response()->json($clientes);
    }
}