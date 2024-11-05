<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Party Wise Opening Balance
				
Functionality	:	
JS Functions	:
Created by		:	Md. Nuruzzaman 
Creation date 	: 	11-09-2021
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
echo load_html_head_contents("Party Wise Opening Balance Entry","../../", 1, 1, $unicode,1,1); 
?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1)
	window.location.href = "../../logout.php";

function populate_buyer()
{
	var data=document.getElementById('cbo_company_name').value+"__"+document.getElementById('cbo_year_name').value+"__"+document.getElementById('cbo_month').value+"__"+document.getElementById('cbo_location_id').value;
	var retunr_mst_id = return_global_ajax_value( data, 'return_mstid', '', 'requires/capacity_allocation_controller');
	//alert(retunr_mst_id)
	document.getElementById('update_id').value=trim(retunr_mst_id);
	show_list_view(document.getElementById('cbo_company_name').value+'__'+retunr_mst_id+'__'+document.getElementById('cbo_month').value,'show_dtls_list_view','list_container','requires/capacity_allocation_controller','');	
	if( trim(retunr_mst_id) !="")
	{
		//alert(retunr_mst_id)
		set_button_status(1, permission, 'func_party_wise_opening_balance_entry',1);
		
	}
	else
	{
		set_button_status(0, permission, 'func_party_wise_opening_balance_entry',1);
	}
}

function total_value( id )
{
	var tamount=0;
	var i=1;
	$( "#tbl_allocation tbody tr" ).each(function( index ) {
	 
	tamount=tamount+($("#txt_allocation_"+i).val()*1);
	i++;
	});
	if(tamount>100) { $("#txt_allocation_"+id).val(''); alert('Allocation over the 100% not allowed'); return; }
	else
	$("#txt_amount").val(tamount);

}
 
 
function func_party_wise_opening_balance_entry(operation)
{
	return;
/*	if( form_validation('cbo_company_name*cbo_item_category*txt_sent_by*txt_sent_to*txt_receive_date*txt_start_hours*txt_start_minuties*txt_item_description*txt_quantity*cbo_uom*txt_rate','Company Name*Item Catagory*Sent By*Sent To*Out Date*Out Time*Out Time*Item Description*Quantity*UOM*Rate')==false )
	{
		return;
	}*/	
	
	if(operation==4)
	 {
		 print_report( $('#cbo_company_name').val()+'*'+$('#cbo_year_name').val()+'*'+$('#update_id').val()+'*'+$('#cbo_month').val()+'*'+$('#cbo_location_id').val(), "capacity_allocation_print", "requires/capacity_allocation_controller" ) 
		 return;
	 }
	 else if(operation==0 || operation==1 || operation==2)
	{
	
		var tot_row_buyer=$('#tbl_allocation tbody tr').length;
		var data1 =get_submitted_data_string('cbo_company_name*cbo_year_name*cbo_month*cbo_location_id*update_id',"../../");
		var data2="";
		for(i=1; i<=tot_row_buyer; i++)
		{
			data2+=get_submitted_data_string('buyer_id_'+i+'*txt_allocation_'+i+'*update_id_dtls_'+i,"../../",i);
		}
		
		var data="action=save_update_delete&operation="+operation+"&tot_row_buyer="+tot_row_buyer+data1+data2;
	
		freeze_window(operation);
		http.open("POST","requires/capacity_allocation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = func_party_wise_opening_balance_entry_reponse;
	}
}

function func_party_wise_opening_balance_entry_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		if(response[0]==0|| response[0]==1)
		{
			
			
			show_msg(trim(response[0]));
			//var ms_id=document.getElementById("update_id").value=response[1];
			//alert(trim(response[1]))
			show_list_view(document.getElementById('cbo_company_name').value+'__'+trim(response[1]),'show_dtls_list_view','list_container','requires/capacity_allocation_controller','');	

			//var retunr_buyer_id = return_global_ajax_value( response[1], 'load_php_dtls_form_return_id_buyer', '', 'requires/capacity_allocation_controller');
			/*var response_return_id=retunr_buyer_id.split('*');
			
			var k=1;
			for(i=0; i<=response_return_id.length; i++)
			{
				$('#update_id_dtls_'+k).val(response_return_id[i])
				k++;
			}*/
			
			/*$("#txt_system_id").val(response[2]);
			$("#update_id").val(response[3]);
			show_list_view(response[2],'show_dtls_list_view','list_container','requires/get_out_entry_controller','');
			reset_form('','','txt_item_description*cbo_uom*txt_quantity*txt_rate*txt_amount*txt_remarks','','','');*/
			set_button_status(1, permission, 'func_party_wise_opening_balance_entry',1,1);
			release_freezing();
		}
		else if(response[0]==10)
		{
			set_button_status(0, permission, 'func_party_wise_opening_balance_entry',1);
			alert("\"Select Year\" mendatory to save data");
			show_msg(trim(response[0]));
			release_freezing();
			return;
		}
		
 	}
}

function fnResetForm()
{
	reset_form('','list_container','cbo_year_name*cbo_month*update_id','','','');
}

function location_select()
{
	if($('#cbo_location_id option').length==1)
	{
		$('#cbo_location_id').val($('#cbo_location_id option:last').val());
		//eval($('#cbo_location_name').attr('onchange'));
	}
	else //if($('#cbo_location_id option').length==2)
	{
		if($('#cbo_location_id option:first').val()==0)
		{
			$('#cbo_location_id').val($('#cbo_location_id option:last').val());
			//eval($('#cbo_location_name').attr('onchange')); 
		}
	}
}
</script>
<body onLoad="set_hotkey()">
   <div style="width:100%;" align="center">
     <? echo load_freeze_divs ("../../",$permission);  ?>
     <form name="capacityallocation_1" id="capacityallocation_1"  autocomplete="off">
    <div style="width:700px">
    <fieldset>  
        <legend>Party Wise Opening Balance Entry</legend>
        <table cellpadding="0" cellspacing="2" width="660px" class="tbl_capacity_allocation">
            <tr>
                <td width="100" align="right" class="must_entry_caption">Company</td>
                <td><? echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/party_wise_opening_balance_entry_controller', this.value, 'load_drop_down_party', 'td_party' );" ); ?></td>
                <td width="100" align="right" class="must_entry_caption">Party</td>
                <td id="td_party"><? echo create_drop_down( "cbo_party_id",120,$blank_array,"", 1, "--Select--", $selected); ?></td>
                <td width="100" align="right" class="must_entry_caption">Opening Balance</td>
                <td><input type="text" name="txt_opening_balance" id="txt_opening_balance" class="text_boxes_numeric" style="width:110px;"</td>
            </tr>
        </table>
    </fieldset>
     <br>
        <table cellpadding="0" cellspacing="1" width="100%">
            <tr>
                <td align="center" colspan="6" valign="middle" class="button_container"><? echo load_submit_buttons( $permission, "func_party_wise_opening_balance_entry", 0,0,"fnResetForm()",1); ?></td>
            </tr> 
        </table> 
    </div>
    </form>
    <br>
     <fieldset style="width:700px;">
    	<div style="width:450px;" id="list_container"></div>
    </fieldset>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>