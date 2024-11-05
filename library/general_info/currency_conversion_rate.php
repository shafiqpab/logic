<?
/*-------------------------------------------- Comments

Purpose			: 	This form will be used for mail recipient setup.

Functionality	:	First create Store Location and save.
					select a team from list view for update.

JS Functions	:

Created by		:	Saidul Reza 
Creation date 	: 	04-12-2013
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
echo load_html_head_contents("Employee Info", "../../", 1, 1,$unicode,'','');

$lib_designation=return_library_array( "select id,designation from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "designation"  );
$lib_user=return_library_array( "select id,user_name from user_passwd", "id", "user_passwd"  );

?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';

function fn_load_list_view(curr_id)
{
	if( form_validation('cbo_company_id','Company')==false )
	{
		return;
	}
	var com_id=$("#cbo_company_id").val();
	//alert(curr_id);
	show_list_view(curr_id+"_"+com_id,'load_list_view','list_container','requires/currency_conversion_rate_controller','setFilterGrid("mail_setup",-1)');
}

function fn_conversion_rate_entry(operation)
{
	if( form_validation('cbo_company_id*txt_currency*txt_conversion_rate*txt_marketing_rate*txt_date','Company*Currency*Conversion Rate*Marketing Rate*Date')==false )
	{
		return;
	}
	var current_date='<? echo date("d-m-Y"); ?>';
	if(date_compare($('#txt_date').val(), current_date)==false)
	{
		alert("Date Can not Be Greater Than Today");return;
	}
	
	var dataString = "cbo_company_id*txt_currency*txt_conversion_rate*txt_marketing_rate*txt_date*update_id";
 	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
	freeze_window(operation);
	http.open("POST","requires/currency_conversion_rate_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_emp_info_reponse;
}

function fnc_emp_info_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		show_msg(trim(response[0]));
		
		if(response[1])
		{
			var com_id=$("#cbo_company_id").val();
			var txt_currency=$("#txt_currency").val();
			show_list_view(txt_currency+"_"+com_id,'load_list_view','list_container','requires/currency_conversion_rate_controller','setFilterGrid("mail_setup",-1)');
			reset_form('conversionForm','','','','','');
			set_button_status(0, permission, 'fn_conversion_rate_entry',1,1);
		}
		release_freezing();
	}
}


</script>

</head>

<body onLoad="set_hotkey()">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">
    <fieldset style="width:600px;">
     <legend>Currency Conversion Rate</legend>
        <form name="conversionForm" id="conversionForm" autocomplete = "off">	
          <table cellpadding="0" cellspacing="2" width="70%" align="center">
          	<tr>
                <td width="90" class="must_entry_caption">Company</td>
                <td colspan="2"><? echo create_drop_down( "cbo_company_id", 160, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, ""); ?> </td>                
                <td colspan="1"></td>                
            </tr>
            <tr>
                <td width="90" class="must_entry_caption">Currency</td>
                <td colspan="2">
                  <?
                      echo create_drop_down( "txt_currency", 160, $currency,"", 1, "--- Select Currency ---", 0, "fn_load_list_view(this.value);", "", "", "" , "" , "1" );
                  ?>
                </td>                
                <td colspan="1"></td>                
            </tr>
            <tr>
                <td width="90" class="must_entry_caption">Conversion Rate</td>
                <td id="location_td_rn" width="90"><input type="text" id="txt_conversion_rate" name="txt_conversion_rate" class="text_boxes_numeric" style="width:70px;"/></td>                
                  <td class="must_entry_caption" width="90">Marketing Rate</td>
                <td id="location_td_rn_mr"><input type="text" id="txt_marketing_rate" name="txt_marketing_rate" class="text_boxes_numeric" style="width:70px;"/></td>                
            </tr>
            <tr>
                <td width="90" class="must_entry_caption">Date</td>
                <td colspan="2"><input type="text" id="txt_date" name="txt_date" class="datepicker" readonly style="width:153px;"/></td> 
                <td colspan="1"></td> 
            </tr>
            <tr><td>&nbsp;</td><td colspan="3">&nbsp;</td></tr>
            <tr>
                  <td align="center" colspan="4" class="button_container">
                  <input type="hidden" id="update_id">
                   <? 
                   echo load_submit_buttons( $permission, "fn_conversion_rate_entry", 0,0 ,"reset_form('conversionForm','','')",1);
                  ?>                   
                   </td>
            </tr>
          </table>
        </form>
    </fieldset>
        
<div id="list_container"></div>
        
        
        
        
        
        
        
</div>
</body>
    
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
