<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Order Non-Order Wise Booking Report.
Functionality	:
JS Functions	:
Created by		:	Zaman
Creation date 	: 	25-02-2021
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
echo load_html_head_contents("Order Non-Order Wise Booking", "../../", 1, 1,$unicode,1,1);
?>
<script>
	if( $('#index_page', window.parent.document).val()!=1)
		window.location.href = "../../logout.php";
	var permission = '<? echo $permission; ?>';

	/*var tableFilters =
	{
		col_40: "none",
		col_operation: {
		id: ["value_tot_wo_qnty","value_tot_wo_value","value_tot_precost_value","value_tot_deference","value_tot_deference_per","value_tot_ontime_receive_qnty","value_tot_receive_qnty","value_tot_receive_value","value_tot_rcv_balance"],
		col: [19,21,23,24,25,26,28,29,30],
		operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum"],
		write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}*/

	/*var tableFilters1 =
	{
		col_35: "none",
		col_5: "select",
		col_operation: {
		id: ["tot_fin_fab_qnty","tot_grey_fab_qnty"],
		col: [17,18],
		operation: ["sum","sum"],
		write_method: ["innerHTML","innerHTML"]
		}
	}*/

	//func_onchange_company
	function func_onchange_company()
	{
		load_drop_down('requires/yarn_fabric_purchase_requisition_report_controller', $('#cbo_company_id').val(),'load_drop_down_buyer','buyer_td' );
		//set_multiselect('cbo_buyer_id','0','0','','0','');
	}
	
	//func_onchange_buyer
	function func_onchange_buyer()
	{
		load_drop_down('requires/yarn_fabric_purchase_requisition_report_controller',$('#cbo_buyer_id').val(),'load_drop_down_sub_dep','sub_department_td' );
		set_multiselect('cbo_department_id','0','0','','0','');
	}
	
	//func_onDblClick_season
	function func_onDblClick_season()
	{
		if(form_validation('cbo_company_id*cbo_buyer_id','Company Name*Buyer Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyerID = $("#cbo_buyer_id").val();
		var page_link='requires/yarn_fabric_purchase_requisition_report_controller.php?action=action_season_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Season Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=350px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var season_id=this.contentDoc.getElementById("hdn_season_id").value;
			var season_name=this.contentDoc.getElementById("hdn_season_name").value;
			$('#txt_season').val(season_name);
			$('#hdn_season').val(season_id);
		}
	}

	//func_onDblClick_style
	function func_onDblClick_style()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyerID = $("#cbo_buyer_id").val();
		var page_link='requires/yarn_fabric_purchase_requisition_report_controller.php?action=action_style_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Style Ref. Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=630px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var style_ref=this.contentDoc.getElementById("hide_style_ref").value;
			var job_id=this.contentDoc.getElementById("hide_job_id").value;
			
			$('#txt_style').val(style_ref);
			$('#hdn_style').val(job_id);	 
		}
	}

	//func_onDblClick_job
	function func_onDblClick_job()
	{
		if( form_validation('cbo_company_id','Company Name')==false )
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyer_name = $("#cbo_buyer_id").val();
		var page_link='requires/yarn_fabric_purchase_requisition_report_controller.php?action=action_job_popup&companyID='+companyID+'&buyer_name='+buyer_name;
		var title='Job No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var job_no=this.contentDoc.getElementById("hdn_job_no").value;
			var job_id=this.contentDoc.getElementById("hdn_job_id").value;
			
			$('#txt_job').val(job_no);
			$('#hdn_job').val(job_id);	 
		}
	}
	
	//func_onDblClick_order
	function func_onDblClick_order()
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		
		var companyID = $("#cbo_company_id").val();
		var buyerID = $("#cbo_buyer_id").val();
		var page_link='requires/yarn_fabric_purchase_requisition_report_controller.php?action=action_order_popup&companyID='+companyID+'&buyerID='+buyerID;
		var title='Order No Search';
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=990px,height=390px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_no=this.contentDoc.getElementById("hide_order_no").value;
			var order_id=this.contentDoc.getElementById("hide_order_id").value;
			
			$('#txt_order').val(order_no);
			$('#hdn_order').val(order_id);
		}
	}

	//func_generate_report
	function func_generate_report(operation)
	{
		if(form_validation('cbo_company_id','Company Name')==false)
		{
			return;
		}
		
		if($('#txt_season').val() == '' && $('#txt_style').val() == '' && $('#txt_job').val() == '' && $('#txt_order').val() == '' )
		{
			if(form_validation('txt_date_from*txt_date_to','From Datet*To Date')==false)
			{
				return;
			}
		}

		var report_title=$( "div.form_caption" ).html();
		var data="action=action_generate_report"+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_department_id*txt_season*hdn_season*txt_style*hdn_style*txt_job*hdn_job*txt_order*hdn_order*txt_date_from*txt_date_to',"../../")+'&report_title='+report_title;
		freeze_window(3);
		http.open("POST","requires/yarn_fabric_purchase_requisition_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = func_generate_report_reponse;
	}

	//func_generate_report_reponse
	function func_generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split("####");
			//var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			//document.getElementById('report_container').innerHTML=report_convert_button('../../');
			document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
	 		show_msg('3');
			/*var cat_id=$("#cbo_category_id").val();
			setFilterGrid("table_body",-1);
			//,tableFilters
			if(cat_id==2)
			{
				setFilterGrid("table_body",-1,tableFilters);
			}
			else if(cat_id==4)
			{
				setFilterGrid("table_body",-1,tableFilters1);
			}*/
			release_freezing();
		}
	}
	
	function new_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		//$("#table_body tr:first").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title><link rel="stylesheet" type="text/css" href="../../css/style_common.css" media="all" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close(); 
		//$("#table_body tr:first").show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="350px";
	}
</script>
</head>
<body onLoad="set_hotkey();">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../",$permission); ?>
    <form name="wofbreport_1" id="wofbreport_1" autocomplete="off" >
    <h3 style="width:1230px; margin-top:20px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
        <div id="content_search_panel" >
            <fieldset style="width:1230px;">
            <table class="rpt_table" width="1230" cellpadding="0" cellspacing="0" border="1" rules="all" align="center">
                <thead>
                    <th width="130" class="must_entry_caption">Company</th>
                    <th width="130">Buyer</th>
                    <th width="130">Sub Department</th>
                    <th width="130">Season</th>
                    <th width="130">Style</th>
                    <th width="130">Job No</th>
                    <th width="130">Order No</th>
                    <th width="160">Date</th>
                    <th><input type="reset" id="reset_btn" class="formbutton" style="width:70px" value="Reset" onClick="reset_form('wofbreport_1','report_container*report_container2','','','')" /></th>
                </thead>
                 <tbody>
                    <tr class="general">
                        <td>
						<?
							echo create_drop_down( "cbo_company_id", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "func_onchange_company();" );
						?>
						</td>
                        <td id="buyer_td">
							<?
								echo create_drop_down( "cbo_buyer_id", 140, $blank_array,"", 1, "--Select Buyer--", $selected, "",'',"" );
                            ?>
                        </td>
                        <td id="sub_department_td">
                            <?
                            echo create_drop_down( "cbo_department_id", 140, "select id,sub_department_name from lib_pro_sub_deparatment where status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name","", 1, "-- Select --", $selected, "" );
                            ?>
                        </td>
                        <td>
                            <input type="text" name="txt_season" id="txt_season" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="func_onDblClick_season();" />
                            <input type="hidden" id="hdn_season" name="hdn_season" style="width:70px" />
                        </td>
                        <td>
                            <input type="text" name="txt_style" id="txt_style" class="text_boxes" style="width:130px" placeholder="Browse" onDblClick="func_onDblClick_style();" />
                            <input type="hidden" id="hdn_style" name="hdn_style" style="width:70px" />
                        </td>
                        <td>
                           <input type="text" style="width:130px" class="text_boxes" name="txt_job" id="txt_job" onDblClick="func_onDblClick_job()" placeholder="Browse" />
                            <input type="hidden" id="hdn_job" name="hdn_job" style="width:70px" />
                        </td>
                        <td>
                           <input type="text" style="width:130px" class="text_boxes" name="txt_order" id="txt_order" onDblClick="func_onDblClick_order()" placeholder="Browse" />
                            <input type="hidden" id="hdn_order" name="hdn_order" style="width:70px" />
                        </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" > To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date"  >
                        </td>
                        <td align="center">
                            <input type="button" id="show_button" class="formbutton" style="width:70px" value="Show" onClick="func_generate_report(0)" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center">
                            <? echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        	</fieldset>
        </div>
        <div id="report_container" align="center"></div>
        <div id="report_container2" align="left"></div>
    </form>
</div>
   <div style="display:none" id="data_panel"></div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	//set_multiselect('cbo_buyer_id','0','0','0','0');
	set_multiselect('cbo_department_id','0','0','0','0');
</script>
</html>