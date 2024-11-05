<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data']; 
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );$color_Arr_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );	
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.party_type not in('2') order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );
  exit();	 
}
if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";




if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$hidden_order_id=str_replace("'","",$hidden_order_id);
	$date_to=str_replace("'","",$txt_date_to);
	$date_from=str_replace("'","",$txt_date_from);
	//$txt_production_date=str_replace("'","",$txt_production_date);
	$job_no=str_replace("'","",$txt_job_no);
	$hiden_order_id=str_replace("'","",$hiden_order_id);
	$cbo_work_company_name=str_replace("'","",$cbo_work_company_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);

    $hidden_job_year=str_replace("'","",$hidden_job_year);

	if($cbo_year==0){
        $cbo_year=$hidden_job_year;
        }
       

	$job_po_id="";
   
	
	
	if(str_replace("'","",$txt_job_no)!="")
	{
		if($db_type==0)
		{
			$job_po_id=return_field_value("group_concat(b.id) as po_id","wo_po_break_down b","b.job_no_mst in ('UHM-21-00192')","po_id");
			
		}
		else
		{
			$job_id=return_field_value("listagg(cast(job_no as varchar(4000)),',') within group(order by id) as job_no","wo_po_details_master","JOB_NO_PREFIX_NUM in ($txt_job_no) and to_char(insert_date,'YYYY') in ($cbo_year) and company_name=$cbo_work_company_name","job_no");
			$job_arr=explode(",",$job_id);
			foreach($job_arr as $val){
				$job_poid=return_field_value("listagg(cast(b.id as varchar(4000)),',') within group(order by id) as po_id","wo_po_break_down b","b.job_no_mst in ('$val')","po_id");
			}
			
		}
		
	}



    //  echo $job_poid;die;

	if($cbo_buyer_name >0){
		$buyer_cond="and e.buyer_name='$cbo_buyer_name'";
		$buyer_cond_2="and d.buyer_name='$cbo_buyer_name'";
		$buyer_cond_3="and a.buyer_name='$cbo_buyer_name'";
		
	}else{
		$buyer_cond="";
		$buyer_cond_2="";
		$buyer_cond_3="";
	}



	if($job_no!=""){
		$job_cond="and d.job_no_prefix_num in ($job_no)";
		$job_cond_2="and e.job_no_prefix_num in ($job_no)";
		$job_cond_3="and a.job_no_prefix_num in ($job_no)";
		
	}else{
		$job_cond="";
		$job_cond_2="";
	}
	if($job_no!=""){
			if($cbo_year!=""){		
				$job_year_cond="and to_char(e.insert_date,'YYYY') in ($cbo_year)";	
				$job_year_cond_2="and to_char(d.insert_date,'YYYY') in ($cbo_year)";
				$job_year_cond_3="and to_char(a.insert_date,'YYYY') in ($cbo_year)";	
					
			}else{
				$job_year_cond="";
				$job_year_cond_2="";
				$job_year_cond_3="";			
			
			}
		}
	
	
	$order_cond_lay="";
	$order_cond_prod="";
	$order_d_cond="";

	if($order_cond_lay!=""){$order_cond_lay.=" and c.order_id in($job_poid)";}else{$order_cond_lay="";}
	if($order_cond!=""){$order_cond.=" and a.po_break_down_id in($job_poid)";}else{$order_cond="";}
	if($order_d_cond!=""){$order_d_cond .=" and g.po_break_down_id in($job_poid)";}else{$order_d_cond="";}



	//  echo $order_cond."<br>";
	//  echo $order_cond_lay."<br>";
	// die();
	
	

	if($cbo_company_name>0){ $company_cond=" and a.company_name=$cbo_company_name";$company_cond_2=" and a.company_id=$cbo_company_name";}

		
	 $current_date=change_date_format(date( 'd-m-Y' ),'dd-mm-yyyy','-',1);
	///$current_date="07-Jun-2021";

	if($date_from=="" && $date_to=="" ){ $date_cond="";}else{
		
		 $date_cond=" and a.production_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_2=" and m.delivery_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_3=" and a.entry_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_4=" and entry_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";

		 $date_cond_5=" and d.production_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		 $date_cond_6=" and c.country_ship_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
		
	}
	$clientArr 	= return_library_array( "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id","buyer_name"); 
	$sizearr=return_library_array("SELECT id,size_name from lib_size ","id","size_name");  
	 if($type==1)
	 {
			
			// ============================== For Cut and Lay Entry Roll Wise entry form =============================================
			
			 //echo $production_sql;// die;				
		
			$sql_lay="SELECT d.size_id, d.size_qty,c.gmt_item_id,c.color_id,d.order_id,e.buyer_name,a.job_no,e.style_ref_no,e.client_id from ppl_cut_lay_mst a,ppl_cut_lay_dtls c,ppl_cut_lay_bundle d ,wo_po_details_master e, wo_po_break_down f where e.job_no=f.job_no_mst and e.job_no=a.job_no and d.order_id=f.id and  a.id=c.mst_id  and a.id=d.mst_id and a.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active in(1,2,3) $date_cond_3 and a.WORKING_COMPANY_ID in ($cbo_work_company_name) $job_cond_2 $buyer_cond $job_year_cond order by d.size_id asc";

		//	echo $sql_lay;
			 
			$sql_lay_result=sql_select($sql_lay);
			$production_data=$porduction_ord_id=$lay_order_id=array();
			$garments_order_id_arr=array();
			$l=0;
			foreach($sql_lay_result as $row)
			{
				
				$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["cut_lay"]+=$row[csf("size_qty")];
				if($l==0){
					$l_order_id .=$row[csf("order_id")];
					$l++;
				}else{
					$l_order_id .=",".$row[csf("order_id")];
				}
                $job_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
                $po_number_array_check[$row[csf("job_no")]]=$row[csf("order_id")];
				
			}
			// print_r($order_color_size_data);

			$l_order=array_unique(explode(",",$l_order_id));
			$l_po_arr=implode(",",$l_order);		
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
                if($l_po_arr){
				$d_l_order_cond="and d.order_id in ($l_po_arr)";
                }
			}else{
				$d_l_order_cond="";
			}
		
			$sql_lay_prev="SELECT d.size_id, d.size_qty,c.gmt_item_id,c.color_id,d.order_id,e.buyer_name,a.job_no,e.client_id as buyer_client from ppl_cut_lay_mst a,ppl_cut_lay_dtls c,ppl_cut_lay_bundle d ,wo_po_details_master e, wo_po_break_down f where e.job_no=f.job_no_mst and e.job_no=a.job_no and d.order_id=f.id and  a.id=c.mst_id  and a.id=d.mst_id and a.status_active=1  and c.status_active=1 and d.status_active=1 and a.WORKING_COMPANY_ID in ($cbo_work_company_name) $job_year_cond  $job_cond_2 $buyer_cond order by d.size_id  asc";
					// echo $sql_lay_prev;
				$sql_lay_prev_result=sql_select($sql_lay_prev);
				foreach($sql_lay_prev_result as $row)
				{
					

					$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("gmt_item_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["lay_prev_qnty"]+=$row[csf("size_qty")];

                    $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                    $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
				}
			
            //===============================================sewing in========================================================
           
			$sewing_in_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,
				sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_qnty,d.job_no,d.style_ref_no,d.client_id,c.order_quantity
				from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e
				where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=4 and a.production_type=4  and a.serving_company in($cbo_work_company_name) $date_cond $job_cond $buyer_cond_2 $job_year_cond_2 group by a.production_date, c.po_break_down_id , c.item_number_id, d.buyer_name, c.color_number_id ,c.size_number_id,d.job_no,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order  order by c.color_order,c.size_order asc";

				//echo $sewing_in_sql;
	

			$sewing_in_sql_result=sql_select($sewing_in_sql);
			$si=0;
			foreach($sewing_in_sql_result as $row)
			{
				if($si==0){
					$si_order_id .=$row[csf("order_id")];
					$si++;
				}else{
					$si_order_id .=",".$row[csf("order_id")];
				}
	

				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_in"]+=$row[csf("sewing_in_qnty")];
			
			}
			$si_order=array_unique(explode(",",$si_order_id));
			$si_po_arr=implode(",",$si_order);			
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				
                $d_si_order_cond="and  c.po_break_down_id in ($si_po_arr)";
			}else{
				$d_si_order_cond="";
			}

			
			$sewing_in_tot_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,
			sum(CASE WHEN b.production_type =4 and a.production_type =4 THEN b.production_qnty ELSE 0 END) AS sewing_in_prev_qnty,d.job_no,d.client_id as buyer_client
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e
			where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=4 and a.production_type=4  and a.serving_company in($cbo_work_company_name)  
			$job_cond $buyer_cond_2 $job_year_cond_2 group by a.production_date, c.po_break_down_id , c.item_number_id, d.buyer_name, c.color_number_id ,c.size_number_id,
			d.job_no,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";
			$sewing_in_tot_sql_data=sql_select($sewing_in_tot_sql);
			foreach($sewing_in_tot_sql_data as $row)
			{
				
				
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_in_prev_qnty"]+=$row[csf("sewing_in_prev_qnty")];
                 $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
                 $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
			}


            
        //===============================================sewing out========================================================

			$sewing_out_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,			
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_qnty,d.job_no,d.style_ref_no,d.client_id,c.order_quantity
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=5 and a.production_type=5  and a.serving_company in($cbo_work_company_name) $date_cond  $job_cond $buyer_cond_2 $job_year_cond_2 group by a.production_date, c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";

			$sewing_out_sql_result=sql_select($sewing_out_sql);
			$so=0;
			foreach($sewing_out_sql_result as $row)
			{
				if($so==0){
					$so_order_id .=$row[csf("order_id")];
					$so++;
				}else{
					$so_order_id .=",".$row[csf("order_id")];
				}

		
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_out"]+=$row[csf("sewing_out_qnty")];
				
			}
			$so_order=array_unique(explode(",",$so_order_id));
			$so_po_arr=implode(",",$so_order);			
		
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				
                $d_so_order_cond="and  c.po_break_down_id in ($so_po_arr)";
			}else{
				$d_so_order_cond="";
			}


			$sewing_out_tot_sql="SELECT a.production_date, c.po_break_down_id as order_id, c.item_number_id, d.buyer_name, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,			
			sum(CASE WHEN b.production_type =5 and a.production_type =5 THEN b.production_qnty ELSE 0 END) AS sewing_out_prev_qnty,d.job_no,d.client_id as buyer_client from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=5 and a.production_type=5  and a.serving_company in($cbo_work_company_name)   $job_cond $buyer_cond_2 $job_year_cond_2	group by a.production_date, c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";

			$sewing_out_tot_sql_data=sql_select($sewing_out_tot_sql);
			foreach($sewing_out_tot_sql_data as $row)
			{
				
				
			 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["sewing_out_prev_qnty"]+=$row[csf("sewing_out_prev_qnty")];
             $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
             $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];

			}
        //=================================================poly ==============================================
		
			$poly_sql="SELECT  c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,	sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS poly_qnty,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=11 and a.production_type=11   and a.serving_company in($cbo_work_company_name) $date_cond $job_cond  $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";


			$poly_sql_result=sql_select($poly_sql);
			$p=0;
			foreach($poly_sql_result as $row)
			{
				if($p==0){
					$p_order_id .=$row[csf("order_id")];
					$p++;
				}else{
					$p_order_id .=",".$row[csf("order_id")];
				}
			
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["poly_qnty"]+=$row[csf("poly_qnty")];

				
			}
			$p_order=array_unique(explode(",",$p_order_id));
			$p_po_arr=implode(",",$p_order);		
	
			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
			
                $d_p_order_cond="and  c.po_break_down_id in ($p_po_arr)";
			}else{
				$d_p_order_cond="";
			}

		
			$poly_tot_sql="SELECT  c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,sum(CASE WHEN b.production_type =11 and a.production_type=11 THEN b.production_qnty ELSE 0 END) AS poly_prev_qnty,d.job_no, d.buyer_name,d.client_id as buyer_client	from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=11 and a.production_type=11  and a.serving_company in($cbo_work_company_name)   $job_cond $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,c.color_order,c.size_order,d.client_id order by  c.color_order,c.size_order asc";

			$poly_sql_data=sql_select($poly_tot_sql);
			foreach($poly_sql_data as $row)
			{
				
				
				 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["poly_prev_qnty"]+=$row[csf("poly_prev_qnty")];
                 $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                 $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
			}

			
    

			$paking_finish_sql="SELECT c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,	sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_qnty,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity
			from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=8 and a.production_type=8   and a.serving_company in($cbo_work_company_name) $date_cond $job_cond $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id,c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";
	    //	echo 	$paking_finish_sql;
            $paking_finish_sql_result=sql_select($paking_finish_sql);
            $pf=0;
            foreach($paking_finish_sql_result as $row)
            {
                if($pf==0){
                    $pf_order_id .=$row[csf("order_id")];
                    $pf++;
                }else{
                    $pf_order_id .=",".$row[csf("order_id")];
                }

            
                    $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["paking_finish_qnty"]+=$row[csf("paking_finish_qnty")];
                
            }

		$pf_order=array_unique(explode(",",$pf_order_id));
		$p_f_po_arr=implode(",",$pf_order);	

		if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
			
            $d_pf_order_cond="and  c.po_break_down_id in ($p_f_po_arr)";

		}else{
			$d_pf_order_cond="";
		}

       
	
		$paking_finish_tot_sql="SELECT c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.size_number_id as size_id,c.color_order,c.size_order,	sum(CASE WHEN b.production_type =8 and a.production_type =8 THEN b.production_qnty ELSE 0 END) AS paking_finish_prev_qnty,d.job_no, d.buyer_name,d.client_id as buyer_client from  pro_garments_production_dtls b,pro_garments_production_mst a,wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and d.job_no=c.job_no_mst and c.po_break_down_id=e.id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active in(1,2,3) and c.is_deleted=0 and a.production_source=1 and b.production_type=8 and a.production_type=8   and a.serving_company in($cbo_work_company_name)   $job_cond $buyer_cond_2 $job_year_cond_2 group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id,d.job_no, d.buyer_name,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";
			// echo $paking_finish_tot_sql;
				
			$paking_finish_tot_sql_result=sql_select($paking_finish_tot_sql);
			foreach($paking_finish_tot_sql_result as $row)
			{
					 $order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["paking_finish_prev_qnty"]+=$row[csf("paking_finish_prev_qnty")];
                    $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                    $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
			}
			//	echo $p_f_po_arr;die; 

				$ex_factory_sql="SELECT  a.po_break_down_id as order_id, a.item_number_id, c.color_number_id as color_id,c.color_order,c.size_order, sum(b.production_qnty) as ex_fact_qnty ,c.size_number_id as size_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id, c.order_quantity from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e  where  d.job_no=e.job_no_mst and a.po_break_down_id=e.id and m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85  and m.delivery_company_id in ($cbo_work_company_name) $date_cond_2 $buyer_cond_2  $job_cond $job_year_cond_2 
				group by  a.po_break_down_id, a.item_number_id, c.color_number_id,c.size_number_id ,d.job_no, d.buyer_name,d.style_ref_no,d.client_id, c.order_quantity,c.color_order,c.size_order order by c.color_order,c.size_order asc";
			//echo $ex_factory_sql;
		
			$ex_factory_sql_result=sql_select($ex_factory_sql);
			$ex=0;
			foreach($ex_factory_sql_result as $row)
			{
				if($ex==0){
					$ex_order_id .=$row[csf("order_id")];
					$ex++;
				}else{
					$ex_order_id .=",".$row[csf("order_id")];
				}
			
				$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["ex_fact_qnty"]+=$row[csf("ex_fact_qnty")];
				
			}

			$ex_order=array_unique(explode(",",$ex_order_id));
			$ex_po_arr=implode(",",$ex_order);

			if($date_from !=="" && $date_to !=="" && $job_cond_2 ==""){
				
                $d_ex_order_cond="and  c.po_break_down_id in ($ex_po_arr)";
			}else{
				$d_ex_order_cond="";
			}
			$all_po_list=implode(",",array_unique(array_merge($l_order,$si_order,$so_order,$p_order,$pf_order,$ex_order)));;
			
			$ex_factory_tot_sql="SELECT  c.po_break_down_id as order_id, c.item_number_id, c.color_number_id as color_id,c.color_order,c.size_order, sum(b.production_qnty) as ex_fact_prev_qnty ,c.size_number_id as size_id,d.job_no, d.buyer_name,d.style_ref_no,d.client_id as buyer_client
			from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c ,wo_po_details_master d, wo_po_break_down e where  d.job_no=e.job_no_mst and a.po_break_down_id=e.id and m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 and m.entry_form!=85  and m.delivery_company_id in ($cbo_work_company_name) $job_cond    $buyer_cond_2 $job_year_cond_2
			group by  c.po_break_down_id, c.item_number_id, c.color_number_id,c.size_number_id ,d.job_no, d.buyer_name,d.style_ref_no ,c.color_order,c.size_order,d.client_id order by c.color_order,c.size_order asc";
		
			// echo 	$ex_factory_tot_sql;
			$ex_factory_tot_sql_data=sql_select($ex_factory_tot_sql);
			foreach($ex_factory_tot_sql_data as $row)
			{

				
				$order_color_size_data_2[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("item_number_id")]][$row[csf("color_id")]][$row[csf("size_id")]]["ex_fact_prev_qnty"]+=$row[csf("ex_fact_prev_qnty")];
                $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                $po_number_array_check[$row[csf("order_id")]]=$row[csf("order_id")];
			}

			//  echo "<pre>";
			// print_r($production_data);die;
			
			
			
			
		
			
				$sql_data=sql_select("SELECT  a.id,a.client_id as buyer_client,b.po_number,a.style_ref_no,c.order_quantity, c.color_number_id,a.buyer_name, c.item_number_id,c.size_number_id,a.job_no,c.po_break_down_id,c.color_order,c.size_order  FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where
				a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 $job_cond_3 $buyer_cond_3 $job_year_cond_3 order by c.color_order,c.size_order asc");
				foreach($sql_data as $row) 
				{
                   
                    $po_arr[$row[csf("po_break_down_id")]]=$row[csf("po_number")];
                    if($po_number_array_check[$row[csf("po_break_down_id")]]){

                        $order_color_size_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['po_qty']+=$row[csf("order_quantity")];
                        $buyer_wise_data[$row[csf("job_no")]]['client_id']=$row[csf("buyer_client")];
                    }
					$order_qty_color_size_data[$row[csf("buyer_name")]][$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("item_number_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]["order_quantity"]+=$row[csf("order_quantity")];
					// $buyer_wise_data[$row[csf("buyer_name")]]['style_ref_no']=$row[csf("style_ref_no")];
                    $style_ref_no_arr[$row[csf("job_no")]]['style_ref_no']=$row[csf("style_ref_no")];
					// $buyer_wise_data[$row[csf("buyer_name")]]['client_id']=$row[csf("client_id")];
				}


		
  
			
			//echo $sql_color_size;die;
		 //  print_r($order_color_data);
		
			ob_start();
		 ?>
        <fieldset style="width:2480px;">
        <div style="width:2480px;">
        <table width="2040" cellspacing="0">
            <tr class="form_caption" style="border:none;">
                <td colspan="31" align="center" style="border:none;font-size:14px; font-weight:bold">Size Wise GMTS Production Report </td>
            </tr>
            <tr style="border:none;">
                <td colspan="31" align="center" style="border:none; font-size:16px; font-weight:bold">
                    Working Company Name:
                    <? 
							$cbo_work_company_name_arr=explode(",",$cbo_work_company_name);
							$workingCompanyName="";
							foreach ($cbo_work_company_name_arr as $workig_cmp_name)
							{
								$workingCompanyName.= $company_arr[$workig_cmp_name]; 
							}
							echo chop($workingCompanyName);
							?>
                </td>
            </tr>

            <tr style="border:none;">
                <td colspan="31" align="center" style="border:none;font-size:12px; font-weight:bold">
                    Date:
                    <? echo $date_from." To ".$date_to ;?>
                </td>
            </tr>
        </table>
        <br />

        <fieldset style="width:2480px; float:left;">
            <legend>Report Details Part</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2460" class="rpt_table" align="left">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Client</th>
                        <th width="100" rowspan="2">Style Ref</th>
                        <th width="60" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Garment Item</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Size</th>
                        <th width="70" rowspan="2">Order Qty.</th>
                        <th width="140" colspan="3">Lay Quantity</th>
                        <th width="140" colspan="3">Sewing Input</th>
                        <th width="140" colspan="3">Sewing Output</th>
                        <th width="140" colspan="3">Poly Entry</th>
                        <th width="140" colspan="3">Packing & Finishing</th>
                        <th width="140" colspan="3">Ex-Factory</th>

                    </tr>
                    <tr>

                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>

                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>


                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70">Balance </th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:2480px;" id="scroll_body">
                <table border="1" class="rpt_table" width="2460" rules="all" id="table_body" align="left">
                    <tbody>
                        <?
				//echo "<pre>";print_r($production_data);die;
				$i=1;
				foreach($order_color_size_data as $buyer_id=>$buyer_data)
				{
					foreach($buyer_data as $job_no=>$job_data)
					{
						foreach($job_data as $order_id=>$order_data)
						{
							foreach($order_data as $item_id=>$item_data)
							{
								foreach($item_data as $color_id=>$color_data)
								{
									foreach($color_data as $size_id=>$value)
									{
										
										
										
											$tot_lay_qnty=$tot_cutting_qnty=$tot_printing_qnty=$tot_printing_rcv_qnty=$tot_embroidery_qnty=$tot_embroidery_rcv_qnty=$tot_sewing_in_qnty=$tot_sewing_out_qnty=$tot_poly_qnty=$tot_paking_finish_qnty=$tot_ex_fact_qnty=0;
											$cut_qc_wip=$printing_wip=$emb_wip=$wash_wip=$sp_work_wip=$sewing_wip=$ex_fact_wip=$ex_fact_wip=0;
											$total_sp_work_reject=$total_sewing_reject=$total_poly_reject=$total_finish_reject=0;
											$po_id=$value['po_id'];
											
											if ($i%2==0)
											$bgcolor="#E9F3FF";
											else
											$bgcolor="#FFFFFF";

                                            $style_ref_no=$style_ref_no_arr[$job_no]['style_ref_no'];
											$client_id=$buyer_wise_data[$job_no]['client_id'];
                                            
											$order_qty=$order_qty_color_size_data[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["order_quantity"];


                                            if($date_from !=="" && $date_to !=="" && $job_cond_2 =="" || $date_from !=="" && $date_to !=="" && $job_cond_2 !==""){
                                                $cut_lay=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["cut_lay"];

                                                $sewing_in=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_in"];

                                                $sewing_out=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_out"];

                                                $poly_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["poly_qnty"];

                                                $paking_finish_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["paking_finish_qnty"];

                                                $ex_fact_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["ex_fact_qnty"];

                                            }


                         if($cut_lay>0 || $sewing_in>0 || $sewing_out>0 ||  $poly_qnty>0 || $paking_finish_qnty>0 ||               $ex_fact_qnty>0){




											?>
                        <tr bgcolor="<? echo $bgcolor; ?>"
                            onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')"
                            id="tr_2nd<? echo $i; ?>">
                            <td width="40" align="center">
                                <? echo $i; ?>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $buyer_short_library[$buyer_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $clientArr[$client_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $style_ref_no; ?>&nbsp;
                                </p>
                            </td>
                            <td width="60" align="center">
                                <p>
                                    <? echo $job_no; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100" title="<?=$order_id;?>">
                                <p>
                                    <? echo $po_arr[$order_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $garments_item[$item_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $colorname_arr[$color_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="100">
                                <p>
                                    <? echo $sizearr[$size_id]; ?>&nbsp;
                                </p>
                            </td>
                            <td width="70" align="right">
                                <?

												echo $order_qty;
												// echo number_format($value["order_quantity"],0);
												 $job_order_qnty+=$order_qty;
												 $color_order_qnty+=$order_qty;
												 $item_order_qnty+=$order_qty;
												 $po_order_qnty+=$order_qty;
												 $buyer_order_qnty+=$order_qty; 
												 $gt_order_qnty+=$order_qty; ?>
                            </td>
                            <td width="70" align="right">
                                <?
											
							

                                     

														echo $cut_lay;
														
														$job_lay_qnty+=$cut_lay;
														$color_lay_qnty+=$cut_lay;
														$item_lay_qnty+=$cut_lay;
														$po_lay_qnty+=$cut_lay;
														$buyer_lay_qnty+=$cut_lay; 
														$gt_lay_qnty+=$cut_lay;

												
													?>


                            </td>
                            <td width="70" align="right">
                                <? 
                                       $lay_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["lay_prev_qnty"];

												echo $lay_prev_qnty;
												// $tot_lay_qnty=$production_data[$order_id][$item_id][$color_id][$size_id]["lay_prev_qnty"];
												//  echo number_format($tot_lay_qnty,0); 
												$tot_lay_qnty=$lay_prev_qnty;
												$job_tot_lay_qnty+=$tot_lay_qnty;
												 $color_tot_lay_qnty+=$tot_lay_qnty; 
												 $item_tot_lay_qnty+=$tot_lay_qnty; 
												 $po_tot_lay_qnty+=$tot_lay_qnty;
												$buyer_tot_lay_qnty+=$tot_lay_qnty; 
												$gt_tot_lay_qnty+=$tot_lay_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_lay_qnty=$order_qty-$tot_lay_qnty; echo number_format($bal_lay_qnty,0);
												$tot_bal_lay_qnty+=$bal_lay_qnty;
												$job_bal_lay_qnty+=$bal_lay_qnty;
												$color_bal_lay_qnty+=$bal_lay_qnty;
												$item_bal_lay_qnty+=$bal_lay_qnty;
												$po_bal_lay_qnty+=$bal_lay_qnty;
												$buyer_bal_lay_qnty+=$bal_lay_qnty;
												$gt_bal_lay_qnty+=$bal_lay_qnty;
												
												?>
                            </td>
                            <td width="70" align="right">
                                <?
											
														echo  $sewing_in;

													// number_format($production_data[$order_id][$item_id][$color_id][$size_id]["sewing_in_qnty"],0); 								
													
													
													$job_sewing_in_qnty+= $sewing_in;
													$color_sewing_in_qnty+= $sewing_in;
													$item_sewing_in_qnty+= $sewing_in;
													$po_sewing_in_qnty+= $sewing_in;
													$buyer_sewing_in_qnty+=$ $sewing_in; $gt_sewing_in_qnty+= $sewing_in;

													
															?>
                            </td>
                            <td width="70" align="right">
                                <p>
                                    <? 

                                                    $sewing_in_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_in_prev_qnty"];

                                                    echo  $sewing_in_prev_qnty;

													// echo $value["sewing_in_prev_qnty"];
											
												$tot_sewing_in_qnty=$sewing_in_prev_qnty;
												$job_tot_sewing_in_qnty+=$tot_sewing_in_qnty;
												$color_tot_sewing_in_qnty+=$tot_sewing_in_qnty;
												$item_tot_sewing_in_qnty+=$tot_sewing_in_qnty;
												$po_tot_sewing_in_qnty+=$tot_sewing_in_qnty;	
												$buyer_tot_sewing_in_qnty+=$tot_sewing_in_qnty; 
												$gt_tot_sewing_in_qnty+=$tot_sewing_in_qnty;?>
                                </p>
                            </td>

                            <td width="70" align="right">
                                <p>
                                    <? $bal_sewing_in_qnty=$tot_lay_qnty-$tot_sewing_in_qnty; echo number_format($bal_sewing_in_qnty,0);
												$job_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$color_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$item_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$po_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$gt_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												$buyer_bal_sewing_in_qnty+=$bal_sewing_in_qnty;
												
												
												?>
                                </p>
                            </td>

                            <td width="70" align="right">
                                <? 

                                                             
													echo  $sewing_out;
											
													
														
												$job_sewing_out_qnty+=$sewing_out;
												 $color_sewing_out_qnty+=$sewing_out;
												 $item_sewing_out_qnty+=$sewing_out;
												 $po_sewing_out_qnty+=$sewing_out;
												$buyer_sewing_out_qnty+=$sewing_out; 
												$gt_sewing_out_qnty+=$sewing_out;
															
												?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                                $sewing_out_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["sewing_out_prev_qnty"];
                                                echo  $sewing_out_prev_qnty;

												// echo $value["sewing_out_prev_qnty"];
											
												$tot_sewing_out_qnty=$sewing_out_prev_qnty;
												$job_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$color_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$item_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$po_tot_sewing_out_qnty+=$tot_sewing_out_qnty;
												$buyer_tot_sewing_out_qnty+=$tot_sewing_out_qnty; 
												$gt_tot_sewing_out_qnty+=$tot_sewing_out_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_sewing_out_qnty=$tot_sewing_in_qnty-$tot_sewing_out_qnty; echo number_format($bal_sewing_out_qnty,0);
												$job_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$color_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$item_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$po_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$gt_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												$buyer_bal_sewing_out_qnty+=$bal_sewing_out_qnty;
												
												
												?>
                            </td>



                            <td width="70" align="right">
                                <? 
                                   

                                      
													echo $poly_qnty;
											
												 $job_poly_qnty+=$poly_qnty;
												$color_poly_qnty+=$poly_qnty;
												$item_poly_qnty+=$poly_qnty;
												$po_poly_qnty+=$poly_qnty;									
												$buyer_poly_qnty+=$poly_qnty;
												 $gt_poly_qnty+=$poly_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                                $poly_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["poly_prev_qnty"];

                                                        echo $poly_prev_qnty;

												// echo $value["poly_prev_qnty"];
										
												$tot_poly_qnty=$poly_prev_qnty;
												$job_tot_poly_qnty+=$tot_poly_qnty;
												$color_tot_poly_qnty+=$tot_poly_qnty;
												$item_tot_poly_qnty+=$tot_poly_qnty;
												$po_tot_poly_qnty+=$tot_poly_qnty;
												$buyer_tot_poly_qnty+=$tot_poly_qnty; $gt_tot_poly_qnty+=$tot_poly_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <?              $bal_poly_qnty=$tot_sewing_in_qnty-$tot_poly_qnty; echo number_format($bal_poly_qnty,0);
												$job_bal_poly_qnty+=$bal_poly_qnty;
												$color_bal_poly_qnty+=$bal_poly_qnty;
												$item_bal_poly_qnty+=$bal_poly_qnty;
												$po_bal_poly_qnty+=$bal_poly_qnty;
												$gt_bal_poly_qnty+=$tot_poly_qnty;
												$buyer_bal_poly_qnty+=$tot_poly_qnty;
												
												?>
                            </td>


                            <td width="70" align="right">
                                <?
												

                                                               
												echo $paking_finish_qnty;
											
											
												$job_paking_finish_qnty+=$paking_finish_qnty;
												$color_paking_finish_qnty+=$paking_finish_qnty;
												$item_paking_finish_qnty+=$paking_finish_qnty;
												$po_paking_finish_qnty+=$paking_finish_qnty;
												$buyer_paking_finish_qnty+=$paking_finish_qnty;
												$gt_paking_finish_qnty+=$paking_finish_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                                $paking_finish_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["paking_finish_prev_qnty"];
                                                echo $paking_finish_prev_qnty;
													// echo $value["paking_finish_prev_qnty"];
											
												$tot_paking_finish_qnty=$paking_finish_prev_qnty;
												$job_tot_paking_finish_qnty+=$tot_paking_finish_qnty; 
												$color_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
												$item_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
												$po_tot_paking_finish_qnty+=$tot_paking_finish_qnty;
												$buyer_tot_paking_finish_qnty+=$tot_paking_finish_qnty; $gt_tot_paking_finish_qnty+=$tot_paking_finish_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                                $bal_paking_finish_qnty=$tot_poly_qnty-$tot_paking_finish_qnty; echo number_format($bal_paking_finish_qnty,0);
												$job_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$color_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$item_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$po_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$gt_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
												$buyer_bal_paking_finish_qnty+=$bal_paking_finish_qnty;
											
												
												?>
                            </td>


                            <td width="70" align="right">
                                <? 
														
													echo $ex_fact_qnty;
										
												$job_ex_fact_qnty+=$ex_fact_qnty;
												$color_ex_fact_qnty+=$ex_fact_qnty;
												$item_ex_fact_qnty+=$ex_fact_qnty;
												$po_ex_fact_qnty+=$ex_fact_qnty;
												 $buyer_ex_fact_qnty+=$ex_fact_qnty;
												 $gt_ex_fact_qnty+=$ex_fact_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <?
                                $ex_fact_prev_qnty=$order_color_size_data_2[$buyer_id][$job_no][$order_id][$item_id][$color_id][$size_id]["ex_fact_prev_qnty"];
													echo  $ex_fact_prev_qnty;
											
												$tot_ex_fact_qnty=$ex_fact_prev_qnty;
												 $job_tot_ex_fact_qnty+=$tot_ex_fact_qnty; 
												 $color_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
												 $item_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
												 $po_tot_ex_fact_qnty+=$tot_ex_fact_qnty;
												
												$buyer_tot_ex_fact_qnty+=$tot_ex_fact_qnty; $gt_tot_ex_fact_qnty+=$tot_ex_fact_qnty;?>
                            </td>
                            <td width="70" align="right">
                                <? $bal_ex_fact_qnty=$tot_paking_finish_qnty-$tot_ex_fact_qnty; echo number_format($bal_ex_fact_qnty,0); 
												$job_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$color_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$item_bal_ex_fact_qnty +=$bal_ex_fact_qnty;
												$po_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$gt_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												$buyer_bal_ex_fact_qnty+=$bal_ex_fact_qnty;
												
												?>
                            </td>

                        </tr>
                        <?
		                                    $i++;
											
                                            }
										
									}
									//  if(color_bal_lay_qnty>0 || $color_bal_sewing_in_qnty>0 || $color_bal_sewing_out_qnty>0 || $color_bal_poly_qnty>0 || $color_bal_paking_finish_qnty>0 || $color_bal_ex_fact_qnty>0)
									//  {
                                
                     if($color_lay_qnty>0 || $color_sewing_in_qnty>0 || $color_sewing_out_qnty>0 ||  $color_poly_qnty>0 || $color_paking_finish_qnty>0 || $color_ex_fact_qnty>0){
									?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">Color Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($color_order_qnty,0); $color_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($color_lay_qnty,0); $color_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_lay_qnty,0); $color_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_lay_qnty,0); $color_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($color_sewing_in_qnty,0); $color_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_sewing_in_qnty,0); $color_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_sewing_in_qnty,0); $color_bal_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($color_sewing_out_qnty,0); $color_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_sewing_out_qnty,0); $color_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_sewing_out_qnty,0); $color_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($color_poly_qnty,0); $color_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_poly_qnty,0); $color_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_poly_qnty,0); $color_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($color_paking_finish_qnty,0); $color_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_paking_finish_qnty,0); $color_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_paking_finish_qnty,0); $color_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($color_ex_fact_qnty,0); $color_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_tot_ex_fact_qnty,0); $color_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($color_bal_ex_fact_qnty,0); $color_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
								}
							}

                            if($item_lay_qnty>0 || $item_sewing_in_qnty>0 || $item_sewing_out_qnty >0 || $item_poly_qnty >0 || $item_paking_finish_qnty>0 || $item_ex_fact_qnty>0)
                                {
							
								?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">Item Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($item_order_qnty,0); $item_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($item_lay_qnty,0); $item_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_lay_qnty,0); $item_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_lay_qnty,0); $item_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($item_sewing_in_qnty,0); $item_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_sewing_in_qnty,0); $item_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_sewing_in_qnty,0); $item_bal_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($item_sewing_out_qnty,0); $item_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_sewing_out_qnty,0); $item_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_sewing_out_qnty,0); $item_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($item_poly_qnty,0); $item_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_poly_qnty,0); $item_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_poly_qnty,0); $item_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($item_paking_finish_qnty,0); $item_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_paking_finish_qnty,0); $item_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_paking_finish_qnty,0); $item_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($item_ex_fact_qnty,0); $item_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_tot_ex_fact_qnty,0); $item_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($item_bal_ex_fact_qnty,0); $item_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
							}
						}

							if($po_lay_qnty>0 || $po_sewing_in_qnty>0 || $po_sewing_out_qnty >0 || $po_poly_qnty >0 || $po_paking_finish_qnty>0 || $po_ex_fact_qnty>0)
									{
							?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">PO Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($po_order_qnty,0); $po_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($po_lay_qnty,0); $po_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_lay_qnty,0); $po_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_lay_qnty,0); $po_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($po_sewing_in_qnty,0); $po_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_sewing_in_qnty,0); $po_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_sewing_in_qnty,0); $color_bal_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($po_sewing_out_qnty,0); $po_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_sewing_out_qnty,0); $po_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_sewing_out_qnty,0); $po_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($po_poly_qnty,0); $po_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_poly_qnty,0); $po_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_poly_qnty,0); $po_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($po_paking_finish_qnty,0); $po_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_paking_finish_qnty,0); $po_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_paking_finish_qnty,0); $po_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($po_ex_fact_qnty,0); $po_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_tot_ex_fact_qnty,0); $po_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($po_bal_ex_fact_qnty,0); $po_bal_ex_fact_qnty=0;?>
                            </td>
                        </tr>
                        <?
						}
					}
						if($job_lay_qnty>0 || $job_sewing_in_qnty>0 || $job_sewing_out_qnty >0 || $job_poly_qnty >0 || $job_paking_finish_qnty>0 || $job_paking_finish_qnty>0)
									{
						?>
                        <tr bgcolor="#F4F3C4">
                            <td align="right" colspan="9" style="font-weight:bold;">Job Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($job_order_qnty,0); $job_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($job_lay_qnty,0); $job_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_lay_qnty,0); $job_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_lay_qnty,0); $job_bal_lay_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($job_sewing_in_qnty,0); $job_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_sewing_in_qnty,0); $job_tot_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($job_sewing_out_qnty,0); $job_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_sewing_out_qnty,0); $job_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_sewing_out_qnty,0); $job_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($job_poly_qnty,0); $job_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_poly_qnty,0); $job_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_poly_qnty,0); $job_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($job_paking_finish_qnty,0); $job_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_paking_finish_qnty,0); $job_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_paking_finish_qnty,0); $job_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($job_ex_fact_qnty,0); $job_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_tot_ex_fact_qnty,0); $job_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($job_bal_ex_fact_qnty,0); $job_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
					}
				}
					// if($buyer_tot_lay_qnty>0 || $buyer_tot_sewing_in_qnty>0 || $buyer_tot_sewing_out_qnty >0 || $buyer_tot_poly_qnty >0 || $buyer_tot_paking_finish_qnty>0 || $buyer_tot_ex_fact_qnty>0)
					// 				{
					?>
                        <tr bgcolor="#CCCCCC">
                            <td align="right" colspan="9" style="font-weight:bold;">Buyer Total:</td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_order_qnty,0); $buyer_order_qnty=0; ?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($buyer_lay_qnty,0);  $buyer_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_lay_qnty,0); $buyer_tot_lay_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_lay_qnty,0); $buyer_tot_lay_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_sewing_in_qnty,0);  $buyer_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_sewing_in_qnty,0);  $buyer_tot_sewing_in_qnty=0;?>
                            </td>

                            <td width="70" align="right">
                                <? echo number_format($buyer_sewing_out_qnty,0);  $buyer_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_sewing_out_qnty,0); $buyer_tot_sewing_out_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_sewing_out_qnty,0); $buyer_bal_sewing_out_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_poly_qnty,0); $buyer_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_poly_qnty,0); $buyer_tot_poly_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_poly_qnty,0); $buyer_bal_poly_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_paking_finish_qnty,0);  $buyer_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_paking_finish_qnty,0); $buyer_tot_paking_finish_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_paking_finish_qnty,0); $buyer_bal_paking_finish_qnty=0;?>
                            </td>


                            <td width="70" align="right">
                                <? echo number_format($buyer_ex_fact_qnty,0); $buyer_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_tot_ex_fact_qnty,0); $buyer_tot_ex_fact_qnty=0;?>
                            </td>
                            <td width="70" align="right">
                                <? echo number_format($buyer_bal_ex_fact_qnty,0); $buyer_bal_ex_fact_qnty=0;?>
                            </td>

                        </tr>
                        <?
				//}
			}
		        
		        ?>
                    </tbody>


                </table>
            </div>
            <table border="1" class="rpt_table" width="2460" rules="all" style="margin-left: 2px;" align="left" id="">
                <tfoot>
                    <tr>
                        <th style="word-break: break-all;word-wrap: break-word;" width="40" align="center">&nbsp;</th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="60">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
                            <p>&nbsp;</p>
                        </th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
                            <p>&nbsp;</p>
                        </th>


                        <th width="100"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">Grand
                            Total</th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_order_qnty,0);?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_lay_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_lay_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_lay_qnty,0);?>
                        </th>



                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_sewing_in_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_sewing_in_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_sewing_in_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_sewing_out_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_sewing_out_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_sewing_out_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_poly_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_poly_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_poly_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_paking_finish_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_paking_finish_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_paking_finish_qnty,0); ?>
                        </th>

                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_ex_fact_qnty,0);?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_tot_ex_fact_qnty,0); ?>
                        </th>
                        <th width="70" align="right"
                            style="font-weight:bold; font-size:16px;word-break: break-all;word-wrap: break-word;">
                            <? echo number_format($gt_bal_ex_fact_qnty,0); ?>
                        </th>

                    </tr>
                </tfoot>
            </table>
        </fieldset>
        </div>
        </fieldset>
     <?	
	}
    if($type==2)
    {
        $hidden_order_id=str_replace("'","",$hidden_order_id);
        $date_to=str_replace("'","",$txt_date_to);
        $date_from=str_replace("'","",$txt_date_from);
        
        $job_no=str_replace("'","",$txt_job_no);
        $hiden_order_id=str_replace("'","",$hiden_order_id);
        $cbo_work_company_name=str_replace("'","",$cbo_work_company_name);
        $cbo_company_name=str_replace("'","",$cbo_company_name);
        $cbo_year=str_replace("'","",$cbo_year);
        $cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
        //echo $cbo_company_name;die;
    
        $hidden_job_year=str_replace("'","",$hidden_job_year);
        if($cbo_company_name) $company_name_cond=" and b.company_name=$cbo_company_name";//job_no

        if($cbo_work_company_name) $cbo_work_company_name_cond=" and a.serving_company in ($cbo_work_company_name)";
        if($cbo_work_company_name_ex) $cbo_work_company_name__ex_cond=" and f.delivery_company_id in ($cbo_work_company_name_ex)";

    
        if($cbo_year==0)
        {
            $cbo_year=$hidden_job_year;
         }
              
        $job_po_id="";

        if(str_replace("'","",$txt_job_no)!="")
        {
            if($db_type==0)
            {
                $job_po_id=return_field_value("group_concat(b.id) as po_id","wo_po_break_down b","b.job_no_mst in ('UHM-21-00192')","po_id");
                
            }
            else
            {
                $job_id=return_field_value("listagg(cast(job_no as varchar(4000)),',') within group(order by id) as job_no","wo_po_details_master","JOB_NO_PREFIX_NUM in ($txt_job_no) and to_char(insert_date,'YYYY') in ($cbo_year) and company_name=$cbo_company_name","job_no");
                $job_arr=explode(",",$job_id);
                foreach($job_arr as $val){
                    $job_poid=return_field_value("listagg(cast(b.id as varchar(4000)),',') within group(order by id) as po_id","wo_po_break_down b","b.job_no_mst in ('$val')","po_id");
                }               
            }            
        }        
        if($cbo_buyer_name >0){
            $buyer_cond="and b.buyer_name='$cbo_buyer_name'";}else{
            $buyer_cond="";
           ;
        }
    
        if($job_no!=""){
            $job_cond="and b.job_no_prefix_num in ($job_no)";}else{
            $job_cond="";}
        if($job_no!="")
        {
                if($cbo_year!="")
                {		
                    $job_year_cond="and to_char(b.insert_date,'YYYY') in ($cbo_year)";
                    }
                        else
                    {
                        $job_year_cond="";                  			             
                }
            }

        if($date_from=="" && $date_to=="" ){ $date_cond="";}else{
            
         $date_cond=" and a.production_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";

         $prod_po_arr = return_library_array("select po_break_down_id,po_break_down_id from pro_garments_production_mst where status_active=1  and company_id=$cbo_company_name and production_date between '$date_from' and '$date_to'","po_break_down_id","po_break_down_id");
	
			$prod_po_cond = where_con_using_array($prod_po_arr,0,"c.id");
      
        }

        $sql = "SELECT b.id as job_id ,c.id as po_id ,b.job_no,e.id, b.style_ref_no,b.buyer_name ,a.po_break_down_id,c.po_number,e.item_number_id,e.color_number_id,c.pub_shipment_date,c.shiping_status,a.sewing_line ,a.production_date,a.serving_company,e.size_number_id,b.company_name,e.order_quantity,

        sum( case when a.production_type=1 and d.production_type=1 and a.production_date between '$date_from' and '$date_to'then d.production_qnty else 0 end ) as today_cutting,

        sum( case when a.production_type=1 and d.production_type=1  then d.production_qnty else 0 end ) as total_cutting,

    	sum( case when a.production_type=4 and d.production_type=4 and a.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end ) as today_sew_input,

        sum( case when a.production_type=4 and d.production_type=4  then d.production_qnty else 0 end ) as total_sew_input,
    	
    	sum( case when a.production_type=5 and d.production_type=5 and a.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end ) as today_sew_output,

        sum( case when a.production_type=5 and d.production_type=5  then d.production_qnty else 0 end ) as total_sew_out,
    			   	  	
    	sum( case when a.production_type=2 and d.production_type=2  and a.embel_name=3 and a.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end ) as today_wash_issue, 	

        sum( case when a.production_type=2 and d.production_type=2 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_issue,       
       	sum( case when a.production_type=3 and d.production_type=3  and a.embel_name=3 and a.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end ) as today_wash_receive,

        sum( case when a.production_type=3 and d.production_type=3 and a.embel_name=3 then d.production_qnty else 0 end ) as total_wash_receive,
    	    
        sum( case when a.production_type=80 and d.production_type=80  and a.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end ) as today_finish_input,

        sum( case when a.production_type=80 and d.production_type=80  then d.production_qnty else 0 end ) as total_finish_input,

    	sum( case when a.production_type=8 and d.production_type=8   and a.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end ) as today_finish,

        sum( case when a.production_type=8 and d.production_type=8  then d.production_qnty else 0 end ) as total_finish,
    	
    	sum( case when a.production_type=11 and d.production_type=11   and a.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end ) as today_poly
    	,
        sum( case when a.production_type=11 and d.production_type=11  then d.production_qnty else 0 end ) as total_poly	
       FROM wo_po_details_master b, wo_po_break_down c,pro_garments_production_mst a, pro_garments_production_dtls d, wo_po_color_size_breakdown e  WHERE b.id = c.job_id  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 
       $company_name_cond  $cbo_work_company_name_cond   $buyer_cond $job_cond $prod_po_cond $job_year_cond     
       group by b.id,c.id,b.job_no,e.id, b.style_ref_no ,b.buyer_name ,a.po_break_down_id,c.po_number,e.item_number_id,e.color_number_id ,c.pub_shipment_date ,c.shiping_status,a.sewing_line,a.production_date,a.serving_company,e.size_number_id,b.company_name,e.order_quantity order by e.size_number_id";  
     // echo $sql;die;

         $data_array=array();
         $job_id_array=array();
         foreach (sql_select($sql) as $val) 
         {
            $job_id_array[$val['JOB_ID']]=$val['JOB_ID'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['style_ref_no']=$val['STYLE_REF_NO'];
            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['job_no']=$val['JOB_NO'];
            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['po_number']=$val['PO_NUMBER'];
            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['order_quantity']=$val['ORDER_QUANTITY'];
            
            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['pub_shipment_date']=$val['PUB_SHIPMENT_DATE'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_cutting']+=$val['TODAY_CUTTING'];
            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_cutting']+=$val['TOTAL_CUTTING'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_sew_input']+=$val['TODAY_SEW_INPUT'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_sew_input']+=$val['TOTAL_SEW_INPUT'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_sew_output']+=$val['TODAY_SEW_OUTPUT'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_sew_out']+=$val['TOTAL_SEW_OUT'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_wash_issue']+=$val['TODAY_WASH_ISSUE'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_wash_issue']+=$val['TOTAL_WASH_ISSUE'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_wash_receive']+=$val['TODAY_WASH_RECEIVE'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_wash_receive']+=$val['TOTAL_WASH_RECEIVE'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_finish_input']+=$val['TODAY_FINISH_INPUT'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_finish_input']+=$val['TOTAL_FINISH_INPUT'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_finish']+=$val['TODAY_FINISH'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_finish']+=$val['TOTAL_FINISH'];
            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['today_poly']+=$val['TODAY_POLY'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['total_poly']+=$val['TOTAL_POLY'];

            $data_array[$val['BUYER_NAME']][$val['JOB_ID']][$val['PO_BREAK_DOWN_ID']][$val['ITEM_NUMBER_ID']][$val['COLOR_NUMBER_ID']][$val['SIZE_NUMBER_ID']]['shiping_status']=$val['SHIPING_STATUS'];

         }

         $job_cond_arr=where_con_using_array($job_id_array,0,"a.job_id");

		$sql_finish_input=("SELECT a.job_id,a.job_no,a.po_break_down_id,a.item_number_id,a.color_number_id,a.size_number_id,b.emb_name from wo_pre_cos_emb_co_avg_con_dtls a,wo_pre_cost_embe_cost_dtls b where requirment is not null  and requirment>0  and b.id=a.pre_cost_emb_cost_dtls_id and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.status_active=1 $job_cond_arr");
	 //  echo $sql_finish_input;die;
		$finish_wip_arr=array();
		foreach(sql_select($sql_finish_input) as $v)
		{
			$finish_wip_arr[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']][$v['COLOR_NUMBER_ID']][$v['SIZE_NUMBER_ID']]=$v['EMB_NAME'];
		}  
         // echo "<pre>" ;print_r($finish_wip_arr);die;
         
        if($date_from=="" && $date_to=="" ){ $date_cond1="";}else{
            
            $date_cond1=" and a.ex_factory_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
   
            $ex_po_arr = return_library_array("select po_break_down_id,po_break_down_id from pro_ex_factory_mst where status_active=1   and ex_factory_date between '$date_from' and '$date_to'","po_break_down_id","po_break_down_id");

               $ex_po_cond = where_con_using_array($ex_po_arr,0,"c.id");
         
           }
           $ex_factory_sql = "SELECT b.id as job_id ,c.id as po_id ,b.job_no,e.id, b.style_ref_no,b.buyer_name ,a.po_break_down_id,c.po_number,e.item_number_id,e.color_number_id,c.pub_shipment_date,c.shiping_status,a.ex_factory_date,f.delivery_company_id,e.size_number_id,b.company_name,e.order_quantity,d.production_qnty as total_ex_factory,
            sum( case when a.ex_factory_date between '$date_from' and '$date_to'then d.production_qnty else 0 end ) as today_ex_factory
          FROM wo_po_details_master b, wo_po_break_down c,pro_ex_factory_mst a, pro_ex_factory_dtls d, wo_po_color_size_breakdown e,pro_ex_factory_delivery_mst f  WHERE b.id = c.job_id  and c.id = a.po_break_down_id and a.id = d.mst_id   and d.color_size_break_down_id = e.id and a.po_break_down_id=e.po_break_down_id and f.id=a.delivery_mst_id  and f.entry_form!=85 and a.status_active = 1 and a.is_deleted = 0 and d.status_active = 1 and d.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0  and c.status_active = 1 and c.is_deleted = 0 and e.status_active = 1 and e.is_deleted = 0 
          $company_name_cond  $cbo_work_company_name_ex   $buyer_cond $job_cond $ex_po_cond $job_year_cond    group by b.id ,c.id,b.job_no,e.id, b.style_ref_no,b.buyer_name ,a.po_break_down_id,c.po_number,e.item_number_id,e.color_number_id,c.pub_shipment_date,c.shiping_status,a.ex_factory_date,f.delivery_company_id,e.size_number_id,b.company_name,e.order_quantity,d.production_qnty
         order by e.id  ";  
        //echo $ex_factory_sql;die;
      
        foreach(sql_select($ex_factory_sql) as $r)
        {
            $data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_BREAK_DOWN_ID']][$r['ITEM_NUMBER_ID']][$r['COLOR_NUMBER_ID']][$r['SIZE_NUMBER_ID']]['today_ex_factory']+=$r['TODAY_EX_FACTORY'];

            $data_array[$r['BUYER_NAME']][$r['JOB_ID']][$r['PO_BREAK_DOWN_ID']][$r['ITEM_NUMBER_ID']][$r['COLOR_NUMBER_ID']][$r['SIZE_NUMBER_ID']]['total_ex_factory']+=$r['TOTAL_EX_FACTORY'];
        }
        
      //echo "<pre>" ;print_r($data_array);die;
       
           ob_start();
        ?>
       <fieldset style="width:2680px;">
        <div style="width:2680px;">
            <table width="2040" cellspacing="0">
                <tr class="form_caption" style="border:none;">
                    <td colspan="31" align="center" style="border:none;font-size:14px; font-weight:bold">Size Wise GMTS Production Report </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="31" align="center" style="border:none; font-size:16px; font-weight:bold">
                        Company Name:
                        <? 
                                $cbo_company_name_arr=explode(",",$cbo_company_name);
                                $CompanyName="";
                                foreach ($cbo_company_name_arr as $cmp_name)
                                {
                                    $CompanyName.= $company_arr[$cmp_name]; 
                                }
                                echo chop($CompanyName);
                                ?>
                    </td>
                </tr>
                <tr style="border:none;">
                    <td colspan="31" align="center" style="border:none;font-size:12px; font-weight:bold">
                        Date:
                        <? echo $date_from." To ".$date_to ;?>
                    </td>
                </tr>
            </table>
            <br />
            <legend>Report Details Part</legend>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2680" class="rpt_table" align="left">
                <thead>
                    <tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="100" rowspan="2">Buyer</th>
                        
                        <th width="100" rowspan="2">Style Ref</th>
                        <th width="100" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Shipment Date</th>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">Garment Item</th>
                        <th width="100" rowspan="2">Color</th>
                        <th width="100" rowspan="2">Size</th>
                        <th width="70" rowspan="2">Order Qty.</th>
                        <th colspan="3">Cutting Quantity</th>
                        <th colspan="3">Sewing Input</th>
                        <th colspan="3">Sewing Output</th>
                        <th colspan="3">Wash Send</th>
                        <th colspan="3">Wash Rcv</th>
                        <th colspan="3">Finishing Input</th>
                        <th colspan="3">Poly Entry</th>
                        <th colspan="3">Packing & Finishing</th>
                        <th colspan="3">Ex-Factory</th>
                        <th width="100" rowspan="2">Shipment Status</th>

                    </tr>
                    <tr>
                            <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title="Total Cutting - Order Qty">Balance</th>

                        <th width="70">Today </th>
                        <th width="70">Total </th>
                        <th width="70" title="Total Sewing Input - Total Cutting">Balance </th>

                        <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title="Total Sewing Output - Total Sewing Input  ">Balance </th>


                        <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title="Wash Send  - Total Sewing Output ">Balance</th>


                        <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title=" Wash Receive= (Total Wash Receive - Total Wash Sent)">Balance</th>


                        <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title="Finishing Input=(Total Finishing Input - Total Wash Receive)  If Non-wash then Finishing Input=(Total Finishing Input - Total Sewing Output)">Balance</th>


                        <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title="Poly= (Total Poly - Total Finishing Input)">Balance</th>

                        <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title="Packing & Finishing = (Total Packing & Finshing - Total Poly)">Balance</th>
                        
                        <th width="70">Today</th>
                        <th width="70">Total</th>
                        <th width="70" title="Ex-Factory= (Ex-factory - Total Packing & Finishing)">Balance </th>
                        
                    </tr>
                </thead>

                            
                <tbody>
                    <?                            
                        $i=1;
                        $gr_wise_order_qty=0;
                        $gr_wise_today_cutting_qty=0;
                        $gr_wise_total_cutting_qty=0;
                        $gr_wise_balance_qty=0;

                        $gr_wise_today_sewing_input_qty=0;
                        $gr_wise_total_sewing_input_qty=0;
                        $gr_wise_sewing_input_balance_qty=0;

                        $gr_wise_today_sewing_out_qty =0 ;
                        $gr_wise_total_sewing_out_qty=0 ;
                        $gr_wise_sewing_out_balance_qty = 0;
                       
                        $gr_wise_today_wash_send_qty =0 ;
                        $gr_wise_total_wash_send_qty=0 ;
                        $gr_wise_wash_send_balance_qty = 0;

                        $gr_wise_today_wash_rcv_qty =0 ;
                        $gr_wise_total_wash_rcv_qty=0 ;
                        $gr_wise_wash_rcv_balance_qty = 0;

                        $gr_wise_today_fin_in_qty = 0 ; 
                        $gr_wise_total_fin_in_qty = 0 ;
                        $gr_wise_fin_in_balance_qty = 0;

                        $gr_wise_today_poly_qty = 0 ; 
                        $gr_wise_total_poly_qty = 0 ;
                        $gr_wise_poly_balance_qty = 0;

                        $gr_wise_today_packing_qty = 0 ; 
                        $gr_wise_total_packing_qty = 0 ;
                        $gr_wise_Packing_balance_qty = 0;

                        $gr_wise_today_ex_fac_qty = 0 ; 
                        $gr_wise_total_ex_fac_qty = 0 ;
                        $gr_wise_ex_fac_balance_qty = 0;

                        foreach($data_array as $buyer_id=>$buyer_data)
                        {
                                $buyer_wise_order_qty=0;
                                $buyer_wise_today_cutting_qty=0;
                                $buyer_wise_total_cutting_qty=0;
                                $buyer_wise_balance_qty=0;

                                $buyer_wise_today_sewing_input_qty=0;
                                $buyer_wise_total_sewing_input_qty=0;
                                $buyer_wise_sewing_input_balance_qty=0;

                                $buyer_wise_today_sewing_out_qty =0 ;
                                $buyer_wise_total_sewing_out_qty=0 ;
                                $buyer_wise_sewing_out_balance_qty = 0;

                                
                                $buyer_wise_today_wash_send_qty =0 ;
                                $buyer_wise_total_wash_send_qty=0 ;
                                $buyer_wise_wash_send_balance_qty = 0;

                                $buyer_wise_today_wash_rcv_qty =0 ;
                                $buyer_wise_total_wash_rcv_qty=0 ;
                                $buyer_wise_wash_rcv_balance_qty = 0;

                                $buyer_wise_today_fin_in_qty = 0 ; 
                                $buyer_wise_total_fin_in_qty = 0 ;
                                $buyer_wise_fin_in_balance_qty = 0;

                                $buyer_wise_today_poly_qty = 0 ; 
                                $buyer_wise_total_poly_qty = 0 ;
                                $buyer_wise_poly_balance_qty = 0;

                                $buyer_wise_today_packing_qty = 0 ; 
                                $buyer_wise_total_packing_qty = 0 ;
                                $buyer_wise_Packing_balance_qty = 0;

                                $buyer_wise_today_ex_fac_qty = 0 ; 
                                $buyer_wise_total_ex_fac_qty = 0 ;
                                $buyer_wise_ex_fac_balance_qty = 0;
                            foreach($buyer_data as $job_no=>$job_data)
                            {
                                $job_wise_order_qty=0;
                                $job_wise_today_cutting_qty=0;
                                $job_wise_total_cutting_qty=0;
                                $job_wise_balance_qty=0;

                                $job_wise_today_sewing_input_qty=0;
                                $job_wise_total_sewing_input_qty=0;
                                $job_wise_sewing_input_balance_qty=0;

                                $job_wise_today_sewing_out_qty =0 ;
                                $job_wise_total_sewing_out_qty=0 ;
                                $job_wise_sewing_out_balance_qty = 0;

                                
                                $job_wise_today_wash_send_qty =0 ;
                                $job_wise_total_wash_send_qty=0 ;
                                $job_wise_wash_send_balance_qty = 0;

                                $job_wise_today_wash_rcv_qty =0 ;
                                $job_wise_total_wash_rcv_qty=0 ;
                                $job_wise_wash_rcv_balance_qty = 0;

                                $job_wise_today_fin_in_qty = 0 ; 
                                $job_wise_total_fin_in_qty = 0 ;
                                $job_wise_fin_in_balance_qty = 0;

                                $job_wise_today_poly_qty = 0 ; 
                                $job_wise_total_poly_qty = 0 ;
                                $job_wise_poly_balance_qty = 0;

                                $job_wise_today_packing_qty = 0 ; 
                                $job_wise_total_packing_qty = 0 ;
                                $job_wise_Packing_balance_qty = 0;

                                $job_wise_today_ex_fac_qty = 0 ; 
                                $job_wise_total_ex_fac_qty = 0 ;
                                $job_wise_ex_fac_balance_qty = 0;

                                foreach($job_data as $order_id=>$order_data)
                                {
                                    $po_wise_order_qty=0;
                                    $po_wise_today_cutting_qty=0;
                                    $po_wise_total_cutting_qty=0;
                                    $po_wise_balance_qty=0;

                                    $po_wise_today_sewing_input_qty=0;
                                    $po_wise_total_sewing_input_qty=0;
                                    $po_wise_sewing_input_balance_qty=0;

                                    $po_wise_today_sewing_out_qty =0 ;
                                    $po_wise_total_sewing_out_qty=0 ;
                                    $po_wise_sewing_out_balance_qty = 0;

                                    
                                    $po_wise_today_wash_send_qty =0 ;
                                    $po_wise_total_wash_send_qty=0 ;
                                    $po_wise_wash_send_balance_qty = 0;

                                    $po_wise_today_wash_rcv_qty =0 ;
                                    $po_wise_total_wash_rcv_qty=0 ;
                                    $po_wise_wash_rcv_balance_qty = 0;

                                    $po_wise_today_fin_in_qty = 0 ; 
                                    $po_wise_total_fin_in_qty = 0 ;
                                    $po_wise_fin_in_balance_qty = 0;

                                    $po_wise_today_poly_qty = 0 ; 
                                    $po_wise_total_poly_qty = 0 ;
                                    $po_wise_poly_balance_qty = 0;

                                    $po_wise_today_packing_qty = 0 ; 
                                    $po_wise_total_packing_qty = 0 ;
                                    $po_wise_Packing_balance_qty = 0;

                                    $po_wise_today_ex_fac_qty = 0 ; 
                                    $po_wise_total_ex_fac_qty = 0 ;
                                    $po_wise_ex_fac_balance_qty = 0;
                                    foreach($order_data as $item_id=>$item_data)
                                    {   

                                        foreach($item_data as $color_id=>$color_data)
                                        { 
                                            $color_wise_order_qty=0;
                                            $color_wise_today_cutting_qty=0;
                                            $color_wise_total_cutting_qty=0;
                                            $color_wise_balance_qty=0;

                                            $color_wise_today_sewing_input_qty=0;
                                            $color_wise_total_sewing_input_qty=0;
                                            $color_wise_sewing_input_balance_qty=0;

                                            $color_wise_today_sewing_out_qty =0 ;
                                            $color_wise_total_sewing_out_qty=0 ;
                                            $color_wise_sewing_out_balance_qty = 0;

                                            
                                            $color_wise_today_wash_send_qty =0 ;
                                            $color_wise_total_wash_send_qty=0 ;
                                            $color_wise_wash_send_balance_qty = 0;

                                            $color_wise_today_wash_rcv_qty =0 ;
                                            $color_wise_total_wash_rcv_qty=0 ;
                                            $color_wise_wash_rcv_balance_qty = 0;

                                            $color_wise_today_fin_in_qty = 0 ; 
                                            $color_wise_total_fin_in_qty = 0 ;
                                            $color_wise_fin_in_balance_qty = 0;

                                            $color_wise_today_poly_qty = 0 ; 
                                            $color_wise_total_poly_qty = 0 ;
                                            $color_wise_poly_balance_qty = 0;

                                            $color_wise_today_packing_qty = 0 ; 
                                            $color_wise_total_packing_qty = 0 ;
                                            $color_wise_Packing_balance_qty = 0;

                                            $color_wise_today_ex_fac_qty = 0 ; 
                                            $color_wise_total_ex_fac_qty = 0 ;
                                            $color_wise_ex_fac_balance_qty = 0;

                                            foreach($color_data as $size_id=>$value)
                                            {
                                                ?>
                                                
                                                <?
                                                    $cutting_balance = $value['total_cutting'] - $value['order_quantity'];
                                                    $sew_balance= ($value['total_sew_input']-$value['total_cutting']);
                                                    $sew_out_balance= ($value['total_sew_out']- $value['total_sew_input']);
                                                    $wash_send_balance= ($value['total_wash_issue']- $value['total_sew_out']);
                                                    if($finish_wip_arr[$order_id][$item_id][$color_id][$size_id]==3)
                                                    {
                                                    $wash_send_balance=($value['total_wash_issue']- $value['total_sew_out']);		
                                                    }else{
                                                    $wash_send_balance=0;
                                                    }

                                                    $wash_rcv_balance= ($value['total_wash_receive'] - $value['total_wash_issue']);
                                                    if($finish_wip_arr[$order_id][$item_id][$color_id][$size_id]==3)
                                                    {
                                                    $finish_input_blance=($value["total_finish_input"] - $value['total_wash_receive']);		
                                                    }else{
                                                    $finish_input_blance=($value["total_finish_input"] - $value["total_sew_out"]);
                                                    }

                                                    $poly_blance=($value['total_poly'] -$value['total_finish_input']);

                                                    $packibg_blance=($value['total_finish']-$value['total_poly']);
                                                    $ex_factory_blance=(($value['total_ex_factory']) - ($value['total_finish']));
                                                    // echo $ex_factory_blance;die;

                                                if ($i%2==0)$bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
                                            ?>
                                                    <tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_2nd<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i;?>">
                                                        <td width="40" align="left" ><p><?=$i?></p></td>
                                                        <td width="100" align="left" ><p><?= $buyer_short_library[$buyer_id];?></p></td>
                                                        
                                                        <td width="100" align="left" ><p><?=$value['style_ref_no'];?></p></td>
                                                        <td width="100" align="left" ><p><?=$value['job_no'];?></p></td>
                                                        <td width="100" align="left" ><p><?=$value['pub_shipment_date'];?></p></td>
                                                        <td width="100" align="left" ><p><?=$value['po_number'];?></p></td>

                                                        <td width="100" align="left" ><p><?=$garments_item[$item_id];?></p></td>
                                                        <td width="100" align="left" ><p><?=$colorname_arr[$color_id];?></p></td>

                                                        <td width="100" align="left" ><p><?=$sizearr[$size_id];?></p></td>

                                                        <td width="70" align="right" ><p><?=$value['order_quantity'];?></p></td>


                                                        <td width="70" align="right" ><p><?=$value['today_cutting'];?></p></td>

                                                        <td width="70" align="right" ><p><?=$value['total_cutting'];?></p></td>

                                                        <td width="70" align="right"><p><?=$cutting_balance?></p> </td>

                                                        <td width="70" align="right" ><p><?=$value['today_sew_input'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_sew_input'];?></p></td>
                                    
                                                        <td width="70" align="right"><p><?=$sew_balance?></p> </td>

                                                        <td width="70" align="right" ><p><?=$value['today_sew_output'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_sew_out'];?></p></td>


                                                        <td width="70" align="right"><p><?=$sew_out_balance?></p> </td>

                                                        <td width="70" align="right" ><p><?=$value['today_wash_issue'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_wash_issue'];?></p></td>

                                                        <td width="70" align="right"><p><?=$wash_send_balance?></p> </td>


                                                        <td width="70" align="right" ><p><?=$value['today_wash_receive'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_wash_receive'];?></p></td>

                                                        <td width="70" align="right"><p><?=$wash_rcv_balance?></p> </td>


                                                        <td width="70" align="right" ><p><?=$value['today_finish_input'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_finish_input'];?></p></td>

                                                        <td width="70" align="right"><p><?=$finish_input_blance?></p> </td>


                                                        <td width="70" align="right" ><p><?=$value['today_poly'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_poly'];?></p></td>

                                                        <td width="70" align="right"><p><?=$poly_blance?></p></td>

                                                    
                                                        <td width="70" align="right" ><p><?=$value['today_finish'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_finish'];?></p></td>

                                                        <td width="70" align="right"><p><?=$packibg_blance?></p></td>
                                                        
                                                        <td width="70" align="right" ><p><?=$value['today_ex_factory'];?></p></td>
                                                        <td width="70" align="right" ><p><?=$value['total_ex_factory'];?></p></td>

                                                            <td width="70" align="right" ><p><?=$ex_factory_blance;?></p></td>
                                                        
                                                    

                                                        <td width="100"><?=$shipment_status[$value['shiping_status']]?></td>                                                                                                             
                                                    </tr>
                                                <?
                                                    $i++;
                                                    $color_wise_order_qty += $value['order_quantity'];

                                                    $color_wise_today_cutting_qty +=$value['today_cutting'];
                                                
                                                    $color_wise_total_cutting_qty +=$value['total_cutting'];
                                                    $color_wise_balance_qty += $cutting_balance;

                                                    $color_wise_today_sewing_input_qty += $value['today_sew_input'];
                                                    $color_wise_total_sewing_input_qty += $value['total_sew_input'];;
                                                    $color_wise_sewing_input_balance_qty += $sew_balance;

                                                    $color_wise_today_sewing_out_qty += $value['today_sew_output'];
                                                    $color_wise_total_sewing_out_qty += $value['total_sew_out'];
                                                    $color_wise_sewing_out_balance_qty += $sew_out_balance;

                                                    $color_wise_today_wash_send_qty += $value['today_wash_issue'] ;
                                                    $color_wise_total_wash_send_qty +=$value['total_wash_issue'];
                                                    $color_wise_wash_send_balance_qty += $wash_send_balance;

                                                    $color_wise_today_wash_rcv_qty += $value['today_wash_rcv'] ; 
                                                    $color_wise_total_wash_rcv_qty += $value['total_wash_receive'] ;
                                                    $color_wise_wash_rcv_balance_qty += $wash_rcv_balance;


                                                    $color_wise_today_fin_in_qty += $value['today_finish_input'] ; 
                                                    $color_wise_total_fin_in_qty += $value['total_finish_input'] ;
                                                    $color_wise_fin_in_balance_qty += $finish_input_blance;


                                                    
                                                    $color_wise_today_poly_qty += $value['today_poly'] ; 
                                                    $color_wise_total_poly_qty += $value['total_poly'] ;
                                                    $color_wise_poly_balance_qty += $poly_blance;

                                                    $color_wise_today_packing_qty += $value['today_finish'] ; 
                                                    $color_wise_total_packing_qty += $value['total_finish'] ;
                                                    $color_wise_packing_balance_qty += $packibg_blance;

                                                    $color_wise_today_ex_fac_qty += $value['today_ex_factory']  ; 
                                                    $color_wise_total_ex_fac_qty += $value['total_ex_factory'];
                                                    $color_wise_ex_fac_balance_qty += $ex_factory_blance;

                                                        //    ========== po wise =========//
                                                    $po_wise_order_qty += $value['order_quantity'];
                                                    $po_wise_today_cutting_qty +=$value['today_cutting'];
                                                    $po_wise_total_cutting_qty +=$value['total_cutting'];
                                                    $po_wise_balance_qty += $cutting_balance;

                                                    $po_wise_today_sewing_input_qty += $value['today_sew_input'];
                                                    $po_wise_total_sewing_input_qty += $value['total_sew_input'];;
                                                    $po_wise_sewing_input_balance_qty += $sew_balance;

                                                    $po_wise_today_sewing_out_qty += $value['today_sew_output'];
                                                    $po_wise_total_sewing_out_qty += $value['total_sew_out'];
                                                    $po_wise_sewing_out_balance_qty += $sew_out_balance;

                                                    $po_wise_today_wash_send_qty += $value['today_wash_issue'] ;
                                                    $po_wise_total_wash_send_qty +=$value['total_wash_issue'];
                                                    $po_wise_wash_send_balance_qty += $wash_send_balance;

                                                    $po_wise_today_wash_rcv_qty += $value['today_wash_rcv'] ; 
                                                    $po_wise_total_wash_rcv_qty += $value['total_wash_receive'] ;
                                                    $po_wise_wash_rcv_balance_qty += $wash_rcv_balance;


                                                    $po_wise_today_fin_in_qty += $value['today_finish_input'] ; 
                                                    $po_wise_total_fin_in_qty += $value['total_finish_input'] ;
                                                    $po_wise_fin_in_balance_qty += $finish_input_blance;


                                                    
                                                    $po_wise_today_poly_qty += $value['today_poly'] ; 
                                                    $po_wise_total_poly_qty += $value['total_poly'] ;
                                                    $po_wise_poly_balance_qty += $poly_blance;

                                                    $po_wise_today_packing_qty += $value['today_finish'] ; 
                                                    $po_wise_total_packing_qty += $value['total_finish'] ;
                                                    $po_wise_packing_balance_qty += $packibg_blance;

                                                    $po_wise_today_ex_fac_qty += $value['today_ex_factory']  ; 
                                                    $po_wise_total_ex_fac_qty += $value['total_ex_factory'];
                                                    $po_wise_ex_fac_balance_qty += $ex_factory_blance;

                                                        //    ========== job wise =========//
                                                        $job_wise_order_qty += $value['order_quantity'];
                                                        $job_wise_today_cutting_qty +=$value['today_cutting'];
                                                        $job_wise_total_cutting_qty +=$value['total_cutting'];
                                                        $job_wise_balance_qty += $cutting_balance;

                                                        $job_wise_today_sewing_input_qty += $value['today_sew_input'];
                                                        $job_wise_total_sewing_input_qty += $value['total_sew_input'];;
                                                        $job_wise_sewing_input_balance_qty += $sew_balance;

                                                        $job_wise_today_sewing_out_qty += $value['today_sew_output'];
                                                        $job_wise_total_sewing_out_qty += $value['total_sew_out'];
                                                        $job_wise_sewing_out_balance_qty += $sew_out_balance;

                                                        $job_wise_today_wash_send_qty += $value['today_wash_issue'] ;
                                                        $job_wise_total_wash_send_qty +=$value['total_wash_issue'];
                                                        $job_wise_wash_send_balance_qty += $wash_send_balance;

                                                        $job_wise_today_wash_rcv_qty += $value['today_wash_rcv'] ; 
                                                        $job_wise_total_wash_rcv_qty += $value['total_wash_receive'] ;
                                                        $job_wise_wash_rcv_balance_qty += $wash_rcv_balance;


                                                        $job_wise_today_fin_in_qty += $value['today_finish_input'] ; 
                                                        $job_wise_total_fin_in_qty += $value['total_finish_input'] ;
                                                        $job_wise_fin_in_balance_qty += $finish_input_blance;
                                                     
                                                      
                                                        $job_wise_today_poly_qty += $value['today_poly'] ; 
                                                        $job_wise_total_poly_qty += $value['total_poly'] ;
                                                        $job_wise_poly_balance_qty += $poly_blance;

                                                        $job_wise_today_packing_qty += $value['today_finish'] ; 
                                                        $job_wise_total_packing_qty += $value['total_finish'] ;
                                                        $job_wise_packing_balance_qty += $packibg_blance;

                                                        $job_wise_today_ex_fac_qty += $value['today_ex_factory']  ; 
                                                        $job_wise_total_ex_fac_qty += $value['total_ex_factory'];
                                                        $job_wise_ex_fac_balance_qty += $ex_factory_blance;

                                                        //    ========== buyer wise =========//
                                                        $buyer_wise_order_qty += $value['order_quantity'];
                                                        $buyer_wise_today_cutting_qty +=$value['today_cutting'];
                                                        $buyer_wise_total_cutting_qty +=$value['total_cutting'];
                                                        $buyer_wise_balance_qty += $cutting_balance;

                                                        $buyer_wise_today_sewing_input_qty += $value['today_sew_input'];
                                                        $buyer_wise_total_sewing_input_qty += $value['total_sew_input'];;
                                                        $buyer_wise_sewing_input_balance_qty += $sew_balance;

                                                        $buyer_wise_today_sewing_out_qty += $value['today_sew_output'];
                                                        $buyer_wise_total_sewing_out_qty += $value['total_sew_out'];
                                                        $buyer_wise_sewing_out_balance_qty += $sew_out_balance;

                                                        $buyer_wise_today_wash_send_qty += $value['today_wash_issue'] ;
                                                        $buyer_wise_total_wash_send_qty +=$value['total_wash_issue'];
                                                        $buyer_wise_wash_send_balance_qty += $wash_send_balance;

                                                        $buyer_wise_today_wash_rcv_qty += $value['today_wash_rcv'] ; 
                                                        $buyer_wise_total_wash_rcv_qty += $value['total_wash_receive'] ;
                                                        $buyer_wise_wash_rcv_balance_qty += $wash_rcv_balance;


                                                        $buyer_wise_today_fin_in_qty += $value['today_finish_input'] ; 
                                                        $buyer_wise_total_fin_in_qty += $value['total_finish_input'] ;
                                                        $buyer_wise_fin_in_balance_qty += $finish_input_blance;

                                                      
                                                        $buyer_wise_today_poly_qty += $value['today_poly'] ; 
                                                        $buyer_wise_total_poly_qty += $value['total_poly'] ;
                                                        $buyer_wise_poly_balance_qty += $poly_blance;

                                                        $buyer_wise_today_packing_qty += $value['today_finish'] ; 
                                                        $buyer_wise_total_packing_qty += $value['total_finish'] ;
                                                        $buyer_wise_packing_balance_qty += $packibg_blance;

                                                        $buyer_wise_today_ex_fac_qty += $value['today_ex_factory']  ; 
                                                        $buyer_wise_total_ex_fac_qty += $value['total_ex_factory'];
                                                        $buyer_wise_ex_fac_balance_qty += $ex_factory_blance;


                                                        //    ========== Grand Total =========//
                                                        $gr_wise_order_qty += $value['order_quantity'];
                                                        $gr_wise_today_cutting_qty +=$value['today_cutting'];
                                                        $gr_wise_total_cutting_qty +=$value['total_cutting'];
                                                        $gr_wise_balance_qty += $cutting_balance;

                                                        $gr_wise_today_sewing_input_qty += $value['today_sew_input'];
                                                        $gr_wise_total_sewing_input_qty += $value['total_sew_input'];;
                                                        $gr_wise_sewing_input_balance_qty += $sew_balance;

                                                        $gr_wise_today_sewing_out_qty += $value['today_sew_output'];
                                                        $gr_wise_total_sewing_out_qty += $value['total_sew_out'];
                                                        $gr_wise_sewing_out_balance_qty += $sew_out_balance;

                                                        $gr_wise_today_wash_send_qty += $value['today_wash_issue'] ;
                                                        $gr_wise_total_wash_send_qty +=$value['total_wash_issue'];
                                                        $gr_wise_wash_send_balance_qty += $wash_send_balance;

                                                        $gr_wise_today_wash_rcv_qty += $value['today_wash_rcv'] ; 
                                                        $gr_wise_total_wash_rcv_qty += $value['total_wash_receive'] ;
                                                        $gr_wise_wash_rcv_balance_qty += $wash_rcv_balance;


                                                        $gr_wise_today_fin_in_qty += $value['today_finish_input'] ; 
                                                        $gr_wise_total_fin_in_qty += $value['total_finish_input'] ;
                                                        $gr_wise_fin_in_balance_qty += $finish_input_blance;

                                                        $gr_wise_today_poly_qty += $value['today_poly'] ; 
                                                        $gr_wise_total_poly_qty += $value['total_poly'] ;
                                                        $gr_wise_poly_balance_qty += $poly_blance;

                                                        $gr_wise_today_packing_qty += $value['today_finish'] ; 
                                                        $gr_wise_total_packing_qty += $value['total_finish'] ;
                                                        $gr_wise_packing_balance_qty += $packibg_blance;

                                                        $gr_wise_today_ex_fac_qty += $value['today_ex_factory']  ; 
                                                        $gr_wise_total_ex_fac_qty += $value['total_ex_factory'];
                                                        $gr_wise_ex_fac_balance_qty += $ex_factory_blance;
                                            }  
                                                ?>
                                                <tr bgcolor="#dccdcd">							
                                                    <td colspan="9" align="right"><b>Color  Total</b></td>
                                                    <td align="right"><?=$color_wise_order_qty;?></td>
                                                    <td align="right"><?=$color_wise_today_cutting_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_cutting_qty;?></td>
                                                    <td align="right"><?=$color_wise_balance_qty;?></td>

                                                    <td align="right"><?=$color_wise_today_sewing_input_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_sewing_input_qty;;?></td>
                                                    <td align="right"><?=$color_wise_sewing_input_balance_qty?></td>

                                                    <td align="right"><?=$color_wise_today_sewing_out_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_sewing_out_qty;;?></td>
                                                    <td align="right"><?=$color_wise_sewing_out_balance_qty?></td>

                                                    <td align="right"><?=$color_wise_today_wash_send_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_wash_send_qty;?></td>
                                                    <td align="right"><?=$color_wise_wash_send_balance_qty?></td>

                                                    <td align="right"><?=$color_wise_today_wash_rcv_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_wash_rcv_qty;?></td>
                                                    <td align="right"><?=$color_wise_wash_rcv_balance_qty;?></td>
                                                    
                                                    <td align="right"><?=$color_wise_today_fin_in_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_fin_in_qty;?></td>
                                                    <td align="right"><?=$color_wise_fin_in_balance_qty;?></td>

                                                    <td align="right"><?=$color_wise_today_poly_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_poly_qty;?></td>
                                                    <td align="right"><?=$color_wise_poly_balance_qty;?></td>

                                                    <td align="right"><?=$color_wise_today_packing_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_packing_qty;?></td>
                                                    <td align="right"><?=$color_wise_packing_balance_qty;?></td>

                                                    <td align="right"><?=$color_wise_today_ex_fac_qty;?></td>
                                                    <td align="right"><?=$color_wise_total_ex_fac_qty;?></td>
                                                    <td align="right"><?=$color_wise_ex_fac_balance_qty;?></td>                                                
                                                </tr>
                                                <?
                                        }
                                        
                                    }  
                                        ?>
                                            <tr bgcolor="#cddcdc">							
                                                <td colspan="9" align="right"><b>PO Total</b></td>
                                                <td align="right"><?=$po_wise_order_qty;?></td>
                                                <td align="right"><?=$po_wise_today_cutting_qty;?></td>
                                                <td align="right"><?=$po_wise_total_cutting_qty;?></td>
                                                <td align="right"><?=$po_wise_balance_qty;?></td>

                                                <td align="right"><?=$po_wise_today_sewing_input_qty;?></td>
                                                <td align="right"><?=$po_wise_total_sewing_input_qty;;?></td>
                                                <td align="right"><?=$po_wise_sewing_input_balance_qty?></td>

                                                <td align="right"><?=$po_wise_today_sewing_out_qty;?></td>
                                                <td align="right"><?=$po_wise_total_sewing_out_qty;;?></td>
                                                <td align="right"><?=$po_wise_sewing_out_balance_qty?></td>

                                                <td align="right"><?=$po_wise_today_wash_send_qty;?></td>
                                                <td align="right"><?=$po_wise_total_wash_send_qty;?></td>
                                                <td align="right"><?=$po_wise_wash_send_balance_qty?></td>

                                                <td align="right"><?=$po_wise_today_wash_rcv_qty;?></td>
                                                <td align="right"><?=$po_wise_total_wash_rcv_qty;?></td>
                                                <td align="right"><?=$po_wise_wash_rcv_balance_qty;?></td>
                                            
                                                <td align="right"><?=$po_wise_today_fin_in_qty;?></td>
                                                <td align="right"><?=$po_wise_total_fin_in_qty;?></td>
                                                <td align="right"><?=$po_wise_fin_in_balance_qty;?></td>

                                                <td align="right"><?=$po_wise_today_poly_qty;?></td>
                                                <td align="right"><?=$po_wise_total_poly_qty;?></td>
                                                <td align="right"><?=$po_wise_poly_balance_qty;?></td>

                                                <td align="right"><?=$po_wise_today_packing_qty;?></td>
                                                <td align="right"><?=$po_wise_total_packing_qty;?></td>
                                                <td align="right"><?=$po_wise_packing_balance_qty;?></td>

                                                <td align="right"><?=$po_wise_today_ex_fac_qty;?></td>
                                                <td align="right"><?=$po_wise_total_ex_fac_qty;?></td>
                                                <td align="right"><?=$po_wise_ex_fac_balance_qty;?></td>
                                            
                                            </tr>
                                        <? 
                                } 
                                ?>
                                    <tr bgcolor="#FAF0E6">							
                                        <td colspan="9" align="right"><b>Job Total</b></td>
                                        <td align="right"><?=$job_wise_order_qty;?></td>
                                        <td align="right"><?=$job_wise_today_cutting_qty;?></td>
                                        <td align="right"><?=$job_wise_total_cutting_qty;?></td>
                                        <td align="right"><?=$job_wise_balance_qty;?></td>

                                        <td align="right"><?=$job_wise_today_sewing_input_qty;?></td>
                                        <td align="right"><?=$job_wise_total_sewing_input_qty;;?></td>
                                        <td align="right"><?=$job_wise_sewing_input_balance_qty?></td>

                                        <td align="right"><?=$job_wise_today_sewing_out_qty;?></td>
                                        <td align="right"><?=$job_wise_total_sewing_out_qty;;?></td>
                                        <td align="right"><?=$job_wise_sewing_out_balance_qty?></td>

                                        <td align="right"><?=$job_wise_today_wash_send_qty;?></td>
                                        <td align="right"><?=$job_wise_total_wash_send_qty;?></td>
                                        <td align="right"><?=$job_wise_wash_send_balance_qty?></td>

                                        <td align="right"><?=$job_wise_today_wash_rcv_qty;?></td>
                                        <td align="right"><?=$job_wise_total_wash_rcv_qty;?></td>
                                        <td align="right"><?=$job_wise_wash_rcv_balance_qty;?></td>
                                    
                                        <td align="right"><?=$job_wise_today_fin_in_qty;?></td>
                                        <td align="right"><?=$job_wise_total_fin_in_qty;?></td>
                                        <td align="right"><?=$job_wise_fin_in_balance_qty;?></td>

                                        <td align="right"><?=$job_wise_today_poly_qty;?></td>
                                        <td align="right"><?=$job_wise_total_poly_qty;?></td>
                                        <td align="right"><?=$job_wise_poly_balance_qty;?></td>

                                        <td align="right"><?=$job_wise_today_packing_qty;?></td>
                                        <td align="right"><?=$job_wise_total_packing_qty;?></td>
                                        <td align="right"><?=$job_wise_packing_balance_qty;?></td>

                                        <td align="right"><?=$job_wise_today_ex_fac_qty;?></td>
                                        <td align="right"><?=$job_wise_total_ex_fac_qty;?></td>
                                        <td align="right"><?=$job_wise_ex_fac_balance_qty;?></td>
                                    </tr>
                                <? 
                            }
                            ?>
                                <tr bgcolor="#D3D3D3">							
                                    <td colspan="9" align="right"><b> Buyer Total</b></td>
                                    <td align="right"><?=$buyer_wise_order_qty;?></td>
                                    <td align="right"><?=$buyer_wise_today_cutting_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_cutting_qty;?></td>
                                    <td align="right"><?=$buyer_wise_balance_qty;?></td>

                                    <td align="right"><?=$buyer_wise_today_sewing_input_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_sewing_input_qty;;?></td>
                                    <td align="right"><?=$buyer_wise_sewing_input_balance_qty?></td>

                                    <td align="right"><?=$buyer_wise_today_sewing_out_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_sewing_out_qty;;?></td>
                                    <td align="right"><?=$buyer_wise_sewing_out_balance_qty?></td>

                                    <td align="right"><?=$buyer_wise_today_wash_send_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_wash_send_qty;?></td>
                                    <td align="right"><?=$buyer_wise_wash_send_balance_qty?></td>

                                    <td align="right"><?=$buyer_wise_today_wash_rcv_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_wash_rcv_qty;?></td>
                                    <td align="right"><?=$buyer_wise_wash_rcv_balance_qty;?></td>
                                
                                    <td align="right"><?=$buyer_wise_today_fin_in_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_fin_in_qty;?></td>
                                    <td align="right"><?=$buyer_wise_fin_in_balance_qty;?></td>

                                    <td align="right"><?=$buyer_wise_today_poly_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_poly_qty;?></td>
                                    <td align="right"><?=$buyer_wise_poly_balance_qty;?></td>

                                    <td align="right"><?=$buyer_wise_today_packing_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_packing_qty;?></td>
                                    <td align="right"><?=$buyer_wise_packing_balance_qty;?></td>

                                    <td align="right"><?=$buyer_wise_today_ex_fac_qty;?></td>
                                    <td align="right"><?=$buyer_wise_total_ex_fac_qty;?></td>
                                    <td align="right"><?=$buyer_wise_ex_fac_balance_qty;?></td>
                                </tr>
                            <? 
                        }                               
                    ?>  
                </tbody>
                <tfoot>                         
                    <tr>							
                        <td colspan="9" align="right"><b>Grand Total</b></td>
                        <td align="right"><?=$gr_wise_order_qty;?></td>
                        <td align="right"><?=$gr_wise_today_cutting_qty;?></td>
                        <td align="right"><?=$gr_wise_total_cutting_qty;?></td>
                        <td align="right"><?=$gr_wise_balance_qty;?></td>

                        <td align="right"><?=$gr_wise_today_sewing_input_qty;?></td>
                        <td align="right"><?=$gr_wise_total_sewing_input_qty;;?></td>
                        <td align="right"><?=$gr_wise_sewing_input_balance_qty?></td>

                        <td align="right"><?=$gr_wise_today_sewing_out_qty;?></td>
                        <td align="right"><?=$gr_wise_total_sewing_out_qty;;?></td>
                        <td align="right"><?=$gr_wise_sewing_out_balance_qty?></td>

                        <td align="right"><?=$gr_wise_today_wash_send_qty;?></td>
                        <td align="right"><?=$gr_wise_total_wash_send_qty;?></td>
                        <td align="right"><?=$gr_wise_wash_send_balance_qty?></td>

                        <td align="right"><?=$gr_wise_today_wash_rcv_qty;?></td>
                        <td align="right"><?=$gr_wise_total_wash_rcv_qty;?></td>
                        <td align="right"><?=$gr_wise_wash_rcv_balance_qty;?></td>
                    
                        <td align="right"><?=$gr_wise_today_fin_in_qty;?></td>
                        <td align="right"><?=$gr_wise_total_fin_in_qty;?></td>
                        <td align="right"><?=$gr_wise_fin_in_balance_qty;?></td>

                        <td align="right"><?=$gr_wise_today_poly_qty;?></td>
                        <td align="right"><?=$gr_wise_total_poly_qty;?></td>
                        <td align="right"><?=$gr_wise_poly_balance_qty;?></td>

                        <td align="right"><?=$gr_wise_today_packing_qty;?></td>
                        <td align="right"><?=$gr_wise_total_packing_qty;?></td>
                        <td align="right"><?=$gr_wise_packing_balance_qty;?></td>

                        <td align="right"><?=$gr_wise_today_ex_fac_qty;?></td>
                        <td align="right"><?=$gr_wise_total_ex_fac_qty;?></td>
                        <td align="right"><?=$gr_wise_ex_fac_balance_qty;?></td>
                    </tr>
                </tfoot>
            </table>        
        </div>                                             
       </fieldset>
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
	$create_new_doc = fopen($filename, 'w') or die('can not open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('can not write');
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}






if($action=="openJobNoPopup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
<script>
var selected_id = new Array;
var selected_name = new Array;
var selected_no = new Array;
var selected_year = new Array;

function check_all_data() {
    var tbl_row_count = document.getElementById('list_view').rows.length;
    tbl_row_count = tbl_row_count - 0;
    for (var i = 1; i <= tbl_row_count; i++) {
        if ($('#tr_' + i).is(':visible')) {
            var onclickString = $('#tr_' + i).attr('onclick');
            var paramArr = onclickString.split("'");
            var functionParam = paramArr[1];
            js_set_value(functionParam);
        }

    }
}

function toggle(x, origColor) {
    var newColor = 'yellow';
    if (x.style) {
        x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
    }
}

function js_set_value(strCon) {
    //alert(strCon);
    var splitSTR = strCon.split("_");
    var str_or = splitSTR[0];
    var selectID = splitSTR[1];
    var selectDESC = splitSTR[2];
    var selectYear = splitSTR[3];
    //$('#txt_individual_id' + str).val(splitSTR[1]);
    //$('#txt_individual' + str).val('"'+splitSTR[2]+'"');

    toggle(document.getElementById('tr_' + str_or), '#FFFFCC');

    if (jQuery.inArray(selectID, selected_id) == -1) {
        selected_id.push(selectID);
        selected_name.push(selectDESC);
        selected_year.push(selectYear);
        selected_no.push(str_or);
    } else {
        for (var i = 0; i < selected_id.length; i++) {
            if (selected_id[i] == selectID) break;
        }
        selected_id.splice(i, 1);
        selected_name.splice(i, 1);
        selected_year.splice(i, 1);
        selected_no.splice(i, 1);
    }
    var id = '';
    var name = '';
    var job = '';
    var year = '';
    var num = '';
    for (var i = 0; i < selected_id.length; i++) {
        id += selected_id[i] + ',';
        name += selected_name[i] + ',';
        year += selected_year[i] + ',';
        num += selected_no[i] + ',';
    }
    id = id.substr(0, id.length - 1);
    name = name.substr(0, name.length - 1);
    year = year.substr(0, year.length - 1);
    num = num.substr(0, num.length - 1);
    //alert(year);
    $('#txt_selected_po').val(id);
    $('#txt_selected_job').val(name);
    $('#txt_selected_year').val(year);
    $('#txt_selected_no').val(num);
}
</script>
<?
	$buyer=str_replace("'","",$buyer);
	$w_company=str_replace("'","",$w_company);
	//echo $w_company;
	$lc_company=str_replace("'","",$lc_company);
	$job_year=str_replace("'","",$job_year);

	
	if($lc_company!=0) $lc_company_cond=" and b.company_name=$lc_company"; else $lc_company_cond="";
	if($buyer!=0) $buyer_cond="and b.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(b.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(b.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(b.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(b.insert_date,'YYYY')";
	}
	if($txt_style_ref_id!="") $style_cond=" and b.id in($txt_style_ref_id)"; else $style_cond="";
	 $sql = "SELECT a.id,a.po_number,a.job_no_mst,b.company_name,buyer_name,b.style_ref_no,b.job_no_prefix_num,$select_date as year from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id $lc_company_cond   $job_year_cond   and a.status_active in(1,2,3) and b.status_active=1"; 
     	//echo $sql; die;
    $buyer_arr=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
    $arr=array(2=>$buyer_arr);
	// $sql ="SELECT id,job_no,style_ref_no,job_no_prefix_num, to_char(insert_date,'YYYY') as year,buyer_name from  wo_po_details_master 
	//  where  status_active=1  $job_year_cond  $buyer_cond ";

	echo create_list_view("list_view", "Year,Job No,Buyer Name,Style Ref No,Order No","70,100,80,80,150,80","520","360",0, $sql , "js_set_value", "id,job_no_prefix_num,year", "", 1, "0,0,buyer_name,0", $arr, "year,job_no_prefix_num,buyer_name,style_ref_no,po_number", "","setFilterGrid('list_view',-1)","0","",1) ;	

	echo "<input type='hidden' id='txt_selected_po' />";
	echo "<input type='hidden' id='txt_selected_job' />";
	echo "<input type='hidden' id='txt_selected_no' />";
    echo "<input type='hidden' id='txt_selected_year' />";
	exit();
}
?>

