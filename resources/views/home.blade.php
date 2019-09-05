@extends('layouts.app')

@section('title', 'Home Page')

@push('js')
    <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
@endpush

@section('layout')
<transition name="fade">
    <router-view></router-view>
</transition>
@endsection
