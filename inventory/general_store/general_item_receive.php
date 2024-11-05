<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	29-08-2013
Updated by 		: 	Kausar / Jahid
Update date		: 	29-10-2013	 (Creating report	)
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
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
//echo "SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id";
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
//var_dump($item_cate_id);
$company_credential_cond = "";

//$library_item_arr=get_item_creation_array();
//echo "<pre>".count($library_item_arr);print_r($library_item_arr);die;
//echo "<pre>".count(return_item_dec_place_array(0,3));print_r(return_item_dec_place_array(0,3));die;

if ($company_id >0) {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)";
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
//echo implode(",",array_flip(general_item_category));
//========== user credential end ==========
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Yarn Receive Info","../../", 1, 1, $unicode);
//echo  $item_cate_credential_cond="".implode(",",array_flip($general_item_category))."";die;

// for autocomplete brand
/*$brand_sql = sql_select("select brand_name from lib_brand order by brand_name");
$brand_name = "";
foreach($brand_sql as $row)
{
	$brand_name.= "{value:'".$row[csf('brand_name')]."'},";
}

// for autocomplete color
$color_sql = sql_select("select id,color_name from lib_color order by id");
$color_name = "";
foreach($color_sql as $row)
{
	$color_name.= "{value:'".$row[csf('color_name')]."',id:".$row[csf('id')]."},";
}


*/

//$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=20 and status_active=1 and is_deleted=0",'company_name','independent_controll');
//if(count($independent_control_arr)<1) $independent_control_arr=array(0=>0);

// last yarn receive exchange rate, currency, store name---------------
$sql = sql_select("select id, store_id, exchange_rate, currency_id from inv_receive_master where entry_form=20 and status_active=1 and id=(select max( id) from inv_receive_master where entry_form=20 and status_active=1)");
$storeName=$exchangeRate=$currencyID=0;

foreach($sql as $row)
{
	$storeName=$row[csf("store_id")];
	$exchangeRate=$row[csf("exchange_rate")];
	$currencyID=$row[csf("currency_id")];
}

?>

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var dtls_mandatory_field="";
var dtls_mandatory_message="";

<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][20] );
echo "var field_level_data= ". $data_arr . ";\n";

if($_SESSION['logic_erp']['mandatory_field'][20]!="")
{
	$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][20] );
	echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
}
else
{
	echo "var mandatory_field_arr= new Array();\n";
}

/* $field_dtls='';
$message_dtls='';
$i=0;
foreach($_SESSION['logic_erp']['mandatory_field'][20] as $key => $value){
    if($i==0){
        $field_dtls.=$value;
        $message_dtls.=ucwords(str_replace('_', ' ', str_replace(array('cbo','txt'),'',$value)));
    }else{
        $field_dtls.='*'.$value;
        $message_dtls.='*'.ucwords(str_replace('_', ' ', str_replace(array('cbo','txt'),'',$value)));
    }
    $i++;
}

echo "var dtls_mandatory_field = '". ($field_dtls) . "';\n";
echo "var dtls_mandatory_message = '". ($message_dtls) . "';\n"; */
?>
// popup for WO/PI----------------------


function openmypage(page_link,title)
{
	if( form_validation('cbo_company_id*cbo_receive_basis*cbo_store_name','Company Name*Receive Basis*Store Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();
	var receive_basis = $("#cbo_receive_basis").val();
    var cbo_store_name = $("#cbo_store_name").val();

	page_link='requires/general_item_receive_controller.php?action=wopi_popup&company='+company+'&receive_basis='+receive_basis+'&cbo_store_name='+cbo_store_name;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px, height=420px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // wo/pi table id
		var wopiNumber=this.contentDoc.getElementById("hidden_wopi_number").value; // wo/pi number
		if (rowID!="")
		{
			freeze_window(5);
			$("#hidden_lc_id").val("");
			//$("#txt_wo_pi_req").val(wopiNumber);
			$("#txt_wo_pi_req_id").val(rowID);
			var store_id=$("#cbo_store_name").val();
			//alert(rowID);
			get_php_form_data(receive_basis+"**"+rowID, "populate_data_from_wopi_popup", "requires/general_item_receive_controller" );
			show_list_view(receive_basis+"**"+rowID+"**"+company+"**"+store_id,'show_product_listview','list_product_container','requires/general_item_receive_controller','setFilterGrid(\'list_view\',-1)');
			release_freezing();
			$("#tbl_child").find('input[type="text"],input[type="hidden"]').val('');
			$("#tbl_child").find('select').val(0);
		}
	}
}

// enable disable field for independent
function fn_onCheckBasis(val)
{
	if(val==4 || val==6)
	{
		$("#cbo_supplier").attr("disabled",false);
		//if(val==4) $("#cbo_currency").attr("disabled",false).val(1);
		$("#cbo_currency").attr("disabled",false).val(1);
		$("#txt_exchange_rate").val(1);
		$("#cbo_source").attr("disabled",false);
 		$("#txt_lc_no").attr("disabled",true);
		$("#txt_wo_pi_req").attr("disabled",true);
		$("#cbo_pay_mode").attr("disabled",false);

	}
	else if(val==7)
	{
		$("#cbo_supplier").attr("disabled",false);
		$("#cbo_currency").attr("disabled",true);
		$("#txt_exchange_rate").val("");
		$("#cbo_source").attr("disabled",true);
		$("#txt_lc_no").attr("disabled",true);
 		$("#txt_wo_pi_req").attr("disabled",false);
		$("#cbo_pay_mode").attr("disabled",true);
	}
	else
	{
 		$("#cbo_supplier").attr("disabled",true);
		$("#cbo_currency").attr("disabled",true).val(0);
		$("#txt_exchange_rate").val("");
		$("#cbo_source").attr("disabled",true);
		$("#txt_lc_no").attr("disabled",false);
		$("#txt_wo_pi_req").attr("disabled",false);
		$("#cbo_pay_mode").attr("disabled",true);
	}

	if(val==1)
	{
		$("#txt_lc_no").attr("disabled",false);
	}
	else
	{
		$("#txt_lc_no").attr("disabled",true);
	}

	if((val==4 || val==6 || val==7) && $('#update_id').val()==''){
		$("#txt_rate").attr("disabled",false);
	}
	else
	{
		$("#txt_rate").attr("disabled",true);
		$("#txt_rate").val(0);
	}

	$("#tbl_child").find('input[type="text"],input[type="hidden"]').val('');
	$("#tbl_child").find('select').val(0);
	$('#txt_wo_pi_req').val('');
	$('#txt_wo_pi_req_id').val('');
	$("#txt_lc_no").val('');
	$('#list_product_container').html('');

}

// LC pop up script here-----------------------------------
function popuppage_lc()
{

	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var receive_basis = $("#cbo_receive_basis").val();
	if(receive_basis*1==1 || receive_basis*1==2 || receive_basis*1==7)
	{
		return;
	}
	var company = $("#cbo_company_id").val();
	var page_link='requires/general_item_receive_controller.php?action=lc_popup&company='+company;
	var title="Search LC Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // lc table id
		var wopiNumber=this.contentDoc.getElementById("hidden_wopi_number").value; // lc number
		$("#txt_lc_no").val(wopiNumber);
		$("#hidden_lc_id").val(rowID);
	}
}

// calculate ILE ---------------------------
function fn_calile()
{
	//if( form_validation('cbo_company_id*cbo_source*txt_rate','Company Name*Source*Rate')==false )
	//{
	//	return;
	//}

	var company=$('#cbo_company_id').val();
	var item_category=$('#cbo_item_category').val();
	var item_group=$('#cbo_item_group').val();
	var source=$('#cbo_source').val();
	var rate=$('#txt_rate').val();
	var responseHtml = return_ajax_request_value(company+'**'+source+'**'+rate+'**'+item_category+'**'+item_group, 'show_ile', 'requires/general_item_receive_controller');
	var splitResponse="";
	if(responseHtml!="")
	{
		splitResponse = responseHtml.split("**");
		$("#ile_td").html('ILE% '+splitResponse[0]);
		$("#txt_ile").val(splitResponse[1]);
	}
	else
	{
		$("#ile_td").html('ILE% 0');
		$("#txt_ile").val(0);
	}
	//amount and book currency calculate--------------//
	var quantity 		= $("#txt_receive_qty").val();
	var exchangeRate 	= $("#txt_exchange_rate").val();
	var ile_cost 		= $("#txt_ile").val();
	var amount = quantity*1*(rate*1+ile_cost*1);
	var bookCurrency = (rate*1+ile_cost*1)*exchangeRate*1*quantity*1;
 	var currency 		= $("#cbo_currency").val();
	$("#txt_amount").val(number_format_common(amount,"","",currency));
	$("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
}

function fn_room_rack_self_box()
{
	if( $("#cbo_room").val()*1 > 0 )
		disable_enable_fields( 'txt_rack', 0, '', '' );
	else
	{
		reset_form('','','txt_rack*txt_shelf*cbo_bin','','','');
		disable_enable_fields( 'txt_rack*txt_shelf*cbo_bin', 1, '', '' );
	}
	if( $("#txt_rack").val()*1 > 0 )
		disable_enable_fields( 'txt_shelf', 0, '', '' );
	else
	{
		reset_form('','','txt_shelf*cbo_bin','','','');
		disable_enable_fields( 'txt_shelf*cbo_bin', 1, '', '' );
	}
	if( $("#txt_shelf").val()*1 > 0 )
		disable_enable_fields( 'cbo_bin', 0, '', '' );
	else
	{
		reset_form('','','cbo_bin','','','');
		disable_enable_fields( 'cbo_bin', 1, '', '' );
	}
}

function show_print_report(type)
{
	if($('#hidden_mrr_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}
	else
	{
		if(type==2)
		{
			var action='general_item_receive_print_new';
		}
		else if(type==3)
		{
			var action='general_item_receive_print_3';
		}
		else if(type==4)
		{
			 var action='general_item_receive_print_4';
		}
		else if(type==5)
		{
			var action='general_item_receive_print_5';
		}
		else if(type==6)
		{
			var action='general_item_receive_print_6';
		}
		else if(type==7)
		{
			var action='general_item_receive_print_7';
		}
		else if(type==8)
		{
			var action='general_item_receive_print_8';
		}
		else if(type==9)
		{
			var action='general_item_receive_print_9';
		}

		var report_title=$( "div.form_caption" ).html();
		var variable_string_inventory =$("#variable_string_inventory").val();
		var data= $('#cbo_company_id').val()+'__'+$('#hidden_mrr_id').val()+'__'+report_title+'__'+$('#cbo_receive_basis').val()+'__'+variable_string_inventory;
		window.open("requires/general_item_receive_controller.php?data=" + data+'&action='+action, true );

		/*var report_title=$( "div.form_caption" ).html();txt_mrr_no
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val(), "chemical_dyes_receive_print", "requires/chemical_dyes_receive_controller" ) */
		return;
	}
}

function fnc_general_item_receive_entry(operation)
{
	var variable_string_inventory =$("#variable_string_inventory").val();
	if(operation==4)
	 {
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_id').val()+'__'+$('#hidden_mrr_id').val()+'__'+report_title+'__'+variable_string_inventory, "general_item_receive_print", "requires/general_item_receive_controller" )
		 return;
	 }
	else if(operation==0 || operation==1 || operation==2)
	{
		if($("#hidden_posted_in_account").val()*1==1)
		{
			alert("Already Posted In Accounts.Save,Update & Delete Not Allowed.");
            return;
		}

		var isFileMandatory = "";
		<?
			if(!empty($_SESSION['logic_erp']['mandatory_field'][20][7])) echo " isFileMandatory = ". $_SESSION['logic_erp']['mandatory_field'][20][7] . ";\n";
		?>
		if($("#multiple_file_field")[0].files.length==0 && isFileMandatory!="" && $('#hidden_mrr_id').val()==''){
			document.getElementById("multiple_file_field").focus();
			var bgcolor='-moz-linear-gradient(bottom, rgb(254,151,174) 0%, rgb(255,255,255) 10%, rgb(254,151,174) 96%)';
			document.getElementById("multiple_file_field").style.backgroundImage=bgcolor;
			alert("Please Add File in Master Part");
			return;
		}

		/*if(operation==2)
		{
			alert("Delete Permission Not allow.");return;
		}*/

		if($('#is_rate_optional').val()==1)
		{
			var fieldData="cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_supplier*cbo_currency*cbo_pay_mode*cbo_source*cbo_item_category*cbo_item_group*txt_item_desc*txt_receive_qty";
			var msgData="Company Name*Receive Basis*Receive Date*Store Name*Supplier*Currency*Pay Mode*Source*Item Category*Item Group*Item Description*Receive Quantity";
		}
		else
		{
			var fieldData="cbo_company_id*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_supplier*cbo_currency*cbo_pay_mode*cbo_source*cbo_item_category*cbo_item_group*txt_item_desc*txt_receive_qty*txt_rate";
			var msgData="Company Name*Receive Basis*Receive Date*Store Name*Supplier*Currency*Pay Mode*Source*Item Category*Item Group*Item Description*Receive Quantity*Rate";
		}

		if( form_validation(fieldData,msgData)==false )
		{
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][20]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][20]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][20]); ?>')==false) {release_freezing();return;}
		}

		/* if(dtls_mandatory_field)
        {
            if (form_validation(dtls_mandatory_field,dtls_mandatory_message)==false)
            {
                release_freezing();
                return;
            }
        } */
        
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Receive Date Can not Be Greater Than Today");
			return;
		}
		if($("#txt_exchange_rate").val()<=0)
		{
			alert("Exchange Rate Should Be More Then Zero");
			$("#txt_exchange_rate").focus();
			return;
		}
		
	    if($("#cbo_receive_basis").val() != 4 && $("#cbo_receive_basis").val() != 6 && $("#cbo_receive_basis").val() != 2 && operation != 2)
	    {
	        if($("#txt_receive_qty").val()*1 > $("#txt_order_qty").val()*1)
			{
				alert("Receive Qnty Cannot be Greater than Balance Qnty");
				$("#txt_receive_qty").focus();
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
		
		var txt_mrr_no="'"+$("#txt_mrr_no").val()+"'";
		
		var dataString = "txt_mrr_no*hidden_mrr_id*cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*txt_challan_date_mst*txt_lc_no*hidden_lc_id*cbo_store_name*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*txt_wo_pi_req*txt_wo_pi_req_id*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date*txt_remarks*cbo_item_category*cbo_item_group*txt_item_desc*txt_serial_no*txt_serial_qty*cbo_uom*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_order_qty*txt_warranty_date*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_receive_purpose*cbo_loan_party*current_prod_id*txt_prod_id*update_id*txt_sup_ref*txt_referance*variable_string_inventory*hid_req_dtls_id*txt_addi_info*txt_lot*txt_ref_no*txt_bill_no_mst*txt_bill_date_mst*store_update_upto";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		freeze_window(operation);
		http.open("POST","requires/general_item_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_general_item_receive_entry_reponse;
	}
}

function fnc_general_item_receive_entry_reponse()
{
	if(http.readyState == 4)
	{
		var reponse=trim(http.responseText).split('**');
		//alert(http.responseText);release_freezing();return;
		var variable_string_inventory =$("#variable_string_inventory").val();
		//var variable_string_inventory =$("#variable_string_inventory").val();
 		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();return;
		}
		else if(reponse[0]==10)
		{
			show_msg(reponse[0]);
			release_freezing();return;
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
		else if(reponse[0]*1==18*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]==50)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]==0)
		{
			show_msg(reponse[0]);
			var check_system_id=$("#hidden_mrr_id").val();
			$("#txt_mrr_no").val(reponse[1]);
			$("#hidden_mrr_id").val(reponse[2]);
			if (check_system_id=="") uploadFile( $("#hidden_mrr_id").val());
			show_list_view(reponse[2]+"__"+variable_string_inventory,'show_dtls_list_view','list_container','requires/general_item_receive_controller','');
 			//$("#tbl_master :input").attr("disabled", true);
		}
		else if(reponse[0]==1)
		{
			show_msg(reponse[0]);
 			//$("#tbl_master :input").attr("disabled", true);
			show_list_view(reponse[2]+"__"+variable_string_inventory,'show_dtls_list_view','list_container','requires/general_item_receive_controller','');
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
				show_list_view(reponse[2]+"__"+variable_string_inventory,'show_dtls_list_view','list_container','requires/general_item_receive_controller','');
			}
		}
		disable_enable_fields( 'cbo_receive_basis*cbo_store_name*cbo_supplier*txt_wo_pi_req*cbo_company_id*cbo_currency', 1, "", "" );
		disable_enable_fields( 'txt_item_desc', 0, "", "" );
  		if($("#cbo_receive_basis").val() == 4 || $("#cbo_receive_basis").val() == 6 )
  		{
			$("#tbl_child").find('input[type="text"],input[type="hidden"]').val('');
			$("#tbl_child").find('select').val(0);
  			$("#cbo_item_category").removeAttr("disabled");
  			$("#cbo_item_group").removeAttr("disabled");
  		}
  		else
  		{
  			$("#tbl_child").find('input').val('');
			$("#tbl_child").find('select:not([name="cbo_item_category"]) select:not([name="cbo_item_group"])').val(0);
  		}
		set_button_status(0, permission, 'fnc_general_item_receive_entry',1,1);
 		release_freezing();
	}
}

function uploadFile(hidden_mrr_id)
{
	$(document).ready(function() { 
			
		var suc=0;
		var fail=0;
		for( var i = 0 ; i < $("#multiple_file_field")[0].files.length ; i++)
		{
			var fd = new FormData();
			console.log($("#multiple_file_field")[0].files[i]);
			var files = $("#multiple_file_field")[0].files[i]; 
			fd.append('file', files);
			// alert(hidden_mrr_id);
			$.ajax({
				url: 'requires/general_item_receive_controller.php?action=file_upload&hidden_mrr_id='+ hidden_mrr_id, 
				type: 'post', 
				data:fd, 
				contentType: false, 
				processData: false, 
				success: function(response){
					var res=response.split('**');
					if(res[0] == 0){ 
						
						suc++;
					}
					else if(fail==0)
					{
						alert('file not uploaded');
						fail++;
					}
				}, 
			}); 
		}

		if(suc > 0 )
		{
				document.getElementById('multiple_file_field').value='';
		}
	}); 
}


function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();
	var page_link='requires/general_item_receive_controller.php?action=mrr_popup&company='+company;
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1070px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var mrr_id=this.contentDoc.getElementById("hidden_recv_number").value.split("_");; // mrr number  
 		//$("#txt_mrr_no").val(mrrNumber);
		//alert(mrr_id);return;
		// master part call here
	//	alert(hidden_mrr_id);
		$('#tbl_child').find('input,select').val("");
		get_php_form_data(mrr_id[0], "populate_data_from_data", "requires/general_item_receive_controller");
		$("#tbl_master").find('input,select').attr("disabled", true);
		
		load_drop_down('requires/general_item_receive_controller', company+'*'+mrr_id[1], 'load_drop_down_supplier_new', 'supplier');

		$("#btn_fileadd").prop("disabled", false);// new add 21-12-2020
		
		disable_enable_fields( 'txt_mrr_no*txt_remarks*txt_sup_ref*txt_addi_popup', 0, "", "" );
		var posted_in_account=$("#hidden_posted_in_account").val()*1;

		//if(posted_in_account==1) 	$("#accounting_posting_td").text("Already Posted In Accounts.");
		//else 						$("#accounting_posting_td").text("");
		if(posted_in_account==1) 	$("#accounting_posted_status").text("Already Posted In Accounting.");
		else 						$("#accounting_posted_status").text("");
		

		var txt_wo_pi_req_id = $("#txt_wo_pi_req_id").val();
		var txt_wo_pi_req = $("#txt_wo_pi_req").val();
		//fn_onCheckBasis($("#cbo_receive_basis").val());

		$("#txt_wo_pi_req_id").val(txt_wo_pi_req_id);
		$("#txt_wo_pi_req").val(txt_wo_pi_req);

		//$("#cbo_supplier").attr("disabled",true);
		$("#txt_wo_pi_req").attr("disabled",true);
		var basis=$("#cbo_receive_basis").val();
		if(basis==1 || basis==2 || basis==7)
		{
			show_list_view($("#cbo_receive_basis").val()+"**"+$("#txt_wo_pi_req_id").val()+"**"+company,'show_product_listview','list_product_container','requires/general_item_receive_controller','setFilterGrid(\'list_view\',-1)');
		}
		else 
		{
			reset_form('','list_product_container','','','','');
		}
		set_button_status(0, permission, 'fnc_general_item_receive_entry',1,1);
 	}
}

function popup_serial()
{
	var serialno = $("#txt_serial_no").val();
	var serialqty = $("#txt_serial_qty").val();
	var serialString=serialno+'**'+serialqty;
	var page_link="requires/general_item_receive_controller.php?action=serial_popup&serialString='"+serialString+"'";
	var title="Serial Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var txt_string=this.contentDoc.getElementById("txt_string").value; // mrr number
		var txt_qty=this.contentDoc.getElementById("txt_qty").value;
 		$("#txt_serial_no").val(txt_string);
		$("#txt_serial_qty").val(txt_qty);
  	}
}

function popup_description()
{
	if( form_validation('cbo_company_id*cbo_store_name*cbo_item_category*cbo_item_group','Company Name*Store*Item Category*Item Group')==false )
	{
		return;
	}
	var company_id = $("#cbo_company_id").val();
	var item_category = $("#cbo_item_category").val();
	var item_group = $("#cbo_item_group").val();
	var cbo_store_name = $("#cbo_store_name").val();
	var page_link="requires/general_item_receive_controller.php?action=item_description_popup&company_id="+company_id+"&item_category="+item_category+"&item_group="+item_group+"&cbo_store_name="+cbo_store_name;
	var title="Item Description Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=320px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var product_id_td=this.contentDoc.getElementById("product_id_td").value;
		var item_description_td=this.contentDoc.getElementById("item_description_td").value;
		var current_stock=this.contentDoc.getElementById("current_stock").value;
		var brand_name=this.contentDoc.getElementById("brand_name").value;
		var origin=this.contentDoc.getElementById("origin").value;
		var model=this.contentDoc.getElementById("model").value;//new dev
		var order_uom=this.contentDoc.getElementById("order_uom").value;
		var re_order_level=this.contentDoc.getElementById("re_order_level").value;
		$("#current_prod_id").val(product_id_td);
		$("#txt_item_desc").val(item_description_td);
		$("#txt_glob_stock").val(current_stock);
		$("#txt_brand").val(brand_name);
		$("#cbo_origin").val(origin);
		$("#txt_model").val(model);// new dev
		$("#cbo_uom").val(order_uom);
		$("#txt_re_order_level").val(re_order_level);
		$('#cbo_item_category').attr('disabled',true);
		$('#cbo_item_group').attr('disabled',true);
  	}
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);
	disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_receive_purpose*cbo_store_name*txt_wo_pi*cbo_yarn_count*cbo_yarn_type*cbocomposition1*percentage1*cbo_color', 0, "", "" );
	set_button_status(0, permission, 'fnc_general_item_receive_entry',1);
	reset_form('genralitem_receive_1','list_container*list_product_container*accounting_posting_td','','','','cbo_company_id*cbo_receive_basis*cbo_store_name*cbo_uom*txt_exchange_rate*cbo_color');
	$("#txt_rate").val(0);
}

function check_exchange_rate()
{
	var cbo_company_id=$('#cbo_company_id').val();
	var cbo_currercy=$('#cbo_currency').val();
	var receive_date = $('#txt_receive_date').val();
	if( form_validation('cbo_company_id*cbo_currency*txt_receive_date','Company Name*Currency*Date')==false )
	{
		return;
	}
	var response=return_global_ajax_value( cbo_currercy+"**"+receive_date+"**"+cbo_company_id, 'check_conversion_rate', '', 'requires/general_item_receive_controller');
	var response=response.split("_");
	fn_calile();
	$('#txt_exchange_rate').val(response[1]);
	$('#txt_exchange_rate').attr('disabled','disabled');
}



/*function get_receive_basis(company_id){
	var data="action=get_receive_basis&company_id="+company_id;
	http.open("POST","requires/general_item_receive_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = get_receive_basis_reponse;
}

function get_receive_basis_reponse()
{
	if(http.readyState == 4)
	{
		var reponse=trim(http.responseText);
		$("#receive_baisis_td").html(reponse);
		release_freezing();
	}
}*/

function store_wise_stock(store_id){
	var prod_id=$("#current_prod_id").val();
	if (prod_id !=""){
		var all_data=prod_id + "__" + store_id;
		var store_stock = return_global_ajax_value(all_data, 'store_wise_stock', '', 'requires/general_item_receive_controller');
		$("#txt_glob_stock").val(store_stock); 
	}
	else return;
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
	
	var varible_string=return_global_ajax_value( data, 'varible_inventory', '', 'requires/general_item_receive_controller');
	
	var varible_string_ref=varible_string.split("**");
	//alert(varible_string_ref[0]);
	if(varible_string_ref[0])
	{
		$('#variable_string_inventory').val(varible_string_ref[1]+"**"+varible_string_ref[2]+"**"+varible_string_ref[3]+"**"+varible_string_ref[4]);
		if(varible_string_ref[1]==1)
		{
			$("#cbo_receive_basis option[value='4']").hide();
		}
		else
		{
			$("#cbo_receive_basis option[value='4']").show();
		}
		$('#is_rate_optional').val(varible_string_ref[2]);
		if(varible_string_ref[4]==2)
		{
			$('#txt_rate').attr("readonly",true);
		}
		else
		{
			$('#txt_rate').attr("readonly",false);
		}
		
		if(varible_string_ref[3]==1)
		{
			$('#rate_td').css("display", "none");
			$('#amount_td').css("display", "none");
			$('#book_currency_td').css("display", "none");
		}
		else
		{
			$('#rate_td').css("display", "");
			$('#amount_td').css("display", "");
			$('#book_currency_td').css("display", "");
		}
		
	}
	else
	{
		$('#variable_string_inventory').val("");
		$("#cbo_receive_basis option[value='4']").show();
		$('#is_rate_optional').val("");
		$('#txt_rate').attr("readonly",false);
		$('#rate_td').css("display", "");
		$('#amount_td').css("display", "");
		$('#book_currency_td').css("display", "");
	}
	
	
	//alert(varible_string);return;

    // ==============Start Floor Room Rack Shelf Bin upto variable Settings============
	
	$('#store_update_upto').val(varible_string_ref[5]);
	$('#variable_lot').val(varible_string_ref[6]);
	
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

function openmypage_addiInfo()
{
	var title = "Additional Info Details";
	var pre_addi_info = $('#txt_addi_info').val();
	page_link='requires/general_item_receive_controller.php?action=addi_info_popup&pre_addi_info='+pre_addi_info;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px, height=350px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var addi_info_string=this.contentDoc.getElementById("txt_string").value;
		$('#txt_addi_info').val(addi_info_string);
	}
}

	function print_report_button_setting(report_ids)
	{
		$("#Print1").hide();
		$("#btn_print").hide();
		$("#btn_print3").hide();
		$("#btn_print4").hide();
		$("#btn_print5").hide();
		$("#btn_print6").hide();
		$("#btn_print7").hide();
		$("#btn_print8").hide();
		$("#btn_print9").hide();
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==78) $("#Print1").show();
			if(report_id[k]==66) $("#btn_print").show();
			if(report_id[k]==85) $("#btn_print3").show();
			if(report_id[k]==137) $("#btn_print4").show();
			if(report_id[k]==129) $("#btn_print5").show();
			if(report_id[k]==72) $("#btn_print6").show();
			if(report_id[k]==191) $("#btn_print7").show();
			if(report_id[k]==220) $("#btn_print8").show();
			if(report_id[k]==235) $("#btn_print9").show();
		}
	}
	function fnc_load_print_report_setting(data)
	{
		get_php_form_data(data,'report_formate_setting','requires/general_item_receive_controller');
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />
    <form name="genralitem_receive_1" id="genralitem_receive_1" autocomplete="off" >
    <div style="width:1000px;">
    <table width="980" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="80%" align="center" valign="top">
            	<fieldset style="width:900px; float:left;">
                <legend>General Item Receive</legend>
                <br />
                 	<fieldset style="width:900px;">
                        <table  width="880" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                                <td colspan="6" align="center">&nbsp;<b>MRR Number</b>
                                <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />
                                <input type="hidden" id="hidden_mrr_id" name="hidden_mrr_id" value="" />
                                <input type="hidden" id="hidden_posted_in_account" name="hidden_posted_in_account" value="" />
                                </td>
                           </tr>
                           <tr>
                                <td  width="120" class="must_entry_caption">Company Name </td>
                                <td width="170">
                                <?
								//create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg="", $selected_index="", $onchange_func="", $is_disabled="", $array_index="", $fixed_options="", $fixed_values="", $not_show_array_index="", $tab_index="", $new_conn="", $field_name="", $additionalClass="", $additionalAttributes="")
                                echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/general_item_receive_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/general_item_receive_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td'); independence_basis_controll_function(this.value);load_drop_down('requires/general_item_receive_controller', this.value, 'load_drop_down_store','store_td');fnc_load_print_report_setting(this.value)" );
                                	//load_drop_down( 'requires/general_item_receive_controller', this.value, 'load_drop_down_store', 'store_td' );
                                ?>
                             	<input type="hidden" name="variable_string_inventory" id="variable_string_inventory" />
                                <input type="hidden" id="is_rate_optional" name="is_rate_optional">
                                <input type="hidden" id="variable_lot" name="variable_lot" />
                                </td>
                                <td width="120" align="" class="must_entry_caption"> Receive Basis </td>
                                <td width="170" id="receive_baisis_td">
                                <?
                                echo create_drop_down( "cbo_receive_basis", 170, $receive_basis_arr,"", 1, "-- Select --", $selected, "fn_onCheckBasis(this.value)","","1,2,4,6,7" );
                                ?>
                                </td>
                                <td width="120" align="" class="must_entry_caption">Receive Date</td>
                                <td width="170"><input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:160px;" onChange="check_exchange_rate();" placeholder="Select Date" value="<? echo date("d-m-Y");?>" readonly /></td>
                            </tr>
                            <tr>
                                <td>Challan</td>
                                <td ><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" ></td>
                                <td >Challan Date</td>
                                <td ><input type="text" name="txt_challan_date_mst" id="txt_challan_date_mst" class="datepicker" style="width:160px" readonly></td>
                                <td>Bill No</td>
                                <td ><input type="text" name="txt_bill_no_mst" id="txt_bill_no_mst" class="text_boxes" style="width:160px" ></td>
                             </tr>
                             <tr>
                                <td>Bill Date</td>
                                <td><input type="text" name="txt_bill_date_mst" id="txt_bill_date_mst" class="datepicker" style="width:160px" readonly></td>
                                <td >Receive Purpose </td>
                                <td >
                                    <?
                                    echo create_drop_down( "cbo_receive_purpose", 170, $general_issue_purpose,"", 1, "--Select--", $selected, "","","5"  );
                                    ?>
                            	</td>
                                <td >Loan Party </td>
								<td id="loan_party_td">
								<?
									echo create_drop_down( "cbo_loan_party", 170, $blank_array,"", 1, "-- Select Loan Party --", $selected, "","","" );
								?>
								</td>
                            </tr>
                            <tr>
                                <td width="" class="must_entry_caption">Supplier</td>
                                <td id="supplier" >
                                <?
                                	echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type in(1,5,6,7,8,30,36,37,39,92) and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
                                ?>
                                </td>
                                <td width="" align="" class="must_entry_caption">Currency</td>
                                <td  id="currency">
                                <? //get_php_form_data(this.value,'load_exchange_rate','requires/general_item_receive_controller')
                                	echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select Currency --",$currencyID, "check_exchange_rate();",1 );
                                ?>
                                </td>
                                <td class="must_entry_caption">Pay Mode</td>
                                <td >
                                <?
                                	echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", $selected, "",1 );
                                ?>
                                </td>
                            </tr>
                             <tr>
							 	<td align="" class="must_entry_caption">Store Name</td>
                                <td  id="store_td">
                                <?
                                	echo create_drop_down( "cbo_store_name", 170, $blank_array,"",1, "--Select store--", 1, "" );
                                ?>
                                </td>
                                
                                <td width="" align="" class="must_entry_caption">Source</td>
                                <td  id="sources" class="must_entry_caption">
                                <?
                                	echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", $selected, "fn_calile()",1 );
                                ?>
                                </td>
                                <td> L/C No</td>
                                <td id="lc_no">
                                <input class="text_boxes"  type="text" name="txt_lc_no" id="txt_lc_no" style="width:160px;" placeholder="Display" onDblClick="popuppage_lc()" readonly disabled  />
                                <input type="hidden" name="hidden_lc_id" id="hidden_lc_id" />
                                </td>
                            </tr>
                            <tr>
								<td width="" align="">WO/PI/Req.No</td>
                                <td ><input class="text_boxes"  type="text" name="txt_wo_pi_req" id="txt_wo_pi_req" onDblClick="openmypage('xx','Order Search')"  placeholder="Double Click" style="width:160px;"  readonly disabled />
                                <input type="hidden" id="txt_wo_pi_req_id" name="txt_wo_pi_req_id" value="" />
                                <input type="hidden" id="txt_ref_no" name="txt_ref_no" value="" />
								</td>
                            	<td> Supplier Ref.</td>
                                <td><input type="text" name="txt_sup_ref" id="txt_sup_ref" class="text_boxes" style="width:160px" ></td>
                                <td>Addi. Info</td>
                                <td>
                                <input type="text" id="txt_addi_popup" name="txt_addi_popup" class="text_boxes" onDblClick="openmypage_addiInfo()"  placeholder="Double Click" style="width:160px;" readonly >

                                <input type='hidden' id="txt_addi_info" name="txt_addi_info" value="">
                                </td> 
                                
                            </tr>
                            <tr>
                                <td width="" align="">Exchange Rate</td>
                                <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:160px"   onBlur="fn_calile()"  readonly /></td>
                            	<td >Remarks</td>
                                <td>
                                <input type="text" id="txt_remarks" name="txt_remarks" class="text_boxes" style="width:160px;" maxlength="255" >
                                </td>
								<td >File</td>
                                <td>
									<input type="file" class="image_uploader" id="multiple_file_field" name="multiple_file_field" multiple style="width:160px">

									<input type="button" class="image_uploader" style="width:160px" id="btn_fileadd" value="CLICK TO ADD FILE" onClick="file_uploader ( '../../', document.getElementById('hidden_mrr_id').value,'', 'general_item_receive', 2 ,1)">
								</td>
                                
                            </tr>
							<tr>
								<td>BOE/Mushak Challan No</td>                                              
								<td> 
									<input type="text" name="txt_boe_mushak_challan_no" id="txt_boe_mushak_challan_no" class="text_boxes" style="width:160px">
								</td>
								<td>BOE/Mushak Challan Date</td>                                              
								<td> 
									<input type="text" name="txt_boe_mushak_challan_date" id="txt_boe_mushak_challan_date" class="datepicker" style="width:160px">
								</td>
							</tr>
                        </table>
                    </fieldset>
                    <br />
                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                    <tr>
	                    <td width="49%" valign="top">
	                   	  	<fieldset style="width:950px;">
		                        <legend>New Receive Item</legend>
		                        <table width="250" cellspacing="2" cellpadding="0" border="0" style="float:left">
									<tr>
								        <td width="130" class="must_entry_caption">Item Category.</td>
								        <td width="130">
								            <?
								           //var_dump($item_cate_credential_cond);
								            echo create_drop_down( "cbo_item_category", 130, $general_item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/general_item_receive_controller', this.value+'_'+$('#cbo_company_id').val(), 'load_drop_down_itemgroup', 'item_group_td' );", 0,"$item_cate_credential_cond" );
								            ?>
								        </td>
									</tr>
									<tr>
								        <td class="must_entry_caption">Item Group</td>
								        <td id="item_group_td">
								        <?
											//load_drop_down( 'requires/general_item_receive_controller', this.value, 'load_drop_down_uom', 'uom_td' );
								            echo create_drop_down( "cbo_item_group", 130, "select id,item_name from lib_item_group where item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22) and status_active and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", $disabled,"" );
								         ?>
								        </td>
									</tr>
									<tr>
								        <td class="must_entry_caption">Item Description.</td>
								        <td>
								             <input name="txt_item_desc" id="txt_item_desc" class="text_boxes" type="text" style="width:120px;" placeholder="Double Click" onClick="popup_description()" readonly />
								             <input type="hidden" name="hid_req_dtls_id" id="hid_req_dtls_id" />
								        </td>
									</tr>
									<tr>
								        <td>UOM</td>
								        <td id="uom_td"><?
								    		echo create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 1, "-- Select --", 0, "", 1 );
										?></td>
									</tr>
									<tr>
								        <td>Store Stock</td>
								        <td ><input type="text" id="txt_glob_stock" name="txt_glob_stock" class="text_boxes_numeric" style="width:120px" placeholder="Display" readonly disabled ></td>
									</tr>
                                    <tr>    
                                        <td id="lot_caption">Lot</td>
                                    	<td><input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:120px;"/></td> 
                                	</tr>
								</table>
		                        <table width="220" cellspacing="2" cellpadding="0" border="0" style="float:left">
									<tr>
									    <td width="80" class="must_entry_caption">Recv. Qnty.</td>
										<td width="130" id="uom_td">
									    <input name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()"/>
									    </td>
									</tr>
									<tr id="rate_td">
									    <td class="must_entry_caption">Rate</td>
									    <td ><input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()" value="0" /></td>
									</tr>
									<tr>
									    <td id="ile_td">ILE%</td>
									    <td ><input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:120px;" placeholder="ILE COST" readonly /></td>
									</tr>
									<tr id="amount_td">
										<td >Amount</td>
										<td><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>
									</tr>
									<tr>
										<td> Brand</td>
										<td>
										   <input class="text_boxes"  name="txt_brand" id="txt_brand" type="text" style="width:120px;" readonly disabled/>
										</td>
									</tr>
                                    <tr>
										<td> Re-Order Level</td>
										<td>
										   <input class="text_boxes_numeric"  name="txt_re_order_level" id="txt_re_order_level" type="text" style="width:120px;" readonly disabled/>
										</td>
									</tr>
		                        </table>
		                        <table width="280" cellspacing="2" cellpadding="0" border="0" style="float:left">
		                                <tr id="book_currency_td">
		                                    <td width="150">Book Currency.</td>
		                                    <td width="130"><input type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>
		                                </tr>
		                                <tr>
		                                  	<td>Warranty Exp. Date</td>
		                                  	<td><input type="text" name="txt_warranty_date" id="txt_warranty_date" class="datepicker" style="width:120px;" /></td>
		                                </tr>
		                                 <tr>
		                                    <td> Serial No</td>
		                                    <td>
		                                    <input name="txt_serial_no" id="txt_serial_no" class="text_boxes" type="text" style="width:120px;" placeholder="Double Click" onDblClick="popup_serial()" />
		                                     <input name="txt_serial_qty" id="txt_serial_qty"  type="hidden" />
		                                    </td>
		                                </tr>
		                                <tr>
		                                    <td>Bal. PI/ Ord/Req Qnty</td>
		                                    <td><input class="text_boxes_numeric"  name="txt_order_qty" id="txt_order_qty" type="text" style="width:120px;" readonly /></td>
		                                </tr>
		                                <tr>
		                                    <td> Origin </td>
		                                    <td><?
		                                    echo create_drop_down( "cbo_origin", 132, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0,"",1 );
		                                    ?></td>
		                                </tr>
		                        </table>
		                        <table width="190" cellspacing="2" cellpadding="0" border="0">
									<tr>
										<td width="80">Floor</td>
										<td id="floor_td">
											<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td width="80">Room</td>
										<td id="room_td">
											<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Rack</td>
										<td id="rack_td">
											<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Shelf</td>
										<td id="shelf_td">
											<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<tr>
										<td>Bin/Box</td>
										<td id="bin_td">
											<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
										</td>
									</tr>
									<!-- new dev -->
									<tr>
										<td>Model</td>
										<td>
											<input class="text_boxes"  name="txt_model" id="txt_model" type="text" style="width:140px;" readonly disabled/>
										</td>
									</tr>
		                        </table>
	                    	</fieldset>
	                    </td>
                    </tr>
                    <tr>
                    	<td style="padding-left:60px;">Comments: <input class="text_boxes"  name="txt_referance" id="txt_referance" type="text" style="width:810px;"  /></td>
                    </tr>
                </table>
               	<table cellpadding="0" cellspacing="1" width="100%">
                	<tr>
                       <td colspan="6" align="center"></td>
                	</tr>
                	<tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
							<!-- details table id for update -->
							<div id="audited" style="float:left; font-size:24px; color:#FF0000;"></div>
							<input type="hidden" id="txt_prod_id" name="txt_prod_id" value="" />
							<input type="hidden" id="update_id" name="update_id" value="" />
							<input type="hidden" id="current_prod_id" name="current_prod_id"/>
							<input type="hidden" name="store_update_upto" id="store_update_upto" />

							<!-- -->
							<? echo load_submit_buttons( $permission, "fnc_general_item_receive_entry", 0,1,"fnResetForm()",1);?>
							<input type="button" id="btn_print" value="Print2" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(2);" >
							<input type="button" id="btn_print3" value="Print3" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(3);" >
							<input type="button" id="btn_print4" value="Print4" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(4);" >
							<input type="button" id="btn_print5" value="Print5" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(5);" >
							<input type="button" id="btn_print6" value="Print6" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(6);" >
							<input type="button" id="btn_print7" value="Print 7" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(7);" >
							<input type="button" id="btn_print8" value="Print 8" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(8);" >
							<input type="button" id="btn_print9" value="Print 9" class="formbutton" style="display:none;width:100px;" onClick="show_print_report(9);" >
							
							<div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
                        </td>
                   </tr>
                </table>
              	</fieldset>
           	</td>
        </tr>
    </table>
    </div>
    <div id="list_product_container" style="max-height:500px; width:350px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
    <div style="clear:both"></div>
    <div style="width:auto;" id="list_container"></div>

	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$(function(){
		//alert("body loaded");
		check_exchange_rate();
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
	$("#cbo_receive_purpose").val(0);	
</script>
</html>
