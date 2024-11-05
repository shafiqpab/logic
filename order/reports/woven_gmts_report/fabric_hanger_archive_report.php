<?
    /*-------------------------------------------- Comments -----------------------
    Purpose			:	This Form Will Create Fabric Hanger Archive Report.
    Functionality	:
    JS Functions	:
    Created by		:	MD. SAKIBUL ISLAM
    Creation date 	: 	21-JUNE-2023
    Updated by 		: 		
    Update date		: 		   
    QC Performed BY	:	EMON	
    QC Date			:	
    Comments		:
    */

    session_start();
    if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
    require_once('../../../includes/common.php');
    extract($_REQUEST);
    $_SESSION['page_permission']=$permission;

    //--------------------------------------------------------------------------------------------------------------------
    echo load_html_head_contents("Fabric Hanger Archive", "../../../", 1, 1,$unicode,1,1);
?>	
    <script>

        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";  
        var permission = '<? echo $permission; ?>';	

            var tableFilters = 
            {
                col_operation: {
                    id: ["value_total_wo_qnty"],
                    col: [7],
                    operation: ["sum"],
                    write_method: ["innerHTML"]
                }
            } 
            
            function fn_report_generated(operation)
            {
                var cbo_company_name=document.getElementById('cbo_company_name').value;
                var txt_dispo_no=document.getElementById('txt_dispo_no').value;
                var txt_date_from=document.getElementById('txt_date_from').value;
                var txt_date_to=document.getElementById('txt_date_to').value;
                
                if(txt_dispo_no=="")
                {
                    if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From* Date To')==false)
                    {
                        return;
                    }
                 }
                 else
                 {
                    if(form_validation('cbo_company_name','Company Name')==false)
                    {
                        return;
                    }
                 }
                    
                    var report_title=$( "div.form_caption" ).html();
                    var data="action=report_generate&operation="+operation+get_submitted_data_string('cbo_company_name*txt_dispo_no*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
                    freeze_window(3);
                    http.open("POST","requires/fabric_hanger_archive_report_controller.php",true);
                    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    http.send(data);
                    http.onreadystatechange = fn_report_generated_reponse;
                 
            }
            
            function fn_report_generated_reponse()
            {
                if(http.readyState == 4) 
                {
                    var reponse=trim(http.responseText).split("**");
                    $('#report_container4').html(reponse[0]);
                    document.getElementById('report_container3').innerHTML=report_convert_button('../../');
                    document.getElementById('report_container3').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px;"/>';
                    
                    show_msg('3');
                    setFilterGrid("table_body",-1,tableFilters);
                    release_freezing();
                }
            }
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
            }
    </script>
    </head>
    <body onLoad="set_hotkey();">
        <div style="width:100%;" align="center">
            <? echo load_freeze_divs ("../../../",$permission); ?>    		 
            <form name="FHAReport_1" id="FHAReport_1" autocomplete="off" > 
            <h3 style="width:650px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                <div id="content_search_panel" >
                    <fieldset style="width:570px;">
                    <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>
                            <tr>
                                <th width="150" class="must_entry_caption">Compnay</th>
                                <th width="120">Dispo No</th>
                                <th width="100" class="must_entry_caption" colspan="2">Date Range</th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td style="width:150px"><? 
                                    $com_sql = "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business not in(3) $company_cond order by company_name";
                                    echo create_drop_down( "cbo_company_name", 150,$com_sql,"id,company_name", 1, "Select Company", $selected,"" ,0);
                                    ?></td>
                                <td style="width:110px"><input type="text" name="txt_dispo_no" id="txt_dispo_no" class="text_boxes" style="width:110px"  maxlength="100" title="Maximum 100 Character"/></td>
                                <td style="width: 90px;"><input name="txt_date_from" id="txt_date_from" class="datepicker"  placeholder="From Date"></td>
                                <td style="width: 90px;"><input name="txt_date_to" id="txt_date_to" class="datepicker"  placeholder="To Date"></td>

                                <td ><input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(1)" /></td>
                            </tr>
                            <!-- <tr>
                                <td colspan="5" align="center"><? //echo load_month_buttons(1); ?></td>
                            </tr> -->
                        </tbody>
                    </table> 
                    </fieldset>
                </div>
                <div id="report_container3" style="margin-top: 2px;" align="center"></div>
                <div id="report_container4" align="center"></div>
            </form> 
        </div>
        <div style="display:none" id="data_panel"></div>
    </body>
        <script>//set_multiselect('cbo_wo_type','0','0','','0');</script>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
