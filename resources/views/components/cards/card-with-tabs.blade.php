@props(['marginHeader' => '0'])
<div {{$attributes->merge(['class' => "card card-dark"])}}>
  <div class="card-header" style="margin-top: {{$marginHeader}};">
    <ul class="nav nav-tabs card-header-tabs">
      {{ $tabs }}
    </ul>
  </div>
  <div class="card-body p-2" style="min-height: 300px; max-height: 70vh; overflow: auto">
    {{ $slot }}
  </div>
</div>