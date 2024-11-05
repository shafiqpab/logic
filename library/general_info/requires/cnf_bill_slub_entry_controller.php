<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

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

		if (is_duplicate_field( "company_id", "lib_cnf_bill_slub_mst", "company_id=$cbo_company_name and cnf_type=$cbo_type_name and slub_name=$txt_slub_name and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "26**0"; disconnect($con); die;
		}
		
		$id=return_next_id( "id", "lib_cnf_bill_slub_mst", 1 );
		$field_array_mst= "id,company_id,cnf_type,slub_name,inserted_by,insert_date,status_active,is_deleted";
		$data_array_mst .="(".$id.",".$cbo_company_name.",".$cbo_type_name.",".$txt_slub_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";

		$dtls_id=return_next_id( "id", "lib_cnf_bill_slub_dtls", 1 );
		$field_array_dtls= "id,mst_id,from_unit,to_unit,charge,inserted_by,insert_date,status_active,is_deleted";

		for($i=1; $i<=$total_row; $i++)
		{			
			$txtFromUnit  = "txtFromUnit_".$i;
			$txtToUnit 	  = "txtToUnit_".$i;
			$txtCharge 	  = "txtCharge_".$i;
						
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls .="(".$dtls_id.",".$id.",".$$txtFromUnit.",".$$txtToUnit.",".$$txtCharge.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
			$dtls_id++;
		}
		//echo "10**INSERT INTO lib_cnf_bill_slub_mst(".$field_array_mst.") VALUES ".$data_array_mst;die;
		$rID=sql_insert("lib_cnf_bill_slub_mst",$field_array_mst,$data_array_mst,0);
		$rID2=sql_insert("lib_cnf_bill_slub_dtls",$field_array_dtls,$data_array_dtls,0);

		if($db_type==0)
		{
			if($rID && $rID2)
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
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
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

		if (is_duplicate_field( "company_id", "lib_cnf_bill_slub_mst", "company_id=$cbo_company_name and cnf_type=$cbo_type_name and slub_name=$txt_slub_name and id!=$update_id and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "26**0"; disconnect($con); die;
		}
		
		$field_array_update_mst= "company_id*cnf_type*slub_name*updated_by*update_date";
		$data_array_update_mst= "" . $cbo_company_name . "*" . $cbo_type_name. "*" . $txt_slub_name. "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$dtls_id=return_next_id( "id", "lib_cnf_bill_slub_dtls", 1 );
		$field_array_update_dtls="from_unit*to_unit*charge*updated_by*update_date";
		$field_array_dtls="id,mst_id,from_unit,to_unit,charge,inserted_by,insert_date,status_active,is_deleted";

		$add_comma=0;
		for($i=1; $i<=$total_row; $i++)
		{			
			$txtFromUnit  = "txtFromUnit_".$i;
			$txtToUnit 	  = "txtToUnit_".$i;
			$txtCharge 	  = "txtCharge_".$i;
			$hdnDtlsUpdateId = "hdnDtlsUpdateId_".$i;
		
			if(str_replace("'","",$$hdnDtlsUpdateId)!="")
			{

				$id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
				$data_array_update_dtls[str_replace("'",'',$$hdnDtlsUpdateId)] = explode("*",("'".str_replace("'","",$$txtFromUnit)."'*'".str_replace("'","",$$txtToUnit)."'*'".str_replace("'","",$$txtCharge)."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));

			}
			else
			{
				if ($add_comma!=0) $data_array_dtls .=",";
				$data_array_dtls .="(".$dtls_id.",".$update_id.",".$$txtFromUnit.",".$$txtToUnit.",".$$txtCharge.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1',0)";
				$dtls_id=$dtls_id+1;
				$add_comma++;
			}			
		}

		$rID=true; $rID2=true; $rID3=true; $rID4=true;
		$rID = sql_update("lib_cnf_bill_slub_mst",$field_array_update_mst,$data_array_update_mst,"id","".$update_id."",0);

		if(count($data_array_update_dtls)>0)
		{
			//echo "10**".bulk_update_sql_statement( "lib_cnf_bill_slub_dtls", "id", $field_array_update_dtls, $data_array_update_dtls, $id_arr); die;
			$rID2=execute_query(bulk_update_sql_statement( "lib_cnf_bill_slub_dtls", "id", $field_array_update_dtls, $data_array_update_dtls, $id_arr),0);
		}

		if($data_array_dtls!="")
		{
			$rID3=sql_insert("lib_cnf_bill_slub_dtls",$field_array_dtls,$data_array_dtls,0);
		}


		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";

			$rID4=sql_multirow_update("lib_cnf_bill_slub_dtls",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}		

		//echo "10**".$rID."**".$rID2."**".$rID3."**".$rID4; die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3){
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID && $rID2 && $rID3)
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
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$user_id=$_SESSION['logic_erp']['user_id'];
		$rID=execute_query("update lib_cnf_bill_slub_mst set status_active=0 , is_deleted=1 , updated_by=$user_id, update_date='$pc_date_time' where id=$update_id");
		$rID2=execute_query("update lib_cnf_bill_slub_dtls set status_active=0 , is_deleted=1 , updated_by=$user_id, update_date='$pc_date_time' where mst_id=$update_id");
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		 	if($rID)
			{
				oci_commit($con);   
				echo "2**".$rID;
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
}

if( $action=='dtls_list_view2' ) 
{
	extract($_REQUEST);
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	//$data=explode('_',$data);
	$company_cond="";
	if ($data != 0) $company_cond= " and company_id=$data";

	$sql = "SELECT id, company_id, cnf_type, slub_name, from_unit, to_unit, charge from lib_cnf_bill_slub_mst where status_active=1 and is_deleted=0 $company_cond order by id ASC";

	$data_array=sql_select($sql); $tblRow=0;
	
	if(count($data_array) > 0)
	{
		foreach ($data_array as $row) 
		{
			$tblRow++;
			?>
			<tr id="row_<? echo $tblRow; ?>" align="center">
				<td><input type="text" name="txtFromUnit[]" id="txtFromUnit_<? echo $tblRow; ?>" style="width:120px" class="text_boxes_numeric" value="<? echo $row[csf('from_unit')]; ?>" /></td>	            
	            <td><input type="text" name="txtToUnit[]" id="txtToUnit_<? echo $tblRow; ?>" style="width:120px" class="text_boxes_numeric" value="<? echo $row[csf('to_unit')]; ?>" /></td>
	            <td><input type="text" name="txtCharge[]" id="txtCharge_<? echo $tblRow; ?>" style="width:120px" class="text_boxes_numeric" value="<? echo $row[csf('charge')]; ?>" /></td>
	            <td> 
	               	<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_')" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_');" />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf('id')]; ?> "; />
	            </td>  
	        </tr>
			<?
		}
	}
	else
	{
		?>
		<tr id="row_1" align="center">
            <td><input type="text" name="txtFromUnit[]" id="txtFromUnit_1" style="width:120px" class="text_boxes_numeric" /></td>
            <td><input type="text" name="txtToUnit[]" id="txtToUnit_1" style="width:120px" class="text_boxes_numeric" /></td>
            <td><input type="text" name="txtCharge[]" id="txtCharge_1" style="width:120px" class="text_boxes_numeric" /></td>
            <td> 
               	<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
				<input id="hdnDtlsUpdateId_1" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" />
                <!-- <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  --> 
            </td>  
        </tr>
		<?
	}
}

if( $action=='load_php_data_to_form' ) 
{
	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//$cnf_arr=array(1=>"Export",2=>"Import");
	$sql_mst = sql_select("SELECT id, company_id, cnf_type, slub_name from lib_cnf_bill_slub_mst where id=$data and status_active=1 and is_deleted=0 order by id ASC");
	foreach ($sql_mst as $row) 
	{
		echo "document.getElementById('cbo_company_name').value 		= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_type_name').value 			= '" . $row[csf("cnf_type")] . "';\n";
		echo "document.getElementById('txt_slub_name').value 			= '" . $row[csf("slub_name")] . "';\n";
		echo "document.getElementById('update_id').value 				= '" . $row[csf("id")] . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_cnf_bill_slub_entry',1);\n";
	}
	
}	

if( $action=='load_php_dtlsdata_to_form' ) 
{
	

	$sql = "SELECT id, from_unit, to_unit, charge from lib_cnf_bill_slub_dtls where status_active=1 and is_deleted=0 and mst_id=$data order by id ASC";

	$data_array=sql_select($sql); $tblRow=0;


	if(count($data_array) > 0)
	{
		foreach ($data_array as $row) 
		{
			$tblRow++;
			?>
			<tr id="row_<? echo $tblRow; ?>" align="center">
				<td><input type="text" name="txtFromUnit[]" id="txtFromUnit_<? echo $tblRow; ?>" style="width:120px" class="text_boxes_numeric" value="<? echo $row[csf('from_unit')]; ?>" /></td>	            
	            <td><input type="text" name="txtToUnit[]" id="txtToUnit_<? echo $tblRow; ?>" style="width:120px" class="text_boxes_numeric" value="<? echo $row[csf('to_unit')]; ?>" /></td>
	            <td><input type="text" name="txtCharge[]" id="txtCharge_<? echo $tblRow; ?>" style="width:120px" class="text_boxes_numeric" value="<? echo $row[csf('charge')]; ?>" /></td>
	            <td> 
	               	<input type="button" id="increase_<? echo $tblRow; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_')" />
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $tblRow; ?>,'tbl_dtls_emb','row_');" />
					<input id="hdnDtlsUpdateId_<? echo $tblRow; ?>" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" value="<? echo $row[csf('id')]; ?> " />
	            </td>  
	        </tr>
			<?
		}
	}
	else
	{
		?>
		<tr id="row_1" align="center">
            <td><input type="text" name="txtFromUnit[]" id="txtFromUnit_1" style="width:120px" class="text_boxes_numeric" /></td>
            <td><input type="text" name="txtToUnit[]" id="txtToUnit_1" style="width:120px" class="text_boxes_numeric" /></td>
            <td><input type="text" name="txtCharge[]" id="txtCharge_1" style="width:120px" class="text_boxes_numeric" /></td>
            <td> 
               	<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_dtls_emb','row_')" />
				<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_dtls_emb','row_');" />
				<input id="hdnDtlsUpdateId_1" name="hdnDtlsUpdateId[]" type="hidden" class="text_boxes_numeric" style="width:40px" />
                <!-- <input type="hidden" id="updateid_1" name="updateid_1"  class="text_boxes" style="width:20px" value=""  />  --> 
            </td>  
        </tr>
		<?
	}
}

if( $action=='dtls_list_view' ) 
{
	extract($_REQUEST);
	//echo $data; die;2_FAL-TOR-19-00092_1_3895
	//$data=explode('_',$data);
	$company_cond="";
	if ($data != 0) $company_cond= " and company_id=$data";

	$sql = "SELECT id, company_id, cnf_type, slub_name from lib_cnf_bill_slub_mst where status_active=1 and is_deleted=0 $company_cond order by id ASC";
	$data_array=sql_select($sql); $tblRow=0;	
 
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$cnf_arr=array(1=>"Export",2=>"Import")

	?>

	<table width="485" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
		<thead>
			<th width="50">SL</th>
			<th width="200">Company_name</th>
			<th width="100">C&F Type</th>
			<th>Slub Name</th>
		</thead>
		<tbody>
			<?
			foreach ($data_array as $row)
			{
				$tblRow++;
				$ids=$row[csf('id')];
				?>
				<tr id="row_<? echo $tblRow; ?>" algn="center" style="text-decoration:none; cursor:pointer;" onClick="get_php_form_data('<? echo $ids; ?>', 'load_php_data_to_form', 'requires/cnf_bill_slub_entry_controller');show_list_view('<? echo $ids; ?>','load_php_dtlsdata_to_form','landing_slub_tbody','requires/cnf_bill_slub_entry_controller', '');">
					<td width="50"><? echo $tblRow; ?></td>
					<td width="200"><? echo $company_arr[$row[csf('company_id')]]; ?></td>            
		            <td width="100"><? echo $cnf_arr[$row[csf('cnf_type')]]; ?></td>
		            <td ><? echo $row[csf('slub_name')]; ?></td>
		        </tr>
			    <?
			}
			?>
	    </tbody>
	</table>
	<?
	exit();
}

if($action=="composition_popup")
{
	echo load_html_head_contents("Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('hidcompid').value=id;
			document.getElementById('hidcompname').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:430px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th>Composition
                        	<input type="hidden" name="hidcompid" id="hidcompid" value="" style="width:50px">
                            <input type="hidden" name="hidcompname" id="hidcompname" value="" style="width:50px">
                        </th>
	                </thead>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="430" class="rpt_table" id="comp_tbl">
                    <tbody>
                    <? $i=1; foreach($composition as $id=>$comp_name) { if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; ?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $id; ?>,'<? echo $comp_name; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td><? echo $comp_name; ?> </td> 						
                        </tr>
                    <? $i++; } ?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
}
	
if($action=="check_yarn_count_determination")
{
	//$data=explode("**",$data);
	$price_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pri_quo_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$price_data_array=sql_select($price_sql);

	$pre_sql="select lib_yarn_count_deter_id as detarmination_id from wo_pre_cost_fabric_cost_dtls a where  a.lib_yarn_count_deter_id=$data";
	$pre_data_array=sql_select($pre_sql);
	 
	 $sql="select a.detarmination_id as detarmination_id from product_details_master a where   a.detarmination_id=$data and a.is_deleted=0 and a.status_active=1"; 
	
	$data_array=sql_select($sql);
	if(count($data_array)>0 || count($pre_data_array)>0 || count($price_data_array)>0)
	{
		echo "1_";
	}
	else
	{
		echo "0_";
	}
	exit();	
}
?>