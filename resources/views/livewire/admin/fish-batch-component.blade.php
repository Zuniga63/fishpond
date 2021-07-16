<div 
  x-data="app()" 
  x-init="init($wire, $dispatch)"
  x-on:cancel-form-operation="formActive = false"
  x-on:fish-batch-created="addNewFishBatch($event.detail.fishBatch)"
  x-on:fish-batch-updated="updateFishBatch($event.detail.fishBatch)"
  x-on:observation-was-created="addObservation($event.detail)"
  x-on:observation-was-updated="updateObservation($event.detail)"
  x-on:back-to-home="home = true"
  x-on:enable-form="enableForm($event.detail.formName, $event.detail.fishBatch, $event.detail.data)"
  wire:ignore
  class="pb-3"
>
  {{-- PANEL PRINCIPAL --}}
  <div x-show.transition.in.duration.500ms="!formActive">
    <div x-show.transition.in.duration.500ms="home" style="display: none;">
      <x-admin.fish-batch.home/>
    </div>

    {{-- PANEL DE CONTROL DEL LOTE --}}
    <div x-show.transition.in.duration.500ms="!home" style="display: none;">
      <x-admin.fish-batch.batch-card/>
    </div>
  </div>

  {{-- FORMULARIOS --}}
  <div x-show.transition.in.duration.300ms="formActive" style="display: none;">
    <x-admin.fish-batch.fish-batch-form/>
    <x-admin.fish-batch.fish-batch-observation-form/>
  </div>

  {{-- BOTON PARA HABILITAR FORMULARIO DE NUEVO LOTE --}}
  <button 
    class="btn btn-primary rounded-circle fixed-buttom z-fixed shadow"
    x-on:click="enableForm('new-fish-batch')"
    x-show.transition.in.duration.500ms="!formActive && home"
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

  window.initialData = @json($data);
</script>
<script src="{{mix('js/admin/fish-batch-component/app.js')}}" defer></script>
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

