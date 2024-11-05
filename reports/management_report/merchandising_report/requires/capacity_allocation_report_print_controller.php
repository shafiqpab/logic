<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Capacity Allocation Print Report
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         : From this version oracle conversion is start
*/

include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
$cm_for_shipment_schedule_arr=return_library_array( "select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls",'job_no','cm_for_sipment_sche');

if ($action=="load_drop_down_buyer")
{
echo create_drop_down( "cbo_buyer_name", 172, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );		 
}

if($action=="capacity_allocation_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
?>
<div id="table_row" align="center" style="height:auto; width:1190px; margin:0 auto; padding:0;">
<?
$company_library=sql_select("select id, company_name, plot_no, level_no,road_no,city from lib_company where id=".$data[0]."");
$year_library=return_library_array( "select id, year from  lib_capacity_calc_mst", "id", "year"  );
	foreach( $company_library as $row)
	{
?>
		<span style="font-size:30px"><center><? echo $row[csf('company_name')]." .\n";?></center></span>
<?
	}
?>
    <table width="1190px" align="center">
        <tr>
            <td colspan="6" align="center" style="font-size:28px"><center><strong><u>Buyer wise capacity allocation chart:<? echo $year_library[$data[1]]; ?></u></strong></center></td>
        </tr>
            <br><br>
        <tr>
            <td colspan="6" align="left" style="font-size:24px"><strong>Basic Quantity Allocation</strong></td>
        </tr>
    </table>
    
    
<div style="width:1190px; height:auto">
    <table align="right" cellspacing="0" width="1190px"  border="1" rules="all" class="rpt_table_qty_allocation" id="tbl_month_pce" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="105" align="center">Buyer</th>
            <th width="75" align="center">Jan</th>
            <th width="75" align="center">Feb</th>
            <th width="75" align="center">Mar</th>
            <th width="75" align="center">Apr</th>
            <th width="75" align="center">May</th>
            <th width="75" align="center">Jun</th>
            <th width="75" align="center">Jul</th>
            <th width="75" align="center">Aug</th>
            <th width="75" align="center">Sep</th>
            <th width="75" align="center">Oct</th>
            <th width="75" align="center">Nov</th>
            <th width="75" align="center">Dec</th>
            <th width="75" align="center">Total</th>
            <th width="80" align="center">%of Total</th>           
        </thead>
        <tbody>
        <?
			$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
			$sql_con="SELECT  b.buyer_id,sum(b.allocation_percentage) as allocation_percentage";
			for($i=1;$i<=12;$i++)
			{
				 $sql_con .= ",SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs)   END) AS capa$i,SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs* b.allocation_percentage)/100   END) AS sum$i";
				
			}
			$sql_con .= " FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
WHERE a.id=b.mst_id AND a.year_id=c.year AND d.month_id=a.month_id AND  a.location_id=c.location_id AND c.id=d.mst_id AND a.company_id=$data[0]  AND a.year_id=$data[1]  AND a.location_id=$data[4]  GROUP BY b.buyer_id";
			//print_r($sql_con);
			$sql_data=sql_select($sql_con);
			
		$i=1;
		$total_sum1=0;$total_sum2=0;$total_sum3=0;$total_sum4=0;$total_sum5=0;$total_sum6=0;$total_sum7=0;$total_sum8=0;$total_sum9=0;$total_sum10=0;$total_sum11=0;$total_sum12=0;$grand_total=0;$total_allocation=0;
		
		foreach( $sql_data as $row)
		{
			if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
			
			$total_sum1 = $total_sum1+$row[csf("sum1")];
			$total_sum2 = $total_sum2+$row[csf("sum2")];
			$total_sum3 = $total_sum3+$row[csf("sum3")];
			$total_sum4 = $total_sum4+$row[csf("sum4")];
			$total_sum5 = $total_sum5+$row[csf("sum5")];
			$total_sum6 = $total_sum6+$row[csf("sum6")];
			$total_sum7 = $total_sum7+$row[csf("sum7")];
			$total_sum8 = $total_sum8+$row[csf("sum8")];
			$total_sum9 = $total_sum9+$row[csf("sum9")];
			$total_sum10 = $total_sum10+$row[csf("sum10")];
			$total_sum11 = $total_sum11+$row[csf("sum11")];
			$total_sum12 = $total_sum12+$row[csf("sum12")];
			$grand_total = $grand_total+$total;
			$total_allocation = $total_allocation+$row[csf("allocation_percentage")];
			$total_capa_1=$row[csf("capa1")];
			$total_capa_2=$row[csf("capa2")];
			$total_capa_3=$row[csf("capa3")];
			$total_capa_4=$row[csf("capa4")];
			$total_capa_5=$row[csf("capa5")];
			$total_capa_6=$row[csf("capa6")];
			$total_capa_7=$row[csf("capa7")];
			$total_capa_8=$row[csf("capa8")];
			$total_capa_9=$row[csf("capa9")];
			$total_capa_10=$row[csf("capa10")];
			$total_capa_11=$row[csf("capa11")];
			$total_capa_12=$row[csf("capa12")];
			
			$unallocate_capacity1=$total_capa_1-$total_sum1;
			$unallocate_capacity2=$total_capa_2-$total_sum2;
			$unallocate_capacity3=$total_capa_3-$total_sum3;
			$unallocate_capacity4=$total_capa_4-$total_sum4;
			$unallocate_capacity5=$total_capa_5-$total_sum5;
			$unallocate_capacity6=$total_capa_6-$total_sum6;
			$unallocate_capacity7=$total_capa_7-$total_sum7;
			$unallocate_capacity8=$total_capa_8-$total_sum8;
			$unallocate_capacity9=$total_capa_9-$total_sum9;
			$unallocate_capacity10=$total_capa_10-$total_sum10;
			$unallocate_capacity11=$total_capa_11-$total_sum11;
			$unallocate_capacity12=$total_capa_12-$total_sum12;
			
	
		?>
       
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td align="left"><? echo $buyer_library[$row[csf("buyer_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("sum1")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum2")],0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($row[csf("sum3")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum4")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum5")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum6")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum7")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum8")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum9")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum10")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum11")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum12")],0,'',','); ?></td>
                <?
				$total=$row[csf("sum1")]+$row[csf("sum2")]+$row[csf("sum3")]+$row[csf("sum4")]+$row[csf("sum5")]+$row[csf("sum6")]+$row[csf("sum7")]+$row[csf("sum8")]+$row[csf("sum9")]+$row[csf("sum10")]+$row[csf("sum11")]+$row[csf("sum12")];
				?>
                <td align="right"><? echo $total;?></td>
                <td align="right"><? echo $row[csf("allocation_percentage")]; ?>%</td> 
            </tr>
            
            <?
				$i++;
				}
			
			?>
             
            <tr>
                <td colspan="2" ><strong>Allocate Capacity:</strong></td>
                <td align="right"><strong><? echo  number_format($total_sum1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum12,0,'',',');?></strong></td>
                <td align="right"><strong><?  echo number_format($grand_total,0,'',',');?></strong></td>
                <td align="right"><strong><? echo $total_allocation;?></strong></td>
            </tr>
            <tr>
                <td colspan="2" ><strong>Total Capacity:</strong></td>
                <td align="right"><strong><? echo  number_format($total_capa_1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_12,0,'',',');?></strong></td>
                <td align="right"><strong><? ?></strong></td>
                <td align="right"><strong><? ?></strong></td>
            </tr>
            <?
			if($unallocate_capacity1<1||$unallocate_capacity2<1||$unallocate_capacity3<1||$unallocate_capacity4<1||$unallocate_capacity5<1||$unallocate_capacity6<1||$unallocate_capacity7<1||$unallocate_capacity8<1||$unallocate_capacity9<1||$unallocate_capacity10<1||$unallocate_capacity11<1||$unallocate_capacity12<1)
			{
			?>
            <tr>
            <?
			}
			else
			{
			?>
            <tr  bgcolor="#FFFF99">
            <?
			}
			?>
            
                <td colspan="2" ><strong>Unallocate Capacity:</strong></td>
                <td align="right"><strong><? echo  number_format($unallocate_capacity1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity12,0,'',',');?></strong></td>
                <td align="right"><strong><? ?></strong></td>
                <td align="right"><strong><? ?></strong></td>
            </tr>
             <?
				/*$total_cap_min =sql_select("select a.id,a.comapny_id,SUM(CASE WHEN b.month_id =1 THEN b.capacity_month_pcs END) AS 'sum1',SUM(CASE WHEN b.month_id =2 THEN b.capacity_month_pcs END) AS 'sum2' FROM  lib_capacity_calc_mst a, lib_capacity_year_dtls b WHERE a.id=b.mst_id AND a.comapny_id=13 AND a.id=2 GROUP BY a.comapny_id");			$sql_total="select a.id,a.comapny_id";
			for($i=1;$i<=12;$i++)
			{
				 $sql_total .= ",SUM(CASE WHEN b.month_id =".$i. " THEN b.capacity_month_pcs  END) AS 'sum$i'";
				
			}
			$sql_total .= " FROM  lib_capacity_calc_mst a, lib_capacity_year_dtls b WHERE a.id=b.mst_id AND a.comapny_id=$data[0] AND a.id=$data[1] GROUP BY a.comapny_id";
			//print_r($sql_total);
			$total_cap_min=sql_select($sql_total);
				
		$total_sum1=0;$total_sum2=0;$total_sum3=0;$total_sum4=0;$total_sum5=0;$total_sum6=0;$total_sum7=0;$total_sum8=0;$total_sum9=0;$total_sum10=0;$total_sum11=0;$total_sum12=0;$grand_total=0;$total_allocation=0;
				foreach( $total_cap_min as $row)
		{
			$total_sum1 = $total_sum1+$row[csf("sum1")];
			$total_sum2 = $total_sum2+$row[csf("sum2")];
			$total_sum3 = $total_sum3+$row[csf("sum3")];
			$total_sum4 = $total_sum4+$row[csf("sum4")];
			$total_sum5 = $total_sum5+$row[csf("sum5")];
			$total_sum6 = $total_sum6+$row[csf("sum6")];
			$total_sum7 = $total_sum7+$row[csf("sum7")];
			$total_sum8 = $total_sum8+$row[csf("sum8")];
			$total_sum9 = $total_sum9+$row[csf("sum9")];
			$total_sum10 = $total_sum10+$row[csf("sum10")];
			$total_sum11 = $total_sum11+$row[csf("sum11")];
			$total_sum12 = $total_sum12+$row[csf("sum12")];
			$grand_total = $grand_total+$total;
			$total_allocation = $total_allocation+$row[csf("allocation_percentage")];
				
			?>
            
               <tr>
                <td colspan="2" ><strong>Total Capacity:</strong></td>
                <td align="right"><strong><? echo  number_format($total_sum1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum12,0,'',',');?></strong></td>
                <td align="right"><strong><? ?></strong></td>
                <td align="right"><strong><? ?></strong></td>
            </tr> 
            <?
		}
			*/?>

        </tbody>
    </table>
<br />
    <table width="1190px" align="center">
        <tr>
            <td height="17" colspan="6" align="left">&nbsp;</td>
        </tr>
        
    </table>     
	<table width="1190px" align="center">
        <tr>
            <td colspan="6" align="left" style="font-size:24px"><strong>Minute Allocation</strong></td>
        </tr>
    </table>
    <table align="right" cellspacing="0" width="1190px"  border="1" rules="all" class="rpt_table_qty_allocation" id="tbl_month_munite" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="105" align="center">Buyer</th>
            <th width="75" align="center">Jan</th>
            <th width="75" align="center">Feb</th>
            <th width="75" align="center">Mar</th>
            <th width="75" align="center">Apr</th>
            <th width="75" align="center">May</th>
            <th width="75" align="center">Jun</th>
            <th width="75" align="center">Jul</th>
            <th width="75" align="center">Aug</th>
            <th width="75" align="center">Sep</th>
            <th width="75" align="center">Oct</th>
            <th width="75" align="center">Nov</th>
            <th width="75" align="center">Dec</th>
            <th width="75" align="center">Total</th>
            <th width="80" align="center">%of Total</th>          
        </thead>
        
        <tbody>
        	<?
			$supplier_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
			$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
			$sql_con="SELECT  b.buyer_id,sum(b.allocation_percentage) as allocation_percentage";
			for($i=1;$i<=12;$i++)
			{
				 $sql_con .= ",SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_min)   END) AS capa$i ,SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_min* b.allocation_percentage)/100   END) AS sum$i ";
				
			}
			$sql_con .= " FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
WHERE a.id=b.mst_id AND a.year_id=c.year AND d.month_id=a.month_id AND  a.location_id=c.location_id AND c.id=d.mst_id AND a.company_id=$data[0] AND a.year_id=$data[1]  AND a.location_id=$data[4] GROUP BY b.buyer_id";
			//print_r($sql_con);
			$sql_data=sql_select($sql_con);
			
		$i=1;
		$total_sum1=0;$total_sum2=0;$total_sum3=0;$total_sum4=0;$total_sum5=0;$total_sum6=0;$total_sum7=0;$total_sum8=0;$total_sum9=0;$total_sum10=0;$total_sum11=0;$total_sum12=0;$grand_total=0;$total_allocation=0;
		
		foreach( $sql_data as $row)
		{
			
			if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
			
			$total_sum1 = $total_sum1+$row[csf("sum1")];
			$total_sum2 = $total_sum2+$row[csf("sum2")];
			$total_sum3 = $total_sum3+$row[csf("sum3")];
			$total_sum4 = $total_sum4+$row[csf("sum4")];
			$total_sum5 = $total_sum5+$row[csf("sum5")];
			$total_sum6 = $total_sum6+$row[csf("sum6")];
			$total_sum7 = $total_sum7+$row[csf("sum7")];
			$total_sum8 = $total_sum8+$row[csf("sum8")];
			$total_sum9 = $total_sum9+$row[csf("sum9")];
			$total_sum10 = $total_sum10+$row[csf("sum10")];
			$total_sum11 = $total_sum11+$row[csf("sum11")];
			$total_sum12 = $total_sum12+$row[csf("sum12")];
			$grand_total = $grand_total+$total;
			$total_allocation = $total_allocation+$row[csf("allocation_percentage")];
			$total_capa_1=$row[csf("capa1")];
			$total_capa_2=$row[csf("capa2")];
			$total_capa_3=$row[csf("capa3")];
			$total_capa_4=$row[csf("capa4")];
			$total_capa_5=$row[csf("capa5")];
			$total_capa_6=$row[csf("capa6")];
			$total_capa_7=$row[csf("capa7")];
			$total_capa_8=$row[csf("capa8")];
			$total_capa_9=$row[csf("capa9")];
			$total_capa_10=$row[csf("capa10")];
			$total_capa_11=$row[csf("capa11")];
			$total_capa_12=$row[csf("capa12")];
			
			$unallocate_capacity1=$total_capa_1-$total_sum1;
			$unallocate_capacity2=$total_capa_2-$total_sum2;
			$unallocate_capacity3=$total_capa_3-$total_sum3;
			$unallocate_capacity4=$total_capa_4-$total_sum4;
			$unallocate_capacity5=$total_capa_5-$total_sum5;
			$unallocate_capacity6=$total_capa_6-$total_sum6;
			$unallocate_capacity7=$total_capa_7-$total_sum7;
			$unallocate_capacity8=$total_capa_8-$total_sum8;
			$unallocate_capacity9=$total_capa_9-$total_sum9;
			$unallocate_capacity10=$total_capa_10-$total_sum10;
			$unallocate_capacity11=$total_capa_11-$total_sum11;
			$unallocate_capacity12=$total_capa_12-$total_sum12;
			
	
		?>
        
            <tr bgcolor="<? echo $bgcolor ; ?>">
            	<td><? echo $i; ?></td>
                <td align="left"><? echo $buyer_library[$row[csf("buyer_id")]]; ?></td>
                <td align="right"><? echo number_format($row[csf("sum1")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum2")],0,'',',');  ; ?></td>
                <td align="right"><? echo number_format($row[csf("sum3")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum4")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum5")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum6")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum7")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum8")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum9")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum10")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum11")],0,'',','); ?></td>
                <td align="right"><? echo number_format($row[csf("sum12")],0,'',','); ?></td>
                <?
				$total=$row[csf("sum1")]+$row[csf("sum2")]+$row[csf("sum3")]+$row[csf("sum4")]+$row[csf("sum5")]+$row[csf("sum6")]+$row[csf("sum7")]+$row[csf("sum8")]+$row[csf("sum9")]+$row[csf("sum10")]+$row[csf("sum11")]+$row[csf("sum12")];
				?>
                <td align="right"><? echo $total;?></td>
                <td align="right"><? echo $row[csf("allocation_percentage")];?>%</td> 
            </tr>
            
            <?
				$i++;
				}
			
			?>
             <tr>
                <td colspan="2" ><strong>Allocate Capacity:</strong></td>
                <td align="right"><strong><? echo  number_format($total_sum1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($total_sum12,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($grand_total,0,'',',');?></strong></td>
                <td align="right"><strong><? echo $total_allocation;?></strong></td>
            </tr>
             
            
           <tr>
                <td colspan="2" ><strong>Total Capacity:</strong></td>
                <td align="right"><strong><? echo  number_format($total_capa_1,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($total_capa_12,0,'',',');?></strong></td>
                <td align="right"><strong><? ?></strong></td>
                <td align="right"><strong><? ?></strong></td>
            </tr>
             <?
			if($unallocate_capacity1<1||$unallocate_capacity2<1||$unallocate_capacity3<1||$unallocate_capacity4<1||$unallocate_capacity5<1||$unallocate_capacity6<1||$unallocate_capacity7<1||$unallocate_capacity8<1||$unallocate_capacity9<1||$unallocate_capacity10<1||$unallocate_capacity11<1||$unallocate_capacity12<1)
			{
			?>
            <tr>
            <?
			}
			else
			{
			?>
            <tr  bgcolor="#FFFF99" >
            <?
			}
			?>
                <td colspan="2" ><strong>Unallocate Capacity:</strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity1,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity2,0,'',','); ?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity3,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity4,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity5,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity6,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity7,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity8,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity9,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity10,0,'',',');?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity11,0,'',',');;?></strong></td>
                <td align="right"><strong><? echo number_format($unallocate_capacity12,0,'',',');?></strong></td>
                <td align="right"><strong><? ?></strong></td>
                <td align="right"><strong><? ?></strong></td>
            </tr> 
       
           
        </tbody>
    </table>
</div>
</div>

<?
}

?>