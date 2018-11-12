@extends('layouts.app')

@section('title', 'Home Page')

@section('layout')
<transition name="fade">
    <router-view></router-view>
</transition>
@endsection
