@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Historial de Ventas</h1>
    <div>
        <a href="{{ route('ventas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nueva Venta
        </a>
        <a href="{{ route('corte-caja.create') }}" class="btn btn-outline-secondary ms-2">
            <i class="fas fa-calculator me-2"></i>Corte de Caja
        </a>
    </div>
</div>

<!-- Filtros y Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Ventas Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="ventas-hoy">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-hoy">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Ventas del Mes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="ventas-mes">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Mes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-mes">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                    <label for="filtro-tipo-pago" class="form-label">Tipo Pago</label>
                    <select class="form-select" id="filtro-tipo-pago">
                        <option value="">Todos</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="fiado">Fiado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="mb-3">
                    <label for="filtro-cliente" class="form-label">Cliente</label>
                    <select class="form-select" id="filtro-cliente">
                        <option value="">Todos</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
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
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Buscar por folio, cliente...">
                    <button class="btn btn-primary" type="button" id="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="filtro-solo-hoy">
                    <label class="form-check-label" for="filtro-solo-hoy">Solo ventas de hoy</label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de ventas -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Historial de Ventas</h6>
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
            <table id="ventas-table" class="table table-bordered table-hover w-100">
                <thead class="table-dark">
                    <tr>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Tipo Pago</th>
                        <th>Productos</th>
                        <th>Vendedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán via AJAX -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th id="tabla-total">$0.00</th>
                        <th colspan="4"></th>
                    </tr>
                </tfoot>
            </table>
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
                <p class="text-danger"><strong>Advertencia:</strong> Esta acción revertirá el stock de los productos y no se puede deshacer.</p>
                <div class="venta-info">
                    <p><strong>Folio:</strong> <span id="delete-folio"></span></p>
                    <p><strong>Cliente:</strong> <span id="delete-cliente"></span></p>
                    <p><strong>Total:</strong> <span id="delete-total"></span></p>
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
    // DataTable configuration
    var table = $('#ventas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('ventas.index') }}",
            data: function (d) {
                d.search = $('#search-input').val();
                d.fecha_desde = $('#filtro-fecha-desde').val();
                d.fecha_hasta = $('#filtro-fecha-hasta').val();
                d.tipo_pago = $('#filtro-tipo-pago').val();
                d.cliente_id = $('#filtro-cliente').val();
                d.solo_hoy = $('#filtro-solo-hoy').is(':checked');
            }
        },
        columns: [
            { data: 'folio', name: 'folio' },
            { data: 'cliente_nombre', name: 'cliente.nombre' },
            { data: 'fecha_formateada', name: 'fecha' },
            { data: 'total_formateado', name: 'total', className: 'text-end' },
            { data: 'tipo_pago_label', name: 'tipo_pago', className: 'text-center' },
            { data: 'total_productos', name: 'total_productos', className: 'text-center', 
              render: function(data, type, row) {
                  return data + ' productos';
              }
            },
            { data: 'vendedor', name: 'usuario.name' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[2, 'desc']],
        drawCallback: function(settings) {
            // Actualizar estadísticas
            var data = settings.json;
            if (data) {
                $('#ventas-hoy').text(data.ventas_hoy || 0);
                $('#total-hoy').text('$' + (parseFloat(data.total_hoy) || 0).toFixed(2));
                $('#ventas-mes').text(data.ventas_mes || 0);
                $('#total-mes').text('$' + (parseFloat(data.total_mes) || 0).toFixed(2));
                $('#tabla-total').text('$' + (parseFloat(data.total_general) || 0).toFixed(2));
            }
            
            // Agregar evento de eliminación
            $('.btn-delete').off('click').on('click', function() {
                var ventaId = $(this).data('id');
                var folio = $(this).data('folio');
                var cliente = $(this).data('cliente');
                var total = $(this).data('total');
                
                $('#delete-folio').text(folio);
                $('#delete-cliente').text(cliente);
                $('#delete-total').text('$' + parseFloat(total).toFixed(2));
                
                $('#deleteModal').data('venta-id', ventaId).modal('show');
            });
        }
    });

    // Eventos de filtros
    $('#filtro-fecha-desde, #filtro-fecha-hasta, #filtro-tipo-pago, #filtro-cliente, #filtro-solo-hoy').change(function() {
        table.ajax.reload();
    });

    $('#search-input').on('keyup', function() {
        table.ajax.reload();
    });

    $('#btn-search').click(function() {
        table.ajax.reload();
    });

    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-fecha-desde').val('');
        $('#filtro-fecha-hasta').val('');
        $('#filtro-tipo-pago').val('');
        $('#filtro-cliente').val('');
        $('#search-input').val('');
        $('#filtro-solo-hoy').prop('checked', false);
        table.ajax.reload();
    });

    $('#btn-refrescar').click(function() {
        table.ajax.reload();
        $(this).addClass('fa-spin');
        setTimeout(() => {
            $(this).removeClass('fa-spin');
        }, 1000);
    });

    // Exportar datos
    $('#btn-exportar').click(function() {
        Swal.fire({
            title: 'Exportar Datos',
            text: 'Selecciona el formato de exportación',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Excel',
            cancelButtonText: 'PDF',
            showDenyButton: true,
            denyButtonText: 'CSV'
        }).then((result) => {
            if (result.isConfirmed) {
                exportarVentas('excel');
            } else if (result.isDenied) {
                exportarVentas('csv');
            } else if (result.dismiss === 'cancel') {
                exportarVentas('pdf');
            }
        });
    });

    function exportarVentas(formato) {
        var params = new URLSearchParams({
            fecha_desde: $('#filtro-fecha-desde').val(),
            fecha_hasta: $('#filtro-fecha-hasta').val(),
            tipo_pago: $('#filtro-tipo-pago').val(),
            cliente_id: $('#filtro-cliente').val(),
            formato: formato
        });

        window.open("{{ route('ventas.index') }}?exportar=1&" + params.toString(), '_blank');
    }

    // Eliminar venta
    $('#confirm-delete').click(function() {
        var ventaId = $('#deleteModal').data('venta-id');
        
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
                $('#deleteModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Eliminado', response.message, 'success');
            },
            error: function(xhr) {
                $('#confirm-delete').prop('disabled', false).text('Eliminar');
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            },
            complete: function() {
                $('#confirm-delete').prop('disabled', false).text('Eliminar');
            }
        });
    });

    // Auto-seleccionar fechas para "solo hoy"
    $('#filtro-solo-hoy').change(function() {
        if ($(this).is(':checked')) {
            var hoy = new Date().toISOString().split('T')[0];
            $('#filtro-fecha-desde').val(hoy);
            $('#filtro-fecha-hasta').val(hoy);
            table.ajax.reload();
        }
    });

    // Inicializar fecha hasta con hoy
    var hoy = new Date().toISOString().split('T')[0];
    $('#filtro-fecha-hasta').val(hoy);
});
</script>

<style>
.table-responsive {
    min-height: 400px;
}
.venta-info {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    margin-top: 10px;
}
.card {
    border: none;
    border-radius: 10px;
}
.table th {
    border-top: none;
    font-weight: 600;
}
#ventas-table tbody tr {
    cursor: pointer;
}
#ventas-table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection