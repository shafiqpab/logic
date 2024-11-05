<?
/*-------------------------------------------- Comments
Purpose			: 	This form will createFabric AOP Multi Issue Challan Info
				
Functionality	:	
JS Functions	:
Created by		:	Md Abu Sayed
Creation date 	: 	10-03-2022
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
echo load_html_head_contents("Fabric AOP Multi Issue Challan Info","../", 1, 1, '','',''); 
?>	


<script>
	<?
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][522] );
    echo "var field_level_data= ". $data_arr . ";\n";
    ?>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var str_gate_pass = [<? echo substr(return_library_autocomplete( "select gate_pass_no from pro_fin_deli_multy_challan_mst group by gate_pass_no", "gate_pass_no" ), 0, -1); ?>];
	var str_dl_no = [<? echo substr(return_library_autocomplete( "select dl_no from pro_fin_deli_multy_challan_mst group by dl_no", "dl_no" ), 0, -1); ?>];
	var str_vehicle_no = [<? echo substr(return_library_autocomplete( "select vehicle_no from pro_fin_deli_multy_challan_mst group by vehicle_no", "vehicle_no" ), 0, -1); ?>];
	var str_driver_name = [<? echo substr(return_library_autocomplete( "select driver_name from pro_fin_deli_multy_challan_mst group by driver_name", "driver_name" ), 0, -1); ?>];
	var str_transport = [<? echo substr(return_library_autocomplete( "select transport from pro_fin_deli_multy_challan_mst group by transport", "transport" ), 0, -1); ?>];
	var str_mobile_no = [<? echo substr(return_library_autocomplete( "select mobile_no from pro_fin_deli_multy_challan_mst group by mobile_no", "mobile_no" ), 0, -1); ?>];

	$(document).ready(function(e)
	{
		$("#txt_gate_pass_no").autocomplete({
			source: str_gate_pass
		});

		$("#txt_dl_no").autocomplete({
			source: str_dl_no
		});

		$("#txt_vehicle_no").autocomplete({
			source: str_vehicle_no
		});

		$("#txt_driver_name").autocomplete({
			source: str_driver_name
		});

		$("#txt_transport").autocomplete({
			source: str_transport
		});

		$("#txt_mobile_no").autocomplete({
			source: str_mobile_no
		});
		
	});

	function openmypage_fso() 
	{
		var cbo_delivery_name = $('#cbo_delivery_name').val();
		var title = 'Fabric Sale Order Form';
		var cbo_company_id = $('#cbo_company_id').val();
		var cbo_issue_purpose = $('#cbo_issue_purpose').val();
		var cbo_service_source = $('#cbo_service_source').val();

		if( form_validation('cbo_company_id*cbo_issue_purpose*cbo_delivery_name','Company Name*Issue Purpose*Delivery To')==false )
		{
			return;
		}
		
		var page_link = 'requires/fabric_aop_multi_issue_challan_controller.php?cbo_company_id='+cbo_company_id+'&cbo_issue_purpose='+cbo_issue_purpose+'&cbo_service_source='+cbo_service_source+'&cbo_delivery_name='+cbo_delivery_name+'&action=fabric_sales_order_popup';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1450px,height=400px,center=1,resize=1,scrolling=0','');		
		emailwindow.onclose = function () 
		{
			var theform=this.contentDoc.forms[0];

			var challan_id=this.contentDoc.getElementById("txt_selected_id").value;          
       		var sales_order_no=this.contentDoc.getElementById("txt_selected").value;
       		var selected_fso=this.contentDoc.getElementById("txt_selected_fso").value;

       		$("#hdn_challan_id").val(challan_id);
			$("#hdn_fso_id").val(selected_fso);
			$("#txt_fso_no").val(sales_order_no);	
			fnc_show_garments();		
		}
	}

	function partyChange()
	{
		$("#txt_fso_no").val('');
		$("#hdn_fso_id").val('');
		$("#list_view_container").html('');
	}

	function fnc_show_garments()
	{

		if( form_validation('cbo_company_id*cbo_delivery_name*txt_fso_no','Company Name*Delivery To*FSO No')==false )
		{
			return;
		}	

		var cbo_company_id		= $("#cbo_company_id").val();
		var cbo_delivery_name 	= $("#cbo_delivery_name").val();
		var txt_vehical_no 		= $("#txt_vehical_no").val(); 
		var txt_fso_no 			= $("#txt_fso_no").val();
		var hdn_fso_id 			= $("#hdn_fso_id").val();
		var txt_po_job 			= $("#txt_po_job").val();
		var txt_dl_no 			= $("#txt_po_job").val();
		var txt_transport 		= $("#txt_transport").val();
		var txt_mobile_no 		= $("#txt_mobile_no").val();
		var txt_gate_pass_no 	= $("#txt_gate_pass_no").val();
		var txt_remarks 		= $("#txt_remarks").val();
		var update_id			= $("#update_id").val();
		var hdn_challan_id		= $("#hdn_challan_id").val();
		//alert(hdn_fso_id+'_'+txt_po_job+'_'+txt_dl_no);
		var dataString = "&cbo_company_id="+cbo_company_id+"&cbo_delivery_name="+cbo_delivery_name+"&txt_vehical_no="+txt_vehical_no+"&txt_fso_no="+txt_fso_no+"&hdn_fso_id="+hdn_fso_id+"&hdn_challan_id="+hdn_challan_id+"&txt_po_job="+txt_po_job+"&txt_dl_no="+txt_dl_no+"&txt_transport="+txt_transport+"&txt_mobile_no="+txt_mobile_no+"&txt_gate_pass_no="+txt_gate_pass_no+"&txt_remarks="+txt_remarks+"&update_id="+update_id;
		
		var data="action=list_view_garments"+dataString;
		
		freeze_window(3);
		
		http.open("POST","requires/fabric_aop_multi_issue_challan_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_garments_reponse;
		
	}

	function fnc_show_garments_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText);
			$('#list_view_container').html(response);
			release_freezing();	 
		}
	}
			

	function fnc_Fabric_aop_multi_entry( operation )
	{

		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			if( form_validation('txt_system_id','System id')==false )
			{
				return;
			}
			generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_system_id').val(),'fabric_aop_multi_issue_print','requires/fabric_aop_multi_issue_challan_controller');
			show_msg("3");
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}
			else 
			{*/
				if( form_validation('cbo_company_id*cbo_issue_purpose*cbo_delivery_name*cbo_service_source*txt_multi_challan_date','Company Name*Issue Purpose*Delivery To*Service Source*Multi Challan Date')==false )
				{
					return;
				}
				else 
				{
					var update_id			= $("#update_id").val();
					var txt_system_id		= $("#txt_system_id").val();
					var company_id			= $("#cbo_company_id").val();
					var vehicle_no 			= $("#txt_vehicle_no").val();
					var driver_name 		= $("#txt_driver_name").val();
					var dl_no 				= $("#txt_dl_no").val(); 
					var transport 			= $("#txt_transport").val();
					var mobile_no 			= $("#txt_mobile_no").val();
					var gate_pass_no 		= $("#txt_gate_pass_no").val();
					var remarks 			= $("#txt_remarks").val();
					var delivery_to 		= $("#cbo_delivery_name").val();
					var delivery_address 	= $("#txt_delivery_address").val();
					var issue_purpose 		= $("#cbo_issue_purpose").val();
					var service_source 		= $("#cbo_service_source").val();
					var multi_challan_date 	= $("#txt_multi_challan_date").val();


				    var chkArray = [];
					/* look for all checkboes that have a parent id called 'checkboxlist' attached to it and check if it was checked */
					 var details_remarks = '';
					 var j=1;
					$('#tbl_list_search tbody tr input:checked').each(function() {
						chkArray.push($(this).val());
						
						var detailsRemarks = $("#text_dtls_remarks_"+j).val();

						details_remarks += '&details_remarks_'+j+'=' + detailsRemarks;

						j++;
					});
					
					/* we join the array separated by the comma */
					var detailsData;
					detailsData = chkArray.join('___') ;

					var selected_detailsRow = chkArray.length;

					if(selected_detailsRow<1)
					{
						alert('Select at least one row first');
						return;
					}

					var dataString = "&company_id="+company_id+"&driver_name="+driver_name+"&vehicle_no="+vehicle_no+"&dl_no="+dl_no+"&transport="+transport+"&mobile_no="+mobile_no+"&gate_pass_no="+gate_pass_no+"&remarks="+remarks+"&detailsData="+detailsData+"&update_id="+update_id+"&txt_system_id="+txt_system_id +"&delivery_to="+delivery_to +"&delivery_address="+delivery_address+"&issue_purpose="+issue_purpose+"&service_source="+service_source+"&multi_challan_date="+multi_challan_date;
				
					var data="action=save_update_delete&operation="+operation+dataString+details_remarks;
					//alert(data);return;
					freeze_window(operation);

					http.open("POST","requires/fabric_aop_multi_issue_challan_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fnc_Fabric_aop_multi_entry_reponse;
				}
			//}
		}
	}

	function fnc_Fabric_aop_multi_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');

			if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
			{
				if (reponse[0]==2) 
				{
					reset_form('aopFabricEntry_1','list_view_container','','','');
				}
				else
				{
					var update_id = document.getElementById('update_id').value = reponse[1];
					var txt_system_id = document.getElementById('txt_system_id').value = reponse[2];

					show_msg(reponse[0]);
				}
				set_button_status(1, permission, 'fnc_Fabric_aop_multi_entry',1,1);
			}
			else if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			release_freezing();
		}	
	}


	function openmypage_systemid()
	{
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{
			var cbo_company_id = $('#cbo_company_id').val();
			var title = 'System ID Info';
			var page_link = 'requires/fabric_aop_multi_issue_challan_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=820px,height=390px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_sys_id=this.contentDoc.getElementById("hidden_sys_id").value;
				var hidden_sys_no=this.contentDoc.getElementById("hidden_sys_no").value;

				$("#txt_system_id").val(hidden_sys_no);
				$("#update_id").val(hidden_sys_id);

				get_php_form_data(hidden_sys_id, "populate_data_from_finish_fabric", "requires/fabric_aop_multi_issue_challan_controller" );
			
				fnc_show_garments();
				set_button_status(1, permission, 'fnc_Fabric_aop_multi_entry',1,1);
			}
		}
	}


	function generate_report_file(data,action,page)
	{
		window.open("requires/fabric_aop_multi_issue_challan_controller.php?data=" + data+'&action='+action, true );
	}	

    
	function check_all(tot_check_box_id)
	{
		if ($('#'+tot_check_box_id).is(":checked"))
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', true);
			});
		}
		else
		{ 
			$('#tbl_list_search tbody tr').each(function() {
				$('#tbl_list_search tbody tr input:checkbox').attr('checked', false);
			});
		} 
	}

	function reset_service_source()
	{
		$("#update_id").val('');
		$("#txt_system_id").val('');
		$('#txt_system_id').val('');
		$("#txt_fso_no").val('');
		$("#hdn_fso_id").val('');
		$("#hdn_challan_id").val('');
		$("#txt_delivery_address").val('');
		$("#list_view_container").html('');
	}

    function fnc_load_address(val){
        var response=return_global_ajax_value(val+'_'+$("#cbo_service_source").val(), 'return_deli_com_address', '', 'requires/fabric_aop_multi_issue_challan_controller');
        $('#txt_delivery_address').val(response);
    }

   

</script> 
</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;">
		<? echo load_freeze_divs ("../",$permission);  ?><br />    		 
		<form name="aopFabricEntry_1" id="aopFabricEntry_1" autocomplete="off" >
			<div style="width:930px; float:left;">   
				<fieldset style="width:920px;">
					<legend>Finish Fabric Receive Entry</legend>
						<table cellpadding="0" cellspacing="2" width="810" border="0">
							<tr>
								<td colspan="3" align="right"><strong>System ID</strong>
									<input type="hidden" name="update_id" id="update_id" />
								</td>
								<td colspan="3" align="left">
									<input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemid();" readonly />
								</td>
							</tr>
							<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td class="must_entry_caption">Company Name</td>
								<td>
								<?
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
								?>
								</td>
                                
                                <td class="must_entry_caption">Issue Purpose</td>
								<td align="center">
                                    <? echo create_drop_down( "cbo_issue_purpose", 160, $yarn_issue_purpose,"", 1, "-- Select Source --", "52", "","",52); ?>
								</td>
                                
                                <td class="must_entry_caption">Multi Challan Date</td>
                                <td width="160"><input class="datepicker" type="text" style="width:160px" name="txt_multi_challan_date" id="txt_multi_challan_date" value="<? echo date("d-m-Y")?>" /></td>
  
							</tr>
                            
							<tr>
                                <td>Service Source</td>                                              
								<td > 
                                <?
									echo create_drop_down("cbo_service_source", 160, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/fabric_aop_multi_issue_challan_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_delivery_com','delivery_td');reset_service_source()","","","","","2");
									?>
								</td>
                               
								<td>Delivery To</td>   
                                <td id="delivery_td" align="center">
									<?
									echo create_drop_down("cbo_delivery_name", 160, $blank_array,"", 1,"-- Select Delivery To --", 0,"partyChange()");
									?>
								</td> 
								<td>Delivery Address</td>                                              
								<td width="160" > 
									<input type="text" name="txt_delivery_address" id="txt_delivery_address" class="text_boxes" style="width:160px;" disabled="disabled">
								</td>
                            </tr>
                            <tr> 
                            	<td class="must_entry_caption">FSO No</td>
								<td>
									<input type="text" name="txt_fso_no" id="txt_fso_no" class="text_boxes" style="width:150px;" placeholder="Browse" onDblClick="openmypage_fso();" readonly />
									<input type="hidden" name="hdn_fso_id" id="hdn_fso_id" class="text_boxes" value="" />
									<input type="hidden" name="hdn_challan_id" id="hdn_challan_id" class="text_boxes" value="" />
								</td>
                                
                                <td>Vehical No</td>
								<td align="center">                                
                                    <input type="text" name="txt_vehicle_no" id="txt_vehicle_no" class="text_boxes" style="width:150px;" autocomplete="on">
								</td>
                               <td>Driver Name</td>                                              
							   <td> 
									<input type="text" name="txt_driver_name" id="txt_driver_name" class="text_boxes" style="width:160px;" autocomplete="on">
								</td> 

							</tr>
                            
                            <tr>
								
                                <td>DL No</td>                                              
								<td> 
									<input type="text" name="txt_dl_no" id="txt_dl_no" class="text_boxes" style="width:150px;" autocomplete="on">
								</td> 
                                <td>Transport</td>                                              
								<td align="center"> 
									<input type="text" name="txt_transport" id="txt_transport" class="text_boxes" style="width:150px;" autocomplete="on">
								</td> 
                                
                                <td>Mobile No</td>           
                                <td > 
									<input type="text" name="txt_mobile_no" id="txt_mobile_no" class="text_boxes" style="width:160px;" autocomplete="on">
								</td>
                                 
							</tr>    
                            
                            <tr>
                            	<td>Gate Pass No</td>           
                                <td> 
									<input type="text" name="txt_gate_pass_no" id="txt_gate_pass_no" class="text_boxes" style="width:150px;" autocomplete="on" >
								</td>
								<td>Remarks</td>                                              
								<td colspan="3" > 
									<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:404px;margin-left:10px ">
								</td> 
                                
							</tr>  

						</table>
					</fieldset>
                    
			</div>
 
			<div id="list_view_container" style="width:1100px; padding-top:165px;"></div>
            
            <table cellpadding="0" cellspacing="2" width="1100" border="0">
                <tr>
                	<td colspan="6" align="center" class="button_container">
                		<? 
                		echo load_submit_buttons($permission, "fnc_Fabric_aop_multi_entry", 0,1,"reset_form('aopFabricEntry_1','list_view_container','','','')",1);
                		?>
                	</td>	  
                </tr>
                
            </table>
            
			<br clear="all" />
		</form>
	</div>    
</body>  
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
