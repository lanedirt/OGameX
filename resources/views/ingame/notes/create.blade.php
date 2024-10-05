<div id="createNote" ref="{{ route('notes.ajax.create') }}">
    <form method="post" name="noticeForm" action="javascript:void(0);" rel="{{ route('notes.ajax.create') }}">
        <input type="hidden" name="id" value="{{ $noteId }}" />
        <input type="hidden" name="save" value="1" />
        <input type='hidden' name='_token' value='{{ csrf_token() }}' />
        <input type="hidden" name="randomId" value="{{ $noteId }}" />
        <table cellpadding="0" cellspacing="0" class="createnote">
            <tr>
                <th class="desc textRight">@lang('Your subject:')</th>
                <td class="value textLeft">
                    <input class="textInput w250" type="text" placeholder="@lang('Your subject')" value="{{ $subject }}" data-value="{{ $subject }}" name="noticeSubject" maxlength="30" />
                </td>
            </tr>
            <tr>
                <th class="desc textRight">@lang('Priority:')</th>
                <td class="value textLeft">
                    <select name="noticePrio" class="w250" data-value="{{ $priority }}">
                        <option value="1"{{ $priority == 1 ? ' selected' : '' }}>@lang('Important')</option>
                        <option value="2"{{ $priority == 2 ? ' selected' : '' }}>@lang('Normal')</option>
                        <option value="3"{{ $priority == 3 ? ' selected' : '' }}>@lang('Unimportant')</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th class="textTop textRight">@lang('Your message:')</th>
                <td class="value textLeft">
                    <textarea cols="120" rows="20" class="textBox text" name="noticeText" tabindex="3" data-max-length="5000" data-value="{{ $content }}">{{ $content }}</textarea>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <div class="fleft count">(<span class="cntChars">0</span> / 5000 characters)</div>
                    <input type="submit" class="btn_blue float_right" name="noticeSubmit" value="@lang('Save')" />
                </td>
            </tr>
        </table>
    </form>
</div>
<script type="text/javascript">
    var locaNotes = {"changesNotSaved":"@lang('The message has not been saved. All changes will be lost if you leave the page.')","questionSaveChanges":"@lang('Should the changes be saved?')"};
    initNotesForm();
</script>