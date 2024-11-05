<?
/*-------------------------------------------- Comments
Purpose         :   This Form Will Create Item Creation Wise Dyes And Chemical Report.
                
Functionality   :   
JS Functions    :
Created by      :   Md. Minul Hasan
Creation date   :   13-08-2022
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        :   Passion to write neat and clean code!
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Process Wise Yarn History Report","../../", 1, 1, $unicode,1,1); 
?>  
<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    function generate_report(rpt_type)
    {
        if( form_validation('cbo_company_id*txt_date','Company*Item Cetagory*Date')==false )
        {
            return;
        }

        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_item_cat_id*cbo_model*txt_date',"../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
        //alert(data);
        freeze_window(3);
        http.open("POST","requires/item_creation_wise_dyes_and_chemical_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_report_reponse; 

    }

    function generate_report_reponse()
    {   
        if(http.readyState == 4) 
        {
            var reponse=trim(http.responseText).split("####");
            $("#report_container2").html(reponse[0]);

            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            setFilterGrid("tbl_list_search",-1);
            show_msg('3');
            release_freezing();

        }   
    }

    function new_window()
    {
        $(".flt").css("display","none");
        //document.getElementById('scroll_body').style.overflow="auto";
        //document.getElementById('scroll_body').style.maxHeight="none";
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
            '<html><head><title></title><link rel="stylesheet" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
        //document.getElementById('scroll_body').style.overflowY="scroll";
        //document.getElementById('scroll_body').style.maxHeight="380px";
        $(".flt").css("display","block");
    }

</script>
</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>          
    <form name="orderwisegreyfabricstock_1" id="orderwisegreyfabricstock_1" autocomplete="off" > 
    <h3 style="width:780px; margin-top:10px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:780px;">
                <table class="rpt_table" width="780" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>        
                            <th class="must_entry_caption">Company</th>                                
                            <th>Item Category</th>
                            <th>Model</th>
                            <th>Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:80px" class="formbutton" onClick="reset_form('orderwisegreyfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <? 
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/item_creation_wise_dyes_and_chemical_report_controller',this.value+'_'+document.getElementById('cbo_item_cat_id').value, 'load_drop_down_model', 'model_td' );" );
                            ?>                            
                        </td>
                        <td> 

                            <?
                                echo create_drop_down( "cbo_item_cat_id", 155, $item_category,"", "1", "--- Select---", 0, "load_drop_down( 'requires/item_creation_wise_dyes_and_chemical_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_model', 'model_td' );","","7,5,6","","","" );
                                
                            ?>
                        </td>
                        <td id="model_td">
                            <?
                                echo create_drop_down( "cbo_model", 155, $blank_array,"", 1, "--Select Model--", 0, "",0 );
                            ?>
                        </td>
                        <td>            
                            <input type="text" name="txt_date" id="txt_date" class="text_boxes" value="<?php echo date("d-m-Y");?>"  readonly/>             
                        </td> 
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:80px" class="formbutton" />
                        </td>
                    </tr>
                </table> 
            </fieldset> 
        </div>
        <div id="report_container" align="center" style="margin:5px 0;"></div>
        <div style="width:1220px;" id="report_container2"></div>   
    </form>    
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
