<form 
  x-data="fishBatchExpenseForm()" 
  x-init="init($wire, $dispatch, $refs)"
  x-show="visible" 
  x-on:enable-fish-batch-expense-form.window="enableForm($event.detail)"
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
        <span x-show="mode === 'register'" style="display: none">Registrar Gasto</span>
        <span x-show="mode === 'updating'" style="display: none">Actualizar Gasto</span>
      </h5>
    </header>

    <!-- Body -->
    <div class="card-body">

      {{-- MOMENTO DEL GASTO --}}
      <div class="form-check mb-2">
        <input type="checkbox" name="expenseInThisMoment" id="expenseInThisMoment" class="form-check-input" x-model="inThisMoment" x-bind:disabled="waiting">
        <label for="expenseInThisMoment" class="form-check-label">Gasto generado justo ahora.</label>
      </div>

      {{-- FECHA DEL GASTO --}}
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
              x-bind:min="date.min.format('YYYY-MM-DD')"
              x-bind:max="date.max.format('YYYY-MM-DD')"
              x-bind:required="!inThisMoment"
              x-bind:disabled="waiting"
            >
            
            <div class="invalid-feedback" role="alert" x-show="date.hasError" x-text="date.errorMessage"></div>
          </div>
        </div>

        <!-- Habilitar o deshabilitar ingreso de hora -->
        <div class="form-check">
          <input type="checkbox" name="setTime" id="expensesSetTime" class="form-check-input" x-model="setTime" x-bind:disabled="waiting">
          <label for="expensesSetTime" class="form-check-label">Establecer hora</label>
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
      <!--/.end DATE -->

      {{-- DESCRIPCIÓN --}}
      <div class="form-group">
        <label for="description.id" class="required text-center" x-html="description.label"></label>
        <textarea 
          id="description.id" 
          name="description.name" 
          cols="30"
          rows="5"
          class="form-control"
          x-bind:class="{'is-invalid': description.hasError}" 
          x-bind:placeholder="description.placeholder"
          x-model="description.value"
          x-on:blur="validateDescription"    
          x-on:focus="$event.target.select()"
          x-bind:disabled="waiting"
        ></textarea>
        <p class="text-sm text-muted text-right mb-0">Longitud: <span x-text="description.value ? description.value.length : 0"></span></p>
        <div class="invalid-feedback" role="alert" x-show="description.hasError" x-text="description.errorMessage"></div>
      </div>

      {{-- IMPORTE DEL GASTO --}}
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