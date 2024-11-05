<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Pre Costing Approval
Functionality	:	
JS Functions	:
Created by		:	Shajib Jaman
Creation date 	: 	24/04/2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$menu_id=$_SESSION['menu_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Shipping Approval Req","../", 1, 1, $unicode,1,1);
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
	var permission='<? echo $permission; ?>';


    function fn_save_update_delete(operation)
	{
    	if (operation == 2) {
    		show_msg('13');
    		return;
    	}
    	else if (operation == 4) {
    		fn_generate_print(operation);
    	}
		else{
			if (form_validation('txt_order_no*cbo_shipment_type', 'Order No*Shiping Type') == false) {
				return;
			}
			else{
				var data = "action=save_update_delete&operation=" + operation + get_submitted_data_string('txt_sys_no*update_id*cbo_company_id*hidden_order_id*cbo_shipment_type*txt_team_leader_comments*txt_merchant_comments*txt_plan_head_comments*txt_approval_authority_comments*txt_date', "../");
				
				freeze_window(operation);
				http.open("POST", "requires/shipping_approval_request_controller.php", true);
				http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_save_update_delete_res;
			}
		}
    }

    function fnc_save_update_delete_res()
	{
    	if (http.readyState == 4) {
            release_freezing();
            var response = trim(http.responseText).split('**');
            
            if ((response[0] == 0 || response[0] == 1)) {
                document.getElementById("update_id").value=response[1];
				document.getElementById("txt_sys_no").value=response[2];
				show_msg(response[0]);
            	set_button_status(1, permission, 'fn_save_update_delete', 1);
            }
			else if(response[0] == 11){
				show_msg(response[0]);
			}


        }
    }



    function openmypage_order()
    {
	    emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/shipping_approval_request_controller.php?action=order_no_popup&cbo_company_id='+document.getElementById('cbo_company_id').value,'Order No Popup', 'width=900px,height=420px,center=1,resize=0','');
        emailwindow.onclose=function()
        {
            var order_id=this.contentDoc.getElementById("order_id").value;
            var order_no=this.contentDoc.getElementById("order_no").value;
			var company_id=this.contentDoc.getElementById("company_id").value;
			freeze_window(5);
			document.getElementById("cbo_company_id").value=company_id;
			document.getElementById("hidden_order_id").value=order_id;
			document.getElementById("txt_order_no").value=order_no;
			release_freezing();
        }
    }

    function openmypage_sys_id()
    {
	    emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/shipping_approval_request_controller.php?action=sys_id_popup&cbo_company_id='+document.getElementById('cbo_company_id').value,'Order No Popup', 'width=900px,height=420px,center=1,resize=0','');
        emailwindow.onclose=function()
        {
			freeze_window(5);
			var sys_id=this.contentDoc.getElementById("sys_id").value;
			get_php_form_data(sys_id, "get_sys_info", "requires/shipping_approval_request_controller" );
			release_freezing();
        }
    }

 
function fn_generate_print(operation){
		var data = "action=generate_print&operation=" + operation + get_submitted_data_string('txt_sys_no*update_id*cbo_company_id*hidden_order_id*cbo_shipment_type*txt_team_leader_comments*txt_merchant_comments*txt_plan_head_comments*txt_approval_authority_comments*txt_date', "../");
		
		freeze_window(operation);
		http.open("POST", "requires/shipping_approval_request_controller.php", true);
		http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_generate_print_res;
	
}

function fn_generate_print_res(){
	
		if(http.readyState == 4) 
		{
			release_freezing();
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
			d.close();
			
		}
}


</script>
</head>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
    <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="shipping_app_form_1" id="shipping_app_form_1" autocomplete="off">
            <fieldset style="width:700px; ">
            <legend>Shipping Approval</legend>
            <table cellpadding="5" cellspacing="5" border="0">
            	<tr>
                    <td colspan="4" align="center"><strong>System No</strong>
					<input type="text" onDblClick="openmypage_sys_id();" class="text_boxes"  name="txt_sys_no" id="txt_sys_no" readonly style="width:180px;" placeholder="Brows">
					<input type="hidden" name="update_id" id="update_id" readonly/> 
                    </td>
                </tr>
             	
                <tr>
                    <td class="must_entry_caption" align="right">Company Name</td>
                    <td>
						<? 
                            echo create_drop_down( "cbo_company_id", 190, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select--", 0, "",1,"" ); 
                        ?>
                    </td>
                    <td class="must_entry_caption" align="right">Order No</td>
                    <td>
                    	<input type="text" onDblClick="openmypage_order();" class="text_boxes"  name="txt_order_no" id="txt_order_no" readonly style="width:180px;" placeholder="Brows Order">
                    	<input type="hidden" name="hidden_order_id" id="hidden_order_id" readonly >
					</td>
                </tr>
                <tr>
                    <td class="must_entry_caption" align="right">Shipment Type</td>
                    <td>
						<? 
                            $shipment_type=array(1=>"Discount Shipment",2=>"Sea-Air Shipment",3=>"Air Shipment");
                            echo create_drop_down( "cbo_shipment_type", 190,  $shipment_type,"", 1, "-- Select--", 0, "","","" ); 
                        ?>
                    </td>
					<td align="right">Team Leader Comments</td>
                    <td><textarea name="txt_team_leader_comments" id="txt_team_leader_comments" class="text_boxes" style="width:180px" ></textarea></td>
                </tr>
				<tr>
					<td align="right">Merchant Comments</td>
                    <td align="left"><textarea name="txt_merchant_comments" id="txt_merchant_comments" class="text_boxes" style="width:180px" ></textarea></td>
					<td align="right">Plan Head Comments</td>
                    <td><textarea name="txt_plan_head_comments" id="txt_plan_head_comments" class="text_boxes" style="width:180px" ></textarea></td>
                </tr>
                <tr>
                    <td align="right">Approval  Authority</td>
                    <td><textarea name="txt_approval_authority_comments" id="txt_approval_authority_comments" class="text_boxes" style="width:180px" ></textarea></td>
                    
                    <td align="right">Date</td>
                    <td><input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:180px" value="<?=date('d-m-Y');?>"/></td>
                    
                    
                    
                    
                 </tr>
                
                    <tr>
                    	<td class="button_container" colspan="4"></td>
                    </tr>
                    <tr>
                        <td width="80%" align="center" colspan="4"> 
                        <? 
							 echo load_submit_buttons($permission, "fn_save_update_delete", 0, 1, "reset_form('shipping_app_form_1','','','','');set_button_status(0, permission, 'fn_save_update_delete', 1);", 1);
							
						 ?>
                    </tr>
            </fieldset>
            </table>
			
            
          
           
        </form>
    </div>
</body>

<script src="../includes/functions_bottom.js" type="text/javascript"></script>

</html>