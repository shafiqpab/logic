<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

$supp_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
$lib_country=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );


if($action=="print_button_variable_setting")
{
	if($data==0) $comp_cond=""; else $comp_cond="and template_name in ($data)";
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where 1=1 $comp_cond and module_id=11 and report_id=74 and is_deleted=0 and status_active=1","format_id","format_id");
	// echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 100, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );
	exit();
}

if($action=="load_drop_delivery_company")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
 			echo create_drop_down( "cbo_delivery_company_name", 150, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_delivery_company_name", 150, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		}
	}
 	else if($data==1)
 	{
  		echo create_drop_down( "cbo_delivery_company_name", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Delivery Company --", '', "load_drop_down( 'requires/export_statement_report_urmi_controller', this.value, 'load_drop_down_location', 'location' );",0 );
 	}
 	else
 		echo create_drop_down( "cbo_delivery_company_name", 150, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
 	exit();
}


if ($action=="load_drop_down_location")
{
	$companies="'".$data."'";
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id in($data) and status_active =1 and is_deleted=0 group by id,location_name order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/export_statement_report_urmi_controller', $companies+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );" );
}
if ($action=="load_drop_down_del_floor")
{
	$data=explode('**',$data);
	$data[0]=str_replace("'","",$data[0]);
	echo create_drop_down( "cbo_del_floor", 105, "select id,floor_name from lib_prod_floor where company_id in($data[0]) and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);
	$del_com_name=str_replace("'","",$cbo_delivery_company_name);
	$location_name=str_replace("'","",$cbo_location_name);
	$delivery_floor=str_replace("'","",$cbo_del_floor);
	$internal_ref=str_replace("'","",$txt_internal_ref);
	$shipping_status=str_replace("'","",$cbo_shipping_status);
	$source=str_replace("'","",$cbo_source);
	
	$sqlCond = "";
	$sqlCond .= ($company_name!=0) ? " and a.company_id=$company_name" : "";
	$sqlCond .= ($del_com_name!=0) ? " and a.delivery_company_id in($del_com_name)" : "";
	$sqlCond .= ($location_name!=0) ? " and a.delivery_location_id=$location_name" : "";
	$sqlCond .= ($delivery_floor!=0) ? " and a.delivery_floor_id=$delivery_floor" : "";
	$sqlCond .= ($buyer_name!=0) ? " and a.buyer_id=$buyer_name" : "";
	// $sqlCond .= ($internal_ref!="") ? " and c.grouping like '%$internal_ref%'" : "";
	$sqlCond .= ($shipping_status!=0) ? " and c.shiping_status=$shipping_status" : "";
	$sqlCond .= ($source!=0) ? " and a.source=$source" : "";
	if($date_from !="" && $date_to !="")
    {
        if($db_type==0)
        {
            $start_date=change_date_format($date_from,"yyyy-mm-dd","");
            $end_date=change_date_format($date_to,"yyyy-mm-dd","");
        }
        else
        {
            $start_date=date("j-M-Y",strtotime($date_from));
            $end_date=date("j-M-Y",strtotime($date_to));
        }        
        
        $sqlCond .= " and b.ex_factory_date between '$start_date' and '$end_date'";       
    }

	if($reportType==1)
	{
		//$tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls","job_no","cm_cost");
		//$inspection_date_arr=return_library_array( "select po_break_down_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "inspection_date");
		//$invoice_array=return_library_array( "select id,invoice_no from com_export_invoice_ship_mst", "id", "invoice_no"  );
		//$shipping_mode_array=return_library_array( "select id,shipping_mode from com_export_invoice_ship_mst", "id", "shipping_mode"  );
		
		// $lc_sc_id_array=return_library_array( "select id,lc_sc_id from com_export_invoice_ship_mst", "id", "lc_sc_id"  );
		$lc_num_arr=return_library_array( "select id,export_lc_no from com_export_lc", "id", "export_lc_no"  );
		$sc_num_arr=return_library_array( "select id,contract_no from com_sales_contract", "id", "contract_no"  );
		$company_library=return_library_array( "select id, company_name from lib_company", 'id', 'company_name'  );

		$forwarder_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");

		// ======================================== getting ls/sc id ==============================
		if($internal_ref !="")
		{
			$sqlLcSc = "(SELECT ID FROM COM_EXPORT_LC WHERE internal_file_no = '$internal_ref') UNION ALL (SELECT ID FROM COM_SALES_CONTRACT WHERE internal_file_no = '$internal_ref') ORDER BY id";
			// echo $sqlLcSc;
			$lcscRes = sql_select($sqlLcSc);
			$lc_sc_id_array = array();
			foreach ($lcscRes as $val) 
			{
				$lc_sc_id_array[$val['ID']]=$val['ID'];
			}
			$lcScIDs = implode(",", $lc_sc_id_array);
			$lcscIdCond = "";
			if($lcScIDs !="")
			{
				$lcscIdCond = " and b.lc_sc_no in($lcScIDs)";
			}
			else
			{
				echo '<div style="text-align:center;font-size:20px;color:red">Data Not Found!</div>';die();
			}
		}
		// echo $lcscIdCond;
		//========================================= main query =====================================
		$sql="SELECT b.ex_factory_date as EXDATE, a.company_id as CID, a.sys_number as CHALAN, b.po_break_down_id as PO_ID, a.BUYER_ID, a.delivery_company_id as DEL_COMPANY, a.delivery_location_id as DEL_LOCATION, a.CHALLAN_NO, b.INVOICE_NO,
		(CASE WHEN a.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as EX_QNTY,
		(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as EX_RETURN_QNTY,
		b.total_carton_qnty as CARTON_QNTY, c.PO_NUMBER, b.LC_SC_NO, b.SHIPING_MODE, b.INCO_TERMS, a.FORWARDER, b.COUNTRY_ID
		from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b, wo_po_break_down c
		where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sqlCond $lcscIdCond order by b.ex_factory_date";
		// echo $sql;die();
		$sql_result=sql_select($sql);

		if(count($sql_result)==0)
		{
			echo '<div style="text-align:center;font-size:20px;color:red">Data Not Found!</div>';die();
		}
		$dataArray=array();
		$tot_rows=0;
		foreach($sql_result as $row)
		{
			$tot_rows++;
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['company']=$row["CID"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['chalan']=$row["CHALAN"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['buyer']=$row["BUYER_ID"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['del_com']=$row["DEL_COMPANY"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['del_loc']=$row["DEL_LOCATION"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['invoice_no']=$row["INVOICE_NO"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['po_number']=$row["PO_NUMBER"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['lc_sc_no']=$row["LC_SC_NO"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['shiping_mode']=$row["SHIPING_MODE"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['inco_terms']=$row["INCO_TERMS"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['exdate']=$row["EXDATE"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['forwarder']=$row["FORWARDER"];
			$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['country_id']=$row["COUNTRY_ID"];
		  	$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['ex_qnty']+=$row["EX_QNTY"];
		  	$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['ex_return_qnty']+=$row["EX_RETURN_QNTY"];
		  	$dataArray[date('Y-F',strtotime($row["EXDATE"]))][$row["PO_ID"]]['carton_qnty']+=$row["CARTON_QNTY"];

		  	$poId .=$row["PO_ID"].',';
		  	if ($row["INVOICE_NO"] != '') $invoiceId .=$row["INVOICE_NO"].',';
		}
		// echo "<pre>";print_r($dataArray);die();
				
		$invoiceId = implode(",", array_flip(array_flip(explode(',', rtrim($invoiceId,',')))));

		if ($poId != '')
		{
			$poIds = array_flip(array_flip(explode(',', rtrim($poId,','))));
			$allPoIdCond = '';
			$allPoIdCond2 = '';

			if($db_type==2 && $tot_rows>1000)
			{
				$allPoIdCond = ' and (';
				$allPoIdCond2 = ' and (';
				$poIdArr = array_chunk($poIds,999);
				foreach($poIdArr as $ids)
				{
					$ids = implode(',',$ids);
					$allPoIdCond .= " b.wo_po_break_down_id in($ids) or ";
					$allPoIdCond2 .= " b.id in($ids) or ";
				}
				$allPoIdCond = rtrim($allPoIdCond,'or ');
				$allPoIdCond2 = rtrim($allPoIdCond2,'or ');
				$allPoIdCond .= ')';
				$allPoIdCond2 .= ')';
			}
			else
			{
				$poIds = implode(',', $poIds);
				$allPoIdCond=" and b.wo_po_break_down_id in ($poIds)";
				$allPoIdCond2=" and b.id in ($poIds)";
			}
		}

		$lcScSql = "select b.wo_po_break_down_id as PO_ID, a.contract_no as LC_SC_NO, a.lien_bank as LIEN_BANK, a.inco_term as INCO_TERM
		from COM_SALES_CONTRACT a, COM_SALES_CONTRACT_ORDER_INFO b
		where a.id=b.COM_SALES_CONTRACT_ID $allPoIdCond
		UNION ALL
		select b.wo_po_break_down_id as PO_ID, a.export_lc_no as LC_SC_NO, a.lien_bank as LIEN_BANK, a.inco_term as INCO_TERM
		from COM_EXPORT_LC a, COM_EXPORT_LC_ORDER_INFO b
		where a.id=b.COM_EXPORT_LC_ID $allPoIdCond";
		$lcScSql_res = sql_select($lcScSql);
		$lcSc_info_arr=array();
		//$lcSc_info_arr[39979]['LS_SC_NO']='gsdhgbh';
		foreach ($lcScSql_res as $val) 
		{
			$lcSc_info_arr[$val['PO_ID']]['LC_SC_NO'] .= $val['LC_SC_NO'].',';
			$lcSc_info_arr[$val['PO_ID']]['LIEN_BANK'] .= $val['LIEN_BANK'].',';
			$lcSc_info_arr[$val['PO_ID']]['INCO_TERM'] .= $val['INCO_TERM'].',';
		}
		//echo '<pre>';print_r($lcSc_info_arr);

		$order_sql="SELECT a.style_ref_no as STYLE,b.id as PO_ID,(b.po_quantity*a.total_set_qnty) as ORDER_QUANTITY, (b.unit_price/a.total_set_qnty) as UNIT_PRICE from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 $allPoIdCond2";
		// echo $order_sql;die();
		foreach(sql_select($order_sql) as $key=>$val)
		{
			$order_info_arr[$val["PO_ID"]]['qty'] += $val['ORDER_QUANTITY'];
			$order_info_arr[$val["PO_ID"]]['style'] = $val['STYLE'];
			$order_info_arr[$val["PO_ID"]]['unit_price'] = $val['UNIT_PRICE'];
		}
		//$lc_sc_type_arr=return_library_array( "select id,is_lc from com_export_invoice_ship_mst where id in($invoiceId)", "id", "is_lc"  );
		if ($invoiceId != '')
		{
			$invoice_no_arr=return_library_array("select id,invoice_no from com_export_invoice_ship_mst where id in($invoiceId)", "id", "invoice_no");
		}
		$lib_bank=return_library_array( "select id,bank_name from lib_bank", "id", "bank_name"  );
		//$lc_bank =return_library_array( "select id, lien_bank from com_export_lc", "id", "lien_bank"  );
		//$sc_bank =return_library_array( "select id, lien_bank from com_sales_contract", "id", "lien_bank" );
		// print_r($sc_bank);
		ob_start();
		?>
     	<div class="main_container" style="width:1700px;">
     		<style type="text/css">
     			table tr th,table tr td{word-wrap: break-word;word-break: break-all;}
     		</style>
     		<div class="header_part" style="width:100%;">
     			<table width="1680"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="21" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="21" align="center" class="form_caption"> <strong style="font-size:15px;">Export Statement Report</strong></td>
                    </tr>
                </table>
                <br clear="all">
     			<table class="rpt_table" rules="all" width="1680" align="left" border="1">
     				<thead>
     					<tr>
     						<th width="30">Sl</th>
     						<th width="120">L/C Factory Name</th>
     						<th width="70">Months</th>
     						<th width="100">Buyer</th>
     						<th width="100">SC/LC No</th>
     						<th width="100">Style</th>
     						<th width="100">PO No</th>
     						<th width="80">PO Qty Pcs</th>
     						<th width="80">Shipment Mode</th>
     						<th width="80">Incoterm</th>
     						<th width="120">Challan NO</th>
     						<th width="80">Ex.Factory Date</th>
     						<th width="100">Invoice NO</th>
     						<th width="100">Bank Name</th>
     						<th width="80">Cartons</th>
     						<th width="80">Shipment (pcs)</th>
     						<th width="80">FOB / PCS ($)</th>
     						<th width="100">Shipment Value ($)</th>
     						<th width="100">C & F</th>
     						<th width="100">Destination</th>
     						<th width="120">Production Factory</th>
     					</tr>
     				</thead>
     			</table>
     		</div>
     		<div class="body_part" style="width:1700px;max-height:300px;overflow:auto" align="left" id="scroll_body">
     			<table class="rpt_table" rules="all" width="1680" id="table_body" border="1">
     				<tbody>
     					<?
     					$i=1;
     					$gr_carton_qty = 0;
     					$gr_ship_qty 	= 0;
     					$gr_ship_val 	= 0;
     					$gr_avg_rate 	= 0;
     					foreach ($dataArray as $year_mon => $ymData) 
     					{
     						foreach ($ymData as $poId => $row) 
     						{
     							$ym = explode("-", $year_mon);
     							$style = $order_info_arr[$poId]['style'];
     							$poQty = $order_info_arr[$poId]['qty'];
     							$rate = $order_info_arr[$poId]['unit_price'];
     							$exQty = $row['ex_qnty'] - $row['ex_return_qnty'];
     							$shipVal = $exQty*$rate;

     	                        $lc_sc_no=implode(',',array_unique(explode(',',rtrim($lcSc_info_arr[$poId]['LC_SC_NO'],','))));

     							$lien_bank=array_unique(explode(',',rtrim($lcSc_info_arr[$poId]['LIEN_BANK'],',')));
     							foreach ($lien_bank as $value) {
     								$lien_banks = $lib_bank[$value].',';
     							}

     							$inco_term=array_unique(explode(',',rtrim($lcSc_info_arr[$poId]['INCO_TERM'],',')));
     							foreach ($inco_term as $value) {
     								$inco_terms = $incoterm[$value].',';
     							}

		     					?>
		     					<tr>
		     						<td width="30"><? echo $i; ?></td>
		     						<td width="120"><p><? echo $company_library[$row['company']]; ?></p></td>
		     						<td width="70"><p><? echo $ym[1]; ?></p></td>
		     						<td width="100"><p><? echo $buyer_arr[$row['buyer']]; ?></p></td>
		     						<td width="100"><p>
		     							<? echo $lc_sc_no;//echo $lc_sc_type_arr[$row['invoice_no']] ==1 ? $lc_num_arr[$row['lc_sc_no']] : $sc_num_arr[$row['lc_sc_no']]; ?>
		     						</p></td>
		     						<td width="100"><p><? echo $style; ?></p></td>
		     						<td width="100"><p><? echo $row['po_number']; ?></p></td>
		     						<td align="right" width="80"><p><? echo number_format($poQty,0); ?></p></td>
		     						<td width="80" align="center"><p><? echo $shipment_mode[$row['shiping_mode']]; ?></p></td>
		     						<td width="80"><p><? echo rtrim($inco_terms,','); ?></p></td>
		     						<td width="120"><p><? echo $row['chalan']; ?></p></td>
		     						<td align="center" width="80"><p><? echo change_date_format($row['exdate']); ?></p></td>
		     						<td width="100"><p><? echo $invoice_no_arr[$row['invoice_no']]; ?></p></td>
		     						<td width="100"><p>
		     							<? echo rtrim($lien_banks,',');//echo $lc_sc_type_arr[$row['invoice_no']] ==1 ? $lib_bank[$lc_bank[$row['lc_sc_no']]] : $lib_bank[$sc_bank[$row['lc_sc_no']]]; ?>
		     						</p></td>
		     						<td align="right" width="80"><p><? echo $row['carton_qnty']; ?></p></td>
		     						<td align="right" width="80"><p><? echo number_format($exQty,0); ?></p></td>
		     						<td align="right" width="80"><p><? echo $rate; ?></p></td>
		     						<td align="right" width="100"><p><? echo number_format($shipVal,2); ?></p></td>
		     						<td width="100"><p><? echo $forwarder_arr[$row['forwarder']]; ?></p></td>
		     						<td width="100"><p><? echo $lib_country[$row['country_id']]; ?></p></td>
		     						<td width="120"><p><? echo $company_library[$row['del_com']]; ?></p></td>
		     					</tr>
		     					<?
		     					$i++;
		     					$gr_carton_qty += $row['carton_qnty'];
		     					$gr_ship_qty 	+= $exQty;
		     					$gr_ship_val 	+= $shipVal;		     					
		     				}
		     			}
     					?>
     				</tbody>
     			</table>
     		</div>
     		<div class="footer_part" style="width:100%;">
     			<table class="rpt_table" rules="all" width="1680" align="left" border="1">
     				<tfoot>
     					<tr>
     						<th width="30"></th>
     						<th width="120"></th>
     						<th width="70"></th>
     						<th width="100"></th>
     						<th width="100"></th>
     						<th width="100"></th>
     						<th width="100"></th>
     						<th width="80"></th>
     						<th width="80"></th>
     						<th width="80"></th>
     						<th width="120"></th>
     						<th width="80"></th>
     						<th width="100"></th>
     						<th width="100"></th>
     						<th width="80" id="value_gr_carton_qty"><? echo number_format($gr_carton_qty,0); ?></th>
     						<th width="80" id="value_gr_ship_qty"><? echo number_format($gr_ship_qty,0); ?></th>
     						<th width="80" id="value_fob_pcs_qty"><? echo number_format(($gr_ship_val/$gr_ship_qty),2); ?></th>
     						<th width="100" id="value_gr_ship_val"><? echo number_format($gr_ship_val,2); ?></th>
     						<th width="100"></th>
     						<th width="100"></th>
     						<th width="120"></th>
     					</tr>
     				</tfoot>
     			</table>
     		</div>
     	</div>

		<?
	}

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
	echo "$total_data####$filename####$reportType";
	exit();
}
disconnect($con);
?>
