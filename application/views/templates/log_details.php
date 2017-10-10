<div class="header">
  <h2>log details</h2>
  <div>
    <span id="log_edit-stop"><a href="#" onClick="MT.Log.stop(); return false;">stop</a> &nbsp; </span>
    <a href="#" onClick="MT.Log.delete(); return false;">delete</a>
  </div>
</div>

<form id="log_edit" class="scrollable" data-id="{{id}}" data-task_id="{{task_id}}">
  <div><label>notes</label> <input name="notes" type="text" /></div>
  <div><label>start</label> <input name="start" type="text" /></div>
  <div id="log_edit-end"><label>end</label> <input name="end" type="text" /></div>

  <p><label>time</label> <span data-name="time"></span></p>
  <p><label>amount</label> <span data-name="amount"></span></p>
</form>

</div>
