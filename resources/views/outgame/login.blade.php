@extends('outgame.layouts.main')

@section('content')

<div id="menu">
    <ul id="tabs">
        <li><a id="tab1" href="#tabContentContainer">{{ __('t_external.nav.home') }}</a></li>
        <li><a id="tab2" href="#">{{ __('t_external.nav.about') }}</a></li>
        <li><a id="tab3" href="#">{{ __('t_external.nav.media') }}</a></li>
    </ul>
    <a id="tab4" href="http://wiki.ogame.org" target="_blank">{{ __('t_external.nav.wiki') }}</a>
    <br class="clearfloat" />
    <div id="tabContentContainer">
        <div class="tabContent">
            <div id="ajaxContent">
                <div class="inner-box clearfix">
                    <h2>{{ __('t_external.home.title') }}</h2>

                    @if ($errors->has('email'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                    @endif

                    @if ($errors->has('password'))
                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                    @endif

                    <p>{!! __('t_external.home.description_html') !!}</p>
                    <a href="#"
                       target="_blank"
                       class="button"
                    >{{ __('t_external.home.board_btn') }}</a>
                </div>

                <div id="trailer" class="inner-box last clearfix">
                    <h2 id="trailer">{{ __('t_external.home.trailer_title') }}</h2>
                    <div id="flashTrailer">
                        <iframe width="425" height="270" src="https://www.youtube.com/embed/Pb6Pgoxajqg?controls=0" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>                        </div>
    <div id="contentFooter"></div>
</div>

@endsection
