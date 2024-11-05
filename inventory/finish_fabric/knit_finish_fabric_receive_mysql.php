<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Finish Fabric Receive Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	08-05-2014
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
echo load_html_head_contents("Knit Finish Fabric Receive Info","../../", 1, 1, '','',''); 

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

	function set_receive_basis()
	{
		var recieve_basis = $('#cbo_receive_basis').val();
		$('#list_fabric_desc_container').html('');
		
		$('#buyer_id').val('');
		$('#buyer_name').val('');
		$('#txt_production_qty').val('');
		$('#all_po_id').val('');
		$('#save_data').val('');
		$('#txt_booking_no').val('');	
		$('#txt_booking_no_id').val('');	
		$('#batch_booking_without_order').val('');
		$("#txt_order_no").val('');
		$("#hidden_order_id").val('');
		$('#cbo_body_part').val(0);
		$("#txt_gsm").val('');
		$("#txt_dia_width").val('');
		//$("#finish_production_dtls_id").val('');
		$('#previous_prod_id').val('');	
		$('#product_id').val('');
		
		$('#txt_production_qty').attr('readonly','readonly');
		$('#txt_production_qty').attr('onClick','openmypage_po();');	
		$('#txt_production_qty').attr('placeholder','Single Click to Search');
		
        if(recieve_basis == 9)
        {
			$('#txt_booking_no').removeAttr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').attr('disabled','disabled');
			$('#txt_batch_no').attr("placeholder","Dispaly");
			$('#cbo_body_part').attr('disabled','disabled');
			$('#txt_fabric_desc').val('');
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').attr('disabled','disabled');
			$('#txt_fabric_desc').removeAttr("onDblClick");
			$('#txt_fabric_desc').attr("placeholder","Dispaly");
			$('#txt_color').val('');
			$('#txt_color').attr('disabled','disabled');
			$("#txt_order_no").attr("placeholder","Display"); 
			$("#txt_order_no").removeAttr("onDblClick");
        }
        else if(recieve_basis == 4 || recieve_basis==6)
        {
			$('#txt_booking_no').attr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').removeAttr('disabled','disabled');
			$('#txt_batch_no').attr("placeholder","Write");
			$('#cbo_body_part').removeAttr('disabled','disabled');
			$('#txt_fabric_desc').val('');
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').removeAttr('disabled','disabled');
			$('#txt_fabric_desc').attr("onDblClick","openmypage_fabricDescription();");
			$('#txt_fabric_desc').attr("placeholder","Double Click For Search");
			$('#txt_color').val('');
			$('#txt_color').removeAttr('disabled','disabled');
			$("#txt_order_no").attr("placeholder","Double Click"); 
			$('#txt_order_no').attr('onDblClick','openmypage_order();');
        }
		else
		{
			if(recieve_basis==1)
			{
				$('#cbo_body_part').removeAttr('disabled','disabled');
				$("#txt_order_no").attr("placeholder","Double Click"); 
				$('#txt_order_no').attr('onDblClick','openmypage_order();');
			}
			else
			{
				$('#cbo_body_part').attr('disabled','disabled');
				$("#txt_order_no").attr("placeholder","Display"); 
				$("#txt_order_no").removeAttr("onDblClick");
			}
			
			$('#txt_booking_no').removeAttr('disabled','disabled');
			$("#txt_batch_no").val('');
			$("#txt_batch_id").val('');
			$('#txt_batch_no').removeAttr('disabled','disabled');
			$('#txt_batch_no').attr("placeholder","Write");
			$('#txt_fabric_desc').val('');
			$('#fabric_desc_id').val('');
			$('#txt_fabric_desc').attr('disabled','disabled');
			$('#txt_fabric_desc').removeAttr("onDblClick");
			$('#txt_fabric_desc').attr("placeholder","Dispaly");
			$('#txt_color').val('');
			$('#txt_color').removeAttr('disabled','disabled');
		}
	}
	
	function openmypage_wo_pi_production_popup()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var receive_basis=$('#cbo_receive_basis').val();
		
		if (form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		
		if(receive_basis==1 || receive_basis==2 || receive_basis==9)
		{ 	
			var title = 'WO/PI/Production Selection Form';	
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&receive_basis='+receive_basis+'&action=wo_pi_production_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var theemail=this.contentDoc.getElementById("hidden_wo_pi_production_id").value;	 //Knit Id for Kintting Plan
				var theename=this.contentDoc.getElementById("hidden_wo_pi_production_no").value; //all data for Kintting Plan
				var booking_without_order=this.contentDoc.getElementById("booking_without_order").value; //Access form field with id="emailfield"

				if(theemail!="")
				{
					freeze_window(5);
					
					$('#buyer_id').val('');
					$('#buyer_name').val('');
					$('#txt_production_qty').val('');
					$('#all_po_id').val('');
					$('#save_data').val('');
					$("#txt_order_no").val('');
					$("#hidden_order_id").val('');
					$('#cbo_body_part').val(0);
					$("#txt_gsm").val('');
					$("#txt_dia_width").val('');
					//$("#finish_production_dtls_id").val('');
					$('#previous_prod_id').val('');	
					$('#product_id').val('');
					
					$('#txt_booking_no').val(theename);
					$('#txt_booking_no_id').val(theemail);
					$('#booking_without_order').val(booking_without_order);
					
					if(booking_without_order==1)
					{
						$('#txt_production_qty').removeAttr('readonly','readonly');
						$('#txt_production_qty').removeAttr('onClick','onClick');
						$('#txt_production_qty').removeAttr('placeholder','placeholder');
					}
					else
					{
						$('#txt_production_qty').attr('readonly','readonly');
						$('#txt_production_qty').attr('onClick','openmypage_po();');	
						$('#txt_production_qty').attr('placeholder','Single Click');
					}
					
					if(receive_basis==2)
					{
						show_list_view(theename+"**"+booking_without_order+"**"+receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
					}
					else
					{
						show_list_view(theemail+"**"+booking_without_order+"**"+receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
					}
					
					release_freezing();
				} 
			}
		}
	}
	 
	function openmypage_order() 
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var receive_basis=$('#cbo_receive_basis').val();
		var hidden_order_id=$('#hidden_order_id').val();
		var buyer_name=$('#buyer_id').val();
		
		if(form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}  
	
		if(receive_basis==1 || receive_basis==4 || receive_basis==6)
		{
			var title = 'PO Info';	
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&hidden_order_id='+hidden_order_id+'&buyer_name='+buyer_name+'&action=po_search_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value;  
				var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value;  
				
				$("#txt_order_no").val(hidden_order_no);
				$("#hidden_order_id").val(hidden_order_id);
			}
		}
	}

	function put_data_dtls_part(id,type,page_path)
	{
		get_php_form_data(id+"**"+$('#roll_maintained').val(), type, page_path );
	}

	function openmypage_fabricDescription()
	{
		var title = 'Fabric Description Info';	
		var page_link = 'requires/knit_finish_fabric_receive_controller.php?action=fabricDescription_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theename=this.contentDoc.getElementById("hidden_desc_no").value; //Access form field with id="emailfield"
			var theegsm=this.contentDoc.getElementById("hidden_gsm").value; //Access form field with id="emailfield"
			var fabric_desc_id=this.contentDoc.getElementById("fabric_desc_id").value; //Access form field with id="emailfield"
			
			$('#txt_fabric_desc').val(theename);
			$('#fabric_desc_id').val(fabric_desc_id);
			$('#txt_gsm').val(theegsm);
		}
	}

	function openmypage_po()
	{
		var receive_basis=$('#cbo_receive_basis').val();
		var roll_maintained = $('#roll_maintained').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var txt_production_qty = $('#txt_production_qty').val(); 
		var distribution_method = $('#distribution_method_id').val();
		var txt_booking_no=$("#txt_booking_no").val();
		var hidden_order_id=$("#hidden_order_id").val();
		var txt_fabric_desc=$('#txt_fabric_desc').val();
		//var finish_production_dtls_id=$('#finish_production_dtls_id').val();
		
		if(form_validation('cbo_company_id*cbo_receive_basis','Company*Receive Basis')==false)
		{
			return;
		}
		
		if(receive_basis==1 || receive_basis==4 || receive_basis==6)
		{
			if(form_validation('txt_order_no','Order Numbers')==false)
			{
				alert("Please Select Order Numbers.");
				return;
			}
		}
		else if(receive_basis==2 && txt_booking_no_id=="")
		{
			alert("Please Select WO No.");
			$('#txt_booking_no').focus();
			return;
		}
		else if(receive_basis==9 && txt_fabric_desc=="")
		{
			alert("Please Select Fabric Description.");
			$('#txt_fabric_desc').focus();
			return false;
		}

		var title = 'PO Info';
		var page_link = 'requires/knit_finish_fabric_receive_controller.php?receive_basis='+receive_basis+'&cbo_company_id='+cbo_company_id+'&txt_booking_no='+txt_booking_no+'&all_po_id='+all_po_id+'&roll_maintained='+roll_maintained+'&save_data='+save_data+'&txt_production_qty='+txt_production_qty+'&prev_distribution_method='+distribution_method+'&hidden_order_id='+hidden_order_id+'&action=po_popup';//+'&finish_production_dtls_id='+finish_production_dtls_id
		 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_finish_qnty=this.contentDoc.getElementById("tot_finish_qnty").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var order_nos=this.contentDoc.getElementById("order_nos").value; //Access form field with id="emailfield"
			var buyer_name=this.contentDoc.getElementById("buyer_name").value; //Access form field with id="emailfield"
			var buyer_id=this.contentDoc.getElementById("buyer_id").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value;
			
			$('#save_data').val(save_string);
			$('#txt_production_qty').val(tot_finish_qnty);
			$('#all_po_id').val(all_po_id);
			$('#hidden_order_id').val(all_po_id);
			$('#txt_order_no').val(order_nos);
			$('#buyer_name').val(buyer_name);
			$('#buyer_id').val(buyer_id);
			$('#distribution_method_id').val(distribution_method);
			
			get_php_form_data(all_po_id, 'load_color', 'requires/knit_finish_fabric_receive_controller');
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
			var page_link = 'requires/knit_finish_fabric_receive_controller.php?cbo_company_id='+cbo_company_id+'&action=systemId_popup';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var finish_fabric_id=this.contentDoc.getElementById("hidden_sys_id").value;
				
				reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container','','','','roll_maintained');
				get_php_form_data(finish_fabric_id, "populate_data_from_finish_fabric", "requires/knit_finish_fabric_receive_controller" );
				
				var booking_pi_production_no = $('#txt_booking_no').val();
				var booking_pi_production_id = $('#txt_booking_no_id').val();
				var booking_without_order = $('#booking_without_order').val();
				var cbo_receive_basis = $('#cbo_receive_basis').val();
				
				if(cbo_receive_basis==2)
				{
					show_list_view(booking_pi_production_no+"**"+booking_without_order+"**"+cbo_receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
				}
				else if(cbo_receive_basis==1 || cbo_receive_basis==9)
				{
					show_list_view(booking_pi_production_id+"**"+booking_without_order+"**"+cbo_receive_basis,'show_fabric_desc_listview','list_fabric_desc_container','requires/knit_finish_fabric_receive_controller','');
				}
				
				show_list_view(finish_fabric_id,'show_finish_fabric_listview','list_container_finishing','requires/knit_finish_fabric_receive_controller','');
			}
		}
	}
		
	function fnc_finish_receive_entry( operation )
	{
		if(operation==2)
		{
			show_msg('13');
			return;
		}
		
		var cbo_receive_basis=$('#cbo_receive_basis').val();
		
		if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_dyeing_source*cbo_dyeing_company*txt_batch_no*cbo_body_part*txt_fabric_desc*txt_production_qty*txt_color','Company*Receive Basis*Receive Date*Store Name*Dyeing Source*Dyeing Company*Body Part*Fabric Description*Production Qnty*Color')==false )
		{
			return;
		}
		
		if(cbo_receive_basis==1 || cbo_receive_basis==2 || cbo_receive_basis==9)	
		{
			if( form_validation('txt_booking_no','WO/PI/Production')==false )
			{
				alert("Please Select WO/PI/Production");
				return;
			}
		}
		
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_receive_basis*txt_system_id*txt_receive_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*cbo_store_name*txt_booking_no*txt_booking_no_id*booking_without_order*txt_batch_no*txt_batch_id*cbo_body_part*txt_fabric_desc*fabric_desc_id*txt_color*txt_gsm*txt_dia_width*txt_production_qty*txt_reject_qty*buyer_id*cbo_machine_name*txt_rack*txt_shelf*update_id*update_dtls_id*save_data*all_po_id*update_trans_id*previous_prod_id*product_id*hidden_receive_qnty*roll_maintained*txt_no_of_roll',"../../");
		//alert (data);return;
	  freeze_window(operation);
	  http.open("POST","requires/knit_finish_fabric_receive_controller.php",true);
	  http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	  http.send(data);
	  http.onreadystatechange = fnc_finish_receive_entry_reponse;
	}
	
	function fnc_finish_receive_entry_reponse()
	{
		if(http.readyState == 4) 
		{
			//release_freezing();alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				show_list_view(reponse[1],'show_finish_fabric_listview','list_container_finishing','requires/knit_finish_fabric_receive_controller','');
			
				reset_form('finishFabricEntry_1','','','','','update_id*txt_system_id*cbo_receive_basis*cbo_company_id*txt_receive_date*cbo_dyeing_source*cbo_dyeing_company*txt_challan_no*cbo_location*cbo_store_name*roll_maintained*txt_booking_no*txt_booking_no_id*booking_without_order');
				
				set_button_status(0, permission, 'fnc_finish_receive_entry',1);
			}
			release_freezing();
		}
	}

	function set_form_data(data)
	{
		var receive_basis = $('#cbo_receive_basis').val();
		var roll_maintained = $('#roll_maintained').val();
		
		if(receive_basis==9)
		{
			get_php_form_data(data+"**"+roll_maintained, "populate_data_from_production", "requires/knit_finish_fabric_receive_controller" );
		}
		else
		{
			var data=data.split("**");
			if(receive_basis!=1)
			{
				$('#cbo_body_part').val(data[0]);
			}
			$('#txt_fabric_desc').val(data[1]);
			$('#txt_gsm').val(data[2]);
			$('#txt_dia_width').val(data[3]);
			$('#fabric_desc_id').val(data[4]);
		}
	}
	
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
    <div style="width:830px; float:left;" align="center">   
        <fieldset style="width:820px;">
        <legend>Finish Fabric Receive Entry</legend>
        <fieldset>
            <table cellpadding="0" cellspacing="2" width="810" border="0">
                <tr>
                    <td colspan="3" align="right"><strong>System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
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
							echo create_drop_down( "cbo_company_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/knit_finish_fabric_receive_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/knit_finish_fabric_receive_controller', this.value, 'load_drop_down_store', 'store_td' );get_php_form_data(this.value,'roll_maintained','requires/knit_finish_fabric_receive_controller' );" );
                        ?>
                    </td>
                    <td class="must_entry_caption">Receive Basis</td>
                    <td>
						<?
							echo create_drop_down("cbo_receive_basis",160,$receive_basis_arr,"",1,"-- Select --",0,"set_receive_basis();","",'1,2,4,6,9');
                        ?>
                    </td>
                    
                    <td class="must_entry_caption">Receive Date</td>
                    <td>
                        <input type="date" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:150px;" readonly>
                    </td>
                </tr>
                <tr>
                    <td>Receive Challan No.</td>
                    <td>
                        <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:140px;" maxlength="20" title="Maximum 20 Character" />
                    </td>
                    <td>WO/PI/Production</td>
                    <td>
                    	<input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:150px"  placeholder="Double Click to Search" onDblClick="openmypage_wo_pi_production_popup();" readonly>
                        <input type="hidden" name="txt_booking_no_id" id="txt_booking_no_id" class="text_boxes">
                        <input type="hidden" name="booking_without_order" id="booking_without_order"/>
                    </td>
                    <td class="must_entry_caption"> Store Name </td>
                    <td id="store_td">
						<?
                            echo create_drop_down( "cbo_store_name", 162, $blank_array,"",1, "--Select store--", 1, "" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="must_entry_caption">Dyeing Source</td>
                    <td>
						<?
							echo create_drop_down("cbo_dyeing_source", 152, $knitting_source,"", 1,"-- Select Source --", 0,"load_drop_down( 'requires/knit_finish_fabric_receive_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_dyeing_com','dyeingcom_td');","","1,3");
                        ?>
                    </td>
                    <td class="must_entry_caption">Dyeing Company</td>
                    <td id="dyeingcom_td">
						<?
							echo create_drop_down("cbo_dyeing_company", 162, $blank_array,"", 1,"-- Select Dyeing Company --", 0,"");
                        ?>
                    </td>
                    <td>Location</td>
                    <td id="location_td">
						<?
							echo create_drop_down("cbo_location", 162, $blank_array,"", 1,"-- Select Location --", 0,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Order Numbers</td>
                    <td colspan="5">
                        <input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:417px" readonly placeholder="Display" />
                        <input type="hidden" id="hidden_order_id" />
                    </td>
        		</tr>
            </table>
        </fieldset>
        <table cellpadding="0" cellspacing="1" width="810" border="0">
            <tr>
                <td width="70%" valign="top">
                    <fieldset>
                    <legend>New Entry</legend>
                        <table cellpadding="0" cellspacing="2" width="100%">
                            <tr>
                                <td width="110" class="must_entry_caption">Batch No.</td>
                                <td>
                                    <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:170px;" placeholder="Write" />
                                    <input type="hidden" name="txt_batch_id" id="txt_batch_id" readonly />
                                </td>
                                <td class="must_entry_caption" width="90">QC Pass Qty</td>
                                <td>
                                    <input type="text" name="txt_production_qty" id="txt_production_qty" class="text_boxes_numeric" placeholder="Single Click to Search" style="width:130px;" onClick="openmypage_po()" readonly />
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Body Part</td>
                                <td>
                                     <?
                                        echo create_drop_down( "cbo_body_part", 182, $body_part,"", 1, "-- Select Body Part --", 0, "",1 );
                                     ?>
                                </td>
                                <td>Reject Qty</td>
                                <td>
                                    <input type="text" name="txt_reject_qty" id="txt_reject_qty" class="text_boxes_numeric" style="width:130px;" />
                                </td>
                            </tr>
                            <tr>
                                <td class="must_entry_caption">Fabric Description</td>
                                <td>
                                    <input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:170px;" readonly disabled/>
                                    <input type="hidden" name="fabric_desc_id" id="fabric_desc_id" readonly/>
                                </td>
                                <td class="must_entry_caption">Color</td>
                                <td>
                                    <input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:130px;" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td>GSM</td>
                                <td>
                                    <input type="text" name="txt_gsm" id="txt_gsm" class="text_boxes_numeric" style="width:170px;" maxlength="10"/>
                                </td>
                                <td>No Of Roll</td>
                                <td>
                                    <input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:130px;"/>
                                </td>
                            </tr>
                            <tr>
                                <td>Dia/Width</td>
                                <td>
                                    <input type="text" name="txt_dia_width" id="txt_dia_width" class="text_boxes_numeric" style="width:170px;" maxlength="10" />
                                </td>
                                <td>Machine Name</td>
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_machine_name", 142, "select id,company_id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=4 and status_active=1 and is_deleted=0 and is_locked=0","id,machine_name", 1, "-- Select Machine --", 0, "","" );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Rack</td>
                                <td>
                                    <input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:170px">
                                </td>
                                <td>Shelf</td>
                                <td>
                                    <input type="text" name="txt_shelf" id="txt_shelf" class="text_boxes_numeric" style="width:130px">
                                </td>
                            </tr>
                            <tr>
                                <td>Buyer</td>
                                <td>
                                    <input type="text" name="buyer_name" id="buyer_name" class="text_boxes" placeholder="Display" style="width:170px;" disabled="disabled" />
                                    <input type="hidden" name="buyer_id" id="buyer_id" class="text_boxes" disabled="disabled" />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td width="2%" valign="top">&nbsp;</td>
                <td width="28%" valign="top">
                    <fieldset style="display:none">
                    <legend>Display</legend>
                        <table>
                            <tr>
                                <td width="100">Batch Quantity</td>
                                <td>
                                    <input type="text" name="txt_batch_qnty" id="txt_batch_qnty" class="text_boxes" style="width:100px;" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td>Total Received</td>
                                <td>
                                    <input type="text" name="txt_total_received" id="txt_total_received" class="text_boxes" style="width:100px;" disabled />
                                </td>
                            </tr>
                            <tr>
                                <td>Yet to Received</td>
                                <td>
                                    <input type="text" name="txt_yet_receive" id="txt_yet_receive" class="text_boxes" style="width:100px;" disabled />
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="4" class="button_container">
                    <?
                        echo load_submit_buttons($permission, "fnc_finish_receive_entry", 0,0,"reset_form('finishFabricEntry_1','list_container_finishing*list_fabric_desc_container','','','disable_enable_fields(\'cbo_company_id\');set_receive_basis();')",1);
                    ?>
                    <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                    <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                    <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                    <input type="hidden" name="product_id" id="product_id" readonly><!--For Receive Basis Production-->
                    <input type="hidden" name="hidden_receive_qnty" id="hidden_receive_qnty" readonly>
                    <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                    <input type="hidden" name="save_data" id="save_data" readonly>
                    <!--<input type="hidden" name="finish_production_dtls_id" id="finish_production_dtls_id" readonly>-->
                    <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                    <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                </td>
            </tr>
        </table>
        <div style="width:820px;" id="list_container_finishing"></div>
		</fieldset>
    </div>
    <div id="list_fabric_desc_container" style="width:360px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
