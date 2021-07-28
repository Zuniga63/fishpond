<div 
  x-data="app()" 
  x-init="init($wire, $dispatch)"
  class="pb-3"
  x-on:cancel-form-operation="formActive = false"
  x-on:fish-food-was-stored="addFishFood($event.detail)"
  x-on:fish-food-was-updated="updateFishFood($event.detail)"
  x-on:stock-was-stored="addFishFoodStock($event.detail)"
>
  {{-- PANEL --}}
  <div x-show.transition.in.duration.300ms="!formActive">
    <x-cards.card-with-tabs>
      <x-slot name="tabs">
        <!-- Muestra los lotes actualmente activos -->
        <x-cards.tab 
          x-bind:class="{active : tab === 'foods'}" 
          href="javascript:;" 
          x-on:click="tab = 'foods'"
        >
          Listado
        </x-cards.nav-tab>
    
        <!-- Muestra los lotes que fueron cosechados -->
        <x-cards.tab 
          x-bind:class="{active : tab === 'inventory'}" 
          href="javascript:;"
          x-on:click="tab = 'inventory'"
        >
          Inventarios
        </x-cards.nav-tab>
      </x-slot>
  
      {{-- LISTADO DE ALIMENTOS --}}
      <x-admin.fish-food.food-list/>
  
      {{-- LISTADO DE STOCKS --}}
      <x-admin.fish-food.stock-list/>
            
    </x-cards.card-with-tabs>
  </div>

  {{-- FORMULARIOS --}}
  <div x-show.transition.in.duration.500ms="formActive" style="display: none;">
    <x-admin.fish-food.fish-food-form/>
    <x-admin.fish-food.stock-form/>
  </div>


  {{-- BOTON PARA HABILITAR FORMULARIO DE NUEVO LOTE --}}
  <button 
    class="btn btn-primary rounded-circle fixed-buttom z-fixed shadow"
    x-on:click="enableForm"
    x-show.transition.in.duration.500ms="!formActive"
  >
    <i class="fas fa-plus"></i>
  </button>
</div>
@push('scripts')
<script>
  window.addEventListener('livewire:load', () => {
    Livewire.on('alert', (title, message, type) => {
      functions.notifications(message, title, type);
    })
  });

  window.initialData = @json($initialData);
</script>
<script src="{{mix('js/admin/fish-food-component/app.js')}}" defer></script>
@endpush

@push('styles')
  <style>
    .fixed-buttom{
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