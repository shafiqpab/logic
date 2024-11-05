<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Wet Production [Sweater]
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	08-04-2020
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
echo load_html_head_contents("Wet Production [Sweater]", "../../", 1, 1,'','','');
?>	
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	var str_supervisor = [<? echo substr(return_library_autocomplete( "select distinct(operator_name) as supervisor from subcon_embel_production_dtls", "operator_name"  ), 0, -1); ?>];
	
	function fnc_embel_entry(operation)
	{
		if(operation==4)
		{
			if ( $('#txt_production_id').val()=='')
			{
				alert ('Production ID Not Save.');
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "wash_production_entry_print", "requires/wet_production_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if( form_validation('cbo_company_id*txt_batch_no*txt_prod_date*txt_reporting_hour','Company*Batch No.*Production Date*Reporting Hour')==false )
			{
				return;
			}
			
			var j=0; var dataString=''; //var all_barcodes='';
			$("#wash_details_container").find('tr').each(function()
			{
				var colorSizeId=$(this).find('input[name="colorSizeId[]"]').val();
				var updateIdDtls=$(this).find('input[name="updateIdDtls[]"]').val();
				//var txtbuyerPoId=$(this).find('input[name="txtbuyerPoId[]"]').val();
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				
				var txtProdQty=$(this).find('input[name="txtProdQty[]"]').val();
				var txtRejQty=$(this).find('input[name="txtRejQty[]"]').val();
				var txtReWashQty=$(this).find('input[name="txtReWashQty[]"]').val();
				var txtRemarks=$(this).find('input[name="txtRemarks[]"]').val();
				var txtPrevProdQty=$(this).find('input[name="txtPrevProdQty[]"]').val();
				var txtbatchQty=$(this).find('input[name="txtbatchQty[]"]').val();
				
				if( txtProdQty*1>0 || txtReWashQty*1>0)
				{
					j++;
					dataString += '&colorSizeId_' + j + '=' + colorSizeId + '&txtPoId_' + j + '=' + txtPoId  + '&updateIdDtls_' + j + '=' + updateIdDtls + '&txtProdQty_' + j + '=' + txtProdQty+ '&txtRejQty_' + j + '=' + txtRejQty+ '&txtReWashQty_' + j + '=' + txtReWashQty+ '&txtRemarks_' + j + '=' + txtRemarks+ '&txtPrevProdQty_' + j + '=' + txtPrevProdQty+ '&txtbatchQty_' + j + '=' + txtbatchQty;
				}
			});
			if(j<1)
			{
				alert('Please Insert Qty At Least One Row.');
				return;
			}
			//alert(dataString);return;
			
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_production_id*update_id*cbo_company_id*cbo_location*txt_batch_id*cbo_buyer_name*txt_job_no*cbo_floor_id*cbo_machine_id*txt_prod_date*txt_reporting_hour*txt_super_visor*cbo_operation*cbo_sub_operation*cboShift',"../../")+dataString+'&total_row='+j;
			//alert (data);return;
			freeze_window(operation);
			
			http.open("POST","requires/wet_production_controller.php",true);
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
			if(trim(response[0])=='emblQc'){
				alert("QC Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			 }
			show_msg(response[0]);
			
			if( response[0]==0 || response[0]==1 )
			{
				document.getElementById('update_id').value = response[1];
				document.getElementById('txt_production_id').value = response[2];
				var batch_id = $('#txt_batch_id').val();
				var jobNo = $('#txt_job_no').val();
				fnc_dtls_data_load(batch_id,jobNo,response[1]);
				set_button_status(1, permission, 'fnc_embel_entry',1,0);
				$('#cbo_company_id').attr('disabled',true);
				$('#txt_batch_no').attr('disabled',true);
			}
			if( response[0]==2 )
			{
				location.reload();
			}
			release_freezing();	
		}
	}
	 
	function openmypage_batch()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Batch Pop-up';	
			var page_link = 'requires/wet_production_controller.php?cbo_company_id='+cbo_company_id+'&action=batch_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var str_data=this.contentDoc.getElementById("selected_str_data").value;	 //Access form field with id="emailfield"
				
				if(update_id!="")
				{
					freeze_window(5);
					var estr_data=str_data.split("___");
					
					$('#txt_batch_id').val(estr_data[0]);
					$('#txt_batch_no').val( estr_data[1] );
					
					$('#txt_job_no').val(estr_data[2]);
					
					$('#txt_style').val(estr_data[3]);
					$('#txt_buyer').val(estr_data[4]);
					load_drop_down( 'requires/wet_production_controller', cbo_company_id+'_'+1, 'load_drop_down_buyer', 'party_td');
					$('#cbo_buyer_name').val(estr_data[5]);
					$('#cbo_operation').val(estr_data[6]);
					$('#cbo_operation').attr('disabled',true);
					
					fnc_dtls_data_load(estr_data[0],estr_data[2],0);
					
					release_freezing();
				} 
			}
		}
	}
	
	function fnc_dtls_data_load(batch_id,jobno,uid)
	{
		//alert(batch_id+'_'+uid); return;
		var cbo_company_id = $('#cbo_company_id').val();
		var list_view_orders = return_global_ajax_value( cbo_company_id+'***'+batch_id+'***'+jobno+'***'+uid, 'order_details', '', 'requires/wet_production_controller');
		if(list_view_orders!='')
		{
			$("#wash_details_container").html(list_view_orders);
		}
		fnc_total_calculate();
	}
	 
	function fnc_embel_prod_id()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 
			var company_id = $('#cbo_company_id').val();
			var title = 'Production ID Selection Form';	
			var page_link = 'requires/wet_production_controller.php?cbo_company_id='+company_id+'&action=embel_production_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//Access the form inside the modal window
				var emblishment_data=this.contentDoc.getElementById("hidden_production_data").value;
				//alert(emblishment_id_data);return;
				var emb_data = emblishment_data.split("***");
				if(emb_data[0]!="")
				{
					freeze_window(5);
					
					$('#update_id').val(emb_data[0]);
					$('#txt_production_id').val(emb_data[1]);
					$('#cbo_location').val(emb_data[2]);
					$('#txt_batch_id').val(emb_data[3]);
					$('#txt_batch_no').val(emb_data[4]);
					$('#txt_job_no').val(emb_data[5]);
					
					load_drop_down( 'requires/wet_production_controller', company_id+'_'+1, 'load_drop_down_buyer', 'party_td');
					$('#cbo_buyer_name').val(emb_data[6]);
					$('#txt_style').val(emb_data[7]);
					$('#txt_buyer').val(emb_data[8]);
					load_drop_down( 'requires/wet_production_controller', company_id+'__'+emb_data[2], 'load_drop_down_floor', 'floor_td');
					load_drop_down( 'requires/wet_production_controller',company_id+'_'+emb_data[18], 'load_drop_down_machine', 'machine_td' );
					//alert(1);
					
					$('#txt_prod_date').val(emb_data[9]);
					$('#txt_reporting_hour').val(emb_data[10]);
					$('#txt_super_visor').val(emb_data[11]);
					$('#cboShift').val(emb_data[12]);
					//alert(estr_data[20]+'=='+emb_data[21]);
					$('#cbo_floor_id').val(emb_data[13]);
					$('#cbo_machine_id').val(emb_data[14]);
					$('#cbo_operation').val(emb_data[15]);
					$('#cbo_sub_operation').val(emb_data[16]);
					
					$('#cbo_sub_operation').attr('readonly',true);
					$('#cbo_operation').attr('disabled',true);
					$('#txt_batch_no').attr('disabled',true);
					$('#cbo_company_id').attr('disabled',true);
					//alert(3);
					fnc_dtls_data_load(emb_data[3],emb_data[5],emb_data[0]);
					set_button_status(1, permission, 'fnc_embel_entry',1,0);
					release_freezing();
				}
			}
		}
	}

	function fnc_valid_time(val,field_id)
	{
		var val_length=val.length;
		if(val_length==2)
		{
			document.getElementById(field_id).value=val+":";
		}
	
		var colon_contains=val.includes(":");
		if(colon_contains==false)
		{
			if(val>23)
			{
				document.getElementById(field_id).value='23:';
			}
		}
		else
		{
			var data=val.split(":");
			var minutes=data[1];
			var str_length=minutes.length;
			var hour=data[0]*1;
	
			if(hour>23)
			{
				hour=23;
			}
	
			if(str_length>=2)
			{
				minutes= minutes.substr(0, 2);
				if(minutes*1>59)
				{
					minutes=59;
				}
			}
	
			var valid_time=hour+":"+minutes;
			document.getElementById(field_id).value=valid_time;
		}
	}
	
	function numOnly(myfield, e, field_id)
	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
		return true;
		// numbers
		else if ((("0123456789:").indexOf(keychar) > -1))
		{
			var dotposl=document.getElementById(field_id).value.lastIndexOf(":");
			if(keychar==":" && dotposl!=-1)
			{
				return false;
			}
			return true;
		}
		else
			return false;
	}
	
	function fn_autocomplete()
	{
		 $("#txt_super_visor").autocomplete({
			 source: str_supervisor
		  });
	}
	
	function load_machine()
	{
		//var cbo_company_id = $('#cbo_company_id').val();
		var cbo_source =1; //$('#cbo_knitting_source').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_floor_id = $('#cbo_floor_id').val();
		if(cbo_source==1)
		{
			load_drop_down( 'requires/wet_production_controller',cbo_company_id+'_'+cbo_floor_id, 'load_drop_down_machine', 'machine_td' );
		}
		else
		{
			load_drop_down( 'requires/wet_production_controller',0+'_'+0, 'load_drop_down_machine', 'machine_td' );
		}
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
		var rowCount = $('#wash_details_container tr').length;
		//alert(rowCount)
		math_operation( "txtTotProdQty", "txtProdQty_", "+", rowCount );
		math_operation( "txtTotRejQty", "txtRejQty_", "+", rowCount );
		math_operation( "txtReWashQty", "txtReWashQty_", "+", rowCount );
	} 
	
	function fnc_production_qty_validation()
	{
		var rowCount = $('#wash_details_container tr').length;
		var PrevProdQty = $('#txtPrevProdQty_'+ rowCount).val()*1;	
		var curentProdQty = $('#txtProdQty_'+ rowCount).val()*1;
		var batchQty = $('#txtbatchQty_'+ rowCount).val()*1;
		var productqtyballance=batchQty-PrevProdQty;
		if(curentProdQty>productqtyballance)
		{
			alert("QC Pass Quantity Greater Than Total Batch Quantity"); 
			$('#txtProdQty_'+ rowCount).val('');
			return;
		}
	} 
	
 </script>
</head>

<body onLoad="set_hotkey();">
	<div align="center" style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission); ?>
    <form name="productionEntry_1" id="productionEntry_1">
        <fieldset style="width:800px; margin-bottom:10px;"">
        <legend>Wet Production</legend> 
            <table width="100%" cellpadding="1" cellspacing="1" border="0" > 
                <tr>
                    <td colspan="3" align="right"><strong>Production ID</strong></td>
                    <td colspan="3">
                        <input type="text" name="txt_production_id" id="txt_production_id" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="fnc_embel_prod_id();" />
                        <input type="hidden" name="update_id" id="update_id"/>
                    </td>
                </tr>
                <tr>
                    <td width="100" class="must_entry_caption">Company Name</td>
                    <td width="160"><? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", 0, "load_drop_down('requires/wet_production_controller', this.value, 'load_drop_down_location', 'location_td'); location_select();get_php_form_data( this.value, 'company_wise_report_button_setting','requires/wet_production_controller');"); ?></td>
                    <td width="100">Location</td>
                    <td width="160" id="location_td"><? echo create_drop_down("cbo_location", 150, $blank_array,"", 1,"-Select Location-", 0,""); ?></td>
                    <td width="100" class="must_entry_caption">Batch No.</td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:140px;" placeholder="Browse" onDblClick="openmypage_batch();" readonly />
                        <input type="hidden" name="txt_batch_id" id="txt_batch_id" class="text_boxes" value="0" style="width:40px;" />
                    </td>
                </tr>
                <tr>
                    <td>Job No.</td>
                    <td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:140px;" disabled placeholder="Display"/></td>
                    <td>Style Ref.</td>
                    <td><input type="text" name="txt_style" id="txt_style" class="text_boxes" value="" style="width:140px;" disabled placeholder="Display" /></td>
                    <td>Buyer</td>
                    <td><input type="text" name="txt_buyer" id="txt_buyer" class="text_boxes_numeric" style="width:140px;" disabled placeholder="Display"/></td>
                </tr>
                <tr>
                    <td>Party Name</td>
                    <td id="party_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 ); ?></td>
                   	<td>Floor</td>
                    <td id="floor_td"><? echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", $selected, "",1 ); ?></td>
                    <td>Machine</td>
                    <td id="machine_td"><? echo create_drop_down( "cbo_machine_id", 150, $blank_array,"", 1, "--Select Machine--", $selected, "",1 ); ?></td>
                </tr>
                <tr>
                    <td>Process Name</td>
                    <td><? echo create_drop_down( "txt_process_id", 150, $wash_type,"", 1, " Select Process", $selected, "" ,"1","1"); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </fieldset>                 
        <fieldset style="width:1080px;" >
        <legend>Wet Production Details Info</legend>
            <table cellpadding="0" cellspacing="0" width="1080" class="rpt_table" border="1" rules="all" id="tbl_item_details">
                <thead>
                	<tr>
                        <th class="must_entry_caption">Date</th>
                        <th><input type="text" name="txt_prod_date" id="txt_prod_date" class="datepicker" style="width:70px;" placeholder="Write" value="<? echo date("d-m-Y");?>" readonly/></th>
                        <th class="must_entry_caption">Reporting Hour</th>
                        <th>
                            <input name="txt_reporting_hour" id="txt_reporting_hour" class="text_boxes" style="width:70px" placeholder="24 Hour Format" onBlur="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyUp="fnc_valid_time(this.value,'txt_reporting_hour');" onKeyPress="return numOnly(this,event,this.id);" /></th>
                        <th>Operator / Superviser</th>
                        <th><input type="text" name="txt_super_visor" id="txt_super_visor" class="text_boxes" onKeyUp="fn_autocomplete();" style="width:65px"></th>
                        <th>Operation</th>
                        <th><? echo create_drop_down( "cbo_operation",80, $wash_operation_arr,"",1, "-- Select --",0,""); ?></th>
                        <th>Sub Operation</th>
                        <th><input type="text" name="cbo_sub_operation" id="cbo_sub_operation" class="text_boxes" style="width:60px"></th>
                        <th colspan="2">Shift<? echo create_drop_down( "cboShift", 65, $shift_name,"", 1, 'Select', 0,"",'','','','','','','',''); ?></th>
                        <th></th> 
                    </tr>
                	<tr>
	                   	<th width="30">SL</th>
	                    <th width="100">PO No.</th>
                        <th width="100">Country</th>
	                    <th width="90">Gmts Item</th>
                        <th width="90">Gmts Color</th>
                        <th width="80">Gmts Size</th>
	                    <th width="110">Process Name</th>
                        <th width="80" class="must_entry_caption">Batch Qty. (Pcs) </th>
                        <th width="80">Prev Prod.Qty (Pcs)</th>
	                    <th width="80" class="must_entry_caption">QC Pass Qty (Pcs)</th>
                        <th width="70">Reject Qty (Pcs)</th>
                        <th width="70" class="must_entry_caption">Re Wash Qty (Pcs)</th>
	                    <th>Remarks</th>
                    </tr>
                </thead> 
                <tbody id="wash_details_container">
                    <tr name="tr[]" id="tr_1">
						<td align="center">1</td>
                        <td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
						<td align="right">
                        	<input type="text" name="txtProdQty[]" id="txtProdQty_1" class="text_boxes_numeric" style="width:80px" placeholder="Write" onBlur="fnc_total_calculate();" />
                            <input type="hidden" name="txtPrevProdQty[]" id="txtPrevProdQty_1" class="text_boxes_numeric" style="width:80px" />
                            <input type="hidden" name="txtbatchQty[]" id="txtbatchQty_1" class="text_boxes_numeric" style="width:80px" />
                        </td>
                        <td align="right"><input type="text" name="txtRejQty[]" id="txtRejQty_1" class="text_boxes_numeric" style="width:60px" placeholder="Write" onBlur="fnc_total_calculate();" /></td>
                        <td align="right"><input type="text" name="txtReWashQty[]" id="txtReWashQty_1" class="text_boxes_numeric" style="width:60px" placeholder="Write" onBlur="fnc_total_calculate();" /></td>
			            <td>
                        	<input type="text" name="txtRemarks[]" id="txtRemarks_1" class="text_boxes" style="width:50px" placeholder="Write" />
                            <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" style="width:50px" />
                            <input type="hidden" name="txtbuyerPoId[]" id="txtbuyerPoId_1" style="width:50px" />
                            <input type="hidden" name="txtPoId[]" id="txtPoId_1" style="width:50px" />
							<input type="hidden" name="colorSizeId[]" id="colorSizeId_1" style="width:50px" />
                        </td>
					</tr>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                        <td colspan="9">Total:</td>
                        <td align="center"><input type="text" name="txtTotProdQty" id="txtTotProdQty" class="text_boxes_numeric" style="width:80px" placeholder="Display" readonly /></td>
                        <td align="center"><input type="text" name="txtTotRejQty" id="txtTotRejQty" class="text_boxes_numeric" style="width:60px" placeholder="Display" readonly /></td>
                        <td align="center"><input type="text" name="txtReWashQty" id="txtReWashQty" class="text_boxes_numeric" style="width:60px" placeholder="Display" readonly /></td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            </fieldset>
            <table cellpadding="0" cellspacing="1" width="1080">
                <tr>
                     <td align="center" colspan="6" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_embel_entry", 0,0,"refresh_data();",1); ?> 
                        <input type="button" name="print" id="print" value="Print" onClick="fnc_embel_entry(4)" style="width:100px;display:none;" class="formbuttonplasminus" />
                    </td>	  
                </tr>
            </table>
    	</form>
    </div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>