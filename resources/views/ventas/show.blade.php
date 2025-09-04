@extends('layout.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Detalle de Venta</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">#{{ $venta->folio }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            <a href="{{ route('ventas.imprimir', $venta->id) }}" class="btn btn-outline-primary" target="_blank">
                <i class="fas fa-print me-2"></i>Imprimir
            </a>
            @if($venta->estado == 'completada')
            <button class="btn btn-danger ms-2" id="btn-eliminar-venta" data-id="{{ $venta->id }}">
                <i class="fas fa-trash me-2"></i>Eliminar
            </button>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Productos Vendidos</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalles as $detalle)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-3">
                                                <h6 class="mb-0">{{ $detalle->producto->nombre }}</h6>
                                                <small class="text-muted">Código: {{ $detalle->producto->codigo ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">${{ number_format($detalle->precio, 2) }}</td>
                                    <td class="text-center">{{ $detalle->cantidad }}</td>
                                    <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">${{ number_format($venta->total, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Información de Fiado si aplica -->
            @if($venta->fiado)
            <div class="card shadow mb-4 border-warning">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">Información de Fiado</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Monto Total:</strong> ${{ number_format($venta->fiado->monto_total, 2) }}</p>
                            <p><strong>Saldo Pendiente:</strong> 
                                <span class="badge bg-{{ $venta->fiado->saldo_pendiente > 0 ? 'warning' : 'success' }}">
                                    ${{ number_format($venta->fiado->saldo_pendiente, 2) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fecha Límite:</strong> {{ $venta->fiado->fecha_limite->format('d/m/Y') }}</p>
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-{{ $venta->fiado->estado == 'pendiente' ? 'warning' : 'success' }}">
                                    {{ ucfirst($venta->fiado->estado) }}
                                </span>
                            </p>
                        </div>
                    </div>
                    
                    @if($venta->fiado->abonos->count() > 0)
                    <hr>
                    <h6 class="font-weight-bold">Historial de Abonos</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                    <th>Notas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->fiado->abonos as $abono)
                                <tr>
                                    <td>{{ $abono->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-success">${{ number_format($abono->monto, 2) }}</td>
                                    <td>{{ $abono->notas ?? 'Sin notas' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Información de la Venta -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información de la Venta</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Folio:</strong> 
                        <span class="badge bg-primary">{{ $venta->folio }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Fecha y Hora:</strong> 
                        <br>{{ $venta->fecha->format('d/m/Y H:i:s') }}
                    </div>
                    <div class="mb-3">
                        <strong>Estado:</strong> 
                        <span class="badge bg-{{ $venta->estado == 'completada' ? 'success' : ($venta->estado == 'pendiente' ? 'warning' : 'danger') }}">
                            {{ ucfirst($venta->estado) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Tipo de Pago:</strong> 
                        <span class="badge bg-{{ $venta->tipo_pago == 'efectivo' ? 'success' : ($venta->tipo_pago == 'tarjeta' ? 'primary' : 'warning') }}">
                            {{ ucfirst($venta->tipo_pago) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Vendedor:</strong> 
                        <br>Vendedor Prueba
                    </div>
                    @if($venta->cliente)
                    <div class="mb-3">
                        <strong>Cliente:</strong> 
                        <br>{{ $venta->cliente->nombre }}
                        @if($venta->cliente->rfc)
                        <br><small class="text-muted">RFC: {{ $venta->cliente->rfc }}</small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Información de Pago -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detalles de Pago</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Subtotal:</th>
                            <td class="text-end">${{ number_format($venta->total, 2) }}</td>
                        </tr>
                        @if($venta->tipo_pago == 'efectivo')
                        <tr>
                            <th>Efectivo Recibido:</th>
                            <td class="text-end">${{ number_format($venta->efectivo, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <th>Cambio:</th>
                            <td class="text-end">${{ number_format($venta->cambio, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-primary">
                            <th>Total:</th>
                            <th class="text-end">${{ number_format($venta->total, 2) }}</th>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Estadísticas de la Venta -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="fas fa-box me-2 text-primary"></i>
                        <strong>Total Productos:</strong> 
                        <span class="float-end">{{ $venta->detalles->sum('cantidad') }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-receipt me-2 text-primary"></i>
                        <strong>Items Diferentes:</strong> 
                        <span class="float-end">{{ $venta->detalles->count() }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-dollar-sign me-2 text-primary"></i>
                        <strong>Ticket Promedio:</strong> 
                        <span class="float-end">${{ number_format($venta->total, 2) }}</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Registrada: {{ $venta->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta venta?</p>
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Advertencia</h6>
                    <ul class="mb-0">
                        <li>Esta acción no se puede deshacer</li>
                        <li>El stock de los productos será revertido</li>
                        <li>Si la venta es a fiado, se eliminará el registro de fiado</li>
                    </ul>
                </div>
                <div class="venta-info">
                    <p><strong>Folio:</strong> {{ $venta->folio }}</p>
                    <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
                    <p><strong>Fecha:</strong> {{ $venta->fecha->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Eliminar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Eliminar venta
    $('#btn-eliminar-venta').click(function() {
        $('#deleteModal').modal('show');
    });

    $('#confirm-delete').click(function() {
        const ventaId = {{ $venta->id }};
        
        $.ajax({
            url: "{{ route('ventas.index') }}/" + ventaId,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#confirm-delete').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Venta Eliminada',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = "{{ route('ventas.index') }}";
                });
            },
            error: function(xhr) {
                $('#confirm-delete').prop('disabled', false).text('Eliminar');
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            }
        });
    });

    // Copiar folio al portapapeles
    $('.badge.bg-primary').click(function() {
        const folio = $(this).text();
        navigator.clipboard.writeText(folio).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Folio copiado',
                text: 'El folio se ha copiado al portapapeles',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });
});
</script>

<style>
.page-title {
    color: #2e59d9;
    font-weight: 700;
}
.card {
    border: none;
    border-radius: 10px;
}
.table th {
    border-top: none;
    font-weight: 600;
}
.badge {
    cursor: pointer;
    transition: opacity 0.3s;
}
.badge:hover {
    opacity: 0.8;
}
.venta-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
}
.breadcrumb {
    background: transparent;
    margin-bottom: 0;
}
</style>
@endsection