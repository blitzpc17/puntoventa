@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Gestión de Clientes</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clienteModal">
        <i class="fas fa-plus me-2"></i>Nuevo Cliente
    </button>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Buscar clientes...">
                    <button class="btn btn-outline-secondary" type="button" id="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="filtro-estado">
                    <option value="">Todos los estados</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Clientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-clientes">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Clientes Activos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="clientes-activos">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Límite Fiado Total
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="limite-fiado">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de clientes -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="clientes-table" class="table table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>RFC</th>
                        <th>Límite Fiado</th>
                        <th>Saldo Pendiente</th>
                        <th>Disponible</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Agregar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="clienteForm">
                <div class="modal-body">
                    <input type="hidden" id="cliente_id" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <div class="invalid-feedback" id="nombre-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rfc" class="form-label">RFC</label>
                                <input type="text" class="form-control" id="rfc" name="rfc" maxlength="13">
                                <div class="invalid-feedback" id="rfc-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                                <div class="invalid-feedback" id="email-error"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="limite_fiado" class="form-label">Límite de Fiado *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="limite_fiado" name="limite_fiado" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado *</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para ver detalles -->
<div class="modal fade" id="detalleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información General</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Nombre:</th>
                                <td id="detalle-nombre"></td>
                            </tr>
                            <tr>
                                <th>RFC:</th>
                                <td id="detalle-rfc"></td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td id="detalle-telefono"></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td id="detalle-email"></td>
                            </tr>
                            <tr>
                                <th>Estado:</th>
                                <td id="detalle-estado"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Información de Fiado</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Límite de Fiado:</th>
                                <td id="detalle-limite-fiado"></td>
                            </tr>
                            <tr>
                                <th>Saldo Pendiente:</th>
                                <td id="detalle-saldo-pendiente"></td>
                            </tr>
                            <tr>
                                <th>Disponible para Fiado:</th>
                                <td id="detalle-disponible-fiado"></td>
                            </tr>
                            <tr>
                                <th>Total Ventas:</th>
                                <td id="detalle-total-ventas"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>Dirección</h6>
                        <p id="detalle-direccion" class="text-muted"></p>
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
    var table = $('#clientes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('clientes.index') }}",
            data: function (d) {
                d.search = $('#search-input').val();
                d.estado = $('#filtro-estado').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nombre', name: 'nombre' },
            { data: 'telefono', name: 'telefono', defaultContent: 'N/A' },
            { data: 'email', name: 'email', defaultContent: 'N/A' },
            { data: 'rfc', name: 'rfc', defaultContent: 'N/A' },
            { data: 'limite_fiado', name: 'limite_fiado', render: function(data) {
                return '$' + parseFloat(data).toFixed(2);
            }},
            { data: 'saldo_pendiente', name: 'saldo_pendiente', orderable: false, searchable: false },
            { data: 'disponible_fiado', name: 'disponible_fiado', orderable: false, searchable: false },
            { data: 'estado_label', name: 'estado', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        drawCallback: function(settings) {
            // Actualizar estadísticas
            var data = settings.json;
            if (data) {
                $('#total-clientes').text(data.recordsTotal || 0);
                $('#clientes-activos').text(data.clientesActivos || 0);
                $('#saldo-pendiente').text('$' + (data.saldoPendiente || 0).toFixed(2));
                $('#limite-fiado').text('$' + (data.limiteFiado || 0).toFixed(2));
            }
        }
    });

    // Búsqueda en tiempo real
    $('#search-input').on('keyup', function() {
        table.ajax.reload();
    });

    $('#filtro-estado').change(function() {
        table.ajax.reload();
    });

    $('#btn-search').click(function() {
        table.ajax.reload();
    });

    // Open modal for new cliente
    $('#clienteModal').on('show.bs.modal', function() {
        $('#modalTitle').text('Agregar Cliente');
        $('#clienteForm')[0].reset();
        $('#cliente_id').val('');
        $('.invalid-feedback').text('').hide();
        $('.is-invalid').removeClass('is-invalid');
    });

    // Edit cliente
    $(document).on('click', '.edit', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('clientes.index') }}/" + id, function(data) {
            $('#modalTitle').text('Editar Cliente');
            $('#cliente_id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#rfc').val(data.rfc);
            $('#telefono').val(data.telefono);
            $('#email').val(data.email);
            $('#direccion').val(data.direccion);
            $('#limite_fiado').val(data.limite_fiado);
            $('#estado').val(data.estado);
            $('#clienteModal').modal('show');
        });
    });

    // View cliente details
    $(document).on('click', '.view', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('clientes.index') }}/" + id, function(cliente) {
            $('#detalle-nombre').text(cliente.nombre);
            $('#detalle-rfc').text(cliente.rfc || 'N/A');
            $('#detalle-telefono').text(cliente.telefono || 'N/A');
            $('#detalle-email').text(cliente.email || 'N/A');
            $('#detalle-estado').html(cliente.estado == 'activo' 
                ? '<span class="badge bg-success">Activo</span>' 
                : '<span class="badge bg-danger">Inactivo</span>');
            $('#detalle-limite-fiado').text('$' + parseFloat(cliente.limite_fiado).toFixed(2));
            $('#detalle-saldo-pendiente').text('$' + parseFloat(cliente.saldo_pendiente).toFixed(2));
            $('#detalle-disponible-fiado').text('$' + parseFloat(cliente.disponible_fiado).toFixed(2));
            $('#detalle-total-ventas').text(cliente.ventas_count || 0);
            $('#detalle-direccion').text(cliente.direccion || 'Sin dirección');
            $('#detalleModal').modal('show');
        });
    });

    // Save cliente
    $('#clienteForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = "{{ route('clientes.store') }}";
        var method = 'POST';
        
        if ($('#cliente_id').val()) {
            url = "{{ route('clientes.index') }}/" + $('#cliente_id').val();
            method = 'PUT';
        }
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                $('#clienteModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Éxito', response.message, 'success');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key + '-error').text(value[0]).show();
                    });
                } else {
                    Swal.fire('Error', xhr.responseJSON.message, 'error');
                }
            }
        });
    });

    // Remove validation on input change
    $('#clienteForm input').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').hide();
    });

    // Delete cliente
    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('clientes.index') }}/" + id,
                    method: 'DELETE',
                    success: function(response) {
                        table.ajax.reload();
                        Swal.fire('Eliminado', response.message, 'success');
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