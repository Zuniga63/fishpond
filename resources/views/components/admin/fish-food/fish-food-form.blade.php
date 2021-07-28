<form 
  x-data="fishFoodForm()" 
  x-init="init($wire, $dispatch, $refs)"
  x-show="visible" 
  x-on:enable-fish-food-form.window="enableForm($event.detail)"
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
        <span x-show="mode === 'register'" style="display: none">Registrar Alimento</span>
        <span x-show="mode === 'updating'" style="display: none">Actualizar Alimento</span>
      </h5>
    </header>

    <!-- Body -->
    <div class="card-body">
      {{-- NOMBRE --}}
      <div class="form-group mb-1">
        <label x-bind:for="name.id" class="required" x-html="name.label"></label>
        <input 
          type="text" 
          x-bind:id="name.id" 
          x-bind:name="name.id" 
          class="form-control" 
          x-bind:class="{'is-invalid': name.hasError}" 
          x-bind:placeholder="name.placeholder" 
          x-on:focus="$event.target.select()"
          x-model="name.value"
          x-on:change="validateName"    
          autocomplete="off"
          x-bind:disabled="waiting"
        >
        <p class="text-sm text-muted text-right mb-0">Longitud: <span x-text="name.value ? name.value.length : 0"></span></p>
        <div class="invalid-feedback" role="alert" x-show="name.hasError" x-text="name.errorMessage"></div>
      </div>

      {{-- ETAPA --}}
      <div class="form-group mb-1">
        <label x-bind:for="stage.id" class="mb-1 required" x-html="stage.label"></label>
        <select 
          x-bind:name="stage.id" 
          id="stage.id" 
          class="form-control" 
          x-bind:class="{'is-invalid': stage.hasError}" 
          x-model.number="stage.value" 
          x-bind:disabled="waiting"
          x-on:change="validateStage"   
        >
          <option value="null" x-text="stage.placeholder" selected disabled></option>
          <template x-for="([key, value], index) in Object.entries(stages)" x-bind:key="index">
            <option x-bind:value="key" x-text="value"></option>
          </template>
        </select>
  
        <div class="invalid-feedback" role="alert" x-show="stage.hasError" x-text="stage.errorMessage"></div>
      </div>

      {{-- MARCA --}}
      <div class="form-group mb-1">
        <label x-bind:for="brand.id" class="required" x-html="brand.label"></label>
        <input 
          type="text" 
          x-bind:id="brand.id" 
          x-bind:name="brand.id" 
          class="form-control" 
          x-bind:class="{'is-invalid': brand.hasError}" 
          x-bind:placeholder="brand.placeholder" 
          x-on:focus="$event.target.select()"
          x-model="brand.value"
          x-on:change="validateBrand"    
          autocomplete="off"
          x-bind:disabled="waiting"
        >
        <p class="text-sm text-muted text-right mb-0">Longitud: <span x-text="brand.value ? brand.value.length : 0"></span></p>
        <div class="invalid-feedback" role="alert" x-show="brand.hasError" x-text="brand.errorMessage"></div>
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