<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Left Over Finish Garments Issue
				
Functionality	:	This form is finish input entry
JS Functions	:
Created by		:	Shafuqr Rahman
Creation date 	: 	10-05-2018
Updated by 		: 	Md. Shafiqul Islam
Update date		: 	21-12-2019	 
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$u_id=$_SESSION['logic_erp']['user_id'];
$level=return_field_value("user_level","user_passwd","id='$u_id' and valid=1 ","user_level");

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Iron Output Info","../", 1, 1, $unicode,'','');


$issue_purpose_arr = array(1=>"Sell",2=>"Gift",3=>"Others");
//$pay_term_arr = array(1=>"Cash",2=>"Credit");
//$order_type_arr = array(1=>"Self Order",2=>"Subcontract Order");
$goods_type_arr = array(1=>"Good GMT In Hand",2=>"Damage GMT",3=>"Leftover Sample");
?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function search_party()
{
	if ( form_validation('cbo_company_name','Company Name')==false )
	{
		return;
	}
	else
	{
		var page_link = 'requires/left_over_garments_issue_controller.php?action=search_party&company='+$('#cbo_company_name').val();
		var title = 'Party Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=404px,height=320px,center=1,resize=0,scrolling=1','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var party_data_arr=this.contentDoc.getElementById("hidden_party_data").value;
			var response=party_data_arr.split("_");
			$('#txt_party_name').val(response[1]);
			$('#hidden_party_id').val(response[0]) ;
			
			$("#cbo_company_name").attr("disabled","disabled"); 
		}
	}
}

function issu_purpose(issue)
{
	//alert(issue);
	if (issue==1) { //Buyer
		
		//load_drop_down( 'requires/left_over_garments_issue_controller', this.value, 'load_drop_down_buyer', 'cbo_buyer_name' );
		$('#txt_party_name').attr("onDblClick","search_party()");
		$('#txt_party_name').attr("placeholder","Browse").val('');
		$('#txt_party_name').attr("readonly","readonly");
		$("#cbo_currency_mst").removeAttr("disabled");
		
	}
	else
	{
		$('#txt_party_name').removeAttr("onDblClick");
		$('#txt_party_name').removeAttr("readonly").val('');
		$('#txt_party_name').attr("placeholder","Write");
		$("#cbo_currency_mst").val('0');
		$("#exchange_rate").val('');
		$("#cbo_currency_mst").attr("disabled","disabled");
		
	}
}


function openmypage(page_link,title)
{
	if ( form_validation('cbo_company_name*cbo_location_name*cbo_order_type*cbo_store_name*cbo_goods_type','Company Name*Location Name*Order Type*Goods Type')==false )
	{
		return;
	}
	else
	{
		
		var page_link = 'requires/left_over_garments_issue_controller.php?action=order_popup&company='+$('#cbo_company_name').val()+'&location_name='+$('#cbo_location_name').val()+'&order_type='+$('#cbo_order_type').val()+'&goods_type='+$('#cbo_goods_type').val()+'&garments_nature='+$('#garments_nature').val()+'&store_name='+$('#cbo_store_name').val()+'&party_id='+$('#hidden_party_id').val()+'&country_maintain_variable='+$('#country_maintain_variable').val()+'&leftover_source='+$('#leftover_source').val();
		var title = 'Left Over Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=370px,center=1,resize=0,scrolling=1','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			//alert(theform);
			//var dtlsId=this.contentDoc.getElementById("hidden_dtls_id").value;
			//var mstId=this.contentDoc.getElementById("hidden_mst_id").value;
			
			var po_id=this.contentDoc.getElementById("hidden_po_id").value;
			var company_id=this.contentDoc.getElementById("hidden_company_id").value;
			var location_name=this.contentDoc.getElementById("hidden_location_name").value;	
			var store_name=this.contentDoc.getElementById("hidden_store_name").value;
			var country_name=this.contentDoc.getElementById("hidden_country_name").value;	
			var item_id=this.contentDoc.getElementById("hidden_item_id").value;	
			let category_id = this.contentDoc.getElementById("hidden_category_id").value;
			//var currency_id=this.contentDoc.getElementById("hidden_currency_id").value;
			//alert(dtlsId+'_'+mstId+'_'+company_id+'_'+location_name+'_'+currency_id);
			//$("#cbo_country_name").val(country_id);
			//$("#cbo_location_name").val(location_name);	
			
			//check_exchange_rate(currency_id);
			
			//load_drop_down( 'requires/left_over_garments_issue_controller', company_id+'_'+location_name, 'load_drop_down_store_name', 'store_name_td' ); 
			var country_maintain = $('#country_maintain_variable').val();
			var leftover_source = $('#leftover_source').val();
			var store_id = $('#cbo_store_name').val();
			var variableSettings = $('#sewing_production_variable').val();
			let goods_type = $('#cbo_goods_type').val();
			
			

			
			//get_php_form_data(dtlsId+'**'+mstId, "color_and_size_level_left_over","requires/left_over_garments_issue_controller" );
			//get_php_form_data(po_id+"**"+company_id+"**"+location_name+"**"+country_name+"**"+item_id+"**"+country_maintain+"**"+leftover_source+"**"+variableSettings, "color_and_size_level_left_over","requires/left_over_garments_issue_controller" );
			get_php_form_data(po_id+"**"+company_id+"**"+location_name+"**"+country_name+"**"+item_id+'**'+country_maintain+"**"+leftover_source+"**"+variableSettings+"**"+category_id, "color_and_size_level_left_over","requires/left_over_garments_issue_controller" );
			show_list_view(po_id+"**"+country_maintain+"**"+leftover_source+"**"+store_id+"**"+goods_type,'show_country_listview','list_view_country','requires/left_over_garments_issue_controller');
			get_left_over_balance(po_id,country_name,item_id,country_maintain,category_id);

			
				$("#cbo_company_name").attr("disabled","disabled"); 
				$("#cbo_location_name").attr("disabled","disabled");
				$("#cbo_store_name").val(store_name);
				//$("#txt_leftover_date").attr("disabled","disabled");
				$("#cbo_order_type").attr("disabled","disabled");
				$("#cbo_buyer_name").attr("disabled","disabled");
				$("#cbo_store_name").attr("disabled","disabled");
				$("#cbo_goods_type").attr("disabled","disabled");
				//$("#cbo_floor_name").attr("disabled","disabled");
				//$("#txt_remark").attr("disabled","disabled");
				
				set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
				release_freezing();
		}
	}
}

function put_country_data(po_id, item_id, country_id, category_id, issue_qnty)
{
	freeze_window(5);

	$("#cbo_item_name").val(item_id);
	$("#txt_yet_to_issue").val(issue_qnty);
	$("#cbo_country_name").val(country_id);
	$("#cbo_category_id").val(category_id);
	var country_maintain=$('#country_maintain_variable').val();
	var leftover_source=$('#leftover_source').val();

	get_php_form_data(po_id+'**'+item_id+'**'+country_id+'**'+country_maintain+"**"+leftover_source+"**"+category_id, "populate_data_from_search_popup", "requires/left_over_garments_issue_controller" );

	var variableSettings=$('#sewing_production_variable').val();
	var styleOrOrderWisw=$('#styleOrOrderWisw').val();
	var company_id=$('#cbo_company_name').val();
	var location_name=$('#cbo_location_name').val();

	if(variableSettings!=1)
	{
		// get_php_form_data(po_id+'**'+item_id+'**'+variableSettings+'**'+styleOrOrderWisw+'**'+country_id, "color_and_size_level_left_over", "requires/left_over_garments_issue_controller" );
		get_php_form_data(po_id+"**"+company_id+"**"+location_name+"**"+country_id+"**"+item_id+'**'+country_maintain+"**"+leftover_source+"**"+variableSettings+"**"+category_id, "color_and_size_level_left_over","requires/left_over_garments_issue_controller" );
	}
	else
	{
		$("#txt_total_issue").removeAttr("readonly");
	}

	// show_list_view(po_id+'**'+item_id+'**'+country_id,'show_dtls_listview','list_view_container','requires/left_over_garments_issue_controller','setFilterGrid(\'tbl_list_search\',-1)');
	reset_form('','','txt_remark','','');
	set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
	release_freezing();
}

function fnc_left_over_gmts_input(operation)
{
	if(operation==0 || operation==1 || operation==2)
	{
 		if ( form_validation('cbo_company_name*cbo_order_type*cbo_goods_type*cbo_issue_purpose*cbo_store_name*txt_order_no*txt_total_issue','Company Name*Order Type*Goods Type*Issue Purpose*Order No*Issue Qty')==false )
		{
			return;
		}		
		else
		{			
			freeze_window(operation);			
			var sewing_production_variable = $("#sewing_production_variable").val();
			var colorList = ($('#hidden_colorSizeID').val()).split(",");
			
			var i=0; 
			var k=0; 
			var colorIDvalue=''; 
			 
			if(sewing_production_variable==2)//color level
			{
 				$("input[name=txt_color]").each(function(index, element) {
 					if( $(this).val()!='' )
					{
						if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{
							colorIDvalue += "**"+colorList[i]+"*"+$(this).val();
						}
					}
					i++;
				});
			}
			else if(sewing_production_variable==3)//color and size level
			{
				$("input[name=colorSize]").each(function(index, element) {
					if( $(this).val()!='' )
					{
						if(i==0)
						{
							colorIDvalue = colorList[i]+"*"+$(this).val();
						}
						else
						{
							colorIDvalue += "***"+colorList[i]+"*"+$(this).val();
						}
					}
					i++;
				});
			}
			/*if(colorIDvalue=="") 
			{
				
				alert("Please Provide Issue Quantity");
				release_freezing();
				return;
			}*/
			//alert(colorIDvalue); 
			 
			 
			 
			var data="action=save_update_delete&operation="+operation+"&colorIDvalue="+colorIDvalue+get_submitted_data_string('cbo_company_name*cbo_location_name*txt_issue_date*cbo_order_type*cbo_buyer_name*cbo_store_name*exchange_rate*txt_remark*cbo_goods_type*txt_order_no*cbo_issue_purpose*txt_party_name*hidden_party_id*cbo_pay_term*txt_challan_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_issue*txt_remark2*cbo_currency*cbo_currency_mst*txt_fob_rate*txt_issue_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no*sewing_production_variable*country_maintain_variable*leftover_source*hidden_po_break_down_id*hidden_colorSizeID*styleOrOrderWisw*variable_is_controll*txt_user_lebel*txt_mst_id*txt_system_no*hidden_dtls_id*hidden_receive_dtls_id*txt_sale_rate*cbo_category_id',"../");
 			//alert(data);return;
 			http.open("POST","requires/left_over_garments_issue_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_left_over_gmts_input_Reply_info;
		}
	}
}
 
function fnc_left_over_gmts_input_Reply_info()
{
	release_freezing();
 	if(http.readyState == 4) 
	{
		var response=http.responseText.split('**');		
		var po_id = $("#txt_po_id") .val();
		var country_maintain = $("#country_maintain_variable").val();
		var country_id 				= $('#cbo_country_name').val();
		var item_id 				= $('#cbo_item_name').val();
		var leftover_source = $("#leftover_source").val();
		var store_id = $('#cbo_store_name').val();
		let goods_type = $('#cbo_goods_type').val();
		if(response[0]==20) 
		{
			alert(response[1]);
			release_freezing();
		}
		else if(response[0]==0 || response[0]==1)
		{ 
			show_msg(trim(response[0]));
			$("#txt_mst_id").val(response[1]);
			$("#txt_system_no").val(response[2]);
			reset_form('','','hidden_dtls_id*cbo_buyer_name*hidden_receive_dtls_id*txt_order_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_issue*txt_sale_rate*txt_remark2*cbo_currency*txt_fob_rate*txt_issue_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no','','');
			
			$('#breakdown_td_id').html('');
			
			$("#cbo_company_name").attr("disabled","disabled"); 
			$("#cbo_location_name").attr("disabled","disabled");
			$("#txt_issue_date").attr("disabled","disabled");
			$("#cbo_order_type").attr("disabled","disabled");
			//$("#cbo_buyer_name").attr("disabled","disabled");
			$("#cbo_store_name").attr("disabled","disabled");
			$("#cbo_goods_type").attr("disabled","disabled");
			//$("#cbo_floor_name").attr("disabled","disabled");
			$("#txt_remark").attr("disabled","disabled");
			show_list_view(response[1]+'**'+country_maintain+'**'+leftover_source,'show_dtls_listview','list_view_container','requires/left_over_garments_issue_controller','setFilterGrid(\'tbl_list_search\',-1)');
			show_list_view(po_id+'**'+country_maintain+'**'+leftover_source+"**"+store_id+"**"+goods_type,'show_country_listview','list_view_country','requires/left_over_garments_issue_controller');	
			get_left_over_balance(po_id,country_id,item_id,country_maintain);
			
			set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
			release_freezing();
		}
		else if(response[0]==2)
		{
			show_msg(trim(response[0]));
			
			reset_form('ironoutput_1','list_view_country','','','childFormReset(3)');
			
			show_list_view(response[1]+'**'+country_maintain+'**'+leftover_source,'show_dtls_listview','list_view_container','requires/left_over_garments_issue_controller','setFilterGrid(\'tbl_list_search\',-1)');
			show_list_view(po_id+'**'+country_maintain+'**'+leftover_source+"**"+store_id+"**"+goods_type,'show_country_listview','list_view_country','requires/left_over_garments_issue_controller');	
			
			set_button_status(0, permission, 'fnc_left_over_gmts_input',1,0);
			release_freezing();
		}
 	}
}

function system_number_popup()
{
	/*if ( form_validation('cbo_company_name*cbo_location_name*txt_leftover_date','Company Name*cbo_location_name*Receive Date')==false )
	{
		return;
	}
	else
	{*/
		
		var page_link = 'requires/left_over_garments_issue_controller.php?action=system_number_popup&company='+$('#cbo_company_name').val()+'&location_name='+$('#cbo_location_name').val()+'&issue_date='+$('#txt_issue_date').val()+'&order_type='+$('#cbo_order_type').val()+'&goods_type='+$('#cbo_goods_type').val()+'&country_maintain_variable='+$('#country_maintain_variable').val()+'&leftover_source='+$('#leftover_source').val();
		
		var title = 'Issue ID Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=370px,center=1,resize=0,scrolling=1','');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			
			var responseDataArr=this.contentDoc.getElementById("hidden_search_data").value.split('_');
			var country_maintain = $('#country_maintain_variable').val()
			var leftover_source = $('#leftover_source').val()
			//alert(responseDataArr[0]);
			get_php_form_data(responseDataArr[0], "populate_mst_form_data", "requires/left_over_garments_issue_controller" )
			show_list_view(responseDataArr[0]+'**'+country_maintain+'**'+leftover_source,'show_dtls_listview','list_view_container','requires/left_over_garments_issue_controller','setFilterGrid(\'tbl_list_search\',-1)');
			// show_list_view(responseDataArr[0],'show_country_listview','list_view_country','requires/left_over_garments_issue_controller');
			

			$("#cbo_company_name").attr("disabled","disabled"); 
			$("#cbo_location_name").attr("disabled","disabled");
			$("#txt_leftover_date").attr("disabled","disabled");
			$("#cbo_order_type").attr("disabled","disabled");
			$("#cbo_store_name").attr("disabled","disabled");
			$("#cbo_goods_type").attr("disabled","disabled");
			$("#cbo_floor_name").attr("disabled","disabled");
			//$("#txt_remark").attr("disabled","disabled");
			
			reset_form('','','hidden_dtls_id*cbo_buyer_name*hidden_receive_dtls_id*txt_order_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_issue*txt_remark2*cbo_currency*txt_fob_rate*txt_issue_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no*cbo_category_id*txt_yet_to_issue','','');

			release_freezing();
			
		}
		
	//}
} 
 
 
function check_exchange_rate(curr_id)
{
	var cbo_currercy=curr_id;
	var booking_date = $('#txt_issue_date').val();
	var response=return_global_ajax_value( cbo_currercy+"**"+booking_date, 'check_conversion_rate', '', 'requires/left_over_garments_issue_controller');
	var response=response.split("_");
	$('#exchange_rate').val(response[1]);
	
}


function sale_rate_calculator()
{
	var sale_rate=$('#txt_sale_rate').val()*1;
	var total_issue=$('#txt_total_issue').val()*1;
	var exchangeRate=$('#exchange_rate').val()*1;
	var issueAmount = total_issue*exchangeRate*sale_rate;
	$("#txt_issue_amount").val(issueAmount);
}




 
function childFormReset(id)
{
	if(id==1 || id==3){ // Button Resate
		$("#cbo_company_name").removeAttr("disabled"); 
		$("#cbo_location_name").removeAttr("disabled"); 
		$("#txt_leftover_date").removeAttr("disabled"); 
		$("#cbo_order_type").removeAttr("disabled"); 
		$("#cbo_buyer_name").removeAttr("disabled"); 
		$("#cbo_store_name").removeAttr("disabled"); 
		$("#cbo_goods_type").removeAttr("disabled"); 
		$("#cbo_floor_name").removeAttr("disabled"); 
			//$("#txt_remark").removeAttr("disabled"); 
		$('#breakdown_td_id').html('');
		$('#list_view_container').html('');
		
	}else if(id==2){ // Button Save / Update
	
		//$('#list_view_container').html('');
		
	}else{
		
		//reset_form('','','txt_remark*txt_remark2*txt_mst_id','','');
		//$('#txt_sewing_quantity').attr('placeholder','');
		//$('#list_view_container').html('');
		
	}
}  


function populate_selected_data(dtls_id,mst_id,po_id,country_id,item_id,variableSettings,country_maintain,leftover_source,category_id)
{
	//freeze_window(5);
	release_freezing();
	reset_form('','','hidden_dtls_id*cbo_buyer_name*hidden_receive_dtls_id*txt_order_no*txt_po_id*txt_style_name*cbo_item_name*cbo_country_name*txt_total_issue*txt_sale_rate*txt_remark2*cbo_currency*txt_fob_rate*txt_issue_amount*txt_bdt_amount*cbo_room_no*cbo_rack_no*cbo_shelf_no*cbo_bin_no','','');	
	
	get_php_form_data(dtls_id+"**"+mst_id+"**"+po_id+"**"+country_id+"**"+item_id+"**"+variableSettings+'**'+country_maintain+'**'+leftover_source+'**'+category_id,"color_and_size_level_left_over_update", "requires/left_over_garments_issue_controller" );
	get_left_over_balance(po_id,country_id,item_id,country_maintain);
	
	/*var gmts_item = $("#cbo_item_name").val();//item_id
	var company_id=$('#cbo_company_name').val();
	if(variableSettings!=1)
	{ 
		
		get_php_form_data(po_id+'**'+country_id+'**'+item_id+'**'+dtls_id+'**'+mst_id+'**'+variableSettings+'**'+$('#cbo_order_type').val(), "color_and_size_level_left_over_update", "requires/left_over_garments_issue_controller" ); 
		
	}*/
	release_freezing();
}

function left_over_gmts_issue_print()
{
	if ( form_validation('txt_system_no*txt_mst_id','System No*System ID')==false )
	{
		return;
	}
	else
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$('#txt_system_no').val()+'*'+$('#cbo_order_type').val(), "left_over_gmts_issue_print", "requires/left_over_garments_issue_controller");
	}
	
}

function left_over_gmts_issue_print_2()
{
	if( form_validation('txt_system_no*txt_mst_id','System No*System ID')==false )
	{
		return;
	}
	var report_title='Garments Sales Challan/Gate Pass';
	var confirmMsg = 'Press OK to show Rate and Amount';
	var showRate = confirm(confirmMsg) ? 1 : 0;

	print_report( $('#cbo_company_name').val()+'*'+$('#txt_mst_id').val()+'*'+report_title+'*'+$('#txt_system_no').val()+'*'+$('#cbo_order_type').val()+'*'+showRate, 'left_over_gmts_issue_print_2', 'requires/left_over_garments_issue_controller');
}

function fn_total(tableName,index)
{	
    var filed_value = $("#colSize_"+tableName+index).val()*1;
    var filed_update_value = $("#colSizeUpdate_"+tableName+index).val()*1;
	//var placeholder_value = $("#colorSizePOQnty_"+tableName+index).val()*1;
	var placeholder_value = $("#colSize_"+tableName+index).attr('placeholder')*1;
	var txt_user_lebel=$('#txt_user_lebel').val()*1;
	var variable_is_controll=$('#hidden_variable_cntl').val()*1;
	//alert(filed_value +'> ('+(placeholder_value*1)+'+'+(filed_update_value*1)+')')
	
	if(filed_value > ((placeholder_value*1)+(filed_update_value*1)))
	{
		//alert(variable_is_controll+"_"+txt_user_lebel);
		if(txt_user_lebel!=2)
		{
			// alert("User Lebel = 2 ? ");		
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+tableName+index).val('');	
		}
		else
		{
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+tableName+index).val('');	
			/*
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
				void(0);
			else
			{
				$("#colSize_"+tableName+index).val('');
			}
			*/
		}

	}
	var totalRow = $("#table_"+tableName+" tr").length;
	//alert(tableName);
	math_operation( "total_"+tableName, "colSize_"+tableName, "+", totalRow);
	if($("#total_"+tableName).val()*1!=0)
	{
		$("#total_"+tableName).html($("#total_"+tableName).val());
	}
	var totalVal = 0;
	$("input[name=colorSize]").each(function(index, element) {
        totalVal += ( $(this).val() )*1;
    });
	//alert(totalVal);
	$("#txt_total_issue").val(totalVal);
	
	if($('#cbo_issue_purpose').val()*1 == 1)
	{
		sale_rate_calculator();
	}
	//var total_left = $("#txt_total_issue").val()*1;
	
	//Laftover Amount = (Total Left Over Receive *  FOB Rate);
	//var left_over = ($('#txt_fob_rate').val()*1)*total_left;
	//alert(left_over);
	//$('#txt_issue_amount').val(left_over);
	
	//var bdt_amount = ($('#exchange_rate').val()*1) * $('#txt_issue_amount').val()*1;
	//$('#txt_bdt_amount').val(bdt_amount);
}

function fn_colorlevel_total(index) //for color level
{

	var filed_value = $("#colSize_"+index).val();
	var placeholder_value = $("#colSize_"+index).attr('placeholder');
	var txt_user_lebel=$('#txt_user_lebel').val();
	var variable_is_controll=$('#variable_is_controll').val();

	if(filed_value*1 > placeholder_value*1)
	{
		// if(variable_is_controll==1 && txt_user_lebel!=2)
		// {
			alert("Qnty Excceded by"+(placeholder_value-filed_value));
			$("#colSize_"+index).val('');
		/*}
		else
		{
			if( confirm("Qnty Excceded by"+(placeholder_value-filed_value)) )
				void(0);
			else
			{
				$("#colSize_"+index).val('');
			}
		}*/
	}

    var totalRow = $("#table_color tbody tr").length;
	//alert(totalRow);
	math_operation( "total_color", "colSize_", "+", totalRow);
	$("#txt_total_issue").val( $("#total_color").val() );
}

	function check_left_over_qty(current_qty) //for gross level
	{

		var prev_qty = $("#txt_total_left_over_issue_hidden").val()*1;
		var balance = $("#txt_yet_to_issue").val()*1;
		var txt_user_lebel=$('#txt_user_lebel').val();
		var leftoverQty = prev_qty + balance;
		// alert(leftoverQty+'=='+current_qty);
		var variable_is_controll=$('#variable_is_controll').val();

		if(current_qty*1 > leftoverQty*1)
		{
			alert("Qnty Excceded by"+(leftoverQty-current_qty));
			$("#txt_total_issue").val('');
		}
	}

	function get_left_over_balance(po_id,country_id,gmts_item,country_maintain,category_id=null)
	{
		var order_type = $('#cbo_order_type').val();
		if (!category_id) {
			category_id = $('#cbo_category_id').val();
		}
		var data = 'po_id='+po_id+'&country_id='+country_id+'&gmts_item='+gmts_item+'&country_maintain='+country_maintain+'&order_type='+order_type+'&category_id='+category_id+'&action=get_left_over_balance';	
		// var data = {"po_id":po_id,"gmts_item":gmts_item,"country_id":country_id,"action":get_left_over_balance};	
		// alert(data);
		$.ajax({
			type:'POST',
			url: 'requires/left_over_garments_issue_controller.php',
			data: data,
			cache: false,
			success: function(response)
			{			
				$("#txt_yet_to_issue").val(response);
			},
			error: function(xhr, status, error) {
	           alert('There are something wrong to get left-over balance.');     
	        }
		});
	}
</script>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;">
	<? echo load_freeze_divs ("../",$permission);  ?>
	<div style="width:930px; float:left" align="center">
        <fieldset style="width:930px;">
        <legend>Production Module</legend>  
            <form name="ironoutput_1" id="ironoutput_1" autocomplete="off" >
  				<fieldset>
                    <table width="100%" border="0">
                        <tr>    
                            <td colspan="3" align="right">System ID</td>
                            <td colspan="3">
                            <input name="txt_system_no" id="txt_system_no" onDblClick="system_number_popup();" placeholder="Browse"  class="text_boxes"  style="width:158px;"  >
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" disabled />
                            </td>
                        </tr>
                        
                        <tr>
                            <td width="110" class="must_entry_caption">Company</td>
                            <td width="180">                                
                            <? 
                            echo create_drop_down( "cbo_company_name", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 $company_cond  order by company_name",'id,company_name', 1, '--- Select Company ---', 0, "load_drop_down( 'requires/left_over_garments_issue_controller', this.value, 'load_drop_down_store_name', 'store_name_td' );load_drop_down( 'requires/left_over_garments_issue_controller', this.value, 'load_drop_down_location', 'location' );get_php_form_data(this.value,'load_variable_settings','requires/left_over_garments_issue_controller');" );
                            ?>
                            <input type="hidden" id="sewing_production_variable" />	                               
                            <input type="hidden" id="country_maintain_variable" />	 
                            <input type="hidden" id="leftover_source" />	 
                            <input type="hidden" id="hidden_po_break_down_id" value="" />
                            <input type="hidden" id="hidden_colorSizeID"  value=""  />
                            
                            <input type="hidden" id="styleOrOrderWisw" /> 
                            <input type="hidden" id="iron_production_variable_rej" />
                            <input type="hidden" id="variable_is_controll" />
                            <input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
                            
                            <input type="hidden" id="hidden_currency_id" />
                            <input type="hidden" id="hidden_exchange_rate" />
                            <input type="hidden" id="hidden_piece_rate" /> 
                            <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">	
                            </td>
                            <td width="110" class="must_entry_caption">Location</td>
                            <td width="180" id="location">
                            <?
                            echo create_drop_down( "cbo_location_name", 170, $blank_array,'', 1, '--- Select Location ---', $selected, "",0,0 );
                            ?>
                            </td>
                            <td width="110" class="must_entry_caption">Issue Date</td>
                            <td width="180"> 
                            <input name="txt_issue_date" id="txt_issue_date" class="datepicker" type="text" value="<? echo date("d-m-Y")?>" style="width:158px;"   />
                            </td>
                        </tr>
                        
                        <tr>
                        	<td width="120" class="must_entry_caption">Goods Type</td>
                            <td> 
                            <?
                            echo create_drop_down( "cbo_goods_type", 170, $goods_type_arr, "", 1, "-- Select Goods Type --", $selected, "", "", "", '', '');
                            ?>
                            </td>

                            <td class="must_entry_caption">Order Type</td>
                            <td>
							<?
                            	echo create_drop_down( "cbo_order_type", 170, $order_source, "", 0, "-- Select --", $selected, "", "", "1,2", "", "");
                            ?>
							</td>
							<td width="120" class="must_entry_caption">Issue Purpose</td>
                            <td> 
                            <?
                            echo create_drop_down( "cbo_issue_purpose", 170, $issue_purpose_arr, "", 1, "-- Select Issue Purpose --", $selected, "issu_purpose(this.value)", "", "", '', '');
                            ?>
                            </td>
                        </tr>
                        
						<tr>
							<td>Party</td>
							<td id="cbo_party_name_td">
                            	<input type="text" name="txt_party_name" id="txt_party_name" class="text_boxes" value="" placeholder="Write" style="width:157px;">
                                <input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes_numeric" value="" style="width:157px;">
							</td>

                            <td class="must_entry_caption">Store Name</td>
                            <td id="store_name_td">                            
                            <? 
                            echo create_drop_down( "cbo_store_name", 170, "$blank_array'", "id,store_name", 1, "-- Select Store --", $selected,"",0,0);
                            ?>
                            </td>
                            <td  id="cbo_pay_term_td">Pay Term</td>
                            <td>
                            <?
                            echo create_drop_down( "cbo_pay_term", 170, $pay_mode, "", 1, "-- Select Pay Term --", $selected, "", "", "1,4", '', '');
                            ?>
                            <input type="text" name="gift_or_others" id="gift_or_others" class="text_boxes" value="" style="display: none; width:157px;">
                            </td>	
						</tr>

                        <tr>
                            <td width="140">Currency</td>
                            <td>
                            <?
                            echo create_drop_down( "cbo_currency_mst", 170, $currency, "", 1, "-- Select --", $selected, "check_exchange_rate(this.value)",0, "1,2","","");
                            ?>
                            <input type="hidden" name="hidden_currency_rate" id="hidden_currency_rate" value="" class="text_boxes" style="width:80px"  />
                            </td> 
                            
                            <td>Challan No</td>
                            <td>
                            <input name="txt_challan_no" id="txt_challan_no" class="text_boxes"  style="width:157px " >
                            </td>
                            <td width="100">Exchange Rate</td>
                            <td width="170">
                            <input name="exchange_rate" id="exchange_rate" class="text_boxes" style="width:158px " disabled>	
                            <input type="hidden" name="exchange_rate_hidden" id="exchange_rate_hidden" class="text_boxes" style="width:160px " disabled>
                            </td>
                        </tr>                        
                        <tr>
                            <td>Remarks</td>
                            <td colspan="5">
                            <input name="txt_remark" id="txt_remark" class="text_boxes"  style="width:97% " >
                            </td>  
                        </tr>
                    </table>
				</fieldset>
				<br />
                <div id="dtls_data" align="center">
                 <table cellpadding="0" cellspacing="1" width="100%">
                    <tr>
                    	<td width="30%" valign="top">
                            <fieldset>
                            <legend>New Entry</legend>
                                 <table  cellpadding="0" cellspacing="2" width="100%">
                                    <tr>
                                        <td>PO No</td> 
                                        <td> 
                                        <input name="txt_order_no" placeholder="Double Click to Search" id="txt_order_no" onDblClick="openmypage();" class="text_boxes" style="width:120px" readonly />
                                        <input type="hidden" id="txt_po_id"  name="txt_po_id"/> 
                                        <input type="hidden" id="hidden_dtls_id"  name="hidden_dtls_id"/> 
                                        <input type="hidden" id="hidden_receive_dtls_id"  name="hidden_receive_dtls_id"/> 
                                        </td>
                                    </tr> 

                                    <tr>
                                    	<td>Buyer</td>
			                            <td id="cbo_buyer_name_td">
			                            <? 
			                            echo create_drop_down( "cbo_buyer_name", 130, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,0 );
			                            ?>
			                            </td>
                                    </tr>

                                    <tr>  
                                        <td>Style Name</td> 
                                        <td> 
                                        <input name="txt_style_name" id="txt_style_name" class="text_boxes"  style="width:120px" disabled  />
                                        </td>
                                    </tr>
                                    <tr>  
                                        <td>Item Name</td> 
                                        <td> 
                                        <?
                                        echo create_drop_down( "cbo_item_name", 132, $garments_item,"", 1, "-- Select Item --", $selected, "",1,0 );	
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>  
                                        <td>Country</td> 
                                        <td> 
                                        <?
                                        echo create_drop_down( "cbo_country_name", 132, "select id,country_name from lib_country","id,country_name", 1, "-- Select Country --", $selected, "",1 );
                                        ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sale Rate</td> 
                                        <td>
                                        <input type="text" name="txt_sale_rate" id="txt_sale_rate" class="text_boxes_numeric" style="width:120px" onBlur="sale_rate_calculator()" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total Issue</td> 
                                        <td>
                                        <input type="text" name="txt_total_issue" id="txt_total_issue" class="text_boxes_numeric" style="width:120px" onBlur="check_left_over_qty(this.value)" disabled/><input type="hidden" name="txt_total_left_over_issue_hidden" id="txt_total_left_over_issue_hidden" class="text_boxes_numeric"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Yet to Issue</td> 
                                        <td>
                                        <input type="text" name="txt_yet_to_issue" id="txt_yet_to_issue" class="text_boxes_numeric" style="width:120px" readonly disabled="" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Category</td> 
                                        <td> 
	                                        <?php
	                                        	$categories = array(1 => 'A', 2 => 'B',3 => 'C',4=>'D');
	                                        	echo create_drop_down( 'cbo_category_id', 132, $categories, '', 1, '-- Select Category --', $selected, '', 1);
	                                        ?> 
                                        </td>
                                    </tr>
                                    <tr>
                                    	<td>Remarks</td> 
                                        <td> 
                                        <input type="text" name="txt_remark2" id="txt_remark2" class="text_boxes" style="width:120px" />
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td width="1%" valign="top"></td>
                        <td width="25%" valign="top">
                            <fieldset>
                            <legend>Display</legend>
                                <table  cellpadding="0" cellspacing="2" width="100%" >
                                	<tr>
                                        <td width="140">Currency</td>
                                        <td>
                                       	 <?
					                        echo create_drop_down( "cbo_currency", 92, $currency, "", 1, "-- Select --", $selected, "",1, "","","");
					                      ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>FOB rate</td>
                                        <td>
                                         <input type="text" name="txt_fob_rate" id="txt_fob_rate" class="text_boxes_numeric" style="width:80px" disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Issue Amount</td>
                                        <td>
                                         <input type="text" name="txt_issue_amount" id="txt_issue_amount" class="text_boxes_numeric" style="width:80px"  disabled  />
                                        </td>
                                    </tr>
                                     <tr>
                                        <td>BDT Amount</td>
                                        <td>
                                         <input type="text" name="txt_bdt_amount" id="txt_bdt_amount" class="text_boxes_numeric" style="width:80px" disabled />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Room</td>
                                        <td>
                                        	<input type="text" name="cbo_room_no" id="cbo_room_no" class="text_boxes_numeric" style="width:80px" disabled/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Rack</td>
                                        <td>
                                        	<input type="text" name="cbo_rack_no" id="cbo_rack_no" class="text_boxes_numeric" style="width:80px" disabled/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Shelf</td>
                                        <td>
                                        	<input type="text" name="cbo_shelf_no" id="cbo_shelf_no" class="text_boxes_numeric" style="width:80px" disabled/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="">Bin</td>
                                        <td>
                                        	<input type="text" name="cbo_bin_no" id="cbo_bin_no" class="text_boxes_numeric" style="width:80px" disabled/>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>	
                        </td>
                        <td width="40%" valign="top" >
                            <div style="max-height:300px; overflow-y:scroll" id="breakdown_td_id" align="center"></div>
                        </td>
                    </tr>
                     <tr>
		   				<td align="center" colspan="9" valign="middle" class="button_container">
							<?
							$date=date('d-m-Y');
                            echo load_submit_buttons( $permission, "fnc_left_over_gmts_input", 0, 0,"reset_form('ironoutput_1','list_view_country','','txt_issue_date,".$date."','childFormReset(1)')",1); 
                            ?>
                            <input value="Print" name="issue_print" onClick="left_over_gmts_issue_print()" style="width:80px" id="issue_print" class="formbutton" type="button">
                            <input value="Print 2" name="issue_print2" onClick="left_over_gmts_issue_print_2()" style="width:80px" id="issue_print2" class="formbutton" type="button">
                            <input type="hidden" name="txt_mst_id" id="txt_mst_id" disabled />
           				</td>
           				<td>&nbsp;</td>					
		  			</tr>
                </table>
                </div>
                <div style="width:930px; margin-top:5px;"  id="list_view_container" align="center"></div>
            </form>
            </fieldset>
    </div>
	<div id="list_view_country" style="width:385px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative; margin-left:10px"></div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>