<div 
  x-data="fishBatchComponent()"
  x-init="init($wire, $dispatch)"
  x-on:fish-batch-selected.window="mountFishBatch($event.detail)"
  x-on:observation-was-added.window="refresh"
  x-on:observation-was-updated.window="refresh"
>
  <template x-if="fishBatch">
    <div class="pb-5">
      {{-- INFORMACIÓN DEL LOTE --}}
      <div class="card card-dark">
        <header class="card-header p-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="text-left p-0 m-0" x-text="fishpond.name"></h5>
              <p class="mb-0 text-sm">Información del lote</p>
            </div>
            <div>
              <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="$dispatch('back-to-home')"><i class="fas fa-times"></i></a>
            </div>
          </div>
        </header>
      
        <div class="card-body p-2">
          {{-- SIEMBRA Y COSECHA --}}
          <div class="border-bottom pb-2">
            <p class="m-0">
              Siembra: 
              <span class="text-bold" x-text="fishBatch.seedtime.format('DD-MM-YYYY hh:mm a')"></span>
              <span class="text-sm text-muted">(<span x-text="fishBatch.seedtime.fromNow()"></span>)</span>
            </p>
            <div x-show="fishBatch.harvest">
              <p class="m-0">
                cosecha: 
                <span class="text-bold" x-text="fishBatch.harvest?.format('DD-MM-YYYY hh:mm a')"></span>
                <span class="text-sm text-muted">(<span x-text="fishBatch.harvest?.fromNow()"></span>)</span>
              </p>
              <p class="m-0">
                Edad al momento de cosecha: 
                <span class="text-bold" x-text="fishBatch.harvest?.to(fishBatch.seedtime, true)"></span>
              </p>
            </div>

          </div>
          {{-- ESTANQUE Y POLBACIÓN --}}
          <div class="row border-bottom">
            {{-- INFORMACIÓN DEL ESTANQUE --}}
            <div class="col-6 border-right">
              <h6 class="text-center border-bottom">Estanque</h6>
              {{-- TIPO --}}
              <p class="m-0"><span class="text-bold">Tipo</span>: <span x-text="fishpond?.type"></span></p>
              {{-- CAPACIDAD --}}
              <p class="m-0" x-show="fishpond?.capacity">
                <span class="text-bold">Capacidad</span>: 
                <span x-text="fishpond?.capacity"></span> <i class="fas fa-fish"></i>
              </p>
              {{-- AREA --}}
              <p class="m-0" x-show="fishpond?.area">
                <span class="text-bold">Area</span>: 
                <span x-text="fishpond?.area"></span> m<sup>2</sup>
              </p>
              {{-- VOLUMEN --}}
              <p class="m-0" x-show="fishpond?.volume">
                <span class="text-bold">Volumen</span>: 
                <span x-text="fishpond?.volume"></span> m<sup>3</sup>
              </p>        
            </div>
            <!--/.end col-->
            {{-- POBLACIÓN DEL ESTANQUE --}}
            <div class="col-6">
              <h6 class="text-center border-bottom">Población</h6>
              {{-- INICIAL --}}
              <p class="m-0">
                <span class="text-bold">Inicial</span>: 
                <span x-bind:class="{'text-warning': initialPopulationWarning}">
                  <span x-text="fishBatch.initialPopulation"></span> 
                  <i class="fas fa-fish"></i>
                </span>
              </p>
              {{-- ACTUAL --}}
              <p class="m-0">
                <span class="text-bold">Actual</span>: 
                <span x-bind:class="{'text-warning': populationWarning}">
                  <span x-text="fishBatch.population"></span> 
                  <i class="fas fa-fish"></i>
                </span>
              </p>
              {{-- DENSIDAD POR AREA --}}
              <p class="m-0" x-show="fishpond?.densityByArea">
                <span class="text-bold"><i class="fas fa-fish"></i>/m<sup>2</sup></span>: 
                <span x-text="fishpond?.densityByArea"></span>
              </p>
              {{-- DENSIDAD POR VOLUMEN --}}
              <p class="m-0" x-show="fishpond?.densityByVolume">
                <span class="text-bold"><i class="fas fa-fish"></i>/m<sup>3</sup></span>: 
                <span x-text="fishpond?.densityByVolume"></span>
              </p>
            </div>
            <!--/.end col-->
          </div>
          <!--/.end row -->
      
          {{-- INFORMACIÓN DE CREACIÓN Y ACTUALIZACIÓN --}}
          <div>
            <p class="mb-0 text-sm"><span class="text-bold">Registro</span>: <span x-text="fishBatch.createdAt.fromNow()"></span></p>
            <p class="mb-0 text-sm" x-show="!fishBatch.updatedAt.isSame(fishBatch.createdAt)">
              <span class="text-bold">Actualización</span>: <span x-text="fishBatch.updatedAt.fromNow()"></span>
            </p>
          </div>
        </div>
      </div>

      {{-- OBSERVACIONES, GASTOS, METRICAS Y MUERTES --}}
      <x-cards.card-with-tabs>
        <x-slot name="tabs">
          <!-- Muestra las observaciones -->
          <x-cards.tab 
            x-bind:class="{active : tab === 'observations'}" 
            href="javascript:;" 
            title="Observaciones"
            x-on:click="tab = 'observations'"
          >
            <i class="fas fa-book"></i>
          </x-cards.nav-tab>
      
          <!-- Muestra los lotes que fueron cosechados -->
          <x-cards.tab 
            x-bind:class="{active : tab === 'expenses'}" 
            href="javascript:;"
            title="Gastos"
            x-on:click="tab = 'expenses'"
          >
            <i class="fas fa-wallet"></i>
          </x-cards.nav-tab>
          <x-cards.tab 
            x-bind:class="{active : tab === 'biometries'}" 
            href="javascript:;"
            title="Biometrías"
            x-on:click="tab = 'biometries'"
          >
            <i class="fas fa-pencil-ruler"></i>
          </x-cards.nav-tab>
          <x-cards.tab 
            x-bind:class="{active : tab === 'deaths'}" 
            href="javascript:;"
            title="Muertes"
            x-on:click="tab = 'deaths'"
          >
            <i class="fas fa-skull-crossbones"></i>
          </x-cards.nav-tab>
        </x-slot>

        {{-- OBSERVACIONES --}}
        <div x-show.transition.in.durations.300ms="tab === 'observations'">
          <template x-for="observation in observations" x-bind:key="observation.id">
            <div class="card card-light">
              <header class="card-header p-2">
                <div class="d-flex justify-content-between items-center">
                  <!-- Fecha y Tiempo relativo -->
                  <div class="d-flex flex-column">
                    <h6 class="m-0 text-bold" x-text="observation.title"></h6>
                    <p class="m-0 text-muted" x-text="observation.createdAt.format('dddd DD/MM/YYYY hh:mm a')"></p>
                  </div>
                  <!-- Controles -->
                  <div class="">
                    <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click="updateObservation(observation)"><i class="fas fa-edit"></i></a>
                    <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="destroyObservation(observation)"><i class="fas fa-trash"></i></a>
                  </div>
                  <!--/.end controles -->
                </div>
                <!--/end flex -->
              </header>
    
              <div class="card-body p-2">
                <!-- Descripción -->
                <p class="m-1" x-text="observation.message"></p>
                <!-- Auditoría -->
                <div class="d-flex flex-column">
                  <p class="m-0 text-sm text-muted">
                    Creado: <span x-text="observation.createdAt.fromNow()"></span>
                  </p>
                  <p class="m-0 text-sm text-muted" x-show="!observation.createIsSameUpdate">
                    Actualizado: <span x-text="observation.updatedAt.fromNow()"></span>
                  </p>
                </div>
                <!--/.edn auditoría -->
              </div>
              <!--/.end body -->
            </div>
          </template>
        </div>
      
      </x-cards.card-with-nav-tabs>

      {{-- BOTON PARA HABILITAR FORMULARIO DE NUEVO LOTE --}}
      <button 
        class="btn btn-primary rounded-circle fixed-buttom z-fixed shadow"
        x-on:click="enableForm()"
      >
        <i class="fas fa-plus"></i>
      </button>
    </div>
  </template>
</div>