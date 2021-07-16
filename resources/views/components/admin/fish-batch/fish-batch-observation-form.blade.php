<form 
  x-data="fishBatchObservationForm()" 
  x-init="init($wire, $dispatch, $refs)"
  x-show="visible" 
  x-on:enable-fish-batch-observation-form.window="enableForm($event.detail)"
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
        <span x-show="mode === 'register'" style="display: none">Registrar Observación</span>
        <span x-show="mode === 'updating'" style="display: none">Actualizar Observación</span>
      </h5>
    </header>

    <!-- Body -->
    <div class="card-body">

      {{-- TITULO --}}
      <div class="form-group">
        <label x-bind:for="title.id" class="required" x-html="title.label"></label>
        <input 
          type="text" 
          x-bind:id="title.id" 
          x-bind:name="title.id" 
          class="form-control" 
          x-bind:class="{'is-invalid': title.hasError}" 
          x-bind:placeholder="title.placeholder" 
          x-on:focus="$event.target.select()"
          x-model="title.value"
          x-on:change="validateTitle"    
          autocomplete="off"
          x-bind:disabled="waiting"
        >
        <p class="text-sm text-muted text-right mb-0">Longitud: <span x-text="title.value ? title.value.length : 0"></span></p>
        <div class="invalid-feedback" role="alert" x-show="title.hasError" x-text="title.errorMessage"></div>
      </div>

      <!-- MESSAGE -->
      <div class="form-group">
        <label for="message.id" class="required text-center" x-html="message.label"></label>
        <textarea 
          id="message.id" 
          name="message.name" 
          cols="30"
          rows="5"
          class="form-control"
          x-bind:class="{'is-invalid': message.hasError}" 
          x-bind:placeholder="message.placeholder"
          x-model="message.value"
          x-on:change="validateMessage"    
          x-on:focus="$event.target.select()"
          x-bind:disabled="waiting"
        ></textarea>
        <p class="text-sm text-muted text-right mb-0">Longitud: <span x-text="message.value ? message.value.length : 0"></span></p>
        <div class="invalid-feedback" role="alert" x-show="message.hasError" x-text="message.errorMessage"></div>
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