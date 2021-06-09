@extends('layouts.admin.layout')

{{-- Se Actualiza el title --}}
@section('title')
  {{$title}}
@endsection

{{-- Se actualiza el titulo de la vista --}}
@section('contentTitle')
  {{$contentTitle}}
@endsection

@section('contentBreadcrum')
  <ol class="breadcrumb float-sm-right">
    @foreach ($breadcrumb as $key => $value)
      @if ($loop->last)
        <li class="breadcrumb-item active">{{$key}}</li>
      @else
        <li class="breadcrumb-item"><a href="{{$value}}">{{$key}}</a></li>
      @endif      
    @endforeach
  </ol>
@endsection

@section('content')
  {{ $slot }}
@endsection