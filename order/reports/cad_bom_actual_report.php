<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Style Wise Material Vs Audit Report.
Functionality	:	
JS Functions	:
Created by		:	Shariar
Creation date 	: 	07-02-2024
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
	$menu_id=$_SESSION['menu_id'];

//--------------------------------------------------------------------------------------------------------------------
	echo load_html_head_contents("Cad Vs BOM Vs Actual","../../", 1, 1, $unicode,1,1,'','','');
	?>	
	<script>

		if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php"; 
		var permission='<?=$permission; ?>'; 
		function fn_report_generated(report){
			 
			//txt_style
			 var txt_job_no=document.getElementById('txt_job_no').value;

			 /* if(txt_job_no!="" || txt_style!="")
            {
                if(form_validation('cbo_company_name*cbo_year','Company*Year')==false)
                {
                    return;
                }
            }
            else
            { */
                if(form_validation('cbo_company_name*txt_job_no','Company*Job No')==false)
                {
                    return;
                }
            //}
			
				if(report==1){
					var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*cbo_year*txt_job_id',"../../");
				}
				freeze_window(3);
				 
				http.open("POST","requires/cad_bom_actual_report_controller.php",true);
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
				//document.getElementById('report_container').innerHTML='<a href="'+response[1]+'" style="text-decoration:none" ><input type="button" value="Convert To Excel" name="excel" id="excel" class="formbutton" style="width:135px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Html Preview" name="Print" class="formbutton" style="width:120px"/>'; 
					document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				show_msg('3');
				release_freezing();
			}
		}
		
		function openmypage(page_link,title,type){
		
			var company_name=document.getElementById('cbo_company_name').value;
			var cbo_buyer_name =$("#cbo_buyer_name").val();
			var cbo_year =$("#cbo_year").val();
			//alert(cbo_budget_version);
			page_link=page_link+'&garments_nature='+garments_nature+'&company_name='+company_name+'&cbo_buyer_name='+cbo_buyer_name+'&cbo_year='+cbo_year+'&type='+type;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1200px,height=450px,center=1,resize=0,scrolling=0','../../')
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				var theemail=this.contentDoc.getElementById("selected_job");
				var theemail1=this.contentDoc.getElementById("selected_year");
				var theemail2=this.contentDoc.getElementById("selected_company");
				var theemail3=this.contentDoc.getElementById("txt_job_id");
				//alert(theemail3.value);
				if (theemail.value!=""){
					
					if(type==1)
					{
						document.getElementById('txt_job_no').value=theemail.value;
					}
					document.getElementById('cbo_year').value=theemail1.value;
					document.getElementById('cbo_company_name').value=theemail2.value;
					document.getElementById('txt_job_id').value=theemail3.value;
					 
					//$("#g_exchange_rate").attr('disabled',true);
					freeze_window(5);
					release_freezing();
				}
			}
		}
	function new_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><title></title><link rel="stylesheet" href="../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
	}
	
	function new_window2(comp_div, container_div)
	{
		document.getElementById(comp_div).style.visibility="visible";
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById(container_div).innerHTML+'</body</html>');
		document.getElementById(comp_div).style.visibility="hidden";
		d.close();
	}
</script>

</head>

<body onLoad="set_hotkey();">
	
	<form id="cost_breakdown_rpt">
		<div style="width:100%;" align="center">
			<? echo load_freeze_divs ("../../"); ?>
			<h3 align="left" id="accordion_h1" style="width:620px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
			<div id="content_search_panel"> 
				<fieldset style="width:620px;">
					<table class="rpt_table" width="620" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
						<thead>
							<tr>                   
								<th width="150" class="must_entry_caption">Company Name</th>
                                <th>Buyer Name</th>
								<th width="65" class="must_entry_caption">Job Year</th>
								<th width="80" class="must_entry_caption">Job No /Style</th>
								<th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" /></th>
							</tr>
						</thead>
						<tbody>
							<tr class="general">
								<td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/cad_bom_actual_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?></td>
                            <td id="buyer_td">
	                        <?
	                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "--All Buyer--", $selected, "",0,"" );
	                        ?>
	                   	   </td>
                        
								<td><? echo create_drop_down( "cbo_year", 65, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
								<td>
                                <input type="hidden" name="txt_job_id" id="txt_job_id" class="text_boxes" style="width:30px" />
                                <input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:70px"  placeholder="Browse Or Write"  onDblClick="openmypage('requires/cad_bom_actual_report_controller.php?action=order_popup','Job Selection Form',1)" /></td>
							<td><input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="fn_report_generated(1);" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
	</div>
	<div id="report_container" align="center"></div>
	<div id="report_container2"></div>
</form>    
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
