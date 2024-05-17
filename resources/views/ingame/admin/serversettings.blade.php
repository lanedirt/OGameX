@extends('ingame.layouts.main')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div id="buddiescomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>Server settings</h2>
        </div>

        <div id="buttonz">
            <div class="header">
                <h2>Server settings</h2>
            </div>
            <div class="content">
                <div class="buddylistContent">
                    <p class="box_highlight textCenter no_buddies">Server settings here</p>
                    <span></span>
                </div>

                <div class="footer"></div>
            </div>
        </div>
        <script language="javascript">
            initBBCodes();
            initOverlays();
        </script>
    </div>

@endsection
