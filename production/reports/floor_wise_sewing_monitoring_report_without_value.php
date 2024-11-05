<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   Report.
Functionality   :   
JS Functions    :
Created by      :   Md. Thorat Islam 
Creation date   :   15-02-2022
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
                col_0: "none", 
            } 
                    
    var tableFilters1 = 
            {
                col_0: "none", 
            } 
                    
    function fn_report_generated(type)
    {
		if (form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Floor Name*From Date*To Date')==false)
        {
            return;
        }
        else
        {
            var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*hidden_line_id*txt_date_from*txt_date_to',"../../");
            freeze_window(3);
            http.open("POST","requires/floor_wise_sewing_monitoring_report_without_value_controller.php",true);
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
            // alert(reponse[2]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
            //document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
            //append_report_checkbox('table_header_1',1);       
            //setFilterGrid("table_body",-1,tableFilters);
            
            //setFilterGrid("table_body1",-1,tableFilters);     
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
        $("#table_body tr:first").show();
    }    


    function show_line_remarks(company_id,order_id,floor_id,line_no,prod_date,action)
    {
        
        popup_width='550px'; 
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/floor_wise_sewing_monitoring_report_without_value_controller.php?order_id='+order_id+'&company_id='+company_id+'&floor_id='+floor_id+'&sewing_line='+line_no+'&prod_date='+prod_date+'&action='+action, 'Detail Veiw','width='+popup_width+', height=270px,center=1,resize=0,scrolling=0','../');
    }
            
   
    function getCompanyId() 
    {
        var company_id = document.getElementById('cbo_company_name').value;
        if(company_id !='') 
        {
          var data="action=load_drop_down_location&data="+company_id;
          http.open("POST","requires/floor_wise_sewing_monitoring_report_without_value_controller.php",true);
          http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
          http.send(data); 
          http.onreadystatechange = function(){
              if(http.readyState == 4) 
              {
                  var response = trim(http.responseText);
                  $('#location_td').html(response);
                 // getFloorId();
              }          
          };
        }     
    }

    function getFloorId() 
    {
        var company_id  = document.getElementById('cbo_company_name').value;
        var location_id = document.getElementById('cbo_location').value;
        var floor_id    = document.getElementById('cbo_floor').value;
        if(company_id !='') 
        {
          var data="action=load_drop_down_line&data="+floor_id+"_"+location_id+"_"+company_id;
          http.open("POST","requires/floor_wise_sewing_monitoring_report_without_value_controller.php",true);
          http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
          http.send(data); 
          http.onreadystatechange = function(){
              if(http.readyState == 4) 
              {
                  var response = trim(http.responseText);
                  $('#floor_td').html(response);
              }          
          };
        }     
    }

    function openmypage_line()
    {
        if( form_validation('cbo_company_name*txt_date_from*txt_date_to','Company*Date From*Date To')==false)
        {
            return;
        }
        var company = $("#cbo_company_name").val();   
        var location=$("#cbo_location").val();
        var floor_id=$("#cbo_floor").val();
        var line_id=$("#hidden_line_id").val();
        var date_from=$("#txt_date_from").val();
        var date_to=$("#txt_date_to").val();

        var page_link='requires/floor_wise_sewing_monitoring_report_without_value_controller.php?action=line_search_popup&company='+company+'&location='+location+'&floor_id='+floor_id+'&date_from='+date_from+'&date_to='+date_to+'&line_id='+line_id; 
        
        var title="Search line Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=310px,height=370px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0]; 
            var prodID=this.contentDoc.getElementById("txt_selected_id").value;
            
            var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
            $("#cbo_line").val(prodDescription);
            $("#hidden_line_id").val(prodID); 
        }
    }

</script>                     
                          
</head>
 
<body onLoad="set_hotkey();">

<form id="lineWiseProductionReport_1">
    <div style="width:100%;" align="center">    
    
        <? echo load_freeze_divs ("../../",'');  ?>
               
         <fieldset style="width:850px;">
            <legend>Search Panel</legend>
            <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" border="1" align="center">
               <thead>                    
                    <tr>
                        <th class="must_entry_caption" width="160">Working Company</th>
                        <th width="130">Location</th>
                        <th width="130" >Floor</th>
                        <th width="130">Line No</th> 
                        <th width="200" id="search_text_td" class="must_entry_caption">Prod. Date</th>
                        <th width="80"><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                    </tr>    
                 </thead>
                <tbody>
                    <tr >
                        <td id="company_td"> 
                            <?
                                echo create_drop_down( "cbo_company_name", 180, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select --", $selected, "" );
                            ?>
                        </td>                   
                        <td id="location_td">
                            <? 
                                echo create_drop_down( "cbo_location", 130, $blank_array,"", 0, "-- Select --", $selected, "", 1, "" );
                            ?>
                        </td>
                        <td id="floor_td">
                            <? 
                                // $floor_sql = sql_select("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name");
                                $floor_sql = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 group by floor_name order by floor_name","id","floor_name"); 
                                echo create_drop_down( "cbo_floor", 130, $floor_sql,"", 0, "-- Select --", $selected, "", 1, "" );
                            ?>
                        </td>
                         <td id="line_td">
                            <? 
                                // echo create_drop_down( "cbo_line", 130, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                            ?>
                                <input type="text" id="cbo_line"  name="cbo_line"  style="width:120px" class="text_boxes" onDblClick="openmypage_line()" placeholder="Browse Line"  readonly/>
                               <input type="hidden" id="hidden_line_id" name="hidden_line_id" />
                        </td>  

                        <td align="center">
                        <input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:70px"/>                                             
                         To
                        <input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:70px"/>
                        </td>
                     
                        <td width="80">
                            <input type="button" id="show_button" class="formbutton" style="width:80px" value="Show" onClick="fn_report_generated(0)" />
                        </td>
                    </tr>                
                    <tr>
                        <td colspan="6" align="center"><? echo load_month_buttons(1);  ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    
    <div id="report_container" align="center" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </form>    
</body>
<script>
    set_multiselect('cbo_company_name','0','0','','0');
    setTimeout[($("#company_td a").attr("onclick","disappear_list(cbo_company_name,'0');getCompanyId();") ,3000)];   
    set_multiselect('cbo_floor','0','0','','0');
    setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor,'0');getFloorId();") ,3000)];   

</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
