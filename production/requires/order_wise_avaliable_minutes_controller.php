<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];
	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];



	if($action=="load_drop_down_location")
	{
		echo create_drop_down( "cbo_location_id", 140, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data' 
			order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/daily_production_status_controller', this.value, 'load_drop_down_floor', 'floor_td' ;get_php_form_data( this.value, 'eval_multi_select', 'requires/daily_production_status_controller' ;",0 ); 
		exit();  
	}



	if($action=="report_generate")
	{ 
		?>
		<style type="text/css">
		.block_div { 
			width:auto;
			height:auto;
			text-wrap:normal;
			vertical-align:bottom;
			display: block;
			position: !important; 
			-webkit-transform: rotate(-90deg);
			-moz-transform: rotate(-90deg);
		}

	</style> 
	<?
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$companyArr = return_library_array("select id,company_name from lib_company","id","company_name"); 
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$locationArr = return_library_array("select id,location_name from lib_location","id","location_name");
	$lineDataArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$lineSerialArr = return_library_array("select id,sewing_line_serial from lib_sewing_line","id","sewing_line_serial");  
	$floorArr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name"); 
	$poNumberArr = return_library_array("select id,po_number from wo_po_break_down","id","po_number"); 
	$subconPoNumberArr = return_library_array("select id,order_no from subcon_ord_dtls","id","order_no"); 
	 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$comapny_id=str_replace("'","",$cbo_company_id);
	$today_date=date("Y-m-d");
	$txt_producting_day="".str_replace("'","",$txt_date)."";
	//***************************************************************************************************************************
	
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	
	if(str_replace("'","",$cbo_company_id)==0) $company_name=""; else $company_name="and a.company_id=".str_replace("'","",$cbo_company_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location_id=""; else $location_id="and a.location_id=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location_id3=""; else $location_id3="and a.location =".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",$cbo_location_id)==0) $location_id2=""; else $location_id2="and location_id=".str_replace("'","",$cbo_location_id)."";
	if(str_replace("'","",trim($txt_date))=="") $txt_date_from=""; else $txt_date_from=" and a.production_date=$txt_date";
	?>
	
	<fieldset style="width:1550px">
		<table  width="900" cellpadding="0"  cellspacing="0" align="center" style="padding-left:200px">
			<tr>
				<td bgcolor="#FF0000" height="18" width="20" ></td>
				<td> &nbsp;Production or order tracking line not found  or Undistributed Used Minutes</td>
				<td bgcolor="#66FF99" height="18" width="20"></td>
				<td> &nbsp;Sewing Production With Multiple Order</td>
				<td bgcolor="#FFD6C1" height="18" width="20"></td>
				<td> &nbsp;No Sewing Production With Multiple Order</td>
			</tr>
		</table>
		<table id="table_header_1" class="rpt_table" width="1500" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<tr height="50">
					<th width="25">SL</th>
					<th width="80">Location Name</th>
					<th width="80">Floor Name</th>
					<th width="50">Line No</th>
					<th width="80">Buyer</th>
					<th width="140">Order No</th>
					<th width="60">File No</th>
					<th width="60">Ref. No</th>
					<th width="120">Garments Item</th>
					<th width="40">SMV</th>
					<th width="70">Used Minutes</th>
					<th width="70">Produce Minutes</th>
					<th width="60">Total Target</th>
					<th width="60">Total Prod.</th>
					<th width="60">Variance pcs </th>
					<th width="50">Operator</th>
					<th width="40">Helper</th>
					<th width="40">Man Power</th>
					<th width="50">Hourly Terget</th>
					<th width="50">Capacity</th>
					<th width="60">Working Hour</th>
					<th width="">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1500px; max-height:400px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" id="resource_allocation_tbody">
				<tbody > 
					<?php
					$sql_update=sql_select("select * from pro_resource_ava_min_mst where company_id=$cbo_company_id $location_id2 and production_date=$txt_date and status_active=1 and is_deleted=0 order by id");
					if(count($sql_update)>0)
					{
						$i=1; $save_status=2;
						foreach($sql_update as $val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$po_information_arr=explode(",",$val[csf('linepo_details')]);
							$po_number_update='';
							$flag=0;
							foreach($po_information_arr as $p_data)
							{
								$p_data_arr=explode("*",$p_data);
								if($po_number_update!='')
								{
									if($p_data_arr[12]==2)
									{
										$po_number_update=$po_number_update.",".$subconPoNumberArr[$p_data_arr[0]];	
									}
									else
									{
										$po_number_update=$po_number_update.",".$poNumberArr[$p_data_arr[0]];
									}
									if($val[csf('produced_min')]=='')
									{
										$flag=1;
										$bgcolor="#FFD6C1";
									}
									else
									{
										$bgcolor="#66FF99"; 
									}
								}
								else
								{
									if($p_data_arr[12]==2)
									{
										
										$po_number_update=$subconPoNumberArr[$p_data_arr[0]];  
									}
									else
									{
										$po_number_update=$poNumberArr[$p_data_arr[0]];
									}
								}
							}
							?>
							<tr bgcolor='<?php echo $bgcolor; ?>'  id=tr_<?php echo $i; ?>>
								<td width="25"><?php echo $i; ?>
									<input type="hidden" id="locationId<?php echo $i; ?>" name="locationId[]" value="<?php echo $val[csf('location_id')]; ?>"  />
									<input type="hidden" id="floorId<?php echo $i; ?>" name="floorId[]" value="<?php echo $val[csf('floor_id')]; ?>"  />
									<input type="hidden" id="resourceId<?php echo $i; ?>" name="resourceId[]" value="<?php echo $val[csf('resource_id')]; ?>"  />
									<input type="hidden" id="buyerIds<?php echo $i; ?>" name="buyerIds[]" value="<?php echo $val[csf('buyer_ids')]; ?>"  />
									<input type="hidden" id="poIds<?php echo $i; ?>" name="poIds[]" value="<?php echo $val[csf('order_ids')]; ?>"  />
									<input type="hidden" id="poNumbers<?php echo $i; ?>" name="poNumbers[]" value="<?php echo $val[csf('ponumbers')]; ?>"  />
									<input type="hidden" id="gmtItemIds<?php echo $i; ?>" name="gmtItemIds[]" value="<?php echo $val[csf('gmtitem_ids')]; ?>"  />
									<input type="hidden" id="poDetails<?php echo $i; ?>" name="poDetails[]" value="<?php echo $val[csf('linepo_details')]; ?>"  />
									<input type="hidden" id="poWiseAvailableMinutes<?php echo $i; ?>" name="poWiseAvailableMinutes[]" value="<?php echo $val[csf('linepo_available_minutes')]; ?>"  />
								</td>
								<td width="80"><?php echo $val[csf('location_name')]; ?></td>
								<td width="80"> <?php echo $val[csf('floor_name')]; ?></td>
								<td align="center" width="50" ><?php echo $val[csf('line_name')]; ?> </td>
								<td width="80"><p><?php echo $val[csf('buyer_names')]; ?></p></td>
								<td width="140">
									<p><?php echo $po_number_update; ?></p>
								</td>
								<td width="60"><p><?php echo $val[csf('file_nos')]; ?></p></td>
								<td width="60"><p><?php echo $val[csf('reference_nos')]; ?></p></td>
								<td width="120"><p><?php echo $val[csf('garments_item_names')]; ?><p/> </td>
									<td align="right" width="40"><p><?php echo $val[csf('item_smvs')]; ?></p></td>
									<td align="right" width="70">
										<?php
										if(count(explode(",",$po_number_update))>1)
										{
											?>
											<a href="#" onClick="openmypage('<?php echo $val[csf('linepo_details')]; ?>','<?php echo $val[csf('efficency_min')]; ?>','<?php echo $i; ?>','<?php echo $val[csf('line_name')]; ?>')">
												<p><?php echo $val[csf('efficency_min')]; ?></p>
											</a>
											<?php
										}
										else 
										{
											?>
											<p><?php echo $val[csf('efficency_min')]; ?></p>
											<?php
										}
										?>
									</td>
									<td width="70" align="right"><?php echo $val[csf('produced_min')]; ?></td>
									<td align="right" width="60"><?php echo ($val[csf('horking_hour')]*$val[csf('hourly_target')]); ?></td>
									<td width="60" align="right"><?php echo $val[csf('total_produced')]; ?></td>
									<td align="right" width="60"><?php echo ($val[csf('variance_pceces')]); ?></td>
									<td align="right" width="50"><?php echo $val[csf('operators')]; ?></td>
									<td align="right" width="40"><?php echo $val[csf('helpers')]; ?></td>
									<td align="right" width="40"><?php echo $val[csf('man_powers')]; ?></td>
									<td align="right" width="50"><?php echo $val[csf('hourly_target')]; ?></td>
									<td align="right" width="50"><?php echo $val[csf('capacity')]; ?></td>
									<td align="right" width=""><?php echo $val[csf('horking_hour')]; ?></td>
									<td align="right" width="">
										<input type="button" class="formbutton"  value="Remarks" style="width:90px" onClick="open_mypage_remarks(<?php echo $i; ?>)" /> 
										<input name="txt_remarks[]"    value="<?php echo $val[csf('remarks')]; ?>" type="hidden" id="txt_remarks_<?php echo $i; ?>">
									</td>
								</tr>
								<?php
								$total_operator+=$val[csf('operators')];
								$total_helper+=$val[csf('helpers')];
								$total_man_power+=$val[csf('man_powers')];
								$total_capacity+=$val[csf('capacity')];
								$total_working_hour+=$val[csf('horking_hour')];
								$total_terget+=($val[csf('horking_hour')]*$val[csf('hourly_target')]);
								$grand_total_product+=$val[csf('total_produced')];
								$variance_pecess+=$val[csf('variance_pceces')];
								$gnd_avable_min+=$val[csf('efficency_min')];
								$gnd_product_min+=$val[csf('produced_min')];
								$i++;
						}
					}
					else
					{
							$save_status=1;
							$prod_resource_array=array();
							$prod_resource_po_details=array();

							$dataArray_sql=sql_select( " select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper,b.smv_adjust,b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour,c.from_date,c.to_date,c.capacity, d.po_id, d.gmts_item_id ,d.target_per_line from prod_resource_mst a ,prod_resource_dtls b,prod_resource_dtls_mast c  left join prod_resource_color_size d on c.id=d.dtls_id and d.po_id>0 where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$comapny_id  and b.pr_date=$txt_date and b.is_deleted=0 and c.is_deleted=0 $location_id order by a.location_id,a.floor_id");

							$lastSlNo=1000;
							foreach($dataArray_sql as $val)
							{
								
		
							
								if($lineSerialArr[$val[csf('line_number')]]=="")
								{
									$lastSlNo++;
									$slNo=$lastSlNo;
									$lineSerialArr[$val[csf('line_number')]]=$slNo;
								}
								else $slNo=$lineSerialArr[$val[csf('line_number')]];
								
								//echo $slNo."**<br/>";
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['man_power']=$val[csf('man_power')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['operator']=$val[csf('operator')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['helper']=$val[csf('helper')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['terget_hour']=$val[csf('target_per_hour')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['working_hour']=$val[csf('working_hour')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['day_start']=$val[csf('from_date')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['day_end']=$val[csf('to_date')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['capacity']=$val[csf('capacity')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['smv_adjust']=$val[csf('smv_adjust')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
								$line_number_arr=explode(",",$val[csf('line_number')]);
								$line_name_arr=array();
								foreach($line_number_arr as $s_line)
								{
									$line_name_arr[]=$lineDataArr[$s_line];
								}
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['line_name']=implode(",",$line_name_arr);

								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['pr_date']=$val[csf('pr_date')];
								$prod_resource_array[$val[csf('location_id')]][$val[csf('floor_id')]][$slNo][$val[csf('id')]]['good_qnty']=0;


								$prod_resource_po_details[$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]][$val[csf('po_id')]]['gmts_item_id']=$val[csf('gmts_item_id')];
								$prod_resource_po_details[$val[csf('location_id')]][$val[csf('floor_id')]][$val[csf('id')]][$val[csf('po_id')]]['target_per_line']+=$val[csf('target_per_line')];
								$all_resource_po[$val[csf('po_id')]]=$val[csf('po_id')];

							}

							$sql_po_data=sql_select("select b.id,a.buyer_name as buyer_id,a.job_no,b.po_number,c.short_name ,  b.grouping , b.file_no from wo_po_details_master a,wo_po_break_down b,lib_buyer c where  b.job_no_mst=a.job_no and a.buyer_name=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");


							foreach($sql_po_data as $val)
							{
								$job_po_data[$val[csf('id')]]['buyer_name']=$val[csf('short_name')];
								$job_po_data[$val[csf('id')]]['buyer_id']=$val[csf('buyer_id')];
								$job_po_data[$val[csf('id')]]['po_number']=$val[csf('po_number')];

								$job_po_data[$val[csf('id')]]['grouping']=$val[csf('grouping')];
								$job_po_data[$val[csf('id')]]['file_no']=$val[csf('file_no')];
							}

							//print_r($job_po_data[28479]);die;
			 				//********************************************************************************************************
							if($db_type==0)
							{
								$manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
							}
							else
							{
								$manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
							}

							$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
							if($smv_source=="") $smv_source=1; else $smv_source=$smv_source;

							if($smv_source==3)
							{
								$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 
								and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
								$resultItem=sql_select($sql_item);
								foreach($resultItem as $itemData)
								{
									$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
								}
							}
							else
							{
								$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
								$resultItem=sql_select($sql_item);
								foreach($resultItem as $itemData)
								{
									if($smv_source==1)
									{
										$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
									}
									if($smv_source==2)
									{
										$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
									}
								}
							}

							if($db_type==2)
							{
								$pr_date=str_replace("'","",$txt_date);
								$pr_date_old=explode("-",str_replace("'","",$txt_date));
								$month=strtoupper($pr_date_old[1]);
								$year=substr($pr_date_old[2],2);
								$pr_date=$pr_date_old[0]."-".$month."-".$year;
							}
							if($db_type==0)
							{
								$pr_date=str_replace("'","",$txt_date);
							}

							$i=1; $grand_total_good=0; $grand_alter_good=0; $grand_total_reject=0;
							$html="";
							$floor_html="";
							$check_arr=array();


							$sql="select  a.company_id, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name  as buyer_name, a.po_break_down_id, a.item_number_id,c.po_number as po_number,c.file_no,c.grouping as ref,sum(a.production_quantity) as good_qnty ,a.production_hour from  wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a where a.production_type=5 and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and a.prod_reso_allo=1 $company_name  $txt_date_from $location_id3 group by a.company_id, a.location, a.floor_id,a.po_break_down_id,a.prod_reso_allo, a.production_date, a.sewing_line,b.buyer_name,a.item_number_id,c.po_number,c.file_no,c.grouping,a.production_hour order by a.location,a.floor_id,a.sewing_line";

							//echo $sql;die;
							$sql_resqlt=sql_select($sql);
							$production_data_arr=array();
							$production_po_data_arr=array();
							$production_serial_arr=array();
							foreach($sql_resqlt as $val)
							{
								$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['qty']+=$val[csf('good_qnty')];	
								$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['production_hour']+=1;	
								$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['buyer_name']=$val[csf('buyer_name')];
								$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['ref']=$val[csf('ref')];
								$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['po_number']=$val[csf('po_number')];
								$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['file_no']=$val[csf('file_no')];
								$production_po_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]['item_number_id']=$val[csf('item_number_id')];					     
								$production_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]]['quantity']+=$val[csf('good_qnty')];
							}
					
					
							$sql_sub_con_order= sql_select(" select c.id,b.party_id ,c.order_no as po_number,c.smv from subcon_ord_mst b,subcon_ord_dtls c
							where  c.job_no_mst=b.subcon_job and c.status_active=1 and c.is_deleted=0");
							 $subcon_order_arr=array();
							foreach($sql_sub_con_order as $order_data)
							{
								$subcon_order_arr[$order_data[csf("id")]]["party_id"]	=$order_data[csf("party_id")];
								$subcon_order_arr[$order_data[csf("id")]]["po_number"]	=$order_data[csf("po_number")];
								$subcon_order_arr[$order_data[csf("id")]]["smv"]		=$order_data[csf("smv")];
							}
							
							$sql_sub_contuct= sql_select("select a.company_id, a.location_id,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id as sewing_line,a.order_id,a.gmts_item_id, sum(a.production_qnty) as good_qnty from subcon_gmts_prod_dtls a where  a.production_type=2 and a.status_active=1 and a.is_deleted=0 and a.prod_reso_allo=1   $company_name $txt_date_from   $location_id group by a.company_id, a.location_id,a.prod_reso_allo, a.floor_id, a.production_date, a.line_id,a.gmts_item_id,a.order_id ");
						
							foreach($sql_sub_contuct as $s_val)
							{
								$subcontact_po_data_arr[$s_val[csf('floor_id')]][$s_val[csf('sewing_line')]][$s_val[csf('order_id')]]['qty']+=$s_val[csf('good_qnty')];	
								$subcontact_po_data_arr[$s_val[csf('floor_id')]][$s_val[csf('sewing_line')]][$s_val[csf('order_id')]]['buyer_name']=$subcon_order_arr[$s_val[csf('order_id')]]["party_id"];
								//buyerArr
								$subcontact_po_data_arr[$s_val[csf('floor_id')]][$s_val[csf('sewing_line')]][$s_val[csf('order_id')]]['po_number']=$subcon_order_arr[$s_val[csf('order_id')]]["po_number"];
								$subcontact_po_data_arr[$s_val[csf('floor_id')]][$s_val[csf('sewing_line')]][$s_val[csf('order_id')]]['item_number_id']=$s_val[csf('gmts_item_id')];					     							$subcontact_po_data_arr[$s_val[csf('floor_id')]][$s_val[csf('sewing_line')]][$s_val[csf('order_id')]]['smv']=$subcon_order_arr[$s_val[csf('order_id')]]["smv"];	
								$production_data_arr[$s_val[csf('floor_id')]][$s_val[csf('sewing_line')]]['quantity']+=$s_val[csf('good_qnty')];
							}

							foreach($prod_resource_array as $l_id=>$fname)
							{
								foreach($fname as $f_id=>$ldata)
								{
									ksort($ldata);
									foreach($ldata as $sl_id=>$sl_data)
									{
										foreach($sl_data as $resource_id=>$resource_data)
										{
											if(($subcon_line_array[$resource_id]*1)<1)
											{
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$buyer_name="";
												$garment_itemname='';
												$item_smv="";
												$smv_for_item="";
												$produce_minit="";
												$order_no_total="";
												$efficiency_min=0;$efficiency_min_summary=0;
												$ref_no='';
												$file_no='';
												$po_id_all=array();
												$po_number_arr=array();
												$po_buyer_arr=array();
												$po_ref_arr=array();
												$po_file_arr=array();
												$item_number_arr=array();
												$item_number_id_arr=array();
												$item_smv_arr=array();
												$po_wise_produced_min=array();
												$display_po_arr=array();
												$po_wise_available_min='';
												$po_information='';
												$flag=0;
	
												$line_production=0;
												$line_production+=$production_data_arr[$f_id][$resource_id]['quantity'];
												//******************************* line effiecency*************************************************['']
												$total_adjustment=0;$total_adjustment_summary=0;
												$smv_adjustmet_type=$resource_data['smv_adjust_type'];
	
												if(str_replace("'","",$smv_adjustmet_type)==1)
												{ 
													$total_adjustment=$resource_data['smv_adjust'];
												}
	
												if(str_replace("'","",$smv_adjustmet_type)==2)
												{
													$total_adjustment=($resource_data['smv_adjust'])*(-1);
												}
	
												$efficiency_min+=$total_adjustment+($resource_data['man_power'])*$resource_data['working_hour']*60;
												$efficiency_min_summary+=$efficiency_min;
	
												$eff_target=$resource_data['working_hour']*$resource_data['terget_hour'];
												
												
												foreach($production_po_data_arr[$f_id][$resource_id] as $po_id=>$po_data)
												{
														//$testasas=$check[$po_id][$po_data['item_number_id']];
														//if( $check[$po_id][$po_data['item_number_id']]=='' )
													//{
														if(count($production_po_data_arr[$f_id][$resource_id])>1)
														{
															$po_target=$prod_resource_po_details[$l_id][$f_id][$resource_id][$po_id]['target_per_line'];
														}
														else $po_target=$resource_data['terget_hour'];
														$po_buyer_arr[$po_data['buyer_name']]=$buyerArr[$po_data['buyer_name']];
														if(!empty($po_data['ref']))
														{
															$po_ref_arr[$po_data['ref']]=$po_data['ref'];
														}
	
														if(!empty($po_data['file_no']))
														{
															$po_file_arr[$po_data['file_no']]=$po_data['file_no'];
														}
	
														$po_number_arr[$po_id]=$po_data['po_number'];
														$item_number_arr[$po_id]=$garments_item[$po_data['item_number_id']];
														$item_number_id_arr[$po_id]=$po_data['item_number_id'];
														$item_smv_arr[$po_data['item_number_id']]=$item_smv_array[$po_id][$po_data['item_number_id']];
														$produce_minit+=$po_data['qty']*$item_smv_array[$po_id][$po_data['item_number_id']];
	
														$po_wise_produced_min[$po_id]=$po_data['qty']*$item_smv_array[$po_id][$po_data['item_number_id']];
	
														if($po_information!='')
														{
															$po_information =$po_information.",".$po_id."**".$po_data['buyer_name']."*".$buyerArr[$po_data['buyer_name']]."*".$po_data['item_number_id']."*".$po_data['ref']."*".$po_data['file_no']."*".$item_smv_array[$po_id][$po_data['item_number_id']]."*".($po_data['qty']*$item_smv_array[$po_id][$po_data['item_number_id']])."*".$po_data['qty']."*".$po_data['production_hour']."*".$po_target."*1";
															$bgcolor="#66FF99";
														}
														else
														{
															$po_information .=$po_id."**".$po_data['buyer_name']."*".$buyerArr[$po_data['buyer_name']]."*".$po_data['item_number_id']."*".$po_data['ref']."*".$po_data['file_no']."*".$item_smv_array[$po_id][$po_data['item_number_id']]."*".($po_data['qty']*$item_smv_array[$po_id][$po_data['item_number_id']])."*".$po_data['qty']."*".$po_data['production_hour']."*".$po_target."*1";
														}
														$check[$po_id][$po_data['item_number_id']]=$po_target;
														$display_po_arr[]=$po_id;
	
													//}
												}
												//print_r($prod_resource_po_details);die;
												//************************************************************************************************************
												foreach($prod_resource_po_details[$l_id][$f_id][$resource_id] as $po_id=>$po_data_arr)
												{
													
													if(!in_array($po_id,$display_po_arr) && $po_id!="")
													{
														//echo "100000000000000000000000";die;
														if(count($prod_resource_po_details[$l_id][$f_id][$resource_id])>1)
														{
															$po_target=$po_data_arr['target_per_line'];
														}
														else $po_target=$resource_data['terget_hour'];
		
		
														if($job_po_data[$po_id]['po_number']!="")
														{
															$po_number_arr[$po_id]=$job_po_data[$po_id]['po_number'];
														}
														$po_buyer_arr[$job_po_data[$po_id]['buyer_id']]=$job_po_data[$po_id]['buyer_name'];
														if(!empty($job_po_data[$po_id]['grouping']))
														{
															$po_ref_arr[$job_po_data[$po_id]['grouping']]=$job_po_data[$po_id]['grouping'];
														}
		
														if(!empty($job_po_data[$po_id]['file_no']))
														{
															$po_file_arr[$job_po_data[$po_id]['file_no']]=$job_po_data[$po_id]['file_no'];
														}
		
														$item_number_arr[$po_id]=$garments_item[$po_data_arr['gmts_item_id']];
														$item_number_id_arr[$po_id]=$po_data_arr['gmts_item_id'];
														$item_smv_arr[$po_id]=$item_smv_array[$po_id][$po_data_arr['gmts_item_id']];
														if($po_information!='')
														{
															$po_information=$po_information.",".$po_id."**".$job_po_data[$po_id]['buyer_id']."*".$job_po_data[$po_id]['buyer_name']."*".$po_data_arr['gmts_item_id']."*".$job_po_data[$po_id]['grouping']."*".$job_po_data[$po_id]['file_no']."*".$item_smv_array[$po_id][$po_data_arr['gmts_item_id']]."****".$po_target."*1";
															$bgcolor="#FF0000";
															$flag=1;
														//	echo "TEST";
															//$po_wise_available_min='';
														}
														else
														{
															if($po_id!='')
															{
																$po_information=$po_id."**".$job_po_data[$po_id]['buyer_id']."*".$job_po_data[$po_id]['buyer_name']."*".$po_data_arr['gmts_item_id']."*".$job_po_data[$po_id]['grouping']."*".$job_po_data[$po_id]['file_no']."*".$item_smv_array[$po_id][$po_data_arr['gmts_item_id']]."****".$po_target."*1";
																$po_wise_available_min=$po_id."*".$efficiency_min;
															}
														}
													}
												}
												
												foreach($subcontact_po_data_arr[$f_id][$resource_id] as $po_id=>$po_data)
												{
														//$testasas=$check[$po_id][$po_data['item_number_id']];
														//if( $check[$po_id][$po_data['item_number_id']]=='' )
													//{
														$po_target=0;
														$po_buyer_arr[$po_data['buyer_name']]=$buyerArr[$po_data['buyer_name']];
														
	
														$po_number_arr[$po_id]=$po_data['po_number'];
														$item_number_arr[$po_id]=$garments_item[$po_data['item_number_id']];
														$item_number_id_arr[$po_id]=$po_data['item_number_id'];
														$item_smv_arr[$po_data['item_number_id']]=$po_data['smv'];
														$produce_minit+=$po_data['qty']*$po_data['smv'];
	
														$po_wise_produced_min[$po_id]=$po_data['qty']*$po_data['smv'];
	
														if($po_information!='')
														{
															$po_information =$po_information.",".$po_id."**".$po_data['buyer_name']."*".$buyerArr[$po_data['buyer_name']]."*".$po_data['item_number_id']."***".$po_data['smv']."*".($po_data['qty']*$po_data['smv'])."*".$po_data['qty']."*".$po_data['production_hour']."*".$po_target."*2";
															$bgcolor="#66FF99";
														}
														else
														{
															$po_information .=$po_id."**".$po_data['buyer_name']."*".$buyerArr[$po_data['buyer_name']]."*".$po_data['item_number_id']."***".$po_data['smv']."*".($po_data['qty']*$po_data['smv'])."*".$po_data['qty']."*".$po_data['production_hour']."*".$po_target."*2";
														}
														$check[$po_id][$po_data['item_number_id']]=$po_target;
														$display_po_arr[]=$po_id;
	
													//}
												}
												
												
												foreach($po_wise_produced_min as $p_id=>$p_val)
												{
													$po_available_min=number_format((($efficiency_min/$produce_minit)*$p_val),0,".","");
													if($po_wise_available_min!='') $po_wise_available_min=$po_wise_available_min.",".$p_id."*".$po_available_min;
													else $po_wise_available_min=$p_id."*".$po_available_min;
												}
												

												$po_numbers=implode(",",$po_number_arr);
												$buyer_name=implode(",",$po_buyer_arr);
												$ref_no=implode(",",$po_ref_arr);
												$file_no=implode(",",$po_file_arr);
												$garment_itemname=implode(",",$item_number_arr);
												$item_smv=implode("/",$item_smv_arr);
	
												$floor_name=$floorArr[$f_id];	
												$floor_smv+=$item_smv;
	
												$total_capacity+=$resource_data['capacity'];
												$gnd_total_tgt_h+=$resource_data['terget_hour'];	
												$total_working_hour+=$resource_data['working_hour']; 
												$total_operator+=$resource_data['operator'];
												$total_helper+=$resource_data['helper'];
												$total_man_power+=$resource_data['man_power'];
												$total_terget+=$eff_target;
												$grand_total_product+=$line_production;
												$gnd_avable_min+=$efficiency_min;
												$gnd_avable_min_summary+=$efficiency_min_summary;;
												$gnd_product_min+=$produce_minit; 
												if(count($po_number_arr)==0)
												{
													$bgcolor="#FF0000";
													$po_wise_available_min='';
												}
	
												?>
												<tr bgcolor='<?php echo $bgcolor; ?>'  id=tr_<?php echo $i; ?>>
													<td width="25" align="right"><?php echo $i; ?>
														<input type="hidden" id="locationId<?php echo $i; ?>" name="locationId[]" value="<?php echo $l_id; ?>"  />
														<input type="hidden" id="floorId<?php echo $i; ?>" name="floorId[]" value="<?php echo $f_id; ?>"  />
														<input type="hidden" id="resourceId<?php echo $i; ?>" name="resourceId[]" value="<?php echo $resource_id; ?>"  />
														<input type="hidden" id="buyerIds<?php echo $i; ?>" name="buyerIds[]" value="<?php echo implode(",", array_keys($po_buyer_arr)); ?>"  />
														<input type="hidden" id="poIds<?php echo $i; ?>" name="poIds[]" value="<?php echo implode(",", array_keys($po_number_arr)); ?>"  />
														<input type="hidden" id="poNumbers<?php echo $i; ?>" name="poNumbers[]" value="<?php echo $po_numbers; ?>"  />
														<input type="hidden" id="gmtItemIds<?php echo $i; ?>" name="gmtItemIds[]" value="<?php echo implode(",",$item_number_id_arr); ?>"  />
														<input type="hidden" id="poDetails<?php echo $i; ?>" name="poDetails[]" value="<?php echo $po_information; ?>"  />
														<input type="hidden" id="poWiseAvailableMinutes<?php echo $i; ?>" name="poWiseAvailableMinutes[]" value="<?php echo $po_wise_available_min; ?>"  />
													</td>
													<td width="80"><?php echo $locationArr[$l_id]; ?></td>
													<td width="80"> <?php echo $floor_name; ?></td>
													<td align="center" width="50" ><?php echo $resource_data['line_name']; ?> </td>
													<td width="80"><p><?php echo $buyer_name; ?></p></td>
													<td width="140">
														<p><?php echo $po_numbers; ?></p>
													</td>
													<td width="60"><p><?php echo $file_no; ?></p></td>
													<td width="60"><p><?php echo $ref_no; ?></p></td>
													<td width="120"><p><?php echo $garment_itemname; ?><p/> </td>
														<td align="right" width="40"><p><?php echo $item_smv; ?></p></td>
														<td align="right" width="70">
															<?php 
															if(count(explode(",",$po_numbers))>1)
															{
																?>
																<a href="#" onClick="openmypage('<?php echo $po_information; ?>','<?php echo $efficiency_min; ?>','<?php echo $i; ?>','<?php echo $resource_data['line_name']; ?>')">
																	<p><?php echo $efficiency_min; ?></p>
																</a>
																<?php
															}
															else 
															{
																?>
																<p><?php echo $efficiency_min; ?></p>
	
																<?php
															}
															?>
														</td>
														<td width="70" align="right"><?php echo $produce_minit; ?></td>
														<td align="right" width="60"><?php echo ($resource_data['working_hour']*$resource_data['terget_hour']); ?></td>
														<td width="60" align="right"><?php echo $line_production; ?></td>
														<td align="right" width="60"><?php echo ($line_production-$eff_target); ?></td>
	
														<td align="right" width="50"><?php echo $resource_data['operator']; ?></td>
														<td align="right" width="40"><?php echo $resource_data['helper']; ?></td>
														<td align="right" width="40"><?php echo $resource_data['man_power']; ?></td>
														<td align="right" width="50"><?php echo $resource_data['terget_hour']; ?></td>
														<td align="right" width="50"><?php echo $resource_data['capacity']; ?></td>
														<td align="right" width="60"><?php echo $resource_data['working_hour']; ?></td>
														<td align="right" width=""><input type="button" class="formbutton"  value="Remarks" style="width:90px" onClick="open_mypage_remarks(<?php echo $i; ?>)" /> 
														<input name="txt_remarks[]" type="hidden" id="txt_remarks_<?php echo $i; ?>">

														</td>
	
													</tr>
													<?php
													$i++;
	
												}
											}
											
										}
									}
								}
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th width="25"></th>
								<th width="80"> </th>
								<th width="80"> </th>
								<th width="50"> </th>
								<th width="80"></th>
								<th width="140"></th>
								<th width="60"></th>
								<th width="60"></th>
								<th width="120">Total</th>
								<th align="right" width="40"><? // echo number_format($total_smv/($i-1),2); ?>&nbsp;</th>
								<th align="right" width="70"><? echo $gnd_avable_min; ?>&nbsp;</th>
								<th align="right" width="70"><? echo $gnd_product_min; ?>&nbsp;</th>
								<th align="right" width="60"><? echo $total_terget; ?>&nbsp;</th>
								<th align="right" width="60"><? echo $grand_total_product; ?>&nbsp;</th>
								<th align="right" width="60"><? echo $variance_pecess; ?>&nbsp;</th>
								<th align="right" width="50"><? echo $total_operator; ?>&nbsp;</th>
								<th align="right" width="40"><? echo $total_helper; ?>&nbsp;</th>
								<th align="right" width="40"><? echo $total_man_power; ?>&nbsp;</th>
								<th align="right" width="50"><? // echo $gnd_total_tgt_h; ?>&nbsp;</th>
								<th align="right" width="50"><? echo $total_capacity; ?></th>
								<th align="right" width="60"><? echo $total_working_hour; ?></th>
								<th align="right" width=""></th>
							</tr>
						</tfoot>
					</table>

				</div>
				<div style="width:1300px">
					<table class="" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="tab_save_button">
						<tr>
							<td align="center" colspan="9" valign="middle" class="">
								<?
								if($save_status==2)
								{
									echo load_submit_buttons( $permission, "fnc_order_wise_available_minutes", 1, 0,"reset_form('OrderWiseAvailableMinutes_1','report_container2','','','')",1); 
								}
								else 
								{
									echo load_submit_buttons( $permission, "fnc_order_wise_available_minutes", 0, 0,"reset_form('OrderWiseAvailableMinutes_1','report_container2','','','')",1); 
								}
								?>
								<div id="report_container" align="center"></div>
								<input type="hidden" name="txt_mst_id" id="txt_mst_id" readonly />
							</td>
						</tr>	
					</table>
				</div>
			</fieldset>

			<?    
			exit();      
		}

//First Button end
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$po_id_arr=explode(",",$all_po_ids);
	$all_po_ids='';
	foreach($po_id_arr as $po_id)
	{
		if($po_id!='') $all_po_ids.=$po_id.",";
	}

	$job_details_data_arr=array(); $job_po_arr=array();

	$sql_result=sql_select("select a.id,a.job_no_mst,c.buyer_name,b.style_ref_no   from wo_po_break_down a,wo_po_details_master b, lib_buyer c
		where a.job_no_mst=b.job_no and b.buyer_name=c.id and a.id in (".chop($all_po_ids,",").") ");
	foreach($sql_result as $pr_val)
	{
	//$job_details_data_arr[$pr_val[csf('job_no_mst')]]['job_no_mst']=$pr_val[csf('job_no_mst')];
		$job_details_data_arr[$pr_val[csf('job_no_mst')]]['buyer_name']=$pr_val[csf('buyer_name')];
		$job_details_data_arr[$pr_val[csf('job_no_mst')]]['style_ref_no']=$pr_val[csf('style_ref_no')];
		$job_po_arr[1][$pr_val[csf('id')]]=$pr_val[csf('job_no_mst')];
	//$job_details_data_arr[$pr_val[csf('id')]]=$pr_val[csf('job_no_mst')];
	}
	$buyerArr = return_library_array("select id,short_name from lib_buyer","id","short_name"); 
	$sql_sub_con_order= sql_select(" select c.id,b.party_id ,c.order_no as po_number,c.cust_style_ref,b.subcon_job from subcon_ord_mst b,subcon_ord_dtls c
					where  c.job_no_mst=b.subcon_job and c.status_active=1 and c.is_deleted=0 and c.id in (".chop($all_po_ids,",").") ");
	
	 $subcon_order_arr=array();
	foreach($sql_sub_con_order as $order_data)
	{
		$subcon_order_arr[$order_data[csf("subcon_job")]]["buyer_name"]	=$buyerArr[$order_data[csf("party_id")]];
		$subcon_order_arr[$order_data[csf("subcon_job")]]["po_number"]	=$order_data[csf("po_number")];
		$subcon_order_arr[$order_data[csf("subcon_job")]]["style_ref_no"]=$order_data[csf("cust_style_ref")];
		$job_po_arr[2][$order_data[csf('id')]]=$order_data[csf('subcon_job')];
	}


	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		
		
		$field_array_mst="id, company_id, production_date, location_id, floor_id, resource_id, buyer_ids, order_ids, gmtitem_ids, ponumbers,  linepo_details, linepo_available_minutes, location_name, floor_name, line_name, buyer_names, file_nos, reference_nos, garments_item_names, item_smvs, operators, helpers, man_powers, hourly_target, capacity, horking_hour, total_target, total_produced, variance_pceces, efficency_min,  produced_min,remarks,inserted_by, insert_date";

		$resource_mst_id = return_next_id("id", "pro_resource_ava_min_mst", 1);
		$resource_dtls_id = return_next_id("id", "pro_resource_ava_min_dtls", 1);
		
		$field_array_dtls=" id, company_id, total_target, total_produced, efficency_min, produced_min, production_date, mst_id, location_id, floor_id, resource_id, buyer_id, order_ids,is_self_order, gmtitem_id, ponumber, location_name, floor_name, line_name, buyer_name,file_no, reference_no, garments_item_name, item_smv, operators, helpers, man_powers, hourly_target, capacity, horking_hour, inserted_by, insert_date";
		
		
		$data_arr_job=array();
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$locationId="locationId_".$j;
			$floorId="floorId_".$j;
			$resource_id="resourceId_".$j;
			$buyer_ids="buyerIds_".$j;
			$orderIds="orderIds_".$j;
			$gmatItem_ids="gmtItemIds_".$j;
			$po_details="poDetails_".$j;
			$poWise_available_min="poWiseAvailableMinutes_".$j;
			$locationName="locationName_".$j;
			$floorName="floorName_".$j;
			$lineName="lineName_".$j;
			$buyer_names="buyerNames_".$j;
			$po_numbers="poNumbers_".$j;
			$file_no="fileNos_".$j;
			$reference_no="referenceNos_".$j;
			$garmentitem="garmentItems_".$j;
			$smv="smv_".$j;
			$operator="operator_".$j;
			$helper="helper_".$j;
			$manpower="manPower_".$j;
			$hourlyTarget="hourlyTarget_".$j;
			$capacity="capacity_".$j;
			
			$working_hour="workingHour_".$j;
			$totalTarget="totalTarget_".$j;
			$produced_qty="totalProduced_".$j;
			$variance_peces="variancePeces_".$j;
			$available_minutes="availableMinutes_".$j;
			$producd_minutes="producdMinutes_".$j;
			$remarks="remarks_".$j;

			if($data_array_mst!="") $data_array_mst.=",";
			$data_array_mst.="(".$resource_mst_id.",".$cbo_company_id.",".$txt_date.",'".$$locationId."','".$$floorId."','".$$resource_id."','".$$buyer_ids."','".$$orderIds."','".$$gmatItem_ids."','".$$po_numbers."','".$$po_details."','".$$poWise_available_min."','".$$locationName."','".$$floorName."','".$$lineName."','".$$buyer_names."','".$$file_no."','".$$reference_no."','".$$garmentitem."','".$$smv."','".$$operator."','".$$helper."','".$$manpower."','".$$hourlyTarget."','".$$capacity."','".$$working_hour."','".$$totalTarget."','".$$produced_qty."','".$$variance_peces."','".$$available_minutes."','".$$producd_minutes."','".$$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$po_available_min_info=explode(",",$$poWise_available_min);
			$po_wise_line_min_arr=array();
			foreach($po_available_min_info as $po_avail_single)
			{
				$po_avail_single_info=explode("*",$po_avail_single);
				$po_wise_line_min_arr[$po_avail_single_info[0]]=$po_avail_single_info[1];
			}
			//echo "10**".$$po_details;die;
			$po_details_info=explode(",",$$po_details);
			
			foreach($po_details_info as $po_details_single)
			{
				$po_details_single_info=explode("*",$po_details_single);
				$single_po_available_min=number_format($po_wise_line_min_arr[$po_details_single_info[0]],0,".","");

				$data_arr_job[$po_details_single_info[12]][$job_po_arr[$po_details_single_info[12]][$po_details_single_info[0]]]['available_min']+=$single_po_available_min;
				$data_arr_job[$po_details_single_info[12]][$job_po_arr[$po_details_single_info[12]][$po_details_single_info[0]]]['produced_min']+=number_format($po_details_single_info[8],0,".","");
				$po_total_target=($po_details_single_info[11]*1)*$$working_hour;
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$resource_dtls_id.",".$cbo_company_id.",'".$po_total_target."','".$po_details_single_info[9]."','".$single_po_available_min."','".$po_details_single_info[8]."',".$txt_date.",'".$resource_mst_id."','".$$locationId."','".$$floorId."','".$$resource_id."','".$po_details_single_info[2]."','".$po_details_single_info[0]."','".$po_details_single_info[12]."','".$po_details_single_info[4]."','".$po_details_single_info[1]."','".$$locationName."','".$$floorName."','".$$lineName."','".$po_details_single_info[3]."','".$po_details_single_info[6]."','".$po_details_single_info[5]."','".$garments_item[$po_details_single_info[4]]."','".$po_details_single_info[7]."','".$$operator."','".$$helper."','".$$manpower."','".$po_details_single_info[11]."','".$$capacity."','".$$working_hour."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$resource_dtls_id = $resource_dtls_id+1;
			}
			
			$resource_mst_id = $resource_mst_id+1;
		}
		//echo "10**".$data_array_dtls;die;
		//print_r($data_arr_job);die;
		$production_id = return_next_id("id", "production_logicsoft", 1);
		$field_array=" id, production_date, buyer, style, jobno, available_min, produce_min,is_self_order";

		foreach($data_arr_job as $job_type=>$job_type_data)
		{
			foreach($job_type_data as $job_no=>$j_val)
			{
				if($data_array!="") $data_array.=",";
				if($job_type==1)
				{
					$data_array.="(".$production_id.",".$txt_date.",'".$job_details_data_arr[$job_no]['buyer_name']."','".$job_details_data_arr[$job_no]['style_ref_no']."','".$job_no."','".$j_val['available_min']."','".$j_val['produced_min']."','".$job_type."')";
				}
				else {
					$data_array.="(".$production_id.",".$txt_date.",'".$subcon_order_arr[$job_no]['buyer_name']."','".$subcon_order_arr[$job_no]['style_ref_no']."','".$job_no."','".$j_val['available_min']."','".$j_val['produced_min']."','".$job_type."')";
				}
				$production_id=$production_id+1;
			}
		}


		//echo "**".$data_array;die;

		//echo "10**insert into pro_resource_ava_min_mst ($field_array_mst)values".$data_array_mst;die;
		$rID=sql_insert("pro_resource_ava_min_mst",$field_array_mst,$data_array_mst,0);
		$rID2=sql_insert("pro_resource_ava_min_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID3=sql_insert("production_logicsoft",$field_array,$data_array,0);
			//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "0**";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$location_cond="";
		if(str_replace("'","",$cbo_location_id)!=0) $location_cond=" and location_id=$cbo_location_id";
		
		$field_array_mst="id, company_id, production_date, location_id, floor_id, resource_id, buyer_ids, order_ids, gmtitem_ids, ponumbers,  linepo_details, linepo_available_minutes, location_name, floor_name, line_name, buyer_names, file_nos, reference_nos, garments_item_names, item_smvs, operators, helpers, man_powers, hourly_target, capacity, horking_hour, total_target, total_produced, variance_pceces, efficency_min,  produced_min,remarks, inserted_by, insert_date";

		$resource_mst_id = return_next_id("id", "pro_resource_ava_min_mst", 1);
		$resource_dtls_id = return_next_id("id", "pro_resource_ava_min_dtls", 1);
		
		$field_array_dtls=" id, company_id,total_target, total_produced, efficency_min, produced_min, production_date, mst_id, location_id, floor_id, resource_id, buyer_id, order_ids,is_self_order, gmtitem_id, ponumber, location_name, floor_name, line_name, buyer_name,file_no, reference_no, garments_item_name, item_smv, operators, helpers, man_powers, hourly_target, capacity, horking_hour,  inserted_by, insert_date";

		$data_arr_job=array();
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$locationId="locationId_".$j;
			$floorId="floorId_".$j;
			$resource_id="resourceId_".$j;
			$buyer_ids="buyerIds_".$j;
			$orderIds="orderIds_".$j;
			$gmatItem_ids="gmtItemIds_".$j;
			$po_details="poDetails_".$j;
			$poWise_available_min="poWiseAvailableMinutes_".$j;
			$locationName="locationName_".$j;
			$floorName="floorName_".$j;
			$lineName="lineName_".$j;
			$buyer_names="buyerNames_".$j;
			$po_numbers="poNumbers_".$j;
			$file_no="fileNos_".$j;
			$reference_no="referenceNos_".$j;
			$garmentitem="garmentItems_".$j;
			$smv="smv_".$j;
			$operator="operator_".$j;
			$helper="helper_".$j;
			$manpower="manPower_".$j;
			$hourlyTarget="hourlyTarget_".$j;
			$capacity="capacity_".$j;
			
			$working_hour="workingHour_".$j;
			$totalTarget="totalTarget_".$j;
			$produced_qty="totalProduced_".$j;
			$variance_peces="variancePeces_".$j;
			$available_minutes="availableMinutes_".$j;
			$producd_minutes="producdMinutes_".$j;
			$remarks="remarks_".$j;

			if($data_array_mst!="") $data_array_mst.=",";
			$data_array_mst.="(".$resource_mst_id.",".$cbo_company_id.",".$txt_date.",'".$$locationId."','".$$floorId."','".$$resource_id."','".$$buyer_ids."','".$$orderIds."','".$$gmatItem_ids."','".$$po_numbers."','".$$po_details."','".$$poWise_available_min."','".$$locationName."','".$$floorName."','".$$lineName."','".$$buyer_names."','".$$file_no."','".$$reference_no."','".$$garmentitem."','".$$smv."','".$$operator."','".$$helper."','".$$manpower."','".$$hourlyTarget."','".$$capacity."','".$$working_hour."','".$$totalTarget."','".$$produced_qty."','".$$variance_peces."','".$$available_minutes."','".$$producd_minutes."','".$$remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$po_available_min_info=explode(",",$$poWise_available_min);
			$po_wise_line_min_arr=array();
			foreach($po_available_min_info as $po_avail_single)
			{
				$po_avail_single_info=explode("*",$po_avail_single);
				$po_wise_line_min_arr[$po_avail_single_info[0]]=$po_avail_single_info[1];
				
			}
			
			$po_details_info=explode(",",$$po_details);
			foreach($po_details_info as $po_details_single)
			{
				$po_details_single_info=explode("*",$po_details_single);
				$single_po_available_min=number_format($po_wise_line_min_arr[$po_details_single_info[0]],0,".","");
				
				$data_arr_job[$po_details_single_info[12]][$job_po_arr[$po_details_single_info[12]][$po_details_single_info[0]]]['available_min']+=$single_po_available_min;
				$data_arr_job[$po_details_single_info[12]][$job_po_arr[$po_details_single_info[12]][$po_details_single_info[0]]]['produced_min']+=number_format($po_details_single_info[8],0,".","");
				$po_total_target=($po_details_single_info[11]*1)*$$working_hour;
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$resource_dtls_id.",".$cbo_company_id.",'".$po_total_target."','".$po_details_single_info[9]."','".$single_po_available_min."','".number_format($po_details_single_info[8],0,".","")."',".$txt_date.",'".$resource_mst_id."','".$$locationId."','".$$floorId."','".$$resource_id."','".$po_details_single_info[2]."','".$po_details_single_info[0]."','".$po_details_single_info[12]."','".$po_details_single_info[4]."','".$po_details_single_info[1]."','".$$locationName."','".$$floorName."','".$$lineName."','".$po_details_single_info[3]."','".$po_details_single_info[6]."','".$po_details_single_info[5]."','".$garments_item[$po_details_single_info[4]]."','".$po_details_single_info[7]."','".$$operator."','".$$helper."','".$$manpower."','".$po_details_single_info[11]."','".$$capacity."','".$$working_hour."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$resource_dtls_id = $resource_dtls_id+1;
			}
			
			$resource_mst_id = $resource_mst_id+1;
		}


		$production_id = return_next_id("id", "production_logicsoft", 1);
		$field_array=" id, production_date, buyer, style, jobno, available_min, produce_min,is_self_order";
		$all_jobs='';
		foreach($data_arr_job as $job_type=>$job_type_data)
		{
			foreach($job_type_data as $job_no=>$j_val)
			{
				if($data_array!="") $data_array.=",";

				if($job_type==1)
				{
					$data_array.="(".$production_id.",".$txt_date.",'".$job_details_data_arr[$job_no]['buyer_name']."','".$job_details_data_arr[$job_no]['style_ref_no']."','".$job_no."','".$j_val['available_min']."','".$j_val['produced_min']."','".$job_type."')";
				}
				else {
					$data_array.="(".$production_id.",".$txt_date.",'".$subcon_order_arr[$job_no]['buyer_name']."','".$subcon_order_arr[$job_no]['style_ref_no']."','".$job_no."','".$j_val['available_min']."','".$j_val['produced_min']."','".$job_type."')";
				}
				
				$all_jobs.="'".$job_no."'";
				$all_jobs.=",";
				$production_id=$production_id+1;
			}
		}
		
		$all_jobs=chop($all_jobs,",");

		//$delete_master=execute_query("delete from pro_resource_ava_min_mst where company_id=".$cbo_company_id." and production_date=".$txt_date."");
		//$delete_dtls=execute_query("delete from pro_resource_ava_min_dtls where company_id=".$cbo_company_id." and production_date=".$txt_date."");
		$delete_master=execute_query("delete from pro_resource_ava_min_mst where company_id=".$cbo_company_id." and production_date=".$txt_date." $location_cond");
		$delete_dtls=execute_query("delete from pro_resource_ava_min_dtls where company_id=".$cbo_company_id." and production_date=".$txt_date." $location_cond");
		$delete_production=execute_query("delete from production_logicsoft where jobno in(".$all_jobs.") and production_date=".$txt_date."");
		$rID=sql_insert("pro_resource_ava_min_mst",$field_array_mst,$data_array_mst,0);
		$rID2=sql_insert("pro_resource_ava_min_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID3=sql_insert("production_logicsoft",$field_array,$data_array,0);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $delete_master && $delete_dtls && $delete_production)
			{
				mysql_query("COMMIT");  
				echo "1**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $delete_master && $delete_dtls)
			{
				oci_commit($con);  
				echo "1**";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$location_cond="";
		if(str_replace("'","",$cbo_location_id)!=0) $location_cond=" and location_id=$cbo_location_id";
		



		$data_arr_job=array();
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$locationId="locationId_".$j;
			$floorId="floorId_".$j;
			$resource_id="resourceId_".$j;
			$buyer_ids="buyerIds_".$j;
			$orderIds="orderIds_".$j;
			$gmatItem_ids="gmtItemIds_".$j;
			$po_details="poDetails_".$j;
			$poWise_available_min="poWiseAvailableMinutes_".$j;
			$locationName="locationName_".$j;
			$floorName="floorName_".$j;
			$lineName="lineName_".$j;
			$buyer_names="buyerNames_".$j;
			$po_numbers="poNumbers_".$j;
			$file_no="fileNos_".$j;
			$reference_no="referenceNos_".$j;
			$garmentitem="garmentItems_".$j;
			$smv="smv_".$j;
			$operator="operator_".$j;
			$helper="helper_".$j;
			$manpower="manPower_".$j;
			$hourlyTarget="hourlyTarget_".$j;
			$capacity="capacity_".$j;
			
			$working_hour="workingHour_".$j;
			$totalTarget="totalTarget_".$j;
			$produced_qty="totalProduced_".$j;
			$variance_peces="variancePeces_".$j;
			$available_minutes="availableMinutes_".$j;
			$producd_minutes="producdMinutes_".$j;

			if($data_array_mst!="") $data_array_mst.=",";
			$data_array_mst.="(".$resource_mst_id.",".$cbo_company_id.",".$txt_date.",'".$$locationId."','".$$floorId."','".$$resource_id."','".$$buyer_ids."','".$$orderIds."','".$$gmatItem_ids."','".$$po_numbers."','".$$po_details."','".$$poWise_available_min."','".$$locationName."','".$$floorName."','".$$lineName."','".$$buyer_names."','".$$file_no."','".$$reference_no."','".$$garmentitem."','".$$smv."','".$$operator."','".$$helper."','".$$manpower."','".$$hourlyTarget."','".$$capacity."','".$$working_hour."','".$$totalTarget."','".$$produced_qty."','".$$variance_peces."','".$$available_minutes."','".$$producd_minutes."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$po_available_min_info=explode(",",$$poWise_available_min);
			$po_wise_line_min_arr=array();
			foreach($po_available_min_info as $po_avail_single)
			{
				$po_avail_single_info=explode("*",$po_avail_single);
				$po_wise_line_min_arr[$po_avail_single_info[0]]=$po_avail_single_info[1];
			}
			
			$po_details_info=explode(",",$$po_details);
			foreach($po_details_info as $po_details_single)
			{
				$po_details_single_info=explode("*",$po_details_single);
				$single_po_available_min=number_format($po_wise_line_min_arr[$po_details_single_info[0]],0,".","");
				
				//$data_arr_job[$job_po_arr[$po_details_single_info[0]]]['available_min']+=$single_po_available_min;
				//$data_arr_job[$job_po_arr[$po_details_single_info[0]]]['produced_min']+=number_format($po_details_single_info[8],0,".","");

				$data_arr_job[$po_details_single_info[12]][$job_po_arr[$po_details_single_info[12]][$po_details_single_info[0]]]['available_min']+=$single_po_available_min;
				$data_arr_job[$po_details_single_info[12]][$job_po_arr[$po_details_single_info[12]][$po_details_single_info[0]]]['produced_min']+=number_format($po_details_single_info[8],0,".","");
				$po_total_target=($po_details_single_info[11]*1)*$$working_hour;
				
			
			}
			
		}

//echo "10**"; print_r($data_arr_job);die;
		$all_jobs='';
		foreach($data_arr_job as $job_type=>$job_type_data)
		{
			foreach($job_type_data as $job_no=>$j_val)
			{
				$all_jobs.="'".$job_no."'";
				$all_jobs.=",";
			}
		}
		$all_jobs=chop($all_jobs,",");
		//echo "10**delete from production_logicsoft where jobno in(".$all_jobs.") and production_date=".$txt_date."";die;
		//echo "10**delete from pro_resource_ava_min_mst where company_id=".$cbo_company_id." and production_date=".$txt_date." $location_cond";
		//$delete_master=execute_query("delete from pro_resource_ava_min_mst where company_id=".$cbo_company_id." and production_date=".$txt_date."");
		//$delete_dtls=execute_query("delete from pro_resource_ava_min_dtls where company_id=".$cbo_company_id." and production_date=".$txt_date."");
		$delete_master=execute_query("delete from pro_resource_ava_min_mst where company_id=".$cbo_company_id." and production_date=".$txt_date." $location_cond");
		$delete_dtls=execute_query("delete from pro_resource_ava_min_dtls where company_id=".$cbo_company_id." and production_date=".$txt_date." $location_cond");
		$delete_production=execute_query("delete from production_logicsoft where jobno in(".$all_jobs.") and production_date=".$txt_date."");
	
		//echo "10**".$delete_master."&&".$delete_dtls."&&".$delete_production."&&".$rID4."&&".$rID5."&&".$prodUpdate;die;
		if($db_type==0)
		{
			if($delete_master && $delete_dtls && $delete_production)
			{
				mysql_query("COMMIT");  
				echo "2**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($delete_master && $delete_dtls && $delete_production)
			{
				oci_commit($con);  
				echo "2**";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="remarks_popup")
{
	echo load_html_head_contents("Approval Cause Info", "../../", 1, 1,'','','');
	extract($_REQUEST);


	?>
    <script>
	
		$( document ).ready(function() {
			document.getElementById("appv_cause").value='<? echo $data; ?>';
		});
		
		var permission='<? echo $permission; ?>';
		
		function fnc_close()
		{	
			appv_cause= $("#appv_cause").val();
			
			document.getElementById('hidden_appv_cause').value=appv_cause;
			
			parent.emailwindow.hide();
		}
		
		
			
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form name="size_1" id="size_1">
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="appv_cause" id="appv_cause" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                       
                    </td>
                </tr>
            </table> 
              
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                
                <tr>
                    <td align="center" class="button_container">
                        <? 
						//print_r ($id_up_all);
                            /*if($id_up!='')
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 1,0,"reset_form('size_1','','','','','');",1);
                            }
                            else
                            {
                                echo load_submit_buttons($permission, "fnc_appv_instru_entry", 0,0,"reset_form('size_1','','','','','');",1);
                            }*/
                        ?>
                        <input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes"/>
                        
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                    </td>	  
                </tr>
            </table>
            </fieldset>
            </form>
        </div> 
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="distribute_available_minit")
{
	echo load_html_head_contents("FOB Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	$poNumberArr = return_library_array("select id,po_number from wo_po_break_down","id","po_number");
	$subconPoNumberArr = return_library_array("select id,order_no from subcon_ord_dtls","id","order_no");  
	?>
	
	<script>

		function calculate_available_minit(value)
		{
			var total_available_minit=0;
			var max_available_minit=$("#hidden_available_min").val();
			$("#table_available_minit").find('tbody tr').each(function()
			{
				
				var	avialable_minit=$(this).find('input[name="txt_available_min[]"]').val()*1;
				total_available_minit+=avialable_minit;
				if(total_available_minit>max_available_minit)
				{
					total_available_minit=total_available_minit-avialable_minit;
					$(this).find('input[name="txt_available_min[]"]').val('');
				}
			});
			
			
			$("#total_available_minit").text(total_available_minit);
		}
		function popup_close()
		{
			var max_available_minit=$("#hidden_available_min").val()*1;
			var total_available_minit=$("#total_available_minit").text()*1;
			var	po_info="";
			if(max_available_minit==total_available_minit)
			{
				
				$("#table_available_minit").find('tbody tr').each(function()
				{
					var	avialable_minit=$(this).find('input[name="txt_available_min[]"]').val()*1;
					var	po_id=$(this).find('input[name="txt_po_id[]"]').val()*1;
					if(po_info!='')
					{
						po_info=po_info+","+po_id+"*"+avialable_minit;
					}
					else
					{
						po_info=po_id+"*"+avialable_minit;
					}
				});
				
				$("#po_available_minutes").val(po_info);
				parent.emailwindow.hide();
			}
			else
			{
				alert("Total available minutes must be equal to line available minutes. ");
			}
		}
	</script>	
	<fieldset style="width:1020px; ">
		<input type="hidden" id="po_available_minutes" name="po_available_minutes" />
		<div id="report_container">
			<h3>Line Avaliable Minute: <?php echo $available_minit; ?> </h3>
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" align="center">

				<thead>
					<th width="30">SL</th>
					<th width="80">Buyer Name</th>
					<th width="120">Order No</th>
                    <th width="80">Order Type</th>
					<th width="80">File No</th>
					<th width="80">Ref. No</th>
					<th width="120">Garments Item</th>
					<th width="30">SMV</th>
					<th width="80">Prod. Qty.</th>
					<th width="100">Produced minutes</th>
					<th width="80">Used Minutes</th>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1000" cellpadding="0" cellspacing="0" align="center" id="table_available_minit">
				<tbody>
					<?
					$all_po_information=explode(",",$po_iinformation);
					$all_pre_po_available_min=explode(",",$pre_po_available_min);
					$pre_po_avai_arr=array();
					foreach($all_pre_po_available_min as $single_available_min)
					{
						$single_available_min_arr= explode("*",$single_available_min);
						$pre_po_avai_arr[$single_available_min_arr[0]]=$single_available_min_arr[1];
					}
						//print_r($pre_po_avai_arr);

					$k=1;	
					foreach($all_po_information as $single_po_information)
					{
						if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$single_po_info_arr= explode("*",$single_po_information);

						?>
						<tr style="font:'Arial Narrow';" align="center" bgcolor="<? echo $bgcolor;?>"  id="tr_<? echo $k; ?>">
							<td width="30"><? echo $k; ?></td>
							<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $single_po_info_arr[3]; ?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:left" ><?
							if($single_po_info_arr[12]==2) 	echo $subconPoNumberArr[$single_po_info_arr[0]];
							else 							echo $poNumberArr[$single_po_info_arr[0]];
							  ?>
                            </td>
                            <td width="80" style="word-wrap:break-word; word-break: break-all; text-align:center" >
							<? 
								if($single_po_info_arr[12]==2) 	echo "Subcon-Order";
								else 							echo "Self-Order";
							?>
                            </td>
							<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo $single_po_info_arr[5]; ?></td>
							<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $single_po_info_arr[6];?></td>
							<td width="120" style="word-wrap:break-word; word-break: break-all; text-align:center" ><? echo $garments_item[$single_po_info_arr[4]];?></td>
							<td width="30" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  $single_po_info_arr[7]; ?></td> 
							<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  $single_po_info_arr[9]; ?></td> 
							<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:right" ><? echo  $single_po_info_arr[8]; ?></td> 
							<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:right" >
								<input type="text" class="text_boxes_numeric" id="txt_available_min<? echo $k; ?>" name="txt_available_min[]" style="width:80px" onKeyUp="calculate_available_minit(this.value)" value="<?php echo $pre_po_avai_arr[$single_po_info_arr[0]];?>" />

								<input type="hidden" class="text_boxes_numeric" id="txt_po_id<? echo $k; ?>" name="txt_po_id[]" value="<?php echo $single_po_info_arr[0] ;?>" />
							</td> 
						</tr>
						<?
						$total_produced_qty+=$single_po_info_arr[9];
						$total_produced_min+=$single_po_info_arr[8];
						$total_available_min+=$pre_po_avai_arr[$single_po_info_arr[0]];
						$k++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom" >
						<td colspan="8"><input type="hidden" class="text_boxes" id="hidden_available_min" name="hidden_available_min" value="<?php echo $available_minit;?>" /> Total </td>
						<td align="right" id=""> <? echo $total_produced_qty;?></td>
						<td align="right" id=""> <? echo $total_produced_min;?></td>
						<td align="right" id="total_available_minit"> <? echo $total_available_min;?></td>
					</tr>
					<tr >
						<td colspan="10" align="center"><input type="button" class="formbutton"  value="Close" style="width:100px" onClick="popup_close()" /> </td>
					</tr>
				</tfoot>
			</table>
		</div>

		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>


	<?
	exit();
}


?>