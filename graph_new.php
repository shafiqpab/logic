<? 
session_start(); 
 echo load_html_head_contents("Graph", "", "", $popup, $unicode, $multi_select, 1);
 //include('includes/common.php');
 function add_month($orgDate,$mon){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
  return $retDAY;
}
?>
<style>
.stack_company
{
	visibility:visible;
}

</style>

<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
    	<td>
        	
        </td>
    </tr>

</table>