@extends('ingame.layouts.main')

@section('content')
    <div id="overviewcomponent" class="maincontent">
        <div id="planet" class="shortHeader">
            <h2>{{ $moduleName }} {{ __('t_helloworld.reference_module') }}</h2>
        </div>

        <div id="buttonz">
            <div class="content">
                <p class="box_highlight textCenter no_buddies">{{ $greeting }}</p>

                <p class="textCenter">
                    {{ __('t_helloworld.page_provided_by') }} <code>Modules/HelloWorld</code>. {{ __('t_helloworld.page_starting_point') }}
                </p>

            </div>
        </div>
    </div>
@endsection
