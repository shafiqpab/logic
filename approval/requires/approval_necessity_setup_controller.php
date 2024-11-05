<?
/*-------------------------------------------- Comments
Purpose			         :  This form will create Approval Necessity Setup Controller  						
Functionality	         :	
JS Functions	         :
Created by		         :	Mirza Tahmid Tajik
Creation date 	         :  02-07-2017
Requirment Client        :  
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                
DB Script                : 
Updated by 		         : 	 
Update date		         : 	 
QC Performed BY	         :		
QC Date			         :	
Comments		         : 	 
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if($_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);
include('../../includes/common.php');
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

		$id_mst=return_next_id( "id", "approval_setup_mst", 1 ) ;
		$field_array_mst="id,company_id,setup_date,inserted_by,insert_date,status_active,is_deleted";

		if($db_type==0) 
		{
			$txt_date= date("Y-m-d",strtotime($txt_date));
		}
		else
		{
			$dateObj = DateTime::createFromFormat('d-m-Y', $txt_date);
			$txt_date= $dateObj->format('d-M-Y');
		}

		$data_array_mst ="(".$id_mst.",".$company_name.",'".$txt_date."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		$rID_1=sql_insert("approval_setup_mst",$field_array_mst, $data_array_mst,1);
	
		$id_dtls=return_next_id( "id", "approval_setup_dtls", 1 ) ;
		$field_array= "id,mst_id,page_id,approval_need,allow_partial,validate_page,inserted_by,insert_date,status_active,is_deleted";

		$data_array="";
		for ($i=1;$i<=$total_row;$i++)
		{  
			$txt_page_id="txt_page_id_".$i;
			$txt_need="txt_need_".$i;
			$txt_allow_partial="txt_allow_partial_".$i;
			$cboValidatePage="cboValidatePage_".$i;

			if ($data_array!='') $data_array .=",";

			$data_array .="(".$id_dtls.",".$id_mst.",".$$txt_page_id.",".$$txt_need.",".$$txt_allow_partial.",".$$cboValidatePage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id_dtls=$id_dtls+1;
		} 
		//echo "insert into approval_setup_dtls $field_array values($data_array)";die;
		$rID_2=sql_insert("approval_setup_dtls",$field_array,$data_array,1);

		if($db_type==0)
		{
			if($rID_1 && $rID_2){
				mysql_query("COMMIT");  
				echo "0**".$company_name."**".$txt_date;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_1 && $rID_2)
			{
				oci_commit($con);  
				echo "0**".$company_name."**".$txt_date;

			}
		else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$mst_id=str_replace("'",'',$mst_id);
		$id_dtls=return_next_id( "id", "approval_setup_dtls", 1 ) ;
		$data_array="";
		$field_array= "id,mst_id,page_id,approval_need,allow_partial,validate_page,inserted_by,insert_date,status_active,is_deleted";
		$field_array_up="approval_need*allow_partial*validate_page*updated_by*update_date";
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$txt_page_id="txt_page_id_".$i;
			$txt_need="txt_need_".$i;
			$updateIdDtls="updateDtls_".$i;
			$txt_allow_partial="txt_allow_partial_".$i;
			$cboValidatePage="cboValidatePage_".$i;
			
			if(str_replace("'",'',$$updateIdDtls)!="")
			{	 				
				$id_arr[]=str_replace("'",'',$$updateIdDtls);
				$data_array_up[str_replace("'",'',$$updateIdDtls)] =explode("*",("".$$txt_need."*".$$txt_allow_partial."*".$$cboValidatePage."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
			else
			{
				if ($data_array!='') $data_array .=",";

				$data_array .="(".$id_dtls.",".$mst_id.",".$$txt_page_id.",".$$txt_need.",".$$txt_allow_partial.",".$$cboValidatePage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
					
				$id_dtls=$id_dtls+1;
			}
		}

		$flag=1;
		//echo bulk_update_sql_statement("approval_setup_dtls", "id",$field_array_up,$data_array_up,$id_arr );die;
		
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("approval_setup_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1) $flag=1; else $flag=0;
		}

		if($data_array!="")
		{
			$rID_2=sql_insert("approval_setup_dtls",$field_array,$data_array,1);
		}
		//master part update
		
		$sql="update approval_setup_mst set updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where id=$mst_id and status_active=1 and is_deleted=0";
		$sql_result =execute_query($sql); 
		
		if($db_type==0)
		{
			if($rID1){
				mysql_query("COMMIT");  
				echo "1**";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		elseif($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**";
			}
			else
			{
				oci_rollback($con); 
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	// else if ($operation==2)  // Delete Here
	// {
	// 	$con = connect(); 
		
		
	// 	$sql="delete approval_setup_mst where id=$mst_id";
	// 	$approval_setup_mst_result = execute_query($sql);
		 
	// 	$sql="delete approval_setup_dtls where mst_id=$mst_id";
	// 	$approval_setup_dtls_result = execute_query($sql);
		  
	// 	if($approval_setup_mst_result == 1 && $approval_setup_dtls_result == 1)
	// 	{
	// 		oci_commit($con);  
	// 		echo "1**";
	// 	}
	// 	else
	// 	{
	// 		oci_rollback($con); 
	// 		echo "10**";
	// 	}
	// 	disconnect($con);
	// 	die;
	// }
}

if($action=='load_php_dtls_form')
{
	$data=explode("_", $data);
	$company_id=$data[0];
	$txt_date=$data[1];

	if($db_type==0)
	{ 
		$mst_id =return_field_value("id","approval_setup_mst","company_id='$company_id' and setup_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and is_deleted=0 and status_active=1");
	}
	else
	{
		$mst_id =return_field_value("id","approval_setup_mst","company_id='$company_id' and setup_date='".change_date_format($txt_date, "", "",1)."' and is_deleted=0 and status_active=1");
	}
	if($mst_id==""){$mst_id=0;}
	$new_approval_necessetity_array=$approval_necessity_array;
	$sql="select id, page_id, approval_need, allow_partial, validate_page from approval_setup_dtls where mst_id=$mst_id and status_active=1 and is_deleted=0 order by id asc";
	$sql_result =sql_select($sql); 
	$i=1; $priceQuotAppArr=array(1=>"Order Entry", 2=> "Pre-Cost");
	$qcAppArr=array(1=> "Order Entry Matrix V2", 2=> "Pre-Cost V3", 3=> "Order Entry By Matrix Woven", 4=> "Pre-Costing V2-Woven");
	$gmtsDelivery=array(1=> "Garments Delivery Entry");
	$ToppingAddingAppArr=array(1=>"Dyes And Chemical Issue Requisition");
	if(count($sql_result)>0)
	{	
		foreach($sql_result as $row)
		{
			unset($new_approval_necessetity_array[$row[csf("page_id")]]);
			$validatePage=$blank_array;
			if($row[csf("page_id")]==1) $validatePage=$priceQuotAppArr; 
			else if($row[csf("page_id")]==5 || $row[csf("page_id")]==6) $validatePage=$yes_no;
			else if($row[csf("page_id")]==25) $validatePage=$gmtsDelivery;
			else if($row[csf("page_id")]==37) $validatePage=$gmtsDelivery;
			else if($row[csf("page_id")]==28) $validatePage=$qcAppArr;
			else if($row[csf("page_id")]==34) $validatePage=$ToppingAddingAppArr;
			else $validatePage=$blank_array;
			//echo $row[csf("page_id")].'DD';
			?>
				<tr title="page id:<?= $row[csf("page_id")]; ?>">
					<td style="width:30px; text-align:center;"><?=$i; ?> 
						<input type="hidden" name="updateDtls_<?=$i; ?>" id="updateDtls_<?=$i; ?>" value="<?=$row[csf("id")]; ?>"/>
					</td>
					<td>  
						<input style="width:180px;" type="hidden"  class="text_boxes" name="txt_page_id_<?=$i; ?>" id="txt_page_id_<?=$i; ?>" value="<?=$row[csf("page_id")]; ?>"/>  
						<?=$approval_necessity_array[$row[csf("page_id")]];?>
					</td>
					<td><?=create_drop_down( "txt_need_".$i, 80, $yes_no,"", 1, "-- Select --", $row[csf("approval_need")], "",0,"","","","",10); ?></td>
					<td><?=create_drop_down( "txt_allow_partial_".$i,80,$yes_no,"",1,"-- Select --", $row[csf("allow_partial")],"",0,"","","","",10); ?></td>
                    <td><?=create_drop_down( "cboValidatePage_".$i,100,$validatePage,"",1,"-- Select --", $row[csf("validate_page")],"",0,"","","","",10); ?></td>
				</tr>
			<?
			$i++;
		}
		if (empty($new_approval_necessetity_array)== FALSE) 
		{
			foreach ($new_approval_necessetity_array as $page_id=>$page_name)
			{
				$validatePage=$blank_array;
				if($page_id==1) $validatePage=$priceQuotAppArr; 
				else if($page_id==5 || $page_id==6) $validatePage=$yes_no;
				else if($page_id==28) $validatePage=$qcAppArr;
				else if($page_id==34) $validatePage=$ToppingAddingAppArr;
				else $validatePage=$blank_array;
				?>
				 <tr title="page id:<?=$page_id; ?>">
					<td style="width:30px; text-align:center;"><?=$i; ?> 
						<input type="hidden" name="updateDtls_<?=$i; ?>" id="updateDtls_<?=$i; ?>" />
					</td>
					<td>  
						<input style="width:180px;" type="hidden" class="text_boxes" name="txt_page_id_<?=$i; ?>" id="txt_page_id_<?=$i; ?>" value="<?=$page_id; ?>"/><?=$page_name;?>
					</td>
					<td><?=create_drop_down( "txt_need_".$i, 80, $yes_no,"", 1, "-- Select --", 0, "",0,"","","","",10); ?></td>
					<td><?=create_drop_down( "txt_allow_partial_".$i, 80, $yes_no,"", 1, "-- Select --", 2, "",0,"","","","",10); ?></td>
                    <td><?=create_drop_down( "cboValidatePage_".$i,100,$validatePage,"",1,"-- Select --", "","",0,"","","","",10); ?></td>
				</tr>
				<?	
				$i++;
			}
		}
	}
	else
	{
		$i=1;
		foreach ($approval_necessity_array as $page_id=>$page_name) 
		{
			$validatePage=$blank_array;
			if($page_id==1) $validatePage=$priceQuotAppArr; 
			else if($page_id==5 || $page_id==6) $validatePage=$yes_no;
			else if($page_id==28) $validatePage=$qcAppArr;
			else if($page_id==34) $validatePage=$ToppingAddingAppArr;
			else $validatePage=$blank_array;
			?>		
			<tr>
				<td style="width:30px; text-align:center;"><?=$i; ?> 
					<input type="hidden" name="updateDtls_<?=$i; ?>" id="updateDtls_<?=$i; ?>" />
				</td>
				<td>  
					<input style="width:180px;" type="hidden"  class="text_boxes" name="txt_page_id_<?=$i; ?>" id="txt_page_id_<?=$i; ?>" value = "<?=$page_id; ?>"/><?=$page_name; ?>
				</td>
				<td><?=create_drop_down( "txt_need_".$i, 80, $yes_no,"", 1, "-- Select --", 0, "",0,"","","","",10); ?></td>
				<td><?=create_drop_down( "txt_allow_partial_".$i, 80, $yes_no,"", 1, "-- Select --", 0, "",0,"","","","",10); ?></td>
                <td><?=create_drop_down( "cboValidatePage_".$i,100,$validatePage,"",1,"-- Select --", "","",0,"","","","",10); ?></td>
			</tr>
			<?
			$i++;
		}
	}
	exit();
}

if($action=='check_data_is_exis')
{
	$data=explode("_", $data);
	$company_id=$data[0];
	$txt_date=$data[1];
	
	if($db_type==0)
	{ 
		$sql =return_field_value("id","approval_setup_mst","company_id='$company_id' and setup_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and is_deleted=0 and status_active=1");
	}
	else
	{
		$sql =return_field_value("id","approval_setup_mst","company_id='$company_id' and setup_date='".change_date_format($txt_date, "", "",1)."' and is_deleted=0 and status_active=1");
	}
	
	if($sql>0)
	{
		echo "yes**".$sql;
	}
	else
	{
		echo "no**".$sql;
	}
	exit();
}

//------------------------------------------------------------
if($action=="create_date_list_view")
{
	$sql="select id, setup_date from approval_setup_mst where company_id=$data and status_active=1 order by setup_date desc";	
	echo create_list_view("list_view", "Setup Date", "300","350","260",0, $sql, "get_php_form_data", "id", "'approval_setup_from_data', 'requires/approval_necessity_setup_controller'", 1, "0", 0 , "setup_date", "",'','3') ;
	exit();
}

if($action=="approval_setup_from_data")
{
	$sql=sql_select("select company_id,setup_date from approval_setup_mst where id=$data and status_active=1 and is_deleted=0");
	foreach($sql as $row)
	{
		echo "document.getElementById('txt_date').value = '".change_date_format($row[csf("setup_date")],"dd-mm-yyyy","-")."';\n";
		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
		echo "fnc_LoadCompanyData('".$row[csf('company_id')]."_".change_date_format($row[csf("setup_date")],"dd-mm-yyyy","-")."')";
	}
	exit();
}

?>
