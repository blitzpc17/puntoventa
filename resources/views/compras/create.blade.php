@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Registrar Nueva Compra</h1>
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Compras
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Productos de la Compra</h6>
            </div>
            <div class="card-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="search-product" placeholder="Buscar producto...">
                    <button class="btn btn-primary" type="button" id="btn-search-product">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="productos-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Compra</th>
                                <th>Stock Actual</th>
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
                                <th id="total-compra">$0.00</th>
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
                <h6 class="m-0 font-weight-bold text-primary">Información de la Compra</h6>
            </div>
            <div class="card-body">
                <form id="compra-form">
                    <div class="mb-3">
                        <label for="proveedor_id" class="form-label">Proveedor *</label>
                        <select class="form-select" id="proveedor_id" name="proveedor_id" required>
                            <option value="">Seleccionar proveedor</option>
                            @foreach($proveedores as $proveedor)
                                <option value="{{ $proveedor->id }}">{{ $proveedor->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha *</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notas" class="form-label">Notas</label>
                        <textarea class="form-control" id="notas" name="notas" rows="3" placeholder="Observaciones de la compra..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100" id="btn-registrar">
                        <i class="fas fa-check me-2"></i>Registrar Compra
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
    $('#btn-search-product').click(buscarProducto);
    $('#search-product').on('keypress', function(e) {
        if(e.which === 13) {
            buscarProducto();
            return false;
        }
    });
    
    function buscarProducto() {
        const term = $('#search-product').val();
        
        if (term.length < 2) {
            Swal.fire('Error', 'Ingrese al menos 2 caracteres para buscar', 'error');
            return;
        }
        
        $.ajax({
            url: "{{ route('productos.buscar') }}",
            method: 'GET',
            data: { term: term },
            success: function(response) {
                if (response.length > 0) {
                    if (response.length === 1) {
                        agregarAlCarrito(response[0]);
                    } else {
                        mostrarModalProductos(response);
                    }
                } else {
                    Swal.fire('Info', 'No se encontraron productos', 'info');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Error al buscar productos', 'error');
            }
        });
    }
    
    function mostrarModalProductos(productos) {
        let modalContent = '<div class="list-group">';
        
        productos.forEach(producto => {
            modalContent += `
                <a href="#" class="list-group-item list-group-item-action" data-producto='${JSON.stringify(producto)}'>
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-1">${producto.text}</h6>
                        <small>Stock: ${producto.existencia}</small>
                    </div>
                    <p class="mb-1">Código: ${producto.codigo || 'N/A'}</p>
                    <small>Precio: $${producto.precio_compra}</small>
                </a>
            `;
        });
        
        modalContent += '</div>';
        
        Swal.fire({
            title: 'Seleccionar Producto',
            html: modalContent,
            showConfirmButton: false,
            showCloseButton: true
        });
        
        // Evento para seleccionar producto
        $('.list-group-item').click(function(e) {
            e.preventDefault();
            const producto = $(this).data('producto');
            agregarAlCarrito(producto);
            Swal.close();
        });
    }
    
    function agregarAlCarrito(producto) {
        // Verificar si ya está en el carrito
        const index = carrito.findIndex(item => item.id === producto.id);
        
        if (index !== -1) {
            // Incrementar cantidad
            carrito[index].cantidad += 1;
            carrito[index].subtotal = carrito[index].precio_compra * carrito[index].cantidad;
        } else {
            // Agregar nuevo producto
            carrito.push({
                id: producto.id,
                nombre: producto.text,
                codigo: producto.codigo,
                precio_compra: producto.precio_compra,
                existencia: producto.existencia,
                cantidad: 1,
                subtotal: producto.precio_compra
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
            console.log(producto)
            const tr = $('<tr>');
            tr.append($('<td>').text(producto.nombre + (producto.codigo ? ' (' + producto.codigo + ')' : '')));
            tr.append($('<td>').text('$' + parseFloat(producto.precio_compra).toFixed(2)));
            tr.append($('<td>').text(producto.existencia));
            
            const inputCantidad = $('<input>')
                .attr('type', 'number')
                .attr('min', 1)
                .attr('max', 10000)
                .addClass('form-control')
                .val(producto.cantidad)
                .on('change', function() {
                    const nuevaCantidad = parseInt($(this).val());
                    if (nuevaCantidad > 0 && nuevaCantidad <= 10000) {
                        carrito[index].cantidad = nuevaCantidad;
                        carrito[index].subtotal = producto.precio_compra * nuevaCantidad;
                        actualizarCarrito();
                    } else {
                        Swal.fire('Error', 'Cantidad no válida', 'error');
                        $(this).val(producto.cantidad);
                    }
                });
                
            tr.append($('<td>').append(inputCantidad));
            tr.append($('<td>').text('$' + parseFloat(producto.subtotal).toFixed(2)));
            
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
        
        $('#total-compra').text('$' + parseFloat(total).toFixed(2));
    }
    
    // Registrar compra
    $('#compra-form').on('submit', function(e) {
        e.preventDefault();
        
        if (carrito.length === 0) {
            Swal.fire('Error', 'Debe agregar al menos un producto', 'error');
            return;
        }
        
        if (!$('#proveedor_id').val()) {
            Swal.fire('Error', 'Debe seleccionar un proveedor', 'error');
            return;
        }
        
        const productos = carrito.map(item => {
            return {
                id: item.id,
                cantidad: item.cantidad,
                precio: item.precio_compra
            };
        });
        
        const formData = {
            proveedor_id: $('#proveedor_id').val(),
            fecha: $('#fecha').val(),
            productos: productos,
            notas: $('#notas').val()
        };
        
        // Mostrar loading
        Swal.fire({
            title: 'Registrando compra...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: "{{ route('compras.store') }}",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = "{{ route('compras.show', '') }}/" + response.compra_id;
                });
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            }
        });
    });
});
</script>
@endsection