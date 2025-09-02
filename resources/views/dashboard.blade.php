@extends('layout.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title">Dashboard</h1>
    <div>
        <span class="me-2 text-muted">{{ now()->format('d/m/Y') }}</span>
        <button class="btn btn-sm btn-outline-primary" id="btn-actualizar">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Fila de Tarjetas de Métricas -->
<div class="row">
    <!-- Total de Ventas Hoy -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Ventas Hoy
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="ventas-hoy">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con Baja Existencia -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Productos Bajos en Stock
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="productos-bajos">0</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fiados Pendientes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Fiados Pendientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="fiados-pendientes">$0.00</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total de Clientes -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
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
</div>

<!-- Gráficos y Tablas -->
<div class="row">
    <!-- Gráfico de Ventas -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ventas de los últimos 7 días</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Más Vendidos -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Productos Más Vendidos</h6>
            </div>
            <div class="card-body">
                <div class="mt-2" id="productos-populares">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tablas de Información Reciente -->
<div class="row">
    <!-- Ventas Recientes -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Ventas Recientes</h6>
                <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-link">Ver todas</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm w-100" id="tabla-ventas-recientes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con Stock Bajo -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Productos con Stock Bajo</h6>
                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-link">Ver todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm w-100" id="tabla-stock-bajo">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Existencia</th>
                                <th>Mínimo</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    // Cargar datos del dashboard
    cargarDashboard();
    
    // Configurar actualización manual
    $('#btn-actualizar').click(function() {
        $(this).addClass('fa-spin');
        cargarDashboard().always(function() {
            $('#btn-actualizar').removeClass('fa-spin');
        });
    });
    
    // Actualizar automáticamente cada 5 minutos
    setInterval(cargarDashboard, 300000);
    
    function cargarDashboard() {
        return $.ajax({
            url: "{{ route('dashboard.data') }}",
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                // Actualizar tarjetas de métricas
                $('#ventas-hoy').text('$' + response.ventas_hoy.toFixed(2));
                $('#productos-bajos').text(response.productos_bajos);
                $('#fiados-pendientes').text('$' + response.fiados_pendientes.toFixed(2));
                $('#total-clientes').text(response.total_clientes);
                
                // Actualizar gráfico de ventas
                actualizarGraficoVentas(response.ventas_ultima_semana);
                
                // Actualizar productos populares
                actualizarProductosPopulares(response.productos_populares);
                
                // Actualizar tablas
                actualizarTablaVentasRecientes(response.ventas_recientes);
                actualizarTablaStockBajo(response.stock_bajo);
            }
        });
    }
    
    function actualizarGraficoVentas(datos) {
        const ctx = document.getElementById('ventasChart').getContext('2d');
        
        // Destruir gráfico anterior si existe
        if (window.ventasChartInstance) {
            window.ventasChartInstance.destroy();
        }
        
        // Crear nuevo gráfico
        window.ventasChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datos.labels,
                datasets: [{
                    label: 'Ventas',
                    data: datos.values,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointRadius: 3,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.raw.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    }
    
    function actualizarProductosPopulares(productos) {
        const container = $('#productos-populares');
        container.empty();
        
        if (productos.length === 0) {
            container.html('<p class="text-center text-muted">No hay datos disponibles</p>');
            return;
        }
        
        productos.forEach((producto, index) => {
            const progress = (producto.total_vendido / productos[0].total_vendido) * 100;
            
            const item = `
                <div class="mb-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>${producto.nombre}</span>
                        <strong>${producto.total_vendido} vendidos</strong>
                    </div>
                    <div class="progress mb-4">
                        <div class="progress-bar" role="progressbar" 
                             style="width: ${progress}%; background-color: ${getColorByIndex(index)}" 
                             aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
            `;
            
            container.append(item);
        });
    }
    
    function getColorByIndex(index) {
        const colors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', 
            '#e74a3b', '#858796', '#f8f9fc', '#5a5c69'
        ];
        return colors[index % colors.length];
    }
    
    function actualizarTablaVentasRecientes(ventas) {
        const tabla = $('#tabla-ventas-recientes tbody');
        tabla.empty();
        
        if (ventas.length === 0) {
            tabla.html('<tr><td colspan="4" class="text-center text-muted">No hay ventas recientes</td></tr>');
            return;
        }
        
        ventas.forEach(venta => {
            const fila = `
                <tr>
                    <td>${venta.id}</td>
                    <td>${venta.cliente_nombre || 'Cliente no registrado'}</td>
                    <td>$${venta.total.toFixed(2)}</td>
                    <td>${new Date(venta.fecha).toLocaleDateString()}</td>
                </tr>
            `;
            tabla.append(fila);
        });
    }
    
    function actualizarTablaStockBajo(productos) {
        const tabla = $('#tabla-stock-bajo tbody');
        tabla.empty();
        
        if (productos.length === 0) {
            tabla.html('<tr><td colspan="4" class="text-center text-muted">No hay productos con stock bajo</td></tr>');
            return;
        }
        
        productos.forEach(producto => {
            const fila = `
                <tr>
                    <td>${producto.nombre}</td>
                    <td><span class="badge bg-warning">${producto.existencia}</span></td>
                    <td>${producto.min_existencia}</td>
                    <td>
                        <a href="/compras/create?producto_id=${producto.id}" class="btn btn-sm btn-primary">
                            <i class="fas fa-cart-plus"></i>
                        </a>
                    </td>
                </tr>
            `;
            tabla.append(fila);
        });
    }
});
</script>
@endsection