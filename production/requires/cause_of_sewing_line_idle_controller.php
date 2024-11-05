<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=='load_drop_down_location') {
	echo create_drop_down( 'cbo_location_id', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", 'id,location_name', 1, '-- Select Location --', $selected, "load_drop_down( 'requires/cause_of_sewing_line_idle_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_floor', 'floor_td' );" );
	exit();
}

if ($action=='load_drop_down_floor') {
	$data=explode('_',$data);
	$loca=$data[0];
	$com=$data[1];
	echo create_drop_down( 'cbo_floor_id', 100, "select id,floor_name from lib_prod_floor where production_process=5 and status_active =1 and is_deleted=0 and company_id='$com' and location_id='$loca' order by floor_name", 'id,floor_name', 1, '-- Select Floor --', $selected,  '', '', '', '', '', '', 4 );     	 
	exit();
}

if ($action=='show_line_list') {
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_floor_id = str_replace("'", '', $cbo_floor_id);
	$date_from_chk = str_replace("'", '', $txt_date_from);

	$is_update_mode = false;

	if($cbo_floor_id != 0) 
	{
		$floor_cond = "and a.floor_id=$cbo_floor_id";
		$floor_cond2 = "and c.floor_id=$cbo_floor_id"; 
	}

	/*	$sql_line = "select distinct a.id as prod_id, c.id as idle_mst_id, d.id as idle_dtls_id, a.line_number, a.location_id, a.floor_id, b.pr_date, d.category_id, d.cause_id, d.duration_hour, d.end_hour, d.end_minute, d.manpower, d.start_hour, d.start_minute, c.remarks
			from ( prod_resource_mst a
			join prod_resource_dtls b
			on a.id = b.mst_id )
			left join (sewing_line_idle_mst c left join sewing_line_idle_dtls d
			on c.id = d.mst_id and c.is_deleted = 0 and d.is_deleted = 0 and c.idle_date = ".$txt_date_from.")
			on a.id = c.prod_resource_id
			where a.company_id=$cbo_company_id and a.location_id=$cbo_location_id $floor_cond and b.pr_date = ".$txt_date_from." and a.is_deleted = 0 and b.is_deleted = 0";*/

	
	
	
	 $sql_line_idle = "SELECT c.prod_resource_id, c.id as idle_mst_id, d.id as idle_dtls_id, c.LINE_IDS as line_number,c.string_data, c.location_id, c.floor_id, c.idle_date, d.category_id, d.cause_id, d.duration_hour, d.end_hour, d.end_minute, d.manpower, d.start_hour, d.start_minute, c.remarks
  		from sewing_line_idle_mst c,sewing_line_idle_dtls d,prod_resource_mst b
          where c.id = d.mst_id and c.prod_resource_id=b.id and c.is_deleted = 0 and b.is_deleted = 0 and c.status_active = 1 and d.status_active = 1 and d.status_active = 1  and c.idle_date = ".$txt_date_from."
 
 		and c.company_id=$cbo_company_id and c.location_id=$cbo_location_id  $floor_cond2 and c.is_deleted = 0 and c.is_deleted = 0";
		// echo $sql_line_idle;
		
	$sql_line_idle_result = sql_select($sql_line_idle);
	$line_arr = array();
	$npt_value_data='';
	foreach ($sql_line_idle_result as $row) 
	{
		$cat_id = $row[csf('category_id')];
		$cause_id = $row[csf('cause_id')];
		$start_hour = $row[csf('start_hour')];
		$start_minute = $row[csf('start_minute')];
		$end_hour = $row[csf('end_hour')];
		$end_minute = $row[csf('end_minute')];
		$manpower = $row[csf('manpower')];
		$duration = $row[csf('duration_hour')];
		$prod_resource_id = $row[csf('prod_resource_id')];
		$idle_date_chk = strtotime($row[csf('idle_date')]);
		//$txt_date_from_chk = strtotime($txt_date_from_ckl);
	 $idle_mnt='';
		$idle_mnt = $duration*$manpower*60;
	// echo $idle_mnt.'='.$date_from_chk.'<br>';
		if($idle_mnt) {
			$is_update_mode = true;
		}

		if($cat_id) {
			if($npt_value_data=='')
			{
			$npt_value_data = $cat_id . '_' . $cause_id . '_' . $start_hour . '_' . $start_minute . '_' . $end_hour . '_' . $end_minute . '_' . $manpower . '_' . $duration . '_' . $idle_mnt . '_' . $row[csf('idle_dtls_id')];
		//	$line_arr2[$prod_resource_id][$row[csf('line_number')]]['npt_value'] = $npt_value_data;
			}
			else
			{
				$npt_value_data .="__".$cat_id . '_' . $cause_id . '_' . $start_hour . '_' . $start_minute . '_' . $end_hour . '_' . $end_minute . '_' . $manpower . '_' . $duration . '_' . $idle_mnt . '_' . $row[csf('idle_dtls_id')];
				
			}
			 // echo $npt_value_data.'<br>';
			 if($row[csf('string_data')]!='')
			 {
				$line_arr2[$prod_resource_id][$row[csf('line_number')]]['npt_value']= $row[csf('string_data')];
			 }
			 else
			 {
				 $line_arr2[$prod_resource_id][$row[csf('line_number')]]['npt_value']= $npt_value_data;
			 }
			
			//$line_arr3[$prod_resource_id][$row[csf('line_number')]]['idle_mnt'] = $idle_mnt;
			$line_arr2[$prod_resource_id][$row[csf('line_number')]]['idle_mnt'] += $idle_mnt;
		}

		//$line_arr2[$row[csf('line_number')]]['idle_date'] = $row[csf('idle_date')];
		$line_arr2[$row[csf('line_number')]]['idle_date'] = $row[csf('idle_date')];
		$line_arr2[$row[csf('line_number')]]['idle_mst_id'] = $row[csf('idle_mst_id')];
		$line_arr2[$row[csf('line_number')]]['idle_dtls_id'] = $row[csf('idle_dtls_id')];
		$line_arr2[$row[csf('line_number')]]['category_id'] = $cat_id;
		$line_arr2[$row[csf('line_number')]]['cause_id'] = $row[csf('cause_id')];
		$line_arr2[$row[csf('line_number')]]['duration_hour'] = $row[csf('duration_hour')];
		$line_arr2[$row[csf('line_number')]]['end_hour'] = $row[csf('end_hour')];
		$line_arr2[$row[csf('line_number')]]['end_minute'] = $row[csf('end_minute')];
		$line_arr2[$row[csf('line_number')]]['manpower'] = $row[csf('manpower')];
		$line_arr2[$row[csf('line_number')]]['start_hour'] = $row[csf('start_hour')];
		$line_arr2[$row[csf('line_number')]]['start_minute'] = $row[csf('start_minute')];
		$line_arr2[$row[csf('line_number')]]['remarks'] = $row[csf('remarks')];

	}
 	//	print_r($line_arr3);
	
	$sql_line = "SELECT a.id as prod_id , a.line_number, a.location_id, a.floor_id, b.pr_date,c.sewing_line_serial
  		from prod_resource_mst a,prod_resource_dtls b,lib_sewing_line c
		where a.id = b.mst_id  and a.company_id=$cbo_company_id and a.location_id=$cbo_location_id $floor_cond and b.pr_date = ".$txt_date_from." and a.is_deleted = 0 and b.is_deleted = 0 and REGEXP_SUBSTR( a.line_number, '[^,]+', 1)=c.id order by c.sewing_line_serial";
	// echo "$sql_line";

 	

	$line_result = sql_select($sql_line);
	if( count($line_result) == 0) 
	{
		echo '<h2 style="text-align:center">No Sewing Line Found!</h2>';
		exit();
	}
	
	foreach ($line_result as $row) 
	{
		/*		
		$cat_id = $row[csf('category_id')];
		$cause_id = $row[csf('cause_id')];
		$start_hour = $row[csf('start_hour')];
		$start_minute = $row[csf('start_minute')];
		$end_hour = $row[csf('end_hour')];
		$end_minute = $row[csf('end_minute')];
		$manpower = $row[csf('manpower')];
		$duration = $row[csf('duration_hour')];
		$idle_mnt = $duration*$manpower*60;

		if($idle_mnt) {
			$is_update_mode = true;
		}

		if($cat_id) {
			$npt_value = $cat_id . '@!@' . $cause_id . '@!@' . $start_hour . '@!@' . $start_minute . '@!@' . $end_hour . '@!@' . $end_minute . '@!@' . $manpower . '@!@' . $duration . '@!@' . $idle_mnt . '@!@' . $row[csf('idle_dtls_id')] . '!!!!!';

			$line_arr[$row[csf('line_number')]]['npt_value'] .= $npt_value;
			$line_arr[$row[csf('line_number')]]['idle_mnt'] += $idle_mnt;
		}*/
		
	
		$idle_date= $line_arr2[$row[csf('line_number')]]['idle_date'];
			//$pr_date=$row[csf('pr_date')];
		$idle_date_chk = strtotime($idle_date);
		$pr_date = strtotime($row[csf('pr_date')]);
	
			// echo $idle_date_chk.'='.$date_from_chk.'<br>';
		//if($idle_date_chk==$pr_date)
		//{	
		//	echo $line_arr2[$row[csf('prod_id')]][$row[csf('line_number')]]['npt_value'].'<br>';
		$line_arr[$row[csf('line_number')]]['idle_mst_id'] = $line_arr2[$row[csf('line_number')]]['idle_mst_id'];
		$line_arr[$row[csf('line_number')]]['idle_dtls_id'] = $line_arr2[$row[csf('line_number')]]['idle_dtls_id'];
		$line_arr[$row[csf('line_number')]]['category_id'] = $line_arr2[$row[csf('line_number')]]['category_id'];
	
		$line_arr[$row[csf('line_number')]]['cause_id'] = $line_arr2[$row[csf('line_number')]]['cause_id'];
		$line_arr[$row[csf('line_number')]]['duration_hour'] =$line_arr2[$row[csf('line_number')]]['duration_hour'];
		$line_arr[$row[csf('line_number')]]['end_hour'] =$line_arr2[$row[csf('line_number')]]['end_hour'];
		
		$line_arr[$row[csf('line_number')]]['end_minute'] =$line_arr2[$row[csf('line_number')]]['end_minute'];
		
		$line_arr[$row[csf('line_number')]]['manpower'] = $line_arr2[$row[csf('line_number')]]['manpower'] ;
		
		$line_arr[$row[csf('line_number')]]['start_hour'] =$line_arr2[$row[csf('line_number')]]['start_hour'];
		
		$line_arr[$row[csf('line_number')]]['start_minute'] =$line_arr2[$row[csf('line_number')]]['start_minute'];
		$line_arr[$row[csf('line_number')]]['remarks'] = $line_arr2[$row[csf('line_number')]]['remarks'];
		$line_arr[$row[csf('line_number')]]['idle_mnt'] = $line_arr2[$row[csf('prod_id')]][$row[csf('line_number')]]['idle_mnt'];
		
		$line_arr[$row[csf('line_number')]]['prod_id'] = $row[csf('prod_id')];
		//$line_arr[$row[csf('line_number')]]['idle_mst_id'] = $row[csf('idle_mst_id')];
		//$line_arr[$row[csf('line_number')]]['idle_dtls_id'] = $row[csf('idle_dtls_id')];
		$line_arr[$row[csf('line_number')]]['line_number'] = $row[csf('line_number')];
		$line_arr[$row[csf('line_number')]]['location_id'] = $row[csf('location_id')];
		$line_arr[$row[csf('line_number')]]['floor_id'] = $row[csf('floor_id')];
		$line_arr[$row[csf('line_number')]]['pr_date'] = $row[csf('pr_date')];
		if($line_arr2[$row[csf('prod_id')]][$row[csf('line_number')]]['npt_value']!='')
		{
		$line_arr[$row[csf('line_number')]]['npt_value']=$line_arr2[$row[csf('prod_id')]][$row[csf('line_number')]]['npt_value'];
		}
		/*		$line_arr[$row[csf('line_number')]]['category_id'] = $cat_id;
		$line_arr[$row[csf('line_number')]]['cause_id'] = $row[csf('cause_id')];
		$line_arr[$row[csf('line_number')]]['duration_hour'] = $row[csf('duration_hour')];
		$line_arr[$row[csf('line_number')]]['end_hour'] = $row[csf('end_hour')];
		$line_arr[$row[csf('line_number')]]['end_minute'] = $row[csf('end_minute')];
		$line_arr[$row[csf('line_number')]]['manpower'] = $row[csf('manpower')];
		$line_arr[$row[csf('line_number')]]['start_hour'] = $row[csf('start_hour')];
		$line_arr[$row[csf('line_number')]]['start_minute'] = $row[csf('start_minute')];
		$line_arr[$row[csf('line_number')]]['remarks'] = $row[csf('remarks')];
		
	*/	//} 
	}
	unset($line_result);

	$location_library = return_library_array("select id, location_name from lib_location where status_active = 1", 'id', 'location_name');
	$line_library = return_library_array("select id, line_name from lib_sewing_line where status_active=1 and is_deleted=0", 'id', 'line_name');
	$floor_library = return_library_array("select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0", 'id', 'floor_name');
	$prod_reso_library = return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	ob_end_flush();
	ob_start();
	?>
	<style type="text/css">
	.rpt_table tbody tr td {
		padding-left: 5px;
	}
	</style>
	<div style="margin: 50px auto; width: 40%;">
    <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_npt_line">
        <thead>
            <tr>
                <th width="2%">SL</th>
                <th width="20%">Location</th>
                <th width="20%">Floor</th>
                <th width="20%">Line</th>
                <th width="10%">Cause/NPT Mnt</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
	<?php
		$sl = 1;
		foreach ($line_arr as $row) 
		{
			$line = '';
			$bgcolor = "#FFFFFF";
			if ($sl % 2 == 0) {
				$bgcolor = "#E9F3FF";
			}
		

			$line_id_arr = explode(',', $row['line_number']);

			foreach ($line_id_arr as $lineId) {
				$line .= $line_library[$lineId] . ', ';
			}

			$line = rtrim($line, ', ');
			//echo $row['npt_value'].'<br>';
			$npt_value = rtrim($row['npt_value'], '__');
		?>
			<tr bgcolor="<?php echo $bgcolor; ?>">
				<td><?php echo $sl; ?></td>
				<td><?php echo $location_library[$row['location_id']]; ?></td>
				<td><?php echo $floor_library[$row['floor_id']]; ?></td>
				<td><?php echo $line; ?></td>
				<td>
					<input type="text" name="txtCauseNptMnt_<?php echo $sl; ?>" id="txtCauseNptMnt_<?php echo $sl; ?>" class="text_boxes" style="width: 70px;" placeholder="Browse" onDblClick="nptCausePopup(<?php echo str_replace(",","_",$row['line_number']); ?>, <?php echo $sl; ?>);" value="<?php echo round($row['idle_mnt'], 2); ?>" readonly />
				</td>
				<td style="text-align: center;">
					<input type="button" name="remarks_<?php echo $sl; ?>" id="remarks_<?php echo $sl; ?>" class="formbuttonplasminus" value="Remarks" onClick="openmypage_remarks(<?php echo $sl; ?>);" />
					<input type="hidden" name="hdnCauseValue_<?php echo $sl; ?>" id="hdnCauseValue_<?php echo $sl; ?>" value="<?php echo $npt_value; ?>" />
					<input type="hidden" name="remarksvalue_<?php echo $sl; ?>" id="remarksvalue_<?php echo $sl; ?>" value="<?php echo $row['remarks']; ?>" />
					<input type="hidden" name="hdnFloorId_<?php echo $sl; ?>" id="hdnFloorId_<?php echo $sl; ?>" value="<?php echo $row['floor_id']; ?>" />
					<input type="hidden" name="hdnProdResourceId_<?php echo $sl; ?>" id="hdnProdResourceId_<?php echo $sl; ?>" value="<?php echo $row['prod_id']; ?>" />
					<input type="hidden" name="hdnDate_<?php echo $sl; ?>" id="hdnDate_<?php echo $sl; ?>" value="<?php echo change_date_format($row['pr_date']); ?>" />
					<input type="hidden" name="hdnLineIds_<?php echo $sl; ?>" id="hdnLineIds_<?php echo $sl; ?>" value="<?php echo $row['line_number']; ?>" />
					<input type="hidden" name="hdnDtlsId_<?php echo $sl; ?>" id="hdnDtlsId_<?php echo $sl; ?>" value="<?php echo $row['idle_dtls_id']; ?>" />
					<input type="hidden" name="hdnMstId_<?php echo $sl; ?>" id="hdnMstId_<?php echo $sl; ?>" value="<?php echo $row['idle_mst_id']; ?>" />
					<input type="hidden" name="hdnSerialNo_<?php echo $sl; ?>" id="hdnSerialNo_<?php echo $sl; ?>" value="<?php echo $sl; ?>" />
				</td>
			</tr>
			<?php
			$sl++;
		}
	?>
		</tbody>
	</table>
	<table align="center" style="margin: 15px auto;">
		<tr>
			<td>
				<?php
					if($is_update_mode) {
						echo load_submit_buttons($permission, 'fnc_sewinglineidle_entry', 1, 0 , 'ResetForm();', 1);
					} else {
						echo load_submit_buttons($permission, 'fnc_sewinglineidle_entry', 0, 0 , 'ResetForm();', 1);
					}
					
				?>
			</td>
		</tr>
	</table>
	</div>
	<?php

	/*$retn_line=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.location_id=$cbo_location_id $floor_cond and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0");

	echo "select a.id, a.line_number, a.location_id, a.floor_id from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.location_id=$cbo_location_id $floor_cond and b.pr_date between ".$txt_date_from." and ".$txt_date_to." and a.is_deleted=0 and b.is_deleted=0";*/

	$lineData=ob_get_contents();
	ob_clean();

	echo $lineData;

	exit();
}

if ($action=='load_drop_down_causes') 
{
	$data=explode('_',$data);
	//echo "<pre>";print_r($data);
	// if($data[0]==1) $causeID='1,2,3,4,5,164,165,166,167,168,169,170,171,172,173,174,175,176,177,179,180,181,182,183,184';
	// else if($data[0]==2) $causeID='1,6,7,8,9,10,11,12,216,217,218,219,220,221,222,223,224,225,226,227,228,229,230';
	// else if($data[0]==3) $causeID='1,13,14,15,16,52,53,210,211,212,213,214,215';
	// else if($data[0]==4) $causeID='1,17,18,19';
	// else if($data[0]==5) $causeID='1,20,21,110,111,112,231,232,233,234,235';
	// else if($data[0]==6) $causeID='1,22,23,247,248,249,250,251,252,253,254';
	// else if($data[0]==7) $causeID='1,24,25';
	// else if($data[0]==8) $causeID='26,27,28,29';
	// else if($data[0]==9) $causeID='1,30,31,32,33,34,35,36';
	// else if($data[0]==10) $causeID='1,37,38,39,40,41';
	// else if($data[0]==11) $causeID='42,43,44';
	// else if($data[0]==12) $causeID='45,46,47';
	// else if($data[0]==13) $causeID='48,49,50';
	
	// else if($data[0]==14) $causeID='53,54,55,56,113,114,115,255,256,257,258,259,266,267,268,269,270,271,272,273,274,275,276,277,278,279';
	// else if($data[0]==15) $causeID='57,58,59,60,61,62,63,185,186,187,188,189,190,191,192,193,194,195,196,197';
	// else if($data[0]==16) $causeID='64,65,66,67';
	// else if($data[0]==17) $causeID='68,69,70,71,72,206,207,208,209';
	// else if($data[0]==18) $causeID='73,74,75,76,77,78,246';
	// else if($data[0]==19) $causeID='79,58,80,81,82,83';
	// else if($data[0]==20) $causeID='84,85,86,87,88,198,199,200,201,202,203,204,205';
	// else if($data[0]==21) $causeID='89,90,91,92';
	// else if($data[0]==22) $causeID='93,94';
	// else if($data[0]==23) $causeID='95,96';
	// else if($data[0]==24) $causeID='97,98,99';
	// else if($data[0]==25) $causeID='100,101,102,103,104';
	// else if($data[0]==26) $causeID='105,106,107,108';
	// else if($data[0]==30) $causeID='243,244,245'; 
	// else if($data[0]==32) $causeID='236,237,238,239,240,241,242'; 
	// else if($data[0]==99) $causeID='109,260,261,262,263,264,265';
	// else if($data[0]==27) $causeID='116,117,118,119,120,121,122,123,124,125,126';


	// else $causeID='0';
	//echo $data[0]."**".$data[1];
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	// $cause_arr=array();
	// // $cause_name_sql=("SELECT CATEGORY_ID,CAUSE_NAME from LIB_CATEGORY_WISE_CAUSES_ENTRY where status_active = 1  and CATEGORY_ID=$data[0] ");
	$cause_name=return_library_array("SELECT CAUSE_NAME,CAUSE_NAME from LIB_CATEGORY_WISE_CAUSES_ENTRY where status_active = 1  and CATEGORY_ID=$data[0]",'CAUSE_NAME', 'CAUSE_NAME');
	foreach($cause_name as $causeID )
	{
		$cause_arr[$causeID] = $npt_cause[$causeID];
	}
	echo create_drop_down( 'cboNptCause_'.$data[1], 130, $cause_arr, '', 1, '-- Select Cause --','', '', 0,'', '', '', '', '', '', "cboSubSection[]");
	// echo create_drop_down( 'cboNptCause_'.$data[1], 130, $npt_cause, '', 1, '-- Select Cause --', '', '', 0,$causeID, '', '', '', '', '', "cboSubSection[]");
	exit();
}

if($action == 'npt_cause_popup')
{ 
	echo load_html_head_contents('Remarks', '../../', 1, 1, $unicode);
	extract($_REQUEST);
 	//echo  $causeValue.'d';
	$causeArr = array_unique(explode('__', $causeValue));
	$category_id=return_library_array("SELECT CATEGORY_ID, CATEGORY_ID from LIB_CATEGORY_WISE_CAUSES_ENTRY where status_active = 1 ",'CATEGORY_ID', 'CATEGORY_ID');
	// echo "<pre>";print_r($category_id);die;
	
	foreach($category_id as $cat )
	{
		$dataArr[$cat] = $npt_category[$cat];
	}
	//echo "<pre>"; print_r($dataArr); exit();

	$cause_name=return_library_array("SELECT CAUSE_NAME,CAUSE_NAME from LIB_CATEGORY_WISE_CAUSES_ENTRY where status_active = 1  and CATEGORY_ID=$data[0]",'CAUSE_NAME', 'CAUSE_NAME');
	foreach($cause_name as $causeID )
	{
		$cause_arr[$causeID] = $npt_cause[$causeID];
	}
	
 	?>
	<script>
		function fn_add_npt_cause(i) {
			var row_num=$('#tbl_npt_cause tbody tr').length;
			if (i==0) {
				i=1;
			}
			if (row_num!=i) {
				return false;
			}
			else {
				i++;
				$("#tbl_npt_cause tbody tr:last").clone().find("input, select").each(function() {
					$(this).attr({
					'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
					'value': function(_, value) { return value }
					});

				}).end().appendTo("#tbl_npt_cause");

				$('#tbl_npt_cause tbody tr:last td:eq(2)').attr('id', 'causesTd_'+i);

				$('#cboNptCategory_'+i).removeAttr("onChange").attr("onChange","load_npt_cause("+i+");");

				// $('#txtitemgroup_'+i).removeAttr("onDblClick").attr("onDblClick","openpopup_itemgroup("+i+")");
				$('#increasetrim_'+i).removeAttr("onClick").attr("onClick","fn_add_npt_cause("+i+");");
				$('#decreasetrim_'+i).removeAttr("onClick").attr("onClick","fn_remove_npt_cause("+i+",'tbl_npt_cause');");
				$('#txtSerial_'+i).val(i);
				$('#txtManpower_'+i).removeAttr("onKeyUp").attr("onKeyUp","calculateIdleMnt("+i+");");

				$('#txtStartHours_'+i).removeAttr("onblur").attr("onblur","calculateTime("+i+");");
				$('#txtEndHours_'+i).removeAttr("onblur").attr("onblur","calculateTime("+i+");");
				$('#txtStartMinutes_'+i).removeAttr("onblur").attr("onblur","calculateTime("+i+");");
				$('#txtEndMinutes_'+i).removeAttr("onblur").attr("onblur","calculateTime("+i+");");

				$('#txtStartHours_'+i).removeAttr("onKeyUp").attr("onKeyUp","moveCursor(this.value, 'txtStartHours_"+i+"','txtStartMinutes_"+i+"',2,23);calculateIdleMnt("+i+");");
				$('#txtEndHours_'+i).removeAttr("onKeyUp").attr("onKeyUp","moveCursor(this.value, 'txtEndHours_"+i+"','txtEndMinutes_"+i+"',2,23);calculateIdleMnt("+i+");");
				$('#txtStartMinutes_'+i).removeAttr("onKeyUp").attr("onKeyUp","moveCursor(this.value,'txtStartMinutes_"+i+"','txtEndHours_"+i+"',2,59);calculateIdleMnt("+i+");");
				$('#txtEndMinutes_'+i).removeAttr("onKeyUp").attr("onKeyUp","moveCursor(this.value,'txtEndMinutes_"+i+"', 'txtManpower_"+i+"',2,59);calculateIdleMnt("+i+");");

				$('#cboNptCategory_'+i).val(0);
				$('#cboNptCause_'+i).val(0);
				$('#txtStartHours_'+i).val('');
				$('#txtStartMinutes_'+i).val('');
				$('#txtEndHours_'+i).val('');
				$('#txtEndMinutes_'+i).val('');
				$('#txtDurationHour_'+i).val('');
				$('#txtManpower_'+i).val('');
				$('#txtIdleMnt_'+i).val('');
				$('#updateIdLine_'+i).val('');
			}
		}

		function fn_remove_npt_cause(rowNo, table_id) {
			var r=confirm("Do you want to delete this row?.\n If yes press OK \n or press Cancel." );
			if(r==false) { return; }

			var numRow = $('table#tbl_npt_cause tbody tr').length;
			/*if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_npt_cause tbody tr:last').remove();
			}*/
			if(rowNo!=1) {
				var index=rowNo-1;
				$("table#tbl_npt_cause tbody tr:eq("+index+")").remove();
				var numRow = $('table#tbl_npt_cause tbody tr').length;
				for(i = rowNo; i <= numRow; i++) {
					// console.log('i: ' + i);
					// var j = i-1;
					// console.log('j: ' + j);
					$("#tbl_npt_cause tbody tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }
						});
					});
				}
			}
		}

		function moveCursor(val,id, field_id,lnth,max_val) {
			var str_length=val.length;
			
			if(str_length==lnth) {
				$('#'+field_id).select();
				$('#'+field_id).focus();
			}
			
			if(val>max_val)
			{
				document.getElementById(id).value=max_val;
			}
		}

		function calculateIdleMnt(rowNum) {
			var durationHour = document.getElementById('txtDurationHour_'+rowNum).value;
			var manpower = document.getElementById('txtManpower_'+rowNum).value;
			var idleMnt = durationHour * manpower * 60;
			document.getElementById('txtIdleMnt_'+rowNum).value = Number(idleMnt).toFixed(2);

			calculateTotalIdleMnt();
		}

		function calculateTotalIdleMnt() {
			var rowNum = $('#tbl_npt_cause tbody tr').length;
			var totalIdleMnt = 0;
			for (var i = 1; i <= rowNum; i++) {
				var currentIdleMnt = parseFloat(document.getElementById('txtIdleMnt_'+i).value);
				totalIdleMnt += currentIdleMnt;
			}

			document.getElementById('totalIdleMnt').value = Number(totalIdleMnt).toFixed(2) ;
		}

		function calculateTime(rowNum) {
			startHour = document.getElementById('txtStartHours_'+rowNum).value;
			endHour = document.getElementById('txtEndHours_'+rowNum).value;
			startMinute = document.getElementById('txtStartMinutes_'+rowNum).value;
			endMinute = document.getElementById('txtEndMinutes_'+rowNum).value;

			var startDate = new Date(0, 0, 0, startHour, startMinute, 0);
			var endDate = new Date(0, 0, 0, endHour, endMinute, 0);
			var diff = endDate.getTime() - startDate.getTime();
			var hours = Math.floor(diff / 1000 / 60 / 60);
			diff -= hours * 1000 * 60 * 60;
			var minutes = Math.floor(diff / 1000 / 60);

			if (hours < 0)
			hours = hours + 24;

			// var timeDiff = (hours <= 9 ? "0" : "") + hours + ":" + (minutes <= 9 ? "0" : "") + minutes;
			var timeDiff = ((hours * 60) + minutes) / 60;

			document.getElementById('txtDurationHour_'+rowNum).value = timeDiff;
		}

		function load_npt_cause(rowNo) {
			var category = $('#cboNptCategory_'+rowNo).val();
			var row_num = $('#tbl_npt_cause tbody tr').length;
			load_drop_down( 'cause_of_sewing_line_idle_controller', category+'_'+rowNo , 'load_drop_down_causes', 'causesTd_'+rowNo );
		}

		function checkDuplicates(arr) 
		{
			var counts = [];

			for (var i = 0; i <= arr.length; i++) {
				if (counts[arr[i]] === undefined) {
					counts[arr[i]] = 1;
				} else {
					return true;
				}
			}
			return false;
		}
		var cause_array = [];
		function js_set_value() {
			var rowNum = $('#tbl_npt_cause tbody tr').length;
			var allData = '';
			for (var i = 1; i <= rowNum; i++) {
				var catId = document.getElementById('cboNptCategory_'+i).value;
				var causeid = document.getElementById('cboNptCause_'+i).value;
				var startHour = document.getElementById('txtStartHours_'+i).value;
				var startMinute = document.getElementById('txtStartMinutes_'+i).value;
				var endHour = document.getElementById('txtEndHours_'+i).value;
				var endMinute = document.getElementById('txtEndMinutes_'+i).value;
				var manpower = document.getElementById('txtManpower_'+i).value;
				var duration = document.getElementById('txtDurationHour_'+i).value;
				var idleMnt = document.getElementById('txtIdleMnt_'+i).value;
				var updateId = document.getElementById('updateIdLine_'+i).value;
				if(catId=='') catId=0; 	if(updateId=='') updateId=0; 
				if(causeid=='') causeid=0; 
				if(startHour=='') startHour=0; if(startMinute=='') startMinute=0; 
				if(endHour=='') endHour=0; 
				if(endMinute=='') endMinute=0; if(manpower=='') manpower=0; if(duration=='') duration=0;  if(idleMnt=='') idleMnt=0;  

				if (catId == 0 || causeid == 0 || startHour == '' || startMinute == '' || endHour == '' || endMinute == '' || manpower == '' || duration == '' || idleMnt == '') {
					alert('Please fill in all the value');
					return;
				}
				if(allData=='')
				{
					allData += catId + '_' + causeid + '_' + startHour + '_' + startMinute + '_' + endHour + '_' + endMinute + '_' + manpower + '_' + duration + '_' + idleMnt + '_' + updateId;
					//alert(updateId);
				}
				else
				{
				allData +="__"+catId + '_' + causeid + '_' + startHour + '_' + startMinute + '_' + endHour + '_' + endMinute + '_' + manpower + '_' + duration + '_' + idleMnt + '_' + updateId;
				}
				cause_array.push(catId + '_' + causeid + '_' + startHour + '_' + startMinute + '_' + endHour + '_' + endMinute + '_' + manpower);
			}
			if (checkDuplicates(cause_array)) 
			{
				alert('Error: you have enter duplicates values ! Please check carefully.');
				cause_array = [];
				return;
			}

		//	allData = allData.replace(/__$/, '');
			//allData = allData.replace(/@!@$/, '');
	//alert(allData);
			document.getElementById('txtAllData').value = allData;
			parent.emailwindow.hide();
		}
	</script>
	<body>
		<div align="center">
			<fieldset style="width:810px; margin-top:10px;">
				<legend>NPT Cause Details</legend>
				<form name="nptcausedetails_2" id="nptcausedetails_2" autocomplete="off">
					<table class="rpt_table" width="100%" cellspacing="1" id="tbl_npt_cause">
						<thead>
							<tr>
								<th width="15" rowspan="2">SL</th>
								<th width="85" rowspan="2">Category</th>
								<th width="130" rowspan="2">Causes</th>
								<th width="140" colspan="2">Time Range</th>
								<th width="70" rowspan="2">Duration (Hour)</th>
								<th width="60" rowspan="2">Manpower</th>
								<th width="60" rowspan="2">Idle Mnt</th>
								<th width="60" rowspan="2">Action</th>
							</tr>
							<tr>
								<th width="60">Start</th>
								<th width="60">End</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if( $causeValue != '' ) 
								{
									$totalIdleMnt = 0;
									$rowNum = 1;
									foreach ($causeArr as $index => $cause)
									{
										$singleCauseArr = explode('_', $cause);
										$category = $singleCauseArr[0];
										$cause = $singleCauseArr[1];
										$startHour = $singleCauseArr[2];
										$startMinute = $singleCauseArr[3];
										$endHour = $singleCauseArr[4];
										$endMinute = $singleCauseArr[5];
										$manpower = $singleCauseArr[6];
										$duration = $singleCauseArr[7];
										$idleMnt = $singleCauseArr[8];

										$updateId = $singleCauseArr[9];

										// $rowNum = $index + 1;
										$totalIdleMnt += $idleMnt;
										?>
																			
										<tr class="general" >
											<input type="hidden" id="updateIdLine_<?php echo $rowNum; ?>" name="updateIdLine_<?php echo $rowNum; ?>" value="<?php echo $updateId; ?>" />
											<td>
												<input type="text" class="text_boxes" id="txtSerial_<?php echo $rowNum; ?>" name="txtSerial_<?php echo $rowNum; ?>" readonly value="<?php echo $rowNum; ?>" style="width: 10px;">
											</td>
											<td id="nptCategoryTd_<?php echo $rowNum; ?>">
												<?php
													echo create_drop_down( 'cboNptCategory_'.$rowNum, 90, $dataArr, 1, 1, '-- Select --',$category, "load_npt_cause($rowNum)", 0, '', '', '', '');//  echo $category
												?>
											</td>
											<td id="causesTd_<?php echo $rowNum; ?>">
												<?php
													echo create_drop_down( 'cboNptCause_'.$rowNum, 130, $cause_arr, 1, 1, '-- Select --', '', '', 0, '', '', '', '');
												?>
											</td>
											<td>
												<input type="text" name="txtStartHours_<?php echo $rowNum; ?>" id="txtStartHours_<?php echo $rowNum; ?>" class="text_boxes_numeric" placeholder="HH" style="width:20px;" onBlur="calculateTime(<?php echo $rowNum; ?>);" onKeyUp="moveCursor(this.value,'txtStartHours_<?php echo $rowNum; ?>','txtStartMinutes_<?php echo $rowNum; ?>',2,23);calculateIdleMnt(<?php echo $rowNum; ?>)" value="<?php echo $startHour; ?>"> :
												<input type="text" name="txtStartMinutes_<?php echo $rowNum; ?>" id="txtStartMinutes_<?php echo $rowNum; ?>" class="text_boxes_numeric" placeholder="MM" style="width:20px;" onBlur="calculateTime(<?php echo $rowNum; ?>);" onKeyUp="moveCursor(this.value,'txtStartMinutes_<?php echo $rowNum; ?>','txtEndHours_<?php echo $rowNum; ?>',2,59);calculateIdleMnt(<?php echo $rowNum; ?>)" value="<?php echo $startMinute; ?>">
											</td>
											<td>
												<input type="text" name="txtEndHours_<?php echo $rowNum; ?>" id="txtEndHours_<?php echo $rowNum; ?>" class="text_boxes_numeric" placeholder="HH" style="width:20px;" onBlur="calculateTime(<?php echo $rowNum; ?>);" onKeyUp="moveCursor(this.value,'txtEndHours_<?php echo $rowNum; ?>','txtEndMinutes_<?php echo $rowNum; ?>',2,23);calculateIdleMnt(<?php echo $rowNum; ?>)" value="<?php echo $endHour; ?>" > :
												<input type="text" name="txtEndMinutes_<?php echo $rowNum; ?>" id="txtEndMinutes_<?php echo $rowNum; ?>" class="text_boxes_numeric" placeholder="MM" style="width:20px;" onBlur="calculateTime(<?php echo $rowNum; ?>);" onKeyUp="moveCursor(this.value,'txtEndMinutes_<?php echo $rowNum; ?>', 'txtManpower_<?php echo $rowNum; ?>',2,59);calculateIdleMnt(<?php echo $rowNum; ?>)" value="<?php echo $endMinute; ?>" >
											</td>
											<td>
												<input type="text" name="txtDurationHour_<?php echo $rowNum; ?>" id="txtDurationHour_<?php echo $rowNum; ?>" class="text_boxes_numeric" style="width:60px;" readonly value="<?php echo number_format($duration,2, ".", ""); ?>" />
											</td>
											<td>
												<input type="text" name="txtManpower_<?php echo $rowNum; ?>" id="txtManpower_<?php echo $rowNum; ?>" class="text_boxes_numeric" autocomplete="off" style="width:60px;" onKeyUp="calculateIdleMnt(<?php echo $rowNum; ?>)" value="<?php echo $manpower; ?>" />
											</td>
											<td>
												<input type="text" name="txtIdleMnt_<?php echo $rowNum; ?>" id="txtIdleMnt_<?php echo $rowNum; ?>" class="text_boxes_numeric" readonly value="<?php echo round($idleMnt,2); ?>" />
											</td>
											<td style="display: inline-flex;">
												<input type="button" id="increasetrim_<?php echo $rowNum; ?>" name="increasetrim_<?php echo $rowNum; ?>" style="width:30px" class="formbutton" value="+" onClick="fn_add_npt_cause(<?php echo $rowNum; ?>)" />
												<input type="button" id="decreasetrim_<?php echo $rowNum; ?>" name="decreasetrim_<?php echo $rowNum; ?>" style="width:30px" class="formbutton" value="-" onClick="fn_remove_npt_cause(<?php echo $rowNum; ?>, 'tbl_npt_cause' );" />
											</td>
										</tr>
										<script>
											load_drop_down( 'cause_of_sewing_line_idle_controller', "<?php echo $category; ?>_<?php echo $rowNum; ?>", 'load_drop_down_causes', "causesTd_<?php echo $rowNum; ?>" );
											document.getElementById('cboNptCause_'+<?php echo $rowNum; ?>).value = "<?php echo $cause; ?>"
										</script>
										<?php
										
										$rowNum++;
										
									}

								} else {
							?>
								<tr class="general" >
									<input type="hidden" id="updateIdLine_1" name="updateIdLine_1" />
									<td>
										<input type="text" class="text_boxes" id="txtSerial_1" name="txtSerial_1" readonly value="1" style="width: 10px;">
									</td>
									<td>
										<?php
											 echo create_drop_down( 'cboNptCategory_1', 90, $dataArr, 1, 1, '-- Select --', $category,'load_npt_cause(1)', 0, '', '', '', '');
										?>
									</td>
									<td id="causesTd_1">
										<?php
											echo create_drop_down( 'cboNptCause_1', 130, $cause_arr, 1, 1, '-- Select --', '', '', 0, '', '', '', '');
										?>
									</td>
									<td>
										<input type="text" name="txtStartHours_1" id="txtStartHours_1" class="text_boxes_numeric" placeholder="HH" style="width:20px;" onBlur="calculateTime(1);" onKeyUp="moveCursor(this.value,'txtStartHours_1','txtStartMinutes_1',2,23);calculateIdleMnt(1)"> :
										<input type="text" name="txtStartMinutes_1" id="txtStartMinutes_1" class="text_boxes_numeric" placeholder="MM" style="width:20px;" onBlur="calculateTime(1);" onKeyUp="moveCursor(this.value,'txtStartMinutes_1','txtEndHours_1',2,59);calculateIdleMnt(1)">
									</td>
									<td>
										<input type="text" name="txtEndHours_1" id="txtEndHours_1" class="text_boxes_numeric" placeholder="HH" style="width:20px;" onBlur="calculateTime(1);" onKeyUp="moveCursor(this.value,'txtEndHours_1','txtEndMinutes_1',2,23);calculateIdleMnt(1)"> :
										<input type="text" name="txtEndMinutes_1" id="txtEndMinutes_1" class="text_boxes_numeric" placeholder="MM" style="width:20px;" onBlur="calculateTime(1);" onKeyUp="moveCursor(this.value,'txtEndMinutes_1', 'txtManpower_1',2,59);calculateIdleMnt(1)">
									</td>
									<td>
										<input type="text" name="txtDurationHour_1" id="txtDurationHour_1" class="text_boxes_numeric" style="width:60px;" readonly />
									</td>
									<td>
										<input type="text" name="txtManpower_1" id="txtManpower_1" class="text_boxes_numeric" autocomplete="off" style="width:60px;" onKeyUp="calculateIdleMnt(1)" />
									</td>
									<td>
										<input type="text" name="txtIdleMnt_1" id="txtIdleMnt_1" class="text_boxes_numeric" readonly />
									</td>
									<td style="display: inline-flex;">
										<input type="button" id="increasetrim_1" name="increasetrim_1" style="width:30px" class="formbutton" value="+" onClick="fn_add_npt_cause(1)" />
										<input type="button" id="decreasetrim_1" name="decreasetrim_1" style="width:30px" class="formbutton" value="-" onClick="fn_remove_npt_cause(1, 'tbl_npt_cause' );" />
									</td>
								</tr>
							<?php
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="3">Total</th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>
									<input type="text" id="totalIdleMnt" name="totalIdleMnt" class="text_boxes_numeric" align="right" readonly value="<?php echo round($totalIdleMnt, 2); ?>">
								</th>
								<th></th>
							</tr>
						</tfoot>
					</table>
					<table width="100%">
						<tr>
							<td align="center">
								<input type="hidden" name="txtAllData" id="txtAllData">
								<input type="button" id="btnClose" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value();" />
							</td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body> 
	<?php
}

if($action == 'remarks_popup') {
	echo load_html_head_contents('Remarks', '../../', 1, 1, $unicode);
	extract($_REQUEST);
?>
    <script>
		function js_set_value(val) {
			document.getElementById('text_new_remarks').value=val;
			parent.emailwindow.hide();
		}
	</script>
    </head>
<body>
<div align="center">
	<fieldset style="width:400px;margin-left:4px;">
        <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
            <table cellpadding="0" cellspacing="0" width="370" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="auto_id" id="auto_id" value="<?php echo $data; ?>" />
                     	<textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:330px; height:270px" placeholder="Remarks Here. Maximum 1000 Character." ><?php echo $data; ?></textarea>
                    </td>
                </tr>
                <tr>
                	<td align="center">
                		<input type="button" id="btnClose" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                	</td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>    
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action == 'save_update_delete') {
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    if ($operation==0) { // save here
        $flag = 1;
        $add_comma_mst = false;
        $add_comma_dtls = false;
        $field_array_mst = 'id, company_id, location_id, floor_id, line_ids, idle_date, prod_resource_id, remarks,string_data, inserted_by, insert_date';
        $field_array_dtls = 'id, mst_id, line_ids, category_id, cause_id, start_hour, start_minute, end_hour, end_minute,  manpower,duration_hour, inserted_by, insert_date';
        $rowwise_mstid = ''; 

        $cbo_company_id = str_replace("'", '', $cbo_company_id);
        $cbo_location_id = str_replace("'", '', $cbo_location_id);
        // $entryForm = 0;
        $con = connect();
        $mstId = return_next_id('id', 'sewing_line_idle_mst', 1);
        $dtlsId = return_next_id('id', 'sewing_line_idle_dtls', 1);

        if($db_type==0) {
            mysql_query("BEGIN");
        }

        for($i=1; $i<=$total_row; $i++) {
        	$remarks = 'remarksvalue_'.$i;
        	$floor = 'hdnFloorId_'.$i;
        	$lineIds = 'hdnLineIds_'.$i;
        	$date = 'hdnDate_'.$i;
        	$prodResId = 'hdnProdResourceId_'.$i;
        	$rowCauseValueD = 'hdnCauseValue_'.$i;
        	$serialNo = 'hdnSerialNo_'.$i;
        	$rowwise_causeValue .= "{$$serialNo}__";
			$rowCauseValue=str_replace("'",'',$$rowCauseValueD);

            if($db_type==0){
				$idle_date=change_date_format(str_replace("'",'', $$date),'yyyy-mm-dd');
			}else{
				$idle_date=change_date_format(str_replace("'",'', $$date), "", "",1);
			}
			//rowCauseValue
            $data_array_mst .= $add_comma_mst ? ',' : ''; 
            $data_array_mst .= "(".$mstId.",".$cbo_company_id.",".$cbo_location_id.",".$$floor.",'".$$lineIds."','".$idle_date."',".$$prodResId.",'".$$remarks."','".$rowCauseValue."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

            $add_comma_mst = true; 

            $causeValueArr = explode('__', $rowCauseValue);

            foreach ($causeValueArr as $causeValue) {
            	$newCauseValue = '';
            	$data_array_dtls .= $add_comma_dtls ? ',' : ''; 
            	$values = explode('_', $causeValue);
            	$category = $values[0];
				$cause = $values[1];
				$startHour = $values[2];
				$startMinute = $values[3];
				$endHour = $values[4];
				$endMinute = $values[5];
				$manpower = $values[6];
				$duration = $values[7];
				$newCauseValue = $causeValue . '_' . $dtlsId . '__';
				$rowwise_causeValue .= "{$newCauseValue}";
            	$data_array_dtls .= "(".$dtlsId.",".$mstId.",'".$$lineIds."',".$category.",".$cause.",".$startHour.",".$startMinute.",".$endHour.",".$endMinute.",".$manpower.",".$duration.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

            	$add_comma_dtls = true;
            	$dtlsId++;
            }
            
            $rowwise_mstid .= "{$$serialNo}__{$mstId}@@@@@";
            $rowwise_causeValue .= "@@@@@";
            
            $mstId++;
        }

        $rowwise_mstid = rtrim($rowwise_mstid, '@@@@@');
        $rowwise_causeValue = rtrim($rowwise_causeValue, '@@@@@');
        $rowwise_causeValue = rtrim($rowwise_causeValue, '__');
        $rID = sql_insert('sewing_line_idle_mst', $field_array_mst, $data_array_mst, 0);
        $flag = ($flag && $rID); 

       $rID2 = sql_insert('sewing_line_idle_dtls', $field_array_dtls, $data_array_dtls, 0);
		// echo "10**insert into sewing_line_idle_mst ($field_array_mst) values $data_array_mst";die();
		//echo "10**=".$rID.'='.$rID2;die;
        $flag = ($flag && $rID2); 

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");
                echo "0**".$rowwise_mstid.'**'.$rowwise_causeValue;
            } else {
                mysql_query("ROLLBACK");
                echo "10**".$rowwise_mstid.'**'.$rowwise_causeValue;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo "0**".$rowwise_mstid.'**'.$rowwise_causeValue;
            } else {
                oci_rollback($con);
                echo "10**".$rowwise_mstid.'**'.$rowwise_causeValue;
            }
        }

        disconnect($con);
        die;
    }

    if ($operation==1) 
	{ // update here
    	$con = connect();
    	$flag = 1;
        $add_comma_mst = false;
        $add_comma_dtls = false;
        $mstId = return_next_id('id', 'sewing_line_idle_mst', 1);
        $dtlsId = return_next_id('id', 'sewing_line_idle_dtls', 1);
		 

        $rID1 = $rID2 = $rID3 = 1;

        if($db_type==0) {
            mysql_query("BEGIN");
        }

        $field_array_mst = 'id, company_id, location_id, floor_id, line_ids, idle_date, prod_resource_id,string_data, inserted_by, insert_date';
		$field_array_mst_up = 'string_data*updated_by*update_date*remarks';
        $field_array_dtls = 'id, mst_id, line_ids, category_id, cause_id, start_hour, start_minute, end_hour, end_minute, duration_hour, manpower, inserted_by, insert_date';
        $field_array_update_dtls = 'category_id*cause_id*start_hour*start_minute*end_hour*end_minute*manpower*duration_hour*updated_by*update_date';
	
        for($i=1; $i<=$total_row; $i++) {
        	$prevMstID = 'hdnMstId_'.$i;
			$prevMstId=str_replace("'",'',$$prevMstID);
			$remarks = 'remarksvalue_'.$i;
        	if($prevMstId != '') {
				 
	        	$rowCauseValue = 'hdnCauseValue_'.$i;
	        	$serialNo = 'hdnSerialNo_'.$i;
	        	$rowwise_causeValue .= "{$$serialNo}__";
		//$dtlsId = return_next_id('id', 'sewing_line_idle_dtls', 1);
			$dtlsrDelete = execute_query("delete from sewing_line_idle_dtls where mst_id=$prevMstId",1); 
			//$dtlsId = delete_dtls(1);
			 
			$data_array_mst_up="'".$$rowCauseValue."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$$remarks."'";
			
			$rID_up=sql_update("sewing_line_idle_mst",$field_array_mst_up,$data_array_mst_up,"id","".$prevMstId."",1);
			//echo $rID_up;die;
		//	echo "10**=delete from sewing_line_idle_dtls where mst_id=$prevMstId";die;
	            $causeValueArr = explode('__', $$rowCauseValue);

	            foreach ($causeValueArr as $causeValue) {
	            	$newCauseValue = '';	            	
	            	$values = explode('_', $causeValue);
	            	$category = $values[0];
					$cause = $values[1];
					$startHour = $values[2];
					$startMinute = $values[3];
					$endHour = $values[4];
					$endMinute = $values[5];
					$manpower = $values[6];
					$duration = $values[7];
					$update_idd = $values[9];
					$update_id=str_replace("'",'',$update_idd);
					
				
							
					// if new category added
		            	$lineIds = 'hdnLineIds_'.$i;
						$prevMstID_chk = 'hdnMstId_'.$i;
		            	$newCauseValue = '';
		            	$values = explode('_', $causeValue);
		            	$category = $values[0];
						$cause = $values[1];
						$startHour = $values[2];
						$startMinute = $values[3];
						$endHour = $values[4];
						$endMinute = $values[5];
						$manpower = $values[6];
						$duration = $values[7];
						// $idleMnt = $values[8];
						$newCauseValue = $causeValue . '_' . $dtlsId . '__';
						$rowwise_causeValue .= "{$newCauseValue}";
						if($data_array_dtls1!='') $data_array_dtls1.=",";
		            	$data_array_dtls1.= "(".$dtlsId.",".$$prevMstID_chk.",'".$$lineIds."',".$category.",".$cause.",".$startHour.",".$startMinute.",".$endHour.",".$endMinute.",".$duration.",".$manpower.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$chk_dtlIdArr2[$dtlsId]=$dtlsId;

		            	$dtlsId++;

		            //  echo "10=A**insert into sewing_line_idle_dtls(".$field_array_dtls.") values ".$data_array_dtls."=".$rID_up; 
		            	    // return true if $flag is true and dtls table insert is successful
		            
	            }
	            $rowwise_mstid .= "{$$serialNo}__{$mstId}@@@@@";
	        } else { // new entry
			  // $dtlsId =$dtlsId+1;
			  
	        //	$dtlsId=delete_dtls(0);
				
				$floor = 'hdnFloorId_'.$i;
	        	$lineIds = 'hdnLineIds_'.$i;
	        	$date = 'hdnDate_'.$i;
	        	$prodResId = 'hdnProdResourceId_'.$i;
	        	$rowCauseValue = 'hdnCauseValue_'.$i;
	        	$serialNo = 'hdnSerialNo_'.$i;
	        	$rowwise_causeValue .= "{$$serialNo}__";
				$rowCauseValueD=str_replace("'",'',$$rowCauseValue);
	            if($db_type==0){
					$idle_date=change_date_format(str_replace("'",'', $$date),'yyyy-mm-dd');
				}else{
					$idle_date=change_date_format(str_replace("'",'', $$date), "", "",1);
				}

	           // $data_array_mst .= $add_comma_mst ? ',' : '';
				

	            $add_comma_mst = true;
				$rowCauseValueD=str_replace("'",'',$$rowCauseValue);
	          	if($rowCauseValueD!='')
				{
					//$data_array_mst .= $add_comma_mst ? ',' : '';
					if($data_array_mst!='') $data_array_mst.=",";
 
	            $data_array_mst .= "(".$mstId.",".$cbo_company_id.",".$cbo_location_id.",".$$floor.",'".$$lineIds."','".$idle_date."',".$$prodResId.",'".$rowCauseValueD."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				// $add_comma_mst = true;
			    $causeValueArr = explode('__', $rowCauseValueD);
				//$k=1;
				 
	            foreach ($causeValueArr as $causeValue) {
	            	$newCauseValue = '';
	            //	$data_array_dtls .= $add_comma_dtls ? ',' : ''; 
	            	$values = explode('_', $causeValue);
	            	$category = $values[0];
					$cause = $values[1];
					$startHour = $values[2];
					$startMinute = $values[3];
					$endHour = $values[4];
					$endMinute = $values[5];
					$manpower = $values[6];
					$duration = $values[7];
					// $idleMnt = $values[8];
					$newCauseValue = $causeValue . '_' . $dtlsId . '__';
	            	// $newCauseValue = rtrim($newCauseValue, '__');
					$rowwise_causeValue .= "{$newCauseValue}";
						if($data_array_dtls!='') $data_array_dtls.=",";
					 
	            	$data_array_dtls .= "(".$dtlsId.",".$mstId.",'".$$lineIds."',".$category.",".$cause.",".$startHour.",".$startMinute.",".$endHour.",".$endMinute.",".$duration.",".$manpower.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$chk_dtlIdArr[$dtlsId]=$dtlsId;

	            	//$add_comma_dtls = true;
					 
	            	$dtlsId++;
	             }
				}
	            
	            $rowwise_mstid .= "{$$serialNo}__{$mstId}@@@@@";
	            $rowwise_causeValue .= "@@@@@";
	            
	            $mstId++;

		      // echo "10**".   $data_array_mst; mysql_query("ROLLBACK");die;
			   
			   
			 
				
				
				
				
	        }
        }
		
		if($data_array_mst!='')
		{
		 $rID3 = sql_insert('sewing_line_idle_mst', $field_array_mst, $data_array_mst, 0);
		// echo "10**A=insert into sewing_line_idle_mst(".$field_array_mst.") values ".$data_array_mst.""; die;
			$flag = ($flag && $rID3);
		}
		
		if($data_array_dtls1!="")
		{
		$rID2 = sql_insert('sewing_line_idle_dtls', $field_array_dtls, $data_array_dtls1, 0);
		// echo "10**A=insert into sewing_line_idle_dtls(".$field_array_dtls.") values ".$data_array_dtls1."";  
		$flag = ($flag && $rID2);
		}
		
		if($data_array_dtls!='')
		{
		$rID4 = sql_insert('sewing_line_idle_dtls', $field_array_dtls, $data_array_dtls, 0);
		// echo "10**B=insert into sewing_line_idle_dtls(".$field_array_dtls.") values ".$data_array_dtls.""; die;
		// echo "10**B=insert into sewing_line_idle_mst(".$field_array_mst.") values ".$data_array_mst.""; die;
		$flag = ($flag && $rID4);
		}
				// echo "10**=". $rID3 .'='. $rID4.'='. $flag; die;
				//echo "10**=". $rID3 .'='. $rID4; die;
				
		
						
			//	print_r($chk_dtlIdArr);
			//	print_r($chk_dtlIdArr2);
		//	 echo "10**=". $flag.'='. $rID2 .'='. $rID3.'='. $rID4; die;

        $rowwise_mstid = rtrim($rowwise_mstid, '@@@@@');
        $rowwise_causeValue = rtrim($rowwise_causeValue, '@@@@@');
        $rowwise_causeValue = rtrim($rowwise_causeValue, '__');
			/* $rID3 = sql_insert('sewing_line_idle_mst', $field_array_mst, $data_array_mst, 0);
		        
		        $flag = ($flag && $rID3);  
		        $rID4 = sql_insert('sewing_line_idle_dtls', $field_array_dtls, $data_array_dtls, 0);
			 	  echo "10**=B=insert into sewing_line_idle_dtls(".$field_array_dtls.") values ".$data_array_dtls.""; die;
 		echo "10**=". $flag.'='. $rID3 .'='. $rID4; die;
		        $flag = ($flag && $rID4);*/ 
        // echo "10**$flag";disconnect($con);die;

        if($db_type==0) {
            if($flag) {
                mysql_query("COMMIT");
                echo "1**".$rowwise_mstid.'**'.$rowwise_causeValue;
            } else {
                mysql_query("ROLLBACK");
                echo "10**".$rID1.'**'.$rID2.'**'.$rID3.'**'.$rID4;
            }
        }
        else if($db_type==2) {
            if($flag) {
                oci_commit($con);
                echo "1**".$rowwise_mstid.'**'.$rowwise_causeValue;
            } else {
                oci_rollback($con);
                echo "10**".$rID1.'**'.$rID2.'**'.$rID3.'**'.$rID4;
            }
        }

		disconnect($con);
        die;
    }
}