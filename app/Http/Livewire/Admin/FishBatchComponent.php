<?php

namespace App\Http\Livewire\Admin;

use App\Models\FishBatch;
use App\Models\FishBatchBiometry;
use App\Models\FishBatchDeath;
use App\Models\FishBatchExpense;
use App\Models\FishBatchObservation;
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
    //Recupero las observaciones
    $observations = $data->observations()
      ->orderBy('created_at')
      ->get(['id', 'title', 'message', 'created_at as createdAt', 'updated_at as updatedAt'])
      ->toArray();
    //Recuepero los gastos
    $expenses = $data->expenses()
      ->orderBy('expense_date')
      ->get(['id', 'expense_date as date', 'description', 'amount', 'created_at as createdAt', 'updated_at as updatedAt'])
      ->toArray();

    $expenses = array_map(function ($expense) {
      $expense['amount'] = intval($expense['amount']);
      return $expense;
    }, $expenses);

    //Recupero las muertes
    $deaths = $data->deaths()->orderBy('created_at')
      ->get(['id', 'deaths', 'created_at as createdAt', 'updated_at as updatedAt'])
      ->toArray();

    //Recupero las biometrías
    $biometries = $data->biometries()
      ->orderBy('date')
      ->get(['id', 'biometry_date as date', 'measurements', 'created_at as createdAt', 'updated_at as updatedAt'])
      ->toArray();

    return [
      'id' => intval($data->id),
      'fishpondId' => intval($data->fishpond_id),
      'seedtime' => $data->seedtime,
      'harvest' => $data->harvest,
      'initialPopulation' => intval($data->initial_population),
      'initialWeight' => floatval($data->initial_weight),
      'population' => intval($data->population),
      'amount' => intval($data->amount),
      'observations' => $observations,
      'expenses' => $expenses,
      'biometries' => $biometries,
      'deaths' => $deaths,
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
      'amount' => 'required|numeric|between:100,99999999.99',
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

  protected function observationRules($update = false)
  {
    $rules = [
      'fishBatchId' => 'required|integer|min:1|exists:fish_batch,id',
      'title' => 'required|string|min:3|max:45',
      'message' => 'required|string|min:3|max:255'
    ];

    if ($update) {
      $rules['observationId'] = 'required|integer|min:1|exists:fish_batch_observation,id';
    }

    return $rules;
  }

  protected $observationAttributes = [
    'fishBatchId' => 'id del estanque',
    'observationId' => 'id de la observación',
    'title' => 'título',
    'message' => 'mensaje',
  ];

  protected function expenseRules(bool $inThisMoment = true, bool $setTime = false, bool $update = false)
  {
    $rules = [
      'fishBatchId' => 'required|integer|min:1|exists:fish_batch,id',
      'description' => 'required|string|min:3|max:255',
      'amount' => 'required|numeric|between:100,99999999.99',
    ];

    if (!$inThisMoment) {
      $rules['date'] = 'required|string|date|before_or_equal:' . Carbon::now()->format('Y-m-d');
      if ($setTime) {
        $rules['time'] = 'required|string|date_format:H:i';
        $rules['fullDate'] = 'required|string|date_format:Y-m-d H:i';
      }
    }

    if ($update) {
      $rules['expenseId'] = 'required|integer|min:1|exists:fish_batch_expense,id';
    }

    return $rules;
  }

  protected $expenseAttributes = [
    'fishBatchId' => 'identificador del lote',
    'expenseId' => 'gasto',
    'description' => 'descripción',
    'amount' => 'importe',
    'date' => 'fecha',
    'time' => 'hora',
    'fullDate' => 'fecha completa',
  ];

  protected function deathsRules(bool $update = false)
  {
    $rules = [
      'fishBatchId' => 'required|integer|min:1|exists:fish_batch,id',
      'deaths' => 'required|integer|min:1|max:65535'
    ];

    if ($update) {
      $rules['deathId'] = 'required|integer|min:|exists:fish_batch_death,id';
    }

    return $rules;
  }

  protected $deathAttributes = [
    'fishBatchId' => 'identificador del lote',
    'deaths' => 'numero de muertes',
    'deathId' => 'identificador de las muertes'
  ];

  protected function biometryRules(bool $inThisMoment = true, bool $setTime = false, bool $update = false)
  {
    $rules = [
      'fishBatchId' => 'required|integer|min:1|exists:fish_batch,id',
      'measurements' => 'required|array'
    ];

    if (!$inThisMoment) {
      $rules['date'] = 'required|string|date|before_or_equal:' . Carbon::now()->format('Y-m-d');
      if ($setTime) {
        $rules['time'] = 'required|string|date_format:H:i';
        $rules['fullDate'] = 'required|string|date_format:Y-m-d H:i';
      }
    }

    if ($update) {
      $rules['biometryId'] = 'required|integer|min:1|exists:fish_batch_biometry,id';
    }

    return $rules;
  }

  protected $biometryAttributes = [
    'fishBatchId' => 'identificador del lote',
    'biometryId' => 'identificador de la biometría',
    'measurements' => 'mediciones',
    'date' => 'fecha',
    'time' => 'hora',
    'fullDate' => 'fecha y hora',
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
    $fishponds = null;

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
          $fishBatch->initial_weight = $inputs['averageWeight'];

          //Se actualiza la población teniendo encuenta las muertes hasta la fecha
          if ($fishBatch->population == $fishBatch->initial_population) {
            $fishBatch->population = $inputs['population'];
          } else {
            //En el caso de no ser igual siempre population debe ser menor que initial population
            $diff = intval($fishBatch->population) - intval($fishBatch->initial_population);
            $fishBatch->population = $inputs['population'] + $diff;
          }
          //Se actualiza la población inicial
          $fishBatch->initial_population = $inputs['population'];
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

  public function storeObservation(array $data)
  {
    $ok = false;
    $errors = null;
    $observation = null;
    $rules = $this->observationRules();
    $attributes = $this->observationAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Recupero el lote de peces
        /** @var FishBatch */
        $fishBatch = FishBatch::find($inputs['fishBatchId'], ['id']);
        //Creo la observación
        $observation = $fishBatch->observations()->create([
          'title' => $inputs['title'],
          'message' => $inputs['message']
        ]);

        $observation = [
          'id' => $observation->id,
          'fishBatchId' => $observation->fish_batch_id,
          'title' => $observation->title,
          'message' => $observation->message,
          'createdAt' => Carbon::createFromFormat('Y-m-d H:i:s', $observation->created_at)->format('Y-m-d H:i:s'),
          'updatedAt' => Carbon::createFromFormat('Y-m-d H:i:s', $observation->updated_at)->format('Y-m-d H:i:s'),
        ];

        $ok = true;
        $this->alert('Observación Creada', 'success');
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear observaciones');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'observation' => $observation
    ];
  }

  public function updateObservation(array $data)
  {
    $ok = false;
    $errors = null;
    $observation = null;
    $rules = $this->observationRules(true);
    $attributes = $this->observationAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Recupero la observación
        $observation = FishBatchObservation::find($inputs['observationId']);
        //Se actualiza la observación
        $observation->title = $inputs['title'];
        $observation->message = $inputs['message'];
        $observation->save();

        //Se crea el objeto
        $observation = [
          'id' => $observation->id,
          'fishBatchId' => $observation->fish_batch_id,
          'title' => $observation->title,
          'message' => $observation->message,
          'createdAt' => Carbon::createFromFormat('Y-m-d H:i:s', $observation->created_at)->format('Y-m-d H:i:s'),
          'updatedAt' => Carbon::createFromFormat('Y-m-d H:i:s', $observation->updated_at)->format('Y-m-d H:i:s'),
        ];

        $ok = true;
        $this->alert('Observación Creada', 'success');
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear observaciones');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'observation' => $observation
    ];
  }

  public function destroyObservation($observationId)
  {
    $ok = false;
    $errors = null;
    $userId = session()->get('userId');
    if (userHasPermission('update_fish_batch')) {
      //Recupero la observación
      $observation = FishBatchObservation::find($observationId);
      if ($observation) {
        $userSafe = intval($observation->fishBatch->user_id) === $userId;
        if ($userSafe) {
          $observation->delete();
          $ok = true;
          $this->alert('Observación eliminada');
        } else {
          $errors = [
            'unknow' => 'Usuario desconocido',
          ];
        }
      } else {
        $this->alert('Observación no encontrada', 'error');
        $errors = [
          'notFound' => true
        ];
      }
    } else {
      $this->doesNotPermission('eliminar observaciones');
    }

    return [
      'ok' => $ok,
      'errors' => $errors
    ];
  }

  public function storeExpense(array $data)
  {
    $ok = false;
    $errors = null;
    $expense = null;

    //Se crean las variables temporales
    $inThisMoment = true;
    $setTime = true;

    if (array_key_exists('inThisMoment', $data)) {
      $inThisMoment = $data['inThisMoment'];
      if (array_key_exists('setTime', $data)) {
        $setTime = $data['setTime'];
      }
    }

    //Se obtienen las reglas y atributos
    $rules = $this->expenseRules($inThisMoment, $setTime);
    $attributes = $this->expenseAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Recupero el lote de peces
        /** @var FishBatch */
        $fishBatch = FishBatch::find($inputs['fishBatchId'], ['id', 'seedtime']);


        //Se crea la varible para la fecha
        $fullDate = Carbon::now();
        $seedtime = Carbon::createFromFormat('Y-m-d H:i:s', $fishBatch->seedtime);
        $dateIsCorrect = true;

        if (!$inThisMoment) {
          if ($setTime) {
            $fullDate = Carbon::createFromFormat('Y-m-d H:i', $inputs['fullDate']);
          } else {
            $fullDate = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
            $seedtimeStart = $seedtime->copy()->startOfDay();
            $seedtimeEnd = $seedtime->copy()->endOfDay();

            if ($fullDate->between($seedtimeStart, $seedtimeEnd)) {
              $fullDate = $seedtime->copy()->addSecond();
            }
          }

          if ($fullDate->lessThan($seedtime)) {
            $dateIsCorrect = false;
          }
        }

        if ($dateIsCorrect) {
          //Se guarda la información en la base de datos
          $expense = $fishBatch->expenses()->create([
            'expense_date' => $fullDate->format('Y-m-d H:i:s'),
            'description' => $inputs['description'],
            'amount' => $inputs['amount']
          ]);

          //Se crea el objeto a retornar
          $expense = [
            'id' => $expense->id,
            'fishBatchId' => $expense->fish_batch_id,
            'date' => $fullDate->format('Y-m-d H:i:s'),
            'description' => $expense->description,
            'amount' => intval($expense->amount),
            'createdAt' => $expense->created_at,
            'updatedAt' => $expense->updated_at
          ];

          $ok = true;
          $this->alert('Gasto Guardado', 'success');
        } else {
          $errors = ['fullDate' => 'La fecha es anterior a la siembra del lote.'];
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear nuevos gastos');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'expense' => $expense,
    ];
  }

  public function updateExpense(array $data)
  {
    $ok = false;
    $errors = null;
    $expense = null;

    //Se crean las variables temporales
    $inThisMoment = true;
    $setTime = true;

    if (array_key_exists('inThisMoment', $data)) {
      $inThisMoment = $data['inThisMoment'];
      if (array_key_exists('setTime', $data)) {
        $setTime = $data['setTime'];
      }
    }

    //Se obtienen las reglas y atributos
    $rules = $this->expenseRules($inThisMoment, $setTime, true);
    $attributes = $this->expenseAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Recupero el lote de peces
        /** @var FishBatch */
        $fishBatch = FishBatch::find($inputs['fishBatchId'], ['id', 'seedtime']);


        //Se crea la varible para la fecha
        $fullDate = Carbon::now();
        $seedtime = Carbon::createFromFormat('Y-m-d H:i:s', $fishBatch->seedtime);
        $dateIsCorrect = true;

        if (!$inThisMoment) {
          if ($setTime) {
            $fullDate = Carbon::createFromFormat('Y-m-d H:i', $inputs['fullDate']);
          } else {
            $fullDate = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
            $seedtimeStart = $seedtime->copy()->startOfDay();
            $seedtimeEnd = $seedtime->copy()->endOfDay();

            if ($fullDate->between($seedtimeStart, $seedtimeEnd)) {
              $fullDate = $seedtime->copy()->addSecond();
            }
          }

          if ($fullDate->lessThan($seedtime)) {
            $dateIsCorrect = false;
          }
        }

        if ($dateIsCorrect) {
          //Se recupera el gasto
          $expense = FishBatchExpense::find($inputs['expenseId']);
          //Se actualiza
          $expense->expense_date = $fullDate->format('Y-m-d H:i:s');
          $expense->description = $inputs['description'];
          $expense->amount = $inputs['amount'];
          $expense->save();

          //Se crea el objeto a retornar
          $expense = [
            'id' => $expense->id,
            'fishBatchId' => $expense->fish_batch_id,
            'date' => $fullDate->format('Y-m-d H:i:s'),
            'description' => $expense->description,
            'amount' => intval($expense->amount),
            'createdAt' => $expense->created_at,
            'updatedAt' => $expense->updated_at
          ];

          $ok = true;
          $this->alert('Gasto Actualizado', 'success');
        } else {
          $errors = ['fullDate' => 'La fecha es anterior a la siembra del lote.'];
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear nuevos gastos');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'expense' => $expense,
    ];
  }

  public function destroyExpense($expenseId)
  {
    $ok = false;
    $errors = null;
    $userId = session()->get('userId');

    if (userHasPermission('update_fish_batch')) {
      //Recupero el gasto
      /** @var FishBatchExpense */
      $expense = FishBatchExpense::find($expenseId);
      if ($expense) {
        //Se verifica si el usuario puede eliminar el gasto
        $userSafe = intval($expense->fishBatch->user_id) === $userId;
        if ($userSafe) {
          $expense->delete();
          $ok = true;
          $this->alert('Gasto eliminado');
        } else {
          $errors = ['unknow' => 'Intento de eliminación inválido.'];
        }
      } else {
        $errors = ['notFound' => true];
      }
    } else {
      $this->doesNotPermission('eliminar gastos');
    }

    return [
      'ok' => $ok,
      'errors' => $errors
    ];
  }

  public function storeDeathReport(array $data)
  {
    $ok = false;
    $errors = null;
    $death = null;

    $rules = $this->deathsRules();
    $attributes = $this->deathAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Recupero el lote de peces
        /** @var FishBatch */
        $fishBatch = FishBatch::find($inputs['fishBatchId'], ['id', 'population']);
        $population = intval($fishBatch->population);

        if ($population >= $inputs['deaths']) {
          //Se inicia la transacción
          DB::beginTransaction();
          //Creo el reporte de muertes
          $death = $fishBatch->deaths()->create(['deaths' => $inputs['deaths']]);
          //Disminuyo la población de peces del estanque
          $fishBatch->population = $population - $inputs['deaths'];
          $fishBatch->save();

          DB::commit();
          //Construyo el objeto de retorno
          $death = [
            'id' => $death->id,
            'fishBatchId' => $fishBatch->id,
            'deaths' => $death->deaths,
            'createdAt' => $death->created_at,
            'updatedAt' => $death->updated_at
          ];

          $ok = true;
          $this->alert('Se guardó exitosamente', 'success');
        } else {
          $errors = ['deaths' => 'las muerte superan la población del lote.'];
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('agregar registro de muertes');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'death' => $death
    ];
  }

  public function updateDeathReport(array $data)
  {
    $ok = false;
    $errors = null;
    $death = null;

    $rules = $this->deathsRules(true);
    $attributes = $this->deathAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Recupero el lote de peces
        /** @var FishBatch */
        $fishBatch = FishBatch::find($inputs['fishBatchId'], ['id', 'population']);
        $population = intval($fishBatch->population);


        if ($population >= $inputs['deaths']) {
          //Se inicia la transacción
          DB::beginTransaction();
          //Recupero el reporte
          $death = FishBatchDeath::find($inputs['deathId']);
          //Se anulan las muertes del deporte
          $population += $death->deaths;
          //Se actualiza el reporte
          $death->deaths = $inputs['deaths'];
          $death->save();
          //Se disminuye la población del estanque
          $fishBatch->population = $population - $inputs['deaths'];
          $fishBatch->save();

          DB::commit();
          //Construyo el objeto de retorno
          $death = [
            'id' => $death->id,
            'fishBatchId' => $fishBatch->id,
            'deaths' => $death->deaths,
            'createdAt' => $death->created_at,
            'updatedAt' => $death->updated_at
          ];

          $ok = true;
          $this->alert('Se actualizó exitosamente', 'success');
        } else {
          $errors = ['deaths' => 'las muerte superan la población del lote.'];
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('agregar registro de muertes');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'death' => $death
    ];
  }

  public function destroyDeathReport(int $deathId)
  {
    $ok = false;
    $errors = null;
    $userId = session()->get('userId');

    if (userHasPermission('update_fish_batch')) {
      //Recupero el reporte
      /** @var FishBatchDeath */
      $report = FishBatchDeath::find($deathId, ['id', 'fish_batch_id', 'deaths']);
      //Recupero el lote de peces
      if ($report) {
        $fishBatch = FishBatch::find($report->fish_batch_id, ['id', 'population', 'user_id']);
        if ($fishBatch->user_id === $userId) {
          $population = $fishBatch->population;
          $population += $report->deaths;

          DB::beginTransaction();
          //Se actualiza el lote
          $fishBatch->population = $population;
          $fishBatch->save();
          //Se elimina el reporte
          $report->delete();
          DB::commit();

          $ok = true;
          $this->alert('¡Reporte Eliminado!', 'success');
        } else {
          $this->alert('¡Eliminación Ilegal!', 'error');
        }
      } else {
        $errors = ['notFound' => true];
      }
    } else {
      $this->doesNotPermission('eliminar registros');
    }

    return [
      'ok' => $ok,
      'errors' => $errors
    ];
  }

  public function storeBiometry($data)
  {
    $ok = false;
    $errors = null;
    $biometry = null;

    //Se crean las variables temporales
    $inThisMoment = true;
    $setTime = true;

    if (array_key_exists('inThisMoment', $data)) {
      $inThisMoment = $data['inThisMoment'];
      if (array_key_exists('setTime', $data)) {
        $setTime = $data['setTime'];
      }
    }

    //Se obtienen las reglas de validación
    $rules = $this->biometryRules($inThisMoment, $setTime);
    $attributes = $this->biometryAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Se recupera el lote de peces
        /** @var FishBatch */
        $fishBatch = FishBatch::find($inputs['fishBatchId'], ['id', 'seedtime']);

        //Se crea la instancia para la fecha
        $fullDate = Carbon::now();
        $seedtime = Carbon::createFromFormat('Y-m-d H:i:s', $fishBatch->seedtime);
        $dateIsCorrect = true;

        if (!$inThisMoment) {
          if ($setTime) {
            $fullDate = Carbon::createFromFormat('Y-m-d H:i', $inputs['fullDate']);
          } else {
            $fullDate = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
            $seedtimeStart = $seedtime->copy()->startOfDay();
            $seedtimeEnd = $seedtime->copy()->endOfDay();

            if ($fullDate->between($seedtimeStart, $seedtimeEnd)) {
              $fullDate = $seedtime->copy()->addSecond();
            }
          }

          if ($fullDate->lessThan($seedtime)) {
            $dateIsCorrect = false;
          }
        }

        if ($dateIsCorrect) {
          //Se guarda la biometría
          $biometry = $fishBatch->biometries()->create([
            'biometry_date' => $fullDate->format('Y-m-d H:i:s'),
            'measurements' => $inputs['measurements']
          ]);

          //Se muta el objeto de la biometría
          $biometry = [
            'id' => $biometry->id,
            'date' => $biometry->biometry_date,
            'measurements' => $biometry->measurements,
            'createdAt' => $biometry->created_at,
            'updatedAt' => $biometry->updated_at,
          ];

          $ok = true;
          $this->alert('¡Biometría Almacenada!', 'success');
        } else {
          $errors = ['fullDate' => 'La fecha es anterior a la siembra del lote.'];
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear biometrías');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'biometry' => $biometry
    ];
  }

  public function updateBiometry($data)
  {
    $ok = false;
    $errors = null;
    $biometry = null;

    //Se crean las variables temporales
    $inThisMoment = true;
    $setTime = true;

    if (array_key_exists('inThisMoment', $data)) {
      $inThisMoment = $data['inThisMoment'];
      if (array_key_exists('setTime', $data)) {
        $setTime = $data['setTime'];
      }
    }

    //Se obtienen las reglas de validación
    $rules = $this->biometryRules($inThisMoment, $setTime, true);
    $attributes = $this->biometryAttributes;

    if (userHasPermission('update_fish_batch')) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Se recupera el lote de peces
        /** @var FishBatch */
        $fishBatch = FishBatch::find($inputs['fishBatchId'], ['id', 'seedtime']);

        //Se recupera la biometría
        $biometry = FishBatchBiometry::find($inputs['biometryId']);

        //Se crea la instancia para la fecha
        $fullDate = Carbon::now();
        $seedtime = Carbon::createFromFormat('Y-m-d H:i:s', $fishBatch->seedtime);
        $dateIsCorrect = true;

        if (!$inThisMoment) {
          if ($setTime) {
            $fullDate = Carbon::createFromFormat('Y-m-d H:i', $inputs['fullDate']);
          } else {
            $fullDate = Carbon::createFromFormat('Y-m-d', $inputs['date'])->startOfDay();
            $seedtimeStart = $seedtime->copy()->startOfDay();
            $seedtimeEnd = $seedtime->copy()->endOfDay();

            if ($fullDate->between($seedtimeStart, $seedtimeEnd)) {
              $fullDate = $seedtime->copy()->addSecond();
            }
          }

          if ($fullDate->lessThan($seedtime)) {
            $dateIsCorrect = false;
          }
        }

        if ($dateIsCorrect) {
          //Se actualiza la biometría
          $biometry->biometry_date = $fullDate->format('Y-m-d H:i:s');
          $biometry->measurements = $inputs['measurements'];
          $biometry->save();

          //Se muta el objeto de la biometría
          $biometry = [
            'id' => $biometry->id,
            'date' => $biometry->biometry_date,
            'measurements' => $biometry->measurements,
            'createdAt' => $biometry->created_at,
            'updatedAt' => $biometry->updated_at,
          ];

          $ok = true;
          $this->alert('¡Biometría Actalizada!', 'success');
        } else {
          $errors = ['fullDate' => 'La fecha es anterior a la siembra del lote.'];
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission('crear biometrías');
    }

    return [
      'ok' => $ok,
      'errors' => $errors,
      'biometry' => $biometry
    ];
  }

  public function destroyBiometry(int $biometryId)
  {
    $ok = false;
    $errors = null;
    $userId = session()->get('userId');

    if (userHasPermission('update_fish_batch')) {
      //Se recupera la biometría
      /** @var FishBatchBiometry */
      $biometry = FishBatchBiometry::find($biometryId, ['id', 'fish_batch_id']);
      //Recupero el lote de peces

      if ($biometry) {

        // $fishBatch = FishBatch::find($report->fish_batch_id, ['id', 'population', 'user_id']);
        if ($biometry->fishBatch->user_id === $userId) {
          $biometry->delete();

          $ok = true;
          $this->alert('¡Biometría Eliminada!', 'success');
        } else {
          $this->alert('¡Eliminación Ilegal!', 'error');
        }
      } else {
        $errors = ['notFound' => true];
      }
    } else {
      $this->doesNotPermission('eliminar registros');
    }

    return [
      'ok' => $ok,
      'errors' => $errors
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
