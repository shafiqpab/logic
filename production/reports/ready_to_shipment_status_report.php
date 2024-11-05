<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   Report.
Functionality   :   
JS Functions    :
Created by      :   Md. Saidul Islam REZA 
Creation date   :   28-06-2021
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
echo load_html_head_contents("Ready to Shipment Status", "../../", 1, 1,$unicode,0,0);

?>  
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission = '<? echo $permission; ?>';
 
function fn_report_generated()
{
    if(form_validation('cbo_company_id*txt_date_to','Company*Date')==false)
    {
        return;
    }
    else
    {
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_buyer_id*cbo_season_id*txt_int_ref*txt_job_no*txt_job_id*txt_style_ref*cbo_order_type*txt_date_to',"../../");
        freeze_window(3);
        http.open("POST","requires/ready_to_shipment_status_report_controller.php",true);
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
        $('#report_container2').html(reponse[0]);
        document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
		setFilterGrid("table_body",-1);
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


 
    
 

function openmy_search_popup(type)
{    
    if(form_validation('cbo_company_id','Company Name')==false)
    {
        return;
    }
    var company = $("#cbo_company_id").val();
    var page_link='requires/ready_to_shipment_status_report_controller.php?action=openmy_search_popup&company='+company+'&type='+type; 
    var title="Search Popup";
    
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var order_no=this.contentDoc.getElementById("hide_order_no").value;
        var job_id=this.contentDoc.getElementById("hide_order_id").value;
                  
        if(type==1){
			$("#txt_int_ref").val(order_no);
			$("#txt_job_id").val(job_id);
		}
        else if(type==2){
			$("#txt_job_no").val(order_no);
			$("#txt_job_id").val(job_id);
		}
        else if(type==3){
			$("#txt_style_ref").val(order_no);
			$("#txt_job_id").val(job_id);
		}
    }
}

 
 

</script>                     
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:1150px; margin:0 auto;" align="center">    
        <? echo load_freeze_divs ("../../",'');  ?>
         <fieldset>
            <legend>Search Panel</legend>
            <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Location</th>
                        <th>Buyer</th> 
                        <th>Season</th> 
                        <th>Internal Ref</th> 
                        <th>Job No</th> 
                        <th>Style</th> 
                        <th>PO Status</th> 
                        <th>Current Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td align="center"> 
                        <?
                            echo create_drop_down( "cbo_company_id", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/ready_to_shipment_status_report_controller',this.value,'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/ready_to_shipment_status_report_controller',this.value,'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>
                    <td id="location_td" align="center">
                        <? 
                            echo create_drop_down( "cbo_location_id", 120, $blank_array,"", 1, "-- Select --", $selected, "", 0, "" );
                        ?>
                    </td>
                    <td id="buyer_td" align="center">
                        <?
                        echo create_drop_down("cbo_buyer_id", 120, $blank_array, "", 1, "-- All --", $selected, "", 0, "");
                        ?>
                    </td>
                    <td id="season_td" align="center">
                        <?
                        echo create_drop_down("cbo_season_id", 120, $blank_array, "", 1, "-- All --", $selected, "", 0, "");
                        ?>
                    </td>
                    <td align="center">
                        <input name="txt_int_ref" id="txt_int_ref" class="text_boxes" style="width:100px" placeholder="Browse/Write" onDblClick="openmy_search_popup(1)">
                    </td>
                    <td align="center">
                        <input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Browse/Write" onDblClick="openmy_search_popup(2)" >
                        <input type="hidden" name="txt_job_id" id="txt_job_id" readonly>
                    </td>
                    <td align="center">
                        <input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px" placeholder="Browse/Write" onDblClick="openmy_search_popup(3)" >
                    </td>
                    <td align="center">
                       <?
                        $orderType=array(1=>"Partial+Full Pending",2=>"Partial Pending",3=>"Full Pending");
						echo create_drop_down("cbo_order_type", 100, $orderType, "", 0, "-- All --", $selected, "", 0, "");
                        ?>  
                    </td>
                    <td align="center">
                    <input type="text" name="txt_date_to" id="txt_date_to" value="<?=date("d-m-Y");?>" class="datepicker" style="width:70px"/>
                    </td>
                 
                    <td align="center">
                        <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(0)" />
                    </td>
                </tr>                                
                </tbody>
            </table>
        </fieldset>
    </div>
    <div id="report_container" align="center" style="padding:10px;"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
