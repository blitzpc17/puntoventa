@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Cortes de Caja</h1>
    <a href="{{ route('corte-caja.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nuevo Corte
    </a>
</div>

<!-- Resumen de Cortes -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Cortes Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="cortes-hoy">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Cortes Abiertos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="cortes-abiertos">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-lock-open fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Ventas Totales (Mes)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="ventas-totales">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
</div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Diferencia Promedio
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="diferencia-promedio">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filtros de Búsqueda</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="filtro-fecha-desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="filtro-fecha-desde">
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="filtro-fecha-hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="filtro-fecha-hasta">
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label for="filtro-estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro-estado">
                        <option value="">Todos</option>
                        <option value="abierto">Abiertos</option>
                        <option value="cerrado">Cerrados</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label for="filtro-usuario" class="form-label">Usuario</label>
                    <select class="form-select" id="filtro-usuario">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-secondary w-100" id="btn-limpiar-filtros">
                    <i class="fas fa-times me-2"></i>Limpiar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de cortes de caja -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Historial de Cortes de Caja</h6>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary btn-sm" id="btn-exportar">
                <i class="fas fa-download me-1"></i>Exportar
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-refrescar">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="cortes-table" class="table table-bordered table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Monto Inicial</th>
                        <th>Monto Final</th>
                        <th>Ventas Efectivo</th>
                        <th>Ventas Tarjeta</th>
                        <th>Ventas Fiado</th>
                        <th>Total Ventas</th>
                        <th>Diferencia</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán via AJAX -->
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <th colspan="3" class="text-end">Totales:</th>
                        <th id="total-inicial">$0.00</th>
                        <th id="total-final">$0.00</th>
                        <th id="total-efectivo">$0.00</th>
                        <th id="total-tarjeta">$0.00</th>
                        <th id="total-fiado">$0.00</th>
                        <th id="total-ventas">$0.00</th>
                        <th id="total-diferencia">$0.00</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Corte de Caja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Usuario:</th>
                                <td id="detalle-usuario"></td>
                            </tr>
                            <tr>
                                <th>Fecha:</th>
                                <td id="detalle-fecha"></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td id="detalle-estado"></td>
                            </tr>
                            <tr>
                                <th>Hora Apertura:</th>
                                <td id="detalle-apertura"></td>
                            </tr>
                            <tr>
                                <th>Hora Cierre:</th>
                                <td id="detalle-cierre"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Información Financiera</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Monto Inicial:</th>
                                <td id="detalle-inicial"></td>
                            </tr>
                            <tr>
                                <th>Monto Final:</th>
                                <td id="detalle-final"></td>
                            </tr>
                            <tr>
                                <th>Total Esperado:</th>
                                <td id="detalle-esperado"></td>
                            </tr>
                            <tr>
                                <th>Diferencia:</th>
                                <td id="detalle-diferencia"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Desglose de Ventas</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Ventas Efectivo:</th>
                                <td id="detalle-efectivo"></td>
                            </tr>
                            <tr>
                                <th>Ventas Tarjeta:</th>
                                <td id="detalle-tarjeta"></td>
                            </tr>
                            <tr>
                                <th>Ventas Fiado:</th>
                                <td id="detalle-fiado"></td>
                            </tr>
                            <tr class="table-primary">
                                <th>Total Ventas:</th>
                                <td id="detalle-total-ventas"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Notas</h6>
                        <p id="detalle-notas" class="text-muted"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btn-imprimir-detalle">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable configuration
    var table = $('#cortes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('corte-caja.index') }}",
            data: function (d) {
                d.fecha_desde = $('#filtro-fecha-desde').val();
                d.fecha_hasta = $('#filtro-fecha-hasta').val();
                d.estado = $('#filtro-estado').val();
                d.usuario_id = $('#filtro-usuario').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'usuario_nombre', name: 'usuario.name' },
            { data: 'fecha_formateada', name: 'fecha' },
            { data: 'monto_inicial_formateado', name: 'monto_inicial', className: 'text-end' },
            { data: 'monto_final_formateado', name: 'monto_final', className: 'text-end' },
            { data: 'ventas_efectivo_formateado', name: 'ventas_efectivo', className: 'text-end' },
            { data: 'ventas_tarjeta_formateado', name: 'ventas_tarjeta', className: 'text-end' },
            { data: 'ventas_fiado_formateado', name: 'ventas_fiado', className: 'text-end' },
            { data: 'total_ventas_formateado', name: 'total_ventas', className: 'text-end' },
            { data: 'diferencia_formateado', name: 'diferencia', className: 'text-end' },
            { data: 'estado_label', name: 'estado', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'desc']],
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            
            // Calcular totales
            var totalInicial = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            var totalFinal = api.column(4, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            var totalEfectivo = api.column(5, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            var totalTarjeta = api.column(6, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            var totalFiado = api.column(7, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            var totalVentas = api.column(8, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            var totalDiferencia = api.column(9, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            // Actualizar footers
            $('#total-inicial').text('$' + totalInicial.toFixed(2));
            $('#total-final').text('$' + totalFinal.toFixed(2));
            $('#total-efectivo').text('$' + totalEfectivo.toFixed(2));
            $('#total-tarjeta').text('$' + totalTarjeta.toFixed(2));
            $('#total-fiado').text('$' + totalFiado.toFixed(2));
            $('#total-ventas').text('$' + totalVentas.toFixed(2));
            $('#total-diferencia').text('$' + totalDiferencia.toFixed(2));
        },
        drawCallback: function(settings) {
            // Actualizar estadísticas
            var data = settings.json;
            if (data) {
                $('#cortes-hoy').text(data.cortes_hoy || 0);
                $('#cortes-abiertos').text(data.cortes_abiertos || 0);
                $('#ventas-totales').text('$' + (data.ventas_totales || 0).toFixed(2));
                $('#diferencia-promedio').text('$' + (data.diferencia_promedio || 0).toFixed(2));
            }
            
            // Agregar evento de detalles
            $('.btn-view').off('click').on('click', function() {
                var corteId = $(this).data('id');
                cargarDetallesCorte(corteId);
            });
        }
    });

    // Eventos de filtros
    $('#filtro-fecha-desde, #filtro-fecha-hasta, #filtro-estado, #filtro-usuario').change(function() {
        table.ajax.reload();
    });

    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-fecha-desde').val('');
        $('#filtro-fecha-hasta').val('');
        $('#filtro-estado').val('');
        $('#filtro-usuario').val('');
        table.ajax.reload();
    });

    $('#btn-refrescar').click(function() {
        table.ajax.reload();
        $(this).addClass('fa-spin');
        setTimeout(() => {
            $(this).removeClass('fa-spin');
        }, 1000);
    });

    // Cargar detalles del corte
    function cargarDetallesCorte(corteId) {
        $.ajax({
            url: "{{ route('corte-caja.index') }}/" + corteId,
            method: 'GET',
            success: function(response) {
                $('#detalle-usuario').text(response.usuario.name);
                $('#detalle-fecha').text(response.fecha);
                $('#detalle-estado').html(response.estado === 'abierto' ? 
                    '<span class="badge bg-warning">Abierto</span>' : 
                    '<span class="badge bg-success">Cerrado</span>');
                $('#detalle-apertura').text(response.created_at);
                $('#detalle-cierre').text(response.updated_at);
                $('#detalle-inicial').text('$' + parseFloat(response.monto_inicial).toFixed(2));
                $('#detalle-final').text('$' + parseFloat(response.monto_final || 0).toFixed(2));
                
                var totalEsperado = parseFloat(response.monto_inicial) + parseFloat(response.ventas_efectivo);
                $('#detalle-esperado').text('$' + totalEsperado.toFixed(2));
                
                $('#detalle-diferencia').html(response.diferencia === 0 ? 
                    '<span class="badge bg-success">$' + parseFloat(response.diferencia || 0).toFixed(2) + '</span>' :
                    '<span class="badge bg-danger">$' + parseFloat(response.diferencia || 0).toFixed(2) + '</span>');
                
                $('#detalle-efectivo').text('$' + parseFloat(response.ventas_efectivo).toFixed(2));
                $('#detalle-tarjeta').text('$' + parseFloat(response.ventas_tarjeta).toFixed(2));
                $('#detalle-fiado').text('$' + parseFloat(response.ventas_fiado).toFixed(2));
                $('#detalle-total-ventas').text('$' + parseFloat(response.total_ventas).toFixed(2));
                $('#detalle-notas').text(response.notas || 'Sin notas');
                
                $('#detalleModal').modal('show');
            }
        });
    }

    // Inicializar fecha hasta con hoy
    var hoy = new Date().toISOString().split('T')[0];
    $('#filtro-fecha-hasta').val(hoy);
    
    // Fecha desde: inicio del mes
    var primerDiaMes = new Date();
    primerDiaMes.setDate(1);
    $('#filtro-fecha-desde').val(primerDiaMes.toISOString().split('T')[0]);
});
</script>

<style>
.table-responsive {
    min-height: 400px;
}
.card {
    border: none;
    border-radius: 10px;
}
.table th {
    border-top: none;
    font-weight: 600;
}
#cortes-table tbody tr {
    cursor: pointer;
}
#cortes-table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection