@props(['menus' => []])
<section {{ $attributes->merge(['class' => 'card']) }} 
  x-bind:class="{
    'card-primary':state === 'creating',
    'card-info': state === 'editing'
  }"
>
  <div class="card-header">
    <h2 class="card-title">Distribución actual de los menús</h2>
  </div>

  <div class="card-body" style="max-height: 65vh;overflow-y: scroll;">
    <div class="dd" id="nestable">
      <ol class="dd-list">
        @foreach ($menus as $item)
        <x-admin.menus.menu-item :item="$item"/>
        @endforeach
      </ol>
    </div>
  </div><!--/.end card-body -->

  <div id="nestableFooter" class="card-footer d-flex justify-content-center no-after" x-data>
    <button class="btn btn-info" wire:click="createSeeder">Crear Seeder</button>
    <button class="btn btn-success d-none" id="saveChange" x-on:click="saveOrder()">Guardar Cambios</button>
  </div>
</section><!--/.end card -->