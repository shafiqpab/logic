<?
ini_set('precision', 8);
ini_set("display_errors", 0);
require_once('../../includes/common.php');


	$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0 ", "id", "company_short_name",$con);
	$floor_library = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");

	$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	//$previous_date='30-Oct-2022';$current_date='30-Oct-2022';
	$from_date=$previous_date;
	$to_date=$previous_date;
	$companyStr=implode(',',array_keys($company_library));
	$current_date=$previous_date;
//------------------------------------------------------------------------------------------------------

$smv_source_arr = return_library_array("select company_name,smv_source from variable_settings_production where company_name in(".implode(',',array_keys($company_library)).") and variable_list=25 and status_active=1 and is_deleted=0","company_name","smv_source");

$prod_reso_allo_arr = return_library_array("select company_name,auto_update from variable_settings_production where variable_list=23 and is_deleted=0 and status_active=1","company_name","auto_update");

//Exfactory--------------------
$ex_factory_date_con = " and b.ex_factory_date between '".$previous_date."' and '".$previous_date."'";
	 
$ex_factory_sql="select a.delivery_company_id,a.delivery_floor_id,sum(b.ex_factory_qnty) ex_factory_qnty  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where b.delivery_mst_id=a.id and  a.delivery_company_id in(".implode(',',array_keys($company_library)).") $ex_factory_date_con and a.is_deleted=0 and a.status_active=1 
 AND b.status_active IN (1, 2, 3)  and b.entry_form!=85
group by a.delivery_company_id,a.delivery_floor_id";
 
 //echo $ex_factory_sql;die;

$ex_factory_sql_result = sql_select($ex_factory_sql, '', '', '', $con);
foreach($ex_factory_sql_result as $rows)
{
	//this is for urmi group.........................start;
	/*
	Note: 
		Floor Technical replace to Unit 1;
		Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
	*/
	if($rows[csf("delivery_floor_id")]==37){$rows[csf("delivery_floor_id")]=24;}
	else if($rows[csf("delivery_floor_id")]==7){$rows[csf("delivery_floor_id")]=19;}
	
	//if($rows[csf("delivery_company_id")]==4){$rows[csf("delivery_company_id")]=2;$rows[csf("delivery_floor_id")]=0;}
	//if($rows[csf("delivery_company_id")]==3 || $rows[csf("delivery_company_id")]==2){$rows[csf("delivery_floor_id")]=0;}
	
	
	if($rows[csf("delivery_company_id")]==2){$rows[csf("delivery_floor_id")]="cf_1";}
	else if($rows[csf("delivery_company_id")]==4){$rows[csf("delivery_floor_id")]="cf_2";}
	else if($rows[csf("delivery_company_id")]==3){$rows[csf("delivery_floor_id")]=0;}
	
	
	
	//this is for urmi group.........................end;
	
	$ex_fac_qty+=$rows[csf("ex_factory_qnty")];
	$ex_fac_qty_arr[$rows[csf("delivery_company_id")]][$rows[csf("delivery_floor_id")]]+=$rows[csf("ex_factory_qnty")];
}
unset($ex_factory_sql_result);

//Production ---------------------------                           
	
$production_date_con = " and a.production_date between '".$previous_date."' and '".$previous_date."'";
$sql_query="select  a.serving_company, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, a.po_break_down_id, e.item_number_id, c.po_number as po_number,c.unit_price,d.color_type_id,
	
	sum(case when a.production_type=11 and d.production_type=11 then d.production_qnty else 0 end) as good_qnty ,
	sum(case when a.production_type=5 and d.production_type=5 then d.production_qnty else 0 end) as sewing_output 		
	
	from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
	where a.production_type in(5,11) and d.production_type in(5,11) and a.id=d.mst_id and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and e.id=d.color_size_break_down_id and e.po_break_down_id=c.id and a.po_break_down_id=e.po_break_down_id AND a.serving_company in(".implode(',',array_keys($company_library)).") $production_date_con --and a.serving_company=4 and a.location=2
	group by a.serving_company, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, e.item_number_id,d.color_type_id, c.po_number,c.unit_price order by a.location, a.floor_id, a.po_break_down_id"; //
	
	

	
  //echo $sql_query;die;

	$production_sql_result=sql_select($sql_query);			 
	foreach($production_sql_result as $val)
	{
		if($val[csf('sewing_output')]){
			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('color_type_id')]; 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('color_type_id')]; 
			}
			
			$sewing_production_po_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('item_number_id')]]+=$val[csf('sewing_output')];
			
			$sewing_production_po_data_arr3[$val[csf('serving_company')]][$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('item_number_id')]]+=$val[csf('sewing_output')];
			
			$sewing_production_po_data_arr4[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('color_type_id')]][$val[csf('style_ref_no')]][$val[csf('item_number_id')]]+=$val[csf('sewing_output')];

			//$production_color_type_data_arr[$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('color_type_id')]][$val[csf('style_ref_no')]][$val[csf('item_number_id')]]['good_qnty']+=$val[csf('good_qnty')];



			
			
			$job_smv_set[$val[csf('serving_company')]][$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('set_break_down')];
		
		

		
		

		}

		$all_style_arr[$val[csf('serving_company')]][$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];			
		$com_style_po_id_arr[$val[csf('serving_company')]][$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
		
		if($val[csf('good_qnty')]){
			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id_poly']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id_poly'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('color_type_id')]; 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id_poly']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]."**".$val[csf('style_ref_no')]."**".$val[csf('color_type_id')]; 
			}
			
			$production_po_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('good_qnty')];
			
		}
		
		//$all_po_id_arr[$val[csf('serving_company')]][$val[csf('color_type_id')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
}

 

		foreach($company_library as $companyId=>$compname)
		{
			
		 
			$smv_source=$smv_source_arr[$companyId]*1; 
			 
			if($smv_source==3)
			{
				$gsdSql="select a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$from_date' and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(4)  ".where_con_using_array($all_style_arr[$companyId],1,'a.STYLE_REF')." and a.APPROVED=1  
					group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,a.TOTAL_SMV,a.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
					 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
						// echo $gsdSql.'======'; die;
					$gsdSqlResult = sql_select($gsdSql); 
					$item_smv_array=array();$item_smv_array2=array();
					foreach($gsdSqlResult as $rows)
					{
						foreach($com_style_po_id_arr[$companyId][$rows[STYLE_REF]] as $po_id)
						{
							if($item_smv_array[$companyId][$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]==''){
								$item_smv_array[$companyId][$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
							}
							if($item_smv_array2[$companyId][$po_id][$rows[GMTS_ITEM_ID]]==''){
								$item_smv_array2[$companyId][$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
							}
							
							
						}
					}
					unset($gsdSqlResult);

					
			}
			else
			{
				
				
				$sql_item="select b.id,c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) $where_cond2";
				 // echo $sql_item;die;
				//$item_smv_array=array();
				$resultItem=sql_select($sql_item);
				foreach($resultItem as $itemData)
				{
					if($smv_source==1)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
					}
					else if($smv_source==2)
					{
						$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
					}
				}
				unset($resultItem);
			}			
			
			
			foreach($production_data_arr[$companyId] as $f_id=>$fname)
			{
				ksort($fname);
				foreach($fname as $l_id=>$ldata)
				{
					//...............................................
					if($f_id==20){$rows[csf("floor_id")]=24;}
					else{$rows[csf("floor_id")]=$f_id;}
					
					if($companyId==2){$rows[csf("floor_id")]="cf_1";}
					else if($companyId==4){$rows[csf("floor_id")]="cf_2";}
					else if($companyId==3){$rows[csf("floor_id")]=0;}
					
					$newCompany=$companyId;
					
					
					//................................................
					
					
					$poly_qty_arr[$newCompany][$rows[csf("floor_id")]]+=array_sum($production_po_data_arr[$companyId][$f_id][$l_id]);
					$sewing_qty_arr[$newCompany][$rows[csf("floor_id")]]+=array_sum($sewing_production_po_data_arr[$companyId][$f_id][$l_id]);
					
					$germents_item=array_unique(explode('****',$ldata['item_number_id']));
					
					$chk_smv_array = array();
					$temp_data_arr = array();
					foreach($germents_item as $g_val)
					{
						$po_garment_item=explode('**',$g_val);
						$item_id = $po_garment_item[1];
						$po_id=$po_garment_item[0];
						$style = $po_garment_item[2];
						$colour_type_id = $po_garment_item[3];
						$qnty = 0;
						if($temp_data_arr[$companyId][$f_id][$l_id][$colour_type_id][$style][$item_id]==''){
							$qnty=$sewing_production_po_data_arr4[$companyId][$f_id][$l_id][$colour_type_id][$style][$item_id];
							$temp_data_arr[$companyId][$f_id][$l_id][$colour_type_id][$style][$item_id]=1;
						}
						
						$color_type_smv = ($item_smv_array[$companyId][$po_id][$colour_type_id][$item_id]) ?$item_smv_array[$companyId][$po_id][$colour_type_id][$item_id] : $item_smv_array2[$companyId][$po_id][$item_id];

				 
						
						if($sewing_production_po_data_arr3[$companyId][$po_id][$f_id][$l_id][$item_id] !="")
						{
							$break_down_smv_arr = explode("__",$job_smv_set[$companyId][$po_id][$f_id][$l_id]);
							foreach($break_down_smv_arr as $smv)
							{
								$set_smv = explode("_", $smv);

								if($item_id==$set_smv[0])
								{
									if(!isset($chk_smv_array[$companyId][$style][$colour_type_id][$item_id]))
									{  
										
										$set_smv[2] = ($color_type_smv !="")?$color_type_smv:$set_smv[2];
										$sewing_produce_arr[$newCompany][$rows[csf("floor_id")]] += ($qnty*$set_smv[2]);
										//$produce_minit2+= ($qnty);//*$set_smv[2]
										
										$chk_smv_array[$companyId][$style][$colour_type_id][$item_id] = $set_smv[2];

										//echo $item_id.'*'. $qnty.'*'.$set_smv[2].'='.$color_type_smv."<br>";
									}
								}
							}
						}
					}
				}
			}
		}//company end;
	 
	
		$company_cond=" and a.serving_company in(".$companyStr.")";
		$sql_cond=" and a.production_date between '$from_date' and '$from_date'";
		
		$sql="SELECT  a.serving_company,a.production_type, a.floor_id, a.production_date, a.sewing_line, a.po_break_down_id, a.item_number_id, d.color_type_id, d.production_qnty as good_qnty  ,b.STYLE_REF_NO
				from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
				where  a.production_type in(5,11) and d.production_type in(5,11) and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.po_break_down_id=e.po_break_down_id and d.color_size_break_down_id=e.id and b.job_no=e.job_no_mst and c.id=e.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3)  and e.status_active in(1,2,3) and e.is_deleted=0 $sql_cond $company_cond 
				order by a.floor_id, a.po_break_down_id";
				 //echo $sql;die;

		$sql_result=sql_select($sql);	
		$sewing_production_po_data_arr=array();
		$poly_production_po_data_arr=array();
		$all_po_id_arr=array();
		foreach($sql_result as $val)
		{
		
			$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')].'**'.$val[csf('po_break_down_id')].'**'.$val[csf('item_number_id')].'**'.$val[csf('production_date')].'**'.$val[csf('serving_company')].'**'.$val[csf('color_type_id')];
			
			if($val[csf('production_type')]==5){// sewing
				$sewing_production_po_data_arr[$val[csf('serving_company')]][$key]+=$val[csf('good_qnty')];
				//$sewing_production_po_produce_data_arr[$val[csf('serving_company')]][$key]+=$val[csf('produce')];
			}
			else if($val[csf('production_type')]==11){// poly
				$poly_production_po_data_arr[$val[csf('serving_company')]][$key]+=$val[csf('good_qnty')];
				//$poly_production_po_produce_data_arr[$val[csf('serving_company')]][$key]+=$val[csf('produce')];
			}
			
			$all_po_id_arr[$val[csf('serving_company')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			$all_style_arr[$val[csf('serving_company')]][$val[csf('STYLE_REF_NO')]]=$val[csf('STYLE_REF_NO')];
			$com_style_po_id_arr[$val[csf('serving_company')]][$val[csf('STYLE_REF_NO')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
	
		}
		unset($sql_result);

$production_date_con = " and a.production_date between '".$previous_date."' and '".$previous_date."'";

$sql_subcon="select a.company_id,a.production_type,a.order_id as po_break_down_id, a.gmts_item_id as item_number_id,a.floor_id,a.line_id as sewing_line,a.production_date, 
sum(case when a.production_type=2 and d.production_type=2 then (c.smv*d.prod_qnty) else 0 end) as produce, 
sum(case when a.production_type=2 and d.production_type=2 then (c.smv*d.prod_qnty) else 0 end) as sewing_produce, 
sum(case when a.production_type=5 and d.production_type=5 then d.prod_qnty else 0 end) as good_qnty ,
sum(case when a.production_type=2 and d.production_type=2 then d.prod_qnty else 0 end) as sewing_output 
from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d,subcon_ord_dtls c
where a.order_id=c.id and a.production_type in(2,5) and d.production_type in(2,5) and a.id=d.dtls_id and a.status_active=1 and a.is_deleted=0 and a.company_id  in(".implode(',',array_keys($company_library)).") $production_date_con group by a.company_id,a.production_type,a.order_id,
 a.gmts_item_id,a.floor_id,a.line_id,a.production_date";

//echo $sql_subcon;die;

$sql_subcon_result = sql_select($sql_subcon, '', '', '', $con);
foreach($sql_subcon_result as $rows)
{
	//this is for urmi group.........................start;
	/*
	Note: 
		Floor Technical replace to Unit 1;
		Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
	*/
	if($rows[csf("floor_id")]==20){$rows[csf("floor_id")]=24;}
	
	//if($rows[csf("company_id")]==4){$rows[csf("company_id")]=2;$rows[csf("floor_id")]=0;}
	//if($rows[csf("company_id")]==3 || $rows[csf("company_id")]==2){$rows[csf("floor_id")]=0;}
	
	if($rows[csf("company_id")]==2){$rows[csf("floor_id")]="cf_1";}
	else if($rows[csf("company_id")]==4){$rows[csf("floor_id")]="cf_2";}
	else if($rows[csf("company_id")]==3){$rows[csf("floor_id")]=0;}

	
	//this is for urmi group.........................end;
	
	//$poly_qty+=$rows[csf("good_qnty")];
	$poly_qty_arr[$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("good_qnty")];
	$poly_produce_arr[$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("produce")];
	
	$sewing_qty_arr[$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("sewing_output")];
	$sewing_produce_arr[$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("sewing_produce")];

//-------------------------
	$key=$rows[csf('floor_id')].'**'.$rows[csf('sewing_line')].'**'.$rows[csf('po_break_down_id')].'**'.$rows[csf('item_number_id')].'**'.$rows[csf('production_date')].'**'.$rows[csf('company_id')].'**'.($rows[csf('color_type_id')]*1);
			
	if($rows[csf('production_type')]==2){// sewing
		$sewing_production_po_data_arr[$rows[csf('company_id')]][$key]+=$rows[csf('sewing_output')];
	}
	
}
unset($sql_subcon_result); 

// echo "<pre>";
// var_dump($poly_qty_arr);
// echo "</pre>";

$floor_library['cf_1']="(Demra)";
$floor_library['cf_2']="(Tejgaon)";
$gmtDataArr=array();
 foreach($poly_qty_arr as $comapny_id=>$rows){
	$rowspan=count($rows);
	krsort($rows);
	foreach($rows as $floor_id=>$polyQty){
		$key=$comapny_id.'*'.$floor_id;
		$gmtDataArr[$key]['garments']=$company_library[$comapny_id].' '.$floor_library[$floor_id];
		$gmtDataArr[$key]['sewing_roduction']=$sewing_qty_arr[$comapny_id][$floor_id];
		$gmtDataArr[$key]['poly_roduction']=$polyQty;
		$gmtDataArr[$key]['sewing_produce']=$sewing_produce_arr[$comapny_id][$floor_id];
		$gmtDataArr[$key]['shipment']=$ex_fac_qty_arr[$comapny_id][$floor_id];
	}
}

if($gmtDataArr['2*cf_1']==''){
	$gmtDataArr['2*cf_1']['garments']=$company_library[2].' '.$floor_library['cf_1'];
}
if($gmtDataArr['4*cf_2']==''){
	$gmtDataArr['4*cf_2']['garments']=$company_library[4].' '.$floor_library['cf_2'];
}


asort($gmtDataArr);

 
ob_start();
?>


<table cellpadding="3" cellspacing="0" border="1" rules="all">
    <tr><th colspan="6">Daily Report</th></tr>		
    <tr><th colspan="6">Urmi Group</th></tr>		
    <tr><th colspan="6">Date-<? echo date('d.m.Y',strtotime($previous_date)); ?></th></tr>		
    <tr bgcolor="#DDD">
        <th width="110">Garments</th>
        <th width="60">Production<br />in Sewing</th>
        <th width="60">Production<br />in Poly</th>
        <th width="60">Produce Minute (Sewing)</th>
        <!--<th width="60">Sew Eff%</th>-->
        <th width="60">Shipment</th>
    </tr>
    <? 
	$totalPolyQty=0;$totalExFactoryQty=0;



	// $tempArr=["1*24"=>1,"1*19"=>2,"1*66"=>3,"4*cf_2"=>4,"2*cf_1"=>5,"3*0"=>6];
	// $gmtDataNewArr=array();
	// foreach($gmtDataArr as $comapny_id=>$rows){
	// 	$gmtDataNewArr[$tempArr[$comapny_id]]=$rows;
	// }
	// ksort($gmtDataNewArr);

	$tempArr=[1=>"1*24",2=>"1*19",3=>"1*66",4=>"4*cf_2",5=>"2*cf_1",6=>"3*0"];
	$gmtDataNewArr=array();
	foreach($tempArr as $key){
		$gmtDataNewArr[$key]=$gmtDataArr[$key];
	}
	$gmtDataNewArr['1*66']['garments']="FTML 4 - Unit";


	foreach($gmtDataNewArr as $comapny_id=>$rows){
		
		$totalPolyQty+=$rows['poly_roduction'];
		$totalExFactoryQty+=$rows['shipment'];	
		$totalProduceQty+=$rows['sewing_produce'];
		$totalSewingQty+=$rows['sewing_roduction'];
		$totalSew_eff+=$rows['sew_eff'];
	
	?>
    <tr>
        <td><?=$rows['garments'];?></td>
        <td align="right"><?=number_format($rows['sewing_roduction']);?></td>
        <td align="right"><?=number_format($rows['poly_roduction']);?></td>
        <td align="right"><?=number_format($rows['sewing_produce']);?></td>
        <?php /*?><td align="right" title="<?=$comapny_id;?>"><?=number_format($rows[sew_eff]);?></td><?php */?>
        <td align="right"><?=number_format($rows['shipment']);?></td>
    </tr>		
    <? $i++;} ?>
    <tr bgcolor="#DDD">
        <td align="right"><strong>Total</strong></td>
        <td align="right"><strong><?= number_format($totalSewingQty);?></strong></td>
        <td align="right"><strong><?= number_format($totalPolyQty);?></strong></td>
        <td align="right"><strong><?= number_format($totalProduceQty);?></strong></td>
        <?php /*?><td align="right"><strong><?= number_format($totalSew_eff);?></strong></td><?php */?>
        <td align="right"><strong><?= number_format($totalExFactoryQty);?></strong></td>
    </tr>		
</table>

<br>

<?
	$html=ob_get_contents();
	ob_clean();

	//----------------------------------------------
	

	$sql_cond=" and pr_date between '$from_date' and '$to_date'";
	$sql="select a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id in(".implode(',',array_keys($company_library)).") and a.is_deleted=0 and b.is_deleted=0 $sql_cond order by a.company_id";
	 //echo $sql;die;

	
	$dataArray=sql_select($sql);// and a.id=1 and c.from_date=$end_date
	$prod_resource_data_arr=array();
	foreach($dataArray as $rows)
	{
		$prod_resource_data_arr[$rows[csf('company_id')]][]=$rows;
	}
	unset($dataArray);

	foreach($company_library as $company_id=>$comapny_name){
		
		
		//if($from_date!="" && $to_date!=""){$sql_cond=" and pr_date between '$from_date' and '$to_date'";}
		$prod_reso_allo=0;
		$prod_reso_allo=$prod_reso_allo_arr[$company_id];


		 
		 $prod_resource_array=array();
		 if($prod_reso_allo==1)
		 {
			foreach($prod_resource_data_arr[$company_id] as $val)
			{
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']+=$val[csf('man_power')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']+=$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('mc_capacity')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
				$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
				
			}
			unset($prod_resource_data_arr[$company_id]);
			
		 }
	
	 	 
		
		//$company_cond=" and company_name=$company_id";
		$smv_source=0;
		$smv_source=$smv_source_arr[$company_id]; 
		
			$where_cond=''; $where_cond2=''; $poIds_cond=''; $poIds_cond2='';
			if($db_type==2 && count($all_po_id_arr[$company_id])>999)
			{
				$po_id_chunk_arr=array_chunk($all_po_id_arr[$company_id],999) ;
				foreach($po_id_chunk_arr as $chunk_arr)
				{
	
	
					$chunk_arr_value=implode(",",$chunk_arr);	
					$poIds_cond.=" c.id in($chunk_arr_value) or ";
					$poIds_cond2.=" b.id in($chunk_arr_value) or ";	
				}
				
				$where_cond.=" and (".chop($poIds_cond,'or ').")";
				$where_cond2.=" and (".chop($poIds_cond2,'or ').")";		
			}
			else
			{
				$where_cond=" and c.id in(".implode(',',$all_po_id_arr[$company_id]).")";
				$where_cond2=" and b.id in(".implode(',',$all_po_id_arr[$company_id]).")";	 
			}
			
			unset($all_po_id_arr[$company_id]);
		
		
		//================XXX===========
		


			
		
		$current_date_time=date('d-m-Y H:i');
		$ex_date_time=explode(" ",$current_date_time);
		$current_date=$ex_date_time[0];
		$current_time=$ex_date_time[1];
		$ex_time=explode(":",$current_time);
		
		$search_prod_date=change_date_format(str_replace("'","",$to_date),'yyyy-mm-dd');
		$current_eff_min=($ex_time[0]*60)+$ex_time[1];
		
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));
		$dif_time=$difa_time[0];
		$dif_hour_min=date("H:i", strtotime($dif_time));
		
	
		//var_dump($item_smv_array);die;
		
		//Sewing...............................................
		$sewing_produce_arr=array();
		$tempSewingDateLine=array();
		foreach($sewing_production_po_data_arr[$company_id] as $key=>$good_qnty){
			list($floor_id,$line_id,$po_id,$item_id,$production_date,$color_type_id)=explode('**',$key);
				//$produce_minit_arr[$production_date]+=$good_qnty*$item_smv_array[$po_id][$item_id];
				
			if(($floor_id==14 || $floor_id==20 || $floor_id==24 || $floor_id==37) and $company_id==1){$company_id_str='unite_1';}
			else if( $floor_id==66 and $company_id==1){$company_id_str='unite_4';}
			else if(($floor_id==7 || $floor_id==15 || $floor_id==19) and $company_id==1){$company_id_str='unite_2';}
			else{$company_id_str=$company_id;}
				
				$sewing_achieved_arr[$company_id_str]+=$good_qnty;
				$color_type_smv=($item_smv_array[$company_id][$po_id][$color_type_id][$item_id])?$item_smv_array[$company_id][$po_id][$color_type_id][$item_id]:$item_smv_array2[$company_id][$po_id][$item_id];
				
			
				//ini_set("display_errors", 0);

				$sewing_produce_arr[$company_id_str]+=(int)$color_type_smv*$good_qnty;

	 

		 
			 
		
				 
				if($tempSewingDateLine[$company_id_str][$floor_id.$line_id.$production_date] != 1){
					$tempSewingDateLine[$company_id_str][$floor_id.$line_id.$production_date]=1;
				
					
					$smv_adjustmet_type=$prod_resource_array[$line_id][$production_date]['smv_adjust_type'];
					$total_adjustment=0;
					if(str_replace("'","",$smv_adjustmet_type)==1)
					{ 
						$total_adjustment=$prod_resource_array[$line_id][$production_date]['smv_adjust'];
					}
					else if(str_replace("'","",$smv_adjustmet_type)==2)
					{
						$total_adjustment=($prod_resource_array[$line_id][$production_date]['smv_adjust'])*(-1);
					}
					$sewing_SMV_svailable_arr[$company_id_str]+=$total_adjustment+($prod_resource_array[$line_id][$production_date]['man_power'])*$prod_resource_array[$line_id][$production_date]['working_hour']*60;
					
					$efficiency_min_arr[$company_id_str]+=$prod_resource_array[$line_id][$production_date]['working_hour']*$prod_resource_array[$line_id][$production_date]['terget_hour'];
				}
		}//foreach end;
	

	
		//echo $current_date.'=='.$search_prod_date;
		//Poly...............................................
		$tempPolyDateLine=array();$poly_produce_arr=array();
		foreach($poly_production_po_data_arr[$company_id] as $key=>$good_qnty){
			list($floor_id,$line_id,$po_id,$item_id,$production_date,$color_type_id)=explode('**',$key);
		
			if(($floor_id==14 || $floor_id==20 || $floor_id==24 || $floor_id==37) and $company_id==1){$company_id_str='unite_1';}
			elseif( $floor_id==66 and $company_id==1){$company_id_str='unite_4';}
			else if(($floor_id==7 || $floor_id==15 || $floor_id==19) and $company_id==1){$company_id_str='unite_2';}
			else{$company_id_str=$company_id;}
			
			$current_wo_time=0;
			if($current_date==$search_prod_date)
			{
				$prod_wo_hour=$prod_resource_array[$line_id][$production_date]['working_hour'];
				
				if ($dif_time<$prod_wo_hour)//
				{
					$current_wo_time=$dif_hour_min;
					$cla_cur_time=$dif_time;
				}
				else
				{
					$current_wo_time=$prod_wo_hour;
					$cla_cur_time=$prod_wo_hour;
				}
			}
			else
			{
				$current_wo_time=$prod_resource_array[$line_id][$production_date]['working_hour'];
				$cla_cur_time=$prod_resource_array[$line_id][$production_date]['working_hour'];
			}
			
			//$poly_produce_minit_arr[$production_date]+=$good_qnty*$item_smv_array[$po_id][$item_id];
			$poly_achieved_arr[$company_id_str]+=$good_qnty;
			
			$color_type_smv=($item_smv_array[$company_id_str][$po_id][$color_type_id][$item_id])?$item_smv_array[$company_id_str][$po_id][$color_type_id][$item_id]:$item_smv_array2[$company_id_str][$po_id][$item_id];
			

			$poly_produce_arr[$company_id_str]+=$color_type_smv*$good_qnty;
			 //echo $company_id;die;
			//$total_adjustment+($prod_resource_array[$l_id][$pr_date]['man_power'])*$cla_cur_time*60;
			//$produce_minit=$item_smv_array[$po_id][$item_id]*$good_qnty;
			
			
			if($tempPolyDateLine[$company_id][$floor_id.$line_id.$production_date] != 1){
				$tempPolyDateLine[$company_id][$floor_id.$line_id.$production_date] = 1;
				
				$smv_adjustmet_type=$prod_resource_array[$line_id][$production_date]['smv_adjust_type'];
				$total_adjustment=0;
				if(str_replace("'","",$smv_adjustmet_type)==1)
				{ 
					$total_adjustment=$prod_resource_array[$line_id][$production_date]['smv_adjust'];
				}
				else if(str_replace("'","",$smv_adjustmet_type)==2)
				{
					$total_adjustment=($prod_resource_array[$line_id][$production_date]['smv_adjust'])*(-1);
				}
				$poly_SMV_svailable_arr[$company_id_str]+=$total_adjustment+($prod_resource_array[$line_id][$production_date]['man_power'])*$prod_resource_array[$line_id][$production_date]['working_hour']*60;
				
				$poly_efficiency_min_arr[$company_id_str]+=($prod_resource_array[$line_id][$production_date]['working_hour']*$prod_resource_array[$line_id][$production_date]['terget_hour']);
			}
			$efficiency_min=$total_adjustment+($prod_resource_array[$line_id][$production_date]['man_power'])*$cla_cur_time*60;
			$poly_effproduce_arr[$company_id_str]+=$efficiency_min;
			
		
			unset($poly_production_po_data_arr[$company_id]);
		}
		//echo $company_id.'--';
		
	}

	//echo 9;
	//print_r($prod_resource_array);die;

 

	$ftml_unite_1=($poly_achieved_arr['unite_1']*100)/$poly_efficiency_min_arr['unite_1'];
	if(is_infinite($ftml_unite_1) || is_nan($ftml_unite_1)){$ftml_unite_1=0;}

	$ftml_unite_4=($poly_achieved_arr['unite_4']*100)/$poly_efficiency_min_arr['unite_4'];
	if(is_infinite($ftml_unite_4) || is_nan($ftml_unite_4)){$ftml_unite_4=0;}

	$ftml_unite_2=($sewing_achieved_arr['unite_2']*100)/$efficiency_min_arr['unite_2'];

	//$ftml_unite_2='('.$sewing_achieved_arr['unite_2'].'*100)/'.$efficiency_min_arr['unite_2'];
	//echo $ftml_unite_2;

	if(is_infinite($ftml_unite_2) || is_nan($ftml_unite_2)){$ftml_unite_2=0;}
	$urmi=(($poly_achieved_arr[2])*100)/($poly_efficiency_min_arr[2]);
	
	$attr=(($poly_achieved_arr[4])*100)/($poly_efficiency_min_arr[4]);
	
	
	if(is_infinite($urmi) || is_nan($urmi)){$urmi=0;}
	$uhm=($poly_achieved_arr[3]*100)/$poly_efficiency_min_arr[3];
	if(is_infinite($uhm) || is_nan($uhm)){$uhm=0;}
	
	
	$total_target=$poly_efficiency_min_arr['unite_1']+$poly_efficiency_min_arr['unite_4']+$efficiency_min_arr['unite_2']+($poly_efficiency_min_arr[2]+$poly_efficiency_min_arr[4])+$poly_efficiency_min_arr[3];
	$total_achive=$poly_achieved_arr['unite_1']+$poly_achieved_arr['unite_4']+$sewing_achieved_arr['unite_2']+($poly_achieved_arr[2]+$poly_achieved_arr[4])+$poly_achieved_arr[3];
	
	
	//------------
	$sewing_achieve_per_ftml_unite_2=($sewing_produce_arr['unite_2']/$sewing_SMV_svailable_arr['unite_2'])*100;
	$sewing_achieve_per_ftml_unite_1=($sewing_produce_arr['unite_1']/$sewing_SMV_svailable_arr['unite_1'])*100;
	$sewing_achieve_per_ftml_unite_4=($sewing_produce_arr['unite_4']/$sewing_SMV_svailable_arr['unite_4'])*100;
	
	$achieve_per_urmi=($sewing_produce_arr2[2]['cf_1']/($sewing_SMV_svailable_arr[2]))*100;
	$achieve_per_attr=($sewing_produce_arr2[4]['cf_2']/($sewing_SMV_svailable_arr[4]))*100;
	
	$achieve_per_uhm=($sewing_produce_arr2[3][0]/$sewing_SMV_svailable_arr[3])*100;
	
	$sewing_achieve_per_ftml_unite_2=($sewing_produce_arr2[1][19]/$sewing_SMV_svailable_arr['unite_2'])*100;
	$sewing_achieve_per_ftml_unite_1=($sewing_produce_arr2[1][24]/$sewing_SMV_svailable_arr['unite_1'])*100;
	//$sewing_achieve_per_ftml_unite_4=($sewing_produce_arr2[1][24]/$sewing_SMV_svailable_arr['unite_4'])*100;
	
	$gmtDataArr['1*24']['sew_eff']=$sewing_achieve_per_ftml_unite_1;
	$gmtDataArr['1*19']['sew_eff']=$sewing_achieve_per_ftml_unite_2;
	$gmtDataArr['2*cf_1']['sew_eff']=$achieve_per_urmi;
	$gmtDataArr['3*0']['sew_eff']=$achieve_per_uhm;
	$gmtDataArr['4*cf_2']['sew_eff']=$achieve_per_attr;
 

	ob_start();
?>




<table cellspacing="0" border="1" rules="all">
	<tr bgcolor="#DDD">
    	<th>Garments</th>
    	<th>Target</th>
    	<th>Achieved</th>
    	<th>Achieve %</th>
        <!--<th><p>Sew Eff%</p></th>-->
    </tr>
    <tr>
    	<td>FTML 1 - Unit (Poly)</td>
    	<td align="right"><? echo number_format($poly_efficiency_min_arr['unite_1']);?></td>
    	<td align="right"><? echo number_format($poly_achieved_arr['unite_1']);?></td>
    	<td align="right"><? echo number_format($ftml_unite_1);?></td>
    	<!--<td align="right">< ? echo fn_number_format($sewing_achieve_per_ftml_unite_1);?></td>-->
	</tr>
    <tr>
    	<td>FTML 2 - Unit (Sewing)</td>
    	<td align="right"><? echo number_format($efficiency_min_arr['unite_2']);?></td>
    	<td align="right"><? echo number_format($sewing_achieved_arr['unite_2']);?></td>
    	<td align="right"><? echo number_format($ftml_unite_2);?></td>
    	<!--<td align="right">< ? echo fn_number_format($sewing_achieve_per_ftml_unite_2);?></td>-->
	</tr>

    <tr>
    	<td>FTML 4 - Unit (Poly)</td>
    	<td align="right"><? echo number_format($poly_efficiency_min_arr['unite_4']);?></td>
    	<td align="right"><? echo number_format($poly_achieved_arr['unite_4']);?></td>
    	<td align="right"><? echo number_format($ftml_unite_4);?></td>
    	<!--<td align="right">< ? echo fn_number_format($sewing_achieve_per_ftml_unite_1);?></td>-->
	</tr>

    <tr>
    	<td>ATTIR (Poly)</td>
    	<td align="right"><? echo number_format($poly_efficiency_min_arr[4]);?></td>
    	<td align="right"><? echo number_format($poly_achieved_arr[4]);?></td>
    	<td align="right"><? echo number_format($attr);?></td>
    	<!--<td align="right">< ? echo fn_number_format($achieve_per_attr);?></td>-->
	</tr>
    
    <tr>
    	<td>URMI (Poly)</td>
    	<td align="right"><? echo number_format($poly_efficiency_min_arr[2]);?></td>
    	<td align="right"><? echo number_format($poly_achieved_arr[2]);?></td>
    	<td align="right"><? echo number_format($urmi);?></td>
    	<!--<td align="right">< ? echo fn_number_format($achieve_per_urmi);?></td>-->
	</tr>
    
    
    <tr>
    	<td>UHM (Poly)</td>
    	<td align="right"><? echo number_format($poly_efficiency_min_arr[3]);?></td>
    	<td align="right"><? echo number_format($poly_achieved_arr[3]);?></td>
    	<td align="right"><? echo number_format($uhm);?></td>
    	<!--<td align="right">< ? echo fn_number_format($achieve_per_uhm);?></td>-->
	</tr>
    <tr bgcolor="#DDD">
    	<td align="right"><b>Total</b></td>
    	<td align="right"><b><? echo number_format($total_target);?></b></td>
    	<td align="right"><b><? echo number_format($total_achive);?></b></td>
    	<td align="right"></td>
    	<!--<td align="right"></td>-->
	</tr>
</table>

<?
	$html .= ob_get_contents();
	ob_clean();
	$file_name = 'html/garments.html';
	$create_file = fopen($file_name, 'w');	
	fwrite($create_file,$html);
	echo $html;
	?>