<?php

namespace App\Providers;

use App\Models\Menu;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    /**
     * Con este codigo de proveeo los menus al sidebar 
     */
    View::composer('layouts.admin.sidebar', function ($view) {
      $menus = Menu::getMenus(true);
      $view->with('menusComposer', $menus);
    });
  }
}
