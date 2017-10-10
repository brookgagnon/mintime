<div class="header">
  <h2>task details</h2>
  <div>
    <span id="task_edit-not_archived"><a href="#" onClick="MT.Tasks.archive(); return false;">archive</a></span>
    <span id="task_edit-is_archived"><a href="#" onClick="MT.Tasks.unarchive(); return false;">un-archive</a></span>
    &nbsp; <a href="#" onClick="MT.Tasks.delete(); return false;">delete</a>
  </div>
</div>

<form class="scrollable" id="task_edit">
  <div><label>name</label> <input name="name" type="text" /></div>
  <div><label>budget</label> <input name="budget" type="text" /></div>
  <div><label>rate</label> <input name="rate" type="text" /></div>
  <div><label>currency</label> <select data-link="currency" name="currency"><option>CAD</option><option>USD</option></select></div>
  <div><label>invoiced</label> <select name="invoiced"><option value="1">Yes</option><option value="0">No</option></select></div>
  
  <p><label>time</label> <span data-name="time"></span></p>
  <p><label>amount</label> <span data-name="amount"></span></p>
</form>
