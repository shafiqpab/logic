<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Additional Raw Material Issue Requisition
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	13-10-2021
Updated by 		: 		
Update date		: 
Oracle Convert 	:		
Convert date	: 		   
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
echo load_html_head_contents("Additional Raw Material Issue requisition", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_armir_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title, "raw_mat_issue_requisition_print", "requires/additional_raw_material_issue_requisition_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(!form_validation('cbo_company_name*txt_job_no','Company*Job No.'))
			{
				return;
			}
			
			var j=0; var dataString=''; 
			$("#armir_details_container").find('tr').each(function()
			{
				var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();				
				var hdnItemGroupId=$(this).find('input[name="hdnItemGroupId[]"]').val();
				var txtReqQty=$(this).find('input[name="txtReqQty[]"]').val();
				var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
				var productId=$(this).find('input[name="productId[]"]').val();
				var sectionId=$(this).find('input[name="sectionId[]"]').val();
 				var cboUom 		= $(this).find('select[name="cboUom[]"]').val();

				if( txtReqQty*1>0)
				{
					j++;
					dataString += '&hdnItemGroupId_' + j + '=' + hdnItemGroupId + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtRemarks_' + j + '=' + txtRemarks + '&txtReqQty_' + j + '=' + txtReqQty+ '&productId_' + j + '=' + productId+ '&sectionId_' + j + '=' + sectionId + '&cboUom_' + j + '=' + cboUom;
				}
			});
			// if(j<1)
			// {
			// 	alert('Please Insert Qty At Least One Row.');
			// 	return;
			// }
			//alert(dataString);//return;
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('update_id*cbo_company_name*txt_issue_date*cbo_location_name*hid_job_id*hid_order_id*txt_job_no*cbo_issue_basis*cbo_section*txt_targeted_prod_qty*cbo_uom*cbo_store_name*txt_production_id',"../../")+dataString+'&total_row='+j;
			// alert (data);return;
			freeze_window(operation);
			
			http.open("POST","requires/additional_raw_material_issue_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_armir_entry_response;
		}
	}	 
	 
	function fnc_armir_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');	
			show_msg(response[0]);
			if( response[0]==0 || response[0]==1 )
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_production_id').value = response[2];

				setDtlsIds(response[3]);
				document.getElementById('cbo_store_name').setAttribute('disabled', true);
				document.getElementById('txt_job_no').setAttribute('disabled', true);

				set_button_status(1, permission, 'fnc_armir_entry', 1, 1);
				// set_button_status(is_update, permission, submit_func, btn_id, show_print)
			}
			if( response[0]==2 )
			{
				location.reload();
			}
			release_freezing();
		}
	}

	function setDtlsIds(idsStr) {
		var dtlsIdsArr = idsStr.split(',');
		for (var i = 0; i < dtlsIdsArr.length; i++) {
			var index = i+1;
			document.getElementById('updateIdDtls_'+index).value = dtlsIdsArr[i];
		}
	}
	 	 
	function fnc_armir_prod_id()
	{
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 
			var company_id = $('#cbo_company_name').val();
			var title = 'Production ID Selection Form';	
			var page_link = 'requires/additional_raw_material_issue_requisition_controller.php?cbo_company_name='+company_id+'&action=armir_production_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//Access the form inside the modal window
				var mstId=this.contentDoc.getElementById("hidden_production_data").value;
				get_php_form_data('2**'+mstId, 'load_mst_php_data_to_form', 'requires/additional_raw_material_issue_requisition_controller');
            	show_list_view('2**'+mstId,'item_dtls_list_view','armir_details_container','requires/additional_raw_material_issue_requisition_controller', '');

            	document.getElementById('txt_targeted_prod_qty').setAttribute('disabled', true);
            	document.getElementById('cbo_uom').setAttribute('disabled', true);
            	document.getElementById('cbo_section').setAttribute('disabled', true);
				document.getElementById('cbo_store_name').setAttribute('disabled', true);
				document.getElementById('txt_job_no').setAttribute('disabled', true);            	

				set_button_status(1, permission, 'fnc_armir_entry',1,1);
				release_freezing();
			}
		}
	}

	function openmypage_job()
	{
		if ( form_validation('cbo_company_name*cbo_section','Company*Section')==false ) { return; }

		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_section').value;
		//var data=document.getElementById('cbo_company_name').value;
		page_link='requires/additional_raw_material_issue_requisition_controller.php?action=job_popup&data='+data;
		title='JOB Search';

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("selected_job").value;
			var ex_data=theemaildata.split('_');
			if (ex_data[0]!="")
			{
				freeze_window(5);
				get_php_form_data('1**'+ex_data[0], "load_mst_php_data_to_form", "requires/additional_raw_material_issue_requisition_controller" );
				// var job_no = $('#txt_job_no').val();
				// show_list_view(1+'**'+ex_data[0],'item_dtls_list_view','armir_details_container','requires/additional_raw_material_issue_requisition_controller','setFilterGrid(\'list_view\',-1)');

				document.getElementById('txt_targeted_prod_qty').setAttribute('disabled', true);
            	document.getElementById('cbo_uom').setAttribute('disabled', true);
            	document.getElementById('cbo_section').setAttribute('disabled', true);
            	show_list_view(1+'**'+0,'item_dtls_list_view','armir_details_container','requires/additional_raw_material_issue_requisition_controller','setFilterGrid(\'list_view\',-1)');

				set_button_status(0, permission, 'fnc_armir_entry',1);
				release_freezing();
			}
		}
	} 

	function fnc_load_party(type,within_group)
	{
		if ( form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		var location_name = $('#cbo_location_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/additional_raw_material_issue_requisition_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/additional_raw_material_issue_requisition_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/additional_raw_material_issue_requisition_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
		} 
	}

	function checkStoreSelection(){
		return form_validation('cbo_store_name', 'Store');
	}

	function checkBalance(requisitionEle, balance, tblRow) 
	{
		// alert(requisitionEle+'=='+balance+'=='+tblRow);
 		var requisitionQty = parseFloat(requisitionEle);
		var tmpBalance = parseFloat(balance);
 		var stockbalance = tmpBalance - requisitionQty;
 		if(stockbalance < 0) 
		{
			alert('Requisition Quantity cannot be greater then Stock Quantity');
			document.getElementById('txtReqQty_'+tblRow).value = 0;
		}
	}
	
	function openmypage_item()
	{
		if(form_validation('cbo_company_name*cbo_section*cbo_store_name','Company*Section*Store')==false) { return; }
		var data=document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_section').value+'_'+document.getElementById('cbo_store_name').value;
		page_link='requires/additional_raw_material_issue_requisition_controller.php?action=item_popup&data='+data;
		title='Product List';

		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var theemaildata=this.contentDoc.getElementById("all_ids").value;
			// var ex_data=theemaildata.split('_');
			if (theemaildata!="")
			{
				freeze_window(5);
				// var job_no = $('#txt_job_no').val();
				show_list_view(1+'**'+theemaildata,'item_dtls_list_view','armir_details_container','requires/additional_raw_material_issue_requisition_controller','setFilterGrid(\'list_view\',-1)');

				document.getElementById('txt_targeted_prod_qty').setAttribute('disabled', true);
            	document.getElementById('cbo_uom').setAttribute('disabled', true);
            	document.getElementById('cbo_section').setAttribute('disabled', true);
            	document.getElementById('cbo_store_name').setAttribute('disabled', true);

				set_button_status(0, permission, 'fnc_armir_entry',1);
				release_freezing();
			}
		}

	}

 </script>
</head>
<body>
<div align="left" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="dryProduction_1" id="dryProduction_1" autocomplete="off" >
    <div style="width:900px;">
        <fieldset style="width:850px; float:left;">
        <legend>Raw Material Issue Requisition</legend>
        <fieldset>
            <table width="100%" cellpadding="1" cellspacing="1" border="0" > 
		        <tr>
		            <td colspan="3" align="right"><strong>Requisition ID</strong></td>
		            <td colspan="3">
		                <input type="text" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="fnc_armir_prod_id();" readonly />
		                <input type="hidden" name="update_id" id="update_id" />		                
		                <input type="hidden" name="hid_job_id" id="hid_job_id" />
		                <input type="hidden" name="hid_order_id" id="hid_order_id" />
		            </td>
		        </tr>
		        <tr>
		            <td width="100" class="must_entry_caption">Company Name</td>
		            <td width="160">
		            	<? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", 0, "load_drop_down('requires/additional_raw_material_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td');"); ?>
		            </td>
		            <td width="100">Location</td>
		            <td width="160" id="location_td">
		            	<? echo create_drop_down("cbo_location_name", 150, $blank_array,"", 1,"-Select Location-", 0,""); ?>
		            </td>
		            <td width="100" class="must_entry_caption">Issue Date</td>
		            <td width="" id="issue_purpose_td"><input type="text" name="txt_issue_date" value="<? echo date("d-m-Y");?>" id="txt_issue_date" class="datepicker" style="width:140px;" /></td>		            
		        </tr>
		        <tr>
		        	<td width="100">Issue Basis </td>
		            <td width="160">
						<?echo create_drop_down( "cbo_issue_basis", 150, $receive_basis_arr,"", 0, "-- Select --", $selected, "","","15" );?>
		            </td>
		            <td  class="must_entry_caption" >Section</td>
		            <td>
		            	<?php echo create_drop_down('cbo_section', 150, $trims_section, '', 1, '- Section -', $selected, '', 0); ?>
		            </td>
		            <td  class="must_entry_caption" >Job No.</td>
		            <td>	
						<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;" onDblClick="openmypage_job();" placeholder="Browse" readonly />
					</td>
		        </tr>
		        <tr>
		            <td>Target Production Qty</td>
		            <td>
		            	<input type="text" name="txt_targeted_prod_qty" id="txt_targeted_prod_qty" class="text_boxes_numeric" style="width:55px;" placeholder="Display" readonly />
		            	<?php echo create_drop_down('cbo_uom', 80, $unit_of_measurement, '', 1, '- UOM -', $selected, '', 0); ?>
		            </td>
		            <td>Order No.</td>
		            <td>
		                <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" value="" style="width:140px;" placeholder="Display" />
		            </td>
		            <td class="must_entry_caption">Store</td>
		            <td id="store_td">
		                <?php echo create_drop_down('cbo_store_name', 150, $blank_array, '', 1, '-- Select --', $selected, ''); ?>
		            </td>
		        </tr>
    		</table>
		</fieldset>
        <br />
        <fieldset style="width:780px; ">
            <legend>Requisition Details Info</legend>
			<table cellpadding="0" cellspacing="0" width="780" class="rpt_table" border="1" rules="all" id="tbl_item_details">
				<thead>
					<tr>
						<th width="90">Item Group</th>
						<th width="150">Material Description</th>
						<th width="90">Brand</th>
						<th width="60">UOM</th>
						<th width="90" class="must_entry_caption">Requ. Qty.</th>
						<th width="90">Stock</th>
						<th >Remarks</th>
					</tr>
				</thead> 
				<tbody id="armir_details_container">
					<tr name="tr[]" id="tr_1">
						<td>
							<?php echo create_drop_down( "cboItemGroup_1", 90, "select id, item_name from lib_item_group where item_category in (4,101,22) and status_active=1","id,item_name", 1, "Display",$selected, "",0,'','','','','','',"cboItemGroup[]"); ?>
						</td>
						<td>
							<input id="txtdescription_1" name="txtdescription[]" type="text" class="text_boxes" placeholder="Browse" style="width: 150px;" onDblClick="openmypage_item();" />
						</td>
						<td align="right">
							<input type="text" name="txtBrand[]" id="txtBrand_1" class="text_boxes" placeholder="Write" style="width: 90;" />
						</td>
						<td>
							<?php echo create_drop_down( "cboUom_1", 60, $unit_of_measurement,"", 1, "Display",2,1, 1,'','','','','','',"cboUom[]"); ?>
						</td>
						<td align="right">
							<input type="text" name="txtReqQty[]" id="txtReqQty_1" class="text_boxes_numeric" placeholder="Write" onBlur="fnc_total_calculate();" style="width: 90px;" />
						</td>
						<td align="right">
							<input type="text" name="txtStock[]" id="txtStock_1" class="text_boxes_numeric" placeholder="Write" style="width: 90px;" />
						</td>
						<td>
							<input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" placeholder="Write" style="width: 90px;" />
							<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" />
							<input type="hidden" name="hdnBalance[]" id="hdnBalance_1" />
							<input type="hidden" name="productId[]" id="productId_1" />
							<input type="hidden" name="sectionId[]" id="sectionId_1" />
						</td>
					</tr>
				</tbody>
			</table>
			<table cellpadding="0" cellspacing="1" width="780">
				<tr>
					<td align="center" colspan="10" valign="middle" class="button_container">
						<? echo load_submit_buttons($permission, "fnc_armir_entry", 0,1,"reset_form('dryProduction_1','list_fabric_desc_container*dry_production_list_conainer','','','');",1); ?> 
					</td>	  
				</tr>
			</table>
        </fieldset>
        <br />
    </div>
    <div id="list_fabric_desc_container" style="max-height:500px; width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div style="clear:both"></div>
    <br />
    <div style="width:500px;" id="dry_production_list_conainer"></div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>