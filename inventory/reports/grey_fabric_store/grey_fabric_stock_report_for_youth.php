<?
/*-------------------------------------------- Comments

Purpose			: 	This form will Create Gray Fabrics Stock Report
Functionality	:
JS Functions	:
Created by		:	Tipu
Creation date 	: 	21-03-2023
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
//---------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Batch Receive WIP Report", "../../../", 1, 1,'','','');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
	var permission='<? echo $permission; ?>';

	function openmypage_job()
	{
	  	if( form_validation('cbo_company_name','Company Name*Type')==false )
	  	{
	   		return;
	 	}

		var companyID = $("#cbo_company_name").val();
		var cbo_year = $("#cbo_year").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/grey_fabric_stock_report_for_youth_controller.php?action=job_no_popup&companyID='+companyID+'&cbo_year='+cbo_year+'&buyer_name='+buyer_name;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=400px,center=1,resize=1,scrolling=0','../../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			$('#txt_job_no').val(job_no);
			$('#txt_job_id').val(job_id);
		}
	}

	function fn_report_generated(operation)
	{
		if (form_validation('cbo_company_name*cbo_year','Comapny Name*Job Year')==false)
		{
			return;
		}

		freeze_window(5);
		
	 	var data="action=generated_report&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_id*cbo_year*txt_job_no*txt_job_id*txt_int_ref*cbo_value_with',"../../../");

	 	// alert(data);release_freezing();return;

		http.open("POST","requires/grey_fabric_stock_report_for_youth_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_show_report_response;
	}

	function fnc_show_report_response()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("##");
			$("#report_container2").html(reponse[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window(1)" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';

			/*setFilterGrid("table_body",-1,tableFilters);
			if(document.getElementById('table_body2'))
			{
				setFilterGrid("table_body2",-1,tableFilters_2);
			}*/
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			// $('#scroll_body tr:first').hide();
		}

		if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="none";
		}

		var w = window.open("Surprise", "#");

		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		if(document.getElementById('table_body'))
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="397px";
	        $('#scroll_body tr:first').show();
	    }

	    if(document.getElementById('table_body2'))
		{
			document.getElementById('scroll_body_subcon').style.overflow="auto";
			document.getElementById('scroll_body_subcon').style.maxHeight="397px";
	        $('#scroll_body_subcon tr:first').show();
		}
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../../",''); ?>
		<form name="dailyYarnStatusReport_1" id="dailyYarnStatusReport_1">
        	<h3 style="width:780px; margin-top:10px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3>
	        <div id="content_search_panel" >
	            <fieldset style="width:780px;">
	                 <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
	                    <thead>
	                        <th class="must_entry_caption">Company Name</th>
	                        <th>Buyer</th>
	                        <th class="must_entry_caption">Job Year</th>
	                        <th>Job</th>
	                        <th>Enter Ref.</th>
		                    <th>Value</th>
	                        <th><input type="reset" name="res" id="res" value="Reset" onClick="reset_form('dailyYarnStatusReport_1','report_container*report_container2','','','')" class="formbutton" style="width:50px" /></th>
	                    </thead>
	                    <tbody>
	                        <tr class="general">
	                            <td>
	                                <?
	                                    echo create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/grey_fabric_stock_report_for_youth_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
	                                ?>
	                            </td>
	                            <td id="buyer_td">
	                                <?
	                                echo create_drop_down( "cbo_buyer_id", 130,$blank_array,"", 1, "-- Select Buyer--", $selected, "","","","","","");
	                                ?>
	                            </td>
	                            <td>
					            	<? echo create_drop_down( "cbo_year", 70, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?>
					            </td>	                            
	                            <td>
					            	<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:100px" onDblClick="openmypage_job();" placeholder="Browse/Write" />
					              	<input type="hidden" id="txt_job_id" name="txt_job_id"/>
					            </td>
					            <td>
					            	<input type="text" id="txt_int_ref" name="txt_int_ref" class="text_boxes" style="width:80px" placeholder="Write"/>
					            </td>
		                        <td>
		                            <?
		                            	$value_type = array('1' => 'Value With 0', '2' => 'Value Without 0');
		                                echo create_drop_down( "cbo_value_with", 100, $value_type,"", 2, "- All -", 2, "",0 );
		                            ?>
		                        </td>
	                            <td>
	                            	<input type="button" id="show_button" class="formbutton" style="width:50px" value="Show" onClick="fn_report_generated(1)" />
	                            </td>
                      			
	                        </tr>
	                    </tbody>
	                </table>
	            </fieldset>
	        </div>
            <div id="report_container"></div>
    		<div id="report_container2"></div>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>