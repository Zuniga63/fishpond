<div class="container-fluid">
  <div class="row">
    <div class="col-lg-4">
      <x-admin.roles.form x-data="model()"/>
    </div>

    <div class="col-lg-8" x-data="tableModel()">
      <x-admin.roles.table :roles="$roles"/>
    </div>
  </div><!--/.end row -->

  <div class="row">
    <div class="col-12">
      <x-admin.roles.user-role-table :roles="$roles" :users="$users" x-data="tableModel()"/>
    </div>
  </div>
</div><!--/.end container -->

@push('scripts')
<script>
  window.model = ()=>{
    return {
      state: @entangle('state'),
      slug: @entangle('slug').defer,
    };
  };

  window.tableModel = () => {
    return {
      state: @entangle('state'),
    }
  }

  window.updateSlug = value =>{
    return value.toLowerCase().replace(/\s/gi, '_').normalize('NFD').replace(/[\u0300-\u036f]/g, '')
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