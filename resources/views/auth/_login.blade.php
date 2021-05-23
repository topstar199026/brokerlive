<!-- @extends('layouts.user')

@section('content')
<div class="ibox-content">
    <form class="m-t" role="form" method="post" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <input type="text" name="username" class="form-control" placeholder="Username" required="" autofocus>
        </div> 
        <div class="form-group">
            <input type="password" name="password" class="form-control  @error('password') is-invalid @enderror" placeholder="Password" required="" autocomplete="current-password">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="remember" value="1" /> &nbsp; {{ __('Remember Me') }}</label>
        </div>
        <button type="submit" class="btn btn-primary block full-width m-b">{{ __('Login') }}</button>

        <button id="asfdasdf" type="button" class="btn btn-primary block full-width m-b">{{ __('Login') }}</button>
        <a href="/authenticate/forgotpassword">
            <small>{{ __('Forgot Password?') }}</small>
        </a>
    </form>
</div>
@endsection -->
