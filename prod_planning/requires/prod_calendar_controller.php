<script>
function showResult_source(str,type,com_name,com_location,div)
{
var page="<?php echo $page;?>";
if (str.length==0)
  {
  document.getElementById(div).innerHTML="";
  document.getElementById(div).style.border="0px";
  return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
        document.getElementById(div).innerHTML=xmlhttp.responseText;   
    }
  }
// alert( type );
xmlhttp.open("GET","includes/list_view.php?search_string="+str+"&type="+type+"&com_name="+com_name+"&com_location="+com_location+'&page='+page,true);
xmlhttp.send();

}

</script>

<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0  order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );	
} 

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  	
} 
if ($action=="load_drop_down_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "",0 );
} 

if ($action=="order_popup")
{
?>


<form name="search_order_frm"  id="search_order_frm">
	<fieldset style="width:600px">
	<table width="600" cellspacing="2" cellpadding="0" border="0">
    	<tr>
				<td align="center">
				You Have Selected: 
                <textarea readonly="readonly" style="width:350px" class="text_area" name="txt_selected" id="txt_selected" ></textarea>
				<input type="hidden" readonly="readonly" style="width:250px" class="text_boxes" name="txt_selected_id" id="txt_selected_id" />
				</td>
		</tr>
		<tr>
				<td align="center">
				Search Line:
				<input type="text" name="search_text" id="search_text" class="text_boxes" style="width:150px" onkeyup="showResult_source(this.value,'search_line_info',document.getElementById('com_name').value,document.getElementById('com_location').value,'search_div_line')" autocomplete=off /><input type="hidden" name="txt_selected_line" id="txt_selected_line" /> 
				</td>
                <input type="hidden" name="com_name" id="com_name" value="<?php echo $cbo_company_name; ?>" />
            	<input type="hidden" name="com_location" id="com_location" value="<?php echo $cbo_location_name; ?>" />
		</tr>
		<tr>
				<td colspan="3">
				<div style="width:650px; overflow-y:scroll; min-height:260px; max-height:260px;" id="search_div_line" align="left">
					
				</div>
				</td>
        </tr>
        <tr>
				<td align="center" height="30" valign="bottom">
				<div style="width:100%"> 
					<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()" /> Check / Uncheck All
					</div>
                    
					<div style="width:50%; float:left" align="left">
					<input type="button" name="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" />
					</div>
				</div>
				</td>
		</tr>
	</table>
	</fieldset>
</form><!-------end form------->

<?
} 
?>