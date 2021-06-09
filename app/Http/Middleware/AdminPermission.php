<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPermission
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    if ($this->hasPermission()) {
      return $next($request);
    }

    return redirect(route('admin.dashboard'));
  }

  private function hasPermission()
  {
    $enabledRoles = [1,2];

    $mainRoleId = session()->get('mainRoleId');
    if ($mainRoleId) {
      return in_array($mainRoleId, $enabledRoles);
    }

    return false;
  }
}
