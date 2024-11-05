<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$team_name_library=return_library_array( "select id,team_name from lib_marketing_team", "id", "team_name"  );
$team_member_name_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$year=str_replace("'","",$cbo_year_selection);
	//$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_session['logic_erp']["data_level_secured"]==1)
		{
			if($_session['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_session['logic_erp']["buyer_id"].")";
			}
			else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	//cbo_company_name*cbo_buyer_name*txt_req_no*cbo_date_type*txt_date_from*txt_date_to*cbo_labappstatus*cbo_req_for*cbo_year_selection
	$txt_req_no=str_replace("'","",$txt_req_no);
	$txt_req_no=trim($txt_req_no);
	if($txt_req_no !="" || $txt_req_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$req_no=$company_library[$company_name]."-".$year."-".str_pad($txt_req_no, 5, 0, str_pad_left);
		$reqcond="and a.requisition_number_prefix_num ='".$txt_req_no."'";
	}
	else $reqcond="";

	if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'yyyy')=$year"; else $year_field_cond="";
	
	$date_cond='';
	if(str_replace("'","",$cbo_date_type)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$req_date_cond="and a.requisition_date between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_date_type)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.plandeliverydate between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_date_type)==3)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.submitted_to_buyer between '$start_date' and '$end_date'";
		}
	}
	if(str_replace("'","",$cbo_date_type)==4)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.approval_status_date between '$start_date' and '$end_date'";
		}
	}
	$labappstatuscond="";
	if(str_replace("'","",$cbo_labappstatus)!=0)
	{
		if(str_replace("'","",$cbo_labappstatus)!=6){
			$labappstatuscond=" and b.approval_status='".str_replace("'","",$cbo_labappstatus)."' ";
		}
		else{
			$labappstatuscond=" and b.approval_status=0 ";
		}
	}
	
		
	if(str_replace("'","",$cbo_req_for)!=0) $reqforcond=" and a.req_for='".str_replace("'","",$cbo_req_for)."' "; else $reqforcond="";
	if(str_replace("'","",$cbo_date_type)!=1){
		$sql="SELECT a.id, a.company_id, a.sample_stage_id, a.style_ref_no, a.buyer_name, a.team_leader, a.dealing_marchant, a.requisition_number, a.requisition_date, a.copy_from, d.color_id as color_name_id,b.fabric_color, b.plandeliverydate, b.submitted_to_buyer, b.approval_status_date, b.approval_status, (b.plandeliverydate-c.labreqdate) as planleadtime, '' as actionleadtime, c.labreqdate, '' as recv_from_factory_date, b.lapdip_no, b.applabdipno from sample_development_mst a join sample_development_fabric_acc c on a.id=c.sample_mst_id join sample_development_rf_color d on c.id=d.dtls_id and d.mst_id=a.id join  wo_po_lapdip_approval_info b on a.id=b.requisition_id and d.fabric_color=b.color_name_id  where a.company_id in ($company_name) and b.app_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form_id=117 $buyer_id_cond $reqcond $date_cond $labappstatuscond $reqforcond $year_field_cond group by a.id, a.company_id, a.sample_stage_id, a.style_ref_no, a.buyer_name, a.team_leader, a.dealing_marchant, a.requisition_number, a.requisition_date, a.copy_from, d.color_id , b.plandeliverydate, b.submitted_to_buyer, b.approval_status_date, b.approval_status, (b.plandeliverydate-c.labreqdate), (b.recv_from_factory_date-c.labreqdate), c.labreqdate, b.recv_from_factory_date, b.lapdip_no, b.applabdipno,d.fabric_color order by d.color_id";
	}
	else{
		$sql="SELECT a.id, a.company_id, a.sample_stage_id, a.style_ref_no, a.buyer_name, a.team_leader, a.dealing_marchant, a.requisition_number, a.requisition_date, a.copy_from, d.color_id as color_name_id,d.fabric_color, b.plandeliverydate, b.submitted_to_buyer, b.approval_status_date, b.approval_status, (b.plandeliverydate-c.labreqdate) as planleadtime, (b.recv_from_factory_date-c.labreqdate) as actionleadtime, c.labreqdate, b.recv_from_factory_date, b.lapdip_no, b.applabdipno from sample_development_mst a join sample_development_fabric_acc c on a.id=c.sample_mst_id join sample_development_rf_color d on c.id=d.dtls_id left join wo_po_lapdip_approval_info b on a.id=b.requisition_id  and b.app_type=2 and b.status_active=1 and b.is_deleted=0 and d.fabric_color=b.color_name_id $labappstatuscond  where a.company_id in ($company_name) and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.entry_form_id=117 $buyer_id_cond $reqcond  $reqforcond $req_date_cond $year_field_cond group by a.id, a.company_id, a.sample_stage_id, a.style_ref_no, a.buyer_name, a.team_leader, a.dealing_marchant, a.requisition_number, a.requisition_date, a.copy_from, d.color_id , b.plandeliverydate, b.submitted_to_buyer, b.approval_status_date, b.approval_status, (b.plandeliverydate-c.labreqdate), (b.recv_from_factory_date-c.labreqdate), c.labreqdate, b.recv_from_factory_date, b.lapdip_no, b.applabdipno,d.fabric_color order by d.color_id";
	}
	
	
	//echo $sql; 
	//sample_development_rf_color
	$sql_data=sql_select($sql);
	foreach($sql_data as $row){
		$req_id_arr[$row[csf('id')]]=$row[csf('id')];
	}
	$req_id_cond = where_con_using_array($req_id_arr,1,"master_tble_id");
	$sample_file_data=sql_select("select image_location, master_tble_id, id from common_photo_library where file_type=2 and is_deleted=0 $req_id_cond");
	$i=0;
	foreach($sample_file_data as $row){
		$image_location=$row[csf('image_location')];
		if($i==0){
			$sample_file_arr[$row[csf('master_tble_id')]][]="<a href='../../".$image_location."' download>file</a>";
		}
		else{
			$sample_file_arr[$row[csf('master_tble_id')]][]="<a href='../../".$image_location."' download>file".$i."</a>";
		}
		$i++;
		
	}
	/* echo '<pre>';
	print_r($sample_file_arr); die; */

	$company_name_arr=explode(",",$company_name);
	foreach($company_name_arr as $comid){
		$company_str_arr[$comid]=$company_library[$comid];
	}

	
	ob_start();
	?>
    <div style="width:1400px">
    <fieldset style="width:100%;">	
        <table width="1500">
            <tr class="form_caption">
                <td colspan="17" align="center"><?=$report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="17" align="center"><?= implode(", ",$company_str_arr); ?></td>
            </tr>
        </table>
        <table class="rpt_table" width="1520" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="110">Company Name</th>
                <th width="100">Team Leader</th>
                <th width="100">Dealing Merchant</th>
                <th width="100">Buyer</th>
                <th width="100">Sample Stage</th>
                <th width="100">Style Ref</th>
                <th width="100">Requisition No</th>
                <th width="100">Pre-Requisition No</th>
                <th width="110">Gmts Color</th>
				<th width="100">Fabric Color</th>
                <th width="70">Lab Req. Date</th>
                <th width="70">Planned Delivery Date</th>
                <th width="70">Submission Date</th>
                <th width="70">Approval Status Date</th>
                <th width="80">Approval Status</th>
                <th width="80">App. Labdip No</th>
                <th width="80">Submit Lab No</th>
                <th width="60">Planned Lead Time</th>
                <th width="60">Actual Lead Time</th>
                <th width="60">OTD</th>
                <th width="60">File</th>
            </thead>
			<tbody id="table_body">
				<? $i=1;
				foreach($sql_data as $row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if(strtotime($row[csf('plandeliverydate')])!='' && strtotime($row[csf('recv_from_factory_date')])!=''){
						if (strtotime($row[csf('plandeliverydate')]) >= strtotime($row[csf('recv_from_factory_date')])) {
							$otd='yes';
						}else{
							$otd='no';
						}
					}
					else{
						$otd='';
					}
					$planleadtime=''; $actionleadtime='';
					if(($row[csf('planleadtime')]>1 || $row[csf('planleadtime')]==0) && $row[csf('planleadtime')]!=''){
						$planleadtime=$row[csf('planleadtime')]+1;
					}
					if(($row[csf('actionleadtime')]>1 || $row[csf('actionleadtime')]==0) && $row[csf('actionleadtime')]!=''){
						$actionleadtime=$row[csf('actionleadtime')]+1;
					}
					$function="generate_report('".$row[csf('company_id')]."*".$row[csf('id')]."*1');";
					

					?>
					<tr align="center" bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>"  >
						<td width="30" align="center"><?=$i; ?></td>
						<td width="110"><?=$company_library[$row[csf('company_id')]]; ?></td>
						<td width="100"><?=$team_name_library[$row[csf('team_leader')]]; ?></td>
						<td width="100"><?=$team_member_name_library[$row[csf('dealing_marchant')]]; ?></td>
						<td width="100"><?=$buyer_short_name_library[$row[csf('buyer_name')]]; ?></td>
						<td width="100"><?=$sample_stage[$row[csf('sample_stage_id')]]; ?></td>
						<td width="100"><?=$row[csf('style_ref_no')]; ?></td>
						<td width="100"><a href='##' onclick="<?=$function; ?>"><?=$row[csf('requisition_number')]; ?></a></td>
						<td width="100"><?=$row[csf('copy_from')]; ?></td>
						<td width="110"><?=$color_name_library[$row[csf('color_name_id')]]; ?></td>
						<td width="110"><?=$color_name_library[$row[csf('fabric_color')]]; ?></td>
						<td width="70"><?=change_date_format($row[csf('labreqdate')]); ?></td>
						<td width="70"><?=change_date_format($row[csf('plandeliverydate')]); ?></td>
						<td width="70"><?=change_date_format($row[csf('recv_from_factory_date')]); ?></td>
						<td width="70"><?=change_date_format($row[csf('approval_status_date')]); ?></td>
						<td width="80"><?=$approval_status[$row[csf('approval_status')]]; ?></td>
						<td width="80"><?= $row[csf('lapdip_no')] ?></td>
						<td width="80"><?= $row[csf('applabdipno')] ?></td>
						<td width="60" align="center" title="<?= $row[csf('planleadtime')] ?>"><?=$planleadtime; ?></td>
						<td width="60" align="center"><?=$actionleadtime; ?></td>
						<td width="60" align="center"><?= $otd ?></td>
						<td align="center"><?= implode(", ",$sample_file_arr[$row[csf('id')]]) ?></td>
					</tr>  
					<?
					$i++;
				}
				?>
			</tbody>
        </table>
     </div>
     </fieldset>
     </div>
	<?
	//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows";
	exit();	
}