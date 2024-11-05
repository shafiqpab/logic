<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from   lib_country", "id", "country_name");
$country_code_arr=return_library_array( "select id, short_name from   lib_country", "id", "short_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

// ================================Print button ==============================


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 order by a.buyer_name","id,buyer_name", 1, "-- Select buyer --", 0, "","" );//load_drop_down( 'requires/daily_knitting_production_report_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_machine', 'machine_td' );$location_cond
  exit();	 
}

if ($action=="load_drop_down_location")
{	
	$data=str_replace("'", "", $data);
	echo create_drop_down( "cbo_location_name", 120, "SELECT id,location_name from lib_location where company_id in($data) and  status_active =1 and is_deleted=0  group by id,location_name  order by location_name","id,location_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/packing_and_finishing_wip_report_controller',this.value, 'load_drop_down_floor', 'floor_td' )" );  
	
	exit();
}

if ($action=="load_drop_down_floor")  //document.getElementById('cbo_floor').value
{ 
	echo create_drop_down( "cbo_floor_name", 120, "select id,floor_name from lib_prod_floor where company_id =$data and production_process in(4,11)and status_active =1 and is_deleted=0 order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 ); 
	
	exit();    	 
}

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";

if($action=="job_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
		  {
			//alert(id);
			document.getElementById('selected_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?

	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
	if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
	$job_year_cond="";
	if($cbo_year!=0)
	{
	if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
	}
	if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	
	if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number"; 
	else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

	$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company $buyer_name $year_cond $job_cond and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date ";	


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Style</th>
	            <th width="">Po number</th>
	            
	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('job_no')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('style_ref_no')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>
			
		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	disconnect($con);
	exit();
}//JobNumberShow


if($action=="int_ref_wise_search")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode('_',$data);
	// $report_type=$data[3];
	// print_r($data);
	//echo $batch_type."AAZZZ";
	?>
	<script type="text/javascript">
	  function js_set_value(id)
		  {
			//alert(id);
			document.getElementById('selected_id').value=id;
			  parent.emailwindow.hide();
		  }
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
	    else  if (str_replace("'","",$job_no)!="") $job_cond="and b.job_no_mst='".$job_no."'";
		if($buyer==0) $buyer_name=""; else $buyer_name="and a.buyer_name=$buyer";
		$job_year_cond="";
		if($cbo_year!=0)
		{
		if($db_type==0) $job_year_cond=" and SUBSTRING_INDEX(b.insert_date, '-', 1)=".str_replace("'","",$cbo_year)." ";
	    if($db_type==2) $job_year_cond=" and extract( year from b.insert_date)=".str_replace("'","",$cbo_year)."";
		}
		if($db_type==0) $year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		
		if($db_type==2) $group_field="LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.po_number) as po_number"; 
		else if($db_type==0) $group_field="group_concat(distinct b.po_number ) as po_number";

		$sql="select a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num as job_prefix, b.grouping ,$year_field,$group_field from wo_po_details_master a,wo_po_break_down b where b.job_no_mst=a.job_no and a.company_name=$company $buyer_name $year_cond $job_cond and a.is_deleted=0 group by  a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,a.insert_date,grouping ";	
		//echo $sql;die;


	//$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

	?>
	<table width="500" border="1" rules="all" class="rpt_table">
		<thead>
	        <tr>
	            <th width="30">SL</th>
	             <th width="40">Year</th>
	             <th width="50">Job no</th>
	            <th width="100">Int.Ref.No</th>
	            <th width="">Po number</th>
	            
	        </tr>
	   </thead>
	</table>
	<div style="max-height:300px; overflow:auto;">
	<table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
	 <? $rows=sql_select($sql);
		 $i=1;
		 foreach($rows as $data)
		 {
			 	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$po_num=implode(",",array_unique(explode(",",$data[csf('po_number')])));
	  ?>
		<tr bgcolor="<? echo  $bgcolor;?>" onclick="js_set_value('<? echo $data[csf('id')]; ?>'+'_'+'<? echo $data[csf('grouping')]; ?>')" style="cursor:pointer;">
			<td width="30"><? echo $i; ?></td>
	        <td align="center" width="40"><p><? echo $data[csf('year')]; ?></p></td>
			<td align="center"  width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
			<td width="100"><p><? echo $data[csf('grouping')]; ?></p></td>
	        <td width=""><p><? echo $po_num; ?></p></td>
			
		</tr>
	    <? $i++; } ?>
	</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	disconnect($con);
	exit();
}//JobNumberShow






if($action=="generate_report")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor","id","floor_name");  

	

	$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');

	$production_floor =return_library_array( "select id, floor_name from lib_prod_floor where status_active=1 and is_deleted=0",'id','floor_name');

	$garments_item = return_library_array("select id,item_name from  lib_garment_item where status_active=1 and is_deleted=0 order by item_name", "id", "item_name");

	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

    $colorname_arr=return_library_array( "select id, color_name from  lib_color", "id", "color_name"  );

	$rpt_type=str_replace("'","",$type);
	$job_cond_id=""; 

	$int_ref_cond="";

	$txt_production_date=str_replace("'","",$txt_production_date);

	
   	if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and b.company_name=".str_replace("'","",$cbo_company_name)."";
	if(str_replace("'","",$cbo_buyer_name)==0)  $buyer_name=""; else $buyer_name="and b.buyer_name=".str_replace("'","",$cbo_buyer_name)."";

	if(str_replace("'","",$cbo_floor_name)==0)  $floor_name=""; else $floor_name="and d.floor_id=".str_replace("'","",$cbo_floor_name)."";
	


	if(str_replace("'","",$hidden_job_id)!="") { $job_cond_id="and b.id in(".str_replace("'","",$hidden_job_id).")";}

	else if (str_replace("'","",$txt_job_no)!="") { $job_cond_id=" and b.job_no=$txt_job_no $job_year_cond  "; }

	else  $job_cond_id=" $job_year_cond  ";

	if(str_replace("'","",$hidden_int_ref_id)!="")  $int_ref_cond="and b.id in(".str_replace("'","",$hidden_int_ref_id).")";

	else  if (str_replace("'","",$txt_int_ref_no)=="") $style_cond=""; else $style_cond="and a.grouping='".$txt_int_ref_no."'";
	
	$sql_cond="";$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	
	if ($txt_int_ref_no!="") $sql_cond.=" and a.grouping='$txt_int_ref_no'";
	
	

	
	
	if(str_replace("'","",$txt_production_date) !=""){$prod_date_cond=" and d.production_date=$txt_production_date";}
	if($txt_production_date!="")
	{
		if($db_type==0) 
		{       
			$txt_production_date=change_date_format($txt_production_date,'yyyy-mm-dd');
			
		}
		else if($db_type==2) 
		{               
			$txt_production_date=change_date_format($txt_production_date,'','',1);
			
		}
		
	}
	$insert_year= date("Y");
     
	
		//$prod_date_cond= change_date_format($prod_date_cond,'','',1);

	
	
	
	if ($rpt_type==2) //show
	{
		
		
		// ==================================== MAIN QUERY ==========================================
	 	$pro_finshing_sql="SELECT b.buyer_name,b.job_no,b.company_name,b.style_ref_no,a.grouping,c.color_number_id,c.item_number_id,c.order_quantity,d.floor_id,d.production_date,d.production_type,d.remarks,
		(case when d.production_type = 5   then e.production_qnty else 0 end) as sewing_output,
		(case when d.production_type = 8 and  d.production_date='$txt_production_date' then e.production_qnty else 0 end) as today_finishing,
		(case when d.production_type = 8 and  d.production_date <='$txt_production_date' then e.production_qnty else 0 end) as total_finishing

		from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown c,pro_garments_production_mst d,  pro_garments_production_dtls e where  d.production_type in(5,8) and  a.job_id=b.id and a.id=c.po_break_down_id and  a.id=d.po_break_down_id and d.id=e.mst_id  and  a.is_deleted=0 and a.status_active=1 and 
 		b.is_deleted=0 and b.is_deleted=0 and d.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0  and 
 		b.status_active=1 and e.is_deleted=0 and e.status_active=1 and  d.production_date <='$txt_production_date' $company_name $buyer_name $sql_cond $job_cond_id  $floor_name  and to_char(d.production_date,'YYYY')=$insert_year order by d.production_date";
 		//echo $pro_finshing_sql;die();
		
 		$pro_finshing_sql_res = sql_select($pro_finshing_sql);
		 $production_data_arr=array();
		 $production_data_array=array();
		 $finishing_wip_summary_arr=array();
	  
	  	foreach($pro_finshing_sql_res as $row)
	  	{
			
			$production_data_arr[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]][$row[csf('production_type')]]['floor_id']=$row[csf('floor_id')];

			
			$production_data_arr[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['style_ref_no']=$row[csf('style_ref_no')];

			$production_data_arr[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['order_quantity']+=$row[csf('order_quantity')];

			$production_data_array[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]]['total_order_quantity']+=$row[csf('order_quantity')];

		


			$production_data_arr[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['sewing_output']+=$row[csf('sewing_output')];
			
			$production_data_array[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]]['total_sewing_output']+=$row[csf('sewing_output')];

			

			$production_data_arr[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['today_finishing']+=$row[csf('today_finishing')];

			$production_data_arr[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['total_finishing']+=$row[csf('total_finishing')];

			$production_data_arr[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]][$row[csf('item_number_id')]][$row[csf('color_number_id')]]['production_date']=$row[csf('production_date')];
			
			$production_data_array[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]]['total_order_wise_pack']+=$row[csf('order_quantity')] - $row[csf('total_finishing')];

			$production_data_array[$row[csf('floor_id')]][$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('grouping')]]['total_order_wise_pack_blan']+=($row[csf('order_quantity')] - $row[csf('total_finishing')]) - $row[csf('sewing_output')] ;

			
			$finishing_wip_summary_arr[$row[csf('buyer_name')]]['style_ref_no'] .=$row[csf('style_ref_no')].",";
			$finishing_wip_summary_arr[$row[csf('buyer_name')]]['job_no'] .=$row[csf('job_no')].",";

			$finishing_wip_summary_arr[$row[csf('buyer_name')]]['color_number_id'] .=$row[csf('color_number_id')].",";
			$finishing_wip_summary_arr[$row[csf('buyer_name')]]['item_number_id'] .=$row[csf('item_number_id')].",";

			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['grouping']=$row[csf('grouping')];
			$finishing_wip_summary_arr[$row[csf('buyer_name')]]['production_date']=$row[csf('production_date')];

			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['order_quantity']+=$row[csf('order_quantity')];
			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['total_order_quantity']+=$row[csf('order_quantity')];

			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['sewing_output']+=$row[csf('sewing_output')];

			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['today_finishing']+=$row[csf('today_finishing')];
			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['total_finishing']+=$row[csf('total_finishing')];
			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['total_order_wise_pack']+=$row[csf('order_quantity')] - $row[csf('total_finishing')];
			//  $finishing_wip_summary_arr[$row[csf('buyer_name')]]['total_poly_blan']+=$row[csf('order_quantity')] - $row[csf('total_finishing')];
			//  $finishing_wip_summary_arr[$row[csf('buyer_name')]]['total_poly_blan']+=$row[csf('order_quantity')] - $row[csf('total_finishing')];

			 $finishing_wip_summary_arr[$row[csf('buyer_name')]]['total_poly_blan']+= ($row[csf('order_quantity')] - $row[csf('total_finishing')]) - $row[csf('sewing_output')] ;
			  $finishing_wip_summary_arr[$row[csf('buyer_name')]]['total_poly_blan_qty']+= ($row[csf('order_quantity')] - $row[csf('total_finishing')]) - $row[csf('sewing_output')] ;


			
	  	}
	  
 		// echo "<pre>";
 		// print_r($finishing_wip_summary_arr); die;
 		// echo "</pre>";

 	

		;
		 ob_start();
			 ?>
	         <fieldset style="width:2550px;">

	        	<table width="2270"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="30" align="center" style="border:none;font-size:14px; font-weight:bold" >Packing and Finishing WIP Report</td>
	                     </tr>
	                    <tr style="border:none;">
	                            <td colspan="30" align="center" style="border:none; font-size:16px; font-weight:bold">
	                            Company Name:<? echo $company_library[str_replace("'","",$cbo_company_name)]; ?>                                
	                            </td>
	                      </tr>
	                      <tr style="border:none;">
	                            <td colspan="30" align="center" style="border:none;font-size:12px; font-weight:bold">
	                            <? echo "Date: ". str_replace("'","",$txt_production_date) ;?>
	                            </td>
	                      </tr>

	            </table>
	               <br />	
	               <table cellspacing="0"  border="1" rules="all"  width="2530" class="rpt_table">
	                <thead>
	                	<tr >
	                       
							<th width="100"><p>Finishing Floor</p></th>
	                        <th width="100"><p>Buyer</p></th>
							<th width="100"><p>Style</p></th>
	                        <th width="100"><p>Job No</p></th>
	                        <th width="100"><p>Int. Ref. No</p> </th>
	                        <th width="100"><p>Gmts.Item</p> </th>
	                        <th width="100"><p>Color</p></th>
							<th width="100"><p>Color wise Qty</p></th>
	                        <th width="100"><p>Total Order Qty.</p> </th>
							<th width="100"><p>Frist Out. Date</p></th>
	                        <th width="100"><p>Total Sewing Output</p></th>
	                        <th width="100"><p>Total Sewing Output</p></th>
							<th width="120"><p>Receive Bal:from order qty.</p></th>
	                        <th width="100"><p>Today Pack and Fin</p></th>
	                        <th width="100"><p>Total Pack and Fin</p></th>
	                        <th width="150"><p>Wip Pack and Fin: Bal (From Rcv)</p></th>
	                        <th width="150"><p>Pack and Fin : Bal  (From Order qty)</p></th>
	                        <th width="150"><p>Order wise Total Pack and Fin</p></th>
	                        <th width="180"><p>WIP Order wise Tot. Pack and Fin: Bal (From Rcv)</p></th>
	                        <th width="180"><p>Order wise Total Pack and Fin Bal (From Order qty)</p></th>
	                        <th width="100"><p>Sewing Floor</p></th>
	                        <th width="100"><p>Remarks</p></th>

	                        
	                        
							</tr>
	                   
	                      
	                </thead>
	               </table>
	            
	          
	               <table  border="1" class="rpt_table"  width="2530" rules="all" >
					<tbody>
						<? 
						
					// echo"<pre>";print_r($production_data_arr); die;
					$buyer_wise_total_arr=array();
						
						foreach($production_data_arr as $floor_key => $floor_val)
						{
						 	foreach($floor_val as $buyer_key => $buyer_val)
						    {
                               
                               

							   	foreach($buyer_val as $job_key => $job_val)
							    { 
									$color_value="";
									$color_wise_qty = 0;
									$order_wise_qty=0;
									$swin_out_qty = 0;
									$total_swin_out_qty=0;
									$rcv_blance_order_qty=0;
									$today_finishing_qty=0;
									$total_finishing_qty=0;
									$total_wip_pack_fin_blan=0;
									$total_pack_fin_blan =0;
									$order_wise_total_pack_fin=0;
									$order_wise_total_pack_blan=0;
									$order_wise_total_pack_qty_baln=0;
                                 
								 	foreach($job_val as $int_ref => $int_ref_val)
							       	{
									  	foreach($int_ref_val as $gmts_item => $gmts_item_val)

							    	    {
											$value_Color=count($gmts_item_val);
											
										   foreach($gmts_item_val as $color__key => $val )
							    		   {
											
											$rcv_blance_order= $val['order_quantity'] - $val['sewing_output'];
											$wip_pack_fin_blan= $val['sewing_output'] - $val['total_finishing'];
											$pack_fin_blan= $val['order_quantity'] - $val['total_finishing'];
											
											//echo 	$order_pack_fin_blan; die;
											//$total_order_qty=$color__key + $val['order_quantity'];

												?>
												  <tr>
												           <?
															 $value=$floor_key."**".$buyer_key."**".$job_key."**".$int_ref;
															 if($color_value!=$value)
															 {

															
																?>
																
																<td rowspan="<?=$value_Color?>" width="100"><p><?=$floor_arr[$floor_key];?></p></td>
																<td rowspan="<?=$value_Color?>" width="100"><p><?=$buyer_library[$buyer_key];?></p></td>
																<td rowspan="<?=$value_Color?>" width="100"><p><?=$val['style_ref_no'];?></p></td>
																<td rowspan="<?=$value_Color?>" width="100"><p><?=$job_key?></p></td>
																<td rowspan="<?=$value_Color?>" width="100"><p><?=$int_ref?></p></td>
																<?
															 }
															 ?>
															<td width="100"><p><?=$garments_item[$gmts_item];?></p></td>
															<td  width="100"><p><?=$colorname_arr[$color__key];?></p></td>
															<td  width="100" align="right"><p><?=$val['order_quantity'];?></p></td>
														  <?	if($color_value!=$value)
															 {

															
															
															?>
															<td  rowspan="<?=$value_Color?>" width="100" align="right"><p><?=$production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_quantity']?></p></td>
															<?
															 $buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_quantity']+= $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_quantity'];
														
															 }
															
															?>
															

                                                            <td width="100"align="center"><p><?=$val['production_date'];?></p></td>
															<td width="100"align="right"><p><?=$val['sewing_output']?></p></td>
															<?	if($color_value!=$value)
															 {

															
															
															?>
															<td  rowspan="<?=$value_Color?>" width="100" align="right"><p><?=$production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_sewing_output']?></p></td>
															<?
															  $buyer_wise_total_arr[$floor_key][$buyer_key]['total_sewing_output']+= $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_sewing_output'];
															
															 }
															
															?>
															
															
															
															<td width="120" align="right"><p><?=$rcv_blance_order?></p></td>
															<td width="100" align="right"><p><?=$val['today_finishing']?></p></td>
															<td width="100" align="right"><p><?=$val['total_finishing']?></p></td>
															<td width="150" align="right"><p><?=$wip_pack_fin_blan ;?></p></td>
															<td width="150" align="right"><p><?=$pack_fin_blan?></p></td>
															<?	if($color_value!=$value)
															 {

																
															
															?>
															<td  rowspan="<?=$value_Color?>" width="150" align="right"><p><?=$production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack']?></p></td>
															<?
															   $buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_wise_pack']+= $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack'];
															 }
															
															?>
															
															<?	if($color_value!=$value)
															 {

																
															
															?>
															<td  rowspan="<?=$value_Color?>" width="180" align="right"><p><?=$production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack_blan']?></p></td>
															<?
																  $buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_wise_pack_blan']+= $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack_blan'];
															 }
															
															?>
															<?	if($color_value!=$value)
															 {

																
															
															?>
															<td  rowspan="<?=$value_Color?>" width="180" align="right"><p><?=$production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_quantity'] - $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack']?></p></td>
															<?
																  $buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_wise_pack_blan_qty']+= $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_quantity'] - $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack'];
															 }
															
															?>
															<?	if($color_value!=$value)
															 {

																$color_value=$value;
															
															?>
															<td rowspan="<?=$value_Color?>" width="100"><p><?=$floor_arr[$val[5]['floor_id']]?></p></td>
															<?
															
															 }
															
															?>
															<td width="100"><p><?=$val['remarks']?></p></td>
															
															

											
														
														
														</tr>
														
														<?
														  $color_wise_qty += $val['order_quantity'];
														  $order_wise_qty += $val['order_quantity'];
														  $swin_out_qty   += $val['sewing_output'];
														  $total_swin_out_qty +=$production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_sewing_output'];
														  $rcv_blance_order_qty +=$rcv_blance_order ;
														  $today_finishing_qty +=$val['today_finishing'];
														  $total_finishing_qty +=$val['total_finishing'];
														  $total_wip_pack_fin_blan +=$wip_pack_fin_blan;
														  $total_pack_fin_blan +=$pack_fin_blan;
														  $order_wise_total_pack_fin += $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack'];
  
														  $order_wise_total_pack_blan +=$production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack_blan'];
  
														  $order_wise_total_pack_qty_baln += $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_quantity'] - $production_data_array[$floor_key][$buyer_key][$job_key][$int_ref]['total_order_wise_pack_blan'];

														



														 
											
										  }			
										}
								 }	
								 
								
								 
						
							   	}	
							  
							   ?>
							   
							                         
							
							   <tr style="text-align:right;font-weight:bold;background:#dcdccd;">
								   <td width="100"><p>Summary  Of</p></td>
								   <td width="100"><p> <?=$buyer_library[$buyer_key];?></p></td>
								   <td width="100"><p></p></td>
								   <td width="100"><p></p></td>
								   <td width="100"><p></p> </td>
								   <td width="100"><p></p> </td>
								   <td width="100"><p></p></td>
								   <td width="100"><p><?=$color_wise_qty?></p></td>
								   <td width="100"><p><?=$buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_quantity'];?></p> </td>
								   <td width="100"><p></p></td>
								   <td width="100"><p><?=$swin_out_qty?></p></td>
								   <td width="100"><p><?=$buyer_wise_total_arr[$floor_key][$buyer_key]['total_sewing_output'];?></p></td>
								   <td width="120"><p><?=$rcv_blance_order_qty?></p></td>
								   <td width="100"><p><?=$today_finishing_qty?></p></td>
								   <td width="100"><p><?=$total_finishing_qty?></p></td>
								   <td width="150"><p><?=$total_wip_pack_fin_blan?></p></td>
								   <td width="150"><p><?=$total_pack_fin_blan?></p></td>
								   <td width="150"><p><?=$buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_wise_pack'];?></p></td>
								   <td width="180"><p><?=$buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_wise_pack_blan'];?></p></td>
								   <td width="180"><p><?=$buyer_wise_total_arr[$floor_key][$buyer_key]['total_order_wise_pack_blan_qty'];?></p></td>
								   <td width="100"><p></p></td>
								   <td width="100"><p></p></td>
								</tr>	
					   
   
						   		<?
										 
							}	
							
						}
						
					  ?>
					</tbody>	
	                   
									    
	               </table> 



				   <br>

				<div style="width:2550px;">
	        	<table width="2550px"  cellspacing="0"   >
	                    <tr class="form_caption" style="border:none;">
	                           <td colspan="30" align="center" style="border:none;font-size:18px; font-weight:bold" >Finishing WIP Summary </td>
	                     </tr>
	                    
	            </table>
	               <br />	
	               <table cellspacing="0"  border="1" rules="all"  width="2530" class="rpt_table">
	                <thead>
	                	<tr >
	                       
	                        <th width="100"><p>Buyer</p></th>
							<th width="100"><p>Style</p></th>
	                        <th width="100"><p>Job No</p></th>
	                        <th width="100"><p>Int. Ref. No</p> </th>
	                        <th width="100"><p>Gmts.Item</p> </th>
	                        <th width="100"><p>Color</p></th>
							<th width="100"><p>Color Wise Qty.</p></th>
	                        <th width="100"><p>Total Order Qty.</p> </th>
	                        <th width="100"><p>Frist Out. Date</p></th>
	                        
	                        <th width="100"><p>Total Sew. Output</p></th>
	                        <th width="120"><p>Rev. Bal:from order qty.</p></th>
	                        <th width="100"><p>Today Pack and Fin.</p></th>
	                        <th width="100"><p>Total Poly</p></th>
	                        <th width="150"><p>Wip Pack and Fin: Bal (From Rcv)</p></th>
	                        <th width="150"><p>Pack and Fin : Bal  (From Order qty)</p></th>
	                        <th width="150"><p>Order wise Total Pack and Fin</p></th>
	                        <th width="180"><p>Poly:bal(From Rcv)</p></th>
	                        <th width="180"><p>Poly:bal (From Order qty)</p></th>
	                        <th width="100"><p>Pack Level Name</p></th>
	                        <th width="100"><p>Remarks</p></th>

							</tr>
	                   
	                      
	                </thead>
	               </table>
	            
	          
	               <table  border="1" class="rpt_table"  width="2530" rules="all" >
					<tbody>
						    <?
								
								$color_wise_qty = 0;
								$order_wise_qty=0;
								
								$total_swin_out_qty=0;
								$rcv_blance_order_qty=0;
								$today_finishing_qty=0;
								$total_finishing_qty=0;
								$total_wip_pack_fin_blan=0;
								$total_pack_fin_blan =0;
								$order_wise_total_pack_fin=0;
								$order_wise_total_pack_blan=0;
								$order_wise_total_pack_qty_baln=0;
							
								foreach($finishing_wip_summary_arr as $buyer => $row)
								{
									$rcv_blance_order= $row['order_quantity'] - $row['sewing_output'];
									$wip_pack_fin_blan= $row['sewing_output'] - $row['total_finishing'];
									$pack_fin_blan= $row['order_quantity'] - $row['total_finishing'];
									$total_poly_blan_qty =($row['total_order_quantity']-$row['total_order_wise_pack']);
								  ;
								
									
									
																$item_id=array_unique(explode(",",chop($row['item_number_id'],",")))	;
																$val="";

                                    								foreach ($item_id  as $value) 
																	{
																		if($val!="")
																		{
																			$val .=",";
																		}
																		$val .=$garments_item[$value];
																		
																	}	
																	
																	$color_id=array_unique(explode(",",chop($row['color_number_id'],",")))	;
																$rows="";

                                    								foreach ($color_id  as $values) 
																	{
																		if($rows!="")
																		{
																			$rows .=",";
																		}
																		$rows .=$colorname_arr[$values];
																		
																	}				
																							
														?>
															<tr>
															
																<td width="100"><p><?=$buyer_library[$buyer];?></p></td>
																<td width="100"><p><?=implode(",",array_unique(explode(",",chop($row['style_ref_no'],","))))?></p></td>
																<td width="100"><p><?=implode(",",array_unique(explode(",",chop($row['job_no'],","))));?></p></p></td>
																<td width="100"><p><?=$row['grouping']?></p> </td>
																<td width="100"><p><?=$val
																;?></p> </td>
																<td width="100"><p><?=$rows;?></p></td>
																<td width="100" align="right"><p><?=$row['order_quantity'];?></p></td>
																<td width="100" align="right"><p><?=$row['total_order_quantity'];?></p> </td>
																<td width="100" align="center"><p><?=$row['production_date'];?></p></td>
																
																<td width="100" align="right"><p><?=$row['sewing_output'];?></p></td>
																<td width="120" align="right"><p><?=$rcv_blance_order?></p></td>
																<td width="100" align="right"><p><?=$row['today_finishing'];?></p></td>
																<td width="100" align="right"><p><?=$row['total_finishing'];?></p></td>
																<td width="150" align="right"><p><?=$wip_pack_fin_blan;?></p></td>
																<td width="150" align="right"><p><?=$pack_fin_blan;?></p></td>
																<td width="150" align="right"><p><?=$row['total_order_wise_pack'];?></p></td>
																<td width="180" align="right"><p><?=$row['total_poly_blan']?></p></td>
																<td width="180" align="right"><p><?=$total_poly_blan_qty;?></p></td>
																<td width="100" align="right"><p><??></p></td>
																<td width="100" align="right"><p><?=$row['remarks'];?></p></p></td>

															</tr>
														
														
														<?
														$color_wise_qty +=$row['order_quantity']; ;
														$order_wise_qty +=$row['total_order_quantity'];
														
														$total_swin_out_qty +=$row['sewing_output'];
														$rcv_blance_order_qty +=$rcv_blance_order;
														$today_finishing_qty +=$row['today_finishing'];
														$total_finishing_qty  +=$row['total_finishing'];
														$total_wip_pack_fin_blan  +=$wip_pack_fin_blan;
														$total_pack_fin_blan  +=$pack_fin_blan;
														$order_wise_total_pack_fin  +=$row['total_order_wise_pack'];
														$order_wise_total_pack_blan  +=$row['total_poly_blan'];
														$order_wise_total_pack_qty_baln  +=$total_poly_blan_qty;
													
										    	
										
								}	
							?>		
				</tbody>	
				<tfoot>
										
										<th colspan="5" ><p>Grand Summary</p></th>
										
										<th width="100"><p></p></th>
										<th width="100"><p><?=$color_wise_qty?></p> </th>
										<th width="100"><p><?=$order_wise_qty?></p></th>
										<th width="100"><p></p></th>
										
										<th width="100"><p><?=$total_swin_out_qty?></p></th>
										<th width="120"><p><?=$rcv_blance_order_qty?></p></th>
										<th width="100"><p><?=$today_finishing_qty?></p></th>
										<th width="100"><p><?=$total_finishing_qty?></p></th>
										<th width="150"><p><?=$total_wip_pack_fin_blan?></p></th>
										<th width="150"><p><?=$total_pack_fin_blan?></p></th>
										<th width="150"><p><?=$order_wise_total_pack_fin?></p></th>
										<th width="180"><p><?=$order_wise_total_pack_blan?></p></th>
										<th width="180"><p><?=$order_wise_total_pack_qty_baln?></p></th>
										<th width="100"><p></p></th>
										
										<th width="100"><p></p></th>

									
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit(); 
}


if($action=="finish_fabric")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	 
	  $insert_cond="   and  d.production_date='$insert_date'";
    // if($type==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="200">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot> 
       </table>
      </fieldset>
       <br />
    <? 
	
	$sql_fabric="SELECT a.po_breakdown_id,a.color_id,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =4  AND a.entry_form =16 and b.item_category=13 THEN a.quantity
	    ELSE 0 END ) AS grey_fabric_issue_return,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =1  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece,
		sum(CASE WHEN b.transaction_date <= ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =37 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS finish_fabric_rece_return,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END ) AS fabric_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =2 and b.item_category=2  AND a.entry_form =18 THEN a.quantity 
		ELSE 0 END ) AS fabric_qty_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =5  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_in_pre,
		sum(CASE WHEN b.transaction_date = ".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_qty,
		sum(CASE WHEN b.transaction_date <".$txt_production_date." AND a.trans_type =6  AND a.entry_form =15 THEN a.quantity ELSE 0 END ) AS trans_out_pre
		FROM order_wise_pro_details a,inv_transaction b
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and a.quantity!=0 and  b.is_deleted=0 AND a.po_breakdown_id 
		in (".str_replace("'","",$po_number_id).") group by a.po_breakdown_id,a.color_id";

			
	
		//echo $sql_cutting_delevery;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_cutting_delevery);
		$production_details_arr=array();
		$production_size_details_arr=array();
		foreach( $sql_data as $row)
		{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];
		}
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px">  
			<label> <strong>Po Number: <? echo $order_number; ?><strong><label/>
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">ID</th>
                        <th width="70">Date</th>
                        <th width="70">Fabric Qty.</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($value_c != "")
				//{
				?>
				 <tr bgcolor="<? echo $bgcolor;?>">
				 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
                 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
				 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
						?>
						<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
						<?
							
						}
				 ?>
				 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

				 </tr>
				<?
				$i++;
				//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
	    <br />
     </fieldset>
 </div>
 <?
}


if($action=="cutting_delivery_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and c.item_number_id=$item_id";
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d 
	  where a.job_id=b.id and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id"); 

	// ================= gross qty ==================
	$sql_delivery_qnty=sql_select("SELECT  d.country_id,d.color_number_id,
	e.production_qnty AS delivery_qnty 
	from  wo_po_color_size_breakdown d,pro_cut_delivery_color_dtls e 
	where  e.color_size_break_down_id=d.id and  d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id $item_cond  group by d.country_id,d.color_number_id,e.production_qnty");	

	$delivery_qty_arr=array();
	foreach($sql_delivery_qnty as $key=>$value)
	{
		$delivery_qty_arr[$value[csf("country_id")]][$value[csf("color_number_id")]]["delivery_qnty"] +=$value[csf("delivery_qnty")];

	}
	// ======================== bundle qty =======================$job_size_array=array();
	$main_sewing_source=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$production_details_arr=array();
	$production_size_details_arr=array();
	$bundle_sql = "SELECT a.id,a.challan_no,a.PRODUCTION_DATE,c.color_number_id as color_id,c.country_id,c.size_number_id,b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown  c where a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and b.status_active=1 and c.status_active=1 and a.status_active=1 and b.production_type=9 and a.PRODUCTION_SOURCE=1 and  c.po_break_down_id='$order_id' and c.color_number_id=$color_id $item_cond2";
	// echo $bundle_sql;
	$res = sql_select($bundle_sql);
	foreach($res as $v)
	{
		$delivery_qty_arr[$v[csf("country_id")]][$v[csf("color_id")]]["delivery_qnty"] +=$v[csf("production_qnty")];
		// =================================================
		$job_size_array[$order_number][$v[csf('size_number_id')]]=$v[csf('size_number_id')];
		$job_size_qnty_array[$v[csf('size_number_id')]]+=$v[csf('production_qnty')];
		

		$job_color_array[$order_number][$v[csf('color_id')]]=$v[csf('color_id')];
		$job_color_qnty_array['color_total']+=$v[csf('production_qnty')];
		//$job_color_size_qnty_array[$order_number][$v[csf('color_id')]][$v[csf('size_number_id')]]+=$v[csf('product_qty')];
		
		$production_details_arr[$v[csf('id')]]['country']=$v[csf('country_id')];
		$production_details_arr[$v[csf('id')]]['color']=$v[csf('color_id')];
		$production_details_arr[$v[csf('id')]]['production_date']=$v[csf('production_date')];
		$production_details_arr[$v[csf('id')]]['challan_no']=$v[csf('challan_no')];
		$production_details_arr[$v[csf('id')]]['product_qty']+=$v[csf('production_qnty')];
		//$production_details_arr[$v[csf('id')]]['size']=$v[csf('size_number_id')];
		$production_size_details_arr[$v[csf('id')]][$v[csf('size_number_id')]]['product_qty']+=$v[csf('production_qnty')];

	}
	
	$sql_color_size="SELECT d.country_id, d.color_number_id,d.size_number_id,sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id  group by d.country_id,d.color_number_id,d.size_number_id";
	//  echo $sql_color_size;
	$sql_color_size_data=sql_select($sql_color_size);
	$country_size_arr=array();
	$country_color_size_arr=array();
	foreach($sql_color_size_data as $key=>$value)
	{
		$country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["order_qty"] +=$value[csf("order_qty")];
		$country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["plan_cut"] +=$value[csf("plan_cut")];
		$country_color_size_arr[$value[csf("country_id")]]["order_qty_color_total"] +=$value[csf("order_qty")];
		$country_color_size_arr[$value[csf("country_id")]]["plan_qty_color_total"] +=$value[csf("order_qty")];

	}
	?>
    <div id="data_panel" align="center" style="width:100%">
       <div  style="width:830px">
	    <div id="data_panel" align="" style="width:100%">
    	<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports_all').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
	 </div>
	 <hr>
	 <div id="details_reports_all">
	 <!-- ======================================== 1st Print Start ================================== -->	
	  <div id="details_reports">
         <table width="810px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="80">Order Qty</th>
			  <th width="80">Delivery Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
			{
				// if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>">
					<td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
					<td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
					<td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
					<td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
					<td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
					<td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
					
					<td align="right">
					<? 
						echo $delivery_qty_arr[$value[csf("country_id")]][$value[csf("color_number_id")]]["delivery_qnty"]+=$value[csf("delivery_qnty")]; 
						$total_delivery_qty+=$delivery_qty_arr[$value[csf("country_id")]][$value[csf("color_number_id")]]["delivery_qnty"] ;
					?>
					</td>
					
				</tr>
				<?
			}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
			  
               <th><? echo $total_qty; ?></th>
			   <th align="right"><? echo $total_delivery_qty;?></th>
               </tr>
          </tfoot>
       </table>
			<!-- </div>
			</div> -->
      </div>
       <br />
    	<? 
		$sql_cutting_delevery="SELECT a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id,d.sewing_source
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c,pro_cut_delivery_mst d
	    where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id 
		and a.po_break_down_id=c.po_break_down_id 
		and d.id=a.delivery_mst_id
		
		and d.sewing_source=1
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id 
		and a.status_active=1 and a.is_deleted=0
		and  b.status_active=1  and b.is_deleted=0
		and c.status_active=1 $item_cond2";
		// echo $sql_cutting_delevery;
		$sql_data = sql_select($sql_cutting_delevery);
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");

		
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			

			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
			//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			
			$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
			$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
			//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
			$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];
	
		}
	
		// print_r($production_size_details_arr);die;
		 $job_color_tot=0;
		 ?> 
		 <!-- 2nd print Start here -->
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
	    <script>
			function new_window2()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('InhouseOutBound_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
	  <div id="InhouseOutBound_details">  
		<div style="text-align: center;"> <strong>In House </strong></div>
		<div style="text-align: center;"> <strong>Style No : <? echo $sql_job[0][csf('style_ref_no')]; ?> </strong></div>
	         
			<div style="text-align:center"> <strong>Order Number: <? echo $order_number; ?><strong></div>
			<table width="390" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
						<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
						<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
						<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
						<td align="right"><?  echo  $value_c['challan_no']; ?></td>
						<?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							
							?>
							<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
							<?
							
						}
						?>
						<td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

					</tr>
					<?
					$i++;
					
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />
				  
			<!-- ==============================================================================================
			/											Outbound Part											/
			/ =============================================================================================== -->
			<? 
		    $sql_outbound="SELECT a.id,a.cut_delivery_date,a.challan_no ,b.production_qnty,c.size_number_id,c.color_number_id,c.country_id,d.sewing_source,d.sewing_company
			from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c,pro_cut_delivery_mst d
			where a.id=b.mst_id 
			and b.color_size_break_down_id=c.id 
			and a.po_break_down_id=c.po_break_down_id 
			and d.id=a.delivery_mst_id
	
			and d.sewing_source=3
			and a.po_break_down_id=$order_id
			and c.color_number_id=$color_id 
			and a.status_active=1 and a.is_deleted=0
			and  b.status_active=1  and b.is_deleted=0
			and c.status_active=1 $item_cond2";
			// echo $sql_outbound;
	
		
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql_outbound);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$country_cutting_qty_arr=array();
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			

			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
			//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			
			$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('id')]]['sewing_company']=$row[csf('sewing_company')];
			

			$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('cut_delivery_date')];
			$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
			//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
			$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];		
	
		}
		
		//  ============================ bundle level data =========================
		$bundle_sql = "SELECT a.id,a.challan_no,a.production_date,a.serving_company,c.color_number_id as color_id,c.country_id,c.size_number_id,b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown  c where a.id=b.mst_id and c.id=b.color_size_break_down_id and a.po_break_down_id=c.po_break_down_id and b.status_active=1 and c.status_active=1 and a.status_active=1 and b.production_type=9 and a.PRODUCTION_SOURCE=3 and  c.po_break_down_id='$order_id' and c.color_number_id=$color_id $item_cond2";
		// echo $bundle_sql;die;
		$sql_data = sql_select($bundle_sql);
		foreach( $sql_data as $row)
		{
			$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
			

			$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array['color_total']+=$row[csf('production_qnty')];
			//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			
			$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('id')]]['sewing_company']=$row[csf('serving_company')];
			

			$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('production_qnty')];
			//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
			$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('production_qnty')];		
	
		}

		$sql_output_qty="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id, d.production_date,f.country_id,d.production_source FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and e.color_size_break_down_id=f.id  and f.po_break_down_id=$order_id   and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active =1 $insert_cond  order by d.production_date";
	  	// echo $sql_output_qty;
		$sql_output_data=sql_select($sql_output_qty);
		// $job_size_array=array();
		$country_color_size_arr=array();
		 $country_cutting_qty_arr=array();
		foreach($sql_output_data as $row){
			$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
			$country_cutting_qty_arr[$row[csf('production_source')]][$row[csf('country_id')]][$row[csf('size_number_id')]]=$row[csf('product_qty')];
			// $grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			// $grand_color_qty+=$row[csf('product_qty')];
			// $country_cutting_qty_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			// $country_cutting_qty_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			// $country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			
			
		}
		//  print_r($country_cutting_qty_arr);
	
		// print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?>
        <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:820px"> 
	    <script>
			function new_window2()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('InhouseOutBound_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
	  <div id="InhouseOutBound_details">  
		<div style="text-align: center;"> <strong>Out Bound </strong></div>
		<div style="text-align: center;"> <strong>Style No : <? echo $sql_job[0][csf('style_ref_no')]; ?> </strong></div>
	         
			<div style="text-align:center"> <strong>Order Number: <? echo $order_number; ?><strong></div>
			<table width="490" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
						<th width="100">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						// print_r($itemSizeArr[$value]);
						?>
						
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
				$i=1;
				foreach($production_details_arr as $key_c=>$value_c)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//if($value_c != "")
					//{
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
					<td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
					<td align="center"><?  echo  $supplier_arr [$value_c['sewing_company']]; ?>
					<td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
					<td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
					<td align="right"><?  echo  $value_c['challan_no']; ?></td>
					<?
							foreach($job_size_array[$order_number] as $key_s=>$value_s)
							{
								
							?>
							<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
							<?
								
							}
					?>
					<td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

					</tr>
					<?
					$i++;
					//}
				}
				?>
				<tfoot>
				 <tr bgcolor="<? // echo $bgcolor;?>">
                 <th></th>
				 <th></th>
                 <th></th>
                 <th></th>
				 <th>Total</th>
				
				 <?
						foreach($job_size_array[$order_number] as $key_s=>$value_s)
						{
							if($value_s !="")
							{
						?>
						<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
						<?
							}
						}
				?>
                 <th align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
				 </tr>
			  </tfoot>
		</table>
				  <br />
				        <!-- 2nd Print End  -->
						<!-- 3rd Print Start from here  -->

		<?
		$tbl_width = 370+(count($job_size_array[$order_number])*60);
		?>
		<!-- ==============================================================================================
		/												Last Part											/
		/ =============================================================================================== -->
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;margin: 0 auto;width: <? echo $tbl_width;?>px">  
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>
					
							
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
			
					<?
					
					$i=1;
					
					foreach($country_color_wise_arr as $country_key=>$country_data)
					{
						
						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>
						
					
						<?
						foreach($country_data as $color_key=>$val)
						{
							
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>			                 	
			                 </td>
							 <td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;
								foreach($job_size_array[$order_number] as $key_s=>$value_s)
								{
									
									?>
									<td width="60" align="right"><? echo $country_size_arr[$country_key][$color_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_arr[$country_key][$color_key][$key_s]['order_qty'];
								}
								// echo '<pre>';
								//  print_r($country_size_arr);
							 ?>
							 
							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>
							
							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
								<td align="left">Plan To <? echo ($type==4) ? "Input" : "Output";?></td>					
									 <?
									 $pc_total = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_arr[$country_key][$color_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_arr[$country_key][$color_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>  
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>               
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Qty</td>					
									<?
									// echo $country_key."==".$key_s."<br>";
										$cuttingTotal = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_cutting_qty_arr[$country_key][$color_key][$key_s];?></td>
												<?
												$cuttingTotal += $country_cutting_qty_arr[$country_key][$color_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Balance</td>					
								 <?
									foreach($job_size_array[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{											
											?>
											<td width="60" align="right"><? echo $bal = $country_size_arr[$country_key][$color_key][$key_s]["plan_cut"] - $country_cutting_qty_arr[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>
		
		
				<br />
		
 </div>
 <?
}

if($action=="cutting_and_sewing_remarks")
{	
	extract($_REQUEST); 
 	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'',''); 
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and c.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	?>
    <div align="center">
        <fieldset style="width:480px">
        <legend>Cutting</legend>
		<? 
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=1  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond  $item_cond2 group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/packing_and_finishing_wip_report_controller", '','0,3,1,0','0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Cutting Delivery to Input</legend>
		<? 
	    $sql_cutting_delevery="select a.id,a.cut_delivery_date ,a.remarks,
		sum(b.production_qnty) AS cut_delivery_qnty
		from pro_cut_delivery_order_dtls a,pro_cut_delivery_color_dtls b ,wo_po_color_size_breakdown c 
	    where a.id=b.mst_id 
	    and b.color_size_break_down_id=c.id 
		and a.po_break_down_id=c.po_break_down_id  
		and a.po_break_down_id=$order_id
		and c.color_number_id=$color_id  $item_cond
	    group by a.id,a.cut_delivery_date ,a.remarks";
       // echo $sql_cutting_delevery;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql_cutting_delevery, "", "","", 1, '0,0,0,0', $arr, "id,cut_delivery_date,cut_delivery_qnty,remarks", "../requires/packing_and_finishing_wip_report_controller", '','0,3,1,0','0,0,0,cut_delivery_qnty,0');
                
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Issue</legend>
		<? 
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=2 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1   $insert_cond $item_cond2 group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/packing_and_finishing_wip_report_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        <fieldset style="width:480px">
        <legend>Print/Embr Receive</legend>
		<? 
        $sql="SELECT  d.id,d.embel_name,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=3 and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		f.is_deleted =0 and
		f.status_active =1 and
		d.is_deleted =0 and
		d.status_active =1 
		
		 $insert_cond $item_cond2 group by d.id,d.embel_name,f.color_number_id,d.remarks,d.production_date order by d.embel_name";
        $arr=array(1=>$emblishment_name_array);
        echo  create_list_view ( "list_view_1", "ID,Embel. Name,Date,Production Qnty,Remarks", "80,100,70,70,180","600","220",1, $sql, "", "","", 1, '0,embel_name,0,0,0', $arr, "id,embel_name,production_date,product_qty,remarks", "../requires/packing_and_finishing_wip_report_controller", '','0,0,3,1,0','0,0,0,0,product_qty,0');
        ?>
        </fieldset>
        <br/>
        
        <fieldset style="width:480px">
        <legend>Sewing Input</legend>
        <?
        $sql="SELECT  d.id,sum(e.production_qnty) as product_qty,f.color_number_id,d.remarks,d.production_date
        FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
        WHERE 
        d.po_break_down_id=$order_id  and
        d.id=e.mst_id and
        e.color_size_break_down_id=f.id and
        f.po_break_down_id=$order_id and
        e.production_type=4  and
        f.color_number_id=$color_id and
        e.is_deleted =0 and
        e.status_active =1  and
		d.is_deleted =0 and
		d.status_active =1 and
		f.is_deleted =0 and
		f.status_active =1 
		 $insert_cond $item_cond2 group by d.id,f.color_number_id,d.remarks,d.production_date order by d.id";
        //echo $sql;
        echo  create_list_view ( "list_view_1", "ID,Date,Production Qnty,Remarks", "80,70,70,280","600","220",1, $sql, "", "","", 1, '0,0,0,0', $arr, "id,production_date,product_qty,remarks", "../requires/packing_and_finishing_wip_report_controller", '','0,3,1,0','0,0,0,product_qty,0');
		?>
        </fieldset>
	</div>  
	<?
	exit();
}



if($action=="emblishment_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);

	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  ); 
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	//echo $item_con.'aaaaaa';
	$sql_job=("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:810px">
         <table width="800px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="100">Order Qty</th>
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach( sql_select($sql_job) as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
               <th><? echo $total_qty; ?></th>
               </tr>
          </tfoot>
       </table>
      </fieldset>
       <br />
    <? 
        $delivery_sql="SELECT id ,sys_number_prefix_num from pro_gmts_delivery_mst where status_active=1 and is_deleted=0 and company_id=$company_id and production_type=$type and embel_name=$embl_type";
    	foreach( sql_select($delivery_sql) as $key=>$vals)
    	{
    		$challan_no_arr[$vals[csf("id")]]=$vals[csf("sys_number_prefix_num")];
    	}
 		$sql="SELECT  d.id,d.floor_id,d.production_source,d.serving_company,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,
		    d.challan_no,d.production_date,f.country_id,d.delivery_mst_id 
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type=$type  and
		    f.color_number_id=$color_id and
			d.embel_name=$embl_type  and
		    e.is_deleted =0 and
			e.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1   $insert_cond  $item_cond2 order by d.production_date,f.id";
		//echo $sql;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$ocrder_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				if($row[csf('challan_no')])
					{
						$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
					}
					else
					{
						$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$challan_no_arr[$row[csf('delivery_mst_id')]];
					}
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				if($row[csf('challan_no')])
				{
					$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				}
				else
				{
					$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$challan_no_arr[$row[csf('delivery_mst_id')]];
				}
				
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House <strong><label/>
            
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="150">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>
                    
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="5" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                          
                             <th></th>
                             <th>Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   
                <label > <strong>Out Bound:<strong><label/>    
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="120">Color</th>
                        <th width="130">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>    
	 </div>
 <?
}


if($action=="cutting_and_sewing_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id"); 
	//   $sql= "SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	//   sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	//   from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	//   where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	//   b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id";
	//   echo $sql;

	$sql_color_size=sql_select("SELECT d.country_id, d.color_number_id,d.size_number_id, sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id  group by d.country_id,d.color_number_id,d.size_number_id "); 
	$country_size_qty_arr = array();
	$country_color_total_arr = array();
	foreach($sql_color_size as $key=>$vals)
	{
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$country_color_total_arr[$vals[csf("country_id")]]["order_qty_color_total"] +=$vals[csf("order_qty")];
		$country_color_total_arr[$vals[csf("country_id")]]["plan_cut_color_total"] +=$vals[csf("plan_cut")];
 	}
	?>
 <div id="data_panel" align="center" style="width:100%">
      
    <?
	$sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no, d.production_date,f.country_id,d.challan_no FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and e.color_size_break_down_id=f.id and f.po_break_down_id=$order_id and e.production_type='$type'  and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active =1 $insert_cond  $item_cond2 order by  d.floor_id,d.production_date,f.size_order"; 
	//echo $sql;die;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$production_details_arr=array();
	$production_size_details_arr=array();
	$job_floor_qnty_array=array();
	$floor_qty_arr=array();
	$grand_size_qty=array();
	$sewing_qty=array();
	//$grand_color_qty=array();

	$sql_data = sql_select($sql);
	foreach( $sql_data as $row)
	{
		// if($row[csf('production_source')]==1)
		// {
			$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
			$job_floor_qnty_array[$row[csf('production_source')]][$row[csf('floor_id')]]+=$row[csf('product_qty')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
			$sewing_qty[$row[csf('country_id')]]+=$row[csf('product_qty')];
			$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];

			$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
			$country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		/*}
		else
		{
			$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
			$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
			$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
		}*/
		$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$grand_color_qty+=$row[csf('product_qty')];
	}
	//print_r($job_floor_qnty_array);die;
	$inhouse_size_total = count($job_size_array[1][$order_number]);
	$inhouse_tbl_width = 600+($inhouse_size_total*60);

	$outbound_size_total = count($job_size_array[3][$order_number]);
	$outbound_tbl_width = 530+($outbound_size_total*60);

	$job_color_tot=0;
	?> 
     <!-- ======================================== All Print Start ================================== -->
     <div id="data_panel" align="" style="width:100%">
    	<script>
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports_all').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
	 </div>
	 <hr>
	<div id="details_reports_all">
	 <!-- ======================================== 1st Print Start ================================== -->	
	  <div id="details_reports">
		<div  style="width:920px;margin: 0 auto;">
	       	<table width="900px" align="center" border="1" rules="all" class="rpt_table" >
		        <thead>
		              <tr>
		              <th width="200">Buyer Name</th>
		              <th width="100">Job No </th>
		              <th width="100">Style Reff.</th>
		              <th width="100">Country</th>
		              <th width="100">Order No</th>
		              <th width="100">Ship Date</th>
		              <th width="100">Order Qty</th>
		              <th width="100">Sewing Qty</th>
		              </tr>
		        </thead>
	         	<tbody>
	          <?				  
			    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
	                <tr bgcolor="<? echo $bgcolor;?>">
	                   <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
	                   <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
	                   <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
	                   <td align="left"><p><? echo $country_arr[$row[csf('country_id')]]."/".$country_code_arr[$row[csf('country_id')]]; ?></p></td>
	                   <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
	                   <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
	                    <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')]; ?></td>
	                    <td align="right"><? echo $sewing_qty[$row[csf('country_id')]]; $total_sewing_qty+=$sewing_qty[$row[csf('country_id')]]; ?></td>
	                </tr>
	                <?
				}
			  ?>
	          </tbody>
	            <tfoot>
	               <tr>
		               <th colspan="6">Total</th>
		               <th><? echo $total_qty; ?></th>
		               <th><? echo $total_sewing_qty; ?></th>
	               </tr>
	            </tfoot>
	       </table>
		</div>
	 </div>
	 <!-- ======================================== 1st Print End ================================== -->	
	 <!-- ======================================== 2nd Print Start ================================== -->
	 <div id="data_panel" align="center" style="width:100%">
	    <script>
			function new_window2()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('InhouseOutBound_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
	  <div id="InhouseOutBound_details">  
		<div style="text-align: center;"> <strong>In House </strong></div>
		<div style="text-align: center;"> <strong>Style No : <? echo $sql_job[0][csf('style_ref_no')]; ?> </strong></div>
		<div style="text-align: center;"> <strong>Order No : <? echo $order_number; ?> </strong></div>
    
		<table width="<? echo $inhouse_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th width="180">Color</th>
                    <th width="70">Country</th>
                    <th width="70">Unit Name</th>
                    <th width="70">Line No</th>
                    <th width="70">Date</th>
                    <th width="70">Challan</th>
					<?
					foreach($job_size_array[1][$order_number] as $key=>$value)
					{
						if($value !="")
						{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
						}
					}
					?>
                    <th width="70">Color Total</th>
				</tr>
			</thead>
		</table>
	
		<table align="center" width="<? echo $inhouse_tbl_width;?>" border="1" rules="all" class="rpt_table" id="html_search_1" >
			<?
		
			$i=1;
			$inhouse_floor=array();
			foreach($production_details_arr[1] as $key_c=>$value_c)
			{						
				if($prod_reso_allo==1)	
				{
					$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
			    }
				else 
				{
					$line_name= $lineArr[$value_c['sewing_line']];
				}	
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($i!=1)
				{							
					if(!in_array($value_c['floor_id'],$inhouse_floor))
						{
						?>
						 <tr bgcolor="#FFFFE8">
							 <td width="530" colspan="6" align="right"> Floor Total</td>
							
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
									?>
									<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
									<?
										}
									}
							?>
							 <td width="70" align="right"><? echo  $job_floor_qnty_array[1][$floor_id]; ?></td>
						 </tr>		
						<?	
						}
					}							
					?>                                         
					<tr bgcolor="<? echo $bgcolor;?>">
						 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
						 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]."/".$country_code_arr[$value_c['country']]; ?></td>
						 <td width="70" align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
						 <td width="70" align="center"><? echo  $line_name; ?></td>
						 
						 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
						 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
						 <?
								foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
								{
									
								?>
								<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
								<?
									
								}
						 ?>
						 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
	
					</tr>
					<?
						
					$i++;
					$inhouse_floor[]=$value_c['floor_id'];
					$floor_id=$value_c['floor_id'];
				}
				?> 
        		<tr bgcolor="#FFFFE8">
						 <td colspan="6" align="right"> Floor Total</td>
						
						 <?
							foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
							{
								if($value_s !="")
								{
									?>
									<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
									<?
								}
							}
					?>
					 <td align="right"><? echo  $job_floor_qnty_array[1][$floor_id];;//$job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
				</tr>      
                <tfoot>
                 <tr bgcolor="<? // echo $bgcolor;?>">
                     <th></th>
                     <th></th>
                     <th></th>
                     <th></th>
                     <th></th>
                     <th>Total</th>
                    
                     <?
                        foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                        {
                            if($value_s !="")
                            {
                                ?>
                                <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                <?
                            }
                        }
                    ?>
                     <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                 </tr>
              </tfoot>
		</table>
			
        <!-- ======================================== OUT BOUND ================================== -->
        <div  style="text-align: center;"> <strong>Out Bound:</strong></div>
        <table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr>
					<th width="180">Color</th>
                    <th width="70">Company</th>
                    <th width="70">Country</th>
                    <th width="70">Date</th>
                    <th width="70">Challan</th>
					<?
					foreach($job_size_array[3][$order_number] as $key=>$value)
					{
						if($value !="")
						{
						?>
						<th width="60"><? echo $itemSizeArr[$value];?></th>
						<?
						}
					}
					?>
                    <th width="70">Color Total</th>
				</tr>
			</thead>
		</table>
		<!-- ============================= for Outbound order =========================== -->
		
		<table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2" >
			<?		
			$j=1;
			$inhouse_floor=array();
			foreach($production_details_arr[3] as $key_c=>$value_c)
			{
				if($prod_reso_allo==1)	
				{
				$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
			    }
				else 
				{
					$line_name= $lineArr[$value_c['sewing_line']];
				}	
				if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				?>                        
				<tr bgcolor="<? echo $bgcolor;?>">
					 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
	                 <td width="70" align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
					 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]."/".$country_code_arr[$value_c['country']]; ?></td>
					 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
					 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
					 <?
							foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
							{
								
							?>
							<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
							<?
								
							}
					 ?>
					 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>

				</tr>
				<?
				$j++;
						
				}
				?>                
                <tfoot>
	                 <tr bgcolor="<? // echo $bgcolor;?>">
		                 <th width="180"></th>
		                 <th width="70"></th>
		                 <th width="70"></th>
		                 <th width="70"></th>
		                 <th width="70">Total</th>
		                
		                		 <?
		                        foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
		                        {
		                            if($value_s !="")
		                            {
		                                ?>
		                                <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
		                                <?
		                            }
		                        }
		                		?>
		                 <th width="70" align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
		                 </tr>
		                 <tr bgcolor="<? // echo $bgcolor;?>">
		                
		                 <th colspan="5"> Grand Total</th>
		                
		                		 <?
		                		 $grand_color_qty=0;
		                        foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
		                        {
		                            if($value_s !="")
		                            {
		                                ?>
		                                <th width="60" align="right"><? $grand_color_qty+=$job_color_qnty_array[3]['color_total']; echo $job_color_qnty_array[3]['color_total'];?></th>
		                                <?
		                            }
		                        }
		                		?>
		                 <th align="right"><? echo  $grand_color_qty; ?></th>
	                 </tr>
                 
              </tfoot>
		
		</table>  
	  </div>	
	 </div>	 
     <!-- ======================= 2nd Print End ================= -->
		<?
		$tbl_width = 370+(count($job_size_array[1][$order_number])*60);
		?>
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;margin: 0 auto;width: <? echo $tbl_width;?>px">  
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>
					
							
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
			
					<?
					
					$i=1;
					
					foreach($country_color_wise_arr as $country_key=>$country_data)
					{
						
						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array[1][$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>
						<?
						foreach($country_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>			                 	
			                 </td>
							 <td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;
								foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
								{
									
									?>
									<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_qty_arr[$country_key][$key_s]['order_qty'];
								}
							 ?>
							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>
							
							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
								<td align="left">Plan To <? echo ($type==4) ? "Input" : "Output";?></td>					
									 <?
									 $pc_total = 0;
										foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_qty_arr[$country_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>  
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>               
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Qty</td>					
									<?
										$cuttingTotal = 0;
										foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_cutting_qty_arr[$country_key][$key_s];?></td>
												<?
												$cuttingTotal += $country_cutting_qty_arr[$country_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
							 	<td  align="left"><? echo ($type==4) ? "Input" : "Output";?> Balance</td>					
								 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{											
											?>
											<td width="60" align="right"><? echo $bal = $country_size_qty_arr[$country_key][$key_s]["plan_cut"] - $country_cutting_qty_arr[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>
				<br />
	     	</div>
		</div>
	 <!-- ======================= 3rd Print End ================= -->	
	</div>	
     <!-- ======================================== All Print End ================================== -->	
 </div>
	<script> 
	 	setFilterGrid("html_search_1",-1);
	 	setFilterGrid("html_search_2",-1);
	</script>
	<?
}



if($action=="iron_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id");  
	?>
    <div id="data_panel" align="center" style="width:100%">
      
    <? 
		 $sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,
		    d.production_date,f.country_id,d.challan_no
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type='$type'  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 
			 $insert_cond  $item_cond2 order by d.production_date,f.size_order";
		//echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		//print_r($job_size_array);die;
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House </strong><label/>
            
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td align="center"><? echo  $line_name; ?></td>
							 
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>                   
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th>Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   
                <label > <strong>Out Bound:<strong><label/>    
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>    
	 </div>
	 <?
}



if($action=="packing_and_finishing_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	
	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	  sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no
	  from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	  where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	  b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id"); 
	$sql_color_size="SELECT d.country_id, d.color_number_id,d.size_number_id,sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id  group by d.country_id,d.color_number_id,d.size_number_id";
	//  echo $sql_color_size;
	$sql_color_size_data=sql_select($sql_color_size);
	$country_size_arr=array();
	$country_color_size_arr=array();
	foreach($sql_color_size_data as $key=>$vals){
		// $country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["order_qty"] +=$value[csf("order_qty")];
		// $country_size_arr[$value[csf("country_id")]][$value[csf("color_number_id")]][$value[csf("size_number_id")]]["plan_cut"] +=$value[csf("plan_cut")];
		// $country_color_size_arr[$value[csf("country_id")]]["order_qty_color_total"] +=$value[csf("order_qty")];
		// $country_color_size_arr[$value[csf("country_id")]]["plan_qty_color_total"] +=$value[csf("order_qty")];
		$color_size_wise_arr[$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$color_size_wise_arr[$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$color_total_arr["order_qty_color_total"] +=$vals[csf("order_qty")];
		$color_total_arr["plan_cut_color_total"] +=$vals[csf("plan_cut")];
		//==============================================
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$country_color_total_arr[$vals[csf("country_id")]]["order_qty_color_total"] +=$vals[csf("order_qty")];
		$country_color_total_arr[$vals[csf("country_id")]]["plan_cut_color_total"] +=$vals[csf("plan_cut")];

	}
	//   $sql="SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	//   sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no,f.production_quantity as product_qty
	//   from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d,pro_garments_production_mst f
	//   where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and f.po_break_down_id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	//   b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id,f.production_quantity";
	//   echo $sql;
	  
	//   $sql="SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id,
	//   sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no 
	//   from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d
	//   where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and 
	//   b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id";
	//   echo $sql;
	?>
    <div id="data_panel" align="center" style="width:100%">
      
    <? 
	 
	

		 $sql="SELECT  d.production_source,d.serving_company,d.floor_id,d.sewing_line,d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,
		    d.production_date,f.country_id,d.challan_no
			FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f
			WHERE 
			d.po_break_down_id=$order_id  and
			d.id=e.mst_id and
			e.color_size_break_down_id=f.id and
			f.po_break_down_id=$order_id and
			e.production_type='$type'  and
			f.color_number_id=$color_id and
		    e.is_deleted =0 and
			e.status_active =1 and
		    f.is_deleted =0 and
			f.status_active =1 and
		    d.is_deleted =0 and
			d.status_active =1 
			 $insert_cond  $item_cond2 order by d.production_date,f.size_order";
		// echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$sql_data = sql_select($sql);
		$production_details_arr=array();
		$production_size_details_arr=array();
		$packing_and_finshing_arr=array();
		$country_color_wise_arr=array();
	    $country_cutting_qty_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $sql_data as $row)
		{
			if($row[csf('production_source')]==1)
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$floor_qty_arr[$row[csf('production_source')]][$row[csf('floor_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];

				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];
				$packing_and_finshing_arr[$row[csf('country_id')]]+=$row[csf('product_qty')];
				
				
			}
			else
			{
				$job_size_array[$row[csf('production_source')]][$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('production_source')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
				$job_color_array[$row[csf('production_source')]][$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('production_source')]]['color_total']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['production_date']=$row[csf('production_date')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
				$production_details_arr[$row[csf('production_source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('production_source')]][$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];	
				$packing_and_finshing_arr[$row[csf('country_id')]]+=$row[csf('product_qty')];
				
		    
			}
			
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			$grand_color_qty+=$row[csf('product_qty')];
		}
		
		$sql_output_qty="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id, d.production_date,f.country_id,g.production_source FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f, pro_garments_production_mst g,
		pro_garments_production_dtls i WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and e.color_size_break_down_id=f.id and  g.id=i.mst_id and i.color_size_break_down_id=f.id and f.po_break_down_id=$order_id   and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active =1 $insert_cond  order by d.production_date";
	//   echo $sql_output_qty;
		$sql_output_data=sql_select($sql_output_qty);
		// $job_size_array=array();
		$country_color_size_arr=array();
		 $country_cutting_qty_arr=array();
		foreach($sql_output_data as $row){
			$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
			$country_cutting_qty_arr[$row[csf('production_source')]][$row[csf('country_id')]][$row[csf('size_number_id')]]=$row[csf('product_qty')];
			// $grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			// $grand_color_qty+=$row[csf('product_qty')];
			// $country_cutting_qty_arr[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			// $country_cutting_qty_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
			// $country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
			
			
		}
		//  print_r($country_cutting_qty_arr);
	
		 $job_color_tot=0;
		$inhouse_size_total = count($job_size_array[1][$order_number]);
		$inhouse_tbl_width = 600+($inhouse_size_total*60);

		$outbound_size_total = count($job_size_array[3][$order_number]);
		$outbound_tbl_width = 530+($outbound_size_total*60);
		 ?>
		 <!-- 1st Print Start Here -->
		 <div id="details_reports_all">
	 <!-- ======================================== 1st Print Start ================================== -->	
	  <div id="details_reports">
         <table width="810px" align="center" border="1" rules="all" class="rpt_table" >
          <thead>
              <tr>
              <th width="150">Buyer Name</th>
              <th width="100">Job No </th>
              <th width="100">Style Reff.</th>
              <th width="100">Country</th>
              <th width="100">Order No</th>
              <th width="100">Ship Date</th>
              <th width="80">Order Qty</th>
			  <th width="80">Packing & Finishing Qty</th>
			  
              </tr>
          </thead>
          <tbody>
          <?
		  
		    foreach($sql_job as $row)
				{
				 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor;?>">
                       <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
                       <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                       <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
                       <td align="center"><p><? echo $country_arr[$row[csf('country_id')]]; ?></p></td>
                       <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
                       <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                        <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')];?></td>
						
						<td align="right"><? echo  $packing_and_finshing_arr[$row[csf('country_id')]] ; $total_packing_qty+= $packing_and_finshing_arr[$row[csf('country_id')]];?></td>
						
                    </tr>
                    <?
				}
		  ?>
          </tbody>
          <tfoot>
               <tr>
               <th colspan="6">Total</th>
			  
               <th><? echo $total_qty; ?></th>
			   <th align="right"><? echo $total_packing_qty;?></th>
               </tr>
          </tfoot>
       </table>
			<!-- </div>
			</div> -->
      </div>
       <br />


		 <!-- 1st print End -->
		  <!--2nd Print Start Here  -->
        <div id="data_panel" align="" style="width:100%">
        	<script>
				function new_window()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
			<hr>
			<div id="details_reports">
			<div style="text-align: center;"> <strong>In House </strong></div>
            <div style="text-align: center;"> <strong>Order No : <? echo $order_number;?> </strong></div>
			<table width="<? echo $inhouse_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[1][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
			</table>
			<table width="<? echo $inhouse_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_1">
				<?
			
				$i=1;
				$inhouse_floor=array();
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td width="530" colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td width="70" align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td width="70" align="center"><? echo  $floor_arr[$value_c['floor_id']]; ?></td>
							 <td width="70" align="center"><? echo  $line_name; ?></td>
							 
							 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['floor_id'];
							$floor_id=$value_c['floor_id'];;
					}
					?>
                    
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td width="530" colspan="6" align="right"> Floor Total</td>
									
									 <?
											foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td width="70" align="right"><? echo  $job_color_qnty_array[1][$floor_id]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th width="180"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70">Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
		                                    ?>
		                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
		                                    <?
                                        }
                                    }
                            ?>
                             <th width="70" align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                <!-- ===================================== OUT BOUND ================================= -->
                <div style="text-align: center;"> <strong>Out Bound</strong></div>
                <table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						foreach($job_size_array[3][$order_number] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
			</table>
			<table width="<? echo $outbound_tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[3] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td width="180" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td width="70" align="center"><? echo  $supplier_arr[$value_c['serving_company']]; ?></td>
							 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
							 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[3][$key_c][$key_s]['product_qty'] ;?></td>
									<?
										
									}
							 ?>
							 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[3][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th width="180"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70"></th>
                             <th width="70">Total</th>
                            
                            		 <?
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[3][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th width="70" align="right"><? echo  $job_color_qnty_array[3]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                            		 $grand_color_qty=0;
                                    foreach($job_size_array[3][$order_number] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
		                                    ?>
		                                    <th width="60" align="right"><? $grand_color_qty+=$grand_size_qty[$key_s]; echo $grand_size_qty[$key_s];?></th>
		                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>   
				</div> 
			</div>
			<!-- 3rd Print -->

			<?
			$tbl_width = 370+($size_total*60);
			
		?>
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;">  
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>				
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
					<?
					$i=1;
					foreach($country_color_wise_arr as $country_key=>$country_data)
					{
						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>
						<?
						
						foreach($country_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>			                 	
			                 </td>
							 <td width="100" align="center"><? echo $colorname_arr [$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;
								
								foreach($job_size_array[$order_number] as $key_s=>$value_s)
								{
									
									?>
									<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$color_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_qty_arr[$country_key][$color_key][$key_s]['order_qty'];
								}
							 ?>
							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>
							
							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
								<td align="left">Plan To Cut</td>					
									 <?
									 $pc_total = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$color_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_qty_arr[$country_key][$color_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>  
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>               
							 	<td  align="left">Finishing Qty</td>					
									<?
										$cuttingTotal = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_cutting_qty_arr[$country_key][$color_key];?></td>
												<?
												$cuttingTotal += $country_cutting_qty_arr[$country_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
							 	<td  align="left">Cutting Balance</td>					
								 <?
									foreach($job_size_array[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{											
											?>
											<td width="60" align="right"><? echo $bal = $country_size_qty_arr[$country_key][$key_s]["plan_cut"] - $country_cutting_qty_arr[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>
				<br />
			
		
				<!-- 3rd print End -->
		
			<script type="text/javascript">
				setFilterGrid("html_search_1",-1);
				setFilterGrid("html_search_2",-1);
			</script>
	 </div>
	 <?
}



if($action=="ex_factory_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$company_arr=return_library_array( "select id, company_name from  lib_company", "id", "company_name"  );
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name"); 
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=="") $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=="") $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";
	
 
	?>
    <div id="data_panel" align="center" style="width:100%">
      
    <?

		$ex_factory_sql="SELECT a.id,a.country_id,a.challan_no,a.po_break_down_id as order_id, a.item_number_id as item_id,a.ex_factory_date, c.color_number_id as color_id,m.source,c.size_number_id, c.color_number_id,m.delivery_floor_id, 
			m.company_id,
	    sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date='".$insert_date."' THEN b.production_qnty ELSE 0 END) AS exf_qnty_today,
		sum(CASE WHEN m.entry_form=85 and a.ex_factory_date='".$insert_date."' THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_today,
		sum(CASE WHEN m.entry_form!=85 and a.ex_factory_date<'".$insert_date."' THEN b.production_qnty ELSE 0 END) AS exf_qnty_pre,
		sum(CASE WHEN m.entry_form=85 and a.ex_factory_date<'".$insert_date."' THEN b.production_qnty ELSE 0 END) AS ret_exf_qnty_pre
		from pro_ex_factory_delivery_mst m, pro_ex_factory_mst a, pro_ex_factory_dtls b, wo_po_color_size_breakdown c
		where  a.po_break_down_id = $order_id and a.item_number_id=$item_id and c.color_number_id=$color_id and
		a.ex_factory_date='".$insert_date."' and  m.id=a.delivery_mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and m.status_active=1 and m.is_deleted=0 
		group by  a.id,a.country_id,a.po_break_down_id ,a.challan_no, a.item_number_id,a.ex_factory_date, c.color_number_id ,m.source,c.size_number_id, c.color_number_id,m.delivery_floor_id,m.company_id";
	// echo $ex_factory_sql;	
	$ex_factory_sql_result=sql_select($ex_factory_sql);
	// echo "<pre>";
	// print_r($ex_factory_sql_result);
	$production_data = array();	 
		//echo $sql;die;
		$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
		$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
		$job_size_array=array();
		$job_size_qnty_array=array();
		$job_color_array=array();
		$job_color_qnty_array=array();
		$job_color_size_qnty_array=array();
		$production_details_arr=array();
		$production_size_details_arr=array();
		$floor_qty_arr=array();
		$grand_size_qty=array();
		//$grand_color_qty=array();
		foreach( $ex_factory_sql_result as $row)
		{
			if($row[csf('source')]==1)
			{
				$job_size_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('source')]][$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
				$floor_qty_arr[$row[csf('source')]][$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
				$job_color_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('source')]]['color_total']+=$row[csf('exf_qnty_today')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['delivery_floor_id']=$row[csf('delivery_floor_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['sewing_line']=$row[csf('sewing_line')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];
				$production_size_details_arr[$row[csf('source')]][$row[csf('id')]][$row[csf('size_number_id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];
			}
			else
			{
				$job_size_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$job_size_qnty_array[$row[csf('source')]][$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
				$job_color_array[$row[csf('source')]][$row[csf('order_id')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				$job_color_qnty_array[$row[csf('source')]]['color_total']+=$row[csf('exf_qnty_today')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['country']=$row[csf('country_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['company_id']=$row[csf('company_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['delivery_floor_id']=$row[csf('delivery_floor_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['color']=$row[csf('color_number_id')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];
				$production_details_arr[$row[csf('source')]][$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
				$production_size_details_arr[$row[csf('source')]][$row[csf('id')]][$row[csf('size_number_id')]]['exf_qnty_today']+=$row[csf('exf_qnty_today')];	
			}
			$grand_size_qty[$row[csf('size_number_id')]]+=$row[csf('exf_qnty_today')];
			$grand_color_qty+=$row[csf('exf_qnty_today')];
		}
		// print_r($production_size_details_arr);
		// print_r($job_size_array[1]);
		 $job_color_tot=0;
		 ?> 
        <div id="data_panel" align="" style="width:100%">
			<label> <strong>In House </strong><label/>
            
			<table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Country</th>
                        <th width="70">Unit Name</th>
                        <th width="70">Line No</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<? 
						foreach($job_size_array[1][$order_id] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$i=1;
				$inhouse_floor=array();
				// echo "<pre>";
				// print_r($production_details_arr);
				foreach($production_details_arr[1] as $key_c=>$value_c)
					{
						
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($i!=1)
						{
							
							if(!in_array($value_c['delivery_floor_id'],$inhouse_floor))
								{
								?>
								 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <? //print_r($job_size_array[1][$order_id]);
											foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$floor_id][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>		
								<?	
								}
							}
							
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  $floor_arr[$value_c['delivery_floor_id']]; ?></td>
							 <td align="center"><? echo  $line_name; ?></td>
							 
							 <td align="center"><? echo  change_date_format($value_c['ex_factory_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
									{
										
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[1][$key_c][$key_s]['exf_qnty_today'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['exf_qnty_today']; $job_color_tot+=$job_color_qnty_array[1][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							
							$i++;
							$inhouse_floor[]=$value_c['delivery_floor_id'];
							$floor_id=$value_c['delivery_floor_id'];;
					}
					?>
                    
                    
                    		 <tr bgcolor="#FFFFE8">
									 <td colspan="6" align="right"> Floor Total</td>
									
									 <? 
											foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
											{
												if($value_s !="")
												{
											?>
											<td width="60" align="right"><? echo $floor_qty_arr[1][$key_s];?></td>
											<?
												}
											}
									?>
									 <td align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></td>
								 </tr>	
                                 
                                 
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th></th>
                             <th>Total</th>
                            
                             <?
                                    foreach($job_size_array[1][$order_id] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[1][$key_s];?></th>
                                    <?
                                        }
                                    }
                            ?>
                             <th align="right"><? echo  $job_color_qnty_array[1]['color_total']; ?></th>
                             </tr>
                          </tfoot>
					</table>
                   <!-- =================================== Out Bound Start ==========================================-->
                <label > <strong>Out Bound:<strong><label/>    
                <table width="" align="center" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="180">Color</th>
                        <th width="70">Company</th>
                        <th width="70">Country</th>
                        <th width="70">Date</th>
                        <th width="70">Challan</th>
						<?
						// echo "<pre>";
						// print_r($job_size_array[2]);
						foreach($job_size_array[2][$order_id] as $key=>$value)
						{
							if($value !="")
							{
							?>
							<th width="60"><? echo $itemSizeArr[$value];?></th>
							<?
							}
						}
						?>
                        <th width="70">Color Total</th>
					</tr>
				</thead>
				<?
			
				$j=1;
				$inhouse_floor=array();
				foreach($production_details_arr[2] as $key_c=>$value_c)
					{
					if($prod_reso_allo==1)	
						{
						$line_name= $lineArr[$prod_reso_arr[$value_c['sewing_line']]];
					    }
						else 
						{
						$line_name= $lineArr[$value_c['sewing_line']];
						}	
						if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
							?>
                            
                            
                            
							 <tr bgcolor="<? echo $bgcolor;?>">
							 <td align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
                             <td align="center"><? echo  $company_arr[$value_c['company_id']]; ?></td>
							 <td align="center"><? echo  $country_arr[$value_c['country']]; ?></td>
							 <td align="center"><? echo  change_date_format($value_c['ex_factory_date']); ?></td>
							 <td align="right"><?  echo  $value_c['challan_no']; ?></td>
							 <?
									foreach($job_size_array[2][$order_id] as $key_s=>$value_s)
									{
									// echo $key_c;	
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[2][$key_c][$key_s]['exf_qnty_today'] ;?></td>
									<?
										
									}
							 ?>
							 <td align="right"><? echo  $value_c['exf_qnty_today']; $job_color_tot+=$job_color_qnty_array[2][$value_po][$value_c]; ?></td>
			
							 </tr>
							<?
							$j++;
							
					}
					?>
                    
                            <tfoot>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                             <th></th>
                             <th></th>
                             <th></th>
                              <th></th>
                             <th>Total</th>
                            
                            		 <?
                                    foreach($job_size_array[2][$order_id] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $job_size_qnty_array[2][$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $job_color_qnty_array[2]['color_total']; ?></th>
                             </tr>
                             <tr bgcolor="<? // echo $bgcolor;?>">
                            
                             <th colspan="5"> Grand Total</th>
                            
                            		 <?
                                    foreach($job_size_array[2][$order_id] as $key_s=>$value_s)
                                    {
                                        if($value_s !="")
                                        {
                                    ?>
                                    <th width="60" align="right"><? echo $grand_size_qty[$key_s];?></th>
                                    <?
                                        }
                                    }
                            		?>
                             <th align="right"><? echo  $grand_color_qty; ?></th>
                             </tr>
                             
                          </tfoot>
					
					</table>    
	 </div>
	 <?
}


if($action=="cutting_popup")
{
	echo load_html_head_contents("Job Color Size","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	if($item_id=='') $item_cond="";else $item_cond="and d.item_number_id=$item_id";
	if($item_id=='') $item_cond2="";else $item_cond2="and f.item_number_id=$item_id";

	$sql_job=sql_select("SELECT  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,d.color_number_id, sum(d.order_quantity) as order_qty,b.buyer_name,b.style_ref_no ,a.excess_cut from wo_po_break_down a,wo_po_details_master b, wo_po_color_size_breakdown d where a.job_no_mst=b.job_no and a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and b.company_name=$company_id and d.color_number_id=$color_id  $item_cond  group by  a.id,a.job_no_mst,a.po_number,d.country_id,d.country_ship_date,b.buyer_name,b.style_ref_no,d.color_number_id,a.excess_cut");

	$sql_color_size=sql_select("SELECT d.country_id, d.color_number_id,d.size_number_id, sum(d.plan_cut_qnty) as plan_cut, sum(d.order_quantity) as order_qty  from wo_po_break_down a, wo_po_color_size_breakdown d where a.id=d.po_break_down_id and a.is_deleted=0 and a.status_active=1  and d.po_break_down_id='$order_id'  and d.status_active=1 and d.is_deleted=0 and d.color_number_id=$color_id  group by d.country_id,d.color_number_id,d.size_number_id "); 
	$country_size_qty_arr = array();
	$country_color_total_arr = array();
	foreach($sql_color_size as $key=>$vals)
	{
		$color_size_wise_arr[$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$color_size_wise_arr[$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$color_total_arr["order_qty_color_total"] +=$vals[csf("order_qty")];
		$color_total_arr["plan_cut_color_total"] +=$vals[csf("plan_cut")];
		//==============================================
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["order_qty"] +=$vals[csf("order_qty")];
		$country_size_qty_arr[$vals[csf("country_id")]][$vals[csf("size_number_id")]]["plan_cut"] +=$vals[csf("plan_cut")];
		$country_color_total_arr[$vals[csf("country_id")]]["order_qty_color_total"] +=$vals[csf("order_qty")];
		$country_color_total_arr[$vals[csf("country_id")]]["plan_cut_color_total"] +=$vals[csf("plan_cut")];
 	}
	

	$sql="SELECT  d.id,e.production_qnty as product_qty,f.size_number_id,f.color_number_id,d.challan_no,d.table_no,
	d.floor_id,d.production_date,f.country_id,d.serving_company,d.cut_no FROM pro_garments_production_mst d,pro_garments_production_dtls e,wo_po_color_size_breakdown f WHERE d.po_break_down_id=$order_id  and d.id=e.mst_id and d.po_break_down_id=f.po_break_down_id and e.color_size_break_down_id=f.id and f.po_break_down_id=$order_id and d.production_type=$type and e.production_type=$type and f.color_number_id=$color_id and e.is_deleted =0 and e.status_active =1 and f.is_deleted =0 and f.status_active =1 and d.is_deleted =0 and d.status_active=1 $insert_cond $item_cond2 order by f.size_order,d.production_date"; 
	//  echo $sql;
	$colorArr = return_library_array("select id,color_name from lib_color","id","color_name");
	$itemSizeArr = return_library_array("select id,size_name from  lib_size ","id","size_name");
	$job_size_array=array();
	$job_size_qnty_array=array();
	$job_color_array=array();
	$job_color_qnty_array=array();
	$job_color_size_qnty_array=array();
	$sql_data = sql_select($sql);
	$production_details_arr=array();
	$production_size_details_arr=array();
	$cutting_qty_arr=array();
	$country_color_wise_arr=array();
	$country_cutting_qty_arr=array();
	foreach( $sql_data as $row)
	{
		$job_size_array[$order_number][$row[csf('size_number_id')]]=$row[csf('size_number_id')];
		$job_size_qnty_array[$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		$job_color_array[$order_number][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		$job_color_qnty_array['color_total']+=$row[csf('product_qty')];
		//$job_color_size_qnty_array[$order_number][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
		
		$production_details_arr[$row[csf('id')]]['country']=$row[csf('country_id')];
		$production_details_arr[$row[csf('id')]]['color']=$row[csf('color_number_id')];
		$production_details_arr[$row[csf('id')]]['production_date']=$row[csf('production_date')];
		$production_details_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
		$production_details_arr[$row[csf('id')]]['serving_company']=$row[csf('serving_company')];
		$production_details_arr[$row[csf('id')]]['cut_no']=$row[csf('cut_no')];
		$production_details_arr[$row[csf('id')]]['floor_id']=$row[csf('floor_id')];
		$production_details_arr[$row[csf('id')]]['table_no']=$row[csf('table_no')];
		$production_details_arr[$row[csf('id')]]['product_qty']+=$row[csf('product_qty')];
		$cutting_qty_arr[$row[csf('country_id')]]+=$row[csf('product_qty')];
		//$production_details_arr[$row[csf('id')]]['size']=$row[csf('size_number_id')];
		$production_size_details_arr[$row[csf('id')]][$row[csf('size_number_id')]]['product_qty']+=$row[csf('product_qty')];

		$country_color_wise_arr[$row[csf('country_id')]][$row[csf('color_number_id')]]['country_qty']+=$row[csf('product_qty')];
		$country_cutting_qty_arr[$row[csf('country_id')]][$row[csf('size_number_id')]]+=$row[csf('product_qty')];
	} 
	?>
    <div id="data_panel" align="center" style="width:920px">
    	<script>
    		function new_window_all()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('details_reports').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
			function new_window()
			{
				$(".flt").css("display","none");
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write(document.getElementById('order_details').innerHTML);
				d.close();
				$(".flt").css("display","block");
			}
		</script>
		<!-- <input type="button" value="Print All" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window_all()" /> -->
		<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window_all()" />
	</div>
	<hr>
	<div id="details_reports">
		<div id="order_details">
		    <div style="width:920px; margin: 0 auto;">
		    	 <table width="920px" align="center" border="" rules="all" class="" >
			          <thead> 
				              <tr>
					              <th colspan="9" align="center"><? echo $company_arr[$company_id]; ?></th>
					               
				              </tr>
				              <tr>
					              <th colspan="9" align="center">Cutting report job card wise</th>
					               
				              </tr>
			          </thead>
		          </table>
		    </div>
		    <br>
	       	<div  style="width:920px;margin: 0 auto;">
	         	<table width="900px" align="center" border="1" rules="all" class="rpt_table" >
		          	<thead>
		              	<tr>
			              	<th width="200">Buyer Name</th>
			              	<th width="100">Job No </th>
			              	<th width="100">Style Reff.</th>
			              	<th width="100">Country</th>
			              	<th width="100">Order No</th>
			              	<th width="100">Ship Date</th>
			             	<th width="100">Order Qty</th>
			              	<th width="100">Cutting Qty</th>
		             	</tr>
		          	</thead>
	          		<tbody>
			          	<?		  
					    foreach($sql_job as $row)
						{
						 // if($l%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
			                <tr bgcolor="<? echo $bgcolor;?>">
			                   <td align="center"><? echo $buyer_short_library[$row[csf('buyer_name')]]; ?></td>
			                   <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
			                   <td align="center"><? echo $row[csf('style_ref_no')]; ?></td>
			                   <td align="left"><p><? echo $country_arr[$row[csf('country_id')]]."/".$country_code_arr[$row[csf('country_id')]]; ?></p></td>
			                   <td align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
			                   <td align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
			                    <td align="right"><? echo $row[csf('order_qty')]; $total_qty+=$row[csf('order_qty')]; ?></td>
			                    <td align="right"><? echo $cutting_qty_arr[$row[csf('country_id')]]; $total_cutting_qty+=$cutting_qty_arr[$row[csf('country_id')]]; ?></td>
			                </tr>
			                <?
						}
					  	?>
	          		</tbody>
	           		<tfoot>
		               	<tr>
			               <th colspan="6">Total</th>
			               <th><? echo $total_qty; ?></th>
			               <th><? echo $total_cutting_qty; ?></th>
		               	</tr>
	            	</tfoot>
	       		</table>
	      	</div>
	    </div>
       	<br />
	    <?    	 
			// print_r($production_size_details_arr);die;
			$size_total = count($job_size_array[$order_number]);
			$tbl_width = 740+($size_total*60);

			 $job_color_tot=0;
			 ?> 
	        <div id="data_panel" align="center" style="width:100%">
	        	<script>
					function new_window2()
					{
						$(".flt").css("display","none");
						var w = window.open("Surprise", "#");
						var d = w.document.open();
						d.write(document.getElementById('breakdown_details').innerHTML);
						d.close();
						$(".flt").css("display","block");
					}
				</script>
				<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window2()" />
		       	<div id="breakdown_details" style="text-align: center;">  
					<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
					 <strong>Po Number: <? echo $order_number; ?></strong></div>
					<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" >
						<thead>
							<tr>
								<th width="120">Cutting Company</th>
								<th width="100">Cutting No</th>
								<th width="100">Color</th>
		                        <th width="70">Country</th>
								<th width="70">Floor</th>
								<th width="70">Table</th>
		                        <th width="70">Date</th>
		                        <th width="70">Challan</th>
								<?
								foreach($job_size_array[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Color Total</th>
							</tr>
						</thead>
					</table>
					<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_1">
						<?
						$i=1;
						foreach($production_details_arr as $key_c=>$value_c)
						{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//if($value_c != "")
						//{
						?>
						 <tr bgcolor="<? echo $bgcolor;?>">
						 <td width="120" align="center"><? echo $company_arr[$value_c['serving_company']]; ?></td>
						 <td width="100" align="center"><? echo  $value_c['cut_no']; ?></td>
						 <td width="100" align="center"><? echo  $colorname_arr[$value_c['color']]; ?></td>
		                 <td width="70" align="center"><? echo  $country_arr[$value_c['country']]."/".$country_code_arr[$value_c['country']]; ?></td>
							<?php
								$floorn_name= return_field_value("floor_name","lib_prod_floor","id='".$value_c['floor_id']."'");
								$table_name= return_field_value("table_name","lib_table_entry","id='".$value_c['table_no']."'");
							?>
						 <td width="70" align="center"><? echo $floorn_name; ?></td>
						 <td width="70" align="center"><? echo $table_name; ?></td>
		                 <td width="70" align="center"><? echo  change_date_format($value_c['production_date']); ?></td>
						 <td width="70" align="right"><?  echo  $value_c['challan_no']; ?></td>
						 <?
								foreach($job_size_array[$order_number] as $key_s=>$value_s)
								{
									
									?>
									<td width="60" align="right"><? echo $production_size_details_arr[$key_c][$key_s]['product_qty'] ;?></td>
									<?
									
								}
						 ?>
						 <td width="70" align="right"><? echo  $value_c['product_qty']; $job_color_tot+=$job_color_qnty_array[$value_po][$value_c]; ?></td>

						 </tr>
						<?
						$i++;
						//}
						}
						?>
						<tfoot>
							 <tr>                 
								 <th colspan="8" width="530" align="left">Total</th>					
									 <?
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<th width="60" align="right"><? echo $job_size_qnty_array[$key_s];?></th>
												<?
											}
										}
									?>
				                 <th width="70" align="right"><? echo  $job_color_qnty_array['color_total']; ?></th>
							 </tr>

							  <tr>                 
								 <th width="530" colspan="8" align="left">Plan To Cut (AVG <?  echo $sql_job[0][csf("excess_cut")];?>)%</th>					
									 <?
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
										?>
										<th width="60" align="right"><? echo $color_size_wise_arr[$key_s]["plan_cut"];?></th>
										<?
											}
										}
									?>
				                 <th width="70" align="right"><? echo   $color_total_arr["plan_cut_color_total"]; ?></th>
							 </tr>

						  	<tr>                 
							 <th width="530" colspan="8" align="left">Cutting Balance</th>					
								 <?
									foreach($job_size_array[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{
											$size_bal=$color_size_wise_arr[$key_s]["plan_cut"]- $job_size_qnty_array[$key_s];
											$color_cond=($size_bal>=0)? " color:black; " : " color:crimson;";
											$color_bal_tot=$color_total_arr["plan_cut_color_total"]- $job_color_qnty_array['color_total'];
											$color_bal_cond=($color_bal_tot>=0)? " color:black; " : "color:crimson;";
											?>
											<th style="<? echo $color_cond;?>" width="60" align="right"><? echo $size_bal;?></th>
											<?
										}
									}
								?>
			                 <th width="70" style="<? echo $color_bal_cond;?>" align="right"><? echo  $color_bal_tot; ?></th>
						 	</tr>
						  	<tr>                 
							 	<th width="530" colspan="8" align="left">Order Qty</th>					
									<?
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
										?>
										<th width="60" align="right"><? echo $color_size_wise_arr[$key_s]["order_qty"];?></th>
										<?
											}
										}
									?>
			                 	<th width="70" align="right"><? echo  $color_total_arr["order_qty_color_total"]; ?></th>
						 	</tr>

					  	</tfoot>
					</table>
					<br />
		     	</div>
			</div>
		<script type="text/javascript">
		 	setFilterGrid("html_search_1",-1);
		</script>
		<!-- ======================================== -->
		<?
			$tbl_width = 370+($size_total*60);
		?>
		<div id="data_panel" align="center" style="width:100%">
        	<script>
				function new_window3()
				{
					$(".flt").css("display","none");
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('country_wise_breakdown_details').innerHTML);
					d.close();
					$(".flt").css("display","block");
				}
			</script>
			<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window3()" />
	       	<div id="country_wise_breakdown_details" style="text-align: center;">  
				<div style="text-align: center;"> <strong>Style : <? echo $sql_job[0][csf('style_ref_no')]; ?></strong>, &nbsp;
				<strong>Po Number: <? echo $order_number; ?></strong></div>				
				<table width="<? echo $tbl_width;?>" align="center" border="1" rules="all" class="rpt_table" id="html_search_2">
					<?
					$i=1;
					foreach($country_color_wise_arr as $country_key=>$country_data)
					{
						?>
						<thead>
							<tr>
		                        <th width="100">Country</th>
		                        <th width="100">Color</th>
		                        <th width="100">Size</th>
								<?
								foreach($job_size_array[$order_number] as $key=>$value)
								{
									if($value !="")
									{
										?>
										<th width="60"><? echo $itemSizeArr[$value];?></th>
										<?
									}
								}
								?>
		                        <th width="70">Total Qty.</th>
							</tr>
						</thead>
						<?
						foreach($country_data as $color_key=>$val)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							//if($value_c != "")
							//{
							?>
							 <tr bgcolor="<? echo $bgcolor;?>">
			                 <td width="100" rowspan="4" valign="middle" align="left" style="word-break: break-all;word-wrap: break-word;">
			                 <? echo  $country_arr[$country_key]."/".$country_code_arr[$country_key]; ?>			                 	
			                 </td>
							 <td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>
							 <td width="100" align="left">Order Qnty</td>
							 <?
							 	$totalQty = 0;
								foreach($job_size_array[$order_number] as $key_s=>$value_s)
								{
									
									?>
									<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]['order_qty'];?></td>
									<?
									$totalQty += $country_size_qty_arr[$country_key][$key_s]['order_qty'];
								}
							 ?>
							 <td width="70" align="right"><? echo  $totalQty; ?></td>

							 </tr>
							
							<tr>
							  	<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
								<td align="left">Plan To Cut</td>					
									 <?
									 $pc_total = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_size_qty_arr[$country_key][$key_s]["plan_cut"];?></td>
												<?
												$pc_total += $country_size_qty_arr[$country_key][$key_s]["plan_cut"];
											}
										}
									?>
				                <td width="70" align="right"><? echo $pc_total; ?></td>
							</tr>
						  	<tr>  
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>               
							 	<td  align="left">Cutting Qty</td>					
									<?
										$cuttingTotal = 0;
										foreach($job_size_array[$order_number] as $key_s=>$value_s)
										{
											if($value_s !="")
											{
												?>
												<td width="60" align="right"><? echo $country_cutting_qty_arr[$country_key][$key_s];?></td>
												<?
												$cuttingTotal += $country_cutting_qty_arr[$country_key][$key_s];
											}
										}
									?>
			                 	<td width="70" align="right"><? echo  $cuttingTotal; ?></td>
						 	</tr>

						  	<tr>
						  		<td width="100" align="center"><? echo  $colorname_arr[$color_key]; ?></td>                 
							 	<td  align="left">Cutting Balance</td>					
								 <?
									foreach($job_size_array[$order_number] as $key_s=>$value_s)
									{
										if($value_s !="")
										{											
											?>
											<td width="60" align="right"><? echo $bal = $country_size_qty_arr[$country_key][$key_s]["plan_cut"] - $country_cutting_qty_arr[$country_key][$key_s];?></td>
											<?
											$balance += $bal;
										}
									}
								?>
			                 	<td  align="right"><? echo  $balance; ?></td>
						 	</tr>
						 	<?
							$i++;
							//}
						}
					}
					?>
				</table>
				<br />
	     	</div>
		</div>
	</div>
	<?
}

if($action=="total_fabric_recv_qty")//total_fabric_recv_qty
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	/*	echo $prod_date.'_';
	echo $order_id.'_';
	echo $color_id.'_';*/
	
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no"); 
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	
	  $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date <= '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity!=0 and  b.is_deleted=0  and a.color_id=$color_id AND a.po_breakdown_id in (".str_replace("'","",$order_id).") order by c.issue_number ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();  
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];
			
		//	$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
		//	-$value[csf("trans_out_pre")];
		
			$fabric_pre_qty[$value[csf("color_id")]]['fab_qty']+=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]-$value[csf("trans_out_pre")];
			//-$value[csf("trans_out_pre")];
			$fabric_pre_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_pre_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_pre_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];
				
		}*/
	//	print_r($fabric_pre_qty);
	?>
	
    
     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:540px">  
		<table width="540" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;
        // foreach($fabric_today_qty as $order_id=>$order_data)
		 //{
			 //foreach($fabric_pre_qty as $color_key=>$color_val)
			 
			 foreach($result as $value)
			 {
				  if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
         <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><? echo  $k;?> </td>
            <td width="130"><? echo  $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"> <? echo  $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"> <? echo  $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);//number_format($color_val['fab_qty']+$color_val['fabric_qty'],2);?> </td>
            
         </tr>
         <?
		 $total_fab_qty+=$value[csf("fabric_qty")];//$color_val['fab_qty']+$color_val['fabric_qty'];
		 $k++;
			 }
		 //}
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th> 
         </tr>
         </table>
                        
  </fieldset>
  </div>
	<?
	//exit();
	
}
if($action=="today_fabric_recv_qty")//
{
	echo load_html_head_contents("Today Fabric Recv Qty","../../../", 1, 1, $unicode);
    extract($_REQUEST);
	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor", "id", "floor_name"  );
	$batch_noArr = return_library_array("select id,batch_no from pro_batch_create_mst","id","batch_no"); 
	$color_name_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from   lib_supplier", "id", "supplier_name");
	//$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name=$company_id and variable_list=23 and is_deleted=0 and status_active=1");
	//print_r($supplier_arr);
	//echo $prod_date;
    if($today_total==1)  $insert_cond="   and  d.production_date='$insert_date'";
    if($today_total==2)  $insert_cond="   and  d.production_date<='$insert_date'";
	
	   $sql_fabric_qty=("SELECT a.po_breakdown_id,a.color_id,c.issue_number,c.issue_date,b.pi_wo_batch_no,
	
		CASE WHEN b.transaction_date = '".$prod_date."' AND a.trans_type =2  AND a.entry_form =18 and b.item_category=2 THEN a.quantity
	    ELSE 0 END  AS fabric_qty
		
		FROM order_wise_pro_details a,inv_transaction b,inv_issue_master c
	    WHERE a.trans_id = b.id 
		and b.status_active=1 and a.entry_form in(18,15,16,37) and c.id=b.mst_id and a.quantity>0 and  b.is_deleted=0 and a.color_id=$color_id  AND a.po_breakdown_id in (".str_replace("'","",$order_id).") ");
		//AND a.po_breakdown_id in (".str_replace("'","",$po_number_id).")
		//echo  $sql_fabric_qty;
		$result=sql_select($sql_fabric_qty);
		$fabric_pre_qty=array();
		$fabric_today_qty=array();  
		$total_fabric=array();
		$fabric_balance=array();
		$fabric_wip=array();
		/*foreach($result as $value)
		{
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['issue']=$value[csf("grey_fabric_issue")]-$value[csf("grey_fabric_issue_return")];
			//$fabric_wip[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]['receive']=$value[csf("finish_fabric_rece")]-$value[csf("finish_fabric_rece_return")];
			
			//$fabric_pre_qty[$value[csf("po_breakdown_id")]][$value[csf("color_id")]]=$value[csf("fabric_qty_pre")]+$value[csf("trans_in_pre")]
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['fabric_qty']+=$value[csf("fabric_qty")]+$value[csf("trans_in_qty")]
			-$value[csf("trans_out_qty")];
			//-$value[csf("trans_out_pre")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_id']=$value[csf("issue_number")];
			$fabric_today_qty[$value[csf("color_id")]]['issue_date']=$value[csf("issue_date")];
			$fabric_today_qty[$value[csf("color_id")]]['batch_no']=$value[csf("pi_wo_batch_no")];
				
		}*/
		
	?>
	
    
     <div id="data_panel" align="center" style="width:100%">
       <fieldset  style="width:550px">  
		<table width="520" align="center" border="1" rules="all" class="rpt_table"   >
		<thead>
			<tr>
                <th width="30">SL</th>
                <th width="130">ISSUE ID</th>
                <th width="80">Issue Date</th>
                <th width="100">BATCH NO</th>
                <th width="100">COLOR NAME</th>
                <th width="80">Recv. Qty</th>
            </tr>
         </thead>
         <?
		 $total_fab_qty=0;
		 $k=1;
        
			 //foreach($fabric_today_qty as $color_key=>$color_val)
			 foreach($result as $value)
			 {
				 if($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
				
				 if($value[csf("fabric_qty")]>0)
				 {
				
		 ?>
        <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k; ?>">
         	<td width="30"><?   echo  	$k;?> </td>
            <td width="130"><? 	echo    $value[csf("issue_number")];//$color_val['issue_id'];?>  </td>
            <td width="80"><? 	echo   change_date_format($value[csf("issue_date")]);//change_date_format($color_val['issue_date']);?> </td>
            <td width="100"><? 	echo   $batch_noArr[$value[csf("pi_wo_batch_no")]];//$batch_noArr[$color_val['batch_no']];?></td>
            <td width="100"><? 	echo   $color_name_arr[$value[csf("color_id")]];//$color_name_arr[$color_key];?></td>
            <td width="80" align="right"><? echo  number_format($value[csf("fabric_qty")],2);// number_format($color_val['fabric_qty'],2);?> </td>
            
         </tr>
         <?
		  $total_fab_qty+=$value[csf("fabric_qty")]; //new
		  //$total_fab_qty+=$color_val['fabric_qty'];//old
		 $k++;
			 	}
			  }
		 ?>
         <tr>
         <tfoot>
         <th align="right" colspan="5">Total</th><th align="right"> <? echo number_format($total_fab_qty,2);?></th> 
         </tr>
         </table>
                        
  </fieldset>
  </div>
	<?
	//	exit();
	
}