<div x-show.transition.in.duration.300ms="tab === 'foods'">
  <template x-for="(food, index) in fishFoodList" x-bind:key="food.id">
    <div class="card card-light">
      {{-- HEADER --}}
      <header class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          {{-- NOMBRE Y MARCA --}}
          <div>
            <h5 class="text-left p-0 m-0" x-text="food.name"></h5>
            <p class="mb-0 text-sm text-muted">
              <span x-text="food.brand"></span> - <span x-text="stages[food.stage]"></span>
            </p>
          </div>
          {{-- CONTROLES --}}
          <div class="">
            <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click="editFood(food)"><i class="fas fa-edit"></i></a>
            <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="destroyFishFood(food.id, index)"><i class="fas fa-trash"></i></a>
          </div>
        </div>
      </header>

      {{-- BODY --}}
      <div class="card-body p-2">
        {{-- INVENTARIOS --}}
        <p class="mb-0">
          Inventarios: <span class="text-bold" x-text="food.stocks.length">
        </p>
        {{-- EXISTENCIAS --}}
        <p class="mb-0">
          Existencias: <span class="text-bold" x-text="round(food.stock.quantity, 2)"></span> <span class="text-bold" x-text="food.stock.unit"></span>
        </p>
        {{-- EXISTENCIAS --}}
        <p class="mb-0">
          Importe: 
          <span class="text-bold" x-text="formatCurrency(food.stock.getAmount(), 0)"></span>
          <span class="text-muted">
            [ <span x-text="formatCurrency(food.stock.getUnitValue().value, 0)"></span>  
              <span x-text="food.stock.getUnitValue().unit"></span>
            ]
          </span>
        </p>
      </div>
    </div>
  </template>
</div>