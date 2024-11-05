<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Monthly Item Wise Total Shipment Schedule Report	
Functionality	:	
JS Functions	:
Created by		:	Md. Sakibul Islam
Creation date 	: 	31-01-2024
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


echo load_html_head_contents("Monthly Item Wise Total Shipment Schedule Report","../../../", 1, 1, $unicode,1,'');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	 
	function fn_report_generated()
	{
		if(form_validation('cbo_company_id*cbo_date_cat_id*txt_date_from*txt_date_to','Company Name*Date Category*Date From*Date to')==false)
		{
			return;
		}
		else
		{

			var report_title=$( "div.form_caption" ).html();
			var data="action=item_report_generate"+get_submitted_data_string('cbo_company_id*cbo_garments_item*cbo_date_cat_id*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
			
			freeze_window(3);
			http.open("POST","requires/monthly_item_wise_total_shipment_schedule_report_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{	release_freezing();
			var reponse=trim(http.responseText).split("****");
			//alert(reponse);
			var totRow=reponse[2];
			$('#report_container').html( '<br><b>Convert To </b><a href="requires/' + reponse[1] + '" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>' );
			$('#report_container').append( '&nbsp;&nbsp;&nbsp;<input type="button" onclick="new_window();" value="HTML Preview" name="Print" class="formbutton" style="width:100px"/>' );
			$('#report_container2').html(reponse[0]);
			
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
	'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /></head><body>'+document.getElementById('report_container2').innerHTML+'</body</html>');
		d.close();

		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="400px";

		$("#table_body tr:first").show();
	}

</script>
</head>
<body onLoad="set_hotkey()">
   <div align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <form id="monthlyItemWiseTotalShipment_1" name="monthlyItemWiseTotalShipment_1">
            <h3 align="center" id="accordion_h1" class="accordion_h" style="width:820px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" style="width:820px" align="center" >
                <fieldset style="width:800px;">  
                    <table cellpadding="0" cellspacing="2" width="800" class="rpt_table" border="1" rules="all">
                        <thead>  
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Garments Item</th>
								<th class="must_entry_caption">Date Category</th>
                                <th class="must_entry_caption">Date Range</th>
                                <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthlyItemWiseTotalShipment_1','report_container*report_container2','','','');" /></th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_id", 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name",1, "--Select Company--", $selected,"" ); ?></td>
                                <td>
									<? echo create_drop_down( "cbo_garments_item", 150,"select id, item_name from lib_garment_item where status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select Item--", $selected, "" ); ?>
                                </td>
								 <td id="">
									<? 
									$date_category_arr=array(0=>'Select Date Category',1=>'Public Ship Date',2=>'Country Ship Date',3=>'Original Ship Date'); //
									echo create_drop_down( "cbo_date_cat_id", 150, $date_category_arr,"", 0, "-- Select --", 0, "",0,"" ); ?>
                                </td>
								
                                <td>
                                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:75px" placeholder="From Date" >&nbsp; To
                                    <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px"  placeholder="To Date" >
                                </td>
								<td><input type="button" name="search" id="search" value="Show" onClick="fn_report_generated();" style="width:80px;" class="formbutton" /></td>
                            </tr>
                            <tr>
                            	<td colspan="6" align="center"></td>
                            </tr>
							<tr>
                    			<td colspan="9" align="left" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                    		</tr>
                         </tbody>
                    </table>
                </fieldset>
            </div>
        </form>    
    </div>
    <div id="report_container" align="center"></div>
    <div id="report_container2"></div>
</body>
<script>

//set_multiselect('cbo_company_id','0','0','','0',"get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/monthly_capacity_buyer_wise_booked_controller' );load_drop_down( 'requires/monthly_capacity_buyer_wise_booked_controller',$('#cbo_company_id').val(), 'load_drop_down_buyer', 'buyer_td' );set_multiselect('cbo_buyer_id','0','0','','0','');");

set_multiselect('cbo_garments_item','0','0','','0',"");
</script>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>