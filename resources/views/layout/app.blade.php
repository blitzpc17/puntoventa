<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Papelería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Estilos adicionales para temas (opcional) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" />

    @stack('css')
    

    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-bg: #f8f9fc;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Nunito', sans-serif;
            overflow-x: hidden;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            color: white;
            transition: transform 0.3s ease;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
        }
        
        .sidebar .nav-link.active {
            font-weight: bold;
            color: #fff;
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .page-title {
            color: var(--secondary-color);
            font-weight: 700;
        }
        
        /* Estilos para el botón hamburguesa */
        .navbar-toggler {
            border: none;
            font-size: 1.25rem;
            background: transparent;
            color: var(--primary-color);
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
            outline: none;
        }
        
        /* Overlay para móviles */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
        }
        
        /* Ajustes responsivos */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 280px;
                z-index: 1050;
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                width: 100%;
            }
            
            .mobile-header {
                display: flex;
                align-items: center;
                padding: 1rem;
                background-color: white;
                box-shadow: 0 2px 4px rgba(0,0,0,.1);
                margin-bottom: 1rem;
            }
        }
        
        @media (min-width: 768px) {
            .mobile-header {
                display: none;
            }
            
            .sidebar-overlay {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay para móviles -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar px-0" id="sidebar">
                <div class="text-center p-4">
                    <h4>Punto de venta</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
                            <i class="fas fa-fw fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('clientes*') ? 'active' : '' }}" href="/clientes">
                            <i class="fas fa-fw fa-users me-2"></i>
                            Clientes
                        </a>
                    </li>
                      <li class="nav-item">
                        <a class="nav-link {{ Request::is('proveedores*') ? 'active' : '' }}" href="/proveedores">
                            <i class="fas fa-fw fa-bag-shopping me-2"></i>
                            Proveedores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('productos*') ? 'active' : '' }}" href="/productos">
                            <i class="fas fa-fw fa-box me-2"></i>
                            Productos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('ventas*') ? 'active' : '' }}" href="/ventas">
                            <i class="fas fa-fw fa-shopping-cart me-2"></i>
                            Ventas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('compras*') ? 'active' : '' }}" href="/compras">
                            <i class="fas fa-fw fa-truck me-2"></i>
                            Compras
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('fiados*') ? 'active' : '' }}" href="/fiados">
                            <i class="fas fa-fw fa-credit-card me-2"></i>
                            Fiados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('corte-caja*') ? 'active' : '' }}" href="/corte-caja">
                            <i class="fas fa-fw fa-calculator me-2"></i>
                            Corte de Caja
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-4 py-4 main-content">
                <!-- Header móvil con botón hamburguesa -->
                <div class="mobile-header">
                    <button class="navbar-toggler me-3" type="button" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h4 class="mb-0">Punto de venta</h4>
                </div>
                
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 

    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Funcionalidad para el menú hamburguesa
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const sidebarToggle = $('#sidebarToggle');
            const sidebarOverlay = $('#sidebarOverlay');
            
            // Alternar menú al hacer clic en el botón hamburguesa
            sidebarToggle.on('click', function() {
                sidebar.toggleClass('show');
                sidebarOverlay.fadeToggle(200);
            });
            
            // Cerrar menú al hacer clic en el overlay
            sidebarOverlay.on('click', function() {
                sidebar.removeClass('show');
                sidebarOverlay.fadeOut(200);
            });
            
            // Cerrar menú al hacer clic en un enlace (solo en móviles)
            if ($(window).width() < 768) {
                sidebar.on('click', 'a.nav-link', function() {
                    sidebar.removeClass('show');
                    sidebarOverlay.fadeOut(200);
                });
            }
            
            // Ajustar comportamiento al cambiar el tamaño de la ventana
            $(window).on('resize', function() {
                if ($(window).width() >= 768) {
                    sidebar.removeClass('show');
                    sidebarOverlay.hide();
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>