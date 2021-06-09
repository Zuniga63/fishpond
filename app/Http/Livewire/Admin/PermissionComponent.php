<?php

namespace App\Http\Livewire\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PermissionComponent extends Component
{
  /** Guarda el estado de la app */
  public string $state = 'creating';
  public ?int $permissionId = null;


  //--------------------------------------
  // Propiedades del formaulario
  //--------------------------------------
  /** Nombre del permiso */
  public string $name = '';
  /** Acción del permiso en forma de slug y en ingles */
  public string $action = '';
  /** Ubicación dentro de la tabla */
  public int $order = 0;

  protected function rules()
  {
    $rules = [
      'name' => 'required|string|min:3|max:50|unique:permission,name,' . $this->permissionId,
      'action' => 'required|string|min:3|max:50|unique:permission,action,' . $this->permissionId,
      'order' => 'required|numeric|min:0|max:' . $this->getMaxOrder(),
    ];

    return $rules;
  }

  protected $validationAttributes = [
    'name' => 'Nombre',
    'action' => 'Acción',
    'order' => 'Orden',
  ];

  //----------------------------------------------------
  // Metodos de Rederización 
  //----------------------------------------------------
  public function render()
  {
    // dd($this->data);
    return view('livewire.admin.permission-component', $this->data)->layout('layouts.admin-layout', $this->layoutData);;
  }

  /**
   * Este metodo se encarga de servir las variables que requiere el
   * layout de administración
   * @return array
   */
  public function getLayoutDataProperty()
  {
    $data = [
      'title' => 'Permisos',
      'contentTitle' => "Administracion de Permisos",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Permisos' => route('admin.users'),
      ],
    ];

    return $data;
  }

  public function mount()
  {
    $this->order = $this->getMaxOrder();
  }

  public function getDataProperty()
  {
    $roles = [];
    $roleData = Role::orderBy('id')
      ->where('id', '>=', session()->get('mainRoleId'))
      ->with(['permissions' => function ($query) {
        $query->orderBy('id')->select('id');
      }])
      ->get(['id', 'name']);


    foreach ($roleData as $record) {
      $permissions = [];
      foreach ($record->permissions as $permission) {
        $permissions[] = $permission['id'];
      }
      $roles[] = [
        'id' => $record->id,
        'name' => $record->name,
        'permissions' => $permissions
      ];
    }

    //Recupero los permisos
    $permissions = Permission::orderBy('order')
      ->get(['id', 'name', 'action', 'order']);

    return [
      'roles' => $roles,
      'permissions' => $permissions,
    ];
  }

  //-------------------------------
  // Utilidades
  //-------------------------------

  public function getMaxOrder()
  {
    $count = Permission::count();

    if ($this->state === 'creating') {
      return $count + 1;
    }

    return $count;
  }

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
    $this->reset('state', 'permissionId', 'name', 'action', 'order');

    $this->order = $this->getMaxOrder();
  }

  /**
   * Se encarga de poner el texto en minusculas
   * y cambiar los espacios en '_'
   */
  protected function formatAction($text)
  {
    $result = strtolower($text);
    $result = str_replace(' ', '_', $result);

    return $result;
  }
  //----------------------------------------
  // CRUD
  //----------------------------------------
  protected function store()
  {
    $name = $this->name;
    $action = $this->formatAction($this->action);
    $order = $this->order;
    $increment = false;

    //Recuero al usuario que realiza el registro
    /** @var User */
    $user = User::find(session()->get('userId'));

    DB::beginTransaction();
    //Se actualiza el orden de los demas permisos
    if ($order <= $this->getMaxOrder()) {
      Permission::where('order', '>=', $order)
        ->increment('order');

      $increment = true;
    }

    //Se registra el permiso
    $permission = Permission::create([
      'name' => $name,
      'action' => $action,
      'order' => $order
    ]);

    //Se asigna el permiso al superadmin
    /** @var Role */
    $superAdmin = Role::find(1);
    if ($superAdmin) {
      $superAdmin->permissions()->attach($permission->id);
    }

    //Se hace el resgistro de usuario
    $logAction = "Registrar Permiso";
    $logDescription = "Se registró el permiso \"$name\" con las acción \"$action\" en la posición \"$order\"";
    if ($increment) {
      $logDescription .= " incrementado el orden de los demás permisos";
    }

    $user->registerLog($logAction, $logDescription);

    $this->alert('Permiso Registrado', 'success');
    $this->resetFields();
    DB::commit();
  }

  protected function update()
  {
    $name = $this->name;
    $action = $this->formatAction($this->action);
    $order = $this->order;

    $originalData = null;

    //Recupero el usuario que realiza la actualización
    /** @var User */
    $user = User::find(session()->get('userId'));

    //Recupero el permiso a actualizar
    /** @var Permission */
    $permission = Permission::find($this->permissionId);

    if ($permission) {
      $originalData = $permission->getOriginal();
      DB::beginTransaction();

      //Se actualizan los datos del permiso
      $permission->name = $name;
      $permission->action = $action;

      //Se verifica que haya que actualizar el orden
      if ($permission->order !== $order) {
        //Se decrementa el orden de los permisos posteriores
        Permission::where('order', '>', $permission->order)
          ->decrement('order');

        /**
         * Se incrementa el orden de los permisos a partir
         * de la nueva posicion
         */
        Permission::where('order', '>=', $order)->increment('order');

        //Se actualiza el orden del permiso
        $permission->order = $order;
      }

      $permission->save();

      $logAction = "Actualizar Permiso";
      $logDescription = "";

      if ($permission->wasChanged('name')) {
        $original = $originalData['name'];
        $logDescription = "Se actualizó el nombre del permiso con id:$permission->id de \"$original\" a \"$name\"";
        $user->registerLog($logAction, $logDescription);
      }

      if ($permission->wasChanged('action')) {
        $original = $originalData['action'];
        $logDescription = "Se actualizó la clave del permiso con id:$permission->id de \"$original\" a \"$action\"";
        $user->registerLog($logAction, $logDescription);
      }

      if ($permission->wasChanged('order')) {
        $original = $originalData['order'];
        $logDescription = "Se movió el permiso con id:$permission->id ";
        $logDescription .= "de la posición \"$original\" ";
        $logDescription .= "a la posición \"$order\"";
        $user->registerLog($logAction, $logDescription);
      }

      DB::commit();

      $this->alert('¡Datos Actualizados!', 'success');
      $this->resetFields();
    } else {
      $this->alert('Permiso no encontrado', 'error');
    }
  }

  public function submit()
  {
    $this->validate();
    try {
      if ($this->state === 'creating') {
        if (userHasPermission('create_permission')) {
          $this->store();
        } else {
          $this->doesNotPermission('registrar permisos');
        }
      } else if ($this->state === 'editing') {
        if (userHasPermission('update_permission')) {
          $this->update();
        } else {
          $this->doesNotPermission('actualizar permisos');
        }
      } else {
        $this->alert('¡Estado Invalido!', 'error');
      }
    } catch (\Throwable $th) {
      $this->emitError($th);
    }
  }

  public function edit($id)
  {
    if (userHasPermission('update_permission')) {
      $permission = Permission::find($id);
      if ($permission) {
        $this->permissionId = $permission->id;
        $this->name = $permission->name;
        $this->action = $permission->action;
        $this->order = $permission->order;
        $this->state = "editing";
      }
    } else {
      $this->doesNotPermission('actualizar permisos');
    }
  }

  public function destroy($id, $password)
  {
    if (userHasPermission('delete_permission')) {
      //recupero el usuario que va a eliminar el permiso
      /** @var User */
      $user = User::find(session()->get('userId'));
      /** @var Permission */
      $permission = Permission::find($id);

      if ($permission) {
        if (Hash::check($password, $user->password)) {
          $originalData = $permission->getOriginal();

          DB::beginTransaction();

          //Se disminuye el orden de los permisos subsecuentes
          Permission::where('order', '>', $permission->order)->decrement('order');

          //Se elimina el permiso
          $permission->delete();

          //Se crea el registro de usuario
          $logAction = "Eliminar Permiso";
          $logDescription = "Se eliminó el permiso con id:" . $originalData['id'];
          $logDescription .= " con nombre \"" . $originalData['name'] . '"';

          $user->registerLog($logAction, $logDescription);

          DB::commit();

          $this->alert('Permiso Eliminado', 'success');
          $this->resetFields();
        } else {
          $this->alert('¡Contraseña incorrecta!', 'error');
        }
      } else {
        $this->alert('¡Permiso no encontrado!');
      }
    } else {
      $this->doesNotPermission('eliminar permisos');
    }
  }

  public function changeState(bool $check, int $roleId, int $permissionId)
  {
    if(userHasPermission('assing_permission')){
      try {
        /** @var Role */
        $role = Role::find($roleId, ['id', 'name']);

        /** @var Permission */
        $permission = Permission::find($permissionId, ['id', 'name']);

        if($role && $permission){
          $title = null;
          $type = 'info';
          $message = null;

          if($check){
            $role->permissions()->attach($permissionId);
            $title = "Permiso Asignado";
            $message = "Se ha asignado el permiso \"$permission->name\" al rol \"$role->name\"";
            $type = "success";
          }else{
            $role->permissions()->detach($permissionId);
            $title = "Permiso Retirado";
            $type = 'warning';
            $message = "Se ha retirado el permiso \"$permission->name\" del rol \"$role->name\"";
          }

          $this->alert($title, $type, $message);
        }else{
          $this->alert('Rol o Permiso no encontrado', 'error');
        }
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    }else{
      $this->doesNotPermission('asignar permiso a roles');
    }
  }

  public function writeSeeder()
  {
    $permissions = Permission::orderBy('order')->get(['id', 'name', 'action', 'order']);
    $permissionsSeeder = "";
    $permissionsRolesSeeder = "";

    foreach($permissions as $permission){
      $permissionsSeeder .= '["name" => "' . $permission->name . '", ';
      $permissionsSeeder .= '"action" => "' . $permission->action . '", ';
      $permissionsSeeder .= '"order" => ' . $permission->order . '],';
      $permissionsSeeder .= "\n";
    }

    $this->emit('showSeeders', $permissionsSeeder, $permissionsRolesSeeder);
  }
}
