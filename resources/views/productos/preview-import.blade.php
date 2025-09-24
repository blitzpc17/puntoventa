@extends('layout.app')

@push('css')

<!-- <link rel="stylesheet" href="/css/select2.css">-->

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

.select2-container {
    z-index: 1060 !important;
}

.table-danger {
    background-color: #f8d7da !important;
}

.select2-selection {
    border: 1px solid #ced4da !important;
    height: 31px !important;
}

.select2-selection__rendered {
    line-height: 29px !important;
}

.select2-selection__arrow {
    height: 29px !important;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.775rem;
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
            <table id="productos-table-result" class="table table-bordered table-hover w-100">
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
                    <span id="row-count"></span>
                </div>
                
                <div class="alert alert-warning" id="removed-alert" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tienes <span id="removed-count">0</span> filas marcadas para eliminación. 
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeMarkedRows()">
                        Eliminar permanentemente
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning ms-1" onclick="restoreAllRows()">
                        Restaurar todas
                    </button>
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
                <button type="button" class="btn btn-warning" onclick="restoreAllRows()">
                    <i class="fas fa-undo me-2"></i>Restaurar Todas
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

 <!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Para traducción al español (opcional) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/es.min.js"></script>

<script src="/js/select2.js"></script>

<script>

// Función para eliminar todas las filas marcadas para eliminación
    function removeMarkedRows() {
        const markedRows = $('#productos-table tbody tr.table-danger');
        
        if (markedRows.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Sin filas para eliminar',
                text: 'No hay filas marcadas para eliminación'
            });
            return;
        }
        
        Swal.fire({
            title: '¿Eliminar filas marcadas?',
            text: `Se eliminarán ${markedRows.length} filas permanentemente`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                markedRows.remove();
                
                // Reindexar las filas restantes
                reindexRows();
                
                // Actualizar DataTable
                if ($.fn.DataTable.isDataTable('#productos-table')) {
                    $('#productos-table').DataTable().draw();
                }
                
                updateRowCount();
                updateRemovedCount();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Filas eliminadas',
                    text: `${markedRows.length} filas han sido eliminadas permanentemente`
                });
            }
        });
    }

    // Función para restaurar todas las filas
    function restoreAllRows() {
        const removedRows = $('#productos-table tbody tr.table-danger');
        
        if (removedRows.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Sin filas para restaurar',
                text: 'No hay filas eliminadas para restaurar'
            });
            return;
        }
        
        removedRows.each(function() {
            const index = $(this).data('index');
            restoreRow(index);
        });
        
        Swal.fire({
            icon: 'success',
            title: 'Filas restauradas',
            text: `${removedRows.length} filas han sido restauradas`
        });
    }

    function restoreRow(index) {
        const row = $(`#productos-table tbody tr[data-index="${index}"]`);
        
        // Mostrar botón eliminar, ocultar restaurar
        row.find('.remove-row').show();
        row.find('.restore-row').hide();
        
        // Habilitar todos los inputs y selects de la fila
        row.find('input, select').prop('disabled', false);
        
        // Quitar estilo de fila eliminada
        row.removeClass('table-danger');
        row.css('opacity', '1');
        
        // Actualizar DataTable
        if ($.fn.DataTable.isDataTable('#productos-table')) {
            $('#productos-table').DataTable().draw();
        }
        
        updateRowCount();
        updateRemovedCount();
    }

    function updateRowCount() {
        const totalRows = $('#productos-table tbody tr').length;
        const activeRows = $('#productos-table tbody tr:not(.table-danger)').length;
        const removedRows = $('#productos-table tbody tr.table-danger').length;
        
        $('#row-count').html(`
            <span class="text-success">${activeRows} activas</span> | 
            <span class="text-danger">${removedRows} eliminadas</span> | 
            <span class="text-primary">${totalRows} total</span>
        `);
    }

    function updateRemovedCount() {
        const removedCount = $('#productos-table tbody tr.table-danger').length;
        $('#removed-count').text(removedCount);
        
        if (removedCount > 0) {
            $('#removed-alert').show();
        } else {
            $('#removed-alert').hide();
        }
    }

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
            
            // Obtener categorías y proveedores existentes (debes tener estas funciones)
            const categorias = getCategorias(); // Array de categorías
            const proveedores = getProveedores(); // Array de proveedores
            
            // Crear encabezados dinámicos
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
                    
                    if (header === 'categoria') {
                        // Select para categorías
                        row.append(`
                            <td>
                                <select class="form-control form-control-sm select2-categoria" 
                                        name="categoria[]" 
                                        data-index="${index}"
                                        style="width: 100%;">
                                    <option value="">Seleccionar categoría</option>
                                    ${categorias.map(cat => 
                                        `<option value="${cat}" ${cat === value ? 'selected' : ''}>${cat}</option>`
                                    ).join('')}
                                </select>
                            </td>
                        `);
                    } else if (header === 'proveedor') {
                        // Select para proveedores
                        row.append(`
                            <td>
                                <select class="form-control form-control-sm select2-proveedor" 
                                        name="proveedor[]" 
                                        data-index="${index}"
                                        style="width: 100%;">
                                    <option value="">Seleccionar proveedor</option>
                                    ${proveedores.map(prov => 
                                        `<option value="${prov}" ${prov === value ? 'selected' : ''}>${prov}</option>`
                                    ).join('')}
                                </select>
                            </td>
                        `);
                    } else {
                        // Input normal para otros campos
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
                    }
                });
                
                // Columna de acciones
                row.append(`
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row" data-index="${index}" title="Eliminar fila">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm restore-row" data-index="${index}" style="display: none;" title="Restaurar fila">
                            <i class="fas fa-undo"></i>
                        </button>
                    </td>
                `);
                
                tableBody.append(row);
            });
            
            // Actualizar información
            $('#filename-info').text(`Archivo: ${filename} - ${products.length} registros encontrados`);
            
            // Inicializar DataTable
            initDataTable();
            
            // Inicializar Select2
            initSelect2();
            
            // Actualizar contador
            updateRowCount();
        }

        function removeRow(index) {
            const row = $(`#productos-table tbody tr[data-index="${index}"]`);
            
            // Ocultar botón eliminar, mostrar restaurar
            row.find('.remove-row').hide();
            row.find('.restore-row').show();
            
            // Deshabilitar todos los inputs y selects de la fila
            row.find('input, select').prop('disabled', true);
            
            // Aplicar estilo de fila eliminada
            row.addClass('table-danger');
            row.css('opacity', '0.6');
            
            // Actualizar DataTable
            if ($.fn.DataTable.isDataTable('#productos-table')) {
                $('#productos-table').DataTable().draw();
            }
            
            updateRowCount();
            updateRemovedCount();
        }    

        function reindexRows() {
            $('#productos-table tbody tr').each(function(newIndex) {
                const oldIndex = $(this).data('index');
                $(this).attr('data-index', newIndex);
                $(this).find('input, select').attr('data-index', newIndex);
                $(this).find('.remove-row, .restore-row').data('index', newIndex);
            });
        }

        

        function initSelect2() {
            // Inicializar Select2 para categorías
            $('.select2-categoria').select2({
                placeholder: 'Seleccionar categoría',
                allowClear: true,
                dropdownParent: $('#previewModal'),
                width: '100%'
            });
            
            // Inicializar Select2 para proveedores
            $('.select2-proveedor').select2({
                placeholder: 'Seleccionar proveedor',
                allowClear: true,
                dropdownParent: $('#previewModal'),
                width: '100%'
            });
            
            // Actualizar Select2 cuando se redibuja DataTable
            $('#productos-table').on('draw.dt', function() {
                $('.select2-categoria').select2({
                    placeholder: 'Seleccionar categoría',
                    allowClear: true,
                    dropdownParent: $('#previewModal'),
                    width: '100%'
                });
                
                $('.select2-proveedor').select2({
                    placeholder: 'Seleccionar proveedor',
                    allowClear: true,
                    dropdownParent: $('#previewModal'),
                    width: '100%'
                });
            });
        }    

        function initDataTable() {
            if ($.fn.DataTable.isDataTable('#productos-table')) {
                $('#productos-table').DataTable().destroy();
            }
            
            const dataTable = $('#productos-table').DataTable({
                dom: '<"row"<"col-sm-12 col-md-4"B><"col-sm-12 col-md-4"l><"col-sm-12 col-md-4"f>>rtip',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Imprimir',
                        className: 'btn btn-info btn-sm'
                    },
                    {
                        text: '<i class="fas fa-trash"></i> Eliminar Marcadas',
                        className: 'btn btn-danger btn-sm',
                        action: function(e, dt, node, config) {
                            removeMarkedRows();
                        }
                    },
                    {
                        text: '<i class="fas fa-undo"></i> Restaurar Todas',
                        className: 'btn btn-warning btn-sm',
                        action: function(e, dt, node, config) {
                            restoreAllRows();
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                pageLength: 10,
                responsive: true,
                order: [],
                columnDefs: [
                    {
                        targets: -1,
                        orderable: false,
                        searchable: false,
                        width: '100px'
                    }
                ]
            });
            
            // Event listeners para botones de acción
            $('#productos-table').on('click', '.remove-row', function() {
                const index = $(this).data('index');
                removeRow(index);
            });
            
            $('#productos-table').on('click', '.restore-row', function() {
                const index = $(this).data('index');
                restoreRow(index);
            });

        

        }

        // Estas funciones deben obtener los datos de tu base de datos
        function getCategorias() {
            // Ejemplo estático - reemplaza con llamada AJAX a tu backend
            return [
                'MISCELANEA',
                'LACTEOS',
                'BEBIDAS',
                'LIMPIEZA',
                'CARNICOS',
                'PANADERIA',
                'FRUTAS Y VERDURAS',
                'ELECTRONICA',
                'ROPA',
                'HOGAR'
            ];
        }

        function getProveedores() {
            // Ejemplo estático - reemplaza con llamada AJAX a tu backend
            return [
                'GAMESA',
                'BIMBO',
                'COCA-COLA',
                'PEPSICO',
                'UNILEVER',
                'PROCTER & GAMBLE',
                'NESTLÉ',
                'DANONE',
                'KELLOGG\'S',
                'LALA'
            ];
        }

        // Versión con AJAX para obtener datos reales
        function loadCategoriasFromServer() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '',
                    type: 'GET',
                    success: function(response) {
                        resolve(response.data || []);
                    },
                    error: function() {
                        resolve([]); // Retornar array vacío en caso de error
                    }
                });
            });
        }

        function loadProveedoresFromServer() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '',
                    type: 'GET',
                    success: function(response) {
                        resolve(response.data || []);
                    },
                    error: function() {
                        resolve([]);
                    }
                });
            });
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