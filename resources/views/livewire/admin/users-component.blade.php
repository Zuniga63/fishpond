<div class="fluid-container" x-data="app()">
  <div class="row" x-bind:class="{'blur':showLogs}">
    <!-- Formulario de registro -->
    <div class="col-lg-4">
      <x-admin.users.form :roles="$roles"/>
    </div><!--/.end col -->

    <!-- Tabla de usuarios -->
    <div class="col-lg-8">
      <x-admin.users.table :users="$users"/>
    </div><!--/.end col -->
  </div><!--/. row -->

  <div class="user-logs" x-show="showLogs">
    <div class="user-logs__content">
      <div class="user-logs__close" x-on:click="showLogs = false"><i class="fas fa-times"></i></div>
      <x-admin.users.logs :logs="$userLogs" :user-name="$name"/>
    </div>
  </div>
</div><!--/.container -->

@push('scripts')
<script>
  window.app = () => {
    return{
      showLogs: @entangle('showingUserLogs'),
      state: @entangle('state'),
    }
  }

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
    
  });

  const showDeleteAlert = (id, name) => {
    Swal.fire({
      title:`¿Desea eliminar el usuario "${name}"?`,
      text: 'Esta acción no pude revertirse y eliminará todos los datos del usuario en la plataforma.',
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

@push('styles')
<style>
  .fluid-container{
    position: relative;
  }
  .blur{
    filter: blur(2px);
  }

  .user-logs{
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1000;
  }

  .user-logs__content{
    position: relative;
    width: 90%;
    margin-left: auto;
    margin-right: auto;
  } 
  .user-logs__close{
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 1.5em;
    line-height: 1.5;
    color: var(--light);
    cursor: pointer;
    z-index: 100;
  }
</style>
@endpush