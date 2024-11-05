<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$sample_array=return_library_array( "select id,sample_name from lib_sample order by sample_name","id","sample_name");
if($db_type==0)
{
	$fabric_desc_details=return_library_array( "select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}
else
{
	$fabric_desc_details=return_library_array( "select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}

$costing_per_id_library=array(); $costing_date_library=array();
$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
foreach($costing_sql as $row)
{
	$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')]; 
	$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
}


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	if($cbo_buyer_name==0) $buyer_name="%%"; else $buyer_name=$cbo_buyer_name;
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$str_cond="and a.booking_date  between '$txt_date_from' and '$txt_date_to'";
		}
		else
		{
			$str_cond="";
		}
	}
	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$str_cond="and a.booking_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else
		{
			$str_cond="";
		}
	}
	
	//echo $str_cond;die;
	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="2250">
		<tr>
		   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_library[$cbo_company_name]; ?></strong></td>
		</tr>
	</table>
	<table class="rpt_table" border="1" rules="all" width="2250" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th colspan="4">Booking Details</th>
				<th colspan="3">Yarn Details</th>
				<th colspan="6">Grey Fabric Status</th>
				<th colspan="8">Finish Fabric Status</th>
			</tr>
			<tr>
				<th width="40">SL</th>
				<th width="130">Sample Fabric Booking No</th>
				<th width="140">Sample Type</th>
                <th width="80">Buyer Name</th>
				<th width="110">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                <th width="110">Issued</th>
                <th width="110">Balance<br/><font style="font-size:9px; font-weight:100">Grey Req-Yarn Issue</font></th>
				<th width="110">Knitted Production</th>
                <th width="100">Knitted Receive</th>
                <th width="100">Total Available</th>
				<th width="110">Knit Balance</th>
				<th width="110">Grey Issue</th>
				<th width="110">Batch Qnty</th>
				<th width="110">Fabric Color</th>
				<th width="110">Required</th>
				<th width="110">Dye Qnty</th>
				<th width="110">Fab Receive</th>
                <th width="110">Fab Production</th>
                <th width="110">Total Available</th>
				<th width="110">Balance</th>
				<th>Issue to Cutting </th>
			</tr>
		</thead>
	</table>
	<div style="width:2270px; overflow-y:scroll; max-height:300px">
    <table class="rpt_table" border="1" rules="all" width="2250" cellpadding="0" cellspacing="0" id="table_body">
    <?
	//echo $sql;die;
	
	$sql_yarn_issue=sql_select("select a.booking_id,a.booking_no,sum(b.cons_quantity) as issue_qty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.item_category=1 and b.transaction_type in(2,3) and a.booking_id>0 group by a.booking_id,a.booking_no order by a.booking_id");
	
	foreach($sql_yarn_issue as $row)
	{
		$yarn_issue_arr[$row[csf("booking_id")]]+=$row[csf("issue_qty")];
	}
	//var_dump($yarn_issue_arr);die;
	$sql_grey_knit_production=sql_select("select a.booking_id,a.booking_no,sum(b.grey_receive_qnty) as receive_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.receive_basis=1 and a.entry_form=2 and a.booking_id>0  group by a.booking_id,a.booking_no");
	
	foreach($sql_grey_knit_production as $row)
	{
		$grey_knit_production_arr[$row[csf("booking_id")]]+=$row[csf("receive_qty")];
	}
	
	$sql_grey_knit_receive=sql_select("select a.booking_id,a.booking_no,sum(b.cons_quantity) as receive_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2,4,6) and a.entry_form=22 and b.transaction_type in(1,4) and a.booking_id>0 group by a.booking_id,a.booking_no");
	
	foreach($sql_grey_knit_receive as $result)
	{
		$grey_knit_receive_arr[$result[csf("booking_id")]]+=$result[csf("receive_qty")];
	}
	
	$sql_grey_issue=sql_select("select a.booking_id,a.booking_no,sum(b.cons_quantity) as issue_qty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.entry_form=16 and b.transaction_type=2  group by a.booking_id,a.booking_no");
	foreach($sql_grey_issue as $result)
	{
		$grey_issue_arr[$result[csf("booking_id")]]+=$result[csf("issue_qty")];
	}
	
	$sql_batch_qty=sql_select("select a.id,a.batch_no,a.booking_no_id,a.color_id ,a.booking_no,sum(b.batch_qnty) as batch_qnty from  pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.booking_without_order=1 group by a.id,a.batch_no,a.booking_no_id ,a.booking_no,a.color_id ");
	//echo $sql_batch_qty;die;
	foreach($sql_batch_qty as $row)
	{
		$batch_qty_arr[$row[csf("booking_no_id")]]['batch_qnty']+=$row[csf("batch_qnty")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_qnty']=$row[csf("batch_qnty")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_id']=$row[csf("id")];
	}
	//var_dump($batch_qty_arr_check);die;
	$sql_dyeing_qty=sql_select("select id,batch_id,batch_no from pro_fab_subprocess where load_unload_id=2");
	foreach($sql_dyeing_qty as $row)
	{
		$dyeing_check_arr[$row[csf("batch_id")]]=$row[csf("id")];
	}
	//var_dump($dyeing_check_arr[2385]);die;
	
	$sql_finish_product=sql_select("select c.booking_no_id,c.booking_no,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b,  pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.receive_basis=5 and a.entry_form=7 and c.booking_no_id>0 group by c.booking_no_id,c.booking_no,b.color_id");
	foreach($sql_finish_product as $row)
	{
		$finish_product_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]=$row[csf("receive_qty")];
	}
	
	$sql_finish_receive=sql_select("select a.booking_id,a.booking_no,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.receive_basis in(1,2,4,6) and a.entry_form=37  group by a.booking_id,a.booking_no,b.color_id");
	
	foreach($sql_finish_receive as $row)
	{
		$finish_receive_arr[$row[csf("booking_id")]][$row[csf("color_id")]]+=$row[csf("receive_qty")];
	}
	
	$sql_cutting_issue=sql_select("select c.booking_no_id,c.booking_no,c.color_id,sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0  group by c.booking_no_id,c.booking_no,c.color_id");
	foreach($sql_cutting_issue as $row)
	{
		$issue_to_cut_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("issue_qty")];
	}
	
		$sql=sql_select("select a.id as booking_id, a.booking_no,a.buyer_id,a.company_id,a.supplier_id,a.item_category,a.is_approved,b.sample_type,b.grey_fabric as grey_fabric_qnty, b.finish_fabric as finish_fabric_qty, b.fabric_color from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.company_id like '$cbo_company_name' and a.buyer_id like '$buyer_name' $str_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.booking_no");
		foreach($sql as $row)
		{
			$result_mst_array[$row[csf("booking_id")]]["booking_id"]=$row[csf("booking_id")];
			$result_mst_array[$row[csf("booking_id")]]["booking_no"]=$row[csf("booking_no")];
			$result_mst_array[$row[csf("booking_id")]]["buyer_id"]=$row[csf("buyer_id")];
			$result_mst_array[$row[csf("booking_id")]]["company_id"]=$row[csf("company_id")];
			$result_mst_array[$row[csf("booking_id")]]["supplier_id"]=$row[csf("supplier_id")];
			$result_mst_array[$row[csf("booking_id")]]["item_category"]=$row[csf("item_category")];
			$result_mst_array[$row[csf("booking_id")]]["is_approved"]=$row[csf("is_approved")];
			$result_mst_array[$row[csf("booking_id")]]["sample_type"].=$row[csf("sample_type")].",";
			$result_mst_array[$row[csf("booking_id")]]["grey_fabric_qnty"]+=$row[csf("grey_fabric_qnty")];
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_color"]=$row[csf("fabric_color")];
			$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["finish_fabric_qty"]=$row[csf("finish_fabric_qty")];
		}
		
		//var_dump($result_mst_array);die;
		
		//echo $sql;die;
		
		//$result=sql_select($sql);

	//echo $sql_batch_qty;die;
	//var_dump($result);die;
	?>
        <tbody>
        <?
		$i=1;
		foreach($result_mst_array as $row)
		{
			?>
        	<tr>
				<td width="40"><? echo $i; ?></td>
				<td width="130"><? echo $row[("booking_no")]; ?></td>
				<td width="140">
				<?
					$sample_arr=array_unique(explode(",",substr($row[("sample_type")], 0, -1))); 
					$p=1;
					foreach($sample_arr as $row_style)
					{
						if($p!=1) echo "<br>";
						echo $sample_array[$row_style];
						$p++;
					}
				?>
                </td>
                <td width="80"><? echo $buyer_short_name_library[$row[("buyer_id")]]; ?></td>
				<td width="110" align="right"><? echo number_format($row[("grey_fabric_qnty")],2); ?></td>
                <td width="110" align="right"><? echo number_format($yarn_issue_arr[$row[("booking_id")]],2); ?></td>
                <td width="110" align="right"><? $yarn_balance=$row[("grey_fabric_qnty")]-$yarn_issue_arr[$row[("booking_id")]]; echo number_format($yarn_balance,2); ?></td>
				<td width="110" align="right"><? echo number_format($grey_knit_production_arr[$row[("booking_id")]],2);?></td>
                <td width="100" align="right"><? echo number_format($grey_knit_receive_arr[$row[("booking_id")]],2);?></td>
                <td width="100" align="right"><? $grey_total_available=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]);  echo number_format($grey_total_available,2);  ?></td>
				<td width="110" align="right"><? $grey_balance=$row[("grey_fabric_qnty")]-$grey_total_available; echo number_format($grey_balance,2); ?></td>
				<td width="110" align="right"><? echo number_format($grey_issue_arr[$row[("booking_id")]],2); ?></td>
				<td width="110" align="right"><? $batch_qt=$batch_qty_arr[$row[("booking_id")]]['batch_qnty']; echo number_format($batch_qt,2); ?></td>
                <?
				//details_part start here
				$m=1;
				foreach($result_dtls_array[$row[("booking_id")]] as $dts_row)
				{
					if($m==1)
					{
						?>
						<td width="110">&nbsp;<? echo $color_array[$dts_row[("fabric_color")]]; ?> </td>
						<td width="110" align="right"><? echo number_format($dts_row[("finish_fabric_qty")],2); ?></td>
						<td width="110" align="right">
						<?
						$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
						$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
						if($dy_check_id!="")  echo number_format($dtls_batch_qty,2); 
						 ?></td>
						<td width="110" align="right"><? echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); ?></td>
						<td width="110" align="right"><? echo number_format($finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); ?></td>
						<td width="110" align="right"><? $finish_total_available=($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[csf("booking_id")]][$dts_row[("fabric_color")]]); echo number_format($finish_total_available,2); ?></td>
						<td width="110" align="right"><? $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); ?></td>
						<td align="right"><? echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); ?></td>
					</tr>
					<?
					}
					else
					{
						?>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="130">&nbsp;</td>
                            <td width="140">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="100" align="right">&nbsp;</td>
                            <td width="100" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110">&nbsp;<? echo $color_array[$dts_row[("fabric_color")]]; ?> </td>
                            <td width="110" align="right"><? echo number_format($dts_row[("finish_fabric_qty")],2); ?></td>
                            <td width="110" align="right">
                            <?
							$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
							$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
							if($dy_check_id!="")  echo number_format($dtls_batch_qty,2);  
                             ?>
                             </td>
                            <td width="110" align="right"><? echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); ?></td>
                            <td width="110" align="right"><? echo number_format($finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); ?></td>
                            <td width="110" align="right"><? $finish_total_available=($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[csf("booking_id")]][$dts_row[("fabric_color")]]); echo number_format($finish_total_available,2); ?></td>
                            <td width="110" align="right"><? $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); ?></td>
                            <td align="right"><? echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); ?></td>
                        </tr>
                        <?
					}
				$m++;
				}
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
     <?
	foreach (glob("$user_name*.xls") as $filename) 
	{
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$tot_rows";
	exit();
}



?>