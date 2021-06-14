<template x-for="fishpond in fishponds" x-bind:key="fishpond.id">
  <div 
    class="card"
    x-bind:class="{
      'card-dark' : fishpond.inUse,
      'card-secondary': !fishpond.inUse,
    }"
  >
    <!-- Nombre del estanque -->
    <header class="card-header p-2">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="text-left p-0 m-0" x-text="fishpond.name"></h5>
        <div class="">
          <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click.stop="editFishpond(fishpond)"><i class="fas fa-edit"></i></a>
          <a href="javascript:;" class="btn btn-danger btn-sm disabled" ><i class="fas fa-trash"></i></a>
        </div>
      </div>
    </header>
    {{-- Cuerpo del estanque --}}
    <div class="card-body">
      {{-- Caracteristicas del estanque --}}
      <div class="border-bottom">
        <div class="row">
          {{-- AREA SUPERFICIAL --}}
          <div class="col-6">
            {{-- CAPACIDAD --}}
            <template x-if="fishpond.capacity">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Capacidad:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.capacity"></span>
                  <i class="fas fa-fish text-xs"></i>
                </p>
              </div>
            </template>

            {{-- DIAMETRO --}}
            <template x-if="fishpond.diameter">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Diametro :</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.diameter"></span>
                  <span class="text-xs">m</span>
                </p>
              </div>
            </template>

            {{-- ANCHO --}}
            <template x-if="fishpond.width">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Ancho:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.width"></span>
                  <span class="text-xs">m</span>
                </p>
              </div>
            </template>

            {{-- LARGO --}}
            <template x-if="fishpond.long">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Largo:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.long"></span>
                  <span class="text-xs">m</span>
                </p>
              </div>
            </template>

            {{-- Área --}}
            <template x-if="fishpond.area">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Área :</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.area"></span>
                  <span class="text-xs">m<sup>2</sup></span>
                </p>
              </div>
            </template>

            {{-- CAPACIDAD POR AREA --}}
            <template x-if="fishpond.area">
              <div class="d-flex justify-content-between" title="Peces por unidad de área">
                <p class="m-0 text-bold text-sm">Densidad:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.capacityByArea"></span>
                  <i class="fas fa-fish text-xs"></i> /
                  <span class="text-xs">m<sup>2</sup></span>
                </p>
              </div>
            </template>
          </div>

          {{-- VOLUMEN DEL ESTANQUE --}}
          <div class="col-6">

            {{-- PROFUNDIDAD EFECTIVA --}}
            <template x-if="fishpond.effectiveHeight">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Prof. Efectiva:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.effectiveHeight"></span>
                  <span class="text-xs">m</span>
                </p>
              </div>
            </template>

            {{-- PROFUNDIDAD MAXIMA --}}
            <template x-if="fishpond.maxHeight">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Prof. Max:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.maxHeight"></span>
                  <span class="text-xs">m</span>
                </p>
              </div>
            </template>

            {{-- VOLUMENES --}}
            <template x-if="fishpond.effectiveVolume || fishpond.maxVolume">
              <div class="d-flex justify-content-between">
                <p class="m-0 text-bold text-sm">Vol <span class="text-xs">[m<sup>3</sup>]</span>:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.effectiveVolume"></span>
                  {{-- VOLUMEN MAXIMO --}}
                  <span class="text-xs" x-show="fishpond.effectiveVolume && fishpond.maxVolume"> - </span>
                  <span x-text="fishpond.maxVolume"></span>
                </p>
              </div>
            </template>           

            {{-- CAPACIDAD POR VOLUMEN --}}
            <template x-if="fishpond.area">
              <div class="d-flex justify-content-between" title="Peces por unidad de volumen">
                <p class="m-0 text-bold text-sm">Densidad:</p>
                <p class="m-0 text-sm">
                  <span x-text="fishpond.capacityByVolume"></span>
                  <i class="fas fa-fish text-xs"></i> /
                  <span class="text-xs">m<sup>3</sup></span>
                </p>
              </div>
            </template>
          </div>
          {{-- FIN DE VARIABLES --}}
        </div>
        {{-- FIN DE CARACTERISTICAS --}}

        <template x-if="!fishpond.volume && !fishpond.area && !fishpond.capacity">
          <p class="m-0">El estanque no presenta variables de diseño</p>
        </template>
      </div>
    </div>
  </div>
</template>