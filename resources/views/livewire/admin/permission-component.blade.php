<div class="container-fluid">
  <div class="row">
    <div class="col-lg-4">
      <x-admin.permissions.form x-data="model()"/>
    </div>

    <div class="col-lg-8" x-data="model()">
      <x-admin.permissions.table :roles="$roles" :permissions="$permissions"/>
    </div>
  </div><!--/.end row -->
  <div class="row d-none mb-4" id="rowSeeder">
    <div class="col-lg-12">
      <textarea name="seeder" id="seeder" rows="10" class="form-control"></textarea>
    </div>
  </div>
</div><!--/.end container -->

@push('scripts')
<script>
  window.model = ()=>{
    return {
      state: @entangle('state')
    };
  };

  window.tableModel = () => {
    return {
      state: @entangle('state'),
    }
  }

  window.addEventListener('livewire:load', ()=>{

    Livewire.on('alert', (title, message, type) => {
      functions.notifications(message, title, type);
      console.log(message);
    })
    
    Livewire.on('reset', () => {
      //TODO
    })

    Livewire.on('viewRender', (data)=>{
      //TODO
    })

    Livewire.on('reload', ()=>{
      location.reload();
    })

    Livewire.on('showSeeders', (permissions)=>{
      const rowSeeder = document.getElementById('rowSeeder');
      const seeder = document.getElementById('seeder');

      let text = `${permissions}`;

      rowSeeder.classList.remove('d-none');
      seeder.value = text;
    })
    
  });

  const showDeleteAlert = (id, name) => {
    Swal.fire({
      title:`¿Desea eliminar el permiso "${name}"?`,
      text: 'Esta acción no pude revertirse y posteriormente se debe cerrar la sessión de todos los usuarios implicados',
      icon: 'warning', 
      input: 'password',
      inputAttributes: {
        placeholder: 'Escribe tu contraseña',
        autocapitalize: 'off'
      },

      showCancelButton: true,
      cancelButtonColor: 'var(--primary)',
      confirmButtonColor: 'var(--success)',
      confirmButtonText: '¡Eliminar!',
      showLoaderOnConfirm: true,

    }).then(result => {
      if(result.isConfirmed){
        @this.call('destroy', id, result.value);
      }//end if
    })
  }

</script>
@endpush