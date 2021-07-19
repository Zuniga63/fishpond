<form 
  x-data="fishBatchDeathForm()" 
  x-init="init($wire, $dispatch, $refs)"
  x-show="visible" 
  x-on:enable-fish-batch-death-form.window="enableForm($event.detail)"
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
        <span x-show="mode === 'register'" style="display: none">Registrar Muerte</span>
        <span x-show="mode === 'updating'" style="display: none">Actualizar Registro de Muerte</span>
      </h5>
    </header>

    <!-- Body -->
    <div class="card-body">

      {{-- Muertes --}}
      <div class="form-group">
        <label x-bind:for="deaths.id" class="required" x-html="deaths.label"></label>
        <input 
          type="number" 
          x-bind:id="deaths.id" 
          x-bind:name="deaths.id" 
          class="form-control text-center" 
          x-bind:class="{'is-invalid': deaths.hasError}" 
          x-bind:placeholder="deaths.placeholder" 
          x-model.number="deaths.value"
          x-on:focus="$event.target.select()"
          x-on:input="validateDeaths"   
          x-bind:min="deaths.min"
          x-bind:max="deaths.max"
          x-bind:step="deaths.step"
          autocomplete="off"
          x-bind:disabled="waiting"
        >
        <div class="invalid-feedback" role="alert" x-show="deaths.hasError" x-text="deaths.errorMessage"></div>
      </div>
      <p class="m-0">Población actual de peces: <span class="text-bold" x-text="fishBatch?.population"></span></p>
      <p class="m-0" x-show="mortality">Mortalidad: <span class="text-bold" x-text="mortality"></span>%</p>

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