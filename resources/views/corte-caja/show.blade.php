@extends('layout.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Detalle de Corte de Caja</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('corte-caja.index') }}">Cortes de Caja</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Corte #{{ $corte->id }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('corte-caja.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
            @if($corte->estado == 'abierto')
            <button class="btn btn-success ms-2" id="btn-cerrar-corte" data-id="{{ $corte->id }}">
                <i class="fas fa-lock me-2"></i>Cerrar Corte
            </button>
            @endif
            <button class="btn btn-outline-primary ms-2" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimir
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Resumen del Corte -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen del Corte</h6>
                    <span class="badge bg-{{ $corte->estado == 'abierto' ? 'warning' : 'success' }}">
                        {{ strtoupper($corte->estado) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Usuario:</strong> Usuario prueba
                            </div>
                            <div class="mb-3">
                                <strong>Fecha:</strong> {{ $corte->fecha->format('d/m/Y') }}
                            </div>
                            <div class="mb-3">
                                <strong>Hora Apertura:</strong> {{ $corte->created_at->format('H:i:s') }}
                            </div>
                            @if($corte->estado == 'cerrado')
                            <div class="mb-3">
                                <strong>Hora Cierre:</strong> {{ $corte->updated_at->format('H:i:s') }}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Monto Inicial:</strong> 
                                <span class="float-end">${{ number_format($corte->monto_inicial??0, 2) }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Monto Final:</strong> 
                                <span class="float-end">${{ number_format($corte->monto_final ?? 0, 2) }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Diferencia:</strong> 
                                <span class="float-end badge bg-{{ $corte->diferencia == 0 ? 'success' : ($corte->diferencia > 0 ? 'warning' : 'danger') }}">
                                    ${{ number_format($corte->diferencia ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($corte->notas)
                    <div class="alert alert-info mt-3">
                        <strong>Notas:</strong> {{ $corte->notas }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Desglose de Ventas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Desglose de Ventas</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Efectivo
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($corte->ventas_efectivo??0, 2) }}
                                    </div>
                                    <small>{{ $resumen['ventas_efectivo_count'] }} ventas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Tarjeta
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($corte->ventas_tarjeta??0, 2) }}
                                    </div>
                                    <small>{{ $resumen['ventas_tarjeta_count'] }} ventas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Fiado
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($corte->ventas_fiado??0, 2) }}
                                    </div>
                                    <small>{{ $resumen['ventas_fiado_count'] }} ventas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tipo de Pago</th>
                                    <th class="text-end">Monto</th>
                                    <th class="text-center">Ventas</th>
                                    <th class="text-end">Porcentaje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Efectivo</td>
                                    <td class="text-end">${{ number_format($corte->ventas_efectivo??0, 2) }}</td>
                                    <td class="text-center">{{ $resumen['ventas_efectivo_count'] }}</td>
                                    <td class="text-end">{{ number_format(($corte->ventas_efectivo??0 / $corte->total_ventas) * 100, 2) }}%</td>
                                </tr>
                                <tr>
                                    <td>Tarjeta</td>
                                    <td class="text-end">${{ number_format($corte->ventas_tarjeta??0, 2) }}</td>
                                    <td class="text-center">{{ $resumen['ventas_tarjeta_count'] }}</td>
                                    <td class="text-end">{{ number_format(($corte->ventas_tarjeta??0 / $corte->total_ventas??0) * 100, 2) }}%</td>
                                </tr>
                                <tr>
                                    <td>Fiado</td>
                                    <td class="text-end">${{ number_format($corte->ventas_fiado??0, 2) }}</td>
                                    <td class="text-center">{{ $resumen['ventas_fiado_count'] }}</td>
                                    <td class="text-end">{{ number_format(($corte->ventas_fiado??0 / $corte->total_ventas??0) * 100, 2) }}%</td>
                                </tr>
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <th>TOTAL</th>
                                    <th class="text-end">${{ number_format($corte->total_ventas??0, 2) }}</th>
                                    <th class="text-center">{{ $resumen['total_ventas_count'] }}</th>
                                    <th class="text-end">100%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Lista de Ventas del Día -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ventas del Día ({{ $corte->fecha->format('d/m/Y') }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Total</th>
                                    <th>Tipo Pago</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventas as $venta)
                                <tr>
                                    <td>
                                        <a href="{{ route('ventas.show', $venta->id) }}" class="text-decoration-none">
                                            {{ $venta->folio }}
                                        </a>
                                    </td>
                                    <td>{{ $venta->fecha->format('H:i') }}</td>
                                    <td>{{ $venta->cliente ? $venta->cliente->nombre : 'Cliente no registrado' }}</td>
                                    <td class="text-end">${{ number_format($venta->total??0, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $venta->tipo_pago == 'efectivo' ? 'success' : ($venta->tipo_pago == 'tarjeta' ? 'primary' : 'warning') }}">
                                            {{ $venta->tipo_pago }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $venta->estado == 'completada' ? 'success' : 'secondary' }}">
                                            {{ $venta->estado }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay ventas registradas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Información de Cálculo -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cálculo de Caja</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Monto Inicial:</td>
                            <td class="text-end">${{ number_format($corte->monto_inicial??0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>+ Ventas Efectivo:</td>
                            <td class="text-end">${{ number_format($corte->ventas_efectivo??0, 2) }}</td>
                        </tr>
                        <tr class="table-info">
                            <th>Total Esperado:</th>
                            <th class="text-end">${{ number_format($corte->monto_inicial??0 + $corte->ventas_efectivo??0, 2) }}</th>
                        </tr>
                        <tr>
                            <td>- Monto Final:</td>
                            <td class="text-end">${{ number_format($corte->monto_final ?? 0, 2) }}</td>
                        </tr>
                        <tr class="{{ $corte->diferencia == 0 ? 'table-success' : 'table-danger' }}">
                            <th>Diferencia:</th>
                            <th class="text-end">${{ number_format($corte->diferencia ?? 0, 2) }}</th>
                        </tr>
                    </table>

                    @if($corte->diferencia != 0)
                    <div class="alert alert-{{ $corte->diferencia > 0 ? 'warning' : 'danger' }} mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> 
                        {{ $corte->diferencia > 0 ? 'Hay un excedente de $' . number_format($corte->diferencia??0, 2) : 'Faltan $' . number_format(abs($corte->diferencia??0), 2) }} en caja.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Acciones -->
            @if($corte->estado == 'abierto')
            <div class="card shadow mb-4 border-warning">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">Acciones Disponibles</h6>
                </div>
                <div class="card-body">
                    <form id="form-cerrar-corte">
                        @csrf
                        <div class="mb-3">
                            <label for="monto_final" class="form-label">Monto Final en Caja *</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" 
                                       id="monto_final" name="monto_final" required
                                       value="{{ old('monto_final', $corte->monto_inicial + $corte->ventas_efectivo) }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notas_cierre" class="form-label">Notas de Cierre</label>
                            <textarea class="form-control" id="notas_cierre" name="notas" 
                                      rows="3" placeholder="Observaciones del cierre..."></textarea>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                El monto final debe ser el dinero físico contado en caja.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-lock me-2"></i>Cerrar Corte de Caja
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="card shadow mb-4 border-success">
                <div class="card-header py-3 bg-success">
                    <h6 class="m-0 font-weight-bold text-white">Corte Cerrado</h6>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>¡Corte Cerrado Exitosamente!</h5>
                    <p class="text-muted">El corte fue cerrado el {{ $corte->updated_at->format('d/m/Y \a \l\a\s H:i') }}</p>
                    
                    @if($corte->diferencia == 0)
                    <div class="alert alert-success">
                        <i class="fas fa-check me-2"></i>
                        Perfecto! No hay diferencias en caja.
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Estadísticas Rápidas -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estadísticas Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <i class="fas fa-receipt me-2 text-primary"></i>
                        <strong>Total Ventas:</strong> 
                        <span class="float-end">{{ $resumen['total_ventas_count'] }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-dollar-sign me-2 text-primary"></i>
                        <strong>Monto Total:</strong> 
                        <span class="float-end">${{ number_format($corte->total_ventas??0, 2) }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-credit-card me-2 text-primary"></i>
                        <strong>Ticket Promedio:</strong> 
                        <span class="float-end">${{ number_format($resumen['total_ventas_count'] > 0 ? $corte->total_ventas / $resumen['total_ventas_count'] : 0, 2) }}</span>
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-users me-2 text-primary"></i>
                        <strong>Clientes Atendidos:</strong> 
                        <span class="float-end">{{ $resumen['clientes_atendidos'] }}</span>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Duración: 
                            @if($corte->estado == 'cerrado')
                                {{ $corte->created_at->diff($corte->updated_at)->format('%h horas %i minutos') }}
                            @else
                                En curso...
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Cierre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas cerrar el corte de caja?</p>
                <div class="alert alert-info">
                    <strong>Monto Final:</strong> $<span id="modal-monto-final">0.00</span><br>
                    <strong>Diferencia:</strong> $<span id="modal-diferencia">0.00</span>
                </div>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirm-close">Sí, Cerrar Corte</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Calcular diferencia en tiempo real
    $('#monto_final').on('input', function() {
        calcularDiferencia();
    });

    function calcularDiferencia() {
        const montoFinal = parseFloat($('#monto_final').val()) || 0;
        const montoInicial = {{ $corte->monto_inicial }};
        const ventasEfectivo = {{ $corte->ventas_efectivo }};
        const totalEsperado = montoInicial + ventasEfectivo;
        const diferencia = montoFinal - totalEsperado;

        $('#modal-monto-final').text(montoFinal.toFixed(2));
        $('#modal-diferencia').text(diferencia.toFixed(2));
    }

    // Formulario de cierre
    $('#form-cerrar-corte').on('submit', function(e) {
        e.preventDefault();
        
        const montoFinal = parseFloat($('#monto_final').val());
        if (isNaN(montoFinal) || montoFinal < 0) {
            Swal.fire('Error', 'Ingrese un monto final válido', 'error');
            return;
        }

        calcularDiferencia();
        $('#confirmModal').modal('show');
    });

    // Confirmar cierre
    $('#confirm-close').click(function() {
        const formData = {
            monto_final: $('#monto_final').val(),
            notas: $('#notas_cierre').val()
        };

        $.ajax({
            url: "{{ route('corte-caja.cerrar', $corte->id) }}",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#confirm-close').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cerrando...');
            },
            success: function(response) {
                $('#confirmModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Corte Cerrado!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                $('#confirm-close').prop('disabled', false).text('Sí, Cerrar Corte');
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            }
        });
    });

    // Inicializar cálculo
    calcularDiferencia();
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
}
.breadcrumb {
    background: transparent;
    margin-bottom: 0;
}
@media print {
    .btn, .breadcrumb, #btn-cerrar-corte {
        display: none !important;
    }
}
</style>
@endsection