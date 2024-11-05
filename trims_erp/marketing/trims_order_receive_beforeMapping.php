<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Order Receive 
					
Functionality	:	
				

JS Functions	:

Created by		:	K.M Nazim Uddin  
Creation date 	: 	11.12.2018 
Updated by 		: 	
Update date		: 		   	   

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
echo load_html_head_contents("Pre Export Finance Form", "../../", 1, 1,'','1','');
?>	

<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission='<? echo $permission; ?>';
	
	function fnc_order_receive( operation )
	{//cbo_company_id*cbo_source_id*cbo_customer_id*txt_wo_no*txt_wo_id*cbo_currency_id*txt_rcv_date*txt_ex_rate*txt_remarks*cbo_staus_id
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_source_id = $('#cbo_source_id').val();
		var cbo_customer_id = $('#cbo_customer_id').val();
		var txt_wo_no = $('#txt_wo_no').val();
		var txt_wo_id = $('#txt_wo_id').val();
		var cbo_currency_id = $('#cbo_currency_id').val();
		var txt_rcv_date = $('#txt_rcv_date').val();
		var txt_ex_rate = $('#txt_ex_rate').val();
		var txt_remarks = $('#txt_remarks').val();
		var cbo_staus_id = $('#cbo_staus_id').val();

		$("#tbl_pi_item tbody tr").each(function()
			{
				var txtCustOrder 			= $(this).find('input[name="txtCustOrder[]"]').val();
				var txtCustOrderId 			= $(this).find('input[name="txtCustOrderId[]"]').val();
				var txtCustStyle 			= $(this).find('input[name="txtCustStyle[]"]').val();
				var cboCustBuyer 			= $(this).find('select[name="cboCustBuyer[]"]').val();
				var cboSection 				= $(this).find('select[name="cboSection[]"]').val();
				var cboItemGroup 			= $(this).find('select[name="cboItemGroup[]"]').val();
				var txtItemDes 				= $(this).find('input[name="txtItemDes[]"]').val();
				var txtItemColor 			= $(this).find('input[name="txtItemColor[]"]').val();
				var txtItemSize 			= $(this).find('select[name="txtItemSize[]"]').val();
				var cboUom 					= $(this).find('select[name="cboUom[]"]').val();
				var txtQty 					= $(this).find('input[name="txtQty[]"]').val();
				var txtRate 				= $(this).find('input[name="txtRate[]"]').val();
				var txtAmount 				= $(this).find('input[name="txtAmount[]"]').val();
				var txtDomRate 				= $(this).find('input[name="txtDomRate[]"]').val();
				var txtDomAmount 			= $(this).find('input[name="txtDomAmount[]"]').val();
				var txtDelDate 				= $(this).find('input[name="txtDelDate[]"]').val();
				var cboStausId 				= $(this).find('input[name="cboStausId[]"]').val();
				var updateIdDtls 			= $(this).find('input[name="updateIdDtls[]"]').val();
				
				//txt_total_amount 	+= $(this).find('input[name="amount[]"]').val()*1;
				j++;
				if(cboSection=='' || cboItemGroup=='' || txtAmount==''|| txtCustOrder=='')
				{				
					if(cboSection=='')
					{
						alert('Please Select Section');
					}
					else if(cboItemGroup=='')
					{
						alert('Please Select Group');
					}
					else if(txtAmount=='')
					{
						alert('Please Fill Amount');
					}
					else
					{
						alert('Please Fill up Order');
					}
					return false;
				}
				i++;
				
				data_all += "&txtCustOrder_" + j + "='" + txtCustOrder + "'&txtCustOrderId_" + j + "='" + txtCustOrderId + "'&txtCustStyle_" + j + "='" + txtCustStyle + "'&cboCustBuyer_" + j + "='" + cboCustBuyer + "'&cboSection_" + j + "='" + cboSection + "'&cboItemGroup_" + j + "='" + cboItemGroup + "'&txtItemDes_" + j + "='" + txtItemDes + "'&txtItemColor_" + j + "='" + txtItemColor+ "'&txtItemSize_" + j + "='" + txtItemSize + "'&cboUom_" + j + "='" + cboUom + "'&txtQty_" + j + "='" + txtQty + "'&txtRate_" + j + "='" + txtRate + "'&txtAmount_" + j + "='" + txtAmount +"'&txtDomRate_" + j + "='" + txtDomRate + "'&txtDomAmount_" + j + "='" + txtDomAmount+ "'&txtDelDate_" + j + "='" + txtDelDate+ "'&cboStausId_" + j + "='" + cboStausId+ "'&updateIdDtls_" + j + "='" + updateIdDtls+ "'";
				
				//data_all+="&colorName_" + i + "='" + $('#colorName_'+j).val()+"'"+"&countName_" + i + "='" + $('#countName_'+j).val()+"'"+"&yarnComposition_" + i + "='" + $('#yarnComposition_'+j).val()+"'"+"&commRate_" + i + "='" + $('#commRate_'+j).val()+"'"+"&type_" + i + "='" + $('#type_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDoChk_" + i + "='" + $('#hideDoChk_'+j).val()+"'";
				//txt_total_amount+=$('#amount_'+j).val()*1;
			});//cbo_company_id*cbo_source_id*cbo_customer_id*txt_wo_no*txt_wo_id*cbo_currency_id*txt_rcv_date*txt_ex_rate*txt_remarks*cbo_staus_id
		var data="action=save_update_delete&operation="+operation+'&total_row='+i+'&hdn_update_id='+hdn_update_id+'&cbo_company_id='+cbo_company_id+'&cbo_source_id='+cbo_source_id+'&cbo_customer_id='+cbo_customer_id+'&txt_wo_no='+txt_wo_no+'&txt_wo_id='+txt_wo_id+'&cbo_currency_id='+cbo_currency_id+'&txt_rcv_date='+txt_rcv_date+'&txt_ex_rate='+txt_ex_rate+'&txt_remarks='+txt_remarks+'&cbo_staus_id='+cbo_staus_id+data_all;
		//alert (data); //return;
		freeze_window(operation);
		
		http.open("POST","requires/trims_order_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_order_receive_response;
	}

	function fnc_order_receive_response()
	{
		if(http.readyState == 4)
		{
			var response=http.responseText.split('**');
			//alert(response);
			if(response[0]==0 || response[0]==1)
			{
				show_msg(trim(response[0]));
				document.getElementById('hdn_update_id').value = response[1];
				document.getElementById('txt_sys_id').value = response[1];
				//$('#txt_bank_ref').attr({'disabled': 'disabled'});
				//alert(1);
				set_button_status(1, permission, 'fnc_order_receive',1);
			}
			release_freezing();	
		} 
	}

	function pop_loan()
	{
		var page_link='requires/trims_order_receive_controller.php?action=doc_lon_popup'; 
		var title="Search Bank Ref. Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mstID=this.contentDoc.getElementById("hidden_system_id").value.split("**"); // master table id
			//$("#txt_negotiation_date").attr("disabled",false);
	  		get_php_form_data(mstID[0], "populate_loan_from_data", "requires/trims_order_receive_controller");
	  		set_button_status(1, permission, 'fnc_export_loan_cal',1);
	  	}
	}

	function pop_doc_submission()
	{
		var page_link='requires/trims_order_receive_controller.php?action=doc_sub_popup'; 
		var title="Search Bank Ref. Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mstID=this.contentDoc.getElementById("hidden_system_number").value.split("**"); // master table id
			//$("#txt_negotiation_date").attr("disabled",false);
	  		get_php_form_data(mstID[0], "populate_master_from_data", "requires/trims_order_receive_controller");
	  	}
	}


	function cal_int_per_year(i)
	{
		var loan=$("#txtLoan_"+i).val()*1;
		var interest=$("#txtInterest_"+i).val()*1;
		var intPerYear=(loan*interest)/100;
		$('#txtIntYear_'+i).val(intPerYear.toFixed(2));
		var purDate=$("#txtPurDate_"+i).val();
		var matudeDate=$("#txtMatudeDate_"+i).val();
		if(purDate!='' && matudeDate!='')
		{
			cal_days(i);
		}
		//cal_amount(i);
	}

	function cal_days(i)
	{
		var purDate=$("#txtPurDate_"+i).val();
		var matudeDate=$("#txtMatudeDate_"+i).val();
		//alert(purDate+"**"+matudeDate);
		if(purDate!='' && matudeDate!='')
		{
			var datediff = date_diff( 'd', purDate, matudeDate )+1;					 
  			$('#txtDays_'+i).val(datediff);
		}
		else
		{
			$('#txtDays_'+i).val('');
			$('#txtAmount_'+i).val('');
		}
		cal_amount(i);
		/*var intYear=$("#txtIntYear_"+i).val()*1;
		var days=$("#txtDays_"+i).val()*1;
		if(intYear!='' && days!='')
		{
			cal_amount(i);
		}*/
	}

	function cal_amount(i)
	{
		var intYear=$("#txtIntYear_"+i).val()*1;
		var days=$("#txtDays_"+i).val()*1;
		var amount=(intYear/360)*days;
		$('#txtAmount_'+i).val(amount.toFixed(2));
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#invo_table tbody tr').length;
		math_operation( "txt_total_interest", "txtAmount_", "+", numRow,ddd );
		var total_amount=$("#txt_total_interest").val()*1;
		var loan=$("#txtLoan_1").val()*1;
		var total=total_amount+loan;
		$('#txt_total').val(total.toFixed(2));
		var deduction=$("#txt_deduction").val()*1;
		if(deduction!='')
		{
			cal_difference()
		}
	}

	function cal_difference()
	{
		var total=$("#txt_total").val()*1;
		var deduction=$("#txt_deduction").val()*1;
		var difference=total-deduction;
		$('#text_difference').val(difference.toFixed(2));
	}

	function fnc_for_customer_source(source)
	{
		if(source==1)
		{
			$("#txt_wo_no").attr("disabled",false);
			$("#txt_wo_no").attr("placeholder", "Double Click");
		}
		else
		{
			$("#txt_wo_no").attr("disabled",true);
			$("#txt_wo_no").attr("placeholder", "");
		}
	}

	function exchange_rate()
	{
		var rcv_date=$('#txt_rcv_date').val();
		var currency_id=$('#cbo_currency_id').val();
		if(rcv_date!='')
		{
			var response=return_global_ajax_value(currency_id+"**"+rcv_date, 'check_conversion_rate', '', 'requires/trims_order_receive_controller');
			$('#txt_ex_rate').val(response);
		}
		else
		{
			return;
		}
	}

	function pop_work_order()
	{		
		if ( form_validation('cbo_customer_id','Customer')==false )
		{
			return;
		}
		else
		{
			var title = 'Order No. Pop-up';
			var company = $('#cbo_company_id').val();
			var customer_id = $('#cbo_customer_id').val();
			var page_link = 'requires/trims_order_receive_controller.php?company='+company+'&customer_id='+customer_id+'&action=order_popup';
		
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=370px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]; 
				var mstID=this.contentDoc.getElementById("hidd_booking_data").value.split("_"); // master table id
				//alert(mstID); return;
				$("#cbo_company_id").attr("disabled",false);
				$("#cbo_customer_id").attr("disabled",false);
				$("#cbo_currency_id").attr("disabled",false);
				$('#txt_wo_id').val(mstID[0]);
				$('#txt_wo_no').val(mstID[1]);
				$('#cbo_currency_id').val(mstID[2]);
				var exchange_rate = $('#txt_ex_rate').val();
		  		show_list_view(mstID[0]+"_"+exchange_rate+"_"+customer_id+"_"+1,'order_dtls_list_view','order_container','requires/trims_order_receive_controller','setFilterGrid(\'list_view\',-1)');
		  	}
		}
	}

	function fnc_load_dtls_data(i)
	{
		alert(i);
		var company = $('#cbo_company_id').val();
		load_drop_down( 'requires/trims_order_receive_controller', company+'_'+2+'_'+i, 'load_drop_down_section', 'sectionTd_'+i );
		load_drop_down( 'requires/trims_order_receive_controller', company+'_'+2+'_'+i, 'load_drop_down_buyer', 'buyerTd_'+i  );
		load_drop_down( 'requires/trims_order_receive_controller', company+'_'+2+'_'+i, 'load_drop_down_group', 'groupTd_'+i  );
	}

	function add_break_down_tr( i )
	{
		var row_num=$('#order_table tbody tr').length;
		//var category=$('#cbo_item_category_id').val();

		if (row_num!=i)
		{
			return false;
		}
		else
		{
			i++;

			$("#order_table tbody tr:last").clone().find("input,select").each(function(){

			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  /*'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i },*/
			  'value': function(_, value) { return value }
			});

			}).end().appendTo("#order_table");
			
			$('#txtSl_'+i).removeAttr("value").attr("value",i);
			$("#order_table tbody tr:last td#buyerTd_"+row_num).removeAttr('id').attr('id','buyerTd_'+i);
			$("#order_table tbody tr:last td#sectionTd_"+row_num).removeAttr('id').attr('id','sectionTd_'+i);
			$("#order_table tbody tr:last td#groupTd_"+row_num).removeAttr('id').attr('id','groupTd_'+i);
			fnc_load_dtls_data(i);
			$("#order_table tbody tr:last").removeAttr('id').attr('id','row_'+i);

			$('#txtRate_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+")");
			$('#txtQty_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculate_amount("+i+")");
			$('#txtDelDate_'+i).removeAttr("onChange").attr("onChange","add_break_down_tr("+i+")");
			/*
			//var tds = tr.find("td[id='Status']");
			 
			if(category==1)
			{
				$('#yarnCompositionItem1_'+i).removeAttr("onchange").attr("onchange","control_composition("+i+",'comp_one')").removeAttr("disabled");
				$('#yarnCompositionPercentage1_'+i).removeAttr("onchange").attr("onchange","control_composition("+i+",'percent_one')");
				$('#yarnCompositionItem2_'+i).removeAttr("onchange").attr("onchange","control_composition("+i+",'comp_two')");
				$('#yarnCompositionPercentage2_'+i).removeAttr("onchange").attr("onchange","control_composition("+i+",'percent_two')");
				$('#yarnCompositionPercentage1_'+i).removeAttr("value").attr("value","100");
				$('#colorName_'+i).removeAttr("disabled");
				$('#countName_'+i).removeAttr("disabled");
				$('#yarnType_'+i).removeAttr("disabled");
                $('#uom_'+i).val(12);
				$('#colorName_'+i).removeAttr("onBlur").attr("onBlur","fn_add_color_id("+i+")");

			}
			else if(category==4)
			{
				$('#itemgroupid_'+i).removeAttr("onchange").attr("onchange","get_php_form_data(this.value+'**'+'uom_"+i+"','get_uom', 'requires/pi_controller_urmi')");
			}
			else if(category==5 || category==6 || category==7 || category==8 || category==9 || category==10 || category==11)
			{
				$('#itemdescription_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_item_desc("+i+")");
				$('#itemgroupid_'+i).val(0);
			}
			else if(category==24)
			{
				$('#lot_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_item_desc("+i+")");
			}
			else if(category==25)
			{
				$('#embellname_'+i).removeAttr("onchange").attr("onchange","load_drop_down('requires/pi_controller_urmi',this.value+'**'+0+'**'+'embelltype_"+i+"', 'load_drop_down_embelltype','embelltypeTd_"+i+"')");
				$('#row_'+i).find("td:eq(2)").removeAttr('id').attr('id','embelltypeTd_'+i);
			}
			else if(category==31)
			{
				$('#txtTestItem_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_test_item("+i+")");
				$('#amount_'+i).removeAttr("onKeyUp").attr("onKeyUp","check_amount("+i+")");
			}
			else
			{
				$('#construction_'+i).removeAttr("onDblClick").attr("onDblClick","openmypage_fabricDescription("+i+")");
				if(category==2) {$('#uom_'+i).val(0);}
				if(category==13) {$('#uom_'+i).val(12);}
			}

			$('#increase_'+i).removeAttr("value").attr("value","+");
			$('#decrease_'+i).removeAttr("value").attr("value","-");
			$('#increase_'+i).removeAttr("onclick").attr("onclick","add_break_down_tr("+i+");");
			$('#decrease_'+i).removeAttr("onclick").attr("onclick","fn_deleteRow("+i+");");*/
			
			
			set_all_onclick();
		}
	}

	function calculate_amount(i)
	{
		var ddd={ dec_type:5, comma:0, currency:''}
		math_operation( 'txtAmount_'+i, 'txtQty_'+i+'*txtRate_'+i, '*','',ddd);
		//calculate_total_amount(1);
	}

	/*function calculate_total_amount(type)
	{
		if(type==1)
		{
			var ddd={ dec_type:5, comma:0, currency:''}
			var numRow = $('table#tbl_pi_item tbody tr').length;
			//alert(numRow);
			math_operation( "txt_total_amount", "amount_", "+", numRow,ddd );
		}
		else
		{
		}

		var txt_total_amount=$('#txt_total_amount').val();
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();

		var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
		$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
	}*/

</script>


</head> 
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">																	
		<? echo load_freeze_divs ("../../",$permission); ?><br/>
		<fieldset style="width:1320px; margin-bottom:10px;">
			<form name="docsubmFrm_1" id="docsubmFrm_1" autocomplete="off" method="POST"  >
				<fieldset style="width:1318px;">
					<table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master"> 
						<tr>
							<td colspan="2">&nbsp;</td>
							<td width="130" align="right">System ID</td>
							<td width="170">
							  	<input style="width:140px " name="txt_sys_id" id="txt_sys_id" class="text_boxes" placeholder="Double Click to Update"  readonly="readonly" ondblclick="pop_loan();">
							  	<input type="hidden" id="hdn_update_id" name="hdn_update_id"/>
							</td>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td width="130" align="right" class="must_entry_caption">Company</td>
							<td width="170">
							<?
								echo create_drop_down( "cbo_company_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'requires/trims_order_receive_controller',this.value+'_'+document.getElementById('cbo_source_id').value, 'load_drop_down_customer_name', 'customer_td' );fnc_load_dtls_data(1);",0);
							?>
                        	</td>
                        	<td width="130" align="right" class="must_entry_caption">Within Group</td>
							<td width="170">
							<?
								echo create_drop_down( "cbo_source_id", 151,$yes_no,'', 1, 'Select',0,"load_drop_down( 'requires/trims_order_receive_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_customer_name', 'customer_td' );fnc_for_customer_source(this.value);",0);
							?>
                        	</td>
							<td width="130" align="right" class="must_entry_caption">Customer</td>
							<td width="170" id="customer_td">
							<?
								echo create_drop_down( "cbo_customer_id", 151, $blank_array,"", 1, "-- Select Customer --", $selected, "",0,"","","","");
							?>
							</td>
						</tr>
						<tr>
							<td width="130" align="right" class="must_entry_caption">WO No.</td>
							<td width="170">
							  	<input style="width:140px " name="txt_wo_no" id="txt_wo_no" class="text_boxes" disabled onDblClick="pop_work_order();" >
							  	<input type="hidden" name="txt_wo_id" id="txt_wo_id" class="text_boxes" readonly="readonly" >
							</td>
							<td width="130" align="right" class="must_entry_caption">Currency</td>
							<td width="170">
							<?
								echo create_drop_down( "cbo_currency_id", 151,$currency,'', 1, 'Select',0,"exchange_rate()",0);
							?>
                        	</td>
                        	<td width="130" align="right" class="must_entry_caption">Order Recv. Date</td>
							<td width="170">
							  	<input style="width:140px " name="txt_rcv_date" id="txt_rcv_date" class="datepicker" placeholder="Select Date" onChange="exchange_rate();" >
							</td>
						</tr>
							<td width="130" align="right" class="must_entry_caption">Exchange Rate</td>
							<td width="170">
							  	<input style="width:140px " name="txt_ex_rate" id="txt_ex_rate" class="text_boxes_numeric" value="1" readonly="readonly" >
							</td>
							<td width="130" align="right">Remarks</td>
							<td width="170">
							  	<input style="width:140px " name="txt_remarks" id="txt_remarks" class="text_boxes" >
							</td>
							<td width="130" align="right">Status</td>
							<td width="170">
							  	<?
								echo create_drop_down( "cbo_staus_id", 151,$row_status,'', 1, 'Select',0,"",0);
								?>
							</td>
	                    </tr>                                      
					</table>
				</fieldset>
				<fieldset>
                <legend>Order Details</legend>
                <table id="order_table" width="1310" class="rpt_table" rules="all">
                     <thead>
                        <tr>
                        	<th width="30" class="must_entry_caption">Sl</th>
                            <th width="100" class="must_entry_caption">Cust.Ord.No</th>
                            <th width="80" class="must_entry_caption">Cust.Style</th>
                            <th width="100" class="must_entry_caption">Cust.Buyer</th>
                            <th width="70" class="must_entry_caption">Section</th>
                            <th width="70" class="must_entry_caption">Item Group</th>
                            <th width="100" class="must_entry_caption">Item Descrip.</th>
                            <th width="60" class="must_entry_caption">Item Color</th>
                            <th width="60" class="must_entry_caption">Item Size</th>
                            <th width="60" class="must_entry_caption">UOM</th>
                            <th width="100" class="must_entry_caption">Order Qnty</th>
                            <th width="60" class="must_entry_caption">Rate</th>
                            <th width="100" class="must_entry_caption">Amount</th>
                            <th width="60" class="must_entry_caption">Rate Domestic</th>
                            <th width="100" class="must_entry_caption">Amnt. Domestic</th>
                            <th width="70" class="must_entry_caption">Delivery Date</th>
                            <th>Status</th>
                        </tr>    
                    </thead>
    				<tbody id="order_container" class="general">
                        <tr id="tr_1">
                        	<td width="30" ><input type="text" name="txtSl[]" id="txtSl_1" class="text_boxes_numeric" value="1" style="width:17px"/></td>
                        	<td width="100" ><input type="text" name="txtCustOrder[]" id="txtCustOrder_1" class="text_boxes" style="width:87px" /><input type="hidden" name="txtCustOrderId[]" id="txtCustOrderId_1" class="text_boxes_numeric" style="width:87px" value="" readonly /></td>
                            <td width="80" ><input type="text" name="txtCustStyle[]" id="txtCustStyle_1" class="text_boxes" style="width:67px" /></td>
                            <td width="100" id="buyerTd_1" ><?
								echo create_drop_down( "cboCustBuyer_1", 100, $blank_array,"", 1, "-- Select Buyer --", $selected,'',0,'','','','','','',"cboCustBuyer[]"); ?>	
							</td>
                            <td width="70" id="sectionTd_1" ><?
								echo create_drop_down( "cboSection_1", 70, $blank_array,"", 1, "-- Select Section --", $selected,'',0,'','','','','','',"cboSection[]"); ?>	
							</td>
                            <td width="70" id="groupTd_1"><?
								echo create_drop_down( "cboItemGroup_1", 70, $blank_array,"", 1, "-- Select Group --", $selected,'',0,'','','','','','',"cboItemGroup[]"); ?>	
							</td>
                            <td width="100" ><input type="text" name="txtItemDes[]" id="txtItemDes_1" class="text_boxes" style="width:87px"/></td>
                            <td width="60" ><input type="text" name="txtItemColor[]" id="txtItemColor_1" class="text_boxes" style="width:47px" /></td>
                            <td width="60" ><input type="text" name="txtItemSize[]" id="txtItemSize_1" class="text_boxes" style="width:47px" /></td>
                            <td width="60" ><?
								echo create_drop_down( "cboUom_1", 100, $unit_of_measurement,"", 1, "-- Select UOM --", $selected,'',0,'','','','','','',"cboUom[]"); ?>	
							</td>
                            <td width="100" ><input type="text" name="txtQty[]" id="txtQty_1" class="text_boxes_numeric" style="width:87px" onKeyUp="calculate_amount(1)" /></td>
                            <td width="60" ><input type="text" name="txtRate[]" id="txtRate_1" class="text_boxes_numeric" style="width:47px" onKeyUp="calculate_amount(1)" /></td>
                            <td width="100" ><input type="text" name="txtAmount[]" id="txtAmount_1" class="text_boxes_numeric" style="width:87px" readonly /></td>
                            <td width="60" ><input type="text" name="txtDomRate[]" id="txtDomRate_1" class="text_boxes_numeric" style="width:47px" readonly /></td>
                            <td width="100" ><input type="text" name="txtDomAmount[]" id="txtDomAmount_1" class="text_boxes_numeric" style="width:87px" readonly /></td>
                            <td width="70" ><input type="text" name="txtDelDate[]" id="txtDelDate_1" class="datepicker" style="width:57px" onChange="add_break_down_tr(1);" />
                            <td><?
								echo create_drop_down( "cboStausId_1", 50,$row_status,'', 1, 'Select',0,'',0,'','','','','','',"cboStausId[]"); ?>
								<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_1" class="text_boxes_numeric" style="width:87px" readonly />	
							</td>
                        </tr>
                    </tbody>
                </table>
                <table cellpadding="0" cellspacing="2" width="1310">
                    <tr>                       
                        <td align="center" colspan="16" valign="middle" class="button_container"><div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
                            <?
                                echo load_submit_buttons( $permission, "fnc_order_receive", 0,1 ,"reset_form('docsubmFrm_1','','','','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            
            <br /> 
           
        </form>
    </fieldset> 
</div>
</body> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>