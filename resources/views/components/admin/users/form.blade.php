@props(['roles', 'state'])
<x-admin.form.card {{$attributes}} wire:submit.prevent="submit">
  {{-- Titulo de la terjeta --}}
  <x-slot name="title">
    <span x-show.transition.in="state === 'creating'">Registrar Usuario</span>
    <span x-show.transition.in="state === 'editing'">Actualizar Datos</span>
  </x-slot>

  <x-admin.users.inputs :roles="$roles"/>
  
  <div wire:loading wire:target="submit">
    Procesando solicitud...
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