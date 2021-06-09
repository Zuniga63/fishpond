<section {{ $attributes->merge(['class' => 'card']) }} 
  x-bind:class="{
    'card-primary':state === 'creating',
    'card-info': state === 'editing'
  }"
>
  <div class="card-header">
    <h2 class="card-title">{{ $title }}</h2>
  </div>

  <div class="card-body table-responsive p-0" style="max-height: 67vh; height:67vh;">
    <table class="table table-head-fixed table-hover table-striped text-nowrap">
      <thead>
        {{ $tableHead }}
      </thead> <!-- ./end thead -->

      <tbody>
        {{ $slot }}
      </tbody>
    </table><!-- ./end table -->
  </div><!--/.end card-body -->

  <div class="card-footer">
    {{ $footer }}
  </div>
</section><!--/.end card -->