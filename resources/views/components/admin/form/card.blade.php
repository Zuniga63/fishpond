<form {{ $attributes->merge(['class' => 'card']) }} 
  x-bind:class="{
    'card-primary':state === 'creating',
    'card-info': state === 'editing'
  }"
>
  <!-- card header -->
  <header class="card-header">
    <h2 class="card-title">{{ $title }}</h2>
  </header>

  <div class="form-horizontal">
    <!-- card body -->
    <section class="card-body">
      {{ $slot }}
    </section><!--/.end card-body -->

    <!-- card footer -->
    <footer class="card-footer">
      {{ $footer }}
    </footer><!--/.end footer -->
  </div><!--/.end form-horizontal -->
</form><!--/.end card -->