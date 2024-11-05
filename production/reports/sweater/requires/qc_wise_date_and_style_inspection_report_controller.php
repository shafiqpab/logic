<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );



if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' ","id,location_name", 1, "-- Select --", $selected, "" );     	 
	exit();
}

if ($action=="print_report_button_setting")
{
	
$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
	echo $print_report_format; 	
} 

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";


$new_conn=integration_params(2);

if ($action=="operator_popup")
{
	echo load_html_head_contents("Operator Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
 ?> 

	<script>
	
		function js_set_value(str)
		{
			$("#hidden_emp_number").val(str);
			parent.emailwindow.hide(); 
		}
	
    </script>

 </head>

 <body>
 <div align="center" style="width:1020px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:1020px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table" align="center">
                <thead>
	                <th width="160" align="center">Company</th>
	                <th width="135" align="center">Location</th>
	                <th width="135" align="center">Division</th>
	                <th width="135" align="center">Department</th>
	                <th width="135" align="center">Section</th>
	            	<th width="135" align="center">Employee Code</th>
	                <th width="90" align="center"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /> <input type="hidden" id="hidden_emp_number"  /></th>           
	            </thead>
                <tr class="general">
                    <td align="center">
                    	<?
                    		$sql_com="select 
											id,
											company_name
										from 
											lib_company comp
						 				where 
											status_active =1 and 
											is_deleted=0 											 
										order by company_name";
							
								
							echo create_drop_down( "cbo_company_name",
													160, 
													$sql_com,
													"id,company_name", 
													1, 
													"--- Select Company ---", 
													$selected, 
													"load_drop_down( 	 
																	'qi_wise_report_controller', this.value, 
																	'load_drop_down_location_hrm', 
																	'location_td_hrm');",
													"",
													"",
													"",
													"",
													"",
													"",
													$new_conn );  
						?>       
                    </td>
                    <td id="location_td_hrm">
					 <? 
						echo create_drop_down( "cbo_location_name", 135, $blank_array,"", 1, "-- Select Location --", $selected );
                    ?>
	                </td>
	                 <td id="division_td_hrm">
						 <? 
	                    	echo create_drop_down( "cbo_division_name", 135,$blank_array ,"", 1, "-- Select Division --", $selected );
	                    ?>
	                </td> 
	                <td id="department_td_hrm">
						<? 
							echo create_drop_down( "cbo_dept_name", 135,$blank_array ,"", 1, "-- Select Department --", $selected );
	                    ?>
	                </td>   
	                <td id="section_td_hrm">
						<? 
							echo create_drop_down( "cbo_section_name", 135,$blank_array ,"", 1, "-- Select Section --", $selected );
	                    ?>
	                </td>
	           
	                <td>
						<input type="text" id="src_emp_code" name="src_emp_code" class="text_boxes" style="width:135px;" >
	                </td> 
	                <td>
	                	<input type="button" 
	                	name="btn_show" 
	                	class="formbutton" 
	                	value="Show" 
	                	onClick="show_list_view ( 
	                								document.getElementById('cbo_company_name').value+'_'+
	                								document.getElementById('cbo_location_name').value+'_'+
	                								document.getElementById('cbo_division_name').value+'_'+
	                								document.getElementById('cbo_dept_name').value+'_'+
	                								document.getElementById('cbo_section_name').value+'_'+
	                								document.getElementById('src_emp_code').value, 'create_emp_search_list_view', 
	                								'search_div', 
	                								'qi_wise_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
	                </td>
	            </tr> 
           </table>
           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
		</fieldset>
	</form>
 </div>
 </body>           
 <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
 </html>
 <?
}

if($action=="create_emp_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$location = $ex_data[1];
	$division = $ex_data[2];
	$department = $ex_data[3];
	$section = $ex_data[4];
	$emp_code = $ex_data[6];


 	//$sql_cond="";
	if( $company!=0 )  $company=" and company_id=$company"; else  $company="";
	if( $location!=0 )  $location=" and location_id=$location"; else  $location="";
	if( $division!=0 )  $division=" and division_id=$division"; else  $division="";
	if( $department!=0 )  $department=" and department_id=$department"; else  $department="";
	if( $section!=0 )  $section=" and section_id=$section"; else  $section="";
	if( $emp_code!=0 )  $emp_code=" and emp_code=$emp_code"; else  $emp_code="";
	

	
	if($db_type==2 || $db_type==1 )
	{
      $sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    }
	if($db_type==0)
	{
	  $sql = "select emp_code,id_card_no, concat(first_name,'  ',middle_name,last_name) as emp_name, designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
		
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name',$new_conn);
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name',$new_conn);
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name',$new_conn);
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name',$new_conn);
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation',$new_conn);
	

	$arr=array(2=>$designation_arr,3=>$line_no_arr,3=>$company_arr,4=>$location_arr,5=>$division_arr,6=>$department_arr,7=>$section_arr);

		
	echo  create_list_view(
							"list_view", 
							"Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section", 
							"80,140,120,110,110,110,110,110,80",
							"1040",
							"260",
							0, 
							$sql, 
							"js_set_value", 
							"emp_code,id_card_no,emp_name", 
							"", 
							1, 
							"0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id", 
							$arr , 
							"emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id", 
							"employee_info_controller",
							'setFilterGrid("list_view",-1);',
							'0,0,0,0,0,0,0,0',
							"",
							"",
							$new_conn) ;
	exit();
}

if ($action=="load_drop_down_location_hrm")
{

   echo create_drop_down( 
   						"cbo_location_name", 
   						135, 
   						"select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0",
   						"id,location_name", 
   						1, 
   						"-- Select Location --", 
   						$selected,
   						"load_drop_down( 
										'qc_wise_date_and_style_inspection_report_controller', this.value,
										'load_drop_down_division', 
										'division_td_hrm');",
   						"",
						"",
						"",
						"",
						"",
						"",
						$new_conn );
}







if ($action == "style_ref_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_style_ref_no_id').val(id);
			$('#hide_style_ref_no').val(name);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th> Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_style_ref_no" id="hide_style_ref_no" value="" />
						<input type="hidden" name="hide_style_ref_no_id" id="hide_style_ref_no_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref");
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_style_ref_search_list_view', 'search_div', 'qc_wise_date_and_style_inspection_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_style_ref_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	$company_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}else{
		$company_con=" and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(b.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(b.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
  

	$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,$year_field
		    from  wo_po_details_master b where  
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field  order by b.id desc";

	       // echo $sql;

	$conclick="id,job_no";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no";
	}

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}


if($action=="report_generate")
{ 
	extract($_REQUEST);
    
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

    $company_id 	= str_replace("'", "", $cbo_company_id);
    $location_id 	= str_replace("'", "", $cbo_location_id);
    $style_ref_no 	= str_replace("'", "", $txt_style_ref_no);
    $operator_id 	= str_replace("'", "", $txt_operator_id);
    $date_from 		= str_replace("'", "", $txt_date_from);    
    $date_to 		= str_replace("'", "", $txt_date_to);    
    $type 			= str_replace("'", "", $type);

	$sql_cond = "";
    $sql_cond .= ($company_id!=0) ? " and a.company_name=$company_id" : "";
    $sql_cond .= ($location_id!=0) ? " and a.location_name=$location_id" : "";
	$sql_cond .= ($style_ref_no!='') ? " and a.style_ref_no='$style_ref_no'" : "";
	$sql_cond .= ($operator_id!="") ? " and d.inspector_id in('$operator_id')" : "";
    
   
    $qc_date .= ($date_from!="") ? " and d.cutting_qc_date between '$date_from' and '$date_to'" : "";

     $sql = "SELECT 
	 a.buyer_name,a.job_no,a.style_ref_no,a.gauge,d.cutting_qc_date,d.inspector_id,
	 sum(e.qc_pass_qty)as qc_pass_qty ,
	 e.reject_qty,sum(e.defect_qty) as defect_qty,
	 sum(e.replace_qty)as replace_qty ,
	 sum(e.bundle_qty) as bundle_qty 
	 from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c, pro_gmts_cutting_qc_mst d,pro_gmts_cutting_qc_dtls e 
	 where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and d.id=e.mst_id and c.id=e.color_size_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0   $sql_cond $qc_date 
	 group by a.buyer_name,a.job_no,a.style_ref_no,a.gauge,d.cutting_qc_date,d.inspector_id,e.qc_pass_qty,e.reject_qty,e.defect_qty,e.replace_qty,e.bundle_qty  
	 order by d.cutting_qc_date";
	//echo $sql; die;

	
    $res = sql_select($sql);
    if(count($res)==0)
    {
    	echo "<div style='text-align:center;color:red;font-size:20px;'>Data not found!</div>";
    	die();
    }
    $inspector_wise_data_array = array();
    $style_array = array();
    foreach ($res as $val) 
    {
    	$style_array[$val['STYLE_REF_NO']] = $val['STYLE_REF_NO'];
    }

    // echo "<pre>";print_r($style_job_array);die();
   

    $style_cond = where_con_using_array($style_array,1,"a.style_ref");

    // ========================= getting smv ===========================
    $smv_arr = return_library_array( "SELECT a.style_ref, b.total_smv from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_cond",'style_ref','total_smv'); // lib_sewing_id 20 for live, 591 for dev
    // print_r($smv_arr);
	
	

    $inspector_wise_operator_arr = array();
    foreach ($res as $val) 
    {
    	
		
    	$inspector_wise_data_array[$val['INSPECTOR_ID']][$val['CUTTING_QC_DATE']][$val['BUYER_NAME']][$val['STYLE_REF_NO']][$val['JOB_NO']][$val['GAUGE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
		$inspector_wise_data_array[$val['INSPECTOR_ID']][$val['CUTTING_QC_DATE']][$val['BUYER_NAME']][$val['STYLE_REF_NO']][$val['JOB_NO']][$val['GAUGE']]['replace_qty'] += $val['REPLACE_QTY'];
    	$inspector_wise_data_array[$val['INSPECTOR_ID']][$val['CUTTING_QC_DATE']][$val['BUYER_NAME']][$val['STYLE_REF_NO']][$val['JOB_NO']][$val['GAUGE']]['defect_qty'] += $val['DEFECT_QTY'];
    	$inspector_wise_data_array[$val['INSPECTOR_ID']][$val['CUTTING_QC_DATE']][$val['BUYER_NAME']][$val['STYLE_REF_NO']][$val['JOB_NO']][$val['GAUGE']]['reject_qty'] += $val['REJECT_QTY'];
		$inspector_wise_data_array[$val['INSPECTOR_ID']][$val['CUTTING_QC_DATE']][$val['BUYER_NAME']][$val['STYLE_REF_NO']][$val['JOB_NO']][$val['GAUGE']]['prod_min'] +=$smv_arr[$val['STYLE_REF_NO']] * $val['BUNDLE_QTY'];
		$inspector_wise_data_array[$val['INSPECTOR_ID']][$val['CUTTING_QC_DATE']][$val['BUYER_NAME']][$val['STYLE_REF_NO']][$val['JOB_NO']][$val['GAUGE']]['smv'] = $smv_arr[$val['STYLE_REF_NO']];
		$inspector_wise_data_array[$val['INSPECTOR_ID']][$val['CUTTING_QC_DATE']][$val['BUYER_NAME']][$val['STYLE_REF_NO']][$val['JOB_NO']][$val['GAUGE']]['qty']+=$val['BUNDLE_QTY'];

    	// $inspector_wise_operator_arr[$val['INSPECTOR_ID']] .= $val['INSPECTOR_ID']."**";
    }
    //echo "<pre>";print_r($inspector_wise_data_array);die();
   // ================= API data============================
    $response = file_get_contents('http://182.160.125.188:8081/hrm/api/api_data.php?company_id=1&from_date='.change_date_format($date_from).'&to_date='.change_date_format($date_to));
    $response = json_decode($response,true);

    $api_data_array = array();
    $api_emp_name_data_array = array();
    foreach ($response as $att_key => $att_value) 
    {
    	foreach ($att_value as $at_date => $date_value) 
    	{
    		foreach ($date_value as $key => $val) 
    		{
    			$api_data_array[$val['ID_CARD_NO']] += $val['WORKING_HOURS_WITHOUT_BREAK'];
				$api_emp_name_data_array[$val['ID_CARD_NO']] = $val['NAME'];
    		}
    	}
    }
	
    $inspector_wise_op_wo_hour = array();
    foreach ($inspector_wise_operator_arr as $inspector_key => $op_val) 
    {
    	$ex_op = array_filter(array_unique(explode("**", $op_val)));
    	foreach ($ex_op as $key => $op_id) 
    	{
    		// echo $op_id;
    		$inspector_wise_op_wo_hour[$inspector_key] += $api_data_array[$inspector_key];
    	}
    }
    // echo "<pre>";print_r($inspector_wise_op_wo_hour);die();


   // ========================== for chart =======================
    if($type==2)
    {
	    $inspector_name_arr = array();
	    foreach ($inspector_wise_data_array as $inspector_id => $row) 
	    {
	    	$inspector_name_arr[$inspector_id] = $api_emp_name_data_array[$inspector_id];
	    	// echo $value['defect_qty']."/".$qcQty."dfgfdgd<br>";
	    }
	    //  echo "<pre>";print_r($inspector_name_arr);die();
	}


	ob_start();
	if($type==1)
	{		
		?>
		<fieldset style="width: 1640px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2> QC Wise Inspection Report </h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>  &nbsp To  &nbsp <?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1620"  align="left">
	             	<thead>
	             		<tr>
	             			<th width="30"><p>Sl.</p> </th>
	             			<th width="170"><p>QC Name</p></th>
	             			<th width="170"><p>Card No</p></th>
	             			<th width="110"><p>QC Date</p></th>
	             			<th width="110"><p>Buyer Name</p></th>
							<th width="110"><p>Style</p></th>
							<th width="110"><p>Job No</p></th>
							<th width="110"><p>Guage</p></th>					
							<th width="110"><p>SMV</p></th>
							<th width="110"><p>Prod min</p></th>
	             			<th width="80"><p>QC Qty.</p></th>
	             			<th width="80"><p>QC Pass Qty.</p></th>
	             			<th width="80"><p>Alter Qty.</p></th>
	             			<th width="80"><p>Alter%</p></th>
	             			<th width="80"><p>Damage Qty.</p></th>
	             			<th width="80"><p>Damage%</p></th>
	             			
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:1640px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1620"  align="left" id="table_body_1">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$gr_tot_prod_min = 0;
		             		
		             		$gr_tot_qc_pass_qty = 0;
		             		$gr_tot_qc_qty = 0;
		             		$gr_tot_defect_qty = 0;
							$gr_tot_defect_qty_persent=0;
		             		$gr_tot_reject_qty = 0;
							$gr_tot_reject_qty_persent=0;
		             		// $style_total_efii = array();
		             		 foreach ($inspector_wise_data_array as $inspector_key => $inspector_val) 
		             		 {
								
								foreach ($inspector_val as $qc_date_key => $qc_date_val) 
								{   
									$tot_prod_min=0;$tot_qc_qty=0;$tot_qc_pass_qty=0;$tot_defect_qty=0;$tot_defect_qty_persent=0;$tot_reject_qty=0;$tot_reject_qty_persent=0;
									foreach ($qc_date_val as $buyer_key => $buyer_val) 
								   {
									
									foreach ($buyer_val as $style_key => $style_val) 
									{
										foreach ($style_val as $job_key => $job_val) 
									    {
											foreach ($job_val as $guage => $row) 
									       {
															$inspector_name = $api_emp_name_data_array[$inspector_id];
															
																if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
																
																
																$qc_qty =$row['qty'];
																$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
																$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
																$prod_min = ($row['qty']*$smv_arr[$style_key]);
																?>
																<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
																	<td width="30"><p><?echo $i?></p></td>
																	<td width="170"><p><?=$inspector_name_arr[$inspector_key];?></p></td>
																	<td width="170"><p><?echo $inspector_key ?></p></td>
																	<td width="110"><p><?echo $qc_date_key ?></p></td>
																	<td width="110"><p><?echo $buyer_short_library[$buyer_key ];?></p></td>
																	<td width="110"><p><?echo $style_key?></p></td>
																	<td width="110"><p><?=$job_key?></p></td>
																	<td width="110" align="right"><p><?=$gauge_arr[$guage]?></p></td>					
																	<td width="110" align="right"><p><?=number_format($smv_arr[$style_key],2);?></p></td>
																	<td width="110"align="right"><p><?= $prod_min ;?></p></td>
																	<td width="80"align="right"><p><?=$row['qty'];?></p></td>
																	<td width="80" align="right">
																	<p><? $qc_pass=$row['qc_pass_qty']- $row['replace_qty'];
																	echo number_format($qc_pass,2);?></p></td>
																	<td width="80"align="right"><p><?=$row['defect_qty']?></p></td>
																	<td width="80"align="right"><p><?=number_format($defect_prsnt,2)."%";?></p></td>
																	<td width="80"align="right"><p><?=number_format($row['reject_qty'],2)?></p></td>
																	<td width="80" align="right"><p><?=number_format($reject_prsnt,2)."%";?></p></td>
																</tr>
																			<?
																$i++;
																$tot_prod_min += $prod_min;
																						
																$tot_qc_qty +=$qc_qty;
																$tot_qc_pass_qty += $qc_pass;
																$tot_defect_qty +=$row['defect_qty'];
																$tot_defect_qty_persent +=$defect_prsnt;
																$tot_reject_qty +=$row['reject_qty'];
																$tot_reject_qty_persent +=$reject_prsnt;
																	
																
														}
														
													}
													
													
												}
												
											}
											?>
											<tr>
															<td width="30"></td>
															<td width="170"></td>
															<td width="170"></td>
															<td width="110"></td>
															<td width="110"></td>
															<td width="110"></td>
															<td width="110"></td>
															<td width="110"></td>					
															<td width="110" style="font-weight:bold;">Date Wise Total</td>
															<td width="110" align="right" style="font-weight:bold;"><p><?=number_format($tot_prod_min);?></p></td>
															<td width="80" align="right" style="font-weight:bold;"><p><?=$tot_qc_qty;?></p></td>
															<td width="80" align="right" style="font-weight:bold;"><p><?=$tot_qc_pass_qty;?></p></td>
															<td width="80" align="right" style="font-weight:bold;"><p><?=$tot_defect_qty;?></p></td>
															<td width="80" align="right" style="font-weight:bold;"><p><?=number_format($tot_defect_qty_persent,2) ;?></p></td>
															<td width="80" align="right" style="font-weight:bold;"><p><?=number_format($tot_reject_qty,2);?></p></td>
															<td width="80" align="right" style="font-weight:bold;"><p><?=number_format($tot_reject_qty_persent,2)?></p></td>
												
												 </tr>
												
												<?
												 $gr_tot_prod_min +=$tot_prod_min;
		             		
												 $gr_tot_qc_qty	  += $tot_qc_qty;
												 $gr_tot_qc_pass_qty += $tot_qc_pass_qty;
												 $gr_tot_defect_qty += $tot_defect_qty;
												$gr_tot_defect_qty_persent+= $tot_defect_qty_persent;
												 $gr_tot_reject_qty += $tot_reject_qty;
												$gr_tot_reject_qty_persent+=$tot_reject_qty_persent;
											
										}
										
									}	
											
											?>
									
													<tr>
																	<td width="30"></td>
																	<td width="170"></td>
																	<td width="170"></td>
																	<td width="110"></td>
																	<td width="110"></td>
																	<td width="110"></td>
																	<td width="110"></td>
																	<td width="110"></td>					
																	<td width="110" style="font-weight:bold">Grand Total</td>
																	<td width="110" align="right" style="font-weight:bold;"><p><?=number_format($gr_tot_prod_min,2); ?></p></td>
																	<td width="80" align="right" style="font-weight:bold;"><p><?=number_format($gr_tot_qc_qty,2); ?></p></td>
																	<td width="80"  align="right" style="font-weight:bold;"><p><?=  number_format($gr_tot_qc_pass_qty,2);?></p></td>
																	<td width="80"  align="right" style="font-weight:bold;"><p><?=number_format($gr_tot_defect_qty,2);?></p></td>
																	<td width="80"  align="right" style="font-weight:bold;"><p><?=number_format($gr_tot_defect_qty_persent,2);?></p></td>
																	<td width="80"  align="right" style="font-weight:bold;"><p><?=number_format($gr_tot_reject_qty,2);?></p></td>
																	<td  width="80" align="right" style="font-weight:bold;"><?= number_format($gr_tot_reject_qty_persent,2);?></p></td>
														
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}

	

	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$type####".implode("__",$inspector_name_arr)."####".implode("__",$inspector_total_mark);
	exit(); 
}




