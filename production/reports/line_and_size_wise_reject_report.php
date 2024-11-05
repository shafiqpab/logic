<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   Report.
Functionality   :   
JS Functions    :
Created by      :   Md. Shafiqul Islam Shafiq 
Creation date   :   07-04-2020
Updated by      :       
Update date     :    
QC Performed BY :       
QC Date         :   
Comments        :   Code is poetry, I try to do that :)
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,1,1);

?>  
<script src="../../js/highchart/highcharts.js"></script>
<script src="../../js/highchart/highcharts-3d.js"></script>
<script src="../../js/highchart/exporting.js"></script>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
var tableFilters = 
        {
            col_0: "none", 
        } 
                
var tableFilters1 = 
        {
            col_0: "none", 
        } 
                    
function fn_report_generated()
{
    if (form_validation('cbo_wo_company_name*cbo_location','Working Company*Location')==false)
    {
        return;
    }
    else
    {
        
        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_wo_company_name*cbo_location*cbo_floor*cbo_line*cbo_buyer_name*cbo_season_name*txt_int_ref*txt_job_no*txt_style_ref*txt_order_no*hidden_color_id*hidden_po_id*txt_date_from*txt_date_to',"../../");
        freeze_window(3);
        http.open("POST","requires/line_and_size_wise_reject_report_controller.php",true);
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
            showChart(reponse[3],reponse[4]);
        }
        $('#report_container2').html(reponse[0]);
        // alert(reponse[2]);
        document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
        release_freezing();
    }
    
}

function new_window()
{
    document.getElementById('scroll_body').style.overflow='auto';
    document.getElementById('scroll_body').style.maxHeight='none'; 
    $("#table_body tr:first").hide();
    $("#table_body1 tr:first").hide();
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
    d.close();
    
    document.getElementById('scroll_body').style.overflowY='scroll';
    document.getElementById('scroll_body').style.maxHeight='425px';
    $("#table_body tr:first").show();
}    


 function show_line_remarks(company_id,order_id,floor_id,line_no,prod_date,action)
    {
        
        popup_width='550px'; 
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_and_size_wise_reject_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
    }
    

function getCompanyId() 
{
    var company_id = document.getElementById('cbo_company_name').value;
    var location_id = document.getElementById('cbo_location').value;
    var floor_id = document.getElementById('cbo_floor').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(company_id !='') {
      var data="action=load_drop_down_line&company_id="+company_id+'&location_id='+location_id+'&floor_id='+floor_id;
      http.open("POST","requires/line_and_size_wise_reject_report_controller.php",true);
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

function openmypage_intref()
{    
    if(form_validation('cbo_wo_company_name','Working Company Name')==false)
    {
        return;
    }
    var company = $("#cbo_wo_company_name").val();
    var page_link='requires/line_and_size_wise_reject_report_controller.php?action=intref_search_popup&company='+company; 
    var title="Search Int Ref Popup";
    
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var order_no=this.contentDoc.getElementById("hide_order_no").value;
        var order_id=this.contentDoc.getElementById("hide_order_id").value;
                  
        $("#txt_int_ref").val(order_no);
        $("#hidden_po_id").val(order_id);  
        $("#txt_order_no").val(''); 
        $("#txt_color").val(''); 
    }
}

function openmypage_po()
{
    if( form_validation('txt_int_ref','Internal Reference')==false)
    {
        return;
    }
    var txt_int_ref = $("#txt_int_ref").val(); 
    var job_id = $("#hidden_po_id").val(); 

    var page_link='requires/line_and_size_wise_reject_report_controller.php?action=po_search_popup&txt_int_ref='+txt_int_ref+'&job_id='+job_id; 
    
    var title="Search PO Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]; 
        var prodID=this.contentDoc.getElementById("txt_selected_id").value;
        
        var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
        $("#txt_order_no").val(prodDescription);
        // $("#hidden_line_id").val(prodID); 
        // $("#txt_order_no").val(''); 
        $("#txt_color").val(''); 
    }
}

function openmypage_color()
{
    if( form_validation('txt_int_ref','Internal Reference')==false)
    {
        return;
    }
    var txt_int_ref = $("#txt_int_ref").val(); 
    var job_id = $("#hidden_po_id").val(); 
    var txt_order_no = $("#txt_order_no").val(); 

    var page_link='requires/line_and_size_wise_reject_report_controller.php?action=color_search_popup&txt_int_ref='+txt_int_ref+'&txt_order_no='+txt_order_no+'&job_id='+job_id; 
    
    var title="Search Color Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]; 
        var prodID=this.contentDoc.getElementById("txt_selected_id").value;
        
        var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
        $("#txt_color").val(prodDescription);
        $("#hidden_color_id").val(prodID); 
    }
}

</script>                     
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:1360px;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
               
         <fieldset style="width:99%;">
            <legend>Search Panel</legend>
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th>Company Name</th>
                        <th class="must_entry_caption">WO Company Name</th>
                        <th class="must_entry_caption">Location</th>
                        <th>Floor</th>
                        <th>Line</th> 
                        <th>Buyer</th> 
                        <th>Season</th> 
                        <th>Int. Ref.</th> 
                        <th>Job</th> 
                        <th>Style</th> 
                        <th>PO</th> 
                        <th>Color</th> 
                        <th id="search_text_td">Production Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td width="100"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 100, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/line_and_size_wise_reject_report_controller',this.value,'load_drop_down_buyer', 'buyer_td' );" );
                        ?>
                        <input type="hidden" id="hidden_po_id">
                        <input type="hidden" id="hidden_color_id">
                    </td>
                    <td width="100"> 
                        <?
                            echo create_drop_down( "cbo_wo_company_name", 100, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/line_and_size_wise_reject_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>                   
                    <td width="100" id="location_td">
                        <? 
                            echo create_drop_down( "cbo_location", 100, $blank_array,"", 1, "-- Select --", $selected, " load_drop_down( 'requires/line_and_size_wise_reject_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
                        ?>
                    </td>
                    <td width="100" id="floor_td">
                        <? 
                            echo create_drop_down( "cbo_floor", 100, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                    <!-- <td width="">
                        <input name="txt_date" id="txt_date" class="datepicker" style="width:75px" onChange="load_drop_down( 'requires/line_and_size_wise_reject_report_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_line', 'line_td' );" readonly >
                    </td> -->  
                     <td width="100" id="line_td">
                        <? 
                            echo create_drop_down( "cbo_line", 100, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                    <td id="buyer_td"  align="center">
                        <?
                        echo create_drop_down("cbo_buyer_name", 100, $blank_array, "", 1, "-- Select Buyer --", $selected, "", 1, "");
                        ?>
                    </td>
                    <td id="season_td"  align="center">
                        <?
                        echo create_drop_down("cbo_season_name", 100, $blank_array, "", 1, "-- Select Season --", $selected, "", 1, "");
                        ?>
                    </td>
                    <td align="center">
                        <input name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:65px" placeholder="Browse" onDblClick="openmypage_intref()" readonly>
                    </td>
                    <td align="center">
                        <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:65px" placeholder="Job No" >
                    </td>
                    <td align="center">
                        <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:65px" placeholder="Style Ref" >
                    </td>
                    <td align="center">
                        <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:65px" placeholder="Browse" onDblClick="openmypage_po()" readonly>
                    </td>
                    <td align="center">
                        <input name="txt_color" id="txt_color" class="text_boxes" style="width:65px" placeholder="Browse" onDblClick="openmypage_color()" readonly>
                    </td> 

                    <td align="center">
                    <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px"/>                                             
                     To
                    <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px"/>
                    </td>
                 
                    <td width="70">
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" />
                    </td>
                </tr>                                
                <tr>
                    <td colspan="14" align="center"><? echo load_month_buttons(1);  ?></td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    
    <div id="report_container" align="center" style="padding:10px;"></div>
    <div id="report_container2"></div>
    <div id="chart_container"></div>
 </form>    
</body>
<script>
    // set_multiselect('cbo_floor','0','0','','0');
    // setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor,'0');getCompanyId();") ,3000)];   

</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
