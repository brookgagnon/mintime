MT.Tasks = {};

MT.Tasks.id = null;
MT.Tasks.showArchived = false;

MT.Tasks.archivedToggle = function()
{
  MT.Tasks.showArchived = !MT.Tasks.showArchived;
  MT.Tasks.get();
}

MT.Tasks.get = function()
{
  $.get('index.php/tasks/get_all/'+(MT.Tasks.showArchived ? 1 : 0), function(tasks)
  {
    var data = {'tasks': tasks, 'archived': MT.Tasks.showArchived};
    MT.template('q1', 'tasks',data);
    MT.running();
  },'json');
}

MT.Tasks.new = function()
{
  $.get('index.php/tasks/new/',function(task)
  { 
    MT.Tasks.showArchived = false;
    MT.Tasks.get();
    MT.Tasks.open(null, task);
    MT.Log.close();
  },'json');
}

MT.Tasks.open = function(id, task)
{
  if(id) MT.Tasks.id = id;
  else if(task && task.id) MT.Tasks.id = task.id;

  var open = function(task)
  {
    MT.Log.close();

    MT.template('q3', 'log', {'log': task.log});
    MT.template('q2', 'task_details', task.data);

    MT.Tasks.update(task.data);

    MT.running();
  }

  if(task) open(task);
  else $.post('index.php/tasks/get_one/'+MT.Tasks.id, open, 'json');
}

// update task details fields
// technically better handled by template system, though this prevents user from losing field focus/input when updating data.
MT.Tasks.update = function(data)
{
  // make sure task name in task list is up to date.
  $('#tasks [data-id='+data.id+']').text(data.name);

  // set the value of the task details fields
  $.each(data, function(name,value) { 
    $('#task_edit [name='+name+']').val( typeof(value)=='boolean' ? +value : value );
    $('#task_edit [data-name='+name+']').text(value);
  });
  
  // show/hide (un-)archive links
  $('#task_edit-is_archived').toggle(data.archived);
  $('#task_edit-not_archived').toggle(!data.archived);
}

MT.Tasks.close = function()
{
  $('#q3').html('');
  $('#q2').html('');
  MT.Log.close();
  MT.Tasks.id = null;
}

MT.Tasks.save = function()
{
  var input = $('#task_edit').serializeObject();

  $.post('index.php/tasks/save/'+MT.Tasks.id, input, function(task)
  {
    MT.Tasks.update(task.data);
  }, 'json');
}

MT.Tasks.archive = function()
{
  if( confirm('Archive this task?') )
  {
    $.post('index.php/tasks/save/'+MT.Tasks.id, {'archived': 1}, function(task)
    {
      MT.Tasks.update(task.data);
      MT.Tasks.get();
    },'json');
  }
}

MT.Tasks.unarchive = function()
{
  if( confirm('Un-archive this task?') )
  {
    $.post('index.php/tasks/save/'+MT.Tasks.id, {'archived': 0}, function(task)
    {
      MT.Tasks.update(task.data);
      MT.Tasks.get();
    },'json');
  }
}

MT.Tasks.delete = function()
{
  if(confirm('Delete this task?'))
  {
    $.post('index.php/tasks/delete/'+MT.Tasks.id, function(data)
    {
      MT.Tasks.close();
      MT.Tasks.get();
    },'json');
  }
}
