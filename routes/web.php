<?php

use App\Http\Livewire\Admin\FishpondComponent;
use App\Http\Livewire\Admin\MenuComponent;
use App\Http\Livewire\Admin\PermissionComponent;
use App\Http\Livewire\Admin\RoleComponent;
use App\Http\Livewire\Admin\UsersComponent;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//   return view('welcome');
// });

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//   return view('dashboard');
// })->name('dashboard');

Route::redirect('/', 'admin');

/**
|-------------------------------------------------------------------------------
| Rutas del panel de administracion 
|
| Aquí se encuentran todas las rutas que se encargan de 
| administrar los aspectos de la aplicacion a la cual solo tienen acceso los 
| roles que se habiliten en el middleware AdminPanelPermission
|
 */
Route::middleware(['auth:sanctum', 'verified', 'adminPanel'])
  ->name('admin.')
  ->prefix('admin')
  ->group(function () {
    Route::view('/', 'layouts.admin.layout')->name('dashboard');
    Route::get('/estanques', FishpondComponent::class)->name('fishpond');

    /**
     ******************************************
     * Rutas del panel de administración
     * 
     * Las rutas listadas aquí solo son accesibles por
     * los administradores del sistema
     */
    Route::middleware('admin')->group(function () {
      Route::get('/usuarios/{id?}', UsersComponent::class)->name('users')->whereNumber('id');
      Route::get('/permisos', PermissionComponent::class)->name('permissions');
      Route::get('/roles', RoleComponent::class)->name('roles');
      Route::get('/menus', MenuComponent::class)->name('menus');
    }); //.end group of admin
  });//.end admin routes
