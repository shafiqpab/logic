<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_location")
{    	 
	echo create_drop_down( "cbo_location_name", 110, "select id,location_name from lib_location where company_id=$data and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All --", 0, "load_drop_down('requires/daily_purchase_requisition_report_controller', this.value+'_'+$data, 'load_drop_down_store','store_td');" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name", 110, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"-- All --",0,"");
	exit();
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 100,"select a.id,a.department_name from lib_department a, lib_division b where  b.company_id=$data and a.division_id=b.id and a.status_active=1 and b.status_active=1 group by a.id,a.department_name order by a.department_name ","id,department_name", 1, "-- All --", $selected, "" );
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_location=str_replace("'","",$cbo_location_name);
	$cbo_store=str_replace("'","",$cbo_store_name);
	$cbo_department=str_replace("'","",$cbo_department_name);
	$cbo_category=str_replace("'","",$cbo_category_name);
	$txt_req_no=trim(str_replace("'","",$txt_req_no));
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$store_arr=return_library_array("select id,store_name from lib_store_location where company_id='$cbo_company' and status_active=1",'id','store_name');

	$search_cond='';
	if($cbo_company){ $search_cond.=" and a.company_id=$cbo_company "; }
	if($cbo_location){ $search_cond.=" and a.location_id=$cbo_location "; }
	if($cbo_store){ $search_cond.=" and a.store_name=$cbo_store_name "; }
	if($cbo_department){ $search_cond.=" and a.department_id=$cbo_department "; }
	if($cbo_category){ $search_cond.=" and b.item_category=$cbo_category "; }
	if($txt_req_no!=''){ $search_cond.=" and a.requ_prefix_num='$txt_req_no' "; }

	if($date_from!='' && $date_to!='' )
	{
		if($db_type==0)
		{
			$from_date=change_date_format($date_from,'yyyy-mm-dd');
			$to_date=change_date_format($date_to,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$from_date=change_date_format($date_from,'','',-1);
			$to_date=change_date_format($date_to,'','',-1);
		}
		$search_cond.= " and a.requisition_date between '".$from_date."' and '".$to_date."'";
	}

	if($db_type==0){$itemCategory="group_concat(distinct(b.item_category)) ";}
	else{ $itemCategory="listagg(cast(b.item_category as varchar(4000)),',') within group(order by b.item_category) ";}

	$main_sql= "SELECT a.id as ID, a.requ_prefix_num as REQU_PREFIX_NUM, a.requisition_date as REQUISITION_DATE, a.department_id as DEPARTMENT_ID, a.store_name as STORE_NAME, a.req_by as REQ_BY, a.remarks as REMARKS,a.cbo_currency as CBO_CURRENCY, $itemCategory as ITEM_CATEGORY, sum(b.amount) as AMOUNT 
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=69 $search_cond 
	group by a.id, a.requ_prefix_num, a.requisition_date, a.department_id, a.store_name,a.req_by, a.remarks,a.cbo_currency
	order by a.department_id,a.id";
    $sql_currency = sql_select("select a.company_id, a.currency, a.conversion_rate from (select company_id, currency, max(con_date) as max_date from currency_conversion_rate where status_active = 1 and is_deleted = 0 and company_id = $cbo_company group by company_id, currency) b inner join currency_conversion_rate a on a.currency = b.currency and a.con_date = b.max_date and a.company_id = b.company_id order by a.company_id");
    $conversionRate = [];
    if(count($sql_currency) > 0){
        foreach ($sql_currency as $rate){
            $conversionRate[$rate[csf('currency')]] = $rate[csf('conversion_rate')];
        }
    }

//	 echo $main_sql;
	$data_result=sql_select($main_sql);
	$all_data_arr=array();
	foreach($data_result as $row)
	{
		$all_data_arr[$row['DEPARTMENT_ID']][]=$row;
	}

	$table_width=1360;
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>

	<body>
		<div style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="11">Purchase Requisition of <?=$company_arr[$cbo_company];?>: From : <?=$from_date;?> To : <?=$to_date;?> </th>
					</tr>
					<tr>
						<th width="40">SL No</th>
						<th width="100">Department</th>
						<th width="50">Req No</th>
						<th width="60">Req. Date</th>
						<th width="80">Requisition By</th>
						<th width="90">Amount</th>
						<th width="50">Currency</th>
						<th width="110">BDT. Amnt.</th>
						<th width="320">Required For</th>
						<th width="320">Item Category</th>
						<th >Store Name</th>
					</tr>
				</thead>	
				<tbody>
					<?
						$department_chk=array();
						$i=1;
                         $grand_req_amount = 0; $grand_bdt_amount = 0;
						foreach($all_data_arr as $department_key=>$department_val)
						{
							$total_req_amount=0;
                            $total_bdt_amount = 0;
							$department_rowspan=count($department_val);
							foreach($department_val as $row)
							{
								if($i%2==0){ $bgcolor="#E9F3FF"; } else{ $bgcolor="#FFFFFF"; }
								$item_category_arr=array_unique(explode(",",$row['ITEM_CATEGORY']));
								$category_name='';
								foreach($item_category_arr as $val)
								{
									$category_name.=$item_category[$val].', ';
								}
                                $conversionRateSingle = isset($conversionRate[$row['CBO_CURRENCY']]) ? $conversionRate[$row['CBO_CURRENCY']] : 1;
                                $bdt_amount = $row['AMOUNT']*$conversionRateSingle;
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<?= $i;?>" style="text-decoration:none; cursor:pointer">
										<td class="wrd_brk center"><?=$i;?></td>
										<?
											if(!in_array($department_key,$department_chk))
											{
												$department_chk[]=$department_key;
												?>
													<td rowspan="<?=$department_rowspan;?>" class="wrd_brk center" valign="middle"><? echo $department_arr[$department_key];?></td>
												<?
											}
										?>
										<td class="wrd_brk center" valign="middle"><a href='#report_detals' onclick= "openmypage('<?=$row['ID']?>','<?=$row['ITEM_CATEGORY']?>');"><?echo $row['REQU_PREFIX_NUM'];?></a> </td>
										<td class="wrd_brk center" valign="middle"><?echo change_date_format($row['REQUISITION_DATE']);?></td>
										<td valign="middle" class="wrd_brk"><?echo $row['REQ_BY'];?></td>
										<td valign="middle" class="wrd_brk right"><?echo number_format($row['AMOUNT'],2);?></td>
										<td valign="middle" class="wrd_brk center"><?echo $currency[$row['CBO_CURRENCY']];?></td>
                                        <td valign="middle" class="wrd_brk right"><?echo number_format($bdt_amount,2);?></td>
                                        <td valign="middle" class="wrd_brk"><?echo $row['REMARKS'];?></td>
										<td valign="middle" class="wrd_brk"><?echo rtrim($category_name,', ');?></td>
										<td valign="middle" class="wrd_brk"><?echo $store_arr[$row['STORE_NAME']];?></td>
									</tr>
								<?
								$i++;
								$total_req_amount+=$row['AMOUNT'];
                                $total_bdt_amount+=$bdt_amount;
							}
							?>
							<tr bgcolor="#A2A2A2">
								<td colspan="7" class="right"><strong><? echo $department_arr[$department_key];?> Department Total</strong> </td>
                                <td class="wrd_brk right"><strong><?echo number_format($total_bdt_amount,2);?></strong> </td>
								<td colspan="3"></td>
							</tr>
							<?
							$grand_req_amount+=$total_req_amount;
							$grand_bdt_amount+=$total_bdt_amount;
						}
						
					?>
					<tr bgcolor="#707070">
						<td colspan="7" class="right"><strong>Grand Total Amount</strong> </td>
                        <td class="wrd_brk right"><strong><?echo number_format($grand_bdt_amount,2);?></strong> </td>
                        <td colspan="4"></td>
					</tr>
				</tbody>	
				<tfoot>
					
				</tfoot>		
			</table>
		</div>
	</body>	
	<?
		
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

if ($action=="req_details")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$itemgroupArr = return_library_array("SELECT id,item_name from lib_item_group where status_active=1","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	$main_sql= "SELECT b.id,b.item_category as ITEM_CATEGORY, b.product_id as PROD_ID,b.quantity as QUANTITY,b.rate as RATE, b.amount as AMOUNT, b.remarks as REMARKS, c.item_group_id as ITEM_GROUP_ID, c.item_description as ITEM_DESCRIPTION, c.unit_of_measure as UOM,
	sum(case when d.transaction_type in (1,4,5) then d.cons_quantity else 0 end) as TOTAL_RECEIVE,
	sum(case when d.transaction_type in (2,3,6) then d.cons_quantity else 0 end) as TOTAL_ISSUE
	from inv_purchase_requisition_dtls b, product_details_master c
	left join inv_transaction d on c.id=d.prod_id and d.transaction_type in (1,2,3,4,5,6) and d.status_active=1
	where b.mst_id=$req_id and b.item_category in ($category_id) and b.product_id=c.id and b.status_active=1 and c.status_active=1
	group by b.id,b.item_category, b.product_id,b.quantity,b.rate , b.amount, b.remarks, c.item_group_id, c.item_description, c.unit_of_measure
	order by b.item_category";
	// echo $main_sql;die;
	$sql_result=sql_select($main_sql);

	$prod_id='';
	$all_data_dtls=array();
	foreach($sql_result as $row)
	{
		$prod_id.=$row['PROD_ID'].',';
		$all_data_dtls[$row['ITEM_CATEGORY']][]=$row;
	}
	$prod_id=rtrim($prod_id,',');

	$last_inv_id=sql_select("SELECT max(a.id) as ID,a.prod_id from inv_transaction a where a.transaction_type=1 and a.prod_id in ($prod_id) group by a.prod_id");
	
	foreach($last_inv_id as $row)
	{
		$last_inv_all.=$row['ID'].',';
	}
	$last_inv_all=rtrim($last_inv_all,',');

	$last_inv_sql="SELECT max(a.id) as ID,a.prod_id as PROD_ID,a.transaction_date as TRANSACTION_DATE, a.cons_quantity as CONS_QUANTITY, a.cons_rate as CONS_RATE,a.supplier_id as SUPPLIER_ID
	from inv_transaction a
	where a.transaction_type=1 and a.prod_id in ($prod_id) and a.id in($last_inv_all) and a.status_active=1 and a.is_deleted=0 
	group by a.prod_id,a.transaction_date, a.cons_quantity, a.cons_rate,a.supplier_id";
	// echo $last_inv_sql;die;
	$last_inv_result=sql_select($last_inv_sql);

	$last_inv_data=array();
	foreach($last_inv_result as $row)
	{
		$last_inv_data[$row['PROD_ID']]['transaction_date']=$row['TRANSACTION_DATE'];
		$last_inv_data[$row['PROD_ID']]['quantity']=$row['CONS_QUANTITY'];
		$last_inv_data[$row['PROD_ID']]['rate']=$row['CONS_RATE'];
		$last_inv_data[$row['PROD_ID']]['supplier_id']=$row['SUPPLIER_ID'];
	}
	
	?>
	<script>
		function new_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /></head><body>'+document.getElementById('popup_body').innerHTML+'</body</html>');
			d.close(); 		
		}
	</script>
    
    <table width="1200" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr><td align="center"><input type="button" class="formbutton" onClick="new_window()" style="width:100px;" value="Print" ></td></tr>
    </table><br>
    <div id="popup_body" style="width:820px;">
	<table width="1200" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <thead>
			<tr>
				<th colspan="14">Requisition Details</th>
			</tr>
        	<tr>
            	<th width="50">SL</th>
                <th width="100">Item Category</th>
                <th width="100">Item Group</th>
                <th width="100">Item Description</th>
                <th width="50">UOM</th>
                <th width="80">Quantity</th>
                <th width="80">Rate</th>
                <th width="80">Amount</th>
                <th width="80">Stock</th>
                <th width="80">Last Rec. Date</th>
                <th width="80">Last Rec. Qty.</th>
                <th width="80">Last Rate</th>
                <th width="100">Supplier</th>
                <th >Remarks</th>	
            </tr>
        </thead>
        <tbody>
        <?
		$i=1;$total_amount=0;
		$category_chk=array();
		foreach($all_data_dtls as $category_id=>$category_val)
		{
			$category_rowspan=count($category_val);
			foreach($category_val as $row)
			{
				if ($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td ><? echo $i; ?></td>
						<?
							if(!in_array($category_id,$category_chk))
							{
								$category_chk[]=$category_id;
								?>
									<td rowspan="<?=$category_rowspan;?>" class="wrd_brk center" valign="middle"><?echo $item_category[$category_id];?></td>
								<?
							}
						?>
						<td><? echo $itemgroupArr[$row["ITEM_GROUP_ID"]]; ?></td>
						<td><? echo $row["ITEM_DESCRIPTION"]; ?></td>
						<td><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
						<td align="right"><? echo number_format($row["QUANTITY"],2); ?></td>
						<td align="right"><? echo number_format($row["RATE"],2); ?></td>
						<td align="right"><? echo number_format($row["AMOUNT"],2); ?></td>
						<td align="right"><? echo number_format($row["TOTAL_RECEIVE"]-$row["TOTAL_ISSUE"],2); ?></td>
						<td align="center"><? echo change_date_format($last_inv_data[$row['PROD_ID']]['transaction_date']); ?></td>
						<td align="right"><? echo number_format($last_inv_data[$row['PROD_ID']]['quantity'],2); ?></td>
						<td align="right"><? echo number_format($last_inv_data[$row['PROD_ID']]['rate'],2); ?></td>
						<td ><? echo $supplierArr[$last_inv_data[$row['PROD_ID']]['supplier_id']]; ?></td>
						<td><? echo $row["REMARKS"]; ?></td>
					</tr>
				<?
				$total_amount+=$row["AMOUNT"];
				$i++;
			}
		}
		?>
		<tr bgcolor="#A2A2A2">
			<td colspan="7" align="right"><strong>Total Amount</strong></td>
			<td align="right"><strong><? echo number_format($total_amount,2); ?></strong></td>
			<td colspan="6"></td>
		</tr>
        </tbody>

	</table>
    </div>
	<?
	exit(); 
}
?>
