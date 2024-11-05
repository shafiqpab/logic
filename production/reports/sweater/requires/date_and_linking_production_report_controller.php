<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{	
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' ","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/date_and_linking_production_report_controller', this.value+'_'+$data, 'load_drop_down_floor', 'floor_td' );" );     	 
	exit();
}
if ($action=="load_drop_down_floor")
{
	$ex_data = explode("_",$data);
	echo create_drop_down( "cbo_floor_id", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$ex_data[0]' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/date_and_linking_production_report_controller', this.value+'_'+$ex_data[0]+'_'+$ex_data[1]+'_'+document.getElementById('txt_date').value, 'load_drop_down_line', 'line_td' );",0 );     	 	
	exit();    	 
}
if ($action=="load_drop_down_line-2")
{
	$explode_data = explode("_",$data);
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$explode_data[2] and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];

	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
		if($txt_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";
			
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");
			}
			else
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_date,'','',1)."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number");	
			}
		}
		
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

		echo create_drop_down( "cbo_floor_id", 120,$line_array,"", 1, "-- Select Line --", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_floor_id", 120, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select Line --", $selected, "",0,0 );
	}
	exit();
}

if ($action=="load_drop_down_line")
{
	$explode_data = explode("_",$data);
	// print_r($explode_data);
	// 2-company>1-location>0-floor
	// rray ( [0] => 448 [1] => 85 [2] => 20 [3] => ) 
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name in($explode_data[2]) and variable_list=23 and is_deleted=0 and status_active=1");
	$txt_date = $explode_data[3];
	
	$cond="";
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		
	
			if( $explode_data[0]!=0 ) $cond .= " and a.location_id in($explode_data[1])";
			if( $explode_data[1]!=0 ) $cond .= " and a.floor_id in($explode_data[0])";
			if( $explode_data[2]!=0 ) $cond .= " and a.company_id in($explode_data[2])";
			if($db_type==0){if($explode_data[3]!=0) $cond .=" and year(a.update_date)=$explode_data[3]"; else $cond.="";}
			else if($db_type==2){if($explode_data[3]!=0) $cond .=" and to_char(b.pr_date,'YYYY')=$explode_data[3]"; else $cond .="";}

			// echo "sselect a.id, a.line_number,b.line_name ,to_char(a.update_date,'YYYY') as year
			// from prod_resource_mst a, lib_sewing_line b where a.line_number=b.id and a.is_deleted=0 and b.is_deleted=0 and a.location_id in(5) and a.floor_id in(12) and a.company_id in(3)  and to_char(a.update_date,'YYYY')='2021' group by a.id, a.line_number,b.line_name,to_char(a.update_date,'YYYY') order by b.line_name";
			$line_data=sql_select( "select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b 
			where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 $cond   group by a.id, a.line_number order by a.id asc");
	
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

		echo create_drop_down( "cbo_line_id", 140,$line_array,"", 1, "-- Select --", $selected, "","",0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name=$explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name=$explode_data[0] and company_name in($explode_data[2])";

		echo create_drop_down( "cbo_line_id", 140, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "-- Select --", $selected, "","load_line();",0 );
		
	}
	exit();
}

if ($action=="print_report_button_setting")
{
	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
		echo $print_report_format; 	
} 

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";






if($action=="report_generate")
{ 
	extract($_REQUEST);
    
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

		$company_id 	= str_replace("'", "", $cbo_company_id);
		$location_id 	= str_replace("'", "", $cbo_location_id);
		$floor_id 	    = str_replace("'", "", $cbo_floor_id);
		$line_id    	= str_replace("'", "", $cbo_line_id);
		$buyer_id 		= str_replace("'", "", $cbo_buyer_id);
		$job_no 		= str_replace("'", "", $txt_job_no);
		$style_ref_no 	= str_replace("'", "", $txt_style_ref_no);
		$hide_job_id 	= str_replace("'", "", $hide_job_id);
		$txt_date 		= str_replace("'", "", $txt_date);  
		$type 			= str_replace("'", "", $type);

	

		$sql_cond = "";
		$sql_cond .= ($company_id!=0) ? " and f.company_name=$company_id" : "";
		$sql_cond .= ($location_id!=0) ? " and a.location=$location_id" : "";
		$sql_cond .= ($floor_id!=0) ?   " and a.floor_id=$floor_id" : "";
		$sql_cond .= ($line_id!=0) ? " and a.sewing_line=$line_id" : "";
		$sql_cond .= ($buyer_id!=0) ? " and f.buyer_name=$buyer_id" : "";
		$sql_cond .= ($hide_job_id!="") ? " and f.id in($hide_job_id)" : "";
		$sql_cond .= ($style_ref_no!="") ? " and f.style_ref_no in('$style_ref_no')" : "";
		$sql_cond .= ($txt_date!="") ? " and a.production_date='$txt_date'" : "";
    



 

    // echo "<pre>";print_r($style_job_array);die();
   

    
			//===================================================================

			// $sql="SELECT c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num,f.job_no,f.id as job_id, to_char(f.insert_date,'YYYY') as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, c.reject_qty, c.alter_qty, c.spot_qty, c.replace_qty, c.is_rescan, e.po_number, c.barcode_no,e.po_quantity,f.gauge,a.production_type from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where  a.id=c.mst_id and c.color_size_break_down_id=d.id 	and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and a.production_type in (56,55,76) and c.status_active=1 and c.is_deleted=0 	and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in (1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  $sql_cond  $style_cond $job_cond order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc ";

			$sql="SELECT c.id as prdid, e.id as po_id, f.job_no_prefix_num,f.job_no,f.id as job_id, to_char(f.insert_date,'YYYY') as year,
			f.buyer_name, f.style_ref_no, c.production_qnty as production_qnty, c.reject_qty, c.alter_qty, c.spot_qty, c.replace_qty, 
			e.po_number, c.barcode_no,a.production_type from pro_garments_production_mst a left join wo_po_break_down e on a.po_break_down_id=e.id join wo_po_details_master f on e.job_no_mst=f.job_no,pro_garments_production_dtls c
			  where a.id=c.mst_id  and   a.production_type in (56,55,76) and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 	 and e.status_active in (1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $sql_cond  $style_cond $job_cond order by f.job_no asc";

			// echo $sql;
			$data_array=sql_select($sql);

			 foreach($data_array as $val){

				 $style_wise_data[$val[csf('style_ref_no')]]['style_ref']=$val[csf('style_ref_no')];
				 $style_wise_data[$val[csf('style_ref_no')]]['job_no']=$val[csf('job_no')];
	
				
				 $style_wise_data[$val[csf('style_ref_no')]]['buyer_name']=$buyer_arr[$val[csf('buyer_name')]];

				 if($val[csf('production_type')]==55){
				
					$style_wise_data[$val[csf('style_ref_no')]]['today_input_qty']+=$val[csf('production_qnty')]-$val[csf('reject_qty')];
				 }elseif($val[csf('production_type')]==56){
					
					$style_wise_data[$val[csf('style_ref_no')]]['today_output_qty']+=$val[csf('production_qnty')]-$val[csf('reject_qty')];
				 }

				 $style_arr[$val[csf('style_ref_no')]]=$val[csf('style_ref_no')];
				 $job_id_arr[$val[csf('job_id')]]=$val[csf('job_id')];
			 }

			 //============================================order query========================================
			 $order_sql="select b.id as po_id, a.job_no_prefix_num,a.job_no,a.id as job_id, to_char(a.insert_date,'YYYY') as year,
			 a.buyer_name, a.style_ref_no,b.po_quantity, a.gauge from wo_po_details_master a ,wo_po_break_down b where a.job_no=b.job_no_mst ".where_con_using_array($job_id_arr,1,'a.id')."
			";
			$order_data=sql_select($order_sql);
			foreach($order_data as $oval){

				$style_wise_data[$oval[csf('style_ref_no')]]['order_qty']+=$oval[csf('po_quantity')];
				$style_wise_data[$oval[csf('style_ref_no')]]['gauge']=$oval[csf('gauge')];
			}
			
			 //============================================order query========================================
			// =========================Linking Intput ===============================


			$input_sql="SELECT c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, to_char(f.insert_date,'YYYY') as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, e.po_number,c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f 
			where a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and a.production_type=55 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 ".where_con_using_array($job_id_arr,1,'f.id')." order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc ";
			$input_data=sql_select($input_sql);

			foreach($input_data as $inval){
				$style_wise_data[$inval[csf('style_ref_no')]]['total_input_qty']+=$inval[csf('production_qnty')]-$inval[csf('reject_qty')];
			}

    	// =========================Linking Output ===============================
   
		$output_sql="SELECT c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, to_char(f.insert_date,'YYYY') as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, c.reject_qty, c.alter_qty, c.spot_qty, c.replace_qty, c.is_rescan, e.po_number, c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where  a.id=c.mst_id and c.color_size_break_down_id=d.id 	 and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and a.production_type=56 and c.status_active=1 and c.is_deleted=0 	and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in (1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 ".where_con_using_array($job_id_arr,1,'f.id')." order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc ";
	
		$output_data=sql_select($output_sql);

			 foreach($output_data as $outval){
				$style_wise_data[$outval[csf('style_ref_no')]]['total_output_qty']+=$outval[csf('production_qnty')]-$outval[csf('reject_qty')];
			 }

		

	
	// =========================Bundle Issue To First Inspection ===========================

	$knitting_sql="SELECT a.serving_company, a.floor_id, c.id as prdid, d.id as colorsizeid, e.id as po_id, f.job_no_prefix_num, to_char(f.insert_date,'YYYY') as year, f.buyer_name, f.style_ref_no, d.item_number_id, d.country_id, d.size_number_id, d.color_number_id, c.cut_no, c.bundle_no, c.barcode_no, c.production_qnty as production_qnty, e.po_number, c.barcode_no from pro_garments_production_mst a,pro_garments_production_dtls c, wo_po_color_size_breakdown d, wo_po_break_down e, wo_po_details_master f where  a.id=c.mst_id and c.color_size_break_down_id=d.id and d.po_break_down_id=e.id and e.job_no_mst=f.job_no  and a.production_type=76 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and d.status_active in(1,2,3) and d.is_deleted=0 and e.status_active in(1,2,3) and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 ".where_con_using_array($job_id_arr,1,'f.id')." order by c.cut_no, length(c.bundle_no) asc, c.bundle_no asc";

	$knitting_data=sql_select($knitting_sql);

	foreach($knitting_data as $knval){
		$style_wise_data[$knval[csf('style_ref_no')]]['knitting_qty']+=$knval[csf('production_qnty')]-$knval[csf('reject_qty')];
	 }


    // echo "<pre>"; print_r($bundle_qty_arr);
	// echo "<pre>";
	// print_r($style_wise_data);

  
	ob_start();
	if($type==1)
	{
		?>
		<fieldset style="width: 1180px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Date and Style wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($txt_date); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1160"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="70">Buyer Name</th>
	             			<th width="100">Job No</th>
	             			<th width="80">Style</th>
	             			<th width="80">Gauge</th>
	             			<th width="80">Order Qty.</th>
	             			<th width="80"> Total Knitting Qty.</th>
	             			<th width="80">Previous Input Qty</th>
	             			<th width="80">Today Input Qty</th>
	             			<th width="80">Total Input</th>
	             			<th width="80">Input Balance From Knitting  Qty</th>
	             			<th width="80">Previous Output Qty</th>
	             			<th width="80">Today Output Qty.</th>
							<th width="80">Total Output</th>
							<th width="80">Output Balance</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:1180px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="1160"  align="center" id="table_body">
		             	<tbody>
		             		<?
		             		$i=1;
		             	
		             		foreach ($style_wise_data as $style_id => $row) 
		             		{		             			
			             		
		             				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		             				// $qc_qty = $bundle_qty_arr[$pdate][$style_job_array[$style]];
		             		
									 $prev_input_qnty=$row['total_input_qty']-$row['today_input_qty'];
									 $prev_output_qnty=$row['total_output_qty']-$row['today_output_qty'];

				             		?>
				             		<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				             			<td width="30"><?=$i;?></td>
				             			<td width="70" align="center"><?=$row['buyer_name'];?></td>
				             			<td width="100"><p><?=$row['job_no'];?></p></td>
				             			<td width="80"><?=$row['style_ref'];?></td>
				             			<td width="80"><?=$row['gauge'];?></td>
				             			<td width="80" align="center"><?=$row['order_qty'];?></td>
				             			<td width="80" align="right"><?=$row['knitting_qty'];?></td>
				             			<td width="80" align="right"><?=$prev_input_qnty;?></td>
				             			<td width="80" align="right"><?=$row['today_input_qty'];?></td>
				             			<td width="80" align="right"><?=$row['total_input_qty'];?></td>
				             			<td width="80" align="right"><?=$row['knitting_qty']-$row['total_input_qty'];?></td>
				             			<td width="80" align="right"><?=$prev_output_qnty;?></td>
				             			<td width="80" align="right"><?=$row['today_output_qty'];?></td>
										 <td width="80" align="right"><?=$row['total_output_qty'];?></td>
				             			<td width="80" align="right"><?=$row['total_input_qty']-$row['total_output_qty'];?></td>
				             		</tr>
				             		<?
									$tot_order_qty+=$row['order_qty'];
									$tot_knitting_qty+=$row['knitting_qty'];
									$tot_prev_input_qnty+=$prev_input_qnty;
									$tot_today_input_qty+=$row['today_input_qty'];
									$tot_total_input_qty+=$row['total_input_qty'];
									$tot_balance_input_qty+=$row['knitting_qty']-$row['total_input_qty'];
									$tot_prev_output_qnty+=$prev_output_qnty;
									$tot_today_output_qty+=$row['today_output_qty'];
									$tot_total_output_qty+=$row['total_output_qty'];
									$tot_balance_output_qty+=$row['total_input_qty']-$row['total_output_qty'];
							
				             		$i++;
				            }
			             
				            ?>
		             	</tbody>						 
		            </table>	 
					                   	
	            </div>
				<table width="1160">	
					<tfoot>
						 <tr style="background: #cddcdc;font-weight: bold;text-align: right;">
			             			<td width="30"></td>
			             			<td width="70"></td>
			             			<td width="100"></td>
			             			<td width="80"></td>
			             			<td width="80" >Total</td>
			             			<td width="80" id="order_qty"><?=number_format($tot_order_qty,0); ?></td>
			             			<td width="80" id="knitting_qty"><?=number_format($tot_knitting_qty,0); ?></td>
			             			<td width="80" id="prev_input_qty"><?=number_format($tot_prev_input_qnty,0); ?></td>
			             			<td width="80" id="today_input_qty"><?=number_format($tot_today_input_qty,0); ?></td>
			             			<td width="80" id="total_input_qty"><?=number_format($tot_total_input_qty,0); ?></td>
			             			<td width="80" id="balance_input_qty"><?=number_format($tot_balance_input_qty,2); ?></td>
			             			<td width="80" id="prev_output_qty"><?=number_format($tot_prev_output_qnty,0); ?></td>
			             			<td width="80" id="today_output_qty"><?=number_format($tot_today_output_qty,2); ?></td>
									 <td width="80" id="total_output_qty"><?=number_format($tot_total_output_qty,0); ?></td>
			             			<td width="80" id="balance_output_qty"><?=number_format($tot_balance_output_qty,2); ?></td>
			             		</tr>
						</tfoot>
		            </table>	  

			</div>
		</fieldset>
		<?
	}

	$particular_name = implode(',', $particular_name_arr);
	$particular_value = implode(',', $fparticular_value_arr);
	
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$type####".implode("__",$style_name_arr)."####".implode("__",$style_total_defect)."####".implode("__",$style_total_reject)."####".implode("__",$style_total_efii);
	exit(); 
}

if ($action == "job_no_search_popup") 
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');

	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {
			

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th> Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref",3=> "Lot Ratio No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'date_and_linking_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_no_search_list_view") 
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	$company_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}else{
		$company_con=" and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
		else if($search_by == 3)
			$search_field = " and c.cut_num_prefix_no like ".$search_string;
	}
	

	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   // $year_cond = "";
    } //defined Later
    
  
  	if($search_by == 3)
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b,ppl_cut_lay_mst c where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";
  	}
  	else
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and 
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name order by job_no";

  	}

	

	// echo $sql;

	$conclick="id,job_no";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no";
	}

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}