<?
require_once('../../includes/common.php');
date_default_timezone_set("Asia/Dhaka");

$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
//$previous_date='05-Sep-2022';$current_date='05-Sep-2022';


 $buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
 $company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0 ", "id", "company_short_name");

//company wise...............................................................
$filename="reference_wise_finish_stock_data/tmp/ftml_garments.txt";
$ftml_garments_data_arr = json_decode(file_get_contents($filename));
$ftmlTotalArr[12] = $ftml_garments_data_arr->qty->Kg;	
$ftmlTotalArr[27] = $ftml_garments_data_arr->qty->Yds;	
$ftmlTotalArr[23] = $ftml_garments_data_arr->qty->Mtr;
$ftmlTotalArr['amount']=$ftml_garments_data_arr->val->Kg+$ftml_garments_data_arr->val->Yds+$ftml_garments_data_arr->val->Mtr;

$filename="reference_wise_finish_stock_data/tmp/uhm.txt";
$uhm_data_arr = json_decode(file_get_contents($filename));
$uhmTotalArr[12] = $uhm_data_arr->qty->Kg;	
$uhmTotalArr[27] = $uhm_data_arr->qty->Yds;	
$uhmTotalArr[23] = $uhm_data_arr->qty->Mtr;
$uhmTotalArr['amount']=$uhm_data_arr->val->Kg+$uhm_data_arr->val->Yds+$uhm_data_arr->val->Mtr;

$filename="reference_wise_finish_stock_data/tmp/attire.txt";
$attire_data_arr = json_decode(file_get_contents($filename));
$attTotalArr[12] = $attire_data_arr->qty->Kg;	
$attTotalArr[27] = $attire_data_arr->qty->Yds;	
$attTotalArr[23] = $attire_data_arr->qty->Mtr;
$attTotalArr['amount']=$attire_data_arr->val->Kg+$attire_data_arr->val->Yds+$attire_data_arr->val->Mtr;

//echo $attTotalArr[12];die;

$filename="reference_wise_finish_stock_data/tmp/urmi.txt";
$urmi_data_arr = json_decode(file_get_contents($filename));
$urmiTotalArr[12] = $urmi_data_arr->qty->Kg;	
$urmiTotalArr[27] = $urmi_data_arr->qty->Yds;	
$urmiTotalArr[23] = $urmi_data_arr->qty->Mtr;
$urmiTotalArr['amount']=$urmi_data_arr->val->Kg+$urmi_data_arr->val->Yds+$urmi_data_arr->val->Mtr;
	


//buyer wise...............................................................
$buyerTotalStockQtyArr=array();

$filename="reference_wise_finish_stock_data/tmp/all_buyer.txt";
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
    $date_from = $previous_date;
	$production_date_con = " and a.production_date between '".$date_from."' and '".$date_from."'";
	$production_sql="SELECT b.BUYER_NAME,a.SEWING_LINE, SUM(d.production_qnty) AS PRODUCTION_QUANTITY
    FROM pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_details_master b,wo_po_break_down c
   WHERE  c.job_no_mst=b.job_no and a.PO_BREAK_DOWN_ID=c.id and a.id=d.MST_ID     
   		 AND a.production_type=11
         AND d.production_type=11
         AND a.id = d.mst_id
         AND a.is_deleted = 0  AND b.is_deleted = 0 AND b.status_active = 1  AND c.is_deleted = 0 AND c.status_active = 1
         AND a.status_active = 1
         AND d.is_deleted = 0
         AND d.status_active = 1
         AND a.serving_company in($cbo_company_id)
        $production_date_con
group by b.BUYER_NAME,a.SEWING_LINE";	
	 //echo $production_sql;die;
	$production_sql_result = sql_select($production_sql);
	foreach($production_sql_result as $rows)
	{
		
		//$buyerTotalStockQtyArr[$rows["BUYER_NAME"]][0]=0;
		if($rows["PRODUCTION_QUANTITY"]){
			$poly_qty_arr[$rows["BUYER_NAME"]]+=$rows["PRODUCTION_QUANTITY"];
			$poly_line_arr[$rows["BUYER_NAME"]][$rows["SEWING_LINE"]]=1;
		}

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
		if($rows[csf("good_qnty")]){
			$poly_qty_arr[$rows["BUYER_NAME"]]+=$rows[csf("good_qnty")];
			$poly_line_arr[$rows["BUYER_NAME"]][$rows[csf("LINE_ID")]]=1;
		}

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

					// if($fso_id_wise_buyer_arr[$po_breakdown_id]==''){
					// 	echo $po_breakdown_id.',';
					// }
					
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


//echo "<pre>";
//print_r($buyerTotalStockQtyArr2);die;



//die;


ob_start();
?>



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
        <td align="right">
            <? echo number_format($textile_stock_qty_arr[12][1]);?>
        </td>
        <td align="right">
            <? echo number_format($textile_stock_qty_arr[27][1]);?>
        </td>
        <td align="right">
            <? echo number_format($textile_stock_qty_arr[23][1]);?>
        </td>
        <td align="right"
            title="<? echo '12='.$textile_stock_amount_arr[12][1].'; 27='.$textile_stock_amount_arr[27][1].'; 23='.$textile_stock_amount_arr[23][1];?>">
            <? echo number_format(($textile_stock_amount_arr[12][1]+$textile_stock_amount_arr[27][1]+$textile_stock_amount_arr[23][1]));?>
        </td>
    </tr>
    <tr>
        <td>FTML Garments</td>
        <td align="right">
            <? echo number_format($ftmlTotalArr[12]);?>
        </td>
        <td align="right">
            <? echo number_format($ftmlTotalArr[27]);?>
        </td>
        <td align="right">
            <? echo number_format($ftmlTotalArr[23]);?>
        </td>
        <td align="right">
            <? echo number_format($ftmlTotalArr['amount']);?>
        </td>
    </tr>
    <tr>
        <td>UHM</td>
        <td align="right">
            <? echo number_format($uhmTotalArr[12]);?>
        </td>
        <td align="right">
            <? echo number_format($uhmTotalArr[27]);?>
        </td>
        <td align="right">
            <? echo number_format($uhmTotalArr[23]);?>
        </td>
        <td align="right">
            <? echo number_format($uhmTotalArr['amount']);?>
        </td>
    </tr>
    <tr>
        <td>ATTIRE (Tejgaon)</td>
        <td align="right">
            <? echo number_format($attTotalArr[12]);?>
        </td>
        <td align="right">
            <? echo number_format($attTotalArr[27]);?>
        </td>
        <td align="right">
            <? echo number_format($attTotalArr[23]);?>
        </td>
        <td align="right">
            <? echo number_format($attTotalArr['amount']);?>
        </td>
    </tr>
    <tr>
        <td>URMI (Demra)</td>
        <td align="right">
            <? echo number_format($urmiTotalArr[12]);?>
        </td>
        <td align="right">
            <? echo number_format($urmiTotalArr[27]);?>
        </td>
        <td align="right">
            <? echo number_format($urmiTotalArr[23]);?>
        </td>
        <td align="right">
            <? echo number_format($urmiTotalArr['amount']);?>
        </td>
    </tr>
    <tr bgcolor="#CCCCCC">
        <td align="right"><b>Total</b></td>
        <td align="right">
            <b>
                <? echo number_format($ftmlTotalArr[12]+$uhmTotalArr[12]+$urmiTotalArr[12]+$attTotalArr[12]+$textile_stock_qty_arr[12][1]);?>
            </b>
        </td>
        <td align="right">
            <b>
                <? echo number_format($ftmlTotalArr[27]+$uhmTotalArr[27]+$urmiTotalArr[27]+$attTotalArr[27]+$textile_stock_qty_arr[27][1]);?>
            </b>
        </td>
        <td align="right">
            <b>
                <? echo number_format($ftmlTotalArr[23]+$uhmTotalArr[23]+$urmiTotalArr[23]+$attTotalArr[23]+$textile_stock_qty_arr[23][1]);?>
            </b>
        </td>
        <td align="right">
            <b>
                <? echo number_format($ftmlTotalArr['amount']+$uhmTotalArr['amount']+$urmiTotalArr['amount']+$attTotalArr['amount']+($textile_stock_amount_arr[12][1]+$textile_stock_amount_arr[27][1]+$textile_stock_amount_arr[23][1]));?>
            </b>
        </td>
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
        <td>
            <? echo $buyer_arr[$buyer_id] ?>
        </td>
        <td align="right">
            <? echo array_sum($poly_line_arr[$buyer_id]);?>
        </td>
        <td align="right"><?=$poly_qty_arr[$buyer_id];?></td>
        <td align="right" title="Textile: <?=$buyerTotalStockQtyArr2[$buyer_id][12];?>">
            <? echo number_format($uomRow[12]);?>
        </td>
        <td align="right" title="Textile: <?=$buyerTotalStockQtyArr2[$buyer_id][27];?>">
            <? echo number_format($uomRow[27]);?>
        </td>
        <td align="right" title="Textile: <?=$buyerTotalStockQtyArr2[$buyer_id][23];?>">
            <? echo number_format($uomRow[23]);?>
        </td>
        <td align="right">
            <? echo number_format($buyerTStockValArr[$buyer_id]);?>
        </td>
    </tr>
    <? } }?>
    <tr bgcolor="#CCCCCC">
        <td align="right"><b>Total</b></td>
        <td align="right">
            <? echo $poly_line_total;?>
        </td>
        <td align="right"><b>
                <? if(round(array_sum($poly_qty_arr))>=1) echo number_format(array_sum($poly_qty_arr));?>
            </b></td>
        <td align="right"><b>
                <? if( round($umQtyArr[12])>=1) echo number_format($umQtyArr[12]);?>
            </b></td>
        <td align="right"><b>
                <? if( round($umQtyArr[27])>=1) echo number_format($umQtyArr[27]);?>
            </b></td>
        <td align="right"><b>
                <? if( round($umQtyArr[23])>=1) echo number_format($umQtyArr[23]);?>
            </b></td>
        <td align="right"><b><?= number_format(array_sum($buyerTStockValArr));?></b></td>
    </tr>
</table>

<?


	$html=ob_get_contents();
	ob_clean();
	$file_name = 'html/textile_rms_buyer_segregation.html';
	$create_file = fopen($file_name, 'w');	
	fwrite($create_file,$html);
	echo $html;

?>