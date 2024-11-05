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
	$importer_name=str_replace("'","",$cbo_importer_name);
	$cbo_lc_type_id=str_replace("'","",$cbo_lc_type_id);
	$txt_system_id=str_replace("'","",$txt_system_id);

	if($txt_system_id!="") $system_id_cond=" and a.con_prefix_number=$txt_system_id";
	
	if($cbo_lc_type_id!=0)
	{
		$lc_type_cond="and a.lc_type=$cbo_lc_type_id";
	}
	else
	{
		$lc_type_cond="";
	}
	
	$date_cond='';
	if(str_replace("'","",$txt_date)!="")
	{
		if(str_replace("'","",$cbo_get_upto)==1) $date_cond=" and a.office_note_date>$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==2) $date_cond=" and a.office_note_date<=$txt_date";
		else if(str_replace("'","",$cbo_get_upto)==3) $date_cond=" and a.office_note_date=$txt_date";
		else $date_cond='';
	}

	$approval_type=str_replace("'","",$cbo_approval_type);
	$approval_type=str_replace("'","",$cbo_approval_type);
	if($previous_approved==1 && $approval_type==1)
	{
		$previous_approved_type=1;
	}
	$txt_alter_user_id=str_replace("'","",$txt_alter_user_id);
	if($txt_alter_user_id!="")
	{		
		$user_id=$txt_alter_user_id;	
	}


	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$importer_name and page_id=$menu_id and user_id=$user_id and is_deleted = 0");

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","company_id=$importer_name and page_id=$menu_id and is_deleted = 0");
	
	if($user_sequence_no=="")
	{
		echo "<font style='color:#F00; font-size:14px; font-weight:bold'>You Have No Authority.</font>";die;
	}

	if($previous_approved==1 && $approval_type==1)	//approval process with prevous approve start
	{
		$sequence_no_cond=" and b.sequence_no<'$user_sequence_no'";
	
		$sql="SELECT a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, $select_year(a.insert_date $year_format) as year,  a.lc_type, a.office_note_date, c.id as approval_id, a.is_approved, a.total_amount 
		from commercial_office_note_mst a, approval_history c 
		where a.importer_id=$importer_name and a.is_deleted=0 and a.status_active=1 and a.is_approved=0 and a.ready_to_approved=1 $lc_type_cond $date_cond $system_id_cond 
		group by a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, a.insert_date, a.lc_type, a.office_note_date, c.id, a.is_approved, a.total_amount 
		order by a.id";
		//echo "$sql";
	}
	else if($approval_type==0) 
	{
		if($user_sequence_no==$min_sequence_no) // First user
		{
		 	$sql ="SELECT a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, $select_year(a.insert_date $year_format) as year,  a.lc_type, a.office_note_date, '0' AS approval_id, a.is_approved, a.total_amount 
		 	from commercial_office_note_mst a where a.importer_id=$importer_name and a.is_deleted=0 and a.status_active=1 and a.is_approved=0 and a.ready_to_approved=1 $lc_type_cond $date_cond $system_id_cond 
		 	group by a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, a.insert_date, a.lc_type, a.office_note_date, a.is_approved, a.total_amount  
		 	order by a.id"; 
		 	//echo $sql;die;
		}
		else // Next user
		{
			$sequence_no=return_field_value("max(sequence_no)","electronic_approval_setup","company_id=$importer_name and page_id=$menu_id and sequence_no<$user_sequence_no and bypass=2 and is_deleted = 0");
			if($sequence_no=="") // bypass if previous user Yes
			{
				if($db_type==0)
				{
					
					$seqSql="select group_concat(sequence_no) as sequence_no_by  from electronic_approval_setup where company_id=$cbo_importer_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
					
					$offie_note_id=return_field_value("group_concat(distinct(b.mst_id)) as offie_note_id","commercial_office_note_mst a, approval_history b","a.id=b.mst_id  and a.importer_id=$importer_name and b.sequence_no in ($sequence_no_by) and a.ready_to_approved=1 and b.entry_form=35 and b.current_approval_status=1 and a.is_approved in (3,1)","offie_note_id");
					$offie_note_id=implode(",",array_unique(explode(",",$offie_note_id)));
					
					$office_note_id_app_byuser=return_field_value("group_concat(distinct(b.mst_id)) as offie_note_id","commercial_office_note_mst a, approval_history b","a.id=b.mst_id and a.importer_id=$importer_name and a.ready_to_approved=1  and b.sequence_no=$user_sequence_no and a.ready_to_approved=1 and b.entry_form=35 and b.current_approval_status=1 and a.is_approved in (3,1)","offie_note_id");
				}
				else
				{
					//$sequence_no_by=return_field_value("LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no","electronic_approval_setup","company_id=$cbo_importer_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0","sequence_no");
					$seqSql="select LISTAGG(sequence_no, ',') WITHIN GROUP (ORDER BY sequence_no) as sequence_no_by from electronic_approval_setup where company_id=$importer_name and page_id=$menu_id and sequence_no<$user_sequence_no and is_deleted=0";
					$seqData=sql_select($seqSql);
					
					$sequence_no_by=$seqData[0][csf('sequence_no_by')];
	
					$office_note_id=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as office_note_id","commercial_office_note_mst a, approval_history b","a.id=b.mst_id  and a.importer_id=$importer_name and b.sequence_no in ($sequence_no_by) and a.ready_to_approved=1 and b.entry_form=39 and b.current_approval_status=1 and a.is_approved in (3,1)","office_note_id");

					$office_note_id=implode(",",array_unique(explode(",",$office_note_id)));
					
					$office_note_id_app_byuser=return_field_value("LISTAGG(b.mst_id, ',') WITHIN GROUP (ORDER BY b.mst_id) as office_note_id","commercial_office_note_mst a, approval_history b","a.id=b.mst_id and a.importer_id=$importer_name and a.ready_to_approved=1  and b.sequence_no=$user_sequence_no  and b.entry_form=39 and b.current_approval_status=1 and a.is_approved in (3,1)","office_note_id");
					$office_note_id_app_byuser=implode(",",array_unique(explode(",",$office_note_id_app_byuser)));
				}
				$result=array_diff(explode(',',$office_note_id),explode(',',$office_note_id_app_byuser));
				$office_note_id=implode(",",$result);

				if($office_note_id!="")
				{					
					$sql=" SELECT x.* from  (SELECT a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, $select_year(a.insert_date $year_format) as year,  a.lc_type, a.office_note_date, '0' AS approval_id, a.is_approved, a.total_amount 
					from commercial_office_note_mst a
					where a.importer_id=$importer_name and a.is_deleted=0 and a.status_active=1 and a.is_approved in(0,3) and a.id in ($office_note_id) $lc_type_cond $date_cond $system_id_cond   
					GROUP by a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, a.insert_date, a.lc_type, a.office_note_date, a.is_approved, a.total_amount  

					UNION ALL

					SELECT a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, $select_year(a.insert_date $year_format) as year,  a.lc_type, a.office_note_date, '0' AS approval_id, a.is_approved, a.total_amount 
					from commercial_office_note_mst a
					where a.id not in ($office_note_id) and a.is_approved=$approval_type and a.importer_id=$importer_name  and a.is_deleted=0 and a.status_active=1 $lc_type_cond $date_cond $system_id_cond 
					GROUP by group by a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, a.insert_date, a.lc_type, a.office_note_date, a.is_approved, a.total_amount  ) x  order by x.id";
					//echo $sql;
				}
				else
				{ 
					$sql=" SELECT a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, $select_year(a.insert_date $year_format) as year,  a.lc_type, a.office_note_date, '0' AS approval_id, a.is_approved, a.total_amount
					from commercial_office_note_mst a 
					where a.is_approved=$approval_type and a.importer_id=$importer_name and a.is_deleted=0 and a.status_active=1 $lc_type_cond $date_cond $system_id_cond   
					GROUP by a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, a.insert_date, a.lc_type, a.office_note_date, a.is_approved, a.total_amount 
					order by a.id";
					//echo $sql;
				}
				//echo $sql;
			}			
			else // if previous user bypass No 
			{
				$user_sequence_no=$user_sequence_no-1;
				//echo $sequence_no;
				if($sequence_no==$user_sequence_no) 
				{
					$sequence_no_by_pass=$sequence_no;
					$sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}
				else
				{
					if($db_type==0) 
					{
						$sequence_no_by_pass=return_field_value("group_concat(sequence_no) as sequence_no","electronic_approval_setup","company_id=$importer_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					else if($db_type==2) 
					{
						$sequence_no_by_pass=return_field_value("listagg(sequence_no,',') within group (order by sequence_no) as sequence_no","electronic_approval_setup","company_id=$importer_name and page_id=$menu_id and sequence_no between $sequence_no and $user_sequence_no and bypass=1","sequence_no");
					}
					
					if($sequence_no_by_pass=="") $sequence_no_cond=" and c.sequence_no='$sequence_no'";
					else $sequence_no_cond=" and c.sequence_no in ($sequence_no_by_pass)";
				}

				$sql=" SELECT a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, $select_year(a.insert_date $year_format) as year,  a.lc_type, a.office_note_date, '0' AS approval_id, a.is_approved, c.id as approval_id, a.total_amount
				from commercial_office_note_mst a, approval_history c 
				where a.is_approved in (1,3) and a.id=c.mst_id and c.entry_form in(39) and a.importer_id=$importer_name and a.is_deleted=0 and a.status_active=1 and c.current_approval_status=1 $sequence_no_cond $lc_type_cond $date_cond $system_id_cond  
				GROUP by a.id, a.con_prefix_number, a.con_system_id, a.item_category_id, a.pi_number, a.importer_id, a.insert_date, a.lc_type, a.office_note_date, a.is_approved, c.id, a.total_amount
				order by a.id";
				//echo $sql;
			}
		}
	}
	else
	{
		$sequence_no_cond=" and c.sequence_no='$user_sequence_no'";
		$sql=" SELECT a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, $select_year(a.insert_date $year_format) as year,  a.lc_type, a.office_note_date, '0' AS approval_id, a.is_approved, c.id as approval_id, a.total_amount
		from commercial_office_note_mst a, approval_history c 
		where a.is_approved in (1,3) and a.id=c.mst_id and c.entry_form in(39) and a.importer_id=$importer_name and a.is_deleted=0 and a.status_active=1 and c.current_approval_status=1 $sequence_no_cond $lc_type_cond $date_cond $system_id_cond  
		GROUP by a.id, a.con_prefix_number, a.con_system_id, a.pi_number, a.item_category_id, a.importer_id, a.insert_date, a.lc_type, a.office_note_date, a.is_approved, c.id, a.total_amount
		order by a.id";

		//echo $sql;die;
	}
	//echo $sql;
	?>
    <form name="requisitionApproval_2" id="requisitionApproval_2">
        <fieldset style="width:750px; margin-top:10px">
        <legend>Commercial Office Note List View</legend>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" >
                <thead>
                	<th width="50"></th>
                    <th width="40">SL</th>
                    <th width="70">System ID</th>
                    <th width="60">File</th>
                    <th width="100">Office Note Date</th>
                    <th width="100">PI Number</th>
                    <th width="80">Year</th>
                    <th width="100">LC Type</th>
                    <th>Value</th>
                </thead>
            </table>
            <div style="width:740px; overflow-y:scroll; max-height:330px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">
                    <tbody>
                        <?						
                            $i=1;
                            $nameArray=sql_select( $sql );$report_title="Finish Fabric Transfer Requisition Entry";
                           
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
									$app_id=return_field_value("id","approval_history","mst_id ='".$row[csf('id')]."' order by id desc");
									$value=$row[csf('id')]."**".$app_id;
								}

								$file_name=return_field_value("image_location", "common_photo_library", "master_tble_id='".$row[csf('con_system_id')]."' and form_name='Commercial Office Note' and file_type=2");

								

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" align="center"> 
                                	<td width="50" align="center" valign="middle">
                                        <input type="checkbox" id="tbl_<? echo $i;?>" />
                                        <input id="officeNote_id_<? echo $i;?>" name="officeNote_id[]" type="hidden" value="<? echo $value; ?>" />
                                        <input id="officeNote_no_<? echo $i;?>" name="officeNote_no[]" type="hidden" value="<? echo $row[csf('id')]; ?>" />
                                        <input id="approval_id_<? echo $i;?>" name="approval_id[]" type="hidden" value="<? echo $approvar_id; ?>" />
                                        <input id="<? echo strtoupper($row[csf('con_prefix_number')]); ?>" name="no_issue[]" type="hidden" value="<? echo $i;?>" />
                                    </td>   
									<td width="40" align="center"><? echo $i; ?></td>
									<td width="70">
										<!-- <a href='##' style='color:#000'onclick="generate_yarn_report('<? //echo $row[csf('company_id')]; ?>','<? //echo $row[csf('id')]; ?>','<? //echo $report_title; ?>','<? //echo $row[csf('is_approved')]; ?>','finish_fabric_transfer_print')">
                                        <? //echo $row[csf('con_prefix_number')];
                                        	// for Report Setting follow: 
                                        	// inventory\requires\purchase_requisition_controller.php 
                                        	// approval\requires\purchase_requisition_approval_controller.php
										?>
										</a> -->
                                    	<p><? echo $row[csf('con_prefix_number')]; ?></p>
                                    	<? 
                                    	if ($row[csf('item_category_id')]==1 || $row[csf('item_category_id')]== 2 || $row[csf('item_category_id')]== 3 || $row[csf('item_category_id')]== 4) 
                                    	{ 
                                    		?>
                                        	<a href='##' onclick="print_report('<? echo $row[csf('importer_id')].'**'.$row[csf('id')].'**'.$row[csf('is_approved')].'**'.'CommercialOfficeNoteApproval'; ?>','print','../commercial/import_details/requires/commercial_office_note_controller')">Print</a>
                                        	<?
                                        }
                                        else
                                        {	
                                        	?>
											<a href='##' onclick="print_report('<? echo $row[csf('importer_id')].'**'.$row[csf('id')].'**'.$row[csf('is_approved')].'**'.'CommercialOfficeNoteApproval'; ?>','print2','../commercial/import_details/requires/commercial_office_note_controller')">Print 2</a>
											<? 
										} 
										?>
                                    </td>
                                    <td width="60" align="center"><a href="##" onClick="openImgFile('<? echo $row[csf('con_system_id')];?>','img');"><? if ($file_name != '' ) echo 'File'; else ''; ?></a></td>                                
                                    <td width="100" align="center"><? if($row[csf('office_note_date')]!="0000-00-00") echo change_date_format($row[csf('office_note_date')]); ?>&nbsp;</td>
                                    <td width="100" align="center"><p><? echo  $row[csf('pi_number')]; ?></p></td>
                                    <td width="80"><p><?  echo $row[csf('year')]; ?>&nbsp;</p></td>
									<td width="100" align="left"><p><? echo $lc_type[$row[csf('lc_type')]]; ?>&nbsp;</p></td>
                                    <td align="center"><? echo  $row[csf('total_amount')]; ?></td>
								</tr>
								<?
								$i++;
							}
                        ?>
                    </tbody>
                </table>
            </div>
            <table cellspacing="0" cellpadding="0" border="0" rules="all" width="740" class="rpt_table">
				<tfoot>
                    <td width="50" align="center" ><input type="checkbox" id="all_check" onclick="check_all('all_check')" /></td>
                    <td colspan="2" align="left"><input type="button" value="<? if($approval_type==1) echo "Un-Approved"; else echo "Approved"; ?>" class="formbutton" style="width:100px" onclick="submit_approved(<? echo $i; ?>,<? echo $approval_type; ?>)"/></td>
				</tfoot>
			</table>
        </fieldset>
    </form>         
	<?
	exit();	
}

if($action=="img")
{	
	echo load_html_head_contents("Image View", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<style type="text/css">
		li { list-style: none; font-size: 9pt; margin-top: 0px; margin-left: 7px; float: left; width: 89px;}
	</style>
    <table width="100%">
        <tr>
        	<td width="100%" height="250" style="vertical-align: top;">
	        <?
	        $sql="select image_location, real_file_name from common_photo_library where master_tble_id='$id' and form_name='Commercial Office Note' and file_type=2";
	        $result=sql_select($sql);
	        foreach ($result as $row)
	        {
	        	?>
	        	<li>
	        		<a href="../../<? echo $row[csf('image_location')]; ?>" target="_new">
	        		<img src="../../file_upload/blank_file.png" height="97" width="89"></a>
	        		<br>
	        		<p style="width: 89px; word-break: break-all; margin-top: 1px;"><? echo $row[csf('real_file_name')]; ?></p>
	        	</li>
	        	<?
	        }
	        ?>
        	</td>
        </tr>
    </table>	
	<?
	exit();
}


if ($action=="approve")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$user_id=23;
	//echo "10**".$operation;die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	if($txt_alter_user_id!="") 	$user_id_approval=$txt_alter_user_id;
	else						$user_id_approval=$user_id;
	$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","page_id=$menu_id and user_id=$user_id_approval and company_id = $cbo_importer_name and is_deleted = 0");
	// echo $user_sequence_no;die;

	$min_sequence_no=return_field_value("min(sequence_no)","electronic_approval_setup","page_id=$menu_id and is_deleted = 0");

	$msg=''; $flag=''; $response='';	
	if($approval_type==0)
	{
		$response=$officeNote_ids;
		// echo $officeNote_ids;die;

		//echo "SELECT sequence_no from electronic_approval_setup where company_id=$cbo_importer_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0";die;

		$is_not_last_user=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_importer_name and page_id=$menu_id and sequence_no>$user_sequence_no and bypass=2 and is_deleted=0");
        // echo $is_not_last_user;die;

        if($is_not_last_user!="") $partial_approval=3; else $partial_approval=1;
        // echo $partial_approval;die;

        $max_approved_no_arr = return_library_array("SELECT mst_id, max(approved_no) as approved_no from approval_history where mst_id in($officeNote_ids) and entry_form=39 group by mst_id","mst_id","approved_no");
		
		$approved_status_arr = return_library_array("SELECT id, is_approved from commercial_office_note_mst where id in($officeNote_ids)","id","is_approved");

		$field_array="id, entry_form, mst_id, approved_no, sequence_no, current_approval_status, approved_by, approved_date, inserted_by, insert_date"; 
		$id=return_next_id( "id","approval_history", 1 ) ;

		$officeNote_ids_all=explode(",",$officeNote_ids);
		$officeNote_nos_all=explode(",",$officeNote_nos);
		
		// ======================================================================== New
		for($i=0;$i<count($officeNote_nos_all);$i++)
		{
			$val=$officeNote_nos_all[$i];
			$officeNote_id=$officeNote_ids_all[$i];

			$approved_no=$max_approved_no_arr[$officeNote_id];
			$approved_status=$approved_status_arr[$officeNote_id];
			
			if($approved_status==0)
			{
				$approved_no=$approved_no+1;
				$approved_no_array[$val]=$approved_no;
				if($book_nos=="") $book_nos=$val; else $book_nos.=",".$val;
			}
			
			if($data_array!="") $data_array.=",";
			
			$data_array.="(".$id.",39,".$officeNote_id.",".$approved_no.",'".$user_sequence_no."',1,".$user_id_approval.",'".$pc_date_time."',".$user_id.",'".$pc_date_time."')"; 
				
			$id=$id+1;
			
		}

		//$data = $partial_approval."*".$user_id."*'".$pc_date_time."'";
    	$rID=sql_multirow_update("commercial_office_note_mst","is_approved",$partial_approval,"id",$officeNote_ids,1);
    	if($rID) $flag=1; else $flag=0;

    	if($approval_ids!="")
		{
			$rIDapp=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
			if($flag==1) 
			{
				if($rIDapp) $flag=1; else $flag=0; 
			} 
		}
		$rID2=sql_insert("approval_history",$field_array,$data_array,1);
		//echo $rID2;return;
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		if($flag==1) $msg='19'; else $msg='21';

		//echo "10**"."insert into approval_history($field_array) values".$data_array;die;
		//echo "10**".$rID."=".$rID2."=".$rIDapp;die;
	}
	else
	{
		// echo($officeNote_ids);die;
		$officeNote_ids_all=explode(",",$officeNote_ids);
		$officeNote_ids=''; $app_ids='';

		foreach($officeNote_ids_all as $value)
		{
			$data = explode('**',$value);
			$officeNote_id=$data[0];
			$app_id=$data[1];
			if($officeNote_ids=='') $officeNote_ids=$officeNote_id; else $officeNote_ids.=",".$officeNote_id;
			if($app_ids=='') $app_ids=$app_id; else $app_ids.=",".$app_id;
		}

		$rID=sql_multirow_update("commercial_office_note_mst","is_approved*ready_to_approved","0*0","id",$officeNote_ids,1);
		if($rID) $flag=1; else $flag=0;
		$rID2=sql_multirow_update("approval_history","current_approval_status",0,"id",$approval_ids,1);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		// echo $app_ids.'=Tipu';
		$data=$user_id_approval."*'".$pc_date_time."'*".$user_id."*'".$pc_date_time."'";
		$rID3=sql_multirow_update("approval_history","un_approved_by*un_approved_date*updated_by*update_date",$data,"id",$app_ids,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}

		// echo "10**".$rID."=".$rID2."=".$rID3;die;

		$response=$officeNote_ids;
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


if($action=='user_popup')
{
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
			 	//$user_sequence_no=return_field_value("sequence_no","electronic_approval_setup","company_id=$cbo_importer_name and page_id=$menu_id and user_id=$user_id and is_deleted=0");
				$sql="select a.id, a.user_name, a.department_id, a.user_full_name, a.designation from  user_passwd a,electronic_approval_setup b where a.id=b.user_id and b.company_id=$cbo_importer_name and b.page_id=$menu_id and valid=1 and a.id!=$user_id  and b.is_deleted=0  and b.page_id=$menu_id order by sequence_no";
			 $arr=array (2=>$custom_designation,3=>$Department);
			 echo  create_list_view ( "list_view", "User ID,Full Name,Designation,Department", "100,120,150,150,","630","220",0, $sql, "js_set_value", "id,user_name", "", 1, "0,department_id,designation,department_id", $arr , "user_name,user_full_name,designation,department_id", "requires/user_creation_controller", 'setFilterGrid("list_view",-1);' ) ;
			?>
	        
	</form>
	<script language="javascript" type="text/javascript">
	  setFilterGrid("tbl_style_ref");
	</script>


	<?
}

?>