<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Shipment Schedule Report
Functionality	         :
JS Functions	         :
Created by		         :	Monzu
Creation date 	         :
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         : 	Jahid,Reza Button Action 7
Update date		         : 	07-06-15,06-04-2021
QC Performed BY	         :
QC Date			         :
Comments		         : From this version oracle conversion is start
							Update description(Create New Button Short)
						 	Report short button not Screen Release--I have checked 71-75 no Line=Aziz

*/
session_start(); 
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Order Info","../../../", 1, 1,$unicode,1,1);
?>
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	var tableFilters =
	{
		col_31: "select",
		display_all_text:'Show All',
		col_operation: {
		id: ["total_order_qnty_pcs","total_order_qnty","value_total_order_value","total_ex_factory_qnty","total_short_access_qnty","value_total_short_access_value","value_yarn_req_tot"],
		col: [19,20,23,30,32,33,34],
		operation: ["sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	
	var tableFilters7 =
	{
		col_45: "select",
		display_all_text:'Show All',
		col_operation: {
		id: ["total_order_qnty_pcs","total_order_qnty","value_total_order_value","total_ex_factory_qnty","total_short_access_qnty","value_total_short_access_value","total_access_qnty","value_total_access_value","value_yarn_req_tot"],
		col: [27,28,31,38,40,41,42,43,44],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	
	var tableFilters8 =
	{
		col_45: "select",
		display_all_text:'Show All',
		col_operation: {
		id: ["total_order_qnty","value_total_order_min","value_total_order_value"],
		col: [19,25,26],
		operation: ["sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML"]
		}
	}

	function generate_report_main(rpt_type)
	{
		freeze_window(3);
		/*if(rpt_type==3)
		{
			if( form_validation('cbo_company_name*cbo_buyer_name','Company Name*Buyer Name')==false )
			{
				release_freezing();
				return;
			}
		}*/
		var report_title=$( "div.form_caption" ).html();
		var data="action=report_generate"+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*cbo_team_name*cbo_team_member*cbo_search_by*txt_search_string*txt_date_from*txt_date_to*cbo_category_by*txt_file*txt_ref*cbo_season*cbo_brand_name*cbo_shipment_status*cbo_order_status*cbo_sustainability_standard*cbo_fab_material',"../../../")+'&report_title='+report_title+'&rpt_type='+rpt_type;
		//alert (data); release_freezing(); return;
		
		http.open("POST","requires/shipment_schedule_controller_v3.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_report_reponse;
	}

	function generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			
			$("#report_container2").html(reponse[0]);
			if(reponse[2]==1) //Aziz
			{
				document.getElementById('content_summary3_panel').innerHTML=document.getElementById('shipment_performance').innerHTML;	percent_set();
			}

			else if(reponse[2]==7)
			{
				document.getElementById('content_summary3_panel').innerHTML=document.getElementById('shipment_performance').innerHTML;	percent_set();
			}

			if((reponse[2]==1 && reponse[3]==1 ) || reponse[2]==6 || reponse[2]==7 || reponse[2]==8)
			{
				 //document.getElementById('report_container').innerHTML=report_convert_button('../../../');
				 document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				append_report_checkbox('table_header_1',1);
				
				if( reponse[2]==7){
					setFilterGrid("table_body",-1,tableFilters7); //all button are subtotal
				}
				
				else if( reponse[2]!=6){
					setFilterGrid("table_body",-1,tableFilters); //all button are subtotal
				}
				 if( reponse[2]==8){
					setFilterGrid("table_body_order_8",-1,tableFilters8);
					setFilterGrid("table_body_job_8",-1,tableFilters8); //all button are subtotal
				}
			}
			else{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			}
			show_msg('3');
			release_freezing();
		}
	}

	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#scroll_body tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();
		$('#scroll_body tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
	}

	function percent_set()
	{
		var tot_row=document.getElementById('tot_row').value;
		var tot_value_js=document.getElementById('total_value').value;
		for(var i=1;i<tot_row;i++)
		{
			var value_js=document.getElementById('value_'+i).value;
			var percent_value_js=((value_js*1)/(tot_value_js*1))*100
			document.getElementById('value_percent_'+i).innerHTML=percent_value_js.toFixed(2);
		}
	}

	function openmypage_image(page_link,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{

		}
	}

	function last_ex_factory_popup(action,job_no,id,width)
	{
		//alert(job_no);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_schedule_controller_v3.php?action='+action+'&job_no='+job_no+'&id='+id, 'Last Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

	function smv_popup(action,job_no,id,width)
	{
		//alert(job_no);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_schedule_controller_v3.php?action='+action+'&job_no='+job_no+'&id='+id, 'SMV Set Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

	function order_status(action,id,width)
	{
		//alert(job_no);
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/shipment_schedule_controller_v3.php?action='+action+'&id='+id, 'Po Status', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}

	function search_by(val)
	{
		$('#txt_search_string').val('');
		if(val==1)
		{
			$('#search_by_td_up').html('Order No');
		}
		else if(val==2)
		{
			$('#search_by_td_up').html('Style No');
		}
		else
		{
			$('#search_by_td_up').html('Job No');
		}
	}
	
	function print_button_setting()
	{
		//$('#data_panel').html('');
		get_php_form_data($('#cbo_company_name').val(),'print_button_variable_setting','requires/shipment_schedule_controller_v3' ); 
	}
	function print_report_button_setting(report_ids) 
	{
		$('#search1').hide();
		$('#search2').hide();
		
		//$('#search7').hide();
		//alert(report_ids);
		var report_id=report_ids.split(",");
		for (var k=0; k<report_id.length; k++)
		{
			if(report_id[k]==282)
			{
				$('#search1').show();
			}
			if(report_id[k]==283)
			{
				$('#search2').show();
			}
			
		
		}
	}

</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
    <form name="shipmentschedule_1" id="shipmentschedule_1" autocomplete="off" >
        <h3 style="width:1640px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1600px;">
            <table class="rpt_table" width="1600" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="130">Company</th>
                        <th width="120">Buyer</th>
                        <th width="70">Brand</th>
						<th width="70">Season</th>
                        <th width="50">Season Year</th>
                        <th width="100">Team</th>
						<th width="100">Team Leader</th>
                        <th width="100">Team Member</th>
                        <th width="100">Sustaina. Standard</th>
                        <th width="100">Fab. Material</th>
                        <th width="70">Search By</th>
                        <th width="80" id="search_by_td_up">Order No</th>
                        <th width="70">File No</th>
                        <th width="70">M. Style/ Int. Ref.</th>
                        <th width="80">Shipment Status</th>
                        <th width="70">Order Status</th>
                        <th width="120" colspan="2">Date</th>
                        <th width="80">Date Category</th>
                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" />
                        	<input type="hidden" id="report_ids">
                        </th>
                    </tr>
                </thead>
                <tr class="general">
                    <td><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, " load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_buyer', 'buyer_td' ),print_button_setting();" ); ?></td>
                    <td id="buyer_td"><?=create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-Select Buyer-", $selected, " load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_season', 'season_td');" ); ?></td>
                    <td id="brand_td"><?=create_drop_down( "cbo_brand_name", 70, $blank_array,"", 1,"--Select--", $selected, "",0,"" ); ?></td>
                    <td id="season_td"><?=create_drop_down( "cbo_season", 70, "select id, season_name from lib_buyer_season where  status_active =1 and is_deleted=0  order by season_name ASC","id,season_name", 1, "-Season-", "", "" ); ?></td>
                    <td><?=create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-All-", $selected, "",0,"" ); ?></td>
                    <td><? echo create_drop_down( "cbo_team_name", 100, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-Team Name-", $selected, " load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_team_leader', 'team_lead_td' );load_drop_down( 'requires/shipment_schedule_controller_v3', this.value, 'load_drop_down_team_member', 'team_td' )" ); ?></td>
					<td id="team_lead_td"><? echo create_drop_down( "cbo_team_leader", 100, $blank_array,"", 1, "-Team Leader-", $selected, "" ); ?></td>
                    <td id="team_td"><? echo create_drop_down( "cbo_team_member", 100, $blank_array,"", 1, "-Team Member-", $selected, "" ); ?></td>

                    <td><? echo create_drop_down( "cbo_sustainability_standard", 100, $sustainability_standard,"", 1, "-- Select--", 0, "","","" ); ?></td>
                    <td><? $fab_material=array(1=>"Organic",2=>"BCI");
                    		echo create_drop_down( "cbo_fab_material", 100, $fab_material,"", 1, "-- Select--", 0, "","","" ); ?></td>

                    <td align="center">
						<?
							$search_by_arr = array(1=>"Order Wise",2=>"Style Wise",3=>"Job Wise");
							echo create_drop_down( "cbo_search_by", 70, $search_by_arr,"",0, "", "",'search_by(this.value)',0 );
                        ?>
                    </td>
                    <td id="search_by_td"><input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:70px" placeholder="Write" /></td>
					<td><input type="text" name="txt_file" id="txt_file" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                    <td><input type="text" name="txt_ref" id="txt_ref" class="text_boxes" style="width:60px" placeholder="Write" /></td>
                    <td><?=create_drop_down( "cbo_shipment_status", 80, $shipment_status,"", 0,"-- Select --", $selected, "",0,"" ); ?></td>
                    <td><?=create_drop_down( "cbo_order_status", 70, $order_status,"", 1,"-- All --", $selected, "",0,"" ); ?>
							<input name="txt_order_status" id="txt_order_status" type="hidden" value=""  style="width:45px " readonly/>
                    </td>
                    <td><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:55px" placeholder="From"></td>
                    <td><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:55px" placeholder="To"></td>
                    <td>
                        <select name="cbo_category_by" id="cbo_category_by"  style="width:80px" class="combo_boxes">
                            <option value="1">Ship Date</option>
                            <option value="2">PO Rec. Date</option>
							<option value="3">PO Insert Date</option>
							<option value="4">PHD Insert Date</option>
							<option value="5">Fac. Receive Date</option>
							<option value="6">Original Ship Date</option>
                        </select>
                    </td>
                    <td><input type="button" name="search1" id="search1" value="Details 3" onClick="generate_report_main(7);" style="width:60px; " class="formbutton" />
					<input type="button" name="search2" id="search2" value="Details 4" onClick="generate_report_main(8);" style="width:60px; " class="formbutton" />
				
				</td>
                </tr>
                <tr>
                    <td colspan="12" align="center">
						<?=load_month_buttons(1); ?>
                    </td>
                    
                </tr>
            </table>
        </fieldset>
        </div>
        </form>
           <div id="report_container" align="center"></div>
           <div id="report_container2">
       </div>
    </div>
</body>
<script>
	set_multiselect('cbo_shipment_status','0','0','','0');
</script> 
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>