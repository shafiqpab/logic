<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($_SESSION['logic_erp']["data_level_secured"]==1)
{
	//if($_SESSION['logic_erp']["buyer_id"]!=0) $buyer_cond=" and id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
	if($_SESSION['logic_erp']["company_id"]!=0) $company_cond=" and id in (".$_SESSION['logic_erp']["company_id"].")"; else $company_cond="";
}
else
{
	$buyer_cond="";	$company_cond="";
}
$permission=$_SESSION['page_permission'];
//---------------------------------------------------- Start
$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
$color_library=return_library_array( "select id,color_name from lib_color where status_active =1 and is_deleted=0", "id", "color_name" );


if($action=="show_sample_approved_list1")
{
	$data=explode('_',$data);
	$sql="select distinct sample_type_id from wo_po_sample_approval_info where job_no_mst='$data[0]' and is_deleted=0 and status_active=1 and (entry_form_id is null or entry_form_id=0)";
	$data_array=sql_select($sql);
	?>
	<table>
	<?
	foreach($data_array as $row)
	{
		?>
		<tr>
            <td><a href="##" onClick="load_form(<? echo $row['sample_type_id'];  ?>)"><? echo $sample_library[$row['sample_type_id']];  ?></a></td>
		</tr>
		<?
	}
	?>
	</table>
	<?
	exit();
}

if($action=="show_sample_approved_list")
{
	if($db_type==0) $sequNullCheck="IFNULL(b.sequ,0)";
	else if($db_type==2) $sequNullCheck="nvl(b.sequ,0)";
	$data=explode('_',$data);
	$buyer_id=$data[1];
	$sql="select min(a.id) as id , a.sample_type_id from wo_po_sample_approval_info a, lib_buyer_tag_sample b where b.tag_sample=a.sample_type_id and a.job_no_mst='$data[0]' and a.is_deleted=0 and a.status_active=1 and b.business_nature=2 and b.buyer_id=$buyer_id and $sequNullCheck!=0 and (a.entry_form_id is null or a.entry_form_id=0)  group by a.sample_type_id order by id";

	$data_array=sql_select($sql);

	$approval_sql="SELECT id, po_break_down_id, sample_type_id, approval_status from wo_po_sample_approval_info where job_no_mst='$data[0]' and is_deleted=0 and status_active=1";
	//echo $approval_sql; die;
	$dataArray=sql_select($approval_sql);
	$partial_approved=0; $full_approved=0; $unapproved=0;
	foreach ($dataArray as $row) {
		$sample_approval_status_arr[$row[csf('sample_type_id')]][$row[csf('po_break_down_id')]]=$row[csf('approval_status')];
	}
	$partial_approved = 0;
	$sample_approved_status=array();
	foreach ($sample_approval_status_arr as $sample_id => $po_app_arr) {
		$status = 'Fully Approved';
		$partial_approved = 0;
		if(count($po_app_arr)>1)
		{
			foreach ($po_app_arr as $key1 => $value1) {
				$unapproved = 0;
				foreach ($po_app_arr as $key2 => $value2) {
					if($key1 != $key2 && $value1 == 3 && $value2 != 3){
						$partial_approved = 1 ; 
						break;			
					}
					if($key1 != $key2 && $value1 != 3 && $value2 != 3){
						$unapproved++;				
					}
				};
				if($partial_approved > 0){
					$status = 'Partial Approved';
					break;
				}
				elseif($unapproved == count($po_app_arr) - 1){
					$status = 'Full Pending';
					break;
				}
			}
		}
		else{
			foreach ($po_app_arr as $key => $value) {
				if($value == 3){
					$status = 'Fully Approved';
				}
				else{
					$status = 'Unapproved';
				}
			}
		}
		$sample_approved_status[$sample_id] = $status;
	}
    foreach($data_array as $row)
	{
	?>
        <h3 align="left" id="accordion_h<? echo $row[csf("sample_type_id")]; ?>" style="width:910px" class="accordion_h" onClick="load_form(<? echo $row[csf("sample_type_id")];?>,<? echo $data ?>)"><span id="accordion_h<? echo $row[csf("sample_type_id")]; ?>span">+</span><? echo $sample_library[$row[csf("sample_type_id")]]; ?> <span style="color: red">[<? echo $sample_approved_status[$row[csf("sample_type_id")]] ?>]</span></h3>
	<?
	}
	exit();
}

if ($action=="show_sample_approval_list_form")
{
	$data=explode('_',$data);
	$i=0;
	$approval_sql="SELECT id, po_break_down_id, sample_type_id, approval_status from wo_po_sample_approval_info where job_no_mst='$data[0]' and is_deleted=0 and status_active=1 and sample_type_id ='$data[1]'";
	//echo $approval_sql; die;
	$dataArray=sql_select($approval_sql);
	$partial_approved=0; $full_approved=0; $unapproved=0;
	foreach ($dataArray as $row) {
		$sample_approval_status_arr[$row[csf('sample_type_id')]][$row[csf('po_break_down_id')]]=$row[csf('approval_status')];
	}
	$partial_approved = 0;
	$sample_approved_status=array();
	foreach ($sample_approval_status_arr as $sample_id => $po_app_arr) {
		$status = 'Fully Approved';
		$partial_approved = 0;
		if(count($po_app_arr)>1)
		{
			foreach ($po_app_arr as $key1 => $value1) {
				$unapproved = 0;
				foreach ($po_app_arr as $key2 => $value2) {
					if($key1 != $key2 && $value1 == 3 && $value2 != 3){
						$partial_approved = 1 ; 
						break;			
					}
					if($key1 != $key2 && $value1 != 3 && $value2 != 3){
						$unapproved++;				
					}
				};
				if($partial_approved > 0){
					$status = 'Partial Approved';
					break;
				}
				elseif($unapproved == count($po_app_arr) - 1){
					$status = 'Full Pending';
					break;
				}
			}
		}
		else{
			foreach ($po_app_arr as $key => $value) {
				if($value == 3){
					$status = 'Fully Approved';
				}
				else{
					$status = 'Unapproved';
				}
			}
		}
		$sample_approved_status[$sample_id] = $status;
	}	
		?>
		<h3 align="left" class="accordion_h" > +<? echo $sample_library[$data[1]];  ?><span style="color: red">[<? echo $sample_approved_status[$data[1]] ?>]</span></h3>
		<div id="row_<? echo $data[1];  ?>" >
            <form name="sampleapproval_1" id="sampleapproval_1" autocomplete="off">

                <table id="tbl_sample_info" class="rpt_table" rules="all">
                    <thead>
                        <tr>
                        <th width="100">Po Number</th>
                        <th width="100">Color </th>
                        <th width="100">Sample Type </th>
                        <th width="100" class="must_entry_caption">Target Approval Date</th>
                        <th width="100" class="must_entry_caption">Sent To Sample Section</th>
                        <th width="100" class="must_entry_caption">Submission to Buyer </th>
                        <th width="100" class="must_entry_caption">Action</th>
                        <th width="100" class="must_entry_caption">Action Date</th>
                        <th width="100">Merchant Comments</th>
                        <th width="100" style="display:none">Sample Department Comments</th>
                        <th width="100">Status</th>
                        <th width="100"></th>
                        </tr>
                    </thead>
                    <tbody>
						<?
						$data_array1=sql_select("select a.id as po_id,a.po_number,min(b.id) as color_mst_id, b.color_number_id,c.id as sample_table_id  from  wo_po_break_down a, wo_po_color_size_breakdown b, wo_po_sample_approval_info c where a.job_no_mst=b.job_no_mst and b.job_no_mst=c.job_no_mst and a.id=b.po_break_down_id and b.po_break_down_id=c.po_break_down_id and  b.id=c.color_number_id  and a.job_no_mst='$data[0]'  and c.sample_type_id ='$data[1]'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.current_status = 1 and b.status_active=1 and (c.entry_form_id is null or c.entry_form_id=0) group by a.id,a.po_number,color_mst_id,b.color_number_id,c.id order by a.id,color_mst_id");   //group by c.id
						if (count($data_array1)<=0)
					    {

                        $data_array1=sql_select("select a.id as po_id,a.po_number, min(b.id) as color_mst_id, b.color_number_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst='$data[0]' and a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,a.po_number,color_mst_id, b.color_number_id order by a.id");
						}

                        foreach ( $data_array1 as $row1)
                        {
						   $i++;
							$data_array_sample_table=sql_select("Select id,target_approval_date,send_to_factory_date,submitted_to_buyer,approval_status,approval_status_date,sample_comments ,current_status,status_active from wo_po_sample_approval_info where job_no_mst='$data[0]' and po_break_down_id='".$row1[csf('po_id')]."' and color_number_id ='".$row1[csf('color_mst_id')]."' and sample_type_id ='$data[1]' and  id='".$row1[csf('sample_table_id')]."'");
							list($sample_table_data_array )=$data_array_sample_table;
							if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 )
							{
							$disabled=1;
							}
							else
							{
							$disabled=0;
							}

                        ?>
                            <tr>
                                <td>
									<?
                                    echo create_drop_down( "cbopono_".$i, 140, "select po_number,id from wo_po_break_down where job_no_mst='$data[0]' and is_deleted=0 and status_active=1 order by po_number","id,po_number", '', "", $row1[csf("po_id")], "",1,'' );
                                    ?>
                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cbocolor_".$i, 100, "select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id='".$row1[csf('po_id')]."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name","color_number_id,color_name", '', "", $row1[csf('color_number_id')], "",1,'' );
                                    ?>
                                    <input name="colorsizetableid_<? echo $i; ?>" type="hidden" id="colorsizetableid_<? echo $i; ?>" style="width:180px" class="text_boxes" value="<? echo $row1[csf("color_mst_id")];  ?>"/>
                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cbosampletype_".$i, 140, "select sample_name,id from lib_sample where is_deleted=0 and status_active=1 order by sample_name","id,sample_name", '', "", $data[1], "",1,'' );
                                    ?>
                                </td>
                                <td>
                                    <input name="txttargetapprovaldate_<? echo $i; ?>" type="text" id="txttargetapprovaldate_<? echo $i; ?>" style="width:80px" class="datepicker" onChange="copy_value(this.value,'txttargetapprovaldate_',<? echo $i;?>)" value="<? if($sample_table_data_array[csf("target_approval_date")]!="") echo change_date_format($sample_table_data_array[csf("target_approval_date")],'dd-mm-yyyy','-');  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
                                    <input name="txtsendtofatorydate_<? echo $i; ?>" type="text" id="txtsendtofatorydate_<? echo $i; ?>" onChange="check_date_status(1);copy_value(this.value,'txtsendtofatorydate_',<? echo $i;?>)" style="width:80px"   class="datepicker" value="<? if($sample_table_data_array[csf("send_to_factory_date")]!="" || $sample_table_data_array[csf("send_to_factory_date")]!="0000-00-00") echo change_date_format($sample_table_data_array[csf("send_to_factory_date")],'dd-mm-yyyy','-');  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
                                    <input name="txtsubmissiontobuyerdate_<? echo $i; ?>" type="text" id="txtsubmissiontobuyerdate_<? echo $i; ?>"   onchange="check_date_status(12);copy_value(this.value,'txtsubmissiontobuyerdate_',<? echo $i;?>)" style="width:80px"  class="datepicker" value="<? if($sample_table_data_array[csf("submitted_to_buyer")]!="" || $sample_table_data_array[csf("submitted_to_buyer")]!="0000-00-00") echo change_date_format($sample_table_data_array[csf("submitted_to_buyer")],'dd-mm-yyyy','-');  ?>" <? if($sample_table_data_array[csf("approval_status")]==5 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cboapprovalstatus_".$i, 100, $approval_status,"", 1, "--   --", $sample_table_data_array[csf("approval_status")], "copy_value(this.value,'cboapprovalstatus_',". $i.")",$disabled,'' );
                                    ?>
                                </td>
                                <td>
                                    <input name="txtapprovalrejectdate_<? echo $i; ?>" type="text" id="txtapprovalrejectdate_<? echo $i; ?>" style="width:80px" class="datepicker" onChange="copy_value(this.value,'txtapprovalrejectdate_',<? echo $i;?>)" value="<? if($sample_table_data_array[csf("approval_status_date")]!="" || $sample_table_data_array[csf("approval_status_date")]!="0000-00-00") echo change_date_format($sample_table_data_array[csf("approval_status_date")],'dd-mm-yyyy','-');  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 || $sample_table_data_array[csf("approval_status")]==4 ){echo "disabled";} else{echo "";} ?>/>
                                </td>
                                <td>
                                    <input name="txtsamplecomments_<? echo $i; ?>" type="text" id="txtsamplecomments_<? echo $i; ?>" style="width:180px" class="text_boxes" onChange="copy_value(this.value,'txtsamplecomments_',<? echo $i;?>)" value="<? echo $sample_table_data_array[csf("sample_comments")];  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==4 ){echo "disabled";} else{ echo "";} ?> placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly/>
                                </td>
                                 <td style="display:none">
                                    <input name="txtsampledepartmentcomments_<? echo $i; ?>" type="text" id="txtsampledepartmentcomments_<? echo $i; ?>" style="width:180px" class="text_boxes" onChange="copy_value(this.value,'txtsamplecomments_',<? echo $i;?>)" value="<? echo $sample_table_data_array[csf("sample_department_comments")];  ?>" <? if($sample_table_data_array[csf("approval_status")]==2 || $sample_table_data_array[csf("approval_status")]==3 ){echo "disabled";} else{ echo "";} ?> placeholder="Single Click" onClick="fnc_comments(this.id,this.value)" readonly/>
                                </td>
                                <td>
									<?
                                    echo create_drop_down( "cbostatus_".$i, 80, $row_status,"", '', "", $sample_table_data_array[csf("status_active")], "",$disabled,'' );
                                    ?>
                                    <input type="hidden" id="updateid_<? echo $i; ?>" value="<? echo $sample_table_data_array[csf('id')];  ?>" style="width:40">
                                </td>

                                <td>
                                <?

								if($sample_table_data_array[csf("approval_status")]==2 && $sample_table_data_array[csf("current_status")]==1)
								{
								?>
								<input type="button" id="addrow_<? echo $i; ?>"  name="addrow_<? echo $i; ?>" style="width:60px" class="formbutton" value=" Re-Submit" onClick="resubmit(<? echo $i; ?>)" />
                                <?
								}
								?>
                                </td>
                            </tr>
                        <?
                        }
                        ?>

                    </tbody>
                </table>
                <table>
                </table>
            </form>
		</div>
		<?
		exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_name and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}

if ($action=="load_drop_down_po")
{
	echo create_drop_down( "cbo_po_no", 140, "select id , po_number from wo_po_break_down where job_no_mst='$data' and status_active=1 and is_deleted=0","id,po_number", 1, "-- Select Po --", '', "load_drop_down( '../woven_order/requires/sample_approval_controller', this.value, 'load_drop_down_color', 'color_td' );" );
}

if ($action=="load_drop_down_color")
{
	echo create_drop_down( "cbo_color", 140, "select a.color_number_id, b.color_name from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 group by a.color_number_id","color_number_id,color_name", 1, "-- Select Color --", '', "" );
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

		 $id=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 $field_array="id,garments_nature,job_no_mst,po_break_down_id, 	color_number_id,sample_type_id,target_approval_date,send_to_factory_date,submitted_to_buyer,approval_status,approval_status_date,sample_comments,is_deleted,status_active,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbopono="cbopono_".$i;
			 $cbocolor="colorsizetableid_".$i;
			 $cbosampletype="cbosampletype_".$i;
			 $txttargetapprovaldate="txttargetapprovaldate_".$i;
			 $txtsendtofatorydate="txtsendtofatorydate_".$i;
			 $txtsubmissiontobuyerdate="txtsubmissiontobuyerdate_".$i;
			 $cboapprovalstatus="cboapprovalstatus_".$i;
			 $txtapprovalrejectdate="txtapprovalrejectdate_".$i;
			 $txtsamplecomments="txtsamplecomments_".$i;
			 $cbostatus="cbostatus_".$i;
			 $updateid="updateid_".$i;
			 if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$garments_nature.",".$txt_job_no.",".$$cbopono.",".$$cbocolor.",".$$cbosampletype.",".$$txttargetapprovaldate.",".$$txtsendtofatorydate.",".$$txtsubmissiontobuyerdate.",".$$cboapprovalstatus.",".$$txtapprovalrejectdate.",".$$txtsamplecomments.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		 }
		 $rID=sql_insert("wo_po_sample_approval_info",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**"."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**"."**".$rID;
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
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 $id=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 $add_comma=0;
		 $field_array="id,garments_nature,job_no_mst,po_break_down_id, 	color_number_id,sample_type_id,target_approval_date,send_to_factory_date,submitted_to_buyer,approval_status,approval_status_date,sample_comments,is_deleted,status_active,inserted_by,insert_date";
		 $field_array_update="job_no_mst*po_break_down_id*color_number_id*sample_type_id*target_approval_date*send_to_factory_date*submitted_to_buyer*approval_status*approval_status_date*sample_comments*is_deleted*status_active*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $cbopono="cbopono_".$i;
			 $cbocolor="colorsizetableid_".$i;
			 $cbosampletype="cbosampletype_".$i;
			 $txttargetapprovaldate="txttargetapprovaldate_".$i;
			 $txtsendtofatorydate="txtsendtofatorydate_".$i;
			 $txtsubmissiontobuyerdate="txtsubmissiontobuyerdate_".$i;
			 $cboapprovalstatus="cboapprovalstatus_".$i;
			 $txtapprovalrejectdate="txtapprovalrejectdate_".$i;
			 $txtsamplecomments="txtsamplecomments_".$i;
			 $cbostatus="cbostatus_".$i;
			 $updateid="updateid_".$i;
			 if(str_replace("'",'',$$updateid)!="")
			 {
				$id_arr[]=str_replace("'",'',$$updateid);
				$data_array_update[str_replace("'",'',$$updateid)] = explode(",",("".$txt_job_no.",".$$cbopono.",".$$cbocolor.",".$$cbosampletype.",".$$txttargetapprovaldate.",".$$txtsendtofatorydate.",".$$txtsubmissiontobuyerdate.",".$$cboapprovalstatus.",".$$txtapprovalrejectdate.",".$$txtsamplecomments.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
			 }
			 if(str_replace("'",'',$$updateid)=="")
			 {
				 if ($add_comma!=0) $data_array .=",";
				 $data_array .="(".$id.",".$garments_nature.",".$txt_job_no.",".$$cbopono.",".$$cbocolor.",".$$cbosampletype.",".$$txttargetapprovaldate.",".$$txtsendtofatorydate.",".$$txtsubmissiontobuyerdate.",".$$cboapprovalstatus.",".$$txtapprovalrejectdate.",".$$txtsamplecomments.",0,".$$cbostatus.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $add_comma++;
				 $id=$id+1;
			 }
		 }
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_sample_approval_info", "id", $field_array_update,$data_array_update, $id_arr ),1);
		 if($data_array !='')
		 {
		 $rID=sql_insert("wo_po_sample_approval_info",$field_array,$data_array,1);
		 }
		 $resubmit_id=rtrim(str_replace("'","",$resubmit_id),",");
		 if( $resubmit_id!="")
		 {
			$rID=execute_query("update wo_po_sample_approval_info set current_status=0 where id in($resubmit_id)",1);
		 }
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**"."**".$rID;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**"."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		    if($rID)
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
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("wo_po_sample_approval_info",$field_array,$data_array,"id","".$update_id."",1);
		//echo $rID;die;
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$rID;
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
		die;
	}
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Sample Approval Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}

	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}

    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="1100" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                 <th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>                     	 
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="172" class="must_entry_caption">Buyer Name</th>
                <th width="80">Job No</th>
                <th width="100">Style Ref </th>
                <th width="100">Order No</th>
                <th width="80">Internal Ref.</th>
                <th width="130" colspan="2">Date Range</th>
                <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>  
            </tr>
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_job">
                <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'size_color_breakdown_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );"); ?>
            </td>
            <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 172, $blank_array,"", 1, "-- Select Buyer --" ); ?></td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:90px"></td>
            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:90px"></td>
            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From"></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To"></td>
            <td>
            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view', 'search_div', 'sample_approval_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
    </tr>
    <tr>
        <td colspan="9" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
    </tr>
 </table>
    <div id="search_div"></div>
    </form>
   </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{

	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }

	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else  $buyer="";//{ echo "Please Select Buyer First."; die; }
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else
	{
		$buyer=" and a.buyer_name=$data[1]";
	}
	if($db_type==0) { $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]";  $insert_year="SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year";  }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";   $insert_year="to_char(a.insert_date,'YYYY') as year";}
	//if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]'  $year_cond "; else  $job_cond="";
	//if (str_replace("'","",$data[7])!="") $order_cond=" and b.po_number like '%$data[7]%'  "; else  $order_cond="";

	$job_cond=""; $order_cond=""; $style_cond=""; $internalRefCond="";
	if($data[9]==1)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num='$data[5]' $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[7])!="") $order_cond=" and b.po_number = '$data[7]'  "; //else  $order_cond="";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no ='$data[8]'"; //else  $style_cond="";
		if (trim($data[10])!="") $internalRefCond=" and b.grouping ='$data[10]'";
	}
	if($data[9]==2)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '$data[5]%' $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[7])!="") $order_cond=" and b.po_number like '$data[7]%'  "; //else  $order_cond="";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '$data[8]%'  "; //else  $style_cond="";
		if (trim($data[10])!="") $internalRefCond=" and b.grouping like '$data[10]%'  ";
	}
	if($data[9]==3)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]' $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[7])!="") $order_cond=" and b.po_number like '%$data[7]'  "; //else  $order_cond="";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]'"; //else  $style_cond="";
		if (trim($data[10])!="") $internalRefCond=" and b.grouping like '%$data[10]'";
	}
	if($data[9]==4 || $data[9]==0)
	{
		if (str_replace("'","",$data[5])!="") $job_cond=" and a.job_no_prefix_num like '%$data[5]%' $year_cond"; //else  $job_cond="";
		if (str_replace("'","",$data[7])!="") $order_cond=" and b.po_number like '%$data[7]%'  "; //else  $order_cond="";
		if (trim($data[8])!="") $style_cond=" and a.style_ref_no like '%$data[8]%'"; //else  $style_cond="";
		if (trim($data[10])!="") $internalRefCond=" and b.grouping like '%$data[10]%'";
	}

	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	if($db_type==1 || $db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (2=>$comp,3=>$buyer_arr);

	if ($data[2]==0)
	{
		  $sql= "select a.job_no_prefix_num, $insert_year, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.grouping, b.po_quantity, b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  $shipment_date $company $buyer $job_cond $style_cond $order_cond $internalRefCond order by a.id DESC";

		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,Internal Ref,PO Quantity,Shipment Date", "50,60,120,100,100,80,90,80,80,70","950","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,grouping,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,1,3');
	}
	else
	{
		$sql= "select a.job_no_prefix_num,$insert_year, a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer $job_cond  order by a.id DESC";

		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,", "90,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "",'','0,0,0,0,0,1,0,2,3');
	}

}

if ($action=="populate_data_from_search_popup")
{
	if($db_type==0) $sequNullCheck="IFNULL(b.sequ,0)";
	else if($db_type==2) $sequNullCheck="nvl(b.sequ,0)";
	$buyer_id="";
	$data_array=sql_select("select id,garments_nature,job_no,job_no_prefix,job_no_prefix_num,company_name,buyer_name,location_name,style_ref_no,style_description,product_dept,currency_id,agent_name,order_repeat_no,region,team_leader,dealing_marchant,packing,remarks,ship_mode from wo_po_details_master where job_no='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";
		//echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";
		$buyer_id=$row[csf("buyer_name")];
	}
	$sample_array=array();
	$sample_tag=sql_select("select a.id,a.sample_name,b.tag_sample,b.sequ from lib_sample a, lib_buyer_tag_sample b where a.id=b.tag_sample and $sequNullCheck!=0 and b.buyer_id='".$buyer_id."' order by b.sequ");
	foreach($sample_tag as $sample_tag_row)
	{
	 $sample_array[$sample_tag_row[csf('id')]]=	$sample_tag_row[csf('sample_name')];
	}
	//print_r($sample_array);
	if(count($sample_array)>0)
	{
		$sample_dropdown=create_drop_down( "cbo_sample_type", 140, $sample_array,"", '1', "--Select--", '', "load_form(this.value)",1,'' );
		echo "document.getElementById('dropdown_span').innerHTML = '".$sample_dropdown."';\n";
		echo "document.getElementById('msg_span').innerHTML = '';\n";
	}
	else
	{
		echo "document.getElementById('msg_span').innerHTML = 'No Sample Taged with this buyer';\n";
	}
	exit();
}

if ($action=="order_search_popup")
{
	echo load_html_head_contents("Order Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	?>
	<script>
		var selected_id = new Array, selected_name = new Array();
		//var selected_color_id = new Array, selected_color_name = new Array();
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				js_set_value( i );
			}
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str_data )
		{
			//toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			var str_all=str_data.split("_");
			var str=str_all[0]+'_'+str_all[2];
			var str_po=str_all[1]+'_'+str_all[3];;
			//var str_color_id=str_all[2];
			//var str_color_name=str_all[3];
			if( jQuery.inArray( str , selected_id ) == -1 )
			{
				//alert("if");
				selected_id.push( str );
				selected_name.push( str_po );
				//selected_color_id.push( str_color_id );
				//selected_color_name.push( str_color_name );
			}
			else
			{
					//alert("else");
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == str) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				//selected_color_id.splice( i, 1 );
				//selected_color_name.splice( i, 1 );
			}



			var id = '' ; var name = '';
			var color_id = '' ; var color_name = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				var data_id=selected_id[i].split("_");
				var data_name=selected_name[i].split("_");
				id += data_id[0] + ',';
				name += data_name[0] + ',';
				color_id += data_id[1] + ',';
				color_name += data_name[1] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			color_id = color_id.substr( 0, color_id.length - 1 );
			color_name = color_name.substr( 0, color_name.length - 1 );
			$('#po_number_id').val( id );
			$('#po_number').val( name );
			$('#color_id').val( color_id );
			$('#po_color').val( color_name );
			//var data=id.split(",");
			//for( var j = 0; j < selected_id.length; j++ )
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchpofrm" id="searchpofrm">
                <fieldset style="width:1000px">
                    <table width="950" cellspacing="0" cellpadding="0" border="0" class="rpt_table" rules="all" align="center">
                        <tr>
                            <td colspan="4" align="center">
                                Select PO Number: &nbsp;<input type="text" class="text_boxes"  readonly style="width:350px" id="po_number">
                                Select PO Color: &nbsp;<input type="text" class="text_boxes"  readonly style="width:350px" id="po_color">
                                <input type="text" id="po_number_id">
                                <input type="text" id="color_id">
                            </td>
                        </tr>
                        <tr>
                            <td id="search_div">
								<?
                                $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
                                $comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
                                $arr=array (1=>$comp,2=>$buyer_arr);
                                $sql= "select a.job_no_prefix_num, a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity,b.id, b.po_number,b.po_quantity,b.shipment_date,c.color_number_id,d.color_name from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, lib_color d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.color_number_id=d.id  and b.job_no_mst='$txt_job_no'   and a.garments_nature=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by c.color_number_id,c.po_break_down_id  order by a.job_no";

                                echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,Color,Shipment Date", "90,120,100,100,90,90,90,80","1000","320",0, $sql , "js_set_value", "id,po_number,color_number_id,color_name", "", 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,company_name,buyer_name,style_ref_no,job_quantity,po_number,color_name,shipment_date", "",'','0,0,0,0,1,0,0,3');
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" >
                                <input type="button" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" />
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if ($action=="set_php_form_data")
{
	$data_array=sql_select("select id,po_break_down_id, color_number_id , sample_type_id, target_approval_date, send_to_factory_date, submitted_to_buyer, approval_status, approval_status_date, sample_comments,status_active from wo_po_sample_approval_info where id='$data'");
	foreach ($data_array as $row)
	{
		//echo "load_drop_down( '../woven_order/requires/sample_approval_controller', this.value, 'load_drop_down_color', 'color_td' )"
		echo "load_drop_down( '../woven_order/requires/sample_approval_controller', '".$row[csf("po_break_down_id")]."', 'load_drop_down_color', 'color_td' ) ;\n";
		echo "document.getElementById('cbo_po_no').value = '".$row[csf("po_break_down_id")]."';\n";
		echo "document.getElementById('cbo_color').value = '".$row[csf("color_number_id")]."';\n";
		echo "document.getElementById('cbo_sample_type').value = '".$row[csf("sample_type_id")]."';\n";
		echo "document.getElementById('txt_target_approval_date').value = '".change_date_format($row[csf("target_approval_date")])."';\n";
		echo "document.getElementById('txt_send_to_fatory_date').value = '".change_date_format($row[csf("send_to_factory_date")])."';\n";
		echo "document.getElementById('txt_submission_to_buyer_date').value = '".change_date_format($row[csf("submitted_to_buyer")])."';\n";
		echo "document.getElementById('cbo_approval_status').value = '".$row[csf("approval_status")]."';\n";
		echo "document.getElementById('txt_approval_reject_date').value = '".change_date_format($row[csf("approval_status_date")])."';\n";
		echo "document.getElementById('txt_sample_comments').value = '".$row[csf("sample_comments")]."';\n";
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_approval',1);\n";
	}
	exit();
}
if($action=="comments_popup")
{
	echo load_html_head_contents("Comments Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

</head>

<body>
<div style="width:430px;" align="center">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:400px; margin-top:10px;">
             <table cellspacing="0" cellpadding="0" border="1" rules="all" width="400" class="rpt_table" >
                <tr>
               		<td><textarea name="txt_comments" id="txt_comments" class="text_area" style="width:385px; height:120px;"><? echo $comments_data; ?></textarea></td>
                </tr>
            </table>
            <table width="400" id="tbl_close">
                 <tr>
                    <td align="center" >
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}
?>