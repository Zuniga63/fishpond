@props(['roles' => [], 'permissions' => []])
<x-admin.state-table {{ $attributes }}>
  <x-slot name="title">Asignación de Permisos</x-slot>

  <x-slot name="tableHead"> 
    <tr>
      <th class="text-center">N°</th>
      <th class="text-center">Permiso</th>
      @foreach ($roles as $role)
      <th class="text-center">{{ $role['name'] }}</th>
      @endforeach
    </tr>
  </x-slot>

  @foreach ($permissions as $permission)
    <tr>
      <td class="align-middle text-center">{{ $permission['order'] }}</td>
      <td>
        <div class="d-flex justify-content-between align-middle">
          {{-- Nombre y acción --}}
          <div class="d-flex flex-column">
            <p class="m-0">{{ $permission['name'] }}</>
            <p class="m-0 text-muted">{{ $permission['action'] }}</p>
          </div>

          <div class="d-flex align-middle">
            <a
              href="javascript:;" 
              class="fas fa-edit text-info pr-2 d-block "
              wire:click="edit({{$permission['id']}})"
            ></a>
            <a 
              x-data="{
                id: {{$permission['id']}},
                name: '{{$permission['name']}}'
              }"
              href="javascript:;" 
              class="fas fa-trash text-danger d-block" 
              
              x-on:click="showDeleteAlert(id, name)"
            ></a>
            
          </div>
        </div>
      </td>
      @foreach ($roles as $role)
      <td class="text-center align-middle"
        x-data="{
          permissionId: {{ $permission['id'] }},
          roleId: {{ $role['id'] }},
          check: {{ in_array($permission['id'], $role['permissions'], true) ? 'true' : 'false' }}
        }"  
      >
      <input 
        type="checkbox" 
        class="menu_rol" 
        x-model="check"
        x-on:change="$wire.changeState(check, roleId, permissionId)"
      >
      </td>
      @endforeach
    </tr>
  @endforeach

  <x-slot name="footer">
    <button class="btn btn-info" wire:click="writeSeeder">Crear Seeder</button>
  </x-slot>
</x-admin.state-table>