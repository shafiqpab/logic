<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Import Report.
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	2-2-2021
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

//--------------------------------------------------------------------------------
echo load_html_head_contents('Report Page', '../../', 1, 1, $unicode, 1, '', '');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';
	function openmypage_Invoice()
	{
		var cbo_country_id = document.getElementById('cbo_company_name').value;
		var cbo_type_name = document.getElementById('cbo_type_name').value;
		
		if(form_validation('cbo_type_name','C&F Type')==false ){
			return;
		}

		var page_link='requires/cnf_bill_report_controller.php?action=invoice_popup_search&cbo_type_name='+cbo_type_name+'&cbo_country_id='+cbo_country_id;
		var title='Invoice Number';


		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
		
				var theform=this.contentDoc.forms[0];
				var company_id=this.contentDoc.getElementById("company_id").value;
				var invoice_no=this.contentDoc.getElementById("invoice_no").value;
				// var buyer_name=this.contentDoc.getElementById("buyer_name").value;
				$('#cbo_company_name').val( company_id );
				// $('#cbo_buyer_name').val( buyer_name );
				$('#txt_invoice_no').val( invoice_no );
				//  release_freezing();
		}
	}

	function generate_report()
	{		
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_buyer_name   = $("#cbo_buyer_name").val();
		var cbo_type_name    = $("#cbo_type_name").val();
		var cbo_candf_name   = $("#cbo_candf_name").val();
		var txt_invoice_no   = $("#txt_invoice_no").val();
		var txt_bill_no      = $("#txt_bill_no").val();
		var cbo_based_on     = $("#cbo_based_on").val();
		var txt_date_from    = $("#txt_date_from").val();
		var txt_date_to      = $("#txt_date_to").val();

		if(form_validation('cbo_company_name*cbo_type_name','Company*C&F Type')==false){
					return;
				}
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_buyer_name*cbo_type_name*cbo_candf_name*txt_invoice_no*txt_bill_no*cbo_based_on*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;

		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/cnf_bill_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container').html(response[0]);
			document.getElementById('report_container2').innerHTML='<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			 document.getElementById('report_container2').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			// setFilterGrid('table_body',-1);
			show_msg('3');
			release_freezing();
		}
	}
	function new_window()
	{
		document.getElementById('table_body').style.overflow='auto';
		document.getElementById('table_body').style.maxHeight='none';
		// $('#table_body tbody').find('tr:first').hide();
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('table_body').style.overflowY='scroll';
		document.getElementById('table_body').style.maxHeight='300px';
	}
</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="reportpage1" name="reportpage1">
			<div style="width:1020px;">
				<h3 align="left" id="accordion_h1" style="width:1020px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:960px;">
					<table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
                    <th>Buyer Name</th>
                    <th class="must_entry_caption">C&F Type</th>
                    <th>C&F Name</th>
                    <th>Invoice No</th>
                    <th>Bill NO</th>
                    <th>Based On </th>
                    <th colspan="2">Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="id_field" id="id_field" value="" /></th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
					<? echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and comp.core_business in(1,3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"",'' ); ?>
                </td>
        		<td id="buyer_pop_td">
				<?
				echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
				?>
				</td>
        		<td>
				<?
                echo create_drop_down( "cbo_type_name",100,array(1=>"Export",2=>"Import"),'',1,'--Select--',$cbo_type_name,"",'');
                ?>
                <td>
				<?
                echo create_drop_down( "cbo_candf_name",100,"select a.id, a.supplier_name FROM lib_supplier a , lib_supplier_party_type b WHERE a.id= b.supplier_id and b.party_type=30 and a.STATUS_ACTIVE=1 AND a.IS_DELETED=0","ID,supplier_name", 1, "-- Select --", 0, "" );
                ?>
				</td>                
                <td>
				<input type="text" name="txt_invoice_no" id="txt_invoice_no" style="width:120px" class="text_boxes" placeholder="Double Click to Search" onDblClick="openmypage_Invoice()" autocomplete="off"/>
				</td>
                <td ><input type="text" name="txt_bill_no" id="txt_bill_no" style="width:80px" class="text_boxes" ></td>
                <td>
				<?
                echo create_drop_down( "cbo_based_on",100,array(1=>"Invoice Date",2=>"Bill Date"),'',1,'--Select--',0,"",0);
                ?>
				</td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="generate_report()" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
					
					</fieldset>
				</div>
			</div>
			<br/>
			<div id="report_container2" align="center"></div><br/>
			<div id="report_container"></div>
		</form>
	</div>
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
