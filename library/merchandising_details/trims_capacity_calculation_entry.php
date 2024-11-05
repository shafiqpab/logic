<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Trims Capacity Calculation Entry
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	30.04.2019
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
echo load_html_head_contents("Trims Capacity Calculation Entry", "../../", 1, 1,$unicode,'','');
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

	
	function change_color(month)
	{
		for( var i=1;i<=12;i++){
			$("#txt_month_capacity_tk_"+i).prop("readonly", true);
			$("#txt_conversion_rate_"+i).prop("readonly", true);
			$('#tr_'+i).css("background-color", "" ); 
		}
		
		$("#txt_month_capacity_tk_"+month).prop("readonly", false);
		$("#txt_conversion_rate_"+month).prop("readonly", false);
		$('#tr_'+month).css("background-color", "green" ); 
	}
	
	
	
	function daysInMonth(month) 
	{
		if( form_validation('cbo_company_id*cbo_year','Company Name*Year')==false )
		{
			return;
		}	
		
		var data=document.getElementById('cbo_year').value+"_"+month+"_"+document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_section').value+"_"+document.getElementById('cbo_sub_section').value;
		
		var list_view_capacity = return_global_ajax_value( data, 'load_month_in_days', '', 'requires/trims_capacity_calculation_entry_controller');
			
 		if(list_view_capacity!='')
		{
			change_color(month);
			
			$("#selected_month_id").val(month);
			
			$("#td_month_breakdown tr").remove();
			$("#td_month_breakdown").append(list_view_capacity);
			//open_close(month);
		}
	}
	
	
	
	function open_close(month)
	{
		
		if( form_validation('cbo_year*selected_month_id','Year*Month')==false )
		{
			return;
		}	
		else
		{
			
			if(document.getElementById('selected_month_id').value!=month){
				alert('Please Select month');
				$('#txt_capacity_tk_'+month).val('');
				$('#txt_conversion_rate_'+month).val('');
				return;
			}
			
			
			var tot_day_row=$('#td_month_breakdown tr').length;
			
			var open_rows=0;
			for(var i=1; i<= tot_day_row; i++)
			{
				if($('#cbo_day_status_'+i).val()==1) open_rows++;
			}
			
			
			
			var yearCapacityInTk=0;var yearCapacityInUsd=0;var year_working_day=0;
			for(var m=1; m <= 12; m++)
			{
				var monthCapacityTk=$('#txt_month_capacity_tk_'+m).val()*1;
				var conversionRate=$('#txt_conversion_rate_'+m).val()*1;
				var capacity_usd=monthCapacityTk/conversionRate;
				if(capacity_usd=='Infinity'){capacity_usd=0}
				
				if(conversionRate && monthCapacityTk){
					yearCapacityInTk+=monthCapacityTk;
					yearCapacityInUsd+=capacity_usd;
				}
				if(month==m){
					document.getElementById('workingDays_'+month).innerHTML=open_rows;
					document.getElementById('txt_month_capacity_usd_'+month).value=capacity_usd.toFixed(2);
				}
			
				year_working_day+=$('#workingDays_'+m).text()*1;
			
			
			}
			
			
			$('#txt_year_capacity_in_tk').val(yearCapacityInTk.toFixed(2));
			$('#txt_year_capacity_in_usd').val(yearCapacityInUsd.toFixed(2));
			
			document.getElementById('td_year_working_day').innerHTML=year_working_day;
			document.getElementById('td_year_capacity_tk').innerHTML=yearCapacityInTk.toFixed(2);
			document.getElementById('td_year_capacity_usd').innerHTML=yearCapacityInUsd.toFixed(2);
			
	
	
			var tot_day_capacity_tk=0;var tot_day_capacity_usd=0;
			for(var i=1; i<= tot_day_row; i++)
			{
				var monthCapacityTK=$('#txt_month_capacity_tk_'+month).val()*1;
				var monthConversionRate=$('#txt_conversion_rate_'+month).val()*1;
	
				if($('#cbo_day_status_'+i).val()==1){
					var calculative_day_cap_tk=monthCapacityTK/open_rows;
					var calculative_day_cap_usd=calculative_day_cap_tk/monthConversionRate;
					
					calculative_day_cap_tk=calculative_day_cap_tk.toFixed(2);
					calculative_day_cap_usd=calculative_day_cap_usd.toFixed(2);
					calculative_day_cap_tk=calculative_day_cap_tk*1;
					calculative_day_cap_usd=calculative_day_cap_usd*1;
					
					tot_day_capacity_tk+=calculative_day_cap_tk;
					tot_day_capacity_usd+=calculative_day_cap_usd;
					
					document.getElementById('txt_day_capacity_tk_'+i).value=calculative_day_cap_tk.toFixed(2);
					if(monthCapacityTK && monthConversionRate){
						document.getElementById('txt_day_capacity_usd_'+i).value=calculative_day_cap_usd.toFixed(2);
					}
					
					
					document.getElementById('days_tr_'+i).style.color = 'black';
				}
				else
				{
					$('#txt_day_capacity_tk_'+i).val('');
					$('#txt_day_capacity_usd_'+i).val('');
					document.getElementById('days_tr_'+i).style.color = 'red';
				}
			}
			
			document.getElementById('tot_day_capacity_tk').innerHTML=monthCapacityTK.toFixed(2);
			var monthCapacityUSD=document.getElementById('txt_month_capacity_usd_'+month).value*1;
			document.getElementById('tot_day_capacity_usd').innerHTML=monthCapacityUSD.toFixed(2);
			
			
			
			
			/*
			for(var i=tot_day_row; i > 1; i--)
			{
				if($('#dayStatus_'+i).val()==1)
				{
					var selected_month_id=document.getElementById('selected_month_id').value;
					var monthCapacityAmount=$('#monthCapacityAmount_'+selected_month_id).val()*1;
					var capacityUsd=$('#capacityUsd_'+selected_month_id).html()*1;
					
					var add_deduct_tk=monthCapacityAmount-tot_day_capacity_tk;
					var add_deduct_usd=capacityUsd-tot_day_capacity_usd;
					
					var dayCapacityTk=$('#dayCapacityTk_'+i).html()*1;
					var dayCapacityUsd=$('#dayCapacityUsd_'+i).html()*1;
					
					
					var tk=(dayCapacityTk+add_deduct_tk).toFixed(2);
					var usd=(dayCapacityUsd+add_deduct_usd).toFixed(2);
					$('#dayCapacityTk_'+i).html(tk);
					$('#dayCapacityUsd_'+i).html(usd);
					
					document.getElementById('tot_day_capacity_tk').innerHTML=monthCapacityAmount;
					document.getElementById('tot_day_capacity_usd').innerHTML=capacityUsd;
					i=0;
				}
				
				
			}*/
			
			
		
		}//else;

		
	}
	
	
	function check_unique_id()
	{
		fnc_reset_update_id();
		var data=document.getElementById('cbo_year').value+"_"+document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_section').value+"_"+document.getElementById('cbo_sub_section').value;
		if(document.getElementById('cbo_year').value>0 && document.getElementById('cbo_company_id').value>0 && document.getElementById('cbo_location_id').value>0 && document.getElementById('cbo_section').value>0){
			get_php_form_data( data, "load_php_dtls_form_update_val", "requires/trims_capacity_calculation_entry_controller" );
		}
	
	}
	
	
	
	function fnc_reset_update_id( )
	{
		document.getElementById('selected_month_id').value	= '';
		document.getElementById('update_id').value = '';
		document.getElementById('txt_year_capacity_in_tk').value = '';
		document.getElementById('txt_year_capacity_in_usd').value = '';
		set_button_status(0, permission, 'fnc_capacity_calculation',1);
		
		for(var m=1;m<=12; m++)
		{
			document.getElementById('update_id_dtls_'+m).value = '';
			document.getElementById('txt_month_capacity_tk_'+m).value = '';
			document.getElementById('txt_conversion_rate_'+m).value	= '';
			document.getElementById('txt_month_capacity_usd_'+m).value = '';
			document.getElementById('workingDays_'+m).value	= '';
		}

	}
	
	
	
	
	function fnc_capacity_calculation( operation )
	{
		if(operation==2){
			alert("Delete not allowed !");
			return;
		}
		
		if( form_validation('cbo_company_id*cbo_location_id*cbo_section*cbo_year*selected_month_id*txt_year_capacity_in_tk*txt_year_capacity_in_usd','Company Name*Location*Section*Year*Month*Capacity TK*Capacity USD')==false )
		{
			return;
		}
		
		
		
		var tot_day_row=$('#td_month_breakdown tr').length;
	
		var yearData="action=save_update_delete&operation="+operation+"&tot_day_row="+tot_day_row+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_section*cbo_sub_section*cbo_year*selected_month_id*txt_year_capacity_in_tk*txt_year_capacity_in_usd*update_id',"../../");
		
		var monthData="";
		for(i=1; i<=12; i++)
		{
			monthData+=get_submitted_data_string('update_id_dtls_'+i+'*txt_month_capacity_tk_'+i+'*txt_conversion_rate_'+i+'*txt_month_capacity_usd_'+i,"../../",i);
			monthData+='&workingDays_'+i+'='+$('#workingDays_'+i).text()*1;
		
		}
		
		var dayData="";
		for(i=1; i<=tot_day_row; i++)
		{
			dayData+=get_submitted_data_string('cbo_day_status_'+i+'*txt_day_capacity_tk_'+i+'*txt_day_capacity_usd_'+i,"../../",i);
			dayData+='&capacity_date_'+i+'='+$('#capacity_date_'+i).text();
		}
		
		var data=yearData+monthData+dayData;
		
		freeze_window(operation);
		http.open("POST","requires/trims_capacity_calculation_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_capacity_calculation_response;
	}
	function fnc_capacity_calculation_response()
	{
		if(http.readyState == 4) 
		{
			var response=trim(http.responseText).split('**');
			show_msg(response[0]);
			document.getElementById("update_id").value=response[1];
						
			set_button_status(1, permission, 'fnc_capacity_calculation',1);
			check_unique_id();
			
			
			release_freezing();
		}
	}








//==========================

	
		
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
   	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="capacitycalculation_1" id="capacitycalculation_1" method="" autocomplete="off">
    <fieldset style="width:900px ">
    <legend>Capacity Calculationy</legend>
        <table cellpadding="3" cellspacing="5">
            <tr>
                <td class="must_entry_caption" align="right">Company</td>
                <td>
                    <input type="hidden" id="update_id" name="update_id" value="" />
                    <?
                        echo create_drop_down( "cbo_company_id",160,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/trims_capacity_calculation_entry_controller', this.value, 'load_drop_down_location', 'location_td');check_unique_id();","","","","","",2);
                    ?>
                </td>
                <td class="must_entry_caption" align="right">Location </td>
                <td id="location_td">
                    <?
                        echo create_drop_down( "cbo_location_id",160,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
                    ?>
                </td>
                <td class="must_entry_caption" align="right">Section</td>
                <td>
                    <?
                        echo create_drop_down( "cbo_section",160,$trims_section,"", 1, "--Section--", $selected, "check_unique_id();","","","","","","");
                    ?>
                </td>
            </tr>
            <tr>
                <td align="right">Sub-section</td>
                <td>
                    <? 
                    echo create_drop_down( "cbo_sub_section", 160,$trims_sub_section,"", 1, "-- Select --", $selected,"check_unique_id();","","","","","" ); 
                    ?>
                </td>
                <td class="must_entry_caption" align="right">Capacity in Taka</td>
                <td>
                    <input type="text" name="txt_year_capacity_in_tk" id="txt_year_capacity_in_tk" class="text_boxes_numeric" style="width:150px" readonly disabled />
                </td>
                <td class="must_entry_caption" align="right">Capacity in USD</td>
                <td>
                    <input type="text" name="txt_year_capacity_in_usd" id="txt_year_capacity_in_usd" class="text_boxes_numeric" style="width:150px"  readonly disabled />
                </td>
                
            </tr>
            <tr>
                <td class="must_entry_caption" align="right">Year</td>
                <td>
                    <?
                        echo create_drop_down( "cbo_year", 160,$year,"", 1, "-- Select --", $selected,"daysInMonth($('#selected_month_id').val());check_unique_id();" );
                    ?>                              
                </td>
                <td><input type="hidden" id="selected_month_id"></td>
                
                
                
            </tr>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="center" colspan="9" valign="middle" class="button_container">
                    <?
                        echo load_submit_buttons( $permission, "fnc_capacity_calculation", 0,0 ,"reset_form('capacitycalculation_1','','','','')",1);
                    ?>
                </td>
            </tr>
        </table>
        </fieldset>
        <br>
       
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="top" width="500">
                
					<fieldset>
                    <table cellpadding="0" border="1" cellspacing="0" class="rpt_table" rules="all" >
                        <thead>
                            <th width="35">SL</th>
                            <th width="70">Month</th>
                            <th width="80">Working Day</th>
                            <th width="100">Capacity (TK)</th>
                            <th width="60">Conversion Rate</th>
                            <th>Capacity (USD)</th>
                        </thead>
                        <tbody id="td_month">
							<? 
							for( $m = 1; $m <= 12; $m++ ) 
							{ 
							?>
                            <tr id="tr_<? echo $m;?>">
                                <td align="center"><? echo $m;?></td>
                                <td><a href="javascript:daysInMonth(<? echo $m; ?>);"><? echo $months[$m]; ?></a></td>
                                <td align="center" id="workingDays_<? echo $m;?>"></td>
                                <td>
                                    
                                    <input type="hidden" id="update_id_dtls_<? echo $m; ?>" name="update_id_dtls_<? echo $m; ?>" value="" />
                                    
                                    <input type="text" id="txt_month_capacity_tk_<? echo $m; ?>" name="txt_capacity_tk_<? echo $m; ?>" class="text_boxes_numeric" style="width:100px" onKeyUp="open_close(<? echo $m; ?>)" readonly  />
                                </td>
                                <td>
                                    <input type="text" id="txt_conversion_rate_<? echo $m; ?>" name="txt_conversion_rate_<? echo $m; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="open_close(<? echo $m; ?>)" readonly  />
                                </td>
                                <td>
                                    <input type="text" id="txt_month_capacity_usd_<? echo $m; ?>" name="txt_capacity_usd_<? echo $m; ?>" class="text_boxes_numeric" style="width:100px" readonly  />
                                </td>
                            </tr>
                            <? } ?>
                        </tbody>
                        <tfoot>
                            <th colspan="2" align="right"><strong>Total : </strong></th>
                            <th align="center" id="td_year_working_day"></th>
                            <th id="td_year_capacity_tk"></th>
                            <th></th>
                            <th id="td_year_capacity_usd"></th>
                        </tfoot>
                    </table>
                    </fieldset>                
                </td>
                <td width="10"></td>
                <td align="center" valign="top" width="450">
                <fieldset>
                    <table cellpadding="0" border="1" cellspacing="0" class="rpt_table" rules="all">
                        <thead>
                            <th width="80">Date</th>
                            <th width="50">Day</th>
                            <th width="70">Day Status</th>
                            <th width="100">Capacity (Tk)</th>
                            <th>Capacity (Usd)</th>
                        </thead>
                        <tbody id="td_month_breakdown">
                            <tr>
                                <td align="center"><? echo  change_date_format(date('d-m-Y')); ?></td>
                                <td align="center"><? echo  date('D'); ?></td>
                                <td align="center">
									<?
										$day_status=array(1=>"Open",2=>"Closed");
										echo create_drop_down( "cbo_day_status_1", 72,$day_status,"", 0, "-- Select --", 1,"" );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_day_capacity_tk_1" id="txt_capacity_tk_1" class="text_boxes" style="width:100px" readonly/>
                                </td>
                                <td>
                                    <input type="text" name="txt_day_capacity_usd_1" id="txt_capacity_usd_1" class="text_boxes" style="width:100px" readonly/>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <th colspan="3" align="right" ><strong>Total : </strong></th>
                            <th id="tot_day_capacity_tk"></th>
                            <th id="tot_day_capacity_usd"></th>
                        </tfoot>
                    </table>
                </fieldset>
                
                </td>
            </tr>
        </table>
        
        
        
    </form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>