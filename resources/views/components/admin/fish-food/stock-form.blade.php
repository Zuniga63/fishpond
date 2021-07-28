<form 
  x-data="fishFoodStockForm()" 
  x-init="init($wire, $dispatch, $refs)"
  x-show="visible" 
  x-on:enable-stock-form.window="enableForm($event.detail)"
  x-on:fish-food-was-stored.window="addFishFood($event.detail)"
  x-on:fish-food-was-updated.window="updateFishFood($event.detail)"
  x-on:submit.prevent="submit"
  style="display: none;"
>
  <div 
    class="card"
    x-bind:class="{
      'card-primary': mode === 'register',
      'card-info': mode === 'updating',
    }"
  >
    <header class="card-header">
      <h5 class="m-0">
        <span x-show="mode === 'register'" style="display: none">Registrar Stock</span>
        <span x-show="mode === 'updating'" style="display: none">Actualizar Stock</span>
      </h5>
    </header>

    <!-- Body -->
    <div class="card-body">

      {{-- ALIMENTO --}}
      <div class="form-group mb-1">
        <label x-bind:for="fishFoodId.id" class="mb-1 required" x-html="fishFoodId.label"></label>
        <select 
          x-bind:name="fishFoodId.id" 
          id="fishFoodId.id" 
          class="form-control" 
          x-bind:class="{'is-invalid': fishFoodId.hasError}" 
          x-model.number="fishFoodId.value" 
          x-on:change="validateFishFoodId"
          x-bind:disabled="waiting"  
        >
          <option value="null" x-text="fishFoodId.placeholder" selected disabled></option>
          <template x-for="item in fishFoodList" x-bind:key="item.id">
            <option x-bind:value="item.id" x-text="item.name"></option>
          </template>
        </select>
  
        <div class="invalid-feedback" role="alert" x-show="fishFoodId.hasError" x-text="fishFoodId.errorMessage"></div>
      </div>
      
      {{-- CANTIDAD --}}
      <div class="form-group mb-1">
        <label x-bind:for="quantity.id" class="mb-1" x-html="quantity.label"></label>
        <x-admin.form.input 
          x-bind:id="quantity.id" 
          x-bind:class="{'is-invalid': quantity.hasError, 'text-center': true}"
          x-bind:name="quantity.id" 
          x-bind:placeholder="quantity.placeholder" 
          type="number"
          x-bind:min="quantity.min"
          x-bind:max="quantity.max"
          x-bind:step="quantity.step"
          x-bind:disabled="waiting"
          x-model.number="quantity.value"
          x-on:change="validateQuantity"        
        />

        <div class="invalid-feedback" role="alert" x-show="quantity.hasError" x-text="quantity.errorMessage"></div>
      </div>

      {{-- IMPORTE --}}
      <div class="form-group">
        <label x-bind:for="amount.id" class="required" x-html="amount.label"></label>
        <input 
          type="text" 
          x-bind:name="amount.id" 
          x-bind:id="amount.id" 
          class="form-control text-right text-bold" 
          x-bind:class="{'is-invalid': amount.hasError}" 
          x-bind:placeholder="amount.placeholder" 
          x-ref="amount"
          x-on:input="formatAmount"
          x-on:focus="$event.target.select()"
          x-on:blur="validateAmount"
          style="font-size: 1.4em;letter-spacing: 2px;"
          autocomplete="off"
          x-bind:disabled="waiting"
        >
        <div class="invalid-feedback" role="alert" x-show="amount.hasError" x-text="amount.errorMessage"></div>
      </div>

    </div>
    <!--/.end body -->

    <footer class="card-footer">
      <button 
        class="btn"
        type="submit"
        x-bind:class="{
          'btn-primary' : mode === 'register',
          'btn-info': mode === 'updating',
        }" 
      >
        <div class="d-flex">
          <div class="mr-2" x-show="waiting">
            <div class="spinner-border" role="status" style="width: 1rem;height: 1rem;">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
          <div x-show="mode === 'register'">
            <span x-show="!waiting">Registrar</span>
            <span x-show="waiting">Registrando...</span>
          </div>
          <div x-show="mode === 'updating'">
            <span x-show="!waiting">Actualizar</span>
            <span x-show="waiting">Actualizando...</span>
          </div>
        </div>
      </button>
      <button class="btn btn-link" type="button" x-on:click="cancel">
        Cancelar
      </button>
    </footer>
  </div>
</form>