@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Gestión de Productos</h1>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productoModal">
            <i class="fas fa-plus me-2"></i> Nuevo Producto
        </button>
        <a href="{{route()}}" class="btn btn-primary">
            <i class="fas fa-upload me-2"></i> Carga Masiva
        </a>
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#stockModal">
            <i class="fas fa-boxes me-2"></i> Ajustar Stock
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form id="filtro-form" class="row g-3">
            <div class="col-md-3">
                <label for="filtro-categoria" class="form-label">Categoría</label>
                <select class="form-select" id="filtro-categoria">
                    <option value="">Todas las categorías</option>
                    <option value="papeleria">Papelería</option>
                    <option value="oficina">Oficina</option>
                    <option value="escolar">Escolar</option>
                    <option value="arte">Arte</option>
                    <option value="impresion">Impresión</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro-stock" class="form-label">Estado de Stock</label>
                <select class="form-select" id="filtro-stock">
                    <option value="">Todos</option>
                    <option value="disponible">Disponible</option>
                    <option value="bajo">Stock Bajo</option>
                    <option value="agotado">Agotado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro-proveedor" class="form-label">Proveedor</label>
                <select class="form-select" id="filtro-proveedor">
                    <option value="">Todos los proveedores</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-secondary w-100" id="btn-limpiar-filtros">
                    <i class="fas fa-times me-2"></i>Limpiar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de productos -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="productos-table" class="table table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Existencia</th>
                        <th>Mínimo</th>
                        <th>Estado</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar producto -->
<div class="modal fade" id="productoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Agregar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productoForm">
                <div class="modal-body">
                    <input type="hidden" id="producto_id" name="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="codigo" class="form-label">Código <small class="text-muted">(Opcional)</small></label>
                                <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Se generará automáticamente si se deja vacío">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="categoria" name="categoria">
                                    <option value="">Seleccionar categoría</option>
                                    <option value="papeleria">Papelería</option>
                                    <option value="oficina">Oficina</option>
                                    <option value="escolar">Escolar</option>
                                    <option value="arte">Arte</option>
                                    <option value="impresion">Impresión</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio_compra" class="form-label">Precio Compra *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="precio_compra" name="precio_compra" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="precio_venta" class="form-label">Precio Venta *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="precio_venta" name="precio_venta" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="existencia" class="form-label">Existencia *</label>
                                <input type="number" min="0" class="form-control" id="existencia" name="existencia" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="min_existencia" class="form-label">Mínimo Existencia *</label>
                                <input type="number" min="0" class="form-control" id="min_existencia" name="min_existencia" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="proveedor_id" class="form-label">Proveedor</label>
                                <select class="form-select" id="proveedor_id" name="proveedor_id">
                                    <option value="">Seleccionar proveedor</option>
                                    @foreach($proveedores as $proveedor)
                                        <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                                    @endforeach
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

<!-- Modal para ajustar stock -->
<!-- Modal para ajustar stock -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stockForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="producto_stock_id" class="form-label">Producto *</label>
                        <select class="form-select" id="producto_stock_id" name="producto_id" required>
                            <option value="">Cargando productos...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_ajuste" class="form-label">Tipo de Ajuste *</label>
                        <select class="form-select" id="tipo_ajuste" name="tipo" required>
                            <option value="incrementar">Incrementar Stock</option>
                            <option value="decrementar">Decrementar Stock</option>
                            <option value="ajustar">Ajustar a Valor Específico</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad_ajuste" class="form-label">Cantidad *</label>
                        <input type="number" min="0" class="form-control" id="cantidad_ajuste" name="cantidad" required>
                    </div>
                    <div class="alert alert-info" id="stock-info">
                        <small>Stock actual: <span id="stock-actual">0</span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Aplicar Ajuste</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

     // Cargar productos cuando se abre el modal de stock
    $('#stockModal').on('show.bs.modal', function() {
        cargarProductosParaStock();
    });

    function cargarProductosParaStock() {
        var select = $('#producto_stock_id');
        select.html('<option value="">Cargando productos...</option>');
        
        $.ajax({
            url: "{{ route('productos.index') }}",
            method: 'GET',
            data: {
                simple: true // Parámetro para obtener datos simples
            },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    select.html('<option value="">Seleccionar producto</option>');
                    
                    $.each(response.data, function(index, producto) {
                        select.append(
                            $('<option>', {
                                value: producto.id,
                                'data-stock': producto.existencia,
                                text: producto.nombre + ' (Stock: ' + producto.existencia + ')'
                            })
                        );
                    });
                } else {
                    select.html('<option value="">No hay productos disponibles</option>');
                }
            },
            error: function(xhr) {
                select.html('<option value="">Error al cargar productos</option>');
                console.error('Error al cargar productos:', xhr);
            }
        });
    }


    // DataTable configuration
    var table = $('#productos-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('productos.index') }}",
            data: function (d) {
                d.categoria = $('#filtro-categoria').val();
                d.stock = $('#filtro-stock').val();
                d.proveedor = $('#filtro-proveedor').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'codigo', name: 'codigo' },
            { data: 'nombre', name: 'nombre' },
            { data: 'categoria', name: 'categoria' },
            { data: 'precio_compra', name: 'precio_compra', render: function(data) {
                return '$' + parseFloat(data).toFixed(2);
            }},
            { data: 'precio_venta', name: 'precio_venta', render: function(data) {
                return '$' + parseFloat(data).toFixed(2);
            }},
            { data: 'existencia', name: 'existencia' },
            { data: 'min_existencia', name: 'min_existencia' },
            { data: 'estado_stock', name: 'estado_stock', orderable: false, searchable: false },
            { data: 'proveedor_nombre', name: 'proveedor.nombre' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });

    // Aplicar filtros
    $('#filtro-categoria, #filtro-stock, #filtro-proveedor').change(function() {
        table.ajax.reload();
    });

    // Limpiar filtros
    $('#btn-limpiar-filtros').click(function() {
        $('#filtro-categoria, #filtro-stock, #filtro-proveedor').val('');
        table.ajax.reload();
    });

    // Open modal for new product
    $('#productoModal').on('show.bs.modal', function() {
        $('#modalTitle').text('Agregar Producto');
        $('#productoForm')[0].reset();
        $('#producto_id').val('');
    });

    // Edit product
    $(document).on('click', '.edit', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('productos.index') }}/" + id, function(data) {
            $('#modalTitle').text('Editar Producto');
            $('#producto_id').val(data.id);
            $('#codigo').val(data.codigo);
            $('#nombre').val(data.nombre);
            $('#descripcion').val(data.descripcion);
            $('#precio_compra').val(data.precio_compra);
            $('#precio_venta').val(data.precio_venta);
            $('#existencia').val(data.existencia);
            $('#min_existencia').val(data.min_existencia);
            $('#categoria').val(data.categoria);
            $('#proveedor_id').val(data.proveedor_id);
            $('#productoModal').modal('show');
        });
    });

    // Save product
    $('#productoForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = "{{ route('productos.store') }}";
        var method = 'POST';
        
        if ($('#producto_id').val()) {
            url = "{{ route('productos.index') }}/" + $('#producto_id').val();
            method = 'PUT';
        }
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                $('#productoModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Éxito', response.message, 'success');
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                
                $.each(errors, function(key, value) {
                    errorMessage += value + '\n';
                });
                
                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });

    // Delete product
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
                    url: "{{ route('productos.index') }}/" + id,
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

    // Stock adjustment functionality
    $('#producto_stock_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var stock = selectedOption.data('stock') || 0;
        $('#stock-actual').text(stock);
    });

    $('#tipo_ajuste').change(function() {
        var tipo = $(this).val();
        var cantidadInput = $('#cantidad_ajuste');
        
        if (tipo === 'decrementar') {
            cantidadInput.attr('min', 1);
        } else {
            cantidadInput.attr('min', 0);
        }
    });

    $('#stockForm').on('submit', function(e) {
         e.preventDefault();
        
        var formData = {
            cantidad: $('#cantidad_ajuste').val(),
            tipo: $('#tipo_ajuste').val()
        };
        
        var productoId = $('#producto_stock_id').val();
        
        if (!productoId) {
            Swal.fire('Error', 'Debe seleccionar un producto', 'error');
            return;
        }
        
        $.ajax({
            url: "{{ route('productos.index') }}/" + productoId + "/stock",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#stockModal').modal('hide');
                table.ajax.reload();
                Swal.fire('Éxito', response.message, 'success');
                
                // Recargar los productos para actualizar los stocks
                cargarProductosParaStock();
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            }
        });
    });
});
</script>
@endsection