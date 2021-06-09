{{-- Nombre --}}
<div class="form-group">
  <x-admin.form.label class="required" id="name">Nombre</x-admin.label>
  <x-admin.form.input 
    id="name" 
    name="name"
    placeholder="Escribe el nombre aquí"
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

{{-- Action --}}
<div class="form-group">
  <x-admin.form.label class="required" id="action">Acción</x-admin.label>
  <x-admin.form.input 
    id="action" 
    name="action"
    placeholder="acción_en_minusculas"
    :error="$errors->has('action')"
    wire:model.defer="action"
    autocomplete="off"
    required
  />

  @error('action')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

{{-- Orden --}}
<div class="form-group">
  <x-admin.form.label class="required" id="order">Orden</x-admin.label>
  <x-admin.form.input 
    id="order" 
    name="order"
    type="number"
    placeholder="Especifica la posición"
    :error="$errors->has('order')"
    wire:model.defer="order"
    autocomplete="off"
    required
    min="0"
    max="{{ $this->getMaxOrder() }}"
  />

  @error('order')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>