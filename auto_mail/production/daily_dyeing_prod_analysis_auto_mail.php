<?php
	date_default_timezone_set("Asia/Dhaka");
	extract($_REQUEST);
	
	require_once('../../includes/common.php');
	require_once('../../mailer/class.phpmailer.php');
	require_once('../setting/mail_setting.php');

 
	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",time());
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",time()),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	}
	
	//echo $current_date;die;
	
	
	$txt_date_from=$previous_date;
	$txt_date_to=$previous_date;
    $type=0;
   

    $company_library=return_library_array( "select id,company_name from lib_company where 1=1", "id", "company_name");
    $batch_quantity_arr=return_library_array( "select a.id,sum(b.batch_qnty) as  batch_qnty from  pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id", "id", "batch_qnty");

    
    $machine_cap_array=array();
    $mach_cap_sql=sql_select("select id, machine_no, sum(prod_capacity) as prod_capacity from lib_machine_name where status_active=1 and is_deleted=0 group by id, machine_no");
    foreach($mach_cap_sql as $row)
    {
        $machine_cap_array[$row[csf('id')]]["machine_no"]=$row[csf('machine_no')];
        $machine_cap_array[$row[csf('id')]]["prod_capacity"]=$row[csf('prod_capacity')];
    }
    

    
function convertMinutes2Hours($Minutes)
{
    if ($Minutes < 0)
    {
        $Min = Abs($Minutes);
    }
    else
    {
        $Min = $Minutes;
    }
    $iHours = Floor($Min / 60);
    $Minutes = ($Min - ($iHours * 60)) / 100;
    $tHours = $iHours + $Minutes;
    if ($Minutes < 0)
    {
        $tHours = $tHours * (-1);
    }
    $aHours = explode(".", $tHours);
    $iHours = $aHours[0];
    if (empty($aHours[1]))
    {
        $aHours[1] = "00";
    }
    $Minutes = $aHours[1];
    if (strlen($Minutes) < 2)
    {
        $Minutes = $Minutes ."0";
    }
    $tHours = $iHours .":". $Minutes;
    return $tHours;
}



	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
		$buyer_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
		$gsm_library=return_library_array( "select id,gsm from  product_details_master", "id", "gsm");
		
		$order_arr=array();
		$sql_order="Select b.id as ID, a.job_no as JOB_NO, a.buyer_name as BUYER_NAME, a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, b.po_quantity as PO_QUANTITY from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$result_sql_order=sql_select($sql_order);
		foreach($result_sql_order as $row)
		{
			$order_arr[$row['ID']]['job_no']=$row['JOB_NO'];
			$order_arr[$row['ID']]['buyer_name']=$row['BUYER_NAME'];
			$order_arr[$row['ID']]['style_ref_no']=$row['STYLE_REF_NO'];
			$order_arr[$row['ID']]['po_number']=$row['PO_NUMBER'];
			$order_arr[$row['ID']]['po_quantity']=$row['PO_QUANTITY'];
		}
		unset($result_sql_order);

		$non_order_arr=array();
		$sql_non_order="SELECT a.company_id, a.buyer_id as BUYER_NAME, b.booking_no as BOOKING_NO, b.bh_qty as BH_QTY
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
		where a.booking_no=b.booking_no and a.booking_type=4 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		$result_sql_non_order=sql_select($sql_non_order);
		foreach($result_sql_non_order as $row)
		{
			$non_order_arr[$row[csf('BOOKING_NO')]]['buyer_name']=$row[csf('BUYER_NAME')];
			$non_order_arr[$row[csf('BOOKING_NO')]]['bh_qty']=$row[csf('BH_QTY')];
		}
		unset($result_sql_non_order);

		

 
 
 foreach($company_library as $cbo_company_id => $company_name){
	 
	$cbo_company=str_replace("'","",$cbo_company_id);
    $date_from=str_replace("'","",$txt_date_from);
    $date_to=str_replace("'","",$txt_date_to);


    $lc_company_cond="and a.company_id in($cbo_company)";
    $lc_company_cond_issue="and c.company_id in($cbo_company)";
	$lc_company_cond_issue2="and c.lc_company in($cbo_company)";
   
	if($date_from!="" && $date_to!="" )
	{
		$date_cond="and a.process_end_date between '$date_from' and '$date_to'";
	}

   
 	$load_data=array();
	$load_time_data=sql_select("select a.id,a.batch_id,a.batch_no,a.load_unload_id,a.process_end_date,a.end_hours,a.end_minutes from pro_fab_subprocess a where a.load_unload_id=1 and a.entry_form=35 and  a.status_active=1  and a.is_deleted=0 $working_company_cond $lc_company_cond");
foreach($load_time_data as $row_time)// for Loading time
{
	$load_data[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
	$load_data[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
	$load_data[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
}  
   
   
    
ob_start(); 
    //$table_width=90+($datediff*160);
    
?>
    <div>
        <table width="2250px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:18px"><? echo $company_library[$cbo_company];?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px">Daily Dyeing Produciton Analysis Report</strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="25" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
        <?
        


        if($type==0)
        {
            
            ?>
            <style>
			span::before {content: '\A'; white-space: pre;}
			</style>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Self Dyeing Production</i></u></strong></div>
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                    <thead>
                        <th width="40">SL</th>
                        <th width="110">WO/Booking No</th>
                        <th width="90">Job No</th>
                        <th width="120">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                        <th width="140">Order No</th>
                        <th width="100">Order Qty.</th>
                        <th width="60">GSM</th>
                        <th width="100">Fabric Color</th>
                        <th width="100">Batch No/ Lot No.</th>
                        <th width="50">Ext. No</th>
                        <th width="100">MC No.</th>
                        <th width="90">MC Capacity</th>
                        <th width="70">Dyeing Date</th>
                        <th width="100">Dyeing Qty</th>
                        <th width="60">UL %</th>
                        <th width="60">Hour Req.</th>
                        <th width="80">Loading Time</th>
                        <th width="80">Unloading Time</th>
                        <th width="100">Liquor Ratio</th>
                        <th width="70">Hour Used</th>
                        <th width="60">Hour Devi.</th>                
                        <th width="90">Process/ Color Range</th>
                        <th width="110">Total Dye Cost</th>
                        <th width="90">Total Dye Cost/kg</th>
                        <th width="110">Total Chem Cost</th>
                        <th width="90">Total Chem Cost/Kg</th>
                        <th width="130">Dyeing Company</th>
                        <th>Remarks</th>
                    </thead>
                <?  
                
                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
                    from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
                    where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against in(1,2) and b.is_sales!=1   $batch_cond $book_cond $floor_cond $working_company_cond $lc_company_cond   $date_cond
                    group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
                    from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
                    where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(1,2) and b.is_sales!=1 $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond 
                    group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
             // echo $sql_dtls;die;

                $sql_result=sql_select($sql_dtls);
                if(!empty($sql_result))
                {
                    // ================== getting batch id =========================
                    $batch_id_array = array();
					$all_prod_id="";
                    foreach ( $sql_result as $row )
                    {
                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
                        $fb_booking_no=$booking_no_arr[1];
                        if($fb_booking_no=='FB' || $fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
                        {
                        $self_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['po_id']= $row[csf('po_id')];
                        $self_batch_wise_array[$row[csf('batch_id')]]['qnty']= $row[csf('qnty')];
						if($all_prod_id=="") $all_prod_id=$row[csf('prod_id')];else $all_prod_id.=",".$row[csf('prod_id')];
                        }
                    }
					$sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$result_data_recipe=sql_select($sql_recipe);
				foreach ($result_data_recipe as $row)
				{
					$batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				

                
                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();

                 //$chem_dye_cost_sql="SELECT a.mst_id, b.batch_id, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c, tmp_batch_or_iss d where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 and b.batch_id= d.batch_issue_no and d.userid=$user_id and d.block=1 and d.type=1 $working_company_cond_issue $lc_company_cond_issue2 group by a.mst_id, b.batch_id ";
					
				$chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1   $lc_company_cond_issue2 $working_company_cond_issue  group by a.mst_id, b.batch_id,a.item_category ";

                    //echo $chem_dye_cost_sql;die;

                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                    //var_dump($chem_dye_cost_result);die;
                    
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                            
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
                            
                        }
                        
                    }
                    //var_dump($chem_dye_cost_kg_array);//die;

                    // ============================ getting issue return amount =============================
                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";
                    if(count($all_issue_id)>0)
                    {
                        
                        foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
                        {
                            $r_id3=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")");
                            if($r_id3) 
                            {
                                $r_id3=1;
                            } 
                            /*else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,1".")";
                                oci_rollback($con);
                                die;
                            }*/
                        }

                        if($r_id3)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }
                        
                        //and a.issue_id in($allIssueIds)
                        //$sql_return_cost="SELECT p.batch_no as BATCH_ID, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.userid=$user_id and c.block=1 and c.type=2 group by p.batch_no"; 

                        $sql_return_cost="SELECT p.batch_no as BATCH_ID,a.ITEM_CATEGORY, sum(a.cons_amount) as CONS_AMOUNT  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond  and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.userid=$user_id and c.block=1 and c.type=2 group by p.batch_no,a.item_category"; 

                        // echo $sql_return_cost;die();
                        $cost_return_data_arr=array();
                        $sql_return_res=sql_select($sql_return_cost);
                        foreach ($sql_return_res as $row)
                        {
                            $cost_return_data_arr[$row['BATCH_ID']][$row['ITEM_CATEGORY']]+=$row['CONS_AMOUNT'];
                            // $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                        }
                    }
                }
                

                // print_r($cost_return_data_arr);
                $tot_rows=count($sql_result);
                unset($sql_result);
                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=0;$total_chem_cost_kg=0;
                foreach ( $self_batch_wise_array as $batch_id=>$row )
                {
                    
					$sub_liquor_ratio_all='';
					$sub_process_id=rtrim($batch_recipe_arr[$batch_id]['sub_process_id'],',');
					$sub_process_ids=array_unique(explode(",",$sub_process_id));
					foreach($sub_process_ids as $sid)
					{
						$liquor_ratio=$batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
						if($liquor_ratio>0)
						{
							$sub_liquor_ratio_all.=$liquor_ratio.',';
						}
						
					}
					
					
					$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
					$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
					$sub_liquor_ratio=$sub_liquor_ratio_allArr[0];
					/////-----------
					
					$start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']);
					$end_time=strtotime($row[('process_end_date')]." ".$row[('end_hours')].':'.$row[('end_minutes')]);
					$timeDiffin=($end_time-$start_time)/60; 
					
					if($timeDiffin>0)
					{
						 $time_used=convertMinutes2Hours($timeDiffin); 
						 list($hour,$minute)=explode(':',$time_used);
					}
					
					if($hour>8 || $sub_liquor_ratio>8){

						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_id_arr=array_unique(explode(",",$row[('po_id')])); 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td><? echo $i; ?></td>
							<td><p><? echo $row[('booking_no')]; ?></p></td>
							<td align="center"><p>
							<?
							$job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
							foreach($po_id_arr as $po_id)
							{
								if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
								{
									$job_chech[]=$order_arr[$po_id]['job_no'];
									if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
									if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
									if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
								}
								if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
								$po_qnty +=$order_arr[$po_id]['po_quantity'];
							}
							echo $job_all; 
							$sub_process_id=rtrim($batch_recipe_arr[$batch_id]['sub_process_id'],',');
							$sub_process_ids=array_unique(explode(",",$sub_process_id));
							$sid_ratio_all='';$sub_liquor_ratio_all='';
							
							foreach($sub_process_ids as $sid)
							{
								$liquor_ratio=$batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
								if($liquor_ratio>0)
								{
								$seq_no=$batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
								$sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
								
								$sub_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
								}
								
							}
							$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
							$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
							$sub_liquor_ratio=$sub_liquor_ratio_allArr[0];
								
							?></p></td>
							<td><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
							<td><p><? echo $style_all;  ?></p></td>
							<td><p><? echo $po_all;?></p></td>
							<td align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
							<td align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
							<td><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
							<td><p><? echo $row[('batch_no')]; ?></p></td>
							<td><p><? echo $row[('extention_no')]; ?></p></td>
							<td align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
							<td align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
							<td align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
							<td align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_dyeing_qnty+=$row[('qnty')]; ?></p></td>
							<td align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
							<td align="center"><p><? $req_hour=""; $req_hour=$row['dur_req_hr'].":".$row['dur_req_min']; echo $req_hour; ?></p></td>
							<td align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
							
							<td align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
							 <td align="center" id="title_td" title="<? echo $sid_ratio_all;?>"><p><?  echo $sub_liquor_ratio; ?></p></td>
							<td align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
							
							if($timeDiffin>0)
							{
								 $time_used=convertMinutes2Hours($timeDiffin); 
								 echo number_format($time_used,2); // 
							}
							else
							{
								echo "Invalid";
							}
							 ?></p></td>
							<?
							
							$req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
							$hour_dev_cal=0;$tot_deviation="";
							$time_used = number_format($time_used,2);
							// echo $req_time."--".$time_used."<br>";
							if($req_time!="")
							{
								$used_time=strtotime($time_used.':'. 00);
								$hour_dev_cal=($used_time-$req_time)/60;
								$tot_deviation=convertMinutes2Hours($hour_dev_cal); 
							}
							if($hour_dev_cal>0)
							{
								?>
								<td align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
								<?
							}
							else
							{
								?>
								<td align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
								<?
							}
							?>
							<td><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>
	
							<td align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6]; echo number_format($dye_cost,4,'.','');?></p></td>
							<td align="right" title="Total Dye Cost/Dyeing Qty"><p><? $tot_dye_cost_kg=$dye_cost/$row[('qnty')];echo number_format($tot_dye_cost_kg,4,'.','');
							$total_dye_cost+=$dye_cost;$total_dye_cost_kg+=$tot_dye_cost_kg;?></p></td>
	
							<td align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $chemi_cost =$chem_dye_cost_array[$row[('batch_id')]][5] - $cost_return_data_arr[$row[('batch_id')]][5]; echo number_format($chemi_cost,4,'.','');?></p></td>                        
							<td align="right" title="Total Dye Chemical Cost/Dyeing Qty"><p><? $tot_chemi_cost_kg=$chemi_cost/$row[('qnty')];echo number_format($tot_chemi_cost_kg,4,'.','');
							$total_chem_cost+=$chemi_cost;$total_chem_cost_kg+=$tot_chemi_cost_kg;?> </p></td>
	
							<td align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
							<td><p><? echo $row[('remarks')]; ?></p></td>
						</tr>
						<?
						$i++;
					}
                    //$total_chem_dye_cost+=$chem_dye_cost;
                } 
                ?>
                    <tfoot>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th><strong>Total</strong></th>
                        <th id="value_total_dyeing_qnty" align="right"><? echo number_format($total_dyeing_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th id="value_total_dye_cost"><? echo number_format($total_dye_cost,4,'.',''); ?></th>
                        <th title="Tot Dye Cost/Tot dying Qty" id=""><? echo number_format(($total_dye_cost/$total_dyeing_qnty),4,'.',''); ?></th>
                        <th id="value_total_chem_cost" align="right"><? echo number_format($total_chem_cost,4,'.',''); ?></th>
                        <th align="right"><? echo number_format(($total_chem_cost/$total_dyeing_qnty),4,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        if($type==0)
        {
            ?>
            <br />
            <div>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:320px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>In-bound Subcontract Dyeing Production</i></u></strong></div>
                <table width="2470" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                    <thead>
                    <th width="40">SL</th>
                    <th width="90">Job No</th>
                    <th width="120">Party Name</th>
                    <th width="130">Style Ref.</th>
                    <th width="140">Order No</th>
                    <th width="100">Order Qty.</th>
                    <th width="100">Batch No</th>
                    <th width="50">Ext. No</th>
                    <th width="100">Batch Color</th>
                    <th width="100">MC No.</th>
                    <th width="90">MC Capacity</th>
                    <th width="70">Dyeing Date</th>
                    <th width="100">Dyeing Qty</th>
                    <th width="60">UL %</th>
                    <th width="60">Hour Req.</th>
                    <th width="80">Loading Time</th>
                    <th width="80">Unloading Time</th>
                    <th width="100">Liquor Ratio</th>
                    <th width="70">Hour Used</th>
                    <th width="60">Hour Devi.</th>                
                    <th width="90">Process/ Color Range</th>
                    <th width="110">Total Dye Cost</th>
                    <th width="90">Total Dye Cost/Kg</th>
                    <th width="110">Total Chem Cost</th>
                    <th width="90">Total Chem Cost/Kg</th>
                    <th width="130">Dyeing Company</th>
                    <th>Remarks</th>
                    </thead>
           
                <?  
                $buyer_library_subcon=return_library_array( "select a.id,a.short_name from lib_buyer a, lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(2,3)", "id", "short_name");
                //$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
                $machine_library=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
                
                $job_arr=array();
                $sql_job="Select b.id, a.subcon_job, a.party_id, b.cust_style_ref, b.order_no, b.order_quantity from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                $result_sql_job=sql_select($sql_job);
                foreach($result_sql_job as $row)
                {
                    $job_arr[$row[csf('id')]]['job_no']=$row[csf('subcon_job')];
                    $job_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
                    $job_arr[$row[csf('id')]]['style_ref_no']=$row[csf('cust_style_ref')];
                    $job_arr[$row[csf('id')]]['order_no']=$row[csf('order_no')];
                    $job_arr[$row[csf('id')]]['order_quantity']=$row[csf('order_quantity')];
                }
                
                $load_data_sub=array();
                $load_time_data_sub=sql_select("select a.id, a.batch_id, a.batch_no, a.process_end_date, a.load_unload_id, a.end_hours, a.end_minutes from pro_fab_subprocess  a where a.load_unload_id=1 and a.entry_form=38 and status_active=1  and is_deleted=0 $working_company_cond $lc_company_cond");
                
                foreach($load_time_data_sub as $row_time)// for Loading time
                {
                    $load_data_sub[$row_time[csf('batch_id')]]['process_end_date']=$row_time[csf('process_end_date')];
                    $load_data_sub[$row_time[csf('batch_id')]]['end_hours']=$row_time[csf('end_hours')];
                    $load_data_sub[$row_time[csf('batch_id')]]['end_minutes']=$row_time[csf('end_minutes')];
                }
                unset($load_time_data_sub);
                
                
               
            
                if($db_type==0)
                {
                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company, a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id), group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c WHERE a.batch_id=b.id and a.entry_form=38 and a.load_unload_id=2 and b.id=c.mst_id   and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond $floor_cond  $batch_cond $book_cond   $working_company_cond $lc_company_cond GROUP BY b.id, b.extention_no,a.batch_id, a.company_id,a.service_company, a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_sub_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company, a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id), LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty FROM pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c WHERE a.batch_id=b.id and a.entry_form=38 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $batch_cond $book_cond $date_cond $floor_cond  $lc_company_cond $working_company_cond GROUP BY b.id, b.extention_no,a.batch_id, a.company_id,a.service_company, a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                //echo $sql_sub_dtls;die;
                $sql_sub_result=sql_select($sql_sub_dtls);
                $tot_rows=count($sql_sub_result);
                if($type==2)
                {
                    $batchIds = "'" . implode ( "', '", $sub_batch_id_array ) . "'";
                    $baIds=chop($batchIds,','); $batch_cond_in="";
                    $ba_ids=count(array_unique(explode(",",$batchIds)));
                    if($db_type==2 && $ba_ids>1000)
                    {
                    $batch_cond_in=" and (";
                    $baIdsArr=array_chunk(explode(",",$baIds),999);
                    foreach($baIdsArr as $ids)
                    {
                    $ids=implode(",",$ids);
                    $batch_cond_in.=" b.batch_id in($ids) or"; 
                    }
                    $batch_cond_in=chop($batch_cond_in,'or ');
                    $batch_cond_in.=")";
                    }
                    else
                    {
                    $batch_cond_in=" and b.batch_id in($baIds)";
                    }

                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array();
                    $chem_dye_cost_kg_array=array();

                    $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $working_company_cond_issue $batch_cond_in $lc_company_cond_issue group by a.mst_id, b.batch_id,a.item_category ";

                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                            
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
                            
                        }
                    }

                    // ============================ getting issue return amount =============================
                   
                }
                // Issue end
				$sub_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=38  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$sub_result_data_recipe=sql_select($sub_sql_recipe);
				foreach ($sub_result_data_recipe as $row)
				{
					$sub_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($sub_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$sub_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				
            
                $k=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=0;
                foreach ( $sql_sub_result as $row )
                {
                   
					$batch_id=$row[csf('batch_id')];
					$sub_process_id=rtrim($sub_batch_recipe_arr[$batch_id]['sub_process_id'],',');
					$sub_process_ids=array_unique(explode(",",$sub_process_id));
					$sub_sid_ratio_all='';$sub_liquor_ratio_all='';
					foreach($sub_process_ids as $sid)
					{
						$sub_liquor_ratio=$sub_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
						if($sub_liquor_ratio>0)
						{
						$sub_liquor_ratio_all.=$sub_liquor_ratio.',';
						}
						
					}
					$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
					$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
					$sub_liquor_ratio=$sub_liquor_ratio_allArr[0];
					  //echo $sub_liquor_ratio.','; 
					
					///////-------------
					$end_time=strtotime($row[csf('process_end_date')]." ".$row[csf('end_hours')].':'.$row[csf('end_minutes')]);
					$start_time=strtotime($load_data_sub[$row[csf('id')]]['process_end_date']." ".$load_data_sub[$row[csf('id')]]['end_hours'].':'.$load_data_sub[$row[csf('id')]]['end_minutes']);
					
					$timeDiffin=($end_time-$start_time)/60; 
					
					if($timeDiffin>0)
					{
						 $time_used=convertMinutes2Hours($timeDiffin); 
						 list($hour,$minute)=explode(':',$time_used);
					}				   
				   
				
				 if($hour>8 || $sub_liquor_ratio>8){  
				   
				    if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row[csf('po_id')])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color_sub('row_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="row_<? echo $k; ?>">
                        <td><? echo $k; ?></td>
                        <td align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($job_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$job_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$job_arr[$po_id]['job_no']; else $job_all .=", ".$job_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$job_arr[$po_id]['party_id']; else $buyer_all .=", ".$job_arr[$po_id]['party_id'];
                                if($style_all=="") $style_all=$job_arr[$po_id]['style_ref_no']; else $style_all .=", ".$job_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$job_arr[$po_id]['order_no']; else $po_all .=", ".$job_arr[$po_id]['order_no'];
                            $po_qnty +=$job_arr[$po_id]['order_quantity'];
                        }
                        echo $job_all;
						
						$batch_id=$row[csf('batch_id')];
						
						$sub_process_id=rtrim($sub_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						$sub_sid_ratio_all='';$sub_liquor_ratio_all='';
						foreach($sub_process_ids as $sid)
						{
							$sub_liquor_ratio=$sub_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($sub_liquor_ratio>0)
							{
							$seq_no=$sub_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$sub_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$sub_liquor_ratio.'&#013;';
							
							$sub_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$sub_liquor_ratio.',';
							}
							
						}
						$sub_liquor_ratio_all=rtrim($sub_liquor_ratio_all,',');
						$sub_liquor_ratio_allArr=array_unique(explode(",",$sub_liquor_ratio_all));
						$sub_liquor_ratio=$sub_liquor_ratio_allArr[0]; 
                        ?></p></td>
                        <td><p><? echo $buyer_library_subcon[$buyer_all]; ?></p></td>
                        <td><p><? echo $style_all;  ?></p></td>
                        <td><p><? echo $po_all;?></p></td>
                        <td align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td><p><? echo $row[csf('batch_no')]; ?></p></td>
                        <td><p><? echo $row[csf('extention_no')]; ?></p></td>
                        <td><p><? echo $color_library[$row[csf('color_id')]]; ?></p></td>
                        <td align="center"><p><? echo $machine_cap_array[$row[csf('machine_id')]]["machine_no"]; ?></p></td>
                        <td align="right"><p><? $machine_capacity=$machine_cap_array[$row[csf('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td align="right"><p><? echo change_date_format($row[csf('production_date')]); ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('qnty')],2,'.',''); $total_sub_dyeing_qnty+=$row[csf('qnty')]; ?></p></td>
                        <td align="right"><p><? $ul_percent=$row[csf('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td align="center"><p><? $req_hour=""; $req_hour=$row[csf('dur_req_hr')].":".$row[csf('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td align="center"><p><? $start_time=''; $start_time=$load_data_sub[$row[csf('id')]]['end_hours'].':'.$load_data_sub[$row[csf('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td align="center"><p><? $end_time=''; $end_time=$row[csf('end_hours')].':'.$row[csf('end_minutes')]; echo $end_time; ?></p></td>
                         <td align="center" title="<? echo $sub_sid_ratio_all;?>"><p><?  echo $sub_liquor_ratio; ?></p></td>
                         
                        <td align="right"><p><? $start_time=strtotime($load_data_sub[$row[csf('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[csf('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                         if($timeDiffin>0)
                         {  
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used ;
                         }
                         else
                         {
                             echo "Invalid";
                         }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[csf('dur_req_hr')].":".$row[csf('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td><p><? echo $color_range[$row[csf('color_range_id')]]; ?></p></td>

                        <td align="right"><p><? $subcon_dye_cost =$chem_dye_cost_array[$row[csf('batch_id')]][6]; echo number_format($subcon_dye_cost,4,'.',''); ?></p></td>
                        <td align="right" title="Total Dye Cost/Dyeing Qty"><p><?  $subcon_dye_cost_kg=$subcon_dye_cost/$row[csf('qnty')];
                        echo number_format($subcon_dye_cost_kg,4,'.','');
                        $total_sub_dye_cost+=$subcon_dye_cost;$total_sub_dye_cost_kg+=$subcon_dye_cost_kg; ?></p></td>

                        <td align="right"><p><? $subcon_chem_cost =$chem_dye_cost_array[$row[csf('batch_id')]][5]; echo number_format($subcon_chem_cost,4,'.',''); ?></p></td>
                        <td align="right" title="Total Chemical Cost/Dyeing Qty"><p><?  $subcon_chemi_cost_kg=$subcon_chem_cost/$row[csf('qnty')];
                        echo number_format($subcon_chemi_cost_kg,4,'.','');
                        $total_sub_chem_cost+=$subcon_chem_cost;$total_sub_chem_cost_kg+=$subcon_chemi_cost_kg; ?></p></td>

                        <td align="center"><p><? echo $company_library[$row[csf('service_company')]]; ?></p></td>
                        <td><p><? echo $row[csf('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $k++;
				 }
                } 
                ?>
                    <tfoot>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th><strong>Total</strong></th>
                        <th id="value_total_sub_dyeing_qnty" align="right"><? echo number_format($total_sub_dyeing_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th><? echo number_format($total_sub_dye_cost,4,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_sub_dye_cost/$total_sub_dyeing_qnty,4,'.',''); ?></th>
                        <th><? echo number_format($total_sub_chem_cost,4,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_sub_chem_cost/$total_sub_dyeing_qnty,4,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
            </div>     
            <?
        }
        if($type==0)
        {
            
            ?>
            <br/>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Sample Dyeing Production</i></u></strong></div>
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                    <thead>
                        <th width="40">SL</th>
                        <th width="110">WO/Booking No</th>
                        <th width="90">Job No</th>
                        <th width="120">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                        <th width="140">Order No</th>
                        <th width="100">Order Qty.</th>
                        <th width="60">GSM</th>
                        <th width="100">Fabric Color</th>
                        <th width="100">Batch No/ Lot No.</th>
                        <th width="50">Ext. No</th>
                        <th width="100">MC No.</th>
                        <th width="90">MC Capacity</th>
                        <th width="70">Dyeing Date</th>
                        <th width="100">Dyeing Qty</th>
                        <th width="60">UL %</th>
                        <th width="60">Hour Req.</th>
                        <th width="80">Loading Time</th>
                        <th width="80">Unloading Time</th>
                        <th width="100">Liquor Ratio</th>
                        <th width="70">Hour Used</th>
                        <th width="60">Hour Devi.</th>                
                        <th width="90">Process/ Color Range</th>
                        <th width="110">Total Dye Cost</th>
                        <th width="90">Dye Cost/Kg</th>
                        <th width="110">Total Chem Cost</th>
                        <th width="90">Chem Cost/Kg</th>
                        <th width="130">Dyeing Company</th>
                        <th>Remarks</th>
                    </thead>

                <?
                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against n(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond  $batch_cond $book_cond 
                group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_without_order,b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against in(3,2) and b.is_sales!=1 $floor_cond $working_company_cond $lc_company_cond  $date_cond $batch_cond $book_cond 
                group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id,b.booking_without_order, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by  a.process_end_date";
                }
                //echo $sql_dtls;
                $sql_result=sql_select($sql_dtls);
                foreach ( $sql_result as $row )
                {
                    $sam_batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                    $samp_booking_no_arr=explode("-",$row[csf('booking_no')]);
                    $sm_booking_no=$samp_booking_no_arr[1];
                    if($sm_booking_no=='SM' || $sm_booking_no=='SMN') 
                    {
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['id']=$row[csf('id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['extention_no']=$row[csf('extention_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dyeing_company']=$row[csf('dyeing_company')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['service_company']=$row[csf('service_company')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['production_date']=$row[csf('production_date')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['process_end_date']=$row[csf('process_end_date')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['machine_id']=$row[csf('machine_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_hours']=$row[csf('end_hours')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['end_minutes']=$row[csf('end_minutes')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['remarks']=$row[csf('remarks')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_id']=$row[csf('color_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_without_order']=$row[csf('booking_without_order')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['color_range_id']=$row[csf('color_range_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_hr']=$row[csf('dur_req_hr')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['dur_req_min']=$row[csf('dur_req_min')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['prod_id']=$row[csf('prod_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['po_id']=$row[csf('po_id')];
                        $sample_batch_wise_arr[$row[csf('batch_id')]]['qnty']=$row[csf('qnty')];
                    }
                }
				$samp_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$samp_result_data_recipe=sql_select($samp_sql_recipe);
				foreach ($samp_result_data_recipe as $row)
				{
					$samp_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($samp_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$samp_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				
                if($type==3)
                {
                    $batchIds = "'" . implode ( "', '", $sam_batch_id_array ) . "'";
                    $baIds=chop($batchIds,','); $other_batch_cond_in="";
                    $ba_ids=count(array_unique(explode(",",$batchIds)));
                    if($db_type==2 && $ba_ids>1000)
                    {
                    $other_batch_cond_in=" and (";
                    $baIdsArr=array_chunk(explode(",",$baIds),999);
                    foreach($baIdsArr as $ids)
                    {
                    $ids=implode(",",$ids);
                    $other_batch_cond_in.=" b.batch_id in($ids) or"; 
                    }
                    $other_batch_cond_in=chop($other_batch_cond_in,'or ');
                    $other_batch_cond_in.=")";
                    }
                    else
                    {
                    $other_batch_cond_in=" and b.batch_id in($baIds)";
                    }

                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();

                    $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 $working_company_cond_issue $lc_company_cond_issue group by a.mst_id, b.batch_id,a.item_category ";

                    //echo $chem_dye_cost_sql;//die;
                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);
                    //var_dump($chem_dye_cost_result);die;
                    
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
                            
                        }
                        
                    }
                    
                    
                }
                
                $tot_rows=count($sql_result);
                $i=1;  $ul_percent=0; $tot_dye_qnty=0;$total_dye_chem_cost=$total_samp_dyeing_qnty=0;
                foreach ( $sample_batch_wise_arr as $batch_id=>$row )
                {
                    
					$samp_liquor_ratio_all='';

					$samp_process_id=rtrim($samp_batch_recipe_arr[$batch_id]['sub_process_id'],',');
					$samp_process_ids=array_unique(explode(",",$samp_process_id));
					
					foreach($samp_process_ids as $sid)
					{
						$liquor_ratio=$samp_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
						if($liquor_ratio>0)
						{
							$samp_liquor_ratio_all.=$liquor_ratio.',';
						}
						
					}
					$samp_liquor_ratio_all=rtrim($samp_liquor_ratio_all,',');
					$samp_liquor_ratio_allArr=array_unique(explode(",",$samp_liquor_ratio_all));
					$samp_liquor_ratio=$samp_liquor_ratio_allArr[0];
					//echo $samp_liquor_ratio.',';
					
					///////-------------
					$start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']);
					$end_time=strtotime($row[('process_end_date')]." ".$row[('end_hours')].':'.$row[('end_minutes')]);
					
					$timeDiffin=($end_time-$start_time)/60; 
					
					if($timeDiffin>0)
					{
						 $time_used=convertMinutes2Hours($timeDiffin); 
						 list($hour,$minute)=explode(':',$time_used);
					}				   
				   
				//echo $hour.',';
				 if($hour>8 || $samp_liquor_ratio>8){ 
					
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row['po_id'])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td><? echo $i; ?></td>
                        <td><p><? echo $row[('booking_no')]; ?></p></td>
                        <td align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$order_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
                        }
                        echo $job_all; 
						
						$samp_sid_ratio_all='';$samp_liquor_ratio_all='';

						$samp_process_id=rtrim($samp_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$samp_process_ids=array_unique(explode(",",$samp_process_id));
						
						foreach($samp_process_ids as $sid)
						{
							$liquor_ratio=$samp_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$samp_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$samp_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$samp_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
							
						}
						$samp_liquor_ratio_all=rtrim($samp_liquor_ratio_all,',');
						$samp_liquor_ratio_allArr=array_unique(explode(",",$samp_liquor_ratio_all));
						$samp_liquor_ratio=$samp_liquor_ratio_allArr[0];
						
                        ?></p></td>
                        <td><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td><p><? echo $style_all;  ?></p></td>
                        <td><p><? echo $po_all;?></p></td>
                        <td align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td><p><? echo $row[('batch_no')]; ?></p></td>
                        <td><p><? echo $row[('extention_no')]; ?></p></td>
                        <td align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_samp_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td align="center"><p><? $req_hour=""; $req_hour=$row[('dur_req_hr')].":".$row[('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                        <td align="center" title="<? echo $samp_sid_ratio_all;?>"><p><?  echo $samp_liquor_ratio; ?></p></td>
                        <td align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used; // 
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td align="center" bgcolor="#FF0000"><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td align="center" ><p><? echo $tot_deviation;// date('H:i', $diff);//floor($hours). ':' . ( ($hours-floor($hours)) * 60 ); ?></p></td>
                            <?
                        }
                        ?>
                        <td><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $samp_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6]; echo number_format($samp_dye_cost,4,'.','');?></p></td>
                        <td align="right" title="Tot Dye Cost/Dyeing Qty"><p><? $tot_samp_dye_cost_kg=$samp_dye_cost/$row[('qnty')];
                        echo number_format($tot_samp_dye_cost_kg,4,'.','');
                        $total_samp_dye_cost+=$samp_dye_cost;$total_samp_dye_cost_kg+=$tot_samp_dye_cost_kg;?> </p></td>

                        <td align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $samp_chem_cost =$chem_dye_cost_array[$row[('batch_id')]][5]; echo number_format($samp_chem_cost,4,'.','');?></p></td>
                        <td align="right" title="Tot Chemical Cost/Dyeing Qty"><p><? $tot_samp_chemi_cost_kg=$samp_chem_cost/$row[('qnty')];
                        echo number_format($tot_samp_chemi_cost_kg,4,'.','');
                        $total_samp_chem_cost+=$samp_chem_cost;$total_samp_chem_cost_kg+=$tot_samp_chemi_cost_kg;?> </p></td>

                        <td align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $i++;
				 }
                } 
                ?>
                    <tfoot>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th><strong>Total</strong></th>
                        <th id="value_dyeing_qnty" align="right"><? echo number_format($total_samp_dyeing_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th id="value_dye_cost"><? echo number_format($total_samp_dye_cost,4,'.',''); ?></th>
                        <th align="right"><? echo number_format(($total_samp_dye_cost/$total_samp_dyeing_qnty),4,'.',''); ?></th>
                        <th id="value_chem_cost"><? echo number_format($total_samp_chem_cost,4,'.',''); ?></th>
                        <th align="right"><? echo number_format(($total_samp_chem_cost/$total_samp_dyeing_qnty),4,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        if($type==0)
        {
            
            ?>
            <div align="left" style="background-color:#E1E1E1; color:#000; width:250px; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif;"><strong><u><i>Others Dyeing Production</i></u></strong></div>
                <table width="2670" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
                    <thead>
                        <th width="40">SL</th>
                        <th width="110">WO/Booking No</th>
                        <th width="90">Job No</th>
                        <th width="120">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                        <th width="140">Order No</th>
                        <th width="100">Order Qty.</th>
                        <th width="60">GSM</th>
                        <th width="100">Fabric Color</th>
                        <th width="100">Batch No/ Lot No.</th>
                        <th width="50">Ext. No</th>
                        <th width="100">MC No.</th>
                        <th width="90">MC Capacity</th>
                        <th width="70">Dyeing Date</th>
                        <th width="100">Dyeing Qty</th>
                        <th width="60">UL %</th>
                        <th width="60">Hour Req.</th>
                        <th width="80">Loading Time</th>
                        <th width="80">Unloading Time</th>
                        <th width="100">Liquor Ratio</th>
                        <th width="70">Hour Used</th>
                        <th width="60">Hour Devi.</th>                
                        <th width="90">Process/ Color Range</th>
                        <th width="110">Total Dye Cost</th>
                        <th width="90">Dye Cost/Kg</th>
                        <th width="110">Total Chem Cost</th>
                        <th width="90">Chem Cost/Kg</th>
                        <th width="130">Dyeing Company</th>
                        <th>Remarks</th>
                    </thead>
            
                <?  
               
                
                if($db_type==0)
                {
                    $sql_dtls="SELECT b.id,b.extention_no, a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, group_concat(c.po_id) as po_id, sum(c.batch_qnty) as qnty
                from pro_fab_subprocess a, pro_batch_create_mst b,  pro_batch_create_dtls c 
                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_against in(5,7) and b.is_sales!=1  $floor_cond $date_cond $working_company_cond $lc_company_cond    $batch_cond $book_cond 
                group by b.id,b.extention_no,a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                else if($db_type==2)
                {
                    $sql_dtls="SELECT b.id,b.extention_no,a.batch_id, a.company_id as dyeing_company,a.service_company , a.process_end_date as production_date, a.production_date as process_end_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min, min(c.prod_id) as prod_id, LISTAGG(cast(c.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.po_id) as po_id, sum(c.batch_qnty) as qnty
                from pro_fab_subprocess a, pro_batch_create_mst b, pro_batch_create_dtls c 
                where a.batch_id=b.id and a.entry_form=35 and a.load_unload_id=2 and b.id=c.mst_id  and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_against  in(5,7) and b.is_sales!=1  $floor_cond $working_company_cond $lc_company_cond $date_cond $batch_cond $book_cond 
                group by b.id,b.extention_no, a.batch_id, a.company_id,a.service_company , a.process_end_date, a.production_date, a.machine_id, a.end_hours, a.end_minutes, a.remarks, b.batch_no, b.color_id, b.booking_no, b.color_range_id, b.dur_req_hr, b.dur_req_min order by a.process_end_date";
                }
                //echo $sql_dtls; die;
                $sql_result=sql_select($sql_dtls);

                if(!empty($sql_result))
                {
                    // ================== getting batch id =========================
                    $batch_id_array = array();
                    foreach ( $sql_result as $row )
                    {
                        $batch_id_array[$row[csf('batch_id')]] = $row[csf('batch_id')];
                        $booking_no_arr=explode("-",$row[csf('booking_no')]);
                        $fb_booking_no=$booking_no_arr[1];
                        //if($fb_booking_no=='Fb') //echo $fb_booking_no.'A';else echo $fb_booking_no.'B';
                        //{
                        $other_batch_wise_array[$row[csf('batch_id')]]['id']= $row[csf('id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_id']= $row[csf('batch_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['extention_no']= $row[csf('extention_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dyeing_company']= $row[csf('dyeing_company')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['service_company']= $row[csf('service_company')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['production_date']= $row[csf('production_date')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['process_end_date']= $row[csf('process_end_date')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['machine_id']= $row[csf('machine_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['end_hours']= $row[csf('end_hours')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['end_minutes']= $row[csf('end_minutes')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['remarks']= $row[csf('remarks')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['batch_no']= $row[csf('batch_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['color_id']= $row[csf('color_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['booking_no']= $row[csf('booking_no')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['color_range_id']= $row[csf('color_range_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_hr']= $row[csf('dur_req_hr')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['dur_req_min']= $row[csf('dur_req_min')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['prod_id']= $row[csf('prod_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['po_id']= $row[csf('po_id')];
                        $other_batch_wise_array[$row[csf('batch_id')]]['qnty']= $row[csf('qnty')];
                        //}
                    }

                    $con = connect();
                    $r_id2=execute_query("delete from tmp_batch_or_iss where userid=$user_id and block=4");
                    if($r_id2)
                    {
                        oci_commit($con);
                    }

                    if(!empty($batch_id_array))
                    {
                        foreach ($batch_id_array as $batch_id_key => $batch_id_val) 
                        {
                            $r_id_1_4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")");
                            if($r_id_1_4) 
                            {
                                $r_id_1_4=1;
                            } 
                            else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_no, type, block) values ($user_id,'".$batch_id_key."',1,4".")";
                                oci_rollback($con);
                                die;
                            }
                        }

                        if($r_id_1_4)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }
                    }

                    //============================= getting issue amount ================================
                    $chem_dye_cost_array=array(); //and b.batch_id in($batchIds) 
                    $chem_dye_cost_kg_array=array();

                    $chem_dye_cost_sql="SELECT a.mst_id, b.batch_id,a.item_category, sum(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b,inv_issue_master c, tmp_batch_or_iss d where a.id=b.trans_id  and c.id=a.mst_id  and c.id=b.mst_id and c.entry_form in(5) AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 and b.batch_id=d.batch_issue_no and d.userid=$user_id and d.type=1 and d.block=4  $working_company_cond_issue $lc_company_cond_issue group by a.mst_id, b.batch_id,a.item_category ";
                    $chem_dye_cost_result=sql_select($chem_dye_cost_sql);

                    $all_issue_id =array();
                    foreach($chem_dye_cost_result as $row)
                    {
                        $all_issue_id[$row[csf('mst_id')]]=$row[csf('mst_id')];
                        $batch_id_arr=array_unique(explode(",",$row[csf('batch_id')]));
                        $total_batch_qnty=0;
                        foreach($batch_id_arr as $batch_ids)
                        {
                            $total_batch_qnty+=$batch_quantity_arr[$batch_ids];
                            //echo $total_batch_qnty;die;
                            //echo $row[csf('cons_amount')].'='.$total_batch_qnty.'='.$batch_quantity_arr[$batch_id];
                            
                        }
                        
                        foreach($batch_id_arr as $batch_id)
                        {
                            $chem_dye_cost_array[$batch_id][$row[csf('item_category')]]+=(($row[csf('cons_amount')]/$total_batch_qnty)*$batch_quantity_arr[$batch_id]);
                            $chem_dye_cost_kg_array[$batch_id][$row[csf('item_category')]]+=($row[csf('cons_amount')]/$total_batch_qnty);
                            
                        }
                    }

                    // ============================ getting issue return amount =============================
                    if($working_company_id==0) $working_company_cond_issue_ret="";else $working_company_cond_issue_ret="and p.knit_dye_company in($working_company_id)";

                    if(count($all_issue_id)>0)
                    {
                        foreach ($all_issue_id as $issue_id_key => $issue_id_val) 
                        {
                            $r_id4=execute_query("insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")");
                            if($r_id4) 
                            {
                                $r_id4=1;
                            } 
                            else 
                            {
                                echo "insert into tmp_batch_or_iss (userid, batch_issue_id, type, block) values ($user_id,'".$issue_id_key."',2,4".")";
                                oci_rollback($con);
                                die;
                            }
                        }

                        if($r_id4)
                        {
                            oci_commit($con);
                        }
                        else
                        {
                            oci_rollback($con);
                            disconnect($con);
                        }

                        $sql_return_cost="SELECT p.batch_no as batch_id,a.item_category, sum(a.cons_amount) as cons_amount  from inv_issue_master p, inv_transaction a, inv_receive_master b, tmp_batch_or_iss c where p.id=a.issue_id and b.id=a.mst_id and a.item_category in(5,6,7) $lc_company_cond $working_company_cond_issue_ret and b.is_deleted=0 and b.status_active=1 and a.issue_id=c.batch_issue_id and c.block=4 and c.type=2 and userid= $user_id  group by p.batch_no,a.item_category"; 

                        $cost_return_data_arr=array();
                        $sql_return_res=sql_select($sql_return_cost);
                        foreach ($sql_return_res as $row)
                        {
                            $cost_return_data_arr[$row[csf('batch_id')]][$row[csf('item_category')]]+=$row[csf('cons_amount')];
                        }
                    }
                }
                
                
                // print_r($cost_return_data_arr);
				$other_sql_recipe= "SELECT b.id as batch_id,b.batch_weight,b.batch_no,b.booking_no,b.color_id,b.color_range_id,c.id as recipe_id,d.sub_process_id,d.sub_seq,d.liquor_ratio from   pro_fab_subprocess a,pro_batch_create_mst b,pro_recipe_entry_mst c,pro_recipe_entry_dtls d where c.batch_id=b.id and c.id=d.mst_id and b.id=a.batch_id  and a.entry_form=35  and b.batch_against  in(5,7) and b.is_sales!=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0   $date_cond $floor_cond $working_company_cond $lc_company_cond $batch_cond $book_cond  order by d.sub_seq";
				//die;
				$other_result_data_recipe=sql_select($other_sql_recipe);
				foreach ($other_result_data_recipe as $row)
				{
					$other_batch_recipe_arr[$row[csf("batch_id")]]['sub_process_id'].=$row[csf("sub_process_id")].',';
					if($other_tmp_chk_subprocess[$row[csf("batch_id")]][$row[csf("sub_process_id")]]=='')
					{
					$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['liquor_ratio']=$row[csf("liquor_ratio")];
					}
					$other_batch_recipe_subpro_arr[$row[csf("batch_id")]][$row[csf("sub_process_id")]]['seq_no']=$row[csf("sub_seq")];
				}
				
                $tot_rows=count($sql_result);
                $m=1;  $ul_percent=0; $tot_dye_qnty=0;$total_other_dye_chem_cost=0;
                foreach ( $other_batch_wise_array as $batch_id=>$row )
                {
                    
					
						$other_liquor_ratio_all='';
						$sub_process_id=rtrim($other_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						
						foreach($sub_process_ids as $sid)
						{
							$liquor_ratio=$other_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
								$other_liquor_ratio_all.=$liquor_ratio.',';
							}
							
						}
						$other_liquor_ratio_all=rtrim($other_liquor_ratio_all,',');
						$other_liquor_ratio_allArr=array_unique(explode(",",$other_liquor_ratio_all));
						$other_liquor_ratio=$other_liquor_ratio_allArr[0];
					//echo $samp_liquor_ratio.',';
					
					///////-------------
					$start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']);
					$end_time=strtotime($row[('process_end_date')]." ".$row[('end_hours')].':'.$row[('end_minutes')]);
					
					$timeDiffin=($end_time-$start_time)/60; 
					
					if($timeDiffin>0)
					{
						 $time_used=convertMinutes2Hours($timeDiffin); 
						 list($hour,$minute)=explode(':',$time_used);
					}				   
				   
				//echo $hour.',';
				 if($hour>8 || $other_liquor_ratio>8){					
					
					
					if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $po_id_arr=array_unique(explode(",",$row[('po_id')])); 
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trother_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="trother_<? echo $m; ?>">
                        <td><? echo $m; ?></td>
                        <td><p><? echo $row[('booking_no')]; ?></p></td>
                        <td align="center"><p>
                        <?
                        $job_chech=array();$job_all=$buyer_all=$style_all=$po_all="";$po_qnty=0;
                        foreach($po_id_arr as $po_id)
                        {
                            if(!in_array($order_arr[$po_id]['job_no'],$job_chech))
                            {
                                $job_chech[]=$order_arr[$po_id]['job_no'];
                                if($job_all=="") $job_all=$order_arr[$po_id]['job_no']; else $job_all .=", ".$order_arr[$po_id]['job_no'];
                                if($buyer_all=="") $buyer_all=$buyer_library[$order_arr[$po_id]['buyer_name']]; else $buyer_all .=", ".$buyer_library[$order_arr[$po_id]['buyer_name']];
                                if($style_all=="") $style_all=$order_arr[$po_id]['style_ref_no']; else $style_all .=", ".$order_arr[$po_id]['style_ref_no'];
                            }
                            if($po_all=="") $po_all=$order_arr[$po_id]['po_number']; else $po_all .=", ".$order_arr[$po_id]['po_number'];
                            $po_qnty +=$order_arr[$po_id]['po_quantity'];
                        }
                        echo $job_all; 
						
						$other_sid_ratio_all='';$other_liquor_ratio_all='';

						$sub_process_id=rtrim($other_batch_recipe_arr[$batch_id]['sub_process_id'],',');
						$sub_process_ids=array_unique(explode(",",$sub_process_id));
						
						foreach($sub_process_ids as $sid)
						{
							$liquor_ratio=$other_batch_recipe_subpro_arr[$batch_id][$sid]['liquor_ratio'];
							if($liquor_ratio>0)
							{
							$seq_no=$other_batch_recipe_subpro_arr[$batch_id][$sid]['seq_no'];
							$other_sid_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.'&#013;';
							
							$other_liquor_ratio_all.=$dyeing_sub_process[$sid].'='.$seq_no.':'.$liquor_ratio.',';
							}
							
						}
						$other_liquor_ratio_all=rtrim($other_liquor_ratio_all,',');
						$other_liquor_ratio_allArr=array_unique(explode(",",$other_liquor_ratio_all));
						$other_liquor_ratio=$other_liquor_ratio_allArr[0];
						
                        ?></p></td>
                        <td><p><? echo $buyer_all; echo $buyer_library[$non_order_arr[$row[('booking_no')]]['buyer_name']]; ?></p></td>
                        <td><p><? echo $style_all;  ?></p></td>
                        <td><p><? echo $po_all;?></p></td>
                        <td align="right"><p><? echo number_format($po_qnty,0);  ?></p></td>
                        <td align="center"><p><? echo $gsm_library[$row[('prod_id')]]; ?></p></td>
                        <td><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
                        <td><p><? echo $row[('batch_no')]; ?></p></td>
                        <td><p><? echo $row[('extention_no')]; ?></p></td>
                        <td align="center"><p><? echo $machine_cap_array[$row[('machine_id')]]["machine_no"]; ?></p></td>
                        <td align="right"><p><? $machine_capacity=$machine_cap_array[$row[('machine_id')]]["prod_capacity"]; echo number_format($machine_capacity,2,'.',''); ?></p></td>
                        <td align="right"><p><? echo change_date_format($row[('production_date')]); ?></p></td>
                        <td align="right"><p><? echo number_format($row[('qnty')],2,'.',''); $total_other_dyeing_qnty+=$row[('qnty')]; ?></p></td>
                        <td align="right"><p><? $ul_percent=$row[('qnty')]/$machine_capacity*100; echo number_format($ul_percent,2,'.',''); ?></p></td>
                        <td align="center"><p><? $req_hour=""; $req_hour=$row[('dur_req_hr')].":".$row[('dur_req_min')]; echo $req_hour; ?></p></td>
                        <td align="center"><p><? $start_time=''; $start_time=$load_data[$row[('id')]]['end_hours'].':'.$load_data[$row[('id')]]['end_minutes']; echo  $start_time; ?></p></td>
                        <td align="center"><p><? $end_time=''; $end_time=$row[('end_hours')].':'.$row[('end_minutes')]; echo $end_time; ?></p></td>
                        <td title="<? echo $other_sid_ratio_all;?>" align="center"><p><? echo $other_liquor_ratio; ?></p></td>
                        <td align="right"><p><? $start_time=strtotime($load_data[$row[('id')]]['process_end_date']." ".$start_time); $end_time=strtotime($row[('process_end_date')]." ".$end_time);  $timeDiffin=($end_time-$start_time)/60;
                        
                        if($timeDiffin>0)
                        {
                             $time_used=convertMinutes2Hours($timeDiffin); 
                             echo $time_used; // 
                        }
                        else
                        {
                            echo "Invalid";
                        }
                         ?></p></td>
                        <?
                        
                        $req_time=strtotime($row[('dur_req_hr')].":".$row[('dur_req_min')].":" . 00);
                        $hour_dev_cal=0;$tot_deviation="";
                        if($req_time!="")
                        {
                            $used_time=strtotime($time_used.':'. 00);
                            $hour_dev_cal=($used_time-$req_time)/60;
                            $tot_deviation=convertMinutes2Hours($hour_dev_cal); 
                        }
                        if($hour_dev_cal>0)
                        {
                            ?>
                            <td align="center" bgcolor="#FF0000"><p><? echo $tot_deviation; ?></p></td>
                            <?
                        }
                        else
                        {
                            ?>
                            <td align="center" ><p><? echo $tot_deviation; ?></p></td>
                            <?
                        }
                        ?>
                        <td><p><? echo $color_range[$row[('color_range_id')]]; ?></p></td>

                        <td align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $other_dye_cost =$chem_dye_cost_array[$row[('batch_id')]][6] - $cost_return_data_arr[$row[('batch_id')]][6]; echo number_format($other_dye_cost,4,'.','');?></p></td>
                        <td align="right" title="Tot Dye Cost/Dyeing Qty"><p><? 
                        $tot_dye_cost_kg=$other_dye_cost/$row[('qnty')]; echo number_format($tot_dye_cost_kg,4,'.','');
                      
                         $total_other_dye_cost+=$other_dye_cost;$total_other_dye_cost_kg+=$tot_dye_cost_kg;?> </p></td>

                        <td align="right" title="Batch ID=<? echo $row[('batch_id')];?>"><p><? $other_chem_cost =$chem_dye_cost_array[$row[('batch_id')]][5] - $cost_return_data_arr[$row[('batch_id')]][5]; echo number_format($other_chem_cost,4,'.','');?></p></td>
                        <td align="right" title="Tot Chemical Cost/Dyeing Qty"><p><? 
                        $tot_chemi_cost_kg=$other_chem_cost/$row[('qnty')]; echo number_format($tot_chemi_cost_kg,4,'.','');
                        
                         $total_other_chem_cost+=$other_chem_cost;$total_other_chem_cost_kg+=$tot_chemi_cost_kg;?> </p></td>

                        <td align="center"><p><? echo $company_library[$row[('service_company')]]; ?></p></td>
                        <td><p><? echo $row[('remarks')]; ?></p></td>
                    </tr>
                    <?
                    $m++;
				 }
                } 
                ?>
                    <tfoot>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th><strong>Total</strong></th>
                        <th id="value_total_dyeing_qnty" align="right"><? echo number_format($total_other_dyeing_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th id="value_total_dye_cost"><? echo number_format($total_other_dye_cost,4,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_other_dye_cost/$total_other_dyeing_qnty,4,'.',''); ?></th>
                        <th id="value_total_chem_cost"><? echo number_format($total_other_chem_cost,4,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_other_chem_cost/$total_other_dyeing_qnty,4,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div> 
            <?
        }
        ?>    
    </div>
  
    <?
	
	
	$message=ob_get_contents();
	ob_clean();

	$to='';
	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=57 and b.mail_user_setup_id=c.id and a.company_id =".$cbo_company_id."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 //echo $sql;die;
	
	
	$mail_sql=sql_select($sql);
	$receverMailArr=array();
	foreach($mail_sql as $row)
	{
		$receverMailArr[$row[csf('email_address')]]=$row[csf('email_address')];
	}

	$to=implode(',',$receverMailArr);
	
	
	$subject="Daily Dyeing Prod Analysis";
	$header=mailHeader();
	if($_REQUEST['isview']==1){
		echo $to.$message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );}
	}
	
	
 }
    exit();

?>
