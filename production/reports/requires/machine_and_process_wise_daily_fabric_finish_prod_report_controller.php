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
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down( 'requires/machine_and_process_wise_daily_fabric_finish_prod_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );",0 );
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

if($action=="report_generate") // Show Button
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
	}
	/*echo '<pre>';
  	print_r($idle_data_arr);die;*/		

	if ($order_type==0 || $order_type==1) // Self Order
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
		<table width="1740" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
			<thead>
				<tr>
	                <th rowspan="2" width="30">SL No</th>
	                <th rowspan="2" width="80">M/C & Capacity</th>
	                <th rowspan="2" width="80">Buyer Name</th>
	                <th rowspan="2" width="80">Batch No.</th>
	                <th rowspan="2" width="80">Batch Color</th>
	                <th rowspan="2" width="80">Booking No.</th>
	                <th rowspan="2" width="80">Color Range</th>
	                <th colspan="5" width="80">Total Production Qty in Kg</th>
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
	                <th width="80">Re-Process</th>
	                <th width="80">Trims Weight</th>
	            </tr> 
			</thead>
		</table>
	    <div style="width:1760px; overflow-y:scroll; max-height:300px;" id="scroll_body">
		<table align="center" cellspacing="0" width="1740"  border="1" rules="all" class="rpt_table" >
			
			<? 
			$sqls="SELECT  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks 
			from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond  
			and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 
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
			//print_r($sql_dtls);
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
			 $sql_result_sales="SELECT q.sales_order_no,a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date, 
			 a.water_flow_meter, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity, p.buyer_id,
			 p.po_buyer as buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
			 from fabric_sales_order_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			 where a.service_company=$cbo_company_id $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond
			 and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2 
			 and a.batch_id=q.id and p.id=q.sales_order_id and q.booking_without_order!=1
			 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			 order by a.production_date, c.machine_no";
			//echo $sql_result;die;
			$sql_dtls_sales=sql_select($sql_result_sales);
			//print_r($sql_dtls);
			foreach ($sql_dtls_sales as $row)
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
			/*echo "<pre>";
			print_r($date_data_arr);die;*/

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
			/*echo '<pre>';
			print_r($rowspan_arr);*/

			$i=1;
			$other_grnd_total=$white_grnd_total=$wash_grnd_total=$pro_qty_grnd_total=0;
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$pro_qty_date_wise_total=0; 
				?>
				<tr>
					<td colspan="20" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
				</tr>
				<?
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					$other_mcw_wise_total=$white_mcw_wise_total=$wash_mcw_wise_total=$total_pro_qty=0; 
					$r=0;
					foreach ($machine_data as $batch_id => $row) 
					{
						//echo $row['batch_ext_no'];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
							<?							
								if ($r==0) 
								{
									?>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="30" valign="middle"><?echo $i;?></td>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="80" valign="middle"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
									<?
								} $r++;
							?>
							<td width="80"><?echo $buyer_library[$row['buyer_id']];?></td>
							<td width="80"><?echo $row['batch_no'];?></td>
							<td width="80"><p><?echo $color_library[$row['color_id']]?></p></td>
							<td width="80"><?echo $row['booking_no'];?></td>
							<td width="80"><?echo $color_range[$row['color_range_id']];?></td>
							<? 
							$avarage_batch_qty=$white_batch_qty=$wash_batch_qty=$pro_qty=0; 
							if ($row['color_range_id']!=4 && $row['color_range_id']!=7)
							{
								$avarage_batch_qty = $row['batch_qty']; 
							} 
							?>
							<td width="80" align="right"><? echo $avarage_batch_qty; $other_mcw_wise_total+=$avarage_batch_qty;?></td> 
							<? 
							if ($row['color_range_id']==4 && $row['result']==1)
							{
								$white_batch_qty = $row['batch_qty']; 
							} 
							?>
							<td width="80" align="right"><? echo $white_batch_qty; $white_mcw_wise_total+=$white_batch_qty;?></td>
							<? 
							if ($row['color_range_id']==7 && $row['result']==1)
							{
								$wash_batch_qty = $row['batch_qty']; 
							} 
							?>
							<td width="80" align="right"><? echo $wash_batch_qty; $wash_mcw_wise_total+=$wash_batch_qty;?></td>
							<? 
							if ($row['result']==2) 
							{
								$pro_qty = $row['batch_qty']; 
							}
							?>
							<td width="80" align="right"><?echo $pro_qty; $total_pro_qty+=$pro_qty;?></td>
							<td width="80" align="right"><?echo $row['total_trims_weight'];?></td>
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
						//$i++;
						//$other_mcw_wise_total+=$other_pro_qty;
						//$white_mcw_wise_total+=$white_pro_qty;
						
					}
					if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					{
						?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$idle_data_arr[$p_date][$machine_id]['machine_idle_cause']];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr[$p_date][$machine_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr[$p_date][$machine_id]['from_hour'];
									$idle_start_minut = $idle_data_arr[$p_date][$machine_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut; 
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr[$p_date][$machine_id]['to_hour'];
									$idle_end_minut = $idle_data_arr[$p_date][$machine_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut; 
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr[$p_date][$machine_id]['from_date'];
									$to_date = $idle_data_arr[$p_date][$machine_id]['to_date'];
									 
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
							</tr>
						<?	
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Total:</b></td>
						<td><strong><? echo $other_mcw_wise_total;?></strong></td>
						<td><strong><? echo $white_mcw_wise_total;?><strong></td>
						<td><strong><? echo $wash_mcw_wise_total;?><strong></td>
						<td><strong><? echo $total_pro_qty;?><strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$i++;
					$other_date_wise_total+=$other_mcw_wise_total;
					$white_date_wise_total+=$white_mcw_wise_total;
					$wash_date_wise_total+=$wash_mcw_wise_total;
					$pro_qty_date_wise_total+=$total_pro_qty;
				}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Date wise Total:</b></td>
						<td><strong><? echo $other_date_wise_total;?></strong></td>
						<td><strong><? echo $white_date_wise_total;?></strong></td>
						<td><strong><? echo $wash_date_wise_total;?></strong></td>
						<td><strong><? echo $pro_qty_date_wise_total;?></strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$other_grnd_total+=$other_date_wise_total;
					$white_grnd_total+=$white_date_wise_total;
					$wash_grnd_total+=$wash_date_wise_total;
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

	if ($order_type==0 || $order_type==2) // Subcontract Order
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
		<table width="1740" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
			<thead>
				<tr>
	                <th rowspan="2" width="30">SL No</th>
	                <th rowspan="2" width="80">M/C & Capacity</th>
	                <th rowspan="2" width="80">Party Name</th>
	                <th rowspan="2" width="80">Batch No.</th>
	                <th rowspan="2" width="80">Batch Color</th>
	                <th rowspan="2" width="80">Order No.</th>
	                <th rowspan="2" width="80">Color Range</th>
	                <th colspan="5" width="80">Total Production Qty in Kg</th>
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
	                <th width="80">Re-Process</th>
	                <th width="80">Trims Weight</th>
	            </tr> 
			</thead>
		</table>
	    <div style="width:1760px; overflow-y:scroll; max-height:300px;" id="scroll_body">
		<table align="center" cellspacing="0" width="1740"  border="1" rules="all" class="rpt_table" >
			
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
		  }
		  	/*echo '<pre>';
		  	print_r($load_time_array);die;*/
		  	
		  		
		  	if ($buyer_name==0  || $buyer_name=='') $party_name_cond =""; else $party_name_cond =" and f.party_id=$buyer_name ";
			if ($order_no=="") $order_no_cond =""; else $order_no_cond =" and p.order_no=$txt_order_no ";

		  	// Main query	
			$sql_result="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date, a.water_flow_meter, b.batch_qnty, b.item_description, c.machine_no, c.prod_capacity, f.party_id, p.order_no, q.color_id, q.color_range_id, q.total_trims_weight, q.process_id 
			from subcon_ord_dtls p, subcon_ord_mst f, pro_batch_create_mst q, pro_fab_subprocess a, pro_batch_create_dtls b, lib_machine_name c 
			where a.service_company=$cbo_company_id $order_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $party_name_cond $batch_color_cond $color_range_cond and a.machine_id=c.id and a.entry_form=38 and q.entry_form=36 and a.load_unload_id=2 and a.batch_id=q.id and b.po_id=p.id  and p.job_no_mst= f.subcon_job and q.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.production_date, c.machine_no";

			//echo $sql_result;die;

			$sql_dtls=sql_select($sql_result);
			//print_r($sql_dtls);
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
		
			/*echo "<pre>";
			print_r($date_data_arr);*/
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
			//echo '<pre>';
			//print_r($rowspan_arr);

			$i=1;
			$subcon_other_grnd_total=$subcon_white_grnd_total=$subcon_wash_grnd_total=$subcon_pro_qty_grnd_total=0;
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				$subcon_other_date_wise_total=$subcon_white_date_wise_total=$subcon_wash_date_wise_total=$subcon_pro_qty_date_wise_total=0;
				?>
				<tr>
					<td colspan="20" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
				</tr>
				<?
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					$subcon_other_mcw_wise_total=$subcon_white_mcw_wise_total=$subcon_wash_mcw_wise_total=$subcon_pro_qty_total=0;
					$r=0;
					foreach ($machine_data as $batch_id => $row) 
					{
						//echo $row['batch_ext_no'];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
							<?							
								if ($r==0) 
								{
									?>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="30" valign="middle"><?echo $i;?></td>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
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
							$subcon_other_pro_qty=$subcon_white_pro_qty=$subcon_wash_pro_qty=$pro_qty=0; 
							if ($row['color_range_id']==5 && $row['result']==1)
							{
								$subcon_other_pro_qty = $row['batch_qnty'];
							} 
							?>
							<td width="80" align="right"><? echo $subcon_other_pro_qty; $subcon_other_mcw_wise_total+=$subcon_other_pro_qty;?></td>	

							<? 
								if ($row['color_range_id']==4 && $row['result']==1)
								{
									$subcon_white_pro_qty = $row['batch_qnty']; 
								} 
							?> 
							<td width="80" align="right"><? echo $subcon_white_pro_qty; $subcon_white_mcw_wise_total+=$subcon_white_pro_qty;?></td>
							<? 
								if ($row['color_range_id']==7 && $row['result']==1)
								{
									$subcon_wash_pro_qty = $row['batch_qnty'];
								} 
							?>
							<td width="80" align="right"><? echo $subcon_wash_pro_qty; $subcon_wash_mcw_wise_total+=$subcon_wash_pro_qty;?></td> 							
							<?
								if ($row['result']==2) 
								{
									$pro_qty = $row['batch_qnty']; 
								}
							?>
							<td width="80" align="right"><?echo $pro_qty; $subcon_pro_qty_total+=$pro_qty;?></td>
							<td width="80" align="right"><?echo $row['total_trims_weight'];?></td>
							<td width="80"><?echo $row['water_flow_meter'];?></td>
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
						//$i++;
						//$subcon_other_mcw_wise_total+=$subcon_other_pro_qty;
						//$subcon_white_mcw_wise_total+=$subcon_white_pro_qty;
					}
					if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					{
						?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$idle_data_arr[$p_date][$machine_id]['machine_idle_cause']];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr[$p_date][$machine_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr[$p_date][$machine_id]['from_hour'];
									$idle_start_minut = $idle_data_arr[$p_date][$machine_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut; 
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr[$p_date][$machine_id]['to_hour'];
									$idle_end_minut = $idle_data_arr[$p_date][$machine_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut; 
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr[$p_date][$machine_id]['from_date'];
									$to_date = $idle_data_arr[$p_date][$machine_id]['to_date'];
									 
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
							</tr>
						<?	
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Total:</b></td>
						<td><strong><? echo $subcon_other_mcw_wise_total;?></strong></td>
						<td><strong><? echo $subcon_white_mcw_wise_total;?><strong></td>
						<td><strong><? echo $subcon_wash_mcw_wise_total;?><strong></td>
						<td><strong><? echo $subcon_pro_qty_total;?><strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$i++;
					$subcon_other_date_wise_total+=$subcon_other_mcw_wise_total;
					$subcon_white_date_wise_total+=$subcon_white_mcw_wise_total;
					$subcon_wash_date_wise_total+=$subcon_wash_mcw_wise_total;
					$subcon_pro_qty_date_wise_total+=$subcon_pro_qty_total;
				}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Date wise Total:</b></td>
						<td><strong><? echo $subcon_other_date_wise_total;?></strong></td>
						<td><strong><? echo $subcon_white_date_wise_total;?></strong></td>
						<td><strong><? echo $subcon_wash_date_wise_total;?></strong></td>
						<td><strong><? echo $subcon_pro_qty_date_wise_total;?></strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$subcon_other_grnd_total+=$subcon_other_date_wise_total;
					$subcon_white_grnd_total+=$subcon_white_date_wise_total;
					$subcon_wash_grnd_total+=$subcon_wash_date_wise_total;
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
	            <td width="80"><strong><? echo $subcon_pro_qty_grnd_total;?></strong></td>
	            <td colspan="9" width="80"></td>
	        </tr> 
	     </tfoot>
	    </table>
	    </div>
	    </div>
	    <?
	}	

	if ($order_type==0 || $order_type==3) // Sample Without Order
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
		<table width="1740" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
			<thead>
				<tr>
	                <th rowspan="2" width="30">M/C No</th>
	                <th rowspan="2" width="80">M/C & Capacity</th>
	                <th rowspan="2" width="80">Buyer Name</th>
	                <th rowspan="2" width="80">Batch No.</th>
	                <th rowspan="2" width="80">Batch Color</th>
	                <th rowspan="2" width="80">Booking No.</th>
	                <th rowspan="2" width="80">Color Range</th>
	                <th colspan="5" width="80">Total Production Qty in Kg</th>
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
	                <th width="80">Re-Process</th>
	                <th width="80">Trims Weight</th>
	            </tr> 
			</thead>
		</table>
	    <div style="width:1760px; overflow-y:scroll; max-height:300px;" id="scroll_body">
		<table align="center" cellspacing="0" width="1740"  border="1" rules="all" class="rpt_table" >
			
			<? 
			$sqls="SELECT  a.batch_id, a.batch_ext_no, a.machine_id,a.end_hours, a.end_minutes, a.process_end_date, a.remarks 
			from pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			where a.service_company=$cbo_company_id $location_id $batch_cond $floor_id_cond $machine_cond  
			and a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.load_unload_id=1 
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

		  	// Main query	fabric_sales_order_mst p,
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
			//print_r($sql_dtls);
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
			 $sql_result_sales="SELECT a.id, a.batch_no, a.batch_id, a.batch_ext_no, a.machine_id, a.load_unload_id, a.end_hours, a.end_minutes, a.result, a.remarks,a.production_date, 
			 a.water_flow_meter, b.batch_qty, b.const_composition, c.machine_no, c.prod_capacity, 
			 p.po_buyer as buyer_id, q.color_id, q.booking_no, q.color_range_id, q.total_trims_weight, q.process_id
			 from fabric_sales_order_mst p, pro_batch_create_mst q, pro_fab_subprocess a, pro_fab_subprocess_dtls b, lib_machine_name c 
			 where a.id=b.mst_id and a.machine_id=c.id and a.entry_form=35 and q.entry_form=0 and a.load_unload_id=2 
			 and a.batch_id=q.id and q.sales_order_id=p.id and q.booking_without_order=1 and a.service_company=$cbo_company_id 
			 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $booking_no_cond $location_id $batch_cond $floor_id_cond $machine_cond $sql_cond $buyer_name_cond $batch_color_cond $color_range_cond 
			 order by a.production_date, c.machine_no";

			//echo $sql_result;die;

			$sql_dtls_sales=sql_select($sql_result_sales);
			//print_r($sql_dtls);
			
			foreach ($sql_dtls_sales as $row)
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
					
			/*echo "<pre>";
			print_r($date_data_arr);die;*/

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
			/*echo '<pre>';
			print_r($rowspan_arr);*/

			$i=1;
			$other_grnd_total=$white_grnd_total=$wash_grnd_total=$wo_pro_qty_grnd_total=0;
			foreach ($date_data_arr as $p_date=>$prod_date_data) 
			{
				$other_date_wise_total=$white_date_wise_total=$wash_date_wise_total=$wo_pro_qty_date_wise_total=0;
				?>
				<tr>
					<td colspan="20" style="background-color: #CCCCCC;"><b>Date: <? echo $p_date;?></b></td>
				</tr>
				<?
				foreach ($prod_date_data as $machine_id => $machine_data) 
				{
					$other_mcw_wise_total=$white_mcw_wise_total=$wo_wash_mcw_wise_total=$wo_pro_qty_total=0;
					$r=0;
					foreach ($machine_data as $batch_id => $row) 
					{
						//echo $row['batch_ext_no'];
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						?>
						<tr id="<? echo 'v_'.$i; ?>" onClick="change_color('v_<? echo $i; ?>','#FFFFFF')" bgcolor="<? echo $bgcolor; ?>">
							<?							
								if ($r==0) 
								{
									?>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="30" valign="middle"><?echo $i;?></td>
									<td rowspan="<? if (isset($idle_data_arr[$p_date][$machine_id])) 
									{ 
										echo $rowspan_arr[$p_date][$machine_id]+1;
									}
									else
									{
										echo $rowspan_arr[$p_date][$machine_id];
									}
									?>" width="80" valign="middle"><?echo $row['machine_no'].' - '.$row['prod_capacity'].'kg';?></td>
									<?
								} $r++;
							?>
							<td width="80"><?echo $buyer_library[$row['buyer_id']];?></td>
							<td width="80"><?echo $row['batch_no'];?></td>
							<td width="80"><p><?echo $color_library[$row['color_id']]?></p></td>
							<td width="80"><?echo $row['booking_no'];?></td>
							<td width="80"><?echo $color_range[$row['color_range_id']];?></td>
							<?

							$other_pro_qty=$white_pro_qty=$wo_wash_pro_qty=$pro_qty=0; 
								if ($row['color_range_id']==5 && $row['result']==1)
								{
									$other_pro_qty = $row['batch_qty'];
								} 
							?>
							<td width="80" align="right"><? echo $other_pro_qty; $other_mcw_wise_total+=$other_pro_qty;?></td>	

							<? 
								if ($row['color_range_id']==4 && $row['result']==1)
								{
									$white_pro_qty = $row['batch_qty']; 
								} 
							?> 
							<td width="80" align="right"><? echo $white_pro_qty; $white_mcw_wise_total+=$white_pro_qty;?></td>
							<? 
								if ($row['color_range_id']==7 && $row['result']==1)
								{
									$wo_wash_pro_qty = $row['batch_qty'];
								} 
							?>
							<td width="80" align="right"><? echo $wo_wash_pro_qty; $wo_wash_mcw_wise_total+=$wo_wash_pro_qty;?></td>						
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
						//$i++;
						//$other_mcw_wise_total+=$other_pro_qty;
						//$white_mcw_wise_total+=$white_pro_qty;
						
					}
					if (isset($idle_data_arr[$p_date][$machine_id]['machine'])) // Cause of Machine Idle
					{
						?>
							<tr style="background: #F0BC8F;">
								<td colspan="5">
									<?
									echo $cause_type[$idle_data_arr[$p_date][$machine_id]['machine_idle_cause']];
									?>
								</td>
								<td colspan="5">
									<?
									echo $idle_data_arr[$p_date][$machine_id]['remarks'];
									?>
								</td>
								<td></td>
								<td></td>
								<td>
									<?
									$idle_start_hour = $idle_data_arr[$p_date][$machine_id]['from_hour'];
									$idle_start_minut = $idle_data_arr[$p_date][$machine_id]['from_minute'];
									$hour_minut = $idle_start_hour.':'.$idle_start_minut; 
									$start_hour_minut = date('h:i A', strtotime($hour_minut));
									echo $start_hour_minut;
									?>
								</td>
								<td>
									<?
									$idle_end_hour = $idle_data_arr[$p_date][$machine_id]['to_hour'];
									$idle_end_minut = $idle_data_arr[$p_date][$machine_id]['to_minute'];
									$idle_hour_minut = $idle_end_hour.':'.$idle_end_minut; 
									$end_hour_minut = date('h:i A', strtotime($idle_hour_minut));
									echo $end_hour_minut;
									?>
								</td>
								<td>
									<?
									$from_date = $idle_data_arr[$p_date][$machine_id]['from_date'];
									$to_date = $idle_data_arr[$p_date][$machine_id]['to_date'];
									 
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
							</tr>
						<?	
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Total:</b></td>
						<td><strong><? echo $other_mcw_wise_total;?></strong></td>
						<td><strong><? echo $white_mcw_wise_total;?><strong></td>
						<td><strong><? echo $wo_wash_mcw_wise_total;?><strong></td>
						<td><strong><? echo $wo_pro_qty_total;?><strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$i++;
					$other_date_wise_total+=$other_mcw_wise_total;
					$white_date_wise_total+=$white_mcw_wise_total;
					$wash_date_wise_total+=$wo_wash_mcw_wise_total;
					$wo_pro_qty_date_wise_total+=$wo_pro_qty_total;
				}
					?>
					<tr class="tbl_bottom">
						<td colspan="7" align="right"><b>Date wise Total:</b></td>
						<td><strong><? echo $other_date_wise_total;?></strong></td>
						<td><strong><? echo $white_date_wise_total;?></strong></td>
						<td><strong><? echo $wash_date_wise_total;?></strong></td>
						<td><strong><? echo $wo_pro_qty_date_wise_total;?></strong></td>
						<td colspan="9"></td>
					</tr>
					<?
					$other_grnd_total+=$other_date_wise_total;
					$white_grnd_total+=$white_date_wise_total;
					$wash_grnd_total+=$wash_date_wise_total;
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
	//echo "$total_data####$filename";
	exit();      
}
?>
