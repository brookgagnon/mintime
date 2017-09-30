<div class="header">
  <h2>log</h2>
  <div>
    <a href="#" onClick="MT.Log.new(); return false;">start</a>
  </div>
</div>

<div class="scrollable" id="log">
{{#log}}
  <p data-id="{{id}}" onClick="MT.Log.open({{id}});">{{notes}}</p>
{{/log}}
</div>
