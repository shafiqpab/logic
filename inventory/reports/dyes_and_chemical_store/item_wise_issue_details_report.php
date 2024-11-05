<?
/*-------------------------------------------- Comments
Purpose			: 	This form will generate Item category wise Dayes and Chemical issue quanty, and amount  Summery Report

Functionality	:
JS Functions	:
Created by		:	Mohammad Shafiqur Rahman
Creation date 	: 	07-10-2018
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
echo load_html_head_contents("Item Wise Issue Details Report","../../../", 1, 1, $unicode,1,1);


?>
<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	function generate_report()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		var cbo_company_name = $("#cbo_company_name").val();
		var cbo_item_category_id = $("#cbo_item_category_id").val();
		var item_group_id = $("#txt_item_group_id").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();


		var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_item_category_id="+cbo_item_category_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&report_title="+report_title;
		var data="action=generate_report"+dataString;
		//alert (data);
		freeze_window(3);
		http.open("POST","requires/item_wise_issue_details_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			$("#report_container2").html(reponse[0]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body_id",-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window(type)
	{
		if(type == 1)
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			$('#table_body_id tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			$('#table_body_id tr:first').show();
			$('#rpt_table_header tr th:last').attr('width', '');
			$('#table_body_id tr td:last').attr('width', '');
			$('#table_body_footer tr th:last').attr('width', '');
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
		else
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="250px";
		}
	}

	function issue_qnty_dtls(prod_id, transaction_date, issue_purpose, action)
    {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/item_wise_issue_details_report_controller.php?action='+action+'&prod_id='+prod_id+'&transaction_date='+transaction_date+'&issue_purpose='+issue_purpose, 'Issue Quantity Details', 'width=710, height=400px, center=1,resize=0,scrolling=0','../../');
		
    }

</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />
    	<form name="item_wise_issue_details_rpt_form" id="item_wise_issue_details_rpt_form" autocomplete="off" >
    		<h3 style="width:740px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
     		<div id="content_search_panel" style="width:745px">
	        	<fieldset>
	            	<table class="rpt_table" width="740" cellpadding="0" cellspacing="0" border="1" rull="all">
	                	<thead>
	                    	<th width="200" class="must_entry_caption">Working Company</th>
	                    	<th width="200">Item Category</th>
	                    	<th width="120">From Date</th>
	                    	<th width="120">To Date</th>
	                    	<th>
	                    		<input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('item_wise_issue_details_rpt_form','report_container*report_container2','','','')" /></th>
	                	</thead>
	                	<tbody>
	                    	<tr class="general">
	                        	<td>
	                            <?
	                                echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "", $selected, "" );
	                            ?>
	                        	</td>
	                       		<td id="cat_td">
								<?php
									echo create_drop_down( "cbo_item_category_id", 200,$item_category,"", 0, "", $selected, "","","5,6,7,19,20,22,23,39","","","");
	                            ?>
	                      		</td>
	                    		<td>
	                    			<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:100px;"/>
	                    		</td>
	                        	<td>
	                            	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:100px;"/>
	                        	</td>
	                    		<td>
	                            	<input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:80px" class="formbutton" />
	                        	</td>
	                    	</tr>
	                	</tbody>
	                	<tfoot>
		                    <tr>
		                        <td colspan="5" align="left">
		                        	<? echo load_month_buttons(1);  ?>&nbsp;&nbsp;
		                        </td>
		                    </tr>
		                </tfoot>
		            </table>
		        </fieldset>
    		</div>
            <div id="report_container" align="center" style="width:1150px;"></div>
            <div id="report_container2"></div>
    	</form>
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	set_multiselect('cbo_item_category_id','0','0','','0');
</script>
</html>
