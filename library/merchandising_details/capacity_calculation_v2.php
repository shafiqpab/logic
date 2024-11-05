<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Capacity Calculationy
Functionality	:	
JS Functions	:
Created by		:	Kausar
Creation date 	: 	08.10.2013
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
echo load_html_head_contents("Capacity Calculationy", "../../", 1, 1,$unicode,'','');
?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
var permission='<? echo $permission; ?>';

	function fnc_capacity_calculation( operation )
	{
		freeze_window(operation);
		if(operation==2){
			alert("Delete not allowed !");
			release_freezing();
			return;
		}
		
		if( form_validation('cbo_company_id*cbo_capacity_source*cbo_product_category*cbo_product_type*cbo_year*cbo_month*txt_avg_mch_line*txt_basic_smv*txt_efficiency_per','Company Name*Capacity Source*Product Category**Product Type*Year*Month*Avg. Machine Line*Basic SMV*Efficiency %')==false )
		{
			release_freezing();
			return;
		}	
		
		/* Start: No of Line value Check*/
		var amountCheck = 0;
		var r =1;
		$("#date_tbl tr").each(function () {
			amountCheck = (amountCheck + $("#txt_no_of_line_"+r).val()*1);
			r++;
		});
		if(amountCheck == 0){ alert("Please given value in No. of Line");return; }
		/* End: No of Line value Check*/
		
		
		var tot_row_date=$('#date_tbl tr').length;
		var tot_row_year=$('#year_tbl tbody tr').length;
		
		var data2=""; var z=1;
		for(i=1; i<=tot_row_date; i++)
		{
			data2+="&txt_date_" + z + "='" + $('#txt_date_'+i).val()+"'"+"&cbo_day_status_" + z + "='" + $('#cbo_day_status_'+i).val()+"'"+"&txt_no_of_line_" + z + "='" + $('#txt_no_of_line_'+i).val()+"'"+"&txt_capacity_min_" + z + "='" + $('#txt_capacity_min_'+i).val()+"'"+"&txt_capacity_pcs_" + z + "='" + $('#txt_capacity_pcs_'+i).val()+"'"+"&update_id_dtls_" + z + "='" + $('#update_id_dtls_'+i).val()+"'";
			z++;
			//data2+=get_submitted_data_string('txt_date_'+i+'*cbo_day_status_'+i+'*txt_no_of_line_'+i+'*txt_capacity_min_'+i+'*txt_capacity_pcs_'+i+'*update_id_dtls_'+i,"../../",i);
		}
		var data3=""; var q=1;
		
		for(i=1; i<=tot_row_year; i++)
		{
			data3+="&txt_month_id_" + q + "='" + $('#txt_month_id_'+i).val()+"'"+"&txt_working_day_" + q + "='" + $('#txt_working_day_'+i).val()+"'"+"&txt_year_capacity_min_" + q + "='" + $('#txt_year_capacity_min_'+i).val()+"'"+"&txt_year_capacity_pcs_" + q + "='" + $('#txt_year_capacity_pcs_'+i).val()+"'"+"&update_id_year_dtls_" + q + "='" + $('#update_id_year_dtls_'+i).val()+"'";
			q++;
			//data3+=get_submitted_data_string('txt_month_id_'+i+'*txt_working_day_'+i+'*txt_year_capacity_min_'+i+'*txt_year_capacity_pcs_'+i+'*update_id_year_dtls_'+i,"../../",i);
		}
		//alert (data2);return;
		var data="action=save_update_delete&operation="+operation+"&tot_row_date="+tot_row_date+"&tot_row_year="+tot_row_year+get_submitted_data_string('cbo_company_id*cbo_capacity_source*cbo_location_id*cbo_year*cbo_month*txt_avg_mch_line*txt_basic_smv*txt_efficiency_per*update_id*cbo_product_category*cbo_product_type',"../../")+data2+data3;
		// alert (data); 
		
		//var data=data1+data2+data3;
		
		
		http.open("POST","requires/capacity_calculation_controller_v2.php",true);
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
			
			var save_id_return_date = return_global_ajax_value( response[1], 'load_php_dtls_form_return_id_date', '', 'requires/capacity_calculation_controller_v2');
			
			//alert(response[1]);
			
			var response_return_id=save_id_return_date.split('*');
			
			var k=1;
			for(i=0; i<=response_return_id.length; i++)
			{
				$('#update_id_dtls_'+k).val(response_return_id[i])
				k++;
			}
			
			var save_id_return_year = return_global_ajax_value( response[1], 'load_php_dtls_form_return_id_year', '', 'requires/capacity_calculation_controller_v2');
			var response_return_id_year=save_id_return_year.split('*');
			
			var l=1;
			for(i=0; i<=response_return_id_year.length; i++)
			{
				$('#update_id_year_dtls_'+l).val(response_return_id_year[i])
				l++;
			}
			
			/*daysInMonth(); 
			update_pr( $('#cbo_month').val() );
			update_year( $('#cbo_year').val() ); // Populate Month wise year data*/
			
			if(response[0]==0 || response[0]==1)
			{
				disable_enable_fields( 'cbo_company_id*cbo_capacity_source*cbo_location_id*cbo_year*txt_avg_mch_line*txt_basic_smv*cbo_product_category*cbo_product_type*txt_efficiency_per', 1, '', '');
				set_button_status(1, permission, 'fnc_capacity_calculation',1);
			}
			release_freezing();
		}
	}



	function update_pr(val)
	{
		//alert(val+"__"+id);
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_year').value+"_"+document.getElementById('cbo_month').value+"_"+document.getElementById('cbo_capacity_source').value+"_"+document.getElementById('cbo_product_category').value+"_"+document.getElementById('cbo_product_type').value;
		get_php_form_data( data, "load_php_dtls_form_update_val", "requires/capacity_calculation_controller_v2" );
	}
	
	function last_fild_value(line)
	{
		var data=document.getElementById('line_id').value;
		var tot_row=$('#date_tbl tr').length;
		for(var i=1; i<= tot_row; i++)
		{
			$('#txt_no_of_line_'+i).val(data);
		}
		//$('#txt_no_of_line_'+i).val(line_val);
		//alert (line_val);
	}
	
	function update_year(val)
	{
		if( form_validation('cbo_product_category','Product Category')==false )
		{
			return;
		}
		var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+val+"_"+document.getElementById('cbo_capacity_source').value+"_"+document.getElementById('cbo_product_category').value+"_"+document.getElementById('cbo_product_type').value;
		//alert(data);
		
		get_php_form_data( data, "load_php_dtls_form_update", "requires/capacity_calculation_controller_v2" );
	}
	
	function daysInMonth() 
	{
		
		var data=document.getElementById('cbo_year').value+"_"+document.getElementById('cbo_month').value+"_"+document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_location_id').value+"_"+document.getElementById('cbo_product_category').value+"_"+document.getElementById('cbo_product_type').value;
		
		var list_view_capacity = return_global_ajax_value( data, 'load_php_dtls_form', '', 'requires/capacity_calculation_controller_v2');
			
 		if(list_view_capacity!='')
		{
			$("#date_tbl tbody").html('');
			$("#date_tbl tr").remove();
			$("#date_tbl").append(list_view_capacity);
		}
	}
	
	function calculate_capacity_min_pcs(data_val,id_no)
	{
		if( form_validation('cbo_company_id*cbo_capacity_source*cbo_product_category*cbo_product_type*cbo_year*cbo_month*txt_avg_mch_line*txt_basic_smv*txt_efficiency_per','Company Name*Capacity Source*Product Category*Product Type*Year*Month*Avg. Machine Line*Basic SMV*Efficiency %')==false )
		{
			return;
		}
		
		var day_status=document.getElementById('cbo_day_status_'+id_no).value;
		var txt_date=document.getElementById('txt_date_'+id_no).value;
		//alert(txt_date);
		/*if (day_status==2)
		{
			$('#txt_capacity_min_'+id_no).val('');
			$('#txt_capacity_min_'+id_no).attr('disabled','disabled');
			$('#txt_capacity_pcs_'+id_no).val('');
			$('#txt_capacity_pcs_'+id_no).attr('disabled','disabled');
			return;
		}*/
		var tot_row=$('#date_tbl tr').length;
		var avg_mch_line=document.getElementById('txt_avg_mch_line').value;
		var basic_smv=document.getElementById('txt_basic_smv').value;
		var txt_efficiency_per=document.getElementById('txt_efficiency_per').value;
		if(txt_efficiency_per =="" || txt_efficiency_per ==0)
		{
			alert("Insert Capacity %");
			return;
		}
		var working_hour = return_global_ajax_value( document.getElementById('cbo_company_id').value+"_"+txt_date, 'working_hour', '', 'requires/capacity_calculation_controller_v2');
		//alert(working_hour);
		
		if(working_hour ==0)
		{
			alert("Set Working Hour");
			return;
		}
		var title_capacity_mint='('+avg_mch_line+'*'+data_val+'*'+trim(working_hour)+'*'+60+')*'+txt_efficiency_per+'/'+100; 
		var capacity_mint=(avg_mch_line*data_val*working_hour*60)*txt_efficiency_per/100; 
		var capacity_pcs=capacity_mint/basic_smv;
		
		var row_close=0;
		for(var i=1; i<= tot_row; i++)
		{
			if($('#cbo_day_status_'+i).val()==1) row_close++;
		}

		for(var i=id_no; i<= tot_row; i++)
		{
		
			$('#txt_capacity_min_'+i).val( capacity_mint );
			$('#txt_capacity_pcs_'+i).val( capacity_pcs );
			$('#txt_no_of_line_'+i).val( data_val );
			
			$('#txt_capacity_min_'+i).attr("title",title_capacity_mint);
		
			if ($('#cbo_day_status_'+i).val()==2)
			{
				$('#txt_capacity_min_'+i).val('');
				$('#txt_capacity_min_'+i).attr('disabled','disabled');
				$('#txt_capacity_pcs_'+i).val('');
				$('#txt_capacity_pcs_'+i).attr('disabled','disabled');
			}
		
		
		}
		math_operation( "total_min", "txt_capacity_min_", "+", tot_row );
		math_operation( "total_pcs", "txt_capacity_pcs_", "+", tot_row );
		
		var month_id=document.getElementById('cbo_month').value;
		count_day_min_pcs(month_id,row_close);
	}
	
	function open_close(drop_val,id)
	{
		//alert (drop_val);
		var tot_row=$('#date_tbl tr').length;
		var total_min="";
		var total_pcs="";
		var row_close=0;
		for(var i=1; i<= tot_row; i++)
		{
			total_min=document.getElementById('txt_capacity_min_'+i).value;
			total_pcs=document.getElementById('txt_capacity_pcs_'+i).value;
			if($('#cbo_day_status_'+i).val()==1) row_close++;
		}

		for(var k=id; k<= tot_row; k++)
		{
			total_min=document.getElementById('txt_capacity_min_'+k).value;
			total_pcs=document.getElementById('txt_capacity_pcs_'+k).value;
			var day_status=document.getElementById('cbo_day_status_'+k).value;
			if (drop_val==2)
			{
				//alert (id);
				$('#txt_capacity_min_'+id).val('');
				$('#txt_capacity_min_'+id).attr('disabled','disabled');
				$('#txt_capacity_pcs_'+id).val('');
				$('#txt_capacity_pcs_'+id).attr('disabled','disabled');
			}
			else if (day_status==1)
			{
				$('#txt_capacity_min_'+id).removeAttr('disabled','disabled');
				$('#txt_capacity_min_'+id).val(total_min);
				$('#txt_capacity_pcs_'+id).removeAttr('disabled','disabled');
				$('#txt_capacity_pcs_'+id).val(total_pcs);
			}
		}
		math_operation( "total_min", "txt_capacity_min_", "+", tot_row );
		math_operation( "total_pcs", "txt_capacity_pcs_", "+", tot_row );
		var month_id=document.getElementById('cbo_month').value;
		count_day_min_pcs(month_id,row_close);
	}

	
	function count_day_min_pcs( month_id,row_close )
	{
		var tot_row_year	=	$('#year_tbl tbody tr').length;
		var total_min		=	document.getElementById('total_min').value;
		var total_pcs		=	document.getElementById('total_pcs').value;

		for(var i=month_id; i<= tot_row_year; i++)
		{
			if (month_id==$('#txt_month_id_'+i).val())
			{
				$('#txt_working_day_'+i).val( row_close );
				$('#txt_year_capacity_min_'+i).val( total_min );
				$('#txt_year_capacity_pcs_'+i).val( total_pcs );
			}
		}
		math_operation( "txt_working_day_total", "txt_working_day_", "+", tot_row_year );
		math_operation( "txt_capacity_min_total", "txt_year_capacity_min_", "+", tot_row_year );
		math_operation( "txt_capacity_pcs_total", "txt_year_capacity_pcs_", "+", tot_row_year );
	}
	
	function location_select()
	{
		if($('#cbo_location_id option').length==1)
		{
			$('#cbo_location_id').val($('#cbo_location_id option:last').val());
			//eval($('#cbo_location_name').attr('onchange'));
		}
		else //if($('#cbo_location_id option').length==2)
		{
			if($('#cbo_location_id option:first').val()==0)
			{
				$('#cbo_location_id').val($('#cbo_location_id option:last').val());
				//eval($('#cbo_location_name').attr('onchange')); 
			}
		}
	}
	
	function fnc_year_default()
	{
		$('#cbo_year').val('0');
	}
	
	function fnc_reset_form()
	{
		<? 
		$jsbatch_against= json_encode($months);
		echo " var monthArray = ". $jsbatch_against . ";\n";
		 ?>
		$('#year_tbl tbody tr').remove();
		
		var html = "";
		for(var r =1;  r <= 12; r++)
		{
			 html +='<tr><td><input type="hidden" id="update_id_year_dtls_'+r+'" name="update_id_year_dtls_'+r+'"/><input type="text" name="txt_sl_no_'+r+'" id="txt_sl_no_'+r+'" class="text_boxes_numeric" value="'+r+'" style="width:50px" readonly /></td><td><input type="text" name="txt_month_'+r+'" id="txt_month_'+r+'" class="text_boxes" style="width:70px" value="'+monthArray[r]+'" readonly /><input type="hidden" id="txt_month_id_'+r+'" name="txt_month_id_'+r+'" value="'+r+'" /></td><td><input type="text" name="txt_working_day_'+r+'" id="txt_working_day_'+r+'" class="text_boxes_numeric" style="width:80px" readonly /></td><td><input type="text" name="txt_year_capacity_min_'+r+'" id="txt_year_capacity_min_'+r+'" class="text_boxes_numeric" style="width:100px" readonly /></td><td><input type="text" name="txt_year_capacity_pcs_'+r+'" id="txt_year_capacity_pcs_'+r+'" class="text_boxes_numeric" style="width:100px" readonly /></td></tr>';
		}
		//alert(html);
		$("#year_tbl tbody").html(html);
	}
	
	
		
</script>
</head>
<body onLoad="set_hotkey()">
<div align="center" style="width:100%;">
   	<? echo load_freeze_divs ("../../",$permission);  ?>
    <form name="capacitycalculation_1" id="capacitycalculation_1" method="" autocomplete="off">
    <fieldset style="width:900px ">
    <legend>Capacity Calculationy</legend>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td width="130" class="must_entry_caption">Company </td>
                <td width="160">
                    <input type="hidden" id="update_id" name="update_id" />
                    <input type="hidden" id="line_id" name="line_id" />
                    <?=create_drop_down( "cbo_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $selected, "load_drop_down( 'requires/capacity_calculation_controller_v2', this.value, 'load_drop_down_location', 'location_td'); location_select();","","","","","",2); ?>
                </td>
                <td width="130" class="must_entry_caption">Capacity Source </td>
                <td width="160"><?=create_drop_down( "cbo_capacity_source",150,$knitting_source,"", 1, "--Select Location--", $selected, "","","1,3","","","",""); ?></td>
                <td width="130">Location </td>
                <td id="location_td"><?=create_drop_down( "cbo_location_id",150,$blank_array,"", 1, "--Select Location--", $selected, "","","","","","",2); ?></td>
            </tr>
            <tr>
            	<td class="must_entry_caption">Product Category </td>
            	<td><?=create_drop_down( "cbo_product_category", 150, $product_category,'', 1,"--- Select Category ---",0,"load_drop_down( 'requires/capacity_calculation_controller_v2', this.value, 'load_drop_down_product_type', 'product_type' );"); ?> </td>
                <td class="must_entry_caption">Product Type</td>
                <td id="product_type"><?=create_drop_down( "cbo_product_type", 150, $blank_array,'', 1,"--- Select Product Type ---",0); ?></td>
                <td class="must_entry_caption">Year</td>
                <td>
                    <?
                        $cyear=date("Y",time());
                        $pyear=$cyear-5;
                        for ($i=0; $i<5; $i++)
                        {
                        	$year[$pyear+$i]=$pyear+$i;
                        }
                        echo create_drop_down( "cbo_year", 150,$year,"", 1, "-- Select --", $selected,"update_year(this.value);" );
                    ?>                              
                </td>
                
            </tr>
            <tr>
            	<td class="must_entry_caption">Month</td>
                <td>
                    <?
                        $cmonth=date("M",time());
                        echo create_drop_down( "cbo_month", 150,$months,"", 1, "-- Select --", $cmonth,"daysInMonth(); update_pr(this.value);" );
                    ?>
                </td>
            	<td class="must_entry_caption">Man / Machine Per Line</td>
                <td><input type="text" name="txt_avg_mch_line" id="txt_avg_mch_line" class="text_boxes_numeric" style="width:140px"/></td>
                <td class="must_entry_caption">Basic SAM</td>
                <td><input type="text" name="txt_basic_smv" id="txt_basic_smv" class="text_boxes_numeric" style="width:140px" /></td>
            </tr>
            <tr>
            	<td class="must_entry_caption">Efficiency %</td>
                <td><input type="text" name="txt_efficiency_per" id="txt_efficiency_per" class="text_boxes_numeric" style="width:140px"/></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <table cellpadding="0" cellspacing="2" width="100%">
            <tr>
                <td align="center" colspan="9" valign="middle" class="button_container">
                    <?
                        echo load_submit_buttons( $permission, "fnc_capacity_calculation", 0,0 ,"reset_form('capacitycalculation_1','','','','disable_enable_fields(\'cbo_company_id*cbo_capacity_source*cbo_product_category*cbo_product_type*cbo_location_id*cbo_year*txt_avg_mch_line*txt_basic_smv*txt_efficiency_per\'); $(\'#date_tbl tr:not(:first)\').remove();fnc_reset_form(); ')",1);
                    ?>
                </td>
            </tr>
        </table>
        </fieldset>
        <br>
        <table cellpadding="0" cellspacing="0" width="100%" >
            <tr>
                <td align="center" valign="top" width="540">
                <fieldset style="width:540px ">
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all">
                        <thead>
                            <th width="67">Date</th>
                             <th width="40">Day</th>
                            <th width="70">Day Status</th>
                            <th width="60">No. of Line</th>
                            <th width="100">Capacity (Mnt.)</th>
                            <th width="100">Capacity (Pcs)</th>
                        </thead>
                    </table>
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" id="date_tbl" rules="all">
                        <tbody>
                            <tr>
                                <td>
                                    <input type="hidden" id="update_id_dtls_1" name="update_id_dtls_1" />
                                    <input type="text" name="txt_date_1" id="txt_date_1" class="text_boxes" style="width:67px" value="<? //echo  change_date_format(add_date($c_date, $k));  ?>" readonly />
                                </td>
                                 <td width="40" id="tdDay_1"><? echo  date("D",time());  ?></td>
                                <td>
									<?
										$day_status=array(1=>"Open",2=>"Closed");
										echo create_drop_down( "cbo_day_status_1", 72,$day_status,"", 0, "-- Select --", 1,"" );
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_no_of_line_1" id="txt_no_of_line_1" class="text_boxes_numeric" style="width:60px" readonly/>
                                </td>
                                <td>
                                    <input type="text" name="txt_capacity_min_1" id="txt_capacity_min_1" class="text_boxes" style="width:100px" readonly/>
                                </td>
                                <td>
                                    <input type="text" name="txt_capacity_pcs_1" id="txt_capacity_pcs_1" class="text_boxes" style="width:100px" readonly/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all">
                        <tfoot>
                        <tr>
                            <td colspan="3" align="right" width="250" ><strong>Total : </strong></td>
                            <td colspan="1" align="left">
                                <input type="text" name="total_min" id="total_min" class="text_boxes_numeric" style="width:100px" readonly />
                            </td>
                            <td colspan="1">
                                <input type="text" name="total_pcs" id="total_pcs" class="text_boxes_numeric" style="width:100px" readonly />
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </fieldset>
                </td>
                <td align="center" valign="top" width="420">
                <fieldset style="width:440px ">
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all" >
                        <thead>
                            <th width="50">SL</th>
                            <th width="70">Month</th>
                            <th width="80">Working Day</th>
                            <th width="100">Capacity (Mnt.)</th>
                            <th width="100">Capacity (Pcs)</th>
                        </thead>
                    </table>
                     <table cellpadding="0" border="1" cellspacing="0" width="100%" id="year_tbl" class="rpt_table" rules="all">
                        <tbody>
							<? 
							$kk=1; 
							for( $i = 1; $i <= 12; $i++ ) 
							{ 
							?>
                            <tr>
                                <td>
                                    <input type="text" id="txt_sl_no_<?php echo $kk; ?>" name="txt_sl_no_<?php echo $kk; ?>"  class="text_boxes_numeric" value="<?php echo $kk; ?>" style="width:50px" readonly />
                                    <input type="hidden" id="update_id_year_dtls_<?php echo $kk; ?>" name="update_id_year_dtls_<?php echo $kk; ?>" />
                                </td>
                                <td>
                                    <input type="text" id="txt_month_<?php echo $kk; ?>" name="txt_month_<?php echo $kk; ?>" class="text_boxes" style="width:70px" value="<? echo $months[$i]; ?>" readonly />
                                    <input type="hidden" id="txt_month_id_<?php echo $kk; ?>" name="txt_month_id_<?php echo $kk; ?>" value="<?php echo $kk; ?>" />
                                </td>
                                <td>
                                    <input type="text" id="txt_working_day_<?php echo $kk; ?>" name="txt_working_day_<?php echo $kk; ?>" class="text_boxes_numeric" style="width:80px" readonly />
                                </td>
                                <td>
                                    <input type="text" id="txt_year_capacity_min_<?php echo $kk; ?>" name="txt_year_capacity_min_<?php echo $kk; ?>" class="text_boxes_numeric" style="width:100px" readonly />
                                </td>
                                <td>
                                    <input type="text" id="txt_year_capacity_pcs_<?php echo $kk; ?>" name="txt_year_capacity_pcs_<?php echo $kk; ?>" class="text_boxes_numeric" style="width:100px" readonly />
                                </td>
                            </tr>
                            <? 
							$kk++; 
							} 
							?>
                        </tbody>
                    </table>
                    <table cellpadding="0" border="1" cellspacing="0" width="100%" class="rpt_table" rules="all">
                        <tfoot>
                            <tr>
                                <td colspan="2" width="120" align="right"><strong>Total : </strong></td>
                                <td width="80" align="right">
                                    <input type="text" id="txt_working_day_total" name="txt_working_day_total" class="text_boxes_numeric" style="width:80px;" readonly />
                                </td>
                                <td width="100">
                                    <input type="text" id="txt_capacity_min_total" name="txt_capacity_min_total" class="text_boxes_numeric" style="width:100px;" readonly />
                                </td>
                                <td width="100">
                                    <input type="text" id="txt_capacity_pcs_total" name="txt_capacity_pcs_total" class="text_boxes_numeric" style="width:100px;" readonly />
                                </td>
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