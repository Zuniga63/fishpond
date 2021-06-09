<?php
/**
 * Esta es una funcion global para poder definir si
 * un mnú esta en la ruta de su href. Esto se registra en
 * el archivo composer.json y despues
 * ejecutar composer dump-autoload
 */
if(!function_exists('getMenuActive')){
  function getMenuActive($url)
  {    
    $isActive = false;
    /**
     * Si la url de la petición es
     * igual a la url del menu se retorna active
     * en caso constrario se procede a hacer validacion
     * profunda
     */
    if(request()->is($url)){
      return 'active';
    }else if($url !== 'admin'){
      /**
       * Se particiona la uri de la peticion
       * que tiene formato /administración/algo/algo
       */
      $actualUrlExplode = explode('/', request()->getRequestUri());

      /**
       * Se particiona la uri del menu que por lo
       * general son solo dos niveles pero se le agrega 
       * el primer "/" ya que este no lo tiene por defecto
       */
      $urlExplode = explode('/', "/".$url);
      
      /**
       * La actual uri debe tener mas nodos
       * que la uri del menú, de lo contrario
       * es que existe algun tipo de error
       */
      if(count($actualUrlExplode) > count($urlExplode)){
        /**
         * Se recorren los nodos y se va validando
         * los nombres de cada uno. Si alguno es diferente
         * automaticamente se rompe el bucle.
         */
        for($index = 0; $index < count($urlExplode); $index++){
          if($urlExplode[$index] === $actualUrlExplode[$index]){
            $isActive = true;
          }else{
            $isActive = false;
            break;
          }
        }//.end for
      }//.end if
    }//.end if-else
    
    return $isActive ? 'active' : '';
  }
}