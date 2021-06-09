@props(['type' => 'button'])
<button 
  type="{{ $type }}"
  class="btn" 
  x-bind:class="{
    'btn-primary': state=== 'creating',
    'btn-info': state === 'editing',
  }"
  {{ $attributes }}
>
  {{ $slot }}
</button>