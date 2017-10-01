<div class="header">
  <h2>task details</h2>
  <div>
    <a href="#" onClick="MT.Tasks.archive(); return false;">{{#archived}}un-{{/archived}}archive</a> &nbsp;
    <a href="#" onClick="MT.Tasks.delete(); return false;">delete</a>
  </div>
</div>

<form class="scrollable" id="task_edit">
  <div><label>name</label> <input name="name" type="text" value="{{name}}" /></div>
  <div><label>budget</label> <input name="budget" type="text" value="{{budget}}" /></div>
  <div><label>rate</label> <input name="rate" type="text" value="{{rate}}" /></div>
  <div><label>currency</label> <select data-link="currency" name="currency"><option>CAD</option><option>USD</option></select></div>
  <div><label>invoiced</label> <select name="invoiced"><option value="1">Yes</option><option value="0">No</option></select></div>
  
  <p><label>time</label> {{time}}</p>
  <p><label>amount</label> {{amount}}</p>
</form>
