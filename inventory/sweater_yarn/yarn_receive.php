<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Sweater Yarn Receive Entry
				
Functionality	:	
JS Functions	:
Created by		:	Jahid 
Creation date 	: 	24-11-2018
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

$independent_control_arr = return_library_array( "select company_name, independent_controll from variable_settings_inventory where variable_list=20 and menu_page_id=1 and status_active=1 and is_deleted=0",'company_name','independent_controll');

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sweater Yarn Receive Info","../../", 1, 1, $unicode,1,1); 

?>	

<script>
<?
$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][1]) ;
	echo "var field_level_data= ". $data_arr . ";\n";
?>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

// autocomplete brand script-------
//var str_brand = [<?// echo substr(return_library_autocomplete( "select distinct(brand_name) from lib_brand", "brand_name"  ), 0, -1); ?>];

var str_brand = [<? echo substr(return_library_autocomplete( "select distinct(a.brand_name) from lib_brand a, product_details_master b where a.id=b.brand and b.item_category_id=1", "brand_name"  ), 0, -1); ?>];
$(function() {
				var brand_name = str_brand;
				$("#txt_brand").autocomplete({
				source: brand_name 
		});
});


function rcv_basis_reset()
{
	document.getElementById('cbo_receive_basis').value=0;
} 
	
	
// popup for WO/PI----------------------	
function openmypage(page_link,title)
{
	if( form_validation('cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*txt_challan_no*cbo_store_name','Company Name*Receive Basis*Receive Purpose*Receive Date*Challan No*Store')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();
	var receive_basis = $("#cbo_receive_basis").val();
	var receive_purpose = $("#cbo_receive_purpose").val();
	 
	page_link='requires/yarn_receive_controller.php?action=wopi_popup&company='+company+'&receive_basis='+receive_basis+'&receive_purpose='+receive_purpose;
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px, height=400px, center=1, resize=0, scrolling=0','../')
	emailwindow.onclose=function()
	{
		 
		var theform=this.contentDoc.forms[0];
		var rowID=this.contentDoc.getElementById("hidden_tbl_id").value; // wo/pi table id
		var wopiNumber=this.contentDoc.getElementById("hidden_wopi_number").value; // wo/pi number
		var hidden_paymode=this.contentDoc.getElementById("hidden_paymode").value; // wo/pi number
		if (rowID!="")
		{
			freeze_window(5);
			$("#txt_wo_pi").val(wopiNumber);
			$("#txt_wo_pi_id").val(rowID);
			var company_id=$("#cbo_company_id").val();
			if(receive_basis==2 && receive_purpose==2)
			{
				load_drop_down( 'requires/yarn_receive_controller', rowID, 'load_drop_down_color', 'color_td_id' );
			}
			else if (receive_basis==2 && (receive_purpose==7 || receive_purpose==12 || receive_purpose==15 || receive_purpose==38 || receive_purpose==46) ) 
			{
				//load_drop_down( 'requires/yarn_receive_controller', company_id+'_'+receive_basis+'_'+receive_purpose+'_'+wopiNumber+'_'+hidden_paymode, 'load_drop_down_supplier_from_issue', 'supplier' );
				//alert(receive_basis+'_'+receive_purpose+'_'+wopiNumber+'_'+hidden_paymode);
				
				//load_drop_down( 'requires/yarn_receive_controller', receive_basis+'_'+receive_purpose+'_'+wopiNumber+'_'+hidden_paymode,'load_drop_down_company_from_eheck_wo_paymode', 'supplier' );
				load_drop_down( 'requires/yarn_receive_controller', receive_basis+'_'+receive_purpose+'_'+rowID+'_'+hidden_paymode,'load_drop_down_company_from_eheck_wo_paymode', 'supplier' );
				$('#cbo_supplier').attr('disabled','disabled');

			}
			else
			{
				//load_drop_down( 'requires/yarn_receive_controller', '', 'load_drop_down_color', 'color_td_id' );
				load_drop_down( 'requires/yarn_receive_controller', receive_purpose, 'load_drop_down_color2', 'color_td_id' );
			}
			
			get_php_form_data(receive_basis+"**"+rowID+"**"+receive_purpose, "populate_data_from_wopi_popup", "requires/yarn_receive_controller" );
			show_list_view(receive_basis+"**"+rowID+"**"+receive_purpose,'show_product_listview','list_product_container','requires/yarn_receive_controller','');
			
			
			
			if(receive_basis==1 || receive_basis==2)
			{
				$('#btn_color').attr('disabled',true);
			}
			else
			{
				$('#btn_color').attr('disabled',false);
			}
			// <input type="button" name="btn_color" id="btn_color" class="formbuttonplasminus"  style="width:20px" onClick="fn_color_new(this.id)" value="N" />
			exchange_rate($("#cbo_currency").val());
			disable_enable_fields( 'cbo_receive_basis*cbo_receive_purpose', 1, '', '' );
			release_freezing();
			$("#tbl_child").find('input[type="text"],input[type="hidden"],select').val('');	 
			$("#cbo_uom").val(15);
			if(receive_purpose==16)
			{
				$('#cbo_color').val($('#cbo_color option:last').val());
				$('#cbo_color').attr('disabled','disabled');
			}
		}
	}		
}

// enable disable field for independent
function fn_independent(val)
{
	var MRR_Number = $('#txt_mrr_no').val();
	if(val==4 || val==6)
	{	
		if (MRR_Number == '') 
		{
			reset_form('','list_product_container','txt_wo_pi*txt_wo_pi_id*txt_lc_no*hidden_lc_id','txt_exchange_rate,1','','');//cbo_currency,1*    
			$("#cbo_supplier").attr("disabled",false);
			$("#cbo_currency").attr("disabled",false);
			$("#cbo_source").attr("disabled",false);
			$("#txt_wo_pi").attr("disabled",true);
			$("#cbo_yarn_count").attr("disabled",false).val("");
			$("#cbocomposition1").attr("disabled",false).val("");
			$("#cbo_yarn_type").attr("disabled",false).val("");
			$("#cbo_party").attr("disabled",false).val("");
			
		}
		else
		{
			$("#cbo_supplier").attr("disabled",true);
			$("#cbo_currency").attr("disabled",true);
			$("#cbo_source").attr("disabled",true);
			$("#txt_wo_pi").attr("disabled",true);
			$("#cbo_yarn_count").attr("disabled",false).val("");
			$("#cbocomposition1").attr("disabled",false).val("");
			$("#cbo_yarn_type").attr("disabled",false).val("");
			$("#cbo_party").attr("disabled",false).val("");
		}
	}
	else
	{
		if (MRR_Number == '') 
		{
			$("#cbo_supplier").attr("disabled",true);
			$("#cbo_currency").attr("disabled",true);
			$("#cbo_source").attr("disabled",true);
			$("#txt_wo_pi").attr("disabled",false);
			$("#cbo_yarn_count").attr("disabled",true);
			$("#cbocomposition1").attr("disabled",true);
			$("#cbo_yarn_type").attr("disabled",true);
		}
		else
		{
			$("#cbo_supplier").attr("disabled",true);
			$("#cbo_currency").attr("disabled",true);
			$("#cbo_source").attr("disabled",true);
			$("#txt_wo_pi").attr("disabled",true);
			$("#cbo_yarn_count").attr("disabled",true);
			$("#cbocomposition1").attr("disabled",true);
			$("#cbo_yarn_type").attr("disabled",true);			
			$("#txt_exchange_rate").attr("disabled",true);
		}
		//$("#tbl_child").find('input[type="text"],input[type="hidden"],select').attr('disabled',true);	
	}
	
	if (MRR_Number == '') 
	{
		if(val==1)
		{
			$("#cbo_receive_purpose").attr('disabled',false);
			load_drop_down( 'requires/yarn_receive_controller', val, 'load_drop_down_purpose', 'rcv_purpose_td' );
		}
		else if(val==2)
		{
			$("#cbo_receive_purpose").attr('disabled',false);
			load_drop_down( 'requires/yarn_receive_controller', val, 'load_drop_down_purpose', 'rcv_purpose_td' );
		}
		else
		{
			load_drop_down( 'requires/yarn_receive_controller', val, 'load_drop_down_purpose', 'rcv_purpose_td' );
			$("#cbo_receive_purpose").attr('disabled',true);
			//$("#cbo_receive_purpose").val(5).attr('disabled',true);
		}
	}
	
	//$("#txt_exchange_rate").attr("disabled",true);
	var cbo_receive_purpose=$("#cbo_receive_purpose").val();
	rate_cond(cbo_receive_purpose);
	
}


// LC pop up script here-----------------------------------Not Used
function popuppage_lc()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/yarn_receive_controller.php?action=lc_popup&company='+company; 
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
	if( form_validation('cbo_company_id*cbo_source*txt_rate','Company Name*Source*Rate')==false )
	{
		return;
	}
	
	var company=$('#cbo_company_id').val();	
	var source=$('#cbo_source').val();	
	var rate=$('#txt_rate').val();	 
	var responseHtml = return_ajax_request_value(company+'**'+source+'**'+rate, 'show_ile', 'requires/yarn_receive_controller');
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

function fn_comp_new(val)
{	
	
	if(document.getElementById(val).value=='N') // when new(N) button click
	{											
		load_drop_down( 'requires/yarn_receive_controller', 1, 'load_drop_down_composition', 'composition_td' );		 		
	}
	else // When F button click
	{			
		load_drop_down( 'requires/yarn_receive_controller', 2, 'load_drop_down_composition', 'composition_td' );
	}
		
}

function fn_color_new(val)
{
	if( form_validation('cbo_receive_purpose','Receive Purpose')==false )
	{
		return;
	}
	
	var receive_basis = $("#cbo_receive_basis").val();
	var receive_purpose = $("#cbo_receive_purpose").val();
	var cbo_company_id= $("#cbo_company_id").val();
	if(receive_purpose==16)
	{
		return;
	}

	if(receive_basis==2 && receive_purpose==2)
	{
		load_drop_down( 'requires/yarn_receive_controller', '', 'load_drop_down_color', 'color_td_id' );
		return;
	}
	
	if(document.getElementById(val).value=='N') // when new(N) button click
	{
		document.getElementById('color_td_id').innerHTML='<input type="text" name="cbo_color" id="cbo_color" class="text_boxes" style="width:100px" /><input type="button" class="formbuttonplasminus" name="btn_color" id="btn_color" width="15" onClick="fn_color_new(this.id)" value="F" />';
		$('#cbo_color').attr('readonly',false);
		$('#cbo_color').removeAttr('placeholder','Click');
	}
	else // When F button click
	{		
 		load_drop_down( 'requires/yarn_receive_controller', '', 'load_drop_down_color', 'color_td_id' );
 	}
}

function fnc_yarn_receive_entry(operation)
{
	
	if(operation==4)
	{
		 print_report( $('#cbo_company_id').val()+'*'+$('#txt_mrr_no').val(), "yarn_receive_print", "requires/yarn_receive_controller" ) 
		 return;
	}
	else
	{
		if($("#is_posted_account").val()==1)
		{
			alert("Already Posted In Accounting. Save Update Delete Restricted.");
			return;
		}
		if( form_validation('cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*txt_challan_no*cbo_store_name*cbo_supplier*txt_job_no','Company Name*Receive Basis*Receive Purpose*Receive Date*Challan No*Store Name*Supplier*Job No')==false )
		{
			return;
		}	
		
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_receive_date').val(), current_date)==false)
		{
			alert("Receive Date Can not Be Greater Than Current Date");
			return;
		}
		
		if( $("#cbo_receive_purpose").val()==5)
		{
			if(form_validation('cbo_party','Loan Party')==false )
			{
				return;
			}	
		}
		
		if( form_validation('cbo_currency*cbo_source*cbo_yarn_count*txt_brand*cbo_yarn_type*cbo_color*txt_yarn_lot*txt_receive_qty*txt_rate','Currency*Source*Yarn Count*Brand*Yarn Type*Color*Yarn Lot*Receive Quantity*Rate')==false )
		{
			return;
		}	
		
		var perc=$("#percentage1").val()*1+$("#percentage2").val()*1;
		if(perc!=100)
		{
			alert('Percentage Should Be 100');
			return;
		}
		
		if($("#cbo_receive_basis").val() == 1 || $("#cbo_receive_basis").val()==2)
		{
			var rcv_purpose=$("#cbo_receive_purpose").val();
			if(rcv_purpose!=2 && rcv_purpose!=7 && rcv_purpose!=12 && rcv_purpose!=15 && rcv_purpose!=38 && rcv_purpose!=46)
			{
				if($("#txt_bal_order_qty").val() - $("#txt_receive_qty").val() < 0){
					alert("Receive Quantity Can Not Greater Than Balance Quantity");
					return;
				}
			}
		}



		if( ($("#percentage1").val()!="" && $("#cbocomposition1").val()==0) || ($("#percentage1").val()=="" && $("#cbocomposition1").val()!=0) )
		{
			alert('First Composition');
			return;
		}
		else if( ($("#percentage2").val()!="" && $("#cbocomposition2").val()==0) || ($("#percentage2").val()=="" && $("#cbocomposition2").val()!=0) )
		{
			alert('2nd Composition');
			return;
		}
		else if($("#cbocomposition1").val()==$("#cbocomposition2").val())
		{
			alert('2nd Composition');
			return;
		}
		else if( $("#txt_rate").val()=="" || $("#txt_rate").val()==0 )
		{
			$("#txt_rate").val('');
			form_validation('txt_rate','Rate');
			return;
		}
		else if( $("#txt_exchange_rate").val()=="" || $("#txt_exchange_rate").val()==0 )
		{
			$("#txt_exchange_rate").val('');
			form_validation('txt_exchange_rate','Exchange Rate');
			return;
		}else if( $("#txt_amount").val()=="" || $("#txt_amount").val() <= 0)
			{
				form_validation('txt_amount','Amount');
				return;
			}
		else
		{
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

			var dataString = "txt_mrr_no*cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_receive_date*txt_challan_no*cbo_store_name*txt_lc_no*hidden_lc_id*cbo_supplier*cbo_currency*txt_exchange_rate*cbo_source*txt_wo_pi*txt_wo_pi_id*cbo_yarn_count*cbocomposition1*cbocomposition2*percentage1*percentage2*cbo_yarn_type*btn_color*cbo_color*txt_yarn_lot*txt_brand*txt_receive_qty*txt_rate*txt_avg_rate*txt_dyeing_charge*txt_ile*cbo_uom*txt_amount*txt_book_currency*txt_order_qty*txt_no_bag*txt_prod_code*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_floor*txt_prod_id*update_id*txt_cone_per_bag*txt_remarks*txt_weight_per_bag*txt_weight_per_cone*job_no*txt_issue_challan_no*txt_issue_id*allocation_maintained*cbo_party*txt_mst_remarks*cbo_buyer_name*hdn_receive_qty*txt_job_no*txt_style_no*hidd_pay_mode*txt_challan_date*txt_challan_qty";
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
			freeze_window(operation);
			http.open("POST","requires/yarn_receive_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_yarn_receive_entry_reponse;
		}
	}
}

function fnc_yarn_receive_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  	
		//release_freezing(); return;
		var reponse=trim(http.responseText).split('**');
		if(reponse[0]==15) 
		{ 
			 setTimeout('fnc_yarn_receive_entry('+ reponse[2] +')',8000); 
			 return;
		}
 		else if(reponse[0]==30 || reponse[0]==20 || reponse[0]==13)
		{
			show_msg(reponse[0]);
			alert(reponse[1]);
			release_freezing();
			return;
		}
		else if(reponse[0]==10)
		{
			release_freezing();
			return;
		}		
		else if(reponse[0]==0)
		{
			show_msg(reponse[0]);
			$("#txt_mrr_no").val(reponse[1]);
 			//$("#tbl_master :input").attr("disabled", true);	
		}
		else if(reponse[0]==1 || reponse[0]==2)
		{
			show_msg(reponse[0]);			 
 			//$("#tbl_master :input").attr("disabled", true);	
			set_button_status(0, permission, 'fnc_yarn_receive_entry',1,1);
			disable_enable_fields( 'cbo_yarn_count*cbocomposition1*cbo_yarn_type*txt_yarn_lot', 0, '', '' );
		}		
		
		show_list_view(reponse[1],'show_dtls_list_view','list_container_yarn','requires/yarn_receive_controller','');
		set_button_status(0, permission, 'fnc_yarn_receive_entry',1,1);			
		disable_enable_fields( 'txt_mrr_no*cbo_yarn_count*cbo_yarn_type*cbocomposition1*percentage1', 0, "", "" );
		disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_receive_purpose*txt_wo_pi*cbo_currency*txt_receive_date*cbo_supplier*cbo_source', 1, "", "" );
		
		if($("#cbo_receive_basis").val()==1 || $("#cbo_receive_basis").val()==2)
		{
			change_color_tr(0,'');
		}
 		//child form reset here after save data-------------//
		var txt_wo_pi_id = $("#txt_wo_pi_id").val();
		var txt_wo_pi = $("#txt_wo_pi").val();
		var btn_color = $("#btn_color").val();
 		//$("#tbl_child").find('input,select').val('');
		$("#tbl_child").find('input,select:not([name="cbo_color"])').val('');	
		//$("#tbl_master").find('input,select').attr("disabled", true);
		$("#txt_wo_pi_id").val(txt_wo_pi_id);
		$("#txt_wo_pi").val(txt_wo_pi);
		$("#btn_color").val(btn_color);
		$("#cbo_uom").val(15);
		$("#percentage1").val(100);
		
		release_freezing();
	}
}

function control_composition(type)
{
	var cbocompone=(document.getElementById('cbocomposition1').value);
	var cbocomptwo=(document.getElementById('cbocomposition2').value);
	var percentone=(document.getElementById('percentage1').value)*1;
	var percenttwo=(document.getElementById('percentage2').value)*1;
	
	if(percentone>100)
	{
		alert("Percentage Greater Than 100 Not Allowed");
		document.getElementById('percentage1').value="";
	}
	
	return;
	// Previous validation
	if(type=='percent_one' && percentone>100)
	{
		alert("Greater Than 100 Not Allowed");
		document.getElementById('percentage1').value="";
	}
	
	if(type=='percent_one' && percentone<=0)
	{
		alert("0 Or Less Than 0 Not Allowed")
		document.getElementById('percentage1').value="";
		document.getElementById('percentage1').disabled=true;
		document.getElementById('cbocomposition1').value=0;
		document.getElementById('cbocomposition1').disabled=true;
		document.getElementById('percentage2').value=100;
	    document.getElementById('percentage2').disabled=false;
		document.getElementById('cbocomposition2').disabled=false;
 	}
	
	if(type=='percent_one' && percentone==100)
	{
		document.getElementById('percentage2').value="";
		document.getElementById('cbocomposition2').value=0;
		document.getElementById('percentage1').disabled=false;
		document.getElementById('cbocomposition1').disabled=false;
		document.getElementById('percentage2').disabled=true;
		document.getElementById('cbocomposition2').disabled=true;
 	}
	
	if(type=='percent_one' && percentone < 100 && percentone > 0 )
	{
		document.getElementById('percentage2').value=100-percentone;
	    document.getElementById('percentage2').disabled=false;
		document.getElementById('cbocomposition2').disabled=false;
 	}
	
	if(type=='comp_one' && cbocompone==cbocomptwo  )
	{
		alert("Same Composition Not Allowed");
		document.getElementById('cbocomposition1').value=0;
 	}
	
 	if(type=='percent_two' && percenttwo>100)
	{
		alert("Greater Than 100 Not Allwed")
		document.getElementById('percentage2').value="";
 	}
	if(type=='percent_two' && percenttwo<=0)
	{
		alert("0 Or Less Than 0 Not Allwed")
		document.getElementById('percentage2').value="";
		document.getElementById('percentage2').disabled=true;
		document.getElementById('cbocomposition2').value=0;
		document.getElementById('cbocomposition2').disabled=true;
		document.getElementById('percentage1').value=100;
		document.getElementById('percentage1').disabled=false;
		document.getElementById('cbocomposition1').disabled=false;
	}
	
	if(type=='percent_two' && percenttwo==100)
	{
		document.getElementById('percentage1').value="";
		document.getElementById('cbocomposition1').value=0;
		document.getElementById('percentage1').disabled=false;
		document.getElementById('cbocomposition1').disabled=false;
		document.getElementById('percentage2').disabled=true;
		document.getElementById('cbocomposition2').disabled=true;
	}
	
	if(type=='percent_two' && percenttwo<100 && percenttwo>0)
	{
		document.getElementById('percentage1').value=100-percenttwo;
		document.getElementById('percentage1').disabled=false;
		document.getElementById('cbocomposition1').disabled=false;
 	}
	
	if(type=='comp_two' && cbocomptwo==cbocompone)
	{
		alert("Same Composition Not Allowed");
		document.getElementById('cbocomposition2').value=0;
 	}
}


function open_mrrpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	var company = $("#cbo_company_id").val();	
	var page_link='requires/yarn_receive_controller.php?action=mrr_popup_info&company='+company; 
	var title="Search MRR Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var recv_id=this.contentDoc.getElementById("hidden_recv_id").value;
		var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value; // mrr number
		var posted_in_account=this.contentDoc.getElementById("hidden_posted_in_account").value; // is posted accounct
		var receive_basis=this.contentDoc.getElementById("hidden_receive_basis").value;
		var receive_purpose=this.contentDoc.getElementById("hidden_receive_purpose").value;
		//alert(receive_basis+"="+receive_purpose);
		//rate_cond(receive_purpose);
		//fn_independent(receive_basis);	

 		$("#txt_mrr_no").val(mrrNumber);
		$("#is_posted_account").val(posted_in_account);
		$("#tbl_child").find('input,select').val('');
		$("#cbo_uom").val(15);
		$("#btn_color").val('N');		
		if(posted_in_account==1) document.getElementById("accounting_posted_status").innerHTML="Already Posted In Accounting.";
		else  document.getElementById("accounting_posted_status").innerHTML="";
		//load_drop_down('requires/yarn_receive_controller', '', 'load_drop_down_color', 'color_td_id' );
		// master part call here
		get_php_form_data(mrrNumber+"_"+recv_id, "populate_data_from_data", "requires/yarn_receive_controller");
		//$("#tbl_master").find('input,select').attr("disabled", true);
		//fn_independent($("#cbo_receive_basis").val());
		disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_receive_purpose*cbo_currency*txt_receive_date*cbo_currency*cbo_supplier', 1, "", "" );
		set_button_status(0, permission, 'fnc_yarn_receive_entry',1,1);	
		//right side list call here
		//show_list_view(receive_basis+"**"+rowID,'show_product_listview','list_product_container','requires/yarn_receive_controller','');
		//list view call here
		//show_list_view(mrrNumber,'show_dtls_list_view','list_container_yarn','requires/yarn_receive_controller','');
 	}
}

function change_color_tr(v_id,e_color)
{
	var tot_row=$("#tbl_product tbody tr").length;
	for(var i=1; i<=tot_row;i++)
	{
		if(v_id==i)
		{
			document.getElementById("tr_"+v_id).bgColor="#33CC00";
		}
		else
		{
			if (i%2==0) Bcolor="#E9F3FF";						
			else Bcolor="#FFFFFF";
			document.getElementById("tr_"+i).bgColor=Bcolor;
		}
	}
}

function openpage_challan()
{
	if( form_validation('cbo_company_id*cbo_supplier','Company Name*Supplier')==false )
	{
		return;
	}
	
	var receive_purpose = $("#cbo_receive_purpose").val();
	var company = $("#cbo_company_id").val();	
	var supplier = $("#cbo_supplier").val();	
	
	if(receive_purpose==15)
	{
		var page_link='requires/yarn_receive_controller.php?action=issue_challan_popup_info&company='+company+'&supplier='+supplier; 
		var title="Issue Challan No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			
			var issue_id=this.contentDoc.getElementById("hidden_issue_id").value;
			var challan_number=this.contentDoc.getElementById("hidden_challan_number").value; // mrr number
			$("#txt_issue_challan_no").val(issue_id);
			$("#txt_issue_id").val(challan_number);
		}
	}
}

function load_supplier()
{
	var receive_purpose = $("#cbo_receive_purpose").val();
	var receive_basis = $("#cbo_receive_basis").val();
	var company = $("#cbo_company_id").val();
	var wopiNumber = $("#txt_wo_pi").val();
	var wopiId = $("#txt_wo_pi_id").val();
	var hidden_paymode = $("#hidd_pay_mode").val();
	//alert(hidden_paymode);	
	
	if(form_validation('cbo_company_id','Company')==false )
	{
		$("#cbo_receive_purpose").val(0);
		return;
	}
	
	$("#txt_issue_challan_no").val('');
	$("#txt_issue_id").val('');
	$("#cbo_party").val(0);
	$('#loanParty_td').css('color','black');
	if(wopiNumber !="" && hidden_paymode > 0)
	{
		//load_drop_down( 'requires/yarn_receive_controller', receive_basis+'_'+receive_purpose+'_'+wopiNumber+'_'+hidden_paymode,'load_drop_down_company_from_eheck_wo_paymode', 'supplier' );
		load_drop_down( 'requires/yarn_receive_controller', receive_basis+'_'+receive_purpose+'_'+wopiId+'_'+hidden_paymode,'load_drop_down_company_from_eheck_wo_paymode', 'supplier' );
	}
	load_drop_down( 'requires/yarn_receive_controller', receive_purpose, 'load_drop_down_color2', 'color_td_id' );
	if(receive_purpose==15)
	{
		$("#txt_issue_challan_no").attr("disabled",false);
		//load_drop_down( 'requires/yarn_receive_controller', company, 'load_drop_down_supplier_from_issue', 'supplier' );
		if($('#cbo_supplier option').length==2)
		{
			$('#cbo_supplier').val($('#cbo_supplier option:last').val());
		}
		$('#cbo_party').attr('disabled','disabled');
	}
	else if(receive_purpose==5)
	{
		$("#txt_issue_challan_no").attr("disabled",true);
		//load_drop_down( 'requires/yarn_receive_controller',company, 'load_drop_down_supplier', 'supplier' );
		load_drop_down( 'requires/yarn_receive_controller',company, 'load_drop_down_party', 'loanParty' );
		$('#cbo_party').removeAttr('disabled','disabled');	
		$('#loanParty_td').css('color','blue');
	}
	else if(receive_purpose==16)
	{
		$("#txt_issue_challan_no").attr("disabled",true);
		//load_drop_down( 'requires/yarn_receive_controller',company, 'load_drop_down_supplier', 'supplier' );
		$('#cbo_party').attr('disabled','disabled');
		$('#cbo_color').val($('#cbo_color option:last').val());
		$('#cbo_color').attr('disabled','disabled');
	}
	else
	{
		$("#txt_issue_challan_no").attr("disabled",true);
		//load_drop_down( 'requires/yarn_receive_controller',company, 'load_drop_down_supplier', 'supplier' );
		$('#cbo_party').attr('disabled','disabled');
	}
	
	if(receive_basis==4) $('#cbo_supplier').removeAttr('disabled','disabled');
	else $('#cbo_supplier').attr('disabled','disabled');
}

//form reset/refresh function here
function fnResetForm()
{
	$("#tbl_master").find('input').attr("disabled", false);	
	disable_enable_fields( 'cbo_company_id*cbo_receive_basis*cbo_receive_purpose*cbo_store_name*txt_wo_pi*cbo_yarn_count*cbo_yarn_type*cbocomposition1*percentage1*cbo_color', 0, "", "" );
	disable_enable_fields( 'cbo_party*txt_lc_no*txt_issue_challan_no', 1, "", "" );
	set_button_status(0, permission, 'fnc_yarn_receive_entry',1);
	reset_form('yarn_receive_1','list_container_yarn*list_product_container','','','','cbo_uom*cbo_currency*txt_exchange_rate');
	document.getElementById("accounting_posted_status").innerHTML="";
	$("#txt_rate").val(0);
	$("#ile_td").text('ILE%');
	
}

function rate_cond(val)
{
	if( form_validation('cbo_company_id*cbo_receive_basis','Company Name*Receive Basis')==false )
	{
		return;
	}
	else
	{
		$("#txt_rate").val('');
		$("#txt_dyeing_charge").val('');
		$("#txt_avg_rate").val('');
		var cbo_receive_basis=$("#cbo_receive_basis").val();
		
		/*if(val==2)
		{
			
			if(cbo_receive_basis==4)
			{
				$("#txt_rate").attr("disabled",false);
				$("#txt_dyeing_charge").attr("disabled",true);
			}
			else
			{
				$("#txt_rate").attr("disabled",true);
				$("#txt_dyeing_charge").attr("disabled",false);
			}
		}
		else
		{
			$("#txt_rate").attr("disabled",false);
			$("#txt_avg_rate").attr("disabled",true);
			$("#txt_dyeing_charge").attr("disabled",true);
		}*/
		
		
		if(cbo_receive_basis==4 || cbo_receive_basis==6)
		{
			$("#txt_rate").attr("disabled",false);
			$("#txt_dyeing_charge").attr("disabled",true);
		}
		else
		{
			$("#txt_rate").attr("disabled",true);
			$("#txt_dyeing_charge").attr("disabled",false);
		}
		
	}
}



function exchange_rate(val)
{
	if(val==1)
	{
		$("#txt_exchange_rate").val(1);
		//$("#txt_exchange_rate").attr("disabled",true);
	}
	else
	{
		var recv_date = $('#txt_receive_date').val();
		var response=return_global_ajax_value( val+"**"+recv_date, 'check_conversion_rate', '', 'requires/yarn_receive_controller');
		$('#txt_exchange_rate').val(response);
		//$("#txt_exchange_rate").attr("disabled",false);
	}
}

function calculate_rate()
{
	var receive_purpose = $("#cbo_receive_purpose").val();
	if (receive_purpose==2 || receive_purpose==15)
	{
		$("#txt_rate").val(($("#txt_avg_rate").val()*1)+($("#txt_dyeing_charge").val()*1));
	}
	else
	{
		$("#txt_rate").val(0);
	}
}
function get_receive_basis(company_id)
{
	var independent_control_arr = JSON.parse('<? echo json_encode($independent_control_arr); ?>');
    $("#cbo_receive_basis option[value='4']").show();
    if(independent_control_arr && independent_control_arr[company_id]==1)
    {
        $("#cbo_receive_basis option[value='4']").hide();
    }

	var status = return_global_ajax_value(company_id, 'upto_variable_settings', '', 'requires/yarn_receive_controller').trim();
	$('#store_update_upto').val(status);

	/*var data="action=get_receive_basis&company_id="+company_id;
	http.open("POST","requires/yarn_receive_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = get_receive_basis_reponse;*/
}

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

/*function get_receive_basis_reponse()
{	
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText);
		$("#receive_baisis_td").html(reponse);		
		release_freezing();
	}
}*/

function change_placeholder(purpose_id)
{
	if(purpose_id == "15")
	{
		$('#txt_dyeing_charge').attr("placeholder","T.ch");
	}
	else if(purpose_id == "38")
	{
		$('#txt_dyeing_charge').attr("placeholder","W.ch");
	}
	else if(purpose_id == "46")
	{
		$('#txt_dyeing_charge').attr("placeholder","Dr.ch");
	}
	else
	{
		$('#txt_dyeing_charge').attr("placeholder","D.ch");
	}
}

function autoCalculateWeightPerBag()
{
	var txt_no_bag = $('#txt_no_bag').val();
	var txt_cone_per_bag = $('#txt_cone_per_bag').val();
	
	if( (txt_no_bag!="" && txt_no_bag>0) && (txt_cone_per_bag!="" && txt_cone_per_bag>0 ) )
	{		
		var weight_per_bag = (txt_no_bag/txt_cone_per_bag);		
	}
	
	if(weight_per_bag>0)
	{
		$('#txt_weight_per_bag').val(weight_per_bag.toFixed(4));
	}else{
		$('#txt_weight_per_bag').val('');
	}
	
}


function openmypage_job()
{
	if(form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	
	var companyID = $("#cbo_company_id").val();
	var buyer_name = $("#cbo_buyer_name").val();
	//var cbo_year = $("#cbo_year").val();
	var page_link='requires/yarn_receive_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name;
	var title='Job No Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var job_no=this.contentDoc.getElementById("hide_job_no").value;
		var job_id=this.contentDoc.getElementById("hide_job_id").value;
		var buyer_id=this.contentDoc.getElementById("hide_buyer_id").value;
		var sty_ref=this.contentDoc.getElementById("hide_sty_ref").value;
		//alert (job_no);
		$('#txt_job_no').val(job_no);
		$('#hide_job_id').val(job_id);
		$('#cbo_buyer_name').val(buyer_id).attr('disabled',true);
		$('#txt_style_no').val(sty_ref);	 
	}
}
</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="yarn_receive_1" id="yarn_receive_1" autocomplete="off" > 
    <div style="width:75%;">       
    <table width="98%" cellpadding="0" cellspacing="2" align="left">
     	<tr>
        	<td width="80%" align="center" valign="top">   
            	<fieldset style="width:1000px; float:left;">
                <legend>Yarn Receive</legend>
                <br />
                 	<fieldset style="width:950px;">                                       
                        <table  width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                            <tr>
                           		<td colspan="6" align="center">&nbsp;<b>MRR Number</b>
                                	<input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly />
                                </td>
                           </tr>
                           <tr>
                                <td width="130" align="right" class="must_entry_caption">Company Name </td>
                                <td width="170">
									<? 
                                  		echo create_drop_down( "cbo_company_id", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_basis_reset();load_drop_down( 'requires/yarn_receive_controller', this.value, 'load_drop_down_supplier', 'supplier' );load_drop_down( 'requires/yarn_receive_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value, 'is_allocation_maintained', 'requires/yarn_receive_controller');load_room_rack_self_bin('requires/yarn_receive_controller*1', 'store','store_td', this.value);get_receive_basis(this.value)" );
                                  		//load_drop_down( 'requires/yarn_receive_controller', this.value, 'load_drop_down_store', 'store_td' )
                                    ?>
                                </td>
                                <td width="94" align="right" class="must_entry_caption"> Receive Basis </td>
                                <td width="160" id="receive_baisis_td">
                                    <? 
                                    //load_drop_down( 'requires/yarn_receive_controller', this.value, 'load_drop_down_supplier', 'supplier'); load_drop_down('requires/yarn_receive_controller', this.value, 'load_drop_down_currency', 'currency'); load_drop_down('requires/yarn_receive_controller', this.value, 'load_drop_down_source', 'sources'); load_drop_down('requires/yarn_receive_controller', this.value, 'load_drop_down_lc', 'lc_no');
                                    
                                    echo create_drop_down( "cbo_receive_basis", 170, $receive_basis_arr,"", 1, "- Select Receive Basis -", $selected, "fn_independent(this.value)","","1,2,6,4");
                                    ?>
                               </td>
                               <td width="130" align="right" class="must_entry_caption">Receive Purpose</td>
                               <td width="170" id="rcv_purpose_td">
                                    <? 
                                    	echo create_drop_down( "cbo_receive_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", 43, "load_supplier(); rate_cond(this.value); change_placeholder(this.value)", 0,"5,4,8,7,12,15,38,43,46");
                                    ?>
                               </td>
                            </tr>
                            <tr>
                                <td  width="130" align="right" class="must_entry_caption">Receive Date </td>
                                <td width="170">
                                    <input type="text" name="txt_receive_date" id="txt_receive_date" class="datepicker" style="width:158px;" placeholder="Select Date" onChange="exchange_rate(document.getElementById('cbo_currency').value)"  value="<? echo date('d-m-Y');?>" readonly/>
                                </td>
                                <td width="94" align="right" class="must_entry_caption"> Challan No </td>
                                <td width="160"><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:158px" ></td>
                                <td width="130" align="right" class="must_entry_caption">Store Name</td>
                                <td width="170" id="store_td">
                                    <? 
                                    echo create_drop_down( "cbo_store_name", 170, $blank_array,"", 1, "-- Select Store --", 0, "" );
                                    ?>
                                </td>
                            </tr>
                            <tr>
                          		<td width="130" align="right">WO / PI</td>
                                <td width="170">
                                <input class="text_boxes"  type="text" name="txt_wo_pi" id="txt_wo_pi" onDblClick="openmypage('xx','WO/PI Popup')"  placeholder="Double Click" style="width:158px;" readonly disabled />
                                <input type="hidden" id="txt_wo_pi_id" name="txt_wo_pi_id" value="" />
                                <input type="hidden" id="hidd_pay_mode" name="hidd_pay_mode" value="" />
                                </td> 
                                <td width="94" align="right" class="must_entry_caption" id="supplier_td"> Supplier </td>
                                <td id="supplier" width="160"> 
                                    <?
                                      echo create_drop_down( "cbo_supplier", 170, $blank_array,"", 1, "--- Select Supplier ---", $selected, "",1);
                                    ?>
                                </td>
                               	<td width="94" align="right" id="loanParty_td"> Loan Party </td>
                                <td id="loanParty" width="160"> 
                                    <?
                                    	echo create_drop_down( "cbo_party", 170, $blank_array,"", 1, "--- Select Party ---", $selected, "",1);
                                    ?>
                                </td>
                            </tr>
                             <tr>
                                <td width="130" align="right" class="must_entry_caption">Currency</td>
                                <td width="170" id="currency"> 
                                <?
                                   echo create_drop_down( "cbo_currency", 170, $currency,"", 1, "-- Select Currency --", 0, "exchange_rate(this.value)",1 );
                                ?>
                                </td>
                                <td width="130" align="right" class="must_entry_caption">Exchange Rate</td>
                                <td width="170">
                                    <input type="text" name="txt_exchange_rate" id="txt_exchange_rate" class="text_boxes_numeric" style="width:158px" onBlur="fn_calile()" disabled/>	
                                </td>
                                <td width="94" align="right" class="must_entry_caption">Source</td>
                                <td width="160" id="sources">  
									<?
                                        echo create_drop_down( "cbo_source", 170, $source,"", 1, "-- Select --", $selected, "",1 );
                                    ?>
                                </td>                                  
                            </tr>
                            <tr>
                            	<td width="130" align="right"> L/C No </td>
                                <td id="lc_no" width="170">
                                <input class="text_boxes"  type="text" name="txt_lc_no" id="txt_lc_no" style="width:158px;" placeholder="Display" onDblClick="popuppage_lc()" readonly disabled  />  
                                <input type="hidden" name="hidden_lc_id" id="hidden_lc_id" />
                                </td>
                                <td width="94" align="right">Issue Challan No.</td>
                                <td width="160" id="sources">  
									<input type="text" name="txt_issue_challan_no" id="txt_issue_challan_no" class="text_boxes" style="width:158px" onDblClick="openpage_challan();" placeholder="Double Click To Search" disabled readonly/>	
                                    <input type="hidden" id="txt_issue_id" name="txt_issue_id" value="" />
                                </td>
                                <td align="right">Remarks</td>
                                <td><input type="text" id="txt_mst_remarks" name="txt_mst_remarks" class="text_boxes" style="width:160px" /></td>
                            </tr>
                            <tr>
                                <td align="right">File</td>
                                <td> <input type="button" class="image_uploader" style="width:170px" value="CLICK TO ADD FILE" onClick="file_uploader( '../../', document.getElementById('txt_mrr_no').value,'', 'sweater_yarn_receive', 2 ,1)"> </td>
                                <td  width="130" align="right">Challan Date </td>
                                <td width="170">
                                    <input type="text" name="txt_challan_date" id="txt_challan_date" class="datepicker" style="width:158px;" placeholder="Select Date"  />
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
                            <table width="240" cellspacing="2" cellpadding="0" border="0" style="float:left"> 
                                <tr>    
                                    <td width="100" align="right" class="must_entry_caption">Job No</td>
                                    <td width="140">
                                    <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:120px" onDblClick="openmypage_job()"  placeholder="Browse" readonly />
                                    <input type="hidden" name="hide_job_id" id="hide_job_id" readonly>
                                    </td>
                                </tr>
                                <tr>    
                                    <td align="right" class="must_entry_caption">Yarn Count</td>
                                    <td>         
                                        <?
                                            echo create_drop_down( "cbo_yarn_count", 130, "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC","id,yarn_count", 1, "--Select--", 0, "",0 );
                                        ?>
                                    </td>
                                </tr>
                                <tr>    
                                    <td align="right">Composition</td>
                                    <td id="composition_td">
                                    <?
                                        echo create_drop_down( "cbocomposition1", 80, $composition,"", 1, "-- Select --", "", "", $disabled,"" );
                                        echo '<input type="text" id="percentage1" name="percentage1" class="text_boxes_numeric" style="width:40px" placeholder="%" value="100" onBlur="control_composition(\'percent_one\')" />';//
                                        echo create_drop_down( "cbocomposition2", 80, $composition,"", 1, "-- Select --", "", "", 1,"" );
                                        echo '<input type="text" id="percentage2" name="percentage2" class="text_boxes_numeric" style="width:40px" disabled />';
                                        // placeholder="%" onBlur="control_composition(\'percent_two\')"
                                    ?>	
                                        <!--script>load_drop_down( 'requires/yarn_receive_controller', '', 'load_drop_down_composition', 'composition_td' );</script-->
                                    </td>
                                </tr>
                                <tr>    
                                    <td align="right" class="must_entry_caption">Yarn Type</td>
                                    <td>
                                        <?
                                            asort($yarn_type);
                                            //echo "<pre>";print_r($yarn_type);
                                            echo create_drop_down( "cbo_yarn_type", 130, $yarn_type,"", 1, "--Select--", 0, "",0 );
                                        ?>
                                    </td>
                                </tr>
                                <tr>   
                                    <td align="right" class="must_entry_caption">Color</td>
                                    <td id="color_td_id">
                                        <?
											//and grey_color=1
                                            if($db_type==0) $color_cond=" and color_name!=''"; else $color_cond=" and color_name IS NOT NULL";
                                            echo create_drop_down( "cbo_color", 110, "select id,color_name from lib_color where status_active=1 $color_cond order by color_name","id,color_name", 1, "--Select--", 0, "",1 );
                                        ?>
                                        <input type="button" name="btn_color" id="btn_color" class="formbuttonplasminus"  style="width:20px" onClick="fn_color_new(this.id)" value="N" />
                                    </td>
                                </tr>  
                                <tr>
                                    <td align="right" class="must_entry_caption"><!--Yarn -->Lot/ Batch</td>
                                    <td><input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:120px;" /></td>
                                </tr> 
								<tr>
					                <td align="right">Bal. PI/ Ord. Qnty</td>
					                <td>
                                    <input class="text_boxes_numeric"  name="txt_order_qty" id="txt_order_qty" type="text" style="width:120px;" readonly />
                                    <input class="text_boxes_numeric"  name="txt_bal_order_qty" id="txt_bal_order_qty" type="hidden"/>
                                    </td>
				                </tr> 
                            </table>
                            <table width="240" cellspacing="2" cellpadding="0" border="0" style="float:left">
                            	<tr>    
                                    <td align="right" width="88">Buyer</td>
                                    <td width="146" id="buyer_td">
									<? 
                                        echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                                    ?>
                                    </td>
                                </tr>
                                <tr>    
                                    <td align="right" width="88" class="must_entry_caption">Brand</td>
                                    <td width="146"><input type="text" name="txt_brand" id="txt_brand" class="text_boxes" style="width:120px;" /></td>
                                </tr>
                                <tr>    
                                    <td align="right" class="must_entry_caption">Recv. Qnty.</td>   
                                    <td >
                                        <input name="txt_receive_qty" id="txt_receive_qty" class="text_boxes_numeric" type="text" style="width:120px;" onBlur="fn_calile()" />
                                        <input name="hdn_receive_qty" id="hdn_receive_qty" type="hidden" />
                                    </td>
                                </tr>
								<tr>
                                    <td  align="right">Challan Qty.</td>
                                    <td>
                                        <input name="txt_challan_qty" id="txt_challan_qty" class="text_boxes_numeric" type="number" style="width:120px;"/>                                      
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">UOM</td>   
                                    <td><? echo create_drop_down( "cbo_uom", 130, $unit_of_measurement,"", 0, "--Select--", 15, "",1 ); ?></td> 
                                </tr>
                                
                                <tr>    
                                    <td align="right" class="must_entry_caption">Rate</td>   
                                    <td >
                                        <input name="txt_rate" id="txt_rate" class="text_boxes_numeric" type="text" style="width:30px;" onBlur="fn_calile()" value="0" />
                                        <input name="txt_avg_rate" id="txt_avg_rate" class="text_boxes_numeric" type="text" style="width:30px;" onBlur="calculate_rate()" placeholder="Avg" disabled  />
                                        <input name="txt_dyeing_charge" id="txt_dyeing_charge" class="text_boxes_numeric" type="text" style="width:30px;" placeholder="D.Ch" onBlur="calculate_rate()" />
                                    </td>
                                </tr>
                                <tr>   
                                    <td align="right" id="ile_td">ILE%</td>   
                                    <td >
                                        <input name="txt_ile" id="txt_ile" class="text_boxes_numeric" type="text" style="width:120px;" placeholder="ILE COST" readonly />
                                    </td>
                                </tr>
                                 <tr>
                                    <td align="right">Amount</td>
                                    <td><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric" style="width:120px;" readonly disabled /></td>
                                </tr> 
                               
                            </table>
                            
                            <table width="240" cellspacing="2" cellpadding="0" border="0" style="float:left">
                                <tr> 
                                    <td align="right" width="110">Style</td>
                                    <td width="130">
                                        <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:120px" readonly disabled />
                                    </td>                
                                </tr>
                                <tr> 
                                    <td align="right">Book Currency.</td>
                                    <td>
                                        <input type="text" name="txt_book_currency" id="txt_book_currency" class="text_boxes_numeric" style="width:120px;" readonly disabled />
                                    </td>                
                                </tr>
                                <tr>
                                    <td align="right">No. Of Bag</td>
                                    <td><input name="txt_no_bag" id="txt_no_bag" type="text" class="text_boxes_numeric"  style="width:120px;" /></td> 
                                </tr>
                                <tr> 
                                    <td align="right">No. Of Cone</td>   
                                    <td>
                                        <input name="txt_cone_per_bag" id="txt_cone_per_bag" class="text_boxes_numeric" type="text" style="width:120px;"/>
                                    </td>
                                </tr>
                                <tr> 
                                    <td align="right">Weight per Bag</td>   
                                    <td>
                                        <input name="txt_weight_per_bag" id="txt_weight_per_bag" class="text_boxes_numeric" type="text" style="width:120px;" onClick="autoCalculateWeightPerBag();"/>
                                    </td>
                                </tr>
                                <tr> 
                                    <td align="right">Wght @ Cone</td>
                                    <td><input class="text_boxes_numeric"  name="txt_weight_per_cone" id="txt_weight_per_cone" type="text" style="width:120px;"  /></td>
                                 </tr> 
                                 <tr>                 
                                    <td align="right">Product Code</td>
                                    <td><input class="text_boxes"  name="txt_prod_code" id="txt_prod_code" type="text" style="width:120px;" readonly  /></td>
                                </tr> 
                                <tr> 
                                    <td align="right">Remarks</td>   
                                    <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:120px;" /></td> 
                                </tr>                               
                            </table>
                            
                            <table width="230" cellspacing="2" cellpadding="0" border="0">
                                
                                 <tr> 
                                    <td width="118" align="right">Floor</td>

									<td id="floor_td"  width="106">
										<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>                                
                                </tr>
                                 <tr>
	                         		<td align="right">Room</td>

	                         		<td id="room_td">
										<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>                                    	
                                </tr>
                                <tr> 
                                    <td width="118" align="right">Rack</td>
                                    <td id="rack_td" width="106">
										<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                	<td align="right">Shelf</td>
                                	<td id="shelf_td">
										<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
									</td>
                                </tr>
                                <tr> 
                                     <td align="right">Bin/Box</td>
                                     <td id="bin_td">
										<? echo create_drop_down( "cbo_bin", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
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
                             <input type="hidden" id="allocation_maintained" name="allocation_maintained" value="" />
                             <input type="hidden" id="update_id" name="update_id" value="" />
                             <input type="hidden" id="is_posted_account" name="is_posted_account" value="" />
							 <input type="hidden" name="store_update_upto" id="store_update_upto">
                             <input type="hidden" name="job_no" id="job_no" readonly /><!--For Basis Bokking and Yarn Dyeing Purpose-->
							 <? echo load_submit_buttons( $permission, "fnc_yarn_receive_entry", 0,1,"fnResetForm()",1);?>
                             <div id="accounting_posted_status" style=" color:red; font-size:24px;" align="left"></div>
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
    <div id="list_product_container" style="max-height:500px; width:24%; overflow:auto; float:left; padding-top:1px; margin-top:5px; position:relative;"></div>  
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
$('#cbo_color').val($('#cbo_color option:last').val());                          
</script>
</html>
