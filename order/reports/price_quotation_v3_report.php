<?
/*-------------------------------------------- Comments -----------------------
Purpose			:
Functionality	:
JS Functions	:
Created by		:	MD. REAZ UDDIN
Creation date 	: 	03-07-2018
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
echo load_html_head_contents("Work Order [Booking] Report", "../../", 1, 1,$unicode,1,1);
?>	
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';	

	var tableFilters = 
	{
		//col_15: "select",
		col_operation: {
			id: ["value_total_wo_qnty","value_total_wo_amnt"],
		    col: [9,10],
		    operation: ["sum","sum"],
		    write_method: ["innerHTML","innerHTML"]
		}
		
	} 
	
	function fn_report_generated(operation)
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		else
		{	
			var report_title=$( "div.form_caption" ).html();
			var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_id*cbo_buyer_id*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
			//alert(data);return;
			freeze_window(3);
			http.open("POST","requires/price_quotation_v3_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
	
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("**");
			//var tot_rows=reponse[2];
			$('#report_container4').html(reponse[0]);
			document.getElementById('report_container3').innerHTML=report_convert_button('../../');
			document.getElementById('report_container3').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px;"/>';
			
	 		show_msg('3');
			setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
		}
	}
	function price_quotation_print( pric_quo_data )
	{
		var data = pric_quo_data.split("_");
		var datas = data[0] + '*' + data[1]  + '*' + data[2];
		window.open("requires/price_quotation_v3_report_controller.php?data=" + datas + '&action=top_botton_report', true);return;
	}
	
	//function new_window(html_filter_print)
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tbody tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container4').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		$('#table_body tbody tr:first').show();
		
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//if(html_filter_print*1>1) $("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container4').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";
		
		//if(html_filter_print*1>1) $("#table_body tr:first").show();*/
	}	
	
	
	
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>    		 
    <form name="priceQuotReport_1" id="priceQuotReport_1" autocomplete="off" > 
    <h3 style="width:750px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:750px;">
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <tr>
                        <th width="120" class="must_entry_caption">Company Name</th>
                        <th width="120">Buyer</th>
                        <th width="150">Date Range</th>
                        <th> <input type="reset" id="reset_btn" class="formbutton" style="width:90px" value="Reset" onClick="reset_form('priceQuotReport_1','report_container3', '','','');reset_form('priceQuotReport_1','report_container4', '','','')" /> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td> 
                        <?
                        echo create_drop_down( "cbo_company_id", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down('requires/price_quotation_v3_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td');" );
                        ?>
                        </td>
                        <td id="buyer_td">
                        <? 
                        echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "--Select Buyer--", $selected, "","","" );
                        ?>
                        </td>
                        <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" value="<? echo date("d-m-Y");?>"> To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"  value="<? echo date("d-m-Y");?>">
                        </td>
                        <td align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:90px" value="All" onClick="fn_report_generated(1)" />
                        <input type="button" id="show_button" class="formbutton" style="width:90px" value="Confirmed" onClick="fn_report_generated(2)" />
                        <input type="button" id="show_button" class="formbutton" style="width:90px" value="Pending" onClick="fn_report_generated(3)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="10" align="center"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </tbody>
            </table> 
        	</fieldset>
        </div>
        <div id="report_container3" align="center"></div>
        <div id="report_container4" align="center"></div>
    </form> 
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script>//set_multiselect('cbo_wo_type','0','0','','0');</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
