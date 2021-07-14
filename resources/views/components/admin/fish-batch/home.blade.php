<x-cards.card-with-tabs>
  <x-slot name="tabs">
    <!-- Muestra los lotes actualmente activos -->
    <x-cards.tab 
      x-bind:class="{active : tab === 'sown-lot'}" 
      href="javascript:;" 
      x-on:click="changeTab('sown-lot')"
    >
      Sembrados
    </x-cards.nav-tab>

    <!-- Muestra los lotes que fueron cosechados -->
    <x-cards.tab 
      x-bind:class="{active : tab === 'harvested-batch'}" 
      href="javascript:;"
      x-on:click="changeTab('harvested-batch')"
    >
      Cosechados
    </x-cards.nav-tab>
  </x-slot>

  {{-- LOTES DE PECES --}}
  <template x-for="batch in fishBatchs" x-bind:key="batch.id">
    <div class="card card-primary">
      <header class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="text-left p-0 m-0" x-text="batch.fishpond.name"></h5>
          <div class="">
            <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click="enableForm('update-fish-batch', batch)"><i class="fas fa-edit"></i></a>
            <a href="javascript:;" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
          </div>
        </div>
      </header>

      {{-- BODY --}}
      <div class="card-body p-2">
        {{-- INFORMACIÓN RELACIONADA A LA SIEMBRA Y COSESCHA --}}
        <div class="border-bottom">
          <p class="m-0">Siembra: <span class="text-bold" x-text="batch.seedtime.format('dddd DD-MM-YYYY hh:mm a')"></span></p>
          <p class="m-0 text-sm text-muted">Edad: <span x-text="batch.age"></span></p>
          <p class="m-0" x-show="batch.harvest">Cosecha: <span class="text-bold" x-text="batch.harvest?.format('dddd, DD/MM/YY')"></span></p>
        </div>

        {{-- POPBLACIÓN Y DESNIDADES --}}
        <div class="row border-bottom">
          {{-- POBLACIÓN Y BIOMASA --}}
          <div class="col-6">
            <!-- Población Actual -->
            <p class="m-0"><i class="fas fa-fish"></i> <span x-text="batch.population"></span></p>
            <!-- Densidad Volumetrica -->
            <p class="m-0" x-show="batch.fishpond.densityByArea">
              <span class="text-sm"><i class="fas fa-fish"></i>/m<sup>3</sup></span> 
              <span x-text="batch.fishpond.densityByVolume"></span>
              <span class="text-sm text-muted">[<span x-text="batch.fishpond.volume"></span> m<sup>3</sup>]</span>
            </p>
          </div>
          <!--/.end col-->
          {{-- BIOMASA --}}
          <div class="col-6">
            <p class="m-0">Biomasa: <span x-text="batch.biomass.value"></span> <span x-text="batch.biomass.unit"></span></p>
            <p class="m-0 text-sm text-muted">Peso Promedio: <span x-text="batch.initialWeight"></span> g</p>
          </div>
          <!--/.end col-->
        </div>
        <!--/.end row -->

        {{-- INFORMACIÓN DE ACTUALIZACIÓN --}}
        <div class="border-bottom">
          <p class="mb-0 text-muted text-xs">Creado: <span x-text="batch.createdAt.fromNow()"></span></p>
          <p class="mb-0 text-muted text-xs" x-show="!batch.createdAt.isSame(batch.updatedAt)">Actualizado: <span x-text="batch.updatedAt.fromNow()"></span></p>
        </div>

        {{-- COSTOS Y GASTOS DEL LOTE --}}
        <P class="h4 m-0 text-bold text-center" x-text="formatCurrency(batch.amount, 0)" title="Sumatoria de Costos"></P>


        <footer class="card-footer">
          <button class="btn btn-primary" style="width: 100%">Ver más información</button>
        </footer>
      </div>
      <!--/.end body -->
    </div>
  </template>
</x-cards.card-with-nav-tabs>