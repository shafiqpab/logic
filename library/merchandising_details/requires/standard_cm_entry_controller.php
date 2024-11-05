<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

if ($action=="search_list_view")
{
	if($db_type==0) $year_field="YEAR(applying_period_date)"; else if($db_type==2) $year_field="to_char(applying_period_date,'YYYY')";
	$comp=return_library_array( "select company_name,id from lib_company", "id", "company_name"  );
	$arr=array (0=>$comp,7=>$row_status);
	echo  create_list_view ( "list_view", "Company Name,Year,Period From,Period To,BEP CM %,Asking Profit %,Asking CM %,Status", "150,80,80,80,80,80,60","760","220",0, "select  company_id, $year_field as year, applying_period_date, applying_period_to_date, bep_cm, asking_profit, asking_cm, status_active, id from lib_standard_cm_entry where is_deleted=0 order by id DESC", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,0,0,0,0,0,0,status_active", $arr , "company_id,year,applying_period_date,applying_period_to_date,bep_cm,asking_profit,asking_cm,status_active", "requires/standard_cm_entry_controller", 'setFilterGrid("list_view",-1);','00,,3,3,2,2,2,0' ) ;
	
}
if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select--", 0, "" );
	exit();
}
if ($action == "load_location_wise_cost_per_minute_variable_settings")
{
	//echo "select yarn_iss_with_serv_app from variable_order_tracking where company_name = $data and variable_list = 67 and is_deleted = 0 and status_active = 1";
	$cost_per_minute_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name =".$data." and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
	if($cost_per_minute_variable=="" || $cost_per_minute_variable==0) $cost_per_minute_variable=0;else $cost_per_minute_variable=$cost_per_minute_variable;
	echo $cost_per_minute_variable;
	
	exit();
}

if ($action == "location_cost_per_popup")
{
	echo load_html_head_contents("Location Cost Per Entry", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	//echo $save_data;

	?>
	<script>
		var permission = '<? echo $permission; ?>';
       
	   
	    function window_close() {
          //  parent.emailwindow.hide();
		 // alert(4);
		  fnc_save_close();
        }
		
		function fnc_save_close()
		{
		var row_num=$('#location_cost_per_tbl tr').length;
		var working_days=  $('#txt_working_days').val()*1;
		var tot_txt_number_machine=0;var tot_txtmonthlycm=0;var tot_txtworkinghour=0;
		var tot_row=0;var tot_loc=0;
		for(var i=1; i<=row_num; i++)
		{
			
			var txtmonthlycm =  $('#txtmonthlycm_'+i).val()*1;
			//alert(txtmonthlycm+'='+working_days);
			var txtworkinghour = $('#txtworkinghour_'+i).val()*1;
			var txt_number_machine = $('#txtnooffactory_'+i).val()*1;
			var txt_cost_per_day = (txtmonthlycm/working_days);
			var txt_cost_per_minute = txt_cost_per_day/(txtworkinghour*60);
			//alert(txt_cost_per_minute);
			txt_cost_per_minute=txt_cost_per_minute/txt_number_machine;
		//	$('#txtcostperminute_'+i).val(number_format_common(txt_cost_per_minute,5,0,''));
			
			var tot_txt_number_machine=tot_txt_number_machine+txt_number_machine;
			var tot_txtmonthlycm=tot_txtmonthlycm+txtmonthlycm;	
			var tot_txtworkinghour=tot_txtworkinghour+txtworkinghour;
			var tot_loc_chk=i;
			if(txtworkinghour>0)
			{
				var tot_loc=tot_loc_chk;
			}
			
		}
		$("#txtnofmachine").val(tot_txt_number_machine);
		$("#txtcmexp").val(tot_txtmonthlycm);
		$("#txtwhour").val(number_format_common(tot_txtworkinghour/tot_loc,5,0,''));
		parent.emailwindow.hide();
		
	}
	
        function fnc_standard_cm_dtls(operation)
		{
		//freeze_window(operation);
		var row_num=$('#location_cost_per_tbl tr').length;
			//alert(row_num);
		var data_all="";
		for(var i=1; i<=row_num; i++)
		{ 
			
			/*if (form_validation('txtnooffactory_'+i+'*txtmonthlycm_'+i+'*txtworkinghour_'+i,'No Of Factory*Monthly CM*Working Hour')==false)
				{
				return;
				}*/
			data_all+=get_submitted_data_string('txtnooffactory_'+i+'*txtmonthlycm_'+i+'*txtworkinghour_'+i+'*txtcostperminute_'+i+'*txthiddenlocation_'+i+'*txtdtlsid_'+i,"../",i);
		}
		
		//alert(data_all);
		var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('mst_update_id*txt_company_id*txt_applying_period_date*txt_applying_period_to_date',"../")+data_all+'&total_row='+row_num;
		//alert(data);//return;
		

		http.open("POST","standard_cm_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_standard_cm_dtls_Reply_info;

	    }
	function fnc_standard_cm_dtls_Reply_info()
	{
		if(http.readyState == 4)
		{
			//release_freezing(); alert(http.responseText);return;
			var reponse=trim(http.responseText).split('**');
			//show_msg(reponse[0]);
			if((reponse[0]==0 || reponse[0]==1))
			{
			if(reponse[0]==0)
			{
				alert('Save is done successfully');
			}
			else if(reponse[0]==1)
			{
				alert('Update is done successfully');
			}
			else if(reponse[0]==2)
			{
				alert('Not allowed');return;
			}
			fnc_save_close();
			// parent.emailwindow.hide();
				//set_button_status(1, permission, 'fnc_standard_cm_dtls',1);
			}
			
			release_freezing();
		}
	}
	
	
	
	function caculate_cost_per_minute_location(row_id)
	{
		//alert(row_id);
		var row_num=$('#location_cost_per_tbl tr').length;
	//	alert(row_num);
		var working_days=  $('#txt_working_days').val()*1;
		var tot_txt_number_machine=0;var tot_txtmonthlycm=0;var tot_txtworkinghour=0;
		var tot_row=0;var tot_loc=0;
		for(var i=1; i<=row_num; i++)
		{
			
			var txtmonthlycm =  $('#txtmonthlycm_'+i).val()*1;
			//alert(txtmonthlycm+'='+working_days);
			var txtworkinghour = $('#txtworkinghour_'+i).val()*1;
			var txt_number_machine = $('#txtnooffactory_'+i).val()*1;
			var txt_cost_per_day = (txtmonthlycm/working_days);
			var txt_cost_per_minute = txt_cost_per_day/(txtworkinghour*60);
			//alert(txt_cost_per_minute);
			txt_cost_per_minute=txt_cost_per_minute/txt_number_machine;
			$('#txtcostperminute_'+i).val(number_format_common(txt_cost_per_minute,5,0,''));
			
			var tot_txt_number_machine=tot_txt_number_machine+txt_number_machine;
			var tot_txtmonthlycm=tot_txtmonthlycm+txtmonthlycm;	
			var tot_txtworkinghour=tot_txtworkinghour+txtworkinghour;
			var tot_loc_chk=i;
			if(txtworkinghour>0)
			{
				var tot_loc=tot_loc_chk;
			}
			
		}
		// alert(tot_loc);
		 
		
		$("#txtnofmachine").val(tot_txt_number_machine);
		$("#txtcmexp").val(tot_txtmonthlycm);
		$("#txtwhour").val(number_format_common(tot_txtworkinghour/tot_loc,5,0,''));
		
	}

 	</script>

	</head>

	<body>
	<div align="center">
		<? echo load_freeze_divs("../../../", $permission, 1); ?>
		<form name="costPerMinute_1" id="costPerMinute_1">

			<fieldset style="width:600px; margin-top:10px">
				<legend>Cost Per Minute Pop Up</legend>
				<?
				$sql_loc_result=sql_select("select id,location_name from lib_location where company_id=$company_name and status_active =1 and is_deleted=0 order by location_name");
				$nameArray=sql_select( "select a.id,b.id as dtls_id,b.location_id,b.applying_period_date,b.applying_period_to_date,b.monthly_cm_expense,b.no_factory_machine,b.working_hour	,b.cost_per_minute from  lib_standard_cm_entry a,lib_standard_cm_entry_dtls b where a.id=b.mst_id and a.id=$update_id and a.company_id=$company_name" );
				//echo "select a.id,b.id as dtls_id,a.location_id,b.applying_period_date,b.applying_period_to_date,b.monthly_cm_expense,b.no_factory_machine,b.working_hour	,b.cost_per_minute from  lib_standard_cm_entry a,lib_standard_cm_entry_dtls b where a.id=b.mst_id and a.id=$update_id";
				foreach($nameArray as $row)
				{
					$cost_per_minute_arr[$row[csf('location_id')]]['dtls_id']=$row[csf('dtls_id')];
					$cost_per_minute_arr[$row[csf('location_id')]]['applying_period_date']=$row[csf('applying_period_date')];
					$cost_per_minute_arr[$row[csf('location_id')]]['applying_period_to_date']=$row[csf('applying_period_to_date')];
					$cost_per_minute_arr[$row[csf('location_id')]]['monthly_cm_expense']=$row[csf('monthly_cm_expense')];
					$cost_per_minute_arr[$row[csf('location_id')]]['no_factory_machine']=$row[csf('no_factory_machine')];
					$cost_per_minute_arr[$row[csf('location_id')]]['working_hour']=$row[csf('working_hour')];
					$cost_per_minute_arr[$row[csf('location_id')]]['cost_per_minute']=$row[csf('cost_per_minute')];
					
				}
				//print_r($cost_per_minute_arr);
				//echo "select a.id,b.id as dtls_id,a.location_id,b.applying_period_date,b.applying_period_to_date,b.monthly_cm_expense,b.no_factory_machine,b.working_hour	,b.cost_per_minute from  lib_standard_cm_entry a,lib_standard_cm_entry_dtls b where a.id=b.mst_id and a.id=$update_id";
				//$sql_loc_result=sql_select("select id,location_name from lib_location where company_id=$company_name and status_active =1 and is_deleted=0 order by location_name");
				 ?>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600" id="tbl_list">
					<thead>
						<th width="20">SL</th>
						<th width="100">Location</th>
						<th width="80">No. of Factory Machine</th>
						<th width="80">Monthly CM Expense</th>
                        <th width="80">Working Hour </th>
						<th width="80">Cost Per Minute</th>
						
						<input type="hidden" name="mst_update_id" id="mst_update_id" class="text_boxes" value="<? echo $update_id;?>">
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $company_name;?>">
                        <input type="hidden" name="txt_working_days" id="txt_working_days" class="text_boxes" value="<? echo $txt_working_days;?>">
                         <input type="hidden" name="txt_applying_period_date" id="txt_applying_period_date" class="text_boxes" value="<? echo $txt_applying_period_date;?>">
                        <input type="hidden" name="txt_applying_period_to_date" id="txt_applying_period_to_date" class="text_boxes" value="<? echo $txt_applying_period_to_date;?>">
                          
						
					</thead>
					<tbody id="location_cost_per_tbl">
                    <?
					$k=0;$tot_row=0;$tot_no_factory_machine=$tot_monthly_cm_expense=$tot_working_hour=0;
                    foreach($sql_loc_result as $row)
					{
						$dtls_id=$cost_per_minute_arr[$row[csf('id')]]['dtls_id'];
						//echo $dtls_id.'kkjjj';
						//$applying_period_date=$cost_per_minute_arr[$row[csf('id')]]['applying_period_date'];
						//$applying_period_to_date=$cost_per_minute_arr[$row[csf('id')]]['applying_period_to_date'];
						$monthly_cm_expense=$cost_per_minute_arr[$row[csf('id')]]['monthly_cm_expense'];
						$no_factory_machine=$cost_per_minute_arr[$row[csf('id')]]['no_factory_machine'];
						$working_hour=$cost_per_minute_arr[$row[csf('id')]]['working_hour'];
						$cost_per_minute=$cost_per_minute_arr[$row[csf('id')]]['cost_per_minute'];
						$k++;
						$tot_no_factory_machine+=$no_factory_machine;
						$tot_monthly_cm_expense+=$monthly_cm_expense;
						$tot_working_hour+=$working_hour;
						if($working_hour>0)
						{
							$tot_row++;
						}
					?>
						<tr id="tr_<? echo $k;?>">
							<td id="slTd_<? echo $k;?>" width="20"><? echo $k;?></td>
							<td>
								<input type="text" name="txtlocation[]" id="txtlocation_<? echo $k;?>" class="text_boxes" value="<? echo $row[csf('location_name')]; ?>" style="width:100px;"/>
                                <input type="hidden" name="txthiddenlocation[]" id="txthiddenlocation_<? echo $k;?>" class="text_boxes" value="<? echo $row[csf('id')]; ?>">
							</td>
							<td>
								<input type="text" name="txtnooffactory[]" id="txtnooffactory_<? echo $k;?>" class="text_boxes_numeric" style="width:80px;" onChange="caculate_cost_per_minute_location(<? echo $k;?>)"  value="<? echo $no_factory_machine;?>" />
							</td>
							<td>
								<input type="text" name="txtmonthlycm[]" id="txtmonthlycm_<? echo $k;?>"   class="text_boxes_numeric" style="width:80px" onChange="caculate_cost_per_minute_location(<? echo $k;?>)"  value="<? echo $monthly_cm_expense;?>" />	
							</td>
                            <td>
								<input type="text" name="txtworkinghour[]" id="txtworkinghour_<? echo $k;?>"   class="text_boxes_numeric" style="width:80px" onChange="caculate_cost_per_minute_location(<? echo $k;?>)"  value="<? echo $working_hour;?>" />	
							</td>
                            <td>
								<input type="text" name="txtcostperminute[]" id="txtcostperminute_<? echo $k;?>"   class="text_boxes_numeric" style="width:80px" value="<? echo $cost_per_minute;?>" />	
                                <input type="hidden" name="txtdtlsid[]" id="txtdtlsid_<? echo $k;?>"  value="<? echo $dtls_id;?>" >
							</td>
                            

						</tr>
                        <?
						
					}
						?>

					</tbody>
                    <tr>
                    <td colspan="2"> &nbsp; </td>
                     <td><input type="text" class="text_boxes_numeric" name="txtnofmachine" id="txtnofmachine" style="width:80px;"  value="<? echo $tot_working_hour;?>" readonly > </td>
                     <td>  <input type="text" class="text_boxes_numeric" name="txtcmexp" id="txtcmexp"  style="width:80px;" value="<? echo $tot_monthly_cm_expense;?>" readonly>  </td>
                     <td>  <input type="text"class="text_boxes_numeric"  name="txtwhour" id="txtwhour"  style="width:80px;" value="<? echo number_format($tot_working_hour/$tot_row,4,'.','');?>" readonly></td>
                     <td><input type="text" class="text_boxes_numeric" name="txtcpm" id="txtcpm"  style="width:80px;" value="" readonly></td>
                    </tr>
					
                    <tr>
                      <td colspan="6" align="center" class="">
                           
                            <? 
							//echo $k;
                                if(count($nameArray)>0)
								{
									echo load_submit_buttons( $permission, "fnc_standard_cm_dtls", 1,0 ,"reset_form('costPerMinute_1','','','','')",1) ; 
								}
								else
								{
									echo load_submit_buttons( $permission, "fnc_standard_cm_dtls", 0,0 ,"reset_form('costPerMinute_1','','','','')",1) ; 
								}
                            ?>
                        </td>				
                    </tr>	
                
					</table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">

						<tr>
							<td colspan="5" align="center">

								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="window_close();" style="width:80px"/>

							</td>
						</tr>
					</table>
				<? 
				?>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script> 
	set_all_onclick();
	//fnc_save_close();

	</script>

	</html>
	<?
}

if ($action == "particular_prsnt_popup")
{
	echo load_html_head_contents("Location Cost Per Entry", "../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	//echo $save_data;

	?>
	<script>
		var permission = '<? echo $permission; ?>';
        function window_close() {
            parent.emailwindow.hide();
        }
        function fnc_standard_cm_dtls(operation)
		{
		//freeze_window(operation);
		var row_num=$('#location_cost_per_tbl tr').length;
			//alert(row_num);
		var data_all="";
		for(var i=1; i<=row_num; i++)
		{ 
			
			/*if (form_validation('txtnooffactory_'+i+'*txtmonthlycm_'+i+'*txtworkinghour_'+i,'No Of Factory*Monthly CM*Working Hour')==false)
				{
				return;
				}*/
			data_all+=get_submitted_data_string('txthiddenparticular_'+i+'*txtparticular_value_'+i+'*txtdtlsid_'+i,"../",i);
		}
		
		//alert(data_all);
		var data="action=save_update_delete_dtls_min&operation="+operation+get_submitted_data_string('mst_update_id*txt_company_id',"../")+data_all+'&total_row='+row_num;
		// alert(data);return;
		

		http.open("POST","standard_cm_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_standard_cm_dtls_Reply_info;

	    }
		function fnc_standard_cm_dtls_Reply_info()
		{
			if(http.readyState == 4)
			{
				//release_freezing(); alert(http.responseText);return;
				var reponse=trim(http.responseText).split('**');
				//show_msg(reponse[0]);
				if((reponse[0]==0 || reponse[0]==1))
				{
				if(reponse[0]==0)
				{
					alert('Save is done successfully');
				}
				else if(reponse[0]==1)
				{
					alert('Update is done successfully');
				}
				else if(reponse[0]==2)
				{
					alert('Not allowed');return;
				}
				 parent.emailwindow.hide();
					//set_button_status(1, permission, 'fnc_standard_cm_dtls',1);
				}
				
				release_freezing();
			}
		}
	
		function check_percent(row_id,val)
		{
			var total = $("#txtparticular_value_1").val()*1 + $("#txtparticular_value_2").val()*1 + $("#txtparticular_value_3").val()*1;
			
			/*$("input[name=txtparticular_value]").each(function(index, element) {
		        total += ( $(this).val() )*1;
		    });*/

			// alert(total);	
			if(total*1>100)		
			{
				alert('Total percent will not over 100');
				$("#txtparticular_value_"+row_id).val(0);
				return false;
			}
		}

 	</script>

	</head>

	<body>
	<div align="center">
		<? echo load_freeze_divs("../../../", $permission, 1); ?>
		<form name="costPerMinute_1" id="costPerMinute_1">

			<fieldset style="width:270px; margin-top:10px">
				<legend>Cost Per Minute Pop Up</legend>
				<?
				$particular_array = array(
					20=>'Cutting',
					30=>'Sewing',
					40=>'Finishing'
				);
				$sql="SELECT a.id,b.id as dtls_id,b.particular,b.particular_value from lib_standard_cm_entry a,lib_standard_cm_entry_min_dtls b where a.id=b.mst_id and a.id=$update_id and a.company_id=$company_name";
				// echo $sql;
				$nameArray = sql_select($sql);
				foreach($nameArray as $row)
				{
					$cost_per_minute_arr[$row[csf('particular')]]['dtls_id']=$row[csf('dtls_id')];
					$cost_per_minute_arr[$row[csf('particular')]]['particular']=$row[csf('particular')];
					$cost_per_minute_arr[$row[csf('particular')]]['particular_value']=$row[csf('particular_value')];
					
				}
				 ?>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="270" id="tbl_list">
						<thead>
							<th width="50">SL</th>
							<th width="110">Particulas</th>
							<th width="110">Percent</th>
							
							<input type="hidden" name="mst_update_id" id="mst_update_id" class="text_boxes" value="<? echo $update_id;?>">
	                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $company_name;?>"> 
						</thead>
					<tbody id="location_cost_per_tbl">
                    <?
					$k=1;
                    foreach($particular_array as $key=>$row)
					{
						$dtls_id=$cost_per_minute_arr[$key]['dtls_id'];
						$particular_value=$cost_per_minute_arr[$key]['particular_value'];
					?>
						<tr id="tr_<? echo $k;?>">
							<td id="slTd_<? echo $k;?>"><? echo $k;?></td>
							<td>
								<p><? echo $row; ?></p>
                                <input type="hidden" name="txthiddenparticular[]" id="txthiddenparticular_<? echo $k;?>" class="text_boxes" value="<? echo $key; ?>">
                                <input type="hidden" name="txtdtlsid[]" id="txtdtlsid_<? echo $k;?>"  value="<? echo $dtls_id;?>" >
							</td>
							<td>
								<input type="text" name="txtparticular_value[]" id="txtparticular_value_<? echo $k;?>" class="text_boxes_numeric" value="<? echo $particular_value;?>" onKeyUp="check_percent(<?=$k;?>,this.value)" />
							</td> 
						</tr>
                        <?
						$k++;
					}
						?>

					</tbody>
					
                    <tr>
                      <td colspan="3" align="center" class="">
                           
                            <? 
                                if(count($nameArray)>0)
								{
									echo load_submit_buttons( $permission, "fnc_standard_cm_dtls", 1,0 ,"reset_form('costPerMinute_1','','','','')",1) ; 
								}
								else
								{
									echo load_submit_buttons( $permission, "fnc_standard_cm_dtls", 0,0 ,"reset_form('costPerMinute_1','','','','')",1) ; 
								}
                            ?>
                        </td>				
                    </tr>	
                
					</table>
					<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="270">

						<tr>
							<td colspan="3" align="center">

								<input type="button" name="close" class="formbutton" value="Close" id="main_close"
								onClick="window_close();" style="width:80px"/>

							</td>
						</tr>
					</table>
				<? 
				?>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script> 
	set_all_onclick();

	</script>

	</html>
	<?
}

if ($action=="check_capacity_calculation")
{
	$data_date=explode("_",$data);
	$company_id=$data_date[0];
	$cal_dates=$data_date[1];
	//$location_id=$data_date[2];
	//if($location_id>0) $location_cond="and a.location_id=$location_id ";else $location_cond="";
	/*if($db_type==0)
	{
		$start_date=change_date_format($cal_dates,'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$end_date=change_date_format($cal_dates,'','-',1);
    }
	$date_cond="and b.date_calc between '$start_date' and  '$end_date'";*/
	$year = date('Y',strtotime($cal_dates));
	 $month_id = date('m',strtotime($cal_dates));
	 $monthid=ltrim($month_id,'0');
	// echo $monthid.'dd'.$year;
	 	//echo $month_id.'dd';;
	$sql="select min(c.working_day) as working_day, min(b.date_calc) as date_calc from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where a.id=b.mst_id and b.mst_id=c.mst_id and a.id=c.mst_id and b.month_id=c.month_id and a.comapny_id in($company_id) and a.year=$year and  c.month_id=$monthid   ";
	 //echo $sql;
	$sql_data_calc=sql_select($sql);
	if(count($sql_data_calc)>0)
	{
		$working_day=$sql_data_calc[0][csf('working_day')];
		echo $working_day;
	}
	else
	{
	 	echo '';
	}
	
	
	
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select company_id,location_id,applying_period_date,applying_period_to_date,bep_cm,asking_profit,asking_cm,monthly_cm_expense,no_factory_machine,working_hour, status_active,	cost_per_minute,asking_avg_rate,id,actual_cm,max_profit,depreciation_amorti,interest_expense,income_tax,operating_expn,actual_cost_source_tax_per,actual_cost_rmg_per from  lib_standard_cm_entry where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";   
		//echo "load_drop_down('requires/standard_cm_entry_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location_td' );\n"; 
		//echo "document.getElementById('cbo_location_id').value = '".($inf[csf("location_id")])."';\n";   
		echo "document.getElementById('txt_applying_period_date').value  = '".change_date_format(($inf[csf("applying_period_date")]),'dd-mm-yyyy','-')."';\n"; 
		echo "document.getElementById('txt_applying_period_to_date').value  = '".change_date_format(($inf[csf("applying_period_to_date")]),'dd-mm-yyyy','-')."';\n";
		echo "document.getElementById('txt_bep_cm').value  = '".($inf[csf("bep_cm")])."';\n"; 
		echo "document.getElementById('txt_asking_cm').value  = '".($inf[csf("asking_cm")])."';\n"; 
		echo "document.getElementById('txt_asking_profit').value  = '".($inf[csf("asking_profit")])."';\n"; 

		echo "document.getElementById('txt_monthly_cm').value  = '".($inf[csf("monthly_cm_expense")])."';\n"; 
		echo "document.getElementById('txt_actual_cost_rmg_per').value  = '".($inf[csf("actual_cost_rmg_per")])."';\n"; 
		echo "document.getElementById('txt_actual_cost_source_tax').value  = '".($inf[csf("actual_cost_source_tax_per")])."';\n"; 

		echo "document.getElementById('txt_number_machine').value  = '".($inf[csf("no_factory_machine")])."';\n"; 
		echo "document.getElementById('txt_working_hour').value  = '".($inf[csf("working_hour")])."';\n"; 
		
		echo "document.getElementById('txt_asking_avg_rate').value  = '".($inf[csf("asking_avg_rate")])."';\n";
		
		echo "document.getElementById('txt_actual_cm').value  = '".($inf[csf("actual_cm")])."';\n";
		echo "document.getElementById('txt_max_profit').value  = '".($inf[csf("max_profit")])."';\n";
		
		echo "location_cost_per_minute(".$inf[csf("company_id")].");\n";
		echo "calculate_date()\n";
		echo "caculate_cost_per_minute()\n";
		echo "document.getElementById('txt_cost_per_minute').value  = '".($inf[csf("cost_per_minute")])."';\n"; 
		echo "document.getElementById('txt_depr_amort').value  = '".($inf[csf("depreciation_amorti")])."';\n";
		echo "document.getElementById('txt_interest_expn').value  = '".($inf[csf("interest_expense")])."';\n";
		echo "document.getElementById('txt_income_tax').value  = '".($inf[csf("income_tax")])."';\n";
		echo "document.getElementById('txt_operating_expn').value  = '".($inf[csf("operating_expn")])."';\n";
		
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_standard_cm',1);\n";  

	}
}

if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_applying_period_date=str_replace("'","",$txt_applying_period_date);
	$txt_applying_period_to_date=str_replace("'","",$txt_applying_period_to_date);
	//$location_id=str_replace("'","",$cbo_location_id);
	if($db_type==0)
	{
		$txt_applying_period_date=change_date_format($txt_applying_period_date,"yyyy-mm-dd","-");
		$txt_applying_period_to_date=change_date_format($txt_applying_period_to_date,"yyyy-mm-dd","-");
	}
	else
	{
		$txt_applying_period_date=change_date_format($txt_applying_period_date, "d-M-y", "-",1);
		$txt_applying_period_to_date=change_date_format($txt_applying_period_to_date, "d-M-y", "-",1);
	}
	//if($location_id>0) $location_cond="and location_id=$location_id";else $location_cond="";
	if ($operation==0)  // Insert Here	
	{
		
		if (is_duplicate_field( "id", "lib_standard_cm_entry", "company_id=$cbo_company_name and applying_period_date='$txt_applying_period_date' and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
        else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$txt_asking_profit=str_replace("'","",trim($txt_asking_profit));
			if($txt_asking_profit=="") $txt_asking_profit=0;else $txt_asking_profit=$txt_asking_profit;
			$id=return_next_id( "id", "lib_standard_cm_entry", 1 ) ; //txt_max_profit 	status_active,is_deleted,inserted_by,insert_date,updated_by,update_date,is_locked
			$field_array="id,company_id,applying_period_date,applying_period_to_date,bep_cm,asking_cm,asking_profit,monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,asking_avg_rate,status_active,is_deleted,inserted_by,insert_date,is_locked,actual_cm,max_profit,depreciation_amorti,interest_expense,income_tax,operating_expn,actual_cost_rmg_per,actual_cost_source_tax_per";
			$data_array="(".$id.",".trim($cbo_company_name).",'".trim($txt_applying_period_date)."','".trim($txt_applying_period_to_date)."',".trim($txt_bep_cm).",".trim($txt_asking_cm).",".$txt_asking_profit.",".trim($txt_monthly_cm).",".trim($txt_number_machine).",".trim($txt_working_hour).",".trim($txt_cost_per_minute).",".trim($txt_asking_avg_rate).",".trim($cbo_status).",'0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',".trim($txt_actual_cm).",".trim($txt_max_profit).",".trim($txt_depr_amort).",".trim($txt_interest_expn).",".trim($txt_income_tax).",".trim($txt_operating_expn).",".trim($txt_actual_cost_rmg_per).",".trim($txt_actual_cost_source_tax).")";
			$rID=sql_insert("lib_standard_cm_entry",$field_array,$data_array,1);
			// echo "11**0".$rID; die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
			if($rID )
			    {
					oci_commit($con);   
					echo "0**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	
	else if ($operation==1)   // Update Here
	{
		$duplicate_id=return_field_value("id","lib_standard_cm_entry","company_id=$cbo_company_name and applying_period_date='$txt_applying_period_date' and status_active=1 and is_deleted=0 ");
		
		if($duplicate_id=="")  // Duplicate
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$txt_asking_profit=str_replace("'","",trim($txt_asking_profit));
			if($txt_asking_profit=="") $txt_asking_profit=0;else $txt_asking_profit=$txt_asking_profit;
			$id=return_next_id( "id", "lib_standard_cm_entry", 1 ) ; // 	status_active,is_deleted,inserted_by,insert_date,updated_by,update_date,is_locked
			$field_array="id,company_id,applying_period_date,applying_period_to_date,bep_cm,asking_cm,asking_profit,monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,asking_avg_rate,status_active,is_deleted,inserted_by,insert_date,is_locked,actual_cm,max_profit,depreciation_amorti,interest_expense,income_tax,operating_expn,actual_cost_rmg_per,actual_cost_source_tax_per";
			$data_array="(".$id.",".trim($cbo_company_name).",'".trim($txt_applying_period_date)."','".trim($txt_applying_period_to_date)."',".trim($txt_bep_cm).",".trim($txt_asking_cm).",".$txt_asking_profit.",".trim($txt_monthly_cm).",".trim($txt_number_machine).",".trim($txt_working_hour).",".trim($txt_cost_per_minute).",".trim($txt_asking_avg_rate).",".trim($cbo_status).",'0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',".trim($txt_actual_cm).",".trim($txt_max_profit).",".trim($txt_depr_amort).",".trim($txt_interest_expn).",".trim($txt_income_tax).",".trim($txt_operating_expn).",".trim($txt_actual_cost_rmg_per).",".trim($txt_actual_cost_source_tax).")";
			$rID=sql_insert("lib_standard_cm_entry",$field_array,$data_array,1);
			//echo $rID; die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
			   if($rID )
			    {
					oci_commit($con);   
					echo "0**".$rID;
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
		else
		{
			$update_id=str_replace("'","",$update_id);
			
			if($duplicate_id!=$update_id)
			{
				echo "11**0"; die;
			}
			else
			{
				
				$con = connect();
			
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				$txt_asking_profit=str_replace("'","",trim($txt_asking_profit));
			if($txt_asking_profit=="") $txt_asking_profit=0;else $txt_asking_profit=$txt_asking_profit;
				//str_replace("'","",$wo_id)
			
				$field_array="company_id*applying_period_date*applying_period_to_date*bep_cm*asking_cm*asking_profit*monthly_cm_expense*no_factory_machine*working_hour*cost_per_minute*asking_avg_rate*status_active*updated_by*update_date*actual_cm*max_profit*depreciation_amorti*interest_expense*income_tax*operating_expn*actual_cost_rmg_per*actual_cost_source_tax_per";
				$data_array="".$cbo_company_name."*'".$txt_applying_period_date."'*'".$txt_applying_period_to_date."'*".$txt_bep_cm."*".$txt_asking_cm."*".$txt_asking_profit."*".trim($txt_monthly_cm)."*".trim($txt_number_machine)."*".trim($txt_working_hour)."*".trim($txt_cost_per_minute)."*".trim($txt_asking_avg_rate)."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".trim($txt_actual_cm)."*".trim($txt_max_profit)."*".trim($txt_depr_amort)."*".trim($txt_interest_expn)."*".trim($txt_income_tax)."*".trim($txt_operating_expn)."*".trim($txt_actual_cost_rmg_per)."*".trim($txt_actual_cost_source_tax)."";
				
				 //echo "10**".$field_array.'_'.$data_array;die;
				
				$rID2=sql_update("lib_standard_cm_entry",$field_array,$data_array,"id","".$update_id."",1);
				//echo $rID; die;
				if($db_type==0)
				{
					if( $rID2 ){
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($rID2)
					{
					    oci_commit($con);  
					    echo "1**".$rID;
					}
				else{
						oci_rollback($con); 
						echo "10**".$rID;
					}
				}
			
				disconnect($con);
			
			}
		}
		
	}
	else if ($operation==2)    // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("lib_standard_cm_entry",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		   if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_dtls")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_applying_period_date=str_replace("'","",$txt_applying_period_date);
	$txt_applying_period_to_date=str_replace("'","",$txt_applying_period_to_date);
	//$location_id=str_replace("'","",$cbo_location_id);
	if($db_type==0)
	{
		$txt_applying_period_date=change_date_format($txt_applying_period_date,"yyyy-mm-dd","-");
		$txt_applying_period_to_date=change_date_format($txt_applying_period_to_date,"yyyy-mm-dd","-");
	}
	else
	{
		$txt_applying_period_date=change_date_format($txt_applying_period_date, "d-M-y", "-",1);
		$txt_applying_period_to_date=change_date_format($txt_applying_period_to_date, "d-M-y", "-",1);
	}
	//if($location_id>0) $location_cond="and location_id=$location_id";else $location_cond="";
	if ($operation==0)  // Insert Here	
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id_dtls=return_next_id( "id", "lib_standard_cm_entry_dtls", 1 ) ; //txt_max_profit 	status_active,is_deleted,inserted_by,insert_date,updated_by,update_date,is_locked
			$field_array_dtls="id,mst_id,location_id,applying_period_date,applying_period_to_date,monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,status_active,is_deleted,is_locked,inserted_by,insert_date";

			for ($i = 1; $i <= $total_row; $i++) {

			$txtnooffactory = "txtnooffactory_" . $i;
			$txtmonthlycm = "txtmonthlycm_" . $i;
			$txtworkinghour = "txtworkinghour_" . $i;
			$txtcostperminute = "txtcostperminute_" . $i;
			$txthiddenlocation = "txthiddenlocation_" . $i;
			$txtdtlsid = "txtdtlsid_" . $i;
		if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . "," . $$txthiddenlocation . ",'" . $txt_applying_period_date . "','" . $txt_applying_period_to_date . "'," . $$txtmonthlycm . "," . $$txtnooffactory . "," . $$txtworkinghour . "," . $$txtcostperminute . ",1,0,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			$id_dtls = $id_dtls + 1;
			
		}
			 $flag=1;
			$rID=sql_insert("lib_standard_cm_entry_dtls",$field_array_dtls,$data_array_dtls,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0; 
		//echo "10**insert into lib_standard_cm_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			//echo "10**".$rID."**".$flag; die;
			if($db_type==0)
			{
				if($flag==1){
					mysql_query("COMMIT");  
					 echo "0**".$rID."**".str_replace("'",'',$mst_update_id);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**".str_replace("'",'',$mst_update_id);
				}
			}
			
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
			    {
					oci_commit($con);   
					echo "0**".$rID."**".str_replace("'",'',$mst_update_id);
				}
				else{
					oci_rollback($con);
					echo "10**".$rID."**".str_replace("'",'',$$mst_update_id);
				}
			}
			disconnect($con);
			die;
	}
	else if ($operation==1)   // Update Here
	{
			$mst_update_id=str_replace("'","",$mst_update_id);
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
			$id_dtls=return_next_id( "id", "lib_standard_cm_entry_dtls", 1 ) ;
			
			$field_array_dtls="id,mst_id,location_id,applying_period_date,applying_period_to_date,monthly_cm_expense,no_factory_machine,working_hour,cost_per_minute,status_active,is_deleted,is_locked,inserted_by,insert_date";
			$field_array_dtls_update="mst_id*location_id*applying_period_date*applying_period_to_date*monthly_cm_expense*no_factory_machine*working_hour*cost_per_minute*updated_by*update_date";
				
			for ($i = 1; $i <= $total_row; $i++) {
			$txtnooffactory = "txtnooffactory_" . $i;
			$txtmonthlycm = "txtmonthlycm_" . $i;
			$txtworkinghour = "txtworkinghour_" . $i;
			$txtcostperminute = "txtcostperminute_" . $i;
			$txthiddenlocation = "txthiddenlocation_" . $i;
			$updateIdDtls = "txtdtlsid_" . $i;
			
				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("" . $mst_update_id . "*" . $$txthiddenlocation . "*'" . $txt_applying_period_date . "'*'" . $txt_applying_period_to_date . "'*" . $$txtmonthlycm . "*" . $$txtnooffactory . "*" . $$txtworkinghour . "*" . $$txtcostperminute . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					
					
				}
				else
				{
					if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . "," . $$txthiddenlocation . ",'" . $txt_applying_period_date . "','" . $txt_applying_period_to_date . "'," . $$txtmonthlycm . "," . $$txtnooffactory . "," . $$txtworkinghour . "," . $$txtcostperminute . ",1,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$id_dtls = $id_dtls + 1;
				}
			
		}
				 //echo "10**".$field_array.'_'.$data_array;die;
				$flag=1;
				if($data_array_dtls_update!="")
				{
					$rID=execute_query(bulk_update_sql_statement( "lib_standard_cm_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
					//echo "10**".bulk_update_sql_statement( "lib_standard_cm_entry_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
					if($rID==1 && $flag==1) $flag=1; else $flag=0; 
				}
				if($data_array_dtls!="")
				{
					$rID2=sql_insert("lib_standard_cm_entry_dtls",$field_array_dtls,$data_array_dtls,0);
					if($rID2==1 && $flag==1) $flag=1; else $flag=0; 
					
				}
		
			//	echo "10**".$rID."**".$rID2."**".$flag; die;
				if($db_type==0)
				{
					if( $flag==1 ){
						mysql_query("COMMIT");  
						echo "1**".$rID."**".$mst_update_id;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID."**".$mst_update_id;;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
					{
					    oci_commit($con);  
					    echo "1**".$rID."**".$mst_update_id;;
					}
				else{
						oci_rollback($con); 
						echo "10**".$rID."**".$mst_update_id;;
					}
				}
			
				disconnect($con);
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo "10**".$rID;disconnect($con);die;
	//	$field_array="updated_by*update_date*status_active*is_deleted";
		//$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//$rID=sql_delete("lib_standard_cm_entry_dtls",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		   if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
	}
}

if ($action=="save_update_delete_dtls_min")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 	
	
	if ($operation==0)  // Insert Here	
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id_dtls=return_next_id( "id", "lib_standard_cm_entry_min_dtls", 1 ) ; //txt_max_profit 	status_active,is_deleted,inserted_by,insert_date,updated_by,update_date,is_locked
		$field_array_dtls="id,mst_id,particular,particular_value,status_active,is_deleted,inserted_by,insert_date";

		for ($i = 1; $i <= $total_row; $i++) 
		{

			$txthiddenparticular = "txthiddenparticular_" . $i;
			$txtparticular_value = "txtparticular_value_" . $i;
			$txtdtlsid = "txtdtlsid_" . $i;
			if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . "," . $$txthiddenparticular . "," . $$txtparticular_value . ",1,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$id_dtls = $id_dtls + 1;
			
		}
		$flag=1;
		$rID=sql_insert("lib_standard_cm_entry_min_dtls",$field_array_dtls,$data_array_dtls,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0; 
		// echo "10**insert into lib_standard_cm_entry_min_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo "10**".$rID."**".$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				 echo "0**".$rID."**".str_replace("'",'',$mst_update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".str_replace("'",'',$mst_update_id);
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
		    {
				oci_commit($con);   
				echo "0**".$rID."**".str_replace("'",'',$mst_update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID."**".str_replace("'",'',$$mst_update_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
			$mst_update_id=str_replace("'","",$mst_update_id);
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id_dtls=return_next_id( "id", "lib_standard_cm_entry_min_dtls", 1 ) ;
			
			$field_array_dtls="id,mst_id,particular,particular_value,status_active,is_deleted,inserted_by,insert_date";
			$field_array_dtls_update="mst_id*particular*particular_value*updated_by*update_date";
				
			for ($i = 1; $i <= $total_row; $i++) {
			$txthiddenparticular = "txthiddenparticular_" . $i;
			$txtparticular_value = "txtparticular_value_" . $i;
			$updateIdDtls = "txtdtlsid_" . $i;
			
				if(str_replace("'","",$$updateIdDtls)!="")
				{
					$id_arr[] = str_replace("'", '', $$updateIdDtls);
					$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ("" . $mst_update_id . "*" . $$txthiddenparticular . "*" . $$txtparticular_value . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					
					
				}
				else
				{
					if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . "," . $$txthiddenparticular . "," . $$txtparticular_value . ",1,0," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				$id_dtls = $id_dtls + 1;
				}
			
		}
				 //echo "10**".$field_array.'_'.$data_array;die;
				$flag=1;
				if($data_array_dtls_update!="")
				{
					$rID=execute_query(bulk_update_sql_statement( "lib_standard_cm_entry_min_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr ));
					//echo "10**".bulk_update_sql_statement( "lib_standard_cm_entry_min_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
					if($rID==1 && $flag==1) $flag=1; else $flag=0; 
				}
				if($data_array_dtls!="")
				{
					$rID2=sql_insert("lib_standard_cm_entry_min_dtls",$field_array_dtls,$data_array_dtls,0);
					if($rID2==1 && $flag==1) $flag=1; else $flag=0; 
					
				}
		
			//	echo "10**".$rID."**".$rID2."**".$flag; die;
				if($db_type==0)
				{
					if( $flag==1 ){
						mysql_query("COMMIT");  
						echo "1**".$rID."**".$mst_update_id;
					}
					else{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID."**".$mst_update_id;;
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
					{
					    oci_commit($con);  
					    echo "1**".$rID."**".$mst_update_id;;
					}
				else{
						oci_rollback($con); 
						echo "10**".$rID."**".$mst_update_id;;
					}
				}
			
				disconnect($con);
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo "10**".$rID;disconnect($con);die;
	//	$field_array="updated_by*update_date*status_active*is_deleted";
		//$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//$rID=sql_delete("lib_standard_cm_entry_min_dtls",$field_array,$data_array,"id","".$update_id."",1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		   if($rID )
			    {
					oci_commit($con);   
					echo "2**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
	}
}

?>