<?
/*-------------------------------------------- Comments
Purpose			: 	Wash Material Receive Return
Functionality	:
JS Functions	:
Created by		:	Md mahbubur Rahman
Creation date 	: 	03-02-2020
Updated by 		: 	
Update date		: 	
QC Performed BY	:	
QC Date			:
Comments		:
*/
//==========start ========
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

echo load_html_head_contents("Wash Material Receive Return","../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];
	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });

     });

	function fnc_material_receive_return( operation )
	{
		
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report($('#cbo_company_name').val()+'*'+$('#txt_receive_return_no').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#receive_id').val()+'*'+$('#txtJob_no').val()+'*'+$('#cbo_location_name').val()+'*'+$('#cbo_party_name').val()+'*'+$('#txt_return_date').val()+'*'+$('#cbo_within_group').val()+'*'+$('#txt_receive_no').val()+'*'+$('#txt_remarks').val()+'*'+$('#txt_return_challan').val()+'*'+$('#txtBuyerName').val(), 'receive_return_in_wash_print', 'requires/wash_metarial_receive_return_controller');
			return;
		}
		
		if ( form_validation('cbo_company_name*cbo_location_name*cbo_party_name*txt_return_challan*txt_return_date*txt_receive_no*cbo_within_group', 'Company Name*Location*Party*Receive Challan*Receive Date*Job No*within group')==false )
		{
			return;
		}
		else
		{
			var data_str="";
			var data_str=get_submitted_data_string('txt_receive_return_no*cbo_company_name*cbo_location_name*cbo_floor_name*cbo_party_name*txt_return_challan*txt_return_date*cbo_within_group*txt_receive_no*receive_id*txt_remarks*update_id*txtJob_no*txt_job_id*txt_order_id*txtBuyerName',"../../");
			var tot_row=$('#rec_issue_table tr').length;
			var k=0;
			 
			for (var i=1; i<=tot_row; i++)
			{
				var qty=$('#txtreceivereturnqty_'+i).val();
				if(qty*1>0)
				{
					k++;
					data_str+="&ordernoid_" + k + "='" + $('#ordernoid_'+i).val()+"'"+"&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&cbouom_" + k + "='" + $('#cbouom_'+i).val()+"'"+"&txtreceiveqty_" + k + "='" + $('#txtreceiveqty_'+i).val()+"'"+"&txtreceivebalance_" + k + "='" + $('#txtreceivebalance_'+i).val()+"'"+"&txtreceivereturnqty_" + k + "='" + $('#txtreceivereturnqty_'+i).val()+"'"+"&txtprereturnqty_" + k + "='" + $('#txtprereturnqty_'+i).val()+"'"+"&jobid_" + k + "='" + $('#jobid_'+i).val()+"'"+"&receiveid_" + k + "='" + $('#receiveid_'+i).val()+"'"+"&receiveNo_" + k + "='" + $('#receiveNo_'+i).val()+"'"+"&receivedtlsid_" + k + "='" + $('#receivedtlsid_'+i).val()+"'"+"&txtRemarks_" + k + "='" + $('#txtRemarks_'+i).val()+"'"+"&updatedtlsid_" + k + "='" + $('#updatedtlsid_'+i).val()+"'";
				}
			}
			var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/wash_metarial_receive_return_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_material_receive_return_response;
		}
	}
	
	function fnc_material_receive_return_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);//return;
			var response=trim(http.responseText).split('**');
			//if (response[0].length>3) reponse[0]=10;	
			/*if(trim(response[0])=='washIssue'){
				alert("Issue Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
			if(response[0]*1==17*1)
			{
				alert(response[1]);
				release_freezing(); return;
			}
			/*if(response[0]==20)
			{
				alert(response[1]);
				release_freezing();
				return;
			}*/
			show_msg(response[0]);
			
			//alert(response[3]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_receive_return_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				
			
				
				
				
				/*$("#receive_id").val(splt_val[0]);
				$("#txtJob_no").val(splt_val[1]);
				$("#txt_receive_no").val(splt_val[3]);
				$("#txt_receive_return_no").val(splt_val[4]);
				$("#txtBuyerName").val(splt_val[5]);
				update_id=splt_val[2]
				get_php_form_data( splt_val[2], "load_php_data_to_form", "requires/wash_metarial_receive_return_controller" );
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+splt_val[2], 'load_php_dtls_form', '', 'requires/wash_metarial_receive_return_controller');
				*/
				
				var list_view_orders = return_global_ajax_value( response[4]+'**'+response[3]+'**'+2+'**'+response[2], 'load_php_dtls_form', '', 'requires/wash_metarial_receive_return_controller');
				
				//var list_view_orders = return_global_ajax_value( response[2]+'**'+response[3]+'**'+2+'**'+response[2], 'load_php_dtls_form', '', 'requires/wash_metarial_receive_return_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				$('#txt_receive_no').attr('disabled','disabled');
				$('#cbo_within_group').attr('disabled','disabled');
				set_button_status(1, permission, 'fnc_material_receive_return',1);
				
			}
			if(response[0]==2)
			{
				location.reload(); 
			}
			release_freezing();
		}
	}
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
		}
		load_drop_down( 'requires/wash_metarial_receive_return_controller', document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');	
	}
	
	function fnc_load_party(within_group)
	{
		if(within_group==1)
		{
			$('#buyerpo_td').css('color','blue');
			$('#buyerstyle_td').css('color','blue');
			
		}
		else if(within_group==2)
		{
			$('#buyerpo_td').css('color','black');
			$('#buyerstyle_td').css('color','black');
		}
	}
	
	function openmypage_job_id()
	{ 
		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company Name*within group*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			var page_link='requires/wash_metarial_receive_return_controller.php?action=receive_popup&data='+data;
			var title="Receive ID Popup";	
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job").value;
				var splt_val=theemail.split("_");
				$("#receive_id").val(splt_val[0]);
				$("#txtJob_no").val(splt_val[1]);
				$("#txt_receive_no").val(splt_val[2]);
				$("#txtBuyerName").val(splt_val[3]);
				if (splt_val[0]!="")
				{
					freeze_window(5);
					var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1, 'load_php_dtls_form', '', 'requires/wash_metarial_receive_return_controller');
					if(list_view_orders!='')
					{
						$("#rec_issue_table tr").remove();
						$("#rec_issue_table").append(list_view_orders);
					}
					fnc_total_calculate();
					var within_group=document.getElementById('cbo_within_group').value*1;
					fnc_load_party(within_group);
					release_freezing();
					set_all_onclick();
				}
			}
	 	 }
	}
	
	function openmypage_return_id()
	{ 
		if ( form_validation('cbo_company_name*cbo_within_group','Company Name*within group')==false )
		{
			return;
		}
		else
		{
		
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var page_link='requires/wash_metarial_receive_return_controller.php?action=return_popup&data='+data;
		var title="Return Id Popup";	
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			//var theemail=this.contentDoc.getElementById("selected_job");
			var theemail=this.contentDoc.getElementById("selected_job").value;
			//alert (theemail);
			var splt_val=theemail.split("_");
			if (splt_val[2]!="")
			{
				freeze_window(5);
				reset_form('','','txt_receive_return_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_return_challan*txt_return_date*cbo_within_group*txtJob_no*update_id','','');
				$("#receive_id").val(splt_val[0]);
				$("#txtJob_no").val(splt_val[1]);
				$("#txt_receive_no").val(splt_val[3]);
				$("#txt_receive_return_no").val(splt_val[4]);
				$("#txtBuyerName").val(splt_val[5]);
				get_php_form_data( splt_val[2], "load_php_data_to_form", "requires/wash_metarial_receive_return_controller" );
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+splt_val[2], 'load_php_dtls_form', '', 'requires/wash_metarial_receive_return_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				var within_group=document.getElementById('cbo_within_group').value*1;
				fnc_load_party(within_group);
				set_button_status(1, permission, 'fnc_material_receive_return',1);
				release_freezing();
			}
		}
		}
	}
	function check_receive_qty_ability(value,i)
	{
		var txtreceivebalance = $("#txtreceivebalance_"+i).attr('placeholder')*1;
		var prerecre_return_qty = $("#txtprereturnqty_"+i).attr('prerecre_return_qty')*1;
		var currentqty =value*1;
		var update_id=$('#update_id').val();
		
		if(currentqty>txtreceivebalance)
		{
			alert("Return qty Excceded by Receive Balance qty.");	
			$("#txtreceivereturnqty_"+i).val('');		
			return;
		}
		
				/*var confirm_value=confirm("Return qty Excceded by Receive Balance qty. Press cancel to proceed otherwise press ok. ");
				if(confirm_value!=0)
				{
					$("#txtreceivereturnqty_"+i).val('');
				}*/
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#rec_issue_table tr').length;
		//alert(rowCount)
		math_operation( "txttotreceivereturnqty", "txtreceivereturnqty_", "+", rowCount );
	} 
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="WashmaterialreceiveReturn_1" id="WashmaterialreceiveReturn_1" autocomplete="off">  
        <fieldset style="width:900px;">
    	<legend>Wash Material Issue Return</legend>
            <table  width="900" cellspacing="2" cellpadding="0" border="0">
            	 <tr>
                    <td colspan="3" align="right">Return ID</td>
                    <td colspan="3" align="left">
                    	<input class="text_boxes"  type="text" name="txt_receive_return_no" id="txt_receive_return_no"  placeholder="Double Click" style="width:140px;"  onDblClick="openmypage_return_id('xx','Wash Return')" readonly/>
                        <input type="hidden" name="update_id" id="update_id">
                    </td>
                </tr>
                <tr>
                    <td width="110" align="right" class="must_entry_caption">Company Name</td>
                    <td width="150"> 
                        <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_metarial_receive_return_controller', this.value, 'load_drop_down_location', 'location_td' ); location_select(); load_drop_down( 'requires/wash_metarial_receive_return_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
                    </td>
                    <td width="110" align="right" class="must_entry_caption">Location Name</td>
                    <td id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?></td>
                    <td width="110" align="right">Floor/Unit</td>
					<td width="160" id="floor_td"><? echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                    
        		</tr>
                <tr>
                	<td width="110" align="right" class="must_entry_caption">Within Group</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'requires/wash_metarial_receive_return_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );fnc_load_party(this.value);" ); 
						?>
					</td>
                    <td align="right" class="must_entry_caption">Party</td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                    </td>
                    <td class="must_entry_caption" align="right">Return Challan</td>
                    <td>
                        <input class="text_boxes"  type="text" name="txt_return_challan" id="txt_return_challan" style="width:140px;" />  
                    </td>
                    
                </tr>
                <tr>
                	<td class="must_entry_caption" align="right">Return Date</td>
                    <td>
                        <input type="text" name="txt_return_date" id="txt_return_date" value="<? echo date("d-m-Y")?>"  class="datepicker" style="width:140px" />             
                    </td>
                 	<td align="right" class="must_entry_caption">Job No</td>
                  	<td  align="left">
                  
                        <input type="text" name="txtJob_no" id="txtJob_no" value="" class="text_boxes"  onDblClick="openmypage_job_id('xx','Subcontract Receive')"  placeholder="Double Click" style="width:140px;" readonly/>
                    	 <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px;" />
                            <input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:70px;" />
                       
                    </td>
                    <td  align="right">Remarks</td>
                    <td >
                    	<input class="text_boxes"  type="text" name="txt_remarks" id="txt_remarks"  placeholder="Write" style="width:150px;"/>
                    </td>
                </tr>
            </table>
      	</fieldset> 
         <br/>
            <fieldset style="width:1030px;">
            <legend>Metarial Receive Return Details</legend>
            <table cellpadding="0" cellspacing="2" border="1" width="950" id="details_tbl" rules="all">
                <thead class="form_table_header">
           			<tr>
                		<th class="must_entry_caption">Receive No</th>
                        <th class="must_entry_caption" align="left">
                          <input class="text_boxes"  type="text" name="txt_receive_no" id="txt_receive_no" style="width:140px;" readonly/>
                         <input type="hidden" name="receive_id" id="receive_id">
                           
                        </th>
                        <th align="right">Buyer</th>
                        <th colspan="9" align="left"><input type="text" name="txtBuyerName" id="txtBuyerName" value="" class="text_boxes"  style="width:120px" placeholder="" readonly/></th>
                       
            	    </tr>
                    <tr><th colspan="13">&nbsp;</th></tr>
                    <tr align="center" >
                        <th width="100" class="must_entry_caption">Order No</th>
                        <th width="100" id="buyerpo_td">Buyer PO</th>
                        <th width="100" id="buyerstyle_td">Style Ref.</th>
                        <th width="90" class="must_entry_caption">Garments Item</th>
                        <th width="90">Color</th>
                        <th width="80">Size</th>
                        <th width="50" class="must_entry_caption">UOM</th>
                        <th width="80" class="must_entry_caption">Receive Qty</th>
                        <th width="80">Rcv Balance</th>
                        <th width="80" class="must_entry_caption">Rcv Return Qty</th>
                        <th width="50">Cumulative Return Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
             	<tbody id="rec_issue_table">
                    <tr>
                        <td>
                       
                        	<input type="hidden" name="receiveid_1" id="receiveid_1">
                            <input type="hidden" name="receiveNo_1" id="receiveNo_1">
                            <input type="hidden" name="receivedtlsid_1" id="receivedtlsid_1">
                            <input type="hidden" name="ordernoid_1" id="ordernoid_1">
                            <input type="hidden" name="jobno_1" id="jobno_1">
                            <input type="hidden" name="jobid_1" id="jobid_1">
                            <input type="hidden" name="updatedtlsid_1" id="updatedtlsid_1">
                            <input class="text_boxes" name="txtorderno_1" id="txtorderno_1" type="text" style="width:90px" readonly /> 
                        </td>
                        <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:120px" readonly />
                            <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                        </td>
                        <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:90px" readonly /></td>
                        <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$break_item[$i], "",1,"" ); ?></td>
                        <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes" style="width:80px" readonly/></td>
                        <td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes" style="width:70px" readonly/></td>
                        <td><? echo create_drop_down( "cbouom_1",50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
                         <td><input name="txtreceiveqty_1" id="txtreceiveqty_1" class="text_boxes_numeric" type="text" style="width:70px" readonly /></td>
                        
                        <td><input name="txtreceivebalance_1" id="txtreceivebalance_1" class="text_boxes_numeric" type="text" style="width:70px" disabled/></td> 
                        <td><input name="txtreceivereturnqty_1" id="txtreceivereturnqty_1" class="text_boxes_numeric" type="text" onKeyUp="check_receive_qty_ability(this.value,1);fnc_total_calculate();" style="width:70px" /></td>
                        <td><input name="txtprereturnqty_1" id="txtprereturnqty_1" class="text_boxes_numeric" type="text" disabled/></td>
                        <td><input name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" type="text" /></td>
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
                         <td><input name="txttotreceivereturnqty" id="txttotreceivereturnqty" class="text_boxes_numeric" type="text" style="width:70px" placeholder="Display" readonly /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
             </table>
             <table width="900" cellpadding="0" border="0">
                 <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_receive_return", 0,1,"reset_form('WashmaterialreceiveReturn_1','receive_list_view','','cbouom_1,1', '$(\'#details_tbl tbody tr:not(:first)\').remove(); disable_enable_fields(\'cbo_company_name*cbo_within_group*cbo_party_name*cboGmtsItem_1*txtcolor_1\',0)')",1); ?>
                    </td>
                 </tr>  
          </table>
          </fieldset>
          <div id="receive_list_view"></div>
        </form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

