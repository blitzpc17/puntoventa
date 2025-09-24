@extends('layout.app')

@push('css')

<style>
.loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    color: white;
}

.loader-content {
    text-align: center;
    background: rgba(255, 255, 255, 0.1);
    padding: 30px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.loader-overlay .spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Agregar en tu CSS */
#productos-table .editable-input {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    width: 100%;
    background-color: #f8f9fa;
}

#productos-table .editable-input:focus {
    background-color: #fff;
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

#productos-table td {
    vertical-align: middle;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

</style>
    
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Carga masiva de Productos</h1>
    <div>     
        <a href="{{route('productos.download.layout')}}" class="btn btn-primary">
            <i class="fas fa-file-excel me-2"></i> Descargar Layout
        </a>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productoModal">
            <i class="fas fa-upload me-2"></i> Carga masiva
        </button>
        <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#stockModal">
            <i class="fas fa-check-circle me-2"></i> Confirmar registros
        </button>
    </div>
</div>

<!-- Loader overlay -->
<div id="loaderOverlay" class="loader-overlay" style="display: none;">
    <div class="loader-content">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3">Procesando archivo, por favor espere...</p>
    </div>
</div>

<!-- Filtros -->
<div class="card shadow mb-4">   
    <!-- Contenido de filtros -->
</div>

<!-- Alert si hay detalles al importar los datos-->
<div id="importAlert" style="display: none;"></div>

<!-- Tabla de productos -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="productos-table" class="table table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio Compra</th>
                        <th>Precio Venta</th>
                        <th>Existencia</th>
                        <th>Mínimo</th>
                        <th>Estado</th>
                        <th>Proveedor</th>
                        <th>Status</th>
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
                <h5 class="modal-title" id="modalTitle">Carga masiva de productos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productoForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="layout" class="form-label">Selecciona el layout a importar (.xlsx)</label>
                        <input type="file" class="form-control" id="layout" name="layout" 
                               accept=".xlsx, .xls" required>
                        <div class="form-text">Solo se permite 1 archivo (layout) .xlsx. Tamaño máximo: 10MB</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-upload me-2"></i> Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal para preview -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="previewModalLabel">
                    <i class="fas fa-eye me-2"></i>Vista Previa de Importación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="filename-info"></span> - 
                    <span id="row-count">0 registros</span>
                    <br>
                    <small>Puede editar los datos directamente en la tabla antes de importar.</small>
                </div>
                
                <div class="table-responsive">
                    <table id="productos-table" class="table table-bordered table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <!-- Los encabezados se generan dinámicamente -->
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se llenan dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="submitEditedData()">
                    <i class="fas fa-check me-2"></i>Confirmar Importación
                </button>
            </div>
        </div>
    </div>
</div>


</style>

@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // Configurar CSRF token para todas las peticiones AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Mostrar/Ocultar loader
    function showLoader() {
        $('#loaderOverlay').fadeIn();
        $('body').css('overflow', 'hidden');
    }

    function hideLoader() {
        $('#loaderOverlay').fadeOut();
        $('body').css('overflow', 'auto');
    }

    // Validar archivo antes de enviar
    function validateFile(file) {
        const allowedExtensions = ['.xlsx', '.xls'];
        const maxSize = 10 * 1024 * 1024; // 10MB
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(fileExtension)) {
            return {
                valid: false,
                message: 'Solo se permiten archivos Excel (.xlsx, .xls)'
            };
        }
        
        if (file.size > maxSize) {
            return {
                valid: false,
                message: 'El archivo no debe exceder los 10MB'
            };
        }
        
        return { valid: true };
    }

    // Envío del formulario
    $('#productoForm').on('submit', function(e) {
        e.preventDefault();
        
        const fileInput = $('#layout')[0];
        const file = fileInput.files[0];
        console.log(file)
        
        // Validar archivo
        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'Archivo requerido',
                text: 'Por favor selecciona un archivo para importar'
            });
            return;
        }
        
        const validation = validateFile(file);
        if (!validation.valid) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo inválido',
                text: validation.message
            });
            return;
        }

        // Crear FormData
        const formData = new FormData();
        formData.append('layout', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        // Mostrar confirmación
        Swal.fire({
            title: '¿Importar archivo?',
            text: `Estás a punto de importar: ${file.name}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, importar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                executeImport(formData, file.name);
            }
        });
    });

    // Ejecutar importación
  function executeImport(formData, fileName) {
    showLoader();
    
    // Deshabilitar botón de enviar
    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Procesando...');

    $.ajax({
        url: '{{ route("productos.import.preview") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            hideLoader();
            $('#submitBtn').prop('disabled', false).html('<i class="fas fa-upload me-2"></i> Enviar');

            console.log(response);
            
            if (response.success) {
                // Llenar la tabla con los datos del preview
                fillPreviewTable(response.products, response.filename);
                
                // Mostrar el modal de preview si está oculto
                $('#previewModal').modal('show');
                
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en importación',
                    text: response.message || 'Ocurrió un error al procesar el archivo'
                });
            }
        },
        error: function(xhr, status, error) {
            hideLoader();
            $('#submitBtn').prop('disabled', false).html('<i class="fas fa-upload me-2"></i> Enviar');
            
            let errorMessage = 'Error al conectar con el servidor';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 413) {
                errorMessage = 'El archivo es demasiado grande';
            } else if (xhr.status === 422) {
                errorMessage = 'Error de validación en el archivo';
            } else if (xhr.status === 500) {
                errorMessage = 'Error interno del servidor';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `
                    <p>${errorMessage}</p>
                    ${xhr.responseJSON && xhr.responseJSON.errors ? 
                        `<div class="text-start mt-3">
                            <strong>Errores detallados:</strong>
                            <ul class="mt-2">
                                ${Object.values(xhr.responseJSON.errors).map(error => `<li>${error}</li>`).join('')}
                            </ul>
                        </div>` : ''
                    }
                `,
                confirmButtonText: 'Entendido'
            });
        }
    });
}

function fillPreviewTable(products, filename) {
    const tableBody = $('#productos-table tbody');
    tableBody.empty();
    
    // Crear encabezados dinámicos basados en las keys del primer producto
    const headers = Object.keys(products[0] || {});
    const thead = $('#productos-table thead tr');
    thead.empty();
    
    // Agregar encabezados
    headers.forEach(header => {
        thead.append(`<th>${formatHeader(header)}</th>`);
    });
    thead.append('<th>Acciones</th>');
    
    // Llenar la tabla con datos
    products.forEach((product, index) => {
        const row = $('<tr>').attr('data-index', index);
        
        headers.forEach(header => {
            const value = product[header] || '';
            row.append(`
                <td>
                    <input type="text" 
                           class="form-control form-control-sm editable-input" 
                           name="${header}[]" 
                           value="${value}" 
                           data-field="${header}"
                           data-index="${index}">
                </td>
            `);
        });
        
        // Columna de acciones
        row.append(`
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-row" data-index="${index}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `);
        
        tableBody.append(row);
    });
    
    // Actualizar información del archivo
    $('#filename-info').text(`Archivo: ${filename} - ${products.length} registros encontrados`);
    
    // Inicializar DataTable si no existe, o recargar si existe
    if ($.fn.DataTable.isDataTable('#productos-table')) {
        $('#productos-table').DataTable().destroy();
    }
    
    // Inicializar DataTable
    const dataTable = $('#productos-table').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Exportar Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm'
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        pageLength: 10,
        responsive: true,
        order: [], // No ordenar por defecto
        columnDefs: [
            {
                targets: -1, // Última columna (acciones)
                orderable: false,
                searchable: false
            }
        ]
    });
    
    // Agregar evento para eliminar filas
    $('#productos-table').on('click', '.remove-row', function() {
        const index = $(this).data('index');
        dataTable.row($(this).closest('tr')).remove().draw();
        updateRowCount();
    });
    
    // Actualizar contador de filas
    updateRowCount();
}

function formatHeader(header) {
    const headerMap = {
        'codigo': 'Código',
        'proveedor': 'Proveedor',
        'categoria': 'Categoría',
        'nombre': 'Nombre',
        'descripcion': 'Descripción',
        'precio_compra': 'Precio Compra',
        'precio_venta': 'Precio Venta',
        'cantidad': 'Cantidad',
        'existencia_minima': 'Existencia Mínima'
    };
    
    return headerMap[header] || header.replace(/_/g, ' ').toUpperCase();
}

function updateRowCount() {
    const rowCount = $('#productos-table tbody tr').length;
    $('#row-count').text(`${rowCount} registros`);
}

// Función para enviar los datos editados
function submitEditedData() {
    const formData = new FormData();
    const products = [];
    
    // Recopilar datos de la tabla
    $('#productos-table tbody tr').each(function() {
        const product = {};
        $(this).find('.editable-input').each(function() {
            const field = $(this).data('field');
            const value = $(this).val();
            product[field] = value;
        });
        products.push(product);
    });
    
    if (products.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tabla vacía',
            text: 'No hay datos para enviar'
        });
        return;
    }
    
    // Agregar datos al FormData
    formData.append('products', JSON.stringify(products));
    formData.append('_token', '{{ csrf_token() }}');
    
    showLoader();
    
    $.ajax({
        url: '{{ route("productos.import.save") }}', // Ruta para el import final
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            hideLoader();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Importación completada!',
                    html: `
                        <p>${response.message || 'Datos importados correctamente'}</p>
                        ${response.stats ? `
                            <div class="text-start mt-3">
                                <p><strong>Resumen:</strong></p>
                                <p>✓ Registros importados: ${response.stats.imported || 0}</p>
                                <p>✓ Registros actualizados: ${response.stats.updated || 0}</p>
                                <p>✗ Errores: ${response.stats.errors || 0}</p>
                            </div>
                        ` : ''}
                    `,
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    $('#previewModal').modal('hide');
                    
                    // Recargar tabla principal si existe
                    if (typeof reloadProductTable === 'function') {
                        reloadProductTable();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message || 'Error al importar los datos'
                });
            }
        },
        error: function(xhr) {
            hideLoader();
            
            let errorMessage = 'Error al conectar con el servidor';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        }
    });
}

    // Limpiar formulario cuando se cierre el modal
    $('#productoModal').on('hidden.bs.modal', function() {
        $('#productoForm')[0].reset();
        $('#submitBtn').prop('disabled', false).html('<i class="fas fa-upload me-2"></i> Enviar');
    });

    // Mostrar nombre del archivo seleccionado
    $('#layout').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $(this).next('.form-text').html(`Archivo seleccionado: <strong>${fileName}</strong>`);
        }
    });
});

// Función para recargar la tabla (debes implementarla según tu DataTable)
function reloadProductTable() {
    if ($.fn.DataTable.isDataTable('#productos-table')) {
        $('#productos-table').DataTable().ajax.reload();
    }
}
</script>
@endsection