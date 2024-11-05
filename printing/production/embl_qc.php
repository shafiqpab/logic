<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Embellishment QC Entry
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	08-07-2018
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
echo load_html_head_contents("Embellishment QC Entry Info", "../../", 1, 1,'','','');

?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	var str_supervisor = [<? echo substr(return_library_autocomplete( "select distinct(operator_name) as supervisor from subcon_embel_production_dtls", "operator_name"  ), 0, -1); ?>];
	
	function fnc_embel_entry(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "embl_qc_entry_print", "requires/embl_qc_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_id*txt_production_no*txt_prod_date','Company*Production NO*Production Date')==false )
			{
				return;
			}
			
			var j=0; var dataString=''; //var all_barcodes='';
			$("#embellishment_details_container").find('tr').each(function()
			{
				var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
				var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
				var dtlsPoId=$(this).find('input[name="dtlsPoId[]"]').val();
				
				var txtRejQty=$(this).find('input[name="txtRejQty[]"]').val();
				var txtQcQty=$(this).find('input[name="txtQcQty[]"]').val();
				var txtremarks=$(this).find('input[name="txtremarks[]"]').val();
				var txtPhysicalQty=$(this).find('input[name="txtPhysicalQty[]"]').val();
				var hdnDtlsdata=$(this).find('input[name="hdnDtlsdata[]"]').val();
				
				if( txtQcQty*1>0 || txtRejQty*1>0)
				{
					j++;
					dataString += '&colorSizeId_' + j + '=' + colorSizeId + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtRejQty_' + j + '=' + txtRejQty + '&txtQcQty_' + j + '=' + txtQcQty + '&txtPhysicalQty_' + j + '=' + txtPhysicalQty + '&txtremarks_' + j + '=' + txtremarks+ '&hdnDtlsdata_' + j + '=' + hdnDtlsdata+ '&dtlsPoId_' + j + '=' + dtlsPoId;
				}
			});
			if(j<1)
			{
				alert('Please Insert Qty At Least One Row.');
				return;
			}
			//alert(dataString);return;
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_qc_id*update_id*cbo_company_id*cbo_location*txt_production_id*txt_order_id*txt_job_no*txt_prod_date*txt_super_visor*txtbuyerPoId*cboShift*cbo_floor_id',"../../")+dataString+'&total_row='+j;
			//alert (data);return;
			freeze_window(operation);
			
			http.open("POST","requires/embl_qc_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_embel_entry_response;
		}
	}	 
	 
	function fnc_embel_entry_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');	
			show_msg(response[0]);
			
			if( response[0]==0 || response[0]==1 )
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_qc_id').value = response[2];
				var production_id = $('#txt_production_id').val();
				fnc_dtls_data_load(production_id,response[1]);
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_embel_entry',1,0);
			}
			release_freezing();	
		}
	}
	 
	function openmypage_production()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Production Pop-up';	
			var page_link = 'requires/embl_qc_controller.php?cbo_company_id='+cbo_company_id+'&action=production_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var str_data=this.contentDoc.getElementById("selected_str_data").value;	 //Access form field with id="emailfield"
				
				if(str_data!="")
				{
					freeze_window(5);
					var estr_data=str_data.split("___");
					
					$('#txt_production_id').val(estr_data[0]);
					$('#txt_production_no').val( estr_data[1] );
					
					$('#txt_job_no').val(estr_data[2]);
					$('#txt_order_id').val(estr_data[3]);
					$('#txt_order').val(estr_data[4]);
					
					$('#txtbuyerPoId').val(estr_data[8]);
					$('#txtbuyerPo').val(estr_data[9]);
					$('#txtstyleRef').val(estr_data[10]);
					//load_drop_down( 'requires/embl_qc_controller', cbo_company_id+'_'+estr_data[6]+'_'+estr_data[5], 'load_drop_down_buyer', 'party_td');
					load_drop_down('requires/embl_qc_controller',cbo_company_id+'__'+ document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td');
					$('#cbo_floor_id').val(estr_data[11]);
					
					load_drop_down( 'requires/embl_qc_controller', cbo_company_id+'_'+estr_data[6]+'_'+estr_data[5], 'load_drop_down_buyer', 'party_td');
					$('#txt_order_qty').val(estr_data[7]);
					
					fnc_dtls_data_load(estr_data[0],0);
					fnc_total_calculate();
					release_freezing();
				} 
			}
		}
	}
	
	function fnc_dtls_data_load(production_id,uid)
	{
		//alert(recipe_id+'_'+uid); return;
		var cbo_company_id = $('#cbo_company_id').val();
		var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+production_id+'***'+uid, 'order_details', '', 'requires/embl_qc_controller');
		if(list_view_orders!='')
		{
			$("#embellishment_details_container").html(list_view_orders);
		}
	}
	 
	function fnc_embel_qc_id()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 
			var company_id = $('#cbo_company_id').val();
			var title = 'QC ID Selection Form';	
			var page_link = 'requires/embl_qc_controller.php?cbo_company_id='+company_id+'&action=embel_qc_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//Access the form inside the modal window
				var emblishment_data=this.contentDoc.getElementById("hidden_qc_data").value;
				//alert(emblishment_id_data);return;
				var emb_data = emblishment_data.split("***");
				if(emb_data[0]!="")
				{
					freeze_window(5);
					
					$('#update_id').val(emb_data[0]);
					$('#txt_qc_id').val(emb_data[1]);
					$('#cbo_location').val(emb_data[2]);
					$('#txt_production_id').val(emb_data[3]);
					$('#txt_production_no').val(emb_data[4]);
					$('#txt_job_no').val(emb_data[5]);
					$('#txt_order_id').val(emb_data[6]);
					$('#txt_order').val(emb_data[7]);
					
					$('#txtbuyerPoId').val(emb_data[13]);
					$('#txtbuyerPo').val(emb_data[14]);
					$('#txtstyleRef').val(emb_data[15]);
					
					load_drop_down( 'requires/embl_qc_controller', company_id+'_'+emb_data[8]+'_'+emb_data[9], 'load_drop_down_buyer', 'party_td');
					$('#txt_order_qty').val(emb_data[10]);
					
					$('#txt_prod_date').val(emb_data[11]);
					$('#txt_super_visor').val(emb_data[12]);
					$('#cboShift').val(emb_data[16]);
					$('#cbo_floor_id').val(emb_data[17]);
					
					fnc_dtls_data_load(emb_data[3],emb_data[0]);
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embel_entry',1,0);
					release_freezing();
				}
			}
		}
	}

	function fn_autocomplete()
	{
		 $("#txt_super_visor").autocomplete({
			 source: str_supervisor
		  });
	} 
	
	function fnc_calculate_qcqty(incid)
	{
		var qc_qty=$("#txtQcQty_"+incid).val()*1;
		var prod_qty=$("#txtProdQty_"+incid).val()*1;
		var PhysicalQty=$("#txtPhysicalQty_"+incid).val()*1;
		var txtRejQty=$("#txtRejQty_"+incid).val()*1;
		var qcqty=PhysicalQty-txtRejQty;
		
		 //alert(PhysicalQty);
		
		var production_balance = $("#txtQcQty_"+incid).attr('placeholder')*1;
		// alert(qc_qty*1); alert(production_balance); alert(prod_qty);
		if(((qc_qty*1)+production_balance)>prod_qty)
		{
			
			var confirm_value=confirm("QC qty Excceded by Production qty.Press cancel to proceed otherwise press ok. ");
			if(confirm_value!=0)
			{
				$("#txtQcQty_"+incid).val('');
				$("#txtPhysicalQty_"+incid).val('');
				$("#txtRejQty_"+incid).val('');
			}			
			return;
		}
		else
		{
			
			if(((qcqty*1)+production_balance)>prod_qty)
			{
				
				var confirm_value=confirm("QC qty Excceded by Production qty.Press cancel to proceed otherwise press ok. ");
				if(confirm_value!=0)
				{
					$("#txtQcQty_"+incid).val('');
					$("#txtPhysicalQty_"+incid).val('');
					$("#txtRejQty_"+incid).val('');
				}			
				return;
			}
			else
			{
				
				if(PhysicalQty>0)
				{
					$("#txtQcQty_"+incid).val(qcqty);
				}
 				//$("#txtQcQty_"+incid).val(qcqty);
			}
			
		}
		
		/*var qc_qty=prod_qty-rej_qty;
		if(prod_qty<rej_qty)
		{
			alert("Reject Qty over then Production Qty.")
			$("#txtRejQty_"+incid).val("");
			$("#txtQcQty_"+incid).val(prod_qty);
			return;
		}*/
		//$("#txtQcQty_"+incid).val(qc_qty);
	}
	function load_physical()
	{
		//alert(val);
		//var row_num = $('#tbl_dtls_wash tbody tr').length;
		var rowCount = $('#embellishment_details_container tr').length;
		if(document.getElementById('is_copy').checked==true)
		{
			
			for(i=1;i<=rowCount;i++)
			{
				var ProdQty=$('#txtProdQty_'+i).val();
				var production_balance = $("#txtQcQty_"+i).attr('placeholder')*1;
				$('#txtPhysicalQty_'+i).val(ProdQty-production_balance);
				var PhysicalQty=$('#txtPhysicalQty_'+i).val();
				var tot_defect_qty=$('#txtRejQty_'+i).val();
				//$('#txtRejQty_'+i).val(tot_defect_qty);
				$('#txtQcQty_'+i).val(PhysicalQty-tot_defect_qty);
				//var QcQty=$('#txtQcQty_'+i).val();
				fnc_calculate_qcqty(i);
			}
		}
		else
		{
			for(i=1;i<=rowCount;i++)
			{
				$('#txtPhysicalQty_'+i).val('');
				var PhysicalQty=$('#txtPhysicalQty_'+i).val();
				var tot_defect_qty=$('#txtRejQty_'+i).val();
				//$('#txtRejQty_'+i).val(tot_defect_qty);
				
				//var ProdQty=$('#txtProdQty_'+i).val();
				//var production_balance = $("#txtQcQty_"+i).attr('placeholder')*1;
				//$('#txtPhysicalQty_'+i).val(ProdQty-production_balance);
				
				$('#txtQcQty_'+i).val(PhysicalQty-tot_defect_qty);
				 
			}
		}
		fnc_total_calculate();
	}
	function btn_load_change_production()
	{
		(function blink() { 
			$('#show').fadeOut(900).fadeIn(900, blink); 
		})();
	}
	
	function show_production()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			show_list_view($('#cbo_company_id').val()+'__'+$('#txt_job_no').val(), 'show_production_no', 'list_production_nos', 'requires/embl_qc_controller', 'setFilterGrid(\'tbl_prod_list_search\',-1);');
		}
	}
	
	function set_form_data(data)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		
		$('#txt_qc_id').val('');
		$('#update_id').val('');
		
		
		var data = data.split("___");
		
		$('#cbo_company_id').attr('disabled', 'disabled');
		
		$('#txt_production_id').val(data[0]);
		$('#txt_production_no').val( data[1] );
		
		$('#txt_job_no').val(data[2]);
		$('#txt_order_id').val(data[3]);
		$('#txt_order').val(data[4]);
		
		$('#txtbuyerPoId').val(data[8]);
		$('#txtbuyerPo').val(data[9]);
		$('#txtstyleRef').val(data[10]);
		load_drop_down('requires/embl_qc_controller',cbo_company_id+'__'+ document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td');
		$('#cbo_floor_id').val(data[11]);
		load_drop_down( 'requires/embl_qc_controller', cbo_company_id+'_'+data[6]+'_'+data[5], 'load_drop_down_buyer', 'party_td');
		$('#txt_order_qty').val(data[7]);
		
		fnc_dtls_data_load(data[0],0);
		fnc_total_calculate();
		set_button_status(0, permission, 'fnc_embel_entry',1,0);
	}
	
	function location_select()
	{
		if($('#cbo_location option').length==2)
		{
			if($('#cbo_location option:first').val()==0)
			{
				$('#cbo_location').val($('#cbo_location option:last').val());
				//eval($('#cbo_location').attr('onchange')); 
			}
		}
		else if($('#cbo_location option').length==1)
		{
			$('#cbo_location').val($('#cbo_location option:last').val());
			//eval($('#cbo_location').attr('onchange'));
		}	
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#embellishment_details_container tr').length;
		//alert(rowCount)
		math_operation( "txtTotPhysicaldQty", "txtPhysicalQty_", "+", rowCount );
		math_operation( "txtTotRejQty", "txtRejQty_", "+", rowCount );
		math_operation( "txtTotQcQty", "txtQcQty_", "+", rowCount );
		math_operation( "txtTotQcQty", "txtQcQty_", "+", rowCount );
		math_operation( "txtTotProdQty", "txtProdQty_", "+", rowCount );
	}
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtremarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/embl_qc_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#txtremarks_'+id).val(theemail.value);
			}
		}
	}
	function openmypage_reject_qty(type,job_dtls_id,row)
	{
		var production_id = $('#txt_production_id').val();
		var RecipeNo= $('#txtRecipeNo_'+row).val()*1;
		var booking_po_id = $('#txtbuyerPoId').val();
		var data_break=$('#hdnDtlsdata_'+row).val();
		var hdnDtlsUpdateId=$('#updateIdDtls_'+row).val();
		
		var page_link = 'requires/embl_qc_controller.php?job_dtls_id='+job_dtls_id+'&data_break='+data_break+'&hdnDtlsUpdateId='+hdnDtlsUpdateId+'&booking_po_id='+booking_po_id+'&RecipeNo='+RecipeNo+'&production_id='+production_id+'&action=reject_qty_popup';
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe',page_link,'Defect Qty Popup', 'width=470px, height=100px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			$('#hdnDtlsdata_'+row).val('');
			var break_data=this.contentDoc.getElementById("hidden_break_tot_row").value; 
			var defect_qty = break_data.split('_');
			var tot_defect_qty= (defect_qty[0])*1+(defect_qty[1])*1+(defect_qty[2])*1;
			$('#hdnDtlsdata_'+row).val(break_data);
			var PhysicalQty=$('#txtPhysicalQty_'+row).val();
			$('#txtRejQty_'+row).val(tot_defect_qty);
			
			if(tot_defect_qty>PhysicalQty)
			{
				alert("Reject Qty Cannot Be Greater Than  Physical Quantity");
				$('#txtRejQty_'+row).val("");
				return;
				
			}
			$('#txtQcQty_'+row).val(PhysicalQty-tot_defect_qty);
			fnc_total_calculate();
		}		
	}
	

 </script>
</head>

<body onLoad="set_hotkey(); btn_load_change_production();">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission); ?>
		<div style="width:1020px; float:left;">
			<fieldset style="width:900px;">
				<form name="embellishmentEntry_1" id="embellishmentEntry_1">
					<fieldset style="width:900px;">
					<legend>Embellishment QC</legend>
						<table width="100%" cellpadding="1" cellspacing="1" border="0" > 
							<tr>
								<td colspan="3" align="right"><strong>QC ID</strong></td>
								<td colspan="3">
									<input type="text" name="txt_qc_id" id="txt_qc_id" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="fnc_embel_qc_id();" />
									<input type="hidden" name="update_id" id="update_id"/>
								</td>
							</tr>
							<tr>
								<td width="100" class="must_entry_caption">Company Name</td>
								<td width="160"><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", 0, "load_drop_down('requires/embl_qc_controller', this.value, 'load_drop_down_location', 'location_td'); location_select();load_drop_down('requires/embl_qc_controller', this.value+'__'+document.getElementById('cbo_location').value, 'load_drop_down_floor', 'floor_td');"); ?></td>
								<td width="100">Location</td>
								<td width="160" id="location_td"><? echo create_drop_down("cbo_location", 150, $blank_array,"", 1,"-Select Location-", 0,""); ?></td>
								<td width="100" class="must_entry_caption">Production ID</td>
								<td>
									<input type="text" name="txt_production_no" id="txt_production_no" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="openmypage_production();" readonly />
									<input type="hidden" name="txt_production_id" id="txt_production_id" class="text_boxes" value="0" style="width:40px;" />
									&nbsp;&nbsp;&nbsp;&nbsp;
									<input id='show' onClick='show_production();' type='button' class='formbutton' value='&nbsp;&nbsp;Show&nbsp;&nbsp;' style='background-color:#d9534f !important; background-image:none !important;border-color: #d43f3a;' title='Production List'>
								</td>
							</tr>
							<tr>
								<td>Embl. Job No.</td>
								<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;" disabled placeholder="Display"/></td>
								<td>WO No.</td>
								<td>
									<input type="text" name="txt_order" id="txt_order" class="text_boxes" value="" style="width:140px;" disabled placeholder="Display" />
									<input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" value="" style="width:60px;" />
								</td>
								<td>Order Qty</td>
								<td><input type="text" name="txt_order_qty" id="txt_order_qty" class="text_boxes_numeric" style="width:140px;" disabled placeholder="Display"/></td>
							</tr>
							<tr>
								<td>Party Name</td>
								<td id="party_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 ); ?></td>
								<td>Buyer PO</td>
								<td><input name="txtbuyerPo" id="txtbuyerPo" type="text" class="text_boxes" style="width:140px" readonly />
									<input name="txtbuyerPoId" id="txtbuyerPoId" type="hidden" class="text_boxes" style="width:70px" />
								</td>
								<td>Style</td>
								<td><input name="txtstyleRef" id="txtstyleRef" type="text" class="text_boxes" style="width:140px" readonly /></td>
							</tr>
                            <tr>
 								 <td>Floor</td>
                    			 <td id="floor_td"><? echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", $selected, "",1 ); ?></td>
							</tr>
						</table>
					</fieldset>
					<fieldset style="width:1105px; margin-top:10px" >
					<legend>Embel. QC Details Info</legend>
						<table cellpadding="0" cellspacing="0" width="1105" class="rpt_table" border="1" rules="all" id="tbl_item_details" align="left">
							<thead>
								<tr>
									<th>&nbsp;</th>
									<th class="must_entry_caption">QC Date</th>
									<th><input type="text" name="txt_prod_date" id="txt_prod_date" class="datepicker" style="width:80px;" placeholder="QC Date" value="<? echo date("d-m-Y")?>" readonly/></th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>Operator/ Superviser/ QC Name</th>
									<th><input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();" style="width:70px"></th>
									<th>&nbsp;</th>
									<th>Shift</th>
									<th><? echo create_drop_down( "cboShift", 60, $shift_name,"", 1, '- Select -', 0,"",'','','','','','','','cboShift'); ?></th>
                                    <th width="50">Physical Qty<input type="checkbox" name="is_copy" id="is_copy" onClick="load_physical()" /></th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
 									<th>&nbsp;</th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="100">Recipe No</th>
									<th width="100">Buyer PO</th>
									<th width="90">Gmts Item</th>
									<th width="100">Body Part</th>
									<th width="100">Embel. Name</th>
									<th width="100">Process/ Type</th>
									<th width="80">Color</th>
									<th width="70">Size</th>
									<th width="65">Production Qty (Pcs)</th>
									<th width="65">Physical Qty (Pcs)</th>
									<th width="65">Reject Qty (Pcs)</th>
									<th width="60">QC Pass Qty (Pcs)</th>
									<th>RMK</th>
								</tr>
							</thead>
							
							<tbody id="embellishment_details_container">
								<tr class="general" name="tr[]" id="tr_1">
									<td><input type="text" name="txtSl[]" id="txtSl_1" class="text_boxes_numeric" style="width:20px" value="1" disabled /></td>
									<td>
										<input type="text" name="txtRecipeNo[]" id="txtRecipeNo_1" class="text_boxes" style="width:90px" placeholder="Display"disabled />
										<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" value="" style="width:50px" />
										<input type="hidden" name="colorSizeId[]" id="colorSizeId_1" value="" style="width:50px" />
										<input type="hidden" name="dtlsPoId[]" id="dtlsPoId_1" value="" style="width:50px" />
									</td>
									<td>
			                            <input type="text" name="txtBuyerPO[]" id="txtBuyerPO_1" class="text_boxes" style="width:90px" placeholder="Display"disabled />
			                            <input type="hidden" name="txtBuyerPOId[]" id="txtBuyerPOId_1" value="" style="width:50px" />
			                        </td>
									<td>
										<input type="text" name="txtGmtsItem[]" id="txtGmtsItem_1" class="text_boxes" style="width:80px" placeholder="Display"disabled />
										<input type="hidden" name="txtGmtsItemId[]" id="updateIdDtlsId_1" value="" style="width:50px" />
									</td>
									<td>	
										<input type="text" name="txtBodyPart[]" id="txtBodyPart_1" class="text_boxes" style="width:90px" placeholder="Display" disabled  />
										<input type="hidden" name="txtBodyPartId[]" id="txtBodyPartId_1" style="width:50px" class="text_boxes" />
									</td>
									<td>
										<input type="text" name="txtEmblName[]" d="txtEmblName_1" value="" class="text_boxes" style="width:90px" placeholder="Display" disabled />
										<input type="hidden" name="txtEmblNameId[]" id="txtEmblNameId_1" value="" />
									</td>
									<td>
										<input type="text" name="txtEmblType[]" id="txtEmblType_1" value="" class="text_boxes" style="width:90px" placeholder="Display" disabled />
										<input type="hidden" name="txtEmblTypeId[]" id="txtEmblTypeId_1" value="" />                        
									</td>
									<td>
										<input type="text" name="txtColor[]" id="txtColor_1" value="" class="text_boxes"  style="width:70px" placeholder="Display" disabled/>
										<input type="hidden" name="txtColorId[]" id="txtColorId_1" value="" />
									</td>
									<td>
										<input type="text" name="txtSize[]" id="txtSize_1" value="" class="text_boxes"  style="width:60px" placeholder="Display" disabled/>
										<input type="hidden" name="txtSizeId[]" id="txtSizeId_1" value="" />
									</td>
									<td>
										<input type="text" name="txtProdQty[]" id="txtProdQty_1" class="text_boxes_numeric" style="width:55px" placeholder="Dispaly" disabled/>
									</td>
									<td>
										<input type="text" name="txtPhysicalQty[]" id="txtPhysicalQty_1" class="text_boxes_numeric"  placeholder="Write" style="width:55px" onBlur="fnc_total_calculate();"/>
									</td>
									<td>
										<input type="text" name="txtRejQty[]" id="txtRejQty_1" class="text_boxes_numeric" style="width:55px" placeholder="Write" onBlur="fnc_total_calculate();" onClick="openmypage_reject_qty(1,'0',1)"  readonly/>
										<!--onBlur="fnc_calculate_qcqty(1);"-->
										 <input type="hidden" name="hdnDtlsdata[]" id="hdnDtlsdata_1">
									</td>
									<td>
										<input type="text" name="txtQcQty[]" id="txtQcQty_1" class="text_boxes_numeric" style="width:50px" />
									</td>
									<td><input type="text" name="txtremarks[]" id="txtremarks_1" style="width:40px" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(1);" /></td>
								</tr>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>Total:</td>
									<td>
										<input type="text" name="txtTotProdQty" id="txtTotProdQty" class="text_boxes_numeric" style="width:55px" placeholder="Dispaly" readonly/>
									</td>
									<td>
										<input type="text" name="txtTotPhysicaldQty" id="txtTotPhysicaldQty" class="text_boxes_numeric" style="width:55px" placeholder="Dispaly" readonly/>
									</td>
									<td>
										<input type="text" name="txtTotRejQty" id="txtTotRejQty" class="text_boxes_numeric" style="width:55px" placeholder="Dispaly" readonly />
									</td>
									<td>
										<input type="text" name="txtTotQcQty" id="txtTotQcQty" class="text_boxes_numeric" style="width:50px" placeholder="Dispaly" readonly />
									</td>
									<td>&nbsp;</td>
								</tr>
							</tfoot>
						</table>
					</fieldset>
					<table cellpadding="0" cellspacing="1" width="800">
						<tr>
							 <td align="center" colspan="6" valign="middle" class="button_container">
								<? echo load_submit_buttons($permission, "fnc_embel_entry", 0,0,"refresh_data();",1); ?> 
							</td>	  
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
		<div id="list_production_nos" style="width:310px; overflow:auto; float:left; margin-left:10px;"></div> 
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>