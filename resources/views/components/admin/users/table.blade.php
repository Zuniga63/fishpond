@props(['users'])
<x-admin.state-table {{ $attributes }}>
  <x-slot name="title">Listado de usuarios</x-slot>

  <x-slot name="tableHead"> 
    <tr>
      <th class="text-center">ID</th>
      <th>Nombre</th>
      <th>Email</th>
      <th class="text-center">Rol</th>
      <th class="text-center">Verificado</th>
      <th></th>
    </tr>
  </x-slot>

  @foreach ($users as $user)
  <tr x-data="{userId:{{ $user->id }}, userName: '{{ $user->name }}'}">
    <td class="text-center">{{ $user->id }}</td>
    <td>{{ $user->name }}</td>
    <td>{{ $user->email }}</td>
    <td class="text-center">{{ count($user->roles) > 0 ? $user->roles[0]->name : 'No asignado' }}</td>
    <td class="text-center">{{ $user->email_verified_at ? 'Si' : 'No' }}</td>
    <td>
      <div class="btn-group-horizontal">
        <a 
          href="javascript:;" 
          class="btn-tools text-success" 
          title="Ver Registros" 
          data-toggle="tooltip" 
          data-placement="top" 
          wire:ignore
          x-on:click="$wire.showUserLogs(userId)"
        >
          <i class="fas fa-book"></i>
        </a>

        <a href="javascript:;" class="btn-tools text-info" title="Actualizar Datos" data-toggle="tooltip" data-placement="top" wire:ignore x-on:click="$wire.edit(userId)">
          <i class="fas fa-edit"></i>
        </a>
        @if(userHasPermission('close_user_session'))
          <a 
            href="javascript:;" 
            class="btn-tools text-danger" 
            title="Cerrar Session" 
            data-toggle="tooltip" 
            data-placement="top" 
            wire:ignore 
            wire:click="closeUserSessions({{$user->id}})"
          >
            <i class="fas fa-sign-out-alt"></i>
          </a>
        @endif
        @if (userHasPermission('delete_user'))
          <a 
            href="javascript:;" 
            class="btn-tools text-danger" 
            title="Eliminar Usuario" 
            data-toggle="tooltip" 
            data-placement="top" 
            wire:ignore 
            x-on:click="showDeleteAlert(userId, userName)"
          >
            <i class="fas fa-trash"></i>
          </a>
        @endif
      </div>
    </td>
  </tr>
  @endforeach

  <x-slot name="footer">
    {{-- TODO --}}
  </x-slot>
</x-admin.state-table>