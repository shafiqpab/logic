<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );
$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  );
$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","");
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/hourly_gmt_finishing_monitoring_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/hourly_gmt_finishing_monitoring_report_controller' );",0 );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
	exit();
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor_id','0','0','','0');\n";
	echo 'setTimeout[($("#floor_td a").attr("onclick","disappear_list(cbo_floor_id,0);getLineId();") ,3000)];';
    exit();
}

if($action=="cutting_popup_one")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('*',$data);
	?>
	<?
      $color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	  $size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name" );
      $sql_cond = "";
	  $sql_cond .= ($company_id!=0) ? "and f.company_name=$company_id" : "";
	  $sql_cond .= ($location_id!=0) ? "and f.location_name=$location_id" : "";
	  $sql_cond .= ($floor_id!="") ? "and b.floor_id in($floor_id)" : "";
	  $sql_cond .= ($buyer_name!=0) ? "and f.buyer_name=$buyer_name" : "";
	  $sql_cond .= ($txt_date!="") ? "and b.production_date='$txt_date'" : "";
	  $sql_cond .= ($job_id!="") ? "and f.id='$job_id'" : "";
	  $sql_cond .= ($po_id!="") ? "and a.id='$po_id'" : "";

	//   if (str_replace("'","",$job_no)!="") $job_cond="and d.job_no_mst='".$job_no."'";

	  $query= "Select a.po_number,d.job_no_mst,d.color_number_id,d.size_number_id,c.production_qnty from wo_po_break_down a,
	           wo_po_color_size_breakdown d,pro_garments_production_mst b,pro_garments_production_dtls c,wo_po_details_master f where a.id=d.po_break_down_id and b.id=c.mst_id and a.job_id=f.id and c.color_size_break_down_id=d.id and a.status_active=1 and a.is_deleted=0
			   and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond";

		//  echo $query;
		$querypopup=sql_select($query);
        $colorsizearr=array();
		$sizeArray = array();
		foreach($querypopup as $val){

				$colorsizearr[$val[csf('color_number_id')]][$val[csf('size_number_id')]]['qty']+=$val[csf('production_qnty')];
				$sizeArray[$val[csf('size_number_id')]] = $val[csf('size_number_id')];

		}

		// echo '<pre>';
		// print_r($colorsizearr);
		// echo '</pre>';
		$tbl_width = 430+(count($colorsizearr)*50);

	?>
          <!-- Table Start From Here -->
					<div style="width:<?=$tbl_width+20;?>px;" align="center">
			     	<table width="<?=$tbl_width;?>" cellspacing="0" border="1" class="rpt_table" rules="all" >
					   <thead>
						   <tr>
									<th width="30">SL</th>
									<th width="100">Order No</th>
									<th width="100">Job No</th>
									<th width="100">Color</th>

									<?
										foreach($sizeArray as $size_id)
										{
											?>
											<th width="50"><?
											echo $size_library[$size_id];
											?></th>
											<?
										}
									?>
								   <th width="100" > Color Total</th>

						   </tr>
					   </thead>
					   <tbody>
						 <?
						   $i=1;
						   foreach($colorsizearr as $color_id=>$color_data)
						   {
							    $total=0;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>">

									<td><? echo $i; ?></td>
									<td><? echo $val['PO_NUMBER']; ?></td>
									<td><? echo $val['JOB_NO_MST']; ?></td>
									<td><? echo $color_library[$color_id]; ?></td>

									<?
										foreach($sizeArray as $sizeID)
										{
												?>
												<td align="right">
													<? 	
														echo $color_data[$sizeID]['qty'];
														$total+=$color_data[$sizeID]['qty'];
													?>
												</td>
												<?
										}
										
									?>
									<td align="right"> <? echo $total; ?></td>

                             <?
							 $i++;

						     }

                         ?>
					   </tbody>
					</table>
	               </div>

    <?
	exit();
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name");
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name");
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");
	$color_library=return_library_array( "select id,color_name from lib_color ", "id", "color_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line ", "id", "line_name"  );
	$sewLineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");

	$company_id = str_replace("'","",$cbo_company_id);
	$location_id = str_replace("'","",$cbo_location_id);
	$floor_id = str_replace("'","",$cbo_floor_id);
	$buyer_name = str_replace("'","",$cbo_buyer_name);
	$txt_date = str_replace("'","",$txt_date);

	$sql_cond = "";
	$sql_cond .= ($company_id!=0) ? "and c.company_name=$company_id" : "";
	$sql_cond .= ($location_id!=0) ? "and c.location_name=$location_id" : "";
	$sql_cond .= ($floor_id!="") ? "and a.floor_id in($floor_id)" : "";
	$sql_cond .= ($buyer_name!=0) ? "and c.buyer_name=$buyer_name" : "";
	$sql_cond .= ($txt_date!="") ? "and a.production_date='$txt_date'" : "";


	$hour = 9;
	$last_hour=23;


	$start_hour_arr=array();
	$start_hour='09:00';
	$start_hour_arr[$hour]=$start_hour;
	for($j=$hour;$j<$last_hour;$j++)
	{
		$start_hour=add_time($start_hour,60);
		$start_hour_arr[$j+1]=substr($start_hour,0,5);
	}
	//echo $pc_date_time;die;
	$start_hour_arr[$j+1]='23:59';

	// echo"<pre>";print_r($start_hour_arr);die;


	/* =====================================================================================================/
	/												Gmts Production data									/
	/===================================================================================================== */
	$sql="SELECT c.buyer_name,c.style_ref_no,c.job_no,a.floor_id,a.production_type, b.production_qnty as good_qnty, d.id as po_id, c.id as job_id,";
	$first=1;
	for($h=$hour;$h<$last_hour;$h++)
	{
		$bg=$start_hour_arr[$h];
		$end=substr(add_time($start_hour_arr[$h],60),0,5);
		$prod_hour="prod_hour_fin".substr($bg,0,2);
		$prod_hour2="prod_hour_iron".substr($bg,0,2);
		$prod_hour3="prod_hour_poly".substr($bg,0,2);
		$prod_hour4="prod_hour_ht".substr($bg,0,2);
		if($first==1)
		{
			$sql.=" (CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=8 THEN b.production_qnty else 0 END) AS $prod_hour,";
			$sql.="(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=7 THEN b.production_qnty else 0 END) AS $prod_hour2,";
			$sql.="(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=11 THEN b.production_qnty else 0 END) AS $prod_hour3,";
			$sql.="(CASE WHEN  TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=15 THEN b.production_qnty else 0 END) AS $prod_hour4,";
		}
		else
		{
			$sql.=" (CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=8 THEN b.production_qnty else 0 END) AS $prod_hour,";
			$sql.="(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=7 THEN b.production_qnty else 0 END) AS $prod_hour2,";
			$sql.="(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=11 THEN b.production_qnty else 0 END) AS $prod_hour3,";
			$sql.="(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>='$bg' and TO_CHAR(a.production_hour,'HH24:MI')<'$end' and a.production_type=15 THEN b.production_qnty else 0 END) AS $prod_hour4,";
		}
		$first++;
	}
	$sql.=" (CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=8 THEN b.production_qnty else 0 END) AS prod_hour_fin23,";
	$sql.="(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=7 THEN b.production_qnty else 0 END) AS prod_hour_iron23,";
	$sql.="(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=11 THEN b.production_qnty else 0 END) AS prod_hour_poly23,";
	$sql.="(CASE WHEN TO_CHAR(a.production_hour,'HH24:MI')>'$start_hour_arr[$last_hour]' and TO_CHAR(a.production_hour,'HH24:MI')<='$start_hour_arr[24]' and a.production_type=15 THEN b.production_qnty else 0 END) AS prod_hour_ht23";

	$sql.=" FROM  pro_garments_production_mst a ,pro_garments_production_dtls b, wo_po_details_master c, wo_po_break_down d,wo_po_color_size_breakdown e
	WHERE a.id=b.mst_id and a.po_break_down_id=d.id and d.job_id=c.id and d.job_id=e.job_id and d.id=e.po_break_down_id and b.color_size_break_down_id=e.id and a.production_type in(7,8,11,15) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond
	ORDER BY a.production_date";
	//  echo $sql;die;
	$res = sql_select($sql);

	$data_array = array(); $floor_total=array();// prod-type, floor, buyer, job
	foreach ($res as $v)
	{

		// echo '<pre>';
		// print_r($data_array);
		// echo '</pre>';
		for($h=$hour;$h<$last_hour;$h++)
		{
		   if($v['PRODUCTION_TYPE']==7)
			{
				// echo "abc";
				$prod_hour="prod_hour_iron".substr($start_hour_arr[$h],0,2)."";
				$seq=0;
			}
		    else if($v['PRODUCTION_TYPE']==8)
			{
				$prod_hour="prod_hour_fin".substr($start_hour_arr[$h],0,2)."";
                $seq=3;
			}


			else if($v['PRODUCTION_TYPE']==11)
			{
				$prod_hour="prod_hour_poly".substr($start_hour_arr[$h],0,2)."";
				$seq=2;
			}
			else if($v['PRODUCTION_TYPE']==15)
			{
				$prod_hour="prod_hour_ht".substr($start_hour_arr[$h],0,2)."";
				$seq=1;
			}
			// echo $prod_hour;die;

			$data_array[$seq][$v['PRODUCTION_TYPE']][$v['FLOOR_ID']][$v['BUYER_NAME']][$v['JOB_NO']][$prod_hour]['qty'] += $v[csf($prod_hour)];
			$data_array[$seq][$v['PRODUCTION_TYPE']][$v['FLOOR_ID']][$v['BUYER_NAME']][$v['JOB_NO']]['po_id'] = $v['PO_ID'];
			$data_array[$seq][$v['PRODUCTION_TYPE']][$v['FLOOR_ID']][$v['BUYER_NAME']][$v['JOB_NO']]['job_id'] = $v['JOB_ID'];
            $data_array[$seq][$v['PRODUCTION_TYPE']][$v['FLOOR_ID']][$v['BUYER_NAME']][$v['JOB_NO']]['style'] = $v['STYLE_REF_NO'];
			$prod_hour2="prod_hour".substr($start_hour_arr[$h],0,2)."";
			$gr_total[$prod_hour2]['qty']+=$v[csf($prod_hour)];
			$floor_total[$v['PRODUCTION_TYPE']][$prod_hour2]['qty']+=$v[csf($prod_hour)];


		}

	}
    //  	echo '<pre>';
	//    print_r($gr_total);
	//     echo '</pre>';

	$tbl_width = 900+(count($start_hour_arr)*50);
	?>
	<fieldset>
		<div class="tbl-body" style="width:<?=$tbl_width+20;?>px">
			<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="100">Type</th>
						<th width="100">Buyer</th>
						<th width="100">Style</th>
						<th width="100">Job</th>
						<th width="100">Hourly Target</th>

						<?
						for($k=$hour; $k<=$last_hour; $k++)
						{
							?>
							<th width="50" style="vertical-align:middle"><p><div class="block_div"><?=substr($start_hour_arr[$k],0,5)."-<br>".substr($start_hour_arr[$k+1],0,5);?></div></p></th>
							<?
						}
						?>
						<th width="100">Today Target</th>
						<th width="100">Today Prod</th>
						<th width="100">Today Achv%</th>
						<th width="100">Today Effi%</th>
					</tr>
				</thead>
			</table>
		</div>
		<div class="tbl-body" style="width:<?=$tbl_width+20;?>px">
			<table class="rpt_table" width="<?=$tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<tbody>
					<?
					ksort($data_array);
					$i=1;
					foreach($data_array as $seq_no=>$seq_data)
					{
							foreach($seq_data as $production_ty=>$production_data)
							{

								$floor_total_today_prod=0;
								foreach($production_data as $floor_id=>$floor_data)
								{
									foreach($floor_data as $buyer_name=>$buyer_data)
									{
										foreach($buyer_data as $job_no=>$row)
										{
											$total=0;
											// $grprod=0;
											if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";

											?>
											<tr bgcolor="#EFEFEF"><td colspan="34"><b><? echo $floorArr[$floor_id]?>;</b></td></tr>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">

												<td width="100"><? echo $production_type[$production_ty]; ?></td>
												<td width="100"><? echo $buyerArr[$buyer_name];?></td>
												<td width="100"><? echo $row['style'];?></td>
												<td width="100"><? echo $job_no; ?></td>
												<td width="100"><??></td>

												<?
												for($k=$hour; $k<=$last_hour; $k++)
												{
													if($production_ty==7)
													{
														// echo "abc";
														$prod_hour="prod_hour_iron".substr($start_hour_arr[$k],0,2)."";
													}

													else if($production_ty==8)
													{

														$prod_hour="prod_hour_fin".substr($start_hour_arr[$k],0,2)."";

													}

													else if($production_ty==11)
													{
														$prod_hour="prod_hour_poly".substr($start_hour_arr[$k],0,2)."";
													}
													else
													{
														$prod_hour="prod_hour_ht".substr($start_hour_arr[$k],0,2)."";
													}
													?>

													<td align="right" width="50" style="vertical-align:middle"><? echo number_format($row[$prod_hour]['qty'],0);?></td>
													<?
													$total+=$row[$prod_hour]['qty'];

												}
												?>
												<td width="100"></td>
												<td width="100" align="right"><a href="##" onclick="openmypage_job_total('<? echo $company_id; ?>','<? echo $location_id; ?>', '<? echo $floor_id; ?>', '<? echo $buyer_name; ?>', '<? echo $txt_date; ?>', 'cutting_popup_one',850,350,'<? echo $row['job_id']; ?>','<? echo $row['po_id']; ?>')"><? echo $total; $grprod+=$total; ?></a></td>
												<td width="100"></td>
												<td width="100"></td>

											</tr>
											<?
												$floor_total_today_prod += $total;
											}
										}
									}
								}
							?>
						<tr style="text-align: right;font-weight:bold;background:#dccddc;">
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100"></td>
							<td width="100">Floor Total</td>
							<?
							$total = 0;
							for($k=$hour; $k<=$last_hour; $k++)
							{
								$prod_hour2="prod_hour".substr($start_hour_arr[$k],0,2)."";
								?>

								<td width="50" align="right" ><? echo $floor_total[$production_ty][$prod_hour2]['qty'];?></td>
								<?
								$total += $floor_total[$production_ty][$prod_hour2]['qty'];

							}
							?>
							<td align="right" width="100"></td>
							<td width="100"> <?= $floor_total_today_prod ?></td>
							<td width="100"></td>
							<td width="100"></td>
						</tr>
                        <?
					}
				   ?>
				</tbody>
			</table>
		</div>


	</fieldset>
	<?
	exit();
}
