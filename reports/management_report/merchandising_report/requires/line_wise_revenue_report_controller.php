<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.washes.php');
require_once('../../../../includes/class4/class.others.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$date=date('Y-m-d');

$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
if ($action=="load_drop_down_location")
{
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name";
	echo create_drop_down( "cbo_location", 110, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_revenue_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/line_wise_revenue_report_controller',document.getElementById('cbo_floor').value+'_'+this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_line', 'line_td' );get_php_form_data( this.value, 'eval_multi_select', 'requires/line_wise_revenue_report_controller' );get_php_form_data( this.value, 'eval_multi_select2', 'requires/line_wise_revenue_report_controller' );",0 );     	 
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_revenue_report_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('cbo_company_name').value, 'load_drop_down_line', 'line_td' );",0 ); 
	
	//echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/line_wise_revenue_report_controller', this.value, 'load_drop_down_line', 'line_td' );",0 ); 
	exit();    	 
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	//echo $explode_data[2].'DDSSSx ';
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
		 if($db_type==0)	$data_format="and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."'";
		 if($db_type==2)	$data_format="and b.pr_date='".change_date_format($txt_date,'','',1)."'";
	
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		echo create_drop_down( "cbo_line", 110,$line_array,"", 1, "-- Select --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "",0,0 );
	}
	exit();
}
if ($action == "eval_multi_select") {
    echo "set_multiselect('cbo_floor','0','0*0','','0');\n";
    exit();
}
if ($action == "eval_multi_select2") {
    echo "set_multiselect('cbo_line','0','0','','0');\n";
    exit();
}

  
if($action=="report_generate_line_wise") //Monthly Ratanpur Kal of RMG
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	 $lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name");
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_floor 			= str_replace("'","",$cbo_floor);
	$cbo_location 	= str_replace("'","",$cbo_location);
	$cbo_line 	= str_replace("'","",$cbo_line);
	if($cbo_location>0) $location_cond="and a.location in($cbo_location)";else $location_cond="";
	if($cbo_location>0) $location_cond2="and a.location_id in($cbo_location)";else $location_cond2="";
	if($cbo_location>0) $location_cond3="and b.location_id in($cbo_location)";else $location_cond3="";
	if($cbo_floor>0) $floor_cond="and a.floor_id in($cbo_floor)";else $floor_cond="";
	if($cbo_line>0) $line_cond="and c.id in($cbo_line)";else $line_cond="";
	//if($cbo_line>0) $line_cond="and a.sewing_line in($cbo_line)";else $line_cond="";
	if($cbo_line>0) $line_cond2="and a.sewing_line in($cbo_line)";else $line_cond2="";
	$from_year 		= str_replace("'","",$cbo_from_year);
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$from_year);
	//$exlastYear 	= explode('-',$to_year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
//	echo $firstYear.'='.$lastYear;
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$startDate =''; 
	$endDate ="";
	for($firstYear; $firstYear <= $lastYear; $firstYear++)
	{
		for($k=1; $k <= $j; $k++)
		{
			if($i==1 && $k>6)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
			else if ($i!=1 && $k<7)
			{
				$fiscal_month=date("Y-m",strtotime(($firstYear.'-'.$k)));
				$fiscal_month=date("M-Y",strtotime($fiscal_month));
				$fiscalMonth_arr[strtoupper($fiscal_month)]=strtoupper($fiscal_month);
			}
		}
		$i++;
	}
	// echo "<pre>";print_r($fiscalMonth_arr);die();
	$startDate=date("d-M-Y",strtotime(($exfirstYear[0].'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	/*
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null $location_cond3  order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
		
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			$lineArr[$val[csf('id')]]=$val[csf('line_name')];
			if($val[csf('floor_name')]==13)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==14)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==16)
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==17)
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
		}*/
		
	//print_r($floor_group_ff_arr);
	//echo $prod_reso_allocation.'d';;
	if($prod_reso_allocation==1)
	{
	    $sql_fin_prod=" SELECT c.id as mst_id,c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and    '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id  and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)  and a.floor_id is not null and a.floor_id <> 0 $location_cond $floor_cond $line_cond order by c.line_number asc";
	}
	else
	{
		 $sql_fin_prod=" SELECT a.sewing_line as mst_id,a.sewing_line as line_number,a.item_number_id as item_id,a.floor_id,a.po_break_down_id,to_char(a.production_date,'MON-YYYY') as month_year,(CASE WHEN a.production_date between '$startDate' and '$endDate' and a.production_type=5 THEN b.production_qnty END) AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)   and a.floor_id is not null and a.floor_id <> 0  $location_cond $floor_cond $line_cond2  order by a.sewing_line asc";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
		$line_wise_arr[$val[csf('line_number')]] = $val[csf('line_number')];
	}
	asort($line_wise_arr);

	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
//	$cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
 $cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);

	
	
	if($prod_reso_allocation==1)
	{
	     $sql_sub_sewOut=" SELECT c.id as mst_id ,c.line_number,a.order_id,a.floor_id ,to_char(a.production_date,'MON-YYYY') as month_year,a.production_qnty from subcon_gmts_prod_dtls a ,prod_resource_mst c where c.id=a.line_id and a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2   $location_cond2 $line_cond $floor_cond  order by a.floor_id";
	}
	else 
	{
		$sql_sub_sewOut=" SELECT a.line_id as mst_id,a.line_id as line_number,a.order_id,a.floor_id ,to_char(a.production_date,'MON-YYYY') as month_year,a.production_qnty from subcon_gmts_prod_dtls a  where  a.company_id in($cbo_company_id) and a.production_date between '$startDate' and '$endDate' and a.production_type=2  and a.status_active=1 $location_cond2 $floor_cond  $line_cond2  order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	$year_floor_array=array();$year_floor_array2=array();$year_floor_array3=array();$year_floor_array4=array();
	foreach ($sql_fin_prod_res as $val) 
	{	
		
			$fyear=$val[csf("month_year")];
			$sew_out=$val[csf("sew_out")];
			$main_array[$fyear]['qty']+=$sew_out;//
			$main_array[$fyear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			
		//	echo $sew_smv.'='.$sew_smv.'X';
		 $ff_floor_id=$val[csf('floor_id')];
		 $sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		 $cm_cost_based_on_date="";
		if($cm_cost_method_based_on==1){ 
		$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
		if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==2){
		$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
		$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
		$min_shipment_date=min($min_shipment_dateArr);
		if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==3){
		$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
		$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
		$max_shipment_date=max($max_shipment_dateArr);
		if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==4){
		$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
		$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
		$min_pub_shipment_date=min($pub_shipment_date);
		//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
		if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
		//echo $cm_cost_based_on_date.'CPm,';
		}
		else if($cm_cost_method_based_on==5){
		$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
		$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
		$max_pub_shipment_date=max($pub_shipment_date);
		if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
		if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
		$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
		if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
		
		 $line_number=array_unique(explode(",",$val[csf('line_number')]));
			if($sew_smv>0)
			{
				//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
				$finish_cost=($sew_smv*$sew_out*$cost_per_minute)/$exch_rate;
				//echo $finish_cost.'='.$sew_out.'='.$cost_per_minute.'<br>';
				if($finish_cost>0)
				{
				$year_floor_array[$fyear][$val[csf('line_number')]]['finishing'] += $finish_cost;
				$year_floor_array[$fyear][$val[csf('line_number')]]['mst_id'] .= $val[csf('mst_id')].',';
				}
				
			}
		
	}
	 //print_r($year_floor_array);
	//SubCon Sewing Out
	$subCon_year_floor_sewOut_array=array();$subCon_year_floor_sewOut_array2=array();
	$subCon_year_floor_sewOut_array3=array();
	$subCon_year_floor_sewOut_array4=array();

	foreach ($sql_sub_sewOut_result as $val) 
	{			
		
			
			$myear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$sub_ff_floor_id=$val[csf('floor_id')];
			 $line_number=array_unique(explode(",",$val[csf('line_number')]));
			
					if($subsewOut_cost>0)
					{
					$subSewOut_costUSD = $subsewOut_cost/$rate;	
					
					$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
					$main_array[$fyear]['subSew'] += $subSewOut_costUSD;
					//foreach( $line_number as $lineId)
					 // {	
					// echo $val[csf("mst_id")].'d,';
					$subCon_year_floor_sewOut_array[$myear][$val[csf('line_number')]]['subSew'] += $subSewOut_costUSD;
					$subCon_year_floor_sewOut_array[$myear][$val[csf('line_number')]]['mst_id'] .= $val[csf("mst_id")].',';
					  //}
					}
			
	}
	//print_r($subCon_year_floor_sewOut_array);
	unset($sql_fin_prod_res);
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$line_width=count($fiscalMonth_arr)*70;
	$tbl_width = 200+$line_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px"><? echo $report_title;?></strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'monthly_revenue_report', '')"> -<b>Month wise revenue <? //echo $year; ?></b></h3>
	    <div id="monthly_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	             <th width="110">Line No</th>
                 <? 
			    foreach ($fiscalMonth_arr as $mon_id => $mon)
		        {
	            	?>
	            	<th width="70" title="Date=<? echo $mon_id;?>"><? echo date('F-y',strtotime($mon_id));?></th>
	            	<?
	            }
	            ?>
	        <th width="70">Total</th>
	        </thead>
		        <tbody>   
		        <?
				
					$total_ashulia_rmg=$total_knit_asia=0;
					$i=1;
		        	foreach ($line_wise_arr as $lineId => $val) 
		        	{
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 $line_arr=explode(",",$lineId);
							$line_name="";
							foreach($line_arr as $line_id)
							{
								$line_name .= ($line_name == "") ? $lineArr[$line_id] : ",".$lineArr[$line_id];
							}		      	        		
			        	?>     
				         <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('trd_<?=$i; ?>','<?=$bgcolor;?>')" id="trd_<?=$i; ?>" style="font-size:12px">
				              <td  align="center" title="LineId=<? echo $lineId;?>" ><p><? echo $line_name;?></p></td>
                            <?
							$i++;
							$line_prod_tot=0;
				            foreach ($fiscalMonth_arr as $mon_id => $mon)
				            {
								$line_prod_qty=$year_floor_array[$mon_id][$lineId]['finishing']+$subCon_year_floor_sewOut_array[$mon_id][$lineId]['subSew'];
								$res_mst_id=rtrim($year_floor_array[$mon_id][$lineId]['mst_id'],',');
								if($res_mst_id!="")
								{
									$res_mst_ids=implode(",",array_unique(explode(",",$res_mst_id)));
								}
								if($subCon_year_floor_sewOut_array[$mon_id][$lineId]['mst_id']!="")
								{
								$sub_res_mst_id=rtrim($subCon_year_floor_sewOut_array[$mon_id][$lineId]['mst_id'],',');
								$sub_res_mst_ids=implode(",",array_unique(explode(",",$sub_res_mst_id)));
								//echo $sub_res_mst_id.'X,';
								}
								
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod(<? echo $year_floor_array[$mon_id][$lineId]['finishing'];?>)*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array[$mon_id][$lineId]['subSew'];?>)"><a href="##" onclick="fnc_line_month_popup('<? echo $mon_id.'_'.$cbo_company_id.'_'.$lineId.'_'.$res_mst_ids.'_'.$sub_res_mst_ids; ?>','gmt_line_month',1,'850');" ><?  echo number_format($line_prod_qty,0);?></a></td>
				            	<?
								$line_prod_tot_arr[$mon_id]+=$line_prod_qty;
								$line_prod_tot+=$line_prod_qty;
				            }
							?>
				            <td align="right" title=""><? echo number_format($line_prod_tot,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	            <th align="right">Grand Total</th>
	             <?
				 $gr_line_prod_tot=0;
	            foreach ($fiscalMonth_arr as $mon_id => $mon)
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><a href="javascript:void()" onclick="report_generate_by_month('<? echo $mon_id?>',3)"><? echo  number_format($line_prod_tot_arr[$mon_id],0);?></a></th>
	            	<?
					$gr_line_prod_tot+=$line_prod_tot_arr[$mon_id]; 
	            }
	            ?>
  				
                <th><? echo number_format($gr_line_prod_tot,0); ?></th>
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
}
 
if($action=="report_generate_by_month_daily_rmg") //Daily Line Wise Revenue RMG
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	 $lineArr = return_library_array("select a.id,a.line_name from lib_sewing_line a","id","line_name");
	$cbo_company_id = str_replace("'","",$cbo_company_id);
	$cbo_floor 			= str_replace("'","",$cbo_floor);
	$cbo_location 	= str_replace("'","",$cbo_location);
	$cbo_line 	= str_replace("'","",$cbo_line);
	if($cbo_location>0) $location_cond="and a.location in($cbo_location)";else $location_cond="";
	if($cbo_location>0) $location_cond2="and a.location_id in($cbo_location)";else $location_cond2="";
	if($cbo_location>0) $location_cond3="and b.location_id in($cbo_location)";else $location_cond3="";
	if($cbo_floor>0) $floor_cond="and a.floor_id in($cbo_floor)";else $floor_cond="";
	if($cbo_line>0) $line_cond="and c.id in($cbo_line)";else $line_cond="";
	//if($cbo_line>0) $line_cond="and a.sewing_line in($cbo_line)";else $line_cond="";
	if($cbo_line>0) $line_cond2="and a.sewing_line in($cbo_line)";else $line_cond2="";
	$year 		= str_replace("'","",$month_year);
	
	// getting month from fiscal year
	$exfirstYear 	= explode('-',$year);
	$firstYear 		= $exfirstYear[0];
	$lastYear 		= $exfirstYear[1];
	$time = date('m,Y',strtotime($year));
	$time = explode(',', $time);
	$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
	$yearMonth_arr 	= array(); 
	$yearStartEnd_arr = array();
	$j=12;
	$i=1;
	$days_arr = array();
	for ($i=1; $numberOfDays >= $i; $i++) 
	{ 
		$day = date('M',strtotime($year));
		$dayMonth = $i.'-'.$day;
		$dayMonth = date('d-M',strtotime($dayMonth));
		$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
	}
	// print_r($days_arr);die();
	$startDate =''; 
	$endDate ="";
	$startDate=date("d-M-Y",strtotime(($firstYear.'-7-1')));
	$endDate=date("d-M-Y",strtotime(($lastYear.'-6-30')));
	// echo $startDate.'='.$endDate;die();
	// $costing_per_arr = return_library_array("select job_no,costing_per from wo_pre_cost_mst","job_no", "costing_per");
	$location_library = return_library_array("select id,location_name from lib_location","id", "location_name");// where company_id=$cbo_company_id
	//$floor_library = return_library_array("select id,floor_name from lib_prod_floor where production_process=3 and company_id in($cbo_company_id) and status_active=1","id", "floor_name");// where company_id=$cbo_company_id
	
	// ============================ for gmts finishing =============================	
	
	$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
	foreach($sql_floor as $row )
	{
		$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
		$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
	}
	
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
	
	$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
	//echo "select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0";die;
	$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		/*$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.location_name=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.sewing_group is not null order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id' and a.sewing_group is not null and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by  a.id asc";
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			if($val[csf('floor_name')]==13)
			{
			$floor_group_ff_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==14)
			{
			$floor_group_sf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==16)
			{
			$floor_group_gf_arr[$flr_grp].=$val[csf('id')].',';
			}
			if($val[csf('floor_name')]==17)
			{
			$floor_group_tf_arr[$flr_grp].=$val[csf('id')].',';
			}
		}*/
		
	if($prod_reso_allocation==1)
	{
	  $sql_fin_prod=" SELECT a.sewing_line,c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0  $location_cond $floor_cond $line_cond order by c.line_number asc";
	}
	else
	{
			$sql_fin_prod=" SELECT a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)  and a.floor_id is not null and a.floor_id <> 0 $location_cond $floor_cond $line_cond2  order by a.sewing_line asc";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
		$line_wise_arr[$val[csf('line_number')]] = $val[csf('line_number')];
	}
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	//$po_qty_array = return_library_array("SELECT id,po_quantity from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond ","id", "id");
	
	$sql_po="SELECT id,po_quantity,pub_shipment_date,shipment_date,job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 $po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	
	//$cm_sql = "SELECT c.costing_date,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
	}

	// ======================================= getting subcontact data ================================= and company_id in($cbo_company_id)
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];

	//==================================== subcon order data =============================
	$order_wise_rate = return_library_array("SELECT id,rate from subcon_ord_dtls where status_active=1 and is_deleted=0","id","rate");
	// print_r($order_wise_rate);


	if($prod_reso_allocation==1)
	{
	$sql_sub_sewOut=" SELECT c.line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.production_type=2   $location_cond2 $line_cond $floor_cond order by a.floor_id";
	}
	else
	{
		$sql_sub_sewOut=" SELECT a.line_id as line_number, a.order_id,a.floor_id ,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$year' and a.status_active=1 and a.production_type=2   $location_cond2 $floor_cond  $line_cond2 order by a.floor_id";
	}
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);

	// =================================== subcon dyeing =============================
	
	//$sql_sub_dye.=" SELECT  b.order_id, to_char(a.product_date,'MON-YYYY') as month_year,b.product_qnty as qty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=292 and a.company_id in($cbo_company_id) and a.product_date between '$startDate' and '$endDate' and a.product_type=4 ";
	//$sql_sub_dye_res = sql_select($sql_sub_dye);
	$floorArray = array();
	$po_id_array = array();
	$year_location_qty_array = array();
	//$process_array = array(1,30);
	$kniting_process_array = array(1);
	foreach ($sql_fin_prod_res as $val) 
	{	
		//foreach($fiscal_year_arr as $fyear=>$ydata)
		//{
			$myear=$val[csf("month_year")];
			$sew_out_prod=$val[csf("sew_out")];
			$main_array[$myear]['qty']+=$sew_out_prod;//
			$main_array[$myear]['floor_id'] = $val[csf('location')];
			// ======================== calcutate SewingOut amount ====================
			//echo $print_avg_rate.'D';;
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
		//	echo $sew_smv.'='.$sew_smv.'X';
			$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				$line_number=array_unique(explode(",",$val[csf('line_number')]));
			  //echo $line_number.'d,';
			  $ff_floor_id=$val[csf('floor_id')];
			  //if($val[csf('line_number')]==72)   echo $val[csf('line_number')].'A,';else echo " ";
			  		 if($sew_smv>0)
					{
						//if($val[csf("sew_out")]=="") $val[csf("sew_out")]=0;else $val[csf("sew_out")]=$val[csf("sew_out")];
						
						$finish_cost=($sew_smv*$sew_out_prod*$cost_per_minute)/$exch_rate;
						if($finish_cost>0)
						{
						$mon_floor_array[$myear][$val[csf('line_number')]]['finishing'] += $finish_cost;
						}
					}
	}
	 //print_r($mon_floor_array);die();
	//SubCon Sewing Out
	foreach ($sql_sub_sewOut_result as $val) 
	{			
			$myear=$val[csf("month_year")];;				
			$subsewOut_cost =$order_wise_rate[$val[csf('order_id')]]*$val[csf('production_qnty')];	
			// echo $order_wise_rate[$val[csf('order_id')]]."*".$val[csf('qty')]."/".$rate;
			$sub_ff_floor_id=$val[csf('floor_id')];
		   $line_number=array_unique(explode(",",$val[csf('line_number')]));
			
			if($subsewOut_cost>0)
			{
				//if($val[csf('line_number')]==72)   echo $val[csf('line_number')].'B,';else echo " ";
			$subSewOut_costUSD = $subsewOut_cost/$rate;	
			$fiscalDyeingYear=$val[csf("year")].'-'.($val[csf("year")]+1);//
			$main_array[$myear]['subSew'] += $subSewOut_costUSD;	
			$subCon_year_floor_sewOut_array[$myear][$val[csf('line_number')]]['subSew'] += $subSewOut_costUSD;
			
			}
			$line_wise_arr[$val[csf('line_number')]] = $val[csf('line_number')];
	}
	asort($line_wise_arr);
	//print_r($subCon_year_floor_sewOut_array);
	unset($sql_fin_prod_res);
	unset($sql_sub_knit_res);
	unset($sql_sub_sewOut_result);
	$line_width=count($days_arr)*45;
	$tbl_width = 140+$line_width;
	ob_start();	
	?>
	<style>
	.rpt_table{font-size:13px!important;}
	</style>
	<!--=============Total Summary Start==================================================================-->

	    <table width="<? echo $tbl_width;?>"  cellspacing="0">
	        <tr class="form_caption">
	            <td colspan="5" align="center" ><strong style="font-size:19px"><?php echo $company_arr[$cbo_company_id]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="5" align="center"><strong style="font-size:14px"><? echo $report_title;?> </strong></td>
	        </tr>
	    </table>
	    <h3 style="width:<? echo $tbl_width;?>px;" align="left" id="accordion_h1" class="accordion_h" onClick="accordion_menu( this.id,'daily_revenue_report', '')"> -<b>Date wise revenue <? echo $year; ?></b></h3>
	    <div id="daily_revenue_report">
		<table border="1" rules="all" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0">
	        <thead>
	            <thead>
	             <th width="80">Line No</th>
                 <? 
			 	foreach ($days_arr as $year => $val) 
		        {
	            	?>
	            	<th width="45"><? echo date('d',strtotime($year)).' Day';?></th>
	            	<?
	            }
	            ?>
	        <th width="50">Total</th>
	        </thead>
	        </thead>
		        <tbody>   
		        <?
				
					$total_ashulia_rmg=$total_knit_asia=0;
					$i=1;
		        	foreach ($line_wise_arr as $lineId => $val) 
		        	{
		        		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 $line_arr=explode(",",$lineId);
							$line_name="";
							foreach($line_arr as $line_id)
							{
								$line_name .= ($line_name == "") ? $lineArr[$line_id] : ",".$lineArr[$line_id];
							}		      	        		
			        	?>     
				         <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('trd_<?=$i; ?>','<?=$bgcolor;?>')" id="trd_<?=$i; ?>" style="font-size:12px">
				              <td  title="LineId=<? echo $lineId;?>" align="center" style="word-break:break-all" ><p><? echo $line_name;?></p></td>
                            <?
							$i++;
							$line_prod_tot=0;
				          	 foreach ($days_arr as $day_id => $val) 
				            {
								$line_prod_qty=$mon_floor_array[$day_id][$lineId]['finishing']+$subCon_year_floor_sewOut_array[$day_id][$lineId]['subSew'];
								  ?>
				            	<td align="right" title="Sewing*SMV*CPM+SubConSewOut Prod(<? echo $year_floor_array[$day_id][$lineId]['finishing'];?>)*SubCOn Order Rate(<? echo $subCon_year_floor_sewOut_array[$day_id][$lineId]['subSew'];?>)"><?  echo number_format($line_prod_qty,0);?></td>
				            	<?
								$line_prod_tot_arr[$day_id]+=$line_prod_qty;
								$line_prod_tot+=$line_prod_qty;
				            }
							?>
				            <td align="right" title=""><? echo number_format($line_prod_tot,0); ?></td>
				        </tr>
				        <?
						$total_ashulia_rmg+=$rmg_floor_tot;
				    }
				    ?>
		        </tbody>
	        <tfoot>
	           <th align="right">Grand Total</th>
	             <?
				 $gr_line_prod_tot=0;
	             foreach ($days_arr as $day_id => $val) 
	            {
	            //	$rmg_floor_total[$floor_id]
					?>
	            	<th><? echo  number_format($line_prod_tot_arr[$day_id],0);?></th>
	            	<?
					$gr_line_prod_tot+=$line_prod_tot_arr[$day_id]; 
	            }
	            ?>
                <th><? echo number_format($gr_line_prod_tot,0); ?></th>
                
	        </tfoot>
	    </table>     
	    </div>    
	<?
	unset($main_array);
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename"; 
    exit();
} 
if($action=="gmt_line_month") // JM RMG
{
		echo load_html_head_contents("Gmts Revenue Dtls Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		
		$ex_dara=explode("_",$string_data);
		$gmt_date=$ex_dara[0];
		$cbo_company_id=$ex_dara[1];
		$line_id=$ex_dara[2];
		$resuo_id=$ex_dara[3];
		$sub_resuo_id=$ex_dara[4];
		//echo $resuo_id.'dDDDDD';
		//$floor_grp=$floor_ex_data[1]; 
		$type_id=$type;
		//echo $cbo_location;
		if($cbo_location>0 || $cbo_location!="") $loc_cond="and a.location in($cbo_location)"; else $loc_cond=""; 
		if($cbo_location>0 || $cbo_location!="") $loc_cond2="and a.location_id in($cbo_location)"; else $loc_cond2=""; 
		if($cbo_floor>0 || $cbo_floor!="") $floor_cond="and a.floor_id in($cbo_floor)"; else $floor_cond="";
		if($cbo_floor>0 || $cbo_floor!="") $floor_cond2="and b.floor_name in($cbo_floor)"; else $floor_cond2=""; 
		if($cbo_floor>0 || $cbo_floor!="") $floor_cond3="and b.id in($cbo_floor)"; else $floor_cond3=""; 
		//if($line_id>0 || $line_id!="") $line_cond="and c.id in($line_id)"; else $line_cond=""; 
	//	echo $line_id;
		$year 		= str_replace("'","",$gmt_date);
	
	// getting month from fiscal year
		$exfirstYear 	= explode('-',$year);
		$firstYear 		= $exfirstYear[0];
		$lastYear 		= $exfirstYear[1];
		$time = date('m,Y',strtotime($year));
		$time = explode(',', $time);
		$numberOfDays = cal_days_in_month(CAL_GREGORIAN, $time[0],$time[1]);
		$yearMonth_arr 	= array(); 
		$yearStartEnd_arr = array();
		$j=12;
		$i=1;
		$days_arr = array();
		for ($i=1; $numberOfDays >= $i; $i++) 
		{ 
			$day = date('M',strtotime($year));
			$dayMonth = $i.'-'.$day;
			$dayMonth = date('d-M',strtotime($dayMonth));
			$days_arr[strtoupper($dayMonth)] = strtoupper($dayMonth);
		}
		//print_r($days_arr);
		//echo $line_id.'='.$floor_grp;die;
		//$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
		$company_name_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
		$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
		$location_arr = return_library_array("select id,location_name from lib_location","id", "location_name"); 
		//$floor_arr = return_library_array("select id,floor_name from lib_prod_floor","id", "floor_name");

		$sql_floor=sql_select("select id,floor_name from lib_prod_floor where production_process=5 and company_id in($cbo_company_id) and status_active=1");
		foreach($sql_floor as $row )
		{
			$floor_library[$row[csf('id')]]['floor']=$row[csf('floor_name')];
			$floor_library[$row[csf('id')]]['floor_id']=$row[csf('id')];
		}
	 $sql_rate = "SELECT conversion_rate as rate from currency_conversion_rate where currency=2  order by con_date desc";
	$sql_rate_res = sql_select($sql_rate);
	$rate = $sql_rate_res[0][csf('rate')];
	$exch_rate = $sql_rate_res[0][csf('rate')];
	//===============CM Variable ===========================
	$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=".$cbo_company_id."  and variable_list=22 and status_active=1 and is_deleted=0");
	//echo $cm_cost_method_based_on.'DD';
	if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
	//===============CPM from Library=============================
	$sql_std_para=sql_select("select cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$cbo_company_id and status_active=1 and is_deleted=0 order by id");
		
		foreach($sql_std_para as $row )
		{
			$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
			$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
			$diff=datediff('d',$applying_period_date,$applying_period_to_date);
			for($j=0;$j<$diff;$j++)
			{
				$date_all=add_date(str_replace("'","",$applying_period_date),$j);
				$newdate =change_date_format($date_all,'','',1);
				$financial_para_cpm[$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
			}
		}
		
		$nameArray= sql_select("select id, auto_update from variable_settings_production where company_name='$cbo_company_id' and variable_list=23 and status_active=1 and is_deleted=0");
		$prod_reso_allocation = $nameArray[0][csf('auto_update')];
	
		$line_data=sql_select("select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name,b.floor_name as f_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    $floor_cond3   order by  a.id asc");
		//echo "select a.id, a.line_name,a.sewing_group,a.floor_name,a.location_name,b.floor_name as f_name from lib_sewing_line a,lib_prod_floor b where  a.floor_name=b.id and a.location_name=b.location_id and a.company_name='$cbo_company_id'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0    $floor_cond3   order by  a.id asc";
	 
		$floor_group_ff_arr=array();$floor_group_sf_arr=array();$floor_group_gf_arr=array();$floor_group_tf_arr=array();
		foreach($line_data as $val)
		{
			//$line_arr=;
			$line_name_arr[$val[csf('id')]]=$val[csf('line_name')];
			$line_name_arr[$val[csf('floor_name')]]=$val[csf('f_name')];
			$flr_grp=$val[csf('floor_name')].'_'.$val[csf('sewing_group')];
			$flr_grp_data=$val[csf('floor_name')].'_'.$val[csf('sewing_group')].'_'.$val[csf('id')];
			$floor_line_group_arr[$val[csf('floor_name')]].=$val[csf('sewing_group')].',';
			
		}
		//print_r($line_name_arr);
		
	if($prod_reso_allocation==1)
	{
		
	   $sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line,c.line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,a.production_date as production_date,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b,prod_resource_mst c where a.id=b.mst_id and c.id=a.sewing_line and a.serving_company in($cbo_company_id)   and to_char(a.production_date,'MON-YYYY')='$gmt_date'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1) and a.floor_id is not null and a.floor_id <> 0 and c.id in($resuo_id)  $loc_cond $floor_cond $line_cond   order by a.floor_id";
	}
	else
	{
			$sql_fin_prod=" SELECT a.item_number_id as item_id,a.sewing_line as line_number,a.floor_id,a.po_break_down_id,a.item_number_id as item_id,to_char(a.production_date,'DD-MON') as month_year,b.production_qnty  AS sew_out from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.serving_company in($cbo_company_id)   and to_char(a.production_date,'MON-YYYY')='$gmt_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and a.production_type in(5)  and a.production_source in(1)   and a.floor_id is not null and a.floor_id <> 0   and a.sewing_line in($resuo_id) $loc_cond $floor_cond $line_cond   order by a.floor_id";
	}

	$sql_fin_prod_res = sql_select($sql_fin_prod);
	foreach ($sql_fin_prod_res as $val) 
	{
		$po_id_array[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
		$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('sew_out')];
	}
	
	$poIds = implode(",", array_unique($po_id_array));
	if($poIds !="")
	{
		$po_cond="";
		if(count($po_id_array)>999)
		{
			$chunk_arr=array_chunk($po_id_array,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( id in ($ids) ";
				else
					$po_cond.=" or   id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and id in ($poIds) ";
		}
	}
	$cm_po_cond = str_replace("id", "b.id", $po_cond);
	$cm_po_cond2 = str_replace("id", "b.po_id", $po_cond);
	$sql_po="SELECT a.buyer_name,a.style_ref_no,b.id,b.grouping as ref_no,b.po_quantity,b.pub_shipment_date,b.shipment_date,b.job_no_mst from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0 $cm_po_cond";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
		$po_ref_array[$val[csf('id')]]['ref_no']= $val[csf('ref_no')];
		$po_ref_array[$val[csf('id')]]['style_ref_no']= $val[csf('style_ref_no')];
		$po_date_array[$val[csf('job_no_mst')]]['ship_date'].= $val[csf('shipment_date')].',';
		$po_date_array[$val[csf('job_no_mst')]]['pub_date'].= $val[csf('pub_shipment_date')].',';
	}
	
	//$cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	 $cm_sql = "SELECT c.costing_date,c.costing_per,c.sew_smv,a.cm_cost,b.id,d.smv_set,d.gmts_item_id from wo_pre_cost_mst c,wo_pre_cost_dtls a, wo_po_break_down b,wo_po_details_mas_set_details d where c.job_no=b.job_no_mst and c.job_no=a.job_no and  a.job_no=b.job_no_mst and d.job_no=b.job_no_mst and d.job_no=c.job_no  and a.status_active=1 and b.status_active=1 and c.status_active=1 $cm_po_cond ";
	$cm_sql_res = sql_select($cm_sql);
	$cm_cost_array = array();
	foreach ($cm_sql_res as $val) 
	{
		$cm_cost_array[$val[csf('id')]] = $val[csf('cm_cost')];
		$pre_cost_smv_array[$val[csf('id')]][$val[csf('gmts_item_id')]]['sew_smv'] = $val[csf('smv_set')];
		$pre_cost_array[$val[csf('id')]]['costing_date'] = $val[csf('costing_date')];
		$costing_per_arr[$val[csf('id')]]=$val[csf('costing_per')];
	} 
	//echo $line_chk=rtrim($line_id,',');
	$line_chk_arr=explode(",",$line_chk);
	foreach ($sql_fin_prod_res as $val)  //Main Query
	{
			//$sewing_qty_array[$val[csf('po_break_down_id')]] = $val[csf('msew_out')];
			$po_buyer=$po_buyer_array[$val[csf('po_break_down_id')]];
			//echo $po_buyer.'d';
			$ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['ref_no'];
			$style_ref_no=$po_ref_array[$val[csf('po_break_down_id')]]['style_ref_no'];
			//$line_number=$val[csf('line_number')];
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			$sew_smv=$pre_cost_smv_array[$val[csf('po_break_down_id')]][$val[csf('item_id')]]['sew_smv'];
			//echo $cm_cost_method_based_on.'d';
			//print_r($line_numberArr);
			if($sew_smv>0)
			{
				$cm_cost_based_on_date="";
				if($cm_cost_method_based_on==1){ 
				$costing_date=$pre_cost_array[$val[csf('po_break_down_id')]]['costing_date'];
				if($db_type==0) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==2){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$min_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$min_shipment_date=min($min_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==3){
				$shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['ship_date'],',');
				$max_shipment_dateArr=array_unique(explode(",",$shipment_date));
				$max_shipment_date=max($max_shipment_dateArr);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				else if($cm_cost_method_based_on==4){
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$min_pub_shipment_date=min($pub_shipment_date);
				//echo $val[csf('po_break_down_id')].'='.$min_pub_shipment_date.', ';
				if($db_type==0) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				//echo $cm_cost_based_on_date.'CPm,';
				}
				else if($cm_cost_method_based_on==5){
				
				$pub_shipment_date=rtrim($po_date_array[$po_job_array[$val[csf('po_break_down_id')]]]['pub_date'],',');
				$pub_shipment_date=array_unique(explode(",",$pub_shipment_date));
				$max_pub_shipment_date=max($pub_shipment_date);
				if($db_type==0) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");
				if($db_type==2) $cm_cost_based_on_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
				}
				
				$cm_cost_based_on_date=change_date_format($cm_cost_based_on_date,'','',1);
				$cost_per_minute=$financial_para_cpm[$cm_cost_based_on_date][cost_per_minute];
				if($cost_per_minute=="") $cost_per_minute=0;else $cost_per_minute=$cost_per_minute;
				//echo $sew_smv.'='.$val[csf('sew_out')].'='.$cost_per_minute.'='.$exch_rate.'<br> ';
					$sew_out_cost=($sew_smv*$val[csf('sew_out')]*$cost_per_minute)/$exch_rate;
					//$year_location_qty_array[$fyear][$val[csf('location')]]['finishing'] += $finish_cost;
					//line_id
					//foreach($line_numberArr as $lId) 
					//{ 
					////if(in_array($lId,$line_chk_arr))
					//{
						$line_wise_array[$val[csf('production_date')]][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_out']+= $val[csf('sew_out')];
						$line_wise_array[$val[csf('production_date')]][$po_buyer][$ref_no][$val[csf('item_id')]]['revenue']+= $sew_out_cost;
						$line_wise_array[$val[csf('production_date')]][$po_buyer][$ref_no][$val[csf('item_id')]]['style_ref_no']= $style_ref_no;
						$line_wise_array[$val[csf('production_date')]][$po_buyer][$ref_no][$val[csf('item_id')]]['sew_smv']= $sew_smv;
						$line_wise_array[$val[csf('production_date')]][$po_buyer][$ref_no][$val[csf('item_id')]]['cost_per_minute']= $cost_per_minute;
					//}
					//}
					
			}
			//echo $val[csf('floor_id')].', ';
				$floor_name_arr[$line_name_arr[$val[csf('floor_id')]]]=$line_name_arr[$val[csf('floor_id')]];
			
	}
	ksort($line_wise_array);
 	//print_r($floor_name_arr);
	
	// =================================== subcon kniting =============================
	  $width_td="780";
	   ?>
       <div style="margin-left:20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
         <caption><b>Garments Revenue - Line wise </b> </caption>
       <tr>
        
           <td  width="100"> <b>Floor</b> </td>
           <td  width="70"><?  echo implode(',',$floor_name_arr);?></td>
             <td  width="100"> <b>Line No</b> </td>
           <td  width="70"><?  echo $line_name_arr[$line_id];?></td>
           <td width="195" colspan="4"><b style="float:right"> Date: </b></td>
           <td width="100"> &nbsp; <? echo $gmt_date;?></td>
            
       </tr>
       <tr>
        </table>
       <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Own Job </b> </caption>
		<thead>
			<th width="70">Date</th>
            <th width="100">Buyer</th>
			<th width="70">Ref No</th>
			<th width="150">Style Name</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">SMV</th>
			<th width="70">Produce Minute</th>
            <th width="40">CPM($)</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
        
        	 
            <?
			foreach ($line_wise_array as $date_key=>$line_data)
			{
				$line_row_span=0;
			 foreach ($line_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $ref_no=>$ref_data)
			  {
				  foreach ($ref_data as $item_id=>$row)
				  {
					  $line_row_span++;
				  }
			 	 $line_row_arr[$date_key]=$line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$buyer_tot_gmt_revenue=$total_gmt_prod_min=$total_gmt_prod=0;
            $i=1;$k=1;$sub_group_by_arr=array();
			foreach ($line_wise_array as $date_key=>$line_data)
			{
			 $b=1;
			 foreach ($line_data  as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data  as $ref_no=>$ref_data)
			  {
			  foreach ($ref_data as $item_id=>$row)
			  {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                 	<?
                   if($b==1)
					{
					?>
					<td width="70" rowspan="<? echo  $line_row_arr[$date_key];?>" title="<? echo $line_id;?>"><? echo date('d-F',strtotime($date_key));; ?></td>
                    <?
                    }?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
					<td width="70"><p>&nbsp;<? echo $ref_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo $row['sew_out']; ?>&nbsp;</p></td>
					<td width="40"><p><? echo $row['sew_smv']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*SMV"><? $produce_min=$row['sew_out']*$row['sew_smv'];echo $produce_min;; ?></td>
                    <td width="40" title="CPM(<? echo $row['cost_per_minute'];?>)/ExhangeRate(<? echo $exch_rate;?>)"><p><? echo number_format($row['cost_per_minute']/$exch_rate,2); ?>&nbsp;</p></td>
                    <td width="" align="right" title="Produce Min*CPM/Exchange Rae"><p><? 
					$tot_revenue=$row['revenue'];//($produce_min*$row['revenue'])/$exch_rate;
					echo number_format($tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$b++;
				$buyer_tot_gmt_revenue+=$tot_revenue;
				$buyer_tot_gmt_prod+=$row['sew_out'];
				$buyer_tot_gmt_prod_min+=$produce_min;
				
				$total_gmt_revenue+=$tot_revenue;
				$total_gmt_prod+=$row['sew_out'];
				$total_gmt_prod_min+=$produce_min;
			  }
			 }
			 }
			}
			?>
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"></td>
                <td width="100"><p>Grand Total </p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo number_format($total_gmt_prod_min,0) ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
        <br>
        <?
      if($prod_reso_allocation==1)
		{
		    $sql_sub_sewOut=" SELECT a.gmts_item_id,a.order_id,c.line_number, a.order_id,a.floor_id ,a.production_date,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a,prod_resource_mst c  where  c.id=a.line_id and  a.company_id in($cbo_company_id)  and to_char(a.production_date,'MON-YYYY')='$gmt_date'  and a.status_active=1 and a.production_type=2  and c.id in($sub_resuo_id)  $loc_cond2 $floor_cond $line_cond2  order by a.floor_id";
		  // $sql_sub_sewOut="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and a.production_date='$gmt_date' and a.production_type=2 and a.location_id=$location_id and a.status_active=1 ";
		}
		else
		{
			$sql_sub_sewOut=" SELECT a.gmts_item_id,a.line_id as line_number, a.order_id,a.floor_id ,a.production_date,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty from subcon_gmts_prod_dtls a where    a.company_id in($cbo_company_id)  and a.status_active=1 and a.production_type=2  and to_char(a.production_date,'MON-YYYY')='$gmt_date' and a.line_id in($sub_resuo_id)  $loc_cond2 $floor_cond2 $line_cond2 order by a.floor_id";
		}
		//echo $sql_sub_sewOut.'XD';
	
	$sql_sub_sewOut_result = sql_select($sql_sub_sewOut);
	// =================================== SubCon Sewout =============================
	  $sql_sub_sewOut2="SELECT a.order_id,a.location_id,a.gmts_item_id as item_id,to_char(a.production_date,'DD-MON') as month_year,a.production_qnty,b.cust_style_ref as style_ref_no,b.rate,b.order_no,c.party_id as buyer_name,b.rate from subcon_gmts_prod_dtls a,subcon_ord_dtls b,subcon_ord_mst c  where  a.order_id=b.id  and b.job_no_mst=c.subcon_job and a.company_id in($cbo_company_id) and to_char(a.production_date,'MON-YYYY')='$gmt_date' and a.production_type=2  and a.status_active=1  $loc_cond2 $floor_cond $line_cond2 ";
	$sql_sub_sewOut_result2 = sql_select($sql_sub_sewOut2);
	foreach($sql_sub_sewOut_result2 as $val)//subcon_job,job_no_mst
	{
		$sub_po_arr[$val[csf("order_id")]]['order_no']=$val[csf("order_no")];
		$sub_po_arr[$val[csf("order_id")]]['buyer_name']=$val[csf("buyer_name")];
		$sub_po_arr[$val[csf("order_id")]]['style_ref_no']=$val[csf("style_ref_no")];
		$sub_po_arr[$val[csf("order_id")]]['rate']=$val[csf("rate")];
	}
//	print_r($sql_sub_sewOut_result);
	foreach($sql_sub_sewOut_result as $val)//subcon_job,job_no_mst
	{
		
			$fyear=$val[csf("month_year")];
			$order_no=$sub_po_arr[$val[csf("order_id")]]['order_no'];
			$style_ref_no=$sub_po_arr[$val[csf("style_ref_no")]]['style_ref_no'];
			$po_buyer=$sub_po_arr[$val[csf("order_id")]]['buyer_name'];
			$order_rate=$sub_po_arr[$val[csf("order_id")]]['rate'];	
			$line_numberArr=array_unique(explode(",",$val[csf('line_number')]));
			//echo $val[csf('line_number')].', ';		
			$subsewOut_cost =$order_rate*$val[csf('production_qnty')];
			if($subsewOut_cost>0)
			{
				//foreach($line_numberArr as $lId)
					//{
					
					$sub_line_wise_array[$val[csf('production_date')]][$po_buyer][$order_no][$val[csf('gmts_item_id')]]['production_qnty']+= $val[csf('production_qnty')];
					$sub_line_wise_array[$val[csf('production_date')]][$po_buyer][$order_no][$val[csf('gmts_item_id')]]['revenue']+= $subsewOut_cost/$exch_rate;
					$sub_line_wise_array[$val[csf('production_date')]][$po_buyer][$order_no][$val[csf('gmts_item_id')]]['style_ref_no']= $style_ref_no;
					$sub_line_wise_array[$val[csf('production_date')]][$po_buyer][$order_no][$val[csf('gmts_item_id')]]['rate']= $order_rate;
					
				// }
			}
	 }
	 ksort($sub_line_wise_array);
	// print_r($sub_line_wise_array);
	
		?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table">
        <caption><b>Subcontract Job </b> </caption>
		<thead>
			<th width="70">Line No</th>
            <th width="100">Factory Name</th>
			<th width="70">Order No</th>
			<th width="150">Customer Style</th>
			<th width="100">Gmts Item Name</th>
			<th width="70">Production qty Pcs</th>
			<th width="40">Pcs Rate<br>(Taka)</th>
			<th width="70">Total Taka</th>
            <th width="40">Dollar Tnx</th>
            <th width="">Revenue</th>
		</thead>
        </tr>
	 </table>
     <div style="width:<? echo $width_td+20;?>px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">  
            <?
			foreach ($sub_line_wise_array as $date_key=>$date_data)
			{
				$sub_line_row_span=0;
			 foreach ($date_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				  foreach ($po_data as $item_id=>$row)
				  {
					  $sub_line_row_span++;
				  }
				  $sub_line_row_arr[$date_key]=$sub_line_row_span;
			  }
			 }
			}
			//print_r($buyer_row_arr);
			$sub_total_gmt_revenue=$sub_total_gmt_prod=$sub_total_gmt_tk=0;
            $i=1;$k=1;$subcon_group_by_arr=array();
			foreach ($sub_line_wise_array as $date_key=>$date_data)
			{
			$sb=1;
			 foreach ($date_data as $buyer_id=>$buyer_data)
			 {
			  foreach ($buyer_data as $po_no=>$po_data)
			  {
				 foreach ($po_data as $item_id=>$row)
			     {
				  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	//line_name_arr
			?>
          		 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trsub_<? echo $i; ?>','<? echo $bgcolor;?>')" id="trsub_<? echo $i; ?>" style="font-size:11px">
                 <?
                  if($sb==1)
					{
					?>
					<td width="70" rowspan="<? echo  $sub_line_row_arr[$date_key];?>" title="<? echo $line_id;?>"><? echo  date('d-F',strtotime($date_key)); ?></td>
                    <?
                   }
					?>
					<td width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></td>
                   
					<td width="70"><p>&nbsp;<? echo $po_no; ?></p></td>
					<td width="150" style="word-break:break-all"><p>&nbsp;<? echo $row['style_ref_no']; ?></p></td>
					<td width="100" align="center"><p><? echo $garments_item[$item_id]; ?></p></td>
					<td width="70" align="right"><p><? echo number_format($row['production_qnty'],0); ?>&nbsp;</p></td>
					<td width="40" align="right"><p><? echo $row['rate']; ?>&nbsp;</p></td>
					<td width="70" align="right" title="Prod Pcs Qty*Order Rate"><? $tot_tk=$row['production_qnty']*$row['rate'];echo number_format($tot_tk,0); ?></td>
                    <td width="40" align="right"><p><? echo $exch_rate; ?>&nbsp;</p></td>
                    <td width="" align="right" title="Tot Tk/Exchange Rae"><p><? 
					$sub_tot_revenue=$row['revenue'];//$tot_tk/$exch_rate;// $sub_tot_revenue=$tot_tk/$exch_rate;
					echo number_format($sub_tot_revenue,0); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;$sb++;
				
				$sub_buyer_tot_gmt_revenue+=$sub_tot_revenue;
				$sub_buyer_tot_gmt_tk+=$tot_tk;
				$sub_buyer_tot_prod+=$row['production_qnty'];
				
				$sub_total_gmt_revenue+=$sub_tot_revenue;
				$sub_total_gmt_prod+=$row['production_qnty'];
				$sub_total_gmt_tk+=$tot_tk;
			   }
			  }
			  
			 }
			}
			?>
            
        </table>
      </div>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width_td;?>" class="rpt_table" id="tbl_list_search">
        		<tr class="tbl_bottom">
            	<td width="70" ></td>
                <td width="100" ></td>
                <td width="70"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="150"><p> </p></td>
                <td width="100"><p>Grand Total</td>
                <td width="70"><p><? echo number_format($sub_total_gmt_prod,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width="70"><p><? echo  number_format($sub_total_gmt_tk,0); ?>&nbsp;</p></td>
                <td width="40"><p><? //echo $dye_comp; ?>&nbsp;</p></td>
                <td width=""><p><? echo number_format($sub_total_gmt_revenue,0); ?>&nbsp;</p></td>
            </tr>
        </table>
    </div>
       <?
	
exit();
}
?>