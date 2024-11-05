<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create chemical & dyes receive return
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	21-11-2013
Updated by 		: 	Kausar		
Update date		: 	10-12-2013 (Creating report)
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
echo load_html_head_contents("Chamical & Dyes Receive Info","../../", 1, 1, $unicode,1,1); 

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/chemical_dyes_receive_return_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_"); // mrr number
		//alert(mrrNumber[0]);return;
  		// master part call here
		reset_form('genralitem_receive_return_1','list_container_general*list_product_container','','','','variable_lot');
		set_button_status(0, permission, 'fnc_dyes_receive_return_entry',1,1);
		get_php_form_data(mrrNumber[0]+'**'+mrrNumber[2], "populate_data_from_data", "requires/chemical_dyes_receive_return_controller");  		
 		$("#tbl_child").find('input').val('');
		$("#tbl_child").find('select').val(0);
		$('#cbo_return_to').attr('disable',false);
		
 	}
}
//txt_return_value
function fn_calculateAmount(qnty)
{
	var rate = $("#txt_return_rate").val();
	var rcvQnty = $("#txt_curr_stock").val();
	
	if(rcvQnty*1 <qnty)
	{
		alert("Returned Qty. Exceeds MRR Stock Qty.");
		$('#txt_receive_qty').val(0);
		$('#txt_return_value').val(0);
		return;
	}
	else
	{		
		var amount = rate*qnty;
		$('#txt_return_value').val(number_format_common(amount,"","",1));
	}
}
//Save Update Delete
function fnc_dyes_receive_return_entry(operation)
{
	if(operation==4)
	 {
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#hidden_mrr_id').val()+'*'+report_title, "chemical_dyes_receive_return_print", "requires/chemical_dyes_receive_return_controller" ) 
		 return;
	 }
	else if(operation==0 || operation==1 || operation==2)
	{
		if( form_validation('cbo_company_id*cbo_return_to*txt_receive_date*txt_mrr_no*txt_challan_no*cbo_store_name*cbo_item_category*txt_receive_qty*txt_return_value*txt_return_rate','Company Name*Return To*Return Date*Received ID*Challan No*txt_mrr_no*Store Name*Item Category*Retuned Qnty*Return Value*Rate')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Receive Return Date Can not Be Greater Than Current Date");
			return;
		}	
		if($("#txt_receive_qty").val()*1>$("#txt_cons_quantity").val()*1)
		{
			alert("Return Quantity Can not be Greater Than Receive Stock.");
			return;
		}
		var dataString = "txt_mrr_retrun_no*cbo_company_id*cbo_return_to*txt_receive_date*txt_received_id*txt_mrr_no*txt_challan_no*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_item_category*txt_item_group*txt_item_description*txt_receive_qty*txt_return_value*txt_return_rate*txt_curr_stock*txt_uom*category*store*uom*txt_prod_id*before_prod_id*update_id*transaction_id*txt_remark*hidden_mrr_id*txt_lot*variable_lot";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");//alert(data);
		freeze_window(operation);
		//alert(data); return;
		http.open("POST","requires/chemical_dyes_receive_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_dyes_receive_return_entry_reponse;
	}
}

function fnc_dyes_receive_return_entry_reponse()
{	
	if(http.readyState == 4) 
	{	 		
		var reponse=trim(http.responseText).split('**');
		//alert(reponse);
		//alert(reponse[1]);
		show_msg(reponse[0]); 		
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		} 
		else if(reponse[0]==30) //Aziz
		{
			alert(reponse[1]);
			release_freezing();
			return;
		} 
		else if(reponse[0]==0)
		{
			$("#txt_mrr_retrun_no").val(reponse[1]);
			$("#hidden_mrr_id").val(reponse[2]);
 			$("#tbl_master :input").attr("disabled", true);
			disable_enable_fields( 'txt_mrr_retrun_no', 0, "", "" ); // disable false	
			show_list_view(reponse[1],'show_dtls_list_view','list_container_general','requires/chemical_dyes_receive_return_controller','');	
			$("#tbl_child").find('input').val('');
			$("#tbl_child").find('select').val(0);
			set_button_status(0, permission, 'fnc_dyes_receive_return_entry',1,1);
		}
		else if(reponse[0]==1 || reponse[0]==2)
		{
			show_list_view(reponse[1],'show_dtls_list_view','list_container_general','requires/chemical_dyes_receive_return_controller','');
			$("#tbl_child").find('input').val('');
			$("#tbl_child").find('select').val(0);
			set_button_status(0, permission, 'fnc_dyes_receive_return_entry',1,1);
		}
		release_freezing();
	}
}

function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/chemical_dyes_receive_return_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		//alert(theform);return; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value; // mrr number
		rcv_variable_check(company);
  		// master part call here
		get_php_form_data(returnNumber, "populate_master_from_data", "requires/chemical_dyes_receive_return_controller");  		
		show_list_view(returnNumber,'show_dtls_list_view','list_container_general','requires/chemical_dyes_receive_return_controller','');
		
		set_button_status(0, permission, 'fnc_dyes_receive_return_entry',1,1);
		$("#tbl_child").find('input').val('');
		$("#tbl_child").find('select').val(0);
		//disable_enable_fields( 'txt_return_no', 0, "", "" ); // disable false
 	}
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input,select').attr("disabled", false);	
	set_button_status(0, permission, 'fnc_dyes_receive_return_entry',1);
	reset_form('genralitem_receive_return_1','list_container_general*list_product_container','','','','variable_lot');
}

function validate_product()
{
	var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_item_category_id').value+"_"+document.getElementById('req_id').value+"_"+document.getElementById('cbo_supplier_id').value+"_"+document.getElementById('hidden_requsition').value;
	var list_view_orders = return_global_ajax_value( data, 'validate_supplier_load_php_dtls_form', '', 'requires/quotation_evaluation_controller');

	if(list_view_orders==1)
	{
		alert("This supplier is exist for same item of this requisition.");
		$("#cbo_supplier_id").focus();
	}
}

function rcv_variable_check(company_id)
{
	reset_form('genralitem_receive_return_1','list_container_general*list_product_container','','','','cbo_company_id');
	var lots_variable=return_global_ajax_value( company_id, 'populate_data_lib_data', '', 'requires/chemical_dyes_receive_return_controller');
	$('#variable_lot').val(lots_variable);
	if(lots_variable==1)
	{
		$('#lot_caption').css('color', 'blue');
	}
	else
	{
		$('#lot_caption').css('color', 'black');
	}
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission);  ?><br />
<form name="genralitem_receive_return_1" id="genralitem_receive_return_1" autocomplete="off" > 
    <div style="width:800px;float:left; margin-left:10px">       
        <fieldset style="width:750px; float:left;">
        <legend>Dye/Chem  Receive Return</legend>
        <br />
        	<fieldset style="width:750px;">
                <input type="hidden" id="transaction_id" name="transaction_id" />                                       
                <table width="750" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="6" align="center"><b>Return ID</b>
                            <input type="text" name="txt_mrr_retrun_no" id="txt_mrr_retrun_no" class="text_boxes" style="width:150px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
                            <input type="hidden" id="hidden_mrr_id" name="hidden_mrr_id" value="" />
                        </td>
                   </tr>
                   <tr>
                       <td  width="120" class="must_entry_caption">Company Name </td>
                       <td width="160">
                            <? 
                              echo create_drop_down( "cbo_company_id", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3)  $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "rcv_variable_check(this.value);load_room_rack_self_bin('requires/chemical_dyes_receive_return_controller*5_6_7_23', 'store','store_td', this.value);load_drop_down( 'requires/chemical_dyes_receive_return_controller', this.value, 'load_drop_down_supplier', 'supplier' );" );
                            ?>
                            <input type="hidden" id="variable_lot" name="variable_lot" />
                       </td>
                       <td width="120" align="" class="must_entry_caption"> Return Date </td>
                       <td width="150">
                       <input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:150px;" placeholder="Select Date" />
                       </td>
                       <td width="120" align="" class="must_entry_caption">Received ID</td>
                       <td width="150">
                       <input class="text_boxes"  type="text" name="txt_mrr_no" id="txt_mrr_no" style="width:150px;" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />
                            <input type="hidden" name="txt_received_id" id="txt_received_id" />
                      </td>
                    </tr>
                    <tr>
                        <td  width="120" align="" class="must_entry_caption"> Challan No</td>
                        <td width="150"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:150px" ></td>
                       <td width="120" align="" >Returned To</td>
                       <td width="150" id="supplier">
                             <?                                    
                            echo create_drop_down( "cbo_return_to", 160, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=3 and a.status_active=1 group by a.id, a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
                        ?>
                       </td>
                       <td>&nbsp;</td>
                       <td>&nbsp;</td>
                    </tr>
                </table>
            </fieldset>
            <br />
            <input type="hidden" id="txt_cons_quantity" name="txt_cons_quantity" value=""/>
            <fieldset style="width:750px;">  
            <legend>New Receive Return Item</legend>                                     
            	<table width="740" cellspacing="2" cellpadding="0" border="0" id="tbl_child"> 
                    <tr>    
                        <td width="130" class="must_entry_caption">Store Name</td>
                         <input type="hidden" id="store" name="store" value="" />
                        <td id="store_td"><? echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select --", $storeName, "" ); ?></td>
                        <td width="100">Item Desc.</td><input type="hidden" name="txt_prod_id" id="txt_prod_id" readonly disabled />
                        <td width="140"><input name="txt_item_description" id="txt_item_description" class="text_boxes" type="text" style="width:120px;" readonly /></td>
                        <td>Lot</td>
                        <td><input name="txt_lot" id="txt_lot" class="text_boxes" type="text" style="width:120px;" placeholder="Display" readonly disabled /></td> 
                    </tr>
                    <tr>    
                       <td width="130">Floor</td>
						<td id="floor_td">
							<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                        <td class="must_entry_caption">Item Category</td>
                        <td>
                            <input type="hidden" id="category" name="category" value="" />
                            <? echo create_drop_down( "cbo_item_category", 132, $item_category,"", 1, "-- Select Category --",0, "", 0,"5,6,7,23" ); ?>
                        </td>
                        <td class="must_entry_caption">Returned Qnty.</td>
                        <td><input name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric" type="text" style="width:120px;" placeholder="Entry" onKeyUp="fn_calculateAmount(this.value)" /></td>
                    </tr>
                   
                    <tr>    
                        <td width="130">Room</td>
						<td id="room_td">
							<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                        <td>Item Group.</td>
                        <td id="item_group_td"><input type="text" name="txt_item_group" id="txt_item_group" class="text_boxes" style="width:120px;" readonly/></td>
                       	<td class="must_entry_caption">Rate</td>   
                        <td><input name="txt_return_rate" id="txt_return_rate" class="text_boxes_numeric" type="text" style="width:120px;" readonly/></td>
                  	</tr>

                  	<tr>
                  		<td width="130">Rack</td>
						<td id="rack_td">
							<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td> 
                    	<td>MRR Stock</td>
                        <td><input type="text" name="txt_curr_stock" id="txt_curr_stock" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>
                  		<td class="must_entry_caption" width="120">Return Value</td>   
                        <td><input name="txt_return_value" id="txt_return_value" class="text_boxes_numeric" type="text" style="width:120px;" readonly/></td>
                  	</tr>
                  	<tr>
                  		<td  width="130">Shelf</td>
						<td id="shelf_td">
							<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>	
                  		<td  width="130">Bin/Box</td>
						<td id="bin_td">
							<? echo create_drop_down( "cbo_bin", 132,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
						</td>
                        <td>Cons. UOM</td>
                        <td width="150" id="uom_td"><input type="text" name="txt_uom" id="txt_uom" class="text_boxes" style="width:120px;" readonly disabled />
                        <input type="hidden" id="uom" name="uom" value="" /></td>
                  	</tr>
                  	<tr>     
                        <td>Remark</td>
                        <td colspan="5"><input name="txt_remark" id="txt_remark" class="text_boxes" type="text" style="width:615px;"/></td> 
                    </tr>
					<tr>
						<td colspan="6" align="center" id="posted_account_td" style="max-width:100px; color:red; font-size:20px;">
						</td>												
					</tr>
				</table>                           
			</fieldset>
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr> 
                	<td colspan="6" align="center"></td>				
                </tr>
                <tr>
                    <td align="center" colspan="6" valign="middle" class="button_container">
                    	<input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
                        <input type="hidden" id="update_id" name="update_id" value="" />
                        <!-- -->
                        <? echo load_submit_buttons( $permission, "fnc_dyes_receive_return_entry", 0,1,"fnResetForm()",1);?>
                    </td>
                </tr> 
            </table>  
            <br>
            <div style="width:800px;" id="list_container_general"></div>               
		</fieldset>
	</div>
	<div id="list_product_container" style="overflow:auto; width:490px; float:left; margin-top:5px; margin-left:10px; position:relative;"></div>
</form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>