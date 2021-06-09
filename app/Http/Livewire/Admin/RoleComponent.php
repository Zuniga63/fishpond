<?php

namespace App\Http\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RoleComponent extends Component
{
  public string $state = 'creating';
  public ?int $roleId = null;

  public function getProtectedRolesProperty()
  {
    return [1, 2];
  }

  //------------------------------------------------------------
  // Campos del formulario
  //------------------------------------------------------------
  public string $name = '';
  public string $slug = '';
  public string $description = '';

  public function rules()
  {
    return [
      'name' => 'required|string|min:3|max:50|unique:role,name,' . $this->roleId,
      'slug' => 'required|string|min:3|max:50|unique:role,slug,' . $this->roleId,
      'description' => 'nullable|string|max:255'
    ];
  }

  protected $validationAttributes = [
    'name' => 'Nombre',
    'description' => 'Descripción',
  ];

  //------------------------------------------------------------
  // Metodos de renderización
  //------------------------------------------------------------

  public function render()
  {
    // dd($this->renderData());
    return view('livewire.admin.role-component', $this->renderData())->layout('layouts.admin-layout', $this->layoutData);
  }

  protected function renderData()
  {
    $roles = [];
    $users = [];

    //Recupero los roles
    $data = Role::orderBy('id')->get(['id', 'name', 'slug']);
    foreach ($data as $record) {
      $permissions = $record->permissions()->count();
      $roles[] = [
        'id' => $record->id,
        'name' => $record->name,
        'slug' => $record->slug,
        'permissions' => $permissions
      ];
    }

    //Recupero los usuarios
    $userData = User::orderBy('name')
      ->with(['roles' => function ($query) {
        $query->orderBy('id')->select('id');
      }])
      ->get(['id', 'name']);

    // dd($data);
    foreach ($userData as $user) {
      $userRoles = [];
      foreach ($user->roles as $role) {
        if ($role['pivot']['state'] === 1) {
          $userRoles[] = $role['id'];
        }
      }

      $users[] = [
        'id' => $user->id,
        'name' => $user->name,
        'roles' => $userRoles
      ];
    }

    return [
      'roles' => $roles,
      'users' => $users,
    ];
  }

  /**
   * Este metodo se encarga de servir las variables que requiere el
   * layout de administración
   * @return array
   */
  public function getLayoutDataProperty()
  {
    $data = [
      'title' => 'Roles',
      'contentTitle' => "Administrar Roles",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Roles' => route('admin.roles'),
      ],
    ];

    return $data;
  }

  //------------------------------------------------------------
  // CRUD
  //------------------------------------------------------------
  /**
   * Se encarga de guardar el nuevo rol en
   * la base de datos
   */
  protected function store()
  {
    $name = $this->name;
    $slug = str_replace(' ', '_', strtolower($this->slug));
    $description = empty($this->description) ? null : $this->description;

    //Recupero al usuario que realiza el registro
    /** @var User */
    $user = User::find(session()->get('userId'));

    DB::beginTransaction();

    //Se registra el rol
    $role = Role::create([
      'name' => $name,
      'slug' => $slug,
      'description' => $description,
    ]);

    //Se hace el resgistro de usuario
    $logAction = "Registrar Rol";
    $logDescription = "Se registró el rol \"$name\" con ID:$role->id";

    $user->registerLog($logAction, $logDescription);

    $this->alert('Rol Registrado', 'success');
    $this->resetFields();
    DB::commit();
  }

  protected function update()
  {
    $name = $this->name;
    $slug = str_replace(' ', '_', strtolower($this->slug));
    $description = empty($this->description) ? null : $this->description;

    $originalData = null;

    //Recupero el usuario que realiza la actualización
    /** @var User */
    $user = User::find(session()->get('userId'));

    //Recupero el rol a actualizar
    /** @var Role */
    $role = Role::find($this->roleId);

    if ($role) {
      $originalData = $role->getOriginal();
      DB::beginTransaction();

      //Se actualizan los datos del rol
      $role->name = $name;
      $role->slug = $slug;
      $role->description = $description;
      $role->save();

      $logAction = "Actualizar Rol";
      $logDescription = "";

      if ($role->wasChanged('name')) {
        $original = $originalData['name'];
        $logDescription = "Se actualizó el nombre del rol con id:$role->id de \"$original\" por \"$name\"";
        $user->registerLog($logAction, $logDescription);
      }

      if ($role->wasChanged('slug')) {
        $original = $originalData['slug'];
        $logDescription = "Se actualizó la clave del rol con id:$role->id de \"$original\" por \"$slug\"";
        $user->registerLog($logAction, $logDescription);
      }

      if ($role->wasChanged('description')) {
        $original = $originalData['description'];
        $logDescription = "Se actualizó la descripción del rol con ID:$role->id";
        $user->registerLog($logAction, $logDescription);
      }

      DB::commit();

      $this->alert('¡Datos Actualizados!', 'success');
      $this->resetFields();
    } else {
      $this->alert('Rol no encontrado', 'error');
    }
  }

  public function submit()
  {
    $this->validate();
    try {
      if ($this->state === 'creating') {
        if (userHasPermission('create_role')) {
          $this->store();
        } else {
          $this->doesNotPermission('registrar roles');
        }
      } else if ($this->state === 'editing') {
        if (userHasPermission('update_role')) {
          $this->update();
        } else {
          $this->doesNotPermission('actualizar roles');
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
    if (userHasPermission('update_role')) {
      $role = Role::find($id);
      if ($role) {
        $this->roleId = $role->id;
        $this->name = $role->name;
        $this->slug = $role->slug;
        $this->description = $role->description ? $role->description : '';
        $this->state = "editing";
      } else {
        $this->alert('¡Rol no encontrado!', 'error');
      }
    } else {
      $this->doesNotPermission('actualizar roles');
    }
  }

  public function destroy($id, $password)
  {
    if (userHasPermission('delete_role')) {
      //Se verifica que no sea un rol protegido
      if (!in_array($id, $this->protectedRoles, true)) {
        //recupero el usuario que va a eliminar el rol
        /** @var User */
        $user = User::find(session()->get('userId'));

        //Recupero el rol que va a ser eliminado
        /** @var Role */
        $role = Role::find($id);

        if ($role) {
          if (Hash::check($password, $user->password)) {
            $originalData = $role->getOriginal();

            DB::beginTransaction();

            //Se elimina el permiso
            $role->delete();

            //Se crea el registro de usuario
            $logAction = "Eliminar Rol";
            $logDescription = "Se eliminó el rol con id:" . $originalData['id'];
            $logDescription .= " y nombre \"" . $originalData['name'] . '"';

            $user->registerLog($logAction, $logDescription);

            DB::commit();

            $this->alert('Rol Eliminado', 'success');
            $this->resetFields();
          } else {
            $this->alert('¡Contraseña incorrecta!', 'error');
          }
        } else {
          $this->alert('¡Rol no encontrado!');
        }
      } else {
        $this->alert('Este Rol no se puede eliminar', 'error');
      }
    } else {
      $this->doesNotPermission('eliminar roles');
    }
  }

  public function changeState(bool $check, int $roleId, int $userId)
  {
    $canUpdate = false;
    $adminUser = null;
    $adminMainRole = session()->get('mainRoleId');
    $user = null;
    $role = null;


    if (userHasPermission('assing_role')) {
      //Recupero al usuario que hace la actalización
      /** @var User|null */
      $adminUser = User::find(session()->get('userId'));

      //Recupero al usuario que se va a actualizar
      /** @var User */
      $user = User::find($userId);

      //Recupero el rol implicado
      /** @var Role */
      $role = Role::find($roleId);

      if ($user && $role) {
        //Se valida si se puede actualizar el estado
        if ($roleId >= $adminMainRole) {
          //Se hace la validación de super administrador
          if ($roleId === 1 && !$check) {
            $users = $role->users()->count();
            if ($users > 1) {
              $canUpdate = true;
            }
          } else {
            $canUpdate = true;
          }
        } else {
          $message = "No puede cambiar el rol de este usuario";
          $this->alert('¡Acción Denegada!', 'error', $message);
        }

        if ($canUpdate) {
          try {
            DB::beginTransaction();

            //Se verifica si el usuario ya tiene el rol
            if ($user->roles()->where('id', $role->id)->exists()) {
              //Se actualiza el estado
              $user->roles()->updateExistingPivot($role->id, ['state' => $check]);
            } else {
              //Se crea la relación
              $user->roles()->attach($role->id, ['state' => $check]);
            }

            //Se crean los correspondientes registros de usuarios
            if ($check) {
              if ($user->id === $adminUser->id) {
                $action = "Asignar Rol";
                $message = "Te asignaste el rol de \"$role->name\"";
                $adminUser->registerLog($action, $message);
              } else {
                $action = "Asignar Rol";
                $message = "Se asignó el rol de \"$role->name\" al usuarios \"$user->name\" con ID:$user->id";
                $adminUser->registerLog($action, $message);

                $action = "Asignación de Rol";
                $message = "El usuario \"$adminUser->name\" te asignó el rol de \"$role->name\"";
                $user->registerLog($action, $message);
              }
            } else {
              if ($user->id === $adminUser->id) {
                $action = "Retirar Rol";
                $message = "Te retiraste el rol de \"$role->name\"";
                $adminUser->registerLog($action, $message);
              } else {
                $action = "Retirar Rol";
                $message = "Se retiró el rol de \"$role->name\" al usuarios \"$user->name\" con ID:$user->id";
                $adminUser->registerLog($action, $message);

                $action = "Asignación de Rol";
                $message = "El usuario \"$adminUser->name\" te retiró el rol de \"$role->name\"";
                $user->registerLog($action, $message);
              }
            }

            DB::commit();

            $alertTitle = $check ? "¡Rol Asignado con exito!" : '¡Rol retirado con exito!';
            $this->alert($alertTitle, 'success');
          } catch (\Throwable $th) {
            $this->emitError($th);
          }
        } else {
          $this->alert('¡Acción Denegada!', 'error');
          $check = !$check;
        }
      } else {
        $this->alert("¡Usuario o rol no encontrado!", 'error');
      }
    } else {
      $this->doesNotPermission('asignar o retirar roles');
    }

    return $check;
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
    $this->reset('state', 'roleId', 'name', 'slug', 'description');
  }
}
