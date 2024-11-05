<?php

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;

//------------------------------------------------------------------------------------
echo load_html_head_contents("Display Report", "../../", 1, 1,$unicode,1,1);

?>  

<script>
    
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission = '<? echo $permission; ?>'; 
    
    function fn_report_generated()
    {   
        if (form_validation('cbo_company_name*txt_date','Comapny Name*From Date')==false)
        {
            return;
        }
        else
        {            
            var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_location*cbo_floor*cbo_line*cbo_buyer_name*txt_style_no*txt_order_no*txt_job_no*txt_date',"../../");
            freeze_window(3);
            http.open("POST","requires/display_2_controller.php",true);
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
            $('#report').html(reponse[0]);
            //alert(reponse[1]);
            document.getElementById('report2').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px; target=_blank;"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';
            release_freezing();
        }    
    }

    function new_window()
    {
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><style type="text/css">.block_div { width:auto;height:auto;text-wrap:normal;vertical-align:bottom;display: block;position: !important;-webkit-transform: rotate(-90deg);} </style><body>'+document.getElementById('report').innerHTML+'</body</html>'); 
        d.close();
    } 

    function getFloorId() 
    {
        var cbo_company_name = document.getElementById('cbo_company_name').value;
        var location_id = document.getElementById('cbo_location').value;
        var floor_id = document.getElementById('cbo_floor').value;
        var txt_date = document.getElementById('txt_date').value;
        if(cbo_company_name !='') 
        {
            var data="action=load_drop_down_line&data="+floor_id+'_'+location_id+'_'+cbo_company_name+'_'+txt_date;
            //alert(data);die;
            http.open("POST","requires/display_2_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data); 
            http.onreadystatechange = function()
            {
                if(http.readyState == 4) 
                {
                    var response = trim(http.responseText);
                    $('#line_td').html(response);
                    // load_drop_down( 'requires/display_2_controller', working_company_id, 'load_drop_down_buyer', 'buyer_td' );
                    // set_multiselect('cbo_line','0','0','','0');
                }          
            };
        }         
    } 

    

</script>      
</head>
 
<body>
    <form id="displayReport">
        <div style="width:100%;" align="center">        
            <? echo load_freeze_divs ("../../",'');  ?>                   
            <fieldset style="width:850px;">
                <legend>Search Panel</legend>
                <table class="rpt_table" width="1050px" cellpadding="0" cellspacing="0" border="1" align="center">
                    <thead>                    
                        <tr>
                            <th class="must_entry_caption">Company Name</th>
                            <th id="search_text_td" class="must_entry_caption">Prod. Date</th>
                            <th>Location</th>
                            <th>Floor</th>
                            <th>Line</th>
                            <th>Buyer Name</th>                                                
                            <th>Job No</th>                                                
                            <th>Order No</th>                                                
                            <th>Style No</th>
                            <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
                        </tr>    
                    </thead>
                    <tbody>
                        <tr>
                            <td width="140"> 
                                <?
                                    echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/display_2_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/display_2_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>
                            </td>  
                            <td width="">
                                <input name="txt_date" id="txt_date" class="datepicker" style="width:114px" onChange="load_drop_down( 'requires/display_2_controller',document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_line', 'line_td' );" readonly >
                            </td>                  
                            <td width="110" id="location_td">
                                <? 
                                    echo create_drop_down( "cbo_location", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                                ?>
                            </td>
                            <td width="110" id="floor_td">
                                <? 
                                    echo create_drop_down( "cbo_floor", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                                ?>
                            </td>  
                             <td width="110" id="line_td">
                                <? 
                                    echo create_drop_down( "cbo_line", 110, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
                                ?>
                            </td>
                            <td width="110" id="buyer_td">
                            <? 
                                echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" );
                            ?>
                            </td>
                            <td width="100">
                                <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:105px" />
                            </td> 
                            <td width="100">
                                <input type="text" id="txt_order_no" name="txt_order_no" class="text_boxes" style="width:105px" />
                            </td> 
                           
                            <td width="130">
                                <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:118px" />
                            </td>  
                            <td width="70">
                                <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated()" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div id="report2" align="center"></div>
        <div id="report" align="center"></div>
        
    </form>
</body>
<script>
    set_multiselect('cbo_floor','0','0','','0');
    setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor,'0');") ,3000)];
</script> 
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
