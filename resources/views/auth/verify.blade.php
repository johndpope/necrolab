@extends('layouts.app')

@section('title', 'Verify Your Email Address')

@push('js')
    <script type="text/javascript" src="{{ mix('/js/auth.js') }}"></script>
@endpush

@section('layout')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-row pt-0">
                        <div class="mr-auto pt-0">
                            <h3>{{ __('Verify Your Email Address') }}</h3>
                        </div>
                        <div class="pt-0">
                            <a href="/">
                                <img src="/images/banners/banner_background_trimmed_small.jpg" alt="The NecroLab">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <noscript>
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif
                        <p>
                            Before proceeding, please check your email for a verification link.
                        </p>
                        <p>
                            If you did not receive this email, please click the Resend button below.
                        </p>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf

                            <button type="submit" class="btn btn-lg btn-secondary">
                                Resend
                            </button>
                        </form>
                    </noscript>
                    <div id="app">
                        <verify-form></verify-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
