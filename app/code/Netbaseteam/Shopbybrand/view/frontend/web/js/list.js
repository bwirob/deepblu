function reply_click(elem)
{
  var x = document.getElementsByClassName('abc')
    var i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
   var x = document.getElementsByClassName(elem.id)
   var i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "block";
    }
    var a =  document.getElementsByClassName('loadmore')
   a[0].style.display = 'none';
}

function numble(){
    var x = document.getElementsByClassName('abc')
    var b = document.getElementsByClassName('numble')
    var i;
    for (i = 0; i < x.length; i++) {
        
        x[i].style.display = "none";
    }
    for (i = 0; i < b.length; i++) {     
        b[i].style.display = "block";     
    }
   var a =  document.getElementsByClassName('loadmore')
   a[0].style.display = 'none';
} 

function viewall()
{
  var x = document.getElementsByClassName('abc')
    var i;
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "block";
    }
   var a =  document.getElementsByClassName('loadmore')
   a[0].style.display = 'none';
}
var i;
var b = 12;
  function loadmore(){
    var x = document.getElementsByClassName("abc")
    var a = document.getElementsByClassName("loadmore")
    if (b = x.length) {
        var d =  document.getElementsByClassName('loadmore')
        d[0].style.display = 'none';
    }
      if (b+12>x.length) { b = x.length-12;}
      for (i = b; i < b+12; i++) {
        x[i].style.display = "block"; 
      }
      b = b+12;    
  }
