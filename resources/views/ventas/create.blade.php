@extends('layout.app')

@push('css')

    <link rel="stylesheet" href="/css/select2.css">
    <link rel="stylesheet" href="/css/checkbox.css">
    
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Registrar Nueva Venta</h1>
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Ventas
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Productos de la Venta</h6>
            </div>
            <div class="card-body">
                <!--<div class="input-group mb-3">
                    <input type="text" class="form-control" id="search-product" placeholder="Buscar producto por código o nombre...">
                    <button class="btn btn-primary" type="button" id="btn-search-product">
                        <i class="fas fa-search"></i>
                    </button>
                </div>-->

                <div class="mb-3">
                    <select id="product-select" class="form-control" style="width: 100%">
                        <option></option>
                    </select>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="productos-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
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
                <h6 class="m-0 font-weight-bold text-primary">Información de la Venta</h6>
            </div>
            <div class="card-body">
                <form id="venta-form">
                   
                    <div class="mb-3">
                         <label class="checkbox-item">
                            <input type="checkbox" class="checkbox-input" id="chk-publico" checked>
                            <span class="checkbox-custom">VENTA PÚBLICO GENERAL</span>
                        </label>
                        <!--<label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id">
                            <option value="">Cliente no registrado</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" 
                                    data-limite="{{ $cliente->limite_fiado }}"
                                    data-saldo="{{ $cliente->saldo_pendiente }}">
                                    {{ $cliente->nombre }} - Límite: ${{ number_format($cliente->limite_fiado, 2) }}
                                </option>
                            @endforeach
                        </select>-->
                            
                    </div>

                    <div class="mb-3">
                        <select id="client-select" class="form-control" style="width: 100%">
                                <option></option>
                            </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tipo_pago" class="form-label">Tipo de Pago *</label>
                        <select class="form-select" id="tipo_pago" name="tipo_pago" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="fiado">Fiado</option>
                        </select>
                    </div>
                    
                    <div id="efectivo-section" class="mb-3">
                        <label for="efectivo" class="form-label">Efectivo Recibido *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" class="form-control" id="efectivo" name="efectivo" value="0">
                        </div>
                        <div class="form-text">Cambio: <span id="cambio">$0.00</span></div>
                    </div>
                    
                    <div id="fiado-info" class="alert alert-info d-none">
                        <h6>Información de Fiado</h6>
                        <p>Límite: <span id="limite-fiado">$0.00</span></p>
                        <p>Saldo Pendiente: <span id="saldo-pendiente">$0.00</span></p>
                        <p>Disponible: <span id="disponible-fiado">$0.00</span></p>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 mb-3" id="btn-registrar">
                        <i class="fas fa-check me-2"></i>Generar Venta
                    </button>

                      <button type="submit" class="btn btn-success w-100" id="btn-registrar">
                        <i class="fas fa-print me-2"></i>Generar Venta con Ticket
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
 <!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Para traducción al español (opcional) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>

<script src="/js/select2.js"></script>

<script>

   let productoSeleccionado; 

   // select2
    const productSelect = new Select2ProductManager('#product-select', 'Buscar productos', 'Producto no encontrado', '/api/products' );
    const clientSelect = new Select2ProductManager('#client-select', 'Buscar clientes', 'Cliente no encontrado', '/api/clients', {initialVisible:false} );



    document.querySelectorAll('.checkbox-input').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            console.log('Checkbox cambiado:', this.id, this.checked);
            
          

            if(this.id == "chk-publico"){                
                 if(this.checked){
                    clientSelect.hide();
                 }else{
                    clientSelect.show();
                 }
            }


        });
    });

    


$(document).ready(function() {
    let carrito = [];
    let total = 0;
    


    // Mostrar/ocultar secciones según tipo de pago
    $('#tipo_pago').change(function() {
        const tipo = $(this).val();
        $('#efectivo-section').toggle(tipo === 'efectivo');
        $('#fiado-info').toggle(tipo === 'fiado');
        
        if (tipo === 'fiado') {
            actualizarInfoFiado();
        }
    });
    
    // Actualizar información de fiado
    function actualizarInfoFiado() {
        const clienteId = $('#client-select').val()//$('#cliente_id').val();
        if (!clienteId) {
            Swal.fire('Error', 'Seleccione un cliente para venta a fiado', 'error');
            $('#tipo_pago').val('efectivo').change();
            return;
        }
        
        $.get(`/ajax/clientes/${clienteId}/fiados`, function(cliente) {
            $('#limite-fiado').text('$' + parseFloat(cliente.limite_fiado).toFixed(2));
            $('#saldo-pendiente').text('$' + parseFloat(cliente.saldo_pendiente).toFixed(2));
            $('#disponible-fiado').text('$' + parseFloat(cliente.disponible_fiado).toFixed(2));
        });
    }
    
    // Buscar producto
   /* $('#btn-search-product').click(buscarProducto);
    $('#search-product').on('keypress', function(e) {
        if(e.which === 13) {
            buscarProducto();
            return false;
        }
    });*/

    $("#product-select").on('change', function(){
        buscarProducto()
    })
    
    function buscarProducto() {
        const term = $('#product-select').val();//$('#search-product').val();

        console.log(term)
        
        /*if (term.length < 2) {
            Swal.fire('Error', 'Ingrese al menos 2 caracteres para buscar', 'error');
            return;
        }*/
        
        $.ajax({
            url: "{{ route('ajax.productos.buscar') }}",
            method: 'GET',
            data: { term: term },
            success: function(response) {

                console.log(response)
                productoSeleccionado = response;

                var msjError = "";
                if(response.existencia<=0){

                    msjError = "El producto no cuenta con existencia.";
                    
                } else if(response.existencia < response.min_existencia){
                    msjError = "El producto esta por debajo del stock mínimo, Quedan:"+ response.existencia;
                }

                if(msjError!=""){
                    Swal.fire('Advertencia', msjError, 'warning');
                }else{
                    agregarAlCarrito(response);
                }

            }
        });
    }
    
    function agregarAlCarrito(producto) {
        
        const index = carrito.findIndex(item => item.id === producto.id);
        
        if (index !== -1) {
            if (carrito[index].cantidad >= producto.existencia) {
                Swal.fire('Advertencia', 'No hay suficiente stock', 'warning');
                return;
            }
            carrito[index].cantidad += 1;
            carrito[index].subtotal = carrito[index].precio_venta * carrito[index].cantidad;
        } else {
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
        //$('#search-product').val('');
        $('#product-select').val('');
    }
    
    function actualizarCarrito() {
        const tbody = $('#carrito-body');
        tbody.empty();
        total = 0;
        
        carrito.forEach((producto, index) => {
            total += producto.subtotal;

            console.log(producto)
            
            const tr = $('<tr>');
            tr.append($('<td>').text(producto.nombre));
            tr.append($('<td>').text('$' + parseFloat(producto.precio_venta).toFixed(2)));
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
        
        $('#total-venta').text('$' + parseFloat(total).toFixed(2));
        
        // Actualizar cambio
        const efectivo = parseFloat($('#efectivo').val()) || 0;
        const cambio = efectivo - total;
        $('#cambio').text('$' + Math.max(0, cambio).toFixed(2));
    }
    
    // Calcular cambio en tiempo real
    $('#efectivo').on('input', function() {
        const efectivo = parseFloat($(this).val()) || 0;
        const cambio = efectivo - total;
        $('#cambio').text('$' + Math.max(0, cambio).toFixed(2));
    });
    
    // Registrar venta
    $('#venta-form').on('submit', function(e) {
        e.preventDefault();
        
        if (carrito.length === 0) {
            Swal.fire('Error', 'Debe agregar al menos un producto', 'error');
            return;
        }
        
        const tipoPago = $('#tipo_pago').val();
        
        if (tipoPago === 'fiado' && /*!$('#cliente_id').val()*/ !$('#client-select').val()) {
            Swal.fire('Error', 'Se requiere cliente para ventas a fiado', 'error');
            return;
        }
        
        if (tipoPago === 'efectivo') {
            const efectivo = parseFloat($('#efectivo').val()) || 0;
            if (efectivo < total) {
                Swal.fire('Error', 'El efectivo recibido es menor al total', 'error');
                return;
            }
        }
        
        const productos = carrito.map(item => {
            return {
                id: item.id,
                cantidad: item.cantidad
            };
        });
        
        const formData = {
            cliente_id: $('#client-select').val(),//$('#cliente_id').val(),
            tipo_pago: tipoPago,
            efectivo: $('#efectivo').val(),
            productos: productos
        };
        
        Swal.fire({
            title: 'Registrando venta...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: "{{ route('ventas.store') }}",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.cambio > 0) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Venta Exitosa!',
                        html: `Venta registrada<br>Cambio: $${parseFloat(response.cambio).toFixed(2)}`,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = "{{ route('ventas.show', '') }}/" + response.venta_id;
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Venta Exitosa!',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = "{{ route('ventas.show', '') }}/" + response.venta_id;
                    });
                }
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON.message, 'error');
            }
        });
    });
});
</script>
@endsection