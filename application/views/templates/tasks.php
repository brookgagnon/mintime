<div class="header">
  <h2>{{#archived}}archived{{/archived}}{{^archived}}current{{/archived}} tasks</h2>
  <div>
    <a href="#" onClick="MT.Tasks.archivedToggle(); return false;">toggle archived</a> &nbsp;
    <a href="#" onClick="MT.Tasks.new(); return false;">new task</a> &nbsp;
    <a href="#" onClick="MT.stats(); return false;">stats</a> &nbsp;
    <a href="#" onClick="MT.Settings.open(); return false;">settings</a>
  </div>
</div>

<div class="scrollable" id="tasks">
  {{#tasks}}
  <p data-id="{{id}}" onClick="MT.Tasks.open({{id}});">{{name}}</p>
  {{/tasks}}
</div>
