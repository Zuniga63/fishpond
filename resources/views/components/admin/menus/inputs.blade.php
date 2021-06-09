{{-- Nombre --}}
<div class="form-group">
  <x-admin.form.label class="required" id="name">Nombre</x-admin.label>
  <x-admin.form.input 
    id="name" 
    name="name"
    placeholder="Escribe el nombre del menú"
    :error="$errors->has('name')"
    wire:model.defer="name"
    autocomplete="off"
    required
  />

  @error('name')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- URL --}}
<div class="form-group">
  <x-admin.form.label class="required" id="url">URL</x-admin.label>
  <x-admin.form.input 
    id="url" 
    name="url"
    placeholder="administracion/ejemplo"
    :error="$errors->has('url')"
    wire:model.defer="url"
    autocomplete="off"
    required
  />

  @error('url')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- ICON --}}
<div class="form-group">
  <x-admin.form.label class="required" id="icon">Icono <i class="{{$icon}}" x-ref="icon"></i></x-admin.label>
  <x-admin.form.input 
    id="icon" 
    name="icon"
    placeholder="administracion/ejemplo"
    :error="$errors->has('icon')"
    x-model="icon"
    x-on:input="$refs.icon.classList=icon"
    autocomplete="off"
    required
  />

  @error('icon')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>