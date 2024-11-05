<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish Fabric Issue return Entry
				
Functionality	:	
JS Functions	:
Created by		:	Md Didarul Alam
Creation date 	: 	15-10-2018
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
echo load_html_head_contents("Knit Finish Fabric Receive By Garments Info","../../", 1, 1, '','',''); 
?>	


<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function openmypage_issue_no() 
	{
		var page_link = 'requires/knit_finish_fabric_issue_return_controller.php?&action=issue_popup';
		var title = "Fabric Issue Pop-up";

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=965px,height=400px,center=1,resize=1,scrolling=0','../');		
		emailwindow.onclose = function () 
		{
			$("#txt_system_id").val('');
			$("#update_id").val('');
			$("#txt_issue_rtn_date").val('');
			
			var theform=this.contentDoc.forms[0];
			var issue_id=this.contentDoc.getElementById("hidden_issue_id").value;
			var issue_no=this.contentDoc.getElementById("hidden_issue_no").value;
			var fso_company = this.contentDoc.getElementById("hidden_fso_company").value;
			var po_company = this.contentDoc.getElementById("hidden_po_company").value;
			var issue_date = this.contentDoc.getElementById("hidden_issue_date").value;
			var hidden_fso_company_id = this.contentDoc.getElementById("hidden_fso_company_id").value;


			$("#text_issue_id").val(issue_id);
			$("#txt_issue_no").val(issue_no);
			$("#txt_fso_company").val(fso_company);
			$("#txt_po_company").val(po_company);
			$("#text_fso_company_id").val(hidden_fso_company_id);
			$("#txt_issue_date").val(issue_date);

			var responseData=return_global_ajax_value( hidden_fso_company_id, 'upto_variable_settings', '', 'requires/knit_finish_fabric_issue_return_controller');
			$("#store_update_upto").val(responseData);  


			var response=return_global_ajax_value( issue_id, 'check_previous_return', '', 'requires/knit_finish_fabric_issue_return_controller');
			var response=response.split("_");
			if(response[0]==1)
			{
				//alert('Issue return challan Found. \nchallan : '+response[1]);
				//return;
				$("#txt_system_id").val(response[1]);
				$("#update_id").val(response[2]);
				$("#txt_issue_rtn_date").val(response[3]);

				$("#txt_issue_no").attr('disabled','disabled');

				show_list_view(issue_id+"**"+response[2],'list_view_garments','list_view_container','requires/knit_finish_fabric_issue_return_controller','');

				set_button_status(1, permission, 'fnc_finish_issue_rtn_entry',1,0);
				return;
			}

			show_list_view(issue_id,'list_view_garments','list_view_container','requires/knit_finish_fabric_issue_return_controller','');	
		}
	}

	function fnc_finish_issue_rtn_entry( operation )
	{
		if (form_validation('txt_issue_rtn_date','Return Date')==false)
		{
			return;
		}

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(),'finish_fabric_receive_print','requires/knit_finish_fabric_issue_return_controller');
			show_msg("3");
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			//else 
			//{
				var dataString = '';
				var details_remarks = '';
				var returnQnty = '';
				var to_store = '';var to_floor = '';var to_room = '';var to_rack = '';var to_shelf = '';
				var hidden_dtls_id = ''; var hidden_transaction_id='';var previousQnty = ''; var product_ids = ''; var previousAmount = '';
				var j=1; var for_break_execution= 1; var blank_qnty_chk = 0;

				var floor_flag = 0; room_flag = 0; rack_flag = 0; shelf_flag = 0; 

				$('#tbl_list_search tbody tr').each(function() 
				{
					if(dataString=="")
					{	
						dataString = $("#hdden_data_"+j).val();
					}else{
						dataString += ","+$("#hdden_data_"+j).val();
					}

					var detailsRemarks = $("#text_dtls_remarks_"+j).val();
					var return_qnty = trim($("#text_return_qnty_"+j).val())*1;
					var issueQnty = trim($("#text_issue_qnty"+j).val())*1;

					

					if(return_qnty > 0)
					{
						blank_qnty_chk++;
					}

					if(return_qnty>issueQnty)
					{
						alert("Return quanty can not be greater than issue quanty ; ret= " + return_qnty+" > iss="+issueQnty);
						$("#text_return_qnty"+j).focus();
						for_break_execution ++;
						
					}

					details_remarks += '&details_remarks_'+j+'=' + detailsRemarks;
					returnQnty += '&return_qnty_'+j+'=' + return_qnty;
					previousQnty += '&previous_rtn_qnty_'+j+'=' + trim($("#previous_rtn_qnty_"+j).val())*1;
					previousAmount += '&previous_rtn_amount_'+j+'=' + trim($("#previous_rtn_amount_"+j).val())*1;



					hidden_transaction_id += '&hidden_transaction_id_'+j+'=' + $("#hidden_transaction_id_"+j).val();
					hidden_dtls_id += '&hidden_dtls_id_'+j+'=' + $("#hidden_dtls_id_"+j).val();

					to_store += '&to_store_'+j+'=' + $("#to_store_"+j).val();
					to_floor += '&to_floor_'+j+'=' + $("#to_floor_"+j).val();
					to_room += '&to_room_'+j+'=' + $("#to_room_"+j).val();
					to_rack += '&to_rack_'+j+'=' + $("#to_rack_"+j).val();
					to_shelf += '&to_shelf_'+j+'=' + $("#to_shelf_"+j).val();

					if(product_ids == "")
					{
						product_ids = $("#product_id_"+j).val();
					}else{
						product_ids += "," + $("#product_id_"+j).val();
					}
					
					// Store upto validation start
					var store_update_upto=$('#store_update_upto').val()*1;
					var floor=$(this).find('input[name="to_floor[]"]').val();
					var room=$(this).find('input[name="to_room[]"]').val();
					var rack=$(this).find('input[name="to_rack[]"]').val();
					var shelf=$(this).find('input[name="to_shelf[]"]').val();
					
					if(store_update_upto > 1)
					{
						if(store_update_upto==5 && (floor==0 || room==0 || rack==0 || shelf==0))
						{
							if(shelf_flag == 0)
							{
								shelf_flag = 1;
							}
						}
						else if(store_update_upto==4 && (floor==0 || room==0 || rack==0))
						{
							if(rack_flag == 0)
							{
								rack_flag = 1;
							}							
						}
						else if(store_update_upto==3 && floor==0 || room==0)
						{
							if(room_flag == 0)
							{
								room_flag = 1;
							}
						}
						else if(store_update_upto==2 && floor==0)
						{
							if(floor_flag == 0)
							{
								floor_flag = 1;
							}
						}
					}
					// Store upto validation End
					j++;
				});

				// Store upto validation start
				if(shelf_flag == 1)
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");return;
				}
				else if(rack_flag == 1)
				{
					alert("Up To Rack Value Full Fill Required For Inventory");return;
				}
				else if(room_flag == 1)
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(floor_flag == 1)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
				// Store upto validation End

				if(for_break_execution > 1)
				{
					return;
				}
				if(blank_qnty_chk == 0)
				{
					alert("select atleast one return quantity");
					$("#text_return_qnty_1").focus();
					return;
				}
				
				var update_id = $("#update_id").val();
				var txt_system_no = $("#txt_system_id").val();
				var received_dtls_id = $("#received_dtls_id").val();
				var trans_id = $("#trans_id").val();
				var proportion_id = $("#proportion_id").val();

				var fso_company_id = $("#text_fso_company_id").val();
				var issue_rtn_date = $("#txt_issue_rtn_date").val();
				var txt_issue_no = $("#txt_issue_no").val();
				var text_issue_id = $("#text_issue_id").val();

				

				var data="action=save_update_delete&operation="+operation+'&datas='+dataString+'&update_id='+update_id+'&txt_system_no='+txt_system_no+'&received_dtls_id='+received_dtls_id+'&trans_id='+trans_id+'&proportion_id='+proportion_id+'&fso_company_id='+fso_company_id+'&txt_issue_no='+txt_issue_no+'&text_issue_id='+text_issue_id+'&issue_rtn_date='+issue_rtn_date+details_remarks+returnQnty+to_store+to_floor+to_room+to_rack+to_shelf+hidden_dtls_id+hidden_transaction_id+previousQnty+previousAmount+'&product_ids='+product_ids;
				// alert(data);return;
				freeze_window(operation);
				http.open("POST","requires/knit_finish_fabric_issue_return_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fnc_finish_issue_rtn_entry_reponse;
				
			//}
		}

	}
	
	function fnc_finish_issue_rtn_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
			{
				if (reponse[0]==2) 
				{
					reset_form('finishFabricEntry_1','list_view_container','','','');
				}
				else
				{
					var update_id = document.getElementById('update_id').value = reponse[1];
					var txt_system_id = document.getElementById('txt_system_id').value = reponse[2];
					var trans_id = document.getElementById('trans_id').value = reponse[3];

					//var proportion_id = document.getElementById('proportion_id').value = reponse[4];
					//var received_dtls_id = document.getElementById('received_dtls_id').value = reponse[5];

					$("#txt_issue_no").attr('disabled','disabled');

					//show_list_view(update_id,'show_finish_fabric_listview','list_container_finishing','requires/knit_finish_fabric_issue_return_controller','');
					var text_issue_id = $("#text_issue_id").val();
					show_list_view(text_issue_id+"**"+update_id,'list_view_garments','list_view_container','requires/knit_finish_fabric_issue_return_controller','');
				}

				set_button_status(1, permission, 'fnc_finish_issue_rtn_entry',1,0);

			}
			release_freezing();
		}	
	}


	function openmypage_systemid()
	{		
		var cbo_company_id = $('#cbo_company_id').val();
		var title = 'System ID Info';
		var page_link = 'requires/knit_finish_fabric_issue_return_controller.php?action=systemId_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=1150px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var hidden_recv_id=this.contentDoc.getElementById("hidden_sys_id").value;
			var hidden_rcv_no=this.contentDoc.getElementById("hidden_sys_no").value;
			var hidden_issue_id=this.contentDoc.getElementById("hidden_issue_id").value;
			var hidden_issue_no=this.contentDoc.getElementById("hidden_issue_no").value;

			var hidden_fso_company_id=this.contentDoc.getElementById("hidden_fso_company_id").value;
			var hidden_fso_company=this.contentDoc.getElementById("hidden_fso_company").value;
			var hidden_po_company=this.contentDoc.getElementById("hidden_po_company").value;
			var hidden_issue_date=this.contentDoc.getElementById("hidden_issue_date").value;
			var hidden_issue_rtn_date=this.contentDoc.getElementById("hidden_issue_rtn_date").value;

			var responseData=return_global_ajax_value( hidden_fso_company_id, 'upto_variable_settings', '', 'requires/knit_finish_fabric_issue_return_controller');
			$("#store_update_upto").val(responseData);

			$("#txt_system_id").val(hidden_rcv_no);
			$("#update_id").val(hidden_recv_id);

			$("#text_issue_id").val(hidden_issue_id);
			$("#txt_issue_no").val(hidden_issue_no);

			$("#txt_fso_company").val(hidden_fso_company);
			$("#txt_po_company").val(hidden_po_company);
			$("#text_fso_company_id").val(hidden_fso_company_id);
			$("#txt_issue_date").val(hidden_issue_date);
			$("#txt_issue_rtn_date").val(hidden_issue_rtn_date);

			$("#txt_issue_no").attr('disabled','disabled');

			show_list_view(hidden_issue_id+"**"+hidden_recv_id,'list_view_garments','list_view_container','requires/knit_finish_fabric_issue_return_controller','');

			//show_list_view(hidden_recv_id,'show_finish_fabric_listview','list_container_finishing','requires/knit_finish_fabric_issue_return_controller','');

			set_button_status(1, permission, 'fnc_finish_issue_rtn_entry',1,1);
		}		
	}


	function calCulateAmount()
	{	
		var j=1;
		$('#tbl_list_search tbody tr').each(function() {
			
			var return_qnty = $("#text_return_qnty_"+j).val()*1;
			var rate =  $("#text_issue_order_rate"+j).val()*1;
 			var amount = (return_qnty*rate);
 			$("#text_issue_amooount"+j).text(number_format(amount,2,'.' , ""));

			j++;
		});
	}

</script> 
</head>

<body onLoad="set_hotkey()">

	<div style="width:100%;">
		<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
		<form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
			<div style="width:1030px; float:left;">   
				<fieldset style="width:1020px;">
					<legend>Finish Fabric Issue Return Entry</legend>
						<table cellpadding="0" cellspacing="2" width="1000" border="0">
							<tr>
								<td colspan="4" align="right"><strong>Issue Return ID</strong>
									<input type="hidden" name="update_id" id="update_id" />
									<input type="hidden" name="received_dtls_id" id="received_dtls_id" />
									<input type="hidden" name="trans_id" id="trans_id" />
									<input type="hidden" name="proportion_id" id="proportion_id" />
									<input type="hidden" name="store_update_upto" id="store_update_upto">
								</td>
								<td align="left" colspan="4">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
								</td>
							</tr>
                            
                            <tr>
								<td colspan="4" align="right">
									<strong>Issue No</strong>
								</td>
								<td align="left" colspan="4">
									<input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_issue_no();" readonly />
									<input type="hidden" name="text_issue_id" id="text_issue_id" />
								</td>
							</tr>
                            
                            
							<tr>
								<td colspan="8">&nbsp;</td>
							</tr>
                            
							<tr>
								<td class="">FSO Company</td>
								<td>
									<input type="text" name="txt_fso_company" id="txt_fso_company" class="text_boxes" style="width:120px;" placeholder="Display" readonly />
									 <input type="hidden" name="text_fso_company_id" id="text_fso_company_id" value="">
								</td>

                                <td class="must_entry_caption">Issue Return Date</td>
								<td>
                                   <input name="txt_issue_rtn_date" id="txt_issue_rtn_date" class="datepicker" style="width:120px;"  placeholder="Select Date" type="text">
								</td>
                                
                                <td class="">PO Company</td>
								<td>
									<input type="text" name="txt_po_company" id="txt_po_company" class="text_boxes"  style="width:120px;"  placeholder="Display" readonly />
								</td>

                                <td class="">Issue Date</td>
								<td>
                                   <input type="text" name="txt_issue_date" id="txt_issue_date" class="text_boxes"  style="width:120px;" readonly placeholder="Display" >
								</td>
  
							</tr>
                            
						</table>

					</fieldset>
			</div>
 
			<div id="list_view_container" style="width:1740px; padding-top:140px;">
				<!-- data load here -->
			</div>


			<table cellpadding="0" cellspacing="2" width="1740" border="0">
				<tr>
					<td class="button_container">
						<span style="margin-left: 300px !important;">
						<? 
						echo load_submit_buttons($permission, "fnc_finish_issue_rtn_entry", 0,0,"reset_form('finishFabricEntry_1','list_view_container','','','')",1);
						?>
						</span>
					</td>	  
				</tr>
			</table>

			<br clear="all" />
			</form>

			<div id="list_container_finishing" style="width:900px; padding-top:0px;"> </div>

		</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
