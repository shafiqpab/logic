<?

/*-------------------------------------------- Comments
Purpose			: 	This form will create

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	03-09-2013
Updated by 		: 	Kausar,Jahid,Md mahbubur Rahman
Update date		: 	30-10-2013,15-01-2019
QC Performed BY	:	Creating Report & List view Repair
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
echo load_html_head_contents("Yarn Receive Info","../../", 1, 1, $unicode,1,1);
?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][265] );
//echo "var field_level_data= ". $data_arr . ";\n";

if($_SESSION['logic_erp']['mandatory_field'][265]!=""){

	$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][265] );

	echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
}
?>

function popup_description()
{
	if( form_validation('cbo_company_id*cbo_store_name','Company Name*Store Name')==false )
	{
		return;
	}
	var company_id = $("#cbo_company_id").val();
	var cbo_store_name = $("#cbo_store_name").val();
 	var page_link="requires/raw_material_item_issue_controller.php?action=item_description_popup&company_id="+company_id+"&cbo_store_name="+cbo_store_name;
	var title="Item Description Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1010px,height=350px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
 		var item_description_all=this.contentDoc.getElementById("item_description_all").value;//alert(item_description_all);
		var splitArr = item_description_all.split("*");
		//alert(splitArr[4]);
		$("#current_prod_id").val(splitArr[0]);
		$("#txt_item_desc").val(splitArr[1]);
		$("#txt_current_stock").val(splitArr[2]);
		$("#hidden_bal_qnty").val(splitArr[2]);
		$("#cbo_item_category").val(splitArr[3]);
		$("#cbo_item_group").val(splitArr[4]);
		$("#cbo_store_name").val(splitArr[5]);
		$("#txt_brand").val(splitArr[6]);
		$("#cbo_origin").val(splitArr[7]);
		$("#txt_model").val(splitArr[8]);// new dev
		$("#cbo_section").val(splitArr[9]);// new dev
		$("#cbo_uom").val(splitArr[10]);// new dev
		$("#cbo_store_name").attr('disabled',true);
		//load_drop_down( 'requires/raw_material_item_issue_controller', splitArr[4], 'load_drop_down_uom', 'uom_td' );
  	}
}

function enable_disable_loc(){
	//alert('Tipu');
	var cbo_location = $("#cbo_location").val();
	if (cbo_location == '') {
		$("#cbo_location").removeAttr('disabled',false);
	}
	if(cbo_location) {
		$("#cbo_location").attr('disabled',true);
	}
}
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
		var cbo_floor = $("#cbo_floor").val('');
		return;
	}
}
function valid_line(rcv){
	if( form_validation('cbo_floor','Floor Name')==false)
	{
		var cbo_sewing_line = $("#cbo_sewing_line").val('');
		return;
	}
}
function valid_machine(rcv){
	if( form_validation('cbo_floor','Floor Name')==false)
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
	var page_link="requires/raw_material_item_issue_controller.php?action=serial_popup&serialStringNo="+serialStringNo+"&serialStringID="+serialStringID+"&current_prod_id="+current_prod_id+"&txt_received_id="+txt_received_id;
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
	// var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_name').value+"_"+document.getElementById('cbo_party_name').value+"_"+document.getElementById('cbo_within_group').value;
	// var	page_link='requires/raw_material_item_issue_controller.php?action=order_popup&data='+data;

	var company = $("#cbo_company_id").val() ;
	var title = 'Order Info';
	var page_link = 'requires/raw_material_item_issue_controller.php?company='+company+'&action=order_popup';

	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var po_string=this.contentDoc.getElementById("hidden_string").value;
		var po_string_arr = po_string.split("_");
  		$('#txt_wo_batch_no').val(po_string_arr[0]);
  		$('#txt_order_id').val(po_string_arr[1]);
 		$('#txt_buyer_order').val(po_string_arr[2]);
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
			window.open("requires/raw_material_item_issue_controller.php?data=" + data+'&action='+action, true );
		}

function fnc_general_item_issue_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();

		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+$('#txt_system_no').val()+'*'+report_title,'general_item_issue_print','requires/raw_material_item_issue_controller');
		// print_report( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title, "general_item_issue_print", "requires/raw_material_item_issue_controller" )
		return;
	}
	
	
	if( form_validation('cbo_company_id*cbo_issue_basis*cbo_issue_purpose*txt_issue_date*txt_item_desc*cbo_item_category*txt_issue_qnty*cbo_location*cbo_store_name*cbo_section_mst','Company Name*Issue Basis*Issue Purpose*Issue Date*Item Description*Item Category*Issue Quantity*Location*Store Name*Section')==false )
	{
		return;
	}

	var cbo_issue_purpose = $('#cbo_issue_purpose').val()*1;

    if(cbo_issue_purpose==5){

    	if( form_validation('cbo_loan_party','Loan Party')==false )
		{
			return;
		}
    }


	if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][265]); ?>') 
	{
		if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][265]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][265]); ?>')==false) {return;}
	}
	
	
	var variable_lot=$("#variable_lot").val();
	var cbo_item_category=$("#cbo_item_category").val();
	if(variable_lot==1 && cbo_item_category==22)
	{
		if( form_validation('txt_lot_no','Lot')==false )
		{
			return;
		}
	}
	
	var current_date='<? echo date("d-m-Y"); ?>';
	if(date_compare($('#txt_issue_date').val(), current_date)==false)
	{
		alert("Issue Date Can not Be Greater Than Current Date");
		return;
	}
	
	//alert($("#txt_issue_qnty").val()*1+$("#txt_current_stock").val()*1);return;
	
	if($("#txt_issue_qnty").val()*1>$("#txt_current_stock").val()*1)
	{
		alert("Issue Quantity Exced By Current Stock Quantity.");
		$("#txt_issue_qnty").focus();
		return;
	}

	var dataString = "txt_system_no*txt_system_id*cbo_company_id*cbo_issue_purpose*cbo_loan_party*txt_issue_date*txt_issue_req_no*txt_challan_no*cbo_issue_source*cbo_issue_to*txt_remarks*txt_item_desc*cbo_store_name*txt_buyer_order*txt_order_id*txt_wo_batch_no*cbo_room*cbo_item_category*cbo_uom*cbo_location*txt_rack*cbo_item_group*txt_serial_no*txt_serial_id*cbo_department*txt_shelf*hidden_p_issue_qnty*txt_issue_qnty*cbo_machine_category*cbo_section*cbo_bin*txt_current_stock*cbo_floor*cbo_sewing_line*cbo_machine_name*current_prod_id*update_id*before_serial_id*txt_return_qty*hidden_issue_req_id*cbo_issue_basis*txt_no_of_qty*cbo_issue_uom*txt_lot_no*variable_lot*cbo_section_mst";
	var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
	//alert(data);die;
	freeze_window(operation);
	http.open("POST","requires/raw_material_item_issue_controller.php",true);

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
		else if(reponse[0]*1==19*1)
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

		else if(reponse[0]==1 || reponse[0]==2)
		{
			show_msg(reponse[0]);
			//$("#tbl_master :input").attr("disabled", true);
 			set_button_status(0, permission, 'fnc_general_item_issue_entry',1,0);
		}
		else if(reponse[0]==50)
		{
			alert("Serial No. Not Over Issue Qnty");
			return;
		}

		disable_enable_fields( 'cbo_company_id*cbo_issue_purpose*txt_issue_req_no*cbo_issue_source*cbo_issue_to*txt_issue_date', 1, "", "" );
		//disable_enable_fields( 'txt_system_no*cbo_store_name', 0, "", "" );
 		//$("#tbl_child").find('select,input').val('');
		var issue_basis = $("#cbo_issue_basis").val();
		if(issue_basis==7)
		{
			reset_form('','','txt_item_desc*txt_buyer_order*txt_order_id*txt_wo_batch_no*cbo_room*cbo_item_category*txt_rack*cbo_item_group*txt_serial_no*txt_serial_id*txt_shelf*hidden_p_issue_qnty*txt_issue_qnty*txt_current_stock*cbo_bin*cbo_machine_category*cbo_floor*cbo_machine_name*txt_return_qty*cbo_issue_uom*txt_no_of_qty','','','');
		}
		else	
		{
			reset_form('','','txt_item_desc*txt_buyer_order*txt_order_id*txt_wo_batch_no*cbo_room*cbo_item_category*txt_rack*cbo_item_group*txt_serial_no*txt_serial_id*txt_shelf*hidden_p_issue_qnty*txt_issue_qnty*txt_current_stock*cbo_bin*cbo_machine_category*cbo_floor*cbo_machine_name*txt_return_qty*cbo_issue_uom*txt_no_of_qty','','','');
			
		}
		show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/raw_material_item_issue_controller','');
		
 		var update_id = $("#update_id").val();
        var current_prod_id = $("#current_prod_id").val();
		if(issue_basis==7)
		{
			$('#cbo_store_name').attr('disabled', true);
		    $('#cbo_location').attr('disabled', true);
			var req_id = $("#hidden_issue_req_id").val();
			var req_no = $("#txt_issue_req_no").val();
			var store_name = $("#cbo_store_name").val();
			var variable_lot = $("#variable_lot").val();
			show_list_view(1+'**'+req_no+'**'+req_id+'**'+issue_basis+'**'+update_id+'**'+current_prod_id+'**'+store_name+'**'+req_id+'**'+variable_lot,'order_dtls_list_view_req','item_issue_listview','requires/raw_material_item_issue_controller','');
  			 
		}
		else
		{
			if(issue_basis==15 && reponse[3]!='')
			{
				var req_id = $("#hidden_issue_req_id").val();
				var req_no = $("#txt_issue_req_no").val();
				var store_name = $("#cbo_store_name").val();
				var variable_lot = $("#variable_lot").val();
				show_list_view(1+'**'+req_no+'**'+req_id+'**'+issue_basis+'**'+update_id+'**'+current_prod_id+'**'+store_name+'**'+req_id+'**'+variable_lot,'order_dtls_list_view','item_issue_listview','requires/raw_material_item_issue_controller','');	
			}
			$('#cbo_store_name').attr('disabled', false);
		    $('#cbo_location').attr('disabled', false);
		    $('#txt_item_desc').attr('disabled', false);
		
		}
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
	//var issue_basis = $("#cbo_issue_basis").val();
	var page_link='requires/raw_material_item_issue_controller.php?action=mrr_popup&company='+company;
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var sys_id=this.contentDoc.getElementById("hidden_sys_id").value; // system number
 		$("#txt_system_id").val(sys_id);

		// master part call here
		get_php_form_data(sys_id, "populate_data_from_data", "requires/raw_material_item_issue_controller");
		$("#tbl_master").find('input,select').attr("disabled", true);
		disable_enable_fields( 'txt_system_no', 0, "", "" );
		chk_issue_requisition_variabe(company);
		fn_onCheckBasis($("#cbo_issue_basis").val());
 		//list view call here
		show_list_view(sys_id,'show_dtls_list_view','list_container','requires/raw_material_item_issue_controller','');
		
		var req_no = $("#txt_issue_req_no").val();
		var req_id = $("#hidden_issue_req_id").val();
		var issue_basis = $("#cbo_issue_basis").val();
		var update_id = $("#update_id").val();
		var store_name = $("#cbo_store_name").val();
        var current_prod_id = $("#current_prod_id").val();
		var variable_lot = $("#variable_lot").val();
		if(issue_basis==7)
		{
 			show_list_view(1+'**'+req_no+'**'+req_id+'**'+issue_basis+'**'+update_id+'**'+current_prod_id+'**'+store_name+'**'+req_id+'**'+variable_lot,'order_dtls_list_view_req','item_issue_listview','requires/raw_material_item_issue_controller','');
		}
		else{
			show_list_view(1+'**'+req_no+'**'+req_id+'**'+issue_basis+'**'+update_id+'**'+current_prod_id,'order_dtls_list_view','item_issue_listview','requires/raw_material_item_issue_controller','');
		}
		//show_list_view(1+'**'+req_no+'**'+req_id,'order_dtls_list_view','item_issue_listview','requires/raw_material_item_issue_controller','');
		set_button_status(0, permission, 'fnc_general_item_issue_entry',1,0);
 	}
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);
	//disable_enable_fields( 'cbo_company_id*cbo_basis*cbo_receive_purpose*cbo_store_name', 0, "", "" );
	$("#tbl_master").find('input,select').attr("disabled", false);
	set_button_status(0, permission, 'fnc_general_item_issue_entry',1,0);
	reset_form('generalItemIssue_1','list_container','','','','cbo_uom*cbo_location*cbo_department*cbo_section');
}


function fnc_job_card_items_sys_popup()
{
	if ( form_validation('cbo_company_id*cbo_issue_basis','Company*Issue Basis')==false )
	{
		return;
	}
	var data=document.getElementById('cbo_company_id').value;
	page_link='requires/raw_material_item_issue_controller.php?action=fnc_job_card_items_sys_popup&data='+data;
	title='Trims Order Receive';
	

	emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=990px, height=420px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]
		var theemaildata=this.contentDoc.getElementById("selected_job").value;
		//alert(theemaildata)
		var ex_data=theemaildata.split('_');
		if (ex_data[0]!="")
		{//alert(theemail.value);
			freeze_window(5);
			$('#hidden_issue_req_id').val(ex_data[0]);
            $('#txt_issue_req_no').val(ex_data[1]);
            $('#cbo_section_mst').val(ex_data[2])

            var issue_basis = $("#cbo_issue_basis").val();
			
			show_list_view(1+'**'+ex_data[1]+'**'+ex_data[0]+'**'+issue_basis,'order_dtls_list_view','item_issue_listview','requires/raw_material_item_issue_controller','');				
			release_freezing();
		}
	}
}

function fnc_req_sys_popup()
{
	if ( form_validation('cbo_company_id*cbo_issue_basis','Company*Issue Basis')==false )
	{
		return;
	}
	var data=document.getElementById('cbo_company_id').value;
	page_link='requires/raw_material_item_issue_controller.php?action=req_sys_popup&data='+data;
	title='Requisition Info';
	//alert(data);

	emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=890px, height=420px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]
		var theemaildata=this.contentDoc.getElementById("hidden_production_data").value;
		//alert(theemaildata)
		//hidden_production_data
		var ex_data=theemaildata.split('_');
		//alert(ex_data)
		//40_OG-RMIR-21-00002
		if (ex_data[0]!="")
		{	//alert(ex_data[0]);
			freeze_window(5);
			$('#hidden_issue_req_id').val(ex_data[0]);
            $('#txt_issue_req_no').val(ex_data[1]);
			$('#cbo_location').val(ex_data[2]);
            $('#cbo_section_mst').val(ex_data[4]);
			
			load_room_rack_self_bin('requires/raw_material_item_issue_controller*4_5_6_7_8_9_10_11_15_16_17_18_19_20_21_22_23_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_101_22', 'store','store_td',$('#cbo_company_id').val(), ex_data[2],'','','','','','','','check_stock(ex_data[2])');
			
			
			$('#cbo_store_name').val(ex_data[3]);
			$('#cbo_store_name').attr('disabled', true);
		    $('#cbo_location').attr('disabled', true);
		    $('#cbo_company_id').attr('disabled', true);
			var issue_basis = $("#cbo_issue_basis").val();
 			var issue_req_id = $("#hidden_issue_req_id").val();
  		    var update_id = $("#update_id").val();
            var current_prod_id = $("#current_prod_id").val();
			var variable_lot = $("#variable_lot").val();
			show_list_view(1+'**'+ex_data[1]+'**'+ex_data[0]+'**'+issue_basis+'**'+update_id+'**'+current_prod_id+'**'+ex_data[3]+'**'+issue_req_id+'**'+variable_lot,'order_dtls_list_view_req','item_issue_listview','requires/raw_material_item_issue_controller','');
			release_freezing();
		}
	}
}


function fnc_items_sys_popup()
{
    var cbo_company_id=$('#cbo_company_id').val();
    if( form_validation('cbo_company_id','Company Name')==false )
    {
            return;
    }

    var page_link='requires/raw_material_item_issue_controller.php?cbo_company_id='+cbo_company_id+'&action=item_issue_requisition_popup_search';
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
            load_drop_down( 'requires/raw_material_item_issue_controller',data[4], 'load_drop_down_section', 'section_td' );
            $('#cbo_section').val(data[5]);
            //$('#hidden_indent_date').val(data[2]);
            $('#txt_item_desc').prop('disabled',true);
            show_list_view(data[0],'show_item_issue_listview','item_issue_listview','requires/raw_material_item_issue_controller','');
        }
        release_freezing();
    }
}

/*$('#txt_issue_req_no').live('keydown', function(e)
{

  	if (e.keyCode === 13) {
	e.preventDefault();
	var list_view=$('#txt_issue_req_no').val();

	var data=trim(return_global_ajax_value(list_view, 'check_reqn_no', '', 'requires/raw_material_item_issue_controller')).split("**");
	if(data[0]==0)
	{
		alert('Req. No not found.');
		$('#hidden_issue_req_id').val('');
		$('#txt_issue_req_no').val('');
	}
	else
	{
		$('#cbo_company_id').val(data[0]);
		$('#hidden_issue_req_id').val(data[1]);
		//alert(data[0]);
		load_drop_down( 'requires/raw_material_item_issue_controller', data[0], 'load_drop_down_location', 'location_td' );
		load_drop_down( 'requires/raw_material_item_issue_controller', data[0], 'load_drop_down_store', 'store_td' );
		load_drop_down( 'requires/raw_material_item_issue_controller', data[0], 'load_drop_down_department', 'department_td' );
		show_list_view(data[1],'show_item_issue_listview','item_issue_listview','requires/raw_material_item_issue_controller','');
	}

}

});*/

function chk_issue_requisition_variabe(company)
{
   var status = return_global_ajax_value(company, 'chk_issue_requisition_variabe', '', 'requires/raw_material_item_issue_controller').trim();
   status = status.split("__");
   if(status[0] == 1)
   {
	   //onDblClick="fnc_items_sys_popup()
       $("#txt_issue_req_no").prop('readonly',true);
       $("#txt_issue_req_no").attr('placeholder',"Browse").attr('onDblClick','fnc_job_card_items_sys_popup()');
   }
   else
   {
        $("#txt_issue_req_no").prop('readonly', false);
        $("#txt_issue_req_no").attr('placeholder',"write").removeAttr('onDblClick');
   }
   $("#variable_lot").val(status[1]);
}

function fn_onCheckBasis(value)
{
	//alert(value);
	
	if(value ==15 || value ==7)
	{
		$("#txt_issue_req_no").prop('readonly',true);
		$('#txt_item_desc').val('');
		$('#current_prod_id').val('');
		$('#txt_item_desc').prop('disabled',true);
		$('#txt_item_desc').attr('placeholder','Display');
		$('#cbo_store_name').val(0);

		if(value ==15)
		{
			$("#txt_issue_req_no").attr('placeholder',"Browse").attr('onDblClick','fnc_job_card_items_sys_popup()'); 
			$("#change_caption").text('Job Card');
		}
		else{
			//alert(value);
			$("#txt_issue_req_no").attr('placeholder',"Browse").attr('onDblClick','fnc_req_sys_popup()'); 
			$("#change_caption").text('Requisition');
		}
		// $('#cbo_store_name').prop('disabled',true);
		
		 $('#cbo_section_mst').prop('disabled',true);
	}
	else
	{
		$("#txt_issue_req_no").prop('readonly', false);
		$("#txt_issue_req_no").attr('placeholder',"write").removeAttr('onDblClick');
		$("#change_caption").text('Issue Req. No');
		$('#txt_item_desc').prop('disabled',false);
		$('#txt_item_desc').attr('placeholder','Browse');
		$('#cbo_store_name').prop('disabled',false);
		$('#cbo_section_mst').prop('disabled',false);
	}
}
function chk_bal_availability(iss_qty)
{
	var bal_qnty=$('#hidden_bal_qnty').val()*1;
	if(iss_qty>bal_qnty)
	{
		alert('Issue Quantity Not Allow Over Balance Quantity');
		$('#txt_issue_qnty').val('');
		return;
	}
}

function check_stock(store_id)
{
	//alert(store_id);
	var prod_id=$('#current_prod_id').val();
	var issue_basis=$('#cbo_issue_basis').val();
	if(issue_basis==15 && prod_id!='' && prod_id!=0)
	{

		var data=store_id+'__'+prod_id;
		//alert(data);
		get_php_form_data(data, "populate_store_prod_data", "requires/raw_material_item_issue_controller");
		//alert('Issue Quantity Not Allow Over Balance Quantity');
		//$('#txt_issue_qnty').val('');
		//return;
	}
}

function change_caption_no_of(id)
{
	if(id==50){
		$('#td_no_of').html('No. of Roll');
	} else if(id==53){
		$('#td_no_of').html('No. of Bag');
	} else if(id==55){
		$('#td_no_of').html('No. of Drum');
	} else if(id==66){
		$('#td_no_of').html('No. of Pot');
	} else if(id==83){
		$('#td_no_of').html('No. of Carton');
	} else {
		$('#td_no_of').html('No. of ');
	}
}

function  dependentClear(data){
    if(data.length > 0){
        var splitData = data.split("*");
        $.each(splitData, function (index, val){
           $('#'+val).val(0);
        });
    }

    var cbo_issue_purpose = $('#cbo_issue_purpose').val()*1;

    if(cbo_issue_purpose==5){

    	$("#cbo_loan_party").parent().prev('td').css("color", "blue");
    }
    else{

    	$("#cbo_loan_party").parent().prev('td').css("color", "black");
    }
}

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="generalItemIssue_1" id="generalItemIssue_1" autocomplete="off" >

    <div style="width:1020px; float: left;">
    <table width="80%" cellpadding="0" cellspacing="2">
     	<tr>
        	<td width="100%" align="center" valign="top">
            	<fieldset style="width:1020px;">
                <legend>Raw Material Issue</legend>
                <br />
                 	<fieldset style="width:1010px;">
                        <table  width="1010" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="6" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>System ID</b>
                                	<input type="text" name="txt_system_no" id="txt_system_no" class="text_boxes" style="width:160px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />&nbsp;&nbsp;
                                    <input type="hidden" id="txt_system_id" name="txt_system_id" value="" />
                                </td>
                           </tr>
                           <tr>
	                            <td  width="120" class="must_entry_caption">Company Name </td>
	                            <td width="170">
	                                <?
	                                 echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/raw_material_item_issue_controller', this.value, 'load_drop_down_location', 'location_td' );load_room_rack_self_bin('requires/raw_material_item_issue_controller*101_22', 'store','store_td',this.value,'','','','','','','','check_stock(this.value)');load_drop_down( 'requires/raw_material_item_issue_controller', this.value, 'load_drop_down_department', 'department_td' );load_drop_down('requires/raw_material_item_issue_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');chk_issue_requisition_variabe(this.value)" );
	                                 	//load_drop_down( 'requires/raw_material_item_issue_controller', this.value, 'load_drop_down_store', 'store_td' );
	                                ?>
                                    <input type="hidden" id="variable_lot" name="variable_lot" />
	                            </td>
	                            <td class="must_entry_caption" width="100" align=""> Issue Basis </td>
	                            <td width="170">
	                            <?
	                            echo create_drop_down( "cbo_issue_basis", 170, $receive_basis_arr,"", 1, "-- Select --", $selected, "fn_onCheckBasis(this.value)","","4,7,15" );
	                            ?>
	                            </td>
	                            <td class="must_entry_caption" width="120">Issue Purpose</td>
	                            <td width="160"><?
	                                 echo create_drop_down( "cbo_issue_purpose", 170, $general_issue_purpose,"", 1, "-- Select Purpose --", $selected, "dependentClear('cbo_issue_source*cbo_issue_to')","","5,15,22,24,25,35,36" );
	                                ?></td>
                            </tr>
                            <tr>
                                <td  width="120" id="change_caption">Issue Req. No</td>
                                <td width="170">
                                	<!--onDblClick="fnc_items_sys_popup()" placeholder="Browse/Scan/Write"-->
                                    <input name="txt_issue_req_no" id="txt_issue_req_no" class="text_boxes" style="width:160px"  maxlength="15" placeholder="Write" />
                                    <input type="hidden" name="hidden_issue_req_id" id="hidden_issue_req_id" />
                                    <input type="hidden" name="req_approval_necissity" id="req_approval_necissity" />
                                </td>
                                <td width="120"  >Challan No</td>
                                <td width="160"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" placeholder="Entry" ></td>

                                <td width="120" class="must_entry_caption">Issue Date</td>
                                <td width="" id="issue_purpose_td"><input type="text" name="txt_issue_date" value="<? echo date("d-m-Y");?>" id="txt_issue_date" class="datepicker" style="width:160px;" /></td>
                            </tr>
                            <tr>
                            	<td width="120" id="issue_source">Issue Source</td>
                            	<td width="160">
                            		<?
                                        echo create_drop_down("cbo_issue_source", 170, $knitting_source, "", 1, "-- Select Source --", $selected, "load_drop_down( 'requires/raw_material_item_issue_controller', this.value+'**'+$('#cbo_company_id').val()+'**'+$('#cbo_issue_purpose').val(), 'load_drop_down_issue_to', 'cbo_issue_to' );", "", "1,3");
                                    ?></td>
                            	<td width="120">Issue To</td>
                            	<td width="160" id="issue_to_td">
                            		<?
                                    	echo create_drop_down("cbo_issue_to", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
                                    ?>

                                </td>
                                
                                <td width="120" >Loan Party </td>
                                   <td width="160" id="loan_party_td">
                                        <?
                                        echo create_drop_down( "cbo_loan_party", 170, $blank_array,"", 1, "-- Select Loan Party --", $selected, "","","" );
                                        ?>
                                   </td>
                            </tr>
                            
                            <tr>
                            	<td width="120">Remarks</td>
								<td width="160">
									<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:170px"  placeholder="Entry" />
								</td>
								<td width="120" class="must_entry_caption">Section</td>
				                <td><? echo create_drop_down( "cbo_section_mst", 170, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
                            </tr>
                             <tr><td colspan="6" >&nbsp;</td></tr>
                        </table>
                    </fieldset>
                    <br />

                    <input type="hidden" id="before_serial_id" name="before_serial_id" value=""/>

                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                    <tr>
                    <td width="49%" valign="top">
                    	<fieldset style="width:1010px;">
                        <legend>New Issue Item</legend>
                            <table  width="100%" cellspacing="2" cellpadding="0" border="0">
                                <tr>
                               	 	<td class="must_entry_caption" >Location</td>
                                    <td width="" id="location_td">
                                        <?
                                        echo create_drop_down( "cbo_location", 152, $blank_array,"", 1, "-- Select --", "", "enable_disable_loc();",0 );
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
                                            echo create_drop_down( "cbo_item_group", 152, "select id,item_name from lib_item_group where item_category  in(101,5,6,7,23,22) and status_active=1 and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", 1,"" );
                                         ?>
                                    </td>
                                    <td class="must_entry_caption">Item Category</td>
                                    <td>
                                    	<?
											echo create_drop_down( "cbo_item_category", 152, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/raw_material_item_issue_controller', this.value, 'load_drop_down_itemgroup', 'item_group_td' );", 1,"",101);
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
                                    <td>
                                     	<input type="text" name="txt_issue_qnty" id="txt_issue_qnty" class="text_boxes_numeric"  style="width:140px;" onKeyUp="chk_bal_availability(this.value)" />
                                     	<input type="hidden" name="hidden_p_issue_qnty" id="hidden_p_issue_qnty" readonly />
                                     	<input type="hidden" name="hidden_bal_qnty" id="hidden_bal_qnty" readonly />
                                     </td>
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
                                    echo create_drop_down( "cbo_machine_category", 152, $machine_category,"", 1, "-- Select --", "", "load_drop_down( 'requires/raw_material_item_issue_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+document.getElementById('cbo_floor').value, 'load_drop_machine', 'machine_td' );load_drop_down( 'requires/raw_material_item_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );valid_cat(1)" );
                                    ?>
                                    </td>

                                    <td >Floor</td>
                                    <td id="floor_td">
                                    <? echo create_drop_down( "cbo_floor", 152, $blank_array,"", 1, "-- Select Floor --", 0, "",0 ); ?>
                                    </td>
                                    <td >Line No</td>
                                    <td id="sewing_line_td">
									<?
                                        echo create_drop_down( "cbo_sewing_line", 152, $blank_array,"", 1, "-- Select Line --", $selected, "",0,0 );
                                    ?>
                                    </td>

                               </tr>

                                <tr>
                                    <td >Machine No</td>
                                    <td id="machine_td">
                                    	<? echo create_drop_down( "cbo_machine_name", 152, $blank_array,"", 1, "-- Select Machine --", 0, "",0 ); ?>
                                    </td>
                                   <td >Department</td>
                                    <td id="department_td"><?
                                        echo create_drop_down( "cbo_department", 152, $blank_array,"", 1, "-- Select --", "", "" );
                                        ?></td>
                                    <td >Section</td>
                                     <td id="section_td"><?
                                        echo create_drop_down( "cbo_section", 152,$trims_section,"", 1, "-- Select --", "", "",1 );
                                        ?></td>
                                    <td width="100">Buyer Order</td>
                                    <td width="140"><input type="text" name="txt_buyer_order" id="txt_buyer_order" class="text_boxes" style="width:140px;" placeholder="Double Click" onDblClick="fn_order()" readonly />
                                    	<input type="hidden" id="txt_wo_batch_no" value="" />
                                    	<input type="hidden" id="txt_order_id" value="" />
                                    </td>

                                </tr>
                                <tr>
                                    <td width="100">Room</td>
                                    <td width="135" id="room_td">
                                     	<?
										echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
										//echo create_drop_down( "cbo_room", 135, "select room from inv_transaction where status_active=1 and room!=0 group by room order by room","room,room", 1, "--Select--", "", "fn_room_rack_self_box()",0 );
										?>
                                    </td>
                                    <td >Rack</td>
                                    <td id="rack_td">
                                    	<?
										//echo "select rack from inv_transaction where status_active=1 and rack !=' ' group by rack order by rack";
											echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
											//echo create_drop_down( "txt_rack", 150, "select rack from inv_transaction where status_active=1 and rack !='0' group by rack order by rack","rack,rack", 1, "--Select--", "", "fn_room_rack_self_box()",1 );
										?>
                                    </td>

                                     <td >Shelf</td>
                                    <td id="shelf_td">
                                    	<?
                                    		echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
											//echo create_drop_down( "txt_shelf", 130, "select self from inv_transaction where status_active=1 and self !=0 group by self order by self","self,self", 1, "--Select--", "", "fn_room_rack_self_box()",1 );
										?>

                                    </td>
                                     <td >Bin/Box</td>
                                     <td id="bin_td">
                                     	<?
                                     		echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 );
											//echo create_drop_down( "cbo_bin", 120, "select bin_box from inv_transaction where status_active=1 and bin_box !=0 group by bin_box order by bin_box","bin_box,bin_box", 1, "--Select--", "", "",1 );
										?>
                                     </td>

                                   <!--  new dev -->

                                </tr>
                                <tr>
	                                <td>Issue UOM</td>
	                                <td><?
	                            		echo create_drop_down( "cbo_issue_uom", 152, $unit_of_measurement,"", 1, "-- Select --", 0, "change_caption_no_of(this.value)", 0,'50,53,55,66,65,83' );
									?></td>
									<td id="td_no_of">No. of</td>
	                                <td>
	                                   <input class="text_boxes_numeric"  name="txt_no_of_qty" id="txt_no_of_qty" type="text" style="width:140px;"/>
	                                </td>
                                    <td id="td_no_of">Lot</td>
	                                <td>
	                                   <input class="text_boxes"  name="txt_lot_no" id="txt_lot_no" type="text" style="width:140px;"/>
	                                </td>
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
                              <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_general_item_issue_entry", 0,1,"fnResetForm();",1);?>
                        </td>
                   </tr>
                </table>
              	</fieldset>
                <br>
    			<div style="width:1020px;" id="list_container"></div>
           </td>
<!--           <td valign="top"></td>-->
         </tr>
    </table>
    </div>
    <div style="margin-left:10px; float:left;" id="item_issue_listview"></div>
	</form>
</div>
</body>
<script type="text/javascript">
	$(document).ready(function() 
		{ 
			for (var property in mandatory_field_arr) {
			  $("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
			}
		});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
