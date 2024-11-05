<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
//------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 200, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'cause_of_machine_idle_controller', this.value, 'load_drop_down_floor', 'floor' )"  );
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_name", 200, "select floor_name,id from  lib_prod_floor where location_id='$data' and is_deleted=0  and status_active=1  order by floor_name",'id,floor_name', 1, '--- Select Floor ---', 0, '' );
}


if ($action=="machine_no_search_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);

?>
     
	<script>
	
	function js_set_value( machine_table_id )
	{
		document.getElementById('selected_machine_id').value=machine_table_id;
		parent.emailwindow.hide();
	}
	
function floor_select()
{
	load_drop_down( 'cause_of_machine_idle_controller', $('#cbo_location_name').val(), 'load_drop_down_floor', 'floor' )
}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>                	 
                        <th width="200">Company Name</th><th width="200">Location Name</th><th width="200">Floor No</th><th width="150">Machine Category</th><th>&nbsp;</th>
                    </thead>
        			<tr>
                    	<td> <input type="hidden" id="selected_machine_id">
							<?
								echo create_drop_down( "cbo_company_name", 200, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond order by company_name",'id,company_name', 1,"--- Select Company ---",'' ,"load_drop_down( 'cause_of_machine_idle_controller', this.value, 'load_drop_down_location', 'location');" ); 
							?>
                    	</td>
                        <td id="location">
                         	<?
								echo create_drop_down( "cbo_location_name", 200, $blank_array,'', 1, '--- Select Location ---', 0, "load_drop_down( 'cause_of_machine_idle_controller', this.value, 'load_drop_down_floor', 'floor' )"  );
                        	?>	
                        </td>
                        <td id="floor">
                        	<? 
								echo create_drop_down( "cbo_floor_name", 200, "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1 order by floor_name",'id,floor_name', 1,"--- Select floor ---", "" );
							?>
					 	</td> 
                        <td>
                        	<? 
								echo create_drop_down( "cbo_catagory", 150, $machine_category,'', 1,"--Select Category--", "" );
							?>
					 	</td>
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('cbo_floor_name').value+'_'+document.getElementById('cbo_catagory').value, 'create_machine_no_search_list_view', 'search_div', 'cause_of_machine_idle_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td align="center" valign="top" id="search_div"> </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body> 
       
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>floor_select()</script>   
</html>
<?
}


if($action=="create_machine_no_search_list_view")
{
	$data=explode('_',$data);
	
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $location=" and location_id='$data[1]'"; else { $location="";}
	if ($data[2]!=0) $floors=" and floor_id='$data[2]'"; else { $floor="";}
	if ($data[3]!=0) $category=" and category_id='$data[3]'"; else { $category="";}
	
	$location_name=return_library_array( "select location_name,id from  lib_location where is_deleted=0", "id", "location_name"  );
	$floor=return_library_array( "select floor_name,id from  lib_prod_floor where is_deleted=0", "id", "floor_name"  );
	
	$arr=array(0=>$location_name,1=>$floor, 4=>$machine_category); 

	$sql= "select location_id,floor_id,machine_no,brand,category_id,machine_group,dia_width,gauge,id from lib_machine_name where status_active=1 and is_deleted=0 $company $location $floors $category order by id"; 
	
	echo  create_list_view("list_view", "Location,Floor,Machine No,Brand Name,Category,Machine Group,Dia Width,Gauge", "150,120,80,80,130,100,80","900","320",0, $sql , "js_set_value", "id", "", 1, "location_id,floor_id,0,0,category_id", $arr , "location_id,floor_id,machine_no,brand,category_id,machine_group,dia_width,gauge", "",'','0,0,0,0,0,0,0') ;
	
} 


if ($action=="populate_machine_no_data_from_search_popup")
{
	//$data=explode("_",$data);
	
	$data_array=sql_select("select company_id,location_id,floor_id,machine_no,category_id,machine_group,dia_width,gauge,extra_cylinder,no_of_feeder,attachment,prod_capacity,capacity_uom_id,remark from lib_machine_name where  id ='".$data."'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_machine_no').value = '".$row[csf("machine_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n"; 
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('cbo_floor_name').value = '".$row[csf("floor_id")]."';\n";
		echo "document.getElementById('cbo_catagory').value = '".$row[csf("category_id")]."';\n";
		echo "document.getElementById('txt_group').value = '".$row[csf("machine_group")]."';\n";
		echo "document.getElementById('txt_dia_width').value = '".$row[csf("dia_width")]."';\n";
		echo "document.getElementById('txt_gauge').value = '".$row[csf("gauge")]."';\n";
		echo "document.getElementById('txt_extra_cylinder').value = '".$row[csf("extra_cylinder")]."';\n";
		echo "document.getElementById('txt_no_of_feeder').value = '".$row[csf("no_of_feeder")]."';\n";
		echo "document.getElementById('txt_attachment').value = '".$row[csf("attachment")]."';\n";
		echo "document.getElementById('txt_prod_capacity').value = '".$row[csf("prod_capacity")]."';\n";
		echo "document.getElementById('cbo_capacity_uom').value = '".$row[csf("capacity_uom_id")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remark")]."';\n";
     }
}

if ($action=="save_update_delete")
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
		/*if(str_replace("'","",$cbo_time_from)==1)
		{
			if(str_replace("'","",$txt_from_hour)==12)
				$from_hour=0;
			else
				 $from_hour = str_replace("'","",$txt_from_hour);
		}
		else
		{
			if (str_replace("'","",$txt_from_hour)==12)
				$from_hour = 0+str_replace("'","",$txt_from_hour);
			else
				$from_hour = 12+str_replace("'","",$txt_from_hour);
		}
		
		if(str_replace("'","",$cbo_time_to)==1)
		{
			if(str_replace("'","",$txt_to_hour)==12)
				$to_hour =0;
			else
				$to_hour = str_replace("'","",$txt_to_hour); 
		}
		else 
		{
			if (str_replace("'","",$txt_to_hour)==12)
				$to_hour = 0+str_replace("'","",$txt_to_hour);
			else
				$to_hour = 12+str_replace("'","",$txt_to_hour);
		}*/
		//$from_hour = str_replace("'","",$txt_from_hour); else $from_hour = 12+str_replace("'","",$txt_from_hour);
		//if(str_replace("'","",$cbo_time_to)==1)$to_hour = str_replace("'","",$txt_to_hour); else $to_hour = 12+str_replace("'","",$txt_to_hour);
		$from_hour = str_replace("'","",$txt_from_hour);
		$to_hour = str_replace("'","",$txt_to_hour); 
		$txt_from_minute = str_replace("'","",$txt_from_minute); 
		$txt_to_minute = str_replace("'","",$txt_to_minute); 

		$from_hour 	 = ($from_hour=="")?0:$from_hour;
		$to_hour 	 = ($to_hour=="")?0:$to_hour;
		$txt_from_minute = ($txt_from_minute=="")?0:$txt_from_minute;
		$txt_to_minute 	 = ($txt_to_minute=="")?0:$txt_to_minute;
		// echo '10**'.$from_hour.'+'.$to_hour.'+'.$from_minute.'+'.$to_minute;die;
		$id=return_next_id( "id", "pro_cause_of_machine_idle", 1 ) ;
		
		$field_array="id,machine_entry_tbl_id,machine_no,from_date,from_hour,from_minute,to_date,to_hour,to_minute,reporting_date,machine_idle_cause,remarks,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",".$txt_machine_table_id.",".$txt_machine_no.",".$txt_from_date.",".$from_hour.",".$txt_from_minute.",".$txt_to_date.",".$to_hour.",".$txt_to_minute.",".$txt_reporting_date.",".$txt_cause_of_machine_idle.",".$txt_remark.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		//echo "10**INSERT INTO pro_cause_of_machine_idle (".$field_array.") VALUES ".$data_array;die;
		
 		$rID=sql_insert("pro_cause_of_machine_idle",$field_array,$data_array,0);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
		if($rID)
			{
				oci_commit($con);
				echo "0**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//if(str_replace("'","",$cbo_time_from)==1) $from_hour =str_replace("'","",$txt_from_hour); else $from_hour = 12+str_replace("'","",$txt_from_hour);
		//if(str_replace("'","",$cbo_time_to)==1)$to_hour = str_replace("'","",$txt_to_hour); else $to_hour = 12+str_replace("'","",$txt_to_hour);
		
		/*if(str_replace("'","",$cbo_time_from)==1)
		{
			if(str_replace("'","",$txt_from_hour)==12)
				$from_hour=0;
			else
				 $from_hour = str_replace("'","",$txt_from_hour);
		}
		else
		{
			if (str_replace("'","",$txt_from_hour)==12)
				$from_hour = 0+str_replace("'","",$txt_from_hour);
			else
				$from_hour = 12+str_replace("'","",$txt_from_hour);
		}
		
		if(str_replace("'","",$cbo_time_to)==1)
		{
			if(str_replace("'","",$txt_to_hour)==12)
				$to_hour =0;
			else
				$to_hour = str_replace("'","",$txt_to_hour); 
		}
		else 
		{
			if (str_replace("'","",$txt_to_hour)==12)
				$to_hour = 0+str_replace("'","",$txt_to_hour);
			else
				$to_hour = 12+str_replace("'","",$txt_to_hour);
		}*/
		$from_hour = str_replace("'","",$txt_from_hour);
		$to_hour = str_replace("'","",$txt_to_hour); 
		$txt_from_minute = str_replace("'","",$txt_from_minute); 
		$txt_to_minute = str_replace("'","",$txt_to_minute); 

		$from_hour 	 = ($from_hour=="")?0:$from_hour;
		$to_hour 	 = ($to_hour=="")?0:$to_hour;
		$txt_from_minute = ($txt_from_minute=="")?0:$txt_from_minute;
		$txt_to_minute 	 = ($txt_to_minute=="")?0:$txt_to_minute;
		 
		$field_array="machine_entry_tbl_id*machine_no*from_date*from_hour*from_minute*to_date*to_hour*to_minute*reporting_date*machine_idle_cause*remarks*updated_by*update_date";
		$data_array="".$txt_machine_table_id."*".$txt_machine_no."*".$txt_from_date."*".$from_hour."*".$txt_from_minute."*".$txt_to_date."*".$to_hour."*".$txt_to_minute."*".$txt_reporting_date."*".$txt_cause_of_machine_idle."*".$txt_remark."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//echo $data_array; die;
		$rID=sql_update("pro_cause_of_machine_idle",$field_array,$data_array,"id","".$txt_mst_id."",1);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "1**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//echo $txt_mst_id;
		$rID=sql_delete("pro_cause_of_machine_idle",$field_array,$data_array,"id","".$txt_mst_id."",1);
		//echo "2**".$rID;
		if($db_type==0)
		{	
			echo "2**".$rID;
		}
		if($db_type==2)
		{	oci_commit($con);
			echo "2**".$rID;
		}
		else
		{
			oci_rollback($con);
			echo "2**".$rID;	
		}
		disconnect($con);die;
	}
}

if($action=="show_active_listview")
{
	$arr=array(7=>$cause_type); 
	 
 	$sql= "select from_date,from_hour,from_minute,to_date,to_hour,to_minute,reporting_date,machine_idle_cause,remarks,id from pro_cause_of_machine_idle where status_active=1 and is_deleted=0 and machine_entry_tbl_id='$data' order by id"; 
	 
	echo  create_list_view("list_view", "From Date,From Hour,From Minute,To Date,To Hour,To Minute,Reporting date,Cause,Remarks", "80,50,50,80,50,50,100,200,300","900","220",0, $sql , "get_php_form_data", "id", "'populate_machine_details_form_data'", 1, "0,0,0,0,0,0,0,machine_idle_cause,0", $arr , "from_date,from_hour,from_minute,to_date,to_hour,to_minute,reporting_date,machine_idle_cause,remarks", "requires/cause_of_machine_idle_controller",'','3,0,0,3,0,0,3,0,0') ;
}

if($action=="populate_machine_details_form_data")
{
	$data_array=sql_select("select from_date, from_hour, from_minute, to_date, to_hour, to_minute,reporting_date, machine_idle_cause, remarks, id from  pro_cause_of_machine_idle  where id =$data");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_from_date').value = '".change_date_format($row[csf("from_date")], "dd-mm-yyyy", "-")."';\n";
		/*if($row[csf('from_hour')]>12)
		{
			$hour_from = $row[csf('from_hour')]-12;  $time_from=2;
 		}
		else if($row[csf('from_hour')]==12)
		{
			$hour_from = "12";  $time_from=2;
		}
		else if($row[csf('from_hour')]==0)
		{
			$hour_from = "12";  $time_from=1;
		}
		else
		{
			$hour_from = $row[csf('from_hour')]; $time_from=1;
		}*/
		echo "document.getElementById('txt_from_hour').value = '".$row[csf('from_hour')]."';\n";
		echo "document.getElementById('txt_from_minute').value = '".$row[csf("from_minute")]."';\n";
		//echo "document.getElementById('cbo_time_from').value = '".$time_from."';\n";
		echo "document.getElementById('txt_to_date').value = '".change_date_format($row[csf("to_date")], "dd-mm-yyyy", "-")."';\n"; 
		/*if($row[csf('to_hour')]>12)
		{
			$hour_to = $row[csf('to_hour')]-12;  $time_to=2;
 		}
		else if($row[csf('to_hour')]==12)
		{
			$hour_to = "12";  $time_to=2;
		}
		else if($row[csf('to_hour')]==0)
		{
			$hour_to = "12";  $time_to=1;
		}
		else
		{
			$hour_to = $row[csf('to_hour')]; $time_to=1;
		}*/
		echo "document.getElementById('txt_reporting_date').value = '".change_date_format($row[csf('reporting_date')])."';\n"; 
	    echo "document.getElementById('txt_to_hour').value = '".$row[csf('to_hour')]."';\n"; 
		echo "document.getElementById('txt_to_minute').value = '".$row[csf("to_minute")]."';\n";
		//echo "document.getElementById('cbo_time_to').value = '".$time_to."';\n"; 
		echo "document.getElementById('txt_cause_of_machine_idle').value = '".$row[csf("machine_idle_cause")]."';\n"; 
		echo "document.getElementById('txt_remark').value = '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('txt_mst_id').value = '".$row[csf("id")]."';\n";  
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_cause_of_machine_idle_entry',1);\n"; 
     }
	 exit();
}

/*function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);	
	
	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value." WHERE ";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues." WHERE ";
	}
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);	
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	
	global $con;
	//echo $strQuery; die;
	 return $strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	
	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con); 
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}*/

?>
