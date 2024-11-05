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

if ($action=="load_drop_down_buyer")
{	
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

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
																	'jacquard_operator_wise_inspection_report_controller', this.value, 
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
	                								'jacquard_operator_wise_inspection_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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
if ($action=="supervisor_popup")
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
																	'jacquard_operator_wise_inspection_report_controller', this.value, 
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
	                								document.getElementById('src_emp_code').value, 'create_supervisor_search_list_view', 
	                								'search_div', 
	                								'jacquard_operator_wise_inspection_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
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

if($action=="create_supervisor_search_list_view")
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
										'bundle_receive_from_knitting_floor_controller', this.value,
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


if($action=="report_generate")
{ 
	extract($_REQUEST);
    
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

    $company_id 	= str_replace("'", "", $cbo_company_id);
    $location_id 	= str_replace("'", "", $cbo_location_id);
    $operator_id 	= str_replace("'", "", $txt_operator_id);
    $supervisor_id 	= str_replace("'", "", $txt_supervisor_id);
    $hide_job_id 	= str_replace("'", "", $hide_job_id);
    $date_from 		= str_replace("'", "", $txt_date_from);    
    $date_to 		= str_replace("'", "", $txt_date_to);    
    $type 			= str_replace("'", "", $type);

    $sql_cond = "";
    $sql_cond .= ($company_id!=0) ? " and a.company_name=$company_id" : "";
    $sql_cond .= ($location_id!=0) ? " and a.location_name=$location_id" : "";
    $sql_cond .= ($date_from!="") ? " and d.production_date between '$date_from' and '$date_to'" : "";
    $qc_date .= ($date_from!="") ? " and a.cutting_qc_date between '$date_from' and '$date_to'" : "";

    $sql_emp_cond = "";
	$sql_emp_cond .= ($operator_id!="") ? " and a.OPERATOR_ID in('$operator_id')" : "";
    $sql_emp_cond .= ($supervisor_id!="") ? " and a.SUPERVISOR_ID in('$supervisor_id')" : "";
	// echo "$operator_id"."__"."$supervisor_id";

	$sql = "SELECT 
	a.gauge,
	d.production_date     AS pdate,
	e.production_qnty     AS qc_pass_qty,
	e.defect_qty,
	e.reject_qty,
	e.replace_qty,
	e.bundle_no
    FROM wo_po_details_master        a,
	wo_po_break_down            b,
	wo_po_color_size_breakdown  c,
	pro_garments_production_mst d,
	pro_garments_production_dtls e
    WHERE     a.id = b.job_id
	AND b.id = c.po_break_down_id
	AND a.id = c.job_id
	AND b.id = d.po_break_down_id
	AND c.id = e.color_size_break_down_id
	AND d.id = e.mst_id
	AND d.production_type = 52
	AND a.status_active = 1
	AND a.is_deleted = 0
	AND b.status_active = 1
	AND b.is_deleted = 0
	AND c.status_active = 1
	AND c.is_deleted = 0
	AND d.status_active = 1
	AND d.is_deleted = 0
	AND e.status_active = 1
	AND e.is_deleted = 0
	AND e.is_rescan = 0
	$sql_cond
    ORDER BY d.production_date";
    // echo $sql;


    $res = sql_select($sql);
    if(count($res)==0)
    {
    	echo "<div style='text-align:center;color:red;font-size:20px;'>Data not found!</div>";
    	die();
    }
    // $data_array = array();
    $date_wise_data_array = array();
    $bundle_array = array();
    $bundle_wise_data_array = array();
    // $particular_data_array = array();
    foreach ($res as $val) 
    {
    	$gauge = "";
    	if($val['GAUGE']==2 || $val['GAUGE']==3 || $val['GAUGE']==4 || $val['GAUGE']==1 || $val['GAUGE']==5 || $val['GAUGE']==8 || $val['GAUGE']==9 || $val['GAUGE']==10)
    	{
    		$gauge = "Course Guage";
    	}
    	elseif ($val['GAUGE']==6 || $val['GAUGE']==7 || $val['GAUGE']==11) 
    	{
    		$gauge = "Fine Guage";
    	}
    	$bundle_wise_data_array[$val['BUNDLE_NO']]['gauge'] = $gauge;
    	$bundle_wise_data_array[$val['BUNDLE_NO']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    	$bundle_wise_data_array[$val['BUNDLE_NO']]['replace_qty'] += $val['REPLACE_QTY'];
    	$bundle_wise_data_array[$val['BUNDLE_NO']]['defect_qty'] += $val['DEFECT_QTY'];
    	$bundle_wise_data_array[$val['BUNDLE_NO']]['reject_qty'] += $val['REJECT_QTY'];

    	$bundle_array[$val['BUNDLE_NO']] = $val['BUNDLE_NO'];

    }
	// echo "<pre>";
	// print_r($bundle_array);
    $bundle_cond = where_con_using_array($bundle_array,1,"c.bundle_no");
    $bundle_qc_qnty_cond = where_con_using_array($bundle_array,1,"b.bundle_no");

	// ========================= getting Operator and Supervisor Name ===========================
      $employee_sql = "SELECT a.OPERATOR_ID, a.SUPERVISOR_ID,c.BUNDLE_NO
	  FROM pro_gmts_delivery_mst         a,
		   pro_garments_production_mst   b,
		   pro_garments_production_dtls  c
	 WHERE
				 b.id = c.mst_id
			 AND b.delivery_mst_id = a.id
			 AND b.production_type = 51
			 AND a.status_active = 1
			 AND a.is_deleted = 0
			 AND b.status_active = 1
			 AND b.is_deleted = 0
			 AND c.status_active = 1
			 AND c.is_deleted = 0
			 $sql_emp_cond
			 $bundle_cond";
	//   echo $employee_sql;
	  $employee_sql_result = sql_select($employee_sql); 

	  $supervisor_array = array();
      $operator_array = array();
      $operator_id_array = array();

	  foreach($employee_sql_result as $row){
        $supervisor_array[$row['BUNDLE_NO']]= $row['SUPERVISOR_ID'];
        $operator_array[$row['BUNDLE_NO']] = $row['OPERATOR_ID'];
        $operator_id_array[$row['OPERATOR_ID']] = $row['OPERATOR_ID'];
		
	  }
	//   echo "<pre>";
	// print_r($supervisor_array);
	$report_data_array = array();
	$supervisor_report_data_array = array();
	foreach($bundle_wise_data_array as $bundle_key => $bundle_data){
        $report_data_array[$operator_array[$bundle_key]]['qc_pass_qty'] += $bundle_data['qc_pass_qty'];
        $report_data_array[$operator_array[$bundle_key]]['replace_qty'] += $bundle_data['replace_qty'];
        $report_data_array[$operator_array[$bundle_key]]['defect_qty'] += $bundle_data['defect_qty'];
        $report_data_array[$operator_array[$bundle_key]]['reject_qty'] += $bundle_data['reject_qty'];
        $report_data_array[$operator_array[$bundle_key]]['gauge'] = $bundle_data['gauge'];
        $report_data_array[$operator_array[$bundle_key]]['supervisor_id'] = $supervisor_array[$bundle_key];

        $supervisor_report_data_array[$supervisor_array[$bundle_key]]['qc_pass_qty'] += $bundle_data['qc_pass_qty'];
        $supervisor_report_data_array[$supervisor_array[$bundle_key]]['replace_qty'] += $bundle_data['replace_qty'];
        $supervisor_report_data_array[$supervisor_array[$bundle_key]]['defect_qty'] += $bundle_data['defect_qty'];
        $supervisor_report_data_array[$supervisor_array[$bundle_key]]['reject_qty'] += $bundle_data['reject_qty'];
        $supervisor_report_data_array[$supervisor_array[$bundle_key]]['gauge'] = $bundle_data['gauge'];
	}
	// echo"<pre>";
	// print_r($supervisor_report_data_array);
	$op_id_card_cond = where_con_using_array($operator_id_array,0,"id");
	// $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
	$emp_name_array = return_library_array("select id_card_no,(first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0 ","id_card_no","emp_name",$new_conn);
	// print_r($emp_name_array);die;

	// ================= API data============================
	// http://182.160.125.188:8081/hrm/api/api_data.php?company_id=1&from_date=01-08-2022&to_date=02-08-2022
	$response = file_get_contents('http://182.160.125.188:8081/hrm/api/api_data.php?company_id='.$company_id.'&from_date='.change_date_format($date_from).'&to_date='.change_date_format($date_to));
    //date('d-m-Y', strtotime($date_from. "- 7 days"));
    $response = json_decode($response,true);
    //echo "<pre>"; print_r($response);//die();
	$api_data_array = array();
	foreach ($response as $att_key => $att_value)
    {
    	foreach ($att_value as $at_date => $date_value) 
    	{
    		foreach ($date_value as $key => $val) 
    		{
			     $api_data_array[$val['ID_CARD_NO']] = $val['NAME'];
    		}
    	}
    }
//     echo "<pre>";print_r($api_data_array);//die();
     
    // ========================= getting bundle qty ===========================
    $bundle_sql =   sql_select( "SELECT    b.bundle_no,
					SUM (b.bundle_qty)     AS bundle_qty
					FROM pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b
					WHERE     a.id = b.mst_id
					AND a.status_active = 1
					AND a.is_deleted = 0
					AND b.status_active = 1
					AND b.is_deleted = 0
					AND b.is_rescan = 0
					$bundle_qc_qnty_cond
					GROUP BY b.bundle_no");

    // $bundle_sql = "SELECT    b.bundle_no,
	// SUM (b.bundle_qty)     AS bundle_qty
    // FROM pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b
    // WHERE     a.id = b.mst_id
	// AND a.status_active = 1
	// AND a.is_deleted = 0
	// AND b.status_active = 1
	// AND b.is_deleted = 0
	// AND b.is_rescan = 0
	// $bundle_qc_qnty_cond
    // GROUP BY b.bundle_no";
    // echo $bundle_sql;// die;


    $bundle_wise_qc_qty_arr = array();
    foreach ($bundle_sql as $val) 
    {
    	$bundle_wise_qc_qty_arr[$val['BUNDLE_NO']]['qty'] += $val['BUNDLE_QTY'];
    }
    //  echo "<pre>";
    // print_r($bundle_wise_qc_qty_arr);

	// =====================
	$qc_data_array = array();
	foreach($bundle_wise_qc_qty_arr as $bundle_key => $bundle_value){
		$operator_wise_qc_data_array[$operator_array[$bundle_key]]['qty'] += $bundle_value['qty'];

		$qc_data_array[$supervisor_array[$bundle_key]]['qty'] += $bundle_value['qty'];
	}
	// echo"<pre>";
	// print_r($operator_wise_qc_data_array);

   // ========================== for chart =======================
   if($type==2)
    {
	    $supervisor_name_arr = array();
	    $supervisor_total_defect = array();
	    $supervisor_total_reject = array();
	    foreach ($report_data_array as $key => $value) 
	    {
	    	if($key !=""){
				$qcQty = $operator_wise_qc_data_array[$key]['qty'];
				// $supervisor_name_arr[$key] = $key;
				$supervisor_name_arr[$key] =  $api_data_array[$key];
				$supervisor_total_defect[] = ($value['defect_qty']) ? number_format((($value['defect_qty']/$qcQty)*100),2) : 0;
				$supervisor_total_reject[] = ($value['reject_qty']) ? number_format((($value['reject_qty']/$qcQty)*100),2) : 0;
				// echo $value['defect_qty']."/".$qcQty."dfgfdgd<br>";
			}
	    }

	    //  echo "<pre>";print_r($supervisor_name_arr);die();
	}

    if($type==4)
    {
	    $supervisor_name_arr = array();
	    $supervisor_total_defect = array();
	    $supervisor_total_reject = array();
	    foreach ($supervisor_report_data_array as $key => $value) 
	    {
	    	if($key !="")
			{
				$qcQty = $qc_data_array[$key]['qty'];
				// $supervisor_name_arr[$key] = $key;
				$supervisor_name_arr[$key] = $api_data_array[$key];
				$supervisor_total_defect[] = ($value['defect_qty']) ? number_format((($value['defect_qty']/$qcQty)*100),2) : 0;
				$supervisor_total_reject[] = ($value['reject_qty']) ? number_format((($value['reject_qty']/$qcQty)*100),2) : 0;
				// echo $value['defect_qty']."/".$qcQty."dfgfdgd<br>";
			}
	    }

	    //  echo "<pre>";print_r($supervisor_report_data_array);die();
	}

	ob_start();
	if($type==1)
	{		
		?>
		<fieldset style="width: 840px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Jacquard Supervisor Wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="820"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="75">Operator Name</th>
	             			<th width="75">Operator ID</th>
	             			<th width="80">Supervisor Name</th>
	             			<th width="80">Gauge</th>
							 <th width="80">QC Qty.</th>
	             			<th width="80">QC Pass Qty.</th>
	             			<th width="80">Alter Qty.</th>
	             			<th width="80">Alter%</th>
	             			<th width="80">Damage Qty.</th>
	             			<th width="80">Damage%</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:840px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="820"  align="center" id="table_body_1">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		foreach ($report_data_array as $operator_key => $row) 
		             		{
	             				if($operator_key !="")
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$qc_qty = $operator_wise_qc_data_array[$operator_key]['qty'];
   
									$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
									$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
										<td valign="middle" width="30"><?=$i;?></td>
										<td valign="middle" width="75" align="left">
											<? 
										    //    echo $api_data_array[$operator_key];
										       echo $emp_name_array[$operator_key];
										 	  // echo $operator_key;
											?>
									   </td>
   
										<td valign="middle" width="75" align="center"><? echo $operator_key; ?></td>
										<td valign="middle" width="80" align="left"><?=$api_data_array[$row['supervisor_id']];?></td>
										<td valign="middle" width="80" align="left"><?=$row['gauge'];?></td>
										<td valign="middle" width="80" align="right"><?=number_format($qc_qty,0);;?></td>
										<td valign="middle" width="80" align="right">
											<?//=number_format($row['qc_pass_qty'],0);?>
											<?
											$qc_pass_qty=$row['qc_pass_qty']-$row['replace_qty'];
											echo number_format($qc_pass_qty,0);?>
										</td>
										<td valign="middle" width="80" align="right"><?=number_format($row['defect_qty'],0);?></td>
										<td valign="middle" width="80" align="right"><?=number_format($defect_prsnt,2)."%";?></td>
										<td valign="middle" width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
										<td valign="middle" width="80" align="right"><?=number_format($reject_prsnt,2)."%";?></td>
									</tr>
									<?
									$i++;
   
									$tot_qc_qty +=$qc_qty;
									$tot_qc_pass_qty += $qc_pass_qty;
									$tot_defect_qty +=$row['defect_qty'];
									$tot_reject_qty +=$row['reject_qty'];
								}
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? ($tot_defect_qty/$tot_qc_qty)*100 : 0;
			             	$tot_rej_prsnt = ($tot_reject_qty) ? ($tot_reject_qty/$tot_qc_qty)*100 : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="820"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30">.</th>
	             			<th width="75">.</th>
	             			<th width="75">.</th>
	             			<th width="80"></th>
	             			<th width="80">Total</th>
							<th width="80"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_dft_prsnt,2)."%"; ?></th>
	             			<th width="80"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_rej_prsnt,2)."%"; ?></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}
	elseif ($type==2) 
	{
		$show_chart = "show_style_wise";
	}
	elseif ($type==3) 
	{		
		?>
		<fieldset style="width: 760px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Jacquard Supervisor Wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="740"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="150">Supervisor Name</th>
	             			<th width="80">Gauge</th>
							 <th width="80">QC Qty.</th>
	             			<th width="80">QC Pass Qty.</th>
	             			<th width="80">Alter Qty.</th>
	             			<th width="80">Alter%</th>
	             			<th width="80">Damage Qty.</th>
	             			<th width="80">Damage%</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:760px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="740"  align="center" id="table_body_1">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		foreach ($supervisor_report_data_array as $supervisor_key => $row) 
		             		{
	             				if( $supervisor_key !="")
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$qc_qty = $qc_data_array[$supervisor_key]['qty'];
									$loss_min = $date_wise_bundle_qty_arr[$pdate]['loss_min'];
   
									$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
									$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
										<td width="30"><?=$i;?></td>
										<td width="150" align="left">
											<? 
											// echo $api_data_array[$supervisor_key];
											echo $emp_name_array[$supervisor_key];
											 ?>
									   </td>
										<td width="80" align="right"><?=$row['gauge'];?></td>
										<td width="80" align="right"><?=number_format($row['qc_pass_qty'],0);?>
										<td width="80" align="right">
											<?//=number_format($qc_qty,0);
											
											$qc_pass_qty=$row['qc_pass_qty']-$row['replace_qty'];
											echo number_format($qc_pass_qty,0);?>
											
											
										</td>
									</td>
										<td width="80" align="right"><?=number_format($row['defect_qty'],0);?></td>
										<td width="80" align="right"><?=number_format($defect_prsnt,2)."%";?></td>
										<td width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
										<td width="80" align="right"><?=number_format($reject_prsnt,2)."%";?></td>
									</tr>
									<?
									$i++;
   
									$tot_qc_qty +=$qc_qty;
									$tot_qc_pass_qty += $qc_pass_qty;
									$tot_defect_qty +=$row['defect_qty'];
									$tot_reject_qty +=$row['reject_qty'];

								}
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? ($tot_defect_qty/$tot_qc_qty)*100 : 0;
			             	$tot_rej_prsnt = ($tot_reject_qty) ? ($tot_reject_qty/$tot_qc_qty)*100 : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="740"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30">.</th>
	             			<th width="150"></th>
	             			<th width="80">Total</th>
							<th width="80"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_dft_prsnt,2)."%"; ?></th>
	             			<th width="80"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_rej_prsnt,2)."%"; ?></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}
	elseif ($type==4) 
	{
		$show_chart = "show_date_wise";
	}

	$particular_name = implode(',', $particular_name_arr);
	$particular_value = implode(',', $fparticular_value_arr);
	
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
	echo "$total_data####$filename####$type####".implode("__",$supervisor_name_arr)."####".implode("__",$supervisor_total_defect)."####".implode("__",$supervisor_total_reject);
	exit(); 
}




