<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Embellishment Material Receive					
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	15-09-2018
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
echo load_html_head_contents("Embellishment Material Receive Info", "../../", 1,1, $unicode,1,'');

?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';

	var str_color = [<? echo substr(return_library_autocomplete( "select color_name from lib_color group by color_name", "color_name" ), 0, -1); ?>];

	$(document).ready(function(e)
	 {
            $("#txt_color").autocomplete({
			 source: str_color
		  });

            $("#cbo_gmts_material_description").hide();
     });
 
	var str_material_description = [<? echo substr(return_library_autocomplete( "select material_description from sub_material_dtls group by material_description ", "material_description" ), 0, -1); ?> ];

	function set_auto_complete(type)
	{
		if(type=='subcon_material_receive')
		{
			$("#txt_material_description").autocomplete({
			source: str_material_description
			});
		}
	}

	var str_size = [<? echo substr(return_library_autocomplete( "select size_name from lib_size group by size_name ", "size_name" ), 0, -1); ?> ];

	function set_auto_complete_size(type)
	{
		if(type=='size_return')
		{
			$(".txt_size").autocomplete({
			source: str_size
			});
		}
	}

	function openmypage_rec_id()
	{ 
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		//alert (data);
		var page_link='requires/emb_material_receive_controller.php?action=receive_popup&data='+data;
		var title="Order Id Popup";	
		
		emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
			//var theemail=this.contentDoc.getElementById("selected_job");
			var theemail=this.contentDoc.getElementById("selected_job").value;
			//alert (theemail); 
			var splt_val=theemail.split("_");
			if (splt_val[0]!="")
			{
				freeze_window(5);
				reset_form('','','txt_receive_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_receive_challan*txt_receive_date*cbo_within_group*txt_job_no*update_id','','');
				get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/emb_material_receive_controller" );
				
				var list_view_orders = return_global_ajax_value( splt_val[0]+'**'+splt_val[1]+'**'+1, 'load_php_dtls_form', '', 'requires/emb_material_receive_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				set_button_status(1, permission, 'fnc_material_receive',1);
				release_freezing();
			}
		}
	}

	function job_search_popup(page_link,title)
	{
		if ( form_validation('cbo_company_name*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		else
		{
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
			page_link='requires/emb_material_receive_controller.php?action=job_popup&data='+data
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				 freeze_window(5);
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_order").value;
				$("#txt_job_no").val( theemail );
				
				var list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1, 'load_php_dtls_form', '', 'requires/emb_material_receive_controller');
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				release_freezing();
			}
		}
	}

	function fnc_material_receive( operation )
	{
		if ( form_validation('cbo_company_name*cbo_party_name*txt_receive_challan*txt_receive_date*txt_job_no', 'Company Name*Party*Receive Challan*Receive Date*Job No')==false )
		{
			return;
		}
		else
		{
			/*var zero_val="0";
			if(operation==2)
			{
				var r=confirm('Press \"OK\" to delete all items of this challan.\nPress \"Cancel\" Do Not Delete.');
				if(r==true) 
				{ 
					zero_val="1";
				}
				else
				{
					zero_val="0";
					return;
				}
			}*/
			var data_str="";
			
			var data_str=get_submitted_data_string('txt_receive_no*cbo_company_name*cbo_location_name*cbo_party_name*txt_receive_challan*txt_receive_date*cbo_within_group*txt_job_no*update_id*hid_challan_id',"../../");
			var tot_row=$('#rec_issue_table tr').length;
			 var k=0;
			 
			for (var i=1; i<=tot_row; i++)
			{
				var qty=$('#txtreceiveqty_'+i).val();
				if(qty*1>0)
				{
					k++;
					data_str+="&ordernoid_" + k + "='" + $('#ordernoid_'+i).val()+"'"+"&breakdownid_" + k + "='" + $('#breakdownid_'+i).val()+"'"+"&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&cboProcessName_" + k + "='" + $('#cboProcessName_'+i).val()+"'"+"&cbouom_" + k + "='" + $('#cbouom_'+i).val()+"'"+"&txtreceiveqty_" + k + "='" + $('#txtreceiveqty_'+i).val()+"'"+"&txtremarks_" + k + "='" + $('#txtremarks_'+i).val()+"'"+"&updatedtlsid_" + k + "='" + $('#updatedtlsid_'+i).val()+"'";
				}
			}
			var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/emb_material_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_material_receive_response;
		}
	}
	
	function fnc_material_receive_response()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);//return;
			var response=trim(http.responseText).split('**');
			if (response[0].length>3) response[0]=10;	
			
			if(trim(response[0])=='emblIssue'){
				alert("Issue Found :"+trim(response[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_receive_no').value= response[1];
				document.getElementById('update_id').value = response[2];
				
				set_button_status(1, permission, 'fnc_material_receive',1);

				let cbo_within_group= $('#cbo_within_group').val(); 
				let cbo_company_name=$('#cbo_company_name').val(); 
				let bundle_variable=$('#bundle_variable').val(); 
				let clln_id=$('#hid_challan_id').val();  
				
				let list_view_orders = return_global_ajax_value(  response[2]+'**'+response[3]+'**'+2+'**'+cbo_within_group+'**'+bundle_variable+'**'+cbo_company_name+'**'+clln_id, 'load_php_dtls_form', '', 'requires/emb_material_receive_controller');

			/* 	var list_view_orders = return_global_ajax_value( response[2]+'**'+response[3]+'**'+2, 'load_php_dtls_form', '', 'requires/emb_material_receive_controller'); */
				if(list_view_orders!='')
				{
					$("#rec_issue_table tr").remove();
					$("#rec_issue_table").append(list_view_orders);
				}
				fnc_total_calculate();
			}
			if(response[0]==2)
			{
				location.reload(); 
			}
			release_freezing();
		}
	}
	
	function check_receive_qty_ability(value,i)
	{
		var placeholder_qty = $("#txtreceiveqty_"+i).attr('placeholder')*1;
		var pre_rec_qty = $("#txtreceiveqty_"+i).attr('pre_rec_qty')*1;
		var order_qty = $("#txtreceiveqty_"+i).attr('order_qty')*1;
		
		//alert(value+'_'+pre_rec_qty+'_'+order_qty);
		if(((value*1)+pre_rec_qty)>order_qty)
		{
			//alert("Qnty Excceded");
			var confirm_value=confirm("Receive qty Excceded by Order qty. Press cancel to proceed otherwise press ok. ");
			if(confirm_value!=0)
			{
				$("#txtreceiveqty_"+i).val('');
			}			
			return;
		}
	}
	//var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder');
	
	function search_by(val)
	{
		$('#txt_search_string').val('');
		if(val==1 || val==0) $('#search_by_td').html('Embl. Job No');
		else if(val==2) $('#search_by_td').html('W/O No');
		else if(val==3) $('#search_by_td').html('Buyer Job');
		else if(val==4) $('#search_by_td').html('Buyer Po');
		else if(val==5) $('#search_by_td').html('Buyer Style');
	}
	
	function location_select()
	{
		if($('#cbo_location_name option').length==2)
		{
			if($('#cbo_location_name option:first').val()==0)
			{
				$('#cbo_location_name').val($('#cbo_location_name option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
		else if($('#cbo_location_name option').length==1)
		{
			$('#cbo_location_name').val($('#cbo_location_name option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}	
	}
	
	function fnc_total_calculate()
	{
		var rowCount = $('#rec_issue_table tr').length;
		//alert(rowCount)
		math_operation( "txttotreceiveqty", "txtreceiveqty_", "+", rowCount );
	} 
	
	function openmypage_remarks(id)
	{
		var data=document.getElementById('txtremarks_'+id).value;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/emb_material_receive_controller.php?data='+data+'&action=remarks_popup','Remarks Popup', 'width=420px,height=320px,center=1,resize=1,scrolling=0','../')
		
		emailwindow.onclose=function()
		{
			var theemail=this.contentDoc.getElementById("text_new_remarks");//Access form field with id="emailfield"
			if (theemail.value!="")
			{
				$('#txtremarks_'+id).val(theemail.value);
			}
		}
	}

	function generate_report_file(data,action,page)
	{
		window.open("requires/emb_material_receive_controller.php?data=" + data+'&action='+action, true );
	}
	function report_emb_material_receive(report){

		if(report==1)
		{
				var report_title=$( "div.form_caption" ).html();
				 generate_report_file( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_receive_no').val()+'*'+$('#cbo_party_name').val()+'*'+$('#cbo_location_name').val()+'*'+$('#txt_receive_challan').val()+'*'+$('#cbo_within_group').val()+'*'+$('#txt_job_no').val()+'*'+report_title,'emb_material_print_print','requires/emb_material_receive_controller');
				
				return;			
		}

	}
	function bundle_popup_chk(company_id)
	{
		
		
		//alert(company_id); 
		var work_order_material_auto_receive = $("#work_order_material_auto_receive").val()*1;
		var within_group = $("#cbo_within_group").val()*1; 
			if(work_order_material_auto_receive==1)
			{
				$('#txt_job_no').attr('disabled','disabled');
				$('#printing_issue_challan_no').attr('disabled',false); 
				$('#printing_work_oder').css('color','blue');
				$('#is_copy').attr('disabled','true');
			}
			else
			{  
			    $('#is_copy').attr('disabled',false);
				$('#printing_issue_challan_no').attr('disabled','true');
				$('#txt_job_no').attr('disabled',false); 
				$('#printing_work_oder').css('color','black');
			}
	}
	
	function printing_issue_challan_popup()
	{
		if ( form_validation('cbo_company_name*cbo_party_name','Company Name*Party Name')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
		page_link='requires/emb_material_receive_controller.php?action=printing_to_issue_challan_popup&data='+ data;
		var title="Job No Popup Info";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=420px,center=1,resize=0,scrolling=0','../')  
		emailwindow.onclose=function()
		{
			freeze_window(5);
			var theform=this.contentDoc.forms[0];
			var theemail=this.contentDoc.getElementById("selected_order").value;
			var challan_id=this.contentDoc.getElementById("hidden_challan_id").value;
			var challan_no=this.contentDoc.getElementById("hidden_challan_no").value;
			$("#printing_issue_challan_no").val( challan_no );
			$("#hid_challan_id").val( challan_id );
			
			
			$("#txt_job_no").val( theemail );
			let cbo_within_group= $('#cbo_within_group').val(); 
			let cbo_company_name=$('#cbo_company_name').val(); 
			let bundle_variable=$('#bundle_variable').val(); 
			let clln_id=$('#hid_challan_id').val();  

			// var list_view_orders = return_global_ajax_value( "5707**OG-EOE-23-00004**1", 'load_php_dtls_form', '', 'requires/emb_material_receive_controller');
			let list_view_orders = return_global_ajax_value( 0+'**'+theemail+'**'+1+'**'+cbo_within_group+'**'+bundle_variable+'**'+cbo_company_name+'**'+clln_id, 'load_php_dtls_form', '', 'requires/emb_material_receive_controller');
			if(list_view_orders!='')
			{
				$("#rec_issue_table tr").remove(); 
				$("#rec_issue_table").append(list_view_orders);
			}

			fnc_total_calculate();
			$('#cbo_company_name').attr('disabled','disabled');
			$('#cbo_party_name').attr('disabled','disabled');
			release_freezing();
		}
	}

</script>
</head>
<body onLoad="set_hotkey();set_auto_complete('subcon_material_receive');set_auto_complete_size('size_return');">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="materialreceive_1" id="materialreceive_1" autocomplete="off">  
        <fieldset style="width:800px;">
    	<legend>Embellishment Material Receive</legend>
            <table  width="800" cellspacing="2" cellpadding="0" border="0">
            	 <tr>
                    <td colspan="3" align="right">Receive ID</td>
                    <td colspan="3" align="left">
                    	<input class="text_boxes"  type="text" name="txt_receive_no" id="txt_receive_no" onDblClick="openmypage_rec_id('xx','Subcontract Receive')"  placeholder="Double Click" style="width:160px;" readonly/><input type="hidden" name="update_id" id="update_id">
						<input type="hidden" id="work_order_material_auto_receive" />
						<input type="hidden" name="bundle_variable" id="bundle_variable" value="0">
                    </td>
                </tr>
                <tr>
                    <td width="110" align="right" class="must_entry_caption">Company Name </td>
                    <td width="150"> 
                        <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/emb_material_receive_controller', this.value, 'load_drop_down_location', 'location_td' ); location_select(); load_drop_down( 'requires/emb_material_receive_controller', this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'load_variable_settings','requires/emb_material_receive_controller');bundle_popup_chk(this.value);"); ?>
                    </td>
                    <td width="110" align="right">Location Name</td>
                    <td id="location_td">
                         <? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" );?>
                    </td>
                    <td width="110" align="right">Within Group</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "load_drop_down( 'requires/emb_material_receive_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
					</td>
        		</tr>
                <tr>
                    <td align="right" class="must_entry_caption">Party</td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "-- Select Party --", $selected, "" );?>
                    </td>
                    <td class="must_entry_caption" align="right">Receive Challan</td>
                    <td>
                        <input class="text_boxes"  type="text" name="txt_receive_challan" id="txt_receive_challan" style="width:140px;" />  
                    </td>
                    <td class="must_entry_caption" align="right">Receive Date</td>
                    <td>
                        <input type="text" name="txt_receive_date" id="txt_receive_date"  class="datepicker" style="width:140px" value="<? echo date("d-m-Y")?>" />             
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption" align="right">Job No</td>
                    <td align="left">
                    	<input class="text_boxes"  type="text" name="txt_job_no" id="txt_job_no" onDblClick="job_search_popup();" placeholder="Double Click" style="width:160px;" readonly/>
                    </td>
					<td align="right" id="printing_work_oder" >Delivery challan</td>
                    <td   align="left">
                    	<input class="text_boxes"  type="text" name="printing_issue_challan_no" id="printing_issue_challan_no" onDblClick="printing_issue_challan_popup();" placeholder="Double Click" style="width:140px;" disabled/> 
                         <input type="hidden" name="hid_challan_id" id="hid_challan_id">  
                    </td>
                </tr>
            </table>
      	</fieldset>
            <br/>
            <fieldset style="width:1180px;">
            <legend>Metarial Details Entry</legend>
            <table cellpadding="0" cellspacing="2" border="1" width="1180" id="details_tbl" rules="all">
                <thead class="form_table_header">
                    <tr align="center" >
                        <th width="100" class="must_entry_caption">Order No</th>
                        <th width="100" class="must_entry_caption">Buyer Po</th>
                        <th width="100">Style Ref.</th>
                        <th width="80" class="must_entry_caption">Embl. Name</th>
                        <th width="80" class="must_entry_caption">Embl. Type</th>
                        <th width="90" class="must_entry_caption">Garments Item</th>
                        <th width="90" class="must_entry_caption">Body Part</th>
                        <th width="120" class="must_entry_caption">Material Description</th>
                        <th width="60">Color</th>
                        <th width="60">GMTS Size</th>
                        <th width="60">Order Qty (Pcs)</th>
                        <th width="60">Prev. Rec. Qty</th>
                        <th width="60" class="must_entry_caption">Receive Qty</th>
                        <th width="50" class="must_entry_caption">UOM</th>
                        <th>RMK</th>
                    </tr>
                </thead>
             	<tbody id="rec_issue_table">
                    <tr>
                        <td><input type="hidden" name="ordernoid_1" id="ordernoid_1">
                            <input type="hidden" name="jobno_1" id="jobno_1">
                            <input type="hidden" name="updatedtlsid_1" id="updatedtlsid_1">
                            <input type="hidden" name="breakdownid_1" id="breakdownid_1">
                            <input class="text_boxes" name="txtorderno_1" id="txtorderno_1" type="text" style="width:90px" readonly /> 
                        </td>
                        <td><input name="txtbuyerPo_1" id="txtbuyerPo_1" type="text" class="text_boxes" style="width:90px" readonly />
                            <input name="txtbuyerPoId_1" id="txtbuyerPoId_1" type="hidden" class="text_boxes" style="width:70px" />
                        </td>
                        <td><input name="txtstyleRef_1" id="txtstyleRef_1" type="text" class="text_boxes" style="width:90px" readonly /></td>
                        <td><? echo create_drop_down( "cboProcessName_1", 80, $emblishment_name_array,"", 1, "--Select--",0,"cbotype_loder(1);", "","" ); ?></td>
                        <td id="reType_1"><? echo create_drop_down( "cboReType_1", 80, $blank_array,"", 1, "Select Item", 0, ""); ?></td>
                        <td><? echo create_drop_down( "cboGmtsItem_1", 90, $garments_item,"", 1, "-- Select --",$break_item[$i], "","","" ); ?></td>
                        <td><? echo create_drop_down( "cboBodyPart_1", 90, $body_part,"", 1, "-- Select --",$break_body_part[$i], "","","" ); ?></td>
                        <td>
                            <input type="text" id="txtmaterialdescription_1" name="txtmaterialdescription_1" class="text_boxes" style="width:110px" title="Maximum 200 Character" >
                        </td>
                        <td><input type="text" id="txtcolor_1" name="txtcolor_1" class="text_boxes" style="width:50px" readonly/></td>
                        <td><input type="text" id="txtsize_1" name="txtsize_1" class="text_boxes txt_size" style="width:50px" readonly/></td>
                        <td><input name="txtordqty_1" id="txtordqty_1" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
                        <td><input name="txtpreqty_1" id="txtpreqty_1" class="text_boxes_numeric" type="text" style="width:50px" disabled/></td>
                        <td><input name="txtreceiveqty_1" id="txtreceiveqty_1" class="text_boxes_numeric" type="text" onKeyUp="check_receive_qty_ability(this.value,1); fnc_total_calculate();" style="width:50px" /></td>
                        <td><? echo create_drop_down( "cbouom_1",50, $unit_of_measurement,"", 1, "-Select-",1,"", 1,"" );?></td>
                        <td><input type="text" name="txtremarks_1" id="txtremarks_1" style="width:40px" class="text_boxes" placeholder="Remark" onClick="openmypage_remarks(1);" /></td>
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
                        <td>Total:</td>
                        <td><input name="txttotreceiveqty" id="txttotreceiveqty" class="text_boxes_numeric" type="text" style="width:50px" placeholder="Display" readonly /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
             </table>
             <table width="1020" cellpadding="0" border="0">
                 <tr>
                    <td align="center" colspan="10" valign="middle" class="button_container">
                        <? echo load_submit_buttons($permission, "fnc_material_receive", 0,0,"reset_form('materialreceive_1','receive_list_view','','cbouom_1,1', '$(\'#details_tbl tbody tr:not(:first)\').remove(); disable_enable_fields(\'cbo_company_name*cbo_within_group*cbo_party_name*cboProcessName_1*cboReType_1*cboGmtsItem_1*cboBodyPart_1*txtmaterialdescription_1*txtcolor_1*txtsize_1\',0)')",1); ?>
                    </td>
                 </tr>  
				 <tr>
					<td align="center">
				       <input type="button"  id="show_button" class="formbutton" style="width:100px; text-align:center;" value="Print"  name="print"  onClick="report_emb_material_receive(1)" />
					</td>
				 </tr>
          </table>
          </fieldset>
    
        </form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>