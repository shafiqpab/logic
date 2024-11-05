<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Wash Delivery
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	31-03-2019 
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


$user_level=$_SESSION['logic_erp']["company_id"];

//print_r($user_level);
//-----------------------------------------------------------------------------------------------------------------

echo load_html_head_contents("Wash Delivery","../../", 1, 1, $unicode,'','');
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
		
		//alert(deli_party_name);
		
		if(within_group==1 && type==1)
		{
			$('#cbo_deli_party_name').val(0);
			$('#cbo_deli_party_name').attr('disabled',false);
			load_drop_down( 'requires/wash_delivery_controller', company+'_'+1, 'load_drop_down_buyer', 'buyer_td' );
			$('#td_party_location').css('color','blue');
			$('#td_deli_party').css('color','blue');
			$('#td_dparty_location').css('color','blue');
		}
		else if(within_group==2  && type==1)
		{
			$('#cbo_deli_party_name').val(0);
			$('#cbo_deli_party_name').attr('disabled',true);
			load_drop_down( 'requires/wash_delivery_controller', company+'_'+2, 'load_drop_down_buyer', 'buyer_td' );
			$('#td_party_location').css('color','black');
			$('#td_deli_party').css('color','black');
			$('#td_dparty_location').css('color','black');
		}
		else if(within_group==1 && type==2)
		{
			load_drop_down( 'requires/wash_delivery_controller', party_name+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
			$('#td_party_location').css('color','blue');
			$('#td_deli_party').css('color','blue');
			$('#td_dparty_location').css('color','blue');
		}
		else if(within_group==1 && type==3)
		{
			load_drop_down( 'requires/wash_delivery_controller', deli_party_name+'_'+3, 'load_drop_down_location', 'dparty_location_td' ); 
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
			var data=document.getElementById('cbo_company_name').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value+"_"+document.getElementById('txt_variable_status').value;
			var title = 'Order Search'
			var page_link='requires/wash_delivery_controller.php?action=job_popup&data='+data
			
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
				var list_view_orders = return_global_ajax_value( 0+'**'+ex_data[0]+'**'+1+'**'+0+'**'+company, 'load_php_dtls_form', '', 'requires/wash_delivery_controller');
				if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
				}
				$('#cbo_company_name').attr('disabled','disabled');
				$('#cbo_within_group').attr('disabled','disabled');
				$('#cbo_party_name').attr('disabled','disabled');
				
				//setFilterGrid("dtls_tbody",-1);
				fnc_total_calculate();
				release_freezing();
			}
		}
	}
	
	function fnc_embl_delivery( operation )
	{
		if(operation==4)
		{
			if ( $('#txt_delv_no').val()=='')
			{
				alert ('Delevery Number Not Save.');
				return;
			}
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+$("#txtJob_no").val()+'*'+report_title, "embl_delivery_entry_print", "requires/wash_delivery_controller") 
			//return;
			show_msg("3");
		}
		else if(operation==5)
		{
			if ( $('#txt_delv_no').val()=='')
			{
				alert ('Delevery Number Not Save.');
				return;
			}
			var report_title='Delivery';
			print_report( $('#cbo_company_name').val()+'*'+$('#txt_update_id').val()+'*'+$("#txtJob_no").val()+'*'+report_title, "embl_delivery_entry_print2", "requires/wash_delivery_controller") 
			//return;
			show_msg("3");
		}		
		
		else
		{
			if ( form_validation('cbo_within_group', 'within group')==false )
				{
					return;
				}
			
			
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
			
			var data_str=get_submitted_data_string('txt_delv_no*cbo_company_name*cbo_location_name*cbo_party_name*cbo_party_location*cbo_deli_party_name*cbo_deli_party_location*txt_delivery_date*cbo_within_group*txt_remarks*txtJob_no*txt_update_id*txt_update_details_id*cboshipingStatus*txt_variable_status',"../../");
			var tot_row=$('#dtls_tbody tr').length;
			 var k=0;
			 //alert(data_str);
			for (var i=1; i<=tot_row; i++)
			{
				var qty=$('#txtCurrDelv_'+i).val();
				if(qty*1>0)
				{
					k++;
					data_str+="&txtbuyerPoId_" + k + "='" + $('#txtbuyerPoId_'+i).val()+"'"+"&txtCurrDelv_" + k + "='" + $('#txtCurrDelv_'+i).val()+"'"+"&txtRemarks_" + k + "='" + $('#txtRemarks_'+i).val()+"'"+"&txtpoid_" + k + "='" + $('#txtpoid_'+i).val()+"'"+"&txtColorSizeid_" + k + "='" + $('#txtColorSizeid_'+i).val()+"'"+"&txtsort_" + k + "='" + $('#txtsort_'+i).val()+"'"+"&txtreject_" + k + "='" + $('#txtreject_'+i).val()+"'"+"&txtDtlsUpdateId_" + k + "='" + $('#txtDtlsUpdateId_'+i).val()+"'";
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
			http.open("POST","requires/wash_delivery_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_embl_delivery_response;
		}
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
				document.getElementById('txtJob_no').value = response[3];
				document.getElementById('txt_update_details_id').value = response[4];
				//var list_view_orders = return_global_ajax_value( response[1]+'**'+$('#txtJob_no').val()+'**'+2, 'load_php_dtls_form', '', 'requires/wash_delivery_controller');
				
				show_list_view(response[1],'delivery_list_view','delivery_list_view','requires/wash_delivery_controller','');
				var list_view_orders = return_global_ajax_value( '', 'default_html', '', 'requires/wash_delivery_controller');
				if(list_view_orders)
				{
					$("#dtls_tbody tr").remove();
					// $('#txtJob_no').attr('disabled',true);
					//reset_form('embdeliventry_1','form_table_header','txtJob_no*txt_order_id*txt_wo_no*txtStyleRef*txtBuyerName*cboshipingStatus','','');
					reset_form('','','txtJob_no*txt_order_id*txt_wo_no*txtStyleRef*txtBuyerName*cboshipingStatus*txtJob_no','','');
					$('#txtTotCurrDelv').val(0);
					$('#txtTotsort').val(0);
					$('#txtTotreject').val(0);
					$('#txtJob_no').attr('disabled',false);
					//$('#txt_update_id').val('');
					//$('#txt_order_id').val('');
					//$('#txtDtlsUpdateId_1').val('');
					//$('#txtColorSizeid_1').val('');
					//$('#txtpoid_1').val('');
					$("#dtls_tbody").append(list_view_orders);
				}
				//set_button_status(1, permission, 'fnc_embl_delivery',1);
				set_button_status(0, permission, 'fnc_embl_delivery',1,1);
				/*if(list_view_orders!='')
				{
					$("#dtls_tbody tr").remove();
					$("#dtls_tbody").append(list_view_orders);
					//setFilterGrid("dtls_tbody",-1);
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embl_delivery',1);
				}*/
			}
			if(response[0]==2)
			{
				reset_form('embdeliventry_1','delivery_list_view','','','');
				//reset_form('embdeliventry_1','dtls_tbody','','','');txtJob_no,txt_order_id,txt_wo_no,txtStyleRef,txtBuyerName,cboshipingStatus
				location.reload(); 
			}
			release_freezing();
		}
	}
	
	function fnc_embl_delivery_dtls(id) 
	{
		var company = $('#cbo_company_name').val();
		var list_view_orders = return_global_ajax_value( $('#txt_update_id').val()+'**'+$('#txtJob_no').val()+'**'+2+'**'+id+'**'+company, 'load_php_dtls_form', '', 'requires/wash_delivery_controller');
		
		if(list_view_orders!='')
		{
			$("#dtls_tbody tr").remove();
			$("#dtls_tbody").append(list_view_orders);
			//setFilterGrid("dtls_tbody",-1);
			fnc_total_calculate();
			get_php_form_data($('#txt_update_id').val()+'**'+$('#txtJob_no').val()+'**'+2+'**'+id, "populate_data_from_data", "requires/wash_delivery_controller");
				//$('#txtJob_no').attr('disabled',false);
				$('#txtJob_no').attr('disabled',true);
			set_button_status(1, permission, 'fnc_embl_delivery',1);
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
			var page_link='requires/wash_delivery_controller.php?action=delivery_popup&data='+data;
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
					reset_form('','','txt_delv_no*cbo_location_name*cbo_party_name*cbo_within_group*txt_delivery_date*txtJob_no*txt_update_id*txt_update_details_id','','');
					
					$('#txt_update_id').val(emb_data[0]);
					$('#txt_delv_no').val(emb_data[1]);
					$('#cbo_location_name').val(emb_data[2]);
					$('#cbo_within_group').val(emb_data[3]);
					$('#txt_delivery_date').val(emb_data[5]);
					$('#txt_remarks').val(emb_data[6]);
					
					$('#txtJob_no').val(emb_data[7]);
					$('#txt_wo_no').val(emb_data[8]);
					$('#txtStyleRef').val(emb_data[9]);
					$('#txtBuyerName').val(emb_data[10]);
					$('#cboshipingStatus').val(emb_data[11]);
					//alert(emb_data[3]);
					//fnc_load_party(type,within_group)
					if(emb_data[3]==1)
					{
						fnc_load_party(2,1);
						//fnc_load_party(3,1);
					}
					else if(emb_data[3]==2)
					{
						$('#cbo_party_location').attr('disabled','disabled');
						$('#cbo_deli_party_name').attr('disabled','disabled');
						$('#cbo_deli_party_location').attr('disabled','disabled');
					}
					fnc_load_party(1,emb_data[3]);
					
					$('#cbo_party_name').val(emb_data[4]);
					if(emb_data[3]==1)
					{
						load_drop_down( 'requires/wash_delivery_controller', emb_data[4]+'_'+2, 'load_drop_down_location', 'party_location_td' ); 
					}
					if(emb_data[3]==2)
					{
						$('#cbo_party_location').attr('disabled','disabled');
						
					}
					//alert(emb_data[12]);
					$('#cbo_party_location').val(emb_data[12]);
					$('#cbo_deli_party_name').val(emb_data[13]);
					
					if(emb_data[3]==1)
					{
						fnc_load_party(3,1);
					}
					$('#cbo_deli_party_location').val(emb_data[14]);
					$('#cbo_company_name').attr('disabled','disabled');
					$('#cbo_within_group').attr('disabled','disabled');
					$('#cbo_party_name').attr('disabled','disabled');
					//get_php_form_data( splt_val[0], "load_php_data_to_form", "requires/wash_delivery_controller" );
					reset_form('','','txtJob_no*txt_order_id*txt_wo_no*txtStyleRef*txtBuyerName*cboshipingStatus','','');
					show_list_view($('#txt_update_id').val(),'delivery_list_view','delivery_list_view','requires/wash_delivery_controller','');
					set_button_status(0, permission, 'fnc_embl_delivery',1,1);
					
					/*var list_view_orders = return_global_ajax_value( emb_data[0]+'**'+emb_data[7]+'**'+1, 'load_php_dtls_form', '', 'requires/wash_delivery_controller');
					if(list_view_orders!='')
					{
						$("#dtls_tbody tr").remove();
						$("#dtls_tbody").append(list_view_orders);
					}
					//setFilterGrid("dtls_tbody",-1);
					fnc_total_calculate();
					set_button_status(1, permission, 'fnc_embl_delivery',1);*/
					release_freezing();
				}
			}
		}
	}
	
	function fnc_production_qty_ability(value,i)
	{
		var placeholder_value = $("#txtCurrDelv_"+i).attr('placeholder')*1;
		var pre_delv_qty = $("#txtCurrDelv_"+i).attr('pre_delv_qty')*1;
		var delv_qty = $("#txtCurrDelv_"+i).attr('delv_qty')*1;
		
		//var balance_qty = $("#txtCurrDelv_"+i).attr('balance_qty')*1;
		//alert(balance_qty);
		var update_id = $('#txt_update_id').val();
		if(update_id!="")
		{
			if((value*1)>placeholder_value)
			{
				alert("Delivery qty Excceded by QC qty.");
				$("#txtCurrDelv_"+i).val('');
				return;
			}
		
		}
		else
		{
			if((value*1)>placeholder_value)
			{
				alert("Delivery qty Excceded by QC qty.");
				$("#txtCurrDelv_"+i).val('');
				return;
			}
			
		}
			
			
		/*if((value*1)>placeholder_value)
		{
			//alert("Qnty Excceded");
			var confirm_value=confirm("Delivery qty Excceded by QC qty. Press cancel to proceed otherwise press ok.");
			if(confirm_value!=0)
			{
				$("#txtCurrDelv_"+i).val('');
			}			
			return;
			
		}*/
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
		//var rowCount = $('#dtls_tbody tr').length-1;
		var rowCount = $('#dtls_tbody tr').length;
		//alert(rowCount)
		math_operation( "txtTotCurrDelv", "txtCurrDelv_", "+", rowCount );
		math_operation( "txtTotsort", "txtsort_", "+", rowCount );
		math_operation( "txtTotreject", "txtreject_", "+", rowCount );
	}
	
function chk_qty_level_variabe(company)
{
   var status = return_global_ajax_value(company, 'chk_qty_level_variable', '', 'requires/wash_delivery_controller').trim();
   status = status.split("**");
   $('#txt_variable_status').val(status[0]);
}


function fnc_td_change(btn_id,status)
{
	if(status==2)
	{
		$('.receiv_id').show();
	}
	else
	{
		$('.receiv_id').hide();
	}
	
}

</script>

</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission); ?>
        <form name="embdeliventry_1" id="embdeliventry_1" autocomplete="off"> 
			<fieldset style="width:1000px;">
			<legend>Wash Delivery</legend>
                <table width="990" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="4" align="right"><strong>Delivery ID</strong></td>
                        <td colspan="4">
                        <input class="text_boxes"  type="text" name="txt_delv_no" id="txt_delv_no" onDblClick="openmypage_delv_no();" placeholder="Double Click" style="width:140px;" readonly />
                        <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:90px" class="text_boxes" value="" />
                        <input type="hidden" name="txt_update_details_id" id="txt_update_details_id" style="width:90px" class="text_boxes" value="" />
                        </td>
                    </tr>
                    <tr>
                        <td width="100" class="must_entry_caption">Company</td>
                        <td width="150"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/wash_delivery_controller', this.value+'_'+1, 'load_drop_down_location', 'location_td'); location_select();chk_qty_level_variabe(this.value); fnc_load_party(1,document.getElementById('cbo_within_group').value);get_php_form_data( this.value, 'company_wise_report_button_setting','requires/wash_delivery_controller'); fnc_td_change(this.value,document.getElementById('txt_variable_status').value);"); ?>
                        <input type="hidden" id="txt_variable_status" name="txt_variable_status" value="" />
                        </td>
                        <td width="100">Location</td>
                        <td width="150" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-- Select Location --", $selected, "" ); ?></td>
                        <td width="100" class="must_entry_caption">Within Group</td>
                        <td width="150"><?php echo create_drop_down( "cbo_within_group", 150, $yes_no,"", 1, "-- Select --",0, "fnc_load_party(1,this.value);" ); ?></td>
                        <td width="100" class="must_entry_caption">Party</td>
                        <td id="buyer_td"><? echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $selected, "load_drop_down( 'requires/wash_delivery_controller', this.value+'_'+2, 'load_drop_down_location', 'party_location_td'); location_select(); fnc_load_party(2,document.getElementById('cbo_within_group').value);"); ?></td>
                    </tr>
                    <tr>
                    	<td id="td_party_location">Party Location</td>
                        <td id="party_location_td"><? echo create_drop_down( "cbo_party_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, "",1 ); ?></td>

                        <td id="td_deli_party">Delivery Party</td>
                        <td><? echo create_drop_down( "cbo_deli_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $selected, "load_drop_down( 'requires/wash_delivery_controller', this.value+'_'+3, 'load_drop_down_location', 'dparty_location_td'); location_select(); fnc_load_party(3,document.getElementById('cbo_within_group').value);"); ?></td>
                        <td id="td_dparty_location">Del.Party Location</td>
                        <td id="dparty_location_td"><? echo create_drop_down( "cbo_deli_party_location", 150, $blank_array,"", 1, "--Select Location--", $selected, "",1 ); ?></td>

                        <td class="must_entry_caption">Delivery Date</td>
                        <td><input type="text" name="txt_delivery_date" id="txt_delivery_date" style="width:140px" class="datepicker" value="" /></td>
                    </tr>
                    <tr>
                        <td>Remarks</td>
                        <td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks"  class="text_boxes" style="width:650px;" /></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr> 
                </table>
            </fieldset>
            <br>
            <fieldset style="width:1000px;">
            <legend>Wash Delivery Details</legend>
                <table style="width:1000px;" cellpadding="0" cellspacing="2" border="1" class="rpt_table" rules="all">
                    <thead class="form_table_header">
                    	<tr>
	                		<th colspan="3" class="must_entry_caption">Job No &nbsp;&nbsp;&nbsp;
	                			<input type="text" name="txtJob_no" id="txtJob_no" value="" class="text_boxes"  style="width:120px" placeholder="Browse" onDblClick="fnc_job_no();" readonly/>
                                <input type="hidden" name="txt_order_id" id="txt_order_id" class="text_boxes" style="width:70px;" />
                            </th>
 	                		<th>Work Order No</th>
	                		<th colspan="2"><input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes" style="width:120px;" placeholder="Display" readonly /></th>
                            <th colspan="2">Buyer Style Ref.</th>
	                		<th colspan="2"><input type="text" name="txtStyleRef" id="txtStyleRef" value="" class="text_boxes"  style="width:100px" placeholder="Display" readonly/></th>
                            <th>Buyer</th>
                            <th colspan="2"><input type="text" name="txtBuyerName" id="txtBuyerName" value="" class="text_boxes"  style="width:90px" placeholder="Display" readonly/></th>
                            <th>Delivery Status</th>
                            <th><?php echo create_drop_down( "cboshipingStatus", 50, $shipment_status,"", 0, "--  --", 0, "" ); ?></th>
                             <th  class="receiv_id" style="display:none"></th>
                	    </tr>
                	    <tr>
	                        <th width="30">SL</th>
                            <th width="90">Buyer PO</th>
	                        <th width="90">Gmts Item</th>
	                        <th width="80">Process Name</th>
	                        <th width="90">Wash Type</th>
	                        <th width="80">Color</th>
                            <th width="70">Size</th>
                            <th width="60">Order Qty (Pcs)</th>
	                        <th width="60">QC Pass Qty (Pcs)</th>
                           <th width="60" class="receiv_id" style="display:none">Receive Return</th>
	                        <th width="60">Previous Delv</th>
	                        <th width="60">Balance</th>
	                        <th width="60" class="must_entry_caption">Current Delv (Pcs)</th>
                            <th width="50">Sort Qty</th>
                            <th width="50">Reject Qty</th>
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
							<td class="receiv_id" style="display:none">&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td align="center"><input type="text" name="txtCurrDelv_1" id="txtCurrDelv_1" class="text_boxes_numeric" style="width:55px;" onBlur="fnc_production_qty_ability(this.value,1); fnc_total_calculate();" /></td>
                            <td align="center"><input type="text" name="txtsort_1" id="txtsort_1" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_total_calculate();" /></td>
                            <td align="center"><input type="text" name="txtreject_1" id="txtreject_1" class="text_boxes_numeric" style="width:50px;" onBlur="fnc_total_calculate();" /></td>
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
                           <td class="receiv_id" style="display:none">&nbsp;</td>
                    		<td>&nbsp;</td>
                    		<td>Total:</td>
                    		<td align="center"><input type="text" name="txtTotCurrDelv" id="txtTotCurrDelv" class="text_boxes_numeric" style="width:55px;" placeholder="Display" readonly /></td>
                            <td align="center"><input type="text" name="txtTotsort" id="txtTotsort" class="text_boxes_numeric" style="width:50px;" placeholder="Display" readonly /></td>
                    		<td align="center"><input type="text" name="txtTotreject" id="txtTotreject" class="text_boxes_numeric" style="width:50px;" placeholder="Display" readonly /></td>
                    		<td align="center">&nbsp;</td> 
                    	</tr>
                    </tfoot>                   
                </table>            
                <table width="1000" cellspacing="2" cellpadding="0" border="0">
                    <tr>
                        <td align="center" valign="middle" class="button_container">
                        	<? 
							//echo load_submit_buttons($permission,"fnc_embl_delivery",0,1,"reset_form('embdeliventry_1', '','','','')",1); 
                         		echo load_submit_buttons( $permission, "fnc_embl_delivery", 0,0,"reset_form('embdeliventry_1','delivery_list_view','','','')",1);
							?>
							<input type="button" name="print" id="print" value="Print" onClick="fnc_embl_delivery(4)" style="width:100px;display:none;" class="formbuttonplasminus" />	
							<input type="button" name="print2" id="print2" value="Print 2" onClick="fnc_embl_delivery(5)" style="width:100px;display:none;" class="formbuttonplasminus" />
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