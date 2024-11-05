<?
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_name_lib=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$color_name_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$date_type=str_replace("'","",$cbo_date_type);
	//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}


	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		

		if($date_type==1){

		$date_cond=" and a.insert_date between '$start_date' and '$end_date'";
		}else{
		 $date_cond=" and a.requisition_date between '$start_date' and '$end_date'";
		}


	}

	
		if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	

	   $sample_name_lib=return_library_array( "select id,sample_name from lib_sample", "id","sample_name");
	   $prod_yarn_quality_lib=return_library_array( "select style_ref_no,yarn_quality from wo_po_details_master", "style_ref_no","yarn_quality");
	   $color_lib=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	   $size_lib=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
	   $imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library where form_name='time_weight_entry' and file_type=1",'master_tble_id','image_location');

	  
	
			$data_sql="SELECT a.id as mst_id, a.company_id, a.buyer_name, a.style_ref_no, a.product_dept, a.item_name, a.item_category, a.region, a.agent_name, a.team_leader, a.dealing_marchant, a.estimated_shipdate,a.remarks, a.product_code, a.bh_merchant, a.season_buyer_wise, a.requisition_number, a.location_id, a.requisition_date, a.garments_nature, a.gauge_no_ends, a.efficiency, b.id, b.sample_mst_id,b.sample_name, b.acc_status_id, b.entry_form_id, b.color_id, b.size_id, b.designer, b.tech_manager, b.programmer, b.yarn_quality, b.count_ply, b.minute, b.second, b.movingsec, b.knitinggm,b.critical_point, b.knitingweight, c.body_part_id, c.minute, c.second, c.movingsec, c.knitinggm, d.color_id as colorid, d.body_part_id as bodypartid, d.bodycolor, a.brand_id, a.season_year, 
			  b.knitting_system, b.machine_brand_name,c.knitinglbs from sample_development_mst a, sample_development_dtls b, sample_development_fabric_acc c, sample_development_rf_color d 
			  where a.id=b.sample_mst_id and b.sample_mst_id=c.sample_mst_id and c.sample_mst_id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.entry_form_id=245 and b.entry_form_id=245 and a.company_id=$company_name  $buyer_id_cond $style_ref_cond $date_cond group by a.id, a.company_id, a.buyer_name, a.style_ref_no, a.product_dept, a.item_name, a.item_category, a.region, a.agent_name, a.team_leader, a.dealing_marchant, a.estimated_shipdate,a.remarks, a.product_code, a.bh_merchant, a.season_buyer_wise, a.requisition_number, a.location_id, a.requisition_date, a.garments_nature, a.gauge_no_ends, a.efficiency, b.id, b.sample_mst_id,b.sample_name, b.acc_status_id, b.entry_form_id, b.color_id, b.size_id, b.designer, b.tech_manager, b.programmer, b.yarn_quality, b.count_ply, b.minute, b.second, b.movingsec, b.knitinggm,b.critical_point, b.knitingweight, c.body_part_id, c.minute, c.second, c.movingsec, c.knitinggm, d.color_id , d.body_part_id , d.bodycolor, a.brand_id, a.season_year,b.knitting_system, b.machine_brand_name,c.knitinglbs order by c.body_part_id,d.body_part_id ";
			  $data_array=sql_select($data_sql);


		foreach($data_array as $row){
			$critical_points_arr = explode("___",$row[csf("critical_point")]);

			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['item_name']=$row[csf("item_name")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['mst_id']=$row[csf("mst_id")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['sample_name']=$sample_name_lib[$row[csf("sample_name")]];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['development_no']=$development_no[$row[csf("acc_status_id")]];

			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['gauge_no_ends']=$row[csf("gauge_no_ends")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['size_id']=$row[csf("size_id")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['color_id']=$row[csf("color_id")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['yarn_quality']=$row[csf("yarn_quality")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['prod_yarn_quality']+=$prod_yarn_quality_lib[$row[csf("style_ref_no")]];

			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['knit_minute']+=$row[csf("minute")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['knit_second']+=$row[csf("second")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['knit_total_second']+=$row[csf("minute")]+($row[csf("second")]/60);

			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['knit_inggm']+=$row[csf("knitinggm")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['programmer']=$row[csf("programmer")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['tech_manager']=$row[csf("tech_manager")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['designer']=$row[csf("designer")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['knitting_lbs']+=$row[csf("knitinglbs")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['mst_id']=$row[csf("mst_id")];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['knitting_comm']=$critical_points_arr[0];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['linking_comm']=$critical_points_arr[1];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['washing_comm']=$critical_points_arr[2];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['attached_comm']=$critical_points_arr[3];
			$style_wise_data_arr[$row[csf("requisition_number")]][$row[csf("buyer_name")]][$row[csf("style_ref_no")]]['finishing_comm']=$critical_points_arr[4];
			$reqArr[$row[csf("mst_id")]]=$row[csf("mst_id")];

		}
		$reqIds=implode(",",$reqArr);
		$data_dtls=sql_select("select id, sample_name, acc_status_id, color_id, size_id, designer, tech_manager, programmer, yarn_quality, count_ply, minute, second, movingsec, knitinggm, critical_point, knitingweight,knitting_system,machine_brand_name,sample_mst_id from sample_development_dtls where sample_mst_id in ($reqIds) and is_deleted=0 and status_active=1");

		foreach($data_dtls as $row){
			$req_dtls_arr[$row[csf("sample_mst_id")]]['knit_minute']+=$row[csf("minute")];
			$req_dtls_arr[$row[csf("sample_mst_id")]]['movingsec']+=$row[csf("movingsec")];
			$req_dtls_arr[$row[csf("sample_mst_id")]]['knitinggm']+=$row[csf("knitinggm")];
			$req_dtls_arr[$row[csf("sample_mst_id")]]['knitingweight']+=$row[csf("knitingweight")];

		}
		// echo "<pre>";
		// print_r($req_dtls_arr);




		ob_start();
	 ?>
		<div style="width:2560px">
		<fieldset style="width:100%;">	
			<table width="2560">
				<tr class="form_caption">
					<td colspan="25" align="center">Time And Weight Report</td>
				</tr>
				<tr class="form_caption">
					<td colspan="25" align="center"><? echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="2530" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="30">SL</th>
					<th width="100">Style Id</th>
					<th width="100">Buyer</th>
					<th width="100">Style Name</th>
					<th width="100">Image</th>
					<th width="100">Gmts . Item</th>
					<th width="100">Sample Name</th>
					<th width="100">Development No.</th>
					<th width="100">Gauge & No. Ends</th>
					<th width="100">Sample Size  </th>
					<th width="100">Sample Color</th>
					<th width="100">Sample Yarn Quality</th>
					<th width="100">Production Yarn Quality</th>
					<th width="100">Total Knitting Time</th>
					<th width="100">M/C Speed M/Sec</th>
					<th width="100">Knitting Weight (Gm)/Pcs</th>
					<th width="100"> Knit. Weight Lbs/Dzn </th>
					<th width="100">Knitting</th>
					<th width="100">Linking</th>
					<th width="100">Trimming</th>
					<th width="100">Washing</th>
					<th width="100">Add Ons/Attach</th>
					<th width="100">Finishing</th>
					<th width="100">Designer</th>
					<th width="100">Tech. Manager</th>
					<th>Programmer</th>
				</thead>
			</table>
			<div style="width:2550px; max-height:400px; overflow-y:scroll" id="scroll_body">
				<table class="rpt_table" width="2530" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">

				<?

				$i=1; 
					foreach($style_wise_data_arr as $req_id=>$buyer_data){
						foreach($buyer_data as $buyer_id=>$style_data){
							foreach($style_data as $style_id=>$row){	
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								?>

									<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $s; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $s; ?>">

											<td width="30"><?=$i;?></td>
											<td width="100"><?=$req_id;?></td>
											<td width="100"><?=$buyer_name_lib[$buyer_id];?></td>
											<td width="100"><?=$style_id;?></td>
											<td width="100" title="<?=$row['mst_id'];?>"><img  src='<? echo '../../'.$imge_arr[$row['mst_id']]; ?>' height='50px' width='80px' /></td>
											<td width="100"><?=$garments_item[$row['item_name']];?></td>
											<td width="100"><?=$row['sample_name'];?></td>
											<td width="100"><?=$row['development_no'];?></td>
											<td width="100"><?=$row['gauge_no_ends'];?></td>
											<td width="100"><?=$size_lib[$row['size_id']];?> </td>
											<td width="100"><?=$color_lib[$row['color_id']];?></td>
											<td width="100"><?=$row['yarn_quality'] ;?></td>
											<td width="100"></td>
											<td width="100" align="right"><?=$req_dtls_arr[$row['mst_id']]['knit_minute'] ;?> Minute</td>
											<td width="100" align="right"><?=$req_dtls_arr[$row['mst_id']]['movingsec'] ;?></td>
											<td width="100" align="right"><?=$req_dtls_arr[$row['mst_id']]['knitinggm'] ;?> </td>
											<td width="100" align="right"><?=$req_dtls_arr[$row['mst_id']]['knitingweight'] ; ;?></td>
											<td width="100"><?=$row['knitting_comm'];?></td>
											<td width="100"><?=$row['linking_comm'];?></td>
											<td width="100"><?=$row['sample_name'];?></td>
											<td width="100"><?=$row['washing_comm'];?></td>
											<td width="100"><?=$row['attached_comm'];?></td>
											<td width="100"><?=$row['finishing_comm'];?></td>
											<td width="100"><?=$row['designer'];?></td>
											<td width="100"><?=$row['tech_manager'];?>r</td>
											<td><?=$row['programmer'];?></td>
								</tr>
								<?php
								$i++;
							}
						}
					}
					?>

			    	</table>
				</div>
			</fieldset>
		</div>

	 <?


	

	

//===========================================================================================================================================================



	foreach (glob("*.xls") as $filename) {

	//if( @filemtime($filename) < (time()-$seconds_old) )

	@unlink($filename);

	}

	//---------end------------//

	$name=time();

	$filename=$name.".xls";

	$create_new_doc = fopen($filename, 'w');	

	$is_created = fwrite($create_new_doc,ob_get_contents());

	echo "$total_data****$filename****$tot_rows";

	exit();	

}



if($action=="booking_info")

{

	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);

	?>

	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>

	<fieldset style="width:770px; margin-left:3px">

		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">

				<thead>

                    <th width="30">Sl</th>

                    <th width="100">Wo No</th>

                    <th width="75">Wo Date</th>

                     <th width="200">Item Description</th>

                    <th width="80">Wo Qty</th>

                    <th width="100">Supplier</th>

				</thead>

                <tbody>

                <?

					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

					$i=1;

					$item_description_arr=array();

					$wo_sql_trim=sql_select("select b.id,b.item_color,b.job_no, b.po_break_down_id, b.description,b.brand_supplier,b.item_size from wo_booking_dtls a, wo_trim_book_con_dtls b where a.id=b.wo_trim_booking_dtls_id and a.is_deleted=0 and a.status_active=1 and a.job_no=b.job_no  group by b.id,b.po_break_down_id,b.job_no,b.description,b.brand_supplier,b.item_size,b.item_color");

					foreach($wo_sql_trim as $row_trim)

					{

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['job_no']=$row_trim[csf('job_no')];

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['description']=$row_trim[csf('description')];

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['size']=$row_trim[csf('item_size')];

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['supplier']=$row_trim[csf('supplier')];	

					$item_description_arr[$row_trim[csf('po_break_down_id')]][$row_trim[csf('job_no')]]['color']=$row_trim[csf('item_color')];		

	

					} //var_dump($item_description_arr);

					$wo_sql="select a.booking_no, a.booking_date, a.supplier_id,b.job_no, b.po_break_down_id,  sum(b.wo_qnty) as wo_qnty from wo_booking_mst a, wo_booking_dtls b where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.trim_group=$item_name and b.po_break_down_id='$po_id' group by b.job_no, a.booking_no, a.booking_date, a.supplier_id, b.po_break_down_id";

					$dtlsArray=sql_select($wo_sql);

					

					foreach($dtlsArray as $row)

					{

						if ($i%2==0)  

							$bgcolor="#E9F3FF";

						else

							$bgcolor="#FFFFFF";	

							$item_group=$item_library[$item_name];

							//$job_no=$row[csf('job_no')];

							$item_descrp=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['description'];

							$item_size=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['size'];

							$supplier=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['supplier'];

							$item_color=$item_description_arr[$row[csf('po_break_down_id')]][$row[csf('job_no')]]['color'];

							$item_des=$item_group.','.$item_descrp.','.$item_size.','.$color_name_library[$item_color].','.$supplier;

							$item_d2=$item_size.','.$color_name_library[$item_color].','.$supplier;

						?>

						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">

							<td width="30"><p><? echo $i; ?></p></td>

                            <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>

                            <td width="75"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>

                             <td width="200" align="right"><p><? if($item_des!='') echo $item_des; else $item_d2; ?></p></td>

                            <td width="80" align="right"><p><? echo number_format($row[csf('wo_qnty')],2); ?></p></td>

                            <td width="100"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>

                        </tr>

						<?

						$tot_qty+=$row[csf('wo_qnty')];

						$i++;

					}

				?>

                </tbody>

                <tfoot>

                	<tr class="tbl_bottom">

                    	<td colspan="4" align="right">Total</td>

                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>

                        <td>&nbsp;</td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </fieldset>

    <?

	exit();

}

disconnect($con);

?>

<?

if($action=="booking_inhouse_info")

{

	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);

	?>

	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>

	<fieldset style="width:770px; margin-left:3px">

		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">

				<thead>

                    <th width="30">Sl</th>

                    <th width="80">Prod. ID</th>

                    <th width="100">Recv. ID</th>

                     <th width="100">Recv. Date</th>

                    <th width="80">Item Description.</th>

                    <th width="100">Recv. Qty.</th>

				</thead>

                <tbody>

                <?

					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

					$i=1;

					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					

					//echo $receive_qty_data=("select b.po_breakdown_id,c.id as prod_id,c.item_description,d.recv_number,d.receive_date, a.item_group_id,sum(b.quantity) as quantity from  inv_receive_master d,inv_trims_entry_dtls a ,order_wise_pro_details b,product_details_master c where d.id=a.mst_id and a.trans_id=b.trans_id and b.trans_type=1 and a.item_group_id='$item_name' and b.po_breakdown_id=$po_id and b.entry_form=24 and a.prod_id=c.id and c.id=b.prod_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,c.item_description,d.recv_number,d.receive_date, a.item_group_id,c.id");

					$receive_qty_data=("select a.id, c.po_breakdown_id,b.item_group_id,b.prod_id as prod_id,b.item_description, a.recv_number, a.receive_date, SUM(c.quantity) as quantity

					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 

					where a.id=b.mst_id  and a.entry_form=24 and  a.item_category=4  and b.id=c.dtls_id and c.trans_type=1 and  c.po_breakdown_id in($po_id)  and b.item_group_id='$item_name' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  c.po_breakdown_id,b.item_group_id,b.prod_id,a.id,b.item_description, a.recv_number, a.receive_date");



					$dtlsArray=sql_select($receive_qty_data);

					

					foreach($dtlsArray as $row)

					{

						if ($i%2==0)  

							$bgcolor="#E9F3FF";

						else

							$bgcolor="#FFFFFF";	

							

						?>

						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">

							<td width="30"><p><? echo $i; ?></p></td>

                            <td width="80"><p><? echo $row[csf('prod_id')]; ?></p></td>

                            <td width="100" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>

                             <td width="100" align="center"><p><? echo  change_date_format($row[csf('receive_date')]); ?></p></td>

                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>

                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>

                        </tr>

						<?

						$tot_qty+=$row[csf('quantity')];

						$i++;

					}

				?>

                </tbody>

                <tfoot>

                	<tr class="tbl_bottom">

                    	<td colspan="4" align="right"></td>

                        <td align="right">Total</td>

                        <td><? echo number_format($tot_qty,2); ?></td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </fieldset>

    <?

	exit();

}

disconnect($con);

?>



<?				

if($action=="booking_issue_info")

{

	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);

	?>

	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>

	<fieldset style="width:770px; margin-left:3px">

		<div id="scroll_body" align="center">

			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0" align="center">

				<thead>

                    <th width="30">Sl</th>

                    <th width="80">Prod. ID</th>

                    <th width="100">Issue. ID</th>

                     <th width="100">Issue. Date</th>

                    <th width="80">Item Description.</th>

                    <th width="100">Issue. Qty.</th>

				</thead>

                <tbody>

                <?

					$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );

					$i=1;

					//$wo_sql="select a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description,sum(a.cons_qnty) as cons_qnty  from inv_receive_master b, inv_trims_entry_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category=4 group by a.item_group_id,a.prod_id,b.recv_number,b.receive_date,a.item_description";

					

				 $mrr_sql=("select a.id, a.issue_number,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity

					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 

					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and

					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and p.item_group_id='$item_name' group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id ");					

					

					$dtlsArray=sql_select($mrr_sql);

					

					foreach($dtlsArray as $row)

					{

						if ($i%2==0)  

							$bgcolor="#E9F3FF";

						else

							$bgcolor="#FFFFFF";	

							

						?>

						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">

							<td width="30"><p><? echo $i; ?></p></td>

                            <td width="80" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>

                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>

                             <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>

                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>

                            <td width="100" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>

                        </tr>

						<?

						$tot_qty+=$row[csf('quantity')];

						$i++;

					}

				?>

                </tbody>

                <tfoot>

                	<tr class="tbl_bottom">

                    	<td colspan="4" align="right"></td>

                        <td align="right">Total</td>

                        <td><? echo number_format($tot_qty,2); ?></td>

                    </tr>

                </tfoot>

            </table>

        </div>

    </fieldset>

    <?

	exit();

}

disconnect($con);

?>