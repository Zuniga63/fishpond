<?php

namespace App\Http\Livewire\Admin;

use App\Models\Fishpond;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FishpondComponent extends Component
{
  public function render()
  {
    return view('livewire.admin.fishpond-component')
      ->layout('layouts.admin-layout', $this->layoutData);
  }

  /**
   * Este metodo se encarga de servir las variables que requiere el
   * layout de administración
   * @return array
   */
  public function getLayoutDataProperty()
  {
    $data = [
      'title' => 'Estanques',
      'contentTitle' => "Estanques",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Estanques' => route('admin.menus'),
      ],
    ];

    return $data;
  }

  // *================================================================*
  // *===================== REGLAS DE VALIDACION =====================*
  // *================================================================*
  protected function rules()
  {
    return [
      'user_id' => 'required|numeric|exists:user,id',
      'name' => 'required|string|min:3|max:20',
      'type' => 'required|string|in:circular,rectangular',
      'width' => 'nullable|numeric|between:0.01,999.99',
      'long' => 'nullable|numeric|between:0.01, 999.99',
      'max_height' => 'nullable|numeric|between:0.01,9.99',
      'effective_height' => 'nullable|numeric|between:0.01,9.99',
      'diameter' => 'nullable|numeric|between:0.01,999.99',
      'capacity' => 'nullable|numeric|between:1,65535',
    ];
  }

  protected $validationAttributes = [
    'name' => 'Nombre',
    'type' => 'Tipo de estanque',
    'width' => 'Ancho',
    'long' => 'Largo',
    'max_height' => 'Produndidad maxima',
    'effective_height' => 'Profundidad Efectiva',
    'diameter' => 'Diametro del estanque',
    'capacity' => 'Capacidad'
  ];

  public function getFishponds()
  {
    $result = [];
    $userId = session()->get('userId');

    //Se recuperan los datos
    $data = Fishpond::orderBy('created_at')->where('user_id', $userId)->get();
    foreach ($data as $record) {
      $result[] = $this->buildFishpond($record);
    }

    return $result;
  }

  // *================================================================*
  // *============================= CRUD =============================*
  // *================================================================*
  public function storeFishpond($data)
  {
    $isOk = false;
    $errors = null;
    $fishpond = null;

    if (userHasPermission('create_fishpond')) {
      //Se agrega el identificador del usuario
      $data['user_id'] = session()->get('userId');
      try {
        $inputs = Validator::make($data, $this->rules(), [], $this->validationAttributes)->validate();
        $record = Fishpond::create($inputs);
        $isOk = true;
        $fishpond = $this->buildFishpond($record);
        $this->alert('Estanque Almacenado', 'success');
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear nuevos estanques');
    }

    return [
      'isOk' => $isOk,
      'errors' => $errors,
      'data' => $fishpond
    ];
  }

  public function updateFishpond($id, $data)
  {
    $isOk = false;
    $errors = null;
    $fishpond = null;
    $userId = intval(session()->get('userId'));

    if (userHasPermission('update_fishpond')) {
      //Se agrega el identificador del usuario
      $data['user_id'] = session()->get('userId');
      try {
        $inputs = Validator::make($data, $this->rules(), [], $this->validationAttributes)->validate();
        //Se recupera la instancia del estanque
        $fishpond = Fishpond::find($id);
        if ($fishpond) {
          if (intval($fishpond->user_id) === $userId) {
            //Se procede a actualizar los campos
            $fishpond->name = $inputs['name'];
            $fishpond->type = $inputs['type'];
            $fishpond->width = $inputs['width'];
            $fishpond->long = $inputs['long'];
            $fishpond->max_height = $inputs['max_height'];
            $fishpond->effective_height = $inputs['effective_height'];
            $fishpond->diameter = $inputs['diameter'];
            $fishpond->capacity = $inputs['capacity'];

            $fishpond->save();

            $isOk = true;
            $fishpond = $this->buildFishpond($fishpond);
            $this->alert('Estanque Almacenado', 'info');
          } else {
            $this->doesNotPermission('actualizar este estanque.');
          }
        } else {
          $this->alert('Estanque no encontrado', 'error');
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('actualizar estanques');
    }

    return [
      'isOk' => $isOk,
      'errors' => $errors,
      'data' => $fishpond
    ];
  }

  protected function buildFishpond($data)
  {
    $type = $data->type;
    $width = $data->width ? floatval($data->width) : null;
    $long = $data->long ? floatval($data->long) : null;
    $maxHeight = $data->max_height ? floatval($data->max_height) : null;
    $effectiveHeight = $data->effective_height ? floatval($data->effective_height) : null;
    $diameter = $data->diameter ? floatval($data->diameter) : null;
    $capacity = $data->capacity ? intval($data->capacity) : null;
    $inUse = $data->in_use ? true : false;
    $capacityByArea = null;
    $capacityByVolume = null;

    $area = null;
    $effectiveVolume = null;
    $maxVolume = null;

    //Se calculan las variables auxiliares
    if ($type === 'circular' && $diameter) {
      $area = pi() * pow(($diameter / 2), 2);
    } else if ($type === 'rectangular' && $width && $long) {
      $area = $width * $long;
    }

    if ($area) {
      //Se calculan los volumenes
      $effectiveVolume = $effectiveHeight ? round($area * $effectiveHeight, 2) : null;
      $maxVolume = $maxHeight ? round($area * $maxHeight, 2) : null;

      //Se calculan los peces por area y por volumen
      if ($capacity) {
        $capacityByArea = round($capacity / $area, 2);
        $capacityByVolume = $effectiveVolume ? round($capacity / $effectiveVolume, 2) : null;
      }

      //Se redondea el área
      $area = round($area, 2);
    }


    return [
      'id' => intval($data->id),
      'name' => $data->name,
      'type' => $type,
      'diameter' => $diameter,
      'width' => $width,
      'long' => $long,
      'effectiveHeight' => $effectiveHeight,
      'maxHeight' => $maxHeight,
      'area' => $area,
      'effectiveVolume' => $effectiveVolume,
      'maxVolume' => $maxVolume,
      'capacity' => $capacity,
      'capacityByArea' => $capacityByArea,
      'capacityByVolume' => $capacityByVolume,
      'inUse' => $inUse,
    ];
  }

  // *================================================================*
  // *========================== UTILIDADES ==========================*
  // *================================================================*
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
}
