@extends('ingame.layouts.main')

@section('content')
    <div id="overviewcomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>{{ $moduleName }} reference module</h2>
        </div>

        <div id="buttonz">
            <div class="content">
                <p class="box_highlight textCenter no_buddies">{{ $greeting }}</p>

                <p class="textCenter">
                    This page is provided by <code>Modules/HelloWorld</code>. Use it as a safe starting point for a new OGameX module.
                </p>

            </div>
        </div>
    </div>
@endsection
