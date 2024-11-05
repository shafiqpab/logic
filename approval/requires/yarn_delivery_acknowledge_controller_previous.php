<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
extract($_REQUEST);

$permission=$_SESSION['page_permission'];

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$menu_id=$_SESSION['menu_id'];

if($db_type==0)
{
	$select_year="year";
	$year_format="";
	$group_concat="group_concat";
}
else if ($db_type==2)
{
	$select_year="to_char";
	$year_format=",'YYYY'";
	$group_concat="wm_concat";
}

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$kniting_company_arr=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0 $company_cod order by company_name","id","company_name");

$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$sequence_no='';
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_issue_purpose=str_replace("'","",$cbo_issue_purpose);
	$txt_issue_id=str_replace("'","",$txt_issue_id);
	$txt_bar_code=str_replace("'","",$txt_bar_code);

	if($txt_issue_id!="") $issue_cond=" and a.issue_number_prefix_num=$txt_issue_id";
	if($txt_bar_code!="") $barcode_cond=" and a.issue_number='$txt_bar_code'";
	
	if($cbo_issue_purpose!=0)
	{
		$txt_req_no="and a.issue_purpose=$cbo_issue_purpose";
	}
	else
	{
		$txt_req_no="";
	}
	
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.issue_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.issue_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.issue_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	$sql_job=sql_select("select b.id,a.buyer_name,a.job_no_prefix_num, a.job_no from wo_po_details_master a,  wo_po_break_down b where a.job_no=b.job_no_mst");
	foreach($sql_job as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['buyer_name']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['job_no_prefix_num']=$row[csf("job_no")];
	}
	
	$po_array=array(); // Buyer and Job No show
	if($db_type==0)
	{
		$po_array=return_library_array("select a.mst_id, group_concat(b.po_breakdown_id) as po_breakdown_id from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.mst_id", "mst_id", "po_breakdown_id" );
	}
	else
	{
		$po_array=return_library_array("select a.mst_id, LISTAGG(CAST( b.po_breakdown_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_breakdown_id from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category=1 and a.transaction_type=2 and b.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.mst_id", "mst_id", "po_breakdown_id" );
	} // Buyer and Job No show End

	if($approval_type==0) 
	{
		if($db_type==0)
		{
			$only_unapprove_data=return_field_value("group_concat(distinct(mst_id)) as mst_id","inv_issue_master a, yarn_acknowledge_mst b", "a.id=b.mst_id and a.company_id=$company_name and b.acknowledge_no=1","mst_id");
		}
		else
		{
			/*echo "SELECT mst_id FROM inv_issue_master a, yarn_acknowledge_mst b WHERE a.id=b.mst_id and a.company_id=$company_name and b.acknowledge_no=0";*/
			$only_unapprove_data=return_field_value("LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id) as mst_id","inv_issue_master a,
			yarn_acknowledge_mst b","a.id=b.mst_id and a.company_id=$company_name and b.acknowledge_no=1","mst_id");
			$only_unapprove_data=implode(",",array_unique(explode(",",$only_unapprove_data)));
		}
		$unapprove_data_cond="";
		if($only_unapprove_data!="") $unapprove_data_cond=" and a.id not in($only_unapprove_data)";
		//echo $unapprove_data_cond;

		if($db_type==0)
		{
			$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, '0' as approval_id, group_concat(distinct(case when a.issue_basis=3 then b.requisition_no end )) as requisition_no, group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved  
			from inv_issue_master a, inv_transaction b 
			where a.id=b.mst_id  and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=1 and a.ready_to_approve=1 $txt_req_no $date_cond $issue_cond $unapprove_data_cond $barcode_cond
			group by a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date, a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, a.is_approved";
		}
		else if($db_type==2)
		{
			$sql="SELECT a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, '0' as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved
			FROM  inv_issue_master a, inv_transaction b 
			WHERE a.id=b.mst_id and a.company_id=$company_name  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 and a.is_approved=1 and a.ready_to_approve=1 $txt_req_no $date_cond  $issue_cond $unapprove_data_cond $barcode_cond
			GROUP by a.id,  a.issue_number_prefix_num, a.issue_number,a.challan_no, a.company_id,a.insert_date, a.issue_purpose, a.issue_basis,a.knit_dye_source, a.issue_date, a.knit_dye_company, a.is_approved";
			//echo $sql;die;
		}
	}
	else
	{
		//LISTAGG(CAST( a.lc_sc_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.lc_sc_id) as lc_id
		if($db_type==0)
		{
			$sql="SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.issue_date,a.challan_no,a.knit_dye_source, a.knit_dye_company, group_concat(distinct c.id) as approval_id,  $group_concat(distinct(case when a.issue_basis=3 then  b.requisition_no end ) ) as requisition_no, $group_concat(distinct(case when a.issue_basis=1 then  a.booking_no end )) as booking_no, a.is_approved 
			from  inv_issue_master a, yarn_acknowledge_mst c, inv_transaction b
			where a.id=b.mst_id and b.mst_id=c.mst_id and a.company_id=$company_name and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(1,3) and c.acknowledge_no!=0 and a.ready_to_approve=1 $txt_req_no $date_cond $issue_cond $barcode_cond
			group by a.id order by a.insert_date desc";
		}
		else if($db_type==2)
		{
			$sql="SELECT a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, $select_year(a.insert_date $year_format) as year,  a.issue_purpose, a.issue_basis, a.issue_date,a.challan_no,a.knit_dye_source, a.knit_dye_company, LISTAGG(CAST( c.id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as approval_id, LISTAGG(CAST( (case when a.issue_basis=3 then  b.requisition_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.requisition_no) as requisition_no, LISTAGG(CAST( (case when a.issue_basis=1 then  a.booking_no end )  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.booking_no) as booking_no, a.is_approved
			from  inv_issue_master a,  yarn_acknowledge_mst c, inv_transaction b
			where a.id=b.mst_id and a.id=c.mst_id and a.company_id=$company_name and a.entry_form=3 and b.item_category=1 and b.transaction_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_approved in(1,3)  and c.acknowledge_no!=0 and a.ready_to_approve=1 $txt_req_no $date_cond $issue_cond $barcode_cond
			group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.insert_date, a.issue_purpose, a.issue_basis, a.issue_date, a.challan_no,a.knit_dye_company,a.knit_dye_source, a.is_approved
			order by a.insert_date desc";
			//, LISTAGG(CAST( d.po_breakdown_id  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.po_breakdown_id) as po_breakdown_id 
			// echo $sql;die;
		}
	}

	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:1180px; margin-top:10px">
        <legend></legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1170" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="70">System No</th>
                    <th width="100">Challan No</th>
                    <th width="80">Year</th>
                    <th width="100">Issue purpuse.</th>
                    <th width="100">Issue Date</th>
                    <th width="180">Issue to</th>
                    <th width="100">Basis</th>
                    <th width="110">Requisition No</th>
                    <th width="100">Buyer</th>
                    <th >Job No</th>
                </thead>
            </table>
            <div style="width:1170px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?
						
                            $i=1;
                            $nameArray=sql_select( $sql );$report_title="Yarn Issue";
                            foreach ($nameArray as $row)
                            {
								$approvar_id=implode(",",array_unique(explode(",",$row[csf('approval_id')])));
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								
								$value='';
								if($approval_type==0)
								{
									$value=$row[csf('id')];
								}
								else
								{
									$app_id=return_field_value("id","yarn_acknowledge_mst","mst_id ='".$row[csf('id')]."' order by id desc");
									$value=$row[csf('id')]."**".$app_id;
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="booking_id_<? echo $i;?>" name="booking_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="booking_no_<? echo $i;?>" name="booking_no]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $approvar_id; ?>" />
                                        <input id="<? echo strtoupper($row[csf('issue_number_prefix_num')]); ?>" name="no_issue[]" type="hidden" value="<? echo $i;?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="70">
                                    	<p><a href='##' style='color:#000'onclick="generate_yarn_report('<? echo $row[csf('company_id')]; ?>','<? echo $row[csf('issue_number')]; ?>','<? echo $report_title; ?>','<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('is_approved')]; ?>','<? echo $row[csf('id')]; ?>','yarn_issue_print')">
                                        <?
                                        echo $row[csf('issue_number_prefix_num')];
										?>
                                       </a></p>
                                    </td>
                                    <td width="100" align="center"><? echo  $row[csf('challan_no')]; ?></td>
                                    <td width="80"><p><?  echo $row[csf('year')]; ?>&nbsp;</p></td>
									<td width="100" align="left"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</p></td>
                                    <td width="100" align="center"><? if($row[csf('issue_date')]!="0000-00-00") echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</td>
                                    <td width="180" align="left">&nbsp;
									<?
									if($row[csf('knit_dye_source')]==1)
									{
									 echo $kniting_company_arr[$row[csf('knit_dye_company')]]; 
									}
									else if($row[csf('knit_dye_source')]==3)
									{
										echo $supplier_arr[$row[csf('knit_dye_company')]];
									}
									?>
                                    </td>
									<td align="center" width="100"><? echo $issue_basis[$row[csf('issue_basis')]]; ?>&nbsp;</td>
                                    <?
									 if($row[csf('issue_basis')]==1)
									 {
										?>
										<td width="110" ><p>
										<?
										 $booking_no=implode(",",array_unique(explode(",",$row[csf('booking_no')]))); echo $booking_no;
										?>
										&nbsp;</p></td>
										<?
									 }
									 else if($row[csf('issue_basis')]==3)
									 {
										?>
										<td align="center" width="110"><p>
										<?
										$req_arr=array_unique(explode(",",$row[csf('requisition_no')]));
										foreach($req_arr as $req_no)
										{
											if($req_no!=0)
											{
												?>
												<a href='##' style='color:#000'onclick="generate_worder_report('<? echo $row[csf('company_id')]; ?>','<? echo $req_no; ?>')">
												<?
												echo $req_no."<br>";
												?>
												</a>
												<?
											}
										}
										?>
										 &nbsp;</p></td>
										<?
									 }
									 else
									 {
										?>
										<td align="center" width="110"> &nbsp;</p></td>
										<?
									 }
									 ?>
                                     <td width="100" align="center">
									 <?
									  $po_id_arr=array_unique(explode(",",$po_array[$row[csf('id')]]));
									  $buyer_all="";
									  $job_all="";
									  $temp_buyer=array();
									  $temp_job=array();
									  foreach($po_id_arr as $po_id)
									  {
										  if(!in_array($buyer_po_arr[$po_id]["buyer_name"],$temp_buyer))
										  {
											  $temp_buyer[]=$buyer_po_arr[$po_id]["buyer_name"];
											  if($buyer_all!="") $buyer_all.=", ";
											  $buyer_all.=$buyer_short_arr[$buyer_po_arr[$po_id]["buyer_name"]];
										  }
										  if(!in_array($buyer_po_arr[$po_id]["job_no_prefix_num"],$temp_job))
										  {
											  $temp_job[]=$buyer_po_arr[$po_id]["job_no_prefix_num"];
											  if($job_all!="") $job_all.=", ";
											  $job_all.=$buyer_po_arr[$po_id]["job_no_prefix_num"];
										  }
									  }
										echo $buyer_all; 
									 ?>
                                     </td>
                                     <td>
									 <? 
										echo $job_all;
									 ?>
                                     </td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="1170" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Acknowledge"; else echo "Acknowledge"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=23;
	//echo $operation;die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$msg=''; $flag=''; $response='';	
	if($approval_type==0)
	{
		$response=$booking_ids;
		// echo $booking_ids;die;

		$field_array="id, mst_id, acknowledge_no, inserted_by, insert_date"; 
		$id=return_next_id( "id","yarn_acknowledge_mst", 1 ) ;

		$booking_ids_all=explode(",",$booking_ids);
		$booking_nos_all=explode(",",$booking_nos);
		
		$duplicate_data = array();
		for($i=0;$i<count($booking_nos_all);$i++)
		{
			$booking_id=$booking_ids_all[$i];
			
			//if($data_array!="") $data_array.=",";
			/*$data_array.="(".$id.",".$booking_id.",1,".$user_id.",'".$pc_date_time."')";*/ 
			//$id=$id+1;

			$duplicate_data[$booking_id]=$booking_id;

			//echo "SELECT mst_id from yarn_acknowledge_mst where mst_id in('".$booking_id."') ";
			//echo $duplicate_data = return_field_value("mst_id", "yarn_acknowledge_mst", "mst_id in($booking_id)", "mst_id");
			//print_r($duplicate_data);die;
		}
		$booking_mst_arr = array();
		$booking_mst_data = sql_select("SELECT mst_id from yarn_acknowledge_mst");
		
		foreach ($booking_mst_data as $key => $row) 
		{
			$booking_mst_arr[$row[csf('mst_id')]] = $row[csf('mst_id')];
			//echo '<pre>';  print_r($row); die;
		}
		// echo '<pre>';  print_r($booking_mst_arr); die;
		// print_r($duplicate_data);die;
		foreach ($duplicate_data as $key_val => $value) 
		{
			if (array_key_exists($key_val,$booking_mst_arr))
			{
				//echo $value.'found in db';
				$query="UPDATE yarn_acknowledge_mst SET acknowledge_no=1, inserted_by=$user_id, insert_date='$pc_date_time' WHERE mst_id=$value";
				$rIDapp=execute_query($query,1);
				if($rIDapp) $flag=1; else $flag=0;
			}
			else
			{
				//echo $value.'new in db';
				$data_array="(".$id.",".$key_val.",1,".$user_id.",'".$pc_date_time."')";
				$id+=1;
				$rID=sql_insert("yarn_acknowledge_mst",$field_array,$data_array,1);
				if($rID) $flag=1; else $flag=0;
			}
		}
		// die;
		// print_r($duplicate);die;
		// echo "5**insert into yarn_acknowledge_mst (".$field_array.") values ".$data_array;die;
		// echo "21**".$rID2;die;
		if($flag==1) $msg='19'; else $msg='21';
	}
	
	else
	{
		//echo($booking_ids);die;
		$booking_ids_all=explode(",",$booking_ids);
		$booking_ids=''; $app_ids='';
		foreach($booking_ids_all as $value)
		{
			$data = explode('**',$value);
			$booking_id=$data[0];
			$app_id=$data[1];
			if($booking_ids=='') $booking_ids=$booking_id; else $booking_ids.=",".$booking_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}
		
		//if($rID) $flag=1; else $flag=0;

		// $data=$user_id."*'".$pc_date_time."'";
		// $rID3=sql_multirow_update("yarn_acknowledge_mst","un_approved_by*un_approved_date",$data,"id",$app_ids,1);
		// echo $booking_id.'*'.$app_ids;die;

		/*$is_acknowledge_no=0;
		$data=$is_acknowledge_no."*'".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("yarn_acknowledge_mst","acknowledge_no*updated_by*update_date",$data,"id",$booking_id,1);
		if($rID3) $flag=1; else $flag=0;
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0; 
		}*/

		$query="UPDATE yarn_acknowledge_mst SET acknowledge_no=0, updated_by=$user_id, update_date='$pc_date_time' WHERE id in ($app_ids)";
		$rIDapp=execute_query($query,1);
		if($rIDapp) $flag=1; else $flag=0;
		if($flag==1)
		{
			if($rIDapp) $flag=1; else $flag=0;
		}

		// echo "22**".$rIDapp;die;
		$response=$booking_ids;
		if($flag==1) $msg='20'; else $msg='22';
	}
	
	if($db_type==0)
	{ 
		if($flag==1)
		{
			mysql_query("COMMIT");  
			echo $msg."**".$response;
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo $msg."**".$response;
		}
	}
	//release lock table   oci_commit($con); oci_rollback($con); 
	if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);  
			echo $msg."**".$response;
		}
		else
		{
			oci_rollback($con); 
			echo $msg."**".$response;
		}
	}
	disconnect($con);
	die;
}

if($action=="img")
{
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
		<fieldset style="width:600px; margin-left:5px">
			<div style="width:100%; word-wrap:break-word" id="scroll_body">
	             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
	             	<tr>
						<?
						$i=0;
	                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='quotation_entry' and file_type=1";
	                    $result=sql_select($sql);
	                    foreach($result as $row)
	                    {
							$i++;
	                    ?>
	                    	<td align="center"><img width="300px" height="180px" src="../../<? echo $row[csf('image_location')];?>" /></td>
	                    <?
							if($i%2==0) echo "</tr><tr>";
	                    }
	                    ?>
	                </tr>
	            </table>
	        </div>	
		</fieldset>     
	<?
	exit();
}

if($action=='user_popup')
{
	echo 333333;die;
	echo load_html_head_contents("Popup Info","../../",1, 1,'',1,'');
	?>	

	<script>

	// flowing script for multy select data------------------------------------------------------------------------------start;
	  function js_set_value(id)
	  { 
	 // alert(id)
		document.getElementById('selected_id').value=id;
		  parent.emailwindow.hide();
	  }

	// avobe script for multy select data------------------------------------------------------------------------------end;

	</script>

	<form>
	        <input type="hidden" id="selected_id" name="selected_id" /> 
	       <?php
	        $custom_designation=return_library_array( "select id,custom_designation from lib_designation ",'id','custom_designation');	
			 $Department=return_library_array( "select id,department_name from  lib_department ",'id','department_name');	;
			 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_company_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_company_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
				echo $sql;die;
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	        
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}

if($action=="file")
{
	echo load_html_head_contents("File View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
		<fieldset style="width:600px; margin-left:5px">
			<div style="width:100%; word-wrap:break-word" id="scroll_body">
	             <table border="0" rules="all" width="100%" cellpadding="2" cellspacing="2">
	             	<tr>
						<?
						$i=0;
	                    $sql="select image_location from common_photo_library where master_tble_id='$id' and form_name='quotation_entry' and file_type=2";
	                    $result=sql_select($sql);
	                    foreach($result as $row)
	                    {
							$i++;
	                    ?>
	                    	<td width="100" align="center"><a href="../../<? echo $row[csf('image_location')]; ?>"><img width="89" height="97" src="../../file_upload/blank_file.png"><br>File-<? echo $i; ?></a></td>
	                    <?
							if($i%6==0) echo "</tr><tr>";
	                    }
	                    ?>
	                </tr>
	            </table>
	        </div>	
		</fieldset>     
	<?
	exit();
}

if($action=="show_requision")
{
	extract($_REQUEST);
	echo "$rtn_no";
}



?>