<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Embroidery Delivery
Functionality	:	
JS Functions	:
Created by		:	Feroz
Creation date 	: 	26-06-2019
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

echo load_html_head_contents("Embroidery Delivery","../../", 1, 1, $unicode,'','');
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
		var deli_party_name = $('#cbo_deli_party_name').val();
		
		if(within_group==1 && type==1)
		{
			$('#cbo_deli_party_name').val(0);
			$('#cbo_deli_party_name').attr('disabled',false);
			load_drop_down( 'requires/emb_delivery_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			$('#td_party_location').css('color','blue');
			$('#td_deli_party').css('color','blue');
			$('#td_dparty_location').css('color','blue');
		}
		else if(within_group==2  && type==1)
		{
			$('#cbo_deli_party_name').val(0);
			$('#cbo_deli_party_name').attr('disabled',true);
			load_drop_down( 'requires/emb_delivery_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			$('#td_party_location').css('color','black');
			$('#td_deli_party').css('color','black');
			$('#td_dparty_location').css('color','black');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/emb_delivery_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#td_deli_party').css('color','blue');
			$('#td_dparty_location').css('color','blue');
		}
		else if(within_group==1 && type==3)
		{
			load_drop_down( 'requires/emb_delivery_controller', deli_party_name+'_'+3, 'load_drop_down_location', 'dparty_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#td_deli_party').css('color','blue');
			$('#td_dparty_location').css('color','blue');
		}
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
			var page_link='requires/emb_delivery_controller.php?action=job_popup&data='+data
			
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
				var company = $('#cbo_company_name').val();
				var list_view_orders = return_global_ajax_value( 0+'**'+ex_data[0]+'**'+1+'**'+company, 'load_php_dtls_form', '', 'requires/emb_delivery_controller');
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
				}
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_within_group').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				
				setFilterGrid("dtls_tbody",-1);
				fnc_total_calculate();
				release_freezing();
			}
		}
	}
	
	function fnc_embl_delivery( operation )
	{
		var within_group=$('#cbo_within_group').val();
		if(within_group==1)
		{
			if ( form_validation('cbo_company_name*cbo_party_name*cbo_deli_party_name*cbo_deli_party_location*txt_delivery_date*txtJob_no', 'Company Name*Party*Delivery Party*Del.Party Location*Delivery Date*Job No')==false )
			{
				return;
			}
		}
		else if(within_group==2)
		{
			if ( form_validation('cbo_company_name*cbo_party_name*txt_delivery_date*txtJob_no', 'Company Name*Party*Delivery Date*Job No')==false )
			{
				return;
			}
		}
			
			var data_str="";
			var data_str=get_submitted_data_string('txt_delv_no*cbo_company_name*cbo_location_name*cbo_party_name*cbo_party_location*cbo_deli_party_name*cbo_deli_party_location*txt_delivery_date*cbo_within_group*txt_remarks*txt_challan_no*txtJob_no*txt_update_id*cboshipingStatus',"../../");
			var tot_row=$('#dtls_tbody tr').length;
			//alert(tot_row); release_freezing(); return;
			var k=0;
			//alert(data_str);
			for (var i=1; i<=tot_row; i++)
			{
				var qty=$('#txtCurrDelv_'+i).val();
				if(qty*1>0)
				{
					k++;
					data_str+="&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&txtCurrDelv_" + k + "='" + $('#txtCurrDelv_'+i).val()+"'"+"&txtRemarks_" + k + "='" + $('#txtRemarks_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtsort_" + k + "='" + $('#txtsort_'+i).val()+"'"+"&txtreject_" + k + "='" + $('#txtreject_'+i).val()+"'"+"&txtDtlsUpdateId_" + k + "='" + $('#txtDtlsUpdateId_'+i).val()+"'"+"&txtcuttingNo_" + k + "='" + $('#txtcuttingNo_'+i).val()+"'";
				}
			}
			if(k==0)
			{
				alert("Please input Current Delv. Qty. (Pcs).");
				return;
			}
			var data="action=save_update_delete&operation="+operation+'&total_row='+k+data_str;//+'&zero_val='+zero_val
			//alert (data); return;
			freeze_window(operation);
			http.open("POST","requires/emb_delivery_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_embl_delivery_response;
		
	}
	
	function fnc_embl_delivery_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			//$('#cbo_uom').val(12);
			if(response[0]==0 || response[0]==1)
			{
				document.getElementById('txt_update_id').value= response[1];
				document.getElementById('txt_delv_no').value = response[2];
				var company = $('#cbo_company_name').val();
				var list_view_orders = return_global_ajax_value( response[1]+'**'+$('#txtJob_no').val()+'**'+2+'**'+company, 'load_php_dtls_form', '', 'requires/emb_delivery_controller');
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
					setFilterGrid("dtls_tbody",-1);
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embl_delivery',1);
				}
			}
			if(response[0]==2)
			{
				location.reload(); 
			}
			release_freezing();
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
			var page_link='requires/emb_delivery_controller.php?action=delivery_popup&data='+data;
			var title="Delivery Popup";	
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=900px, height=400px, center=1, resize=0, scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_subcontract_frm"); //Access the form inside the modal window
				//var theemail=this.contentDoc.getElementById("selected_job");
				var theemail=this.contentDoc.getElementById("selected_job").value;
				//alert (theemail); 
				
				var emb_data=theemail.split("***");
				if (emb_data[0]!="")
				{
					freeze_window(5);
					reset_form('','','txt_delv_no*cbo_location_name*cbo_party_name*cbo_within_group*txt_delivery_date*txtJob_no*txt_update_id','','');
					
					$('#txt_update_id').val(emb_data[0]);
					$('#txt_delv_no').val(emb_data[1]);
					$('#cbo_location_name').val(emb_data[2]);
					$('#cbo_within_group').val(emb_data[3]);
					$('#cbo_party_name').val(emb_data[4]);
					$('#txt_delivery_date').val(emb_data[5]);
					$('#txt_remarks').val(emb_data[6]);
					
					$('#txtJob_no').val(emb_data[7]);
					$('#txt_wo_no').val(emb_data[8]);
					$('#txtStyleRef').val(emb_data[9]);
					$('#txtBuyerName').val(emb_data[10]);
					$('#cboshipingStatus').val(emb_data[11]);
					$('#txt_challan_no').val(emb_data[15]);
					
					$('#cbo_deli_party_name').val(emb_data[13]);
					if(emb_data[3]==1)
					{
						fnc_load_party(2,1);
						fnc_load_party(3,1);
					}
					$('#cbo_party_location').val(emb_data[12]);
					$('#cbo_deli_party_location').val(emb_data[14]);
					
					$('#cbo_company_name').attr('disabled','disabled');
					$('#cbo_within_group').attr('disabled','disabled');
					$('#cbo_party_name').attr('disabled','disabled');
					
					//get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/emb_delivery_controller" );
					var company = $('#cbo_company_name').val();
					
					var list_view_orders = return_global_ajax_value( emb_data[0]+'**'+emb_data[7]+'**'+1+'**'+company, 'load_php_dtls_form', '', 'requires/emb_delivery_controller');
					if(list_view_orders!='')
					{
						$("#dtls_tbody tr").remove();
						$("#dtls_tbody").append(list_view_orders);
					}
					setFilterGrid("dtls_tbody",-1);
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embl_delivery',1);
					release_freezing();
				}
			}
		}
	}

	function fnc_embl_delivery_print(operation){

		if ( form_validation('cbo_company_name*txt_update_id','Company Name*Update Id')==false )
		{
			return;
		}
		
		if(operation==1){
			
			var show_item='';
			var r=confirm("if Select  \"OK\" then Print Preview Will show with (Gate Pass details)\n if Select  \"Cancel\" then Print Preview Will Not show with (Gate Pass details)");
			var show_item = (r) ? "1" : "0";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+report_title+'*'+$('#txt_delv_no').val()+'*'+show_item+'*'+$('#cbo_template_id').val(), "embl_delivery_entry_print", "requires/emb_delivery_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==2){

			var show_item='';
			var r=confirm("if Select  \"OK\" then Print Preview Will show with (Gate Pass details)\n if Select  \"Cancel\" then Print Preview Will Not show with (Gate Pass details)");
			var show_item = (r) ? "1" : "0";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+report_title+'*'+$('#txt_delv_no').val()+'*'+show_item+'*'+$('#cbo_template_id').val(), "embl_delivery_entry_print_1", "requires/emb_delivery_controller") 
			//return;
			show_msg("3");
		}
	 

	}

	
	function fnc_production_qty_ability(value,i)
	{
		var placeholder_value = $("#txtCurrDelv_"+i).attr('placeholder')*1;
		var pre_delv_qty = $("#txtCurrDelv_"+i).attr('pre_delv_qty')*1;
		var delv_qty = $("#txtCurrDelv_"+i).attr('delv_qty')*1;
		var variable_status = $("#txtCurrDelv_"+i).attr('variable_status')*1;
		
		//alert(placeholder_value);//1300
		//alert(pre_delv_qty);//0
		//alert(delv_qty);//1300
		//alert(value);//1300
		
		//alert((value*1)+pre_delv_qty);//1300
		//alert(placeholder_value);
	 		
		if(((value*1)+pre_delv_qty)>delv_qty)
		{
			//alert("Qnty Excceded");
			if(variable_status==3) // qc qty 
			{
			   //var confirm_value=confirm("Delivery qty Excceded by QC qty. Press cancel to proceed otherwise press ok.");
				alert("Delivery qty Excceded by QC qty Not Allow.");
				$("#txtCurrDelv_"+i).val('');
				 return;
			}
			else if(variable_status==2)
			{
				//var confirm_value=confirm("Delivery Qty can not Exceed Production Quantity.");
				alert("Delivery Qty can not Exceed Production Quantity."); 
				$("#txtCurrDelv_"+i).val('');
				return;
			}
			else
			{
				//var confirm_value=confirm("Delivery qty Excceded by QC qty. Press cancel to proceed otherwise press ok.");
				alert("Delivery qty Excceded by Order qty Not Allow."); 
				$("#txtCurrDelv_"+i).val('');
				return;
			}
			if(confirm_value!=0)
			{
				$("#txtCurrDelv_"+i).val('');
			}			
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
		var rowCount = $('#dtls_tbody tr').length-1;
		//var rowCount = $('#dtls_tbody tr').length;
		//alert(rowCount)
		math_operation( "txtTotCurrDelv", "txtCurrDelv_", "+", rowCount );
		math_operation( "txtTotsort", "txtsort_", "+", rowCount );
		math_operation( "txtTotreject", "txtreject_", "+", rowCount );
	}
	
	function fnc_td_change(btn_id,status)
	{
		if(status==1)//order 1 qc 3 // 2 product
		{
			//$('.receiv_id').show();  QC Pass Qty (Pcs
			document.getElementById("receiv_id").innerHTML = "Order Qty (Pcs)";
		}
		else if(status==2)//qc 3 // 2 product
		{
			//$('.receiv_id').show();  QC Pass Qty (Pcs
			document.getElementById("receiv_id").innerHTML = "Production Qty (Pcs)";
		}
		else
		{
			
			document.getElementById("receiv_id").innerHTML ="QC Pass Qty (Pcs)";
			
			//$('.receiv_id').hide();
		}
		
	}
	function chk_qty_level_variabe(company)
	{
	   var status = return_global_ajax_value(company, 'chk_qty_level_variable', '', 'requires/emb_delivery_controller').trim();
	   status = status.split("**");
	   $('#txt_variable_status').val(status[0]);
	}
</script>

</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="embdeliventry_1" id="embdeliventry_1" autocomplete="off"> 
			<fieldset style="width:1000px;">
			<legend>Embroidery Delivery</legend>
                <table width="990" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Delivery ID</strong></td>
                        <td colspan="4">
                        <input class="text_boxes"  type="text" name="txt_delv_no" id="txt_delv_no" onDblClick="openmypage_delv_no();" placeholder="Double Click" style="width:140px;" readonly />
                        <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:90px" class="text_boxes" value="" />
                        <input type="hidden" id="txt_variable_status" name="txt_variable_status" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="150"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/emb_delivery_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select(); fnc_load_party(1,document.getElementById('cbo_within_group').value);chk_qty_level_variabe(this.value);fnc_td_change(this.value,document.getElementById('txt_variable_status').value);"); ?>
                        
                        </td>
                        <td width="100">Location</td>
                        <td width="150" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="100" class="must_entry_caption">Within Group</td>
                        <td width="150"><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 0, "--  --", 0, "fnc_load_party(1,this.value);" ); ?></td>
                        <td width="100" class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/emb_delivery_controller', this.value+'_'+2, 'load_drop_down_location', 'party_location_td'); location_select(); fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                    </tr>
                    <tr>
                    	<td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>

                        <td id="td_deli_party">Delivery Party</td>
                        <td><? echo create_drop_down( "cbo_deli_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $selected, "load_drop_down( 'requires/emb_delivery_controller', this.value+'_'+3, 'load_drop_down_location', 'dparty_location_td'); location_select(); fnc_load_party(3,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td id="td_dparty_location">Del.Party Location</td>
                        <td id="dparty_location_td"><? echo create_drop_down( "cbo_deli_party_location", 150, $blank_array,"", 1, "--Select Location--", $selected, "",1 ); ?></td>

                        <td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date" style="width:140px" class="datepicker" value="<? echo date('d-m-Y'); ?>" /></td>
                    </tr>
                    <tr>
                    	<td>Challan No.</td>
                    	<td>
                        	<input class="text_boxes_numeric"  type="text" name="txt_challan_no" id="txt_challan_no"  style="width:140px;" />
                        </td>
                        <td>Remarks</td>
                        <td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:650px;" /></td>
                    </tr> 
                </table>
            </fieldset>
            <br>
            <fieldset style="width:1100px;">
            <legend>Embroidery Delivery Details</legend>
                <table style="width:1100px;" cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all">
                    <thead class="form_table_header">
                    	<tr>
	                		<th colspan="4" class="must_entry_caption">Job No &nbsp;&nbsp;&nbsp;
	                			<input type="text" name="txtJob_no" id="txtJob_no" value="" class="text_boxes"  style="width:120px" placeholder="Browse" onDblClick="fnc_job_no();" readonly/>
                                <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px;" />
                            </th>
 	                		<th>Work Order No</th>
	                		<th colspan="2"><input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px;" placeholder="Display" readonly /></th>
                            <th style="display:none">Buyer Style Ref.</th>
	                		<th colspan="2" style="display:none"><input type="text" name="txtStyleRef" id="txtStyleRef" value="" class="text_boxes"  style="width:110px" placeholder="Display" readonly/></th>
                            <th>Buyer</th>
                            <th colspan="2"><input type="text" name="txtBuyerName" id="txtBuyerName" value="" class="text_boxes"  style="width:110px" placeholder="Display" readonly/></th>
                            <th>Delivery Status</th>
                            <th colspan="2"><?php echo create_drop_down( "cboshipingStatus", 100, $shipment_status,"", 0, "--  --", 0, "" ); ?></th>
                    		<th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                	    </tr>
                	    <tr>
	                        <th width="30">SL</th>
                            <th width="90">Buyer PO</th>
                            <th width="90">Style Ref.</th>
	                        <th width="90">Gmts Item</th>
	                        <th width="80">Body Part</th>
	                        <th width="80">Emb Name</th>
	                        <th width="90">Process/Type</th>
	                        <th width="80">Color</th>
	                        <th width="80">Size</th>
                            <th width="60">Order Qty</th>
	                        <th width="60" id="receiv_id">QC Pass Qty (Pcs)</th>
	                        <th width="60">Previous Delv</th>
	                        <th width="60">Balance</th>
	                        <th width="60" class="must_entry_caption">Current Delv (Pcs)</th>
                            <th width="50">Sort Qty</th>
                            <th width="50">Reject Qty</th>
                            <th width="80">Cutting Number</th>
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
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td align="center"><input type="text" name="txtCurrDelv_1" id="txtCurrDelv_1" class="text_boxes_numeric" style="width:45px;" onBlur="fnc_production_qty_ability(this.value,1); fnc_total_calculate();" /></td>
                            <td align="center"><input type="text" name="txtsort_1" id="txtsort_1" class="text_boxes_numeric" style="width:40px;" onBlur="fnc_total_calculate();" /></td>
                            <td align="center"><input type="text" name="txtreject_1" id="txtreject_1" class="text_boxes_numeric" style="width:40px;" onBlur="fnc_total_calculate();" /></td>
                            <td>&nbsp;</td>
                    		<td align="center"><input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" style="width:60px;" />
                            	<input type="hidden" name="txtDtlsUpdateId_1" id="txtDtlsUpdateId_1" style="width:50px" class="text_boxes" value="" />
                                <input type="hidden" name="txtColorSizeid_1" id="txtColorSizeid_1" style="width:50px" class="text_boxes" value="" />
                                <input type="hidden" name="txtpoid_1" id="txtpoid_1" style="width:50px" class="text_boxes" value="" />
                            </td> 
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
                    		<td>&nbsp;</td>
                    		<td>Total:</td>
                    		<td align="center"><input type="text" name="txtTotCurrDelv" id="txtTotCurrDelv" class="text_boxes_numeric" style="width:45px;" placeholder="Display" readonly /></td>
                            <td align="center"><input type="text" name="txtTotsort" id="txtTotsort" class="text_boxes_numeric" style="width:40px;" placeholder="Display" readonly /></td>
                    		<td align="center"><input type="text" name="txtTotreject" id="txtTotreject" class="text_boxes_numeric" style="width:40px;" placeholder="Display" readonly /></td>
                            <td>&nbsp;</td>
                    		<td align="center">&nbsp;</td> 
                    	</tr>
                    </tfoot>                   
                </table>            
                <table width="830" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" valign="middle" class="button_container">
                         <? echo create_drop_down( "cbo_template_id", 100, $report_template_list,'', 0, '', 0, "");?>&nbsp;
                        	<? echo load_submit_buttons($permission,"fnc_embl_delivery",0,0,"reset_form('embdeliventry_1', '','','','')",1); ?>
                        </td>
                    </tr>   
					<tr>
						<td align="center" valign="middle">
						   <input id="Print" class="formbutton" type="button" style="width:80px" onClick="fnc_embl_delivery_print(1)" name="print" value="Print">
						   <input id="Print_1" class="formbutton" type="button" style="width:80px" onClick="fnc_embl_delivery_print(2)" name="print_1" value="Print 1">
						</td>
					</tr>
                </table>
            </fieldset>          
        </form>                         
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>