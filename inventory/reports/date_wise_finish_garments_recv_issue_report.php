<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Date Wise Finish Garments Receive Issue Report
				
Functionality	:	
JS Functions	:
Created by		:	Md. Jakir Hosen
Creation date 	: 	17/08/2022
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) {
    header("location:login.php");
}
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Date Wise Finish Garments Receive Issue","../../", 1, 1, $unicode,1,'');
?>	
    <script>
        var permission='<? echo $permission; ?>';
        if( $('#index_page', window.parent.document).val()!=1)
            window.location.href = "../logout.php";

        var tableFilters =
            {
                col_operation: {
                    id: ["value_total_receive","value_total_receive_rtn","value_total_issue","value_total_issue_rtn"],
                    col: [12,13,14,15],
                    operation: ["sum","sum","sum","sum"],
                    write_method: ["innerHTML","innerHTML","innerHTML","innerHTML"]
                }
            };
        var tableFilters1 =
            {
                col_operation: {
                    id: ["value_total_receive","value_total_receive_rtn"],
                    col: [12,13],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                }
            };
        var tableFilters2 =
            {
                col_operation: {
                    id: ["value_total_issue","value_total_issue_rtn"],
                    col: [11,12],
                    operation: ["sum","sum"],
                    write_method: ["innerHTML","innerHTML"]
                }
            };

        function openmypage_style()
        {
            if( form_validation('cbo_company_name','Company Name')==false )
            {
                return;
            }
            var company = $("#cbo_company_name").val();
            var buyer = $("#cbo_buyer_name").val();
            var txt_style_ref_no = $("#txt_style_ref_no").val();
            var txt_style_ref_id = $("#txt_style_ref_id").val();
            var txt_style_ref = $("#txt_style_ref").val();
            var cbo_year = $("#cbo_year").val();
            var page_link='requires/date_wise_finish_garments_recv_issue_report_controller.php?action=style_reference_search&company='+company+'&buyer='+buyer+'&txt_style_ref_no='+txt_style_ref_no+'&txt_style_ref_id='+txt_style_ref_id+'&txt_style_ref='+txt_style_ref+'&cbo_year='+cbo_year;
            var title="Search Style Popup";
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var style_id=this.contentDoc.getElementById("txt_selected_id").value;
                var style_des=this.contentDoc.getElementById("txt_selected").value;
                var style_no=this.contentDoc.getElementById("txt_selected_no").value;

                $("#txt_style_ref").val(style_des);
                $("#txt_style_ref_id").val(style_id);
                $("#txt_style_ref_no").val(style_no);
            }
        }

        function openmypage_job()
        {
            if( form_validation('cbo_company_name','Company Name')==false )
            {
                return;
            }
            var company = $("#cbo_company_name").val();
            var buyer = $("#cbo_buyer_name").val();
            var txt_style_ref_id = $("#txt_style_ref_id").val();
            var txt_job_no = $("#txt_job_no").val();
            var txt_job_id = $("#txt_job_id").val();
            var txt_job = $("#txt_job").val();
            var cbo_year = $("#cbo_year").val();
            var page_link='requires/date_wise_finish_garments_recv_issue_report_controller.php?action=job_search&company='+company+'&buyer='+buyer+'&txt_style_ref_id='+txt_style_ref_id+'&txt_job='+txt_job+'&txt_job_id='+txt_job_id+'&txt_job_no='+txt_job_no+'&cbo_year='+cbo_year;
            var title="Search Job No. Popup";
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=420px,height=370px,center=1,resize=0,scrolling=0','../')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var style_id=this.contentDoc.getElementById("txt_selected_id").value;
                var style_des=this.contentDoc.getElementById("txt_selected").value;
                var style_no=this.contentDoc.getElementById("txt_selected_no").value;

                $("#txt_job").val(style_des);
                $("#txt_job_id").val(style_id);
                $("#txt_job_no").val(style_no);
            }
        }

        function openmypage_order()
        {
            if( form_validation('cbo_company_name','Company Name')==false )
            {
                return;
            }
            var company = $("#cbo_company_name").val();
            var buyer = $("#cbo_buyer_name").val();
            var txt_job_id = $("#txt_job_id").val();
            var txt_style_ref_id = $("#txt_style_ref_id").val();
            var txt_order_id_no = $("#txt_order_id_no").val();
            var txt_order_id = $("#txt_order_id").val();
            var txt_order = $("#txt_order").val();
            var cbo_year = $("#cbo_year").val();
            var page_link='requires/date_wise_finish_garments_recv_issue_report_controller.php?action=order_search&company='+company+'&buyer='+buyer+'&txt_order_id_no='+txt_order_id_no+'&txt_order_id='+txt_order_id+'&txt_order='+txt_order+'&txt_job_id='+txt_job_id+'&txt_style_ref_id='+txt_style_ref_id+'&cbo_year='+cbo_year;
            var title="Search Order Popup";
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=370px,center=1,resize=0,scrolling=0','../')
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var style_id=this.contentDoc.getElementById("txt_selected_id").value;
                var style_des=this.contentDoc.getElementById("txt_selected").value;
                var style_des_no=this.contentDoc.getElementById("txt_selected_no").value;

                $("#txt_order").val(style_des);
                $("#txt_order_id").val(style_id);
                $("#txt_order_id_no").val(style_des_no);
            }
        }


        function reset_field()
        {
            reset_form('item_receive_issue_1','report_container2','','','','');
        }

        function  generate_report(rptType)
        {
            var cbo_company_name = $("#cbo_company_name").val();
            var cbo_buyer_name = $("#cbo_buyer_name").val();
            var txt_style_ref = $("#txt_style_ref").val();
            var txt_style_ref_id = $("#txt_style_ref_id").val();
            var txt_job = $("#txt_job").val();
            var txt_job_id = $("#txt_job_id").val();
            var txt_order = $("#txt_order").val();
            var txt_order_id = $("#txt_order_id").val();
            var txt_date_from = $("#txt_date_from").val();
            var txt_date_to = $("#txt_date_to").val();
            var cbo_based_on = $("#cbo_based_on").val();
            var txt_search_val = $("#txt_search_val").val();
            var cbo_search_id = $("#cbo_search_id").val();
            var cbo_year = $("#cbo_year").val();

            if( form_validation('cbo_company_name','Company Name')==false )
            {
                return;
            }

            if(txt_style_ref == "" && txt_order == "" &&  txt_job == "")
            {
                if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
                {
                    return;
                }
            }
            var fso_id=0;
            // if ($("#fso_id").is('checked') == true)
            //     fso_id=1;

            var dataString = "&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name+"&txt_style_ref="+txt_style_ref+"&txt_style_ref_id="+txt_style_ref_id+"&txt_job="+txt_job+"&txt_job_id="+txt_job_id+"&txt_order="+txt_order+"&txt_order_id="+txt_order_id+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_based_on="+cbo_based_on+"&rptType="+rptType+"&cbo_search_id="+cbo_search_id+"&txt_search_val="+txt_search_val+"&fso_id="+fso_id+"&cbo_year="+cbo_year;
            var data="action=generate_report"+dataString;

            freeze_window(5);
            http.open("POST","requires/date_wise_finish_garments_recv_issue_report_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = generate_report_reponse;
        }

        function generate_report_reponse()
        {
            if(http.readyState == 4)
            {
                var reponse=trim(http.responseText).split("**");
                $("#report_container2").html(reponse[0]);

                document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

                // if(reponse[2]!=2)
                // {
                //     append_report_checkbox('table_header_1',1);
                // }
                if(reponse[2] == 1){
                    setFilterGrid("table_body",-1,tableFilters);
                }else if(reponse[2] == 2){
                    setFilterGrid("table_body",-1,tableFilters1);
                }else if(reponse[2] == 3){
                    setFilterGrid("table_body",-1,tableFilters2);
                }

                release_freezing();
                show_msg('3');
            }
        }

        function print_preview_button(url)
        {
            return '<input type="button" onclick="print_priview_html( \'report_container2\', \'scroll_body\',\'table_header_1\',\'report_table_footer\', 3, \'0\',\''+url+'\' )" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
        }
        function excel_preview_button(url)
        {
            return '<a href="requires/'+url+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>';
        }


        function new_window()
        {

            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="none";
            $('#table_body tr:first').hide();
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
            d.close();
            document.getElementById('scroll_body').style.overflow="auto";
            document.getElementById('scroll_body').style.maxHeight="250px";
            $('#table_body tr:first').show();
        }


        function fn_change_base(str)
        {
            //alert(str);
            if(str==1)
            {
                $("#up_tr_date").html("");
                $("#up_tr_date").html("Transaction Date Range");
                $('#up_tr_date').attr('style','color:blue');
            }
            else
            {
                $("#up_tr_date").html("");
                $("#up_tr_date").html("Insert Date Range");
                $('#up_tr_date').attr('style','color:blue');
            }
        }

        function fn_change_base2(str)
        {
            //alert(str);
            if(str==1)
            {
                $("#file_ref_td").html("");
                $("#file_ref_td").html("File No");

            }
            else
            {
                $("#file_ref_td").html("");
                $("#file_ref_td").html("Ref. No");

            }
        }
    </script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
        <? echo load_freeze_divs ("../../",$permission);  ?><br />
        <form name="item_receive_issue_1" id="item_receive_issue_1" autocomplete="off" >
        <h3 align="left" id="accordion_h1" style="width:1220px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div style="width:1220px;" align="center" id="content_search_panel">
            <fieldset style="width:1220px;">
                    <table class="rpt_table" width="1220" cellpadding="0" cellspacing="0" rules="all">
                    <thead>
                        <tr>
                            <th width="120" class="must_entry_caption">Company</th>
                            <th width="65" style="display: none;">Job Year</th>
                            <th width="120">Buyer Name</th>
                            <th width="95">Style</th>
                            <th width="95">Job No.</th>
                            <th width="95">Order No.</th>
                            <th width="70">Search By</th>
                            <th width="70" id="file_ref_td" >File No</th>
                            <th width="100">Based On</th>
                            <th width="170" id="up_tr_date" class="must_entry_caption">Transaction Date Range</th>
                            <th>
                                <input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_field()" />
<!--                                <span style="float: left;">FSO <input type="checkbox" checked="checked" name="fso_id" id="fso_id" value="" onClick="fso_check_item_fnc()"></span>-->
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                                echo create_drop_down( "cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/date_wise_finish_garments_recv_issue_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                            ?>
                        </td>
                        <td style="display: none;">
                            <?
                                echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
                            ?>
                        </td>
                        <td align="center">
                            <input style="width:90px;"  name="txt_style_ref" id="txt_style_ref"  ondblclick="openmypage_style()"  class="text_boxes" placeholder="Browse"  readonly />
                            <input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>
                            <input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>
                        </td>
                        <td align="center">
                            <input style="width:90px;" name="txt_job" id="txt_job"  ondblclick="openmypage_job()"  class="text_boxes" placeholder="Browse" readonly />
                            <input type="hidden" name="txt_job_id" id="txt_job_id"/>
                            <input type="hidden" name="txt_job_no" id="txt_job_no"/>
                        </td>

                         <td align="center">
                            <input type="text" style="width:90px;"  name="txt_order" id="txt_order"  ondblclick="openmypage_order()"  class="text_boxes" placeholder="Browse" readonly  />
                            <input type="hidden" name="txt_order_id" id="txt_order_id"/>
                             <input type="hidden" name="txt_order_id_no" id="txt_order_id_no"/>
                        </td>
                         <td>
                            <?
                            $search_by_arr=array(1=>"File No",2=>"Ref. No");
                            echo create_drop_down( "cbo_search_id", 70, $search_by_arr,"", 0, "", 1, "fn_change_base2(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_search_val" id="txt_search_val" class="text_boxes" style="width:70px;" placeholder="Write" />

                        </td>
                        <td>
                            <?
                            $base_on_arr=array(1=>"Transaction Date",2=>"Insert Date");
                            echo create_drop_down( "cbo_based_on", 100, $base_on_arr,"", 0, "", 1, "fn_change_base(this.value);",0 );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px;" placeholder="From Date" readonly/>&nbsp; <strong>--</strong> &nbsp;
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="search1" id="search1" value="All" onClick="generate_report(1)" style="width:55px" class="formbutton" />
                            <input type="button" name="search2" id="search2" value="Receive" onClick="generate_report(2)" style="width:55px" class="formbutton" />
                            <input type="button" name="search4" id="search4" value="Issue" onClick="generate_report(3)" style="width:55px" class="formbutton" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="11" align="center" valign="bottom">
                            <? echo load_month_buttons(1);  ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>

                </table>
            </fieldset>

        </div>
            <!-- Result Contain Start -->
                <div style="margin-top:10px" id=""><span id="report_container"></span><span id="report_container3"></span></div>
                <div id="report_container2"></div>
            <!-- Result Contain END -->


        </form>
    </div>
</body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
