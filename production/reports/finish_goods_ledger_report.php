<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Finish Goods Ledger Report
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	30-11-2021
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
echo load_html_head_contents("Finish Goods Ledger Report", "../../", 1, 1,$unicode,1,1,1);

?>

<script>

	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	 
	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company_name=$("#cbo_company_name").val();
		var page_link='requires/finish_goods_ledger_report_controller.php?action=job_popup&company_name='+company_name;
		var title="Search Job/Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=390px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value;
			var com_id=this.contentDoc.getElementById("cbo_company_id").value;

			$("#txt_job_no").val(job_no);
			$("#hidden_job_id").val(job_id); 
			$("#cbo_company_name").val(com_id); 
		}
	}

	function fn_generate_report(type)
	{
		if( form_validation('cbo_company_name*hidden_job_id','Company Name*Job/Style')==false )
		{
			return;
		}
		
		var report_title=$( "div.form_caption" ).html();
		var data="action=generate_report"+get_submitted_data_string('cbo_company_name*hidden_job_id',"../../")+'&type='+type+'&report_title='+report_title;

		freeze_window(3);
		http.open("POST","requires/finish_goods_ledger_report_controller.php",true);
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
			setFilterGrid("table_body",-1);
			show_msg('3');
			release_freezing();
		}
	} 

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none"; 
		$(".flt").css("display","none");
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="all" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll"; 
		document.getElementById('scroll_body').style.maxHeight="400px";
		$(".flt").css("display","block");
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center"> 
		<? echo load_freeze_divs ("../../",'');  ?>
		<h3 style="width:450px; margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
		<div style="width:100%;" align="center" id="content_search_panel">
		<form id="dateWiseProductionReport_1">    
			<fieldset style="width:450px;">
				<table class="rpt_table" width="450" cellpadding="0" cellspacing="0" align="center" border="1" rules="all">
					<thead>                    
						<tr>
							<th class="must_entry_caption" width="180" >LC Company</th>
							<th class="must_entry_caption"  width="150">Job No</th>
							<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form()"/></th>
						</tr>   
					</thead>
					<tbody>
						<tr class="general">
							<td align="center" id="td_company"> 
								<?
									echo create_drop_down( "cbo_company_name", 180, "SELECT id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
								?>
							</td>
							<td>
							<input type="text" id="txt_job_no"  name="txt_job_no"  style="width:150px" class="text_boxes" onDblClick="open_job_no()" placeholder="Browse" readonly />
							<input type="hidden" id="hidden_job_id"  name="hidden_job_id" />
							</td>
							<td>
								<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_generate_report(1)" /> 
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</form> 
	</div>
	<div id="report_container" style="margin:10px 0;"></div>
	<div id="report_container2"></div>  
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
