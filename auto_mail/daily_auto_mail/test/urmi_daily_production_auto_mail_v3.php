<?php 
ini_set('memory_limit','20624M');
ini_set('precision', 8);
ini_set("display_errors", 1);
ini_set("max_execution_time", 300000000);
date_default_timezone_set("Asia/Dhaka");
 
// $file = '../locl.txt';
// $yesterday = file_get_contents($file);
// $today = date("d-m-Y",time());
// file_put_contents($file, $today);
// //if($yesterday==$today){exit();}


require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
//require '../vendor/autoload.php';
require_once('../setting/mail_setting.php');

$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0 ", "id", "company_short_name",$con);
$floor_library = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");

 $conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);
 //echo $conversion_rate;die;
 //$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer where id in(select  buyer_id from  lib_buyer_party_type where party_type=1)", "id", "buyer_name"  );
 $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
		//$previous_date='16-Jul-2018';$current_date='16-Jul-2018';
	}
			
	//$previous_date='12-Aug-2021';$current_date='12-Aug-2021';

	
	if($db_type==0){
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}

	
	
	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):'';
		
	}

	$current_date=$previous_date;


//Greay Febric Stock Qty.............................................................start;
//[Note:Style Wise Grey Fabric Stock Report]
	

require_once('../urmi_daily_mail/grand_stock_qty.php');				
$greyStockQty=$grand_stock_qty;
unset($grand_stock_qty);




//-----------------------------------------------end gray stock;



$smv_source_arr = return_library_array("select company_name,smv_source from variable_settings_production where company_name in(".implode(',',array_keys($company_library)).") and variable_list=25 and status_active=1 and is_deleted=0","company_name","smv_source");


//Exfactory--------------------
	$ex_factory_date_con = " and b.ex_factory_date between '".$previous_date."' and '".$current_date."'";
	 
	$ex_factory_sql="select a.delivery_company_id,a.delivery_floor_id,sum(b.ex_factory_qnty) ex_factory_qnty  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where b.delivery_mst_id=a.id and  a.delivery_company_id in(".implode(',',array_keys($company_library)).") $ex_factory_date_con and a.is_deleted=0 and a.status_active=1 
	 AND b.status_active IN (1, 2, 3)  and b.entry_form!=85
	group by a.delivery_company_id,a.delivery_floor_id";
	 
	
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
		$sql_query="select  a.serving_company, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, a.po_break_down_id, e.item_number_id, c.po_number as po_number,d.color_type_id,
			
			sum(case when a.production_type=11 and d.production_type=11 then d.production_qnty else 0 end) as good_qnty ,
			sum(case when a.production_type=5 and d.production_type=5 then d.production_qnty else 0 end) as sewing_output 		
			
			from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
			where a.production_type in(5,11) and d.production_type in(5,11) and a.id=d.mst_id and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and e.id=d.color_size_break_down_id and e.po_break_down_id=c.id and a.po_break_down_id=e.po_break_down_id AND a.serving_company in(".implode(',',array_keys($company_library)).") $production_date_con 
			
			--and a.po_break_down_id in(select id from WO_PO_BREAK_DOWN where PO_NUMBER in('20204011300','694522 ARTEMOS-KSA','694369 CRYSTAM-KSA' ))
			--and a.sewing_line in(199) 
			
			group by a.serving_company, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, e.item_number_id,d.color_type_id, c.po_number order by a.location, a.floor_id, a.po_break_down_id"; 
	// echo $sql_query;die;
	 
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
				
				$sewing_production_po_data_arr4[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('color_type_id')]][$val[csf('item_number_id')]]+=$val[csf('sewing_output')];
				
				
				$job_smv_set[$val[csf('serving_company')]][$val[csf('po_break_down_id')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]=$val[csf('set_break_down')];
			
				$all_style_arr[$val[csf('serving_company')]][$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];			
				$com_style_po_id_arr[$val[csf('serving_company')]][$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			
			
			}
			
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
			
			$all_po_id_arr[$val[csf('serving_company')]][$val[csf('color_type_id')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
			
			
			
			
		}

		//echo implode("','",$all_style_arr[3]);die;

		
		$item_smv_array=array();$item_smv_array2=array();
		foreach($all_style_arr as $com_id=>$styleArr){
			
			$smv_source=$smv_source_arr[$com_id];
			if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		 
			if($smv_source==3)
			{
			 
			$gsdSql="SELECT a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$previous_date' and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(4) ".where_con_using_array($all_style_arr[$com_id],1,'a.STYLE_REF')."  and a.APPROVED=1 
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,a.TOTAL_SMV,a.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			 
			    //echo $gsdSql."*".$com_id.'======'; 
				$gsdSqlResult = sql_select($gsdSql); 
				foreach($gsdSqlResult as $rows)
				{
					foreach($com_style_po_id_arr[$com_id][$rows[STYLE_REF]] as $po_id)
					{
						if($item_smv_array[$com_id][$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]==''){
							$item_smv_array[$com_id][$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
						}
						if($item_smv_array2[$com_id][$po_id][$rows[GMTS_ITEM_ID]]=='')
						{
							$item_smv_array2[$com_id][$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
						}
					}


				}
				unset($gsdSqlResult);
				
			}
			else
			{
				$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) ".where_con_using_array($all_po_id_arr[$com_id],0,'b.id').""; //echo $sql_item;die;
				$resultItem=sql_select($sql_item);
				foreach($resultItem as $itemData)
				{
					if($smv_source==1)
					{
						$item_smv_array[$com_id][$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
					}
					else if($smv_source==2)
					{
						$item_smv_array[$com_id][$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
					}
				}
				unset($resultItem);
			}

			
		}//company.....
		//die;
	 
		 //print_r($item_smv_array);die;
		

		foreach($company_library as $companyId=>$compname)
		{
			
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
						if($temp_data_arr[$companyId][$colour_type_id][$f_id][$l_id][$item_id]==''){
							$qnty=$sewing_production_po_data_arr4[$companyId][$f_id][$l_id][$colour_type_id][$item_id];
							$temp_data_arr[$companyId][$colour_type_id][$f_id][$l_id][$item_id]=1;
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
										$sewing_produce_arr[$newCompany][$rows[csf("floor_id")]] += $qnty*$set_smv[2];
										//$sewing_produce_arr[$newCompany][$rows[csf("floor_id")]] += $qnty;
										
										
										$chk_smv_array[$companyId][$style][$colour_type_id][$item_id] = $set_smv[2];
									}
								}
							}
						}
					}
				}
			}
		}//company end;
		
	
	
	
	 //var_dump($poly_qty_arr);die;
	

	$sql_subcon="select a.company_id,a.order_id as po_break_down_id, a.gmts_item_id as item_number_id,a.floor_id,
	sum(case when a.production_type=2 and d.production_type=2 then (c.smv*d.prod_qnty) else 0 end) as produce, 
	sum(case when a.production_type=2 and d.production_type=2 then (c.smv*d.prod_qnty) else 0 end) as sewing_produce, 
	sum(case when a.production_type=5 and d.production_type=5 then d.prod_qnty else 0 end) as good_qnty ,
	sum(case when a.production_type=2 and d.production_type=2 then d.prod_qnty else 0 end) as sewing_output 
	from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d,subcon_ord_dtls c
	where a.order_id=c.id and a.production_type in(2,5) and d.production_type in(2,5) and a.id=d.dtls_id and a.status_active=1 and a.is_deleted=0 and a.company_id  in(".implode(',',array_keys($company_library)).") $production_date_con group by a.company_id,a.order_id,
	 a.gmts_item_id,a.floor_id";
	 //echo $sql_subcon;
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
		
		
	}
	unset($sql_subcon_result); 


//rint_r($poly_produce_arr[3]);die;



	
//$company_library=array(3=>$company_library[3]);
foreach($company_library as $compid=>$compname)/// Total Activities
{
	
//Yearn stock-------------------------------------- 



		$avg_rate_per_unit_arr = return_library_array( "select id,avg_rate_per_unit from product_details_master  where item_category_id=1 and status_active=1 and is_deleted=0 and company_id=$compid", "id", "avg_rate_per_unit",$con);
	  	
		$yearn_stock_qty_sql = "select a.prod_id, 
		sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
		(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
		
		(sum(case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
		sum(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
		
		from inv_transaction a where a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.company_id=$compid  and a.transaction_date <='" . $current_date . "' group by a.prod_id";
          	
			// echo $yearn_stock_qty_sql;die;
			$total_stock_qty=0;$total_stock_amu=0;
          	foreach ($yearn_stock_qty_sql_result as $row) {
          		$total_stock_qty += $row[csf("stock_qty")];
				$total_stock_amu +=($row[csf("stock_qty")]*$avg_rate_per_unit_arr[$row[csf("prod_id")]])/$conversion_rate;
          	}
          	unset($yearn_stock_qty_sql_result);
	
	
	
	
 	
	//Company data arr..................................................
	//$dataArr[$compid]['poly']=$poly_qty;
	//$dataArr[$compid]['exFactory']=$ex_fac_qty;
	
	//$yearnStockQty+=$total_stock_qty;
	//$yearnStockVal+=$total_stock_amu;
	
	


	//$to="";
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
		
}

  $companyStr = implode(',',array_keys($company_library));
   
   $from_date=$previous_date;
   $to_date=$current_date;
   

                      
		
 //Dyeing--------------------------------------                            
    $dye_date_con=" and a.process_end_date between '$previous_date' and '$current_date'";
	
	$dye_sql="select sum(b.production_qty) as production_qty,c.total_trims_weight
from  pro_fab_subprocess a, pro_fab_subprocess_dtls b,pro_batch_create_mst c
where a.id = b.mst_id and a.batch_id=c.id and a.load_unload_id = 2 and a.result=1 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1  and c.status_active=1 $dye_date_con  and a.company_id in($companyStr) group by c.total_trims_weight";
	//echo $dye_sql;
	  
	$dyeing_qty=0;
	$dye_sql_result = sql_select($dye_sql, '', '', '', $con);
	foreach($dye_sql_result as $row)
	{
		$dyeing_qty+=($row[csf('production_qty')]+$row[csf('total_trims_weight')]);
	}
	unset($dye_sql_result);
	
	
	$sub_dye_sql="select sum(c.batch_weight) as batch_weight
from  pro_fab_subprocess a,pro_batch_create_mst c
where a.batch_id=c.id and a.load_unload_id = 2 $dye_date_con and a.company_id in($companyStr) and a.result=1 and a.entry_form=38 and c.status_active = 1 and c.status_active=1  and c.status_active=1";
	 //echo $dye_sql;die;
	  
	$sub_dye_sql_result = sql_select($sub_dye_sql, '', '', '', $con);
	foreach($sub_dye_sql_result as $row)
	{
		$dyeing_qty+=$row[csf('batch_weight')];
	}
	unset($dye_sql_result);	
	$dyeing=$dyeing_qty;

//Dyeing--------------------------------------end      		
		
//Kniting Production--------------------------------------                            
	$str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$current_date."'";
	 $sql_qty="select sum(c.quantity) as qtyinhouse 
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_source in(1,3) and a.knitting_company in($companyStr) $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
  union all
     select sum(b.PRODUCT_QNTY) as qtyinhouse  from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type=2 and b.product_type=2 and a.company_id in($companyStr) and a.product_date between '".$previous_date."' and '".$current_date."'   and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1	 
	 ";//and a.ENTRY_FORM=159 
	 //echo $sql_qty;die;
	 
	$sql_result=sql_select( $sql_qty, '', '', '', $con);
	$kniting_pro_qty=0;
	foreach($sql_result as $row)
	{
		$kniting_pro_qty += $row[csf('qtyinhouse')];
	}				
	unset($sql_result);
	$knite=$kniting_pro_qty;
//Kniting Production--------------------------------------end                            


		
		if ($db_type == 0)
			$select_field = "group_concat(distinct(a.store_id))";
		else if ($db_type == 2)
			$select_field = "listagg(a.store_id,',') within group (order by a.store_id)";
			
			
			$receive_array = array();
          	$sql_receive = "Select a.prod_id, $select_field as store_id, max(a.weight_per_bag) as weight_per_bag, max(a.weight_per_cone) as weight_per_cone,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as rcv_total_opening,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as rcv_total_opening_amt,
          	sum(case when a.transaction_type in (1,4) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as rcv_total_opening_rate,
          	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as purchase,
          	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as purchase_amt,
          	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_loan,
          	sum(case when a.transaction_type in (1) and c.receive_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_loan_amt,
          	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_inside_return,
          	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_inside_return_amt,
          	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_outside_return,
          	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_outside_return_amt 
          	from inv_transaction a, inv_receive_master c where a.mst_id=c.id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id in($companyStr) $store_cond group by a.prod_id";
			
			  
		
          	$result_sql_receive = sql_select($sql_receive, '', '', '', $con);
          	foreach ($result_sql_receive as $row) {
          		$receive_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
          		$receive_array[$row[csf("prod_id")]]['rcv_total_opening'] = $row[csf("rcv_total_opening")];
          		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_amt'] = $row[csf("rcv_total_opening_amt")];
          		$receive_array[$row[csf("prod_id")]]['purchase'] = $row[csf("purchase")];
          		$receive_array[$row[csf("prod_id")]]['purchase_amt'] = $row[csf("purchase_amt")];
          		$receive_array[$row[csf("prod_id")]]['rcv_loan'] = $row[csf("rcv_loan")];
          		$receive_array[$row[csf("prod_id")]]['rcv_loan_amt'] = $row[csf("rcv_loan_amt")];
          		$receive_array[$row[csf("prod_id")]]['rcv_inside_return'] = $row[csf("rcv_inside_return")];
          		$receive_array[$row[csf("prod_id")]]['rcv_inside_return_amt'] = $row[csf("rcv_inside_return_amt")];
          		$receive_array[$row[csf("prod_id")]]['rcv_outside_return'] = $row[csf("rcv_outside_return")];
          		$receive_array[$row[csf("prod_id")]]['rcv_outside_return_amt'] = $row[csf("rcv_outside_return_amt")];
          		//$receive_array[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
          		$receive_array[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
          		$receive_array[$row[csf("prod_id")]]['rcv_total_opening_rate'] = $row[csf("rcv_total_opening_rate")];

          		//$product_wgt_cone_arr[$row[csf("prod_id")]]['weight_per_bag'] = $row[csf("weight_per_bag")];
          		//$product_wgt_cone_arr[$row[csf("prod_id")]]['weight_per_cone'] = $row[csf("weight_per_cone")];
          	}

          	unset($result_sql_receive);
		
          	$issue_array = array();
          	$sql_issue = "select a.prod_id, $select_field as store_id,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as issue_total_opening,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as issue_total_opening_rate,
          	sum(case when a.transaction_type in (2,3) and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as issue_total_opening_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_inside,
          	sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_inside_amt,
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_outside,
          	sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_outside_amt,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as rcv_return,
          	sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as rcv_return_amt,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as issue_loan,
          	sum(case when a.transaction_type=2 and c.issue_purpose=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as issue_loan_amt			
          	from inv_transaction a, inv_issue_master c
          	where a.mst_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond group by a.prod_id";
			
			
			
          	$result_sql_issue = sql_select($sql_issue, '', '', '', $con);
          	foreach ($result_sql_issue as $row) {
          		$issue_array[$row[csf("prod_id")]]['store_id'] = $row[csf("store_id")];
          		$issue_array[$row[csf("prod_id")]]['issue_total_opening'] = $row[csf("issue_total_opening")];
          		$issue_array[$row[csf("prod_id")]]['issue_total_opening_amt'] = $row[csf("issue_total_opening_amt")];
          		$issue_array[$row[csf("prod_id")]]['issue_total_opening_rate'] = $row[csf("issue_total_opening_rate")];
          		$issue_array[$row[csf("prod_id")]]['issue_inside'] = $row[csf("issue_inside")];
          		$issue_array[$row[csf("prod_id")]]['issue_inside_amt'] = $row[csf("issue_inside_amt")];
          		$issue_array[$row[csf("prod_id")]]['issue_outside'] = $row[csf("issue_outside")];
          		$issue_array[$row[csf("prod_id")]]['issue_outside_amt'] = $row[csf("issue_outside_amt")];
          		$issue_array[$row[csf("prod_id")]]['rcv_return'] = $row[csf("rcv_return")];
          		$issue_array[$row[csf("prod_id")]]['rcv_return_amt'] = $row[csf("rcv_return_amt")];
          		$issue_array[$row[csf("prod_id")]]['issue_loan'] = $row[csf("issue_loan")];
          		$issue_array[$row[csf("prod_id")]]['issue_loan_amt'] = $row[csf("issue_loan_amt")];
          	}

          	unset($result_sql_issue);
		
		
         
          	
			$trans_criteria_cond = " and c.transfer_criteria=1";
			$transfer_qty_array = array();
          	

          	$sql_transfer = "select a.prod_id, 
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_out_total_opening,
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_out_total_opening_amt,
          	sum(case when a.transaction_type=6 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_out_total_opening_rate,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_quantity else 0 end) as trans_in_total_opening,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_rate else 0 end) as trans_in_total_opening_rate,
          	sum(case when a.transaction_type=5 and a.transaction_date<'" . $from_date . "' then a.cons_amount else 0 end) as trans_in_total_opening_amt,
          	sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_out_qty,
          	sum(case when a.transaction_type=6 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_out_amt,
          	sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_quantity else 0 end) as transfer_in_qty,
          	sum(case when a.transaction_type=5 and a.transaction_date between '" . $from_date . "' and '" . $to_date . "' then a.cons_amount else 0 end) as transfer_in_amt 
          	from inv_transaction a left join inv_item_transfer_dtls d on a.mst_id = d.mst_id and d.status_active= 1 and d.is_deleted = 0 and a.prod_id = d.to_prod_id and d.item_category = 1, inv_item_transfer_mst c where a.mst_id=c.id and a.transaction_type in (5,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1  and c.is_deleted=0 $trans_criteria_cond $store_cond group by a.prod_id";

		
          	$result_sql_transfer = sql_select($sql_transfer, '', '', '', $con);
          	foreach ($result_sql_transfer as $transRow) 
          	{
          		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_qty'] = $transRow[csf("transfer_out_qty")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_out_amt'] = $transRow[csf("transfer_out_amt")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_qty'] = $transRow[csf("transfer_in_qty")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['transfer_in_amt'] = $transRow[csf("transfer_in_amt")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening'] = $transRow[csf("trans_out_total_opening")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_amt'] = $transRow[csf("trans_out_total_opening_amt")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening'] = $transRow[csf("trans_in_total_opening")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_amt'] = $transRow[csf("trans_in_total_opening_amt")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_in_total_opening_rate'] = $transRow[csf("trans_in_total_opening_rate")];
          		$transfer_qty_array[$transRow[csf("prod_id")]]['trans_out_total_opening_rate'] = $transRow[csf("trans_out_total_opening_rate")];
          		//$product_wgt_cone_arr[$transRow[csf("prod_id")]]["weight_per_bag"] = $transRow[csf("weight_per_bag")];
          	}

          	unset($result_sql_transfer);
		
		$sql = "select a.id, a.avg_rate_per_unit 
		from product_details_master a
		where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.company_id in($companyStr) $search_cond group by a.id, a.avg_rate_per_unit";

			$result = sql_select($sql, '', '', '', $con);
					   
					   
			foreach ($result as $row) 
			{
				$transfer_in_qty = $transfer_qty_array[$row[csf("id")]]['transfer_in_qty'];
				$transfer_out_qty = $transfer_qty_array[$row[csf("id")]]['transfer_out_qty'];

				$trans_out_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_out_total_opening'];
				$trans_in_total_opening = $transfer_qty_array[$row[csf("id")]]['trans_in_total_opening'];

				$openingBalance = ($receive_array[$row[csf("id")]]['rcv_total_opening'] + $trans_in_total_opening) - ($issue_array[$row[csf("id")]]['issue_total_opening'] + $trans_out_total_opening);

				$totalRcv = $receive_array[$row[csf("id")]]['purchase'] + $receive_array[$row[csf("id")]]['rcv_inside_return'] + $receive_array[$row[csf("id")]]['rcv_outside_return'] + $receive_array[$row[csf("id")]]['rcv_loan'] + $transfer_in_qty;
				$totalIssue = $issue_array[$row[csf("id")]]['issue_inside'] + $issue_array[$row[csf("id")]]['issue_outside'] + $issue_array[$row[csf("id")]]['rcv_return'] + $issue_array[$row[csf("id")]]['issue_loan'] + $transfer_out_qty;

				$stockInHand = $openingBalance + $totalRcv - $totalIssue;

				$stock_value = 0;
				
				$stock_value = $stockInHand * $row[csf("avg_rate_per_unit")];
				$stock_value_usd = ($stockInHand * $row[csf("avg_rate_per_unit")]) / $conversion_rate;

				//echo number_format($row[csf("avg_rate_per_unit")], 2);
				//echo number_format($stock_value, 2);
				//echo number_format($avz_rates_usd, 4);
				//echo number_format($stock_value_usd, 2);
				
				$stock+=$stockInHand;
				$valueUsd+=$stock_value_usd;
			}

		
			//echo $stock.'='.$valueUsd;die;

		//Finishing (Kg/Yds)...............................................
		
		$finishigSql="select b.REJECT_QTY,b.receive_qnty,b.uom from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.item_category=2 and a.receive_basis=5 and a.knitting_company in($companyStr) and a.receive_date between '$from_date' and '$to_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //and a.recv_number = 'FTML-FFPE-18-03926'
		$finishigSqlResult = sql_select($finishigSql);
		foreach ($finishigSqlResult as $row) 
		{
			$finishQty[$row[csf('uom')]]+=$row[csf('receive_qnty')];
			$finishRejQty[$row[csf('uom')]]+=$row['REJECT_QTY'];

		}
			
			
			
		$sub_finishig_sql="select 12 as uom, b.product_qnty as finish_qty,b.REJECT_QNTY from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id  and a.product_type=4 and a.product_date between '$from_date' and '$to_date' and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1
"; 
		$sub_finishig_sql_result = sql_select($sub_finishig_sql);
		foreach ($sub_finishig_sql_result as $row) 
		{
			$finishQty[$row[csf('uom')]]+=$row[csf('finish_qty')];
			$finishRejQty[$row[csf('uom')]]+=$row[csf('REJECT_QNTY')];

		}
	

	
	
	
	
	
//Dyes/Chemical...........................................
//Note: [Report Name: Closing Stock]
	
	$mrr_rate_sql=sql_select("select prod_id, sum(cons_quantity) as cons_quantiy, sum(cons_amount) as cons_amount from inv_transaction where status_active=1 and  COMPANY_ID=1 and is_deleted=0 and item_category in(5,6,7,23) and transaction_type in(1,4,5) group by prod_id");
	$mrr_rate_arr=array();
	foreach($mrr_rate_sql as $row)
	{
		$mrr_rate_arr[$row[csf("prod_id")]]=$row[csf("cons_amount")]/$row[csf("cons_quantiy")];
	}
			
		
		
		$dyes_chemical_stock_sql="select a.expire_date,a.prod_id,
        sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
        (case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
        sum((case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
        (case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
        
        from inv_transaction a , product_details_master b
         where  a.prod_id=b.id and a.item_category in(5,6,7,23) and b.company_id=1 and b.item_category_id in (5,6,7,23)  and a.order_id=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.TRANSACTION_DATE <= '$to_date'
         group by a.expire_date,a.prod_id";
		
		
		$dyesChemicalStockSqlResult = sql_select($dyes_chemical_stock_sql);
			$dyesChemicalStockQty=0;$dyesChemicalStockAmountUsd=0;
			foreach ($dyesChemicalStockSqlResult as $row) 
			{
				$dyesChemicalStockQty+=$row[csf('stock_qty')];
				$dyesChemicalStockAmountUsd+=($row[csf('stock_amount')]);
				//$dyesChemicalStockAmountUsd+=($row[csf('stock_qty')]*$mrr_rate_arr[$row[csf("prod_id")]])/$conversion_rate;
			}
			$dyesChemicalStockAmountUsd=$dyesChemicalStockAmountUsd/$conversion_rate;

//Printing/Chemical...........................................
	$print_chemical_stock_sql="select 
        sum((case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end)-
        (case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end)) stock_qty,
        sum((case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end)-
        (case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end)) stock_amount
        from inv_transaction a where a.item_category=22 and a.status_active=1 and a.is_deleted=0  and a.TRANSACTION_DATE<= '$to_date'";			
		$printChemicalStockSqlResult = sql_select($print_chemical_stock_sql);
			$printChemicalStockQty=0;$printChemicalStockAmountUsd=0;
			foreach ($printChemicalStockSqlResult as $row) 
			{
				$printChemicalStockQty+=$row[csf('stock_qty')];
				$printChemicalStockAmountUsd+=($row[csf('stock_amount')]/$conversion_rate);

			}



$fabric_delivery_sql=" SELECT
         b.uom,
         SUM (d.current_delivery) AS current_delivery
    FROM inv_receive_master a,
         pro_finish_fabric_rcv_dtls b,
         pro_grey_prod_delivery_mst c,
         pro_grey_prod_delivery_dtls d
   WHERE a.company_id in($companyStr)
         AND a.id = b.mst_id
         AND a.id = d.grey_sys_id
         AND b.id = d.sys_dtls_id
         AND c.id = d.mst_id
         AND a.entry_form IN (7, 66)
         AND d.entry_form = 54
         AND a.item_category = 2
         AND a.status_active = 1
         AND c.delevery_date BETWEEN '" . $from_date . "' and '" . $to_date . "'
         AND a.is_deleted = 0
         AND b.uom != 0
         AND d.is_deleted = 0
         AND d.status_active = 1
GROUP BY  b.uom";

		$fabric_delivery_result = sql_select($fabric_delivery_sql);
			foreach ($fabric_delivery_result as $row) 
			{
				$fabric_delivery_qty[$row[csf('uom')]]+=$row[csf('current_delivery')];

			}
			

	$floor_library['cf_1']="(Demra)";
	$floor_library['cf_2']="(Tejgaon)";


	$gmtDataArr=array();
 	foreach($poly_qty_arr as $comapny_id=>$rows){
		$rowspan=count($rows);
		krsort($rows);
		foreach($rows as $floor_id=>$polyQty){
			$key=$comapny_id.'*'.$floor_id;
			$gmtDataArr[$key][garments]=$company_library[$comapny_id].' '.$floor_library[$floor_id];
			$gmtDataArr[$key][sewing_roduction]=$sewing_qty_arr[$comapny_id][$floor_id];
			$gmtDataArr[$key][poly_roduction]=$polyQty;
			$gmtDataArr[$key][sewing_produce]=$sewing_produce_arr[$comapny_id][$floor_id];
			$gmtDataArr[$key][shipment]=$ex_fac_qty_arr[$comapny_id][$floor_id];
        }
	}  
 

 

ob_start();	
?>
<table cellpadding="3" cellspacing="0" border="1" rules="all">
    <tr bgcolor="#DDD"><th>Textile</th><th>Qty/Value</th></tr>	
    <tr><td>Yarn Stock (Kg)</td><td align="right"><? echo number_format($stock);//number_format($yearnStockQty);?></td></tr> 
    <tr><td>Yarn Value (USD)</td><td align="right"><? echo number_format($valueUsd);//number_format($yearnStockVal);?></td></tr>
    <tr><td>Grey Stock (Kg)</td><td align="right"><? echo number_format($greyStockQty);?></td></tr>	
    <tr><td>Dyes/Chemical Stock (Kg)</td><td align="right"><? echo number_format($dyesChemicalStockQty);?></td></tr>	
    <tr><td>Dyes/Chemical Value (USD)</td><td align="right"><? echo number_format($dyesChemicalStockAmountUsd);?></td></tr>	
    <tr><td>Printing Chemical Stock (Kg)</td><td align="right"><? echo number_format($printChemicalStockQty);?></td></tr>	
    <tr><td>Printing Chemical Value (USD)</td><td align="right"><? echo number_format($printChemicalStockAmountUsd);?></td></tr>	
     
    <tr><td>Knitting (Kg)</td><td align="right"><? echo number_format($knite);?></td></tr> 
    <tr><td>Dyeing (Kg)</td><td align="right"><? echo number_format($dyeing);?></td></tr> 
    <tr><td>Finishing (Kg/Yds)</td><td align="right"><? echo number_format($finishQty[12]).' / '.number_format($finishQty[27]);?></td></tr>
    <tr><td>Finishing Reject Qty (Kg/Yds)</td><td align="right"><? echo number_format($finishRejQty[12]).' / '.number_format($finishRejQty[27]);?></td></tr>
    
    <tr><td> Fabric Delivery to Store (Kg/Yds)</td><td align="right"><? echo number_format($fabric_delivery_qty[12]).' / '.number_format($fabric_delivery_qty[27]);?></td></tr>
</table>

<?
$message_1_2=ob_get_contents();
ob_clean();
ob_start();	
?>


<?
$machine_sql= "select id,category_id,machine_group,dia_width,gauge from lib_machine_name where category_id in (1,2) and is_deleted = 0 and status_active = 1";
$machine_sql_result=sql_select($machine_sql);
foreach($machine_sql_result as $row)
{
	if($row[csf(machine_group)] and $row[csf(machine_group)] and $row[csf(gauge)] and $row[csf(category_id)]==1){
		$machineIdArr[$row[csf(category_id)]][$row[csf(id)]]=$row[csf(id)];
	}
	else if($row[csf(category_id)]==2)
	{
		$machineIdArr[$row[csf(category_id)]][$row[csf(id)]]=$row[csf(id)];
	}
	

}
//1=kniting;2=Dyeing;
	   
	/*$sql="select b.machine_no_id, 1 as active_machie_type from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.machine_no_id>0
	 and a.receive_date = '".$previous_date."'  group by b.machine_no_id
	 
	 union all
	 select b.machine_id AS machine_no_id, 1 as active_machie_type  FROM subcon_production_mst a, subcon_production_dtls b where a.id = b.mst_id AND a.product_type = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND a.product_date  = '".$previous_date."'  group by b.machine_id
	
	union all
	select f.machine_id as machine_no_id, 2 as active_machie_type  from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a where f.batch_id=a.id and a.working_company_id=1 and f.process_end_date = '".$previous_date."'   and f.service_source in(1) and a.entry_form=0 and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 group by  f.machine_id  
	";*/
	
	$receive_date= " and a.receive_date between '".$previous_date."' and '".$previous_date."'";
	$receive_date_sub= " and a.product_date between '".$previous_date."' and '".$previous_date."'";
	$sql="Select b.machine_no_id as MACHINE_ID, 1 as ACTIVE_MACHINE_TYPE from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_no_id!=0 $receive_date 
	group by b.machine_no_id
	union all
	
	Select  b.machine_id as MACHINE_ID, 1 as ACTIVE_MACHINE_TYPE from  subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.product_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.machine_id!=0 $receive_date_sub 
	group by b.machine_id
	
	union all
	select f.machine_id as MACHINE_ID, 2 as ACTIVE_MACHINE_TYPE  from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f, pro_batch_create_mst a where f.batch_id=a.id and a.working_company_id=1 and f.process_end_date = '".$previous_date."'   and f.service_source in(1) and a.entry_form=0 and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2 and a.batch_against in(1,11,2,3) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1 group by  f.machine_id 
	";	
	
 //echo $sql;
 
   
$sql_result=sql_select($sql);
foreach($sql_result as $row)
{
	if($row[csf(ACTIVE_MACHINE_TYPE)]==1){$active_kniting_machine_id_arr[$row[csf(MACHINE_ID)]]=$row[csf(MACHINE_ID)];}
	if($row[csf(ACTIVE_MACHINE_TYPE)]==2){$active_dyeing_machine_id_arr[$row[csf(MACHINE_ID)]]=$row[csf(MACHINE_ID)];}

}
	$total_kniting_machine=count($machineIdArr[1]);
	$total_dyeing_machine=count($machineIdArr[2]);
	$total_idle_kniting_machine=$total_kniting_machine-count($active_kniting_machine_id_arr);
	$total_idle_dyeing_machine=$total_dyeing_machine-count($active_dyeing_machine_id_arr);


	
?>

<table cellspacing="0" border="1" rules="all" width="243">
    <tr bgcolor="#CCCCCC">
        <th colspan="4">Machine Summary	</th>
    </tr>		
    <tr>
        <th>Dept</th>
        <th>Total M/C</th>
        <th>Idle M/C</th>
        <th>Idle M/C%</th>
    </tr>
    <tr>
        <td>Knitting</td>
        <td align="right"><? echo $total_kniting_machine;?></td>
        <td align="right" title="TAM:<?= count($active_kniting_machine_id_arr); ?>"><? echo $total_idle_kniting_machine;?></td>
        <td align="right"><? echo number_format(($total_idle_kniting_machine*100)/$total_kniting_machine,2);?></td>
    </tr>
    <tr>
        <td>Dyeing</td>
        <td align="right"><? echo $total_dyeing_machine;?></td>
        <td align="right" title="TAM:<?= count($active_dyeing_machine_id_arr); ?>"><? echo $total_idle_dyeing_machine;?></td>
        <td align="right"><? echo number_format(($total_idle_dyeing_machine*100)/$total_dyeing_machine,2);?></td>
    </tr>
</table>




<style>
	table tr{font-size:12px;}
</style>

<? 
$sewing_produce_arr2=$sewing_produce_arr;

unset($total_idle_kniting_machine);
unset($active_kniting_machine_id_arr);
unset($poly_produce_arr);
unset($sewing_produce_arr);
unset($ex_fac_qty_arr);
unset($all_po_id_arr);




$message_1_3=ob_get_contents();
ob_clean();

$date_from=$previous_date;
 
//echo $message_1;die;

//end 1 part.........................................
ob_start();	
	//company wise...............................................................
	$filename="urmi_daily_mail/tmp/ftml_garments.txt";
	$ftml_garments_data_arr = json_decode(file_get_contents($filename));
	$ftmlTotalArr[12] = $ftml_garments_data_arr->qty->Kg;	
	$ftmlTotalArr[27] = $ftml_garments_data_arr->qty->Yds;	
	$ftmlTotalArr[23] = $ftml_garments_data_arr->qty->Mtr;
	$ftmlTotalArr['amount']=$ftml_garments_data_arr->val->Kg+$ftml_garments_data_arr->val->Yds+$ftml_garments_data_arr->val->Mtr;

	$filename="urmi_daily_mail/tmp/uhm.txt";
	$uhm_data_arr = json_decode(file_get_contents($filename));
	$uhmTotalArr[12] = $uhm_data_arr->qty->Kg;	
	$uhmTotalArr[27] = $uhm_data_arr->qty->Yds;	
	$uhmTotalArr[23] = $uhm_data_arr->qty->Mtr;
	$uhmTotalArr['amount']=$uhm_data_arr->val->Kg+$uhm_data_arr->val->Yds+$uhm_data_arr->val->Mtr;

	$filename="urmi_daily_mail/tmp/attire.txt";
	$attire_data_arr = json_decode(file_get_contents($filename));
	$attTotalArr[12] = $attire_data_arr->qty->Kg;	
	$attTotalArr[27] = $attire_data_arr->qty->Yds;	
	$attTotalArr[23] = $attire_data_arr->qty->Mtr;
	$attTotalArr['amount']=$attire_data_arr->val->Kg+$attire_data_arr->val->Yds+$attire_data_arr->val->Mtr;
	
	$filename="urmi_daily_mail/tmp/urmi.txt";
	$urmi_data_arr = json_decode(file_get_contents($filename));
	$urmiTotalArr[12] = $urmi_data_arr->qty->Kg;	
	$urmiTotalArr[27] = $urmi_data_arr->qty->Yds;	
	$urmiTotalArr[23] = $urmi_data_arr->qty->Mtr;
	$urmiTotalArr['amount']=$urmi_data_arr->val->Kg+$urmi_data_arr->val->Yds+$urmi_data_arr->val->Mtr;
		
	//buyer wise...............................................................
	$buyerTotalStockQtyArr=array();
 
	/*$filename="urmi_daily_mail/tmp/ftml_garments_buyer.txt";
	$ftml_data_arr = json_decode(file_get_contents($filename));
	foreach($ftml_data_arr->qty as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			$buyerTotalStockQtyArr[$buyer_id][$uom]+=$qty;
			$umQtyArr[$uom]+=$qty;
		}
	}
	foreach($ftml_data_arr->val as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			if($uom==12 || $uom==27 || $uom==23){
				$buyerTStockValArr[$buyer_id]+=$qty*1;
			}
		}
	}
 

	$filename="urmi_daily_mail/tmp/uhm_buyer.txt";
	$uhm_data_arr = json_decode(file_get_contents($filename));
	foreach($uhm_data_arr->qty as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			$buyerTotalStockQtyArr[$buyer_id][$uom]+=$qty;
			$umQtyArr[$uom]+=$qty;
		}
	}
	foreach($uhm_data_arr->val as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			if($uom==12 || $uom==27 || $uom==23){
				$buyerTStockValArr[$buyer_id]+=$qty;
			}
		}
	}
 
	$filename="tmp/attire_buyer.txt";
	$attire_data_arr = json_decode(file_get_contents($filename));
	foreach($attire_data_arr->qty as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			$buyerTotalStockQtyArr[$buyer_id][$uom]+=$qty;
			$umQtyArr[$uom]+=$qty;
		}
	}
	foreach($attire_data_arr->val as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			if($uom==12 || $uom==27 || $uom==23){
				$buyerTStockValArr[$buyer_id]+=$qty;
			}
		}
	}
	
	$filename="urmi_daily_mail/tmp/urmi_buyer.txt";
	$urmi_data_arr = json_decode(file_get_contents($filename));
	foreach($urmi_data_arr->qty as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			$buyerTotalStockQtyArr[$buyer_id][$uom]+=$qty;
			$umQtyArr[$uom]+=$qty;
		}
	}
	foreach($urmi_data_arr->val as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			if($uom==12 || $uom==27 || $uom==23){
				$buyerTStockValArr[$buyer_id]+=$qty;
			}
		}
	}*/


	$filename="urmi_daily_mail/tmp/all_buyer.txt";
	$urmi_data_arr = json_decode(file_get_contents($filename));
	foreach($urmi_data_arr->qty as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			$buyerTotalStockQtyArr[$buyer_id][$uom]+=$qty;
			$umQtyArr[$uom]+=$qty;
		}
	}
	foreach($urmi_data_arr->val as $buyer_id => $buyer_data_arr){
		foreach($buyer_data_arr as $uom => $qty){
			if($uom==12 || $uom==27 || $uom==23){
				$buyerTStockValArr[$buyer_id]+=$qty;
			}
		}
	}






$cbo_company_id=implode(',',array_flip($company_library));
	
$poly_line_arr=array();	
$poly_qty_arr=array();		
				
//Production ---------------------------                           
	
	$production_date_con = " and a.production_date between '".$date_from."' and '".$date_from."'";
	$production_sql="SELECT b.BUYER_NAME,a.SEWING_LINE, SUM(d.production_qnty) AS PRODUCTION_QUANTITY
    FROM pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_details_master b,wo_po_break_down c
   WHERE  c.job_no_mst=b.job_no and a.PO_BREAK_DOWN_ID=c.id and a.id=d.MST_ID     
   		 AND a.production_type=11
         AND d.production_type=11
         AND a.id = d.mst_id
         AND a.is_deleted = 0
         AND a.status_active = 1
         AND d.is_deleted = 0
         AND d.status_active = 1
         AND a.serving_company in($cbo_company_id)
        $production_date_con
group by b.BUYER_NAME,a.SEWING_LINE";	
	 // echo $production_sql;die;
	$production_sql_result = sql_select($production_sql);
	foreach($production_sql_result as $rows)
	{
		
		//$buyerTotalStockQtyArr[$rows["BUYER_NAME"]][0]=0;
		
		$poly_qty_arr[$rows["BUYER_NAME"]]+=$rows["PRODUCTION_QUANTITY"];
		$poly_line_arr[$rows["BUYER_NAME"]][$rows["SEWING_LINE"]]=1;
	}
	unset($production_sql_result); 
	// var_dump($poly_qty_arr);die; 
	
  // echo 2;die; 

	
	$sql_subcon="select c.BUYER_BUYER,a.LINE_ID,sum(d.prod_qnty) as good_qnty,sum(c.smv*d.prod_qnty) as produce 
from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d,subcon_ord_dtls c
where a.order_id=c.id and a.production_type=5 and d.production_type=5 and a.id=d.dtls_id and a.status_active=1 and a.is_deleted=0 and a.company_id in($cbo_company_id) $production_date_con group by c.BUYER_BUYER,a.LINE_ID";
	
	$sql_subcon_result = sql_select($sql_subcon, '', '', '', $con);
	foreach($sql_subcon_result as $rows)
	{
		//$buyerTotalStockQtyArr[$rows["BUYER_NAME"]][0]=0;
		
		$poly_qty_arr[$rows["BUYER_NAME"]]+=$rows[csf("good_qnty")];
		$poly_line_arr[$rows["BUYER_NAME"]][$rows[csf("LINE_ID")]]=1;
	}
	unset($production_sql_result); 
    

				
				
	
//textile stock.....................................................................
	
	$cbo_company_id='1'; //1=for textile;
	$cbo_within_group='0';
	$cbo_pocompany_id='0';
	$cbo_buyer_id='0';
	$cbo_year='0';
	$txt_booking_no=	'';
	$txt_booking_id	='';
	$txt_order_no='';
	$hide_order_id='';
	$cbo_store_wise='1';
	$cbo_get_upto='0';
	$txt_days='';
	$cbo_get_upto_qnty='0';
	$txt_qnty='';
	$txt_date_from=$previous_date;
	$txt_date_to=$previous_date;
	$cbo_store_name='5';
	



	$company_name= str_replace("'","",$cbo_company_id);
	$within_group= str_replace("'","",$cbo_within_group);
	$pocompany_id= str_replace("'","",$cbo_pocompany_id);
	$buyer_id= str_replace("'","",$cbo_buyer_id);
	$year_id= str_replace("'","",$cbo_year);
	$booking_no= str_replace("'","",$txt_booking_no);
	$booking_id= str_replace("'","",$txt_booking_id);
	$order_no= str_replace("'","",$txt_order_no);
	$order_id= str_replace("'","",$hide_order_id);
	$cbo_store_wise= str_replace("'","",$cbo_store_wise);
	$cbo_store_name= str_replace("'","",$cbo_store_name);
	$cbo_get_upto= str_replace("'","",$cbo_get_upto);
	$txt_days= str_replace("'","",$txt_days);
	$cbo_get_upto_qnty= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty= str_replace("'","",$txt_qnty);
	$txt_date_from= str_replace("'","",$txt_date_from);
	$txt_date_to= str_replace("'","",$txt_date_to);

	if($within_group==1)
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.po_buyer=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		if($buyer_id>0)
		{
			$buyer_id_cond=" and d.buyer_id=$buyer_id" ;
		}
		else
		{
			$buyer_id_cond="";
		}
	}

	if($pocompany_id==0) $pocompany_cond=""; else $pocompany_cond="and a.company_id='$pocompany_id'";
	$date_cond="";
	if($date_from!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		$date_cond=" and d.program_date='$date_from'";
	}

	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($program_no=="") $program_no_cond=""; else $program_no_cond=" and d.id in ($program_no) ";
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
			$po_cond="and b.id in(".$po_id.")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_cond="and b.po_number like '$po_number'";
		}
	}

	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(a.insert_date)=$year_id"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(a.insert_date,'YYYY')=$year_id"; else $year_search_cond="";
	}


	if ($order_no=='') $order_no_cond=""; else $order_no_cond="and d.job_no_prefix_num='$order_no'";
	$bookingdata_arr=array();
	$bookingIds="";
	$totRows=0;
	$bookingIds_cond="";
	$booking_cond="";

	if ($booking_no!="")
	{
		$booking_no_cond=" and d.sales_booking_no like '%$booking_no%'";
		if($within_group==1 || $year_id!=0 )
		{
			$booking_no_cond.=" and d.sales_booking_no like '%-".substr($year_id, -2)."-%'";
		}

	} else {
		$booking_no_cond="";
	}

	/*if($cbo_store_wise ==1)
	{
		$selectRcvStore = " a.store_id,";
		$selectTransStore = " b.to_store as store_id,";
		$selectTransOutStore = " b.from_store as store_id,";
		$groupByRcvStore = " a.store_id,";
		$groupByTransStore = " b.to_store,";
		$groupByTransOutStore = " b.from_store,";

		if($cbo_store_name)
		{
			$rcvStoreCond = " and e.store_id = $cbo_store_name";
			$TransStoreCond = " and b.to_store = $cbo_store_name";
		}
	}*/
	
	
	if($cbo_store_wise ==1)
	{
		$selectRcvStore_a = " a.store_id,";
		$selectRcvStore_e = " e.store_id,";
		$selectTransStore = " b.to_store as store_id,";
		$selectTransOutStore = " b.from_store as store_id,";
		$groupByRcvStore_a = " a.store_id,";
		$groupByRcvStore_e = " e.store_id,";
		$groupByTransStore = " b.to_store,";
		$groupByTransOutStore = " b.from_store,";

		if($cbo_store_name)
		{
			$rcvStoreCond_e = " and e.store_id = $cbo_store_name";
			$rcvStoreCond_a = " and a.store_id = $cbo_store_name";
			$TransStoreCond = " and b.to_store = $cbo_store_name";
		}
	}
	
	

	if($within_group>0)
	{
		$withinGroupCond = "and d.within_group=$within_group";
	}

	if($txt_date_from != "" && $txt_date_to != "")
	{
		$to_trans_date_cond = " and e.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond2 = " and a.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond3 = " and c.transaction_date <= '".$txt_date_to."'";
		$to_trans_date_cond4 = " and f.transaction_date <= '".$txt_date_to."'";
	}

	$sql = "select d.WITHIN_GROUP,d.BUYER_ID,d.po_buyer,1 as type, min(a.receive_date) as mrr_date, a.company_id, c.po_breakdown_id, b.prod_id, b.body_part_id, b.fabric_description_id, $selectRcvStore_e b.uom, f.color as color_id,b.dia_width_type, b.width, b.gsm, sum(c.quantity) as quantity , sum(e.cons_amount) as amount,0 as is_transfered,0 as from_order_id, a.receive_basis, sum(e.order_amount) as order_amount, e.transaction_date
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, fabric_sales_order_mst d, inv_transaction e, product_details_master f
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.trans_id=e.id and e.prod_id=f.id and a.entry_form=225 and c.entry_form=225 and b.is_sales=1 and c.is_sales=1 and a.company_id = $company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $rcvStoreCond_e $year_search_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis in (10,14) $to_trans_date_cond
	group by d.WITHIN_GROUP,a.company_id,d.BUYER_ID,d.po_buyer,c.po_breakdown_id, b.prod_id,b.body_part_id,b.fabric_description_id, $groupByRcvStore_e b.uom, f.color, b.dia_width_type, b.width, a.item_category, b.gsm, a.receive_basis, e.transaction_date
	union all
	select d.WITHIN_GROUP,d.BUYER_ID,d.po_buyer,2 as type, min(a.transfer_date) as mrr_date, a.company_id,a.to_order_id as po_breakdown_id, b.from_prod_id as prod_id, b.body_part_id,b.feb_description_id as fabric_description_id, $selectTransStore b.uom, f.color as color_id,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity , sum(e.cons_amount) as amount,1 as is_transfered,a.from_order_id , 0 as receive_basis, sum(e.order_amount) as order_amount, e.transaction_date
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, fabric_sales_order_mst d , inv_transaction e, product_details_master f
	where a.id=b.mst_id and a.to_order_id=d.id and b.to_trans_id=e.id and e.prod_id=f.id and a.company_id=$company_name $withinGroupCond $order_no_cond $booking_no_cond $buyer_id_cond $TransStoreCond $year_search_cond and a.entry_form in(230) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_trans_date_cond
	group by d.WITHIN_GROUP,a.company_id,d.BUYER_ID,d.po_buyer,a.to_order_id, b.from_prod_id, b.body_part_id, b.feb_description_id, $groupByTransStore b.uom, f.color,b.dia_width_type, b.dia_width, b.gsm,a.from_order_id, e.transaction_date
	order by uom,po_breakdown_id, prod_id";

	//echo $sql;die;

	$fso_id_wise_buyer_arr=array();
	$nameArray=sql_select($sql);
	$ref_key="";$open=0;
	foreach($nameArray as $row)
	{
		if($row[csf("quantity")] > 0)
		{
			
			$BUYER_ID=($row[csf("WITHIN_GROUP")]==1)?$row[csf("po_buyer")]:$row[csf("BUYER_ID")];
			$fso_id_wise_buyer_arr[$row[csf("po_breakdown_id")]] = $BUYER_ID;

			//echo $row[csf("WITHIN_GROUP")].'='.$row[csf("po_buyer")].'='.$row[csf("BUYER_ID")].'='.$row[csf("po_breakdown_id")]."<br>";


			$fso_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			if($cbo_store_wise ==1)
			{
				$ref_key =$row[csf("company_id")]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")]."**".$row[csf("store_id")];
			}else{
				$ref_key = $row[csf("company_id")]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("body_part_id")]."**".$row[csf("dia_width_type")]."**".$row[csf("color_id")];
			}

			
			
			
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{

				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($row[csf("type")] == 1)
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
					}else{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("order_amount")];
						}else{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_rcv_qnty"] += $row[csf("quantity")];

							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("order_amount")];
						}
						
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["opening"] += $row[csf("quantity")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += 0;
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] = 0;
							
						
					}

					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
					else
					{
						if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
						{
							$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
						}
					}
				}
			}
			else
			{
				if($row[csf("type")] == 1)
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["rcv_amount"] += $row[csf("order_amount")];
				}else{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_qnty"] += $row[csf("quantity")];
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["trans_in_amount"] += $row[csf("order_amount")];
				}

				if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] == "")
				{
					$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
				}
				else
				{
					if($source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] > $row[csf("mrr_date")])
					{
						$source_arr[$row[csf("uom")]][$row[csf("po_breakdown_id")]][$ref_key]["mrr_date"] = $row[csf("mrr_date")];
					}
				}
			}
		}
	}
	

	$fso_id_arr = array_filter($fso_id_arr);
	if(!empty($fso_id_arr))
	{
		$fso_ids = implode(",", array_filter($fso_id_arr));
		$fsoCond = $all_fso_cond = "";
		$fsoCond2 = $all_fso_cond2 = "";
		$fsoCond3 = $all_fso_cond3 = "";
		if($db_type==2 && count($fso_id_arr)>999)
		{
			$fso_id_arr_chunk=array_chunk($fso_id_arr,999) ;
			foreach($fso_id_arr_chunk as $chunk_arr)
			{
				$fsoCond.=" a.id in(".implode(",",$chunk_arr).") or ";
				$fsoCond2.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				$fsoCond3.=" a.from_order_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_fso_cond.=" and (".chop($fsoCond,'or ').")";
			$all_fso_cond2.=" and (".chop($fsoCond2,'or ').")";
			$all_fso_cond3.=" and (".chop($fsoCond3,'or ').")";
		}
		else
		{
			$all_fso_cond=" and a.id in($fso_ids)";
			$all_fso_cond2=" and c.po_breakdown_id in($fso_ids)";
			$all_fso_cond3=" and a.from_order_id in($fso_ids)";
		}

		$fso_ref_sql = sql_select("SELECT a.company_id,a.po_buyer,a.po_company_id,a.within_group, a.id as sales_id, a.job_no,a.season,a.sales_booking_no,a.style_ref_no,a.buyer_id,a.season,a.sales_booking_no,a.booking_type,a.booking_without_order,a.booking_entry_form, b.determination_id, b.gsm_weight,b.width_dia_type, b.dia, b.cons_uom, b.color_id, b.color_type_id,b.finish_qty,b.grey_qty from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id = b.mst_id $all_fso_cond and a.status_active =1 and b.status_active =1");

		$fso_ref_data_arr=array();$fso_ref_data=array();
		$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		foreach($fso_ref_sql as $row)
		{
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['book_qnty'] +=$row[csf('finish_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['fso_qnty'] +=$row[csf('grey_qty')];
			$fso_ref_qnty_type_arr[$row[csf('sales_id')]][$row[csf('determination_id')]][$row[csf('width_dia_type')]][$row[csf('color_id')]][$row[csf('cons_uom')]]['color_type'] .=$row[csf('color_type_id')].",";

			$fso_ref_data[$row[csf('sales_id')]]["within_group"] = $row[csf('within_group')];
			$fso_ref_data[$row[csf('sales_id')]]["po_company_id"] = $row[csf('po_company_id')];

			if($row[csf('within_group')]==1)
			{
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('po_buyer')];
			}else {
				$fso_ref_data[$row[csf('sales_id')]]["po_buyer"] = $row[csf('buyer_id')];
			}

			$fso_ref_data[$row[csf('sales_id')]]["style_ref_no"] = $row[csf('style_ref_no')];
			$fso_ref_data[$row[csf('sales_id')]]["season"] = $row[csf('season')];
			$fso_ref_data[$row[csf('sales_id')]]["job_no"] = $row[csf('job_no')];
			$fso_ref_data[$row[csf('sales_id')]]["sales_booking_no"] = $row[csf('sales_booking_no')];

			if($row[csf('booking_type')] == 4)
			{
				if($row[csf('booking_without_order')] == 1)
				{
					$bookingType = "Sample Without Order";
				}
				else
				{
					$bookingType =  "Sample With Order";
				}
			}
			else
			{
				$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
			}

			$salesTypeData[$row[csf("sales_id")]]['booking_type'] = $bookingType;
		}

		unset($fso_ref_sql);

		$delivery_qnty_sql = sql_select("select b.body_part_id bodypart_id,b.uom,b.width_type,sum(c.quantity) delivery_qnty, sum(a.order_amount) as amount, c.is_sales, c.po_breakdown_id, c.prod_id,d.detarmination_id determination_id,d.gsm,d.dia_width dia, $selectRcvStore_a d.color color_id, a.transaction_date
			from inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, inv_transaction a
			where a.company_id=$company_name and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id $all_fso_cond2 $rcvStoreCond_a and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 $to_trans_date_cond2 group by b.body_part_id,b.uom, b.width_type, c.is_sales, c.po_breakdown_id, c.prod_id,d.detarmination_id,d.gsm, $groupByRcvStore_a d.dia_width,d.color,a.transaction_date");

		foreach ($delivery_qnty_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
					}
					else
					{
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
						$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_qnty"] += $row[csf("delivery_qnty")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise==1)
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["delivery_amount"] += $row[csf("amount")];
				}
				else
				{
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_qnty"] += $row[csf("delivery_qnty")];
					$delivery_qnty_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("determination_id")]][$row[csf("gsm")]][$row[csf("dia")]][$row[csf("bodypart_id")]][$row[csf("width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["delivery_amount"] += $row[csf("amount")];
				}
			}
		}
		unset($delivery_qnty_sql);

		$issue_return_sql = sql_select("select a.company_id, c.po_breakdown_id, b.prod_id, b.body_part_id, b.fabric_description_id,  b.uom, f.color as color_id,b.dia_width_type, b.width, b.gsm, $selectRcvStore_e sum(c.quantity) as quantity , sum(e.order_amount) as amount, e.transaction_date from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, inv_transaction e, product_details_master f where a.id=b.mst_id and b.id=c.dtls_id and c.trans_id=e.id and e.prod_id=f.id and a.entry_form=233 and c.entry_form=233 and b.is_sales=1 and c.is_sales=1 and a.company_id=$company_name $all_fso_cond2 $rcvStoreCond_e $to_trans_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.company_id,c.po_breakdown_id, b.prod_id, b.body_part_id, b.fabric_description_id,b.uom, f.color, $groupByRcvStore_e b.dia_width_type, b.width, a.item_category, b.gsm, e.transaction_date");
	
		foreach ($issue_return_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] += $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_iss_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] += $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$issue_return_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($issue_return_sql);

		$transfered_fabric_sql = sql_select("select a.from_order_id as po_breakdown_id,b.batch_id, b.from_prod_id as prod_id, b.body_part_id, b.feb_description_id as fabric_description_id, $selectTransOutStore b.uom, d.color as color_id,b.fabric_shade,b.dia_width_type, b.dia_width as width, b.gsm, sum(b.transfer_qnty) as quantity, sum(c.order_amount) as amount, c.transaction_date
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
			where a.id=b.mst_id and b.trans_id = c.id and c.prod_id=d.id and c.transaction_type=6 and a.entry_form in(230) and a.company_id = $company_name $all_fso_cond3 $to_trans_date_cond3 and a.status_active =1 and a.is_deleted =0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0
			group by a.from_order_id, b.batch_id, b.from_prod_id, b.body_part_id, b.feb_description_id, $groupByTransOutStore b.uom, d.color, b.fabric_shade, b.dia_width_type, b.dia_width, b.gsm, c.transaction_date");

		foreach ($transfered_fabric_sql as $row)
		{
			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}

				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_trans_out"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];

					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$transfered_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("fabric_description_id")]][$row[csf("gsm")]][$row[csf("width")]][$row[csf("body_part_id")]][$row[csf("dia_width_type")]][$row[csf("color_id")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}
		unset($transfered_fabric_sql);

		$rcv_return_sql = sql_select("select c.po_breakdown_id, c.entry_form , c.quantity, c.is_sales, d.store_id, c.prod_id, e.detarmination_id, e.gsm, e.dia_width, d.body_part_id, d.width_type, e.color,d.uom, f.order_amount as amount, f.transaction_date
			from order_wise_pro_details c, inv_finish_fabric_issue_dtls d, product_details_master e, inv_transaction f
			where c.dtls_id = d.id and d.prod_id = e.id and c.trans_id = f.id and c.entry_form = 287 $all_fso_cond2 $to_trans_date_cond4 and c.is_sales =1 and e.item_category_id =2 and c.status_active =1 and c.is_deleted = 0 and d.status_active =1 and d.is_deleted = 0 and f.status_active =1 and f.is_deleted = 0");

		foreach ($rcv_return_sql as $row)
		{

			if( ($txt_date_from != "" && $txt_date_to != "") )
			{
				if(strtotime($row[csf("transaction_date")]) >= strtotime($txt_date_from) && strtotime($row[csf("transaction_date")]) <= strtotime($txt_date_to))
				{
					if($cbo_store_wise ==1)
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
					}
					else
					{
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
						$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
					}
				}
				else
				{
					if(strtotime($row[csf("transaction_date")]) < strtotime($txt_date_from))
					{
						if($cbo_store_wise ==1)
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["open_balance_amt"] -= $row[csf("amount")];
						}
						else
						{
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_rec_ret_qnty"] += $row[csf("quantity")];
							$opening_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["open_balance_amt"] -= $row[csf("amount")];
						}
					}
				}
			}
			else
			{
				if($cbo_store_wise ==1)
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]][$row[csf("store_id")]]["amount"] += $row[csf("amount")];
				}
				else
				{
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["quantity"] += $row[csf("quantity")];
					$rcv_ret_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]][$row[csf("dia_width")]][$row[csf("body_part_id")]][$row[csf("width_type")]][$row[csf("color")]][$row[csf("uom")]]["amount"] += $row[csf("amount")];
				}
			}
		}

		$prod_id_arr = array_filter($prod_id_arr);
		if(count($prod_id_arr)>0)
		{
			$prod_ids = implode(",", $prod_id_arr);
			$prodCond = $all_prod_id_cond = "";
			if($db_type==2 && count($prod_id_arr)>999)
			{
				$prod_id_arr_chunk=array_chunk($prod_id_arr,999) ;
				foreach($prod_id_arr_chunk as $chunk_arr)
				{
					$prodCond.=" a.id in(".implode(",",$chunk_arr).") or ";
				}

				$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
			}
			else
			{
				$all_prod_id_cond=" and a.id in($prod_ids)";
			}
		}

		$date_array=array();
		$dateRes_date="select c.po_breakdown_id,b.prod_id, a.avg_rate_per_unit, min(b.transaction_date) as min_date, max(b.transaction_date) as max_date 
		from product_details_master a, inv_transaction b,order_wise_pro_details c
		where a.id=b.prod_id and b.id=c.trans_id and b.is_deleted=0 and b.status_active=1 and b.item_category=2 and b.transaction_type=2 and c.trans_type=2
		$all_prod_id_cond
		group by c.po_breakdown_id,b.prod_id, a.avg_rate_per_unit ";
		$result_dateRes_date = sql_select($dateRes_date);
		foreach($result_dateRes_date as $row)
		{
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
			$avg_rate_arr[$row[csf("prod_id")]] = $row[csf("avg_rate_per_unit")];
		}
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array_deter);

			

	$i=1;
	foreach ($source_arr as $uom_id => $uom_data)
	{
		$uom_arr=array();
		$sub_rcv=$sub_trans_in=$sub_iss_ret=$sub_rcv_tot=$sub_rcv_amount=$sub_issue=$sub_issue_return=$sub_rcv_ret=$sub_tran_out=$sub_issue_tot=$sub_issue_amount=$sub_stock_qty=$sub_stock_amount=$sub_opening_qnty=0;
		foreach ($uom_data as $po_breakdown_id => $po_breakdown_data)
		{
			$y=1; $show_row_sub_total = false;
			$opening_balance_qnty=0;
			foreach ($po_breakdown_data as $prod_ref => $row)
			{
				$sales_prod_key_arr=explode("**", $prod_ref);
				$company_id = $sales_prod_key_arr[0];
				$prod_id = $sales_prod_key_arr[1];
				$fabric_description_id = $sales_prod_key_arr[2];
				$gsm = $sales_prod_key_arr[3];
				$width = $sales_prod_key_arr[4];
				$body_part_id = $sales_prod_key_arr[5];
				$dia_width_type = $sales_prod_key_arr[6];
				$color_id  = $sales_prod_key_arr[7];
				$booking_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['book_qnty'];
				$fso_qnty = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['fso_qnty'];
				$color_type_id = $fso_ref_qnty_type_arr[$po_breakdown_id][$fabric_description_id][$dia_width_type][$color_id][$uom_id]['color_type'];

				$daysOnHand = datediff("d",$date_array[$po_breakdown_id][$prod_id]['max_date'],date("Y-m-d"));

				if($cbo_store_wise ==1)
				{
					$store_id  = $sales_prod_key_arr[8];
					$is_transfered  = $sales_prod_key_arr[9];
					$delivery_qnty = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_qnty"];
					$delivery_amount = $delivery_qnty_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["delivery_amount"];

					$transferOutQnty =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
					$transferOutAmount =  $transfered_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

					$rcv_ret_qnty = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
					$rcv_ret_amount = $rcv_ret_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

					$issue_return_qnty = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["quantity"];
					$issue_return_amount = $issue_return_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["amount"];

					$opening_balance_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rcv_qnty"];

					$opening_issue_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_qnty"];

					$opening_trans_out_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_trans_out"];

					$opening_recv_rtn_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_rec_ret_qnty"];

					$open_iss_ret_qnty = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_iss_ret_qnty"];

					$opening_balance_amount = $opening_arr[$po_breakdown_id][$prod_id][$fabric_description_id][$gsm][$width][$body_part_id][$dia_width_type][$color_id][$uom_id][$store_id]["open_balance_amt"];
				}
			

				$total_rcv_qnty = $row['rcv_qnty']+$issue_return_qnty+$row['trans_in_qnty'];

				$rcv_amount = $row['rcv_amount'] + $issue_return_amount + $row['trans_in_amount'];
				
				
				if($total_rcv_qnty > 0)
				{
					$rcv_avg_rate = $rcv_amount/$total_rcv_qnty;
				}else{
					$rcv_avg_rate = 0;
					$rcv_amount=0;
				}


				$total_issue_qnty = $delivery_qnty+$rcv_ret_qnty+ $transferOutQnty;
				$issue_amount = $delivery_amount+$rcv_ret_amount+ $transferOutAmount;
				if($total_issue_qnty>0)
				{
					$issue_avg_rate = $issue_amount/$total_issue_qnty;
				}else{
					$issue_avg_rate = 0;
					$issue_amount=0;
				}

				$opening_bal = ($opening_balance_qnty+$open_iss_ret_qnty)-($opening_issue_qnty+$opening_trans_out_qnty+$opening_recv_rtn_qnty);

				$total_stock_qty =  $opening_bal + ($total_rcv_qnty-$total_issue_qnty);
				//echo $user_id;die;
				if($user_id != 276)
				{
					$total_stock_qty = ($total_stock_qty>0)?$total_stock_qty:0;
				}

				$total_stock_amount = ($opening_balance_amount + $rcv_amount) - $issue_amount;
				//$total_stock_amount = $rcv_amount;

				//echo $total_stock_amount."<br>";
				if($total_stock_qty>0)
				{
					$total_stock_amount = ($total_stock_amount>0)?$total_stock_amount:0;
					$total_stock_avg_rate = $total_stock_amount/$total_stock_qty;
				}
				else
				{
					$total_stock_amount=0;
					$total_stock_avg_rate=0;
				}

				$color_type_ids="";
				$color_type_arr =  array_filter(array_unique(explode(",",chop($color_type_id,","))));
				foreach ($color_type_arr as $val)
				{
					if($color_type_ids == "") $color_type_ids = $color_type[$val]; else $color_type_ids .= ", ". $color_type[$val];
				}

				if ((($cbo_get_upto_qnty == 1 && $total_stock_qty > $txt_qnty) || ($cbo_get_upto_qnty == 2 && $total_stock_qty < $txt_qnty) || ($cbo_get_upto_qnty == 3 && $total_stock_qty >= $txt_qnty) || ($cbo_get_upto_qnty == 4 && $total_stock_qty <= $txt_qnty) || ($cbo_get_upto_qnty == 5 && $total_stock_qty == $txt_qnty) || $cbo_get_upto_qnty == 0) && (($cbo_get_upto == 1 && $daysOnHand > $txt_days) || ($cbo_get_upto == 2 && $daysOnHand < $txt_days) || ($cbo_get_upto == 3 && $daysOnHand >= $txt_days) || ($cbo_get_upto == 4 && $daysOnHand <= $txt_days) || ($cbo_get_upto == 5 && $daysOnHand == $txt_days) || $cbo_get_upto == 0))
				{

						//print_r($fso_id_wise_buyer_arr);die;
						
						$buyerId=$fso_id_wise_buyer_arr[$po_breakdown_id];
						
						if($uom_id==12 || $uom_id==27 || $uom_id==23){
							$textile_stock_qty_arr[$uom_id][1] += $total_stock_qty;
							$textile_stock_amount_arr[$uom_id][1] += $total_stock_amount;

							
							$buyerTotalStockQtyArr[$buyerId][$uom_id]+= $total_stock_qty;
							$buyerTotalStockQtyArr2[$buyerId][$uom_id]+= $total_stock_qty;
							$umQtyArr[$uom_id]+=$total_stock_qty;
							$buyerTStockValArr[$buyerId]+= $total_stock_amount;
							$comTotalStockValArr[$company_id]+=$total_stock_amount;
								
						}
					}
				}

			}
		}
				
				
 //echo "<pre>";print_r($buyerTotalStockQtyArr_2);die;				
 		
				
?>
<!--</table>-->
            
		
<table border="1" rules="all">
	<tr bgcolor="#CCCCCC">
	    <td align="center"><b>SBU</b></td>
	    <td width="60" align="center"><b>Fabric Stock (Kg)</b></td>
	    <td width="60" align="center"><b>Fabric Stock (Yds)</b></td>
	    <td width="60" align="center"><b>Fabric Stock (Mtr)</b></td>
	    <td width="60" align="center"><b>Total Value (USD)</b></td>
	</tr>

	<tr>
	    <td>FTML Textile</td>
	    <td align="right"><? echo number_format($textile_stock_qty_arr[12][1]);?></td>
	    <td align="right"><? echo number_format($textile_stock_qty_arr[27][1]);?></td>
	    <td align="right"><? echo number_format($textile_stock_qty_arr[23][1]);?></td>
	    <td align="right" title="<? echo '12='.$textile_stock_amount_arr[12][1].'; 27='.$textile_stock_amount_arr[27][1].'; 23='.$textile_stock_amount_arr[23][1];?>"><? echo number_format(($textile_stock_amount_arr[12][1]+$textile_stock_amount_arr[27][1]+$textile_stock_amount_arr[23][1]));?></td>
	</tr>
	<tr>
		<td>FTML Garments</td>
        <td align="right"><? echo number_format($ftmlTotalArr[12]);?></td>
		<td align="right"><? echo number_format($ftmlTotalArr[27]);?></td>
		<td align="right"><? echo number_format($ftmlTotalArr[23]);?></td>
        <td align="right"><? echo number_format($ftmlTotalArr['amount']);?></td>
	</tr>
	<tr>
		<td>UHM</td>
		<td align="right"><? echo number_format($uhmTotalArr[12]);?></td>
		<td align="right"><? echo number_format($uhmTotalArr[27]);?></td>
		<td align="right"><? echo number_format($uhmTotalArr[23]);?></td>
        <td align="right"><? echo number_format($uhmTotalArr['amount']);?></td>
	</tr>
	<tr>
		<td>ATTIRE (Tejgaon)</td>
		<td align="right"><? echo number_format($attTotalArr[12]);?></td>
		<td align="right"><? echo number_format($attTotalArr[27]);?></td>
		<td align="right"><? echo number_format($attTotalArr[23]);?></td>
        <td align="right"><? echo number_format($attTotalArr['amount']);?></td>
	</tr>
	<tr>
		<td>URMI (Demra)</td>
		<td align="right"><? echo number_format($urmiTotalArr[12]);?></td>
		<td align="right"><? echo number_format($urmiTotalArr[27]);?></td>
		<td align="right"><? echo number_format($urmiTotalArr[23]);?></td>
        <td align="right"><? echo number_format($urmiTotalArr['amount']);?></td>
	</tr>
    <tr bgcolor="#CCCCCC">
        <td align="right"><b>Total</b></td>
        <td align="right"><b><? echo number_format($ftmlTotalArr[12]+$uhmTotalArr[12]+$urmiTotalArr[12]+$attTotalArr[12]+$textile_stock_qty_arr[12][1]);?></b></td>
        <td align="right"><b><? echo number_format($ftmlTotalArr[27]+$uhmTotalArr[27]+$urmiTotalArr[27]+$attTotalArr[27]+$textile_stock_qty_arr[27][1]);?></b></td>
        <td align="right"><b><? echo number_format($ftmlTotalArr[23]+$uhmTotalArr[23]+$urmiTotalArr[23]+$attTotalArr[23]+$textile_stock_qty_arr[23][1]);?></b></td>
        <td align="right"><b><? echo number_format($ftmlTotalArr['amount']+$uhmTotalArr['amount']+$urmiTotalArr['amount']+$attTotalArr['amount']+($textile_stock_amount_arr[12][1]+$textile_stock_amount_arr[27][1]+$textile_stock_amount_arr[23][1]));?></b></td>
    </tr>
</table>

<br />

<table cellspacing="0" border="1" rules="all">
    <tr>
        <td colspan="7" align="center"><b>Buyer wise Segregation</b></td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <td align="center"><b>Buyer</b></td>
        <td align="center" width="60"><b>Production Lines</b></td>
        <td align="center" width="60"><b>Production in Poly (Pcs)</b></td>
        <td align="center" width="60"><b>Fabric Stock (Kg)</b></td>
        <td align="center" width="60"><b>Fabric Stock (Yds)</b></td>
        <td align="center" width="60"><b>Fabric Stock (Mtr)</b></td>
        <td align="center" width="60"><b>Total Fabric Value (USD)</b></td>
    </tr>
    <? 
	
	
	$poly_line_total=0;
	foreach($buyerTotalStockQtyArr as $buyer_id=>$uomRow){
		if(
		(round(array_sum($poly_line_arr[$buyer_id]))>1) || 
		(round(array_sum($poly_qty_arr[$buyer_id]))>1) || 
		(round($uomRow[12])>1) || 
		(round($uomRow[27])>1) || 
		(round($uomRow[23])>1) 
		){

		$poly_line_total+=array_sum($poly_line_arr[$buyer_id]);
	?>
    <tr>
        <td><? echo $buyer_arr[$buyer_id];?></td>
        <td align="right"><? echo array_sum($poly_line_arr[$buyer_id]);?></td>
        <td align="right"><?=$poly_qty_arr[$buyer_id];?></td>
        <td align="right" title="Textile: <?=$buyerTotalStockQtyArr2[$buyer_id][12];?>"><? echo number_format($uomRow[12]);?></td>
        <td align="right" title="Textile: <?=$buyerTotalStockQtyArr2[$buyer_id][27];?>"><? echo number_format($uomRow[27]);?></td>
        <td align="right" title="Textile: <?=$buyerTotalStockQtyArr2[$buyer_id][23];?>"><? echo number_format($uomRow[23]);?></td>
        <td align="right">
		<? echo number_format($buyerTStockValArr[$buyer_id]);?>
        </td>
    </tr>
    <? } }?>
    <tr bgcolor="#CCCCCC">
        <td align="right"><b>Total</b></td>
        <td align="right"><? echo $poly_line_total;?></td>
        <td align="right"><b><? if(round(array_sum($poly_qty_arr))>=1) echo number_format(array_sum($poly_qty_arr));?></b></td>
        <td align="right"><b><? if( round($umQtyArr[12])>=1) echo number_format($umQtyArr[12]);?></b></td>
        <td align="right"><b><? if( round($umQtyArr[27])>=1) echo number_format($umQtyArr[27]);?></b></td>
        <td align="right"><b><? if( round($umQtyArr[23])>=1) echo number_format($umQtyArr[23]);?></b></td>
        <td align="right"><b><?= number_format(array_sum($buyerTStockValArr));?></b></td>
    </tr>
</table>






<?

unset($poly_line_arr);
unset($poly_qty_arr);
unset($buyerTStockValArr);
unset($uomRow);
unset($umQtyArr);
unset($comTotalStockQtyArr);


$message_2=ob_get_contents();
ob_clean();


//print_r($otherStorArr);

//echo $message_2;die;

//end 2 nd part..................................
ob_start();	


	$from_date=$previous_date;
	$to_date=$previous_date;
	$poly_produce_arr2=array();
	$sewing_SMV_svailable_arr=array();


$production_date_con = " and a.production_date between '".$previous_date."' and '".$previous_date."'";
		$sql_query="select  a.serving_company, a.location, a.floor_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name as buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, a.po_break_down_id, e.item_number_id, c.po_number as po_number,
			sum(d.production_qnty) as good_qnty 
			from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
			where a.production_type=11 and d.production_type=11 and a.id=d.mst_id and a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,2,3)  and c.is_deleted=0  and e.status_active in(1,2,3) and e.is_deleted=0 and e.id=d.color_size_break_down_id and e.po_break_down_id=c.id and a.po_break_down_id=e.po_break_down_id AND a.serving_company in(".implode(',',array_keys($company_library)).")  $production_date_con
			group by a.serving_company, a.location, a.floor_id, a.po_break_down_id, a.prod_reso_allo, a.production_date, a.sewing_line, b.buyer_name, b.style_ref_no, b.set_break_down,b.set_smv, e.item_number_id, c.po_number order by a.location, a.floor_id, a.po_break_down_id";
	//echo $sql_query;die;
		$production_sql_result=sql_select($sql_query);
		$production_po_data_arr=array();$all_po_id_arr=array();	$all_style_arr=array();$com_style_po_id_arr=array();		 
		foreach($production_sql_result as $val)
		{
			$production_po_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]][$val[csf('po_break_down_id')]]+=$val[csf('good_qnty')];
			
			if($production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']!="")
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id'].="****".$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
			}
			else
			{
				$production_data_arr[$val[csf('serving_company')]][$val[csf('floor_id')]][$val[csf('sewing_line')]]['item_number_id']=$val[csf('po_break_down_id')]."**".$val[csf('item_number_id')]; 
			}
			
			$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			$all_style_arr[$val[csf('serving_company')]][$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];			
			$com_style_po_id_arr[$val[csf('serving_company')]][$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
			
		}


		$all_po_ids=implode(",", array_unique($all_po_id_arr)); 
		
		$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no  and b.id in($all_po_ids) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)";
		$resultItem=sql_select($sql_item);
		$item_smv_array=array();
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
		}
		unset($resultItem);

		foreach($company_library as $companyId=>$compname)/// Total Activities
		{
		foreach($production_data_arr[$companyId] as $f_id=>$fname)
		{
			ksort($fname);
			foreach($fname as $l_id=>$ldata)
			{
				/*if($f_id==20){$rows[csf("floor_id")]=24;}
				else{$rows[csf("floor_id")]=$f_id;}
				
				if($companyId==4){$newCompany=2;$rows[csf("floor_id")]=0;}
				else{$newCompany=$companyId;}
				
				if($newCompany==3 || $newCompany==2){$rows[csf("floor_id")]=0;}*/

				
				if(($f_id==14 || $f_id==20 || $f_id==24 || $f_id==37) and $companyId==1){$newCompany='unite_1';}
				else if(($f_id==7 || $f_id==15 || $f_id==19) and $companyId==1){$newCompany='unite_2';}
				else{$newCompany=$companyId;}
				
				
				//$poly_qty_arr[$newCompany][$rows[csf("floor_id")]]+=array_sum($production_po_data_arr[$companyId][$f_id][$l_id]);
				$germents_item=array_unique(explode('****',$ldata['item_number_id']));
				foreach($germents_item as $g_val)
				{
					$po_garment_item=explode('**',$g_val);
					$poly_produce_arr2[$newCompany]+=($production_po_data_arr[$companyId][$f_id][$l_id][$po_garment_item[0]]*$item_smv_array[$po_garment_item[0]][$po_garment_item[1]]);
				}
				
			}
		}
		}
		
// print_r($poly_produce_arr2[3]);die;
	



//......production.......................
$companyStr = implode(',',array_keys($company_library));


	$prod_reso_allo_arr = return_library_array("select company_name,auto_update from variable_settings_production where variable_list=23 and company_name in($companyStr) and is_deleted=0 and status_active=1","company_name","auto_update");
	//$smv_source_arr = return_library_array("select company_name,smv_source from variable_settings_production where company_name in($companyStr) and variable_list=25 and status_active=1 and is_deleted=0","company_name","smv_source");


//------------------------------------
	$company_cond=" and a.serving_company in(".$companyStr.")";
	if($from_date!="" && $to_date!=""){$sql_cond=" and a.production_date between '$from_date' and '$from_date'";}
	
		
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




$company_cond=" and a.company_id in(".$companyStr.")";
	
 $sql_subcon="select  a.company_id,a.production_type, a.floor_id, a.production_date, a.line_id as sewing_line, a.order_id as po_break_down_id, a.gmts_item_id as item_number_id,sum(d.prod_qnty) as good_qnty,sum(c.smv*d.prod_qnty) as produce,c.smv,c.cust_style_ref as style_ref_no
				 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
				where a.production_type in(2,5) and d.production_type in(2,5) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $sql_cond $company_cond
				group by a.company_id,a.production_type, a.floor_id, a.order_id, a.production_date, a.line_id , a.gmts_item_id,c.smv,c.cust_style_ref order by a.floor_id, a.order_id";
				  //echo $sql_subcon;die;
	$sql_subcon_result=sql_select($sql_subcon);	
	foreach($sql_subcon_result as $val)
	{
		
		$item_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_number_id')]]=$val[csf('smv')];
		
		$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')].'**'.$val[csf('po_break_down_id')].'**'.$val[csf('item_number_id')].'**'.$val[csf('production_date')].'**'.$val[csf('company_id')].'**0';
		
		if($val[csf('production_type')]==2){//sub sewing
			$sewing_production_po_data_arr[$val[csf('company_id')]][$key]+=$val[csf('good_qnty')];		
		}
		else if($val[csf('production_type')]==5){//sub poly
			$poly_production_po_data_arr[$val[csf('company_id')]][$key]+=$val[csf('good_qnty')];
		}
		
		$all_po_id_arr[$val[csf('company_id')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
		$all_style_arr[$val[csf('company_id')]][$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
		$com_style_po_id_arr[$val[csf('company_id')]][$val[csf('style_ref_no')]][$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
	}
	unset($sql_subcon_result);
	
//--------------------------	
	
	

if($from_date!="" && $to_date!=""){$sql_cond=" and pr_date between '$from_date' and '$to_date'";}
$sql="select a.company_id,a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id and a.company_id in($companyStr) and a.is_deleted=0 and b.is_deleted=0 $sql_cond order by a.company_id";
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
	
	
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	if($smv_source==3)
	{
		$gsdSql="select a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,A.TOTAL_SMV,A.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE  from PPL_GSD_ENTRY_MST a where a.APPLICABLE_PERIOD <= '$from_date' and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and a.BULLETIN_TYPE in(4)  ".where_con_using_array($all_style_arr[$company_id],1,'a.STYLE_REF')." and a.APPROVED=1  
			group by a.id,a.APPLICABLE_PERIOD,a.BULLETIN_TYPE,a.TOTAL_SMV,a.STYLE_REF,a.GMTS_ITEM_ID,a.COLOR_TYPE
			 ORDER BY a.GMTS_ITEM_ID ,a.APPLICABLE_PERIOD  DESC , a.id DESC";
			     //echo $gsdSql.'======'; 
			$gsdSqlResult = sql_select($gsdSql); 
			$item_smv_array=array();$item_smv_array2=array();
			foreach($gsdSqlResult as $rows)
			{
				foreach($com_style_po_id_arr[$company_id][$rows[STYLE_REF]] as $po_id)
				{
					if($item_smv_array[$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]==''){
						$item_smv_array[$po_id][$rows[COLOR_TYPE]][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
					}
					if($item_smv_array2[$po_id][$rows[GMTS_ITEM_ID]]==''){
						$item_smv_array2[$po_id][$rows[GMTS_ITEM_ID]]=$rows[TOTAL_SMV];
					}
					
					
				}
			}
			unset($gsdSqlResult);
		
		
		
		
	}
	else
	{
		
		
		$sql_item="select b.id,c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) $where_cond2";
		//echo $sql_item;die;
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
	


	
	//Sewing...............................................
	$tempSewingDateLine=array();
	foreach($sewing_production_po_data_arr[$company_id] as $key=>$good_qnty){
		list($floor_id,$line_id,$po_id,$item_id,$production_date,$color_type_id)=explode('**',$key);
			//$produce_minit_arr[$production_date]+=$good_qnty*$item_smv_array[$po_id][$item_id];
			
		if(($floor_id==14 || $floor_id==20 || $floor_id==24 || $floor_id==37) and $company_id==1){$company_id_str='unite_1';}
		else if(($floor_id==7 || $floor_id==15 || $floor_id==19) and $company_id==1){$company_id_str='unite_2';}
		else{$company_id_str=$company_id;}
			
			$sewing_achieved_arr[$company_id_str]+=$good_qnty;
			$color_type_smv=($item_smv_array[$po_id][$color_type_id][$item_id])?$item_smv_array[$po_id][$color_type_id][$item_id]:$item_smv_array2[$po_id][$item_id];
			
			$sewing_produce_arr[$company_id_str]+=$color_type_smv*$good_qnty;
			
			if($tempSewingDateLine[$company_id][$floor_id.$line_id.$production_date] != 1){
				$tempSewingDateLine[$company_id][$floor_id.$line_id.$production_date]=1;
				
				
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
	$tempPolyDateLine=array();
	foreach($poly_production_po_data_arr[$company_id] as $key=>$good_qnty){
		list($floor_id,$line_id,$po_id,$item_id,$production_date,$color_type_id)=explode('**',$key);
	
		if(($floor_id==14 || $floor_id==20 || $floor_id==24 || $floor_id==37) and $company_id==1){$company_id_str='unite_1';}
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
		
		$color_type_smv=($item_smv_array[$po_id][$color_type_id][$item_id])?$item_smv_array[$po_id][$color_type_id][$item_id]:$item_smv_array2[$po_id][$item_id];
		
		$poly_produce_arr[$company_id_str]+=$color_type_smv*$good_qnty;
		
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
		
}
//echo round((memory_get_usage()/1024)/1024).' Kb';

$ftml_unite_1=($poly_achieved_arr['unite_1']*100)/$poly_efficiency_min_arr['unite_1'];
if(is_infinite($ftml_unite_1) || is_nan($ftml_unite_1)){$ftml_unite_1=0;}
$ftml_unite_2=($sewing_achieved_arr['unite_2']*100)/$efficiency_min_arr['unite_2'];
if(is_infinite($ftml_unite_2) || is_nan($ftml_unite_2)){$ftml_unite_2=0;}
$urmi=(($poly_achieved_arr[2])*100)/($poly_efficiency_min_arr[2]);

$attr=(($poly_achieved_arr[4])*100)/($poly_efficiency_min_arr[4]);


if(is_infinite($urmi) || is_nan($urmi)){$urmi=0;}
$uhm=($poly_achieved_arr[3]*100)/$poly_efficiency_min_arr[3];
if(is_infinite($uhm) || is_nan($uhm)){$uhm=0;}


$total_target=$poly_efficiency_min_arr['unite_1']+$efficiency_min_arr['unite_2']+($poly_efficiency_min_arr[2]+$poly_efficiency_min_arr[4])+$poly_efficiency_min_arr[3];
$total_achive=$poly_achieved_arr['unite_1']+$sewing_achieved_arr['unite_2']+($poly_achieved_arr[2]+$poly_achieved_arr[4])+$poly_achieved_arr[3];


//------------
$sewing_achieve_per_ftml_unite_2=($sewing_produce_arr['unite_2']/$sewing_SMV_svailable_arr['unite_2'])*100;
$sewing_achieve_per_ftml_unite_1=($sewing_produce_arr['unite_1']/$sewing_SMV_svailable_arr['unite_1'])*100;

$achieve_per_urmi=($sewing_produce_arr2[2]['cf_1']/($sewing_SMV_svailable_arr[2]))*100;
$achieve_per_attr=($sewing_produce_arr2[4]['cf_2']/($sewing_SMV_svailable_arr[4]))*100;

$achieve_per_uhm=($sewing_produce_arr2[3][0]/$sewing_SMV_svailable_arr[3])*100;

$sewing_achieve_per_ftml_unite_2=($sewing_produce_arr2[1][19]/$sewing_SMV_svailable_arr['unite_2'])*100;
$sewing_achieve_per_ftml_unite_1=($sewing_produce_arr2[1][24]/$sewing_SMV_svailable_arr['unite_1'])*100;

$gmtDataArr['1*24'][sew_eff]=$sewing_achieve_per_ftml_unite_1;
$gmtDataArr['1*19'][sew_eff]=$sewing_achieve_per_ftml_unite_2;
$gmtDataArr['2*cf_1'][sew_eff]=$achieve_per_urmi;
$gmtDataArr['3*0'][sew_eff]=$achieve_per_uhm;
$gmtDataArr['4*cf_2'][sew_eff]=$achieve_per_attr;

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
<style>
	table tr{font-size:12px;}
</style>
<?	

$message_3=ob_get_contents();
ob_clean();

//end 3rd part.....................................

//start 1st part.....................................
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
	foreach($gmtDataArr as $comapny_id=>$rows){
		
		$totalPolyQty+=$rows[poly_roduction];
		$totalExFactoryQty+=$rows[shipment];	
		$totalProduceQty+=$rows[sewing_produce];
		$totalSewingQty+=$rows[sewing_roduction];
		$totalSew_eff+=$rows[sew_eff];
	
	?>
    <tr>
        <td><?=$rows[garments];?></td>
        <td align="right"><?=number_format($rows[sewing_roduction]);?></td>
        <td align="right"><?=number_format($rows[poly_roduction]);?></td>
        <td align="right"><?=number_format($rows[sewing_produce]);?></td>
        <?php /*?><td align="right" title="<?=$comapny_id;?>"><?=number_format($rows[sew_eff]);?></td><?php */?>
        <td align="right"><?=number_format($rows[shipment]);?></td>
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

<?
$message_1_1=ob_get_contents();
ob_clean();
//end 1st part.....................................





	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id and a.company_id in(".implode(',',array_keys($company_library)).")  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 
	
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		$emailArr[$row[csf('email_address')]]=$row[csf('email_address')];
	}

	$to=implode(',',$emailArr);
	//$subject="Daily Report of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
	$subject="Daily Report V-3";
	$message="";
	$header=mailHeader();
	
	$message=$message_1_1.'<br>'.$message_3.'<br>'.$message_1_2.'<br>'.$message_1_3.'<br>'.$message_2;

	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	
	 
    if($_REQUEST['isview']==1){
        echo $to.$message;
    }
    else{
        //$to="sarker@logicsoftbd.com,reza@logicsoftbd.com";
		
		//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
    }

 
 
	
	//echo round(memory_get_usage(true)/1048576).' M';
	unset($message);
	die;
	
	
		
?>









