<div class="container-fluid">
  <div class="row">
    <div class="col-lg-4">
      <x-admin.menus.form x-data="model()" :icon="$icon"/>
    </div>

    <div class="col-lg-8" x-data="tableModel()">
      <x-admin.menus.table :menus="$menus"/>
    </div>
  </div><!--/.end row -->

  <div class="row">
    <div class="col-12">
      <x-admin.menus.menu-role-table :roles="$roles" :menus="$menus" x-data="tableModel()"/>
    </div>
    @if ($showSeeder)
      <div class="col-lg-12">
        <textarea class="form-control" rows="10">{{ $seeder }}</textarea>
      </div>
    @endif
  </div>
</div><!--/.end container -->

@push('styles')
<style>
  .card-footer.no-after::after{
    content: none;
  }

  .rotate{
    -webkit-transform: rotate(90deg);
    -moz-transform: rotate(90deg);
    -ms-transform: rotate(90deg);
    -o-transform: rotate(90deg);
  }
</style>
<link rel="stylesheet" href="{{asset('css/admin/jquery.nestable.css')}}">
@endpush

@push('scripts')
<script>
  window.model = ()=>{
    return {
      state: @entangle('state'),
      icon: @entangle('icon').defer,
    };
  };

  window.tableModel = () => {
    return {
      state: @entangle('state'),
    }
  }

  window.saveOrder = () =>{
    let menus = JSON.stringify($('#nestable').nestable('serialize'));
    console.log(menus);
    @this.saveOrder(menus);
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
    
    Livewire.on('saveOrder', (isOk)=>{
      const nestableFooter = document.getElementById('nestableFooter')
      const button = document.getElementById('saveChange');
      
      if(button && nestableFooter){
        if(!isOk){
          nestableFooter.classList.remove('justify-content-center');
          nestableFooter.classList.add('justify-content-around')
          button.classList.remove('d-none');
        }
      }
    })

    /**
     * Se inicializa el plugin nestable
     */
    $('#nestable').nestable().on('change', function() {
      const nestableFooter = document.getElementById('nestableFooter')
      const button = document.getElementById('saveChange');
      if(button && nestableFooter){
        //Cambio el justify content de center to around
        nestableFooter.classList.remove('justify-content-center');
        nestableFooter.classList.add('justify-content-around')
        button.classList.remove('d-none');
      }
    })
  });

  const showDeleteAlert = (id, name) => {
    Swal.fire({
      title:`¿Desea eliminar el menú "${name}"?`,
      text: 'Esta acción no pude revertirse y eliminará tambien todos los submenus',
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

<script src="{{asset('js/admin/jquery.nestable.js')}}" defer></script>
@endpush