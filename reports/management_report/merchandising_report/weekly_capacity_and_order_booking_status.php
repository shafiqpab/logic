<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Gmts Shipment Schedule Report
				
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	11/01/2013
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
echo load_html_head_contents("Order Info","../../../", 1, 1, $unicode);
?>	

<script>
var permission='<? echo $permission; ?>';

var tableFilters = 
	{
		col_operation: 
		{
			id: ["value_smv_tot","total_order_qnty","total_order_qnty_pcs","value_yarn_req_tot","value_total_order_value","value_total_commission","value_total_net_order_value","total_sewing_ouput","total_ex_factory_qnty","total_short_access_qnty","total_over_access_qnty","value_total_short_access_value","value_total_over_access_value"],
			col: [16,17,19,20,22,23,24,25,26,27,28,29,30],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_28: "select",
		col_32: "select",
	}
	
	var tableFiltersCountry = 
	{
		col_operation: 
		{
			id: ["total_order_qnty","total_ord_qnty_pcs","value_total_order_value","total_fab_req_qty","total_sewing_ouput","total_ex_factory_qnty","total_ex_factory_qnty_bal","value_total_ex_factory_value"],
			col: [14,16,17,18,19,20,21,22],
			operation: ["sum","sum","sum","sum","sum","sum","sum","sum"],
			write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
		} , 
		col_28: "select",
		col_32: "select",
	}	
function generate_report_main(e)
	{
			if (e!=13) {var unicode=e.keyCode? e.keyCode : e.charCode } else {unicode=13;}
			var inn=document.getElementById('fillter_check').value;
			if(inn=='')
			{
				generate_report('report_container2',1)
			}
			if(inn==1)
			{
				show_inner_filter(unicode);
			}
	}
		
function generate_report(type,week_pad){
	document.getElementById('report_container2').innerHTML="";
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	var cbo_year_selection=document.getElementById('cbo_year_selection').value;

	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var cbo_order_status=2;
	var cbo_team_name=document.getElementById('cbo_team_name').value;
	var cbo_team_member=document.getElementById('cbo_team_member').value;
	var cbo_category_by=document.getElementById('cbo_category_by').value;
	var cbo_order_status=document.getElementById('cbo_order_status').value;
	var cbo_product_category=document.getElementById('cbo_product_category').value;	
	var cbo_week=document.getElementById('cbo_week').value;	
	var cbo_season_id=document.getElementById('cbo_season_id').value;	
	
	var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"_"+cbo_order_status+"_"+cbo_year_selection+"_"+cbo_product_category+"_"+cbo_week+"_"+cbo_season_id;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	freeze_window(3);
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			var response=(xmlhttp.responseText).split('####');	
			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table-body",-1,tableFilters);
			release_freezing();
		}
	}
	xmlhttp.open("GET","requires/weekly_capacity_and_order_booking_status_controller.php?data="+data+"&type="+type+"&week_pad="+week_pad,true);
	xmlhttp.send();
}
	
	function generate_report1()
	{
			var stype=1;
			var myColValues=TF_GetColValues("table-body",28);
			myColValues="'"+myColValues.join()+"'";
			var txt_date_from=document.getElementById('txt_date_from').value;
			var txt_date_to=document.getElementById('txt_date_to').value;
			if (stype==1) // main call
	        {
				document.getElementById('report_container2').innerHTML="";
				var cbo_company_name=document.getElementById('cbo_company_name').value;
				var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
				var cbo_order_status=2;
				var cbo_team_name=document.getElementById('cbo_team_name').value;
				var cbo_team_member=document.getElementById('cbo_team_member').value;
				var cbo_category_by=document.getElementById('cbo_category_by').value;
				var cbo_year_selection=document.getElementById('cbo_year_selection').value;
				
				var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+'_'+myColValues+'_'+cbo_year_selection;
				//alert(data);
			}
			
			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange=function()
			{
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			var response=(xmlhttp.responseText).split('####');	
			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 

			append_report_checkbox('table_header_1',1);
				/*var tableFilters = {
					col_operation: {
						 id: ["total_order_qnty_pcs","total_order_qnty","value_total_order_value","total_ex_factory_qnty","total_short_access_qnty","value_total_short_access_value","value_yarn_req_tot"],
								   col: [14,15,18,20,21,22,23],
								   operation: ["sum","sum","sum","sum","sum","sum","sum"],
								   write_method: ["innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML","innerHTML"]
					}, 
					ex_function:{
						fn_name:generate_report1
					}
				}*/
				setFilterGrid("table-body",-1,tableFilters);
				document.getElementById('content_summary3_panel').innerHTML=document.getElementById('shipment_performance').innerHTML
							//percent_set()

			}
			}
			xmlhttp.open("GET","requires/weekly_capacity_and_order_booking_status_controller.php?data="+data+"&type=report_generate",true);
			xmlhttp.send();
	}
	
function percent_set()
{
	//alert("monzu");
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
	//alert("monzu");
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../../')
	emailwindow.onclose=function()
	{
	}
}

function print_report_part_by_part(id,button_id)
{
	//javascript:window.print()
		//$('#data_panel').html( http.responseText );
		 $(button_id).removeAttr("onClick").attr("onClick","javascript:window.print()");
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
'<html><head><title></title></head><body>'+document.getElementById(id).innerHTML+'</body</html>');
		
		d.close();
		 $(button_id).removeAttr("onClick").attr("onClick","print_report_part_by_part("+id,button_id+")");
	
}
	
	function generate_ex_factory_popup(action,job_no,id,width)
	{
		//alert(job_no); 
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/weekly_capacity_and_order_booking_status_controller.php?action='+action+'&job_no='+job_no+'&id='+id, 'Ex-Factory Details', 'width='+width+',height=400px,center=1,resize=0,scrolling=0','../../');
	}
	
	function set_week_date(){
		var week=document.getElementById('cbo_week').value*1;
		var year=document.getElementById('cbo_year_selection').value;
	
		if(week){
			
			$('.month_button').attr('disabled','true');
			$('.month_button_selected').attr('disabled','true');
			$('#txt_date_from').attr('disabled','true');
			$('#txt_date_to').attr('disabled','true');
			var week_date=return_global_ajax_value(week+"_"+year, 'week_date', '', 'requires/weekly_capacity_and_order_booking_status_controller');
			var week_date_arr=week_date.split('_');
			document.getElementById('txt_date_from').value=week_date_arr[0];
			document.getElementById('txt_date_to').value=week_date_arr[1];
		}else{
			$('.month_button').removeAttr('disabled');
			$('.month_button_selected').removeAttr('disabled');
			$('#txt_date_from').removeAttr('disabled');
			$('#txt_date_to').removeAttr('disabled');
		}
	}
	
function generate_report_country_wise(type,week_pad){
	document.getElementById('report_container2').innerHTML="";
	var txt_date_from=document.getElementById('txt_date_from').value;
	var txt_date_to=document.getElementById('txt_date_to').value;
	var cbo_year_selection=document.getElementById('cbo_year_selection').value;

	var cbo_company_name=document.getElementById('cbo_company_name').value;
	var cbo_buyer_name=document.getElementById('cbo_buyer_name').value;
	var cbo_order_status=2;
	var cbo_team_name=document.getElementById('cbo_team_name').value;
	var cbo_team_member=document.getElementById('cbo_team_member').value;
	var cbo_category_by=document.getElementById('cbo_category_by').value;
	var cbo_order_status=document.getElementById('cbo_order_status').value;
	var cbo_product_category=document.getElementById('cbo_product_category').value;	
	var cbo_week=document.getElementById('cbo_week').value;	
	var cbo_season_id=document.getElementById('cbo_season_id').value;	
	
	var data=cbo_company_name+"_"+cbo_buyer_name+"_"+txt_date_from+"_"+txt_date_to+"_"+cbo_order_status+"_"+cbo_team_name+"_"+cbo_team_member+"_"+cbo_category_by+"_"+cbo_order_status+"_"+cbo_year_selection+"_"+cbo_product_category+"_"+cbo_week+"_"+cbo_season_id;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	freeze_window(3);
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			var response=(xmlhttp.responseText).split('####');	
			document.getElementById('report_container2').innerHTML=response[0];
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
			append_report_checkbox('table_header_1',1);
			setFilterGrid("table-body",-1,tableFiltersCountry);
			release_freezing();
		}
	}
	xmlhttp.open("GET","requires/weekly_capacity_and_order_booking_status_controller.php?data="+data+"&type="+type+"&week_pad="+week_pad,true);
	xmlhttp.send();
}

 function print_report_button_setting(report_ids) 
    {
     
        $('#search1').hide();
        $('#search2').hide();
        $('#search3').hide();
        var report_id=report_ids.split(",");
        report_id.forEach(function(items){
            if(items==108){$('#search1').show();}
            else if(items==255){$('#search2').show();}
            else if(items==254){$('#search3').show();}
            });
    }

</script>
</head>
<body onLoad="set_hotkey()">
    <div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../../../");  ?>
   <h3 style="width:1550px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu(this.id,'content_search_panel','')"> -Search Panel</h3>
       <div id="content_search_panel"> 
       
            <form>
                <fieldset style="width:98%;">
                    <div  style="width:100%" align="center">
                            <table class="rpt_table" width="1550" cellpadding="0" cellspacing="0" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Buyer</th>
                                        <th>Season</th>
                                        <th>Team</th>
                                        <th>Dealing Merchant</th>
                                         <th>Product Category</th>
                                         <th>Week</th>
                                        <th colspan="2">Date</th>
                                        <th>Date Category</th>
                                        <th>Order Status</th>
                                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" /></th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                           <?
                                           echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $selected, " load_drop_down( '../merchandising_report/requires/weekly_capacity_and_order_booking_status_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/weekly_capacity_and_order_booking_status_controller' );" );
                                            ?> 
                                    </td>
                                    <td id="buyer_td" align="center">
                                     <? 
                                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
                                     ?>	
                                    </td>
                                    <td id="season_td" align="center">
                                     <? 
                                        echo create_drop_down( "cbo_season_id", 120, $blank_array,"", 1, "-- All --", $selected, "" );
                                     ?>	
                                    </td>
                                    <td align="center">                
                                    
                                    <?
                                           echo create_drop_down( "cbo_team_name", 130, "select id,team_name from lib_marketing_team  where status_active =1 and is_deleted=0  order by team_name","id,team_name", 1, "-- Select Team --", $selected, " load_drop_down( '../merchandising_report/requires/weekly_capacity_and_order_booking_status_controller', this.value, 'load_drop_down_team_member', 'team_td' );" );
                                            ?>
                                    </td>
                                    <td id="team_td" align="center">
                                    <? 
                                        echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "- Select Dealing Merchant- ", $selected, "" );
                                     ?>	
                                    </td>
                                 
                                     <td align="center"> 
                                        <? 
                                            echo create_drop_down( "cbo_product_category", 100, $product_category,"", 1, "-- Select --", $selected, ""  );
                                        ?>	
                                    </td>
                                       <td> 
                                    <?
									$weekArr=array();
									$sql=sql_select("select id,week from week_of_year  where year=".date("Y"));
									foreach($sql as $row){
										$weekArr[$row[csf('week')]]="Week-".$row[csf('week')];
									}
                                            echo create_drop_down( "cbo_week", 80, $weekArr,"", 1, "-- Select --", $selected, "set_week_date()"  );
								    ?>
                                    </td>
                                    <td align="center"><input name="txt_date_from" id="txt_date_from"  class="datepicker" style="width:75px">
                                    </td>
                                    <td align="center"><input name="txt_date_to" id="txt_date_to"  class="datepicker" style="width:75px">
                                    </td>
                                    <td>
                                    <select name="cbo_category_by" id="cbo_category_by"  style="width:130px" class="combo_boxes">
                                    <option value="3">Country Ship Date Wise </option>
                                    <option value="1">Ship Date Wise </option>
                                    <option value="2">PO Rec. Date Wise </option>
                                    
                                    </select>
                                    </td>
                                    <td>
									<? 
                                    	echo create_drop_down( "cbo_order_status", 100, $order_status,"", 1, "ALL", 1, "" );
                                    ?>	
                                    </td>
                                    <td>
                                    <input type="button" name="search" id="search1" value="Show" onClick="generate_report('report_generate',0)" style="width:80px;display: none;" class="formbutton" />
                                    <input type="button" name="search" id="search2" value="Show-2" onClick="generate_report('report_generate',1)" style="width:80px;display: none;" class="formbutton" />
                                    <input name="fillter_check" id="fillter_check" type="hidden" >
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" align="center">
                                        <? echo load_month_buttons(1); ?>
                                    </td>
                                    <td align="center">
                                        <input type="button" name="search" id="search3" value="Show-Country" onClick="generate_report_country_wise('report_generate_country_wise',0)" style="width:150px;display: none;" class="formbutton" />
                                    </td>
                                </tr>
                            </table>
                    </div>
                </fieldset>
            </form>
        </div>
       <div id="report_container" align="center"></div>
       <div id="report_container2"> 
       
        </div>
    </div>
    
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>