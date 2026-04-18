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
                    <h2>{{ __('t_external.reset_password.title') }}</h2>

                    <form method="POST" action="{{ route('password.update') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $request->route('token') }}"/>

                        <div class="input-wrap">
                            <label for="email">{{ __('t_external.reset_password.email_label') }}</label>
                            <div class="black-border">
                                <input type="text"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $request->email) }}"
                                />
                            </div>
                            @if ($errors->has('email'))
                                <span class="help-block" style="color: #e74c3c;">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="input-wrap">
                            <label for="password">{{ __('t_external.reset_password.password_label') }}</label>
                            <div class="black-border">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       maxlength="128"
                                />
                            </div>
                            @if ($errors->has('password'))
                                <span class="help-block" style="color: #e74c3c;">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="input-wrap">
                            <label for="password_confirmation">{{ __('t_external.reset_password.confirm_label') }}</label>
                            <div class="black-border">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       maxlength="128"
                                />
                            </div>
                        </div>

                        <input type="submit" value="{{ __('t_external.reset_password.submit') }}"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="contentFooter"></div>
</div>

@endsection
