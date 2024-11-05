<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_wise_daily_dyeing_prod_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();
}
  
  
if ($action == "load_drop_down_buyer")
{
	$lode_data=explode('_',$data);
	//print_r($lode_data[0]); // [0] == type and [1] == company
	if ($lode_data[0]==0 || $lode_data[0]==1 || $lode_data[0]==3) // Self and Sample
	{
		echo create_drop_down("cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$lode_data[1]' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
		exit();
	}
	if ($lode_data[0]==2) // Sub Party
	{
		echo create_drop_down("cbo_buyer_name", 100, "select sup.id, sup.supplier_name from lib_supplier sup, lib_supplier_tag_company b  where sup.status_active =1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$lode_data[1]' and sup.id in (select supplier_id from lib_supplier_party_type where party_type in (1,3,21,90)) order by supplier_name", "id,supplier_name", 1, "-- All Buyer --", $selected, "");
		exit();
	}
}

if ($action=="load_drop_down_floor")
{
	$ex_data=explode('_',$data);
	if($ex_data[1]!=0) $location_cond=" and b.location_id='$ex_data[1]'"; else $location_cond="";

	echo create_drop_down( "cbo_floor_id", 100, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=2 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0  $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "","" );
	exit();
}


if($action=="report_generate") //Live Show2 button now show Button. issue=25170
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company 	= str_replace("'","",$cbo_company_id);
	$machine_name 	= str_replace("'","",$txt_machine_id);
	$cbo_location 	= str_replace("'","",$cbo_location_id);
	$order_type 	= str_replace("'","",$cbo_order_type);
	$buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$booking_no 	= str_replace("'","",$txt_booking_no);
	$order_no 		= str_replace("'","",$txt_order_no);
	$batch 			= str_replace("'","",$txt_batch);
	$batch_color 	= str_replace("'","",$hidden_color_id);
	$txt_color_range= str_replace("'","",$txt_color_range);
	$floor_name 	= str_replace("'","",$cbo_floor_id);
	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);


	if ($batch=="") 	$batch_cond =""; else $batch_cond =" and a.batch_no='$batch' ";
	if ($order_type==0) $order_type_cond =""; else $order_type_cond =" and a.order_type=$order_type ";
	if ($cbo_location==0 || $cbo_location=='') $location_id =""; else $location_id =" and c.location_id=$cbo_location ";
	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and c.floor_id=$cbo_floor_id";
	if ($machine_name=="") $machine_cond=""; else $machine_cond =" and c.id in ($machine_name) ";
	if ($buyer_name==0  || $buyer_name=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$buyer_name ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and q.booking_no=$txt_booking_no ";
	if ($batch_color=="") $batch_color_cond =""; else $batch_color_cond =" and q.color_id=$batch_color ";
	if ($txt_color_range==0 || $txt_color_range=="") $color_range_cond =""; else $color_range_cond =" and q.color_range_id=$txt_color_range ";
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="")
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="")
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$machineArr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	ob_start();
	
	if ($order_type==0 || $order_type==1) // Self Order sql and array
	{
		$Self_sqls="SELECT  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks
		from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 and a.process_id not in (203,193)
		group by  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes,a.process_end_date, a.remarks ";

		$self_load_time_array=array();
		foreach(sql_select($Self_sqls) as $vals)
		{
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("process_end_date")];
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
		}
		
		/*echo '<pre>';
		print_r($self_load_time_array);die;*/
		// Main query
		$self_sql_result="SELECT a.id, a.batch_no, a.batch_id,a.system_no, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.process_end_date, a.production_date,
		a.water_flow_meter,a.incomplete_result,a.redyeing_needed,a.shade_matched, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity,
		p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
		from wo_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2 and a.process_id not in (203,193)
		and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order!=1
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.system_no";

		//echo $self_sql_result;//die;

		$sql_dtls=sql_select($self_sql_result);
		//print_r($sql_dtls);
		$self_date_data_arr=array();
		$fabr_arr=array();
		$batch_qty_arr=array();$self_all_batch_id=""; $self_all_batch_no="";
		$fun_batch_rowspan_arr=array();
		$array_result_arr=array(2,4);
		foreach ($sql_dtls as $row)
		{

			$all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$all_batch_data[$row[csf('batch_id')]]['result']=$row[csf('result')];


			$all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$self_all_batch_id.=$row[csf('batch_id')].',';
			$self_all_batch_no.="'".$row[csf('batch_no')]."'".',';
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['system_no']=$row[csf('system_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('process_end_date')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_end_date']=$row[csf('production_date')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition'].=$row[csf('const_composition')].',';
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['incomplete_result']=$row[csf('incomplete_result')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['redyeing_needed']=$row[csf('redyeing_needed')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['shade_matched']=$row[csf('shade_matched')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];

			if($fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=="")
			{
				$fun_batch_rowspan_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('system_no')]]+=1;
				$fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=1;
			}
			//if($row[csf('batch_ext_no')]=="" || $row[csf('batch_ext_no')]==0) $row[csf('batch_ext_no')]="";else $row[csf('batch_ext_no')]=$row[csf('batch_ext_no')];
			$batch_qty_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_no')]]=$row[csf('batch_ext_no')];
			$machine_no_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];

			//echo $row[csf('batch_ext_no')].'X';
		}
		//print_r($batch_qty_arr);
		$self_all_batch_id=implode(",",array_unique(explode(",",$self_all_batch_id)));
			$poIds=chop($self_all_batch_id,','); //$batch_cond_for_in="";
			$po_ids=count(array_unique(explode(",",$self_all_batch_id)));
			if($db_type==2 && $po_ids>1000)
			{
				$batch_cond_for_in=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$batch_cond_for_in.=" b.batch_id in($ids) or";

				}
				$batch_cond_for_in=chop($batch_cond_for_in,'or ');
				$batch_cond_for_in.=")";
			}
			else
			{
				$batch_cond_for_in=" and b.batch_id in($poIds)";
			}
			$self_all_batch_no=implode(",",array_unique(explode(",",$self_all_batch_no)));
			$batIds=chop($self_all_batch_no,','); //$batch_cond_for_in="";
			$bat_ids=count(array_unique(explode(",",$self_all_batch_no)));
			if($db_type==2 && $bat_ids>1000)
			{
				$batch_no_cond_for_in=" and (";
				$batIdsArr=array_chunk(explode(",",$batIds),999);
				foreach($batIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$batch_no_cond_for_in.=" aa.batch_no in($ids) or";
				}
				$batch_no_cond_for_in=chop($batch_no_cond_for_in,'or ');
				$batch_no_cond_for_in.=")";
			}
			else
			{
				$batch_no_cond_for_in=" and aa.batch_no in($batIds)";
			}


		 /*echo "<pre>";
		 print_r($self_date_data_arr);*/
		/* $sql_prod="select aa.batch_no,aa.result,aa.batch_ext_no,b.batch_id,b.receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.receive_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $batch_cond_for_in";//batch_no
			$fin_prod=sql_select($sql_prod);
			foreach($fin_prod as $row)
			{
	
				if($row[csf("result")]==4 && $row[csf("batch_ext_no")]=="")
				{
					$self_fin_prod_arr[$row[csf("batch_no")]][1]["fin_qnty"]=$row[csf("receive_qnty")];
				}
				if($row[csf("batch_ext_no")]=="")
				{
				$self_fin_prod_arr2[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];
				}
	
			}
		*/ // $all_batch[$row[csf('batch_id')]]
	 	/* echo $sql_prod2="select aa.batch_no,aa.result,max(aa.batch_ext_no) as batch_ext_no,b.batch_id,sum(b.receive_qnty) as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.receive_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $batch_no_cond_for_in group by aa.batch_no,aa.result,b.batch_id,b.machine_no_id";//batch_no
			$fin_prod2=sql_select($sql_prod2);
			foreach($fin_prod2 as $row)
			{
	
				$batch_ext_no=$row[csf("batch_ext_no")];
				$self_fin_prod_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
				$self_fin_prod_arr4[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];
				if(($row[csf("batch_ext_no")]*1)<1)
				{
				$self_fin_prod_arr_check[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
				}
	
			}
	
			*/
	
	
		 $sql_prod2="select a.recv_number,b.batch_id,b.receive_qnty as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id  and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 and b.batch_id in (".implode(",",$all_batch).")  ";//batch_no
		 $fin_prod2=sql_select($sql_prod2);
		 foreach($fin_prod2 as $row)
		 {
	
			$batch_ext_no=$row[csf("batch_ext_no")];
			$self_fin_prod_qty_arr[$row[csf("batch_id")]]["recv_number"]=$row[csf("recv_number")];
			$self_fin_prod_qty_arr[$row[csf("batch_id")]]["receive_qnty"]+=$row[csf("receive_qnty")];
			$self_fin_prod_arr4[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];
	
			$self_fin_prod_arr_check[$all_batch_data[$row[csf('batch_id')]]['batch_no']][$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["fin_qnty"] +=$row[csf("receive_qnty")];
			$self_fin_prod_arr_check[$all_batch_data[$row[csf('batch_id')]]['batch_no']][$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["result"] =$all_batch_data[$row[csf('batch_id')]]['result'];
	
			 /* $all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
				$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
				$all_batch_data[$row[csf('batch_id')]]['result']*/
			}
	
			//print_r($self_fin_prod_arr_check);
	
			//print_r($self_fin_prod_arr3);
			$self_rowspan_arr=array();$tt=1;
			$self_func_batch_arr=array();$self_trims_chk_batch_arr=array();
			$mc_ut_qty_arr=array();$self_prod_qty=$self_summ_trims_wgt_batch_qty=$tot_self_actual_prod_qty=0;
			$batch_qty=$self_summ_white_qty=$self_summ_wash_qty=$self_summ_color_batch_qty=$summ_white_qty=0;
			$total_summ_trim_wgt=0;
			foreach ($self_date_data_arr as $p_date=>$prod_date_data)
			{
				foreach ($prod_date_data as $machine_id => $machine_data)
				{
					foreach ($machine_data as $batch_id => $row)
					{
						$self_fin_prod_summ=$self_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
						$self_fin_prod_summ2=$self_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
						$self_fin_prod_summ3=$self_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
						$batch_qty = $row['batch_qty'];
						if($row['batch_ext_no']==1)
						{
						$bext=$row['batch_ext_no'];
						}
						else $bext=$row['batch_ext_no']-1;
											
						//$bext=$row['batch_ext_no']-1;
						if(($bext*1)<=0) $bext='';
						
						
	
						if ($row['result']==1)
						{
							if ($row['color_range_id']==4)
							{
								$self_summ_white_qty += $row['batch_qty'];
							}
							else if ($row['color_range_id']==7)
							{
								$self_summ_wash_qty += $row['batch_qty'];
							}
							else $self_summ_color_batch_qty += $row['batch_qty'];
	
						}
						else if ($row['result']==5 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=="") // //Under Trial//Fin Fab not Avaiable
						{
							if ($row['color_range_id']==4)
							{
								$self_summ_white_qty +=0;
							}
							else if ($row['color_range_id']==7)
							{
								$self_summ_wash_qty +=0;
							}
							else $self_summ_color_batch_qty +=0;
						}
						else if($row['batch_ext_no']>0)
						{
							$self_summ_white_qty +=0;
							$self_summ_wash_qty += 0;
							$self_summ_color_batch_qty +=0;
						}
						/*if ($row['result']==4 && $row['batch_ext_no']>0 && $self_fin_prod_summ>0) //if ($row['result']==4 && $row['batch_ext_no']>0 )
						{
							$prod_qty += $row['batch_qty'];
						}*/
						//$self_fin_prod_chk=$self_fin_prod_arr[$row[csf('batch_no')]]["fin_qnty"];
						$trims_weight_qty+=$row['total_trims_weight'];
						$self_prod_qty+=$row['batch_qty'];
	
						if ($row['result']==1 && $row['batch_ext_no']=="")
						{
							if ($checkTrimWgtArr[$row['batch_id']]=='')
							{
								$total_summ_trim_wgt+=$row['total_trims_weight'];
							}
							$self_actual_prod_qty= $row['batch_qty'];
							$tot_self_actual_prod_qty+= $row['batch_qty'];
								//echo  $row['result'].'='.$re_process_qty3.'<br>';
						}
						else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
						{
							$self_actual_prod_qty = 0;
							$tot_self_actual_prod_qty+= 0;
									//echo "B";
						}
						if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
						{
							if ($checkTrimWgtArr[$row['batch_id']]=='')
							{
								$total_summ_trim_wgt+=$row['total_trims_weight'];
							}
							$self_actual_prod_qty= $row['batch_qty'];
							$tot_self_actual_prod_qty+= $row['batch_qty'];
						}
	
						if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
						{
							$self_summ_re_pro_qty +=$row['batch_qty'];// $row['batch_qty'];
							$is_finished[$row['batch_no']]=1;
						}
						if($is_finished[$row['batch_no']]==1) $self_summ_re_pro_qty +=$row['batch_qty'];
						if($self_actual_prod_qty>0)
						{
							if(!in_array($batch_id,$self_trims_chk_batch_arr))
							{
								$tt++;
	
								$self_trims_chk_batch_arr[]=$batch_id;
								$self_summ_trims_wgt_batch_qty +=$row['total_trims_weight'];
								$trimswgt =$row['total_trims_weight'];
							}
							else { $self_summ_trims_wgt_batch_qty +=0;$trimswgt =0;}
						}
						$summ_mc_total_qty+= $batch_qty+$trimswgt;
						$mc_ut_qty_arr[$p_date][$machine_id] += $batch_qty+$row['total_trims_weight'];
						$self_total_batch+=count($row['batch_no']);
						$self_func_batch_arr[$row['system_no']]=$row['system_no'];
						$tot_self_func_batch_arr=count($self_func_batch_arr);
						if (isset($self_rowspan_arr[$p_date][$machine_id]))
						{
							$self_rowspan_arr[$p_date][$machine_id]++;
						}
						else
						{
							$self_rowspan_arr[$p_date][$machine_id]=1;
							$self_total_machine_capacity+=$row['prod_capacity'];
							$self_total_machine+=count($row['machine_no']);
	
						}
						$self_avg_batch_per_mc = $self_total_batch/$self_total_machine;
					}
	
				}
			}
			/*echo '<pre>';
			print_r($mc_ut_qty_arr);*/
		}
		//print_r($self_re_process_qty_arr);
	
		if ($order_type==0 || $order_type==2) // Subcontract Order sql and array
		{
			$Subcon_sqls=" SELECT a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks
			from pro_fab_subprocess a, lib_machine_name c
			where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
			and a.machine_id=c.id and a.entry_form in(35,38) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 and a.process_id not in (203,193)
			group by a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes,a.process_end_date, a.remarks";
	
			$subcon_load_time_array=array();
			foreach(sql_select($Subcon_sqls) as $vals)
			{
				$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
				$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
				$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("process_end_date")];
				$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
			}
			/*echo '<pre>';
			print_r($subcon_load_time_array);die;*/
	
			if ($buyer_name==0  || $buyer_name=='') $party_name_cond =""; else $party_name_cond =" and f.party_id=$buyer_name ";
			if ($order_no=="") $order_no_cond =""; else $order_no_cond =" and p.order_no=$txt_order_no ";
	
			// Main query
			$subcon_sql_result="SELECT a.id, a.batch_no, a.system_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.process_end_date, a.production_date,a.incomplete_result,a.redyeing_needed, a.water_flow_meter, b.batch_qnty, b.item_description, c.machine_no, c.prod_capacity, f.party_id, p.order_no, q.color_id, q.color_range_id, q.total_trims_weight, q.process_id
			from subcon_ord_dtls p, subcon_ord_mst f, pro_batch_create_mst q, pro_fab_subprocess a, pro_batch_create_dtls b, lib_machine_name c
			where a.service_company=$cbo_company_id $order_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $party_name_cond $batch_color_cond $color_range_cond and a.machine_id=c.id and a.entry_form in(38) and q.entry_form=36 and a.load_unload_id=2 and a.process_id not in (203,193) and a.batch_id=q.id and b.po_id=p.id  and p.job_no_mst= f.subcon_job and q.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.process_end_date, c.machine_no";
	
			//echo $subcon_sql_result;die;
	
			$sql_dtls=sql_select($subcon_sql_result);
			//print_r($sql_dtls);
			$subcon_date_data_arr=array();
			$subc_fun_batch_rowspan_arr=array();
			$subc_batch_qty_arr=array();
			foreach ($sql_dtls as $row)
			{
				$sub_all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
				$sub_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
				$sub_all_batch_data[$row[csf('batch_id')]]['result']=$row[csf('result')];
	
				$all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
				$sub_all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
				$subcon_all_batch_id.=$row[csf('batch_id')].',';
				$subcon_all_batch_no.="'".$row[csf('batch_no')]."'".',';
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['system_no']=$row[csf('system_no')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('process_end_date')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_end_date']=$row[csf('production_date')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['item_description'].=$row[csf('item_description')].',';
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
	
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['party_id']=$row[csf('party_id')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['order_no']=$row[csf('order_no')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['incomplete_result']=$row[csf('incomplete_result')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['redyeing_needed']=$row[csf('redyeing_needed')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['shade_matched']=$row[csf('shade_matched')];
				$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];
	
				if($subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=="")
				{
					$subc_fun_batch_rowspan_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('system_no')]]+=1;
					$subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=1;
				}
	
				$subc_batch_qty_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_no')]]=$row[csf('batch_ext_no')];
				$machine_no_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
			}
			$subcon_all_batch_id=implode(",",array_unique(explode(",",$subcon_all_batch_id)));
			$baIds=chop($subcon_all_batch_id,','); $sub_batch_cond_for_in="";
			$ba_ids=count(array_unique(explode(",",$subcon_all_batch_id)));
			if($db_type==2 && $ba_ids>1000)
			{
				$sub_batch_cond_for_in=" and (";
				$baIdsArr=array_chunk(explode(",",$baIds),999);
				foreach($baIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$sub_batch_cond_for_in.=" b.batch_id in($ids) or";
	
				}
				$sub_batch_cond_for_in=chop($sub_batch_cond_for_in,'or ');
				$sub_batch_cond_for_in.=")";
			}
			else
			{
				$sub_batch_cond_for_in=" and b.batch_id in($baIds)";
			}
			$subcon_all_batch_no=implode(",",array_unique(explode(",",$subcon_all_batch_no)));
			$baIds=chop($subcon_all_batch_no,','); $sub_batch_no_cond_for_in="";
			$ba_ids=count(array_unique(explode(",",$subcon_all_batch_no)));
			if($db_type==2 && $ba_ids>1000)
			{
				$sub_batch_no_cond_for_in=" and (";
				$baIdsArr=array_chunk(explode(",",$baIds),999);
				foreach($baIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$sub_batch_no_cond_for_in.=" aa.batch_no in($ids) or";
	
				}
				$sub_batch_no_cond_for_in=chop($sub_batch_no_cond_for_in,'or ');
				$sub_batch_no_cond_for_in.=")";
			}
			else
			{
				$sub_batch_no_cond_for_in=" and aa.batch_no in($baIds)";
			}
	
			 /*echo "<pre>";
			 print_r($self_date_data_arr);*/
		// $sql_subprod="select b.batch_id,b.receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $sub_batch_cond_for_in";
		/*
			 $sql_subprod2="select aa.batch_no,aa.result,max(aa.batch_ext_no) as batch_ext_no,b.batch_id,sum(b.product_qnty) as receive_qnty  from subcon_production_mst a,subcon_production_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.product_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=292 and aa.entry_form=38 $sub_batch_no_cond_for_in group by  aa.batch_no,aa.result,b.batch_id";
			$sub_fin_prod2=sql_select($sql_subprod2);
			foreach($sub_fin_prod2 as $row)
			{
				$batch_ext_no=$row[csf("batch_ext_no")];
				$subcon_fin_prod_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
			}*/
	
			$sql_subprod2="select a.product_no,b.batch_id,b.product_qnty as receive_qnty  from subcon_production_mst a,subcon_production_dtls b where a.id=b.mst_id  and b.product_qnty>0  and a.status_active=1 and a.is_deleted=0 and a.entry_form=292   and b.batch_id in (".implode(",",$sub_all_batch).")";
			$sub_fin_prod2=sql_select($sql_subprod2);
			foreach($sub_fin_prod2 as $row)
			{
				$batch_ext_no=$row[csf("batch_ext_no")];
				$subcon_fin_prod_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
	
				$sub_fin_prod_qty_arr[$row[csf("batch_id")]]["recv_number"]=$row[csf("product_no")];
				$sub_fin_prod_qty_arr[$row[csf("batch_id")]]["receive_qnty"]+=$row[csf("receive_qnty")];
	
	
				$self_fin_prod_qty_arr[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];
	
				$sub_fin_prod_arr_check[$sub_all_batch_data[$row[csf('batch_id')]]['batch_no']][$sub_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["fin_qnty"] +=$row[csf("receive_qnty")];
				$sub_fin_prod_arr_check[$sub_all_batch_data[$row[csf('batch_id')]]['batch_no']][$sub_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["result"] =$sub_all_batch_data[$row[csf('batch_id')]]['result'];
			}
			/*echo "<pre>";
			print_r($subcon_fin_prod_arr3);
		*/
			$subcon_rowspan_arr=array();$ttt=1;
			$subcon_func_batch_arr=array();$sub_trims_chk_batch_arr=array();
			$subc_mc_ut_qty_arr=array();$subcon_total_prod_qty=$summ_subcon_white_qty=$summ_subcon_wash_qty=$summ_subcon_color_qty=$summ_sub_re_pro_qty=$sub_summ_trims_wgt_batch_qty=0;
			foreach ($subcon_date_data_arr as $p_date=>$prod_date_data)
			{
				foreach ($prod_date_data as $machine_id => $machine_data)
				{
					foreach ($machine_data as $batch_id => $row)
					{
						//$subcon_fin_prod_sum=$subcon_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
						//$subcon_fin_prod_sum2=$subcon_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
						$subcon_fin_prod_sum3=$subcon_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
						if($row['batch_ext_no']==1)
						{
						$bext=$row['batch_ext_no'];
						}
						else $bext=$row['batch_ext_no']-1;
						
					//	$bext=$row['batch_ext_no']-1;
						if(($bext*1)<=0) $bext='';
						
						
	
						if ($row['result']==1)
						{
							if ($row['color_range_id']==4)
							{
								$summ_subcon_white_qty += $row['batch_qnty'];
							}
							else if ($row['color_range_id']==7)
							{
								$summ_subcon_wash_qty += $row['batch_qnty'];
							}
							else $summ_subcon_color_qty += $row['batch_qnty'];
	
						}
						else if($row['result']==5 && $subcon_fin_prod_sum3=='')   // Shade match/Redyeing Needed/Fin Prod Available
						{
							if ($row['color_range_id']==4)
							{
								$summ_subcon_white_qty +=0;
							}
							else if ($row['color_range_id']==7)
							{
								$summ_subcon_wash_qty +=0;
							}
							else $summ_subcon_color_qty +=0;
						}
						else if($row['batch_ext_no']>0)
						{
							$summ_subcon_white_qty =0;
							$summ_subcon_wash_qty = 0;
							$summ_subcon_color_qty =0;
							//echo "B";
						}
	
						if ($row['result']==1  && $row['batch_ext_no']=="")
						{
							$subcon_total_prod_qty+= $row['batch_qnty'];
							$subcon_actual_prod_qty= $row['batch_qnty'];
							if ($checkTrimWgtArr[$row['batch_id']]=='')
							{
								$total_summ_trim_wgt+=$row['total_trims_weight'];
							}
								//echo  $row['result'].'='.$re_process_qty3.'<br>';
						}
						else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
						{
							$subcon_total_prod_qty+= 0;
							$subcon_actual_prod_qty= 0;
						}
						if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
						{
	
							$subcon_total_prod_qty+= $row['batch_qnty'];
							$subcon_actual_prod_qty= $row['batch_qnty'];
							if ($checkTrimWgtArr[$row['batch_id']]=='')
							{
								$total_summ_trim_wgt+=$row['total_trims_weight'];
							}
						}
						if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
						{
							$summ_sub_re_pro_qty +=$row['batch_qnty'];// $row['batch_qty'];
							$is_finished[$row['batch_no']]=1;
						}
						if($is_finished[$row['batch_no']]==1) $summ_sub_re_pro_qty +=$row['batch_qnty'];
	
						if($subcon_actual_prod_qty>0)
						{
							if(!in_array($batch_id,$sub_trims_chk_batch_arr))
							{
								$ttt++;
	
								$sub_trims_chk_batch_arr[]=$batch_id;
								$sub_summ_trims_wgt_batch_qty +=$row['total_trims_weight'];
								$sub_trimswgt =$row['total_trims_weight'];
							}
							else { $sub_summ_trims_wgt_batch_qty +=0;$sub_trimswgt =0;}
						}
	
						$summ_mc_total_qty+= $row['batch_qnty']+$sub_trimswgt;
						$subcon_trims_weight += $row['total_trims_weight'];
						//$subcon_total_prod_qty +=$row['batch_qnty']+$row['total_trims_weight'];
						//$subcon_total_prod_qty += $row['batch_qty']+$row['total_trims_weight'];// $subcon_color_qty+$subcon_white_qty+$subcon_pro_qty+$subcon_prod_qty+$subcon_trims_weight;
						$batch_qnty = $row['batch_qnty'];
						$subc_mc_ut_qty_arr[$p_date][$machine_id] += $batch_qnty+$row['total_trims_weight'];
	
						$subcon_total_batch+=count($row['batch_no']);
						$subcon_func_batch_arr[$row['system_no']]=$row['system_no'];
						$subcon_total_func_batch=count($subcon_func_batch_arr);
	
						if (isset($subcon_rowspan_arr[$p_date][$machine_id]))
						{
							$subcon_rowspan_arr[$p_date][$machine_id]+=1;
						}
						else
						{
							$subcon_rowspan_arr[$p_date][$machine_id]=1;
							$subcon_total_machine_capacity+=$row['prod_capacity'];
							$subcon_total_machine+=count($row['machine_no']);
						}
						$subcon_avg_batch_per_mc = $subcon_total_batch/$subcon_total_machine;
					}
				}
			}
			//echo '<pre>';
			//print_r($subcon_rowspan_arr);
		}
	
		if ($order_type==0 || $order_type==3) // Sample Without Order  sql and array
		{
			$sqls="SELECT  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks
			from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
			where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
			and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 and a.process_id not in (203,193)
			group by  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks ";
	
			$load_time_array=array();
			foreach(sql_select($sqls) as $vals)
			{
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("process_end_date")];
				$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
			}
			/*echo '<pre>';
			print_r($load_time_array);die;*/
	
			// Main query
			$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.production_date, a.end_minutes, a.result, a.remarks,a.process_end_date, a.water_flow_meter, a.system_no,a.incomplete_result,a.redyeing_needed,a.shade_matched, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity,
			p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
			from wo_non_ord_samp_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
			where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
			and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2 and a.process_id not in (203,193)
			and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order=1
			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			order by a.process_end_date, c.machine_no";
	
			//echo $sql_result;die;
	
			$sql_dtls=sql_select($sql_result);
			//print_r($sql_dtls);
			$date_data_arr=array();
			$samp_fun_batch_rowspan_arr=array();
			$samp_batch_qty_arr=array();$samp_all_batch_id="";
			foreach ($sql_dtls as $row)
			{
	
				$samp_all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
				$samp_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
				$samp_all_batch_data[$row[csf('batch_id')]]['result']=$row[csf('result')];
	
				$all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
				$samp_all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
				$samp_all_batch_id.=$row[csf('batch_id')].',';
				$samp_all_batch_no.="'".$row[csf('batch_no')]."'".',';
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['system_no']=$row[csf('system_no')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('process_end_date')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_end_date']=$row[csf('production_date')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition'].=$row[csf('const_composition')].',';
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];
	
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['incomplete_result']=$row[csf('incomplete_result')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['redyeing_needed']=$row[csf('redyeing_needed')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['shade_matched']=$row[csf('shade_matched')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];
	
				if($subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=="")
				{
					$samp_fun_batch_rowspan_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('system_no')]]+=1;
					$subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=1;
				}
				$samp_batch_qty_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_no')]]=$row[csf('batch_ext_no')];
				$machine_no_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
			}
			/*echo "<pre>";*/
			//print_r($machine_no_arr);die;
			$tot_machine_no=count($machine_no_arr);
			//echo $tot_machine_no.'D';
			$samp_all_batch_id=implode(",",array_unique(explode(",",$samp_all_batch_id)));
			$baIds=chop($samp_all_batch_id,','); $sam_batch_cond_for_in="";
			$ba_ids=count(array_unique(explode(",",$samp_all_batch_id)));
			if($db_type==2 && $ba_ids>1000)
			{
				$sam_batch_cond_for_in=" and (";
				$baIdsArr=array_chunk(explode(",",$baIds),999);
				foreach($baIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$sam_batch_cond_for_in.=" b.batch_id in($ids) or";
				}
				$sam_batch_cond_for_in=chop($sam_batch_cond_for_in,'or ');
				$sam_batch_cond_for_in.=")";
			}
			else
			{
				$sam_batch_cond_for_in=" and b.batch_id in($baIds)";
			}
			$samp_all_batch_no=implode(",",array_unique(explode(",",$samp_all_batch_no)));//samp_all_batch_no
			$baIds=chop($samp_all_batch_no,','); $sam_batch_no_cond_for_in="";
			$ba_ids=count(array_unique(explode(",",$samp_all_batch_no)));
			if($db_type==2 && $ba_ids>1000)
			{
				$sam_batch_no_cond_for_in=" and (";
				$baIdsArr=array_chunk(explode(",",$baIds),999);
				foreach($baIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$sam_batch_no_cond_for_in.=" aa.batch_no in($ids) or";
				}
				$sam_batch_no_cond_for_in=chop($sam_batch_no_cond_for_in,'or ');
				$sam_batch_no_cond_for_in.=")";
			}
			else
			{
				$sam_batch_no_cond_for_in=" and aa.batch_no in($baIds)";
			}
			// $sql_sam_prod="select b.batch_id,b.receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $sam_batch_cond_for_in";
			/*
			 $sql_sam_prod2="select aa.batch_no,aa.result,max(aa.batch_ext_no) as batch_ext_no,b.batch_id,sum(b.receive_qnty) as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.receive_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $sam_batch_no_cond_for_in group by aa.batch_no,aa.result,b.batch_id,b.machine_no_id";
			$samp_fin_prod2=sql_select($sql_sam_prod2);
			foreach($samp_fin_prod2 as $row)
			{
				$batch_ext_no=$row[csf("batch_ext_no")];
				$samp_re_process_qty_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
			}
		*/
			$sql_sam_prod2="select a.recv_number,b.batch_id,b.receive_qnty as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 and b.batch_id in (".implode(",",$samp_all_batch).")";
			$samp_fin_prod2=sql_select($sql_sam_prod2);
			foreach($samp_fin_prod2 as $row)
			{
				$batch_ext_no=$row[csf("batch_ext_no")];
	
				$samp_fin_prod_qty_arr[$row[csf("batch_id")]]["recv_number"]=$row[csf("recv_number")];
				$samp_fin_prod_qty_arr[$row[csf("batch_id")]]["receive_qnty"]+=$row[csf("receive_qnty")];
	
				$samp_re_process_qty_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
	
				$samp_fin_prod_arr_check[$samp_all_batch_data[$row[csf('batch_id')]]['batch_no']][$samp_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["fin_qnty"] +=$row[csf("receive_qnty")];
				$samp_fin_prod_arr_check[$samp_all_batch_data[$row[csf('batch_id')]]['batch_no']][$samp_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["result"] =$samp_all_batch_data[$row[csf('batch_id')]]['result'];
			}
			$rowspan_arr=array();$tttt=1;
			$smp_mc_ut_qty_arr=array();$samp_trims_chk_batch_arr=array();$sample_total_prod_qty=$samp_summ_re_pro_qty=$samp_summ_re_pro_qty=$samp_summ_re_pro_qty=$samp_summ_re_pro_qty=0;
			$samp_rowspan_arr=array();
			foreach ($date_data_arr as $p_date=>$prod_date_data)
			{
				$samp_rowspan=0;
				foreach ($prod_date_data as $machine_id => $machine_data)
				{
					foreach ($machine_data as $batch_id => $row)
					{
						$samp_fin_prod_summ=$samp_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
						$samp_fin_prod_summ3=$samp_re_process_qty_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
						$sample_trims_weight = $row['total_trims_weight'];
						//$sample_total_prod_qty+= $row['batch_qty']+$row['total_trims_weight'];// $sample_color_qty+$sample_white_qty+$sample_pro_qty+$sample_prod_qty+$sample_trims_weight;
						//echo $sample_total_prod_qty.'DDD';
						if($row['batch_ext_no']==1)
						{
						$bext=$row['batch_ext_no'];
						}
						else $bext=$row['batch_ext_no']-1;
						
						//$bext=$row['batch_ext_no']-1;
						if(($bext*1)<=0) $bext='';
						
						
						if ($row['result']==1)
						{
							if ($row['color_range_id']==4)
							{
								$self_summ_white_qty += $row['batch_qty'];
							}
							else if ($row['color_range_id']==7)
							{
								$self_summ_wash_qty += $row['batch_qty'];
							}
							else $self_summ_color_batch_qty += $row['batch_qty'];
	
						}
						else if ($row['result']==5 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=="") // //Under Trial//Fin Fab not Avaiable
						{
							if ($row['color_range_id']==4)
							{
								$self_summ_white_qty +=0;
							}
							else if ($row['color_range_id']==7)
							{
								$self_summ_wash_qty +=0;
							}
							else $self_summ_color_batch_qty +=0;
						}
						else if($row['batch_ext_no']>0)
						{
							$self_summ_white_qty +=0;
							$self_summ_wash_qty += 0;
							$self_summ_color_batch_qty +=0;
						}
	
						if ($row['result']==1 && $row['batch_ext_no']=="")
						{
							$sample_total_prod_qty+= $row['batch_qty'];
							$samp_actual_prod_qty=$row['batch_qty'];
							if ($checkTrimWgtArr[$row['batch_id']]=='')
							{
								$total_summ_trim_wgt+=$row['total_trims_weight'];
							}
								//echo  $row['result'].'='.$re_process_qty3.'<br>';
						}
						else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
						{
							$sample_total_prod_qty+= 0;
							$samp_actual_prod_qty=0;
						}
						if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
						{
							$sample_total_prod_qty+= $row['batch_qty'];
							$samp_actual_prod_qty=$row['batch_qty'];
							if ($checkTrimWgtArr[$row['batch_id']]=='')
							{
								$total_summ_trim_wgt+=$row['total_trims_weight'];
							}
						}
	
						if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
						{
							$self_summ_re_pro_qty +=$row['batch_qty'];// $row['batch_qty'];
							$is_finished[$row['batch_no']]=1;
						}
						if($is_finished[$row['batch_no']]==1) $self_summ_re_pro_qty +=$row['batch_qty'];
						if($samp_actual_prod_qty>0)
						{
							if(!in_array($batch_id,$samp_trims_chk_batch_arr))
							{
								$tttt++;
								$samp_trims_chk_batch_arr[]=$batch_id;
								$self_summ_trims_wgt_batch_qty+=$row['total_trims_weight'];
								$samp_trimswgt =$row['total_trims_weight'];
							}
							else { $self_summ_trims_wgt_batch_qty+=0;$samp_trimswgt =0;}
						}
						$summ_mc_total_qty+= $row['batch_qty']+$samp_trimswgt;
	
						$smp_batch_qty = $row['batch_qty'];
						$smp_mc_ut_qty_arr[$p_date][$machine_id] += $smp_batch_qty+$row['total_trims_weight'];
						$sample_total_batch+=count($row['batch_no']);
						$sample_func_batch_arr[$row['system_no']]=$row['system_no'];
						$sample_total_func_batch=count($sample_func_batch_arr);
	
						$samp_rowspan++;
	
						if (isset($rowspan_arr[$p_date][$machine_id]))
						{
							$rowspan_arr[$p_date][$machine_id]++;
						}
						else
						{
							$rowspan_arr[$p_date][$machine_id]=1;
							$sample_total_machine_capacity+=$row['prod_capacity'];
							$sample_total_machine+=count($row['machine_no']);
						}
						$sample_avg_batch_per_mc = $sample_total_batch/$sample_total_machine;
					}
					$samp_rowspan_arr[$p_date][$machine_id][$row['system_no']]=$samp_rowspan;
				}
			}
			//echo '<pre>';
			//print_r($samp_rowspan_arr);
		}
	
		// mc idle sql
		$mc_idle = "SELECT a.machine_entry_tbl_id, a.machine_no, a.from_date, a.from_hour, a.from_minute, a.to_date, a.to_hour, a.to_minute, a.machine_idle_cause, a.remarks, b.batch_id
		from pro_cause_of_machine_idle a, pro_fab_subprocess b
		where a.machine_entry_tbl_id=b.machine_id and a.status_active=1 and a.is_deleted=0 $idle_sql_cond";
	
		//echo $mc_idle;die;
	
		$mc_idle_result=sql_select($mc_idle);
		$idle_data_arr=array();
		foreach ($mc_idle_result as $rows)
		{
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine"]=$rows[csf("machine_entry_tbl_id")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_no"]=$rows[csf("machine_no")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_date"]=$rows[csf("from_date")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_hour"]=$rows[csf("from_hour")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_minute"]=$rows[csf("from_minute")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_date"]=$rows[csf("to_date")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_hour"]=$rows[csf("to_hour")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_minute"]=$rows[csf("to_minute")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_idle_cause"]=$rows[csf("machine_idle_cause")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["remarks"]=$rows[csf("remarks")];
			
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine"]=$rows[csf("machine_entry_tbl_id")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine_no"]=$rows[csf("machine_no")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_date"]=$rows[csf("from_date")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_hour"]=$rows[csf("from_hour")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_minute"]=$rows[csf("from_minute")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_date"]=$rows[csf("to_date")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_hour"]=$rows[csf("to_hour")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_minute"]=$rows[csf("to_minute")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine_idle_cause"]=$rows[csf("machine_idle_cause")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["remarks"]=$rows[csf("remarks")];
		}
		/*echo '<pre>';
		print_r($idle_data_arr);die;*/
			//Chemical and Dyes issue cost sql
		//$batch_id_string = "'".implode ( "', '",$all_batch)."'";
		//if($batch_id_string!="") $issue_batch_cond=" and b.batch_id in($batch_id_string)";
	
		$all_batch_id="'".implode ( "', '",array_unique($all_batch))."'";
		$issue_batch_cond="";
		$all_batch_id_arr=explode(",", trim($all_batch_id));
		if($db_type==2 && count($all_batch_id_arr)>999)
		{
			$chunk_arr=array_chunk($all_batch_id_arr, 999);
			foreach($chunk_arr as $keys=>$vals)
			{
				$po_ids=implode(",", $vals);
				if($issue_batch_cond=="")
				{
					$issue_batch_cond.=" and ( b.batch_id in ($po_ids) ";
				}
				else
				{
					$issue_batch_cond.=" or b.batch_id in ($po_ids) ";
				}
			}
			$issue_batch_cond.=" ) ";
		}
		else
		{
			$issue_batch_cond=" and b.batch_id in (".$all_batch_id.")";
		}
		//echo $issue_batch_cond;die;
	
		$sql_cost="SELECT a.mst_id, b.batch_id, b.item_category, a.cons_amount from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id and b.item_category in(5,6) and a.company_id=$cbo_company_id and b.is_deleted=0 and b.status_active=1 $issue_batch_cond";
	
		//echo $sql_cost;
		$cost_data_arr=array();
		$sql_res=sql_select($sql_cost);
		foreach ($sql_res as $row)
		{
			$all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
			$cost_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
		}
		/*echo '<pre>';
		print_r($cost_data_arr);die;*/
		//Chemical and Dyes return sql
		$uniq_issue_id=array_unique($all_issue_id);
	
		$all_issue_id="'".implode ( "', '",array_unique($all_issue_id))."'";
		$uniq_issue_id_cond="";
		$all_issue_id_arr=explode(",", trim($all_issue_id));
		if($db_type==2 && count($all_issue_id_arr)>999)
		{
			$chunk_arr=array_chunk($all_issue_id_arr, 999);
			foreach($chunk_arr as $keys=>$vals)
			{
				$po_ids=implode(",", $vals);
				if($uniq_issue_id_cond=="")
				{
					$uniq_issue_id_cond.=" and ( a.issue_id in ($po_ids) ";
				}
				else
				{
					$uniq_issue_id_cond.=" or a.issue_id in ($po_ids) ";
				}
			}
			$uniq_issue_id_cond.=" ) ";
		}
		else
		{
			$uniq_issue_id_cond=" and a.issue_id in (".$all_issue_id.")";
		}
		//echo $uniq_issue_id_cond;die;
	
		if(count($uniq_issue_id)>0)
		{
			$sql_return_cost="SELECT p.batch_no as batch_id, a.item_category, a.cons_amount
			from inv_issue_master p, inv_transaction a, inv_receive_master b
			where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6) and a.company_id=$cbo_company_id and b.is_deleted=0 and b.status_active=1 $uniq_issue_id_cond";
			//echo $sql_return_cost;die;
			$cost_return_data_arr=array();
			$sql_return_res=sql_select($sql_return_cost);
			foreach ($sql_return_res as $row)
			{
				$cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
			}
		}
	
		/*echo '<pre>';
		print_r($cost_return_data_arr);die;*/
		?>
		<div style="width: 2160px;">
			<table width="2010" cellpadding="0" cellspacing="0" id="caption" align="center">
				<tr>
					<td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
				</tr>
			</table>
	
			<style>
				#tdtag
				{
					border: 1px solid #8DAFDA;
				}
			</style>
	
			<div style="margin-left:6px;">
				<table width="600"   border="0" cellpadding="0" cellspacing="0" >
					<tr>
						<td>
							<table width="200" id="tdtag" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
								<thead>
									<tr>
										<th colspan="2" id="tdtag" style="text-align: center;"><strong>Production Qty in KG Summary</strong></th>
									</tr>
									<tr>
										<td id="tdtag" style="background: #E9F3FF;"><strong>Total Color </strong></td>
										<td id="tdtag" style="background: #E9F3FF;"><strong><?php
										$tot_summ_color_batch_qty=$self_summ_color_batch_qty+$summ_subcon_color_qty;
										echo number_format($tot_summ_color_batch_qty,2,'.',''); ?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #FFFFFF;"><strong>Total White</strong></td>
										<td id="tdtag" style="background: #FFFFFF;"><strong><?php
										$tot_summ_white_qty=$self_summ_white_qty+$summ_subcon_white_qty;
										echo  number_format($tot_summ_white_qty,2,'.',''); ?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #E9F3FF;"><strong>Total Wash (Y/D)</strong></td>
										<td id="tdtag" style="background: #E9F3FF;"><strong><?php
										$tot_summ_wash_qty=$self_summ_wash_qty+$summ_subcon_wash_qty;
										echo number_format($tot_summ_wash_qty,2,'.',''); ?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #FFFFFF;"><strong>Total Re-Process</strong></td>
										<td id="tdtag" style="background: #FFFFFF;"><strong><?php
										$tot_summ_re_pro_qty=$self_summ_re_pro_qty+$summ_sub_re_pro_qty;
										echo number_format($tot_summ_re_pro_qty,2,'.','');
										?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #FFFFFF;" title="Total Machine/Total Functional Batch"><strong>Total Trims Weight</strong></td>
										<td id="tdtag" style="background: #FFFFFF;"><strong><?php
										$tot_summ_trims_wgt_batch_qty=$self_summ_trims_wgt_batch_qty+$sub_summ_trims_wgt_batch_qty;	echo number_format($tot_summ_trims_wgt_batch_qty,2,'.','');
										?></strong></td>
									</tr>
								</thead>
							</table>
						</td>
						<td width="10" height="5">
						</td>
						<td>
							<table width="300"  style=" margin:5px;" id="tdtag"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
								<thead>
									<tr>
										<th colspan="2" id="tdtag" style="text-align: center;"><strong>Summary</strong></th>
									</tr>
									<tr>
										<td id="tdtag" style="background: #E9F3FF;"><strong>Total Actual Production </strong></td>
										<td id="tdtag" style="background: #E9F3FF;"><strong><?php
										$tot_actual_prod_qty=$tot_self_actual_prod_qty+$subcon_total_prod_qty+$sample_total_prod_qty+$total_summ_trim_wgt;
										echo number_format($tot_actual_prod_qty,2); //$self_prod_qty+$subcon_total_prod_qty+$sample_total_prod_qty; ?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #E9F3FF;"><strong>Total Machine Production </strong></td>
										<td id="tdtag" style="background: #E9F3FF;"><strong><?php
	
										echo number_format($summ_mc_total_qty,2); ?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #FFFFFF;"><strong>Total Machine Capacity</strong></td>
										<td id="tdtag" style="background: #FFFFFF;"><strong><?php echo number_format($self_total_machine_capacity+$subcon_total_machine_capacity+$sample_total_machine_capacity,2); ?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #E9F3FF;"><strong>Total Machine</strong></td>
										<td id="tdtag" style="background: #E9F3FF;"><strong><?php echo $tot_machine_no;//$self_total_machine+$subcon_total_machine+$sample_total_machine; ?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #FFFFFF;"><strong>Total Functional Batch</strong></td>
										<td id="tdtag" style="background: #FFFFFF;"><strong><?php
										$tot_functional_batch=$tot_self_func_batch_arr+$subcon_total_func_batch+$sample_total_func_batch;
										echo number_format($tot_functional_batch,2,'.','');
										?></strong></td>
									</tr>
									<tr>
										<td id="tdtag" style="background: #FFFFFF;" title="Total Machine/Total Functional Batch"><strong>Avg Batch Per M/C</strong></td>
										<td id="tdtag" style="background: #FFFFFF;"><strong><?php
								echo number_format($tot_functional_batch/$tot_machine_no,2,'.','');//number_format($self_avg_batch_per_mc+$subcon_avg_batch_per_mc+$sample_avg_batch_per_mc,2,'.','');
								?></strong></td>
							</tr>
						</thead>
					</table>
				</td>
			</tr>
		</table>
		</div>
	
		<?
	
		if ($order_type==0 || $order_type==1) // Self Order
		{
			?>
			<div>
				<table width="2450" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th colspan="29" style="text-align: left;">Self Order</th>
						</tr>
					</thead>
				</table>
				<table width="2450" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th rowspan="2" width="30">SL No</th>
							<th rowspan="2" width="100">M/C & Capacity</th>
							<th rowspan="2" width="80">Functional Batch No.</th>
							<th rowspan="2" width="100">Buyer Name</th>
							<th rowspan="2" width="80">Batch No.</th>
							<th rowspan="2" width="100">Batch Color</th>
							<th rowspan="2" width="120">Booking No.</th>
							<th rowspan="2" width="80">Batch Qty</th>
							<th rowspan="2" width="50">Ext. No</th>
							<th rowspan="2" width="100">Color Range</th>
							<th colspan="5" >Total Production Qty in Kg</th>
							<th rowspan="2" width="70">MC Total Production</th>
							<th rowspan="2" width="70">Actual Production</th>
							<th rowspan="2" width="70"><p>Incomplete Production</p></th>
							<th rowspan="2" width="110">Result</th>
							<th rowspan="2" width="70">Water/kg in Ltr</th>
							<th rowspan="2" width="70">M/C UT%</th>
							<th rowspan="2" width="80">Loading Time</th>
							<th rowspan="2" width="80">Unloading Time</th>
							<th rowspan="2" width="80">Total Time (Hour)</th>
							<th rowspan="2" width="70"><p>Chemical Cost</p></th>
							<th rowspan="2" width="70">Dyes Cost</th>
							<th rowspan="2" width="70">Total Cost</th>
							<th rowspan="2" width="">Fabric Construction</th>
	
							<th rowspan="2"  width="100">Remarks</th>
						</tr>
						<tr>
							<th width="60" title="Data is showing, according to [Color Range] - Average Color, from batch creation page.">Color</th>
							<th width="60" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
							<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
							<th width="60">Re-Process</th>
							<th width="60">Trims Weight</th>
						</tr>
					</thead>
				</table>
				<div style="width:2468px; overflow-y:scroll; max-height:300px;" id="scroll_body">
					<table cellspacing="0" width="2450"  border="1" rules="all" class="rpt_table" >
	
						<?
						$i=1;
						$other_grnd_total=$white_grnd_total=$wash_grnd_total=$pro_qty_grnd_total=$trims_weight_grnd_total=$grnd_total_batch=$grnd_total_self_functional_batch=$self_production_grnd_total=$incomplete_qty_grnd_total=$self_actual_production_grnd_total=0;
						$array_check=array();
	
						foreach ($self_date_data_arr as $p_date=>$prod_date_data)
						{
							$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$pro_qty_date_wise_total=$trims_weight_date_wise_total=$date_wise_total_batch=$date_wise_self_functional_batch=$date_wise_total_self_production=$date_wise_total_self_actual_production=$date_wise_total_self_incomplete_qty=0;
							$total_batch=0;
							?>
							<tr>
								<td colspan="29" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
							</tr>
							<?
							foreach ($prod_date_data as $machine_id => $machine_data)
							{
								$other_mcw_wise_total=$white_mcw_wise_total=$wash_mcw_wise_total=$total_pro_qty=$total_trims_weight=$mcw_wise_total_batch=$total_self_production_qty=$total_self_incomplete_qty=$total_actual_prod_qty=0;
								$self_functional_batch_arr=array();
								$r=0;
								foreach ($machine_data as $batch_id => $row)
								{
							//echo 'Test '.$batch_qty_arr[$p_date][$machine_id][$row['batch_no']].'<br>';
						/*$prod_fin_qnty=$self_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
						$prod_fin_qnty2=$self_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
						$re_process_qty=$self_re_process_qty_arr[$row['batch_no']][$row['result']]['re_process_qty'];*/
						$re_process_qty3=$self_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
						$self_fin_prod_check=$self_fin_prod_arr_check[$row[csf("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
	
						$recv_number=$self_fin_prod_qty_arr[$batch_id]["recv_number"];
						$self_receive_qnty=$self_fin_prod_qty_arr[$batch_id]["receive_qnty"];
						if($self_receive_qnty>0) $batch_color_td="green";else  $batch_color_td="";
	
	
						//echo $row[('batch_ext_no')].'='.$prod_fin_qnty2.'<br>'; $batch_ext_no
						//$re_pro_qty = $re_process_qty;
						//echo $self_fin_prod_check."=";
							//echo $self_fin_prod_check_main=$self_fin_prod_arr_check[$row[csf("batch_no")]]['']["fin_qnty"];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
							<?
							if ($r==0)
							{
								?>
								<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
								{
									$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
									echo $self_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
								}
								else
								{
									echo $self_rowspan_arr[$p_date][$machine_id];
								}
								?>" width="30" valign="middle"><? echo $i;?></td>
								<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
								{
									$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
									echo $self_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
								}
								else
								{ 
									$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
									echo $self_rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
								}
								?>" width="100"  valign="middle"> <div style="word-wrap:break-word; width:100px;"><? echo $row['machine_no'].'-'.$row['prod_capacity'].'kg';?> </div></td>
								<?
							} $r++;
	
									//if($arrs[$p_date][$machine_id][$row['system_no']]=="")
									//{
							?>
							<td rowspan="<? //echo $fun_batch_rowspan_arr[$p_date][$machine_id][$row['system_no']] ?>" width="80" valign="middle" style="word-wrap:break-word; word-break: break-all;"><? echo $functional_batch=$row['system_no']; $self_functional_batch_arr[$row['system_no']]=$row['system_no']; ?></td>
							<?
									//	$arrs[$p_date][$machine_id][$row['system_no']]=$row['system_no'];
									//}
							if($row['batch_ext_no']>0) $extBgcolor = "yellow";else $extBgcolor="";
							?>
							<td width="100" valign="middle"><div style="word-wrap:break-word; word-break: break-all;"><? echo $buyer_library[$row['buyer_id']];?></div></td>
							<td width="80" valign="middle" title="Recv No=<? echo $recv_number.',Fin Qty='.$self_receive_qnty; ?>" style=" word-break:break-all"><p><b style=" color:<? echo $batch_color_td;?>"><? echo $row['batch_no'];
	
							$mcw_wise_total_batch+=count($row['batch_no']); ?></b></td>
							<td width="100" valign="middle"><p><? echo $color_library[$row['color_id']]?></p></td>
							<td width="120" valign="middle"><div style="word-wrap:break-word; width:120px;"><? echo $row['booking_no'];?></div></td>
							<td width="80" valign="middle" title="Fabric + Trims " align="right"><? echo number_format($row['batch_qty']+$row['total_trims_weight'],2,'.','');?></td>
							<td width="50" valign="middle" bgcolor="<? echo $extBgcolor ;?>" align="center"><? echo $row['batch_ext_no'];?></td>
							<td width="100" valign="middle"><div style="word-wrap:break-word; word-break: break-all;"><? echo $color_range[$row['color_range_id']];?></div></td>
							<?
							$avarage_batch_qty=$white_batch_qty=$wash_batch_qty=$actual_prod_qty=$re_pro_qty=$other_qty=0;
								//if (($row['result']==1 || $row['result']==2)  && $re_process_qty3=='')  //Shade match/Redyeing Needed/Fin Prod not Available
							if($row['batch_ext_no']==1)
							{
							$bext=$row['batch_ext_no'];
							}
							else $bext=$row['batch_ext_no']-1;
							//$bext=$row['batch_ext_no']-1;
							if(($bext*1)<=0) $bext='';
	
								if ($row['result']==1) //Shade match
								{
									if ($row['color_range_id']==4)
									{
										$white_batch_qty = $row['batch_qty'];
									}
									else if ($row['color_range_id']==7)
									{
										$wash_batch_qty = $row['batch_qty'];
									}
									else $avarage_batch_qty = $row['batch_qty'];
	
								}
								else if ($row['result']==5  &&  $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=='')  //Under Trial/Fin Prod not Available
								{
									if ($row['color_range_id']==4)
									{
										$white_batch_qty =0;
									}
									else if ($row['color_range_id']==7)
									{
										$wash_batch_qty = 0;
									}
									else $avarage_batch_qty =0;
								}
								else if($row['batch_ext_no']>0)
								{
									$white_batch_qty =0;
									$wash_batch_qty = 0;
									$avarage_batch_qty =0;
									//echo "B";
								}
								//if (($row['result']==1 || $row['result']==2) && $re_process_qty3>0)  //Shade match/Redyeing Needed/Fin Prod Available
								if ($row['result']==1  && $row['batch_ext_no']=="")
								{
									$actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
								}
								else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
								{
									$actual_prod_qty = 0;
								}
								if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
								{
									$actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
								}
							//if (($row['result']==1 || $row['result']==2) && $row['batch_ext_no']>0 && $re_process_qty3>0 ) //Shade match/Redyeing Needed/Re Process /Fin Prod Available
								if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
								{
									$re_pro_qty =$row['batch_qty'];// $row['batch_qty'];
									$is_fnished[$row['batch_no']]=1;
								}
								if($is_fnished[$row['batch_no']]==1) $re_pro_qty =$row['batch_qty'];// $row['batch_qty'];
								if($row['incomplete_result']==4)
								{
									$self_incomplete_qty=$row['batch_qty']+$row['total_trims_weight'];
								}
								else $self_incomplete_qty=0;
	
								$total_self_incomplete_qty+=$self_incomplete_qty;//$self_incomplete_qty;
								if($actual_prod_qty>0) $trims_weight=$row['total_trims_weight']; else $trims_weight="";
								?>
								<td width="60" valign="middle" align="right"><? echo number_format($avarage_batch_qty,2,'.',''); $other_mcw_wise_total+=$avarage_batch_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($white_batch_qty,2,'.',''); $white_mcw_wise_total+=$white_batch_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($wash_batch_qty,2,'.',''); $wash_mcw_wise_total+=$wash_batch_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($re_pro_qty,2,'.',''); $total_pro_qty+=$re_pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($trims_weight,2,'.',''); $total_trims_weight+=$trims_weight;?></td>
								<td width="70" valign="middle" align="right"><? echo number_format($total_self_production,2,'.','');//=$row['batch_qty']+$row['total_trims_weight'];
								//($avarage_batch_qty+$white_batch_qty+$wash_batch_qty+$re_pro_qty+$trims_weight);
								$total_self_production_qty+=$total_self_production; ?></td>
								<td width="70" valign="middle" align="right"><? echo number_format($actual_prod_qty,2,'.',''); $total_actual_prod_qty+=$actual_prod_qty;?></td>
								<td width="70" valign="middle" align="right"><? echo number_format($self_incomplete_qty,2,'.','');//if($row['incomplete_result']==4) echo $total_self_incomplete_qty=$row['batch_qty']; $total_self_incomplete_qty+=$self_incomplete_qty; ?></td>
								<? if($row['result']==1) { $resultBgcolor = "green";} else if($row['result']==2) {$resultBgcolor = "yellow";} else{$resultBgcolor = "#FFFFFF";} ?>
								<td width="110" valign="middle" bgcolor="<? echo $resultBgcolor; ?>"> <div style="word-wrap:break-word; width:110px;"><? echo $dyeing_result[$row['result']];?></div></td>
								<td width="70" valign="middle" align="right"><? echo $row['water_flow_meter'];?></td>
								<?
								$total_btch_qty = $row['batch_qty']+$row['total_trims_weight'];
								$mc_capacity = $row['prod_capacity'];
								$mc_ut = ($mc_ut_qty_arr[$p_date][$machine_id]*100)/$mc_capacity;
								$mc_ut_result = ($mc_ut*$total_btch_qty)/$mc_ut_qty_arr[$p_date][$machine_id];
								?>
								<td width="70" valign="middle"><? echo number_format($mc_ut_result,2,'.','');?></td>
								<?
								$load_hour = $self_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $self_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
								$process_start_date = $self_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
								$start_time = $load_hour.'.'.$load_minut;
								$load_hour_minut = date('h:i A', strtotime($start_time));
								?>
								<td width="80" valign="middle"><? echo $load_hour_minut;?></td>
								<?
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$process_end_dateIs = $row['production_end_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$unload_hour_minut = date('h:i A', strtotime($end_time));
								?>
								<td width="80" valign="middle"><div style="word-wrap:break-word; width:80px;"><? echo $unload_hour_minut; ?></div></td>
								<?
								$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
								$prod_start_date_time=strtotime($start_date_time);
	
								$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
								$prod_end_date_time=strtotime($end_date_time);
	
								$diff = ($prod_end_date_time - $prod_start_date_time);
								$total = $diff/60;
								$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
								?>
								<td width="80" valign="middle"><? echo $total_time; ?></td>
								<td width="70" valign="middle" align="right">
									<?	$chemical_return_cost=$cost_return_data_arr[$batch_id][5];
									$chemical_issue_cost=$cost_data_arr[$batch_id][5];
									$chemical_cost=$chemical_issue_cost-$chemical_return_cost;
									echo number_format($chemical_cost,2,'.','');
									?></td>
									<td width="70" valign="middle" align="right"><?
									$dyes_return_cost=$cost_return_data_arr[$batch_id][6];
									$dyes_issue_cost=$cost_data_arr[$batch_id][6];
									$dyes_cost=$dyes_issue_cost-$dyes_return_cost;
									echo number_format($dyes_cost,2,'.',''); ?></td>
									<td width="70" valign="middle" align="right"><p><? echo number_format($chemical_cost+$dyes_cost,2,'.',''); ?></p></td>
									<td width=""><p style="word-break:break-all">
	
										<?
										$fabr_compo = trim($row['const_composition']);
										$str = implode(',',array_unique(explode(',', $fabr_compo)));
										echo chop($str,',');
										?>
									</p>
								</td>
	
								<?
								$unload_remarks = $row['remarks'];
								$load_remarks = $self_load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
								if ($unload_remarks=="")
								{
									?><td valign="middle"  width="100"><div style="word-wrap:break-word; width:100px;"><? echo $load_remarks;?></div></td><?
								}
								else
								{
									?><td valign="middle" width="100"><div style="word-wrap:break-word; width:100px;"><? echo $row['remarks']; ?></div></td><?
								}
								?>
							</tr>
							<?
							//$i++;
							//$other_mcw_wise_total+=$other_pro_qty;
							//$white_mcw_wise_total+=$white_pro_qty;
	
						}
						//if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
						foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
						{
							?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$cause_id];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
									$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut;
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
									$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
									$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];
	
									$before = strtotime($from_date . " " . $start_hour_minut);
									$after = strtotime($to_date . " " . $end_hour_minut);
									$diff = $after - $before;
										//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments
	
										// $diff is in seconds
									$hours = floor($diff / 3600);
									$minutes = floor(($diff - $hours * 3600) / 60);
									$seconds = $diff - $hours * 3600 - $minutes * 60;
									echo $hours.'.'.$minutes.'H';
									?>
								</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<?
						}
						?>
						<tr class="tbl_bottom">
							<td colspan="2" align="right"><b>Batch Per M/C :</b></td>
							<td><strong><? echo $mcw_wise_self_functional_batch_arr=count($self_functional_batch_arr); ?></strong></td>
							<td><strong></strong></td>
							<td><strong><? echo number_format($mcw_wise_total_batch,2,'.',''); ?></strong></td>
							<td colspan="5" align="right"><b>Total:</b></td>
							<td><strong><? echo number_format($other_mcw_wise_total,2,'.','');?></strong></td>
							<td><strong><? echo number_format($white_mcw_wise_total,2,'.','');?></strong></td>
								<td><strong><? echo number_format($wash_mcw_wise_total,2,'.','');?></strong></td>
									<td><strong><? echo number_format($total_pro_qty,2,'.','')?></strong></td>
										<td><strong><? echo number_format($total_trims_weight,2,'.','');?></strong></td>
											<td><strong><? echo number_format($total_self_production_qty,2,'.','');?></strong></td>
												<td><strong><? echo number_format($total_actual_prod_qty,2,'.','');?></strong></td>
													<td><strong><? echo number_format($total_self_incomplete_qty,2,'.','');?></strong></td>
														<td colspan="11"></td>
													</tr>
													<?
													$i++;
													$other_date_wise_total+=$other_mcw_wise_total;
													$white_date_wise_total+=$white_mcw_wise_total;
													$wash_date_wise_total+=$wash_mcw_wise_total;
													$pro_qty_date_wise_total+=$total_pro_qty;
													$trims_weight_date_wise_total+=$total_trims_weight;
													$date_wise_total_batch+=$mcw_wise_total_batch;
													$date_wise_total_self_production+=$total_self_production_qty;
													$date_wise_total_self_actual_production+=$total_actual_prod_qty;
													$date_wise_total_self_incomplete_qty+=$total_self_incomplete_qty;
													$date_wise_self_functional_batch+=$mcw_wise_self_functional_batch_arr;
												}
												?>
												<tr class="tbl_bottom">
													<td colspan="2" align="right"><b>Date wise Batch:</b></td>
													<td><strong><? echo $date_wise_self_functional_batch;?></strong></td>
													<td><strong></strong></td>
													<td><strong><? echo number_format($date_wise_total_batch,2,'.','');?></strong></td>
													<td colspan="5" align="right"><b>Date wise Total:</b></td>
													<td><strong><? echo number_format($other_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($white_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($wash_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($pro_qty_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($trims_weight_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_self_production,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_self_actual_production,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_self_incomplete_qty,2,'.','');?></strong></td>
													<td colspan="11"></td>
												</tr>
												<?
												$other_grnd_total+=$other_date_wise_total;
												$white_grnd_total+=$white_date_wise_total;
												$wash_grnd_total+=$wash_date_wise_total;
												$pro_qty_grnd_total+=$pro_qty_date_wise_total;
												$trims_weight_grnd_total+=$trims_weight_date_wise_total;
												$self_production_grnd_total+=$date_wise_total_self_production;
												$self_actual_production_grnd_total+=$date_wise_total_self_actual_production;
												$incomplete_qty_grnd_total+=$date_wise_total_self_incomplete_qty;
												$grnd_total_batch+=$date_wise_total_batch;
												$grnd_total_self_functional_batch+=$date_wise_self_functional_batch;
											}
											?>
											<tfoot>
												<tr class="tbl_bottom">
													<td colspan="2" align="right"><b>Grand Total Batch:</b></td>
													<td width="80"><strong><? echo $grnd_total_self_functional_batch;?></strong>&nbsp;</td>
													<td width="100"></td>
													<td width="80"><strong><? echo number_format($grnd_total_batch,2,'.','');?></strong>&nbsp;</td>
													<td width="100"></td>
													<td width="120"></td>
													<td width="80"></td>
													<td width="50"></td>
													<td width="100" align="right"><strong>Grand Total :</strong></td>
													<td width="60"><strong><? echo number_format($other_grnd_total,2,'.','');?></strong>&nbsp;</td>
													<td width="60"><strong><? echo number_format($white_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($wash_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($pro_qty_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($trims_weight_grnd_total,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($self_production_grnd_total,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($self_actual_production_grnd_total,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($incomplete_qty_grnd_total,2,'.','');?></strong></td>
													<td colspan="11"></td>
												</tr>
	
												<tr class="tbl_bottom">
													<td colspan="2" align="right"><b>Avg Batch Per M/C:</b></td>
													<td width="80"><strong><? echo number_format($total_mc=$grnd_total_batch/($i-1),2,'.','') ;?></strong>&nbsp;</td>
													<td colspan="26" align="right"></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<br>
								<?
							}
	
		if ($order_type==0 || $order_type==2) // Subcontract Order
		{
			?>
			<div>
				<table width="2350" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th colspan="29" style="text-align: left;">Subcontract Order</th>
						</tr>
					</thead>
				</table>
				<table width="2350" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th rowspan="2" width="30">SL No</th>
							<th rowspan="2" width="100">M/C & Capacity</th>
							<th rowspan="2" width="80">Functional Batch No.</th>
							<th rowspan="2" width="100">Party Name</th>
							<th rowspan="2" width="100">Batch No.</th>
							<th rowspan="2" width="100">Batch Color</th>
							<th rowspan="2" width="100">Order No.</th>
							<th rowspan="2" width="70">Batch Qty</th>
							<th rowspan="2" width="50">Ext. No</th>
							<th rowspan="2" width="100">Color Range</th>
							<th colspan="5" width="">Total Production Qty in Kg</th>
							<th rowspan="2" width="70">MC Total Production</th>
							<th rowspan="2" width="70">Actual Production</th>
							<th rowspan="2" width="70">Incomplete Production</th>
							<th rowspan="2" width="110">Result</th>
							<th rowspan="2" width="80">Water/kg in Ltr</th>
							<th rowspan="2" width="80">M/C UT%y</th>
							<th rowspan="2" width="80">Loading Time</th>
							<th rowspan="2" width="80">Unloading Time</th>
							<th rowspan="2" width="80">Total Time (Hour)</th>
							<th rowspan="2" width="70">Chemical Cost</th>
							<th rowspan="2" width="70">Dyes Cost</th>
							<th rowspan="2" width="70">Total Cost</th>
							<th rowspan="2" width="150">Fabric Construction</th>
	
							<th rowspan="2" width="">Remarks</th>
						</tr>
						<tr>
							<th width="60" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
							<th width="60" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
							<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
							<th width="60">Re-Process</th>
							<th width="60">Trims Weight</th>
						</tr>
					</thead>
				</table>
				<div style="width:2350px; overflow-y:scroll; max-height:300px;" id="scroll_body">
					<table align="center" cellspacing="0" width="2330"  border="1" rules="all" class="rpt_table" >
	
						<?
						$i=1;
						$subcon_other_grnd_total=$subcon_white_grnd_total=$subcon_wash_grnd_total=$subcon_pro_qty_grnd_total=$subcon_trims_weight_grnd_total=$subcon_grnd_total_batch=$subcon_grnd_total_functional_batch=$subc_production_grnd_total=$subcon_grnd_total_actual_batch=$subc_incomplete_qty_grnd_total=0;
						foreach ($subcon_date_data_arr as $p_date=>$prod_date_data)
						{
							$subcon_other_date_wise_total=$subcon_white_date_wise_total=$subcon_wash_date_wise_total=$subcon_pro_qty_date_wise_total=$subcon_trims_weight_date_wise_total=$subcon_date_wise_total_batch=$subcon_date_wise_total_functional_batch=$date_wise_total_subc_production=$date_wise_total_subc_actual_production=$date_wise_total_subc_incomplete_qty=0;
							?>
							<tr>
								<td colspan="29" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
							</tr>
							<?
							foreach ($prod_date_data as $machine_id => $machine_data)
							{
								$subcon_other_mcw_wise_total=$subcon_white_mcw_wise_total=$subcon_wash_mcw_wise_total=$subcon_pro_qty_total=$subcon_trims_weight_total=$subcon_mc_wise_total_batch=$total_subc_production_qty=$subcon_actual_prod_qty_total=$total_subc_incomplete_qty=0;
								$subcon_functional_batch_arr=array();
								$r=0;
								foreach ($machine_data as $batch_id => $row)
								{
							//echo $row['batch_ext_no'];
	
							//echo 'Test '.$subc_batch_qty_arr[$p_date][$machine_id][$row['batch_no']].'<br>';
									$sub_fin_qnty=$subcon_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
									$sub_fin_qnty2=$subcon_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
									$sub_re_process_qty=$sub_re_process_qty_arr[$row['batch_no']][$row['result']]['re_process_qty'];
									$sub_re_process_qty3=$subcon_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
							//echo $sub_re_process_qty3.'DDD'.$row['batch_ext_no'].'<br>';
	
									$sub_recv_number=$sub_fin_prod_qty_arr[$batch_id]["recv_number"];
									$sub_receive_qnty=$sub_fin_prod_qty_arr[$batch_id]["receive_qnty"];
									if($sub_receive_qnty>0) $sub_batch_color_td="green";else  $sub_batch_color_td="";
	
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									?>
									<tr id="<? echo 'vs_'.$i; ?>" onClick="change_color('vs_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
										<?
										if ($r==0)
										{
											?>
											<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $subcon_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
											}
											else
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $subcon_rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
											}
											?>" width="30" valign="middle"><? echo $i;?></td>
											<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $subcon_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
											}
											else
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $subcon_rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
											}
											?>" width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $row['machine_no'].'-'.$row['prod_capacity'].'kg';?></div></td>
											<?
										} $r++;
	
	
								//if($subc_arrs[$p_date][$machine_id][$row['system_no']]=="")
								//{
										?>
										<td rowspan="<? //echo $subc_fun_batch_rowspan_arr[$p_date][$machine_id][$row['system_no']] ?>" width="80" valign="middle" style="word-wrap:break-word; word-break: break-all;"><? echo $subcon_functional_batch=$row['system_no']; $subcon_functional_batch_arr[$row['system_no']]=$row['system_no']; ?></td>
										<?
									//$subc_arrs[$p_date][$machine_id][$row['system_no']]=$row['system_no'];
								//}
										if($row['batch_ext_no']>0) $sub_extBgcolor = "yellow";else $sub_extBgcolor="";
										?>
	
										<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $buyer_library[$row['party_id']];?></div></td>
	
										<td width="100" valign="middle" style="word-wrap:break-word; word-break: break-all;"  title="Recv No=<? echo $sub_recv_number.',Fin Qty='.$sub_receive_qnty; ?>">
											<b style=" color:<? echo $sub_batch_color_td;?>"><? echo $row['batch_no']; $subcon_mc_wise_total_batch+=count($row['batch_no']);?></b></td>
											<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $color_library[$row['color_id']]?></div></td>
											<td width="100" valign="middle"><? echo $row['order_no'];?></td>
	
											<td width="70" valign="middle" title="Fabric + Trims" align="right"><? echo number_format($row['batch_qnty']+$row['total_trims_weight'],2,'.','');?></td>
											<td width="50" valign="middle" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $sub_extBgcolor;?>" align="center"><? echo $row['batch_ext_no'];?></td>
											<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $color_range[$row['color_range_id']];?></div></td>
											<?
								//sub_fin_prod_arr_check
											$subcon_other_pro_qty=$subcon_white_pro_qty=$subcon_wash_pro_qty=$pro_qty=$sub_actual_prod_qty=$sub_other_qty=0;
								//if (($row['result']==1 || $row['result']==2) && $sub_re_process_qty3=='')   // Shade match/Redyeing Needed/Fin Prod Available
									if($row['batch_ext_no']==1)
									{
									$bext=$row['batch_ext_no'];
									}
									else $bext=$row['batch_ext_no']-1;
											//$bext=$row['batch_ext_no']-1;
								if(($bext*1)<=0) $bext='';
								if($row['result']==1)   // Shade match/Redyeing Needed/Fin Prod Available
								{
									if ($row['color_range_id']==4)
									{
										$subcon_white_pro_qty = $row['batch_qnty'];
									}
									else if ($row['color_range_id']==7)
									{
										$subcon_wash_pro_qty = $row['batch_qnty'];
									}
									else $subcon_other_pro_qty = $row['batch_qnty'];
											//$sub_actual_prod_qty = $row['batch_qnty']+$row['total_trims_weight'];
	
								}
								else if($row['result']==5 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=='')   // Shade match/Redyeing Needed/Fin Prod Available
								{
									if ($row['color_range_id']==4)
									{
										$subcon_white_pro_qty = 0;
									}
									else if ($row['color_range_id']==7)
									{
										$subcon_wash_pro_qty = 0;
									}
									else $subcon_other_pro_qty = 0;
											//$sub_actual_prod_qty = $row['batch_qnty']+$row['total_trims_weight'];
								}
								if($row['batch_ext_no']>0)
								{
									$subcon_white_pro_qty =0;
									$subcon_wash_pro_qty = 0;
									$subcon_other_pro_qty =0;
									//echo "B";
								}
	
								if ($row['result']==1 &&  $row['batch_ext_no']=="")  //Shade match/Redyeing Needed/Fin Prod Available
								{
									$sub_actual_prod_qty =$row['batch_qnty']+$row['total_trims_weight'];
								}
								else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available
								{
									$sub_actual_prod_qty = 0;
								}
								if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
								{
									$sub_actual_prod_qty = $row['batch_qnty']+$row['total_trims_weight'];
								}
	
								//if (($row['result']==1 || $row['result']==2) && $row['batch_ext_no']>0 && $sub_re_process_qty3>0 ) //Shade match/Redyeing Needed/Redying/Fin Prod Available
								if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
								{
									$pro_qty =$row['batch_qnty'];// $row['batch_qty'];
									$is_fnished[$row['batch_no']]=1;
								}
								if($is_fnished[$row['batch_no']]==1) $pro_qty =$row['batch_qnty'];
	
								if($row['incomplete_result']==4)
								{
									$incomplete_qty=$row['batch_qty']+$row['total_trims_weight'];
								}
								else $incomplete_qty=0;
								$total_subc_incomplete_qty+=$incomplete_qty;
								if($sub_actual_prod_qty>0) $trims_weight = $row['total_trims_weight']; else $trims_weight="";
								?>
								<td width="60" valign="middle" align="right"><? echo number_format($subcon_other_pro_qty,2,'.',''); $subcon_other_mcw_wise_total+=$subcon_other_pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($subcon_white_pro_qty,2,'.',''); $subcon_white_mcw_wise_total+=$subcon_white_pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($subcon_wash_pro_qty,2,'.',''); $subcon_wash_mcw_wise_total+=$subcon_wash_pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($pro_qty,2,'.',''); $subcon_pro_qty_total+=$pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($trims_weight,2,'.',''); $subcon_trims_weight_total+=$trims_weight;?></td>
								<td width="70" valign="middle" align="right"><? $total_production=$row['batch_qnty']+$row['total_trims_weight'];
								echo number_format($total_production,2,'.','');//$subcon_other_pro_qty+$subcon_white_pro_qty+$subcon_wash_pro_qty+$pro_qty+$trims_weight+$incomplete_qty+$sub_other_qty;
								$total_subc_production_qty+=$total_production;?></td>
								<td width="70" valign="middle" align="right" title="BatchID=<? echo $batch_id;?>"><? echo number_format($sub_actual_prod_qty,2,'.',''); $subcon_actual_prod_qty_total+=$sub_actual_prod_qty;?></td>
								<td width="70" valign="middle"  align="right"><? echo number_format($incomplete_qty,2,'.','');//if($row['incomplete_result']==4) echo $incomplete_qty=$row['batch_qnty']; $total_subc_incomplete_qty+=$incomplete_qty; ?></td>
								<? if($row['result']==1) { $resultBgcolor = "green";} else if($row['result']==2) {$resultBgcolor = "yellow";} else{$resultBgcolor = "#FFFFFF";} ?>
								<td width="110" valign="middle" bgcolor="<? echo $resultBgcolor; ?>"><div style="word-wrap:break-word; width:110px;"><? echo $dyeing_result[$row['result']];?></div></td>
								<td width="80" valign="middle"  align="right"><? echo $row['water_flow_meter'];?></td>
								<?
								$total_subc_btch_qty = $row['batch_qnty']+$row['total_trims_weight'];
								$subc_mc_capacity = $row['prod_capacity'];
								$subc_mc_ut = ($subc_mc_ut_qty_arr[$p_date][$machine_id]*100)/$subc_mc_capacity;
								$subc_mc_ut_result = ($subc_mc_ut*$total_subc_btch_qty)/$subc_mc_ut_qty_arr[$p_date][$machine_id];
								?>
								<td width="80" valign="middle"><p><? echo number_format($subc_mc_ut_result,2,'.','');?></p></td>
								<?
								$load_hour = $subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
								$process_start_date=$subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
								$start_time = $load_hour.'.'.$load_minut;
								$load_hour_minut = date('h:i a', strtotime($start_time));
								?>
								<td width="80" valign="middle"><? echo $load_hour_minut;?></td>
								<?
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$sub_process_end_date = $row['production_end_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$unload_hour_minut = date('h:i a', strtotime($end_time));
								?>
								<td width="80" valign="middle"><? echo $unload_hour_minut; ?></td>
								<?
								$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
								$prod_start_date_time=strtotime($start_date_time);
	
								$end_date_time=$sub_process_end_date.'.'.$unload_hour.'.'.$unload_minut;
								$prod_end_date_time=strtotime($end_date_time);
	
								$diff = ($prod_end_date_time - $prod_start_date_time);
								$total = $diff/60;
								$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
								?>
								<td width="80" valign="middle"><? echo $total_time; ?></td>
								<td width="70" valign="middle" align="right">
									<?	$chemical_return_cost=$cost_return_data_arr[$batch_id][5];
									$chemical_issue_cost=$cost_data_arr[$batch_id][5];
									$chemical_cost=$chemical_issue_cost-$chemical_return_cost;
									echo number_format($chemical_cost,2,'.','');
									?></td>
									<td width="70" valign="middle" align="right"><?
									$dyes_return_cost=$cost_return_data_arr[$batch_id][6];
									$dyes_issue_cost=$cost_data_arr[$batch_id][6];
									$dyes_cost=$dyes_issue_cost-$dyes_return_cost;
									echo number_format($dyes_cost,2,'.',''); ?></td>
									<td width="70" valign="middle" align="right"><? echo number_format($chemical_cost+$dyes_cost,2,'.',''); ?></td>
									<td width="150" valign="middle" style="word-wrap:break-word; word-break: break-all;"> <div style="word-wrap:break-word; width:150px;">
										<?
										$subcon_fabr_compo = trim($row['item_description']);
										$subcon_str = implode(',',array_unique(explode(',', $subcon_fabr_compo)));
										echo chop($subcon_str,',');
										?></div></td>
										<?
										$unload_remarks = $row['remarks'];
										$load_remarks = $subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["remarks"];
										if ($unload_remarks=="")
										{
											?><td valign="middle" width=""><p><? echo $load_remarks;?></p></td><?
										}
										else
										{
											?><td valign="middle" width=""><p><? echo $row['remarks'];?></p></td><?
										}
										?>
									</tr>
									<?
							//$i++;
							//$subcon_other_mcw_wise_total+=$subcon_other_pro_qty;
							//$subcon_white_mcw_wise_total+=$subcon_white_pro_qty;
								}
						//if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
						foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
						{
							?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$cause_id];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
									$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut;
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
									$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
									$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];
	
									$before = strtotime($from_date . " " . $start_hour_minut);
									$after = strtotime($to_date . " " . $end_hour_minut);
									$diff = $after - $before;
										//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments
	
										// $diff is in seconds
									$hours = floor($diff / 3600);
									$minutes = floor(($diff - $hours * 3600) / 60);
									$seconds = $diff - $hours * 3600 - $minutes * 60;
									echo $hours.'.'.$minutes.'H';
									?>
								</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<?
						}
						?>
						<tr class="tbl_bottom">
							<td colspan="2" align="right"><b>Batch Per M/C :</b></td>
							<td><strong><? echo $mcw_wise_subcon_functional_batch=count($subcon_functional_batch_arr); ?></strong></td>
							<td width="100"></td>
							<td><strong><? echo number_format($subcon_mc_wise_total_batch,2,'.','');?></strong></td>
							<td colspan="5" align="right"><b>Total:</b></td>
							<td><strong><? echo number_format($subcon_other_mcw_wise_total,2,'.','');?></strong></td>
							<td><strong><? echo number_format($subcon_white_mcw_wise_total,2,'.','');?></strong></td>
								<td><strong><? echo number_format($subcon_wash_mcw_wise_total,2,'.','');?></strong></td>
									<td><strong><? echo number_format($subcon_pro_qty_total,2,'.','');?></strong></td>
										<td><strong><? echo number_format($subcon_trims_weight_total,2,'.','');?></strong></td>
											<td><strong><? echo number_format($total_subc_production_qty,2,'.','');?></strong></td>
												<td><strong><?  echo number_format($subcon_actual_prod_qty_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($total_subc_incomplete_qty,2,'.','');?></strong></td>
														<td colspan="11"></td>
													</tr>
													<?
													$i++;
													$subcon_other_date_wise_total+=$subcon_other_mcw_wise_total;
													$subcon_white_date_wise_total+=$subcon_white_mcw_wise_total;
													$subcon_wash_date_wise_total+=$subcon_wash_mcw_wise_total;
													$subcon_pro_qty_date_wise_total+=$subcon_pro_qty_total;
													$subcon_trims_weight_date_wise_total+=$subcon_trims_weight_total;
													$date_wise_total_subc_production+=$total_subc_production_qty;
													$date_wise_total_subc_actual_production+=$subcon_actual_prod_qty_total;
													$date_wise_total_subc_incomplete_qty+=$total_subc_incomplete_qty;
													$subcon_date_wise_total_batch+=$subcon_mc_wise_total_batch;
													$subcon_date_wise_total_functional_batch+=$mcw_wise_subcon_functional_batch;
												}
												?>
												<tr class="tbl_bottom">
													<td colspan="2" align="right"><b>Date wise Batch:</b></td>
													<td><strong><? echo $subcon_date_wise_total_functional_batch;?></strong></td>
													<td width="100"></td>
													<td><strong><? echo number_format($subcon_date_wise_total_batch,2,'.','');?></strong></td>
													<td colspan="5" align="right"><b>Date wise Total:</b></td>
													<td><strong><? echo number_format($subcon_other_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($subcon_white_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($subcon_wash_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($subcon_pro_qty_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($subcon_trims_weight_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_subc_production,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_subc_actual_production,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_subc_incomplete_qty,2,'.','');?></strong></td>
													<td colspan="11"></td>
												</tr>
												<?
												$subcon_other_grnd_total+=$subcon_other_date_wise_total;
												$subcon_white_grnd_total+=$subcon_white_date_wise_total;
												$subcon_wash_grnd_total+=$subcon_wash_date_wise_total;
												$subcon_pro_qty_grnd_total+=$subcon_pro_qty_date_wise_total;
												$subcon_trims_weight_grnd_total+=$subcon_trims_weight_date_wise_total;
												$subc_production_grnd_total+=$date_wise_total_subc_production;
												$subc_incomplete_qty_grnd_total+=$date_wise_total_subc_incomplete_qty;
												$subcon_grnd_total_batch+=$subcon_date_wise_total_batch;
												$subcon_grnd_total_actual_batch+=$date_wise_total_subc_actual_production;
												$subcon_grnd_total_functional_batch+=$subcon_date_wise_total_functional_batch;
											}
											?>
											<tfoot>
												<tr class="tbl_bottom">
													<td align="right" colspan="2"><strong>Grand Total Batch:</strong></td>
													<td width="80"><strong><? echo $subcon_grnd_total_functional_batch;?></strong>&nbsp;</td>
													<td width="100"></td>
													<td width="100"><strong><? echo number_format($subcon_grnd_total_batch,2,'.','');?></strong>&nbsp;</td>
													<td width="100"></td>
													<td width="100"></td>
													<td width="70"></td>
													<td width="50"></td>
													<td width="100" align="right"><strong>Grand Total :</strong></td>
													<td width="60"><strong><? echo number_format($subcon_other_grnd_total,2,'.','');?></strong>&nbsp;</td>
													<td width="60"><strong><? echo number_format($subcon_white_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($subcon_wash_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($subcon_pro_qty_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($subcon_trims_weight_grnd_total,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($subc_production_grnd_total,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($subcon_grnd_total_actual_batch,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($subc_incomplete_qty_grnd_total,2,'.','');?></strong></td>
													<td colspan="11" width="80"></td>
												</tr>
												<tr class="tbl_bottom">
													<td colspan="2" align="right"><b>Avg Batch Per M/C:</b></td>
													<td width="80"><strong><? echo number_format($total_mc=$subcon_grnd_total_batch/($i-1),2,'.','') ;?></strong>&nbsp;</td>
													<td colspan="26" align="right"></td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<br>
								<?
							}
	
		if ($order_type==0 || $order_type==3) // Sample Without Order
		{
			?>
			<div>
				<table width="2420" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th colspan="29" style="text-align: left;">Sample Without Order</th>
						</tr>
					</thead>
				</table>
				<table width="2420" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th rowspan="2" width="30">M/C No</th>
							<th rowspan="2" width="100">M/C & Capacity</th>
							<th rowspan="2" width="80">Functional Batch No.</th>
							<th rowspan="2" width="100">Buyer Name</th>
							<th rowspan="2" width="100">Batch No.</th>
							<th rowspan="2" width="120">Batch Color</th>
							<th rowspan="2" width="120">Booking No.</th>
							<th rowspan="2" width="70">Batch Qty</th>
							<th rowspan="2" width="50">Ext. No</th>
							<th rowspan="2" width="100">Color Range</th>
							<th colspan="5" width="">Total Production Qty in Kg</th>
							<th rowspan="2" width="70">MC Total Production</th>
							<th rowspan="2" width="70">Actual Production</th>
							<th rowspan="2" width="70">Incomplete Production</th>
							<th rowspan="2" width="110">Result</th>
							<th rowspan="2" width="70">Water/kg in Ltr</th>
							<th rowspan="2" width="70">M/C UT%y</th>
							<th rowspan="2" width="80">Loading Time</th>
							<th rowspan="2" width="80">Unloading Time</th>
							<th rowspan="2" width="80">Total Time (Hour)</th>
							<th rowspan="2" width="70">Chemical Cost</th>
							<th rowspan="2" width="70">Dyes Cost</th>
							<th rowspan="2" width="70">Total Cost</th>
							<th rowspan="2" width="150">Fabric Construction</th>
	
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="60" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
							<th width="60" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
							<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
							<th width="60">Re-Process</th>
							<th width="60">Trims Weight</th>
						</tr>
					</thead>
				</table>
				<div style="width:2420px; overflow-y:scroll; max-height:300px;" id="scroll_body">
					<table align="center" cellspacing="0" width="2400"  border="1" rules="all" class="rpt_table" >
	
						<?
						$i=1;
						$other_grnd_total=$white_grnd_total=$wash_grnd_total=$wo_pro_qty_grnd_total=$wo_trims_weight_grnd_total=$sm_grnd_total_batch=$sm_grnd_total_functional_batch=$grnd_total_samp_production=$grnd_total_samp_incomplete_qty=$actual_wo_pro_qty_grnd_total=0;
						foreach ($date_data_arr as $p_date=>$prod_date_data)
						{
							$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$wo_pro_qty_date_wise_total=$wo_trims_weight_date_wise_total=$sm_date_wise_total_batch=$sm_date_wise_total_functional_batch=$date_wise_total_samp_production=$wo_pro_qty_actual_date_wise_total=$date_wise_total_samp_incomplete_qty=0;
							?>
							<tr>
								<td colspan="29" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
							</tr>
							<?
							$j=0;
							foreach ($prod_date_data as $machine_id => $machine_data)
							{
								$other_mcw_wise_total=$white_mcw_wise_total=$wo_wash_mcw_wise_total=$wo_pro_qty_total=$wo_trims_weight_total=$sm_mcw_wise_total_batch=$total_samp_production=$total_samp_incomplete_qty=$samp_actual_pro_qty_total=0;
								$sample_functional_batch_arr=array();
								$r=0;
								foreach ($machine_data as $batch_id => $row)
								{
							//echo $row['batch_ext_no'];
									$samp_fin_prod=$samp_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
									$samp_fin_prod2=$samp_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
									$samp_re_process=$samp_re_process_qty_arr[$row['batch_no']][$row['result']]['re_process_qty'];
									$samp_re_process3=$samp_re_process_qty_arr3[$row['batch_no']][$row['batch_ext_no']]['re_process_qty'];
	
									$samp_recv_number=$samp_fin_prod_qty_arr[$batch_id]["recv_number"];
									$samp_recv_qty=$samp_fin_prod_qty_arr[$batch_id]["receive_qnty"];
									if($samp_recv_qty>0) $samp_batch_color_td="green";else  $samp_batch_color_td="";
	
	
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									?>
									<tr id="<? echo 'vSamp_'.$i; ?>" onClick="change_color('vSamp_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
										<?
										if ($r==0)
										{
											?>
											<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
											}
											else
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
											}
											?>" width="30" valign="middle"><?echo $i;?></td>
											<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
											}
											else
											{
												$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
											}
											?>" width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $row['machine_no'].'- '.$row['prod_capacity'].'kg';?></div></td>
											<?
											?>
	
											<?
										}
									//$samp_rowspan_arr[$p_date][$machine_id][$row['system_no']]
	
										if($row['batch_ext_no']>0) $samp_extBgcolor = "yellow";else $samp_extBgcolor="";
								//if ($j==0)
								//{
										?>
										<td  width="80" rowspan="<? //echo $samp_rowspan_arr[$p_date][$machine_id][$row['system_no']];?>" valign="middle" style="word-wrap:break-word; word-break: break-all;"><? echo $sample_functional_batch=$row['system_no'];
										$sample_functional_batch_arr[$row['system_no']]=$row['system_no']; ?></td>
										<?
								//}
										?>
										<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $buyer_library[$row['buyer_id']];?></div></td>
	
										<td width="100" valign="middle" style="word-wrap:break-word; word-break: break-all;" title="Recv No=<? echo $samp_recv_number.',Fin Qty='.$samp_recv_qty; ?>">
											<b style=" color:<? echo $samp_batch_color_td;?>"><div style="word-wrap:break-word; width:100px;"><? echo $row['batch_no']; $sm_mcw_wise_total_batch+=count($row['batch_no']);?></b></div></td>
											<td width="120" valign="middle"><div style="word-wrap:break-word; width:120px;"><? echo $color_library[$row['color_id']]?></div></td>
											<td width="120" valign="middle"><div style="word-wrap:break-word; width:120px;"><? echo $row['booking_no'];?></div></td>
											<td width="70" valign="middle" align="right" title="Fabric + Trims" ><? echo number_format($row['batch_qty']+$row['total_trims_weight'],2,'.','');?></td>
											<td width="50" valign="middle" bgcolor="<? echo $samp_extBgcolor;?>" align="center"><? echo $row['batch_ext_no'];?></td>
											<td width="100" valign="middle"><? echo $color_range[$row['color_range_id']];?></td>
											<?
	
											$other_pro_qty=$white_pro_qty=$wo_wash_pro_qty=$samp_actual_prod_qty=$pro_qty=$samp_other_qty=0;
											if($row['batch_ext_no']==1)
											{
											$bext=$row['batch_ext_no'];
											}
											else $bext=$row['batch_ext_no']-1;
											//$bext=$row['batch_ext_no']-1;
											if(($bext*1)<=0) $bext='';
	
											if ($row['result']==1 )
											{
	
												if ($row['color_range_id']==4)
												{
													$white_pro_qty = $row['batch_qty'];
												}
												else if ($row['color_range_id']==7)
												{
													$wo_wash_pro_qty = $row['batch_qty'];
												}
												else $other_pro_qty = $row['batch_qty'];
	
	
											}
											else if ($row['result']==5  && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=='')
											{
												if ($row['color_range_id']==4)
												{
													$white_pro_qty = 0;
												}
												else if ($row['color_range_id']==7)
												{
													$wo_wash_pro_qty = 0;
												}
												else $other_pro_qty =0;
											}
											else if($row['batch_ext_no']>0)
											{
												$white_pro_qty =0;
												$wo_wash_pro_qty = 0;
												$other_pro_qty =0;
									//echo "B";
											}
	
								if ($row['result']==1  && $row['batch_ext_no']=="")  //Shade match/Redyeing Needed/Fin Prod Available
								{
									$samp_actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
								}
								else if ($row['result']==1  && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available
								{
									$samp_actual_prod_qty = 0;
								}
	
								if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
								{
									$samp_actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
								}
	
								if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
								{
									$samp_pro_qty =$row['batch_qty'];// $row['batch_qty'];
									$is_fnished[$row['batch_no']]=1;
								}
								if($is_fnished[$row['batch_no']]==1) $samp_pro_qty =$row['batch_qty'];// $row['batch_qty'];
	
								if($row['incomplete_result']==4)
								{
									$samp_incomplete_qty=$row['batch_qty']+$row['total_trims_weight'];
								}
								else $samp_incomplete_qty=0;
								$total_samp_incomplete_qty+=$samp_incomplete_qty;
								if($samp_actual_prod_qty>0) $trims_weight=$row['total_trims_weight'];else $trims_weight="";
	
								?>
								<td width="60" valign="middle" align="right"><? echo number_format($other_pro_qty,2,'.',''); $other_mcw_wise_total+=$other_pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($white_pro_qty,2,'.',''); $white_mcw_wise_total+=$white_pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($wo_wash_pro_qty,2,'.',''); $wo_wash_mcw_wise_total+=$wo_wash_pro_qty;?></td>
								<td width="60" valign="middle" align="right"><? echo number_format($samp_pro_qty,2,'.',''); $wo_pro_qty_total+=$samp_pro_qty;?></td>
								<td width="60" valign="middle"><? echo number_format($trims_weight,2,'.',''); $wo_trims_weight_total+=$trims_weight;?></td>
								<td width="70" valign="middle" align="right"><? $total_production=$row['batch_qty']+$row['total_trims_weight'];
								echo number_format($total_production,2,'.','');//($other_pro_qty+$white_pro_qty+$wo_wash_pro_qty+$samp_pro_qty+$trims_weight+$samp_incomplete_qty);
								$total_samp_production+=$total_production; ?></td>
								<td width="70" valign="middle" align="right"><? echo number_format($samp_actual_prod_qty,2,'.',''); $samp_actual_pro_qty_total+=$samp_actual_prod_qty;?></td>
								<td width="70" valign="middle"><? echo number_format($incomplete_qty,2,'.','');//if($row['result']==4) echo $incomplete_qty=$row['batch_qty'];
								$total_samp_incomplete_qty+=$incomplete_qty; ?></td>
								<? if($row['result']==1) { $resultBgcolor = "green";} else if($row['result']==2) {$resultBgcolor = "yellow";} else{$resultBgcolor = "#FFFFFF";} ?>
								<td width="110" valign="middle" bgcolor="<? echo $resultBgcolor; ?>"><div style="word-wrap:break-word; width:110px;"><? echo $dyeing_result[$row['result']];?></div></td>
								<td width="70" valign="middle"><? echo $row['water_flow_meter'];?></td>
								<?
								$smp_total_btch_qty = $row['batch_qty']+$row['total_trims_weight'];
								$smp_mc_capacity = $row['prod_capacity'];
								$smp_mc_ut = ($smp_mc_ut_qty_arr[$p_date][$machine_id]*100)/$smp_mc_capacity;
								$smp_mc_ut_result = ($smp_mc_ut*$smp_total_btch_qty)/$smp_mc_ut_qty_arr[$p_date][$machine_id];
								?>
								<td width="70" valign="middle"><? echo number_format($smp_mc_ut_result,2,'.','');?></td>
								<?
								$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
								$process_start_date = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
								$start_time = $load_hour.'.'.$load_minut;
								$load_hour_minut = date('h:i A', strtotime($start_time));
								?>
								<td width="80" valign="middle"><? echo $load_hour_minut;?></td>
								<?
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$process_end_dateIs = $row['production_end_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$unload_hour_minut = date('h:i A', strtotime($end_time));
								?>
								<td width="80" valign="middle"><? echo $unload_hour_minut; ?></td>
								<?
								$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
								$prod_start_date_time=strtotime($start_date_time);
	
								$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
								$prod_end_date_time=strtotime($end_date_time);
	
								$diff = ($prod_end_date_time - $prod_start_date_time);
								$total = $diff/60;
								$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
								?>
								<td width="80" valign="middle"><? echo $total_time; ?></td>
								<td width="70" valign="middle" align="right">
									<?	$chemical_return_cost=$cost_return_data_arr[$batch_id][5];
									$chemical_issue_cost=$cost_data_arr[$batch_id][5];
									$chemical_cost=$chemical_issue_cost-$chemical_return_cost;
									echo number_format($chemical_cost,2,'.','');
									?></td>
									<td width="70" valign="middle" align="right"><?
									$dyes_return_cost=$cost_return_data_arr[$batch_id][6];
									$dyes_issue_cost=$cost_data_arr[$batch_id][6];
									$dyes_cost=$dyes_issue_cost-$dyes_return_cost;
									echo number_format($dyes_cost,2,'.',''); ?></td>
									<td width="70" valign="middle" align="right"><? echo number_format($chemical_cost+$dyes_cost,2,'.',''); ?></td>
									<td width="150" valign="middle" style="">
										<div style="word-wrap:break-word; width:150px;">
											<?
											$samp_fabr_compo = trim($row['const_composition']);
											$samp_str = implode(',',array_unique(explode(',', $samp_fabr_compo)));
											echo chop($samp_str,',');
											?> </div></td>
	
											<?
											$unload_remarks = $row['remarks'];
											$load_remarks = $load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
											if ($unload_remarks=="")
											{
												?><td valign="middle"><p><? echo $load_remarks;?></p></td><?
											}
											else
											{
												?><td valign="middle"><p><? echo $row['remarks'];?></p></td><?
											}
											?>
										</tr>
										<?
										$r++;	$j++;
							//$i++;
							//$other_mcw_wise_total+=$other_pro_qty;
							//$white_mcw_wise_total+=$white_pro_qty;
	
									}
						//if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
						foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
						{
							?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$cause_id];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
									$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut;
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
									$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
									$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];
	
									$before = strtotime($from_date . " " . $start_hour_minut);
									$after = strtotime($to_date . " " . $end_hour_minut);
									$diff = $after - $before;
										//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments
	
										// $diff is in seconds
									$hours = floor($diff / 3600);
									$minutes = floor(($diff - $hours * 3600) / 60);
									$seconds = $diff - $hours * 3600 - $minutes * 60;
									echo $hours.'.'.$minutes.'H';
									?>
								</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<?
						}
						?>
						<tr class="tbl_bottom">
							<td colspan="2" align="right"><b>Batch Per M/C :</b></td>
							<td><strong><? echo $mcw_wise_sample_functional_batch=count($sample_functional_batch_arr); ?></strong></td>
							<td></td>
							<td><strong><? echo number_format($sm_mcw_wise_total_batch,2,'.','');?></strong></td>
							<td colspan="5" align="right"><b>Total:</b></td>
							<td><strong><? echo number_format($other_mcw_wise_total,2,'.','');?></strong></td>
							<td><strong><? echo number_format($white_mcw_wise_total,2,'.','');?></strong></td>
								<td><strong><? echo number_format($wo_wash_mcw_wise_total,2,'.','');?></strong></td>
									<td><strong><? echo number_format($wo_pro_qty_total,2,'.','');?></strong></td>
										<td><strong><? echo number_format($wo_trims_weight_total,2,'.','');?></strong></td>
											<td><strong><? echo number_format($total_samp_production,2,'.','');?></strong></td>
												<td><strong><? echo number_format($samp_actual_pro_qty_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($total_samp_incomplete_qty,2,'.','');?></strong></td>
														<td colspan="11"></td>
	
													</tr>
													<?
													$i++;
													$other_date_wise_total+=$other_mcw_wise_total;
													$white_date_wise_total+=$white_mcw_wise_total;
													$wash_date_wise_total+=$wo_wash_mcw_wise_total;
													$wo_pro_qty_date_wise_total+=$wo_pro_qty_total;
	
													$wo_pro_qty_actual_date_wise_total+=$samp_actual_pro_qty_total;
	
													$wo_trims_weight_date_wise_total+=$wo_trims_weight_total;
													$sm_date_wise_total_batch+=$sm_mcw_wise_total_batch;
													$date_wise_total_samp_production+=$total_samp_production;
													$date_wise_total_samp_incomplete_qty+=$total_samp_incomplete_qty;
													$sm_date_wise_total_functional_batch+=$mcw_wise_sample_functional_batch;
												}
												?>
												<tr class="tbl_bottom">
													<td colspan="2" align="right"><b>Date wise Batch:</b></td>
													<td><strong><? echo $sm_date_wise_total_functional_batch;?></strong></td>
													<td></td>
													<td><strong><? echo number_format($sm_date_wise_total_batch,2,'.','');?></strong></td>
													<td colspan="5" align="right"><b>Date wise Total:</b></td>
													<td><strong><? echo number_format($other_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($white_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($wash_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($wo_pro_qty_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($wo_trims_weight_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_samp_production,2,'.','');?></strong></td>
													<td><strong><?  echo number_format($wo_pro_qty_actual_date_wise_total,2,'.','');?></strong></td>
													<td><strong><? echo number_format($date_wise_total_samp_incomplete_qty,2,'.','');?></strong></td>
													<td colspan="11"></td>
												</tr>
												<?
												$other_grnd_total+=$other_date_wise_total;
												$white_grnd_total+=$white_date_wise_total;
												$wash_grnd_total+=$wash_date_wise_total;
												$wo_pro_qty_grnd_total+=$wo_pro_qty_date_wise_total;
												$actual_wo_pro_qty_grnd_total+=$wo_pro_qty_actual_date_wise_total;
												$wo_trims_weight_grnd_total+=$wo_trims_weight_date_wise_total;
												$sm_grnd_total_batch+=$sm_date_wise_total_batch;
												$grnd_total_samp_production+=$date_wise_total_samp_production;
												$grnd_total_samp_incomplete_qty+=$date_wise_total_samp_incomplete_qty;
												$sm_grnd_total_functional_batch+=$sm_date_wise_total_functional_batch;
											}
											?>
											<tfoot>
												<tr class="tbl_bottom">
													<td width="80" align="right" colspan="2"><strong>Grand Total Batch:</strong></td>
													<td width="80"><strong><? echo $sm_grnd_total_functional_batch;?></strong>&nbsp;</td>
													<td width="100"></td>
													<td width="100"><strong><? echo number_format($sm_grnd_total_batch,2,'.','');?></strong>&nbsp;</td>
													<td width="120"></td>
													<td width="120"></td>
													<td width="70"></td>
													<td width="50"></td>
													<td width="100" align="right"><strong>Grand Total :</strong></td>
													<td width="60"><strong><? echo number_format($other_grnd_total,2,'.','');?></strong>&nbsp;</td>
													<td width="60"><strong><? echo number_format($white_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($wash_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($wo_pro_qty_grnd_total,2,'.','');?></strong></td>
													<td width="60"><strong><? echo number_format($wo_trims_weight_grnd_total,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($grnd_total_samp_production,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($actual_wo_pro_qty_grnd_total,2,'.','');?></strong></td>
													<td width="70"><strong><? echo number_format($grnd_total_samp_incomplete_qty,2,'.','');?></strong></td>
													<td colspan="11"></td>
												</tr>
												<tr class="tbl_bottom">
													<td colspan="2" align="right"><b>Avg Batch Per M/C:</b></td>
													<td width="80"><strong><? echo number_format($total_mc=$sm_grnd_total_batch/($i-1),2,'.','') ;?></strong>&nbsp;</td>
													<td colspan="26" align="right"> </td>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
								<br>
								<?
							}
					$user_id = $_SESSION['logic_erp']["user_id"];
					foreach (glob("$user_id*.xls") as $filename)
					{
						if( @filemtime($filename) < (time()-$seconds_old) )
							@unlink($filename);
					}
		//---------end------------//
					$name=time();
					$filename=$user_id."_".$name.".xls";
					$create_new_doc = fopen($filename, 'w');
					$is_created = fwrite($create_new_doc,ob_get_contents());
					$filename=$user_id."_".$name.".xls";
					echo "$total_data****$filename";
					exit();
						 
						 
		 
}
		
if($action=="report_generate_not_used") // Show Button
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company=str_replace("'","",$cbo_company_id);
	$machine_name=str_replace("'","",$txt_machine_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$order_type=str_replace("'","",$cbo_order_type);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$booking_no=str_replace("'","",$txt_booking_no);
	$order_no=str_replace("'","",$txt_order_no);
	$batch=str_replace("'","",$txt_batch);
	$batch_color=str_replace("'","",$hidden_color_id);
	$color_range=str_replace("'","",$txt_color_range);
	$floor_name=str_replace("'","",$cbo_floor_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);


	if ($batch=="") $batch_cond =""; else $batch_cond =" and a.batch_no='$batch' ";
	if ($order_type==0) $order_type_cond =""; else $order_type_cond =" and a.order_type=$order_type ";
	if ($cbo_location==0 || $cbo_location=='') $location_id =""; else $location_id =" and c.location_id=$cbo_location ";
	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and c.floor_id=$cbo_floor_id";
	if ($machine_name=="") $machine_cond=""; else $machine_cond =" and c.id in ($machine_name) ";
	if ($buyer_name==0  || $buyer_name=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$buyer_name ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and q.booking_no=$txt_booking_no ";
	if ($batch_color=="") $batch_color_cond =""; else $batch_color_cond =" and q.color_id=$batch_color ";
	if ($color_range==0 || $color_range=="") $color_range_cond =""; else $color_range_cond =" and q.color_range_id=$color_range ";
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="")
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$machineArr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	ob_start();

	?>
	<div>
		<table width="1760" cellpadding="0" cellspacing="0" id="caption" align="center">
			<tr>
				<td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
			</tr>
		</table>
		<?
		if($db_type==0)
		{
			if( $date_from!="" && $date_to!="" )
			{
				$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
		}
		else if($db_type==2)
		{
			if($date_from!="" && $date_to!="")
			{
				$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			}
		}

		 $mc_idle = "SELECT a.machine_entry_tbl_id, a.machine_no, a.from_date, a.from_hour, a.from_minute, a.to_date, a.to_hour, a.to_minute, a.machine_idle_cause, a.remarks, b.batch_id
		from pro_cause_of_machine_idle a, pro_fab_subprocess b
		where a.machine_entry_tbl_id=b.machine_id and a.status_active=1 and a.is_deleted=0 $idle_sql_cond";

		$mc_idle_result=sql_select($mc_idle);
		$idle_data_arr=array();
		foreach ($mc_idle_result as $rows)
		{
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine"]=$rows[csf("machine_entry_tbl_id")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_no"]=$rows[csf("machine_no")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_date"]=$rows[csf("from_date")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_hour"]=$rows[csf("from_hour")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_minute"]=$rows[csf("from_minute")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_date"]=$rows[csf("to_date")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_hour"]=$rows[csf("to_hour")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_minute"]=$rows[csf("to_minute")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_idle_cause"]=$rows[csf("machine_idle_cause")];
			$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["remarks"]=$rows[csf("remarks")];
			
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine"]=$rows[csf("machine_entry_tbl_id")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine_no"]=$rows[csf("machine_no")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_date"]=$rows[csf("from_date")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_hour"]=$rows[csf("from_hour")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_minute"]=$rows[csf("from_minute")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_date"]=$rows[csf("to_date")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_hour"]=$rows[csf("to_hour")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_minute"]=$rows[csf("to_minute")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine_idle_cause"]=$rows[csf("machine_idle_cause")];
			$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["remarks"]=$rows[csf("remarks")];
			
			 
		}
		//print_r($idle_data_arr2);
		// Self Order
		if ($order_type==0 || $order_type==1)
		{
			?>
			<div>
				<table width="518" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th colspan="5">Self Order</th>
						</tr>
					</thead>
				</table>
				<table width="1900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>
						<tr>
							<th rowspan="2" width="30">SL No</th>
							<th rowspan="2" width="80">M/C & Capacity</th>
							<th rowspan="2" width="80">Buyer Name</th>
							<th rowspan="2" width="80">Batch No.</th>
							<th rowspan="2" width="80">Batch Color</th>
							<th rowspan="2" width="80">Booking No.</th>
							<th rowspan="2" width="80">Color Range</th>
							<th colspan="7" width="80">Total Production Qty in Kg</th>
							<th rowspan="2" width="80">Water/kg in Ltr</th>
							<th rowspan="2" width="80">M/C UT%y</th>
							<th rowspan="2" width="80">Loading Time</th>
							<th rowspan="2" width="80">Unloading Time</th>
							<th rowspan="2" width="80">Total Time (Hour)</th>
							<th rowspan="2" width="120">Fabric Construction</th>
							<th rowspan="2" width="80">Result</th>
							<th rowspan="2">Remarks</th>
						</tr>
						<tr>
							<th width="80" title="Data is showing, according to [Color Range] - Average Color, from batch creation page.">Color</th>
							<th width="80" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
							<th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
                            <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Normal Wash</th>
                            <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">AOP Wash</th>
							<th width="80">Re-Process</th>
							<th width="80">Trims Weight</th>
						</tr>
					</thead>
				</table>
				<div style="width:1920px; overflow-y:scroll; max-height:300px;" id="scroll_body">
					<table align="center" cellspacing="0" width="1900"  border="1" rules="all" class="rpt_table" >

						<?
					$sqls="SELECT  a.batch_id, a.process_id, a.load_unload_id,a.entry_form,a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks,b.production_qty
						from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
						where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
						and a.id=b.mst_id and a.machine_id=c.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";//group by  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks 

						$load_time_array=array();
						foreach(sql_select($sqls) as $vals)
						{
							if($vals[csf("load_unload_id")]==1 && $vals[csf("entry_form")]==35)
							{
							$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
							$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
							$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("process_end_date")];
							$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
							}
							else if($vals[csf("entry_form")]==424)
							{
								$process_id=$vals[csf("process_id")];
								if($process_id==209)
								{
								$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];
								}
								else if($process_id==193)
								{
								$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
								}
								$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
							}
						}
						//print_r($wash_qty_array);

		  				// Main query
						$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date,
						a.water_flow_meter, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity,
						p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
						from wo_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
						where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
						and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2
						and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order!=1
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
						order by a.production_date, c.machine_no";
						//echo $sql_result;die;

						$sql_dtls=sql_select($sql_result);
						$date_data_arr=array();
						foreach ($sql_dtls as $row)
						{
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('production_date')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition']=$row[csf('const_composition')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
							$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];
						}

						$color_range=array(1=>"Dark Color",2=>"Light Color",3=>"Black Color",4=>"White Color",5=>"Average Color",6=>"Melange",7=>"Wash",8=>"Scouring",9=>"Extra Dark",10=>"Medium Color",11=>"Super Dark");


						$rowspan_arr=array();
						foreach ($date_data_arr as $p_date=>$prod_date_data)
						{
							foreach ($prod_date_data as $machine_id => $machine_data)
							{
								foreach ($machine_data as $batch_id => $row)
								{
									if (isset($rowspan_arr[$p_date][$machine_id]))
									{
										$rowspan_arr[$p_date][$machine_id]++;
									}
									else
									{
										$rowspan_arr[$p_date][$machine_id]=1;
									}
								}
							}
						}

						$i=1;
						$other_grnd_total=$white_grnd_total=$wash_grnd_total=$normal_wash_grnd_total=$aop_wash_grnd_total=$pro_qty_grnd_total=0;
						foreach ($date_data_arr as $p_date=>$prod_date_data)
						{
							$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$normal_date_wise_total=$aop_date_wise_total=$pro_qty_date_wise_total=0;
							?>
							<tr>
								<td colspan="24" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
							</tr>
							<?
							foreach ($prod_date_data as $machine_id => $machine_data)
							{
								$other_mcw_wise_total=$white_mcw_wise_total=$wash_mcw_wise_total=$total_pro_qty=$normal_wash_qty_total=$aop_wash_qty_total=0;
								$r=0;
								foreach ($machine_data as $batch_id => $row)
								{
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									?>
									<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
										<?
										if ($r==0)
										{
											?>
											<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
											{
												$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+1+$m_cause_row;
											}
											else
											{
												$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+$m_cause_row;
											}
											?>" width="30" valign="middle"><? echo $i;?></td>
											<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
											{
												$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+1+$m_cause_row;
											}
											else
											{
												$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
												echo $rowspan_arr[$p_date][$machine_id]+$m_cause_row;
											}
											?>" width="80" valign="middle"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
											<?
										} $r++;
										?>
										<td width="80"><? echo $buyer_library[$row['buyer_id']];?></td>
										<td width="80" title="<? echo $batch_id;?>"><? echo $row['batch_no'];?></td>
										<td width="80"><p><? echo $color_library[$row['color_id']]?></p></td>
										<td width="80"><? echo $row['booking_no'];?></td>
										<td width="80"><? echo $color_range[$row['color_range_id']];?></td>
										<?
										
										// echo $normal_wash_qty.'='.$aop_wash_qty;
										 $wash_entry_form=0;
										 $wash_entry_form=$wash_qty_array[$machine_id][$batch_id][$row['batch_ext_no']]["wash_entry_form"];
										$avarage_batch_qty=$white_batch_qty=$wash_batch_qty=$pro_qty=0;
										if($wash_entry_form!=424)
										{
											 $aop_wash_qty=$normal_wash_qty=0;
											if ($row['color_range_id']!=4 && $row['color_range_id']!=7)
											{
												$avarage_batch_qty = $row['batch_qty'];
											}
											else if ($row['color_range_id']==4 && $row['result']==1)
											{
												$white_batch_qty = $row['batch_qty'];
											}
											else if ($row['color_range_id']==7 && $row['result']==1)
											{
												$wash_batch_qty = $row['batch_qty'];
											}
										}
										else {
											
											 $normal_wash_qty=$wash_qty_array[$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_normal"];
										 	$aop_wash_qty=$wash_qty_array[$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_aop"];
											$avarage_batch_qty=$white_batch_qty=$wash_batch_qty=0;
										}
										?>
										<td width="80" align="right"><? echo $avarage_batch_qty; $other_mcw_wise_total+=$avarage_batch_qty;?></td>
										<?
										
										?>
										<td width="80" align="right"><? echo $white_batch_qty; $white_mcw_wise_total+=$white_batch_qty;?></td>
										<?
										
										?>
										<td width="80" align="right"><? echo $wash_batch_qty; $wash_mcw_wise_total+=$wash_batch_qty;?></td>
                                        <td width="80" align="right" title="Wash Aop=<? echo $wash_entry_form;?>"><? echo $normal_wash_qty; $normal_wash_qty_total+=$normal_wash_qty;?></td>
                                        <td width="80" align="right"><? echo $aop_wash_qty; $aop_wash_qty_total+=$aop_wash_qty;?></td>
										<?
										if ($row['result']==2)
										{
											$pro_qty = $row['batch_qty'];
										}
										?>
										<td width="80" align="right"><? echo $pro_qty; $total_pro_qty+=$pro_qty;?></td>
										<td width="80" align="right"><? echo $row['total_trims_weight'];?></td>
										<td width="80"><? echo $row['water_flow_meter'];?></td>
										<?
										$pro_qty = $row['batch_qty'];
										$mc_capacity = $row['prod_capacity'];
										$mc_ut = ($pro_qty/$mc_capacity)*100;
										?>
										<td width="80"><? echo number_format($mc_ut,2,'.','');?></td>
										<?
										$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
										$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
										$process_start_date = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
										$start_time = $load_hour.'.'.$load_minut;
										$load_hour_minut = date('h:i A', strtotime($start_time));
										?>
										<td width="80"><?echo $load_hour_minut;?></td>
										<?
										$unload_hour = $row['unload_hours'];
										$unload_minut = $row['unload_minutes'];
										$process_end_dateIs = $row['p_date'];
										$end_time = $unload_hour.'.'.$unload_minut;
										$unload_hour_minut = date('h:i A', strtotime($end_time));
										?>
										<td width="80"><?echo $unload_hour_minut; ?></td>
										<?
										$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
										$prod_start_date_time=strtotime($start_date_time);

										$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
										$prod_end_date_time=strtotime($end_date_time);

										$diff = ($prod_end_date_time - $prod_start_date_time);
										$total = $diff/60;
										$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
										?>
										<td width="80"><? echo $total_time; ?></td>
										<td width="120"><?echo $row['const_composition'];?></td>
										<td width="80"><?echo $dyeing_result[$row['result']];?></td>
										<?
										$unload_remarks = $row['remarks'];
										$load_remarks = $load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
										if ($unload_remarks=="")
										{
											?><td><?echo $load_remarks;?></td><?
										}
										else
										{
											?><td><?echo $row['remarks'];?></td><?
										}
										?>
									</tr>
									<?

								}
								//echo $p_date.'='.$machine_id;
								 // Cause of Machine Idle
								//if (isset($idle_data_arr[$p_date][$machine_id]['machine']))
								foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
								{
									// print_r($mc_data);
									//echo $cause_id.",";
									?>
									<tr style="background: #F0BC8F;">
										<td colspan="5">
											<?
											echo $cause_type[$cause_id];
											?>
										</td>
										<td colspan="5">
											<?
											echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
											?>
										</td>
										<td></td>
										<td></td>
                                        <td></td>
										<td></td>
										<td>
											<?
											$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
											$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
											$hour_minut = $idle_start_hour.':'.$idle_start_minut;
											$start_hour_minut = date('h:i A', strtotime($hour_minut));
											echo $start_hour_minut;
											?>
										</td>
										<td>
											<?
											$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
											$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
											$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
											$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
											echo $end_hour_minut;
											?>
										</td>
										<td>
											<?
											$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
											$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];

											$before = strtotime($from_date . " " . $start_hour_minut);
											$after = strtotime($to_date . " " . $end_hour_minut);
											$diff = $after - $before;

											// $diff is in seconds
											$hours = floor($diff / 3600);
											$minutes = floor(($diff - $hours * 3600) / 60);
											$seconds = $diff - $hours * 3600 - $minutes * 60;
											echo $hours.'.'.$minutes.'H';
											?>
										</td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
									<?
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="7" align="right"><b>Total:</b></td>
									<td><strong><? echo $other_mcw_wise_total;?></strong></td>
									<td><strong><? echo $white_mcw_wise_total;?></strong></td>
										<td><strong><? echo $wash_mcw_wise_total;?></strong></td>
                                        <td><strong><? echo $normal_wash_qty_total;?></strong></td>
                                        <td><strong><? echo $aop_wash_qty_total;?></strong></td>
											<td><strong><? echo $total_pro_qty;?></strong></td>
												<td colspan="9"></td>
											</tr>
											<?
											$i++;
											$other_date_wise_total+=$other_mcw_wise_total;
											$white_date_wise_total+=$white_mcw_wise_total;
											$wash_date_wise_total+=$wash_mcw_wise_total;
											
											$normal_date_wise_total+=$normal_wash_qty_total;
											$aop_date_wise_total+=$aop_wash_qty_total;
											
											$pro_qty_date_wise_total+=$total_pro_qty;
										}
										?>
										<tr class="tbl_bottom">
											<td colspan="7" align="right"><b>Date wise Total:</b></td>
											<td><strong><? echo $other_date_wise_total;?></strong></td>
											<td><strong><? echo $white_date_wise_total;?></strong></td>
											<td><strong><? echo $wash_date_wise_total;?></strong></td>
                                            <td><strong><? echo $normal_date_wise_total;?></strong></td>
                                            <td><strong><? echo $aop_date_wise_total;?></strong></td>
											<td><strong><? echo $pro_qty_date_wise_total;?></strong></td>
											<td colspan="9"></td>
										</tr>
										<?
										$other_grnd_total+=$other_date_wise_total;
										$white_grnd_total+=$white_date_wise_total;
										$wash_grnd_total+=$wash_date_wise_total;
										
										$normal_wash_grnd_total+=$normal_date_wise_total;
										$aop_grnd_total+=$aop_date_wise_total;
										
										$pro_qty_grnd_total+=$pro_qty_date_wise_total;
									}
									?>
									<tfoot>
										<tr class="tbl_bottom">
											<td width="30"></td>
											<td width="80"></td>
											<td width="80"></td>
											<td width="80"></td>
											<td width="80"></td>
											<td width="80"></td>
											<td width="80" align="right"><strong>Grand Total :</strong></td>
											<td width="80"><strong><? echo $other_grnd_total;?></strong>&nbsp;</td>
											<td width="80"><strong><? echo $white_grnd_total;?></strong></td>
											<td width="80"><strong><? echo $wash_grnd_total;?></strong></td>
                                            <td width="80"><strong><? echo $normal_wash_grnd_total;?></strong></td>
                                            <td width="80"><strong><? echo $aop_grnd_total;?></strong></td>
											<td width="80"><strong><? echo $pro_qty_grnd_total;?></strong></td>
											<td colspan="9"></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
						<br>
						<?
	}
					// Subcontract Order
	if ($order_type==0 || $order_type==2)
	{
						?>
						<div>
							<table width="518" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
								<thead>
									<tr>
										<th colspan="5">Subcontract Order</th>
									</tr>
								</thead>
							</table>
							<table width="1900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
								<thead>
									<tr>
										<th rowspan="2" width="30">SL No</th>
										<th rowspan="2" width="80">M/C & Capacity</th>
										<th rowspan="2" width="80">Party Name</th>
										<th rowspan="2" width="80">Batch No.</th>
										<th rowspan="2" width="80">Batch Color</th>
										<th rowspan="2" width="80">Order No.</th>
										<th rowspan="2" width="80">Color Range</th>
										
                                        <th colspan="7" width="80">Total Production Qty in Kg</th>
                                        
										<th rowspan="2" width="80">Water/kg in Ltr</th>
										<th rowspan="2" width="80">M/C UT%y</th>
										<th rowspan="2" width="80">Loading Time</th>
										<th rowspan="2" width="80">Unloading Time</th>
										<th rowspan="2" width="80">Total Time (Hour)</th>
										<th rowspan="2" width="120">Fabric Construction</th>
										<th rowspan="2" width="80">Result</th>
										<th rowspan="2">Remarks</th>
									</tr>
									<tr>
										<th width="80" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
										<th width="80" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
										<th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
                                        <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Normal Wash</th>
                                        <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">AOP Wash</th>
										<th width="80">Re-Process</th>
										<th width="80">Trims Weight</th>
									</tr>
								</thead>
							</table>
							<div style="width:1920px; overflow-y:scroll; max-height:300px;" id="scroll_body">
								<table align="center" cellspacing="0" width="1900"  border="1" rules="all" class="rpt_table" >

									<?
									$sqls=" SELECT a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks
									from pro_fab_subprocess a, lib_machine_name c
									where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
									and a.machine_id=c.id and a.entry_form=38 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1
									group by a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks";

									$load_time_array=array();
									foreach(sql_select($sqls) as $vals)
									{
										$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
										$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
										$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("process_end_date")];
										$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
										
										/*if($vals[csf("entry_form")]==424)
										{
											$process_id=$vals[csf("process_id")];
											if($process_id==209)
											{
											$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];
											}
											else if($process_id==193)
											{
											$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
											}
											$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
										}*/
									}
									
									/*$sql_wash=" SELECT a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks,b.production_qty
									from pro_fab_subprocess a,pro_fab_subprocess_dtls b, lib_machine_name c
									where a.id=b.mst_id and a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
									and a.machine_id=c.id and a.entry_form=424 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

									$load_time_array=array();
									foreach(sql_select($sql_wash) as $vals)
									{
									$process_id=$vals[csf("process_id")];
									if($process_id==209)
									{
									$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];
									}
									else if($process_id==193)
									{
									$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
									}
									$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
										
									}*/
									$sub_sql_wash=" SELECT a.batch_id, a.batch_ext_no,a.process_id, a.machine_id,a.end_hours,a.entry_form, a.end_minutes, a.process_end_date, a.remarks,b.production_qty
									from pro_fab_subprocess a,pro_fab_subprocess_dtls b, lib_machine_name c
									where a.id=b.mst_id and a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
									and a.machine_id=c.id and a.entry_form=424 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

									$load_time_array=array();
									foreach(sql_select($sub_sql_wash) as $vals)
									{
									$process_id=$vals[csf("process_id")];
									if($process_id==209)
									{
									$sub_wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];
									}
									else if($process_id==193)
									{
									$sub_wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
									}
									$sub_wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
										
									}


									if ($buyer_name==0  || $buyer_name=='') $party_name_cond =""; else $party_name_cond =" and f.party_id=$buyer_name ";
									if ($order_no=="") $order_no_cond =""; else $order_no_cond =" and p.order_no=$txt_order_no ";

		  							// Main query
									$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date, a.water_flow_meter, b.batch_qnty, b.item_description, c.machine_no, c.prod_capacity, f.party_id, p.order_no, q.color_id, q.color_range_id, q.total_trims_weight, q.process_id
									from subcon_ord_dtls p, subcon_ord_mst f, pro_batch_create_mst q, pro_fab_subprocess a, pro_batch_create_dtls b, lib_machine_name c
									where a.service_company=$cbo_company_id $order_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $party_name_cond $batch_color_cond $color_range_cond and a.machine_id=c.id and a.entry_form=38 and q.entry_form=36 and a.load_unload_id=2 and a.batch_id=q.id and b.po_id=p.id  and p.job_no_mst= f.subcon_job and q.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.production_date, c.machine_no";
									//echo $sql_result;die;

									$sql_dtls=sql_select($sql_result);
									$date_data_arr=array();
									foreach ($sql_dtls as $row)
									{
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('production_date')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['item_description']=$row[csf('item_description')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['party_id']=$row[csf('party_id')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['order_no']=$row[csf('order_no')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
										$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];
									}

									$color_range=array(1=>"Dark Color",2=>"Light Color",3=>"Black Color",4=>"White Color",5=>"Average Color",6=>"Melange",7=>"Wash",8=>"Scouring",9=>"Extra Dark",10=>"Medium Color",11=>"Super Dark");


									$rowspan_arr=array();
									foreach ($date_data_arr as $p_date=>$prod_date_data)
									{
										foreach ($prod_date_data as $machine_id => $machine_data)
										{
											foreach ($machine_data as $batch_id => $row)
											{
												if (isset($rowspan_arr[$p_date][$machine_id]))
												{
													$rowspan_arr[$p_date][$machine_id]+=1;
												}
												else
												{
													$rowspan_arr[$p_date][$machine_id]=1;
												}
											}
										}
									}

									$i=1;
									$subcon_other_grnd_total=$subcon_white_grnd_total=$subcon_wash_grnd_total=$subcon_normal_wash_grnd_total=$subcon_aop_wash_grnd_total=$subcon_pro_qty_grnd_total=0;
									foreach ($date_data_arr as $p_date=>$prod_date_data)
									{
										$subcon_other_date_wise_total=$subcon_white_date_wise_total=$subcon_wash_date_wise_total=$subcon_normal_wash_date_wise_total=$subcon_aop_wash_date_wise_total=$subcon_pro_qty_date_wise_total=0;
										?>
										<tr>
											<td colspan="22" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
										</tr>
										<?
										foreach ($prod_date_data as $machine_id => $machine_data)
										{
											$subcon_other_mcw_wise_total=$subcon_white_mcw_wise_total=$subcon_wash_mcw_wise_total=$subcon_normal_wash_mcw_wise_total=$subcon_normal_wash_mcw_wise_total=$subcon_pro_qty_total=0;
											$r=0;
											foreach ($machine_data as $batch_id => $row)
											{
												if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
												?>
												<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
													<?
													if ($r==0)
													{
														?>
														<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
														{
															$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
															echo $rowspan_arr[$p_date][$machine_id]+1+$m_cause_row;
														}
														else
														{
															$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
															echo $rowspan_arr[$p_date][$machine_id]+$m_cause_row;
														}
														?>" width="30" valign="middle"><?echo $i;?></td>
														<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
														{	$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
															echo $rowspan_arr[$p_date][$machine_id]+1+$m_cause_row;
														}
														else
														{
															$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
															echo $rowspan_arr[$p_date][$machine_id]+$m_cause_row;
														}
														?>" width="80" valign="middle"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
														<?
													} $r++;
													?>
													<td width="80"><?echo $buyer_library[$row['party_id']];?></td>
													<td width="80"><?echo $row['batch_no'];?></td>
													<td width="80"><p><?echo $color_library[$row['color_id']]?></p></td>
													<td width="80"><?echo $row['order_no'];?></td>
													<td width="80"><?echo $color_range[$row['color_range_id']];?></td>
													<?
													
													$wash_entry_form=$sub_wash_qty_array[$machine_id][$batch_id][$row[("batch_ext_no")]]["wash_entry_form"];
													$subcon_other_pro_qty=$subcon_white_pro_qty=$subcon_wash_pro_qty=$pro_qty=0;
													if($wash_entry_form!=424)
													{
														$sub_prod_qty_aop=$sub_prod_qty_normal=0;
														if ($row['color_range_id']==5 && $row['result']==1)
														{
															$subcon_other_pro_qty = $row['batch_qnty'];
														}
														elseif ($row['color_range_id']==4 && $row['result']==1)
														{
															$subcon_white_pro_qty = $row['batch_qnty'];
														}
														else if ($row['color_range_id']==7 && $row['result']==1)
														{
															$subcon_wash_pro_qty = $row['batch_qnty'];
														}
													}
													else
													{
														$sub_prod_qty_aop=$sub_wash_qty_array[$machine_id][$batch_id][$row[("batch_ext_no")]]["prod_qty_aop"];
														$sub_prod_qty_normal=$sub_wash_qty_array[$machine_id][$batch_id][$row[("batch_ext_no")]]["prod_qty_normal"];
														$subcon_other_pro_qty=$subcon_white_pro_qty=$subcon_wash_pro_qty=0;
													}
													?>
													<td width="80" align="right"><? echo $subcon_other_pro_qty; $subcon_other_mcw_wise_total+=$subcon_other_pro_qty;?></td>

													<?
													
													?>
													<td width="80" align="right"><? echo $subcon_white_pro_qty; $subcon_white_mcw_wise_total+=$subcon_white_pro_qty;?></td>
													<?
													
													?>
													<td width="80" align="right"><? echo $subcon_wash_pro_qty; $subcon_wash_mcw_wise_total+=$subcon_wash_pro_qty;?></td>
                                                    <td width="80" align="right"><? echo $sub_prod_qty_normal; $subcon_normal_wash_mcw_wise_total+=$sub_prod_qty_normal;?></td>
                                                     <td width="80" align="right"><? echo $sub_prod_qty_aop; $subcon_aop_wash_mcw_wise_total+=$sub_prod_qty_aop;?></td>
													<?
													if ($row['result']==2)
													{
														$pro_qty = $row['batch_qnty'];
													}
													?>
													<td width="80" align="right"><? echo $pro_qty; $subcon_pro_qty_total+=$pro_qty;?></td>
													<td width="80" align="right"><? echo $row['total_trims_weight'];?></td>
													<td width="80"><? echo $row['water_flow_meter'];?></td>
													<?
													$pro_qty = $row['batch_qnty'];
													$mc_capacity = $row['prod_capacity'];
													$mc_ut = ($pro_qty/$mc_capacity)*100;
													?>
													<td width="80"><? echo number_format($mc_ut,2,'.','');?></td>
													<?
													$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
													$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
													$process_start_date = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
													$start_time = $load_hour.'.'.$load_minut;
													$load_hour_minut = date('h:i a', strtotime($start_time));
													?>
													<td width="80"><?echo $load_hour_minut;?></td>
													<?
													$unload_hour = $row['unload_hours'];
													$unload_minut = $row['unload_minutes'];
													$sub_process_end_date = $row['p_date'];
													$end_time = $unload_hour.'.'.$unload_minut;
													$unload_hour_minut = date('h:i a', strtotime($end_time));
													?>
													<td width="80"><?echo $unload_hour_minut; ?></td>
													<?
													$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
													$prod_start_date_time=strtotime($start_date_time);

													$end_date_time=$sub_process_end_date.'.'.$unload_hour.'.'.$unload_minut;
													$prod_end_date_time=strtotime($end_date_time);

													$diff = ($prod_end_date_time - $prod_start_date_time);
													$total = $diff/60;
													$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
													?>
													<td width="80"><? echo $total_time; ?></td>
													<td width="120"><?echo $row['item_description'];?></td>
													<td width="80"><?echo $dyeing_result[$row['result']];?></td>
													<?
													$unload_remarks = $row['remarks'];
													$load_remarks = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["remarks"];
													if ($unload_remarks=="")
													{
														?><td><?echo $load_remarks;?></td><?
													}
													else
													{
														?><td><?echo $row['remarks'];?></td><?
													}
													?>
												</tr>
												<?
											}
											// Cause of Machine Idle
											//if (isset($idle_data_arr[$p_date][$machine_id]['machine']))
											//{
												foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
												{
												?>
												<tr style="background: #F0BC8F;">
													<td colspan="5">
														<?
														//echo $cause_type[$idle_data_arr[$p_date][$machine_id]['machine_idle_cause']];
														echo $cause_type[$cause_id];
														?>
													</td>
													<td colspan="5">
														<?
														echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
														?>
													</td>
													<td></td>
													<td></td>
                                                    <td></td>
													<td></td>
													<td>
														<?
														$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
														$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
														$hour_minut = $idle_start_hour.':'.$idle_start_minut;
														$start_hour_minut = date('h:i A', strtotime($hour_minut));
														echo $start_hour_minut;
														?>
													</td>
													<td>
														<?
														$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
														$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
														$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
														$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
														echo $end_hour_minut;
														?>
													</td>
													<td>
														<?
														$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
														$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];

														$before = strtotime($from_date . " " . $start_hour_minut);
														$after = strtotime($to_date . " " . $end_hour_minut);
														$diff = $after - $before;

														// $diff is in seconds
														$hours = floor($diff / 3600);
														$minutes = floor(($diff - $hours * 3600) / 60);
														$seconds = $diff - $hours * 3600 - $minutes * 60;
														echo $hours.'.'.$minutes.'H';
														?>
													</td>
													<td></td>
													<td></td>
													<td></td>
												</tr>
												<?
											}
											?>
											<tr class="tbl_bottom">
												<td colspan="7" align="right"><b>Total:</b></td>
												<td><strong><? echo $subcon_other_mcw_wise_total;?></strong></td>
												<td><strong><? echo $subcon_white_mcw_wise_total;?></strong></td>
													<td><strong><? echo $subcon_wash_mcw_wise_total;?></strong></td>
                                                    <td><strong><? echo $subcon_normal_wash_mcw_wise_total;?></strong></td>
                                                    <td><strong><? echo $subcon_aop_wash_mcw_wise_total;?></strong></td>
														<td><strong><? echo $subcon_pro_qty_total;?></strong></td>
                                                        
															<td colspan="9"></td>
														</tr>
														<?
														$i++;
														$subcon_other_date_wise_total+=$subcon_other_mcw_wise_total;
														$subcon_white_date_wise_total+=$subcon_white_mcw_wise_total;
														$subcon_wash_date_wise_total+=$subcon_wash_mcw_wise_total;
														
														$subcon_normal_wash_date_wise_total+=$subcon_normal_wash_mcw_wise_total;
														$subcon_aop_wash_date_wise_total+=$subcon_aop_wash_mcw_wise_total;
														
														$subcon_pro_qty_date_wise_total+=$subcon_pro_qty_total;
													}
													?>
													<tr class="tbl_bottom">
														<td colspan="7" align="right"><b>Date wise Total:</b></td>
														<td><strong><? echo $subcon_other_date_wise_total;?></strong></td>
														<td><strong><? echo $subcon_white_date_wise_total;?></strong></td>
														<td><strong><? echo $subcon_wash_date_wise_total;?></strong></td>
                                                        <td><strong><? echo $subcon_normal_wash_date_wise_total;?></strong></td>
                                                        <td><strong><? echo $subcon_aop_wash_date_wise_total;?></strong></td>
														<td><strong><? echo $subcon_pro_qty_date_wise_total;?></strong></td>
														<td colspan="9"></td>
													</tr>
													<?
													$subcon_other_grnd_total+=$subcon_other_date_wise_total;
													$subcon_white_grnd_total+=$subcon_white_date_wise_total;
													$subcon_wash_grnd_total+=$subcon_wash_date_wise_total;
													
													$subcon_normal_wash_grnd_total+=$subcon_aop_wash_date_wise_total;
													$subcon_aop_wash_grnd_total+=$subcon_aop_wash_date_wise_total;
													
													$subcon_pro_qty_grnd_total+=$subcon_pro_qty_date_wise_total;
												}
												?>
												<tfoot>
													<tr class="tbl_bottom">
														<td width="30"></td>
														<td width="80"></td>
														<td width="80"></td>
														<td width="80"></td>
														<td width="80"></td>
														<td width="80"></td>
														<td width="80" align="right"><strong>Grand Total :</strong></td>
														<td width="80"><strong><? echo $subcon_other_grnd_total;?></strong>&nbsp;</td>
														<td width="80"><strong><? echo $subcon_white_grnd_total;?></strong></td>
														<td width="80"><strong><? echo $subcon_wash_grnd_total;?></strong></td>
                                                        <td width="80"><strong><? echo $subcon_normal_wash_grnd_total;?></strong></td>
                                                        <td width="80"><strong><? echo $subcon_aop_wash_grnd_total;?></strong></td>
														<td width="80"><strong><? echo $subcon_pro_qty_grnd_total;?></strong></td>
														<td colspan="9" width="80"></td>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
									<?
	}
								// Sample Without Order
	if ($order_type==0 || $order_type==3)
	{
	?>
	<div>
	<table width="518" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th colspan="5">Sample Without Order</th>
			</tr>
		</thead>
	</table>
	<table width="1900" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	<thead>
		<tr>
			<th rowspan="2" width="30">M/C No</th>
			<th rowspan="2" width="80">M/C & Capacity</th>
			<th rowspan="2" width="80">Buyer Name</th>
			<th rowspan="2" width="80">Batch No.</th>
			<th rowspan="2" width="80">Batch Color</th>
			<th rowspan="2" width="80">Booking No.</th>
			<th rowspan="2" width="80">Color Range</th>
			<th colspan="7" width="80">Total Production Qty in Kg</th>
			<th rowspan="2" width="80">Water/kg in Ltr</th>
			<th rowspan="2" width="80">M/C UT%y</th>
			<th rowspan="2" width="80">Loading Time</th>
			<th rowspan="2" width="80">Unloading Time</th>
			<th rowspan="2" width="80">Total Time (Hour)</th>
			<th rowspan="2" width="120">Fabric Construction</th>
			<th rowspan="2" width="80">Result</th>
			<th rowspan="2">Remarks</th>
		</tr>
		<tr>
			<th width="80" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
			<th width="80" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
			<th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
            <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Normal Wash</th>
            <th width="80" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">AOP Wash</th>
			<th width="80">Re-Process</th>
			<th width="80">Trims Weight</th>
		</tr>
	</thead>
	</table>
	<div style="width:1920px; overflow-y:scroll; max-height:300px;" id="scroll_body">
	<table align="center" cellspacing="0" width="1900"  border="1" rules="all" class="rpt_table" >

		<?
		 $sqls="SELECT  a.batch_id, a.batch_ext_no,a.load_unload_id,a.entry_form,a.process_id, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks,b.production_qty
		from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form in(424,35) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ";

		$load_time_array=array();
		foreach(sql_select($sqls) as $vals)
		{
			$load_unload_id=$vals[csf("load_unload_id")];
			$entry_form_id=$vals[csf("entry_form")];
			if($load_unload_id==1)
			{
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("process_end_date")];
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
			}
			
			if($entry_form_id==424)
			{
				//echo $entry_form_id.'X';
			$process_id=$vals[csf("process_id")];
			if($process_id==209)
			{
			$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];									}
			else if($process_id==193)
			{
			$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
			}
			$wash_qty_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
		  }
		}
		//print_r($wash_qty_array);
			// Main query
		$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date,
		a.water_flow_meter, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity,
		p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
		from wo_non_ord_samp_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2
		and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order=1
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		order by a.production_date, c.machine_no";
		//echo $sql_result;die;

		$sql_dtls=sql_select($sql_result);
		$date_data_arr=array();
		foreach ($sql_dtls as $row)
		{
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('production_date')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition']=$row[csf('const_composition')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
			$date_data_arr[$row[csf('production_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];
		}

		$color_range=array(1=>"Dark Color",2=>"Light Color",3=>"Black Color",4=>"White Color",5=>"Average Color",6=>"Melange",7=>"Wash",8=>"Scouring",9=>"Extra Dark",10=>"Medium Color",11=>"Super Dark");


		$rowspan_arr=array();
		foreach ($date_data_arr as $p_date=>$prod_date_data)
		{
			foreach ($prod_date_data as $machine_id => $machine_data)
			{
				foreach ($machine_data as $batch_id => $row)
				{
					if (isset($rowspan_arr[$p_date][$machine_id]))
					{
						$rowspan_arr[$p_date][$machine_id]++;
					}
					else
					{
						$rowspan_arr[$p_date][$machine_id]=1;
					}
				}
			}
		}

		$i=1;
		$other_grnd_total=$white_grnd_total=$wash_grnd_total=$normal_wash_grnd_total=$aop_wash_grnd_total=$wo_pro_qty_grnd_total=0;
		foreach ($date_data_arr as $p_date=>$prod_date_data)
		{
			$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$normal_wash_date_wise_total=$aop_wash_date_wise_total=$wo_pro_qty_date_wise_total=0;
			?>
			<tr>
				<td colspan="22" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
			</tr>
			<?
			foreach ($prod_date_data as $machine_id => $machine_data)
			{
				$other_mcw_wise_total=$white_mcw_wise_total=$wo_wash_mcw_wise_total=$wo_normal_wash_mcw_wise_total=$wo_aop_wash_mcw_wise_total=$wo_pro_qty_total=0;
				$r=0;
				foreach ($machine_data as $batch_id => $row)
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
						<?
						if ($r==0)
						{
							?>
							<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
							{
								$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
								echo $rowspan_arr[$p_date][$machine_id]+1+$m_cause_row;
							}
							else
							{
								$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
								echo $rowspan_arr[$p_date][$machine_id]+$m_cause_row;
							}
							?>" width="30" valign="middle"><?echo $i;?></td>
							<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
							{
								$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
								echo $rowspan_arr[$p_date][$machine_id]+1+$m_cause_row;
							}
							else
							{
								$m_cause_row=count($idle_data_arr2[$p_date][$machine_id]);
								echo $rowspan_arr[$p_date][$machine_id]+$m_cause_row;
							}
							?>" width="80" valign="middle" title="MC Id=<? echo $machine_id;?>"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
							<?
						} $r++;
						?>
						<td width="80"><?echo $buyer_library[$row['buyer_id']];?></td>
						<td width="80"><?echo $row['batch_no'];?></td>
						<td width="80"><p><?echo $color_library[$row['color_id']]?></p></td>
						<td width="80"><?echo $row['booking_no'];?></td>
						<td width="80"><?echo $color_range[$row['color_range_id']];?></td>
						<?
						
						$wash_entry_form=$wash_qty_array[$machine_id][$batch_id][$row["batch_ext_no"]]["wash_entry_form"];
						$other_pro_qty=$white_pro_qty=$wo_wash_pro_qty=$pro_qty=0;
						if($wash_entry_form!=424)
						{
							$samp_prod_qty_aop=$samp_prod_qty_normal=0;
							if ($row['color_range_id']==5 && $row['result']==1)
							{
								$other_pro_qty = $row['batch_qty'];
							}
							elseif ($row['color_range_id']==4 && $row['result']==1)
							{
								$white_pro_qty = $row['batch_qty'];
							}
							elseif ($row['color_range_id']==7 && $row['result']==1)
							{
								$wo_wash_pro_qty = $row['batch_qty'];
							}
						}
						else
						{
							$prod_qty_normal=$wash_qty_array[$machine_id][$batch_id][$row["batch_ext_no"]]["prod_qty_normal"];
							$prod_qty_aop=$wash_qty_array[$machine_id][$batch_id][$row["batch_ext_no"]]["prod_qty_aop"];
							$other_pro_qty=$white_pro_qty=$wo_wash_pro_qty=$pro_qty=0;
						}
						?>
						<td width="80" align="right"><? echo $other_pro_qty; $other_mcw_wise_total+=$other_pro_qty;?></td>

						<?
						
						?>
						<td width="80" align="right"><? echo $white_pro_qty; $white_mcw_wise_total+=$white_pro_qty;?></td>
						<?
						
						?>
						<td width="80" align="right"><? echo $wo_wash_pro_qty; $wo_wash_mcw_wise_total+=$wo_wash_pro_qty;?></td>
                        <td width="80" align="right"><? echo $prod_qty_normal; $wo_normal_wash_mcw_wise_total+=$prod_qty_normal;?></td>
                        <td width="80" align="right"><? echo $prod_qty_aop; $wo_aop_wash_mcw_wise_total+=$prod_qty_aop;?></td>
						<?
						if ($row['result']==2)
						{
							$pro_qty = $row['batch_qty'];
						}
						?>
						<td width="80" align="right"><?echo $pro_qty; $wo_pro_qty_total+=$pro_qty;?></td>
						<td width="80"><?echo $row['total_trims_weight'];?></td>
						<td width="80"><?echo $row['water_flow_meter'];?></td>
						<?
						$pro_qty = $row['batch_qty'];
						$mc_capacity = $row['prod_capacity'];
						$mc_ut = ($pro_qty/$mc_capacity)*100;
						?>
						<td width="80"><? echo number_format($mc_ut,2,'.','');?></td>
						<?
						$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
						$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
						$process_start_date = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
						$start_time = $load_hour.'.'.$load_minut;
						$load_hour_minut = date('h:i A', strtotime($start_time));
						?>
						<td width="80"><?echo $load_hour_minut;?></td>
						<?
						$unload_hour = $row['unload_hours'];
						$unload_minut = $row['unload_minutes'];
						$process_end_dateIs = $row['p_date'];
						$end_time = $unload_hour.'.'.$unload_minut;
						$unload_hour_minut = date('h:i A', strtotime($end_time));
						?>
						<td width="80"><?echo $unload_hour_minut; ?></td>
						<?
						$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
						$prod_start_date_time=strtotime($start_date_time);

						$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
						$prod_end_date_time=strtotime($end_date_time);

						$diff = ($prod_end_date_time - $prod_start_date_time);
						$total = $diff/60;
						$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
						?>
						<td width="80"><? echo $total_time; ?></td>
						<td width="120"><?echo $row['const_composition'];?></td>
						<td width="80"><?echo $dyeing_result[$row['result']];?></td>
						<?
						$unload_remarks = $row['remarks'];
						$load_remarks = $load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
						if ($unload_remarks=="")
						{
							?><td><?echo $load_remarks;?></td><?
						}
						else
						{
							?><td><?echo $row['remarks'];?></td><?
						}
						?>
					</tr>
					<?

				}
				 // Cause of Machine Idle
				//if (isset($idle_data_arr[$p_date][$machine_id]['machine']))
				//{
					foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
					{
					?>
					<tr style="background: #F0BC8F;">
						<td colspan="5">
							<?
							echo $cause_type[$cause_id];
							?>
						</td>
						<td colspan="5">
							<?
							echo $idle_data_ar2r[$p_date][$machine_id][$cause_id]['remarks'];
							?>
						</td>
						<td></td>
						<td></td>
                        <td></td>
						<td></td>
						<td>
							<?
							$idle_start_hour = $idle_data_ar2r[$p_date][$machine_id][$cause_id]['from_hour'];
							$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
							$hour_minut = $idle_start_hour.':'.$idle_start_minut;
							$start_hour_minut = date('h:i A', strtotime($hour_minut));
							echo $start_hour_minut;
							?>
						</td>
						<td>
							<?
							$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
							$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
							$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
							$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
							echo $end_hour_minut;
							?>
						</td>
						<td>
							<?
							$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
							$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];

							$before = strtotime($from_date . " " . $start_hour_minut);
							$after = strtotime($to_date . " " . $end_hour_minut);
							$diff = $after - $before;

							// $diff is in seconds
							$hours = floor($diff / 3600);
							$minutes = floor(($diff - $hours * 3600) / 60);
							$seconds = $diff - $hours * 3600 - $minutes * 60;
							echo $hours.'.'.$minutes.'H';
							?>
						</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
					<?
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="7" align="right"><b>Total:</b></td>
					<td><strong><? echo $other_mcw_wise_total;?></strong></td>
					<td><strong><? echo $white_mcw_wise_total;?></strong></td>
						<td><strong><? echo $wo_wash_mcw_wise_total;?></strong></td>
                        <td><strong><? echo $wo_normal_wash_mcw_wise_total;?></strong></td>
                        <td><strong><? echo $wo_aop_wash_mcw_wise_total;?></strong></td>
							<td><strong><? echo $wo_pro_qty_total;?></strong></td>
								<td colspan="9"></td>
							</tr>
							<?
							$i++;
							$other_date_wise_total+=$other_mcw_wise_total;
							$white_date_wise_total+=$white_mcw_wise_total;
							$wash_date_wise_total+=$wo_wash_mcw_wise_total;
							$aop_wash_date_wise_total+=$wo_aop_wash_mcw_wise_total;
							$normal_wash_date_wise_total+=$wo_normal_wash_mcw_wise_total;
							$wo_pro_qty_date_wise_total+=$wo_pro_qty_total;
						}
						?>
						<tr class="tbl_bottom">
							<td colspan="7" align="right"><b>Date wise Total:</b></td>
							<td><strong><? echo $other_date_wise_total;?></strong></td>
							<td><strong><? echo $white_date_wise_total;?></strong></td>
							<td><strong><? echo $wash_date_wise_total;?></strong></td>
                            <td><strong><? echo $normal_wash_date_wise_total;?></strong></td>
                            <td><strong><? echo $aop_wash_date_wise_total;?></strong></td>
							<td><strong><? echo $wo_pro_qty_date_wise_total;?></strong></td>
							<td colspan="9"></td>
						</tr>
						<?
						$other_grnd_total+=$other_date_wise_total;
						$white_grnd_total+=$white_date_wise_total;
						$wash_grnd_total+=$wash_date_wise_total;
						
						$normal_wash_grnd_total+=$normal_wash_date_wise_total;
						$aop_wash_grnd_total+=$aop_wash_date_wise_total;
						
						$wo_pro_qty_grnd_total+=$wo_pro_qty_date_wise_total;
					}
					?>
					<tfoot>
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80"></td>
							<td width="80" align="right"><strong>Grand Total :</strong></td>
							<td width="80"><strong><? echo $other_grnd_total;?></strong>&nbsp;</td>
							<td width="80"><strong><? echo $white_grnd_total;?></strong></td>
							<td width="80"><strong><? echo $wash_grnd_total;?></strong></td>
                            <td width="80"><strong><? echo $normal_wash_grnd_total;?></strong></td>
                            <td width="80"><strong><? echo $aop_wash_grnd_total;?></strong></td>
							<td width="80"><strong><? echo $wo_pro_qty_grnd_total;?></strong></td>
							<td colspan="19"></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<br>
		<?
	}
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";

	exit();
}
	
 										// Show Button 2, Summary
if($action=="report_generate2")
{		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company=str_replace("'","",$cbo_company_id);
	$machine_name=str_replace("'","",$txt_machine_id);
	$cbo_location=str_replace("'","",$cbo_location_id);
	$order_type=str_replace("'","",$cbo_order_type);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$booking_no=str_replace("'","",$txt_booking_no);
	$order_no=str_replace("'","",$txt_order_no);
	$batch=str_replace("'","",$txt_batch);
	$batch_color=str_replace("'","",$hidden_color_id);
	$txt_color_range=str_replace("'","",$txt_color_range);
	$floor_name=str_replace("'","",$cbo_floor_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);


	if ($batch=="") $batch_cond =""; else $batch_cond =" and a.batch_no='$batch' ";
	if ($order_type==0) $order_type_cond =""; else $order_type_cond =" and a.order_type=$order_type ";
	if ($cbo_location==0 || $cbo_location=='') $location_id =""; else $location_id =" and c.location_id=$cbo_location ";
	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and c.floor_id=$cbo_floor_id";
	if ($machine_name=="") $machine_cond=""; else $machine_cond =" and c.id in ($machine_name) ";
	if ($buyer_name==0  || $buyer_name=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$buyer_name ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and q.booking_no=$txt_booking_no ";
	if ($batch_color=="") $batch_color_cond =""; else $batch_color_cond =" and q.color_id=$batch_color ";
	if ($txt_color_range==0 || $txt_color_range=="") $color_range_cond =""; else $color_range_cond =" and q.color_range_id=$txt_color_range ";
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="")
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="")
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$machineArr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	ob_start();

	if ($order_type==0 || $order_type==1) // Self Order sql and array
	{
		$Self_sqls="SELECT  a.batch_id,a.process_id, a.batch_ext_no,a.load_unload_id,a.entry_form, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date as prod_date, a.remarks,b.production_qty
		from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form in(35) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by a.batch_id";

		$self_load_time_array=array();
		foreach(sql_select($Self_sqls) as $vals)
		{
			if($vals[csf("load_unload_id")]==1 && $vals[csf("entry_form")]==35)
			{
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("prod_date")];
			$self_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
			}
			else if($vals[csf("entry_form")]==424)
			{
				$process_id=$vals[csf("process_id")];
				if($process_id==209)
				{
					//echo $vals[csf("production_qty")].',';
					if($vals[csf("production_qty")]>0)
					{
					$wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];
					}
				}
				else if($process_id==193)
				{
					if($vals[csf("production_qty")]>0)
					{
					$wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
					}
				}
				$wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
			
			}
		}
		

		//print_r($wash_qty_array);
		/*echo '<pre>';
		print_r($self_load_time_array);die;*/
		// Main query
		/* $self_sql_result="SELECT a.id, a.batch_no, a.batch_id,a.system_no, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.process_end_date, a.production_date,
		a.water_flow_meter,a.incomplete_result,a.redyeing_needed,a.shade_matched, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity,
		p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight,q.double_dyeing, q.process_id
		from wo_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2
		and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order!=1
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.system_no";*/
		  $self_sql_result="(SELECT a.id, a.batch_no, a.batch_id,a.system_no,a.entry_form,a.process_id as prod_process_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes,a.start_hours,a.start_minutes, a.result, a.remarks,a.process_end_date, a.production_date,a.process_start_date,
		a.water_flow_meter,a.incomplete_result,a.redyeing_needed,a.shade_matched,b.id as dtls_id, b.batch_qty,b.production_qty, b.const_composition, c.machine_no, c.prod_capacity,
		p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight,q.double_dyeing, q.process_id
		from wo_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2
		and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order!=1
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		union
		SELECT a.id, a.batch_no, a.batch_id,a.system_no,a.entry_form,a.process_id as prod_process_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes,a.start_hours,a.start_minutes, a.result, a.remarks,a.process_end_date, a.production_date,a.process_start_date,
		a.water_flow_meter,a.incomplete_result,a.redyeing_needed,a.shade_matched,b.id as dtls_id, b.batch_qty,b.production_qty, b.const_composition, c.machine_no, c.prod_capacity,
		p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight,q.double_dyeing, q.process_id
		from wo_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where  a.id=b.mst_id and a.machine_id=c.id and a.entry_form=424 and q.entry_form=0 
		and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order!=1 and a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
		
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0) order by system_no
		
		";

		//echo $self_sql_result;die;

		$sql_dtls=sql_select($self_sql_result);
		//print_r($sql_dtls);
		$self_date_data_arr=array();
		$fabr_arr=array();
		$batch_qty_arr=array();$self_all_batch_id=""; $self_all_batch_no="";
		$fun_batch_rowspan_arr=array();
		$array_result_arr=array(2,4);
		$self_double_dying_qty_summ=0;
		$batch_qty_chk_arr=array();
		foreach ($sql_dtls as $row)
		{

			$all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$all_batch_data[$row[csf('batch_id')]]['result']=$row[csf('result')];


			$all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$self_all_batch_id.=$row[csf('batch_id')].',';
			$self_all_batch_no.="'".$row[csf('batch_no')]."'".',';//row[csf("entry_form")]
			if($batch_qty_chk_arr[$row[csf('dtls_id')]]=="")
			{
				
			//$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];
			//$batch_qty_chk_arr[$row[csf('dtls_id')]]=$row[csf('dtls_id')];
			
			}
			if($row[csf('entry_form')]==35)
			{
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_qty']+=$row[csf('production_qty')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qty']+=$row[csf('batch_qty')];
			}
			else
			{
				
				$process_id=$row[csf("prod_process_id")];
				//echo $process_id.'D,';
				if($process_id==209) //aop
				{
				//	echo $vals[csf("production_qty")].',';
					if($row[csf("production_qty")]>0)
					{
					$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_qty_aop']+=$row[csf('production_qty')];
					}
				}
				else if($process_id==193) //Normal wash 
				{
					if($row[csf("production_qty")]>0)
					{
						//echo $row[csf('production_qty')].',';
					$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_qty_normal']+=$row[csf('production_qty')];
					}
				}
				//start_hours,a.start_minutes
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_start_hr']=$row[csf('start_hours')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_star_min']=$row[csf('start_minutes')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_end_hr']=$row[csf('end_hours')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_end_min']=$row[csf('end_minutes')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_prod_date']=$row[csf('production_date')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_start_date']=$row[csf('process_start_date')];
			}
			
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['entry_form']=$row[csf('entry_form')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['system_no']=$row[csf('system_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('process_end_date')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
			
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_end_date']=$row[csf('production_date')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition'].=$row[csf('const_composition')].',';
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['incomplete_result']=$row[csf('incomplete_result')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['redyeing_needed']=$row[csf('redyeing_needed')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['shade_matched']=$row[csf('shade_matched')];
			$self_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];

			if($fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=="")
			{
				$fun_batch_rowspan_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('system_no')]]+=1;
				$fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=1;
			}
			//if($row[csf('batch_ext_no')]=="" || $row[csf('batch_ext_no')]==0) $row[csf('batch_ext_no')]="";else $row[csf('batch_ext_no')]=$row[csf('batch_ext_no')];
			$batch_qty_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_no')]]=$row[csf('batch_ext_no')];
			$machine_no_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
			if($row[csf('double_dyeing')]==1)//Double Dying Yes
			{
				//echo $row[csf('batch_qty')].', ';
			$self_double_dying_qty_summ+=$row[csf('batch_qty')];
			}

			//echo $row[csf('batch_ext_no')].'X';
		}
		asort($self_date_data_arr);
		//print_r($batch_qty_arr);
		$self_all_batch_id=implode(",",array_unique(explode(",",$self_all_batch_id)));
			$poIds=chop($self_all_batch_id,','); //$batch_cond_for_in="";
			$po_ids=count(array_unique(explode(",",$self_all_batch_id)));
			if($db_type==2 && $po_ids>1000)
			{
				$batch_cond_for_in=" and (";
				$batch_cond_for_in2=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$batch_cond_for_in.=" b.batch_id in($ids) or";
					$batch_cond_for_in2.=" q.id in($ids) or";

				}
				$batch_cond_for_in=chop($batch_cond_for_in,'or ');
				$batch_cond_for_in.=")";
				$batch_cond_for_in2=chop($batch_cond_for_in2,'or ');
				$batch_cond_for_in2.=")";
			}
			else
			{
				$batch_cond_for_in=" and b.batch_id in($poIds)";
				$batch_cond_for_in2=" and q.id in($poIds)";
			}
		 $batch_sql_result="SELECT q.id, q.batch_no,b.id as dtls_id, b.batch_qnty,
		p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight,q.double_dyeing, q.process_id
		from pro_batch_create_mst q,pro_batch_create_dtls b,wo_booking_mst p
		where q.id=b.mst_id  and q.booking_no_id=p.id and q.booking_without_order!=1 and q.entry_form=0     $buyer_name_cond $batch_color_cond $color_range_cond $batch_cond_for_in2 	and q.status_active=1 and q.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0";
		$sql_batch=sql_select($batch_sql_result);
		foreach($sql_batch as $row)
		{
			$self_batch_qty_arr[$row[csf("id")]]+=$row[csf("batch_qnty")];
		}
		unset($sql_batch);
		//print_r($self_batch_qty_arr);
	
			$self_all_batch_no=implode(",",array_unique(explode(",",$self_all_batch_no)));
			$batIds=chop($self_all_batch_no,','); //$batch_cond_for_in="";
			$bat_ids=count(array_unique(explode(",",$self_all_batch_no)));
			if($db_type==2 && $bat_ids>1000)
			{
				$batch_no_cond_for_in=" and (";
				$batIdsArr=array_chunk(explode(",",$batIds),999);
				foreach($batIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$batch_no_cond_for_in.=" aa.batch_no in($ids) or";
				}
				$batch_no_cond_for_in=chop($batch_no_cond_for_in,'or ');
				$batch_no_cond_for_in.=")";
			}
			else
			{
				$batch_no_cond_for_in=" and aa.batch_no in($batIds)";
			}


		 /*echo "<pre>";
		 print_r($self_date_data_arr);*/
		/* $sql_prod="select aa.batch_no,aa.result,aa.batch_ext_no,b.batch_id,b.receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.receive_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $batch_cond_for_in";//batch_no
		$fin_prod=sql_select($sql_prod);
		foreach($fin_prod as $row)
		{

			if($row[csf("result")]==4 && $row[csf("batch_ext_no")]=="")
			{
				$self_fin_prod_arr[$row[csf("batch_no")]][1]["fin_qnty"]=$row[csf("receive_qnty")];
			}
			if($row[csf("batch_ext_no")]=="")
			{
			$self_fin_prod_arr2[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];
			}

		}
		*/ // $all_batch[$row[csf('batch_id')]]
 		/* echo $sql_prod2="select aa.batch_no,aa.result,max(aa.batch_ext_no) as batch_ext_no,b.batch_id,sum(b.receive_qnty) as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.receive_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $batch_no_cond_for_in group by aa.batch_no,aa.result,b.batch_id,b.machine_no_id";//batch_no
		$fin_prod2=sql_select($sql_prod2);
		foreach($fin_prod2 as $row)
		{

			$batch_ext_no=$row[csf("batch_ext_no")];
			$self_fin_prod_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
			$self_fin_prod_arr4[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];
			if(($row[csf("batch_ext_no")]*1)<1)
			{
			$self_fin_prod_arr_check[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
			}

		}

		*/


	 $sql_prod2="select a.recv_number,b.batch_id,b.receive_qnty as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id  and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 and b.batch_id in (".implode(",",$all_batch).")  ";//batch_no
	 $fin_prod2=sql_select($sql_prod2);
	 foreach($fin_prod2 as $row)
	 {

		$batch_ext_no=$row[csf("batch_ext_no")];
		$self_fin_prod_qty_arr[$row[csf("batch_id")]]["recv_number"]=$row[csf("recv_number")];
		$self_fin_prod_qty_arr[$row[csf("batch_id")]]["receive_qnty"]+=$row[csf("receive_qnty")];
		$self_fin_prod_arr4[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];

		$self_fin_prod_arr_check[$all_batch_data[$row[csf('batch_id')]]['batch_no']][$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["fin_qnty"] +=$row[csf("receive_qnty")];
		$self_fin_prod_arr_check[$all_batch_data[$row[csf('batch_id')]]['batch_no']][$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["result"] =$all_batch_data[$row[csf('batch_id')]]['result'];

		 /* $all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$all_batch_data[$row[csf('batch_id')]]['result']*/
		}

		//print_r($self_fin_prod_arr_check);

		//print_r($self_fin_prod_arr3);
		$self_rowspan_arr=array();$tt=1;
		$self_func_batch_arr=array();$self_trims_chk_batch_arr=array();
		$mc_ut_qty_arr=array();$self_prod_qty=$self_summ_trims_wgt_batch_qty=$tot_self_actual_prod_qty=0;
		$batch_qty=$self_summ_white_qty=$self_summ_wash_qty=$self_summ_color_batch_qty=$summ_white_qty=0;
		$total_summ_trim_wgt=0;$tot_self_summ_normal_wash=$tot_self_summ_aop_wash=0;
		foreach ($self_date_data_arr as $p_date=>$prod_date_data)
		{
			foreach ($prod_date_data as $machine_id => $machine_data)
			{
				foreach ($machine_data as $batch_id => $row)
				{
					$self_fin_prod_summ=$self_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
					$self_fin_prod_summ2=$self_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
					$self_fin_prod_summ3=$self_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
					
					  $self_batch_qty=$self_batch_qty_arr[$batch_id];
					  	$entry_form_id =$row['entry_form'];
						//$tot_aop_qty=$prod_qty_normal=0;
					 //if($entry_form_id==424)
					 //{
					  //if($row['prod_qty_normal']>0)
						//{
						$tot_self_summ_normal_wash+=$row['prod_qty_normal'];
						$prod_qty_normal=$row['prod_qty_normal'];
						//}
						//elseif($row['prod_qty_aop']>0)
						//{
							
						$tot_self_summ_aop_wash+=$row['prod_qty_aop'];
						$prod_qty_aop=$row['prod_qty_aop'];
						
						//}
						//echo $prod_qty_normal.'='.$prod_qty_aop.'<br>';
					// }
						
						$tot_aop_qty=$prod_qty_normal+$prod_qty_aop;
					   $row['batch_qty']=0;
					  $row['batch_qty']=$row['production_qty'];
					  if($tot_aop_qty>0)
					  {
						  $batch_qty =$tot_aop_qty;
					  }
					  else
					  {
						  $batch_qty =$row['batch_qty'];
					  }
						
						
				
					/*if($row['batch_ext_no']==1)
					{
					$bext=$row['batch_ext_no'];
					}
					else*/ 
					// if($entry_form_id==35)
					// {
					 $bext=$row['batch_ext_no']-1;
					// }
										
					//$bext=$row['batch_ext_no']-1;
					if(($bext*1)<=0) $bext='';
					
					

					if ($row['result']==1)
					{
						if ($row['color_range_id']==4)
						{
							$self_summ_white_qty += $row['batch_qty'];
						}
						else if ($row['color_range_id']==7)
						{
							$self_summ_wash_qty += $row['batch_qty'];
						}
						else $self_summ_color_batch_qty += $row['batch_qty'];

					}
					else if ($row['result']==5 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=="") // //Under Trial//Fin Fab not Avaiable
					{
						if ($row['color_range_id']==4)
						{
							$self_summ_white_qty +=0;
						}
						else if ($row['color_range_id']==7)
						{
							$self_summ_wash_qty +=0;
						}
						else $self_summ_color_batch_qty +=0;
					}
					else if($row['batch_ext_no']>0)
					{
						$self_summ_white_qty +=0;
						$self_summ_wash_qty += 0;
						$self_summ_color_batch_qty +=0;
					}
					//$entry_form_wash=$wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["wash_entry_form"];
				//	echo $row['prod_qty_normal'].', ';
					//if($entry_form_id==424)
					//{
						// $normal_wash_qty=$wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_normal"];
						//$aop_wash_qty=$wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_aop"];
						//$self_summ_white_qty +=0;
						//$self_summ_wash_qty += 0;
						//$self_summ_color_batch_qty +=0;
						
						//echo $tot_self_summ_normal_wash.'='.$tot_self_summ_aop_wash;
						
					//}
					
					
					/*if ($row['result']==4 && $row['batch_ext_no']>0 && $self_fin_prod_summ>0) //if ($row['result']==4 && $row['batch_ext_no']>0 )
					{
						$prod_qty += $row['batch_qty'];
					}*/
					//$self_fin_prod_chk=$self_fin_prod_arr[$row[csf('batch_no')]]["fin_qnty"];
					$trims_weight_qty+=$row['total_trims_weight'];
					$self_prod_qty+=$row['batch_qty'];

					if ($row['result']==1 && $row['batch_ext_no']=="")
					{
						if ($checkTrimWgtArr[$row['batch_id']]=='')
						{
							$total_summ_trim_wgt+=$row['total_trims_weight'];
						}
						$self_actual_prod_qty= $row['batch_qty'];
						$tot_self_actual_prod_qty+= $row['batch_qty'];
							//echo  $row['result'].'='.$re_process_qty3.'<br>';
					}
					else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
					{
						$self_actual_prod_qty = 0;
						$tot_self_actual_prod_qty+= 0;
								//echo "B";
					}
					if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
					{
						if ($checkTrimWgtArr[$row['batch_id']]=='')
						{
							$total_summ_trim_wgt+=$row['total_trims_weight'];
						}
						$self_actual_prod_qty= $row['batch_qty'];
						$tot_self_actual_prod_qty+= $row['batch_qty'];
					}

					if($entry_form_id==35)
					 {
					if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
					{
						//$self_summ_re_pro_qty +=$row['batch_qty'];// $row['batch_qty'];
						$self_re_pro_qty =$row['batch_qty'];// $row['batch_qty'];
						$is_finished[$row['batch_no']]=1;
						//echo $row['batch_qty'].',';
					}
					if($is_finished[$row['batch_no']]==1) {$self_re_pro_qty =$row['batch_qty'];} else {$self_re_pro_qty=0;}
					$self_summ_re_pro_qty+=$self_re_pro_qty;
					
					//$self_summ_re_pro_qty +=$row['batch_qty'];// $row['batch_qty'];
				 }
					
					if($self_actual_prod_qty>0)
					{
						if(!in_array($batch_id,$self_trims_chk_batch_arr))
						{
							$tt++;

							$self_trims_chk_batch_arr[]=$batch_id;
							$self_summ_trims_wgt_batch_qty +=$row['total_trims_weight'];
							$trimswgt =$row['total_trims_weight'];
						}
						else { $self_summ_trims_wgt_batch_qty +=0;$trimswgt =0;}
					}
				
					$summ_mc_total_qty+=$batch_qty+$trimswgt;
					$mc_ut_qty_arr[$p_date][$machine_id] += $batch_qty+$row['total_trims_weight'];
					$self_total_batch+=count($row['batch_no']);
					$self_func_batch_arr[$row['system_no']]=$row['system_no'];
					$tot_self_func_batch_arr=count($self_func_batch_arr);
					if (isset($self_rowspan_arr[$p_date][$machine_id]))
					{
						$self_rowspan_arr[$p_date][$machine_id]++;
					}
					else
					{
						$self_rowspan_arr[$p_date][$machine_id]=1;
						$self_total_machine_capacity+=$row['prod_capacity'];
						$self_total_machine+=count($row['machine_no']);

					}
					$self_avg_batch_per_mc = $self_total_batch/$self_total_machine;
				}

			}
		}
		/*echo '<pre>';
		print_r($mc_ut_qty_arr);*/
	}
	//print_r($self_re_process_qty_arr);

	if ($order_type==0 || $order_type==2) // Subcontract Order sql and array
	{
		$Subcon_sqls=" SELECT a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks
		from pro_fab_subprocess a, lib_machine_name c
		where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
		and a.machine_id=c.id and a.entry_form in(35,38) and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1
		group by a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes,a.process_end_date, a.remarks";

		$subcon_load_time_array=array();
		foreach(sql_select($Subcon_sqls) as $vals)
		{
			$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
			$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
			$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("process_end_date")];
			$subcon_load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
		}
		
	  $sub_sql_wash=" SELECT a.batch_id, a.batch_ext_no,a.process_id, a.machine_id,a.end_hours,a.entry_form, a.end_minutes, a.process_end_date as prod_date, a.remarks,b.production_qty
								from pro_fab_subprocess a,pro_fab_subprocess_dtls b, lib_machine_name c
								where a.id=b.mst_id and a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
								and a.machine_id=c.id and a.entry_form=424 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0";

		$load_time_array=array();
		foreach(sql_select($sub_sql_wash) as $vals)
		{
		$process_id=$vals[csf("process_id")];
		if($process_id==209) //AOP 
		{
		$sub_wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];
		}
		else if($process_id==193)// Normal
		{
		$sub_wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
		}
		$sub_wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
			
		}
		//print_r($sub_wash_qty_array);

		/*echo '<pre>';
		print_r($subcon_load_time_array);die;*/

		if ($buyer_name==0  || $buyer_name=='') $party_name_cond =""; else $party_name_cond =" and f.party_id=$buyer_name ";
		if ($order_no=="") $order_no_cond =""; else $order_no_cond =" and p.order_no=$txt_order_no ";

		// Main query
		$subcon_sql_result="(SELECT a.id, a.batch_no, a.system_no,a.entry_form, a.process_id as prod_process_id, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.process_end_date, a.production_date,a.incomplete_result,a.redyeing_needed, a.water_flow_meter, b.batch_qnty, b.item_description, c.machine_no, c.prod_capacity, f.party_id, p.order_no, q.color_id, q.color_range_id,q.double_dyeing, q.total_trims_weight, q.process_id
		from subcon_ord_dtls p, subcon_ord_mst f, pro_batch_create_mst q, pro_fab_subprocess a, pro_batch_create_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $order_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $party_name_cond $batch_color_cond $color_range_cond and a.machine_id=c.id and a.entry_form in(38) and q.entry_form=36 and a.load_unload_id=2 and a.batch_id=q.id and b.po_id=p.id  and p.job_no_mst= f.subcon_job and q.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		union all 
		SELECT a.id, a.batch_no, a.system_no,a.entry_form,a.process_id as prod_process_id, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.process_end_date, a.production_date,a.incomplete_result,a.redyeing_needed, a.water_flow_meter, b.batch_qnty, b.item_description, c.machine_no, c.prod_capacity, f.party_id, p.order_no, q.color_id, q.color_range_id,q.double_dyeing, q.total_trims_weight, q.process_id
		from subcon_ord_dtls p, subcon_ord_mst f, pro_batch_create_mst q, pro_fab_subprocess a, pro_batch_create_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $order_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $party_name_cond $batch_color_cond $color_range_cond and a.machine_id=c.id and a.entry_form in(424) and q.entry_form=36 and a.batch_id=q.id and b.po_id=p.id  and p.job_no_mst= f.subcon_job and q.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		)  order by process_end_date, machine_no";
		

		//echo $subcon_sql_result;die;

		$sql_dtls=sql_select($subcon_sql_result);
		//print_r($sql_dtls);
		$subcon_double_dying_qty_summ=0;
		$subcon_date_data_arr=array();
		$subc_fun_batch_rowspan_arr=array();
		$subc_batch_qty_arr=array();
		foreach ($sql_dtls as $row)
		{
			$sub_all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$sub_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$sub_all_batch_data[$row[csf('batch_id')]]['result']=$row[csf('result')];

			$all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$sub_all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$subcon_all_batch_id.=$row[csf('batch_id')].',';
			$subcon_all_batch_no.="'".$row[csf('batch_no')]."'".',';
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['system_no']=$row[csf('system_no')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('process_end_date')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_end_date']=$row[csf('production_date')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['item_description'].=$row[csf('item_description')].',';
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['party_id']=$row[csf('party_id')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['order_no']=$row[csf('order_no')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['incomplete_result']=$row[csf('incomplete_result')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['redyeing_needed']=$row[csf('redyeing_needed')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['shade_matched']=$row[csf('shade_matched')];
			$subcon_date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];

			if($subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=="")
			{
				$subc_fun_batch_rowspan_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('system_no')]]+=1;
				$subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=1;
			}

			$subc_batch_qty_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_no')]]=$row[csf('batch_ext_no')];
			$machine_no_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
			if($row[csf('double_dyeing')]==1)//Double Dying Yes
			{
			$subcon_double_dying_qty_summ+=$row[csf('batch_qnty')];
			}
		}
		asort($subcon_date_data_arr);
		$subcon_all_batch_id=implode(",",array_unique(explode(",",$subcon_all_batch_id)));
		$baIds=chop($subcon_all_batch_id,','); $sub_batch_cond_for_in="";
		$ba_ids=count(array_unique(explode(",",$subcon_all_batch_id)));
		if($db_type==2 && $ba_ids>1000)
		{
			$sub_batch_cond_for_in=" and (";
			$sub_batch_cond_for_in2=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$sub_batch_cond_for_in.=" b.batch_id in($ids) or";
				$sub_batch_cond_for_in2.=" q.id in($ids) or";

			}
			$sub_batch_cond_for_in=chop($sub_batch_cond_for_in,'or ');
			$sub_batch_cond_for_in.=")";
			$sub_batch_cond_for_in2=chop($sub_batch_cond_for_in2,'or ');
			$sub_batch_cond_for_in2.=")";
		}
		else
		{
			$sub_batch_cond_for_in=" and b.batch_id in($baIds)";
			$sub_batch_cond_for_in2=" and q.id in($baIds)";
		}
		$subcon_batch_result="SELECT q.id, b.batch_qnty
		from subcon_ord_dtls p, subcon_ord_mst f, pro_batch_create_mst q, pro_batch_create_dtls b
		where  b.mst_id=q.id and b.po_id=p.id  and p.job_no_mst= f.subcon_job and q.id=b.mst_id  and q.entry_form=36   and q.status_active=1 and q.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0   $sub_batch_cond_for_in2  $party_name_cond $batch_color_cond $color_range_cond order by q.id";
		$sub_sql_dtls=sql_select($subcon_batch_result);
		foreach($sub_sql_dtls as $row)
		{
			$sub_batch_qty_arr[$row[csf('id')]]+=$row[csf('batch_qnty')];
		}
		
		
		$subcon_all_batch_no=implode(",",array_unique(explode(",",$subcon_all_batch_no)));
		$baIds=chop($subcon_all_batch_no,','); $sub_batch_no_cond_for_in="";
		$ba_ids=count(array_unique(explode(",",$subcon_all_batch_no)));
		if($db_type==2 && $ba_ids>1000)
		{
			$sub_batch_no_cond_for_in=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$sub_batch_no_cond_for_in.=" aa.batch_no in($ids) or";

			}
			$sub_batch_no_cond_for_in=chop($sub_batch_no_cond_for_in,'or ');
			$sub_batch_no_cond_for_in.=")";
		}
		else
		{
			$sub_batch_no_cond_for_in=" and aa.batch_no in($baIds)";
		}

		 /*echo "<pre>";
		 print_r($self_date_data_arr);*/
		// $sql_subprod="select b.batch_id,b.receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $sub_batch_cond_for_in";
		/*
		 $sql_subprod2="select aa.batch_no,aa.result,max(aa.batch_ext_no) as batch_ext_no,b.batch_id,sum(b.product_qnty) as receive_qnty  from subcon_production_mst a,subcon_production_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.product_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=292 and aa.entry_form=38 $sub_batch_no_cond_for_in group by  aa.batch_no,aa.result,b.batch_id";
		$sub_fin_prod2=sql_select($sql_subprod2);
		foreach($sub_fin_prod2 as $row)
		{
			$batch_ext_no=$row[csf("batch_ext_no")];
			$subcon_fin_prod_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
		}*/

		$sql_subprod2="select a.product_no,b.batch_id,b.product_qnty as receive_qnty  from subcon_production_mst a,subcon_production_dtls b where a.id=b.mst_id  and b.product_qnty>0  and a.status_active=1 and a.is_deleted=0 and a.entry_form=292   and b.batch_id in (".implode(",",$sub_all_batch).")";
		$sub_fin_prod2=sql_select($sql_subprod2);
		foreach($sub_fin_prod2 as $row)
		{
			$batch_ext_no=$row[csf("batch_ext_no")];
			$subcon_fin_prod_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];

			$sub_fin_prod_qty_arr[$row[csf("batch_id")]]["recv_number"]=$row[csf("product_no")];
			$sub_fin_prod_qty_arr[$row[csf("batch_id")]]["receive_qnty"]+=$row[csf("receive_qnty")];


			$self_fin_prod_qty_arr[$row[csf("batch_no")]]["fin_qnty"]=$row[csf("receive_qnty")];

			$sub_fin_prod_arr_check[$sub_all_batch_data[$row[csf('batch_id')]]['batch_no']][$sub_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["fin_qnty"] +=$row[csf("receive_qnty")];
			$sub_fin_prod_arr_check[$sub_all_batch_data[$row[csf('batch_id')]]['batch_no']][$sub_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["result"] =$sub_all_batch_data[$row[csf('batch_id')]]['result'];
		}
		/*echo "<pre>";
		print_r($subcon_fin_prod_arr3);
		*/
		$subcon_rowspan_arr=array();$ttt=1;
		$subcon_func_batch_arr=array();$sub_trims_chk_batch_arr=array();
		$subc_mc_ut_qty_arr=array();$subcon_total_prod_qty=$summ_subcon_white_qty=$summ_subcon_wash_qty=$summ_subcon_color_qty=$summ_sub_re_pro_qty=$sub_summ_trims_wgt_batch_qty=0;
		$tot_sub_summ_normal_wash=$tot_sub_summ_aop_wash=0;
		foreach ($subcon_date_data_arr as $p_date=>$prod_date_data)
		{
			foreach ($prod_date_data as $machine_id => $machine_data)
			{
				foreach ($machine_data as $batch_id => $row)
				{
					//$subcon_fin_prod_sum=$subcon_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
					//$subcon_fin_prod_sum2=$subcon_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
					$subcon_fin_prod_sum3=$subcon_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
					/*if($row['batch_ext_no']==1)
					{
					$bext=$row['batch_ext_no'];
					}
					else*/ $bext=$row['batch_ext_no']-1;
					
				//	$bext=$row['batch_ext_no']-1;
					if(($bext*1)<=0) $bext='';
					
					

					if ($row['result']==1)
					{
						if ($row['color_range_id']==4)
						{
							$summ_subcon_white_qty += $row['batch_qnty'];
						}
						else if ($row['color_range_id']==7)
						{
							$summ_subcon_wash_qty += $row['batch_qnty'];
						}
						else $summ_subcon_color_qty += $row['batch_qnty'];

					}
					else if($row['result']==5 && $subcon_fin_prod_sum3=='')   // Shade match/Redyeing Needed/Fin Prod Available
					{
						if ($row['color_range_id']==4)
						{
							$summ_subcon_white_qty +=0;
						}
						else if ($row['color_range_id']==7)
						{
							$summ_subcon_wash_qty +=0;
						}
						else $summ_subcon_color_qty +=0;
					}
					else if($row['batch_ext_no']>0)
					{
						$summ_subcon_white_qty =0;
						$summ_subcon_wash_qty = 0;
						$summ_subcon_color_qty =0;
						//echo "B";
					}
					$sub_entry_form_wash=$sub_wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["wash_entry_form"];
					//if($sub_entry_form_wash==424)
					//{
						 $sub_normal_wash_qty=$sub_wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_normal"];
						$sub_aop_wash_qty=$sub_wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_aop"];
						//$summ_subcon_white_qty +=0;
						//$summ_subcon_wash_qty += 0;
						//$summ_subcon_color_qty +=0;
						$tot_sub_summ_normal_wash+=$sub_normal_wash_qty;
						$tot_sub_summ_aop_wash+=$sub_aop_wash_qty;
					//}
					if($sub_normal_wash_qty>0 || $sub_aop_wash_qty>0)
							{
							 $tot_prod=$sub_normal_wash_qty+$sub_aop_wash_qty;
							}
							else{
								 $tot_prod=$row['batch_qnty'];
							}

					if ($row['result']==1  && $row['batch_ext_no']=="")
					{
						$subcon_total_prod_qty+= $row['batch_qnty'];
						$subcon_actual_prod_qty= $row['batch_qnty'];
						if ($checkTrimWgtArr[$row['batch_id']]=='')
						{
							$total_summ_trim_wgt+=$row['total_trims_weight'];
						}
							//echo  $row['result'].'='.$re_process_qty3.'<br>';
					}
					else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
					{
						$subcon_total_prod_qty+= 0;
						$subcon_actual_prod_qty= 0;
					}
					if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
					{

						$subcon_total_prod_qty+= $row['batch_qnty'];
						$subcon_actual_prod_qty= $row['batch_qnty'];
						if ($checkTrimWgtArr[$row['batch_id']]=='')
						{
							$total_summ_trim_wgt+=$row['total_trims_weight'];
						}
					}
					if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
					{
						$summ_sub_re_pro_qty +=$row['batch_qnty'];// $row['batch_qty'];
						$is_finished[$row['batch_no']]=1;
					}
					if($is_finished[$row['batch_no']]==1) {$summ_sub_re_pro_qty +=$row['batch_qnty'];} else {$summ_sub_re_pro_qty +=0;}

					if($subcon_actual_prod_qty>0)
					{
						if(!in_array($batch_id,$sub_trims_chk_batch_arr))
						{
							$ttt++;

							$sub_trims_chk_batch_arr[]=$batch_id;
							$sub_summ_trims_wgt_batch_qty +=$row['total_trims_weight'];
							$sub_trimswgt =$row['total_trims_weight'];
						}
						else { $sub_summ_trims_wgt_batch_qty +=0;$sub_trimswgt =0;}
					}

					$summ_mc_total_qty+=$tot_prod+$sub_trimswgt;
					$subcon_trims_weight += $row['total_trims_weight'];
					//$subcon_total_prod_qty +=$row['batch_qnty']+$row['total_trims_weight'];
					//$subcon_total_prod_qty += $row['batch_qty']+$row['total_trims_weight'];// $subcon_color_qty+$subcon_white_qty+$subcon_pro_qty+$subcon_prod_qty+$subcon_trims_weight;
					$batch_qnty = $row['batch_qnty'];
					$subc_mc_ut_qty_arr[$p_date][$machine_id] += $batch_qnty+$row['total_trims_weight'];

					$subcon_total_batch+=count($row['batch_no']);
					$subcon_func_batch_arr[$row['system_no']]=$row['system_no'];
					$subcon_total_func_batch=count($subcon_func_batch_arr);

					if (isset($subcon_rowspan_arr[$p_date][$machine_id]))
					{
						$subcon_rowspan_arr[$p_date][$machine_id]+=1;
					}
					else
					{
						$subcon_rowspan_arr[$p_date][$machine_id]=1;
						$subcon_total_machine_capacity+=$row['prod_capacity'];
						$subcon_total_machine+=count($row['machine_no']);
					}
					$subcon_avg_batch_per_mc = $subcon_total_batch/$subcon_total_machine;
				}
			}
		}
		//echo '<pre>';
		//print_r($subcon_rowspan_arr);
	}

	if ($order_type==0 || $order_type==3) // Sample Without Order  sql and array
	{
		$sqls="SELECT  a.batch_id, a.batch_ext_no,a.process_id,a.load_unload_id, a.entry_form,a.machine_id,a.end_hours, a.end_minutes, a.process_end_date as prod_date, a.remarks,b.production_qty
		from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form in(35) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  order by a.batch_id";

		$load_time_array=array();
		foreach(sql_select($sqls) as $vals)
		{
			if($vals[csf("entry_form")]==35 && $vals[csf("load_unload_id")]==1)
			{
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_hours"]=$vals[csf("end_hours")];
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["load_end_minutes"]=$vals[csf("end_minutes")];
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["process_start_date"]=$vals[csf("prod_date")];
			$load_time_array[$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["remarks"]=$vals[csf("remarks")];
			}
			else if($vals[csf("entry_form")]==424)
			{
			
			$process_id=$vals[csf("process_id")];
			if($process_id==209) //AOP wash
			{
			$samp_wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_aop"]+=$vals[csf("production_qty")];
			}
			else if($process_id==193) //Normal Wash
			{
			$samp_wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["prod_qty_normal"]+=$vals[csf("production_qty")];
			}
			$samp_wash_qty_array[$vals[csf("prod_date")]][$vals[csf("machine_id")]][$vals[csf("batch_id")]][$vals[csf("batch_ext_no")]]["wash_entry_form"]=$vals[csf("entry_form")];
			}
		}
		/*echo '<pre>';
		print_r($load_time_array);die;*/

		// Main query
		$sql_result="(SELECT a.id, a.batch_no, a.batch_id,a.entry_form, a.batch_ext_no, a.machine_id,a.process_id as  prod_process_id, a.load_unload_id, a.end_hours, a.production_date, a.end_minutes,a.start_hours,a.start_minutes,a.process_start_date, a.result, a.remarks,a.process_end_date, a.water_flow_meter, a.system_no,a.incomplete_result,a.redyeing_needed,a.shade_matched, b.batch_qty,b.production_qty, b.const_composition,b.roll_no,b.barcode_no, c.machine_no, c.prod_capacity,
		p.buyer_id, q.color_id,q.double_dyeing, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
		from wo_non_ord_samp_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2
		and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order=1	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		
		union
		SELECT a.id, a.batch_no, a.batch_id,a.entry_form, a.batch_ext_no, a.machine_id,a.process_id as  prod_process_id, a.load_unload_id, a.end_hours, a.production_date, a.end_minutes,a.start_hours,a.start_minutes,a.process_start_date, a.result, a.remarks,a.process_end_date, a.water_flow_meter, a.system_no,a.incomplete_result,a.redyeing_needed,a.shade_matched, b.batch_qty,b.production_qty, b.const_composition,b.roll_no,b.barcode_no, c.machine_no, c.prod_capacity,
		p.buyer_id, q.color_id,q.double_dyeing, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
		from wo_non_ord_samp_booking_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
		where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
		and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=424 and q.entry_form=0  
		and a.batch_id=q.id and q.booking_no_id=p.id and q.booking_without_order=1
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0) order by process_end_date, machine_no";

		//echo $sql_result;die;

		$sql_dtls=sql_select($sql_result);
		//print_r($sql_dtls);
		$date_data_arr=array();
		$samp_fun_batch_rowspan_arr=array();
		$samp_batch_qty_arr=array();$samp_all_batch_id="";
		$sample_double_dying_qty_summ=0;
		foreach ($sql_dtls as $row)
		{

			$samp_all_batch_data[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$samp_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$samp_all_batch_data[$row[csf('batch_id')]]['result']=$row[csf('result')];

			$all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$samp_all_batch[$row[csf('batch_id')]]=$row[csf('batch_id')];
			$samp_all_batch_id.=$row[csf('batch_id')].',';
			$samp_all_batch_no.="'".$row[csf('batch_no')]."'".',';
			if($row[csf('entry_form')]==35)
			{
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_qty']+=$row[csf('production_qty')];
				//echo $row[csf('batch_qty')].'fff,';
			}
			else
			{
				$process_id=$row[csf("prod_process_id")];
				//echo $process_id.'D,';
				if($process_id==209) //AOP
				{
				//echo $row[csf("production_qty")].',';
					if($row[csf("production_qty")]>0)
					{
					$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_qty_aop']+=$row[csf('production_qty')];
					}
				}
				else if($process_id==193) //Normal 
				{
					if($row[csf("production_qty")]>0)
					{
						//echo $row[csf('production_qty')].',';
					$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_qty_normal']+=$row[csf('production_qty')];
					}
				}
				
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_start_hr']=$row[csf('start_hours')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_star_min']=$row[csf('start_minutes')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_end_hr']=$row[csf('end_hours')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_end_min']=$row[csf('end_minutes')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_prod_date']=$row[csf('production_date')];
				$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['wash_start_date']=$row[csf('process_start_date')];
				
			}
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['entry_form']=$row[csf('entry_form')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['machine_no']=$row[csf('machine_no')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['system_no']=$row[csf('system_no')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['p_date']=$row[csf('process_end_date')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
			
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['batch_ext_no']=$row[csf('batch_ext_no')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['load_unload_id']=$row[csf('load_unload_id')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_hours']=$row[csf('end_hours')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['unload_minutes']=$row[csf('end_minutes')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['production_end_date']=$row[csf('production_date')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['const_composition'].=$row[csf('const_composition')].',';
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['prod_capacity']=$row[csf('prod_capacity')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['water_flow_meter']=$row[csf('water_flow_meter')];

			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['total_trims_weight']=$row[csf('total_trims_weight')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['result']=$row[csf('result')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['incomplete_result']=$row[csf('incomplete_result')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['redyeing_needed']=$row[csf('redyeing_needed')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['shade_matched']=$row[csf('shade_matched')];
			$date_data_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]['process_id']=$row[csf('process_id')];

			if($subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=="")
			{
				$samp_fun_batch_rowspan_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('system_no')]]+=1;
				$subc_fun_batch_rowspan_arr_chk[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_id')]]=1;
			}
			$samp_batch_qty_arr[$row[csf('process_end_date')]][$row[csf('machine_id')]][$row[csf('batch_no')]]=$row[csf('batch_ext_no')];
			$machine_no_arr[$row[csf('machine_id')]]=$row[csf('machine_id')];
			if($row[csf('double_dyeing')]==1)//Double Dying Yes
			{
			$sample_double_dying_qty_summ+=$row[csf('batch_qty')];
			}
		}
		asort($date_data_arr);
		/*echo "<pre>";*/
		//print_r($machine_no_arr);die;
		$tot_machine_no=count($machine_no_arr);
		//echo $tot_machine_no.'D';
		$samp_all_batch_id=implode(",",array_unique(explode(",",$samp_all_batch_id)));
		$baIds=chop($samp_all_batch_id,','); $sam_batch_cond_for_in="";
		$ba_ids=count(array_unique(explode(",",$samp_all_batch_id)));
		if($db_type==2 && $ba_ids>1000)
		{
			$sam_batch_cond_for_in=" and (";
			$sam_batch_cond_for_in2=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$sam_batch_cond_for_in.=" b.batch_id in($ids) or";
				$sam_batch_cond_for_in2.=" q.id in($ids) or";
			}
			$sam_batch_cond_for_in=chop($sam_batch_cond_for_in,'or ');
			$sam_batch_cond_for_in.=")";
			$sam_batch_cond_for_in2=chop($sam_batch_cond_for_in2,'or ');
			$sam_batch_cond_for_in2.=")";
		}
		else
		{
			$sam_batch_cond_for_in=" and b.batch_id in($baIds)";
			$sam_batch_cond_for_in2=" and q.id in($baIds)";
		}
		 $sam_batch_sql_result="SELECT q.id, q.batch_no,b.id as dtls_id, b.batch_qnty,
		p.buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight,q.double_dyeing, q.process_id
		from pro_batch_create_mst q,pro_batch_create_dtls b,wo_non_ord_samp_booking_mst p
		where q.id=b.mst_id  and q.booking_no_id=p.id and q.booking_without_order=1 and q.entry_form=0     $buyer_name_cond $batch_color_cond $color_range_cond $sam_batch_cond_for_in2 	and q.status_active=1 and q.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and p.status_active=1 and p.is_deleted=0";
		$sql_batch_sam=sql_select($sam_batch_sql_result);
		foreach($sql_batch_sam as $row)
		{
			$samp_batch_qty_arr[$row[csf("id")]]+=$row[csf("batch_qnty")];
		}
		unset($sql_batch_sam);
	
		$samp_all_batch_no=implode(",",array_unique(explode(",",$samp_all_batch_no)));//samp_all_batch_no
		$baIds=chop($samp_all_batch_no,','); $sam_batch_no_cond_for_in="";
		$ba_ids=count(array_unique(explode(",",$samp_all_batch_no)));
		if($db_type==2 && $ba_ids>1000)
		{
			$sam_batch_no_cond_for_in=" and (";
			$baIdsArr=array_chunk(explode(",",$baIds),999);
			foreach($baIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$sam_batch_no_cond_for_in.=" aa.batch_no in($ids) or";
			}
			$sam_batch_no_cond_for_in=chop($sam_batch_no_cond_for_in,'or ');
			$sam_batch_no_cond_for_in.=")";
		}
		else
		{
			$sam_batch_no_cond_for_in=" and aa.batch_no in($baIds)";
		}
		// $sql_sam_prod="select b.batch_id,b.receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $sam_batch_cond_for_in";
		/*
		 $sql_sam_prod2="select aa.batch_no,aa.result,max(aa.batch_ext_no) as batch_ext_no,b.batch_id,sum(b.receive_qnty) as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_fab_subprocess aa where a.id=b.mst_id and aa.batch_id=b.batch_id and b.receive_qnty>0 and aa.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 $sam_batch_no_cond_for_in group by aa.batch_no,aa.result,b.batch_id,b.machine_no_id";
		$samp_fin_prod2=sql_select($sql_sam_prod2);
		foreach($samp_fin_prod2 as $row)
		{
			$batch_ext_no=$row[csf("batch_ext_no")];
			$samp_re_process_qty_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];
		}
		*/
		$sql_sam_prod2="select a.recv_number,b.batch_id,b.receive_qnty as receive_qnty,b.machine_no_id from inv_receive_master a,pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and b.receive_qnty>0 and a.status_active=1 and a.is_deleted=0 and a.entry_form=7 and b.batch_id in (".implode(",",$samp_all_batch).")";
		$samp_fin_prod2=sql_select($sql_sam_prod2);
		foreach($samp_fin_prod2 as $row)
		{
			$batch_ext_no=$row[csf("batch_ext_no")];

			$samp_fin_prod_qty_arr[$row[csf("batch_id")]]["recv_number"]=$row[csf("recv_number")];
			$samp_fin_prod_qty_arr[$row[csf("batch_id")]]["receive_qnty"]+=$row[csf("receive_qnty")];

			$samp_re_process_qty_arr3[$row[csf("batch_no")]][$batch_ext_no]["fin_qnty"]=$row[csf("receive_qnty")];

			$samp_fin_prod_arr_check[$samp_all_batch_data[$row[csf('batch_id')]]['batch_no']][$samp_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["fin_qnty"] +=$row[csf("receive_qnty")];
			$samp_fin_prod_arr_check[$samp_all_batch_data[$row[csf('batch_id')]]['batch_no']][$samp_all_batch_data[$row[csf('batch_id')]]['batch_ext_no']]["result"] =$samp_all_batch_data[$row[csf('batch_id')]]['result'];
		}
		$rowspan_arr=array();$tttt=1;
		$smp_mc_ut_qty_arr=array();$samp_trims_chk_batch_arr=array();$sample_total_prod_qty=$samp_summ_re_pro_qty=$samp_summ_re_pro_qty=$samp_summ_re_pro_qty=$samp_summ_re_pro_qty=0;
		$samp_rowspan_arr=array();$tot_samp_summ_normal_wash=$tot_samp_summ_aop_wash=0;
		foreach ($date_data_arr as $p_date=>$prod_date_data)
		{
			$samp_rowspan=0;
			foreach ($prod_date_data as $machine_id => $machine_data)
			{
				foreach ($machine_data as $batch_id => $row)
				{
					$samp_fin_prod_summ=$samp_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
					$samp_fin_prod_summ3=$samp_re_process_qty_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
					$sample_trims_weight = $row['total_trims_weight'];
					//$sample_total_prod_qty+= $row['batch_qty']+$row['total_trims_weight'];// $sample_color_qty+$sample_white_qty+$sample_pro_qty+$sample_prod_qty+$sample_trims_weight;
					//echo $sample_total_prod_qty.'DDD';
					 $entry_form_id =$row['entry_form'];
					$sam_prod_qty_aop=$sam_prod_qty_normal=0;
					//if($entry_form_id==424)
					//{
						$tot_samp_summ_normal_wash+=$row['prod_qty_normal'];
						$sam_prod_qty_normal=$row['prod_qty_normal'];
						$tot_samp_summ_aop_wash+=$row['prod_qty_aop'];
						$sam_prod_qty_aop=$row['prod_qty_aop'];
					// }
					
					 $sam_tot_aop_qty=$sam_prod_qty_normal+$sam_prod_qty_aop;
					// echo $sam_tot_aop_qty.'='.$entry_form_id.', ';
					  $row['batch_qty']=0;
					  $row['batch_qty']=$row['production_qty'];
					  if($sam_tot_aop_qty>0)
					  {
						  $batch_qty =$sam_tot_aop_qty;
					  }
					  else
					  {
						  $batch_qty =$row['batch_qty'];
					  }
						
					/*if($row['batch_ext_no']==1)
					{
					$bext=$row['batch_ext_no'];
					}
					else*/ $bext=$row['batch_ext_no']-1;
					
					//$bext=$row['batch_ext_no']-1;
					if(($bext*1)<=0) $bext='';
					
					
					if ($row['result']==1)
					{
						if ($row['color_range_id']==4)
						{
							$self_summ_white_qty += $row['batch_qty'];
						}
						else if ($row['color_range_id']==7)
						{
							$self_summ_wash_qty += $row['batch_qty'];
						}
						else $self_summ_color_batch_qty += $row['batch_qty'];

					}
					else if ($row['result']==5 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=="") // //Under Trial//Fin Fab not Avaiable
					{
						if ($row['color_range_id']==4)
						{
							$self_summ_white_qty +=0;
						}
						else if ($row['color_range_id']==7)
						{
							$self_summ_wash_qty +=0;
						}
						else $self_summ_color_batch_qty +=0;
					}
					else if($row['batch_ext_no']>0)
					{
						$self_summ_white_qty +=0;
						$self_summ_wash_qty += 0;
						$self_summ_color_batch_qty +=0;
					}
					$samp_entry_form_wash=$samp_wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["wash_entry_form"];
				//	echo $samp_entry_form_wash.'';
					 if($samp_entry_form_wash==424)
					{
						// $samp_normal_wash_qty=$samp_wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_normal"];
						//$samp_aop_wash_qty=$samp_wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["prod_qty_aop"];
					//	$self_summ_white_qty +=0;
						//$self_summ_wash_qty += 0;
					//	$self_summ_color_batch_qty +=0;
						//$tot_samp_summ_normal_wash+=$samp_normal_wash_qty;
						//$tot_samp_summ_aop_wash+=$samp_aop_wash_qty;
					}
					
					
					if ($row['result']==1 && $row['batch_ext_no']=="")
					{
						$sample_total_prod_qty+= $row['batch_qty'];
						$samp_actual_prod_qty=$row['batch_qty'];
						if ($checkTrimWgtArr[$row['batch_id']]=='')
						{
							$total_summ_trim_wgt+=$row['total_trims_weight'];
						}
							//echo  $row['result'].'='.$re_process_qty3.'<br>';
					}
					else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
					{
						$sample_total_prod_qty+= 0;
						$samp_actual_prod_qty=0;
					}
					if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
					{
						$sample_total_prod_qty+= $row['batch_qty'];
						$samp_actual_prod_qty=$row['batch_qty'];
						if ($checkTrimWgtArr[$row['batch_id']]=='')
						{
							$total_summ_trim_wgt+=$row['total_trims_weight'];
						}
					}

					
					if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
					{
						$self_samp_re_pro_qty =$row['batch_qty'];// $row['batch_qty'];
						$is_finished[$row['batch_no']]=1;
					}
					if($is_finished[$row['batch_no']]==1) {$self_samp_re_pro_qty =$row['batch_qty'];} else {$self_samp_re_pro_qty =0;}
					
					$self_summ_re_pro_qty+=$self_samp_re_pro_qty;
					if($samp_actual_prod_qty>0)
					{
						if(!in_array($batch_id,$samp_trims_chk_batch_arr))
						{
							$tttt++;
							$samp_trims_chk_batch_arr[]=$batch_id;
							$self_summ_trims_wgt_batch_qty+=$row['total_trims_weight'];
							$samp_trimswgt =$row['total_trims_weight'];
						}
						else { $self_summ_trims_wgt_batch_qty+=0;$samp_trimswgt =0;}
					}
					$summ_mc_total_qty+=$batch_qty+$samp_trimswgt;

					$smp_batch_qty = $row['batch_qty'];
					$smp_mc_ut_qty_arr[$p_date][$machine_id] += $smp_batch_qty+$row['total_trims_weight'];
					$sample_total_batch+=count($row['batch_no']);
					$sample_func_batch_arr[$row['system_no']]=$row['system_no'];
					$sample_total_func_batch=count($sample_func_batch_arr);

					$samp_rowspan++;

					if (isset($rowspan_arr[$p_date][$machine_id]))
					{
						$rowspan_arr[$p_date][$machine_id]++;
					}
					else
					{
						$rowspan_arr[$p_date][$machine_id]=1;
						$sample_total_machine_capacity+=$row['prod_capacity'];
						$sample_total_machine+=count($row['machine_no']);
					}
					$sample_avg_batch_per_mc = $sample_total_batch/$sample_total_machine;
				}
				$samp_rowspan_arr[$p_date][$machine_id][$row['system_no']]=$samp_rowspan;
			}
		}
		//echo '<pre>';
		//print_r($samp_rowspan_arr);
	}

	// mc idle sql
	$mc_idle = "SELECT a.machine_entry_tbl_id, a.machine_no, a.from_date, a.from_hour, a.from_minute, a.to_date, a.to_hour, a.to_minute, a.machine_idle_cause, a.remarks, b.batch_id
	from pro_cause_of_machine_idle a, pro_fab_subprocess b
	where a.machine_entry_tbl_id=b.machine_id and a.status_active=1 and a.is_deleted=0 $idle_sql_cond";

	//echo $mc_idle;die;

	$mc_idle_result=sql_select($mc_idle);
	$idle_data_arr=array();
	foreach ($mc_idle_result as $rows)
	{
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine"]=$rows[csf("machine_entry_tbl_id")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_no"]=$rows[csf("machine_no")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_date"]=$rows[csf("from_date")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_hour"]=$rows[csf("from_hour")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["from_minute"]=$rows[csf("from_minute")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_date"]=$rows[csf("to_date")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_hour"]=$rows[csf("to_hour")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["to_minute"]=$rows[csf("to_minute")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["machine_idle_cause"]=$rows[csf("machine_idle_cause")];
		$idle_data_arr[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]]["remarks"]=$rows[csf("remarks")];
		
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine"]=$rows[csf("machine_entry_tbl_id")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine_no"]=$rows[csf("machine_no")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_date"]=$rows[csf("from_date")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_hour"]=$rows[csf("from_hour")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["from_minute"]=$rows[csf("from_minute")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_date"]=$rows[csf("to_date")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_hour"]=$rows[csf("to_hour")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["to_minute"]=$rows[csf("to_minute")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["machine_idle_cause"]=$rows[csf("machine_idle_cause")];
		$idle_data_arr2[$rows[csf("from_date")]][$rows[csf("machine_entry_tbl_id")]][$rows[csf("machine_idle_cause")]]["remarks"]=$rows[csf("remarks")];
	}
	/*echo '<pre>';
	print_r($idle_data_arr);die;*/
		//Chemical and Dyes issue cost sql
	//$batch_id_string = "'".implode ( "', '",$all_batch)."'";
	//if($batch_id_string!="") $issue_batch_cond=" and b.batch_id in($batch_id_string)";

	$all_batch_id="'".implode ( "', '",array_unique($all_batch))."'";
	$issue_batch_cond="";
	$all_batch_id_arr=explode(",", trim($all_batch_id));
	if($db_type==2 && count($all_batch_id_arr)>999)
	{
		$chunk_arr=array_chunk($all_batch_id_arr, 999);
		foreach($chunk_arr as $keys=>$vals)
		{
			$po_ids=implode(",", $vals);
			if($issue_batch_cond=="")
			{
				$issue_batch_cond.=" and ( b.batch_id in ($po_ids) ";
			}
			else
			{
				$issue_batch_cond.=" or b.batch_id in ($po_ids) ";
			}
		}
		$issue_batch_cond.=" ) ";
	}
	else
	{
		$issue_batch_cond=" and b.batch_id in (".$all_batch_id.")";
	}
	//echo $issue_batch_cond;die;

	$sql_cost="SELECT a.mst_id, b.batch_id, b.item_category, a.cons_amount from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id and b.item_category in(5,6) and a.company_id=$cbo_company_id and b.is_deleted=0 and b.status_active=1 $issue_batch_cond";

	//echo $sql_cost;
	$cost_data_arr=array();
	$sql_res=sql_select($sql_cost);
	foreach ($sql_res as $row)
	{
		$all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
		$cost_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
	}
	/*echo '<pre>';
	print_r($cost_data_arr);die;*/
	//Chemical and Dyes return sql
	$uniq_issue_id=array_unique($all_issue_id);

	$all_issue_id="'".implode ( "', '",array_unique($all_issue_id))."'";
	$uniq_issue_id_cond="";
	$all_issue_id_arr=explode(",", trim($all_issue_id));
	if($db_type==2 && count($all_issue_id_arr)>999)
	{
		$chunk_arr=array_chunk($all_issue_id_arr, 999);
		foreach($chunk_arr as $keys=>$vals)
		{
			$po_ids=implode(",", $vals);
			if($uniq_issue_id_cond=="")
			{
				$uniq_issue_id_cond.=" and ( a.issue_id in ($po_ids) ";
			}
			else
			{
				$uniq_issue_id_cond.=" or a.issue_id in ($po_ids) ";
			}
		}
		$uniq_issue_id_cond.=" ) ";
	}
	else
	{
		$uniq_issue_id_cond=" and a.issue_id in (".$all_issue_id.")";
	}
	//echo $uniq_issue_id_cond;die;

	if(count($uniq_issue_id)>0)
	{
		$sql_return_cost="SELECT p.batch_no as batch_id, a.item_category, a.cons_amount
		from inv_issue_master p, inv_transaction a, inv_receive_master b
		where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6) and a.company_id=$cbo_company_id and b.is_deleted=0 and b.status_active=1 $uniq_issue_id_cond";
		//echo $sql_return_cost;die;
		$cost_return_data_arr=array();
		$sql_return_res=sql_select($sql_return_cost);
		foreach ($sql_return_res as $row)
		{
			$cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
		}
	}

	/*echo '<pre>';
	print_r($cost_return_data_arr);die;*/
	?>
	<div style="width: 2160px;">
		<table width="2010" cellpadding="0" cellspacing="0" id="caption" align="center">
			<tr>
				<td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:16px"><? echo $company_library[$cbo_company]; ?></strong></td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="6" class="form_caption" ><strong style="font-size:13px"><? echo $report_title;  echo ";<br> From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
			</tr>
		</table>

		<style>
			#tdtag
			{
				border: 1px solid #8DAFDA;
			}
		</style>

		<div style="margin-left:6px;">
			<table width="600"   border="0" cellpadding="0" cellspacing="0" >
				<tr>
					<td>
						<table width="200" id="tdtag" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<thead>
								<tr>
									<th colspan="2" id="tdtag" style="text-align: center;"><strong>Production Qty in KG Summary</strong></th>
								</tr>
								<tr>
									<td id="tdtag" style="background: #E9F3FF;"><strong>Total Color </strong></td>
									<td id="tdtag" style="background: #E9F3FF;"><strong><?php
									$tot_summ_color_batch_qty=$self_summ_color_batch_qty+$summ_subcon_color_qty;
									echo number_format($tot_summ_color_batch_qty,2,'.',''); ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #FFFFFF;"><strong>Total White</strong></td>
									<td id="tdtag" style="background: #FFFFFF;"><strong><?php
									$tot_summ_white_qty=$self_summ_white_qty+$summ_subcon_white_qty;
									echo  number_format($tot_summ_white_qty,2,'.',''); ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #E9F3FF;"><strong>Total Wash (Y/D)</strong></td>
									<td id="tdtag" style="background: #E9F3FF;"><strong><?php
									$tot_summ_wash_qty=$self_summ_wash_qty+$summ_subcon_wash_qty;
									echo number_format($tot_summ_wash_qty,2,'.',''); ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #FFFFFF;"><strong>Total Re-Process</strong></td>
									<td id="tdtag" style="background: #FFFFFF;"><strong><?php
									$tot_summ_re_pro_qty=$self_summ_re_pro_qty+$summ_sub_re_pro_qty;
									echo number_format($tot_summ_re_pro_qty,2,'.','');
									?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #FFFFFF;" title="Total Machine/Total Functional Batch"><strong>Total Trims Weight</strong></td>
									<td id="tdtag" style="background: #FFFFFF;"><strong><?php
									$tot_summ_trims_wgt_batch_qty=$self_summ_trims_wgt_batch_qty+$sub_summ_trims_wgt_batch_qty;	echo number_format($tot_summ_trims_wgt_batch_qty,2,'.','');
									?></strong></td>

								</tr>
								<tr>
									<td id="tdtag" style="background: #FFFFFF;" title="Total Machine/Total Functional Batch"><strong>Double Dying Qty:</strong></td>
									<td id="tdtag" style="background: #FFFFFF;" title="Self=<? echo $self_double_dying_qty_summ.',Subcon='.$subcon_double_dying_qty_summ.'Sample='.$sample_double_dying_qty_summ?>"><strong><?php
									$tot_summ_double_dying_batch_qty=$sample_double_dying_qty_summ+$subcon_double_dying_qty_summ+$self_double_dying_qty_summ;
										echo number_format($tot_summ_double_dying_batch_qty,2,'.','');
									?></strong></td>
								</tr>
							</thead>
						</table>
					</td>
					<td width="10" height="5">
					</td>
					<td>
						<table width="300"  style=" margin:5px;" id="tdtag"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<thead>
								<tr>
									<th colspan="2" id="tdtag" style="text-align: center;"><strong>Summary</strong></th>
								</tr>
								<tr>
									<td id="tdtag" style="background: #E9F3FF;"><strong>Total Actual Production </strong></td>
									<td id="tdtag" style="background: #E9F3FF;"><strong><?php
									$tot_actual_prod_qty=$tot_self_actual_prod_qty+$subcon_total_prod_qty+$sample_total_prod_qty+$total_summ_trim_wgt;
									echo number_format($tot_actual_prod_qty,2); //$self_prod_qty+$subcon_total_prod_qty+$sample_total_prod_qty; ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #E9F3FF;"><strong>Total Machine Production </strong></td>
									<td id="tdtag" style="background: #E9F3FF;"><strong><?php

									echo number_format($summ_mc_total_qty,2); ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #FFFFFF;"><strong>Total Machine Capacity</strong></td>
									<td id="tdtag" style="background: #FFFFFF;"><strong><?php echo number_format($self_total_machine_capacity+$subcon_total_machine_capacity+$sample_total_machine_capacity,2); ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #E9F3FF;"><strong>Total Machine</strong></td>
									<td id="tdtag" style="background: #E9F3FF;"><strong><?php echo $tot_machine_no;//$self_total_machine+$subcon_total_machine+$sample_total_machine; ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #FFFFFF;"><strong>Total Functional Batch</strong></td>
									<td id="tdtag" style="background: #FFFFFF;"><strong><?php
									$tot_functional_batch=$tot_self_func_batch_arr+$subcon_total_func_batch+$sample_total_func_batch;
									echo number_format($tot_functional_batch,2,'.','');
									?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #FFFFFF;" title="Total Machine/Total Functional Batch"><strong>Avg Batch Per M/C</strong></td>
									<td id="tdtag" style="background: #FFFFFF;"><strong><?php
							echo number_format($tot_functional_batch/$tot_machine_no,2,'.','');//number_format($self_avg_batch_per_mc+$subcon_avg_batch_per_mc+$sample_avg_batch_per_mc,2,'.','');
							?></strong></td>
						</tr>
					</thead>
				</table>
			</td>
            
            <td>
						<table width="300"  style=" margin:5px;" id="tdtag"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
							<thead>
								<tr>
									<th colspan="2" id="tdtag" style="text-align: center;"><strong>AOP Summary</strong></th>
								</tr>
								<tr>
									<td id="tdtag" style="background: #E9F3FF;"><strong>Total Normal Wash </strong></td>
									<td id="tdtag" style="background: #E9F3FF;"><strong><?php
									$tot_normal_wash_qty=$tot_self_summ_normal_wash+$tot_sub_summ_normal_wash+$tot_samp_summ_normal_wash;
									//$tot_sub_aop_wash_qty=$tot_sub_summ_normal_wash+$tot_sub_summ_aop_wash;
									echo number_format($tot_normal_wash_qty,2); //$self_prod_qty+$subcon_total_prod_qty+$sample_total_prod_qty; ?></strong></td>
								</tr>
								<tr>
									<td id="tdtag" style="background: #E9F3FF;"><strong>Total AOP Wash </strong></td>
									<td id="tdtag" style="background: #E9F3FF;"><strong><?php
									$tot_aop_wash_qty=$tot_self_summ_aop_wash+$tot_sub_summ_aop_wash+$tot_samp_summ_aop_wash;

									echo number_format($tot_aop_wash_qty,2); ?></strong></td>
								</tr>
								
								<tr>
									<td id="tdtag" style="background: #FFFFFF;" title="Total Machine/Total Functional Batch"><strong>Grand Total</strong></td>
									<td id="tdtag" style="background: #FFFFFF;"><strong><?php echo number_format($tot_normal_wash_qty+$tot_aop_wash_qty,2);?></strong></td>
						</tr>
					</thead>
				</table>
			</td>
		</tr>
	</table>
	</div>

	<?

	if ($order_type==0 || $order_type==1) // Self Order
	{
		?>
		<div>
			<table width="2570" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th colspan="31" style="text-align: left;">Self Order</th>
					</tr>
				</thead>
			</table>
			<table width="2570" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL No</th>
						<th rowspan="2" width="100">M/C & Capacity</th>
						<th rowspan="2" width="80">Functional Batch No.</th>
						<th rowspan="2" width="100">Buyer Name</th>
						<th rowspan="2" width="80">Batch No.</th>
						<th rowspan="2" width="100">Batch Color</th>
						<th rowspan="2" width="120">Booking No.</th>
						<th rowspan="2" width="80">Batch Qty</th>
						<th rowspan="2" width="50">Ext. No</th>
						<th rowspan="2" width="100">Color Range</th>
						<th colspan="7" >Total Production Qty in Kg</th>
						<th rowspan="2" width="70">MC Total Production</th>
						<th rowspan="2" width="70">Actual Production</th>
						<th rowspan="2" width="70"><p>Incomplete Production</p></th>
						<th rowspan="2" width="110">Result</th>
						<th rowspan="2" width="70">Water/kg in Ltr</th>
						<th rowspan="2" width="70">M/C UT%</th>
						<th rowspan="2" width="80">Loading Time</th>
						<th rowspan="2" width="80">Unloading Time</th>
						<th rowspan="2" width="80">Total Time (Hour)</th>
						<th rowspan="2" width="70"><p>Chemical Cost</p></th>
						<th rowspan="2" width="70">Dyes Cost</th>
						<th rowspan="2" width="70">Total Cost</th>
						<th rowspan="2" width="">Fabric Construction</th>

						<th rowspan="2"  width="100">Remarks</th>
					</tr>
					<tr>
						<th width="60" title="Data is showing, according to [Color Range] - Average Color, from batch creation page.">Color</th>
						<th width="60" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Normal Wash</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">AOP Wash</th>
						
						<th width="60">Re-Process</th>
						<th width="60">Trims Weight</th>
					</tr>
				</thead>
			</table>
			<div style="width:2588px; overflow-y:scroll; max-height:300px;" id="scroll_body">
				<table cellspacing="0" width="2570"  border="1" rules="all" class="rpt_table" >

					<?
					$i=1;
					$other_grnd_total=$white_grnd_total=$wash_grnd_total=$normal_wash_grnd_total=$aop_wash_grnd_total=$pro_qty_grnd_total=$trims_weight_grnd_total=$grnd_total_batch=$grnd_total_self_functional_batch=$self_production_grnd_total=$incomplete_qty_grnd_total=$self_actual_production_grnd_total=0;
					$array_check=array();

					foreach ($self_date_data_arr as $p_date=>$prod_date_data)
					{
						$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$normal_wash_date_wise_total=$aop_wash_date_wise_total=$pro_qty_date_wise_total=$trims_weight_date_wise_total=$date_wise_total_batch=$date_wise_self_functional_batch=$date_wise_total_self_production=$date_wise_total_self_actual_production=$date_wise_total_self_incomplete_qty=0;
						$total_batch=0;
						?>
						<tr>
							<td colspan="31" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
						</tr>
						<?
						foreach ($prod_date_data as $machine_id => $machine_data)
						{
							$other_mcw_wise_total=$white_mcw_wise_total=$wash_mcw_wise_total=$normal_wash_mcw_wise_total=$aop_wash_mcw_wise_total=$total_pro_qty=$total_trims_weight=$mcw_wise_total_batch=$total_self_production_qty=$total_self_incomplete_qty=$total_actual_prod_qty=0;
							$self_functional_batch_arr=array();
							$r=0;
							foreach ($machine_data as $batch_id => $row)
							{
						//echo 'Test '.$batch_qty_arr[$p_date][$machine_id][$row['batch_no']].'<br>';
					/*$prod_fin_qnty=$self_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
					$prod_fin_qnty2=$self_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
					$re_process_qty=$self_re_process_qty_arr[$row['batch_no']][$row['result']]['re_process_qty'];*/
					$self_batch_qty=$self_batch_qty_arr[$batch_id];
					 $row['batch_qty']=0;//$row['production_qty']=0;
					 $row['batch_qty']=$row['production_qty'];//$self_batch_qty_arr[$batch_id];
					$re_process_qty3=$self_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
					$self_fin_prod_check=$self_fin_prod_arr_check[$row[csf("batch_no")]][$row['batch_ext_no']]["fin_qnty"];

					$recv_number=$self_fin_prod_qty_arr[$batch_id]["recv_number"];
					$self_receive_qnty=$self_fin_prod_qty_arr[$batch_id]["receive_qnty"];
					if($self_receive_qnty>0) $batch_color_td="green";else  $batch_color_td="";
					
					//=======***==============
					
					
					//echo $p_date.'='.$machine_id.'='.$batch_id.', ';

					//echo $row[('batch_ext_no')].'='.$prod_fin_qnty2.'<br>'; $batch_ext_no
					//$re_pro_qty = $re_process_qty;
					//echo $self_fin_prod_check."=";
						//echo $self_fin_prod_check_main=$self_fin_prod_arr_check[$row[csf("batch_no")]]['']["fin_qnty"];
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
						<?
						if ($r==0)
						{
							?>
							<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
							{
								$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
								echo $self_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
							}
							else
							{
								echo $self_rowspan_arr[$p_date][$machine_id];
							}
							?>" width="30" valign="middle"><? echo $i;?></td>
							<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
							{
								$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
								echo $self_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
							}
							else
							{ 
								$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
								echo $self_rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
							}
							?>" width="100"  valign="middle"> <div style="word-wrap:break-word; width:100px;"><? echo $row['machine_no'].'-'.$row['prod_capacity'].'kg';?> </div></td>
							<?
						} $r++;

								//if($arrs[$p_date][$machine_id][$row['system_no']]=="")
								//{
						?>
						<td rowspan="<? //echo $fun_batch_rowspan_arr[$p_date][$machine_id][$row['system_no']] ?>" width="80" valign="middle" style="word-wrap:break-word; word-break: break-all;"><? echo $functional_batch=$row['system_no']; $self_functional_batch_arr[$row['system_no']]=$row['system_no']; ?></td>
						<?
								//	$arrs[$p_date][$machine_id][$row['system_no']]=$row['system_no'];
								//}
						if($row['batch_ext_no']>0) $extBgcolor = "yellow";else $extBgcolor="";
						?>
						<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $buyer_library[$row['buyer_id']];?></div></td>
						<td width="80" valign="middle" align="center" title="Recv No=<? echo $recv_number.',Fin Qty='.$self_receive_qnty; ?>" style=" word-break:break-all"><p>
                        <b style=" color:<? echo $batch_color_td;?>"><? echo $row['batch_no'];

						$mcw_wise_total_batch+=count($row['batch_no']); ?></b></td>
						<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $color_library[$row['color_id']]?></div></td>
						<td width="120" valign="middle"><div style="word-wrap:break-word; width:120px;"><? echo $row['booking_no'];?></div></td>
						<td width="80" valign="middle" title="Fabric + Trims " align="right"><? echo number_format($self_batch_qty+$row['total_trims_weight'],2,'.','');?></td>
						<td width="50" valign="middle" bgcolor="<? echo $extBgcolor ;?>" align="center"><? echo $row['batch_ext_no'];?></td>
						<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $color_range[$row['color_range_id']];?></div></td>
						<?
						$avarage_batch_qty=$white_batch_qty=$wash_batch_qty=$actual_prod_qty=$re_pro_qty=$other_qty=0;
							//if (($row['result']==1 || $row['result']==2)  && $re_process_qty3=='')  //Shade match/Redyeing Needed/Fin Prod not Available
						/*if($row['batch_ext_no']==1)
						{
						$bext=$row['batch_ext_no'];
						}
						else */$bext=$row['batch_ext_no']-1;
						//$bext=$row['batch_ext_no']-1;
						if(($bext*1)<=0) $bext='';

							$normal_wash_qty=$aop_wash_qty=0;
							
							 if ($row['result']==1) //Shade match
							{
								if ($row['color_range_id']==4)
								{
									$white_batch_qty = $row['batch_qty'];
								}
								else if ($row['color_range_id']==7)
								{
									$wash_batch_qty = $row['batch_qty'];
								}
								else $avarage_batch_qty = $row['batch_qty'];

							}
							else if ($row['result']==5  &&  $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=='')  //Under Trial/Fin Prod not Available
							{
								if ($row['color_range_id']==4)
								{
									$white_batch_qty =0;
								}
								else if ($row['color_range_id']==7)
								{
									$wash_batch_qty = 0;
								}
								else $avarage_batch_qty =0;
							}
							else if($row['batch_ext_no']>0)
							{
								$white_batch_qty =0;
								$wash_batch_qty = 0;
								$avarage_batch_qty =0;
								//echo "B";
							}
							$entry_form_wash=$wash_qty_array[$p_date][$machine_id][$batch_id][$row['batch_ext_no']]["wash_entry_form"];
							$normal_wash_qty=$row['prod_qty_normal'];
							$aop_wash_qty=$row['prod_qty_aop'];
							$wash_prod_date=$row['wash_prod_date'];
							$wash_start_date=$row['wash_start_date'];
								//echo $entry_form_wash.'X='.$wash_start_date.',';
						
							//if (($row['result']==1 || $row['result']==2) && $re_process_qty3>0)  //Shade match/Redyeing Needed/Fin Prod Available
							if ($row['result']==1  && $row['batch_ext_no']=="")
							{
								$actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
							}
							else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available //else if (($row['result']==1 || $row['result']==2) && ($row['redyeing_needed']==2 || $row['shade_matched']==1) && $re_process_qty3=='')//Shade match/Redyeing Needed/Fin Prod not Available
							{
								$actual_prod_qty = 0;
							}
							if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
							{
								$actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
							}
						//if (($row['result']==1 || $row['result']==2) && $row['batch_ext_no']>0 && $re_process_qty3>0 ) //Shade match/Redyeing Needed/Re Process /Fin Prod Available
							if ( $row['result']==1 && $self_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
							{
								$re_pro_qty =$row['batch_qty'];// $row['batch_qty'];
								$is_fnished[$row['batch_no']]=1;
							}
							if($is_fnished[$row['batch_no']]==1) $re_pro_qty =$row['batch_qty'];// $row['batch_qty'];
							if($row['incomplete_result']==4)
							{
								$self_incomplete_qty=$row['batch_qty']+$row['total_trims_weight'];
							}
							else $self_incomplete_qty=0;

							$total_self_incomplete_qty+=$self_incomplete_qty;//$self_incomplete_qty;
							if($actual_prod_qty>0) $trims_weight=$row['total_trims_weight']; else $trims_weight="";
							?>
							<td width="60" valign="middle" align="right"><? echo number_format($avarage_batch_qty,2,'.',''); $other_mcw_wise_total+=$avarage_batch_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($white_batch_qty,2,'.',''); $white_mcw_wise_total+=$white_batch_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($wash_batch_qty,2,'.',''); $wash_mcw_wise_total+=$wash_batch_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($normal_wash_qty,2,'.',''); $normal_wash_mcw_wise_total+=$normal_wash_qty;?></td>
							<td width="60" valign="middle" align="right" title="<? echo $machine_id.'='.$batch_id;?>"><? echo number_format($aop_wash_qty,2,'.',''); $aop_wash_mcw_wise_total+=$aop_wash_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($re_pro_qty,2,'.',''); $total_pro_qty+=$re_pro_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($trims_weight,2,'.',''); $total_trims_weight+=$trims_weight;?></td>
							<td width="70" valign="middle" align="right"><? 
							if($normal_wash_qty>0 || $aop_wash_qty>0)
							{
								 $total_self_production=$aop_wash_qty+$normal_wash_qty;
							}
							else
							{
								 $total_self_production=$row['batch_qty']+$row['total_trims_weight'];
							}
							echo number_format($total_self_production,2,'.','');//($avarage_batch_qty+$white_batch_qty+$wash_batch_qty+$re_pro_qty+$trims_weight);
							$total_self_production_qty+=$total_self_production; ?></td>
							<td width="70" valign="middle" align="right"><? echo number_format($actual_prod_qty,2,'.',''); $total_actual_prod_qty+=$actual_prod_qty;?></td>
							<td width="70" valign="middle" align="right"><? echo number_format($self_incomplete_qty,2,'.','');//if($row['incomplete_result']==4) echo $total_self_incomplete_qty=$row['batch_qty']; $total_self_incomplete_qty+=$self_incomplete_qty; ?></td>
							<? if($row['result']==1) { $resultBgcolor = "green";} else if($row['result']==2) {$resultBgcolor = "yellow";} else{$resultBgcolor = "#FFFFFF";} ?>
							<td width="110" valign="middle" bgcolor="<? echo $resultBgcolor; ?>"> <div style="word-wrap:break-word; width:110px;"><? echo $dyeing_result[$row['result']];?></div></td>
							<td width="70" valign="middle" align="right"><? echo $row['water_flow_meter'];?></td>
							<?
							$total_btch_qty = $row['batch_qty']+$row['total_trims_weight'];
							$mc_capacity = $row['prod_capacity'];
							$mc_ut = ($mc_ut_qty_arr[$p_date][$machine_id]*100)/$mc_capacity;
							$mc_ut_result = ($mc_ut*$total_btch_qty)/$mc_ut_qty_arr[$p_date][$machine_id];
							?>
							<td width="70" valign="middle"><? echo number_format($mc_ut_result,2,'.','');?></td>
							<?
							$unload_hour='';$unload_minut='';
							if($normal_wash_qty>0 || $aop_wash_qty>0)
							{
								$load_hour = $row['wash_start_hr'];
								$load_minut = $row['wash_star_min'];
								$start_time = $load_hour.'.'.$load_minut;
								$process_start_date = $row['wash_start_date'];
								//$process_end_dateIs = $row['wash_prod_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$load_hour_minut = date('h:i A', strtotime($start_time));
								
								//end time
								$unload_hour = $row['wash_end_hr'];
								$unload_minut = $row['wash_end_min'];
								//echo $unload_hour.'='.$unload_minut.', ';
								$process_end_dateIs = $row['wash_prod_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$unload_hour_minut = date('h:i A', strtotime($end_time));
								
								$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
								$prod_start_date_time=strtotime($start_date_time);

								$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
								$prod_end_date_time=strtotime($end_date_time);

								$diff = ($prod_end_date_time - $prod_start_date_time);
								$total = $diff/60;
								$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
								
							}
							else
							{
								$load_hour = $self_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $self_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
								$process_start_date = $self_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
								$start_time = $load_hour.'.'.$load_minut;
								$load_hour_minut = date('h:i A', strtotime($start_time));
								
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$process_end_dateIs = $row['production_end_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$unload_hour_minut = date('h:i A', strtotime($end_time));
								
								$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
								$prod_start_date_time=strtotime($start_date_time);

								$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
								$prod_end_date_time=strtotime($end_date_time);

								$diff = ($prod_end_date_time - $prod_start_date_time);
								$total = $diff/60;
								$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
							}
							?>
							<td width="80" valign="middle"><? echo $load_hour_minut;?></td>
							<?
							
							?>
							<td width="80" valign="middle"><div style="word-wrap:break-word; width:80px;"><? echo $unload_hour_minut; ?></div></td>
							<?
							
							?>
							<td width="80" valign="middle"><? echo $total_time; ?></td>
                            
							<td width="70" valign="middle" align="right">
								<?	$chemical_return_cost=$cost_return_data_arr[$batch_id][5];
								$chemical_issue_cost=$cost_data_arr[$batch_id][5];
								$chemical_cost=$chemical_issue_cost-$chemical_return_cost;
								echo number_format($chemical_cost,2,'.','');
								?></td>
								<td width="70" valign="middle" align="right"><?
								$dyes_return_cost=$cost_return_data_arr[$batch_id][6];
								$dyes_issue_cost=$cost_data_arr[$batch_id][6];
								$dyes_cost=$dyes_issue_cost-$dyes_return_cost;
								echo number_format($dyes_cost,2,'.',''); ?></td>
								<td width="70" valign="middle" align="right"><p><? echo number_format($chemical_cost+$dyes_cost,2,'.',''); ?></p></td>
								<td width=""><p style="word-break:break-all">

									<?
									$fabr_compo = trim($row['const_composition']);
									$str = implode(',',array_unique(explode(',', $fabr_compo)));
									echo chop($str,',');
									?>
								</p>
							</td>

							<?
							$unload_remarks = $row['remarks'];
							$load_remarks = $self_load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
							if ($unload_remarks=="")
							{
								?><td valign="middle"  width="100"><div style="word-wrap:break-word; width:100px;"><? echo $load_remarks;?></div></td><?
							}
							else
							{
								?><td valign="middle" width="100"><div style="word-wrap:break-word; width:100px;"><? echo $row['remarks']; ?></div></td><?
							}
							?>
						</tr>
						<?
						//$i++;
						//$other_mcw_wise_total+=$other_pro_qty;
						//$white_mcw_wise_total+=$white_pro_qty;

					}
					//if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
					{
						?>
						<tr style="background: #F0BC8F;">
							<td colspan="5">
								<?
								echo $cause_type[$cause_id];
								?>
							</td>
							<td colspan="5">
								<?
								echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
								?>
							</td>
							<td></td>
							<td></td>
							<td>
								<?
								$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
								$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
								$hour_minut = $idle_start_hour.':'.$idle_start_minut;
								$start_hour_minut = date('h:i A', strtotime($hour_minut));
								echo $start_hour_minut;
								?>
							</td>
							<td>
								<?
								$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
								$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
								$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
								$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
								echo $end_hour_minut;
								?>
							</td>
							<td>
								<?
								$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
								$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];

								$before = strtotime($from_date . " " . $start_hour_minut);
								$after = strtotime($to_date . " " . $end_hour_minut);
								$diff = $after - $before;
									//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments

									// $diff is in seconds
								$hours = floor($diff / 3600);
								$minutes = floor(($diff - $hours * 3600) / 60);
								$seconds = $diff - $hours * 3600 - $minutes * 60;
								echo $hours.'.'.$minutes.'H';
								?>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?
					}
					?>
					<tr  bgcolor="#99CC99">
						<td colspan="2" align="right"><b>Batch Per M/C :</b></td>
						<td align="center"><strong><? echo $mcw_wise_self_functional_batch_arr=count($self_functional_batch_arr); ?></strong></td>
						<td><strong></strong></td>
						<td align="center"><strong><? echo number_format($mcw_wise_total_batch,2,'.',''); ?></strong></td>
						<td colspan="5" align="right"><b>Total:</b></td>
						<td  align="right"><strong><? echo number_format($other_mcw_wise_total,2,'.','');?></strong></td>
						<td  align="right"><strong><? echo number_format($white_mcw_wise_total,2,'.','');?></strong></td>
						<td  align="right"><strong><? echo number_format($wash_mcw_wise_total,2,'.','');?></strong></td>
						<td   align="right"><strong><? echo number_format($normal_wash_mcw_wise_total,2,'.','');?></strong></td>
						<td  align="right"><strong><? echo number_format($aop_wash_mcw_wise_total,2,'.','');?></strong></td>
								<td  align="right"><strong><? echo number_format($total_pro_qty,2,'.','');?></strong></td>
									<td  align="right"><strong><? echo number_format($total_trims_weight,2,'.','');?></strong></td>
										<td  align="right"><strong><? echo number_format($total_self_production_qty,2,'.','');?></strong></td>
											<td  align="right"><strong><? echo number_format($total_actual_prod_qty,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($total_self_incomplete_qty,2,'.','');?></strong></td>
													<td colspan="11"></td>
												</tr>
												<?
												$i++;
												$other_date_wise_total+=$other_mcw_wise_total;
												$white_date_wise_total+=$white_mcw_wise_total;
												$wash_date_wise_total+=$wash_mcw_wise_total;
												
												$normal_wash_date_wise_total+=$normal_wash_mcw_wise_total;
												$aop_wash_date_wise_total+=$aop_wash_mcw_wise_total;
												
												$pro_qty_date_wise_total+=$total_pro_qty;
												$trims_weight_date_wise_total+=$total_trims_weight;
												$date_wise_total_batch+=$mcw_wise_total_batch;
												$date_wise_total_self_production+=$total_self_production_qty;
												$date_wise_total_self_actual_production+=$total_actual_prod_qty;
												$date_wise_total_self_incomplete_qty+=$total_self_incomplete_qty;
												$date_wise_self_functional_batch+=$mcw_wise_self_functional_batch_arr;
											}
											?>
											<tr bgcolor="#99CC99" >
												<td colspan="2" align="right"><b>Date wise Batch:</b></td>
												<td align="center"><strong><? echo $date_wise_self_functional_batch;?></strong></td>
												<td><strong></strong></td>
												<td align="center"><strong><? echo number_format($date_wise_total_batch,2,'.','');?></strong></td>
												<td colspan="5" align="right"><b>Date wise Total:</b></td>
												<td  align="right"><strong><? echo number_format($other_date_wise_total,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($white_date_wise_total,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($wash_date_wise_total,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($normal_wash_date_wise_total,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($aop_wash_date_wise_total,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($pro_qty_date_wise_total,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($trims_weight_date_wise_total,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($date_wise_total_self_production,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($date_wise_total_self_actual_production,2,'.','');?></strong></td>
												<td  align="right"><strong><? echo number_format($date_wise_total_self_incomplete_qty,2,'.','');?></strong></td>
												<td colspan="11"></td>
											</tr>
											<?
											$other_grnd_total+=$other_date_wise_total;
											$white_grnd_total+=$white_date_wise_total;
											$wash_grnd_total+=$wash_date_wise_total;
											$normal_wash_grnd_total+=$normal_wash_date_wise_total;
											$aop_wash_grnd_total+=$aop_wash_date_wise_total;
											$pro_qty_grnd_total+=$pro_qty_date_wise_total;
											$trims_weight_grnd_total+=$trims_weight_date_wise_total;
											$self_production_grnd_total+=$date_wise_total_self_production;
											$self_actual_production_grnd_total+=$date_wise_total_self_actual_production;
											$incomplete_qty_grnd_total+=$date_wise_total_self_incomplete_qty;
											$grnd_total_batch+=$date_wise_total_batch;
											$grnd_total_self_functional_batch+=$date_wise_self_functional_batch;
										}
										?>
										<tfoot>
											<tr class="tbl_bottom">
												<td colspan="2" align="right"><b>Grand Total Batch:</b></td>
												<td width="80"><strong><? echo $grnd_total_self_functional_batch;?></strong>&nbsp;</td>
												<td width="100"></td>
												<td width="80"><strong><? echo number_format($grnd_total_batch,2,'.','');?></strong>&nbsp;</td>
												<td width="100"></td>
												<td width="120"></td>
												<td width="80"></td>
												<td width="50"></td>
												<td width="100" align="right"><strong>Grand Total :</strong></td>
												<td width="60"><strong><? echo number_format($other_grnd_total,2,'.','');?></strong>&nbsp;</td>
												<td width="60"><strong><? echo number_format($white_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($wash_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($normal_wash_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($aop_wash_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($pro_qty_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($trims_weight_grnd_total,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($self_production_grnd_total,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($self_actual_production_grnd_total,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($incomplete_qty_grnd_total,2,'.','');?></strong></td>
												<td colspan="11"></td>
											</tr>

											<tr class="tbl_bottom">
												<td colspan="2" align="right"><b>Avg Batch Per M/C:</b></td>
												<td width="80"><strong><? echo number_format($total_mc=$grnd_total_batch/($i-1),2,'.','') ;?></strong>&nbsp;</td>
												<td colspan="28" align="right"></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<br>
							<?
						}

	if ($order_type==0 || $order_type==2) // Subcontract Order
	{
		?>
		<div>
			<table width="2470" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th colspan="31" style="text-align: left;">Subcontract Order</th>
					</tr>
				</thead>
			</table>
			<table width="2470" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL No</th>
						<th rowspan="2" width="100">M/C & Capacity</th>
						<th rowspan="2" width="80">Functional Batch No.</th>
						<th rowspan="2" width="100">Party Name</th>
						<th rowspan="2" width="100">Batch No.</th>
						<th rowspan="2" width="100">Batch Color</th>
						<th rowspan="2" width="100">Order No.</th>
						<th rowspan="2" width="70">Batch Qty</th>
						<th rowspan="2" width="50">Ext. No</th>
						<th rowspan="2" width="100">Color Range</th>
						<th colspan="7" width="">Total Production Qty in Kg</th>
						<th rowspan="2" width="70">MC Total Production</th>
						<th rowspan="2" width="70">Actual Production</th>
						<th rowspan="2" width="70">Incomplete Production</th>
						<th rowspan="2" width="110">Result</th>
						<th rowspan="2" width="80">Water/kg in Ltr</th>
						<th rowspan="2" width="80">M/C UT%y</th>
						<th rowspan="2" width="80">Loading Time</th>
						<th rowspan="2" width="80">Unloading Time</th>
						<th rowspan="2" width="80">Total Time (Hour)</th>
						<th rowspan="2" width="70">Chemical Cost</th>
						<th rowspan="2" width="70">Dyes Cost</th>
						<th rowspan="2" width="70">Total Cost</th>
						<th rowspan="2" width="150">Fabric Construction</th>

						<th rowspan="2" width="">Remarks</th>
					</tr>
					<tr>
						<th width="60" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
						<th width="60" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Normal Wash</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">AOP Wash</th>
						
						<th width="60">Re-Process</th>
						<th width="60">Trims Weight</th>
					</tr>
				</thead>
			</table>
			<div style="width:2490px; overflow-y:scroll; max-height:300px;" id="scroll_body">
				<table align="center" cellspacing="0" width="2470"  border="1" rules="all" class="rpt_table" >

					<?
					$i=1;
					$subcon_other_grnd_total=$subcon_white_grnd_total=$subcon_wash_grnd_total=$subcon_normal_wash_grnd_total=$subcon_aop_wash_grnd_total=$subcon_pro_qty_grnd_total=$subcon_trims_weight_grnd_total=$subcon_grnd_total_batch=$subcon_grnd_total_functional_batch=$subc_production_grnd_total=$subcon_grnd_total_actual_batch=$subc_incomplete_qty_grnd_total=0;
					foreach ($subcon_date_data_arr as $p_date=>$prod_date_data)
					{
						$subcon_other_date_wise_total=$subcon_white_date_wise_total=$subcon_wash_date_wise_total=$subcon_normal_wash_date_wise_total=$subcon_aop_wash_date_wise_total=$subcon_pro_qty_date_wise_total=$subcon_trims_weight_date_wise_total=$subcon_date_wise_total_batch=$subcon_date_wise_total_functional_batch=$date_wise_total_subc_production=$date_wise_total_subc_actual_production=$date_wise_total_subc_incomplete_qty=0;
						?>
						<tr>
							<td colspan="29" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
						</tr>
						<?
						foreach ($prod_date_data as $machine_id => $machine_data)
						{
							$subcon_other_mcw_wise_total=$subcon_white_mcw_wise_total=$subcon_wash_mcw_wise_total=$subcon_normal_wash_mcw_wise_total=$subcon_aop_wash_mcw_wise_total=$subcon_pro_qty_total=$subcon_trims_weight_total=$subcon_mc_wise_total_batch=$total_subc_production_qty=$subcon_actual_prod_qty_total=$total_subc_incomplete_qty=0;
							$subcon_functional_batch_arr=array();
							$r=0;
							foreach ($machine_data as $batch_id => $row)
							{
						//echo $row['batch_ext_no'];

						//echo 'Test '.$subc_batch_qty_arr[$p_date][$machine_id][$row['batch_no']].'<br>';
								$sub_fin_qnty=$subcon_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
								$sub_fin_qnty2=$subcon_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
								$sub_re_process_qty=$sub_re_process_qty_arr[$row['batch_no']][$row['result']]['re_process_qty'];
								$sub_re_process_qty3=$subcon_fin_prod_arr3[$row[("batch_no")]][$row['batch_ext_no']]["fin_qnty"];
						//echo $sub_re_process_qty3.'DDD'.$row['batch_ext_no'].'<br>';
								$sub_batch_qty=$sub_batch_qty_arr[$batch_id];

								$sub_recv_number=$sub_fin_prod_qty_arr[$batch_id]["recv_number"];
								$sub_receive_qnty=$sub_fin_prod_qty_arr[$batch_id]["receive_qnty"];
								if($sub_receive_qnty>0) $sub_batch_color_td="green";else  $sub_batch_color_td="";

								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr id="<? echo 'vs_'.$i; ?>" onClick="change_color('vs_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
									<?
									if ($r==0)
									{
										?>
										<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $subcon_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
										}
										else
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $subcon_rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
										}
										?>" width="30" valign="middle"><? echo $i;?></td>
										<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $subcon_rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
										}
										else
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $subcon_rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
										}
										?>" width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $row['machine_no'].'-'.$row['prod_capacity'].'kg';?></div></td>
										<?
									} $r++;


							//if($subc_arrs[$p_date][$machine_id][$row['system_no']]=="")
							//{
									?>
									<td rowspan="<? //echo $subc_fun_batch_rowspan_arr[$p_date][$machine_id][$row['system_no']] ?>" width="80" valign="middle" style="word-wrap:break-word; word-break: break-all;"><? echo $subcon_functional_batch=$row['system_no']; $subcon_functional_batch_arr[$row['system_no']]=$row['system_no']; ?></td>
									<?
								//$subc_arrs[$p_date][$machine_id][$row['system_no']]=$row['system_no'];
							//}
									if($row['batch_ext_no']>0) $sub_extBgcolor = "yellow";else $sub_extBgcolor="";
									?>

									<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $buyer_library[$row['party_id']];?></div></td>

									<td width="100" valign="middle" style="word-wrap:break-word; word-break: break-all;"  title="Recv No=<? echo $sub_recv_number.',Fin Qty='.$sub_receive_qnty; ?>">
										<b style=" color:<? echo $sub_batch_color_td;?>"><? echo $row['batch_no']; $subcon_mc_wise_total_batch+=count($row['batch_no']);?></b></td>
										<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $color_library[$row['color_id']]?></div></td>
										<td width="100" valign="middle"><? echo $row['order_no'];?></td>

										<td width="70" valign="middle" title="Fabric + Trims" align="right"><? echo number_format($sub_batch_qty+$row['total_trims_weight'],2,'.','');?></td>
										<td width="50" valign="middle" style="word-wrap:break-word; word-break: break-all;" bgcolor="<? echo $sub_extBgcolor;?>" align="center"><? echo $row['batch_ext_no'];?></td>
										<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $color_range[$row['color_range_id']];?></div></td>
										<?
							//sub_fin_prod_arr_check
										$subcon_other_pro_qty=$subcon_white_pro_qty=$subcon_wash_pro_qty=$pro_qty=$sub_actual_prod_qty=$sub_other_qty=0;
							//if (($row['result']==1 || $row['result']==2) && $sub_re_process_qty3=='')   // Shade match/Redyeing Needed/Fin Prod Available
								$sub_wash_entry_form=$sub_wash_qty_array[$p_date][$machine_id][$batch_id][$row[("batch_ext_no")]]["wash_entry_form"];
								/*if($row['batch_ext_no']==1)
								{
								$bext=$row['batch_ext_no'];
								}
								else */$bext=$row['batch_ext_no']-1;
										//$bext=$row['batch_ext_no']-1;
							if(($bext*1)<=0) $bext='';
							if($row['result']==1)   // Shade match/Redyeing Needed/Fin Prod Available
							{
								if ($row['color_range_id']==4)
								{
									$subcon_white_pro_qty = $row['batch_qnty'];
								}
								else if ($row['color_range_id']==7)
								{
									$subcon_wash_pro_qty = $row['batch_qnty'];
								}
								else $subcon_other_pro_qty = $row['batch_qnty'];
										//$sub_actual_prod_qty = $row['batch_qnty']+$row['total_trims_weight'];

							}
							else if($row['result']==5 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=='')   // Shade match/Redyeing Needed/Fin Prod Available
							{
								if ($row['color_range_id']==4)
								{
									$subcon_white_pro_qty = 0;
								}
								else if ($row['color_range_id']==7)
								{
									$subcon_wash_pro_qty = 0;
								}
								else $subcon_other_pro_qty = 0;
										//$sub_actual_prod_qty = $row['batch_qnty']+$row['total_trims_weight'];
							}
							if($row['batch_ext_no']>0)
							{
								$subcon_white_pro_qty =0;
								$subcon_wash_pro_qty = 0;
								$subcon_other_pro_qty =0;
								//echo "B";
							}
							if($sub_wash_entry_form==424)
							{
								
								$sub_prod_qty_aop=$sub_wash_qty_array[$p_date][$machine_id][$batch_id][$row[("batch_ext_no")]]["prod_qty_aop"];
								$sub_prod_qty_normal=$sub_wash_qty_array[$p_date][$machine_id][$batch_id][$row[("batch_ext_no")]]["prod_qty_normal"];
								//$subcon_white_pro_qty =0;
								//$subcon_wash_pro_qty = 0;
							//	$subcon_other_pro_qty =0;
							}
							//echo $sub_wash_entry_form.'='.$sub_prod_qty_normal;

							if ($row['result']==1 &&  $row['batch_ext_no']=="")  //Shade match/Redyeing Needed/Fin Prod Available
							{
								$sub_actual_prod_qty =$row['batch_qnty']+$row['total_trims_weight'];
							}
							else if ($row['result']==1 && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available
							{
								$sub_actual_prod_qty = 0;
							}
							if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
							{
								$sub_actual_prod_qty = $row['batch_qnty']+$row['total_trims_weight'];
							}

							//if (($row['result']==1 || $row['result']==2) && $row['batch_ext_no']>0 && $sub_re_process_qty3>0 ) //Shade match/Redyeing Needed/Redying/Fin Prod Available
							if ( $row['result']==1 && $sub_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
							{
								$pro_qty =$row['batch_qnty'];// $row['batch_qty'];
								$is_fnished[$row['batch_no']]=1;
							}
							if($is_fnished[$row['batch_no']]==1) $pro_qty =$row['batch_qnty'];

							if($row['incomplete_result']==4)
							{
								$incomplete_qty=$row['batch_qty']+$row['total_trims_weight'];
							}
							else $incomplete_qty=0;
							$total_subc_incomplete_qty+=$incomplete_qty;
							if($sub_actual_prod_qty>0) $trims_weight = $row['total_trims_weight']; else $trims_weight="";
							?>
							<td width="60" valign="middle" align="right"><? echo number_format($subcon_other_pro_qty,2,'.',''); $subcon_other_mcw_wise_total+=$subcon_other_pro_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($subcon_white_pro_qty,2,'.',''); $subcon_white_mcw_wise_total+=$subcon_white_pro_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($subcon_wash_pro_qty,2,'.',''); $subcon_wash_mcw_wise_total+=$subcon_wash_pro_qty;?></td>
							
							<td width="60" valign="middle" align="right" title="<? echo $machine_id.',BatchId='.$batch_id;?>"><? echo number_format($sub_prod_qty_normal,2,'.',''); $subcon_normal_wash_mcw_wise_total+=$sub_prod_qty_normal;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($sub_prod_qty_aop,2,'.',''); $subcon_aop_wash_mcw_wise_total+=$sub_prod_qty_aop;?></td>
							
							<td width="60" valign="middle" align="right"><? echo number_format($pro_qty,2,'.',''); $subcon_pro_qty_total+=$pro_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($trims_weight,2,'.',''); $subcon_trims_weight_total+=$trims_weight;?></td>
							<td width="70" valign="middle" align="right"><? 
							if($sub_prod_qty_normal>0 || $sub_prod_qty_aop>0)
							{
							 $total_production=$sub_prod_qty_normal+$sub_prod_qty_aop;
							}
							else{
								 $total_production=$row['batch_qnty']+$row['total_trims_weight'];
							}
							echo number_format($total_production,2,'.','');
							//$subcon_other_pro_qty+$subcon_white_pro_qty+$subcon_wash_pro_qty+$pro_qty+$trims_weight+$incomplete_qty+$sub_other_qty;
							$total_subc_production_qty+=$total_production;?></td>
							<td width="70" valign="middle" align="right" title="BatchID=<? echo $batch_id;?>"><? echo number_format($sub_actual_prod_qty,2,'.',''); $subcon_actual_prod_qty_total+=$sub_actual_prod_qty;?></td>
							<td width="70" valign="middle"  align="right"><? echo number_format($incomplete_qty,2,'.','');//if($row['incomplete_result']==4) echo $incomplete_qty=$row['batch_qnty']; $total_subc_incomplete_qty+=$incomplete_qty; ?></td>
							<? if($row['result']==1) { $resultBgcolor = "green";} else if($row['result']==2) {$resultBgcolor = "yellow";} else{$resultBgcolor = "#FFFFFF";} ?>
							<td width="110" valign="middle" bgcolor="<? echo $resultBgcolor; ?>"><div style="word-wrap:break-word; width:110px;"><? echo $dyeing_result[$row['result']];?></div></td>
							<td width="80" valign="middle"  align="right"><? echo $row['water_flow_meter'];?></td>
							<?
							$total_subc_btch_qty = $row['batch_qnty']+$row['total_trims_weight'];
							$subc_mc_capacity = $row['prod_capacity'];
							$subc_mc_ut = ($subc_mc_ut_qty_arr[$p_date][$machine_id]*100)/$subc_mc_capacity;
							$subc_mc_ut_result = ($subc_mc_ut*$total_subc_btch_qty)/$subc_mc_ut_qty_arr[$p_date][$machine_id];
							?>
							<td width="80" valign="middle"><p><? echo number_format($subc_mc_ut_result,2,'.','');?></p></td>
							<?
							$load_hour = $subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
							$load_minut = $subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
							$process_start_date=$subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
							$start_time = $load_hour.'.'.$load_minut;
							$load_hour_minut = date('h:i a', strtotime($start_time));
							?>
							<td width="80" valign="middle"><? echo $load_hour_minut;?></td>
							<?
							$unload_hour = $row['unload_hours'];
							$unload_minut = $row['unload_minutes'];
							$sub_process_end_date = $row['production_end_date'];
							$end_time = $unload_hour.'.'.$unload_minut;
							$unload_hour_minut = date('h:i a', strtotime($end_time));
							?>
							<td width="80" valign="middle"><? echo $unload_hour_minut; ?></td>
							<?
							$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
							$prod_start_date_time=strtotime($start_date_time);

							$end_date_time=$sub_process_end_date.'.'.$unload_hour.'.'.$unload_minut;
							$prod_end_date_time=strtotime($end_date_time);

							$diff = ($prod_end_date_time - $prod_start_date_time);
							$total = $diff/60;
							$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
							?>
							<td width="80" valign="middle"><? echo $total_time; ?></td>
							<td width="70" valign="middle" align="right">
								<?	$chemical_return_cost=$cost_return_data_arr[$batch_id][5];
								$chemical_issue_cost=$cost_data_arr[$batch_id][5];
								$chemical_cost=$chemical_issue_cost-$chemical_return_cost;
								echo number_format($chemical_cost,2,'.','');
								?></td>
								<td width="70" valign="middle" align="right"><?
								$dyes_return_cost=$cost_return_data_arr[$batch_id][6];
								$dyes_issue_cost=$cost_data_arr[$batch_id][6];
								$dyes_cost=$dyes_issue_cost-$dyes_return_cost;
								echo number_format($dyes_cost,2,'.',''); ?></td>
								<td width="70" valign="middle" align="right"><? echo number_format($chemical_cost+$dyes_cost,2,'.',''); ?></td>
								<td width="150" valign="middle" style="word-wrap:break-word; word-break: break-all;"> <div style="word-wrap:break-word; width:150px;">
									<?
									$subcon_fabr_compo = trim($row['item_description']);
									$subcon_str = implode(',',array_unique(explode(',', $subcon_fabr_compo)));
									echo chop($subcon_str,',');
									?></div></td>
									<?
									$unload_remarks = $row['remarks'];
									$load_remarks = $subcon_load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["remarks"];
									if ($unload_remarks=="")
									{
										?><td valign="middle" width=""><p><? echo $load_remarks;?></p></td><?
									}
									else
									{
										?><td valign="middle" width=""><p><? echo $row['remarks'];?></p></td><?
									}
									?>
								</tr>
								<?
						//$i++;
						//$subcon_other_mcw_wise_total+=$subcon_other_pro_qty;
						//$subcon_white_mcw_wise_total+=$subcon_white_pro_qty;
							}
					//if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
					{
						?>
						<tr style="background: #F0BC8F;">
							<td colspan="5">
								<?
								echo $cause_type[$cause_id];
								?>
							</td>
							<td colspan="5">
								<?
								echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
								?>
							</td>
							<td></td>
							<td></td>
							<td>
								<?
								$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
								$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
								$hour_minut = $idle_start_hour.':'.$idle_start_minut;
								$start_hour_minut = date('h:i A', strtotime($hour_minut));
								echo $start_hour_minut;
								?>
							</td>
							<td>
								<?
								$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
								$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
								$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
								$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
								echo $end_hour_minut;
								?>
							</td>
							<td>
								<?
								$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
								$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];

								$before = strtotime($from_date . " " . $start_hour_minut);
								$after = strtotime($to_date . " " . $end_hour_minut);
								$diff = $after - $before;
									//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments

									// $diff is in seconds
								$hours = floor($diff / 3600);
								$minutes = floor(($diff - $hours * 3600) / 60);
								$seconds = $diff - $hours * 3600 - $minutes * 60;
								echo $hours.'.'.$minutes.'H';
								?>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?
					}
					?>
					<tr   bgcolor="#99CC99">
						<td colspan="2" align="right"><b>Batch Per M/C :</b></td>
						<td align="center"><strong><? echo $mcw_wise_subcon_functional_batch=count($subcon_functional_batch_arr); ?></strong></td>
						<td width="100"></td>
						<td align="center"><strong><? echo number_format($subcon_mc_wise_total_batch,2,'.','');?></strong></td>
						<td colspan="5" align="right"><b>Total:</b></td>
						<td align="right"><strong><? echo number_format($subcon_other_mcw_wise_total,2,'.','');?></strong></td>
						<td align="right"><strong><? echo number_format($subcon_white_mcw_wise_total,2,'.','');?></strong></td>
							<td align="right"><strong><? echo number_format($subcon_wash_mcw_wise_total,2,'.','');?></strong></td>
							<td align="right"><strong><? echo number_format($subcon_normal_wash_mcw_wise_total,2,'.','');?></strong></td> 
							<td align="right"><strong><? echo number_format($subcon_aop_wash_mcw_wise_total,2,'.','');?></strong></td>
								<td align="right"><strong><? echo number_format($subcon_pro_qty_total,2,'.','');?></strong></td>
									<td align="right"><strong><? echo number_format($subcon_trims_weight_total,2,'.','');?></strong></td>
										<td align="right"><strong><? echo number_format($total_subc_production_qty,2,'.','');?></strong></td>
											<td align="right"> <strong><?  echo number_format($subcon_actual_prod_qty_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($total_subc_incomplete_qty,2,'.','');?></strong></td>
													<td colspan="11"></td>
												</tr>
												<?
												$i++;
												$subcon_other_date_wise_total+=$subcon_other_mcw_wise_total;
												$subcon_white_date_wise_total+=$subcon_white_mcw_wise_total;
												$subcon_wash_date_wise_total+=$subcon_wash_mcw_wise_total;
												
												$subcon_normal_wash_date_wise_total+=$subcon_normal_wash_mcw_wise_total;
												$subcon_aop_wash_date_wise_total+=$subcon_aop_wash_mcw_wise_total;
												
												$subcon_pro_qty_date_wise_total+=$subcon_pro_qty_total;
												$subcon_trims_weight_date_wise_total+=$subcon_trims_weight_total;
												$date_wise_total_subc_production+=$total_subc_production_qty;
												$date_wise_total_subc_actual_production+=$subcon_actual_prod_qty_total;
												$date_wise_total_subc_incomplete_qty+=$total_subc_incomplete_qty;
												$subcon_date_wise_total_batch+=$subcon_mc_wise_total_batch;
												$subcon_date_wise_total_functional_batch+=$mcw_wise_subcon_functional_batch;
											}
											?>
											<tr bgcolor="#99CC99">
												<td colspan="2" align="right"><b>Date wise Batch:</b></td>
												<td align="center"><strong><? echo $subcon_date_wise_total_functional_batch;?></strong></td>
												<td width="100"></td>
												<td  align="center"><strong><? echo number_format($subcon_date_wise_total_batch,2,'.','');?></strong></td>
												<td colspan="5" align="right"><b>Date wise Total:</b></td>
												<td align="right"><strong><? echo number_format($subcon_other_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($subcon_white_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($subcon_wash_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($subcon_normal_wash_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($subcon_aop_wash_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($subcon_pro_qty_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($subcon_trims_weight_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($date_wise_total_subc_production,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($date_wise_total_subc_actual_production,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($date_wise_total_subc_incomplete_qty,2,'.','');?></strong></td>
												<td colspan="11"></td>
											</tr>
											<?
											$subcon_other_grnd_total+=$subcon_other_date_wise_total;
											$subcon_white_grnd_total+=$subcon_white_date_wise_total;
											$subcon_wash_grnd_total+=$subcon_wash_date_wise_total;
											
											$subcon_normal_wash_grnd_total+=$subcon_normal_wash_date_wise_total;
											$subcon_aop_wash_grnd_total+=$subcon_aop_wash_date_wise_total;
											
											$subcon_pro_qty_grnd_total+=$subcon_pro_qty_date_wise_total;
											$subcon_trims_weight_grnd_total+=$subcon_trims_weight_date_wise_total;
											$subc_production_grnd_total+=$date_wise_total_subc_production;
											$subc_incomplete_qty_grnd_total+=$date_wise_total_subc_incomplete_qty;
											$subcon_grnd_total_batch+=$subcon_date_wise_total_batch;
											$subcon_grnd_total_actual_batch+=$date_wise_total_subc_actual_production;
											$subcon_grnd_total_functional_batch+=$subcon_date_wise_total_functional_batch;
										}
										?>
										<tfoot>
											<tr class="tbl_bottom">
												<td align="right" colspan="2"><strong>Grand Total Batch:</strong></td>
												<td width="80"><strong><? echo $subcon_grnd_total_functional_batch;?></strong>&nbsp;</td>
												<td width="100"></td>
												<td width="100" ><strong><? echo number_format($subcon_grnd_total_batch,2,'.','');?></strong>&nbsp;</td>
												<td width="100"></td>
												<td width="100"></td>
												<td width="70"></td>
												<td width="50"></td>
												<td width="100" align="right"><strong>Grand Total :</strong></td>
												<td width="60"><strong><? echo number_format($subcon_other_grnd_total,2,'.','');?></strong>&nbsp;</td>
												<td width="60"><strong><? echo number_format($subcon_white_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($subcon_wash_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($subcon_normal_wash_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($subcon_aop_wash_grnd_total,2,'.','');?></strong></td>
												
												<td width="60"><strong><? echo number_format($subcon_pro_qty_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($subcon_trims_weight_grnd_total,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($subc_production_grnd_total,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($subcon_grnd_total_actual_batch,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($subc_incomplete_qty_grnd_total,2,'.','');?></strong></td>
												<td colspan="11" width="80"></td>
											</tr>
											<tr class="tbl_bottom">
												<td colspan="2" align="right"><b>Avg Batch Per M/C:</b></td>
												<td width="80"><strong><? echo number_format($total_mc=$subcon_grnd_total_batch/($i-1),2,'.','') ;?></strong>&nbsp;</td>
												<td colspan="28" align="right"></td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<br>
							<?
	}

	if ($order_type==0 || $order_type==3) // Sample Without Order
	{
		?>
		<div>
			<table width="2520" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th colspan="29" style="text-align: left;">Sample Without Order</th>
					</tr>
				</thead>
			</table>
			<table width="2520" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th rowspan="2" width="30">M/C No</th>
						<th rowspan="2" width="100">M/C & Capacity</th>
						<th rowspan="2" width="80">Functional Batch No.</th>
						<th rowspan="2" width="100">Buyer Name</th>
						<th rowspan="2" width="100">Batch No.</th>
						<th rowspan="2" width="120">Batch Color</th>
						<th rowspan="2" width="120">Booking No.</th>
						<th rowspan="2" width="70">Batch Qty</th>
						<th rowspan="2" width="50">Ext. No</th>
						<th rowspan="2" width="100">Color Range</th>
						<th colspan="7" width="">Total Production Qty in Kg</th>
						<th rowspan="2" width="70">MC Total Production</th>
						<th rowspan="2" width="70">Actual Production</th>
						<th rowspan="2" width="70">Incomplete Production</th>
						<th rowspan="2" width="110">Result</th>
						<th rowspan="2" width="70">Water/kg in Ltr</th>
						<th rowspan="2" width="70">M/C UT%y</th>
						<th rowspan="2" width="80">Loading Time</th>
						<th rowspan="2" width="80">Unloading Time</th>
						<th rowspan="2" width="80">Total Time (Hour)</th>
						<th rowspan="2" width="70">Chemical Cost</th>
						<th rowspan="2" width="70">Dyes Cost</th>
						<th rowspan="2" width="70">Total Cost</th>
						<th rowspan="2" width="150">Fabric Construction</th>

						<th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<th width="60" title="Data is showing, according to [Color Range] - Average Color, from batch creation page">Color</th>
						<th width="60" title="Data is showing, according to [Color Range] - White Color, from batch creation page.">White</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Wash (Y/D)</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">Normal Wash</th>
						<th width="60" title="Data is showing, according to [Color Range] - Wash, from batch creation page.">AOP Wash</th>
						<th width="60">Re-Process</th>
						<th width="60">Trims Weight</th>
					</tr>
				</thead>
			</table>
			<div style="width:2540px; overflow-y:scroll; max-height:300px;" id="scroll_body">
				<table align="center" cellspacing="0" width="2520"  border="1" rules="all" class="rpt_table" >

					<?
					$i=1;
					$other_grnd_total=$white_grnd_total=$wash_grnd_total=$normal_wash_grnd_total=$aop_wash_grnd_total=$wo_pro_qty_grnd_total=$wo_trims_weight_grnd_total=$sm_grnd_total_batch=$sm_grnd_total_functional_batch=$grnd_total_samp_production=$grnd_total_samp_incomplete_qty=$actual_wo_pro_qty_grnd_total=0;
					foreach ($date_data_arr as $p_date=>$prod_date_data)
					{
						$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$normal_wash_date_wise_total=$aop_wash_date_wise_total=$wo_pro_qty_date_wise_total=$wo_trims_weight_date_wise_total=$sm_date_wise_total_batch=$sm_date_wise_total_functional_batch=$date_wise_total_samp_production=$wo_pro_qty_actual_date_wise_total=$date_wise_total_samp_incomplete_qty=0;
						?>
						<tr>
							<td colspan="31" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
						</tr>
						<?
						$j=0;
						foreach ($prod_date_data as $machine_id => $machine_data)
						{
							$other_mcw_wise_total=$white_mcw_wise_total=$wo_wash_mcw_wise_total=$wo_normal_wash_mcw_wise_total=$wo_aop_wash_mcw_wise_total=$wo_pro_qty_total=$wo_trims_weight_total=$sm_mcw_wise_total_batch=$total_samp_production=$total_samp_incomplete_qty=$samp_actual_pro_qty_total=0;
							$sample_functional_batch_arr=array();
							$r=0;
							foreach ($machine_data as $batch_id => $row)
							{
						//echo $row['batch_ext_no'];
								$samp_fin_prod=$samp_fin_prod_arr[$row['batch_no']][$row['result']]["fin_qnty"];
								$samp_fin_prod2=$samp_fin_prod_arr2[$row['batch_no']]["fin_qnty"];
								$samp_re_process=$samp_re_process_qty_arr[$row['batch_no']][$row['result']]['re_process_qty'];
								$samp_re_process3=$samp_re_process_qty_arr3[$row['batch_no']][$row['batch_ext_no']]['re_process_qty'];
									
								$samp_recv_number=$samp_fin_prod_qty_arr[$batch_id]["recv_number"];
								$samp_recv_qty=$samp_fin_prod_qty_arr[$batch_id]["receive_qnty"];
								if($samp_recv_qty>0) $samp_batch_color_td="green";else  $samp_batch_color_td="";
								$samp_wash_entry_form=$samp_wash_qty_array[$p_date][$machine_id][$batch_id][$vals["batch_ext_no"]]["wash_entry_form"];
								$samp_batch_qty=$samp_batch_qty_arr[$batch_id];

								 $row['batch_qty']=0;//$row['production_qty']=0;
								 $row['batch_qty']=$row['production_qty'];
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								?>
								<tr id="<? echo 'vSamp_'.$i; ?>" onClick="change_color('vSamp_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
									<?
									if ($r==0)
									{
										?>
										<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
										}
										else
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
										}
										?>" width="30" valign="middle"><?echo $i;?></td>
										<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id]))
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $rowspan_arr[$p_date][$machine_id]+1+$tot_rowspan;
										}
										else
										{
											$tot_rowspan=count($idle_data_arr2[$p_date][$machine_id]);
											echo $rowspan_arr[$p_date][$machine_id]+$tot_rowspan;
										}
										?>" width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $row['machine_no'].'- '.$row['prod_capacity'].'kg';?></div></td>
										<?
										?>

										<?
									}
								//$samp_rowspan_arr[$p_date][$machine_id][$row['system_no']]

									if($row['batch_ext_no']>0) $samp_extBgcolor = "yellow";else $samp_extBgcolor="";
							//if ($j==0)
							//{
									?>
									<td  width="80" rowspan="<? //echo $samp_rowspan_arr[$p_date][$machine_id][$row['system_no']];?>" valign="middle" style="word-wrap:break-word; word-break: break-all;"><? echo $sample_functional_batch=$row['system_no'];
									$sample_functional_batch_arr[$row['system_no']]=$row['system_no']; ?></td>
									<?
							//}
									?>
									<td width="100" valign="middle"><div style="word-wrap:break-word; width:100px;"><? echo $buyer_library[$row['buyer_id']];?></div></td>

									<td width="100" valign="middle" style="word-wrap:break-word; word-break: break-all;" title="Recv No=<? echo $samp_recv_number.',Fin Qty='.$samp_recv_qty; ?>">
										<b style=" color:<? echo $samp_batch_color_td;?>"><div style="word-wrap:break-word; width:100px;"><? echo $row['batch_no']; $sm_mcw_wise_total_batch+=count($row['batch_no']);?></b></div></td>
										<td width="120" valign="middle"><div style="word-wrap:break-word; width:120px;"><? echo $color_library[$row['color_id']]?></div></td>
										<td width="120" valign="middle"><div style="word-wrap:break-word; width:120px;"><? echo $row['booking_no'];?></div></td>
										<td width="70" valign="middle" align="right" title="Fabric + Trims" ><? echo number_format($samp_batch_qty+$row['total_trims_weight'],2,'.','');?></td>
										<td width="50" valign="middle" bgcolor="<? echo $samp_extBgcolor;?>" align="center"><? echo $row['batch_ext_no'];?></td>
										<td width="100" valign="middle"><? echo $color_range[$row['color_range_id']];?></td>
										<?

										$other_pro_qty=$white_pro_qty=$wo_wash_pro_qty=$samp_actual_prod_qty=$pro_qty=$samp_other_qty=0;
										/*if($row['batch_ext_no']==1)
										{
										$bext=$row['batch_ext_no'];
										}
										else $bext=$row['batch_ext_no']-1;*/
										$bext=$row['batch_ext_no']-1;
										if(($bext*1)<=0) $bext='';

										if ($row['result']==1 )
										{

											if ($row['color_range_id']==4)
											{
												$white_pro_qty = $row['batch_qty'];
											}
											else if ($row['color_range_id']==7)
											{
												$wo_wash_pro_qty = $row['batch_qty'];
											}
											else $other_pro_qty = $row['batch_qty'];


										}
										else if ($row['result']==5  && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]=='')
										{
											if ($row['color_range_id']==4)
											{
												$white_pro_qty = 0;
											}
											else if ($row['color_range_id']==7)
											{
												$wo_wash_pro_qty = 0;
											}
											else $other_pro_qty =0;
										}
										else if($row['batch_ext_no']>0)
										{
											$white_pro_qty =0;
											$wo_wash_pro_qty = 0;
											$other_pro_qty =0;
								//echo "B";
										}
										if($samp_wash_entry_form==424)
										{
											//$samp_prod_qty_aop=$samp_wash_qty_array[$p_date][$machine_id][$batch_id][$row[("batch_ext_no")]]["prod_qty_aop"];
											//$samp_prod_qty_normal=$samp_wash_qty_array[$p_date][$machine_id][$batch_id][$row[("batch_ext_no")]]["prod_qty_normal"];
										//	$white_pro_qty =0;
											//$wo_wash_pro_qty = 0;
											//$other_pro_qty =0;
										}
											$samp_prod_qty_normal=$row['prod_qty_normal'];
											$samp_prod_qty_aop=$row['prod_qty_aop'];
										//echo $samp_prod_qty_normal.'='.$samp_prod_qty_aop.', ';

							if ($row['result']==1  && $row['batch_ext_no']=="")  //Shade match/Redyeing Needed/Fin Prod Available
							{
								$samp_actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
							}
							else if ($row['result']==1  && $row['batch_ext_no']>0)//Shade match/Redyeing Needed/Fin Prod not Available
							{
								$samp_actual_prod_qty = 0;
							}

							if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
							{
								$samp_actual_prod_qty = $row['batch_qty']+$row['total_trims_weight'];
							}

							if ( $row['result']==1 && $samp_fin_prod_arr_check[$row['batch_no']][$bext]["fin_qnty"]  >0 && $row['batch_ext_no']>0)
							{
								$samp_pro_qty =$row['batch_qty'];// $row['batch_qty'];
								$is_fnished[$row['batch_no']]=1;
							}
							if($is_fnished[$row['batch_no']]==1) $samp_pro_qty =$row['batch_qty'];// $row['batch_qty'];

							if($row['incomplete_result']==4)
							{
								$samp_incomplete_qty=$row['batch_qty']+$row['total_trims_weight'];
							}
							else $samp_incomplete_qty=0;
							$total_samp_incomplete_qty+=$samp_incomplete_qty;
							if($samp_actual_prod_qty>0) $trims_weight=$row['total_trims_weight'];else $trims_weight="";

							?>
							<td width="60" valign="middle" align="right"><? echo number_format($other_pro_qty,2,'.',''); $other_mcw_wise_total+=$other_pro_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($white_pro_qty,2,'.',''); $white_mcw_wise_total+=$white_pro_qty;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($wo_wash_pro_qty,2,'.',''); $wo_wash_mcw_wise_total+=$wo_wash_pro_qty;?></td>
							<td width="60" valign="middle" align="right" title="<? echo $machine_id.'=BatchId='.$batch_id;?>"><? echo number_format($samp_prod_qty_normal,2,'.',''); $wo_normal_wash_mcw_wise_total+=$samp_prod_qty_normal;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($samp_prod_qty_aop,2,'.',''); $wo_aop_wash_mcw_wise_total+=$samp_prod_qty_aop;?></td>
							<td width="60" valign="middle" align="right"><? echo number_format($samp_pro_qty,2,'.',''); $wo_pro_qty_total+=$samp_pro_qty;?></td>
							<td width="60" valign="middle"><? echo number_format($trims_weight,2,'.',''); $wo_trims_weight_total+=$trims_weight;?></td>
							<td width="70" valign="middle" align="right"><? 
							if($samp_prod_qty_normal>0 || $samp_prod_qty_aop>0)
							{
								 $total_production=$samp_prod_qty_normal+$samp_prod_qty_aop;
							}
							else
							{
							  $total_production=$row['batch_qty']+$row['total_trims_weight'];
							}//($other_pro_qty+$white_pro_qty+$wo_wash_pro_qty+$samp_pro_qty+$trims_weight+$samp_incomplete_qty);
							echo number_format($total_production,2,'.','');
							$total_samp_production+=$total_production; ?></td>
							<td width="70" valign="middle" align="right"><? echo number_format($samp_actual_prod_qty,2,'.',''); $samp_actual_pro_qty_total+=$samp_actual_prod_qty;?></td>
							<td width="70" valign="middle"><? echo number_format($incomplete_qty,2,'.','');//if($row['result']==4) echo $incomplete_qty=$row['batch_qty'];
							$total_samp_incomplete_qty+=$incomplete_qty; ?></td>
							<? if($row['result']==1) { $resultBgcolor = "green";} else if($row['result']==2) {$resultBgcolor = "yellow";} else{$resultBgcolor = "#FFFFFF";} ?>
							<td width="110" valign="middle" bgcolor="<? echo $resultBgcolor; ?>"><div style="word-wrap:break-word; width:110px;"><? echo $dyeing_result[$row['result']];?></div></td>
							<td width="70" valign="middle"><? echo $row['water_flow_meter'];?></td>
							<?
							$smp_total_btch_qty = $row['batch_qty']+$row['total_trims_weight'];
							$smp_mc_capacity = $row['prod_capacity'];
							$smp_mc_ut = ($smp_mc_ut_qty_arr[$p_date][$machine_id]*100)/$smp_mc_capacity;
							$smp_mc_ut_result = ($smp_mc_ut*$smp_total_btch_qty)/$smp_mc_ut_qty_arr[$p_date][$machine_id];
							?>
							<td width="70" valign="middle"><? echo number_format($smp_mc_ut_result,2,'.','');?></td>
							<?
							if($samp_prod_qty_normal>0 || $samp_prod_qty_aop>0)
							{
								$load_hour = $row['wash_start_hr'];
								$load_minut = $row['wash_star_min'];
								$process_start_date = $row['wash_start_date'];
								$start_time = $load_hour.'.'.$load_minut;
								$load_hour_minut = date('h:i A', strtotime($start_time));
								$unload_hour = $row['wash_end_hr'];
								$unload_minut = $row['wash_end_min'];
								$process_end_dateIs = $row['wash_prod_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$unload_hour_minut = date('h:i A', strtotime($end_time));
								
								$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
								$prod_start_date_time=strtotime($start_date_time);
	
								$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
								$prod_end_date_time=strtotime($end_date_time);
	
								$diff = ($prod_end_date_time - $prod_start_date_time);
								$total = $diff/60;
								$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
							}
							else
							{
							$load_hour = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_hours"];
							$load_minut = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["load_end_minutes"];
							$process_start_date = $load_time_array[$row['machine_id']][$row['batch_id']][$row['batch_ext_no']]["process_start_date"];
							$start_time = $load_hour.'.'.$load_minut;
							$load_hour_minut = date('h:i A', strtotime($start_time));
							$unload_hour = $row['unload_hours'];
							$unload_minut = $row['unload_minutes'];
							$process_end_dateIs = $row['production_end_date'];
							$end_time = $unload_hour.'.'.$unload_minut;
							$unload_hour_minut = date('h:i A', strtotime($end_time));
							
							$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
							$prod_start_date_time=strtotime($start_date_time);

							$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
							$prod_end_date_time=strtotime($end_date_time);

							$diff = ($prod_end_date_time - $prod_start_date_time);
							$total = $diff/60;
							$total_time = sprintf("%02d.%02d H", floor($total/60), $total%60);
							}
							
							?>
							<td width="80" valign="middle"><? echo $load_hour_minut;?></td>
							<?
							
							?>
							<td width="80" valign="middle"><? echo $unload_hour_minut; ?></td>
							<?
							
							?>
							<td width="80" valign="middle"><? echo $total_time; ?></td>
							<td width="70" valign="middle" align="right">
								<?	$chemical_return_cost=$cost_return_data_arr[$batch_id][5];
								$chemical_issue_cost=$cost_data_arr[$batch_id][5];
								$chemical_cost=$chemical_issue_cost-$chemical_return_cost;
								echo number_format($chemical_cost,2,'.','');
								?></td>
								<td width="70" valign="middle" align="right"><?
								$dyes_return_cost=$cost_return_data_arr[$batch_id][6];
								$dyes_issue_cost=$cost_data_arr[$batch_id][6];
								$dyes_cost=$dyes_issue_cost-$dyes_return_cost;
								echo number_format($dyes_cost,2,'.',''); ?></td>
								<td width="70" valign="middle" align="right"><? echo number_format($chemical_cost+$dyes_cost,2,'.',''); ?></td>
								<td width="150" valign="middle" style="">
									<div style="word-wrap:break-word; width:150px;">
										<?
										$samp_fabr_compo = trim($row['const_composition']);
										$samp_str = implode(',',array_unique(explode(',', $samp_fabr_compo)));
										echo chop($samp_str,',');
										?> </div></td>

										<?
										$unload_remarks = $row['remarks'];
										$load_remarks = $load_time_array[$row['batch_id']][$row['batch_ext_no']]["remarks"];
										if ($unload_remarks=="")
										{
											?><td valign="middle"><p><? echo $load_remarks;?></p></td><?
										}
										else
										{
											?><td valign="middle"><p><? echo $row['remarks'];?></p></td><?
										}
										?>
									</tr>
									<?
									$r++;	$j++;
						//$i++;
						//$other_mcw_wise_total+=$other_pro_qty;
						//$white_mcw_wise_total+=$white_pro_qty;

								}
					//if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					foreach($idle_data_arr2[$p_date][$machine_id] as $cause_id=>$val)
					{
						?>
						<tr style="background: #F0BC8F;">
							<td colspan="5">
								<?
								echo $cause_type[$cause_id];
								?>
							</td>
							<td colspan="5">
								<?
								echo $idle_data_arr2[$p_date][$machine_id][$cause_id]['remarks'];
								?>
							</td>
							<td></td>
							<td></td>
							<td>
								<?
								$idle_start_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_hour'];
								$idle_start_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_minute'];
								$hour_minut = $idle_start_hour.':'.$idle_start_minut;
								$start_hour_minut = date('h:i A', strtotime($hour_minut));
								echo $start_hour_minut;
								?>
							</td>
							<td>
								<?
								$idle_end_hour = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_hour'];
								$idle_end_minut = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_minute'];
								$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut;
								$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
								echo $end_hour_minut;
								?>
							</td>
							<td>
								<?
								$from_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['from_date'];
								$to_date = $idle_data_arr2[$p_date][$machine_id][$cause_id]['to_date'];

								$before = strtotime($from_date . " " . $start_hour_minut);
								$after = strtotime($to_date . " " . $end_hour_minut);
								$diff = $after - $before;
									//$diff += 3600 * (date("I", $after) - date("I", $before)); // daylight savings adjustments

									// $diff is in seconds
								$hours = floor($diff / 3600);
								$minutes = floor(($diff - $hours * 3600) / 60);
								$seconds = $diff - $hours * 3600 - $minutes * 60;
								echo $hours.'.'.$minutes.'H';
								?>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?
					}
					?>
					<tr bgcolor="#99CC66"  >
						<td colspan="2" align="right"><b>Batch Per M/C :</b></td>
						<td align="center"><strong><? echo $mcw_wise_sample_functional_batch=count($sample_functional_batch_arr); ?></strong></td>
						<td></td>
						<td align="center"><strong><? echo number_format($sm_mcw_wise_total_batch,2,'.','');?></strong></td>
						<td colspan="5" align="right"><b>Total:</b></td>
						<td align="right"><strong><? echo number_format($other_mcw_wise_total,2,'.','');?></strong></td>
						<td align="right"><strong><? echo number_format($white_mcw_wise_total,2,'.','');?></strong></td>
							<td align="right"><strong><? echo number_format($wo_wash_mcw_wise_total,2,'.','');?></strong></td>
							<td align="right"><strong><? echo number_format($wo_normal_wash_mcw_wise_total,2,'.','');?></strong></td>
							<td align="right"><strong><? echo number_format($wo_aop_wash_mcw_wise_total,2,'.','');?></strong></td>
							
								<td align="right"><strong><? echo number_format($wo_pro_qty_total,2,'.','');?></strong></td>
									<td align="right"><strong><? echo number_format($wo_trims_weight_total,2,'.','');?></strong></td>
										<td align="right"><strong><? echo number_format($total_samp_production,2,'.','');?></strong></td>
											<td align="right"><strong><? echo number_format($samp_actual_pro_qty_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($total_samp_incomplete_qty,2,'.','');?></strong></td>
													<td colspan="11"></td>

												</tr>
												<?
												$i++;
												$other_date_wise_total+=$other_mcw_wise_total;
												$white_date_wise_total+=$white_mcw_wise_total;
												$wash_date_wise_total+=$wo_wash_mcw_wise_total;
												$normal_wash_date_wise_total+=$wo_normal_wash_mcw_wise_total;
												$aop_wash_date_wise_total+=$wo_aop_wash_mcw_wise_total;
												
												$wo_pro_qty_date_wise_total+=$wo_pro_qty_total;

												$wo_pro_qty_actual_date_wise_total+=$samp_actual_pro_qty_total;

												$wo_trims_weight_date_wise_total+=$wo_trims_weight_total;
												$sm_date_wise_total_batch+=$sm_mcw_wise_total_batch;
												$date_wise_total_samp_production+=$total_samp_production;
												$date_wise_total_samp_incomplete_qty+=$total_samp_incomplete_qty;
												$sm_date_wise_total_functional_batch+=$mcw_wise_sample_functional_batch;
											}
											?>
											<tr  bgcolor="#99CC66" >
												<td colspan="2" align="right"><b>Date wise Batch:</b></td>
												<td align="center"><strong><? echo $sm_date_wise_total_functional_batch;?></strong></td>
												<td></td>
												<td align="center"><strong><? echo number_format($sm_date_wise_total_batch,2,'.','');?></strong></td>
												<td colspan="5" align="right"><b>Date wise Total:</b></td>
												<td align="right"><strong><? echo number_format($other_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($white_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($wash_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($normal_wash_date_wise_total,2,'.','');?></strong></td> 
												<td align="right"><strong><? echo number_format($aop_wash_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($wo_pro_qty_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($wo_trims_weight_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($date_wise_total_samp_production,2,'.','');?></strong></td>
												<td align="right"><strong><?  echo number_format($wo_pro_qty_actual_date_wise_total,2,'.','');?></strong></td>
												<td align="right"><strong><? echo number_format($date_wise_total_samp_incomplete_qty,2,'.','');?></strong></td>
												<td colspan="11"></td>
											</tr>
											<?
											$other_grnd_total+=$other_date_wise_total;
											$white_grnd_total+=$white_date_wise_total;
											$wash_grnd_total+=$wash_date_wise_total;
												$normal_wash_grnd_total+=$normal_wash_date_wise_total;
												$aop_wash_grnd_total+=$aop_wash_date_wise_total;
													
											$wo_pro_qty_grnd_total+=$wo_pro_qty_date_wise_total;
											$actual_wo_pro_qty_grnd_total+=$wo_pro_qty_actual_date_wise_total;
											$wo_trims_weight_grnd_total+=$wo_trims_weight_date_wise_total;
											$sm_grnd_total_batch+=$sm_date_wise_total_batch;
											$grnd_total_samp_production+=$date_wise_total_samp_production;
											$grnd_total_samp_incomplete_qty+=$date_wise_total_samp_incomplete_qty;
											$sm_grnd_total_functional_batch+=$sm_date_wise_total_functional_batch;
										}
										?>
										<tfoot>
											<tr class="tbl_bottom">
												<td width="80" align="right" colspan="2"><strong>Grand Total Batch:</strong></td>
												<td width="80" align="right"><b style=" text-align:center"><strong><? echo number_format($sm_grnd_total_functional_batch,2,'.','');?></strong></b>&nbsp;</td>
												<td width="100"></td>
												<td width="100" align="right"><strong><? echo number_format($sm_grnd_total_batch,2,'.','');?></strong>&nbsp;</td>
												<td width="120"></td>
												<td width="120"></td>
												<td width="70"></td>
												<td width="50"></td>
												<td width="100" align="right"><strong>Grand Total :</strong></td>
												<td width="60"><strong><? echo number_format($other_grnd_total,2,'.','');?></strong>&nbsp;</td>
												<td width="60"><strong><? echo number_format($white_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($wash_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($normal_wash_grnd_total,2,'.','');?></strong></td> 
												<td width="60"><strong><? echo number_format($aop_wash_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($wo_pro_qty_grnd_total,2,'.','');?></strong></td>
												<td width="60"><strong><? echo number_format($wo_trims_weight_grnd_total,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($grnd_total_samp_production,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($actual_wo_pro_qty_grnd_total,2,'.','');?></strong></td>
												<td width="70"><strong><? echo number_format($grnd_total_samp_incomplete_qty,2,'.','');?></strong></td>
												<td colspan="11"></td>
											</tr>
											<tr class="tbl_bottom">
												<td colspan="2" align="right"><b>Avg Batch Per M/C:</b></td>
												<td width="80"><strong><? echo number_format($total_mc=$sm_grnd_total_batch/($i-1),2,'.','') ;?></strong>&nbsp;</td>
												<td colspan="28" align="right"> </td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
							<br>
							<?
						}
						$user_id = $_SESSION['logic_erp']["user_id"];
						foreach (glob("$user_id*.xls") as $filename)
						{
							if( @filemtime($filename) < (time()-$seconds_old) )
								@unlink($filename);
						}
		//---------end------------//
						$name=time();
						$filename=$user_id."_".$name.".xls";
						$create_new_doc = fopen($filename, 'w');
						$is_created = fwrite($create_new_doc,ob_get_contents());
						$filename=$user_id."_".$name.".xls";
						echo "$total_data****$filename";
						exit();
					 
} //show 2 end

if($action=="report_generate3") // Machine wise button(Shafiq)
{		
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company 	= str_replace("'","",$cbo_company_id);
	$machine_name 	= str_replace("'","",$txt_machine_id);
	$cbo_location 	= str_replace("'","",$cbo_location_id);
	$order_type 	= str_replace("'","",$cbo_order_type);
	$buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$booking_no 	= str_replace("'","",$txt_booking_no);
	$order_no 		= str_replace("'","",$txt_order_no);
	$batch 			= str_replace("'","",$txt_batch);
	$batch_color 	= str_replace("'","",$hidden_color_id);
	$txt_color_range= str_replace("'","",$txt_color_range);
	$floor_name 	= str_replace("'","",$cbo_floor_id);
	$date_from 		= str_replace("'","",$txt_date_from);
	$date_to 		= str_replace("'","",$txt_date_to);


	if ($batch=="") $batch_cond =""; else $batch_cond =" and a.batch_no='$batch' ";
	if ($order_type==0) $order_type_cond =""; else $order_type_cond =" and a.order_type=$order_type ";
	if ($cbo_location==0 || $cbo_location=='') $location_id =""; else $location_id =" and c.location_id=$cbo_location ";
	if ($floor_name==0 || $floor_name=='') $floor_id_cond=""; else $floor_id_cond=" and c.floor_id=$cbo_floor_id";
	if ($machine_name=="") $machine_cond=""; else $machine_cond =" and c.id in ($machine_name) ";
	if ($buyer_name==0  || $buyer_name=='') $buyer_name_cond =""; else $buyer_name_cond =" and p.buyer_id=$buyer_name ";
	if ($booking_no=="") $booking_no_cond =""; else $booking_no_cond =" and q.booking_no=$txt_booking_no ";
	if ($batch_color=="") $batch_color_cond =""; else $batch_color_cond =" and q.color_id=$batch_color ";
	if ($txt_color_range==0 || $txt_color_range=="") $color_range_cond =""; else $color_range_cond =" and q.color_range_id=$txt_color_range ";
	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="")
		{
			$sql_cond .= " and a.process_end_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}


	if($db_type==0)
	{
		if( $date_from!="" && $date_to!="" )
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		}
	}
	else if($db_type==2)
	{
		if($date_from!="" && $date_to!="")
		{
			$idle_sql_cond .= " and a.from_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		}
	}

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$machineArr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
	$floor_library=return_library_array( "select id,floor_name from  lib_prod_floor", "id", "floor_name"  );
	
	function get_time($time) 
	{
	    $duration = $time;// / 1000;
	    $hours = floor($duration / 3600);
	    $minutes = floor(($duration / 60) % 60);
	    $seconds = $duration % 60;
	    
	    return "$hours:$minutes:$seconds";
	    
	}

	// =============================== Shift Duration Entry ==============================
	$sql = "SELECT shift_name,start_time,end_time from shift_duration_entry where production_type=2 and status_active=1 and is_deleted=0";
	$sql_res = sql_select($sql);
	$shift_wise_time_array = array();
	foreach ($sql_res as $val) 
	{
		$shift_wise_time_array[$val['SHIFT_NAME']]['start_time'] = $val['START_TIME'];
		$shift_wise_time_array[$val['SHIFT_NAME']]['end_time'] = $val['END_TIME'];
	}
	// echo "<pre>";print_r($shift_wise_time_array);

	// ====================================== main query =====================================
	$sql="SELECT  a.entry_form,a.load_unload_id,a.floor_id,a.batch_id,a.machine_id,a.remarks,b.production_qty,a.shift_name,c.seq_no,c.machine_no,c.prod_capacity,a.batch_no,a.end_hours, a.end_minutes, a.process_end_date as PROD_DATE,a.batch_ext_no
	from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c
	where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond
	and a.id=b.mst_id and a.machine_id=c.id and a.entry_form in(35) and a.load_unload_id=2 and a.result=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.seq_no"; 
	// echo $sql;die();
	$res = sql_select($sql);
	$data_array = array();
	$qty_array = array();
	$shift_array = array();
	$machine_id_array = array();
	foreach ($res as $val) 
	{
		$qty_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']][$val['SHIFT_NAME']]['qty'] += $val['PRODUCTION_QTY'];
		$qty_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']][$val['SHIFT_NAME']]['batch_no'] = $val['BATCH_NO'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['batch_no'] = $val['BATCH_NO'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['prod_capacity'] = $val['PROD_CAPACITY'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['machine_no'] = $val['MACHINE_NO'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['seq_no'] = $val['SEQ_NO'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['remarks'] = $val['REMARKS'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['batch_ext_no'] = $val['BATCH_EXT_NO'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['unload_hours'] = $val['END_HOURS'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['unload_minutes'] = $val['END_MINUTES'];
		$data_array[$val['FLOOR_ID']][$val['MACHINE_ID']][$val['BATCH_ID']]['production_end_date'] = $val['PROD_DATE'];

		$shift_array[$val['SHIFT_NAME']] = $val['SHIFT_NAME'];
		$machine_id_array[$val['MACHINE_ID']] = $val['MACHINE_ID'];
	}
	// echo "<pre>";print_r($data_array);die();
	$machineIds = implode(",", $machine_id_array);

	// ============================== load time =================================
	$sql="SELECT  a.entry_form,a.load_unload_id,a.floor_id,a.batch_id,a.machine_id,a.remarks,b.production_qty,a.shift_name,a.batch_no,a.end_hours, a.end_minutes, a.process_end_date as prod_date,a.batch_ext_no
	from pro_fab_subprocess a, pro_fab_subprocess_dtls b
	where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond and a.machine_id in($machineIds) and a.load_unload_id=1
	and a.id=b.mst_id and a.entry_form in(35) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; //and a.load_unload_id=2
	// echo $sql;die();
	$res = sql_select($sql);
	$load_time_array = array();
	foreach ($res as $val) 
	{
		if($val[csf("load_unload_id")]==1 && $val[csf("entry_form")]==35)
		{
			$load_time_array[$val[csf("machine_id")]][$val[csf("batch_id")]][$val[csf("batch_ext_no")]]["load_end_hours"]=$val[csf("end_hours")];
			$load_time_array[$val[csf("machine_id")]][$val[csf("batch_id")]][$val[csf("batch_ext_no")]]["load_end_minutes"]=$val[csf("end_minutes")];
			$load_time_array[$val[csf("machine_id")]][$val[csf("batch_id")]][$val[csf("batch_ext_no")]]["process_start_date"]=$val[csf("prod_date")];
			$load_time_array[$val[csf("machine_id")]][$val[csf("batch_id")]][$val[csf("batch_ext_no")]]["remarks"]=$val[csf("remarks")];
		}
	}

	// ============================== get cause of machine idle ========================
	

	$sql = "SELECT b.machine_entry_tbl_id as machine_id, b.from_date, b.from_hour, b.from_minute, b.to_date, b.to_hour, b.to_minute, b.machine_idle_cause from PRO_CAUSE_OF_MACHINE_IDLE b where b.status_active=1 and b.is_deleted=0 and b.machine_entry_tbl_id in($machineIds) and b.from_date between '$date_from' and '$date_to'";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$idle_cause_array = array();
	$idle_time_array = array();
	foreach ($sql_res as $val) 
	{
		$fromDate = strtotime($val['FROM_MINUTE']);
		$toDate = strtotime($val['TO_DATE']);

		$tot_hour = 86400;
		$start = strtotime($val['FROM_HOUR'].":".$val['FROM_MINUTE']);
		$end = strtotime($val['TO_HOUR'].":".$val['TO_MINUTE']);
		$elapsed = $end - $start;


		$first_shift_start_time = 0;
		$first_shift_end_time = 0;
		$second_shift_start_time = 0;
		$second_shift_end_time = 0;
		$third_shift_start_time = 0;
		$third_shift_end_time = 0;

		$first_shift_start_time = strtotime($shift_wise_time_array[1]['start_time']);
		$first_shift_end_time = strtotime($shift_wise_time_array[1]['end_time']);

		$second_shift_start_time = strtotime($shift_wise_time_array[2]['start_time']);
		$second_shift_end_time = strtotime($shift_wise_time_array[2]['end_time']);

		$third_shift_start_time = strtotime($shift_wise_time_array[3]['start_time']);
		$third_shift_end_time = strtotime($shift_wise_time_array[3]['end_time']);

		// echo $start.">=".$first_shift_start_time ."&&". $start."<=".$first_shift_end_time."<br>";

		$shift_id = '';
		if($start>=$first_shift_start_time && $start<=$first_shift_end_time)
		{
			$shift_id=1;
		}
		elseif ($start>=$second_shift_start_time && $start<=$second_shift_end_time) 
		{
			$shift_id=2;
		}
		elseif ($start>=$third_shift_start_time && $start<=$third_shift_end_time) 
		{
			$shift_id=3;
		}


		$rest_of_time = $tot_hour - $elapsed;
		
		$idle_time = get_time($elapsed);
		$active_time = get_time($rest_of_time);
		$tot_time = get_time($tot_hour);

		$idle_cause_array[$val['MACHINE_ID']][$shift_id] = $val['MACHINE_IDLE_CAUSE'];
		$idle_time_array[$val['MACHINE_ID']]['idle_time'] = $idle_time;
		$idle_time_array[$val['MACHINE_ID']]['active_time'] = $active_time;
		$idle_time_array[$val['MACHINE_ID']]['tot_time'] = $tot_time;
	}

	// echo "<pre>";print_r($idle_cause_array);echo "</pre>";

	$tble_width = 730+(count($shift_name)*350);
	ob_start();
	?>
	<fieldset style="width: <? echo $tble_width+20;?>px;">
		<div style="text-align: center;">
			<h1><? echo $company_library[$cbo_company]; ?></h1>
			<h2>Daily Dyeing Machine wise Produciton Summary Report</h2>
			<h2>Date: <? echo $date_from;?></h2>			
		</div>

		<div>
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="<? echo $tble_width;?>" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="80" >MC Sequence</th>
						<th rowspan="2" width="100" >MC Name</th>
						<th rowspan="2" width="100" ><p>MC Capacity(KG)</p></th>
						<?
						foreach ($shift_name as $key => $val) 
						{
							?>
							<th colspan="3" width="350">Shift <? echo $val; ?></th>
							<?
						}
						?>
						<th rowspan="2" width="60">Ship Total</th>
						<th rowspan="2" width="60">UL %</th>
						<th rowspan="2" width="60">Use Hour</th>
						<th rowspan="2" width="60">Idle Hour</th>
						<th rowspan="2" width="60">Total Hour</th>
						<th rowspan="2" width="150">Remarks</th>
					</tr>
					<tr>
						<?
						foreach ($shift_name as $key => $val) 
						{
							?>
							<th width="100">Batch No</th>
							<th width="100">Batch Qty</th>
							<th width="150">Idle Cause</th>
							<?
						}
						?>
					</tr>
				</thead>				
			</table>			
		</div>
		<div style="width: <? echo $tble_width+20;?>px; overflow-y: scroll;" id="scroll_body">
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="<? echo $tble_width;?>" align="left" id="tbl_machine_wise">
				<tbody>
					<?
					$gt_shift_qty_array = array();
					foreach ($data_array as $floor_id => $floor_data) 
					{
						?>
						<tr style="font-weight: bold;font-size: 24px;background: #dccdcd">
							<td colspan="18">Floor : <? echo $floor_library[$floor_id]; ?></td>
						</tr>
						<?
						$floor_qty_array = array();
						foreach ($floor_data as $machine_id => $machine_data) 
						{
							foreach ($machine_data as $batch_id => $row) 
							{
								// load time 
								$load_hour = $load_time_array[$machine_id][$batch_id][$row['batch_ext_no']]["load_end_hours"];
								$load_minut = $load_time_array[$machine_id][$batch_id][$row['batch_ext_no']]["load_end_minutes"];
								$process_start_date = $load_time_array[$machine_id][$batch_id][$row['batch_ext_no']]["process_start_date"];
								$start_time = $load_hour.'.'.$load_minut;
								$load_hour_minut = date('h:i A', strtotime($start_time));

								// unload time
								$unload_hour = $row['unload_hours'];
								$unload_minut = $row['unload_minutes'];
								$process_end_dateIs = $row['production_end_date'];
								$end_time = $unload_hour.'.'.$unload_minut;
								$unload_hour_minut = date('h:i A', strtotime($end_time));

								$start_date_time=$process_start_date.'.'.$load_hour.'.'.$load_minut;
								$prod_start_date_time=strtotime($start_date_time);

								$end_date_time=$process_end_dateIs.'.'.$unload_hour.'.'.$unload_minut;
								$prod_end_date_time=strtotime($end_date_time);

								$diff = ($prod_end_date_time - $prod_start_date_time);
								$total = $diff/60;
								$active_time = sprintf("%02d.%02d H", floor($total/60), $total%60);

								$idle_time_dif = 86400 - $diff;
								$dif_total = $idle_time_dif/60;
								$idle_time = sprintf("%02d.%02d H", floor($dif_total/60), $dif_total%60);

								?>
								<tr>
									<td width="80"><? echo $row['seq_no']; ?></td>
									<td width="100"><? echo $row['machine_no']; ?></td>
									<td width="100"><? echo $row['prod_capacity']; ?></td>
									<?
									$tot_prop = 0;
									foreach ($shift_name as $key => $val) 
									{
										$qty = $qty_array[$floor_id][$machine_id][$batch_id][$key]['qty'];
										$batch_no = $qty_array[$floor_id][$machine_id][$batch_id][$key]['batch_no'];
										$idle_cause = $idle_cause_array[$machine_id][$key];
										?>
										<td width="100"><? echo $batch_no; ?></td>
										<td align="right" width="100"><? echo number_format($qty,0); ?></td>
										<td width="150"><? echo $cause_type[$idle_cause]; ?></td>
										<?
										$gt_shift_qty_array[$key] += $qty;
										$tot_prop += $qty;
										$floor_qty_array[$floor_id][$key] += $qty;
									}
									$ul = ($tot_prop>0) ? ($tot_prop/$row['prod_capacity'])*100 : 0;
									// $idle_time = $idle_time_array[$machine_id]['idle_time'];
									// $active_time = $idle_time_array[$machine_id]['active_time'];
									$tot_time = $idle_time_array[$machine_id]['tot_time'];
									?>
									<td width="60" align="right" ><? echo $tot_prop; ?></td>
									<td width="60" align="right" ><? echo number_format($ul,2); ?></td>
									<td width="60" align="center" ><? echo $active_time; ?></td>
									<td width="60" align="center" ><? echo $idle_time; ?></td>
									<td width="60" align="center" ><? echo $tot_time; ?></td>
									<td width="150"><? echo $row['remarks'] ?></td>
								</tr>
								<?
							}
						}
						?>
						<tr bgcolor="#cdcddc" style="text-align: right;font-weight: bold;">
							<td></td>
							<td></td>
							<td>Floor Total</td>
							<?
							$f_tot_qty = 0;
							foreach ($shift_name as $shift_key => $val) 
							{
								$f_qty = $floor_qty_array[$floor_id][$shift_key];
								$f_tot_qty += $floor_qty_array[$floor_id][$shift_key];
								?>
								<td></td>
								<td><? echo number_format($f_qty,0); ?></td>
								<td></td>
								<?
							}
							?>
							<td><? echo number_format($f_tot_qty,0); ?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?
					}
					?>
				</tbody>
			</table>
		</div>		
		<div>
			<table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="<? echo $tble_width;?>" align="left">
				<tfoot>
					<tr>
						<th width="80" ></th>
						<th width="100" ></th>
						<th width="100" >Grand Total</th>
						<?
						foreach ($shift_name as $key => $val) 
						{
							?>
							<th width="100"></th>
							<th width="100"><? echo number_format($gt_shift_qty_array[$key],0);?></th>
							<th width="150"></th>
							<?
						}
						?>
						<th width="60"></th>
						<th width="60"></th>
						<th width="60"></th>
						<th width="60"></th>
						<th width="60"></th>
						<th width="150"></th>
					</tr>
					
				</tfoot>
			</table>			
		</div>
	</fieldset>
	<?	
	$user_id = $_SESSION['logic_erp']["user_id"];
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
		
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename";
	exit();	 
} //machine wise end

if($action=="machine_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$im_data=explode('_',$data);
	// print_r ($im_data);

	$floor_cond='';
	if ($im_data[2]==0) {
		$floor_cond = "";
	}
	else{
		$floor_cond = "and a.floor_id=$im_data[2]";
	}
	?>
	<script>

		var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str )
		{

			if (str!="") str=str.split("_");

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hid_machine_id').val( id );
			$('#hid_machine_name').val( name );
		}

		function hidden_field_reset()
		{
			$('#hid_machine_id').val('');
			$('#hid_machine_name').val( '' );
			selected_id = new Array();
			selected_name = new Array();
		}
	</script>
	</head>
	<input type="hidden" name="hid_machine_id" id="hid_machine_id" />
	<input type="hidden" name="hid_machine_name" id="hid_machine_name" />
	<?
	$sql = "select a.id, a.machine_no, a.brand, a.origin, a.machine_group, b.floor_name from lib_machine_name a, lib_prod_floor b where a.floor_id=b.id and a.category_id=2 and a.company_id=$im_data[0] $floor_cond and a.status_active=1 and a.is_deleted=0 and a.is_locked=0 order by a.machine_no, b.floor_name ";
	// echo  $sql;

	echo create_list_view("tbl_list_search", "Machine Name,Machine Group,Floor Name", "200,110,110","450","350",0, $sql , "js_set_value", "id,machine_no", "", 1, "0,0,0", $arr , "machine_no,machine_group,floor_name", "",'setFilterGrid(\'tbl_list_search\',-1);','0,0,0','',1) ;

	exit();
}

		if($action=="color_name_suggestion")
		{
			$search_color = $_POST['color_name'];
			$color_sql = "SELECT id, color_name from lib_color where color_name like '$search_color%' and status_active=1 and is_deleted=0 order by color_name";
			$search_color_arr=sql_select($color_sql);
			echo '<ul>';
			foreach ($search_color_arr as $value)
			{
				$color_name = $value[csf('color_name')];
				$color_name2 = "'$color_name'";
				?>
				<li onClick="set_color_id(<? echo $value[csf('id')];?>,<? echo $color_name2; ?>)"><? echo $value[csf('color_name')];?></li>
				<?
			}
			echo '</ul>';
		}

				if ($action == "fabricBooking_popup")
				{
					echo load_html_head_contents("WO Info", "../../../", 1, 1,'','','');
					extract($_REQUEST);
	//$im_data=explode('_',$data);
	//print_r ($im_data);

					$width = 1055;
					?>
					<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
					<script>
						<?
						$data_arr = json_encode($_SESSION['logic_erp']['data_arr'][98]);
						echo "var field_level_data= " . $data_arr . ";\n";
						?>
						window.onload = function () {
							set_field_level_access( <? echo $cbo_company_id; ?> );
						}
						function js_set_value(id, booking_no, type)
						{
			//alert(id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_booking_id').val(id);
			$('#booking_without_order').val(type);
			parent.emailwindow.hide();
		}

		function company_validation()
		{
			var cbo_lc_company_id = $('#cbo_lc_company_id').val();
			if (cbo_lc_company_id==0)
			{
				if (form_validation('cbo_lc_company_id','Company')==false)
				{
					alert('Please Select LC Company');
					return;
				}
			}
			else
			{
				show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_lc_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'machine_wise_daily_dyeing_prod_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
			}
		}

	</script>

</head>

<body>
	<div align="center" style="width:<? echo $width; ?>px;">
		<form name="searchwofrm" id="searchwofrm" autocomplete=off>
			<fieldset style="width:<? echo $width - 5; ?>px; margin-left:2px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="750" class="rpt_table">
					<thead>
						<th>LC Company</th>
						<th>Year</th>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="200">Enter Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
							value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes"
							value="">
							<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes"
							value="">
							<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes"
							value="">
						</th>
					</thead>
					<tr>
						<td>
							<?
							echo create_drop_down( "cbo_lc_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "" );
							?>
						</td>
						<td align="center">
							<?
							echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
							?>
						</td>
						<td align="center">
							<?
							echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", '', "", $disable);
							?>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Booking Date", 5 => "Internal Ref", 6 => "File No");
							$dd = "change_search_event(this.value, '0*0*0*3*0*0', '0*0*0*2*0*0', '../../') ";
							$selected = 1;
							$disable = 0;
							echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", $selected, $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="company_validation();"
							style="width:100px;"/>
						</td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
</html>
<?
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	//print_r($data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$buyer_id = $data[3];
	$booking_year = $data[4];

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	if ($buyer_id == 0) $buyer_id = "%%";

	if (trim($data[0]) != "")
	{
		if ($search_by == 1)
		{
			$search_field_cond = "and a.booking_no like '$search_string'";
			$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
		}
		else if ($search_by == 2)
		{
			$search_field_cond = "and b.po_number like '$search_string'";
			$search_field_cond_sample = "";
		}
		else if ($search_by == 3)
		{
			$search_field_cond = "and b.job_no_mst like '$search_string'";
			$search_field_cond_sample = "";
		}
		else if ($search_by == 5)
		{
			$search_field_cond = "and b.grouping like '$search_string'";
			$search_field_cond_sample = "";
		}
		else if ($search_by == 6)
		{
			$search_field_cond = "and b.file_no like '$search_string'";
			$search_field_cond_sample = "";
		}
		else
		{
			if ($db_type == 0) {
				$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
				$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
			} else {
				$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
				$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
			}
		}
	}
	else
	{
		$search_field_cond = "";
		$search_field_cond_sample = "";
	}
	$po_arr = array();
	$po_data = sql_select("select b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($po_data as $row)
	{
		$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
	}
	$year_cond = "";
	$year_cond_non_order = "";
	$booking_year_condition="";
	$booking_year_non_order_condition="";

	if ($db_type == 0)
	{
		$year_cond = "YEAR(a.insert_date) as year";
		$year_cond_non_order = "YEAR(s.insert_date) as year";
		if($booking_year>0)
		{
			$booking_year_condition=" and YEAR(a.insert_date)=$booking_year";
			$booking_year_non_order_condition=" and YEAR(s.insert_date)=$booking_year";
		}
	}
	else if ($db_type == 2)
	{
		$year_cond = "to_char(a.insert_date,'YYYY') as year";
		$year_cond_non_order = "to_char(s.insert_date,'YYYY') as year";
		if($booking_year>0)
		{
			$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$booking_year";
			$booking_year_non_order_condition=" and to_char(s.insert_date,'YYYY')=$booking_year";
		}
	}

 		// check variable settings if allocation is available or not
		/*$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company_id and variable_list=18 and item_category_id = 1");
		$booking_type_cond = ($variable_set_allocation==1)?" and a.booking_type not in(1,4)":"";*/
		if (trim($data[0]) != "" && ($search_by == 2 || $search_by == 3 || $search_by == 5 || $search_by == 6))
		{
			$sql = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id,c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,a.booking_type, a.is_short, $year_cond
			from wo_booking_mst a,wo_booking_dtls c, wo_po_break_down b
			where a.booking_no=c.booking_no and c.po_break_down_id=b.id and a.company_id=$company_id and a.buyer_id like '$buyer_id' and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $booking_year_condition
			group by a.id,a.booking_no, a.booking_date, a.buyer_id,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num, a.booking_type,a.is_short
			order by a.id";
		}
		else
		{
			$sql = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id,c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,a.booking_type, a.is_short, $year_cond
			from wo_booking_mst a,wo_booking_dtls c, wo_po_break_down b
			where a.booking_no=c.booking_no and c.po_break_down_id=b.id and a.company_id=$company_id and a.buyer_id like '$buyer_id' and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $booking_year_condition group by a.id,a.booking_no, a.booking_date, a.buyer_id,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num,a.booking_type, a.is_short
			union all
			SELECT s.id, s.booking_no,s.booking_no_prefix_num, s.booking_date, s.buyer_id, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type, 0 as booking_type, 0 as is_short, $year_cond_non_order
			FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t
			WHERE s.booking_no=t.booking_no and s.company_id=$company_id and s.buyer_id like '$buyer_id' and s.status_active =1 and s.is_deleted=0 and s.item_category=2 and (s.fabric_source=1 OR t.fabric_source=1) $search_field_cond_sample $booking_year_non_order_condition
			group by s.id, s.booking_no, s.booking_no_prefix_num, s.booking_date, s.buyer_id, s.item_category, s.delivery_date, s.insert_date
			order by id,type desc";
		}
		//echo $sql;
		$result = sql_select($sql);
		$po_id_arr = $booking_arr = array();
		$job_ids = "";
		foreach ($result  as $value)
		{
			$po_id_arr[$value[csf("booking_no")]] .= $value[csf("po_break_down_id")] . ",";
			$booking_arr[$value[csf("booking_no")]] = $value[csf("id")] . "**" . $value[csf("booking_no")] . "**" . $value[csf("booking_no_prefix_num")] . "**" . $value[csf("booking_date")] . "**" . $value[csf("buyer_id")] . "**" . $value[csf("item_category")] . "**" . $value[csf("delivery_date")] . "**" . $value[csf("job_no_mst")] . "**" . $value[csf("type")] . "**" . $value[csf("booking_type")] . "**" . $value[csf("is_short")] . "**" . $value[csf("year")];
		}
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Booking No</th>
				<th width="70">Type</th>
				<th width="50">Year</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="88">Item Category</th>
				<th width="75">Delivary date</th>
				<th width="80">Job No</th>
				<th width="70">Order Qnty</th>
				<th width="75">Shipment Date</th>
				<th width="130">Order No</th>
				<th width="70">Internal Ref</th>
				<th>File No</th>
			</thead>
		</table>
		<div style="width:1050px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			foreach ($booking_arr as $row)
			{
				$data = explode("**",$row);
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				$booking_type = '';
				if ($data[9] == 0) {
					$booking_type = 'Sample Without Order';
				} else if ($data[9] == 4) {
					$booking_type = 'Sample';
				} else {
					if ($data[10] == 1) $booking_type = 'Short'; else $booking_type = 'Main';
				}

				$po_qnty_in_pcs = '';
				$po_no = '';
				$min_shipment_date = '';
				$internal_ref = '';
				$file_nos = '';
				if ($data[1] != "" && $data[8] == 0)
				{
					$po_id = explode(",", rtrim($po_id_arr[$data[1]],","));
					foreach ($po_id as $id) {
						$po_data = explode("**", $po_arr[$id]);
						$po_number = $po_data[0];
						$pub_shipment_date = $po_data[1];
						$po_qnty = $po_data[2];
						$poQntyPcs = $po_data[3];
						$grouping = $po_data[4];
						$file_no = $po_data[5];

						if ($po_no == "") $po_no = $po_number; else $po_no .= "," . $po_number;
						if ($grouping != "") {
							if ($internal_ref == "") $internal_ref = $grouping; else $internal_ref .= "," . $grouping;
						}
						if ($file_no != "") {
							if ($file_nos == "") $file_nos = $file_no; else $file_nos .= "," . $file_no;
						}

						if ($min_shipment_date == '') {
							$min_shipment_date = $pub_shipment_date;
						} else {
							if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
						}

						$po_qnty_in_pcs += $poQntyPcs;
					}
				}

				$internal_ref = implode(",", array_unique(explode(",", $internal_ref)));
				$file_nos = implode(",", array_unique(explode(",", $file_nos)));
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value(<? echo $data[0]; ?>,'<? echo $data[1]; ?>','<? echo $data[8]; ?>','');">
					<td width="30"><? echo $i; ?></td>
					<td width="60" align="left"><p><? echo $data[2]; ?></p>
					</td>
					<td width="70" align="center"><p><? echo $booking_type; ?></p></td>
					<td width="50" align="center"><p><? echo $data[11]; ?></p></td>
					<td width="75" align="center"><? echo change_date_format($data[3]); ?></td>
					<td width="60"><p><? echo $buyer_arr[$data[4]]; ?></p></td>
					<td width="88"><p><? echo $item_category[$data[5]]; ?></p></td>
					<td width="75" align="center"><? echo change_date_format($data[6]); ?></td>
					<td width="80"><p><? echo $data[7]; ?></p></td>
					<td width="70" align="right"><? echo $po_qnty_in_pcs; ?></td>
					<td width="75" align="center"><? echo change_date_format($min_shipment_date); ?></td>
					<td width="130"><p><? echo $po_no; ?></p></td>
					<td width="70"><p><? echo $internal_ref; ?></p></td>
					<td><p><? echo $file_nos; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
}
?>
