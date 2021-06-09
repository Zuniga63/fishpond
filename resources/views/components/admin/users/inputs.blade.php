{{-- Nombre de usuario --}}
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

{{-- Correo Electronico --}}
<div class="form-group">
  <x-admin.form.label class="required" id="email">Correo Electronico</x-admin.label>
  <x-admin.form.input 
    id="email" 
    type="email"
    name="email"
    placeholder="Escribe el correo aquí"
    :error="$errors->has('email')"
    wire:model.defer="email"
    autocomplete="off"
    required
  />

  @error('email')
  <div class="invalid-feedback" role="alert">
    {{$message}}
  </div>
  @enderror
</div>

<div x-show.transition.duration.500ms="state === 'creating'">
  {{-- Contraseña --}}
  <div class="form-group">
    <x-admin.form.label class="required" id="password">Contraseña</x-admin.label>
    <x-admin.form.input 
      id="password" 
      type="password"
      name="password"
      placeholder="Escribe la contraseña aquí"
      :error="$errors->has('password')"
      wire:model.defer="password"
      autocomplete="off"
      x-bind:required="state === 'creating'"
    />

    @error('password')
    <div class="invalid-feedback" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>

  {{-- Confirmar Contraseña --}}
  <div class="form-group">
    <x-admin.form.label class="required" id="password_confirmation">Confirmar Contraseña</x-admin.label>
    <x-admin.form.input 
      id="password_confirmation" 
      type="password"
      name="password_confirmation"
      placeholder="Escribela nuevamente"
      :error="$errors->has('password_confirmation')"
      wire:model.defer="password_confirmation"
      autocomplete="off"
      x-bind:required="state === 'creating'"
    />

    @error('password_confirmation')
    <div class="invalid-feedback" role="alert">
      {{$message}}
    </div>
    @enderror
  </div>
</div>