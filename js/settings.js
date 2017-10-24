MT.Settings = {};

MT.Settings.open = function()
{
  MT.post('settings/get', function(data)
  {
    MT.template('q2','settings',data);
    MT.Settings.update(data);
  });
}

MT.Settings.update = function(data)
{
  // set functional currency
  $('#settings_edit select[name=functional_currency]').val(data.functional_currency);

  // set available currencies
  $('#settings_edit input[type=checkbox]').prop('checked',false);
  $.each(data.available_currencies, function(index, currency)
  {
    $('#settings_edit input[type=checkbox][value='+currency+']').prop('checked',true);
  });

  // set exchange fee
  $('#settings_edit input[name=exchange_fee]').val(data.exchange_fee);
}

MT.Settings.save = function()
{
  var input = $('#settings_edit').serializeObject();

  MT.post('settings/save', input, function(data)
  {
    MT.Settings.update(data);
  });
}
