<? 
//Note: path =>/production/reports/requires/factory_monthly_production_report_v4_controller.php;
extract($_REQUEST);
 
require_once('../../includes/common.php');
require('../setting/mail_setting.php');
require('../../ext_resource/mpdf60/mpdf.php');
 
 
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
$company_group_library=return_library_array( "select id,group_id from lib_company", "id", "group_id");
$group_short_library=return_library_array( "select id,group_name from lib_group", "id", "group_name");
 
$time_stamp=time();
if($view_date){$time_stamp = strtotime($view_date);}

$previous_date = change_date_format(date('d-M-Y', strtotime('-1 day', $time_stamp)),'','',1);

 

$action='report_generate';
$cbo_location_id='';
$cbo_floor_id='';
$cbo_group_name='0';
$txt_job_no='';
$hidden_job_id='';
$txt_int_ref='';
$cbo_buyer_name='0';
$txt_date_from=$previous_date;
$txt_date_to=$previous_date;
$type=1;
$report_title='Factory Monthly Production Report V4';
$cbo_company_id =3;
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_name 	= str_replace("'","",$cbo_company_id);
	$cbo_floor 		= str_replace("'","",$cbo_floor_id);
	$cbo_location 	= str_replace("'","",$cbo_location_id);
	$cbo_group_name = str_replace("'","",$cbo_group_name);
	$buyer_name 	= str_replace("'","",$cbo_buyer_name);
	$int_ref 		= str_replace("'","",$txt_int_ref);
	$job_no 		= str_replace("'","",$txt_job_no);
	$style_ref 		= str_replace("'","",$txt_style_ref);
	$date_from 		=  str_replace("'","",$txt_date_from);
	$date_to 		=  str_replace("'","",$txt_date_to);
	$search_prod_date=change_date_format(str_replace("'","",$txt_date_from));
	$current_eff_min=($ex_time[0]*60)+$ex_time[1];
	$variable_time= explode(":",$variable_start_time_arr);
	$vari_min=($variable_time[0]*60)+$variable_time[1];
	$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
	$dif_time=number_format($datediff/60,2);
	$dif_hour_min=date("H", strtotime($dif_time));	
	

	foreach($company_library as $company_name=>$company){
        ob_start();	
        
        if($type==1)
        {

            if(str_replace("'","",$cbo_location)==""){$location_con="";}else{$location_con=" and a.location=$cbo_location";}
            if(str_replace("'","",$cbo_floor)==""){$floor_con="";}else{$floor_con=" and a.floor_id=$cbo_floor";}
            if(str_replace("'","",$buyer_name)==0){$buyer_con="";}else{$buyer_con=" and c.buyer_name=$buyer_name";}
            
            if($company_name==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id in($company_name)";
            if($company_name==0) $cbo_company_cond_ex=""; else $cbo_company_cond_ex=" and d.company_id in($company_name)";		

            //  ======================= geting shift name ============================
            $sql = "SELECT shift_name,start_time,end_time from shift_duration_entry where status_active=1 and is_deleted=0 and production_type=3 order by shift_name asc";
            $res = sql_select($sql);
            $shift_arr = array();
            foreach ($res as $val) 
            {
                $shift_arr[$val['SHIFT_NAME']]['start_time'] = $val['START_TIME'];
                $shift_arr[$val['SHIFT_NAME']]['end_time'] = $val['END_TIME'];
            }
            unset($res);

            $start_time_data_arr=sql_select("select company_name, shift_id, TO_CHAR(prod_start_time,'HH24:MI') as prod_start_time,TO_CHAR(lunch_start_time,'HH24:MI') as lunch_start_time from variable_settings_production where  company_name in($company_name) and  shift_id=1 and variable_list=26 and status_active=1 and is_deleted=0");
         
            
            $group_prod_start_time=sql_select("select min(TO_CHAR(prod_start_time,'HH24:MI')) as prod_start_time  from variable_settings_production where company_name in($company_name) and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
            
            
            foreach($start_time_data_arr as $row)
            {
                $start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['pst']=$row[csf('prod_start_time')];
                $start_time_arr[$row[csf('company_name')]][$row[csf('shift_id')]]['lst']=$row[csf('lunch_start_time')];
            }
            unset($start_time_data_arr);

            $prod_start_hour=$group_prod_start_time[0][csf('prod_start_time')];
            if($prod_start_hour=="") $prod_start_hour="08:00";
            $start_time=explode(":",$prod_start_hour);
            $hour=$start_time[0]*1; 
            $minutes=$start_time[1]; 
            $last_hour=23;
            $lineWiseProd_arr=array(); $prod_arr=array(); $start_hour_arr=array();
            $start_hour=$prod_start_hour;
            $start_hour_arr[$hour]=$start_hour;
            for($j=$hour;$j<$last_hour;$j++)
            {
                $start_hour=add_time($start_hour,60);
                $start_hour_arr[$j+1]=substr($start_hour,0,5);
                // echo $j."<br>";
            }
            //echo $pc_date_time;die;
            $start_hour_arr[$j+1]='23:59';


            $min_shif_start=return_field_value("min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time","prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d ","a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($company_name) and shift_id=1 and pr_date='$date_from' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0","line_start_time");
     
            $first_hour_time=explode(":",$min_shif_start);
            $hour_line=substr($first_hour_time[0],1,1); $minutes_one=$start_time[1];
            $line_start_hour_arr[$hour_line]=$min_shif_start;
          
 

            for($l=($hour_line*1);$l<$last_hour;$l++)
            {  
                $min_shif_start=add_time($min_shif_start,60);
                $line_start_hour_arr[$l+1]=substr($min_shif_start,0,5);
            }
            
            $line_start_hour_arr[$j+1]='23:59';

 
            
            $dtls_sql="SELECT a.production_date,a.po_break_down_id as po_id,c.buyer_name,a.company_id,a.item_number_id,a.sewing_line,b.unit_price,c.total_set_qnty,c.job_no,c.id as job_id,
                        sum(CASE WHEN a.production_type =1 THEN e.production_qnty END) AS cutting_qnty,
                        sum(CASE WHEN a.production_type =1 and a.production_source=1 THEN e.production_qnty END) AS cutting_qnty_inhouse,
                        sum(CASE WHEN a.production_type =1 and a.production_source=3 THEN e.production_qnty END) AS cutting_qnty_outbound, 	
                        sum(CASE WHEN a.production_type =5 THEN e.production_qnty END) AS sewing_qnty,
                        sum(CASE WHEN a.production_type =5 and a.production_source=1 THEN e.production_qnty END) AS sewingout_qnty_inhouse,
                        sum(CASE WHEN a.production_type =5 and a.production_source=3 THEN e.production_qnty END) AS sewingout_qnty_outbound, 
                        sum(CASE WHEN a.production_type =4 THEN e.production_qnty END) AS sewing_input_qnty,
                        sum(CASE WHEN a.production_type =4 and a.production_source=1 THEN e.production_qnty END) AS sewing_input_qnty_inhouse,
                        sum(CASE WHEN a.production_type =4 and a.production_source=3 THEN e.production_qnty END) AS sewing_input_qnty_outbound, 
                        sum(CASE WHEN a.production_type =8 THEN e.production_qnty END) AS finish_qnty,
                        sum(CASE WHEN a.production_type =8 and a.production_source=1 THEN e.production_qnty END) AS finish_qnty_inhouse, 
                        sum(CASE WHEN a.production_type =8 and a.production_source=3 THEN e.production_qnty END) AS finish_qnty_outbound,
                        sum(CASE WHEN a.production_type =8  THEN a.carton_qty END) AS carton_qty					
                        
                        from pro_garments_production_mst a, wo_po_break_down b,wo_po_details_master c , WO_PO_COLOR_SIZE_BREAKDOWN d, pro_garments_production_dtls e
                        where a.po_break_down_id=b.id and c.id=b.job_id and d.id=e.COLOR_SIZE_BREAK_DOWN_ID and a.id=e.mst_id and b.id=d.po_break_down_id   $location_con $floor_con $cbo_company_cond $buyer_con and a.production_date between '$date_from' and '$date_to'  and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.production_date,a.po_break_down_id,c.buyer_name,a.company_id,a.item_number_id,a.sewing_line,b.unit_price,c.total_set_qnty,c.job_no,c.id order by a.sewing_line asc"; //and b.id=11106 
                   // echo $dtls_sql;die; 
                   
                    $dtls_sql_result=sql_select($dtls_sql);
                    $prod_date_buyer_wise_summary=array();
                    $sewing_total_buyer_wise_array=array();
                    $sewing_line_buyer_wise_array=array();
                    $production_data_arr=array();
                    $po_id_array=array();
                    $job_id_array=array();
                    $check_array=array();
                    foreach($dtls_sql_result as $row)
                    {
                        $job_id_array[$row['JOB_ID']] = $row['JOB_ID'];
                    }
                    $job_id_cond = where_con_using_array($job_id_array,0,"job_id");
                    $costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per"); 
                    // echo "select job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond";die;
                    $tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost"); 
                    foreach($dtls_sql_result as $row)
                    {
                        if($check_array[$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]]=="")
                        {
                            if($production_data_arr[$row[csf('sewing_line')]][$row[csf('production_date')]]['item_number_id']!="")
                            {
                                $production_data_arr[$row[csf('sewing_line')]][$row[csf('production_date')]]['item_number_id'].="****".$row[csf('po_id')]."**".$row[csf('item_number_id')]."**".$row[csf('job_no')]."**".$row[csf('buyer_name')]."**".$row[csf('sewing_line')]; 
                            }
                            else
                            {
                                $production_data_arr[$row[csf('sewing_line')]][$row[csf('production_date')]]['item_number_id']=$row[csf('po_id')]."**".$row[csf('item_number_id')]."**".$row[csf('job_no')]."**".$row[csf('buyer_name')]."**".$row[csf('sewing_line')]; 
                            }
                            $check_array[$row[csf('sewing_line')]][$row[csf('production_date')]][$row[csf('po_id')]][$row[csf('item_number_id')]] = "aa";
                        }
                        //array for summary part
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cutting_qnty']+=$row[csf("cutting_qnty")];
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewing_input_qnty']+=$row[csf("sewing_input_qnty")];
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewing_qnty']+=$row[csf("sewing_qnty")]; //sew output
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")]; 
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['finish_qnty']+=$row[csf("finish_qnty")];

                        $sewing_total_buyer_wise_array[$row[csf('sewing_line')]][$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("item_number_id")]]+=$row[csf("sewing_qnty")];
                        // echo $row[csf('sewing_line')]."==".$row[csf("buyer_name")]."=".$row[csf("po_id")]."=".$row[csf("item_number_id")]."=".$row[csf("sewing_qnty")]."<br>";

                        $po_id_array[$row[csf("po_id")]]=$row[csf("po_id")];

                        $sewing_line_buyer_wise_array[$row[csf("buyer_name")]][$row[csf("po_id")]][$row[csf("item_number_id")]][$row[csf("sewing_line")]][$row[csf("production_date")]]+=$row[csf("sewing_qnty")];
                        
                        
                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*($row[csf("unit_price")]/$row[csf("total_set_qnty")]);

                        $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*($row[csf("unit_price")]/$row[csf("total_set_qnty")]);
                
                            $cm_value=0; $cm_value_in=0; $cm_value_out=0; $sewing_qty_in=0; $sewing_qty_out=0;
                                //$sewing_qnty=$row[csf("sewing_qnty")];
                            $sewing_qty_in=$row[csf("sewingout_qnty_inhouse")];
                            $sewing_qty_out=$row[csf("sewingout_qnty_outbound")];
                            
                            $job_no=$row[csf("job_no")];
                            $total_set_qnty=$row[csf("total_set_qnty")];
                            $costing_per=$costing_per_arr[$job_no];
                            
                            if($costing_per==1) $dzn_qnty=12;
                            else if($costing_per==3) $dzn_qnty=12*2;
                            else if($costing_per==4) $dzn_qnty=12*3;
                            else if($costing_per==5) $dzn_qnty=12*4;
                            else $dzn_qnty=1;
                                    
                            $dzn_qnty=$dzn_qnty*$total_set_qnty;
                            
                            $cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_in;
                            // echo "(".$tot_cost_arr[$job_no]."/".$dzn_qnty.")*".$sewing_qty_in."<br>";
                            $cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_out;
                            $prod_date_buyer_wise_summary[$row[csf("buyer_name")]]['cm_value_in']+=$cm_value_in;   
                }
                //    echo"<pre>";  print_r($sewing_total_buyer_wise_array);die;

                    if($cbo_company==0) $cbo_delivery_com_cond=""; else $cbo_delivery_com_cond=" and d.company_id in($cbo_company)";

                    $exfactory_res =("SELECT a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,d.company_id as company,b.unit_price,c.total_set_qnty,c.job_no,c.id as job_id,  
                    
                    sum(case when a.entry_form!=85 then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty, 
                    sum(case when a.entry_form!=85 and (d.company_id=0 OR d.company_id=c.company_name)  then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and (d.company_id=0 OR d.company_id=c.company_name) then a.ex_factory_qnty else 0 end) as ex_factory_qnty_inhouse,
                    
                    
                    sum(case when a.entry_form!=85 and d.company_id!=0 and d.company_id!=c.company_name then a.ex_factory_qnty else 0 end)-sum(case when a.entry_form=85 and d.company_id!=0 and d.company_id!=c.company_name then a.ex_factory_qnty else 0 end) as ex_factory_qnty_outbound
                    
                    from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d
                    
                    where a.delivery_mst_id=d.id and  a.po_break_down_id=b.id and b.job_id=c.id $cbo_company_cond_ex $cbo_delivery_com_cond  and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id,c.company_name,c.buyer_name,d.company_id,b.unit_price,c.total_set_qnty,c.job_no,c.id");
                    
                    //echo $exfactory_res; die;
                            
                    $exfactory_res_val=sql_select($exfactory_res);
                    
                    $job_id_array=array();
                    foreach($exfactory_res_val as $row)
                    {
                        $job_id_array[$row['JOB_ID']] = $row['JOB_ID'];
                    }
                    $job_id_cond = where_con_using_array($job_id_array,0,"job_id");
                    $costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where status_active=1 $job_id_cond","job_no","costing_per"); 
                    $tot_cost_arr = return_library_array("select job_no, cm_cost from wo_pre_cost_dtls where status_active=1 $job_id_cond","job_no","cm_cost"); 

                    foreach($exfactory_res_val as $ex_row)
                    {
                        //for summery part
                        $ex_cm_value_in=0; $ex_cm_value_inhouse=0; $ex_cm_value_outbound=0; $ex_sewing_qty_in=0; $ex_sewing_qty_inhouse=0; $ex_sewing_qty_outbound=0;
                        
                        $ex_sewing_qty_in=$ex_row[csf("ex_factory_qnty")];
                        $ex_sewing_qty_inhouse=$ex_row[csf("ex_factory_qnty_inhouse")];
                        $ex_sewing_qty_outbound=$ex_row[csf("ex_factory_qnty_outbound")];
                        
                        $job_no_ex=$ex_row[csf("job_no")];
                        $total_ex_set_qnty=$ex_row[csf("total_set_qnty")];
                        $costing_per_ex=$costing_per_arr[$job_no_ex];
                        
                        if($costing_per_ex==1) $dzn_qnty_ex=12;
                        else if($costing_per_ex==3) $dzn_qnty_ex=12*2;
                        else if($costing_per_ex==4) $dzn_qnty_ex=12*3;
                        else if($costing_per_ex==5) $dzn_qdzn_qnty_exnty=12*4;
                        else $dzn_qnty_ex=1;
                                    
                        $dzn_qnty_ex=$dzn_qnty_ex*$total_ex_set_qnty;
                        $ex_cm_value_in=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_in;
                        // echo "(".$tot_cost_arr[$job_no_ex]."/".$dzn_qnty_ex.")*".$ex_sewing_qty_in."<br>";
                        $ex_cm_value_inhouse=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_inhouse;
                        $ex_cm_value_outbound=($tot_cost_arr[$job_no_ex]/$dzn_qnty_ex)*$ex_sewing_qty_outbound;
                        
                        
                        //for summary part
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_cm_value_in']+=$ex_cm_value_in;
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_cm_value_inhouse']+=$ex_cm_value_inhouse;
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_cm_value_outbound']+=$ex_cm_value_outbound;
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry']+=$ex_row[csf("ex_factory_qnty")];
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_qnty_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")];
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_qnty_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")];	
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_unitPrice']=$ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")];
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_inhouse']+=$ex_row[csf("ex_factory_qnty_inhouse")]*($ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")]);
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal_outbound']+=$ex_row[csf("ex_factory_qnty_outbound")]*($ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")]);
                        $prod_date_buyer_wise_summary[$ex_row[csf("buyer_name")]]['ex_factory_smry_fobVal']+=$ex_row[csf("ex_factory_qnty")]*($ex_row[csf("unit_price")]/$ex_row[csf("total_set_qnty")]);
                        //end
                            
                        
                        // end for summary part
                    }

                    /* $knited_query="SELECT a.buyer_id,a.knitting_source,c.quantity
                    from inv_receive_master a, pro_grey_prod_entry_dtls b , order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $cbo_company_cond   and a.receive_date between '$date_from' and '$date_to'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.entry_form =2  and c.entry_form =2 "; */

                    $knited_query="SELECT a.buyer_id,a.knitting_source,b.grey_receive_qnty as quantity
                    from inv_receive_master a, pro_grey_prod_entry_dtls b  where a.id=b.mst_id $cbo_company_cond   and a.receive_date between '$date_from' and '$date_to'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.entry_form =2";
              // echo $knited_query; die;
                
                    $knited_query_result=sql_select($knited_query);
                    foreach( $knited_query_result as $knit_row)
                    {
                        $buyer_wise_kint_summary[$knit_row[csf("buyer_id")]][$knit_row[csf("knitting_source")]]+=$knit_row[csf("quantity")];			

                    }
                    // echo"<pre>"; print_r($buyer_wise_kint_summary);die;
                    $finish_query="SELECT b.buyer_id,a.knitting_source,c.quantity from  inv_receive_master a, pro_finish_fabric_rcv_dtls b,order_wise_pro_details c  where a.id=b.mst_id and b.id = c.dtls_id $cbo_company_cond  and a.receive_date between '$date_from' and '$date_to' and a.entry_form=37  and c.entry_form =37 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
                //echo $finish_query; die;

                $finish_query_result=sql_select($finish_query);
                $count_finish=count($finish_query_result);

                foreach( $finish_query_result as $finish_row)
                {
                    $buyer_wise_fin_summary[$finish_row[csf("buyer_id")]][$finish_row[csf("knitting_source")]]+=$finish_row[csf("quantity")];
                }
            
                if($db_type==0)
                {
                    $manufacturing_company=return_field_value("group_concat(comp.id) as company_id","lib_company as comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
                }
                else
                {
                    $manufacturing_company=return_field_value("listagg(comp.id,',') within group (order by comp.id) as company_id","lib_company comp", "comp.core_business=1 and comp.status_active=1 and comp.is_deleted=0 $company_cond","company_id");
                }
                //echo $manufacturing_company;
                $smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($manufacturing_company) and variable_list=25 and   status_active=1 and is_deleted=0");
                // echo $smv_source;die;
                $po_id_con=where_con_using_array($po_id_array,0,"b.id");

                if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
                
                if($smv_source==3)
                {
                    $sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 
                    and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_id_con";
                    $resultItem=sql_select($sql_item);
                    foreach($resultItem as $itemData)
                    {
                        $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
                    }
                }
                else
                {
                    $sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where b.job_id=a.id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_id_con";
                    //echo $sql_item;
                    $resultItem=sql_select($sql_item);
                    foreach($resultItem as $itemData)
                    {
                        if($smv_source==1)
                        {
                        $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
                        }
                        if($smv_source==2)
                        {
                        $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
                        }
                    }
                }
                //print_r($item_smv_array);

                $location_con = str_replace("a.location","a.location_id",$location_con);
                $smv_adjustment_sql="SELECT a.id,a.company_id, b.pr_date,b.ADJUSTMENT_SOURCE, sum(b.TOTAL_SMV) as TOTAL_SMV
                from prod_resource_mst a, prod_resource_smv_adj b
                where a.id=b.mst_id and a.is_deleted=0 and b.status_active=1 and b.adjustment_source in (9,10) $cbo_company_cond $location_con $floor_con and b.pr_date between '$date_from' and '$date_to' group by a.id,a.company_id, b.pr_date,b.adjustment_source ";
                // echo $smv_adjustment_sql;die;
                $smv_adjustment_data=sql_select($smv_adjustment_sql);
                // print_r($smv_adjustment_data);die;
                $smv_adjust_array = array();
                foreach($smv_adjustment_data as $row)
                {
                    if($row["ADJUSTMENT_SOURCE"]==9)
                    {
                        $smv_adjust_array[$row[csf('id')]][$row[csf('pr_date')]]['smv_adjustment_plus']+=$row["TOTAL_SMV"];
                    }
                    if($row["ADJUSTMENT_SOURCE"]==10)
                    {
                        $smv_adjust_array[$row[csf('id')]][$row[csf('pr_date')]]['smv_adjustment_minus']+=$row["TOTAL_SMV"];
                    }		
                    // echo $row["TOTAL_SMV"]."sdfdsfds<br>";	
                }
                // echo"<pre>"; print_r($smv_adjust_array);die;
                $buyer_wise_min_array=array();
                $buyer_wise_prod_min_array=array();
                ksort($sewing_total_buyer_wise_array);
                foreach ($sewing_total_buyer_wise_array as $li_key => $l_data) 
                {
                    foreach($l_data as $buyer_key=> $buyer_val)
                    {
                        foreach($buyer_val as $po_key=> $po_val)
                        {
                            foreach($po_val as $item_key=> $val)
                            {
                                $buyer_wise_min_array[$buyer_key]['earn_min']+=$val*$item_smv_array[$po_key][$item_key];
                                // echo $li_key."=".$po_key."=".$item_key."==".$val."*".$item_smv_array[$po_key][$item_key]."<br>";
                                $buyer_wise_prod_min_array[$buyer_key][$po_key][$item_key][$li_key]+=$val*$item_smv_array[$po_key][$item_key];
                            }
                        
                        }

                    }
                }
            // echo"<pre>"; print_r($buyer_wise_prod_min_array);die;
            // =========================== acctual resource data ========================
            $prod_resource_array=array();
            
            
            $dataArray_sql=("SELECT a.id, a.line_number,b.pr_date, b.man_power,b.smv_adjust,b.smv_adjust_type,b.working_hour from prod_resource_mst a, prod_resource_dtls b,prod_resource_dtls_mast c where a.id=c.mst_id and c.id=b.mast_dtl_id and a.company_id=$company_name $location_con $floor_con and b.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0");
            // echo $dataArray_sql;die;
            
            $data_arry=sql_select($dataArray_sql);
            
            foreach($data_arry as $val)
            {
                $prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['man_power']=$val[csf('man_power')];
                if($val[csf('smv_adjust_type')]==1)
                {							
                    $prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')];
                }
                if($val[csf('smv_adjust_type')]==2)
                {							
                    $prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust']=$val[csf('smv_adjust')]*-1;
                }
                $prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['working_hour']=$val[csf('working_hour')];
                $prod_resource_array[$val[csf('id')]][$val[csf('pr_date')]]['smv_adjust_type']=$val[csf('smv_adjust_type')];
            }
            // echo"<pre>";print_r($prod_resource_array);die;		

            // ======================== shift wise line =========================
            
            $sql = "SELECT a.id,min(TO_CHAR(d.prod_start_time,'HH24:MI')) as line_start_time,min(TO_CHAR(d.lunch_start_time,'HH24:MI')) as LUNCH_START_TIME,b.pr_date from prod_resource_mst a,prod_resource_dtls b,prod_resource_dtls_time d where a.id=d.mst_id and a.id=b.mst_id and b.mast_dtl_id=d.mast_dtl_id and   a.company_id in($company_name) $location_con $floor_con and shift_id=1 and b.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 and d.is_deleted=0 group by a.id,b.pr_date";
            // echo $sql;
            $res = sql_select($sql);
            $line_wise_shift_arr = array();
            $line_wise_shift_lunch_arr = array();

            foreach ($res as $val) 
            {
                $line_wise_shift_arr[$val['ID']][$val[csf('pr_date')]] = $val['LINE_START_TIME'];
                $line_wise_shift_lunch_arr[$val['ID']][$val[csf('pr_date')]] = $val['LUNCH_START_TIME'];
            }
            unset($res);
            // echo"<pre>";print_r($line_wise_shift_lunch_arr);die;		
           
            //echo"<pre>";print_r($production_data_arr);
            $buyer_wise_avai_min_array=array();
            $l_chk_array=array();
            $search_prod_date=change_date_format(str_replace("'","",$date_from));
            $current_date_time=date('d-m-Y H:i');
            $ex_date_time=explode(" ",$current_date_time);
            $current_date=$ex_date_time[0];
            $current_time=$ex_date_time[1];

            
            $actual_date=date("Y-m-d");
            
            $actual_time=substr(date("Y-m-d H:i:s",strtotime($pc_date_time)),11,2);	
            $line_start_hour_arr[$hour_line]=$min_shif_start;
            // $actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$txt_date)));

            foreach ($prod_resource_array as $l_key => $l_value) 
            {
                foreach ($l_value as $dt_key => $r) 
                {
                    // echo $dt_key;die;
                    $lunch_start="";
                    $lunch_start=$line_number_arr[$l_key][$pr_date]['lunch_start_time']; 
                    $lunch_hour=$start_time_arr[$company_id][1]['lst']; 
                    if($lunch_start!="") 
                    { 
                        $lunch_start_hour=$lunch_start; 
                    }
                    else
                    {
                        $lunch_start_hour=$lunch_hour; 
                    }

                    $actual_production_date=date("Y-m-d",strtotime(str_replace("'","",$dt_key)));				
                    // echo $production_data_arr[$l_key][$dt_key]['item_number_id']."<br>";
                    $production_data = explode("****",$production_data_arr[$l_key][$dt_key]['item_number_id']);
                    foreach ($production_data as $key => $val) 
                    {
                        if($l_chk_array[$l_key][$dt_key]=="")
                        {
                            // ============================================
                            if(str_replace("'","",$actual_production_date)>str_replace("'","",$actual_date)) 
                            {
                                
                                $line_start=$line_number_arr[$resource_id][$pr_date]['prod_start_time'];
                                
                                if($line_start!="") 
                                { 
                                    $line_start_hour=substr($line_start,0,2); 
                                    if(substr($line_start_hour,0,1)==0)  $line_start_hour=substr($line_start_hour,1,1);	
                                }
                                else
                                {
                                    $line_start_hour=$hour; 
                                }
                                $actual_time_hour=0;
                                $total_eff_hour=0;
                                for($lh=$line_start_hour;$lh<=$last_hour;$lh++)
                                {
                                    $bg=$start_hour_arr[$lh];
                                    if($lh<$actual_time)
                                    {
                                        $total_eff_hour=$total_eff_hour+1;
                                    }
                                }
                                //echo $total_eff_hour.'aaaa';
                                if($start_hour_arr[$actual_time]>$lunch_start_hour) $total_eff_hour=$total_eff_hour-1;
                                
                                if($total_eff_hour>$production_data_arr[$f_id][$resource_id]['working_hour'])
                                {
                                    $total_eff_hour=$production_data_arr[$f_id][$resource_id]['working_hour'];
                                }
                            }
                            
                            if(str_replace("'","",$actual_production_date)<=str_replace("'","",$actual_date)) 
                            {
                                for($ah=$hour;$ah<=$last_hour;$ah++)
                                {
                                    $prod_hour="prod_hour".substr($start_hour_arr[$ah],0,2).""; 
                                    $line_production_hour+=$production_data_arr[$f_id][$resource_id][$prod_hour];
                                    //echo $production_data_arr[$f_id][$ldata][$prod_hour];
                                    $line_floor_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
                                    $line_total_production+=$production_data_arr[$f_id][$resource_id][$prod_hour];
                                    $actual_time_hour=$start_hour_arr[$ah+1];
                                }
                                
                                $total_eff_hour=$resource_data['working_hour'];	
                            }
                            // =============================================
                            if($current_date==$search_prod_date)
                            {

                                $current_hour_min=date('H:i');
                                $line_shift_hour_min=$line_wise_shift_arr[$l_key][$dt_key];
                                $timeDiff=datediff("n",$line_shift_hour_min,$current_hour_min);
                                $time_dif=number_format($timeDiff/60,2);
                                if(strtotime(date('H:i'))>strtotime($line_wise_shift_lunch_arr[$l_key][$dt_key]) && $line_wise_shift_lunch_arr[$l_key][$dt_key]!="")
                                {
                                    $line_wise_shift_lunch_h_m = $line_wise_shift_lunch_arr[$l_key][$dt_key]; 
                                    $lunchTimeDiff=datediff("n",$line_wise_shift_lunch_h_m,$current_hour_min);
                                    if($lunchTimeDiff>60)
                                    {
                                        $cla_cur_time=$time_dif-1;
                                    }
                                    else
                                    {
                                        $lunchMin=number_format($lunchTimeDiff/60,2);
                                        $cla_cur_time=$time_dif-$lunchMin;
                                    }
                                }
                                else
                                {
                                    $cla_cur_time=$time_dif;
                                }
                            }
                            else
                            {
                                $cla_cur_time=$r['working_hour'];
                            }


                            $ex_data = explode("**",$val);
                            $efficiency_min=$r['smv_adjust']+($r['man_power']*$cla_cur_time*60);
                            // echo $l_key."==".$efficiency_min."<br>";
                            // echo $efficiency_min."==".$l_key."==".$r['smv_adjust']."+(".$r['man_power']."*".$cla_cur_time."*60)<br>";
                            $produce_minit = $buyer_wise_prod_min_array[$ex_data[3]][$ex_data[0]][$ex_data[1]][$l_key];
                            $line_efficiency=(($produce_minit)*100)/$efficiency_min;
                            // echo $l_key."((".$produce_minit.")*100)/".$efficiency_min."<br>";
                            $buyer_wise_avai_min_array[$ex_data[3]]['available_min']+=($r['smv_adjust']+($r['man_power']*$cla_cur_time*60));
                            $l_chk_array[$l_key][$dt_key] = $dt_key;
                            
                            // echo $efficiency_min."<br>";
                        }
                    }
                    // echo $l_key."==(".$r['smv_adjust']."+".$r['man_power'].")*8*60<br>";
                }
                
            }
            // echo "<pre>";print_r($buyer_wise_avai_min_array);die;
            $check_array = array();
            foreach($sewing_line_buyer_wise_array as $buyer_key=> $buyer_val )
            {
                foreach($buyer_val as $po_key=> $po_val)
                {
                    foreach ($po_val as $item_key => $item_value) 
                    {
                        
                        foreach($item_value as $line_key=> $lineval)
                        {
                            foreach($lineval as $pro_date=> $row)
                            {
                                $efficiency_min=($production_data_arr[$line_key][$pro_date]['smv_adjust'])+($production_data_arr[$line_key][$pro_date]['man_power'])*8*60;
                                // echo $line_key."==(".$production_data_arr[$line_key][$pro_date]['smv_adjust'].")+(".$production_data_arr[$line_key][$pro_date]['man_power'].")*8*60<br>";
                                // echo $line_key."==".$efficiency_min."<br>";
                                $produce_minit = $buyer_wise_prod_min_array[$buyer_key][$po_key][$item_key][$line_key];
                                $line_efficiency=(($produce_minit)*100)/$efficiency_min;
                                // echo "((".$produce_minit.")*100)/".$efficiency_min."<br>";
                                // $buyer_wise_avai_min_array[$buyer_key]['available_min']+=$efficiency_min;

                                if($check_array[$line_key][$pro_date]=="")
                                {

                                    $buyer_wise_min_array[$buyer_key]['earn_min']+=$smv_adjust_array[$line_key][$pro_date]['smv_adjustment_plus'] - $smv_adjust_array[$line_key][$pro_date]['smv_adjustment_minus'];
                                    // echo $smv_adjust_array[$line_key][$pro_date]['smv_adjustment_plus'] ."-". $smv_adjust_array[$line_key][$pro_date]['smv_adjustment_minus']."<br>";
                                    // echo $line_key."==".$pro_date."<br>";
                                    $check_array[$line_key][$pro_date] = $line_key;
                                }
                            }

                        }
                    }

                }
            }
            // echo"<pre>";print_r($buyer_wise_min_array);

            ?>
            <table width="2310px" cellpadding="0" cellspacing="0" id="caption" align="center">
                <tr>
                <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:18px;">Group Name:<? $comp=explode(",",$company_name); echo $group_short_library[$company_group_library[$comp[0]]];?></td>
                </tr> 
                <tr>  
                <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>  
                <td align="center" width="100%" colspan="22" class="form_caption" style="font-size:14px;"><strong> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
                </tr>  
            </table>
        <div>
            <table width="2650px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
                        
                    <thead>
                        <tr>
                            <th  width="30" rowspan="2">SL</th>
                            <th  width="150" rowspan="2">Buyer Name</th>
                            <th  colspan="3">Knitting Production</th>
                            <th  colspan="3">Finish Fabrics Receive</th>
                            <th  colspan="3">Cutting</th>
                            <th  colspan="3">Sewing</th>
                            <th  colspan="3">Finishing</th>
                            <th  rowspan="2" width="80">Earn Min</th>
                            <th  rowspan="2" width="80" title="smv adjust +(manpower*current hour*60)">Available Min</th>
                            <th  width="80">Sewing CM Value</th>
                            <th  colspan="3">FOB Value(On Sewing Qty)</th>
                            <th  colspan="3">Ex-Factory Qty</th>
                            <th  colspan="3">Ex-Factory CM Value</th>
                            <th  colspan="3">FOB Value(On Ex-Factory Qty)</th>
                        </tr>
                        <tr>
                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th width="80">Total</th>

                            <th width="80">In House</th>
                            <th width="80">Sub Contact</th>
                            <th >Total</th>
                    </tr>
                </thead>
                <tbody>
                            <?
                            $i=1;
                            $tot_kint_qnty_inhouse = 0;
                            $tot_kint_qnty_outbond = 0;
                            $tot_kint_qnty = 0;

                            $tot_fin_qnty_inhouse = 0;
                            $tot_fin_qnty_outbond = 0;
                            $tot_fin_qnty = 0;

                            $tot_cutting_qnty_inhouse = 0;
                            $tot_cutting_qnty_outbound = 0;
                            $tot_cutting_qnty = 0;

                            $tot_sewingout_qnty_inhouse = 0;
                            $tot_sewingout_qnty_outbound = 0;
                            $tot_sewing_qnty = 0;

                            $tot_finish_qnty_inhouse = 0;
                            $tot_finish_qnty_outbound = 0;
                            $tot_finish_qnty = 0;

                            $tot_earn_min = 0;

                            $tot_aval_min = 0;

                            $tot_cm_value_in = 0;

                            $tot_sewingout_value_inhouse = 0;
                            $tot_sewingout_value_outbound = 0;
                            $tot_fob_sew = 0;

                            $tot_ex_qnty_inhouse = 0;
                            $tot_ex_qnty_outbound = 0;
                            $tot_ex_factory_smry = 0;

                            $tot_ex_cm_value_inhouse = 0;
                            $tot_ex_cm_value_outbound = 0;
                            $tot_ex_cm_value_in = 0;

                            $tot_ex_fobVal_inhouse = 0;
                            $tot_ex_factory_unitPrice = 0;
                            $tot_ex_fobVal = 0;
                            
                            
                            foreach($prod_date_buyer_wise_summary as $buyerKey => $buyer_value)
                            {
                                
                                ?>
                                <tr  bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $i; ?>">
                                        <td width="30"><p><?=$i?></p></td>
                                        <td width="150"><p><?= $buyer_short_library[$buyerKey];?></p></td>
                                        <td width="80"  align="right"><p><?= number_format($buyer_wise_kint_summary[$buyerKey][1],0);?></p></td>
                                        <td width="80"  align="right"><p><?= number_format($buyer_wise_kint_summary[$buyerKey][3],0);?></p></td>
                                        <td width="80"  align="right"><p><?=number_format(($buyer_wise_kint_summary[$buyerKey][1])+($buyer_wise_kint_summary[$buyerKey][3]),0)?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_wise_fin_summary[$buyerKey][1],0);?></p></td>
                                        <td width="80"  align="right"><p><?= number_format($buyer_wise_fin_summary[$buyerKey][3],0);?></p></td>
                                        <td width="80"  align="right"><p><?=number_format(($buyer_wise_fin_summary[$buyerKey][1])+($buyer_wise_fin_summary[$buyerKey][3]),0)?></p></td>

                                        <td width="80" align="right"><p><?= number_format($buyer_value['cutting_qnty_inhouse'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['cutting_qnty_outbound'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['cutting_qnty'],0);?></p></td>

                                        <td width="80" align="right"><p><?= number_format($buyer_value['sewingout_qnty_inhouse'],0) ;?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['sewingout_qnty_outbound'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format ($buyer_value['sewing_qnty'],0);?></p></td>

                                        <td width="80" align="right"><p><?= number_format ($buyer_value['finish_qnty_inhouse'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['finish_qnty_outbound'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['finish_qnty'],0);?></p></td>

                                        <td width="80" align="right"><p><?= number_format($buyer_wise_min_array[$buyerKey]['earn_min'],2);?></p></td>
                                        <td width="80" align="right"  title="smv adjust +(manpower*current hour*60)"><p><?= number_format($buyer_wise_avai_min_array[$buyerKey]['available_min'],2);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['cm_value_in'],0);?></p></td>

                                        <td width="80" align="right"><p><?= number_format($buyer_value['sewingout_value_inhouse'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['sewingout_value_outbound'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['sewingout_value_inhouse'] + $buyer_value['sewingout_value_outbound'],0)?></p></td>

                                        <td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_qnty_inhouse'],0) ;?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_qnty_outbound'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_smry'],0);?></p></td>

                                        <td width="80" align="right"><p><?= number_format($buyer_value['ex_cm_value_inhouse'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['ex_cm_value_outbound'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['ex_cm_value_in'],0);?></p></td>

                                        <td width="80" align="right"><p><?= number_format($buyer_value['ex_factory_smry_fobVal_inhouse'],0);?></p></td>
                                        <td width="80" align="right"><p><?= number_format($buyer_value['exfactory_unitPrice'],0);?></p></td>
                                        <td align="center"><p><?= number_format($buyer_value['ex_factory_smry_fobVal'],0);?></p></td>
                                        
                            </tr>
                            <?                      
                                                        $i++;
                                                        
                                                        $tot_kint_qnty_inhouse += $buyer_wise_kint_summary[$buyerKey][1] ;
                                                        $tot_kint_qnty_outbond += $buyer_wise_kint_summary[$buyerKey][3];
                                                        $tot_kint_qnty += ($buyer_wise_kint_summary[$buyerKey][1])+($buyer_wise_kint_summary[$buyerKey][3]);
                                    
                                                        $tot_fin_qnty_inhouse += $buyer_wise_fin_summary[$buyerKey][1] ;
                                                        $tot_fin_qnty_outbond += $buyer_wise_fin_summary[$buyerKey][3];
                                                        $tot_fin_qnty += (($buyer_wise_fin_summary[$buyerKey][1])+($buyer_wise_fin_summary[$buyerKey][3]));
                                    
                                                        $tot_cutting_qnty_inhouse += $buyer_value['cutting_qnty_inhouse'];
                                                        $tot_cutting_qnty_outbound += $buyer_value['cutting_qnty_outbound'];
                                                        $tot_cutting_qnty += $buyer_value['cutting_qnty'];
                                    
                                                        $tot_sewingout_qnty_inhouse += $buyer_value['sewingout_qnty_inhouse'];
                                                        $tot_sewingout_qnty_outbound += $buyer_value['sewingout_qnty_outbound'];
                                                        $tot_sewing_qnty += $buyer_value['sewing_qnty'];
                                    
                                                        $tot_finish_qnty_inhouse += $buyer_value['finish_qnty_inhouse'];
                                                        $tot_finish_qnty_outbound += $buyer_value['finish_qnty_outbound'];
                                                        $tot_finish_qnty += $buyer_value['finish_qnty'];
                                    
                                                        $tot_earn_min += $buyer_wise_min_array[$buyerKey]['earn_min'];
                                    
                                                        $tot_aval_min += $buyer_wise_avai_min_array[$buyerKey]['available_min'];
                                    
                                                        $tot_cm_value_in +=$buyer_value['cm_value_in'] ;
                                    
                                                        $tot_sewingout_value_inhouse += $buyer_value['sewingout_value_inhouse'];
                                                        $tot_sewingout_value_outbound += $buyer_value['ex_factory_qnty_outbound'];
                                                        $tot_fob_sew += ($buyer_value['sewingout_value_inhouse'] + $buyer_value['sewingout_value_outbound']);
                                    
                                                        $tot_ex_qnty_inhouse += $buyer_value['ex_factory_qnty_inhouse'];
                                                        $tot_ex_qnty_outbound += $buyer_value['ex_factory_qnty_outbound'];
                                                        $tot_ex_factory_smry += $buyer_value['ex_factory_smry'];
                                    
                                                        $tot_ex_cm_value_inhouse += $buyer_value['ex_cm_value_inhouse'];
                                                        $tot_ex_cm_value_outbound += $buyer_value['ex_cm_value_outbound'];;
                                                        $tot_ex_cm_value_in += $buyer_value['ex_cm_value_in'];;
                                    
                                                        $tot_ex_fobVal_inhouse += $buyer_value['ex_factory_smry_fobVal_inhouse'];;
                                                        $tot_ex_factory_unitPrice += $buyer_value['exfactory_unitPrice'];;
                                                        $tot_ex_fobVal += $buyer_value['ex_factory_smry_fobVal'];;
                                                        
                                    }
                                    
                            ?>
                            
                        </tbody>

                        <tfoot>
                            <tr>
                                        <th width="30"><p></p></th>
                                        <th width="150">Total</th>

                                        <th width="80"><?= number_format($tot_kint_qnty_inhouse,0);?></th>
                                        <th width="80"><?= number_format($tot_kint_qnty_outbond,0);?></th>
                                        <th width="80"><?= number_format($tot_kint_qnty,0);?></th>

                                        <th width="80"><?= number_format($tot_fin_qnty_inhouse,0);?></th>
                                        <th width="80"><?= number_format($tot_fin_qnty_outbond,0);?></th>
                                        <th width="80"><?= number_format($tot_fin_qnty,0);?></th>

                                        <th width="80"><p><?= number_format($tot_cutting_qnty_inhouse,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_cutting_qnty_outbound,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_cutting_qnty,0);?></p></th>

                                        <th width="80"><p><?= number_format($tot_sewingout_qnty_inhouse,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_sewingout_qnty_outbound,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_sewing_qnty,0);?></p></th>

                                        <th width="80"><p><?= number_format($tot_finish_qnty_inhouse,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_finish_qnty_outbound,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_finish_qnty,0);?></p></th>

                                        <th width="80"><p><?= number_format($tot_earn_min,2); ?></p></th>
                                        <th width="80"><p><?=number_format($tot_aval_min,2);?></p></th>

                                        <th width="80"><p><?= number_format($tot_cm_value_in,0);?></p></th>

                                        <th width="80"><p><?= number_format($tot_sewingout_value_inhouse,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_sewingout_value_outbound,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_fob_sew,0);?></p></th>

                                        <th width="80"><p><?= number_format($tot_ex_qnty_inhouse,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_ex_qnty_outbound,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_ex_factory_smry,0);?></p></th>

                                        <th width="80"><p><?= number_format($tot_ex_cm_value_inhouse,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_ex_cm_value_outbound,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_ex_cm_value_in,0);?></p></th>

                                        <th width="80"><p><?= number_format($tot_ex_fobVal_inhouse,0);?></p></th>
                                        <th width="80"><p><?= number_format($tot_ex_factory_unitPrice,0);?></p></th>
                                        <th style="text-align: center;"><p><?= number_format($tot_ex_fobVal,0);?></p></th>
                                </tr>		
                        </tfoot>
                        
                </table>
            </div>
            <br/>
            <?		
            
        
        
        }


        $html = ob_get_contents();
		ob_clean();

		foreach (glob("../tmp/daily_factory_production"."*.pdf") as $filename) {			
			@unlink($filename);
		}
        $pdfObj = ['mode' => 'utf-8', 'format' => [190, 436]];

		$att_file_arr=array();
		$mpdf = new mPDF($pdfObj);
		$mpdf->WriteHTML($html,2);
		$REAL_FILE_NAME = 'daily_factory_production_'.$company_name .'_'. date('j-M-Y_h-iA') . '.pdf';
		$mpdf->Output('../tmp/' . $REAL_FILE_NAME, 'F');
		$att_file_arr[]='../tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;


		$mail_item=123;
		$to="";	
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and a.company_id=".$company_name." and b.mail_user_setup_id=c.id   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";//and 
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if($row[csf('email_address')]){$toMailArr[]=$row[csf('email_address')]; }
		}
		
		$to=implode(',',$toMailArr);
		$subject = "Daily Factory Production";
		$message="<b>Sir,</b><br>Please check Daily Factory Production";
		
		

		$header=mailHeader();
		//$to="reza@logicsoftbd.com";

		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo  $message."<br>".$html;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
		}



    }//company loof end;
	exit(); 
    
}


?>