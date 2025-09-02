@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Detalle de Compra #{{ $compra->folio }}</h1>
    <div>
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
        <button class="btn btn-danger ms-2" id="btn-eliminar" data-id="{{ $compra->id }}">
            <i class="fas fa-trash me-2"></i>Eliminar
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Productos Comprados</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($compra->detalles as $detalle)
                            <tr>
                                <td>{{ $detalle->producto->nombre }}</td>
                                <td>${{ number_format($detalle->precio, 2) }}</td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th>${{ number_format($compra->total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Información de la Compra</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Folio:</strong> {{ $compra->folio }}
                </div>
                <div class="mb-3">
                    <strong>Proveedor:</strong> {{ $compra->proveedor->nombre }}
                </div>
                <div class="mb-3">
                    <strong>Fecha:</strong> {{ $compra->fecha->format('d/m/Y') }}
                </div>
                <div class="mb-3">
                    <strong>Estado:</strong> 
                    <span class="badge bg-{{ $compra->estado == 'completada' ? 'success' : 'warning' }}">
                        {{ ucfirst($compra->estado) }}
                    </span>
                </div>
                @if($compra->notas)
                <div class="mb-3">
                    <strong>Notas:</strong> 
                    <p class="text-muted">{{ $compra->notas }}</p>
                </div>
                @endif
                <div class="mb-3">
                    <strong>Registrada el:</strong> {{ $compra->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Eliminar compra
    $('#btn-eliminar').click(function() {
        const compraId = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción revertirá el stock de los productos y no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('compras.index') }}/" + compraId,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "{{ route('compras.index') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection