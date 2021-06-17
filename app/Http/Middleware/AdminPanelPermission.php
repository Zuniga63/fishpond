<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminPanelPermission
{
  private $enabledRoles = [1,2,3];
  /**
   * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
  public function handle(Request $request, Closure $next)
  {
    if($this->hasPermission()){
      return $next($request);
    }

    return redirect(route('dashboard'));
  }

  private function hasPermission()
  {
    $mainRoleId = session()->get('mainRoleId');
    if($mainRoleId){
      return in_array($mainRoleId, $this->enabledRoles);
    }

    return false;
  }
}
