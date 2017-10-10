MT.Log = {};

MT.Log.id = null;

MT.Log.get = function(log)
{
  var get = function(log)
  {
    MT.template('q3', 'log', {'log': log});
  }

  if(log) get(log);
  else $.post('index.php/logs/get_for_task/'+MT.Tasks.id, get, 'json');
}

MT.Log.new = function()
{
  $.post('index.php/logs/start/'+MT.Tasks.id, function(entry)
  {
    MT.Log.id = entry.id;
    MT.Log.get();
    MT.Log.open(null, entry);
    MT.running();
  },'json');
}

MT.Log.stop = function()
{
  $.post('index.php/logs/stop/',function()
  { 
    MT.Log.open();
    MT.running();
  },'json');
}

MT.Log.open = function(id, entry)
{
  if(id) MT.Log.id = id;
  else if(entry && entry.id) MT.Log.id = entry.id;

  var open = function(entry)
  {
    MT.template('q4', 'log_details', entry);
    MT.Log.update(entry);
  }

  if(entry) open(entry);
  else $.post('index.php/logs/get_one/'+MT.Log.id, open,'json');
}

// update log entry fields.
// technically better handled by templte system, though this prevents user from losing field focus/input when updating.
MT.Log.update = function(entry)
{
  // make sure log entry name is up to date on log entry list.
  $('#log [data-id='+entry.id+']').text(entry.notes);

  // set values for log entry data.
  $.each(entry, function(name,value) { 
    if(name=='start' || name=='end') value = new Date(value*1000).formatMT();
    $('#log_edit [name='+name+']').val( typeof(value)=='boolean' ? +value : value );
    $('#log_edit [data-name='+name+']').text( value );
  });

  // show or hide stop link, end field.
  if(!entry.end) $('#log_edit-end').hide();
  else $('#log_edit-stop').hide();
}

MT.Log.close = function()
{
  $('#q4').html('');
  MT.Log.id = null;
}

MT.Log.save = function()
{
  var input = $('#log_edit').serializeObject();

  input['start'] = strtotime(input['start']);
  if(input['end']) input['end'] = strtotime(input['end']);

  $.post('index.php/logs/save/'+MT.Log.id, input, function(entry)
  {
    MT.Log.update(entry);
  },'json');
}

MT.Log.delete = function()
{
  if(confirm('Delete this entry?'))
  {
    $.post('index.php/logs/delete/'+MT.Log.id, function(satus)
    {
      $('#log [data-id='+MT.Log.id+']').remove();
      MT.Log.close();
      MT.running();
    },'json');
  }
}
