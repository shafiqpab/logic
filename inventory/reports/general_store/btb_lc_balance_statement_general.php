<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create BTB Lc Balance Statement General
				
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	05-11-2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("BTB Lc Balance Statement General","../../../", 1, 1, $unicode,1,1); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


var tableFilters = { 
	col_20: "none", 
	col_operation: {
		id: ["value_total_pi_qnty","value_total_rcv_qnty","value_total_rcv_return_qnty","value_total_balance_qty","value_total_rcev_value","value_total_bal_value"],
		col: [9,10,11,12,13,14],
		operation: ["sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
}

var tableFilters2 = { 
	col_30: "none", 
	col_operation: {
		id: ["value_total_pi_value","value_total_pi_item_qtny","value_total_pi_item_value","value_total_rcv_btb_qnty","value_total_rtn_btb_qnty","value_total_balance_qnty","value_total_rcv_value","value_total_balance_value"],
		col: [6,15,17,18,19,20,21,22],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
	}
}



function openmypage_pi()
{
	var companyID = $('#cbo_company_name').val();
	var supplierID = $('#cbo_suppler_name').val();
	var btbLc_id = $('#btbLc_id').val();
	
	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/btb_lc_balance_statement_general_controller.php?action=pi_searce_popup&companyID='+companyID+'&supplierID='+supplierID+'&btbLc_id='+btbLc_id;
	var title='Yarn Requisition Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var pi_id=this.contentDoc.getElementById("pi_id").value;
		var pi_no=this.contentDoc.getElementById("pi_no").value;
		
		$('#pi_id').val(pi_id);
		$('#txt_pi_no').val(pi_no);
	}
}

function openmypage_btbLc()
{
	var companyID = $('#cbo_company_name').val();
	var supplierID = $('#cbo_suppler_name').val();
	var pi_id = $('#pi_id').val();

	if (form_validation('cbo_company_name','Company')==false)
	{
		return;
	}
	
	var page_link='requires/btb_lc_balance_statement_general_controller.php?action=btbLc_popup&companyID='+companyID+'&supplierID='+supplierID+'&pi_id='+pi_id;
	var title='Yarn Requisition Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var btbLc_id=this.contentDoc.getElementById("btbLc_id").value;
		var btbLc_no=this.contentDoc.getElementById("btbLc_no").value;
		
		$('#btbLc_id').val(btbLc_id);
		$('#txt_btbLc_no').val(btbLc_no);
	}
}


function generate_report(rpt_type)
{
	var btbLcNO = $('#txt_btbLc_no').val();
	var supplierID = $('#cbo_suppler_name').val();
	var pi_id = $('#pi_id').val();
	var btbLc_id = $('#btbLc_id').val();
	var receiving_status = $('#cbo_receiving_status').val();
	if(supplierID==0 &&  btbLc_id=="")
	{
		
		if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
		{
			return;
		}
	}
	else
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
	}
	
	var report_title=$( "div.form_caption" ).html();
	var data="action=report_generate"+get_submitted_data_string('cbo_item_category*cbo_company_name*cbo_suppler_name*pi_id*btbLc_id*txt_date_from_pi*txt_date_to_pi*txt_date_from*txt_date_to*cbo_receiving_status',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
	//alert(data);return;
	freeze_window(3);
	http.open("POST","requires/btb_lc_balance_statement_general_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
	if(http.readyState == 4) 
	{	 
 		var reponse=trim(http.responseText).split("####");
		$("#report_container2").html(reponse[0]);   
		document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
		//alert(reponse[1]);
		if(reponse[2]==1)
		{
			
			setFilterGrid("table_bodyy",-1,tableFilters);
		}
		else
		{
			setFilterGrid("table_body",-1,tableFilters2);
		}
		
		
		show_msg('3');
		release_freezing();
	}
} 
 

function new_window()
{
	document.getElementById('caption').style.visibility='visible';
	document.getElementById('scroll_body').style.overflow="auto";
	document.getElementById('scroll_body').style.maxHeight="none";
	$('#table_bodyy tbody tr:first').hide();
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
	$('#table_bodyy tbody tr:first').show();
	document.getElementById('scroll_body').style.overflowY="scroll";
	document.getElementById('scroll_body').style.maxHeight="350px";
	document.getElementById('caption').style.visibility='hidden';
	d.close(); 
}



function openmypage_popup(company_id,pi_id,item_category_id,page_title,action)
{
	var width='';
	if(action=='receive_popup')
	{
		width ='1070px';
	}
	else if(action=='payable_popup')
	{
		width ='1070px';
	}
	else
	{
		width ='820px';
	}
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/btb_lc_balance_statement_general_controller.php?company_id='+company_id+'&pi_id='+pi_id+'&item_category_id='+item_category_id+'&action='+action, page_title, 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
}

function openmypage_mrr(company_id,pi_id,count_id,composition_id,type_id,color_id,page_title,action)
{
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/btb_lc_balance_statement_general_controller.php?company_id='+company_id+'&pi_id='+pi_id+'&count_id='+count_id+'&composition_id='+composition_id+'&type_id='+type_id+'&color_id='+color_id+'&action='+action, page_title, 'width=690px,height=400px,center=1,resize=0,scrolling=0','../../');
}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="left">
	<? echo load_freeze_divs ("../../../",$permission); ?><br />    		 
    <form name="btbLcBalanceStatement_1" id="btbLcBalanceStatement_1" autocomplete="off" > 
        <div style="width:100%;" align="center">
            <fieldset style="width:900px;">
            <legend>Search Panel</legend> 
                <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr> 	 	
                            <th width="150" class="must_entry_caption" style="display:none">Item Category</th>
                            <th width="160" class="must_entry_caption">Company</th>                                
                            <th width="160">Supplier</th>
                            <th width="110" style="display:none">PI No.</th>
                            <th width="120">BTB LC No.</th>
                            <th width="150" class="must_entry_caption" style="display:none">PI Date Range</th>
                            <th width="160" class="must_entry_caption">LC Date Range</th>
                            <th width="120">Receiving Status</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('btbLcBalanceStatement_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td style="display:none"> 
                            <?
								//create_drop_down($field_id, $field_width, $query, $field_list, $show_select, $select_text_msg="", $selected_index="", $onchange_func="", $is_disabled="", $array_index="", $fixed_options="", $fixed_values="", $not_show_array_index="", $tab_index="", $new_conn="", $field_name="", $additionalClass="", $additionalAttributes="")
                                echo create_drop_down( "cbo_item_category", 150, $general_item_category,"", 1, "-- Select Category --",0,"",0,"","","","" );
                            ?>
                        </td>
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "reset_form('','','txt_btbLc_no*btbLc_id','','',''); load_drop_down( 'requires/btb_lc_balance_statement_general_controller', this.value, 'load_drop_down_supplier', 'supplier_td' );" );
                            ?>                            
                        </td>
                        <td id="supplier_td"> 
                            <?
                                echo create_drop_down( "cbo_suppler_name", 150, $blank_array,"", 1, "-- Select Supplier --", 0, "" );
                            ?>
                        </td>
                        <td style="display:none">
                            <input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Double Click To Search" onDblClick="openmypage_pi()" readonly />
                            <input type="hidden" id="pi_id" name="pi_id" readonly /> 
                        </td>
                        <td>
                            <input type="text" id="txt_btbLc_no" name="txt_btbLc_no" class="text_boxes" style="width:100px" placeholder="Double Click To Search" onDblClick="openmypage_btbLc()" readonly />
                            <input type="hidden" id="btbLc_id" name="btbLc_id" readonly /> 
                        </td>
                        <td style="display:none">
                            <input name="txt_date_from_pi" id="txt_date_from_pi" class="datepicker"  style="width:55px" placeholder="From Date" readonly>
                    		<input name="txt_date_to_pi" id="txt_date_to_pi" class="datepicker" style="width:55px"placeholder="To Date" readonly>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:55px" placeholder="From Date" readonly>
                    		<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"placeholder="To Date" readonly>
                        </td>
                        <td> 
                            <?
							$receive_status=array(1=>"Full Pending",2=>"Pertial Received",3=>"Fully Received",4=>"Full Pending And Pertial Received");
                            echo create_drop_down( "cbo_receiving_status", 110, $receive_status,"", 1, "-- Select Status --", 0, "" );
                            ?>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" valign="bottom"><? echo load_month_buttons(1);  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <!--<input type="button" name="search_pipe" id="search_pipe" value="Pipe Line" onClick="generate_report(2)" style="width:90px" class="formbutton" />-->
                        </td>
                    </tr>
                </table> 
            </fieldset>  
            
            <div id="report_container" align="center"></div>
        	<div id="report_container2"></div>
            
        </div>
    </form>    
</div>    
</body>  
<script>
	//set_multiselect('cbo_suppler_name','0','0','','0');
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
