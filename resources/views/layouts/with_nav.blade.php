@extends('layouts.app')

@section('layout')
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/">
            <img src="/images/logo.png" />
        </a>
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse collapse" id="navbarColor01">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Rankings</a>
                    <div class="dropdown-menu" x-placement="bottom-start">
                        <a class="dropdown-item" href="/rankings/power">Power</a>
                        <a class="dropdown-item" href="/rankings/score">Score</a>
                        <a class="dropdown-item" href="/rankings/speed">Speed</a>
                        <a class="dropdown-item" href="/rankings/deathless">Deathless</a>
                        <a class="dropdown-item" href="/rankings/character">Character</a>
                        <a class="dropdown-item" href="/rankings/daily">Daily</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Leaderboards</a>
                    <div class="dropdown-menu" x-placement="bottom-start">
                        <a class="dropdown-item" href="/leaderboards/score">Score</a>
                        <a class="dropdown-item" href="/leaderboards/speed">Speed</a>
                        <a class="dropdown-item" href="/leaderboards/deathless">Deathless</a>
                        <a class="dropdown-item" href="/leaderboards/daily">Daily</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/players">Players</a>
                </li>
            </ul>
            
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    @auth
                        <a class="nav-link" href="#">Welcome [user]!</a>
                    @else
                        <a href="/login" class="btn btn-secondary my-2 my-sm-0" role="button">Log In</a>
                    @endauth
                </li>
            </ul>
        </div>
    </div>
</nav>
@yield('content')
@endsection
