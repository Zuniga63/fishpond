@props(['logs', 'userName'])
<x-admin.state-table {{ $attributes }}>
  <x-slot name="title">{{$userName}}</x-slot>

  <x-slot name="tableHead"> 
    <tr>
      <th class="text-center">N°</th>
      <th class="text-center">Fecha</th>
      <th>Acción</th>
      <th>Descripción</th>
    </tr>
  </x-slot>

  @foreach ($logs as $log)
  <tr>
    <td class="text-center">{{ $log['index'] }}</td>
    <td>{{ $log['date'] }}</td>
    <td>{{ $log['action'] }}</td>
    <td>{{ $log['description'] }}</td>
  </tr>
  @endforeach

  <x-slot name="footer">
    {{-- TODO --}}
  </x-slot>
</x-admin.state-table>