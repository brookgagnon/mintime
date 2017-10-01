
/* Helper Functions */

Date.prototype.formatMT = function(format)
{
  var date = this;
  var string = '';

  string += date.getFullYear();
  string += '-';
  string += ('0'+(date.getMonth()+1)).slice(-2);
  string += '-';
  string += ('0'+date.getDate()).slice(-2);

  string += ' ';

  string += ('0'+date.getHours()).slice(-2);
  string += ':';
  string += ('0'+date.getMinutes()).slice(-2);
  string += ':';
  string += ('0'+date.getSeconds()).slice(-2);

  return string;
}

template = function(destination_id, template_name, data)
{
  var template = $('#'+template_name+'-template').html();
  $('#'+destination_id).html( Mustache.to_html(template, data) );
  $('.scrollable').mCustomScrollbar({ scrollInertia: 300 });
}

// source: https://stackoverflow.com/questions/8900587/jquery-serializeobject-is-not-a-function-only-in-firefox
$.fn.serializeObject = function()
{
   var o = {};
   var a = this.serializeArray();
   $.each(a, function() {
       if (o[this.name]) {
           if (!o[this.name].push) {
               o[this.name] = [o[this.name]];
           }
           o[this.name].push(this.value || '');
       } else {
           o[this.name] = this.value || '';
       }
   });
   return o;
};


/* Main Application */

MT = {};

MT.init = function()
{
  MT.Tasks.get();
  $('body').on('change','#task_edit input', MT.Tasks.save);
  $('body').on('change','#task_edit select', MT.Tasks.save);
  $('body').on('change','#log_edit input', MT.Log.save);

  $('#top').resizable({'handles': 's'});
  $('#q1c').resizable({'handles': 'e'});
  $('#q3c').resizable({'handles': 'e'});
}

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
    template('q1', 'tasks',data);
    MT.Tasks.running();
  },'json');
}

MT.Tasks.new = function()
{
  $.get('index.php/ajax/task_new',function(id)
  {
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

    template('q3', 'log', {'log': task.log});
    template('q2', 'task_details', task.data);

    $('#task_edit select[name=currency]').val(task.data.currency); // TODO do in template?
    $('#task_edit select[name=invoiced]').val(task.data.invoiced ? 1 : 0); // TODO do in template?

    MT.Tasks.running();
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

  $.post('index.php/ajax/task_edit/'+MT.Tasks.id, input, function(data)
  {
    MT.Tasks.get();
    MT.Tasks.open();
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

MT.Tasks.running = function()
{
  $.get('index.php/ajax/task_running/',function(data)
  {
    $('#tasks [data-id]').removeClass('running');
    $('#log [data-id]').removeClass('running');
    if(data)
    {
      $('#tasks [data-id='+data.task+']').addClass('running');
      $('#log [data-id='+data.log+']').addClass('running');
    }
  },'json');
}

MT.Log = {};

MT.Log.id = null;

MT.Log.new = function()
{
  $.get('index.php/ajax/task_start/'+MT.Tasks.id, function(id)
  {
    MT.Log.id = id;
    MT.Tasks.open();
    MT.Log.open();
    MT.Tasks.running();
  },'json');
}

MT.Log.stop = function()
{
  $.get('index.php/ajax/task_stop',function()
  { 
    MT.Log.open();
    MT.Tasks.running();
  },'json');
}

MT.Log.open = function(id)
{
  if(id) MT.Log.id = id;

  $.get('index.php/ajax/log/'+MT.Log.id, function(data)
  {
    data.start = new Date(data.start*1000).formatMT();
    if(data.end) data.end = new Date(data.end*1000).formatMT()

    template('q4', 'log_details', data);
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

  $.post('index.php/ajax/log_edit/'+MT.Log.id, input, function(data)
  {
    MT.Tasks.open();
    MT.Log.open();
  },'json');
}

MT.Log.delete = function()
{
  if(confirm('Delete this entry?'))
  {
    $.get('index.php/ajax/log_delete/'+MT.Log.id, function(data)
    {
      MT.Tasks.open();
      MT.Log.close();
    },'json');
  }
}

MT.stats = function()
{
  var postdata = {};
  postdata['today'] = strtotime('00:00:00');
  postdata['week'] = strtotime('-7 days');
  postdata['month'] = strtotime('-1 month');
  postdata['year'] = strtotime('-1 year');

  $.post('index.php/ajax/stats',postdata,function(data)
  {
    template('q2', 'stats', data);
  },'json');
}

$(document).ready(function() { MT.init(); });
