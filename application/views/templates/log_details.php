<div class="header">
  <h2>log details</h2>
  <div>
    <span id="log_edit-stop"><a href="#" onClick="MT.Log.stop(); return false;">stop</a> &nbsp; </span>
    <a href="#" onClick="MT.Log.delete(); return false;">delete</a>
  </div>
</div>

<form id="log_edit" class="scrollable" data-id="{{id}}" data-taskid="{{taskid}}">
  <div><label>notes</label> <input name="notes" type="text" value="{{notes}}" /></div>
  <div><label>start</label> <input name="start" type="text" value="{{start}}" /></div>
  <div id="log_edit-end"><label>end</label> <input name="end" type="text" value="{{end}}" /></div>

  <p><label>time</label> {{time}}</p>
  <p><label>amount</label> {{amount}}</p>
</form>

</div>
