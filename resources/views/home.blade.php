@extends('layouts.with_nav')

@section('title', 'Home Page')

@section('content')
<transition name="fade">
    <router-view></router-view>
</transition>
@endsection
