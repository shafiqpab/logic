<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Size Wise GMTS Production Report.
Functionality	:
JS Functions	:
Created by		:	Md Mamun Ahmed Sagor
Creation date 	: 	24-05-2021
Updated by 		:  Kamrul Hasan (show 2)
Update date		:  20-03-2023
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
echo load_html_head_contents("Size Wise GMTS Production Report", "../../", 1, 1,$unicode,1,1);
?>

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	var tableFilters =
	{
		col_operation: {
		id: [""],
		col: [],
		operation: [],
		write_method: []
		}
	}


	function fn_generate_report(type)
	{
		var company=$("#cbo_company_name").val();
		var job=$("#txt_job_no").val();
		var date_from=$("#txt_date_from").val();
		var date_to=$("#txt_date_to").val();

		// alert(job);
		if(job=='')
		{
			if(form_validation('cbo_company_name*txt_date_from*txt_date_to','Company Name*Date From*Date To')==false )
			{

				return;
			}

		}

		if(form_validation('cbo_company_name',' Company Name')==false )
		{

			return;
		}

		var report_title=$( "div.form_caption" ).html();

		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*cbo_work_company_name*cbo_buyer_name*cbo_year*txt_job_no*hidden_job_id*txt_date_to*txt_date_from*hiden_order_id*hidden_job_year',"../../")+'&type='+type+'&report_title='+report_title;

		freeze_window(3);
		http.open("POST","requires/size_wise_gmts_production_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			//alert (http.responseText);
			var reponse=trim(http.responseText).split("####");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			setFilterGrid("table_body",-1,tableFilters);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow="auto";
		// document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").css("display","none");

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../css/style_print.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		// document.getElementById('scroll_body').style.overflowY="scroll";
		// document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");

	}

	function openmypage_job_no() // JOB POPUP
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var w_company = $("#cbo_work_company_name").val();
		var lc_company = $("#cbo_company_name").val();
		var buyer = $("#cbo_buyer_name").val();
		var job_year = $("#cbo_year").val();
		var page_link='requires/size_wise_gmts_production_report_controller.php?action=openJobNoPopup&w_company='+w_company+'&lc_company='+lc_company+'&buyer='+buyer+'&job_year='+job_year;
		var title="Job No Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=550px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var po_id=this.contentDoc.getElementById("txt_selected_po").value; // product ID
			var job_no=this.contentDoc.getElementById("txt_selected_job").value; // product Description
			var style_des_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
			var job_year=this.contentDoc.getElementById("txt_selected_year").value; // product Description
			// alert(job_year);
			// $("#txt_order").val(style_des);
			var order_id_arr = po_id.split(',');
			var unique_ord_id_arr = Array.from(new Set(order_id_arr));
			var orderIds = unique_ord_id_arr.join(',');

			var job_year_arr = job_year.split(',');
			var unique_job_year_arr = Array.from(new Set(job_year_arr));
			var jobYear = unique_job_year_arr.join(',');

			var job_no_arr = job_no.split(',');
			var unique_job_arr = Array.from(new Set(job_no_arr));
			var jobNo = unique_job_arr.join(',');

			$("#hiden_order_id").val(orderIds);
			$("#hidden_job_year").val(jobYear);
			$("#txt_job_no").val(jobNo);
		}
	}
</script>

</head>

<body onLoad="set_hotkey();">
  <div style="width:100%;" align="center">
   <? echo load_freeze_divs ("../../",'');  ?>
    <h3 style="width:866px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
       <div style="width:100%;" align="center" id="content_search_panel">
    <form id="dateWiseProductionReport_1">
      <fieldset style="width:850px;">
            <table class="rpt_table" width="850" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
               <thead>
                       <tr>
                        <th class="must_entry_caption" width="120" >Company</th>
						<th  width="210" >Working Company</th>
                        <th width="130" >Buyer Name</th>
                        <th width="60">Job Year</th>
                        <th width="100">Job No</th>
						<th width="100"  colspan="2">Date</th>
                        <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
                    </tr>
              </thead>
                <tbody>
                <tr class="general">
					<td>
					<?
					echo create_drop_down("cbo_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"" );
					?>
			        </td>
                    <td align="center" id="td_company">
                        <?
                            echo create_drop_down( "cbo_work_company_name", 200, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 0, "-- Select Company --", $selected, "" );
                        ?>
                    </td>
                    <td align="center">
                        <?
                        echo create_drop_down( "cbo_buyer_name", 130, "SELECT a.id,a.buyer_name from lib_buyer a where a.status_active=1 and a.is_deleted=0 and a.party_type not in('2') group by  a.id,a.buyer_name  order by a.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0,"" );
                        ?>
                    </td>
                     <td>
                        <?
						$selected_year=date("Y");
						echo create_drop_down( "cbo_year", 60, $year,"", 1, "Year--",$selected_year, "",0 );
						?>
                    </td>
                    <td>
					   <input type="text" id="txt_job_no"  name="txt_job_no"  style="width:100px" class="text_boxes" onDblClick="openmypage_job_no()" placeholder="Browse"  readonly/>
					   <input type="hidden" name="hiden_order_id" id="hiden_order_id" value="">
                       <input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
					   <input type="hidden" name="hidden_job_year" id="hidden_job_year" value="">
                    </td>
                    <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"  placeholder="From Date"></td>
					<td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"  placeholder="To Date"></td>
                    <td>
                         <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" />

                     </td>
					 <td>
						<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show2" onClick="fn_generate_report(2)" />
					</td>

                </tr>
				<tr>
                    <td colspan="12" align="center" width="100%"><? echo load_month_buttons(1); ?></td>
                </tr>
                </tbody>
            </table>
      </fieldset>

 </form>
 </div>
    <div id="report_container" style="padding: 10px;"></div>
    <div id="report_container2"></div>
 </div>
</body>
<script>
	set_multiselect('cbo_work_company_name','0','0','','0');

	setTimeout[($("#td_company a").attr("onclick","disappear_list(cbo_work_company_name,'0');") ,3000)];
	document.getElementById('cbo_year').value='<? echo date('Y');?>';
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>


</html>
