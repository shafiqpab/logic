<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}


//--------------------------------------------------------------------------------------------

$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$supplier_arr=return_library_array( "select id, short_name from  lib_supplier",'id','short_name');
$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1",'id','yarn_count');





$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');


//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_item_cat=str_replace("'","",$cbo_item_cat);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

    $to_company_cond = "";$transfer_criteria_cond=$booking_cond="";
	if($cbo_transfer_type==1)
	{
		$from_caption="From Company";
		$to_caption="To Company";	
		$cbo_company_to= $cbo_company_to;
		$to_company_cond = " and a.to_company=$cbo_company_to";  
    }
        
    $transfer_criteria_cond = " and a.transfer_criteria = $cbo_transfer_type";
	if($db_type==0)
	{
		if($txt_date_from=="" || $txt_date_to=="")
		{
			$txt_date_con="";
		}
		else
		{
			$txt_date_con=" and a.transfer_date between '$txt_date_from' and '$txt_date_to'";
		}
	}
	if($db_type==2)
	{
		if($txt_date_from=="" || $txt_date_to=="")
		{
			$txt_date_con="";
		}
		else
		{
			$txt_date_con=" and a.transfer_date between '".change_date_format($txt_date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($txt_date_to,'dd-mm-yyyy','-',1)."'";
		}
	}

	if($txt_booking_no!='')
	{
		$booking_cond=" and d.sales_booking_no like '%$txt_booking_no%'";
	}
	$lib_item_group=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');
	$sql="SELECT 
		a.id,a.transfer_date,a.transfer_system_id as challan_no,a.company_id,a.to_company,
		b.yarn_lot,b.transfer_qnty,b.rate as rate,b.transfer_value as transfer_value,b.from_prod_id,b.fso_no,
		c.supplier_id,c.yarn_count_id,c.yarn_type,c.yarn_comp_type1st,c.yarn_comp_type2nd,c.item_category_id,c.item_group_id,c.item_description,c.unit_of_measure,
		d.sales_booking_no
	from 
		inv_item_transfer_mst a,
		product_details_master c,
		inv_item_transfer_dtls b 
	left join fabric_sales_order_mst d on b.fso_no=d.job_no and d.status_active=1 and d.is_deleted=0 
	where
		a.id=b.mst_id
		and b.from_prod_id=c.id
		and a.company_id=$cbo_company_from
		$to_company_cond $transfer_criteria_cond
		and b.item_category=$cbo_item_cat
		and a.status_active=1 
		and a.is_deleted=0
		$txt_date_con $booking_cond order by a.id
	";
	//  echo $sql;
	
	$result_array=sql_select($sql);
	foreach ($result_array as $rows)
	{ 
		$key=$rows[csf('transfer_date')].$rows[csf('yarn_lot')].$rows[csf('from_prod_id')];
		$data_arr[$key]=array(
			'transfer_date'	=>$rows[csf('transfer_date')],
			'challan_no'	=>$rows[csf('challan_no')],
			'yarn_lot'		=>$rows[csf('yarn_lot')],
			'company_id'	=>$rows[csf('company_id')],
			'to_company'	=>$rows[csf('to_company')],
			'from_prod_id'	=>$rows[csf('from_prod_id')],
			'item_category_id'	=>$rows[csf('item_category_id')],
			'item_group_id'		=>$rows[csf('item_group_id')],
			'item_description'	=>$rows[csf('item_description')],
			'unit_of_measure'	=>$rows[csf('unit_of_measure')],
			'supplier_id'	=>$rows[csf('supplier_id')],
			'yarn_count_id'	=>$rows[csf('yarn_count_id')],
			'yarn_type'		=>$rows[csf('yarn_type')],
			'composition'	=>$rows[csf('yarn_comp_type1st')]
			// 'fso_no'	=>$rows[csf('fso_no')],
		);
		$transfer_qnty_arr[$key]+=$rows[csf('transfer_qnty')];
		$transfer_rate_arr[$key]=$rows[csf('rate')];
		$transfer_val_arr[$key]+=$rows[csf('transfer_value')];
        $transfer_challan_arr[$key] .=$rows[csf('challan_no')].",";
        $transfer_order_no[$key] .=$rows[csf('fso_no')].",";
        $transfer_booking_no[$key] .=$rows[csf('sales_booking_no')].",";
		
		$from_prod_id_arr[$key][]=$rows[csf('from_prod_id')];

	}
	
	//var_dump($from_prod_id_arr);
	if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==5 || $cbo_item_cat==6 || $cbo_item_cat==7 || $cbo_item_cat==13 || $cbo_item_cat==23 )
	{
		$tbl_width=1400;
		$div_width='1418px';
	}
	else
	{	
		$tbl_width=1220;
		$div_width='1238px';
	}

	ob_start();
	?>
	<script type="text/javascript">
		$('.hide_td_header').hide();
		//$('.hide_td_header').fadeOut();
	</script>
	<div style="width:1200px;">
	<table id="tbl_headers">
		<tr>
			<td colspan="13" class="hide_td_header"><h2>Item Transfer Report </h2></td>
			
		</tr>
		<tr>
			<td colspan="12" class="hide_td_header"><strong>From Company  </strong></td>
			<td class="hide_td_header"> :<? echo $company_arr[$cbo_company_from];?>  </td>
			
		</tr>

		<tr>
			<td colspan="12" class="hide_td_header"><strong>To Company </strong></td>
			<td class="hide_td_header"> :<? echo $company_arr[$cbo_company_to];?>  </td>
			
		</tr>
		<tr>
			<td colspan="12" class="hide_td_header"><strong>Item Category </strong></td>
			<td class="hide_td_header"> :<? echo $item_category[$cbo_item_cat];?>  </td>
			
		</tr>

		<tr>
			<td colspan="13" class="hide_td_header"><strong>From Date</strong> :&nbsp;&nbsp;<? echo $txt_date_from;?>&nbsp;&nbsp;<strong>To Date</strong> :&nbsp;&nbsp;&nbsp;<? echo $txt_date_to;?></td>
			
		</tr>

	</table>
	<table width="<?=$tbl_width;?>" border="1" rules="all" class="rpt_table">
		<thead>
			<tr>
				<th width="35">SL</th>
				<th width="80">Transfer Date</th>
				<th width="80">System Challan</th>
				<?
					if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==5 || $cbo_item_cat==6 || $cbo_item_cat==7 || $cbo_item_cat==13 || $cbo_item_cat==23 )
					{
						?>
							<th width="80">Sales Order No</th>
							<th width="80">Fab. Booking No</th>
							<th width="50">Count</th>
							<th width="80">Composition</th>
							<th width="100">Type</th>
							<th width="100">Lot No.</th>
							<th width="100">Supplier</th>
						<?
					}
					else
					{
						?>
							<th width="100">Item Category</th>
							<th width="100">Item Group</th>
							<th width="150">Item Description</th>
							<th width="60">UOM</th>
						<?
					}
				?>

				<th width="100">Quantity</th>
				<th width="60">Rate </th>
				<th width="100">Total Value </th>
				<th class="hide_td" width="120"><? echo $from_caption;?></th>
				<th class="hide_td"><? echo $to_caption;?></th>
			</tr>
	</thead>
	</table>
			
	<div style=" max-height:360px; width:<?=$div_width;?>; overflow-y:scroll;" id="scroll_body">

		<table class="rpt_table" id="table_body" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
		
		<? 
		
		$i=1;
		foreach($data_arr as $key=>$rows){
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
			?> 
			<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				<td align="center" width="35"><? echo $i;?></td>
				<td width="80" align="center"><? echo change_date_format($rows['transfer_date']);?></td>
				<td width="80"><? echo chop($transfer_challan_arr[$key],",");//$rows['challan_no'];?></td>

				<?
					if($rows['item_category_id']==1 || $rows['item_category_id']==2 || $rows['item_category_id']==5 || $rows['item_category_id']==6 || $rows['item_category_id']==7 || $rows['item_category_id']==13 || $rows['item_category_id']==23 )
					{
						?>
							<td width="80" style="word-break:break-all;"><? echo implode(",",array_unique(explode(",",chop($transfer_order_no[$key],','))));?></td>
							<td width="80" style="word-break:break-all;"><? echo implode(",",array_unique(explode(",",chop($transfer_booking_no[$key],','))));?></td>
							<td width="50"><? echo $yarn_count_arr[$rows['yarn_count_id']];?></td>
							<td width="80"><? echo $composition[$rows['composition']];?></td>
							<td width="100"><p><? echo $yarn_type[$rows['yarn_type']];?></p></td>
							<td width="100"><p><? echo $rows['yarn_lot'];?></p></td>
							<td width="100"><p><? echo $supplier_arr[$rows['supplier_id']];?></p></td>
						<?
					}
					else
					{
						?>
							<td width="100"><p><? echo $item_category[$rows['item_category_id']];?></p></td>
							<td width="100"><p><? echo $lib_item_group[$rows['item_group_id']];?></p></td>
							<td width="150"><p><? echo $rows['item_description'];?></p></td>
							<td width="60" align="center"><p><? echo $unit_of_measurement[$rows['unit_of_measure']];?></p></td>
						<?
					}
				?>

				<td width="100" align="right"><? echo number_format($transfer_qnty_arr[$key],3); $totQty+=$transfer_qnty_arr[$key];?></td>
				<td width="60" align="right"><? echo number_format($transfer_rate_arr[$key],4);?></td>
				<td width="100" align="right"><?  $amu=$transfer_val_arr[$key]; $totAmu+=$amu; echo number_format($transfer_val_arr[$key],3);?></td>
				<td width="120" class="hide_td"><p><? echo $company_arr[$rows['company_id']];?></p></td>
				<td class="hide_td"><p><? echo $company_arr[$rows['to_company']];?></p></td>
			</tr>
		<? 
		$i++;
		}
		?>
		</table>
		<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
		<tfoot>
			<th width="35">&nbsp;</th>
			<th width="80">&nbsp;</th>
			<th width="80">&nbsp;</th>

			<?
				if($cbo_item_cat==1 || $cbo_item_cat==2 || $cbo_item_cat==5 || $cbo_item_cat==6 || $cbo_item_cat==7 || $cbo_item_cat==13 || $cbo_item_cat==23)
				{
					?>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">Total: </th>
					<?
				}
				else
				{
					?>
						<th width="100"></th>
						<th width="100"></th>
						<th width="150"></th>
						<th width="60">Total: </th>
					<?
				}
			?>
			<th width="100"><? echo number_format($totQty,3);?></th>
			<th width="60">&nbsp;</th>
			<th width="100"><? echo number_format($totAmu,2);?></th>
			<th class="hide_td" width="120">&nbsp;</th>
			<th class="hide_td">&nbsp;</th>
		</tfoot>
	</table>
	</div>



	</div>
	<?
	$html=ob_get_contents();	
	ob_end_clean();	
				
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html**$filename"; 
	exit();
		
}
disconnect($con);
?>

