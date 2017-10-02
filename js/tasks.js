MT.Tasks = {};

MT.Tasks.id = null;
MT.Tasks.showArchived = false;

MT.Tasks.archivedToggle = function()
{
  MT.Tasks.showArchived = !MT.Tasks.showArchived;
  MT.Tasks.get();
  MT.Tasks.close();
  MT.Log.close();
}

MT.Tasks.get = function()
{
  $.get('index.php/ajax/tasks/'+(MT.Tasks.showArchived ? 1 : 0), function(tasks)
  {
    var data = {'tasks': tasks, 'archived': MT.Tasks.showArchived};
    MT.template('q1', 'tasks',data);
    MT.running();
  },'json');
}

MT.Tasks.new = function()
{
  $.get('index.php/ajax/task_new',function(id)
  { 
    MT.Tasks.showArchived = false;
    MT.Tasks.get();
    MT.Tasks.open(id);
    MT.Log.close();
  },'json');
}

MT.Tasks.open = function(id)
{
  if(id) MT.Tasks.id = id;

  $.get('index.php/ajax/task/'+MT.Tasks.id,function(task)
  {
    MT.Log.close();

    MT.template('q3', 'log', {'log': task.log});
    MT.template('q2', 'task_details', task.data);

    $('#task_edit select[name=currency]').val(task.data.currency); // TODO do in template?
    $('#task_edit select[name=invoiced]').val(task.data.invoiced ? 1 : 0); // TODO do in template?

    MT.running();
  }, 'json');
}

MT.Tasks.close = function()
{
  $('#q3').html('');
  $('#q2').html('');
  MT.Tasks.id = null;
}

MT.Tasks.save = function()
{
  var input = $('#task_edit').serializeObject();

  $.post('index.php/ajax/task_save/'+MT.Tasks.id, input, function(data)
  {
    $('#tasks [data-id='+data.id+']').text(data.name);

    $.each(data, function(name,value) { 
      $('#task_edit [name='+name+']').val( typeof(value)=='boolean' ? +value : value );
    });

  }, 'json');
}

MT.Tasks.archive = function()
{
  if(confirm( (MT.Tasks.showArchived ? 'Un-archive' : 'Archive') + ' this task?'))
  {
    $.get('index.php/ajax/task_archive/'+MT.Tasks.id, function(data)
    {
      MT.Tasks.get();
      MT.Tasks.close();
      MT.Log.close();
    },'json');
  }
}

MT.Tasks.delete = function()
{
  if(confirm('Delete this task?'))
  {
    $.get('index.php/ajax/task_delete/'+MT.Tasks.id, function(data)
    {
      MT.Tasks.get();
      MT.Tasks.close();
      MT.Log.close();
    },'json');
  }
}
