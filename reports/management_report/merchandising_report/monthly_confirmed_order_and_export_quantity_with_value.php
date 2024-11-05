<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Monthly Confirmed Order and Export Quantity with Value.
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	27-05-2021
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
echo load_html_head_contents("Monthly Confirmed Order and Export Quantity with Value","../../../", 1, 1, $unicode,1,1);
?>
 <script src="../../../Chart.js-master/Chart.min.js"></script>
 <style>
 @media print {
  body {
    visibility: hidden;
  }
  #report_container2 {
    visibility: visible;
	position: absolute;
    left: 0;
    top: 0;
  }
}
 </style>
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";

	var tableFilters = 
	{
		col_10: "none",
		col_operation:
		{
			id: ["smv_tot","order_pcs_tot","break_down_pcs_tot","order_value_tot","net_order_value_tot","ex_factory_qnty_tot","ex_factory_value_tot","net_ex_factory_value_tot","total_short_access_qnty","total_over_access_qnty","total_short_access_value","total_net_short_access_value","total_over_access_value","total_net_over_access_value","total_ex_factory_brk_dwn_qty"],
			// col: [18,21,22,24,25,26,27,28,29,30,31,32,33,34,39],
			col: [22,25,26,28,29,30,31,32,33,34,35,36,37,38,43],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}
	var tableFilters2 = 
	{
		col_10: "none",
		col_operation: 
		{
			id: ["smv_tot","order_pcs_tot","break_down_pcs_tot","order_value_tot","net_order_value_tot","ex_factory_qnty_tot","ex_factory_value_tot","net_ex_factory_value_tot","total_short_access_qnty","total_short_access_value","total_net_short_access_value","total_ex_factory_brk_dwn_qty"],
			// col: [18,21,22,24,25,26,27,28,29,30,31,36],
			col: [22,25,26,28,29,30,31,32,33,34,35,40],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	var tableFilters3 = 
	{
		col_10: "none",
		col_operation:
		{
			id: ["smv_tot","order_pcs_tot","break_down_pcs_tot","order_value_tot","net_order_value_tot","ex_factory_qnty_tot","ex_factory_value_tot","net_ex_factory_value_tot","total_short_access_qnty","total_over_access_qnty","total_short_access_value","total_net_short_access_value","total_over_access_value","total_net_over_access_value","total_ex_factory_brk_dwn_qty"],
			// col: [19,22,23,25,26,27,28,29,30,31,32,33,34,39,40],
			col: [23,26,27,29,30,31,32,33,34,35,36,37,38,43,44],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		}
	}

	function fn_report_generated(type)
	{
		freeze_window(3);
        // if( form_validation('cbo_company_name*cbo_date_type*txt_date_from*txt_date_to','Company Name*Date Type*Date Range')==false )
		// {
        //     release_freezing();
		// 	return;
		// }

		if (form_validation('cbo_company_name','Comapny Name')==false)
		{
			release_freezing();
			return;
		}
		
		var job_no 	= $('#txt_job_no').val();
		var style 	= $('#txt_style_ref').val();
		
		if (job_no == "" &&  style == "")
        {
            if (form_validation('txt_date_from*txt_date_to', 'Date Form*Date To') == false)
            {
				release_freezing();
                return;
            }
        }
	
        var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_job_no*txt_style_ref*cbo_team_leader*cbo_product_department*cbo_date_type*txt_date_from*txt_date_to',"../../../");
        // var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_name*cbo_start_year*cbo_start_month*cbo_end_year*cbo_end_month',"../../../");
        // alert(data);return;
        // freeze_window(3);
        http.open("POST","requires/monthly_confirmed_order_and_export_quantity_with_value_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fn_report_generated_reponse;

	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			var reponse=trim(http.responseText).split("####");
			//alert(reponse);
			$('#report_container2').html(reponse[0]);

			if(reponse[2]==1)
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="window.print();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				
				var levelm= new Array();
				var levelc= new Array();
				var levelp= new Array();
				var levele= new Array();
				var levels= new Array();
				// alert(reponse[3]);
				var objm=JSON.parse(reponse[3]);
				var objc=JSON.parse(reponse[4]);
				var objp=JSON.parse(reponse[5]);
				var obje=JSON.parse(reponse[6]);
				var objs=JSON.parse(reponse[7]);

				for(i in objm){
					levelm.push(objm[i])
					levelc.push(objc[i])
					levelp.push(objp[i])
					levele.push(obje[i])
					levels.push(objs[i])
				}
				// alert(objo);
				var line_bar_data = {
					type: 'bar',
					data:{
						labels : levelm,
						datasets : [
							{
								label: 'PROJECTED ORDER QTY(PCS)',
								backgroundColor: '#ffb6c1',
								data: levelp,
								fill: false
							},
							{
								label: 'CONFIRMED ORDER QTY(PCS)',
								backgroundColor: '#2E75B6',
								data: levelc,
								fill: false
							},
							{
								label: "EXPORT QTY(PCS)",
								backgroundColor: "#9DDE58",
								data: levele,
								fill: false
							},
							{
								label: "SHORT EXPORT QTY(PCS)",
								backgroundColor: "#ff0000",
								data: levels,
								fill: false
							}
						]
					}
				}
				new Chart(document.getElementById("canvas").getContext("2d"),line_bar_data);
			}
			else
			{
				document.getElementById('report_container').innerHTML='<a href="requires/'+reponse[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window();" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
				if(reponse[2]==2){setFilterGrid("export_details_tbl",-1,tableFilters);}
				else if(reponse[2]==3){setFilterGrid("export_details_tbl",-1,tableFilters2);}
				else if(reponse[2]==4){setFilterGrid("export_details_tbl",-1,tableFilters3);}
				// document.getElementById('export_details_tbl').rows[0].style.position='sticky';
				// document.getElementById('export_details_tbl').rows[0].style.top='0';
			}
			show_msg('3');
		}
	}

    function new_window()
	{
		$('.fltrow').hide();
        var w = window.open("Surprise", "#");
        var d = w.document.open();
        d.write ('<!DOCTYPE HTML>'+
        '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'
        +document.getElementById('report_container2').innerHTML+'</body</html>');
        d.close();
		$('.fltrow').show();
		// window.print();
        
	}

</script>
</head>
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_confirmed_order_and_export_quantity_with_value" name="monthly_confirmed_order_and_export_quantity_with_value">
    <div style="width:1255px;" align="center">
        <? echo load_freeze_divs ("../../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1210px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
            <fieldset style="width:1210px;">
                <table class="rpt_table" width="1210" border="1" align="center" rules="all">
                	<thead>
                    	<tr>
                            <th width="120">Company Name</th>
                            <th width="100">Buyer Name</th>
                            <th width="100">Job No</th>
                            <th width="100">Style Ref.</th>
                            <th width="100">Team</th>
                            <th width="100">Prod Dept</th>
                            <th width="100">Date Type</th>
                            <th width="200" colspan="2" >Date Range</th>
							<th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthly_confirmed_order_and_export_quantity_with_value','report_container*report_container2','','','')" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general" >
							<td>
                                <? 
                                    echo create_drop_down( "cbo_company_name", 120, "SELECT id,company_name from lib_company comp where status_active=1 and is_deleted=0  $company_cond order by company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/monthly_confirmed_order_and_export_quantity_with_value_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                                ?>                            
                           </td>

                            <td id="buyer_td">
								<?=create_drop_down( "cbo_buyer_name", 120, $blank_array ,"", 1, "-- All Buyer --", $selected,'' ); ?>
							</td>
							<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" placeholder="Write" ></td>
							<td><input type="text" name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:100px" placeholder="Write" ></td>
							<td >
								<?=create_drop_down( "cbo_team_leader", 100, "SELECT id,team_leader_name from lib_marketing_team where project_type=1 and team_type in (0,1,2) and status_active =1 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "" ); ?>
							</td>
							<td>
								<?=create_drop_down( "cbo_product_department", 90, $product_dept, "", 1, "-Select-", $selected, "" ); ?> 
							</td>
							<td><?
								$date_type=array(1=>"Original Ship Date",2=>"Publish Ship Date",3=>"Country Ship Date");							
								echo create_drop_down( "cbo_date_type", 90, $date_type, "", 1, "--Select--", "1", "" ); 
							// echo create_drop_down( "cbo_ready_to_approved", 150, $yes_no,"", 1, "-- Select--", 2, "","","" );
								?> 
							</td>
							<td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px"  placeholder="To Date" ></td>
                            <td>
								<input type="button" id="show_button1" class="formbutton" style="width:60px;" value="Show" onClick="fn_report_generated(1);" />
								<input type="button" id="show_button2" class="formbutton" style="width:60px;" value="Details" onClick="fn_report_generated(2);" />
							</td>
                        </tr>
						<tr>
							<td colspan="7" align="center"><? echo load_month_buttons(1); ?></td>
							<td colspan="3" align="center">
								<input type="button" id="show_button3" class="formbutton" style="width:60px;" value="Short" onClick="fn_report_generated(3);" />
								<input type="button" id="show_button4" class="formbutton" style="width:100px;" value="Carried From Job" onClick="fn_report_generated(4);" />
								<input type="button" id="show_button5" class="formbutton" style="width:100px; " value="Excluding Act. PO" onClick="fn_report_generated(5);" />
								<input type="button" id="show_button6" class="formbutton" style="width:100px;" value="Including Act. PO" onClick="fn_report_generated(6);" />
							</td>
						</tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
     </form>

    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
    <div style="display:none" id="data_panel"></div>
 </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
<script>set_multiselect('cbo_company_name','0','0','','0',"load_drop_down( 'requires/monthly_confirmed_order_and_export_quantity_with_value_controller',$('#cbo_company_name').val(), 'load_drop_down_buyer', 'buyer_td' );get_php_form_data($('#cbo_company_name').val(), 'load_drop_down_mul_com_cond', 'requires/monthly_confirmed_order_and_export_quantity_with_value_controller');");</script>
<style>
/* #canvas{width:80% !important;height:500px !important;} */
#canvas{height:400px !important;}
</style>
</html>
