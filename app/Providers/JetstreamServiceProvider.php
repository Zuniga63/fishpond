<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
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
    $this->configurePermissions();

    Jetstream::deleteUsersUsing(DeleteUser::class);

    /**
     * Metodo personalizado para la autenticacion de usuario
     */
    Fortify::authenticateUsing(function (Request $request) {
      /** @var USer */
      $user = User::where('email', $request->email)->first();
      if ($user && Hash::check($request->password, $user->password)) {
        $roles = $user->roles()->where('state', 1)->get();

        if ($roles && $roles->isNotEmpty()) {
          $permissions = [];

          //Se recuperan todos los permisos
          foreach ($roles as $role) {
            $list = $role->permissions()->pluck('action')->toArray();
            $permissions = array_merge($permissions, $list);
          }

          //Se eliminan duplicados
          $permissions = array_unique($permissions);

          $user->setSession($roles->toArray(), $permissions);
          return $user;
        }
      } //.end if
    });
  }

  /**
   * Configure the permissions that are available within the application.
   *
   * @return void
   */
  protected function configurePermissions()
  {
    Jetstream::defaultApiTokenPermissions(['read']);

    Jetstream::permissions([
      'create',
      'read',
      'update',
      'delete',
    ]);
  }
}
