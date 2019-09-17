@extends('layouts.app')

@section('title', 'Login')

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
                            <h3>{{ __('Login') }}</h3>
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
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <div>
                                    <label for="email" class="col-form-label">{{ __('E-Mail Address') }}</label>
                                </div>
                                <div class="input-group">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="off" maxlength="255">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <div>
                                    <label for="password" class="col-form-label">{{ __('Password') }}</label>
                                </div>
                                <div class="input-group">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="off" maxlength="255">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label for="password" class="form-check-label">{{ __('Remember Me') }}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-block">
                                    {{ __('Login') }}
                                </button>
                            </div>
                            <div class="form-group">
                                <a href="/register" class="btn btn-lg btn-secondary btn-block" role="button">
                                    Register
                                </a>
                            </div>
                            <div class="form-group mb-0 text-right">
                                <a class="btn btn-small btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        </form>
                    </noscript>
                    <div id="app">
                        <login-form></login-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
