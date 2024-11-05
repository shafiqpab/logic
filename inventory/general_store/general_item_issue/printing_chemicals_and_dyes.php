<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create  
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	03-09-2013
Updated by 		: 	Kausar,Jahid	
Update date		: 	30-10-2013	   
QC Performed BY	:	Creating Report & List view Repair	
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
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
echo load_html_head_contents("Yarn Receive Info","../../../", 1, 1, $unicode,1,1); 
?>	
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
 	
function popup_description()
{
	if( form_validation('cbo_company_name*cbo_store_name','Company Name*Store Name')==false )
	{
		return;
	}
	var company_id = $("#cbo_company_name").val();
	var cbo_store_name = $("#cbo_store_name").val();
 	var page_link="requires/general_item_issue_controller.php?action=item_description_popup&company_id="+company_id+"&cbo_store_name="+cbo_store_name+'&item_cat=22';
	var title="Item Description Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=910px,height=350px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
 		var item_description_all=this.contentDoc.getElementById("item_description_all").value;//alert(item_description_all); 
		var splitArr = item_description_all.split("*");
		//alert(splitArr[4]);
 		$("#current_prod_id").val(splitArr[0]); 
 		$("#txt_item_desc").val(splitArr[1]);
		$("#txt_current_stock").val(splitArr[2]);
		$("#cbo_item_category").val(splitArr[3]);
		$("#cbo_item_group").val(splitArr[4]);
		$("#cbo_store_name").val(splitArr[5]);


		$("#txt_brand").val(splitArr[6]);//new dev
		$("#cbo_origin").val(splitArr[7]);//new dev
		$("#txt_model").val(splitArr[8]);//new dev
		$("#cbo_floor").val(splitArr[9]).attr('disabled','disabled');
		$("#cbo_room").val(splitArr[10]).attr('disabled','disabled');
		$("#cbo_rack").val(splitArr[11]).attr('disabled','disabled');
		$("#cbo_self").val(splitArr[12]).attr('disabled','disabled');
		$("#cbo_binbox").val(splitArr[13]).attr('disabled','disabled');
		$("#cbo_uom").val(splitArr[14]).attr('disabled','disabled');
		$("#txt_lot").val(splitArr[15]).attr('disabled','disabled');
		$("#cbo_store_name").attr('disabled',true);
		//load_drop_down( 'requires/general_item_issue_controller', splitArr[4], 'load_drop_down_uom', 'uom_td' );
  	}
}

function popup_serial()
{
	if( form_validation('cbo_company_name*txt_item_desc','Company Name*Item Description')==false )
	{
		return;
	}
	var serialStringNo = $("#txt_serial_no").val();
	var serialStringID = $("#txt_serial_id").val();
	var current_prod_id = $("#current_prod_id").val();
	var txt_received_id = $("#txt_received_id").val();
	 //alert(serialStringID)
	var page_link="requires/general_item_issue_controller.php?action=serial_popup&serialStringNo="+serialStringNo+"&serialStringID="+serialStringID+"&current_prod_id="+current_prod_id+"&txt_received_id="+txt_received_id; 
	var title="Serial Popup";	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=300px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var txt_stringId=this.contentDoc.getElementById("txt_string_id").value;  
		var txt_stringNo=this.contentDoc.getElementById("txt_string_no").value;
 		$("#txt_serial_no").val(txt_stringNo);
		$("#txt_serial_id").val(txt_stringId);
  	}
}

function fn_order()  
{ 
	if(form_validation('cbo_company_name','Company')==false)
	{
		return;
	}  
	var company = $("#cbo_company_name").val() ;
	var title = 'PO Info';	
	var page_link = 'requires/general_item_issue_controller.php?company='+company+'&action=order_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var po_string=this.contentDoc.getElementById("hidden_string").value;	 
		var po_string_arr = po_string.split("_");
  		$('#txt_order_id').val(po_string_arr[0]);
 		$('#txt_buyer_order').val(po_string_arr[1]);
	}
}

function fn_room_rack_self_box()
{
	if( $("#cbo_room").val()!=0 )  
		disable_enable_fields( 'cbo_rack', 0, '', '' ); 
	else
	{
		reset_form('','','cbo_rack*cbo_self*cbo_binbox','','','');
		disable_enable_fields( 'cbo_rack*cbo_self*cbo_binbox', 1, '', '' ); 
	}
	if( $("#cbo_rack").val()!=0 )  
		disable_enable_fields( 'cbo_self', 0, '', '' ); 
	else
	{
		reset_form('','','cbo_self*cbo_binbox','','','');
		disable_enable_fields( 'cbo_self*cbo_binbox', 1, '', '' ); 	
	}
	if( $("#cbo_self").val()!=0 )  
		disable_enable_fields( 'cbo_binbox', 0, '', '' ); 
	else
	{
		reset_form('','','cbo_binbox','','','');
		disable_enable_fields( 'cbo_binbox', 1, '', '' ); 	
	}
}


function generate_report_file(data,action,page)
		{
			window.open("requires/general_item_issue_controller.php?data=" + data+'&action='+action, true );
		}

function fnc_general_item_issue_entry(operation)
{
	if(operation==4)
	 {
		 var report_title=$( "div.form_caption" ).html();
		 
		  generate_report_file( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title,'general_item_issue_print','requires/general_item_issue_controller');
		// print_report( $('#cbo_company_name').val()+'*'+$('#txt_system_id').val()+'*'+report_title, "general_item_issue_print", "requires/general_item_issue_controller" ) 
		 return;
	 }
	else if(operation==0 || operation==1 || operation==2)
	{
		if($("#hidden_posted_in_account").val()*1==1)
		{
			alert("Already Posted In Accounts.Save,Update & Delete Not Allowed.");
            return;
		}
		if( form_validation('cbo_company_name*cbo_issue_purpose*txt_issue_date*txt_issue_req_no*txt_item_desc*cbo_item_category*txt_issue_qnty*cbo_store_name*cbo_location','Company Name*Issue Purpose*Issue Date*Issue Req No*Item Description*Item Category*Issue Quantity*Store Name*Location')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Date Can not Be Greater Than Current Date");
			return;
		}	
		
		if($("#txt_issue_qnty").val()*1>$("#txt_current_stock").val()*1) 
		{
			alert("Issue Quantity Exced By Current Stock Quantity.");
			$("#txt_issue_qnty").focus();
			return;
		}

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var cbo_floor=$('#cbo_floor').val()*1;
		var cbo_room=$('#cbo_room').val()*1;
		var txt_rack=$('#cbo_rack').val()*1;
		var txt_shelf=$('#cbo_self').val()*1;
		var cbo_bin=$('#cbo_binbox').val()*1;
		
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
		
		var dataString = "txt_system_no*txt_system_id*cbo_company_name*cbo_issue_purpose*txt_issue_date*cbo_loan_party*txt_issue_req_no*txt_challan_no*txt_remarks*txt_item_desc*cbo_store_name*txt_buyer_order*txt_order_id*cbo_room*cbo_item_category*cbo_uom*cbo_location*cbo_rack*cbo_item_group*txt_serial_no*txt_serial_id*cbo_department*cbo_self*hidden_p_issue_qnty*txt_issue_qnty*cbo_machine_category*cbo_section*cbo_binbox*txt_current_stock*cbo_issue_floor*cbo_machine_name*current_prod_id*update_id*before_serial_id*txt_return_qty*cbo_floor*variable_lot*txt_lot";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../../");
		
		freeze_window(operation);
		http.open("POST","requires/general_item_issue_controller.php",true);
		
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_general_item_issue_entry_reponse;
	}
}

function fnc_general_item_issue_entry_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);release_freezing(); return;  		
		var reponse=trim(http.responseText).split('**');
		//show_msg(reponse[0]); 
 		
		if(reponse[0]*1==20*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]==10)
		{
			show_msg(reponse[0]);
			release_freezing(); return;
		}
		else if(reponse[0]==17)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]==0)
		{
 			show_msg(reponse[0]);
			$("#txt_system_no").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			disable_enable_fields( 'cbo_company_name*cbo_issue_purpose*txt_issue_req_no', 1, "", "" );
 			//$("#tbl_master :input").attr("disabled", true);	
		}
			
		else if(reponse[0]==1)
		{
			show_msg(reponse[0]);
			disable_enable_fields( 'cbo_company_name*cbo_issue_purpose*txt_issue_req_no', 1, "", "" );	
			//$("#tbl_master :input").attr("disabled", true);	
 			set_button_status(0, permission, 'fnc_general_item_issue_entry',1,1);
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
				show_msg(reponse[0]);
				disable_enable_fields( 'cbo_company_name*cbo_issue_purpose*txt_issue_req_no', 1, "", "" );	
				//$("#tbl_master :input").attr("disabled", true);	
				set_button_status(0, permission, 'fnc_general_item_issue_entry',1,1);
			}
		}
		else if(reponse[0]==50)
		{
			alert("Serial No. Not Over Issue Qnty");
			return;
		}
		disable_enable_fields( 'cbo_company_name*cbo_issue_purpose*txt_issue_req_no*cbo_issue_source*cbo_issue_to', 1, "", "" );		 	
 		//$("#tbl_child").find('select,input').val('');	
		reset_form('','','txt_item_desc*txt_buyer_order*txt_order_id*cbo_floor*cbo_room*cbo_item_category*cbo_rack*cbo_item_group*txt_serial_no*txt_serial_id*cbo_self*hidden_p_issue_qnty*txt_issue_qnty*txt_current_stock*cbo_binbox*cbo_machine_category*cbo_issue_floor*cbo_machine_name*txt_return_qty*txt_lot','','','');   
		show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/general_item_issue_controller','');
		release_freezing(); 

	}
}

function open_mrrpopup()
{
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var page_link='requires/general_item_issue_controller.php?action=mrr_popup&company='+company+'&item_cat=22';
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var sys_id=this.contentDoc.getElementById("hidden_sys_id").value; // system number
 		$("#txt_system_id").val(sys_id);
		
		// master part call here
		get_php_form_data(sys_id, "populate_data_from_data", "requires/general_item_issue_controller");
		$("#tbl_master").find('input,select').attr("disabled", true);	
		disable_enable_fields( 'txt_system_no', 0, "", "" );
		
		var posted_in_account=$("#hidden_posted_in_account").val()*1;
		if(posted_in_account==1) 	$("#accounting_posting_td").text("Already Posted In Accounts.");
		else 						$("#accounting_posting_td").text("");	
 		//list view call here
		show_list_view(sys_id,'show_dtls_list_view','list_container','requires/general_item_issue_controller','');
		set_button_status(0, permission, 'fnc_general_item_issue_entry',1,1);
 	}
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);	
	//disable_enable_fields( 'cbo_company_name*cbo_basis*cbo_receive_purpose*cbo_store_name', 0, "", "" );
	$("#tbl_master").find('input,select').attr("disabled", false);
	set_button_status(0, permission, 'fnc_general_item_issue_entry',1,0);
	reset_form('generalItemIssue_1','list_container','','','','cbo_uom*cbo_location*cbo_department*cbo_section');
}

function fnc_loan_party(val)
{
	if(val==5)
	{
		$("#cbo_loan_party").attr("disabled",false);
	}
	else
	{
		$("#cbo_loan_party").val('');
		$("#cbo_loan_party").attr("disabled",true);
	}
}

function chk_issue_requisition_variabe(company)
{
	var status = return_global_ajax_value(company+"**22", 'chk_issue_requisition_variabe', '', 'requires/general_item_issue_controller').trim();
	status = status.split("**");
	if(status[0] == 1)
	{
		$("#txt_issue_req_no").prop('readonly',true);
		$("#txt_issue_req_no").attr('placeholder',"Browse").attr('onDblClick','fnc_items_sys_popup()');
	}
	else
	{
		$("#txt_issue_req_no").prop('readonly', false);
		$("#txt_issue_req_no").attr('placeholder',"write").removeAttr('onDblClick');
	}
	
	$("#variable_lot").val(status[1]);
	
	// =============================Store upto_variable_settings==============================
	var data='cbo_company_name='+company+'&action=upto_variable_settings';
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() 
	{
		if (this.readyState == 4 && this.status == 200) {
			document.getElementById("store_update_upto").value = this.responseText;
		}
	}
	xmlhttp.open("POST", "requires/general_item_issue_controller.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(data);
    // =====================================================================
}
function fnc_items_sys_popup()
{
    var cbo_company_name=$('#cbo_company_name').val();
    if( form_validation('cbo_company_name','Company Name')==false )
    {
            return;
    }

    var page_link='requires/general_item_issue_controller.php?cbo_company_name='+cbo_company_name+'&action=item_issue_requisition_popup_search&item_category_id=22';
    var title='Issue Req. No'
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=400px,center=1,resize=1,scrolling=0','../');

    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var hidden_item_issue_id=this.contentDoc.getElementById("hidden_item_issue_id").value;
        //var hidden_sys_id=this.contentDoc.getElementById("hidden_itemissue_req_sys_id").value;
        var data=hidden_item_issue_id.split("_");
        //alert(data[0]);
        if(trim(hidden_item_issue_id)!="")
        {
			freeze_window(5);
			$('#hidden_issue_req_id').val(data[0]);
			$('#txt_issue_req_no').val(data[1]);
			$('#cbo_location').val(data[3]);
			$('#cbo_department').val(data[4]);
			load_drop_down( 'requires/general_item_issue_controller',data[4], 'load_drop_down_section', 'section_td' );
			$('#cbo_section').val(data[5]);
			//$('#hidden_indent_date').val(data[2]);
			$('#txt_item_desc').prop('disabled',true);
			show_list_view(data[0]+'__22','show_item_issue_listview','item_issue_listview','requires/general_item_issue_controller','');
        }
        release_freezing();
    }
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
    <form name="generalItemIssue_1" id="generalItemIssue_1" autocomplete="off" > 
    
    <div style="width:1000px; float:left; position:relative;" align="center">       
    <table width="80%" cellpadding="0" cellspacing="2">
     	<tr>
        	<td width="100%" align="center" valign="top">   
            	<fieldset style="width:1000px;">
                <legend>General Item Issue</legend>
                <br />
                 	<fieldset style="width:950px;">                                       
                        <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="6" align="center"><b>System ID</b>
                                	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />&nbsp;&nbsp;
                                    <input type="hidden" id="txt_system_id" name="txt_system_id" value="" />
                                    <input type="hidden" id="hidden_posted_in_account" name="hidden_posted_in_account" value="" />
                                </td>
                           </tr>
                           <tr>
                                    <td  width="120" class="must_entry_caption">Company Name </td>
                                    <td width="170">
                                        <? 
                                         echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/general_item_issue_controller', this.value+'__'+22, 'load_drop_down_store', 'store_td' );load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_department', 'department_td' ); load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');chk_issue_requisition_variabe(this.value);" );
                                        ?>
                                        <input type="hidden" id="variable_lot" name="variable_lot" />
                                    </td>
                                    <td width="120" class="must_entry_caption">Issue Purpose</td>
                                    <td width="160"><? 
                                         echo create_drop_down( "cbo_issue_purpose", 170, $general_issue_purpose,"", 1, "-- Select Purpose --", $selected, "fnc_loan_party(this.value);","" );
                                        ?></td>
                                    <td width="120" class="must_entry_caption">Issue Date</td>
                                    <td width="" id="issue_purpose_td"><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:160px;" placeholder="Select Date" readonly /></td>
                            </tr>
                            <tr>
                            		<td>Loan Party </td>
                                    <td id="loan_party_td">
                                        <? 
                                            echo create_drop_down( "cbo_loan_party", 170, $blank_array,"", 1, "- Select Loan Party -", $selected, "","1","" );
                                        ?>
                                   </td>                         
                                    <td class="must_entry_caption">Issue Req. No</td>
                                    <td><input name="txt_issue_req_no" id="txt_issue_req_no" class="text_boxes" style="width:160px" />
                                    <input type="hidden" name="hidden_issue_req_id" id="hidden_issue_req_id" />
                                    <input type="hidden" name="req_approval_necissity" id="req_approval_necissity" />
                                    </td>
                                    <td>Challan No</td>
                                    <td><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry" ></td>
                            </tr>
                             <tr>
                             	<td width="120" >Remarks</td>
                                 <td colspan="5"><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:400px"  placeholder="Entry" /></td>
                             </tr> 
                             <tr>
                            	<td colspan="6" id="accounting_posting_td" style="font-size:18px; color:red"></td>
                            </tr>
                        </table>
                    </fieldset>
                    <br />
                    
                    <input type="hidden" id="before_serial_id" name="before_serial_id" value=""/>
                    
                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                    <tr>
                    <td width="49%" valign="top">
                    	<fieldset style="width:950px;">  
                        <legend>New Issue Item</legend>                                     
                            <table  width="100%" cellspacing="2" cellpadding="0" border="0">
                                <tr> 
                                	<td width="110" class="must_entry_caption">Location</td>
                                    <td width="140" id="location_td" >
                                        <? 
                                        echo create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select --", "", "" );
                                        ?>
                                    </td>                              
                                    
                                    <td width="100" class="must_entry_caption">Store Name</td>
                                    <td width="140" id="store_td">
										<? 
                                           echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1, "-- Select Store --", 0, "", 1 );
                                        ?>
                                    </td>  

                                    <td width="110" class="must_entry_caption">Item Description</td>
                                    <td width="140" >
                                    	<input name="txt_item_desc" id="txt_item_desc" class="text_boxes" type="text" style="width:110px;" placeholder="Double Click" onDblClick="popup_description()" readonly />
                                  	</td> 
                                    
                                    <td width="100">Stock Floor</td>
                                    <td width="140" id="floor_td">
                                    	<? 
                                    		echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select Floor --", 0, "",1 ); 
                                    	?>
                                    </td>
                                 </tr>
                                <tr>
                                    <td class="must_entry_caption">Item Category</td>
                                    <td >
                                    	<?
                                                echo create_drop_down( "cbo_item_category", 150, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_itemgroup', 'item_group_td' );", 1,"22" );
                                            ?>
                                    </td>
                                    <td >UOM</td>
                                    <td id="uom_td"><?
                                            echo create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 1, "--Select--", $selected, "",1 );
                                        ?></td>
                                    <td width="100">Buyer Order</td>
                                    <td width="140"><input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:110px;" placeholder="Double Click" onDblClick="fn_order()"  />
                                    	<input type="hidden" id="txt_order_id" value="" />
                                    </td>
                                    <td>Stock Room</td>
                                    <td id="room_td">
                                     	<?										
											echo create_drop_down( "cbo_room", 110, "$blank_array","", 1, "--Select--", "", "fn_room_rack_self_box()",1 );
										?>
                                    </td>
                                </tr>
                                <tr>
                                    <td >Item Group</td>
                                    <td  id="item_group_td">
                                    	<?
                                            echo create_drop_down( "cbo_item_group", 150, "select id,item_name from lib_item_group where item_category not in(1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", 1,"" );
                                         ?>
                                    </td>
                                    <td >Serial No</td>   
                                    <td><input name="txt_serial_no" id="txt_serial_no" class="text_boxes" type="text" style="width:120px;" placeholder="Double Click" onDblClick="popup_serial()" />
                                    	<input type="hidden" id="txt_serial_id" value="" />
                                    </td> 
                                    <td >Department</td>
                                    <td id="department_td"><? 
                                        echo create_drop_down( "cbo_department", 120, $blank_array,"", 1, "-- Select --", "", "" );
                                        ?></td>
                                    <td>Stock Rack</td>
                                    <td id="rack_td">
                                    	<?
											echo create_drop_down( "cbo_rack", 110, "$blank_array","", 1, "--Select--", "", "fn_room_rack_self_box()",1 );
										?>
                                    </td>
                                </tr>
                                <tr>
                                     <td class="must_entry_caption">Issue Qnty</td>
                                     <td><input type="hidden" name="hidden_p_issue_qnty" id="hidden_p_issue_qnty" readonly />
                                     <input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric"  style="width:140px;" /></td>
                                     <td >Current Stock</td>
                                  	 <td><input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:120px;" placeholder="Display" readonly /></td>
                                     <td >Section</td>
                                     <td id="section_td"><? 
                                        echo create_drop_down( "cbo_section", 120, $blank_array,"", 1, "-- Select --", "", "" );

                                        ?></td>
                                     <td>Stock Shelf</td>
                                    <td id="shelf_td">
                                    	<?
											echo create_drop_down( "cbo_self", 110, "$blank_array","", 1, "--Select--", "", "fn_room_rack_self_box()",1 );
										?>
                                    </td>
                              </tr>
                                <tr>
                                  
                                    <td >Machine Categ.</td>
                                    <td><? 
                                    echo create_drop_down( "cbo_machine_category", 150, $machine_category,"", 1, "-- Select --", "", "load_drop_down( 'requires/general_item_issue_controller', document.getElementById('cbo_company_name').value+'_'+this.value+'_'+document.getElementById('cbo_issue_floor').value, 'load_drop_machine', 'machine_td' );load_drop_down( 'requires/general_item_issue_controller',document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_floor', 'issue_floor_td' );" );
                                    ?>
                                    </td>
                                    <td >Issue To Floor</td>
                                    <td id="issue_floor_td">
                                    <? echo create_drop_down( "cbo_issue_floor", 130, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                    </td>
                                    <td >Machine No</td>
                                    <td id="machine_td">
                                    	<? echo create_drop_down( "cbo_machine_name", 120, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                                    </td>
                                    <td >Stock Bin/Box</td>
                                 	<td id="bin_td">
	                                 	<?
											echo create_drop_down( "cbo_binbox", 110, "$blank_array","", 1, "--Select--", "", "",1 );
										?>
                                 	</td>
                                    
                                </tr>

                                 <!--  new dev -->
                                <tr>
                                    <td>Brand</td>
                                    <td>
                                        <input type="text" name="txt_brand" id="txt_brand" class="text_boxes"  style="width:140px;" readonly disabled/>
                                    </td>
                                    <td>Origin</td>
                                    <td><?
                                        echo create_drop_down( "cbo_origin", 130, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 ,"",1);            
                                        ?>
                                    </td>
                                   
                                   <td>Model</td>
                                    <td>
                                        <input type="text" name="txt_model" id="txt_model" class="text_boxes"  style="width:110px;" readonly disabled/>
                                    </td>
                                    <td >Return Qty</td>
                                    <td>
                                    	<input type="text" name="txt_return_qty" id="txt_return_qty" class="text_boxes_numeric"  style="width:100px;" />
                                    </td>
                                </tr>
                                <tr>    
                                    <td id="lot_caption">Lot</td>
                                    <td><input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:140px;" readonly disabled /></td> 
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
                             <!-- details table id for update -->
                             <input type="hidden" id="current_prod_id" name="current_prod_id" readonly /> 
                             <input type="hidden" id="update_id" name="update_id" readonly /> 
                             <input type="hidden" name="store_update_upto" id="store_update_upto">
                              <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_general_item_issue_entry", 0,1,"fnResetForm();",1);?>
                        </td>
                   </tr> 
                </table>                 
              	</fieldset>
                <br>
    			<div style="width:1000px;" id="list_container"></div>
           </td>
         </tr>
    </table>
    </div> 
    <div style="width:300px; margin-left:15px;float: left;position: relative;" id="item_issue_listview" align="left"></div>
	</form>
</div>    
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
$('#cbo_location').val(0);
</script>
</html>
