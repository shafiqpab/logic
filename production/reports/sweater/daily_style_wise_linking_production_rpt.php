<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Daily Style Wise Linking Production Report
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	02-02-2022
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
echo load_html_head_contents("Daily Style Wise Linking Production Report","../../../", 1, 1, $unicode,1); 
?>	 
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function generate_report(rpt_type)
	{		

		if( form_validation('cbo_company_id*txt_date','Company,Date')==false )
		{
			return;
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_wo_company_name*cbo_buyer_id*hdn_job_id*cbo_ship_status*txt_date',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
				
		// alert(data); return;
		freeze_window(3);
		http.open("POST","requires/daily_style_wise_linking_production_rpt_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{	
			if(http.readyState == 4) 
			{
				//alert (http.responseText);
				var reponse=trim(http.responseText).split("####");
				$("#report_container2").html(reponse[0]);  
				
				release_freezing();
				//alert(reponse[1]);
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				
				setFilterGrid("table_body",-1);							
				show_msg('3');
				release_freezing();
			}
		}   
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$("#table_body tr:first").hide();
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
		
		$("#table_body tr:first").show();
	}
	
	function openmypage_job_no(title) 
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_id").val();	
		var buyer = $("#cbo_buyer_id").val();
		var page_link='requires/daily_style_wise_linking_production_rpt_controller.php?action=job_popup&companyID='+company+'&buyer_name='+buyer;  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			var job_no=this.contentDoc.getElementById("hide_job_no").value; 
			var style_des_no=this.contentDoc.getElementById("hide_style_no").value; 

			$("#hdn_job_id").val(job_id);
			$("#txt_job_no").val(job_no);
			$("#txt_style_ref_no").val(style_des_no);
		}
	}

</script>
</head>
<body onLoad="set_hotkey();">
<form id="bundleTrackReport_1">
    <div style="width:100%;" align="center">    
        <? echo load_freeze_divs ("../../../",'');  ?>
         <h3 style="width:1020px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')">-Search Panel</h3> 
         <div id="content_search_panel" >      
         <fieldset style="width:1020px;">
             <table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
             	<thead>
                    <th width="140" class="must_entry_caption">Company Name</th>
					<th class="">Working Company</th>
                    <th width="120">Buyer</th>
                    <th width="120">Job No</th>
                    <th width="120">Style Ref. No.</th>                
					<th width="100">Ship Status</th>
					<th  width="100" class="must_entry_caption"> Date </th>
                    <th><input type="reset" name="res" id="res" value="Reset" style="width:70px" class="formbutton" onClick="reset_form( 'bundleTrackReport_1', 'report_container','','','')" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
							<? echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/daily_style_wise_linking_production_rpt_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
						</td>
                        <td>
                        	<? echo create_drop_down( "cbo_wo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" ); ?>
                        </td>
					    <td id="buyer_td">
							<? echo create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?>
						</td>                       
                        <td>
                            <input type="text" id="txt_job_no" name="txt_job_no" style="width:110px" class="text_boxes" placeholder="Browse" onDblClick="openmypage_job_no('Job No Popup');" readonly/>
                            <input type="hidden" name="hdn_job_id" id="hdn_job_id" value="">
                        </td>
                        <td>
							<input type="text" id="txt_style_ref_no" name="txt_style_ref_no" style="width:110px" class="text_boxes" placeholder="Browse" onDblClick="openmypage_job_no('Style Ref. No Popup');" readonly/>
						</td> 
						<td>
							<? $shipStatus=array(1 => "Partial or Pending", 2 => "Full Shipped");
							echo create_drop_down( "cbo_ship_status", 100, $shipStatus,"", 1, "--Select--", 1, "",0,"" ); ?>
						</td>
						<td>
							<input type="text" name="txt_date" id="txt_date" class="datepicker" style="width:70px" placeholder="Date" >
                        <td>
							<input type="button" name="search1" id="search1" value="Show" onClick="generate_report(1);" style="width:70px" class="formbutton" />
						</td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    	</div>
    </div>
    <div id="report_container" align="center" style="padding: 10px 0;"></div>
    <div id="report_container2" align="left">
    <div style="float:left; " id="report_container3"></div>
    </div>
 </form>   
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
