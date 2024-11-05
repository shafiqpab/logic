<?

header('location: platform/');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
//echo load_html_head_contents("Order Info","", 1, 1, $unicode,1,'../');

echo load_html_head_contents("Planning Board","../", 1, 1, $unicode,'','');

//load_html_head_contents($title, $path, $filter, $popup, $unicode, $multi_select, $am_chart)
//w4821 --genuine win  8 hrs quad core procesor, 32 GB 28,500, 64GB 30,500

$cdate=date("dmY",time());

?>
<script src="plan_board.js" type="text/javascript"></script>
<link rel="stylesheet"  type="text/css" href="plan_board.css" >

</head>

<body>

<div id="plan_bar" class="testdiv" style=" height:15px; color:#EE44C4"></div>
<div id="plan_bar_partial" class="plan_crossed_bar" style="height:15px;"></div>

<span id="selection_line_vert"></span>
<span id="selection_line_hor"></span>
<center>
<table width="900" class="rpt_table" rules="all" border="0" cellpadding="0" cellspacing="0">
	<tr>
    	<td width="100" bgcolor="">Company Name
        <input type="hidden" id="txt_cdate">
        </td>
        <td width="150">
        	<? 
				echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and id=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", 1,"load_drop_down( 'requires/planning_board_controller', this.value, 'load_drop_down_location', 'location_td' );" );
			?>
        </td>
        <td width="100">Location Name</td>
        <td id="location_td" width="150">
        	<? 
				echo create_drop_down( "cbo_location_name", 150, $blank_array,"",1, "-- Select Location --",1 );
			?>
        </td>
        <td width="100">Plan Start Date</td>
        <td width="100">
        	<input type="text" class="datepicker" value="<? echo "10-03-2015"//date("d-m-Y",time()); ?>" readonly style="width:100px" id="txt_start_date">
        </td>
        <td height="35" valign="top">
        	<input type="button" name="generate" class="formbutton" style="width:120px" value="Generate Board" onClick="populate_line()" >
        </td>
        <td height="35" valign="middle">
        	<input type="text" class="text_boxes"   style="width:100px;" onDblClick="search_element()" id="txt_search_text">
        </td>
    </tr>
</table>
 
 <span id="plan_container"></span>
 
<table width="100%">
	<tr>
    	<td width="100%" align="center" valign="bottom">
        	<div id="footer" style=" position:fixed; opacity:0.8; z-index:900; width:950px; height:200px; background-color:#DEE9D8; bottom: 30px; ">
                             
            </div> 
        </td>
    </tr>
</table>
 
 
 </center>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
<script>
//id="common"
$.fn.scrollTo = function( target, options, callback ){
  if(typeof options == 'function' && arguments.length == 2){ callback = options; options = target; }
  var settings = $.extend({
    scrollTarget  : target,
    offsetTop     : 50,
    duration      : 500,
    easing        : 'swing'
  }, options);
  return this.each(function(){
    var scrollPane = $(this);
    var scrollTarget = (typeof settings.scrollTarget == "number") ? settings.scrollTarget : $(settings.scrollTarget);
    var scrollY = (typeof scrollTarget == "number") ? scrollTarget : scrollTarget.offset().top + scrollPane.scrollTop() - parseInt(settings.offsetTop);
    scrollPane.animate({scrollTop : scrollY }, parseInt(settings.duration), settings.easing, function(){
      if (typeof callback == 'function') { callback.call(this); }
    });
  });
}
var gblcount=0;
var gblsearch=Array();
function search_element()
{
	//scrollIntoView("tdbody-17-05062015","plan_container");
	//document.getElementById("tdbody-17-05062015").scrollIntoView();
	//document.getElementsByClassName('testdiv').scrollIntoView();
 
	var myList=document.getElementsByName( $('#txt_search_text').val())
	///D571230F  D571230D  D571230D   112328-7930
	for(var k=0; k<myList.length; k++)
	{
		
		if(!gblsearch[$('#txt_search_text').val()] )
		{
			//alert('first'+gblsearch[$('#txt_search_text').val()]);
			 gblsearch[$('#txt_search_text').val()]=1;
			 document.getElementById(myList[k].id).scrollIntoView();
			 return;
		}
		else if(gblsearch[$('#txt_search_text').val()]=="" || gblsearch[$('#txt_search_text').val()]==0)
		{ 
		//alert('2nd'+gblsearch[$('#txt_search_text').val()]);
			gblsearch.length=0;
			gblsearch[$('#txt_search_text').val()]=1;
			document.getElementById(myList[k].id).scrollIntoView();
			 return;
		}
		else
		{
			gblsearch[$('#txt_search_text').val()]=((gblsearch[$('#txt_search_text').val()]*1)+1)-1;
			//alert(myList.length+"=="+gblsearch[$('#txt_search_text').val()]+"=="+k);
			if( gblsearch[$('#txt_search_text').val()] == myList.length )
			{
				//k=gblsearch[$('#txt_search_text').val()]-1;
				k=gblsearch[$('#txt_search_text').val()]-1;
				gblsearch[$('#txt_search_text').val()]=0;
			}
			else k=gblsearch[$('#txt_search_text').val()];
			
			document.getElementById(myList[k].id).scrollIntoView();
			return;
		} 
		//document.getElementById(myList[k].id).scrollIntoView();
		//	 return;
		
		
	}
}

function scrollIntoView(element, container) {
  var containerTop = $(container).scrollTop(); 
  var containerBottom = containerTop + $(container).height(); 
  var elemTop = element.offsetTop;
  var elemBottom = elemTop + $(element).height(); 
  if (elemTop < containerTop) {
    $(container).scrollTop(elemTop);
  } else if (elemBottom > containerBottom) {
    $(container).scrollTop(elemBottom - $(container).height());
  }
}
 
</script>

</html>

 