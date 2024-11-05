<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   Report.
Functionality   :   
JS Functions    :
Created by      :   Kamrul Hasan
Creation date   :   13-05-2023
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
                    
function fn_report_generated(type)
{
    if(type==1)
    {
        var po_id = $("#hidden_job_id").val();
        var txt_date = $("#txt_date").val();
        if(po_id=="" && txt_date=="")
        {
            alert('Style or IR/IB production date required!');
            return;
        }

        if (form_validation('cbo_company_name','Company Name')==false)
        {
            return;
        }
    }
    
    var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*cbo_line*txt_date*txt_style_no*hidden_job_id*txt_internal_ref',"../../");
    freeze_window(3);
    http.open("POST","requires/floor_wise_finishing_wip_report_controller.php",true);
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
    // $("#table_body tr:first").hide();
    // $("#table_body1 tr:first").hide();
    var w = window.open("Surprise", "#");
    var d = w.document.open();
    d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
    d.close();
    
    document.getElementById('scroll_body').style.overflowY='scroll';
    document.getElementById('scroll_body').style.maxHeight='425px';
    // $("#table_body tr:first").show();
}    


 function show_line_remarks(company_id,order_id,floor_id,line_no,prod_date,action)
    {
        
        popup_width='550px'; 
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/floor_wise_finishing_wip_report_controller.php?order_id='+order_id+'&company_id='+company_id+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
    }
            
function showChart(floor_name,floor_total) 
{
    // $("#chart_container").show('fast');
    var floor_name_arr = floor_name.split(',');
    // var floor_name_arr = floor_name_arr.toString();
    var floor_total_arr = floor_total.split(',');
    var floor_total_arr = floor_total_arr.map(Number);
    // alert(value);
    
    Highcharts.chart('chart_container', {
        chart: {
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 5,
                beta: 5,
                depth: 70
            }
        },
        title: 
        {
            text: 'Floor Wise Sewing WIP Report Chart',
            style:
                {
                    color: 'black',
                    fontSize: '22px',
                    fontWeight: 'bold'
                }
        },
        subtitle: 
        {
            useHTML: true,
            align: 'center',
            y: 40,
            text: '<b>Date : '+$("#txt_date").val()+'</b>' 
        },
        plotOptions: 
        {
            column: 
            {
                depth: 25
            },
            series: 
            {
                dataLabels: 
                {
                    align: 'center',
                    enabled: true
                }
            }
        },
        xAxis: 
        {
            categories: floor_name_arr,
            labels: 
            {
                skew3d: true,
                style: 
                {
                    fontSize: '14px',
                    color: 'black',
                    fontWeight: 'bold'
                }
            },
        },
        yAxis: 
        {
            title: 
            {
                text: 'Floor Wise Total Quantity',
                style:
                {
                    color: 'black',
                    fontSize: '14px',
                    fontWeight: 'bold'
                }
            }
        },
        credits: 
        {
            enabled: false
        },
        series: [{
            name: ['Floor Wise Total'],
            data: floor_total_arr
        }]
    });

}   

function getLineId() 
{
    var location_id = document.getElementById('cbo_location').value;
    var floor_id = document.getElementById('cbo_floor').value;
    //var search_type = document.getElementById('cbo_search_by').value;
    if(location_id && floor_id) {
      var data="action=load_drop_down_line&floor_id="+floor_id+'&location_id='+location_id;
      http.open("POST","requires/floor_wise_finishing_wip_report_controller.php",true);
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

function open_job_no()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var company_name=$("#cbo_company_name").val();
    var cbo_year=$("#cbo_year").val();
    var page_link='requires/floor_wise_finishing_wip_report_controller.php?action=job_popup&company_name='+company_name+'&cbo_year='+cbo_year;
    var title="Search Job/Style Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]; 
        var job_id=this.contentDoc.getElementById("hide_job_id").value;
        var job_no=this.contentDoc.getElementById("hide_job_no").value.split('*');
        var style_no=this.contentDoc.getElementById("hide_style_no").value.split('*');

        $("#txt_job_no").val('');
        $("#txt_style_no").val('');
        $("#hidden_job_id").val('');

        $("#txt_job_no").val([...new Set(job_no)]);
        $("#txt_style_no").val([...new Set(style_no)]);
        $("#hidden_job_id").val(job_id);
    }
}
function open_internal_ref()
{
    if( form_validation('cbo_company_name','Company Name')==false )
    {
        return;
    }
    var company_name=$("#cbo_company_name").val();
    var cbo_year=$("#cbo_year").val();
    var page_link='requires/floor_wise_finishing_wip_report_controller.php?action=internal_ref_popup&company_name='+company_name+'&cbo_year='+cbo_year;
    var title="Search Job/IR Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=390px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0]; 
        var job_id=this.contentDoc.getElementById("hide_job_id").value;
        var job_no=this.contentDoc.getElementById("hide_job_no").value.split('*');
        var internal_ref=this.contentDoc.getElementById("hide_internal_ref").value;
     
      //alert(job_no);
        $("#txt_job_no").val(job_no);
        $("#txt_internal_ref").val(internal_ref);
        $("#hidden_job_id").val(job_id);
      
    }
}

</script>
  <style type="text/css">
    #chart_container {
      height: 400px;
      min-width: 400px;
      max-width: 800px;
      margin: 0 auto;
    }
 </style>
                     
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
               
         <fieldset style="width:920px;">
            <legend>Search Panel</legend>
            <table class="rpt_table" width="900px" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th width="140">Working Company Name</th>
                        <th  width="110">Location</th>
                        <th  width="100">Style</th> 
                        <th   width="100">IR/IB</th> 
                        <th  width="140">Floor </th>
                        <th  id="search_text_td" class="must_entry_caption">Prod. Date</th>
                        <th  width="140">Line</th> 
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:100px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                <tr >
                    <td width="140"> 
                        <?
                            echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/floor_wise_finishing_wip_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
                        ?>
                    </td>                   
                    <td width="110" id="location_td">
                        <? 
                            echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, " load_drop_down( 'requires/floor_wise_finishing_wip_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
                        ?>
                    </td>
                    <td>
                        <input type="text" id="txt_style_no"  name="txt_style_no"  style="width:100px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
                        <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
                        <input type="hidden" id="txt_job_no"  name="txt_job_no" />
                    </td>
                    <td>
                        <input type="text" id="txt_internal_ref"  name="txt_internal_ref"  style="width:100px" class="text_boxes" onDblClick="open_internal_ref()" placeholder="Browse/Write"  />
                        <input type="hidden" id="hidden_job_id"  name="hidden_job_id"  />
                        <input type="hidden" id="txt_job_no"  name="txt_job_no" />
                    </td>
                    <td width="110" id="floor_td">
                        <? 
                            echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                    <td width="">
                        <input name="txt_date" id="txt_date" class="datepicker" style="width:75px" readonly >
                    </td>   
                     <td width="110" id="line_td">
                        <? 
                            echo create_drop_down( "cbo_line", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                        ?>
                    </td>
                 
                    <td width="100">
                    <input type="button" id="show_button" class="formbutton" style="width:100px" value="Show" onClick="fn_report_generated(1)" />
                    </td>                          
                </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div id="chart_container"></div>
 </form>    
</body>
<script>
    set_multiselect('cbo_floor','0','0','','0');
    // setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor,'0');getLineId();") ,3000)];   

</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
