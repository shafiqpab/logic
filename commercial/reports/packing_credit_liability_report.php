<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Packing Credit Liability Report
Functionality	:
JS Functions	:
Created by		:	Md. Shafiqul Islam Shafiq
Creation date 	: 	24-07-2018
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
echo load_html_head_contents("Export Lc/Sc Report", "../../", 1, 1,'','','');
?>

<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

function generate_report()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
        //var date_from = $("#txt_date_from").val();

		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*loan_ref*cbo_year_selection*cbo_file_year*cbo_lien_bank*txt_file_no*txt_lc_sc_no*txt_date_from_loan*txt_date_to_loan*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/packing_credit_liability_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			var response=trim(http.responseText);
			$('#report_container2').html(response);
			document.getElementById('report_container').innerHTML=report_convert_button('../../');
			show_msg('3');
		}
	}
    function openmypage(company,file_no,bank_id,text_year,lc_sc,lc_sc_id,action)
    {
        var popup_width="";var title = "";
        if(action=="net_amount_popup")
        {
            popup_width="1250px";
            title = " Net value popup";
        }
        else
        {
            popup_width="850px";
            title = " Gross value popup";
        }
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/packing_credit_liability_report_controller.php?file_no='+file_no+'&company_name='+company+'&bank_id='+bank_id+'&text_year='+text_year+'&lc_sc_type='+lc_sc +'&lc_sc_id='+lc_sc_id+'&action='+action, title, 'width='+popup_width+',height=350px,center=1,resize=0,scrolling=0','../');
    }
</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",''); ?>
        <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
            <div style="width:1050px;">
                <h3 align="left" id="accordion_h1" style="width:1050px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                <div id="content_search_panel">
                    <fieldset style="width:100%;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">
                                    <table class="rpt_table" cellspacing="0" cellpadding="0" width="1040" border="1" rules="a">
                                        <thead>
                                            <th width="130" class="must_entry_caption">Company</th>
                                            <th width="120">Lien Bank</th>
                                            <th width="100">LC/SC No</th>
                                            <th width="100">Loan Ref.</th>
                                            <th width="80">File Year</th>
                                            <th width="80">File No</th>
                                            <th width="80">Loan Date from</th>
                                            <th width="80">Loan Date To</th>
                                            <th width="80">Expiry Date From</th>
                                            <th width="80">Expiry Date To</th>
                                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
                                        </thead>
                                        <tbody>
                                            <tr class="general">
                                                <td>
                        						<?
                                                	echo create_drop_down( "cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "load_drop_down( 'requires/packing_credit_liability_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/packing_credit_liability_report_controller',this.value, 'load_drop_down_applicant', 'applicat_td' );" );
                                                ?>
                                                </td>
                                                <td>
                                                <?
                                                    echo create_drop_down( "cbo_lien_bank", 120, "select (bank_name||' ('||branch_name||')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "- All Lien Bank -", 0, "" );
                                                ?>
                                                </td>
                                                <td>
                                                <input type="text" id="txt_lc_sc_no" name="txt_lc_sc_no" class="text_boxes" style="width:90px;">
                                                </td>
                                                <td>
                                                <input type="text" id="loan_ref" name="loan_ref" class="text_boxes" style="width:90px;">
                                                </td>
                                                <td>
                                                <?
                                                echo create_drop_down( "cbo_file_year", 80, $year,"id,cbo_file_year", 1, "- Select File Year -", 0, "" );
                                                ?>
                                                </td>
                                                <td>
                                                <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:70px;">
                                                </td>
                                                <td>
                                                    <input type="text" id="txt_date_from_loan" name="txt_date_from_loan" class="datepicker" style="width:60px;">
                                                </td>
                                                <td>
                                                    <input type="text" id="txt_date_to_loan" name="txt_date_to_loan" class="datepicker" style="width:60px;">
                                                </td>
                                                <td>
                                                    <input type="text" id="txt_date_from" name="txt_date_from" class="datepicker" style="width:60px;">
                                                </td>
                                                <td>
                                                    <input type="text" id="txt_date_to" name="txt_date_to" class="datepicker" style="width:60px;">
                                                </td>
                                               	<td>
                                                <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:70px" class="formbutton" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="15" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                            </tr>
                        </table>

                    </fieldset>
                </div>
            </div>
            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
