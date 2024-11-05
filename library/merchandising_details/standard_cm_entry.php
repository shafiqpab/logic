<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Standard CM", "../../", 1, 1,$unicode,'','');
?>	
<script language="javascript">

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";
 	var permission='<? echo $permission; ?>';
	
	function calculate()
	{
		var number1 = Number( $('#txt_bep_cm').val() );
		var number2 = Number( $('#txt_asking_profit').val() );
		var number3 = number1 + number2;
		$('#txt_asking_cm').val( number3 );
	}

	function fnc_standard_cm( operation )
	{
		if (form_validation('cbo_company_name*txt_applying_period_date*txt_applying_period_to_date*txt_monthly_cm*txt_number_machine*txt_working_hour*txt_cost_per_minute*txt_asking_avg_rate*cbo_status','Company Name*Applying Period Date from*Applying Period Date to*Monthly CM*Number of Factory Machine*Working Hour*Cost Per Minute*Asking AVG Rate*Status')==false)
		{
			return;
		} 
		else
		{
			//eval(get_submitted_variables('cbo_company_name*txt_applying_period_date*txt_applying_period_to_date*txt_bep_cm*txt_asking_cm*txt_asking_profit*cbo_status*update_id'));
			var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*txt_applying_period_date*txt_applying_period_to_date*txt_bep_cm*txt_asking_cm*txt_asking_profit*cbo_status*txt_monthly_cm*txt_number_machine*txt_working_hour*txt_cost_per_minute*txt_asking_avg_rate*update_id*txt_actual_cm*txt_max_profit*txt_depr_amort*txt_interest_expn*txt_income_tax*txt_operating_expn*txt_actual_cost_rmg_per*txt_actual_cost_source_tax',"../../");
			//alert(data);
			freeze_window(operation);
			http.open("POST","requires/standard_cm_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_standard_cm_reponse;
		}
	}

	function fnc_standard_cm_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			show_msg(reponse[0]);
			show_list_view('--'+reponse[1],'search_list_view','standard_cm_entry_list_view','../merchandising_details/requires/standard_cm_entry_controller','setFilterGrid("list_view",-1)');
			if(reponse[0]!=11){reset_form('standardcmentry_1','','');}
			set_button_status(0, permission, 'fnc_standard_cm',1);
			release_freezing();
		}
	} 

	function calculate_date()
	{		
		var thisDate=($('#txt_applying_period_date').val()).split('-');
		var last=new Date( (new Date(thisDate[2], thisDate[1],1))-1 );
		
		//alert(last);return;
		var last_date = last.getDate();
		var month = last.getMonth()+1;
		var year = last.getFullYear();
		
		if(month<10)
		{
			var months='0'+month;
		}
		else
		{
			var months=month;
		}
		
		var last_full_date=last_date+'-'+months+'-'+year;
		var first_full_date='01'+'-'+months+'-'+year;
		
		$('#txt_applying_period_date').val(first_full_date);
		$('#txt_applying_period_to_date').val(last_full_date);
		var cbo_company_name=$('#cbo_company_name').val();
		//var cbo_location_id=$('#cbo_location_id').val();
		var applying_period_date=$('#txt_applying_period_date').val();
		var w_days = return_ajax_request_value(cbo_company_name+'_'+applying_period_date, 'check_capacity_calculation', 'requires/standard_cm_entry_controller');
		var working_days=trim(w_days);
		if(working_days!='')
		{
			$('#txt_working_days').val(working_days);
		}
		else
		{
			$('#txt_working_days').val(26);
		}
	}

	function caculate_cost_per_minute()
	{
		var txt_monthly_cm = Number( $('#txt_monthly_cm').val() );
		var txt_working_hour = Number( $('#txt_working_hour').val() );
		var txt_number_machine = Number( $('#txt_number_machine').val() );
		var working_days= Number( $('#txt_working_days').val() );
		
		var txt_cost_per_day = (txt_monthly_cm/working_days);
		
		var txt_cost_per_minute = txt_cost_per_day/(txt_working_hour*60);
		//alert(txt_cost_per_minute);
		txt_cost_per_minute=txt_cost_per_minute/txt_number_machine;
		
		$('#txt_cost_per_minute').val(number_format_common(txt_cost_per_minute,5,0,''));
	}
	
	function load_location()
	{
		var company_name= $("#cbo_company_name").val();
		location_cost_per_minute(company_name);
	}
	
	function location_cost_per_minute(company)
	{
		var cost_per_setting =return_global_ajax_value( company, 'load_location_wise_cost_per_minute_variable_settings', '', 'requires/standard_cm_entry_controller');
		if(cost_per_setting==1)
		{
			$('#txt_cost_per_minute').removeAttr("onDblClick").attr("onDblClick","openmypage_cost_per_min();");
		}
		else
		{
			$('#txt_cost_per_minute').removeAttr("onDblClick","");
		}
	}
	
	function openmypage_cost_per_min()
	{
		var update_id= $("#update_id").val();
		if(update_id=="")
		{
			alert('Save First!');
			return;
		}
		
		var company_name= $("#cbo_company_name").val();
		var txt_applying_period_date= $("#txt_applying_period_date").val();
		var txt_applying_period_to_date= $("#txt_applying_period_to_date").val();
		var txt_working_days= $("#txt_working_days").val();
		
		var title = 'Location Wise Cost Per Minute Form';
		var page_link = 'requires/standard_cm_entry_controller.php?company_name='+company_name+'&txt_applying_period_date='+txt_applying_period_date+'&txt_applying_period_to_date='+txt_applying_period_to_date+'&txt_working_days='+txt_working_days+'&update_id='+update_id+'&action=location_cost_per_popup';
		//alert(page_link);
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=770px,height=370px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txtnofmachine");
			var theemail2=this.contentDoc.getElementById("txtcmexp");
			var theemail3=this.contentDoc.getElementById("txtwhour");
			
			var no_of_factory_mc=theemail.value;
			var tot_cm_exp=theemail2.value;
			var avg_working_hour=theemail3.value;
			$("#txt_number_machine").val(no_of_factory_mc);
			$("#txt_monthly_cm").val(tot_cm_exp);
			$("#txt_working_hour").val(avg_working_hour);
			//alert(no_of_factory_mc);
			caculate_cost_per_minute();
		}
	}

	function openmypage_particular_prsnt()
	{
		var update_id= $("#update_id").val();
		if(update_id=="")
		{
			alert('Save First!');
			return;
		}
		
		var company_name= $("#cbo_company_name").val();
		var txt_applying_period_date= $("#txt_applying_period_date").val();
		var txt_applying_period_to_date= $("#txt_applying_period_to_date").val();
		var txt_working_days= $("#txt_working_days").val();
		
		var title = 'Particular percent popup';
		var page_link = 'requires/standard_cm_entry_controller.php?company_name='+company_name+'&txt_applying_period_date='+txt_applying_period_date+'&txt_applying_period_to_date='+txt_applying_period_to_date+'&txt_working_days='+txt_working_days+'&update_id='+update_id+'&action=particular_prsnt_popup';
		//alert(page_link);
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=300px,height=270px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		}
	}

	
</script>
</head>
<body onLoad="set_hotkey(); load_location();">
<? echo load_freeze_divs ("../../",$permission);  ?>
<div align="center" style="width:100%;">	
	<fieldset style="width:700px;">
		<legend>Financial Parameter Setup</legend>
		<form name="standardcmentry_1" id="standardcmentry_1" autocomplete="off">	
			<table cellpadding="0" cellspacing="2" width="650">
				<tr>
					<td width="110" class="must_entry_caption">Company Name</td>
					<td width="220" colspan="3"><? echo create_drop_down( "cbo_company_name", 500, "select id,company_name from lib_company comp where is_deleted=0  and status_active=1 $company_cond order by company_name","id,company_name", 1, "--- Select Company ---", 0, "location_cost_per_minute(this.value)" ); ?></td>
				</tr>
				<tr>
					<td width="110" class="must_entry_caption">Applying Period </td>
					<td width="170"><input type="text" name="txt_applying_period_date" id="txt_applying_period_date" class="datepicker" style="width:150px; text-align:center" onChange="calculate_date()" readonly /></td>
					<td width="110">To</td>
					<td width="170"><input type="text" name="txt_applying_period_to_date" id="txt_applying_period_to_date" class="datepicker" style="width:150px; text-align:center" disabled/></td>
				</tr>			
				<tr>
					<td width="110">BEP CM %</td>
					<td width="170"><input type="text" name="txt_bep_cm" id="txt_bep_cm" onChange="calculate()" class="text_boxes_numeric" style="width:150px" /></td>
					<td width="110" class="must_entry_caption">Asking Profit %</td>
					<td width="170"><input type="text" name="txt_asking_profit" id="txt_asking_profit" onChange="calculate()" class="text_boxes_numeric"  value="0" style="width:150px"/></td>
				</tr>
				<tr><td style="height:3px ">&nbsp;</td></tr>
				<tr>
					<td width="110">Asking CM %</td>
					<td width="170"><input type="text" name="txt_asking_cm" id="txt_asking_cm" class="text_boxes_numeric" style="width:150px" readonly/></td>
					<td width="110" class="must_entry_caption">No. of Factory Machine </td>
					<td width="170"><input type="text" name="txt_number_machine" id="txt_number_machine" class="text_boxes_numeric" style="width:150px" onChange="caculate_cost_per_minute()" /></td>
				</tr>
                <tr>
					<td width="110" class="must_entry_caption">Monthly CM Expense </td>
					<td width="170"><input type="text" name="txt_monthly_cm" id="txt_monthly_cm"  class="text_boxes_numeric" style="width:150px" onChange="caculate_cost_per_minute()" onDblClick="openmypage_particular_prsnt()" /></td>
                    <td width="110" class="must_entry_caption">Working Hour </td>
					<td width="170"><input type="text" name="txt_working_hour" id="txt_working_hour" class="text_boxes_numeric" style="width:150px"  onChange="caculate_cost_per_minute()"/></td>
				</tr>
                <tr>
					<td width="110" class="must_entry_caption">Cost Per Minute  </td>
					<td width="170" title="Cost Per Minute=(cost per day/(working hour*60))/number of machine"><input type="text" name="txt_cost_per_minute" id="txt_cost_per_minute" class="text_boxes_numeric" style="width:150px" /></td>
                    <td width="110">Actual CM</td>
					<td width="170"><input type="text" name="txt_actual_cm" id="txt_actual_cm" class="text_boxes_numeric" style="width:150px"/></td>
				</tr>
				<tr>
                    <td width="110" class="must_entry_caption">Asking AVG Rate</td>
					<td width="170"><input type="text" name="txt_asking_avg_rate" id="txt_asking_avg_rate" class="text_boxes_numeric" style="width:150px"/></td>
					<td width="110" class="must_entry_caption">Status</td>
					<td width="170"><? echo create_drop_down( "cbo_status", 160, $row_status,"", '', "", 1, "" ); ?></td>				
				</tr>
                <tr>
                    <td width="110">Max Profit %</td>
					<td width="170"><input type="text" name="txt_max_profit" id="txt_max_profit" class="text_boxes_numeric" style="width:150px" /></td>
					<td width="110">Depreciation & Amortization %</td>
					<td width="170"><input type="text" name="txt_depr_amort" id="txt_depr_amort" class="text_boxes_numeric" style="width:150px" /></td>				
				</tr>
                <tr>
                    <td width="110">Interest Expenses %</td>
					<td width="170"><input type="text" name="txt_interest_expn" id="txt_interest_expn" class="text_boxes_numeric" style="width:150px" /></td>
					<td width="110">Income Tax %</td>
					<td width="170"><input type="text" name="txt_income_tax" id="txt_income_tax" class="text_boxes_numeric" style="width:150px" /></td>				
				</tr>
                <tr>
                    <td width="110">Operating Expenses %</td>
					<td width="170"><input type="text" name="txt_operating_expn" id="txt_operating_expn" class="text_boxes_numeric" style="width:150px" /></td>
					 <td width="110">Working Days</td>
					<td width="170"><input type="text" name="txt_working_days" id="txt_working_days" class="text_boxes_numeric" style="width:150px" value="26" disabled="disabled" readonly/></td>
				</tr>
				<tr>
                    <td width="110">Actual Cost RMG %</td>
					<td width="170"><input type="text" name="txt_actual_cost_rmg_per" id="txt_actual_cost_rmg_per" class="text_boxes_numeric" style="width:150px" /></td>
					 <td width="110">Actual Cost Source Tax %</td>
					<td width="170"><input type="text" name="txt_actual_cost_source_tax" id="txt_actual_cost_source_tax" class="text_boxes_numeric" style="width:150px"  /></td>
				</tr> 

				<tr>
				  <td colspan="4" align="center" class="button_container">
                  		<input type="hidden" name="update_id" id="update_id" >
						<? echo load_submit_buttons( $permission, "fnc_standard_cm", 0,0 ,"reset_form('standardcmentry_1','','',1)"); ?>
					</td>				
				</tr>				
			</table>
		</form>	
	</fieldset>
	<div style="width:100%; float:left; margin:auto" align="center">
		<fieldset style="width:720px; margin-top:10px;">
        	<div style="width:720px; margin-top:10px" id="standard_cm_entry_list_view" align="left">
            	<?
				if($db_type==0) $year_field="YEAR(applying_period_date)"; else if($db_type==2) $year_field="to_char(applying_period_date,'YYYY')";
				$comp=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
				$arr=array (0=>$comp,7=>$row_status);
				echo  create_list_view ( "list_view", "Company Name,Year,Period From,Period To,BEP CM %,Asking Profit %,Asking CM %,Status", "150,60,80,80,80,80,80,60","760","220",0, "select company_id, $year_field as year, applying_period_date, applying_period_to_date, bep_cm, asking_profit, asking_cm, id, status_active from  lib_standard_cm_entry where is_deleted=0 order by id DESC", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,0,0,0,0,0,0,status_active", $arr , "company_id,year,applying_period_date,applying_period_to_date,bep_cm,asking_profit,asking_cm,status_active", "requires/standard_cm_entry_controller", 'setFilterGrid("list_view",-1);','0,0,3,3,2,2,2,0' ) ;
				 ?>            
            </div>
		</fieldset>	
	</div>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>


