@extends('layouts.user')

@section('content')
<div class="ibox-content">
    <form class="m-t" role="form" method="post" action="{{ route('forgot') }}">
        @csrf
        <h4>{{ __('Forgot password') }}</h4>
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="email" required="" autofocus>
        </div> 
        <button type="submit" class="btn btn-primary block full-width m-b">{{ __('Submit') }}</button>
        <a href="/login">
            <small>{{ __('Login?') }}</small>
        </a>
    </form>
</div>
@endsection
