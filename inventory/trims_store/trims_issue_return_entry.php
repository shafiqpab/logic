<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Issue Return Entry 
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	30/03/2015
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

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)"; 
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Trims Issue Return Entry", "../../", 1, 1,'','1',''); 

?>
<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
	var permission='<? echo $permission; ?>';
	
	function openpage_issueNo()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/trims_issue_return_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=trims_issue_popup_search';
			var title='Trims Issue Pop Up';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var company_id=$('#cbo_company_id').val();

				var theform=this.contentDoc.forms[0];
				var hidden_data=this.contentDoc.getElementById("hidden_data").value.split("_");
				var trims_issue_id=hidden_data[0];
				var trims_issue_no=hidden_data[1];
				var issue_basis=hidden_data[2];
				get_php_form_data(trims_issue_id+"**"+company_id, "populate_load_room_rack_self_bin", "requires/trims_issue_return_entry_controller" );	
				var store_id=hidden_data[3];
				var floor_id=hidden_data[4];
				var room_id=hidden_data[5];
				var rack_id=hidden_data[6];
				var shelf_id=hidden_data[7];
				var bin_id=hidden_data[8];
				if(trims_issue_id!="")
				{
					freeze_window(5);
					reset_form('','','cbo_item_group*cbo_uom*txt_item_description*hidden_prod_id*txt_item_size*txt_item_color*txt_return_qnty*txt_rack*txt_shelf*txt_buyer_order*txt_issued_qnty*txt_rate*txt_amount*txt_cumulative_returned*txt_net_used*save_data*all_po_id','','','');
					$('#issue_id').val(trims_issue_id);
					$('#txt_issue_no').val(trims_issue_no);
					$('#cbo_basis').val(issue_basis);
					$('#cbo_store_name').val(store_id);
					$('#cbo_floor').val(floor_id);
					$('#cbo_room').val(room_id);
					$('#txt_rack').val(rack_id);
					$('#txt_shelf').val(shelf_id);
					$('#cbo_bin').val(bin_id);
					enable_disable();
					show_list_view(trims_issue_id,'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_return_entry_controller','');
					release_freezing();
				}
							 
			}
		}
	}
	
	function openmypage_returnQty()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		var save_data = $('#save_data').val();
		var all_po_id = $('#all_po_id').val();
		var returnQnty = $('#txt_return_qnty').val();
		var prod_id = $('#hidden_prod_id').val();
		var distribution_method = $('#distribution_method_id').val();
		var conversion_faction = $('#txt_conversion_faction').val();
		
		if (form_validation('cbo_company_id*txt_item_description','Company*Item Description')==false)
		{
			return;
		}
			
		var title = 'PO Info';	
		var page_link = 'requires/trims_issue_return_entry_controller.php?cbo_company_id='+cbo_company_id+'&save_data='+save_data+'&all_po_id='+all_po_id+'&returnQnty='+returnQnty+'&prev_method='+distribution_method+'&prod_id='+prod_id+'&conversion_faction='+conversion_faction+'&action=po_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=420px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window

			var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
			var tot_trims_qnty=this.contentDoc.getElementById("tot_trims_qnty").value; //Access form field with id="emailfield"
			var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
			var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
			var distribution_method=this.contentDoc.getElementById("distribution_method").value; //Access form field with id="emailfield"

			$('#save_data').val(save_string);
			//alert(tot_trims_qnty);
			$('#txt_return_qnty').val(tot_trims_qnty);
			$('#all_po_id').val(all_po_id);
			$('#txt_buyer_order').val(all_po_no);
			$('#distribution_method_id').val(distribution_method);
			calculate();
		}
	}
	
	function generate_report_file(data,action,page)
	{
		window.open("requires/trims_issue_return_entry_controller.php?data=" + data+'&action='+action, true );
	}
		
	function fnc_trims_issue(operation)
	{
		if(operation==4)
		{
			 var report_title=$( "div.form_caption" ).html();
			 generate_report_file( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title,'trims_issue_entry_print','requires/trims_issue_return_entry_controller');
			 return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('13');
				return;
			}*/
			if ($("#is_posted_account").val()*1 == 1) {
				alert("Already Posted In Accounting. Save Update Delete Restricted.");
				return;
			}
			
			if( form_validation('cbo_company_id*txt_return_date*txt_issue_no*cbo_store_name*txt_item_description*txt_return_qnty','Company*Return Date*Issue No*Store*Description*Qnty')==false )
			{
				return;
			}
			
			var current_date='<? echo date("d-m-Y"); ?>';
			if(date_compare($('#txt_return_date').val(), current_date)==false)
			{
				alert("Issue Return Date Can not Be Greater Than Current Date");
				return;
			}	
			
			
			if(($("#txt_issue_qnty").val()*1 > ($("#txt_return_qnty").val()*1+$("#txt_cumulative_returned").val()*1)-$("#hidden_return_qnty").val()*1)) 
			{
				alert("Return Quantity Exceeds Issued Quantity.");
				return;
			}

			// Store upto validation start
			var store_update_upto=$('#store_update_upto').val()*1;
			var cbo_floor=$('#cbo_floor').val()*1;
			var cbo_room=$('#cbo_room').val()*1;
			var txt_rack=$('#txt_rack').val()*1;
			var txt_shelf=$('#txt_shelf').val()*1;
			var cbo_bin=$('#cbo_bin').val()*1;
			
			if(store_update_upto > 1)
			{
				if(store_update_upto==6 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0 || cbo_bin==0))
				{
					alert("Up To Bin Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==5 && (cbo_floor==0 || cbo_room==0 || txt_rack==0 || txt_shelf==0))
				{
					alert("Up To Shelf Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==4 && (cbo_floor==0 || cbo_room==0 || txt_rack==0))
				{
					alert("Up To Rack Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==3 && (cbo_floor==0 || cbo_room==0))
				{
					alert("Up To Room Value Full Fill Required For Inventory");return;
				}
				else if(store_update_upto==2 && cbo_floor==0)
				{
					alert("Up To Floor Value Full Fill Required For Inventory");return;
				}
			}
			// Store upto validation End
			var all_po_id=$('#all_po_id').val()*1;
			if(all_po_id=="")
			{
				alert("PO Not Found."); return;
			}
			

			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*cbo_company_id*txt_return_date*cbo_uom*issue_id*cbo_basis*txt_challan_no*hidden_prod_id*txt_rack*txt_shelf*txt_return_qnty*txt_rate*txt_amount*hidden_return_qnty*update_id*all_po_id*update_trans_id*save_data*previous_prod_id*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*store_update_upto',"../../");
			
			freeze_window(operation);
			
			http.open("POST","requires/trims_issue_return_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange =fnc_trims_issue_Reply_info;
		}
	}
	
	function fnc_trims_issue_Reply_info()
	{
		if(http.readyState == 4) 
		{
			 //alert(http.responseText);
			//release_freezing();	return;
			var reponse=trim(http.responseText).split('**');
			
			if(reponse[0]==30)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			if(reponse[0]==20)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			show_msg(reponse[0]);
			
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				document.getElementById('update_id').value = reponse[1];
				document.getElementById('txt_system_id').value = reponse[2];
				$('#cbo_company_id').attr('disabled','disabled');
				reset_form('trimsissue_1','','','','','update_id*txt_system_id*cbo_company_id*txt_return_date*txt_challan_no*cbo_basis*issue_id*txt_issue_no*cbo_store_name*store_update_upto*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*is_posted_account*store_update_upto');
				show_list_view(reponse[1],'show_trims_listview','div_details_list_view','requires/trims_issue_return_entry_controller','');
				set_button_status(0, permission, 'fnc_trims_issue',1,1);
			}
			release_freezing();	
		}
	}
	
	function openmypage_systemId()
	{
		var cbo_company_id = $('#cbo_company_id').val();
		if (form_validation('cbo_company_id','Company')==false)
		{
			return;
		}
		else
		{ 	
			var page_link='requires/trims_issue_return_entry_controller.php?cbo_company_id='+cbo_company_id+'&action=trims_issue_return_popup_search';
			var title='Trims Issue Return Pop Up';
			
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=810px,height=390px,center=1,resize=1,scrolling=0','../');
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var trims_return_id=this.contentDoc.getElementById("hidden_issue_id").value;
				if(trims_return_id!="")
				{
					freeze_window(5);
					reset_form('trimsissue_1','div_details_list_view*list_fabric_desc_container','','','','cbo_company_id*is_posted_account*store_update_upto');
					
					var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
					$("#is_posted_account").val(posted_in_account);
					if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
					else 					 document.getElementById("accounting_posted_status").innerHTML="";
					
					get_php_form_data(trims_return_id, "populate_data_from_trims_issue", "requires/trims_issue_return_entry_controller" );
					var trims_issue_id=$('#issue_id').val();
					show_list_view(trims_return_id,'show_trims_listview','div_details_list_view','requires/trims_issue_return_entry_controller','');
					show_list_view(trims_issue_id,'create_itemDesc_search_list_view','list_fabric_desc_container','requires/trims_issue_return_entry_controller','');
					
					set_button_status(0, permission, 'fnc_trims_issue',1,1);
					release_freezing();
				}
							 
			}
		}
	}
	
	function calculate()
	{
		var quantity 		= $("#txt_return_qnty").val();
		var rate			= $('#txt_rate').val();	 
		var amount 			= quantity*1*rate*1; 
		$("#txt_amount").val(amount.toFixed(2));
	}
	
	
	function set_form_data(data)
	{
		var data=data.split("**");
		$('#hidden_prod_id').val(data[0]);
		$('#cbo_item_group').val(data[1]);
		$('#txt_item_description').val(data[2]);
		$('#txt_item_color').val(data[3]);
		$('#txt_item_size').val(data[4]);
		//$('#txt_rack').val(data[5]);
		//$('#txt_shelf').val(data[6]);
		$('#txt_issued_qnty').val(data[7]);
		$('#all_po_id').val(data[8]);
		$('#txt_buyer_order').val(data[9]);
		$('#txt_conversion_faction').val(data[11]);
		
		var issueBasis =$("#cbo_basis").val();
		var issue_id = $('#issue_id').val();
		
		if(issue_id!="")
		{
			get_php_form_data(issue_id+"**"+data[0]+"**"+data[5]+"**"+data[6]+"**"+data[7], 'get_trim_return_cum_info', 'requires/trims_issue_return_entry_controller' );
			$('#save_data').val('');
			$('#txt_return_qnty').val('');
			calculate();	
		}
		set_button_status(0, permission, 'fnc_trims_issue',1,1);
		
	}
	
	function enable_disable()
	{
		var issueBasis =$("#cbo_basis").val();
		if(issueBasis==2)	
		{
			$('#txt_return_qnty').removeAttr('readonly','readonly');
			$('#txt_return_qnty').removeAttr('onClick','onClick');
			$('#txt_return_qnty').attr('onKeyUp','calculate();');
			$('#txt_return_qnty').attr('placeholder','Write');			
		}
		else
		{
			$('#txt_return_qnty').attr('readonly','readonly');
			$('#txt_return_qnty').removeAttr('onKeyUp','onKeyUp');
			$('#txt_return_qnty').attr('onClick','openmypage_returnQty();');	
			$('#txt_return_qnty').attr('placeholder','Single Click');	
		}
	}

	function company_on_change(company)
	{
	    var data='cbo_company_id='+company+'&action=upto_variable_settings';    

	    var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() 
	    {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("store_update_upto").value = this.responseText;
	        }
	    }
	    xmlhttp.open("POST", "requires/trims_issue_return_entry_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);
	}

	function independence_basis_controll_function(data)
	{
	    /*var independent_control_arr = JSON.parse('<? //echo json_encode($independent_control_arr); ?>');
	    $("#cbo_receive_basis").val(0);
	    $("#cbo_receive_basis option[value='4']").show();
	    if(independent_control_arr[data]==1)
	    {
	        $("#cbo_receive_basis option[value='4']").hide();
	    }*/
		
		var varible_string=return_global_ajax_value( data, 'varible_inventory', '', 'requires/trims_issue_return_entry_controller');
		
		var varible_string_ref=varible_string.split("**");
		//alert(varible_string_ref[0]);
		if(varible_string_ref[0])
		{
			$('#variable_string_inventory').val(varible_string_ref[1]+"**"+varible_string_ref[2]+"**"+varible_string_ref[3]+"**"+varible_string_ref[4]);
			/*if(varible_string_ref[1]==1)
			{
				$("#cbo_receive_basis option[value='4']").hide();
			}
			else
			{
				$("#cbo_receive_basis option[value='4']").show();
			}*/
			$('#is_rate_optional').val(varible_string_ref[2]);
			/*if(varible_string_ref[4]==2)
			{
				$('#txt_rate').attr("readonly",true);
			}
			else
			{
				$('#txt_rate').attr("readonly",false);
			}*/
			
			if(varible_string_ref[3]==1)
			{
				$('#rate_td').css("display", "none");
				$('#rate_th_td').css("display", "none");
				$('#amount_td').css("display", "none");
				//$('#book_currency_td').css("display", "none");
			}
			else
			{
				$('#rate_td').css("display", "");
				$('#rate_th_td').css("display", "");
				$('#amount_td').css("display", "");
				//$('#book_currency_td').css("display", "");
			}
			
		}
		else
		{
			$('#variable_string_inventory').val("");
			//$("#cbo_receive_basis option[value='4']").show();
			$('#is_rate_optional').val("");
			//$('#txt_rate').attr("readonly",false);
			$('#rate_td').css("display", "");
			$('#rate_th_td').css("display", "");
			$('#amount_td').css("display", "");
			//$('#book_currency_td').css("display", "");
		}
		
		
		//alert(varible_string);return;

	    // ==============Start Floor Room Rack Shelf Bin upto variable Settings============
		
		//$('#store_update_upto').val(varible_string_ref[5]);
		//$('#variable_lot').val(varible_string_ref[6]);
		
	    /*var data='cbo_company_id='+data+'&action=upto_variable_settings';
	    var xmlhttp = new XMLHttpRequest();
	    xmlhttp.onreadystatechange = function() 
	    {
	        if (this.readyState == 4 && this.status == 200) {
	            document.getElementById("store_update_upto").value = this.responseText;				
	        }
	    }
	    xmlhttp.open("POST", "requires/general_item_receive_controller.php", true);
	    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	    xmlhttp.send(data);*/
	    // ==============End Floor Room Rack Shelf Bin upto variable Settings============
	}
	
</script>
<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="trimsissue_1" id="trimsissue_1" autocomplete="off" >
    <div style="width:740px;float:left; margin-left:10px">   
        <fieldset style="width:740px;">
        <legend>Trims Issue Entry</legend>
        	<fieldset style="width:740px;">
                <table width="678" cellspacing="2" cellpadding="0" border="0" id="tbl_master" align="center">
                    <tr>
                        <td colspan="3" align="right"><strong>Issue Return No</strong></td>
                        <td colspan="3" align="left">
                        	<input type="hidden" name="update_id" id="update_id" />
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="80" class="must_entry_caption"> Company </td>
                        <td width="150">
                            <? 
                                echo create_drop_down( "cbo_company_id", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/trims_issue_return_entry_controller*4', 'store','store_td', this.value);company_on_change(this.value);independence_basis_controll_function(this.value);" );
                            ?>
                            <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
	                        <input type="hidden" id="is_rate_optional" name="is_rate_optional">
	                        <input type="hidden" id="variable_lot" name="variable_lot" />
                        </td>
                        <td width="70" class="must_entry_caption"> Return Date </td>
                        <td width="150">
                            <input class="datepicker" type="text" style="width:120px" name="txt_return_date" id="txt_return_date"/>
                        </td>
                        <td class="must_entry_caption">Issue No</td>
                        <td>
                            <input name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:120px"  placeholder="Double Click to Search" onDblClick="openpage_issueNo();" readonly/>
                            <input type="hidden" name="issue_id" id="issue_id" />
                            <input type="hidden" name="cbo_basis" id="cbo_basis" />
                        </td>
                    </tr> 
                    <tr>
                    	<td>Challan No </td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:130px" >
                        </td>

                        <td  class="must_entry_caption">Store Name </td>
                        <td id="store_td">
                            <?
                            echo create_drop_down( "cbo_store_name", 132, $blank_array,"", 1, "-- Select --", $storeName, "" ); 
							//echo create_drop_down( "cbo_store_name", 132, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "",1 );
							?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="740" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls" align="center">
                <tr>
                    <td width="65%" valign="top">
                        <fieldset>
                        <legend>New Entry</legend>
                            <table cellpadding="0" cellspacing="2" width="100%">
                                <tr>
                                    <td>Item Group</td>
                                    <td>
                                    <?
                                        echo create_drop_down( "cbo_item_group", 146, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, " Display ", 0,  "",1 );
                                    ?>	
                                    </td>
                                   	<td  width="41" >Floor</td>
									<td id="floor_td">
										<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	 <td width="90">UOM</td>
                                    <td>
                                        <?
                                            echo create_drop_down( "cbo_uom", 146, $unit_of_measurement,"", 1, " Display ", '0', "",1 );
                                        ?>
                                    </td>
                                    <td  width="41">Room</td>
									<td id="room_td">
										<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Item Desc.</td>
                                    <td>
                                        <input type="text" name="txt_item_description" id="txt_item_description" class="text_boxes" style="width:135px" placeholder="Display" disabled />
                                        <input type="hidden" name="hidden_prod_id" id="hidden_prod_id" disabled/>
                                    </td>
                                     <td width="41">Rack</td>
                                    <td id="rack_td">
										<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td> 
                              	</tr> 
                              	<tr>
                              		<td>Item Size</td>   
                                    <td>
                                        <input type="text" name="txt_item_size" id="txt_item_size" class="text_boxes" style="width:135px;" placeholder="Display" disabled/>
                                    </td>
                                   
                                    <td  width="41">Shelf</td>
									<td id="shelf_td">
										<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
									
                              	</tr>
                                <tr>
                                    <td>Item Color</td>
                                    <td>
                                        <input type="text" name="txt_item_color" id="txt_item_color" class="text_boxes" style="width:135px;" placeholder="Display" disabled/>
                                    </td>
                                     <td  width="41">Bin</td>
									<td id="bin_td">
										<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                   
                                </tr>
                               <tr>
                               	 <td width="60" class="must_entry_caption">Return Qnty</td>
                                    <td>
                                        <input type="text" name="txt_return_qnty" id="txt_return_qnty" class="text_boxes_numeric" style="width:140px;" onClick="openmypage_returnQty()"placeholder="Single Click" readonly/>	
                                    </td>
                                    <td id="rate_th_td">Rate</td>
                                    <td id="rate_td">
                                        <input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:140px;" placeholder="Display" disabled/>
                                    </td>
                               	</tr>
                                <tr id="amount_td">
                                   <td>Amount</td>
                                    <td>
                                        <input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:140px;" placeholder="Display" disabled/>	
                                    </td>
                                </tr>
                               
                               <!--  <tr>
                                    <td>Rack</td>
                                    <td>
                                        <input type="text" name="txt_rack" id="txt_rack" class="text_boxes" style="width:135px" placeholder="Display" disabled>
                                    </td>
                                    <td>Shelf</td>
                                    <td>
                                        <input type="text" name="txt_shelf" id="txt_shelf" class="text_boxes_numeric" style="width:90px" placeholder="Display" disabled>
                                    </td>
                                </tr> -->
                             </table>
                        </fieldset>
					</td>
                    <td width="2%" valign="top"></td>
					<td width="28%" valign="top">
						<fieldset>
                        <legend>Display</legend>					
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >
                            	<tr>
                                	<td>Buyer Order</td>
                                    <td>
                                        <input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:100px;" placeholder="Display" disabled/>	
                                    </td>
                                </tr>				
                                <tr>
                                    <td>Issue Qty</td>						
                                	<td>
                                    	<input type="text" name="txt_issued_qnty" id="txt_issued_qnty" class="text_boxes_numeric" placeholder="Display" style="width:100px" disabled/>
                                    </td>
								</tr>
                                <tr>
                                    <td>Cumulative Return</td>
                                    <td><input type="text" name="txt_cumulative_returned" id="txt_cumulative_returned" class="text_boxes_numeric" placeholder="Display" style="width:100px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Net Used</td>
                                    <td><input type="text" name="txt_net_used" id="txt_net_used" class="text_boxes_numeric" placeholder="Display" style="width:100px" disabled/></td>
                                </tr>					
                            </table>                  
                       </fieldset>	
              		</td>
				</tr>
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_trims_issue", 0,1,"reset_form('trimsissue_1','div_details_list_view*list_fabric_desc_container','','','disable_enable_fields(\'cbo_company_id\');')",1);
                        ?>
                        <input type="hidden" name="save_data" id="save_data" readonly>
                        <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                        <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                        <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                        <input type="hidden" name="hidden_return_qnty" id="hidden_return_qnty" readonly>
                        <input type="hidden" name="txt_conversion_faction" id="txt_conversion_faction" />
                        <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        <input type="hidden" name="store_update_upto" id="store_update_upto">
                    </td>
                </tr>
                <tr>
                    <td colspan="6" align="center">
                        <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                    </td>
                </tr>
			</table>
            <div style="width:680px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    <div id="list_fabric_desc_container" style="width:490px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:15px"></div>
	</form>
</div>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>