@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Gestión de Categorías</h1>
    <button class="btn btn-primary" id="btn-add">
        <i class="fas fa-plus me-2"></i>Nueva Categoría
    </button>
</div>



<!-- Tarjetas de Resumen -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Número de Categorías
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
</div>

<!-- Tabla de proveedores -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="categorias-table" class="table table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal para agregar/editar proveedor -->
<div class="modal fade" id="proveedorModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Agregar Categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="proveedorForm">
                <div class="modal-body">
                    <input type="hidden" id="proveedor_id" name="id">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                        <div class="invalid-feedback" id="nombre-error"></div>
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




@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable configuration
    var table = $('#categorias-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('categorias.index') }}",
            data: function (d) {
                d.search = $('#search-input').val();
            }
        },
        columns: [
            { data: null, name: 'consecutivo',  
                render: function (data, type, row, meta) {
                    return meta.settings._iDisplayStart + meta.row + 1;
                },
                orderable: false,
                searchable: false

            },
            { data: 'nombre', name: 'nombre' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        drawCallback: function(settings) {
            // Actualizar estadísticas
            var data = settings.json;
            console.log(data)
          //  alert(data)
            if (data) {
                $('#total-proveedores').text(data.recordsTotal || 0);             
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
   
    $('#btn-add').click(function(){
          $('#modalTitle').text('Agregar Categoría');
        $('#proveedorForm')[0].reset();
        $('#proveedor_id').val('');
        $('.invalid-feedback').text('').hide();
        $('.is-invalid').removeClass('is-invalid');
            $('#proveedorModal').modal('show');
    })

    // Edit proveedor
    $(document).on('click', '.edit', function() {
        var id = $(this).data('id');
        
        $.get("{{ route('categorias.index') }}/" + id, function(data) {
            console.log(data)
            $('#modalTitle').text('Editar Categoría');
            $('#proveedor_id').val(data.id);
            console.log(data.id)
            $('#nombre').val(data.nombre);        
            $('#proveedorModal').modal('show');
        });
    });
    

    // Save proveedor
    $('#proveedorForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var url = "{{ route('categorias.store') }}";
        console.log(url)
        var method = 'POST';
        
        if ($('#proveedor_id').val()) {
            url = "{{ route('categorias.index') }}/" + $('#proveedor_id').val();
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


    
});
</script>
@endsection