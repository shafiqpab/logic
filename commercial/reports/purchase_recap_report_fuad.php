<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Purchase Recap Report.
Functionality	:	
JS Functions	:
Created by		:	Fuad 
Creation date 	: 	18-06-2013
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Purchase Recap Report", "../../", 1, 1,'','','');
?>	

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
 
	var permission = '<? echo $permission; ?>';
	
	function generate_report()
	{
		if(form_validation('cbo_company_name*cbo_item_category_id','Company Name*Item Category')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();	
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_issuing_bank*cbo_item_category_id*cbo_lc_type_id*txt_date_from*txt_date_to*txt_maturity_date_from*txt_maturity_date_to*txt_date_from_btb*txt_date_to_btb',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/purchase_recap_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			var item_cat=$('#cbo_item_category_id').val();
			if(item_cat==1)
			{
				document.getElementById('report_container').innerHTML+='&nbsp;&nbsp;<a href="requires/'+response[2]+'" style="text-decoration:none"><input type="button" value="Excel Short Preview" name="excel" id="excel" class="formbutton" style="width:130px"/></a>';
			}
			
			/*var tableFilters = { 
				col_0: "none", 
				col_operation: {
					id: ["value_total_opening_balance","value_total_purchase","value_total_inside_return","value_total_outside_return","value_total_rcv_loan","value_total_total_rcv","value_total_issue_inside","value_total_issue_outside","value_total_receive_return","value_total_issue_loan","value_total_total_delivery","value_total_stock_in_hand","value_total_alocatted","value_total_free_stock"],
					col: [8,9,10,11,12,13,14,15,16,17,18,19,20,21],
					operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
					write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
				}
			}*/
			
			setFilterGrid("table_body",-1);
			
			show_msg('3');
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');// media="print"
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="300px";
		$("#table_body tr:first").show();

	}
	
	function show_qty_details(pi_id,category_id)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/purchase_recap_report_controller.php?pi_id='+pi_id+'&category_id='+category_id+'&action=receive_qnty', "Receive Wise Quantity", 'width=550px,height=330px,center=1,resize=0,scrolling=0','../');
	}	

</script>

</head>

<body onLoad="set_hotkey();">
<form id="PurchaseRecap_report">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <h3 align="left" id="accordion_h1" style="width:1090px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:1090px;">
                <table class="rpt_table" width="1080" border="1" rules="all" cellpadding="1" cellspacing="2" align="center">
                	<thead>
                   		<tr>                    
                            <th class="must_entry_caption">Company Name</th>
                            <th>Issuing Bank</th>
                            <th>PI Date</th>
                            <th>Maturity Date</th>
                            <th>BTB Date</th>
                            <th class="must_entry_caption">Item Category</th>
                            <th>LC Type</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('PurchaseRecap_report','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general">
                            <td> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
                                ?>
                            </td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_issuing_bank", 130, "select id,bank_name from lib_bank where issusing_bank=1 and status_active=1 and is_deleted=0  order by bank_name","id,bank_name", 1, "--Select Bank--", $selected, "" );
                                ?>                            
                           	</td>
                            <td align="center">
                             <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        	</td>
                            <td align="center">
                             <input type="text" name="txt_maturity_date_from" id="txt_maturity_date_from" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_maturity_date_to" id="txt_maturity_date_to" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        	</td>
                            <td align="center">
                             <input type="text" name="txt_date_from_btb" id="txt_date_from_btb" value="" class="datepicker" style="width:60px" placeholder="From Date"/>
                             To
                             <input type="text" name="txt_date_to_btb" id="txt_date_to_btb" value="" class="datepicker" style="width:60px" placeholder="To Date"/>
                        	</td>
                            <td align="center">
                                <? echo create_drop_down( "cbo_item_category_id", 130, $item_category,'', 1, '-- Select --',0,"",0); ?>
                            </td>
                            <td>
                                <? echo create_drop_down( "cbo_lc_type_id",90,$lc_type,'',1,'-- All Type --',0,"",0); ?>  
                            </td>
                           <td>
                                <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                        <tr>
                        	<td colspan="8" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
    
    <div style="margin-top:10px" id="report_container" align="center"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
