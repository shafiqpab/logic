<?
	include('includes/common.php');
	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$location_arr=return_library_array("select id,location_name from  lib_location where status_active=1 and is_deleted=0","id","location_name");
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor where status_active=1 and is_deleted=0","id","floor_name");
	$line_arr = return_library_array("select id,line_name from lib_sewing_line where status_active=1 and is_deleted=0","id","line_name");
	 
	$today_date=date('Y-m-d');
	//$ctime = 11;//date('H')-1;
	$pre_prod_date=date("d-m-Y");
	
	//echo $pre_prod_date;
	$ctime =date('H');
	
	if( $ctime=="14" )
		$ctime=$ctime-1;
	
 //	$hour=$date-1;
 	//$hour=10;
	$from_hour=date("d-M-Y",time())." ".str_pad($ctime, 2, "0", STR_PAD_LEFT).":00:00";
	$to_hour=date("d-M-Y",time())." ".str_pad($ctime, 2, "0", STR_PAD_LEFT).":59:59";
	 //echo str_pad($ctime,2,"0").'=='.str_pad($ctime, 2, "0", STR_PAD_LEFT);
	// die;
	
//	echo"select a.id, a.company_id as com, a.location_id as loc, a.floor_id as flr, a.line_number as line, b.target_per_hour as trgt,working_hour,man_power,smv_adjust,smv_adjust_type  from prod_resource_mst a,prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.pr_date=to_date('".$today_date."', 'YYYY-MM-DD')";
//	die;
	 $sewing_output_sql=sql_select($sql);
	$htarget_sql=sql_select("select a.id, a.company_id as com, a.location_id as loc, a.floor_id as flr, a.line_number as line, b.target_per_hour as trgt,working_hour,man_power,smv_adjust,smv_adjust_type  from prod_resource_mst a,prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.company_id=3 and b.pr_date=to_date('".$today_date."', 'YYYY-MM-DD')");
	foreach($htarget_sql as $row)
	{
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['hourt']=$row[csf("trgt")];
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['total']=$row[csf("trgt")]*$row[csf("working_hour")];
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['line']=$row[csf("line")];
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['working_hour']=$row[csf("working_hour")];
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['man_power']=$row[csf("man_power")];
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['smv_adjust']=$row[csf("smv_adjust")];
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['smv_adjust_type']=$row[csf("smv_adjust_type")];
		$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['man_power']=$row[csf("man_power")];
		//$htarget_arr[$row[csf("com")]][$row[csf("loc")]][$row[csf("flr")]][$row[csf("id")]]['man_power']=$row[csf("man_power")];
		//northern -- test db
	  
	}
	
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in (3) and variable_list=25 and status_active=1 and is_deleted=0");
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in(3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				if($smv_source==1)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
				}
				else if($smv_source==2)
				{
					$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
				}
			}
		}
		
		$prod_start_time=sql_select("select $prod_start_cond as prod_start_time from variable_settings_production where company_name=3 and variable_list=26 and status_active=1 and is_deleted=0 and shift_id=1");
		foreach($prod_start_time as $row)
		{
			//$ex_time=explode(" ",$row[csf('prod_start_time')]);
			$variable_start_time_arr=$row[csf('prod_start_time')];
		}//die;
		if ($variable_start_time_arr=='') $variable_start_time_arr="08:00";
		$ex_time=$ctime;
		$current_eff_min=($ctime*60);
		$variable_time= explode(":",$variable_start_time_arr);
		$vari_min=($variable_time[0]*60)+$variable_time[1];
		$difa_time=explode(".",number_format(($current_eff_min-$vari_min)/60,2));//datediff("",$ctime,$variable_start_time_arr);
		$dif_time=$difa_time[0];
		$dif_hour_min=date("H:i", strtotime($dif_time));
		
		$defect_qty_arr=array();
		$sql_defect="select mst_id, defect_qty from pro_gmts_prod_dft where production_type=5  and  defect_type_id in (3,4) and status_active=1 and is_deleted=0";
		$sql_defect_res=sql_select($sql_defect);
		foreach($sql_defect_res as $drow)
		{
			$defect_qty_arr[$drow[csf("mst_id")]]+=$drow[csf("defect_qty")];
		}
		unset($sql_defect_res);
		
	 $sql="SELECT  a.id, a.serving_company as company_id, a.location,a.floor_id, a.sewing_line, b.production_qnty as qntys, b.alter_qty, b.reject_qty, b.spot_qty,b.replace_qty, TO_CHAR(a.production_hour,'DD-MON-YYYY HH24:MI:SS') as production_hour, a.production_type, a.po_break_down_id, a.item_number_id FROM pro_garments_production_mst a,pro_garments_production_dtls b WHERE  a.id =b.mst_id and 
	a.production_type in (4,5,11) and  b.production_type in (4,5,11)  and (a.production_date)= to_date('".$today_date."', 'YYYY-MM-DD') and a.serving_company=3 and a.sewing_line!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	";
// echo $sql; die; //--production_hour between to_date('".$from_hour."', 'DD-MM-YYYY HH24:MI:SS')  and to_date('".$to_hour."', 'DD-MM-YYYY HH24:MI:SS')   AND 
// group by a.id, a.serving_company , a.location,a.floor_id, a.sewing_line, b.alter_qty, b.reject_qty, b.spot_qty,b.replace_qty, production_hour,a.production_type, a.po_break_down_id, a.item_number_id 
	 $sewing_output_sql=sql_select($sql); $tot_rows=0; $poIds=''; $lineIds='';
	 foreach( $sewing_output_sql as $row )
	 {
		 $tot_rows++;
		 $poIds.=$row[csf("po_break_down_id")].",";
		 $lineIds.=$row[csf("sewing_line")].",";
		if($row[csf("production_type")]==5 || $row[csf("production_type")]==11)
		{
			if( date("Y-m-d H:i:s",strtotime($row[csf("production_hour")]))>=(date("Y-m-d H:i:s",strtotime($from_hour))) && (date("Y-m-d H:i:s",strtotime($row[csf("production_hour")])))<=(date("Y-m-d H:i:s",strtotime($to_hour))) )
			{
				 if($row[csf("production_type")]==5)
					$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['hourqnty_sew']+=$row[csf("qntys")];
				 else if($row[csf("production_type")]==11)
					$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['hourqnty_poly']+=$row[csf("qntys")];
				 $houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['prdhour']=$ctime;
			 }
		}
		else
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['hourqnty_sewin']+=$row[csf("qntys")];
		
		 if($row[csf("production_type")]==5)
		 {
			//$checked_gmts_item=($row[csf("qntys")]+$row[csf("alter_qnty")]+$row[csf("reject_qnty")]+$row[csf("spot_qnty")])-$row[csf("replace_qty")];
			$checked_gmts_item=($row[csf("qntys")]+$row[csf("alter_qty")]+$row[csf("spot_qty")]+$row[csf("reject_qty")]);
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['defect_qty']+=$defect_qty_arr[$row[csf("id")]];
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['checked_gmts_item']+=$checked_gmts_item;
			$defect_qty_arr[$row[csf("id")]]=0;
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['totalqnty_sew']+=$row[csf("qntys")];
			$produce_minit=$row[csf("qntys")]*$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]];
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['produce_minit']+=$produce_minit;
		 }
		 else if($row[csf("production_type")]==11)
		 {
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['totalqnty_poly']+=$row[csf("qntys")];
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['produce_minit_poly']+=$row[csf("qntys")]*$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]];
		 }
		 else
			$houry[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['totalqnty_sewin']+=$row[csf("qntys")];
			
			$po_break_down_id[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
			
			//$line_wise_po_arr[$row[csf("sewing_line")]].=$row[csf("po_break_down_id")].',';
	//	if($row[csf("production_type")]==11)
			$line_wise_po_arr[$row[csf("po_break_down_id")]][$row[csf("sewing_line")]]=$row[csf("sewing_line")];
	 }
	 unset($sewing_output_sql);
	 
	// echo "10**";
	 $poIds=chop($poIds,',');
	 $lineIds=chop($lineIds,',');
	 $poIds=implode(",",array_unique(explode(",",$poIds)));
	 $lineIds=implode(",",array_unique(explode(",",$lineIds)));
	//echo $poIds.'=='.$lineIds; die; 
	 if($poIds!='')
	 {
		 $sql_tot_prod="SELECT a.id, a.serving_company as company_id, a.location, a.floor_id, a.sewing_line, b.production_qnty as qntys, b.alter_qty, b.reject_qty, b.spot_qty, b.replace_qty, a.production_type, a.po_break_down_id, a.item_number_id FROM pro_garments_production_mst a,pro_garments_production_dtls b WHERE   a.id=b.mst_id and 
	a.production_type in (4,5,11) and b.production_type in (4,5,11) and (a.production_date)<=to_date('".$today_date."', 'YYYY-MM-DD') 
	and a.po_break_down_id in ($poIds) 
	and a.sewing_line in ($lineIds) 
	and a.serving_company=3 and a.sewing_line!=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $sql_tot_prod; die;  group by a.id, a.serving_company , a.location, a.floor_id, a.sewing_line,  b.alter_qty, b.reject_qty, b.spot_qty, b.replace_qty, a.production_type, a.po_break_down_id, a.item_number_id
		$grand_prod_arr=array();
		$sql_tot_prod_res=sql_select( $sql_tot_prod);
		foreach($sql_tot_prod_res as $row)
		{
			if( $line_wise_po_arr[$row[csf("po_break_down_id")]][$row[csf("sewing_line")]]==$row[csf("sewing_line")] )
			{
				if($row[csf("production_type")]==4)
					$grand_prod_arr[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['grand_sew_in']+=$row[csf("qntys")];
				else if($row[csf("production_type")]==5)
				{
					$grand_prod_arr[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['grand_sew_out']+=$row[csf("qntys")];
					//if($po_rej[$row[csf("id")]]=='')
					$grand_prod_arr[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['grand_sew_reject']+=$row[csf("reject_qty")];
						//$po_rej[$row[csf("id")]]=$row[csf("id")];
				}
				else if($row[csf("production_type")]==11)
					$grand_prod_arr[$row[csf("company_id")]][$row[csf("location")]][$row[csf("floor_id")]][$row[csf("sewing_line")]]['grand_poly']+=$row[csf("qntys")];
			}
		}
		unset($sql_tot_prod_res);
	 }
	 
	 
	 
	
	$con = connect();
 	$field_array="company,location,floor,line,hour,htarget,hsewqty,hpolyqty,totaltarget,totsewqty,totpolyqty,sewwip,polywip,seweff,polyeff,dhu";
  	 $truncate=execute_query("truncate table linedisplayboard",1);
	 foreach($houry as $comp=>$compdata)
	 {
		 foreach($compdata as $loca=>$locdata)
		 {
			 foreach($locdata as $floor=>$floordata)
			 {
				 foreach($floordata as $silne=>$sdata)
				 {
					// print_r( $sdata['hourqnty_sew'] );
					$htarget=$htarget_arr[$comp][$loca][$floor][$silne]['hourt'];
					$htargettotal=$htarget_arr[$comp][$loca][$floor][$silne]['total'];
					$allline=(int)$htarget_arr[$comp][$loca][$floor][$silne]['line'];
					$total_adjustment=0;
					if($htarget_arr[$comp][$loca][$floor][$silne]['smv_adjust_type']==1)
					{ 
						$total_adjustment=$htarget_arr[$comp][$loca][$floor][$silne]['smv_adjust'];
					}
					else if($htarget_arr[$comp][$loca][$floor][$silne]['smv_adjust_type']==2)
					{
						$total_adjustment=($htarget_arr[$comp][$loca][$floor][$silne]['smv_adjust'])*(-1);
					}
					$prod_wo_hour=$htarget_arr[$comp][$loca][$floor][$silne]['working_hour'];
					
					if ($dif_time<$prod_wo_hour)//
					{
						$current_wo_time=$dif_hour_min;
						$cla_cur_time=$dif_time;
					}
					else
					{
						$current_wo_time=$prod_wo_hour;
						$cla_cur_time=$prod_wo_hour;
					}
					
					$efficiency_min =$total_adjustment+($htarget_arr[$comp][$loca][$floor][$silne]['man_power']*$cla_cur_time*60);
					
					//$hour_target=$htarget_arr[$comp][$loca][$floor][$silne]['hourt'];
					
					$line_eff_sew=number_format((($sdata['produce_minit'])*100)/$efficiency_min,2);
					$line_eff_poly=number_format((($sdata['produce_minit_poly'])*100)/$efficiency_min,2);
					$check_qty=number_format((($sdata['defect_qty']/$sdata['checked_gmts_item'])*100),2);
					//echo $sdata['produce_minit'].'=='.$efficiency_min.'<br>';defect_qty checked_gmts_item
					$allline=$line_arr[$allline];
					//echo $check_qty.'='.$allline.'<br>'; 
					//echo $total_adjustment."==".$sdata['produce_minit'].'=='.$efficiency_min.'=='.$hour_target.'=='.$allline.'<br>';
					$grand_sew_in=0; $grand_sew_out=0; $grand_poly=0;
					$grand_sew_in=$grand_prod_arr[$comp][$loca][$floor][$silne]['grand_sew_in']; 
					$grand_sew_out=$grand_prod_arr[$comp][$loca][$floor][$silne]['grand_sew_out'] + $grand_prod_arr[$comp][$loca][$floor][$silne]['grand_sew_reject']; 
					$grand_poly=$grand_prod_arr[$comp][$loca][$floor][$silne]['grand_poly'] + $grand_prod_arr[$comp][$loca][$floor][$silne]['grand_sew_reject']; 
					
					if( $sdata['totalqnty_sew']<1) $check_qty=0;
					
					if( $allline!='') //$sdata['totalqnty_sew']>0 &&
					{
						if ( $data_array!="" ) $data_array.=",";
			
						//$data_array ="('".$company_arr[$comp]."','".$location_arr[$loca]."','".$floor_arr[$floor]."','".$allline."','".$ctime."','".$htarget."','".$sdata['hourqnty_sew']."','".$sdata['hourqnty_poly']."','".$htargettotal."','".$sdata['totalqnty_sew']."','".$sdata['totalqnty_poly']."','".($sdata['totalqnty_sewin']-$sdata['totalqnty_sew'])."','".($sdata['totalqnty_sewin']-$sdata['totalqnty_poly'])."','".$line_eff_sew."','".$line_eff_poly."','".$check_qty."')";
						$data_array ="('".$company_arr[$comp]."','".$location_arr[$loca]."','".$floor_arr[$floor]."','".$allline."','".$ctime."','".$htarget."','".$sdata['hourqnty_sew']."','".$sdata['hourqnty_poly']."','".$htargettotal."','".$sdata['totalqnty_sew']."','".$sdata['totalqnty_poly']."','".($grand_sew_in-$grand_sew_out)."','".($grand_sew_in-$grand_poly)."','".$line_eff_sew."','".$line_eff_poly."','".$check_qty."')";
						// echo $data_array;die;
						$rID=sql_insert("linedisplayboard", $field_array, $data_array,1);
					}
				 }
			 }
		 }
	 }
	// die;
 //oci_commit($con);   
	
	if($db_type==2 || $db_type==1 )
	{
		
		if($rID )
		{
			oci_commit($con);   
			echo " Inserted <br>";
		}
		else
		{
			oci_rollback($con);
			 echo " Not Inserted <br> ";
		}
	}

	 
 
    disconnect($con);
    die;
	 
	 
	 
 	
 	
 	$con = connect();
 	$field_array='company,location,floor,line,hour,htarget,hsewqty,hpolyqty,totaltarget,totsewqty,totpolyqty,sewwip,polywip,seweff,polyeff,dhu';
  	$truncate=execute_query("truncate table linedisplayboard",1);
	if(count($sewing_output_sql)>0)
	{
		$data_array="";
		for ($i=0;$i<count($sewing_output_sql);$i++)
	    {			 
			if ($data_array!="") $data_array.=",";
			$htarget=$htarget_arr[$sewing_output_sql[$i][csf("company_id")]][$sewing_output_sql[$i][csf("location")]][$sewing_output_sql[$i][csf("floor_id")]][$sewing_output_sql[$i][csf("sewing_line")]];

			$data_array .="('".$company_arr[$sewing_output_sql[$i][csf("company_id")]]."','".$location_arr[$sewing_output_sql[$i][csf("location")]]."','".$floor_arr[$sewing_output_sql[$i][csf("floor_id")]]."','".$line_arr[$sewing_output_sql[$i][csf("sewing_line")]]."','".$htarget."','".$sewing_output_sql[$i][csf("qntys")]."')";
	 				
	    }
	 //  echo $data_array;die;
	 	$rID=sql_insert("linedisplayboard",$field_array,$data_array,1);
	  	if($db_type==0)
			{
	 			if($rID)
				{
					mysql_query("COMMIT");  
					echo " Inserted <br>";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo " Not Inserted <br> ";
				}
			}

	 		if($db_type==2 || $db_type==1 )
			{
				
				if($rID )
			 	{
					oci_commit($con);   
					echo " Inserted <br>";
				}
				else
				{
					oci_rollback($con);
					 echo " Not Inserted <br> ";
				}
	 		}

	}
	else
	{
		echo "no data found";
	}
	
 
    disconnect($con);
    die;

?>
