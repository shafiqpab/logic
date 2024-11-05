<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------


if ($action == "load_drop_down_store") {
	$data = explode("**", $data);

	if ($data[1] == 2)
	{
		$disable = 1;

	}
	else
	{
		$disable = 0;
	}

	echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] and b.category_type in(1)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "", $disable);
	exit();
}



if ($action == "generate_report")
{
	$process = array(&$_POST);
	//echo "test";die;
	//print_r($process);die;
	extract(check_magic_quote_gpc($process));
	$rpt_type=str_replace("'","",$rpt_type);
	//echo $cbo_store_name;//die;
	if($rpt_type==1)
	{
		$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
		$company_short_name_array = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
		$companyArr[0] = "All Company";
		
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$yarn_composition_arr = return_library_array("select id, composition_name from lib_composition_array", 'id', 'composition_name');
		
		$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
		$store_name_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		
	
		if ($db_type == 0) {
			
			$from_date = change_date_format($from_date, 'yyyy-mm-dd');
			$to_date = change_date_format($to_date, 'yyyy-mm-dd');
		} else if ($db_type == 2) {
			
			$from_date = change_date_format($from_date, '', '', 1);
			$to_date = change_date_format($to_date, '', '', 1);
			
		} else {
			$from_date = "";
			$to_date = "";
			
		}
		if($from_date != "" || $to_date != ""){
			$date_cond = " and transaction_date between '$from_date' and '$to_date' ";
		}
		
		

		if($cbo_store_name!=0){
			$store_name_cond=" and store_id in($cbo_store_name)"; 
		}else {
			$store_name_cond="";
		}

		$sql="select  sum(case when transaction_type=1 and entry_form=248 and transaction_date < '$from_date' then cons_quantity else 0 end) as opening_receive, 
			sum(case when transaction_type=2 and entry_form=277 and transaction_date < '$from_date' then cons_quantity else 0 end) as opening_issue 
			from inv_transaction where item_category=1 and entry_form in(248,277) and status_active=1 and is_deleted=0 and company_id=$cbo_company_name $store_name_cond 
		";
		$result=sql_select($sql);
		$opening_issue=0;
		$opening_receive=0;
		foreach ($result as $row) {
			$opening_issue=$row[csf('opening_issue')];
			$opening_receive=$row[csf('opening_receive')];
			break;
		}

		unset($sql);
		unset($result);

		// $sql="select transaction_date,sum(case when transaction_type=1 and entry_form=248 then cons_quantity else 0 end) as today_receive,sum(case when transaction_type=2 and entry_form=277  then cons_quantity else 0 end) as today_issue from inv_transaction where item_category=1 and entry_form in(248,277) and status_active=1 and is_deleted=0 and company_id=$cbo_company_name $store_name_cond  $date_cond
		// 	group by transaction_date 
		// 	order by transaction_date";

		$sql="select transaction_date,sum(case when transaction_type=1 and entry_form=248 then cons_quantity else 0 end) as today_receive,sum(case when transaction_type=2 and entry_form=277  then cons_quantity else 0 end) as today_issue from inv_transaction where item_category=1 and entry_form in(248,277) and status_active=1 and is_deleted=0 and company_id=$cbo_company_name $store_name_cond  $date_cond
			group by transaction_date
			order by transaction_date";

		$result=sql_select($sql);
		ob_start();
		?>

		<div style="width: 100%;" align="center">
			<p align="center"><strong>Yarn Stock Summary Report</strong><br>
			<strong>Company Name : <?php echo $companyArr[$cbo_company_name]; ?></strong><br>
			<strong>From <?echo $from_date;?> To <?echo $to_date;?></strong></p>
		</div>
		
		
	<div style="width: 100%;">
		<table width="80%" class="rpt_table" rules="all" border="1" id="data_table" style="word-break:break-all;margin:0 auto;" >
			<thead id="table_header_1">
				<tr>
					<th  align="center" >SL</th>
					<th  align="center" >Date</th>
					<th  align="center" >Opening (LBS)</th>
					<th  align="center" >Today Receive (LBS)</th>
					<th  align="center" >Total Receive (LBS)</th>
					<th  align="center" >Today Issue (LBS)</th>
					<th  align="center" >Day Closing (LBS)</th>
				</tr>
			</thead>
			<tbody id="scroll_body" class="rpt_table">
				<?php 
					$i=1;
					$today_total_receive=0;
					$today_total_issue=0;
					$opening=$opening_receive-$opening_issue;
					foreach ($result as $row) {
						$today_total_receive+=$row[csf('today_receive')];
						$today_total_issue+=$row[csf('today_issue')];
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FEFEFE";
						?>

						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td align="center"><?php echo $i++;?></td>
							<td align="center"><? echo $row[csf('transaction_date')];?></td>
							<td align="right"><? echo $opening;?></td>
							<td align="right">
								<? echo $row[csf('today_receive')];

								 ?>
							</td>
							<td align="right"><? 
								$opening+=$row[csf('today_receive')];
								echo $opening;

							 ?></td>
							<td align="right">
								<? 
								
								echo $row[csf('today_issue')]; ?>
									
							</td>
							<td align="right">
								<? 
								$opening-=$row[csf('today_issue')];
								echo $opening; 
								?>
							</td>
						</tr>
				<?php	}
				?>
				<tfoot>
					<tr bgcolor="#ecfa8b">
						<td align="right" colspan="3" style="text-align:right;"><strong>Today Rcvd & Issue Total:</strong> </td>
						<td align="right" id="today_total_receive" ><strong> <? echo  number_format($today_total_receive,2);?></strong></td>
						<td align="right" ></td>
						<td align="right" id="today_total_issue"><strong><? echo  number_format($today_total_issue,2);?></strong></td>
						<td></td>
						
						
					</tr>
				</tfoot>
		</table>
	</div>		
	
	<?php 

	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("$user_id*.xls") as $filename) {
                //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
            //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$rpt_type";
    exit();

}
	
		
    exit();
}

