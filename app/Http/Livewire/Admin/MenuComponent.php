<?php

namespace App\Http\Livewire\Admin;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class MenuComponent extends Component
{
  public string $state = "creating";
  public ?int $menuId = null;
  public bool $showSeeder = false;
  public string $seeder = '';

  //----------------------------------------------
  // Campos del formulario
  //----------------------------------------------
  public string $name = "";
  public string $url = "";
  public string $icon = "";

  public function rules()
  {
    return [
      'name' => 'required|string|max:50',
      'icon' => 'nullable|string|max:50',
      'url' => ['required', 'string', 'max:100', function ($attribute, $value, $fail) {
        if ($value !== '#') {
          $menu = Menu::where('url', $value)
            ->where('id', '!=', $this->menuId)
            ->first(['id', 'name']);
          if ($menu) {
            $name = $menu->name;
            $fail("Url asignada al menu \"$name\"");
          } //.end fi
        } //.end if
      }],
    ];
  }

  protected $validationAttributes = [
    'name' => 'Nombre',
    'icon' => 'Icono',
  ];


  public function render()
  {
    // dd($this->createSeeder());
    // dd($this->getData());
    return view('livewire.admin.menu-component', $this->getData())
      ->layout('layouts.admin-layout', $this->layoutData);;
  }

  /**
   * Este metodo se encarga de servir las variables que requiere el
   * layout de administración
   * @return array
   */
  public function getLayoutDataProperty()
  {
    $data = [
      'title' => 'Menus',
      'contentTitle' => "Administracion de Menus",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Menus' => route('admin.menus'),
      ],
    ];

    return $data;
  }

  protected function getData()
  {
    $menus = Menu::getMenus();
    $roles = [];

    //Recupero los roles de la base de datos
    $rolesData = Role::orderBy('id')
      ->with(['menus' => function ($query) {
        $query->orderBy('id')->select('id');
      }])
      ->get(['id', 'name']);

    foreach($rolesData as $role){
      $roleMenus = [];
      foreach($role->menus as $menu){
        $roleMenus[] = $menu->id;
      }

      $roles[] = [
        'id' => $role->id,
        'name' => $role->name,
        'menus' => $roleMenus
      ];
    }
    return [
      'menus' => $menus,
      'roles' => $roles
    ];
  }

  //-----------------------------------------------------------------
  // CRUD
  //-----------------------------------------------------------------
  public function submit()
  {
    $this->validate();
    try {
      if ($this->state === 'creating') {
        if (userHasPermission('create_menu')) {
          $this->store();
        } else {
          $this->doesNotPermission('registrar menús');
        }
      } else if ($this->state === 'editing') {
        if (userHasPermission('update_menu')) {
          $this->update();
        } else {
          $this->doesNotPermission('actualizar menús');
        }
      } else {
        $this->alert('¡Estado Invalido!', 'error');
      }
    } catch (\Throwable $th) {
      $this->emitError($th);
    }
  }

  protected function store()
  {
    DB::beginTransaction();

    //Se crea el menú
    /** @var Menu */
    $menu = Menu::create([
      'name' => $this->name,
      'icon' => empty($this->icon) ? null : $this->icon,
      'url' => $this->url,
    ]);

    //Se asigna el menú al super administrador
    DB::table('role_menu')->insert([
      'menu_id' => $menu->id,
      'role_id' => 1
    ]);

    $action = "Registrar Menú";
    $description = "Se registra el menú \"$menu->name\" ";
    $description .= "con ID:$menu->id ";
    $description .= "y url \"$menu->url\"";

    $this->registerUserLog($action, $description);

    DB::commit();
    $this->resetFields();
    $this->alert('Menú Registrado', 'success');
  }

  protected function update()
  {
    /**
     * Menú a modificar
     * @var Menu
     */
    $menu = Menu::find($this->menuId, ['id', 'name', 'url', 'icon']);

    if ($menu) {
      DB::beginTransaction();
      $menuOriginal = $menu->getOriginal();
      //se actualiza el menú
      $menu->name = $this->name;
      $menu->url = $this->url;
      $menu->icon = empty($this->icon) ? null : $this->icon;

      $menu->save();

      if ($menu->wasChanged()) {
        $action = "Actualizar menú";
        $description = '';
        if ($menu->wasChanged('name')) {
          $originalName = $menuOriginal['name'];
          $description = "Se cambió el nombre del menú de ";
          $description .= "\"$originalName\" por \"$menu->name\"";

          $this->registerUserLog($action, $description);
        }

        if ($menu->wasChanged('url')) {
          $originalUrl = $menuOriginal['url'];
          $description = "Se cambío la dirección Url \"$originalUrl\" ";
          $description .= "por la Url \"$menu->url\" ";
          $description .= "del menú \"$menu->name\"";

          $this->registerUserLog($action, $description);
        }

        if ($menu->wasChanged('icon')) {
          $originalIcon = $menuOriginal['icon'];

          if ($originalIcon && $menu->icon) {
            $description = "Se cambío el icono \"$originalIcon\" del menú \"$menu->name\" ";
            $description .= "por \"$menu->icon\"";
          } else if (empty($originalIcon) && $menu->icon) {
            $description = "Se agregó un iconó al menú \"$menu->name\"";
          } else if ($originalIcon && empty($menu->icon)) {
            $description = "Se retiró el icono al menú \"$menu->name\"";
          }

          $this->registerUserLog($action, $description);
        }
      }

      DB::commit();

      $this->alert('Menú Actualizado', 'info');
      $this->resetFields();
    } else {
      $this->alert('¡Menú no encontrado!', 'error');
    }
  }

  public function edit($id)
  {
    if (userHasPermission('update_menu')) {
      $menu = Menu::find($id, ['id', 'name', 'icon', 'url']);
      if ($menu) {
        $this->menuId = $menu->id;
        $this->name = $menu->name;
        $this->url = $menu->url;
        $this->icon = $menu->icon;
        $this->state = 'editing';
      } else {
        $this->alert('¡Menú no enocntrado!', 'error');
      }
    } else {
      $this->doesNotPermission('actalizar menús');
    }
  }

  public function destroy($id, $password)
  {
    if (userHasPermission('delete_menu')) {
      /**
       * Usuario que intenta eliminar el menú
       * @var User
       */
      $user = User::find(session()->get('userId'), ['password']);
      if (Hash::check($password, $user->password)) {
        /**
         * Menú a eliminar
         * @var Menu
         */
        $menu = Menu::find($id, ['id', 'name', 'url']);
        if ($menu) {
          $menuName = $menu->getOriginal('name');
          $menuUrl = $menu->getOriginal('url');
          try {
            DB::beginTransaction();
            //Se elimina el menú
            $menu->delete();

            //Se crea el registro de usuario
            $action = "Eliminar Menú";
            $description = "Se eliminó el menú \"$menuName\" con url:\"$menuUrl\"";
            $this->registerUserLog($action, $description);

            DB::commit();

            $this->alert('¡Menú Eliminado!', 'info');
            $this->resetFields();
          } catch (\Throwable $th) {
            $this->emitError($th);
          }
        } else {
          $this->alert('¡Menú no Encontrado!', 'error');
        }
      } else {
        $this->alert('¡Contraseña Incorrecta!', 'error');
      }
    } else {
      $this->doesNotPermission('eliminar menús');
    }
  }

  /**
   * Se encarga de actualizar el orden de los menús
   * en la base de datos
   */
  public function saveOrder($menus)
  {
    $isOk = false;
    if (userHasPermission('order_menu')) {
      try {
        DB::beginTransaction();
        //Se crea una instancia de menu
        $menu = new Menu();
        $menu->saveOrder($menus);

        $action = "Reordenar Menús";
        $description = "Se reorganizarón los menús";

        $this->registerUserLog($action, $description);

        DB::commit();
        $this->alert('¡Nueva distribución Guardada!', 'success');
        $this->resetFields();
        $isOk = true;
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('reordernar menús');
      $isOk = true;
    }

    $this->emit('orderSaved', $isOk);
  }

  public function changeState(bool $check, int $roleId, int $menuId){
    //Se verfica que el usuario puede hacer esta tarea
    if(userHasPermission('assign_menu')){
      try {
        /**
         * Rol a modificar
         * @var Role
         */
        $role = Role::find($roleId, ['id', 'name']);

        /**
         * Menú a asignar o retirar
         * @var Menu
         */
        $menu = Menu::find($menuId, ['id', 'name']);

        if($role && $menu){
          $userAction = '';
          $userDescription = '';
          $alertTitle = "";
          $alerType = 'info';

          DB::beginTransaction();
          if($check){
            $userAction = "Asignar Menú a Rol";
            $role->menus()->attach($menu->id);

            $userDescription = "Se asignó el menú \"$menu->name\" ";
            $userDescription .= "al rol \"$role->name\"";

            $alertTitle = "Se asignó el menú correctamente";
          }else{
            $userAction = "Retirar Menú de Rol";
            $role->menus()->detach($menu->id);
            
            $userDescription = "Se retiró el menú \"$menu->name\" ";
            $userDescription .= "del rol \"$role->name\"";

            $alertTitle = "Menú retirado correctamente";
            $alerType = 'warning';
          }

          $this->registerUserLog($userAction, $userDescription);
          DB::commit();

          $this->alert($alertTitle, $alerType);
        }else{
          $this->alert('Menú o Rol no encontrado', 'info');
        }
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    }else{
      $this->doesNotPermission('asignar o retirar menús a los roles');
    }
  }

  //----------------------------------------------------------------
  // Utilidades
  //----------------------------------------------------------------
  /**
   * Se encarga de notificar al usuario
   * que ha habido un error en el servidor
   */
  protected function emitError($th)
  {
    $title = '¡Ups, Algo salió mal!';
    $message = "Contacte con el administrador!";
    $type = 'error';
    $this->alert($title, $message, $type);
    if (env('APP_DEBUG')) {
      throw $th;
    }
  }

  /**
   * Se encarga de emitir una alerta al 
   * navegador y que se muestre por toastr
   */
  protected function alert(?string $title = null, ?string $type = 'warning', ?string $message = null)
  {
    $this->emit('alert', $title, $message, $type);
  }

  /**
   * Se encarga de notificarle al usuarios
   * que no tiene el permiso para realizar
   * la accion
   * @param string|null $action Accion que no tiene permitido
   */
  protected function doesNotPermission(string $action)
  {
    $title = "¡Acción denegada!";
    $message = "No tiene el permiso para $action";
    $type = 'error';
    $this->alert($title, $type, $message);
  }

  public function resetFields()
  {
    $this->reset('state', 'menuId', 'name', 'url', 'icon');
  }

  /**
   * Se encarga de guardar un registro de 
   * actividad del usuario actual de la sesión
   * @param string $action Actividad realizada por el usuario
   * @param string $description Información adicional con respecto a la acción
   */
  protected function registerUserLog(string $action, string $description)
  {
    //Recupero el usuario
    /** @var User */
    $user = User::find(session()->get('userId'), ['id', 'name']);
    $user->registerLog($action, $description);
  }

  public function createSeeder()
  {
    $menus = Menu::getMenus();
    $result = '$menus = [' . "\n";

    foreach($menus as $menu){
      $item = $this->writeMenu($menu);
      $result .= "$item";
    }

    $result .= ']';

    $this->showSeeder = true;
    $this->seeder = $result;
  }

  protected function writeMenu($menu, $level=1)
  {
    $row = "";
    for($i = 0; $i < $level; $i++){
      $row .= "  ";
    }

    $fatherId = empty($menu['father_id']) ? 'null' : $menu['father_id'];

    $row .= '["id" => ' . $menu['id'] . ', ';
    $row .= '"fatherId" => ' . $fatherId . ', ';
    $row .= '"name" => ' . '"' . $menu['name'] . '", ';
    $row .= '"url" => ' . '"' . $menu['url'] . '", ';
    $row .= '"icon" => ' . '"' . $menu['icon'] . '", ';
    $row .= '"order" => ' . $menu['order'] . ', ';
    $row .= '],';
    $row .= "\n";

    if($menu['submenus']){
      foreach( $menu['submenus'] as $submenu){
        $item = $this->writeMenu($submenu, $level + 1);
        $row .= "$item";
      }
    }

    return $row;
  }
}
