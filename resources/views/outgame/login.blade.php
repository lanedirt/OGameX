@extends('outgame.layouts.main')

@section('content')

<div id="menu">
    <ul id="tabs">
        <li><a id="tab1" href="#tabContentContainer">Home</a></li>
        <li><a id="tab2" href="#">About OGame</a></li>
        <li><a id="tab3" href="#">Media</a></li>
    </ul>
    <a id="tab4" href="http://wiki.ogame.org" target="_blank">Wiki</a>
    <br class="clearfloat" />
    <div id="tabContentContainer">
        <div class="tabContent">
            <div id="ajaxContent">
                <div class="inner-box clearfix">
                    <h2>OGame - Conquer the universe</h2>

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

                    <p><em>OGame</em> is a strategy game set in space, with thousands of players from across the world competing at the same time. You only need a regular web browser to play.</p>
                    <a href="#"
                       target="_blank"
                       class="button"
                    >Board</a>
                </div>

                <div id="trailer" class="inner-box last clearfix">
                    <h2 id="trailer">Trailer</h2>
                    <div id="flashTrailer">
                        <iframe width="425" height="270" src="https://www.youtube.com/embed/Pb6Pgoxajqg?controls=0" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>                        </div>
    <div id="contentFooter"></div>
</div>

@endsection