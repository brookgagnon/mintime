MT.Log = {};

MT.Log.id = null;

MT.Log.new = function()
{
  $.get('index.php/logs/start/'+MT.Tasks.id, function(id)
  {
    MT.Log.id = id;
    MT.Tasks.open();
    MT.Log.open();
    MT.running();
  },'json');
}

MT.Log.stop = function()
{
  $.get('index.php/logs/stop/',function()
  { 
    MT.Log.open();
    MT.running();
  },'json');
}

MT.Log.open = function(id)
{
  if(id) MT.Log.id = id;

  $.get('index.php/logs/get_one/'+MT.Log.id, function(data)
  {
    data.start = new Date(data.start*1000).formatMT();
    if(data.end) data.end = new Date(data.end*1000).formatMT()

    MT.template('q4', 'log_details', data);
    if(!data.end) $('#log_edit-end').hide();
    else $('#log_edit-stop').hide();
  },'json');
}

MT.Log.close = function()
{
  $('#q4').html('');
}

MT.Log.save = function()
{
  var input = $('#log_edit').serializeObject();

  input['start'] = strtotime(input['start']);
  if(input['end']) input['end'] = strtotime(input['end']);

  $.post('index.php/logs/save/'+MT.Log.id, input, function(data)
  {
    $('#log [data-id='+data.id+']').text(data.notes);

    $.each(data, function(name,value) { 
      if(name=='start' || name=='end') value = new Date(value*1000).formatMT();
      $('#log_edit [name='+name+']').val( typeof(value)=='boolean' ? +value : value );
      $('#log_edit [data-name='+name+']').text( value );
    });
  },'json');
}

MT.Log.delete = function()
{
  if(confirm('Delete this entry?'))
  {
    $.get('index.php/logs/delete/'+MT.Log.id, function(data)
    {
      MT.Tasks.open();
      MT.Log.close();
    },'json');
  }
}
