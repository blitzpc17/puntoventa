@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Gestión de Proveedores</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#proveedorModal">
        <i class="fas fa-plus me-2"></i>Nuevo Proveedor
    </button>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Buscar proveedores...">
                    <button class="btn btn-outline-secondary" type="button" id="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <button class="btn btn-outline-secondary w-100" id="btn-limpiar-busqueda">
                    <i class="fas fa-times me-2"></i>Limpiar Búsqueda
                </button>
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
                            Total Proveedores
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-proveedores">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                            Proveedores Activos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="proveedores-activos">0</div>
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
                            Productos por Proveedores
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-productos">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            Compras Totales
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-compras">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de proveedores -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="proveedores-table" class="table table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>RFC</th>
                        <th>Productos</th>
                        <th>Compras</th>
                        <th>Última Compra</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar proveedor -->
<div class="modal fade" id="proveedorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Agregar Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="proveedorForm">
                <div class="modal-body">
                    <input type="hidden" id="proveedor_id" name="id">
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
                                <label for="contacto" class="form-label">Persona de Contacto</label>
                                <input type="text" class="form-control" id="contacto" name="contacto">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div class="invalid-feedback" id="email-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3"></textarea>
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
                <h5 class="modal-title">Detalles del Proveedor</h5>
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
                                <th>Contacto:</th>
                                <td id="detalle-contacto"></td>
                            </tr>
                            <tr>
                                <th>Teléfono:</th>
                                <td id="detalle-telefono"></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td id="detalle-email"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Estadísticas</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>Total Productos:</th>
                                <td id="detalle-total-productos"></td>
                            </tr>
                            <tr>
                                <th>Total Compras:</th>
                                <td id="detalle-total-compras"></td>
                            </tr>
                            <tr>
                                <th>Última Compra:</th>
                                <td id="detalle-ultima-compra"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h6>Dirección y Notas</h6>
                        <p id="detalle-direccion" class="text-muted"></p>
                        <p id="detalle-notas" class="text-muted"></p>
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
    var table = $('#proveedores-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('proveedores.index') }}",
            data: function (d) {
                d.search = $('#search-input').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nombre', name: 'nombre' },
            { data: 'contacto', name: 'contacto', defaultContent: 'N/A' },
            { data: 'telefono', name: 'telefono', defaultContent: 'N/A' },
            { data: 'email', name: 'email', defaultContent: 'N/A' },
            { data: 'rfc', name: 'rfc', defaultContent: 'N/A' },
            { data: 'productos_count', name: 'productos_count', searchable: false },
            { data: 'compras_count', name: 'compras_count', searchable: false },
            { data: 'ultima_compra', name: 'ultima_compra', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        drawCallback: function(settings) {
            // Actualizar estadísticas
            var data = settings.json;
            if (data) {
                $('#total-proveedores').text(data.recordsTotal || 0);
                $('#proveedores-activos').text(data.recordsTotal || 0);
                $('#total-productos').text(data.totalProductos || 0);
                $('#total-compras').text('$' + (data.totalCompras || 0).toFixed(2));
            }
        }
    });

    // Búsqueda en tiempo real
    $('#search-input').on('keyup', function() {
        table.ajax.reload();
    });

    $('#btn-search').click(function() {
        table.ajax.reload();
    });

    $('#btn-limpiar-busqueda').click(function() {
        $('#search-input').val('');
        table.ajax.reload();
    });

    // Open modal for new proveedor
    $('#proveedorModal').on('show.bs.modal', function() {
        $('#modalTitle').text('Agregar Proveedor');
        $('#proveedorForm')[0].reset();
        $('#proveedor_id').val('');
        $('.invalid-feedback').text('').hide();
        $('.is-invalid').removeClass('is-invalid');
    });

    // Edit proveedor
    $(document).on('click', '.edit', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('proveedores.index') }}/" + id, function(data) {
            $('#modalTitle').text('Editar Proveedor');
            $('#proveedor_id').val(data.id);
            $('#nombre').val(data.nombre);
            $('#rfc').val(data.rfc);
            $('#contacto').val(data.contacto);
            $('#telefono').val(data.telefono);
            $('#email').val(data.email);
            $('#direccion').val(data.direccion);
            $('#notas').val(data.notas);
            $('#proveedorModal').modal('show');
        });
    });

    // View proveedor details
    $(document).on('dblclick', '#proveedores-table tbody tr', function() {
        var data = table.row(this).data();
        if (data) {
            $.get("{{ route('proveedores.index') }}/" + data.id, function(proveedor) {
                $('#detalle-nombre').text(proveedor.nombre);
                $('#detalle-rfc').text(proveedor.rfc || 'N/A');
                $('#detalle-contacto').text(proveedor.contacto || 'N/A');
                $('#detalle-telefono').text(proveedor.telefono || 'N/A');
                $('#detalle-email').text(proveedor.email || 'N/A');
                $('#detalle-direccion').text(proveedor.direccion || 'Sin dirección');
                $('#detalle-notas').text(proveedor.notas || 'Sin notas');
                $('#detalle-total-productos').text(proveedor.productos_count);
                $('#detalle-total-compras').text('$' + proveedor.total_compras.toFixed(2));
                $('#detalle-ultima-compra').text(proveedor.ultima_compra || 'Nunca');
                $('#detalleModal').modal('show');
            });
        }
    });

    // Save proveedor
    $('#proveedorForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = "{{ route('proveedores.store') }}";
        var method = 'POST';
        
        if ($('#proveedor_id').val()) {
            url = "{{ route('proveedores.index') }}/" + $('#proveedor_id').val();
            method = 'PUT';
        }
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                $('#proveedorModal').modal('hide');
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
    $('#proveedorForm input').on('input', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').hide();
    });

    // Delete proveedor
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
                    url: "{{ route('proveedores.index') }}/" + id,
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