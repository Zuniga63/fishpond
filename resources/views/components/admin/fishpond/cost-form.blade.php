<form 
  class="card w-90 mx-auto my-0" 
  x-bind:class="{
    'card-primary' : mode === 'register',
    'card-info' : mode === 'update',
  }"
  x-data="costForm()" 
  x-init="init($wire, $dispatch, $refs)"
  x-on:hidden-cost-form.window="hidden"
  x-on:edit-cost.window="mountCost($event.detail)"
  x-on:fishpond-selected.window="fishpond = $event.detail"
  x-on:submit.prevent="submit"
>
  <!-- header -->
  <header class="card-header">
    <h5 class="m-0" x-text="title"></h5>
  </header>
  <!--/.end header -->

  <!-- body -->
  <div class="card-body" style="max-height: 75vh; overflow-y: scroll">
    <!-- Tipo de costo -->
    <div class="form-group mb-1">
      <label x-bind:for="costType.id" class="mb-1 required" x-html="costType.label"></label>
      <select 
        x-bind:name="costType.id" 
        id="costType.id" 
        class="form-control" 
        x-bind:class="{'is-invalid': costType.hasError}" 
        x-model="costType.value" 
        x-bind:disabled="waiting"
        x-on:change="validateCostType"   
      >
        <option value="">Elije un tipo de costo</option>
        <option value="materials">Materiales</option>
        <option value="workforce">Mano de obra</option>
        <option value="maintenance">Mantenimiento</option>
      </select>

      <div class="invalid-feedback" role="alert" x-show="costType.hasError" x-text="costType.errorMessage"></div>
    </div>

    <!-- Momento del costo -->
    <div class="form-check">
      <input type="checkbox" name="inThisMoment" id="inThisMoment" class="form-check-input" x-model="inThisMoment" x-bind:disabled="waiting">
      <label for="inThisMoment" class="form-check-label">Registar en este momento</label>
    </div>

    <!-- Selección de la fecha y la hora-->
    <div x-show.transition.duration.300ms="!inThisMoment">
      <!-- Ingreso de la fecha -->
      <div class="form-group">
        <label x-bind:for="date.id" x-text="date.label">Selecciona una fecha</label>
        <div class="input-group mb-2">
          <div class="input-group-prepend">
            <span class="input-group-text">
              <i class="far fa-calendar-alt"></i>
            </span>
          </div>
          <input 
            type="date" 
            x-bind:id="date.id"
            x-bind:name="date.id" 
            class="form-control"
            x-bind:class="{'is-invalid': date.hasError}"
            x-model="date.value"
            x-on:change="validateDate"
            x-bind:max="date.max"
            x-bind:required="!inThisMoment"
            x-bind:disabled="waiting"
          >
          
          <div class="invalid-feedback" role="alert" x-show="date.hasError" x-text="date.errorMessage"></div>
        </div>
      </div>

      <!-- Habilitar o deshabilitar ingreso de hora -->
      <div class="form-check">
        <input type="checkbox" name="setTime" id="setTime" class="form-check-input" x-model="setTime" x-bind:disabled="waiting">
        <label for="setTime" class="form-check-label">Establecer hora</label>
      </div>

      <!-- Hora del registro -->
      <div class="form-group row" x-show.transition="setTime">
        <label class="col-3 col-form-label" x-bind:for="time.id" x-html="time.label"></label>
        <div class="col-9">
          <input 
            type="time" 
            x-bind:name="time.id" 
            x-bind:id="time.id" 
            class="form-control"
            x-bind:class="{'is-invalid': time.hasError}"
            x-model="time.value"
            x-on:change="validateTime"
            x-bind:required="!inThisMoment && setTime"
            x-bind:disabled="waiting"
          >
          
          <div class="invalid-feedback" role="alert" x-show="time.hasError" x-text="time.errorMessage"></div>
        </div>
      </div>


    </div>

    <!-- Descripción -->
    <div class="form-group">
      <label for="description.id" class="required text-center" x-html="description.label"></label>
      <textarea 
        name="description.id" 
        id="description.id" 
        cols="30" 
        class="form-control"
        x-bind:class="{'is-invalid': description.hasError}" 
        x-bind:placeholder="description.placeholder"
        x-model="description.value"
        x-on:focus="$event.target.select()"
        x-bind:disabled="waiting"
      ></textarea>
    
      <div class="invalid-feedback" role="alert" x-show="description.hasError" x-text="description.errorMessage"></div>
    </div>

    <!-- Importe del costo -->
    <div class="form-group">
      <label x-bind:for="amount.id" class="required" x-html="amount.label"></label>
      <input 
        type="text" 
        x-bind:name="amount.id" 
        x-bind:id="amount.id" 
        class="form-control text-right text-bold" 
        x-bind:class="{'is-invalid': amount.hasError}" 
        x-bind:placeholder="amount.placeholder" 
        x-ref="costAmount"
        x-on:input="formatAmount($event.target)"
        x-on:focus="$event.target.select()"
        style="font-size: 1.5em;letter-spacing: 2px;"
        autocomplete="off"
        x-bind:disabled="waiting"
      >
      <div class="invalid-feedback" role="alert" x-show="amount.hasError" x-text="amount.errorMessage"></div>
    </div>
  </div>
  <!--/.end body -->

  <!-- footer -->
  <footer class="card-footer">
    <button 
      class="btn btn-primary"
      type="submit"
      x-bind:class="{
        'btn-primary' : mode === 'register',
        'btn-info': mode === 'update',
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
        <div x-show="mode === 'update'">
          <span x-show="!waiting">Actualizar</span>
          <span x-show="waiting">Actualizando...</span>
        </div>
      </div>
    </button>
    <button class="btn btn-link" type="button" x-on:click="hidden" x-bind:disabled="waiting">
      Cancelar
    </button>
  </footer>
</form>