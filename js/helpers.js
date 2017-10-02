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
