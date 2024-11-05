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

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//echo implode(",",array_keys($general_item_category));die;
//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}
//========== user credential end ==========
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Receive Info","../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

<?
if($_SESSION['logic_erp']['data_arr'][21]!="")
{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][21] );
	echo "var field_level_data= ". $data_arr . ";\n";
}
?>


/*function enable_disable_loc(){
	//alert('Tipu');
	var cbo_location = $("#cbo_location").val();
	if (cbo_location == '') {
		$("#cbo_location").removeAttr('disabled',false);
	}
	if(cbo_location) {
		$("#cbo_location").attr('disabled',true);
	}
}*/
function valid_cat(rcv){
	if( form_validation('cbo_company_id*cbo_location','Company Name*Location')==false)
	{
		var cbo_machine_category = $("#cbo_machine_category").val('');
		return;
	}
}
function valid_floor(rcv){
	if( form_validation('cbo_machine_category','Machine Category')==false)
	{
		var cbo_issue_floor = $("#cbo_issue_floor").val('');
		return;
	}
}
function valid_line(rcv){
	if( form_validation('cbo_issue_floor','Floor Name')==false)
	{

		var cbo_sewing_line = $("#cbo_sewing_line").val('');
		return;
	}
}
function valid_machine(rcv){
	if( form_validation('cbo_issue_floor','Floor Name')==false)
	{
		var cbo_machine_name = $("#cbo_machine_name").val('');
		return;
	}
}


function popup_serial()
{
	if( form_validation('cbo_company_id*txt_item_desc','Company Name*Item Description')==false )
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=300px,center=1,resize=1,scrolling=0','../')
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
	if(form_validation('cbo_company_id','Company')==false)
	{
		return;
	}
	var company = $("#cbo_company_id").val() ;
	var txt_order_ref = $("#txt_order_ref").val() ;
	
	var title = 'PO Info';
	var page_link = 'requires/general_item_issue_controller.php?company='+company+'&txt_order_ref='+txt_order_ref+'&action=order_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var save_string=this.contentDoc.getElementById("save_string").value;	 //Access form field with id="emailfield"
		var tot_rcv_qnty=this.contentDoc.getElementById("tot_rcv_qnty").value; //Access form field with id="emailfield"
		var all_po_id=this.contentDoc.getElementById("all_po_id").value; //Access form field with id="emailfield"
		var all_po_no=this.contentDoc.getElementById("all_po_no").value; //Access form field with id="emailfield"
  		$('#txt_buyer_order').val(all_po_no);
 		$('#txt_order_id').val(all_po_id);
		$('#txt_order_ref').val(save_string);
 		$('#txt_order_tot_qnty').val(tot_rcv_qnty);
		$('#txt_issue_qnty').val(tot_rcv_qnty);		
	}
}

function fn_room_rack_self_box()
{
	if( $("#cbo_room").val()!=0 )
		disable_enable_fields( 'txt_rack', 0, '', '' );
	else
	{
		reset_form('','','txt_rack*txt_shelf*cbo_bin','','','');
		disable_enable_fields( 'txt_rack*txt_shelf*cbo_bin', 1, '', '' ); //flds, operation, loop_flds, loop_leng
	}
	if( $("#txt_rack").val()!=0 )
		disable_enable_fields( 'txt_shelf', 0, '', '' );
	else
	{
		reset_form('','','txt_shelf*cbo_bin','','','');
		disable_enable_fields( 'txt_shelf*cbo_bin', 1, '', '' );
	}
	if( $("#txt_shelf").val()!=0 )
		disable_enable_fields( 'cbo_bin', 0, '', '' );
	else
	{
		reset_form('','','cbo_bin','','','');
		disable_enable_fields( 'cbo_bin', 1, '', '' );
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

		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title +'*'+$('#cbo_location').val(),'general_item_issue_print','requires/general_item_issue_controller');
		return;
	}
	else if(operation==5)
	{
		var report_title=$( "div.form_caption" ).html();

		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title +'*'+$('#cbo_location').val(),'general_item_issue_print_2','requires/general_item_issue_controller');
		return;
	}
	else if(operation==6)
	{
		var report_title=$( "div.form_caption" ).html();

		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title +'*'+$('#cbo_location').val(),'general_item_issue_print_3','requires/general_item_issue_controller');
		return;
	}
	else if(operation==7)
	{
		var report_title=$( "div.form_caption" ).html();

		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title,'general_item_issue_print_4','requires/general_item_issue_controller');
		return;
	}
	else if(operation==8)
	{
		var report_title=$( "div.form_caption" ).html();

		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title,'general_item_issue_print_5','requires/general_item_issue_controller');
		return;
	}
	
	
	if ($("#is_posted_account").val()*1 == 1) {
		alert("Already Posted In Accounting. Save Update Delete Restricted.");
		return;
	}

	if( form_validation('cbo_company_id*txt_issue_date*txt_issue_req_no*cbo_issue_source*cbo_issue_to*txt_item_desc*cbo_item_category*txt_issue_qnty*cbo_store_name','Company Name*Issue Date*Issue Req No*Item Description*Item Category*Issue Quantity*Store Name')==false )
	{
		return;
	}
	var current_date='<? echo date("d-m-Y"); ?>';
	if(date_compare($('#txt_issue_date').val(), current_date)==false)
	{
		alert("Issue Date Can not Be Greater Than Current Date");
		return;
	}

	var val_stock=$("#txt_current_stock").val()*1-$("#txt_issue_qnty").val()*1;
	if(val_stock<$("#txt_re_order_level").val()*1)
	{
		alert("Current Stock Qty cross re order level.");
	}
	
	
	if($("#txt_order_tot_qnty").val()*1>0 && $("#txt_issue_qnty").val()*1 != $("#txt_order_tot_qnty").val()*1)
	{
		alert("Gross Quantity And Order Wise Quantity Does Not Match.");
		return;
	}

	if($("#txt_issue_qnty").val()*1>$("#txt_current_stock").val()*1)
	{
		alert("Issue Quantity Exced By Current Stock Quantity.");
		$("#txt_issue_qnty").focus();
		return;
	}
	if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][21]);?>'){
		if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][21]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][21]);?>')==false)
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
	
	var variable_lot=$('#variable_lot').val()*1;
	var cbo_item_category=$('#cbo_item_category').val()*1;
	var txt_lot=$('#txt_lot').val();
	if(variable_lot==1 && cbo_item_category==22 && txt_lot=="")
	{
		alert("Lot Maintain Mandatory.");
		$('#txt_lot').focus();
		return;
	}

	var dataString = "txt_system_no*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_loan_party*txt_issue_date*txt_issue_req_no*txt_challan_no*cbo_issue_source*cbo_issue_to*cbo_location_issue_to*txt_remarks*txt_attention*txt_item_desc*cbo_store_name*txt_buyer_order*txt_order_id*txt_order_ref*cbo_room*cbo_item_category*cbo_uom*cbo_location*txt_rack*cbo_item_group*txt_serial_no*txt_serial_id*cbo_department*cbo_division*txt_shelf*hidden_p_issue_qnty*txt_issue_qnty*cbo_machine_category*cbo_section*cbo_bin*txt_current_stock*cbo_issue_floor*cbo_sewing_line*cbo_machine_name*current_prod_id*update_id*before_serial_id*txt_return_qty*hidden_issue_req_id*txt_remarks_dtls*txt_variable_status*cbo_floor*store_update_upto*variable_lot*txt_lot*txt_entry_no*cbo_table_no";
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
	// alert(data);return;
	freeze_window(operation);
	http.open("POST","requires/general_item_issue_controller.php",true);

	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_general_item_issue_entry_reponse;
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
		else if(reponse[0]*1==16*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==18*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==11 *1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]==0)
		{
 			show_msg(reponse[0]);
			$("#txt_system_no").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
 			//$("#tbl_master :input").attr("disabled", true);
		}
		else if(reponse[0]==1)
		{
			show_msg(reponse[0]);
			//$("#tbl_master :input").attr("disabled", true);
 			set_button_status(0, permission, 'fnc_general_item_issue_entry',1,1);
			//$('#print_button_2').removeClass('formbutton_disabled').addClass('formbutton');
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
				//$("#tbl_master :input").attr("disabled", true);
				set_button_status(0, permission, 'fnc_general_item_issue_entry',1,1);
				//$('#print_button_2').removeClass('formbutton_disabled').addClass('formbutton');
			}
		}
		else if(reponse[0]==50)
		{
			alert("Serial No. Not Over Issue Qnty");
			return;
		}
		
		
		$('#Print1').removeAttr("onclick");
		$('#Print1').attr('onclick','fnc_general_item_issue_entry(4)');
		$('#Print1').removeClass('formbutton_disabled').addClass('formbutton');
		 $('#print_button_1').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_2').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_3').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_4').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_5').removeClass('formbutton_disabled').addClass('formbutton');
		disable_enable_fields( 'cbo_company_id*cbo_issue_purpose*txt_issue_req_no*cbo_issue_source*cbo_issue_to*cbo_issue_to_location', 1, "", "" );
		disable_enable_fields( 'txt_item_desc', 0, "", "" );
		//disable_enable_fields( 'txt_system_no*cbo_store_name', 0, "", "" );
 		//$("#tbl_child").find('select,input').val('');
		reset_form('','','txt_item_desc*txt_buyer_order*txt_order_id*txt_order_ref*cbo_room*cbo_item_category*txt_rack*cbo_item_group*txt_serial_no*txt_serial_id*txt_shelf*hidden_p_issue_qnty*txt_issue_qnty*txt_current_stock*cbo_bin*cbo_machine_category*cbo_issue_floor*cbo_machine_name*txt_return_qty*txt_remarks_dtls*cbo_table_no','','','');
		show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/general_item_issue_controller','');
		release_freezing();

	}
}

function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();
	var page_link='requires/general_item_issue_controller.php?action=mrr_popup&company='+company;
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sys_id=this.contentDoc.getElementById("hidden_sys_id").value; // system number
 		$("#txt_system_id").val(sys_id);

		// master part call here
		
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		$("#is_posted_account").val(posted_in_account);
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else 					 document.getElementById("accounting_posted_status").innerHTML="";
		
		get_php_form_data(sys_id, "populate_data_from_data", "requires/general_item_issue_controller");
		$("#tbl_master").find('input,select').attr("disabled", true);
		disable_enable_fields( 'txt_system_no', 0, "", "" );
		chk_issue_requisition_variabe(company);
 		//list view call here
		show_list_view(sys_id,'show_dtls_list_view','list_container','requires/general_item_issue_controller','');
		hidden_issue_req_id
		var txt_variable_status = $("#txt_variable_status").val();
		var hidden_issue_req_id = $("#hidden_issue_req_id").val();
		if(txt_variable_status==1 && hidden_issue_req_id!='')
		{
			var store = $("#cbo_store_name").val();
			show_list_view(hidden_issue_req_id+'_'+company+'_'+store,'show_item_issue_listview','item_issue_listview','requires/general_item_issue_controller','');
		}
		set_button_status(0, permission, 'fnc_general_item_issue_entry',1,1);
		$('#print_button_1').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_2').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_3').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_4').removeClass('formbutton_disabled').addClass('formbutton');
		$('#print_button_5').removeClass('formbutton_disabled').addClass('formbutton');
 	}
}

//form reset/refresh function here
function fnResetForm()
{ 
	reset_form('generalItemIssue_1','list_container','','','','cbo_uom*cbo_location*cbo_section');
	//*cbo_department*cbo_devetion
	
	
	$("#tbl_master").find('input').attr("disabled", false);
	//disable_enable_fields( 'cbo_company_id*cbo_basis*cbo_receive_purpose*cbo_store_name', 0, "", "" );
	$("#tbl_master").find('input,select').attr("disabled", false);
	set_button_status(0, permission, 'fnc_general_item_issue_entry',1,0);
}

function fnc_items_sys_popup()
{
    var cbo_company_id=$('#cbo_company_id').val();
    if( form_validation('cbo_company_id','Company Name')==false )
    {
        return;
    }

    var page_link='requires/general_item_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=item_issue_requisition_popup_search';
    var title='Issue Req. No'
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1250px,height=400px,center=1,resize=1,scrolling=0','../');

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
			
			//$('#cbo_division').val(data[6]);
			//load_drop_down( 'requires/general_item_issue_controller',data[6], 'load_drop_down_section', 'department_td' );
			reset_form('','','txt_item_desc*txt_buyer_order*txt_order_id*cbo_room*cbo_item_category*txt_rack*cbo_item_group*txt_serial_no*txt_serial_id*txt_shelf*hidden_p_issue_qnty*txt_issue_qnty*txt_current_stock*cbo_bin*cbo_machine_category*cbo_issue_floor*cbo_machine_name*txt_return_qty*txt_remarks_dtls*cbo_uom','','','');
			var company = $("#cbo_company_id").val();
			
			load_drop_down( 'requires/general_item_issue_controller', data[11], 'load_drop_down_division', 'division_td' );
			load_drop_down( 'requires/general_item_issue_controller', data[10], 'load_drop_down_department', 'department_td' );
			load_drop_down( 'requires/general_item_issue_controller',data[4], 'load_drop_down_section', 'section_td' );
			load_room_rack_self_bin('requires/general_item_issue_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'store','store_td',$('#cbo_company_id').val(), data[3]);
			$('#hidden_issue_req_id').val(data[0]);
			$('#txt_issue_req_no').val(data[1]);
			$('#cbo_location').val(data[3]);
			$('#cbo_department').val(data[4]);
			$('#cbo_section').val(data[5]);
			$('#cbo_store_name').val(data[7]);
			$('#cbo_division').val(data[10]);
			$('#cbo_company_id').val(data[11]);
			$('#cbo_location').prop('disabled',true);
			$('#cbo_store_name').prop('disabled',true);
			$('#cbo_company_id').prop('disabled',true);
			//$('#hidden_indent_date').val(data[2]);
			$('#txt_item_desc').prop('disabled',true);
			$('#cbo_division').prop('disabled',true);
			$('#cbo_department').prop('disabled',true);
			
			var store = $("#cbo_store_name").val();
			show_list_view(data[0]+'_'+company+'_'+store,'show_item_issue_listview','item_issue_listview','requires/general_item_issue_controller','');
        }
        release_freezing();
    }
}

function popup_description()
{
	if( form_validation('cbo_company_id*cbo_store_name','Company Name*Store Name')==false )
	{
		return;
	}
	var company_id = $("#cbo_company_id").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var variable_lot = $("#variable_lot").val();
 	var page_link="requires/general_item_issue_controller.php?action=item_description_popup&company_id="+company_id+"&cbo_store_name="+cbo_store_name+"&variable_lot="+variable_lot;
	var title="Item Description Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0','../')
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
		$("#txt_brand").val(splitArr[6]);
		$("#cbo_origin").val(splitArr[7]);
		$("#txt_model").val(splitArr[8]);// new dev
		$("#cbo_floor").val(splitArr[9]).attr('disabled','disabled');
		$("#cbo_room").val(splitArr[10]).attr('disabled','disabled');
		$("#txt_rack").val(splitArr[11]).attr('disabled','disabled');
		$("#txt_shelf").val(splitArr[12]).attr('disabled','disabled');
		$("#cbo_bin").val(splitArr[13]).attr('disabled','disabled');
		$("#cbo_uom").val(splitArr[14]).attr('disabled','disabled');
		$("#txt_lot").val(splitArr[15]).attr('disabled','disabled');
		$("#txt_re_order_level").val(splitArr[16]).attr('disabled','disabled');
		$("#cbo_store_name").attr('disabled',true);
		var company_id=$("#cbo_company_id").val();
		if (splitArr[10]>0) // room
		{
			load_drop_down( 'requires/general_item_issue_controller', splitArr[9]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[10], 'load_drop_room', 'room_td' );
		}
		if (splitArr[11]>0) // rack
		{
			load_drop_down( 'requires/general_item_issue_controller', splitArr[10]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[11], 'load_drop_rack', 'rack_td' );
		}
		if (splitArr[12]>0) // shelf
		{
			load_drop_down( 'requires/general_item_issue_controller', splitArr[11]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[12], 'load_drop_shelf', 'shelf_td' );
		}
		if (splitArr[13]>0) // bin
		{
			load_drop_down( 'requires/general_item_issue_controller', splitArr[12]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[13], 'load_drop_bin', 'bin_td' );
		}
		//load_drop_down( 'requires/general_item_issue_controller', splitArr[4], 'load_drop_down_uom', 'uom_td' );
  	}
}


function set_requisition_data(str_data)
{
	
	var str_data_ref=str_data.split("**");
	var store_update_upto=$('#store_update_upto').val()*1;
	var variable_lot=$('#variable_lot').val()*1;
	if(store_update_upto>1)
	{
		var page_link="requires/general_item_issue_controller.php?action=create_item_search_list_view_req&company_id="+str_data_ref[5]+"&prod_id="+str_data_ref[0]+"&stores="+str_data_ref[6]+"&store_update_upto="+store_update_upto+"&variable_lot="+variable_lot+"&item_category_id="+str_data_ref[8];
        var title="Item Description Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=350px,center=1,resize=1,scrolling=0','../../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
			var item_description_all=this.contentDoc.getElementById("item_description_all").value;//alert(item_description_all);
			var splitArr = item_description_all.split("*");
			load_drop_down('requires/general_item_issue_controller', splitArr[5]+'_'+str_data_ref[5], 'load_drop_floor','floor_td');
			//alert(splitArr[4]);
			$("#current_prod_id").val(splitArr[0]);
			$("#txt_item_desc").val(splitArr[1]);
			$("#txt_current_stock").val(splitArr[2]);
			$("#cbo_item_category").val(splitArr[3]);
			$("#cbo_item_group").val(splitArr[4]);
			$("#cbo_store_name").val(splitArr[5]);
			$("#txt_brand").val(splitArr[6]);
			$("#cbo_origin").val(splitArr[7]);
			$("#txt_model").val(splitArr[8]);// new dev
			$("#cbo_floor").val(splitArr[9]).attr('disabled','disabled');
			$("#cbo_room").val(splitArr[10]).attr('disabled','disabled');
			$("#txt_rack").val(splitArr[11]).attr('disabled','disabled');
			$("#txt_shelf").val(splitArr[12]).attr('disabled','disabled');
			$("#cbo_bin").val(splitArr[13]).attr('disabled','disabled');
			$("#cbo_uom").val(splitArr[14]).attr('disabled','disabled');
			$("#txt_lot").val(splitArr[15]).attr('disabled','disabled');
			$("#txt_re_order_level").val(splitArr[16]).attr('disabled','disabled');
			$("#cbo_store_name").attr('disabled',true);
			var company_id=$("#cbo_company_id").val();
			if (splitArr[10]>0) // room
			{
				load_drop_down( 'requires/general_item_issue_controller', splitArr[9]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[10], 'load_drop_room', 'room_td' );
			}
			if (splitArr[11]>0) // rack
			{
				load_drop_down( 'requires/general_item_issue_controller', splitArr[10]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[11], 'load_drop_rack', 'rack_td' );
			}
			if (splitArr[12]>0) // shelf
			{
				load_drop_down( 'requires/general_item_issue_controller', splitArr[11]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[12], 'load_drop_shelf', 'shelf_td' );
			}
			if (splitArr[13]>0) // bin
			{
				load_drop_down( 'requires/general_item_issue_controller', splitArr[12]+'_'+company_id+'_'+splitArr[5]+'_'+splitArr[13], 'load_drop_bin', 'bin_td' );
			}
        }
	}
	else
	{
		get_php_form_data(str_data,'populate_item_details_form_data_dtls','requires/general_item_issue_controller');
	}
	
}

function issue_onchange(com) 
{
	//alert(com);
	if($("#cbo_issue_source").val() == 1)
	{
		var cbo_company_id=$('#cbo_company_id').val();
		//alert(cbo_company_id);
		$("#cbo_issue_to").val(cbo_company_id);
		var hidden_issue_req_id=$('#hidden_issue_req_id').val();
		if(hidden_issue_req_id=="") load_drop_down( 'requires/general_item_issue_controller', cbo_company_id, 'load_drop_down_division', 'division_td' );
		load_drop_down( 'requires/general_item_issue_controller', com+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_issue_to', 'cbo_issue_to' );
	}
	else
	{
		load_drop_down( 'requires/general_item_issue_controller', com+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_issue_to', 'cbo_issue_to' );
	}

}
function disabled_enable()
{
	if($("#cbo_department").val() != '')
	{
		$('#cbo_department').prop("disabled", true);
	}
	if($("#cbo_division").val() != '')
	{
		$('#cbo_division').prop("disabled", true);
	}
}



// ==============End Floor Room Rack Shelf Bin upto disable============
function storeUpdateUptoDisable() 
{
	var store_update_upto=$('#store_update_upto').val()*1;	
	if(store_update_upto==5)
	{
		$('#cbo_bin').prop("disabled", true);
	}
	if(store_update_upto==4)
	{
		$('#txt_shelf').prop("disabled", true);
		$('#cbo_bin').prop("disabled", true);
	}
	else if(store_update_upto==3)
	{
		$('#txt_rack').prop("disabled", true);
		$('#txt_shelf').prop("disabled", true);
		$('#cbo_bin').prop("disabled", true);
	}
	else if(store_update_upto==2)
	{	
		$('#cbo_room').prop("disabled", true);
		$('#txt_rack').prop("disabled", true);
		$('#txt_shelf').prop("disabled", true);	
		$('#cbo_bin').prop("disabled", true);
	}
	else if(store_update_upto==1)
	{
		$('#cbo_floor').prop("disabled", true);
		$('#cbo_room').prop("disabled", true);
		$('#txt_rack').prop("disabled", true);
		$('#txt_shelf').prop("disabled", true);	
		$('#cbo_bin').prop("disabled", true);	
	}
}
// ==============End Floor Room Rack Shelf Bin upto disable============


function reset_on_change(id)
{
	
	if(id =="cbo_store_name")
	{
		// var unRefreshId = "cbo_company_id*cbo_location*cbo_store_name*txt_delivery_date*store_update_upto";
	}
	else if(id =="cbo_location")
	{
		// var unRefreshId = "cbo_company_id*cbo_location*txt_delivery_date*store_update_upto";
		//load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_floor','floor_td');
		//load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_room','room_td');
		//load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_rack','rack_td');
		//load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_shelf','shelf_td');
		//load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_bin','bin_td');
	}
	else if(id =="cbo_company_id")
	{
		// var unRefreshId = "cbo_company_id*txt_delivery_date*store_update_upto";
		load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_down_store','store_td');
		load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_floor','floor_td');
		load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_room','room_td');
		load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_rack','rack_td');
		load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_shelf','shelf_td');
		load_drop_down('requires/general_item_issue_controller', '0', 'load_drop_bin','bin_td');
	}
	// reset_form('finishFabricEntry_1', 'list_container_finishing*roll_details_list_view*list_fabric_desc_container', '', '', '', unRefreshId);

}
/*function floor_function(argument) 
{
	var store = $('#cbo_store_name').val();
	alert(store);
	
}*/

function search_asset()
{
	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/general_item_issue_controller.php?action=search_asset_entry' + '&cbo_company_name=' + $('#cbo_company_name').val(), 'Asset Acquisition Search', 'width=1085px,height=400px,center=1,resize=0,scrolling=0', '../')
	emailwindow.onclose = function ()
	{
		var theform = this.contentDoc.forms[0];
		var data = this.contentDoc.getElementById("hidden_system_number").value;
		$("#txt_entry_no").val(data);
		//alert(data);
		//get_php_form_data(data, "populate_asset_details_form_data", "requires/asset_acquisition_controller");
		//show_list_view(data, 'show_asset_active_listview', 'asset_list_view', 'requires/asset_acquisition_controller', 'setFilterGrid(\'list_view\',-1)');
	}
}



function store_select()
{
	if($('#cbo_store_name option').length==2)
	{
		if($('#cbo_store_name option:first').val()==0)
		{
			$('#cbo_store_name').val($('#cbo_store_name option:last').val());
			load_drop_down('requires/general_item_issue_controller', $('#cbo_store_name').val()+'_'+$('#cbo_company_id').val(), 'load_drop_floor','floor_td');
		}
	}
	else if($('#cbo_store_name option').length==1)
	{
		$('#cbo_store_name').val($('#cbo_store_name option:last').val());
	}	
}


//function location_select()
//{
//	if($('#cbo_location option').length==2)
//	{
//		if($('#cbo_location option:first').val()==0)
//		{
//			$('#cbo_location').val($('#cbo_location option:last').val());
//			load_drop_down('requires/general_item_issue_controller', $('#cbo_location option:last').val()+'_'+$('#cbo_company_id').val(), 'load_drop_down_store','store_td');
//			store_select();
//		}
//	}
//	else if($('#cbo_location option').length==1)
//	{
//		$('#cbo_location').val($('#cbo_location option:last').val());
//	}	
//}

function company_onchange(com_id)
{
	//alert(com_id);
	var com_all_data = return_global_ajax_value(com_id, 'com_wise_all_data', '', 'requires/general_item_issue_controller');
	var com_all_data_arr=com_all_data.split("**");
	var JSONObject = JSON.parse(com_all_data_arr[0]);
	$('#cbo_location').html('<option value="'+0+'">Select</option>');
	for (var key of Object.keys(JSONObject).sort())
	{
		$('#cbo_location').append('<option value="'+key+'">'+JSONObject[key]+'</option>');
	}
	
	var JSONObject_party = JSON.parse(com_all_data_arr[1]);
	$('#cbo_loan_party').html('<option value="'+0+'">Select</option>');
	for (var key of Object.keys(JSONObject_party).sort())
	{
		$('#cbo_loan_party').append('<option value="'+key+'">'+JSONObject_party[key]+'</option>');
	}
	
	var print_button_setup_arr = com_all_data_arr[2].split(",");
	var buttonHtml="";
	for(var i in print_button_setup_arr)
	{
		if(print_button_setup_arr[i]==143) buttonHtml=buttonHtml+'<input id="print_button_1" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(4)" name="print" value="Print">';
		if(print_button_setup_arr[i]==66) buttonHtml=buttonHtml+'<input id="print_button_2" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(5)" name="print" value="Print 2">';
		if(print_button_setup_arr[i]==85) buttonHtml=buttonHtml+'<input id="print_button_3" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(6)" name="print" value="Print 3">';
		if(print_button_setup_arr[i]==160) buttonHtml=buttonHtml+'<input id="print_button_4" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(7)" name="print_4" value="Print 4">';
		if(print_button_setup_arr[i]==129) buttonHtml=buttonHtml+'<input id="print_button_5" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(8)" name="print_5" value="Print 5">';
	}
	document.getElementById('button_data_panel').innerHTML =buttonHtml;
	
	if($("#cbo_issue_to").val() != '')
	{
		$("#cbo_issue_to").val("");
	}
	reset_form('generalItemIssue_1','list_container','','','','cbo_uom*cbo_location*cbo_section*cbo_company_id');
}


function chk_issue_requisition_variabe(company)
{
	
   var status_datas = return_global_ajax_value(company, 'chk_issue_requisition_variabe', '', 'requires/general_item_issue_controller').trim();
   var status_data = status_datas.split("__");
   var status = status_data[0].split("**");
  // $("#txt_issue_req_no").val('');
   if(status[0] == 1)
   {
	   $('#txt_variable_status').val(status[0]);
	   //onDblClick="fnc_items_sys_popup()
       $("#txt_issue_req_no").prop('readonly',true);
       $("#txt_issue_req_no").attr('placeholder',"Browse").attr('onDblClick','fnc_items_sys_popup()');
	   //load_drop_down( 'requires/general_item_issue_controller',company, 'load_drop_down_store', 'store_td' );
   }
   else
   {	
   		$('#txt_variable_status').val(2);
        $("#txt_issue_req_no").prop('readonly', false);
        $("#txt_issue_req_no").attr('placeholder',"write").removeAttr('onDblClick');
		//load_drop_down( 'requires/general_item_issue_controller',company, 'load_drop_down_store', 'store_td' );
   }
   $('#store_update_upto').val(status_data[1]);
   $('#variable_lot').val(status_data[2]);
}
	
	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="generalItemIssue_1" id="generalItemIssue_1" autocomplete="off" >

    <div style="width:1000px; float: left;position: relative;" align="center">
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
                                     <input type="hidden" id="txt_variable_status" name="txt_variable_status" value="" />
                                </td>
                           </tr>
                           <tr>
								<td  width="120" class="must_entry_caption">Company Name </td>
								<td width="170">
									<?
										//load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down('requires/general_item_issue_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');
										//get_php_form_data(this.value,'print_button_variable_setting','requires/general_item_issue_controller' );
										echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "chk_issue_requisition_variabe(this.value);company_onchange(this.value);" );
										//load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_store', 'store_td' );
									?>
									<input type="hidden" id="variable_lot" name="variable_lot" />
								</td>
								<td width="120"  >Issue Purpose</td>
								<td width="160"><?
										echo create_drop_down( "cbo_issue_purpose", 170, $general_issue_purpose,"", 1, "-- Select Purpose --", $selected, "","" ); ?></td>

								<td width="120" >Loan Party </td>
								<td width="160" id="loan_party_td">
									<?
									echo create_drop_down( "cbo_loan_party", 170, $blank_array,"", 1, "-- Select Loan Party --", $selected, "","","" );
									?>
								</td>
                            </tr>
                            <tr>
								<td  width="120" class="must_entry_caption">Issue Req. No</td>
								<td width="170">
									<!--onDblClick="fnc_items_sys_popup()" placeholder="Browse/Scan/Write"-->
									<input name="txt_issue_req_no" id="txt_issue_req_no" class="text_boxes" style="width:160px"  maxlength="30" placeholder="Write" />
									<input type="hidden" name="hidden_issue_req_id" id="hidden_issue_req_id" />
									<input type="hidden" name="req_approval_necissity" id="req_approval_necissity" />
								</td>
								<td width="120"  >Challan No</td>
								<td width="160"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry" ></td>

								<td width="120" class="must_entry_caption">Issue Date</td>
								<td width="" id="issue_purpose_td"><input type="text" name="txt_issue_date" value="<? echo date("d-m-Y");?>" id="txt_issue_date" class="datepicker" style="width:160px;" /></td>
                            </tr>
                            <tr>
                            	<td width="120" class="must_entry_caption" id="issue_source">Issue Source</td>
                            	<td width="160">
                            		<?
                                        echo create_drop_down("cbo_issue_source", 170, $knitting_source, "", 1, "-- Select Source --", $selected, "issue_onchange(this.value);", "", "1,3");
                                    ?></td>
                            	<td width="120" class="must_entry_caption">Issue To</td>
                            	<td width="160" id="issue_to_td">
                            		<?
										//load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_issue_to_location', 'cbo_location_issue_to' );load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_division', 'division_td' );
                                    	echo create_drop_down("cbo_issue_to", 170,"select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", "", "");
                                    ?>

                                </td>
                            	<td width="120">Location</td>
								<td width="160" id="location_issue_to_td">
									<?
                                    	echo create_drop_down("cbo_location_issue_to", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
                                    ?>
									<!-- <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:160px"  placeholder="Entry" /> -->
								</td>
                            </tr>
                            <tr>
							 	<td width="120">Remarks</td>
								<td colspan="3">
									<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes"  placeholder="Remarks Entry" style="width:95%"/> 
								</td>
								<td width="120">Attention</td>
								<td width="160"><input type="text" name="txt_attention" id="txt_attention" class="text_boxes" style="width:156px" placeholder="Entry" ></td>
							</tr>
                        </table>
                    </fieldset>
                    <br />

                    <input type="hidden" id="before_serial_id" name="before_serial_id" value=""/>

                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                    <tr>
                    <td width="49%" valign="top">
                    	<fieldset style="width:990px;">
                        <legend>New Issue Item</legend>
                            <table  width="100%" cellspacing="2" cellpadding="0" border="0">
                                <tr>
                               	 	<td class="must_entry_caption">Location</td>
                                    <td width="" id="location_td">
                                        <?
										//enable_disable_loc();
                                        echo create_drop_down( "cbo_location", 152, $blank_array,"", 1, "-- Select --", 0, "load_drop_down('requires/general_item_issue_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_store','store_td');store_select();",0 );
                                        ?>
                                    </td>

                                    <td width="110" class="must_entry_caption">Item Description</td>
                                    <td width="140" >
                                    	<input name="txt_item_desc" id="txt_item_desc" class="text_boxes" type="text" style="width:140px;" placeholder="Double Click" onDblClick="popup_description()" readonly />
                                    </td>

                                    <td>Brand</td>
                                    <td>
                                        <input type="text" name="txt_brand" id="txt_brand" class="text_boxes"  style="width:140px;" readonly disabled/>
                                    </td>
                                    <td>Origin</td>
                                    <td><?
                                        echo create_drop_down( "cbo_origin", 152, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0 ,"",1);
                                        ?>
                                    </td>

                                 </tr>

                                <tr>
                                    <td>Model</td>
                                    <td>
                                        <input type="text" name="txt_model" id="txt_model" class="text_boxes"  style="width:140px;" readonly disabled/>
                                    </td>
                                    <td >Item Group</td>
                                    <td  id="item_group_td">
                                    	<?
                                            echo create_drop_down( "cbo_item_group", 152, "select id,item_name from lib_item_group where item_category not in(1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", 1,"" );
                                         ?>
                                    </td>
                                    <td class="must_entry_caption">Item Category</td>
                                    <td>
                                    	<?
											echo create_drop_down( "cbo_item_category", 152, $general_item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_itemgroup', 'item_group_td' );", 1,"" );
										?>
                                    </td>
                                    <td >UOM</td>
                                    <td id="uom_td">
										<?
                                            echo create_drop_down( "cbo_uom", 152, $unit_of_measurement,"", 1, "--Select--", $selected, "",1 );
                                        ?>
                                    </td>

                                </tr>

                                <tr>
                                    <td class="must_entry_caption">Issue Qnty</td>
                                     <td><input type="hidden" name="hidden_p_issue_qnty" id="hidden_p_issue_qnty" readonly />
                                     <input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric"  style="width:140px;" /></td>
                                    <td >Return Qty</td>
                                    <td>
                                    	<input type="text" name="txt_return_qty" id="txt_return_qty" class="text_boxes_numeric"  style="width:140px;" />
                                    </td>
                                    <td >Serial No</td>
                                    <td><input name="txt_serial_no" id="txt_serial_no" class="text_boxes" type="text" style="width:140px;" placeholder="Double Click" onDblClick="popup_serial()" />
                                    	<input type="hidden" id="txt_serial_id" value="" />
                                    </td>
                                      <td >Current Stock</td>
                                    <td>
                                        <input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:140px;" placeholder="Display" readonly />
                                        <input type="hidden" name="txt_re_order_level" id="txt_re_order_level" class="text_boxes_numeric" style="width:140px;" placeholder="Display" readonly />
                                    </td>

                                </tr>

                                <tr>
                                    <td width="100" class="must_entry_caption">Store Name</td>
                                    <td width="140" id="store_td">
										<?
                                           echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select Store --", 0, "", 1 );
                                        ?>
                                    </td>
                                     <td >Machine Categ.</td>
                                    <td><?
                                    echo create_drop_down( "cbo_machine_category", 152, $machine_category,"", 1, "-- Select --", "", "load_drop_down( 'requires/general_item_issue_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+document.getElementById('cbo_issue_floor').value, 'load_drop_machine', 'machine_td' );load_drop_down( 'requires/general_item_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_floor', 'issue_floor_td' );valid_cat(1)" );
                                    ?>
                                    </td>

                                    <td >Issue To Floor</td>
                                    <td id="issue_floor_td">
                                    <? echo create_drop_down( "cbo_issue_floor", 152, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                    </td>
                                    <td >Line No</td>
                                    <td id="sewing_line_td">
									<?
                                        echo create_drop_down( "cbo_sewing_line", 152, $blank_array,"", 1, "-- Select Line --", $selected, "",0,0 );
                                    ?>
                                    </td>

                                </tr>

                                <tr>
                                    <td width="100" >Machine No</td>
                                    <td  width="140" id="machine_td">
                                    	<? echo create_drop_down( "cbo_machine_name", 152, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                                    </td>
                                    <td >Division</td>
                                    <td id="division_td"><?
										//function create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg="", $selected_index="", $onchange_func="", $is_disabled="", $array_index="", $fixed_options="", $fixed_values="", $not_show_array_index="", $tab_index="", $new_conn="", $field_name="", $additionalClass="", $additionalAttributes="")
                                        echo create_drop_down( "cbo_division", 152, $blank_array,"", 1, "-- Select --", "", "","","","","","","","","","onchange_void","" );
                                        ?></td>
                                   <td >Department</td>
                                    <td id="department_td"><?
                                        echo create_drop_down( "cbo_department", 152, $blank_array,"", 1, "-- Select --", "", "" );
                                        ?></td>
                                    <td >Section</td>
                                     <td id="section_td"><?
                                        echo create_drop_down( "cbo_section", 152, $blank_array,"", 1, "-- Select --", "", "" );
                                        ?></td>
                                   
                                </tr>
                                <tr>
                                 <td width="100">Buyer Order</td>
                                    <td width="135"><input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:140px;" placeholder="Double Click" onDblClick="fn_order()" readonly />
                                    	<input type="hidden" id="txt_order_id" value="" />
                                        <input type="hidden" id="txt_order_ref" value="" />
                                        <input type="hidden" id="txt_order_tot_qnty" value="" />
                                    </td>

                                     <td >Stock Floor</td>
                                    <td id="floor_td">
                                    	<?
                                    		echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
											//echo create_drop_down( "txt_shelf", 130, "select self from inv_transaction where status_active=1 and self !=0 group by self order by self","self,self", 1, "--Select--", "", "fn_room_rack_self_box()",1 );
										?>

                                    </td>

                                    <td>Stock Room</td>
                                    <td id="room_td">
                                     	<?
										echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
										//echo create_drop_down( "cbo_room", 135, "select room from inv_transaction where status_active=1 and room!=0 group by room order by room","room,room", 1, "--Select--", "", "fn_room_rack_self_box()",0 );
										?>
                                    </td>
                                    <td >Stock Rack</td>
                                    <td id="rack_td">
                                    	<?
										//echo "select rack from inv_transaction where status_active=1 and rack !=' ' group by rack order by rack";
											echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
											//echo create_drop_down( "txt_rack", 150, "select rack from inv_transaction where status_active=1 and rack !='0' group by rack order by rack","rack,rack", 1, "--Select--", "", "fn_room_rack_self_box()",1 );
										?>
                                    </td>
                                </tr>
                                <tr>
                                <td >Stock Shelf</td>
                                <td id="shelf_td">
                                	<?
                                		echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
									?>

                                </td>
                                <td width="100">Stock Bin/Box</td>
                                 <td id="bin_td">
                                    <?
                                        echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
                                        //echo create_drop_down( "cbo_bin", 120, "select bin_box from inv_transaction where status_active=1 and bin_box !=0 group by bin_box order by bin_box","bin_box,bin_box", 1, "--Select--", "", "",1 );
                                    ?>
                                 </td>
                                <td>Remarks</td>
                                <td colspan="5"><input name="txt_remarks_dtls" id="txt_remarks_dtls" class="text_boxes" type="text" style="width:370px;"/></td>
                                </tr>
                                <tr>
                                    <td>Lot</td>
                                    <td><input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:140px;" readonly disabled/></td>
                                    <td>Asset No</td>
                                    <td><input type="text" name="txt_entry_no" id="txt_entry_no" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="search_asset()" readonly /></td>
                                    <td >Table No</td>
                                    <td id="table_no_td">
                                        <?
                                            echo create_drop_down( "cbo_table_no", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
                                        ?>
    
                                    </td>
                                    <td colspan="4">&nbsp;</td>
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
                             <input type="hidden" id="is_posted_account" name="is_posted_account" value=""/>
                             <input type="hidden" name="store_update_upto" id="store_update_upto">
                              <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_general_item_issue_entry", 0,0,"fnResetForm();",1);?>
							 <span id="button_data_panel"></span>
                             <!-- <input type="button" id="print_button_2" onClick="fnc_general_item_issue_entry(5)" value="Print 2" class="formbutton_disabled" style="width:80px;"/>
							 <input type="button" id="print_button_3" onClick="fnc_general_item_issue_entry(6)" value="Print 3" class="formbutton_disabled" style="width:80px;"/> -->
                        </td>
                   </tr>
                   <tr>
                        <td colspan="6" align="center">
                            <div id="accounting_posted_status" style=" color:red; font-size:24px;";  ></div>
                        </td>
                    </tr>
                </table>
              	</fieldset>
                <br>
    			<div style="width:1000px;" id="list_container"></div>
           </td>
<!--           <td valign="top"></td>-->
         </tr>
    </table>
    </div>
        <div style="width:300px; margin-left:15px;float: left;position: relative;" id="item_issue_listview" align="left"></div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	$('#cbo_division').val(0);
	$('#cbo_store_name').val(0);
</script>
</html>
