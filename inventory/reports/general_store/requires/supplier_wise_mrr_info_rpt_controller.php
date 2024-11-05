<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//library array-------------------
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$countryArr = return_library_array("select id,country_name from lib_country where status_active=1 and is_deleted=0","id","country_name");
$location_details = sql_select("select id,company_name,plot_no,level_no,road_no,block_no,city,zip_code,country_id from lib_company where status_active=1 and is_deleted=0");
$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active in(1,3) and is_deleted=0","id","supplier_name");
$supplier_address = sql_select("SELECT id, address_1, address_2, address_3, address_4 from lib_supplier where status_active in(1,3) and is_deleted=0");
$item_group_arr=return_library_array( "select id, item_name from  lib_item_group",'id','item_name');

if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_supplier_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where  a.id=b.supplier_id and a.id=c.supplier_id and  c.tag_company=$data and b.party_type in (1,6,7,8,90,92) and a.status_active in (1,3) and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "", "" );
	exit();

}

if ($action=="load_drop_down_store")
{
	$userCredential = sql_select("SELECT store_location_id FROM user_passwd where id=$user_id");
	$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and id in (".$userCredential[0][csf("store_location_id")].")" : "" ;
	echo create_drop_down( "cbo_store_name", 120, "SELECT id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id='$data' $store_cond order by store_name","id,store_name", 1, "--Select Store--", 0, "" );
	exit();
}

//report generated here--------------------//
if($action=="generate_report")
{

	// $process = array( &$_POST );
	// extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	$location = array();

	foreach ($location_details as  $value) 
	{
		$location[$value[csf("id")]]["id"] = $value[csf("id")];
		$location[$value[csf("id")]]["company_name"] = $value[csf("company_name")];
		$location[$value[csf("id")]]["plot_no"] = $value[csf("plot_no")];
		$location[$value[csf("id")]]["block_no"] = $value[csf("block_no")];
		$location[$value[csf("id")]]["level_no"] = $value[csf("level_no")];
		$location[$value[csf("id")]]["road_no"] = $value[csf("road_no")];
		$location[$value[csf("id")]]["city"] = $value[csf("city")];
		$location[$value[csf("id")]]["zip_code"] = $value[csf("zip_code")];
		$location[$value[csf("id")]]["country_id"] = $value[csf("country_id")];
	}
	// echo "<pre>";
	// print_r($location);
	// echo "</pre>";
	// // var_dump($location);die;

	$supplier_addr = array();
	foreach ($supplier_address as  $value) 
	{
		$supplier_addr[$value[csf("id")]]["address_1"] = $value[csf("address_1")];
		$supplier_addr[$value[csf("id")]]["address_2"] = $value[csf("address_2")];
		$supplier_addr[$value[csf("id")]]["address_3"] = $value[csf("address_3")];
		$supplier_addr[$value[csf("id")]]["address_4"] = $value[csf("address_4")];
	}
	$general_item_cat_arr = implode(", ", array_keys($general_item_category));


	$search_cond="";
	if($cbo_supplier_name != ""){
		$search_cond.=" and a.supplier_id in($cbo_supplier_name)";
	}

	if($cbo_store_name){ $search_cond.=" and a.store_id=$cbo_store_name "; }

	if($txt_mrr_number != ""){
		$mrr_number = explode("-", $txt_mrr_number);
		if(count($mrr_number)>1){
			$search_cond.=" and a.recv_number Like '%$txt_mrr_number%'";
		}else{
			$search_cond.=" and a.recv_number_prefix_num Like '%$mrr_number[0]%'";
		}
	}
	// if($cbo_year != ""){
	// 	$search_cond.=" and extract(year from a.insert_date) = $cbo_year";
	// }
	
	if($db_type==2)
	{
		$year_cond = " extract(year from a.insert_date) as year";

		if( $txt_date_from== "" && $txt_date_to=="" ) $search_cond.=""; else $search_cond.=" and a.receive_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
	}
	if($db_type==0)
	{
		$year_cond = " extract(year from a.insert_date) as year";

		if( $txt_date_from=="" && $txt_date_to=="" ) $search_cond.=""; else $search_cond.= "  and a.receive_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	}



	$sql="SELECT a.id as receive_id, a.company_id, a.recv_number, a.receive_date, a.challan_no, a.addi_challan_date, a.supplier_id, a.receive_basis,a.booking_no, a.bill_no, a.bill_date, a.entry_form, $year_cond, b.item_category, b.cons_quantity,b.cons_amount, c.id as prod_id, c.product_name_details, c.unit_of_measure, c.item_group_id, c.item_description, c.item_size
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.company_id = $cbo_company_name and a.entry_form=20 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted = 0 and c.status_active=1 and b.item_category in($general_item_cat_arr) $search_cond and b.transaction_type=1 order by a.id";

	$result = sql_select($sql);
	// echo $sql;die;
	$supplier_wise_mrr_arr = array();
	foreach ($result as  $value) {
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['receive_id'] = $value[csf('receive_id')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['recv_number'] = $value[csf('recv_number')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['receive_date'] = $value[csf('receive_date')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['challan_no'] = $value[csf('challan_no')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['challan_date'] = $value[csf('addi_challan_date')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['receive_basis'] = $value[csf('receive_basis')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['receive_basis_ref']= $value[csf('booking_no')];;
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['bill_no'] = $value[csf('bill_no')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['bill_date'] = $value[csf('bill_date')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['cons_quantity'] = $value[csf('cons_quantity')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['cons_amount'] = $value[csf('cons_amount')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['product_name_details'] = $value[csf('product_name_details')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['unit_of_measure'] = $value[csf('unit_of_measure')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['item_group_id'] = $value[csf('item_group_id')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['item_description'] = $value[csf('item_description')];
		$supplier_wise_mrr_arr[$value[csf('receive_id')]][$value[csf('prod_id')]]['item_size'] = $value[csf('item_size')];
		if($dup_check[$value[csf('receive_id')]][$value[csf('prod_id')]]=="")
		{
			$dup_check[$value[csf('receive_id')]][$value[csf('prod_id')]]=$value[csf('prod_id')];
			$row_count[$value[csf('receive_id')]]++;
		}
	}
	// echo "<pre>";print_r($row_count);die;
	// var_dump($supplier_wise_mrr_arr);
	// echo "<pre>";
	// print_r($supplier_wise_mrr_arr);
	// echo "</pre>";

		ob_start();
		?>
		<div style="width: 1150px; margin: 15px 0;" id="scroll_body">
        	<table width="99%" border="0" align = "left">
            	<tr class="form_caption" style="border:none;">
                    <td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="16" align="center" style="border:none; font-size:14px;">
                    <h1>Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></h1>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
                    <? echo "Level No# ".$location["$cbo_company_name"]["level_no"].", Plot No# ".$location["$cbo_company_name"]["plot_no"].", Road No# ".$location["$cbo_company_name"]["road_no"].", Block No# ".$location["$cbo_company_name"]["block_no"].", City: ".$location["$cbo_company_name"]["city"].", ZIP Code# ".$location["$cbo_company_name"]["zip_code"];?>
                    </td>
                </tr>

            </table>
			<div style="width: 1150px; overflow-y: scroll; max-height: 350px;" id="democlass">
				<table width="99%" border="0" cellpadding="4" cellspacing="0" class="rpt_table" rules="all" id="table_header_2"  align = "left">
					<tr style="background-color: #def8c4; padding: 3px 2px;">
                        <td width="150" align="left"><strong>Supplier Name : </strong></th>
                        <td><? echo $supplierArr[$cbo_supplier_name];?></td>
					</tr>
					<tr style="background-color: #def8c4; padding: 3px 2px;">
                        <td width="150" align="left"><strong>Address: </strong></th>
                        <td><? echo $supplier_addr["$cbo_supplier_name"]["address_1"].' '.$supplier_addr["$cbo_supplier_name"]["address_2"].' '.$supplier_addr["$cbo_supplier_name"]["address_3"].' '.$supplier_addr["$cbo_supplier_name"]["address_4"];?></td>

                    </tr>
				</table>
            	<table width="99%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1"  align = "left">
                <thead>
                    <tr>
                        <th width="120">MRR No</th>
                        <th width="90">MRR Date</th>
                        <th width="90">Challan No</th>
                        <th width="90">Challan Date</th>
                        <th width="80">Bill No</th>
                        <th width="90">Bill Date</th>
                        <th width="80">Receive Basis</th>
                        <th width="100">Receive Basis Ref</th>
                        <th width="160">Item Description</th>
                        <th width="80">UOM</th>
                        <th width="100">Quantity</th>
                        <th width="80">Rate(Tk)</th>
                        <th>Amount(Tk)</th>
                    </tr>
                </thead>

                <tbody id="mrr_details_tbl_body" >
				<?

					$i = 1;
					foreach ($supplier_wise_mrr_arr  as $rcv_id=>$receiv_arr)
					{
						foreach ($receiv_arr as $prod_id=>$row)
						{
							$total_qnty += $row['cons_quantity'];
							$total_amount += $row['cons_amount'];
							$rate = $row['cons_amount']/$row['cons_quantity'];
							$rowspan = $row_count[$rcv_id];
							$item_desc_dtls=$item_group_arr[$row["item_group_id"]]." ".$row["item_description"]." ".$row["item_size"];
							?>
		                	<tr onclick="change_color('tr_<? echo $i;?>','')" id = "tr_<? echo $i;?>">
								<?
								if($rcv_data_check[$rcv_id]=="")
								{
									$rcv_data_check[$rcv_id]=$rcv_id;
									?>
									<td align="center" rowspan = "<? echo $rowspan ;?>"><? echo $row['recv_number']; ?></td>
									<td align="center" rowspan = "<? echo $rowspan ;?>"> <? echo $row['receive_date'];?></td>
									<td align="right" rowspan = "<? echo $rowspan ;?>"><? echo $row['challan_no'];?> </td>
									<td align="center" rowspan = "<? echo $rowspan ;?>"> <? echo $row['challan_date'];?></td>
									<td align="center" rowspan = "<? echo $rowspan ;?>"> <? echo $row['bill_no'];?></td>
									<td align="center" rowspan = "<? echo $rowspan ;?>"><? echo change_date_format($row['bill_date']);?> </td>
									<td align="center" rowspan = "<? echo $rowspan ;?>"> <? echo $receive_basis_arr[$row['receive_basis']];?></td>
									<td align="center" rowspan = "<? echo $rowspan ;?>"> <? echo $row['receive_basis_ref'];?></td>
									
									<?
								}
								?>

								<td> <? echo $item_desc_dtls; //$row['product_name_details'];?></td>
								<td align="right"> <? echo $unit_of_measurement[$row['unit_of_measure']];?></td>
								<td align="right"> <? echo number_format($row['cons_quantity'],2);?></td>
								<td align="right"> <? echo number_format($rate,2);?></td>
								<td align="right"> <? echo number_format($row['cons_amount'],2);?></td>
							</tr>
							<?
							$i++;
							$mrr_total_qnty+=$row['cons_quantity'];
							$mrr_total_amt+=$row['cons_amount'];
						}
						?>
						<tr style="background-color:  #f3fccd; padding: 3px 2px;">
							<td align="right" colspan="10"> <strong>Mrr Wise Total : </strong></td>
							<td align="right"> <strong><? echo number_format($mrr_total_qnty,2); ?></strong></td>
							<td align="right"> </td>
							<td align="right"> <strong><? echo number_format($mrr_total_amt,2); ?></strong></td>
						</tr>
						<?
						$mrr_total_qnty=$mrr_total_amt=0;

					}
				?>
                </tbody>
				<tfoot>
					<tr style="background-color: #e7e8a5; padding: 3px 2px;">
						<td align="right" colspan="10"> <strong>Supplier Wise Total : </strong></td>
						<td align="right"> <strong><? echo number_format($total_qnty,2); ?></strong></td>
						<td align="right"> </td>
						<td align="right"> <strong><? echo number_format($total_amount,2); ?></strong></td>
					</tr>
				</tfoot>
            </table>
			</div>
		</div>

    <?
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

?>
