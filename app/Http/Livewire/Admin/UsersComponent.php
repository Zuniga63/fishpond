<?php

namespace App\Http\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UsersComponent extends Component
{
  public string $state = "creating";

  /*******************************************
   * QUERY BUILDER
   *******************************************/
  public ?string $user = null;
  protected $queryString = [
    'user' => ['except' => ''],
  ];

  /*******************************************
   * CAMPOS DEL FORMULARIO DE REGISTRO
   *******************************************/
  public ?int     $userId                 = null;
  public ?string  $name                   = '';
  public ?string  $profilePhoto           = '';
  public ?string  $email                  = '';
  public ?string  $password               = null;
  public ?string  $password_confirmation  = null;


  protected function rules()
  {
    $rules = [
      'name' => 'required|string|min:8|max:50',
      'email' => 'required|string|email|max:255|unique:user,email,' . $this->userId,
    ];

    if ($this->state === 'creating') {
      $rules['password'] = 'required|string|min:8|confirmed';
      $rules['password_confirmation'] = 'required|string|min:8';
    }

    return $rules;
  }

  protected $validationAttributes = [
    'name' => 'Nombre',
    'email' => 'Correo Electronico',
    'password' => 'Contraseña',
    'password_confirmation' => 'Confirmacion de contraseña'
  ];

  /*******************************************
   * REGISTROS DE USUARIOS
   *******************************************/
  public bool $showingUserLogs = false;
  public array $userLogs = [];

  public function updatedShowingUserLogs($value)
  {
    if(!$value){
      $this->name = '';
    }
  }
  /*******************************************
   * RENDERIZACIÓN
   *******************************************/

  public function render()
  {
    $data = $this->renderData();

    return view('livewire.admin.users-component', $data)
      ->layout('layouts.admin-layout', $this->layoutData);
  }

  public function mount($id = null)
  {
    if ($id) {
      /** @var User */
      $user = User::find($id, ['id', 'name', 'email', 'profile_photo_path']);

      if ($user) {
        $mainRole = $user->roles()->orderBy('id')->first();
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->profilePhoto = $user->getProfilePhotoUrlAttribute();
        $this->email = $user->email;

        $this->state = 'editing';
        $this->user = str_replace(' ', '-', $user->name);

        if ($mainRole) {
          $this->roleId = $mainRole->id;
        }
      } else {
        return redirect()->route('admin.users');
      }
    }
  }

  /**
   * Este metodo se encarga de servir las variables que requiere el
   * layout de administración
   * @return array
   */
  public function getLayoutDataProperty()
  {
    $data = [
      'title' => 'Usuarios',
      'contentTitle' => "Administracion de Usuarios",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Usuarios' => route('admin.users'),
      ],
    ];

    if ($this->userId) {
      $userName = User::find($this->userId, ['name'])->name;
      $data['breadcrumb'][$userName] = '';
    }

    return $data;
  }

  /**
   * Se encarga de recuperar los datos utilizados
   * por el componente
   */
  protected function renderData()
  {
    $mainRole = session()->get('mainRoleId');
    $roles = Role::where('id', '>=', $mainRole)->orderBy('id')->pluck('name', 'id');
    $users = User::orderBy('name')
      ->with(['roles' => function ($query) {
        $query->where('state', 1)
          ->orderBy('id')
          ->select('id', 'name');
      }])
      ->get(['id', 'name', 'email', 'email_verified_at']);

    return [
      'roles' => $roles,
      'users' => $users
    ];
  }

  //------------------------------------------------------
  // UTILIDADES
  //------------------------------------------------------
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
    $this->reset('state', 'user', 'userId', 'name', 'profilePhoto', 'email', 'password', 'password_confirmation');
  }

  /*******************************************
   * CRUD
   *******************************************/

  public function submit()
  {
    if (userHasPermission('create_user')) {
      $this->validate();
      try {
        switch ($this->state) {
          case 'creating':
            $this->store();
            break;
          case 'editing':
            $this->update();
            break;
          default:
            $this->alert('¡Acción del componente invalida!', 'error');
        }
      } catch (\Throwable $th) {
        $this->emitError($th);
      } //.end try-catch
    } else {
      $this->doesNotPermission('crear nuevo usuario');
    }
  }

  /**
   * Se encarga de almacenar los datos del usuario
   * en la base de datos
   */
  protected function store()
  {
    $title = null;
    $type = 'warning';
    $description = null;

    if (userHasPermission('create_user')) {
      DB::beginTransaction();

      //Recupero la instancia del usuario actual
      /** @var User */
      $user = User::find(session()->get('userId'));

      if ($user) {
        //Se registra el nuevo usuario en la base de datos
        /** @var User */
        $newUser = User::create([
          'name' => $this->name,
          'email' => $this->email,
          'password' => Hash::make($this->password)
        ]);

        //Se crea el registro de usuarios
        $action = 'Registrar Usuario';
        $description = "Se crea el usuario \"$newUser->name\" con ID:$newUser->id";

        $user->registerLog($action, $description);

        DB::commit();
        $this->resetFields();

        $title = "¡Usuario Registrado!";
        $type = "success";
      } else {
        $title = "Sesión no registrada";
        $description = "por favor vuelva a iniciar sesión.";
      } //.end if-else (1)

      //Se emite la alerta correspondiente
      $this->alert($title, $type, $description);
    } else {
      $this->doesNotPermission('crear nuevo usuario');
    }
  }

  protected function update()
  {
    if (userHasPermission('update_user')) {
      /** @var User */
      $user = User::find($this->userId);

      /** @var User */
      $actualUser = User::find(session()->get('userId'));

      if ($user) {
        DB::beginTransaction();

        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        $description = "Se actualizaron los datos del usuario $user->name con ID:$user->id";
        $actualUser->registerLog('Actualizar Datos', $description);

        $this->alert('¡Datos Actualizados!', 'info');
        $this->resetFields();

        DB::commit();
      } else {
        $this->alert('¡Usuario no encontrado!', 'error');
        $this->resetFields();
      }
    } else {
      $this->doesNotPermission('actualizar usuarios');
    }
  }

  /**
   * Se encarga de montar los datos
   * del usuario en el componente
   */
  public function edit($id)
  {
    if (userHasPermission('update_user')) {
      /** @var User */
      $user = User::find($id);

      if ($user) {
        $this->user = str_replace(' ', '-', $user->name);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->profilePhoto = $user->getProfilePhotoUrlAttribute();
        $this->state = "editing";
      } else {
        $this->alert('¡Usuario no encontrado!', 'error');
      }
    } else {
      $this->doesNotPermission('actualizar usuarios');
    }
  }

  /**
   * Caracteristica para la eliminacion de usuarios
   * de forma segura.
   */
  public function destroy($id, $password)
  {
    //Se comprueba si el usuario tiene permiso
    if (userHasPermission('delete_user')) {
      //Se recuperan las entidades del administrador y del usuario a eliminar
      /** @var User */
      $userToDelete = User::find($id);
      /** @var User */
      $admin = User::find(session()->get('userId'));

      //Se verifica que la constraseña sea la correcta
      if (Hash::check($password, $admin->password)) {
        //Se valida que exista el usuario a eliminar
        if ($userToDelete) {
          //Se recuperan los roles principales de cada uno de los usuarios
          /** @var Role */
          $adminRole = $admin->roles()->orderBy('id')->where('state', 1)->first();
          /** @var Role */
          $userRole = $userToDelete->roles()->orderBy('id')->where('state', 1)->first();

          //Se procede a la eliminacion de datos
          try {
            DB::beginTransaction();
            $canDelete = true;

            //Se comprueba si el usario tiene un rol principal
            if ($userRole) {
              //Se comprueba que rol del usuario sea igual o mayor que el del administrador
              if ($adminRole->id >= $userRole->id) {
                //Se hace una ultima validacion, para el caso de superadmin
                if ($userRole->id === 1) {
                  //Se recupera el numero de usuarios con este rol
                  $count = $userRole->users()->count();
                  if ($count <= 1) {
                    $canDelete = false;
                  } //.end if (7)
                } //.end if (6)
              } else {
                $canDelete = false;
              } //.end if-else (5)
            } //.end if (4)

            if ($canDelete) {
              $userName = $userToDelete->name;
              $userToDelete->deleteProfilePhoto();
              $userToDelete->delete();

              //Se actualiza el registro
              $admin->registerLog('Eliminar Usuario', "Se eliminó el usuario \"$userName\"");
              $this->alert('Usuario Eliminado', 'success');
              $this->resetFields();
              DB::commit();
            } else {
              $this->alert('¡No puede eliminar este usuario!', 'error');
            }
          } catch (\Throwable $th) {
            $this->emitError($th);
          } //.end try-catch
        } else {
          $this->alert('¡Usuario a eliminar no existe!', 'error');
        } //.end if-else (3)
      } else {
        $this->alert('¡Contraseña Incorrecta!', 'error');
      } //.end if-else (2)
    } else {
      $this->doesNotPermission('eliminar usuarios');
    } //.end if-else (1)
  } //.end method

  public function closeUserSessions($id)
  {
    if (userHasPermission('close_user_session')) {
      try {
        DB::table('sessions')
          ->where('user_id', $id)
          ->delete();
        
        if($id === session()->get('user_id')){
          $this->emit('reload');
        }else{
          $this->alert('¡Sesión Cerrada!');
        }
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('cerrar sesiones de usuarios');
    }
  } //.end method

  public function showUserLogs($id)
  {
    //Recupero los datos del usuario
    /** @var User */
    $user = User::find($id, ['id', 'name']);
    
    if($user){
      $this->name = $user->name;
      //Recupero los registros de usuario
      $logsData = $user->logs()->orderBy('created_at', 'desc')->limit(100)->get();

      $this->userLogs = [];

      foreach($logsData as $index => $data){
        $this->userLogs[] = [
          'id' => $data->id,
          'index' => $index + 1,
          'action' => $data->action,
          'description' => $data->description,
          'date' => Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->longRelativeToNowDiffForHumans(),
        ];
      }

      $this->showingUserLogs = true;
    }else{
      $this->alert('Usuario no Encontrado', 'warning');
    }

  }
}
