<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Export Proceed Realization Report.
Functionality	:
JS Functions	:
Created by		:	Jahid
Creation date 	: 	28-02-2018
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
echo load_html_head_contents("Export Proceed Realization Report", "../../", 1, 1,'','','');
?>
<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var permission = '<? echo $permission; ?>';

	function generate_report()
	{
		var bill_no=$('#txt_bill_no').val();
		if(bill_no!="")
		{
			if(form_validation('cbo_company_name','Company Name')==false)
			{
				return;
			}
		}
		else
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false)
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_lein_bank*cbo_buyer_name*txt_bill_no*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/export_proceeds_realization_rpt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("####");
			//alert(http.responseText);return;
			$('#report_container2').html(response[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
            setFilterGrid("table_body",-1);
			release_freezing();
		}
	}

	function new_window()
	{
		/*document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";*/
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		/*document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="auto";*/
	}

	function openmypage_inv(bil_id,is_invoice_bill,action)
	{
		var title="Invoice Details";
		page_link='requires/export_proceeds_realization_rpt_controller.php?action='+action+'&bil_id='+bil_id+'&is_invoice_bill='+is_invoice_bill;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe',page_link,title, 'width=550px,height=350px,center=1,resize=0,scrolling=0','../');
		//alert(bil_id+"**"+is_invoice_bill+"**"+action);return; //4176,2**inv_details**undefined
		emailwindow.onclose=function()
		{
			//alert("Jahid");
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
 <div style="width:100%" align="center">
    <form id="exp_proceed_rlz" action="" autocomplete="off" method="post">
		<? echo load_freeze_divs ("../../"); ?>
        <h3 align="left" id="accordion_h1" style="width:900px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:900px;">
    	<fieldset style="width:100%" >
        <table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="900">
            <thead>
                <th class="must_entry_caption" width="170">Company Name</th>
                <th width="170">Lien Bank</th>
                <th width="170">Buyer</th>
                <th width="100">Bill No</th>
                <th class="must_entry_caption" width="180">Realization Date Range</th>
                <th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:90px" onClick="reset_form('exp_proceed_rlz','report_container*report_container2','','','')" /></th>
           </thead>
            <tr class="general">
                <td align="center">
                   <?
                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/export_proceeds_realization_rpt_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                    ?>
                </td>
                <td align="center">
                <?
                    echo create_drop_down( "cbo_lein_bank", 150, "select (bank_name||' ('||branch_name||')') as bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- All Bank --", $selected, "",0,"" );
                ?>
                </td>
                <td align="center" id="buyer_td">
                <?
					echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "- All Buyer -", $selected, "",0,"" );
				?>
                </td>
                <td><input type="text" id="txt_bill_no" name="txt_bill_no" class="text_boxes" style="width:90px;"></td>
                <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px">&nbsp;
                To<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                </td>
				<td align="center"><input type="button" name="show" id="show" onClick="generate_report();" class="formbutton" style="width:90px" value="Show" /></td>
            </tr>
            <tr>
                <td colspan="6" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
            </tr>
         </table>
    </fieldset>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
