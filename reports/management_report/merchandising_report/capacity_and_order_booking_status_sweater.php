<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 
Functionality	         :
JS Functions	         :
Created by		         :	Kamrul Hasan
Creation date 	         : 17-02-2024
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
Comments		         : 

*/
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../../", 1, 1,$unicode,1,1);
$user_id=$_SESSION['logic_erp']['user_id'];

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";



    function generate_report(rpt_type)
	{
		freeze_window(3);
		if( form_validation('cbo_company_name*txt_job','Company Name*Job No')==false )
        {
            release_freezing();
            return;
        }
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style*txt_job*cbo_date_type*txt_date_from*txt_date_to*cbo_year_selection',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		
		http.open("POST","requires/capacity_and_order_booking_status_sweater_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid("table_body_id",-1,'');
			show_msg('3');
			release_freezing();
			//setFilterGrid("table_body_id",-1,tableFilters2);
			//setFilterGrid("table_body_ids",-1,tableFilters3);
		}
	}

    function new_window(type)
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		//$('#table_body_id tr:first').hide();
		//$('.delivery_challan').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//$('#table_body_id tr:first').show();
		//$('.delivery_challan').show();
		//document.getElementById('scroll_body').style.overflow="auto"; 
		//document.getElementById('scroll_body').style.maxHeight="250px";
	}

</script>
</head>
<body onLoad="set_hotkey(); fnc_brandload();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../../");  ?>
        <form name="shipmentschedule_1" id="shipmentschedule_1" autocomplete="off" >
            <h3 style="width:800px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" >
                <fieldset style="width:800px;">
                    <table class="rpt_table" width="700" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <tr>
                                <th width="100">Company</th>
                                <th width="100">Buyer</th>
                                <th width="100">Style</th>
                                <th width="100">Job</th>
                                <th width="100">Date Category</th>
                                <th width="100" colspan="2">Date</th>
                                <th width="100"><input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" />
                                    <input type="hidden" id="report_ids">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",0, "--Select Company--", $selected); ?></td>

                                <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, " load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_season', 'season_td');" ); ?></td>
                                <td><input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:100px" placeholder="Write" /></td>
                                <td><input type="text" name="txt_job" id="txt_job" class="text_boxes" style="width:100px" placeholder="Write" /></td>
                                <td>
                                    <select name="cbo_date_type" id="cbo_date_type"  style="width:100px" class="combo_boxes">
                                        <option value="1">Country Ship Date</option>
                                        <option value="2">Publish Shipment Date</option>
                                        <option value="3">Original Shipment Date</option>
                                        <option value="4">PO Insert date</option>
                                    </select>
                                </td>
                                <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:50px" placeholder="From"></td>
                                <td>
                                    <input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:50px" placeholder="To">
                                </td>
                                <td>
                                    <input type="button" name="show" id="show" value="Show" onClick="generate_report(1);" style="width:60px;" class="formbutton" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="10" align="center">
                                    <?=load_month_buttons(1); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </div>
        </form>
        <div id="report_container" align="center"></div>
        <div id="report_container2">
    </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>