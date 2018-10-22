@extends('layouts.without_nav')

@section('title', 'Login')

@push('css')
    <style>
    html, body {
        height:100%;
    }
    body {
        display:flex;
        align-items:center;
    }
    </style>
@endpush

@section('content')
<div class="container text-center">
    <div class="row justify-content-center mb-4">
        <div class="col-12">
            <a href="/">
                <img class="img-fluid" src="/images/banners/banner_background.png" alt="The NecroLab">
            </a>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-sm-7 col-md-5 col-lg-4 col-xl-4">
            <h5 class="mb-3 font-weight-normal">Welcome to The NecroLab.</h5>
            <h5 class="mb-3 font-weight-normal">Please login below.</h5>
            @if ($error != '')
                <div class="alert alert-danger">
                    @if ($error == 'steam_not_exists')
                        Your Steam account doesn't exist on this site. If you've recently submitted to the Steam leaderboards you may need to wait a few minutes before logging in.
                    @endif
                </div>
            @endif
            
            <a href="/login/steam" class="btn btn-lg btn-primary btn-block" role="button"><i class="fab fa-steam-square"></i> Log in with Steam</a>
        </div>
    </div>
</div>
@endsection
