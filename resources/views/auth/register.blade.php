@extends('layouts.app')

@section('title', 'Register')

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
                            <h2>{{ __('Register') }}</h2>
                        </div>
                        <div class="pt-0">
                            <a href="/">
                                <img src="/images/banners/banner_background_trimmed_small.jpg" alt="The NecroLab">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <noscript>
                            <div class="form-group">
                                <div>
                                    <label for="username" class="col-form-label">{{ __('Username') }}</label>
                                </div>

                                <div class="input-group">
                                    <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="off" maxlength="25" autofocus>

                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

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
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="off">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <div>
                                    <label for="password-confirm" class="col-form-label">{{ __('Confirm Password') }}</label>
                                </div>

                                <div class="input-group">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary btn-block">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </noscript>
                        <div id="app">
                            <register-form></register-form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
