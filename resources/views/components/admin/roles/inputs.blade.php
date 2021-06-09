{{-- Nombre --}}
<div class="form-group">
  <x-admin.form.label class="required" id="name">Nombre</x-admin.label>
  <x-admin.form.input 
    id="name" 
    name="name"
    placeholder="Escribe el nombre aquí"
    :error="$errors->has('name') || $errors->has('slug')"
    wire:model.defer="name"
    autocomplete="off"
    x-on:change="slug = updateSlug($event.target.value)"
    required
  />

  @error('name')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
  @error('slug')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div class="form-group">
  <label class="required" for="description">Descripción</label>
  <textarea 
    name="description" 
    id="description" 
    class="form-control {{$errors->has('description') ? 'is-invalid' : ''}}"
    placeholder="Escribe una breve descripción aquí"
    wire:model.defer="description"
    x-on:focus="$event.target.select()"
  ></textarea>
  @error('description')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>