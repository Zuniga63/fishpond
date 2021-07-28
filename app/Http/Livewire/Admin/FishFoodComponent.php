<?php

namespace App\Http\Livewire\Admin;

use App\Models\FishFood;
use App\Models\FishFoodStock;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class FishFoodComponent extends Component
{
  public function render()
  {
    $initialData = $this->getInitialData();
    return view('livewire.admin.fish-food-component', compact('initialData'))
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
      'title' => 'Alimentos',
      'contentTitle' => "Listado de Alimentos",
      'breadcrumb' => [
        'Panel' => route('admin.dashboard'),
        'Alimentos' => route('admin.fish_food'),
      ],
    ];

    return $data;
  }

  protected function getInitialData()
  {
    $fishFoods = $this->getFishFoodList();
    $stages = [
      'initiation' => 'Inicio',
      'growth' => 'Levante',
      'grow-fat' => 'Engorde',
      'ending' => 'Finalización'
    ];

    return [
      'fishFoodList' => $fishFoods,
      'stages' => $stages
    ];
  }

  protected function getFishFoodList()
  {
    $list = [];
    $userId = session()->get('userId');
    /** @var FishFood */
    $fishFoods = FishFood::orderBy('stage')
      ->orderBy('name')
      ->where('user_id', $userId)
      ->with(['stocks' => function ($query) {
        $query->orderBy('created_at')->where('stock', '>', 0);
      }])
      ->get();

    foreach ($fishFoods as $record) {
      $fishFood = $this->createFishFood($record);
      $list[] = $fishFood;
    }

    return $list;
  }

  protected function createFishFood($data)
  {
    $stocks = [];
    foreach ($data->stocks as $stock) {
      $stocks[] = $this->createStock($stock);
    }

    $fishFood = [
      'id' => $data->id,
      'name' => $data->name,
      'brand' => $data->brand,
      'stage' => $data->stage,
      'stocks' => $stocks,
      'createdAt' => $data->created_at,
      'updatedAt' => $data->updated_at,
    ];

    return $fishFood;
  }

  protected function createStock($stock)
  {
    return [
      'id' => $stock->id,
      'initialStock' => $stock->initial_stock,
      'stock' => $stock->stock,
      'amount' => intval($stock->amount),
      'createdAt' => $stock->created_at,
      'updatedAt' => $stock->updated_at
    ];
  }

  // *==========================================================*
  // *================= REGLAS Y VALIDACIONES ==================*
  // *==========================================================*
  protected function fishFoodRules(bool $update = false)
  {
    $rules = [
      'userId' => 'required|integer|exists:user,id',
      'name' => 'required|string|min:3|max:50',
      'brand' => 'required|string|min:3|max:50',
      'stage' => 'required|in:initiation,growth,grow-fat,ending'
    ];

    if ($update) {
      $rules['fishFoodId'] = 'required|integer|exists:fish_food,id';
    }

    return $rules;
  }

  protected $fishFoddAttributes = [
    'userId' => 'usuario',
    'name' => 'nombre',
    'brand' => 'marca',
    'stage' => 'etapa',
    'fishFoodId' => 'alimento'
  ];

  protected function fishFoodStockRules(bool $update = false)
  {
    return [
      'fishFoodId' => 'required|integer|exists:fish_food,id',
      'quantity' => 'required|numeric|between:1,16700.00',
      'amount' => 'required|numeric|between:0.01,99999999.99',
    ];
  }

  protected $fishFoodStockAttributes = [
    'fishFoodId' => 'alimento',
    'quantity' => 'cantidad',
    'amount' => 'importe'
  ];

  // *===============================================*
  // *==================== CRUD =====================*
  // *===============================================*
  public function storeFishFood(array $data)
  {
    $ok = false;
    $errors = null;
    $fishFood = null;
    $permissionKey = 'create_fish_food';
    $permissionInfo = 'agregar alimento';

    $rules = $this->fishFoodRules();
    $attributes = $this->fishFoddAttributes;

    $userId = session()->get('userId');
    $data['userId'] = $userId;

    if (userHasPermission($permissionKey)) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();
        $fishFood = FishFood::create([
          'user_id' => $userId,
          'name' => $inputs['name'],
          'brand' => $inputs['brand'],
          'stage' => $inputs['stage']
        ]);

        $ok = true;
        $fishFood = $this->createFishFood($fishFood);
        $this->alert('El alimento fue guardado con éxito.', 'success');
        //TODO
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission($permissionInfo);
    }


    return [
      'ok' => $ok,
      'errors' => $errors,
      'fishFood' => $fishFood
    ];
  }

  public function updateFishFood(array $data)
  {
    $ok = false;
    $errors = null;
    $fishFood = null;
    $permissionKey = 'update_fish_food';
    $permissionInfo = 'actualizar alimento';

    $rules = $this->fishFoodRules(true);
    $attributes = $this->fishFoddAttributes;

    $userId = session()->get('userId');
    $data['userId'] = $userId;

    if (userHasPermission($permissionKey)) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();

        //Se recupera la instancia
        $fishFood = FishFood::find($inputs['fishFoodId']);
        //Se actualiza
        $fishFood->name = $inputs['name'];
        $fishFood->brand = $inputs['brand'];
        $fishFood->stage = $inputs['stage'];
        $fishFood->save();

        $ok = true;
        $this->alert('Información Actualizada', 'success');
        $fishFood = $this->createFishFood($fishFood);
        //TODO
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission($permissionInfo);
    }


    return [
      'ok' => $ok,
      'errors' => $errors,
      'fishFood' => $fishFood
    ];
  }

  public function destroyFishFood(int $fishFoodId)
  {
    $ok = false;
    $errors = null;
    $permissionKey = 'delete_fish_food';
    $permissionInfo = 'eliminar alimento';


    if (userHasPermission($permissionKey)) {
      try {
        //Se recupera la instancia
        /** @var FishFood */
        $fishFood = FishFood::find($fishFoodId, ['id', 'name']);

        if ($fishFood) {
          //Se verifica si no se estan usando los recursos con este alimento
          $stocks = $fishFood->stocks()->count();

          //Se conprueba si este alimento tiene raciones
          $rations = $fishFood->rations()->count();
          if ($stocks > 0 || $rations > 0) {
            $this->alert('¡No se puede Eliminar!', 'error', 'Este recurso está siendo utilizado.');
          } else {
            $fishFood->delete();
            $ok = true;
            $this->alert('¡Alimento Eliminado!', 'success');
          }
        } else {
          $this->alert('Alimento no encontrado', 'error');
          $ok = true;
        }
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission($permissionInfo);
    }


    return [
      'ok' => $ok,
      'errors' => $errors
    ];
  }

  public function storeFishFoodStock(array $data)
  {
    $ok = false;
    $errors = null;
    $stock = null;
    $permissionKey = 'create_fish_food_stock';
    $permissionInfo = 'agregar stock';

    $rules = $this->fishFoodStockRules();
    $attributes = $this->fishFoodStockAttributes;

    if (userHasPermission($permissionKey)) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();
        /** @var FishFood */
        $fishFood = FishFood::find($inputs['fishFoodId'], ['id']);
        $quantity = $inputs['quantity'] * 1000;
        $stock = $fishFood->stocks()->create([
          'initial_stock' => $quantity,
          'stock' => $quantity,
          'amount' => $inputs['amount'],
        ]);

        $stock = $this->createStock($stock);
        $ok = true;
        $this->alert('Stock Guardado', 'success');
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission($permissionInfo);
    }


    return [
      'ok' => $ok,
      'errors' => $errors,
      'stock' => $stock
    ];
  }

  public function updateFishFoodStock(array $data)
  {
    $ok = false;
    $errors = null;
    $permissionKey = 'create_fish_food';
    $permissionInfo = 'agregar alimento';

    $rules = null;
    $attributes = null;

    if (userHasPermission($permissionKey)) {
      try {
        $inputs = Validator::make($data, $rules, [], $attributes)->validate();
        //TODO
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission($permissionInfo);
    }


    return [
      'ok' => $ok,
      'errors' => $errors
    ];
  }

  public function destroyFishFoodStock(int $stockId)
  {
    $ok = false;
    $errors = null;
    $permissionKey = 'delete_fish_food_stock';
    $permissionInfo = 'eliminar stock';

    if (userHasPermission($permissionKey)) {
      try {
        //Recupero el stock
        /** @var FishFoodStock */
        $stock = FishFoodStock::find($stockId);

        if($stock){
          //Se verifica que no esté siendo usado en ninguna ración
          $rations = $stock->rations()->count();
          if($rations > 0){
            $this->alert('¡No se puede Eliminar!', 'error', 'Inventario está siendo usado por alguna ración.');
          }else{
            $stock->delete();
            $ok = true;
            $this->alert('¡Inventario Eliminado!', 'success');
          }
        }else{
          $this->alert('Inventario no encontrado', 'error');
          $ok = true;
        }
      } catch (ValidationException $valExc) {
        $errors = $valExc->errors();
      } catch (\Throwable $th) {
        $this->emitError($th);
      }
    } else {
      $this->doesNotPermission($permissionInfo);
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
}
