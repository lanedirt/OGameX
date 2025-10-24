@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Search Alliance</h2>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <form action="{{ route('alliance.search') }}" method="GET">
                                <div style="margin-bottom: 20px;">
                                    <input type="text" name="query" placeholder="Search by tag or name..." value="{{ $query }}" style="width: 300px; padding: 5px;">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>

                            @if($query)
                                <h3>Search Results for "{{ $query }}"</h3>

                                @if($results->isEmpty())
                                    <p>No alliances found matching your search.</p>
                                @else
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tag</th>
                                                <th>Name</th>
                                                <th>Founder</th>
                                                <th>Members</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($results as $alliance)
                                                <tr>
                                                    <td>[{{ $alliance->tag }}]</td>
                                                    <td>{{ $alliance->name }}</td>
                                                    <td>{{ $alliance->founder->username }}</td>
                                                    <td>{{ $alliance->members_count }}</td>
                                                    <td>
                                                        <a href="{{ route('alliance.show', $alliance->id) }}" class="btn btn-sm btn-info">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
