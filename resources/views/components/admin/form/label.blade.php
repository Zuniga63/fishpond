@props(['id' => ''])
<label {{ $attributes }} for="{{ $id }}">
  {{ $slot }}
</label>