@props(['roles' => [], 'menus' => []])
<x-admin.state-table {{ $attributes }}>
  <x-slot name="title">Asignar Menús a Roles</x-slot>

  <x-slot name="tableHead"> 
    <tr class="text-center">
      <th>Menús</th>
      @foreach ($roles as $role)
      <th>{{ $role['name'] }}</th>
      @endforeach
    </tr>
  </x-slot>
  
  {{-- Cuerpor de la tabla --}}
  @foreach ($menus as $menuBase)
  <x-admin.menus.menu-role-item :menu="$menuBase" :roles="$roles" level="0" />
    @if ($menuBase['submenus'])
      @foreach ($menuBase['submenus'] as $menuLevelOne)
        <x-admin.menus.menu-role-item :menu="$menuLevelOne" :roles="$roles" level="1" />
        @if ($menuLevelOne['submenus'])
          @foreach ($menuLevelOne['submenus'] as $menuLevelTwo)
            <x-admin.menus.menu-role-item :menu="$menuLevelTwo" :roles="$roles" level="2" />
          @endforeach
        @endif
      @endforeach
    @else
        
    @endif
  @endforeach

  <x-slot name="footer">
    {{-- TODO --}}
  </x-slot>
</x-admin.state-table>