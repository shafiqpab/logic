<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:	
JS Functions	:
Created by		:	zakaria 
Creation date 	: 	24-10-2018
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
echo load_html_head_contents("Sample Info","../../../", 1, 1, $unicode);
?>	
<script> 
	var permission = '<? echo $permission; ?>';	
		
	function fn_report_generated()
	{
		var statement = $("#statement").is(':checked') ? 1 : 0;
		/*if (form_validation('cbo_company_name*cbo_buyer_name','Plsease Select Comapny*Plsease Select Buyer')==false)
		{
			return;
		}
		else
		{
		*/	
			//eval(get_submitted_variables('cbo_company_name*cbo_buyer_name*cbo_search_by*txt_search_text'));
			 
			var data="action=report_generate&statement="+statement+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_text*txt_date_from*txt_date_to*cbo_date_type*cbo_year_selection',"../../../");
			//alert(data);
			freeze_window();
			http.open("POST","requires/woven_price_quotation_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		/*}*/
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("****");
			//alert(http.responseText);
			$('#data_panel').html( '<br><b>Convert To </b><a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#data_panel').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );		
			$('#report_container').html(reponse[0]);
			var tableFilters = { 
									col_0: "none",
									col_10: "select", 
									display_all_text: "- All -",
								}
			setFilterGrid("report_tbl",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('report_div').style.overflow="auto";
		document.getElementById('report_div').style.maxHeight="none";
		
		$("#report_tbl tr:first").hide();
		 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('report_div').style.overflowY="scroll";
		document.getElementById('report_div').style.maxHeight="350px";
		
		$("#report_tbl tr:first").show();
	}	

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
<form id="Price_Quotation_Statment">
    <div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <h3 style="width:890px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" > 
            <fieldset style="width:890px;">
                <table class="rpt_table" width="890px" cellpadding="1" cellspacing="0" rules="all" border="1">
                    <thead>
                    	<tr>
                    		<th colspan="6"><input type="checkbox" name="statement" value="statement" id="statement" checked> Include Closed Statement</th>
                    	</tr>
                        <tr>
	                        <th width="160">Company</th>
	                        <th width="160">Buyer</th>                  	
	                        <th id="search_text_td">Style reference</th>
	                        <th>Type</th>
	                        <th class="captionTxt">Est. Ship Date</th>
	                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td> 
							<?
                           	 echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/woven_price_quotation_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
							<? 
                            	//echo create_drop_down( "cbo_buyer_name", 160, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            	//echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );  
                            	echo create_drop_down( "cbo_buyer_name", 160, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                            ?>
                        </td>
                        <td>
                        	<input type="text"  id="txt_search_text" class="text_boxes" style="width:140px">
                        </td>
                        <td>
							<? 
                            	echo create_drop_down( "cbo_date_type", 120,array(1=>'Est. Ship Date',2=>'Price Quotation Entry Date Wise'),"", 0, "-- Type --", $selected, "$('.captionTxt').text(this.options[this.selectedIndex].text);",0,"" );
                            ?>
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  ></td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table> 
            <br /> 
            </fieldset>
        </div>
    </div>
    <div id="data_panel" align="center"></div>
    <div id="report_container" align="center"></div>
</form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>