<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
require_once('../../includes/class.reports.php');
require_once('../../includes/class.yarns.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];




if ($action=="job_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
        function js_set_value(str)
        {
			//alert(str);
            $("#hide_job_no").val(str);
            parent.emailwindow.hide(); 
        }
    </script>
<?
	
	
		if($db_type==0)
		{
			$year_field="YEAR(insert_date)";
		}
		else
		{
			$year_field="to_char(insert_date,'YYYY')";
		}
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	
	$arr=array (2=>$company_library,3=>$buyer_arr);
	$sql= "select a.job_no_prefix_num, a.job_no, a.company_name,a.buyer_name,a.style_ref_no,$year_field as year from wo_po_details_master a where a.company_name=$companyID and status_active=1 and is_deleted=0 order by a.id";
	//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No", "70,70,120,100,100","570","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no", "","setFilterGrid('list_view',-1)",'0,0,0,0,0');
	echo "<input type='hidden' id='hide_job_no' />";
	
	exit();
}

if ($action=='report_generate')
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$addressArr=return_library_array( "select id,city from lib_company where id=$cbo_company_name",'id','city');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$fab_description=sql_select( "select job_no, fabric_description,avg_cons,color_type_id from wo_pre_cost_fabric_cost_dtls where job_no='$txt_job_no' and  status_active=1 and is_deleted=0");
	//echo "select cons_dzn_gmts from wo_pre_cost_embe_cost_dtls where job_no='$txt_job_no'";
	$emb_cost=sql_select( "select cons_dzn_gmts from wo_pre_cost_embe_cost_dtls where job_no='$txt_job_no'");
	$cons=sql_select( "select fab_knit_req_kg from wo_pre_cost_sum_dtls where job_no='$txt_job_no'");
	//print_r($fab_arr);
	$factory_merchand=return_library_array("select id,team_member_name from lib_mkt_team_member_info where  status_active =1 and is_deleted=0 order by team_member_name","id","team_member_name");
	
	
	/*echo "select a.id,a.job_no,a.job_no_prefix,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,
a.bh_merchant,a.packing,a.remarks,a.ship_mode,a.order_uom,a.gmts_item_id,a.pro_sub_dep,a.total_set_qnty,a.set_smv,a.season_buyer_wise,a.quotation_id,a.job_quantity,a.order_uom,a.avg_unit_price,a.currency_id,a.total_price,a.factory_marchant,a.quotation_id,b.is_confirmed,b.po_number,b.po_received_date,b.pub_shipment_date,b.shipment_date,b.po_quantity,b.unit_price,b.po_total_price,b.excess_cut,b.plan_cut,(b.pub_shipment_date-b.po_received_date) as  date_diff,b.status_active,b.id
 from wo_po_details_master a ,wo_po_break_down b  where a.job_no=b.job_no_mst and  a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";*/
$data_array=sql_select("select a.job_no,a.id,a.job_no_prefix,a.job_no_prefix_num,a.company_name,a.buyer_name,a.style_ref_no,
a.bh_merchant,a.packing,a.remarks,a.ship_mode,a.order_uom,a.gmts_item_id,a.pro_sub_dep,a.total_set_qnty,a.set_smv,a.season_buyer_wise,a.quotation_id,a.job_quantity,a.order_uom,a.avg_unit_price,a.currency_id,a.total_price,a.factory_marchant,a.quotation_id,b.is_confirmed,b.po_number,b.po_received_date,b.pub_shipment_date,b.shipment_date,b.shiping_status,b.po_quantity,b.unit_price,b.po_total_price,b.excess_cut,b.plan_cut,(b.pub_shipment_date-b.po_received_date) as  date_diff,b.status_active,b.id
 from wo_po_details_master a ,wo_po_break_down b  where a.job_no=b.job_no_mst and  a.job_no='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

$projected_po_date=sql_select("select job_no,po_received_date from wo_po_update_log where job_no='$txt_job_no'");
?>

<div id="scroll_body" style="margin-left:50px;">

 <table cellspacing="0" cellpadding="0" style=" border-color:transparent;"  width="100%"  >
	<tr>
        <td colspan="5" align="center"><strong style="font-size:20px;"><? echo $company_library[$cbo_company_name];?></strong></td>
    </tr>
	<tr>
        <td colspan="5" align="center"><strong style="font-size:16px;" ><? echo $addressArr[$cbo_company_name];?></strong></td>
    </tr>
	<tr>
        <td colspan="5" align="center"><strong style="font-size:16px;" >Order Status report</strong></td>
    </tr>
</table>
<fieldset>
<h3>Job Details: <? echo $txt_job_no;?></h3>
<table width="1000" border="1" rules="all" class="rpt_table" style="table-layout: fixed;">
	<thead>
    <tr>
		<th width="100">Compnay</th>
		<th width="80">Buyer</th>
		<th width="80">Season</th>
		<th width="80">PO No</th>
		<th width="80">style no</th>
		<th width="80">Item</th>
		<th width="150">Fab Type</th>
		<th width="80">Lead Time</th>
		<th width="80">Yarn Dyed</th>
		<th width="80">Print Emb wash</th>
      </tr>
	</thead>
    <tbody>
    <? 
    foreach($data_array as $row)
	{
	?>
    	<tr>
	        <td width="100"><? echo $company_library[$row[csf("company_name")]]; ?></td>
	        <td width="80" ><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
	        <td width="80"><? echo $season_arr[$row[csf("season_buyer_wise")]]; ?></td>
	        <td width="80"><? echo $row[csf("po_number")]; ?></td>
	        <td width="80"><? echo $row[csf("style_ref_no")]; ?></td>
	        <td width="80"><? echo $garments_item[$row[csf("gmts_item_id")]]; ?></td>
	        <td width="150">
			<?
			$yarn_dyed=""; 
			foreach($fab_description as $fab)
			{
		
				echo $fab[csf("fabric_description")].",";
				//$fab_arr[$row[csf("job_no")]]=$row[csf("color_type_id")].",";
				//$fab_arr[$row[csf("job_no")]]=$row[csf("fabric_description")].",";
				$yarn_dyed=$fab[csf("color_type_id")];
			} 
			?>
		    </td>
	        <td width="80" align="center"><? echo $row[csf("date_diff")]; ?></td>
	        <td><? if($yarn_dyed==2 || $yarn_dyed==3 ||$yarn_dyed==4 || $yarn_dyed==6 ) { echo "Yes";} else { echo "NO";}; ?></td>
	        <td width="80">
			<? 
			$emb_data="";
			foreach($emb_cost as $emb )
			{
				$emb_data=$emb[csf("cons_dzn_gmts")];
			}
			if($emb_data>0){ echo "Yes"; } else{ echo "NO"; };
			 ?>
            </td>
        </tr>
        <? }
		
		
		?>
    </tbody>
	
</table>
<div style="margin-top:20px;"></div>

<table width="1000" border="1" rules="all" class="rpt_table" style="table-layout: fixed;">
	<thead>
		<th>Factory Merchandiser</th>
		<th>BH Merchandiser</th>
		<th>OPD</th>
		<th>Cont Rcvd Date</th>
		<th>First Ship Dt</th>
		<th>Ship mode</th>
		<th>Cons/Dz</th>
		<th>SMV</th>
        <th>Price</th>
		<th>POSID</th>
        <th>Department</th>
        <th>Remarks</th>
       
	</thead>
    <tbody>
        <td><? echo $factory_merchand[$data_array[0][csf("factory_marchant")]]; ?></td>
        <td><? echo $data_array[0][csf("bh_merchant")]; ?></td>
        <td><?  echo change_date_format($data_array[0][csf("po_received_date")]); ?></td>
        <td><? echo change_date_format($projected_po_date[0][csf("po_received_date")]);?></td>
        <td><? echo change_date_format($data_array[0][csf("shipment_date")]); ?></td>
        <td><? echo $shipment_mode[$data_array[0][csf("ship_mode")]]; ?></td>
        <td>
			<? 
			foreach($cons as $row)
			{
				echo $row[csf("fab_knit_req_kg")];
				
			}
			
			?>
        </td>
        <td align="right"><? echo $data_array[0][csf("set_smv")]; ?></td>
        <td align="right"><? echo $data_array[0][csf("avg_unit_price")]; ?></td>
        <td align="right" ><?  echo $data_array[0][csf("quotation_id")]; ?></td>
        
        <td><? echo $product_dept[$data_array[0][csf("pro_sub_dep")]]; ?></td>
        <td><? echo $data_array[0][csf("remarks")]; ?></td>
        
    </tbody>
	
</table>




<?
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name");
$size_arr=return_library_array( "select id, size_name from lib_size",'id','size_name');
/*echo "select po_break_down_id, country_type, country_id, size_number_id, plan_cut_qnty, order_quantity, country_ship_date, size_order 
	from wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 and job_no_mst='$txt_job_no' ";
*/	$sql_query=sql_select("select po_break_down_id,color_number_id, country_type, country_id, size_number_id,item_number_id, plan_cut_qnty, order_quantity, country_ship_date, size_order,order_total
	from wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 and job_no_mst='$txt_job_no' ");
	
	$sizeId_arr=$size_order_data=$order_dtls_arr=array();
	foreach($sql_query as $row)
	{
		$sizeId_arr[$row[csf('size_number_id')]]=$row[csf("size_number_id")];
		$itemId_arr[$row[csf('item_number_id')]]=$row[csf("item_number_id")];
		$colorId_arr[$row[csf('color_number_id')]]=$row[csf("color_number_id")];
		$size_order_data[$row[csf('size_number_id')]]['order_quantity']=$row[csf('order_quantity')];
		
	}
	//print_r($size_order_data);
?>
<h3>Color & Size Wise Qty:</h3>
<table class="rpt_table"  cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
	    <tr>
        	<th>GMT Item</th>
	        <th>Color</th>
	        <th>Total Qty</th>
	        <?
	        foreach($sizeId_arr as $key=>$value)
	        {
	            echo '<th>'.$size_arr[$key].'</th>';
	        } 
	        ?>
	       
	       </tr>
            </thead>
            <tbody>
            
           <?
		   $t_order_qty="";
		   foreach($sql_query as $row)
		        {
					?>
                    <tr>
                    <td><? echo $garments_item[$row[csf('item_number_id')]]; ?> </td>
                    <td><? echo $color_library[$row[csf('color_number_id')]]; ?></td>
                    <td><? echo $row[csf('order_total')];  $t_order_qty+=$row[csf('order_total')]; ?></td>
                   <?
		         
		   		
	          /* foreach($colorId_arr as $key=>$value)
		        {
					
		            echo '<tr><td>'.$color_library[$key].'</td></tr>';
		        } 
 			?>*/
             ?>
			 <?
			 $total_qty="";
	        foreach($sizeId_arr as $key=>$value)
	        {
	            echo '<td>'.$size_order_data[$key]['order_quantity']; $total_qty=$size_order_data[$key]['order_quantity'];$total_q+=$total_qty .'</td>';
				
				
	        } 
			
			?>
			</tr>
            <?
			}
	        ?>
            <tr><td colspan="2"><b>Total</b></td><td><? echo $t_order_qty; ?></td><td><? echo $total_q; ?></td></tr>
	    	</tbody>
	</table>
    <div style="float:left;">
 <h3>Shipment Date and Status:</h3>
   <table class="rpt_table"  cellpadding="0" cellspacing="0" border="1" rules="all">
     <thead>
     <tr>
		<th>PO No.</th>
		<th>Country</th>
		<th>Cut off Date</th>
		<th>week</th>
		<th>Qty</th>
		<th>Production Company</th>
        <th>Order Status</th>
        <th>Shiping Status</th>
       </tr>
	</thead>
     <tbody>
      <? 
		$week=return_library_array( "select week,week_date from week_of_year", "week_date", "week");
		$produc_company=return_library_array( "select job_no,company_id
		from ppl_order_allocation_mst where job_no='$txt_job_no'", "job_no", "company_id");
		$cutup_date=return_library_array( "select job_no_mst,cutup_date
		from wo_po_color_size_breakdown where job_no_mst='$txt_job_no'", "job_no_mst", "cutup_date");
		$country=return_library_array( "select job_no_mst,country_id
		from wo_po_color_size_breakdown", "job_no_mst", "country_id");
		$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
		
	
		$ship_sql=sql_select("select job_no_mst,po_number,po_quantity,is_confirmed,shiping_status,shipment_date
	from wo_po_break_down where job_no_mst='$txt_job_no' ");
		$total_qty="";
		foreach($ship_sql as $row) 
		{?> 
     <tr>
	     <td><? echo $row[csf('po_number')]; ?></td>
	     <td><? echo $country_arr[$country[$row[csf('job_no_mst')]]]; ?></td>
	     <td><?  echo change_date_format($cutup_date[$row[csf('job_no_mst')]]);?></td>
	     <td><? echo $week[$row[csf('shipment_date')]]; ?></td>
	     <td><? echo $row[csf('po_quantity')]; $total_qty+=$row[csf('po_quantity')]; ?></td>
	     <td> <? echo $company_library[$produc_company[$row[csf('job_no_mst')]]];?></td>
         <td><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
         <td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
       </tr>
       
       <? } ?>
       <tr><td colspan="4"><b>Total:</b></td><td><?  echo $total_qty;?></td></tr>
     </tbody>
</table> 
</div>
<div style="float:left; margin-left:50px;">
<h3>Revision:</h3> 
<table class="rpt_table"  cellpadding="0" cellspacing="0" border="1" rules="all">
	<thead>
		<tr>
			<th>Rev. Date</th>
			<th>Remarks</th>
		</tr>
        <? 
	
		$revision_sql=sql_select("select po_received_date,remarks 
	from wo_po_update_log where   job_no='$txt_job_no' ");
		foreach($revision_sql as $row) 
		{?> 
		<tr>
			<td><? $r_date=explode(" ",$row[csf('po_received_date')]); echo change_date_format($r_date[0]); ?></td>
			<td><? echo $row[csf('remarks')]; ?></td>
		</tr>
        <? }?>
	</thead>
</table> 
</div> 
</div>
</fieldset>
<?


	foreach (glob("*.xls") as $filename)
	{		
		@unlink($filename);

	}
	$name=time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name;
	exit();	


	
}



?>