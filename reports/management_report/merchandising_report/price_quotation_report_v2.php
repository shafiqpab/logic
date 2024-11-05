<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Woven Garments Price Quotation Entry.
Functionality	:
JS Functions	:
Created by		:	Aziz
Creation date 	: 	25-01-2021
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

	function generate_report(type,qid,companyid,buyer,style,date)
	{
		console.log(type);
	    if (qid == ''){
	    	alert("Quotation id can not be null");
	        return;
	    }
	    else
	    {
	        var zero_val=0;
	        if(type=='preCostRpt' || type=='preCostRpt2')
	        {
	            var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
	            if (r==true) zero_val="1"; else zero_val="0";
	        }

	        var data="action=generate_report&type="+type+"&zero_value="+zero_val+"&txt_quotation_id="+qid+"&cbo_company_name="+companyid+"&cbo_buyer_name="+buyer+"&txt_style_ref="+style+"&txt_quotation_date="+date+'&path=../../';
	        //alert(data);
	        http.open("POST","../../../order/woven_order/requires/quotation_entry_controller.php",true);
	        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	        http.send(data);
	        http.onreadystatechange = fnc_generate_report_reponse;
	    }
	}

	function fnc_generate_report_reponse(){
	    if(http.readyState == 4){
	        $('#data_panel_report').html( http.responseText );
	        var w = window.open("Surprise", "_blank");
	        var d = w.document.open();
	        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    '<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel_report').innerHTML+'</body</html>');
	        d.close();
	    }
	}

	function fn_report_generated()
	{
		/*if (form_validation('cbo_company_name*cbo_buyer_name','Plsease Select Comapny*Plsease Select Buyer')==false)
		{
			return;
		}
		else
		{
		*/
			//eval(get_submitted_variables('cbo_company_name*cbo_buyer_name*cbo_search_by*txt_search_text'));

			var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_search_text*txt_date_from*txt_date_to*cbo_date_type*txt_quotation_id*cbo_approval_type',"../../../");
			//alert(data);
			freeze_window();
			http.open("POST","requires/price_quotation_report_controller_v2.php",true);
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
			
			setFilterGrid("report_tbl2",-1);
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
		$("#report_tbl2 tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();

		document.getElementById('report_div').style.overflowY="scroll";
		document.getElementById('report_div').style.maxHeight="350px";

		$("#report_tbl tr:first").show();
		$("#report_tbl2 tr:first").show();
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
        <h3 style="width:1050px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1050px;">
                <table class="rpt_table" width="1050px" cellpadding="1" cellspacing="0" rules="all" border="1">
                    <thead>
                        <th width="160">Company</th>
                        <th width="160">Buyer</th>
                        <th width="60">Quotation Id</th>
                        <th id="search_text_td" width="120">Style reference</th>
                        <th width="80">Type</th>
                          <th>Approval Type</th>
                        <th class="captionTxt" width="220">Est. Ship Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </thead>
                    <tr class="general">
                        <td>
							<?
                           	 echo create_drop_down( "cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/price_quotation_report_controller_v2',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
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
                        	<input type="text"  id="txt_quotation_id" class="text_boxes" style="width:60px">
                        </td>
                        <td>
                        	<input type="text"  id="txt_search_text" class="text_boxes" style="width:120px">
                        </td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_date_type", 120,array(1=>'Est. Ship Date',2=>'Price Quotation Entry Date Wise'),"", 0, "-- Type --", $selected, "$('.captionTxt').text(this.options[this.selectedIndex].text);",0,"" );
                            ?>
                        </td>
                       <td> 
                       <? $pre_cost_approval_type=array(0=>"All",2=>"Un-Approved",1=>"Approved");
                            echo create_drop_down( "cbo_approval_type", 100, $pre_cost_approval_type,"", 0, "", 1,"","", "" );?></td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
                        </td>
                        <td>
                        	<input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated()" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="7"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
            <br />
            </fieldset>
        </div>
    </div>
    <div id="data_panel" align="center"></div>
    <div id="report_container" align="center"></div>

    <div id="data_panel_report" align="center" style="display: none"></div>
</form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>