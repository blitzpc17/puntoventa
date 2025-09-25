<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PUNTO DE VENTA</title>
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
            padding-top: 70px; /* Para compensar el navbar fijo */
        }
        
        /* Navbar superior */
        .top-navbar {
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            padding: 0.5rem 1rem;
        }
        
        .navbar-brand {
            color: white;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.25rem 0.5rem;
        }
        
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* Dropdown de usuario */
        .user-dropdown .dropdown-toggle {
            color: white;
            background: transparent;
            border: none;
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
        }
        
        .user-dropdown .dropdown-toggle:after {
            margin-left: 0.5rem;
        }
        
        .user-dropdown .dropdown-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin-right: 0.5rem;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-right: 0.5rem;
        }
        
        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .user-role {
            font-size: 0.75rem;
            opacity: 0.8;
        }
        
        .sidebar {
            min-height: calc(100vh - 70px);
            background: linear-gradient(180deg, var(--primary-color) 10%, #224abe 100%);
            color: white;
            transition: transform 0.3s ease;
           /* margin-top: 70px;*/
            position: fixed;
            width: 250px;
            z-index: 1020;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            border-left: 4px solid transparent;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 4px solid rgba(255, 255, 255, 0.5);
        }
        
        .sidebar .nav-link.active {
            font-weight: bold;
            color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #fff;
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
        
        /* Overlay para móviles */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1010;
            display: none;
        }
        
        /* Ajustes responsivos */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                width: 100%;
                margin-left: 0;
            }
            
            .user-info {
                display: none;
            }
        }
        
        @media (min-width: 992px) {
            .main-content {
                margin-left: 250px;
            }
            
            .sidebar-overlay {
                display: none !important;
            }
            
            .navbar-toggler {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Overlay para móviles -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="/dashboard">
                <i class="fas fa-store me-2"></i>Punto de venta
            </a>
            
            <div class="d-flex align-items-center">
                <!-- Dropdown de usuario -->
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <!-- Avatar del usuario - puedes reemplazar con la imagen real del usuario -->
                        @if(/*auth()->user()->avatar*/true)
                            <img src="" alt="Avatar" class="user-avatar">
                        @else
                            <img src="https://ui-avatars.com/api/?name=perro&background=4e73df&color=ffffff&size=32" alt="Avatar" class="user-avatar">
                        @endif
                        <div class="user-info d-none d-lg-flex">
                            <span class="user-name">{{ /*auth()->user()->name*/ "Usuario prueba" }}</span>
                            <span class="user-role">{{ /*auth()->user()->role ?? 'Usuario'*/ "Admin" }}</span>
                        </div>
                    
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="#">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 sidebar px-0" id="sidebar">
                <div class="text-center p-4">
                    <h5>Menú Principal</h5>
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
                     <li class="nav-item">
                        <a class="nav-link {{ Request::is('corte-caja*') ? 'active' : '' }}" href="/corte-caja">
                            <i class="fas fa-fw fa-layer-group me-2"></i>
                            Categorías
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-lg-10 main-content px-4 py-4">
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
                $('body').toggleClass('overflow-hidden');
            });
            
            // Cerrar menú al hacer clic en el overlay
            sidebarOverlay.on('click', function() {
                sidebar.removeClass('show');
                sidebarOverlay.fadeOut(200);
                $('body').removeClass('overflow-hidden');
            });
            
            // Cerrar menú al hacer clic en un enlace (solo en móviles)
            if ($(window).width() < 992) {
                sidebar.on('click', 'a.nav-link', function() {
                    sidebar.removeClass('show');
                    sidebarOverlay.fadeOut(200);
                    $('body').removeClass('overflow-hidden');
                });
            }
            
            // Ajustar comportamiento al cambiar el tamaño de la ventana
            $(window).on('resize', function() {
                if ($(window).width() >= 992) {
                    sidebar.removeClass('show');
                    sidebarOverlay.hide();
                    $('body').removeClass('overflow-hidden');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>