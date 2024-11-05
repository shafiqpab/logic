<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims receive return
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	15-11-2014
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
echo load_html_head_contents("Grey Fabric Receive Return Info","../../", 1, 1, $unicode,1,1); 
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
	var page_link='requires/trims_receive_rtn_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split("_"); // mrr number
  		// master part call here
		
		fnResetForm();
		
		var posted_in_account=returnNumber[3]; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(returnNumber[0], "populate_master_from_data", "requires/trims_receive_rtn_controller"); 
		//list view call here
		show_list_view(returnNumber[0],'show_dtls_list_view','list_container_yarn','requires/trims_receive_rtn_controller','');
		set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
		//$("#tbl_master").find('input,select').attr("disabled", true);	
		$("#tbl_child").find('input,select').val('');
		disable_enable_fields( 'cbo_company_id', 1, "", "" ); 
 	}
}

 



//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input,select').attr("disabled", false);	
	set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1);
	reset_form('grey_fab_receive_rtn_1','list_container_yarn*list_product_container','','','','');
}

// popup for PI----------------------	
function openmypage_po()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var all_po_id = $('#all_po_id').val();
	var cbo_store_name=$('#cbo_store_name').val();
	
	if (form_validation('cbo_company_id*cbo_store_name','Company*Store Name')==false)
	{
		return;
	}
	
	var title = 'PO Info';	
	var page_link = 'requires/trims_receive_rtn_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&action=po_search_popup';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var hidden_order_id=this.contentDoc.getElementById("hidden_order_id").value; //Access form field with id="emailfield"
		var hidden_order_no=this.contentDoc.getElementById("hidden_order_no").value; //Access form field with id="emailfield"
		
		/*if(hidden_order_id!=all_po_id)
		{
			reset_form('','','txt_prod_id*txt_item_description*cbo_item_group*cbo_item_color*txt_brad_supp*txt_item_size*txt_return_qnty*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_issue_qnty*txt_conversion_faction*txt_cons_rate*txt_amount*cbo_uom*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_global_stock','','','');
		}*/
		
		$('#all_po_id').val(hidden_order_id);
		$('#txt_po_no').val(hidden_order_no);
		
		//show_list_view(hidden_order_id+'****'+cbo_store_name,'create_itemDesc_search_list_view','list_product_container','requires/trims_receive_rtn_controller','setFilterGrid(\'tbl_list_search\',-1);');
		
		disable_enable_fields( 'cbo_company_id*cbo_store_name', 1, "", "" ); 
	}
	
}


function openmypage_booking()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var all_po_id = $('#all_po_id').val();
	var cbo_store_name=$('#cbo_store_name').val();
	var cbo_receive_basis=$('#cbo_receive_basis').val();
	//alert(all_po_id);
	
	if (form_validation('cbo_company_id*cbo_store_name*txt_po_no*cbo_receive_basis','Company*Store Name*Po Number*Return Basis')==false)
	{
		return;
	}
	
	var title = 'Booking Info';	
	var page_link = 'requires/trims_receive_rtn_controller.php?cbo_company_id='+cbo_company_id+'&all_po_id='+all_po_id+'&cbo_receive_basis='+cbo_receive_basis+'&action=booking_search_popup';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var hidden_booking_id=this.contentDoc.getElementById("booking_id").value; //Access form field with id="emailfield"
		var hidden_booking_no=this.contentDoc.getElementById("booking_no").value; //Access form field with id="emailfield"
		
		reset_form('','','txt_prod_id*txt_item_description*cbo_item_group*cbo_item_color*txt_brad_supp*txt_item_size*txt_return_qnty*save_data*update_dtls_id*update_trans_id*previous_prod_id*hidden_issue_qnty*txt_conversion_faction*txt_cons_rate*txt_rcv_rate*txt_amount*cbo_uom*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_global_stock','','','');
		
		$('#txt_booking_id').val(hidden_booking_id);
		$('#txt_booking_no').val(hidden_booking_no);
		//alert(all_po_id);
		show_list_view(all_po_id+'****'+cbo_store_name+'**'+hidden_booking_id+'**'+hidden_booking_no+'**'+cbo_receive_basis,'create_itemDesc_search_list_view','list_product_container','requires/trims_receive_rtn_controller','setFilterGrid(\'tbl_list_search\',-1);');
		
		disable_enable_fields( 'txt_po_no', 1, "", "" ); 
	}
	
}


function set_form_data(data)
{
	var data=data.split("**");
	$('#txt_prod_id').val(data[0]);
	$('#txt_item_description').val(data[2]);
	
	$('#cbo_item_group').val(data[1]);
	
	$('#cbo_item_color').val(data[3]);
	//$('#gmts_color_id').val(data[4]);
	
	$('#txt_item_size').val(data[5]);
	//$('#gmts_size_id').val(data[6]);
	
	
	$('#txt_brad_supp').val(data[7]);
	
	$('#cbo_uom').val(data[8]);
	$('#txt_conversion_faction').val(data[16]);
	
	//$('#txt_rack').val(data[9]);
	//$('#txt_shelf').val(data[10]);
	
	//$('#txt_item_color_id').val(data[11]);
	$('#txt_global_stock').val(data[12]);
	
	$('#txt_cumulative_issued').val(data[13]);
	$('#txt_yet_to_issue').val(data[14]);
	$('#txt_fabric_received').val(data[15]);
	
	$('#txt_po_no').val(data[17]);
	$('#all_po_id').val(data[18]);
	$('#txt_cons_rate').val(data[19]);
	//alert(data[20]);
	$('#txt_rcv_rate').val(data[20]);
	$('#txt_book_rcv_qnty').val(data[21]);
	
	
	set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
	
}

function openmypage_issueQty()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var save_data = $('#save_data').val();
	var all_po_id = $('#all_po_id').val();
	var issueQnty = $('#txt_return_qnty').val();
	var prod_id = $('#txt_prod_id').val();
	var cbo_store_name = $('#cbo_store_name').val();
	var distribution_method = $('#distribution_method_id').val();
	var conversion_factor = $('#txt_conversion_faction').val()*1;
	var txt_rcv_rate = $('#txt_rcv_rate').val()*1;
	
	if (form_validation('cbo_company_id*cbo_store_name*txt_po_no*txt_item_description','Company*Store Name*Buyer Order*Item Description')==false)
	{
		return;

	}
		
	var title = 'PO Info';	
	var page_link = 'requires/trims_receive_rtn_controller.php?cbo_company_id='+cbo_company_id+'&save_data='+save_data+'&all_po_id='+all_po_id+'&issueQnty='+issueQnty+'&prev_method='+distribution_method+'&prod_id='+prod_id+'&conversion_factor='+conversion_factor+'&cbo_store_name='+cbo_store_name+'&action=po_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=420px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
		var tot_trims_qnty=this.contentDoc.getElementById("tot_trims_qnty").value; //Access form field with id="emailfield"
		var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
		var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
		var distribution_method=this.contentDoc.getElementById("distribution_method").value; //Access form field with id="emailfield"
		//alert(all_po_id + "__"+ all_po_no);
		$('#save_data').val(save_string);
		$('#txt_return_qnty').val(tot_trims_qnty);
		$('#txt_amount').val(number_format(tot_trims_qnty*txt_rcv_rate,2,'.',''));
		$('#all_po_id').val(all_po_id);
		$('#txt_po_no').val(all_po_no);
		$('#distribution_method_id').val(distribution_method);
		//$('#cbo_store_name').attr('disabled',true);
		
		if(all_po_id!="")
		{
			//get_php_form_data(all_po_id+"**"+$('#hidden_prod_id').val(), 'get_trim_cum_info', 'requires/trims_issue_entry_controller' );
		}
	}
}
	
	
	

function fnc_yarn_receive_return_entry(operation)
{
	if(operation==4)
	{
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_id').val()+'*'+$('#issue_mst_id').val()+'*'+report_title, "trims_receive_return_print", "requires/trims_receive_rtn_controller" ) 
		 return;
	}
	else
	{
		if ($("#is_posted_account").val()*1 == 1) {
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}
		
		if( form_validation('cbo_company_id*txt_return_date*cbo_return_source*cbo_return_to*cbo_store_name*txt_po_no*cbo_receive_basis*txt_booking_no*txt_item_description*txt_return_qnty','Company Name*Return Date*Return source*Return To*store name*Order No*Return Basis*WO/PI No*Item Description*Return Quantity')==false )
		{
			return;
		}
		
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_return_date').val(), current_date)==false)
		{
			alert("Receive Return Date Can not Be Greater Than Current Date");
			return;
		}
		
		/* var txt_return_qnty=$('#txt_return_qnty').val()*1;
		var txt_book_rcv_qnty=$('#txt_book_rcv_qnty').val()*1;
		if(txt_return_qnty > txt_book_rcv_qnty)
		{
			alert("Rrturn Quantity Not Allow Over MRR Quantity");return;
		} */

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
		
		var dataString = "txt_return_no*issue_mst_id*cbo_company_id*txt_return_date*cbo_return_to*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*txt_po_no*txt_item_description*txt_prod_id*cbo_item_group*cbo_item_color*txt_brad_supp*txt_item_size*txt_return_qnty*txt_cons_rate*txt_rcv_rate*txt_amount*cbo_uom*txt_remarks*save_data*update_dtls_id*update_trans_id*previous_prod_id*all_po_id*hidden_issue_qnty*distribution_method_id*txt_conversion_faction*cbo_return_source*cbo_receive_basis*txt_booking_no*txt_booking_id*txt_gate_pass_no*store_update_upto";

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);return;
		
		freeze_window(operation);
		http.open("POST","requires/trims_receive_rtn_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_receive_return_entry_reponse;
	}
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
			$("#txt_return_no").val(reponse[2]);
			$("#issue_mst_id").val(reponse[1]);
 			//$("#tbl_master :input").attr("disabled", true);
			disable_enable_fields( 'cbo_company_id', 1, "", "" ); // disable True
			disable_enable_fields( 'cbo_store_name*txt_po_no*txt_booking_no', 0, '', '' ); // disable False
			reset_form('','','txt_item_description*txt_prod_id*cbo_item_group*cbo_item_color*txt_brad_supp*txt_item_size*txt_return_qnty*txt_cons_rate*txt_rcv_rate*txt_amount*cbo_uom*txt_remarks*save_data*update_dtls_id*update_trans_id*previous_prod_id*all_po_id*hidden_issue_qnty*distribution_method_id*txt_conversion_faction*txt_fabric_received*txt_cumulative_issued*txt_yet_to_issue*txt_global_stock','','','');
			//reset_form('','','','','','');
			show_list_view(reponse[1],'show_dtls_list_view','list_container_yarn','requires/trims_receive_rtn_controller','');
			show_list_view(reponse[4]+'****'+reponse[5]+'**'+reponse[6]+'**'+reponse[7]+'**'+reponse[8],'create_itemDesc_search_list_view','list_product_container','requires/trims_receive_rtn_controller','setFilterGrid(\'tbl_list_search\',-1);');		
			//child form reset here after save data-------------//
			set_button_status(0, permission, 'fnc_yarn_receive_return_entry',1,1);
			//$("#tbl_child").find('input,select').val('');
			
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

function calc_tot_amt(amt)
{
	var rate=$("#txt_rcv_rate").val();
	$("#txt_amount").val((rate*1)*(amt*1));
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
    xmlhttp.open("POST", "requires/trims_receive_rtn_controller.php", true);
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
		
		var varible_string=return_global_ajax_value( data, 'varible_inventory', '', 'requires/trims_receive_rtn_controller');
		
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
				//$('#amount_td').css("display", "none");
				//$('#book_currency_td').css("display", "none");
			}
			else
			{
				$('#rate_td').css("display", "");
				//$('#amount_td').css("display", "");
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
			//$('#amount_td').css("display", "");
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
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="grey_fab_receive_rtn_1" id="grey_fab_receive_rtn_1" autocomplete="off" > 
    <div style="width:75%;">       
    <table width="80%" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:900px; float:left;">
                <legend>Trims Receive Return</legend>
                <br />
                 	<fieldset style="width:900px;">                                       
                        <table  width="850" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
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
                                     	echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_room_rack_self_bin('requires/trims_receive_rtn_controller*4', 'store','store_td', this.value);company_on_change(this.value);independence_basis_controll_function(this.value);" );
                                     	//load_drop_down( 'requires/trims_receive_rtn_controller',this.value, 'load_drop_down_store', 'store_td' );
                                    ?>
                                    <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
	                        		<input type="hidden" id="is_rate_optional" name="is_rate_optional">
	                        		<input type="hidden" id="variable_lot" name="variable_lot" />
                                    <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                                    <input type="hidden" name="store_update_upto" id="store_update_upto">
                                    <input type="hidden" name="distribution_method_id" id="distribution_method_id" readonly />
                                </td>
                                <td width="120" align="right" class="must_entry_caption">Return Date</td>
                                <td width="170"><input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:160px;" placeholder="Select Date" /></td>
                                <td class="must_entry_caption"  width="120" align="right"> Returned Source </td>
								<td>
									<?
									$knitting_source_custom = array(1 => "In-house", 2 => "In-bound Subcontract", 3 => "Out-bound");

									echo create_drop_down("cbo_return_source",170,$knitting_source_custom,"", 1, "-- Select --", 0,"load_drop_down( 'requires/trims_receive_rtn_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');",0,'1,3');
									?>
								</td>
                                
                          </tr>
                          <tr>
                          		<td align="right" class="must_entry_caption" width="120">Returned To</td>
                                <td id="knitting_com">
									<?
									echo create_drop_down( "cbo_return_to", 170, $blank_array,"",1, "-- Select --", 1, "" );
									?>
								</td>
								<td align="right" width="170" class="must_entry_caption">Store Name</td>
                                <td id="store_td" width="170">
                                <? 
                                echo create_drop_down( "cbo_store_name", 170, $blank_array,"", 1, "-- Select --", $storeName, "" ); 
                                //echo create_drop_down( "cbo_store_name", 150, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "",0 );
                                ?>
                                </td>
                                <td align="right">Gate Pass No.</td>
                                <td><input type="text" name="txt_gate_pass_no" id="txt_gate_pass_no" class="text_boxes" style="width:160px" /></td>
                          </tr>
                        </table>
                    </fieldset>
                    <br />
                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                     <tr>
                   	   <td valign="top" align="center">
                         <fieldset style="width:560px; float:left">  
                         <legend>Return Item Info</legend>                                     
                            <table  width="550" cellspacing="2" cellpadding="0" border="0">
                                <tr>
                                	<td align="right" class="must_entry_caption" width="120">Order NO </td>
                                    <td width="150">
                                    
                                    <input class="text_boxes" type="text" name="txt_po_no" id="txt_po_no" onDblClick="openmypage_po()" placeholder="Double Click To Search" style="width:140px;" readonly />
                                    </td>
                                    <td align="right" width="71" >Floor</td>
									<td id="floor_td">
										<? echo create_drop_down( "cbo_floor", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                   
                                </tr>
                                <tr>
                                    <td align="right" class="must_entry_caption">Return Basis&nbsp;</td>
                                    
                                    <td>
                                    <? 
                                    //set_receive_basis();
										echo create_drop_down("cbo_receive_basis",150,$receive_basis_arr,"",1,"-- Select --",0,"","",'1,2');
									?>
                                    </td> 
                                   <td align="right" width="71">Room</td>
									<td id="room_td">
										<? echo create_drop_down( "cbo_room", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	<td align="right" class="must_entry_caption">WO/PI NO </td>
                                    <td>
                                    
                                    <input class="text_boxes" type="text" name="txt_booking_no" id="txt_booking_no" onDblClick="openmypage_booking()" placeholder="Double Click To Search" style="width:140px;" readonly />
                                    <input type="hidden" id="txt_booking_id" name="txt_booking_id" />
                                    </td>
                                    <td align="right" width="71">Rack</td>
									<td id="rack_td">
										<? echo create_drop_down( "txt_rack", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	<td align="right">Color</td>
                                    <td>
                                    <?
                                    echo create_drop_down( "cbo_item_color", 150, "select id,color_name from lib_color where status_active=1 and is_deleted=0 order by color_name", "id,color_name", 1, "-- Select --", 0,  "",1 );
                                    ?>
                                    </td>
                                    <td align="right" width="41">Shelf</td>
									<td id="shelf_td">
										<? echo create_drop_down( "txt_shelf", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	<td  align="right">Size</td>
                                    <td >
                                    <input type="text" id="txt_item_size" name="txt_item_size" class="text_boxes" style="width:140px;" readonly disabled >
                                    </td>
                                    <td align="right" width="41">Box/Bin</td>
									<td id="bin_td">
										<? echo create_drop_down( "cbo_bin", 150,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	<td align="right">Item Group &nbsp;</td>
                                    <td>
                                    
                                    <?
                                    echo create_drop_down( "cbo_item_group", 150, "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0,  "",1 );
                                    ?>
                                    </td>
                                	<td  align="right">UOM</td>
                                    <td >
                                    
                                    <? echo create_drop_down( "cbo_uom", 150, $unit_of_measurement,"", 1, "Display", 0, "",1 ); ?>
                                    </td> 
                                </tr>
                                <tr>
                                    <td align="right" class="must_entry_caption">Item Description&nbsp;</td>
                                    
                                    <td colspan="3">
                                    <input class="text_boxes" type="text" name="txt_item_description" id="txt_item_description" style="width:365px;" placeholder="Display" readonly disabled/>
                                    <input type="hidden" id="txt_prod_id" name="txt_prod_id" />
                                    </td>
                                </tr>
                                <tr>
                                	<td  align="right" class="must_entry_caption">Returned Qnty&nbsp;</td>
                                    <td >
                                    
                                    <input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:135px;" placeholder="Double Click To Search" readonly onDblClick="openmypage_issueQty()"/>
                                    </td>
                                    <td  align="right">Brand/Sup Ref</td>
                                    <td ><input class="text_boxes" type="text" name="txt_brad_supp" id="txt_brad_supp" style="width:140px;" placeholder="Display" readonly disabled  /></td>
                                   
                                </tr>
                               
                                <tr id="rate_td">
                                	<td  align="right">Rate</td>
                                    <td >
                                    <input class="text_boxes_numeric" type="text" name="txt_rcv_rate" id="txt_rcv_rate" style="width:140px;" placeholder="Display" readonly disabled   />
                                    <input class="text_boxes_numeric" type="hidden" name="txt_cons_rate" id="txt_cons_rate" />
                                    
                                    </td>
                                    <td  align="right">Amount</td>
                                    <td >
                                    <input class="text_boxes_numeric" type="text" name="txt_amount" id="txt_amount" style="width:140px;" placeholder="Display" readonly disabled  />
                                    </td>                                 
                                </tr>
                                <tr>
                                    <td  align="right">Remarks&nbsp;</td>
                                    <td colspan="3"><input class="text_boxes" type="text" name="txt_remarks" id="txt_remarks" style="width:365px;" placeholder="Write" /></td>
                                </tr>
                            </table>
                         </fieldset>
                       	 <fieldset style="width:250px; float:left; margin-left:5px">  
                           <legend>Display</legend>                                     
                           <table  width="250" cellspacing="2" cellpadding="0" border="0" id="display" >                           
                            <tr>
                                  <td>Trims Received</td>
                                  
                                  <td width="130">
                                  <input  type="text" name="txt_fabric_received" id="txt_fabric_received" class="text_boxes_numeric" style="width:130px" readonly disabled  />
                                  <input type="hidden" name="txt_book_rcv_qnty" id="txt_book_rcv_qnty" disabled />
                                  </td>
                            </tr>                        
                            <tr>
                                <td>Cumulative Return</td>
                                <td>
                                <input  type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes_numeric" style="width:130px"  readonly disabled />
                                <input type="hidden" id="hidden_receive_trans_id" name="hidden_receive_trans_id" readonly disabled  />
                                <input type="hidden" id="before_receive_trans_id" name="before_receive_trans_id" readonly disabled  />
                                </td>
                            </tr>
                            <tr style="display:none">
                                <td>Yet to Issue</td>
                                <td>
                                    <input  type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:130px"  readonly disabled />
                                </td>
                            </tr> 
                            <tr>
                                <td>Global Stock</td>
                                <td>
                                <input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes_numeric" style="width:130px" disabled /></td>
                            </tr>
                      </table>
                      </fieldset>
                   	   </td>
                    </tr>
                </table>                
               	<table cellpadding="0" cellspacing="1" width="100%">
                	<tr> 
                       <td colspan="6" align="center"></td>				
                	</tr>
                	<tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
							<? echo load_submit_buttons( $permission, "fnc_yarn_receive_return_entry", 0,1,"fnResetForm()",1);?>
                            
                            
                            <!-- details table id for update -->
                            <input type="hidden" name="save_data" id="save_data" readonly>
                            <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                            <input type="hidden" name="update_trans_id" id="update_trans_id" readonly>
                            <input type="hidden" name="previous_prod_id" id="previous_prod_id" readonly>
                            <input type="hidden" name="all_po_id" id="all_po_id" readonly>
                            <input type="hidden" name="hidden_issue_qnty" id="hidden_issue_qnty" readonly>
                            <input type="hidden" name="txt_conversion_faction" id="txt_conversion_faction" />
                            <!-- -->
                        </td>
                   </tr> 
                   <tr>
                        <td colspan="6" align="center">
                            <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                        </td>
                    </tr>
                </table>                 
              	</fieldset>
              	<fieldset>
    			<div style="width:900px;" id="list_container_yarn"></div>
    		  	</fieldset>
           </td>
         </tr>
    </table>
    </div>
    <div id="list_product_container" style="max-height:500px; width:25%; overflow:auto; float:left; padding-top:5px; margin-top:5px; margin-left:10px; position:relative;"></div>  
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
