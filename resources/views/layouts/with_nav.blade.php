@extends('layouts.app')

@section('layout')
<b-navbar toggleable="md" type="dark" variant="primary">
    <b-navbar-brand href="/">
        <img src="/images/banners/banner_no_background.png" class="img-fluid" />
    </b-navbar-brand>
    <b-navbar-toggle target="nav_collapse"></b-navbar-toggle>
    <b-collapse is-nav id="nav_collapse">
        <b-navbar-nav>
            <b-nav-item href="/">Home</b-nav-item>
            <b-nav-item-dropdown text="Rankings" right>
                <b-dropdown-item href="/rankings/power">Power</b-dropdown-item>
                <b-dropdown-item href="/rankings/score">Score</b-dropdown-item>
                <b-dropdown-item href="/rankings/speed">Speed</b-dropdown-item>
                <b-dropdown-item href="/rankings/deathless">Deathless</b-dropdown-item>
                <b-dropdown-item href="/rankings/character">Character</b-dropdown-item>
                <b-dropdown-item href="/rankings/daily">Daily</b-dropdown-item>
            </b-nav-item-dropdown>
            <b-nav-item-dropdown text="Leaderboards" right>
                <b-dropdown-item href="/leaderboards/score">Score</b-dropdown-item>
                <b-dropdown-item href="/leaderboards/speed">Speed</b-dropdown-item>
                <b-dropdown-item href="/leaderboards/deathless">Deathless</b-dropdown-item>
                <b-dropdown-item href="/leaderboards/daily">Daily</b-dropdown-item>
            </b-nav-item-dropdown>
            <b-nav-item href="/players">Players</b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav class="ml-auto">
            @auth
                <b-nav-item href="#">Welcome [user]!</b-nav-item>
            @else
                <b-button href="/login" class="my-2 my-sm-0">Log In</b-button>
            @endauth
        </b-navbar-nav>
    </b-collapse>
</b-navbar>
@yield('content')
@endsection
