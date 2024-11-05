<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action==='report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id      = str_replace("'","",$cbo_company_id);
	$cbo_item_category_id= str_replace("'","",$cbo_item_category_id);
	$cbo_search_by       = str_replace("'","",$cbo_search_by);
	$txt_search_common   = trim(str_replace("'","",$txt_search_common));
	$txt_date_from       = str_replace("'","",$txt_date_from);
	$txt_date_to         = str_replace("'","",$txt_date_to);

	$company_cond=$item_category_cond='';
	if ($cbo_company_id != 0) $company_cond=" and a.importer_id in($cbo_company_id)";
	if ($cbo_item_category_id != 0) $item_category_cond =" and d.item_category_id in($cbo_item_category_id)";

	$date_cond=$search_cond='';
	if ($txt_search_common != '')
	{
		if ($cbo_search_by==1) $search_cond =" and a.con_system_id like '%$txt_search_common%'";
		else if($cbo_search_by==2) $search_cond =" and a.pi_number like '%$txt_search_common%'";
		else $search_cond =" and f.lc_number like '%$txt_search_common%'";
	}
	else
	{
		$date_cond=="";
		if ($txt_date_from != '' && $txt_date_to != '')	
		{
			if ($db_type==0) {			
				$txt_date_from = date('Y-m-d', strtotime($txt_date_from));
				$txt_date_to   = date('Y-m-d', strtotime($txt_date_to));
			} else {
				$txt_date_from = date('d-M-Y', strtotime($txt_date_from));
				$txt_date_to = date('d-M-Y', strtotime($txt_date_to));
			}

			$date_cond = " and a.office_note_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}
	}
	//echo $date_cond.'**'.$search_cond;die;
	$company_library=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_library=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$section_library=return_library_array( "select id, section_name from lib_section where status_active=1 and is_deleted=0",'id','section_name');

	if ($cbo_search_by != 3) // 
	{
	    $sql="SELECT a.id, a.con_system_id, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.pi_id, a.pi_number, a.section, a.remarks, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.pi_date, b.amount as pi_value, c.id as pi_dtls_id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year, d.item_category_id, f.lc_number
		FROM commercial_office_note_mst a, commercial_office_note_dtls b, com_pi_item_details c, com_pi_master_details d 
		left join com_btb_lc_pi e on d.id=e.pi_id and e.is_deleted=0 and e.status_active=1 
		left join com_btb_lc_master_details f on e.com_btb_lc_master_details_id=f.id and f.is_deleted=0 and f.status_active=1 
		WHERE a.id=b.mst_id and b.pi_dtls_id=c.id and c.pi_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond $item_category_cond $search_cond $date_cond
		group by a.id, a.con_system_id, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.pi_id, a.pi_number, a.section, a.remarks, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.pi_date, b.amount, c.id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year, d.item_category_id,f.lc_number
		order by a.office_note_date desc";
	} 
	else 
	{		
		$sql="SELECT a.id, a.con_system_id, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.pi_id, a.pi_number, a.section, a.remarks, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.pi_date, b.amount as pi_value, c.id as pi_dtls_id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year, d.item_category_id, f.lc_number
		FROM commercial_office_note_mst a, commercial_office_note_dtls b, com_pi_item_details c, com_pi_master_details d, com_btb_lc_pi e, com_btb_lc_master_details f  
		WHERE a.id=b.mst_id and b.pi_dtls_id=c.id and c.pi_id=d.id and d.id=e.pi_id and e.com_btb_lc_master_details_id=f.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $company_cond $item_category_cond $search_cond $date_cond
		group by a.id, a.con_system_id, a.lc_type, a.office_note_date, a.importer_id, a.tenor, a.pi_id, a.pi_number, a.section, a.remarks, b.yarn_composition_percentage1, b.yarn_composition_percentage2, b.yarn_composition_item1, b.yarn_composition_item2, b.body_part_id, b.fab_type, b.fabric_construction, b.fab_design, b.fabric_composition, b.item_description, d.internal_file_no, d.supplier_id, d.pi_date, b.amount, c.id, c.work_order_no, c.work_order_dtls_id, c.item_group, d.file_year, d.item_category_id,f.lc_number
		order by a.office_note_date desc";
	}
	
	$sql_res=sql_select($sql);
	foreach ($sql_res as $row) 
	{
		$office_note_arr[$row[csf('con_system_id')]]['importer_id'] = $row[csf('importer_id')];	
		$office_note_arr[$row[csf('con_system_id')]]['office_note_date'] = $row[csf('office_note_date')];
		if ($row[csf('internal_file_no')] != "") $office_note_arr[$row[csf('con_system_id')]]['internal_file_no'] .= $row[csf('internal_file_no')].',';
		$office_note_arr[$row[csf('con_system_id')]]['supplier_id'] = $row[csf('supplier_id')];
		$office_note_arr[$row[csf('con_system_id')]]['item_category_id'] .= $item_category[$row[csf('item_category_id')]].',';
		$office_note_arr[$row[csf('con_system_id')]]['pi_number'] = $row[csf('pi_number')];
		$office_note_arr[$row[csf('con_system_id')]]['pi_date'] .= change_date_format($row[csf('pi_date')]).',';
		$office_note_arr[$row[csf('con_system_id')]]['pi_value'] += $row[csf('pi_value')];
		$office_note_arr[$row[csf('con_system_id')]]['section'] = $row[csf('section')];
		$office_note_arr[$row[csf('con_system_id')]]['lc_type'] = $row[csf('lc_type')];
		if ($row[csf('lc_number')] != "") $office_note_arr[$row[csf('con_system_id')]]['lc_number'] .= $row[csf('lc_number')].',';			
		$office_note_arr[$row[csf('con_system_id')]]['remarks'] = $row[csf('remarks')];	
	}
	

	$width=1460;
	ob_start();
	?>
	<style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
	<div width="<?= $width; ?>">
		<table width="<?= $width; ?>" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="14" align="center" width="<?= $width; ?>"><p style="font-weight:bold; font-size:20px">Date wise Commercial Office Note</p>
				</td>
			</tr>				
		</table>
		<table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" style="margin-left: 2px;">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="130">Company Name</th>
	                <th width="130">Office Note No</th>
					<th width="80">Office Note Date</th>
	                <th width="100">File No</th>
	                <th width="120">Supplier Name</th>
	                <th width="120">Item Category</th>
	                <th width="120">PI No</th>
	                <th width="120">PI Date</th>
	                <th width="80">PI Value</th>
	                <th width="80">Section</th>
	                <th width="80">L/C Type</th>
	                <th width="120">LC Number</th>
	                <th width="150">Remarks</th>
				</tr>
			</thead>
		</table>

		<div style="width:<?= $width+20; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body">			
		    <table class="rpt_table" border="1" rules="all" width="<?= $width; ?>" cellpadding="0" cellspacing="0" id="table_body">
		        <tbody>
		        	<?
		        	$i=1;
		        	foreach ($office_note_arr as $system_id => $row) 
		        	{
		        		$pi_date=implode(',',array_unique(explode(',', rtrim($row['pi_date'],','))));
		        		$internal_file_no=implode(',',array_unique(explode(',', rtrim($row['internal_file_no'],','))));
		        		$item_category=implode(',',array_unique(explode(',', rtrim($row['item_category_id'],','))));
		        		$lc_number=implode(',',array_unique(explode(',', rtrim($row['lc_number'],','))));
		        		?>
			        	<tr bgcolor="<?= $bgcolor; ?>"  onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
			        		<td width="30" align="center"><p><?= $i; ?></p></td>			        	
			                <td width="130" class="wrd_brk"><p><?= $company_library[$row['importer_id']]; ?></p></td>
							<td width="130" class="wrd_brk"><p><?= $system_id; ?></p></td>
			                <td width="80" class="wrd_brk" align="center"><p><?= change_date_format($row['office_note_date']); ?></p></td>
			                <td width="100" class="wrd_brk"><p><?= $internal_file_no; ?></p></td>
			                <td width="120" class="wrd_brk"><p><?= $supplier_library[$row['supplier_id']]; ?></p></td>

			                <td width="120" class="wrd_brk"><p><?= $item_category; ?></p></td>
			                <td width="120" class="wrd_brk"><p><?= $row['pi_number']; ?></p></td>
			                <td width="120" class="wrd_brk"><p><?= $pi_date; ?></p></td>
			                <td width="80" class="wrd_brk" align="right"><p><?= number_format($row['pi_value'],2); ?>&nbsp;</p></td>
			                <td width="80" class="wrd_brk"><p><?= $section_library[$row['section']]; ?></p></td>
			                <td width="80" class="wrd_brk"><p><?= $lc_type[$row['lc_type']]; ?></p></td>
			                <td width="120" class="wrd_brk"><p><?= $lc_number; ?></p></td>
			                <td width="150" class="wrd_brk"><p><?= $row['remarks']; ?></p></td>
			        	</tr>
			        	<?
			        	$i++;
			        }
			        ?>	
		        </tbody>
		    </table>
		</div>
	</div>
	
	<?
	foreach (glob("$user_id*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}



