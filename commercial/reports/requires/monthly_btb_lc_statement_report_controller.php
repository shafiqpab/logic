<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];




//report generated here--------------------//
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	//var_dump($process );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_year=str_replace("'","",$txt_year);
	$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	$cbo_type=str_replace("'","",$cbo_type);
	$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
	
	if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;

	if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;
	if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;
	//if(trim($data[7])!="") $cbo_year2=$data[7];,lc_date

	if ($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond=" and d.lc_date between '$txt_date_from' and  '$txt_date_to'";
		
		$str_cond_con=" and d.contract_date between '$txt_date_from' and  '$txt_date_to'";
	}
	else
	{
		$str_cond="";
		$str_cond_con="";
	}
	

	if($txt_year!="")
	{
		$year_lc="and d.lc_year='$txt_year'";
		$year_sc="and d.sc_year='$txt_year'";
	}
	else
	{
		$year_lc="";
		$year_sc="";
	}

	if($txt_lc_sc_no == "") 
	{
		$txt_lc_no_cond="";
		$txt_sc_no_cond="";
	} 
	else 
	{
		$txt_lc_no_cond = " and d.export_lc_no like '%$txt_lc_sc_no%'";
		$txt_sc_no_cond = " and d.contract_no like '%$txt_lc_sc_no%'";
	}
	//============================
	// if($db_type==0)
	// {
	// 	$from_date=change_date_format($txt_date_from,"yyyy-mm-dd");
	// 	$to_date=change_date_format($txt_date_to,"yyyy-mm-dd");
	// }
	// else
	// {
	// 	$from_date=change_date_format($txt_date_from,"","",1);
	// 	$to_date=change_date_format($txt_date_to,"","",1);
	// }
	
	// $from_time=strtotime($from_date);
	// $to_time=strtotime($to_date);
	// // $from_time=strtotime($txt_date_from);
	// // $to_time=strtotime($txt_date_to);
	
	// $time_dev=strtotime($to_date)-strtotime($from_date);
	// $p=1;
	// for($i=$from_time;$i<=$to_time;$i=$i+86400)
	// {
	// 	$month_arr[date("m-Y",$i)]=date("M-y",$i);
	// 	$p++;
	// }
	//echo $p."<pre>";print_r($month_arr);die;


	//var_dump($txt_date_from);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	//$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	// if($cbo_type==0)
	// {
	// 	$sql="select b.id as btb_id, b.lc_number,d.export_lc_no as lc_sc_no, 0 as type
	// 	from com_btb_lc_master_details b, com_btb_export_lc_attachment c, com_export_lc d
	// 	where b.id=c.import_mst_id and c.lc_sc_id=d.id and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	// 	and  d.status_active=1 and d.is_deleted=0 and c.is_lc_sc=0 and b.importer_id like '$cbo_company_name' and d.lien_bank like '$cbo_lien_bank' $str_cond $year_lc $txt_lc_no_cond group by b.id , b.lc_number,d.export_lc_no
	// 	union all
	// 	select b.id as btb_id, b.lc_number,d.contract_no as lc_sc_no,1 as type
	// 	from com_btb_lc_master_details b, com_btb_export_lc_attachment c, com_sales_contract d
	// 	where b.id=c.import_mst_id and c.lc_sc_id=d.id and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc_sc=1 and b.importer_id like '$cbo_company_name' and d.lien_bank like '$cbo_lien_bank' $str_cond_con
	// 	$year_sc $txt_sc_no_cond group by b.id , b.lc_number,d.contract_no";
	// }
	if($cbo_type==1)
	{
		$sql="select b.id as btb_id,d.id as expoid,b.item_category_id,b.lc_date,b.lc_value, b.lc_number,b.supplier_id,b.payterm_id,d.export_lc_no as lc_sc_no,0 as type,d.lc_date as lsdate,d.lc_value as value
		from com_btb_lc_master_details b, com_btb_export_lc_attachment c, com_export_lc d
		where b.id=c.import_mst_id and c.lc_sc_id=d.id and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		and  d.status_active=1 and d.is_deleted=0 and c.is_lc_sc=0 and b.importer_id like '$cbo_company_name' and d.lien_bank like '$cbo_lien_bank' $str_cond $year_lc
		$txt_lc_no_cond";
	}
	else
	{
		$sql="select b.id as btb_id,b.item_category_id,b.lc_date,b.lc_value, b.lc_number,b.supplier_id,b.payterm_id,d.contract_no as lc_sc_no, 1 as type,d.contract_date as lsdate,d.contract_value as value
		from com_btb_lc_master_details b, com_btb_export_lc_attachment c, com_sales_contract d
		where b.id=c.import_mst_id and c.lc_sc_id=d.id and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc_sc=1 and b.importer_id like '$cbo_company_name' and d.lien_bank like '$cbo_lien_bank' $str_cond_con
		$year_sc $txt_sc_no_cond";
	}

	//echo $sql;
	$result=sql_select($sql);
	$btb_arr=array();
	foreach($result as $row)
	{
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["lc_number"]= $row[csf("lc_number")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["type"].= $row[csf("type")].",";
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["lc_sc_no"].= $row[csf("lc_sc_no")].",";
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["date"]= $row[csf("lsdate")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["lc_sc_value"]+= $row[csf("value")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["supplier_id"]= $row[csf("supplier_id")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["item_category_id"]= $row[csf("item_category_id")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["lc_date"]= $row[csf("lc_date")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["lc_value"]= $row[csf("lc_value")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["payterm_id"]= $row[csf("payterm_id")];
		$btb_arr[$row[csf("id")]][$row[csf("lc_number")]]["expoid"]= $row[csf("expoid")];

		$lc_number_arr[] = "'".$row[csf('lc_number')]."'";
		$expoid_arr[] = "'".$row[csf('expoid')]."'";
	}
	$lc_number_string = implode(',',array_unique($lc_number_arr));
	$expoid_arr_string = implode(',',array_unique($expoid_arr));
	//var_dump($expoid_arr);
	if($lc_number_string!="")
	{
		$import_payment_sql = "SELECT a.lc_number,c.accepted_ammount  
		FROM com_btb_lc_master_details a, com_import_invoice_mst b,com_import_payment c,com_import_payment_mst d 
		WHERE d.id=c.mst_id and c.invoice_id=b.id and b.btb_lc_id=a.id and  a.importer_id like '$cbo_company_name' and a.lc_number in($lc_number_string) and b.is_lc=1 and a.payterm_id !=1  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 
		group by a.lc_number,c.accepted_ammount ";
		//echo $import_payment_sql;
		$import_payment_result=sql_select($import_payment_sql);
		$import_payment_result_arr=array();
		foreach($import_payment_result as $row)
		{
			$import_payment_result_arr[$row[csf("lc_number")]]["accepted_ammount"]+= $row[csf("accepted_ammount")];
		}
		//var_dump($import_payment_result_arr);
		// $atsight_sql = "SELECT a.lc_number,c.accepted_ammount
		// FROM com_btb_lc_master_details a, com_import_invoice_mst b,com_import_payment_com c,com_import_payment_com_mst d 
		// WHERE d.id=c.mst_id and c.invoice_id=b.id and b.btb_lc_id=a.id and   a.importer_id like '$cbo_company_name' and a.lc_number in($lc_number_string) and b.is_lc=1 and a.payterm_id =1   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 
		// group by a.lc_number,c.accepted_ammount
		// union all
		// SELECT a.lc_number,c.accepted_ammount
		// FROM com_btb_lc_master_details a, com_import_invoice_mst b,com_import_payment_com c,com_import_payment_com_mst d 
		// WHERE d.id=c.mst_id and c.invoice_id=b.id and b.btb_lc_id=a.id and   a.importer_id like '$cbo_company_name' and a.lc_number in($lc_number_string) and b.is_lc=2   and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 
		// group by a.lc_number,c.accepted_ammount";

		$atsight_sql = "SELECT a.lc_number,c.accepted_ammount
		FROM com_btb_lc_master_details a, com_import_invoice_mst b,com_import_payment_com c,com_import_payment_com_mst d 
		WHERE d.id=c.mst_id and c.invoice_id=b.id and b.btb_lc_id=a.id and   a.importer_id like '$cbo_company_name' and a.lc_number in($lc_number_string) and b.is_lc=1 and a.payterm_id =1  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 
		group by a.lc_number,c.accepted_ammount";
		//echo $sql;
		$atsight_result=sql_select($atsight_sql);
		$atsight_result_arr=array();
		foreach($atsight_result as $row)
		{
			$atsight_result_arr[$row[csf("lc_number")]]["accepted_ammount"]+= $row[csf("accepted_ammount")];
		}
		//var_dump($atsight_result_arr);

	}

	if($expoid_arr_string!="")
	{
		$sql1 = "select a.id,a.com_export_lc_id, a.com_sales_contract_id, b.contract_no, b.contract_value,b.contract_date, a.replaced_amount, a.attched_btb_lc_id from com_export_lc_atch_sc_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.com_export_lc_id in($expoid_arr_string) and a.is_deleted = 0 and a.status_active=1 order by a.id";
		//echo $sql;
		$sql_result=sql_select($sql1);
		$attach_result_arr=array();
		foreach($sql_result as $row)
		{
			$attach_result_arr[$row[csf("com_export_lc_id")]]["contract_no"]= $row[csf("contract_no")];
			$attach_result_arr[$row[csf("com_export_lc_id")]]["contract_date"]= $row[csf("contract_date")];
			$attach_result_arr[$row[csf("com_export_lc_id")]]["contract_value"]= $row[csf("contract_value")];
		}
		//var_dump($attach_result_arr);
	}
	
	ob_start();	
	?>
	<div style="width:1800px;"> 
     
        
        <table width="700"  border="0" align="left">
        	<tr>
            	<td>&nbsp;</td>
            </tr>
        </table>
        <br >
        <table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <thead>
            	<th width="30">SL</th>
				<?if($cbo_type==1){?>
                <th width="130">LC No/SC No</th>
				<th width="90"> Date</th>
				<th width="80">LC Value (USD)</th>
				<? }else {?>	
				<th width="130">SC No</th>
				<th width="90"> Date</th>
				<th width="80">SC Value (USD)</th>
				<?}?>
                <th width="70">BBL/C NO.</th>
                <th width="200">Supplier Name</th>
                <th width="70">Date</th>
                <th width="70"> Value (USD)</th>
                <th width="70">MATURITE</th>
                <th width="80">YET NOT MATURITE</th>
                <th width="70">Jan</th>
                <th width="70">Feb</th>
                <th width="70">Mar</th>
                <th width="70">Apr</th>
                <th width="70">May</th>
                <th width="70">Jun</th>
                <th width="70">Jul</th>
                <th width="70">Aug</th>
                <th width="70">Sep</th>
                <th width="70">Oct</th>
                <th width="70">Nov</th>
                <th width="">Dec</th>
            </thead>
        </table>
		<div style="width:1800px; overflow-y:scroll; max-height:250px" id="scroll_body"> 
            <table width="1782" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left" id="table_body">
                <tbody>  
                <?
				
                $i=1; $tot_lc_value=0;
							
                foreach($btb_arr as $btb_id=> $btb_datum)
                {
					
					foreach ($btb_datum as $key => $value) {
				
						
						$supplier='';
						if($value['item_category_id']==110)
						{
							$supplier=$comp[$value["supplier_id"]];
						}
						else
						{
							$supplier=$supplier_lib[$value["supplier_id"]];
						}

						$maturite='';
						if($value['payterm_id']==1)
						{
							$maturite=$atsight_result_arr[$key]["accepted_ammount"];
						}
						else
						{
							$maturite=$import_payment_result_arr[$key]["accepted_ammount"];
						}

						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="130" style="word-break:break-all"><p>
								<?
								//echo $lc_sc_no=rtrim(implode(",",array_unique(explode(",",$value["lc_sc_no"]))), ',')
								if($cbo_type==1)
								{
									echo "L/C:".$lc_sc_no=rtrim(implode(",",array_unique(explode(",",$value["lc_sc_no"]))), ','). '<br>';
									echo "S/C:".$attach_result_arr[$value["expoid"]]["contract_no"];
								}
								else
								{
									echo $lc_sc_no=rtrim(implode(",",array_unique(explode(",",$value["lc_sc_no"]))), ',');
									
								}
									
								?>
							</p></td>
							<td width="90" align="center" style="word-break:break-all"><p>
								<? 
									if($cbo_type==1)
									{
										echo "L/C:".change_date_format($value["date"]). '<br>';
										echo "S/C:".change_date_format($attach_result_arr[$value["expoid"]]["contract_date"]); 
									}
									else
									{
										echo change_date_format($value["date"]). '<br>';
										
									}
									// echo "LC:".change_date_format($value["date"]). '<br>';
									// echo "SC:".change_date_format($attach_result_arr[$value["expoid"]]["contract_date"]); 
								?>
							</p></td>
							<td width="80" align="left" style="word-break:break-all">
							<? //echo number_format($value["lc_sc_value"],2); 
								if($cbo_type==1)
								{
									echo "L/C:".number_format($value["lc_sc_value"],2). '<br>';
									echo "S/C:".$attach_result_arr[$value["expoid"]]["contract_value"];
								}
								else
								{
									echo number_format($value["lc_sc_value"],2);
									
								}
							?>
							</td>
							<td width="70" align="center" style="word-break:break-all"><? echo $key; ?></td>
							<td width="200" align="center" style="word-break:break-all"><p><? echo $supplier; ?>&nbsp;</p></td>
							<td width="70" align="right" style="word-break:break-all"><? echo change_date_format($value["lc_date"]); ?></td>
							<td width="70" align="right" style="word-break:break-all"><? echo number_format($value["lc_value"],2); ?></td>
							<td width="70" align="right" style="word-break:break-all"><? echo number_format($maturite,2); ?></td>
							<td width="80" align="right" style="word-break:break-all"><? echo number_format($value["lc_value"]-$maturite,2);  ?></td>
							<td width="70" align="left" style="word-break:break-all"><?  //echo $row[csf("shipping_bill_n")]; ?></td>
							<td width="70" align="left" style="word-break:break-all"><?  //echo $row[csf("shipping_bill_n")]; ?></td>
							<td width="70" align="center" style="word-break:break-all"><? // echo change_date_format($row[csf("ship_bl_date")]); ?></td>
							<td width="70" align="center" style="word-break:break-all"><? // echo change_date_format($row[csf("ship_bl_date")]); ?></td>
							<td width="70" align="center" style="word-break:break-all"><? // echo change_date_format($row[csf("ship_bl_date")]); ?></td>
							<td width="70" align="center" style="word-break:break-all"><? // echo change_date_format($row[csf("ship_bl_date")]); ?></td>
							<td width="70" align="center" style="word-break:break-all"><? // echo change_date_format($row[csf("ship_bl_date")]); ?></td>
							<td width="70" align="right" style="word-break:break-all"><? //echo number_format($row[csf("cons_per_pcs")],2); $total_cons_per_pcs+=$row[csf("cons_per_pcs")]; ?></td>
							<td width="70" align="right" style="word-break:break-all"><? $yarn_used=$row[csf("invoice_qnty")]*$row[csf("cons_per_pcs")]; //echo number_format($yarn_used,2); $total_yarn_used+=$yarn_used;  ?></td>
							<td width="70" style="word-break:break-all"><p><? //echo $fdbc_arr[$row[csf("invoice_id")]]; ?>&nbsp;</p></td>
							<td width="70" style="word-break:break-all"><p><? //echo $row[csf("remarks")]; ?>&nbsp;</p></td>
							<td width="" style="word-break:break-all"><p><? //echo $row[csf("remarks")]; ?>&nbsp;</p></td>
						</tr>
						<?	
						$i++;
					}
                }
                ?>
                </tbody>
            </table>
		</div>
        <table width="1800" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
            <tfoot>
            	<th width="30">&nbsp;</th>
           
            </tfoot>
        </table>
		
	</div>    
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
	echo "$total_data####$filename";
	exit();

disconnect($con);
}
?>

