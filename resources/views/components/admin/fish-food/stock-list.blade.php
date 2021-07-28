<div x-show.transition.in.duration.300ms="tab === 'inventory'" style="display: none">
  <div class="form-group">
    <select name="" id="selectFoodInput" class="form-control" x-model.number="fishFoodId" x-on:change="fishFoodChange">
      <option x-bind:value="null" disabled>Selecciona un concentrado</option>
      <template x-for="food in fishFoodList" x-bind:key="food.id">
        <option x-bind:value="food.id">
          <span x-text="food.name"></span> - 
          <span x-text="stages[food.stage]"></span> -
          <span x-text="round(food.stock.quantity, 2)"></span> <span x-text="food.stock.unit"></span>
        </option>
      </template>
    </select>
  </div>

  <template x-if="fishFoodId">
    <div>
      <table class="table table-sm table-striped table-bordered mb-2">
        <thead class="thead-dark">
          <tr>
            <th scope="col" class="text-center">Inicial</th>
            <th scope="col" class="text-center">Actual</th>
            <th scope="col" class="text-center">Importe</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <template x-for="stock in stocks" x-bind:key="stock.id">
            <tr>
              <th scope="row" class="text-center">
                <span x-text="stock.initialStock.quantity"></span>
                <span x-text="stock.initialStock.unit"></span>
              </th>
              <th class="text-center">
                <span x-text="stock.stock.quantity"></span>
                <span x-text="stock.stock.unit"></span>
              </th>
              <th class="text-center" x-text="formatCurrency(stock.stock.getAmount(), 0)"></th>
              <th class="text-center">
                <button type="button" class="btn btn-danger btn-sm" x-on:click="destroyFishFoodStock(stock.id)"><i class="fas fa-trash"></i></button>
              </th>
            </tr>
          </template>
      </table>
    </div>
  </template>
</div>