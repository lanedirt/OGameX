@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Search Alliance</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <div class="contentz">
                                <form action="{{ route('alliance.search') }}" method="GET">
                                    <table class="members" width="100%" cellpadding="0" cellspacing="1">
                                        <tr>
                                            <th colspan="2">Search for Alliance</th>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: center; padding: 15px;">
                                                <input type="text" name="query" class="text w290" placeholder="Search by tag or name..." value="{{ $query }}">
                                                <button type="submit" class="btn_blue">Search</button>
                                            </td>
                                        </tr>
                                    </table>
                                </form>

                                @if($query)
                                    @if($results->isEmpty())
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-top: 10px;">
                                            <tr>
                                                <th>Search Results for "{{ $query }}"</th>
                                            </tr>
                                            <tr>
                                                <td>No alliances found matching your search.</td>
                                            </tr>
                                        </table>
                                    @else
                                        <table class="members" width="100%" cellpadding="0" cellspacing="1" style="margin-top: 10px;">
                                            <tr>
                                                <th colspan="5">Search Results for "{{ $query }}"</th>
                                            </tr>
                                            <tr>
                                                <th>Tag</th>
                                                <th>Name</th>
                                                <th>Founder</th>
                                                <th>Members</th>
                                                <th>Actions</th>
                                            </tr>
                                            @foreach($results as $index => $alliance)
                                                <tr class="{{ $index % 2 == 1 ? 'alt' : '' }}">
                                                    <td>[{{ $alliance->tag }}]</td>
                                                    <td>{{ $alliance->name }}</td>
                                                    <td>{{ $alliance->founder->username }}</td>
                                                    <td>{{ $alliance->members_count }}</td>
                                                    <td>
                                                        <a href="{{ route('alliance.show', $alliance->id) }}" class="btn_blue" style="padding: 2px 8px;">View</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endif
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
