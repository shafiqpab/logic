<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Monthly Capacity Vs Buyer Wise Booked		
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	10-05-2017
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


echo load_html_head_contents("Monthly Capacity Vs Buyer Wise Booked","../../../", 1, 1, $unicode,1,'');
?>	
<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
	 
	function fn_report_generated(type)
	{
		if(form_validation('cbo_company_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end','Company Name*Start Year*Start Month*End Year*End Month')==false)
		{
			return;
		}
		else
		{	

			if ($('#conver_to_million').is(':checked')) { var conver_to_million=1000000;}
			else{ var conver_to_million=1;}
			
			var report_title=$( "div.form_caption" ).html();
			if(type==1)
			{
			var data="action=report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end*cbo_date_cat_id*cbo_buyer_id',"../../../")+'&report_title='+report_title+'&conver_to_million='+conver_to_million;
			
			
			//alert(data);
			
			}
			else if(type==2)
			{
			var data="action=item_report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end*cbo_date_cat_id*cbo_buyer_id',"../../../")+'&report_title='+report_title+'&conver_to_million='+conver_to_million;
			}
			else if(type==3)
			{
			var data="action=report_generate3"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end*cbo_date_cat_id*cbo_buyer_id',"../../../")+'&report_title='+report_title+'&conver_to_million='+conver_to_million;
			}
			else if(type==4 || type==7)// type 3 and 7
			{
				var data="action=buyer_wise_report_generate"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end*cbo_date_cat_id*cbo_buyer_id',"../../../")+'&report_title='+report_title+'&type='+type+'&conver_to_million='+conver_to_million;
			}
			else if(type==5)
			{
			var data="action=report_generate_buyer_wise"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end*cbo_date_cat_id*cbo_buyer_id',"../../../")+'&report_title='+report_title+'&conver_to_million='+conver_to_million;
			}
			else if(type==6)
			{
			var data="action=report_generate4"+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_year_start*cbo_month_start*cbo_year_end*cbo_month_end*cbo_date_cat_id*cbo_buyer_id',"../../../")+'&report_title='+report_title+'&conver_to_million='+conver_to_million;
			}
			else
			{
				/* There is no job*/
			}
			freeze_window(3);
			http.open("POST","requires/monthly_capacity_buyer_wise_booked_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fn_report_generated_reponse;
		}
	}
		
	function fn_report_generated_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split("####");
			var tot_rows=reponse[2];
			$('#report_container2').html(reponse[0]);
			document.getElementById('report_container').innerHTML=report_convert_button('../../../'); 
	 		show_msg('3');
			release_freezing();
		}
	}
	
	function fnc_details_popup(month,company_id,location_id,type,action)
	{
		var popup_width=0;
		if(type==0)
		{
			popup_width='400px';
		}
		else if(type==1)
		{
			popup_width='950px';
		}
		else if(type==2)
		{
			popup_width='950px';
		}
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'requires/monthly_capacity_buyer_wise_booked_controller.php?month='+month+'&company_id='+company_id+'&location_id='+location_id+'&type='+type+'&action='+action, 'Details Veiw', 'width='+popup_width+', height=400px,center=1,resize=0,scrolling=0','../../');
	}
	



	//for print button
	 
	function print_report_button_setting(report_ids) 
	{
		$('#search1').hide();
		$('#search2').hide();
		$('#search3').hide();
		$('#search4').hide();
		$('#search5').hide();
		$('#search7').hide();
		var report_id=report_ids.split(",");
		report_id.forEach(function(items){
			if(items==108){$('#search1').show();}
			else if(items==243){$('#search2').show();}
			else if(items==195){$('#search3').show();}
			else if(items==242){$('#search4').show();}
			else if(items==54){$('#search5').show();}
			else if(items==90){$('#search7').show();}
		});
	}

</script>
</head>
<body onLoad="set_hotkey()">
   <div align="center">
		<? echo load_freeze_divs ("../../../");  ?>
        <form id="monthlyCapacityBuyerWiseBooked_1" name="monthlyCapacityBuyerWiseBooked_1">
            <h3 align="center" id="accordion_h1" class="accordion_h" style="width:1020px;" onClick="accordion_menu( this.id,'content_search_panel', '')"> -Search Panel</h3>
            <div id="content_search_panel" style="width:1050px" align="center" >
                <fieldset style="width:980px;">  
                    <table cellpadding="0" cellspacing="2" width="980" class="rpt_table" border="1" rules="all">
                        <thead>  
                            <tr>
                                <th class="must_entry_caption">Company</th>
                                <th>Buyer</th>
                                <th>Location</th>
								<th>Date Category</th>
                                <th class="must_entry_caption" width="80">Start Year</th>
                                <th class="must_entry_caption" width="100">Start Month</th>
                                <th class="must_entry_caption" width="80">End Year</th>
                                <th class="must_entry_caption" width="100">End Month</th>
                                <th><input type="reset" name="reset" id="reset" value="Reset" class="formbutton" style="width:80px" onClick="reset_form('monthlyCapacityBuyerWiseBooked_1','report_container*report_container2','','','');" /></th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr class="general">
                                <td><? echo create_drop_down( "cbo_company_id", 150, "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name","id,company_name",1, "--Select Company--", $selected,"load_drop_down( 'requires/monthly_capacity_buyer_wise_booked_controller', this.value, 'load_drop_down_location', 'location_td' ); alert()" ); ?></td>
                                <td id="buyer_td">
									<? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,"", 1, "-- Select --", $selected, "","1","" ); ?>
                                </td>
                                <td id="location_td">
									<? echo create_drop_down( "cbo_location_id", 150, $blank_array,"", 1, "-- Select --", $selected, "","1","" ); ?>
                                </td>
								 <td id="">
									<? 
									$date_category_arr=array(1=>'Pub Ship Date',2=>'Country Ship Date',3=>'Actual Ship Date'); //
									echo create_drop_down( "cbo_date_cat_id", 100, $date_category_arr,"", 0, "-- Select --", 1, "",0,"" ); ?>
                                </td>
								
                                <td><? echo create_drop_down( "cbo_year_start", 80,$year,"", 1, "-Start Year-", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month_start", 100,$months,"", 1, "-Start Month-", "","" ); ?></td>
                                <td><? echo create_drop_down( "cbo_year_end", 100,$year,"", 1, "-End Year-", date('Y'),"" ); ?></td>
                                <td><? echo create_drop_down( "cbo_month_end", 100,$months,"", 1, "-End Month-", "","" ); ?></td>
								<td><input type="button" name="search" id="search6" value="Report" onClick="fn_report_generated(6);" style="width:80px;" class="formbutton" /></td>
                            </tr>
                            <tr>
                            	<td colspan="8" align="center">
                                	<input type="checkbox" name="conver_to_million" id="conver_to_million"> &nbsp; Millon
                                    <input type="button" name="search" id="search1" value="Show" onClick="fn_report_generated(1);" style="width:80px;display:none;" class="formbutton"  />
                                	 <input type="button" name="search" id="search2" value="Item Wise" onClick="fn_report_generated(2);" style="width:80px;display:none;" class="formbutton" />
                                    <input type="button" name="search" id="search3" value="Show2" onClick="fn_report_generated(3);" style="width:80px;display:none;" class="formbutton" />
                                    <input type="button" name="search" id="search4" value="Show 3" onClick="fn_report_generated(4);" style="width:80px;display:none;" class="formbutton" />
                                    <input type="button" name="search" id="search5" value="Buyer Wise" onClick="fn_report_generated(5);" style="width:80px;display:none;" class="formbutton" />
                                    <input type="button" name="search" id="search7" value="Quot. Rpt" onClick="fn_report_generated(7);" style="width:80px;display:none;" class="formbutton" />
                                </td>
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

set_multiselect('cbo_company_id','0','0','','0',"get_php_form_data($('#cbo_company_id').val(),'print_button_variable_setting','requires/monthly_capacity_buyer_wise_booked_controller' );load_drop_down( 'requires/monthly_capacity_buyer_wise_booked_controller',$('#cbo_company_id').val(), 'load_drop_down_buyer', 'buyer_td' );set_multiselect('cbo_buyer_id','0','0','','0','');");

set_multiselect('cbo_buyer_id','0','0','','0',"");
</script>

<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>