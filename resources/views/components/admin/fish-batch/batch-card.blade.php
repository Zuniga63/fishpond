<div 
  x-data="fishBatchComponent()"
  x-init="init($wire, $dispatch)"
  x-on:fish-batch-selected.window="mountFishBatch($event.detail)"
  x-on:observation-was-added.window="refresh"
  x-on:observation-was-updated.window="refresh"
  x-on:expense-was-added.window="refresh"
  x-on:expense-was-removed.window="refresh"
  x-on:fish-batch-was-updated.window="refresh"
>
  <template x-if="fishBatch">
    <div class="pb-5">
      {{-- INFORMACIÓN DEL LOTE --}}
      <div class="card card-dark">
        <header class="card-header p-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="text-left p-0 m-0" x-text="fishpond.name"></h5>
              <p class="mb-0 text-sm">Lote N°: <span x-text="fishBatch.id"></span></p>
            </div>
            <div>
              <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="$dispatch('back-to-home')"><i class="fas fa-times"></i></a>
            </div>
          </div>
        </header>
      
        <div class="card-body p-0" style="margin-top: -5px">
          {{-- OBSERVACIONES, GASTOS, METRICAS Y MUERTES --}}
          <x-cards.card-with-tabs class="m-0" margin-header="-5px">
            <x-slot name="tabs">
              <!-- INFO -->
              <x-cards.tab 
                x-bind:class="{active : tab === 'info'}" 
                href="javascript:;" 
                title="Información"
                x-on:click="tab = 'info'"
              >
                <i class="fas fa-info"></i>
              </x-cards.nav-tab>
              
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

            {{-- INFORMACIÓN --}}
            <div x-show.transition.in.durations.300ms="tab === 'info'">
              {{-- SIEMBRA Y COSECHA --}}
              <div class="border-bottom pb-2">
                {{-- SIEMBRA --}}
                <p class="m-0">
                  Siembra: 
                  <span class="text-bold" x-text="fishBatch.seedtime.format('DD-MM-YYYY hh:mm a')"></span>
                  <span class="text-sm text-muted">(<span x-text="fishBatch.seedtime.fromNow()"></span>)</span>
                </p>
                {{-- COSECHA --}}
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
                {{-- POBLACIÓN INICIAL --}}
                <p class="m-0">
                  Población Inicial: 
                  <span x-text="fishBatch.initialPopulation" class="text-bold"></span>
                  <i class="fas fa-fish"></i>
                </p>
                {{-- BIOMASA INICIAL --}}
                <p class="m-0">
                  Biomasa Inicial: 
                  <span x-text="fishBatch.initialBiomass.value" class="text-bold">
                  </span> <i x-text="fishBatch.initialBiomass.unit"></i>
                  <span class="text-muted">[ <span x-text="fishBatch.initialWeight"></span> g ]</span>
                </p>
              </div>          
              
              {{-- ESTANQUE Y POLBACIÓN --}}
              <div class="row border mb-2">
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
                  {{-- PROFUNDIDAD --}}
                  <p class="m-0" x-show="fishpond?.depth">
                    <span class="text-bold">Profundidad</span>: 
                    <span x-text="fishpond?.depth"></span> m
                  </p>        
                </div>
                <!--/.end col-->
                {{-- POBLACIÓN DEL ESTANQUE --}}
                <div class="col-6">
                  <h6 class="text-center border-bottom">Población</h6>
                  {{-- ACTUAL --}}
                  <p class="m-0">
                    <span class="text-bold">Actual</span>: 
                    <span x-bind:class="{'text-warning': populationWarning}">
                      <span x-text="fishBatch.population"></span> 
                      <i class="fas fa-fish"></i>
                    </span>
                  </p>

                  {{-- BIOMASA --}}
                  <p class="m-0">
                    <span class="text-bold">Biomasa</span>: 
                    <span x-text="fishBatch.biomass.value">
                    </span> <i x-text="fishBatch.biomass.unit"></i>
                  </p>

                  {{-- PESO PROMEDIO --}}
                  <p class="m-0">
                    <span class="text-bold">Peso Prom.</span>: 
                    <span x-text="round(fishBatch.averageWeight, 2)"></span> g.
                  </p>

                  {{-- DENSIDADES --}}
                  <p class="mb-0 text-center">Densidad</p>
                  <div class="d-flex justify-content-between">
                    {{-- DENSIDAD POR AREA --}}
                    <p class="m-0" x-show="fishpond?.densityByArea">
                      <span x-text="fishpond?.densityByArea"></span>
                      <span class="text-bold"><i class="fas fa-fish"></i>/m<sup>2</sup></span> 
                    </p>
                    
                    {{-- DENSIDAD POR VOLUMEN --}}
                    <p class="m-0" x-show="fishpond?.densityByVolume">
                      <span x-text="fishpond?.densityByVolume"></span>
                      <span class="text-bold"><i class="fas fa-fish"></i>/m<sup>3</sup></span>
                    </p>
                  </div>
                </div>
                <!--/.end col-->
              </div>
              {{-- INFORMACIÓN FINANCIERA --}}
              <div class="border-bottom mb-2">
                <h6 class="text-center border-bottom text-bold">Información Financiera</h6>
                {{-- COSTO INICIAL --}}
                <div class="row">
                  <p class="m-0 col-6">Costo alevinos:</p>
                  <p class="m-0 col-6 text-bold text-right" x-text="formatCurrency(fishBatch.amount, 0)"></p>
                </div>
                {{-- GASTOS ADICIONALES --}}
                <div class="row">
                  <p class="m-0 col-6">Gastos:</p>
                  <p class="m-0 col-6 text-bold text-right" x-text="formatCurrency(fishBatch.expenseAmount,0)"></p>
                </div>
                {{-- GASTOS DE ALIMENTO --}}
                <div class="row">
                  <p class="m-0 col-6">Alimentación:</p>
                  <p class="m-0 col-6 text-bold text-right" x-text="formatCurrency(0,0)"></p>
                </div>
                {{-- SUMATORIA --}}
                <div class="row border-top text-bold border-bottom">
                  <p class="m-0 text-lg col-6">Total:</p>
                  <p class="m-0 text-lg col-6 text-right" x-text="formatCurrency(fishBatch.totalAmount)"></p>
                </div>

                {{-- PRECIO UNITARIO --}}
                <p class="m-0">
                  <span class="text-bold">Precio Unitario [$/pez]</span>: 
                  <span x-text="formatCurrency(fishBatch.unitPrice, 0)"></span>
                </p>
                {{-- PRECIO UNITARIO --}}
                <p class="m-0">
                  <span class="text-bold">Precio [$/Kg]</span>: 
                  <span x-text="formatCurrency(fishBatch.price, 0)"></span>
                </p>
              </div>

              {{-- AUDITORÍA DEL LOTE --}}
              <div>
                <p class="mb-0 text-sm"><span class="text-bold">Registro</span>: <span x-text="fishBatch.createdAt.fromNow()"></span></p>
                <p class="mb-0 text-sm" x-show="!fishBatch.updatedAt.isSame(fishBatch.createdAt)">
                  <span class="text-bold">Actualización</span>: <span x-text="fishBatch.updatedAt.fromNow()"></span>
                </p>
              </div>
            </div>

            {{-- OBSERVACIONES --}}
            <div x-show.transition.in.durations.300ms="tab === 'observations'" style="display: none;">
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

            {{-- GASTOS --}}
            <div x-show.transition.in.durations.300ms="tab === 'expenses'" style="display: none;">
              <template x-for="expense in expenses" x-bind:key="expense.id">
                <div class="card card-light">
                  <header class="card-header p-2">
                    <div class="d-flex justify-content-between items-center">
                      <!-- Fecha y Tiempo relativo -->
                      <div class="d-flex flex-column">
                        <h6 class="m-0 text-bold" x-text="expense.date.format('dddd, DD-MM-YYYY')"></h6>
                        <p class="m-0 text-muted" x-text="expense.date.fromNow()"></p>
                      </div>
                      <!-- Controles -->
                      <div class="">
                        <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click="updateExpense(expense)"><i class="fas fa-edit"></i></a>
                        <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="destroyExpense(expense)"><i class="fas fa-trash"></i></a>
                      </div>
                      <!--/.end controles -->
                    </div>
                    <!--/end flex -->
                  </header>
        
                  <div class="card-body p-2">
                    <!-- Descripción -->
                    <p class="m-1" x-text="expense.description"></p>
                    <p class="text-center text-lg text-bold border-top border-bottom" x-text="formatCurrency(expense.amount)"></p>
                    <!-- Auditoría -->
                    <div class="d-flex flex-column">
                      <p class="m-0 text-sm text-muted">
                        Creado: <span x-text="expense.createdAt.fromNow()"></span>
                      </p>
                      <p class="m-0 text-sm text-muted" x-show="!expense.createIsSameUpdate">
                        Actualizado: <span x-text="expense.updatedAt.fromNow()"></span>
                      </p>
                    </div>
                    <!--/.edn auditoría -->
                  </div>
                  <!--/.end body -->
                </div>
              </template>
            </div>

            {{-- REPORTES DE MORTALIDAD --}}
            <div x-show.transition.in.durations.300ms="tab === 'deaths'" style="display: none;">
              <template x-for="deathReport in deaths" x-bind:key="deathReport.id">
                <div class="card card-light">
                  <header class="card-header p-2">
                    <div class="d-flex justify-content-between items-center">
                      <!-- Fecha y Tiempo relativo -->
                      <div class="d-flex flex-column">
                        <h6 class="m-0 text-bold" x-text="deathReport.createdAt.format('dddd, DD-MM-YYYY hh:mm a')"></h6>
                        <p class="m-0 text-muted" x-text="deathReport.createdAt.fromNow()"></p>
                      </div>
                      <!-- Controles -->
                      <div class="">
                        <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click="updateDeathReport(deathReport)"><i class="fas fa-edit"></i></a>
                        <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="destroyDeathReport(deathReport)"><i class="fas fa-trash"></i></a>
                      </div>
                      <!--/.end controles -->
                    </div>
                    <!--/end flex -->
                  </header>
        
                  <div class="card-body p-2">
                    <div class="row border-bottom">
                      {{-- POBLACIÓN --}}
                      <div class="col-5">
                        <p class="m-0">
                          Inicial: 
                          <span class="text-bold" x-text="deathReport.initialPopulation"></span>
                          <i class="fas fa-fish"></i>
                        </p>
                        <p class="m-0">
                          Final: 
                          <span class="text-bold" x-text="deathReport.population"></span>
                          <i class="fas fa-fish"></i>
                        </p>
                      </div>
                      {{-- MORTALIDAD --}}
                      <div class="col-7">
                        <p class="m-0">Muertes: <span class="text-bold" x-text="deathReport.deaths"></span></p>
                        <p class="m-0">Mortalidad: <span class="text-bold" x-text="deathReport.mortality"></span>%</p>
                      </div>
                    </div>
                    <div class="border-bottom text-center py-2">
                      <p class="text-lg mb-0">Muertes totales: <span class="text-bold" x-text="deathReport.totalDeaths"></span></p>
                      <p class="m-0 text-muted">Mortalidad Global: <span class="text-bold" x-text="deathReport.globalMortality"></span>%</p>
                    </div>
                    <!-- Auditoría -->
                    <div class="d-flex flex-column">
                      <p class="m-0 text-sm text-muted">
                        Creado: <span x-text="deathReport.createdAt.fromNow()"></span>
                      </p>
                      <p class="m-0 text-sm text-muted" x-show="!deathReport.createIsSameUpdate">
                        Actualizado: <span x-text="deathReport.updatedAt.fromNow()"></span>
                      </p>
                    </div>
                    <!--/.edn auditoría -->
                  </div>
                  <!--/.end body -->
                </div>
              </template>
            </div>

            {{-- BIOMETRÍAS --}}
            <div x-show.transition.in.durations.300ms="tab === 'biometries'" style="display: none;">
              <template x-for="(biometry, index) in biometries" x-bind:key="biometry.id">
                {{-- card --}}
                <div class="card mb-3" x-bind:class="{'card-light': index % 2 === 0, 'card-dark': index % 2 !== 0}">
                  {{-- HEADER --}}
                  <header class="card-header p-2">
                    <div class="d-flex justify-content-between items-center">
                      <!-- Fecha y Tiempo relativo -->
                      <div class="d-flex flex-column">
                        <h6 class="m-0 text-bold" x-text="biometry.date.format('dddd, DD-MM-YYYY hh:mm a')"></h6>
                        <p class="m-0 text-muted" x-text="biometry.date.fromNow()"></p>
                      </div>
                      <!-- Controles -->
                      <div class="">
                        <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click="updateBiometry(biometry)"><i class="fas fa-edit"></i></a>
                        <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="destroyBiometry(biometry)"><i class="fas fa-trash"></i></a>
                      </div>
                      <!--/.end controles -->
                    </div>
                    <!--/end flex -->
                  </header>
        
                  {{-- BODY --}}
                  <div class="card-body p-2">
                    <div class="row border-bottom">
                      {{-- INFORMACIÓN DE LA MUESTRA --}}
                      <div class="col-6">
                        {{-- POBLACIÓN --}}
                        <p class="m-0">
                          Población: 
                          <span class="text-bold" x-text="biometry.population"></span>
                          <i class="fas fa-fish"></i>
                        </p>
                        {{-- TAMAÑO DE LA MUESTRA --}}
                        <p class="m-0">
                          Muestra: 
                          <span class="text-bold" x-text="biometry.sampleSize"></span>
                          <i class="fas fa-fish"></i>
                          &#40;<span class="text-muted" x-text="biometry.samplePercentage"></span>&#37;&#41;
                        </p>
                        {{-- PESO PROMEDIO PROMEDIO --}}
                        <p class="m-0" x-show="biometry.averageWeight">
                          Peso Prom: 
                          <span class="text-bold" x-text="biometry.averageWeight ? round(biometry.averageWeight, 2) : 0"></span>
                          g.
                        </p>
                        {{-- PROMEDIO --}}
                        <p class="m-0" x-show="biometry.averageLong">
                          Long. Prom: 
                          <span class="text-bold" x-text="biometry.averageLong ? round(biometry.averageLong, 2) : 0"></span>
                          cm.
                        </p>
                      </div>

                      <div class="col-16">
                        {{-- BIOMASA INICIAL --}}
                        <p class="m-0">
                          Biomasa: 
                          <span x-text="round(biometry.biomass.value,2)" class="text-bold">
                          </span> <i x-text="biometry.biomass.unit"></i>
                        </p>
                      </div>
                    </div>

                    {{-- MEDICIONES --}}
                    <div class="row border">
                      <p class="text-bold text-center text-lg col-12 mb-0 p-1 border-bottom">Mediciones</p>
                      <table class="table table-sm table-striped table-bordered mb-2">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col" class="text-center">
                              Peso <span class="text-muted">[g]</span>
                            </th>
                            <th scope="col" class="text-center">
                              Largo <span class="text-muted">[cm]</span>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <template x-for="(measuring, index) in biometry.measurements" x-bind:key="index">
                            <tr>
                              <th scope="row" x-text="index + 1"></th>
                              <th class="text-center" x-text="measuring.weight"></th>
                              <th class="text-center" x-text="measuring.long"></th>
                            </tr>
                          </template>
                          <tr>
                            <th scope="row">Total</th>
                            <th class="text-center" x-text="biometry.totalWeight"></th>
                            <th class="text-center" x-text="biometry.totalLong"></th>
                          </tr>
                        </tbody>
                      </table>
                    </div>

                    <!-- Auditoría -->
                    <div class="d-flex flex-column">
                      <p class="m-0 text-sm text-muted">
                        Creado: <span x-text="biometry.createdAt.fromNow()"></span>
                      </p>
                      <p class="m-0 text-sm text-muted" x-show="!biometry.createIsSameUpdate">
                        Actualizado: <span x-text="biometry.updatedAt.fromNow()"></span>
                      </p>
                    </div>
                    <!--/.edn auditoría -->
                  </div>
                  <!--/.end body -->
                </div>
                <!--/.end card -->
              </template>
            </div>
          
          </x-cards.card-with-nav-tabs>
        </div>
      </div>

      

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