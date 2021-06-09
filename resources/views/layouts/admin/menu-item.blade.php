@if ($item['submenus'])
<li class="nav-item has-treeview">
  <a href="javascript:;" class="nav-link">
    <i class="nav-icon {{!empty($item['icon']) ? $item['icon'] : 'fas fa-circle'}}"></i>
    <p>
      {{$item['name']}}
      <i class="right fas fa-angle-left"></i>
    </p>
  </a>
  <ul class="nav nav-treeview">
    @foreach ($item['submenus'] as $submenu)
    @include("layouts/admin/menu-item", ["item" => $submenu])
    @endforeach
  </ul>
</li>
@else
<li class="nav-item">
  <a href="{{url($item['url'])}}" class="nav-link {{getMenuActive($item['url'])}}">
    <i class="nav-icon {{!empty($item['icon']) ? $item['icon'] : 'far fa-dot-circle'}}"></i>
    <p>{{$item['name']}}</p>
  </a>
</li>
@endif