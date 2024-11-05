<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Embellishment Bill Issue
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	16-01-2019
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

echo load_html_head_contents("Embl. Bill Issue","../../", 1, 1, $unicode,'','');
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
			load_drop_down( 'requires/embl_bill_issue_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
		}
		else if(within_group==2 && type==1)
		{
			load_drop_down( 'requires/embl_bill_issue_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
		}
		if(within_group==1)
		{
			load_drop_down( 'requires/embl_bill_issue_controller', party_name, 'load_drop_down_party_location', 'partylocation_td' );
		}
		//fnc_party_location(within_group);
	}
	
	function fnc_job_no()
	{
		if ( form_validation('cbo_company_name*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var title = 'Order Search'
			var page_link='requires/embl_bill_issue_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				var ex_data=theemail.split('***')
				
				$("#txtJob_no").val( ex_data[0] );
				$("#txt_wo_no").val( ex_data[1] );
				$("#txtStyleRef").val( ex_data[2] );
				$("#txtBuyerName").val( ex_data[3] );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+ex_data[0]+'**'+1, 'load_php_dtls_form', '', 'requires/embl_bill_issue_controller');
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
					fnc_total_calculate();
				}
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_within_group').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}
	
	function fnc_embl_bill_issue( operation )
	{
		if(operation==2)
		{
			alert("Delete Restricted.");
			return;
		}
		if ( form_validation('cbo_company_name*cbo_party_name*txt_bill_date*txtJob_no', 'Company Name*Party*Bill Date*Job No')==false )
		{
			return;
		}
		else
		{
			if(operation==4)
			{
				var report_title=$( "div.form_caption" ).html();
				print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+report_title, "embl_bill_issue_print", "requires/embl_bill_issue_controller") 
				//return;
				show_msg("3");
			}
			else
			{
				var data_str="";
				
				var data_str=get_submitted_data_string('txt_bill_no*cbo_company_name*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*txt_bill_date*txt_remarks*txtJob_no*txt_update_id',"../../");
				var tot_row=$('#dtls_tbody tr').length;
				 var k=0;
				 
				for (var i=1; i<=tot_row; i++)
				{
					var rate=$('#txtbillrate_'+i).val();
					if(rate*1>0)
					{
						k++;
						data_str+="&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&txtdeliveryid_" + k + "='" + $('#txtdeliveryid_'+i).val()+"'"+"&sysnotd_" + k + "='" + $('#sysnotd_'+i).text()+"'"+"&deliverydatetd_" + k + "='" + $('#deliverydatetd_'+i).text()+"'"+"&txtbillqty_" + k + "='" + $('#txtbillqty_'+i).val()+"'"+"&txtbillrate_" + k + "='" + $('#txtbillrate_'+i).val()+"'"+"&txtbillamount_" + k + "='" + $('#txtbillamount_'+i).val()+"'"+"&txtRemarks_" + k + "='" + $('#txtRemarks_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtDtlsUpdateId_" + k + "='" + $('#txtDtlsUpdateId_'+i).val()+"'";
					}
				}
				if(k==0)
				{
					alert("Please input Current Rate.");
					return;
				}
				var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
				//alert (data); return;
				freeze_window(operation);
				http.open("POST","requires/embl_bill_issue_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_embl_bill_issue_response;
			}
		}
	}
	
	function fnc_embl_bill_issue_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			
			/*if(trim(response[0])=='emblIssue')
			{
				alert("Issue Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_update_id').value= response[1];
				document.getElementById('txt_bill_no').value = response[2];
				var list_view_orders = return_global_ajax_value( response[1]+'**'+$('#txtJob_no').val()+'**'+2, 'load_php_dtls_form', '', 'requires/embl_bill_issue_controller');
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embl_bill_issue',1);
				}
			}
			if(response[0]==2)
			{
				location.reload(); 
			}
			release_freezing();
		}
	}
	
	function openmypage_bill_no()
	{ 
		if(form_validation('cbo_company_name', 'Company Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var page_link='requires/embl_bill_issue_controller.php?action=bill_popup&data='+data;
			var title="Bill Popup";	
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
				//var theemail=this.contentDoc.getElementById("selected_job");
				var theemail=this.contentDoc.getElementById("selected_job").value;
				//alert (theemail); 
				
				var bill_data=theemail.split("***");
				if (bill_data[0]!="")
				{
					freeze_window(5);
					reset_form('','','txt_bill_no*cbo_location_name*cbo_within_group*cbo_party_name*cbo_party_location*txt_bill_date*txt_remarks*txtJob_no*txt_update_id','','');
					
					$('#txt_update_id').val(bill_data[0]);
					$('#txt_bill_no').val(bill_data[1]);
					$('#cbo_location_name').val(bill_data[2]);
					$('#cbo_within_group').val(bill_data[3]);
					$('#cbo_party_name').val(bill_data[4]);
					$('#cbo_party_location').val(bill_data[5]);
					$('#txt_bill_date').val(bill_data[6]);
					$('#txt_remarks').val(bill_data[7]);
					
					$('#txtJob_no').val(bill_data[8]);
					$('#txt_wo_no').val(bill_data[9]);
					$('#txtStyleRef').val(bill_data[10]);
					$('#txtBuyerName').val(bill_data[11]);
					
					$('#cbo_company_name').attr('disabled','disabled');
					$('#cbo_within_group').attr('disabled','disabled');
					$('#cbo_party_name').attr('disabled','disabled');
					
					//get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/embl_bill_issue_controller" );
					
					var list_view_orders = return_global_ajax_value( bill_data[0]+'**'+bill_data[8]+'**'+1, 'load_php_dtls_form', '', 'requires/embl_bill_issue_controller');
					if(list_view_orders!='')
					{
						$("#dtls_tbody tr").remove();
						$("#dtls_tbody").append(list_view_orders);
					}
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embl_bill_issue',1);
					release_freezing();
				}
			}
		}
	}
	
	function fnc_amount_calculation(val,inc)
	{
		var qty=$("#txtbillqty_"+inc).val()*1;
		
		var amount=qty*val;
		$("#txtbillamount_"+inc).val( number_format(amount,2,'.','' ));
	}
	
	function fnc_party_location(val)
	{
		if(val==1) $('#cbo_party_location').removeAttr('disabled','disabled');
		else $('#cbo_party_location').attr('disabled','disabled');
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#dtls_tbody tr').length;
		//alert(rowCount)
		math_operation( "txtTotbillqty", "txtbillqty_", "+", rowCount );
		math_operation( "txtTotbillamount", "txtbillamount_", "+", rowCount );
	} 
</script>

</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="embbillissue_1" id="embbillissue_1" autocomplete="off"> 
			<fieldset style="width:1000px;">
			<legend>Embl. Bill Issue</legend>
                <table width="990" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Bill ID</strong></td>
                        <td colspan="4">
                        <input class="text_boxes"  type="text" name="txt_bill_no" id="txt_bill_no" onDblClick="openmypage_bill_no();" placeholder="Double Click" style="width:140px;" readonly />
                        <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:90px" class="text_boxes" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="160"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/embl_bill_issue_controller', this.value, 'load_drop_down_location', 'location_td'); fnc_load_party(1,document.getElementById('cbo_within_group').value);"); ?>
                        </td>
                        <td width="100">Location</td>
                        <td width="160" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="80" class="must_entry_caption">Within Group</td>
                        <td width="130"><?php echo create_drop_down( "cbo_within_group", 120, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value); fnc_party_location(this.value);" ); ?></td>
                        
                        <td width="100" class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $selected, "fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                        
                    </tr>
                    <tr>
                    	<td>Party Location</td>
                        <td id="partylocation_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "--Party Location--", $selected, "" ); ?></td>
                    	<td class="must_entry_caption">Bill Date</td>
                        <td><input type="text" name="txt_bill_date" id="txt_bill_date" style="width:135px" class="datepicker" value="<? echo date('d-m-Y'); ?>" /></td>
                        <td>Remarks</td>
                        <td colspan="3"><input type="text" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:360px;" /></td>
                    </tr>
                    <tr style="display:none">
                        <td class="must_entry_caption">Delivery Date</td>                                              
                        <td>
                            <input class="datepicker" type="text" style="width:55px" name="txt_bill_form_date" id="txt_bill_form_date" placeholder="From Date" disabled />&nbsp;
                            <input class="datepicker" type="text" style="width:55px" name="txt_bill_to_date" id="txt_bill_to_date" placeholder="To Date" disabled />
                        </td>
                        <td>Sys. No</td>                                              
                        <td>
                            <input class="text_boxes" type="text" style="width:135px" name="txt_manual_challan" id="txt_manual_challan" disabled />
                        </td>
                        <td class="must_entry_caption">&nbsp;</td>                                              
                        <td>
                            <input class="formbutton" type="button" onClick="fnc_list_search(0);" style="width:130px" name="btn_populate" value="Populate" id="btn_populate" />
                        </td>
                    </tr> 
                </table>
            </fieldset>
            <br>
            <fieldset style="width:1110px;">
            <legend>Embellishment Bill Details</legend>
                <table style="width:1110px;" cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all">
                    <thead class="form_table_header">
                    	<tr>
	                		<th colspan="4" class="must_entry_caption">Job No &nbsp;&nbsp;&nbsp;
	                			<input type="text" name="txtJob_no" id="txtJob_no" value="" class="text_boxes"  style="width:120px" placeholder="Browse" onDblClick="fnc_job_no();" readonly/>
                                <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px;" />
                            </th>
 	                		<th>Work Order No</th>
	                		<th colspan="2"><input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px;" placeholder="Display" readonly /></th>
                            <th>Buyer Style Ref.</th>
	                		<th colspan="2"><input type="text" name="txtStyleRef" id="txtStyleRef" value="" class="text_boxes"  style="width:110px" placeholder="Display" readonly/></th>
                            <th>Buyer</th>
                            <th colspan="3"><input type="text" name="txtBuyerName" id="txtBuyerName" value="" class="text_boxes"  style="width:110px" placeholder="Display" readonly/></th>
                            <th>&nbsp;</th>
                	    </tr>
                	    <tr>
	                        <th width="30">SL</th>
                            <th width="60">Delivery ID</th>
                            <th width="60">Delivery Date</th>
                            <th width="100">Buyer PO</th>
	                        <th width="100">Gmts Item</th>
	                        <th width="110">Body Part</th>
	                        <th width="90">Embel. Name</th>
	                        <th width="80">Process/Type</th>
	                        <th width="80">Color</th>
	                        <th width="60">Size</th>
                            <th width="60">Qty</th>
                            <th width="60">Initial Rate</th>
                            <th width="60" class="must_entry_caption">Final Rate</th>
                            <th width="60">Amount</th>
	                        <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="dtls_tbody">
                    	<tr bgcolor="#FFFFFF">
                    		<td align="center">1<input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" value="" /></td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td align="center"><input type="text" name="txtbillqty_1" id="txtbillqty_1" class="text_boxes_numeric" style="width:50px;" disabled /></td>
                    		<td align="center"><input type="text" name="txtInitialRate_1" id="txtInitialRate_1" class="text_boxes_numeric" style="width:50px;" disabled /></td>
                            <td align="center"><input type="text" name="txtbillrate_1" id="txtbillrate_1" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_amount_calculation(this.value,1); fnc_total_calculate();" /></td>
                            <td align="center"><input type="text" name="txtbillamount_1" id="txtbillamount_1" class="text_boxes_numeric" style="width:50px;" readonly /></td>
                    		<td align="center"><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" style="width:80px;" />
                            	<input type="hidden" name="txtDtlsUpdateId_1" id="txtDtlsUpdateId_1" style="width:50px" class="text_boxes" value="" />
                                <input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:50px" class="text_boxes" value="" />
                                <input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:50px" class="text_boxes" value="" />
                            </td> 
                    	</tr>
                    </tbody> 
                    <tfoot>
                    	<tr class="tbl_bottom" name="tr_btm" id="tr_btm">
                    		<td align="center">&nbsp;</td>
                            <td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>Total:</td>
                    		<td align="center"><input type="text" name="txtTotbillqty" id="txtTotbillqty" class="text_boxes_numeric" style="width:50px;" readonly /></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="center"><input type="text" name="txtTotbillamount" id="txtTotbillamount" class="text_boxes_numeric" style="width:50px;" readonly /></td>
                    		<td>&nbsp;</td> 
                    	</tr>
                    </tfoot>                   
                </table>            
                <table width="830" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" valign="middle" class="button_container">
                        	<? echo load_submit_buttons($permission,"fnc_embl_bill_issue",0,1,"reset_form('embbillissue_1', '','','','')",1); ?>
                        </td>
                    </tr>   
                </table>
            </fieldset>          
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>