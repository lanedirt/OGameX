<div id="createNote" ref="{{ route('notes.ajax.create') }}">
    <form method="post" name="noticeForm" action="javascript:void(0);" rel="{{ route('notes.ajax.create') }}">
        <input type="hidden" name="id" value="{{ $noteId }}" />
        <input type="hidden" name="save" value="1" />
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <input type="hidden" name="randomId" value="{{ $noteId }}" />
        <table cellpadding="0" cellspacing="0" class="createnote">
            <tr>
                <th class="desc textRight">{{ __('t_ingame.notes.your_subject') }}</th>
                <td class="value textLeft">
                    <input class="textInput w250" type="text" placeholder="{{ __('t_ingame.notes.subject_placeholder') }}" value="{{ $subject }}" data-value="{{ $subject }}" name="noticeSubject" maxlength="30" />
                </td>
            </tr>
            <tr>
                <th class="desc textRight">{{ __('t_ingame.notes.priority_label') }}</th>
                <td class="value textLeft">
                    <select name="noticePrio" class="w250" data-value="{{ $priority }}">
                        <option value="1"{{ $priority == 1 ? ' selected' : '' }}>{{ __('t_ingame.notes.priority_important') }}</option>
                        <option value="2"{{ $priority == 2 ? ' selected' : '' }}>{{ __('t_ingame.notes.priority_normal') }}</option>
                        <option value="3"{{ $priority == 3 ? ' selected' : '' }}>{{ __('t_ingame.notes.priority_unimportant') }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="textTop textRight">{{ __('t_ingame.notes.your_message') }}</th>
                <td class="value textLeft">
                    <textarea cols="120" rows="20" class="textBox text" name="noticeText" tabindex="3" data-max-length="5000" data-value="{{ $content }}">{{ $content }}</textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <div class="fleft count">(<span class="cntChars">0</span> / 5000 characters)</div>
                    <input type="submit" class="btn_blue float_right" name="noticeSubmit" value="{{ __('t_ingame.notes.save_btn') }}" />
                </td>
            </tr>
        </table>
    </form>
</div>
<script type="text/javascript">
    var locaNotes = {"changesNotSaved":"{{ __('t_ingame.notes.unsaved_warning') }}","questionSaveChanges":"{{ __('t_ingame.notes.save_question') }}"};
    initNotesForm();
</script>