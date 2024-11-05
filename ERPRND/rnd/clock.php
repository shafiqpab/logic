<?
date_default_timezone_set('Asia/Dhaka');
?>

<script>

var ctoday = Date.parse('<?=date('D, d M Y H:i:s',time());?>');

function loadDoc() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
     ctoday = Date.parse(this.responseText)*1;
  }
  xhttp.open("GET", "date.php");
  xhttp.send();
}


loadDoc();







setInterval(function() {ctoday += 1000;},1000);
setInterval(function() {loadDoc();},1000*60);





function startTime() {
    var today = new Date(ctoday);
    var montharray = new Array("Jan","Feb","Mar","Abr","May","Jun","Jul","Ogu","Sep","Oct","Nov","Des");
    var h = today.getHours();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    h = h ? h : 12;
    var m = today.getMinutes();
    var s = today.getSeconds();
    h = checkTime(h);
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('txt').innerHTML =
    checkTime(today.getDate())+" "+montharray[today.getMonth()]+" "+today.getFullYear() + " (" + ampm +" " + h + ":" + m + ":" + s +")"; 
    setTimeout(startTime, 1000);
     
    
}

function checkTime(i) {
    if (i < 10) {i = "0" + i};
    return i;
}



</script>
<body onLoad="startTime();">
    <span id="txt"></span>
</body>
