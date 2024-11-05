<?
/************************************************Comments************************************
Purpose			: 	This form will create Dyes And Chemical Receive Entry
Functionality	:	
JS Functions	:
Created by		:	MONZU 
Creation date 	: 	17-08-2013
Updated by 		: 	Kausar	
Update date		: 	09-12-2013 (Creating Report)	   
QC Performed BY	:		
QC Date			:	
Comments		:
**********************************************************************************************/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);

//echo $mid."***".$permission;die;

$_SESSION['page_permission']=$permission;
$mid=$_SESSION['menu_id'];

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,item_cate_id FROM user_passwd where id=$user_id");
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$company_id = $userCredential[0][csf('company_id')];

if($item_cate_id !='') {
	$cre_cat_arr=explode(",",$item_cate_id);
	$selected_category=array( '5', '6', '7', '23' );
	$filteredArr = array_intersect( $cre_cat_arr, $selected_category );
    $item_cate_credential_cond = implode(",",$filteredArr);
}
else
{
	$item_cate_credential_cond="5,6,7,23";
}
//echo $item_cate_id."<br>". $item_cate_credential_cond."<br>";print_r($filteredArr);die;
if ($company_id !='') {
    $company_credential_cond = "and lib_company.id in($company_id)";
}

echo load_html_head_contents("Dyes And Chemical Receive","../../", 1, 1, $unicode,1,1); 


//--------------------------------------------------------------------------------------------------------------------
// last yarn receive exchange rate, currency,store name
/*$sql = sql_select("select store_id,exchange_rate,currency_id,max(id) from inv_receive_master where item_category in(5,6,7) group by store_id,exchange_rate,currency_id");
$storeName=$exchangeRate=$currencyID=0;
foreach($sql as $row)
{
	$storeName=$row[csf("store_id")];
	$exchangeRate=$row[csf("exchange_rate")];
	$currencyID=$row[csf("currency_id")];
}*/

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

<?
if($_SESSION['logic_erp']['data_arr'][4] !="")
{
	$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][4] );
	echo "var field_level_data= ". $data_arr . ";\n";
}
if($_SESSION['logic_erp']['mandatory_field'][4]!="")
{
	$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][4] );
	echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
}
?>

function rcv_basis_reset(str)
{
	if(str==2)
	{
		document.getElementById('cbo_receive_basis').value=0;
		reset_form('chemicaldyesreceive_1','list_container_yarn*list_product_container','','','','cbo_company_id*variable_lot');
		fn_independent(0);
		
	}
	var company_id=$('#cbo_company_id').val();
	var lots_variable=return_global_ajax_value( company_id, 'populate_data_lib_data', '', 'requires/chemical_dyes_receive_controller');
	var varible_string_ref=lots_variable.split("**");
	$('#variable_lot').val(varible_string_ref[5]);
	if(lots_variable==1)
	{
		$('#lot_caption').css('color', 'blue');
	}
	else
	{
		$('#lot_caption').css('color', 'black');
	}
	
	if(str==2)
	{
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
	}
	
}


	
	
// popup for WO/PI----------------------	
function openmypage(page_link,title)
{
	if( form_validation('cbo_company_id*cbo_receive_basis','Company Name*Receive Basis')==false )
	{
		return;
	}
	
 		var company = $("#cbo_company_id").val();
		var receive_basis = $("#cbo_receive_basis").val();
		 
		page_link='requires/chemical_dyes_receive_controller.php?action=wopi_popup&company='+company+'&receive_basis='+receive_basis;
 		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px, height=400px, center=1, resize=0, scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // wo/pi table id
			var wopiNumber=this.contentDoc.getElementById("hidden_wopi_number").value; // wo/pi number
			var hidden_is_non_ord_sample=this.contentDoc.getElementById("hidden_is_non_ord_sample").value; // wo/pi number
			if (rowID!="")
			{
 				freeze_window(5);
				$("#txt_wo_pi").val(wopiNumber);
				$("#txt_wo_pi_id").val(rowID);
				$("#booking_without_order").val(hidden_is_non_ord_sample);
				get_php_form_data(receive_basis+"**"+rowID+"**"+hidden_is_non_ord_sample+"**"+wopiNumber, "populate_data_from_wopi_popup", "requires/chemical_dyes_receive_controller" );
				show_list_view(receive_basis+"**"+rowID+"**"+hidden_is_non_ord_sample+"**"+wopiNumber,'show_product_listview','list_product_container','requires/chemical_dyes_receive_controller','');
				check_exchange_rate();
				release_freezing();	 
 			}
		}		
}

// popup for WO/PI----------------------	
function openmypage_gate()
{
	if( form_validation('cbo_company_id*cbo_receive_basis','Company Name*Receive Basis')==false )
	{
		return;
	}
	
    var company = $("#cbo_company_id").val();
    var receive_basis = $("#cbo_receive_basis").val();
     title="";
    page_link='requires/chemical_dyes_receive_controller.php?action=gate_search&company='+company+'&receive_basis='+receive_basis;
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px, height=400px, center=1, resize=0, scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        
        var wopiNumber=this.contentDoc.getElementById("hidden_system_id").value; // wo/pi number
            freeze_window(5);
            $("#txt_gate_entry").val(wopiNumber);
            release_freezing();	 
        }
				
}

// enable disable field for independent
function fn_independent(val)
{
	$('#txt_wo_pi').val('');
	reset_form('chemicaldyesreceive_1','list_container_yarn*list_product_container','','','','cbo_company_id*cbo_receive_basis*variable_lot*txt_receive_date');
	$("#txt_rack").attr("disabled",true);
    $("#txt_shelf").attr("disabled",true);
    $("#cbo_bin").attr("disabled",true);

	if(val==0)
	{
		$("#txt_lc_no").attr("disabled",false);
		$('#txt_lc_no').removeAttr('placeholder','Display');
		$('#cbo_supplier').val(0);
		$("#cbo_supplier").attr("disabled",false);
		$('#cbo_supplier option:eq(0)').text('-Select-')
		$('#cbo_currency').val(0);
		$("#cbo_currency").attr("disabled",false);
		$('#cbo_currency option:eq(0)').text('-Select-')
		$('#cbo_source').val(0);
		$("#cbo_source").attr("disabled",false);
		$('#cbo_source option:eq(0)').text('-Select-')
		$("#txt_wo_pi").attr("disabled",false);
		$('#txt_wo_pi').removeAttr('placeholder','No Need');
		$('#txt_wo_pi').attr('placeholder','Double Click');
		$('#cbo_receive_purpose').val(0);
		$('#cbo_loan_party').val(0);
		$('#cbo_receive_purpose').attr('disabled',false);
		$('#cbo_loan_party').attr('disabled',false);
		$('#cbo_receive_purpose option:eq(0)').text('-Select-')
		$('#cbo_loan_party option:eq(0)').text('-Select-')
		
		$('#cbo_item_category_id').val(0);
		$("#cbo_item_category_id").attr("disabled",false);
		$('#cbo_item_category_id option:eq(0)').text('-Select-')
		
		$('#cbo_item_group_id').val(0);
		$("#cbo_item_group_id").attr("disabled",false);
		$('#cbo_item_group_id option:eq(0)').text('-Select-')
		
		$('#txt_description').val("");
		$("#txt_description").attr("disabled",false);
		$("#txt_description").removeAttr("onClick","openmypage_ItemDescription()");
		$('#txt_description').removeAttr('placeholder','Display');
		
		$('#txt_rate').val(0);
		$("#txt_rate").attr("disabled",false);
		$('#txt_rate').removeAttr('placeholder','Display');
		
		
	}
	if(val==1)
	{
		$("#txt_lc_no").attr("disabled",true);
		$('#txt_lc_no').attr('placeholder','Display');
		$('#cbo_supplier').val(0);
		$("#cbo_supplier").attr("disabled",true);
		$('#cbo_supplier option:eq(0)').text('Display')
		$('#cbo_currency').val(0);
		$("#cbo_currency").attr("disabled",true);
		$('#cbo_currency option:eq(0)').text('Display')
		$('#cbo_source').val(0);
		$("#cbo_source").attr("disabled",true);
		$('#cbo_source option:eq(0)').text('Display')
		$("#txt_wo_pi").attr("disabled",false);
		$('#txt_wo_pi').removeAttr('placeholder','No Need');
		$('#txt_wo_pi').attr('placeholder','Double Click');
		$('#cbo_receive_purpose').val(0);
		$('#cbo_loan_party').val(0);
		$('#cbo_receive_purpose').attr('disabled','disabled');
		$('#cbo_loan_party').attr('disabled','disabled');
		$('#cbo_receive_purpose option:eq(0)').text('No Need')
		$('#cbo_loan_party option:eq(0)').text('No Need')
		
		$('#cbo_item_category_id').val(0);
		$("#cbo_item_category_id").attr("disabled",true);
		$('#cbo_item_category_id option:eq(0)').text('Display')
		
		$('#cbo_item_group_id').val(0);
		$("#cbo_item_group_id").attr("disabled",true);
		$('#cbo_item_group_id option:eq(0)').text('Display')
		
		$('#txt_description').val("");
		$("#txt_description").attr("disabled",true);
		$("#txt_description").removeAttr("onClick","openmypage_ItemDescription()");
		$('#txt_description').attr('placeholder','Display');
		
		$('#txt_rate').val(0);
		$("#txt_rate").attr("disabled",true);
		$('#txt_rate').attr('placeholder','Display');
	}
	if(val==2)
	{
		$("#txt_lc_no").attr("disabled",true);
		$('#txt_lc_no').attr('placeholder','No Need');
		$('#cbo_supplier').val(0);
		$("#cbo_supplier").attr("disabled",true);
		$('#cbo_supplier option:eq(0)').text('Display')
		
		$('#cbo_currency').val(0);
		$("#cbo_currency").attr("disabled",true);
		$('#cbo_currency option:eq(0)').text('Display')
		
		$('#cbo_source').val(0);
		$("#cbo_source").attr("disabled",true);
		$('#cbo_source option:eq(0)').text('Display')
		$("#txt_wo_pi").attr("disabled",false);
		$('#txt_wo_pi').removeAttr('placeholder','No Need');
		$('#txt_wo_pi').attr('placeholder','Double Click');
		$('#cbo_receive_purpose').val(0);
		$('#cbo_loan_party').val(0);
		$('#cbo_receive_purpose').attr('disabled','disabled');
		$('#cbo_loan_party').attr('disabled','disabled');
		$('#cbo_receive_purpose option:eq(0)').text('No Need')
		$('#cbo_loan_party option:eq(0)').text('No Need')
		$('#cbo_item_category_id').val(0);
		$("#cbo_item_category_id").attr("disabled",true);
		$('#cbo_item_category_id option:eq(0)').text('Display')
		$('#cbo_item_group_id').val(0);
		$("#cbo_item_group_id").attr("disabled",true);
		$('#cbo_item_group_id option:eq(0)').text('Display')
		
		$('#txt_description').val("");
		$("#txt_description").attr("disabled",true);
		$("#txt_description").removeAttr("onClick","openmypage_ItemDescription()");
		$('#txt_description').attr('placeholder','Display');
		
		$('#txt_rate').val(0);
		$("#txt_rate").attr("disabled",true);
		$('#txt_rate').attr('placeholder','Display');
	}
	
	if(val==4)
	{
		$("#txt_lc_no").attr("disabled",true);
		$('#txt_lc_no').attr('placeholder','No Need');
		$('#cbo_supplier').val(0);
		$("#cbo_supplier").attr("disabled",false);
		$('#cbo_supplier option:eq(0)').text('-Select-');
		$('#cbo_currency').val(0);
		$("#cbo_currency").attr("disabled",false);
		$('#cbo_currency option:eq(0)').text('-Select-')
		$('#txt_exchange_rate').removeAttr('disabled','disabled');
		$('#cbo_source').val(0);
		$("#cbo_source").attr("disabled",false);
		$('#cbo_source option:eq(0)').text('-Select-')
		$("#txt_wo_pi").attr("disabled",true);
		$('#txt_wo_pi').attr('placeholder','No Need');
		$('#cbo_receive_purpose').val(0);
		$('#cbo_loan_party').val(0);
		$('#cbo_receive_purpose').removeAttr('disabled','disabled'); 
		$('#cbo_loan_party').removeAttr('disabled','disabled');
		$('#cbo_receive_purpose option:eq(0)').text('-Select Purpose-')
		$('#cbo_loan_party option:eq(0)').text('-Select Loan Party-')
		$('#cbo_item_category_id').val(0);
		$("#cbo_item_category_id").attr("disabled",false);
		$('#cbo_item_category_id option:eq(0)').text('-Select-')
		$('#cbo_item_group_id').val(0);
		$("#cbo_item_group_id").attr("disabled",false);
		$('#cbo_item_group_id option:eq(0)').text('-Select-')
		
		$('#txt_description').val("");
		$("#txt_description").attr("disabled",false);
	    $('#txt_description').attr('placeholder','Click');
		$("#txt_description").attr("onClick","openmypage_ItemDescription()");
		$('#txt_rate').val(0);
		$("#txt_rate").attr("disabled",false);
		$('#txt_rate').removeAttr('placeholder','Display');
		
	}
	if(val==6)
	{
		$("#txt_lc_no").removeAttr("disabled",true);
		$('#txt_lc_no').attr('placeholder','Click');
		$('#cbo_supplier').val(0);
		$("#cbo_supplier").attr("disabled",false);
		$('#cbo_supplier option:eq(0)').text('-Select-');
		$('#cbo_currency').val(0);
		$("#cbo_currency").attr("disabled",false);
		$('#cbo_currency option:eq(0)').text('-Select-')
		$('#txt_exchange_rate').removeAttr('disabled','disabled');
		$('#cbo_source').val(0);
		$("#cbo_source").attr("disabled",false);
		$('#cbo_source option:eq(0)').text('-Select-')
		$("#txt_wo_pi").attr("disabled",true);
		$('#txt_wo_pi').attr('placeholder','No Need');
		$('#cbo_receive_purpose').val(0);
		$('#cbo_loan_party').val(0);
		$('#cbo_receive_purpose').attr('disabled','disabled');
		$('#cbo_loan_party').attr('disabled','disabled');
		$('#cbo_receive_purpose option:eq(0)').text('No Need')
		$('#cbo_loan_party option:eq(0)').text('No Need')
		$('#cbo_item_category_id').val(0);
		$("#cbo_item_category_id").attr("disabled",false);
		$('#cbo_item_category_id option:eq(0)').text('-Select-')
		$('#cbo_item_group_id').val(0);
		$("#cbo_item_group_id").attr("disabled",false);
		$('#cbo_item_group_id option:eq(0)').text('-Select-')
		$('#txt_description').val("");
		$("#txt_description").attr("disabled",false);
		$("#txt_description").attr("onClick","openmypage_ItemDescription()");
		$('#txt_description').attr('placeholder','Click');
		
		$('#txt_rate').val(0);
		$("#txt_rate").attr("disabled",false);
		$('#txt_rate').removeAttr('placeholder','Display');
	}
    var nowDate     = new Date();
	var nowmonth	= nowDate.getMonth()+1;
    var nowDay      = ((nowDate.getDate().toString().length) == 1) ? '0'+(nowDate.getDate()) : (nowDate.getDate());
    var nowMonth    = ((nowmonth.toString().length) == 1) ? '0'+(nowDate.getMonth()+1) : (nowDate.getMonth()+1);
    var nowYear     = nowDate.getFullYear();
	//alert(nowDate.getMonth().toString().length+"="+nowDate.getMonth()+"="+nowMonth+"="+nowDate);
    var formatDate  = nowDay + "-" + nowMonth + "-" + nowYear;
    if($('#txt_receive_date').val().length == 0){
        $('#txt_receive_date').val(formatDate);
    }
}

function set_exchange_rate(currency_id)
{
	if(currency_id==1)
	{
		$('#txt_exchange_rate').val(1);
		$('#txt_exchange_rate').attr('disabled','disabled');
		 fn_calile();
	}
	if(currency_id!=1)
	{
		var response=return_global_ajax_value( currency_id, 'set_exchange_rate', '', 'requires/chemical_dyes_receive_controller');
		$('#txt_exchange_rate').val(response);
		$('#txt_exchange_rate').removeAttr('disabled','disabled')
		 fn_calile();
	}
}


// LC pop up script here-----------------------------------
function popuppage_lc()
{
	
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/chemical_dyes_receive_controller.php?action=lc_popup&company='+company; 
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
	/*if( form_validation('cbo_company_id*txt_exchange_rate*cbo_source*txt_rate','Company Name*Exchange Rate*Source*Rate')==false )
	{
		return;
	}*/
	
	var company=$('#cbo_company_id').val();	
	var source=$('#cbo_source').val();	
	var rate=$('#txt_rate').val();	
	var cbo_item_category_id=$('#cbo_item_category_id').val();
	var cbo_item_group_id=$('#cbo_item_group_id').val();		 
	var responseHtml = return_ajax_request_value(company+'**'+source+'**'+rate+'**'+cbo_item_category_id+'**'+cbo_item_group_id, 'show_ile', 'requires/chemical_dyes_receive_controller');
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
	$("#txt_amount").val(number_format_common(amount,"","",1));
	$("#txt_book_currency").val(number_format_common(bookCurrency,"","",1));
	
}

function fn_check_receive_qnty(){
	var basis 		= $("#cbo_receive_basis").val();
	var quantity 		= $("#txt_receive_qty").val()*1;
	var balance_qnty 	= $("#txt_bla_order_qty").val()*1;
	if(basis==7){
		if(balance_qnty < quantity){
			alert("Receive Quantity can not be more than Balance Quantity ("+balance_qnty+")");
			$("#txt_receive_qty").val("").focus();
			return;		
		}
	}
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

function fnc_chemical_dyes_receive_entry(operation)
{
	if(operation==4 || operation==5)
	{
		if($('#update_id').val()=="")
		{
			alert("Please Save Data First.");
			return;
		}

		var report_title=$( "div.form_caption" ).html();
	
		var action='';
		if(operation==4)
		{
			action='chemical_dyes_receive_print';
		}
		else if(operation==5)
		{
			action='chemical_dyes_receive_print_new';
			var rateAmount = 'no';
            if(confirm('Print with rate and amount')){
                rateAmount = 'yes';
            }else{
                rateAmount = 'no';
            }
		}
		var data= $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val()+'*'+$('#cbo_company_id').val()+'*'+$('#cbo_location').val()+'*'+rateAmount;
		
		window.open("requires/chemical_dyes_receive_controller.php?data=" + data+'&action='+action, true );
		
		/*var report_title=$( "div.form_caption" ).html();txt_mrr_no
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val(), "chemical_dyes_receive_print", "requires/chemical_dyes_receive_controller" ) */
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		/*if(operation==2)
		{
			show_msg('13');
			return;
		}*/
		var variable_lot=$('#variable_lot').val();
		var rcv_purpose=$('#cbo_receive_purpose').val();
		//alert(variable_lot);return;
		
		if(rcv_purpose==5)
		{
			if(variable_lot==1)
			{
				if( form_validation('cbo_company_id*cbo_receive_basis*cbo_loan_party*txt_receive_date*txt_challan_no*cbo_location*cbo_store_name*cbo_currency*cbo_source*cbo_item_category_id*cbo_item_group_id*txt_description*txt_receive_qty*txt_rate*txt_lot','Company Name*Receive Basis*Loan Party*Receive Date*Challan No*Store Name*Currency*Source*Item Category*Item Group*Item Description*Receive Quantity*Rate*Lot')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_id*cbo_receive_basis*cbo_loan_party*txt_receive_date*txt_challan_no*cbo_location*cbo_store_name*cbo_currency*cbo_source*cbo_item_category_id*cbo_item_group_id*txt_description*txt_receive_qty*txt_rate','Company Name*Receive Basis*Loan Party*Receive Date*Challan No*Store Name*Currency*Source*Item Category*Item Group*Item Description*Receive Quantity*Rate')==false )
				{
					return;
				}
			}
			
		}
		else
		{
			if(variable_lot==1)
			{
				//alert(9);
				if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_location*cbo_store_name*cbo_supplier*cbo_currency*cbo_source*cbo_item_category_id*cbo_item_group_id*txt_description*txt_receive_qty*txt_rate*txt_lot','Company Name*Receive Basis*Receive Date*Challan No*Location*Store Name*Supplier*Currency*Source*Item Category*Item Group*Item Description*Receive Quantity*Rate*Lot')==false )
				{
					return;
				}
			}
			else
			{
				if( form_validation('cbo_company_id*cbo_receive_basis*txt_receive_date*txt_challan_no*cbo_location*cbo_store_name*cbo_supplier*cbo_currency*cbo_source*cbo_item_category_id*cbo_item_group_id*txt_description*txt_receive_qty*txt_rate','Company Name*Receive Basis*Receive Date*Challan No*Store Name*Supplier*Currency*Source*Item Category*Item Group*Item Description*Receive Quantity*Rate')==false )
				{
					return;
				}
			}
		}
		
		
		if( $("#txt_rate").val()=="" || $("#txt_rate").val()==0 )
		{
			$("#txt_rate").val('');
			form_validation('txt_rate','Rate');
			return;
		}
		
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Receive Date Can not Be Greater Than Current Date");
			return;
		}
		
		
			
		if($("#txt_exchange_rate").val()<=0)
		{
			alert("Exchange Rate Should Be More Then Zero");
			$("#txt_exchange_rate").focus();
			return;
		}

		if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][4]); ?>') 
		{
			if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][4]); ?>','<? echo implode('*',$_SESSION['logic_erp']['mandatory_message'][4]); ?>')==false) {return;}
		}

		var dataString = "txt_mrr_no*update_id*cbo_company_id*cbo_receive_basis*txt_wo_pi*txt_wo_pi_id*cbo_receive_purpose*cbo_loan_party*txt_gate_entry*txt_receive_date*txt_challan_no*txt_challan_date*cbo_location*cbo_store_name*cbo_supplier*txt_lc_no*hidden_lc_id*cbo_currency*txt_exchange_rate*cbo_source*cbo_item_category_id*cbo_item_group_id*txt_description*txt_product_id*txt_lot*cbo_uom*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_expire_date*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*update_dtls_id*txt_sup_ref*cbo_pay_mode*txt_referance*variable_lot*txt_manufac_date*cbo_zero_discharge*txt_boe_mushak_challan_no*txt_boe_mushak_challan_date*txt_gate_entry_date";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/chemical_dyes_receive_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_chemical_dyes_receive_entry_reponse;
	}
}

function fnc_chemical_dyes_receive_entry_reponse()
{	
	if(http.readyState == 4) 
	{
		//freeze_window(operation);
		//alert(http.responseText);
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
		else if(reponse[0]==50)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
		{
			show_msg(trim(reponse[0]));
			document.getElementById('update_id').value = reponse[1];
			document.getElementById('txt_mrr_no').value = reponse[2];
			$('#cbo_company_id').attr('disabled','disabled');
			disable_enable_fields( 'cbo_company_id*cbo_receive_basis*txt_wo_pi*cbo_currency', 1, "", "" );
			$('#txt_lot').attr('disabled',false);
			//show_list_view('".$row[csf("recv_number")]."**".$row[csf("id")]."','show_dtls_list_view','list_container_yarn','requires/chemical_dyes_receive_controller','');\
			show_list_view(reponse[2]+'**'+reponse[1],'show_dtls_list_view','list_container_yarn','requires/chemical_dyes_receive_controller','');
			reset_form('','','cbo_item_category_id*cbo_item_group_id*txt_description*txt_product_id*txt_lot*cbo_uom*txt_receive_qty*txt_rate*txt_ile*txt_amount*txt_book_currency*txt_bla_order_qty*txt_expire_date*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*update_dtls_id*cbo_zero_discharge','','','');
			$('#cbo_store_name').attr('disabled',true);
			set_button_status(reponse[3], permission, 'fnc_chemical_dyes_receive_entry',1,1);	
			release_freezing();	
		}
		else
		{
			release_freezing();	
		}
	}
}


function open_mrrpopup(str_ref)
{
	//alert(str_ref);
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/chemical_dyes_receive_controller.php?action=mrr_popup&company='+company+'&str_ref='+str_ref; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1030px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_"); // mrr number
		//alert(mrrNumber[0]+"="+mrrNumber[1]+"="+mrrNumber[2]+"="+mrrNumber[3]+"="+mrrNumber[4]);return;
 		$("#txt_mrr_no").val(mrrNumber[0]);
		
		// master part call here
		get_php_form_data(mrrNumber[0], "populate_data_from_data", "requires/chemical_dyes_receive_controller");
		//echo show_list_view(".$row[csf("receive_basis")]."+'**'+".$row[csf("pi_wo_batch_no")]."+'**'+".$row[csf("booking_without_order")]."+'**'+'".$row[csf("booking_no")]."','show_product_listview','list_product_container','requires/chemical_dyes_receive_controller','');
		if(mrrNumber[1]==1 || mrrNumber[1]==2 || mrrNumber[1]==7)
		{
			show_list_view(mrrNumber[1]+"**"+mrrNumber[2]+"**"+mrrNumber[3]+"**"+mrrNumber[2],'show_product_listview','list_product_container','requires/chemical_dyes_receive_controller','');
		}
		//set_button_status(0, permission, 'fnc_chemical_dyes_receive_entry',1,1);
		rcv_basis_reset(1);
		$("#tbl_master").find('input,select').attr("disabled", true);	
		
		$("#btn_fileadd").prop("disabled", false);// new add 21-12-2020
		
		disable_enable_fields( 'txt_mrr_no', 0, "", "" );
		//disable_enable_fields( 'cbo_company_id*cbo_receive_basis*txt_wo_pi*cbo_currency', 1, "", "" );	
 	}
}


//form reset/refresh function here
function fnResetForm()
{
	fn_independent(0)
	set_button_status(0, permission, 'fnResetForm',1);
	reset_form('chemicaldyesreceive_1','list_container_yarn*list_product_container','','','','');
	disable_enable_fields( 'cbo_company_id*cbo_receive_basis*txt_gate_entry*txt_receive_date*txt_challan_no*cbo_location*cbo_store_name*txt_exchange_rate*cbo_uom', 0, "", "" );
}

	function openmypage_ItemDescription()
	{
		
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var cbo_company_id=$('#cbo_company_id').val();
		var cbo_item_category_id=$('#cbo_item_category_id').val();
		var cbo_item_group_id=$('#cbo_item_group_id').val();
		var title = 'Item Description Info';	
		var page_link = 'requires/chemical_dyes_receive_controller.php?action=ItemDescription_popup&cbo_company_id='+cbo_company_id+'&cbo_item_category_id='+cbo_item_category_id+'&cbo_item_group_id='+cbo_item_group_id;
		var empty="";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var cbo_receive_basis=$('#cbo_receive_basis').val();
			cbo_receive_basis
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("hidden_prod_id").value;	 //Access form field with id="emailfield"
			get_php_form_data(cbo_receive_basis+"**"+empty+"**"+cbo_company_id+"**"+empty+"**"+theemail,"wo_pi_product_form_input","requires/chemical_dyes_receive_controller")
		}
	}
	
	function company_anable()
	  {
		$("#cbo_company_id").attr("disabled",false);  
		  
	  }
	 
	 
	function show_print_report()
	{
		if($('#update_id').val()=="")
		{
			alert("Please Save Data First.");
			return;
		}
		else
		{
            var rateAmount = 'no';
            if(confirm('Print with rate and amount')){
                rateAmount = 'yes';
            }else{
                rateAmount = 'no';
            }
			var report_title=$( "div.form_caption" ).html();
			var data= $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val()+'*'+$('#cbo_company_id').val()+'*'+$('#cbo_location').val()+'*'+rateAmount;
			var action='chemical_dyes_receive_print_new';
			window.open("requires/chemical_dyes_receive_controller.php?data=" + data+'&action='+action, true );
			
			/*var report_title=$( "div.form_caption" ).html();txt_mrr_no
			print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_receive_basis').val(), "chemical_dyes_receive_print", "requires/chemical_dyes_receive_controller" ) */
			return;
		}
	}
	
	function check_exchange_rate()
	{
		var cbo_currercy=$('#cbo_currency').val();
		var receive_date = $('#txt_receive_date').val();
		var cbo_company_id = $('#cbo_company_id').val();
		var response=return_global_ajax_value( cbo_currercy+"**"+receive_date+"**"+cbo_company_id, 'check_conversion_rate', '', 'requires/chemical_dyes_receive_controller');
		//alert(cbo_currercy+"="+receive_date+"="+cbo_company_id+"="+response);
		var response=response.split("_");
		$('#txt_exchange_rate').val("");
		$('#txt_exchange_rate').val(response[1]);
		$('#txt_exchange_rate').attr('disabled','disabled');
		fn_calile();
	}

	
</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="chemicaldyesreceive_1" id="chemicaldyesreceive_1" autocomplete="off" data-entry_form="4"> 
    <div style="width:77%;">       
    <table width="80%" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:1000px; float:left;">
                <legend>Dyes And Chemical Receive</legend>
                <br />
                 	<fieldset style="width:950px;">                                       
                        <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="6" align="center">&nbsp;<b>MRR Number</b>
                                	<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup(<? echo $mid; ?>)" readonly /> <input type="hidden" name="update_id" id="update_id" />
                                </td>
                           </tr>
                           <tr>
                                <td  width="130" class="must_entry_caption">Company Name </td>
								<td width="170">
									<?
										//load_drop_down( 'requires/chemical_dyes_receive_controller',this.value +'**4', 'load_drop_down_rate','rate_td');
										echo create_drop_down( "cbo_company_id", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 $company_credential_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_basis_reset(2);load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_location', 'location_td');load_drop_down( 'requires/chemical_dyes_receive_controller',this.value +'**4', 'load_drop_down_basis','basis_id');load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_loan_party', 'loan_party_td');load_room_rack_self_bin('requires/chemical_dyes_receive_controller*5_6_7_23', 'store','store_td', this.value);
										get_php_form_data( this.value, 'company_wise_report_button_setting','requires/chemical_dyes_receive_controller' ); " );
										//load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_store', 'store_td' ); get_php_form_data(this.value, 'populate_data_lib_data', 'requires/chemical_dyes_receive_controller');
									?>
									<input type="hidden" id="variable_lot" name="variable_lot" />
                                    <input type="hidden" id="is_rate_optional" name="is_rate_optional">
                                </td>
								<td width="130" class="must_entry_caption"> Receive Basis </td>
								<td width="170" id="basis_id">
									<? 
									//load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_supplier', 'supplier'); load_drop_down('requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_currency', 'currency'); load_drop_down('requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_source', 'sources'); load_drop_down('requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_lc', 'lc_no');
									echo create_drop_down( "cbo_receive_basis", 170, $receive_basis_arr,"", 1, "- Select Receive Basis -", $selected, "fn_independent(this.value)","","1,2,4,6,7" );
									?>
								</td>
								<td width="130" >WO/PI/Req</td>
								<td>
									<input class="text_boxes"  type="text" name="txt_wo_pi" id="txt_wo_pi" onDblClick="openmypage('xx','Order Search')"  placeholder="Double Click" style="width:160px;"  readonly  /> 
									<input type="hidden" id="txt_wo_pi_id" name="txt_wo_pi_id" value="" />
								</td>                                   
                            </tr>
                            
                            <tr>
                                <td>Receive Purpose </td>
                                <td>
									<? 
									echo create_drop_down( "cbo_receive_purpose", 170, $general_issue_purpose,"", 1, "-- Select Purpose --", 0, "", "","5");
									?>
                                </td>
								<td>  Loan Party </td>
								<td id="loan_party_td">
									<? 
									//load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_supplier', 'supplier'); load_drop_down('requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_currency', 'currency'); load_drop_down('requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_source', 'sources'); load_drop_down('requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_lc', 'lc_no');
									echo create_drop_down( "cbo_loan_party", 170, $blank_array,"", 1, "- Select Loan Party -", $selected, "","","" );
									?>
								</td>
								<td>Gate Entry No </td>
								<td>
									<input class="text_boxes"  type="text" name="txt_gate_entry" id="txt_gate_entry" onDblClick="openmypage_gate()"  placeholder="Double Click" style="width:160px;"    /> 
									
								</td>                                   
                            </tr>
                            <tr>
                            	<td class="must_entry_caption">Receive Date </td>
								<td>
									<input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:160px;" placeholder="Select Date" onChange="check_exchange_rate();" value="<?=date('d-m-Y')?>" />
								</td>
						
								<td class="must_entry_caption" > Challan No </td>
								<td>
									<input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:160px" >
								</td>
								<td> Challan Date </td>
								<td>
									<input type="text" name="txt_challan_date" id="txt_challan_date" class="datepicker" style="width:160px;" placeholder="Select Date"/>
								</td>                                 
                            </tr>
                            <tr>
								<td class="must_entry_caption"> Location </td>
								<td id="location_td">
									<? 
									echo create_drop_down( "cbo_location", 170, $blank_array,"", 1, "-- Select Location --", 0, "" );
									?>
								</td>
								<td class="must_entry_caption">Store Name</td>
								<td id="store_td">
									<? 
									echo create_drop_down( "cbo_store_name", 170, "select lib_store_location.id,lib_store_location.store_name,lib_store_location.company_id,lib_store_location_category.category_type from lib_store_location,lib_store_location_category where lib_store_location.id=lib_store_location_category.store_location_id and lib_store_location.status_active=1 and lib_store_location.is_deleted=0  and lib_store_location_category.category_type in(5,6,7,23) group by lib_store_location.id order by lib_store_location.store_name","id,store_name", 1, "-- Select Store --", $storeName, "" );
									?>
								</td>						
								<td class="must_entry_caption"> Supplier </td>
								<td id="supplier"> 
								<?
									echo create_drop_down( "cbo_supplier", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=3 and a.status_active=1 group by a.id, a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "","" );
								?>
								</td>   
                            </tr>
                             <tr>
								 <td > L/C No </td>
								 <td id="lc_no">
									 <input class="text_boxes"  type="text" name="txt_lc_no" id="txt_lc_no" style="width:160px;" placeholder="Display" onDblClick="popuppage_lc()" readonly   />  
									<input type="hidden" name="hidden_lc_id" id="hidden_lc_id" />
								</td>
								<td width="130" class="must_entry_caption">Currency</td>
								<td width="170" id="currency"> 
								<? //set_exchange_rate(this.value)
									echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select Currency --", $selected, "check_exchange_rate();","" );
									// echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", $selected, "","" );
								?>
								</td>						
								<td>Exchange Rate</td>
								<td>
									<input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:160px" onKeyUp="fn_calile()" readonly />	
								</td>
                            </tr>
                            <tr>
								<td class="must_entry_caption">Source</td>
								<td id="sources">  
								<?
									echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", $selected, "","" );
								?>
								</td>
                               <td >Supplier Ref.</td>
                               <td ><input type="text" id="txt_sup_ref" name="txt_sup_ref" class="text_boxes" style="width:157px;" ></td>
                                <td >Pay Mode</td>
                                <td >
                                <?
                                    echo create_drop_down( "cbo_pay_mode", 170, $pay_mode,"", 1, "-- Select --", 0, "",0 );
                                ?>	
                                </td>
                            </tr>
							<tr>
								<td>BOE/Mushak Challan No</td>                                              
								<td> 
									<input type="text" name="txt_boe_mushak_challan_no" id="txt_boe_mushak_challan_no" class="text_boxes" style="width:160px">
								</td>
								<td>BOE/Mushak Challan Date</td>                                              
								<td> 
									<input type="text" name="txt_boe_mushak_challan_date" id="txt_boe_mushak_challan_date" class="datepicker" style="width:160px" placeholder="Select Date">
								</td>
                                <td>Gate Entry Date</td>
                                <td >
									<input type="text" name="txt_gate_entry_date" id="txt_gate_entry_date" class="datepicker" style="width:160px;" placeholder="Select Date"/>
                                </td>
							</tr>
							<tr>
								<td>File</td>
                                <td> <input type="button" class="image_uploader" style="width:160px" value="CLICK TO ADD FILE" id="btn_fileadd" onClick="file_uploader ( '../../', document.getElementById('txt_mrr_no').value,'', 'dyes_and_chemical_receive', 2 ,1)"> </td>
								<td ></td><td></td>
                                <td></td><td></td>
							</tr>
                        </table>
                    </fieldset>
                    <br />
                    <table cellpadding="0" cellspacing="1" width="96%" id="tbl_child">
                    <tr>
                    <td width="49%" valign="top">
                    	<fieldset style="width:950px;">  
                        <legend>New Receive Item</legend>                                     
                            <table width="220" cellspacing="2" cellpadding="0" border="0" style="float:left">
                            	
                                <tr>    
                                    <td class="must_entry_caption">Item Categ</td>
                                    <td>         
                                        <?php 
                                        	//echo $item_cate_credential_cond;
                                            echo create_drop_down( "cbo_item_category_id", 130,$item_category,"", 1, "-- Select --", $selected, "load_drop_down( 'requires/chemical_dyes_receive_controller', this.value, 'load_drop_down_item_group', 'item_group_td' )","","$item_cate_credential_cond","","","");
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Item Group</td>
                                    <td id="item_group_td" >
                                        <?php 
										echo create_drop_down( "cbo_item_group_id", 130,"select id,item_name  from lib_item_group where status_active=1","id,item_name", 1, "-- Select --", "", "","","","","","");
                                        ?>
                                    </td>
                                </tr>
                                
                                <tr>   
                                        <td class="must_entry_caption">Item Desc.</td>
                                        <td>
                                            <input type="text" name="txt_description" id="txt_description" class="text_boxes" style="width:120px;"  readonly  />
                                            <input type="hidden" name="txt_product_id" id="txt_product_id" class="text_boxes" style="width:120px;"   />
                                        </td>
                                </tr> 
                                <tr>    
                                        <td width="110" id="lot_caption">Lot</td>
                                    	<td width="130">
                                        <input type="text" name="txt_lot" id="txt_lot" class="text_boxes" style="width:120px;"/>
                                        </td> 
                                </tr>
                                <tr>    
                                        <td id="ile_td">ILE%</td>   
                                        <td >
                                        	<input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:120px;" placeholder="Display" readonly />
                                        </td>
                                </tr>   
                            </table>
                            
                            
                            
                            <table width="240" cellspacing="2" cellpadding="0" border="0" style="float:left">
                                <tr>                 
                                    <td width="140" >UOM</td>
                                    <td width="140">
                                    	<?
                                    		echo create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 1, "Display", "", "",1 );
										?>
                                    </td>
                                </tr>
                                <tr>    
                                        <td class="must_entry_caption">Recv. Qnty.</td>   
                                        <td >
                                        
                                            <input name="txt_receive_qty" id="txt_receive_qty"  class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()" onKeyUp="fn_check_receive_qnty()" />
                                        </td> 
                                </tr>
                                <tr id="rate_td">    
                                        <td class="must_entry_caption">Rate</td>   
                                        <td>
                                        	<input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()" value="0" />
                                        </td>
                                </tr>
                                <tr>   
                                        <td>Manufacture Date</td>   
                                        <td >
                                        	<input class="datepicker"  name="txt_manufac_date" id="txt_manufac_date" type="text" style="width:120px;"  />
                                        </td>
                                </tr>
                                <tr>   
                                        <td>Zero Discharge</td>   
                                        <td>
										<?
                                        echo create_drop_down( "cbo_zero_discharge", 130, $compliance_arr,"", 1, "Select", "", "",0 );
										?>
                                        </td>
                                </tr>
                            </table>
                            
                            <table width="280" cellspacing="2" cellpadding="0" border="0" style="float:left">
                               
                                
                                <tr id="amount_td"> 
                                    <td >Amount</td>
                                    <td><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>
                                </tr>
                                <tr id="book_currency_td"> 
                                    <td >Book Currency.</td>
                                    <td>
                                      	<input type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:120px;" readonly disabled />
                                    </td>
                                </tr>
                                <tr> 
                                      <td >Balance PI/ WO </td>
                                      <td><input class="text_boxes_numeric"  name="txt_bla_order_qty" id="txt_bla_order_qty" type="text" style="width:120px;" readonly /></td>
                                </tr>
                                <tr>                 
                                    <td width="100" >Expire Date</td>
                                    <td width="100"><input class="datepicker"  name="txt_expire_date" id="txt_expire_date" type="text" style="width:120px;"  /></td>
                                </tr>
                            </table>
                            <table width="200" cellspacing="2" cellpadding="0" border="0">
                            	<tr> 
                                    <td >Floor</td>
                                   <td id="floor_td">
										<? echo create_drop_down( "cbo_floor", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                    <td >Room</td>
                                   <td id="room_td">
										<? echo create_drop_down( "cbo_room", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                   <td >Rack</td>
                                   <td id="rack_td">
										<? echo create_drop_down( "txt_rack", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                     <td >Self</td>
                                     <td id="shelf_td">
										<? echo create_drop_down( "txt_shelf", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                      <td >Bin/Box</td>
                                      <td id="bin_td">
										<? echo create_drop_down( "cbo_bin", 140,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                 </tr> 
								
                            </table>
                    </fieldset>
                    </td>
                    </tr>
                    <tr>
                    	<td style="padding-left:30px;">Comments: <input class="text_boxes"  name="txt_referance" id="txt_referance" type="text" style="width:810px;"  /></td>
                    </tr>
					<tr>
					<td colspan="6" align="center" id="posted_account_td" style="max-width:100px; color:red; font-size:20px;">
					</td>												
				</tr>
                </table>                
               	<table cellpadding="0" cellspacing="1" width="100%">
                	<tr> 
                       <td colspan="6" align="center"></td>				
                	</tr>
                	<tr>
                        <td align="center" colspan="6" valign="middle" class="button_container">
                        	<div id="audited" style="float:left; font-size:24px; color:#FF0000;"></div>
                             <!-- details table id for update -->
                             <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                             
							 <?
							 echo load_submit_buttons( $permission, "fnc_chemical_dyes_receive_entry", 0,0,"fnResetForm()",1);
							 ?>

							 <input type="button" name="Print1" id="print1" value="Print" class="formbutton" style="width:100px;" onClick="fnc_chemical_dyes_receive_entry(4);" >

							 <input type="button" name="Print2" id="print2" value="Print2" class="formbutton" style="width:100px;" onClick="fnc_chemical_dyes_receive_entry(5);" >

                             <!-- <input type="button" id="btn_print" value="Print2" class="formbutton" style="width:100px;" onClick="show_print_report();" > -->
                        </td>
                   </tr> 
                </table>                 
              	</fieldset>
              	<fieldset>
    			<div style="width:990px;" id="list_container_yarn"></div>
    		  	</fieldset>
           </td>
         </tr>
    </table>
    </div>
    <div id="list_product_container" style="max-height:500px; width:23%; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>  
	</form>
</div>    
</body>
<script>
	$(document).ready(function() {
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
