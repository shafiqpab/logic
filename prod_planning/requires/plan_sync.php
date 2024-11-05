<?

include( '../../includes/common.php' );
 
 
 
$sql="select id,line_id,po_break_down_id,plan_id,start_date,start_hour,end_date,end_hour,duration,plan_qnty,comp_level,first_day_output,increment_qty,terget,day_wise_plan,company_id,location_id,item_number_id,off_day_plan,order_complexity,ship_date,extra_param from  ppl_sewing_plan_board order by line_id,start_date,end_date"; //and line_id in (174,175)   where  ( start_date > '".$from_date."'  or  end_date > '".$from_date."')  
	$sql_data=sql_select($sql);  
	$i=0;
	$rcount=count( $sql_data ) ;
	$line_wise_days=array();
	
	foreach( $sql_data as $rows )
	{
		$i++;
		if( $new_line[$rows[csf('line_id')]]=='' && $i!=1 )
		{
			echo "<br>";
		}
		//$line_wise_days
		$start_date=date("Y-m-d",strtotime( $rows[csf('start_date')] ));
		$end_date=date("Y-m-d",strtotime( $rows[csf('end_date')] ));
		//echo $rows[csf('day_wise_plan')]."=".$rows[csf('plan_qnty')]."<br>";
		
		//Check duplicate Plans here to delete them automatically Start
		if( $chk_plan_start[$rows[csf('line_id')]]['start'][$start_date]!='' && $chk_plan_end[$rows[csf('line_id')]]['end'][$end_date]==$chk_plan_start[$rows[csf('line_id')]]['start'][$start_date] )
		{
			$delete[$rows[csf('plan_id')]]=$rows[csf('plan_id')];
		}//Check duplicate Plans here to delete them automatically Ends
		else
		{
			$chk_plan_start[$rows[csf('line_id')]]['start'][$start_date]=$rows[csf('plan_id')];
			$chk_plan_end[$rows[csf('line_id')]]['end'][$end_date]=$rows[csf('plan_id')];
			
			for( $k=0; $k< $rows[csf('duration')]; $k++ )
			{
				$ndate=date("Y-m-d",strtotime(add_date($start_date,$k)));
				if($line_wise_days[$rows[csf('line_id')]][$ndate][$rows[csf('plan_id')]]=='')
				{
					$line_wise_days[$rows[csf('line_id')]][$ndate][$rows[csf('plan_id')]]=$rows[csf('plan_id')];
				}
				
				if($chk_plan[$rows[csf('line_id')]][$ndate]!='') 
				{ 
					//echo "duplicate"."="; 
					 $delete[$rows[csf('plan_id')]]=$rows[csf('plan_id')]; 
				}
				//echo $k."=".$ndate.'='.$rows[csf('plan_id')].'='.$rows[csf('line_id')].'='.date("d-M-y",strtotime( $ndate ))."<br>";
				$chk_plan[$rows[csf('line_id')]][$ndate]=$rows[csf('plan_id')];
			}
			//echo "<br>";
			//if($i==5) die;
			if( $i==$rcount )
			{
				//echo "<br>";
				//$new_line[$rows[csf('line_id')]]=$rows[csf('line_id')];
			}
		}
		//echo $rows[csf('line_id')]."==";
		$new_line[$rows[csf('line_id')]]=$rows[csf('line_id')];
	}
	echo "<br>";
	print_r($delete);
	die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID1=execute_query("delete from ppl_sewing_plan_board where plan_id in (".implode(",",$delete).")");
		 	
		if($db_type==0)
		{
			mysql_query("COMMIT");  
			 
		}
		if($db_type==2 || $db_type==1 )
		{
			oci_commit($con);  
			  
		}
		//echo "SDSD";
	 
	
	//0**109**5941**0**19-04-2016**0**19-04-2016**0**1**500**1**1000**100**1200**0**19-04-2016**19-04-2016**921750-1676 S-4**5.95**5**-134524**01-04-2016**2**01-04-2016**20160418**H n M**5.95**Moa Tank Top**0**12-04-2016
	echo '<pre>';
		print_r($delete);
?>