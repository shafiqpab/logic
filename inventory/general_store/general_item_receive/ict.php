<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create 
				
Functionality	:	
JS Functions	:
Created by		:	Bilas 
Creation date 	: 	29-08-2013
Updated by 		: 	Kausar / Jahid/ Reza			
Update date		: 	29-10-2013	 (Creating report	)
QC Performed BY	:	
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

// for autocomplete brand
$brand_sql = sql_select("select brand_name from lib_brand order by brand_name");
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

// last yarn receive exchange rate, currency, store name---------------
$sql = sql_select("select store_id,exchange_rate,currency_id,max(id) from inv_receive_master where item_category in (8,9,10,11,18,68)");
$storeName=$exchangeRate=$currencyID=0;
foreach($sql as $row)
{
	$storeName=$row[csf("store_id")];
	$exchangeRate=$row[csf("exchange_rate")];
	$currencyID=$row[csf("currency_id")];
}

$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=20 and status_active=1 and is_deleted=0",'company_name','independent_controll');

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
<?
if($_SESSION['logic_erp']['mandatory_field'][596]!="")
{
	$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][596] );
	echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
}
?>	
// popup for WO/PI----------------------	
function openmypage(page_link,title,itme_category_id)
{
	if( form_validation('cbo_company_name*cbo_receive_basis','Company Name*Receive Basis')==false )
	{
		return;
	}
	var company = $("#cbo_company_name").val();
	var receive_basis = $("#cbo_receive_basis").val();
	 
	page_link='requires/general_item_receive_controller.php?action=wopi_popup&company='+company+'&receive_basis='+receive_basis+'&itme_category_id='+itme_category_id;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px, height=400px, center=1, resize=0, scrolling=0','../../')
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
			//alert(rowID);
			get_php_form_data(receive_basis+"**"+rowID, "populate_data_from_wopi_popup", "requires/general_item_receive_controller" );
			show_list_view(receive_basis+"**"+rowID+"**"+itme_category_id,'show_product_listview','list_product_container','requires/general_item_receive_controller','');
			release_freezing();
			$("#tbl_child").find('input[type="text"],input[type="hidden"],select').val('');	 
		}
	}		
}

// enable disable field for independent
function fn_onCheckBasis(val)
{
	if(val==4 || val==6)
	{    
		$("#cbo_supplier").attr("disabled",false);
		$("#cbo_currency").attr("disabled",false);
		$("#cbo_source").attr("disabled",false);
 		$("#txt_lc_no").attr("disabled",true);
		$("#txt_wo_pi_req").attr("disabled",true);
		$("#cbo_pay_mode").attr("disabled",false);
	}
	else if(val==7)
	{    
		$("#cbo_supplier").attr("disabled",false);
		$("#cbo_currency").attr("disabled",true);
		$("#cbo_source").attr("disabled",true);
		$("#txt_lc_no").attr("disabled",true);
 		$("#txt_wo_pi_req").attr("disabled",false);
		$("#cbo_pay_mode").attr("disabled",true);
	}
	else
	{
 		$("#cbo_supplier").attr("disabled",true);
		$("#cbo_currency").attr("disabled",true);
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
	if(val==4 || val==6 || val==7){
		$("#txt_rate").attr("disabled",false);
	}
	else
	{
		$("#txt_rate").attr("disabled",true);
		$("#txt_rate").val(0);
	}
	
	
	
}

// LC pop up script here-----------------------------------
function popuppage_lc()
{
	
	if( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	var receive_basis = $("#cbo_receive_basis").val();
	if(receive_basis*1==1 || receive_basis*1==2 || receive_basis*1==7)
	{
		return;
	}
	var company = $("#cbo_company_name").val();	
	var page_link='requires/general_item_receive_controller.php?action=lc_popup&company='+company; 
	var title="Search LC Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=0,scrolling=0','../../')
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
	//if( form_validation('cbo_company_name*cbo_source*txt_rate','Company Name*Source*Rate')==false )
	//{
	//	return;
	//}
	
	var company=$('#cbo_company_name').val();	
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

/*function fn_room_rack_self_box()
{ 
	if( $("#txt_room").val()*1 > 0 )  
		disable_enable_fields( 'txt_rack', 0, '', '' ); 
	else
	{
		reset_form('','','txt_rack*txt_self*txt_binbox','','','');
		disable_enable_fields( 'txt_rack*txt_self*txt_binbox', 1, '', '' ); 
	}
	if( $("#txt_rack").val()*1 > 0 )  
		disable_enable_fields( 'txt_self', 0, '', '' ); 
	else
	{
		reset_form('','','txt_self*txt_binbox','','','');
		disable_enable_fields( 'txt_self*txt_binbox', 1, '', '' ); 	
	}
	if( $("#txt_self").val()*1 > 0 )  
		disable_enable_fields( 'txt_binbox', 0, '', '' ); 
	else
	{
		reset_form('','','txt_binbox','','','');
		disable_enable_fields( 'txt_binbox', 1, '', '' ); 	
	}
}*/

/*function show_print_report()
{
	if($('#hidden_mrr_id').val()=="")
	{
		alert("Please Save Data First.");
		return;
	}
	else
	{
		var report_title=$( "div.form_caption" ).html();
		var data= $('#cbo_company_name').val()+'*'+$('#hidden_mrr_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val();
		var action='general_item_receive_print_new';
		window.open("requires/general_item_receive_controller.php?data=" + data+'&action='+action, true );
		
		var report_title=$( "div.form_caption" ).html();txt_mrr_no
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val(), "chemical_dyes_receive_print", "requires/chemical_dyes_receive_controller" ) 
		return;
	}
}*/

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
		else
		{
			var action='general_item_receive_print_3';
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data= $('#cbo_company_name').val()+'*'+$('#hidden_mrr_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val();
		window.open("requires/general_item_receive_controller.php?data=" + data+'&action='+action, true );
		
		/*var report_title=$( "div.form_caption" ).html();txt_mrr_no
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val(), "chemical_dyes_receive_print", "requires/chemical_dyes_receive_controller" ) */
		return;
	}
}
function fnc_general_item_receive_entry(operation)
{
	if(operation==4)
	 {
		 var report_title=$( "div.form_caption" ).html();
		 print_report( $('#cbo_company_name').val()+'*'+$('#hidden_mrr_id').val()+'*'+report_title, "general_item_receive_print", "requires/general_item_receive_controller" ) 
		 return;
	 }
	else if(operation==0 || operation==1 || operation==2)
	{
		if($("#hidden_posted_in_account").val()*1==1)
		{
			alert("Already Posted In Accounts.Save,Update & Delete Not Allowed.");
            return;
		}
		
		
		/*if(operation==2)
		{
			alert("Delete Permission Not allow.");return;
		}*/
		
		if( form_validation('cbo_company_name*cbo_receive_basis*txt_receive_date*cbo_store_name*cbo_supplier*cbo_currency*cbo_source*cbo_item_category*cbo_item_group*txt_item_desc*txt_receive_qty*txt_rate','Company Name*Receive Basis*Receive Date*Store Name*Supplier*Currency*Source*Item Category*Item Group*Item Description*Receive Quantity*Rate')==false )
		{
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][596]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][596]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][596]); ?>')==false) {release_freezing();return;}
		}

		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Recevied Date Can not Be Greater Than Current Date");
			return;
		}
		if($("#txt_exchange_rate").val()<=0)
		{
			alert("Exchange Rate Should Be More Then Zero");
			$("#txt_exchange_rate").focus();
			return;
		}
		if($("#txt_receive_qty").val()<=0)
		{
			alert("Receive Quantity Should Be More Then Zero");
			$("#txt_receive_qty").focus();
			return;
		}
		if($("#txt_rate").val()<=0)
		{
			alert("Rate Should Be More Then Zero");
			$("#txt_rate").focus();
			return;
		} 

		// Store upto validation start
		var store_update_upto=$('#store_update_upto').val()*1;
		var txt_floor=$('#cbo_floor').val()*1;
		var txt_room=$('#txt_room').val()*1;
		var txt_rack=$('#txt_rack').val()*1;
		var txt_shelf=$('#txt_self').val()*1;
		var txt_bin=$('#txt_binbox').val()*1;
		
		if(store_update_upto > 1)
		{
			if(store_update_upto==6 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0 || txt_bin==0))
			{
				alert("Up To Bin Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==5 && (txt_floor==0 || txt_room==0 || txt_rack==0 || txt_shelf==0))
			{
				alert("Up To Shelf Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==4 && (txt_floor==0 || txt_room==0 || txt_rack==0))
			{
				alert("Up To Rack Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==3 && (txt_floor==0 || txt_room==0))
			{
				alert("Up To Room Value Full Fill Required For Inventory");return;
			}
			else if(store_update_upto==2 && txt_floor==0)
			{
				alert("Up To Floor Value Full Fill Required For Inventory");return;
			}
		}
		// Store upto validation End
		var dataString = "txt_mrr_no*hidden_mrr_id*cbo_company_name*cbo_receive_basis*txt_receive_date*txt_challan_no*txt_lc_no*hidden_lc_id*cbo_store_name*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_pay_mode*cbo_source*txt_wo_pi_req*txt_wo_pi_req_id*txt_remarks*cbo_item_category*cbo_item_group*txt_item_desc*txt_serial_no*txt_serial_qty*cbo_uom*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_order_qty*txt_warranty_date*cbo_floor*txt_room*txt_rack*txt_self*txt_binbox*current_prod_id*txt_prod_id*update_id*txt_sup_ref*txt_referance*txt_addi_info*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../../");
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
		var receive_basis=$("#cbo_receive_basis").val();
		//alert(http.responseText);release_freezing();return;
 		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();return;
		}
		else if(reponse[0]==10)
		{
			release_freezing();return;
		}
		else if(reponse[0]==30)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}		
		else if(reponse[0]==0)
		{
			show_msg(reponse[0]);
			$("#txt_mrr_no").val(reponse[1]);
			$("#hidden_mrr_id").val(reponse[2]);
			//disable_enable_fields( 'cbo_receive_basis*cbo_store_name*cbo_supplier*txt_wo_pi_req*cbo_company_name*cbo_currency', 1, "", "" );
			reset_form('','','cbo_floor*txt_room*txt_rack*txt_self*txt_binbox','','','');
			show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/general_item_receive_controller','');
			if(receive_basis==1 || receive_basis==2 || receive_basis==7)
			{
				show_list_view($("#cbo_receive_basis").val()+"**"+$("#txt_wo_pi_req_id").val()+"**"+$("#cbo_item_category").val(),'show_product_listview','list_product_container','requires/general_item_receive_controller','');
			}
 			//$("#tbl_master :input").attr("disabled", true);	
		}
		else if(reponse[0]==1)
		{
			//alert(http.responseText);return;
			show_msg(reponse[0]);
			//disable_enable_fields( 'cbo_receive_basis*cbo_store_name*cbo_supplier*txt_wo_pi_req*cbo_company_name*cbo_currency', 1, "", "" );			 
 			//$("#tbl_master :input").attr("disabled", true);	
 			reset_form('','','cbo_floor*txt_room*txt_rack*txt_self*txt_binbox','','','');
			show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/general_item_receive_controller','');
			if(receive_basis==1 || receive_basis==2 || receive_basis==7)
			{
				show_list_view($("#cbo_receive_basis").val()+"**"+$("#txt_wo_pi_req_id").val()+"**"+$("#cbo_item_category").val(),'show_product_listview','list_product_container','requires/general_item_receive_controller','');
			}
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
				//reset_form('genralitem_receive_1','list_container*list_product_container','','','','');
				//set_button_status(0, permission, 'fnc_general_item_receive_entry',1,0);
				//release_freezing();return;
				show_msg(reponse[0]);
				//$("#tbl_master :input").attr("disabled", true);
				reset_form('','','cbo_floor*txt_room*txt_rack*txt_self*txt_binbox','','','');
				show_list_view(reponse[2],'show_dtls_list_view','list_container','requires/general_item_receive_controller','');
			}
		}
		
		disable_enable_fields( 'cbo_receive_basis*cbo_store_name*cbo_supplier*txt_wo_pi_req*cbo_company_name*cbo_currency', 1, "", "" );		
  		//child form reset here after save data-------------//
  		//$("#tbl_child").find('input,select').val('');
		$("#tbl_child").find('input,select:not([name="cbo_item_category"]) select:not([name="cbo_item_group"])').val('');
		set_button_status(0, permission, 'fnc_general_item_receive_entry',1,1);
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
	var page_link='requires/general_item_receive_controller.php?action=mrr_popup&company='+company+'&item_category=18,68'; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrr_id=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
 		//$("#txt_mrr_no").val(mrrNumber);
		
		// master part call here
		get_php_form_data(mrr_id, "populate_data_from_data", "requires/general_item_receive_controller");
		$("#tbl_master").find('input,select').attr("disabled", true);	
		disable_enable_fields( 'txt_mrr_no*txt_remarks*txt_sup_ref*txt_addi_popup', 0, "", "" );
		//fn_onCheckBasis($("#cbo_receive_basis").val());
		
		var posted_in_account=$("#hidden_posted_in_account").val()*1;
		if(posted_in_account==1) 	$("#accounting_posting_td").text("Already Posted In Accounts.");
		else 						$("#accounting_posting_td").text("");
		
		var basis=$("#cbo_receive_basis").val();
		if(basis==1 || basis==2 || basis==7)
		{
			show_list_view($("#cbo_receive_basis").val()+"**"+$("#txt_wo_pi_req_id").val()+"**18",'show_product_listview','list_product_container','requires/general_item_receive_controller','');
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
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=400px,center=1,resize=1,scrolling=0','../../')
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
	if( form_validation('cbo_company_name*cbo_item_category*cbo_item_group','Company Name*Item Category*Item Group')==false )
	{
		return;
	}
	var company_id = $("#cbo_company_name").val();
	var item_category = $("#cbo_item_category").val();
	var item_group = $("#cbo_item_group").val();
	var page_link="requires/general_item_receive_controller.php?action=item_description_popup&company_id="+company_id+"&item_category="+item_category+"&item_group="+item_group; 
	var title="Item Description Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=600px,height=320px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var product_id_td=this.contentDoc.getElementById("product_id_td").value;  
		var item_description_td=this.contentDoc.getElementById("item_description_td").value;
		var current_stock=this.contentDoc.getElementById("current_stock").value; 

		var brand_name=this.contentDoc.getElementById("brand_name").value; //new dev
        var origin=this.contentDoc.getElementById("origin").value; //new dev
        var model=this.contentDoc.getElementById("model").value;//new dev
		var order_uom=this.contentDoc.getElementById("order_uom").value;//new dev

        /* new dev*/
		$("#txt_brand").val(brand_name);
        $("#cbo_origin").val(origin);
        $("#txt_model").val(model);
		$("#cbo_uom").val(order_uom);

		$("#current_prod_id").val(product_id_td); 
 		$("#txt_item_desc").val(item_description_td);
		$("#txt_glob_stock").val(current_stock);
  	}
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);	
	disable_enable_fields( 'cbo_company_name*cbo_receive_basis*cbo_receive_purpose*cbo_store_name*txt_wo_pi*cbo_yarn_count*cbo_yarn_type*cbocomposition1*percentage1*cbo_color', 0, "", "" );
	set_button_status(0, permission, 'fnc_general_item_receive_entry',1);
	reset_form('genralitem_receive_1','list_container*list_product_container*accounting_posting_td','','','','cbo_uom*cbo_currency*txt_exchange_rate*cbo_color');
	$("#txt_rate").val(0);
}
function check_exchange_rate()
{
	var cbo_currercy=$('#cbo_currency').val();
	var receive_date = $('#txt_receive_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+receive_date, 'check_conversion_rate', '', 'requires/general_item_receive_controller');
	var response=response.split("_");
	fn_calile();
	$('#txt_exchange_rate').val(response[1]);
	$('#txt_exchange_rate').attr('disabled','disabled');
}

function independence_basis_controll_function(data)
{
    var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
    $("#cbo_receive_basis").val(0);
    $("#cbo_receive_basis option[value='4']").show();
    if(independent_control_arr[data]==1)
    {
        $("#cbo_receive_basis option[value='4']").hide();
    }

    // ==============Start Floor Room Rack Shelf Bin upto variable Settings============
    var data='cbo_company_id='+data+'&action=upto_variable_settings';
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() 
    {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("store_update_upto").value = this.responseText;				
        }
    }
    xmlhttp.open("POST", "requires/general_item_receive_controller.php", true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send(data);
    // ==============End Floor Room Rack Shelf Bin upto variable Settings============
}

function openmypage_addiInfo()
{
	var title = "Additional Info Details";
	var pre_addi_info = $('#txt_addi_info').val();
	page_link='requires/general_item_receive_controller.php?action=addi_info_popup&pre_addi_info='+pre_addi_info;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=700px, height=350px, center=1, resize=0, scrolling=0','../../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var addi_info_string=this.contentDoc.getElementById("txt_string").value;
		$('#txt_addi_info').val(addi_info_string);
	}		
}

// =====================Reset Store, Floor, room, rack, shelf, bin =================================
function reset_on_change(id)
{	
	if(id =="cbo_store_name")
	{
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_floor','to_floor_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_room','to_room_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_rack','to_rack_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_shelf','to_shelf_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_bin','to_bin_td');
	}
	else if(id =="cbo_company_name")
	{
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_down_store','store_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_floor','to_floor_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_room','to_room_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_rack','to_rack_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_shelf','to_shelf_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_bin','to_bin_td');
	}
	else if(id =="cbo_floor")
	{
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_rack','to_rack_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_shelf','to_shelf_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_bin','to_bin_td');
	}
	else if(id =="txt_room")
	{
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_shelf','to_shelf_td');
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_bin','to_bin_td');
	}
	else if(id =="txt_rack")
	{
		load_drop_down('requires/general_item_receive_controller', '0', 'load_drop_bin','to_bin_td');
	}
}
// =====================Reset Store, Floor, room, rack, shelf, bin =================================

// ==============End Floor Room Rack Shelf Bin upto disable=========================================
function storeUpdateUptoDisable() 
{
	var store_update_upto=$('#store_update_upto').val()*1;	
	if(store_update_upto==5)
	{
		$('#txt_binbox').prop("disabled", true);
	}
	if(store_update_upto==4)
	{
		$('#txt_self').prop("disabled", true);
		$('#txt_binbox').prop("disabled", true);
	}
	else if(store_update_upto==3)
	{
		$('#txt_rack').prop("disabled", true);
		$('#txt_self').prop("disabled", true);
		$('#txt_binbox').prop("disabled", true);
	}
	else if(store_update_upto==2)
	{	
		$('#txt_room').prop("disabled", true);
		$('#txt_rack').prop("disabled", true);
		$('#txt_self').prop("disabled", true);	
		$('#txt_binbox').prop("disabled", true);
	}
	else if(store_update_upto==1)
	{
		$('#cbo_floor').prop("disabled", true);
		$('#txt_room').prop("disabled", true);
		$('#txt_rack').prop("disabled", true);
		$('#txt_self').prop("disabled", true);	
		$('#txt_binbox').prop("disabled", true);	
	}
}
// ==============End Floor Room Rack Shelf Bin upto disable=========================================
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
    <form name="genralitem_receive_1" id="genralitem_receive_1" autocomplete="off" > 
    <div style="width:1000px;">       
    <table width="980" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:900px; float:left;">
                <legend>General Item Receive</legend>
                <br />
                 	<fieldset style="width:920px;">                                       
                        <table  width="900" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
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
                                echo create_drop_down( "cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/general_item_receive_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/general_item_receive_controller', this.value+'_'+18, 'load_drop_down_store', 'store_td' ); independence_basis_controll_function(this.value);" );
                                ?>
                                </td>
                                <td width="120" align="" class="must_entry_caption"> Receive Basis </td>
                                <td width="170">
                                <? 
                                echo create_drop_down( "cbo_receive_basis", 170, $receive_basis_arr,"", 1, "-- Select --", $selected, "fn_onCheckBasis(this.value)","","1,2,4,6,7" );
                                ?>
                                </td>
                                <td width="120" align="" class="must_entry_caption">Receive Date</td>
                                <td width="170"><input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:160px;" onChange="check_exchange_rate();" placeholder="Select Date" /></td>
                            </tr>
                            <tr>
                                <td  align="" > Challan No</td>
                                <td ><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" ></td>
                                <td width="" align="" > L/C No</td>
                                <td  id="lc_no">
                                <input class="text_boxes"  type="text" name="txt_lc_no" id="txt_lc_no" style="width:160px;" placeholder="Display" onDblClick="popuppage_lc()" readonly disabled  />
                                <input type="hidden" name="hidden_lc_id" id="hidden_lc_id" />
                                </td>
                                <td align="" class="must_entry_caption">Store Name</td>
                                <td  id="store_td">
                                <? 
                                echo create_drop_down( "cbo_store_name", 170, $blank_array,"", 1, "-- Select --", 0, "" );
                                ?>
                                </td>
                            </tr>
                            <tr>
                                <td width="" class="must_entry_caption">Supplier</td>
                                <td id="supplier" >
                                <?
                                echo create_drop_down( "cbo_supplier", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and  b.party_type in(1,5,6,7,8,30,36,37,39) and a.status_active=1 and a.is_deleted=0  group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",1 );
                                ?>
                                </td>
                                <td width="" align="" class="must_entry_caption">Currency</td>
                                <td  id="currency">
                                <? //get_php_form_data(this.value,'load_exchange_rate','requires/general_item_receive_controller')
                                echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select Currency --", $currencyID, "check_exchange_rate();",1 );
                                ?>
                                </td>
                                <td width="" align="">Exchange Rate</td>
                                <td><input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:160px" onBlur="fn_calile()" readonly /></td>
                            </tr>
                             <tr>
                                <td width="" align="">Pay Mode</td>
                                <td >
                                <?
                                echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", $selected, "",1 );
                                ?>
                                </td>
                                <td width="" align="" class="must_entry_caption">Source</td>
                                <td  id="sources" class="must_entry_caption">  
                                <?
                                echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", $selected, "fn_calile()",1 );
                                ?>
                                </td>
                                <td width="" align="">WO/PI/Req.No</td>
                                <td ><input class="text_boxes"  type="text" name="txt_wo_pi_req" id="txt_wo_pi_req" onDblClick="openmypage('xx','Order Search','18')"  placeholder="Double Click" style="width:160px;"  readonly disabled />
                                <input type="hidden" id="txt_wo_pi_req_id" name="txt_wo_pi_req_id" value="" /></td>                                   
                            </tr>
                            <tr>
                            	<td> Supplier Ref.</td>
                                <td><input type="text" name="txt_sup_ref" id="txt_sup_ref" class="text_boxes" style="width:160px" ></td>
                                <td >Remarks</td>
                                <td>
                                <input type="text" id="txt_remarks" name="txt_remarks" class="text_boxes" style="width:160px;" maxlength="255" > 
                                </td>
                                <td >Addi. Info</td>
                                <td>
                                <input type="text" id="txt_addi_popup" name="txt_addi_popup" class="text_boxes" onDblClick="openmypage_addiInfo()"  placeholder="Double Click" style="width:160px;" readonly > 

                                <input type='hidden' id="txt_addi_info" name="txt_addi_info" value="">
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
								<td></td>
								<td></td>
                            </tr>
                            <tr>
                            	<td colspan="6" id="accounting_posting_td" style="font-size:18px; color:red"></td>
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
                                                echo create_drop_down( "cbo_item_category", 130, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'requires/general_item_receive_controller', this.value, 'load_drop_down_itemgroup', 'item_group_td' );", 0,"18,68" );
                                            ?>
                                        </td>
                                </tr>
                                <tr>    
                                        <td class="must_entry_caption">Item Group</td>
                                        <td id="item_group_td">
                                        <?
											//load_drop_down( 'requires/general_item_receive_controller', this.value, 'load_drop_down_uom', 'uom_td' );
                                            echo create_drop_down( "cbo_item_group", 130, "select id,item_name from lib_item_group where item_category in (4,8,9,10,11,15,16,17,18,19,20,21,22,68) and status_active and is_deleted=0 order by item_name", "id,item_name", 1, "-- Select --", 0, "", $disabled,"" );
                                         ?>	
                                        </td>
                                </tr>
                                <tr>    
                                        <td class="must_entry_caption">Item Description.</td>
                                        <td>
                                             <input name="txt_item_desc" id="txt_item_desc" class="text_boxes" type="text" style="width:120px;" placeholder="Double Click" onClick="popup_description()" />
                                        </td>
                                </tr>
                                <tr>   
                                        <td>UOM</td>
                                        <td id="uom_td"><?
                                    		echo create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 1, "-- Select --", 0, "", 1 );
										?></td> 
                                </tr>
                                <tr>   
                                        <td>Global Stock</td>
                                        <td ><input type="text" id="txt_glob_stock" name="txt_glob_stock" class="text_boxes_numeric" style="width:120px" placeholder="Display" readonly disabled ></td> 
                                </tr> 
                            </table>
                            
                            
                        <table width="220" cellspacing="2" cellpadding="0" border="0" style="float:left">
                            	<tr>
                                        <td width="80" class="must_entry_caption">Recv. Qnty.</td>
                                    	<td width="130" id="uom_td">
                                        <input name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()"/>
                                        </td> 
                          </tr>
                                 <tr>    
                                        <td class="must_entry_caption">Rate</td>   
                                        <td ><input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()" value="0" /></td> 
                                </tr>
                                
                                <tr>    
                                        <td id="ile_td">ILE%</td>   
                                        <td ><input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:120px;" placeholder="ILE COST" readonly /></td>
                                </tr> 
                                <tr>                 
                                    <td >Amount</td>
                                    <td><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>
                                </tr>

                                 <!-- new dev -->
                                <tr>
                                    <td>
                                        Brand
                                    </td>
                                    <td>
                                       <input class="text_boxes"  name="txt_brand" id="txt_brand" type="text" style="width:120px;" readonly disabled/>
                                    </td>
                                </tr>

                                   
                        </table>
                            
                        <table width="280" cellspacing="2" cellpadding="0" border="0" style="float:left">
                                <tr>                 
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

                                 <!--  new dev -->
                                <tr>
                                    <td>
                                        Origin
                                    </td>
                                    <td><?
                                    echo create_drop_down( "cbo_origin", 132, "select country_name,id from lib_country comp where is_deleted=0  and status_active=1 order by country_name",'id,country_name', 1, '--- Select Country ---', 0,"",1 );            
                                    ?></td>
                                </tr>

                            </table>
                            
                            
                            <table width="190" cellspacing="2" cellpadding="0" border="0">
                            	<tr>
                                    <td width="80">Floor</td>
									<td width="100" id="to_floor_td">
										<? echo create_drop_down( "cbo_floor", 130,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                    <td>Room</td>
                                    <td id="to_room_td">
                                    	<? echo create_drop_down( "txt_room", 130,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>Rack</td>
                                    <td id="to_rack_td">
                                    	<? echo create_drop_down( "txt_rack", 130,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shelf</td>
									<td id="to_shelf_td">
										<? echo create_drop_down( "txt_self", 130,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                      <td>Bin/Box</td>
                                      <td id="to_bin_td">
										<? echo create_drop_down( "txt_binbox", 130,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                 </tr>                               
                            </table>

                            <table width="250" cellspacing="2" cellpadding="0" border="0" style="float:left">
                                <tr>
                                    <td width="130">Model.</td>
                                    <td width="130">
                                        <input class="text_boxes"  name="txt_model" id="txt_model" type="text" style="width:120px;" readonly disabled/>
                                    </td>
                                </tr>	                                
	                        </table>

	                        <table width="250" cellspacing="2" cellpadding="0" border="0" style="float:left">
                                <tr>
                                    <td width="130">Comments</td>
                                    <td colspan="5" width="130">
                                        <input class="text_boxes"  name="txt_referance" id="txt_referance" type="text" style="width:510px; width: 590px; margin-left: 33px;"  />
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
                              
                             <input type="hidden" id="txt_prod_id" name="txt_prod_id" value="" />
                             <input type="hidden" id="update_id" name="update_id" value="" />
                             <input type="hidden" id="current_prod_id" name="current_prod_id"/>
                             <input type="hidden" name="store_update_upto" id="store_update_upto" />
                              <!-- -->
							 <? echo load_submit_buttons( $permission, "fnc_general_item_receive_entry", 0,1,"fnResetForm()",1);?>
                             <input type="button" id="btn_print" value="Print2" class="formbutton" style="width:100px;" onClick="show_print_report(2);" >
                             <input type="button" id="btn_print3" value="Print3" class="formbutton" style="width:100px;" onClick="show_print_report(3);" >
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	$(function(){
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
</html>
