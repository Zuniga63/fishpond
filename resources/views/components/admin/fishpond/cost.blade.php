<template x-if="fishpondSelected">
  <div class="card card-dark mx-auto mt-0 mb-5">
    <!-- header -->
    <header class="card-header p-2">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex flex-column">
          <h5 class="m-0" x-text="fishpondSelected.name"></h5>
          <p class="m-0 text-sm text-muted">Listado de costos</p>
        </div>
        <button class="btn btn-danger btn-sm" x-on:click="showingCosts = false">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </header>
    <!--/.end header -->
  
    <!-- body -->
    <div class="card-body" style="min-height: 20vh; max-height: 75vh; overflow-y: scroll">
      <template x-for="cost in fishpondSelected.costs" x-bind:key="cost.id">
        <!-- Costo Individual -->
        <div class="card card-light">
          <header class="card-header p-2">
            <div class="d-flex justify-content-between">
              <!-- Fecha y Tiempo relativo -->
              <div class="d-flex flex-column">
                <h6 class="m-0" x-text="cost.dateFormat"></h5>
                <p class="m-0 text-sm text-muted" x-text="cost.fromNow"></p>
              </div>
              <!-- Controles -->
              <div class="">
                <a href="javascript:;" class="btn btn-info btn-sm mr-1" x-on:click="editCost(cost)"><i class="fas fa-edit"></i></a>
                <a href="javascript:;" class="btn btn-danger btn-sm" x-on:click="destroyFishpondCost(cost.id, fishpondSelected.id)" ><i class="fas fa-trash"></i></a>
              </div>
              <!--/.end controles -->
            </div>
            <!--/end flex -->
          </header>

          <div class="card-body p-2">
            <!-- Descripción -->
            <p class="text-center m-1" x-text="cost.description"></p>
            <p class="text-center text-lg text-bold" x-text="formatCurrency(cost.amount)"></p>
            <!-- Auditoría -->
            <div class="d-flex flex-column">
              <p class="m-0 text-sm"> 
                Tipo de costo: <span x-text="costType[cost.type]"></span>
              </p>
              <p class="m-0 text-sm text-muted">
                Creado: <span x-text="cost.createdAt"></span>
              </p>
              <p class="m-0 text-sm text-muted">
                Actualizado: <span x-text="cost.updatedAt"></span>
              </p>
            </div>
            <!--/.edn auditoría -->
          </div>
          <!--/.end body -->
        </div>
        <!--/.end card-->
      </template>
    </div>
    <!--/.end body -->
  </div>
</template>