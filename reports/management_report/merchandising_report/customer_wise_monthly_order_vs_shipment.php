<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Shipment Schedule Report
Functionality	         :
JS Functions	         :
Created by		         :	Shakil Ahmed Setu
Creation date 	         :	23-02-2021
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         : 	
Update date		         : 	
QC Performed BY	         :
QC Date			         :
Comments		         : From this version oracle conversion is start
							Update description(Create New Button Short)
						 	Report short button not Screen Release--I have checked 71-75 no Line=Aziz

*/
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../../", 1, 1,$unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_31: "select",
		display_all_text:'Show All',
		col_operation: {
		id: ["total_order_qnty_pcs","total_order_qnty","value_total_order_value","total_ex_factory_qnty","total_short_access_qnty","value_total_short_access_value","value_yarn_req_tot"],
		col: [19,20,23,30,32,33,34],
		operation: ["sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report_main(rpt_type)
	{
		freeze_window(3);
		if(rpt_type==1)
		{
			if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
			{
				release_freezing();
				return;
			}
		}
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_location_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data); release_freezing(); return;
		
		http.open("POST","requires/customer_wise_monthly_order_vs_shipment_controller.php",true);
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
		
			show_msg('3');
			release_freezing();
			setFilterGrid("table_body",-1,tableFilters);
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#scroll_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}

	/*function percent_set()
	{
		var tot_row=document.getElementById('tot_row').value;
		var tot_value_js=document.getElementById('total_value').value;
		for(var i=1;i<tot_row;i++)
		{
			var value_js=document.getElementById('value_'+i).value;
			var percent_value_js=((value_js*1)/(tot_value_js*1))*100
			document.getElementById('value_percent_'+i).innerHTML=percent_value_js.toFixed(2);
		}
	}*/

	

</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    <form name="shipmentschedule_1" id="shipmentschedule_1" autocomplete="off" >
        <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:800px;">
            <table class="rpt_table" width="800" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="150" class="must_entry_caption">Company</th>
                        <th width="150">Location</th>
                        <th width="150">Customer/Buyer</th>
                        <th width="200" class="must_entry_caption" colspan="2">Publish Ship Date</th>
                        
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" />
                        	
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><?=create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, " load_drop_down( 'requires/customer_wise_monthly_order_vs_shipment_controller', this.value, 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/customer_wise_monthly_order_vs_shipment_controller', this.value, 'load_drop_down_location', 'location_id_td' )" ); ?></td>

                    <td id="location_id_td"><? 
						echo create_drop_down( "cbo_location_id", 150,array(),"", 0, "--All-", 1,"" ); ?></td>


                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 150, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, "" ); ?></td>


                    <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:95px" placeholder="From"></td>
                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:95px" placeholder="To"></td>
                    
                    <td><input type="button" name="search1" id="search1" value="Show" onClick="generate_report_main(1);" style="width:80px;" class="formbutton" /></td>
                </tr>
                <tr>
                    <td colspan="10" align="center">
						<?=load_month_buttons(1); ?>
                    </td>
                    
                </tr>
            </table>
        </fieldset>
        </div>
        </form>
           <div id="report_container" align="center"></div>
           <div id="report_container2">
       </div>
    </div>
</body>
<script>
	//set_multiselect('cbo_shipment_status','0','0','','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>