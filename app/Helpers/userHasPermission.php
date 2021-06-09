<?php
/**
 * Este metodo se encarga de verificar si un 
 * usuario tiene el permiso especificado
 * el archivo composer.json y despues
 * ejecutar composer dump-autoload
 */
if(!function_exists('userHasPermission')){
  /**
   * Se encarga de verificar si un usuario 
   * tiene el permiso solicitado
   */
  function userHasPermission(string $permission)
  {
    return in_array($permission, session()->get('permissions'));
  }
}