@props(['roles' => []])
<x-admin.state-table {{ $attributes }}>
  <x-slot name="title">Roles</x-slot>

  <x-slot name="tableHead"> 
    <tr class="text-center">
      <th>ID</th>
      <th class="text-left">Nombre</th>
      <th class="text-left">Slug</th>
      <th>Permisos</th>
      <th></th>
    </tr>
  </x-slot>
  
  @foreach ($roles as $role)
  <tr>
    <td class="text-center">{{ $role['id'] }}</td>
    <td>{{ $role['name'] }}</td>
    <td>{{ $role['slug'] }}</td>
    <td class="text-center">{{ $role['permissions'] }}</td>
    <td class="text-center">
      <a
        href="javascript:;" 
        class="fas fa-edit text-info pr-2"
        wire:click="edit({{$role['id']}})"
      ></a>
      @if (!in_array($role['id'] ,$this->protectedRoles, true))
      <a 
        x-data="{
          id: {{$role['id']}},
          name: '{{$role['name']}}'
        }"
        href="javascript:;" 
        class="fas fa-trash text-danger" 
        x-on:click="showDeleteAlert(id, name)"
      ></a>
      @endif
    </td>
  </tr>
  @endforeach

  <x-slot name="footer">
    {{-- TODO --}}
  </x-slot>
</x-admin.state-table>