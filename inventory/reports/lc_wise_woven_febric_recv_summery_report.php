<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create LC Wise Knit Febric Receive Report

Functionality	:
JS Functions	:
Created by		:	Shafiqur Rahman Sumon
Creation date 	: 	24/09/2018
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
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("LC Wise Woven Febric Receive Report","../../", 1, 1, $unicode,1,1);
?>

    <script>
        var permission='<? echo $permission; ?>';
        if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

        function generate_report()
        {
            if( form_validation('cbo_company_name','Company Name')==false )
            {
                return;
            }

            var report_title=$( "div.form_caption" ).html();
            var data="action=report_generate"+get_submitted_data_string('cbo_company_name*txt_pi_no*btbLc_id',"../../")+'&report_title='+report_title;
            freeze_window(3);
            http.open("POST","requires/lc_wise_woven_febric_recv_summery_report_controller.php",true);
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

                setFilterGrid("tbl_woven_finish_feb_btb_lc_dtls",-1);
                setFilterGrid("tbl_woven_finish_feb_pi_dtls",-1);
                setFilterGrid("tbl_woven_finish_feb_rcv",-1);
                setFilterGrid("tbl_woven_finish_feb_rcv_rtrn",-1);
                setFilterGrid("tbl_woven_finish_feb_acceptance",-1);
                show_msg('3');
                release_freezing();

            }
        }

        function openmypage_btbLc()
        {
            var companyID = $('#cbo_company_name').val();

            if (form_validation('cbo_company_name','Company')==false)
            {
                return;
            }

            var page_link='requires/lc_wise_woven_febric_recv_summery_report_controller.php?action=btbLc_popup&companyID='+companyID;
            var title='BTB LC No';

            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var btbLc_id=this.contentDoc.getElementById("btbLc_id").value;
                var btbLc_no=this.contentDoc.getElementById("btbLc_no").value;

                $('#btbLc_id').val(btbLc_id);
                $('#txt_btbLc_no').val(btbLc_no);
            }
        }

        function openmypage_pinumber()
        {
            var companyID = $('#cbo_company_name').val();

            if (form_validation('cbo_company_name','Company')==false)
            {
                return;
            }

            var page_link='requires/lc_wise_woven_febric_recv_summery_report_controller.php?action=pinumber_popup&companyID='+companyID;
            var title='PI Number Info';

            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=390px,center=1,resize=1,scrolling=0','../');
            emailwindow.onclose=function()
            {
                var theform=this.contentDoc.forms[0];
                var pi_id=this.contentDoc.getElementById("pi_id").value;
                var pi_no=this.contentDoc.getElementById("pi_no").value;

                $('#pi_id').val(pi_id);
                $('#txt_pi_no').val(pi_no);
            }
        }

        function new_window()
        {
            document.getElementById('caption').style.visibility='visible';
            document.getElementById('caption').style.visibility='visible';
            document.getElementById('tbl_woven_finish_feb_btb_lc_dtls').rows[0].style.display='none';
            document.getElementById('tbl_woven_finish_feb_pi_dtls').rows[0].style.display='none';
            document.getElementById('tbl_woven_finish_feb_rcv').rows[0].style.display='none';
            document.getElementById('tbl_woven_finish_feb_rcv_rtrn').rows[0].style.display='none';
            document.getElementById('tbl_woven_finish_feb_acceptance').rows[0].style.display='none';
            
            document.getElementById('scroll_body_tbl_woven_finish_feb_pi_dtls').style.overflow="auto";
            document.getElementById('scroll_body_tbl_woven_finish_feb_pi_dtls').style.maxHeight="none";
            document.getElementById('scroll_body_tbl_woven_finish_feb_rcv').style.overflow="auto";
            document.getElementById('scroll_body_tbl_woven_finish_feb_rcv').style.maxHeight="none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');

            document.getElementById('scroll_body_tbl_woven_finish_feb_pi_dtls').style.overflow="auto";
            document.getElementById('scroll_body_tbl_woven_finish_feb_pi_dtls').style.maxHeight="380px";
            document.getElementById('scroll_body_tbl_woven_finish_feb_rcv').style.overflow="auto";
            document.getElementById('scroll_body_tbl_woven_finish_feb_rcv').style.maxHeight="380px";

            document.getElementById('caption').style.visibility='hidden';
            d.close();
            setFilterGrid("tbl_woven_finish_feb_btb_lc_dtls",-1);
            setFilterGrid("tbl_woven_finish_feb_pi_dtls",-1);
            setFilterGrid("tbl_woven_finish_feb_rcv",-1);
            setFilterGrid("tbl_woven_finish_feb_rcv_rtrn",-1);
            setFilterGrid("tbl_woven_finish_feb_acceptance",-1);
        }
    </script>
</head>

<body onLoad="set_hotkey()">
    <div style="width:100%;" align="left">
    <? echo load_freeze_divs ("../../",$permission); ?><br />
    <form name="lc_wise_feb_recv_rpt" id="lc_wise_feb_recv_rpt" autocomplete="off" >
        <div style="width:100%;" align="center">
            <fieldset style="width:560px;">
            <legend>Search Panel</legend>
                <table class="rpt_table" width="550" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="150" class="must_entry_caption">Company</th>
                            <th width="150">PI Number</th>
                            <th>BTB LC No</th>
                            <th width="120"><input type="reset" name="res" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('lc_wise_feb_recv_rpt','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tr align="center">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "reset_form('','','txt_btbLc_no*btbLc_id','','','');" );

                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_pi_no" name="txt_pi_no" class="text_boxes" style="width:100px" placeholder="Write Or Browse" onDblClick="openmypage_pinumber()"  />
                            <input type="hidden" id="pi_id" readonly />
                        </td>
                        <td>
                            <input type="text" id="txt_btbLc_no" name="txt_btbLc_no" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="openmypage_btbLc()" readonly />
                            <input type="hidden" id="btbLc_id" readonly />
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report()" style="width:100px" class="formbutton" />
                        </td>
                    </tr>
                </table>
            </fieldset>

            <div id="report_container" align="center"></div>
            <div id="report_container2"></div>

        </div>
    </form>
</div>
</body>
<script>
    //set_multiselect('cbo_store_name','0','0','','0');
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
