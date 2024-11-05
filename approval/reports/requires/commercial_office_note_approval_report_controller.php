<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_lc_type_id = str_replace("'","",$cbo_lc_type_id);
	$cbo_date_by = str_replace("'","",$cbo_date_by);
	$txt_date_from = str_replace("'","",$txt_date_from);
	$txt_date_to = str_replace("'","",$txt_date_to);
	$txt_office_note_no = trim(str_replace("'","",$txt_office_note_no));
	$txt_pi_no = trim(str_replace("'","",$txt_pi_no));
	$cbo_type = str_replace("'","",$cbo_type);	

 	$date_cond="";
 	if ($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$date_form = date("Y-m-d", strtotime($txt_date_from));
			$date_form = date("Y-m-d", strtotime($txt_date_to));
			if ($cbo_date_by==1) $date_cond=" and a.office_note_date between '".$date_form."' and '".$date_to."'";
			else if ($cbo_date_by==2) $date_cond=" and a.insert_date between '".$date_form."' and '".$date_to." 11:59:59'";
			else $date_cond=" and c.approved_date between '".$date_form."' and '".$date_to." 11:59:59'";
		}
		else
		{			
			$date_form = date("d-M-Y", strtotime($txt_date_from));
			$date_to = date("d-M-Y", strtotime($txt_date_to));
			if ($cbo_date_by==1) $date_cond=" and a.office_note_date between '".$date_form."' and '".$date_to."'";
			else if ($cbo_date_by==2) $date_cond=" and a.insert_date between '".$date_form."' and '".$date_to." 11:59:59 PM'";
			else $date_cond=" and c.approved_date between '".$date_form."' and '".$date_to." 11:59:59 PM'";		
		}		
	}

	$company_cond=$lc_type_cond=$office_note_no_cond=$pi_no_cond='';
	if ($cbo_company_name!='') $company_cond=" and a.importer_id in($cbo_company_name)";
	if ($cbo_lc_type_id>0) $lc_type_cond=" and a.lc_type=$cbo_lc_type_id";
	if ($txt_office_note_no!='') $office_note_no_cond=" and a.con_prefix_number=$txt_office_note_no";
	if ($txt_pi_no!='') $pi_no_cond=" and a.pi_number like '%$txt_pi_no%'";

	if ($cbo_type==2) //Full Approved
	{
		$sql="SELECT a.id as ID, a.importer_id as COMPANY_ID, a.con_prefix_number as CON_PREFIX_NUMBER, a.con_system_id as CON_SYSTEM_ID, a.lc_type as LC_TYPE, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.item_category_id as ITEM_CATEGORY_ID, a.office_note_date as OFFICE_NOTE_DATE, a.insert_date as INSERT_DATE, a.update_date as UPDATE_DATE, b.pi_dtls_id as PI_DTLS_ID, b.quantity as PI_QTY, b.amount as PI_VALUE, c.approved_date as APPROVED_DATE from commercial_office_note_mst a, commercial_office_note_dtls b, approval_history c where a.id=b.mst_id and a.id=c.mst_id and a.is_approved=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.entry_form=39 $company_cond $lc_type_cond $office_note_no_cond $pi_no_cond $date_cond";
	}
	else //Pending
	{
		$sql="SELECT a.id as ID, a.importer_id as COMPANY_ID, a.con_prefix_number as CON_PREFIX_NUMBER, a.con_system_id as CON_SYSTEM_ID, a.lc_type as LC_TYPE, a.pi_number as PI_NUMBER, a.supplier_id as SUPPLIER_ID, a.item_category_id as ITEM_CATEGORY_ID, a.office_note_date as OFFICE_NOTE_DATE, a.insert_date as INSERT_DATE, a.update_date as UPDATE_DATE, b.pi_dtls_id as PI_DTLS_ID, b.quantity as PI_QTY, b.amount as PI_VALUE from commercial_office_note_mst a, commercial_office_note_dtls b where a.id=b.mst_id and a.ready_to_approved=1 and a.is_approved in(0,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $lc_type_cond $office_note_no_cond $pi_no_cond $date_cond";
	}
	//echo $sql;

	$sql_res=sql_select($sql);
	foreach ($sql_res as $row) 
	{
		if ($check_office_note_id[$row['ID']]==''){
			$office_note_id.=$row['ID'].',';
			$check_office_note_id[$row['ID']]=$row['ID'];
			$main_data_arr[$row['ID']]['COMPANY_ID']=$row['COMPANY_ID'];
			$main_data_arr[$row['ID']]['CON_PREFIX_NUMBER']=$row['CON_PREFIX_NUMBER'];
			$main_data_arr[$row['ID']]['CON_SYSTEM_ID']=$row['CON_SYSTEM_ID'];
			$main_data_arr[$row['ID']]['LC_TYPE']=$row['LC_TYPE'];
			$main_data_arr[$row['ID']]['PI_NUMBER']=$row['PI_NUMBER'];
			$main_data_arr[$row['ID']]['SUPPLIER_ID']=$row['SUPPLIER_ID'];
			$main_data_arr[$row['ID']]['ITEM_CATEGORY_ID']=$row['ITEM_CATEGORY_ID'];
			$main_data_arr[$row['ID']]['OFFICE_NOTE_DATE']=$row['OFFICE_NOTE_DATE'];
			$main_data_arr[$row['ID']]['INSERT_DATE']=$row['INSERT_DATE'];
			$main_data_arr[$row['ID']]['UPDATE_DATE']=$row['UPDATE_DATE'];
		}
		if ($check_pi_dtls_id[$row['ID']][$row['PI_DTLS_ID']]==''){
			$check_pi_dtls_id[$row['ID']][$row['PI_DTLS_ID']]=$row['PI_DTLS_ID'];
			$main_data_arr[$row['ID']]['PI_QTY']+=$row['PI_QTY'];
			$main_data_arr[$row['ID']]['PI_VALUE']+=$row['PI_VALUE'];
		}		
		
	}
	//echo '<pre>';print_r($main_data_arr);die;
	$office_note_ids=rtrim($office_note_id,',');

	$signatory_data = sql_select("SELECT company_id as COMPANY_ID, user_id as USER_ID, sequence_no as SEQUENCE_NO, bypass as BYPASS from electronic_approval_setup where company_id in($cbo_company_name) and is_deleted=0 and entry_form=39 order by sequence_no");
	$signatory_data_arr=array();
	foreach ($signatory_data as $row) {
		$signatory_data_arr[$row['COMPANY_ID']][$row['USER_ID']]['USER_ID']=$row['USER_ID'];
		$signatory_data_arr[$row['COMPANY_ID']][$row['USER_ID']]['SEQUENCE_NO']=$row['SEQUENCE_NO'];
		$signatory_data_arr[$row['COMPANY_ID']][$row['USER_ID']]['BYPASS']=$row['BYPASS'];
		$rowspan_arr[$row['COMPANY_ID']]++;
	}
	//echo '<pre>';print_r($signatory_data_arr);die;
	$company_arr=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$supplier_arr = return_library_array("SELECT id, supplier_name from lib_supplier","id","supplier_name");
	$designation_arr = return_library_array( "SELECT id, custom_designation from lib_designation", "id", "custom_designation" );
	
	$user_name_array = array();
	$userData = sql_select( "SELECT id as ID, user_name as USER_NAME, user_full_name as USER_FULL_NAME, designation as DESIGNATION from user_passwd where valid=1");
	foreach($userData as $user_row)
	{
		$user_name_array[$user_row['ID']]['NAME']=$user_row['USER_NAME'];
		$user_name_array[$user_row['ID']]['FULL_NAME']=$user_row['USER_FULL_NAME'];
		$user_name_array[$user_row['ID']]['DESIGNATION']=$designation_arr[$user_row['DESIGNATION']];	
	}

	$sql_approved="SELECT mst_id as MST_ID, max(approved_no) as APPROVED_NO, approved_by as APPROVED_BY, max(approved_date) as APPROVED_DATE from approval_history where entry_form=39 and un_approved_by=0 and mst_id in($office_note_ids) group by mst_id, approved_by";
	$sql_approved_res=sql_select($sql_approved);
	foreach ($sql_approved_res as $row) {
		$approval_arr[$row['MST_ID']][$row['APPROVED_BY']]['APPROVED_DATE']=$row['APPROVED_DATE'];
	}
	
	//echo '<pre>';print_r($user_name_array);
	ob_start();
	?>
    <fieldset style="width:1520px;">
    	<table cellpadding="0" cellspacing="0" width="100%">
            <tr>
               <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
            </tr>
        </table>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="60">Company</th>
                <th width="110">Office Note No</th>
                <th width="80">LC Type</th>
                <th width="150">PI Number</th>
                <th width="130">Supplier Name</th>
                <th width="110">Item Category</th>
                <th width="70">PI Qty</th>
                <th width="70">PI Value</th>
                <th width="70">Office Note Date</th>
                <th width="125">Office Note Insert Date</th>
                <th width="125">Last Submit Date</th>
                <th width="80">Signatory</th>
                <th width="100">Designation</th>
                <th width="50">Can Bypass</th>
                <th >Approval Date</th>
            </thead>
        </table>
		<div style="width:1520px; overflow-y:scroll; max-height:310px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1500" class="rpt_table" id="tbl_list_search23">
                <tbody>
                    <?
					$i=1;
                    foreach ($main_data_arr as $office_id => $row)
                    {
                    	if($i%2==0) $bgcolor="#E9F3FF"; 
                    	else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $i; ?></p></td>
							<td width="60" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $company_arr[$row['COMPANY_ID']]; ?></p></td>
							<td width="110" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $row['CON_SYSTEM_ID']; ?></p></td>
							<td width="80" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $lc_type[$row['LC_TYPE']]; ?></p></td>
							<td width="150" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $row['PI_NUMBER']; ?></p></td>
							<td width="130" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
							<td width="110" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $item_category[$row['ITEM_CATEGORY_ID']]; ?></p></td>
							<td width="70" align="right" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $row['PI_QTY']; ?></p></td>
							<td width="70" align="right" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $row['PI_VALUE']; ?></p></td>
							<td width="70" align="center" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo change_date_format($row['OFFICE_NOTE_DATE']); ?></p></td>
							<td width="125" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $row['INSERT_DATE']; ?></p></td>
							<td width="125" rowspan="<?= $rowspan_arr[$row['COMPANY_ID']]; ?>"><p><? echo $row['UPDATE_DATE']; ?></p></td>
							<?
							if (!empty($signatory_data_arr))
							{
								foreach ($signatory_data_arr as $company_id => $user_data) 
								{
									$m=1;
									if ($company_id==$row['COMPANY_ID'])
									{						
										foreach ($user_data as $userid => $val) 
										{
											if ($m!=1)
											{
												?>
												<tr bgcolor="<? echo $bgcolor; ?>">
												<?
											}
											?>
											<td width="80"><? echo $user_name_array[$userid]['NAME']; ?></td>
											<td width="100"><? echo $user_name_array[$userid]['DESIGNATION']; ?></td>
											<td width="50"><? echo $yes_no[$val['BYPASS']]; ?></td>
											<td width=""><? echo $approval_arr[$office_id][$userid]['APPROVED_DATE']; ?></td>
											</tr>
											<?
											$m++;
										}
									}
									else
									{
										?>
										<td width="80">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="50">&nbsp;</td>
										<td width="">&nbsp;</td>
										</tr>
										<?
									}							
								}
							}
							else
							{
								?>								
								<td width="80">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="50">&nbsp;</td>
								<td width="">&nbsp;</td>
								</tr>
								<?
							}													
						$i++;
					}
					?>	
                </tbody>
            </table>
		</div>
  	</fieldset>
	<?	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 	
}
?>