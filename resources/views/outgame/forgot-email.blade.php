@extends('outgame.layouts.main')

@section('content')

<div id="menu">
    <ul id="tabs">
        <li><a id="tab1" class="tabActive" href="#tabContentContainer">{{ __('t_external.nav.home') }}</a></li>
    </ul>
    <br class="clearfloat" />
    <div id="tabContentContainer">
        <div class="tabContent">
            <div id="ajaxContent">
                <div class="inner-box clearfix">
                    <h2>{{ __('t_external.forgot_email.title') }}</h2>
                    <p>{{ __('t_external.forgot_email.description') }}</p>

                    @if (session('status'))
                        <p class="help-block" style="color: #4caf50;">
                            {{ session('status') }}
                        </p>
                    @endif

                    <form method="POST" action="{{ route('password.email-lookup') }}">
                        {{ csrf_field() }}

                        <div class="input-wrap">
                            <label for="username">{{ __('t_external.forgot_email.username_label') }}</label>
                            <div class="black-border">
                                <input type="text"
                                       id="username"
                                       name="username"
                                       value="{{ old('username') }}"
                                />
                            </div>
                            @if ($errors->has('username'))
                                <span class="help-block" style="color: #e74c3c;">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif
                        </div>

                        <input type="submit" value="{{ __('t_external.forgot_email.submit') }}"/>
                        <br/>
                        <a href="{{ route('login') }}">{{ __('t_external.forgot_email.back_to_login') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="contentFooter"></div>
</div>

@endsection
