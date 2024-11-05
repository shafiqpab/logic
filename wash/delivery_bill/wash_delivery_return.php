<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Wash Delivery Return
Functionality	:	
JS Functions	:
Created by		:	Md Mahbubur Rahman
Creation date 	: 	31-12-2019 
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
//-----------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Wash Delivery Return","../../", 1, 1, $unicode,1,'');
//echo load_html_head_contents("Trims Delivery Info", "../../", 1,1, $unicode,1,'');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function fnc_load_party(type,within_group)
	{
		if(form_validation('cbo_company_name','Company')==false )
		{
			$('#cbo_within_group').val(1);
			return;
		}
		var company = $('#cbo_company_name').val();
		var party_name = $('#cbo_party_name').val();
		
		if(within_group==1 && type==1)
		{
			load_drop_down( 'requires/wash_delivery_return_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2  && type==1)
		{
			load_drop_down( 'requires/wash_delivery_return_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/wash_delivery_return_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
		}
		
	}
	
	function openmypage_delv_return_no()
	{ 
		if(form_validation('cbo_company_name', 'Company Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var page_link='requires/wash_delivery_return_controller.php?action=delivery_return_popup&data='+data;
			var title="Delivery Return Popup";	
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job").value;
				var emb_data=theemail.split("***");
				//alert(emb_data); return;
				
				if (emb_data[15]!="")
				{
					freeze_window(5);
					$('#txt_update_id').val(emb_data[15]);
					$('#txt_delv_return_no').val(emb_data[1]);
					$('#cbo_location_name').val(emb_data[2]);
					load_drop_down( 'requires/wash_delivery_return_controller', emb_data[2]+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');
					$('#cbo_floor_name').val(emb_data[18]);
					$('#cbo_within_group').val(emb_data[3]);
					$('#txt_delivery_return_date').val(emb_data[5]);
					$('#txt_return_challan_no').val(emb_data[6]);
					$('#txtJob_no').val(emb_data[7]);
					$('#txt_wo_no').val(emb_data[8]);
					$('#txtStyleRef').val(emb_data[9]);
					$('#txtBuyerName').val(emb_data[10]);
					if(emb_data[3]==1)
					{
						fnc_load_party(2,1);
					}
					else if(emb_data[3]==2)
					{
						$('#cbo_party_location').attr('disabled','disabled');
					}
					fnc_load_party(1,emb_data[3]);
					$('#cbo_party_name').val(emb_data[4]);
					$('#cbo_party_location').val(emb_data[12]);
					$('#txt_delv_id').val(emb_data[16]);
					$('#txt_delv_no').val(emb_data[17]);					
					if(emb_data[3]==1)
					{
						fnc_load_party(3,1);
					}
					$('#cbo_company_name').attr('disabled','disabled');
					$('#cbo_within_group').attr('disabled','disabled');
					$('#cbo_party_name').attr('disabled','disabled');
					$('#txt_delv_no').attr('disabled','disabled');
					var list_view_orders = return_global_ajax_value( emb_data[0]+'**'+emb_data[7]+'**'+1+'**'+emb_data[15]+'**'+emb_data[16], 'load_php_dtls_form', '', 'requires/wash_delivery_return_controller');
					
					//alert(list_view_orders);return;
					if(list_view_orders!='')
					{
						$("#dtls_tbody tr").remove();
						$("#dtls_tbody").append(list_view_orders);
					}
					
					fnc_total_calculate();
					fnc_production_qty_ability();
					set_button_status(1, permission, 'fnc_wash_delivery_return',1);
					release_freezing();
				}
			}
		}
	}
	
	function openmypage_delv_no()
	{ 
		if(form_validation('cbo_company_name', 'Company Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var page_link='requires/wash_delivery_return_controller.php?action=delivery_popup&data='+data;
			var title="Delivery Popup";	
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job").value;
				var emb_data=theemail.split("***");
				//alert(emb_data); return;
				
				if (emb_data[0]!="")
				{
					freeze_window(5);
					$('#txt_delv_id').val(emb_data[0]);
					$('#txt_delv_no').val(emb_data[1]);
					$('#cbo_location_name').val(emb_data[2]);
					$('#cbo_within_group').val(emb_data[3]);
					$('#txtJob_no').val(emb_data[7]);
					$('#txt_wo_no').val(emb_data[8]);
					$('#txtStyleRef').val(emb_data[9]);
					$('#txtBuyerName').val(emb_data[10]);
					if(emb_data[3]==1)
					{
						fnc_load_party(2,1);
					}
					else if(emb_data[3]==2)
					{
						$('#cbo_party_location').attr('disabled','disabled');
					}
					fnc_load_party(1,emb_data[3]);
					$('#cbo_party_name').val(emb_data[4]);
					$('#cbo_party_location').val(emb_data[12]);					
					if(emb_data[3]==1)
					{
						fnc_load_party(3,1);
					}
					$('#cbo_company_name').attr('disabled','disabled');
					$('#cbo_within_group').attr('disabled','disabled');
					$('#cbo_party_name').attr('disabled','disabled');
					var list_view_orders = return_global_ajax_value( emb_data[0]+'**'+emb_data[7]+'**'+1, 'load_php_dtls_form', '', 'requires/wash_delivery_return_controller');
					
					//alert(list_view_orders);return;
					if(list_view_orders!='')
					{
						$("#dtls_tbody tr").remove();
						$("#dtls_tbody").append(list_view_orders);
					}
					
					fnc_total_calculate();
					fnc_production_qty_ability();
					release_freezing();
				}
			}
		}
	}
	function fnc_wash_delivery_return( operation )
	{
		
		
			if(operation==4)
			{
				var report_title=$( "div.form_caption" ).html();
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+$('#cbo_within_group').val()+'*'+report_title+'*'+$('#txtJob_no').val()+'*'+$('#txt_wo_no').val()+'*'+$('#cbo_party_name').val(), "challan_print", "requires/wash_delivery_return_controller") 
				//return;
				show_msg("3");
			}
		
			var within_group=$('#cbo_within_group').val();
			if(within_group==1)
			{
				if ( form_validation('cbo_company_name*cbo_location_name*cbo_party_name*txt_delivery_return_date*txtJob_no', 'Company Name*Location*Party*Delivery Date*Job No')==false )
				{
					return;
				}
			}
			else if(within_group==2)
			{
				if ( form_validation('cbo_company_name*cbo_location_name*cbo_party_name*txt_delivery_return_date*txtJob_no', 'Company Name*Location*Party*Delivery Date*Job No')==false )
				{
					return;
				}
			}		
			var data_str="";
			
			var data_str=get_submitted_data_string('txt_delv_return_no*txt_update_id*txt_update_details_id*cbo_company_name*cbo_location_name*cbo_floor_name*cbo_within_group*cbo_party_name*cbo_party_location*txt_delivery_return_date*txt_return_challan_no*txt_delv_no*txt_delv_id*txtJob_no*txt_order_id*txt_wo_no*txtStyleRef*txtBuyerName',"../../");
			var tot_row=$('#dtls_tbody tr').length;
			 var k=0; var check_field=0;
			 //alert(data_str);
			for (var i=1; i<=tot_row; i++)
			{
				var qty=$('#txtTotCurrReturnDelv_'+i).val(); 
				var next_process=$('#cbo_next_process_'+i).val();
				
				if(next_process=='' || next_process==0)
				{
					alert('Please Fill up Next Process ');
					check_field=1; return;
				} 
				 
				if(qty*1>0)
				{
					k++;
					data_str+="&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&txtPrvCurrDelv_" + k + "='" + $('#txtPrvCurrDelv_'+i).val()+"'"+"&txtTotCurrReturnDelv_" + k + "='" + $('#txtTotCurrReturnDelv_'+i).val()+"'"+"&txtTotCurrReturnDelvBalance_" + k + "='" + $('#txtTotCurrReturnDelvBalance_'+i).val()+"'"+"&txtDtlsUpdateId_" + k + "='" + $('#txtDtlsUpdateId_'+i).val()+"'"+"&txtdeliverydtlsid_" + k + "='" + $('#txtdeliverydtlsid_'+i).val()+"'"+"&txtremarks_" + k + "='" + $('#txtremarks_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&cbo_next_process_" + k + "='" + $('#cbo_next_process_'+i).val()+"'"+"&txtdelvnextprocessid_" + k + "='" + $('#txtdelvnextprocessid_'+i).val()+"'";
				}
			}
			if(k==0)
			{
				alert("Please input Total Return Qty.");
				return;
			}
			var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/wash_delivery_return_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_wash_delivery_return_response;
	
	}
	
	function fnc_wash_delivery_return_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_update_id').value= response[1];
				document.getElementById('txt_delv_return_no').value = response[2];
				document.getElementById('txtJob_no').value = response[3];
				document.getElementById('txt_update_details_id').value = response[4];
				var list_view_orders = return_global_ajax_value( response[1]+'**'+$('#txtJob_no').val()+'**'+2+'**'+$('#txt_update_id').val()+'**'+$('#txt_delv_id').val(), 'load_php_dtls_form', '', 'requires/wash_delivery_return_controller');
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_wash_delivery_return',1);
				}
			}
			release_freezing();
		}
	}
	
	
	function fnc_production_qty_ability(value,i)
	{
		//alert();
		var PrvCurrDelv = $("#txtPrvCurrDelv_"+i).val()*1;
 		var pre_delv_qty = $("#txtTotCurrReturnDelvBalance_"+i).attr('pre_delv_qty')*1;
		var CurrReturnDelv = $("#txtTotCurrReturnDelv_"+i).val()*1;
		
		//alert();
		
		var balance_qty =PrvCurrDelv-pre_delv_qty;
		
		var amount = (balance_qty-CurrReturnDelv);
 		$("#txtTotCurrReturnDelvBalance_"+i).val(number_format_common(amount,"","",1));
		
		if(CurrReturnDelv>balance_qty)
			{
				alert("Total Return Quantity Greater Than  Delivery Quantity (Pcs)"); 
				$('#txtTotCurrReturnDelv_'+ i).val('');
				$('#txtTotCurrReturnDelvBalance_'+ i).val('');
				return;
			}
		
		/*var update_id = $("#txt_update_id").val()*1;
		if(update_id!="")
		{
			if(curentProdQty>productqtyballance)
			{
				alert("QC Pass Quantity Greater Than Total Batch Quantity"); 
				$('#txtProdQty_'+ rowCount).val('');
				return;
			}
		}*/
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#dtls_tbody tr').length;
		math_operation( "TotCurrDelv", "txtPrvCurrDelv_", "+", rowCount );
		math_operation( "TotCurrReturnDelv", "txtTotCurrReturnDelv_", "+", rowCount );
		math_operation( "TotCurrReturnDelvBalance", "txtTotCurrReturnDelvBalance_", "+", rowCount );
	}
	
	 
	function next_process_validation(value,i)
	{
		var delv_next_process_id = $("#txtpoid_"+i).attr('delv_next_process_id')*1;
		var return_next_process_id =value*1;
		
		//alert(delv_next_process_id); 
			if(delv_next_process_id==1)
			{
				if(return_next_process_id==3)
				{
					alert("Next Process Dry Production Not Allow.");
					$("#cbo_next_process_"+i).val(0);
					return;
				}
			}
			else if(delv_next_process_id==2)
			{
				if(return_next_process_id==2)
				{
					alert("Next Process Re-Wash Not Allow.");
					$("#cbo_next_process_"+i).val(0);
					return;
				}
			}
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="washdeliveryreturn_1" id="washdeliveryreturn_1" autocomplete="off"> 
        <fieldset style="width:1100px;">
			<legend>Wash Delivery Return</legend>
            <table width="1090" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Delivery Return ID</strong></td>
                        <td colspan="4">
                        <input class="text_boxes"  type="text" name="txt_delv_return_no" id="txt_delv_return_no" onDblClick="openmypage_delv_return_no();" placeholder="Double Click" style="width:140px;" readonly />
                        <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:90px" class="text_boxes" value="" />
                        <input type="hidden" name="txt_update_details_id" id="txt_update_details_id" style="width:90px" class="text_boxes" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="right"></td>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="150"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_delivery_return_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
                        </td>
                        <td width="100" class="must_entry_caption">Location</td>
                        <td width="150" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="100" >Floor/Unit</td>
						<td width="150" id="floor_td"><? echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                        <td width="100" class="must_entry_caption">Within Group</td>
                        <td width="150"><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" ); ?></td>
                        
                    </tr>
                    <tr>

                    	<td width="100" class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/wash_delivery_return_controller', this.value+'_'+2, 'load_drop_down_location', 'party_location_td'); fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>

                    	<td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>
                        
                        <td class="must_entry_caption">Return Date</td>
                        <td><input type="text" name="txt_delivery_return_date" id="txt_delivery_return_date" style="width:140px" class="datepicker" value="" /></td>
                        <td>Return Challan</td>
                        <td><input class="text_boxes"  type="text" name="txt_return_challan_no" id="txt_return_challan_no"  placeholder="write" style="width:140px;" /></td>
                         
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Delivery Challan ID</td>
                        <td><input class="text_boxes"  type="text" name="txt_delv_no" id="txt_delv_no" onDblClick="openmypage_delv_no();" placeholder="Double Click" style="width:140px;" readonly />
                         <input type="hidden" name="txt_delv_id" id="txt_delv_id" style="width:90px" class="text_boxes" value="" />
                        </td>
                    </tr>
                    </table>
            </fieldset>
            <br>
            <fieldset style="width:1400px;">
            <legend>Wash Delivery Return Details</legend>
                <table style="width:1400px;" cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all">
                    <thead class="form_table_header">
                    	<tr style="display:none">
                       		 <th class="must_entry_caption" colspan="2">Job No</th>
	                		 <th class="must_entry_caption">
	                			<input type="text" name="txtJob_no" id="txtJob_no" value="" class="text_boxes"  style="width:120px" placeholder="Display" readonly/>
                                <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px;" />
                            </th>
 	                		<th>Work Order No</th>
	                		<th><input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px;" placeholder="Display" readonly /></th>
                            <th colspan="2">Buyer Style Ref.</th>
	                		<th><input type="text" name="txtStyleRef" id="txtStyleRef" value="" class="text_boxes"  style="width:100px" placeholder="Display" readonly/></th>
                            <th>Buyer</th>
                            <th><input type="text" name="txtBuyerName" id="txtBuyerName" value="" class="text_boxes"  style="width:90px" placeholder="Display" readonly/></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                	    </tr>
                	    <tr>
	                        <th width="30">SL</th>
                            <th width="120">Buyer PO</th>
                            <th width="120">Job No</th>
                            <th width="120">Work Order No</th>
                            <th width="120">Buyer Style Ref.</th>
                            <th width="120">Buyer</th>
	                        <th width="120">Gmts Item</th>
	                        <th width="120">Process Name</th>
	                        <th width="120">Wash Type</th>
	                        <th width="120">Color</th>
                            <th width="120">Size</th>
                            <th width="75">Delivery Qty(Pcs)</th>
	                        <th width="75">Total Return Qty</th>
	                        <th width="75">Balance</th>
                             <th width="100" class="must_entry_caption">Next Process</th>
                            <th width="100">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="dtls_tbody">
                    	<tr bgcolor="#FFFFFF">
                    	<td align="center">1
                        <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" value="" /></td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                            <td>&nbsp;</td>
                    		<td align="right"><input type="text" name="txtPrvCurrDelv_1" id="txtPrvCurrDelv_1" class="text_boxes_numeric" style="width:70px;"   readonly /></td>
                    		<td align="right"><input type="text" name="txtTotCurrReturnDelv_1" id="txtTotCurrReturnDelv_1" class="text_boxes_numeric" style="width:70px;" onBlur="fnc_production_qty_ability(this.value,1); fnc_total_calculate();" /></td>
                    		<td align="right">
                            <input type="text" name="txtTotCurrReturnDelvBalance_1" id="txtTotCurrReturnDelvBalance_1" class="text_boxes_numeric" style="width:70px;" readonly />
                           	<input type="hidden" name="txtDtlsUpdateId_1" id="txtDtlsUpdateId_1" style="width:50px" class="text_boxes" value="" />
                            <input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:50px" class="text_boxes" value="" />
                            <input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:50px" class="text_boxes" value="" />
                            <input type="hidden" name="txtdeliverydtlsid_1" id="txtdeliverydtlsid_1" style="width:50px" class="text_boxes" value="" />
                             <input type="hidden" name="txtdelvnextprocessid_1" id="txtdelvnextprocessid_1" style="width:50px" class="text_boxes" value="" />
                            </td>
                            <td><?   echo create_drop_down( "cbo_next_process_1",100,$next_process_type,'', 1,'-Select Next Process-',"","next_process_validation(this.value,1)",0,"","","","","","","cbo_next_process[]");?></td>
                            <td><input class="text_boxes"  type="text" name="txtremarks_1" id="txtremarks_1"  placeholder="write" style="width:80px;" /></td>
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
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                            <td><input type="text" name="CurrDelv" id="TotCurrDelv" class="text_boxes_numeric" style="width:70px;" placeholder="Display" readonly /></td>
                    		<td align="center"><input type="text" name="TotCurrReturnDelv" id="TotCurrReturnDelv" class="text_boxes_numeric" style="width:70px;" placeholder="Display" readonly /></td>
                    		<td align="right"><input type="text" name="TotCurrReturnDelvBalance" id="TotCurrReturnDelvBalance" class="text_boxes_numeric" style="width:80px;" placeholder="Display" readonly /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            
                    	</tr>
                    </tfoot>                   
                </table>            
                <table width="1400" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" valign="middle" class="button_container">
                        	<? 
                         		echo load_submit_buttons( $permission, "fnc_wash_delivery_return", 0,1,"reset_form('washdeliveryreturn_1','delivery_list_view','','','')",1);
							?>	
                           </td>
                    </tr>   
                </table>
            </fieldset>          
        </form>  
        <div style="width:830px; margin-top:5px;" id="delivery_list_view" align="center"></div>                       
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>