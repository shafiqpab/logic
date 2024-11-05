<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Order Wise Finich Fabric Stock Report

Functionality	:
JS Functions	:
Created by		:
Creation date 	:
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Buyer Wise Finich Fabric Received Issued And Stock Report","../../../", 1, 1, $unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";


	function generate_report(type)
	{
		if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
		{
			return;
		}
        if (type==1) 
        {
            var action = "report_generate";
        }
        else
        {
            var action = "report_generate2";
        }

		var report_title=$( "div.form_caption" ).html();
		var data="action="+action+ get_submitted_data_string('cbo_company_id*cbo_item_category*cbo_product_category*txt_date_from*txt_date_to*cbo_buyer_id*txt_job_no*txt_job_no_show*txt_style_no*cbo_location_id',"../../../")+'&report_title='+report_title;
		// alert (data);return;
		freeze_window(3);
		http.open("POST","requires/buyer_wise_finish_fabric_received_issued_stock_controller.php",true);
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
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			show_msg('3');
			release_freezing();
		}
	}

	function generate_details_report() {
        if( form_validation('cbo_company_id*txt_date_from*txt_date_to','Company Name*From Date*To Date')==false )
        {
            return;
        }
        var report_title=$( "div.form_caption" ).html();
        var data="action=generate_details_report"+ get_submitted_data_string
        ('cbo_company_id*cbo_item_category*cbo_product_category*txt_date_from*txt_date_to*cbo_buyer_id*txt_job_no*txt_job_no_show*txt_style_no*cbo_year_selection*cbo_location_id',"../../../")+'&report_title='+report_title;
        //alert (data);
        freeze_window(3);
        http.open("POST","requires/buyer_wise_finish_fabric_received_issued_stock_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = generate_details_report_reponse;
    }

    function generate_details_report_reponse() {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split("**");
            $("#report_container2").html(reponse[0]);
            document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1);
			setFilterGrid("table_body_id",-1);
            show_msg('3');
            release_freezing();
        }
    }

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#table_body tr:first').hide();
		$('#table_body_id tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		//$('#table_body tr:first').hide();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		//$('#scroll_body tr:first').show();
	}

	function openmypage_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var cbo_year_id = $("#cbo_year_selection").val();
		//var cbo_month_id = $("#cbo_month").val();
		var page_link='requires/buyer_wise_finish_fabric_received_issued_stock_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year_id='+cbo_year_id;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;

			$('#txt_job_no_show').val(job_no);
			$('#txt_job_no').val(job_id);
		}
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",$permission); ?>
    <form name="ordewisefinishfabricstock_1" id="ordewisefinishfabricstock_1" autocomplete="off" >
    <h3 style="width:1225px; margin-top:20px;" align="" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,
    'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" style="width:100%;" align="center">
            <fieldset style="width:1225px;">
                <table class="rpt_table" width="1225" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="140" class="must_entry_caption">Company</th>
                            <th width="140">Buyer</th>
                            <th width="110">Fabric Nature By</th>
                            <th width="110">Location</th>
                            <th width="90">Prod Category</th>
                            <th width="90">Style Ref.</th>
                            <th width="100">Job No.</th>
                            <th width="150" class="must_entry_caption">Transaction Date</th>
                            <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('ordewisefinishfabricstock_1','report_container*report_container2','','','','');" /></th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr align="center" class="general">
                        <td>
                            <?
                               echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/buyer_wise_finish_fabric_received_issued_stock_controller',this.value+'_'+1+'_'+4, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/buyer_wise_finish_fabric_received_issued_stock_controller',this.value, 'load_drop_down_location', 'location_td' );" );
                            ?>
                        </td>
                        <td id="buyer_td">
                            <?
                                echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", 0, "",0 );
                            ?>
                        </td>
                        <td>
                            <?
                                //$report_arr=array(1=>'Knit Finish',2=>'Woven Finish');
                                echo create_drop_down( "cbo_item_category", 115, $item_category, "", 0, "--  --", 0, "", "", "2,3","");
                            ?>
                        </td>
                        <td id="location_td">
                            <?
                                echo create_drop_down( "cbo_location_id", 115, $blank_array, "", 1, "--Select Location--", 0);
                            ?>
                        </td>
                        <td>
                            <?
                                echo create_drop_down( "cbo_product_category", 115, $product_category, "", 1, "-- ALL --", 0, "", "", "");
                            ?>
                        </td>
                        <td>
                            <input type="text" id="txt_style_no" name="txt_style_no" class="text_boxes" style="width:100px"  placeholder="Write" />
                        </td>
                         <td>
                            <input type="text" id="txt_job_no_show" name="txt_job_no_show" class="text_boxes" style="width:100px" onDblClick="openmypage_job()" placeholder="browse" />
                            <input type="hidden" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" />
                        </td>

                        <td>
                            <input type="text" name="txt_date_from" id="txt_date_from" value="<? echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly/>
                            <input type="text" name="txt_date_to" id="txt_date_to" value="<? echo date("d-m-Y", time());?>" class="datepicker" style="width:50px;" readonly/>
                        </td>
                        <td>
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton" />
                            <input type="button" name="search" id="search" value="Show2" onClick="generate_report(2)" style="width:70px" class="formbutton" />
                            <input type="button" name="report" id="report" value="Report" onClick="generate_details_report()" style="width:70px"
                                   class="formbutton" />
                        </td>
                    </tr>
                    </tbody>
                    <tfoot>
                    	<tr>
                    		<td colspan="9" align="center">
                    			<?  echo load_month_buttons(1); ?>
                    		</td>
                    	</tr>
                    </tfoot>

                </table>
            </fieldset>
        </div>

    </form>
</div>
	<div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
