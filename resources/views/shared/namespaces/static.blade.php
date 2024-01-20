@extends('shared.namespaces.base')

@section('namespace') static @stop

@section('metas')
  @foreach(config('locales') as $locale => $data)
    <link rel="alternate"
      hreflang="{{ str_replace('_', '-', $locale) }}"
      href="{{ $data['url'] . '/' . Request::route()->uri }}" />
  @endforeach
@stop

@section('content')

<nav class="static-submenu">
  <ul class="container">

    <li><a href="{{ route('get.static.about') }}">{{ _i("A propos") }}</a></li>
    <li><a href="{{ route('get.static.faq') }}">{{ _i("F.A.Q") }}</a></li>
    <li><a href="mailto:{{ config('app.email') }}" target="_blank">{{ _i("Contactez-nous") }}</a></li>
    <li><a href="{{ route('get.static.legals') }}">{{ _i("Mentions légales") }}</a></li>

  </ul>
</nav>

@yield('static_content')

@stop
