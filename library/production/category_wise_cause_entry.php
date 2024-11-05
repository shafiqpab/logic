<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Category Wise Cause Entry
Functionality	:	
JS Functions	:
Created by		:	Kamrul Sheikh
Creation date 	: 	12.11.2023
Updated by 		:  
Update date		: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Category Wise Cause Entry", "../../", 1, 1,$unicode,'','');
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

function fnc_category_wise_cause_entry( operation )
{
	if ( form_validation('cbo_npt_category*cbo_npt_cause','Category*Causes')==false )
	{
		return;
	}	
	else
	{
		 
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_npt_category*cbo_npt_cause*cbo_status*update_id',"../../");
		freeze_window(operation);
		http.open("POST","requires/category_wise_cause_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_category_wise_cause_entry_reponse;
	}
}
function fnc_category_wise_cause_entry_reponse()
{
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		show_msg(trim(reponse[0]));
		show_list_view('','report_settings_cause_type','list_view_report_settings','requires/category_wise_cause_entry_controller','setFilterGrid("tbl_task_list",-1)');
		
		// show_list_view('','cause_type_list','list_view_cause_type','requires/category_wise_cause_entry_controller','setFilterGrid("tbl_cause_type_list",-1)');

		reset_form('tnataskentry_1','','');
		set_button_status(0, permission, 'fnc_category_wise_cause_entry',1);
		release_freezing();
	}
}



function fn_set_cause_type(task_id)
{
	//reset_form('tnataskentry_1','','');
	$('#cbo_npt_cause').val(task_id);
}
function fn_catagory_type()
{
	// reset_form('tnataskentry_1','','');
	var catagory=$('#cbo_npt_category').val();
	//alert($('#cbo_npt_category').val());
	show_list_view(catagory,'cause_type_list','list_view_cause_type','requires/category_wise_cause_entry_controller','setFilterGrid("cause_type_list",-1)');
}


</script>


</head>

<body onLoad="set_hotkey()">

<div style="width:100%;">

<? echo load_freeze_divs ("../../",$permission);  ?>
 <table cellspacing="10"><tr><td align="center" valign="top">  
<fieldset style="width:620px">
	<legend>Category Wise Cause Entry</legend>
	<form name="tnataskentry_1" id="tnataskentry_1" method="" autocomplete="off">
        <table  cellspacing="2" cellpadding="0" border="1">
			<input type="hidden" id="update_id" value="">
			<!-- <input type="hidden" id="system_id" value=""> -->

            <tr>
				
                <td class="must_entry_caption" align="right">Category</td>
                <td id=txt_system_id>
                    <? echo create_drop_down( "cbo_npt_category", 220, $npt_category,"", 1, "-- Select Category --", $selected, "fn_catagory_type()",0);	 ?>				
                </td> 
            </tr>
            <tr>
                <td class="must_entry_caption" align="right">Causes</td>
                <td id="task_name_td">
					
                <? echo create_drop_down( "cbo_npt_cause", 220, $npt_cause,"", 1, "-- Select Cause --", $selected, "",1);?>				
                  
                </td> 
            </tr>
            
             <tr>
                <td align="right">Status</td>
                <td><? echo create_drop_down( "cbo_status", 213, $row_status, 0, "", 1, "" ); ?></td>
            </tr>
             
            <tr>
                <td align="center" colspan="2" valign="middle" class="button_container">
                <?
                echo load_submit_buttons( $permission, "fnc_category_wise_cause_entry", 0,0 ,"reset_form('tnataskentry_1','','')",1); 
                ?>
                </td>
            </tr>
    	</table>
        
         <div id="list_view_report_settings">
             <script>
				show_list_view('','report_settings_cause_type','list_view_report_settings','requires/category_wise_cause_entry_controller','setFilterGrid("tbl_task_list",-1)');
			</script>
      </div>
     </form>	
</fieldset>
</td>
<td valign="top" id="list_view_cause_type"></td>
</tr>
</table>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
 <script>	
</script>
</html>
