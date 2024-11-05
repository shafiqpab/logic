<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	
Functionality	:	
JS Functions	:
Created by		:	Md Rakib Hasan Mondal
Creation date 	: 	13-June-2023
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
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);

?> 
<script>

    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission = '<? echo $permission; ?>';

    function fn_report_generated()
    {
        if ($('#txt_search_common').val()) {
            if (form_validation('cbo_company_name','Company Name')==false)
            {
                return;
            } 
        }
        else
        {
            if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Cutting Date*Cutting Date')==false)
            {
                return;
            } 
        }
                   
        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_buyer_name*cbo_search_by*txt_search_common*txt_date_from*txt_date_to',"../../");
        freeze_window(3);
        http.open("POST","requires/cutting_summary_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse; 
    } 
    function fn_report_generated_reponse()
    {
        if(http.readyState == 4) 
        {
            show_msg('3'); 
            var reponse=trim(http.responseText).split("####"); 
            if(reponse[2]=="show_chart")
            {
                // alert(reponse[3]+reponse[4]);
                // showChart(reponse[3],reponse[4]);
            }
            $('#report_container2').html(reponse[0]);
            // alert(reponse[2]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
            // setFilterGrid("table_body",-1,tableFilters);     
            release_freezing();
        }
        
    }
    function new_window() {
        document.getElementById('scroll_body').style.overflow='auto';
        document.getElementById('scroll_body').style.maxHeight='none'; 
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
        d.close();
        document.getElementById('scroll_body').style.overflowY='scroll';
        document.getElementById('scroll_body').style.maxHeight='300px';
    }
</script>         
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">   
        <? echo load_freeze_divs ("../../",'');  ?>     
        <fieldset style="width:1020px;">
            <legend>Search Panel</legend> 
            <table class="rpt_table" width="1020px" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th width="150" class="must_entry_caption">Working Company</th>
                        <th width="150">Location</th>
                        <th width="150">Buyer</th> 
                        <th width="110">Search By</th>
                        <th id="search_by_td_up" width="130">Please Enter Style No</th> 
                        <th width="140" id="search_text_td"  class="must_entry_caption">Cutting Date Range</th>
                        <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                    <tr >
                        <td width="150"> 
                            <?
                                echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_summary_report_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/cutting_summary_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>                   
                        <td width="150" id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, " load_drop_down( 'requires/cutting_summary_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
                            ?>
                        </td>
                        <td width="150" id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "", 1, "" );
                            ?>
                        </td>      
                        <td width="110">
                            <?
                                $search_by_arr=array(1=>"Style No",2=>"Job No",3=>"Order No");
                                $dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
                                echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td width="130" align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 
                        <td width="140" align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px"/>                                             
                            To
                            <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px"/>
                        </td>                
                        <td width="100">
                            <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show" onClick="fn_report_generated(0)" />
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="7"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </form>    
</body>

<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
