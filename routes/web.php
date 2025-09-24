<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\FiadoController;
use App\Http\Controllers\CorteCajaController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Productos
Route::get('productos/buscar', [ProductoController::class, 'buscar'])->name('productos.buscar');
Route::get('productos/barcode', [ProductoController::class, 'getByBarcode'])->name('productos.barcode');
Route::post('productos/{id}/stock', [ProductoController::class, 'updateStock'])->name('productos.stock.update');
Route::get('productos/importar', [ProductoController::class, 'previewView'])->name('productos.importar');
Route::get('productos/download-layout', [ProductoController::class, 'downloadLayout'])->name('productos.download.layout');
Route::post('productos/masivo/preview', [ProductoController::class, 'dataPreview'])->name('productos.import.preview');
Route::post('productos/masivo/upload', [ProductoController::class, 'uploadMasivo'])->name('productos.import.save');
Route::resource('productos', ProductoController::class);


// Proveedores
Route::get('proveedores/buscar', [ProveedorController::class, 'buscar'])->name('proveedores.buscar');
Route::get('proveedores/{id}/productos', [ProveedorController::class, 'getProductos'])->name('proveedores.productos');
Route::get('proveedores/{id}/compras', [ProveedorController::class, 'getCompras'])->name('proveedores.compras');
Route::resource('proveedores', ProveedorController::class);


// Clientes
Route::get('clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
Route::resource('clientes', ClienteController::class);

//categorias
Route::get('categorias-data', [CategoriaController::class, 'getCategorias'])->name('categorias.data');
Route::resource('categorias', CategoriaController::class);



// Rutas para AJAX
Route::prefix('ajax')->group(function () {
    // Productos
    Route::get('productos/buscar', [ProductoController::class, 'buscar'])->name('ajax.productos.buscar');
    Route::get('productos/barcode', [ProductoController::class, 'getByBarcode'])->name('ajax.productos.barcode');
    Route::post('productos/{id}/stock', [ProductoController::class, 'updateStock'])->name('ajax.productos.stock.update');
    
    // Clientes
    Route::get('clientes/{id}/fiados', [ClienteController::class, 'getFiados'])->name('ajax.clientes.fiados');
    
    // Fiados
    Route::get('fiados/cliente/{clienteId}', [FiadoController::class, 'getFiadosCliente'])->name('ajax.fiados.cliente');
    Route::get('fiados/{id}/abonos', [FiadoController::class, 'getAbonos'])->name('fiados.abonos');
    Route::post('fiados/abonar/{id}', [FiadoController::class, 'storeAbono'])->name('ajax.fiados.abonar');
    
    // Corte de Caja
    Route::get('corte-caja/abierto', [CorteCajaController::class, 'getCorteAbierto'])->name('ajax.corte-caja.abierto');
});

// Rutas principales
Route::get('ventas/{venta}/imprimir', [VentaController::class, 'imprimir'])->name('ventas.imprimir');
Route::resource('ventas', VentaController::class);


Route::resource('fiados', FiadoController::class);

Route::post('corte-caja/{corte}/cerrar', [CorteCajaController::class, 'cerrar'])->name('corte-caja.cerrar');
Route::resource('corte-caja', CorteCajaController::class);




// Ventas
Route::get('ventas/create', [VentaController::class, 'create'])->name('ventas.create');
Route::resource('ventas', VentaController::class);

/*
// Compras
Route::resource('compras', CompraController::class);

// Fiados
Route::resource('fiados', FiadoController::class);

// Corte de Caja
Route::get('corte-caja/generar', [CorteCajaController::class, 'generar'])->name('corte-caja.generar');
Route::resource('corte-caja', CorteCajaController::class);
*/

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');



