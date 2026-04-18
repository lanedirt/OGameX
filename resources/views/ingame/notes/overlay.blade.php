<div id="noteList" rel="{{ route('notes.overlay') }}">
    <a href="{{ route('notes.view') }}" class="btn_blue openOverlay" data-title="{{ __('t_ingame.notes.add_note') }}" data-overlay-class="newNote-1" id="newNote">
        <span>+ {{ __('t_ingame.notes.new_note') }}</span>
    </a> 
    <form action="javascript:void(0);" method="post">
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <table cellspacing="0" cellpadding="0" id="notizen">
            <thead>
            <tr>
                <th class="spacer">
                </th><th class="subject">{{ __('t_ingame.notes.subject_label') }}</th>
                <th class="date">{{ __('t_ingame.notes.date_label') }}</th>
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
                                <a href="{{ route('notes.view', ['id' => $note->id]) }}" class="openOverlay" data-title="{{ __('t_ingame.notes.edit_note') }}" data-overlay-class="note-{{ $note->id }}">
                                    <span class="{{ $note->priority === 1 ? 'overmark' : ($note->priority === 2 ? 'undermark' : '') }}">{{ $note->subject }}</span>
                                </a>
                            </td>
                            <td class="date">{{ $note->created_at->format('d.m.Y H:i:s') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr class="alt">
                        <td colspan="4" align="center">{{ __('t_ingame.notes.no_notes_found') }}</td>
                    </tr>
                @endif
            </tbody>
            @if(count($notes) > 0)
            <tfoot>
                <tr class="last-tr">
                    <td colspan="2" class="options">
                        <select name="noticeDeleteMethode" class="choose">
                            <option value="0">{{ __('t_ingame.notes.select_action') }}</option>
                            <option value="1">{{ __('t_ingame.notes.delete_marked') }}</option>
                            <option value="2">{{ __('t_ingame.notes.delete_all') }}</option>
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
    var locaNotes = {"changesNotSaved":"{{ __('t_ingame.notes.unsaved_warning') }}","questionSaveChanges":"{{ __('t_ingame.notes.save_question') }}"};
    initNotes();
    @if (isset($success) && !empty($success))
    $(document).ready(function () {
        fadeBox("{!! $success !!}", 0);
    });
    @endif
</script>