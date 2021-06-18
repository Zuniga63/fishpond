<form 
  class="card w-90 mx-auto my-0" 
  x-bind:class="{
    'card-primary' : register,
    'card-info' : updating,
  }"
  x-data="registerForm()" 
  x-init="init($wire, $dispatch)"
  x-on:hidden-register-form.window="hidden"
  x-on:edit-fishpond.window="mountFishpond($event.detail)"
  x-on:submit.prevent="submit"
>
  <!-- header -->
  <header class="card-header">
    <h5 class="m-0" x-text="title"></h5>
  </header>
  <!--/.end header -->

  <!-- body -->
  <div class="card-body" style="max-height: 70vh; overflow-y: scroll">
    <!-- Nombre del estanque -->
    <div class="form-group mb-1">
      <label x-bind:for="name.id" class="mb-1 required" x-html="name.label"></label>
      <x-admin.form.input 
        x-bind:id="name.id" 
        x-bind:class="{'is-invalid': name.hasError}"
        x-bind:name="name.id" 
        x-bind:placeholder="name.placeholder" 
        x-bind:disabled="name.disabled"
        x-model="name.value"
        x-on:change="validateInput(name.name)"        
      />

      <div class="invalid-feedback" role="alert" x-show="name.hasError" x-text="name.errorMessage"></div>
    </div>

    <!-- CAPACIDAD DEL ESTANQUE -->
    <div class="form-group mb-1">
      <label x-bind:for="capacity.id" class="mb-1" x-html="capacity.label"></label>
      <x-admin.form.input 
        x-bind:id="capacity.id" 
        x-bind:class="{'is-invalid': capacity.hasError}"
        x-bind:name="capacity.id" 
        x-bind:placeholder="capacity.placeholder" 
        type="number"
        x-bind:min="capacity.min"
        x-bind:max="capacity.max"
        x-bind:step="capacity.step"
        x-bind:disabled="capacity.disabled"
        x-model.number="capacity.value"
        x-on:change="validateInput(capacity.name)"        
      />

      <div class="invalid-feedback" role="alert" x-show="capacity.hasError" x-text="capacity.errorMessage"></div>
    </div>

    <!-- Tipo de estanque -->
    <div class="form-group mb-1">
      <label x-bind:for="type.id" class="mb-1 required" x-html="type.label"></label>
      <select 
        x-bind:name="type.id" 
        id="type.id" 
        class="form-control" 
        x-bind:class="{'is-invalid': type.hasError}" 
        x-model="type.value" 
        x-bind:disabled="name.disabled"
        x-on:change="validateInput(type.name)"   
      >
        <option value="circular">Circular</option>
        <option value="rectangular">Rectangular</option>
      </select>

      <div class="invalid-feedback" role="alert" x-show="type.hasError" x-text="type.errorMessage"></div>
    </div>

    <!-- PARAMETROS DEL ESTANQUE CIRCULAR -->
    <div x-show.transition.in.duration.300ms="type.value === 'circular'">
      <!-- Diametro del estanque -->
      <div class="form-group mb-1">
        <label x-bind:for="diameter.id" class="mb-1" x-html="diameter.label"></label>
        <x-admin.form.input 
          x-bind:id="diameter.id" 
          x-bind:class="{'is-invalid': diameter.hasError}"
          x-bind:name="diameter.id" 
          x-bind:placeholder="diameter.placeholder" 
          type="number"
          x-bind:min="diameter.min"
          x-bind:max="diameter.max"
          x-bind:step="diameter.step"
          x-bind:disabled="diameter.disabled"
          x-model.number="diameter.value"
          x-on:change="validateInput(diameter.name)"        
        />

        <div class="invalid-feedback" role="alert" x-show="diameter.hasError" x-text="diameter.errorMessage"></div>
      </div>
    </div>
    <!--/.end PARAMETROS DEL ESTANQUE CIRCULAR -->

    <!-- PARAMETROS DEL ESTANQUE RECTANGULAR -->
    <div x-show.transition.in.duration.300ms="type.value === 'rectangular'">
      <div class="row">
        <!-- ANCHO DEL ESTANQUE -->
        <div class="col-6">
          <div class="form-group mb-1">
            <label x-bind:for="width.id" class="mb-1" x-html="width.label"></label>
            <x-admin.form.input 
              x-bind:id="width.id" 
              x-bind:class="{'is-invalid': width.hasError, 'text-center': true}"
              x-bind:name="width.id" 
              x-bind:placeholder="width.placeholder" 
              type="number"
              x-bind:min="width.min"
              x-bind:max="width.max"
              x-bind:step="width.step"
              x-bind:disabled="width.disabled"
              x-model.number="width.value"
              x-on:change="validateInput(width.name)"        
            />
    
            <div class="invalid-feedback" role="alert" x-show="width.hasError" x-text="width.errorMessage"></div>
          </div>
        </div>

        <!-- LONGITUD DEL ESTANQUE -->
        <div class="col-6">
          <div class="form-group mb-1">
            <label x-bind:for="long.id" class="mb-1" x-html="long.label"></label>
            <x-admin.form.input 
              x-bind:id="long.id" 
              x-bind:class="{'is-invalid': long.hasError, 'text-center': true}"
              x-bind:name="long.id" 
              x-bind:placeholder="long.placeholder" 
              type="number"
              x-bind:min="long.min"
              x-bind:max="long.max"
              x-bind:step="long.step"
              x-bind:disabled="long.disabled"
              x-model.number="long.value"
              x-on:change="validateInput(long.name)"        
            />
    
            <div class="invalid-feedback" role="alert" x-show="long.hasError" x-text="long.errorMessage"></div>
          </div>
        </div>
      </div>
      <!--/.end row -->
    </div>
    <!--/.end PARAMETROS DEL ESTANQUE RECTANGULAR -->

    <!-- PROFUNDIDAD DEL ESTANQUE -->
    <p class="text-bold mb-1 mt-2 text-center">Profundiad</p>
    <div class="row">
      <!-- PROFUNDIDAD EFECTIVA DEL ESTANQUE -->
      <div class="col-6">
        <div class="form-group mb-1">
          <label x-bind:for="effectiveHeight.id" class="mb-1" x-html="effectiveHeight.label"></label>
          <x-admin.form.input 
            x-bind:id="effectiveHeight.id" 
            x-bind:class="{'is-invalid': effectiveHeight.hasError, 'text-center': true}"
            x-bind:name="effectiveHeight.id" 
            x-bind:placeholder="effectiveHeight.placeholder" 
            type="number"
            x-bind:min="effectiveHeight.min"
            x-bind:max="effectiveHeight.max"
            x-bind:step="effectiveHeight.step"
            x-bind:disabled="effectiveHeight.disabled"
            x-model.number="effectiveHeight.value"
            x-on:change="validateInput(effectiveHeight.name)"        
          />
  
          <div class="invalid-feedback" role="alert" x-show="effectiveHeight.hasError" x-text="effectiveHeight.errorMessage"></div>
        </div>
      </div>
      
      <!-- PROFUNDIAD MAXIMA -->
      <div class="col-6">
        <div class="form-group mb-1">
          <label x-bind:for="maxHeight.id" class="mb-1" x-html="maxHeight.label"></label>
          <x-admin.form.input 
            x-bind:id="maxHeight.id" 
            x-bind:class="{'is-invalid': maxHeight.hasError, 'text-center': true}"
            x-bind:name="maxHeight.id" 
            x-bind:placeholder="maxHeight.placeholder" 
            type="number"
            x-bind:min="maxHeight.min"
            x-bind:max="maxHeight.max"
            x-bind:step="maxHeight.step"
            x-bind:disabled="maxHeight.disabled"
            x-model.number="maxHeight.value"
            x-on:change="validateInput(maxHeight.name)"        
          />
  
          <div class="invalid-feedback" role="alert" x-show="maxHeight.hasError" x-text="maxHeight.errorMessage"></div>
        </div>
      </div>

      <div class="col-12 alert alert-danger text-sm mb-0 mt-2 p-1" role="alert" x-show.transition.duration.300ms="errorInDepth">
        La profundiad maxima es inferior a la efectiva.
      </div>
    </div>

  </div>
  <!--/.end body -->

  <!-- footer -->
  <footer class="card-footer">
    <button 
      class="btn btn-primary"
      type="submit"
      x-bind:class="{
        'btn-primary' : register,
        'btn-info': updating,
      }" 
    >
      <div class="d-flex">
        <div class="mr-2" x-show="waiting">
          <div class="spinner-border" role="status" style="width: 1rem;height: 1rem;">
            <span class="sr-only">Loading...</span>
          </div>
        </div>
        <div x-show="register">
          <span x-show="!waiting">Registrar</span>
          <span x-show="waiting">Registrando...</span>
        </div>
        <div x-show="updating">
          <span x-show="!waiting">Actualizar</span>
          <span x-show="waiting">Actualizando...</span>
        </div>
      </div>
    </button>
    <button class="btn btn-link" type="button" x-on:click="hidden">
      Cancelar
    </button>
  </footer>
</form>