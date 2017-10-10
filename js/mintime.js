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

MT.template = function(destination_id, template_name, data)
{
  var template = $('#'+template_name+'-template').html();
  $('#'+destination_id).html( Mustache.to_html(template, data) );
  $('.scrollable').mCustomScrollbar({ scrollInertia: 300 });
}

MT.running = function()
{
  $.post('index.php/mintime/running/',function(data)
  {
    $('#tasks [data-id]').removeClass('running');
    $('#log [data-id]').removeClass('running');
    if(data)
    {
      $('#tasks [data-id='+data.task_id+']').addClass('running');
      $('#log [data-id='+data.log_id+']').addClass('running');
    }
  },'json');
}

MT.stats = function()
{
  var postdata = {};
  postdata['today'] = strtotime('00:00:00');
  postdata['week'] = strtotime('-7 days');
  postdata['month'] = strtotime('-1 month');
  postdata['year'] = strtotime('-1 year');

  $.post('index.php/mintime/stats/',postdata,function(data)
  {
    MT.template('q2', 'stats', data);
  },'json');
}

$(document).ready(function() { MT.init(); });
