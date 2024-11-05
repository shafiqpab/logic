<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
extract($_REQUEST);
$permission = $_SESSION['page_permission'];
include('../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if($action == "load_drop_down_buyer")
{
	$sql =  "  select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b  where     a.id = b.buyer_id and a.is_deleted = 0 and a.status_active = 1 and b.tag_company = $data order by a.buyer_name";
	//echo $sql;die;
	echo create_drop_down( "cbo_buyer_name", 120,$sql,'id,buyer_name', 1, '--- Select Buyer ---', 0, "" );

}


if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$txt_despo_product_no 			= str_replace("'","",$txt_despo_product_no);
	$cbo_basis 						= str_replace("'","",$cbo_basis);
	$txt_req_sale_no 				= str_replace("'","",$txt_req_sale_no);
	$txt_pi_no 						= str_replace("'","",$txt_pi_no);
	$txt_mill_code 					= str_replace("'","",$txt_mill_code);
	$cbo_color_id 					= str_replace("'","",$cbo_color_id);
	$txt_style_design 				= str_replace("'","",$txt_style_design);
	$txt_extension 					= str_replace("'","",$txt_extension);
	$txt_spo_number 				= str_replace("'","",$txt_spo_number);
	$hidden_color_type 				= str_replace("'","",$hidden_color_type);
	$construction_id 				= str_replace("'","",$hidden_construction_id);
	$composition_id 				= str_replace("'","",$hidden_composition_id);
	$fin_fab_width_inch 			= str_replace("'","",$txt_finished_fabric_width_inch);
	$txt_final_delivery_date 		= str_replace("'","",$txt_final_delivery_date);
	$txt_order_qnty 				= str_replace("'","",$txt_order_qnty);
	$txt_pp_delivery_date 			= str_replace("'","",$txt_pp_delivery_date);
	$txt_pp_qnty 					= str_replace("'","",$txt_pp_qnty);
	$hidden_determination_id 		= str_replace("'","",$hidden_determination_id);
    if ($operation == 0) // Insert Here
    {
    	if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";

		$new_system_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'WPP', date("Y",time()), 5, "select system_no_prefix, system_no_prefix_num from woven_production_budget_planning_mst where company_id=$cbo_company_name and $date_cond=".date('Y',time())." order by id desc ", "system_no_prefix", "system_no_prefix_num" ));
		
		$id=return_next_id( "id", "woven_production_budget_planning_mst", 1 ) ;
		$field_array="id,system_no_prefix, system_no_prefix_num, system_no, company_id, despo_no, planing_basis, gmt_item_id, req_sales_order_no, req_sales_order_dtls_id, pi_no, mill_code, color_id, style_degin, extension, spo_number, color_type, construction_id, buyer_id, composition_id, finish_type, finished_fabric_width_inch, final_delivery_date, order_qnty, pp_delivery_date, pp_qnty,determination_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array ="(".$id.",'".$new_system_no[1]."',".$new_system_no[2].",'".$new_system_no[0]."',".$cbo_company_name.",'".$txt_despo_product_no."',".$cbo_basis.",".$cbo_gmts_item_id.",'".$txt_req_sale_no."',".$txt_req_sale_dtls_id.",'".$txt_pi_no."','".$txt_mill_code."','".$cbo_color_id."','".$txt_style_design."','".$txt_extension."','".$txt_spo_number."','".$hidden_color_type."','".$construction_id."',".$cbo_buyer_id.",'".$composition_id."',".$cbo_finish_type.",'".$fin_fab_width_inch."','".$txt_final_delivery_date."','".$txt_order_qnty."','".$txt_pp_delivery_date."','".$txt_pp_qnty."','".$hidden_determination_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		$con = connect();
		try
		{
			$rID=sql_insert("woven_production_budget_planning_mst",$field_array,$data_array,0);
			if($rID!=1)
			{
				//throw new Exception($rID);
				throw new Exception('Something Error while Creating woven production budget planning');
				//we need to => throw new Exception($rID)
			}
			oci_commit($con);
			echo "0**".$new_system_no[0]."**".$id;
		}
		catch(Exception $e)
		{
			oci_rollback($con);
			$error_message ="Error: ".$e->getMessage()." in ".$e->getFile()." at line ".$e->getLine();
			$error_message ="insert into woven_production_budget_planning_mst ($field_array) values ".$data_array;
			echo "10**".$error_message;
		}
		disconnect($con);
		die;
     
    }
	else if ($operation == 1) // Update Here
    {
		$field_array="despo_no*planing_basis*gmt_item_id*req_sales_order_no*req_sales_order_dtls_id*pi_no*mill_code*color_id*style_degin*extension*spo_number*color_type*construction_id*buyer_id*composition_id*finish_type*finished_fabric_width_inch*final_delivery_date*order_qnty*pp_delivery_date*pp_qnty*determination_id*updated_by*update_date";

		$data_array="'".$txt_despo_product_no."'*".$cbo_basis."*".$cbo_gmts_item_id."*'".$txt_req_sale_no."'*".$txt_req_sale_dtls_id."*'".$txt_pi_no."'*'".$txt_mill_code."'*'".$cbo_color_id."'*'".$txt_style_design."'*'".$txt_extension."'*'".$txt_spo_number."'*'".$hidden_color_type."'*'".$construction_id."'*".$cbo_buyer_id."*'".$composition_id."'*".$cbo_finish_type."*'".$fin_fab_width_inch."'*'".$txt_final_delivery_date."'*'".$txt_order_qnty."'*'".$txt_pp_delivery_date."'*'".$txt_pp_qnty."'*'".$hidden_determination_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$con = connect();
		try
		{
			$rID=sql_update("woven_production_budget_planning_mst",$field_array,$data_array,"id","".$update_id."",1);
			if($rID!=1)
			{
				throw new Exception('Something Error while updating woven production budget planning');
				//we need to throw throw new Exception($rID)
			}
			oci_commit($con);
			echo "1**".$txt_system_no."**".$update_id;
		}
		catch(Exception $e)
		{
			oci_rollback($con);
			$error_message ="Error: ".$e->getMessage()." in ".$e->getFile()." at line ".$e->getLine();
			echo "10**".$txt_system_no."**".$update_id;
		}
		
        disconnect($con);
        die;
    }
    else if ($operation == 2) // Delete Here
    {
    	$con = connect();
		try
		{
			$rID = execute_query("UPDATE woven_production_budget_planning_mst SET STATUS_ACTIVE=0 , IS_DELETED = 1  WHERE ID = $update_id ");
			if($rID!=1)
			{
				throw new Exception("Something error while deleting breakdown data ");
			}

			$rID1 = execute_query("UPDATE woven_production_budget_planning_dtls SET STATUS_ACTIVE=0 , IS_DELETED = 1  WHERE MST_ID = $update_id ");
			if($rID1!=1)
			{
				throw new Exception("Something error while deleting breakdown data ");
			}

			$rID2 = execute_query("DELETE FROM woven_production_budget_planning_break_down WHERE  MST_ID = $update_id ");
			if($rID2!=1)
			{
				throw new Exception("Something error while deleting breakdown data ");
			}

			$rID3 = execute_query("DELETE FROM woven_production_budget_planning_break_down_dtls WHERE  MST_ID = $update_id ");
			if($rID3!=1)
			{
				throw new Exception("Something error while deleting breakdown data ");
			}

			oci_commit($con);
			echo "2**".$rID."**".$rID1."**".$rID2;
		}
		catch(Exception $e)
		{
			oci_commit($con);
			echo "10**".$rID."**".$rID1."**".$rID2;
		}
    }
}

if($action == "save_update_delete_dtls")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$txt_despo_product_no 			= str_replace("'","",$txt_despo_product_no);
	$warp_plan_data 				= str_replace("'","",$warp_plan_data);
	$weft_plan_data 				= str_replace("'","",$weft_plan_data);
	$update_id 						= str_replace("'","",$update_id);
	$row_num 						= str_replace("'","",$row_num);
	$txt_greige_fabric_width_inch 	= str_replace("'","",$txt_greige_fabric_width_inch);
	$txt_reed 						= str_replace("'","",$txt_reed);
	$txt_reed_space 				= str_replace("'","",$txt_reed_space);
	$txt_required_greige_mtr 		= str_replace("'","",$txt_required_greige_mtr);
	$txt_required_warp_length_mtr 	= str_replace("'","",$txt_required_warp_length_mtr);
	$txt_ground_ends 				= str_replace("'","",$txt_ground_ends);
	$txt_extra_selvedge_ends 		= str_replace("'","",$txt_extra_selvedge_ends);
	$txt_spo_receive_date 			= str_replace("'","",$txt_spo_receive_date);
	$txt_total_ends 				= str_replace("'","",$txt_total_ends);
	$txt_total_allowance 			= str_replace("'","",$txt_total_allowance);
	$txt_previous_status 			= str_replace("'","",$txt_previous_status);
	$txt_balance_qty 				= str_replace("'","",$txt_balance_qty);
	$txt_weave 						= str_replace("'","",$txt_weave);
	$txt_ends_x_pick_greige 		= str_replace("'","",$txt_ends_x_pick_greige);
	$txt_ref 						= str_replace("'","",$txt_ref);
	$dtls_id 						= str_replace("'","",$dtls_id);
	$cbo_template_id 				= str_replace("'","",$cbo_template_id);
	
	
	$product_wise_data = array();
	$warp_plan_data_exp =explode("----",$warp_plan_data);		
	foreach($warp_plan_data_exp as $warp_plan)
	{
		$col_data = explode("____",$warp_plan);
		$col_len = count($col_data);
		$prod_id 	= 0;
		$count_id 	= 0;
		$color_id 	= 0;
		$ald 		= 0;
		$endpat 	= 0;
		$wt 		= 0;
		$cnt 		= 0;
		for ($col = 0; $col < $col_len; $col++ )
		{   
			if($col == 0 )
			{
				$prod_id = $col_data[$col];
			}
			else if($col == 1 )
			{
				$count_id = $col_data[$col];
			}
			else if($col == 2)
			{
				$color_id = $col_data[$col];
			}
			else if($col == 3)
			{
				$ald = $col_data[$col];
			}
			else if($col == $col_len-2)
			{
				$endpat= $col_data[$col];
			}
			else if($col == $col_len-1)
			{
				$wt= $col_data[$col];
			}
			else
			{
				$product_wise_data[$prod_id][1]['col_'.$cnt] = $col_data[$col];
				$cnt++;
			}
			$product_wise_data[$prod_id][1]['count_id'] 	= $count_id;
			$product_wise_data[$prod_id][1]['color_id'] 	= $color_id;
			$product_wise_data[$prod_id][1]['ald'] 			= $ald;
			$product_wise_data[$prod_id][1]['end_pat'] 		= $endpat;
			$product_wise_data[$prod_id][1]['wt_in_kg'] 	= $wt;
			$product_wise_data[$prod_id][1]['total_column'] = $cnt;
		}
	}

	$weft_plan_data_exp =explode("----",$weft_plan_data);		
	foreach($weft_plan_data_exp as $weft_plan)
	{
		$col_data = explode("____",$weft_plan);
		$col_len = count($col_data);
		$prod_id 	= 0;
		$count_id 	= 0;
		$color_id 	= 0;
		$ald 		= 0;
		$endpat 	= 0;
		$wt 		= 0;
		$cnt        = 0;
		for ($col = 0; $col < $col_len; $col++ )
		{   
			if($col == 0 )
			{
				$prod_id = $col_data[$col];
			}
			else if($col == 1 )
			{
				$count_id = $col_data[$col];
			}
			else if($col == 2)
			{
				$color_id = $col_data[$col];
			}
			else if($col == 3)
			{
				$ald = $col_data[$col];
			}
			else if($col == $col_len-2)
			{
				$endpat= $col_data[$col];
			}
			else if($col == $col_len-1)
			{
				$wt= $col_data[$col];
			}
			else
			{
				$product_wise_data[$prod_id][2]['col_'.$cnt] = $col_data[$col];
				$cnt++;
			}
			$product_wise_data[$prod_id][2]['count_id'] 	= $count_id;
			$product_wise_data[$prod_id][2]['color_id'] 	= $color_id;
			$product_wise_data[$prod_id][2]['ald'] 			= $ald;
			$product_wise_data[$prod_id][2]['end_pat'] 		= $endpat;
			$product_wise_data[$prod_id][2]['wt_in_kg'] 	= $wt;
			$product_wise_data[$prod_id][2]['total_column'] = $cnt;
		}
	}

    if ($operation == 0) // Insert Here
    {
		$dtls_id=return_next_id( "id", "woven_production_budget_planning_dtls", 1 ) ;
		$break_down_id=return_next_id( "id", "woven_production_budget_planning_break_down", 1 ) ;
		$break_down_dtls_id=return_next_id( "id", "woven_production_budget_planning_break_down_dtls", 1 ) ;
		$field_array ="id,mst_id,weave,ends_x_pick_greige,ref,greige_fabric_width,reed_count,reed_space,required_greige,required_warp_length,ground_ends,extra_selvedge_ends,spo_receive_date,total_ends,total_allowance,previous_status,balance_qty,warp_plan_data,weft_plan_data,template_id, inserted_by, insert_date, status_active, is_deleted";
		$data_array  = "";

		$field_array_break_down ="id,mst_id,dtls_id,product_id, wrap, weft, total, count_id, color_id, ald, end_pat,wt_in_kg,break_down_type";
		$data_array_break_down  = "";

		$field_array_break_down_dtls ="id,mst_id,dtls_id,break_down_id, column_no, break_down_type, value";
		$data_array_break_down_dtls  = "";

		
		$data_array="(".$dtls_id.",".$update_id.",'".$txt_weave."','".$txt_ends_x_pick_greige."','".$txt_ref."','".$txt_greige_fabric_width_inch."','".$txt_reed."','".$txt_reed_space."','".$txt_required_greige_mtr."','".$txt_required_warp_length_mtr."','".$txt_ground_ends."','".$txt_extra_selvedge_ends."','".$txt_spo_receive_date."','".$txt_total_ends."','".$txt_total_allowance."','".$txt_previous_status."','".$txt_balance_qty."','".$warp_plan_data."','".$weft_plan_data."','".$cbo_template_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		for($row = 1;$row <= $row_num; $row++)
		{
			$product_id="product_id_".$row;
			$product_id=str_replace("'","",$$product_id);
			$dtls_id_="dtls_id_".$row;
			$dtls_id_=str_replace("'","",$$dtls_id_);
			$wrap_kg="wrap_kg_".$row;
			$wrap_kg=str_replace("'","",$$wrap_kg);
			$weft_kg="weft_kg_".$row;
			$weft_kg=str_replace("'","",$$weft_kg);
			$total_kg="total_kg_".$row;
			$total_kg=str_replace("'","",$$total_kg);

			if(empty($dtls_id_) )
			{
				foreach($product_wise_data[$product_id] as $type => $type_data)
				{
					if(!empty($data_array_break_down))
					{
						$data_array_break_down.=",";
					}

					$data_array_break_down.="(".$break_down_id.",".$update_id.",".$dtls_id.",".$product_id.",".$wrap_kg.",".$weft_kg.",".$total_kg.",".$type_data['count_id'].",".$type_data['color_id'].",".$type_data['ald'].",".$type_data['end_pat'].",".$type_data['wt_in_kg'].",".$type.")";

					for($col_no = 0; $col_no < $type_data['total_column'];$col_no++)
					{
						if(!empty($data_array_break_down_dtls))
						{
							$data_array_break_down_dtls.=",";
						}
						$data_array_break_down_dtls.="(".$break_down_dtls_id.",".$update_id.",".$dtls_id.",".$break_down_id.",".$col_no.",".$type.",".$type_data['col_'.$col_no].")";
						$break_down_dtls_id++;
					}
					$break_down_id++;
				}
			}
		}
		
		$con = connect();
		try
		{
			$rID=sql_insert("woven_production_budget_planning_dtls",$field_array,$data_array,0);
			if($rID!=1)
			{
				throw new Exception("Something error while saving details data ");
				//we need to => throw new Exception($rID)
			}
			$rID1=sql_insert("woven_production_budget_planning_break_down",$field_array_break_down,$data_array_break_down,0);
			if($rID1!=1)
			{
				throw new Exception("Something error while saving breakdown data ".$data_array_break_down);
			}
			$rID2=sql_insert("woven_production_budget_planning_break_down_dtls",$field_array_break_down_dtls,$data_array_break_down_dtls,0);
			if($rID2!=1)
			{
				throw new Exception("Something error while saving breakdown details data ");
			}
			oci_commit($con);
			echo "0**".$rID."**".$rID1."**".$rID2;
		}
		catch(Exception $e)
		{
			oci_rollback($con);
			$error_message =$e->getMessage()." in ".$e->getFile()." at line ".$e->getLine();
			echo "10**".$error_message;
			//print_r($e);
		}
		disconnect($con);
		die;
     
    }
	else if ($operation == 1) // Update Here
    {
		
		$break_down_id=return_next_id( "id", "woven_production_budget_planning_break_down", 1 ) ;
		$break_down_dtls_id=return_next_id( "id", "woven_production_budget_planning_break_down_dtls", 1 ) ;
		$field_array ="weave*ends_x_pick_greige*ref*greige_fabric_width*reed_count*reed_space*required_greige*required_warp_length*ground_ends*extra_selvedge_ends*spo_receive_date*total_ends*total_allowance*previous_status*balance_qty*warp_plan_data*weft_plan_data*template_id*updated_by*update_date";
		$data_array  = "";

		$field_array_break_down ="id,mst_id,dtls_id,product_id, wrap, weft, total, count_id, color_id, ald, end_pat,wt_in_kg,break_down_type";
		$data_array_break_down  = "";

		$field_array_break_down_dtls ="id,mst_id,dtls_id,break_down_id, column_no, break_down_type, value";
		$data_array_break_down_dtls  = "";

		
		$data_array="'".$txt_weave."'*'".$txt_ends_x_pick_greige."'*'".$txt_ref."'*'".$txt_greige_fabric_width_inch."'*'".$txt_reed."'*'".$txt_reed_space."'*'".$txt_required_greige_mtr."'*'".$txt_required_warp_length_mtr."'*'".$txt_ground_ends."'*'".$txt_extra_selvedge_ends."'*'".$txt_spo_receive_date."'*'".$txt_total_ends."'*'".$txt_total_allowance."'*'".$txt_previous_status."'*'".$txt_balance_qty."'*'".$warp_plan_data."'*'".$weft_plan_data."'*'".$cbo_template_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		for($row = 1;$row <= $row_num; $row++)
		{
			$product_id="product_id_".$row;
			$product_id=str_replace("'","",$$product_id);
			$wrap_kg="wrap_kg_".$row;
			$wrap_kg=str_replace("'","",$$wrap_kg);
			$weft_kg="weft_kg_".$row;
			$weft_kg=str_replace("'","",$$weft_kg);
			$total_kg="total_kg_".$row;
			$total_kg=str_replace("'","",$$total_kg);

			$dtls_id_="dtls_id_".$row;
			$dtls_id_=str_replace("'","",$$dtls_id_);

			if($dtls_id_ == $dtls_id)
			{
				foreach($product_wise_data[$product_id] as $type => $type_data)
				{
					if(!empty($data_array_break_down))
					{
						$data_array_break_down.=",";
					}

					$data_array_break_down.="(".$break_down_id.",".$update_id.",".$dtls_id.",".$product_id.",".$wrap_kg.",".$weft_kg.",".$total_kg.",".$type_data['count_id'].",".$type_data['color_id'].",".$type_data['ald'].",".$type_data['end_pat'].",".$type_data['wt_in_kg'].",".$type.")";

					for($col_no = 0; $col_no < $type_data['total_column'];$col_no++)
					{
						if(!empty($data_array_break_down_dtls))
						{
							$data_array_break_down_dtls.=",";
						}
						$data_array_break_down_dtls.="(".$break_down_dtls_id.",".$update_id.",".$dtls_id.",".$break_down_id.",".$col_no.",".$type.",".$type_data['col_'.$col_no].")";
						$break_down_dtls_id++;
					}
					$break_down_id++;
				}
			}

			
		}
		
		$con = connect();
		try
		{
			$rID=sql_update("woven_production_budget_planning_dtls",$field_array,$data_array,"id","".$dtls_id."",0);
			if($rID!=1)
			{
				throw new Exception("Something error while updating details data ");
			}
			$rID1 = execute_query("DELETE FROM woven_production_budget_planning_break_down WHERE DTLS_ID = $dtls_id ");
			if($rID1!=1)
			{
				throw new Exception("Something error while updating breakdown data ");
			}
			$rID2=sql_insert("woven_production_budget_planning_break_down",$field_array_break_down,$data_array_break_down,0);
			if($rID2!=1)
			{
				throw new Exception("Something error while updating breakdown data ");
			}
			$rID3 = execute_query("DELETE FROM woven_production_budget_planning_break_down_dtls WHERE DTLS_ID = $dtls_id ");
			if($rID3!=1)
			{
				throw new Exception("Something error while updating breakdown data ");
			}

			$rID4=sql_insert("woven_production_budget_planning_break_down_dtls",$field_array_break_down_dtls,$data_array_break_down_dtls,0);
			if($rID4!=1)
			{
				throw new Exception("Something error while updating breakdown details data ");
			}
			oci_commit($con);
			echo "1**".$rID."**".$rID1."**".$rID2;
		}
		catch(Exception $e)
		{
			oci_rollback($con);
			$error_message =$e->getMessage()." in ".$e->getFile()." at line ".$e->getLine();
			echo "10**".$error_message;
			//print_r($e);
		}
		disconnect($con);
		die;
    }
    else if ($operation == 2) // Delete Here
    {
		$con = connect();
		try
		{
			$rID1 = execute_query("UPDATE woven_production_budget_planning_dtls SET STATUS_ACTIVE=0 , IS_DELETED = 1  WHERE ID = $dtls_id ");
			if($rID1!=1)
			{
				throw new Exception("Something error while deleting breakdown data ");
			}

			$rID1 = execute_query("DELETE FROM woven_production_budget_planning_break_down WHERE DTLS_ID = $dtls_id ");
			if($rID1!=1)
			{
				throw new Exception("Something error while deleting breakdown data ");
			}

			$rID3 = execute_query("DELETE FROM woven_production_budget_planning_break_down_dtls WHERE DTLS_ID = $dtls_id ");
			if($rID3!=1)
			{
				throw new Exception("Something error while deleting breakdown data ");
			}

			oci_commit($con);
			echo "2**".$rID."**".$rID1."**".$rID2;
		}
		catch(Exception $e)
		{
			oci_commit($con);
			echo "10**".$rID."**".$rID1."**".$rID2;
		}

    }
}

if ($action == "warp_plan_popup")
{
	echo load_html_head_contents("Warp Plan", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value()
		{
			var no_of_column 	= $("#no_of_column").val() * 1;
			var product_ids 	= $("#product_ids").val();

			var product_id_arr = product_ids.split(",");

			var data = "";
			var no_of_row = 0;
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				console.log('row='+row);
				if(no_of_row > 0)
				{
					data+="----";
				}
				var row_data = "";
				var count = $("#count_"+row).val();
				var shade = $("#shade_"+row).val();
				var ald   = $("#ald_"+row).val();
				row_data+=row+"____"+count+"____"+shade+"____"+ald;
				for( var col = 1; col <= no_of_column ; col++)
				{
					row_data+="____"+$("#col_"+row+"_"+col).val();
				}
				var endpat = $("#endpat_"+row).val();
				var wt = $("#wt_"+row).val();
				row_data+="____"+endpat+"____"+wt;
				data+=row_data;
				
				no_of_row = Number(no_of_row + 1);
			}
			$('#data').val(data);
			console.log(data);
			//alert(data);
			parent.emailwindow.hide();
		}

		function calculate_end_pat()
		{
			var no_of_column 	= $("#no_of_column").val() * 1;
			var product_ids 	= $("#product_ids").val();

			var product_id_arr = product_ids.split(",");

			var data = "";
			var no_of_row = 0;
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				var sum = 0;
				for( var col = 1; col <= no_of_column ; col++)
				{
					sum = sum + ( $("#col_"+row+"_"+col).val() * 1) ;
				}
				$("#endpat_"+row).val(sum);
			}
			calculate_wt_kg();
		}
		// arrow function
		const set_previous_data = () =>{
			//console.clear();
			//var prod_arr = JSON.parse(data);
			//console.log(prod_arr);	
			var no_of_column 	= $("#no_of_column").val() * 1;
			var product_ids 	= $("#product_ids").val();
			var product_id_arr  = product_ids.split(",");
			var previous_data   = $("#data").val() ;
			previous_data = previous_data.split("----");
			console.log(previous_data);
			
			for (var r = 0; r < Math.min(previous_data.length,product_id_arr.length); r++ )
			{ 
				var col_data = previous_data[r].split("____");
				console.log(col_data);
				var col_len = Math.min(col_data.length,Number(no_of_column+6));
				console.log(col_len);
				var row = col_data[0];
				console.log('row='+row);
				for (var c = 0; c < col_len; c++ )
				{   
					if(c == 0 )
					{
						//$("#count_"+Number(r+1)).val(col_data[c]);
						console.log("#count_"+row);
					}
					else if(c == 1 )
					{
						//$("#count_"+Number(r+1)).val(col_data[c]);
						console.log("#count_"+row);
					}
					else if(c == 2)
					{
						//$("#shade_"+Number(r+1)).val(col_data[c]);
						console.log("#shade_"+row);
					}
					else if(c == 3)
					{
						$("#ald_"+row).val(col_data[c]);
						console.log("#ald_"+row);
					}
					else if(c == Number(col_len-2))
					{
						$("#endpat_"+row).val(col_data[c]);
						console.log("endpat_"+row);
					}
					else if(c == Number(col_len-1))
					{
						$("#wt_"+row).val(col_data[c]);
						console.log("#wt_"+row);
					}
					else
					{
						$("#col_"+row+"_"+Number(c-3)).val(col_data[c]);
						console.log("#col_"+row+"_"+Number(c+1));
					}
				}
			}
			calculate_wt_kg();
		}

		const calculate_wt_kg = () => {
			var v16 = $("#txt_required_warp_length_mtr").val() * 1;
			var v19 = $("#txt_total_ends").val() * 1;
			
			var product_ids 	= $("#product_ids").val();
			var no_of_column 	= $("#no_of_column").val() * 1;

			var product_id_arr = product_ids.split(",");

			var data = "";
			var no_of_row = 0;
			var ad25 = 0;
			var w33 = 0;
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				for( var col = 1; col <= no_of_column ; col++)
				{
					w33 = w33 + ( $("#col_"+row+"_"+col).val() * 1) ;
				}
			}
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				var w25 = 0;
				for( var col = 1; col <= no_of_column ; col++)
				{
					w25 = w25 + ( $("#col_"+row+"_"+col).val() * 1) ;
				}
				ad25 = w33;
				var ac25 = v19;
				var ae25 = ac25 / ad25;
				var z14 = $("#count_value"+row).val() * 1;
				var ag24 = ae25.toFixed(0);
				var z25 = w25*ag24;
				var aa25 = 0;
				var ab25 = z25+aa25;
				var atc_kg = (ab25*0.0005905*v16)/z14;
				$("#wt_"+row).val(atc_kg.toFixed(6));
				//console.log(`atc_kg (${atc_kg}) = (${ab25}*0.0005905*${v16})/${z14}`);
			}
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:<?=$width-10;?>px;">
			<form name="searchsystemidfrm" id="searchsystemidfrm">
				<fieldset style="width:<?=$width-30;?>px;">
					<legend>WARP PLAN</legend>
					<?php 

					$txt_total_ends 				= str_replace("'","",$txt_total_ends) * 1;
					$txt_required_warp_length_mtr   = str_replace("'","",$txt_required_warp_length_mtr) * 1;
					$txt_order_qnty   				= str_replace("'","",$txt_order_qnty) * 1;
					$hidden_warp_prod_id = str_replace("'","",$hidden_warp_prod_id);
					$product_str = implode(",", explode("*,*",$hidden_warp_prod_id));

                    $lib_color   = return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
                    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
                    $count_value = return_library_array("select id, count_value from lib_yarn_count", 'id', 'count_value');
                    $fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
                    $sql = "SELECT a.id,a.lot, a.product_name_details,a.color, a.yarn_count_id from product_details_master a where  a.id in ($product_str)";
					//echo $sql;
					$result = sql_select($sql);

                    $product_arr = array();
                    foreach($result as $row)
                    { 
                    	$product_arr[$row[csf('id')]]['count_id'] = $row[csf('yarn_count_id')];
                    	$product_arr[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
                    	$product_arr[$row[csf('id')]]['color_id'] = $row[csf('color')];
                    	$product_arr[$row[csf('id')]]['color'] = $lib_color[$row[csf('color')]];
                    	$product_arr[$row[csf('id')]]['count_value'] = $count_value[$row[csf('yarn_count_id')]];
                    }

					?>
					<table cellpadding="0" cellspacing="0" width="<?=$width-30;?>" class="rpt_table">
						<thead>
							<tr>
								<th width="35">Sl</th>
								<th width="80">Count</th>
								<th width="80">Shade</th>
								<th width="80">ALD</th>
								<div style="width:480px;overflow-x:scroll;">
									<?php for($col = 1 ; $col <= $column; $col++): ?>
										<th width="80"><?=$col;?></th>
									<?php endfor ?>
								</div>
								<th width="80">Ends/PAT</th>
								<th width="80">WT in KG
									<input type="hidden" id="no_of_column" value="<?=$column;?>">
									<input type="hidden" id="product_ids" value="<?=$product_str;?>">
									<input type="hidden" id="data" value="<?=$plan_data;?>">
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$sl = 1;
							foreach($product_arr as $prod_id => $prod): ?>
							<tr>
								<td><?=$sl++;?></td>
								<td>
									<input type="hidden" name="count_<?=$prod_id;?>" id="count_<?=$prod_id;?>"  value="<?=$prod['count_id']?>">
									<input type="text" name="countname_<?=$prod_id;?>" id="countname_<?=$prod_id;?>" style="width: 60px;" class="text_boxes" value="<?=$prod['count']?>">
									<input type="hidden" name="count_value<?=$prod_id;?>" id="count_value<?=$prod_id;?>" style="width: 60px;" class="text_boxes" value="<?=$prod['count_value']?>">
								</td>
								<td>
									<input type="hidden" name="shade_<?=$prod_id;?>" id="shade_<?=$prod_id;?>" value="<?=$prod['color_id']?>">
									<input type="text" name="shadename_<?=$prod_id;?>" id="shadename_<?=$prod_id;?>" style="width: 60px;" class="text_boxes" value="<?=$prod['color']?>">
								</td>
								<td>
									<input type="text" name="ald_<?=$prod_id;?>" id="ald_<?=$prod_id;?>" style="width: 60px;" class="text_boxes">
								</td>
								<div style="width:480px;overflow-x:scroll;">
									<?php for($col = 1 ; $col <= $column; $col++): ?>
										<td>
											<input type="text" name="col_<?=$prod_id;?>_<?=$col;?>" id="col_<?=$prod_id;?>_<?=$col;?>" style="width: 60px;" class="text_boxes" onkeyup="calculate_end_pat()">
										</td>
									<?php endfor ?>
								</div>
								<td>
									<input type="text" name="endpat_<?=$prod_id;?>" id="endpat_<?=$prod_id;?>" style="width: 60px;" class="text_boxes">
								</td>
								<td>
									<input type="text" name="wt_<?=$prod_id;?>" id="wt_<?=$prod_id;?>" style="width: 60px;" class="text_boxes">
								</td>
							</tr>
							<?php endforeach ?>
						</tbody>
					</table>
					<input type="hidden" id="txt_required_warp_length_mtr" value="<?=$txt_required_warp_length_mtr;?>">
					<input type="hidden" id="txt_total_ends" value="<?=$txt_total_ends;?>">
				</fieldset>
			</form>
		</div>
		<center><input type="text" value="Close" onclick="js_set_value()" class="formbutton" style="width: 70px;justify-content: center;text-align: center;"></center>
	</body>
	<script type="text/javascript">
		set_previous_data();
	</script>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}


if ($action == "weft_plan_popup")
{
	echo load_html_head_contents("Warp Plan", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value()
		{
			var no_of_column 	= $("#no_of_column").val() * 1;
			var product_ids 	= $("#product_ids").val();

			var product_id_arr = product_ids.split(",");

			var data = "";
			var no_of_row = 0;
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				console.log('row='+row);
				if(no_of_row > 0)
				{
					data+="----";
				}
				var row_data = "";
				var count = $("#count_"+row).val();
				var shade = $("#shade_"+row).val();
				var ald   = $("#ald_"+row).val();
				row_data+=row+"____"+count+"____"+shade+"____"+ald;
				for( var col = 1; col <= no_of_column ; col++)
				{
					row_data+="____"+$("#col_"+row+"_"+col).val();
				}
				var endpat = $("#endpat_"+row).val();
				var wt = $("#wt_"+row).val();
				row_data+="____"+endpat+"____"+wt;
				data+=row_data;
				
				no_of_row = Number(no_of_row + 1);
			}
			$('#data').val(data);
			console.log(data);
			//alert(data);
			parent.emailwindow.hide();
		}

		function calculate_end_pat()
		{
			var no_of_column 	= $("#no_of_column").val() * 1;
			var product_ids 	= $("#product_ids").val();

			var product_id_arr = product_ids.split(",");

			var data = "";
			var no_of_row = 0;
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				var sum = 0;
				for( var col = 1; col <= no_of_column ; col++)
				{
					sum = sum + ( $("#col_"+row+"_"+col).val() * 1) ;
				}
				$("#endpat_"+row).val(sum);
				
			}
			calculate_wt_for_weft_kg();
		}
		// arrow function
		const set_previous_data_for_weft = () =>{
			//console.clear();
			//var prod_arr = JSON.parse(data);
			//console.log(prod_arr);	
			var no_of_column 	= $("#no_of_column").val() * 1;
			var product_ids 	= $("#product_ids").val();
			var product_id_arr  = product_ids.split(",");
			var previous_data   = $("#data").val() ;
			previous_data = previous_data.split("----");
			console.log(previous_data);
			
			for (var r = 0; r < Math.min(previous_data.length,product_id_arr.length); r++ )
			{ 
				var col_data = previous_data[r].split("____");
				console.log(col_data);
				var col_len = Math.min(col_data.length,Number(no_of_column+6));
				console.log(col_len);
				var row = col_data[0];
				console.log('row='+row);
				for (var c = 0; c < col_len; c++ )
				{   
					if(c == 0 )
					{
						//$("#count_"+Number(r+1)).val(col_data[c]);
						console.log("#count_"+row);
					}
					else if(c == 1 )
					{
						//$("#count_"+Number(r+1)).val(col_data[c]);
						console.log("#count_"+row);
					}
					else if(c == 2)
					{
						//$("#shade_"+Number(r+1)).val(col_data[c]);
						console.log("#shade_"+row);
					}
					else if(c == 3)
					{
						$("#ald_"+row).val(col_data[c]);
						console.log("#ald_"+row);
					}
					else if(c == Number(col_len-2))
					{
						$("#endpat_"+row).val(col_data[c]);
						console.log("endpat_"+row);
					}
					else if(c == Number(col_len-1))
					{
						$("#wt_"+row).val(col_data[c]);
						console.log("#wt_"+row);
					}
					else
					{
						$("#col_"+row+"_"+Number(c-3)).val(col_data[c]);
						console.log("#col_"+row+"_"+Number(c+1));
					}
				}
			}
			calculate_wt_for_weft_kg();
			
		}

		const calculate_wt_for_weft_kg = () => {
			var v16 = $("#txt_required_warp_length_mtr").val() * 1;
			var d16 = $("#txt_reed_space").val() * 1;
			var aa14 = $("#txt_g_pick").val() * 1;
			var v15 = $("#txt_reed").val() * 1;
			
			var product_ids 	= $("#product_ids").val();
			var no_of_column 	= $("#no_of_column").val() * 1;

			var product_id_arr = product_ids.split(",");

			var data = "";
			var no_of_row = 0;
			var ad25 = 0;
			var w43 = 0;
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				for( var col = 1; col <= no_of_column ; col++)
				{
					w43 = w43 + ( $("#col_"+row+"_"+col).val() * 1) ;
				}
			}
			
			for(let row of product_id_arr) // for of loop , it loop throgh every item value 
			{
				var w25 = 0;
				for( var col = 1; col <= no_of_column ; col++)
				{
					w25 = w25 + ( $("#col_"+row+"_"+col).val() * 1) ;
				}
				var ab14 = $("#count_value"+row).val() * 1;
				var x36 = ((((d16+5)*aa14*v15)/1693.3)/ab14);
				console.log(`${x36} = ((((${d16}+5)*${aa14}*${v15})/1693.3)/${ab14})`);
				$("#wt_"+row).val(x36.toFixed(6));
				
			}
		}

	</script>
	</head>

	<body>
		<div align="center" style="width:<?=$width-10;?>px;">
			<form name="searchsystemidfrm" id="searchsystemidfrm">
				<fieldset style="width:<?=$width-30;?>px;">
					<legend>WARP PLAN</legend>
					<?php 

					$txt_total_ends 				= str_replace("'","",$txt_total_ends) * 1;
					$txt_required_warp_length_mtr   = str_replace("'","",$txt_required_warp_length_mtr) * 1;
					$txt_order_qnty   				= str_replace("'","",$txt_order_qnty) * 1;
					$txt_reed_space   				= str_replace("'","",$txt_reed_space) * 1;
					$txt_g_pick   					= str_replace("'","",$txt_g_pick) * 1;
					$txt_reed   					= str_replace("'","",$txt_reed) * 1;

					$hidden_weft_prod_id = str_replace("'","",$hidden_weft_prod_id);
					$product_str = implode(",", explode("*,*",$hidden_weft_prod_id));

                    $lib_color   = return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
                    $count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
					$count_value = return_library_array("select id, count_value from lib_yarn_count", 'id', 'count_value');
                    $fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
                    $sql = "SELECT a.id,a.lot, a.product_name_details,a.color, a.yarn_count_id from product_details_master a where  a.id in ($product_str)";
					$result = sql_select($sql);

                    $product_arr = array();
                    foreach($result as $row)
                    { 
                    	$product_arr[$row[csf('id')]]['count_id'] = $row[csf('yarn_count_id')];
                    	$product_arr[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
                    	$product_arr[$row[csf('id')]]['color_id'] = $row[csf('color')];
                    	$product_arr[$row[csf('id')]]['color'] = $lib_color[$row[csf('color')]];
						$product_arr[$row[csf('id')]]['count_value'] = $count_value[$row[csf('yarn_count_id')]];
                    }

					?>
					<table cellpadding="0" cellspacing="0" width="<?=$width-30;?>" class="rpt_table">
						<thead>
							<tr>
								<th width="35">Sl</th>
								<th width="80">Count</th>
								<th width="80">Shade</th>
								<th width="80">ALD</th>
								<div style="width:480px;overflow-x:scroll;">
									<?php for($col = 1 ; $col <= $column; $col++): ?>
										<th width="80"><?=$col;?></th>
									<?php endfor ?>
								</div>
								
								<th width="80">Ends/PAT</th>
								<th width="80">WT in KG
									<input type="hidden" id="no_of_column" value="<?=$column;?>">
									<input type="hidden" id="product_ids" value="<?=$product_str;?>">
									<input type="hidden" id="data" value="<?=$plan_data;?>">
								</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$sl = 1;
							foreach($product_arr as $prod_id => $prod): ?>
							<tr>
								<td><?=$sl++;?></td>
								<td>
									<input type="hidden" name="count_<?=$prod_id;?>" id="count_<?=$prod_id;?>"  value="<?=$prod['count_id']?>">
									<input type="text" name="countname_<?=$prod_id;?>" id="countname_<?=$prod_id;?>" style="width: 60px;" class="text_boxes" value="<?=$prod['count']?>">
									<input type="hidden" name="count_value<?=$prod_id;?>" id="count_value<?=$prod_id;?>" style="width: 60px;" class="text_boxes" value="<?=$prod['count_value']?>">
								</td>
								<td>
									<input type="hidden" name="shade_<?=$prod_id;?>" id="shade_<?=$prod_id;?>" value="<?=$prod['color_id']?>">
									<input type="text" name="shadename_<?=$prod_id;?>" id="shadename_<?=$prod_id;?>" style="width: 60px;" class="text_boxes" value="<?=$prod['color']?>">
								</td>
								<td>
									<input type="text" name="ald_<?=$prod_id;?>" id="ald_<?=$prod_id;?>" style="width: 60px;" class="text_boxes">
								</td>
								<div style="width:480px;overflow-x:scroll;">
								<?php for($col = 1 ; $col <= $column; $col++): ?>
									<td>
										<input type="text" name="col_<?=$prod_id;?>_<?=$col;?>" id="col_<?=$prod_id;?>_<?=$col;?>" style="width: 60px;" class="text_boxes" onkeyup="calculate_end_pat()">
									</td>
								<?php endfor ?>
								</div>
								<td>
									<input type="text" name="endpat_<?=$prod_id;?>" id="endpat_<?=$prod_id;?>" style="width: 60px;" class="text_boxes">
								</td>
								<td>
									<input type="text" name="wt_<?=$prod_id;?>" id="wt_<?=$prod_id;?>" style="width: 60px;" class="text_boxes">
								</td>
							</tr>
							<?php endforeach ?>
						</tbody>
					</table>
					<input type="hidden" id="txt_required_warp_length_mtr" value="<?=$txt_required_warp_length_mtr;?>">
					<input type="hidden" id="txt_total_ends" value="<?=$txt_total_ends;?>">
					<input type="hidden" id="txt_reed_space" value="<?=$txt_reed_space;?>">
					<input type="hidden" id="txt_g_pick" value="<?=$txt_g_pick;?>">
					<input type="hidden" id="txt_reed" value="<?=$txt_reed;?>">
				</fieldset>
			</form>
		</div>
		<center><input type="text" value="Close" onclick="js_set_value()" class="formbutton" style="width: 70px;justify-content: center;text-align: center;"></center>
	</body>
	<script type="text/javascript">
		set_previous_data_for_weft();
	</script>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="system_no_popup")
{
	echo load_html_head_contents("System Info","../../", 1, 1, '','1','');
	extract($_REQUEST);	
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('dtls_id').value=id;
			document.getElementById('all_data').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table">
	                <thead>
	                    
	                    <th width="200">Company Name</th>
	                    <th width="120">Buyer Name</th>
	                    <th width="100">System No</th>
	                    <th width="150">Req./Sales Order</th>
	                    <th width="150">Style</th>
	                    <th>
	                    	<input type="hidden" id="dtls_id"/>
	                    	<input type="hidden" id="all_data"/>
	                    </th>
	                </thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 200, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$cbo_company_name, "load_drop_down( 'weaving_plan_entry_controller', this.value, 'load_drop_down_buyer', 'req_popup_buyer' );");
								?>
							</td>
							<td id="req_popup_buyer">
								<?
								if(!empty($cbo_company_name))
								{
									$sql =  "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b  where     a.id = b.buyer_id and a.is_deleted = 0 and a.status_active = 1 and b.tag_company = $cbo_company_name order by a.buyer_name";
								}
								else
								{
									$sql = "select id, buyer_name from lib_buyer order by buyer_name";
								}
								//echo $sql;
								echo create_drop_down( "cbo_buyer_name", 120,$sql ,'id,buyer_name', 1, '--- Select Buyer ---',0, "");
								?>
							</td>
							<td>
	                    		<input type="text" style="width:90px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
	                    	</td>
							<td>
	                    		<input type="text" style="width:130px" class="text_boxes"  name="txt_requisition_no" id="txt_requisition_no" />
	                    	</td>
							<td>
								<input type="text" style="width:130px" class="text_boxes"  name="txt_style_ref_no" id="txt_style_ref_no" />
	                    	</td>
							
							
							<td align="right">
								<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('txt_style_ref_no').value+'_'+document.getElementById('txt_system_no').value, 'system_list_view', 'search_div', 'weaving_plan_entry_controller', 'setFilterGrid(\'comp_tbl\',-1)')" style="width:80px;" />
							</td>
						</tr>
					</tbody>
                </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action == "system_list_view")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);

    $company = str_replace("'","",$ex_data[0]);
    $cbo_buyer = str_replace("'","",$ex_data[1]);
    $txt_requisition_no = str_replace("'","",$ex_data[2]);
    $txt_style_ref_no = str_replace("'","",$ex_data[3]);
    $txt_system_no = str_replace("'","",$ex_data[4]);
    

	if(empty($company)) {echo "<p bgcolor='red'>Select Company First</p>";die;}
	?>
	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="130">Company Name</th>
			<th width="130">Buyer Name</th>
			<th width="115">System No</th>
			<th width="120">Req./Sales Order</th>
			<th width="150">Style</th>
			<th>Despo No</th>
		</thead>
		</table>
		<table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" id="comp_tbl">
		<tbody>

		<? 
		$lib_company = return_library_array("SELECT id,company_name FROM lib_company","id","company_name");
		$lib_buyer   = return_library_array("SELECT id,buyer_name FROM lib_buyer","id","buyer_name");
		$lib_color   = return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
		$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
		$buyer_cond = "";
		if(!empty($cbo_buyer))
		{
			$buyer_cond = " and buyer_id = $cbo_buyer ";
		}
		$requisition_cond = "";
		if(!empty($txt_requisition_no))
		{
			$requisition_cond = " and req_sales_order_no like  '%".$txt_requisition_no."%' ";
		}
		$txt_system_cond = "";
		if(!empty($txt_system_no))
		{
			$txt_system_cond = " and system_no_prefix_num =  '".$txt_system_no."' ";
		}
		$style_cond = "";
		if(!empty($txt_style_ref_no))
		{
			$style_cond = " and style_degin like  '%".$txt_style_ref_no."%' ";
		}
		$sql = "SELECT id,company_id, system_no, despo_no, planing_basis, gmt_item_id, req_sales_order_no, req_sales_order_dtls_id, pi_no, mill_code, color_id, style_degin, extension, spo_number, color_type, construction_id, buyer_id, composition_id, finish_type, finished_fabric_width_inch, final_delivery_date, order_qnty, pp_delivery_date, pp_qnty, inserted_by, insert_date, status_active, is_deleted,determination_id
				FROM   WOVEN_PRODUCTION_BUDGET_PLANNING_MST 
				WHERE   is_deleted = 0 and is_deleted = 0 and company_id = $company $requisition_cond $style_cond $txt_system_cond order by id desc";
		$result = sql_select($sql);

		$deter_ids = array();
		foreach($result as $row)
		{
			if(!empty($row[csf('determination_id')]))
			{
				$deter_ids[$row[csf('determination_id')]] = $row[csf('determination_id')];
			}
			
		}

		$composition_arr = array();

		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=581 and a.id in (".implode(",", $deter_ids).") order by a.id,b.id";
		//echo $sql;
					
		$data_array=sql_select($sql);
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}


		$i=1;
		foreach($result as $row)
		{ 
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$final_delivery_date = "";

			if(!empty($row[csf('final_delivery_date')]))
			{
				$final_delivery_date = change_date_format($row[csf('final_delivery_date')]);
			}
			$pp_delivery_date = "";

			if(!empty($row[csf('pp_delivery_date')]))
			{
				$pp_delivery_date = change_date_format($row[csf('pp_delivery_date')]);
			}

			
			if(!empty($row[csf('determination_id')]) && $row[csf('planing_basis')] == 1)
			{
				$composition_str = $composition_arr[$row[csf('determination_id')]];
			}
			else
			{
				$composition_ids = explode(",",$row[csf('composition_id')]);
				$composition_str = "";
				foreach($composition_ids as $comp_id)
				{
					$composition_str .= $composition[$comp_id] .",";
				}
				$composition_str = chop($composition_str,",");
			}

			

			$data =$row[csf('id')]."*".$row[csf('company_id')]."*".$row[csf('system_no')]."*".$row[csf('despo_no')]."*".$row[csf('planing_basis')]."*".$row[csf('gmt_item_id')]."*".$row[csf('req_sales_order_no')]."*".$row[csf('req_sales_order_dtls_id')]."*".$row[csf('pi_no')]."*".$row[csf('mill_code')]."*".$lib_color[$row[csf('color_id')]]."*".$row[csf('color_id')]."*".$row[csf('style_degin')]."*".$row[csf('extension')]."*".$row[csf('spo_number')]."*".$row[csf('color_type')]."*".$fabric_construction_name_arr[$row[csf('construction_id')]]."*".$row[csf('construction_id')]."*".$row[csf('buyer_id')]."*".$composition_str."*".$row[csf('composition_id')]."*".$row[csf('finish_type')]."*".$row[csf('finished_fabric_width_inch')]."*".$final_delivery_date."*".$row[csf('order_qnty')]."*".$pp_delivery_date."*".$row[csf('pp_qnty')];

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>')">
				<td width="30"><? echo $i; ?></td>
				<td width="130"><? echo $lib_company[$row[csf('company_id')]]; ?> </td> 
				<td width="130"><?=$lib_buyer[$row[csf('buyer_id')]];?></td>						
				<td width="115"><?=$row[csf('system_no')];?></td>						
				<td width="120"><?=$row[csf('req_sales_order_no')];?></td>						
				<td width="150"><p style="word-break:break-all;"><?=$row[csf('style_degin')];?></p></td>						
				<td><p style="word-break:break-all;"><?=$row[csf('despo_no')];?></p></td>						
			</tr>
			<? $i++; 
		}
		?>
		</tbody>
	</table>
	<script>setFilterGrid('comp_tbl',-1);</script>
	<?
}

if($action=="requisition_popup")
{
	echo load_html_head_contents("Order Info","../../", 1, 1, '','1','');
	extract($_REQUEST);	
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('dtls_id').value=id;
			document.getElementById('all_data').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table">
	                <thead>
	                    
	                    <th width="200">Company Name</th>
	                    <th width="120">Buyer Name</th>
	                    <th width="170">Requisition No</th>
	                    <th width="170">M.Style Ref/Name.</th>
	                    <th>
	                    	<input type="hidden" id="dtls_id">
	                    	<input type="hidden" id="all_data">
	                    </th>
	                </thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 200, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$cbo_company_name, "load_drop_down( 'weaving_plan_entry_controller', this.value, 'load_drop_down_buyer', 'req_popup_buyer' );");
								?>
							</td>
							<td id="req_popup_buyer">
								<?
								if(!empty($cbo_company_name))
								{
									$sql =  "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b  where     a.id = b.buyer_id and a.is_deleted = 0 and a.status_active = 1 and b.tag_company = $cbo_company_name order by a.buyer_name";
								}
								else
								{
									$sql = "select id, buyer_name from lib_buyer order by buyer_name";
								}
								//echo $sql;
								echo create_drop_down( "cbo_buyer_name", 120,$sql ,'id,buyer_name', 1, '--- Select Buyer ---',0, "");
								?>
							</td>
							<td>
	                    		<input type="text" style="width:170px" class="text_boxes"  name="txt_requisition_no" id="txt_requisition_no" />
	                    	</td>
							<td>
								<input type="text" style="width:170px" class="text_boxes"  name="txt_style_ref_no" id="txt_style_ref_no" />
	                    	</td>
							
							
							<td align="right">
								<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_requisition_no').value+'_'+document.getElementById('txt_style_ref_no').value, 'requisition_list_view', 'search_div', 'weaving_plan_entry_controller', 'setFilterGrid(\'comp_tbl\',-1)')" style="width:80px;" />
							</td>
						</tr>
					</tbody>
                </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action == "requisition_list_view")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);

    $company = str_replace("'","",$ex_data[0]);
    $cbo_buyer = str_replace("'","",$ex_data[1]);
    $txt_requisition_no = str_replace("'","",$ex_data[2]);
    $txt_style_ref_no = str_replace("'","",$ex_data[3]);
    

	if(empty($company)) {echo "<p bgcolor='red'>Select Company First</p>";die;}
	?>
	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Company Name</th>
			<th width="100">Buyer Name</th>
			<th width="100">Requisition No</th>
			<th width="100">M.Style Ref/Name.</th>
			<th width="150"> Fabric Construction</th>
			<th>Fab. Composition</th>
		</thead>
		</table>
		<table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" id="comp_tbl">
		<tbody>

		<? 
		$lib_company = return_library_array("SELECT id,company_name FROM lib_company","id","company_name");
		$lib_buyer   = return_library_array("SELECT id,buyer_name FROM lib_buyer","id","buyer_name");
		$lib_color   = return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
		$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
		$buyer_cond = "";
		$buyer_cond2 = "";
		if(!empty($cbo_buyer))
		{
			$buyer_cond = " and a.buyer_id = $cbo_buyer ";
			$buyer_cond2= " and c.buyer_id = $cbo_buyer ";
		}
		$requisition_cond = "";
		$requisition_cond2 = "";
		if(!empty($txt_requisition_no))
		{
			$requisition_cond = " and a.system_number like  '%".$txt_requisition_no."%' ";
			$requisition_cond2 = " and c.req_sales_order_no like  '%".$txt_requisition_no."%' ";
		}
		$style_cond = "";
		$style_cond2 = "";
		if(!empty($txt_style_ref_no))
		{
			$style_cond = " and a.style_refernce like  '%".$txt_style_ref_no."%' ";
			$style_cond2 = " and c.style_degin like  '%".$txt_style_ref_no."%' ";
		}
		$sql = "SELECT a.company_id, a.buyer_id, a.system_number, b.determination_id, b.constuction_id, b.product_type, b.composition_id, b.weave_design, b.finish_type, b.color_id, b.fabric_weight, b.fabric_weight_type,  b.finish_width,  b.cutable_width,  b.wash_type,  b.offer_qnty,  b.uom,  b.buyer_target_price, b.amount , b.id , a.style_refernce,a.delivery_date
				FROM   wo_sample_requisition_mst a, wo_sample_requisition_dtls b
				WHERE  a.id = b.mst_id and a.is_deleted = 0 and b.is_deleted = 0 and a.company_id = $company and b.id not in (SELECT c.req_sales_order_dtls_id
				FROM   woven_production_budget_planning_mst c
				WHERE planing_basis = 1 and  c.is_deleted = 0 and c.is_deleted = 0 and c.company_id = $company $requisition_cond2 $style_cond2 $buyer_cond2) $requisition_cond $style_cond $buyer_cond";
		$result = sql_select($sql);

		$deter_ids = array();
		foreach($result as $row)
		{
			if(!empty($row[csf('determination_id')]))
			{
				$deter_ids[$row[csf('determination_id')]] = $row[csf('determination_id')];
			}
			
		}

		$composition_arr = array();

		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=581 and a.id in (".implode(",", $deter_ids).") order by a.id,b.id";
		//echo $sql;
					
		$data_array=sql_select($sql);
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
			}
		}


		$i=1;
		foreach($result as $row)
		{ 
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$constuction_id=$row[csf("constuction_id")];
			$fabric_construction=$fabric_construction_name_arr[$constuction_id];

			if(!empty($row[csf('determination_id')]))
			{
				$composition_str = $composition_arr[$row[csf('determination_id')]];
			}
			else
			{
				$composition_str = $composition[$row[csf('composition_id')]];
			}
			$delivery_date = "";

			if(!empty($row[csf('delivery_date')]))
			{
				$delivery_date = change_date_format($row[csf('delivery_date')]);
			}

			$item_id = '';
			$data =$row[csf('id')]."*".$row[csf('company_id')]."*".$row[csf('system_number')]."*".$row[csf('buyer_id')]."*".$row[csf('determination_id')]."*".$row[csf('constuction_id')]."*".$row[csf('composition_id')]."*".$row[csf('product_type')]."*".$row[csf('weave_design')]."*".$row[csf('finish_type')]."*".$lib_color[$row[csf('color_id')]]."*".$row[csf('fabric_weight')]."*".$row[csf('fabric_weight_type')]."*".$row[csf('finish_width')]."*".$row[csf('cutable_width')]."*".$row[csf('wash_type')]."*".$row[csf('offer_qnty')]."*".$row[csf('uom')]."*".$row[csf('buyer_target_price')]."*".$row[csf('amount')]."*".$row[csf('style_refernce')]."*".$fabric_construction."*".$composition_str."*".$delivery_date."*".$item_id."*".$color_type[$row[csf('product_type')]];

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>')">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><? echo $lib_company[$row[csf('company_id')]]; ?> </td> 
				<td width="100"><?=$lib_buyer[$row[csf('buyer_id')]];?></td>						
				<td width="100"><?=$row[csf('system_number')];?></td>						
				<td width="100"><?=$row[csf('style_refernce')];?></td>						
				<td width="150"><p style="word-break:break-all;"><?=$fabric_construction;?></p></td>						
				<td><p style="word-break:break-all;"><?=$composition_str;?></p></td>						
			</tr>
			<? $i++; 
		}
		?>
		</tbody>
	</table>
	<script>setFilterGrid('comp_tbl',-1);</script>
	<?
}

if($action=="sales_order_popup")
{
	echo load_html_head_contents("Order Info","../../", 1, 1, '','1','');
	extract($_REQUEST);	
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('dtls_id').value=id;
			document.getElementById('all_data').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table">
	                <thead>
	                    
	                    <th width="200">Company Name</th>
	                    <th width="120">Buyer Name</th>
	                    <th width="170">Job No</th>
	                    <th width="170">M.Style Ref/Name.</th>
	                    <th>
	                    	<input type="hidden" id="dtls_id">
	                    	<input type="hidden" id="all_data">
	                    </th>
	                </thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 200, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$cbo_company_name, "load_drop_down( 'weaving_plan_entry_controller', this.value, 'load_drop_down_buyer', 'req_popup_buyer' );");
								?>
							</td>
							<td id="req_popup_buyer">
								<?
								if(!empty($cbo_company_name))
								{
									$sql =  "select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b  where     a.id = b.buyer_id and a.is_deleted = 0 and a.status_active = 1 and b.tag_company = $cbo_company_name order by a.buyer_name";
								}
								else
								{
									$sql = "select id, buyer_name from lib_buyer order by buyer_name";
								}
								echo create_drop_down( "cbo_buyer_name", 120, $sql,'id,buyer_name', 1, '--- Select Buyer ---',0, "");
								?>
							</td>
							<td>
	                    		<input type="text" style="width:170px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
	                    	</td>
							<td>
								<input type="text" style="width:170px" class="text_boxes"  name="txt_style_ref_no" id="txt_style_ref_no" />
	                    	</td>
							
							
							<td align="right">
								<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_ref_no').value, 'sales_order_listview', 'search_div', 'weaving_plan_entry_controller', 'setFilterGrid(\'comp_tbl\',-1)')" style="width:80px;" />
							</td>
						</tr>
					</tbody>
                </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
}

if($action == "sales_order_listview")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);

    $company = str_replace("'","",$ex_data[0]);
    $cbo_buyer = str_replace("'","",$ex_data[1]);
    $txt_job_no = str_replace("'","",$ex_data[2]);
    $txt_style_ref_no = str_replace("'","",$ex_data[3]);
    

	if(empty($company)) {echo "<p bgcolor='red'>Select Company First</p>";die;}
	?>
	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Company Name</th>
			<th width="100">Buyer Name</th>
			<th width="100">Job No</th>
			<th width="100">M.Style Ref/Name.</th>
			<th width="150"> Fabric Construction</th>
			<th>Fab. Composition
				<input type="hidden" id="dtls_id">
				<input type="hidden" id="all_data">
			</th>
		</thead>
		</table>
		<table cellpadding="0" cellspacing="0" border="1" rules="all" width="830" class="rpt_table" id="comp_tbl">
		<tbody>

		<? 
		$lib_company = return_library_array("SELECT id,company_name FROM lib_company","id","company_name");
		$lib_buyer   = return_library_array("SELECT id,buyer_name FROM lib_buyer","id","buyer_name");
		$lib_color   = return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
		$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );

		$buyer_cond = "";
		$buyer_cond2 = "";
		if(!empty($cbo_buyer))
		{
			$buyer_cond = " and a.buyer_id = $cbo_buyer ";
			$buyer_cond2= " and c.buyer_id = $cbo_buyer ";
		}
		$job_cond = "";
		$requisition_cond2 = "";
		if(!empty($txt_job_no))
		{
			$job_cond = " and a.job_no like  '%".$txt_job_no."%' ";
			$requisition_cond2 = " and c.req_sales_order_no like  '%".$txt_job_no."%' ";
		}
		$style_cond = "";
		$style_cond2 = "";
		if(!empty($txt_style_ref_no))
		{
			$style_cond = " and a.style_ref_no like  '%".$txt_style_ref_no."%' ";
			$style_cond2 = " and c.style_degin like  '%".$txt_style_ref_no."%' ";
		}

		$sql = "SELECT a.job_no,a.style_ref_no,a.delivery_date,a.company_id,a.buyer_id,b.determination_id,b.gsm_weight,b.color_id,b.finish_qty,b.grey_qty,b.cutable_width,b.weight_type,b.item_number_id,b.id,b.color_type_id from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id = b.mst_id and a.entry_form = 547 and a.company_id = $company and a.is_deleted = 0 and b.is_deleted = 0 and b.id not in (SELECT c.req_sales_order_dtls_id
		FROM   woven_production_budget_planning_mst c
		WHERE   planing_basis = 1 and c.is_deleted = 0 and c.is_deleted = 0 and c.company_id = $company $requisition_cond2 $style_cond2 $buyer_cond2) $buyer_cond $style_cond $job_cond ";
		//echo $sql;
		$result = sql_select($sql);

		$deter_ids = array();
		foreach($result as $row)
		{
			if(!empty($row[csf('determination_id')]))
			{
				$deter_ids[$row[csf('determination_id')]] = $row[csf('determination_id')];
			}
			
		}

		$composition_arr = array();

		$sql="SELECT a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid,a.fabric_construction_id from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0  and a.id in (".implode(",", $deter_ids).") order by a.id,b.id";
		//echo $sql;
					
		$data_array=sql_select($sql);
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(!empty($composition_arr[$row[csf('id')]]['copmposition']))
				{
					$composition_arr[$row[csf('id')]]['copmposition']=$composition_arr[$row[csf('id')]]['copmposition']." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				else
				{
					$composition_arr[$row[csf('id')]]['copmposition']=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
				}
				$composition_arr[$row[csf('id')]]['construction']= $row[csf('construction')];
				$composition_arr[$row[csf('id')]]['composition_id'][$row[csf('copmposition_id')]]= $row[csf('copmposition_id')];
				$composition_arr[$row[csf('id')]]['construction_id']= $row[csf('fabric_construction_id')];
			}
		}


		$i=1;
		foreach($result as $row)
		{ 
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		
			if(!empty($row[csf('determination_id')]))
			{
				$composition_str = $composition_arr[$row[csf('determination_id')]]['construction'];
				$fabric_construction = $composition_arr[$row[csf('determination_id')]]['copmposition'];
				$construction_id = $composition_arr[$row[csf('determination_id')]]['construction_id'];
				$composition_id = implode(",", $composition_arr[$row[csf('determination_id')]]['composition_id']);
				
			}
			else
			{
				$composition_str = '';
				$composition_id = '';
				$fabric_construction = '';
				$construction_id = '';
			}
			$delivery_date = "";

			if(!empty($row[csf('delivery_date')]))
			{
				$delivery_date = change_date_format($row[csf('delivery_date')]);
			}

			$data =$row[csf('id')]."*".$row[csf('company_id')]."*".$row[csf('job_no')]."*".$row[csf('buyer_id')]."*".$row[csf('determination_id')]."*".$construction_id."*".$composition_id."*".$row[csf('color_type_id')]."*".$row[csf('weave_design')]."*".$row[csf('finish_type')]."*".$lib_color[$row[csf('color_id')]]."*".$row[csf('fabric_weight')]."*".$row[csf('weight_type')]."*".$row[csf('finish_width')]."*".$row[csf('cutable_width')]."*".$row[csf('wash_type')]."*".$row[csf('grey_qty')]."*".$row[csf('uom')]."*".$row[csf('buyer_target_price')]."*".$row[csf('amount')]."*".$row[csf('style_ref_no')]."*".$fabric_construction."*".$composition_str."*".$delivery_date."*".$row[csf('item_number_id')]."*".$color_type[$row[csf('color_type_id')]];

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $data; ?>')">
				<td width="30"><? echo $i; ?></td>
				<td width="100"><? echo $lib_company[$row[csf('company_id')]]; ?> </td> 
				<td width="100"><?=$lib_buyer[$row[csf('buyer_id')]];?></td>						
				<td width="100"><?=$row[csf('job_no')];?></td>						
				<td width="100"><?=$row[csf('style_ref_no')];?></td>						
				<td width="150"><p style="word-break:break-all;"><?=$fabric_construction;?></p></td>						
				<td><p style="word-break:break-all;"><?=$composition_str;?></p></td>						
			</tr>
			<? $i++; 
		}
		?>
		</tbody>
	</table>
	<?
}

if($action=="composition_popup")
{
	echo load_html_head_contents("Composition Info","../../", 1, 1, '','1','');
	extract($_REQUEST);	
	
	?>
	<script>
		function js_set_value(id,name)
		{
			document.getElementById('composition_id').value=id;
			document.getElementById('composition_name').value=name;
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="300" class="rpt_table">
	                <thead>
	                    <th width="30">SL</th>
	                    <th >Composition</th>
						<input type="hidden" id="composition_id">
						<input type="hidden" id="composition_name">
	                </thead>
                    <tbody id="comp_tbl">
                    <?
					$sql = "select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name";
					$result = sql_select($sql);

                    $i=1;
                    foreach($result as $row)
                    { 
                    	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    	
                    	?>
                    	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('composition_name')]; ?>')">
                            <td width="30"><? echo $i; ?></td>
                            <td ><? echo$row[csf('composition_name')]; ?> </td> 
                           				
                        </tr>
                    	<? $i++; 
                	}
                	?>
                    </tbody>
	            </table>
	            <div id="search_div" style="margin-top:5px"></div>   
	        </form>
	    </fieldset>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
	</html>
	<?
	exit();
}

if($action == "yarn_lot_popup")
{
	echo load_html_head_contents("Order Info","../../", 1, 1, '','1','');
	extract($_REQUEST);	
	$cbo_company_name = str_replace("'","",$cbo_company_name);
	
	?>
	<script>
		var product_arr = [];
		var name_arr = [];
		var data_arr = [];
		const fn_select_row = (sl,id,name,data) =>{
			toggle( document.getElementById( 'tr_' + sl ), '#FFFFCC' );
			let index = product_arr.indexOf(id);
			if(index > -1) // only splice array when item is found
			{
				product_arr.splice(index, 1); // 2nd parameter means remove one item only
				name_arr.splice(index, 1); 
				data_arr.splice(index, 1); 
			}
			else
			{
				product_arr.push(id);
				name_arr.push(name);
				data_arr.push(data);
			}

		}
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) { 
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all()
		{
			$("#product_id").val(product_arr.join("*,*"))
			$("#all_data").val(data_arr.join("*,*"))
			$("#lot_with_name").val(name_arr.join("*,*"))
			parent.emailwindow.hide();
		}
		function open_composition()
		{
			var title = 'Composition Info';
			var page_link = 'weaving_plan_entry_controller.php?action=composition_popup';
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe',  page_link, title, 'width=350px,height=150px,center=1,resize=0,scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var composition_id=this.contentDoc.getElementById("composition_id").value;
				var composition_name=this.contentDoc.getElementById("composition_name").value;
				$("#txt_composition").val(composition_name);
				$("#cbo_composition_id").val(composition_id);
			}
		}
    </script>
	</head>
	<body>
		<fieldset style="width:850px;margin-left:10px">
	        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<input type="hidden" id="product_id">
				<input type="hidden" id="all_data">
				<input type="hidden" id="lot_with_name">
				
	                   
	            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="810" class="rpt_table">
	                <thead>
	                    <tr>
							<th width="150">Company Name</th>
							<th width="130">Count</th>
							<th width="150">Lot</th>
							<th width="150">Composition</th>
							<th width="150">Type</th>
							<th></th>
						</tr>
	                </thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down( "cbo_company_name", 150, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$cbo_company_name, "");
								?>
							</td>
							<td>
								<?
								echo create_drop_down( "cbo_count", 120, "select id, yarn_count from lib_yarn_count order by yarn_count",'id,yarn_count', 1, '--- Select Count ---',0, "");
								?>
							</td>
							<td>
	                    		<input type="text" style="width:100px" class="text_boxes"  name="txt_yarn_lot" id="txt_yarn_lot" />
	                    	</td>
							<td>
	                    		<input type="text" style="width:120px" class="text_boxes"  name="txt_composition" id="txt_composition" ondblclick="open_composition();" />
								<input type="hidden" style="width:120px" class="text_boxes"  name="cbo_composition_id" id="cbo_composition_id"  />
	                    	</td>
							<td>
								<?
								echo create_drop_down( "cbo_type", 120, $yarn_type,'', 1, '--- Select Type ---',0, "");
								?>
							</td>
							
							
							<td align="right">
								<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_count').value+'_'+document.getElementById('txt_yarn_lot').value+'_'+document.getElementById('cbo_composition_id').value+'_'+document.getElementById('cbo_type').value, 'yarn_lot_list_view', 'search_div', 'weaving_plan_entry_controller', 'setFilterGrid(\'comp_tbl\',-1)')" style="width:80px;" />
							</td>
						</tr>
					</tbody>
                </table>
	            <div style="margin-top:10px" id="search_div"></div>
	        </form>
	    </fieldset>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action =="yarn_lot_list_view")
{
	$ex_data = explode("_",$data);
	//print_r($ex_data);

    $company = str_replace("'","",$ex_data[0]);
    $cbo_count = str_replace("'","",$ex_data[1]);
    $txt_yarn_lot = str_replace("'","",$ex_data[2]);
    $cbo_composition_id = str_replace("'","",$ex_data[3]);
    $cbo_type = str_replace("'","",$ex_data[4]);

	if(empty($company)) {echo "<p bgcolor='red'>Select Company First</p>";die;}
	?>
	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="810" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="100">Company Name</th>
			<th width="150">Product Name details</th>
			<th width="100">Lot</th>
			<th width="100">Count</th>
			<th width="150">Composition</th>
			<th width="100"> Type</th>
			<th>Current Stock
				
			</th>
		</thead>
	</table>
	
	<div style="width:830px; max-height:250px; overflow-y:scroll" id="scroll_body">
		<table cellpadding="0" cellspacing="0" border="1" rules="all" width="810" class="rpt_table" id="comp_tbl">
			<tbody>

			<? 
			$lib_company = return_library_array("SELECT id,company_name FROM lib_company","id","company_name");
			$lib_buyer   = return_library_array("SELECT id,buyer_name FROM lib_buyer","id","buyer_name");
			$lib_color   = return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
			$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
			$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
			$composition_cond = "";
			if(!empty($cbo_composition_id))
			{
				$composition_cond = " and a.yarn_comp_type1st = '".$cbo_composition_id."'";
			}
			$count_cond = "";
			if(!empty($cbo_count))
			{
				$count_cond = " and a.yarn_count_id = '".$cbo_count."'";
			}
			$type_cond = "";
			if(!empty($cbo_type))
			{
				$type_cond = " and a.yarn_type = '".$cbo_type."'";
			}
			$lot_cond = "";
			if(!empty($txt_yarn_lot))
			{
				$lot_cond = " and a.lot like '%".$txt_yarn_lot."%'";
			}
			$sql = "SELECT a.id, a.company_id, a.item_category_id, a.supplier_id, a.lot, a.product_name_details, a.current_stock, a.allocated_qnty, a.available_qnty, a.unit_of_measure, a.yarn_comp_percent1st, a.yarn_comp_type1st, a.color, a.yarn_count_id, a.yarn_type, a.avg_rate_per_unit, a.dyed_type from product_details_master a where a.company_id = $company and  a.item_category_id=1 and a.current_stock > 0 and a.status_active=1 and a.is_deleted=0 $composition_cond $lot_cond $count_cond $type_cond";
			$result = sql_select($sql);
			//echo $sql;

			$i=1;
			foreach($result as $row)
			{ 
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
				$comp = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "% ";
				$delivery_date = "";

				if(!empty($row[csf('delivery_date')]))
				{
					$delivery_date = change_date_format($row[csf('delivery_date')]);
				}

				$data = $row[csf('id')]."*".$row[csf("product_name_details")]."*".$row[csf("lot")]."*".$row[csf('yarn_count_id')]."*".$count_arr[$row[csf('yarn_count_id')]]."*".$comp."*".$row[csf('yarn_type')]."*".$yarn_type[$row[csf('yarn_type')]]."*".$current_stock;
				$name_with_lot = $row[csf("product_name_details")]."*".$row[csf("lot")];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="fn_select_row(<? echo $i; ?>,<? echo $row[csf('id')]; ?>,'<? echo $name_with_lot; ?>','<? echo $data; ?>')">
					<td width="30" style="word-break: break-all;"><p><? echo $i; ?></p></td>
					<td width="100" style="word-break: break-all;"><p><? echo $lib_company[$row[csf('company_id')]]; ?></p></td> 
					<td width="150" style="word-break: break-all;"><p><? echo $row[csf("product_name_details")]; ?></p></td>						
					<td width="100" style="word-break: break-all;"><p><? echo $row[csf("lot")]; ?></p></td>						
					<td width="100" style="word-break: break-all;"><?=$count_arr[$row[csf('yarn_count_id')]];?></p></td>						
					<td width="150" style="word-break: break-all;"><p><? echo $comp; ?> </p></td>
					<td width="100" style="word-break: break-all;"> <p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>						
					<td><p><? echo number_format($row[csf("current_stock")], 2); ?></p></td>						
				</tr>
				<? $i++; 
			}
			?>
			</tbody>
			
		</table>
	</div>
	<br>
	<br>
	<table cellpadding="0" cellspacing="0" border="1" rules="all" width="810" class="rpt_table">
		<tfoot>
			<tr>
				<th colspan="8" align="center"><center><input type="button" onclick="check_all()" value="Close" class="formbutton"></center></th>
			</tr>
		</tfoot>
	</table>
	<?
}

if($action == "show_weaving_plan_break_down_listview")
{
	$data = explode("**",$data);
	$update_id 		= str_replace("'","",$data[0]);
	$wrap_prod_ids 	= str_replace("'","",$data[1]);
	$wrap_plan_data = str_replace("'","",$data[2]);
	$weft_prod_ids 	= str_replace("'","",$data[3]);
	$weft_plan_data = str_replace("'","",$data[4]);

	if(!empty($update_id))
	{
		$prev_sql = "SELECT A.ID,A.WARP_PLAN_DATA,A.WEFT_PLAN_DATA,B.PRODUCT_ID,B.BREAK_DOWN_TYPE FROM WOVEN_PRODUCTION_BUDGET_PLANNING_DTLS A,WOVEN_PRODUCTION_BUDGET_PLANNING_BREAK_DOWN B WHERE A.ID = B.DTLS_ID AND A.MST_ID =$update_id AND A.IS_DELETED = 0 AND B.IS_DELETED = 0";
		$res_prev = sql_select($prev_sql);
		//echo $prev_sql;
		$data_prev = array();
		$prev_prod_id = array();
		foreach($res_prev as $row)
		{
			if(empty($wrap_plan_data))
			{
				$wrap_plan_data = $row['WARP_PLAN_DATA'];
			}
			if(empty($weft_plan_data))
			{
				$weft_plan_data = $row['WEFT_PLAN_DATA'];
			}
			$data_prev[$row['PRODUCT_ID']] = $row['ID'];
			$prev_prod_id[$row['BREAK_DOWN_TYPE']][$row['PRODUCT_ID']] = $row['PRODUCT_ID'];
		}

		if(empty($wrap_prod_ids))
		{
			$wrap_prod_ids = implode(",",$prev_prod_id[1]);
		}
		if(empty($weft_prod_ids))
		{
			$weft_prod_ids = implode(",",$prev_prod_id[2]);
		}
	}

	$product_ids = array();
	$wrap_prod_id =  explode("----",$wrap_prod_ids);
	for($i = 0 ; $i < count($wrap_prod_id) ; $i++)
	{
		$prod_id = $wrap_prod_id[$i];
		$product_ids[$prod_id] = $prod_id;
	}
	$weft_prod_id =  explode("----",$weft_prod_ids);
	for($i = 0 ; $i < count($weft_prod_id) ; $i++)
	{
		$prod_id = $weft_prod_id[$i];
		$product_ids[$prod_id] = $prod_id;
	}
	$product_wise_data = array();

	$wrap_plan_data = explode("----",$wrap_plan_data);		
	for ($r = 0; $r < count($wrap_plan_data); $r++ )
	{ 
		$col_data = explode("____",$wrap_plan_data[$r]);
		$col_len = count($col_data);
		$prod_id = $col_data[0];
		for ($c = 0; $c < $col_len; $c++ )
		{   
			if($c == 3)
			{
				$product_wise_data[$prod_id]['ald'][$col_data[$c]]=$col_data[$c];
			}
			else if($c == $col_len-1)
			{
				$product_wise_data[$prod_id]['wrap']+=$col_data[$c];
			}
		}
	}

	$weft_plan_data = explode("----",$weft_plan_data);		
	for ($r = 0; $r < count($weft_plan_data); $r++ )
	{ 
		$col_data = explode("____",$weft_plan_data[$r]);
		$col_len = count($col_data);
		$prod_id = $col_data[0];
		for ($c = 0; $c < $col_len; $c++ )
		{   
			if($c == 3)
			{
				$product_wise_data[$prod_id]['ald'][$col_data[$c]]=$col_data[$c];
			}
			else if($c == $col_len-1)
			{
				$product_wise_data[$prod_id]['weft']+=$col_data[$c];
				//echo "<pre>weft=>".$col_data[$c]."</pre>";
			}
			//echo "<pre>".$r."=>".$c."=>".$col_data[$c]."</pre>";
		}
	}
	$product_ids = array_filter($product_ids);
	$product_str = implode(",", $product_ids);

	$lib_color   = return_library_array("SELECT id,color_name FROM lib_color","id","color_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$fabric_construction_name_arr=return_library_array( "select id,fabric_construction_name from lib_fabric_construction", "id", "fabric_construction_name" );
	$sql = "SELECT a.id,a.lot, a.product_name_details,a.color, a.yarn_count_id from product_details_master a where  a.id in ($product_str)";
	//echo $sql;
	$result = sql_select($sql);

	$product_arr = array();
	foreach($result as $row)
	{ 
		$product_arr[$row[csf('id')]]['count_id'] = $row[csf('yarn_count_id')];
		$product_arr[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_arr[$row[csf('id')]]['color_id'] = $row[csf('color')];
		$product_arr[$row[csf('id')]]['color'] = $lib_color[$row[csf('color')]];
	}

	
	// echo "<pre>";
	// print_r($product_wise_data);
	// echo "</pre>";
	$table_width = 1000;
	?>
	<table align="right" cellspacing="0" width="<? echo $table_width;?>"  border="1" rules="all" class="rpt_table" id="yarn_dyeing_breakdown">
		<caption style="justify-content: center;text-align:center" width="1000">
			<strong>Yarn Dyeing Breakdown</strong>
		</caption>
		<thead>
			<tr>
				<th>SL</th>
				<th>Count</th>
				<th>Shade</th>
				<th>ALD</th>
				<th>Warp(KG)</th>
				<th>Weft (KG)</th>
				<th>Total (KG)</th>
				<th>For Dyeing Kg</th>
				<th>Swatch</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach($product_ids as $prod_id)
			{
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	
				if(!empty( $data_prev[$prod_id]))
				{
					$dtls_id_prod = $data_prev[$prod_id];
				}
				else
				{
					$dtls_id_prod = '';
				}
				
				?>
				<tr  bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer;" onclick="put_details_data('<?=$data_prev[$prod_id];?>')">
					<td><?=$i;?></td>
					<td><?=$product_arr[$prod_id]['count'];?></td>
					<td><?=$product_arr[$prod_id]['color'];?></td>
					<td><?=implode(",",$product_wise_data[$prod_id]['ald'])?></td>
					<td><?=fn_number_format($product_wise_data[$prod_id]['wrap'],4)?></td>
					<td><?=fn_number_format($product_wise_data[$prod_id]['weft'],4)?></td>
					<td><?=fn_number_format($product_wise_data[$prod_id]['wrap']+$product_wise_data[$prod_id]['weft'],4)?></td>
					<td>For Dyeing Kg</td>
					<td>
						<input type="button" id="image_id_<?=$i;?>" class="image_uploader" onclick="add_img(<?=$prod_id;?>)" value="ADD/VIEW IMAGE">
						<input type="hidden" id="product_id_<?=$i;?>" value="<?=$prod_id;?>">
						<input type="hidden" id="wrap_kg_<?=$i;?>" value="<?=fn_number_format($product_wise_data[$prod_id]['wrap'],4,".","");?>">
						<input type="hidden" id="weft_kg_<?=$i;?>" value="<?=fn_number_format($product_wise_data[$prod_id]['weft'],4,".","");?>">
						<input type="hidden" id="total_kg_<?=$i;?>" value="<?=fn_number_format($product_wise_data[$prod_id]['wrap']+$product_wise_data[$prod_id]['weft'],4,".","");?>">
						<input type="hidden" id="dtls_id_<?=$i;?>" value="<?=$dtls_id_prod;?>">
					</td>
				</tr>
				<?
				$i++;
			}
			?>
		</tbody>
	</table>
	<?
}

if($action == 'populate_dtls_data')
{
	$dtls_id = $data;
	$prev_sql = "SELECT ID,WARP_PLAN_DATA,WEFT_PLAN_DATA ,WEAVE,ENDS_X_PICK_GREIGE,REF,GREIGE_FABRIC_WIDTH,REED_COUNT,REED_SPACE,REQUIRED_GREIGE,REQUIRED_WARP_LENGTH,GROUND_ENDS,EXTRA_SELVEDGE_ENDS,SPO_RECEIVE_DATE,TOTAL_ENDS,TOTAL_ALLOWANCE,PREVIOUS_STATUS,BALANCE_QTY,TEMPLATE_ID FROM WOVEN_PRODUCTION_BUDGET_PLANNING_DTLS WHERE  ID =$dtls_id AND IS_DELETED = 0";
	$res_prev = sql_select($prev_sql);
	$data_prev = array();
	$prev_prod_id = array();
	foreach($res_prev as $row)
	{
		echo "$('#warp_plan_data').val('".$row['WARP_PLAN_DATA']."');\n";
		echo "$('#weft_plan_data').val('".$row['WEFT_PLAN_DATA']."');\n";
		echo "$('#txt_weave').val('".$row['WEAVE']."');\n";
		echo "$('#txt_ends_x_pick_greige').val('".$row['ENDS_X_PICK_GREIGE']."');\n";
		echo "$('#txt_ref').val('".$row['REF']."');\n";
		echo "$('#txt_greige_fabric_width_inch').val('".$row['GREIGE_FABRIC_WIDTH']."');\n";
		echo "$('#txt_reed').val('".$row['REED_COUNT']."');\n";
		echo "$('#txt_reed_space').val('".$row['REED_SPACE']."');\n";
		echo "$('#txt_required_greige_mtr').val('".$row['REQUIRED_GREIGE']."');\n";
		echo "$('#txt_required_warp_length_mtr').val('".$row['REQUIRED_WARP_LENGTH']."');\n";
		echo "$('#txt_ground_ends').val('".$row['GROUND_ENDS']."');\n";
		echo "$('#txt_extra_selvedge_ends').val('".$row['EXTRA_SELVEDGE_ENDS']."');\n";
		echo "$('#txt_spo_receive_date').val('".change_date_format($row['SPO_RECEIVE_DATE'])."');\n";
		echo "$('#txt_total_ends').val('".$row['TOTAL_ENDS']."');\n";
		echo "$('#txt_total_allowance').val('".$row['TOTAL_ALLOWANCE']."');\n";
		echo "$('#txt_previous_status').val('".$row['PREVIOUS_STATUS']."');\n";
		echo "$('#txt_balance_qty').val('".$row['BALANCE_QTY']."');\n";
		echo "$('#cbo_template_id').val('".$row['TEMPLATE_ID']."');\n";
	}

	if(count($res_prev))
	{
		$sql_break_down = "SELECT A.ID,A.MST_ID,A.DTLS_ID,A.PRODUCT_ID, A.WRAP, A.WEFT, A.TOTAL, A.COUNT_ID, A.COLOR_ID, A.ALD, A.END_PAT,A.WT_IN_KG,A.BREAK_DOWN_TYPE,B.LOT, B.PRODUCT_NAME_DETAILS FROM WOVEN_PRODUCTION_BUDGET_PLANNING_BREAK_DOWN A,PRODUCT_DETAILS_MASTER B WHERE A.PRODUCT_ID = B.ID AND  A.DTLS_ID =$dtls_id AND A.IS_DELETED = 0 ";
		$res_break_down = sql_select($sql_break_down);
		$data_break_down = array();
		$wrap_prod_ids = '';
		$weft_prod_ids = '';
		$prod_ids = array();
		foreach($res_break_down as $row)
		{
			
			$prod_ids[$row['BREAK_DOWN_TYPE']][$row['PRODUCT_ID']] = $row['PRODUCT_ID'];
			$name_with_lot = $row[csf("PRODUCT_NAME_DETAILS")]."*".$row[csf("LOT")];
			$data_break_down[$row['BREAK_DOWN_TYPE']][$name_with_lot] = $name_with_lot;
		}
		$wrap_name 		= implode("----",$data_break_down[1]);
		$weft_name 		= implode("----",$data_break_down[2]);
		$wrap_prod_ids 	= implode(",",$prod_ids[1]);
		$weft_prod_ids 	= implode(",",$prod_ids[2]);

		echo "$('#txt_warp_yarn_lot_and_brand').val(`".$wrap_name."`);\n";
		echo "$('#txt_weft_yarn_lot_and_brand').val(`".$weft_name."`);\n";
		echo "$('#hidden_warp_prod_id').val(`".$wrap_prod_ids."`);\n";
		echo "$('#hidden_weft_prod_id').val(`".$weft_prod_ids."`);\n";
		//echo "set_button_status(1, ".$permission.", 'fnc_weaving_plan_entry',2);\n";

	}
}

?>