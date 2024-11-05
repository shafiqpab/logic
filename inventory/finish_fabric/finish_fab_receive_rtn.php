<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish fabric receive return
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	29-10-2014
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
echo load_html_head_contents("Finish Fabric Receive Return Info","../../", 1, 1, $unicode,1,1); 
$con = connect();
?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/finish_fab_receive_rtn_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split("_"); // mrr number
  		// master part call here
		get_php_form_data(returnNumber[0], "populate_master_from_data", "requires/finish_fab_receive_rtn_controller"); 
		get_php_form_data(returnNumber[2], "populate_data_from_data", "requires/finish_fab_receive_rtn_controller");  
		//list view call here
		show_list_view(returnNumber[0],'show_dtls_list_view','list_container_yarn','requires/finish_fab_receive_rtn_controller','');
		set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
		//$("#tbl_master").find('input,select').attr("disabled", true);	
		$("#tbl_child").find('input,select').val('');
		disable_enable_fields( 'txt_return_no', 0, "", "" ); // disable false
 	}
}



function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/finish_fab_receive_rtn_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_"); // mrr number.
  		// master part call here
		$("#pi_id").val('');
		$("#txt_pi_no").val('');
		
		get_php_form_data(mrrNumber[0], "populate_data_from_data", "requires/finish_fab_receive_rtn_controller");  
 		$("#tbl_child").find('input,select').val('');
 		$("#txt_is_sales").val(mrrNumber[2]);
 	}
}
 



//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input,select').attr("disabled", false);	
	set_button_status(0, permission, 'fnc_yarn_receive_entry',1);
	reset_form('yarn_receive_return_1','list_container_yarn*list_product_container','','','','');
}

// popup for PI----------------------	
function openmypage_pi()
{
	if( form_validation('cbo_company_id*txt_mrr_no','Company Name*MRR No')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();
	 
	page_link='requires/finish_fab_receive_rtn_controller.php?action=pi_popup&company='+company;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'PI Search', 'width=850px, height=370px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var piID=this.contentDoc.getElementById("hidden_tbl_id").value; // pi table id
		var piNumber=this.contentDoc.getElementById("hidden_pi_number").value; // pi number
		
		$("#pi_id").val(piID);
		$("#txt_pi_no").val(piNumber);
	}		
}

function openmypage_rtn_qty()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var txt_received_id = $('#txt_received_id').val();
	var txt_prod_id = $('#txt_prod_id').val();
	var hidden_receive_trans_id= $('#hidden_receive_trans_id').val();
	var update_id = $('#update_id').val();
	var txt_break_qnty = $('#txt_break_qnty').val();
	var txt_break_roll = $('#txt_break_roll').val();
	var txt_booking_no = $('#txt_booking_no').val();
	var txt_cons_rate =  $('#txt_cons_rate').val()*1;
	var txt_order_rate =  $('#txt_order_rate').val()*1;
	var txt_yet_to_issue =  $('#txt_yet_to_issue').val()*1;


	var txt_dia_width_type = $('#txt_dia_width_type').val();
	var cbo_body_part = $('#cbo_body_part').val();
	var txt_fabric_shade = $('#txt_fabric_shade').val();
	var batch_id = $('#hidden_batch_id').val();
	var cbo_store_name = $('#cbo_store_name').val();
	var cbo_floor = $('#cbo_floor').val();
	var cbo_room = $('#cbo_room').val();
	var txt_rack = $('#txt_rack').val();
	var txt_shelf = $('#txt_shelf').val();
	
	
	if (form_validation('cbo_company_id*txt_received_id*txt_prod_id','Company*Receive MRR*Item Description')==false)
	{
		return;
	}
	var title = 'Receive Info';	
	var page_link = 'requires/finish_fab_receive_rtn_controller.php?cbo_company_id='+cbo_company_id+'&txt_received_id='+txt_received_id+'&txt_prod_id='+txt_prod_id+'&hidden_receive_trans_id='+hidden_receive_trans_id+'&txt_break_qnty='+txt_break_qnty+'&txt_break_roll='+txt_break_roll+'&txt_booking_no='+txt_booking_no+'&update_id='+update_id+'&txt_yet_to_issue='+txt_yet_to_issue+'&txt_dia_width_type='+txt_dia_width_type+'&cbo_body_part='+cbo_body_part+'&txt_fabric_shade='+txt_fabric_shade+'&batch_id='+batch_id+'&cbo_store_name='+cbo_store_name+'&cbo_floor='+cbo_floor+'&cbo_room='+cbo_room+'&txt_rack='+txt_rack+'&txt_shelf='+txt_shelf+'&action=return_po_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var tot_qnty=this.contentDoc.getElementById("tot_qnty").value;	 //Access form field with id="emailfield"
		var break_qnty=this.contentDoc.getElementById("break_qnty").value; //Access form field with id="emailfield"
		var break_roll=this.contentDoc.getElementById("break_roll").value; //Access form field with id="emailfield"
		var break_order_id=this.contentDoc.getElementById("break_order_id").value; //Access form field with id="emailfield"
		var tot_roll=this.contentDoc.getElementById("tot_roll").value; //Access form field with id="emailfield"
		//alert(tot_qnty);return;
		var totalAmount = (txt_order_rate*tot_qnty);
		$("#txt_amount").val(totalAmount);
		$('#txt_return_qnty').val(tot_qnty);
		$('#txt_break_qnty').val(break_qnty);
		$('#txt_break_roll').val(break_roll);
		$('#txt_order_id_all').val(break_order_id);
		$('#txt_roll').val(tot_roll);
	}
}
	
	

function fnc_yarn_receive_return_entry(operation)
{
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_id').val()+'*'+$('#issue_mst_id').val()+'*'+report_title, "yarn_receive_return_print", "requires/finish_fab_receive_rtn_controller" ) 
		 return;
	}
	/*else if(operation==2)
	{
		show_msg('13');
		return;
	}
	else
	{*/
		if( form_validation('cbo_company_id*txt_return_date*txt_mrr_no*cbo_return_to*txt_item_description*txt_return_qnty','Company Name*Return Date*MRR Number*Return To*Item Description*Return Quantity')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_return_date').val(), current_date)==false)
		{
			alert("Receive Return Date Can not Be Greater Than Current Date");
			return;
		}
		
		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][287]);?>')
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][287]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][287]);?>')==false)
			{
				
				return;
			}
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_shelf').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && cbo_floor==0 || cbo_room==0)
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && cbo_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End

		var dataString = "txt_return_no*cbo_company_id*txt_return_date*txt_received_id*txt_mrr_no*cbo_return_to*txt_pi_no*pi_id*txt_item_description*txt_prod_id*txt_return_qnty*cbo_uom*txt_break_qnty*txt_break_roll*txt_order_id_all*txt_remarks*txt_roll*hidden_receive_trans_id*before_prod_id*update_id*issue_mst_id*prev_return_qnty*before_receive_trans_id*txt_global_stock*hidden_batch_id*cbo_body_part*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*txt_is_sales*txt_dia_width_type*txt_fabric_shade*txt_color_id*update_details_id*txt_order_rate*txt_cons_rate*txt_booking_no*txt_aop_rate";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		freeze_window(operation);
		http.open("POST","requires/finish_fab_receive_rtn_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_receive_return_entry_reponse;
	//}
}

function fnc_yarn_receive_return_entry_reponse()
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
		
		else if(reponse[0]==30)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		} 
		 		
		else if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			show_msg(reponse[0]);
			if (reponse[0]==2 && reponse[3]==1) // is mst delete reset form
			{
				reset_form('grey_fab_receive_rtn_1','list_container_yarn*list_product_container','','','disable_enable_fields(\'cbo_company_id\');');
			}
			else
			{
				$("#txt_return_no").val(reponse[1]);
				$("#issue_mst_id").val(reponse[2]);
	 			$("#tbl_master :input").attr("disabled", true);
				disable_enable_fields( 'txt_return_no', 0, "", "" ); // disable false
				show_list_view(reponse[2],'show_dtls_list_view','list_container_yarn','requires/finish_fab_receive_rtn_controller','');	

				var rcv_id = $("#txt_received_id").val();
				show_list_view(rcv_id,'show_product_listview','list_product_container','requires/finish_fab_receive_rtn_controller','');
			}					

			//child form reset here after save data-------------//
			set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
			$("#tbl_child").find('input,select').val('');
			release_freezing();
		}
		else if(reponse[0]==10)
		{
			show_msg(reponse[0]);
			release_freezing();
			return;
		}
	}
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="grey_fab_receive_rtn_1" id="grey_fab_receive_rtn_1" autocomplete="off" > 
    <div style="width:840px;">       
    <table width="840" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="840" align="center" valign="top">   
            	<fieldset style="width:830px; float:left;">
                <legend>Finish Fabric Receive Return</legend>
                <br />
                 	<fieldset style="width:830px;">                                       
                        <table  width="800" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="3" align="right"><b>Return Number</b></td>
                           		<td colspan="3" align="left">
                                <input type="text" name="txt_return_no" id="txt_return_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
                                <input type="hidden" id="issue_mst_id" name="issue_mst_id" >
                                </td>
                   		   </tr>
                           <tr>
                           		<td colspan="6" align="center">&nbsp;</td>
                            </tr>
                           <tr>
                                <td  width="120" align="right" class="must_entry_caption">Company Name </td>
                                <td width="170">
									<? 
                                     	echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'load_variable_settings','requires/finish_fab_receive_rtn_controller');load_room_rack_self_bin('requires/finish_fab_receive_rtn_controller*2', 'store','store_td', this.value);" );
                                    ?>
                                </td>
                                <td width="120" align="right" class="must_entry_caption">Return Date</td>
                                <td width="170"><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:160px;" placeholder="Select Date" /></td>
                                <td width="120" align="right" class="must_entry_caption">MRR NO</td>
                                <td width="160" >
                                <input class="text_boxes" type="text" name="txt_mrr_no" id="txt_mrr_no" style="width:160px;" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly  />
                                <input type="hidden" name="txt_received_id" id="txt_received_id" />
                                <input type="hidden" name="txt_is_sales" id="txt_is_sales" />
                                </td>
                          </tr>
                            <tr>
                                <td width="130" align="right" class="must_entry_caption">Returned To</td>
                                <td width="170" id="knitting_com">
									<? 
										$blank_arr=array();
										echo create_drop_down( "cbo_return_to", 170, $blank_arr,"", 1, "-- Select --", 0, "",1 ); 
									?>
                               	</td>
                                <td align="right">PI NO </td>
                                <td>
                                	<input class="text_boxes" type="text" name="txt_pi_no" id="txt_pi_no" onDblClick="openmypage_pi()" placeholder="Double Click To Search" style="width:160px;" readonly />
                                    <input class="text_boxes" type="hidden" name="pi_id" id="pi_id" disabled/>
                                </td>
                                <td align="right">&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    <table cellpadding="0" cellspacing="1" width="840" id="tbl_child">
                     <tr>
                   	   <td width="460" valign="top" align="center">
                         <fieldset style="width:450px; float:left">  
                                <legend>Return Item Info</legend>                                     
                                  <table  width="450" cellspacing="2" cellpadding="0" border="0">
                                          <tr>
                                               <td align="right" class="must_entry_caption">Fabric Description&nbsp;&nbsp;</td>
                                               <td colspan="3">
                                               		<input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:317px;" placeholder="Display" readonly disabled/>
                                                    <input type="hidden" id="txt_prod_id" name="txt_prod_id" />
                                                    <input type="hidden" id="txt_dia_width_type" name="txt_dia_width_type" />
                                                    <input type="hidden" id="txt_fabric_shade" name="txt_fabric_shade" />
                                               </td> 
                                          </tr>
                                           <tr>
                                               <td align="right">Batch No.&nbsp;&nbsp;</td>
                                               <td>
                                                    <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:110px;" placeholder="Dispaly" disabled/>
                                                    <input type="hidden" id="hidden_batch_id" name="hidden_batch_id" readonly disabled  />
                                               </td>
                                               <td width="100" align="right">Store Name&nbsp;&nbsp;</td>
                                               	<td id="store_td">
                                               	 <? 
                                               	 	echo create_drop_down( "cbo_store_name", 140, $blank_array,"", 1, "-- Select --", $storeName, "" ); 
                                               	 		//echo create_drop_down( "cbo_store_name", 122, "select id, store_name from lib_store_location","id,store_name", 1,"--Display--",0,"",1);
                                               	 ?>                                               		
                                               	</td>
                                               
                                          </tr>
                                          <tr>
                                          	<td align="right">Body Part&nbsp;&nbsp;</td>
	                                        <td>
	                                             <?
	                                                echo create_drop_down( "cbo_body_part", 122, $body_part,"", 1, "-- Display --", 0, "",1 );
	                                             ?>
	                                        </td>
	                                        <td  align="right">Floor&nbsp;&nbsp;</td>
											<td>
												<? //echo create_drop_down( "cbo_floor", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
												<input type="text" name="cbo_floor_name" id="cbo_floor_name" class="text_boxes" style="width:130px" placeholder="Display" disabled />
												<input type="hidden" name="cbo_floor" id="cbo_floor" class="text_boxes" style="width:130px"/>
											</td>
                                          </tr>
                                          <tr>

                                               <td width="100" align="right">GSM&nbsp;&nbsp;</td>
                                               <td><input class="text_boxes" type="text" name="txt_gsm" id="txt_gsm" style="width:110px;" placeholder="Display" readonly disabled  /></td>
                                                <td  align="right">Room&nbsp;&nbsp;</td>
												<td>
													<? //echo create_drop_down( "cbo_room", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													<input type="text" name="cbo_room_name" id="cbo_room_name" class="text_boxes" style="width:130px" placeholder="Display" disabled/>
															<input type="hidden" name="cbo_room" id="cbo_room" class="text_boxes" style="width:130px"/>
												</td>
                                               
                                          </tr>
                                          <tr>
                                          	<td align="right">Dia&nbsp;&nbsp;</td>
                                               <td><input class="text_boxes" type="text" name="txt_dia" id="txt_dia" style="width:110px;" placeholder="Display" readonly disabled  /></td>
                                               <td  align="right">Rack&nbsp;&nbsp;</td>
												<td>
														<? //echo create_drop_down( "txt_rack", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													<input type="text" name="txt_rack_name" id="txt_rack_name" class="text_boxes" style="width:130px" placeholder="Display" disabled />
													<input type="hidden" name="txt_rack" id="txt_rack" class="text_boxes" style="width:130px"/>
												</td> 
                                          </tr>
                                          <tr>
                                               <td  align="right" class="must_entry_caption">Returned Qnty&nbsp;&nbsp;</td>
                                               <td >
                                               <input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:110px;" placeholder="Double Click To Search" readonly onDblClick="openmypage_rtn_qty()" />

                                               <input type="hidden" id="txt_break_qnty" name="txt_break_qnty" > 
                                               <input type="hidden" id="txt_break_roll" name="txt_break_roll" >
                                               <input type="hidden" id="txt_order_id_all" name="txt_order_id_all" >
                                               <input type="hidden" id="prev_return_qnty" name="prev_return_qnty" >

                                               <input type="hidden" id="txt_cons_rate" name="txt_cons_rate" > 
                                               <!-- <input type="hidden" id="txt_order_rate" name="txt_order_rate" >  -->

                                               </td>
                                               <td  align="right">Shelf&nbsp;&nbsp;</td>
												<td>
													<? //echo create_drop_down( "txt_shelf", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
													<input type="text" name="txt_shelf_name" id="txt_shelf_name" class="text_boxes" style="width:130px" placeholder="Display" disabled/>
													<input type="hidden" name="txt_shelf" id="txt_shelf" class="text_boxes" style="width:130px" />
												</td>
                                               
                                          </tr>
                                          <tr>
                                          	   <td align="right">Rate &nbsp;&nbsp;</td>
                                               <td>
                                               		<input class="text_boxes_numeric" type="text" name="txt_order_rate" id="txt_order_rate" style="width:110px;" disabled placeholder="Display" />
                                               		<input class="text_boxes_numeric" type="hidden" name="txt_aop_rate" id="txt_aop_rate"  />
                                               </td>

                                               <td align="right">Color&nbsp;&nbsp;</td>
                                               <td>
                                               <input class="text_boxes" type="text" name="txt_color_name" id="txt_color_name" style="width:130px;" placeholder="Display" readonly disabled  />
                                               <input type="hidden" name="txt_color_id" id="txt_color_id" readonly disabled  />
                                             </td>
                                          </tr>
                                         
                                         
                                          <tr>
                                      			<td align="right">Amount &nbsp;&nbsp;</td>
                                               <td><input class="text_boxes_numeric" type="text" name="txt_amount" id="txt_amount" style="width:110px;" disabled placeholder="Display" /></td>
                                            

                                               <td align="right">Remarks&nbsp;&nbsp;</td>
                                               <td><input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:130px;" placeholder="Write" /></td>
                                          </tr>

                                          <tr>
                                          	   <td align="right">UOM&nbsp;&nbsp;</td>
                                            	<td><? echo create_drop_down( "cbo_uom", 122, $unit_of_measurement,"", 1, "Display", 0, "",1 ); ?></td>

                                               <td align="right">Booking No&nbsp;&nbsp;</td>
                                               <td><input class="text_boxes" type="text" name="txt_booking_no" id="txt_booking_no" style="width:130px;" disabled placeholder="Display" /></td>
                                          </tr>
                                          <tr>
                                          	   <td align="right">No Of Roll &nbsp;&nbsp;</td>
                                               <td><input class="text_boxes_numeric" type="text" name="txt_roll" id="txt_roll" style="width:110px;" readonly /></td>
                                          </tr>
                                   </table>
                            </fieldset>
                       	 <fieldset style="width:360px; float:left; margin-left:5px">  
                           <legend>Display</legend>                                     
                                  <table  width="350" cellspacing="2" cellpadding="0" border="0" id="display" >                           
                            <tr>
                                  <td>Fabric Received</td>
                                  <td width="100"><input  type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes" style="width:160px" readonly disabled  /></td>
                            </tr>     
                            <!-- <tr>
                                  <td>Total Received</td>
                                  <td width="100"><input  type="text" name="txt_total_receive" id="txt_total_receive" class="text_boxes" style="width:160px" readonly disabled  /></td>
                            </tr>  
                            <tr>
                                  <td>Total Issue</td>
                                  <td width="100"><input  type="text" name="txt_total_issue" id="txt_total_issue" class="text_boxes" style="width:160px" readonly disabled  /></td>
                            </tr>         -->             
                            <tr>
                                <td>Cumulative Return</td>
                                <td>
                                <input  type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes" style="width:160px"  readonly disabled />
                                <input type="hidden" id="hidden_receive_trans_id" name="hidden_receive_trans_id" readonly disabled  />
                                <input type="hidden" id="before_receive_trans_id" name="before_receive_trans_id" readonly disabled  />
                                </td>
                            </tr>
                            <tr>
                                <td>Yet to Issue</td>
                                <td width="100">
                                    <input  type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes" style="width:160px"  readonly disabled />
                                </td>
                            </tr> 
                            <tr>
                                <td>Global Stock</td>
                                <td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes" style="width:160px" disabled /></td>
                            </tr>
                      </table>
                            </fieldset>
                   	   </td>
                    </tr>
                </table>                
               	<table cellpadding="0" cellspacing="1" width="820">
                	<tr> 
                       <td colspan="6" align="center"></td>				
                	</tr>
                	<tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                             <!-- details table id for update -->
                             <input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
                             <input type="hidden" id="update_id" name="update_id" value="" />
                             <input type="hidden" id="update_details_id" name="update_details_id" value="" />
                             <input type="hidden" name="store_update_upto" id="store_update_upto">
                              <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_yarn_receive_return_entry", 0,1,"fnResetForm()",1);?>
                        </td>
                   </tr> 
                </table>                 
              	</fieldset>
              	<fieldset>
    			<div style="width:840px;" id="list_container_yarn"></div>
    		  	</fieldset>
           </td>
         </tr>
    </table>
    </div>
    <div style="width:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div id="list_product_container" style="overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>  
	</form>
</div>   
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
