<?php

date_default_timezone_set("Asia/Dhaka");
require_once('mailer/class.phpmailer.php');
require_once('includes/common.php');
$company_library=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
//print_r($company_library);die;
$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$marketing_team_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
//print_r($marketing_team_library);die;
$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
//echo load_html_head_contents("A/C SubGroup Info", "", 1, 1,$unicode,'','');

$buyer_library_active=return_library_array( "select id, short_name from lib_buyer where is_deleted=0 and status_active=1", "id", "short_name"  );

 ob_start();

  $start =  date("Y-m-d");
 
  $second_month = date("Y-m-d",strtotime("+1 months"));
  $third_month = date("Y-m-d",strtotime("+2 months"));
  $forth_month = date("Y-m-d",strtotime("+3 months"));
  $month_1=str_replace("0","",date('m', strtotime($start)));
  $month_2=str_replace("0","",date('m', strtotime($second_month )));
  $month_3=str_replace("0","",date('m', strtotime($third_month )));
  $month_4=str_replace("0","",date('m', strtotime($forth_month )));
  $year_1=date('Y', strtotime($start));
  $year_2=date('Y', strtotime($second_month));
  $year_3=date('Y', strtotime($third_month));
  $year_4=date('Y', strtotime($forth_month));
  
  
  $first_month_fdate=date('Y-m-01');
  $first_month_ldate=date('Y-m-t');
  $third_date=date("Y-m-d",strtotime("+1 months"));
  $second_month_fdate=date("Y-m-01", strtotime("+1 Months", strtotime($first_month_fdate)));
  $second_month_ldate=date("Y-m-t",strtotime($second_month_fdate));
  $third_month_fdate=date("Y-m-01", strtotime("+2 Months", strtotime($first_month_fdate)));
  $third_month_ldate=date("Y-m-t",strtotime($third_month_fdate));
  $forth_month_fdate=date("Y-m-01", strtotime("+3 Months", strtotime($first_month_fdate)));
  $forth_month_ldate=date("Y-m-t",strtotime($forth_month_fdate));

	  $date_cond1=" c.country_ship_date  between '".change_date_format($first_month_fdate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($first_month_ldate,'yyyy-mm-dd',"-",1)."'";
	  $date_cond2=" c.country_ship_date  between '".change_date_format($second_month_fdate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($second_month_ldate,'yyyy-mm-dd',"-",1)."'";
	  $date_cond3=" c.country_ship_date  between '".change_date_format($third_month_fdate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($third_month_ldate,'yyyy-mm-dd',"-",1)."'";
	  $date_cond4=" c.country_ship_date  between '".change_date_format($forth_month_fdate,'yyyy-mm-dd',"-",1)."' and '".change_date_format($forth_month_ldate,'yyyy-mm-dd',"-",1)."'";
	

	  
$allcated_projected_arr=array();
foreach($company_library as $compid=>$compname) /// Daily Order Entry
{
	
  $capacity_allocation_ist=array();
  $marketing_team_arr=array();
 for($i=1;$i<5;$i++)
	{
	$mth="month_$i";
	//echo $$mth;die;
	$yr="year_$i";
    $sql=sql_select(" select e.marketing_team_id,b.buyer_id,SUM(CASE WHEN d.month_id =".$$mth." THEN (d.capacity_month_pcs)   END) AS capa$i ,SUM(CASE WHEN d.month_id =".$$mth. " THEN (d.capacity_month_pcs* b.allocation_percentage)/100   END) AS sum$i
    FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b, lib_capacity_calc_mst c,  lib_capacity_year_dtls d, lib_buyer e
	WHERE 
	a.id=b.mst_id AND 
	a.year_id=c.year AND 
	a.month_id=d.month_id  AND 
	c.id=d.mst_id AND 
	b.buyer_id=e.id and
	a.company_id=".$compid." and
	a.year_id=".$$yr." and 
	d.month_id=".$$mth." and
	a.status_active=1 and 
	a.is_deleted=0 and 
	b.status_active=1 and 
	b.is_deleted=0 and 
    e.marketing_team_id!=0 and
	c.status_active=1 and 
	c.is_deleted=0  
	GROUP BY e.marketing_team_id,b.buyer_id");
	//print_r($sql);die;
	
	foreach($sql as $row)
	{
		$marketing_team_arr[$row[csf("marketing_team_id")]]=array("marketing_team"=>$row[csf('marketing_team_id')]);//$row[csf("marketing_team_id")];
		$capacity_allocation_arr[$row[csf("marketing_team_id")]][$row[csf("buyer_id")]][$i]=$row[csf("sum$i")];
		$allcated_projected_arr[$row[csf("marketing_team_id")]][$row[csf("buyer_id")]]=$row[csf("buyer_id")];
	}
	}
	//print_r($marketing_team_arr);
	//die;
  $basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst",'comapny_id','basic_smv');
  $job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');
  $data_array=sql_select("select  a.job_no, a.company_name, a.buyer_name,a.team_leader,
   sum(CASE WHEN $date_cond1 THEN (c.order_quantity/a.total_set_qnty) END) as po_quantity_fst,  
   sum(CASE WHEN $date_cond2 THEN ( c.order_quantity/a.total_set_qnty)END) as po_quantity_2nd,
   sum(CASE WHEN $date_cond3 THEN ( c.order_quantity/a.total_set_qnty)END) as po_quantity_trd,
   sum(CASE WHEN $date_cond4 THEN ( c.order_quantity/a.total_set_qnty)END)  as po_quantity_forth
   from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and a.company_name=".$compid."   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by   a.company_name, a.team_leader,a.job_no,a.buyer_name order by  a.team_leader");


  $booked_basic_qnty_fst=array();
  $booked_basic_qnty_snd=array();
  $booked_basic_qnty_trd=array();
  $booked_basic_qnty_forth=array();
  $company_buyer_array=array();
  $booked_buyer_arr=array();
  $buyer_cond=0;
  foreach($data_array as $val)
  {
	 if($job_smv_arr[$val[csf("job_no")]] !=0)
	 {
		$booked_basic_qnty_fst=($val[csf("po_quantity_fst")]*($job_smv_arr[$val[csf("job_no")]]))/$basic_smv_arr[$val[csf("company_name")]];
		$booked_basic_qnty_snd=($val[csf("po_quantity_2nd")]*($job_smv_arr[$val[csf("job_no")]]))/$basic_smv_arr[$val[csf("company_name")]];
		$booked_basic_qnty_trd=($val[csf("po_quantity_trd")]*($job_smv_arr[$val[csf("job_no")]]))/$basic_smv_arr[$val[csf("company_name")]];
		$booked_basic_qnty_forth=($val[csf("po_quantity_forth")]*($job_smv_arr[$val[csf("job_no")]]))/$basic_smv_arr[$val[csf("company_name")]];
		$company_buyer_array[$val[csf("team_leader")]][$val[csf("buyer_name")]]['booked_basic_fst']+=$booked_basic_qnty_fst;
		$company_buyer_array[$val[csf("team_leader")]][$val[csf("buyer_name")]]['booked_basic_snd']+=$booked_basic_qnty_snd;
		$company_buyer_array[$val[csf("team_leader")]][$val[csf("buyer_name")]]['booked_basic_trd']+=$booked_basic_qnty_trd;
		$company_buyer_array[$val[csf("team_leader")]][$val[csf("buyer_name")]]['booked_basic_forth']+=$booked_basic_qnty_forth;
		
		$buyer_cond+=$val[csf("po_quantity_fst")];
		$buyer_cond+=$val[csf("po_quantity_2nd")];
		$buyer_cond+=$val[csf("po_quantity_trd")];
		$buyer_cond+=$val[csf("po_quantity_forth")];
		if($buyer_cond!=0)
		{
		$allcated_projected_arr[$val[csf("team_leader")]][$val[csf("buyer_name")]]=$val[csf("buyer_name")];
		}
		$booked_basic_qnty_tot+=$booked_basic_qnty;
	 }
        $buyer_cond=0;
  }
//print_r($allcated_projected_arr);	die;
?>

<table width="920">
    <tr>
    	<td colspan="10" height="30" valign="top" align="righr" style="font-size:24px"> Company Name: <? echo $compname; ?>		
         </td>
    </tr>
    <tr>
    	<td colspan="10" height="30" valign="top" align="righr" style="font-size:24px"> Date:
         <? 
		   if ($db_type==2){ echo change_date_format($start,'yyyy-mm-dd',"-",1); }
		   if ($db_type==0){ echo change_date_format($start); }
		
		
		?>		
         </td>
    </tr>
  </table>
  <?
  
  $grand_total_allocated_qty_fst=0;
  $grand_total_allocated_qty_snd=0;
  $$grand_total_allocated_qty_trd=0;
  $grand_total_allocated_qty_forth=0;
  $grand_total_po_qty_fst=0;
  $grand_total_po_qty_snd=0;
  $grand_total_po_qty_trd=0;
  $grand_total_po_qty_forth=0;
   ?>
   
  <table cellspacing="0" cellpadding="0" border="1"  width="1740" class="">
  <?
  foreach($marketing_team_arr  as $rdata=>$det)
  {
  
  ?>
  <br/>
  
            <tr><td colspan="14"><strong>Team Leader Name: <? echo $marketing_team_library[$det["marketing_team"]]; ?> </strong></td>  </tr>
           	<tr>
                    <td width="30" rowspan="2">SL</td>
                    <td width="150" rowspan="2">Buyer</td>
                    <td width="300" colspan="3" align="center" ><? echo date('F')."-".date('Y'); ?></td>
                    <td width="300" colspan="3" align="center"> <? echo  date('F', strtotime($second_month ))." - ".date('Y', strtotime($second_month ));  ?></td>
                    <td width="300" colspan="3" align="center"><? echo date('F', strtotime($third_month ))." - ".date('Y', strtotime($third_month ));?></td>
                    <td width="300" colspan="3" align="center"><? echo date('F', strtotime($forth_month ))." - ".date('Y', strtotime($forth_month )); ?></td>
            </tr>
            <tr>
                     <td width="100" >Allocated (Basic Qty)</td>
                     <td width="100" >Order Recv. (Basic Qty)</td>
                     <td width="100" >Balance (Basic Qty)</td>
                     <td width="100" >Allocated (Basic Qty)</td>
                     <td width="100" >Order Recv. (Basic Qty)</td>
                     <td width="100" >Balance (Basic Qty)</td>
                     <td width="100" >Allocated (Basic Qty)</td>
                     <td width="100" >Order Recv. (Basic Qty)</td>
                     <td width="100" >Balance (Basic Qty)</td>
                     <td width="100" >Allocated (Basic Qty)</td>
                     <td width="100" >Order Recv. (Basic Qty)</td>
                     <td width="100" >Balance (Basic Qty)</td>
            </tr>
        
           <?
			   $i=1;
			   $total_allocated_qty_fst=0;
			   $total_allocated_qty_snd=0;
			   $total_allocated_qty_trd=0;
			   $total_allocated_qty_forth=0;
			   $total_po_qty_fst=0;
			   $total_po_qty_snd=0;
			   $total_po_qty_trd=0;
			   $total_po_qty_forth=0;
		  // print_r($capacity_allocation_arr);die;
		     foreach($allcated_projected_arr[$det["marketing_team"]] as $key=>$val)
			 {
				// if(in_array( $key, $buyer_library_active ))
				 if( $buyer_library_active[$key]!='' )
				 {
					$total_allocated_qty_fst+=$capacity_allocation_arr[$det["marketing_team"]][$key][1];
					$total_allocated_qty_snd+=$capacity_allocation_arr[$det["marketing_team"]][$key][2];
					$total_allocated_qty_trd+=$capacity_allocation_arr[$det["marketing_team"]][$key][3];
					$total_allocated_qty_forth+=$capacity_allocation_arr[$det["marketing_team"]][$key][4];
					$total_po_qty_fst+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_fst'];
					$total_po_qty_snd+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_snd'];
					$total_po_qty_trd+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_trd'];
					$total_po_qty_forth+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_forth'];
					
					$grand_total_allocated_qty_fst+=$capacity_allocation_arr[$det["marketing_team"]][$key][1];
					$grand_total_allocated_qty_snd+=$capacity_allocation_arr[$det["marketing_team"]][$key][2];
					$grand_total_allocated_qty_trd+=$capacity_allocation_arr[$det["marketing_team"]][$key][3];
					$grand_total_allocated_qty_forth+=$capacity_allocation_arr[$det["marketing_team"]][$key][4];
					$grand_total_po_qty_fst+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_fst'];
					$grand_total_po_qty_snd+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_snd'];
					$grand_total_po_qty_trd+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_trd'];
					$grand_total_po_qty_forth+=$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_forth'];
					
					
					
					?>
					<tr>
						<td width="30"> <? echo $i; ?></td>
						<td width="150" > <p><? echo $buyer_library[$key]; ?></p></td>
						<td width="100" align="right"><? echo number_format($capacity_allocation_arr[$det["marketing_team"]][$key][1],0); ?></td>
						<td width="100" align="right"><? echo number_format($company_buyer_array[$det["marketing_team"]][$key]['booked_basic_fst'],0); ?></td>
						<td width="100" align="right"><? echo number_format(($capacity_allocation_arr[$det["marketing_team"]][$key][1]-$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_fst']),0); ?> </td>
						<td width="100" align="right"><? echo number_format($capacity_allocation_arr[$det["marketing_team"]][$key][2],0); ?></td>
						<td width="100" align="right"><? echo number_format($company_buyer_array[$det["marketing_team"]][$key]['booked_basic_snd'],0); ?></td>
						<td width="100" align="right"><? echo number_format(($capacity_allocation_arr[$det["marketing_team"]][$key][2]-$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_snd']),0); ?></td>
						<td width="100" align="right"><? echo number_format($capacity_allocation_arr[$det["marketing_team"]][$key][3],0); ?></td>
						<td width="100" align="right"><? echo number_format($company_buyer_array[$det["marketing_team"]][$key]['booked_basic_trd'],0); ?></td>
						<td width="100" align="right"><? echo number_format(($capacity_allocation_arr[$det["marketing_team"]][$key][3]-$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_trd']),0); ?></td>
						<td width="100" align="right"><? echo number_format($capacity_allocation_arr[$det["marketing_team"]][$key][4],0); ?></td>
						<td width="100" align="right"><? echo number_format($company_buyer_array[$det["marketing_team"]][$key]['booked_basic_forth'],0); ?></td>
						<td width="100" align="right"> <? echo number_format(($capacity_allocation_arr[$det["marketing_team"]][$key][4]-$company_buyer_array[$det["marketing_team"]][$key]['booked_basic_forth']),0); ?></td>
						
					 </tr>
					<? 
					
				// }
				 $i++;
				 }
  }
 
			 
			 
		     ?>
             <tr bgcolor="#EAEAEA">
                    <td width="180"  colspan="2" align="right">Total</td>
                    <td width="100" align="right"><? echo number_format($total_allocated_qty_fst,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_fst,0); ?></td>
                    <td width="100" align="right"><? echo number_format(($total_allocated_qty_fst-$total_po_qty_fst),0); ?> </td>
                    <td width="100" align="right"><? echo number_format($total_allocated_qty_snd,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_snd,0); ?></td>
                    <td width="100" align="right"><? echo number_format(($total_allocated_qty_snd-$total_po_qty_snd),0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_allocated_qty_trd,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_trd,0); ?></td>
                    <td width="100" align="right"><? echo number_format(($total_allocated_qty_trd-$total_po_qty_trd),0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_allocated_qty_forth,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_forth,0); ?></td>
                    <td width="100" align="right"> <? echo number_format(($total_allocated_qty_forth-$total_po_qty_forth),0); ?></td>
           </tr> 
                <tr bgcolor="#EAEAEA">
                    <td width="180"  colspan="2" align="right">Percentage( %)</td>
                    <td width="100" align="right"><? //echo number_format($total_allocated_qty_fst,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_fst/$total_allocated_qty_fst*100,0)."%"; ?></td>
                    <td width="100" align="right"><? echo number_format((($total_allocated_qty_fst-$total_po_qty_fst)/$total_allocated_qty_fst*100),0)."%"; ?> </td>
                    <td width="100" align="right"><? //echo number_format($total_allocated_qty_snd,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_snd/$total_allocated_qty_snd*100,0)."%"; ?></td>
                    <td width="100" align="right"><? echo number_format(($total_allocated_qty_snd-$total_po_qty_snd)/$total_allocated_qty_snd*100,0)."%"; ?></td>
                    <td width="100" align="right"><? // echo number_format($total_allocated_qty_trd,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_trd/$total_allocated_qty_trd*100,0)."%"; ?></td>
                    <td width="100" align="right"><? echo number_format(($total_allocated_qty_trd-$total_po_qty_trd)/$total_allocated_qty_trd*100,0)."%"; ?></td>
                    <td width="100" align="right"><? // echo number_format($total_allocated_qty_forth,0); ?></td>
                    <td width="100" align="right"><? echo number_format($total_po_qty_forth/$total_allocated_qty_forth*100,0)."%"; ?></td>
                    <td width="100" align="right"> <? echo number_format(($total_allocated_qty_forth-$total_po_qty_forth)/$total_allocated_qty_forth*100,0)."%"; ?></td>
           </tr>  
            <?
			
      }
	  
  if($grand_total_allocated_qty_fst!="" || $grand_total_allocated_qty_fst!=0 )
          {
           ?>
              <tr >
                    <td width="180"  colspan="2" align="right">Grand Total</td>
                    <td width="100" align="right"><? echo number_format($grand_total_allocated_qty_fst,0); ?></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_fst,0); ?></td>
                    <td width="100" align="right"><? echo number_format(($grand_total_allocated_qty_fst-$grand_total_po_qty_fst),0); ?> </td>
                    <td width="100" align="right"><? echo number_format($grand_total_allocated_qty_snd,0); ?></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_snd,0); ?></td>
                    <td width="100" align="right"><? echo number_format(($grand_total_allocated_qty_snd-$grand_total_po_qty_snd),0); ?></td>
                    <td width="100" align="right"><? echo number_format($grand_total_allocated_qty_trd,0); ?></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_trd,0); ?></td>
                    <td width="100" align="right"><? echo number_format(($grand_total_allocated_qty_trd-$grand_total_po_qty_trd),0); ?></td>
                    <td width="100" align="right"><? echo number_format($grand_total_allocated_qty_forth,0); ?></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_forth,0); ?></td>
                    <td width="100" align="right"> <? echo number_format(($grand_total_allocated_qty_forth-$grand_total_po_qty_forth),0); ?></td>
              </tr> 
                <tr >
                    <td width="180"  colspan="2" align="right">Percentage</td>
                    <td width="100" align="right"></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_fst/$grand_total_allocated_qty_fst*100,0)."%"; ?></td>
                    <td width="100" align="right"><? echo number_format(($grand_total_allocated_qty_fst-$grand_total_po_qty_fst)/$grand_total_allocated_qty_fst*100,0)."%"; ?> </td>
                    <td width="100" align="right"></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_snd/$grand_total_allocated_qty_snd*100,0)."%"; ?></td>
                    <td width="100" align="right"><? echo number_format(($grand_total_allocated_qty_snd-$grand_total_po_qty_snd)/$grand_total_allocated_qty_snd*100,0)."%"; ?></td>
                    <td width="100" align="right"></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_trd/$grand_total_allocated_qty_trd*100,0)."%"; ?></td>
                    <td width="100" align="right"><? echo number_format(($grand_total_allocated_qty_trd-$grand_total_po_qty_trd)/$grand_total_allocated_qty_trd*100,0)."%"; ?></td>
                    <td width="100" align="right"></td>
                    <td width="100" align="right"><? echo number_format($grand_total_po_qty_forth/$grand_total_allocated_qty_forth*100,0)."%"; ?></td>
                    <td width="100" align="right"> <? echo number_format(($grand_total_allocated_qty_forth-$grand_total_po_qty_forth)/$grand_total_allocated_qty_forth*100,0)."%"; ?></td>
              </tr> 
	         <?
			 }
			 ?>
     </table>      
         <?

 	
		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=4 and b.mail_user_setup_id=c.id and a.company_id=$compid";
		//echo $sql2;die;
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		//echo $to;die;
		$subject="Order Position by Team (Pcs) Details";
 		//$from_mail="";
    	$message="";
    	$message=ob_get_contents();
    	//ob_clean();
		$header=mail_header();
		echo send_mail_mailer( $to, $subject, $message, $from_mail );
	
}
	