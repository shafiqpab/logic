<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Buyer Quotation Finalization Pending
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	20.10.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
include('../includes/common.php');
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
extract($_REQUEST);

echo load_html_head_contents("Quotation Finalization Pending", "../", "", $popup, $unicode, $multi_select, 1);

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$ffl_merchandiser_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$weak_of_year=return_library_array( "select week_date,week from  week_of_year",'week_date','week');
$inquery_confirm_date=return_library_array( "select a.inquery_id,b.confirm_date from  wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.inquery_id>0",'inquery_id','confirm_date');
$yarn_count_array=return_library_array( "Select id, yarn_count from  lib_yarn_count where  status_active=1",'id','yarn_count');

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace(",","",$cbo_company_name);
	$company_name=$cbo_company_name;
	$location= $cbo_location_id;
	?>
    <h3 style="top:1px;width:100%; text-align:center;" id="accordion_h1" class="accordion_h" align="center"> Quotation Finalization Pending </h3>
    <div align="center">
		<? if($company_name!="") echo "<b>Company : </b> ".$company_library[$company_name]; ?> 
        <? if($location!="" && $location!=0) echo " , <b>Location :</b> ".$loc_name_arr[$location]; ?>
    </div>
    <br />
    <div align="center" style="height:200px;">
		 <div id="buyer_summery_div_show" align="left" style="float:left; margin-left:200px; margin-top:10px;"></div>
		 <div style="width:32%; height:180px; float:right; position:relative; margin-right:120px; margin-top:10px; border:solid 1px">
			<table style="margin-left:60px; font-size:12px">
				<tr>
					<td colspan="4">Summary Graph</td>
				</tr>
			</table>
			<canvas id="canvas" height="180" width="500"></canvas>
		</div>
	</div>
    
    <br />
    <div id="summery_div_show" align="center"></div>
    <br/>
   <div id="detail_report" style="height:auto; width:1700px; margin:0 auto; padding:0;">
    <fieldset>
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1700" align="left">
        <thead>
            <tr>
                <td colspan="28" align="center"><h2>Quotation Finalization Pending Report</h2></td>
            </tr>
            <tr> 
                <th width="20">SL</th>
                <th width="40">Quot. ID</th> 
                <th width="40">Month</th>
                <th width="40">Year</th>
                <th width="60">Quot. Date</th>
                <th width="40">Inq. ID</th>
                <th width="50">Comp.</th>                                    
                <th width="50">Buyer</th>
                <th width="50">Agent</th>
                <th width="80">Style Desc </th> 
                <th width="80">Season </th> 
                <th width="50">Prod. Dept</th>
                <th width="70">Offered Qnty</th>
                <th width="35">UOM</th>
                <th width="70">Est. Ship Date</th>
                <th width="70">Status</th> 
                <th width="65">Fabric Cost /Dzn</th>
                <th width="65">Trims Cost /Dzn</th>
                <th width="65">Embel. Cost /Dzn</th>
                <th width="65">Gmts Wash. Cost /Dzn</th>
                <th width="65">CM Cost /Dzn</th>
                <th width="65">Other Cost /Dzn</th>
                <th width="65">Total Cost /Dzn</th>
                <th width="65">Cost/Pcs</th>
                <th width="65">Quot/Pcs</th>
                <th width="65">Asking Price/Pcs</th>
                <th width="65">Conf. Price /Pcs</th>
                <th width="">Remarks</th>                                    
            </tr>  
        </thead>
    </table>
   <div style="width:1717px; max-height:410px; overflow-y: scroll;" id="scroll_body">
        <table cellspacing="0" cellpadding="0"  width="1700"  border="1" rules="all" class="rpt_table" id="tbl_ship_pending" >
        	<tbody>
			<?
			$res_month=array();
			$res_buyer=array();
			$i=1;
			if($db_type==0) $confirm_date_cond="and b.confirm_date='0000-00-00'"; else if($db_type==2) $confirm_date_cond="and b.confirm_date IS NULL ";
			$sql= "select a.id,a.inquery_id,b.quotation_id,b.confirm_date,company_id, buyer_id, agent, costing_per_id, style_desc,season,product_code, pord_dept, offer_qnty, order_uom, est_ship_date, approved,remarks, fabric_cost, trims_cost, embel_cost,wash_cost, cm_cost,a.quot_date, commission, (comm_cost+lab_test+inspection+freight+common_oh+currier_pre_cost+certificate_pre_cost) as othercost, final_cost_dzn,total_cost, final_cost_pcs, a1st_quoted_price,asking_quoted_price, confirm_price, revised_price, margin_dzn from  wo_price_quotation a,  wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $confirm_date_cond order by a.quot_date,b.quotation_id "; 
			//echo $sql;
			$master_sql = sql_select($sql) or die(mysql_error());	
            foreach($master_sql as $row)
            {
				$quot_date=date("Y-m", strtotime($row[csf('quot_date')]));	
				$res_month[$quot_date][$row[csf('buyer_id')]][$row[csf('id')]]+=1;
				$res_buyer[$row[csf('buyer_id')]][$row[csf("id")]]+=1;
				
				$quot_date_month_year=date("Y-m",strtotime($row[csf('quot_date')]));
				$quot_date_month_year_arr=(explode("-",$quot_date_month_year));
				$month_val=($quot_date_month_year_arr[1]*1);
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" id="tr3_<? echo $k; ?>" >
					<td width="20" bgcolor="<? echo $sl_bg_color;  ?>"> <? echo $i; ?> </td>
					<td width="40" align="left" ><p><? echo $row[csf('id')]; ?></p></td>
                    <td width="40" align="left" ><p><? echo $months[$month_val]; ?></p></td>
                    <td width="40" align="left" ><p><? echo $quot_date_month_year_arr[0]; ?></p></td>
                    <td width="60" align="left" ><p><? echo change_date_format($row[csf('quot_date')]); ?></p></td> 
					<td width="40" align="left" ><p><? echo $row[csf("inquery_id")]; ?></p></td>
					<td width="50" align="left" ><p><? echo $company_short_name_arr[$row[csf('company_id')]]; ?></p></td>	
					<td width="50" align="left"><p><? echo $buyer_short_name_arr[$row[csf('buyer_id')]]; ?></p></td>
					<td width="50" align="left"><p><? echo $buyer_short_name_arr[$row[csf('agent')]]; ?></p></td>
					<td width="80" align="left"><p><? echo trim($row[csf('style_desc')]); ?></p></td> 
					<td width="80" align="left"><p><? echo trim($row[csf('season')]); ?></td>                               
					<td width="50" align="left"><p><? echo $row[csf('product_code')]." ". $product_dept[$row[csf('pord_dept')]]; ?></p></td>
					<td width="70" align="right"><? echo number_format($row[csf('offer_qnty')]); ?></td>
					<td width="35" align="left"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
					<td width="70" align="left" bgcolor="<? echo $to_confirm_color;  ?>"><? echo change_date_format($row[csf('est_ship_date')]); ?></td> 
					<td width="70" align="left"><? if($row[csf('confirm_date')]=="" || $row[csf('confirm_date')]=='0000-00-00') echo "Under Process"; else echo "Confirm";?></td>
					<td width="65" align="right"><? echo number_format($row[csf('fabric_cost')],2); ?></td> 
					<td width="65" align="right"><? echo number_format($row[csf('trims_cost')],2); ?></td> 
					<td width="65" align="right"><? echo number_format($row[csf('embel_cost')],2); ?></td>
					<td width="65" align="right"><? echo number_format($row[csf('wash_cost')],2); ?></td>  
					<td width="65" align="right"><? echo number_format($row[csf('cm_cost')],2); ?></td>
					<td width="65" align="right"><? echo number_format($row[csf('othercost')],2); ?></td>
					<td width="65" align="right"><? echo number_format($row[csf('total_cost')],2); ?></td> 
					<td width="65" align="right"><? echo number_format($row[csf('final_cost_pcs')],2); ?></td>
					<? if($row[csf('revised_price')] > 0) $row[csf('1st_quoted_price')]=$row[csf('revised_price')];  ?>
					<td width="65" align="right" ><? echo number_format($row[csf('1st_quoted_price')],2); ?></td>
					<td width="65" align="right" ><? echo number_format($row[csf('asking_quoted_price')],2); ?></td>  
					<td width="65" align="right" ><? echo number_format($row[csf('confirm_price')],2); ?></td> 
					<td width=""><p><? echo $row[csf('remarks')]; ?></p></td>    
				</tr>
				<?
				$i++;
			}
        	?>
            </tbody>
        </table> 
    </div>
    </fieldset> 
</div>

    <div id="summery_div" style="display:none; visibility:hidden;"> 
    <fieldset style="width:1220px;">
        <div align="center" style="width:100%; font-size:18px;"><b>Month Wise Quotation Finalization Pending Summary</b></div> 
        <table width="1200">
            <tr>
				<?	
                foreach( $res_month as $month_id=>$buyer_arr)
                {
					if($s%3==0) $tr="</tr><tr>"; else $tr=""; echo $tr;
					?>
					<td valign="top">
                        <div style="width:380px">
                            <table width="380" class="rpt_table" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th colspan="3" align="center" style="font-size:16px;"> Total Summary  <? echo $month_name=date("F",strtotime($month_id)).", ".date("Y",strtotime($month_id)); ?> </th>
                                    </tr>
                                    <tr>
                                        <th width="80">SL</th>
                                        <th width="150">Buyer Name</th>
                                        <th width="150">Finalization Pending</th>
                                    </tr>
                            	</thead>
								<?
                                $d=1; 
                                foreach( $buyer_arr as $buyer_id=>$quot_id)
                                {
                                    $no_of_quot=count($quot_id);
                                    if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                        <td><? echo $d; ?></td>
                                        <td><div style="word-wrap:break-word; width:150px"><? echo $buyer_short_name_arr[$buyer_id]; ?></div></td>
                                        <td align="right"><?  echo number_format($no_of_quot,0); $total_quot_pendin +=$no_of_quot;?></td>
                                    </tr>
                                    <?
                                    $d++;
                                }
                                ?>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" align="right">Total</th>
                                        <th ><? echo number_format($total_quot_pendin,0); ?></th>											
                                    </tr>
                                </tfoot>
                            </table>
                        </div>  
					</td> 
					<?
					$total_quot_pendin="";
					$s++;
                }		 
                ?>
            </tr>
        </table>
    </fieldset> 
    </div>
  
  <div id="buyer_wise_summery" style="display:none; visibility:hidden;">  
   <fieldset style="width:500px; height:180px;">
   <div align="center" style="width:100%; font-size:16px;"><b>Buyer Wise Quotation Finalization Pending Summary</b></div>
    <div id="detail_summery" style="height:auto; width:380px; margin:0 auto; padding:0;"> 
        <table width="380" class="rpt_table" border="1" rules="all">
            <thead>
                <tr>
                    <th width="80">SL</th>
                    <th width="150">Buyer Name</th>
                    <th width="150">Finalization Pending</th>
                </tr>
            </thead>
        </table>
        <div style="width:397px; max-height:120px; overflow-y: scroll;" id="scroll_body">
            <table width="380" class="rpt_table" border="1" rules="all">
				<?	
				$buyer_data[]=array();
				$pending_val[]=array();
                $d=1; 
                $total_buyer_quot=0;
				$i=0;
                foreach( $res_buyer as $buyer_id=>$quot_id)
                {
					$buyer_quot=count($quot_id);
					if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
                        <td width="80"><? echo $d; ?></td>
                        <td width="150"><? echo $buyer_short_name_arr[$buyer_id]; ?></td>
                        <td width="150" align="right"><?  echo number_format($buyer_quot,0); $total_buyer_quot +=$buyer_quot;?></td>
					</tr>
					<?
					$d++;
					$buyer_data[$i]=$buyer_short_name_arr[$buyer_id];
					$pending_val[$i]=$buyer_quot;
					$i++;
                }
				
				$buyer_data= json_encode($buyer_data);
    			$pending_val= json_encode($pending_val);
                ?>
                <tfoot>
                    <tr>
                        <th colspan="2" align="right">Total</th>
                        <th ><? echo number_format($total_buyer_quot,0); ?></th>											
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    </fieldset>
   </div>
	<script src="../Chart.js-master/Chart.js"></script>
    <script>
    var barChartData = {
        labels : <? echo $buyer_data; ?>,
        datasets : [
            {
                fillColor : "green",
                strokeColor : "rgba(0,128,0)",
                highlightFill: "rgb(0,128,0)",
                highlightStroke: "rgba(0,128,0)",
                data : <? echo $pending_val; ?>
            }
        ]
    }
    var ctx = document.getElementById("canvas").getContext("2d");
    window.myBar = new Chart(ctx).Bar(barChartData, {
    responsive : true
    });
    
    document.getElementById('summery_div_show').innerHTML=document.getElementById('summery_div').innerHTML;
    document.getElementById('summery_div').innerHTML="";
    
    document.getElementById('buyer_summery_div_show').innerHTML=document.getElementById('buyer_wise_summery').innerHTML;
    document.getElementById('buyer_wise_summery').innerHTML="";
    </script> 

	<?
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
	//echo "$total_data****$filename";
	disconnect($con);
	exit();		
} 

?>