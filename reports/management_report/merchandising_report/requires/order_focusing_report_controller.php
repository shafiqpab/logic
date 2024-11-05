<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
	
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/order_focusing_report_controller', this.value, 'load_drop_down_season', 'season_td');" );   	 
} 
if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season", 70, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}
if ($action=="week_date")
{
	$data=explode("_",$data);
	$sql_week_start_end_date=sql_select("select week, min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week=$data[0] and year= $data[1] group by week");
	$week_start_day=0;
	$week_end_day=0;
	foreach ($sql_week_start_end_date as $row_week_week_start_end_date)
	{
		$week_start_day=$row_week_week_start_end_date[csf("week_start_day")];
		$week_end_day=$row_week_week_start_end_date[csf("week_end_day")];
	}
	echo change_date_format($week_start_day,"dd-mm-yyyy",'-')."_".change_date_format($week_end_day,"dd-mm-yyyy",'-');
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";





if($action=="generate_report")
{ 
    $process = array( &$_POST );

   // print_r($process);die;
    extract(check_magic_quote_gpc( $process ));
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

	$season_name_arr=return_library_array( "select id,season_name from lib_buyer_season ",'id','season_name');
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

	
//echo $txt_int_ref.'ddsds';
	$job_cond_id=""; 
	$style_cond="";
	$order_cond="";
	$type=str_replace("'","",$type);
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";

	$cbo_string_search_type=str_replace("'", "", $cbo_string_search_type);
	$cbo_date_type=str_replace("'", "", $cbo_date_type);
	$cbo_company_name=str_replace("'", "", $cbo_company_name);
	$txt_job_no=str_replace("'", "", $txt_job_no);
	$cbo_year=str_replace("'", "", $cbo_year);
	$txt_style_ref=str_replace("'", "", $txt_style_ref);
	$txt_int_ref=str_replace("'", "", $txt_int_ref);
	$txt_week=str_replace("'", "", $txt_week);
	$cbo_season=str_replace("'", "", $cbo_season);
	 
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		 $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

	//if (str_replace("'","",$txt_job_no)=="") $job_cond_id=""; else $job_cond_id="and a.job_no_prefix_num = '".str_replace("'","",$txt_job_no)."'";

	//if (empty($txt_style_ref)) $txt_style_ref_cond=""; else $txt_style_ref_cond="and a.style_ref_no = '".$txt_style_ref."' ";
	//if(empty($txt_int_ref)) $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping = '$txt_int_ref' ";
	
	if(empty($cbo_season)) $season_buyer_wise=""; else $season_buyer_wise=" and a.season_buyer_wise = '$cbo_season' ";
	
	 if($cbo_string_search_type==1)
	{
		//if(str_replace("'","",trim($txt_job_no))!="") $search_string="".str_replace("'","",trim($txt_job_no)).""; else $search_string="%%";
		if(str_replace("'","",trim($txt_job_no))!="") $job_cond=" and LOWER(a.job_no_prefix_num) = LOWER('".str_replace("'","",trim($txt_job_no))."')"; else $job_cond="";
		
		if(str_replace("'","",trim($txt_style_ref))!="") $style_no_cond=" and LOWER(a.style_ref_no) = LOWER('".str_replace("'","",trim($txt_style_ref))."')"; else $style_no_cond="";
		if(str_replace("'","",trim($txt_int_ref))!="") $ref_no_cond=" and LOWER(b.grouping) = LOWER('".str_replace("'","",trim($txt_int_ref))."')"; else $ref_no_cond="";
	}
	else if($cbo_string_search_type==4 || $cbo_string_search_type==0)
	{
		if(str_replace("'","",trim($txt_job_no))!="") $job_cond=" and LOWER(a.job_no_prefix_num) like LOWER('%".str_replace("'","",trim($txt_job_no))."%')"; else $job_cond="";
		
		if(str_replace("'","",trim($txt_style_ref))!="") $style_no_cond=" and LOWER(a.style_ref_no) like LOWER('%".str_replace("'","",trim($txt_file_no))."%')"; else $style_no_cond="";
		if(str_replace("'","",trim($txt_int_ref))!="") $ref_no_cond=" and LOWER(b.grouping) like LOWER('%".str_replace("'","",trim($txt_int_ref))."%')"; else $ref_no_cond="";
	}
	else if($cbo_string_search_type==2)
	{
		if(str_replace("'","",trim($txt_job_no))!="") $job_cond=" and LOWER(a.job_no_prefix_num) like LOWER('".str_replace("'","",trim($txt_job_no))."%')"; else $job_cond="";
		
		if(str_replace("'","",trim($txt_style_ref))!="") $style_no_cond=" and LOWER(a.style_ref_no) like LOWER('".str_replace("'","",trim($txt_style_ref))."%')"; else $style_no_cond="";
		if(str_replace("'","",trim($txt_int_ref))!="") $ref_no_cond=" and LOWER(b.grouping) like LOWER('".str_replace("'","",trim($txt_int_ref))."%')"; else $ref_no_cond="";
	}
	else if($cbo_string_search_type==3)
	{
		if(str_replace("'","",trim($txt_job_no))!="") $job_cond=" and LOWER(a.job_no_prefix_num) like LOWER('%".str_replace("'","",trim($txt_job_no))."')"; else $job_cond="";
		if(str_replace("'","",trim($txt_style_ref))!="") $style_no_cond=" and LOWER(a.style_ref_no) like LOWER('%".str_replace("'","",trim($txt_style_ref))."')"; else $style_no_cond="";
		if(str_replace("'","",trim($txt_int_ref))!="") $ref_no_cond=" and LOWER(b.grouping) like LOWER('%".str_replace("'","",trim($txt_int_ref))."')"; else $ref_no_cond="";
	}
	
	
	$working_company_cond="";
	$company_cond="";

	if(str_replace("'","",$cbo_working_company)>0)
	{
		$working_company_cond=" and d.working_company_id=$cbo_working_company";
	}
	if(($cbo_company_name)>0)
	{
		$company_cond=" and d.company_id=$cbo_company_name ";
	}
	 $date_cond="";
	if(trim($txt_date_from)=="" || trim($txt_date_to)=="") $date_cond="";
	else $date_cond=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
	

	
	 
	
	
  	if($type==1)
  	{


		 $po_number_data=array();
		 $production_data_arr=array();
		 $po_number_id=array();

	 	 $week_for_header=array();$no_of_week_for_header=array();
	 	if(trim($txt_date_from)=="" || trim($txt_date_to)=="")
	 	{
	 		$sql_week_header=sql_select("select week_date,week from week_of_year");
	 	}
	 	else {
	 		$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$txt_date_from' and  '$txt_date_to'");
	 	}
		
		foreach ($sql_week_header as $row_week_header)
		{
			$week_for_header[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		}
			

			 
			
		    $year_cond="";
			//$cbo_year=str_replace("'","",$cbo_year_selection);
			if(trim($cbo_year)!=0)
			{
				if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
				else $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}

			if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";

			$sql="SELECT a.buyer_name,
			         a.product_dept,
			         a.job_no_prefix_num,
			         $select_job_year,
			         b.grouping,
			         a.style_ref_no,
			         c.item_number_id,
			         SUM (d.fin_fab_qnty) AS fin_fab_qnty,
			         SUM (d.grey_fab_qnty) AS grey_fab_qnty,
			         sum(c.order_quantity) as order_qnty,
			         d.construction,
			         b.po_number,
			         b.pub_shipment_date,
			         a.season_buyer_wise as season,
			         a.set_smv,
			         d.booking_no,
			         b.id,
			         a.id as mst_id
			    FROM wo_po_details_master a,
			         wo_po_break_down b,
			         wo_po_color_size_breakdown c,
			         wo_booking_dtls d
			   WHERE     a.job_no = b.job_no_mst
			         AND a.job_no = d.job_no
			         AND a.job_no = c.job_no_mst
			         AND b.id = d.po_break_down_id
			         AND c.po_break_down_id = b.id
			         AND c.id = d.color_size_table_id
			         AND a.status_active = 1
			         AND a.is_deleted = 0
			         AND b.status_active = 1
			         AND b.is_deleted = 0
			         AND c.status_active = 1
			         AND c.is_deleted = 0
			         AND d.status_active = 1
			         AND d.is_deleted = 0
			         and d.booking_type=1
			         and d.is_short=2
			         and a.company_name in ($cbo_company_name)
			         $date_cond $buyer_id_cond  $job_cond $style_no_cond $ref_no_cond $season_buyer_wise $year_cond
			GROUP BY  a.buyer_name,
			         a.product_dept,
			         a.job_no_prefix_num,
			         a.insert_date,
			         b.grouping,
			         a.style_ref_no,
			         c.item_number_id,
			         d.construction,
			         b.po_number,
			         b.pub_shipment_date,
			         a.season_buyer_wise,
			         a.set_smv,
			         d.booking_no,
			         b.id,
			         a.id
			order by a.id";

    		// echo $sql;
			$result_sql_budget=sql_select($sql); 
			$construction_arr=array();
			$po_wise_data=array();
			$po_construction_data=array();
			$po_id_arr=array();
			$booking_no_arr=array();
			foreach ($result_sql_budget as $row) 
			{
				array_push($po_id_arr, $row[csf('id')]);
				array_push($booking_no_arr, $row[csf('booking_no')]);
				$construction=trim($row[csf('construction')]);
				array_push($construction_arr, $construction);
				$po_wise_data[$row[csf('id')]]['buyer_name'].=$buyer_arr[$row[csf('buyer_name')]]."***";
				$po_wise_data[$row[csf('id')]]['product_dept'].=$product_dept[$row[csf('product_dept')]]."***";
				$po_wise_data[$row[csf('id')]]['job_no_prefix_num'].=$row[csf('job_no_prefix_num')]."***";
				$po_wise_data[$row[csf('id')]]['job_year'].=$row[csf('job_year')]."***";
				$po_wise_data[$row[csf('id')]]['grouping'].=$row[csf('grouping')]."***";
				$po_wise_data[$row[csf('id')]]['style_ref_no'].=$row[csf('style_ref_no')]."***";
				$po_wise_data[$row[csf('id')]]['item_number_id'].=$garments_item[$row[csf('item_number_id')]]."***";
				$po_wise_data[$row[csf('id')]]['po_number'].=$row[csf('po_number')]."***";
				if(!empty($row[csf('pub_shipment_date')]))
				{
					$po_wise_data[$row[csf('id')]]['pub_shipment_date'].=change_date_format($row[csf('pub_shipment_date')])."***";
				}
				 $week_de= $no_of_week_for_header[$row[csf("pub_shipment_date")]];
	            if( date('l', strtotime($row[csf("pub_shipment_date")]))=='Sunday' && $week_pad==1){
	              $week_de=$week_de+1;
	            }
	           
	            $po_wise_data[$row[csf('id')]]['week_de']= $week_de;
				
				$po_wise_data[$row[csf('id')]]['season'].=$season_name_arr[$row[csf('season')]]."***";
				$po_wise_data[$row[csf('id')]]['set_smv']=$row[csf('set_smv')];
				$po_wise_data[$row[csf('id')]]['order_qnty']+=$row[csf('order_qnty')];
				$po_wise_data[$row[csf('id')]]['booking_no'].=$row[csf('booking_no')]."***";
				
				$po_construction_data[$row[csf('id')]][$construction]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
				$po_construction_data[$row[csf('id')]][$construction]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
				
			}
			$po_cond=where_con_using_array(array_unique($po_id_arr),0,"po_break_down_id");
			$sql_act="select po_break_down_id, acc_po_no from wo_po_acc_po_info where  status_active=1 and is_deleted=0 $po_cond";
			//echo $sql_act;
			$res_act=sql_select($sql_act);
			$actual_po_no_arr=array();
			foreach ($res_act as $row) 
			{
				$actual_po_no_arr[$row[csf('po_break_down_id')]].=$row[csf('acc_po_no')]."***";
			}



			$booking_cond=where_con_using_array(array_unique($booking_no_arr),1,"a.booking_no");
			$sql_booking="select a.booking_no,a.item_category, a.fabric_source,a.is_approved,a.entry_form,listagg(cast(b.po_break_down_id as varchar2(4000)),',') within group (order by b.po_break_down_id) AS po_id,
			listagg(cast(b.job_no as varchar2(4000)),',') within group (order by b.job_no) AS job_no from wo_booking_mst a , wo_booking_dtls b  where a.booking_no=b.booking_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $booking_cond group by a.booking_no,a.item_category, a.fabric_source,a.is_approved,a.entry_form";
			//echo $sql_act;
			$res_booking=sql_select($sql_booking);
			$booking_info_arr=array();
			foreach ($res_booking as $row) 
			{
				$booking_info_arr[$row[csf('booking_no')]]['item_category']=$row[csf('item_category')];
				$booking_info_arr[$row[csf('booking_no')]]['fabric_source']=$row[csf('fabric_source')];
				$booking_info_arr[$row[csf('booking_no')]]['is_approved']=$row[csf('is_approved')];
				$booking_info_arr[$row[csf('booking_no')]]['entry_form']=$row[csf('entry_form')];
				$booking_info_arr[$row[csf('booking_no')]]['po_id']=$row[csf('po_id')];
				$booking_info_arr[$row[csf('booking_no')]]['job_no']=$row[csf('job_no')];
			}

			$po_cond=str_replace("po_break_down_id", "id", $po_cond);
			$sql_po_qty="select id, po_quantity,job_no_mst,plan_cut from wo_po_break_down where  status_active=1 and is_deleted=0 $po_cond";
			//echo $sql_act;
			$res_po_qty=sql_select($sql_po_qty);
			$po_qnty_arr=array();
			$job_no_arr=array();
			foreach ($res_po_qty as $row) 
			{
				$po_qnty_arr[$row[csf('id')]]['po_quantity']+=$row[csf('po_quantity')];
				$po_qnty_arr[$row[csf('id')]]['plan_cut']+=$row[csf('plan_cut')];
				$job_no_arr[$row[csf('id')]]=$row[csf('job_no_mst')];
			}
			$construction_arr=array_filter($construction_arr);
			$construction_arr=array_unique($construction_arr);
			$com_arr=explode(",", $cbo_company_name);
			$company_name_str='';
			foreach ($com_arr as $key => $value) {
				$company_name_str.=$company_arr[$value].",";
			}

			$width=1550+count($construction_arr)*100*3;

			$print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_name." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
		   $format_ids=explode(",",$print_report_format_ids);
			ob_start();
		 ?>
		 <style type="text/css">
		 	hr {
			  border-top: 1px solid black;
			}
		 </style>
  		<fieldset style="width:<?=$width+50?>px;">
        	   <table  cellspacing="0" style="justify-content: center;text-align: center;width: <?=$width+20?>px;" >
                    <tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none;font-size:14px; font-weight:bold" ><?php echo $report_title; ?></td>
                    </tr>
                    <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none; font-size:16px; font-weight:bold">
                            Company Name:<? echo chop($company_name_str,","); ?>                                
                           </td>
                     </tr>
                     <tr style="border:none;justify-content: center;text-align: center;">
                           <td colspan="29" align="center" style="border:none;font-size:12px; font-weight:bold">
                            <? echo "Production date ".change_date_format(str_replace("'","",$txt_date_from))." to ". change_date_format(str_replace("'","",$txt_date_to)) ;?>
                           </td>
                     </tr>
              </table>
             <br />	

                    <table cellspacing="0" border="1" class="rpt_table"  width="<?=$width?>" rules="all"   >
                    	  <thead>
             				<tr>
             					<th width="40"  rowspan="2"  style="word-break: break-all;">SL</th>
             					<th width="110" rowspan="2" style="word-break: break-all;">Buyer Name</th>
             					<th width="110" rowspan="2" style="word-break: break-all;">Department</th>
             					<th width="70" rowspan="2" style="word-break: break-all;">Job No</th>
             					<th width="70" rowspan="2" style="word-break: break-all;">Job Year</th>
             					<th width="110" rowspan="2" style="word-break: break-all;">Internal Ref</th>
             					<th width="110" rowspan="2" style="word-break: break-all;" >Style</th>
             					<th width="110" rowspan="2" style="word-break: break-all;">Order No</th>
             					<th width="110" rowspan="2" style="word-break: break-all;">Actual Order No</th>
             					<th width="110" rowspan="2" style="word-break: break-all;">Garments Item</th>
             					<th width="70" rowspan="2" style="word-break: break-all;">Pub. Ship<br>Date</th>
             					<th width="60" rowspan="2" style="word-break: break-all;">Week</th>
             					<th width="60" rowspan="2" style="word-break: break-all;">Season</th>
             					<th width="80" rowspan="2" style="word-break: break-all;">Order Qty [Pcs]</th>
             					<th width="100" rowspan="2" style="word-break: break-all;">Total Plan Cut<br>Qty(Pcs)</th>
             					<th width="80" rowspan="2" style="word-break: break-all;">SMV</th>
             					<th width="115" rowspan="2" style="word-break: break-all;">Fabric Booking No</th>
             					
             					

             					<th colspan="<?=count($construction_arr);?>" width="<?=count($construction_arr)*100;?>" style="word-break: break-all;">Fabric Consumption Dzn</th>
             					<th colspan="<?=count($construction_arr);?>" width="<?=count($construction_arr)*100;?>" style="word-break: break-all;">Fabric Total Qty [Kg] Finish</th>
             					<th colspan="<?=count($construction_arr);?>" width="<?=count($construction_arr)*100;?>" style="word-break: break-all;">Fabric Total Qty [Kg] Grey</th>

             					
             				</tr>
             				<tr >
		                       

		                     
		                      
		                      

		                       <?php foreach ($construction_arr as $construction): ?>
		                       		 <th width="100" style="word-break: break-all;"><?php echo $construction; ?></th>
		                       <?php endforeach ?>

		                         <?php foreach ($construction_arr as $construction): ?>
		                       		 <th width="100" style="word-break: break-all;"><?php echo $construction; ?></th>
		                       <?php endforeach ?>

		                         <?php foreach ($construction_arr as $construction): ?>
		                       		 <th width="100" style="word-break: break-all;"><?php echo $construction; ?></th>
		                       <?php endforeach ?>

		                      
		                       
		                      
		                    </tr>
             			</thead>
             		</table>
             	<div style="width:<?=$width+50?>px;max-height: 400px;overflow-y: auto;overflow-x: hidden;" id="overflow_div">
             		<table    id="scroll_body" cellspacing="0" border="1" class="rpt_table"  width="<?=$width?>" rules="all">
		                	
                    	
		                <tbody >

		                	
                    	
							
		                	<?
		                				$i=1;
		                				$construction_wise_summary=array();
		                				$total_order_qnty=0;
		                				foreach ($po_wise_data as $po_id => $row) 
		                				{
		                					
		                					
		                					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                					   $booking_no=implode(", ", array_unique(explode("***", chop($row['booking_no'],"***"))));
		                					   $row_id=$format_ids[0];
		                					   $all_po_id=implode(",", array_unique(explode(",", $booking_info_arr[$booking_no]['po_id'])));
		                					   $all_job_no=implode(",", array_unique(explode(",", $booking_info_arr[$booking_no]['job_no'])));
		                					   $item_category=$booking_info_arr[$booking_no]['item_category'];
		                					   $entry_form=$booking_info_arr[$booking_no]['entry_form'];
		                					   $fabric_source=$booking_info_arr[$booking_no]['fabric_source'];
		                					   $is_approved=$booking_info_arr[$booking_no]['is_approved'];
		                					   $fabric_nature=$item_category;
		                					   $variable=$booking_no;
		                					   if($format_ids[0]==719)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report16','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}else if($format_ids[0]==274)
		                						{
		                						
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','print_booking_10','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==1)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report_gr','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==2)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==3)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report3','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==4)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report1','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==5)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report2','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==6)
		                						{
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report4','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==7)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report5','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==45)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else  if($format_ids[0]==53)
		                						{
		                							

		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report_jk','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==93)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report_libas','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==73)
		                						{
		                							

		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report_mf','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==85)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','print_booking_3','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==143)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report_urmi','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==220)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','print_booking_northern_new','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==160)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                						else if($format_ids[0]==269)
		                						{
		                							
		                							$variable="<a href='#' onClick=\"generate_worder_report('".$booking_no."','".$cbo_company_name."','".$all_po_id."','".$item_category."','".$fabric_source."','".$all_job_no."','".$is_approved."','".$row_id."','".$entry_form."','show_fabric_booking_report_knit','".$i."',".$fabric_nature.")\"> ".$booking_no."<a/>";
		                						}
		                			
		                					?>
		                						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
		                							
	        						                <td width="40"><? echo $i; ?></td>
	        						                <td width="110" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['buyer_name'],"***")))); ?></p></td>
	        						                <td width="110" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['product_dept'],"***")))); ?></p></td>
	        						                <td width="70" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['job_no_prefix_num'],"***")))); ?></p></td>
	        						                <td width="70" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['job_year'],"***")))); ?></p></td>
	        						                <td width="110" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['grouping'],"***")))); ?></p></td>
	        						                <td width="110" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['style_ref_no'],"***")))); ?></p></td>
	        						                <td width="110" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['po_number'],"***")))); ?></p></td>
	        						                <td width="110" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($actual_po_no_arr[$po_id],"***")))); ?></p></td>
	        						                <td width="110" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['item_number_id'],"***")))); ?></p></td>
	        						                <td width="70" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['pub_shipment_date'],"***")))); ?></p></td>
	        						                <td width="60" style="word-break: break-all;"><p><? echo $row['week_de']; ?></p></td>
	        						                <td width="60" style="word-break: break-all;"><p><? echo implode(", ", array_unique(explode("***", chop($row['season'],"***")))); ?></p></td>
	        						                <td width="80" style="word-break: break-all;" align="right"><p><? echo fn_number_format($po_qnty_arr[$po_id]['po_quantity']); ?></p></td>
	        						                <td width="100" style="word-break: break-all;" align="right"><p><? echo fn_number_format($po_qnty_arr[$po_id]['plan_cut']); ?></p></td>
	        						                <td width="80" style="word-break: break-all;" align="right"><p><? echo fn_number_format($row['set_smv'],2); ?></p></td>
	        						                <td width="115" style="word-break: break-all;" align="center"><?=$variable?></td>
	        						                
	        						             
	        						              
	        						                     
        						                     <?

        						                    
		                							$total_order_qnty+=fn_number_format($po_qnty_arr[$po_id]['po_quantity'],2,".","");
		                							$total_plan_cut+=fn_number_format($po_qnty_arr[$po_id]['plan_cut'],2,".","");

							                     	foreach ($construction_arr as $construction) 
							                     	{
							                     		?>
							                     		<td width="100" align="right" style="word-break: break-all;"><p><? echo fn_number_format(($po_construction_data[$po_id][$construction]['fin_fab_qnty']/$po_qnty_arr[$po_id]['plan_cut'])*12,3);?></p></td>
							                     		<?
							                     	}
							                     	foreach ($construction_arr as $construction) 
							                     	{
							                     		?>
							                     		<td width="100" align="right" style="word-break: break-all;"><p><? echo fn_number_format($po_construction_data[$po_id][$construction]['fin_fab_qnty'],2,".",",");?></p></td>
							                     		<?
							                     		$construction_wise_summary[$construction]['fin_fab_qnty']+=fn_number_format($po_construction_data[$po_id][$construction]['fin_fab_qnty'],2,".","");
							                     	}
							                     	foreach ($construction_arr as $construction) 
							                     	{
							                     		?>
							                     		<td width="100" align="right" style="word-break: break-all;"><p><? echo fn_number_format($po_construction_data[$po_id][$construction]['grey_fab_qnty'],2,".",",");?></p></td>
							                     		<?
							                     		$construction_wise_summary[$construction]['grey_fab_qnty']+=fn_number_format($po_construction_data[$po_id][$construction]['grey_fab_qnty'],2,".","");
							                     	}
							                     	?>
							                    </tr>
							                     	<?
							                    $i++;

		                				}
		                			


		                	?>
	                  			

								

	                    </tbody>
	                </table>
	            </div>
	                <table cellspacing="0" border="1" class="rpt_table"  width="<?=$width?>" rules="all">
	                   
	                     
	                    <tfoot>
        						<tr >
        							
						            <td width="40"    style="word-break: break-all;"></td>
	             					<td width="110"  style="word-break: break-all;"></td>
	             					<td width="110" style="word-break: break-all;"></td>
	             					<td width="70"  style="word-break: break-all;"></td>
	             					<td width="70"  style="word-break: break-all;"></td>
	             					<td width="110"  style="word-break: break-all;"></td>
	             					<td width="110" style="word-break: break-all;" ></td>
	             					<td width="110"  style="word-break: break-all;"></td>
	             					<td width="110"  style="word-break: break-all;"></td>
	             					<td width="110"  style="word-break: break-all;"></td>
	             					<td width="70"  style="word-break: break-all;"></td>
	             					<td width="60" style="word-break: break-all;"></td>
	             					<td width="60"  style="word-break: break-all;" align="right"> Total</td>
             					
             					
					                
					                <td width="80" style="word-break: break-all;" align="right" id="total_order_qnty"><p><? echo fn_number_format($total_order_qnty); ?></p></td>
					                <td width="100" style="word-break: break-all;" align="right" id="total_plan_cut"><p><? echo fn_number_format($total_plan_cut); ?></p></td>
					                <td width="80"  style="word-break: break-all;"></td>
             						<td width="115"  style="word-break: break-all;"></td>
					                   
				                     <?
				                     $sl=1;
				                     foreach ($construction_arr as $construction) 
			                     	{
			                     		?>
			                     		<td  width="100" align="right" style="word-break: break-all;"></td>
			                     		<?
			                     		
			                     	}
				                 
			                     	foreach ($construction_arr as $construction) 
			                     	{
			                     		?>
			                     		<td id="fin_<?=$sl?>" width="100" align="right" style="word-break: break-all;"><p><? echo fn_number_format($construction_wise_summary[$construction]['fin_fab_qnty'],2);?></p></td>
			                     		<?
			                     		$sl++;
			                     	}
			                     	$sl=1;
			                     	foreach ($construction_arr as $construction) 
			                     	{
			                     		?>
			                     		<td id="grey_<?=$sl?>" width="100" align="right" style="word-break: break-all;"><p><? echo fn_number_format($construction_wise_summary[$construction]['grey_fab_qnty'],2);?></p></td>
			                     		<?
			                     		$sl++;
			                     	}
			                     	?>
			                    </tr>
	                    </tfoot>     
									    
	                </table> 
	               

	              
	  		</fieldset>
	  		
	 		<?	

		foreach (glob("$user_id*.xls") as $filename) 
		{
			if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
		}
		//---------end------------//
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$contents.= ob_get_flush();
		$is_created = fwrite($create_new_doc,$contents);
		//$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename####".count($construction_arr);
		exit(); 
	}
	
}




