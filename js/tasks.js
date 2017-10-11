MT.Tasks = {};

MT.Tasks.id = null;
MT.Tasks.showArchived = false;

MT.Tasks.archivedToggle = function()
{
  MT.Tasks.showArchived = !MT.Tasks.showArchived;
  MT.Tasks.list();
}

MT.Tasks.list = function()
{
  MT.post('tasks/get_all/'+(MT.Tasks.showArchived ? 1 : 0), function(tasks)
  {
    var data = {'tasks': tasks, 'archived': MT.Tasks.showArchived};
    MT.template('q1', 'tasks',data);
    MT.running();
  });
}

MT.Tasks.new = function()
{
  MT.post('tasks/new/',function(task)
  { 
    MT.Tasks.showArchived = false;
    MT.Tasks.list();
    MT.Tasks.open(null, task);
    MT.Log.close();
  });
}

MT.Tasks.open = function(id, task)
{
  if(id) MT.Tasks.id = id;
  else if(task && task.data.id) MT.Tasks.id = task.data.id;

  var open = function(task)
  {
    MT.Log.close();
    MT.Log.list(task.log);

    MT.template('q2', 'task_details', task.data);

    MT.Tasks.update(task.data);

    MT.running();
  }

  if(task) open(task);
  else MT.post('tasks/get_one/'+MT.Tasks.id, open);
}

// update task details fields.
// technically better handled by template system, though this prevents user from losing field focus/input when updating.
MT.Tasks.update = function(task)
{
  // make sure task name in task list is up to date.
  $('#tasks [data-id='+task.id+']').text(task.name);

  // set values for task data.
  $.each(task, function(name,value) { 
    $('#task_edit [name='+name+']').val( typeof(value)=='boolean' ? +value : value );
    $('#task_edit [data-name='+name+']').text(value);
  });
  
  // show/hide (un-)archive links
  $('#task_edit-is_archived').toggle(task.archived);
  $('#task_edit-not_archived').toggle(!task.archived);
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

  MT.post('index.php/tasks/save/'+MT.Tasks.id, input, function(task)
  {
    MT.Tasks.update(task.data);
  });
}

MT.Tasks.archive = function()
{
  if( confirm('Archive this task?') )
  {
    MT.post('index.php/tasks/save/'+MT.Tasks.id, {'archived': 1}, function(task)
    {
      MT.Tasks.update(task.data);
      MT.Tasks.list();
    });
  }
}

MT.Tasks.unarchive = function()
{
  if( confirm('Un-archive this task?') )
  {
    MT.post('index.php/tasks/save/'+MT.Tasks.id, {'archived': 0}, function(task)
    {
      MT.Tasks.update(task.data);
      MT.Tasks.list();
    });
  }
}

MT.Tasks.delete = function()
{
  if(confirm('Delete this task?'))
  {
    MT.post('index.php/tasks/delete/'+MT.Tasks.id, function(data)
    {
      $('#tasks [data-id='+MT.Tasks.id+']').remove();
      MT.Tasks.close();
    });
  }
}
