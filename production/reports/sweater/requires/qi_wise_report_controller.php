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
	                								document.getElementById('src_emp_code').value, 'create_supervisor_search_list_view', 
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
    $date_from 		= str_replace("'", "", $txt_date_from);    
    $date_to 		= str_replace("'", "", $txt_date_to);    
    $type 			= str_replace("'", "", $type);

	$sql_cond = "";
    $sql_cond .= ($company_id!=0) ? " and a.company_name=$company_id" : "";
    $sql_cond .= ($location_id!=0) ? " and a.location_name=$location_id" : "";
	$sql_cond .= ($operator_id!="") ? " and f.inspector_id in('$operator_id')" : "";
    $sql_cond .= ($supervisor_id!="") ? " and f.SUPERVISOR_ID in('$supervisor_id')" : "";
    $sql_cond .= ($date_from!="") ? " and d.production_date between '$date_from' and '$date_to'" : "";
    $qc_date .= ($date_from!="") ? " and a.cutting_qc_date between '$date_from' and '$date_to'" : "";

    $sql = "SELECT a.buyer_name,a.job_no,a.style_ref_no as style,d.production_date as pdate,e.production_qnty as qc_pass_qty,e.defect_qty,e.reject_qty,f.inspector_id,f.supervisor_id,e.replace_qty from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e, pro_gmts_cutting_qc_mst f where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and f.id=d.delivery_mst_id and d.production_type=52 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.is_rescan=0 $sql_cond order by d.production_date";
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
    	$style_array[$val['STYLE']] = $val['STYLE'];
    }

    // echo "<pre>";print_r($style_job_array);die();
   

    $style_cond = where_con_using_array($style_array,1,"a.style_ref");

    // ========================= getting smv ===========================
    $smv_arr = return_library_array( "SELECT a.style_ref, b.total_smv from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and b.lib_sewing_id=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_cond",'style_ref','total_smv'); // lib_sewing_id 20 for live, 591 for dev
    // print_r($smv_arr);

    // ========================= getting qc qty ===========================
    $bundle_sql = sql_select( "SELECT a.inspector_id,a.cutting_qc_date as qc_date,a.loss_min, sum(b.bundle_qty) as bundle_qty from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_rescan=0 $qc_date group by a.inspector_id,a.cutting_qc_date,a.loss_min");
    // $bundle_sql =  "SELECT a.job_no,a.cutting_qc_date as qc_date,a.loss_min, sum(b.bundle_qty) as bundle_qty from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.is_rescan=0 $job_cond $qc_date group by a.job_no,a.cutting_qc_date,a.loss_min";
	// echo $bundle_sql;
	
    $inspector_wise_bundle_qty_arr = array();
    foreach ($bundle_sql as $val) 
    {
    	$inspector_wise_bundle_qty_arr[$val['INSPECTOR_ID']]['qty'] += $val['BUNDLE_QTY'];
    	$inspector_wise_bundle_qty_arr[$val['INSPECTOR_ID']]['loss_min'] += $val['LOSS_MIN'];
    }

    // echo "<pre>"; print_r($inspector_wise_bundle_qty_arr);

    $inspector_wise_operator_arr = array();
    foreach ($res as $val) 
    {
    	if($inspector_wise_data_array[$val['INSPECTOR_ID']]['supervisor_id']=="")
    	{
    		$inspector_wise_data_array[$val['INSPECTOR_ID']]['supervisor_id'] = $val['SUPERVISOR_ID'];
    	}
    	$inspector_wise_data_array[$val['INSPECTOR_ID']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
		$inspector_wise_data_array[$val['INSPECTOR_ID']]['replace_qty'] += $val['REPLACE_QTY'];
    	$inspector_wise_data_array[$val['INSPECTOR_ID']]['defect_qty'] += $val['DEFECT_QTY'];
    	$inspector_wise_data_array[$val['INSPECTOR_ID']]['reject_qty'] += $val['REJECT_QTY'];
    	$inspector_wise_data_array[$val['INSPECTOR_ID']]['prod_min'] += $val['QC_PASS_QTY']*$smv_arr[$val['STYLE']];

    	$inspector_wise_operator_arr[$val['INSPECTOR_ID']] .= $val['INSPECTOR_ID']."**";
    }
    // echo "<pre>";print_r($inspector_wise_data_array);//die();
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
		<fieldset style="width: 1380px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2> QI Wise Summary and  Efficency Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1360"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="170">Supervisor</th>
	             			<th width="170">Inspector Name</th>
	             			<th width="110">Card No</th>
	             			<th width="80">QC Qty.</th>
	             			<th width="80">Prod min</th>
	             			<th width="80">Working Min</th>
	             			<th width="80">Loss Min</th>
	             			<th width="80">Effi%</th>
	             			<th width="80">QC Pass Qty.</th>
	             			<th width="80">Alter Qty.</th>
	             			<th width="80">Alter%</th>
	             			<th width="80">Damage Qty.</th>
	             			<th width="80">Damage%</th>
	             			<th width="80">Total Marks</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:1380px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1360"  align="center" id="table_body_1">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_prod_min = 0;
		             		$tot_working_min = 0;
		             		$tot_loss_min = 0;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		// $style_total_efii = array();
		             		foreach ($inspector_wise_data_array as $inspector_id => $row) 
		             		{
								$inspector_name = $api_emp_name_data_array[$inspector_id];
								// if($inspector_name !="")
								// {
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$qc_qty = $inspector_wise_bundle_qty_arr[$inspector_id]['qty'];
									$loss_min = $inspector_wise_bundle_qty_arr[$inspector_id]['loss_min'];
									$prod_min = $qc_qty*$smv_arr[$style];
									$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
									$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
									$working_min = $inspector_wise_op_wo_hour[$inspector_id];
									$effi = $row['prod_min'] / ($working_min - $loss_min)*100;
									$effi = is_nan($effi) ? 0 : $effi;
									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
										<td width="30" align="center"><?=$i;?></td>
										<td width="170" align="left">
											<? 
												echo $api_emp_name_data_array[$row['supervisor_id']];
												//  echo $row['supervisor_id']; 
											?>
										</td>
										<td width="170" align="left">
											<?
											   echo $inspector_name;
											?>
										</td>
										<td width="110" align="left"><? echo $inspector_id; ?></td>
										<td width="80" align="right"><?=number_format($qc_qty,0);;?></td>
										<td width="80" align="right"><?=number_format($row['prod_min'],2);?></td>
										<td width="80" align="right"><?=number_format($working_min,2);?></td>
										<td width="80" align="right"><?=number_format($loss_min,2);?></td>
										<td width="80" align="right"><?=number_format($effi,2);?></td>
										<td width="80" align="right">
											<?//=number_format($row['qc_pass_qty'],0);?>
											<? $qc_pass=$row['qc_pass_qty']- $row['replace_qty'];
										echo number_format($qc_pass,0);?>
										</td>
										<td width="80" align="right" ><?=number_format($row['defect_qty'],0);?></td>
										<td width="80" align="right" style="font-weight:bold;"><?=number_format($defect_prsnt,2)."%";?></td>
										<td width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
										<td width="80" align="right" style="font-weight:bold;"><?=number_format($reject_prsnt,2)."%";?></td>
										<td width="80" align="right" style="font-weight:bold;">
											<? 
												$total_marks = 0;
												// $eff_marks = (20*(($effi/100)*100))/100;
												if($effi>= 100){
													$eff_marks=20;
												}else{
													$eff_marks = (20*(($effi/100)*100))/100;
												}
												// $alter_marks
												$alter_marks ="";
												if($defect_prsnt<=5)
												{
													$alter_marks= 0;
												}
												else if($defect_prsnt>=20)
												{
													$alter_marks = 10;
												}
												else if($defect_prsnt>5 || $defect_prsnt<20)
												{
													$alter_marks = (10*($defect_prsnt-5))/15;
												}
												// reject_marks
												$reject_marks ="";
												if($reject_prsnt<=2)
												{
													$reject_marks = 0;
												}
												else if($reject_prsnt>=10)
												{
													$reject_marks = 10;
												}
												else if($reject_prsnt>5 || $reject_prsnt<20)
												{
													$reject_marks = (10*($reject_prsnt-2))/8;
												}
												// $total_marks
												$total_marks = $eff_marks + $alter_marks + $reject_marks;
												echo number_format(is_nan($total_marks) ? 0 : $total_marks,2);

											?>
										</td>
									</tr>
									<?
									$i++;

									$tot_prod_min += $row['prod_min'];
									$tot_working_min += $working_min;
									$tot_loss_min += $loss_min;
									$tot_qc_qty +=$qc_qty;
									$tot_qc_pass_qty += $row['qc_pass_qty'];
									$tot_defect_qty +=$row['defect_qty'];
									$tot_reject_qty +=$row['reject_qty'];
								// }	
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? $tot_defect_qty/$tot_qc_qty : 0;
			             	$tot_rej_prsnt = ($tot_reject_qty) ? $tot_reject_qty/$tot_qc_qty : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1360"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30"></th>
	             			<th width="170"></th>
	             			<th width="170"></th>
	             			<th width="110">Total</th>
	             			<th width="80"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_prod_min,2); ?></th>
	             			<th width="80"><?=number_format($tot_working_min,2);?></th>
	             			<th width="80"><?=number_format($tot_loss_min,2);?></th>
	             			<th width="80"><?=number_format($a,2);?></th>
	             			<th width="80"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_dft_prsnt,2)."%"; ?></th>
	             			<th width="80"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_rej_prsnt,2)."%"; ?></th>
	             			<th width="80"></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}
	elseif ($type==3) 
	{		
		?>
		<fieldset style="width: 820px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2> QI Wise Summary and  Efficency Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($date_from); ?>To<?=change_date_format($date_to); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="800"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="170">Supervisor</th>
	             			<th width="170">Inspector Name</th>
	             			<th width="110">Card No</th>
	             			<th width="80">Eff% Marks</th>
	             			<th width="80">Alter Marks</th>
	             			<th width="80">Damage Marks</th>
	             			<th width="80">Total Marks</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:820px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="800"  align="center" id="table_body_1">
		             	<tbody>
		             		<?
		             		$i=1;
		             		// $style_total_efii = array();
		             		foreach ($inspector_wise_data_array as $inspector_id => $row) 
		             		{
								$inspector_name = $api_emp_name_data_array[$inspector_id];
								// if($inspector_name !="")
								// {
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$qc_qty = $inspector_wise_bundle_qty_arr[$inspector_id]['qty'];
									$loss_min = $inspector_wise_bundle_qty_arr[$inspector_id]['loss_min'];
									$prod_min = $qc_qty*$smv_arr[$style];
									$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
									$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
									$working_min = $inspector_wise_op_wo_hour[$inspector_id];
									$effi = $row['prod_min'] / ($working_min - $loss_min)*100;
									$effi = is_nan($effi) ? 0 : $effi;
                                    // $style_total_efii[] = number_format($effi,2);
									?>
									<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
										<td width="30" align="center"><?=$i;?></td>
										<td width="170" align="left">
											<? 
												echo $api_emp_name_data_array[$row['supervisor_id']]; 
												//  echo $row['supervisor_id']; 
											?>
										</td>
										<td width="170" align="left">
											<? 
											// echo $api_emp_name_data_array[$inspector_id];
											//    echo $inspector_id;
											   echo $inspector_name;
											?>
										</td>
										<td width="110" align="left"><? echo $inspector_id; ?></td>
										<td width="80" align="right">
											<? 
											// $eff_marks = (20*(($effi/100)*100))/100;
											// echo number_format($eff_marks,2);

										    if($effi>= 100){
												$eff_marks=20;
											}else{
                                                $eff_marks = (20*(($effi/100)*100))/100;
											}
                                            $eff_marks = is_nan($eff_marks) ? 0 : $eff_marks;
                                            echo number_format($eff_marks, 2);
											?>
										</td>
										<td width="80" align="right">
											<? 
												$alter_marks ="";
												if($defect_prsnt<=5)
												{
													$alter_marks= 0;
												}
												else if($defect_prsnt>=20)
												{
													$alter_marks = 10;
												}
												else if($defect_prsnt>5 || $defect_prsnt<20)
												{
													$alter_marks = (10*($defect_prsnt-5))/15;
												}
												echo number_format($alter_marks,2);
											?>
										</td>
										<td width="80" align="right">
											<?
											$reject_marks ="";
											if($reject_prsnt<=2)
												{
													$reject_marks = 0;
												}
												else if($reject_prsnt>=10)
												{
													$reject_marks = 10;
												}
												else if($reject_prsnt>5 || $reject_prsnt<20)
												{
													$reject_marks = (10*($reject_prsnt-2))/8;
												}
												echo number_format($reject_marks,2);
											?>
										</td>
										<td width="80" align="right" style="font-weight:bold;">
										<?
											$total_marks = $eff_marks + $alter_marks + $reject_marks;
											echo number_format(is_nan($total_marks) ? 0 : $total_marks,2);
										?>
										</td>
									</tr>
									<?
									$i++;
								// }	
				            }
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
			</div>
		</fieldset>
		<?
	}
	elseif ($type==2) 
	{
		$show_chart = "show_supervisor_wise";

		$inspector_total_mark =array();
		$i=1;
		foreach ($inspector_wise_data_array as $inspector_id => $row) 
		{
			$inspector_name = $api_emp_name_data_array[$inspector_id];
			if($inspector_name !="")
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$qc_qty = $inspector_wise_bundle_qty_arr[$inspector_id]['qty'];
				$loss_min = $inspector_wise_bundle_qty_arr[$inspector_id]['loss_min'];
				$prod_min = $qc_qty*$smv_arr[$style];
				$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
				$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
				$working_min = $inspector_wise_op_wo_hour[$inspector_id];
				$effi = $row['prod_min'] / ($working_min - $loss_min)*100;
				$effi = is_nan($effi) ? 0 : $effi;
				// $style_total_efii[] = number_format($effi,2);
				?>
				
						<? 
						// $eff_marks = (20*(($effi/100)*100))/100;
					
						// echo number_format($eff_marks,2);
						if($effi>= 100){
							$eff_marks=20;
						}else{
							$eff_marks = (20*(($effi/100)*100))/100;
						}
                        $eff_marks = is_nan($eff_marks) ? 0 : $eff_marks;
						?>
						<? 
							$alter_marks ="";
							if($defect_prsnt<=5)
							{
								$alter_marks= 0;
							}
							else if($defect_prsnt>=20)
							{
								$alter_marks = 10;
							}
							else if($defect_prsnt>5 || $defect_prsnt<20)
							{
								$alter_marks = (10*($defect_prsnt-5))/15;
							}
							// echo number_format($alter_marks,2);
						?>

						<?
						$reject_marks ="";
						if($reject_prsnt<=2)
							{
								$reject_marks = 0;
							}
							else if($reject_prsnt>=10)
							{
								$reject_marks = 10;
							}
							else if($reject_prsnt>5 || $reject_prsnt<20)
							{
								$reject_marks = (10*($reject_prsnt-2))/8;
							}
							// echo number_format($reject_marks,2);
						?>

					<?
						$total_marks = $eff_marks + $alter_marks + $reject_marks;
						// echo number_format($total_marks,2);
					?>
					
				<?
				$inspector_total_mark[] = number_format($total_marks,2);
				$i++;
			}	
		}

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




