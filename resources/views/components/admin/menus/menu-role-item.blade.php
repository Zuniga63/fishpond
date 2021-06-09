@props(['menu', 'roles', 'level' => 0])
@php
  $levelClass = [
    'font-weight-bold pl-10',
    'pl-20',
    'pl-30',
    'pl-40',
  ];

  $levelIcon = [ 'fas fa-arrows-alt', 'fas fa-level-up-alt rotate', 'fas fa-dot-circle',]
@endphp
<tr>
  <td class="{{ $levelClass[$level] }}">
    <i class="{{ $levelIcon[$level]}}"></i>
    {{ $menu['name'] }}
  </td>
  @foreach ($roles as $role)
  <td class="text-center align-middle" x-data="{
    menuId: {{ $menu['id'] }},
    roleId: {{ $role['id'] }},
    check: {{ in_array($menu['id'], $role['menus'], true) ? 'true' : 'false'}}
  }"
  >
    <input 
      type="checkbox" 
      class="menu_rol" 
      x-model="check"
      value="{{ in_array($menu['id'], $role['menus'], true) ? 'true' : 'false'}}"
      x-on:change="check=$wire.changeState(check, roleId, menuId)"
    >
  </td>
  @endforeach
</tr>