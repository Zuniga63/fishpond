<?php

namespace App\Http\Livewire\Admin;

use App\Models\FishBatch;
use App\Models\Fishpond;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FishBatchComponent extends Component
{
  public function render()
  {
    $data = $this->getData();
    return view('livewire.admin.fish-batch-component', compact('data'))
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
      'title' => 'Lotes',
      'contentTitle' => "Lotes de Peces",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Lotes' => route('admin.fish_batch'),
      ],
    ];

    return $data;
  }

  public function getData()
  {
    $fishponds = $this->getFishponds();

    return [
      'fishponds' => $fishponds,
      'fishBatchs' => $this->getFishBatchs(),
    ];
  }

  public function getFishponds()
  {
    $data = Fishpond::orderBy('id')->get();
    $fishponds = [];

    foreach ($data as $record) {
      $fishponds[] = [
        'id' => intval($record->id),
        'name' => $record->name,
        'type' => $record->type,
        'width' => $record->width ? floatval($record->width) : null,
        'long' => $record->long ? floatval($record->long) : null,
        'maxHeight' => $record->max_height ? floatval($record->max_height) : null,
        'effectiveHeight' => $record->effective_height ? floatval($record->effective_height) : null,
        'diameter' => $record->diameter ? floatval($record->diameter) : null,
        'capacity' => $record->capacity ? intval($record->capacity) : null,
        'inUse' => $record->in_use ? true : false
      ];
    }

    return $fishponds;
  }

  public function getFishBatchs()
  {
    $userId = session()->get('userId');
    $data = FishBatch::orderBy('seedtime')->where('user_id', $userId)->get();
    $fishBatchs = [];

    foreach ($data as $item) {
      $fishBatchs[] = $this->createFishBatch($item);
    }

    return $fishBatchs;
  }

  protected function createFishBatch(FishBatch $data)
  {
    return [
      'id' => intval($data->id),
      'fishpondId' => intval($data->fishpond_id),
      'seedtime' => $data->seedtime,
      'harvest' => $data->harvest,
      'initialPopulation' => intval($data->initial_population),
      'initialWeight' => floatval($data->initial_weight),
      'population' => intval($data->population),
      'amount' => intval($data->amount),
      'createdAt' => $data->created_at,
      'updatedAt' => $data->updated_at,
    ];
  }

  // *==========================================================*
  // *================= REGLAS Y VALIDACIONES ==================*
  // *==========================================================*
  /**
   * Constuye las reglas de validación tanto para crear como para actualizar
   * @param bool $inThisMoment True para ahora, false para en otra fecha
   * @param bool $setTime True para establecer la hora.
   * @param bool $update True para decirle que agregue la regla del estanque
   */
  protected function fishBatchRules($inThisMoment = true, $setTime = false, bool $update = false)
  {
    $rules = [
      'userId' => 'required|numeric|exists:user,id',
      'fishpondId' => 'required|numeric|exists:fishpond,id',
      'population' => 'required|numeric|between:1,65535',
      'averageWeight' => 'required|numeric|between:0.01,999.99',
      'amount' => 'required|numeric|between:200,99999999.99',
    ];

    if (!$inThisMoment) {
      $rules['date'] = 'required|string|date|before_or_equal:' . Carbon::now()->format('Y-m-d');
      if ($setTime) {
        $rules['time'] = 'required|string|date_format:H:i';
      }
    }

    if ($update) {
      $rules['fishBatchId'] = "required|numeric|exists:fish_batch,id";
    }

    return $rules;
  }
  /**
   * Atributos de los campos personalizados
   */
  protected $fishBatchAttributes = [
    'userId' => 'Usuario',
    'fishpondId' => 'Estanque',
    'population' => 'Población Inicial',
    'averageWeight' => 'Peso Incial',
    'amount' => 'Costo del lote',
    'date' => 'Fecha',
    'time' => 'Hora',
  ];
  // *===============================================*
  // *==================== CRUD =====================*
  // *===============================================*
  public function storeFishBatch(array $data)
  {
    //Variables de resultado
    $ok = false;
    $errors = null;
    $fishBatch = null;
    $fihsponds = null;

    //variables temporales
    $inThisMoment = true;
    $setTime = false;

    if (array_key_exists('inThisMoment', $data)) {
      $inThisMoment = $data['inThisMoment'];
      if (array_key_exists('setTime', $data)) {
        $setTime = $data['setTime'];
      }
    }

    //Agrego el identificador del usuario al arreglo de datos
    $data['userId'] = intval(session()->get('userId'));

    if (userHasPermission('create_fish_batch')) {
      try {
        $inputs = Validator::make($data, $this->fishBatchRules($inThisMoment, $setTime), [], $this->fishBatchAttributes)->validate();

        //Recupero el estanque
        $fishpond = Fishpond::find($inputs['fishpondId'], ['id', 'name', 'in_use as inUse']);

        /**
         * Se procede a crear el lote solo si el estanque no está en uso,
         * en caso contrario se crea el error que será manejado por el front
         * y se refrescan los datos de los estanques.
         */
        if (!$fishpond->inUse) {
          //Se crea la variable de tiempo
          $fullDate = Carbon::now();
          if (!$inThisMoment) {
            if ($setTime) {
              //Se crea la fecha conbinada
              $date = $inputs['date'];
              $time = $inputs['time'];

              $fullDate = "$date $time";
              $fullDate = Carbon::createFromFormat('Y-m-d H:i', $fullDate);
            } else {
              $fullDate = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
            }
          }

          //Se inicia la transacción
          DB::beginTransaction();
          //Se crea el lote
          $fishBatch = FishBatch::create([
            'user_id' => $inputs['userId'],
            'fishpond_id' => $inputs['fishpondId'],
            'seedtime' => $fullDate->format('Y-m-d H:i:s'),
            'initial_population' => $inputs['population'],
            'initial_weight' => $inputs['averageWeight'],
            'population' => $inputs['population'],
            'amount' => $inputs['amount']
          ]);

          //Se actualiza el estanque
          $fishpond->in_use = true;
          $fishpond->save();

          //Se cierra la transacción
          DB::commit();

          $fishBatch = $this->createFishBatch($fishBatch);
          $fihsponds = $this->getFishponds();

          $ok = true;
          $this->alert('Lote de peces creado', 'success');
        } else {
          //Se crea el error
          $errors = [
            'fishpondInUse' => true,
          ];
          //Se refrescan los datos de los estanques
          $fihsponds = $this->getFishponds();

          //Se crea la alerta
          $message = "El estanque $fishpond->name se encuentra en uso actualmente por lo que no se puede sembrar en él.";
          $this->alert('Estanque en uso', 'error', $message);
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear nuevos lotes de peces');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'fishBatch' => $fishBatch,
      'fishponds' => $fihsponds,
    ];
  }

  public function updateFishBatch(array $data)
  {
    $ok = false;
    $errors = null;
    $fishBatch = null;
    $fishPonds = null;

    //variables temporales
    $inThisMoment = true;
    $setTime = false;

    if (array_key_exists('inThisMoment', $data)) {
      $inThisMoment = $data['inThisMoment'];
      if (array_key_exists('setTime', $data)) {
        $setTime = $data['setTime'];
      }
    }

    //Agrego el identificador del usuario al arreglo de datos
    $data['userId'] = intval(session()->get('userId'));

    if (userHasPermission('update_fish_batch')) {
      try {
        //Se validan los datos suministrados
        $inputs = Validator::make($data, $this->fishBatchRules($inThisMoment, $setTime, true), [], $this->fishBatchAttributes)->validate();

        //Recupero el estanque
        $fishpond = Fishpond::find($inputs['fishpondId'], ['id', 'name', 'in_use as inUse']);
        //Recupero el lote
        $fishBatch = FishBatch::find($inputs['fishBatchId']);

        /**
         * Se procede a crear el lote solo si el estanque no está en uso, o si es el mismo que el lote
         * en caso contrario se crea el error que será manejado por el front
         * y se refrescan los datos de los estanques.
         */
        if (!$fishpond->inUse || $fishpond->id === $fishBatch->fishpond_id) {
          //Se crea la variable de tiempo
          $fullDate = Carbon::now();
          if (!$inThisMoment) {
            if ($setTime) {
              //Se crea la fecha conbinada
              $date = $inputs['date'];
              $time = $inputs['time'];

              $fullDate = "$date $time";
              $fullDate = Carbon::createFromFormat('Y-m-d H:i', $fullDate);
            } else {
              $fullDate = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
            }
          }

          //Se inicia la transacción
          DB::beginTransaction();
          //Antes de actualizar el lote se comprueba si no se cambió el estanque
          if ($fishBatch->fishpond_id != $fishpond->id) {
            //Se cambia el estado del estanque anterior
            $lastFishpond = Fishpond::find($fishBatch->fishpond_id, ['id', 'in_use']);
            $lastFishpond->in_use = false;
            $lastFishpond->save();
          }

          //Se actualiza el lote
          $fishBatch->fishpond_id = $inputs['fishpondId'];
          $fishBatch->seedtime = $fullDate->format('Y-m-d H:i');
          $fishBatch->initial_population = $inputs['population'];
          $fishBatch->initial_weight = $inputs['averageWeight'];

          //Se actualiza el lote teniendo encuenta las muertes hasta la fecha
          if ($fishBatch->population == $fishBatch->initial_population) {
            $fishBatch->population = $inputs['population'];
          } else {
            //En el caso de no ser igual siempre population debe ser menor que initial population
            $diff = intval($fishBatch->population) - intval($fishBatch->initial_population);
            $fishBatch->population = $inputs['population'] + $diff;
          }

          //Se actualiza el coste
          $fishBatch->amount = $inputs['amount'];
          //Se guardan los cambios
          $fishBatch->save();

          //Se actualiza el estanque
          $fishpond->in_use = true;
          $fishpond->save();

          //Se cierra la transacción
          DB::commit();

          $fishBatch = $this->createFishBatch($fishBatch);
          $fishponds = $this->getFishponds();

          $ok = true;
          $this->alert('Lote de peces actualizado', 'success');
        } else {
          //Se crea el error
          $errors = [
            'fishpondInUse' => true,
          ];
          //Se refrescan los datos de los estanques
          $fishponds = $this->getFishponds();

          //Se crea la alerta
          $message = "El estanque $fishpond->name se encuentra en uso actualmente por lo que no se puede sembrar en él.";
          $this->alert('Estanque en uso', 'error', $message);
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
      'ok' => $ok,
      'errors' => $errors,
      'fishBatch' => $fishBatch,
      'fishponds' => $fishponds,
    ];
  } //.end method

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
