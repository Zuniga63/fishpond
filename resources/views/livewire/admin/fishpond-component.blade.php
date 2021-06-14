<div 
  x-data="app()" 
  x-init="init($wire, $dispatch)"
  x-on:cancel-register="showingModal = false"
  x-on:new-fishpond-registered="addNewFishpond($event.detail)"
  x-on:fishpond-updated="updateFishpond($event.detail)";
  wire:ignore
>
  {{-- Version Mobil --}}
  <div class="d-block d-lg-none position-relative">
    {{-- Tarjeta con los estanques activos e inactivos--}}
    <div x-show="updatingModel">
      <div class="d-flex flex-column align-items-center">
        <div class="spinner-border mb-3" role="status">
          <span class="sr-only">Loading...</span>
        </div>

        Recuperando información de los estanques...
      </div>
    </div>

    <div x-show="!updatingModel" class="pb-5">
      <x-admin.fishpond.fishpond-card/>
    </div>

    <template x-if="fishponds.length <= 0 && !updatingModel">
      <p class="h5">El numero de estanques es cero</p>
    </template>
    {{-- Fin de sección pricipal --}}

    {{-- Boton para habilitar formulario --}}
    <button 
      class="btn btn-primary rounded-circle new-fishpond-buttom z-fixed shadow"
      x-show.transition.duration.500ms="!showingModal"
      x-on:click="showingModal = true"
    >
      <i class="fas fa-plus"></i>
    </button>

    {{-- Modal para registrar nuevo estanque --}}
    <div 
      class="new-fishpond-modal z-modal" 
      style="display: none"
      x-show.transition.duration.500ms="showingModal"
    >
      <div 
        class="d-flex flex-column justify-content-center h-screen"
        x-on:click.self="$dispatch('hidden-register-form')"
      >
        <x-admin.fishpond.register-form/>
      </div>
    </div>
  </div>

  {{-- Version de escritorio --}}
</div>

@push('styles')
  <style>
    .new-fishpond-buttom{
      position: fixed;
      right: 1rem;
      bottom: 1rem;
      width: 40px;
      height: 40px;
    }

    .new-fishpond-modal{
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
    }

    .w-90{
      width: 90%;
    }

    .z-fixed{
      z-index: 100;
    }

    .z-modal{
      z-index: 1100;
    }

    .h-screen{
      height: 100vh;
    }
  </style>
@endpush

@push('scripts')
<script>
  window.addEventListener('livewire:load', () => {
    Livewire.on('alert', (title, message, type) => {
      functions.notifications(message, title, type);
    })
  });
</script>
<script src="{{mix('js/admin/fishpond-component/app.js')}}" defer></script>
@endpush
