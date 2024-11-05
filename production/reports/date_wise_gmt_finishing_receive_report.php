<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Line Wise Productivity Analysis Report

Functionality	:
JS Functions	:
Created by		:	Arnab Dutta
Creation date 	: 	28-05-2023
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
echo load_html_head_contents("Date  Wise GMT Finishing  Receive Report","../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';


	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

    function generate_report(type){

           var working_company_id=document.getElementById('working_company_id').value;
           var wc_location_id=document.getElementById('wc_location_id').value;
           var lc_company_id=document.getElementById('lc_company_id').value;
           var lc_location_id=document.getElementById('lc_location_id').value;
           var wc_floor=document.getElementById('wc_floor').value;
           var txt_job_no=document.getElementById('txt_job_no').value;
           var txt_order_no=document.getElementById('txt_po_no').value;
           var txt_date_from=document.getElementById('txt_date_from').value;
           var txt_date_to=document.getElementById('txt_date_to').value;



           var data='action=generate_report&type='+type+get_submitted_data_string('working_company_id*wc_location_id*lc_company_id*lc_location_id*txt_date_from*txt_date_to*wc_floor*cbo_buyer_id*txt_job_no*txt_po_no','../');
          // alert(data);
           //return;
           freeze_window(5);
           http.open("POST","requires/date_wise_gmt_finishing_receive_report_controller.php",true);
           http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
           http.send(data);
           http.onreadystatechange = fnc_show_details_reponse;
       }

       function fnc_show_details_reponse(){
            if(http.readyState == 4)
            {
                var response=trim(http.responseText);
                //alert(response);
                $("#update_id").val("");
                $("#txt_system_no").val("");
                $('#report_container').html(response);
                set_all_onclick();
                show_msg('18');
                release_freezing();


                //  var div_overflow = document.getElementById("div_overflow");
                var height = div_overflow.clientHeight;
                console.log(height);
                if (height*1<200) {
                   document.getElementById("scanning_tbl").style.marginLeft = "-17px";
                }
            }
        }



	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="auto";
		document.getElementById('scroll_body').style.maxHeight="400px";
	}
    function job_no_popup(type)
	{
		if( form_validation('working_company_id','Company Name')==false)
		{
			return;
		}
		var company = $("#working_company_id").val();
		var buyer=$("#cbo_buyer_name").val();
		var txt_job_no = $("#txt_job_no").val();
		var page_link='requires/date_wise_gmt_finishing_receive_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&type='+type+'&txt_job_no='+txt_job_no;
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=780px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			//alert(type);

			if(type==1)
			{
				$('#txt_job_no').val(job_no);
				$('#txt_job_id').val(job_id);
			}

			else if(type==2)
			{
				$('#txt_po_no').val(job_no);
				$('#txt_po_no_hidden').val(job_id);
			}
			/*else if(type==4)
			{
				$('#txt_ref_no').val(job_no);
				$('#txt_job_id').val(job_id);
			}*/

		}
	}





</script>

</head>
<body onLoad="set_hotkey();">

	<form id="DateWiseGmtFinishingReceiveReport_1">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../",''); ?>
         <form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">

         <h3 style="width:1110px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
         <div id="content_search_panel" >
             <fieldset style="width:1090px;">
                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                        <thead>

                            <th class="must_entry_caption">Sewing Company</th>
                            <th class="must_entry_caption">Sewing Location</th>
                            <th>Sewing Floor</th>
                            <th class="must_entry_caption">Finishing Company</th>
                            <th class="must_entry_caption">Finishing Location</th>
                            <th class="must_entry_caption">Finishing Floor</th>
                            <th>Job/Style Reff.</th>
                            <th>Order No</th>
                            <th>Buyer</th>
                            <th colspan="2">Finishing Receive Date Range</th>
                            <th>
                                <input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" />

                            </th>

                        </thead>
                        <tbody>
                            <tr class="general">


                                  <td id="sew_company_td">
                                  <?
                                    echo create_drop_down("working_company_id", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Select--", 0, "load_drop_down( 'requires/date_wise_gmt_finishing_receive_report_controller', this.value, 'load_drop_down_working_location', 'wc_location_td' );load_drop_down( 'requires/date_wise_gmt_finishing_receive_report_controller', document.getElementById('wc_location_id').value+'_'+document.getElementById('working_company_id').value, 'load_drop_down_wc_company_location_wise_floor', 'wc_floor_td' )", 0);//
                                    ?>
                                </td>
                               <td id="wc_location_td">
                                    <?
                                    echo create_drop_down("wc_location_id", 100, "select id, location_name from lib_location", "id,location_name", 1, "--Select--", 0, "", 1);
                                    ?>
                                </td>
                                 <td id="wc_floor_td">
                                     <?
                                        $arr=array();
                                        echo create_drop_down("wc_floor", 90, $arr, "", 1, "-- Select Floor --", 0, "", 1);
                                    ?>
                                </td>

                                  <td>
                                     <?
                                        echo create_drop_down("lc_company_id", 90, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by company_name", "id,company_name", 1, "--Select--", 0, "load_drop_down( 'requires/date_wise_gmt_finishing_receive_report_controller', this.value, 'load_drop_down_lc_company_location', 'lc_location_td' );", 0);//
                                        ?>
                                </td>

                                <td id="lc_location_td">
                                    <?
                                        $arr=array();
                                     echo create_drop_down("lc_location_id", 90, $arr, "", 1, "-- Select Location --", 0, "", 1);
                                    ?>

                                </td>
                                 <td id="fini_floor_td">
                                     <?
                                       $floor_arr=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and production_process =11",'id','floor_name');
                                        echo create_drop_down("finishing_floor", 90, $floor_arr, "", 1, "-- Select Floor --", 0, "", 0);
                                    ?>
                                </td>
                                 <td>
                                    <input type="text"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px;"  placeholder="Write/Browse" onDblClick="job_no_popup(1);">
                                </td>
                                <td>
                            	 <input type="text"  name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:80px;"  placeholder="Write/Browse" onDblClick="job_no_popup(2);">
                              </td>
                                 <td id="buyer_td">
                                    <?

                                     echo create_drop_down("cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active=1 and buy.is_deleted=0", "id,buyer_name", 1, "-- Select Buyer --", 0, "");
                                    ?>

                                </td>
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px"  placeholder="From Date"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px"  placeholder="To Date"></td>

                                <td>
                                    <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="generate_report(1)" />
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <table>
                   <tr>
                    <td colspan="9">
                        <? echo load_month_buttons(1); ?>
                    </td>
                </tr>
            </table>
            <br />
                </fieldset>
            </div>
            <div id="report_container"></div>
            <div id="report_container2"></div>
        </form>
    </div>
	    <div id="report_container" align="center"></div>
	    <div id="report_container2" align="left" style="margin: 10px 0"></div>
 	</form>
</body>
<script>


	setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,'0');getLineId();") ,3000)];
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_id').val(0);
</script>
</html>
