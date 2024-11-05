<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
//$con=connect();



	$reportType=1;
	$cbo_company_name='3';
	$txt_date_from='01-Oct-2019';
	$txt_date_to='31-Oct-2019';

	
	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond="and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
	}
	
	$master_data=array();
	$sy = date('Y',strtotime($txt_date_from));
	$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');

	if($reportType==1)
	{
		$exfact_sql=sql_select("select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty, 
		sum(total_carton_qnty) as carton_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id");
		$exfact_qty_arr=$exfact_return_qty_arr=array();
		foreach($exfact_sql as $row)
		{
			$exfact_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_return_qty_arr[$row[csf("po_break_down_id")]]=$row[csf("ex_factory_return_qnty")];
		}
		
	
		$sql= "SELECT b.id as po_id,max(a.lc_sc_no) as lc_sc_arr_no, 
		sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(a.total_carton_qnty) as carton_qnty, a.ex_factory_date as ex_factory_date,  
		
		b.shipment_date, b.po_number, (b.po_quantity*c.total_set_qnty) as po_quantity,(b.unit_price/c.total_set_qnty) as unit_price,b.shiping_status, 
		c.id, c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, to_char(c.insert_date,'YYYY') as year, c.style_ref_no, c.style_description, c.set_smv,c.total_set_qnty
		
		from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c 
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$cbo_company_name' $str_cond  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active in(1,3) and c.is_deleted=0 and c.status_active=1 
		group by 
				b.id , b.shipment_date, b.po_number, b.unit_price,b.po_quantity,b.shiping_status,c.total_set_qnty,c.id,c.company_name, c.buyer_name, c.job_no_prefix_num,c.job_no, c.insert_date, c.style_ref_no, c.style_description,c.total_set_qnty, c.set_smv,a.ex_factory_date
		order by c.buyer_name, b.shipment_date ASC";
		
		$total_po_val=0;
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$total_ex_fact_qty=$exfact_qty_arr[$row[csf("po_id")]];
			$basic_qnty=($total_ex_fact_qty*$row[csf("set_smv")])/$basic_smv_arr[$row[csf("company_name")]];
							
			$master_data[$row[csf("buyer_name")]]['b_id']=$row[csf("buyer_name")];	
			$master_data[$row[csf("buyer_name")]]['po_qnty'] +=$row[csf("po_quantity")];
			$master_data[$row[csf("buyer_name")]]['po_value'] +=$row[csf("po_quantity")]*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['basic_qnty'] +=$basic_qnty;
			$master_data[$row[csf("buyer_name")]]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]];
			$master_data[$row[csf("buyer_name")]]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]-$exfact_return_qty_arr[$row[csf("po_id")]])*$row[csf("unit_price")];
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_qty'] +=$total_ex_fact_qty;
			$master_data[$row[csf("buyer_name")]]['total_ex_fact_value'] +=$total_ex_fact_qty*$row[csf("unit_price")];
		
			$total_po_val+=$row[csf("po_quantity")]*$row[csf("unit_price")];
		
		} 
		
							
		/*foreach($master_data as $rows)
		{
			$total_po_val+=$rows[po_value];
		}*/
							
		
		foreach($master_data as $rows)
		{
			$apiDataArr[]=array(
				BUYER_NAME=>$rows[b_id],
				PO_QTY=>$rows[po_qnty],
				PO_VAL=>$rows[po_value],
				PO_VAL_PER=>number_format(($rows[po_value]/$total_po_val)*100,2,'.',''),
				CURR_EX_FACT_QTY=>$rows[ex_factory_qnty],
				CURR_EX_FACT_VAL=>$rows[ex_factory_value],
				TOT_EX_FACT_QTY=>$rows[total_ex_fact_qty],
				TOT_EX_FACT_VAL=>$rows[total_ex_fact_value],
				TOT_EX_FACT_BASIC_QTY=>$rows[basic_qnty],
				TOT_EX_FACT_VAL_PER=>number_format(($rows[total_ex_fact_value]/$rows[po_value])*100,2)
			);
		
		}
                    
	}
	
	
	var_dump($apiDataArr);
	
	
?>













