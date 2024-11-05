<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Lc/Salse Contact Report.
Functionality	:	
JS Functions	:
Created by		:	Jahid
Creation date 	: 	21-12-2013
Updated by 		: 	Rakib	
Update date		: 	29-10-2019	   
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
	
    function generate_report(rpt_type)
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();

        if(rpt_type==1){
            var data="action=report_generate"+get_submitted_data_string("cbo_company_name*txt_year*cbo_lien_bank*txt_lc_sc_no*txt_date_from*txt_date_to*cbo_type","../../")+'&report_title='+report_title;
        }
		
		freeze_window(3);
		http.open("POST","requires/monthly_btb_lc_statement_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}
	
	function fn_report_generated_reponse()
    {
        if(http.readyState == 4) 
        {
            release_freezing();
            // var response=trim(http.responseText);
            var response=trim(http.responseText).split("####");
            $('#report_container2').html(response[0]);
            // document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
            document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
            show_msg('3');
        }
    }
    function new_window()
    {
        document.getElementById('scroll_body').style.overflow="auto";
        document.getElementById('scroll_body').style.maxHeight="none";
        
        
        // $('#table_body tr:first').hide();
        
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close(); 
        
        // $('#table_body tr:first').show();
        
        
        document.getElementById('scroll_body').style.overflowY="scroll";
        document.getElementById('scroll_body').style.maxHeight="300px";
    }
    
</script>
</head>

<body onLoad="set_hotkey();">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",''); ?>
    <form id="frm_lc_salse_contact" name="frm_lc_salse_contact">
    <div style="width:1080px;">
    <h3 align="left" id="accordion_h1" style="width:1085px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
    <div id="content_search_panel"> 
    <fieldset style="width:1070px;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table class="rpt_table" cellspacing="0" cellpadding="0" width="1070">
                    <thead>
                        <th width="120" class="must_entry_caption">Company</th>
                        <th width="70">Year</th>
                        <th width="110">Lien Bank</th>
                        <th width="130">LC/SC No</th>
                        <th width="90"> Type</th>
                        <th width="180" >LC/SC Date Range</th>
                        <th width="80"><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('frm_lc_salse_contact','report_container*report_container2','','','')" /></th>
                    </thead>
                    <tbody>
                        <tr >
                            <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "- Select Company -", $selected, "" );
                            ?>
                            </td>
                            <td>
                                <input type="text" id="txt_year" name="txt_year" class="text_boxes" style="width:70px;">
                            </td>
                            <td>
                            <?
                                echo create_drop_down( "cbo_lien_bank", 110, "select (bank_name||' ('||branch_name||')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "- All Lien Bank -", 0, "" );
                            ?>
                            </td>
                            <td>
                            <input type="text" id="txt_lc_sc_no" name="txt_lc_sc_no" class="text_boxes" style="width:130px;">
                            </td>
                            <td id="">
                                <? 
                               
                                    $type_array=array(1=>"LC ", 2=>"SC");
                                    echo create_drop_down( "cbo_type", 85, $type_array,"", 0 , "- All -", 0, "",0,"" );
                                ?>
                            </td>
                            <td>                        
                                <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:70px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td colspan="12" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>              
                    </tr>
                </table>
            </td>
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