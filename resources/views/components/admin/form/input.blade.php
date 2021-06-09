@props(['type' => 'text', 'id' => '', 'name' => '', 'error' => false])
<input 
  {{ $attributes->class(['form-control', 'is-invalid' => $error]) }}
  type="{{ $type }}"
  id="{{ $id }}"
  name="{{ $name }}"
  x-on:focus="$event.target.select()"
>