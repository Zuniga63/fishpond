<div class="card card-dark">
  <div class="card-header">
    <ul class="nav nav-tabs card-header-tabs">
      {{ $tabs }}
    </ul>
  </div>
  <div class="card-body" style="min-height: 300px; max-height: 70vh; overflow: auto">
    {{ $slot }}
  </div>
</div>