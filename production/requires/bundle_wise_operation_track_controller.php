<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_working_location",130,"select id, location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name",1,"-Select Location-",$selected,"",1,"","","","","");
	exit();
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process=5 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",1);
	exit();
}

if ($action=="load_drop_down_line")
{
	list($company_id,$location,$floor,$issue_date,$prod_reso_allocation)=explode("_",$data);

	/*$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$company_id' and variable_list=23 and status_active=1 and is_deleted=0");

	$prod_reso_allocation = $nameArray[0][csf('auto_update')];*/
	$cond="";
	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();

		if( $floor==0 && $location!=0 ) $cond = " and a.location_id= $location";
		if( $floor!=0 ) $cond = " and a.floor_id= $floor";

		if($db_type==0) $issue_date = date("Y-m-d",strtotime($issue_date));
		else $issue_date = change_date_format(date("Y-m-d",strtotime($issue_date)),'','',1);

		$cond.=" and b.pr_date='".$issue_date."'";

		if($db_type==0)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num order by a.prod_resource_num asc, a.id asc");
		}
		else if($db_type==2 || $db_type==1)
		{
			$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number,a.prod_resource_num  order by  a.prod_resource_num,a.id asc");
		}
		 $line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$line_library[$val]]=$row[csf('id')];

				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_line_no", 130,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $floor==0 && $location!=0 ) $cond = " and location_name= $location";
		if( $floor!=0 ) $cond = " and floor_name= $floor"; else  $cond = " and floor_name like('%%')";

		echo create_drop_down( "cbo_line_no", 130, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--Select--", $selected, "",1,0 );
	}
	exit();
}

if($action=="load_drop_down_operation")
{
	$sql="select a.id, a.operation_name from lib_sewing_operation_entry a, ppl_gsd_entry_dtls b where b.mst_id='$data' and a.id=b.lib_sewing_id and a.status_active=1 and b.is_deleted=0 group by a.id, a.operation_name, b.row_sequence_no order by b.row_sequence_no ASC";
	
	echo create_drop_down( "cbo_operation_id", 130, $sql,"id,operation_name", 1, "--- Select ---", $selected, "fnc_ws_data(this.value);",0,0 );
	exit();
}

if($action=="load_wsdata")
{
	$ex_data=explode("_",$data);
	
	$sql="select total_smv, resource_gsd from ppl_gsd_entry_dtls where lib_sewing_id='$ex_data[1]' and mst_id='$ex_data[0]'";
	$data_array=sql_select($sql);
	$total_smv=$data_array[0][csf("total_smv")];
	$resource_gsd=$data_array[0][csf("resource_gsd")];
	
	$wtrack=return_field_value("worker_tracking", "ppl_balancing_dtls_entry", "lib_sewing_id='$ex_data[1]' and gsd_mst_id='$ex_data[0]'");
	echo $total_smv.'_'.$resource_gsd.'_'.$production_resource[$resource_gsd].'_'.$wtrack;
}

if ($action=="bulletin_popup")
{
	echo load_html_head_contents("WS Popup Info", "../../", 1, 1,'',1,'');
	extract($_REQUEST);
	?>
		<script>
			function js_set_value(id)
			{ 
				//var all_data=id+'_'+buyer_id+'_'+buyer_id+'_'+buyer_id+'_'+buyer_id+'_'+buyer_id;
				//alert(all_data);
				document.getElementById('system_id').value=id;
				parent.emailwindow.hide();
			}
		</script>  
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="system_1" id="system_1" autocomplete="off">
				<table width="960" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
					<thead>                	 
						<th width="130">Buyer Name</th>
						<th width="130">Garments Item</th>
						<th width="100">Style Ref.</th>
						<th width="130">Bulletin Type</th>
						<th width="130">Prod Description</th>
						<th width="80">System ID</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>           
					</thead>
					<tr class="general">
						<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
						<td>
							<input type="hidden" id="system_id" style="width:100px;" >
							<? echo create_drop_down( "cbo_gmt_item", 130, $garments_item,'', 1, "-Select Gmt. Item-","","","","" ); ?>
						</td>
						<td><input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" /></td> 
						<td><? echo create_drop_down( "cbo_bulletin_type", 130, $bulletin_type_arr,'', 1, "-Select Bulletin Type-","","","","" ); ?></td>
						<td><input type="text" style="width:120px" class="text_boxes"  name="txt_search_prod" id="txt_search_prod" /></td>
						<td><input type="text" style="width:70px" class="text_boxes_numeric"  name="txt_system_id" id="txt_system_id" /></td>
						<td>
							<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_gmt_item').value+'_'+document.getElementById('txt_system_id').value+'_'+document.getElementById('txt_search_prod').value+'_'+document.getElementById('cbo_bulletin_type').value, 'bulletin_list_view', 'search_div', 'bundle_wise_operation_track_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:5px"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    exit();
}

if ($action=="bulletin_list_view")
{
	$data=explode('_',$data);
    //var_dump($data);
	$buyer_name_arr=return_library_array( "select id,short_name from lib_buyer", "id","short_name"  );
	$user_arr=return_library_array( "select id,user_name from user_passwd", "id","user_name"  );
	
	if ($data[0]!=0) $buyer_id_cond=" and a.buyer_id='$data[0]'"; else $buyer_id_cond="";
	if (trim($data[1])!="") $search_field_cond=" and LOWER(a.style_ref) like LOWER('%".trim($data[1])."%')"; else $search_field_cond=""; 
	if ($data[2]!=0) $gmt_item_cond=" and a.gmts_item_id='$data[2]'"; else { $gmt_item_cond=""; }
	if (trim($data[3])!="") $system_id_cond=" and a.system_no_prefix='".trim($data[3])."'"; else $system_id_cond=""; 
	if (trim($data[4])!="") $prod_id_cond=" and a.prod_description like '%".trim($data[4])."%' "; else $prod_id_cond=""; 
	if ($data[5]!=0) $bulletin_type_cond=" and a.bulletin_type='$data[5]'"; else { $bulletin_type_cond=""; }

	$arr=array (2=>$buyer_name_arr,6=>$garments_item,7=>$color_type,8=>$bulletin_type_arr,11=>$user_arr,12=>$user_arr);
	
    $sql ="SELECT a.id, a.system_no_prefix, a.extention_no, a.prod_description, a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.updated_by, max(b.row_sequence_no) as seq_no, a.custom_style, a.remarks, a.fabric_type, a.color_type, a.approved, a.applicable_period
    FROM ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b 
    where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $prod_id_cond $buyer_id_cond $search_field_cond $gmt_item_cond  $system_id_cond $bulletin_type_cond
    group by a.id, a.system_no_prefix, a.extention_no, a.prod_description, a.bulletin_type, a.is_copied, a.buyer_id, a.style_ref, a.working_hour, a.gmts_item_id, a.operation_count, a.mc_operation_count, a.total_smv, a.tot_mc_smv, a.tot_manual_smv, a.tot_finishing_smv, a.product_dept, a.inserted_by, a.updated_by, a.custom_style, a.remarks, a.fabric_type, a.color_type, a.approved, a.applicable_period order by a.id DESC";
    //echo $sql;
    
	echo create_list_view("list_view", "GSD ID, Ext. No, Buyer, Style Ref., Custom Style, Prod Description, Gmt. Item, Color Type, Bulletin Type, Working Hour, Total SMV, Inserted by, Updated by", "40,40,70,100,80,110,110,60,70,50,60,65","980","250",0, $sql , "js_set_value", "id","",1,"0,0,buyer_id,0,0,0,gmts_item_id,color_type,bulletin_type,0,0,inserted_by,updated_by", $arr,"system_no_prefix,extention_no,buyer_id,style_ref,custom_style,prod_description,gmts_item_id,color_type,bulletin_type,working_hour,total_smv,inserted_by,updated_by","","",'0,0,0,0,0,0,0,0,0,1,2,0,0');

	exit();
}

if ($action=="populate_data_from_ws_popup")
{
     $sql= "SELECT id, system_no_prefix, extention_no, buyer_id, style_ref, gmts_item_id, total_smv FROM ppl_gsd_entry_mst where id='$data'";
	 //echo $sql; die;
	 $data_array=sql_select($sql);
	 foreach ($data_array as $row)
	 {
		 echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		 echo "$('#txt_bulletin_no').val('".$row[csf("system_no_prefix")]."');\n";
		 echo "$('#txt_style_no').val('".$row[csf("style_ref")]."');\n";
		 //echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
		 //echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";
	 }
	 exit();
}

$new_conn=integration_params(2);

if ($action=="load_drop_down_location_hrm")
{
  echo create_drop_down( "cbo_location_name",130,"select id,location_name from lib_location where company_id=$data and status_active=1 and is_deleted=0","id,location_name",1,"-- Select Location --",$selected,"load_drop_down('bundle_wise_operation_track_controller', this.value,'load_drop_down_division','division_td_hrm');","","","","","","",$new_conn );
  exit();
}

if ($action=="load_drop_down_division")
{
    echo create_drop_down("cbo_division_name",130,"select id,division_name from lib_division where location_id=$data and status_active=1 and is_deleted=0","id,division_name",1,"-- Select Division --",$selected,"load_drop_down('bundle_wise_operation_track_controller',this.value,'load_drop_down_department','department_td_hrm');","","","","","","",$new_conn );
	exit();
}

if ($action=="load_drop_down_department")
{
   echo create_drop_down("cbo_dept_name",130,"select id,department_name from lib_department where division_id=$data and status_active=1 and is_deleted=0","id,department_name",1,"-- Select Department --",$selected,"load_drop_down( 'bundle_wise_operation_track_controller',this.value, 'load_drop_down_section', 'section_td_hrm');","","","","","","",$new_conn );
   exit();
}

if ($action=="load_drop_down_section")
{
   echo create_drop_down("cbo_section_name",130,"select id,section_name from lib_section where department_id=$data and status_active=1 and is_deleted=0","id,section_name",1,"-- Select Section --",$selected,"","","","","","","",$new_conn );
   exit();
}

if ($action=="operator_popup")
{
	echo load_html_head_contents("Operator Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(str)
		{
			//alert(str);
			$("#hidden_emp_number").val(str);
			parent.emailwindow.hide(); 
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:950px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:950px;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="870" border="1" rules="all" class="rpt_table" align="center">
	                <thead>
		                <th width="150">Company</th>
		                <th width="130">Location</th>
		                <th width="130">Division</th>
		                <th width="130">Department</th>
		                <th width="130">Section</th>
		            	<th width="100">Employee Code</th>
		                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" id="hidden_emp_number" /></th>           
		            </thead>
	                <tr class="general">
	                    <td><? echo create_drop_down( "cbo_company_name", 150, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 order by company_name", "id,company_name", 1, "--- Select Company ---", $selected,"load_drop_down('bundle_wise_operation_track_controller', this.value,'load_drop_down_location_hrm','location_td_hrm');","","","","","","",$new_conn ); ?></td>
	                    <td id="location_td_hrm"><? echo create_drop_down( "cbo_location_name", 130, $blank_array,"", 1, "-- Select Location --", $selected ); ?></td>
		                <td id="division_td_hrm"><? echo create_drop_down( "cbo_division_name", 130,$blank_array ,"", 1, "-- Select Division --", $selected ); ?></td> 
		                <td id="department_td_hrm"><? echo create_drop_down( "cbo_dept_name", 130,$blank_array ,"", 1, "-- Select Department --", $selected ); ?></td>   
		                <td id="section_td_hrm"><? echo create_drop_down( "cbo_section_name", 130,$blank_array ,"", 1, "-- Select Section --", $selected ); ?></td>
		           
		                <td><input type="text" id="src_emp_code" name="src_emp_code" class="text_boxes" style="width:90px;" ></td> 
		                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_division_name').value+'_'+document.getElementById('cbo_dept_name').value+'_'+document.getElementById('cbo_section_name').value+'_'+document.getElementById('src_emp_code').value, 'create_emp_search_list_view','search_div','bundle_wise_operation_track_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
		                </td>
		            </tr> 
	           </table>
	           <div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_emp_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$location = $ex_data[1];
	$division = $ex_data[2];
	$department = $ex_data[3];
	$section = $ex_data[4];
	$emp_code = $ex_data[5];

 	//$sql_cond="";
	if( $company!=0 ) $company=" and company_id=$company"; else $company="";
	if( $location!=0 ) $location=" and location_id=$location"; else $location="";
	if( $division!=0 ) $division=" and division_id=$division"; else $division="";
	if( $department!=0 ) $department=" and department_id=$department"; else $department="";
	if( $section!=0 ) $section=" and section_id=$section"; else $section="";
	if( $emp_code!='' ) $emp_code=" and emp_code=$emp_code"; else $emp_code="";
	
	if($db_type==2 || $db_type==1 )
	{
    	$sql = "select emp_code,id_card_no,(first_name||' '||middle_name|| ' ' || last_name) as emp_name,designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
    }
	else if($db_type==0)
	{
		$sql = "select emp_code,id_card_no, concat(first_name,' ',middle_name,' ',last_name) as emp_name, designation_id, company_id, location_id, division_id,department_id,section_id from hrm_employee where status_active=1 and is_deleted=0 $company $location $division $department $section $line_no $emp_code";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name',$new_conn);
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name',$new_conn);
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name',$new_conn);
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name',$new_conn);
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name',$new_conn);
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation',$new_conn);

	$arr=array(3=>$designation_arr,4=>$company_arr,5=>$location_arr,6=>$division_arr,7=>$department_arr,8=>$section_arr);
		
	echo create_list_view("list_view", "Emp Code,ID Card,Employee Name,Designation,Company,Location,Division,Department,Section", "65,65,100,90,110,140,80,100,80","930","250",0, $sql, "js_set_value", "emp_code,id_card_no,emp_name", "", 1, "0,0,0,designation_id,company_id,location_id,division_id,department_id,section_id", $arr , "emp_code,id_card_no,emp_name,designation_id,company_id,location_id,division_id,department_id,section_id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0',"","",$new_conn) ;
	exit();
}

if($action=="populate_operator_data")
{
	if($db_type==2 || $db_type==1 )
	{
    	$sql = "select emp_code, id_card_no,(first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0 and id_card_no='$data'";
    }
	else if($db_type==0)
	{
		$sql = "select id_card_no, concat(first_name,' ',middle_name,' ',last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0 and id_card_no='$data'";
	}
	//echo $sql;
	$result = sql_select($sql,'',$new_conn);	
	
	foreach ($result as $row)
	{ 
		echo "document.getElementById('txt_operator_id').value 			= '".$row[csf("id_card_no")]."';\n";
		echo "document.getElementById('txt_operator_name').value 		= '".$row[csf("emp_name")]."';\n";
	}
	exit();
}

if($action=='populate_mst_data')
{
	$exData=explode("_",$data);
	/*$barcode_no="'".implode("','",explode(",",$exData[0]))."'";
	echo "select mst_barcode_no from ppl_cut_lay_bundle_operation where status_active=1 and is_deleted=0 and barcode_no in ($barcode_no)";
	$result=sql_select("select mst_barcode_no from ppl_cut_lay_bundle_operation where status_active=1 and is_deleted=0 and barcode_no in ($barcode_no)");

	$datastr="";
	foreach ($result as $row)
	{ 
		$datastr.=$row[csf('mst_barcode_no')].',';
	}
	$barcode_noCond="";*/
	//$barcode_noCond="'".implode("','",array_unique(array_filter(explode(",",$datastr))))."'";
	$barcode_noCond="'".implode("','",explode(",",$exData[0]))."'";
	
	$sql="select a.company_id, a.serving_company, a.location, a.floor_id, a.prod_reso_allo, a.sewing_line, a.production_date, b.buyer_name, b.job_no from pro_garments_production_mst a, wo_po_details_master b, wo_po_break_down c, pro_garments_production_dtls d where d.barcode_no in ($barcode_noCond) and a.id=d.mst_id and a.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.production_type=5 and d.production_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id, a.serving_company, a.location, a.floor_id, a.prod_reso_allo, a.sewing_line, a.production_date, b.buyer_name, b.job_no";
	//echo $sql;
	$data_array=sql_select($sql);
	
	foreach ($data_array as $row)
	{
		echo "$('#cbo_working_company').val('".$row[csf("serving_company")]."');\n";
		echo "load_drop_down( 'requires/bundle_wise_operation_track_controller', '".$row[csf('serving_company')]."', 'load_drop_down_location', 'working_location_td' );\n";
		echo "$('#cbo_working_location').val('".$row[csf("location")]."');\n";
		echo "load_drop_down('requires/bundle_wise_operation_track_controller', '".$row[csf('location')]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$row[csf("floor_id")]."');\n";
		echo "load_drop_down( 'requires/bundle_wise_operation_track_controller', '".$row[csf("serving_company")].'_'.$row[csf("location")].'_'.$row[csf("floor_id")].'_'.$row[csf("production_date")].'_'.$row[csf("prod_reso_allo")]."', 'load_drop_down_line', 'line_td');\n";
		echo "$('#hidd_prod_reso_allo').val('".$row[csf("prod_reso_allo")]."');\n";
		echo "$('#txt_production_date').val('".change_date_format($row[csf("production_date")])."');\n";
		echo "$('#cbo_line_no').val('".$row[csf("sewing_line")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_name")]."');\n";
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		
		exit();
	}
}

if($action=="bundle_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	list($shortName,$ryear,$lot_prifix)=explode('-',$lot_ratio);
	if($ryear=="") $ryear=date("Y",time()); else $ryear=("20$ryear")*1; 
	//echo $style_ref;die;
	if(trim($style_ref)!="") $styDisable="disabled readonly"; else $styDisable="";
	if(trim($production_date)!="") $prodDateDisable="disabled readonly"; else $prodDateDisable="";
	?>
	<script>
		function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
            for (var i = 1; i <= tbl_row_count; i++) {
                if ($("#search" + i).css("display") != 'none') {
					var hddn_data=$("#hddn_data"+i).val();
                    js_set_value(hddn_data);
					//return;
                }
            }
        }
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value(str) 
		{
			var strs=str.split("__");
			//var strs=strs[0];
			//alert(str); return;
			if( $("#hidden_job").val()!="" && $("#hidden_job").val()!=strs[3] ) {
				alert("Job Mixed Not Allow."+strs[3]);
				return;
			}
			else 
			{
				$("#hidden_job").val(strs[3]);
			}

			toggle( document.getElementById( 'search' + strs[0] ), '#FFFFCC' );
			
			if( jQuery.inArray( strs[1], selected_id ) == -1 ) {
				selected_id.push( strs[1] );
				$('#hidden_lot_ratio').val( strs[1] );	
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == strs[1] ) break;
				}
				selected_id.splice( i, 1 );
				
				/*if(selected_id.length==0 && $('#hidden_lot_ratio_pre').val()=="")
					$('#hidden_lot_ratio').val('');*/

			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_bundle_nos').val( id );
		}
		
		function fnc_close()
		{	
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			selected_id = new Array();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1022px;">
			<legend></legend>           
	            <table cellpadding="0" cellspacing="0" width="620" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<th width="140" class="must_entry_caption">W.Company</th>
	                    <th width="60">Job Year</th>
	                    <th width="90">Job No</th>                  
	                    <th width="90" class="must_entry_caption">Style Ref.</th>
	                    <th width="90">Bundle No</th>
                        <th width="70" class="must_entry_caption">Production Date</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                            <input type="hidden" name="hidden_bundle_nos" id="hidden_bundle_nos"> 
	                    </th>
	                </thead>
	                <tr class="general">
	                	<td><? echo create_drop_down( "cbo_company_name",140, "select id, company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1,"-- Select --", $wcompany_id, "",0 ); ?></td>
	                    <td><? echo create_drop_down( "cbo_lot_year", 60, $year,'', "", '-- Select --',$ryear, "" ); ?></td>  				
	                    <td><input type="text" style="width:80px" class="text_boxes" name="txt_job_no" id="txt_job_no" />	</td> 				
	                    <td><input type="text" name="txt_lot_no" id="txt_lot_no" style="width:80px" value="<?=$style_ref; ?>" <?=$styDisable; ?> class="text_boxes" /></td>
	                    <td>
                        	<input type="hidden" name="hidden_job" value="<?=$job_no; ?>" id="hidden_job" />
                        	<input type="hidden" name="hidden_lot_ratio_pre"  value="<?php echo $lot_ratio; ?>" id="hidden_lot_ratio_pre"  />
				            <input type="text" name="bundle_no" id="bundle_no" style="width:80px" class="text_boxes" />
	                    </td>  	
                         <td><input type="text" name="txt_production_date" id="txt_production_date" style="width:60px" value="<?=$production_date; ?>" <?=$prodDateDisable; ?> class="datepicker" /></td>	
	            		<td>
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<?=trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<?=trim($lot_ratio,','); ?>'+'_'+'<?=trim($bulletin_id,','); ?>'+'_'+'<?=trim($operation_id,','); ?>'+'_'+'<?=trim($job_no,','); ?>'+'_'+'<?=trim($buyer,','); ?>'+'_'+document.getElementById('txt_production_date').value, 'create_bundle_search_list_view','search_div','bundle_wise_operation_track_controller','setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:70px;" />
	                     </td>
	                </tr>
	           </table>
	           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		/*if($("#hidden_lot_ratio").val()!="")
		{
			show_list_view (document.getElementById('cbo_company_name').value+'_'+document.getElementById('bundle_no').value+'_'+'<?//=trim($bundleNo,','); ?>'+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_lot_no').value+'_'+document.getElementById('cbo_lot_year').value+'_'+'<? //=trim($lot_ratio,','); ?>'+'_'+'<? //=trim($bulletin_id,','); ?>'+'_'+'<? //=trim($operation_id,','); ?>','create_bundle_search_list_view','search_div','bundle_wise_operation_track_controller','setFilterGrid(\'tbl_list_search\',-1); reset_hide_field();')
		}*/
	</script>
	</html>
	<?
	exit();
}

if($action=="create_bundle_search_list_view")
{
 	$ex_data 				= explode("_",$data);
	$company 				= $ex_data[0];
	$selectedBuldle			="'".implode("','",explode(",",$ex_data[2]))."'";
	$job_no					=$ex_data[3];
	$style_no				=$ex_data[4];
	$jyear 					=$ex_data[5];
	$bulletin_id			=$ex_data[7];
	$operation_id			=$ex_data[8];
	$fulljob				=$ex_data[9];
	$buyer					=$ex_data[10];
	$production_date		=$ex_data[11];
	
	if(trim($ex_data[1]))	$bundle_no_cond = " and a.bundle_no='".trim($ex_data[1])."'";

	if( trim($ex_data[0])=='' || trim($ex_data[0])==0)
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select W. Company First. </h2>";
		exit();
	}

	/*if( trim($ex_data[1])=='' && trim($ex_data[3])==''  && trim($ex_data[4])=='')
	{
		echo "<h2 style='color:#D00; text-align:center;'>Please Select Job No Or  Lot No Or Bundle No. </h2>";
		exit();
	} */
	$pordDateCond= "";
	if($production_date!="")
	{
		if($db_type==0) $pordDateCond= " and a.production_date ='".change_date_format($production_date,'yyyy-mm-dd')."'";
		else if($db_type==2) $pordDateCond= " and a.production_date='".change_date_format($production_date,'yyyy-mm-dd','-',1)."'";
	}
	
	$cutCon=''; $receiveCon=''; $cutCon='';
	if ($lot_no != '') $cutCon = " and d.cutting_no like'%".$lot_no."%'";
    if ($full_lot_no != '') $cutpCon = " and b.cut_no='".$full_lot_no."'";
	
	if($job_no!='') $jobCon=" and f.job_no_prefix_num='$job_no'"; else $jobCon="";
	if($fulljob!='') $jobPreCon=" and f.job_no='$fulljob'"; else $jobPreCon="";
	if($style_no!='') $style_noCon=" and f.style_ref_no='$style_no'"; else $style_noCon="";
	if($buyer!=0) $buyerCon=" and f.buyer_name='$buyer'"; else $buyerCon="";
	if($db_type==0) $yearCond="and YEAR(f.insert_date)=$jyear"; else if($db_type==2) $yearCond=" and to_char(f.insert_date,'YYYY')=$jyear";
	if(str_replace("'","",$selectedBuldle)!="") $selected_bundle_cond=" and c.bundle_no not in (".$selectedBuldle.")";
	//echo "SELECT a.barcode_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a where b.id=a.mst_id and a.production_type=56 and b.production_type=56 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutpCon "; 
	//$output_bundle_arr=return_library_array("SELECT a.barcode_no, a.bundle_no from pro_garments_production_mst b, pro_garments_production_dtls a where b.id=a.mst_id and a.production_type=56 and b.production_type=56 and b.status_active=1 and b.is_deleted=0 $bundle_no_cond $cutpCon ", 'barcode_no', 'bundle_no');
	$scanned_bundle_sql = sql_select("select a.bulletin_id, b.operation_id, b.barcode_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.bulletin_id='$bulletin_id' and b.operation_id='$operation_id'");
	//echo "select a.bulletin_id, a.operation_id, b.barcode_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.bulletin_id='$bulletin_id' and a.operation_id='$operation_id'";
	$scanned_bundle_arr=array();
	foreach($scanned_bundle_sql as $row)
	{
		$scanned_bundle_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	unset($scanned_bundle_sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="50">Year</th>
            <th width="50">Job No</th>
            <th width="90">Order No</th>
            <th width="100">Gmts Item</th>
            <th width="100">Country</th>
            <th width="100">Color</th>
            <th width="50">Size</th>
            <th width="85">Bundle No</th>
            <th width="70">Bundle Qty.</th>
            <th width="90">Barcode No</th>
            <th width="80">Sewing Line</th>
            <th>Prod. Date</th>
        </thead>
	</table>
	<div style="width:1010px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table" id="tbl_list_search">  
        	<?
			$i=1;
			$sql="SELECT c.cut_no, c.bundle_no, c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, sum(c.production_qnty) as qty, e.po_number, a.production_date, a.prod_reso_allo, a.sewing_line from pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where d.job_no_mst=f.job_no and a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id $orderCon $bndlCon and c.production_type=5 and a.status_active=1 and a.is_deleted=0 and c.barcode_no is not null and a.serving_company=$company $jobCon $jobPreCon $yearCond $cutCon $line_id_cond $style_noCon $selected_bundle_cond $buyerCon $pordDateCond group by c.cut_no, c.bundle_no,c.barcode_no, d.po_break_down_id, d.job_no_mst, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.bundle_no, e.po_number, a.production_date, a.prod_reso_allo, a.sewing_line order by c.cut_no,length(c.bundle_no) asc, c.bundle_no asc";// 
			
			//echo $sql;
			$result = sql_select($sql);	
			foreach ($result as $val)
			{
				//$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
				$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
				$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
				$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
				//$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
				//$operation_id_arr[$val[csf('operation_id')]] 	=$val[csf('operation_id')];
			}

			$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
			$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
			$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
			$lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name"); 
			$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
			if(count($result)==0) { echo "<h2 style='color:#D00; text-align:center;'>Sewing Output Not Found. </h2>"; }

			foreach ($result as $row)
			{  
				if($scanned_bundle_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
					$sewing_line="";
					if($row[csf('prod_reso_allo')]==1)
					{
						$sline=explode(',',$prod_reso_arr[$row[csf('sewing_line')]]);
						foreach($sline as $sln)
						{
							if($sewing_line=="") $sewing_line=$lineArr[$sln]; else $sewing_line.=','.$lineArr[$sln];
						}
					}
					else $sewing_line=$lineArr[$row[csf('sewing_line')]];
					?>
                    <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$i.'__'.$row[csf('barcode_no')].'__'.$row[csf('cut_no')].'__'.$row[csf('job_no_mst')].'__'.$row[csf('sewing_line')].'__'.change_date_format($row[csf('production_date')]); ?>')"> 
                        <td width="30"><?=$i; ?>
                        	<input type="hidden" name="txt_individual" id="txt_individual<?=$i; ?>" alue="<?=$row[csf('barcode_no')]; ?>"/>
                            <input type="hidden" name="txt_individual_name" id="txt_individual_name<?=$i; ?>" value="<?=$row[csf('cut_no')]; ?>"/>
                            <input type="hidden" name="hddn_data" id="hddn_data<?=$i; ?>" value="<?=$i.'__'.$row[csf('barcode_no')].'__'.$row[csf('cut_no')].'__'.$row[csf('job_no_mst')]; ?>"/>
                        </td>
                        <td width="50" align="center"><?=$year; ?></td>
                        <td width="50" align="center" title="<?=$row[csf('job_no_mst')]; ?>"><?=$job*1; ?></td>
                        <td width="90" style="word-break:break-all"><?=$row[csf('po_number')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$country_arr[$row[csf('country_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
                        <td width="50" style="word-break:break-all"><?=$size_arr[$row[csf('size_number_id')]]; ?></td>
                        <td width="85" style="word-break:break-all"><?=$row[csf('bundle_no')]; ?></td>
                        <td width="70" align="center"><?=$row[csf('qty')]; ?></td>
                        <td width="90" style="word-break:break-all"><?=$row[csf('barcode_no')]; ?></td>
                        <td width="80" style="word-break:break-all"><?=$sewing_line; ?></td>
                        <td style="word-break:break-all"><?=change_date_format($row[csf('production_date')]); ?></td>
                    </tr>
                    <?
                    $i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="900">
        <tr>
            <td align="center" >
               <span style="float:left;"><input type="checkbox" name="check_all" id="check_all" onClick="check_all_data();" />Check / Uncheck All </span>
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();	
}

if($action=="populate_bundle_data")
{
	$ex_data = explode("**", $data);
    $bundle = explode(",", $ex_data[0]);
    $mst_id = $ex_data[2];
    $bundle_nos = "'" . implode("','", $bundle) . "'";
    $vscan=$ex_data[4];
   // $source_cond=$ex_data[5];
	$bulletin_id=$ex_data[6];
	$operation_id=$ex_data[7];
	$jobno=$ex_data[8];
	//echo $mst_id.'--'.str_replace("'",'',$bundle_nos);
	if($mst_id!="" && str_replace("'",'',$bundle_nos)=='')
	{
		$bundle_nos='';
		$sql_bundle="select b.id, b.operator_id, b.barcode_no, b.rate, b.amount from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and a.bulletin_id='$bulletin_id' and b.operation_id='$operation_id' and a.id='$mst_id' and b.status_active=1 and b.is_deleted=0";
		$sql_bundle_res=sql_select($sql_bundle); $bundle_count=0; $ticketDataArr=array();
		foreach($sql_bundle_res as $row)
		{
			$bundle_count++;
			$bundle_nos.="'".$row[csf("barcode_no")]."',";
			$ticketDataArr[$row[csf("barcode_no")]]['id']=$row[csf("id")];
			$ticketDataArr[$row[csf("barcode_no")]]['opid']=$row[csf("operator_id")];
			$ticketDataArr[$row[csf("barcode_no")]]['rate']=$row[csf("rate")];
			$ticketDataArr[$row[csf("barcode_no")]]['amount']=$row[csf("amount")];
		}
		unset($sql_bundle_res);
	}
	//die;
	$bundle_nos=implode(",",array_filter(explode(',',$bundle_nos)));
	//echo $bundle_nos;
	$bundle_count=count(explode(",",$bundle_nos)); $bundle_nosCond=""; $scnbundle_nos_cond=""; $cutbundle_nos_cond="";
	if($db_type==2 && $bundle_count>400)
	{
		$bundle_nosCond=" and (";
		$scnbundle_nos_cond=" and (";
		$cutbundle_nos_cond=" and (";
		$bundleArr=array_chunk(explode(",",$bundle_nos),399);
		foreach($bundleArr as $bundleNos)
		{
			$bundleNos=implode(",",$bundleNos);
			$bundle_nosCond.=" c.barcode_no in($bundleNos) or ";
			$scnbundle_nos_cond.=" b.barcode_no in($bundleNos) or ";
			$cutbundle_nos_cond.=" barcode_no in($bundleNos) or ";
		}
		$bundle_nosCond=chop($bundle_nosCond,'or ');
		$bundle_nosCond.=")";
		
		$scnbundle_nos_cond=chop($scnbundle_nos_cond,'or ');
		$scnbundle_nos_cond.=")";
		
		$cutbundle_nos_cond=chop($cutbundle_nos_cond,'or ');
		$cutbundle_nos_cond.=")";
	}
	else
	{
		$bundle_nosCond=" and c.barcode_no in ($bundle_nos)";
		$scnbundle_nos_cond=" and b.barcode_no in ($bundle_nos)";
		$cutbundle_nos_cond=" and barcode_no in ($bundle_nos)";
	}

	$scanned_bundle_arr=array();
    if($mst_id=="") $scanned_bundle_arr = return_library_array("select b.barcode_no, b.barcode_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and a.bulletin_id='$bulletin_id' and b.operation_id='$operation_id' $scnbundle_nos_cond and b.status_active=1 and b.is_deleted=0 group by b.barcode_no, b.bundle_no","barcode_no","barcode_no");
	
	$emp_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee",'id_card_no','emp_name',$new_conn);
	
    $year_field = "";
    if ($db_type == 0) $year_field = "YEAR(f.insert_date)"; else if ($db_type == 2) $year_field = "to_char(f.insert_date,'YYYY')";
	if($jobno!="") $jobnoCond="and e.job_no_mst='$jobno'"; else $jobnoCond="";
     
    $last_operation=array();
	$sql="SELECT max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, sum(c.production_qnty) as qty, e.po_number from pro_gmts_delivery_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e where a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and c.production_type=5 $str_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 $bundle_nosCond $jobnoCond group by d.id, e.id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";//f.company_name=$ex_data[3] and 
	//echo $sql;		
	$result = sql_select($sql);	
	foreach ($result as $val)
	{
		//$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
		$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
		$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
		$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
		//$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
		//$operation_id_arr[$val[csf('operation_id')]] 	=$val[csf('operation_id')];
		$bundle_no_arr[$val[csf('bundle_no')]] 			="'".$val[csf('bundle_no')]."'";
	}
	
	$bundleQtyArr = return_library_array("select bundle_no, size_qty from ppl_cut_lay_bundle where bundle_no in (".implode(',', $bundle_no_arr).")", 'bundle_no', 'size_qty');
	$size_arr=return_library_array( "select id, size_name from lib_size where id in (".implode(',', $size_id_arr).")",'id','size_name');
	$color_arr=return_library_array( "select id, color_name from lib_color where id in (".implode(',', $color_id_arr).")", "id", "color_name");
	$country_arr=return_library_array( "select id, country_name from lib_country where id in (".implode(',', $country_id_arr).")",'id','country_name');
	
	$result = sql_select($sql);
	$count=count($result);
	$i=$ex_data[1]+$count;
	foreach ($result as $row)
	{
		$dtls_id=$opid=$rate=$amount='';
		$dtls_id=$ticketDataArr[$row[csf("barcode_no")]]['id'];
		$opid=$ticketDataArr[$row[csf("barcode_no")]]['opid'];
		$rate=$ticketDataArr[$row[csf("barcode_no")]]['rate'];
		$amount=$ticketDataArr[$row[csf("barcode_no")]]['amount'];
		//echo $scanned_bundle_arr[$row[csf('bundle_no')]];
		if(trim($scanned_bundle_arr[$row[csf('barcode_no')]])=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			//list($shortName,$year,$job)=explode('-',$row[csf('job_no_mst')]);
			?>
			<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i;?>"> 
                <td width="25"><?=$i; ?></td>
                <td width="70" id="bundle_<?=$i; ?>" align="center"><?=$row[csf('bundle_no')]; ?></td>
                <td width="90" id="barcodeno_<?=$i; ?>"><?=$row[csf('barcode_no')]; ?></td>
                <td width="75"><input name="txtOperatorId[]" id="txtOperatorId_<?=$i; ?>" class="text_boxes" type="text" style="width:63px" onDblClick="openmypage_operator(<?=$i; ?>);" value="<?=$opid; ?>" placeholder="Br" /></td>
                <td width="90" id="operatorName_<?=$i; ?>" style="word-break:break-all"><?=$emp_arr[$opid]; ?>&nbsp;</td>
                <td width="90" style="word-break:break-all;"><?=$row[csf('po_number')]; ?></td>
                <td width="100" style="word-break:break-all;"><?=$garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="100" style="word-break:break-all;"><?=$color_arr[$row[csf('color_number_id')]]; ?></td>
                <td width="50" align="center" style="word-break:break-all;"><?=$size_arr[$row[csf('size_number_id')]]; ?></td>
                <td width="50" align="center"><?=$bundleQtyArr[$row[csf('bundle_no')]]; ?></td>
                <td width="50" align="center"><?=$row[csf('qty')]; ?></td>
                <td width="40" id="rate_<?=$i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="txtRate[]" id="txtRate_<?=$i; ?>" style="width:28px" value="<?=$rate; ?>" onBlur="fnc_calculate_amount(<?=$i; ?>); fnc_copyval(2,this.value,<?=$i; ?>)" /></td>
                <td width="50" id="amount_<?=$i; ?>" align="center"><input type="text" class="text_boxes_numeric" name="txtAmount[]" id="txtAmount_<?=$i; ?>" style="width:35px" value="<?=$amount; ?>" readonly/></td>
                <td id="button_1" align="center">
                    <input type="button" id="decrease_<?=$i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i; ?>);" />
                    <input type="hidden" name="txtcutNo[]" id="txtcutNo_<?=$i; ?>" value="<?=$row[csf('cut_no')]; ?>"/>
                    <input type="hidden" name="txtbarcode[]" id="txtbarcode_<?=$i; ?>" value="<?=$row[csf('barcode_no')]; ?>"/>
                    <input type="hidden" name="txtcolorSizeId[]" id="txtcolorSizeId_<?=$i; ?>" value="<?=$row[csf('colorsizeid')]; ?>"/>
                    <input type="hidden" name="txtorderId[]" id="txtorderId_<?=$i; ?>" value="<?=$row[csf('po_id')]; ?>"/>
                    <input type="hidden" name="txtgmtsitemId[]" id="txtgmtsitemId_<?=$i; ?>" value="<?=$row[csf('item_number_id')]; ?>"/>
                    <input type="hidden" name="txtcountryId[]" id="txtcountryId_<?=$i; ?>" value="<?=$row[csf('country_id')]; ?>"/>
                    <input type="hidden" name="txtcolorId[]" id="txtcolorId_<?=$i; ?>" value="<?=$row[csf('color_number_id')]; ?>"/>
                    <input type="hidden" name="txtsizeId[]" id="txtsizeId_<?=$i; ?>" value="<?=$row[csf('size_number_id')]; ?>"/>
                    <input type="hidden" name="txtqty[]" id="txtqty_<?=$i; ?>" value="<?=$row[csf('qty')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<?=$i; ?>" value="<?=$dtls_id; ?>"/> 
                    <input type="hidden" name="isRescan[]" id="isRescan_<?=$i; ?>" value="0"/>
                </td>
            </tr>
			<?
			$i--;
		}
	}
    exit(); 
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con= connect();
		$delivery_basis =3;
		if($db_type==0)	{ mysql_query("BEGIN"); }
		if($db_type==0) $year_cond="YEAR(insert_date)"; else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')"; else $year_cond="";	

		for($j=1;$j<=$tot_row;$j++)
        {   
            $txtbarcode 	="barcodeNo_".$j;       
            $barcodeCheckArr[$$txtbarcode]=$$txtbarcode;       
        }
            
        $barcode_str ="'".implode("','",$barcodeCheckArr)."'";

        $operation_sql="select b.barcode_no, b.bundle_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and a.bulletin_id=$txt_bulletin_id and b.operation_id=$cbo_operation_id and b.barcode_no in ($barcode_str) and b.status_active=1 and b.is_deleted=0 group by b.barcode_no, b.bundle_no"; //and (c.is_rescan=0 or c.is_rescan is null)

        $operation_result = sql_select($operation_sql);
        foreach ($operation_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		unset($operation_result);
		if(str_replace("'",'',$txt_update_id)=='')
		{
			$field_array_mst = "id, sys_prefix, sys_prefix_no, sys_no, working_company, working_location, operation_date, production_date, challan_no, floor_id, prod_reso_allo, sewing_line, bulletin_id, job_no, remarks, inserted_by, insert_date, status_active, is_deleted";
			
			$new_system_id=explode("*", return_next_id_by_sequence("", "pro_operation_track_mst",$con,1,$cbo_working_company,'BOT',0,date("Y",time()),0,0,78,0,0 ));
			//print_r($new_system_id); die;
			if(str_replace("'",'',$txt_challan_no)=='') $challan_no=(int)$new_system_id[2]; else $challan_no=str_replace("'",'',$txt_challan_no);
			$qc_id = return_next_id_by_sequence(  "PRO_OPERATION_TRACK_MST_PK_SEQ", "pro_operation_track_mst", $con );
			$data_array_mst="(".$qc_id.",'".$new_system_id[1]."',".(int)$new_system_id[2].",'".$new_system_id[0]."',".$cbo_working_company.",".$cbo_working_location.",".$txt_operation_date.",".$txt_production_date.",'".$challan_no."',".$cbo_floor.",".$hidd_prod_reso_allo.",".$cbo_line_no.",".$txt_bulletin_id.",".$txt_job_no.",".$txt_remarks.",".$user_id.",'".$pc_date_time."',1,0)";
		}
		else
		{
			$field_array_mst = "working_location*operation_date*production_date*challan_no*bulletin_id*remarks*updated_by*update_date";
			$data_array_mst="".$cbo_working_location."*".$txt_operation_date."*".$txt_production_date."*".$txt_challan_no."*".$txt_bulletin_id."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
			$challan_no=str_replace("'",'',$txt_challan_no);
			$qc_id=str_replace("'",'',$txt_update_id);
		}
		
		$field_array_dtls ="id, mst_id, operation_id, po_id, colorsizeid, item_id, country_id, color_id, size_id, cut_no, barcode_no, bundle_no, operator_id, qc_qty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls="";
		for($j=1; $j<=$tot_row; $j++)
		{
			$txtoperatorId	="txtoperatorId_".$j;
			$bundleNo 		="bundleNo_".$j;
			$txtcutNo 		="txtcutNo_".$j;
			$txtbarcode 	="barcodeNo_".$j;
			$txtcolorSizeId ="txtcolorSizeId_".$j;
			$txtorderId 	="txtorderId_".$j;
			$txtgmtsitemId 	="txtgmtsitemId_".$j;
			$txtcountryId 	="txtcountryId_".$j;
			$txtcolorId 	="txtcolorId_".$j;
			$txtsizeId 		="txtsizeId_".$j;
			$txtBundleQty 	="txtBundleQty_".$j;
			$txtqty 		="txtqty_".$j;
			$txtRate 		="txtRate_".$j;
			$txtAmount 		="txtAmount_".$j;
			$dtlsId 		="dtlsId_".$j;
			$isRescan 		="isRescan_".$j;
	
			if($duplicate_bundle[$$bundleNo]=='')
            {
				$qc_dtls_id = return_next_id_by_sequence(  "PRO_OPERATION_TRACK_DTLS_PK_SEQ", "pro_operation_track_dtls", $con );
				if($data_array_dtls!='') $data_array_dtls.=",";
				$data_array_dtls.="(".$qc_dtls_id.",".$qc_id.",".$cbo_operation_id.",'".$$txtorderId."','".$$txtcolorSizeId."','".$$txtgmtsitemId."','".$$txtcountryId."','".$$txtcolorId."','".$$txtsizeId."','".$$txtcutNo."','".$$txtbarcode."','".$$bundleNo."','".$$txtoperatorId."','".$$txtqty."',".$$txtRate.",'".$$txtAmount."',".$user_id.",'".$pc_date_time."',1,0)";
			}
		}
				
		$flag=1;
		if(str_replace("'",'',$txt_update_id)=='')
		{
			$rID_mst=sql_insert("pro_operation_track_mst",$field_array_mst,$data_array_mst,1);
			if($rID_mst==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("pro_operation_track_mst",$field_array_mst,$data_array_mst,"id",$txt_update_id,1);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		$rID_dtls=sql_insert("pro_operation_track_dtls",$field_array_dtls,$data_array_dtls,0);
		if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**insert into pro_operation_track_dtls($field_array_dtls) values ".$data_array_dtls; die;
		
		//$bundlerID=sql_insert("pro_cut_delivery_color_dtls",$field_array_bundle,$data_array_bundle,1);
		//echo "10**".$query;die;
		//echo "10**".$rID_mst."**".$rID."**".$rID_dtls."**".$flag;die;
	
		if($db_type==0)
		{  
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$qc_id."**".str_replace("'","",$challan_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$qc_id."**".str_replace("'","",$challan_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
 		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
	
		$mst_id=str_replace("'","",$txt_update_id);
		$txt_chal_no=explode("-",str_replace("'","",$txt_system_no));
		$challan_no=(int) $txt_chal_no[3];
		
		for($j=1;$j<=$tot_row;$j++)
        {   
            $txtbarcode 	="barcodeNo_".$j;       
            $barcodeCheckArr[$$txtbarcode]=$$txtbarcode;       
        }
        $barcode_str ="'".implode("','",$barcodeCheckArr)."'";
		
		$sql_dtls="select b.id from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and a.bulletin_id=$txt_bulletin_id and b.operation_id=$cbo_operation_id and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0 group by b.id";
		$nameArray=sql_select($sql_dtls); $dtls_update_id_array=array();
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}

		$operation_sql="select b.barcode_no, b.bundle_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and a.bulletin_id=$txt_bulletin_id and b.operation_id=$cbo_operation_id and b.mst_id!=$mst_id and b.barcode_no in ($barcode_str) and b.status_active=1 and b.is_deleted=0 group by b.barcode_no, b.bundle_no";

        $operation_result = sql_select($operation_sql);
        foreach ($operation_result as $row)
        {           
            $duplicate_bundle[$row[csf('bundle_no')]]=$row[csf('bundle_no')];
        }
		unset($operation_result);
		
		$field_array_mst = "working_location*operation_date*production_date*challan_no*bulletin_id*remarks*updated_by*update_date";
		$data_array_mst="".$cbo_working_location."*".$txt_operation_date."*".$txt_production_date."*".$txt_challan_no."*".$txt_bulletin_id."*".$txt_remarks."*".$user_id."*'".$pc_date_time."'";
		
		$field_arrup_dtls ="po_id*operation_id*colorsizeid*item_id*country_id*color_id*size_id*cut_no*barcode_no*bundle_no*operator_id*qc_qty*rate*amount*updated_by*update_date";
		
		$field_array_dtls ="id, mst_id, operation_id, po_id, colorsizeid, item_id, country_id, color_id, size_id, cut_no, barcode_no, bundle_no, operator_id, qc_qty, rate, amount, inserted_by, insert_date, status_active, is_deleted";
		$data_array_dtls=""; $data_arrup_dtls="";
		for($j=1;$j<=$tot_row;$j++)
		{
			$txtoperatorId	="txtoperatorId_".$j;
			$bundleNo 		="bundleNo_".$j;
			$txtcutNo 		="txtcutNo_".$j;
			$txtbarcode 	="barcodeNo_".$j;
			$txtcolorSizeId ="txtcolorSizeId_".$j;
			$txtorderId 	="txtorderId_".$j;
			$txtgmtsitemId 	="txtgmtsitemId_".$j;
			$txtcountryId 	="txtcountryId_".$j;
			$txtcolorId 	="txtcolorId_".$j;
			$txtsizeId 		="txtsizeId_".$j;
			$txtBundleQty 	="txtBundleQty_".$j;
			$txtqty 		="txtqty_".$j;
			$txtRate 		="txtRate_".$j;
			$txtAmount 		="txtAmount_".$j;
			$dtlsId 		="dtlsId_".$j;
			$isRescan 		="isRescan_".$j;
			
			if(str_replace("'",'',$$dtlsId)=='')
			{
				if($duplicate_bundle[$$bundleNo]=='')
				{
					$qc_dtls_id = return_next_id_by_sequence(  "PRO_OPERATION_TRACK_DTLS_PK_SEQ", "pro_operation_track_dtls", $con );
					if($data_array_dtls!='') $data_array_dtls.=",";
					$data_array_dtls.="(".$qc_dtls_id.",".$mst_id.",".$cbo_operation_id.",'".$$txtorderId."','".$$txtcolorSizeId."','".$$txtgmtsitemId."','".$$txtcountryId."','".$$txtcolorId."','".$$txtsizeId."','".$$txtcutNo."','".$$txtbarcode."','".$$bundleNo."','".$$txtoperatorId."','".$$txtqty."',".$$txtRate.",'".$$txtAmount."',".$user_id.",'".$pc_date_time."',1,0)";
				}
			}
			else if(str_replace("'",'',$$dtlsId)!='')
			{
				$data_arrup_dtls[str_replace("'",'',$$dtlsId)] =explode("*",("'".$$txtorderId."'*".$cbo_operation_id."*'".$$txtcolorSizeId."'*'".$$txtgmtsitemId."'*'".$$txtcountryId."'*'".$$txtcolorId."'*'".$$txtsizeId."'*'".$$txtcutNo."'*'".$$txtbarcode."'*'".$$bundleNo."'*'".$$txtoperatorId."'*'".$$txtqty."'*'".$$txtRate."'*'".$$txtAmount."'*".$user_id."*'".$pc_date_time."'"));
				$id_arr[]=str_replace("'",'',$$dtlsId);
			}
		}
		
		$flag=1;
		$rID=sql_update("pro_operation_track_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_arrup_dtls!="" && $flag==1)
		{
			$rID1=execute_query(bulk_update_sql_statement("pro_operation_track_dtls", "id",$field_arrup_dtls,$data_arrup_dtls,$id_arr ));
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		if($data_array_dtls!="" && $flag==1)
		{
			$rID_dtls=sql_insert("pro_operation_track_dtls",$field_array_dtls,$data_array_dtls,0);
			//echo "10**insert into pro_linking_operation_dtls($field_array_dtls) values ".$data_array_dtls; die;
			if($rID_dtls==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if(implode(',',$id_arr)!="")
		{
			$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
		}
		else
		{
			$distance_delete_id=implode(',',$dtls_update_id_array);
		}
		if(str_replace("'",'',$distance_delete_id)!="")
		{
			$rID3=execute_query( "update pro_operation_track_dtls set updated_by='$user_id', update_date='".$pc_date_time."', status_active=0, is_deleted=1 where id in ($distance_delete_id) and status_active=1 and is_deleted=0 ",0);
			if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		//echo "10**".bulk_update_sql_statement( "pro_gmts_knitting_issue_dtls", "id", $field_array_color_dtls, $data_array_color_dtls, $color_dtls_id_arr );die;	
		 // echo "10**".$rID_mst_qc .'&&'. $rID_dtls .'&&'. $rID .'&&'. $dtlsrID .'&&'. $defectQ."**".$delete.'&&'. $delete_dtls .'&&'. $delete_qc."**".$delete_defect;oci_rollback($con);die;
		//echo "10**".$rID.'='.$rID1.'='.$rID_dtls.'='.$flag;die;
		
		
		if($db_type==0)
		{  
			if($flag==1)
			{ 
				mysql_query("COMMIT");  
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==1 || $db_type==2 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".$mst_id."**".str_replace("'","",$txt_system_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		$mst_id=str_replace("'","",$txt_system_id);
		
 		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
			else
			{
				oci_rollback($con);
				echo "10**".$mst_id."**".str_replace("'","",$txt_challan_no); 
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="system_number_popup")
{
  	echo load_html_head_contents("Operation Track Info","../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_system_value(strCon ) 
		{
			document.getElementById('update_mst_id').value=strCon;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
    	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <th width="130" class="must_entry_caption">Working Company</th>
                    <th width="100">System ID</th>
                    <th width="100">Cutting No</th>
                    <th width="100">Job No</th>
                    <th width="100">Order No</th>
                    <th width="130" colspan="2">Op. Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" /></th>           
                </thead>
                <tbody>
                    <tr class="general">                    
                        <td><?=create_drop_down( "cbo_company_name", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --","", ""); ?></td>
                        <td><input name="txt_cut_qc" id="txt_cut_qc" class="text_boxes" style="width:90px"  placeholder="Write"/></td>
                        <td>
                            <input type="text" id="txt_cut_no" name="txt_cut_no" style="width:90px"  class="text_boxes" placeholder="Write"/>
                            <input type="hidden" id="update_mst_id" name="update_mst_id" />
                        </td>
                        <td><input name="txt_job_search" id="txt_job_search" class="text_boxes" style="width:90px" placeholder="Write"/></td>
                        <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px" placeholder="Write" /></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" /></td>
                        <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date" /></td>
                        <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_cut_qc').value+'_'+document.getElementById('txt_order_search').value, 'create_system_search_list_view', 'search_div', 'bundle_wise_operation_track_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />				
                        </td>
                    </tr>
                    <tr>                  
                        <td align="center" valign="middle" colspan="8"><? echo load_month_buttons(1); ?></td>
                    </tr>   
                </tbody>
            </table> 
     	<div align="center" valign="top" id="search_div"> </div>  
    </form>
    </div>    
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cut_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$system_no= $ex_data[6];
	$order_no= $ex_data[7];
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
	
    if($db_type==2){ $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from f.insert_date) as year";}
    if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(f.insert_date, '-', 1) as year";}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and f.working_company=".str_replace("'","",$company)."";
	if(str_replace("'","",$cut_no)=="") $cut_nocond=""; else $cut_nocond="and a.cut_qc_prefix_no='".str_replace("'","",$cut_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and f.sys_prefix_no=".trim($system_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and f.operation_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$sql_cond= " and f.operation_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	//$emp_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee",'id_card_no','emp_name',$new_conn);
	$arr=array(6=>$emp_arr);
	
	$sql_order="SELECT f.id, f.sys_prefix_no, a.job_no, f.operation_date, f.working_location, c.job_no_prefix_num, b.cut_num_prefix_no, $year, d.po_number
    FROM pro_gmts_cutting_qc_mst a, ppl_cut_lay_mst b, wo_po_details_master c, wo_po_break_down d, ppl_cut_lay_dtls e, pro_operation_track_mst f, pro_operation_track_dtls g
    where a.cutting_no=b.cutting_no and a.job_no=b.job_no and a.job_no=c.job_no and b.job_no=c.job_no and c.id=d.job_id and b.id=e.mst_id and f.id=g.mst_id and d.id=g.po_id $conpany_cond $cut_nocond $job_cond $sql_cond $order_cond $system_cond and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0
	group by f.id, f.sys_prefix_no, a.job_no, f.operation_date, f.working_location, c.job_no_prefix_num, b.cut_num_prefix_no, f.insert_date, d.po_number
	 order by f.id DESC";
	 // echo $sql_order;
	 
	 //$sql="select a.id, a.sys_prefix_no, a.sys_no, working_location, a.operation_date, a.challan_no, a.bulletin_id, a.job_no from pro_operation_track_mst";
//echo $sql_order;//die;
	echo create_list_view("list_view", "Sys. No,Year,Cut No,Job No,Order No,Operation Date","60,60,60,80,100","750","270",0, $sql_order , "js_set_system_value", "id", "", 1, "0,0,0,0,0,0", $arr, "sys_prefix_no,year,cut_num_prefix_no,job_no,po_number,operation_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,3") ;
	exit();	
}

if($action=='populate_data_from_track')
{
	//$employee_arr=return_library_array( "select id_card_no, (first_name||' '||middle_name|| '  ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0",'id_card_no','emp_name',$new_conn);
	if($db_type==0) $production_hour="operation_time"; else if($db_type==2) $production_hour="TO_CHAR(operation_time,'HH24:MI')";
	//echo "select id, sys_prefix, sys_prefix_no, sys_no, working_company, working_location, operation_date, challan_no, floor_id, prod_reso_allo, sewing_line, bulletin_id, job_no, remarks from pro_operation_track_mst";
	$data_array=sql_select("select id, sys_prefix, sys_prefix_no, sys_no, working_company, working_location, operation_date, production_date, challan_no, floor_id, prod_reso_allo, sewing_line, bulletin_id, job_no, remarks from pro_operation_track_mst where id=$data and status_active=1 and  is_deleted=0");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_system_no').value 				= '".$row[csf("sys_no")]."';\n";
		echo "document.getElementById('txt_update_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_working_company').value 			= '".$row[csf("working_company")]."';\n";
		echo "load_drop_down( 'requires/bundle_wise_operation_track_controller', ".$row[csf("working_company")].", 'load_drop_down_location','working_location_td');";
		echo "document.getElementById('cbo_working_location').value 		= '".$row[csf("working_location")]."';\n";
		echo "load_drop_down('requires/bundle_wise_operation_track_controller', '".$row[csf('working_location')]."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('txt_operation_date').value 			= '".change_date_format($row[csf("operation_date")])."';\n";
		echo "document.getElementById('txt_production_date').value 			= '".change_date_format($row[csf("production_date")])."';\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		echo "$('#txt_bulletin_id').val('".$row[csf("bulletin_id")]."');\n";
		echo "$('#txt_job_no').val('".$row[csf("job_no")]."');\n";
		
		echo "load_drop_down( 'requires/bundle_wise_operation_track_controller', '".$row[csf("working_company")].'_'.$row[csf("working_location")].'_'.$row[csf("floor_id")].'_'.$row[csf("production_date")].'_'.$row[csf("prod_reso_allo")]."', 'load_drop_down_line', 'line_td');\n";
		echo "$('#hidd_prod_reso_allo').val('".$row[csf("prod_reso_allo")]."');\n";
		echo "$('#cbo_line_no').val('".$row[csf("sewing_line")]."');\n";
		
		//echo "document.getElementById('txt_operator_name').value 			= '".$employee_arr[$row[csf("operator_id")]]."';\n";
		
		//echo "document.getElementById('txt_reporting_hour').value 			= '".$row[csf("operation_time")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_remarks').value  				= '".($row[csf("remarks")])."';\n";
		exit();
	}
}

if($action=="operation_list_view")
{
	?>	
	<div style="width:350px;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="340" class="rpt_table">
            <thead>
                <th width="20">SL</th>
                <th width="60">B.ID</th>
                <th width="130">Operation</th>
                <th>Job</th>
            </thead>
         </table>
         <div style="width:340px; max-height:340px; overflow-y:scroll">
         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="table_body">
            <tbody>
		<?  
			$i=1;
			$operationArr=return_library_array("select a.id, a.operation_name from lib_sewing_operation_entry a, ppl_gsd_entry_dtls b where a.id=b.lib_sewing_id and a.status_active=1 and b.is_deleted=0 group by a.id, a.operation_name","id","operation_name" );
			
			$sql = sql_select("select a.id, a.bulletin_id, b.operation_id, a.job_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.id='$data' group by a.id, a.bulletin_id, b.operation_id, a.job_no");  
			foreach($sql as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<?=$row[csf('id')].'_'.$row[csf('bulletin_id')].'_'.$row[csf('operation_id')]; ?>','load_php_data_operation_dtls','requires/bundle_wise_operation_track_controller');" > 
                    <td width="20" align="center"><?=$i; ?></td>
                    <td width="60" align="center"><?=$row[csf('bulletin_id')]; ?></td>
                    <td width="130" style="word-break:break-all;"><?=$operationArr[$row[csf('operation_id')]]; ?></td>
                    <td style="word-break:break-all;"><?=$row[csf('job_no')]; ?></td>
                </tr>
                <?
                $i++;
			}
			?>
            </tbody>
		</table>
       </div>
	<?
	exit();	
}

if($action=="load_php_data_operation_dtls")
{
	//echo $data;
	$ex_data=explode('_',$data);
	//echo "select a.id, a.bulletin_id, b.operation_id, a.job_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.id='$data' group by a.id, a.bulletin_id, b.operation_id, a.job_no";
	$sql = sql_select("select a.id, a.bulletin_id, b.operation_id, a.job_no from pro_operation_track_mst a, pro_operation_track_dtls b where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.id='$ex_data[0]' and b.operation_id='$ex_data[2]' group by a.id, a.bulletin_id, b.operation_id, a.job_no");
	foreach($sql as $row)
	{
		echo "get_php_form_data( '".$row[csf('bulletin_id')]."', 'populate_data_from_ws_popup', 'requires/bundle_wise_operation_track_controller' );\n";
		echo "load_drop_down( 'requires/bundle_wise_operation_track_controller','".$row[csf('bulletin_id')]."', 'load_drop_down_operation', 'operation_td');\n";
		echo "$('#txt_bulletin_id').val('".$row[csf("bulletin_id")]."');\n";
		echo "$('#cbo_operation_id').val('".$row[csf("operation_id")]."');\n";
		echo "fnc_ws_data('".$row[csf("operation_id")]."');\n";
		echo "$('#tbl_details tbody').empty();\n";
		echo "create_row('','browse','');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_operation_track_entry',1,0);\n";
		//echo "set_button_status(1 permission, 'fnc_operation_track_entry',1,0);\n";
	}
}

if($action=="challan_duplicate_check")
{
	$bundle_no="'".implode("','",explode(",",$data))."'";
	$msg=1;
	
	$bundle_count=count(explode(",",$bundle_no)); $bundle_nos_cond="";
	$bundle_nos_cond=" and b.barcode_no in ($bundle_no)";
	//$result=sql_select("select a.cutting_qc_no,b.bundle_no from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bundle_nos_cond");

	$datastr="";
	if(count($result)>0)
	{
		foreach ($result as $row)
		{ 
			$msg=2;
			$datastr=$row[csf('bundle_no')]."*".$row[csf('cutting_qc_no')];
		}
	}
	
	echo rtrim($msg)."_".rtrim($datastr)."_".$search_lot_no;
	exit();
}

//------------------------------------------------------------------------------------------------------
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id in(50) and is_deleted=0 and status_active=1");		 
	echo trim($print_report_format);	
	exit();

}

if($action=='print_search_popup')
{
  	echo load_html_head_contents('Search', '../../', 1, 1, '', '1', '');
	extract($_REQUEST);

	?>
	<script>
		var working_company = '<?php echo $company_name; ?>';

		function js_set_system_value(strCon )
		{
			document.getElementById('update_mst_id').value=strCon;
			parent.emailwindow.hide();
		}

		function calculateDate() {
			// calculate the date so that the time between from_date and to_date should be maximum 10 days
			// also when user select from_date 3, to_date should be 10, when from_date 5, to_date should be 10, when from_date 15, to_date should be 20
			// and so on
			$dateFrom = $('#txt_date_from').val();
			$dateToAdd = 0;
			$dateSplit = $dateFrom.split('-');
			$date = parseInt($dateSplit[0]);
			
			if( ($date % 10) == 0 ) {
				$dateToAdd = 9;
			} else {
				$dateToAdd = (10 - ($date % 10));
			}

			$("#txt_date_to").val(add_days($('#txt_date_from').val(), $dateToAdd));
		}

		function openPrint(data) {
			/*var cbo_company_id = $('#cbo_company_id').val();
			var cbo_working_company = $('#cbo_working_company').val();*/
			
			// var proOperationTrackMstId = id;
			var action = 'print_details';
			var data = data + '_' + "<?php echo $bulletin_id; ?>";

			window.open("bundle_wise_operation_track_controller.php?data=" + data + '&action=' + action, true );
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%; overflow-y:hidden;" >
    	<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table width="310" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <th width="230" colspan="2">Billing Date</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" /></th>
                </thead>
                <tbody>
                    <tr class="general">
                        <td>
                        	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" onchange="calculateDate();" />
                        </td>
                        <td>
                        	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
                        </td>
                        <td>
                        	<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( working_company+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_system_print_list_view', 'search_div', 'bundle_wise_operation_track_controller', '')" style="width:70px;" />
                        </td>
                    </tr>  
                </tbody>
            </table> 
     	<div align="center" valign="top" id="search_div"> </div>
    </form>
    </div>    
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?php
    exit();
}

if($action=='create_system_print_list_view')
{
    $ex_data = explode("_",$data);
	$company = $ex_data[0];
	$from_date = $ex_data[1];
	$to_date = $ex_data[2];
	if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}

	$company_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name');
	
    if($db_type==2){ $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from f.insert_date) as year";}
    if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(f.insert_date, '-', 1) as year";}
	
	if(str_replace("'","",$company)==0) $company_cond=""; else $company_cond="and a.working_company=".str_replace("'","",$company)."";
	/*if(str_replace("'","",$cut_no)=="") $cut_nocond=""; else $cut_nocond="and a.cut_qc_prefix_no='".str_replace("'","",$cut_no)."'  ";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$system_no)=="") $system_cond=""; else $system_cond="and f.sys_prefix_num=".trim($system_no)." $year_cond";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";*/
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$date_cond= " and a.operation_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$date_cond= " and a.operation_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}

	$sql_order = "select distinct a.id, a.working_company, a.operation_date, a.sys_no, a.floor_id, c.grouping, a.job_no, d.style_ref_no, c.po_number, c.file_no
				from pro_operation_track_mst a, pro_operation_track_dtls b, wo_po_break_down c, wo_po_details_master d
				where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0
				$company_cond $date_cond and a.id = b.mst_id and a.job_no=c.job_no_mst and c.job_no_mst = d.job_no and b.po_id = c.id
				order by a.id desc";

	// echo $sql_order;
	
	$sql_result = sql_select($sql_order);
	 
	//$sql="select a.id, a.sys_prefix_no, a.sys_no, working_location, a.operation_date, a.challan_no, a.bulletin_id, a.job_no from pro_operation_track_mst";
	//echo $sql_order;//die;
	/*echo create_list_view("list_view", "Sys. No,Year,Cut No,Job No,Order No,Operation Date","60,60,60,80,100","750","270",0, $sql_order , "js_set_system_value", "id", "", 1, "0,0,0,0,0,0", $arr, "sys_prefix_no,year,cut_num_prefix_no,job_no,po_number,operation_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,3");*/
?>
<div valign="top" id="search_div" align="center">
    <table class="rpt_table" id="rpt_tablelist_view" rules="all" width="760" cellspacing="0" cellpadding="0" border="0">
      <thead>
        <tr>
          <th>W.Company</th>
          <th>Bill Date</th>
          <th>Bill ID</th>
          <th>Floor	Line</th>
          <th>File No</th>
          <th>Ref No</th>
          <th>Job No</th>
          <th>Style</th>
          <th>Order No</th>
          <th>App Status</th>
        </tr>
      </thead>
      <tbody>
      	<?php
      		$i=1;
      		foreach ($sql_result as $row) {
      			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
      	?>
      		<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="openPrint(document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<?php echo $row[csf('id')]; ?>);" >
      			<td><?php echo $company_arr[$row[csf('working_company')]]; ?></td>
      			<td><?php echo $row[csf('operation_date')]; ?></td>
      			<td><?php echo $row[csf('sys_no')]; ?></td>
      			<td><?php echo $row[csf('floor_id')]; ?></td>
      			<td><?php echo $row[csf('file_no')]; ?></td>
      			<td><?php echo $row[csf('grouping')]; ?></td>
      			<td><?php echo $row[csf('job_no')]; ?></td>
      			<td><?php echo $row[csf('style_ref_no')]; ?></td>
      			<td><?php echo $row[csf('po_number')]; ?></td>
      			<td></td>
      		</tr>
      	<?php
      			$i++;
      		}
      	?>
      </tbody>
    </table>
</div>
<?php
	exit();	
}

if($action == 'print_details') {
	extract($_REQUEST);
	$data = explode('_', $data);
	$from_date = $data[0];
	$to_date = $data[1];
	$operationMstId = $data[2];
	$bulletin_id = $data[3];
	$floorName = '';

    $company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", 'id', 'company_name' );
    $location_library=return_library_array( "select id, location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name' );
    $buyer_name_library=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", 'id', 'buyer_name' );
    $operation_library=return_library_array("select a.id, a.operation_name from lib_sewing_operation_entry a, ppl_gsd_entry_dtls b where a.id=b.lib_sewing_id and a.status_active=1 and b.is_deleted=0 group by a.id, a.operation_name", 'id', 'operation_name');
    $floor_library=return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0and production_process=5", 'id', 'floor_name' );
    $line_library=return_library_array( "select id, line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name' );
    $emp_library=return_library_array( "select id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee", 'id_card_no', 'emp_name', $new_conn);
    $color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

    /*if($db_type==2 || $db_type==1 )
	{
    	$operator_library = "select emp_code, id_card_no, (first_name||' '||middle_name|| ' ' || last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0 and id_card_no='$data'";
    }
	else if($db_type==0)
	{
		$operator_library = "select id_card_no, concat(first_name,' ',middle_name,' ',last_name) as emp_name from hrm_employee where status_active=1 and is_deleted=0 and id_card_no='$data'";
	}*/

    /*$sql = "select working_company_id,sys_number,bill_date,company_id,currency,upcharge,grand_total,discount,source,manual_bill
    		from piece_rate_bill_mst
    		where id='$data' and status_active=1 and is_deleted=0";*/   

    $sql_mst = "select distinct a.id, b.id as dtls_id, b.barcode_no, b.amount, a.working_company, a.working_location, d.buyer_name, c.file_no, c.grouping, a.job_no, d.style_ref_no, c.po_number, a.remarks, b.operation_id, b.operator_id, b.operator_name, b.qc_qty, b.rate, c.id as po_break_down_id, a.floor_id, a.sewing_line, b.bundle_no, b.color_id, b.item_id, a.operation_date
                from pro_operation_track_mst a, pro_operation_track_dtls b, wo_po_break_down c, wo_po_details_master d
                where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 and a.id = b.mst_id and a.job_no=c.job_no_mst and c.job_no_mst = d.job_no and b.po_id = c.id and a.id=$operationMstId
                order by b.id";

    // echo $sql_mst;
    $mst_result = sql_select($sql_mst);
    $styleReference = $mst_result[0][csf('style_ref_no')];

    $floorId = $mst_result[0][csf('floor_id')];
    $locationId = $mst_result[0][csf('working_location')];

    if($db_type==0)
	{
		$line_data=return_library_array("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number,a.prod_resource_num", 'id', 'line_number');
	}
	else if($db_type==2 || $db_type==1)
	{
		$line_data=return_library_array("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number,a.prod_resource_num", 'id', 'line_number');
	}

	$floorName = $line_library[$line_data[$mst_result[0][csf('sewing_line')]]];

    $po_arr = array();
    $totalBill = 0;
    foreach ($mst_result as $row) {
    	$po_arr[] = $row[csf('po_break_down_id')];
    	$totalBill += $row[csf('amount')];
    }

    $con = connect();
    $user_id = $_SESSION['logic_erp']["user_id"];
    $type = 915;
    if($db_type==0) { mysql_query("BEGIN"); }
    foreach($po_arr as $po_id) {
        if($po_id!=0) {
            $r_id2=execute_query("insert into tmp_poid(userid, poid, type) values($user_id,$po_id,$type)");
            // echo "insert into tmp_poid (userid, poid, type) values ($user_id,$ord_id,985)";
            // if($pi_id=="") $pi_id=$p_id[csf('pi_id')];else $pi_id.=",".$p_id[csf('pi_id')];
        }            
    }
    if($db_type==0) {
        if($r_id2) {
            mysql_query("COMMIT");  
        }
    }
    if($db_type==2 || $db_type==1) {
        if($r_id2) {
            oci_commit($con);  
        }
    }

    $sql_prev = "select distinct a.id, c.file_no, c.grouping, d.style_ref_no, b.qc_qty, b.rate, b.color_id, b.item_id, b.barcode_no
    	from pro_operation_track_mst a, pro_operation_track_dtls b, wo_po_break_down c, wo_po_details_master d
    	where a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and a.id = b.mst_id and a.job_no = c.job_no_mst and c.job_no_mst = d.job_no and b.po_id = c.id and a.id != $operationMstId and d.style_ref_no='$styleReference'";

    // echo $sql_prev;
    $prev_result = sql_select($sql_prev);

    $prevBill = 0;
    foreach ($prev_result as $row) {
    	$prevBill += ($row[csf('qc_qty')] * $row[csf('rate')]);
    }
    $totalBill += $prevBill;
    $billBalance = ($totalBill - $prevBill);

    /*$sql="SELECT max(c.id) as prdid, d.id as colorsizeid, e.id as po_id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, sum(c.production_qnty) as qty, e.po_number
    from pro_gmts_delivery_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e
    where a.id=c.delivery_mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and c.production_type=5 $str_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0
    group by d.id, e.id, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no,c.barcode_no, e.po_number order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";//f.company_name=$ex_data[3] and */
	//echo $sql;

	$sql="select b.id as production_id, b.cut_no, b.bundle_no, b.barcode_no, b.production_qnty, b.reject_qty, b.bundle_qty,
         a.po_break_down_id, a.order_quantity, a.color_mst_id, a.item_mst_id
			from wo_po_color_size_breakdown a, pro_garments_production_dtls b, tmp_poid c
			where b.color_size_break_down_id=a.id and a.po_break_down_id=c.poid and c.userid=$user_id and c.type=$type and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.id, b.cut_no, b.bundle_no, b.barcode_no, b.bundle_qty, a.po_break_down_id, b.production_qnty, b.reject_qty, a.order_quantity, a.color_mst_id, a.item_mst_id";
	// echo $sql;
	$result = sql_select($sql);	
	foreach ($result as $val)
	{
		//$po_id_arr[$val[csf('po_break_down_id')]] 		=$val[csf('po_break_down_id')];
		$color_id_arr[$val[csf('color_number_id')]] 	=$val[csf('color_number_id')];
		$size_id_arr[$val[csf('size_number_id')]] 		=$val[csf('size_number_id')];
		$country_id_arr[$val[csf('country_id')]] 		=$val[csf('country_id')];
		//$cutting_id_arr[$val[csf('mst_id')]] 			=$val[csf('mst_id')];
		//$operation_id_arr[$val[csf('operation_id')]] 	=$val[csf('operation_id')];
		$bundle_arr[$val[csf('bundle_no')]]['production_qty'] = $val[csf('production_qnty')];
		$bundle_arr[$val[csf('bundle_no')]]['reject_qty'] = $val[csf('reject_qty')];
		$bundle_arr[$val[csf('bundle_no')]]['qc_passed_qty'] = $val[csf('production_qnty')] - $val[csf('reject_qty')];
		$bundle_no_arr[$val[csf('bundle_no')]] 			="'".$val[csf('bundle_no')]."'";
		$order_quantity = $val[csf('order_quantity')];
	}
	
	$bundleQtyArr = return_library_array("select bundle_no, size_qty from ppl_cut_lay_bundle where bundle_no in (".implode(',', $bundle_no_arr).")", 'bundle_no', 'size_qty');

    $bundle_nos='';
	/*$sql_bundle="select b.id, b.operator_id, b.barcode_no, b.rate, b.amount
	from pro_operation_track_mst a, pro_operation_track_dtls b
	where a.id=b.mst_id and a.bulletin_id='$bulletin_id' and a.id=$operationMstId and b.status_active=1 and b.is_deleted=0";
	// echo $sql_bundle;
	$sql_bundle_res=sql_select($sql_bundle); $bundle_count=0; $ticketDataArr=array();
	foreach($sql_bundle_res as $row)
	{
		$bundle_count++;
		$bundle_nos.="'".$row[csf("barcode_no")]."',";
		$ticketDataArr[$row[csf("barcode_no")]]['id']=$row[csf("id")];
		$ticketDataArr[$row[csf("barcode_no")]]['opid']=$row[csf("operator_id")];
		$ticketDataArr[$row[csf("barcode_no")]]['rate']=$row[csf("rate")];
		$ticketDataArr[$row[csf("barcode_no")]]['amount']=$row[csf("amount")];
	}
	unset($sql_bundle_res);

	echo '<pre>';
	print_r($ticketDataArr);
	echo '</pre>';*/

    ?>
    <style>
    	table th {
    		text-align: left;
    	}
    </style>
    <table cellspacing="5" cellpadding="5" border="0" width="900px;">
    	<tr>
    		<td></td>
    		<td></td>
    		<td></td>
    		<td></td>
    		<td>From Date</td>
    		<td><?php echo $from_date; ?></td>
    		<td>To Date</td>
    		<td><?php echo $to_date; ?></td>
    		<td></td>
    		<td></td>
    	</tr>
    </table>
    <table cellspacing="5" cellpadding="5" border="0" width="1250px;">
    	<tr>
    		<th width="100">W.Company</th>
    		<td width="150"><?php echo $company_library[$mst_result[0][csf('working_company')]]; ?></td>
    		<th width="100"></th>
    		<td width="150"></td>
    		<th width="100">Ref Qty</th>
    		<td width="150"><?php echo $order_quantity; ?></td>
    		<td width="100"></td>
    		<td width="100"></td>
    		<td width="100"></td>
    		<td width="100"></td>
    	</tr>
    	<tr>
    		<th>WC. Location</th>
    		<td><?php echo $location_library[$mst_result[0][csf('working_location')]]; ?></td>
    		<td></td>
    		<td></td>
    		<th>Ref Prev. Bill</th>
    		<td><?php echo $prevBill; ?></td>
    		<td></td>
    		<td></td>
    		<td></td>
    		<td></td>
    	</tr>
    	<tr>
    		<th>Buyer</th>
    		<td><?php echo $buyer_name_library[$mst_result[0][csf('buyer_name')]]; ?></td>
    		<td></td>
    		<td></td>
    		<th>Ref TTL.Bill</th>
    		<td><?php echo $totalBill; ?></td>
    		<td></td>
    		<td></td>
    		<td></td>
    		<td></td>
    	</tr>
    	<tr>
    		<th>File No</th>
    		<td><?php echo $mst_result[0][csf('file_no')]; ?></td>
    		<td></td>
    		<td></td>
    		<th>Ref Balance</th>
    		<td><?php echo $billBalance; ?></td>
    		<td></td>
    		<td></td>
    		<td></td>
    		<td></td>
    	</tr>
    	<tr>
    		<th>Ref No</th>
    		<td><?php echo $mst_result[0][csf('grouping')]; ?></td>
    		<td></td>
    		<td></td>
    		<th>Bill Date</th>
    		<td><?php echo $mst_result[0][csf('operation_date')]; ?></td>
    		<td></td>
    		<td></td>
    		<td></td>
    		<td></td>
    	</tr>
    	<tr>
    		<th>Job No</th>
    		<td><?php echo $mst_result[0][csf('job_no')]; ?></td>
    		<th>Order QTY</th>
    		<td><?php echo $order_quantity; ?></td>
    		<th>Style Prev. Bill</th>
    		<td><?php echo $prevBill; ?></td>
    		<th></th>
    		<td></td>
    		<th>Item</th>
    		<td><?php echo $garments_item[$mst_result[0][csf('item_id')]]; ?></td>
    	</tr>
    	<tr>
    		<th>Style</th>
    		<td><?php echo $styleReference; ?></td>
    		<th>Floor</th>
    		<td><?php echo $floor_library[$mst_result[0][csf('floor_id')]]; ?></td>
    		<th>Sty.TTL.Bill</th>
    		<td><?php echo $totalBill; ?></td>
    		<th></th>
    		<td></td>
    		<th>GMT Color</th>
    		<td><?php echo $color_arr[$mst_result[0][csf('color_id')]]; ?></td>
    	</tr>
    	<tr>
    		<th>Order No</th>
    		<td><?php echo $mst_result[0][csf('po_number')]; ?></td>
    		<th>Line</th>
    		<td><?php echo $floorName; ?></td>
    		<th>Style Balance</th>
    		<td><?php echo $billBalance; ?></td>
    		<th></th>
    		<td></td>
    		<td></td>
    		<td></td>
    	</tr>
    	<tr>
    		<th>Remarks</th>
    		<td colspan="9"><?php echo $mst_result[0][csf('remarks')]; ?></td>
    	</tr>
   </table>
   <br>
   <table cellspacing="5" cellpadding="5" border="1" rules="all" width="1250px">
        <thead>
        	<tr>
	            <th>SL</th>
	            <th>Operation Name</th>
	            <th>Operator ID</th>
	            <th>Oparetor Name</th>
	            <th>No of Bndle</th>
	            <th>Bundle Qty. (Pcs)</th>
	            <th>QC Pass Qty. (Pcs)</th>
	            <th>QC Pass Qty. (Dzn)</th>
	            <th>Rate</th>
	            <th>Amount</th>
	            <th>Approved Amount</th>
			</tr>
        </thead>
        <?php
        $sl=1;
        foreach ($mst_result as $row)
        {
        	$qc_passed_qty = $bundle_arr[$row[csf('bundle_no')]]['qc_passed_qty'];
    ?>
       <tr>
            <td align="center"><?php echo $sl;?></td>
            <td><?php echo $operation_library[$row[csf('operation_id')]]; ?></td>
            <td><?php echo $row[csf('operator_id')]; ?></td>
            <td><?php echo $emp_library[$row[csf('operator_id')]]; ?></td>
            <td><?php echo $row[csf('bundle_no')]; ?></td>
            <td><?php echo $bundle_arr[$row[csf('bundle_no')]]['production_qty']; ?></td>
            <td><?php echo $qc_passed_qty; ?></td>
            <td><?php echo number_format($qc_passed_qty/12); ?></td>
            <td align="right"><?php echo $row[csf('rate')]; ?></td>
            <td align="right"><?php echo $row[csf('amount')]; ?></td>
            <td align="right"><?php echo $row[csf('amount')]; ?></td>
        </tr>
    <?php
        $sl++;
    }
        ?>
    </table>
    <?php
	    $r_id3=execute_query("delete from tmp_poid where userid=$user_id and type=$type");
	    if($db_type==0) {
	        if($r_id3) {
	            mysql_query("COMMIT");  
	        }
	    }
	    if($db_type==2 || $db_type==1 ) {
	        if($r_id3) {
	            oci_commit($con);  
	        }
	    }
    disconnect($con);
    die;

exit();
}

?>
