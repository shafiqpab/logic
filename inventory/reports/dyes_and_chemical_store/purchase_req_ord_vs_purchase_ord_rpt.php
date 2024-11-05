<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Purchase Requisition Order Vs Purchase Order Report
				
Functionality	:	
JS Functions	:
Created by		:   Nayem
Creation date 	:   10-03-2022
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
echo load_html_head_contents("Purchase Requisition Order Vs Purchase Order Report","../../../", 1, 1, $unicode,1,1); 

?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	
	function generate_report(report_type)
	{
		var cbo_company_name = $("#cbo_company_name").val();
		if(cbo_company_name=='')
		{
			alert('Select Company Name');
			return;
		}

		var cbo_item_category = $("#cbo_item_category").val();
		var cbo_req_year = $("#cbo_req_year").val();
		var cbo_req_status = $("#cbo_req_status").val();
		var txt_date_from = $("#txt_date_from").val();
		var txt_date_to = $("#txt_date_to").val();
		var report_title=$( "div.form_caption" ).html(); 
	
		var dataString="&cbo_company_name="+cbo_company_name+"&cbo_item_category="+cbo_item_category+"&cbo_req_year="+cbo_req_year+"&cbo_req_status="+cbo_req_status+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&report_type="+report_type+"&report_title="+report_title;
		var data="action=generate_report"+dataString;
		// alert (data);
		freeze_window();
		http.open("POST","requires/purchase_req_ord_vs_purchase_ord_rpt_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window('+reponse[2]+')" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			
			setFilterGrid("table_body_id",-1,'');
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window(type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$("#table_body_id tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		$("#table_body_id tr:first").show();
		document.getElementById('scroll_body').style.overflow="auto"; 
		document.getElementById('scroll_body').style.maxHeight="250px";
	}

	function fnc_rcv_details(wo_id,prod_id,title,action)
	{
		var page_link='requires/purchase_req_ord_vs_purchase_ord_rpt_controller.php?wo_id='+wo_id+'&prod_id='+prod_id+'&title='+title+'&action='+action;
		// alert(page_link);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=350px,center=1,resize=0,scrolling=0','../../');
	}
</script>
</head>
<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../",$permission);  ?><br />    		 
        <form name="purchaseReqOrdVsPurchaseOrdRpt_1" id="purchaseReqOrdVsPurchaseOrdRpt_1" autocomplete="off" > 
         <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
         <div id="content_search_panel" style="width:800px">      
            <fieldset>  
                <table class="rpt_table" width="800" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="130" class="must_entry_caption">Company</th>
                        <th width="120">Category</th>
                        <th width="100">Req Year</th>
                        <th width="130">Req. Status</th>
                        <th width="180">Req. Date Range</th>
                        <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('purchaseReqOrdVsPurchaseOrdRpt_1','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr class="general">
							<td>
								<? 
									echo create_drop_down( "cbo_company_name", 130, "SELECT id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "","" );
								?>                            
							</td>
							<td id="supplier_td">
								<?php 
									echo create_drop_down( "cbo_item_category", 120, $item_category,"", 1, "-- Select --", $category, "",0,"5,6,7,23" );
								?> 
							</td>
                            <td>
								<?php 
									echo create_drop_down( "cbo_req_year", 80, $year,"", 1, "-- PI Year --", date('Y'), "" );
								?> 
                            </td>
                            <td>
								<?
									$req_status=array(1=>"PO Full Pending",2=>"PO Partial Pending",3=>"PO Full & Partial Pending",4=>"PO Done",5=>"All Req");
									echo create_drop_down( "cbo_req_status", 130, $req_status,"", 0, " All ", 1, "",0,"" );
								?> 
                            </td>
                            <td>
								<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:65px" placeholder="From Date"/>&nbsp;To&nbsp;<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:65px" placeholder="To Date"/>
                            </td>
                            <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
					<tfoot>
						<tr>
                        	<td colspan="6" align="center"><? echo load_month_buttons(1); ?></td>                        	
                        </tr>
					</tfoot>
                </table> 
            </fieldset> 
            </div>
                <div id="report_container" align="center"></div>
                <div id="report_container2"></div> 
        </form>    
    </div>
</body>  
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
<script>
	set_multiselect('cbo_company_name*cbo_item_category','0*0','0*0','0*0','0*0');
</script>
</html>
