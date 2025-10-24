@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>[{{ $alliance->tag }}] {{ $alliance->name }}</h2>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            @if($alliance->logo)
                                <div class="alliance-logo" style="margin-bottom: 20px;">
                                    <img src="{{ $alliance->logo }}" alt="{{ $alliance->name }} Logo" style="max-width: 200px;">
                                </div>
                            @endif

                            <div class="alliance-info">
                                <p><strong>Tag:</strong> [{{ $alliance->tag }}]</p>
                                <p><strong>Name:</strong> {{ $alliance->name }}</p>
                                <p><strong>Founder:</strong> {{ $alliance->founder->username }}</p>
                                <p><strong>Members:</strong> {{ $memberCount }}</p>

                                @if($alliance->external_url)
                                    <p><strong>Website:</strong> <a href="{{ $alliance->external_url }}" target="_blank">{{ $alliance->external_url }}</a></p>
                                @endif
                            </div>

                            @if($alliance->description)
                                <div class="alliance-description" style="margin-top: 20px;">
                                    <h4>Description</h4>
                                    <p>{{ $alliance->description }}</p>
                                </div>
                            @endif

                            @if($alliance->application_text && !$isMember)
                                <div class="alliance-application-text" style="margin-top: 20px;">
                                    <h4>Application Information</h4>
                                    <p>{{ $alliance->application_text }}</p>
                                </div>
                            @endif

                            <div class="alliance-actions" style="margin-top: 30px;">
                                @if($isMember)
                                    <a href="{{ route('alliance.index') }}" class="btn btn-primary">View Your Alliance</a>
                                @elseif($hasPendingApplication)
                                    <p class="alert alert-info">You have a pending application to this alliance.</p>
                                @elseif($canApply)
                                    <h4>Apply to this Alliance</h4>
                                    <form action="{{ route('alliance.apply', $alliance->id) }}" method="POST">
                                        @csrf
                                        <div>
                                            <label>Application Message (optional):</label><br>
                                            <textarea name="application_text" rows="5" cols="50"></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">Submit Application</button>
                                    </form>
                                @else
                                    <p class="alert alert-warning">This alliance is not currently accepting applications.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
