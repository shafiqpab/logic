<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create PI Pending Report.
Functionality	:
JS Functions	:
Created by		:	Rakib
Creation date 	: 	20-10-2020
Updated by 		:
Update date		:
QC Performed BY	:
QC Date			:
Comments		:
*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$permission = $_SESSION['page_permission'];

//--------------------------------------------------------------------------------
echo load_html_head_contents('PI Pending Report', '../../', 1, 1,'','','');
?>

<script>
 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = '../../logout.php';

 	function openmypage_pi()
	{		

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company = document.getElementById('cbo_company_name').value;	
		var page_link='requires/pi_pending_report_controller.php?action=pi_popup&company='+company; 
		var title="Search PI Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var pi_id=this.contentDoc.getElementById("txt_selected_id").value;  //PI ID
			var pi_no=this.contentDoc.getElementById("txt_selected_no").value;  //PI no	
			$("#txt_pi_no").val(pi_no);
			$("#txt_pi_id").val(pi_id);
		}
	}

 	function openmypage_btb_lc()
	{		

		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}

		var company = document.getElementById('cbo_company_name').value;	
		var page_link='requires/pi_pending_report_controller.php?action=btb_lc_popup&company='+company; 
		var title="Search LC Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var btb_lc_id=this.contentDoc.getElementById("txt_selected_id").value; // BTB LC ID
			var btb_lc_no=this.contentDoc.getElementById("txt_selected_no").value; // BTB LC no
			$("#txt_btb_lc_id").val(btb_lc_id);
			$("#txt_btb_lc_no").val(btb_lc_no);
		}
	}

	function generate_report(type)
	{



		var cbo_company_name = document.getElementById('cbo_company_name').value;
		var pi_num = document.getElementById('txt_pi_no').value;
		//var pi_ids = document.getElementById('txt_pi_id').value;
		var btb_num = document.getElementById('txt_btb_lc_no').value;
		//var btb_id = document.getElementById('txt_btb_lc_id').value;
		var pi_type = document.getElementById('pi_pending_type').value*1;

 		
		
		if(pi_num=="" && btb_num=="" && pi_type==1 )
		{
			//alert(pi_num);
			if(cbo_company_name == 0) 
			{			
				alert("Please Select  company ");
				return;			
			}
			else if( form_validation('txt_date_from*txt_date_to','Form Date*To Date')==false )
			{
				return;
			}			
				
		}
		else
		{
		
			
			if(cbo_company_name == 0 ) 
			{			
				alert("Please Select company");
				return;			
			}
				
		}



		var pi_no=$("#txt_pi_no").val();
		var btb_lc_no=$("#txt_btb_lc_no").val();
		if (pi_no == '') $("#txt_pi_id").val('');
		if (btb_lc_no == '') $("#txt_btb_lc_id").val('');

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string("cbo_company_name*cbo_supplier_id*txt_pi_no*txt_pi_id*txt_btb_lc_no*txt_btb_lc_id*pi_pending_type*txt_date_from*txt_date_to","../../")+'&report_title='+report_title;
		//alert(data);return;
		freeze_window(3);
		http.open("POST","requires/pi_pending_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var response=trim(http.responseText).split("****");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			//setFilterGrid('table_body',-1);
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow='auto';
		document.getElementById('scroll_body').style.maxHeight='none';
		$('#table_body tbody').find('tr:first').hide();
		var w = window.open('Surprise', '#');
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#table_body tbody').find('tr:first').show();
		document.getElementById('scroll_body').style.overflowY='scroll';
		document.getElementById('scroll_body').style.maxHeight='300px';
	}

	function change_color(v_id,e_color)
	{
		if (document.getElementById(v_id).bgColor=='#33CC00')
			document.getElementById(v_id).bgColor=e_color;
		else
			document.getElementById(v_id).bgColor='#33CC00';
	}

	function openmypage_file(mst_id,action)
	{
		var page_link='requires/pi_pending_report_controller.php?action='+action+'&mst_id='+mst_id;
		var title="Image View";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=330px,center=1,resize=0,scrolling=0','../')
	}

	function openmypage_piItemmDescription(company_id,pi_id,item_category_id,lc_sc_no,action)
	{
		var page_link='requires/pi_pending_report_controller.php?action='+action+'&company_id='+company_id+'&pi_id='+pi_id+'&item_category_id='+item_category_id+'&lc_sc_no='+lc_sc_no;
		var title="PI Item Description";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=910px,height=330px,center=1,resize=0,scrolling=0','../')
	}

</script>
</head>

<body onLoad="set_hotkey();">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",''); ?>
		<form id="pipendingreport" name="pipendingreport">
			<div style="width:950px;">
				<h3 align="left" id="accordion_h1" style="width:950px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
				<div id="content_search_panel">
					<fieldset style="width:950px;">
						<table class="rpt_table" cellspacing="0" cellpadding="0" width="930" border="1" rules="all">
							<thead>
								<th width="160" class="must_entry_caption">Company Name</th>
								<th width="150">Supplier Name</th>
								<th width="120">PI No</th>
								<th width="120">BTB LC NO</th>
								<th width="100">PI Status</th>
								<th width="180" class="must_entry_caption">PI Date</th>
								<th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('pipendingreport','report_container*report_container2','','','')" /></th>
							</thead>
							<tbody>
								<tr>
									<td>
										<?
										echo create_drop_down("cbo_company_name", 160,"select id, company_name from lib_company where status_active =1 and is_deleted=0 and core_business in(1,3) order by company_name",'id,company_name', 1, '-- Select Company --',0,"load_drop_down('requires/pi_pending_report_controller',this.value, 'load_drop_down_supplier', 'supplier_td' );",0);
										?>
									</td>
									<td id="supplier_td">
										<?
										echo create_drop_down('cbo_supplier_id', 150, $blank_array, '', 1, '-- All Supplier --', $selected, '', 0, '' ); ?>						
									</td>
									<td>
										<input type="text" id="txt_pi_no" name="txt_pi_no" ondblclick="openmypage_pi()" class="text_boxes" style="width:120px;" placeholder="Write/Browse"/>
										<input type="hidden" name="txt_pi_id" id="txt_pi_id"/>
									</td>
									<td>
										<input type="text" id="txt_btb_lc_no" name="txt_btb_lc_no" ondblclick="openmypage_btb_lc()" class="text_boxes" style="width:120px;" placeholder="Write/Browse"/>
										<input type="hidden" name="txt_btb_lc_id" id="txt_btb_lc_id"/>  
									</td>
									<td>										   
                        				<?
											$pi_status_array=array(1=>"All",2=>"Done",3=>"Pending");
		                            		echo create_drop_down("pi_pending_type", 100, $pi_status_array,'', 0, '', 1, "", 0); 
		                            	?>
									</td>
									<td>
										&nbsp;&nbsp;<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date">
									</td>
									<td align="center">
										<input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton"/>
									</td>
								</tr>
								<tr>
									<td colspan="8" align="center" valign="bottom"><? echo load_month_buttons(1); ?>
								</tr>
								</tbody>
							</table>
						</fieldset>
					</div>
				</div>
				<div id="report_container" align="center"></div>
				<div id="report_container2"></div>
			</form>
		</div>
	</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
