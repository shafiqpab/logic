<?
/*--- ----------------------------------------- Comments
Purpose			: 						
Functionality	:	
JS Functions	:
Created by		:	Md. Abdul Hakim /sohel
Creation date 	: 	19-03-2013
Updated by 		: 		
Update date		:
Oracle Convert 	:	Kausar		
Convert date	: 	20-05-2014	   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub-Contract Order Info", "../", 1,1, $unicode,1,'');

//print_r($_SESSION['logic_erp']['mandatory_field'][238]);
//die;
?>
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php"; 
var permission='<? echo $permission; ?>';

	var str_cust_buyer = [<? echo substr(return_library_autocomplete( "select cust_buyer from subcon_ord_dtls group by cust_buyer", "cust_buyer" ), 0, -1); ?>];
	$(document).ready(function(e)
	 {
            $("#txt_cust_buyer").autocomplete({
			 source: str_cust_buyer
		  });
     });

	function openmypage_job()
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value;
		page_link='requires/sub_contract_order_entry_controller.php?action=job_popup&data='+data;
		title='Subcontrat Order';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=790px, height=420px, center=1, resize=0, scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemail=this.contentDoc.getElementById("selected_job");
			if (theemail.value!="")
			{
				freeze_window(5);
				reset_form('','','txt_job_no*cbo_company_name*cbo_location_name*cbo_party_name*cbo_currency*txt_order_no*txt_order_quantity*cbo_uom*txt_rate*txt_amount*txt_order_receive_date*txt_order_delivery_date*txt_cust_buyer*txt_style_ref*cbo_process_name*txt_process_id*txt_smv*cbo_status*txt_details_remark*cbo_delay_cause*txt_deleted_id','','');
				get_php_form_data( theemail.value, "load_php_data_to_form", "requires/sub_contract_order_entry_controller" );
				show_list_view(theemail.value,'subcontract_dtls_list_view','order_list_view','requires/sub_contract_order_entry_controller','setFilterGrid("list_view",-1)');
				set_button_status(0, permission, 'fnc_job_order_entry',1);
				release_freezing();
			}
		}
	}
	
	function active_inactive(type)
	{
		if(type==1) //Active
		{
			$('#txt_order_no').attr('disabled','disabled');
			$('#cbo_process_name').attr('disabled','disabled');
			$('#txt_process_name').attr('disabled','disabled');
			$('#cbo_grey_req').attr('disabled','disabled');
			$('#cbo_uom').attr('disabled','disabled');
			$('#txt_rate').attr('disabled','disabled');
			$('#txt_amount').attr('disabled','disabled');
			$('#txt_cust_buyer').attr('disabled','disabled');
			$('#txt_style_ref').attr('disabled','disabled');
			$('#txt_smv').attr('disabled','disabled');
			$('#cbo_status').attr('disabled','disabled');
			$('#txt_order_receive_date').attr('disabled','disabled');
			$('#txt_order_delivery_date').attr('disabled','disabled');
		}
		else
		{
			$('#txt_order_no').removeAttr('disabled','disabled');
			$('#cbo_process_name').removeAttr('disabled','disabled');
			$('#txt_process_name').removeAttr('disabled','disabled');
			$('#cbo_grey_req').removeAttr('disabled','disabled');
			$('#cbo_uom').removeAttr('disabled','disabled');
			$('#txt_rate').removeAttr('disabled','disabled');
			$('#txt_amount').removeAttr('disabled','disabled');
			$('#txt_cust_buyer').removeAttr('disabled','disabled');
			$('#txt_style_ref').removeAttr('disabled','disabled');
			$('#txt_smv').removeAttr('disabled','disabled');
			$('#cbo_status').removeAttr('disabled','disabled');
			$('#txt_order_receive_date').removeAttr('disabled','disabled');
			$('#txt_order_delivery_date').removeAttr('disabled','disabled');
		}
	}
	
	function format_date(date)
	{
		var data=date.split('-');
		var new_date=data[2]+'-'+data[1]+'-'+data[0];
		return new_date;
	}
	
	function fnc_job_order_entry( operation )
	{
		freeze_window(operation);
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][238]);?>')
        {
            if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][238]);?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][238]);?>')==false)
            { 
                release_freezing();
                return;
            }
        }

		var delete_master_info=0;
        var process = $("#cbo_process_name").val();
		var txt_order_delivery_date=new Date(format_date(document.getElementById('txt_order_delivery_date').value));
		var txt_material_recv_date=new Date(format_date(document.getElementById('txt_material_recv_date').value));

		if (txt_material_recv_date.getTime() > txt_order_delivery_date.getTime()) {
			alert("Material. Received Date is over  Than Po Delivery Date!");
			release_freezing();
			return;
		}

		if ( form_validation('cbo_company_name*cbo_party_name*cbo_currency*txt_order_no*cbo_process_name*txt_order_quantity*cbo_uom*txt_rate*txt_amount*txt_order_receive_date*txt_order_delivery_date','Company*Party*Currency*Order No*Process Name*Order Quantity*UOM*Rate*Amount*Order Receive Date*Order Delivery Date')==false )
		{ 
			release_freezing();
			return;
		}
		else
		{	
			if(operation==2)
			{
				var r=confirm("Press OK to Delete Or Press Cancel");
				if(r==false){
					release_freezing();
					return;
				}
		    }

			if(process == 3 || process == 4){
				if ( form_validation('cbo_grey_req','Grey Req.')==false )
				{
					release_freezing();
					return;
				}
			}
			var data="action=save_update_delete&operation="+operation+'&delete_master_info='+delete_master_info+get_submitted_data_string('txt_job_no*cbo_company_name*cbo_location_name*cbo_party_name*cbo_currency*cbo_approve_status*update_id*txt_order_no*txt_order_quantity*cbo_uom*txt_rate*txt_amount*txt_order_receive_date*txt_order_delivery_date*txt_cust_buyer*txt_style_ref*cbo_process_name*txt_process_id*cbo_grey_req*txt_smv*cbo_status*txt_details_remark*cbo_delay_cause*update_id2*hidden_item*hidden_color*hidden_size*hidden_qnty*hidden_rate*hidden_amount*hidden_excess_cut*hidden_plan_cut*hidden_loss*hidden_tbl_id_break*txt_deleted_id*hidden_gsm*hidden_grey_dia*hidden_finish_dia*hidden_embelishment_type*hidden_description*hidden_diawidth_type*hidden_cbo_dyeing_part*hidden_cbo_color_range*hidden_cbo_dyeing_upto*hidden_txtlab*hidden_txtaddrate*hidden_color_qty_breakdown*txt_material_recv_date*txt_efficiency_per*cbo_team_leader*collarAndCuffStr',"../");
			//alert (data); release_freezing(); return;
			
			http.open("POST","requires/sub_contract_order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_job_order_entry_response;
		}
	}

	function fnc_job_order_entry_response()
	{
		if(http.readyState == 4) 
		{
			//alert (http.responseText); release_freezing();//return;
			var response=trim(http.responseText).split('**');
			//alert (response); release_freezing();return;
			//alert(response[0]);
			//if (response[0].length>3) reponse[0]=10;
			if(response[0]==13)
			{
				alert(response[1]);
				release_freezing();
				return;
			}

			show_msg(response[0]);	
			if(response[0]==14)
			{
				alert('Recv Found= '+response[2]);
				release_freezing();
				return;
			}
			else if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_job_no').value = response[1];
				document.getElementById('update_id').value = response[2];
				active_inactive(0);
				reset_form('','','txt_order_quantity*cbo_uom*txt_rate*txt_amount*txt_order_receive_date*txt_order_delivery_date*txt_cust_buyer*txt_style_ref*cbo_process_name*txt_process_id*txt_process_name*cbo_grey_req*txt_smv*cbo_status*txt_details_remark*cbo_delay_cause*update_id2*hidden_item*hidden_color*hidden_size*hidden_qnty*hidden_rate*hidden_amount*hidden_excess_cut*hidden_plan_cut*hidden_loss*hidden_tbl_id_break*txt_deleted_id*hidden_embelishment_type*hidden_description*hidden_diawidth_type*hidden_cbo_dyeing_part*hidden_cbo_color_range*hidden_cbo_dyeing_upto*hidden_txtlab*hidden_txtaddrate','','','');
				set_button_status(0, permission, 'fnc_job_order_entry',1);
				show_list_view(response[1],'subcontract_dtls_list_view','order_list_view','requires/sub_contract_order_entry_controller','setFilterGrid("list_view",-1)');
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');			

				release_freezing();
			}
			release_freezing();
		}
	}

	function openmypage_order_qnty()
	{
		if ( form_validation('cbo_company_name*cbo_party_name*cbo_process_name*txt_order_no','Company*Party*Process*Order No')==false )
		{
			return;
		}
		else
		{	
			var hidden_item_chk=document.getElementById('hidden_item').value;
			
			var data_break=document.getElementById('hidden_item').value+"_"+document.getElementById('hidden_color').value+"_"+document.getElementById('hidden_size').value+"_"+document.getElementById('hidden_qnty').value+"_"+document.getElementById('hidden_rate').value+"_"+document.getElementById('hidden_amount').value+"_"+document.getElementById('hidden_excess_cut').value+"_"+document.getElementById('hidden_plan_cut').value+"_"+document.getElementById('hidden_loss').value+"_"+document.getElementById('hidden_gsm').value+"_"+document.getElementById('hidden_grey_dia').value+"_"+document.getElementById('hidden_finish_dia').value+"_"+document.getElementById('hidden_embelishment_type').value+"_"+document.getElementById('hidden_description').value;
			
			if(hidden_item_chk!='')
			{
				var data_break='';
			}
			//alert (data_break);
			var data=document.getElementById('txt_order_no').value+"_"+document.getElementById('cbo_process_name').value+"_"+document.getElementById('update_id2').value+"_"+document.getElementById('txt_order_quantity').value+"_"+document.getElementById('txt_rate').value+"_"+document.getElementById('txt_amount').value+"_"+document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('hidden_tbl_id_break').value+"_"+document.getElementById('update_id').value+"_"+document.getElementById('cbo_currency').value;
			
			page_link='requires/sub_contract_order_entry_controller.php?action=order_qnty_popup&data='+data+'&data_break='+data_break	
			emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Qnty Dtls Popup', 'width=1080px, height=400px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{

				var receive_embelishment_type=this.contentDoc.getElementById("hidden_embelishment_type"); 
				var receive_description=this.contentDoc.getElementById("hidden_description"); 

				var receive_itemid=this.contentDoc.getElementById("hidden_itemid"); 
                var receive_gsm=this.contentDoc.getElementById("hidden_gsm"); 
                var receive_greydia=this.contentDoc.getElementById('hidden_grey_dia');
                var receive_finishdia=this.contentDoc.getElementById('hidden_finish_dia');
				var receive_color=this.contentDoc.getElementById("hidden_color"); 
				var receive_size=this.contentDoc.getElementById("hidden_size");
				var receive_qnty=this.contentDoc.getElementById("hidden_order_quantity"); 
				var receive_rate=this.contentDoc.getElementById("hidden_order_rate"); 
				var receive_amount=this.contentDoc.getElementById("hidden_order_amount"); 
				var receive_total_qnty=this.contentDoc.getElementById("txt_total_order_qnty"); 
				var receive_average_rate=this.contentDoc.getElementById("txt_average_rate"); 
				var receive_total_amount=this.contentDoc.getElementById("txt_total_order_amount"); 
				var receive_excess=this.contentDoc.getElementById("hidden_excess"); 
				var receive_plan=this.contentDoc.getElementById("hidden_plan"); 
				var receive_loss=this.contentDoc.getElementById("hidden_loss"); 
				var receive_hidden_tbl_id=this.contentDoc.getElementById("hidden_tbl_id"); 
				var receive_delete_id=this.contentDoc.getElementById("txt_deleted_id"); 
				var receive_diawidth_type=this.contentDoc.getElementById("hidden_txtdiawidth"); 
				var receive_cbo_dyeing_part=this.contentDoc.getElementById("hidden_cbo_dyeing_part"); 
				var receive_cbo_color_range=this.contentDoc.getElementById("hidden_cbo_color_range"); 
				var receive_cbo_dyeing_upto=this.contentDoc.getElementById("hidden_cbo_dyeing_upto"); 
				var receive_txtlab=this.contentDoc.getElementById("hidden_txtlab"); 
				var receive_txtaddrate=this.contentDoc.getElementById("hidden_txtaddrate"); 
				var hidden_color_qty_breakdown=this.contentDoc.getElementById("hidden_color_qty_breakdown"); 

				//alert (receive_finishdia.value);//return;
				$('#hidden_color_qty_breakdown').val(hidden_color_qty_breakdown.value);
				$('#hidden_embelishment_type').val(receive_embelishment_type.value);
				$('#hidden_description').val(receive_description.value);
				$('#hidden_item').val(receive_itemid.value);
	            $('#hidden_gsm').val(receive_gsm.value);
	            $('#hidden_grey_dia').val(receive_greydia.value);
	            $('#hidden_finish_dia').val(receive_finishdia.value);
				$('#hidden_color').val(receive_color.value);
				$('#hidden_size').val(receive_size.value);
				$('#hidden_qnty').val(receive_qnty.value);
				$('#hidden_rate').val(receive_rate.value);
				$('#hidden_amount').val(receive_amount.value);
				$('#hidden_excess_cut').val(receive_excess.value);
				$('#hidden_plan_cut').val(receive_plan.value);
				$('#hidden_loss').val(receive_loss.value);
				$('#hidden_tbl_id_break').val(receive_hidden_tbl_id.value);			
				$('#txt_order_quantity').val(receive_total_qnty.value);
				$('#txt_rate').val(receive_average_rate.value);
				$('#txt_amount').val(receive_total_amount.value);
				$('#txt_deleted_id').val(receive_delete_id.value);
				$('#hidden_cbo_dyeing_part').val(receive_cbo_dyeing_part.value);
				$('#hidden_cbo_color_range').val(receive_cbo_color_range.value);
				$('#hidden_cbo_dyeing_upto').val(receive_cbo_dyeing_upto.value);
				$('#hidden_txtlab').val(receive_txtlab.value);
				$('#hidden_txtaddrate').val(receive_txtaddrate.value);
				$('#hidden_diawidth_type').val(receive_diawidth_type.value);
			}
		}
	}

	function openmypage_process()
	{
		var txt_process_id = $('#txt_process_id').val();
		var cbo_process_name = $('#cbo_process_name').val();
		var title = 'Process Name Selection Form';	
		var page_link = 'requires/sub_contract_order_entry_controller.php?txt_process_id='+txt_process_id+'&cbo_process_name='+cbo_process_name+'&action=process_name_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var process_id=this.contentDoc.getElementById("hidden_process_id").value;	 //Access form field with id="emailfield"
			var process_name=this.contentDoc.getElementById("hidden_process_name").value;
			$('#txt_process_id').val(process_id);
			$('#txt_process_name').val(process_name);
		}
	}

	function make_empty_subprocess()
	{
		$('#txt_process_id').val("");
		$('#txt_process_name').val("");
	}
        
    function change_caption_n_uom(process)
    {
        if(process == 3 || process == 4) $("#grey_req_caption").css("color", "blue");
		else $("#grey_req_caption").css("color", "#444");
		
        if(process == 2 || process == 3 || process == 4) $("#cbo_uom").val(12); else  $("#cbo_uom").val(2);
    }
	
	function fnc_order_print(type)
	{  // alert(type);
			var job_no=$('#txt_job_no').val();
			//alert(type);return;
			//if(job_no=="")
			//{
				if ( form_validation('txt_job_no*cbo_company_name','Job No*Company')==false )
				{
					return;
				}
			//}
			var report_title=$( "div.form_caption" ).html();
			generate_report_file($('#cbo_company_name').val()+'*'+$('#update_id2').val()+'*'+$('#txt_job_no').val()+'*'+report_title,'sub_order_print','requires/sub_contract_order_entry_controller');
			return;
	}
	function generate_report_file(data,action,page)
	{
		window.open("requires/sub_contract_order_entry_controller.php?data=" + data+'&action='+action, true );
	}
	
	function openmypage_collar_and_cuff()
	{
		if(form_validation('cbo_company_name*txt_job_no*txt_order_no','Company*Job No*Order No')==false)
		{
			return;
		}
		 
		collarAndCuffStr = $('#collarAndCuffStr').val();
		var title = 'Coller & Cuff Mesurement Info';	
		var page_link = 'requires/sub_contract_order_entry_controller.php?action=collar_and_cuff_popup&collarAndCuffStr='+collarAndCuffStr;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window 
			var data=this.contentDoc.getElementById("hide_data").value; //Access form field with id="emailfield"
			$('#collarAndCuffStr').val(data);
		}
	}
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../",$permission);  ?>
    <fieldset style="width:1050px;">
    <legend>Sub-Contract Order Entry</legend>
        <form name="subjoborderentry_1" id="subjoborderentry_1" autocomplete="off">  
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td width="130" align="right">Job No</td>
                    <td width="170">
                    	<input type="hidden" name="txt_deleted_id" id="txt_deleted_id" class="text_boxes_numeric" style="width:90px" readonly />
                        <input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="openmypage_job();" placeholder="Double Click" style="width:130px;" readonly /></td>
                    <td width="130" align="right" class="must_entry_caption">Company Name </td>
                    <td width="170"> 
						<? echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/sub_contract_order_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'requires/sub_contract_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    </td>
                    <td width="130" align="right">Location Name</td>
                    <td id="location_td"><? echo create_drop_down( "cbo_location_name", 140, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
               </tr>
               <tr>
                    <td align="right" class="must_entry_caption">Party</td>
                    <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 140, $blank_array,"", 1, "-- Select Party --", $selected, "" ); ?></td>
                    <td align="right" class="must_entry_caption">Currency</td>
                    <td><? echo create_drop_down( "cbo_currency", 140, $currency,"", 1, "-- Select Currency --",1,"", "","" );?>
                        <input type="hidden" name="update_id" id="update_id">
                    </td>
					<td align="right">Ready to Approve</td>
                    <td>
					<? echo create_drop_down( "cbo_approve_status", 160, $yes_no,"", 1, "-- Select --", "", "","" ); ?> 
                    </td>
                </tr> 
				<tr>
				<td align="right">Team Leader</td>
                    <td id="div_teamleader"><?=create_drop_down( "cbo_team_leader", 140, "select id,team_leader_name from lib_marketing_team where   team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "" );  ?>
				</td>
				</tr>
            </table>              
            <fieldset style="width:1000px;">
            <legend>Sub-Contract Order Details Entry</legend>
            <table cellpadding="0" cellspacing="2" border="0" class="rpt_table" rules="all">
                <thead class="form_table_header">
                    <tr align="center" >
                        <th width="80" class="must_entry_caption">Order No </th>
                        <th width="80" class="must_entry_caption">Process</th>
                        <th width="145">Sub Process</th>
                        <th width="60" id="grey_req_caption">Grey Req.</th>
                        <th width="60" class="must_entry_caption">Order Qty</th>
                        <th width="50" class="must_entry_caption">Order UOM</th>
                        <th width="60" class="must_entry_caption">Rate/Unit</th>
                        <th width="60" class="must_entry_caption">Amount</th>
                        <th width="55" class="must_entry_caption">Receive Date</th>
                        <th width="55" class="must_entry_caption">Delivery Date</th>
                        <th width="70">Cust Buyer</th>
                        <th width="70">Cust Style Ref</th>
						<th width="60">Efficiency%</th>
                        <th width="50">SMV</th>
						<th width="55">Material Recv. Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input name="txt_order_no" id="txt_order_no" type="text" class="text_boxes" value=""  style="width:75px"/></td>
                        <td><? echo create_drop_down( "cbo_process_name", 80, $production_process,"", 1, "--Select Process--",0,"change_caption_n_uom(this.value);make_empty_subprocess();", "","" ); ?></td>
                        <td><input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:140px;" placeholder="Click To Search" onClick="openmypage_process();" tabindex="12" readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" /></td>
                        <td><? echo create_drop_down( "cbo_grey_req", 60, $yes_no,"", 1, "-Select-",0,"", "","" ); ?></td>
                        <td><input name="txt_order_quantity" id="txt_order_quantity" class="text_boxes_numeric" type="text"  style="width:55px" onClick="openmypage_order_qnty();" placeholder="Click To Search" readonly /></td>
                        <td><? echo create_drop_down( "cbo_uom", 50, $unit_of_measurement,"", 1, "-- Select --",0,"", "","" ); ?></td>
                        <td><input name="txt_rate" id="txt_rate" type="text"  class="text_boxes_numeric" style="width:50px" readonly/></td>
                        <td><input name="txt_amount" id="txt_amount" type="text" style="width:55px"  class="text_boxes_numeric" readonly /></td>
                        <td><input type="text" name="txt_order_receive_date" id="txt_order_receive_date"  class="datepicker" style="width:55px" /></td>
                        <td><input type="text" name="txt_order_delivery_date" id="txt_order_delivery_date"  class="datepicker" style="width:55px" /></td>
                        <td><input name="txt_cust_buyer" id="txt_cust_buyer" type="text"  class="text_boxes" style="width:60px" /></td>
                        <td><input name="txt_style_ref" id="txt_style_ref" type="text"  class="text_boxes" style="width:60px" /></td>
						<td><input name="txt_efficiency_per" id="txt_efficiency_per" type="text_boxes_numeric"  class="text_boxes" style="width:60px" /></td>
                        <td><input name="txt_smv" id="txt_smv" type="text"  class="text_boxes_numeric" style="width:40px" /></td>
						<td><input type="text" name="txt_material_recv_date" id="txt_material_recv_date"  class="datepicker" style="width:55px" /></td>
                        <td><? echo create_drop_down( "cbo_status", 70, $row_status, 0, "", 1, "" ); ?></td>
                    </tr>  
                    <tr>
                        <td align="right"><strong>Remarks</strong></td>
                        <td colspan="5" height="20">
                            <input type="text" id="txt_details_remark" name="txt_details_remark" class="text_boxes" style="width:400px" maxlength="150" title="Maximum 150 Character" />
                        </td>
                        <td width="120"><strong>Delay Cause</strong></td>
                        <td colspan="3" height="20">
							<? echo create_drop_down( "cbo_delay_cause", 200, $delay_for, 0, "", 1, "" ); ?>
                            <input type="hidden" name="update_id2" id="update_id2">
                        </td>
                         <td><strong>Image</strong></td> 
                        <td colspan="2">
                            <input type="button" class="image_uploader" style="width:80px" value="Browse Image" onClick="file_uploader ( '../', document.getElementById('update_id').value,'', 'sub_contract_order_entry', 0 ,1)">
                        </td>
                        <td>&nbsp;</td> 
						<td colspan="2"> 
							<input type="button" class="formbutton" style="width:100px" value="Collar and Cuff" onClick="openmypage_collar_and_cuff()"> 
							<input type="hidden" name="collarAndCuffStr" id="collarAndCuffStr">
						</td>
                    </tr>
                </tbody>
            </table>
            </fieldset>
            <br>
            <table width="1000" cellspacing="2" cellpadding="0" border="0">
                <tr>
                    <td align="center" colspan="12" valign="middle" class="button_container">
					<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
						<? echo load_submit_buttons($permission, "fnc_job_order_entry", 0,0,"reset_form('subjoborderentry_1','order_list_view','','','disable_enable_fields(\'cbo_company_name*cbo_party_name\',0*0)')",1,1); ?>
                         <input type="button" id="Print_1" value="Print" class="formbutton" onClick="fnc_order_print(1)" style="width:100px;">
                    </td>
                </tr>   
                <tr align="center">
                    <td colspan="12" id="order_list_view"> </td>	
                </tr>     
            </table>
                <input type="hidden" name="hidden_item" id="hidden_item">  
                <input type="hidden" name="hidden_gsm" id="hidden_gsm">
                <input type="hidden" name="hidden_grey_dia" id="hidden_grey_dia">
                <input type="hidden" name="hidden_finish_dia" id="hidden_finish_dia">
                <input type="hidden" name="hidden_color" id="hidden_color">
                <input type="hidden" name="hidden_size" id="hidden_size">
                <input type="hidden" name="hidden_qnty" id="hidden_qnty">
                <input type="hidden" name="hidden_rate" id="hidden_rate">
                <input type="hidden" name="hidden_amount" id="hidden_amount">
                <input type="hidden" name="hidden_excess_cut" id="hidden_excess_cut">
                <input type="hidden" name="hidden_plan_cut" id="hidden_plan_cut">
                <input type="hidden" name="hidden_loss" id="hidden_loss">
                <input type="hidden" name="hidden_tbl_id_break" id="hidden_tbl_id_break">
                <input type="hidden" name="hidden_diawidth_type" id="hidden_diawidth_type">
				<input type="hidden" name="hidden_cbo_dyeing_part" id="hidden_cbo_dyeing_part">
				<input type="hidden" name="hidden_cbo_color_range" id="hidden_cbo_color_range">
				<input type="hidden" name="hidden_cbo_dyeing_upto" id="hidden_cbo_dyeing_upto">
				<input type="hidden" name="hidden_txtlab" id="hidden_txtlab">
				<input type="hidden" name="hidden_txtaddrate" id="hidden_txtaddrate">
                <input type="hidden" name="hidden_embelishment_type" id="hidden_embelishment_type">
                <input type="hidden" name="hidden_description" id="hidden_description">
				<input type="hidden" name="hidden_color_qty_breakdown" id="hidden_color_qty_breakdown">
				<input type="hidden" name="hidden_qtytbl_id" id="hidden_qtytbl_id">
            </form>
        </fieldset>
    </div>
</body>
<script>//set_multiselect('cbo_process','0*0','0','','__populate_sub_group_info__requires/sub_contract_order_entry_controller');</script>
<script>set_multiselect('cbo_delay_cause','0','0','','');</script>  
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>