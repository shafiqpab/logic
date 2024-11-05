<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 150, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
}

if($action=="populate_employee_info_data")
{
	$data=explode("**",$data);
	if($data[1]==1) $search_field="emp_code"; else $search_field="id_card_no";
	
	$sql_result=sql_select("select id, emp_code, concat_ws(' ',first_name,middle_name,last_name) as name,id_card_no, designation_id, location_id, line_no from lib_employee where $search_field='$data[0]' and status_active=1 and is_deleted=0");
	foreach($sql_result as $row)
	{
		echo "$('#txt_worker_code').val('".$row[csf("emp_code")]."');\n";
		echo "$('#txt_worker_name').val('".$row[csf("name")]."');\n";
		echo "$('#cbo_designation').val(".$row[csf("designation_id")].");\n";
		echo "$('#cbo_location_id').val(".$row[csf("location_id")].");\n";
		echo "$('#cbo_line_num').val(".$row[csf("line_no")].");\n";
		echo "$('#cbo_floor_id').val('".$row[csf("line_no")]."');\n";
		echo "$('#txt_id_card_no').val('".$row[csf("id_card_no")]."');\n";
		
		echo "$('#txt_update_id').val('');\n";
		echo "$('#txt_prod_date').focus();\n";
		echo "create_row(1);\n";
		echo "set_button_status(0, permission, 'fnc_production_scanning',1);\n";
	}
}

if($action=="worker_code_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	$data=explode('_',$data);
?>
	<script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
					
		function js_set_value(id)
		{ 
			document.getElementById('worker_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
    <div style="width:100%" align="center">
        <input type="hidden" id="worker_id" />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th width="80">ID Card</th>
                <th width="100">Worker Code</th>
                <th width="100">Worker Name</th>
                <th width="100">Designation</th>
                <th width="100">Line Number</th>
                <th width="100">Floor Name</th>
                <th>Location</th>
            </thead>
        </table>
        <div style="width:750px;max-height:300px; overflow-y:scroll" id="worker_code_list_view" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="732" class="rpt_table" id="tbl_list_search">
                <?php  
                $location_arr=return_library_array( "select id, location_name from lib_location", "id","location_name"  );
                $i=1;
                $sql_result=sql_select("select id, emp_code, concat_ws(' ',first_name,middle_name,last_name) as name,id_card_no, designation_name, designation_id, location_id, line_no, line_name from lib_employee where status_active=1 and is_deleted=0");
                foreach($sql_result as $row)
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."__".$row[csf('emp_code')]."__".$row[csf('name')]."__".$row[csf('designation_id')]."__".$row[csf('location_id')]."__".$row[csf('line_no')]."__".$row[csf("id_card_no")]; ?>');" > 
                        <td width="50" align="center"><? echo $i; ?></td>
                         <td width="80"><p><? echo $row[csf('id_card_no')]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('emp_code')]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('name')]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('designation_name')]; ?></p></td>
                        <td width="100"><p><? echo $row[csf('line_name')]; ?></p></td>
                        <td width="100"><p><? //echo $floor_arr[$row[csf('')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
                    </tr>
                <?
                    $i++;
                }
                ?>
            </table>
        </div>
    </div>
</body>           
</html>
	<?	
	exit();
}


if($action=="upodate_info_popup")
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1,'',1,'');
	$data=explode('_',$data);
?>
    <script>
		$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });
		
		function js_set_value(id)
		{ 
			document.getElementById('worker_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
    <div style="width:100%" align="center">
    	<input type="hidden" id="worker_id" />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
            <thead>
                <th width="40">SL</th>
                <th width="80">System ID</th>
                <th width="110">ID Card No.</th>
                <th width="110">Worker Code</th>
                <th width="130">Worker Name</th>
                <th width="130">Designation</th>
                <th width="100">Line Number</th>
                <th>Production Date</th>
            </thead>
        </table>
        <div style="width:820px;max-height:300px; overflow-y:scroll" id="worker_code_list_view" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="802" class="rpt_table" id="tbl_list_search">
                <?php  
                $designation_arr=return_library_array( "select id, custom_designation from lib_designation", "id","custom_designation"  );
				$line_arr=return_library_array( "select id, line_name from lib_sewing_line", "id","line_name"  );
                $location_arr=return_library_array( "select id, location_name from lib_location", "id","location_name"  );
                $i=1;
                $sql_result=sql_select("SELECT a.prod_date,a.mst_id,group_concat(a.operation_barcode) as operation_barcode,b.emp_code, concat_ws(' ',b.first_name,b.middle_name,b.last_name) as name,id_card_no,designation_id,location_id,section_id,floor_id,line_no FROM pro_scanning_operation a, lib_employee b WHERE a.emp_code=b.emp_code group by a.mst_id");//,a.prod_date, a.emp_code
				//echo "SELECT a.prod_date,a.mst_id,group_concat(a.operation_barcode) as operation_barcode,b.emp_code, concat_ws(' ',b.first_name,b.middle_name,b.last_name) as name,id_card_no,designation_id,location_id,section_id,floor_id,line_no FROM pro_scanning_operation a, lib_employee b WHERE a.emp_code=b.emp_code group by a.mst_id,a.prod_date, a.emp_code";
                foreach($sql_result as $row)
                {
					if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf('mst_id')]."__".$row[csf('emp_code')]."__".$row[csf('name')]."__".$row[csf('operation_barcode')]."__".$row[csf('designation_id')]."__".$row[csf('location_id')]."__".$row[csf('line_no')]."__".$row[csf('floor_id')]."__".change_date_format($row[csf('prod_date')])."__".$row[csf("id_card_no")]; ?>');" > 
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="80" align="center"><? echo $row[csf('mst_id')]; ?></td>
                        <td width="110" ><p><? echo $row[csf('id_card_no')]; ?></p></td>
                        <td width="110" ><p><? echo $row[csf('emp_code')]; ?></p></td>
                        <td width="130"><p><? echo $row[csf('name')]; ?></p></td>
                        <td width="130"><p><? echo $designation_arr[$row[csf('designation_id')]]; ?></p></td>
                        <td width="100"><p><? echo $line_arr[$row[csf('line_no')]]; ?></p></td>
                        <td align="center"><p><? echo change_date_format($row[csf('prod_date')]); ?></p></td>
                      
                    </tr>
                <?
                    $i++;
                }
                ?>
            </table>
        </div>
    </div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
	<?	
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Start Here=================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=return_next_id( "id", "pro_scanning_operation", 1 ) ; 
		$mst_id=return_next_id( "mst_id", "pro_scanning_operation", 1 ) ; 
		$field_array="id,mst_id,emp_code,prod_date,operation_barcode,inserted_by,insert_date";
		$bundle_operarion_num=explode("__",$bundle_operarion_num);
		for($i=0;$i<count($bundle_operarion_num);$i++)
		{
			if($i==0) $data_array="(".$id.",".$mst_id.",".$txt_worker_code.",".$txt_prod_date.",'".$bundle_operarion_num[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			else $data_array .=",(".$id.",".$mst_id.",".$txt_worker_code.",".$txt_prod_date.",'".$bundle_operarion_num[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id++;
		}
		
		$rID=sql_insert("pro_scanning_operation",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$mst_id);
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1) // Update Start Here=================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from pro_scanning_operation where mst_id=$txt_update_id");
		$id=return_next_id( "id", "pro_scanning_operation", 1 ) ; 
		$mst_id=$txt_update_id; //return_next_id( "mst_id", "pro_scanning_operation", 1 ) ; 
		$field_array="id,mst_id,emp_code,prod_date,operation_barcode,inserted_by,insert_date";
		$bundle_operarion_num=explode("__",$bundle_operarion_num);
		for($i=0;$i<count($bundle_operarion_num);$i++)
		{
			if($i==0) $data_array="(".$id.",".$mst_id.",".$txt_worker_code.",".$txt_prod_date.",'".$bundle_operarion_num[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			else  $data_array .=",(".$id.",".$mst_id.",".$txt_worker_code.",".$txt_prod_date.",'".$bundle_operarion_num[$i]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id++;
		}
		$rID=sql_insert("pro_scanning_operation",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID && $delete)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$mst_id);
			}
		}
		disconnect($con);
		die;
	}
}