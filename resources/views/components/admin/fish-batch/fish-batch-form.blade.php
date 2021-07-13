<form 
  x-data="fishBatchForm()" 
  x-init="init($wire, $dispatch, $refs)"
  x-show="visible" 
  x-on:enable-fish-batch-form.window="enableForm($event.detail.mode, $event.detail.fishBatch)"
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
        <span x-show="mode === 'register'" style="display: none">Registrar Lote</span>
        <span x-show="mode === 'updating'" style="display: none">Actualizar Lote</span>
      </h5>
    </header>

    <!-- Body -->
    <div class="card-body">
      {{-- SELECCIÓN DEL ESTANQUE --}}
      <div class="form-group mb-1">
        <label x-bind:for="fishpondId.id" class="mb-1 required" x-html="fishpondId.label"></label>
        <select 
          x-bind:name="fishpondId.id" 
          id="fishpondId.id" 
          class="form-control" 
          x-bind:class="{'is-invalid': fishpondId.hasError}" 
          x-model.number="fishpondId.value" 
          x-bind:disabled="waiting"
          x-on:change="validateFishpond"   
        >
          <option value="null" x-text="fishpondId.placeholder" selected disabled></option>
          <template x-for="item in fishponds" x-bind:key="item.id">
            <option x-bind:value="item.id" x-text="item.name"></option>
          </template>
        </select>
  
        {{-- <div class="invalid-feedback" role="alert" x-show="type.hasError" x-text="type.errorMessage"></div> --}}
      </div>

      <!-- Momento del costo -->
      <div class="form-check mb-2">
        <input type="checkbox" name="fishBatchInThisMoment" id="fishBatchInThisMoment" class="form-check-input" x-model="inThisMoment" x-bind:disabled="waiting">
        <label for="fishBatchInThisMoment" class="form-check-label">Sembrado justo ahora</label>
      </div>

      <!-- Selección de la fecha y la hora-->
      <div x-show.transition.duration.300ms="!inThisMoment" style="display: none">
        <!-- Ingreso de la fecha -->
        <div class="form-group mb-1">
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
          <input type="checkbox" name="setTime" id="fishBatchSetTime" class="form-check-input" x-model="setTime" x-bind:disabled="waiting">
          <label for="fishBatchSetTime" class="form-check-label">Establecer hora</label>
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

      {{-- INGRESO DE LA POBLACIÓN INICIAL --}}
      <div class="form-group mb-1">
        <label x-bind:for="population.id" class="mb-1" x-html="population.label"></label>
        <x-admin.form.input 
          x-bind:id="population.id" 
          x-bind:class="{'is-invalid': population.hasError, 'text-center': true}"
          x-bind:name="population.id" 
          x-bind:placeholder="population.placeholder" 
          type="number"
          x-bind:min="population.min"
          x-bind:max="population.max"
          x-bind:step="population.step"
          x-bind:disabled="waiting"
          x-model.number="population.value"
          x-on:change="validatePopulation"        
        />

        <div class="invalid-feedback" role="alert" x-show="population.hasError" x-text="population.errorMessage"></div>
      </div>

      {{-- INGRESO DEL PESO INCIAL --}}
      <div class="form-group mb-1">
        <label x-bind:for="averageWeight.id" class="mb-1" x-html="averageWeight.label"></label>
        <x-admin.form.input 
          x-bind:id="averageWeight.id" 
          x-bind:class="{'is-invalid': averageWeight.hasError, 'text-center': true}"
          x-bind:name="averageWeight.id" 
          x-bind:placeholder="averageWeight.placeholder" 
          type="number"
          x-bind:min="averageWeight.min"
          x-bind:max="averageWeight.max"
          x-bind:step="averageWeight.step"
          x-bind:disabled="waiting"
          x-model.number="averageWeight.value"
          x-on:change="validateAverageWeight"        
        />

        <div class="invalid-feedback" role="alert" x-show="averageWeight.hasError" x-text="averageWeight.errorMessage"></div>
      </div>

      {{-- CALCULO DE LA BIOMASA --}}
      <div x-show.transition.duration.300ms="biomass" style="display: none">
        <p class="mb-2">Biomasa: <span x-text="biomass" class="text-bold"></span></p>
      </div>

      {{-- COSTE DEL LOTE --}}
      <div class="form-group">
        <label x-bind:for="amount.id" class="required" x-html="amount.label"></label>
        <input 
          type="text" 
          x-bind:name="amount.id" 
          x-bind:id="amount.id" 
          class="form-control text-right text-bold" 
          x-bind:class="{'is-invalid': amount.hasError}" 
          x-bind:placeholder="amount.placeholder" 
          x-ref="fishBatchAmount"
          x-on:input="formatAmount($event.target)"
          x-on:focus="$event.target.select()"
          style="font-size: 1.4em;letter-spacing: 2px;"
          autocomplete="off"
          x-bind:disabled="waiting"
        >
        <div class="invalid-feedback" role="alert" x-show="amount.hasError" x-text="amount.errorMessage"></div>
      </div>

      {{-- COSTO UNITARIO DE LOS ALEVINOS --}}
      <div x-show.transition.duration.300ms="unitCost" style="display: none">
        <p class="mb-2">Costo unitario: <span x-text="unitCost" class="text-bold"></span></p>
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