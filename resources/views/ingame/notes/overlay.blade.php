<div id="noteList" rel="{{ route('notes.overlay') }}">
    <a href="{{ route('notes.view') }}" class="btn_blue openOverlay" data-title="@lang('Add note')" data-overlay-class="newNote-1" id="newNote">
        <span>+ @lang('New Note')</span>
    </a> 
    <form action="javascript:void(0);" method="post">
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <table cellspacing="0" cellpadding="0" id="notizen">
            <thead>
            <tr>
                <th class="spacer">
                </th><th class="subject">@lang('Subject')</th>
                <th class="date">@lang('Date')</th>
            </tr>
            </thead>
            <tbody>
                @if(count($notes) > 0)
                    @foreach($notes as $index => $note)
                        <tr class="{{ $index % 2 == 0 ? 'alt' : '' }}">
                            <td width="30">
                                <input type="checkbox" value="{{ $note->id }}" name="delIds[]">
                            </td>
                            <td class="subject">
                                <a href="{{ route('notes.view', ['id' => $note->id]) }}" class="openOverlay" data-title="@lang('Edit note')" data-overlay-class="note-{{ $note->id }}">
                                    <span class="{{ $note->priority === 1 ? 'overmark' : ($note->priority === 2 ? 'undermark' : '') }}">{{ $note->subject }}</span>
                                </a>
                            </td>
                            <td class="date">{{ $note->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr class="alt">
                        <td colspan="4" align="center">No notes found</td>
                    </tr>
                @endif
            </tbody>
            @if(count($notes) > 0)
            <tfoot>
                <tr class="last-tr">
                    <td colspan="2" class="options">
                        <select name="noticeDeleteMethode" class="choose">
                            <option value="0">@lang('Select action')</option>
                            <option value="1">@lang('Delete marked notes')</option>
                            <option value="2">@lang('Delete all notes')</option>
                        </select>
                        <input type="submit" value="Ok" name="delNow" class="btn_blue buttonOK">
                    </td>
                    <td align="right">
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </form>
</div>
<script type="text/javascript">
    var locaNotes = {"changesNotSaved":"@lang('The message has not been saved. All changes will be lost if you leave the page.')","questionSaveChanges":"@lang('Should the changes be saved?')"};
    initNotes();
    @if (isset($success) && !empty($success))
    $(document).ready(function () {
        fadeBox("{!! $success !!}", 0);
    });
    @endif
</script>