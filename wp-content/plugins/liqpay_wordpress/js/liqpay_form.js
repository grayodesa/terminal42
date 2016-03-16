$js=jQuery.noConflict();

jQuery(function(){


jQuery(document).ready(function($js) {

if($js('.textarea.sel').attr('readonly')) {
  $js('.textarea.val').width(86);
}
else {
  $js('.textarea.val').width(74);
  $js('.textarea.sel').height(20);
 }
/*if($('input[readonly="readonly"]')){
   
}*/

//$js('.flag').removeClass("usd eur rub"); 

if (document.getElementsByName('menu').length) {
$js('.flag').addClass(document.getElementsByName('menu')[0].value.toLowerCase());
}


  $js('.textarea.sel').on('change', function() {

    if ( this.value == "UAH") {
     $js('.flag').removeClass("usd eur rub");
     $js('.flag').addClass("uah",  1500, "easeOutBounce");

  }
  else if  ( this.value == "USD") {
     $js('.flag').removeClass("uah eur rub");
     $js('.flag').addClass("usd",  1500, "easeOutBounce");
  }
  else if  ( this.value == "EUR") {
     $js('.flag').removeClass("uah usd rub");
     $js('.flag').addClass("eur",  1500, "easeOutBounce");
  }
  else if  ( this.value == "RUB") {
     $js('.flag').removeClass("uah usd eur");
     $js('.flag').addClass("rub", 1500, "easeOutBounce");
  }
    else  {
     $js('.flag').removeClass("usd eur rub"); 
     $js('.flag').addClass("uah");
  }
 });

});

});