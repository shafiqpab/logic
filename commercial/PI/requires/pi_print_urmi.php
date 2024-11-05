<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);

$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');

$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
$importer_name_library = return_library_array('SELECT id,company_name FROM lib_company','id','company_name');

?>
<link href="../../css/style_common.css" rel="stylesheet" type="text/css" />


<?
if($action=="print") 
{
	//echo "10**".$action; die;
	$data = explode('*',$data);
	$entryForm=$data[2];
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form=1 ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_name from user_passwd", "id", "user_name" );	 
	
?>
<div style="width:1000px">
	<? 
		$cbo_pi_basis_id='';
		$sql_mst = sql_select("select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, total_amount, upcharge, discount, net_total_amount,inserted_by,goods_rcv_status from com_pi_master_details where id= $data[1]"); 
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
	 	$i = 0; $total_ammount = 0;
		if($sql_mst[0][csf('import_pi')]==1)
		{
		?>
        	<div style="margin-left:10px">
                <table width="100%">
                    <tr>
                        <td style="font-size:20px;" align="center" colspan="6">
                            <strong>
                                <? 
                                    if($sql_mst[0][csf('within_group')]==1)
                                    {
                                        $buyer=$importer_name_library[$sql_mst[0][csf('importer_id')]];
                                        $address=$company_address;
                                    }
                                    else
                                    {
                                        $buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('importer_id')]."'");
                                        $buyerData=sql_select("select buyer_name, address_1 from lib_buyer where id='".$sql_mst[0][csf('importer_id')]."'");

                                        $buyer=$buyerData[0][csf('buyer_name')];
                                        $address=$buyerData[0][csf('address_1')];
                                    }
                                    echo $buyer;
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="100" align="right">From</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td width="200"><? echo $importer_name_library[$sql_mst[0][csf('supplier_id')]]; ?></td>
                        <td width="100">PI No:</td>
                        <td width="250"><? echo $sql_mst[0][csf('pi_number')];?></td>
                        <td width="150">Within Group:</td>
                        <td><? echo $yes_no[$sql_mst[0][csf('within_group')]];?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td rowspan="3"><? echo $address ;?></td>
                        <td>PI Date:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('pi_date')]);?></td>
                        <td>Last Shipment Date:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('last_shipment_date')]);?></td>
                    </tr> 
                    <tr>
                        <td></td>
                        <td>Currency:</td>
                        <td><? echo $currency[$sql_mst[0][csf('currency_id')]];?></td>
                        <td>Validity:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('pi_validity_date')]);?></td>
                    </tr>
                     <tr>
                        <td></td>
                        <td>Insert By:</td>
                        <td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                    </tr>
                    <tr>
                    	<td colspan="5" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; else echo "&nbsp;"; ?></td>
                    </tr> 
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th>Job No</th>
                        <th>Construction</th>
                        <th>Composition</th>
                        <th>Color</th>					
                        <th>GSM</th>
                        <th>Dia/Width</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                    <?
                        $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" ); $total_ammount = 0; $total_quantity=0;
						if($goods_rcv_status==2)
						{
							$sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount, item_prod_id from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
						}
						else
						{
							if($db_type==0)
							{
								$sql = "select group_concat(id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, item_prod_id";
								
							}
							else
							{
								$sql = "select listagg(cast(id as varchar(4000)),',') within group(order by id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, item_prod_id";
							}
						
							
						}
                       //echo $sql; die;
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        ?>
                            <tr>
                                <td><? echo $row[csf('work_order_no')]; ?></td>
                                <td><? echo $row[csf('construction')]; ?></td>
                                <td><? echo $row[csf('composition')]; ?></td>
                                <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td><? echo $row[csf('gsm')]; ?></td>
                                <td><? echo $row[csf('dia_width')]; ?></td>
                                <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                                <td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                            </tr>
                        <? 
                        } 
                        ?>
                        <tr>
                            <td align="right" colspan="7">Total</td> 
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                    </tbody> 
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
        	</div>
        <?
		}
		else
		{
		?>
			<div style="width:100%">
				<table width="100%">
				<?		
                foreach($sql_mst as $row_mst) 
                {
					$i++;
					$supplier_id=$row_mst[csf('supplier_id')];
					$sql_supplier=sql_select("SELECT id,supplier_name,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM lib_supplier WHERE id=$supplier_id");
					foreach($sql_supplier as $supplier_data) 
					{
						if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
						if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
						if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
						if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
						if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
						if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
						if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
						
						$supplier_address = $address_1.$address_2.$address_3.$address_4.$web_site.$email.$country;
					}
					?>
					<tr>
					   <td style="font-size:20px;" align="center" colspan="6"><strong><? echo $supplier_name_library[$row_mst[csf('supplier_id')]]; ?></strong></td>
					</tr>
					<tr>
					   <td style="font-size:12px;" align="center" colspan="6"><strong><? echo $supplier_address; ?></strong></td>
					</tr>
					<tr>
					   <td width="100" align="right">To</td>
					</tr>
					<tr>
					   <td>&nbsp;</td>
					   <td width="200"><? echo $importer_name_library[$row_mst[csf('importer_id')]];?></td>
					   <td width="100">Pi No:</td>
					   <td width="250"><? echo $row_mst[csf('pi_number')];?></td>
						<td width="150">HS Code:</td>
					   <td><? echo $row_mst[csf('hs_code')];?></td>
					</tr>
					<tr>
					   <td>&nbsp;</td>
					   <td rowspan="3"><? echo $company_address ;?></td>
					   <td>Pi Date:</td>
					   <td><? echo change_date_format($row_mst[csf('pi_date')]);?></td>
					   <td>Last Shipment Date:</td>
					   <td><? echo change_date_format($row_mst[csf('last_shipment_date')]);?></td>
					</tr> 
					<tr>
					   <td>&nbsp;</td>
					   <td>Currency:</td>
					   <td><? echo $currency[$row_mst[csf('currency_id')]];?></td>
					   <td>Validity:</td>
					   <td><? echo change_date_format($row_mst[csf('pi_validity_date')]);?></td>
					</tr> 
					<tr>
					   <td>&nbsp;</td> 
					   <td>Indentor:</td>
					   <td><? echo $supplier_name_library[$row_mst[csf('intendor_name')]];?></td>
					   <td>System ID:</td>
					   <td><? echo $row_mst[csf('id')];?></td>
					</tr>
                    <tr>
                     	<td>&nbsp;</td> 
					    <td>Insert By: </td> 
					    <td><? echo $user_library[$row_mst[csf('inserted_by')]]; ?></td>
					</tr>
                   
                    <tr>
                     	<td>&nbsp;</td>
                       	<td colspan="4">Remarks: &nbsp;<? echo $sql_mst[0][csf("remarks")]; ?></td>
                    </tr>
                     <tr>
                    	<td colspan="3" style="text-align:center; color:#FF0000; font-size:22px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; else echo "Not Approved"; ?></td>
                      
                    </tr> 
				 <?
				 $cbo_pi_basis_id=$row_mst[csf('pi_basis_id')];   
				} 
				?>
             </table>
          </div> 
          <div style="width:1000px; margin-left:10px">
			<? 
            if($entryForm==165)
            {
				$data_array=sql_select("SELECT a.id, work_order_no, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	
				
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				$color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
					<thead>
						<tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
							<th>WO</th>
							<? } ?>
							<th>Color</th>
							<th>Count</th>
							<th colspan="4">Composition</th>
							<th>Yarn Type</th>
							<th>UOM</th>
							<th>Quantity</th>
							<th>Rate</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
					<?
					$i = 0;
					$total_quantity = 0;
					$total_ammount = 0;
					foreach($data_array as $row) 
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<? if( $cbo_pi_basis_id  == 1 ) { ?>
							<td width="50"><? echo $row[csf('work_order_no')]; ?></td>
							<? } ?>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $color_name[$row[csf('color_id')]]; ?></td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "85"; ?>"><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "85"; else echo "90"; ?>"><? echo $composition[$row[csf('yarn_composition_item1')]]; ?></td>
							<td width="25"><? echo $row[csf('yarn_composition_percentage1')]; ?>%</td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "85"; else echo "90"; ?>"><? echo $composition[$row[csf('yarn_composition_item2')]]; ?></td>
							<td width="25"><? echo $row[csf('yarn_composition_percentage2')]; ?>%</td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "90"; else echo "135"; ?>">
							<? if( $row[csf('yarn_type')] != 0 ) echo $yarn_type[$row[csf('yarn_type')]]; ?>
							</td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "85"; ?>">
							<? if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?>
							</td>
							<td width="61"  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
							<td width="45" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td width="75" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
						</tr>
						<? 
					} 
					?>
					
						<tr class="tbl_bottom" height="25">
							<? 
							if( $cbo_pi_basis_id == 1) $colspan="9"; else $colspan="8";
							?>
							<td colspan="<? echo $colspan; ?>">Sum</td> 
							<td><? echo number_format($total_quantity,2);?></td>
							<td></td>
							<td><? echo number_format($total_ammount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
							<td><? echo number_format($upcharge,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
							<td><? echo number_format($discount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
							<td><? echo number_format($net_total_amount,4); ?></td>
						</tr> 
					</tbody> 
				</table>
				<table>
					<tr height="20"></tr>
					<tr>
						<td valign="top"><strong>In-Words: </strong></td>
						<td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
					</tr>
					<tr> 
					<tr height="50"></tr>
				</table>
				<table>
					<tr height="20"></tr>
					<tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
					<tr> 
					<tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==166) // knit 2,3
            {
				?>	
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?> 
                            <th>Fabric Type</th>
                            <th>Construction</th>
                            <th>Composition</th>
                            <th>Color</th>					
                            <th>GSM</th>
                            <th>Dia/Width</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.fabric_composition,a.fabric_construction,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 
                    
                    
                    $color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
                    $total_ammount = 0; $i = 0;
                    foreach($data_array as $row) 
                    {
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id  == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? echo $row[csf('fabric_type')]; ?></td>
                            <td><? echo $row[csf('fabric_construction')]; ?></td>
                            <td><? echo $row[csf('fabric_composition')]; ?></td>
                            <td><? echo $color_name[$row[csf('color_id')]]; ?></td>
                            <td><? echo $row[csf('gsm')]; ?></td>
                            <td><? echo $row[csf('dia_width')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
					} 
					?>
                        <tr class="tbl_bottom">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                            ?>
                            <td colspan="<? echo $colspan; ?>">Sum</td> 
                            <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==167)//Accessories  
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header">WO</th>
                            <? } ?>
                            
                            <th>Item Group</th>
                            <th>Item Description</th>
                            <th>Gmts Color</th>
                            <th>Gmts Size</th>
                            <th>Item Color</th>
                            <th>Item Size</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            </tr>
                    </thead>
                    <tbody>
                    <?
					if($goods_rcv_status==2)
					{
						$data_array=sql_select("SELECT a.id, a.pi_id, a.work_order_no, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, item_prod_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 
					}
					else
					{
						if($db_type==0)
							{
								$sql = "select group_concat(id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, item_prod_id";
								
							}
							else
							{
								$sql = "select listagg(cast(id as varchar(4000)),',') within group(order by id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, item_prod_id";
							}
						if($db_type==0)
						{
							$data_array=sql_select("SELECT group_concat(a.id) as id, a.pi_id, a.work_order_no, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0
							group by a.pi_id, a.work_order_no, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id 
							ORDER BY a.id ASC");	
						}
						else
						{
							$data_array=sql_select("SELECT listagg(cast(a.id as varchar(4000)),',') within group(order by a.id) as id, a.pi_id, a.work_order_no, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 
							group by a.pi_id, a.work_order_no, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id 
							ORDER BY a.id ASC");	
						}
						 
					}
                    
                    
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
                    
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><? echo $size_library[$row[csf('size_id')]]; ?></td>
                            <td><? echo $color_library[$row[csf('item_color')]]; ?></td>
                            <td><? echo $row[csf('item_size')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<? 
                    } 
                    ?>
                        <tr class="tbl_bottom">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                            ?>
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
				<tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==168)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?>
                            <th>Service Type</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.item_description,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 
                    
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=4; ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=3; ?>
                            <td><? echo $service_type[$row[csf('service_type')]]; ?></td>
                            <td><? echo ($row[csf('item_description')]); ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
					} 
					?>
                    
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==169)
            {
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				$color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?>
                            <th>Lot</th>
                            <th>Count</th>
                            <th>Yarn Description</th>
                            <th>Yarn Color</th>
                            <th>Color Range</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT work_order_no, lot_no,count_name,item_description,yarn_color,color_range,uom,quantity,rate,amount FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");	 
                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=7; ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=6; ?>
                            <td><? echo $row[csf('lot_no')]; ?></td>
                            <td><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $color_name[$row[csf('yarn_color')]]; ?></td>
                            <td><? echo $color_range[$row[csf('color_range')]]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
                    }
                    ?>
                        <tr class="tbl_bottom" height="25">
                            <td colspan="<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); ?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==170)
            {
				$color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?>
                            <th>Gmts Item</th>
                            <th>Embellishment Name</th>
                            <th>Embellishment Type</th>
                            <th>Gmts Color</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT work_order_no, gmts_item_id,embell_name,embell_type,color_id,uom,quantity,rate,amount FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");	 
                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row) 
                    {
						$emb_arr=array();
						if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
						else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
						else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
						else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
						else $emb_arr=$blank_array;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=6; ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=5; ?>
                            <td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                            <td><? echo $emblishment_name_array[$row[csf('embell_name')]]; ?></td>
                            <td><? echo $emb_arr[$row[csf('embell_type')]]; ?></td>
                            <td><? echo $color_name[$row[csf('color_id')]]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
                    }
                    ?>
                        <tr class="tbl_bottom" height="25">
                            <td colspan="<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); ?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<?
            }
			else if($entryForm==171)
            {
				$color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				$test_item_arr=return_library_array( "SELECT id,test_item FROM lib_lab_test_rate_chart",'id','test_item');
				
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=5; ?>
                            <th>WO</th>
                            <? } else $colspan=4; ?>
                            <th>Style Ref.</th>
                            <th>Test For</th>
                            <th>Remarks</th>
                            <th>Color</th>
                            <th>Test Item</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT b.id as labtest_mst_id,  a.work_order_no, a.test_for, a.test_item_id, a.remarks, a.color_id, a.amount FROM com_pi_item_details a,WO_LABTEST_MST b WHERE a.work_order_no=b.labtest_no and  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 

                    $i = 0; $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						$mst_id=$row[csf('labtest_mst_id')];
						$job_no_sql=sql_select("SELECT job_no FROM  WO_LABTEST_DTLS where mst_id='$mst_id'");
						//echo "SELECT job_no FROM  WO_LABTEST_DTLS where mst_id='$mst_id'";
												$style_ref_no='';
												$unique_data=array();
						foreach($job_no_sql as $job_no_value)
						{
						$job_no=$job_no_value[csf('job_no')];
						$style_ref_no_sql=sql_select("SELECT style_ref_no FROM  WO_PO_DETAILS_MASTER  where job_no='$job_no'");
						$style_ref_no_get=$style_ref_no_sql[0][csf('style_ref_no')];
						if(!in_array($style_ref_no_get,$unique_data))
						{
													array_push($unique_data,$style_ref_no_get);
																			$style_ref_no.=$style_ref_no_get.',';
						}


						
							}
						
						//echo "SELECT style_ref_no FROM  WO_PO_DETAILS_MASTER  where job_no='$job_no'";



						$test_item='';
						$test_item_ids=array_unique(explode(",",$row[csf('test_item_id')]));
						foreach($test_item_ids as $test_item_id)
						{
							$test_item.=$test_item_arr[$test_item_id].",";
						}
						$test_item=chop($test_item,',');
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? echo $style_ref_no; ?></td>
                            <td><? echo $test_for[$row[csf('test_for')]]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
                            <td><? echo $color_name[$row[csf('color_id')]]; ?></td>
                            <td><? echo $test_item; ?></td>
                            <td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
					<? 
                    }
                    ?>
                        <tr class="tbl_bottom" height="25">
                            <td colspan="<? echo $colspan; ?>" align="right">Sum</td> 
                            <td colspan="2"><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan; ?>" align="right">Upcharge</td> 
                            <td colspan="2"><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan; ?>" align="right">Discount</td> 
                            <td colspan="2"><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan; ?>" align="right">Net Total</td> 
                            <td colspan="2"><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <? } ?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<?
            }
            else if($entryForm==227)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header">WO</th>
                            <? } ?>
                            <th class="header">Item Group</th>
                            <th class="header">Item Description</th>
                            <th class="header">UOM</th>
                            <th class="header">Quantity</th>
                            <th class="header">Rate</th>
                            <th class="header">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id, a.work_order_no, a.color_id, a.item_group, a.item_description, a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	
                    
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo $row[csf('rate')]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<? 
					} 
					?>
                        <tr class="tbl_bottom" height="25">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                            ?>
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr> 
                    <tr height="50"></tr>
				</table>
				<?
            }
            else 
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? }?>
                            <th>Item Group</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.item_group,a.item_description,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 
                    
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? }  ?>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<? 
                    } 
                    ?>
                        <tr class="tbl_bottom" height="25">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="4"; else $colspan="3";
                            ?>
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				
				<? 
            }
            ?>
          </div>
        <?
		}
		?>  
	</div>
	<?
    exit();	 
}
 
if($action=="print_pi")
{
	$data = explode('*',$data);
	//$item_category_id=$data[2];
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $company_data[csf('country_id')].','.' ';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	/*$approved_arr=array();
	$sql_app = sql_select("SELECT mst_id, approved_by, max(approved_date) as approved_date FROM approval_history where mst_id=$data[1] and entry_form=1 group by mst_id, approved_by ORDER BY id ASC");
	foreach($sql_app as $row)
	{
		$approved_arr[$row[csf('mst_id')]]['user']=$row[csf('approved_by')];
		$approved_arr[$row[csf('mst_id')]]['date']=$row[csf('approved_date')];
	}*/
	//$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form=1 ORDER BY id ASC","mst_id","approved_by");
	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form=1 ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_name from user_passwd", "id", "user_name" );		 
	?>
	<div style="width:1000px">
	<? 
		$cbo_pi_basis_id='';
		 $sql_qry="select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, approved_by, approved_date, total_amount, upcharge, discount, net_total_amount, inserted_by, goods_rcv_status 
		 from com_pi_master_details where id= $data[1]";
		$sql_mst = sql_select($sql_qry);
		$item_category_id=$sql_mst[0][csf("item_category_id")];//cute 
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];
		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
		$approved_by=$sql_mst[0][csf("approved_by")];
		$approved_date=$sql_mst[0][csf("approved_date")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
	 	$i=0; $total_ammount=0;
		if($sql_mst[0][csf('import_pi')]==1)
		{
		?>
        	<div style="margin-left:10px">
                <table width="100%">
                    <tr>
                        <td style="font-size:20px;" align="center" colspan="6">
                            <strong>
                                <? 
                                    if($sql_mst[0][csf('within_group')]==1)
                                    {
                                        $buyer=$importer_name_library[$sql_mst[0][csf('importer_id')]];
                                        $address=$company_address;
                                    }
                                    else
                                    {
                                        $buyer=return_field_value("buyer_name","lib_buyer","id='".$sql_mst[0][csf('importer_id')]."'");
                                        $buyerData=sql_select("select buyer_name, address_1 from lib_buyer where id='".$sql_mst[0][csf('importer_id')]."'");
                                        $buyer=$buyerData[0][csf('buyer_name')];
                                        $address=$buyerData[0][csf('address_1')];
                                    }
                                    echo $buyer;
                                ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="100" align="right">From</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td width="200"><? echo $importer_name_library[$sql_mst[0][csf('supplier_id')]]; ?></td>
                        <td width="100">PI No:</td>
                        <td width="250"><? echo $sql_mst[0][csf('pi_number')];?></td>
                        <td width="150">Within Group:</td>
                        <td><? echo $yes_no[$sql_mst[0][csf('within_group')]];?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td rowspan="3"><? echo $address ;?></td>
                        <td>PI Date:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('pi_date')]);?></td>
                        <td>Last Shipment Date:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('last_shipment_date')]);?></td>
                    </tr> 
                    <tr>
                        <td></td>
                        <td>Currency:</td>
                        <td><? echo $currency[$sql_mst[0][csf('currency_id')]];?></td>
                        <td>Validity:</td>
                        <td><? echo change_date_format($sql_mst[0][csf('pi_validity_date')]);?></td>
                    </tr>
                     <tr>
                        <td></td>
                        <td>Insert By:</td>
                        <td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                      </tr>
                    <tr>
                    	<td colspan="5" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; else echo "&nbsp;"; ?></td>
                    </tr> 
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th>Job No</th>
                        <th>Construction</th>
                        <th>Composition</th>
                        <th>Color</th>					
                        <th>GSM</th>
                        <th>Dia/Width</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                    <?
                        $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" ); $total_ammount = 0; $total_quantity=0;
                        $sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        ?>
                            <tr>
                                <td><? echo $row[csf('work_order_no')]; ?></td>
                                <td><? echo $row[csf('construction')]; ?></td>
                                <td><? echo $row[csf('composition')]; ?></td>
                                <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td><? echo $row[csf('gsm')]; ?></td>
                                <td><? echo $row[csf('dia_width')]; ?></td>
                                <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                                <td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                            </tr>
                        <? 
                        } 
                        ?>
                        <tr>
                            <td align="right" colspan="7">Total</td> 
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                    </tbody> 
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($total_ammount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
        	</div>
        <?
		}
		else
		{
			?>
			<div style="width:100%">
			<table width="1000">
				<?		
                $supplier_id=$sql_mst[0][csf("supplier_id")];
                $sql_supplier=sql_select("SELECT id, supplier_name, country_id, web_site, email, address_1, address_2, address_3, address_4 FROM lib_supplier WHERE id=$supplier_id");
                foreach($sql_supplier as $supplier_data) 
                {
                    if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
                    if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
                    if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
                    if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
                    if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
                    if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
                    if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
                    
                    $supplier_address = $address_1.$address_2.$address_3.$address_4.$web_site.$email.$country;
                }
                ?>
                <tr><td style="font-size:20px;" align="center" colspan="5"><strong><? echo $supplier_name_library[$supplier_id]; ?></strong></td><td rowspan="2" style="text-align:center; font-size:22px; border: 2px solid black;"><b><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; else echo "Not Approved"; ?></b></td></tr>
                <tr><td style="font-size:12px;" align="center" colspan="5"><strong><? echo $supplier_address; ?></strong></td></tr>
                <tr><td width="100" align="right">To</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td style="font-size:16px; border: 1px solid black; word-break:break-all;" ><b>PI: <? echo $sql_mst[0][csf("pi_number")]; ?><br /><b>Sys. ID: <? echo $sql_mst[0][csf("id")]; ?></b></b></td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td width="200"><? echo $importer_name_library[$sql_mst[0][csf("importer_id")]];?></td>
                    <td width="100">Pi No:</td>
                    <td width="250" style="word-break:break-all;"><? echo $sql_mst[0][csf("pi_number")]; ?></td>
                    <td width="150">HS Code:</td>
                    <td><? echo $sql_mst[0][csf("hs_code")]; ?></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td rowspan="3"><? echo $company_address ;?></td>
                    <td>Pi Date:</td>
                    <td><? echo change_date_format($sql_mst[0][csf("pi_date")]);?></td>
                    <td>Last Shipment Date:</td>
                    <td><? echo change_date_format($sql_mst[0][csf("last_shipment_date")]);?></td>
                </tr> 
                <tr>
                    <td>&nbsp;</td>
                    <td>Currency:</td>
                    <td><? echo $currency[$sql_mst[0][csf("currency_id")]];?></td>
                    <td>Validity:</td>
                    <td><? echo change_date_format($sql_mst[0][csf("pi_validity_date")]);?></td>
                </tr> 
                <tr>
                    <td>&nbsp;</td> 
                    <td>Indentor:</td>
                    <td><? echo $supplier_name_library[$sql_mst[0][csf("intendor_name")]];?></td>
                    <td>System ID:</td>
                    <td><? echo $sql_mst[0][csf("id")];?></td>
                </tr>
                <tr>
                	<td>&nbsp;</td> 
                    <td>Last Approved Date:</td>
                    <td><? echo change_date_format($approved_date); ?></td>
                    <td colspan="2">Insert By: &nbsp;<? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                    <td>&nbsp;</td>
                </tr> 
                <tr>
	                <td>&nbsp;</td> 
	                <td colspan="4">Remarks: &nbsp;<? echo $sql_mst[0][csf("remarks")]; ?></td>
                </tr>
                
			</table>
			</div> 
          <div style="width:1000px; margin-left:10px">
			<? 
			$cbo_pi_basis_id=$sql_mst[0][csf("pi_basis_id")];
            if($entryForm==165)
            {
				$data_array=sql_select("SELECT a.id, work_order_no, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	
				
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				$color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
					<thead>
						<tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
							<th>WO</th>
							<? } ?>
							<th>Color</th>
							<th>Count</th>
							<th colspan="4">Composition</th>
							<th>Yarn Type</th>
							<th>UOM</th>
							<th>Quantity</th>
							<th>Rate</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
					<?
					$i = 0;
					$total_quantity = 0;
					$total_ammount = 0;
					foreach($data_array as $row) 
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<? if( $cbo_pi_basis_id  == 1 ) { ?>
							<td width="50"><? echo $row[csf('work_order_no')]; ?></td>
							<? } ?>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $color_name[$row[csf('color_id')]]; ?></td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "85"; ?>"><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "85"; else echo "90"; ?>"><? echo $composition[$row[csf('yarn_composition_item1')]]; ?></td>
							<td width="25"><? echo $row[csf('yarn_composition_percentage1')]; ?>%</td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "85"; else echo "90"; ?>"><? echo $composition[$row[csf('yarn_composition_item2')]]; ?></td>
							<td width="25"><? echo $row[csf('yarn_composition_percentage2')]; ?>%</td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "90"; else echo "135"; ?>">
							<? if( $row[csf('yarn_type')] != 0 ) echo $yarn_type[$row[csf('yarn_type')]]; ?>
							</td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "85"; ?>">
							<? if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?>
							</td>
							<td width="61"  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
							<td width="45" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td width="75" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
						</tr>
						<? 
					} 
					?>
					
						<tr class="tbl_bottom" height="25">
							<? 
							if( $cbo_pi_basis_id == 1) $colspan="9"; else $colspan="8";
							?>
							<td colspan="<? echo $colspan; ?>">Sum</td> 
							<td><? echo number_format($total_quantity,2);?></td>
							<td></td>
							<td><? echo number_format($total_ammount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
							<td><? echo number_format($upcharge,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
							<td><? echo number_format($discount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right" style="font-size:20px;">Net Total</td> 
                            <td style="font-size:20px;"><i><? echo number_format($net_total_amount,2); ?></i></td>
                        </tr>  
					</tbody> 
				</table>
				<table>
					<tr height="20"></tr>
					<tr>
						<td valign="top"><strong>In-Words: </strong></td>
						<td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
					</tr>
					<tr> 
					<tr height="50"></tr>
				</table>
				<table>
					<tr height="20"></tr>
					<tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
					<tr> 
					<tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==166) // knit
            {
				?>	
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?> 
                            <th>Fabric Type</th>
                            <th>Construction</th>
                            <th>Composition</th>
                            <th>Color</th>					
                            <th>GSM</th>
                            <th>Dia/Width</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.fabric_composition,a.fabric_construction,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 
                    
                    
                    $color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
                    $total_ammount = 0; $i = 0;
                    foreach($data_array as $row) 
                    {
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id  == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? echo $row[csf('fabric_type')]; ?></td>
                            <td><? echo $row[csf('fabric_construction')]; ?></td>
                            <td><? echo $row[csf('fabric_composition')]; ?></td>
                            <td><? echo $color_name[$row[csf('color_id')]]; ?></td>
                            <td><? echo $row[csf('gsm')]; ?></td>
                            <td><? echo $row[csf('dia_width')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
					} 
					?>
                        <tr class="tbl_bottom">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="8"; else $colspan="7";
                            ?>
                            <td colspan="<? echo $colspan; ?>">Sum</td> 
                            <td><? //echo number_format($total_quantity,2); $total_quantity = 0;?>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right" style="font-size:20px;">Net Total</td> 
                            <td style="font-size:20px;"><i><? echo number_format($net_total_amount,2); ?></i></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==167)//Accessories  
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header">WO</th>
                            <? } ?>
                            <th>Style Ref.</th>
                            <th>Item Group</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                    <?
					if($db_type==0) $style_grpCond="group_concat(style_des)";
					else if($db_type==2) $style_grpCond="listagg(cast(style_des as varchar(4000)),',') within group (order by style_des)";
					//$wo_ord_arr = return_library_array('SELECT a.booking_no, b.style_ref_no FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no ','booking_no','style_ref_no');
					$wo_po=sql_select("SELECT a.booking_no, b.style_ref_no FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$data[0] ");
					$wo_ord_arr=array();
					foreach($wo_po as $row)
					{
						$wo_ord_arr[$row[csf('booking_no')]].=$row[csf('style_ref_no')].',';
					}
					$wo_nonOrd_arr = return_library_array('SELECT booking_no, $style_grpCond as style_ref FROM wo_non_ord_samp_booking_dtls where status_active=1 group by booking_no','booking_no','style_ref');
					
                    //$data_array=sql_select("SELECT id, pi_id, work_order_no, booking_without_order, color_id, size_id, item_color, item_size, item_group, item_description, gsm, dia_width, uom, quantity, rate, amount, service_type FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");
					$data_array=sql_select("SELECT work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0 group by work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id");	 
                    
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						$i++;
                    	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$wo_style_no='';
						if($row[csf('booking_without_order')]==1) $wo_style_no=implode(",",array_unique(explode(",",$wo_nonOrd_arr[$row[csf('work_order_no')]])));
						else $wo_style_no=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_no')]])));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? echo rtrim($wo_style_no,','); ?></td>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<? 
                    } 
                    ?>
                        <tr class="tbl_bottom">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="5"; else $colspan="4";
                            ?>
                            <td colspan = "<? echo $colspan; ?>" align="right">&nbsp;</td> 
                            <td>&nbsp;<? /*echo number_format($total_quantity,2);*/ $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right" style="font-size:20px;">Net Total</td> 
                            <td style="font-size:20px;"><i><? $net_total_amount=(($total_ammount+$upcharge)-$discount); echo number_format($net_total_amount,2); ?></i></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
				<tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <? } ?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==168)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?>
                            <th>Service Type</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.item_description,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 
                    
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=4; ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=3; ?>
                            <td><? echo $service_type[$row[csf('service_type')]]; ?></td>
                            <td><? echo ($row[csf('item_description')]); ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
					} 
					?>
                    
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right" style="font-size:20px;">Net Total</td> 
                            <td style="font-size:20px;"><i><? echo number_format($net_total_amount,2); ?></i></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==169)
            {
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				$color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?>
                            <th>Lot</th>
                            <th>Count</th>
                            <th>Yarn Description</th>
                            <th>Yarn Color</th>
                            <th>Color Range</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT work_order_no, lot_no,count_name,item_description,yarn_color,color_range,uom,quantity,rate,amount FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");	 
                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=7; ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=6; ?>
                            <td><? echo $row[csf('lot_no')]; ?></td>
                            <td><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $color_name[$row[csf('yarn_color')]]; ?></td>
                            <td><? echo $color_range[$row[csf('color_range')]]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
                    }
                    ?>
                        <tr class="tbl_bottom" height="25">
                            <td colspan="<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); ?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr> 
                    <tr height="50"></tr>
				</table>
				<? 
            }
            else if($entryForm==170)
            {
				$color_name = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?>
                            <th>Gmts Item</th>
                            <th>Embellishment Name</th>
                            <th>Embellishment Type</th>
                            <th>Gmts Color</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT work_order_no, gmts_item_id,embell_name,embell_type,color_id,uom,quantity,rate,amount FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");	 
                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row) 
                    {
						$emb_arr=array();
						if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
						else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
						else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
						else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
						else $emb_arr=$blank_array;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=6; ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=5; ?>
                            <td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                            <td><? echo $emblishment_name_array[$row[csf('embell_name')]]; ?></td>
                            <td><? echo $emb_arr[$row[csf('embell_type')]]; ?></td>
                            <td><? echo $color_name[$row[csf('color_id')]]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<? 
                    }
                    ?>
                        <tr class="tbl_bottom" height="25">
                            <td colspan="<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); ?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Net Total</td> 
                            <td><? echo number_format($net_total_amount,4); ?></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<?
            }
            else if($entryForm==227)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header">WO</th>
                            <? } ?>
                            <th class="header">Item Group</th>
                            <th class="header">Item Description</th>
                            <th class="header">UOM</th>
                            <th class="header">Quantity</th>
                            <th class="header">Rate</th>
                            <th class="header">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id, a.work_order_no, a.color_id, a.item_group, a.item_description, a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	
                    
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo $row[csf('rate')]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<? 
					} 
					?>
                        <tr class="tbl_bottom" height="25">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                            ?>
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right" style="font-size:20px;">Net Total</td> 
                            <td style="font-size:20px;"><i><? echo number_format($net_total_amount,2); ?></i></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				<?
            }
            else
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? }?>
                            <th>Item Group</th>
                            <th>Item Description</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.item_group,a.item_description,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	 
                    
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? }  ?>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<? 
                    } 
                    ?>
                        <tr class="tbl_bottom" height="25">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="2"; else $colspan="3";
                            ?>
                            <td  colspan = "<? echo $colspan; ?>" align="right">Sum</td> 
                            <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Upcharge</td> 
                            <td><? echo number_format($upcharge,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right">Discount</td> 
                            <td><? echo number_format($discount,4); ?></td>
                        </tr>
                        <tr class="tbl_bottom" height="25">
                            <td  colspan = "<? echo $colspan+2; ?>" align="right" style="font-size:20px;">Net Total</td> 
                            <td style="font-size:20px;"><i><? echo number_format($net_total_amount,2); ?></i></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,4, '.', ''),'USD','Cent');?></td>
                    </tr>
                    <tr> 
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                        <? if ($sql_mst[0][csf('approved')]==1) { ?>
						<td><? echo $userName_arr[$approvedBy_arr[$data[1]]]; ?><hr style = 'background-color:#000000; border-width:0; color:#000000; height:2px; lineheight:0; width:100%;' />Appove By</td>
                        <?}?>
                    </tr>
                    <tr height="50"></tr>
				</table>
				
				<? 
            }
            ?>
          </div>
        <?
		}
		?>  
	</div>
<?	 
 exit();	
}
 
?>


 