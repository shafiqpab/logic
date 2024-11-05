<?
die;
	include('../includes/common.php');
	$con=connect();
	//echo $con;die;
	$tableNameArr=array();
	//52495
	$po_breakdown_id = "58686"; // ,	,		

	$sql ="SELECT b.COMPANY_ID,b.ITEM_NUMBER_ID,b.COUNTRY_ID,b.SERVING_COMPANY,b.LOCATION,b.PRODUCTION_DATE,b.SEWING_LINE,b.FLOOR_ID,TO_CHAR(b.production_hour,'HH24') as PROD_HOUR, b.PO_BREAK_DOWN_ID,b.REMARKS,a.COLOR_SIZE_BREAK_DOWN_ID as COL_SIZE_ID,a.OPERATOR_ID, a.PRODUCTION_QNTY, a.REJECT_QTY,a.ALTER_QTY, a.SPOT_QTY,a.REPLACE_QTY,b.id as mst_id,a.id as dtls_id
	FROM PRO_GARMENTS_PRODUCTION_DTLS a, PRO_GARMENTS_PRODUCTION_MST b
	WHERE a.mst_id=b.id and b.production_type=5 and a.status_active=1 and b.status_active=1 and b.is_tab=1 and b.PO_BREAK_DOWN_ID in($po_breakdown_id)";
	// echo $sql;die;
	$res=sql_select($sql);
	
	$data_array = array();
	$mst_id_array = array();
	$prev_dtls_mst_id_array = array();
	foreach($res as $v)
	{
		$data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']]['prod_qty'] += $v['PRODUCTION_QNTY'];

		$data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']]['rej_qty'] += $v['REJECT_QTY'];

		$data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']]['alter_qty'] += $v['ALTER_QTY'];

		$data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']]['spot_qty'] += $v['SPOT_QTY'];

		$data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']]['rpls_qty'] += $v['REPLACE_QTY'];

		$data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']]['remarks'] = $v['REMARKS'];
		$mst_id_array[$v['MST_ID']] = $v['MST_ID'];
		$prev_dtls_mst_id_array[$v['DTLS_ID']][$v['MST_ID']] = $v['DTLS_ID'];
	}
	//echo "<pre>";print_r($data_array);die;

	/* $dft_sql ="SELECT b.COMPANY_ID,b.ITEM_NUMBER_ID,b.COUNTRY_ID,b.SERVING_COMPANY,b.LOCATION,b.PRODUCTION_DATE,b.SEWING_LINE,b.FLOOR_ID,TO_CHAR(b.production_hour,'HH24') as PROD_HOUR, b.PO_BREAK_DOWN_ID,b.REMARKS,c.COLOR_SIZE_BREAK_DOWN_ID as COL_SIZE_ID,c.OPERATION_ID,c.DEFECT_TYPE_ID, c.DEFECT_POINT_ID, c.DEFECT_QTY,b.id as mst_id,c.dtls_id
	FROM PRO_GARMENTS_PRODUCTION_MST b,PRO_GMTS_PROD_DFT c
	WHERE b.id=c.mst_id and b.production_type=5 and b.status_active=1 and b.is_tab=1 and b.PO_BREAK_DOWN_ID in($po_breakdown_id)"; */

	$dft_sql ="SELECT b.COMPANY_ID,b.ITEM_NUMBER_ID,b.COUNTRY_ID,b.SERVING_COMPANY,b.LOCATION,b.PRODUCTION_DATE,b.SEWING_LINE,b.FLOOR_ID,TO_CHAR(b.production_hour,'HH24') as PROD_HOUR, b.PO_BREAK_DOWN_ID,b.REMARKS,c.COLOR_SIZE_BREAK_DOWN_ID as COL_SIZE_ID,a.OPERATION_ID,c.DEFECT_TYPE_ID, c.DEFECT_POINT_ID, c.DEFECT_QTY,b.id as mst_id,c.dtls_id
FROM PRO_GARMENTS_PRODUCTION_DTLS a, PRO_GARMENTS_PRODUCTION_MST b,PRO_GMTS_PROD_DFT c
WHERE a.mst_id=b.id and  b.id=c.mst_id and a.id=c.dtls_id and b.production_type=5 and b.status_active=1 and b.is_tab=1 and b.PO_BREAK_DOWN_ID in($po_breakdown_id)";

	$res_dft=sql_select($dft_sql);	
	$dft_data_array = array();
	foreach($res_dft as $v)
	{
		// $dft_data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']][$v['DEFECT_TYPE_ID']][$v['DEFECT_POINT_ID']]['operation_id'] = $v['OPERATION_ID'];

		$dft_data_array[$v['PRODUCTION_DATE']][$v['PROD_HOUR']][$v['COMPANY_ID']][$v['SERVING_COMPANY']][$v['LOCATION']][$v['FLOOR_ID']][$v['SEWING_LINE']][$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COUNTRY_ID']][$v['COL_SIZE_ID']][$v['DEFECT_TYPE_ID']][$v['DEFECT_POINT_ID']][$v['OPERATION_ID']]['defect_qty'] += $v['DEFECT_QTY'];
	}
	// echo "<pre>";
	// print_r($dft_data_array);die;

	$mst_tbl_fields="id, garments_nature, company_id, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, produced_by, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, sewing_line,  production_hour, remarks, floor_id, alter_qnty, reject_qnty, prod_reso_allo, spot_qnty, inserted_by, insert_date";

	$dtls_tbl_fields="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,alter_qty,spot_qty,replace_qty,rectified_qty,color_type_id,updated_by";

	$dft_tbl_fields="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty,color_size_break_down_id,dtls_id,operation_id,inserted_by, insert_date";

	$mst_tbl_data = "";
	$dtls_tbl_data = "";
	$dft_tbl_data = "";
	$i=0;
	$j=0;
	$k=0;
	$new_dtls_mst_id_array = array();
	foreach ($data_array as $date => $date_value) 
	{
		foreach ($date_value as $hour => $hour_value) 
		{
			foreach ($hour_value as $com_id => $com_value) 
			{
				foreach ($com_value as $scom_id => $scom_value) 
				{
					foreach ($scom_value as $loc_id => $loc_value) 
					{
						foreach ($loc_value as $flr_id => $flr_value) 
						{
							foreach ($flr_value as $l_id => $l_value) 
							{
								foreach ($l_value as $po_id => $po_value) 
								{
									foreach ($po_value as $itm_id => $itm_value) 
									{
										foreach ($itm_value as $country_id => $country_value) 
										{
											$mst_id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
											$prod_qty = 0;
											$alter_qty = 0;
											$spot_qty = 0;
											$rej_qty = 0;
											$replace_qty = 0;
											$remarks = "";

											foreach ($country_value as $col_size_id => $col_size_value) 
											{
												$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );

												$new_dtls_mst_id_array[$dtls_id][$mst_id] = $dtls_id;

												$prod_qty += $col_size_value['prod_qty'];//+$col_size_value['rpls_qty'];
												$alter_qty += $col_size_value['alter_qty'];
												$spot_qty += $col_size_value['spot_qty'];
												$rej_qty += $col_size_value['rej_qty'];
												$replace_qty += $col_size_value['rpls_qty'];
												$remarks = $col_size_value['remarks'];

												$defect_data_arr = $dft_data_array[$date][$hour][$com_id][$scom_id][$loc_id][$flr_id][$l_id][$po_id][$itm_id][$country_id][$col_size_id];
												foreach ($defect_data_arr as $dft_type => $dft_value) 
												{
													foreach ($dft_value as $dft_point_id => $point_data) 
													{
														foreach ($point_data as $op_id => $r) 
														{
															$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
															if( $k>0 ) $dft_tbl_data.=",";
															$dft_tbl_data .= "(".$dftSp_id.",".$mst_id.",5,".$po_id.",".$dft_type.",".$dft_point_id.",".$r['defect_qty'].",".$col_size_id.",".$dtls_id.",".$op_id.",898989,'".$date."')";
															$k++;
														}
														
													}
												}
												// ===== prepare dtls tbl data
												// $dtls_tbl_fields="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,alter_qnty,spot_qnty,replace_qty,color_type_id";
												if( $j>0 ) $dtls_tbl_data.=",";
												$dtls_tbl_data .= "(".$dtls_id.",".$mst_id.",5,".$col_size_id.",".$col_size_value['prod_qty'].",".$col_size_value['rej_qty'].",".$col_size_value['alter_qty'].",".$col_size_value['spot_qty'].",".$col_size_value['rpls_qty'].",".$col_size_value['rpls_qty'].",1,898989)";
												$j++;
											}
											// ===== prepare mst tbl data	
											$reporting_hour=date('d-M-Y H:i:s',strtotime($hour.":00"));	
											$reporting_hour="to_date('".$reporting_hour."','DD MONTH YYYY HH24:MI:SS')";

											// if( $i>0 ) $mst_tbl_data.=",";
											// $mst_tbl_data .= "(".$mst_id.",2,".$com_id.",".$po_id.",".$itm_id.",".$country_id.",1,".$scom_id.",".$loc_id.",2,'".$date."',".$prod_qty.",5,3,3,".$l_id.",".$reporting_hour.",'".$remarks."',".$flr_id.",".$alter_qty.",".$rej_qty.",1,".$spot_qty.",898989,'".$date."')";
											$mst_tbl_data .= " INTO pro_garments_production_mst (".$mst_tbl_fields.") VALUES(".$mst_id.",2,".$com_id.",".$po_id.",".$itm_id.",".$country_id.",1,".$scom_id.",".$loc_id.",2,'".$date."',".$prod_qty.",5,3,3,".$l_id.",".$reporting_hour.",'".$remarks."',".$flr_id.",".$alter_qty.",".$rej_qty.",1,".$spot_qty.",898989,'".$date."')";
											$i++;
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	// ==================== prepare piece table data ================================
	/* $i=0;
	foreach ($prev_dtls_mst_id_array as $dtls_id => $d_data) 
	{
		foreach ($d_data as $mst_id => $v) 
		{
			 $sql = "INSERT INTO PRO_GARMENTS_PROD_DTLS_PIECE(ID, MST_ID, DTLS_ID, 
			 PRODUCTION_TYPE, COLOR_SIZE_BREAK_DOWN_ID, PRODUCTION_QNTY, STATUS_ACTIVE, IS_DELETED, REJECT_QTY,DELIVERY_MST_ID, ALTER_QTY,  SPOT_QTY, REPLACE_QTY, DEFECT_QTY, IS_RECTIFIED, RECTIFIED_FROM, OPERATION_ID, UPDATED_BY, UPDATE_DATE, RECTIFIED_QTY) 
			 SELECT ID, MST_ID, DTLS_ID, PRODUCTION_TYPE, COLOR_SIZE_BREAK_DOWN_ID, PRODUCTION_QNTY, STATUS_ACTIVE, IS_DELETED, REJECT_QTY,DELIVERY_MST_ID, ALTER_QTY,  SPOT_QTY, REPLACE_QTY, DEFECT_QTY, IS_RECTIFIED, RECTIFIED_FROM, OPERATION_ID, UPDATED_BY, UPDATE_DATE, RECTIFIED_QTY FROM PRO_GARMENTS_PRODUCTION_DTLS where ";
		}
	} */
	//echo $dft_tbl_data;die;
	// ========== delete prev data =============
	if(count($mst_id_array)>999)
	{
		$chunk_arr = array_chunk($mst_id_array, 999);
		foreach ($chunk_arr as $id_arr)
 		{
			$mst_ids = implode(",",$id_arr);
			$mstDelete = execute_query("UPDATE pro_garments_production_mst set status_active=0,is_deleted=1,updated_by=808080 where id in($mst_ids)", 1);
			$dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls set status_active=0,is_deleted=1,updated_by=808080 where mst_id in($mst_ids)", 1);
			$dftDelete = execute_query("UPDATE pro_gmts_prod_dft set status_active=0,is_deleted=1,updated_by=808080 where mst_id in($mst_ids)", 1);
		}
	}
	else
	{
		$mst_ids = implode(",",$mst_id_array);
		$mstDelete = execute_query("UPDATE pro_garments_production_mst set status_active=0,is_deleted=1,updated_by=808080 where id in($mst_ids)", 1);
		$dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls set status_active=0,is_deleted=1,updated_by=808080 where mst_id in($mst_ids)", 1);
		$dftDelete = execute_query("UPDATE pro_gmts_prod_dft set status_active=0,is_deleted=1,updated_by=808080 where mst_id in($mst_ids)", 1);
	}
	$query="INSERT ALL ".$mst_tbl_data." SELECT * FROM dual";
	// echo "insert into pro_garments_production_mst (".$mst_tbl_fields.") values ".$mst_tbl_data ."<br /><br />";
	// echo "insert into pro_garments_production_dtls (".$dtls_tbl_fields.") values ".$dtls_tbl_data ."<br /><br />";
	// echo "insert into pro_gmts_prod_dft (".$dft_tbl_fields.") values ".$dft_tbl_data ."<br /><br />"; //die;
	//die;
	// ========== data insert here ===============
	$rID=$dtlsrID=true;
	// $rID=sql_insert("pro_garments_production_mst",$mst_tbl_fields,$mst_tbl_data,1);
	$rID=execute_query($query);
	$dtlsrID=sql_insert("pro_garments_production_dtls",$dtls_tbl_fields,$dtls_tbl_data,1);
	$defectme=sql_insert("pro_gmts_prod_dft",$dft_tbl_fields,$dft_tbl_data,1);
	// echo $rID ."&&". $dtlsrID;die;
	if($db_type==2)
	{
		if($rID && $dtlsrID)
		{
			oci_commit($con); 
			echo "Data Successfully Inserted. <br>";
			disconnect($con);
			die;
		}
		else
		{
			oci_rollback($con);
			echo " Data Insert Failed";
			disconnect($con);
			die;
		}
	}
?>