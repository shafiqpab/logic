<?php 
/*-------------------------------------------- Comments
Purpose			: 	This form created Batch Wise Cutting Report For urmi

Functionality	:	
JS Functions	:
Created by		:	Md. Thorat Islam
Creation date 	: 	27-Jun-2022
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
//==========================================================================
echo load_html_head_contents("Batch Wise Cutting Report","../../", 1, 1, "",'1','');
?>
</head>
<body onLoad="set_hotkey()"><script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';

// var tableFilters = 
// {
//     col_operation: 
//     {
//         id: ["gr_order_qty"],
//         col: [9],
//         operation: ["sum"],
//         write_method: ["innerHTML"]
//     }
// }
//  =========================Cut No Pop Not Work rignt now, This is Future plan======================
function open_cut_no()
{	
    var buyer_name=$("#cbo_buyer_name").val();
    // var cbo_year=$("#cbo_year").val();
    var page_link='requires/batch_wise_cutting_report_controller_urmi.php?action=cut_popup&buyer_name='+buyer_name;
    var title="Search Job No Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]; 
        // var job_id=this.contentDoc.getElementById("hide_job_id").value;
        var job_no=this.contentDoc.getElementById("hide_job_no").value;

        $("#txt_cut_no").val(job_no);
        // $("#hidden_job_id").val(job_id); 
    }
}

function fn_generate_report(type)
{
    var cut_no = document.getElementById('txt_cut_no').value;
    if(cut_no=="")
    {
        if( form_validation('cbo_wo_company_name*txt_date_from*txt_date_to','Company*Date From* Date To')==false )
        {
            return;
        }
    }
    else
    {
        if( form_validation('cbo_wo_company_name*txt_cut_no','Company*Cutting Number')==false )
        {
            return;
        }
    }
    
    var report_title=$( "div.form_caption" ).html();

    var data="action=generate_report"+get_submitted_data_string('cbo_wo_company_name*cbo_buyer_name*txt_cut_no*txt_date_from*txt_date_to*cbo_cutting_year*txt_batch_no',"../../")+'&type='+type+'&report_title='+report_title;
     
    freeze_window(3);
    http.open("POST","requires/batch_wise_cutting_report_controller_urmi.php",true);
    http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    http.send(data);
    http.onreadystatechange = generate_report_reponse;  
}

function generate_report_reponse()
{	
    if(http.readyState == 4) 
    {			 
        var reponse=trim(http.responseText).split("####");
        $("#report_container3").html('');
        $("#report_container2").html(reponse[0]);  
        // document.getElementById('report_container').innerHTML = report_convert_button('../../');
        document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" ondbclick="exportReportToExcel(this);" name="excel" id="exportBtn" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
        
        // setFilterGrid("table_body",-1,tableFilters);
        show_msg('3');
        release_freezing();
    }
} 

function new_window()
{
    document.getElementById('scroll_body').style.overflow="auto";
    document.getElementById('scroll_body').style.maxHeight="none"; 
    $(".flt").css("display","none");
    
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
    d.close(); 
    
    document.getElementById('scroll_body').style.overflowY="scroll"; 
    document.getElementById('scroll_body').style.maxHeight="400px";
    $(".flt").css("display","block");
} 

function reset_form()
{
    $("#hidden_style_id").val("");
    $("#hidden_order_id").val("");
    $("#hidden_job_id").val("");
    
}	 
 
    function fnc_cng_title(type)
    {
        if(type==1)
        {
            $("#txt_cut_no").show();
            $("#txt_batch_no").hide();
            $("#txt_batch_no").val(null);
            document.getElementById("change_title").innerHTML = "Cutting Number";
        }
        else
        {
            $("#txt_cut_no").hide();
            $("#txt_batch_no").show();
            $("#txt_cut_no").val(null);
            document.getElementById("change_title").innerHTML = "Batch Number";
        }
    }
</script>
<style>
.accordion {
transition: max-height 1s ease-in;
}

.active, .accordion:hover {
background-color: #ccc; 
}

.panel {
padding: 0 18px;
display: none;
background-color: white;
overflow: hidden;
}
</style>
</head>

<body onLoad="set_hotkey();">
<div style="width:100%;" align="center"> 
<? echo load_freeze_divs ("../../",'');  ?>
<h3 style="width:950px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
   <div style="width:100%;" align="center" id="content_search_panel">
<form id="dateWiseProductionReport_1">    
  <fieldset style="width:950px;">
        <table class="rpt_table" width="950" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
           <thead>                    
                   <tr>
                    <th class="must_entry_caption" width="150" >Company</th>
                    <th width="150">Buyer Name</th>
                    <th width="100" >Cutting Year</th>
                    <th width="150" >Search Type</th>
                    <th width="150" id="change_title" >Cutting Number</th>
                    <th class="must_entry_caption" width="150">Cutting Date Range </th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                </tr>   
          </thead>
            <tbody>
            <tr class="general">
                <td align="center"> 
                    <?
                        echo create_drop_down( "cbo_wo_company_name", 150, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/batch_wise_cutting_report_controller_urmi', this.value, 'load_drop_down_buyer', 'td_buyer' );" );
                    ?>
                </td>
                <td align="center" id="td_buyer"> 
                    <?
                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"",1, "-- Select Buyer --", "", "" );
                    ?>
                </td>
                <td>
                    <? 
                    $selected_year=date("Y");                               
                    echo create_drop_down( "cbo_cutting_year", 80, $year,"", 1, "--Select Year--",$selected_year,'',0);
                    ?>                            
                </td>  
                <td>
                    <? 
                    $search_by=array(1=>"Cutting Number",2=>"Batch Number");                               
                    echo create_drop_down( "cbo_search_by", 130, $search_by,"", 0, "--Select--",1,'fnc_cng_title(this.value);',0);
                    ?>                            
                </td>  
                <td>
                       <input type="text" id="txt_cut_no"  name="txt_cut_no"  style="width:150px" class="text_boxes"  placeholder="Write"  />
                       <input type="text" id="txt_batch_no"  name="txt_batch_no"  style="width:150px; display:none;" class="text_boxes"  placeholder="Write"  />
                       <!-- <input type="text" id="txt_cut_no"  name="txt_cut_no"  style="width:150px" class="text_boxes" onDblClick="open_cut_no()" placeholder="Browse" readonly /> -->
                       <!-- <input type="hidden" id="hidden_job_id"  name="hidden_job_id" /> -->
                    </td>
                    
                <td align="center">
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date">&nbsp;
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px"  placeholder="To Date">
                </td>
                <td>
                     <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />                                            
                     
                 </td>
                
            </tr>
            <tr>
                <td colspan="8">
                    <? echo load_month_buttons(1); ?>
                </td> 
            </tr>
            </tbody>
        </table>
  </fieldset>

</form> 
</div>
<div id="report_container" style="margin:10px 0;"></div>
<div id="all_report_container">
    <div id="report_container2"></div>  
    <div id="report_container3"></div> 
</div> 
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
