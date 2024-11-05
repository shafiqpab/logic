<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   Report.
Functionality   :   
JS Functions    :
Created by      :   Md. Shafiqul Islam Shafiq 
Creation date   :   10-07-2019
Updated by      :       
Update date     :    
QC Performed BY :       
QC Date         :   
Comments        :
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
     
    var tableFilters = 
    {
        // col_0: "none", 
    }                    
    
                        
    function fn_report_generated()
    {
        if (form_validation('cbo_company_name*cbo_location*cbo_floor*txt_date_from*txt_date_to','Company Name*Location Name*Floor Name*Production Date')==false)
        {
            return;
        }
        else
        {            
            var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*txt_date_from*txt_date_to',"../../");
            freeze_window(3);
            http.open("POST","requires/monthly_production_summary_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fn_report_generated_reponse;
        }
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

    function new_window()
    {
        document.getElementById('scroll_body').style.overflow='auto';
        document.getElementById('scroll_body').style.maxHeight='none'; 
        // $("#table_body tr:first").hide();
        // $("#table_body1 tr:first").hide();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
        d.close();
        
        document.getElementById('scroll_body').style.overflowY='scroll';
        document.getElementById('scroll_body').style.maxHeight='425px';
        // $("#table_body tr:first").show();
    }    
     
    function getCompanyId() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        var location_id = document.getElementById('cbo_location').value;
        var floor_id = document.getElementById('cbo_floor').value;
        //var search_type = document.getElementById('cbo_search_by').value;
        if(company_id !='') {
          var data="action=load_drop_down_line&company_id="+company_id+'&location_id='+location_id+'&floor_id='+floor_id;
          http.open("POST","requires/monthly_production_summary_report_controller.php",true);
          http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
          http.send(data); 
          http.onreadystatechange = function(){
              if(http.readyState == 4) 
              {
                  var response = trim(http.responseText);
                  //$('#location_td').html(response);
                  $('#line_td').html(response);
                 // set_multiselect('cbo_location','0','0','','0');
                  //set_multiselect('cbo_buyer_name','0','0','','0');
                 // fn_buyer_visibility(search_type);
              }          
          };
        }     
    }

</script>         
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
               
         <fieldset style="width:750px;">
            <legend>Search Panel</legend>
            <table class="rpt_table" width="750px" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th width="150" class="must_entry_caption">Working Company Name</th>
                        <th width="150" class="must_entry_caption">Location Name</th>
                        <th width="150" class="must_entry_caption">Floor Name</th>
                        <th width="200" id="search_text_td" class="must_entry_caption">Production Date Range</th>
                        <th width="100"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td width="150"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/monthly_production_summary_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>                   
                    <td width="150" id="location_td">
                        <? 
                            echo create_drop_down( "cbo_location", 150, $blank_array,"", 1, "-- Select Location --", $selected, " load_drop_down( 'requires/monthly_production_summary_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
                        ?>
                    </td>
                    <td width="150" id="floor_td">
                        <? 
                            echo create_drop_down( "cbo_floor", 150, $blank_array,"", 1, "-- Select Floor --", $selected, "", 1, "" );
                        ?>
                    </td>
                    <td width="200">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" >&nbsp; To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"  >
                    </td>                  
                    <td width="100">
                        <input type="button" id="show_button" class="formbutton" style="width:90px" value="Show Report" onClick="fn_report_generated(0)" />
                    </td>
                </tr>
                </tbody>
            </table>            
            <table>
                <tr>
                    <td>
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table> 
        </fieldset>
    </div>
    
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script>
    set_multiselect('cbo_floor','0','0','','0');
    // setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor,'0');getCompanyId();") ,3000)];   

</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
