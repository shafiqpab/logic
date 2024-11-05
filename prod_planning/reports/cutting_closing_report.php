<?php 
/*********************************************** Comments *************************************
*	Purpose			: 	
*	Functionality	:	
*	JS Functions	:
*	Created by		:	Thorat
*	Creation date 	: 	28-Apr-2022
*	Updated by 		: 		
*	Update date		: 		   
*	QC Performed BY	:		
*	QC Date			:	
*	Comments		:
************************************************************************************************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//=============================================================
echo load_html_head_contents("Cutting Closing Report", "../../", 1, 1,'', '', '');
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';

	function fn_show_report(type)
	{
		if (type==1 || type==3) 
		{
			var job_no = $("#txt_job_no").val();
			var company_name = $("#cbo_company_name").val();
			//  var file_no		 = $("#file_no").val();
			//alert(job_no);
			if (company_name="" && job_no =="")
			{
				alert('Please enter value to Job no and Company. field');
				return;
			}
			else
			{	
				var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_season_year*txt_job_no*txt_job_id*hide_order_id*cbo_gmts_color*txt_order_no*file_no*txt_ref_no*txt_style_ref_id*txt_style_ref_no',"../../");
				//alert(data);
				freeze_window('3');
				http.open("POST","requires/cutting_closing_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
			
		}

		if (type==2) 
		{
			var job_no = $("#txt_job_no").val();
			var company_name = $("#cbo_company_name").val();
			//  var file_no		 = $("#file_no").val();
			//alert(job_no);
			if (company_name="" && job_no =="")
			{
				alert('Please enter value to Job no and Company. field');
				return;
			}
			else
			{	
				var data="action=report_generate&type="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_season_year*txt_job_no*txt_job_id*hide_order_id*cbo_gmts_color*txt_order_no*file_no*txt_ref_no*txt_style_ref_id*txt_style_ref_no',"../../");
				//alert(data);
				freeze_window('3');
				http.open("POST","requires/cutting_closing_report_controller.php",true);
				http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				http.send(data);
				http.onreadystatechange = fn_report_generated_reponse;
			}
			
		}
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText);
			show_msg('3');
			var response=trim(http.responseText).split("####");
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>'+'&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window()" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>';

			release_freezing();
		}
	}

	function new_window()
	{
		// document.getElementById('scroll_body').style.overflow='auto';
		// document.getElementById('scroll_body').style.maxHeight='none'; 
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>'); 
		d.close();
		// document.getElementById('scroll_body').style.overflowY='scroll';
		// document.getElementById('scroll_body').style.maxHeight='300px';
	}	 
	//job search popup
	function openmypage_job()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var buyer_name = $("#cbo_buyer_name").val();
		var cbo_season_year = $("#cbo_season_year").val();
		
		var page_link='requires/cutting_closing_report_controller.php?action=job_no_popup&companyID='+companyID+'&buyer_name='+buyer_name+'&cbo_year='+cbo_season_year;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var data_arr=this.contentDoc.getElementById("hide_job_no").value.split("_");
			fnc_gmt_item(data_arr[1]);
			$('#txt_job_no').val(data_arr[1]);
			$('#txt_job_id').val(data_arr[0]);
			$('#hide_order_id').val(data_arr[2]);
		}
	}

	function openmypage_po()
	{
		if(form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_name").val();
		var job_no = $("#txt_job_no").val();
		var cbo_season_year = $("#cbo_season_year").val();
		var page_link='requires/cutting_closing_report_controller.php?action=order_no_search_popup&companyID='+companyID+'&job_no='+job_no+'&cbo_season_year='+cbo_season_year;
		var title='PO/Order Number Search';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order_no').val(order_no);
			$('#hide_order_id').val(order_id);	 
		}
	}
	function openmypage_fileNo()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			var company = $("#cbo_company_name").val();	
			var buyer = $("#cbo_buyer_name").val();
			var page_link='requires/cutting_closing_report_controller.php?action=fileno_search_popup&company='+company+'&buyer='+buyer;
			var title="Search Item Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				// var order_id=this.contentDoc.getElementById("hide_order_id").value;
				// var order_no=this.contentDoc.getElementById("hide_order_no").value;
				// var intref=this.contentDoc.getElementById("file_no").value;
				var intref=this.contentDoc.getElementById("file_no").value;
				// fnc_gmt_item(data[1]);
				$('#file_no').val(intref);
				// $('#hide_order_id').val(order_id);
				// $('#txt_order_no').val(order_no);

			}
	}
	function openmypage_ref()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
			{
				return;
			}
			var company = $("#cbo_company_name").val();	
			var buyer = $("#cbo_buyer_name").val();
			var page_link='requires/cutting_closing_report_controller.php?action=style_search_popup&company='+company+'&buyer='+buyer;
			var title="Style Search Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				//alert(theform);
				var job_no=this.contentDoc.getElementById("hide_job_no").value;
				var job_id=this.contentDoc.getElementById("hide_job_id").value;
				var style_no=this.contentDoc.getElementById("hide_style_no").value;
				//fnc_gmt_item(job_no);
				// alert(job_no);
				$('#txt_style_ref_id').val(job_id);
				$('#txt_job_no').val(job_no);
				$("#txt_job_no_hidden").val(job_id); 
				$('#txt_ref_no').val(style_no);
				$('#txt_job_no').attr('disabled','true'); 
			}
	}

	// for report lay chart
	function generate_report_lay_chart(data)
	{
		var action	= 'cut_lay_entry_report_print';
		window.open("../../prod_planning/cutting_plan/requires/cut_and_lay_entry_controller.php?data=" + data+'&action='+action, true );
	}

	function onchange_buyer()
	{
		if($('#cbo_buyer_name').val() !=0)
		{
			document.getElementById("cap_cut_date").style.color = "blue";
		}
		else 
		{
			document.getElementById("cap_cut_date").style.color = "";
		}
	}

	// function openmypage_intRef()
	// {
	// 	if( form_validation('cbo_company_name','Company Name')==false )
	// 		{
	// 			return;
	// 		}
	// 		var company = $("#cbo_company_name").val();	
	// 		var buyer = $("#cbo_buyer_name").val();
	// 		var page_link='requires/cutting_closing_report_controller.php?action=intref_search_popup&company='+company+'&buyer='+buyer;
	// 		var title="Search Item Popup";
	// 		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=790px,height=390px,center=1,resize=0,scrolling=0','../')
	// 		emailwindow.onclose=function()
	// 		{
	// 			var theform=this.contentDoc.forms[0];
	// 			var order_no=this.contentDoc.getElementById("hide_order_no").value;
	// 			var order_id=this.contentDoc.getElementById("hide_order_id").value;
	// 			var intref=this.contentDoc.getElementById("hide_int_ref").value;
	// 			// fnc_gmt_item(data[1]);
	// 			$('#int_ref').val(intref);
	// 			$('#hide_order_id').val(order_id);
	// 			$('#txt_order_no').val(order_no);
	// 		}
	// }

	
	function fnc_gmt_item(job_no)
	{
			//alert(job_no);
			load_drop_down( 'requires/cutting_closing_report_controller',job_no,'load_drop_down_gmts_color','gmt_td' );	
	}
</script>
<style>
	hr
	{
		color: #676767;
		background-color: #676767;
		height: 1px;
	}
</style> 
</head>
<body onLoad="set_hotkey();">
	<form id="cuttingLayProductionReport_1">
		<div style="width:100%;" align="center">    
			<? echo load_freeze_divs ("../../",'');  ?>
			<h3 style="width:1110px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
			<div id="content_search_panel">      
				<fieldset style="width:1110px;">
					<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<th class="must_entry_caption">Company Name</th>
							<th>Buyer Name</th>
							<th class="must_entry_caption">Year.</th>
							<th class="must_entry_caption">Job No</th>
							<th>Gmts Color</th>
							<th>PO/Order Number</th>
							<th >File NO.</th>
							<th >Ref. No</th>
							<th>
								<input type="reset" name="res" id="res" value="Reset" onClick="reset_form('cuttingLayProductionReport_1','','','','')" class="formbutton" style="width:50px" /></th>
						</thead>
						<tbody>
							<tr class="general">
								<td>
									
									<? 
									
									echo create_drop_down( "cbo_company_name", 120, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cutting_closing_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); 
									?> 
									
								</td>
								<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, $blank_array,"", 1, "-- Select Buyer --", $selected, "","","" ); ?></td>
						
								<td width="60" id="season_year_td"><!-- fpr show3 button -->
									<? 
										echo create_drop_down( "cbo_season_year", 60, $year,"", 1, "-- All --", date('Y'), "",0,"" );
									?>
								</td>
								<!-- <td>                        
									<input type="text" name="txt_cutting_no" id="txt_cutting_no" class="text_boxes" style="width:100px;" onKeyDown="if (event.keyCode == 13) document.getElementById(this.id).ondblclick()" />
								<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes"  style="width:100px" onDblClick="openmypage_job();"  placeholder="Browse"/>
									<input type="hidden" name="update_id"  id="update_id" readonly />
									<input type="hidden" name="txt_job_no_hidden"  id="txt_job_no_hidden"  />
								</td> -->
								<td>
									<!--<input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px;" />-->
									<input type="text" id="txt_job_no" name="txt_job_no" class="text_boxes" style="width:80px" onDblClick="openmypage_job();" placeholder="Browse. Job"  onChange="fnc_gmt_item(this.value)" readonly="true" />
									<input type="hidden" id="txt_job_id" name="txt_job_id"/>
									<input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
								</td>
								<td id="gmt_td"><? echo create_drop_down( "cbo_gmts_color", 100, $blank_array,"", 1, "-- Select Item --", $selected, "","","" ); ?></td>
								<td>
									<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:100px" placeholder="Browse Or Write" onDblClick="openmypage_po();" onChange="$('#hide_order_id').val('');" autocomplete="off" >
									<input type="hidden" name="hide_order_id" id="hide_order_id" readonly>
								</td>
								<!-- <td>
									<input type="text" name="int_ref"  id="int_ref" class="text_boxes" onDblClick="openmypage_intRef()" placeholder="Browse" readonly="" />
									<input type="hidden" name="int_ref_id" id="int_ref_id"/>  
								</td> -->
								<td>
									<input type="text" name="file_no"  id="file_no" class="text_boxes" onDblClick="openmypage_fileNo()" placeholder="Browse " readonly="" />
								</td>  
								
								<td id="gmt_td">
									
									<!-- <input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Browse"  onDblClick="openmypage_ref();" onChange="fnc_gmt_item(this.value)" readonly  />  -->

									<input type="text" name="txt_ref_no" id="txt_ref_no" class="text_boxes" style="width:100px" placeholder="Browse"  onDblClick="openmypage_ref();" readonly  /> 
									<input type="hidden" name="txt_style_ref_id" id="txt_style_ref_id"/>    
								<input type="hidden" name="txt_style_ref_no" id="txt_style_ref_no"/>     
									</td>  
													
								
								<td>
									<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_show_report(1)" />

									<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show2" onClick="fn_show_report(2)" />
									<input type="button" id="show_button" class="formbutton" style="width:70px" value="Show3" onClick="fn_show_report(3)" />
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div style="display:none" id="data_panel"></div>   
			<div id="report_container" align="center"></div>
			<div id="report_container2" align="left"></div>
		</div>
	</form>   
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

