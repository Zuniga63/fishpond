<?php

namespace App\Http\Livewire\Admin;

use App\Models\Fishpond;
use App\Models\FishpondCost;
use App\Models\User;
use Carbon\Carbon;
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

  public function getFishponds()
  {
    $result = [];
    $userId = session()->get('userId');

    //Se recuperan los datos
    $data = Fishpond::with('costs')->orderBy('created_at')->where('user_id', $userId)->get();
    foreach ($data as $record) {
      $result[] = $this->buildFishpond($record);
    }

    return $result;
  }

  /**
   * Este metodo se encarga de crear las instancias de los 
   * estanques que son utilizadas por el componente
   */
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
    $costs = [];
    $costsAmount = 0;

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
      $effectiveVolume = $effectiveHeight ? round($area * $effectiveHeight, 1) : null;
      $maxVolume = $maxHeight ? round($area * $maxHeight, 1) : null;

      //Se calculan los peces por area y por volumen
      if ($capacity) {
        $capacityByArea = round($capacity / $area, 0);
        $capacityByVolume = $effectiveVolume ? round($capacity / $effectiveVolume, 0) : null;
      }

      //Se redondea el área
      $area = round($area, 2);
    }

    foreach ($data->costs as $record) {
      $cost = $this->buildFishpondCost($record);
      $costs[] = $cost;
      $costsAmount += $cost['amount'];
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
      'costs' => $costs,
      'costsAmount' => $costsAmount
    ];
  }

  /**
   * Este metodo se encarga de crear las instancias de
   * los costos que hacen parte de los estanques y 
   * que son utilizdas por el compoenente.
   */
  protected function buildFishpondCost($cost)
  {
    $date = Carbon::createFromFormat('Y-m-d H:i:s', $cost->cost_date);
    $amount = floatval($cost->amount);
    return [
      'id' => intval($cost->id),
      'date' => $date->format('Y-m-d'),
      'time' => $date->format('H:i'),
      'type' => $cost->type,
      'description' => $cost->description,
      'amount' => $amount,
      'createdAt' => $cost->created_at,
      'updatedAt' => $cost->updated_at
    ];
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


  /**
   * Se encarga de crear las reglas de validación que
   * se van a utilizar para los datos provenietes del formulario
   * @param bool $inThisMoment Sirve para definir si construye la fecha o esta es suministrada
   * @param bool $setTime Si la hora de la fecha fue establecida
   */
  protected function costRules(bool $inThisMoment = true, bool $setTime = false)
  {
    $rules = [
      'fishpondId' => 'required|numeric|exists:fishpond,id',
      'type' => 'required|string|in:materials,workforce,maintenance',
      'description' => 'required|string|min:3|max:255',
      'amount' => 'required|numeric|between:0.01,99999999.99',
      'inThisMoment' => 'required|boolean',
      'setTime' => 'required|boolean',
    ];

    if (!$inThisMoment) {
      $rules['date'] = 'required|string|date|before_or_equal:' . Carbon::now()->format('Y-m-d');
      if ($setTime) {
        $rules['time'] = 'required|string|date_format:H:i';
      }
    }

    return $rules;
  }

  protected function costAttributes()
  {
    return [
      'type' => 'Tipo de costo',
      'description' => 'descripción',
      'amount' => 'importe',
      'date' => 'fecha',
      'time' => 'hora'
    ];
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

  public function destroyFishpond(int $id)
  {
    $isOk = false;
    $errors = [];
    //Se busca el estanque
    if (userHasPermission('delete_fishpond')) {
      $fispond = Fishpond::find($id, ['id', 'name']);
      if ($fispond) {
        $message = "El estanque \"$fispond->name\" fue eliminado correctamente";
        $fispond->delete();
        $this->alert('¡Estanque Eliminado!', 'success', $message);
        $isOk = true;
      } else {
        $this->alert('Estanque no encontrado', 'error');
        $errors['notFound'] = "El estanque no fue encontrado";
      }
    } else {
      $this->doesNotPermission('eliminar estanques');
    }

    return [
      'isOk' => $isOk,
      'errors' => $errors
    ];
  }

  public function storeFishpondCost($data)
  {
    $isOk = false;
    $errors = [];
    $cost = null;
    $fishpond = null;

    if (userHasPermission('create_fishpond_cost')) {
      $inThisMoment = $data['inThisMoment'];
      $setTime = $data['setTime'];
      $rules = $this->costRules($inThisMoment, $setTime);
      $attributes = $this->costAttributes();

      try {
        $validation = Validator::make($data, $rules, [], $attributes)->validate();
        $inputs = $this->buildCostData($validation);

        //Recupero el estanque 
        /** @var Fishpond */
        $fishpond = Fishpond::find($inputs['fishpond_id'], ['id']);
        //Se guarda el costo del estanque
        $cost = $fishpond?->costs()->create($inputs);
        //Se emite el evento y se guarda que todo ha sidocorrecto
        $this->alert('¡Costo creado con exito!', 'success');
        $isOk = true;
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('registar costos el estanque');
    }

    if (!empty($cost)) {
      $cost = $this->buildFishpondCost($cost);
      $cost['fishpondId'] = intval($fishpond->id);
    }

    return [
      'isOk' => $isOk,
      'errors' => $errors,
      'cost' => $cost,
    ];
  }

  public function updateFishpondCost($data)
  {
    $isOk = false;
    $errors = null;
    $cost = null;
    $fishpondId = $data['fishpondId'];
    $costId = $data['costId'];

    if (userHasPermission('update_fishpond_cost')) {
      //Recupero las variables temporales
      $inThisMoment = $data['inThisMoment'];
      $setTime = $data['setTime'];
      //Recupero las reglas y los atributos
      $rules = $this->costRules($inThisMoment, $setTime);
      $attributes = $this->costAttributes();

      try {
        //Se realiza la validación
        $validation = Validator::make($data, $rules, [], $attributes)->validate();
        $inputs = $this->buildCostData($validation);

        //Se recupera el costo
        /** @var FishpondCost */
        $cost = FishpondCost::find($costId);

        //Se actualizan los campos del costo
        $cost->cost_date = $inputs['cost_date'];
        $cost->type = $inputs['type'];
        $cost->description = $inputs['description'];
        $cost->amount = $inputs['amount'];
        $cost->save();

        $this->alert('¡Costo Actualizado!', 'info');
        $isOk = true;
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('actualizar el costo de este estanque');
    }

    if (!empty($cost)) {
      $cost = $this->buildFishpondCost($cost);
      $cost['fishpondId'] = $fishpondId;
    }

    return [
      'isOk' => $isOk,
      'errors' => $errors,
      'cost' => $cost,
    ];
  }

  public function destroyFishpondCost($fishpondId, $costId)
  {
    $isOk = false;
    $errors = [];

    if (userHasPermission('delete_fishpond')) {
      //Se busca el costo
      $cost = FishpondCost::where('fishpond_id', $fishpondId)->find($costId, ['id']);
      if ($cost) {
        
        $cost->delete();
        $this->alert('¡Costo Eliminado!', 'success');
        $isOk = true;
      } else {
        $this->alert('Costo no encontrado', 'error');
        $errors['notFound'] = "El estanque no fue encontrado";
      }
    } else {
      $this->doesNotPermission('eliminar costo de estanques');
    }

    return [
      'isOk' => $isOk,
      'errors' => $errors
    ];
  }

  /**
   * Este metodo se encarga de crear la información 
   * que puede ser alamcenada en la base de datos
   * con los nombre de las columnas que este utiliza
   */
  protected function buildCostData($data)
  {
    $result = [
      'fishpond_id' => $data['fishpondId'],
      'type' => $data['type'],
      'description' => $data['description'],
      'amount' => $data['amount']
    ];

    if (!$data['inThisMoment']) {
      $date = $data['date'];
      if ($data['setTime']) {
        $time = $data['time'];
        $result['cost_date'] = Carbon::createFromFormat('Y-m-d H:i', "$date $time")->format('Y-m-d H:i:s');
      } else {
        $result['cost_date'] = Carbon::createFromFormat('Y-m-d', $date)->startOfDay()->format('Y-m-d H:i:s');
      }
    } else {
      $result['cost_date'] = Carbon::now()->format('Y-m-d H:i:s');
    }

    return $result;
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
