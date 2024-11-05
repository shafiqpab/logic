<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------


if ($action=="load_supplier_dropdown")
{
	$ex_data = explode('_',$data);
	$company=$ex_data[0];
	$item_category=$ex_data[1];
	$supplier=$ex_data[2];
	
	echo create_drop_down( "cbo_supplier_id", 151,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(96) and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',$supplier,'',0);

	
	exit();
}

if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_supplier_id','0','0','','0');\n";
	exit();
}

$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
$team_leader_arr_library=return_library_array( "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0", "id", "team_leader_name"  );
$team_member_name=return_library_array( "select id, team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
$buyer_arr_library=return_library_array( "select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );
$supplier_arr_library=return_library_array( "select id, supplier_name from lib_supplier where status_active =1 and is_deleted=0", "id", "supplier_name"  );
$trim_group_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4",'id','item_name');



if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//cbo_company_id*cbo_supplier_id*txt_wo_no*cbo_section*txt_date_from*txt_date_to
	$company_name=str_replace("'","",$cbo_company_id);
	$cbo_supplier_id=str_replace("'","",$cbo_supplier_id);
	$section_id=str_replace("'","",$cbo_section);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$start_date=str_replace("'","",$txt_date_from);
	$end_date=str_replace("'","",$txt_date_to);
	
	if($section_id==0 || $section_id=='') $sql_cond.=""; else $sql_cond.=" and b.section=$section_id";
	if($company_name==0 || $company_name=='') $sql_cond.=""; else $sql_cond.=" and d.company_id=$company_name";
	if($cbo_supplier_id==0 || $cbo_supplier_id=='') $sql_cond.=""; else $sql_cond.=" and d.supplier_id in ($cbo_supplier_id)";
	if($txt_wo_no=='') $sql_cond.=""; else $sql_cond.=" and d.subcon_job like '%$txt_wo_no%'";
	
	if($start_date !='' && $end_date!='')
	{
		if($db_type==0) 
		{
			//( year_id=2019 and month_id>=1)  or  ( year_id=2020 and month_id<=1) or  ( year_id=2021 and month_id<=1) 
			$start_date=change_date_format($start_date,'yyyy-mm-dd');
			$end_date=change_date_format($end_date,'yyyy-mm-dd');
			$date_cond_order=" and d.wo_date between '$start_date' and '$end_date'";
		}
		if($db_type==2) 
		{
			$start_date=change_date_format($start_date,'','',1);
			$end_date=change_date_format($end_date,'','',1);
			$date_cond_order=" and d.wo_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		}
	}
	
	
	/*
	$rcv_qty_result=sql_select( "select a.order_rcv_id , a.wo_id , a.wo_dtls_id, a.wo_break_id, a.rcv_qty from trims_receive_dtls a  where entry_form =451 and a.status_active=1 and a.is_deleted=0");		
	foreach ($rcv_qty_result as  $row) 
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$rec_qty_arr[$row[csf("order_rcv_id")]][$row[csf("wo_break_id")]]["cum_qty"] +=$row[csf("rcv_qty")];
	}*/
	//and b.mst_id=c.wo_id
	$sql="select a.id, a.job_no_mst, a.qnty, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id as dtls_id ,b.order_quantity as wo_qnty, b.order_uom, b.section, b.sub_section, b.item_group  as trim_group , b.order_rcv_dtls_id, b.order_rcv_id, c.rcv_qty, b.remarks, d.id as wo_id, d.wo_date,d.supplier_id,d.subcon_job,d.order_rcv_no, d.exchange_rate  from  trims_subcon_ord_dtls b,  trims_subcon_ord_mst d , trims_subcon_ord_breakdown a  left join trims_receive_dtls c on  a.id=c.wo_break_id and c.status_active=1 and c.is_deleted=0
	 where d.id=b.mst_id and a.job_no_mst=d.subcon_job and a.job_no_mst=b.job_no_mst and b.id=a.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 $sql_cond $date_cond_order  group by a.id, a.job_no_mst, a.qnty, a.amount, a.order_rcv_break_id, a.order_rcv_id ,b.id  ,b.order_quantity, b.order_uom, b.section, b.sub_section, b.item_group , b.order_rcv_dtls_id, b.order_rcv_id, c.rcv_qty, b.remarks, d.id , d.wo_date,d.supplier_id,d.subcon_job,d.order_rcv_no,d.exchange_rate order by d.subcon_job ";

	$qry_result=sql_select($sql);

	foreach ($qry_result as  $row)
	{
		//echo $mstIDS.'=='.$ids.'=='.$book_con_dtls_ids.'++'; 
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["wo_qnty"] +=$row[csf("qnty")];
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["amount"] +=$row[csf("amount")];
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["rcv_qty"] +=$row[csf("rcv_qty")];
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["subcon_job"] =$row[csf("subcon_job")];
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["order_rcv_no"] =$row[csf("order_rcv_no")];
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["exchange_rate"] =$row[csf("exchange_rate")];
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["dtls_id"] .=$row[csf("dtls_id")].',';
		$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["remarks"] .=$row[csf("remarks")].',';
		//$wo_arr[$row[csf("wo_id")]][$row[csf("wo_date")]][$row[csf("supplier_id")]][$row[csf("section")]][$row[csf("sub_section")]][$row[csf("trim_group")]][$row[csf("order_uom")]]["wo_brk_id"] .=$row[csf("id")].',';
	}

	//echo "<pre>";
	//print_r($wo_arr); die;
	//die;

	$section_rowspan_arr=array(); $sub_section_rowspan_arr=array(); $trim_group_rowspan_arr=array(); $wo_id_rowspan_arr=array(); $wo_date_rowspan_arr=array(); $supplier_id_rowspan_arr=array();
    foreach($wo_arr as $wo_id=> $wo_id_data)
	{
		$wo_id_rowspan=0;
		foreach($wo_id_data as $wo_date=> $wo_date_data)
		{
			$wo_date_rowspan=0;	
			foreach($wo_date_data as $supplier_id=> $supplier_id_data)
			{
				$supplier_id_rowspan=0;
				foreach($supplier_id_data as $section_id=> $section_id_data)
				{
					$section_rowspan=0;	
					foreach($section_id_data as $sub_section_id=> $sub_section_data)
					{
						$sub_section_rowspan=0;	
						foreach($sub_section_data as $trim_group_id=> $trim_group_id_data)
						{
							$trim_group_rowspan=0;		
							foreach($trim_group_id_data  as $order_uom=> $row)
							{
								$section_rowspan++;
								$sub_section_rowspan++;
								$trim_group_rowspan++;
								$supplier_id_rowspan++;
								$wo_date_rowspan++;
								$wo_id_rowspan++;
							}
							$trim_group_rowspan_arr[$wo_id][$wo_date][$supplier_id][$section_id][$sub_section_id][$trim_group_id]=$trim_group_rowspan;
						}
						$sub_section_rowspan_arr[$wo_id][$wo_date][$supplier_id][$section_id][$sub_section_id]=$sub_section_rowspan;
					}
					$section_rowspan_arr[$wo_id][$wo_date][$supplier_id][$section_id]=$section_rowspan;
				}
				$supplier_id_rowspan_arr[$wo_id][$wo_date][$supplier_id]=$supplier_id_rowspan;
			}
			$wo_date_rowspan_arr[$wo_id][$wo_date]=$wo_date_rowspan;
		}
		$wo_id_rowspan_arr[$wo_id]=$wo_id_rowspan;
	}

//echo "<pre>";	
//print_r($wo_id_rowspan_arr);	die;
ob_start();
?>

<div style="width:1100px;">
	<table align="center" cellspacing="0" width="1060"  border="0" >
		<tr><td style="font-size:large" align="center"><strong><? echo $company_library[$company_name];?></strong></td></tr>
		<? if($start_date !='' && $end_date!='') {
			?><tr><td style="font-size:large" align="center"> <? echo $start_date.' To '.$end_date;?></td></tr><?
		}
		?>
		
	</table>
	<br>
	<table align="left" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table"  >
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="110">WO No.</th>
				<th width="100">Order Rcv.NO</th>
				<th width="60">WO Date</th>
				<th width="110">Supplier</th>
        		<th width="110">Section</th>
                <th width="110" >Sub Section</th>
                <th width="110">Trims Group</th>
                <th width="60">WO UOM</th>
                <th width="60">WO Qty</th>
                <th width="60">Rate (Taka)</th>
                <th width="70">Amount</th>
                <th width="70">Delivered Qnty</th>
                <th width="70">Balance Qty</th>
                <th >Remarks</th>
        	</tr>
		</thead>
	</table>
	<div style="width:<? echo 1218;?>px; max-height:500px; float:left; overflow-y:scroll;" id="scroll_body">
		<table align="left" cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
			<tbody>
				<?
				$tblRow=1; $i=1;
				foreach($wo_arr as $wo_id=> $wo_id_data)
				{
					$wo_id_rowspan=0;
					foreach($wo_id_data as $wo_date=> $wo_date_data)
					{
						$wo_date_rowspan=0;	
						foreach($wo_date_data as $supplier_id=> $supplier_id_data)
						{
							$supplier_id_rowspan=0;
							foreach($supplier_id_data as $section_id=> $section_id_data)
							{
								$section_rowspan=0;	
								foreach($section_id_data as $sub_section_id=> $sub_section_data)
								{
									$sub_section_rowspan=0;	
									foreach($sub_section_data as $trim_group_id=> $trim_group_id_data)
									{
										$trim_group_rowspan=0;		
										foreach($trim_group_id_data  as $order_uom=> $row)
										{
											$wo_qnty=$row['wo_qnty'];
											$amount=$row['amount']*$row['exchange_rate'];
											$rcv_qty=$row['rcv_qty']; 
											$rate=$amount/$wo_qnty;
											$balance=$wo_qnty-$rcv_qty;
											$grand_tot_amt+=$amount;
											$dtls_id=chop($row['dtls_id'],',');
											$remarks=chop($row['remarks'],',');
											if ($tblRow % 2 == 0)
					      						$bgcolor = "#E9F3FF";
					      					else
					      						$bgcolor = "#FFFFFF";
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $tblRow; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $tblRow; ?>">
												<? if($wo_id_rowspan==0){ 
													?> 	<td style="word-break:break-all" width="30" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"><?  echo $tblRow ; ?></td>
														<td style="word-break:break-all" width="110" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>" align="center"><a href='##' style='color:#000' onClick="print_report('<? echo $company_name.'*'.$wo_id.'*'."Sub Con Work Order Entry";?>','trims_subcon_wo_print', '../../marketing/requires/sub_con_wo_entry_controller')"><font color="blue"><strong><? echo $row['subcon_job']; ?></strong></font></a></td>

														<td style="word-break:break-all" width="100" rowspan="<? echo $wo_id_rowspan_arr[$wo_id]; ?>"><? echo $row['order_rcv_no']; ?></td>
													<? }
													?>
												
												<? if($wo_date_rowspan==0){ ?> <td style="word-break:break-all" width="60" rowspan="<? echo $wo_date_rowspan_arr[$wo_id][$wo_date]; ?>"><?  echo change_date_format($wo_date) ; ?></td><? } ?>
												<? if($supplier_id_rowspan==0){ ?> <td style="word-break:break-all" width="110" rowspan="<? echo $supplier_id_rowspan_arr[$wo_id][$wo_date][$supplier_id]; ?>"><?  echo $supplier_arr_library[$supplier_id] ; ?></td><? } ?>
												<? if($section_rowspan==0){ ?> <td style="word-break:break-all" width="110" rowspan="<? echo $section_rowspan_arr[$wo_id][$wo_date][$supplier_id][$section_id]; ?>"><?  echo $trims_section[$section_id] ; ?></td><? } ?>
												
												<? if($sub_section_rowspan==0){ ?> <td style="word-break:break-all" width="110" rowspan="<? echo $sub_section_rowspan_arr[$wo_id][$wo_date][$supplier_id][$section_id][$sub_section_id]; ?>"><?  echo $trims_sub_section[$sub_section_id] ; ?></td><? } ?>
												<? if($trim_group_rowspan==0){ ?> <td style="word-break:break-all" width="110" rowspan="<? echo $trim_group_rowspan_arr[$wo_id][$wo_date][$supplier_id][$section_id][$sub_section_id][$trim_group_id]; ?>"><?  echo $trim_group_arr[$trim_group_id] ; ?></td><? } ?>
												
												<td style="word-break:break-all" width="60"><?  echo $unit_of_measurement[$order_uom] ; ?></td>
												<td style="word-break:break-all" width="60" align="right"><?  echo number_format($wo_qnty,2) ; ?></td>
												<td style="word-break:break-all" width="60" align="right"><?  echo number_format($rate ,4); ?></td>
												<td style="word-break:break-all" width="70" align="right"><?  echo number_format($amount,2) ; ?></td>
												<td style="word-break:break-all" width="70" align="right"><?  echo number_format($rcv_qty,2) ; ?></td>
												<td style="word-break:break-all" width="70" align="right"><?  echo number_format($balance,2) ; ?></td>
												<td style="word-break:break-all" align="center" ><a href="##" onclick="fnc_remarks('<? echo $dtls_id ;?>','remarks_popup')"><? if($remarks!='') echo 'View';?></a></td>
											</tr>
											<?
											$tblRow++; $section_rowspan++; $sub_section_rowspan++; $trim_group_rowspan++; $supplier_id_rowspan++; $wo_date_rowspan++; $wo_id_rowspan++;
										}
									}
								}
							}
						}
					}
				}
				?>
			</tbody>
			
		</table>
		
	</div>
	<table align="left" cellspacing="0" width="1210"  border="1" rules="all" class="rpt_table" >
			<tfoot>
				<tr>
					<th width="928" align="right"><strong>G.Total:</strong></th>
					<th width="70" align="right"><p><strong><? echo number_format($grand_tot_amt,2) ; ?></strong></p></th>
					<th >&nbsp;</th>
				</tr>
			</tfoot>
		</table>
</div>

<?
	/*foreach (glob("$user_id*.xls") as $filename) 
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
	echo "$total_data####$filename";
	
	exit();*/


	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
    exit();	
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script type="text/javascript">
	function fnc_close()
	{
		parent.emailwindow.hide();
	}
	</script>

    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
        <tbody>
		<?
		$details_sql="select b.id as dtls_id , b.remarks from trims_subcon_ord_dtls b where b.id in(".chop($ids,',').") and b.status_active=1 and b.is_deleted=0 ";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><p><? echo $t; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
				<tr>
					<td align="center" colspan="2" align="center"><input type="button" name="main_close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" /></td>
				</tr>
			</tfoot>
    </table>
    <?
}