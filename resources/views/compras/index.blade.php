@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Gestión de Compras</h1>
    <a href="{{ route('compras.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nueva Compra
    </a>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-input" placeholder="Buscar por folio o proveedor...">
                    <button class="btn btn-outline-secondary" type="button" id="btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="date" class="form-control" id="filtro-fecha">
                    <button class="btn btn-outline-secondary" type="button" id="btn-limpiar-filtros">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
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
                            Total Compras
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-compras">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                            Compras Este Mes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="compras-mes">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            Total Gastado
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-gastado">$0.00</div>
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
                            Promedio por Compra
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="promedio-compra">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de compras -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="compras-table" class="table table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Folio</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // DataTable configuration
    var table = $('#compras-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('compras.index') }}",
            data: function (d) {
                d.search = $('#search-input').val();
                d.fecha = $('#filtro-fecha').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'folio', name: 'folio' },
            { data: 'proveedor_nombre', name: 'proveedor.nombre' },
            { data: 'fecha_formateada', name: 'fecha' },
            { data: 'total_formateado', name: 'total', orderable: false, searchable: false },
            { data: 'estado_label', name: 'estado', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[0, 'desc']]
    });

    // Búsqueda en tiempo real
    $('#search-input').on('keyup', function() {
        table.ajax.reload();
    });

    $('#filtro-fecha').change(function() {
        table.ajax.reload();
    });

    $('#btn-search').click(function() {
        table.ajax.reload();
    });

    $('#btn-limpiar-filtros').click(function() {
        $('#search-input').val('');
        $('#filtro-fecha').val('');
        table.ajax.reload();
    });

    // Delete compra
    $(document).on('click', '.delete', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción revertirá el stock de los productos",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('compras.index') }}/" + id,
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