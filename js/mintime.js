MT = {};

MT.init = function()
{
  MT.Tasks.list();
  $('body').on('change','#task_edit input', MT.Tasks.save);
  $('body').on('change','#task_edit select', MT.Tasks.save);
  $('body').on('change','#log_edit input', MT.Log.save);

  $('#top').resizable({'handles': 's'});
  $('#q1c').resizable({'handles': 'e'});
  $('#q3c').resizable({'handles': 'e'});
}

MT.template = function(destination_id, template_name, data)
{
  var template = $('#'+template_name+'-template').html();
  $('#'+destination_id).html( Mustache.to_html(template, data) );
  $('.scrollable').mCustomScrollbar({ scrollInertia: 300 });
}

MT.post = function(url, arg1, arg2)
{
  if(typeof(arg2)=='undefined')
  {
    var data = {};
    var callback = arg1;
  }
  else
  {
    var data = arg1;
    var callback = arg2;
  }

  data.csrf_token = getCookie('csrf_token');

  return $.post('index.php/'+url, data, callback, 'json');
}

MT.running = function()
{
  MT.post('mintime/running/',function(data)
  {
    $('#tasks [data-id]').removeClass('running');
    $('#log [data-id]').removeClass('running');
    if(data)
    {
      $('#tasks [data-id='+data.task_id+']').addClass('running');
      $('#log [data-id='+data.log_id+']').addClass('running');
    }
  });
}

MT.stats = function()
{
  var postdata = {};
  postdata['today'] = strtotime('00:00:00');
  postdata['week'] = strtotime('-7 days');
  postdata['month'] = strtotime('-1 month');
  postdata['year'] = strtotime('-1 year');

  MT.post('mintime/stats/',postdata,function(data)
  {
    MT.template('q2', 'stats', data);
  });
}

$(document).ready(function() { MT.init(); });
