<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
    echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond  group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}
if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name in (".$data.") and module_id=4 and report_id=270 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}
if($action=="report_generate")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_allocation_company_id=str_replace("'","",$cbo_allocation_company_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$sew_date_from=str_replace("'","",$txt_sew_date_from);
	$sew_date_to=str_replace("'","",$txt_sew_date_to);
	
	$rpt_type_id=str_replace("'","",$type);
	
	$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	
	$sew_start_date=change_date_format(str_replace("'","",$sew_date_from),"","",1);
	$sew_end_date=change_date_format(str_replace("'","",$sew_date_to),"","",1);
	$date_cond="";$sew_date_cond="";
	if($start_date!="" && $end_date!="")
	{
		$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'"; 	
	}
	if($sew_start_date!="" && $sew_end_date!="")
	{
		
		$sew_date_cond=" and c.date_from>='$sew_start_date' and c.date_to<='$sew_end_date'"; 	
	}
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');
	//$line_name_library=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	//--------------------------------------------------------------------------------------------------------------------
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	//if( $cbo_allocation_company_id==0 ) $allc_comp2="";
	//	else $allc_comp2=" and a.style_owner in($cbo_allocation_company_id)";
	if( $cbo_company_id!=0 )//ppl_order_allocation_mst
	{
		$sql_allowcate=sql_select("select c.id as allocate_mst_id,c.company_id,c.item,c.customize_smv,c.smv, c.location_name,b.is_confirmed,a.style_ref_no,b.id as poid,b.po_number,a.company_name,a.buyer_name, b.unit_price,b.po_quantity,a.ship_mode,b.pub_shipment_date,d.date_name,d.qty from wo_po_details_master a, wo_po_break_down b, ppl_order_allocation_mst c,ppl_order_allocation_dtls d  where a.id=b.job_id  and b.id=c.po_no and c.id=d.mst_id and a.company_name in($cbo_company_id)  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1  $buyer_id_cond  $date_cond  order by d.date_name asc");
		  
		
		$allocationArr=array();$location_sew_planQty_arr=array();$sew_planQty_arr=array();
	   $pub_date_arr=array();$po_chkArr=array();
	   foreach($sql_allowcate as $row)
	   {
		   $allocationArr[$row[csf("allocate_mst_id")]].= strtotime($row[csf("date_name")]).',';
		   $date_allcate=date('M-y',strtotime($row[csf("date_name")]));
		   $date_cal=strtotime($row[csf("date_name")]);
		   $pub_date_arr[]=$date_cal;
		   if($po_chkArr[$row[csf("poid")]]=='')
		   {
		   $location_sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$date_allcate]=$row[csf("location_name")];
		   $sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$date_allcate]['qty']+= $row[csf("qty")];
		  // $sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("poid")]][$row[csf("item")]][$date_allcate]['c_smv']+= $row[csf("customize_smv")];
		   $po_chkArr[]=$row[csf("poid")];
		   }
	   }
	   //=====Allocation DTLS=============FOR DATE Wise============
 

		
		$sql_exe=sql_select("select a.job_no,a.style_ref_no,b.id,a.company_name,a.buyer_name, b.unit_price,b.po_quantity,a.ship_mode,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, ppl_order_allocation_mst c  where a.id=b.job_id  and b.id=c.po_no    and a.company_name in($cbo_company_id) $buyer_id_cond $date_cond   order by b.pub_shipment_date asc");
		//$pub_date_arr=array();
		foreach($sql_exe as $row)
		{
			$order_inf[$row[csf("id")]]['po_number']= $row[csf("po_number")];
			$order_inf[$row[csf("id")]]['style_ref_no']= $row[csf("style_ref_no")];
			$order_inf[$row[csf("id")]]['unit_price']= $row[csf("unit_price")];
			$order_inf[$row[csf("id")]]['po_quantity']= $row[csf("po_quantity")];
			//$companyArr[$row[csf("company_name")]][$row[csf("buyer_name")]]= $row[csf("company_name")];
			//$date_cal=strtotime($row[csf("pub_shipment_date")]);
			//$pub_date_arr[]=$date_cal;
			$poidArr[$row[csf("id")]]=$row[csf("id")];
			$buyer_powise[$row[csf("id")]]=$row[csf("buyer_name")];
		}
		ksort($pub_date_arr);
		foreach($pub_date_arr as $date_key)
		{
			$date_mon=date('M-y',$date_key);
			$mon_pub_date_arr[$date_mon]=$date_mon;
		}
		$po_id_cond_in=where_con_using_array($poidArr,0,'b.id');
		// print_r($mon_pub_date_arr);
		if( $cbo_allocation_company_id==0 ) $allc_comp="";
		else $allc_comp=" and c.company_id in($cbo_allocation_company_id)";
		$sql_allocation="select a.style_ref_no,b.id,a.buyer_name,c.company_id, c.location_name,b.is_confirmed,c.allocated_qty,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, ppl_order_allocation_mst c  where a.id=b.job_id and a.job_no=c.job_no and b.id=c.po_no  and a.company_name in($cbo_company_id) $date_cond  $buyer_id_cond $allc_comp $po_id_cond_in";

		  
		$sql_res_allocation=sql_select($sql_allocation);
		foreach($sql_res_allocation as $row)
		{
				//$date_allcate=date('M-y',strtotime($row[csf("pub_shipment_date")]));
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]]= $row[csf("company_id")];
				
				//$location_sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$date_allcate]=$row[csf("location_name")];
			//	$sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("is_confirmed")]][$date_allcate]['qty']+= $row[csf("allocated_qty")];
		}
		//print_r($sew_planQty_arr);
		unset($sql_res_allocation);
		$poid=implode(",",$bid);
		if($cbo_allocation_company_id==0) $allc_comp="";
		$po_cond=" and po_no in ($poid)";
	}

	if( $cbo_allocation_company_id==0 ) $allc_comp_capa="";
		else $allc_comp_capa=" and a.comapny_id in($cbo_allocation_company_id)";
		
	 $sql_capa="SELECT a.year,a.comapny_id,a.location_id,b.month_id,b.working_day, b.capacity_month_pcs AS capa  FROM lib_capacity_calc_mst a,  lib_capacity_year_dtls b
	WHERE a.id=b.mst_id   and a.status_active=1 and a.is_deleted=0 and b.working_day>0 $allc_comp_capa";
	$sql_data_capa=sql_select($sql_capa);
	foreach( $sql_data_capa as $row)
	{
	$date_key=date("M-y",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
	//echo $date_key.'d';
	$working_days_capa_arr[$row[csf("comapny_id")]][$row[csf("location_id")]][$date_key]+=$row[csf("working_day")];	
//	$tot_com_month_capa+=$row[csf("capa")];	
	}
	unset($sql_data_capa);
	
	$tbl_width=350+(count($mon_pub_date_arr)*80 );
	ob_start();
	?>
	<div style="width:100%" align="center">
    <p><b> Updated  Production Allocation Chart - of <? echo date("M-Y",strtotime($txt_date_from)).' To '.date("M-Y",strtotime($txt_date_to)); ?></b></p>
    <?
    foreach($companyArr as $compKey=>$comp_data)
	{
	?>
		<table width="<? echo $tbl_width+20; ?>" cellpadding="0" border="1" cellspacing="0" rules="all" class="rpt_table">
        <caption style=" font-size:24px"><b style="float:left"> Company: - <? echo $company_library[$compKey]; ?></b></caption>
			<thead>
				<tr>
					<th width="30">SL No</th>
					<th width="100">Buyer</th>
                    <th width="80">PO Status</th>
					<?
					foreach($mon_pub_date_arr as $mon_key=>$mon_val)
					{
						?>
						<th width="80"><? echo $mon_key; ?><br>Qty(Pcs)</th>
						<?
					}
					?>
					<th  width="">Total Qty</th>

				</tr>
			</thead>
		</table>
	<div style="width:<? echo $tbl_width+20; ?>px; max-height:320px; overflow-y:scroll"  align="left" id="scroll_body">
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">  
		<?
		$i=1;$tot_confirmQtyArr=array();$tot_confProjQtyArr=array();$tot_projectQtyArr=array();$tot_workingArr=array();
		foreach($comp_data as $buyer=>$val)
		{
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
			?>
             <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i.$compKey.$buyer; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$compKey.$buyer; ?>">
                <td width="30"  rowspan="2" ><? echo $i; ?></td>
                <td width="100"  rowspan="2" ><p style="word-wrap:break-word"><? echo $buyer_arr[$buyer]; ?></p></td>
                <td align="left" width="80">Confirm</td>
            <?
			 $tot_confirmQty=0;
            foreach($mon_pub_date_arr as $mon_key=>$mon_val)
            {
                $confirmedQty=$sew_planQty_arr[$compKey][$buyer][1][$mon_key]['qty'];
				$tot_confirmQty+=$confirmedQty;
				$tot_confirmQtyArr[$mon_key]+=$confirmedQty;
				$tot_confProjQtyArr[$mon_key]+=$confirmedQty;
				
				$location_id=$location_sew_planQty_arr[$compKey][$buyer][$mon_key];
				$working_days=$working_days_capa_arr[$compKey][$location_id][$mon_key];
				
				$tot_workingArr[$mon_key]+=$working_days;
				?>
                    <td  align="right" width="80"><? echo number_format($confirmedQty,0); ?></td>
                <?
            }
            ?>
            	<td align="right" width=""><? echo number_format($tot_confirmQty,0); ?></td>
            </tr>
             <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i.$compKey.$buyer; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$compKey.$buyer; ?>">
            	<td align="left" width="80">Projection</td>
            <?
			 $tot_projectQty=0;
            foreach($mon_pub_date_arr as $mon_key=>$mon_val)
            {
                $projectQty=$sew_planQty_arr[$compKey][$buyer][2][$mon_key]['qty'];
				 $tot_projectQty+=$projectQty;
				 $tot_projectQtyArr[$mon_key]+=$projectQty;
				 $tot_confProjQtyArr[$mon_key]+=$projectQty;
				?>
               <td  align="right" width="80"><? echo number_format($projectQty,0); ?></td>
                <?
            }
            ?>
              <td align="right" width=""><? echo number_format($tot_projectQty,0); ?></td>
            </tr>
        <?
		$i++;
		}?>
			<tr align="right" bgcolor="#EFEFEF" >
				<td colspan="3" > <b>Pord. Qnty per day</b> </td>
				<?
				$tot_per_day=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$perDay_tot_confProjQty=$tot_confProjQtyArr[$mon_key];
					$perDay_tot_working=$tot_workingArr[$mon_key];
					if($perDay_tot_confProjQty>0 && $perDay_tot_working>0)
					{
					$tot_per_day+=$perDay_tot_confProjQty/$perDay_tot_working;
					}
					?>
					<td  align="right" width="80" title="Total Qty/Working Day"><? echo fn_number_format($perDay_tot_confProjQty/$perDay_tot_working,0); ?></td>
					<?
				}
				?>
                <td align="right" width=""><? echo fn_number_format($tot_per_day,0); ?></td>	
			</tr>
            <tr align="right" bgcolor="#EFEFEF">
				<td colspan="3"> <b>Working day:</b></td>
				<?
				$tot_working=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$tot_working+=$tot_workingArr[$mon_key];
					?>
					<td  align="right"  width="80"><b><? echo fn_number_format($tot_workingArr[$mon_key],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($tot_working,0); ?></b></td>	
			</tr>
             <tr align="right"  bgcolor="#EFEFEF">
				<td colspan="3"> <?=$company_short_library[$compKey];?>: <b>&nbsp;Total:</b></td>
				<?
				$tot_confProjQty=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$tot_confProjQty+=$tot_confProjQtyArr[$mon_key];
					?>
					<td  align="right"  width="80"><b><? echo number_format($tot_confProjQtyArr[$mon_key],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($tot_confProjQty,0); ?></b></td>	
			</tr>
             <tr align="right"  bgcolor="#EFEFEF">
				<td colspan="3"><b> Confirm:</b></td>
				<?
				$grd_tot_confirmQty=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$grd_tot_confirmQty+=$confirmedQty;
					?>
					<td  align="right"  width="80"><b><? echo number_format($tot_confirmQtyArr[$mon_key],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($grd_tot_confirmQty,0); ?></b></td>	
			</tr>
             <tr align="right" bgcolor="#EFEFEF">
				<td colspan="3" ><b> Projection:</b></td>
				<?
				$grd_tot_projectQty=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$grd_tot_projectQty+=$confirmedQty;
					?>
					<td  align="right" width="80"><b><? echo number_format($tot_projectQtyArr[$mon_key],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($grd_tot_projectQty,0); ?></b></td>	
			</tr>
			<?
		 
		?>
	 
</table>
</div>
<?
	} //============Company End========================
?>
</div>

	<?
	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename####$rpt_type_id";
	exit();
	
}
if($action=="report_generate2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_allocation_company_id=str_replace("'","",$cbo_allocation_company_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	$txt_sew_date_from=str_replace("'","",$txt_sew_date_from);
	$txt_sew_date_to=str_replace("'","",$txt_sew_date_to);
	$rpt_type_id=str_replace("'","",$type);
	 
	$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
	
	//$sew_start_date=change_date_format(str_replace("'","",$txt_sew_date_from),"","",1);
	//$sew_end_date=change_date_format(str_replace("'","",$txt_sew_date_to),"","",1);
	$date_cond="";$sew_date_cond="";
	if($start_date!="" && $end_date!="")
	{
		// $date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'"; 	
		$date_cond=" and b.pack_handover_date between '$start_date' and '$end_date'"; 	
	}
	if($sew_start_date!="" && $sew_end_date!="")
	{
		$sew_date_cond=" and c.date_from>='$sew_start_date' and c.date_to<='$sew_end_date'";
		 //$sew_date_cond=" and d.date_name between '$sew_start_date' and '$sew_end_date'";
		 
	}	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location",'id','location_name');
	//$line_name_library=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	//--------------------------------------------------------------------------------------------------------------------
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	//if( $cbo_allocation_company_id==0 ) $allc_comp2="";
	//	else $allc_comp2=" and a.style_owner in($cbo_allocation_company_id)";
	if( $cbo_company_id!=0 )//ppl_order_allocation_mst
	{
		// $sql_allowcate="SELECT a.id AS details_mst_id, b.pack_handover_date,a.JOB_NO,
        //  b.is_confirmed,style_ref_no, b.id AS poid, b.po_number, a.company_name, a.buyer_name, b.unit_price, b.po_quantity, a.ship_mode,
        //  b.pub_shipment_date FROM wo_po_details_master a, wo_po_break_down b WHERE a.id = b.job_id
        //  AND a.company_name IN ($cbo_company_id) AND a.status_active = 1 AND b.status_active = 1 $buyer_id_cond $sew_date_cond $date_cond ORDER BY b.pack_handover_date ASC";
		// echo $sql_allowcate;exit;



		  $sql_allowcate=sql_select("select c.id as allocate_mst_id,c.company_id,c.item,c.customize_smv,c.smv, c.location_name,b.is_confirmed,a.style_ref_no,b.id as poid,b.po_number,a.company_name,a.buyer_name, b.unit_price,b.po_quantity,a.ship_mode,b.pub_shipment_date,d.date_name,d.qty from wo_po_details_master a, wo_po_break_down b, ppl_order_allocation_mst c,ppl_order_allocation_dtls d  where a.id=b.job_id  and b.id=c.po_no and c.id=d.mst_id and a.company_name in($cbo_company_id)  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1  $buyer_id_cond $sew_date_cond $date_cond  order by d.date_name asc");
		  
		
		 $allocationArr=array();$location_sew_planQty_arr=array();$sew_planQty_arr=array();
		$pub_date_arr=array();$po_chkArr=array();
		foreach($sql_allowcate as $row)
		{
			$allocationArr[$row[csf("allocate_mst_id")]].= strtotime($row[csf("date_name")]).',';
			$date_allcate=date('M-y',strtotime($row[csf("date_name")]));
			$date_cal=strtotime($row[csf("date_name")]);
			$pub_date_arr[]=$date_cal;
			if($po_chkArr[$row[csf("poid")]]=='')
			{
			$location_sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("poid")]][$row[csf("item")]][$date_allcate]=$row[csf("location_name")];
			$sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("poid")]][$row[csf("item")]][$date_allcate]['qty']+= $row[csf("qty")];
			$sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("poid")]][$row[csf("item")]][$date_allcate]['c_smv']+= $row[csf("customize_smv")];
			$po_chkArr[]=$row[csf("poid")];
			}
		}
		
 		$sql_exe=sql_select("select c.id as allo_mst_id,c.date_from,c.date_to,a.style_ref_no,b.id,b.po_number,a.company_name,a.buyer_name, b.unit_price,b.po_quantity,a.ship_mode,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, ppl_order_allocation_mst c  where a.id=b.job_id  and b.id=c.po_no  and a.company_name in($cbo_company_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 $buyer_id_cond $sew_date_cond $date_cond  order by b.pub_shipment_date asc");
		//echo "select c.id as allo_mst_id,a.style_ref_no,b.id,b.po_number,a.company_name,a.buyer_name, b.unit_price,b.po_quantity,a.ship_mode,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, ppl_order_allocation_mst c  where a.id=b.job_id  and b.id=c.po_no  and a.company_name in($cbo_company_id) and a.status_active=1 and b.status_active=1 and c.status_active=1 $buyer_id_cond $sew_date_cond $date_cond  order by b.pub_shipment_date asc";
		$poIdChkArr=array();
		foreach($sql_exe as $row)
		{
			$order_inf[$row[csf("id")]]['po_number']= $row[csf("po_number")];
			$order_inf[$row[csf("id")]]['style_ref_no']= $row[csf("style_ref_no")];
			$order_inf[$row[csf("id")]]['style_ref_no']= $row[csf("style_ref_no")];
			if($poIdChkArr[$row[csf("id")]]=="")
			{
			$order_inf[$row[csf("id")]]['unit_price']= $row[csf("unit_price")];
			$order_inf[$row[csf("id")]]['po_quantity']= $row[csf("po_quantity")];
			$poIdChkArr[$row[csf("id")]]=$row[csf("id")];
			}
			
			/*$allocation_date=rtrim($allocationArr[$row[csf("allo_mst_id")]],',');
			$allocation_dateArr=array_unique(explode(",",$allocation_date));
			foreach($allocation_dateArr as $allo_date_key)
			{
				$pub_date_arr[]=$allo_date_key;
			}*/
			//$date_from=date("Y-m-t", strtotime($row[csf("date_from")]));
			//$date_to=date("Y-m-t", strtotime($row[csf("date_to")]));
			//echo $date_from.'F='.$date_to.'<br>';
			//$companyArr[$row[csf("company_name")]][$row[csf("buyer_name")]]= $row[csf("company_name")];
			//$date_cal=strtotime($row[csf("pub_shipment_date")]);
			//$pub_date_arr[]=$date_cal;
			$poidArr[$row[csf("id")]]=$row[csf("id")];
			$buyer_powise[$row[csf("id")]]=$row[csf("buyer_name")];
		}
		ksort($pub_date_arr);
		foreach($pub_date_arr as $date_key)
		{
			$date_mon=date('M-y',$date_key);
			$mon_pub_date_arr[$date_mon]=$date_mon;
		}
		$po_id_cond_in=where_con_using_array($poidArr,0,'b.id');
		// print_r($mon_pub_date_arr);
		if( $cbo_allocation_company_id==0 ) $allc_comp="";
		else $allc_comp=" and c.company_id in($cbo_allocation_company_id)";

		$sql_allocation="select a.job_no,a.style_ref_no,b.id,b.unit_price,b.po_total_price,a.buyer_name,c.company_id,c.item,c.customize_smv,c.smv, c.location_name,b.is_confirmed,c.allocated_qty,c.po_quantity as actual_po_qty,b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b, ppl_order_allocation_mst c  where a.id=b.job_id and a.job_no=c.job_no and b.id=c.po_no and a.company_name in($cbo_company_id) $sew_date_cond $date_cond $buyer_id_cond $allc_comp $po_id_cond_in";
		// echo $sql_allocation;die;
		$confcompanyBuyerArr=array(); 
		$projcompanyBuyerArr=array();
		$sql_res_allocation=sql_select($sql_allocation);
		foreach($sql_res_allocation as $row)
		{
				$date_allcate=date('M-y',strtotime($row[csf("pub_shipment_date")]));
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['pub_shipment_date']= $row[csf("pub_shipment_date")];
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['customize_smv']= $row[csf("customize_smv")];
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['smv']= $row[csf("smv")];
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['actual_po_qty']= $row[csf("actual_po_qty")];
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['unit_price']= $row[csf("unit_price")];
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['po_total_price']= $row[csf("po_total_price")];
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['is_confirmed']= $row[csf("is_confirmed")];
				$companyArr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]]['job_no']= $row[csf("job_no")];
				
				if($row[csf("is_confirmed")]==1)
				{
					$confcompanyBuyerArr[$row[csf("is_confirmed")]][$row[csf("buyer_name")]]+= $row[csf("actual_po_qty")];
				}
				else
				{
					$projcompanyBuyerArr[$row[csf("is_confirmed")]][$row[csf("buyer_name")]]+= $row[csf("actual_po_qty")];
				}
				
				//$location_sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]][$date_allcate]=$row[csf("location_name")];
				////$sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]][$date_allcate]['qty']+= $row[csf("allocated_qty")];
				//$sew_planQty_arr[$row[csf("company_id")]][$row[csf("buyer_name")]][$row[csf("id")]][$row[csf("item")]][$date_allcate]['c_smv']+= $row[csf("customize_smv")];
		}
		//print_r($sew_planQty_arr);
		unset($sql_res_allocation);
		$poid=implode(",",$bid);
		if($cbo_allocation_company_id==0) $allc_comp="";
		$po_cond=" and po_no in ($poid)";
	}
	unset($sql_res_allocation);
	/*if( $cbo_allocation_company_id==0 ) $allc_comp_capa="";
		else $allc_comp_capa=" and a.comapny_id in($cbo_allocation_company_id)";
		
		if( $cbo_company_id==0 ) $allc_comp_capa2="";
		else $allc_comp_capa2=" and a.comapny_id in($cbo_company_id)";*/
		if( $cbo_allocation_company_id==0 && $cbo_company_id!=0 )
		{
			$allc_comp_capa=" and a.comapny_id in($cbo_company_id)";
		}
		else if( $cbo_allocation_company_id!=0 && $cbo_company_id==0 )
		{
			$allc_comp_capa=" and a.comapny_id in($cbo_allocation_company_id)";
		}
		
	$sql_capa="SELECT a.year, a.comapny_id, a.location_id, a.basic_smv, b.month_id, b.working_day, b.capacity_month_pcs AS capa  FROM lib_capacity_calc_mst a,  lib_capacity_year_dtls b
	WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.working_day>0  $allc_comp_capa__ ";
 	//echo $sql_capa;exit;
	$sql_data_capa=sql_select($sql_capa);
	$working_days_capa_arr=array();
	foreach( $sql_data_capa as $row)
	{
		$date_key=date("M-y",strtotime($row[csf("year")].'-'.$row[csf("month_id")]));
		//echo $date_key.'d';
		$working_days_capa_arr[$row[csf("comapny_id")]][$date_key]+=$row[csf("working_day")];	
		$working_days_capa_arr[$row[csf("comapny_id")]][$date_key]=$row[csf("basic_smv")];	
	//	$tot_com_month_capa+=$row[csf("capa")];	
	}
	 
	unset($sql_data_capa);
	//working_days_capa_arr[$compKey][$location_id][$mon_key]
	// print_r($working_days_capa_arr);
	$tbl_width=930+(count($mon_pub_date_arr)*80 );
	ob_start();
	
	$summaryArr=array();$summ_tot_workingArr=array();
	 foreach($companyArr as $compKey=>$comp_data)
	 {
		foreach($comp_data as $buyer=>$buyerData)
		{
			foreach($buyerData as $poId=>$poData)
		    {
			  foreach($poData as $itemId=>$val)
		      {
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$is_confirmed=$companyArr[$compKey][$buyer][$poId][$itemId]['is_confirmed'];
					$allocated_Qty=$sew_planQty_arr[$compKey][$buyer][$poId][$itemId][$mon_key]['qty'];
					$summaryArr[$is_confirmed][$buyer][$mon_key]+=$allocated_Qty;
					
					$location_id=$location_sew_planQty_arr[$compKey][$buyer][$poId][$itemId][$mon_key];
					
					$summ_working_days=$working_days_capa_arr[$compKey][$mon_key];
				  	//echo $summ_working_days.'='.$mon_key.'<br>';
					$summ_tot_workingArr[$mon_key][$is_confirmed]=$summ_working_days;
					
					if($val['customize_smv'])
					{
					$booking_convert=($val['smv']*$val['actual_po_qty'])/$val['customize_smv'];
					}
					else $booking_convert=0;
				} //Month end
			  }
			}
		}
	}
	
	$tbl_width_summ=230+(count($mon_pub_date_arr)*80);
	?>
	<div style="width:100%" align="center">
    <p><b> Updated  Production Allocation Chart - of <? if($start_date!="") echo date("d-M-Y",strtotime($start_date)).' To '.date("d-M-Y",strtotime($end_date));
		else  echo date("d-M-Y",strtotime($sew_start_date)).' To '.date("d-M-Y",strtotime($sew_end_date)); ?></b></p>
    
    <div style="width:<? echo $tbl_width_summ+20; ?>px;margin:5px; float:left" >
     <table width="<? echo ($tbl_width_summ+20)*2; ?>" align="left"  cellpadding="0" border="1" cellspacing="0" rules="all" class="rpt_table">
     <tr>
     <td>
    <table width="<? echo $tbl_width_summ+20; ?>" style="margin:5px;" cellpadding="0" border="1" cellspacing="0" rules="all" class="rpt_table">
        <caption style=" font-size:24px"><b style="float:left"> Summary(Confirmed):</b></caption>
			<thead>
				<tr>
					<th width="30">SL No</th>
					<th width="100">Buyer</th>
					<?
					foreach($mon_pub_date_arr as $mon_key=>$mon_val)
					{
						?>
						<th width="80"><? echo $mon_key; ?><br>Confirm<br>Qty(Pcs)</th>
						<?
					}
					?>
					<th  width="">Total Qty</th>

				</tr>
			</thead>
            <tbody>
            <?
			$k=1;$summary_confirmQtyArr=array();
            foreach($confcompanyBuyerArr as $conf_key=>$confData)
		    {
			foreach($confData as $buyerId=>$row)
		    {
            ?>
             <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $k.$buyerId; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k.$buyerId; ?>">
               <td width="30"><? echo $k; ?></td>
               <td width="100"><p style="word-wrap:break-word"><? echo $buyer_arr[$buyerId]; ?></p></td>
               <?
					$tot_summ_conf_qty=0;
					foreach($mon_pub_date_arr as $mon_key=>$mon_val)
					{
						$summ_conf_qty=$summaryArr[1][$buyerId][$mon_key];
						$tot_summ_conf_qty+=$summ_conf_qty;
						$summary_confirmQtyArr[$mon_key][$conf_key]+=$summ_conf_qty;
						?>
						<td width="80" align="right"><? echo fn_number_format($summ_conf_qty,2); ?></td>
						<?
					}
					?>
               <td width="" align="right"><p style="word-wrap:break-word"><? echo fn_number_format($tot_summ_conf_qty,2); ?></p></td>
             </tr>
             <?
			 $k++;
			 }
			}
			 ?>
             
			<tr align="right" bgcolor="#EFEFEF" >
				<td colspan="2" > <b>Prod. Qnty per day</b> </td>
				<?
				$summ_tot_per_day=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$summ_perDay_tot_confQty=$summary_confirmQtyArr[$mon_key][1];
					$summ_perDay_tot_working=$summ_tot_workingArr[$mon_key][1];
					if($summ_perDay_tot_confProjQty>0 && $summ_perDay_tot_working>0)
					{
					$summ_tot_per_day+=$summ_perDay_tot_confQty/$summ_perDay_tot_working;
					}
					?>
					<td  align="right" width="80" title="Total Qty/Working Day"><? echo fn_number_format($summ_perDay_tot_confQty/$summ_perDay_tot_working,0); ?></td>
					<?
				}
				?>
                <td align="right" width=""><? echo fn_number_format($summ_tot_per_day,0); ?></td>	
			</tr>
            <tr align="right" bgcolor="#EFEFEF">
				<td colspan="2"> <b>Working day:</b></td>
				<?
				$summ_tot_working=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$summ_tot_working+=$summ_tot_workingArr[$mon_key][1];
					?>
					<td  align="right"  width="80"><b><? echo fn_number_format($summ_tot_workingArr[$mon_key][1],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($summ_tot_working,0); ?></b></td>	
			</tr>
             <tr align="right"  bgcolor="#EFEFEF">
				<td colspan="2"> <? //$company_short_library[$compKey];?><b>&nbsp;Total:</b></td>
				<?
				$summ_tot_confQty=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$summ_tot_confQty+=$summary_confirmQtyArr[$mon_key][1];
					?>
					<td  align="right"  width="80"><b><? echo number_format($summary_confirmQtyArr[$mon_key][1],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($summ_tot_confQty,0); ?></b></td>	
			</tr>
            </tbody>
	 </table>
     </td>
     
         <td>
         <table width="<? echo $tbl_width_summ+20; ?>" cellpadding="0" style="margin:5px;" border="1" cellspacing="0" rules="all" class="rpt_table">
       	 <caption style=" font-size:24px"><b style="float:left"> Summary(Projected):</b></caption>
			<thead>
				<tr>
					<th width="30">SL No</th>
					<th width="100">Buyer</th>
					<?
					foreach($mon_pub_date_arr as $mon_key=>$mon_val)
					{
						?>
						<th width="80"><? echo $mon_key; ?><br>Projected<br>Qty(Pcs)</th>
						<?
					}
					?>
					<th  width="">Total Qty</th>

				</tr>
			</thead>
            <tbody>
            <?
			$k=1;$summary_projQtyArr=array();
            foreach($projcompanyBuyerArr as $conf_key=>$confData)
		    {
			foreach($confData as $buyerId=>$row)
		    {
            ?>
             <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $k.$buyerId; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k.$buyerId; ?>">
               <td width="30"><? echo $k; ?></td>
               <td width="100"><p style="word-wrap:break-word"><? echo $buyer_arr[$buyerId]; ?></p></td>
               <?
					$tot_summ_proj_qty=0;
					foreach($mon_pub_date_arr as $mon_key=>$mon_val)
					{
						$summ_proj_qty=$summaryArr[2][$buyerId][$mon_key];
						$tot_summ_proj_qty+=$summ_proj_qty;
						$summary_projQtyArr[$mon_key][$conf_key]+=$summ_proj_qty;
						 
						
						//$tot_confProjQtyArr[$mon_key]+=$confirmedQty;
						?>
						<td width="80" align="right"><? echo fn_number_format($summ_conf_qty,2); ?></td>
						<?
					}
					?>
               <td width="" align="right"><p style="word-wrap:break-word"><? echo fn_number_format($tot_summ_proj_qty,2); ?></p></td>
             </tr>
             <?
			 $k++;
			 }
			}
			 ?>
             
			<tr align="right" bgcolor="#EFEFEF" >
				<td colspan="2" > <b>Prod. Qnty per day</b> </td>
				<?
				$proj_summ_tot_per_day=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$summ_perDay_tot_projQty=$summary_projQtyArr[$mon_key][2];
					$summ_perDay_tot_working=$summ_tot_workingArr[$mon_key][2];
					if($summ_perDay_tot_confProjQty>0 && $summ_perDay_tot_working>0)
					{
					$proj_summ_tot_per_day+=$summ_perDay_tot_projQty/$summ_perDay_tot_working;
					}
					?>
					<td  align="right" width="80" title="Total Qty/Working Day"><? echo fn_number_format($summ_perDay_tot_projQty/$summ_perDay_tot_working,0); ?></td>
					<?
				}
				?>
                <td align="right" width=""><? echo fn_number_format($proj_summ_tot_per_day,0); ?></td>	
			</tr>
            <tr align="right" bgcolor="#EFEFEF">
				<td colspan="2"> <b>Working day:</b></td>
				<?
				$proj_summ_tot_working=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$proj_summ_tot_working+=$summ_tot_workingArr[$mon_key][2];
					?>
					<td  align="right"  width="80"><b><? echo fn_number_format($summ_tot_workingArr[$mon_key][2],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($proj_summ_tot_working,0); ?></b></td>	
			</tr>
             <tr align="right"  bgcolor="#EFEFEF">
				<td colspan="2"> <? //$company_short_library[$compKey];?><b>&nbsp;Total:</b></td>
				<?
				$summ_tot_ProjQty=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$summ_tot_ProjQty+=$summary_projQtyArr[$mon_key][2];
					?>
					<td  align="right"  width="80"><b><? echo number_format($summary_projQtyArr[$mon_key][2],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width=""><b><? echo number_format($summ_tot_ProjQty,0); ?></b></td>	
			</tr>
            </tbody>
	 </table>
    </td>  
    </tr>
    </table>
    </div>
    <?
	$tot_acl_po_Qty=0;
    foreach($companyArr as $compKey=>$comp_data)
	{
	?>
		<div style="width:<? echo $tbl_width+20; ?>px; margin:5px;margin:5px; float:left">
        <table width="<? echo $tbl_width+20; ?>"    cellpadding="0" border="1" cellspacing="0" rules="all" align="left" class="rpt_table">
        <caption style=" font-size:24px"><b style="float:left"> Company: - <? echo $company_library[$compKey]; ?></b></caption>
			<thead>
				<tr>
					<th width="30">SL No</th>
					<th width="100">Buyer</th>
                    <th width="100">Style</th>
                    <th width="100">PO No</th>
                    <th width="130">Item</th>
                    <th width="80">Shipment Date</th>
                    <th width="80">BookingQty<br>(Converted)</th>
                    <th width="80">Actual <br>order qty</th>
                    <th width="80">FOB/PC</th>
                    <th width="80">FOB Value</th>
                    <th width="80">SAM</th>
                    <th width="80">Total Minutes</th>
					<?
					foreach($mon_pub_date_arr as $mon_key=>$mon_val)
					{
						?>
						<th width="80"><? echo $mon_key; ?><br>Qty(Pcs)</th>
						<?
					}
					?>
					<th  width="80">Total Qty</th>
				</tr>
			</thead>
		<tbody>
		<?
		 
		$i=1;$tot_confirmQtyArr=array();$tot_confProjQtyArr=array();$tot_projectQtyArr=array();$tot_workingArr=array();
		foreach($comp_data as $buyer=>$buyerData)
		{
			foreach($buyerData as $poId=>$poData)
		    {
			  foreach($poData as $itemId=>$val)
		      { 
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
				if($val['customize_smv'])
				{ 
					$booking_convert=($val['smv']/$working_days_capa_arr[$compKey][date("M-y",strtotime($start_date))])*$val['actual_po_qty'];
				}
				else $booking_convert=0;
				$is_confirmed=$val['is_confirmed'];$job_no=$val['job_no'];
				$bg_color="";
				if($is_confirmed==2)
				{
					$bg_color="red"; 
				}
			?>
             <tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $i.$compKey.$buyer; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$compKey.$buyer; ?>">
               <td width="30"><? echo $i; ?></td>
               <td width="100"><p style="word-wrap:break-word"><? echo $buyer_arr[$buyer]; ?></p></td>
               <td width="100" title="<?=$job_no;?>"><p style="word-wrap:break-word"><? echo $order_inf[$poId]['style_ref_no']; ?></p></td>
               <td width="100"><p style="word-wrap:break-word"><? echo $order_inf[$poId]['po_number']; ?></p></td>
               <td width="130"><p style="word-wrap:break-word"><? echo $garments_item[$itemId]; ?></p></td>
                <td width="80"><p style="word-wrap:break-word"><? echo change_date_format($val['pub_shipment_date']); ?></p></td>
                <td width="80" align="right" title="Total Min/Customize Smv(<?=$val['customize_smv'];?>)">
					<p style="word-wrap:break-word"> <? echo fn_number_format($booking_convert,2); ?></p>
				</td>
                <td width="80" align="right"><p style="word-wrap:break-word"><? echo number_format($val['actual_po_qty'],2); ?></p></td>
                <td width="80" align="right"><p style="word-wrap:break-word"> <?  echo number_format($val['unit_price'],2); ?></p></td>
                <td width="80" align="right"><p style="word-wrap:break-word"><? echo number_format($val['po_total_price'],2); ?></p></td>
                <td width="80" align="right"><p style="word-wrap:break-word"><?  echo number_format($val['smv'],2); ?></p></td>
                <td width="80" align="right" title="smv*actual PoQty"><p style="word-wrap:break-word"><?  echo  number_format($val['smv']*$val['actual_po_qty'],2); ?></p></td>    
            <?
			 $tot_allocated_Qty=0;
            foreach($mon_pub_date_arr as $mon_key=>$mon_val)
            {
                $allocated_Qty=$sew_planQty_arr[$compKey][$buyer][$poId][$itemId][$mon_key]['qty'];
				//$customize_smv=$sew_planQty_arr[$compKey][$buyer][$poId][$itemId][$mon_key]['c_smv'];
				$tot_allocated_Qty+=$allocated_Qty;
				$tot_confirmQtyArr[$mon_key]+=$allocated_Qty;
				$tot_confProjQtyArr[$mon_key]+=$allocated_Qty;
				
				$location_id=$location_sew_planQty_arr[$compKey][$buyer][$poId][$itemId][$mon_key];
				$working_days=$working_days_capa_arr[$compKey][$mon_key];
				
				$tot_workingArr[$mon_key]+=$working_days;
				?>
                    <td  align="right" width="80" ><b style="color:<?=$bg_color;?>"><? echo number_format($allocated_Qty,0); ?></b></td>
                <?
            }
            ?>
            	<td align="right" width="80"><b style="color:<?=$bg_color;?>"><? echo number_format($tot_allocated_Qty,0); ?></b></td>
            </tr>
             
        <?
				$i++;
			 }
		   }
		}?>
			<tr align="right" bgcolor="#EFEFEF" >
				<td colspan="12" > <b>Prod. Qnty per day</b> </td>
				<?
				$tot_per_day=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$perDay_tot_confProjQty=$tot_confProjQtyArr[$mon_key];
					$perDay_tot_working=$working_days_capa_arr[$compKey][$mon_key];
					if($perDay_tot_confProjQty>0 && $perDay_tot_working>0)
					{
					$tot_per_day+=$perDay_tot_confProjQty/$perDay_tot_working;
					}
					?>
					<td  align="right" width="80" title="Total Qty/Working Day"><? echo fn_number_format($perDay_tot_confProjQty/$perDay_tot_working,0); ?></td>
					<?
				}
				?>
                <td align="right" width="80"><? echo fn_number_format($tot_per_day,0); ?></td>	
			</tr>
            <tr align="right" bgcolor="#EFEFEF">
				<td colspan="12"> <b>Working day:</b></td>
				<?
				$tot_working=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$tot_working+=$working_days_capa_arr[$compKey][$mon_key];
					?>
					<td  align="right"  width="80"><b><? echo fn_number_format($working_days_capa_arr[$compKey][$mon_key],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width="80"><b><? echo number_format($tot_working,0); ?></b></td>	
			</tr>
             <tr align="right"  bgcolor="#EFEFEF">
				<td colspan="12"> <?=$company_short_library[$compKey];?>: <b>&nbsp;Total:</b></td>
				<?
				$tot_confProjQty=0;
				foreach($mon_pub_date_arr as $mon_key=>$mon_val)
				{
					$tot_confProjQty+=$tot_confProjQtyArr[$mon_key];
					?>
					<td  align="right"  width="80"><b><? echo number_format($tot_confProjQtyArr[$mon_key],0); ?></b></td>
					<?
				}
				?>
                <td align="right" width="80"><b><? echo number_format($tot_confProjQty,0); ?></b></td>	
			</tr>
             
			<?
		 
		?>
	 </tbody>
</table>
</div>
<br>

 
<?
	} //============Company End========================
?>
</div>

	<?
	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename####$rpt_type_id";
	exit();
	
}


?>

