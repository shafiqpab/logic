<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise DHU Report
Functionality	:	
JS Functions	:
Created by		:	Kamrul
Creation date 	: 	23-07-2023
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
echo load_html_head_contents("Line Wise DHU Report", "../../", 1, 1,$unicode,1,'');
?>	
<script>
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
	var permission = '<? echo $permission; ?>';
	

	function generate_report(report_type)
	{

		let inter_ref= $("#txt_int_ref").val();
		let style_ref= $("#txt_style_ref").val();
		let po_no= $("#txt_po_no").val(); 

		if(inter_ref=="" && style_ref=="" && po_no=="")
        {        
        	if (form_validation('cbo_company_name*cbo_wo_company_name*txt_date_from*txt_date_to','Company Name*Working Company*From Date*To Date')==false)
			{
				return;
			}
        }
		else
		{
			if (form_validation('cbo_company_name*cbo_wo_company_name','Company Name*Working Company')==false)
			{
				return;
			}
		}	
	
		
		var report_title=$( "div.form_caption" ).html();
		
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_wo_company_name*cbo_location*cbo_floor*cbo_line_id*cbo_floor_group_name*cbo_shift_name*cbo_buyer_name*txt_int_ref*txt_job_no*txt_style_ref*txt_po_no*cbo_production_status*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title+'&report_type='+report_type;
		freeze_window(3);
		http.open("POST","requires/line_wise_dhu_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;  
	}
	
	function generate_report_reponse()
	{	
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split("****");
			show_msg('3');
			release_freezing();
			
			//$("#report_container2").html(reponse[0]);  
			//document.getElementById('report_container').innerHTML = report_convert_button('../../'); 
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>';


		} 
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		//$('#table_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 

		//$('#table_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="425px";
	}

	function reset_form()
	{
		$("#hidden_style_id").val("");
		$("#hidden_order_id").val("");
		$("#hidden_job_id").val("");
	}
	
	
	

	function open_style_ref()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/line_wise_dhu_report_controller.php?action=style_wise_search&company='+company+'&buyer='+buyer+'&job_no='+job_no+'&job_id='+job_id+'&cbo_year='+cbo_year; 
		var title="Search Style Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=450px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var styleID=this.contentDoc.getElementById("txt_selected_id").value;
			var styleDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_style_ref").val(styleDescription);
			$("#hidden_style_id").val(styleID); 
		}
	}	
	
	function open_order_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var job_no=$("#txt_job_no").val();
		var job_id=$("#hidden_job_id").val();
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var style_no=$('#txt_style_ref').val();
		var style_id=$('#hidden_style_id').val();
		var cbo_year = $("#cbo_year").val();
		var page_link='requires/line_wise_dhu_report_controller.php?action=order_wise_search&company='+company+'&buyer='+buyer+'&style_id='+style_id+'&job_id='+job_id+'&job_no='+job_no+'&cbo_year='+cbo_year; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=560px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_order_no").val(prodDescription);
			$("#hidden_order_id").val(prodID); 
		}
	}
	 
	function open_job_no()
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();	
		var page_link='requires/line_wise_dhu_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year; 
		var title="Search Order Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=510px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var prodID=this.contentDoc.getElementById("txt_selected_id").value;
			//alert(prodID); // product ID
			var prodDescription=this.contentDoc.getElementById("txt_selected").value; // product Description
			$("#txt_job_no").val(prodDescription);
			$("#hidden_job_id").val(prodID); 
			//alert($("#hidden_job_id").val())
		}
	}
	
	
	function openmypage_order(company_id,order_id,order_number,insert_date,type,action,width,height)
	{
		var popup_width=width;
		var popup_height=height;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_dhu_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&order_id='+order_id+'&order_number='+order_number+'&type='+type, 'Detail Veiw', 'width='+popup_width+','+'height='+popup_height+',center=1,resize=0,scrolling=0','../');
	}	
	
	
	
	
	function openmypage(company_id,jobnumber_prefix,insert_date,action,width)
	{
		var popup_width=width;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/line_wise_dhu_report_controller.php?company_id='+company_id+'&action='+action+'&insert_date='+insert_date+'&jobnumber_prefix='+jobnumber_prefix, 'Detail Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../');
	}
	
	
	function disable_enable(val)
	{
		$("#txt_job_no").val("");
		$("#txt_style_ref").val("");
		$("#txt_order_no").val("");
		if(val==1 || val==2)
		{
			$('#txt_job_no').attr('disabled','disabled');
			$('#txt_style_ref').attr('disabled','disabled');
			$('#txt_order_no').attr('disabled','disabled');
		}
		else
		{
			$('#txt_job_no').removeAttr('disabled','disabled');
			$('#txt_style_ref').removeAttr('disabled','disabled');
			$('#txt_order_no').removeAttr('disabled','disabled');
		}
	}
	
	function job_no_popup(type)
	{
		if( form_validation('cbo_company_name','Company Name')==false)
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var buyer=$("#cbo_buyer_name").val();
		var cbo_year = $("#cbo_year").val();
		var txt_job_no = $("#txt_job_no").val();	
		var page_link='requires/line_wise_dhu_report_controller.php?action=job_wise_search&company='+company+'&buyer='+buyer+'&cbo_year='+cbo_year+'&type='+type+'&txt_job_no='+txt_job_no; 
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
				$('#txt_style_ref').val(job_no);
				$('#txt_style_hidden').val(job_id);
			}
			else if(type==3)
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
	<div style="width:100%;" align="center"> 
		<? echo load_freeze_divs ("../../",'');  ?>
		<h3 style="width:1800px;  margin-top:20px;" align="center" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel','')"> -Search Panel</h3>
		<div style="width:100%;" align="center" id="content_search_panel">
			<form id="dateWiseProductionReport_1"  autocomplete="off">    
				<fieldset style="width:1800px;">
					<table class="rpt_table" width="1780px" cellpadding="0" cellspacing="0" align="center" rules="all">
						<thead>                    
							<tr>
								<th class="must_entry_caption" width="150">Company Name</th> 

								<th class="must_entry_caption" width="150">Working Company</th>
                                <th width="100">Location</th>
                                <th width="100">Floor</th>
								<th width="100">Line</th>  
                                <th width="100">Floor Group</th>  

								<th width="100" >Shift Name</th>

								<th width="150">Buyer Name</th>
								

								<th width="80">Int. Ref. </th>

								<th width="100">Style</th>                       
								<th width="100">Order No </th> 

								<th class="must_entry_caption" width="100">Production</th>                       
								<th class="must_entry_caption" width="200">Date Range</th> 

								<th><input type="reset" id="reset_btn" class="formbutton" style="width:60px" value="Reset" onClick="reset_form()"/></th>
							</tr>   
						</thead>
						<tbody>
							<tr class="general">
								<td id="company_td"> 
									<?
									echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/line_wise_dhu_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
									?>
								</td>
                                
								<td width="100"> 
									<?
										echo create_drop_down( "cbo_wo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/line_wise_dhu_report_controller', this.value, 'load_drop_down_location', 'location_td' );" );
									?>
								</td>                   
								<td width="100" id="location_td">
									<? 
										echo create_drop_down( "cbo_location", 100, $blank_array,"", 1, "-- Select --", $selected, " load_drop_down( 'requires/line_wise_dhu_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );", 1, "" );
									?>
								</td>
								<td width="100" id="floor_td">
									<? 
										echo create_drop_down( "cbo_floor", 100, $blank_array,"", 1, "-- Select --", $selected, "", 1, "" );
									?>
								</td>
								<td id="line_td">
									<? 
										echo create_drop_down( "cbo_line_id", 100, $blank_array,"", 1, "-- Select Line --", $selected,"",1, "" );
									?>                            
								</td>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_floor_group_name", 100, "SELECT a.group_name from lib_prod_floor a where a.status_active=1 and a.is_deleted=0 and a.group_name is not null group by a.group_name  order by a.group_name","group_name,group_name", 1, "Select Group", $selected, "",0,"" );
									?>
								</td>

								<td>
									<?
									echo create_drop_down( "cbo_shift_name", 100, $shift_name,"", 1, "-Select Type-", 0, "",0 );
									?>
								</td>

								<td id="buyer_td">
									<? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "",1,"" ); ?>
								</td>
								<td>
								   <input type="text" id="txt_int_ref"  name="txt_int_ref"  style="width:80px" class="text_boxes" placeholder="Write" />
								</td>
								<td>
									<input type="text"  name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(2);"> 
									<input type="hidden"  name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(1);"> 

								</td>
								<td>
									<input type="text"  name="txt_po_no" id="txt_po_no" class="text_boxes" style="width:90px;" tabindex="1" placeholder="Write/Browse" onDblClick="job_no_popup(3);"> 
								</td>
								<td>
									<?
									$production_status = array(1 => "Cutting", 2 => "Sewing", 3 => "Iron");
										echo create_drop_down( "cbo_production_status", 150, $production_status,"", 0, "-- Production Status --", 2, "",0 );
									?>
								</td>
								<td align="center">
                                    <input name="txt_date_from" id="txt_date_from" class="datepicker"
                                           style="width:70px"> To
                                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                                </td>


								<td>
									<input type="button" id="show_button1" class="formbutton"   value="Floor Wise" onClick="generate_report(1)" />
									<input type="button" id="show_button2" class="formbutton"   value="Hour Wise" onClick="generate_report(2)" />
									

								</td>
								
							</tr>

							
						</tbody>
					</table>
				</fieldset>
			</form> 
		</div>
	</div> 
	<div id="report_container" align="center"></div>
	<div id="report_container2"></div>  
</body>
     
 
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
