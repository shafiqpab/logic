
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
		mysql_query("BEGIN");
		}
		 $category_id=2; $entry_form=68; $prefix='FFRR';


		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		$new_grey_recv_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', $prefix, date("Y",time()), 5,
		"select recv_number_prefix, recv_number_prefix_num from inv_receive_master where company_id=$cbo_company_id and entry_form='$entry_form' 
		and $year_cond=".date('Y',time())." order by id desc", "recv_number_prefix", "recv_number_prefix_num" ));
		$id=return_next_id( "id", "inv_receive_master", 1 ) ;
		$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form,receive_basis, item_category, company_id, receive_date, 
		challan_no,knitting_source, knitting_company, fabric_nature, inserted_by, insert_date";
		$data_array="(".$id.",'".$new_grey_recv_system_id[1]."',".$new_grey_recv_system_id[2].",'".$new_grey_recv_system_id[0]."',$entry_form,9,
		$category_id,".$cbo_company_id.",".$txt_delivery_date.",".$txt_challan_no.",".$cbo_knitting_source.",".$knit_company_id.",2,
		".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$grey_recv_num=$new_grey_recv_system_id[0];
		$grey_update_id=$id;
		
		if($color_id=="") $color_id=0;
		$field_array_trans="id, mst_id, batch_id, company_id, prod_id, item_category, transaction_type, transaction_date, cons_quantity,
		cons_reject_qnty,  inserted_by, insert_date";
		$field_array_dtls="id, mst_id, trans_id, prod_id,body_part_id,fabric_description_id,gsm,width,order_id,receive_qnty,reject_qty,batch_id,
		dia_width_type,barcode_no, color_id,room,rack_no,shelf_no, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no,reject_qnty,qc_pass_qnty,
		inserted_by, insert_date";
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details",1 );
		$i=0;
		$id_dtls=return_next_id( "id", "pro_finish_fabric_rcv_dtls",1);
		$id_roll =return_next_id( "id", "pro_roll_details", 1 );
		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$cur_st_qnty=0;
		$barcodeNos="";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		   $activeId="activeId_".$j;
		   if($$activeId==1)
		   {
		    $rollId="rollId_".$j;
			$rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$rollDia="rolldia_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollQty_".$j;
			$rolldia="rolldia_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$currentWgt="currentWgt_".$j;
			$rejectQty="rejectQty_".$j;
			$wideTypeId="wideTypeId_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
	
			if($data_array_roll!="") $data_array_roll.= ",";
			if($data_array_trans!="") $data_array_trans.= ",";
			if($data_array_dtls!="") $data_array_dtls.= ",";
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_trans.="(".$id_trans.",".$grey_update_id.",".$$batchId.",".$cbo_company_id.",".$$productId.",".$category_id.",1,
			".$txt_delivery_date.",".$$currentWgt.",'".$$rejectQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls.="(".$id_dtls.",".$grey_update_id.",".$id_trans.",".$$productId.",'".$$bodyPart."','".$$deterId."','".$$rollGsm."',
			'".$$rollDia."','".$$orderId."','".$$currentWgt."','".$$rejectQty."','".$$batchId."','".$$wideTypeId."','".$$barcodeNo."','".$$colorId."',
			'".$$room."','".$$rack."','".$$self."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			
			$data_array_roll.="(".$id_roll.",".$grey_update_id.",".$id_dtls.",'".$$orderId."',$entry_form,'".$$rollwgt."','".$$rollId."','".$$rollNo."',
			'".$$barcodeNo."','".$$rejectQty."','".$$currentWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$$orderId."',".$$productId.",'".$$colorId."','".$$currentWgt."',
			".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$prodData_array[$$productId]+=$$currentWgt;
			$barcodeNos.=$j."__".$id_dtls."__".$id_trans."__".$id_roll."__".$$currentWgt.",";
			$all_prod_id.=$$productId.",";
			$id_roll=$id_roll+1;
			$id_prop=$id_prop+1;
			$id_dtls++;
			$id_trans++;
			$i++;
		   }
		}
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_purchased_qnty*current_stock*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$issue_qty;
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
	
		
		$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		if($rID) $flag=1; else $flag=0;
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		if($flag==1) 
		{
		if($prodUpdate) $flag=1; else $flag=0; 
		} 
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
		if($rID3) $flag=1; else $flag=0; 
		} 
		$rID4=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
		if($rID4) $flag=1; else $flag=0; 
		} 
		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		if($flag==1) 
		{
		if($rID5) $flag=1; else $flag=0; 
		} 
		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		if($flag==1) 
		{
		if($rID6) $flag=1; else $flag=0; 
		} 
		//echo $flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
			mysql_query("COMMIT");  
			echo "0**".$grey_update_id."**".$new_grey_recv_system_id[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
			oci_commit($con);  
			echo "0**".$grey_update_id."**".$new_grey_recv_system_id[0]."**".substr($barcodeNos,0,-1);
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
		
	    $category_id=2; $entry_form=68;
		$field_array_update="receive_date*updated_by*update_date";
		$data_array_update="".$txt_delivery_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if($color_id=="") $color_id=0;
		$field_array_trans="id, mst_id, batch_id, company_id, prod_id, item_category, transaction_type, transaction_date, cons_quantity,
		cons_reject_qnty,  inserted_by, insert_date";
		$field_array_dtls="id, mst_id, trans_id, prod_id,body_part_id,fabric_description_id,gsm,width,order_id,receive_qnty,reject_qty,batch_id,
		dia_width_type,barcode_no, color_id,room,rack_no,shelf_no, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no,reject_qnty,qc_pass_qnty,
		inserted_by, insert_date";
		$field_array_proportionate="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,color_id,quantity,inserted_by,insert_date";
		
		$field_array_trans_update="transaction_date*cons_quantity*room*rack*self*updated_by*update_date";
		$field_array_dtls_update="receive_qnty*room*rack_no*shelf_no*updated_by*update_date";
		$field_array_roll_update="qnty* updated_by* update_date";
		$field_array_propo_update="quantity*updated_by*update_date";
		$field_array_trans_remove="updated_by*update_date*status_active*is_deleted";
		$field_array_dtls_remove="updated_by*update_date*status_active*is_deleted";
		$field_array_roll_remove="updated_by* update_date*status_active*is_deleted";
		$field_array_propor_remove="updated_by*update_date*status_active*is_deleted";
		
		$id_prop = return_next_id( "id", "order_wise_pro_details",1 );
		$i=0;
		$id_dtls=return_next_id( "id", "pro_finish_fabric_rcv_dtls",1);
		$id_roll =return_next_id( "id", "pro_roll_details", 1 );
		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$cur_st_qnty=0;
		$barcodeNos="";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		    $activeId="activeId_".$j;
		    $updateDetailsId="updateDetailsId_".$j;
			$transId="transId_".$j;
			$rollTableId="rollTableId_".$j;
		    $rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$rollDia="rolldia_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollQty_".$j;
			$rolldia="rolldia_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$currentWgt="currentWgt_".$j;
			$rejectQty="rejectQty_".$j;
			$wideTypeId="wideTypeId_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			
		   if(str_replace("'","",$$updateDetailsId)!=0)
		   {
			  if($$activeId==1)
				{
				$update_roll_id[]=$$rollTableId;
				$update_array_roll[$$rollTableId]=explode("*",("".$$currentWgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$prodData_array[$$productId]+=$$currentWgt-$$rollwgt;
			//echo 	$prodData_array[$$productId]+=$$currentWgt-$$rollwgt;
		//	echo "**";
				$prodData_issarray[$$productId]+=$$currentWgt;
				
				$update_trans_id[]=$$transId;
				$update_trans_arr[$$transId]=explode("*",("".$txt_delivery_date."*".$$currentWgt."*'".$$room."'*'".$$rack."'*'".$$self."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$update_detl_id[]=$$updateDetailsId;
				$update_array_dtls[$$updateDetailsId]=explode("*",("".$$currentWgt."*'".$$room."'*'".$$rack."'*'".$$self."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$all_prod_id.=$$productId.",";
				
				if(str_replace("'", "", $$transId))
				{
					$update_prop_id[]=$$transId;
					$update_array_prop[$$transId]=explode("*",("".$$currentWgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}

				$barcodeNos.=$j."__".$$updateDetailsId."__".$$transId."__".$$rollTableId."__".$$currentWgt.",";
				}	
			else if(str_replace("'","",$$activeId)==0)
				{
				
					//$stock=return_field_value("current_stock","product_details_master","id=$prod_id[$i]");
					$remove_roll_id[]=$$rollTableId;
					$remove_array_roll[$$rollTableId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					$prodData_array[$$productId]+=-$$currentWgt;
				//echo	$prodData_array[$$productId]+=-$$currentWgt;
				//echo "##";
					$prodData_issarray[$$productId]+=0;
					$all_prod_id.=$$productId.",";
					$remove_trans_id[]=$$transId;
					$remove_trans_arr[$$transId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					$remove_detl_id[]=$$updateDetailsId;
					$remove_array_dtls[$$updateDetailsId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					if(str_replace("'", "", $$transId))
					{
						$remove_prop_id[]=$$transId;
						$remove_array_prop[$$transId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
					}
					$barcodeNos.=$j."__0__0__0__".$$currentWgt.",";
					
				}
			}
			else
			{
				if(str_replace("'","",$$activeId)==1)
				{
			
				if($data_array_roll!="") $data_array_roll.= ",";
				if($data_array_trans!="") $data_array_trans.= ",";
				if($data_array_dtls!="") $data_array_dtls.= ",";
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_trans.="(".$id_trans.",".$update_id.",".$$batchId.",".$cbo_company_id.",".$$productId.",".$category_id.",1,
				".$txt_delivery_date.",".$$currentWgt.",'".$$rejectQty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$id_trans.",".$$productId.",'".$$bodyPart."','".$$deterId."','".$$rollGsm."',
				'".$$rollDia."','".$$orderId."','".$$currentWgt."',".$$rejectQty.",'".$$batchId."','".$$wideTypeId."','".$$barcodeNo."','".$$colorId."',
				'".$$room."','".$$rack."','".$$self."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$data_array_roll.="(".$id_roll.",".$update_id.",".$id_dtls.",'".$$orderId."',$entry_form,'".$$rollwgt."','".$$rollId."','".$$rollNo."',
				'".$$barcodeNo."','".$$rejectQty."','".$$currentWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$data_array_prop.="(".$id_prop.",".$id_trans.",1,$entry_form,".$id_dtls.",'".$$orderId."',".$$productId.",'".$$colorId."','".$$currentWgt."',
				".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$prodData_array[$$productId]+=$$currentWgt;
				//echo $prodData_array[$$productId]+=$$currentWgt;
				//echo "%%";
				$prodData_issarray[$$productId]+=$$currentWgt;
				$barcodeNos.=$j."__".$id_dtls."__".$id_trans."__".$id_roll."__".$$currentWgt.",";
				$all_prod_id.=$$productId.",";
				$id_roll=$id_roll+1;
				$id_prop=$id_prop+1;
				$id_dtls++;
				$id_trans++;
				$i++;	
				}
			}
		}
		//print_r($prodData_array);die;
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_purchased_qnty*current_stock*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$issue_qty;
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_issarray[$row[csf('id')]]."*'".$current_stock."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,0);
		if($rID) $flag=1; else $flag=0;
		
        if(count($data_array_prod_update)>0)
	 	{
			//echo bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$prod_id_array);die;
		$update_product=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$prod_id_array),1);
		if($flag==1) 
		{
		if($update_product) $flag=1; else $flag=0; 
		} 
	 	}

		if($remove_trans_arr!="")
	 	{
			
		$remove_tran=execute_query(bulk_update_sql_statement(" inv_transaction","id",$field_array_trans_remove,$remove_trans_arr,$remove_trans_id),1);
		if($flag==1) 
		{
		if($remove_tran) $flag=1; else $flag=0; 
		} 
	 	}
		 
		 
		if($remove_array_dtls!="")
	 	{
		$remove_grey=execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_array_dtls_remove,$remove_array_dtls,$remove_detl_id),1);
		if($flag==1) 
		{
		if($remove_grey) $flag=1; else $flag=0; 
		} 
	    }
		 
		 
		if($remove_array_roll!="")
	 	{
		$remove_roll=execute_query(bulk_update_sql_statement(" pro_roll_details","id",$field_array_roll_remove,$remove_array_roll,$remove_roll_id),1);
		if($flag==1) 
		{
		if($remove_roll) $flag=1; else $flag=0; 
		} 
	 	}
		
	    if(!empty($remove_array_prop))
	 	{
		$remove_order=execute_query(bulk_update_sql_statement(" order_wise_pro_details","trans_id",$field_array_propor_remove,$remove_array_prop,$remove_prop_id),1);
	    if($flag==1) 
		{
		if($remove_order) $flag=1; else $flag=0; 
		} 
	 	}
			
	//***************************************************************************************************************************************
		
	    if(count($update_array_roll)>0)
	    {
	    $update_roll=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_array_roll_update,$update_array_roll,$update_roll_id),1);
		if($flag==1) 
		{
		if($update_roll) $flag=1; else $flag=0; 
		} 
	 	}
		
		if(count($update_array_dtls)>0)
	 	{
		$update_grey_prod=execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_array_dtls_update,$update_array_dtls,$update_detl_id),1);
		if($flag==1) 
		{
		if($update_grey_prod) $flag=1; else $flag=0; 
		} 
	 	}
		
		if(count($update_trans_arr)>0)
	 	{
		$update_trans=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$update_trans_arr,$update_trans_id),1);
		if($flag==1) 
		{
		if($update_trans) $flag=1; else $flag=0; 
		} 
	 	}
		
		if(count($update_array_prop)>0)
	 	{
		$update_order=execute_query(bulk_update_sql_statement("order_wise_pro_details","trans_id",$field_array_propo_update,$update_array_prop,$update_prop_id),1);
		if($flag==1) 
		{
		if($update_order) $flag=1; else $flag=0; 
		} 
	 	}

		if(count($roll_data_array_update)>0)
		{
		$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $roll_data_array_update, $roll_id_arr ));
		if($flag==1)
		{
		if($rollUpdate) $flag=1; else $flag=0;
		}
		}
			
	
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		if($flag==1) 
		{
		if($prodUpdate) $flag=1; else $flag=0; 
		} 
		
		if(count($data_array_trans)>0)
		{
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
		if($rID3) $flag=1; else $flag=0; 
		} 
		}
		
		if(count($data_array_dtls)>0)
		{
		$rID4=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
		if($rID4) $flag=1; else $flag=0; 
		} 
		}
		if(count($data_array_roll)>0)
		{
		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		if($flag==1) 
		{
		if($rID5) $flag=1; else $flag=0; 
		} 
		}
		
		if(count($data_array_prop)>0)
		{
		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		if($flag==1) 
		{
		if($rID6) $flag=1; else $flag=0; 
		} 
		}
		
		
		//echo $flag;die;
		if($db_type==0)
		{
			if($flag==1)
			{
			mysql_query("COMMIT");  
			echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);;
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "6**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
			oci_commit($con);  
			echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);;
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
}

if($action=="finish_item_details_update")
{
	
	$ext_data=explode("_",$data);
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id
    FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
	$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
	$po_details_array[$row[csf("po_id")]]['job_no_full']=$row[csf("job_no")];
	$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
	$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
	$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
	$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
	$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
	}
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
	$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
	$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
    $issue_roll_arr=array();
	$sql_issue=sql_select("select barcode_no from pro_roll_details where entry_form=71 and status_active=1 and is_deleted=0");
    foreach($sql_issue as $inv)
    {
	$issue_roll_arr[]=$inv[csf('barcode_no')];   
    }

	$inserted_roll=sql_select("select c.id as roll_table_id,b.id as update_dtls_id,b.trans_id,c.barcode_no,b.room,b.rack_no,b.shelf_no,c.qc_pass_qnty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b,pro_roll_details c where  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=68 and c.entry_form=68 and a.id=$ext_data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	$inserted_roll_arr=array();
	$inserted_barcode=array();
	foreach($inserted_roll as $inf)
	{
	$inserted_barcode[]=$inf[csf('barcode_no')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['barcode']=$inf[csf('barcode_no')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['rack']=$inf[csf('rack_no')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['room']=$inf[csf('room')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['self']=$inf[csf('shelf_no')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['qc_pass_qnty']=$inf[csf('qc_pass_qnty')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['roll_table_id']=$inf[csf('roll_table_id')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['update_dtls_id']=$inf[csf('update_dtls_id')];
	$inserted_roll_arr[$inf[csf('barcode_no')]]['trans_id']=$inf[csf('trans_id')];
	}
	//print_r($inserted_barcode);die;
	$insert_roll_cond="";
	//echo count($inserted_barcode);die;
	if(count($inserted_barcode)>0){ $insert_roll_cond=" and barcode_no not in (".implode(",",$inserted_barcode).")"; }
	$all_barcode=sql_select("select barcode_no from pro_roll_details where entry_form=68 $insert_roll_cond and status_active=1 and is_deleted=0");
	$receive_barcode=array();
	foreach($all_barcode as $val)
	{
	$receive_barcode[]=$val[csf("barcode_no")];
	}
	
	$insert_roll_condall="";
	if(count($receive_barcode)>0){ $insert_roll_condall=" and c.barcode_no not in (".implode(",",$receive_barcode).")"; }
    $data_array=sql_select("SELECT b.id as dtls_id,b.product_id,b.color_id,b.job_no,b.order_id,b.bodypart_id,b.construction,b.composition,b.batch_id
	,b.grey_sys_id,b.sys_dtls_id,b.grey_sys_number,b.determination_id, b.gsm,b.dia, c.qnty,b.width_type,c.barcode_no,c.po_breakdown_id,c.id as roll_id,c.roll_no,c.reject_qnty FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and a.entry_form=67 and a.sys_number='$ext_data[0]'  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $insert_roll_condall");

	$roll_details_array=array(); 
	foreach($data_array as $row)
	{
	$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
	$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
	$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("bodypart_id")];
	$roll_details_array[$row[csf("barcode_no")]]['construction']=$row[csf("construction")];
	$roll_details_array[$row[csf("barcode_no")]]['composition']=$row[csf("composition")];
	$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
	$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
	$roll_details_array[$row[csf("barcode_no")]]['dia']=$row[csf("dia")];
	$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
	$roll_details_array[$row[csf("barcode_no")]]['reject_qnty']=$row[csf("reject_qnty")];
	$roll_details_array[$row[csf("barcode_no")]]['width_type']=$row[csf("width_type")];
	$roll_details_array[$row[csf("barcode_no")]]['grey_sys_id']=$row[csf("grey_sys_id")];
	$roll_details_array[$row[csf("barcode_no")]]['sys_dtls_id']=$row[csf("sys_dtls_id")];
	$roll_details_array[$row[csf("barcode_no")]]['grey_sys_number']=$row[csf("grey_sys_number")];
	$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
	$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("product_id")];
	$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
	$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("determination_id")];
	}
	$j=1;
	foreach($roll_details_array as $key=>$b_code)
	{
		
		
   ?>
	 <tr id="tr_1" align="center" valign="middle">
			<td width="40" id="sl_<? echo $j;?>" ><? echo $j;?> &nbsp;&nbsp;
			<?
			$issue_cond='';
		    if(in_array($key,$issue_roll_arr)) $issue_cond="disabled";
		 
			if(in_array($key,$inserted_roll_arr[$key]))
			{
			?>	
			<input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked" <? echo $issue_cond; ?> > 
			<?	
			}
			else
			{
			?>
			<input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]">
			<?
			}
			?>
			</td>
			<td width="80" id="barcode_<? echo $j;?>"><? echo $key;?></td>
			<td width="45" id="rollNo_<? echo $j;?>"><? echo $b_code['roll_no'];?></td>
			<td width="60" id="batchNo_<? echo $j;?>"><? echo $batch_name_array[$b_code['batch_id']];?></td>
			<td width="80" id="bodyPart_<? echo $j;?>" style="word-break:break-all;" align="center"><? echo $body_part[$b_code['body_part_id']];?></td>
			<td width="80" id="cons_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $constructtion_arr[$b_code['deter_id']];?></td>
			<td width="80" id="comps_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $composition_arr[$b_code['deter_id']];?></td>
			<td width="70" id="color_<? echo $j;?>"><? echo $color_arr[$b_code['color_id']];?></td>
			<td width="40" id="gsm_<? echo $j;?>"><? echo $b_code['gsm'];?></td>
			<td width="40" id="dia_<? echo $j;?>"><? echo $b_code['dia'];?></td>
			<td width="50" id="rollWgt_<? echo $j;?>"><input type="text" id="currentQty_1" class="text_boxes_numeric" 
            value="<? if(in_array($key,$inserted_roll_arr[$key])) echo $inserted_roll_arr[$key]['qc_pass_qnty']; 
			          else   echo $b_code['qnty'];?>" style="width:35px" name="currentQty[]" <? echo $issue_cond; ?>/>
            </td>
			<td width="50" id="rejectQty_<? echo $j;?>"><? echo $b_code['reject_qnty'];?></td>
			
			<td width="50" id="room_1">
			<input type="text" id="roomName_<? echo $j;?>" class="text_boxes_numeric"  style="width:35px" name="roomName[]" value="<? if(in_array($key,$inserted_roll_arr[$key])) echo  $inserted_roll_arr[$key]['room']; ?>" onBlur="copy_all('<?  echo $j."_0"; ?>')" <? echo $issue_cond; ?>/>
			</td>
			<td width="50" id="rack_1">
			<input type="text" id="rackName_<? echo $j;?>" class="text_boxes"  style="width:35px" name="rackName[]" value="<? if(in_array($key,$inserted_roll_arr[$key])) echo  $inserted_roll_arr[$key]['rack']; ?>" onBlur="copy_all('<?  echo $j."_1"; ?>')" <? echo $issue_cond; ?>/>
			</td>
			<td width="50" id="self_1">
			<input type="text" id="selfName_<? echo $j;?>" class="text_boxes_numeric"  style="width:35px" name="selfName[]" value="<? if(in_array($key,$inserted_roll_arr[$key])) echo  $inserted_roll_arr[$key]['self']; ?>" onBlur="copy_all('<?  echo $j."_2"; ?>')" <? echo $issue_cond; ?>/>
			</td>
			<td width="60" id="wideType_<? echo $j;?>"><? echo $fabric_typee[$b_code['width_type']];?></td>
			<td width="45" id="year_<? echo $j;?>" align="center"><? echo $po_details_array[$b_code['po_breakdown_id']]['year']; ?></td>
			<td width="45" id="job_<? echo $j;?>"><? echo $po_details_array[$b_code['po_breakdown_id']]['job_no']; ?></td>
			<td width="65" id="buyer_<? echo $j;?>"><? echo $po_details_array[$b_code['po_breakdown_id']]['buyer_name']; ?></td>
			<td width="80" id="order_<? echo $j;?>" style="word-break:break-all;" align="center"><? echo $po_details_array[$b_code['po_breakdown_id']]['po_number']; ?></td>
			<td width="60" id="prodId_<? echo $j;?>"><? echo $b_code['prod_id'];?></td>
			<td width="" id="systemId_<? echo $j;?>" style="word-break:break-all;"><? echo $b_code['grey_sys_number'];?>
            <input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $j;?>" value="<? echo $key; ?>"/>
            <input type="hidden" name="productionId[]" id="productionId_<? echo $j;?>" value="<? echo $b_code['grey_sys_id']; ?>"/>
            <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $j;?>" value="<? echo $b_code['sys_dtls_id']; ?>"/>
            <input type="hidden" name="deterId[]" id="deterId_1" value="<? echo $b_code['deter_id']; ?>"/>
            <input type="hidden" name="productId[]" id="productId_<? echo $j;?>" value="<? echo $b_code['prod_id']; ?>"/>
            <input type="hidden" name="orderId[]" id="orderId_<? echo $j;?>" value="<? echo $b_code['po_breakdown_id']; ?>"/>
            <input type="hidden" name="rollId[]" id="rollId_<? echo $j;?>" value="<? echo $b_code['roll_id']; ?>"/>
            <input type="hidden" name="rollQty[]" id="rollQty_<? echo $j;?>"  value="<?   if(in_array($key,$inserted_roll_arr[$key])) 
			echo $inserted_roll_arr[$key]['qc_pass_qnty']; else   echo $b_code['qnty'];?>" />
            <input type="hidden" name="batchID[]" id="batchID_<? echo $j;?>"  value="<? echo $b_code['batch_id']; ?>" />
            <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $b_code['body_part_id']; ?>"/> 
            <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $b_code['color_id']; ?>"/> 
            <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" 
            value="<? if(in_array($key,$inserted_roll_arr[$key])) echo  $inserted_roll_arr[$key]['update_dtls_id']; else echo 0; ?>" /> 
            <input type="hidden" name="transId[]" id="transId_<? echo $j; ?>" 
             value="<? if(in_array($key,$inserted_roll_arr[$key])) echo  $inserted_roll_arr[$key]['trans_id']; else echo 0; ?>" /> 
            <input type="hidden" name="rollTableId[]" id="rollTableId_<? echo $j; ?>" 
            value="<? if(in_array($key,$inserted_roll_arr[$key])) echo  $inserted_roll_arr[$key]['roll_table_id']; else echo 0; ?>"/> 
            <input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $j; ?>"  value="<? echo $b_code['width_type']; ?>"/> 
            <input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $j; ?>"  value="<? echo $po_details_array[$b_code['po_breakdown_id']]['job_no_full']; ?>"/> 
             <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>"  value="<? echo $po_details_array[$b_code['po_breakdown_id']]['buyer_id']; ?>"/>
             <input type="hidden" value="<? echo $j; ?>" id="txt_tr_length" name="txt_tr_length" />
		 </td>  
	</tr>
	<?
	$j++;
	}
	echo "<input type='hidden' value='<? echo $j-1; ?>' id='txt_tr_length' name='txt_tr_length' />";
}

if($action=="finish_item_details")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
	$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
	$po_details_array[$row[csf("po_id")]]['job_no_full']=$row[csf("job_no")];
	$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
	$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
	$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
	$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
	$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
	}
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
	$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
	$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$inserted_roll=sql_select("select barcode_no from pro_roll_details c where entry_form=68  and status_active=1 and is_deleted=0");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
	 $inserted_roll_arr[]=$inf[csf('barcode_no')];
	}
    $data_array=sql_select("SELECT b.id as dtls_id,b.product_id,b.color_id,b.job_no,b.order_id,b.bodypart_id,b.construction,b.composition,b.batch_id
	,b.grey_sys_id,b.sys_dtls_id,b.grey_sys_number,b.determination_id, b.gsm,b.dia, c.qnty,b.width_type,c.barcode_no,c.po_breakdown_id,c.id as roll_id,c.roll_no,c.reject_qnty FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and a.entry_form=67 and a.sys_number='$data'  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

	$roll_details_array=array(); 
	foreach($data_array as $row)
	{
	$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
	$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
	$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("bodypart_id")];
	$roll_details_array[$row[csf("barcode_no")]]['construction']=$row[csf("construction")];
	$roll_details_array[$row[csf("barcode_no")]]['composition']=$row[csf("composition")];
	$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
	$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
	$roll_details_array[$row[csf("barcode_no")]]['dia']=$row[csf("dia")];
	$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
	$roll_details_array[$row[csf("barcode_no")]]['reject_qnty']=$row[csf("reject_qnty")];
	$roll_details_array[$row[csf("barcode_no")]]['width_type']=$row[csf("width_type")];
	$roll_details_array[$row[csf("barcode_no")]]['grey_sys_id']=$row[csf("grey_sys_id")];
	$roll_details_array[$row[csf("barcode_no")]]['sys_dtls_id']=$row[csf("sys_dtls_id")];
	$roll_details_array[$row[csf("barcode_no")]]['grey_sys_number']=$row[csf("grey_sys_number")];
	$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
	$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("product_id")];
	$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
	$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("determination_id")];
	}
	$j=1;
 	foreach($roll_details_array as $key=>$b_code)
	{
	if(!in_array($key,$inserted_roll_arr))
	{
   ?>
	 <tr id="tr_1" align="center" valign="middle">
        <td width="40" id="sl_<? echo $j;?>" ><? echo $j;?> &nbsp;&nbsp;
        <input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked">
        </td>
        <td width="80" id="barcode_<? echo $j;?>"><? echo $key;?></td>
        <td width="45" id="rollNo_<? echo $j;?>"><? echo $b_code['roll_no'];?></td>
        <td width="60" id="batchNo_<? echo $j;?>"><? echo $batch_name_array[$b_code['batch_id']];?></td>
        <td width="80" id="bodyPart_<? echo $j;?>" style="word-break:break-all;" align="center"><? echo $body_part[$b_code['body_part_id']];?></td>
        <td width="80" id="cons_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $constructtion_arr[$b_code['deter_id']];?></td>
        <td width="80" id="comps_<? echo $j;?>" style="word-break:break-all;" align="left"><? echo $composition_arr[$b_code['deter_id']];?></td>
        <td width="70" id="color_<? echo $j;?>"><? echo $color_arr[$b_code['color_id']];?></td>
        <td width="40" id="gsm_<? echo $j;?>"><? echo $b_code['gsm'];?></td>
        <td width="40" id="dia_<? echo $j;?>"><? echo $b_code['dia'];?></td>
        <td width="50" id="rollWgt_<? echo $j;?>">
        <input type="text" id="currentQty_1" class="text_boxes_numeric" value="<? echo $b_code['qnty'];?>" style="width:35px" name="currentQty[]" />
        </td>
        <td width="50" id="rejectQty_<? echo $j;?>"><? echo $b_code['reject_qnty'];?></td>
        <td width="50" id="room_1"><input type="text" id="roomName_<? echo $j;?>" class="text_boxes_numeric"  style="width:35px" name="roomName[]" onBlur="copy_all('<?  echo $j."_0"; ?>')"/></td>
        <td width="50" id="rack_1"><input type="text" id="rackName_<? echo $j;?>" class="text_boxes"  style="width:35px" name="rackName[]" onBlur="copy_all('<?  echo $j."_1"; ?>')"/></td>
        <td width="50" id="self_1"><input type="text" id="selfName_<? echo $j;?>" class="text_boxes_numeric"  style="width:35px" name="selfName[]" onBlur="copy_all('<?  echo $j."_2"; ?>')"/></td>
        <td width="60" id="wideType_<? echo $j;?>"><? echo $fabric_typee[$b_code['width_type']];?></td>
        <td width="45" id="year_<? echo $j;?>" align="center"><? echo $po_details_array[$b_code['po_breakdown_id']]['year']; ?></td>
        <td width="45" id="job_<? echo $j;?>"><? echo $po_details_array[$b_code['po_breakdown_id']]['job_no']; ?></td>
        <td width="65" id="buyer_<? echo $j;?>"><? echo $po_details_array[$b_code['po_breakdown_id']]['buyer_name']; ?></td>
        <td width="80" id="order_<? echo $j;?>" style="word-break:break-all;" align="center">
        <? echo $po_details_array[$b_code['po_breakdown_id']]['po_number']; ?>
        </td>
        <td width="60" id="prodId_<? echo $j;?>"><? echo $b_code['prod_id'];?></td>
        <td width="" id="systemId_<? echo $j;?>" style="word-break:break-all;"><? echo $b_code['grey_sys_number'];?>
        <input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $j;?>" value="<? echo $key; ?>"/>
        <input type="hidden" name="productionId[]" id="productionId_<? echo $j;?>" value="<? echo $b_code['grey_sys_id']; ?>"/>
        <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $j;?>" value="<? echo $b_code['sys_dtls_id']; ?>"/>
        <input type="hidden" name="deterId[]" id="deterId_1" value="<? echo $b_code['deter_id']; ?>"/>
        <input type="hidden" name="productId[]" id="productId_<? echo $j;?>" value="<? echo $b_code['prod_id']; ?>"/>
        <input type="hidden" name="orderId[]" id="orderId_<? echo $j;?>" value="<? echo $b_code['po_breakdown_id']; ?>"/>
        <input type="hidden" name="rollId[]" id="rollId_<? echo $j;?>" value="<? echo $b_code['roll_id']; ?>"/>
        <input type="hidden" name="rollQty[]" id="rollQty_<? echo $j;?>"  value="<? echo $b_code['qnty'];?>" />
        <input type="hidden" name="batchID[]" id="batchID_<? echo $j;?>"  value="<? echo $b_code['batch_id']; ?>" />
        <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $b_code['body_part_id']; ?>"/> 
        <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $b_code['color_id']; ?>"/> 
        
        <input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $j; ?>"  value="<? echo $b_code['width_type']; ?>"/> 
        <input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $j; ?>"  
        value="<? echo $po_details_array[$b_code['po_breakdown_id']]['job_no_full']; ?>"/> 
        <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>"  
        value="<? echo $po_details_array[$b_code['po_breakdown_id']]['buyer_id']; ?>"/>
        
        <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>"  value="0" /> 
        <input type="hidden" name="transId[]" id="transId_<? echo $j; ?>" value="0" /> 
        <input type="hidden" name="rollTableId[]" id="rollTableId_<? echo $j; ?>"  value="0"/> 
        <input type="hidden" value="<? echo $j; ?>" id="txt_tr_length" name="txt_tr_length" />
        
        
		</td>  
	</tr>
	<?
	$j++;
	}
 }
 echo "<input type='hidden' value='<? echo $j-1; ?>' id='txt_tr_length' name='txt_tr_length' />";
}

if($action=="load_php_form_update")
{

	$sql=sql_select("select  a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date,a.challan_no,a.recv_number,a.company_id, a.receive_basis,a.knitting_source,a.knitting_company,a.receive_date
	from  inv_receive_master a where  a.id=$data ");
	//echo $sql;die;
	foreach($sql as $val)
	{
	if($val[csf('knitting_source')]==1) $knit_comp=$company_arr[$val[csf('knitting_company')]]; 
	else $knit_comp=$supllier_arr[$val[csf('knitting_company')]];
	echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
	echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("knitting_source")])."';\n"; 
	echo "document.getElementById('knit_company_id').value  = '".($val[csf("knitting_company")])."';\n";  
	echo "document.getElementById('txt_knitting_company').value  = '".$knit_comp."';\n"; 
	echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	echo "document.getElementById('txt_delivery_date').value  = '".change_date_format(($val[csf("receive_date")]))."';\n";
	}
}

if($action=="load_php_form")
{
	$sql=sql_select("select  a.id,a.sys_number,a.company_id,a.knitting_source,a.knitting_company,a.sys_number_prefix_num,a.delevery_date
	from pro_grey_prod_delivery_mst a
    where  a.entry_form=67   and a.sys_number='$data'  order by sys_number");
	foreach($sql as $val)
	{
	if($val[csf('knitting_source')]==1) $knit_comp=$company_arr[$val[csf('knitting_company')]]; 
	else $knit_comp=$supllier_arr[$val[csf('knitting_company')]];
	echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
	echo "document.getElementById('txt_knitting_company').value  = '".$knit_comp."';\n"; 
	echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("knitting_source")])."';\n"; 
	echo "document.getElementById('knit_company_id').value  = '".($val[csf("knitting_company")])."';\n";  
	echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	}
}

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

<script>

	function js_set_value(data,id)
	{
	$('#hidden_challan_no').val(data);
	$('#hidden_challan_id').val(id);
	parent.emailwindow.hide();
	}

</script>

</head>
<body>
<div align="center" style="width:760px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Delivery Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Challan No</th>
                    <th>
                    <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
                    <input type="hidden" name="hidden_challan_id" id="hidden_challan_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
					<?
					$search_by_arr=array(1=>"System No");
					$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
					echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
                    ?>
                    </td>     
                    <td align="center" id="search_by_td">				
                    <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', 'finish_feb_roll_receive_by_store_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                   </td>
             </tr>
             <tr>
                  <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
         <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
	</fieldset>
</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	if($company_id==0) { echo "Please Select Company First."; die; }
	if($start_date!="" && $end_date!="")
	{
	if($db_type==0)
	{
	$date_cond="and delevery_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and 
	'".change_date_format(trim($end_date),       "yyyy-mm-dd", "-")."'";
	}
	else
	{
	$date_cond="and delevery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
	}
	}
	else
	{
	$date_cond="";
	}
	$search_field_cond="";
	if(trim($data[0])!="")
	{
	if($search_by==1) $search_field_cond="and sys_number like '$search_string'";
	}
	if($db_type==0) 
	{
	$year_field=" YEAR(insert_date) as year";
	}
	else if($db_type==2) 
	{
	$year_field=" to_char(insert_date,'YYYY') as year";
	}
	else $year_field="";
	$data_array=sql_select("SELECT c.barcode_no,a.sys_number FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
	WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and a.entry_form=67  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$challan_barcode=array();
	$inserted_barcode=array();
	foreach($data_array as $val)
	{
	$challan_barcode[$val[csf('sys_number')]][]=$val[csf('barcode_no')];
	}
	$inserted_roll=sql_select("select b.challan_no,a.barcode_no from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=68 and b.entry_form=68");
	foreach($inserted_roll as $b_id)
	{
	$inserted_barcode[$b_id[csf('challan_no')]][]=$b_id[csf('barcode_no')];	
	}
	$sql="select  a.id,a.sys_number,a.company_id,a.knitting_source,a.knitting_company,a.sys_number_prefix_num,a.delevery_date,$year_field
	from pro_grey_prod_delivery_mst a
	where  a.entry_form=67  and  company_id=$company_id $search_field_cond $date_cond order by sys_number";
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">System No</th>
            <th width="70">Year</th>
            <th width="120">Prod. Source</th>
            <th width="140">Dye/Finishing Company</th>
            <th>Delivery date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
        $i=1;
        foreach ($result as $row)
		{ 
		if(count($challan_barcode[$row[csf('sys_number')]])-count($inserted_barcode[$row[csf('sys_number')]])>0)
		{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
		$knit_comp="&nbsp;";
		if($row[csf('knitting_source')]==1) $knit_comp=$company_arr[$row[csf('knitting_company')]]; 
		else $knit_comp=$supllier_arr[$row[csf('knitting_company')]];
		$data_all=$row[csf('sys_number')]."_".$row[csf('company_id')]."_".$row[csf('knitting_source')]."_".$row[csf('knitting_company')]."_".$knit_comp;
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data_all; ?>','<? echo $row[csf('id')]; ?>');"> 
			<td width="40"><? echo $i; ?></td>
			<td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
			<td width="80" align="center"><p>&nbsp;<? echo $row[csf('sys_number_prefix_num')]; ?></p></td>
			<td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
			<td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
			<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
			<td align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
		</tr>
		<?
		$i++;
		}
	}
    ?>
    </table>
</div>
<?	
exit();
}

if($action=="check_challan_no")
{
	

    $data_array=sql_select("SELECT c.barcode_no FROM pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c 
	WHERE  a.id=b.mst_id and b.id=c.dtls_id and  c.entry_form=67 and a.entry_form=67  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sys_number='$data'");
	$inserted_roll=sql_select("select a.barcode_no from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=68 and b.entry_form=68 and b.challan_no='$data' ");
	if(count($data_array)-count($inserted_roll)>0){ echo 1;}
	else{ echo 0; }
	exit();	
}

if($action=="update_system_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    ?> 
<script>
	
	function js_set_value(data,id,challan)
	{
	$('#hidden_receive_no').val(data);
	$('#hidden_update_id').val(id);
	$('#hidden_challan_no').val(challan);
	parent.emailwindow.hide();
	}
</script>

</head>
<body>
<div align="center" style="width:760px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Receive Number Popup</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Receive No</th>
                    <th id="" width="250">Receive Date</th>
                    <th>
                    <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    <input type="hidden" name="hidden_receive_no" id="hidden_receive_no">  
                    <input type="hidden" name="hidden_update_id" id="hidden_update_id">
                    <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">    
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    <input type="text" style="width:140px" class="text_boxes_numeric"  name="txt_receive_number" id="txt_receive_number" />
					</td>
                    <td align="center">	
                    <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly>To
					<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
                    </td>     
            		<td align="center">
                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_receive_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_update_search_list_view', 'search_div', 'finish_feb_roll_receive_by_store_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if($action=="create_update_search_list_view")
{
	
	$data = explode("_",$data);
	//$search_string="%".trim($data[0]);
	$receive_number=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[0];
	$year_id =$data[4];
	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
	if($db_type==0)
	{
	$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
	}
	else
	{
	$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
	}
	}
	else
	{
	$date_cond="";
	}
	
	$search_field_cond="";
	
	if($db_type==0) 
	{
	$year_field=" YEAR(a.insert_date) as year" ; $year=" and YEAR(insert_date)=$year_id ";
	}
	else if($db_type==2) 
	{
	$year_field=" to_char(a.insert_date,'YYYY') as year";  $year=" and to_char(a.insert_date,'YYYY')=$year_id ";
	}
	else $year_field="";
	if(trim($receive_number)!="")
	{
		$receiv_cond="and a.recv_number_prefix_num='$receive_number' $year ";
	}
	$sql="select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.knitting_source,a.knitting_company,
	a.receive_date,$year_field
	from  inv_receive_master a
	where a.entry_form=68 and a.company_id=$company_id $receiv_cond $date_cond order by  a.recv_number_prefix_num, a.receive_date";
	//echo $sql;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">Receive No</th>
            <th width="70">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th>Receive date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			$knit_comp="&nbsp;";
			if($row[csf('knitting_source')]==1)
			$knit_comp=$company_arr[$row[csf('knitting_company')]]; 
			else
			$knit_comp=$supllier_arr[$row[csf('knitting_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('challan_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
<?	
exit();
}

if($action=="finish_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Finish Fabric Roll Receive</u></strong></td>
			</tr>
            <tr>
				<td align="center" style="font-size:18px"><strong><u>Receive No <? echo $txt_challan_no; ?></u></strong></td>
			</tr>
        </table> 
        <br>
        <?
			$sql_data= sql_select("select a.challan_no,a.recv_number,a.company_id,a.knitting_source,a.knitting_company,a.receive_date
	        from  inv_receive_master a
	        where a.entry_form=68 and a.company_id=$company");
		
		?>
        
        
		<table width="1110" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Challan No</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
                <td width="200"  align=""><? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
                <td style="font-size:16px; font-weight:bold;" width="100">Company</td>
                <td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Prod. Source</td>
                <td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Dye/Finishing Company</td>
                <td width="200">:&nbsp;
                 <?
				 if($sql_data[0][csf('knitting_source')]==1) echo  $company_array[$sql_data[0][csf('knitting_company')]]['name'];
				 else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
				 ?>
				</td>
                
			</tr>
            <tr>
			<td width="" id="barcode_img_id"  colspan="2"></td>	
              
		    </tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="90">Barcode No</th>
                    <th width="60">Batch No</th>
                    <th width="70">Order No</th>
                    <th width="70">Buyer <br> Job</th>
                    <!--<th width="70">Knitting Source</th>-->
                    <th width="70">Prod. Source</th>
                    <th width="90">Dye/Finishing Company</th>
                    <th width="50">Product Id</th>
                    <th width="70">Body Part</th>
                    <th width="130">Fabric Type</th>
                    <th width="70"> Color</th>
                   
                    <th width="50">GSM</th>
                    <th width="40">Dia</th>
                    <th width="60">Dia/Width Type</th>
                    <th width="40">Room</th>
                    <th width="60">Rack<br> Self</th>
                    <th width="40">Roll No</th>
                    <th width="40">Reject Qty</th>   	
                    <th>QC Pass Qty</th>
                </tr>
            </thead>
            <?
			
	$data_array=sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0");
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf("knitting_company")]]['name'];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}
	
	}
		
			 $i=1; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			 $sql_update=sql_select("select  b.id,b.order_id,b.body_part_id,b.batch_id,b.width,b.dia_width_type,b.fabric_description_id,b.gsm,
			 b.prod_id,b.color_id,b.room,b.rack_no,b.shelf_no,c.roll_no,c.barcode_no,c.qc_pass_qnty,c.reject_qnty
			 
			 from pro_finish_fabric_rcv_dtls b,pro_roll_details c
			 where  b.id=c.dtls_id and b.mst_id=$update_id and  c.entry_form=68 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.roll_no");
			 
				foreach($sql_update as $row)
				{
				
				?>
                	<tr>
                        <td width="30"><? echo $i; ?></td>
                        <td width="90" style="word-break:break-all;"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="60" style="word-break:break-all;" align="center"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
                        <td width="90" style="word-break:break-all;" align="center"><? echo $job_array[$row[csf('order_id')]]['po']; ?></td>
                        <td width="60" style="word-break:break-all;" align="center"><? echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer']]."<br>".$job_array[$row[csf('order_id')]]['job']; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_source']; ?></td>
                 
                        <td width="100" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_company']; ?></td>
                       <!-- <td width="70" style="word-break:break-all;"><?echo $knitting_source[$row[csf("knitting_source")]]; ?></td>-->
                        <td width="70" align="center"><? echo $row[csf("prod_id")]; ?></td>
                        <td width="50" style="word-break:break-all;" align="center"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
                        <td width="70" style="word-break:break-all;" align="center"><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></td>
                        <td width="60" style="word-break:break-all;" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="50" align="center" ><? echo  $row[csf('gsm')];  ?></td>
                        <td width="40" align="center"><? echo $row[csf('width')]; ?></td>
                        
                        <td width="50" style="word-break:break-all;" align="center"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("room")]; ?></td>
                        <td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('rack_no')]."<br>".$row[csf('shelf_no')]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
                        <td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('reject_qnty')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
                    </tr>
                <?
					$tot_qty+=$row[csf('qc_pass_qnty')];
					$i++;
				}
			?>
            <tr> 
                <td align="right" colspan="18"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
          
		</table>
	</div>
    <? echo signature_table(16, $company, "1210px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
<?
exit();
}



if($action=="fabric_details_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number,a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
	}
	//print_r($job_array);
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Finish Fabric Roll Receive</u></strong></td>
			</tr>
            <tr>
				<td align="center" style="font-size:18px"><strong><u>Receive No <? echo $txt_challan_no; ?></u></strong></td>
			</tr>
        </table> 
        <br>
        <?
			$sql_data= sql_select("select a.challan_no,a.recv_number,a.company_id,a.knitting_source,a.knitting_company,a.receive_date
	        from  inv_receive_master a
	        where a.entry_form=68 and a.company_id=$company");
		
		?>
        
        
		<table width="1110" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Challan No</td>
                <td width="200">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
                <td width="200"  align=""><? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
                <td style="font-size:16px; font-weight:bold;" width="100">Company</td>
                <td width="200">:&nbsp;<? echo $company_array[$company]['name']; ?></td>
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Prod. Source</td>
                <td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('knitting_source')]]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="150">Dye/Finishing Company</td>
                <td width="200">:&nbsp;
                 <?
				 if($sql_data[0][csf('knitting_source')]==1) echo  $company_array[$sql_data[0][csf('knitting_company')]]['name'];
				 else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
				 ?>
				</td>
                
			</tr>
            <tr>
			<td width="" id="barcode_img_id"  colspan="2"></td>	
              
		    </tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1150" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="60">Batch No</th>
                    <th width="80">Order No</th>
                    <th width="100">Buyer <br> Job</th>
                    <th width="50">Product Id</th>
                    <th width="80">Body Part</th>
                    <th width="150">Fabric Type</th>
                    <th width="70"> Color</th>
                    <th width="50">GSM</th>
                    <th width="40">Dia</th>
                    <th width="70">Dia/Width Type</th>
                    <th width="40">Room</th>
                    <th width="60">Rack</th>
                     <th width="40">Self</th>
                    <th width="40">Roll No</th>
                    <th width="40">Reject Qty</th>   	
                    <th>QC Pass Qty</th>
                </tr>
            </thead>
            <?
			
	$data_array=sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0");
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf("knitting_company")]]['name'];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
		}
	
	}
		
			 $i=1; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			
			 $sql_update=sql_select("select  b.order_id,b.body_part_id,b.batch_id,b.width,b.dia_width_type,b.fabric_description_id,b.gsm,
			 b.prod_id,b.color_id,b.room,b.rack_no,b.shelf_no,count(c.id) as no_of_roll,sum(c.qc_pass_qnty) as qc_pass_qnty,
			 sum(c.reject_qnty) as reject_qnty
			 from pro_finish_fabric_rcv_dtls b,pro_roll_details c
			 where  b.id=c.dtls_id and b.mst_id=$update_id and  c.entry_form=68 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 
			 and c.is_deleted=0 
			 group by b.order_id,b.body_part_id,b.batch_id,b.width,b.dia_width_type,b.fabric_description_id,b.gsm,
			 b.prod_id,b.color_id,b.room,b.rack_no,b.shelf_no");
			 
				foreach($sql_update as $row)
				{
				
				?>
                	<tr>
                        <td width="30"><? echo $i; ?></td>
                        <td width="60" style="word-break:break-all;" align="center"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
                        <td width="80" style="word-break:break-all;" align="center"><? echo $job_array[$row[csf('order_id')]]['po']; ?></td>
                        <td width="100" style="word-break:break-all;" align="center"><? echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer']]."<br>".$job_array[$row[csf('order_id')]]['job']; ?></td>
                        <td width="50" align="center"><? echo $row[csf("prod_id")]; ?></td>
                        <td width="80" style="word-break:break-all;" align="center"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
                        <td width="150" style="word-break:break-all;" align="center"><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></td>
                        <td width="70" style="word-break:break-all;" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="50" align="center" ><? echo  $row[csf('gsm')];  ?></td>
                        <td width="40" align="center"><? echo $row[csf('width')]; ?></td>
                        <td width="70" style="word-break:break-all;" align="center"><? echo $fabric_typee[$row[csf('dia_width_type')]]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("room")]; ?></td>
                        <td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('rack_no')]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("shelf_no")]; ?></td>
                        <td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
                        <td width="40" style="word-break:break-all;" align="right"><? echo $row[csf('reject_qnty')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
                    </tr>
                <?
					$tot_qty+=$row[csf('qc_pass_qnty')];
					$i++;
				}
			?>
            <tr> 
                <td align="right" colspan="16"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
          
		</table>
	</div>
    <? echo signature_table(16, $company, "1210px"); ?>
     	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
    <?
	
exit();
}



?>
