@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Manage Alliance</h2>
                        <a class="toggleHeader" href="javascript:void(0);" data-name="alliance">
                            <img alt="" src="/img/icons/3e567d6f16d040326c7a0ea29a4f41.gif" height="22" width="22">
                        </a>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            <div class="contentz">
                                @if(session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if(session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('alliance.update') }}" method="POST">
                                    @csrf
                                    <table class="createnote createALLY">
                                        <tbody>
                                            <tr>
                                                <td class="desc">Alliance Name</td>
                                                <td class="value"><input type="text" name="name" class="text w200" value="{{ old('name', $alliance->name) }}" required></td>
                                            </tr>
                                            <tr>
                                                <td class="desc">Description</td>
                                                <td class="value"><textarea name="description" rows="5" cols="50">{{ old('description', $alliance->description) }}</textarea></td>
                                            </tr>
                                            <tr>
                                                <td class="desc">Logo URL</td>
                                                <td class="value"><input type="url" name="logo" class="text w200" value="{{ old('logo', $alliance->logo) }}"></td>
                                            </tr>
                                            <tr>
                                                <td class="desc">Website URL</td>
                                                <td class="value"><input type="url" name="external_url" class="text w200" value="{{ old('external_url', $alliance->external_url) }}"></td>
                                            </tr>
                                            <tr>
                                                <td class="desc">Internal Text</td>
                                                <td class="value"><textarea name="internal_text" rows="5" cols="50">{{ old('internal_text', $alliance->internal_text) }}</textarea></td>
                                            </tr>
                                            <tr>
                                                <td class="desc">Application Text</td>
                                                <td class="value"><textarea name="application_text" rows="3" cols="50">{{ old('application_text', $alliance->application_text) }}</textarea></td>
                                            </tr>
                                            <tr>
                                                <td class="desc">Open for Applications</td>
                                                <td class="value">
                                                    <input type="checkbox" name="open_for_applications" value="1" {{ old('open_for_applications', $alliance->open_for_applications) ? 'checked' : '' }}>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" align="center">
                                                    <button type="submit" class="action btn_blue">Update Alliance</button>
                                                    <a href="{{ route('alliance.index') }}" class="action btn_grey">Back to Alliance</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
