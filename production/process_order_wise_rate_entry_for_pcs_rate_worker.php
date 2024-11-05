<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Process Order Wise Rate Entry For Pcs Rate Worker

Functionality	:	
JS Functions	:
Created by		:	Kamrul Hasan
Creation date 	: 	08.01.2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/ 

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Process Order Wise Rate Entry For Pcs Rate Worker","../", 1, 1, "",'1','');
?>

<script>

	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function set_exchange_rate(currence)
	{	// 1 for TK.
		if(currence==1)
		{
			$('#txt_exchange_rate').val(1);
			$('#txt_exchange_rate').attr('readonly', 1);
		}
		else
		{
			$('#txt_exchange_rate').val('');
			$('#txt_exchange_rate').removeAttr("readonly");
		}
	}

	function openmypage_systemid()
	{ 
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'System ID Info';
			var page_link = 'requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';
			if( form_validation('cbo_company_id','Company Name')==false )
			{
				alert("Please Select Company Name");
				return;
			}	
		

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=900px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var mst_id=this.contentDoc.getElementById("hidden_mst_id").value;
				get_php_form_data(mst_id, "populate_price_rat_mst_form_data", "requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller" );
				show_list_view(mst_id,'show_dtls_listview','list_view_container','requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller','setFilterGrid(\'tbl_list_search\',-1)');
				set_button_status(1, permission, 'fnc_process_rate',1);
				
			}
	}
	
	function generate_report_file(data,action)
	{
		window.open("requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller.php?data=" + data+'&action='+action, true );
	}
	
	function fnc_process_rate( operation )
	{ 
		if(operation==4)
		{
			 generate_report_file($('#update_id').val(),'price_rate_wo_print');
			 return;
		}
		
		var fill_txt='update_id*dtls_id*txt_system_id*cbo_company_id*cbo_rate_category*txt_style_ref*cbo_process*cbo_uom*cbo_currency*txt_exchange_rate*cbo_status*txt_date*hidden_job_id*hidden_po_id';
		
		
		
		
		if( form_validation('cbo_company_id*cbo_rate_category*cbo_currency*txt_style_ref*txt_exchange_rate','Company Name*Rate Category*Currency*Rate')==false )
		{
			alert("Please Select Company Name Or RATE_CATEGORY Or CURRENCY Or STYLE_REF Or Rate ");
			return;
		}	
		
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(fill_txt,"../");
		
		  //alert (data);return;
	  freeze_window(operation);
	  http.open("POST","requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data);
	  http.onreadystatechange = fnc_process_rate_reponse;
	}
	
	function fnc_process_rate_reponse()
	{
		if(http.readyState == 4) 
		{
			console.log(http.responseText);
			var reponse=trim(http.responseText).split('**');
			  //alert(reponse[0]); release_freezing();return;
			show_msg(reponse[0]);
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				document.getElementById('dtls_id').value = reponse[3];
				set_button_status(0, permission, 'fnc_process_rate',1);				
				show_list_view(reponse[1],'show_dtls_listview','list_view_container','requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller','setFilterGrid(\'tbl_list_search\',-1)');

				reset_form('','','txt_style_ref*cbo_process*txt_exchange_rate*txt_date','','','');
			}
			else if(reponse[0]==2)
			{				
				show_list_view(reponse[1],'show_dtls_listview','list_view_container','requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller','setFilterGrid(\'tbl_list_search\',-1)');
				reset_form('','','cbo_rate_category*txt_style_ref*cbo_process*cbo_uom*cbo_currency*txt_exchange_rate*cbo_status*txt_date','','','');
			}
			document.getElementById("cbo_company_id").disabled = true;
			// document.getElementById("txt_system_id").disabled = true;



			release_freezing();
		}
		release_freezing();
	}

 /*-----------------------------------------------------------------------------------------------------------*/	function fnc_load_from_dtls(id)
	{
		//alert(id); return;
		get_php_form_data(id,'populate_input_form_data','requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller');
	}	
	
	
	function open_style_ref()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			alert("Please Select Company Name ");
			return;
		}	
		var company = $("#cbo_company_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year=$("#cbo_year").val();
		var page_link='requires/process_order_wise_rate_entry_for_pcs_rate_worker_controller.php?action=style_popup&company='+company;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=390px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			let hide_po_id=this.contentDoc.getElementById("hide_po_id").value;
			let hide_po_no=this.contentDoc.getElementById("hide_po_no").value;
			let hide_job_id=this.contentDoc.getElementById("hide_job_id").value.split('*');
			
			$("#txt_style_ref").val(hide_po_no);
			$("#hidden_job_id").val(hide_job_id[0]); 
			$("#hidden_po_id").val(hide_po_id); 
			}
	}
	
</script>
</head>
	<body onLoad="set_hotkey()">
	 <div style="width:100%;" align="center">

		<? echo load_freeze_divs ("../",$permission); ?>
			<form name="priceRateEntry_1" id="priceRateEntry_1" autocomplete="off" >
				<div style="width:1050px; float:left;" align="center">   
					<fieldset style="width:1050px;">
					 <legend>Process Order Wise Rate Entry For Pcs Rate Worker</legend>
						<table cellpadding="0" cellspacing="2" width="1050" border="0">
								<tr>
									<td colspan="4" align="right"><strong>System ID</strong>
									<input  type="hidden" name="update_id" id="update_id" />
									<input  type="hidden" name="dtls_id" id="dtls_id" />
								    </td>
									<td colspan="4" align="left">
										<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
									</td>
								</tr>
								<tr>
									<td colspan="8">&nbsp;</td>
								</tr>
							<tr>
								<td class="must_entry_caption">Company Name</td>
									<td>
										<?
											echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Company--", 0, " "); 
										
										?>
									</td>
									<td class="must_entry_caption">Rate Category/ For:</td>
									<td>
										<?
											echo create_drop_down("cbo_rate_category", 160, $rate_category_array,"", 1,"-- Select Rate Category --", 1,"");
										
										?>
									</td>
									<td>Order:</td>
									<td>
									
									<input type="text" id="txt_style_ref"  name="txt_style_ref"  style="width:150px" class="text_boxes" onDblClick="open_style_ref()" placeholder="Browse" readonly  />
										<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
										<input type="hidden" id="hidden_po_id"  name="hidden_po_id" />
									
									</td>
									
									<td >Process</td>
									<td >

										<?
											echo create_drop_down("cbo_process", 160, $process_array,"", 1,"-- Select Process--", 1,"");

											?>
									</td>
									
								
									
									</tr>
									<tr>
									<td >UOM</td>
									<td>
									<? 
											echo create_drop_down("cbo_uom", 160, $unit_of_measurement,"", 1,"-- Select  --", 1,"");
										?>

									</td>
									
									<td class="must_entry_caption">Currency</td>
									<td>

										<? 
											echo create_drop_down("cbo_currency", 160, $currency,"", 1,"-- Select Currency --", 1,"");
										?>
									</td>
									
									<td class="must_entry_caption">Rate</td>
									<td>

										<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:150px;" maxlength="20" title="Maximum 5 Character"   />
									</td>

								

									<td > Status:</td>
									
									<td >
									<? 
										
										echo create_drop_down( "cbo_status",160,$row_status,"",1, "-- Select  --",  1,"" );
									?>
								</td>
								
								

							</tr>
							<tr>
								<td > Entry Date:</td>
									<td>
										<input style="width:160px;" type="text"   class="datepicker" autocomplete="off" value="<? echo date("d-m-Y",time()); ?>" name="txt_date" id="txt_date"  />
								</td>
							</tr>
													
							
							<tr>
								<td align="center" colspan="9" class="button_container">
									<?
										echo load_submit_buttons($permission, "fnc_process_rate", 0,1,"reset_form('priceRateEntry_1','list_view_container','','cbo_production_basis,5','disable_enable_fields(\'cbo_company_id\');set_production_basis();set_auto_complete();')",1);
									?>
								</td>
							</tr>
							
						</table>
						
					</fieldset>
					<div id="list_view_container"></div>
					
				</div>
			</form>

	 </div>
	</body>
	<script src="../includes/functions_bottom.js" type="text/javascript"></script>
	</html>

