<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create 
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	02-11-2013
Updated by 		:  	Kausar		
Update date		: 			/	11-12-2013 (Creating report	)
QC Performed BY	:	
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
//========== user credential end ==========
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Receive Info","../../", 1, 1, $unicode,1,1); 

?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

<?
if($_SESSION['logic_erp']['data_arr'][26])
{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][26] );
	echo "var field_level_data= ". $data_arr . ";\n";
}

?>

function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/general_item_receive_return_entry_controller.php?action=mrr_popup&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value;
		//alert(mrrNumber);
  		// master part call here
		set_button_status(0, permission, 'fnc_general_receive_return_entry',1,1);
		get_php_form_data(mrrNumber, "populate_data_from_data", "requires/general_item_receive_return_entry_controller");  		
 		$("#tbl_child").find('input,select').val('');
 	}
}
//txt_return_value
function fn_calculateAmount(qnty)
{
	var rate = $("#txt_return_rate").val();
	var rcvQnty = $("#txt_mrr_stock").val();
	var total_qty=$("#txt_yet_to_issue").val();
	//var current_issue=$("#prev_return_qnty").val();
	//total_qty=(total_qty*1)+(current_issue*1);
	
	if(qnty=="" || rate=="" || rcvQnty*1<qnty*1)
	{
		//alert(total_qty);
		alert("Return Quantity Over MRR Stock");
		$('#txt_return_qnty').val(0);
		$('#txt_return_value').val(0);
		return;
	}
	else if(($("#txt_global_stock").val()*1)<(qnty*1))
	{
		//alert($("#txt_global_stock").val());
		alert("Return Quantity Over Global Stock");
		$('#txt_return_qnty').val(0);
		$('#txt_return_value').val(0);
		return;
	}
	else
	{
		//alert(qnty);		
		var amount = rate*qnty;
		$('#txt_return_value').val(number_format_common(amount,"","",1));
	}
}
//Save Update Delete
function fnc_general_receive_return_entry(operation)
{
	//alert($("#txt_return_rate").val());return;
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		var variable_string_inventory=$('#variable_string_inventory').val();
		print_report( $('#cbo_company_id').val()+'*'+$('#txt_mrr_retrun_id').val()+'*'+report_title+'*'+variable_string_inventory, "general_item_receive_return_print", "requires/general_item_receive_return_entry_controller" ) 
		return;
	}
	
	if ($("#is_posted_account").val()*1 == 1) {
		alert("Already Posted In Accounting. Save Update Delete Restricted.");
		return;
	}

	if(operation==2){
		 
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+$('#txt_mrr_retrun_no').val(), "general_item_receive_return_print2", "requires/general_item_receive_return_entry_controller" ) 		 
		return;
	}

	
	if( form_validation('cbo_company_id*cbo_return_to*txt_return_date*txt_mrr_no*cbo_store_name*txt_item_category*txt_return_qnty*txt_return_value*txt_return_rate','Company Name*Return To*Return Date*Received ID*txt_mrr_no*Store Name*Item Category*Retuned Qnty*Return Value*Rate')==false )
	{
		return;
	}

	var variable_lot=$('#variable_lot').val()*1;
	var cbo_item_category=$('#category').val()*1;
	var txt_lot=$('#txt_lot').val();
	if(variable_lot==1 && cbo_item_category==22 && txt_lot=="")
	{
		alert("Lot Maintain Mandatory.");
		$('#txt_lot').focus();
		return;
	}

	var current_date='<? echo date("d-m-Y"); ?>';
	if(date_compare($('#txt_return_date').val(), current_date)==false)
	{
		alert("Receive Return Date Can not Be Greater Than Today");
		return;
	}		
	if($("#txt_return_qnty").val()*1>$("#txt_mrr_stock").val()*1)
	{
		alert("Return Quantity Can not be Greater Than Current Stock.");
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

	var dataString = "txt_mrr_retrun_no*cbo_company_id*cbo_return_to*txt_return_date*txt_received_id*txt_mrr_no*txt_challan_no*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*txt_item_category*txt_item_group*txt_item_description*txt_return_qnty*txt_return_value*txt_return_rate*txt_mrr_stock*txt_uom*category*store*uom*txt_prod_id*before_prod_id*update_id*transaction_id*txt_serial_id*txt_serial_no*before_serial_id*check_prod_id*txt_mrr_retrun_id*hidden_receive_trans_id*before_receive_trans_id*prev_return_qnty*txt_global_stock*txt_remark*txt_pi_no*pi_id*variable_lot*txt_lot*is_issueRtn*txt_issueRtn_rate*store_update_upto";
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
	freeze_window(operation);
	// alert(data); return;
	http.open("POST","requires/general_item_receive_return_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_general_receive_return_entry_reponse;	
}

function fnc_general_receive_return_entry_reponse()
{	
	if(http.readyState == 4)
	{	  		
		var reponse=trim(http.responseText).split('**');
		//alert(http.responseText);
		//release_freezing();return;

		if(reponse[0]==50)
		{
			alert("Serial No. Not Over Receive Qnty");
			release_freezing();
			return;
		} 
		else if(reponse[0]==60)
		{
			alert("Same Product Not Entry Within Same MRR");
			release_freezing();
			return;
		}
		else if(reponse[0]==20)
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
		else if(reponse[0]*1==16*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==17*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		 		
		if(reponse[0]==0)
		{
			$("#txt_mrr_retrun_no").val(reponse[1]);
			$("#txt_mrr_retrun_id").val(reponse[2]);
 			//$("#tbl_master :input").attr("disabled", true);
			show_msg(reponse[0]);
			disable_enable_fields( 'cbo_company_id*txt_mrr_no*cbo_return_to', 1, "", "" );
			//disable_enable_fields( 'txt_mrr_retrun_no', 0, "", "" ); // disable false
			//$("#tbl_master").find('input,select').attr("disabled",true);
			var variable_string_inventory=$('#variable_string_inventory').val(); 		
			show_list_view(reponse[1]+'**'+reponse[2]+'**'+variable_string_inventory,'show_dtls_list_view','list_container_general','requires/general_item_receive_return_entry_controller','');
			$("#tbl_child").find('input,select').val('');
			set_button_status(0, permission, 'fnc_general_receive_return_entry',1,1);
			release_freezing();
		}
		if( reponse[0]==1)
		{
			disable_enable_fields( 'cbo_company_id*txt_mrr_no*cbo_return_to', 1, "", "" );
			//disable_enable_fields( 'txt_mrr_retrun_no', 0, "", "" ); // disable false
			//$("#tbl_master").find('input,select').attr("disabled",true);	
			show_msg(reponse[0]);
			show_list_view(reponse[1]+'**'+reponse[2],'show_dtls_list_view','list_container_general','requires/general_item_receive_return_entry_controller','');
			$("#tbl_child").find('input,select').val('');
			set_button_status(0, permission, 'fnc_general_receive_return_entry',1,1);
			release_freezing();
			
		}
		else if(reponse[0]==2)
		{
			if(reponse[3]==1)
			{
				show_msg(reponse[0]);
				release_freezing();
				location.reload();
			}
			if(reponse[3]==2)
			{
				disable_enable_fields( 'cbo_company_id*txt_mrr_no*cbo_return_to', 1, "", "" );
				//disable_enable_fields( 'txt_mrr_retrun_no', 0, "", "" ); // disable false
				//$("#tbl_master").find('input,select').attr("disabled",true);	
				show_msg(reponse[0]);
				show_list_view(reponse[1]+'**'+reponse[2],'show_dtls_list_view','list_container_general','requires/general_item_receive_return_entry_controller','');
				$("#tbl_child").find('input,select').val('');
				set_button_status(0, permission, 'fnc_general_receive_return_entry',1,1);
				release_freezing();
			}
		}
		else if(reponse[0]==10)
		{
			show_msg(reponse[0]);
			release_freezing();
			return;
		}		
	}
}
// Return ID POPUP

function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/general_item_receive_return_entry_controller.php?action=return_number_popup&company='+company; 
	var title="Search Return Number Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split("_"); // mrr number
		//alert(returnNumber[1]);return;
  		// master part call here
		$("#txt_mrr_retrun_id").val(returnNumber[1]);
		
		var posted_in_account=returnNumber[2]; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(returnNumber[1], "populate_master_from_data", "requires/general_item_receive_return_entry_controller"); 
		var variable_string_inventory=$('#variable_string_inventory').val(); 		
		show_list_view(returnNumber[0]+"**"+returnNumber[1]+"**"+variable_string_inventory,'show_dtls_list_view','list_container_general','requires/general_item_receive_return_entry_controller','');
		set_button_status(0, permission, 'fnc_general_receive_return_entry',1,1);
		$("#tbl_child").find('input,select').val('');
		//disable_enable_fields( 'txt_return_no*cbo_company_id*', 0, "", "" ); // disable false
		
		$("#tbl_master").find('input,select').attr("disabled",true);
 	}
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input,select').attr("disabled", false);	
	set_button_status(0, permission, 'fnc_general_receive_return_entry',1);
	reset_form('genralitem_receive_return_1','list_container_general*list_product_container','','','','');
}

function popup_serial()
{
	if( form_validation('cbo_company_id*txt_item_description','Company Name*Item Description')==false )
	{
		return;
	}
	var serialStringNo = $("#txt_serial_no").val();
	var serialStringID = $("#txt_serial_id").val();
	var current_prod_id = $("#txt_prod_id").val();
	var txt_received_id = $("#txt_received_id").val();
	 //alert(serialStringID)
	var page_link="requires/general_item_receive_return_entry_controller.php?action=serial_popup&serialStringNo="+serialStringNo+"&serialStringID="+serialStringID+"&current_prod_id="+current_prod_id+"&txt_received_id="+txt_received_id; 
	var title="Serial Popup";	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var txt_stringId=this.contentDoc.getElementById("txt_string_id").value;  
		var txt_stringNo=this.contentDoc.getElementById("txt_string_no").value;
 		$("#txt_serial_no").val(txt_stringNo);
		$("#txt_serial_id").val(txt_stringId);
  	}
}

function company_onchange(company) 
{
	// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
    /*var data='cbo_company_id='+company+'&action=upto_variable_settings';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto").value = this.responseText;				
        }
    }
    xmlhttp.open("POST", "requires/general_item_receive_return_entry_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);*/
    // ==============End Floor Room Rack Shelf Bin upto variable Settings============
	
	var varible_string=return_global_ajax_value( company, 'varible_inventory', '', 'requires/general_item_receive_return_entry_controller');
	
	var varible_string_ref=varible_string.split("**");
	if(varible_string_ref[0])
	{
		$('#variable_string_inventory').val(varible_string_ref[1]);
		if(varible_string_ref[1]==1)
		{
			$('#rate_td').css("display", "none");
			$('#amount_td').css("display", "none");
		}
		else
		{
			$('#rate_td').css("display", "");
			$('#amount_td').css("display", "");
		}
			
	}
	else
	{
		$('#variable_string_inventory').val("");
		$('#rate_td').css("display", "");
		$('#amount_td').css("display", "");
	}
	$('#store_update_upto').val(varible_string_ref[2]);
	$('#variable_lot').val(varible_string_ref[3]);
}


function openmypage_pi()
{
	if( form_validation('cbo_company_id*txt_mrr_no','Company Name*MRR No')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();
	var mrr_id = $("#txt_received_id").val();
	var received_entry_form = $("#received_entry_form").val();

	page_link='requires/general_item_receive_return_entry_controller.php?action=piwo_popup&company='+company+'&mrr_id='+mrr_id+'&received_entry_form='+received_entry_form;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'PI/WO Search', 'width=550px, height=370px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var piID=this.contentDoc.getElementById("pi_wo_id").value; // pi table id
		var piNumber=this.contentDoc.getElementById("pi_wo_no").value; // pi number
		
		$("#pi_id").val(piID);
		$("#txt_pi_no").val(piNumber);
	}		
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
<? echo load_freeze_divs ("../../",$permission); ?><br />
<form name="genralitem_receive_return_1" id="genralitem_receive_return_1" autocomplete="off" data-entry_form="26"> 
    <div style="width:1250px;">  
    <table width="880" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:850px; float:left;">
                <legend>General Item Receive Return</legend>
                <br />
                 	<fieldset style="width:850px;"> 
                    <input type="hidden" id="transaction_id" name="transaction_id" />                                      
                        <table  width="800" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="6" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Return ID</b>
                                	<input type="text" name="txt_mrr_retrun_no" id="txt_mrr_retrun_no" class="text_boxes" style="width:150px" placeholder="Double Click To Search" onDblClick="open_returnpopup()" readonly />
                                    <input type="hidden" name="txt_mrr_retrun_id" id="txt_mrr_retrun_id"  />
                                    <!--<input type="text" id="hidden_mrr_id" name="hidden_mrr_id" value="" />-->
                                </td>
                           </tr>
                           <tr>
                                   <td  width="120" class="must_entry_caption">Company Name </td>
                                   <td width="160">
                                        <? 
                                         echo create_drop_down( "cbo_company_id", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_room_rack_self_bin('requires/general_item_receive_return_entry_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'store','store_td', this.value);company_onchange(this.value)" );
                                        ?>
                                        <input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
                                        <input type="hidden" id="variable_lot" name="variable_lot" />
                                        <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                        				<input type="hidden" name="store_update_upto" id="store_update_upto" />
                                   </td>
                                   <td width="120" align="" class="must_entry_caption"> Return Date </td>
                                   <td width="150">
                                   <input type="text" name="txt_return_date" id="txt_return_date" class="datepicker" style="width:150px;"  value="<? echo date("d-m-Y"); ?>" />
                                       
                                   </td>
                                   <td width="120" align="" class="must_entry_caption">Received ID</td>
                                   <td width="150">
								   <input class="text_boxes"  type="text" name="txt_mrr_no" id="txt_mrr_no" style="width:150px;" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />						   		
                                   		<input type="hidden" name="txt_received_id" id="txt_received_id" />
                                        <input type="hidden" name="received_entry_form" id="received_entry_form" />
                                        <input type="hidden" name="txt_received_trans_id" id="txt_received_trans_id" />
										<input type="hidden" name="is_issueRtn" id="is_issueRtn" />										
								  </td>
                            </tr>
                            <tr>
                                <td  width="120" align="" class="must_entry_caption"> Challan No</td>
                                <td width="150"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:150px" ></td>
                               <td width="120" align="" class="must_entry_caption">Returned To</td>
                               <td width="150">
                                <?                                    
                                echo create_drop_down( "cbo_return_to", 160, "select id,supplier_name from lib_supplier order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
                                ?>
                               </td>
                               <td width="120" align="">WO/PI NO</td>
                               <td width="150">
                               <input class="text_boxes" type="text" name="txt_pi_no" id="txt_pi_no" onDblClick="openmypage_pi()" placeholder="Double Click To Search" style="width:160px;" readonly />
                               <input type="hidden" name="pi_id" id="pi_id"/>
                               </td>
                            </tr>
                           </table>
                    </fieldset>
                    <br />
                    
                    
                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                    <tr>
                    <td width="49%" valign="top">
                   	  <fieldset style="width:800px;">  
                        <legend>New Receive Return Item</legend>                                     
                        
						<table width="250" cellspacing="2" cellpadding="0" border="0" style="float:left"> 
                            <tr>    
                                        <td width="130" class="must_entry_caption">Store Name</td>
                                        <input type="hidden" id="store" name="store" value="" />
                                        <td width="" id="store_td"> 
                                         <? echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select --", $storeName, "" ); ?>
                                        <!-- <input type="text" name="cbo_store_name" id="cbo_store_name" class="text_boxes" style="width:120px;" readonly/> -->        
                                        </td>
                                </tr>
                                <tr>    
                                    <td  width="41" >Floor</td>
									<td id="floor_td">
										<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>    
                                	<td width="41">Room</td>
									<td id="room_td">
										<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	<td  width="41">Rack</td>
									<td id="rack_td">
										<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	<td  width="41">Shelf</td>
									<td id="shelf_td">
										<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr>
                                	<td  width="41">Bin/Box</td>
									<td id="bin_td">
										<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                              
                            </table>
                        <table width="220" cellspacing="2" cellpadding="0" border="0" style="float:left">
                            	  
                                <tr>    
                                        <td class="must_entry_caption">Rtn. Qnty.</td>
                                    	<td>
                                        <input name="txt_return_qnty" id="txt_return_qnty" class="text_boxes_numeric" type="text" style="width:120px;" placeholder="Entry" onKeyUp="fn_calculateAmount(this.value)" />
                                       <input type="hidden" id="prev_return_qnty" name="prev_return_qnty" >
                                        </td>
                                </tr>
                                <tr> 
                                	<td width="150">MRR Stock</td>
                                    <td width="130"><input type="text" name="txt_mrr_stock" id="txt_mrr_stock" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>     
                                </tr>
                                <tr id="rate_td"> 
                                		<td class="must_entry_caption">Rate</td>   
                                        <td >
                                        <input name="txt_return_rate" id="txt_return_rate" class="text_boxes_numeric" type="text" style="width:120px;" readonly/>
                                         <input name="privous_rate" id="privous_rate" class="text_boxes_numeric" type="hidden" style="width:120px;" />
                                        </td>
                                </tr>
                                <tr> 
                                	<td>Item Group.</td>
                                        <td id="item_group_td">
                                         <input type="text" name="txt_item_group" id="txt_item_group" class="text_boxes" style="width:120px;" readonly/>
                                        </td>
                                </tr>
                                <tr>
                                	<td>Item Desc.</td>
                                   <td>
                                   <input name="txt_item_description" id="txt_item_description" class="text_boxes" type="text" style="width:120px;" placeholder="" readonly />
                                   <input type="hidden" name="txt_prod_id" id="txt_prod_id" readonly disabled />
                                   <input type="hidden" name="check_prod_id" id="check_prod_id" readonly disabled />
                                   </td>
                                </tr>
                                <tr>
                                	<td>Remark</td>
									 <td width="150">
                                     <input type="text" name="txt_remark" id="txt_remark" class="text_boxes" style="width:120px;" />
                                     </td>
                                </tr>
                        </table>
                            
                        <table width="280" cellspacing="2" cellpadding="0" border="0" style="float:left">
                        
                        		 <tr>    
                                        <td>Serial No</td>
                                        <td>
                                        <input name="txt_serial_no" id="txt_serial_no" class="text_boxes" type="text" style="width:120px;" placeholder="Double Click" onDblClick="popup_serial()" />
                                    	<input type="hidden" id="txt_serial_id" value="" />
                                        </td>
                                </tr>
                                <tr>
                                		<td class="must_entry_caption">Item Category</td>
                                        <td id="">
                                        <input type="hidden" id="category" name="category" value="" />
                                        <input type="text" name="txt_item_category" id="txt_item_category" class="text_boxes" style="width:120px;" readonly/>
                                        </td> 
                                </tr>
                                <tr style="display:none"> 
                                    <td>Cumulative Return</td>
									 <td width="150">
                                     <input type="text" name="txt_cumulative_issued" id="txt_cumulative_issued" class="text_boxes" style="width:120px;" readonly disabled />
                                    <input type="hidden" id="hidden_receive_trans_id" name="hidden_receive_trans_id" />
                                    <input type="hidden" id="before_receive_trans_id" name="before_receive_trans_id"  />
                                     </td>
                                </tr>
                                 <tr style="display:none"> 
                                    <td>Yet To Issue</td>
									 <td width="150" id="uom_td">
                                     <input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes" style="width:120px;" readonly disabled />
                                     </td>
                                </tr>
                                 <tr> 
                                 	<td>Global Stock</td>
                                  
									 <td width="150" id="uom_td">
                                     <input type="text" name="txt_global_stock" id="txt_global_stock" class="text_boxes_numeric" style="width:120px;" readonly disabled />
									 <input type="hidden" name="txt_issueRtn_rate" id="txt_issueRtn_rate" />
									 <input type="hidden" name="txt_issue_trans_id" id="txt_issue_trans_id" />
                                     </td>
                                </tr>
                                <tr id="amount_td"> 
                                	 <td class="must_entry_caption">Rtn Value</td>   
                                      <td ><input name="txt_return_value" id="txt_return_value" class="text_boxes_numeric" type="text" style="width:120px;" readonly/></td>
                                </tr>
                                <tr>
                                	<td>Cons. UOM</td>
                                  
									 <td width="150" id="uom_td"><input type="text" name="txt_uom" id="txt_uom" class="text_boxes" style="width:120px;" readonly disabled /></td>
									 <input type="hidden" id="uom" name="uom" value="" />
                                </tr>
                                <tr>
                                	<td>Lot</td>
									 <td width="150">
                                     <input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:120px;" readonly disabled />
                                     </td>
                                </tr>
                            </table>                            
                    </fieldset>
                    </td>
                    </tr>
                </table>                
               	<table cellpadding="0" cellspacing="1" width="100%">
                	<tr> 
                       <td colspan="6" align="center">
                       
                       </td>				
                	</tr>
                	<tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                              <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_general_receive_return_entry", 0,1,"fnResetForm()",1);?>
							 <input type="button" style="width:100px;" name="print2" id="print2"  onClick="fnc_general_receive_return_entry(2)" class="formbutton" value="Print 2" />
                        </td>						
                        <input type="hidden" id="txt_cons_quantity" name="txt_cons_quantity" value=""/>
                        <input type="hidden" id="before_serial_id" name="before_serial_id" value=""/>
                        <input type="hidden" id="before_prod_id" name="before_prod_id" value="" />
                        <input type="hidden" id="update_id" name="update_id" value="" />
                   </tr> 
                   <tr>
                        <td colspan="6" align="center">
                            <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                        </td>
                    </tr>
                </table>                 
              	</fieldset>
         </td>
         </tr>
        </table>
         <div id="list_product_container" style="max-height:auto; width:350px; overflow: hidden; padding-top:0px; margin-top:0px; position:relative;"></div>
		 <div style="clear:both"></div> 
         <br />
    	<div style="width:1250px; margin-left:-350px;" id="list_container_general"> </div> 
    </div>
    
	</form>    
    </div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
 