@extends('ingame.layouts.main')

@section('content')
    <div id="alliancecomponent" class="maincontent">
        <div id="netz">
            <div id="alliance">
                <div id="inhalt">
                    <div id="planet" class="planet-header">
                        <h2>Manage Alliance</h2>
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

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <h3>Manage [{{ $alliance->tag }}] {{ $alliance->name }}</h3>

                            <form action="{{ route('alliance.update') }}" method="POST">
                                @csrf
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><strong>Alliance Name:</strong></td>
                                            <td><input type="text" name="name" value="{{ old('name', $alliance->name) }}" required class="text w200"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Description:</strong></td>
                                            <td><textarea name="description" rows="5" cols="50">{{ old('description', $alliance->description) }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Logo URL:</strong></td>
                                            <td><input type="url" name="logo" value="{{ old('logo', $alliance->logo) }}" class="text w200"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Website URL:</strong></td>
                                            <td><input type="url" name="external_url" value="{{ old('external_url', $alliance->external_url) }}" class="text w200"></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Internal Text:</strong></td>
                                            <td><textarea name="internal_text" rows="5" cols="50">{{ old('internal_text', $alliance->internal_text) }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Application Text:</strong></td>
                                            <td><textarea name="application_text" rows="5" cols="50">{{ old('application_text', $alliance->application_text) }}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Open for Applications:</strong></td>
                                            <td>
                                                <input type="checkbox" name="open_for_applications" value="1" {{ old('open_for_applications', $alliance->open_for_applications) ? 'checked' : '' }}>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="center">
                                                <button type="submit" class="btn btn-primary">Update Alliance</button>
                                                <a href="{{ route('alliance.index') }}" class="btn btn-secondary">Back to Alliance</a>
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
