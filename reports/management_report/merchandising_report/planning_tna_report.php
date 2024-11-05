<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Planning AS @ TNA.
Functionality	:
JS Functions	:
Created by		:	Kausar
Creation date 	: 	08-03-2021
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

echo load_html_head_contents("Planning AS @ TNA","../../../", 1, 1, $unicode,1,'');

?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters =
	{
		col_7: "none",
		col_operation: 
		{
			id: ["td_fabAmt","td_aopAmt","td_accAmt","td_embAmt","td_labAmt","td_ttlAmt","td_wipQty","td_wipValue"],
			col: [17,18,20,22,24,26,27,28],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated(type)
	{
		freeze_window(3);
		if(form_validation('cbo_company_id*cbo_fiscal_year','Company*Fiscal Year')==false)
		{
			release_freezing();
			return;
		}
		
		$('#report_container_details').html('');
		
		var report_title=$( "div.form_caption" ).html();
		if($('#cbo_location_id').val()!=0)
		{
			$("#cbo_location_id").attr("disabled",true);
		}
		else
		{
			$("#cbo_location_id").attr("disabled",false);
		}
		
		if(type==1 || type==3)
		{
			var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_client*cbo_buyer_id*cbo_order_status*cbo_fiscal_year*cbo_season_id',"../../../")+'&report_title='+report_title;
		}
		else{
			var data="action=report_generate_master&reporttype="+type+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_client*cbo_buyer_id*cbo_order_status*cbo_fiscal_year*cbo_season_id',"../../../")+'&report_title='+report_title;
		}
		
		http.open("POST","requires/planning_tna_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("**");
			//alert(reponse[0]);
			//alert(reponse[1]);
			//var tot_rows=reponse[0];
			$('#report_container2').html(reponse[0]);

			//document.getElementById('report_container').innerHTML=report_convert_button('../../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
			//setFilterGrid("table_body",-1,tableFilters);
			release_freezing();
			show_msg('3');
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$("#table_body tr:first").hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflow="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";

		$("#table_body tr:first").show();
	}
	
	function generate_report(company_id,monthindex,taskid,location_id,buyer_id,orderStatus,client_id,action,task_type='')
	{
		//$('#report_container_details').html('');
		//show_list_view(strdata,'month_buyer_details_list_view','report_container_details','requires/planning_tna_report_controller', '');
		var param='width=1280px,height=500px,center=1,resize=1,scrolling=0';
		var titel="Po Details Popup";
		if(taskid==9) var titel="Labdip Submission";
		if(taskid==10) var titel="Labdip Approval";
		if(taskid==48) var titel="Yarn Allocation";
		if(taskid==52) var titel="Dyed Yarn Receive";
		if(taskid==60) var titel="Grey Production";
		if(taskid==61) var titel="Dyeing";
		if(taskid==63) var titel="AOP Receive";
		if(taskid==73)
		{
			var titel="F. Fabric Recvd";
			if(task_type=='f_fabric_rcvd_mfg_kg') var titel="F. Fabric Rcvd ( MFG ) ( Kg )";
			if(task_type=='f_fabric_rcvd_pur_kg') var titel="F. Fabric Rcvd ( Pur ) ( Kg )";
			if(task_type=='f_fabric_rcvd_pur_pcs') var titel="F. Fabric Rcvd ( Pur ) ( Pcs )";
			if(task_type=='f_fabric_rcvd_pur_yds_meter') var titel="F. Fabric Rcvd ( Pur ) ( yds/meter )";
		} 
		if(taskid==84) var titel="Cutting QC";
		if(taskid==267) var titel="Printing Receive";
		if(taskid==268) var titel="Embroidery Receive";
		if(taskid==86) var titel="Sewing[Pcs]";
		if(taskid==902) var titel="Sewing[Minutes]";
		if(taskid==90) var titel="Garments Wash Rcv";
		if(taskid==88) var titel="Garments Finishing";
		if(taskid==903) var titel="Shipment [Pcs]";
		if(taskid==904) var titel="Shipment [Minutes]";
		if(taskid==905) var titel="Shipment [Value]";
		if(action=="order_quantity_popup")
		{
			param='width=1280px,height=350px,center=1,resize=1,scrolling=0';

		}
		else if(action=="total_order_quantity_popup")
		{
			param='width=1280px,height=350px,center=1,resize=1,scrolling=0';
			
		}
		else if(action=="buyer_wise_capacity_popup")
		{
			param='width=1280px,height=350px,center=1,resize=1,scrolling=0';
			if(taskid==2000) var titel="Buyer Wise Allocation (Min)%";
			if(taskid==2000.5) var titel="Buyer Wise Allocation Min";
			if(taskid==2001) var titel="Buyer Wise Projection (Min)";
			if(taskid==2002) var titel="Buyer Wise Projection (Value)";
			if(taskid==2003) var titel="Buyer Wise Capacity vs Projection (Min)%";
			if(taskid==2004) var titel="Buyer Wise Capacity vs Confirmed Booking (Min)%";
			if(taskid==2005) var titel="Buyer Wise Capacity vs Sewing Plan( (Min)%";
			if(taskid==2006) var titel="Buyer Wise Last Projection (Pcs)";
			
		}
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/planning_tna_report_controller.php?company_id='+company_id+'&monthindex='+monthindex+'&taskid='+taskid+'&location_id='+location_id+'&buyer_id='+buyer_id+'&orderStatus='+orderStatus+'&client_id='+client_id+'&action='+action+'&task_type='+task_type,titel,param ,'../../');
        emailwindow.onclose=function()
        {
            
        }
	}
	function buyerConfig(data)
	{
		if(data>0)
		{
			$('#cbo_client').val(0);
			$('#cbo_client').attr('disabled','true');
			
		}
		else
		{
			$('#cbo_client').removeAttr('disabled');
		}
		load_drop_down( 'requires/planning_tna_report_controller', data, 'load_drop_down_season', 'season_td');
	}
	function clientConfig(data)
	{
		if(data>0)
		{
			$('#cbo_buyer_id').val(0);
			$('#cbo_buyer_id').attr('disabled','true');
		}
		else
		{
			$('#cbo_buyer_id').removeAttr('disabled');
		}
	}
	
	/*function toggleSigns(span) {
		//alert(span)
		if($("#"+span).text()=='+'){
			$("#"+span).text("-")
		}else{
			$("#"+span).text("+")
		}
	}
	
	function yearT(span,selector){
		//alert(span+'_'+selector)
		$( selector ).toggle( "fast", function() {
		});
		toggleSigns(span)
	}*/
</script>

<style>
	/*.year,.month,.buyer,.monthdetails,.buyerdetails,.podtls{
		display:none;
	}
	
	.adl-signs{
		font-weight:bold;
		font-size:18px;
		cursor:pointer
	}*/
</style>
</head>
<body onLoad="set_hotkey();">
<form id="businessAnalysisReport_1">
    <div style="width:100%;" align="center">
        <?=load_freeze_divs ("../../../",$permission); ?>
          <h3 align="left" id="accordion_h1" style="width:1020px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
         <fieldset style="width:1020px;" id="content_search_panel">
            <table class="rpt_table" width="1020" cellpadding="1" cellspacing="2" align="center" border="1" rules="all">
                <thead>
                    <th width="140" class="must_entry_caption">Company</th>
                    <th width="140">Location</th>
                    <th width="120">Client</th>
                    <th width="120">Buyer</th>
                    <th width="90">Season</th>
                    <th width="120">Status</th>
                    <th width="120" class="must_entry_caption">Fiscal Year</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('businessAnalysisReport_1','report_container*report_container2','','','');" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td><?=create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "load_drop_down( 'requires/planning_tna_report_controller', this.value, 'load_drop_down_location', 'location_td'); load_drop_down( 'requires/planning_tna_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td'); load_drop_down( 'requires/planning_tna_report_controller', this.value, 'load_drop_down_client', 'party_type_td');"); ?></td>
                        <td id="location_td"><?=create_drop_down( "cbo_location_id", 140, $blank_array,"", 1, "-All-", $selected, "" ); ?></td>
                        <td id="party_type_td"><?=create_drop_down( "cbo_client", 120, $blank_array,"", 1, "--ALL--", $selected, "" ); ?></td>
                        <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 120, $blank_array,"", 1, "-All-", $selected, "" ); ?></td>
                        <td id="season_td"><?=create_drop_down( "cbo_season_id", 80, $blank_array,'', 1, "-Season-",$selected, "" ); ?></td>
                        <td>
						<?php
						$statusArray=array(1 => "Open Order", 2 => "Close Order");
                        $starting_year  =date('Y', strtotime('-5 year'));
                        $ending_year = date('Y', strtotime('+4 year'));
						$fiscal_year_arr=array();
                        for($starting_year; $starting_year <= $ending_year; $starting_year++)
						{
							$fiscal_year='';
							$conYear=1;
							$fiscal_year=$starting_year.'-'.($starting_year+$conYear);
							//echo $starting_year.'-'.($starting_year+$conYear).'<br>';
							$fiscal_year_arr[$fiscal_year]=$fiscal_year;
                        }
						echo create_drop_down( "cbo_order_status", 120, $statusArray, "",  1, "All Order", 0, "",0 ); ?></td>
                        <td><?=create_drop_down( "cbo_fiscal_year", 120, $fiscal_year_arr, "",  1, "--Select--", $selected, "",0 ); ?></td>
                        <td><input type="button" id="show_button" class="formbutton" style="width:70px;" value="Plan" onClick="fn_report_generated(3);" />
                        	<input type="button" id="show_button" class="formbutton" style="width:70px;" value="Production" onClick="fn_report_generated(2);" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div id="report_container3"></div>
    <br>
    <div id="report_container_details"></div>
    
 </form>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>