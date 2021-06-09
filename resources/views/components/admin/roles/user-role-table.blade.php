@props(['roles' => [], 'users' => []])
<x-admin.state-table {{ $attributes }}>
  <x-slot name="title">Asignar Roles a Usuarios</x-slot>

  <x-slot name="tableHead"> 
    <tr class="text-center">
      <th>Usuario</th>
      @foreach ($roles as $role)
      <th>{{ $role['name'] }}</th>
      @endforeach
    </tr>
  </x-slot>
  
  {{-- Cuerpor de la tabla --}}
  @foreach ($users as $user)
  <tr>
    <td>{{ $user['name'] }}</td>
    @foreach ($roles as $role)
    <td class="text-center align-middle" x-data="{
      userId: {{ $user['id'] }},
      roleId: {{ $role['id'] }},
      check: {{ in_array($role['id'], $user['roles'], true) ? 'true' : 'false'}}
    }"
    >
      <input 
        type="checkbox" 
        class="menu_rol" 
        x-model="check"
        value="{{ in_array($role['id'], $user['roles'], true) ? 'true' : 'false'}}"
        x-on:change="check=$wire.changeState(check, roleId, userId)"
      >
    </td>
    @endforeach
  </tr>
  @endforeach

  <x-slot name="footer">
    {{-- TODO --}}
  </x-slot>
</x-admin.state-table>