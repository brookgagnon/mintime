MT.Log = {};

MT.Log.id = null;

MT.Log.list = function(log)
{
  var list = function(log)
  {
    MT.template('q3', 'log', {'log': log});
  }

  if(log) list(log);
  else MT.post('logs/get_for_task/'+MT.Tasks.id, list);
}

MT.Log.new = function()
{
  MT.post('logs/start/'+MT.Tasks.id, function(entry)
  {
    MT.Log.id = entry.id;
    MT.Log.list();
    MT.Log.open(null, entry);
    MT.running();
  });
}

MT.Log.stop = function()
{
  MT.post('logs/stop/',function()
  { 
    MT.Log.open();
    MT.running();
  });
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
  else MT.post('logs/get_one/'+MT.Log.id, open);
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

  MT.post('logs/save/'+MT.Log.id, input, function(entry)
  {
    MT.Log.update(entry);
  });
}

MT.Log.delete = function()
{
  if(confirm('Delete this entry?'))
  {
    MT.post('logs/delete/'+MT.Log.id, function(satus)
    {
      $('#log [data-id='+MT.Log.id+']').remove();
      MT.Log.close();
      MT.running();
    });
  }
}
