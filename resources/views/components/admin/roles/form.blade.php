<x-admin.form.card {{$attributes}} wire:submit.prevent="submit">
  {{-- Titulo de la terjeta --}}
  <x-slot name="title">
    <span x-show.transition.in="state === 'creating'">Registrar Rol</span>
    <span x-show.transition.in="state === 'editing'">Actualizar Rol</span>
  </x-slot>

  <x-admin.roles.inputs/>
  
  <div wire:loading wire:target="submit">
    Procesando solicitud...
  </div>

  <div wire:loading wire:target="edit">
    Recuperando datos...
  </div>

  <x-slot name="footer">
    <x-admin.form.button type="submit">
      <span x-show.transition.in="state === 'creating'">Registrar</span>
      <span x-show.transition.in="state === 'editing'">Actualizar</span>
    </x-admin.form.button>

    <button class="btn btn-link" wire:click="resetFields" type="button">
      Cancelar
    </button>
  </x-slot>
</x-admin.form.card>