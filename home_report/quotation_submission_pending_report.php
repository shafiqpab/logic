<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Quotation Submission Pending Report
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
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
include('../includes/common.php');
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];

echo load_html_head_contents("Quotation Submission Pending", "../", "", $popup, $unicode, $multi_select, $amchart);

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

	$sql_quatation=sql_select("SELECT inquery_id from  wo_price_quotation where company_id =$cbo_company_name and status_active=1 and is_deleted=0 and inquery_id>0 group by inquery_id order by inquery_id");
	foreach($sql_quatation as $row)
	{
		$quatation_mst_arr[$row[csf("inquery_id")]]['inquery_id']=$row[csf("inquery_id")];
		$quatation_mst_arr[$row[csf("inquery_id")]]['id']=$row[csf("id")];
	}
	//var_dump($quatation_mst_arr);
	
	$sql_inquery_sum="SELECT id, buyer_id, inquery_date from  wo_quotation_inquery where company_id =$cbo_company_name and status_active=1 and is_deleted=0  order by id";
	//echo $sql_inquery_sum;
	$sql_result=sql_select($sql_inquery_sum);
	$count=0;
	$res_buyer=array();
	foreach($sql_result as $row)
	{
		if($quatation_mst_arr[$row[csf("id")]]['inquery_id']=="")
		{
			 $inqu_date=date("Y-m", strtotime($row[csf("inquery_date")]));
			 $res_month[$inqu_date][$row[csf("buyer_id")]][$row[csf("id")]]+=1;
			 $res_buyer[$row[csf("buyer_id")]][$row[csf("id")]]+=1;
			 $count++;
		}
	}
	ksort($res_month);
	ksort($res_buyer);
	//var_dump($res_buyer);
	
	?>
    <h3 style="top:1px;width:100%; text-align:center;" id="accordion_h1" class="accordion_h" align="center"> Quotation Submission Pending </h3>
     <div align="center">
		<? if($company_name!="") echo "<b>Company : </b> ".$company_library[$company_name]; ?> 
        <? if($location!="" && $location!=0) echo " , <b>Location :</b> ".$loc_name_arr[$location]; ?>
    </div>
   <br/>
   
    <div align="center" style="height:200px;">
        <div id="buyer_summery_div_show" align="left" style="float:left; margin-left:200px; margin-top:10px;">
        <fieldset style="width:500px; height:170px;">
            <div align="center" style="width:100%; font-size:18px;"><b>Buyer Wise Quotation Submission Pending Summary</b></div>
            <div id="detail_summery" style="height:auto; width:380px; margin:0 auto; padding:0;"> 
            <table width="380" class="rpt_table" border="1" rules="all">
            <thead>
                <tr>
                    <th width="80">SL</th>
                    <th width="150">Buyer Name</th>
                    <th width="150">Submission Pending</th>
                </tr>
            </thead>
            </table>
                <div style="width:397px; max-height:120px; overflow-y: scroll;" id="scroll_body">
                    <table width="380" class="rpt_table" border="1" rules="all">
						<?	
                        $buyer_data[]=array();
                        $pending_val[]=array();
                        $d=1; 
                        $i=0;
                        foreach( $res_buyer as $buyer_id=>$inq_id)
                        {
                            $buyer_inq=count($inq_id);
                            if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td width="80"><? echo $d; ?></td>
                                <td width="150"><? echo $buyer_short_name_arr[$buyer_id]; ?></td>
                                <td width="150" align="right"><?  echo number_format($buyer_inq,0); $total_buyer_inq +=$buyer_inq;?></td>
                            </tr>
                            <?
                            $d++;
                            $buyer_data[$i]=$buyer_short_name_arr[$buyer_id];
                            $pending_val[$i]=$buyer_inq;
                            $i++;
                        }
                        $buyer_data= json_encode($buyer_data);
                        $pending_val= json_encode($pending_val);
                        ?>
                        <tfoot>
                            <tr>
                            <th colspan="2" align="right">Total</th>
                            <th ><? echo number_format($total_buyer_inq,0); ?></th>											
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </fieldset>
        </div>
        <div style="width:32%; height:180px; float:right; position:relative; margin-right:120px; margin-top:10px; border:solid 1px">
            <table style="margin-left:60px; font-size:12px">
                <tr>
                    <td colspan="4">Summary Graph</td>
                </tr>
            </table>
            <canvas id="canvas" height="180" width="500"></canvas>
        </div>
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
    </script>
<br/>
<div align="center" id="Summary_report" style="height:auto; width:100%; margin:0 auto; padding:0;">
    <fieldset style="width:1220px;">
    	<div align="center" style="width:100%; font-size:18px;"><b>Month Wise Quotation Submission Pending Summary</b></div> 
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
                                <th width="150">Submission Pending</th>
                                </tr>
                            </thead>
                                    <?
                                    $d=1; 
                                    foreach( $buyer_arr as $buyer_id=>$inq_id)
                                    {
                                    $no_of_inq=count($inq_id);
                                    if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                    <td><? echo $d; ?></td>
                                    <td><div style="word-wrap:break-word; width:150px"><? echo $buyer_short_name_arr[$buyer_id]; ?></div></td>
                                    <td align="right"><?  echo number_format($no_of_inq,0); $total_confirm_pendin +=$no_of_inq;?></td>
                                    </tr>
                                    <?
                                    $d++;
                                    }
                                    ?>
                            <tfoot>
                                <tr>
                                <th colspan="2" align="right">Total</th>
                                <th ><? echo number_format($total_confirm_pendin,0); ?></th>											
                                </tr>
                            </tfoot>
                        </table>
                    </div>  
                    </td> 
                    <?
                    $total_confirm_pendin="";
                    $s++;
                }		 
            ?>
            </tr>
        </table>
    </fieldset>
</div>
<br />
<div id="detail_report" style="height:auto; width:1200px; margin:0 auto; padding:0;">
    <fieldset>
        <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="1200" align="left">
        	<thead>
                <tr>
                    <td  colspan="17" align="center"><h2>Quotation Submission Pending Report</h2></td>
                </tr>
            	<tr>
                    <th width="30">SL</th>
                    <th width="80">Month</th>
                    <th width="50">Year</th>
                    <th width="50">Inquery Id</th>
                    <th width="80" >Season</th>
                    <th width="80" >Buyer Request No</th>
                    <th width="150">Style Name</th>
                    <th width="100" >Buyer Name</th>
                    <th width="100" >Merchandiser</th>
                    <th width="60">Status</th>
                    <th width="80">Inquery Date</th>
                    <th width="60">Offer qty.</th>
                    <th width="60">Fabrication</th>
                    <th width="70">Gmts Item</th>
                    <th>Remarks</th>
                </tr>
        	</thead>
		</table>
    <div style="width:1217px; max-height:410px; overflow-y: scroll;" id="scroll_body">
        <table cellspacing="0" cellpadding="0"  width="1200"  border="1" rules="all" class="rpt_table" id="tbl_ship_pending" >
            <tbody>
            <?
            $i=1;
            $sql_inquery="SELECT id, system_number_prefix_num, company_id, buyer_id, season, inquery_date, buyer_request, remarks, dealing_marchant, style_refernce, insert_date, offer_qty, fabrication, gmts_item from  wo_quotation_inquery where company_id =$cbo_company_name and status_active=1 and is_deleted=0  order by inquery_date";
            $sql_result=sql_select($sql_inquery);
            foreach($sql_result as $row)
            {
                if($quatation_mst_arr[$row[csf("id")]]['inquery_id']=="")
                {
                    $month_year=date("Y-m",strtotime($row[csf("inquery_date")]));
                    $month_year_arr=(explode("-",$month_year));
                    $month_val=($month_year_arr[1]*1);
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>"  id="tr_<? echo $i;?>"><!--onClick="change_color('tr_<? //echo $i;?>','<? //echo $bgcolor;?>')"-->
                        <td width="30"><? echo $i; ?></td>
                        <td width="80" align="center"><p><? echo $months[$month_val]; ?></p></td>
                        <td width="50" align="center"><p><? echo $month_year_arr[0]; ?></p></td>
                        <td width="50" align="center"><p><? echo $row[csf("system_number_prefix_num")]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('season')]; ?></p></td>
                        <td width="80"><p><? echo $row[csf('buyer_request')]; ?></p></td>
                        <td width="150" align="center"><p><? echo $row[csf('style_refernce')]; ?></p></td>
                        <td width="100"><p><? echo $buyer_short_name_arr[$row[csf('buyer_id')]]; ?></p></td>
                        <td width="100"><p><? echo $ffl_merchandiser_arr[$row[csf("dealing_marchant")]]; ?></p></td>
                        <td width="60" align="center"><p>Not Submited</p></td>
                        <td align="center" width="80"><p><? if($row[csf('inquery_date')]!='0000-00-00' && $row[csf('inquery_date')] !="")  echo change_date_format($row[csf('inquery_date')]);  ?></p></td>
                        <td width="60" align="center"><p><? echo $row[csf("offer_qty")]; ?></p></td>
                        <td width="60" align="center"><p><? echo $row[csf("fabrication")]; ?></p></td>
                        <td align="right" width="70" ><p><? echo $row[csf("gmts_item")]; ?></p></td>
                        <td ><p><? echo $row[csf("remarks")]; ?></p></td>
                    </tr>
                    <?
                    $i++;
                }
            }
            ?>
            </tbody>
        </table> 
    </div>
    </fieldset> 
</div>

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

 