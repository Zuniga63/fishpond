<?php

namespace App\Http\Livewire\Admin;

use App\Models\FishFood;
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
    $fishFoods = FishFood::orderBy('name')
      ->where('user_id', $userId)
      ->with(['stocks' => function ($query) {
        $query->where('stock', '>', 0);
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
      $stocks[] = [
        'id' => $stock->id,
        'initialStock' => $stock->initial_stock,
        'stock' => $stock->stock,
        'amount' => intval($stock->amount),
        'createdAt' => $stock->created_at,
        'updatedAt' => $stock->updated_at
      ];
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

  // *==========================================================*
  // *================= REGLAS Y VALIDACIONES ==================*
  // *==========================================================*
  protected function fishFoodRules(bool $update = false)
  {
    //TODO
  }

  protected $fishFoddAttributes = [];

  protected function fishFoodStockRules(bool $inThisMoment, bool $setTime, bool $update = false)
  {
    //TODO
  }

  protected $fishFoodStockAttributes = [];

  // *===============================================*
  // *==================== CRUD =====================*
  // *===============================================*
  public function storeFishFood(array $data)
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

  public function updateFishFood(array $data)
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

  public function destroyFishFood(int $fishFoodId)
  {
    $ok = false;
    $errors = null;
    $permissionKey = 'create_fish_food';
    $permissionInfo = 'agregar alimento';

    $rules = null;
    $attributes = null;

    if (userHasPermission($permissionKey)) {
      try {
        // $inputs = Validator::make($data, $rules, [], $attributes)->validate();
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

  public function storeFishFoodStock(array $data)
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
    $permissionKey = 'create_fish_food';
    $permissionInfo = 'agregar alimento';

    $rules = null;
    $attributes = null;

    if (userHasPermission($permissionKey)) {
      try {
        // $inputs = Validator::make($data, $rules, [], $attributes)->validate();
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
