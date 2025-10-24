@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Create Alliance</h2>
                    </div>
                    <div class="c-left"></div>
                    <div class="c-right"></div>

                    <div class="alliance_wrapper">
                        <div class="allianceContent">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('alliance.store') }}" method="POST">
                                @csrf
                                <table class="createnote createALLY">
                                    <tbody>
                                        <tr>
                                            <td class="desc">Alliance Tag (3-8 characters)</td>
                                            <td class="value">
                                                <input class="text w200" type="text" name="tag" id="allyTagField" maxlength="8" value="{{ old('tag') }}" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="desc">Alliance Name (3-64 characters)</td>
                                            <td class="value">
                                                <input class="text w200" type="text" name="name" id="allyNameField" maxlength="64" value="{{ old('name') }}" required>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="desc">Description (optional)</td>
                                            <td class="value">
                                                <textarea name="description" rows="5" cols="50">{{ old('description') }}</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="desc">Logo URL (optional)</td>
                                            <td class="value">
                                                <input class="text w200" type="url" name="logo" value="{{ old('logo') }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="desc">Website URL (optional)</td>
                                            <td class="value">
                                                <input class="text w200" type="url" name="external_url" value="{{ old('external_url') }}">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="desc">Application Text (optional)</td>
                                            <td class="value">
                                                <textarea name="application_text" rows="3" cols="50">{{ old('application_text') }}</textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="center">
                                                <button type="submit" class="action btn_blue">Create Alliance</button>
                                                <a href="{{ route('alliance.index') }}" class="action btn_grey">Cancel</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                    <div class="new_footer"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
