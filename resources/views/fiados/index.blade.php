@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Gestión de Fiados</h1>
    <div>
        <button class="btn btn-outline-secondary" id="btn-exportar">
            <i class="fas fa-download me-2"></i>Exportar
        </button>
        <button class="btn btn-outline-secondary ms-2" id="btn-refrescar">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Estadísticas Resumen -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Fiados
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-fiados">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
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
                            Fiados Pendientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="fiados-pendientes">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Saldo Pendiente Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="saldo-pendiente">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                            Fiados Pagados
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="fiados-pagados">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                    <label for="filtro-estado" class="form-label">Estado</label>
                    <select class="form-select" id="filtro-estado">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendientes</option>
                        <option value="pagado">Pagados</option>
                        <option value="vencido">Vencidos</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <label for="filtro-cliente" class="form-label">Cliente</label>
                    <select class="form-select" id="filtro-cliente">
                        <option value="">Todos los clientes</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
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
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Buscar por cliente, folio...">
                    <button class="btn btn-primary" type="button" id="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-secondary w-100" id="btn-limpiar-filtros">
                    <i class="fas fa-times me-2"></i>Limpiar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alertas Importantes -->
<div class="alert alert-warning mb-4">
    <h6><i class="fas fa-exclamation-triangle me-2"></i>Fiados Vencidos</h6>
    <div class="row">
        <div class="col-md-6">
            <strong>Total vencidos:</strong> <span id="total-vencidos">0</span>
        </div>
        <div class="col-md-6">
            <strong>Monto vencido:</strong> <span id="monto-vencido">$0.00</span>
        </div>
    </div>
</div>

<!-- Tabla de Fiados -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Fiados</h6>
        <span class="badge bg-info" id="total-registros">0 registros</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="fiados-table" class="table table-bordered table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Folio Venta</th>
                        <th>Monto Total</th>
                        <th>Saldo Pendiente</th>
                        <th>Fecha Límite</th>
                        <th>Días Restantes</th>
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
                        <th id="total-monto">$0.00</th>
                        <th id="total-saldo">$0.00</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Abonar -->
<div class="modal fade" id="abonoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Abono</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="abonoForm">
                <div class="modal-body">
                    <input type="hidden" id="fiado_id" name="fiado_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <p class="form-control-static" id="modal-cliente"></p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Monto Total</label>
                                <p class="form-control-static" id="modal-monto-total"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Saldo Pendiente</label>
                                <p class="form-control-static" id="modal-saldo-pendiente"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="monto_abono" class="form-label">Monto del Abono *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" 
                                   id="monto_abono" name="monto" required>
                        </div>
                        <div class="form-text">Máximo: <span id="maximo-abono">$0.00</span></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas_abono" class="form-label">Notas (Opcional)</label>
                        <textarea class="form-control" id="notas_abono" name="notas" rows="2"></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-2"></i>
                            El abono se aplicará inmediatamente al saldo pendiente.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Abono</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Fiado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información del Fiado</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Cliente:</th>
                                <td id="detalle-cliente"></td>
                            </tr>
                            <tr>
                                <th>Folio Venta:</th>
                                <td id="detalle-folio"></td>
                            </tr>
                            <tr>
                                <th>Monto Total:</th>
                                <td id="detalle-monto-total"></td>
                            </tr>
                            <tr>
                                <th>Saldo Pendiente:</th>
                                <td id="detalle-saldo-pendiente"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Información de Pago</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Fecha Límite:</th>
                                <td id="detalle-fecha-limite"></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td id="detalle-estado"></td>
                            </tr>
                            <tr>
                                <th>Días Restantes:</th>
                                <td id="detalle-dias-restantes"></td>
                            </tr>
                            <tr>
                                <th>Fecha Registro:</th>
                                <td id="detalle-fecha-registro"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>Historial de Abonos</h6>
                        <div id="detalle-abonos">
                            <p class="text-muted">Cargando abonos...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable configuration
    var table = $('#fiados-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('fiados.index') }}",
            data: function (d) {
                d.search = $('#search-input').val();
                d.estado = $('#filtro-estado').val();
                d.cliente_id = $('#filtro-cliente').val();
                d.fecha_desde = $('#filtro-fecha-desde').val();
                d.fecha_hasta = $('#filtro-fecha-hasta').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'cliente_nombre', name: 'cliente.nombre' },
            { data: 'folio_venta', name: 'venta.folio' },
            { data: 'monto_formateado', name: 'monto_total', className: 'text-end' },
            { data: 'saldo_formateado', name: 'saldo_pendiente', className: 'text-end' },
            { data: 'fecha_limite_formateada', name: 'fecha_limite' },
            { data: 'dias_restantes', name: 'dias_restantes', className: 'text-center' },
            { data: 'estado_label', name: 'estado', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[5, 'asc']], // Ordenar por fecha límite
        footerCallback: function (row, data, start, end, display) {
            var api = this.api();
            
            // Calcular totales
            var totalMonto = api.column(3, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            var totalSaldo = api.column(4, { page: 'current' }).data().reduce(function (a, b) {
                return a + parseFloat(b.replace('$', '').replace(',', ''));
            }, 0);
            
            // Actualizar footers
            $('#total-monto').text('$' + totalMonto.toFixed(2));
            $('#total-saldo').text('$' + totalSaldo.toFixed(2));
        },
        drawCallback: function(settings) {
            // Actualizar estadísticas
            var data = settings.json;
            if (data) {
                $('#total-fiados').text(data.total_fiados || 0);
                $('#fiados-pendientes').text(data.fiados_pendientes || 0);
                $('#fiados-pagados').text(data.fiados_pagados || 0);
                $('#saldo-pendiente').text('$' + (data.saldo_pendiente || 0).toFixed(2));
                $('#total-vencidos').text(data.fiados_vencidos || 0);
                $('#monto-vencido').text('$' + (data.monto_vencido || 0).toFixed(2));
                $('#total-registros').text(settings.json.recordsTotal + ' registros');
            }
            
            // Agregar eventos a los botones
            $('.btn-abonar').off('click').on('click', function() {
                var fiadoId = $(this).data('id');
                abrirModalAbono(fiadoId);
            });
            
            $('.btn-view').off('click').on('click', function() {
                var fiadoId = $(this).data('id');
                verDetallesFiado(fiadoId);
            });
        }
    });

    // Eventos de filtros
    $('#filtro-estado, #filtro-cliente, #filtro-fecha-desde, #filtro-fecha-hasta').change(function() {
        table.ajax.reload();
    });

    $('#search-input').on('keyup', function() {
        table.ajax.reload();
    });

    $('#btn-search').click(function() {
        table.ajax.reload();
    });

    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-estado').val('');
        $('#filtro-cliente').val('');
        $('#filtro-fecha-desde').val('');
        $('#filtro-fecha-hasta').val('');
        $('#search-input').val('');
        table.ajax.reload();
    });

    $('#btn-refrescar').click(function() {
        table.ajax.reload();
        $(this).addClass('fa-spin');
        setTimeout(() => {
            $(this).removeClass('fa-spin');
        }, 1000);
    });

    // Abrir modal de abono
    function abrirModalAbono(fiadoId) {
        $.ajax({
            url: "{{ route('fiados.index') }}/" + fiadoId,
            method: 'GET',
            success: function(response) {
                $('#fiado_id').val(response.id);
                $('#modal-cliente').text(response.cliente.nombre);
                $('#modal-monto-total').text('$' + parseFloat(response.monto_total).toFixed(2));
                $('#modal-saldo-pendiente').text('$' + parseFloat(response.saldo_pendiente).toFixed(2));
                $('#maximo-abono').text('$' + parseFloat(response.saldo_pendiente).toFixed(2));
                
                // Establecer máximo en el input
                $('#monto_abono').attr('max', response.saldo_pendiente);
                $('#monto_abono').val('');
                $('#notas_abono').val('');
                
                $('#abonoModal').modal('show');
            }
        });
    }

    // Ver detalles del fiado
    function verDetallesFiado(fiadoId) {
        $.ajax({
            url: "{{ route('fiados.index') }}/" + fiadoId,
            method: 'GET',
            success: function(response) {
                // Información básica
                $('#detalle-cliente').text(response.cliente.nombre);
                $('#detalle-folio').text(response.venta.folio);
                $('#detalle-monto-total').text('$' + parseFloat(response.monto_total).toFixed(2));
                $('#detalle-saldo-pendiente').text('$' + parseFloat(response.saldo_pendiente).toFixed(2));
                $('#detalle-fecha-limite').text(response.fecha_limite);
                $('#detalle-estado').html(response.estado_label);
                $('#detalle-dias-restantes').text(response.dias_restantes + ' días');
                $('#detalle-fecha-registro').text(response.created_at);
                
                // Cargar abonos
                cargarAbonosFiado(fiadoId);
                
                $('#detalleModal').modal('show');
            }
        });
    }

    function cargarAbonosFiado(fiadoId) {
        $.ajax({
            url: "{{ route('fiados.index') }}/" + fiadoId + "/abonos",
            method: 'GET',
            success: function(response) {
                if (response.length > 0) {
                    let html = '<div class="table-responsive"><table class="table table-sm">';
                    html += '<thead><tr><th>Fecha</th><th>Monto</th><th>Notas</th></tr></thead><tbody>';
                    
                    response.forEach(function(abono) {
                        html += '<tr>';
                        html += '<td>' + abono.fecha + '</td>';
                        html += '<td class="text-success">$' + parseFloat(abono.monto).toFixed(2) + '</td>';
                        html += '<td>' + (abono.notas || 'Sin notas') + '</td>';
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                    $('#detalle-abonos').html(html);
                } else {
                    $('#detalle-abonos').html('<p class="text-muted">No hay abonos registrados</p>');
                }
            }
        });
    }

    // Formulario de abono
    $('#abonoForm').on('submit', function(e) {
        e.preventDefault();
        
        const monto = parseFloat($('#monto_abono').val());
        const saldoPendiente = parseFloat($('#modal-saldo-pendiente').text().replace('$', ''));
        
        if (monto <= 0) {
            Swal.fire('Error', 'El monto debe ser mayor a cero', 'error');
            return;
        }
        
        if (monto > saldoPendiente) {
            Swal.fire('Error', 'El monto no puede ser mayor al saldo pendiente', 'error');
            return;
        }
        
        const formData = {
            monto: monto,
            notas: $('#notas_abono').val()
        };
        
        const fiadoId = $('#fiado_id').val();
        
        $.ajax({
            url: "{{ url('fiados/abonar') }}/" + fiadoId,
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');
            },
            success: function(response) {
                $('#abonoModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: '¡Abono Registrado!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    table.ajax.reload();
                });
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message, 'error');
                $('button[type="submit"]').prop('disabled', false).text('Registrar Abono');
            }
        });
    });

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
#fiados-table tbody tr {
    cursor: pointer;
}
#fiados-table tbody tr:hover {
    background-color: #f8f9fa;
}
.badge {
    font-size: 0.85em;
}
.alert {
    border-radius: 10px;
}
</style>
@endsection