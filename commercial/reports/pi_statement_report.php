<?php
    /**
     * Purpose: PI statement reports
     * Created by Mohammad Shafiqur Rahman.
     * User: shafiq-sumon
     * Date: 6/7/2018
     * Time: 10:17 AM
     */
    session_start();
    if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

    require_once('../../includes/common.php');
    extract($_REQUEST);
    $_SESSION['page_permission']=$permission;
    //-------------------------------------------------  html start -----------------------------------
    echo load_html_head_contents("PI Statement Reports","../../", 1, 1, $unicode,1,1);
?>
    <script>
        var permission='<? echo $permission; ?>';
        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

        /**
         * Open PI numbers against the company name
         */
        function openmypage_pi()
        {
           var cbo_company = $("#cbo_company_name").val();
           var category_ids = $("#cbo_item_category_id").val();
           var supplier_ids = $("#cbo_supplier").val();

            if(form_validation("cbo_company_name","Company Name")==false){
                return;
            }

            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_statement_report_controller.php?action=pi_number_popup&company='+cbo_company+"&category_ids="+category_ids, "PI Number", 'width=450px,height=380px,center=1,resize=0,scrolling=0','../');
            emailwindow.onclose=function()
            {
              var theform=this.contentDoc.forms[0];
              var pi_id=this.contentDoc.getElementById("hidden_pi_id").value;
              var pi_number=this.contentDoc.getElementById("hidden_pi_number").value;
              $("#pi_no_id").val(pi_id);
              $("#sys_id").val(pi_id);
              $("#pi_no").val(pi_number);

            }
        }

        function openmypage_sys_id()
        {
           var cbo_company = $("#cbo_company_name").val();

            if(form_validation("cbo_company_name","Company Name")==false){
                return;
            }

            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_statement_report_controller.php?action=openmypage_sys_id&company='+cbo_company, "System ID Number", 'width=450px,height=380px,center=1,resize=0,scrolling=0','../');
            emailwindow.onclose=function()
            {
              var theform=this.contentDoc.forms[0];
              var pi_id=this.contentDoc.getElementById("hidden_pi_id").value;
              var pi_number=this.contentDoc.getElementById("hidden_pi_number").value;
              $("#pi_no_id").val(pi_id);
              $("#sys_id").val(pi_id);
              $("#pi_no").val(pi_number);

            }
        }

        /**
         * show details report
         */
        function generate_report(type)
        {
            //alert("hello fun__front12");
            var cbo_company = $("#cbo_company_name").val();
            var pi_no = $("#pi_no").val();
            var sys_id = $("#sys_id").val();
            var cbo_based_on = $("#cbo_based_on").val();
            var from_date = $("#txt_date_from").val();
            var to_date = $("#txt_date_to").val();

            if(form_validation("cbo_company_name","Company Name")==false){
                return;
            }
            else
            {
                var report_title = $('.form_caption').html();
                if(pi_no == "" && sys_id ==""  && from_date == "" && to_date == "")
                {
                    alert("Either Select PI NO Or System ID Or Date Range");
                    $("#pi_no").focus();
                    //$("#txt_date_from").focus();
                    //$("#txt_date_to").focus();
                    return;
                }
                else if(pi_no != "")
                {
                    var data  = "action=generate_report"+get_submitted_data_string('cbo_company_name*pi_no_id*pi_no*txt_date_from*txt_date_to*cbo_item_category_id*cbo_supplier*cbo_approval_status','../../')+"&report_title="+report_title+"&type="+type;
                    //alert(data);return;
                }
                else if(sys_id != "")
                {
                    var data  = "action=generate_report"+get_submitted_data_string('cbo_company_name*sys_id*txt_date_from*txt_date_to*cbo_item_category_id*cbo_supplier*cbo_approval_status','../../')+"&report_title="+report_title+"&type="+type;
                    //alert(data);return;
                }
                else
                {
                    var data  = "action=generate_report"+get_submitted_data_string('cbo_company_name*pi_no_id*txt_date_from*txt_date_to*cbo_item_category_id*cbo_supplier*cbo_approval_status*cbo_based_on','../../')+"&report_title="+report_title+"&type="+type;
                    //alert(data);return;
                }

                freeze_window(3);
                http.open("POST","requires/pi_statement_report_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = fn_report_generated_reponse;
            }
        }


        /**
         * response function
         */
        function fn_report_generated_reponse()
        {
            if (http.readyState == 4)
			{
                var reponse = trim(http.responseText).split("**");
                $("#report_container2").html(reponse[0]);
                //alert(reponse[0]);
                //document.getElementById('report_container').innerHTML=report_convert_button('../../');
                document.getElementById('report_container').innerHTML = '<a href="requires/' + reponse[1] +'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;' +
                                                                        '<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" target="_blank" style="width:100px"/>';
              	document.getElementById('report_container2').innerHTML=reponse[0];
                show_msg('3');
                //setFilterGrid("table_body_id",-1); //,tableFilters
                release_freezing();
            }
        }

        function new_window()
        {
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
           // $('#scroll_body tr:first').hide();
            var w = window.open("Surprise", "#");

            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
                     '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body></html>');


            d.close();

            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="350px";
            //$('#scroll_body tr:first').show();
        }
		
		
		
        function openmypage_image(pi_id)
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_statement_report_controller.php?action=image_file&pi_id='+pi_id, "File/Image", 'width=450px,height=380px,center=1,resize=0,scrolling=0','../');
            emailwindow.onclose=function()
            {
            }
        }

        function openmypage_wo(pi_id)
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_statement_report_controller.php?action=wo_details&pi_id='+pi_id, "WO Wiew", 'width=230px,height=250px,center=1,resize=0,scrolling=0','../');
            emailwindow.onclose=function()
            {
            }
        }
        function openmypage_pi_wo(pi_id)
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_statement_report_controller.php?action=page_pi_wo&pi_id='+pi_id, "WO Wiew", 'width=630px,height=250px,center=1,resize=0,scrolling=0','../');
            emailwindow.onclose=function()
            {
            }
        }
		
        function openmypage_approve(pi_id)
        {
            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'requires/pi_statement_report_controller.php?action=approve_details&pi_id='+pi_id, "Approval History Wiew", 'width=530px,height=250px,center=1,resize=0,scrolling=0','../');
            emailwindow.onclose=function()
            {
            }
        }
		

    </script>
    </head>
    <body onLoad="set_hotkey();">
        <div style="width: 100%; text-align: center">
            <?php echo load_freeze_divs("../../", $permission);?>
            <form name="pi_statement_report" id="pi_statement_report" autocomplete="off">

                <h3 id="accordion_h1" style="width:1200px; text-align: left; margin: 0 auto" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
                <div style="width:1200px; text-align: center; margin:0 auto;" id="content_search_panel">

                    <fieldset style="width:1200px;">
                        <table class="rpt_table" width="1195" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                            <thead>
                            <tr>
                                <th width="150" class="must_entry_caption">Company</th>
                                <th width="170" >Item Category</th>
                                <th width="170" >Supplier</th>
                                <th width="150" >PI No</th>
                                <th width="150" >System ID</th>
                                <th width="120" >Approval Status</th>
                                <th width="120">Based On</th>
                                <th width="165" >Date Range</th>
                                <th ><input type="reset" name="res" id="res" value="Reset" style="width:90px" class="formbutton" onClick="reset_field()" /></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="general">
                                <td>
                                    <?
                                        echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "get_php_form_data(this.value,'print_button_variable_setting','requires/pi_statement_report_controller' );" );
                                    ?>
                                </td>
                                <td id="cat_td">
                                    <?php 
                                    echo create_drop_down( "cbo_item_category_id", 170,$item_category,"", 0, "", $selected, "","","","","","");
                                    ?> 
                                </td>
                                <td id="supplier_td">
                                    <?php 
                                    echo create_drop_down( "cbo_supplier", 190, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 order by supplier_name","id,supplier_name", 1, "--Select Supplier--", $selected, "" );                           
                                    ?> 
                                </td>
                                <td align="center">
                                    <input style="width:137px;"  name="pi_no" id="pi_no"  ondblclick="openmypage_pi()"  class="text_boxes" placeholder="Browse or Write"   />
                                    <input type="hidden" name="pi_no_id" id="pi_no_id"/>
                                </td>
                                <td align="center">
                                    <input style="width:137px;"  name="sys_id" id="sys_id"  ondblclick="openmypage_sys_id()"  class="text_boxes" placeholder="Browse or Write"   />
                                </td>
                                <td>
                                    <? 
                                        $approval_status_flag=array(1=>"Un Approved",2=>"Partial Approved",3=>"Approved");
                                        echo create_drop_down( "cbo_approval_status", 120,$approval_status_flag,"", 1, "-- All --", $selected, "","","","","","");
                                    ?> 
                                </td>
                                <td>
                                    <?
                                        $based_on_arr=array(1=>"PI Date Wise", 2=>"PI Insert Date Wise");
                                        echo create_drop_down( "cbo_based_on", 120, $based_on_arr,"", 1, "--- Select ---", 1);
                                    ?>
                                </td>
                                <td style="text-align: center">
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;"/>
                                    To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px;"/>
                                </td>

                                <td>
                                    <!-- <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:50px" class="formbutton" />
                                    <input type="button" name="search" id="search" value="Show-2" onClick="generate_report(2)" style="width:50px" class="formbutton" /> -->
                                    <span id="button_data_panel"></span>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" align="center"><? echo load_month_buttons(1);  ?></td>                    
                                    <td colspan="3">
                                        <span id="button_data_panel2"></span>
                                   </td>
                                </tr>
                            </tfoot>
                        </table>
                    </fieldset>
                </div>
                <div id="report_container" style="margin-top: 15px;text-align: center"></div>
                <div id="report_container2" ></div>
            </form>
        </div>
    </body>
    <script>
        set_multiselect('cbo_item_category_id','0','0','','0');
        set_multiselect('cbo_supplier','0','0','','0');
    </script>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
