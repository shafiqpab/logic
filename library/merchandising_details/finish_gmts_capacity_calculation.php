<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Gmts Capacity Calculation
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam
Creation date 	: 	10.03.2016
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
echo load_html_head_contents("Finish Gmts Capacity Calculation", "../../", 1, 1,$unicode,'','');
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

	
	function daysInMonth() 
	{
		if ( form_validation('cbo_company_id*cbo_fin_type*cbo_year','Company Name*Finish Type*year')==false )
		{
			return;
		}	
		
		var data=document.getElementById('cbo_year').value+"_"+document.getElementById('cbo_month').value+"_"+document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_fin_type').value;
		get_php_form_data( data, "load_php_dtls_form_update", "requires/finish_gmts_capacity_calculation_controller" );
		
		if(document.getElementById('cbo_year').value!=0 && document.getElementById('cbo_month').value!=0){
			var list_view_capacity = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/finish_gmts_capacity_calculation_controller');
				
			if(list_view_capacity!='')
			{
				$("#days_table tbody").html('');
				$("#days_table tr").remove();
				$("#days_table").append(list_view_capacity);
			}
		}
	}

	function fn_calculate_capacity(){
		
		if(form_validation('cbo_year*cbo_month','Year*Month')==false ){
			return;
		}	
		
		var cbo_year=document.getElementById('cbo_year').value;	
		var cbo_month=document.getElementById('cbo_month').value;	
		var txt_wo_hrs=document.getElementById('txt_wo_hrs').value;	
		var txt_efficiency_per=document.getElementById('txt_efficiency_per').value;	
		var txt_smv=document.getElementById('txt_smv').value;
		var tot_day_row=$('#days_table tr').length;
		
		var tot_day_capacity_mint=0;
		var tot_day_capacity_pcs=0;
		var tot_manpower=0;
		var open_rows=0;
			for(var i=1; i<= tot_day_row; i++)
			{
				var txt_manpower=document.getElementById('manpower_'+i).value*1;
				if($('#dayStatus_'+i).val()==1){

					var day_capacity_mint=((txt_wo_hrs*txt_manpower*60)*txt_efficiency_per)/100;
					var day_capacity_pcs=day_capacity_mint/txt_smv;
					tot_day_capacity_mint+=day_capacity_mint;
					tot_day_capacity_pcs+=day_capacity_pcs;
					tot_manpower+=txt_manpower;
					
					if(day_capacity_mint){
						document.getElementById('dayCapacityMint_'+i).innerHTML=day_capacity_mint.toFixed(2);
					}
					if(day_capacity_pcs && txt_smv){
						document.getElementById('dayCapacityPcs_'+i).innerHTML=day_capacity_pcs.toFixed(2);
					}
					document.getElementById('days_tr_'+i).style.color = 'black';
				open_rows++;
				}
				else
				{
					$('#dayCapacityMint_'+i).html('');
					$('#dayCapacityPcs_'+i).html('');
					$('#manpower_'+i).val('');
					document.getElementById('days_tr_'+i).style.color = 'red';
				}
			}
			document.getElementById('workingDays_'+cbo_month).innerHTML=open_rows;
			document.getElementById('tot_manpower').innerHTML=tot_manpower.toFixed(2);
			document.getElementById('tot_day_capacity_mint').innerHTML=tot_day_capacity_mint.toFixed(2);
			document.getElementById('tot_day_capacity_pcs').innerHTML=tot_day_capacity_pcs.toFixed(2);
			
			document.getElementById('monthCapMint_'+cbo_month).innerHTML=tot_day_capacity_mint.toFixed(2);
			if(tot_day_capacity_pcs && txt_smv){
				document.getElementById('monthCapPcs_'+cbo_month).innerHTML=tot_day_capacity_pcs.toFixed(2);
			}
	
	
	
			var total_month_capacity_mint=0;
			var total_month_capacity_pcs=0;
			var total_working_day=0;
			
			for(var m=1; m<= 12; m++)
			{
				total_month_capacity_mint	+=	$('#monthCapMint_'+m).html()*1;
				total_month_capacity_pcs	+=	$('#monthCapPcs_'+m).html()*1;
				total_working_day			+=	$('#workingDays_'+m).html()*1;
			}
			document.getElementById('totMonthWorkignDays').innerHTML=total_working_day;
			document.getElementById('totMonthCapMint').innerHTML=total_month_capacity_mint.toFixed(2);
			document.getElementById('totMonthCapPcs').innerHTML=total_month_capacity_pcs.toFixed(2);
	
	
	
	
	}
	
	
	function fnc_finish_gmts_capacity_calculation( operation )
	{
		
		var tot_day_row=$('#days_table tr').length;
		var cbo_month=document.getElementById('cbo_month').value;
		
		if ( form_validation('cbo_company_id*cbo_fin_type*cbo_year*cbo_month*txt_wo_hrs*txt_efficiency_per*txt_smv','Company Name*Finish Type*Year*Month*WO Hour*Efficiency %*smv')==false )
		{
			return;
		}	

		//Days data---------------------------------------------------
		var day_data="";
		for(i=1; i<=tot_day_row; i++)
		{
			var updateIdDay 	= $('#updateIdDay_'+i).val();
			var tdDate 			= $('#tdDate_'+i).html();
			var tdDay 			= $('#tdDay_'+i).html();
			var dayStatus 		= $('#dayStatus_'+i).val();
			var dayCapacityMint = $('#dayCapacityMint_'+i).html()*1;
			var dayCapacityPcs 	= $('#dayCapacityPcs_'+i).html()*1;
			var manPower 		= $('#manpower_'+i).val()*1;
			
			if(day_data==""){
				day_data = tdDate+'**'+i+'**'+dayStatus+'**'+manPower+'**'+dayCapacityMint+'**'+dayCapacityPcs+'**'+updateIdDay;
			}
			else{
				day_data += '__'+tdDate+'**'+i+'**'+dayStatus+'**'+manPower+'**'+dayCapacityMint+'**'+dayCapacityPcs+'**'+updateIdDay;
			}
			
			
		}
		
		
		
		
			//month data---------------------------------------------------
			var update_id_year_dtls= $('#update_id_year_dtls_'+cbo_month).val();
			var workingDays= $('#workingDays_'+cbo_month).html();
			var monthCapMint= $('#monthCapMint_'+cbo_month).html();
			var monthCapPcs= $('#monthCapPcs_'+cbo_month).html();
			var month_data = cbo_month+'**'+workingDays+'**'+monthCapMint+'**'+monthCapPcs+'**'+update_id_year_dtls;
			
		var data="action=save_update_delete&operation="+operation+"&tot_row_date="+tot_day_row+"&month_data="+month_data+"&day_data="+day_data+get_submitted_data_string('cbo_company_id*cbo_location_id*cbo_fin_type*cbo_year*cbo_month*txt_wo_hrs*txt_efficiency_per*txt_smv*update_id',"../../");
		 //alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/finish_gmts_capacity_calculation_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_finish_gmts_capacity_calculation_reponse;
	}
	
	
	
	function fnc_finish_gmts_capacity_calculation_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			show_msg(reponse[0]);
			
			
			if(reponse[0]!=10){
				$('#update_id').val(reponse[1]);
				var data=document.getElementById('cbo_year').value+"_"+document.getElementById('cbo_month').value+"_"+document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_fin_type').value;
				get_php_form_data( data, "load_php_dtls_form_update", "requires/finish_gmts_capacity_calculation_controller" );
				var list_view_capacity = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/finish_gmts_capacity_calculation_controller');
				if(list_view_capacity!='')
				{
					$("#days_table tbody").html('');
					$("#days_table tr").remove();
					$("#days_table").append(list_view_capacity);
				}
				
				
				set_button_status(1, permission, 'fnc_finish_gmts_capacity_calculation',1);
			}
			release_freezing();
		}
	}
	
	function copy_manpower(str){
		var tot_day_row=$('#days_table tr').length;	
		var manPower = $('#manpower_'+str).val()*1;
		for(i=str+1; i<=tot_day_row; i++)
		{
			if($('#dayStatus_'+i).val()==1){
				$('#manpower_'+i).val(manPower);
			}
			else{
				$('#manpower_'+i).val('');
			}
		}
		
		if($('#dayStatus_'+str).val()==2){
			$('#manpower_'+str).val('');
		}
		fn_calculate_capacity();
	}
	
	
//===============================================================================	
		
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
   	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="fgcc_1" id="fgcc_1" method="" autocomplete="off">
    <fieldset style="width:880px ">
    <legend>Finish Gmts Capacity Calculation</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td class="must_entry_caption" align="right">Company </td>
                <td>
                    <?
                        echo create_drop_down( "cbo_company_id",160,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/finish_gmts_capacity_calculation_controller', this.value, 'load_drop_down_location', 'location_td');","","","","","",2);
                    ?>
                </td>
                <td align="right">Location </td>
                <td id="location_td">
                    <?
                        echo create_drop_down( "cbo_location_id",160,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2);
                    ?>
                </td>
                <td class="must_entry_caption" align="right">Fin. Type</td>
                <td>
                    <? $fin_type_arr=array(1=>"Iron",2=>"Poly",3=>"QC",4=>"Carton");
                        echo create_drop_down( "cbo_fin_type",160,$fin_type_arr,"", 1, "-- Select --", $selected, "$('#smv_td').text($('#cbo_fin_type option:selected').text()+' SMV');");
                    ?>
                </td>
                
            </tr>
            <tr>
            <td class="must_entry_caption" align="right">Year</td>
                <td>
                    <?
                        echo create_drop_down( "cbo_year", 160,$year,"", 1, "-- Select --", $selected,"daysInMonth();" );
                    ?>                              
                </td>
                <td class="must_entry_caption" align="right">Month </td>
                <td>
                    <?
                        echo create_drop_down( "cbo_month", 160,$months,"", 1, "-- Select --","","daysInMonth();" );
                    ?>
                </td>
                <td class="must_entry_caption" align="right">WO Hrs</td>
                <td>
                    <input type="text" name="txt_wo_hrs" id="txt_wo_hrs" class="text_boxes_numeric" style="width:150px" onKeyUp="fn_calculate_capacity()" />
                </td>
            </tr>
            <tr>
                <td align="right" class="must_entry_caption">Efficiency %</td>
                <td>
                    <input type="text" name="txt_efficiency_per" id="txt_efficiency_per" class="text_boxes_numeric" style="width:150px" onKeyUp="fn_calculate_capacity()"/>
                </td>
                
                <td class="must_entry_caption" align="right" id="smv_td">Iron SMV</td>
                <td>
                    <input type="text" name="txt_smv" id="txt_smv" class="text_boxes_numeric" style="width:150px" onKeyUp="fn_calculate_capacity()"/>
                </td>
                
                
                
            </tr>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="center" colspan="9" valign="middle" class="button_container">
                    <?
                        echo load_submit_buttons( $permission, "fnc_finish_gmts_capacity_calculation", 0,0 ,"reset_form('capacitycalculation_1','','','','disable_enable_fields(\'cbo_company_id*cbo_location_id*cbo_year*txt_avg_mch_line*txt_basic_smv\'); $(\'#date_tbl tr:not(:first)\').remove(); ')",1);
                    ?>
                    <input type="hidden" id="update_id" value="">
                </td>
            </tr>
        </table>
        </fieldset>
        <br>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td align="center" valign="top" width="500">
                <fieldset style="width:95%">
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all">
                        <thead>
                            <th width="60">Date</th>
                            <th width="40">Day</th>
                            <th width="80">Day Status</th>
                            <th width="80">Manpower</th>
                            <th width="100">Capacity (Mnt.)</th>
                            <th >Capacity (Pcs)</th>
                        </thead>
                    </table>
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" id="days_table" rules="all">
                        <tbody>
                            <tr>
                                <td width="60" id="tdDate_1" align="center"><? echo  date("d-m-Y",time());  ?></td>
                                <td width="40" id="tdDay_1"><? echo  date("D",time());  ?></td>
                                <td width="80" align="center">
									<?
										$day_status=array(1=>"Open",2=>"Closed");
										echo create_drop_down( "dayStatus_1", 72,$day_status,"", 0, "-- Select --", 1,"" );
                                    ?>
                                </td>
                                <td width="80" align="center"><input type="text" id="manpower_1" name="manpower_1" style="width:55px;" class="text_boxes_numeric"></td>
                                <td width="100" id="dayCapacityMint_1" align="right"></td>
                                <td id="dayCapacityPcs_1" align="right"></td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all">
                        <tfoot>
                            <th colspan="3" align="right" ><strong>Total : </strong></th>
                            <th id="tot_manpower" width="80" align="right">&nbsp;</th>
                            <th id="tot_day_capacity_mint" width="100" align="right">&nbsp;</th>
                            <th id="tot_day_capacity_pcs" width="108" align="right">&nbsp;</th>
                        </tfoot>
                    </table>
                </fieldset>
                </td>
                <td align="right" valign="top" width="400">
                <fieldset style="width:95%">
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all" >
                        <thead>
                            <th width="50">SL</th>
                            <th width="70">Month</th>
                            <th width="80">Working Day</th>
                            <th width="100">Capacity (Mnt.)</th>
                            <th>Capacity (Pcs)</th>
                        </thead>
                        <tbody>
							<?php foreach($months as $month_no=>$month_name){ 
                            $bgcolor=( $month_no%2==0 ? "#E9F3FF" : "#FFFFFF" );
                            ?>
                            
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <input type="hidden" id="update_id_year_dtls_<?php echo $month_no; ?>" name="update_id_year_dtls_<?php echo $month_no; ?>" value="" />
                                <td id="monthId_<?php echo $month_no;?>"><?php echo $month_no;?></td>
                                <td id="monthName_<?php echo $month_no;?>"><?php echo $month_name;?></td>
                                <td id="workingDays_<?php echo $month_no;?>" align="right"></td>
                                <td id="monthCapMint_<?php echo $month_no;?>" align="right"></td>
                                <td id="monthCapPcs_<?php echo $month_no;?>" align="right"></td>
                            </tr>
                            <? $kk++; } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" align="right"><strong>Total : </strong></th>
                                <th id="totMonthWorkignDays" align="right"></th>
                                <th id="totMonthCapMint" align="right"></th>
                                <th id="totMonthCapPcs" align="right"></th>
                            </tr>
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