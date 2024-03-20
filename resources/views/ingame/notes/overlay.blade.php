<div id="noteList" rel="#">
    <a href="#" class="btn_blue openOverlay" data-title="Add note" data-overlay-class="newNote-1" id="newNote">
        <span>+ New Note</span>
    </a>
    <form action="javascript:void(0);" method="post">
        <input type="hidden" name="token" value="b62002b60cc674937a9e5e0471e594c0">
        <table cellspacing="0" cellpadding="0" id="notizen">
            <thead>
            <tr>
                <th class="spacer">
                </th><th class="subject">Subject</th>
                <th class="date">Date</th>
            </tr>
            </thead>
            <tbody>
            <tr class="alt">
                <td width="30"><input type="checkbox" value="314" name="delIds[]"></td>
                <td class="subject">
                    <a href="#" class="openOverlay" data-title="Edit note" data-overlay-class="note-314">
                        <span class="undermark">attack pages</span>
                    </a>
                </td>
                <td class="date">18.03.2024 22:00:35</td>
            </tr>
            </tbody>
            <tfoot>
            <tr class="last-tr">
                <td colspan="2" class="options">
                    <select name="noticeDeleteMethode" class="choose dropdownInitialized" style="display: none;">
                        <option value="0">Select action</option>
                        <option value="1">Delete marked notes</option>
                        <option value="2">Delete all notes</option>
                    </select><span class="dropdown currentlySelected choose" rel="dropdown183" style="width: 250px;"><a class="undefined" data-value="0" rel="dropdown183" href="javascript:void(0);">Select action</a></span>
                    <input type="submit" value="Ok" name="delNow" class="btn_blue buttonOK">
                </td>
                <td align="right">
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script type="text/javascript">
    var locaNotes = {"changesNotSaved":"The message has not been saved. All changes will be lost if you leave the page.","questionSaveChanges":"Should the changes be saved?"};
    initNotes();
</script>