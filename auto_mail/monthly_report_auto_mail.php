<?php
date_default_timezone_set("Asia/Dhaka");

// require_once('../includes/common.php');
// require '../vendor/autoload.php';
// require_once('setting/mail_setting.php');


require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');
//require '../vendor/autoload.php';
require_once('setting/mail_setting.php');

if(date('d')!=4){exit('This mail will be send only date of 4 every month');}

$file = 'locl_monthly.txt';
$yesterday = file_get_contents($file);
$today = date("d-m-Y",time());
file_put_contents($file, $today);
if($yesterday==$today){exit();}

 
$customCompany=array(1=>'FTML',3=>'UHM',2=>'URMI',4=>'ATTIR');//
$DataArr[1]=array(24=>'(Unit-1)',19=>'(Unit-2)');
$DataArr[3]=array(0=>'');
$DataArr[2]=array(0=>'(Demra)');
$DataArr[4]=array(0=>'(Tejgaon)');

 
$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name",$con);
$floor_library = return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");

 $conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","is_deleted=0 and status_active=1 and id=(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)","",$con);
	
	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):'';
	}
	
	if($db_type==0)
	{
		$previous_date= date('Y-m-d', strtotime("first day of -1 month"));
		$current_date = date('Y-m-d', strtotime("last day of -1 month"));
	}
	else
	{
		$previous_date= date('d-M-Y', strtotime("first day of -1 month"));
		$current_date = date('d-M-Y', strtotime("last day of -1 month"));
	}
	
 //echo "and a.process_end_date between '$previous_date' and '$current_date'";die;



$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name",$con);
$supplier_library = return_library_array( "select id, supplier_name from lib_supplier  where status_active=1 and is_deleted=0", "id", "supplier_name",$con);


//Exfactory--------------------
	$ex_factory_date_con = " and a.ex_factory_date between '".$previous_date."' and '".$current_date."'";
$ex_factory_sql= "SELECT d.source,d.delivery_company_id,d.delivery_floor_id,
			SUM(a.ex_factory_qnty) as ex_factory_qnty,
			SUM(b.unit_price/c.total_set_qnty*a.ex_factory_qnty) as ex_factory_val,
			avg(b.unit_price/c.total_set_qnty) as fob
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c ,pro_ex_factory_delivery_mst d 
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 $ex_factory_date_con and c.status_active=1 and a.delivery_mst_id=d.id 
			and a.entry_form<>85 
			group by d.delivery_company_id,d.source,d.delivery_floor_id ";	 
	 //and d.delivery_company_id=$compid
	
	$ex_factory_sql_result = sql_select($ex_factory_sql, '', '', '', $con);
	foreach($ex_factory_sql_result as $rows)
	{
		//this is for urmi group.........................start;
		/*
		Note: 
			Floor Technical replace to Unit 1;
			Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
		*/
		
		if($rows[csf("delivery_floor_id")]==14 || $rows[csf("delivery_floor_id")]==20 || $rows[csf("delivery_floor_id")]==24 || $rows[csf("delivery_floor_id")]==37){$rows[csf("delivery_floor_id")]=24;}
		if($rows[csf("delivery_floor_id")]==7 || $rows[csf("delivery_floor_id")]==15 || $rows[csf("delivery_floor_id")]==19){$rows[csf("delivery_floor_id")]=19;}
		
		if($rows[csf("delivery_company_id")]==4){$rows[csf("delivery_floor_id")]=0;}
		if($rows[csf("delivery_company_id")]==3 || $rows[csf("delivery_company_id")]==2){
			$rows[csf("delivery_floor_id")]=0;
		}
		//this is for urmi group.........................end;
		
		$ex_fac_qty+=$rows[csf("ex_factory_qnty")];
		$ex_fac_qty_arr[$rows[csf("delivery_company_id")]][$rows[csf("delivery_floor_id")]]+=$rows[csf("ex_factory_qnty")];
		$ex_fac_val_arr[$rows[csf("delivery_company_id")]][$rows[csf("delivery_floor_id")]]+=$rows[csf("ex_factory_val")];
	}
	unset($ex_factory_sql_result);


//sub con........Bill

$ex_factory_date_sub_con_bill = " and a.BILL_DATE between '".$previous_date."' and '".$current_date."'";
$ex_factory_sub_con_sql_bill= "select a.company_id,b.delivery_qty,(b.delivery_qty*b.rate) as delivery_amount from SUBCON_INBOUND_BILL_MST a,SUBCON_INBOUND_BILL_DTLS b where a.id=b.mst_id and a.process_id=11 and b.process_id=11  $ex_factory_date_sub_con_bill and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";


	$ex_factory_sub_con_sql_result_bill = sql_select($ex_factory_sub_con_sql_bill, '', '', '', $con);
	foreach($ex_factory_sub_con_sql_result_bill as $rows)
	{
		//this is for urmi group.........................start;
		/*
		Note: 
			Floor Technical replace to Unit 1;
			Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
		*/
		//if($rows[csf("company_id")]==4){$rows[csf("company_id")]=2;}
		//this is for urmi group.........................end;
		
		//$ex_fac_qty_in_bound_bill+=$rows[csf("delivery_qty")];
		//$ex_fac_qty_in_bound_bill_arr[$rows[csf("company_id")]]+=$rows[csf("delivery_qty")];
		$ex_fac_val_in_bound_bill_arr[$rows[csf("company_id")]]+=$rows[csf("delivery_amount")];
	}
	unset($ex_factory_sub_con_sql_result_bill); 
//---------------------
//sub con........Delivery
$ex_factory_date_sub_con = " and a.delivery_date between '".$previous_date."' and '".$current_date."'";
$ex_factory_sub_con_sql= "select a.company_id,b.delivery_qty,(b.delivery_qty*c.rate) as delivery_amount from subcon_delivery_mst a,subcon_delivery_dtls b,subcon_ord_dtls c where a.id=b.mst_id and b.order_id=c.id and a.process_id=3 $ex_factory_date_sub_con and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0";
//echo $ex_factory_sub_con_sql;

	$ex_factory_sub_con_sql_result = sql_select($ex_factory_sub_con_sql, '', '', '', $con);
	foreach($ex_factory_sub_con_sql_result as $rows)
	{
		//this is for urmi group.........................start;
		/*
		Note: 
			Floor Technical replace to Unit 1;
			Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
		*/
		//if($rows[csf("company_id")]==4){$rows[csf("company_id")]=2;}
		//this is for urmi group.........................end;
		
		$ex_fac_qty_in_bound+=$rows[csf("delivery_qty")];
		$ex_fac_qty_in_bound_arr[$rows[csf("company_id")]]+=$rows[csf("delivery_qty")];
		$ex_fac_val_in_bound_arr[$rows[csf("company_id")]]+=$rows[csf("delivery_amount")];
	}
	unset($ex_factory_sub_con_sql); 

//Production ---------------------------                           

	$production_date_con = " and a.production_date between '".$previous_date."' and '".$current_date."'";
	$production_sql="select a.production_type,a.po_break_down_id,a.serving_company as company_id,a.floor_id,sum(b.production_qnty)  production_quantity  from pro_garments_production_mst a,pro_garments_production_dtls b,wo_po_color_size_breakdown c  where a.id=b.mst_id and a.production_type in(8,11) and b.production_type in(8,11)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and c.is_deleted=0 and b.status_active=1 and c.id=b.color_size_break_down_id $production_date_con group by a.serving_company,a.production_type,a.po_break_down_id,a.floor_id order by a.floor_id desc";
	//and a.serving_company=$compid
	$production_sql_result = sql_select($production_sql, '', '', '', $con);
	foreach($production_sql_result as $rows)
	{
		//this is for urmi group.........................start;
		/*
		Note: 
			Floor Technical replace to Unit 1;
			Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
		*/
		if($rows[csf("floor_id")]==14 || $rows[csf("floor_id")]==20 || $rows[csf("floor_id")]==24 || $rows[csf("floor_id")]==37){$rows[csf("floor_id")]=24;}
		if($rows[csf("floor_id")]==7 || $rows[csf("floor_id")]==15 || $rows[csf("floor_id")]==19){$rows[csf("floor_id")]=19;}		
		//if($rows[csf("floor_id")]==20){$rows[csf("floor_id")]=24;}
		if($rows[csf("company_id")]==4){$rows[csf("floor_id")]=0;}
		if($rows[csf("company_id")]==3 || $rows[csf("company_id")]==2){$rows[csf("floor_id")]=0;}
		//this is for urmi group.........................end;
		
		if($rows[csf('production_type')]==11){
			$poly_qty+=$rows[csf("production_quantity")];
			$poly_qty_arr[$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("production_quantity")];
		}
		else if($rows[csf('production_type')]==8){// Packing And Finishing
			$packing_finishing_po_data_arr[$rows[csf("company_id")]][$rows[csf("floor_id")]]+=$rows[csf("production_quantity")];
		}
	}
	unset($production_sql_result); 


	$production_date_con = " and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."'";
	$sql_subcon="select a.company_id,a.floor_id,sum(d.prod_qnty) as good_qnty 
from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d
where a.production_type=5 and d.production_type=5 and a.id=d.dtls_id and a.status_active=1 and a.is_deleted=0  $production_date_con group by a.company_id,a.floor_id";
//and a.company_id =$compid

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
		if($rows[csf("company_id")]==4){$rows[csf("floor_id")]=0;}
		if($rows[csf("company_id")]==3 || $rows[csf("company_id")]==2){$rows[csf("floor_id")]=0;}
		//this is for urmi group.........................end;
		$poly_qty_in_bound+=$rows[csf("good_qnty")];
		$poly_qty_in_bound_arr[$rows[csf("company_id")]]+=$rows[csf("good_qnty")];
		
	}
	unset($sql_subcon_result); 



//Kniting Production--------------------------------------                            
	
	//Kniting inhouse............
	$str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$current_date."'";
	$sql_qty="select sum(case when a.knitting_source in(1) then c.quantity end ) as qtyinhouse 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	 //and a.knitting_company=$compid
	$sql_result=sql_select( $sql_qty, '', '', '', $con);
	$kniting_pro_qty=0;
	foreach($sql_result as $row)
	{
		$kniting_pro_qty += $row[csf('qtyinhouse')];
	}				
	unset($sql_result);
	
	
	
	//Kniting inbound sub con............
	$knit_sub_con_date_con	=" and a.product_date between '".$previous_date."' and '".$current_date."'";
	$knit_in_bound_sub_con_sql="select a.product_type,sum(b.product_qnty) as qtyinhouse_sub,sum(REJECT_QNTY) as REJECT_QNTY from subcon_production_mst a,subcon_production_dtls b where a.id=b.mst_id $knit_sub_con_date_con and a.product_type in(2,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
group by a.product_type";
	
	$knit_in_bound_sub_con_sql_result=sql_select( $knit_in_bound_sub_con_sql, '', '', '', $con);
	$kniting_pro_sub_con_qty=$kniting_delivery_sub_con_qty=0;
	foreach($knit_in_bound_sub_con_sql_result as $row)
	{
		if($row[csf('product_type')]==2){
			$kniting_pro_sub_con_qty += $row[csf('qtyinhouse_sub')];
			$kniting_pro_sub_con_reg_qty += $row[csf('REJECT_QNTY')];
		}
		elseif($row[csf('product_type')]==4){
			$kniting_delivery_sub_con_qty += $row[csf('qtyinhouse_sub')];
			$kniting_delivery_sub_con_reg_qty += $row[csf('REJECT_QNTY')];
		}
	}				
	unset($knit_in_bound_sub_con_sql_result);
	//echo $knit_in_bound_sub_con_sql;die;
	
	





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
		$total_stock_qty=0;$total_stock_amu=0;
		foreach ($yearn_stock_qty_sql_result as $row) {
			$total_stock_qty += $row[csf("stock_qty")];
			$total_stock_amu +=($row[csf("stock_qty")]*$avg_rate_per_unit_arr[$row[csf("prod_id")]])/$conversion_rate;
		}
		unset($yearn_stock_qty_sql_result);
	
	
	
	
	//Efficiency in %----------------------------------------------start;
	$cd=date('d-m-Y');
	$sd=date('d-m-Y',strtotime(str_replace("'","",$current_date)));
	if($cd==$sd)
	{
		$end_date_one_day_back = date('d-m-Y', strtotime('-1 day', strtotime($current_date))); 
			if($db_type==0)
			{
				$end_date_one_day_back=change_date_format($end_date_one_day_back,'YYYY-MM-DD');
			}
			else if($db_type==2)
			{
				$end_date_one_day_back=change_date_format($end_date_one_day_back,'','',-1);
			}
	}
	else
	{
		$end_date_one_day_back=$end_date;
	}
	
	
	if($db_type==0) $prod_start_cond="prod_start_time";
	else if($db_type==2) $prod_start_cond="TO_CHAR(prod_start_time,'HH24:MI')";
	
	$variable_start_time_arr='';
	$company_cond=" and company_name=$compid";
	$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where variable_list=26 $company_cond and status_active=1 and is_deleted=0 and shift_id=1");
	foreach($prod_start_time as $row)
	{
		$ex_time=explode(" ",$row[csf('prod_start_time')]);
		$variable_start_time_arr=$row[csf('prod_start_time')];
	}
	unset($prod_start_time);
	$current_date_time=date('d-m-Y H:i');
	$ex_date_time=explode(" ",$current_date_time);
	$current_date2=$ex_date_time[0];
	$current_time=$ex_date_time[1];
	$ex_time=explode(":",$current_time);
	
	$search_prod_date=change_date_format(str_replace("'","",$end_date_one_day_back),'yyyy-mm-dd');
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));
	$dif_time=$difa_time[0];
	$dif_hour_min=date("H:i", strtotime($dif_time));
	
	
	$company_cond=" and company_name=$compid";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","variable_list=23 $company_cond and is_deleted=0 and status_active=1");
	
	$company_cond=" and a.company_id=$compid";
	if($start_date!="" && $end_date!=""){$sql_cond=" and pr_date between '$start_date' and '$end_date_one_day_back'";}
	 
	if($location){$location_cond=" and a.location_id=$location";}
	if($floor){$floor_cond=" and a.floor_id in($floor)";}

	 
	 if($prod_reso_allo==1)
	 {
		$prod_resource_array=array();
		$sql="select a.id, a.location_id, a.floor_id, a.line_number, b.active_machine, b.pr_date, b.man_power, b.operator, b.helper, b.smv_adjust, b.smv_adjust_type, b.line_chief, b.target_per_hour, b.working_hour, c.from_date, c.to_date, c.capacity as mc_capacity from prod_resource_mst a, prod_resource_dtls b, prod_resource_dtls_mast c where a.id=c.mst_id and a.id=b.mst_id and c.id=b.mast_dtl_id $company_cond $location_cond $floor_cond $sql_cond";
		$dataArray=sql_select($sql);// and a.id=1 and c.from_date=$end_date
		
		foreach($dataArray as $val)
		{
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['operator']=$val[csf('operator')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['helper']=$val[csf('helper')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['terget_hour']=$val[csf('target_per_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['tpd']=$val[csf('target_per_hour')]*$val[csf('working_hour')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_start']=$val[csf('from_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['day_end']=$val[csf('to_date')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['capacity']=$val[csf('mc_capacity')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
			$prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
			
			$date_line_wise_prod_resource[$val[csf('id')]][$val[csf('pr_date')]]+=$val[csf('man_power')];
		}
	 }
	
	
	$company_cond=" and a.serving_company=$compid";
	if($start_date!="" && $end_date!=""){$sql_cond=" and a.production_date between '$start_date' and '$end_date_one_day_back'";}
	
	if($location){$location_cond=" and a.location=$location";}
	if($floor){$floor_cond=" and a.floor_id in($floor)";}
		
	$sql="SELECT  a.production_type, a.floor_id, a.production_date, a.sewing_line,  a.po_break_down_id, a.item_number_id,sum(d.production_qnty) as good_qnty 
			from pro_garments_production_mst a,pro_garments_production_dtls d, wo_po_details_master b, wo_po_break_down c,wo_po_color_size_breakdown e
			where  a.production_type in(5,11) and d.production_type in(5,11) and a.id=d.mst_id and   a.po_break_down_id=c.id and c.job_no_mst=b.job_no and a.po_break_down_id=e.po_break_down_id and d.color_size_break_down_id=e.id and b.job_no=e.job_no_mst and c.id=e.po_break_down_id and  a.status_active=1 and a.is_deleted=0 and c.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active in(1,2,3)  and e.status_active in(1,2,3) and e.is_deleted=0 $sql_cond $company_cond $location_cond $floor_cond
			group by a.production_type,a.floor_id, a.po_break_down_id,  a.production_date, a.sewing_line, a.item_number_id order by a.floor_id, a.po_break_down_id";
	$sql_result=sql_select($sql);	
	$sewing_production_po_data_arr=array();
	$poly_production_po_data_arr=array();
	foreach($sql_result as $val)
	{
	
		
		$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')].'**'.$val[csf('po_break_down_id')].'**'.$val[csf('item_number_id')].'**'.$val[csf('production_date')];
		
		if($val[csf('production_type')]==5){// sewing
			$sewing_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		else if($val[csf('production_type')]==11){// poly
			$poly_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		
		$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
	}
	
	
	
	$company_cond=" and a.company_id=$compid";
	if($location){$location_cond=" and a.location_id=$location";}
	if($floor){$floor_cond=" and a.floor_id in($floor)";}
	
 $sql_subcon="select  a.production_type, a.floor_id, a.production_date, a.line_id as sewing_line, a.order_id as po_break_down_id, a.gmts_item_id as item_number_id,sum(d.prod_qnty) as good_qnty
				 from subcon_gmts_prod_dtls a,subcon_gmts_prod_col_sz d, subcon_ord_mst b, subcon_ord_dtls c,subcon_ord_breakdown e
				where a.production_type in(2,5) and d.production_type in(2,5) and a.id=d.dtls_id and a.order_id=c.id and c.job_no_mst=b.subcon_job and a.status_active=1 and a.is_deleted=0   and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and e.id=d.ord_color_size_id and e.order_id=c.id and a.order_id=e.order_id $sql_cond $company_cond $location_cond $floor_cond
				group by a.production_type, a.floor_id, a.order_id, a.production_date, a.line_id , a.gmts_item_id order by a.floor_id, a.order_id";	
	$sql_subcon_result=sql_select($sql_subcon);	
	foreach($sql_subcon_result as $val)
	{
	
		$key=$val[csf('floor_id')].'**'.$val[csf('sewing_line')].'**'.$val[csf('po_break_down_id')].'**'.$val[csf('item_number_id')].'**'.$val[csf('production_date')];
		
		if($val[csf('production_type')]==2){//sub sewing
			$sewing_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		else if($val[csf('production_type')]==5){//sub poly
			$poly_production_po_data_arr[$key]+=$val[csf('good_qnty')];
		}
		
		
		$all_po_id_arr[$val[csf('po_break_down_id')]]=$val[csf('po_break_down_id')];
	}
	
	
	
	$company_cond=" and company_name=$compid";
	$smv_source=return_field_value("smv_source","variable_settings_production","variable_list=25 $company_cond and status_active=1 and is_deleted=0");
	
	
		$where_cond='';$poIds_cond='';
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$po_id_chunk_arr=array_chunk($all_po_id_arr,999) ;
			foreach($po_id_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$poIds_cond.=" b.id in($chunk_arr_value) or ";	
			}
			
			$where_cond.=" and (".chop($poIds_cond,'or ').")";			
		}
		else
		{
			$where_cond=" and b.id in(".implode(',',$all_po_id_arr).")";	 
		}
	
	
	
	if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
	if($smv_source==3)
	{
		$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3)  $where_cond";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
		}
	}
	else
	{
		$sql_item="select b.id,c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,2,3) $where_cond";
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
	}

	
	
	 //echo $sql_item;die;
	//Sewing...............................................
	$tempSewLine=array();
	foreach($sewing_production_po_data_arr as $key=>$good_qnty){
		list($floor_id,$line_id,$po_id,$item_id,$production_date)=explode('**',$key);	
			//this is for urmi group.........................start;
			/*
			Note: 
				Floor Technical replace to Unit 1;
				Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
			*/
			if($floor_id==14 || $floor_id==20 || $floor_id==24 || $floor_id==37){$floor_id=24;}
			if($floor_id==7 || $floor_id==15 || $floor_id==19){$floor_id=19;}
			
			//if($compid==4){$compid=2;}
			if($compid==3 || $compid==2 || $compid==4){$floor_id=0;}
			//this is for urmi group.........................end;
		
		$produce_minit_arr[$compid][$floor_id]+=$good_qnty*$item_smv_array[$po_id][$item_id];
		
		if($tempSewLine[$line_id.$production_date]==''){

			if($current_date2==$search_prod_date)
			{
				$prod_wo_hour=$prod_resource_array[$line_id][$production_date]['working_hour'];
				
				if ($dif_time<$prod_wo_hour)//
				{
					$cla_cur_time=$dif_time;
				}
				else
				{
					$cla_cur_time=$prod_wo_hour;
				}
			}
			else
			{
				$cla_cur_time=$prod_resource_array[$line_id][$production_date]['working_hour'];
			}

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
			
			
			$efficiency_min_arr[$compid][$floor_id]+=$total_adjustment+$prod_resource_array[$line_id][$production_date]['man_power']*$cla_cur_time*60;
			
			
			$tempSewLine[$line_id.$production_date]=1;
		}
	}//foreach end;
	
	//$sewing_efficiency_array[]=number_format(($produce_minit_arr[$Ym]/$efficiency_min_arr[$Ym])*100,2,".","");


	//Poly...............................................
	$tempPolyLine=array();
	foreach($poly_production_po_data_arr as $key=>$good_qnty){
		list($floor_id,$line_id,$po_id,$item_id,$production_date)=explode('**',$key);
			//this is for urmi group.........................start;
			/*
			Note: 
				Floor Technical replace to Unit 1;
				Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
			*/
			if($floor_id==14 || $floor_id==20 || $floor_id==24 || $floor_id==37){$floor_id=24;}
			if($floor_id==7 || $floor_id==15 || $floor_id==19){$floor_id=19;}
			if($compid==4){$floor_id=0;}
			if($compid==3 || $compid==2){$floor_id=0;}
			//this is for urmi group.........................end;
			$poly_produce_minit_arr[$compid][$floor_id]+=$good_qnty*$item_smv_array[$po_id][$item_id];
		
		
		if($tempPolyLine[$line_id.$production_date]==''){
			if($current_date2==$search_prod_date)
			{
				$prod_wo_hour=$prod_resource_array[$line_id][$production_date]['working_hour'];
				
				if ($dif_time<$prod_wo_hour)
				{
					$cla_cur_time=$dif_time;
				}
				else
				{
					$cla_cur_time=$prod_wo_hour;
				}
			}
			else
			{
				$cla_cur_time=$prod_resource_array[$line_id][$production_date]['working_hour'];
			}
			
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
			$poly_efficiency_min_arr[$compid][$floor_id]+=$total_adjustment+$prod_resource_array[$line_id][$production_date]['man_power']*$cla_cur_time*60;
			$tempPolyLine[$line_id.$production_date]=1;
		}
		
	}//foreach end;


	//all company....................................
	//$to="";
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id and a.company_id=$compid";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}

}//end company foreach;


//Kniting sub............
$sql = "
  SELECT *
    FROM ( (  SELECT a.receive_basis,
                     a.receive_date,
                     a.booking_no,
                     NVL (f.booking_type, 1) booking_type,
                     1 AS is_order,
                     f.entry_form,
                     e.job_no,
                     e.sales_booking_no,
                     c.is_sales,
                     e.buyer_id unit_id,
                     e.within_group,
                     a.knitting_source,
                     e.buyer_id,
                     SUM (c.quantity) AS qntyshifta 
                FROM inv_receive_master a,
                     pro_grey_prod_entry_dtls b,
                     order_wise_pro_details c,
                     fabric_sales_order_mst e,
                     wo_booking_mst f
               WHERE a.id = b.mst_id
                     AND b.id = c.dtls_id
                     AND c.po_breakdown_id = e.id
                     AND e.sales_booking_no = f.booking_no
                     AND a.entry_form = 2
                     AND a.item_category = 13
                     AND c.entry_form = 2
                     AND c.trans_type = 1
                     AND a.company_id = 1
                     AND a.knitting_source=3
                     AND a.status_active = 1
                     AND a.is_deleted = 0
                     AND b.status_active = 1
                     AND b.is_deleted = 0
                     AND c.status_active = 1
                     AND c.is_deleted = 0
                     AND f.status_active = 1
                     AND f.is_deleted = 0  AND b.shift_name in('1','2','3')
                     AND a.receive_date BETWEEN '".$previous_date."' and '".$current_date."'
            GROUP BY b.machine_no_id,
                     a.receive_date,
                     e.job_no,
                     e.sales_booking_no,
                     e.within_group,
                     a.receive_basis,
                     a.booking_no,
                     f.booking_type,
                     f.entry_form,
                     b.floor_id,
                     a.knitting_source,
                     c.is_sales,
                     e.buyer_id)
          UNION ALL
          (  SELECT a.receive_basis,
                    a.receive_date,
                    a.booking_no,
                    NVL (g.booking_type, 1) booking_type,
                    2 AS is_order,
                    g.entry_form_id AS entry_form,
                    e.job_no,
                    e.sales_booking_no,
                    c.is_sales,
                    e.buyer_id unit_id,
                    e.within_group,
                    a.knitting_source,
                    e.buyer_id,
                    SUM (c.quantity) AS qntyshifta 
               FROM inv_receive_master a,
                    pro_grey_prod_entry_dtls b,
                    order_wise_pro_details c,
                    fabric_sales_order_mst e,
                    wo_non_ord_samp_booking_mst g
              WHERE a.id = b.mst_id
                    AND b.id = c.dtls_id
                    AND c.po_breakdown_id = e.id
                    AND e.sales_booking_no = g.booking_no
                    AND g.status_active = 1
                    AND g.is_deleted = 0
                    AND a.entry_form = 2
                    AND a.item_category = 13
                    AND c.entry_form = 2
                    AND c.trans_type = 1
                    AND a.company_id = 1
                    AND a.knitting_source=3
                    AND a.status_active = 1
                    AND a.is_deleted = 0
                    AND b.status_active = 1
                    AND b.is_deleted = 0
                    AND c.status_active = 1
                    AND c.is_deleted = 0  AND b.shift_name in('1','2','3')
                    AND a.receive_date BETWEEN '".$previous_date."' and '".$current_date."'
           GROUP BY b.machine_no_id,
                    a.receive_date,
                    e.job_no,
                    e.sales_booking_no,
                    e.within_group,
                    a.receive_basis,
                    a.booking_no,
                    g.booking_type,
                    g.entry_form_id,
                    b.floor_id,
                    a.knitting_source,
                    c.is_sales,
                    e.buyer_id)
          UNION ALL
          (  SELECT a.receive_basis,
                    a.receive_date,
                    a.booking_no,
                    999 AS booking_type,
                    1 AS is_order,
                    NULL AS entry_form,
                    e.job_no,
                    e.sales_booking_no,
                    c.is_sales,
                    e.buyer_id unit_id,
                    e.within_group,
                    a.knitting_source,
                    e.buyer_id,
                    SUM (c.quantity) AS qntyshifta 
               FROM inv_receive_master a,
                    pro_grey_prod_entry_dtls b,
                    order_wise_pro_details c,
                    fabric_sales_order_mst e
              WHERE a.id = b.mst_id
                    AND b.id = c.dtls_id
                    AND c.po_breakdown_id = e.id
                    AND e.within_group = 2
                    AND a.entry_form = 2
                    AND a.item_category = 13
                    AND c.entry_form = 2
                    AND c.trans_type = 1
                    AND a.company_id = 1
                    AND a.knitting_source=3
                    AND a.status_active = 1
                    AND a.is_deleted = 0
                    AND b.status_active = 1
                    AND b.is_deleted = 0
                    AND c.status_active = 1
                    AND c.is_deleted = 0  AND b.shift_name in('1','2','3')
                    AND a.receive_date BETWEEN '".$previous_date."' and '".$current_date."'
           GROUP BY b.machine_no_id,
                    a.receive_date,
                    e.job_no,
                    e.sales_booking_no,
                    e.within_group,
                    a.receive_basis,
                    a.booking_no,
                    b.floor_id,
                    a.knitting_source,
                    c.is_sales,
                    e.buyer_id))"; 
					
			$sql_result=sql_select( $sql);
			$buyer_wise_production_arr=array();
			foreach($sql_result as $row)
			{
				$buyer_wise_production_arr[0][$row[csf("knitting_source")]][$row[csf("booking_type")]][$row[csf("within_group")]][$row[csf("is_order")]] += ($row[csf("qntyshifta")]+$row[csf("qntyshiftb")]+$row[csf("qntyshiftc")]);
			}
			
			
		$knite_sub_qty=0;	
			foreach($buyer_wise_production_arr as $buyer => $rows)
			{
				$out_bound_qnty_wg_yes=$rows[3][1][1][1] + $rows[3][1][1][2] + $rows[3][999][1][1];
				$out_bound_qnty_wg_no=$rows[3][1][2][1]+ $rows[3][1][2][2] + $rows[3][999][2][1];						
			}
		$knite_sub_qty =($out_bound_qnty_wg_yes+$out_bound_qnty_wg_no);		
		unset($sql_result);






//Efficiency % loof.....................................start;
	foreach($efficiency_min_arr as $comapny_id=>$floorDataArr){
		foreach($floorDataArr as $floor_id=>$efficiency_min){			
			
			$sewing_efficiency_array[$comapny_id][$floor_id]=number_format(($produce_minit_arr[$comapny_id][$floor_id]/$efficiency_min)*100,2,".","");
		}
	}


	
	foreach($poly_efficiency_min_arr as $comapny_id=>$floorDataArr){
		foreach($floorDataArr as $floor_id=>$efficiency_min){
			$poly_efficiency_array[$comapny_id][$floor_id]=number_format(($poly_produce_minit_arr[$comapny_id][$floor_id]/$efficiency_min)*100,2,".","");
		}
	}
	
	
//Efficiency % loof.....................................end;



//Average SMV-------------------------------------------------start;
	
	$companyStr = implode(',',array_keys($company_library));
	$company_cond=" and a.style_owner in($companyStr)";
	$date_cond=" and c.country_ship_date between '$previous_date' and  '$current_date'";
	
	if($location){$location_cond=" and a.working_location_id=$location";}
	

$avg_smg_sql="select 
	a.style_owner,a.job_no,a.set_smv,
	sum(c.order_quantity/a.total_set_qnty) as po_quantity, 
	sum(c.order_quantity) as po_quantity_pcs
	
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	 where 
	 a.job_no=b.job_no_mst and 
	 a.job_no=c.job_no_mst and 
	 b.id=c.po_break_down_id 
	
	 $company_cond  $date_cond  and
	 a.status_active=1 and 
	 a.is_deleted=0 and 
	 b.status_active=1 and 
	 b.is_deleted=0 and 
	 c.status_active=1 and 
	 c.is_deleted=0
	 group by a.style_owner,a.job_no,a.set_smv"; // and b.is_confirmed=1 

	$avg_smg_sql_result=sql_select($avg_smg_sql);
	
	foreach ($avg_smg_sql_result as $row){
		$quantity_tot[$row[csf("style_owner")]]+=$row[csf("po_quantity_pcs")];
		$avg_smv_data_array[$row[csf("style_owner")]]+=$row[csf("set_smv")]*$row[csf('po_quantity')];
	}
	

	foreach($quantity_tot as $company_id=>$qty){
		$avg_smv_qty_array[$company_id]=number_format($avg_smv_data_array[$company_id]/$qty,2,".","");
	}

	

//Average SMV-------------------------------------------------end;
//Avg FoB in $------------------------------------start;		
$sql="select 
    a.style_owner,
    sum(c.order_total) val , sum(c.order_quantity) as qty
    from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
     where 
     a.job_no=b.job_no_mst and 
     a.job_no=c.job_no_mst and 
     b.id=c.po_break_down_id 
     $company_cond
  	$date_cond and
     a.status_active=1 and 
     a.is_deleted=0 and 
     b.status_active=1 and 
     b.is_deleted=0 and 
     c.status_active=1 and 
     c.is_deleted=0
     group by a.style_owner";

	$avg_fob_sql_result=sql_select($sql);
	
	foreach ($avg_fob_sql_result as $row){
		$fob=number_format($row[csf("val")]/$row[csf("qty")],2,".","");
		$ex_fac_fob_arr[$row[csf("style_owner")]]=$fob;
	}
	

//Avg FoB in $------------------------------------end;
	
	
//DHU-------------------------------------------------start;
	
	$date_cond=" and d.production_date between '$previous_date' and  '$current_date'";
	$company_cond=" and d.serving_company in($companyStr)";
	$dhu_sql = "SELECT d.serving_company,d.floor_id, sum(a.defect_qty) as defect_qty,sum(d.reject_qnty) as reject_qnty 
		FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d, pro_gmts_prod_dft a
		WHERE b.job_no=c.job_no_mst and d.po_break_down_id=c.id and d.id=a.mst_id and  a.defect_type_id in (3,4) and a.production_type=5 and a.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.status_active=1 and c.status_active in(1,2,3)  $date_cond $company_cond
		group by d.serving_company,d.floor_id";
	$dhu_deft_sql_result=sql_select($dhu_sql);
	foreach($dhu_deft_sql_result as $row){
			//this is for urmi group.........................start;
			/*
			Note: 
				Floor Technical replace to Unit 1;
				Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
			*/
			if($row[csf('floor_id')]==20){$row[csf('floor_id')]=24;}
			if($row[csf('serving_company')]==4){$row[csf('floor_id')]=0;}//
			if($row[csf('serving_company')]==3 || $row[csf('serving_company')]==2){$row[csf('floor_id')]=0;}
			//this is for urmi group.........................end;
		$dhu_defet_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('defect_qty')];
	}

	
	
	$dhu_sql = "SELECT d.serving_company,d.floor_id,sum(f.production_qnty) as qc_pass_qty,sum(f.alter_qty) as alter_qnty, sum(f.reject_qty) as reject_qnty,sum(f.spot_qty) as spot_qnty,sum(f.replace_qty) as replace_qty
FROM pro_garments_production_mst d,pro_garments_production_dtls f
WHERE d.production_type=5 and f.production_type=5 and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 $company_cond $date_cond  group by d.serving_company,d.floor_id
";
	$dhu_qc_sql_result=sql_select($dhu_sql);
	foreach($dhu_qc_sql_result as $row){
		//this is for urmi group.........................start;
		/*
		Note: 
			Floor Technical replace to Unit 1;
			Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
		*/
		if($row[csf('floor_id')]==20){$row[csf('floor_id')]=24;}
		if($row[csf('serving_company')]==4){$row[csf('floor_id')]=0;}//
		if($row[csf('serving_company')]==3 || $row[csf('serving_company')]==2){$row[csf('floor_id')]=0;}
		//this is for urmi group.........................end;
		$dhu_qc_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=($row[csf('qc_pass_qty')]+$row[csf('alter_qnty')]+$row[csf('reject_qnty')]+$row[csf('spot_qnty')]);//+$row[csf('replace_qty')]
	}
	
	
	
	
	foreach($dhu_defet_data_array as $company_id=>$floorDataArr){
		foreach($floorDataArr as $floor_id=>$qty){
			
			$dhu_qty_array[$company_id][$floor_id]=number_format($qty/$dhu_qc_data_array[$company_id][$floor_id]*100,2,".","");
		}
	}

//DHU-------------------------------------------------end;
	

		
//Cut Panel Rejection----------------------------------------------start;
	$date_cond=" and a.production_date between '$previous_date' and  '$current_date'";
	$company_cond=" and a.serving_company in($companyStr)";
	$production_sql ="select a.po_break_down_id,a.serving_company,a.floor_id,
	sum(CASE WHEN b.production_type=1  then b.reject_qty ELSE 0 END) as cut_reject_qty,
	sum(CASE WHEN b.production_type=1 then b.replace_qty ELSE 0 END) as cut_replace_qty,
	sum(CASE WHEN b.production_type=1 then b.production_qnty ELSE 0 END) as cut_production_qnty,
	
	sum(case when b.production_type=5 and  b.is_rescan=0 then b.reject_qty else 0 end) as reject_qty,
	sum(case when b.production_type=5 then b.replace_qty else 0 end) as replace_qty,
	sum(case when b.production_type=5 then b.alter_qty else 0 end) as alter_qty,
	sum(case when b.production_type=5 then b.spot_qty else 0 end) as spot_qty,
	sum(case when b.production_type=5 then b.production_qnty else 0 end) as production_qnty
	
	from pro_garments_production_mst a,pro_garments_production_dtls b 
	where a.id=b.mst_id and b.color_size_break_down_id!=0 and a.production_type in(1,5) and b.production_type in(1,5)  $company_cond $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id,a.serving_company,a.floor_id";		
	//echo $production_sql;
	$production_sql_result = sql_select($production_sql);
	foreach($production_sql_result as $row){
			//this is for urmi group.........................start;
			/*
			Note: 
				Floor Technical replace to Unit 1;
				Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
			*/
			if($row[csf('floor_id')]==14 || $row[csf('floor_id')]==20 || $row[csf('floor_id')]==24 || $row[csf('floor_id')]==37){$row[csf('floor_id')]=24;}
			if($row[csf('floor_id')]==7 || $row[csf('floor_id')]==15 || $row[csf('floor_id')]==19){$row[csf('floor_id')]=19;}
			if($row[csf('serving_company')]==4){$row[csf('floor_id')]=0;}//
			if($row[csf('serving_company')]==3 || $row[csf('serving_company')]==2){$row[csf('floor_id')]=0;}
			//this is for urmi group.........................end;
		
		$cut_rej_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('cut_reject_qty')];
		$cut_production_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=(($row[csf('cut_reject_qty')]+$row[csf('cut_production_qnty')])-$row[csf('cut_replace_qty')]);
		
		
		$sew_rej_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('reject_qty')];
		$sew_rep_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('replace_qty')];
		$sew_alt_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('alter_qty')];
		$sew_spot_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('spot_qty')];
		$sew_production_data_array[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('production_qnty')];
		
	}

	
	foreach($cut_production_data_array as $company_id=>$floorDataArr){
		foreach($floorDataArr as $floor_id=>$qty){
			$cut_panel_rejection=($cut_rej_data_array[$company_id][$floor_id]/$qty)*100;
			$cut_panel_rejection_array[$company_id][$floor_id]=number_format($cut_panel_rejection,2,".","");
		}
	}
	
	//var_dump($cut_panel_rejection_array);	
		
//Cut Panel Rejection----------------------------------------------end;

//Re-Cheque----------------------------------------------start;
		foreach($sew_production_data_array as $company_id=>$floorDataARr){
			foreach($floorDataARr as $floor_id=>$qty){
				$total_check_qty=$sew_rej_data_array[$company_id][$floor_id]+$sew_alt_data_array[$company_id][$floor_id]+$sew_spot_data_array[$company_id][$floor_id]+$sew_production_data_array[$company_id][$floor_id];
				
				//$re_check_qty_percent=($sew_rep_data_array[$company_id][$floor_id]/$total_check_qty)*100;
				//$re_check_qty_array[$company_id][$floor_id]=number_format($re_check_qty_percent,2,".","");


				$re_check_qty_percent=($sew_alt_data_array[$company_id][$floor_id]/$total_check_qty)*100;
				$re_check_qty_array[$company_id][$floor_id]=number_format($re_check_qty_percent,2,".","");
				

				$re_check_qty_array_test[$company_id][$floor_id] = '('.$sew_alt_data_array[$company_id][$floor_id].'/('.$sew_rej_data_array[$company_id][$floor_id].'+'.$sew_alt_data_array[$company_id][$floor_id].'+'.$sew_spot_data_array[$company_id][$floor_id].'+'.$sew_production_data_array[$company_id][$floor_id].'))*100';
				
			}
		}
//Re-Cheque----------------------------------------------end;


//Reject %----------------------------------------------start;
	$date_cond=" and d.production_date between '$previous_date' and  '$current_date'";
	$company_cond=" and d.serving_company in($companyStr)";
	$sql = "SELECT 
	d.serving_company,d.floor_id,
	sum(c.po_quantity) as po_quantity,
	sum(f.production_qnty) as qc_pass_qty, 
	sum(f.alter_qty) as alter_qnty, 
	sum(f.reject_qty) as reject_qnty, 
	sum(f.spot_qty) as spot_qnty,
	sum(f.replace_qty) as replace_qty
	
	FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst d,pro_garments_production_dtls f
	WHERE d.production_type=5 and f.production_type=5 and d.id=f.mst_id  and d.is_deleted=0 and d.status_active=1 and f.is_deleted=0 and f.status_active=1 and b.job_no=c.job_no_mst and d.po_break_down_id=c.id  and b.status_active=1 and c.status_active in(1,2,3) $company_cond $date_cond
	
	group by d.serving_company,d.floor_id
	";	  
		$result = sql_select($sql);
		foreach($result as $row)
		{
			//this is for urmi group.........................start;
			/*
			Note: 
				Floor Technical replace to Unit 1;
				Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
			*/
			if($row[csf('floor_id')]==14 || $row[csf('floor_id')]==20 || $row[csf('floor_id')]==24 || $row[csf('floor_id')]==37){$row[csf('floor_id')]=24;}
			if($row[csf('floor_id')]==7 || $row[csf('floor_id')]==15 || $row[csf('floor_id')]==19){$row[csf('floor_id')]=19;}
			if($row[csf('serving_company')]==4){$row[csf('floor_id')]=0;}//
			if($row[csf('serving_company')]==3 || $row[csf('serving_company')]==2){$row[csf('floor_id')]=0;}
			//this is for urmi group.........................end;
			$qc_qty_arr[$row[csf('serving_company')]][$row[csf('floor_id')]]+=($row[csf('qc_pass_qty')]+$row[csf('alter_qnty')]+$row[csf('reject_qnty')]+$row[csf('spot_qnty')]);//+$row[csf('replace_qty')]
			
			$line_def_rej_qty_arr[$row[csf('serving_company')]][$row[csf('floor_id')]]+=$row[csf('reject_qnty')];
		}


		foreach($qc_qty_arr as $company_id=>$floorDataARr){
			foreach($floorDataARr as $floor_id=>$qty){
				$re_reject_parcentage_qty_array[$company_id][$floor_id]=number_format($line_def_rej_qty_arr[$company_id][$floor_id]/$qty*100,2,".","");
			}
		}
//Reject %----------------------------------------------end;


//Man Machine Ratio----------------------------------------------start;
		
		$date_cond=" and insert_date between '$previous_date' and '$current_date'";
		$machine_sql="select insert_date,unit_id,sum(mmr_value) as mmr_value  from  mmrdashboard where mmr_value>0 $date_cond group by insert_date,unit_id";
		$machine_sql_result = sql_select($machine_sql);
		foreach($machine_sql_result as $row){
			
			if($row[csf('unit_id')]==1){ $company=1; $floor=24;}
			else if($row[csf('unit_id')]==5){ $company=1; $floor=19;}
			else if($row[csf('unit_id')]==2){ $company=2; $floor=0;}
			else if($row[csf('unit_id')]==3){ $company=3; $floor=0;}
			else if($row[csf('unit_id')]==4){ $company=4; $floor=0;}
			
			
			$machine_data_array[$company][$floor]+=$row[csf('mmr_value')]*1;
			$day_data_array[$company][$row[csf('insert_date')]]=1;
		}
		
		foreach($machine_data_array as $company_id=>$floorDataARr){
			foreach($floorDataARr as $floor_id=>$val){
				$man_machine_ratio=($val/array_sum($day_data_array[$company_id]))*1;
				$man_machine_ratio_array[$company_id][$floor_id]=number_format($man_machine_ratio,2,".","");
			}
		}

		
//Man Machine Ratio----------------------------------------------end;

//Air freight in Qty (lakh/pcs)--------------------------start;

	
	$date_cond=" and a.delivery_date between '$previous_date' and '$current_date'";
	$company_cond=" and a.delivery_company_id in($companyStr)";
	$air_exfactory_sql="select a.delivery_floor_id,a.delivery_company_id,b.ex_factory_qnty from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b where a.id=b.delivery_mst_id $date_cond $company_cond and b.shiping_mode=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";

	$air_exfactory_sql_result = sql_select($air_exfactory_sql);
	foreach($air_exfactory_sql_result as $row)
	{
		//this is for urmi group.........................start;
		/*
		Note: 
			Floor Technical replace to Unit 1;
			Company 'ATTIRES MANUFACTURING CO. LTD.' replace to 'URMI GARMENTS LTD'.;
		*/
		if($row[csf('delivery_floor_id')]==14 || $row[csf('delivery_floor_id')]==20 || $row[csf('delivery_floor_id')]==24 || $row[csf('delivery_floor_id')]==37){$row[csf('delivery_floor_id')]=24;}
		if($row[csf('delivery_floor_id')]==7 || $row[csf('delivery_floor_id')]==15 || $row[csf('delivery_floor_id')]==19){$row[csf('delivery_floor_id')]=19;}
		if($row[csf('delivery_company_id')]==4){$row[csf('delivery_floor_id')]=0;}//
		if($row[csf('delivery_company_id')]==3 || $row[csf('delivery_company_id')]==2){$row[csf('delivery_floor_id')]=0;}
		//this is for urmi group.........................end;
		$air_exfactory_qty_array[$row[csf('delivery_company_id')]][$row[csf('delivery_floor_id')]]+=($row[csf('ex_factory_qnty')]/100000);
	}
		
//Air freight in Qty (lakh/pcs)--------------------------end;


	//Daying Loading data.....................................start;
	$company_cond=" and c.working_company_id in($companyStr)";
	$sql_cond=" and a.process_end_date between '$previous_date' and '$current_date'";
	$re_process_sql="select a.service_source,c.total_trims_weight,a.batch_ext_no,a.batch_id,a.service_company,a.floor_id,sum(b.production_qty) as production_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b, pro_batch_create_mst c  where a.id=b.mst_id and a.batch_id=c.id  $sql_cond $company_cond and a.load_unload_id = 1  and c.batch_against in(1,2) and a.status_active=1 and a.is_deleted=0 and a.entry_form=35 and c.entry_form=0 and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 group by a.service_source,a.service_company,a.floor_id,c.total_trims_weight,a.batch_ext_no,a.batch_id,a.process_end_date";
	
	$re_process_sql_result = sql_select($re_process_sql);
	foreach($re_process_sql_result as $row){
		$batch_qty_data_array[$row[csf('service_source')]]+=($row[csf('production_qty')]+$row[csf('total_trims_weight')]);
	}
	
	
	
	//sub con............
	$company_cond=" and c.working_company_id in($companyStr)";
	$sql_cond=" and a.process_end_date between '$previous_date' and '$current_date'";
	$re_process_sub_con_sql=" SELECT a.service_source,c.total_trims_weight,
         a.batch_ext_no,
         a.batch_id,
         a.service_company,
         a.floor_id,
         SUM (d.batch_qnty) AS batch_qnty
    FROM pro_fab_subprocess a, pro_batch_create_mst c, pro_batch_create_dtls d
   WHERE     a.batch_id = c.id
         AND c.id = d.mst_id
         $sql_cond
		 
         AND a.load_unload_id = 1
         AND c.batch_against IN (1, 2)
         AND a.status_active = 1
         AND a.is_deleted = 0
         AND a.entry_form = 38
         AND c.entry_form = 36
         AND d.status_active = 1
         AND d.is_deleted = 0
         AND c.status_active = 1
         AND c.is_deleted = 0
         
GROUP BY a.service_source,a.service_company,
         a.floor_id,
         c.total_trims_weight,
         a.batch_ext_no,
         a.batch_id,
         a.process_end_date";//AND a.batch_id = 402 $company_cond
	//echo $re_process_sub_con_sql;die;
	$re_process_sub_con_sql_result = sql_select($re_process_sub_con_sql);
	foreach($re_process_sub_con_sql_result as $row){
		$batch_qty_sub_con_data_array[$row[csf('service_source')]]+=($row[csf('batch_qnty')]+$row[csf('total_trims_weight')]);
	}
	
	
	
	
	
	
	
    $workingCompany_name_cond2="  and a.working_company_id in($companyStr)";       
	$dates_com=" and  f.process_end_date BETWEEN '$previous_date' AND '$current_date' ";	
			$sql = "select a.id as batch_id,a.working_company_id, f.floor_id, f.result , a.extention_no, a.total_trims_weight
			from pro_batch_create_dtls b, fabric_sales_order_mst d, pro_fab_subprocess f,  pro_batch_create_mst a 
			where f.batch_id=a.id 
			$workingCompany_name_cond2  $dates_com and a.entry_form=0  and a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form in(35,38) and f.load_unload_id=2 and a.batch_against in(1,2,3,11) and b.po_id=d.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.is_sales=1 and b.is_sales=1
			";
			//echo $sql;die;
            $batchdata=sql_select($sql);
			foreach($batchdata as $row)
			{
				$all_batch_arr[$row[csf("batch_id")]]=$row[csf("batch_id")];
			}
			
			
			if($db_type==2 && count($all_batch_arr)>999)
			{
				$all_batch_chunk=array_chunk($all_batch_arr,999) ;
				foreach($all_batch_chunk as $chunk_arr)
				{
					$batchCond.=" f.batch_id in(".implode(",",$chunk_arr).") or ";	
					$batchCond2.=" a.batch_id in(".implode(",",$chunk_arr).") or ";	
				}

				$dyeing_batch_id_cond.=" and (".chop($batchCond,'or ').")";			
				$all_batch_no_cond2.=" and (".chop($batchCond2,'or ').")";			

			}
			else
			{ 	

				$dyeing_batch_id_cond=" and f.batch_id in($all_batch_ids)";
				$all_batch_no_cond2=" and a.batch_id in($all_batch_ids)";

			}

			
			$add_tp_stri_batch_sql=sql_select("select  a.batch_id, a.dyeing_re_process from pro_recipe_entry_mst a where a.entry_form = 60 and a.status_active = 1 and a.is_deleted = 0 $all_batch_no_cond2 group by a.batch_id, a.dyeing_re_process");
			foreach ($add_tp_stri_batch_sql as $val) 
			{
				$add_tp_stri_batch_arr[$val[csf("batch_id")]] = $val[csf("dyeing_re_process")];
			}
			unset($add_tp_stri_batch_sql);
			
			
			$sql_prod_ref= sql_select("select a.id,a.batch_id, b.prod_id,b.const_composition,sum(b.batch_qty) as batch_qty, sum(b.production_qty) as production_qty
				from  pro_fab_subprocess a, pro_fab_subprocess_dtls b
				where a.id = b.mst_id and a.load_unload_id = 2 and b.load_unload_id=2 and b.entry_page=35 and a.status_active = 1 and b.status_active=1 $all_batch_no_cond2 
				group by a.id,a.batch_id, b.prod_id,b.const_composition"); // and a.batch_id in ($all_batch_ids)

			foreach ($sql_prod_ref as $val) 
			{
				$batch_product_arr[$val[csf("batch_id")]]["batch_prod_tot"] += $val[csf("production_qty")];
			}

			
			
			
            foreach($batchdata as $row)
			{
					//re-process
				if($row[csf("extention_no")]>0 && $row[csf("result")]==1)
				{
					if($chkBatch[$row[csf("batch_id")]] =="")
					{
						$total_reprocess_qty[$row[csf("working_company_id")]][$row[csf("floor_id")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}

					//adding
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]==2)
				{
					if($chkBatch_1[$row[csf("batch_id")]] =="")
					{
						
						$total_adding_qnty[$row[csf("working_company_id")]][$row[csf("floor_id")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_1[$row[csf("batch_id")]] =$row[csf("batch_id")];
						$btb_shade_matched[$row[csf("ltb_btb_id")]]["shade_matched"] ++;
					}
				}


					//rft
				if($row[csf("extention_no")]==0 && $row[csf("result")]==1 && $add_tp_stri_batch_arr[$row[csf("batch_id")]]=="")
				{
					if($chkBatch_2[$row[csf("batch_id")]] =="")
					{
							
						$total_rft_qnty[$row[csf("working_company_id")]][$row[csf("floor_id")]] += $batch_product_arr[$row[csf("batch_id")]]["batch_prod_tot"] + $row[csf("total_trims_weight")];
						$chkBatch_2[$row[csf("batch_id")]] =$row[csf("batch_id")];
					}
				}


		}
		
	
	//Re-Process data.....................................start;
	foreach($total_reprocess_qty as $company_id=>$floorDataARr){
		foreach($floorDataARr as $floor_id=>$qty){
			$total_re_precess_qty+=$qty;
			$total_rft_qty+=$total_rft_qnty[$company_id][$floor_id];
			$total_adding_qty+=$total_adding_qnty[$company_id][$floor_id];
		}
	}
		
	 $ExLotReProcess = number_format(($total_re_precess_qty*100)/($total_rft_qty + $total_adding_qty + $total_re_precess_qty),2);
	
	
	
	
	//Re-Processg data.....................................end;
	//% of Compensation on Production.....................................start;
	
	$sql_cond=" and e.process_end_date between '$previous_date' and '$current_date'";
	$company_cond=" and e.company_id in($companyStr)";	
	$short_fb_sql=" select d.total_trims_weight,e.process_end_date,sum(f.production_qty) as production_qty
	 from 
		 wo_booking_mst a, 
		 fabric_sales_order_mst c, 
		 pro_batch_create_mst d, 
		 pro_fab_subprocess e, 
		 pro_fab_subprocess_dtls f
	 where
	  a.short_booking_type=2 and
	  a.booking_no=c.sales_booking_no and
	  c.sales_booking_no=d.booking_no and
	  d.batch_no=e.batch_no and
	  e.id=f.mst_id and
	  d.is_sales=1 and
	  a.status_active=1 and a.is_deleted=0 and
	  c.status_active=1 and c.is_deleted=0 and
	  d.status_active=1 and d.is_deleted=0 and
	  e.status_active=1 and e.is_deleted=0 and
	  f.status_active=1 and f.is_deleted=0 and
	  e.id=f.mst_id $sql_cond $company_cond and e.load_unload_id = 1
	 group by d.total_trims_weight,e.process_end_date
	  ";	
	  //echo $short_fb_sql;die; 
	  
	$short_fb_qty_compensative_data_array=0;
	$short_fb_sql_result = sql_select($short_fb_sql);
	foreach($short_fb_sql_result as $row){
		$short_fb_qty_compensative_data_array+=$row[csf('production_qty')]+$row[csf('total_trims_weight')]*1;
	}
	$parcent_of_compensation_on_production_qty_array+=($short_fb_qty_compensative_data_array/array_sum($batch_qty_data_array))*100;
	
	//% of Compensation on Production.....................................end;

	//Dyes/Chemical Avg Cost Per Kg.....................................start;
	
	$company_cond=" and a.company_id in($companyStr)";
	$sql_cond=" and a.batch_date between '$previous_date' and '$current_date'";
		
	//$batch_weight_sql="select a.batch_weight from pro_batch_create_mst a where status_active=1 and is_deleted=0 $sql_cond $company_cond";
	
	//ref report name Batch Report- Sales; path:batch_report_for_sales_controller.php
	$batch_weight_sql="SELECT SUM (b.batch_qnty)     AS batch_weight
  FROM pro_batch_create_mst    a,
       pro_batch_create_dtls   b,
       fabric_sales_order_mst  c,
       pro_roll_details        d
 WHERE   a.status_active = 1
       $sql_cond $company_cond
       AND a.is_sales = 1
       AND a.id = b.mst_id
       AND b.po_id = c.id
       AND b.barcode_no = d.barcode_no
       AND b.is_deleted = 0
       AND d.entry_form IN (2, 22)
       AND d.status_active = 1
       AND c.status_active = 1
UNION ALL
SELECT SUM (b.batch_qnty)     AS batch_weight
  FROM pro_batch_create_mst a, pro_batch_create_dtls b
 WHERE     a.id = b.mst_id
       AND a.status_active = 1
       AND a.is_deleted = 0
       AND b.status_active = 1
       AND b.is_deleted = 0 
       AND a.entry_form = 36
       $sql_cond $company_cond
	   ";
	
	
	$batch_weight_sql_result = sql_select($batch_weight_sql);
	foreach($batch_weight_sql_result as $row){
		$batch_weight_data+=$row[csf('batch_weight')];
	}
		
	
	
	
	
	$sql_cond=" and a.transaction_date between '$previous_date' and '$current_date'";
	$company_cond=" and a.company_id in($companyStr)";
 
	//$dys_issue_sql="select sum(case when a.transaction_type in(2,3,6) then a.cons_amount else 0 end) as CONS_AMOUNT from inv_transaction a ,inv_issue_master b where b.id=a.mst_id and b.issue_purpose not in(3,5,26,27,28,29,30,34) and a.item_category in(5,6,7,23) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $sql_cond";

	$dys_issue_sql="select sum(case when a.transaction_type in(2,3) then a.cons_amount else 0 end) as CONS_AMOUNT from inv_transaction a ,inv_issue_master b where b.id=a.mst_id and b.issue_purpose not in(5) and a.item_category in(5,6,7,23) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
  $company_cond $sql_cond";

  
//  UNION ALL
//  SELECT sum(a.cons_amount) AS CONS_AMOUNT  FROM inv_transaction a, product_details_master b, inv_item_transfer_mst c
//   WHERE a.prod_id = b.id  AND a.mst_id = c.id AND a.company_id = 1  AND a.transaction_type IN (6) AND a.status_active = 1 AND a.is_deleted = 0  AND c.status_active = 1 AND c.is_deleted = 0 AND a.item_category IN (5,6,7,23)  $company_cond $sql_cond
	
		
		$dys_issue_sql_result = sql_select($dys_issue_sql);
		foreach($dys_issue_sql_result as $val){
			$dyc_amount_data_array+=$val['CONS_AMOUNT'];
		}


//Dyes/Chemical Avg Cost Per Kg.....................................end;


  $companyStr = implode(',',array_keys($company_library));
   $from_date=$previous_date;
   $to_date=$current_date;
   
                       
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
				
				$stock+=$stockInHand;
				$valueUsd+=$stock_value_usd;
			}

		//echo $stock.'='.$valueUsd;



	$finishigSql="select b.receive_qnty,b.REJECT_QTY,b.uom from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=7 and a.item_category=2 and a.receive_basis=5 and a.knitting_company in($companyStr) and a.receive_date between '$from_date' and '$to_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //and a.recv_number = 'FTML-FFPE-18-03926'
	$finishigSqlResult = sql_select($finishigSql);
	foreach ($finishigSqlResult as $row) 
	{
		$finishQty[$row[csf('uom')]]+=$row[csf('receive_qnty')];
		$finishRegQty[$row[csf('uom')]]+=$row[csf('REJECT_QTY')];
	}


ob_start();

$i=1;
?>


<table cellpadding="3" cellspacing="0" border="1" rules="all">
    <tr><th colspan="8">Monthly Report</th></tr>		
    <tr><th colspan="8">Urmi Group</th></tr>		
    <tr><th colspan="8"><? echo date('M-Y',strtotime($current_date)); ?></th></tr>		
    <tr bgcolor="#DDD">
        <th rowspan="2">SL</th>
        <th width="150" rowspan="2">Description</th>
        <?
			foreach($DataArr as $compId=>$valArr){
			 echo "<th width='60' colspan=".count($valArr).">$customCompany[$compId]</th>";	
			}
		?>
        <th width="60" rowspan="2">Total/Avg</th>
    </tr>
    <tr bgcolor="#DDD">
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 echo "<th width='60'>$val</th>";	
			}
		}
		?>
    </tr>
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Production in Poly</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 echo "<td align='right'>".number_format($poly_qty_arr[$compId][$folId])."</td>";
			   $tot_poly_qty+=$poly_qty_arr[$compId][$folId];
			}
		}
		?>
        <td align="right"><b><? echo number_format($tot_poly_qty);?></b></td>
     </tr>  
       
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Shipment Qty </td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 if($ex_fac_qty_arr[$compId][$folId]>0){
				 echo "<td align='right'>".number_format($ex_fac_qty_arr[$compId][$folId])."</td>";
			 }
			 else{echo "<td align='right'></td>";}
			   $tot_ex_fac_qty+=$ex_fac_qty_arr[$compId][$folId];
			}
		}
		?>
        <td align="right" ><b><? echo number_format($tot_ex_fac_qty);?></b></td>
     </tr>  
       
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Shipment Value</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 if($ex_fac_val_arr[$compId][$folId]>0){
				 echo "<td align='right'>".number_format($ex_fac_val_arr[$compId][$folId],0)."</td>";
			}
			else{echo "<td align='right'></td>";}
			   $tot_ex_fac_val+=$ex_fac_val_arr[$compId][$folId];
	
			}
		}
		?>
        <td align="right"><b><? echo number_format($tot_ex_fac_val,0);?></b></td>
     </tr>
    
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Sewing DHU %</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 echo "<td align='right' title='Defect Qty:".$dhu_defet_data_array[$compId][$folId].'/Qc+Alter+Rej+Spot:'.$dhu_qc_data_array[$compId][$folId]."'>".$dhu_qty_array[$compId][$folId]."</td>";	
			 $total_dhu_qty+=$dhu_qty_array[$compId][$folId];
			}
		}
		?>
        <td align="right"><b><? echo number_format($total_dhu_qty/5,2);?></b></td>
     </tr>
     
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Cut Panel Rejection %</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 if($cut_panel_rejection_array[$compId][$folId]>0){
				 echo "<td align='right'>".$cut_panel_rejection_array[$compId][$folId]."</td>";	
			 }
			 else{echo "<td align='right'></td>";	}
			 $tot_cut_panel_rejection+=$cut_panel_rejection_array[$compId][$folId];
			 
			}
		}
		?>
        <td align="right" ><b><? echo number_format($tot_cut_panel_rejection/5,2);?></b></td>
     </tr>
     
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Alter %</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 echo "<td align='right' title='".$re_check_qty_array_test[$compId][$folId]."' >".$re_check_qty_array[$compId][$folId]."</td>";
			 $tot_re_check_qty+=$re_check_qty_array[$compId][$folId];	
			}
		}
		?>
        <td align="right" ><b><? echo number_format($tot_re_check_qty/5,2);?></b></td>
     </tr>
     
     
     <tr>
    	<td><? echo $i++;?></td>
    	<td>Sewing Reject %</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 echo "<td align='right'>".$re_reject_parcentage_qty_array[$compId][$folId]."</td>";
			 $tot_re_reject_parcentage_qty+=$re_reject_parcentage_qty_array[$compId][$folId];	
			}
		}
		?>
        <td align="right"><b><? echo number_format($tot_re_reject_parcentage_qty/5,2);?></b></td>
     </tr>
    
     
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Man Machine Ratio</td>
        <?
/*		foreach($DataArr as $compId=>$valArr){
			 echo "<td colspan=".count($valArr)." align='right'>".$man_machine_ratio_array[$compId]."</td>";
			 $totManMachineRatio+=$man_machine_ratio_array[$compId];	
			  
		}
*/		
		
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			 echo "<td align='right'>".$man_machine_ratio_array[$compId][$folId]."</td>";
			 $totManMachineRatio+=$man_machine_ratio_array[$compId][$folId];	
			}
		}
		?>
        <td align="right" ><b><? echo number_format($totManMachineRatio/5,2);?></b></td>
     </tr>
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Air Freight  (in Lakh-Pcs)</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			if($air_exfactory_qty_array[$compId][$folId]>0){
			 echo "<td align='right'>".number_format($air_exfactory_qty_array[$compId][$folId],3)."</td>";
			}
			else{echo "<td align='right'></td>";}
			 $totAirFreightQty+=$air_exfactory_qty_array[$compId][$folId];
			}
		}
		?>
        <td align="right"><b><? echo number_format($totAirFreightQty,3);?></b></td>
     </tr>
     
    <tr>
    	<td><? echo $i++;?></td>
    	<td>Packing & Finishing Qty</td>
        <?
		foreach($DataArr as $compId=>$valArr){
			foreach($valArr as $folId=>$val){
			if($packing_finishing_po_data_arr[$compId][$folId]>0){
			 echo "<td align='right'>".number_format($packing_finishing_po_data_arr[$compId][$folId])."</td>";
			}
			else{echo "<td align='right'></td>";}
			 $tot_packing_finishing_qty+=$packing_finishing_po_data_arr[$compId][$folId];
			}
		}
		?>
        <td align="right" ><b><? echo number_format($tot_packing_finishing_qty);?></b></td>
     </tr>
</table>

 

<br />
        
<table cellpadding="3" cellspacing="0" border="1" rules="all">
    <tr bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Inbound Subcontract(RMG)</th>
        <? foreach($customCompany as $comID=>$comName){echo "<th>$comName ".$DataArr[$comID][0]."</th>";}?>
    </tr>
    <tr>
    	<td>1</td>
    	<td>Production Qty</td>
        <? foreach($customCompany as $comID=>$comName){
			echo "<td align='right'>".number_format($poly_qty_in_bound_arr[$comID])."</td>";
			}
		?>
     </tr>
    <tr>
    	<td>2</td>
    	<td>Delivery Qty</td>
        <? foreach($customCompany as $comID=>$comName){
			echo "<td align='right'>".number_format($ex_fac_qty_in_bound_arr[$comID])."</td>";
			}
		?>
     </tr>
    <tr>
    	<td>3</td>
    	<td>Delivery Value (Tk)</td>
        <? foreach($customCompany as $comID=>$comName){
			echo "<td align='right'>".number_format($ex_fac_val_in_bound_arr[$comID])."</td>";
			}
		?>
     </tr>
     
     
    <tr>
    	<td>4</td>
    	<td>Billed Value (Tk)</td>
        <? foreach($customCompany as $comID=>$comName){
			echo "<td align='right'>".number_format($ex_fac_val_in_bound_bill_arr[$comID])."</td>";
			}
		?>
     </tr>  
     
</table>        


<br />
<?

$ex_factory_date_con = " and b.ex_factory_date between '".$previous_date."' and '".$current_date."'";
$sql="select a.company_id,a.delivery_company_id,a.buyer_id,  sum(b.ex_factory_qnty) as ex_factory_qnty,
sum((c.unit_price/d.total_set_qnty)*b.ex_factory_qnty) as ex_factory_val 
from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,wo_po_break_down c,wo_po_details_master d  where a.id=b.delivery_mst_id and b.po_break_down_id=c.id  and c.job_no_mst=d.job_no and a.source=3  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  $ex_factory_date_con
group by a.company_id,a.delivery_company_id,a.buyer_id
";
	$ex_factory_sql_result = sql_select($sql, '', '', '', $con);
	foreach($ex_factory_sql_result as $row)
	{
		
		//if($row[csf("company_id")]==4){$row[csf("company_id")]=2;}
		$out_bound_sub_exfactory_data_arr[$row[csf('company_id')]][$row[csf('delivery_company_id')]][$row[csf('buyer_id')]]=array(
			company_id=>$row[csf('company_id')],
			delivery_company_id=>$row[csf('delivery_company_id')],
			buyer_id=>$row[csf('buyer_id')],
			delivery_floor_id=>$row[csf('delivery_floor_id')],
			ex_factory_val=>$row[csf('ex_factory_val')],
			ex_factory_qnty=>$row[csf('ex_factory_qnty')]
			);
			$rowspanArr[company][$row[csf('company_id')]][$row[csf('delivery_company_id')].$row[csf('buyer_id')]]=1;
	}
	

?>

<table cellpadding="3" cellspacing="0" border="1" rules="all">
    <tr bgcolor="#CCCCCC">
        <th>SL</th>
        <th>LC Com</th>
        <th>Outbound Subcontract (RMG)</th>
        <th>Buyer</th>
        <th>Shipment Qty</th>
        <th>Shipment Value</th>
    </tr>
    <? 
	$i=1;
	foreach($out_bound_sub_exfactory_data_arr as $company_id=>$comapny_data_arr){ 
	?>
    <tr>
        <td align="center" rowspan="<? echo count($rowspanArr[company][$company_id]);?>"><? echo $i;?></td>
        <td rowspan="<? echo count($rowspanArr[company][$company_id]);?>"><? echo $company_library[$company_id];?></td>
	<?
	$i1=1;
	foreach($comapny_data_arr as $supplier_id=>$suppliyer_data_arr){
		if($i1!=1){echo "<tr>";}
		echo "<td rowspan='".count($suppliyer_data_arr)."'>".$supplier_library[$supplier_id]."</td>";
	$i2=1;
	foreach($suppliyer_data_arr as $supplier_id=>$row){
		$total_ex_factory_qnty+=$row[ex_factory_qnty];
		$total_ex_factory_val+=$row[ex_factory_val];
		
		if($i2!=1){echo "<tr>";}
	?>
        
        <td><? echo $buyer_library[$row[buyer_id]];?></td>
        <td align="right"><? echo number_format($row[ex_factory_qnty]);?></td>
        <td align="right"><? echo number_format($row[ex_factory_val],0);?></td>
    </tr>
    <? $i1++;$i2++;}}$i++;}  ?>
    
    <tfoot>
        <th colspan="4" align="right">Total:</th>
        <th align="right"><? echo number_format($total_ex_factory_qnty);?></th>
        <th align="right"><? echo number_format($total_ex_factory_val,0);?></th>
    </tfoot>
    
</table>

<br />

<table cellpadding="3" cellspacing="0" border="1" rules="all">
    <tr bgcolor="#DDD"><th>SL</th><th width="210">Textile</th><th width="100">Qty/Value</th></tr>	
<!--    <tr><td>1</td><td>Yarn Stock (Kg)</td><td align="right"><? //echo number_format($stock);?></td></tr> 
    <tr><td>2</td><td>Y. Value (USD)</td><td align="right"><? //echo number_format($valueUsd);?></td></tr> 
-->   
    <tr><td>1</td><td>Knitting-Inhouse (Ton)</td><td align="right">
	<? //echo number_format(($kniting_pro_qty)/1000);
	echo number_format(($kniting_pro_qty+$kniting_pro_sub_con_qty)/1000);
	//echo "==";
	//echo number_format(($kniting_pro_sub_con_qty)/1000);
	
	?>
    
    
    
    </td></tr> 
    <tr><td>2</td>
    <td>Knitting-Outside Subcon. (Ton)</td><td align="right"><? echo number_format($knite_sub_qty/1000);?></td></tr> 
    <tr><td>3</td>
    <td>Dyeing-Inhouse (Ton)</td><td align="right">
	<? echo number_format(($batch_qty_data_array[1]+$batch_qty_sub_con_data_array[1])/1000); ?>
    </td></tr> 
    
    <!--<tr><td>4</td>
    <td>Dyeing-Outside Subcon (Ton)</td><td align="right">
	< ? echo number_format(($batch_qty_data_array[3]+$batch_qty_sub_con_data_array[3])/1000); ?>
    </td></tr>-->
  	
    <tr><td>4</td><td>Finishing (Kg/Yds)</td><td align="right"><? echo number_format(($finishQty[12]+$kniting_delivery_sub_con_qty)).' / '.number_format($finishQty[27]);?></td></tr>
    
    <tr><td>5</td><td>Finishing Reject (Kg/Yds)</td>
    <td align="right"><? echo number_format(($finishRegQty[12]+$kniting_delivery_sub_con_reg_qty)).' / '.number_format($finishRegQty[27]);?></td>
    </tr>
    
    
    <tr><td>6</td><td>Ex-Lot (Re-Process) %</td><td align="right"><? echo $ExLotReProcess;//number_format($re_process_qty_array,2);?></td></tr> 
    <tr><td>7</td><td>% of Compensation on Production</td><td align="right"><? echo number_format($parcent_of_compensation_on_production_qty_array,2);?></td></tr> 
    <tr><td>8</td><td>Dyes/Chemical Avg Cost Per Kg</td><td align="right" title="(<?= $dyc_amount_data_array;?>/<?= $batch_weight_data;?>)"><? echo number_format($dyc_amount_data_array  / $batch_weight_data,2);?></td></tr> 
    
    
</table>

<style>
	table tr{font-size:13px;}
</style>




<?
	$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=59 and b.mail_user_setup_id=c.id and a.company_id in($companyStr)";
	$mail_sql2=sql_select($sql2);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}

 	//$to='erpsupport@urmigroup.net';
	$subject="ERP- Monthly Report of ".date("M-Y", strtotime($previous_date))."";
	$message="";
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	//echo $message;
		
?>

