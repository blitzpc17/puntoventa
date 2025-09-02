@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Registrar Venta</h1>
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Productos</h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="search-product" placeholder="Buscar producto...">
                    <button class="btn btn-primary" type="button" id="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="productos-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Existencia</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="carrito-body">
                            <!-- Los productos se añadirán aquí dinámicamente -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Total:</th>
                                <th id="total-venta">$0.00</th>
                                <th></th>
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
                <h6 class="m-0 font-weight-bold text-primary">Información de Venta</h6>
            </div>
            <div class="card-body">
                <form id="venta-form">
                    <div class="mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id">
                            <option value="">Seleccionar cliente</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo_pago" class="form-label">Tipo de Pago</label>
                        <select class="form-select" id="tipo_pago" name="tipo_pago" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="fiado">Fiado</option>
                        </select>
                    </div>
                    
                    <div id="fiado-info" class="alert alert-info d-none">
                        <small>El cliente deberá pagar antes de la fecha límite.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" id="btn-finalizar">
                        <i class="fas fa-check me-2"></i>Finalizar Venta
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    let carrito = [];
    let total = 0;
    
    // Buscar producto
    $('#btn-search').click(buscarProducto);
    $('#search-product').on('keypress', function(e) {
        if(e.which === 13) {
            buscarProducto();
            return false;
        }
    });
    
    // Mostrar/ocultar info de fiado
    $('#tipo_pago').change(function() {
        if ($(this).val() === 'fiado') {
            $('#fiado-info').removeClass('d-none');
        } else {
            $('#fiado-info').addClass('d-none');
        }
    });
    
    function buscarProducto() {
        const term = $('#search-product').val();
        
        if (term.length < 2) {
            alert('Ingrese al menos 2 caracteres para buscar');
            return;
        }
        
        $.ajax({
            url: "{{ route('productos.buscar') }}",
            method: 'GET',
            data: { term: term },
            success: function(response) {
                if (response.length > 0) {
                    // Mostrar modal de selección o agregar directamente si hay uno
                    if (response.length === 1) {
                        agregarAlCarrito(response[0]);
                    } else {
                        // Mostrar modal con opciones
                        mostrarModalProductos(response);
                    }
                } else {
                    alert('No se encontraron productos');
                }
            }
        });
    }
    
    function agregarAlCarrito(producto) {
        // Verificar si ya está en el carrito
        const index = carrito.findIndex(item => item.id === producto.id);
        
        if (index !== -1) {
            // Incrementar cantidad
            carrito[index].cantidad += 1;
            carrito[index].subtotal = carrito[index].precio_venta * carrito[index].cantidad;
        } else {
            // Agregar nuevo producto
            carrito.push({
                id: producto.id,
                nombre: producto.nombre,
                precio_venta: producto.precio_venta,
                existencia: producto.existencia,
                cantidad: 1,
                subtotal: producto.precio_venta
            });
        }
        
        actualizarCarrito();
        $('#search-product').val('');
    }
    
    function actualizarCarrito() {
        const tbody = $('#carrito-body');
        tbody.empty();
        total = 0;
        
        carrito.forEach((producto, index) => {
            total += producto.subtotal;
            
            const tr = $('<tr>');
            tr.append($('<td>').text(producto.nombre));
            tr.append($('<td>').text('$' + producto.precio_venta.toFixed(2)));
            tr.append($('<td>').text(producto.existencia));
            
            const inputCantidad = $('<input>')
                .attr('type', 'number')
                .attr('min', 1)
                .attr('max', producto.existencia)
                .addClass('form-control')
                .val(producto.cantidad)
                .on('change', function() {
                    const nuevaCantidad = parseInt($(this).val());
                    if (nuevaCantidad > 0 && nuevaCantidad <= producto.existencia) {
                        carrito[index].cantidad = nuevaCantidad;
                        carrito[index].subtotal = producto.precio_venta * nuevaCantidad;
                        actualizarCarrito();
                    } else {
                        alert('Cantidad no válida');
                        $(this).val(producto.cantidad);
                    }
                });
                
            tr.append($('<td>').append(inputCantidad));
            tr.append($('<td>').text('$' + producto.subtotal.toFixed(2)));
            
            const btnEliminar = $('<button>')
                .addClass('btn btn-sm btn-danger')
                .html('<i class="fas fa-trash"></i>')
                .on('click', function() {
                    carrito.splice(index, 1);
                    actualizarCarrito();
                });
                
            tr.append($('<td>').append(btnEliminar));
            tbody.append(tr);
        });
        
        $('#total-venta').text('$' + total.toFixed(2));
    }
    
    // Finalizar venta
    $('#venta-form').on('submit', function(e) {
        e.preventDefault();
        
        if (carrito.length === 0) {
            alert('Debe agregar al menos un producto');
            return;
        }
        
        if ($('#tipo_pago').val() === 'fiado' && !$('#cliente_id').val()) {
            alert('Para ventas a fiado debe seleccionar un cliente');
            return;
        }
        
        const productos = carrito.map(item => {
            return {
                id: item.id,
                cantidad: item.cantidad
            };
        });
        
        const formData = {
            cliente_id: $('#cliente_id').val(),
            tipo_pago: $('#tipo_pago').val(),
            productos: productos
        };
        
        $.ajax({
            url: "{{ route('ventas.store') }}",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.message);
                window.location.href = "{{ route('ventas.show', '') }}/" + response.venta_id;
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    });
});
</script>
@endsection