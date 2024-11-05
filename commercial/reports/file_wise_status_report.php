<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create File wise Status Report.
Functionality	:
JS Functions	:
Created by		:	Safa
Creation date 	: 	04-04-2023
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
//--------------------------------------------------------------------------------
echo load_html_head_contents("File wise Status Report", "../../", 1, 1,'','','');
?>

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission = '<? echo $permission; ?>';

function new_window()
{
	var w = window.open("Surprise", "#");
	var d = w.document.open();
	d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
}

function load_style(buyer_id)
{
	var cbo_company_id=$("#cbo_company_name").val();
	load_drop_down( 'requires/file_wise_status_report_controller',cbo_company_id+'_'+buyer_id, 'load_drop_down_style', 'style_td' );
}


function openStylePopup() {
	if( form_validation('cbo_company_name', 'Company Name')==false ) { return; }

	var cbo_company_name = document.getElementById('cbo_company_name').value;
	var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
	
	var page_link='requires/file_wise_status_report_controller.php?action=style_search_popup&company='+cbo_company_name+'&buyer='+cbo_buyer_name;
	var title="Style Reference";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=460px,height=370px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function() {
		var theform=this.contentDoc.forms[0];
		var style_no=this.contentDoc.getElementById("txt_selected_no").value; // product Description
		var data=style_no.split("_");
		//alert(data);
		$('#txt_job_no').val(data[1]);
		$('#txt_style_no').val(data[2]);
		$('#txt_file_no').val(data[3]);
		$('#cbo_file_status').val(data[4]);
  		$('#txt_job_no').attr('disabled','true'); 
  		$('#txt_file_no').attr('disabled','true'); 
	}
}

function generate_report(operation)
{
    var company_id=$("#cbo_company_name").val();
    if(form_validation('cbo_company_name','Company Name')==false)
    {
        release_freezing();
        return;
    }
    else
    {	
        var report_title=$( "div.form_caption" ).html();
        var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_style_no*txt_job_no*txt_file_no*cbo_file_status',"../../")+'&report_title='+report_title+'&report_type='+operation;
        // alert(data);return;
        freeze_window(3);
        http.open("POST","requires/file_wise_status_report_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;
    }
}

function fn_report_generated_reponse()
{
    if(http.readyState == 4) 
    {
        var reponse=trim(http.responseText).split("****");
        // var tot_rows=reponse[2];
        $('#report_container2').html(reponse[0]);
        //alert(reponse[0]);return;
        //document.getElementById('report_container').innerHTML=report_convert_button('../../'); 
        document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
        //setFilterGrid("tbl_marginlc_list",-1,tableFilters);
        show_msg('3');
        release_freezing();
    }
}

</script>
</head>
<!-- <h1>This page is under construction...</h1> -->
<body onLoad="set_hotkey();">
	<div style="width:1040px" align="center">
		<form id="file_wise_explort_import_status" action="" autocomplete="off" method="post">
			<? echo load_freeze_divs ("../../"); ?>
            <h3 style="width:990px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3> 
			<div id="content_search_panel" style="width:990px;">
				<fieldset style="width:100%" >
					<table class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" width="960">
						<thead>
							<th class="must_entry_caption" width="170px">Company Name</th>
							<th width="150px">Buyer</th>
							<th width="150px">Style No</th>
							<th width="150px">Job No</th>
							<th width="140px">File No</th>
							<th width="100px">File Status</th>
							<th align="center"><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:100px" onClick="reset_form('file_wise_explort_import_status','report_container*report_container2','','','')" /></th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/file_wise_status_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" );
								?>
							</td>
							<td align="center" id="buyer_td">
								<?
								echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_style_no" id="txt_style_no" class="text_boxes" style="width:150px" placeholder="Browse"  onDblClick="openStylePopup();" readonly  />
							</td>
							<td>
				                <input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:140px" onDblClick="openJobPopup();"  disabled  />
				            </td>
                            <td>
				                <input type="text" id="txt_file_no" name="txt_file_no" class="text_boxes" style="width:140px" onDblClick="openJobPopup();"  disabled  />
				            </td>
							<td>
								<?
								$file_status = array(1 => "Active", 2=>"Inactive");
								echo create_drop_down( "cbo_file_status", 100, $file_status,"", 0, "-- Select Status --", 1, "" );
								?>
							</td>
							<td align="center">
                            <input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:100px" class="formbutton" />
							</td>
						</tr>
					</table>
				</fieldset>
			</div>
			<div id="report_container" align="center"></div>
			<div id="report_container2"></div>
		</form>
	</div>
</body>
<!-- <script src="../../includes/functions_bottom.js" type="text/javascript"></script> -->
</html>
