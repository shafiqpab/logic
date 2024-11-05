<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Air Way Bill Report
Functionality	:
JS Functions	:
Created by		:	Md Saidul Islam Reza
Creation date 	: 	29-09-2020
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
echo load_html_head_contents("Air Way Bill Report","../../", 1, 1, $unicode,1,1);
?>
<script>

 	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	var tableFilters =
	{
		col_operation: {
			id: ['totalWeight','totalChargeUsd','totalDFSChargeUsd','totalTotalChargeUsd','totalAmount'],
			col: [11,12,13,14,16],
			operation: ['sum','sum','sum','sum','sum'],
			write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
		}
	}

	
	function fn_report_generated(type)
	{
		freeze_window(3);
		if( trim($('#txt_air_way_bill').val())=="" )
		{
			if (form_validation('cbo_company_id*txt_date_from*txt_date_to','Company id*From Date*To Date')==false )
			{
				release_freezing();
				return;
			}
		}
		else
		{
			if (form_validation('cbo_company_id','Company id')==false )
			{
				release_freezing();
				return;
			}
		}
			
		var data="action=report_generate&reportType="+type+get_submitted_data_string('cbo_company_id*cbo_buyer_id*cbo_currier_name*cbo_team_leader*cbo_dealing_merchant*cbo_country_id*cbo_style_status*txt_air_way_bill*txt_date_from*txt_date_to',"../../");
		//alert(data);
		
		http.open("POST","requires/air_way_bill_report_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fn_report_generated_reponse;
	}

	function fn_report_generated_reponse()
	{
		if(http.readyState == 4)
		{
			release_freezing();
			var response=trim(http.responseText).split("####");
			//alert(response);
			$('#report_container2').html(response[0]);
			document.getElementById('report_container').innerHTML='<a href="requires/'+response[1]+'" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;&nbsp;<input type="button" onclick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>';
			setFilterGrid("table_body",-1,tableFilters);						
	 		show_msg('3');
		}
	}

	function new_window()
	{		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";

		$('#table_body tr:first').hide();

		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		$('#table_body tr:first').show();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="225px";
	}
	
</script>
</head>
<body onLoad="set_hotkey();">
 <div style="width:100%;" align="center">
<form id="monthly_ex_factory" name="monthly_ex_factory">
    <div style="width:1100px;" align="center">
        <? echo load_freeze_divs ("../../"); ?>
         <h3 align="left" id="accordion_h1" style="width:1100" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel">
            <fieldset style="width:1100px;">
                <table class="rpt_table" width="1100" border="1" align="center" rules="all">
                	<thead>
                    	<tr>
                            <th width="150" class="must_entry_caption">Company Name</th>
                            <th width="150">Buyer</th>
                            <th width="100">Currier Name</th>
                            <th width="100">Team Leader</th>
                            <th width="100">Dealing Merchant</th>
                            <th width="110">Destination</th>
                            <th width="80">Style Status</th>
                            <th width="100">Bill No</th>
                            <th width="130" colspan="2" class="must_entry_caption">Bill Date Range</th>
                            <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:70px" onClick="reset_form('','report_container*report_container2','','','');" /></th>
                        </tr>
                     </thead>
                    <tbody>
                        <tr class="general" >
                            <td><?=create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected,"load_drop_down( 'requires/air_way_bill_report_controller', this.value, 'load_buyer_dropdown', 'buyer_td' );" ); ?></td>
                            <td id="buyer_td"><?=create_drop_down( "cbo_buyer_id", 150, $blank_array,"", 1, "-- Select --", $selected, "" ); ?></td>
                            <td><?=create_drop_down( "cbo_currier_name",100,array(1=>"DHL",2=>"TNT",3=>"FedEx Express"),'',1,'-Select',$selected,"",0); ?></td>
                            <td id="dev_company_td"><?=create_drop_down( "cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0 order by team_leader_name","id,team_leader_name", 1, "-- Select Team --", $selected, "load_drop_down( 'requires/air_way_bill_report_controller', this.value, 'load_dealing_merchant_dropdown', 'marchant_td' );" );//project_type=1 and ?></td>
                            <td id="marchant_td"><?=create_drop_down( "cbo_dealing_merchant", 100, array(),"", 1, "-- Select --", 0, "" ); ?></td>
                            <td><?=create_drop_down( "cbo_country_id",110,"select ID,COUNTRY_NAME FROM LIB_COUNTRY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0","ID,COUNTRY_NAME",1,'-Select',0,"",0);?></td>
                            <td><?=create_drop_down( "cbo_style_status", 80, array(1=>"Before Order",2=>"After Order"),"", 1, "-- Select --", 0, "" ); ?></td>
                            <td><input style="width:90px " name="txt_air_way_bill" id="txt_air_way_bill" class="text_boxes" /></td>
                            <td><input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" ></td>
                            <td><input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"  placeholder="To Date" ></td>
                            <td><input type="button" id="show_button" class="formbutton" style="width:70px;" value="Show" onClick="fn_report_generated(1);" /></td>
                        </tr>
                        <tr>
                        	<td colspan="11" align="center"><?=load_month_buttons(1); ?></td>
                        </tr>
                    </tbody>
                </table>

            </fieldset>
        </div>
    </div>
     </form>
    <div id="report_container" align="center" style="padding:10px;"></div>
    <div id="report_container2"></div>
 </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
