<?
include('../includes/common.php');
$con = connect();
	//, a.booking_dtls_id, a.receive_dtls_id, a.job_dtls_id
	$del_sql ="select a.id,a.sales_order_dtls_id from wo_fabric_aop_dtls a , wo_fabric_aop_mst b where a.mst_id=b.id and sales_order_dtls_id is not null and prod_id is null and b.entry_form=462";
	$del_result=sql_select($del_sql); 
	foreach ($del_result as $rows)
	{
		$sales_order_dtls_id.=$rows[csf("sales_order_dtls_id")].',';
	}
	$sales_order_dtls_id=chop($sales_order_dtls_id,',');
	$sales_order_dtls_id=implode(",",array_unique(explode(",",$sales_order_dtls_id)));

	/*$sql = "select b.id as so_dtls_id,e.id as batch_id,d.id as batch_dtls_id,d.prod_id from 
	wo_pre_cost_fabric_cost_dtls a, fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_booking_mst f , product_details_master  g where a.id=b.pre_cost_fabric_cost_dtls_id and b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id and f.tagged_booking_no = c.sales_booking_no and d.prod_id=g.id and g.item_description= trim(b.fabric_desc) and f.booking_type=3 and f.process=35 and b.id in ($sales_order_dtls_id) and b.body_part_id= d.body_part_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 group by  b.id,e.id,d.id,d.prod_id order by b.id desc";*/

	/*$sql = "select b.id as so_dtls_id,e.id as batch_id,d.id as batch_dtls_id,d.prod_id from 
	fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e, wo_non_ord_samp_booking_mst f, product_details_master g  where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and f.booking_no = c.sales_booking_no and e.color_id=b.color_id and f.booking_type=4 and f.pay_mode=5 and f.supplier_id=c.company_id and b.body_part_id= d.body_part_id  and d.prod_id=g.id and g.item_description= trim(b.fabric_desc) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and b.id in ($sales_order_dtls_id) group by  b.id,e.id,d.id,d.prod_id order by b.id desc";*/

	$sql = "select b.id as so_dtls_id,e.id as batch_id,d.id as batch_dtls_id,d.prod_id from 
		 fabric_sales_order_dtls b, fabric_sales_order_mst c, pro_batch_create_dtls d , pro_batch_create_mst e , product_details_master  g where  b.mst_id=c.id and c.id=d.po_id and d.mst_id=e.id and e.color_id=b.color_id and d.prod_id=g.id and g.item_description= trim(b.fabric_desc) and b.body_part_id= d.body_part_id and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and b.id in ($sales_order_dtls_id)  group by  b.id,e.id,d.id,d.prod_id order by b.id desc";
	$order_result=sql_select($sql); 
	foreach ($order_result as $rows)
	{
		$order_dtls_arr[$rows[csf("so_dtls_id")]]['batch_dtls_id']	.=$rows[csf("batch_dtls_id")].',';
		$order_dtls_arr[$rows[csf("so_dtls_id")]]['prod_id']		.=$rows[csf("prod_id")].',';
	}

	foreach ($del_result as $value) 
	{
		$soDtlsId	=$value[csf('sales_order_dtls_id')];
		$batch_dtls_id 		= $order_dtls_arr[$soDtlsId]['batch_dtls_id'];
		$prod_id 			= $order_dtls_arr[$soDtlsId]['prod_id'];
		
		$batch_dtls_id=chop($batch_dtls_id,',');
		$batch_dtls_id=implode(",",array_unique(explode(",",$batch_dtls_id)));
		$prod_id=chop($prod_id,',');
		$prod_id=implode(",",array_unique(explode(",",$prod_id)));

		if($value[csf('sales_order_dtls_id')]!="")
		{
			$data_array2[$value[csf('sales_order_dtls_id')]]=explode("*",("'".$batch_dtls_id."'*'".$prod_id."'"));
			$hdn_dtls_id_arr[]=$value[csf('sales_order_dtls_id')];
		}
	}
	//echo "<pre>";print_r($data_array2);die;
	$field_array2="batch_dtls_id*prod_id";
	if($data_array2!="")
	{
		//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "id",$field_array2,$data_array2,$hdn_dtls_id_arr);die;
		$rID2=execute_query(bulk_update_sql_statement( "wo_fabric_aop_dtls", "sales_order_dtls_id",$field_array2,$data_array2,$hdn_dtls_id_arr),1);
		if($rID2) $flag=1; else $flag=0;
	}


//echo $rID2.'=='.$flag; die;

if($db_type==2)
{
	
	if($rID2 && $flag)
	{
		oci_commit($con); 
		echo " Update Successful. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo " Update Failed";
		die;
	}
}
?>