<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Wash Material Issue Return					
Functionality	:	
JS Functions	:
Created by		:	Rakib
Creation date 	: 	13-09-2020
Updated by 		: 		
Update date		:
Oracle Convert 	:		
Convert date	: 	
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start(); 
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Wash Material Issue Info", "../../", 1,1, $unicode,'','','');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	function openmypage_issueReturn_id()
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var page_link='requires/wash_material_issue_return_controller.php?action=issue_reurn_popup&data='+data;
		var title="Issue ID";
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job").value;
			var splt_val=theemail.split("_");
			if (splt_val[2] != '')
			{
				freeze_window(5);
				reset_form('','','txt_issue_return_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_return_challan*txt_issue_return_date*cbo_within_group*txt_job_no*update_id','','');
				$("#issue_id").val(splt_val[0]);
				$("#txt_job_no").val(splt_val[1]);
				$("#txt_issue_no").val(splt_val[3]);
				$("#txt_issue_return_no").val(splt_val[4]);
				$("#txt_buyer_name").val(splt_val[5]);
				get_php_form_data( splt_val[2], "load_php_data_to_form", "requires/wash_material_issue_return_controller" );
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+splt_val[2], 'load_php_dtls_form', '', 'requires/wash_material_issue_return_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_return_table tr").remove();
					$("#rec_issue_return_table").append(list_view_orders);
				}
				fnc_total_calculate();
				var within_group=document.getElementById('cbo_within_group').value*1;
				fnc_load_party(within_group);
				set_button_status(1, permission, 'fnc_material_issue_return', 1);
				$('#txt_issue_no').attr('disabled','disabled');
				$('#cbo_company_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}

	function openmypage_issue_popup()
	{ 
		if ( form_validation('cbo_company_name*cbo_within_group*cbo_party_name','Company Name*within group*Party Name')==false )
		{
			return;
		}

		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		var page_link='requires/wash_material_issue_return_controller.php?action=issue_popup&data='+data;
		var title="Issue ID Popup";	
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_job").value;
			var splt_val=theemail.split('_');
			$("#issue_id").val(splt_val[0]);
			$("#txt_job_no").val(splt_val[1]);
			$("#txt_issue_no").val(splt_val[2]);
			$("#txt_buyer_name").val(splt_val[3]);
			var update_id=document.getElementById('update_id').value;

			if (splt_val[0] != '' )
			{
				freeze_window(5);
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1+'**'+splt_val[2]+'**'+update_id, 'load_php_dtls_form', '', 'requires/wash_material_issue_return_controller');
  				if(list_view_orders!='')
				{
					$("#rec_issue_return_table tr").remove();
					$("#rec_issue_return_table").append(list_view_orders);
				}
				fnc_total_calculate();
				var within_group=document.getElementById('cbo_within_group').value*1;
				fnc_load_party(within_group);
				release_freezing();
				set_all_onclick();
			}
		}
	}

	function fnc_material_issue_return( operation )
	{
		
		
		
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report($('#cbo_company_name').val()+'*'+$('#txt_issue_return_no').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#issue_id').val()+'*'+$('#txt_job_no').val()+'*'+$('#cbo_location_name').val()+'*'+$('#cbo_party_name').val()+'*'+$('#txt_issue_return_date').val()+'*'+$('#cbo_within_group').val()+'*'+$('#txt_issue_no').val()+'*'+$('#txt_remarks').val()+'*'+$('#txt_issue_return_challan').val()+'*'+$('#txt_buyer_name').val(), 'receive_return_in_wash_print', 'requires/wash_material_issue_return_controller');
			return;
		}
		
		if ( form_validation('cbo_company_name*cbo_location_name*cbo_party_name*txt_issue_return_date*txt_job_no*cbo_within_group', 'Company Name*Location Name*Party*Issue Return Date*Job No*within group')==false )
		{
			return;
		}

		var data_str='';
		var data_str=get_submitted_data_string('txt_issue_return_no*cbo_company_name*cbo_location_name*cbo_floor_name*cbo_party_name*txt_issue_return_challan*txt_issue_return_date*cbo_within_group*txt_issue_no*issue_id*txt_remarks*update_id*txt_job_no*txt_job_id*txt_order_id*txt_buyer_name',"../../");

		var k=0;
		var tot_row=$('#rec_issue_return_table tr').length;
		for (var i=1; i<=tot_row; i++)
		{
			var qty=$('#txtissuereturnqty_'+i).val();
			if(qty*1>0)
			{
				k++;
				data_str+="&ordernoid_" + k + "='" + $('#ordernoid_'+i).val()+"'"+"&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&cbouom_" + k + "='" + $('#cbouom_'+i).val()+"'"+"&txtissueqty_" + k + "='" + $('#txtissueqty_'+i).val()+"'"+"&txtissuereturnqty_" + k + "='" + $('#txtissuereturnqty_'+i).val()+"'"+"&txtpreissuereturnqty_" + k + "='" + $('#txtpreissuereturnqty_'+i).val()+"'"+"&jobid_" + k + "='" + $('#jobid_'+i).val()+"'"+"&issueid_" + k + "='" + $('#issueid_'+i).val()+"'"+"&receiveNo_" + k + "='" + $('#receiveNo_'+i).val()+"'"+"&issuedtlsid_" + k + "='" + $('#issuedtlsid_'+i).val()+"'"+"&updatedtlsid_" + k + "='" + $('#updatedtlsid_'+i).val()+"'";
			}
		}

		var data="action=save_update_delete&operation="+operation+'&tot_row='+k+data_str;
		//alert (data); return;
		freeze_window(operation);
		http.open("POST","requires/wash_material_issue_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_material_issue_return_response;
	}
	
	function fnc_material_issue_return_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			//if (response[0].length>3) reponse[0]=10;	
			if(trim(response[0])=='washProduction'){
				alert("Production Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
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
			/*if(trim(response[0])=='washreturn')
			{
				alert("Wash Issue Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
			
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_issue_return_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				//alert(response[3]);
				var list_view_orders = return_global_ajax_value( response[4]+'**'+response[3]+'**'+2+'**'+response[2], 'load_php_dtls_form', '', 'requires/wash_material_issue_return_controller');
				
				
				
				if(list_view_orders!='')
				{
					$("#rec_issue_return_table tr").remove();
					$("#rec_issue_return_table").append(list_view_orders);
				}
				fnc_total_calculate();
				
				set_button_status(1, permission, 'fnc_material_issue_return', 1);
			}
			if(response[0]==2)
			{
				reset_fnc();
			}
			$('#txt_issue_no').attr('disabled','disabled');
			$('#cbo_company_name').attr('disabled','disabled');
			release_freezing();
		}
	}
	
	function reset_fnc()
	{
		location.reload(); 
	}
	
	function check_issue_qty_ability(value,i)
	{
		var issue_qty = $("#txtissueqty_"+i).val()*1;
		var issue_return_qty = $("#txtissuereturnqty_"+i).attr('placeholder')*1;
		var pre_issue_return_qty = $("#txtpreissuereturnqty_"+i).val()*1;
		//var pre_issue_return_qty = $("#hiddentxtpreissuereturnqty_"+i).val()*1;
		if(((value*1)+pre_issue_return_qty)>issue_qty)
		{
			alert("Issue Return qty Excceded by Issue qty.");
			$("#txtissuereturnqty_"+i).val('');
			return;
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
		load_drop_down( 'requires/wash_material_issue_return_controller', document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_floor', 'floor_td');	
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#rec_issue_return_table tr').length;
		math_operation("txttotissuereturn_qty", "txtissuereturnqty_", "+", rowCount );
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

</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
        <form name="materialissuereturn_1" id="materialissuereturn_1" autocomplete="off">  
            <fieldset style="width:800px;">
    		<legend>Wash Material Issue</legend>
            <table  width="800" cellspacing="2" cellpadding="0" border="0">
            	<tr>
                    <td colspan="3" align="right"><strong>Issue Return ID</strong></td>
                    <td colspan="3" align="left">
                    	<input class="text_boxes" type="text" name="txt_issue_return_no" id="txt_issue_return_no" onDblClick="openmypage_issueReturn_id();"  placeholder="Double Click" style="width:137px;" readonly/><input type="hidden" name="update_id" id="update_id">
                    </td>
                </tr>
                <tr>
                    <td width="110" class="must_entry_caption">Company Name </td>
                    <td width="150"> 
                        <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_material_issue_return_controller', this.value, 'load_drop_down_location', 'location_td' ); location_select(); load_drop_down( 'requires/wash_material_issue_return_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' ); ");//load_drop_down( 'requires/wash_material_issue_return_controller', this.value, 'load_drop_down_issueto', 'issue_to_td' ); force_pro_source(); ?>
                    </td>
                    <td width="110" class="must_entry_caption">Location Name</td>
                    <td id="location_td">
                        <? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                    <td width="110" align="right">Floor/Unit</td>
					<td width="160" id="floor_td"><? echo create_drop_down( "cbo_floor_name", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "" ); ?></td>
                    
        		</tr>
                <tr>
                	<td width="110" class="must_entry_caption">Within Group</td>
					<td>
						<? echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 1, "-- Select --", 0, "load_drop_down( 'requires/wash_material_issue_return_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );fnc_load_party(this.value);" ); ?>
					</td>
                    <td class="must_entry_caption">Party</td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                    </td>
                    <td>Issue Return Challan</td>
                    <td>
                        <input class="text_boxes" type="text" name="txt_issue_return_challan" id="txt_issue_return_challan" style="width:137px;" />  
                    </td>
                    
                </tr>
                <tr style="display:none">
	                <td>Prod Source</td>
	                <td>
	                     <?
	                       echo create_drop_down( "cbo_source", 150, $knitting_source,"", 1, "-- Select Source --", 1, "load_drop_down( 'requires/wash_material_issue_return_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_company_supplier', 'issue_to_td' );",0,'1,3' );
	                     ?> 
	                </td>
	                <td class="must_entry_caption">Issue To</td>
	                <td id="issue_to_td">
	                     <?
	                        echo create_drop_down( "cbo_company_supplier", 150, $blank_array,"", 1, "-- Select Company --", $selected, "",0 );	
	                     ?> 
	                </td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	            </tr>
                <tr> 
                	<td class="must_entry_caption">Issue Return Date</td>
                    <td>
                        <input type="text" name="txt_issue_return_date" id="txt_issue_return_date" class="datepicker" value="<? echo date("d-m-Y")?>" style="width:137px" />             
                    </td>               	
	                <td class="must_entry_caption"><strong>Issue ID</strong></td>
	                <td>
	                	<input class="text_boxes"  type="text" name="txt_issue_no" id="txt_issue_no" onDblClick="openmypage_issue_popup();" placeholder="Double Click" style="width:137px;" readonly/>	
	                	<input type="hidden" name="issue_id" id="issue_id">           
	                </td>
	                <td>Remarks</td>
                    <td >
                    	<input class="text_boxes"  type="text" name="txt_remarks" id="txt_remarks" placeholder="Write" style="width:150px;"/>
                    </td>
	            </tr>                    
            </table>
        </fieldset>
        <br/>
        <fieldset style="width:950px;">
        <legend>Metarial Details Entry</legend>
            <table cellpadding="0" cellspacing="2" border="1" width="950" id="details_tbl" rules="all">
                <thead class="form_table_header">
                	<tr>
                		<th class="must_entry_caption">Job No</th>
                        <th class="must_entry_caption" align="left">
                			<input type="text" name="txt_job_no" id="txt_job_no" value="" class="text_boxes"  style="width:120px" readonly/>
                            <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:120px;" />
                            <input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:120px;" />
                        </th>
                        <th align="center">Buyer</th>
                        <th colspan="7" align="left"><input type="text" name="txt_buyer_name" id="txt_buyer_name" value="" class="text_boxes"  style="width:120px" placeholder="" readonly/></th>                       
            	    </tr>
                    <tr><th colspan="10">&nbsp;</th></tr>
                    <tr align="center" >
                        <th width="120" class="must_entry_caption">Order No</th>
                        <th width="120" id="buyerpo_td">Buyer PO</th>
                        <th width="120" id="buyerstyle_td">Style Ref.</th>
                        <th width="90" class="must_entry_caption">Garments Item</th>
                        <th width="90">Color</th>
                        <th width="90">Size</th>
                        <th class="must_entry_caption">UOM</th>
                        <th width="80">Issue Qty</th>
                        <th width="80" class="must_entry_caption">Issue Return Qty</th>
                        <th width="80">Cumulative Return Qty</th>                        
                    </tr>
                </thead>
             	<tbody id="rec_issue_return_table">
                    <tr>
                        <td>
                        	<input type="hidden" name="issueid_1" id="issueid_1">
                            <input type="hidden" name="receiveNo_1" id="receiveNo_1">
                            <input type="hidden" name="issuedtlsid_1" id="issuedtlsid_1">
                            <input type="hidden" name="ordernoid_1" id="ordernoid_1">
                            <input type="hidden" name="jobno_1" id="jobno_1">
                            <input type="hidden" name="jobid_1" id="jobid_1">
                            <input type="hidden" name="updatedtlsid_1" id="updatedtlsid_1">
                            <input class="text_boxes" name="txtorderno_1" id="txtorderno_1" type="text" style="width:120px" readonly />
                        </td>
                        <td>
                        	<input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:120px" readonly />
                            <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:120px" />
                        </td>
                        <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:120px" readonly /></td>
                        <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$break_item[$i], "","","" ); ?></td>
                        <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes" style="width:90px" readonly/></td>
                        <td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes" style="width:90px" readonly/></td>
                        <td><? echo create_drop_down( "cbouom_1",60, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" ); ?></td>
                        <td><input type="text" id="txtissueqty_1" name="txtissueqty_1" class="text_boxes txt_size" style="width:80px" readonly/></td>
                        <td><input name="txtissuereturnqty_1" id="txtissuereturnqty_1" class="text_boxes_numeric" type="text" onKeyUp="check_issue_qty_ability(this.value,1); fnc_total_calculate();" style="width:80px" /></td>                        
                        <td><input type="text" id="txtpreissuereturnqty_1" name="txtpreissuereturnqty_1" class="text_boxes txt_size" style="width:80px" readonly/></td>
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
                        <td>Total:</td>
                        <td><input name="txttotissuereturn_qty" id="txttotissuereturn_qty" class="text_boxes_numeric" type="text" style="width:80px" placeholder="Display" readonly /></td>
                        <td>&nbsp;</td>                       
                    </tr>
                </tfoot>
             </table>
         
             <table width="950" cellspacing="2" cellpadding="0" border="0">
                 <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_issue_return", 0,1,"reset_form('materialissuereturn_1','issue_list_view','','cbouom_1,1', '$(\'#details_tbl tbody tr:not(:first)\').remove(); disable_enable_fields(\'cbo_company_name*cbo_within_group*cbo_party_name*cboGmtsItem_1*txtcolor_1\',0)')",1); ?>
                        
                    </td>
                 </tr>  
          	</table>
            </fieldset>
           <div id="issue_list_view"></div>
        </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>