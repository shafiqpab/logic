<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//-----------------------------

if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 160, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );		 
}
if($action=="show_dtls_list_view")
{
	//echo $data;
 	$data=explode('__',$data);
	 
	$sql_result=sql_select("SELECT mid, bid,allocation_percentage,buyer_name,status_active,updid FROM (
SELECT a.id as mid,  b.buyer_id AS bid,b.allocation_percentage,c.buyer_name,c.status_active, b.id as updid 
FROM lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b, lib_buyer c  
WHERE a.id=b.mst_id AND a.company_id=$data[0]  AND a.year_id=$data[1] AND a.month_id=$data[2] AND a.location_id=$data[3] AND c.id=b.buyer_id 
UNION 
SELECT 0, d.id AS bid,'',d.buyer_name,d.status_active,0
FROM  lib_buyer d
 ) AS tabl
 left join lib_buyer_party_type e on e.party_type=party_type
 left join lib_buyer_tag_company f on e.buyer_id=f.buyer_id
 where party_type in(1||3||21||90) and tag_company=$data[0]
 GROUP BY bid");
 
	
	
	 
	 $i=0;
	?>
	 <table cellpadding="0" cellspacing="0" border="1" width="450" id="tbl_allocation" class="rpt_table" rules="all">
				<thead>
					<th align="center">SL</th>
					<th align="center">Buyer Name</th>
					<th align="center">Status</th>
					<th align="center">Allocation %</th>
				</thead>

				<tbody>
                <?
				$is_update=0;
				$is_new=1;
				foreach( $sql_result as $row)
				{
					$i++;
					if($row[csf("status_active")]==0)$row[csf("status_active")]=2;
					
					if( $row[csf("updid")]>0 and $is_new==1  )
					{
						$is_update=1;
						$is_new=0;
					}
					
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
				
					
				?>
             
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td  align="center">
						<? echo $i; ?>
                        <input type="hidden" id="update_id" name="update_id" value="<? echo $row[csf("mid")]; ?>" >
                        <input type="hidden" id="update_id_dtls_<? echo $i; ?>" name="update_id_dtls_<? echo $i; ?>" value="<? echo $row[csf("updid")]; ?>" />
                        </td>
						<td  align="left"><? echo $row[csf("buyer_name")]; ?><input type="hidden"  id="buyer_id_<? echo $i; ?>" name="buyer_id_<? echo $i; ?>" value="<? echo $row[csf("bid")];  ?>" /></td>
                     
						<td  align="center"><? echo $row_status[$row[csf("status_active")]]; ?></td>
                        <? if($row[csf("status_active")]==2){?><td  align="center"><input type="text" id="txt_allocation_<? echo $i; ?>" name="txt_allocation_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf("allocation_percentage")]; ?>" style="width:100px" readonly="readonly" /> </td> <? } else { ?>
						<td  align="center"><input type="text" id="txt_allocation_<? echo $i; ?>" name="txt_allocation_<? echo $i; ?>" onchange="total_value(<? echo $i; ?>)" class="text_boxes_numeric" value="<? echo $row[csf("allocation_percentage")]; ?>" style="width:100px" /> </td><? } ?>
					</tr>
                    <? } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td align="center" ></td>
                            <td align="center"></td>
                            <td align="center" >Total
                            
                            <input type="hidden" id="is_update" value="<? echo $is_update; ?>" />
                            </td>
                            <td align="center" >
                            <?
							$run_total_txt_amount=0;
							foreach( $sql_result as $row)
							{
								 $run_total_txt_amount =$run_total_txt_amount+ $row[csf("allocation_percentage")];
							}
							?>
							<input type="text" id="txt_amount" name="txt_amount" class="text_boxes_numeric" value="<? echo $run_total_txt_amount ; ?>" style="width:100px" randomly disabled />
							
                            </td>
                        </tr> 
                   </tfoot>
            	
			 
                     
			</table> 
	<?
}

if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $tot_row_buyer;
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}
		
		
		if((str_replace("'","",$update_id)=="") || (str_replace("'","",$update_id)==0)&&(str_replace("'","",$cbo_year_name))!=0)
		{
			$mst_id= return_next_id("id","lib_capacity_allocation_mst",1);
			$field_array_mst="id,company_id,location_id,year_id,month_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array_mst="(".$mst_id.",".$cbo_company_name.",".$cbo_location_id.",".$cbo_year_name.",".$cbo_month.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,1);
			//$return_no=str_replace("'",'',$txt_system_id);
		}
		else
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_mst="location_id*year_id*month_id*updated_by*update_date*status_active*is_deleted";
			
			$data_array_mst="".$cbo_location_id."*".$cbo_year_name."*".$cbo_month."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,"id",$update_id,1);
		}
		
		$dtls_id=return_next_id( "id", "lib_capacity_allocation_dtls", 1 );
		$field_array_dtls="id,mst_id,buyer_id,allocation_percentage,inserted_by,insert_date,status_active,is_deleted";
		
		for($i=1; $i<=$tot_row_buyer; $i++)
		{
			$txt_buyer= "buyer_id_".$i;
			$txt_allocation_parcentage="txt_allocation_".$i;
			$update_id_dtls="update_id_dtls_".$i;
			
			if((str_replace("'",'',$$update_id_dtls)=="")||(str_replace("'",'',$$update_id_dtls)==0)&&(str_replace("'","",$cbo_year_name))!=0)
			{
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls.="(".$dtls_id.",".$mst_id.",".$$txt_buyer.",".$$txt_allocation_parcentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$dtls_id=$dtls_id+1;
			}
		}
		//echo "0**"."insert into lib_capacity_calc_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;	 
		$rID1=sql_insert("lib_capacity_allocation_dtls",$field_array_dtls,$data_array_dtls,1);
		//if($rID1) echo "r";
		if($db_type==0)
		{
			if( $rID && $rID1)
			{
			mysql_query("COMMIT");  
			echo "0**".str_replace("'",'',$mst_id)."**".str_replace("'",'',$dtls_id);
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo "10**".str_replace("'",'',$mst_id);
			}
		}
		disconnect($con);
		die;
		
	
	}

	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_mst="location_id*year_id*month_id*updated_by*update_date*status_active*is_deleted";
		$data_array_mst="".$cbo_location_id."*".$cbo_year_name."*".$cbo_month."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		$rID=sql_update("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,"id",$update_id,1);
		
		$id_arr=array();
		$data_array_dtls=array();
		$field_array_dtls="allocation_percentage";
		$dtls_id=return_next_id( "id", "lib_capacity_allocation_dtls", 1 );
		$mst_id=str_replace("'",'',$update_id);
		$field_array_dtls_in="id,mst_id,buyer_id,allocation_percentage,inserted_by,insert_date,status_active,is_deleted";
		$coma=0;
		for($i=1; $i<=$tot_row_buyer; $i++)
		{
			$txt_buyer= "buyer_id_".$i;
			$txt_allocation_percentage="txt_allocation_".$i;
			$update_id_dtls="update_id_dtls_".$i;
			
			if(str_replace("'",'',$$update_id_dtls)!=0)
			{
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_dtls[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$txt_allocation_percentage.""));
			}
			else
			{
				if(str_replace("'",'',$$update_id_dtls)==0)
				{
					if(str_replace("'",'',$$txt_allocation_percentage)!="")
					{
						if ($coma!=0)$field_array_dtls_in .=",";
				$data_array_dtls_in.="(".$dtls_id.",".$mst_id.",".$$txt_buyer.",".$$txt_allocation_percentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$dtls_id=$dtls_id+1;
				$coma++;
					}
				}
				
			}
		}
		
		$rID1=execute_query(bulk_update_sql_statement( " lib_capacity_allocation_dtls", "id", $field_array_dtls, $data_array_dtls, $id_arr ));
		$rID2=sql_insert("lib_capacity_allocation_dtls",$field_array_dtls_in,$data_array_dtls_in,1);
		
		
		
		if($db_type==0)
		{
			if($rID&&$rID1||$rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}

}


if($action=="load_php_dtls_form_return_id_buyer")
{
	$qry_result=sql_select( "select id,mst_id from  lib_capacity_allocation_dtls where mst_id='$data'");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		
	}
	echo $id;
}

/*Report Print*/
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
			$sql_con="SELECT  a.company_id,b.buyer_id,b.allocation_percentage";
			for($i=1;$i<=12;$i++)
			{
				 $sql_con .= ",SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs)   END) AS 'capa$i',SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_pcs* b.allocation_percentage)/100   END) AS 'sum$i'";
				
			}
			$sql_con .= " FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
WHERE a.id=b.mst_id AND a.year_id=c.year AND d.month_id=a.month_id AND  a.location_id=c.location_id AND c.id=d.mst_id AND a.company_id=$data[0]  AND a.year_id=$data[1] AND a.month_id=$data[3] AND a.location_id=$data[4]  GROUP BY b.buyer_id";
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
			$sql_con="SELECT  a.company_id,b.buyer_id,b.allocation_percentage";
			for($i=1;$i<=12;$i++)
			{
				 $sql_con .= ",SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_min)   END) AS 'capa$i',SUM(CASE WHEN d.month_id =".$i. " THEN (d.capacity_month_min* b.allocation_percentage)/100   END) AS 'sum$i' ";
				
			}
			$sql_con .= " FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d
WHERE a.id=b.mst_id AND a.year_id=c.year AND d.month_id=a.month_id AND  a.location_id=c.location_id AND c.id=d.mst_id AND a.company_id=$data[0] AND a.year_id=$data[1] AND a.month_id=$data[3] AND a.location_id=$data[4] GROUP BY b.buyer_id";
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

