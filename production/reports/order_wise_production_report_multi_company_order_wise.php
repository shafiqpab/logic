<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Form Will Create Order wise Production Report.
Functionality   :
JS Functions    :
Created by      :   Helal Uddin
Creation date   :   26-02-2020
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
echo load_html_head_contents(" Multi Company Order wise Production Report", "../../", 1, 1,$unicode,1,1);

?>
<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

    var tableFilters1 =
    {
        col_0: "none",col_9: "select",display_all_text: " -- All --",col_10: "select",display_all_text: " -- All --",col_12: "select",display_all_text: " -- All --",col_36: "select",display_all_text: " -- All --",
        col_operation: {
            id: ["total_order_quantity","order_value","tot_plan_cut","total_cutting","total_cutting_bal","total_emb_issue","total_emb_receive","total_sewing_input","total_sewing_out","total_iron_qnty","total_re_iron_qnty","total_finish_qnty","total_rej_value_td","total_out","ship_out_value"],
            col: [15,17,24,25,26,28,29,30,31,32,33,34,36,37,38],
            operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    var tableFilters2 =
    {
        col_0: "none",col_28: "select",display_all_text: " -- All --",col_29: "select",display_all_text: " -- All --",
        col_operation: {
            id: ["total_order_quantity","total_cutting","total_cutting_bal","total_emb_issue","total_emb_receive","total_sewing_input","total_sewing_out","total_iron_qnty","total_re_iron_qnty","total_finish_qnty","total_rej_value_td","total_out","total_shortage","ship_status"],
            //col: [9,16,17,19,20,21,22,23,24,25,27,28,29,30],
            col: [10,17,18,20,21,22,23,24,25,26,28,29,30,31],
            operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }
    var tableFilters3 =
    {
        col_0: "none",col_27: "select",display_all_text: " -- All --",col_28: "select",display_all_text: " -- All --",
        col_operation: {
            id: ["total_order_quantity","total_cutting","total_cutting_bal","total_emb_issue","total_emb_receive","total_sewing_input","total_sewing_out","total_iron_qnty","total_re_iron_qnty","total_finish_qnty","total_rej_value_td","total_out","total_shortage","ship_status"],
            //col: [7,12,14,15,16,17,18,19,20,22,23,24],
            col: [10,15,16,18,19,20,21,22,23,24,26,27,28,29],
            operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    var tableFilters4 =
    {
        col_0: "none",col_29: "select",display_all_text: " -- All --",col_30: "select",display_all_text: " -- All --",
        col_operation: {
            id: ["total_order_quantity","total_cutting","total_cutting_bal","total_emb_issue","total_emb_receive","total_sewing_input","total_sewing_out","total_iron_qnty","total_re_iron_qnty","total_finish_qnty","total_rej_value_td","total_out","total_shortage","ship_status"],
            //col: [7,14,16,17,18,19,20,21,22,24,25,26],
            col: [10,17,18,20,21,22,23,24,25,26,28,29,30,31],
            operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    var tableFilters5 =
    {
        col_0: "none",col_27: "select",display_all_text: " -- All --",col_28: "select",display_all_text: " -- All --",
        col_operation: {
            id: ["total_order_quantity","total_cutting","total_cutting_bal","total_emb_issue","total_emb_receive","total_sewing_input","total_sewing_out","total_iron_qnty","total_re_iron_qnty","total_finish_qnty","total_rej_value_td","total_out","total_shortage","ship_status"],
            //col: [6,11,13,14,15,16,17,18,19,21,22,23],
            //col: [10,15,16,18,19,20,21,22,23,24,26,27,28],
            col: [10,17,18,20,21,22,23,24,25,26,28,29,30],
            operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    var tableFilters6 =
    {
        //col_0: "none", col_9: "select", col_30: "select",display_all_text: " -- All --",col_31: "select",display_all_text: " -- All --",
        col_operation: {
            id: ["total_order_quantity","total_cutting","total_cutting_bal","total_emb_issue","total_emb_receive","total_sewing_input","total_sewing_out","total_iron_qnty","total_re_iron_qnty","total_finish_qnty","total_rej_value_td","total_out","total_shortage","ship_status"],
            col: [15,20,21,23,24,25,26,27,28,29,31,32,33,44],
            // col: [13,18,19,21,22,23,24,25,26,27,29,30,31,42],
            operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
            write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
        }
    }

    function fn_report_generated()
    {
    //alert(4);return;
        var order_no=document.getElementById('txt_order_no').value;
        var file_no=document.getElementById('txt_file_no').value;
        var ref_no=document.getElementById('txt_ref_no').value;
        if(trim(order_no)!="" || trim(file_no)!="" || trim(ref_no)!="") //Aziz
        {
            if(form_validation('cbo_company_name*cbo_type','Company*Type')==false)
            {
                return;
            }
        }
        else //----
        {
             if(form_validation('cbo_company_name*cbo_type*txt_date_from*txt_date_to','Comapny Name*Report Type*From Date*To Date')==false)
            return;
        }

        var data="action=report_generate"+get_submitted_data_string('cbo_garments_nature*cbo_company_name*cbo_buyer_name*cbo_team_name*cbo_team_member*cbo_location*cbo_floor*cbo_year*cbo_type*txt_date_from*txt_date_to*txt_order_no*txt_file_no*txt_ref_no*shipping_status*cbo_agent*cbo_order_status*cbo_active_status*txt_item_catgory*cbo_season_name*cbo_string_search_type',"../../");
        freeze_window(3);
        http.open("POST","requires/order_wise_production_report__multi_company_order_wise_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }

    function fn_report_generated_reponse()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("****");

            show_msg('3');
            release_freezing();
            $('#report_container2').html(reponse[0]);
            // document.getElementById('report_container').innerHTML=report_convert_button('../../');
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

            var type=$('#cbo_type').val();

            append_report_checkbox('table_header_1',1);
            if(type==1) setFilterGrid("table_body",-1,tableFilters1);
            else if(type==2) setFilterGrid("table_body",-1,tableFilters2);
            else if(type==3) setFilterGrid("table_body",-1,tableFilters3);
            else if(type==4) setFilterGrid("table_body",-1,tableFilters4);
            else if(type==5 || type==7) setFilterGrid("table_body",-1,tableFilters5);
            else if(type==6) setFilterGrid("table_body",-1,tableFilters6);
        }
    }

    function openmypage_remark(po_break_down_id,item_id,country_id,action)
    {
        var garments_nature = $("#cbo_garments_nature").val();
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report__multi_company_order_wise_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Remarks Veiw', 'width=550px,height=450px,center=1,resize=0,scrolling=0','../');
    }

    function openmypage_order(po_break_down_id,company_name,item_id,country_id,action)
    {
        //var garments_nature = $("#cbo_garments_nature").val();
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report__multi_company_order_wise_controller.php?po_break_down_id='+po_break_down_id+'&company_name='+company_name+'&item_id='+item_id+'&country_id='+country_id+'&action='+action, 'Order Quantity', 'width=750px,height=350px,center=1,resize=0,scrolling=0','../');
    }

    function openmypage_country_ship_date(po_break_down_id,item_id,action,production_type,floor_id,dateOrLocWise,country_id)
    {
        if(production_type==2 || production_type==3)
            var popupWidth = "width=1050px,height=350px,";
        else if (production_type==10)
            var popupWidth = "width=550px,height=420px,";
        else
            var popupWidth = "width=750px,height=420px,";

        if (production_type==2)
        {
            var popup_caption="Embl. Issue Details";
        }
        else if (production_type==3)
        {
            var popup_caption="Embl. Rec. Details";
        }
        else
        {
            var popup_caption="Production Quantity";
        }

        emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_production_report__multi_company_order_wise_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&production_type='+production_type+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
    }

    function openmypage_rej(po_id,item_id,action,location_id,floor_id,reportType,country_id)
    {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report__multi_company_order_wise_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&reportType='+reportType+'&country_id='+country_id, 'Reject Quantity', 'width=510px,height=400px,center=1,resize=0,scrolling=0','../');
    }

    function openmypage(po_break_down_id,item_id,action,location_id,floor_id,dateOrLocWise,country_id)
    {
        if(action==2 || action==3)
            var popupWidth = "width=1050px,height=350px,";
        else if (action==10)
            var popupWidth = "width=550px,height=420px,";
        else
            var popupWidth = "width=800px,height=470px,";

        if (action==2)
        {
            var popup_caption="Embl. Issue Details";
        }
        else if (action==3)
        {
            var popup_caption="Embl. Rec. Details";
        }
        else
        {
            var popup_caption="Production Quantity";
        }

        emailwindow=dhtmlmodal.open('EmailBox','iframe','requires/order_wise_production_report__multi_company_order_wise_controller.php?po_break_down_id='+po_break_down_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&dateOrLocWise='+dateOrLocWise+'&country_id='+country_id, popup_caption, popupWidth+'center=1,resize=0,scrolling=0','../');
    }

    function openmypage_rej(po_id,item_id,action,location_id,floor_id,reportType,country_id)
    {
        var company_name=$('#cbo_company_name').val();
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/order_wise_production_report__multi_company_order_wise_controller.php?po_id='+po_id+'&item_id='+item_id+'&action='+action+'&location_id='+location_id+'&floor_id='+floor_id+'&reportType='+reportType+'&country_id='+country_id+'&company_name='+company_name, 'Reject Quantity', 'width=660px,height=400px,center=1,resize=0,scrolling=0','../');
    }

    function disable_order( val )
    {
        $('#txt_order_no').val('');
        $('#txt_file_no').val('');
        $('#txt_ref_no').val('');

        if(val==1)
        {
            $('#txt_file_no').removeAttr('disabled','disabled');
            $('#txt_ref_no').removeAttr('disabled','disabled');
        }
        else
        {
            $('#txt_file_no').attr('disabled','disabled');
            $('#txt_ref_no').attr('disabled','disabled');
        }

        if(val==5)
        {
            $('#Order_td').html('Style Ref.');
        }
        else if(val==7)
        {
            $('#Order_td').html('Job No');
        }
        else
        {
            $('#Order_td').html('Order No');
        }
    }


    function progress_comment_popup(po_id,template_id,tna_process_type)
    {
        var data="action=update_tna_progress_comment"+
                                '&po_id='+"'"+po_id+"'"+
                                '&template_id='+"'"+template_id+"'"+
                                '&tna_process_type='+"'"+tna_process_type+"'"+
                                '&permission='+"'"+permission+"'";

        http.open("POST","../../reports/management_report/merchandising_report/requires/shipment_date_wise_wp_report_controller.php",true);

        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_progress_comment_reponse;
    }

    function generate_progress_comment_reponse()
    {
        if(http.readyState == 4)
        {
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
    '<html><head><title></title></head><body>'+http.responseText+'</body</html>');//<link rel="stylesheet" href="../../css/style_common.css" type="text/css" />
            d.close();
        }
    }

    function openmypage_image(page_link,title)
    {
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
        emailwindow.onclose=function()
        {
        }
    }

    $(function(){
        $("#cbo_type").change(function(){
            if($(this).val()==6)
            {
                $("#caption").text('Country Ship Date');
            }
            else
            {
                $("#caption").text('Shipment Date');
            }
        });
    })

</script>
</head>
<body onLoad="set_hotkey();">
<form id="dateWiseProductionReport_1">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",'');  ?>
        <h3 style="width:1420px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1410px;">
                <table align="center" cellspacing="0" cellpadding="0" width="100%" border="1" rules="all" class="rpt_table" >
                <thead>
                <th colspan="18" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </thead>
                    <thead>
                        <th class="must_entry_caption">Company Name</th>
                        <th>Buyer Name</th>
                        <th>Season</th>
                        <th>File No</th>
                        <th>Internal Ref.</th>
                        <th>Team Name</th>
                        <th>Team Member</th>
                        <th>Location</th>
                        <th>Floor</th>
                        <th>Year</th>
                        <th class="must_entry_caption">Report Category</th>
                        <th>Product Category</th>
                        <th id="Order_td">Order No</th>
                        <th>Order Status</th>
                        <th>Active Status</th>
                        <th>Gmts. Nature</th>
                        <th>Ship Status</th>
                        <th>Agent Name</th>
                    </thead>
                    <tbody>
                        <tr class="general">
                            <td>
                                <?
                                    echo create_drop_down( "cbo_company_name", 110, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/order_wise_production_report__multi_company_order_wise_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/order_wise_production_report__multi_company_order_wise_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/order_wise_production_report__multi_company_order_wise_controller', this.value, 'load_drop_down_agent', 'agent_td');" );
                                ?>
                            </td>
                            <td id="buyer_td">
                                <?
                                    echo create_drop_down( "cbo_buyer_name", 110, $blank_array,"", 1, "-Select Buyer-", $selected, "",1,"" );
                                ?>
                            </td>
                            <td id="season_td">
                                <?
                                    echo create_drop_down( "cbo_season_name", 70, $blank_array,"", 1, "-Season-", $selected, "",1,"" );
                                ?>
                            </td>
                            <td>
                                <input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:55px" placeholder="Write" >
                            </td>
                            <td>
                                <input name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:55px" placeholder="Write" >
                            </td>
                            <td>
                                <?
                                    echo create_drop_down( "cbo_team_name", 100, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-Select Team-", $selected, " load_drop_down( 'requires/order_wise_production_report__multi_company_order_wise_controller', this.value, 'load_drop_down_team_member', 'team_td' )" );
                                ?>
                            </td>
                            <td id="team_td">
                                <?
                                    echo create_drop_down( "cbo_team_member", 100, $blank_array,"", 1, "-Select Member-", $selected, "" );
                                ?>
                            </td>
                            <td id="location_td">
                                <?
                                    echo create_drop_down( "cbo_location", 100, $blank_array,"", 1, "-Select Location-", $selected, "",1,"" );
                                ?>
                            </td>
                            <td id="floor_td">
                                <?
                                    echo create_drop_down( "cbo_floor", 100, $blank_array,"", 1, "-Select Floor-", $selected, "",1,"" );
                                ?>
                            </td>

                            <td>
                                <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 55, $year,"", 1, "-All-", 0, "",0 );
                                ?>
                            </td>
                            <td>
                                <?
                                    //$arr=array(1=>"Order Wise",2=>"Order Location & Floor Wise",3=>"Order Country Wise",4=>"Order Country Location & Floor Wise",5=>"Style Wise",6=>"Order Country Shipdate Wise",7=>"Job Wise");
                                    $arr=array(1=>"Order Wise",2=>"Order Location & Floor Wise",3=>"Order Country Wise",4=>"Order Country Location & Floor Wise",5=>"Style Wise",6=>"Country Shipdate Wise",7=>"Job Wise");
                                    echo create_drop_down( "cbo_type", 110, $arr,"", 1, "-- Select --", 1, "disable_order(this.value);",0,"","" );
                                    //"disable_order(this.value);",0,"1,5,6,7",""
                                ?>
                            </td>


                            <td><? echo create_drop_down( "txt_item_catgory", 100, $product_category,"", 1, "-- Select Product Category --", 0, "","","" ); ?></td>


                            <td>
                                <input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:60px" placeholder="Write" >
                            </td>
                             <td>
                            <?
                                $order_status_arr=array(0=>"All",1=>"Confirmed",2=>"Projected");
                                echo create_drop_down( "cbo_order_status", 80, $order_status_arr,"", 0, "", 1, "" );
                            ?>
                        </td>

                        <td>
                            <?
                                $active_status=array(1=>"Active",2=>"In-Active",3=>"Cancel",4=>"All");
                                echo create_drop_down( "cbo_active_status", 80, $active_status,"", 0, "", 1, "" );
                            ?>
                        </td>

                            <td>
                                <?
                                    $arr = array(1=>"ALL",2=>"Woven",3=>"Knit");
                                    echo create_drop_down( "cbo_garments_nature", 55, $arr,"", 0, "-- Select --", $selected, "",0,"" );
                                ?>
                            </td>
                            <td>
                                <?
                                      $ship_status_arr = array(1=>"Full Pending",2=>"Partial Shipment",3=>"Full Shipment/Closed");
                                      echo create_drop_down( "shipping_status", 50, $ship_status_arr,"", 1,"-All-","", "",0,"" );
                                    //  echo create_drop_down( "shipping_status", 50, $shipment_status,"", 0, "", 0, "",0,'','','','','' );
                                ?>
                            </td>
                            <td id="agent_td">
                                <?
                                    echo create_drop_down( "cbo_agent", 90, $blank_array,"", 1, "-Agent-", $selected, "" );
                                ?>
                            </td>
                      </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="9">
                                <? echo load_month_buttons(1); ?>
                            </td>
                            <th colspan="4"><font color="blue"><strong id="caption">Shipment Date:</strong></font>
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:65px" placeholder="From Date">To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:65px" placeholder="To Date" >
                            </th>
                            <td>
                                <input type="button" id="show_button" class="formbutton" style="width:60px" value="Show" onClick="fn_report_generated(0)" />
                            </td>
                            <td><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" /></td>
                        </tr>
                    </tfoot>
                </table>
            </fieldset>
        </div>
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2" align="left"></div>
</form>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
    set_multiselect('cbo_company_name','0','0','0','0');
    $("#multi_select_cbo_company_name a").click(function(){
        load_buyer();
        load_company_location();
    });

    function load_buyer()
    {
        var company=$("#cbo_company_name").val();
        load_drop_down( 'requires/order_wise_production_report__multi_company_order_wise_controller', company, 'load_drop_down_buyer', 'buyer_td' );
    }
    function load_company_location(){
         var company=$("#cbo_company_name").val();
         //alert(company);
        load_drop_down( 'requires/order_wise_production_report__multi_company_order_wise_controller', company, 'load_drop_down_location', 'location_td' );
    }

</script>
<script>
    $('#cbo_location').val(0);
</script>
<script class="include" type="text/javascript" src="../../js/chart/logic_chart.js"></script>
</html>
