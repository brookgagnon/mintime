<div class="header">
  <h2>settings</h2>
  <div></div>
</div>

<form class="scrollable" id="settings_edit">
  <p><label>functional currency</label><select name="functional_currency">{{#currencies}}<option value="{{symbol}}">{{symbol}} ({{name}})</option>{{/currencies}}</select></p>
  <div id="settings_edit-currencies">
    <label>available currencies</label>
    {{#currencies}}<div><input type="checkbox" name="available_currencies" value="{{symbol}}" /> {{symbol}} ({{name}})</div>{{/currencies}}
  </div>
  <p><label>exchange fee (%)</label><input type="text" name="exchange_fee" /></p>
</form>
