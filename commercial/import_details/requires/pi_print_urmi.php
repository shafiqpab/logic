<?
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.trims.php');
session_start();
extract($_REQUEST);

$color_library=return_library_array( "select id,color_name from lib_color where status_active=1", "id", "color_name"  );

$size_library=return_library_array( "select id,size_name from lib_size where status_active=1", "id", "size_name"  );

$supplier_name_library = return_library_array('SELECT id,supplier_name FROM lib_supplier where status_active=1','id','supplier_name');
$importer_name_library = return_library_array('SELECT id,company_name FROM lib_company','id','company_name');



if($action=="print")
{
	$data = explode('*',$data);
	$entryForm=$data[2];
	$cbo_item_category_id=$data[3];
	$is_mail_send=$data[4];
	$mailAddress=$data[5];
	$company_id=$data[0];
	$sys_id=$data[1];
	// echo $is_mail_send;die;
	// print_r($data); die;
 


	  $path='../../';

	ob_start();
	
	if($data[3] == 'PI Approval New'){
		echo load_html_head_contents($data[3],"../", 1, 1, $unicode,'','');
	}else{
		echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
	}
	$country_arr=return_library_array( "select id,country_name from lib_country where status_active=1", "id", "country_name");
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	
  	foreach($sql_company as $company_data)
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]].','.' ';else $country='';

		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form in(21,27) ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_full_name from user_passwd", "id", "user_full_name" );
	$buyer_library_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" );
	
	

	?>
	<div style="width:1000px">
	<?
		$cbo_pi_basis_id='';
		$sql_mst = sql_select("select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, total_amount, upcharge, discount, net_total_amount,inserted_by, goods_rcv_status, pay_term, tenor, location_id, pi_for, inserted_by, approved_by, buyer_id,lc_req_date from com_pi_master_details where id= $data[1]");
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
		$pi_basis_id=$sql_mst[0][csf("pi_basis_id")];
		$buyer_data=$buyer_library_arr[$sql_mst[0][csf("buyer_id")]];
		$pi_location_name=return_field_value("location_name","lib_location","id='".$sql_mst[0][csf("location_id")]."'");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$approved_by=$sql_mst[0][csf("approved_by")];
		$lc_req_date=$sql_mst[0][csf("lc_req_date")];
		
	 	$i = 0; $total_ammount = 0;
		
		//  echo $sql_mst[0][csf('import_pi')]; die;
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
                        <td>Pay Term</td>
                        <td><? echo $sql_mst[0][csf('pay_term')];?></td>
                        <td>Tenor</td>
                        <td><? echo $sql_mst[0][csf('tenor')];?></td>
                    </tr>
                    <tr>
                    	<td>Insert By:</td>
                        <td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                        <td>Remarks</td>
                        <td><? echo $sql_mst[0][csf('remarks')];?></td>
                    </tr>
                    <tr>
                    	<td colspan="5" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;">
							<? if($sql_mst[0][csf('approved')]==1) echo "Approved"; 
							//elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved"; else echo "&nbsp;"; 
							?>
						</td>
                    </tr>
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th style="width: 150px;">Job No</th>
                        <th style="width: 150px;">Construction</th>
                        <th style="width: 150px;">Composition</th>
                        <th style="width: 100px;">Color</th>
                        <th style="width: 50px;">GSM</th>
                        <th style="width: 80px;">Dia/Width</th>
                        <th style="width: 100px;">Item Size</th>
                        <th style="width: 50px;">UOM</th>
                        <th style="width: 100px;">Quantity</th>
                        <th style="width: 70px;">Rate</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                    <?
						if($goods_rcv_status==2)
						{
							$sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount, item_prod_id,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
						}
						else
						{
							if($db_type==0)
							{
								$sql = "select group_concat(id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id ,item_size";
							}
							else
							{
								$sql = "select rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size  from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id,item_size";
							}
						}
                       	// echo $sql;
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        ?>
                            <tr>
                                <td style="word-break: break-all;"><? echo $row[csf('work_order_no')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('construction')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('composition')]; ?></td>
                                <td style="word-break: break-all;"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('gsm')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('dia_width')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('item_size')]; ?></td>
                                <td style="word-break: break-all;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td style="word-break: break-all;" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td style="word-break: break-all;" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                                <td style="word-break: break-all;" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                            </tr>
                        <?
                        }
                        ?>
                        <tr class="tbl_bottom" height="25">
                            <td align="right" colspan="8">Sum</td>
                           	<td align="right"><? echo number_format($total_quantity,4); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($total_ammount,4); ?></td>
                        </tr>

						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Upcharge</td>
							<td><? echo number_format($upcharge,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Discount</td>
							<td><? echo number_format($discount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Net Total</td>
							<td><? echo number_format($net_total_amount,4); ?></td>
						</tr>
                    </tbody>
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
                <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by ");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


			?>
			<? if(count($approved_sql)>0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval Status </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="250">Name</th>
								<th width="200">Designation</th>
								<th width="100">Approval Date</th>
							</tr>
						</thead>
						<? foreach ($approved_sql as $key => $value)
						{
							?>
							<tr>
								<td width="20"><? echo $sl; ?></td>
								<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</table>
				</div>
				<?
			}
			?>
			<? if(count($approved_his_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="150">Approved / Un-Approved</th>
								<th width="150">Designation</th>
								<th width="50">Approval Status</th>
								<th width="150">Reason for Un-Approval</th>
								<th width="150">Date</th>
							</tr>
						</thead>
						<? foreach ($approved_his_sql as $key => $value)
						{
							if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td width="20"><? echo $sl; ?></td>
								<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="50">Yes</td>
								<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
								<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
							</tr>

							<?
							$sl++;
							if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>
								<?
								$sl++;
							}
						}
						?>
					</table>
				</div>
				<?
			}
			?>
	        <!-- //approved status end-->
	        <br/>
	            <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

	                // echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
				// 	$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
					
	            //     $userSignatureArr[$approved_by]=$path.$signature_arr[$approved_by];
                // echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);	
	             ?>

	        	</div>
	        <?
		}
		else
		{
			//echo $entryForm;die;
			$pi_order_sql=="";$pi_wo_data=array();$pi_wo_check=0;
			if($entryForm==165)//yarn
			{
				$pi_order_sql="select c.id as PO_ID from com_pi_item_details a, wo_non_order_info_dtls b, wo_po_break_down c where a.work_order_dtls_id=b.id and b.job_no=c.job_no_mst and a.item_category_id=1 and b.job_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by c.id";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.supplier_order_quantity as wo_qnty, b.rate as rate, b.amount as wo_amount from com_pi_item_details a, wo_non_order_info_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=1 and b.job_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1]";
			}
			else if($entryForm==169) // Services - Yarn Dyeing
			{
				$pi_order_sql="select c.id as PO_ID from com_pi_item_details a, wo_yarn_dyeing_dtls b, wo_po_break_down c where a.work_order_dtls_id=b.id and b.job_no=c.job_no_mst and a.item_category_id=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by c.id";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.yarn_wo_qty as wo_qnty, b.dyeing_charge as rate, b.amount as wo_amount from com_pi_item_details a, wo_yarn_dyeing_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] ";
			}
			if($entryForm==170) // Services - Embroidery, Services - Printing, Services - Wash
			{
				$pi_order_sql="select b.po_break_down_id as PO_ID from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=25 and b.booking_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by b.po_break_down_id ";
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.wo_qnty as wo_qnty, b.rate as rate, b.amount as wo_amount from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=25 and b.booking_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] ";
			}
			elseif($entryForm==166) // Knit Finish Fabrics, Woven Fabrics, Grey Fabric(Knit), Grey Fabric(woven)
			{
				$pi_order_sql="select b.po_break_down_id as PO_ID from com_pi_item_details a, wo_booking_dtls b where a.work_order_no=b.booking_no and a.item_category_id in(2,3,13,14) and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by b.po_break_down_id ";
				if($cbo_item_category_id==3)
				{
					//$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_or_width')]][$row[csf('width')]][$row[csf('cutable_width')]][$row[csf('uom')]]
					$pi_wo_sql="SELECT a.pi_id, b.booking_no, c.body_part_id, c.lib_yarn_count_deter_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, c.gsm_weight_type, b.dia_width, c.width_dia_type, c.uom, b.id as wo_dtls_id, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as wo_qnty, b.rate as rate, b.amount as wo_amount 
					from com_pi_item_details a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.work_order_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category_id in(2,3,13,14) and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] 
					group by a.pi_id, b.booking_no, c.body_part_id, c.lib_yarn_count_deter_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, c.gsm_weight_type, b.dia_width, c.width_dia_type, c.uom, b.id, b.fin_fab_qnty, b.grey_fab_qnty , b.rate, b.amount ";
					//group by a.id, a.booking_no, b.fabric_color_id, c.construction, c.composition, b.gsm_weight, c.gsm_weight_type, c.width_dia_type, b.dia_width, b.item_size, c.lib_yarn_count_deter_id, c.body_part_id, c.uom
					//echo $pi_wo_sql;die;
					$pi_wo_sql_result=sql_select($pi_wo_sql);
					if(count($pi_wo_sql_result)>0) $pi_wo_check=1;
					foreach($pi_wo_sql_result as $row)
					{
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('gsm_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('gsm_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['wo_qnty']+=$row[csf('wo_qnty')];
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('gsm_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['wo_amount']+=$row[csf('wo_amount')];
					}
					
				}
				else
				{
					//$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]]
					$pi_wo_sql="SELECT a.pi_id, b.booking_no, c.lib_yarn_count_deter_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, c.uom, b.id as wo_dtls_id, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as wo_qnty, b.rate as rate, b.amount as wo_amount 
					from com_pi_item_details a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
					where a.work_order_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category_id in(2,3,13,14) and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] ";
					$pi_wo_sql_result=sql_select($pi_wo_sql);
					if(count($pi_wo_sql_result)>0) $pi_wo_check=1;
					foreach($pi_wo_sql_result as $row)
					{
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_qnty']+=$row[csf('wo_qnty')];
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_amount']+=$row[csf('wo_amount')];
					}
				}
				
			}
			elseif($entryForm==167)//Accessories
			{
				/*$sql_trims=("select a.id, a.booking_no, b.id as dtls_id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, sum(c.cons) as qnty from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id $booking_ids_cond and c.cons>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0
				group by a.id, a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier");*/
				
				$pi_order_sql="select b.po_break_down_id as PO_ID from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=4 and b.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by b.po_break_down_id ";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.cons as wo_qnty, c.rate as rate, c.amount as wo_amount 
				from com_pi_item_details a, wo_booking_dtls b, wo_trim_book_con_dtls c 
				where a.work_order_dtls_id=b.id and b.id=c.wo_trim_booking_dtls_id and c.cons>0 and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.pi_id=$data[1] 
				group by a.pi_id, b.id, b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, c.cons, c.rate, c.amount";
				$pi_wo_sql_result=sql_select($pi_wo_sql);
				if (count($pi_wo_sql_result)>0) $pi_wo_check=1;
				foreach($pi_wo_sql_result as $row)
				{
					$pi_wo_data[$row[csf('wo_dtls_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_number_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_qnty']+=$row[csf('wo_qnty')];
					$pi_wo_data[$row[csf('wo_dtls_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_number_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_amount']+=$row[csf('wo_amount')];
				}
				//echo '<pre>';print_r($pi_wo_data);die;
				
			}
			elseif($entryForm==171) // Services Lab Test
			{
				$pi_order_sql="select c.id as PO_ID from com_pi_item_details a, wo_labtest_dtls b, wo_po_break_down c where a.work_order_dtls_id=b.id and b.job_no=c.job_no_mst and a.item_category_id=31 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by c.id";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.wo_value as wo_qnty, b.labtest_charge as rate, b.amount as wo_amount from com_pi_item_details a, wo_labtest_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=31 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1]";
			}
			
			if($entryForm != 167 && $entryForm != 166)
			{
				$pi_wo_sql_result=sql_select($pi_wo_sql);
				if(count($pi_wo_sql_result)>0) $pi_wo_check=1;
				foreach($pi_wo_sql_result as $row)
				{
					$pi_wo_data[$row[csf('wo_dtls_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
					$pi_wo_data[$row[csf('wo_dtls_id')]]['wo_amount']+=$row[csf('wo_amount')];
				}
			}
			

			$sc_lc_file=sql_select("SELECT ID, INTERNAL_FILE_NO, LC_SC_NO FROM COM_PI_MASTER_DETAILS WHERE ID=$data[1] AND STATUS_ACTIVE=1 AND IS_DELETED=0 ");
			$lc_sc_no=$sc_lc_file[0]['LC_SC_NO'];
			$ls_sc_file=$sc_lc_file[0]['INTERNAL_FILE_NO'];

			if($db_type==0)
			{
				$approval_cause=return_field_value("group_concat(approval_cause) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			else
			{
				$approval_cause=return_field_value("listagg(cast(approval_cause as varchar(4000)),',') within group(order by id) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			
			$po_qnty_pcs=0;
			if($entryForm==166 || $entryForm==167)
			{
				//$pi_order_sql
				$order_qnty_sql="select b.PO_QUANTITY*a.TOTAL_SET_QNTY as PO_QNTY_PCS from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b where a.id=b.job_id and a.status_active=1 and b.status_active=1 and b.id in($pi_order_sql)";
				//echo $order_qnty_sql;
				$order_qnty_sql_result=sql_select($order_qnty_sql);
				foreach($order_qnty_sql_result as $val)
				{
					$po_qnty_pcs+=$val["PO_QNTY_PCS"];
				}
				unset($order_qnty_sql_result);
			}
			
			?>
			<style>
				.wrd_brk{word-break: break-all;}
				.left{text-align: left;}
				.center{text-align: center;}
				.right{text-align: right;}
			</style>
			<div style="width:100%">
				<table width="100%">
					<?
					$item_category_library = return_library_array('SELECT category_id, short_name FROM lib_item_category_list','category_id','short_name');
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
							if($supplier_data[csf('country_id')]!=0)$country = $country_arr[$supplier_data[csf('country_id')]].','.' ';else $country='';

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
						   <td style="font-size:20px;" align="center" colspan="6"><strong>Proforma Invoice-(PI)</strong></td>
						</tr>
						<tr>
						   <td width="100" align="right">To</td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td width="200"><? echo $importer_name_library[$row_mst[csf('importer_id')]];?></td>
						   <td width="100"><strong>Pi No:</strong></td>
						   <td width="250"><strong><? echo $row_mst[csf('pi_number')];?></strong></td>
							<td width="150">HS Code:</td>
						   <td><? echo $row_mst[csf('hs_code')];?></td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td rowspan="3"><? echo $company_address ;?></td>
						   <td><strong> Pi Date:</strong></td>
						   <td><strong><? echo change_date_format($row_mst[csf('pi_date')]);?></strong></td>
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
                            <?
							if ($cbo_item_category_id==1){
								?>
								<td> <b>Location:</b> <? echo $pi_location_name;?></td>     
								<?
							}else{
								?>
								<td>&nbsp;</td>
								<?
							}?>
	                     	<td>Pay Term</td>
	                     	<td><? echo $pay_term[$sql_mst[0][csf("pay_term")]];?></td>
                            <td>Tenor:</td>
						   	<td><? echo $sql_mst[0][csf("tenor")];?></td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
                            <td>&nbsp;</td>
	                     	<td>LS/SC No:</td>
	                     	<td><? echo $lc_sc_no;?></td>
                            <td>File No:</td>
						   	<td><? echo $ls_sc_file;?></td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
                            <td>&nbsp;</td>
	                     	<td>Insert By: </td>
	                     	<td><? echo $user_library[$row_mst[csf('inserted_by')]]; ?></td>
							 <? if($cbo_item_category_id==1){
							?>
							<td>Buyer:</td>
						   	<td><? echo $buyer_data;?></td>
							<?
							}else{
								?>
								<td>Location:</td>
						   	<td><? echo $pi_location_name;?></td>
								<?
							}?>
						</tr>
	                     <tr>
                         	<td>&nbsp;</td>
                         	<td>&nbsp;</td>
                         	<td>Remarks:</td>
						   	<td><? echo $sql_mst[0][csf("remarks")];?></td>
						    <td> <b>Pi For:</b></td>
							<td> <b><? $piFor_array=array(1=>"BTB",2=>"Margin LC",3=>"Fund Buildup",4=>"TT/Pay Order",5=>"FTT",6=>"FDD/RTGS");
						  echo $piFor_array[$row_mst[csf('pi_for')]];
							?></b></td>

	                    </tr>
	                    <tr>
                         	<td>&nbsp;</td>
                         	<td>&nbsp;</td>
                         	<td>Category:</td>
						   	<td><? if($sql_mst[0][csf("item_category_id")]==8){
								echo "General Item";
							}else{
								echo $item_category_library[$sql_mst[0][csf("item_category_id")]];
							} ?></td>
							<td>LC required date</td>
                         	<td><?echo change_date_format($lc_req_date)?></td>
	                    </tr>
						<tr>
                         	<td>&nbsp;</td>
							<td>&nbsp;</td>
                         	<td>Order Qnty PCS:</td>
                            <td><? echo $po_qnty_pcs; ?></td>
							<td>Approval Status:</td>
							<td style="color:#FF0000; font-size:22px;">
							<? 
							if($sql_mst[0][csf('approved')]==1) echo "Approved";
							elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved";  
							else echo "Not Approved";
							?>
                            </td>
						</tr>
                    <?
					 $cbo_pi_basis_id=$row_mst[csf('pi_basis_id')];
					}
					?>
             	</table>
          	</div>
          <div style="width:1000px; margin-left:10px">
			<?
			//echo $entryForm;die;
			
            if($entryForm==165) // Yarn
            {
				//echo "SELECT a.id, work_order_no, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount, a.hs_code, a.work_order_dtls_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC";
				$data_array=sql_select("SELECT a.id, work_order_no,a.item_category_id, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount, a.hs_code, a.work_order_dtls_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
				$work_order_no=$data_array[0][csf("work_order_no")];
				if($pi_basis_id==1)
				{
					/*echo "select p.id as pi_dtls_id, max(a.id) as wo_id, max(a.wo_basis_id) as wo_basis_id, sum(b.supplier_order_quantity) as wo_qnty, sum(b.amount) as amount from com_pi_item_details p, wo_non_order_info_mst a, wo_non_order_info_dtls b 
					where p.work_order_dtls_id=b.id and a.id=b.mst_id and a.pi_id = $data[1] group by p.id";*/
					$wo_sql=sql_select("select p.id as pi_dtls_id, max(a.id) as wo_id, max(a.wo_basis_id) as wo_basis_id, sum(b.supplier_order_quantity) as wo_qnty, sum(b.amount) as amount from com_pi_item_details p, wo_non_order_info_mst a, wo_non_order_info_dtls b 
					where p.work_order_dtls_id=b.id and a.id=b.mst_id and p.pi_id = $data[1] group by p.id");
					$wo_data=array();
					foreach($wo_sql as $row)
					{
						$wo_data[$row[csf("pi_dtls_id")]]["pi_dtls_id"]=$row[csf("pi_dtls_id")];
						$wo_data[$row[csf("pi_dtls_id")]]["wo_id"]=$row[csf("wo_id")];
						$wo_data[$row[csf("pi_dtls_id")]]["wo_qnty"]=$row[csf("wo_qnty")];
						$wo_data[$row[csf("pi_dtls_id")]]["amount"]=$row[csf("amount")];
						$wo_basis_id=$row[csf("wo_basis_id")];
					}
					$work_order_no=$wo_basis_array[0][csf("wo_basis_id")];
				}
				
				//echo $wo_basis_id.test;die;
				if ($wo_basis_id==1 || $wo_basis_id==2) // requ
				{
					$buyer_style_sql=" SELECT a.id, a.job_no, a.style_ref_no, a.buyer_name, c.id as wo_dtls_id from wo_po_details_master a, wo_non_order_info_dtls c 
					where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.job_no, a.style_ref_no, a.buyer_name, c.id";
					
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						$buyer_style_array[$row[csf('wo_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('wo_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/
				}
				elseif ($wo_basis_id==3) // buyer po
				{
					$buyer_style_sql="SELECT a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number, c.id as wo_dtls_id 
					from wo_po_details_master a, wo_po_break_down b, wo_non_order_info_dtls c 
					where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id, b.po_number, c.id";
					//and a.job_no in('og-20-00236','og-20-00207')
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{

						$buyer_style_array[$row[csf('wo_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('wo_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/
				}
				else // Independent
				{

				}
				
				/*echo "<pre>";
				print_r($buyer_style_array);*/

				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
					<thead>
						<tr>
							<? 
							if( $cbo_pi_basis_id == 1 ) 
							{ 
								?>
								<th style="min-width: 80px;">WO</th>
								<th style="min-width: 80px;">Buyer</th>
								<th style="min-width: 100px;">Style Ref.</th>
								<? 
							} 
							?>
							<!-- <th style="min-width: 60px;">Cateogry</th> -->
							<th style="min-width: 60px;">HS Code</th>
							<th style="min-width: 60px;">Color</th>
							<th style="min-width: 50px;">Count</th>
							<th colspan="4" style="min-width: 120px;">Composition</th>
							<th style="min-width: 60px;">Yarn Type</th>
							<th style="min-width: 30px;">UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th style="min-width: 60px;">WO Qnty</th>
                                <th style="min-width: 60px;">WO Rate</th>
                                <th style="min-width: 60px;">WO Amount</th>
                                <?
							}
							?>
							<th style="min-width: 60px;">Quantity</th>
							<th style="min-width: 60px;">Rate</th>
							<th style="min-width: 60px;">Amount</th>
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
						$buyer_name=$buyer_arr[$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']];
						$style_no=$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<? 
							if( $cbo_pi_basis_id  == 1 ) 
							{ 
							?>
							<td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
							<td class="wrd_brk"><? echo $buyer_name; ?></td>
							<td class="wrd_brk"><? echo $style_no; ?></td>
							<? 
							} 
							?>
							<!-- <td class="wrd_brk"><? //echo $row[csf('item_category_id')]; ?></td> -->
							<td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
							<td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
							<td class="wrd_brk"><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
							<td class="wrd_brk"><? echo $composition[$row[csf('yarn_composition_item1')]]; ?></td>
							<td class="wrd_brk"><? echo $row[csf('yarn_composition_percentage1')]; ?>%</td>
							<td class="wrd_brk"><? echo $composition[$row[csf('yarn_composition_item2')]]; ?></td>
							<td class="wrd_brk"><? echo $row[csf('yarn_composition_percentage2')]; ?>%</td>
							<td class="wrd_brk">
							<? if( $row[csf('yarn_type')] != 0 ) echo $yarn_type[$row[csf('yarn_type')]]; ?>
							</td>
							<td class="wrd_brk">
							<? if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?>
							</td>
                            <? 
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_rate=$wo_data[$row[csf("id")]]["amount"]/$wo_data[$row[csf("id")]]["wo_qnty"];
								?>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_data[$row[csf("id")]]["wo_qnty"],2);  $tot_wo_quantity += $wo_data[$row[csf("id")]]["wo_qnty"];?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_data[$row[csf("id")]]["amount"],4);  $tot_wo_amt += $wo_data[$row[csf("id")]]["amount"];?></td>
                                <?
							}
							?>
							<td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
							<td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
						</tr>
						<?
					}
					?>

						<tr class="tbl_bottom" height="25">
							<?
							if($pi_basis_id==1)
							{if($pi_wo_check==1) $colspan="15"; else $colspan="12";}
							else $colspan="9";
							
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
						<td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
					</tr>
					<tr>
					<tr height="50"></tr>
				</table>
                 <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by  order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
                                   
								</tr>
							</thead>
							<? 
							foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                   
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		         <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				// echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
            
			}
            else if($entryForm==166) // Knit Finish Fabrics, Woven Fabrics, Grey Fabric(Knit), Grey Fabric(woven)
            {
				//echo $pi_basis_id."=".$pi_wo_check;die;
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 60px;">WO</th>
                            <th style="min-width: 60px;">Buyer</th>
                            <th style="min-width: 90px; max-width: 200px;">Style Ref.</th>
                            <? } ?>
                            <th style="min-width: 50px;">HS Code</th>
                            <th style="min-width: 50px;">Fabric Type</th>
                            <th style="min-width: 100px;">Construction</th>
                            <th style="min-width: 100px;">Composition</th>
                            <th style="min-width: 50px;">Color</th>
                            <th style="min-width: 30px;">GSM</th>
                            <th style="min-width: 40px;">Dia/Width</th>
                            <th style="min-width: 30px;">UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th style="min-width: 60px;">Req Qty</th>
                                <th style="min-width: 60px;">Req Rate</th>
                                <th style="min-width: 60px;">Req Amount</th>
                                <th style="min-width: 60px;">WO Qnty</th>
                                <th style="min-width: 50px;">WO Rate</th>
                                <th style="min-width: 60px;">WO Amount</th>
                                <?
							}
							?>
                            <th style="min-width: 60px;">Quantity</th>
                            <th style="min-width: 50px;">Rate</th>
                            <th style="min-width: 60px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$dtls_sql="SELECT a.id, a.pi_id, a.work_order_no, a.work_order_id, a.determination_id, a.color_id, a.fabric_construction, a.fabric_composition, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, a.hs_code, a.work_order_dtls_id, a.body_part_id, a.fab_weight, a.fab_weight_type, a.width_dia_type
					from com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC";
					//echo $dtls_sql;die;
                    $data_array=sql_select($dtls_sql);

                    $buyer_style_sql="SELECT a.work_order_dtls_id, a.work_order_no, a.body_part_id, a.determination_id, a.color_id, a.fabric_construction, a.fabric_composition, a.gsm, a.dia_width, a.uom, a.fab_weight, a.fab_weight_type, a.width_dia_type, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id as fabbomdtlsid, c.buyer_name, c.style_ref_no, c.job_no from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c where a.work_order_no=b.booking_no and b.job_no=c.job_no and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category_id = $data[3] and a.pi_id = $data[1] group by a.work_order_dtls_id, a.work_order_no, a.body_part_id,  a.determination_id, a.color_id, a.fabric_construction, a.fabric_composition, a.gsm, a.dia_width, a.uom, a.fab_weight, a.fab_weight_type, a.width_dia_type, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, c.buyer_name, c.style_ref_no, c.job_no";
					//echo $buyer_style_sql; die;
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array(); $wo_po_arr=array();
					foreach($buyer_style_data_array as $row)
					{
						if($buyer_style_array[$row[csf("work_order_no")]][$row[csf("buyer_name")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_no")]][$row[csf("buyer_name")]]=$row[csf("buyer_name")];
				            $buyer_style_array[$row[csf("work_order_no")]]["buyer_name"].=$row[csf("buyer_name")].',';
				        }
				        if($buyer_style_array[$row[csf("work_order_no")]][$row[csf("style_ref_no")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_no")]][$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
				            $buyer_style_array[$row[csf("work_order_no")]]["style_ref_no"].=$row[csf("style_ref_no")].',';
				        }
						$wo_po_arr['po'][$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
						$wo_po_arr['bomdtlsid'][$row[csf('work_order_no')]]=$row[csf('fabbomdtlsid')];
						$wo_po_arr['powo'][$row[csf('po_break_down_id')]][$row[csf('color_id')]][$row[csf('dia_width')]][$row[csf('uom')]].=$row[csf('work_order_no')].'__'.$row[csf('body_part_id')].'__'.$row[csf('determination_id')].'__'.$row[csf('fabric_construction')].'__'.$row[csf('fabric_composition')].'__'.$row[csf('fab_weight')].'__'.$row[csf('width_dia_type')].'__'.$row[csf('fab_weight_type')].'*';
					}
					
					 /*echo "<pre>";
					print_r($wo_po_arr['powo']); die;*/
					
					$poIDStr=implode(",",$wo_po_arr['po']);
					if(str_replace("'","",$poIDStr) !='')
					{
						$condition= new condition();
						if(str_replace("'","",$poIDStr) !=''){
							$condition->po_id_in("$poIDStr");
						}
						
						$condition->init();
						$fabric= new fabric($condition);
						//echo $fabric->getQuery(); die;
						
						$req_qty_arr=$fabric->getQtyArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
						$req_amount_arr=$fabric->getAmountArray_by_OrderFabriccostidGmtscolorDiaWidthAndRemarks_knitAndwoven_greyAndfinish();
						/*echo "<pre>";
						print_r($req_amount_arr['knit']['grey']); die;*/
					}
					
					$bomReqArr=array();
					foreach($wo_po_arr['po'] as $poid=>$podata)
					{
						foreach($req_qty_arr['woven']['grey'][$poid] as $fabbomid=>$fabbomdata)//Woven Fabrics
						{
							foreach($fabbomdata as $colorid=>$colordata)
							{
								foreach($colordata as $diawidth=>$diawidthdata)
								{
									foreach($diawidthdata as $remark=>$remarkdata)
									{
										foreach($remarkdata as $fuom=>$frqty)
										{
											//print_r($frqty);
											//echo $diawidth.'<br>';
											$fabstr="";
											$fabstr=array_filter(array_unique(explode("*",$wo_po_arr['powo'][$poid][$colorid][$diawidth][$fuom])));
											foreach($fabstr as $fabdata)
											{
												$exfabdata=explode("__",$fabdata);
												$piwono=$exfabdata[0];
												$body_part_id=$exfabdata[1];
												$determination_id=$exfabdata[2];
												$fabric_construction=$exfabdata[3];
												$fabric_composition=$exfabdata[4];
												$fab_weight=$exfabdata[5];
												$width_diaType=$exfabdata[6];
												$fab_weight_type=$exfabdata[7];
												$bomReqArr[$piwono][$body_part_id][$determination_id][$colorid][$fabric_construction][$fabric_composition][$fab_weight][$fab_weight_type][$diawidth][$width_diaType][$fuom]['qty']+=$frqty;
												
												$bomReqArr[$piwono][$body_part_id][$determination_id][$colorid][$fabric_construction][$fabric_composition][$fab_weight][$fab_weight_type][$diawidth][$width_diaType][$fuom]['amt']+=$req_amount_arr['woven']['grey'][$poid][$fabbomid][$colorid][$diawidth][$remark][$fuom];
											}
										}
									}
								}
							}
						}
						
						foreach($req_qty_arr['knit']['grey'][$poid] as $fabbomid=>$fabbomdata)//Knit Finish Fabrics
						{
							foreach($fabbomdata as $colorid=>$colordata)
							{
								foreach($colordata as $diawidth=>$diawidthdata)
								{
									foreach($diawidthdata as $remark=>$remarkdata)
									{
										foreach($remarkdata as $fuom=>$frqty)
										{
											//print_r($frqty);
											//echo $diawidth.'<br>';
											$fabstr="";
											$fabstr=array_filter(array_unique(explode("*",$wo_po_arr['powo'][$poid][$colorid][$diawidth][$fuom])));
											foreach($fabstr as $fabdata)
											{
												$exfabdata=explode("__",$fabdata);
												$piwono=$exfabdata[0];
												$body_part_id=$exfabdata[1];
												$determination_id=$exfabdata[2];
												$fabric_construction=$exfabdata[3];
												$fabric_composition=$exfabdata[4];
												$fab_weight=$exfabdata[5];
												$width_diaType=$exfabdata[6];
												$fab_weight_type=$exfabdata[7];
												$bomReqArr[$piwono][$body_part_id][$determination_id][$colorid][$fabric_construction][$fabric_composition][$fab_weight][$fab_weight_type][$diawidth][$width_diaType][$fuom]['qty']+=$frqty;
												
												$bomReqArr[$piwono][$body_part_id][$determination_id][$colorid][$fabric_construction][$fabric_composition][$fab_weight][$fab_weight_type][$diawidth][$width_diaType][$fuom]['amt']+=$req_amount_arr['knit']['grey'][$poid][$fabbomid][$colorid][$diawidth][$remark][$fuom];
											}
										}
									}
								}
							}
						}
					}
					
					/*echo "<pre>";
					print_r($bomReqArr['FAL-Fb-23-01165']);//  die;*/
                    $total_ammount = 0; $i = 0; $total_quantity=0;  $all_uom='';
                    foreach($data_array as $row)
                    {
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
						
						$buyer_id=chop($buyer_style_array[$row[csf("work_order_no")]]['buyer_name'],',');
                        $buyer_name=array_unique(explode(',', $buyer_id));
                        $comma_separate_buyer="";
                        foreach ($buyer_name as $key => $value) 
                        {
                            if ($comma_separate_buyer=="") 
                            {
                               $comma_separate_buyer.=$buyer_library_arr[$value];
                            }
                            else
                            {
                                $comma_separate_buyer.=','.$buyer_library_arr[$value];
                            }
                        }
                        //echo $comma_separate_buyer;
                        $style_ref=chop($buyer_style_array[$row[csf("work_order_no")]]['style_ref_no'],',');
                        $style_ref_arr=array_unique(explode(',', $style_ref));
                        $comma_separate_style="";
                        foreach ($style_ref_arr as $key => $value) 
                        {
                            if ($comma_separate_style=="") 
                            {
                               $comma_separate_style.=$value;
                            }
                            else
                            {
                                $comma_separate_style.=','.$value;
                            }
                        }
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id  == 1 ) { ?>
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <td class="wrd_brk"><? echo $comma_separate_buyer ; ?></td>
                            <td class="wrd_brk"><? echo $comma_separate_style;//$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']; ?></td>
                            <? } ?>
                            <td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
                            <!-- <td class="wrd_brk"><? echo $row[csf('fabric_type')]; ?></td> -->
                            <td class="wrd_brk"><? echo $item_category[$data[3]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('fabric_construction')]; ?></td>
                            <td class="wrd_brk"><? echo chop($row[csf('fabric_composition')],","); ?></td>
                            <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('gsm')]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('dia_width')]; ?></td>
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								if($cbo_item_category_id==3)
								{
									//if($i==1) { echo "<pre>";print_r($pi_wo_data);}
									//echo $row[csf('work_order_no')]."=".$row[csf('body_part_id')]."=".$row[csf('determination_id')]."=".$row[csf('color_id')]."=".$row[csf('fabric_construction')]."=".$row[csf('fabric_composition')]."=".$row[csf('fab_weight')]."=".$row[csf('fab_weight_type')]."test=".$row[csf('dia_width')]."=".$row[csf('width_dia_type')]."=".$row[csf('uom')]."<br>";
									$wo_qnty=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('fab_weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['fin_fab_qnty'];
									$wo_amt=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('fab_weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['wo_amount'];
								}
								else
								{
									$wo_qnty=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_qnty'];
									$wo_amt=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_amount'];
								}
								
								$bomreqQty=$bomreqrate=$bomreqAmt=0;
								$bomreqQty=$bomReqArr[$row[csf('work_order_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('fab_weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['qty'];
								$bomreqAmt=$bomReqArr[$row[csf('work_order_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('fab_weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['amt'];
								$bomreqrate=$bomreqAmt/$bomreqQty;
								$wo_rate=0;
								if($wo_amt>0 && $wo_qnty>0) $wo_rate=$wo_amt/$wo_qnty;
								?>
                                <td class="wrd_brk"  align="right"><? echo number_format($bomreqQty,2);  $tot_bomreq_quantity += $bomreqQty;?></td>
                                <td class="wrd_brk"  align="right"><? echo number_format($bomreqrate,4); ?></td>
                                <td class="wrd_brk"  align="right"><? echo number_format($bomreqAmt,4);  $tot_bomreq_amt += $bomreqAmt; ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<?
						$all_uom.=$row[csf('uom')].',';
					}
					$all_uom=array_unique(explode(",",chop($all_uom,',')));
					?>
                        <tr class="tbl_bottom">
							<?
                            if( $cbo_pi_basis_id == 1) {if($pi_wo_check)$colspan="17"; else $colspan="11";} else $colspan="8";
                            ?>
                            <td colspan="<? echo $colspan; ?>">Sum</td>
                            <td class="right"><? if(count($all_uom)==1){echo number_format($total_quantity,2);}?></td>
                            <td>&nbsp;</td>
                            <td class="right"><? echo number_format($total_ammount,4); ?></td>
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
                        <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?>
                        </td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
                                   
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                   
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		         <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<? $i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				 <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                // echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
            }
            else if($entryForm==167)//Accessories
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>   
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header" style="min-width: 60px;">WO</th>
                            <? } ?>
                            <th style="min-width: 60px;">Delivery Date</th>
                            <th style="min-width: 60px;">WO Type</th>
                            <th style="min-width: 60px;">Buyer</th>                          
                            <th style="min-width: 60px;">PO Number</th>
							<th style="min-width: 60px;">PCD</th>
                            <th style="min-width: 100px;">Style Ref.</th>
                            <th style="min-width: 50px;">HS Code</th>
                            <th style="min-width: 60px;">Item Group</th>
                            <th style="min-width: 100px;">Item Description</th>
                            <th style="min-width: 50px;">Gmts Color</th>
                            <th style="min-width: 50px;">Gmts Size</th>
                            <th style="min-width: 50px;">Item Color</th>
                            <th style="min-width: 50px;">Item Size</th>
                            <th style="min-width: 30px;">UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th style="min-width: 60px;">Req Qty</th>
                                <th style="min-width: 60px;">Req Rate</th>
                                <th style="min-width: 60px;">Req Amount</th>
                                <th style="min-width: 60px;">WO Qnty</th>
                                <th style="min-width: 50px;">WO Rate</th>
                                <th style="min-width: 60px;">WO Amount</th>
                                <?
							}
							?>
                            <th style="min-width: 60px;">Quantity</th>
                            <th style="min-width: 50px;">Rate</th>
                            <th style="min-width: 60px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					if($goods_rcv_status==2)
					{
						$data_array=sql_select("SELECT a.id, a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, a.item_prod_id, a.hs_code, a.brand_supplier,a.order_no, a.work_order_id, a.order_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
					}
					else
					{
						if($db_type==0)
						{
							$data_array=sql_select("SELECT group_concat(a.id) as id, a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0
							group by a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no
							ORDER BY a.id ASC");
						}
						else
						{
							$data_array=sql_select("SELECT rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no, a.work_order_id, a.order_id 
							FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0
							group by a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no, a.work_order_id, a.order_id");
						}
					}
					$wo_dtls_id_all_arr=array(); 
					foreach($data_array as $row){
						$wo_dtls_id_all_arr[$row[csf('work_order_dtls_id')]]=$row[csf('work_order_dtls_id')];
                    }
					
					if(count($wo_dtls_id_all_arr)>0)
					{
						$wo_dtls_id_all=implode(",",$wo_dtls_id_all_arr);
						$prev_pi_sql="SELECT a.id, a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, a.item_prod_id, a.hs_code, a.brand_supplier,a.order_no, a.work_order_id, a.order_id 
						FROM com_pi_item_details a 
						WHERE a.pi_id <> $data[1] and a.work_order_dtls_id in($wo_dtls_id_all)  and a.status_active=1 and a.is_deleted=0";
						$prev_pi_sql=sql_select($prev_pi_sql);
	
						$is_sort_array=return_library_array( "SELECT id, is_short FROM wo_booking_dtls WHERE  status_active = 1 AND is_deleted = 0 and id in($wo_dtls_id_all) ",'id','is_short');
						$wo_po=sql_select("SELECT a.id as booking_dtlsid, a.booking_no, a.trim_group, a.sensitivity, a.po_break_down_id, a.pre_cost_fabric_cost_dtls_id as bomdtlsid, c.delivery_date, c.id, b.style_ref_no, b.buyer_name FROM wo_booking_dtls a, wo_po_details_master b, wo_booking_mst c where a.job_no=b.job_no and a.booking_mst_id=c.id and b.company_name=$data[0] and a.id in($wo_dtls_id_all) and a.status_active=1");
						$wo_ord_arr=array(); $wo_po_arr=array();
						$del_date_arr=array();
						foreach($wo_po as $row)
						{
							$wo_ord_arr[$row[csf('booking_dtlsid')]]['sensitivity']=$row[csf('sensitivity')];
							$wo_ord_arr[$row[csf('booking_dtlsid')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
							$wo_ord_arr[$row[csf('booking_dtlsid')]]['buyer_name'].=$buyer_library_arr[$row[csf('buyer_name')]].',';
							$wo_po_arr['po'][$row[csf('booking_dtlsid')]]=$row[csf('po_break_down_id')];
							$wo_po_arr['bomdtlsid'][$row[csf('booking_dtlsid')]]=$row[csf('bomdtlsid')];
							$del_date_arr[$row[csf("id")]]["delivery_date"]=$row[csf("delivery_date")];
						}
						
						$sqlWoCons="SELECT WO_TRIM_BOOKING_DTLS_ID, PO_BREAK_DOWN_ID, COLOR_NUMBER_ID, GMTS_SIZES, ITEM_COLOR, ITEM_SIZE, DESCRIPTION, BRAND_SUPPLIER, BOM_ITEM_COLOR, BOM_ITEM_SIZE FROM WO_TRIM_BOOK_CON_DTLS WHERE STATUS_ACTIVE=1 and IS_DELETED=0 and WO_TRIM_BOOKING_DTLS_ID in($wo_dtls_id_all)";
						//echo $sqlWoCons;
						$sqlWoConsArr=sql_select($sqlWoCons); $piwodatastrArr=array();
						foreach($sqlWoConsArr as $cnrow)
						{
							$piwodatastrArr[$cnrow['WO_TRIM_BOOKING_DTLS_ID']][$cnrow['PO_BREAK_DOWN_ID']].=$cnrow['COLOR_NUMBER_ID'].'__'.$cnrow['GMTS_SIZES'].'__'.$cnrow['BOM_ITEM_COLOR'].'__'.$cnrow['BOM_ITEM_SIZE'].'__'.$cnrow['ITEM_COLOR'].'__'.$cnrow['ITEM_SIZE'].'__'.$cnrow['DESCRIPTION'].'*';
						}
						unset($sqlWoConsArr);
						/*echo "<pre>";
						print_r($piwodatastrArr); die;*/
						
						$poIDStr=implode(",",$wo_po_arr['po']);
						
						$condition= new condition();
						$req_qtyasper_arr=$req_amountasper_arr=$req_qtysize_arr=$req_amountsize_arr=$req_qtycolorsize_arr=$req_amountcolorsize_arr=$req_qtyall_arr=$req_amountall_arr=array();
                        if(str_replace("'","",$poIDStr) !='')
						{
							$condition->po_id_in("$poIDStr");
							$condition->init();
							$trims= new trims($condition);
							//echo $trims->getQuery(); die;
							
							$req_qtyasper_arr=$trims->getQtyArray_by_orderPrecostdtlsidAndGmtscolor();//As per Gmts. Color and Contrast Color
							$req_amountasper_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidAndGmtscolor();//As per Gmts. Color and Contrast Color
							
							$req_qtysize_arr=$trims->getQtyArray_by_orderPrecostdtlsidGmtssizeItemsizeAndArticle();//Size Sensitive
							$req_amountsize_arr=$trims->getAmountArray_by_orderAndPrecostdtlsidGmtssizeItemsizeAndArticle();//Size Sensitive
							
							$req_qtycolorsize_arr=$trims->getQtyArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();//Color & Size Sensitive
							$req_amountcolorsize_arr=$trims->getAmountArray_by_OrderPrecostdtlsidGmtscolorGmtssizeAndArticleItemColorItemSize();//Color & Size Sensitive
							
							$req_qtyall_arr=$trims->getQtyArray_by_orderAndPrecostdtlsid();//--Select--
							$req_amountall_arr=$trims->getAmountArray_by_orderAndPrecostdtlsid();//--Select--
                        }
						
                        
						
						/*echo "<pre>";
						print_r($req_qtycolorsize_arr['83563'][73903]);die;*/
						$bomReqArr=array();
						foreach($wo_po_arr['po'] as $bookingid=>$poid)
						{
							$exwodata=$powodatastr="";
							$bomdtlsid=$wosensitivity=0;
							
							$bomdtlsid=$wo_po_arr['bomdtlsid'][$bookingid];
							//echo $bookingid."=".$poid."=".$bomdtlsid.'<br>';
							
							$wosensitivity=$wo_ord_arr[$bookingid]['sensitivity'];
							$piwodatastr=array_filter(array_unique(explode('*',$piwodatastrArr[$bookingid][$poid])));
							
							foreach($piwodatastr as $piwodata)
							{
								$expidata=$itemdescription="";
								$gmtcolorid=$gmtsizeid=$bomitemcolorid=$bomitemsizeid=$itemcolorid=$itemsizeid=0;
								$expidata=explode('__',$piwodata);
								
								$gmtcolorid=$expidata[0];
								$gmtsizeid=$expidata[1];
								$bomitemcolorid=$expidata[2];
								$bomitemsizeid=$expidata[3];
								$itemcolorid=$expidata[4];
								$itemsizeid=$expidata[5];
								$itemdescription=$expidata[6];
								
								$indexstr="";
								
								$indexstr=$gmtcolorid.'_'.$itemcolorid.'_'.$gmtsizeid.'_'.$itemsizeid;
								
								if($wosensitivity==1 || $wosensitivity==3)//As per Gmts. Color || Contrast Color
								{
									$bomReqArr[$bookingid][$indexstr]['qty']+=$req_qtyasper_arr[$poid][$bomdtlsid][$gmtcolorid];
									$bomReqArr[$bookingid][$indexstr]['amt']+=$req_amountasper_arr[$poid][$bomdtlsid][$gmtcolorid];
								}
								else if($wosensitivity==2)//Size Sensitive
								{
									//if($bookingid==620131) echo $indexstr.'='.array_sum($req_qtysize_arr[$poid][$bomdtlsid][$gmtsizeid][$itemsizeid]).'=<br>';
									$bomReqArr[$bookingid][$indexstr]['qty']+=array_sum($req_qtysize_arr[$poid][$bomdtlsid][$gmtsizeid][$bomitemsizeid]);
									$bomReqArr[$bookingid][$indexstr]['amt']+=array_sum($req_amountsize_arr[$poid][$bomdtlsid][$gmtsizeid][$bomitemsizeid]);
								}
								else if($wosensitivity==4)//Contrast Color
								{
									foreach($req_qtycolorsize_arr[$poid][$bomdtlsid][$gmtcolorid][$gmtsizeid] as $artno=>$artdata)
									{
										if($bookingid==620133) 
										{
											//print_r($artdata);
										//echo $indexstr.'_'.$bomitemcolorid.'_'.$bomitemsizeid.'='.$req_amountcolorsize_arr[$poid][$bomdtlsid][$gmtcolorid][$gmtsizeid][$artno][$bomitemcolorid][$bomitemsizeid].'=<br>';
										}
										$bomReqArr[$bookingid][$indexstr]['qty']+=$artdata[$bomitemcolorid][$bomitemsizeid];
										$bomReqArr[$bookingid][$indexstr]['amt']+=$req_amountcolorsize_arr[$poid][$bomdtlsid][$gmtcolorid][$gmtsizeid][$artno][$bomitemcolorid][$bomitemsizeid];
									}
								}
								else//All
								{
									$bomReqArr[$bookingid][$indexstr]['qty']+=$req_qtyall_arr[$poid][$bomdtlsid];
									$bomReqArr[$bookingid][$indexstr]['amt']+=$req_amountall_arr[$poid][$bomdtlsid];
								}
							}
						}
					}
					/*echo "<pre>";
					print_r($bomReqArr['620133']['7_7_1_S']);die;*/
					if($db_type==0) $style_grpCond="group_concat(style_des)";
					else if($db_type==2) $style_grpCond="listagg(cast(style_des as varchar(4000)),',') within group (order by style_des)";
					
					$wo_nonOrd_arr = return_library_array('SELECT booking_no, $style_grpCond as style_ref FROM wo_non_ord_samp_booking_dtls where status_active=1 group by booking_no','booking_no','style_ref');

					$pcd_data=sql_select("SELECT task_start_date,po_number_id  FROM tna_plan_actual_history  where status_active=1 and task_number=81");

					$pcd_arr=array();
					foreach($pcd_data as $row){
						$pcd_arr[$row[csf("po_number_id")]]["task_start_date"]=$row[csf("task_start_date")];
					}

					$pcd_tna_data=sql_select("SELECT task_start_date,po_number_id  FROM tna_process_mst  where status_active=1 and task_number=81");

					$pcd_tna_arr=array();
					foreach($pcd_tna_data as $row){
						$pcd_tna_arr[$row[csf("po_number_id")]]["task_start_date"]=$row[csf("task_start_date")];
					}
					

					$wo_nonPo=sql_select("SELECT a.booking_no, b.style_des, a.buyer_id FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$data[0] and a.status_active=1");
					$wo_nonOrd_buyer_arr=array();
					foreach($wo_nonPo as $row)
					{
						$wo_nonOrd_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_id')]].',';
					}
					/*echo "<pre>";
					print_r($wo_nonOrd_buyer_arr);die;*/


                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row)
                    {
                    	if($db_type==2 && $goods_rcv_status !=2) $row[csf('id')] = $row[csf('id')]->load();
                    	$wo_style_no=''; $wo_buyer_name='';
						
						if($row[csf('booking_without_order')]==1) 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_nonOrd_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_nonOrd_buyer_arr[$row[csf('work_order_no')]])));
						}
						else 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_dtls_id')]]['style_ref_no'])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_dtls_id')]]['buyer_name'])));
						} 
						if($is_sort_array[$row[csf('work_order_dtls_id')]]==1 ){
							$wo_title="Short";
						}
						if($is_sort_array[$row[csf('work_order_dtls_id')]]==2 ){
							$wo_title="Main Booking";
						}

						if($pcd_arr[$row[csf("order_id")]]["task_start_date"]==""){
							$pcd_data=$pcd_tna_arr[$row[csf("order_id")]]["task_start_date"];
						}else{
							$pcd_data=$pcd_arr[$row[csf("order_id")]]["task_start_date"];
						}
						
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td class="wrd_brk"><? echo change_date_format($del_date_arr[$row[csf("work_order_id")]]["delivery_date"]) ?></td>
                            <td class="wrd_brk"><? echo $wo_title; ?></td>
                            <td class="wrd_brk"><? //echo $arrayName[$wo_ord_buyer_arr[$row[csf('work_order_no')]]];
                            	echo rtrim($wo_buyer_name,','); ?></td>                          
                            <td class="wrd_brk"><? echo $row["ORDER_NO"]; ?></td>
							<td class="wrd_brk"><? echo change_date_format($pcd_data) ?></td>
                            <td class="wrd_brk"><? echo rtrim($wo_style_no,','); ?></td>
                            <td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
                            <td class="wrd_brk"><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_description')]; ?></td>
                            <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td class="wrd_brk"><? echo $size_library[$row[csf('size_id')]]; ?></td>
                            <td class="wrd_brk"><? echo $color_library[$row[csf('item_color')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_size')]; ?></td>
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_qnty=$pi_wo_data[$row[csf('work_order_dtls_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_qnty'];
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_amount'];
								
								$wo_rate=$wo_amt/$wo_qnty;
								
								$indexpistr='';
								$indexpistr=$row[csf('color_id')].'_'.$row[csf('item_color')].'_'.$row[csf('size_id')].'_'.$row[csf('item_size')];
								
								//if($row[csf('work_order_dtls_id')]==620133) echo $indexpistr.'<br>';
								
								$bomreqQty=$bomReqArr[$row[csf('work_order_dtls_id')]][$indexpistr]['qty'];
								$bomreqAmt=$bomReqArr[$row[csf('work_order_dtls_id')]][$indexpistr]['amt'];
								$bomreqrate=$bomreqAmt/$bomreqQty;
								?>
                                <td class="wrd_brk"  align="right" title="<?=$row[csf('work_order_dtls_id')]; ?>"><? echo number_format($bomreqQty,2);  $tot_bomreq_quantity += $bomreqQty;?></td>
                                <td class="wrd_brk"  align="right"><? echo number_format($bomreqrate,4); ?></td>
                                <td class="wrd_brk"  align="right"><? echo number_format($bomreqAmt,4);  $tot_bomreq_amt += $bomreqAmt; ?></td>
                                <td class="wrd_brk"  align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<?
                    }
                    ?>
                        <tr class="tbl_bottom">
							<?
                            if( $cbo_pi_basis_id == 1) {if ($pi_wo_check) $colspan="21"; else $colspan="13";} else $colspan="12";
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
		                    <td><?

								if($sql_mst[0][csf('currency_id')]*1==1)
								{
									echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

								}
								else
								{
									echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
								}
								?>
							</td>
	                    </tr>
	                <tr>
                    <tr height="50"></tr>
				</table>
                 <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,approved_date   from approval_history where entry_form in (21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by,approved_date  order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
                                   
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                   
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>

				</div>
		        <br/>
				 <?					
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				//$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);	

            }
            else if($entryForm==168) //Services - Fabric
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 150px;">WO</th>
                            <? } ?>
                            <th style="min-width: 100px;">Buyer</th>
                            <th style="min-width: 100px;">Style</th>
                            <th style="min-width: 100px;">HS Code</th>
                            <th style="min-width: 100px;">Service Type</th>
                            <th style="min-width: 200px;">Item Description</th>
                            <th style="min-width: 60px;">UOM</th>
                            <th style="min-width: 80px;">Quantity</th>
                            <th style="min-width: 80px;">Rate</th>
                            <th style="min-width: 80px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.item_description,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active, a.hs_code,a.work_order_dtls_id FROM com_pi_item_details a WHERE  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");

					$buyer_style_sql="SELECT a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no 
					from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c 
                    where a.work_order_no=b.booking_no and b.job_no=c.job_no and a.is_deleted=0 and a.status_active=1 
                    and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category_id = $data[3] and a.pi_id = $data[1]
                    group by a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no";
					//  echo $buyer_style_sql;
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						if($buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("buyer_name")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("buyer_name")]]=$row[csf("buyer_name")];
				            $buyer_style_array[$row[csf("work_order_dtls_id")]]["buyer_name"].=$row[csf("buyer_name")].',';
				        }
				        if($buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("style_ref_no")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
				            $buyer_style_array[$row[csf("work_order_dtls_id")]]["style_ref_no"].=$row[csf("style_ref_no")].',';
				        }
					}
				   
				    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row)
                    {
						$buyer_id=chop($buyer_style_array[$row[csf("work_order_dtls_id")]]['buyer_name'],',');
                        $buyer_name=array_unique(explode(',', $buyer_id));
                        $comma_separate_buyer="";
                        foreach ($buyer_name as $key => $value) 
                        {
                            if ($comma_separate_buyer=="") 
                            {
                               $comma_separate_buyer.=$buyer_library_arr[$value];
                            }
                            else
                            {
                                $comma_separate_buyer.=','.$buyer_library_arr[$value];
                            }
                        }
                        //echo $comma_separate_buyer;
                        $style_ref=chop($buyer_style_array[$row[csf("work_order_dtls_id")]]['style_ref_no'],',');
                        $style_ref_arr=array_unique(explode(',', $style_ref));
                        $comma_separate_style="";
                        foreach ($style_ref_arr as $key => $value) 
                        {
                            if ($comma_separate_style=="") 
                            {
                               $comma_separate_style.=$value;
                            }
                            else
                            {
                                $comma_separate_style.=','.$value;
                            }
                        }
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=7; ?>
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=6; ?>
                            <td class="wrd_brk"><? echo $comma_separate_buyer; ?></td>
                            <td class="wrd_brk"><? echo $comma_separate_style; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
                            <!-- <td class="wrd_brk"><? echo $service_type[$row[csf('service_type')]]; ?></td> -->
                            <td class="wrd_brk"><? echo $conversion_cost_head_array[$row[csf('service_type')]]; ?></td>
                            <td class="wrd_brk"><? echo ($row[csf('item_description')]); ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


					?>
				<? if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="250">Name</th>
									<th width="200">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
				 <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
					 <?
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);			
				
            }
            else if($entryForm==169) // Services - Yarn Dyeing
            {
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 90px;">WO</th>
                            <th style="min-width: 90px;">Buyer</th>
                            <th style="min-width: 100px;">Style Ref.</th>

                            <? } ?>
                            <!-- <th style="min-width: 50px;">Category</th> -->
                            <th style="min-width: 50px;">HS Code</th>
                            <th style="min-width: 50px;">Lot</th>
                            <th style="min-width: 50px;">Count</th>
                            <th style="min-width: 100px;">Yarn Description</th>
                            <th style="min-width: 50px;">Yarn Color</th>
                            <th style="min-width: 50px;">Color Range</th>
                            <th style="min-width: 30px;">UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th style="min-width: 60px;">WO Qnty</th>
                                <th style="min-width: 50px;">WO Rate</th>
                                <th style="min-width: 60px;">WO Amount</th>
                                <?
							}
							?>
                            <th style="min-width: 60px;">Quantity</th>
                            <th style="min-width: 50px;">Rate</th>
                            <th style="min-width: 60px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$item_category_library = return_library_array('SELECT category_id, short_name FROM lib_item_category_list','category_id','short_name');
					$data_array=sql_select("SELECT work_order_no, lot_no, count_name, item_description, yarn_color, color_range, uom,quantity, rate, amount, hs_code, work_order_dtls_id,item_category_id
					FROM com_pi_item_details 
					WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 
					ORDER BY id ASC");

					$buyer_style_sql="SELECT a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no from com_pi_item_details a, wo_yarn_dyeing_dtls b, wo_po_details_master c 
					where a.work_order_dtls_id=b.id and b.job_no=c.job_no and a.pi_id = $data[1] and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
					and a.item_category_id = 24
					group by a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no";
					
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/

                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row)
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? 
							if( $cbo_pi_basis_id == 1 ) 
							{ 
								{if($pi_wo_check)$colspan="13"; else $colspan="9";}
								?>
								<td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
								<td class="wrd_brk"><? echo $buyer_library_arr[$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']]; ?></td>
								<td class="wrd_brk"><? echo $buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']; ?></td>
								<? 
							} else $colspan=7; 
							?>
                            <!-- <td class="wrd_brk"><? //echo $item_category_library[$row[csf('item_category_id')]]; ?></td> -->
                            <td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('lot_no')]; ?></td>
                            <td class="wrd_brk"><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_description')]; ?></td>
                            <td class="wrd_brk"><? echo $color_library[$row[csf('yarn_color')]]; ?></td>
                            <td class="wrd_brk"><? echo $color_range[$row[csf('color_range')]]; ?></td>
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_qnty=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_qnty'];
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_amount'];
								
								$wo_rate=$wo_amt/$wo_qnty;
								?>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in (21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
                                  
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
            }
            else if($entryForm==170) // Services - Embroidery, Services - Printing, Services - Wash
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 90px;">WO</th>
                            <th style="min-width: 90px;">Buyer</th>
                            <th style="min-width: 100px;">Style Ref.</th>
                            <? } ?>
                            <th style="min-width: 60px;">HS Code</th>
                            <th style="min-width: 60px;">Gmts Item</th>
                            <th style="min-width: 80px;">Embellishment Name</th>
                            <th style="min-width: 80px;">Embellishment Type</th>
                            <th style="min-width: 60px;">Gmts Color</th>
                            <th style="min-width: 40px;">UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th style="min-width: 60px;">WO Qnty</th>
                                <th style="min-width: 50px;">WO Rate</th>
                                <th style="min-width: 60px;">WO Amount</th>
                                <?
							}
							?>
                            <th style="min-width: 60px;">Quantity</th>
                            <th style="min-width: 50px;">Rate</th>
                            <th style="min-width: 60px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT work_order_no, gmts_item_id, embell_name, embell_type, color_id, uom, quantity, rate, amount, net_pi_amount,hs_code, work_order_dtls_id FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");

                    $buyer_style_sql="SELECT a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no, 3 as type
					from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c 
					where a.work_order_dtls_id = b.id and b.job_no = c.job_no and a.pi_id = $data[1] and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 
					and a.item_category_id in(25) group by a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no";
					
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/


                    $i = 0; $total_ammount = 0; $total_quantity=0;
					$total_net_pi_amount=0;
                    foreach($data_array as $row)
                    {
						$emb_arr=array();
						if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
						else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
						else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
						else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
						else $emb_arr=$blank_array;
						$total_net_pi_amount += $row[csf('net_pi_amount')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? if( $cbo_pi_basis_id == 1 ) 
							{ 
								if ($pi_wo_check) $colspan="12"; else $colspan="9";
								?>
								<td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
								<td class="wrd_brk"><? echo $buyer_library_arr[$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']]; ?></td>
								<td class="wrd_brk"><? echo $buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']; ?></td>
								<? 
							} else $colspan=6; ?>
                            <td class="wrd_brk"><? echo $garments_item[$row[csf('hs_code')]]; ?></td>
                            <td class="wrd_brk"><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                            <td class="wrd_brk"><? echo $emblishment_name_array[$row[csf('embell_name')]]; ?></td>
                            <td class="wrd_brk"><? echo $emb_arr[$row[csf('embell_type')]]; ?></td>
                            <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_qnty=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_qnty'];
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_amount'];
								
								$wo_rate=$wo_amt/$wo_qnty;
								?>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
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
                            <td><? echo number_format($total_net_pi_amount,4); ?></td>
                        </tr>
                    </tbody>
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in (21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
                                   
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
             ?>
				<?
            }
			else if($entryForm==171) // Services Lab Test
            {
				$test_item_arr=return_library_array( "SELECT id,test_item FROM lib_lab_test_rate_chart",'id','test_item');
				$buyer_arr=return_library_array( "SELECT id,buyer_name FROM lib_buyer",'id','buyer_name');

				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? 
							if ( $cbo_pi_basis_id == 1) $colspan=9;
							else if ( $cbo_pi_basis_id == 2) $colspan=6;
							else $colspan=7;
							if( $cbo_pi_basis_id == 1 ) {  ?>
                            <th style="min-width: 100px;">WO</th>
                            <th style="min-width: 100px;">Buyer</th>
                            <? } ?>
                            <th style="min-width: 100px;">HS Code</th>
                            <th style="min-width: 100px;">Style Ref.</th>
                            <th style="min-width: 100px;">Test For</th>
                            <th style="min-width: 100px;">Remarks</th>
                            <th style="min-width: 100px;">Color</th>
                            <th style="min-width: 100px;">Test Item</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th style="min-width: 100px;">WO Amount</th>
                                <?
							}
							?>
                            <th style="min-width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    if( $cbo_pi_basis_id == 2 )  //Independent
                    {
                    	$data_array=sql_select("SELECT a.work_order_no, a.test_for, a.test_item_id, a.remarks, a.color_id, a.amount, a.hs_code FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
                    }
                    else 
                    {
                    	$data_array=sql_select("SELECT b.id as labtest_mst_id,  a.work_order_no, a.test_for, a.test_item_id, a.remarks, a.color_id, a.amount, a.hs_code, a.work_order_dtls_id FROM com_pi_item_details a,WO_LABTEST_MST b WHERE a.work_order_no=b.labtest_no and  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
                    }                    

                    $i = 0; $total_ammount = 0;
                    foreach($data_array as $row)
                    {
						$mst_id=$row[csf('labtest_mst_id')];
						if ($mst_id != ''){
							$job_no_sql=sql_select("SELECT job_no FROM  WO_LABTEST_DTLS where mst_id='$mst_id'");
						}
						
						//echo "SELECT job_no FROM  WO_LABTEST_DTLS where mst_id='$mst_id'";
						$style_ref_no=''; $buyerName='';
						$unique_data=array(); $buyer_unique_data=array();
						foreach($job_no_sql as $job_no_value)
						{
							$job_no=$job_no_value[csf('job_no')];
							if ($job_no !=''){
								$style_ref_no_sql=sql_select("SELECT style_ref_no, buyer_name FROM  WO_PO_DETAILS_MASTER  where job_no='$job_no'");
							}
							
							
							$style_ref_no_get=$style_ref_no_sql[0][csf('style_ref_no')];
							$buyer_name_get=$buyer_arr[$style_ref_no_sql[0][csf('buyer_name')]];
							if(!in_array($style_ref_no_get,$unique_data))
							{
								array_push($unique_data,$style_ref_no_get);
								$style_ref_no.=$style_ref_no_get.',';
							}
							if(!in_array($buyer_name_get,$buyer_unique_data))
							{
								array_push($buyer_unique_data,$buyer_name_get);
								$buyerName.=$buyer_name_get.',';
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
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <td class="wrd_brk"><? echo chop($buyerName,','); ?></td>
                            <? } ?>
                            <td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
                            <td class="wrd_brk"><? echo $style_ref_no; ?></td>
                            <td class="wrd_brk"><? echo $test_for[$row[csf('test_for')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('remarks')]; ?></td>
                            <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td class="wrd_brk"><? echo $test_item; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_amount'];
								?>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
                            <td  class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
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
                    <td><?
						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?>
                        </td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                <?

				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in (21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
                                   
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                   
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
             ?>
				<?
            }
            else if($entryForm==227) // Chemicals, Dyes, Auxilary Chemicals, Dyes Chemicals & Auxilary Chemicals
            {
				
				
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header" style="min-width: 100px;">WO</th>
                            <? } ?>
                            <th class="header" style="min-width: 100px;">Item Category</th>
                            <th class="header" style="min-width: 100px;">HS Code</th>
                            <th class="header" style="min-width: 140px;">Item Group</th>
                            <th class="header" style="min-width: 200px;">Item Description</th>
                            <th class="header" style="min-width: 100px;">Item Size</th>
                            <th class="header" style="min-width: 60px;">UOM</th>
                            <th class="header" style="min-width: 100px;">Quantity</th>
                            <th class="header" style="min-width: 100px;">Rate</th>
                            <th class="header" style="min-width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id, a.work_order_no, a.color_id, a.item_group, a.item_description, a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active,a.item_size, a.hs_code, a.item_category_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");

                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');

                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row)
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
							<td class="wrd_brk"><?  if($row[csf('item_category_id')]==101){echo "Raw Material";}else{
								echo $general_item_category[$row[csf('item_category_id')]];} ?></td>
                            <td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
                            <td class="wrd_brk"><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_description')]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_size')]; ?></td>
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                            <td class="wrd_brk" align="right"><? echo $row[csf('rate')]; ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<?
					}
					?>
                        <tr class="tbl_bottom" height="25">
							<?
                            if( $cbo_pi_basis_id == 1) $colspan="7"; else $colspan="6";
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in (21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by ");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


				?>
				<? if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="250">Name</th>
									<th width="200">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		       <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
					 <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
             ?>
				<?
            }
            else // Others
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 100px;">WO</th>
                            <? }?>
                            <th style="min-width: 100px;">Item Category</th>
                            <th style="min-width: 100px;">HS Code</th>
                            <th style="min-width: 150px;">Item Group</th>
                            <th style="min-width: 200px;">Item Description</th>
                            <th style="min-width: 100px;">Item Size</th>
                            <th style="min-width: 50px;">UOM</th>
                            <th style="min-width: 100px;">Quantity</th>
                            <th style="min-width: 100px;">Rate</th>
                            <th style="min-width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.item_group,a.item_description,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active,a.item_size, a.hs_code, a.item_category_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");

                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row)
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <? }  ?>
                            <td class="wrd_brk"><?  if($row[csf('item_category_id')]==101){echo "Raw Material";}else{
								echo $general_item_category[$row[csf('item_category_id')]];} ?></td>
                            <td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
                            <td class="wrd_brk"><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_description')]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_size')]; ?></td>
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<?
                    }
                    ?>
                        <tr class="tbl_bottom" height="25">
							<?
                            if( $cbo_pi_basis_id == 1) $colspan="7"; else $colspan="6";
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in (21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


				?>
				<? if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="250">Name</th>
									<th width="200">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <!-- //approved status end-->
				<br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$sys_id' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		             <br>
					 <?
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
             ?>

				<?
            }
            ?>
          </div>
        	<?
		}
		?>
	</div>
	<?
	
	$html=ob_get_contents();
	ob_clean();

	if($is_mail_send==1){

		$toArr=array();
		$toArr=return_library_array("SELECT id,USER_EMAIL from user_passwd where id=$inserted_by", "id", "USER_EMAIL");
		if($mailAddress){$toArr[]=$mailAddress;}
 
		//require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');

		$image_arr = return_library_array("select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID='$sys_id'",'ID','IMAGE_LOCATION');
		$att_file_arr=array();
		foreach($image_arr as $file){
			$att_file_arr[] = '../../../'.$file.'**'.$file;
		}

			 
		/* 
		 $WORK_ORDER_NO_ARR=array();
		 foreach($data_array as $row){
			$WORK_ORDER_NO_ARR[$row[WORK_ORDER_NO]]=$row[WORK_ORDER_NO]; 
		 }

		$team_leader_arr = return_library_array("select id,TEAM_LEADER_EMAIL from lib_marketing_team where project_type=1 and team_type in (0,1) and status_active =1 and is_deleted=0 order by TEAM_LEADER_EMAIL",'id','TEAM_LEADER_EMAIL');
		
		$team_member_arr = return_library_array("select id,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by TEAM_MEMBER_EMAIL",'id','TEAM_MEMBER_EMAIL');

		 $sql_team_mail="
		SELECT b.TEAM_LEADER,b.DEALING_MARCHANT  FROM WO_BOOKING_DTLS a,  WO_PO_DETAILS_MASTER b WHERE a.JOB_NO = b.JOB_NO and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 ".where_con_using_array($WORK_ORDER_NO_ARR,1,'a.BOOKING_NO')."";
		$sql_team_mail_result=sql_select($sql_team_mail);
		
		foreach($sql_team_mail_result as $rows){
			if($team_leader_arr[$rows[TEAM_LEADER]]){$toArr[]=$team_leader_arr[$rows[TEAM_LEADER]];}
			if($team_member_arr[$rows[DEALING_MARCHANT]]){$toArr[]=$team_member_arr[$rows[DEALING_MARCHANT]];}
		}
		*/

		$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=63 and b.mail_user_setup_id=c.id and a.company_id=$company_id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.MAIL_TEMPLATE<>1";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if($row['EMAIL_ADDRESS']){$toArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS']; }
		}

	 
		$team_email_arr = return_library_array("
		select ID,TEAM_MEMBER_EMAIL from LIB_MKT_TEAM_MEMBER_INFO where team_id in(select a.id from LIB_MARKETING_TEAM a,LIB_MKT_TEAM_MEMBER_INFO b where a.id=b.team_id and b.USER_TAG_ID=$inserted_by and TEAM_MEMBER_EMAIL is not null)  and STATUS_ACTIVE=1 and IS_DELETED=0 and TEAM_MEMBER_EMAIL is not null",'ID','TEAM_MEMBER_EMAIL');
		if(count($team_email_arr)){
			$team_email_str = implode(',',$team_email_arr);
			$toArr[$team_email_str]=$team_email_str;
		}

		//$toArr=array();

		$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL,b.ITEM_CATE_ID  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.ENTRY_FORM in(27) and a.company_id=$company_id and b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and a.IS_DELETED=0 order by a.SEQUENCE_NO"; //21,

		 //echo $elcetronicSql;die;
 
		 
		$elcetronicSqlRes=sql_select($elcetronicSql);
		foreach($elcetronicSqlRes as $rows){


			if($rows['ITEM_CATE_ID'] != ''){
				$ITEM_CATE_ID_ARR = explode(',',$rows['ITEM_CATE_ID']);
				foreach($ITEM_CATE_ID_ARR as $item_cat_id){  
					if($rows['USER_EMAIL']!='' && $item_cat_id == $cbo_item_category_id){
						$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];
					} 
				}
				if($rows['BYPASS']==2){break;}
			}
			else{
				if($rows['USER_EMAIL']){$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
				if($rows['BYPASS']==2){break;}
			}
		
		}
		
		
		$to = implode(',',array_unique(explode(',',implode(',',$toArr))));
		 //echo $to;die;
	 
		
		$subject="Pro Forma Invoice";
		$header=mailHeader();
		if($to!=""){
			echo "Send to:".$to." ".sendMailMailer( $to, $subject, $html, $from_mail,$att_file_arr );
		}
		
	}
	else{echo $html;}
    exit();
}

if($action=="print_pi")
{
	// echo "Hello";die;
	$data = explode('*',$data);
	// print_r($data);
	//$item_category_id=$data[2];
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$company_location_arr = return_library_array("SELECT id, location_name from lib_location WHERE company_id=$data[0] and is_deleted=0 and status_active=1", "id", "location_name" );
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_library[$company_data[csf('country_id')]].',';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	//echo $plot_no.'**'.$level_no.'**'.$road_no.'**'.$block_no.'**'.$city.'**'.$zip_code.'**'.$country;
	/*$approved_arr=array();
	$sql_app = sql_select("SELECT mst_id, approved_by, max(approved_date) as approved_date FROM approval_history where mst_id=$data[1] and entry_form=1 group by mst_id, approved_by ORDER BY id ASC");
	foreach($sql_app as $row)
	{
		$approved_arr[$row[csf('mst_id')]]['user']=$row[csf('approved_by')];
		$approved_arr[$row[csf('mst_id')]]['date']=$row[csf('approved_date')];
	}*/
	//$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form=1 ORDER BY id ASC","mst_id","approved_by");
	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form in(21,27) ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_full_name from user_passwd", "id", "user_full_name" );		
	$buyer_library_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" ); 
	?>
	<style type="text/css">
		.wrd_brk{word-break: break-all; word-wrap: break-word;}
	</style>
	<div style="width:1000px">
	<? 
		$cbo_pi_basis_id='';
		$sql_qry="SELECT id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, approved_by, approved_date, total_amount, upcharge, discount, net_total_amount, inserted_by, goods_rcv_status ,beneficiary, pay_term, tenor,location_id, inserted_by,lc_req_date
		 from com_pi_master_details where id= $data[1]";
		//  echo $sql_qry;
		$sql_mst = sql_select($sql_qry);
		$item_category_id=$sql_mst[0][csf("item_category_id")];//cute 
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];
		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
		$approved_by=$sql_mst[0][csf("approved_by")];
		$approved_date=$sql_mst[0][csf("approved_date")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
		$location_id=$sql_mst[0][csf("location_id")];
	   $currencys=$currency[$sql_mst[0][csf("currency_id")]];
	   $lc_req_date=$sql_mst[0][csf("lc_req_date")];
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
						<td style="font-size:20px;" align="center" colspan="6"><strong>Proforma Invoice (PI)</strong></td>
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
                        <td>Pay Term</td>
                        <td><? echo $pay_term[$sql_mst[0][csf('pay_term')]];?></td>
                        <td>Tenor</td>
                        <td><? echo $sql_mst[0][csf('tenor')];?></td>
                      </tr>
                    <tr>
                    	<td>Insert By:</td>
                        <td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                    	<td colspan="3" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved";  else echo "&nbsp;"; ?></td>
                    </tr> 
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th style="min-width: 100px;">Job No</th>
                        <th style="min-width: 150px;">Construction</th>
                        <th style="min-width: 150px;">Composition</th>
                        <th style="min-width: 100px;">Color</th>					
                        <th style="min-width: 60px;">GSM</th>
                        <th style="min-width: 100px;">Dia/Width</th>
                        <th style="min-width: 60px;">UOM</th>
                        <th style="min-width: 100px;">Quantity</th>
                        <th style="min-width: 80px;">Rate</th>
                        <th style="min-width: 100px;">Amount</th>
                    </thead>
                    <tbody>
                    <?
                        $total_ammount = 0; $total_quantity=0;
                        $sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        	?>
                            <tr>
                                <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('construction')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('composition')]; ?></td>
                                <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('gsm')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('dia_width')]; ?></td>
                                <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
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
                        <td><? echo number_to_words(number_format($total_ammount,2, '.', ''),'USD','Cent');?></td>
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
				$item_category_library = return_library_array('SELECT category_id, short_name FROM lib_item_category_list','category_id','short_name');
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
                    if($supplier_data[csf('country_id')]!=0)$country = $country_library[$supplier_data[csf('country_id')]].','.' ';else $country='';
                    
                    $supplier_address = $address_1.$address_2.$address_3.$address_4.$web_site.$email.$country;
                }
                ?>
                <tr>
					<td style="font-size:20px;" align="center" colspan="5"><strong><? echo $supplier_name_library[$supplier_id]; ?></strong></td>
					<td rowspan="2" style="text-align:center; font-size:22px; border: 2px solid black;"><b><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved"; else echo "Not Approved"; ?></b></td>
				</tr>
                <tr>
					<td style="font-size:12px;" align="center" colspan="5"><strong><? echo $supplier_address; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:20px;" align="center" colspan="5"><strong>Proforma Invoice (PI)</strong></td>
				</tr>
                <tr>
					<td width="100" align="right">To</td>
					<td>&nbsp;</td><td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style="font-size:16px; border: 1px solid black; word-break:break-all;" ><b>PI: <? echo $sql_mst[0][csf("pi_number")]; ?><br /><b>Sys. ID: <? echo $sql_mst[0][csf("id")]; ?></b></b></td>
				</tr>
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
                    <td><? echo $currencys;?></td>
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
                    <td>Pay Term</td>
                    <td><? echo $pay_term[$sql_mst[0][csf('pay_term')]];?></td>
                    <td>Tenor</td>
                    <td><? echo $sql_mst[0][csf('tenor')];?></td>
                </tr>
                
                <tr>
                	<td>&nbsp;</td> 
                    <td>Last Approved Date:</td>
                    <td><? echo change_date_format($approved_date); ?></td>
                    <td>Insert By: &nbsp;<? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                    <td>Beneficiary:</td>
                    <td>&nbsp;<? echo $sql_mst[0][csf('beneficiary')];?></td>
                </tr> 
                <tr>
	                <td>&nbsp;</td> 
	                <td colspan="2">Remarks: &nbsp;<? echo $sql_mst[0][csf("remarks")]; ?></td>
	                <td >Item Category: &nbsp;<? echo $item_category_library[$sql_mst[0][csf("item_category_id")]]; ?></td>
	                <td ><? echo "Location: ".$company_location_arr[$location_id];?></td>
					<td valign="top" width="120">LC required date: </td>
					<td  valign="top"><? echo change_date_format($lc_req_date);?></td>
                </tr>
			</table>
			</div> 
          <div style="width:1000px; margin-left:10px">
			<? 
			$cbo_pi_basis_id=$sql_mst[0][csf("pi_basis_id")];
            if($item_category_id==1)
            {
				$data_array=sql_select("SELECT a.id, work_order_no, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	
				
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				
				?>
				<table class="rpt_table" width="100%" cellspacing="1" border="1" rules="all">
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
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $color_library[$row[csf('color_id')]]; ?></td>
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
						<td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($data[2]==2 || $data[2]==13) // knit
            {
				?>	
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
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
                        <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($data[2]==3 || $data[2]==14)//Woven Fabric //Grey Fabric Woven
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>&nbsp;</th>
                            <th>WO</th>
                            <? } ?> 
                            <th>Fabric Type</th>
                            <th>Construction</th>
                            <th>Composition</th> 
                            <th>Color</th>
                            <th>Weight</th>
                            <th>Width</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.fabric_composition,a.fabric_construction,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	  
                    
                    $total_ammount = 0;
                    
                    $i = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"   height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? }  ?>
                            <td><? echo $row[csf('fabric_type')]; ?></td>
                            <td><? echo $row[csf('fabric_construction')]; ?></td>
                            <td><? echo $row[csf('fabric_composition')]; ?></td>
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><? echo $row[csf('weight')]; ?></td>
                            <td><? echo $row[csf('width')]; ?></td>
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
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="7";
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
                            <td style="font-size:20px;"><i><? echo number_format($net_total_amount,4); ?></i></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==8 || $item_category_id==9 || $item_category_id==10 || $item_category_id==11)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($data[2]==4)//Accessories  
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header" width="270">WO</th>
                            <? } ?>
                            <th width="100">Buyer</th>
                            <th width="100">Style Ref.</th>
							<!-- <th>Item Cateogry</th> -->
                            <th width="120">Item Group</th>
                            <th width="120">Item Description</th>
                            <th width="40">UOM</th>
                            <th width="70">Quantity</th>
                            <th width="70">Rate</th>
                            <th>Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                    <?
					if($db_type==0) $style_grpCond="group_concat(style_des)";
					else if($db_type==2) $style_grpCond="listagg(cast(style_des as varchar(4000)),',') within group (order by style_des)";
					//$wo_ord_arr = return_library_array('SELECT a.booking_no, b.style_ref_no FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no ','booking_no','style_ref_no');
					/*echo "SELECT a.booking_no, b.style_ref_no FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$data[0] ";*/
					$wo_po=sql_select("SELECT a.booking_no, b.style_ref_no, b.buyer_name FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$data[0] and a.status_active=1 and a.booking_no is not null");
					$wo_ord_arr=array(); $wo_ord_buyer_arr=array();
					foreach($wo_po as $row)
					{
						$wo_ord_arr[$row[csf('booking_no')]].=$row[csf('style_ref_no')].',';
						$wo_ord_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_name')]].',';
					}
					/*echo "<pre>";
					print_r($wo_ord_buyer_arr);die;*/

					$wo_nonOrd_arr = return_library_array('SELECT booking_no, $style_grpCond as style_ref FROM wo_non_ord_samp_booking_dtls where status_active=1 group by booking_no','booking_no','style_ref');

					$wo_nonPo=sql_select("SELECT a.booking_no, b.style_des, a.buyer_id FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$data[0] and a.status_active=1 ");
					$wo_nonOrd_buyer_arr=array();
					foreach($wo_nonPo as $row)
					{
						$wo_nonOrd_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_id')]].',';
					}
					/*echo "<pre>";
					print_r($wo_nonOrd_buyer_arr);die;*/

                    //$data_array=sql_select("SELECT id, pi_id, work_order_no, booking_without_order, color_id, size_id, item_color, item_size, item_group, item_description, gsm, dia_width, uom, quantity, rate, amount, service_type FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");

					// $data_array=sql_select("SELECT id,item_category_id,work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0 group by id,item_category_id, work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id order by work_order_no");


					$data_array=sql_select("SELECT id,item_category_id,work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id, quantity as quantity, rate as rate, amount as amount FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0  order by work_order_no");
					
					$description_arr=array();
					foreach($data_array as $row)
					{
						$item_key=$row["ITEM_GROUP"]."*".$row["ITEM_DESCRIPTION"];
						$description_arr[$item_key]["ITEM_DESCRIPTION"]=$row["ITEM_DESCRIPTION"];
						$description_arr[$item_key]["WORK_ORDER_NO"].=$row["WORK_ORDER_NO"].",";
						$description_arr[$item_key]["ITEM_CATEGORY_ID"]=$row["item_category_id"];
						$description_arr[$item_key]["BOOKING_WITHOUT_ORDER"]=$row["BOOKING_WITHOUT_ORDER"];
						$description_arr[$item_key]["ITEM_GROUP"]=$row["ITEM_GROUP"];
						$description_arr[$item_key]["UOM"]=$row["UOM"];
						$description_arr[$item_key]["QUANTITY"]+=$row["QUANTITY"];
						$description_arr[$item_key]["RATE"]=$row["RATE"];
						$description_arr[$item_key]["AMOUNT"]+=$row["AMOUNT"];
					}
					// print_r($description_arr);
                    
					$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form=27 ORDER BY id ASC","mst_id","approved_by");
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
					// echo "<pre>"; print_r($data_array);die;
                    foreach($description_arr as $key=> $row) 
                    {
						$i++;
                    	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$wo_style_no=''; $wo_buyer_name='';
						if($row[csf('booking_without_order')]==1) 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_nonOrd_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_nonOrd_buyer_arr[$row[csf('work_order_no')]])));
						}
						else 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_ord_buyer_arr[$row[csf('work_order_no')]])));
						} 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td style="word-break:break-all"><? echo implode(",",array_unique(explode(",",chop($row[csf('work_order_no')],",")))); ?></td>
                            <? } ?>
                            <td><? //echo $arrayName[$wo_ord_buyer_arr[$row[csf('work_order_no')]]];
                            	echo rtrim($wo_buyer_name,','); ?></td>
                            <td style="word-break: break-all;"><? echo rtrim($wo_style_no,','); ?></td>
                            <!-- <td><? //echo $item_category_library[$row[csf('item_category_id')]]; ?></td> -->
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
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="4";
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''), $currencys,'Cent');?></td>
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
            else if($item_category_id==4)//Accessories  
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 100px;" class="header">WO</th>
                            <? } ?>
                            <th style="min-width: 150px;">Buyer</th>
                            <th style="min-width: 150px;">Style Ref.</th>
                            <th style="min-width: 120px;">Item Group</th>
                            <th style="min-width: 150px;">Item Description</th>
                            <th style="min-width: 60px;">UOM</th>
                            <th style="min-width: 100px;">Quantity</th>
                            <th style="min-width: 70px;">Rate</th>
                            <th style="min-width: 100px;">Amount</th>
                         </tr>
                    </thead>
                    <tbody>
                    <?
					if($db_type==0) $style_grpCond="group_concat(style_des)";
					else if($db_type==2) $style_grpCond="listagg(cast(style_des as varchar(4000)),',') within group (order by style_des)";
					//$wo_ord_arr = return_library_array('SELECT a.booking_no, b.style_ref_no FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no ','booking_no','style_ref_no');
					$wo_po=sql_select("SELECT a.booking_no, b.style_ref_no, b.buyer_name FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$data[0] ");
					$wo_ord_arr=array(); $wo_ord_buyer_arr=array();
					foreach($wo_po as $row)
					{
						$wo_ord_arr[$row[csf('booking_no')]].=$row[csf('style_ref_no')].',';
						$wo_ord_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_name')]].',';
					}
					$wo_nonOrd_arr = return_library_array('SELECT booking_no, $style_grpCond as style_ref FROM wo_non_ord_samp_booking_dtls where status_active=1 group by booking_no','booking_no','style_ref');

					$wo_nonPo=sql_select("SELECT a.booking_no, b.style_des, a.buyer_id FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$data[0] and a.status_active=1 ");
					$wo_nonOrd_buyer_arr=array();
					foreach($wo_nonPo as $row)
					{
						$wo_nonOrd_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_id')]].',';
					}
					/*echo "<pre>";
					print_r($wo_nonOrd_buyer_arr);die;*/
					
                    //$data_array=sql_select("SELECT id, pi_id, work_order_no, booking_without_order, color_id, size_id, item_color, item_size, item_group, item_description, gsm, dia_width, uom, quantity, rate, amount, service_type FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");
					$data_array=sql_select("SELECT work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0 group by work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id order by work_order_no");	 
                    
					$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form=27 ORDER BY id ASC","mst_id","approved_by");
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row) 
                    {
						$i++;
                    	if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$wo_style_no=''; $wo_buyer_name='';
						if($row[csf('booking_without_order')]==1) 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_nonOrd_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_nonOrd_buyer_arr[$row[csf('work_order_no')]])));
						}
						else
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_ord_buyer_arr[$row[csf('work_order_no')]])));
						} 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td class="wrd_brk"><? //echo $arrayName[$wo_ord_buyer_arr[$row[csf('work_order_no')]]];
                            	echo rtrim($wo_buyer_name,','); ?></td>
                            <td class="wrd_brk"><? echo rtrim($wo_style_no,','); ?></td>
                            <td class="wrd_brk"><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('item_description')]; ?></td>
                            
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<? 
                    } 
                    ?>
                        <tr class="tbl_bottom">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="4";
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''), $currencys,'Cent');?></td>
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
            else if($data[2]==5 || $data[2]==6 || $data[2]==7)
            {
				//echo "order by work_order_no"; die;
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==12)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <!-- <td><? echo $service_type[$row[csf('service_type')]]; ?></td> -->
                            <td><? echo $conversion_cost_head_array[$row[csf('service_type')]]; ?></td>
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==24)
            {
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <td><? echo $color_library[$row[csf('yarn_color')]]; ?></td>
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==25)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
		<br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
				<br>
		<?
	        echo signature_table(147, $data[0],"900px",$data[18],20,$sql_mst[0][csf("inserted_by")]);
	     ?>	 
	</div>
	<?	 
 	exit();	
}

if($action=="print_pi_two")
{
	// echo "Hello";die;
	$data = explode('*',$data);
	// print_r($data);
	//$item_category_id=$data[2];
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name" );
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
	$company_location_arr = return_library_array("SELECT id, location_name from lib_location WHERE company_id=$data[0] and is_deleted=0 and status_active=1", "id", "location_name" );
  	foreach($sql_company as $company_data) 
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_library[$company_data[csf('country_id')]].',';else $country='';
		
		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}


	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form in(21,27) ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_full_name from user_passwd", "id", "user_full_name" );		
	$buyer_library_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" ); 
	?>
	<style type="text/css">
		.wrd_brk{word-break: break-all; word-wrap: break-word;}
	</style>
	<div style="width:1000px">
	<? 
		$cbo_pi_basis_id='';
		$sql_qry="SELECT id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, approved_by, approved_date, total_amount, upcharge, discount, net_total_amount, inserted_by, goods_rcv_status ,beneficiary, pay_term, tenor,location_id, inserted_by, upcharge_breakdown, discount_breakdown
		 from com_pi_master_details where id= $data[1]";
		//  echo $sql_qry;
		$sql_mst = sql_select($sql_qry);
		$item_category_id=$sql_mst[0][csf("item_category_id")];//cute 
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];

		$upcharge_breakdown=$sql_mst[0][csf("upcharge_breakdown")];
		$discount_breakdown=$sql_mst[0][csf("discount_breakdown")];

		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
		$approved_by=$sql_mst[0][csf("approved_by")];
		$approved_date=$sql_mst[0][csf("approved_date")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
		$location_id=$sql_mst[0][csf("location_id")];
	   $currencys=$currency[$sql_mst[0][csf("currency_id")]];
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
						<td style="font-size:20px;" align="center" colspan="6"><strong>Proforma Invoice (PI)</strong></td>
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
                        <td>Pay Term</td>
                        <td><? echo $pay_term[$sql_mst[0][csf('pay_term')]];?></td>
                        <td>Tenor</td>
                        <td><? echo $sql_mst[0][csf('tenor')];?></td>
                      </tr>
                    <tr>
                    	<td>Insert By:</td>
                        <td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                    	<td colspan="3" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved";  else echo "&nbsp;"; ?></td>
                    </tr> 
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th style="min-width: 100px;">Job No</th>
                        <th style="min-width: 150px;">Construction</th>
                        <th style="min-width: 150px;">Composition</th>
                        <th style="min-width: 100px;">Color</th>					
                        <th style="min-width: 60px;">GSM</th>
                        <th style="min-width: 100px;">Dia/Width</th>
                        <th style="min-width: 60px;">UOM</th>
                        <th style="min-width: 100px;">Quantity</th>
                        <th style="min-width: 80px;">Rate</th>
                        <th style="min-width: 100px;">Amount</th>
                    </thead>
                    <tbody>
                    <?
                        $total_ammount = 0; $total_quantity=0;
                        $sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        	?>
                            <tr>
                                <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('construction')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('composition')]; ?></td>
                                <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('gsm')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('dia_width')]; ?></td>
                                <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
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
                        <td><? echo number_to_words(number_format($total_ammount,2, '.', ''),'USD','Cent');?></td>
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
				$item_category_library = return_library_array('SELECT category_id, short_name FROM lib_item_category_list','category_id','short_name');
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
                    if($supplier_data[csf('country_id')]!=0)$country = $country_library[$supplier_data[csf('country_id')]].','.' ';else $country='';
                    
                    $supplier_address = $address_1.$address_2.$address_3.$address_4.$web_site.$email.$country;
                }
                ?>
                <tr>
					<td style="font-size:20px;" align="center" colspan="5"><strong><? echo $supplier_name_library[$supplier_id]; ?></strong></td>
					<td rowspan="2" style="text-align:center; font-size:22px; border: 2px solid black;"><b><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved"; else echo "Not Approved"; ?></b></td>
				</tr>
                <tr>
					<td style="font-size:12px;" align="center" colspan="5"><strong><? echo $supplier_address; ?></strong></td>
				</tr>
				<tr>
					<td style="font-size:20px;" align="center" colspan="5"><strong>Proforma Invoice (PI)</strong></td>
				</tr>
                <tr>
					<td width="100" align="right">To</td>
					<td>&nbsp;</td><td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td style="font-size:16px; border: 1px solid black; word-break:break-all;" ><b>PI: <? echo $sql_mst[0][csf("pi_number")]; ?><br /><b>Sys. ID: <? echo $sql_mst[0][csf("id")]; ?></b></b></td>
				</tr>
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
                    <td><? echo $currencys;?></td>
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
                    <td>Pay Term</td>
                    <td><? echo $pay_term[$sql_mst[0][csf('pay_term')]];?></td>
                    <td>Tenor</td>
                    <td><? echo $sql_mst[0][csf('tenor')];?></td>
                </tr>
                
                <tr>
                	<td>&nbsp;</td> 
                    <td>Last Approved Date:</td>
                    <td><? echo change_date_format($approved_date); ?></td>
                    <td>Insert By: &nbsp;<? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                    <td>Beneficiary:</td>
                    <td>&nbsp;<? echo $sql_mst[0][csf('beneficiary')];?></td>
                </tr> 
                <tr>
	                <td>&nbsp;</td> 
	                <td colspan="2">Remarks: &nbsp;<? echo $sql_mst[0][csf("remarks")]; ?></td>
	                <td >Item Category: &nbsp;<? echo $item_category_library[$sql_mst[0][csf("item_category_id")]]; ?></td>
	                <td ><? echo "Location: ".$company_location_arr[$location_id];?></td>
                </tr>
			</table>
			</div> 
          <div style="width:1000px; margin-left:10px">
			<? 
			$cbo_pi_basis_id=$sql_mst[0][csf("pi_basis_id")];
            if($item_category_id==1)
            {
				$data_array=sql_select("SELECT a.id, work_order_no, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	
				
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				
				?>
				<table class="rpt_table" width="100%" cellspacing="1" border="1" rules="all">
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
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $color_library[$row[csf('color_id')]]; ?></td>
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
						<td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($data[2]==2 || $data[2]==13) // knit
            {
				?>	
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
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
                        <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($data[2]==3 || $data[2]==14)//Woven Fabric //Grey Fabric Woven
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>&nbsp;</th>
                            <th>WO</th>
                            <? } ?> 
                            <th>Fabric Type</th>
                            <th>Construction</th>
                            <th>Composition</th> 
                            <th>Color</th>
                            <th>Weight</th>
                            <th>Width</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.fabric_composition,a.fabric_construction,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");	  
                    
                    $total_ammount = 0;
                    
                    $i = 0;
                    foreach($data_array as $row) 
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"   height="25" >
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? }  ?>
                            <td><? echo $row[csf('fabric_type')]; ?></td>
                            <td><? echo $row[csf('fabric_construction')]; ?></td>
                            <td><? echo $row[csf('fabric_composition')]; ?></td>
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><? echo $row[csf('weight')]; ?></td>
                            <td><? echo $row[csf('width')]; ?></td>
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
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="7";
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
                            <td style="font-size:20px;"><i><? echo number_format($net_total_amount,4); ?></i></td>
                        </tr> 
                    </tbody> 
				</table>
				<table>
                    <tr height="20"></tr>
                    <tr>
                    <td valign="top"><strong>In-Words: </strong></td>
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==8 || $item_category_id==9 || $item_category_id==10 || $item_category_id==11)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
			else if($data[2]==4)//Accessories  
            {
				// echo "1";die;
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header">WO</th>
                            <? } ?>
                            <th>Buyer</th>
                            <th>Style Ref.</th>
							<!-- <th>Item Cateogry</th> -->
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
		
					$wo_po=sql_select("SELECT a.booking_no, b.style_ref_no, b.buyer_name FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$data[0] and a.status_active=1 and a.booking_no is not null");
					$wo_ord_arr=array(); $wo_ord_buyer_arr=array();
					foreach($wo_po as $row)
					{
						$wo_ord_arr[$row[csf('booking_no')]].=$row[csf('style_ref_no')].',';
						$wo_ord_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_name')]].',';
					}
					/*echo "<pre>";
					print_r($wo_ord_buyer_arr);die;*/

					$wo_nonOrd_arr = return_library_array('SELECT booking_no, $style_grpCond as style_ref FROM wo_non_ord_samp_booking_dtls where status_active=1 group by booking_no','booking_no','style_ref');

					$wo_nonPo=sql_select("SELECT a.booking_no, b.style_des, a.buyer_id FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$data[0] and a.status_active=1 ");
					$wo_nonOrd_buyer_arr=array();
					foreach($wo_nonPo as $row)
					{
						$wo_nonOrd_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_id')]].',';
					}
					
					$data_array=sql_select("SELECT id,item_category_id, work_order_no, booking_without_order, item_group, item_description, uom, item_prod_id, quantity as quantity, rate as rate, amount as amount FROM com_pi_item_details WHERE pi_id = $data[1] and status_active=1 and is_deleted=0  
					order by work_order_no");
					
					$description_arr=array();
					foreach($data_array as $row)
					{
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["ITEM_DESCRIPTION"]=$row["ITEM_DESCRIPTION"];
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["WORK_ORDER_NO"].=$row["WORK_ORDER_NO"].",";
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["ITEM_CATEGORY_ID"]=$row["item_category_id"];
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["BOOKING_WITHOUT_ORDER"]=$row["BOOKING_WITHOUT_ORDER"];
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["ITEM_GROUP"]=$row["ITEM_GROUP"];
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["UOM"]=$row["UOM"];
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["QUANTITY"]+=$row["QUANTITY"];
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["RATE"]=$row["RATE"];
                       $description_arr[$row["WORK_ORDER_NO"]][$row["ITEM_DESCRIPTION"]]["AMOUNT"]+=$row["AMOUNT"];
					}
					// print_r($description_arr);

					$upcharge_breakdown_arr = explode("_", $upcharge_breakdown);
					$discount_breakdown_arr = explode("_", $discount_breakdown);

					$upcharge_data = array_filter($upcharge_breakdown_arr);
					$discount_data = array_filter($discount_breakdown_arr);
					
					// echo $upchargeCount = count($upcharge_data);
					// echo $discountCount = count($discount_data);
                    
					$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form=27 ORDER BY id ASC","mst_id","approved_by");
                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name'); 
                    $i = 0;
                    $total_ammount = 0;
					// echo "<pre>"; print_r($data_array);die;
                    foreach($description_arr as $booking_no=> $book_val) 
                    {
						foreach($book_val as $key=> $row) 
						{
							$i++;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$wo_style_no=''; $wo_buyer_name='';
							if($row[csf('booking_without_order')]==1)
							{
								$wo_style_no=implode(",",array_unique(explode(",",$wo_nonOrd_arr[$booking_no])));
								$wo_buyer_name=implode(",",array_unique(explode(",",$wo_nonOrd_buyer_arr[$booking_no])));
							}
							else
							{
								$wo_style_no=implode(",",array_unique(explode(",",$wo_ord_arr[$booking_no])));
								$wo_buyer_name=implode(",",array_unique(explode(",",$wo_ord_buyer_arr[$booking_no])));
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<? if( $cbo_pi_basis_id == 1 ) { ?>
								<td><? echo $booking_no; ?></td>
								<? } ?>
								<td><? //echo $arrayName[$wo_ord_buyer_arr[$row[csf('work_order_no')]]];
									echo rtrim($wo_buyer_name,','); ?></td>
								<td style="word-break: break-all;"><? echo rtrim($wo_style_no,','); ?></td>
								<!-- <td><? //echo $item_category_library[$row[csf('item_category_id')]]; ?></td> -->
								<td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
								<td><? echo $row[csf('item_description')]; ?></td>
								
								<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
								<td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
								<td  align="right"><? if(number_format($row[csf('amount')],4)!=0 && number_format($row[csf('quantity')],2)!=0) echo number_format($row[csf('amount')]/$row[csf('quantity')],4); ?></td>
								<td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
							</tr>
							<?
						}
						 
                    } 
                      ?>
                        <tr class="tbl_bottom">
							<? 
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="4";
                            ?>
                            <td colspan = "<? echo $colspan; ?>" align="right">&nbsp;</td> 
                            <td>&nbsp;<? /*echo number_format($total_quantity,2);*/ $total_quantity = 0;?></td>
                            <td></td>
                            <td><? echo number_format($total_ammount,4); ?></td>
                        </tr>
                       
				<? if($upcharge_breakdown!=""){?>
						<tr>
							<td rowspan="8"  colspan = "7" align="right">Upcharge</td> 
							<td>Freight Cost</td>
							<td align="right"><? echo number_format($upcharge_breakdown_arr[0],4); //$upchargeCount ?></td>
						</tr>
						<tr>
							<td>Courier Cost</td>								
							<td align="right"><? echo number_format($upcharge_breakdown_arr[1],4); ?></td>
						</tr>
						<tr>
							<td>Upcharge</td>								
							<td align="right"><? echo number_format($upcharge_breakdown_arr[2],4); ?></td>
						</tr>
						<tr>
							<td>Transport Cost</td>								
							<td align="right"><? echo number_format($upcharge_breakdown_arr[3],4); ?></td>
						</tr>
						<tr>
							<td>Bank Charge</td>								
							<td align="right"><? echo number_format($upcharge_breakdown_arr[4],4); ?></td>
						</tr>
						<tr>
							<td>Vat</td>								
							<td align="right"><? echo number_format($upcharge_breakdown_arr[5],4); ?></td>
						</tr>
						<tr>
							<td>Service Charge</td>								
							<td align="right"><? echo number_format($upcharge_breakdown_arr[6],4); ?></td>
						</tr>
						<tr>
							<td>Adjustment</td>								
							<td align="right"><? echo number_format($upcharge_breakdown_arr[7],4); ?></td>
						</tr>
                   <?}else if($discount_breakdown!=""){?>
                        <tr class="tbl_bottom" height="25">
                            <td rowspan="2" colspan = "7" align="right">Discount</td> 
							<td>Discount</td>								
							<td align="right"><? echo number_format($discount_breakdown_arr[0],4); ?></td>
                        </tr>
						<tr>
							<td>Adjustment</td>								
							<td align="right"><? echo number_format($discount_breakdown_arr[1],4); ?></td>
						</tr>

                    <?}?>
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''), $currencys,'Cent');?></td>
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
            else if($data[2]==5 || $data[2]==6 || $data[2]==7)
            {
				//echo "order by work_order_no"; die;
				?>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==12)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <!-- <td><? echo $service_type[$row[csf('service_type')]]; ?></td> -->
                            <td><? echo $conversion_cost_head_array[$row[csf('service_type')]]; ?></td>
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==24)
            {
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <td><? echo $color_library[$row[csf('yarn_color')]]; ?></td>
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
            else if($item_category_id==25)
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
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
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
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
                    <td><? echo number_to_words(number_format($net_total_amount,2, '.', ''),'USD','Cent');?></td>
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
	<?php
	$sql_term = sql_select("select id, terms, terms_prefix from wo_booking_terms_condition where entry_form = 405 and booking_no = '$data[1]' order by id");

	if ($sql_term) {
	?>
		<div style="margin-left:10px">
			<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
				<tr>
					<th colspan="3">
						<strong style="font-size:15pt;">Terms & Conditions:</strong>
					</th>
				</tr>
				<tr>
					<?php
					$i = 1;
					foreach ($sql_term as $value) {
					?>
						<td width="40px"><?php echo $i ?></td>
						<td width="100px"><?php echo $value[csf('terms_prefix')] ?></td>
						<td><?php echo $value[csf('terms')] ?></td>
				</tr>
			<?php
						$i++;
					}
			?>
			</table>
			
			<!-- Include Signature Here -->
			<?php
			echo signature_table(147, $data[0],"900px",$data[18],20,$sql_mst[0][csf("inserted_by")]);
			?>
			
		</div>
		
	<?php
	} else {
		// Display an empty div or any other content if $sql_term is empty
		echo '<div>' . signature_table(147, $data[0], "900px", $data[18], 20, $sql_mst[0][csf("inserted_by")]) . '</div>';

	}
	?>



			<?
			// echo signature_table(147, $data[0],"900px",$data[18],20,$sql_mst[0][csf("inserted_by")]);
			//?>	 
		</div>
	<?	 
 	exit();	
}


if($action=="print_wf")
{
	$data = explode('*',$data);
	$cbo_item_category_id=$data[2];
	$company_id=$data[0];
	//print_r($data); die;
	if( $cbo_item_category_id ==1)
	{
		$entryForm = 165;
	}
	else if( $cbo_item_category_id==2 ||  $cbo_item_category_id ==3 ||  $cbo_item_category_id ==13 ||  $cbo_item_category_id == 14)
	{
		$entryForm = 166;
	}
	else if( $cbo_item_category_id == 4)
	{
		$entryForm = 167;
	}
	else if( $cbo_item_category_id == 12)
	{
		$entryForm = 168;
	}
	else if( $cbo_item_category_id == 24)
	{
		$entryForm = 169;
	}
	else if( $cbo_item_category_id == 25 || $cbo_item_category_id == 102 || $cbo_item_category_id == 103)
	{
		$entryForm = 170;
	}
	else if( $cbo_item_category_id == 30)
	{
		$entryForm = 197;
	}
	else if( $cbo_item_category_id == 31)
	{
		$entryForm = 171;
	}
	else if( $cbo_item_category_id == 5 ||  $cbo_item_category_id == 6 ||  $cbo_item_category_id == 7 ||  $cbo_item_category_id == 23)
	{
		$entryForm = 227;
	}
	else
	{
		$entryForm = 172;
	}
	ob_start();
	
	if($data[2] == 'PI Approval New'){
		echo load_html_head_contents($data[2],"../", 1, 1, $unicode,'','');
	}else{
		echo load_html_head_contents($data[2],"../../", 1, 1, $unicode,'','');
	}
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data)
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]].','.' ';else $country='';

		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form in(21,27) ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_full_name from user_passwd", "id", "user_full_name" );
	$buyer_library_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" );
	
	

	?>
	<style type="text/css">
		.wrd_brk{word-break: break-all; word-wrap: break-word;}
	</style>
	<div style="width:1000px">
	<?
		$cbo_pi_basis_id='';
		$sql_mst = sql_select("select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, lc_sc_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, total_amount, upcharge, discount, net_total_amount,inserted_by, goods_rcv_status, pay_term, tenor, location_id, nagotiate_by,pi_notes, inserted_by from com_pi_master_details where id= $data[1]");
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
		$pi_basis_id=$sql_mst[0][csf("pi_basis_id")];
		$pi_location_name=return_field_value("location_name","lib_location","id='".$sql_mst[0][csf("location_id")]."'");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$internal_file_no=$sql_mst[0][csf("internal_file_no")];
		$lc_sc_no=$sql_mst[0][csf("lc_sc_no")];
		
	 	$i = 0; $total_ammount = 0;
		
		 //echo $sql_mst[0][csf('import_pi')]; die;
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
                        <td>Pay Term</td>
                        <td><? echo $sql_mst[0][csf('pay_term')];?></td>
                        <td>Tenor</td>
                        <td><? echo $sql_mst[0][csf('tenor')];?></td>
                    </tr>
                    <tr>
                    	<td>Insert By:</td>
                        <td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                        <td>Remarks</td>
                        <td><? echo $sql_mst[0][csf('remarks')];?></td>
                    </tr>
                    <tr>
                    	<td colspan="5" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved"; else echo "&nbsp;"; ?></td>
                    </tr>
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th style="min-width: 100px;">Job No</th>
                        <th style="min-width: 150px;">Construction</th>
                        <th style="min-width: 150px;">Composition</th>
                        <th style="min-width: 100px;">Color</th>
                        <th style="min-width: 60px;">GSM</th>
                        <th style="min-width: 100px;">Dia/Width</th>
                        <th style="min-width: 100px;">Item Size</th>
                        <th style="min-width: 70px;">UOM</th>
                        <th style="min-width: 100px;">Quantity</th>
                        <th style="min-width: 70px;">Rate</th>
                        <th style="min-width: 100px;">Amount</th>
                    </thead>
                    <tbody>
                    <?
                        $total_ammount = 0; $total_quantity=0;
						if($goods_rcv_status==2)
						{
							$sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount, item_prod_id,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
						}
						else
						{
							if($db_type==0)
							{
								$sql = "select group_concat(id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id ,item_size";
							}
							else
							{
								$sql = "select rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size  from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id,item_size";
							}
						}
                       	echo $sql;
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        ?>
                            <tr>
                                <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('construction')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('composition')]; ?></td>
                                <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('gsm')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('dia_width')]; ?></td>
                                <td class="wrd_brk"><? echo $row[csf('item_size')]; ?></td>
                                <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                            </tr>
                        <?
                        }
                        ?>
                        <tr class="tbl_bottom" height="25">
                            <td align="right" colspan="8">Sum</td>
                           	<td align="right"><? echo number_format($total_quantity,4); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($total_ammount,4); ?></td>
                        </tr>

						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Upcharge</td>
							<td><? echo number_format($upcharge,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Discount</td>
							<td><? echo number_format($discount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Net Total</td>
							<td><? echo number_format($net_total_amount,4); ?></td>
						</tr>
                    </tbody>
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
                <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by ");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


			?>
			<? if(count($approved_sql)>0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval Status </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="250">Name</th>
								<th width="200">Designation</th>
								<th width="100">Approval Date</th>
							</tr>
						</thead>
						<? foreach ($approved_sql as $key => $value)
						{
							?>
							<tr>
								<td width="20"><? echo $sl; ?></td>
								<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</table>
				</div>
				<?
			}
			?>
			<? if(count($approved_his_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="150">Approved / Un-Approved</th>
								<th width="150">Designation</th>
								<th width="50">Approval Status</th>
								<th width="150">Reason for Un-Approval</th>
								<th width="150">Date</th>
							</tr>
						</thead>
						<? foreach ($approved_his_sql as $key => $value)
						{
							if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td width="20"><? echo $sl; ?></td>
								<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="50">Yes</td>
								<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
								<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
							</tr>

							<?
							$sl++;
							if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>
								<?
								$sl++;
							}
						}
						?>
					</table>
				</div>
				<?
			}
			?>
	        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
	        <br/>
	            <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

	                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
	             ?>

	        	</div>
	        <?
		}
		else
		{
			//echo $entryForm;die;
			$pi_order_sql=="";$pi_wo_data=array();$pi_wo_check=0;
			if($entryForm==165)//yarn
			{
				$pi_order_sql="select c.id as PO_ID from com_pi_item_details a, wo_non_order_info_dtls b, wo_po_break_down c where a.work_order_dtls_id=b.id and b.job_no=c.job_no_mst and a.item_category_id=1 and b.job_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by c.id";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.supplier_order_quantity as wo_qnty, b.rate as rate, b.amount as wo_amount from com_pi_item_details a, wo_non_order_info_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=1 and b.job_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1]";
			}
			else if($entryForm==169) // Services - Yarn Dyeing
			{
				$pi_order_sql="select c.id as PO_ID from com_pi_item_details a, wo_yarn_dyeing_dtls b, wo_po_break_down c where a.work_order_dtls_id=b.id and b.job_no=c.job_no_mst and a.item_category_id=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by c.id";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.yarn_wo_qty as wo_qnty, b.dyeing_charge as rate, b.amount as wo_amount from com_pi_item_details a, wo_yarn_dyeing_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] ";
			}
			if($entryForm==170) // Services - Embroidery, Services - Printing, Services - Wash
			{
				$pi_order_sql="select b.po_break_down_id as PO_ID from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=25 and b.booking_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by b.po_break_down_id ";
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.wo_qnty as wo_qnty, b.rate as rate, b.amount as wo_amount from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=25 and b.booking_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] ";
			}
			elseif($entryForm==166) // Knit Finish Fabrics, Woven Fabrics, Grey Fabric(Knit), Grey Fabric(woven)
			{
				$pi_order_sql="select b.po_break_down_id as PO_ID from com_pi_item_details a, wo_booking_dtls b where a.work_order_no=b.booking_no and a.item_category_id in(2,3,13,14) and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by b.po_break_down_id ";
				if($cbo_item_category_id==3)
				{
					//$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_or_width')]][$row[csf('width')]][$row[csf('cutable_width')]][$row[csf('uom')]]
					$pi_wo_sql="select a.pi_id, b.booking_no, c.body_part_id, c.lib_yarn_count_deter_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, c.gsm_weight_type, b.dia_width, c.width_dia_type, c.uom, b.id as wo_dtls_id, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as wo_qnty, b.rate as rate, b.amount as wo_amount from com_pi_item_details a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.work_order_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category_id in(2,3,13,14) and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] ";
					//group by a.id, a.booking_no, b.fabric_color_id, c.construction, c.composition, b.gsm_weight, c.gsm_weight_type, c.width_dia_type, b.dia_width, b.item_size, c.lib_yarn_count_deter_id, c.body_part_id, c.uom
					//echo $pi_wo_sql;die;
					$pi_wo_sql_result=sql_select($pi_wo_sql);
					if(count($pi_wo_sql_result)>0) $pi_wo_check=1;
					foreach($pi_wo_sql_result as $row)
					{
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('gsm_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('gsm_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['wo_qnty']+=$row[csf('wo_qnty')];
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('gsm_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['wo_amount']+=$row[csf('wo_amount')];
					}
					
				}
				else
				{
					//$prev_pi_qty_arr[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]]
					$pi_wo_sql="select a.pi_id, b.booking_no, c.lib_yarn_count_deter_id, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, c.uom, b.id as wo_dtls_id, b.fin_fab_qnty as fin_fab_qnty, b.grey_fab_qnty as wo_qnty, b.rate as rate, b.amount as wo_amount from com_pi_item_details a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.work_order_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.item_category_id in(2,3,13,14) and b.booking_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] ";
					$pi_wo_sql_result=sql_select($pi_wo_sql);
					if(count($pi_wo_sql_result)>0) $pi_wo_check=1;
					foreach($pi_wo_sql_result as $row)
					{
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_qnty']+=$row[csf('wo_qnty')];
						$pi_wo_data[$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('fabric_color_id')]][$row[csf('construction')]][$row[csf('copmposition')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_amount']+=$row[csf('wo_amount')];
					}
				}
				
			}
			elseif($entryForm==167)//Accessories
			{
				/*$sql_trims=("select a.id, a.booking_no, b.id as dtls_id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, sum(c.cons) as qnty from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id $booking_ids_cond and c.cons>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and  c.is_deleted=0
				group by a.id, a.booking_no, b.id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier");*/
				
				$pi_order_sql="select b.po_break_down_id as PO_ID from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=4 and b.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by b.po_break_down_id ";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.cons as wo_qnty, c.rate as rate, c.amount as wo_amount from com_pi_item_details a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.work_order_dtls_id=b.id and b.id=c.wo_trim_booking_dtls_id and c.cons>0 and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.pi_id=$data[1]";
				$pi_wo_sql_result=sql_select($pi_wo_sql);
				if (count($pi_wo_sql_result)>0) $pi_wo_check=1;
				foreach($pi_wo_sql_result as $row)
				{
					$pi_wo_data[$row[csf('wo_dtls_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_number_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_qnty']+=$row[csf('wo_qnty')];
					$pi_wo_data[$row[csf('wo_dtls_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_number_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_amount']+=$row[csf('wo_amount')];
				}
				//echo '<pre>';print_r($pi_wo_data);die;
				
			}
			elseif($entryForm==171) // Services Lab Test
			{
				$pi_order_sql="select c.id as PO_ID from com_pi_item_details a, wo_labtest_dtls b, wo_po_break_down c where a.work_order_dtls_id=b.id and b.job_no=c.job_no_mst and a.item_category_id=31 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by c.id";
				
				$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.wo_value as wo_qnty, b.labtest_charge as rate, b.amount as wo_amount from com_pi_item_details a, wo_labtest_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=31 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1]";
			}
			
			if($entryForm != 167 && $entryForm != 166)
			{
				$pi_wo_sql_result=sql_select($pi_wo_sql);
				if(count($pi_wo_sql_result)>0) $pi_wo_check=1;
				foreach($pi_wo_sql_result as $row)
				{
					$pi_wo_data[$row[csf('wo_dtls_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
					$pi_wo_data[$row[csf('wo_dtls_id')]]['wo_amount']+=$row[csf('wo_amount')];
				}
			}
			
			
			
			//echo $pi_order_sql;die;
			if($pi_order_sql!="")
			{
				$sql_lc_sc="select b.wo_po_break_down_id as PO_ID, a.export_lc_no as LC_SC_NO, a.internal_file_no as FILE_NO 
				from com_export_lc a, com_export_lc_order_info b 
				where a.id=b.com_export_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$data[0] and b.wo_po_break_down_id in($pi_order_sql)
				union all
				select b.wo_po_break_down_id as PO_ID, a.contract_no as LC_SC_NO, a.internal_file_no as FILE_NO 
				from com_sales_contract a, com_sales_contract_order_info b 
				where a.id=b.com_sales_contract_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$data[0] and b.wo_po_break_down_id in($pi_order_sql)";
			}
			
			// echo $sql_lc_sc;die;
			// $lc_sc_no=$ls_sc_file="";
			// $sql_lc_sc_result=sql_select($sql_lc_sc);
			// $lc_sc_data=array();
			// foreach($sql_lc_sc_result as $row)
			// {
			// 	if($lc_sc_check[$row["LC_SC_NO"]]=="")
			// 	{
			// 		$lc_sc_check[$row["LC_SC_NO"]]=$row["LC_SC_NO"];
			// 		$lc_sc_no.=$row["LC_SC_NO"].",";
			// 	}
			// 	if($file_check[$row["FILE_NO"]]=="")
			// 	{
			// 		$file_check[$row["FILE_NO"]]=$row["FILE_NO"];
			// 		$ls_sc_file.=$row["FILE_NO"].",";
			// 	}
			// }
			//print_r($lc_sc_data);die;
			// unset($sql_lc_sc_result);
			// $lc_sc_no=chop($lc_sc_no,",");
			// $ls_sc_file=chop($ls_sc_file,",");
			if($db_type==0)
			{
				$approval_cause=return_field_value("group_concat(approval_cause) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			else
			{
				$approval_cause=return_field_value("listagg(cast(approval_cause as varchar(4000)),',') within group(order by id) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			?>
			<div style="width:100%">
				<table width="100%">
					<?
	                foreach($sql_mst as $row_mst)
	                {
						$i++;
						$supplier_id=$row_mst[csf('supplier_id')];
						$sql_supplier=sql_select("SELECT id,supplier_name,country_id,web_site,email,address_1,address_2,address_3,address_4 FROM lib_supplier WHERE id=$supplier_id");
						$nagotiate_array=array(1=>"Buyer",2=>"Procurement");
						foreach($sql_supplier as $supplier_data)
						{
							if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
							if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
							if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
							if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
							if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
							if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].','.' ';else $email='';
							if($supplier_data[csf('country_id')]!=0)$country = $country_arr[$supplier_data[csf('country_id')]].','.' ';else $country='';

							$supplier_address = $address_1.$address_2.$address_3.$address_4.$web_site.$email.$country;
						}
						?>
						<tr>
						   <td style="font-size:20px;" align="center" colspan="5"><strong><? echo $supplier_name_library[$row_mst[csf('supplier_id')]]; ?></strong></td>
						</tr>
						<tr>
						   <td style="font-size:12px;" align="center" colspan="5"><strong><? echo $supplier_address; ?></strong></td>
						</tr>
						<tr>
						   <td width="200" style="padding-left:20px;">To</td>
						</tr>
						<tr>
						   <td width="200" style="padding-left:20px;"><? echo $importer_name_library[$row_mst[csf('importer_id')]];?></td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td width="100">Pi No:</td>
						   <td width="250"><? echo $row_mst[csf('pi_number')];?></td>
							<td width="150">System ID:</td>
						   <td><? echo $row_mst[csf('id')];?></td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td>Pi Date:</td>
						   <td><? echo change_date_format($row_mst[csf('pi_date')]);?></td>
						   <td>Last Shipment Date:</td>
						   <td><? echo change_date_format($row_mst[csf('last_shipment_date')]);?></td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td>Currency:</td>
						   <td><? echo $currency[$row_mst[csf('currency_id')]];?></td>
						   <td>PI Validity Date:</td>
						   <td><? echo change_date_format($row_mst[csf('pi_validity_date')]);?></td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td>Supplier:</td>
						   <td><? echo $supplier_name_library[$row_mst[csf('supplier_id')]]; ?></td>
						   <td>HS Code:</td>
						   <td><? echo $row_mst[csf('hs_code')];?></td>
						</tr>
	                    <tr>
                        	<td>&nbsp;</td>
	                     	<td>Pay Term</td>
	                     	<td><? echo $pay_term[$sql_mst[0][csf("pay_term")]];?></td>
                            <td>Tenor:</td>
						   	<td><? echo $sql_mst[0][csf("tenor")];?></td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
	                     	<td>LS/SC No:</td>
	                     	<td><? echo $lc_sc_no;?></td>
                            <td>File No:</td>
						   	<td><? echo $internal_file_no;?></td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
	                     	<td>Insert By: </td>
	                     	<td><? echo $user_library[$row_mst[csf('inserted_by')]]; ?></td>
                            <td>Location:</td>
						   	<td><? echo $pi_location_name;?></td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
	                     	<td>Indentor: </td>
	                     	<td><? echo $supplier_name_library[$row_mst[csf('intendor_name')]];?></td>
                            <td>Rate Negotiate by:</td>
						   	<td><? echo $nagotiate_array[$row_mst[csf('nagotiate_by')]];?></td>
						</tr>
	                     <tr>
                         	<td>&nbsp;</td>
                         	<td>Remarks:</td>
						   	<td><? echo $sql_mst[0][csf("remarks")];?></td>
	                    	<td style="text-align:center; color:#FF0000; font-size:22px;" colspan="2"><? if($sql_mst[0][csf('approved')]==1) echo "Approved"; elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved";  else echo "Not Approved"; ?></td>
	                    </tr>
                        
					 <?
					 $cbo_pi_basis_id=$row_mst[csf('pi_basis_id')];
					}
					?>
             	</table>
          	</div>
          <div style="width:1000px; margin-left:10px">
			<?
			//echo $entryForm;die;
			
            if($entryForm==165) // Yarn
            {
				//echo "SELECT a.id, work_order_no, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount, a.hs_code, a.work_order_dtls_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC";
				$data_array=sql_select("SELECT a.id, work_order_no, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.rate, a.amount, a.hs_code, a.work_order_dtls_id FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
				$work_order_no=$data_array[0][csf("work_order_no")];
				if($pi_basis_id==1)
				{
					/*echo "select p.id as pi_dtls_id, max(a.id) as wo_id, max(a.wo_basis_id) as wo_basis_id, sum(b.supplier_order_quantity) as wo_qnty, sum(b.amount) as amount from com_pi_item_details p, wo_non_order_info_mst a, wo_non_order_info_dtls b 
					where p.work_order_dtls_id=b.id and a.id=b.mst_id and a.pi_id = $data[1] group by p.id";*/
					$wo_sql=sql_select("select p.id as pi_dtls_id, max(a.id) as wo_id, max(a.wo_basis_id) as wo_basis_id, sum(b.supplier_order_quantity) as wo_qnty, sum(b.amount) as amount from com_pi_item_details p, wo_non_order_info_mst a, wo_non_order_info_dtls b 
					where p.work_order_dtls_id=b.id and a.id=b.mst_id and p.pi_id = $data[1] group by p.id");
					$wo_data=array();
					foreach($wo_sql as $row)
					{
						$wo_data[$row[csf("pi_dtls_id")]]["pi_dtls_id"]=$row[csf("pi_dtls_id")];
						$wo_data[$row[csf("pi_dtls_id")]]["wo_id"]=$row[csf("wo_id")];
						$wo_data[$row[csf("pi_dtls_id")]]["wo_qnty"]=$row[csf("wo_qnty")];
						$wo_data[$row[csf("pi_dtls_id")]]["amount"]=$row[csf("amount")];
						$wo_basis_id=$row[csf("wo_basis_id")];
					}
					$work_order_no=$wo_basis_array[0][csf("wo_basis_id")];
				}
				
				//echo $wo_basis_id.test;die;
				if ($wo_basis_id==1 || $wo_basis_id==2) // requ
				{
					$buyer_style_sql=" SELECT a.id, a.job_no, a.style_ref_no, a.buyer_name, c.id as wo_dtls_id from wo_po_details_master a, wo_non_order_info_dtls c 
					where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.job_no, a.style_ref_no, a.buyer_name, c.id";
					
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						$buyer_style_array[$row[csf('wo_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('wo_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/
				}
				elseif ($wo_basis_id==3) // buyer po
				{
					$buyer_style_sql="SELECT a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id as po_id, b.po_number, c.id as wo_dtls_id 
					from wo_po_details_master a, wo_po_break_down b, wo_non_order_info_dtls c 
					where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.id, a.job_no, a.style_ref_no, a.buyer_name,b.id, b.po_number, c.id";
					//and a.job_no in('og-20-00236','og-20-00207')
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						$buyer_style_array[$row[csf('wo_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('wo_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/
				}
				else // Independent
				{

				}
				
				/*echo "<pre>";
				print_r($buyer_style_array);*/

				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
					<thead>
						<tr>
							<? 
							if( $cbo_pi_basis_id == 1 ) 
							{ 
								?>
								<th>WO</th>
								<th>Buyer</th>
								<th>Style Ref.</th>
								<? 
							} 
							?>
							<th>HS Code</th>
							<th>Color</th>
							<th>Count</th>
							<th colspan="4">Composition</th>
							<th>Yarn Type</th>
							<th>UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th>WO Qnty</th>
                                <th>WO Rate</th>
                                <th>WO Amount</th>
                                <?
							}
							?>
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
						$buyer_name=$buyer_arr[$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']];
						$style_no=$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<? 
							if( $cbo_pi_basis_id  == 1 ) 
							{ 
							?>
							<td width="50"><? echo $row[csf('work_order_no')]; ?></td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $buyer_name; ?></td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $style_no; ?></td>
							<? 
							} 
							?>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $row[csf('hs_code')]; ?></td>
							<td width="<? if( $cbo_pi_basis_id == 1 ) echo "80"; else echo "110"; ?>"><? echo $color_library[$row[csf('color_id')]]; ?></td>
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
                            <? 
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_rate=$wo_data[$row[csf("id")]]["amount"]/$wo_data[$row[csf("id")]]["wo_qnty"];
								?>
                                <td width="60"  align="right"><? echo number_format($wo_data[$row[csf("id")]]["wo_qnty"],2);  $tot_wo_quantity += $wo_data[$row[csf("id")]]["wo_qnty"];?></td>
                                <td width="45" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td width="75" align="right"><? echo number_format($wo_data[$row[csf("id")]]["amount"],4);  $tot_wo_amt += $wo_data[$row[csf("id")]]["amount"];?></td>
                                <?
							}
							?>
							<td width="61"  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
							<td width="45" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td width="75" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
						</tr>
						<?
					}
					?>

						<tr class="tbl_bottom" height="25">
							<?
							if($pi_basis_id==1)
							{if($pi_wo_check==1) $colspan="15"; else $colspan="12";}
							else $colspan="9";
							
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
						<td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
					</tr>
					<tr>
					<tr height="50"></tr>
				</table>
                 <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by  order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="100">Approval Date</th>
                                    <th>Comments/Remarks</th>
								</tr>
							</thead>
							<? 
							foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    <td><? echo $approval_cause; ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
            }
            else if($entryForm==166) // Knit Finish Fabrics, Woven Fabrics, Grey Fabric(Knit), Grey Fabric(woven)
            {
				//echo $pi_basis_id."=".$pi_wo_check;die;
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 50px;">WO</th>
                            <th style="min-width: 50px;">Delivery Date</th>
							<th style="min-width: 120px;">Buyer</th>
                            <? } ?>
                            <th style="min-width: 50px;">Fab Ref</th>
                            <th style="min-width: 50px;">RD No</th>
                            <th style="min-width: 100px;">Fab. Description</th>
                            <th style="min-width: 50px;">Color</th>
                            <th style="min-width: 30px;">Weight</th>
                            <th style="min-width: 40px;">Weight Type</th>
                            <th style="min-width: 40px;">Dia/Width</th>
                            <th style="min-width: 30px;">Width</th>
                            <th style="min-width: 40px;">Cutable Width</th>
                            <th style="min-width: 30px;">UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th style="min-width: 60px;">WO Qnty</th>
                                <th style="min-width: 40px;">WO Rate</th>
                                <th style="min-width: 60px;">WO Amount</th>
                                <?
							}
							?>
                            <th style="min-width: 60px;">Quantity</th>
                            <th style="min-width: 40px;">Rate</th>
                            <th style="min-width: 60px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?

					$dtls_sql="SELECT a.id, a.pi_id, a.work_order_no, a.work_order_id, a.determination_id, a.color_id, a.fabric_construction, a.fabric_composition, a.gsm, a.weight,a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, a.hs_code, a.work_order_dtls_id, a.body_part_id, a.fabric_ref, a.rd_no, a.fab_design,a.fab_weight, a.fab_weight_type, a.cutable_width, a.width_dia_type, a.fab_type
					from com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC";
					//echo $dtls_sql;die;
                    $data_array=sql_select($dtls_sql);

                    $buyer_style_sql="SELECT a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no , d.delivery_date
					from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c, wo_booking_mst d 
                    where a.work_order_no=b.booking_no and b.job_no=c.job_no and a.work_order_id=d.id and d.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 
                    and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category_id = 3 and a.pi_id = $data[1]
                    group by a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no,d.delivery_date";

					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						if($buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("buyer_name")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("buyer_name")]]=$row[csf("buyer_name")];
				            $buyer_style_array[$row[csf("work_order_dtls_id")]]["buyer_name"].=$row[csf("buyer_name")].',';
				        }
				        if($buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("delivery_date")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_dtls_id")]][$row[csf("delivery_date")]]=$row[csf("delivery_date")];
				            $buyer_style_array[$row[csf("work_order_dtls_id")]]["delivery_date"].=$row[csf("delivery_date")].',';
				        }
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/

					$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
                    $total_ammount = 0; $i = 0;
                    foreach($data_array as $row)
                    {
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
						$fab_description=$lib_body_part_arr[$row[csf('body_part_id')]]." ".$row[csf('fab_type')]." ".$row[csf('fabric_construction')]." ".$row[csf('fab_design')]." ".$row[csf('fabric_composition')];
						$buyer_id=chop($buyer_style_array[$row[csf("work_order_dtls_id")]]['buyer_name'],',');
                        $buyer_name=array_unique(explode(',', $buyer_id));
                        $comma_separate_buyer="";
                        foreach ($buyer_name as $key => $value) 
                        {
                            if ($comma_separate_buyer=="") 
                            {
                               $comma_separate_buyer.=$buyer_library_arr[$value];
                            }
                            else
                            {
                                $comma_separate_buyer.=','.$buyer_library_arr[$value];
                            }
                        }
                        //echo $comma_separate_buyer;
                        $delivery_date_ref=chop($buyer_style_array[$row[csf("work_order_dtls_id")]]['delivery_date'],',');
                        $delivery_date_ref_arr=array_unique(explode(',', $delivery_date_ref));
                        $comma_separate_delivery_date="";
                        foreach ($delivery_date_ref_arr as $key => $value) 
                        {
                            if ($comma_separate_delivery_date=="") 
                            {
                               $comma_separate_delivery_date.=$value;
                            }
                            else
                            {
                                $comma_separate_delivery_date.=','.$value;
                            }
                        }
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id  == 1 ) { ?>
                            <td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
                            <td class="wrd_brk"><? echo $comma_separate_delivery_date;//$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']; ?></td>
							<td class="wrd_brk"><? echo $comma_separate_buyer ; ?></td>
                            <? } ?>
                            <td class="wrd_brk"><? echo $row[csf('fabric_ref')]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('rd_no')]; ?></td>
                            <td class="wrd_brk"><? echo $fab_description; ?></td>
                            <td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('fab_weight')]; ?></td>
                            <td class="wrd_brk"><? echo $fabric_weight_type[$row[csf('fab_weight_type')]]; ?></td>
                            <td class="wrd_brk"><? echo $fabric_typee[$row[csf('width_dia_type')]];  ?></td>
                            <td class="wrd_brk"><? echo $row[csf('dia_width')]; ?></td>
                            <td class="wrd_brk"><? echo $row[csf('cutable_width')]; ?></td>
                            <td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								if($cbo_item_category_id==3)
								{
									$wo_qnty=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('fab_weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['fin_fab_qnty'];
									$wo_amt=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('fab_weight')]][$row[csf('fab_weight_type')]][$row[csf('dia_width')]][$row[csf('width_dia_type')]][$row[csf('uom')]]['wo_amount'];
								}
								else
								{
									$wo_qnty=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_qnty'];
									$wo_amt=$pi_wo_data[$row[csf('work_order_no')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('fabric_construction')]][$row[csf('fabric_composition')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]]['wo_amount'];
								}
								$wo_rate=0;
								if($wo_amt>0 && $wo_qnty>0) $wo_rate=$wo_amt/$wo_qnty;
								?>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td class="wrd_brk" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
						</tr>
						<?
					}
					?>
                        <tr class="tbl_bottom">
							<?
                            if( $cbo_pi_basis_id == 1) {if($pi_wo_check)$colspan="16"; else $colspan="13";} else $colspan="10";
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
                        <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?>
                        </td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
				<table>
                    <tr>
                        <td valign="top"><strong>Notes: </strong></td>
                        <td>						
                        </td>
                    </tr>
					<? 
						$note_arr=explode('__',$sql_mst[0][csf('pi_notes')]); 
						foreach($note_arr as $rows)
						{
							?>
							    <tr>
									<td ></td>
									<td><?echo $rows;?></td>
								</tr>
							<?
						}
					?>

                    <tr height="20"></tr>
				</table>

                 <?
				// $approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				// $approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				/*if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="100">Approval Date</th>
                                    <th>Comments/Remarks</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    <td><? echo $approval_cause;?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}*/
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				 <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
            }
            else if($entryForm==167)//Accessories
            {
				//echo $goods_rcv_status;die;
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header">WO</th>
                            <? } ?>
                            <th>Buyer</th>
                            <th>Style Ref.</th>
                            <th>HS Code</th>
                            <th>Item Group</th>
                            <th>Item Description</th>
                            <th>Gmts Color</th>
                            <th>Gmts Size</th>
                            <th>Item Color</th>
                            <th>Item Size</th>
                            <th>UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th>WO Qnty</th>
                                <th>WO Rate</th>
                                <th>WO Amount</th>
                                <?
							}
							?>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                            </tr>
                    </thead>
                    <tbody>
                    <?
					if($goods_rcv_status==2)
					{
						$data_array=sql_select("SELECT a.id, a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, a.item_prod_id, a.hs_code, a.brand_supplier FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
					}
					else
					{
						/*if($db_type==0)
						{
							$sql = "SELECT group_concat(id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id, hs_code from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, item_prod_id, hs_code";

						}
						else
						{
							$sql = "SELECT rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id, hs_code from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, item_prod_id, hs_code";
						}*/

						if($db_type==0)
						{
							$data_array=sql_select("SELECT group_concat(a.id) as id, a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0
							group by a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier
							ORDER BY a.id ASC");
						}
						else
						{
							
							$data_array=sql_select("SELECT rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0
							group by a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier");
						}

					}

					$wo_po=sql_select("SELECT a.booking_no, b.style_ref_no, b.buyer_name FROM  wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$data[0] and a.status_active=1");
					$wo_ord_arr=array(); $wo_ord_buyer_arr=array();
					foreach($wo_po as $row)
					{
						$wo_ord_arr[$row[csf('booking_no')]].=$row[csf('style_ref_no')].',';
						$wo_ord_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_name')]].',';
					}
					/*echo "<pre>";
					print_r($wo_ord_buyer_arr);die;*/
					if($db_type==0) $style_grpCond="group_concat(style_des)";
					else if($db_type==2) $style_grpCond="listagg(cast(style_des as varchar(4000)),',') within group (order by style_des)";
					
					$wo_nonOrd_arr = return_library_array('SELECT booking_no, $style_grpCond as style_ref FROM wo_non_ord_samp_booking_dtls where status_active=1 group by booking_no','booking_no','style_ref');

					$wo_nonPo=sql_select("SELECT a.booking_no, b.style_des, a.buyer_id FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$data[0] and a.status_active=1 ");
					$wo_nonOrd_buyer_arr=array();
					foreach($wo_nonPo as $row)
					{
						$wo_nonOrd_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_id')]].',';
					}
					/*echo "<pre>";
					print_r($wo_nonOrd_buyer_arr);die;*/


                    $item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
                    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row)
                    {
                    	if($db_type==2 && $goods_rcv_status !=2) $row[csf('id')] = $row[csf('id')]->load();
                    	$wo_style_no=''; $wo_buyer_name='';
						
						if($row[csf('booking_without_order')]==1) 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_nonOrd_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_nonOrd_buyer_arr[$row[csf('work_order_no')]])));
						}
						else 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_ord_buyer_arr[$row[csf('work_order_no')]])));
						} 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } ?>
                            <td><? //echo $arrayName[$wo_ord_buyer_arr[$row[csf('work_order_no')]]];
                            	echo rtrim($wo_buyer_name,','); ?></td>
                            <td><? echo rtrim($wo_style_no,','); ?></td>
                            <td><? echo $row[csf('hs_code')]; ?></td>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><? echo $size_library[$row[csf('size_id')]]; ?></td>
                            <td><? echo $color_library[$row[csf('item_color')]]; ?></td>
                            <td><? echo $row[csf('item_size')]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_qnty=$pi_wo_data[$row[csf('work_order_dtls_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_qnty'];
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_amount'];
								
								$wo_rate=$wo_amt/$wo_qnty;
								?>
                                <td width="60"  align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td width="45" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td width="75" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
                            <td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
                            <td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                            <td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<?
                    }
                    ?>
                        <tr class="tbl_bottom">
							<?
                            if( $cbo_pi_basis_id == 1) {if($pi_wo_check)$colspan="14"; else $colspan="11";} else $colspan="8";
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
		                    <td><?

								if($sql_mst[0][csf('currency_id')]*1==1)
								{
									echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

								}
								else
								{
									echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
								}
								?>
							</td>
	                    </tr>
	                <tr>
                    <tr height="50"></tr>
				</table>
                 <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="100">Approval Date</th>
                                    <th>Comments/Remarks</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    <td><? echo $approval_cause;?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		       <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				 <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
				
            }
            else if($entryForm==168) //Services - Fabric
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? } ?>
                            <th>HS Code</th>
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
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.item_description,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active, a.hs_code FROM com_pi_item_details a WHERE  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");

				   
				    $i = 0;
                    $total_ammount = 0;
                    foreach($data_array as $row)
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=5; ?>
                            <td><? echo $row[csf('work_order_no')]; ?></td>
                            <? } else $colspan=4; ?>
                            <td><? echo $row[csf('hs_code')]; ?></td>
                            <!-- <td><? echo $service_type[$row[csf('service_type')]]; ?></td> -->
                            <td><? echo $conversion_cost_head_array[$row[csf('service_type')]]; ?></td>
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


					?>
				<? if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="250">Name</th>
									<th width="200">Designation</th>
									<th width="100">Approval Date</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		         <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
					 <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
				
            }
            else if($entryForm==169) // Services - Yarn Dyeing
            {
				$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <th>Buyer</th>
                            <th>Style Ref.</th>
                            <? } ?>
                            <th>HS Code</th>
                            <th>Lot</th>
                            <th>Count</th>
                            <th>Yarn Description</th>
                            <th>Yarn Color</th>
                            <th>Color Range</th>
                            <th>UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th>WO Qnty</th>
                                <th>WO Rate</th>
                                <th>WO Amount</th>
                                <?
							}
							?>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$data_array=sql_select("SELECT work_order_no, lot_no, count_name, item_description, yarn_color, color_range, uom,quantity, rate, amount, hs_code, work_order_dtls_id 
					FROM com_pi_item_details 
					WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 
					ORDER BY id ASC");

					$buyer_style_sql="SELECT a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no from com_pi_item_details a, wo_yarn_dyeing_dtls b, wo_po_details_master c 
					where a.work_order_dtls_id=b.id and b.job_no=c.job_no and a.pi_id = $data[1] and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 
					and a.item_category_id = 24
					group by a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no";
					
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/

                    $i = 0; $total_ammount = 0; $total_quantity=0;
                    foreach($data_array as $row)
                    {
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<? 
							if( $cbo_pi_basis_id == 1 ) 
							{ 
								{if($pi_wo_check)$colspan="13"; else $colspan="9";}
								?>
								<td><? echo $row[csf('work_order_no')]; ?></td>
								<td><? echo $buyer_library_arr[$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']]; ?></td>
								<td><? echo $buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']; ?></td>
								<? 
							} else $colspan=10; 
							?>
                            <td><? echo $row[csf('hs_code')]; ?></td>
                            <td><? echo $row[csf('lot_no')]; ?></td>
                            <td><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $color_library[$row[csf('yarn_color')]]; ?></td>
                            <td><? echo $color_range[$row[csf('color_range')]]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_qnty=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_qnty'];
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_amount'];
								
								$wo_rate=$wo_amt/$wo_qnty;
								?>
                                <td width="60"  align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td width="45" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td width="75" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="100">Approval Date</th>
                                    <th>Comments/Remarks</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    <td><? echo $approval_cause;?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		         <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
            }
            else if($entryForm==170) // Services - Embroidery, Services - Printing, Services - Wash
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <th>Buyer</th>
                            <th>Style Ref.</th>
                            <? } ?>
                            <th>HS Code</th>
                            <th>Gmts Item</th>
                            <th>Embellishment Name</th>
                            <th>Embellishment Type</th>
                            <th>Gmts Color</th>
                            <th>UOM</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th>WO Qnty</th>
                                <th>WO Rate</th>
                                <th>WO Amount</th>
                                <?
							}
							?>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT work_order_no, gmts_item_id, embell_name, embell_type, color_id, uom, quantity, rate, amount, hs_code, work_order_dtls_id FROM com_pi_item_details WHERE  pi_id = $data[1] and status_active=1 and is_deleted=0 ORDER BY id ASC");

                    $buyer_style_sql="SELECT a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no, 3 as type
					from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c 
					where a.work_order_dtls_id = b.id and b.job_no = c.job_no and a.pi_id = $data[1] and a.is_deleted = 0 and a.status_active = 1 and b.is_deleted = 0 and b.status_active = 1 and c.is_deleted = 0 and c.status_active = 1 
					and a.item_category_id in(25) group by a.work_order_dtls_id, c.buyer_name, c.style_ref_no, c.job_no";
					
					$buyer_style_data_array=sql_select($buyer_style_sql);
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']=$row[csf('buyer_name')];
						$buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']=$row[csf('style_ref_no')];
					}
					/*echo "<pre>";
					print_r($buyer_style_array);*/


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
							<? if( $cbo_pi_basis_id == 1 ) 
							{ 
								if($pi_wo_check)$colspan="12"; else $colspan="9";
								?>
								<td><? echo $row[csf('work_order_no')]; ?></td>
								<td><? echo $buyer_library_arr[$buyer_style_array[$row[csf('work_order_dtls_id')]]['buyer_name']]; ?></td>
								<td><? echo $buyer_style_array[$row[csf('work_order_dtls_id')]]['style_ref_no']; ?></td>
								<? 
							} else $colspan=6; ?>
                            <td><? echo $garments_item[$row[csf('hs_code')]]; ?></td>
                            <td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                            <td><? echo $emblishment_name_array[$row[csf('embell_name')]]; ?></td>
                            <td><? echo $emb_arr[$row[csf('embell_type')]]; ?></td>
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_qnty=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_qnty'];
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_amount'];
								
								$wo_rate=$wo_amt/$wo_qnty;
								?>
                                <td width="60"  align="right"><? echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
                                <td width="45" align="right"><? echo number_format($wo_rate,4); ?></td>
                                <td width="75" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>

									<th width="100">Approval Date</th>
                                    <th>Comments/Remarks</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    <td><? echo $approval_cause;?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		         <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
             ?>
				<?
            }
			else if($entryForm==171) // Services Lab Test
            {
				$test_item_arr=return_library_array( "SELECT id,test_item FROM lib_lab_test_rate_chart",'id','test_item');
				$buyer_arr=return_library_array( "SELECT id,buyer_name FROM lib_buyer",'id','buyer_name');

				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? 
							if ( $cbo_pi_basis_id == 1) $colspan=7;
							else if ( $cbo_pi_basis_id == 2) $colspan=6;
							else $colspan=5;
							if( $cbo_pi_basis_id == 1 ) {  ?>
                            <th>WO</th>
                            <th>Buyer</th>
                            <? } ?>
                            <th>HS Code</th>
                            <th>Style Ref.</th>
                            <th>Test For</th>
                            <th>Remarks</th>
                            <th>Color</th>
                            <th>Test Item</th>
                            <?
							if($pi_basis_id==1 && $pi_wo_check==1)
							{
								?>
                                <th>WO Amount</th>
                                <?
							}
							?>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    if( $cbo_pi_basis_id == 2 )  //Independent
                    {
                    	$data_array=sql_select("SELECT a.work_order_no, a.test_for, a.test_item_id, a.remarks, a.color_id, a.amount, a.hs_code FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
                    }
                    else 
                    {
                    	$data_array=sql_select("SELECT b.id as labtest_mst_id,  a.work_order_no, a.test_for, a.test_item_id, a.remarks, a.color_id, a.amount, a.hs_code, a.work_order_dtls_id FROM com_pi_item_details a,WO_LABTEST_MST b WHERE a.work_order_no=b.labtest_no and  a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
                    }                    

                    $i = 0; $total_ammount = 0;
                    foreach($data_array as $row)
                    {
						$mst_id=$row[csf('labtest_mst_id')];
						if ($mst_id != ''){
							$job_no_sql=sql_select("SELECT job_no FROM  WO_LABTEST_DTLS where mst_id='$mst_id'");
						}
						
						//echo "SELECT job_no FROM  WO_LABTEST_DTLS where mst_id='$mst_id'";
						$style_ref_no=''; $buyerName='';
						$unique_data=array(); $buyer_unique_data=array();
						foreach($job_no_sql as $job_no_value)
						{
							$job_no=$job_no_value[csf('job_no')];
							if ($job_no !=''){
								$style_ref_no_sql=sql_select("SELECT style_ref_no, buyer_name FROM  WO_PO_DETAILS_MASTER  where job_no='$job_no'");
							}
							
							
							$style_ref_no_get=$style_ref_no_sql[0][csf('style_ref_no')];
							$buyer_name_get=$buyer_arr[$style_ref_no_sql[0][csf('buyer_name')]];
							if(!in_array($style_ref_no_get,$unique_data))
							{
								array_push($unique_data,$style_ref_no_get);
								$style_ref_no.=$style_ref_no_get.',';
							}
							if(!in_array($buyer_name_get,$buyer_unique_data))
							{
								array_push($buyer_unique_data,$buyer_name_get);
								$buyerName.=$buyer_name_get.',';
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
                            <td><? echo chop($buyerName,','); ?></td>
                            <? } ?>
                            <td><? echo $row[csf('hs_code')]; ?></td>
                            <td><? echo $style_ref_no; ?></td>
                            <td><? echo $test_for[$row[csf('test_for')]]; ?></td>
                            <td><? echo $row[csf('remarks')]; ?></td>
                            <td><? echo $color_library[$row[csf('color_id')]]; ?></td>
                            <td><? echo $test_item; ?></td>
                            <?
                            if($pi_basis_id==1 && $pi_wo_check==1)
							{
								$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]]['wo_amount'];
								?>
                                <td width="75" align="right"><? echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td>
                                <?
							}
							?>
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
                    <td><?
						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?>
                        </td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                <?

				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");
				
				if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Name</th>
									<th width="150">Designation</th>
									<th width="100">Approval Date</th>
                                    <th>Comments/Remarks</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);
									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
                                    <td><? echo $approval_cause;?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				
				if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
				<?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
             ?>
				<?
            }
            else if($entryForm==227) // Chemicals, Dyes, Auxilary Chemicals, Dyes Chemicals & Auxilary Chemicals
            {
				
				
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th class="header">WO</th>
                            <? } ?>
                            <th class="header">HS Code</th>
                            <th class="header">Item Group</th>
                            <th class="header">Item Description</th>
                            <th class="header">Item Size</th>
                            <th class="header">UOM</th>
                            <th class="header">Quantity</th>
                            <th class="header">Rate</th>
                            <th class="header">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id, a.work_order_no, a.color_id, a.item_group, a.item_description, a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active,a.item_size, a.hs_code FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");

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
                            <td><? echo $row[csf('hs_code')]; ?></td>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $row[csf('item_size')]; ?></td>
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
                            if( $cbo_pi_basis_id == 1) $colspan="4"; else $colspan="5";
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by ");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


				?>
				<? if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="250">Name</th>
									<th width="200">Designation</th>
									<th width="100">Approval Date</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
					 <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
             ?>
				<?
            }
            else // Others
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th>WO</th>
                            <? }?>
                            <th>HS Code</th>
                            <th>Item Group</th>
                            <th>Item Description</th>
                            <th>Item Size</th>
                            <th>UOM</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $data_array=sql_select("SELECT a.id,a.pi_id,a.work_order_no,a.color_id,a.item_group,a.item_description,a.gsm,a.dia_width,a.uom,a.quantity,a.rate,amount,a.service_type,a.status_active,a.item_size, a.hs_code FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");

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
                            <td><? echo $row[csf('hs_code')]; ?></td>
                            <td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
                            <td><? echo $row[csf('item_description')]; ?></td>
                            <td><? echo $row[csf('item_size')]; ?></td>
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
                            if( $cbo_pi_basis_id == 1) $colspan="6"; else $colspan="5";
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


				?>
				<? if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="250">Name</th>
									<th width="200">Designation</th>
									<th width="100">Approval Date</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
					 <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

                echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
             ?>

				<?
            }
            ?>
          </div>
        	<?
		}
		?>
	</div>
	<?
	
	$html=ob_get_contents();
	ob_clean();
	
	if($is_mail_send==1){
		
		require_once('../../../mailer/class.phpmailer.php');
		require_once('../../../auto_mail/setting/mail_setting.php');

		$image_arr = return_library_array("select ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='proforma_invoice' and MASTER_TBLE_ID='$sys_id'",'ID','IMAGE_LOCATION');
		$att_file_arr=array();
		foreach($image_arr as $file){
			$att_file_arr[] = '../../../'.$file.'**'.$file;
		}

		 
		 
		 $WORK_ORDER_NO_ARR=array();
		 foreach($data_array as $row){
			$WORK_ORDER_NO_ARR[$row[WORK_ORDER_NO]]=$row[WORK_ORDER_NO]; 
		 }

		$team_leader_arr = return_library_array("select id,TEAM_LEADER_EMAIL from lib_marketing_team where project_type=1 and team_type in (0,1) and status_active =1 and is_deleted=0 order by TEAM_LEADER_EMAIL",'id','TEAM_LEADER_EMAIL');
		
		$team_member_arr = return_library_array("select id,TEAM_MEMBER_EMAIL from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by TEAM_MEMBER_EMAIL",'id','TEAM_MEMBER_EMAIL');

		 $sql_team_mail="
		SELECT b.TEAM_LEADER,b.DEALING_MARCHANT  FROM WO_BOOKING_DTLS a,  WO_PO_DETAILS_MASTER b WHERE a.JOB_NO = b.JOB_NO and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 ".where_con_using_array($WORK_ORDER_NO_ARR,1,'a.BOOKING_NO')."";
		$sql_team_mail_result=sql_select($sql_team_mail);
		$toArr=array();
		foreach($sql_team_mail_result as $rows){
			if($team_leader_arr[$rows[TEAM_LEADER]]){$toArr[]=$team_leader_arr[$rows[TEAM_LEADER]];}
			if($team_member_arr[$rows[DEALING_MARCHANT]]){$toArr[]=$team_member_arr[$rows[DEALING_MARCHANT]];}
			
		}

		 
		
		
		$sql_team_mail="select USER_ID,SEQUENCE_NO,BYPASS from ELECTRONIC_APPROVAL_SETUP where PAGE_ID = 867 AND COMPANY_ID =$company_id AND IS_DELETED = 0";
		$sql_team_mail_result=sql_select($sql_team_mail);
		foreach($sql_team_mail_result as $rows){
			if($rows[SEQUENCE_NO]==1 || ($rows[BYPASS]==1 && $rows[SEQUENCE_NO]==2)){
				$userIdArr[$rows[USER_ID]]=$rows[USER_ID];
			}
		}
		
		if(count($userIdArr)>0){$whereCon=" or d.id in(".implode(',',$userIdArr).")";}
 		
		$sql_team_mail="SELECT d.USER_EMAIL from USER_PASSWD d WHERE d.id = $inserted_by $whereCon";
		//echo $sql_team_mail;die;
		
		$sql_team_mail_result=sql_select($sql_team_mail);
		foreach($sql_team_mail_result as $rows){
			if($rows[USER_EMAIL]){$toArr[]=$rows[USER_EMAIL];}
		}
		$to = implode(',',$toArr);
		
		
		
		$subject="Pro Forma Invoice";
		$header=mailHeader();
		if($to!=""){
			echo sendMailMailer( $to, $subject, $html, $from_mail,$att_file_arr );
		}

		
	}
	else{echo $html;}
    exit();
}

if($action=="print_sf")
{
	$data = explode('*',$data);
	$entryForm=168;
	$cbo_item_category_id=$data[2];	
	$company_id=$data[0];
	//print_r($data); die;

	$path='../../';

	ob_start();
	
	if($data[3] == 'PI Approval New'){
		echo load_html_head_contents($data[3],"../", 1, 1, $unicode,'','');
	}else{
		echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
	}
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data)
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]].','.' ';else $country='';

		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form in(21,27) ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_full_name from user_passwd", "id", "user_full_name" );
	$buyer_library_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" );
	
	

	?>
	<div style="width:1000px">
	<?
		$cbo_pi_basis_id='';
		$sql_mst = sql_select("select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, total_amount, upcharge, discount, net_total_amount,inserted_by, goods_rcv_status, pay_term, tenor, location_id, pi_for, inserted_by, approved_by,lc_req_date from com_pi_master_details where id= $data[1]");
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
		$pi_basis_id=$sql_mst[0][csf("pi_basis_id")];
		$pi_location_name=return_field_value("location_name","lib_location","id='".$sql_mst[0][csf("location_id")]."'");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$approved_by=$sql_mst[0][csf("approved_by")];
		$lc_req_date=$sql_mst[0][csf("lc_req_date")];
		
	 	$i = 0; $total_ammount = 0;
		
		//  echo $sql_mst[0][csf('import_pi')]; die;
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
                        <td>Pay Term</td>
                        <td><? echo $sql_mst[0][csf('pay_term')];?></td>
                        <td>Tenor</td>
                        <td><? echo $sql_mst[0][csf('tenor')];?></td>
                    </tr>
                    <tr>
                    	<td>Insert By:</td>
                        <td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
                        <td>Remarks</td>
                        <td><? echo $sql_mst[0][csf('remarks')];?></td>
                    </tr>
                    <tr>
                    	<td colspan="5" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;">
							<? if($sql_mst[0][csf('approved')]==1) echo "Approved"; 
							//elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved"; else echo "&nbsp;"; 
							?>
						</td>
                    </tr>
                </table>
                <br>
                <table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
                    <thead>
                        <th style="width: 150px;">Job No</th>
                        <th style="width: 150px;">Construction</th>
                        <th style="width: 150px;">Composition</th>
                        <th style="width: 100px;">Color</th>
                        <th style="width: 50px;">GSM</th>
                        <th style="width: 80px;">Dia/Width</th>
                        <th style="width: 100px;">Item Size</th>
                        <th style="width: 50px;">UOM</th>
                        <th style="width: 100px;">Quantity</th>
                        <th style="width: 70px;">Rate</th>
                        <th>Amount</th>
                    </thead>
                    <tbody>
                    <?
						if($goods_rcv_status==2)
						{
							$sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount, item_prod_id,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
						}
						else
						{
							if($db_type==0)
							{
								$sql = "select group_concat(id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id ,item_size";
							}
							else
							{
								$sql = "select rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size  from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id,item_size";
							}
						}
                       	// echo $sql;
                        $data_array=sql_select($sql);
                        foreach($data_array as $row)
                        {
                        ?>
                            <tr>
                                <td style="word-break: break-all;"><? echo $row[csf('work_order_no')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('construction')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('composition')]; ?></td>
                                <td style="word-break: break-all;"><? echo $color_library[$row[csf('color_id')]]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('gsm')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('dia_width')]; ?></td>
                                <td style="word-break: break-all;"><? echo $row[csf('item_size')]; ?></td>
                                <td style="word-break: break-all;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                                <td style="word-break: break-all;" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
                                <td style="word-break: break-all;" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                                <td style="word-break: break-all;" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
                            </tr>
                        <?
                        }
                        ?>
                        <tr class="tbl_bottom" height="25">
                            <td align="right" colspan="8">Sum</td>
                           	<td align="right"><? echo number_format($total_quantity,4); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($total_ammount,4); ?></td>
                        </tr>

						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Upcharge</td>
							<td><? echo number_format($upcharge,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Discount</td>
							<td><? echo number_format($discount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Net Total</td>
							<td><? echo number_format($net_total_amount,4); ?></td>
						</tr>
                    </tbody>
                </table>
                <table>
                    <tr height="20"><td></td></tr>
                    <tr>
                        <td valign="top"><strong>In-Words: </strong></td>
                        <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr height="50"><td></td></tr>
                </table>
                <?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by ");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


			?>
			<? if(count($approved_sql)>0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval Status </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="250">Name</th>
								<th width="200">Designation</th>
								<th width="100">Approval Date</th>
							</tr>
						</thead>
						<? foreach ($approved_sql as $key => $value)
						{
							?>
							<tr>
								<td width="20"><? echo $sl; ?></td>
								<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</table>
				</div>
				<?
			}
			?>
			<? if(count($approved_his_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="150">Approved / Un-Approved</th>
								<th width="150">Designation</th>
								<th width="50">Approval Status</th>
								<th width="150">Reason for Un-Approval</th>
								<th width="150">Date</th>
							</tr>
						</thead>
						<? foreach ($approved_his_sql as $key => $value)
						{
							if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td width="20"><? echo $sl; ?></td>
								<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="50">Yes</td>
								<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
								<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
							</tr>

							<?
							$sl++;
							if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>
								<?
								$sl++;
							}
						}
						?>
					</table>
				</div>
				<?
			}
			?>
	        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
	        <br/>
	            <?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

	                // echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
				// 	$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
					
	            //     $userSignatureArr[$approved_by]=$path.$signature_arr[$approved_by];
                // echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);	
	             ?>

	        	</div>
	        <?
		}
		else
		{
			//echo $entryForm;die;
			$pi_order_sql=="";$pi_wo_data=array();$pi_wo_check=0;
			$pi_wo_sql_result=sql_select($pi_wo_sql);
			if(count($pi_wo_sql_result)>0) $pi_wo_check=1;
			foreach($pi_wo_sql_result as $row)
			{
				$pi_wo_data[$row[csf('wo_dtls_id')]]['wo_qnty']+=$row[csf('wo_qnty')];
				$pi_wo_data[$row[csf('wo_dtls_id')]]['wo_amount']+=$row[csf('wo_amount')];
			}
			
			//echo $pi_order_sql;die;
			if($pi_order_sql!="")
			{
				$sql_lc_sc="select b.wo_po_break_down_id as PO_ID, a.export_lc_no as LC_SC_NO, a.internal_file_no as FILE_NO 
				from com_export_lc a, com_export_lc_order_info b 
				where a.id=b.com_export_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$data[0] and b.wo_po_break_down_id in($pi_order_sql)
				union all
				select b.wo_po_break_down_id as PO_ID, a.contract_no as LC_SC_NO, a.internal_file_no as FILE_NO 
				from com_sales_contract a, com_sales_contract_order_info b 
				where a.id=b.com_sales_contract_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$data[0] and b.wo_po_break_down_id in($pi_order_sql)";
			}
			
			//echo $sql_lc_sc;die;
			$lc_sc_no=$ls_sc_file="";
			$sql_lc_sc_result=sql_select($sql_lc_sc);
			$lc_sc_data=array();
			foreach($sql_lc_sc_result as $row)
			{
				if($lc_sc_check[$row["LC_SC_NO"]]=="")
				{
					$lc_sc_check[$row["LC_SC_NO"]]=$row["LC_SC_NO"];
					$lc_sc_no.=$row["LC_SC_NO"].",";
				}
				if($file_check[$row["FILE_NO"]]=="")
				{
					$file_check[$row["FILE_NO"]]=$row["FILE_NO"];
					$ls_sc_file.=$row["FILE_NO"].",";
				}
			}
			//print_r($lc_sc_data);die;
			unset($sql_lc_sc_result);
			$lc_sc_no=chop($lc_sc_no,",");
			$ls_sc_file=chop($ls_sc_file,",");
			if($db_type==0)
			{
				$approval_cause=return_field_value("group_concat(approval_cause) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			else
			{
				$approval_cause=return_field_value("listagg(cast(approval_cause as varchar(4000)),',') within group(order by id) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			?>
			<style>
				.wrd_brk{word-break: break-all;}
				.left{text-align: left;}
				.center{text-align: center;}
				.right{text-align: right;}
			</style>
			<div style="width:100%">
				<table width="100%">
					<?
					$item_category_library = return_library_array('SELECT category_id, short_name FROM lib_item_category_list','category_id','short_name');
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
							if($supplier_data[csf('country_id')]!=0)$country = $country_arr[$supplier_data[csf('country_id')]].','.' ';else $country='';

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
						   <td style="font-size:20px;" align="center" colspan="6"><strong>Proforma Invoice-(PI)</strong></td>
						</tr>
						<tr>
						   <td width="100" align="right">To</td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td width="200"><? echo $importer_name_library[$row_mst[csf('importer_id')]];?></td>
						   <td width="100"><strong>Pi No:</strong></td>
						   <td width="250"> <strong> <? echo $row_mst[csf('pi_number')];?></strong></td>
							<td width="150">HS Code: </td>
						   <td><? echo $row_mst[csf('hs_code')];?></td>
						</tr>
						<tr>
						   <td>&nbsp;</td>
						   <td rowspan="3"><? echo $company_address ;?></td>
						   <td><strong>Pi Date:</strong> </td>
						   <td><strong><? echo change_date_format($row_mst[csf('pi_date')]);?></strong></td>
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
                            <td>&nbsp;</td>
	                     	<td>Pay Term</td>
	                     	<td><? echo $pay_term[$sql_mst[0][csf("pay_term")]];?></td>
                            <td>Tenor:</td>
						   	<td><? echo $sql_mst[0][csf("tenor")];?></td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
                            <td>&nbsp;</td>
	                     	<td>LS/SC No:</td>
	                     	<td><? echo $lc_sc_no;?></td>
                            <td>File No:</td>
						   	<td><? echo $ls_sc_file;?></td>
						</tr>
                        <tr>
                        	<td>&nbsp;</td>
                            <td>&nbsp;</td>
	                     	<td>Insert By: </td>
	                     	<td><? echo $user_library[$row_mst[csf('inserted_by')]]; ?></td>
                            <td>Location:</td>
						   	<td><? echo $pi_location_name;?></td>
						</tr>
	                    <tr>
                         	<td>&nbsp;</td>
                         	<td>&nbsp;</td>
                         	<td>Remarks:</td>
						   	<td><? echo $sql_mst[0][csf("remarks")];?></td>
						    <td> <b>Pi For:</b></td>
							<td> <b><? $piFor_array=array(1=>"BTB",2=>"Margin LC",3=>"Fund Buildup",4=>"TT/Pay Order",5=>"FTT",6=>"FDD/RTGS");
						  echo $piFor_array[$row_mst[csf('pi_for')]];
							?></b></td>
	                    </tr>
	                    <tr>
                         	<td>&nbsp;</td>
                         	<td>&nbsp;</td>
                         	<td>Category:</td>
						   	<td><? echo $item_category_library[$sql_mst[0][csf("item_category_id")]];?></td>
							<td valign="top" width="120">LC required date: </td>
					        <td  valign="top"><? echo change_date_format($lc_req_date);?></td>
	                    </tr>
						<tr>
							<td>&nbsp;</td>
                         	 <td>&nbsp;</td>
							 <td>&nbsp;</td>
                         	 <td>&nbsp;</td>
							 <td>&nbsp;</td>
							 <td style="text-align:center; color:#FF0000; font-size:22px;" colspan="2">
								<? if($sql_mst[0][csf('approved')]==1) echo "Approved"; 
								//elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved";  else echo "Not Approved"; 
								?>
							</td>
						</tr>
                    <?
					 $cbo_pi_basis_id=$row_mst[csf('pi_basis_id')];
					}
					?>
             	</table>
          	</div>
          <div style="width:1000px; margin-left:10px">
			<?
			//echo $entryForm;die;
			if($entryForm==168) //Services - Fabric
            {
				?>
				<table class="rpt_table" width="100%" cellspacing="1"  border="1" rules="all">
                    <thead>
                        <tr>
							<? if( $cbo_pi_basis_id == 1 ) { ?>
                            <th style="min-width: 150px;">WO</th>
                            <? } ?>
                            <th style="min-width: 100px;">Buyer</th>
                            <th style="min-width: 100px;">Style</th>
                            <!-- <th style="min-width: 100px;">Category</th> -->
                            <th style="min-width: 100px;">HS Code</th>
                            <th style="min-width: 100px;">Service Type</th>
                            <th style="min-width: 200px;">Item Color</th>
                            <th style="min-width: 60px;">UOM</th>
                            <th style="min-width: 80px;">Quantity</th>
                            <th style="min-width: 80px;">Rate</th>
                            <th style="min-width: 80px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$sql="SELECT a.id, a.pi_id, a.work_order_no, a.item_color, a.item_description,a.item_category_id, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, a.hs_code, a.work_order_dtls_id FROM com_pi_item_details a WHERE a.pi_id=$data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC";
                    $sql_res=sql_select($sql);
					$data_array=array();
					$check_row_span_arr=array();
					$service_type_array=array();
					foreach($sql_res as $row)
					{						
						$data_array[$row[csf('work_order_no')]][$row[csf('rate')]]['uom'].=$unit_of_measurement[$row[csf('uom')]].',';
						$data_array[$row[csf('work_order_no')]][$row[csf('rate')]]['item_color'].=$color_library[$row[csf('item_color')]].',';						
						$data_array[$row[csf('work_order_no')]][$row[csf('rate')]]['quantity']+=$row[csf('quantity')];
						$data_array[$row[csf('work_order_no')]][$row[csf('rate')]]['amount']+=$row[csf('amount')];
						
						if($check_row_span_arr[$row[csf("work_order_no")]][$row[csf("rate")]]=="")
						{
							$check_row_span_arr[$row[csf("work_order_no")]][$row[csf("rate")]]=$row[csf("rate")];
							$row_span_arr[$row[csf('work_order_no')]]++;
							$service_type_array[$row[csf('work_order_no')]].=$conversion_cost_head_array[$row[csf('service_type')]].',';
						}
						
					}

					// echo '<pre>';print_r($data_array); die;

					$buyer_style_sql="SELECT a.work_order_no,a.item_category_id, c.buyer_name, c.style_ref_no, c.job_no 
					from com_pi_item_details a, wo_booking_dtls b, wo_po_details_master c 
                    where a.work_order_no=b.booking_no and b.job_no=c.job_no and a.is_deleted=0 and a.status_active=1 
                    and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category_id = $data[2] and a.pi_id = $data[1]
                    group by a.work_order_no,a.item_category_id, c.buyer_name, c.style_ref_no, c.job_no";
					//  echo $buyer_style_sql;
					$buyer_style_data_array=sql_select($buyer_style_sql);
					// echo '<pre>';print_r($buyer_style_data_array); die;
					$buyer_style_array=array();
					foreach($buyer_style_data_array as $row)
					{
						if($buyer_style_array[$row[csf("work_order_no")]][$row[csf("buyer_name")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_no")]][$row[csf("buyer_name")]]=$row[csf("buyer_name")];
				            $buyer_style_array[$row[csf("work_order_no")]]["buyer_name"].=$row[csf("buyer_name")].',';
				        }
				        if($buyer_style_array[$row[csf("work_order_no")]][$row[csf("style_ref_no")]]=="")
				        {
				            $buyer_style_array[$row[csf("work_order_no")]][$row[csf("style_ref_no")]]=$row[csf("style_ref_no")];
				            $buyer_style_array[$row[csf("work_order_no")]]["style_ref_no"].=$row[csf("style_ref_no")].',';
				        }
						if($buyer_style_array[$row[csf("work_order_no")]][$row[csf("item_category_id")]]=="")
						{
							$buyer_style_array[$row[csf("work_order_no")]][$row[csf("item_category_id")]]=$row[csf("item_category_id")];
							$buyer_style_array[$row[csf("work_order_no")]]["item_category_id"].=$row[csf("item_category_id")].',';
						}
					}
				   	// echo '<pre>';print_r($buyer_style_array); die;
				    $i = 0;
                    $total_ammount = 0;
					$service_type_val="";
                    foreach($data_array as $wo_no => $wo_data)
                    {
						$buyer_id=chop($buyer_style_array[ $wo_no]['buyer_name'],',');
                        $buyer_name=array_unique(explode(',', $buyer_id));
                        $comma_separate_buyer="";
                        foreach ($buyer_name as $key => $value) 
                        {
                            if ($comma_separate_buyer=="") 
                            {
                               $comma_separate_buyer.=$buyer_library_arr[$value];
                            }
                            else
                            {
                                $comma_separate_buyer.=','.$buyer_library_arr[$value];
                            }
                        }
                        //echo $comma_separate_buyer;
                        $style_ref=chop($buyer_style_array[ $wo_no]['style_ref_no'],',');
                        $style_ref_arr=array_unique(explode(',', $style_ref));
                        $comma_separate_style="";
                        foreach ($style_ref_arr as $key => $value) 
                        {
                            if ($comma_separate_style=="") 
                            {
                               $comma_separate_style.=$value;
                            }
                            else
                            {
                                $comma_separate_style.=','.$value;
                            }
                        }

						$item_category_library = return_library_array('SELECT category_id, short_name FROM lib_item_category_list','category_id','short_name');
                        $item_category=chop($buyer_style_array[ $wo_no]['item_category_id'],',');
                        $item_category_arr=array_unique(explode(',', $item_category));
                        $comma_separate_category="";
                        foreach ($item_category_arr as $key => $value) 
                        {
                            if ($comma_separate_category=="") 
                            {
                               $comma_separate_category.=$item_category_library[$value];
                            }
                            else
                            {
                                $comma_separate_category.=','.$item_category_library[$value];
                            }
                        }

						
						$service_type_val = implode(",", array_unique(explode(",", rtrim($service_type_array[$wo_no],','))));
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { $colspan=7; ?>
                            <td class="wrd_brk" rowspan="<? echo $row_span_arr[$wo_no]; ?>"><? echo $wo_no; ?></td>
                            <? } else $colspan=6; ?>
							<td class="wrd_brk" rowspan="<? echo $row_span_arr[$wo_no]; ?>"><? echo $comma_separate_buyer; ?></td>
							<td class="wrd_brk" rowspan="<? echo $row_span_arr[$wo_no]; ?>"><? echo $comma_separate_style; ?></td>
							<!-- <td class="wrd_brk" rowspan="<? //echo $row_span_arr[$wo_no]; ?>"><? //echo $comma_separate_category; ?></td> -->
							<td class="wrd_brk" rowspan="<? echo $row_span_arr[$wo_no]; ?>"><? echo $row[csf('hs_code')]; ?></td>
							<td class="wrd_brk" rowspan="<? echo $row_span_arr[$wo_no]; ?>"><? echo $service_type_val; ?></td>
							<?
							foreach($wo_data as $pi_rate => $row)
							{
								$uom = implode(",", array_unique(explode(",", rtrim($row["uom"],','))));
								$item_color = implode(",", array_unique(explode(",", rtrim($row["item_color"],','))));
								?>
								<td class="wrd_brk"><? echo $item_color; ?></td>
								<td><? echo $uom; ?></td>
								<td class="wrd_brk" align="right"><? echo number_format($row['quantity'],2);  $total_quantity += $row['quantity'];?></td>
								<td class="wrd_brk" align="right"><? echo number_format($pi_rate,4); ?></td>
								<td class="wrd_brk" align="right"><? echo number_format($row['amount'],4);  $total_ammount += $row['amount'];  ?></td>
								</tr>
								<?
							}
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
                    <td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
                    </tr>
                    <tr>
                    <tr height="50"></tr>
				</table>
                 <?



				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


					?>
				<? if(count($approved_sql)>0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:900px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval Status </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="250">Name</th>
									<th width="200">Designation</th>
									<th width="50">Approval Status</th>
									<th width="100">Approval Date</th>
								</tr>
							</thead>
							<? foreach ($approved_sql as $key => $value)
							{
								?>
								<tr>
									<td width="20"><? echo $sl; ?></td>
									<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
								</tr>
								<?
								$sl++;
							}
							?>
						</table>
					</div>
					<?
				}
				?>
				<? if(count($approved_his_sql) > 0)
				{
					$sl=1;
					?>
					<div style="margin-top:15px">
						<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="width:850px;text-align:center;" rules="all">
							<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
							<thead>
								<tr style="font-weight:bold">
									<th width="20">SL</th>
									<th width="150">Approved / Un-Approved</th>
									<th width="150">Designation</th>
									<th width="50">Approval Status</th>
									<th width="150">Reason for Un-Approval</th>
									<th width="150">Date</th>
								</tr>
							</thead>
							<? foreach ($approved_his_sql as $key => $value)
							{
								if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">Yes</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

								<?
								$sl++;
								if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>

									<?
									$sl++;

								}

							}
							?>
						</table>
					</div>
					<?
				}
				?>
		        <br/>
		        <? $sql_term= sql_select("select id, terms,terms_prefix from wo_booking_terms_condition where entry_form=405 and booking_no='$data[1]' order by id");?>
				<div style="margin-left:10px">
					<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
						<tr>
							<th colspan="3">
								<strong style="font-size:15pt;">Terms & Conditions:</strong>
							</th>
						</tr>
						<tr>
							<?$i=1;
							foreach ($sql_term as $value) { ?>
							<td  width="40px"><? echo $i?></td>
							<td width="100px"><? echo $value[csf('terms_prefix')]?></td>
							<td><? echo $value[csf('terms')]?></td>
						</tr>
						<?	
						$i++;					
							}
						?>
					</table>
				</div>
		        <br/>
					 <?
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);			
				
            }
            ?>
          </div>
        	<?
		}
		?>
	</div>
	<?
	
	$html=ob_get_contents();
	ob_clean();
	echo $html;
    exit();
}


if($action=="print_f")
{	
	$data = explode('*',$data);
	// print_r($data);
	// die();
	$entryForm=$data[2];
	$cbo_item_category_id=$data[3];
	$is_mail_send=$data[4];
	$company_id=$data[0];
	$sys_id=$data[1];


	  $path='../../';

	ob_start();
	
	if($data[3] == 'PI Approval New'){
		echo load_html_head_contents($data[3],"../", 1, 1, $unicode,'','');
	}else{
		echo load_html_head_contents($data[3],"../../", 1, 1, $unicode,'','');
	}
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$sql_company = sql_select("SELECT * FROM lib_company WHERE id=$data[0] and is_deleted=0 and status_active=1");
  	foreach($sql_company as $company_data)
  	{
		if($company_data[csf('plot_no')]!='')$plot_no = 'Plot No.#'.$company_data[csf('plot_no')].','.' ';else $plot_no='';
		if($company_data[csf('level_no')]!='')$level_no = 'Level No.#'.$company_data[csf('level_no')].','.' ';else $level_no='';
		if($company_data[csf('road_no')]!='')$road_no = 'Road No.#'.$company_data[csf('road_no')].','.' ';else $road_no='';
		if($company_data[csf('block_no')]!='')$block_no = 'Block No.#'.$company_data[csf('block_no')].','.' ';else $block_no='';
		if($company_data[csf('city')]!='')$city = $company_data[csf('city')].','.' ';else $city='';
		if($company_data[csf('zip_code')]!='')$zip_code = '-'.$company_data[csf('zip_code')].','.' ';else $zip_code='';
		if($company_data[csf('country_id')]!=0)$country = $country_arr[$company_data[csf('country_id')]].','.' ';else $country='';

		$company_address = $plot_no.$level_no.$road_no.$block_no.$city.$zip_code.$country;
	}
	$approvedBy_arr = return_library_array("select mst_id, approved_by from approval_history where mst_id=$data[1] and entry_form in(21,27) ORDER BY id ASC","mst_id","approved_by");
	$userName_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	$user_library=return_library_array( "select id,user_full_name from user_passwd", "id", "user_full_name" );
	$buyer_library_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" );
	
	

	?>
	<div style="width:1000px">
	<?
		$cbo_pi_basis_id='';
		$sql_mst = sql_select("select id, item_category_id, importer_id, supplier_id, pi_number, pi_date, last_shipment_date, pi_validity_date, currency_id, source, hs_code, internal_file_no, intendor_name, pi_basis_id, remarks, import_pi, within_group, approved, total_amount, upcharge, discount, net_total_amount,inserted_by, goods_rcv_status, pay_term, tenor, location_id, pi_for, inserted_by, approved_by,buyer_id, internal_file_no from com_pi_master_details where id= $data[1]");
		$total_amount=$sql_mst[0][csf("total_amount")];
		$upcharge=$sql_mst[0][csf("upcharge")];
		$discount=$sql_mst[0][csf("discount")];
		$goods_rcv_status=$sql_mst[0][csf("goods_rcv_status")];
		$net_total_amount=$sql_mst[0][csf("net_total_amount")];
		$pi_basis_id=$sql_mst[0][csf("pi_basis_id")];
		$pi_location_name=return_field_value("location_name","lib_location","id='".$sql_mst[0][csf("location_id")]."'");
		$inserted_by=$sql_mst[0][csf("inserted_by")];
		$approved_by=$sql_mst[0][csf("approved_by")];
	
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
						<td>Pay Term</td>
						<td><? echo $sql_mst[0][csf('pay_term')];?></td>
						<td>Tenor</td>
						<td><? echo $sql_mst[0][csf('tenor')];?></td>
					</tr>
					<tr>
						<td>Insert By:</td>
						<td><? echo $user_library[$sql_mst[0][csf('inserted_by')]];?></td>
						<td>Remarks</td>
						<td><? echo $sql_mst[0][csf('remarks')];?></td>
					</tr>
					<tr>
						<td colspan="5" style="text-align:center; color:#FF0000; font-weight:bold; font-size:16px;">
							<? if($sql_mst[0][csf('approved')]==1) echo "Approved"; 
							//elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved"; else echo "&nbsp;"; 
							?>
						</td>
					</tr>
				</table>
				<br>
				<table class="rpt_table" width="100%" cellspacing="1" rules="all" border="1">
					<thead>
						<th style="width: 150px;">Job No</th>
						<th style="width: 150px;">Construction</th>
						<th style="width: 150px;">Composition</th>
						<th style="width: 100px;">Color</th>
						<th style="width: 50px;">GSM</th>
						<th style="width: 80px;">Dia/Width</th>
						<th style="width: 100px;">Item Size</th>
						<th style="width: 50px;">UOM</th>
						<th style="width: 100px;">Quantity</th>
						<th style="width: 70px;">Rate</th>
						<th>Amount</th>
					</thead>
					<tbody>
					<?
						if($goods_rcv_status==2)
						{
							$sql = "select id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, quantity, rate, amount, item_prod_id,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0";
						}
						else
						{
							if($db_type==0)
							{
								$sql = "select group_concat(id) as id, work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id ,item_size";
							}
							else
							{
								$sql = "select rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , work_order_no, color_id, fabric_construction as construction, fabric_composition as composition, gsm, dia_width, uom, sum(quantity) as quantity, avg(rate) as rate, sum(amount) as amount, item_prod_id ,item_size  from com_pi_item_details where pi_id='$data[1]' and quantity>0 and status_active=1 and is_deleted=0 group by work_order_no, color_id, fabric_construction, fabric_composition, gsm, dia_width, uom, item_prod_id,item_size";
							}
						}
						// echo $sql;
						$data_array=sql_select($sql);
						foreach($data_array as $row)
						{
						?>
							<tr>
								<td style="word-break: break-all;"><? echo $row[csf('work_order_no')]; ?></td>
								<td style="word-break: break-all;"><? echo $row[csf('construction')]; ?></td>
								<td style="word-break: break-all;"><? echo $row[csf('composition')]; ?></td>
								<td style="word-break: break-all;"><? echo $color_library[$row[csf('color_id')]]; ?></td>
								<td style="word-break: break-all;"><? echo $row[csf('gsm')]; ?></td>
								<td style="word-break: break-all;"><? echo $row[csf('dia_width')]; ?></td>
								<td style="word-break: break-all;"><? echo $row[csf('item_size')]; ?></td>
								<td style="word-break: break-all;"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
								<td style="word-break: break-all;" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
								<td style="word-break: break-all;" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
								<td style="word-break: break-all;" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')];  ?></td>
							</tr>
						<?
						}
						?>
						<tr class="tbl_bottom" height="25">
							<td align="right" colspan="8">Sum</td>
							<td align="right"><? echo number_format($total_quantity,4); ?></td>
							<td>&nbsp;</td>
							<td align="right"><? echo number_format($total_ammount,4); ?></td>
						</tr>

						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Upcharge</td>
							<td><? echo number_format($upcharge,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Discount</td>
							<td><? echo number_format($discount,4); ?></td>
						</tr>
						<tr class="tbl_bottom" height="25">
							<td  colspan = "10" align="right">Net Total</td>
							<td><? echo number_format($net_total_amount,4); ?></td>
						</tr>
					</tbody>
				</table>
				<table>
					<tr height="20"><td></td></tr>
					<tr>
						<td valign="top"><strong>In-Words: </strong></td>
						<td><?

						if($sql_mst[0][csf('currency_id')]*1==1)
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

						}
						else
						{
							echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
						}
						?></td>
					</tr>
					<tr height="50"><td></td></tr>
				</table>
				<?
				$approved_sql=sql_select("SELECT  mst_id, approved_by ,min(approved_date) as approved_date  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]."  group by mst_id, approved_by order by  approved_by ");
				$approved_his_sql=sql_select("SELECT  mst_id, approved_by ,approved_date,un_approved_reason,un_approved_date,approved_no  from approval_history where entry_form in(21,27) AND  mst_id =".$data[1]." order by  approved_no,approved_date");
				$user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
				$user_lib_desg=return_library_array("SELECT id,designation from user_passwd", "id", "designation");
				$designation_lib=return_library_array("SELECT id,custom_designation from lib_designation", "id", "custom_designation");


			?>
			<? if(count($approved_sql)>0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval Status </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="250">Name</th>
								<th width="200">Designation</th>
								<th width="100">Approval Date</th>
							</tr>
						</thead>
						<? foreach ($approved_sql as $key => $value)
						{
							?>
							<tr>
								<td width="20"><? echo $sl; ?></td>
								<td width="250"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="200"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="100"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1];  ?></td>
							</tr>
							<?
							$sl++;
						}
						?>
					</table>
				</div>
				<?
			}
			?>
			<? if(count($approved_his_sql) > 0)
			{
				$sl=1;
				?>
				<div style="margin-top:15px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" style="width:850px;text-align:center;" rules="all">
						<label><b>Pro Forma Invoice Approval / Un-Approval History </b></label>
						<thead>
							<tr style="font-weight:bold">
								<th width="20">SL</th>
								<th width="150">Approved / Un-Approved</th>
								<th width="150">Designation</th>
								<th width="50">Approval Status</th>
								<th width="150">Reason for Un-Approval</th>
								<th width="150">Date</th>
							</tr>
						</thead>
						<? foreach ($approved_his_sql as $key => $value)
						{
							if($sl%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
								<td width="20"><? echo $sl; ?></td>
								<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
								<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
								<td width="50">Yes</td>
								<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
								<td width="150"><? $approved_date=explode(" ",$value[csf("approved_date")]);

								echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
							</tr>

							<?
							$sl++;
							if($value[csf("un_approved_date")] !='0000-00-00 00:00:00' && $value[csf("un_approved_date")] !='')
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>">
									<td width="20"><? echo $sl; ?></td>
									<td width="150"><? echo $user_lib_name[$value[csf("approved_by")]]; ?></td>
									<td width="150"><? echo $designation_lib[$user_lib_desg[$value[csf("approved_by")]]]; ?></td>
									<td width="50">No</td>
									<td width="150"><? echo $value[csf("un_approved_reason")] ?></td>
									<td width="150"><? $approved_date=explode(" ",$value[csf("un_approved_date")]);

									echo change_date_format($approved_date[0])." ".$approved_date[1]; ?></td>
								</tr>
								<?
								$sl++;
							}
						}
						?>
					</table>
				</div>
				<?
			}
			?>
			<!-- //approved status end-->
			<br/>
				<?
				//signature_table($report_id, $company, $width, $template_id,$padding_top=70)

					// echo signature_table(147, $data[0],"900px",$data[18],"",$sql_mst[0][csf("inserted_by")]);
				// 	$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
					
				//     $userSignatureArr[$approved_by]=$path.$signature_arr[$approved_by];
				// echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);		
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];

				echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);	
				?>

				</div>
			<?
		}
		else{
			$pi_order_sql=="";$pi_wo_data=array();$pi_wo_check=0;
			$pi_order_sql="select b.po_break_down_id as PO_ID from com_pi_item_details a, wo_booking_dtls b where a.work_order_dtls_id=b.id and a.item_category_id=4 and b.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.pi_id=$data[1] group by b.po_break_down_id ";
			
			$pi_wo_sql="select a.pi_id, b.id as wo_dtls_id, b.trim_group, b.uom, c.description, c.rate, c.color_number_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.cons as wo_qnty, c.rate as rate, c.amount as wo_amount from com_pi_item_details a, wo_booking_dtls b, wo_trim_book_con_dtls c where a.work_order_dtls_id=b.id and b.id=c.wo_trim_booking_dtls_id and c.cons>0 and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.pi_id=$data[1]";
			$pi_wo_sql_result=sql_select($pi_wo_sql);
			if (count($pi_wo_sql_result)>0) $pi_wo_check=1;
			foreach($pi_wo_sql_result as $row)
			{
				$pi_wo_data[$row[csf('wo_dtls_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_number_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_qnty']+=$row[csf('wo_qnty')];
				$pi_wo_data[$row[csf('wo_dtls_id')]][$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_number_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_amount']+=$row[csf('wo_amount')];
			}
			//echo '<pre>';print_r($pi_wo_data);die;

			//echo $pi_order_sql;die;
			if($pi_order_sql!="")
			{
				$sql_lc_sc="select b.wo_po_break_down_id as PO_ID, a.export_lc_no as LC_SC_NO, a.internal_file_no as FILE_NO 
				from com_export_lc a, com_export_lc_order_info b 
				where a.id=b.com_export_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$data[0] and b.wo_po_break_down_id in($pi_order_sql)
				union all
				select b.wo_po_break_down_id as PO_ID, a.contract_no as LC_SC_NO, a.internal_file_no as FILE_NO 
				from com_sales_contract a, com_sales_contract_order_info b 
				where a.id=b.com_sales_contract_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$data[0] and b.wo_po_break_down_id in($pi_order_sql)";
			}
			
			//echo $sql_lc_sc;die;
			$lc_sc_no=$ls_sc_file="";
			$sql_lc_sc_result=sql_select($sql_lc_sc);
			$lc_sc_data=array();
			foreach($sql_lc_sc_result as $row)
			{
				if($lc_sc_check[$row["LC_SC_NO"]]=="")
				{
					$lc_sc_check[$row["LC_SC_NO"]]=$row["LC_SC_NO"];
					$lc_sc_no.=$row["LC_SC_NO"].",";
				}
				if($file_check[$row["FILE_NO"]]=="")
				{
					$file_check[$row["FILE_NO"]]=$row["FILE_NO"];
					$ls_sc_file.=$row["FILE_NO"].",";
				}
			}
			//print_r($lc_sc_data);die;
			unset($sql_lc_sc_result);
			$lc_sc_no=chop($lc_sc_no,",");
			$ls_sc_file=chop($ls_sc_file,",");
			if($db_type==0)
			{
				$approval_cause=return_field_value("group_concat(approval_cause) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			else
			{
				$approval_cause=return_field_value("listagg(cast(approval_cause as varchar(4000)),',') within group(order by id) as approval_cause","fabric_booking_approval_cause","booking_id=$data[1]","approval_cause");
			}
			?>
			<style>
				.wrd_brk{word-break: break-all;}
				.left{text-align: left;}
				.center{text-align: center;}
				.right{text-align: right;}

				.myTable { border-collapse:collapse; float: left; border-spacing: 0;}
				.myTable td, .myTable th { padding:3px;border:1px solid #000; }

				.row {
				display: flex;
				margin-left:-5px;
				margin-right:-5px;
				padding: 5px;
				}

				.column {
				flex: 30%;
				padding: 2px;
				}

				
			</style>
			<!-- <div style="width:90%" style="margin-left: auto; margin-right: auto;"> -->
				<table width="70%" style="margin-left: auto; margin-top: 10px; margin-right: auto; border: 2px solid black;">
					<?
	                foreach($sql_mst as $row_mst)
	                {
						$i++;
						$supplier_id=$row_mst[csf('supplier_id')];
						$sql_supplier=sql_select("SELECT id,supplier_name,country_id,web_site,email,address_1,address_2,address_3,address_4,contact_no FROM lib_supplier WHERE id=$supplier_id");
						foreach($sql_supplier as $supplier_data)
						{
							if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
							if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].' '.' ';else $address_2='';
							if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')].','.' ';else $address_3='';
							if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')].','.' ';else $address_4='';
							if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')].','.' ';else $web_site='';
							if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')].' '.' ';else $email='';
							if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')].' '.' ';else $contact_no='';
							if($supplier_data[csf('country_id')]!=0)$country = $country_arr[$supplier_data[csf('country_id')]].','.' ';else $country='';
							$supplier_address = $address_1.$address_3.$address_4.$web_site.$country;
						}
						?>
							<tr>
								<td style="font-size:20px;" align="left" colspan="6"><strong><? echo $supplier_name_library[$row_mst[csf('supplier_id')]]; ?></strong></td>
							</tr>
							<tr>
								<td style="font-size:12px;" align="left" colspan="6">Address:&nbsp;</Address><strong><? echo $supplier_address; ?></strong></td>
							</tr>
							<tr>
								<td>Telephone:&nbsp;<?echo $contact_no; ?></td>
							</tr>
							<tr>
								<td>Email:&nbsp;<?echo $email; ?></td>
							</tr>
							<tr>
								<td>Factory Address: &nbsp;<?echo $address_2;?></td>
							</tr>
						</table>
						<table width="100%">
						<tr>
						   <td width="100" align="center" colspan="6" style="font-size:12px;"><strong><u>PROFORMA INVOICE</u></strong></td>
						</tr>
						</table>
						
						<table width="60%" style="display: inline-block;">
						<tr><br></tr>
						    <?
								$importer_id  = $row_mst[csf('importer_id')];
								$sql_importer=sql_select("SELECT id,contact_no,email,company_name FROM lib_company WHERE id= '$importer_id'");
								foreach($sql_importer as $importer_data)
								{
									if($importer_data[csf('contact_no')]!='')$contact_no = $importer_data[csf('contact_no')].','.' ';else $contact_no='';
									if($importer_data[csf('email')]!='')$email = $importer_data[csf('email')].' '.' ';else $email='';
								}
							?>
							<tr><td>&nbsp;</td><td width="300">To:  <? echo $importer_name_library[$row_mst[csf('importer_id')]];?></td></tr>
							<tr><td>&nbsp;</td><td width="300">Address:  <? echo $company_address;?></td></tr>
							<tr><td>&nbsp;</td><td width="300">Telephone:  <? echo $contact_no;?></td></tr>
							<tr><td>&nbsp;</td><td width="300">Email:  <? echo $email;?></td></tr>
						</table>

						<table class="myTable" width="30%" style="float: right;">
							<tr>
								<td width="70" >System ID:</td>
								<td width="70" ><b><? echo $sql_mst[0][csf("id")];?></b></td>
							</tr>
							<tr>
								<td width="70">Approval Status</td>
								<td width="70" ><b><? if($sql_mst[0][csf('approved')]==1) echo "<b style='color:green;'>"."Approved"."</b>"; elseif($sql_mst[0][csf('approved')]==3) echo "Partial Approved"; else echo "<b style='color:red;'>"."Not Approved"."</b>"; ?></b></td>
							</tr>
						</table>
				
						<div class="row">
							<div class="column">
								<table class="myTable" width="330px">
									<tr>
										<td width="70">&nbsp;PI NO:</td>
										<td width="100">&nbsp;<b><? echo $row_mst[csf('pi_number')];?></b></td>
									</tr>
									<tr>
										<td>&nbsp;PI Date:</td>
										<td>&nbsp;<b><? echo change_date_format($row_mst[csf('pi_date')]);?></b></td>
									</tr>
									<tr>
										<td>&nbsp;Buyer:</td>
										<td>&nbsp;<b><?echo $buyer_library_arr[$row_mst[csf('buyer_id')]];?></b></td>
									</tr>
									<tr>
										<td>&nbsp;LC/SC No:</td>
										<td>&nbsp;<b><? echo $lc_sc_no;?></b></td>
									</tr>
								</table>
							</div>
							<div class="column">
								<table class="myTable" width="330px">
									<tr>
										<td width="70">&nbsp;File NO:</td>
										<td width="100">&nbsp;<b><? echo $row_mst[csf('internal_file_no')];?></b></td>
									</tr>
									<tr>
										<td>&nbsp;HS Code:</td>
										<td>&nbsp;<b><? echo $row_mst[csf('hs_code')];?></b></td>
									</tr>
									<tr>
										<td>&nbsp;Pay Term:</td>
										<td>&nbsp;<b><? echo $pay_term[$sql_mst[0][csf("pay_term")]];?></b></td>
									</tr>
									<tr>
										<td>&nbsp;Tenor:</td>
										<td>&nbsp;<b><? echo $sql_mst[0][csf("tenor")];?></b></td>
									</tr>
								</table>
							</div>
							<div class="column">
								<table class="myTable" width="330px" >
									<tr>
										<td width="70">&nbsp;Last Shipment Date:</td>
										<td width="100">&nbsp;<b><? echo change_date_format($row_mst[csf('last_shipment_date')]);?></b></td>
									</tr>
									<tr>
										<td>&nbsp;Currency:</td>
										<td>&nbsp;<b><? echo $currency[$row_mst[csf('currency_id')]];?></b></td>
									</tr>
									<tr>
										<td>&nbsp;Good recv. status:</td>
										<td>&nbsp;<b><? if($row_mst[csf('goods_rcv_status')] ==1) echo "After Goods Receive"; else echo "Before Goods Receive"; ?></b></td> 
									</tr>
									<tr>
										<td>&nbsp;Remarks:</td>
										<td>&nbsp;<b><? echo $sql_mst[0][csf("remarks")];?></b></td>
									</tr>
								</table>
							</div>			

							<?
								$cbo_pi_basis_id=$row_mst[csf('pi_basis_id')];
								}
							?>
          				</div>

				<br> <br>

				<table class="myTable" width="100%" cellspacing="1"  border="1" rules="all">
					<thead>
						<tr>   
							<? if( $cbo_pi_basis_id == 1 ) { ?>
							<th class="header" style="min-width: 60px;">WO</th>
							<? } ?>
							<th style="min-width: 60px;">WO Type</th>
							<th style="min-width: 60px;">Buyer</th>
							<!-- <th style="min-width: 60px;">PO Number</th> -->
							<th style="min-width: 100px;">Style Ref.</th>
							<th style="min-width: 50px;">HS Code</th>
							<th style="min-width: 60px;">Item Group</th>
							<th style="min-width: 100px;">Item Description</th>
							<th style="min-width: 50px;">Gmts Color</th>
							<th style="min-width: 50px;">Gmts Size</th>
							<th style="min-width: 50px;">Item Color</th>
							<!-- <th style="min-width: 50px;">Item Size</th> -->
							<th style="min-width: 30px;">UOM</th>
							<?
							// if($pi_basis_id==1 && $pi_wo_check==1)
							// {
								?>
								<!-- <th style="min-width: 60px;">WO Qnty</th>
								<th style="min-width: 50px;">WO Rate</th>
								<th style="min-width: 60px;">WO Amount</th> -->
								<?
							//}
							?>
							<th style="min-width: 60px;">Quantity</th>
							<th style="min-width: 50px;">Rate</th>
							<th style="min-width: 60px;">Amount</th>
						</tr>
					</thead>
					<tbody>
					<?

					if($goods_rcv_status==2)
					{
						$data_array=sql_select("SELECT a.id, a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.quantity, a.rate, a.amount, a.service_type, a.status_active, a.item_prod_id, a.hs_code, a.brand_supplier,a.order_no FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0 ORDER BY a.id ASC");
					}
					else
					{
						
						if($db_type==0)
						{
							$data_array=sql_select("SELECT group_concat(a.id) as id, a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0
							group by a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no
							ORDER BY a.id ASC");
						}
						else
						{
							
							$data_array=sql_select("SELECT rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') AS id , a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, sum(a.quantity) as quantity, avg(a.rate) as rate, sum(a.amount) as amount, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no FROM com_pi_item_details a WHERE a.pi_id = $data[1] and a.status_active=1 and a.is_deleted=0
							group by a.pi_id, a.work_order_no, a.work_order_dtls_id, a.color_id, a.size_id, a.item_color, a.item_size, a.item_group, a.item_description, a.gsm, a.dia_width, a.uom, a.service_type, a.status_active, item_prod_id, a.hs_code, a.brand_supplier,a.order_no");
						}

					}
					
					$wo_po=sql_select("SELECT a.id as booking_dtlsid, a.booking_no, a.trim_group, b.style_ref_no, b.buyer_name FROM wo_booking_dtls a, wo_po_details_master b where a.job_no=b.job_no and b.company_name=$data[0] and a.status_active=1");

					foreach($data_array as $row){
						$wo_dtls_id_all.=$row[csf('work_order_dtls_id')].',';
					}
					$wo_dtls_id_all=chop($wo_dtls_id_all,',');


					$is_sort_array=return_library_array( "SELECT id, is_short FROM wo_booking_dtls WHERE  status_active = 1 AND is_deleted = 0 and id in($wo_dtls_id_all) ",'id','is_short');

					$wo_ord_arr=array(); //$wo_ord_buyer_arr=array();
					foreach($wo_po as $row)
					{
						$wo_ord_arr[$row[csf('booking_dtlsid')]]['style_ref_no'].=$row[csf('style_ref_no')].',';
						$wo_ord_arr[$row[csf('booking_dtlsid')]]['buyer_name'].=$buyer_library_arr[$row[csf('buyer_name')]].',';
						//$wo_ord_buyer_arr[$row[csf('booking_no')]][$row[csf('trim_group')]].=$buyer_library_arr[$row[csf('buyer_name')]].',';
					}
					/*echo "<pre>";
					print_r($wo_ord_buyer_arr);die;*/
					if($db_type==0) $style_grpCond="group_concat(style_des)";
					else if($db_type==2) $style_grpCond="listagg(cast(style_des as varchar(4000)),',') within group (order by style_des)";
					
					$wo_nonOrd_arr = return_library_array('SELECT booking_no, $style_grpCond as style_ref FROM wo_non_ord_samp_booking_dtls where status_active=1 group by booking_no','booking_no','style_ref');

					$wo_nonPo=sql_select("SELECT a.booking_no, b.style_des, a.buyer_id FROM  wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$data[0] and a.status_active=1 ");
					$wo_nonOrd_buyer_arr=array();
					foreach($wo_nonPo as $row)
					{
						$wo_nonOrd_buyer_arr[$row[csf('booking_no')]].=$buyer_library_arr[$row[csf('buyer_id')]].',';
					}
					/*echo "<pre>";
					print_r($wo_nonOrd_buyer_arr);die;*/


					$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
					$i = 0;
					$total_ammount = 0;
					
					foreach($data_array as $row)
					{
						if($db_type==2 && $goods_rcv_status !=2) $row[csf('id')] = $row[csf('id')]->load();
						$wo_style_no=''; $wo_buyer_name='';
						
						if($row[csf('booking_without_order')]==1) 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_nonOrd_arr[$row[csf('work_order_no')]])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_nonOrd_buyer_arr[$row[csf('work_order_no')]])));
						}
						else 
						{
							$wo_style_no=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_dtls_id')]]['style_ref_no'])));
							$wo_buyer_name=implode(",",array_unique(explode(",",$wo_ord_arr[$row[csf('work_order_dtls_id')]]['buyer_name'])));
						} 
						if($is_sort_array[$row[csf('work_order_dtls_id')]]==1 ){
							$wo_title="Short";
						}
						if($is_sort_array[$row[csf('work_order_dtls_id')]]==2 ){
							$wo_title="Main Booking";
						}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<? if( $cbo_pi_basis_id == 1 ) { ?>
							<td class="wrd_brk"><? echo $row[csf('work_order_no')]; ?></td>
							<? } ?>
							<td class="wrd_brk"><? echo $wo_title; ?></td>
							<td class="wrd_brk"><? //echo $arrayName[$wo_ord_buyer_arr[$row[csf('work_order_no')]]];
								echo rtrim($wo_buyer_name,','); ?></td>
							<!-- <td class="wrd_brk"><?// echo $row["ORDER_NO"]; ?></td> -->
							<td class="wrd_brk"><? echo rtrim($wo_style_no,','); ?></td>
							<td class="wrd_brk"><? echo $row[csf('hs_code')]; ?></td>
							<td class="wrd_brk"><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
							<td class="wrd_brk"><? echo $row[csf('item_description')]; ?></td>
							<td class="wrd_brk"><? echo $color_library[$row[csf('color_id')]]; ?></td>
							<td class="wrd_brk"><? echo $size_library[$row[csf('size_id')]]; ?></td>
							<td class="wrd_brk"><? echo $color_library[$row[csf('item_color')]]; ?></td>
							<!-- <td class="wrd_brk"><?// echo $row[csf('item_size')]; ?></td> -->
							<td class="wrd_brk"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<?
							// if($pi_basis_id==1 && $pi_wo_check==1)
							// {
							// 	$wo_qnty=$pi_wo_data[$row[csf('work_order_dtls_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_qnty'];
							// 	$wo_amt=$pi_wo_data[$row[csf('work_order_dtls_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]['wo_amount'];
								
							// 	$wo_rate=$wo_amt/$wo_qnty;
							// 	?>
							 	<!-- <td class="wrd_brk"  align="right"><?// echo number_format($wo_qnty,2);  $tot_wo_quantity += $wo_qnty;?></td>
							 	<td class="wrd_brk" align="right"><?// echo number_format($wo_rate,4); ?></td>
							 	<td class="wrd_brk" align="right"><?// echo number_format($wo_amt,4);  $tot_wo_amt += $wo_amt;?></td> -->
							 	<?
							// }
							?>
							<td class="wrd_brk" align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
							<td class="wrd_brk" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td class="wrd_brk" align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
						<?
					}
					?>
						<tr class="tbl_bottom">
							<?
							if( $cbo_pi_basis_id == 1) {if ($pi_wo_check) $colspan="11"; else $colspan="8";} else $colspan="7";
							?>
							<td  colspan = "<? echo $colspan+2; ?>" align="right">Total</td>
							<!-- <td><?// echo number_format($total_quantity,2); $total_quantity = 0;?></td>
							<td></td> -->
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
		                    <td><?

								if($sql_mst[0][csf('currency_id')]*1==1)
								{
									echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Poisa');

								}
								else
								{
									echo number_to_words(number_format($net_total_amount,2, '.', ''),$currency[$sql_mst[0][csf('currency_id')]],'Cent');
								}
								?>
							</td>
	                    </tr>
	                <tr>
                    <tr height="50"></tr>
				</table>
			<?
		}
				$signature_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME='user_signature'",'MASTER_TBLE_ID','IMAGE_LOCATION');
				$appSql="select APPROVED_BY from APPROVAL_HISTORY where ENTRY_FORM in(7,27) and MST_ID = $data[1]";
				// echo $appSql;
				$appSqlRes=sql_select($appSql);
				foreach($appSqlRes as $row){
					$userSignatureArr[$row["APPROVED_BY"]]=$path.$signature_arr[$row["APPROVED_BY"]];	
				}
				//$userSignatureArr[$user_lib_name[$inserted_by]]=$path.$signature_arr[$inserted_by];
                echo signature_table(147, $data[0],"900px",$data[18],"",$user_library[$inserted_by],$userSignatureArr);	
		?>
	</div><?
}
?>

<link href="../../css/style_common.css" rel="stylesheet" type="text/css" />


