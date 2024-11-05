<? 
include('../../../includes/common.php'); 
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');

if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=5 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 100, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );	
	exit();
} 

if ($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 
if($action=="job_no_popup") 
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	$year_condition="";
	if($cbo_year_selection>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(a.insert_date)='$cbo_year_selection'";
		}
		else
		{
			$year_condition=" and to_char(a.insert_date,'YYYY')='$cbo_year_selection'";
		}
	}
    
	//echo $cbo_year_selection."sfsdf";
	
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
	if($cbo_within_group==0) $within_group=""; else $within_group=" and a.within_group='$cbo_within_group'";
	
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
    
   // $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
	
      $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.buyer_style_ref from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' and  a.entry_form=278 $buyer_cond $within_group $year_field_cond $year_condition  group by a.party_id, a.subcon_job, a.job_no_prefix_num, a.insert_date, b.order_no, b.buyer_style_ref order by a.job_no_prefix_num desc";
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
		 foreach($data_array as $row)
		 {
			 if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $party_arr[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('buyer_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? $i++; 
			} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
	exit();
}


if($action=="rate_popup")
{
    echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
    
    //echo $color_id."__".$order_id."__".$mst_id."__".$dtls_id; die;
    
    $color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");

	$rate_sql="SELECT a.currency_id, a.exchange_rate, b.gmts_item_id, b.gmts_color_id, c.description, c.process, c.embellishment_type, c.rate, c.prod_sequence_no
	From subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.id=b.mst_id and b.id=c.mst_id and b.id in($dtls_id) and b.gmts_color_id in ($color_id) and a.entry_form=278 and c.process>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.currency_id, a.exchange_rate, b.gmts_item_id, b.gmts_color_id, c.description, c.process, c.embellishment_type, c.rate, c.prod_sequence_no order by c.prod_sequence_no";// a.order_id in($order_id)



   //echo $rate_sql; die;
    $job_dtls_sql= sql_select($rate_sql);

    if($job_dtls_sql[0][csf('process')]==1) $process_type=$wash_wet_process;
	else if($job_dtls_sql[0][csf('process')]==2) $process_type=$wash_dry_process;
	else if($job_dtls_sql[0][csf('process')]==3) $process_type=$wash_laser_desing;
	else $process_type=$blank_array;

    ?>
    <fieldset style="width:600px">
        <div style="width:100%;" align="center">

        	<table width="580" cellspacing="0" align="right" border="0">
		        
		        <tr>
		            <td width="180"><strong>Gmts. Item:</strong></td> <td width="195px"><? echo $garments_item[$job_dtls_sql[0][csf('gmts_item_id')]]; ?></td>
		            <td width="180"><strong>Currency:</strong></td><td width="195px"><? echo $currency[$job_dtls_sql[0][csf('currency_id')]]; ?></td>
		            
		        </tr>
		        <tr>
		            <td width="180"><strong>Color:</strong></td> <td width="195px"><? echo $color_library_arr[$job_dtls_sql[0][csf('gmts_color_id')]]; ?></td>
		            <td width="180"><strong>E. Rate:</strong></td><td width="195px"><? echo $job_dtls_sql[0][csf('exchange_rate')]; ?></td>
		           
		        </tr>
		        
		        <tr style=" height:20px">
						<td colspan="6">&nbsp;</td>
			    </tr>
		    </table>
         <br>
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="6">Rate Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Wash Description</th>
                        <th width="80">Prod. Sequence no</th>
                        <th width="100">Process</th>
                        <th width="100">Wash Type</th>
                        <th width="80">Rate</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                
                $i=0;
                
                $tot_rate = 0;
                foreach( $job_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td align="center" width="100"><? echo $row[csf("description")];?> </td>
                        <td align="center" width="80"><? echo $row[csf('prod_sequence_no')]; ?>&nbsp; </td> 
                        <td align="center" width="100"><p><? echo $wash_type[$row[csf("process")]]; ?>&nbsp;</p></td>
                        <td align="center" width="100"><? echo $process_type[$row[csf("embellishment_type")]]; ?>&nbsp;</td>
                        <td align="right" width="80"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                        
                    </tr>
                    <? 
                    $tot_rate+=$row[csf("rate")];
                    
                } ?>
                <tr class="tbl_bottom">
                    <td width="30">&nbsp; </td>
                    <td width="100">&nbsp; </td>
                    <td width="80">&nbsp; </td>
                    <td width="100">&nbsp; </td>
                    <td width="100" colspan="">Total: &nbsp;</td>
                    <td width="80" align="right"><p><? echo $tot_rate; ?> &nbsp; </p></td>
                    
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}





if($action=="report_generate")
{ 
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$job_no=str_replace("'","",$txt_job_no);
	$cbo_buyer_id=str_replace("'","",$cbo_party_name);
	$company_id=str_replace("'","",$cbo_company_id);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$team_leader=str_replace("'","",$cbo_team_leader);
	$team_member=str_replace("'","",$cbo_team_member);
	$aop_ref=str_replace("'","",$txt_aop_ref);
	$order_no=str_replace("'","",$txt_order_no);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$cbo_year =str_replace("'","",$cbo_year_selection);
	
	

	//echo $cbo_year; die;
	
	if($db_type==0)
	{
		if($cbo_year!=0) $year_cond="and YEAR(a.insert_date)=$cbo_year";  else $year_field_cond="";
	}
	else if($db_type==2)
	{
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_field_cond="";
	}
	
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	
	/*if($db_type==0) $select_from_date=change_date_format($from_date,'yyyy-mm-dd');
	if($db_type==2) $select_from_date=change_date_format($from_date,'','',1);
	if($db_type==0) $select_to_date=change_date_format($to_date,'yyyy-mm-dd');
	if($db_type==2) $select_to_date=change_date_format($to_date,'','',1);*/
	if($team_leader==0) $team_leader_con=""; else $team_leader_con=" and a.team_leader='$team_leader'";
	
	if($team_member==0) $team_member_con=""; else $team_member_con=" and a.team_member='$team_member'";
	
	if($cbo_buyer_id==0) $party_con=""; else $party_con=" and a.party_id='$cbo_buyer_id'";
	if($gmts_type==0) $gmts_type_con=""; else $gmts_type_con=" and a.gmts_type='$gmts_type'";
	//if($cbo_within_group==0) $within_group=""; else $within_group=" and a.within_group='$cbo_within_group'";
	if($cbo_within_group!=0) $within_group=" and a.within_group='$cbo_within_group'"; else $within_group="";
	if($order_no!="") $order_con=" and a.order_no like '%$order_no'"; else $order_con="";
	
	if($aop_ref!="") $aop_ref_con=" and a.aop_reference like '%$aop_ref'"; else $aop_ref_con="";
	
	if($cbo_within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
    
	if(str_replace("'","",$company_id)==0)$company_name=""; else $company_name=" and a.company_id=$company_id";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	
	$color_library_arr=return_library_array( "select id,color_name from lib_color  where status_active=1 and is_deleted=0", "id", "color_name");
	
	$team_leader_name_arr=return_library_array( "select id,team_leader_name from lib_marketing_team  where status_active=1 and is_deleted=0 ", "id", "team_leader_name");

	if ($cbo_date_category==1) 
	{
		if($db_type==0)
		{
			if( $from_date==0 && $to_date==0 ) $date_con=""; else $date_con= " and a.receive_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
	 	}
		if($db_type==2)
		{
			if( $from_date==0 && $to_date==0 ) $date_con=""; else $date_con= " and a.receive_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}
	else if($cbo_date_category==2) 
	{
		if($db_type==0)
		{
			if( $from_date==0 && $to_date==0 ) $date_con=""; else $date_con= " and a.delivery_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
	 	}
		if($db_type==2)
		{
			if( $from_date==0 && $to_date==0 ) $date_con=""; else $date_con= " and a.delivery_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}
	else 
	{
		if($db_type==0)
		{
			if( $from_date==0 && $to_date==0 ) $date_con=""; else $date_con= " and a.insert_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";	
	 	}
		if($db_type==2)
		{
			if( $from_date==0 && $to_date==0 ) $date_con=""; else $date_con= " and a.insert_date  between '".change_date_format($from_date,'','',1)."' and '".change_date_format($to_date,'','',1)."'";
		}
	}
		
		
		
	$job_sql="SELECT a.id, a.entry_form, a.subcon_job, a.job_no_prefix_num, a.company_id, a.within_group, a.party_id, a.currency_id, a.exchange_rate, a.receive_date, a.gmts_type, a.order_id, a.order_no, b.id as dtls_id, b.buyer_style_ref, b.booking_dtls_id, b.gmts_item_id, b.gmts_color_id, b.order_quantity, b.order_uom, b.rate, b.amount, b.delivery_date, b.wastage, b.remarks,b.body_part,b.construction, b.composition, b.gsm as gsm_weight, b.grey_dia, b.gmts_color_id, b.item_color_id, b.fin_dia as dia_width, b.aop_color_id, b.print_type,a.team_leader
	From subcon_ord_mst a, subcon_ord_dtls b
	where a.id=b.mst_id and a.entry_form=278 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_con $team_leader_con $team_member_con $aop_ref_con $within_group $party_con $company_name $job_no_cond $gmts_type_con $order_con order by a.id desc";

	//echo $job_sql; die;
	
	$job_sql_result=sql_select($job_sql);


	/*echo "<pre>";
	print_r($job_sql_result); */
	$report_data_march=array(); 
	$rec_date_arr=array();
	foreach ($job_sql_result as  $row)
	 {
		$report_data_march[$row[csf('id')]]+=1;
		$rec_date_arr[$row[csf('receive_date')]]=$row[csf('receive_date')];
	}
	/*select con_date, conversion_rate from currency_conversion_rate where currency=2 and company_id=1 and con_date<='02-Mar-20' order by con_date desc*/

	$rate_sql="select company_id, con_date, conversion_rate from currency_conversion_rate where company_id='$company_id' and status_active=1 and con_date<='$from_date'  and is_deleted=0 order by con_date";
	//echo $rate_sql; die;
	$rate_sql_result=sql_select($rate_sql);

	


	$rate_arr=array();
	foreach ($rate_sql_result as  $row) 
	{

		$dateKey= date('Y-m-d', strtotime($row[csf('con_date')]));

		$original_rate_arr[$dateKey]=$row[csf('conversion_rate')];
		$custom_rate_arr[$dateKey]=$row[csf('conversion_rate')];		

		//$rate_arr[$row[csf('company_id')]]["con_rate"]=$row[csf('conversion_rate')]; //[$row[csf('con_date')]]
	}



foreach($original_rate_arr as $date=>$rate)
{
	for($i=1;$i<365;$i++){
		$incrementDate= date('Y-m-d', strtotime($date. ' + '. $i. 'days'));
		if($custom_rate_arr[$incrementDate]==''){
			$custom_rate_arr[$incrementDate]=$rate;
		}
		else{break;}			
	}//for loof	
}

/*echo "<pre>";
print_r($custom_rate_arr);die;*/



	ob_start();
	
	//$job_sql_result[$row["within_group"]];
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$convention_rate=return_library_array( "select company_id, conversion_rate from currency_conversion_rate", "company_id", "conversion_rate"  );
	?>
    <style type="text/css">
		.wrd_brk{word-break: break-all;}
	</style>
     <!--<div style="width:2620px"> -->
     <fieldset style="width:2380px;">
     <div style="width:2380px; margin:0 auto;">
     
         <!--<table width="100%" cellspacing="0" >-->
          <table cellpadding="0" cellspacing="0" width="2380">
         		<tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="25" style="font-size:20px"><strong><? echo 'AOP Job Created Summary Report'; ?></strong></td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td colspan="25" align="center" style="border:none; font-size:14px;">
                        <b><? echo $company_library[$company_id]; ?></b>
                    </td>
                </tr>
                <tr  class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="25" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
         	<!--<table width="2600" cellspacing="0" border="1" class="rpt_table" rules="all">-->
            <table width="2380" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                        <th width="35">SL</th>
                        <th width="120">Job No</th>
                        <th width="80">Within Group</th>
                        <th width="100">Order Rcv.Date</th>
                        <th width="100">Party WO No</th>
                        <th width="120">Party Name</th>
                        <th width="100">Buyer Style Ref.</th>
                        <th width="100">Body Part</th>
                        
                        <th width="100">Construction</th>
                        <th width="160">Composition</th>
                        <th width="100">GSM/Dia</th>
                        <th width="100">Gmts Color</th>
                        <th width="100">Item Color</th>
                        <th width="100">Fin. Dia</th>
                        
                        <th width="100">AOP Color</th>
                        <th width="80">Order UOM</th>
                        <th width="80">Order Qty</th>
                        <th width="80">Order Rate ($)</th>
                        <th width="80">Order Amount ($)</th>
                        <th width="80">Print Type</th>
                        <th width="80">Delv. Date</th>
                        <th width="80">P.Loss %</th>
                        <th width="90">Image</th>
                        <th width="100">Team Leader</th>
                        <th >Remarks</th>
                </thead>
            </table>
             <div style="max-height:300px; overflow-y:scroll; width:2380px" id="scroll_body">
             <table width="2380" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="table_body">
				<?  
					$i=1;
					$m=1;

					$total_amount=0; $job_ids=""; $job_ids2="";
					foreach($job_sql_result as $row)
					{
						$order_id=$row[csf("order_id")];
						$dtls_id=$row[csf("dtls_id")];
						$mst_id=$row[csf("id")];
						$color_id=$row[csf('gmts_color_id')];
						
						$receive_date= date('Y-m-d', strtotime($row[csf('receive_date')]));
						$conv_rate=$custom_rate_arr[$receive_date];

						//echo $conv_rate; die;

						if ($row[csf('currency_id')]==1) {
                        	$rate=$row[csf('rate')]/$conv_rate;
                        	$amount=$row[csf('amount')]/$conv_rate;
                        }else{
                        	$rate=$row[csf('rate')];
                        	$amount=$row[csf('amount')];
                        }

						?>
                          <tr valign="middle" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            
                            <?php
                            
                            if ($job_ids!=$mst_id) 
							{
                            	$job_ids=$mst_id;
								
								
                            
                            ?>
                            <td width="35" rowspan="<?php echo $report_data_march[$row[csf("id")]];?>"><?php echo $m; ?></td>
                            <td width="120" rowspan="<?php echo $report_data_march[$row[csf("id")]];?>" align="center"><?php echo $row[csf("subcon_job")]; ?></td>
                            <td width="80" align="center" rowspan="<?php echo $report_data_march[$row[csf("id")]];?>"><?php echo $yes_no[$row[csf('within_group')]]; ?></td>
                            <td width="100" align="center" rowspan="<?php echo $report_data_march[$row[csf("id")]];?>"><?php echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100" align="center" rowspan="<?php echo $report_data_march[$row[csf("id")]];?>"><?php echo $row[csf('order_no')]; ?></td>
                            <td width="120" align="center" rowspan="<?php echo $report_data_march[$row[csf("id")]];?>"><?php echo $party_arr[$row[csf("party_id")]]; ?></td>
                            <?
                            $m++;
                            }
                            ?>
                            <td width="100" align="center"><?php echo $row[csf("buyer_style_ref")]; ?></td>
                            <td width="100" align="center"><?php echo $body_part[$row[csf("body_part")]]; ?></td>
                            <td width="100" align="center"><?php echo $row[csf("construction")]; ?></td>
                            <td width="160" align="center"><?php echo $row[csf("composition")]; ?></td>
                            <td width="100" align="center"><?php echo $row[csf("gsm_weight")]."/".$row[csf("dia_width")]; ?></td>
                            <td width="100" align="center"><?php echo $color_library_arr[$row[csf("gmts_color_id")]]; ?></td> 
                            <td width="100" align="center"><?php echo $color_library_arr[$row[csf("item_color_id")]]; ?></td> 
                            <td width="100" align="center"><?php echo $row[csf("dia_width")]; ?></td>
                            <td width="100" align="center"><?php echo $color_library_arr[$row[csf("aop_color_id")]]; ?></td> 
                            <td width="80" align="center"><?php echo  $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                            <td width="80" align="right" ><?php echo $row[csf('order_quantity')]; ?></td>

                            <td width="80" align="right" title="<?php echo $conv_rate;?>"><?php echo number_format($rate,4); ?></td>
                            
                            <td width="80" align="right" title="<?php echo $conv_rate;?>"><?php $total_amount+=$row[csf('amount')]; echo number_format($amount,4); ?></td>
                            <td width="80" align="right"><?php echo $print_type[$row[csf('print_type')]];  ?></td>
                            
                            <td width="80" align="center"><?php echo change_date_format($row[csf('delivery_date')]); ?></td>
                            <td width="80" align="right"><?php echo $row[csf('wastage')]; ?></td>
                            <td width="90">
                            <input type="button" class="image_uploader" style="width:90px" value="Image" onClick="image_popup('<?php echo $mst_id.'__1' ?>')"  tabindex="">
                        	</td>
                            <?
                            if($job_ids2!=$mst_id) 
							{
                            	$job_ids2=$mst_id;
                            
                            ?>
                               <td width="100" align="center" rowspan="<?php echo $report_data_march[$row[csf("id")]];?>"><?php echo $team_leader_name_arr[$row[csf('team_leader')]]; ?></td>
							 <? 
							 }
                            ?>
                        	<td><input type="button" class="formbuttonplasminus" value="RMK" onClick="image_popup('<?php echo $dtls_id.'__2' ?>')" /> </td>
						  </tr>
                       
						<?		
					$i++;
					}
                  ?>
                </table>
         	
         <table width="2380" border="1" cellpadding="0" cellspacing="0" rules="all"> 
			<tr class="tbl_bottom">
                <td width="35" >&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="120" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="160">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80" >&nbsp;</td>
                <td width="80" >&nbsp;</td>
                <td width="80" >Grand Total: </td>
                <td width="80" ><?php echo $total_amount; ?></td>
                <td width="80" >&nbsp;</td>
                <td width="80" >&nbsp;</td>
                <td width="80" >&nbsp;</td>
                <td width="90" >&nbsp;</td>
                <td width="100" >&nbsp;</td>
                <td>&nbsp;</td>
			</tr>
		</table> 
		</div>
     </div>
     </fieldset>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}


if ($action == "view_image_dtls") 
{
    
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
    extract($_REQUEST);
    $data=explode("__", $data);
    //$user_arr   = return_library_array("select id, user_name from user_passwd", "id", "user_name");

    if($data[1]==2)
    {
    	$query = "SELECT  id, remarks FROM  subcon_ord_dtls where id='$data[0]' and status_active=1 and is_deleted=0";

	  // echo $query; die;

	    $mst_query = sql_select($query); 
    }
    else{
    	$mst_id=str_replace("'", "", $data[0]);
    	$query="select id,master_tble_id,details_tble_id,form_name,image_location,file_type,real_file_name from common_photo_library where master_tble_id='$mst_id'";
    	//echo $query;
    	 $mst_query = sql_select($query);
    }
    
   
  
    ?>
    </head>
    <body>
        <div align="left" style="width:100%;" >
            <?php
                foreach ($mst_query as $row) {
                     
                    if ($data[1]==1) {
                         
                        ?>
                        <img width="50" height="65" title="<?php echo $row[csf("real_file_name")]; ?>" style="float: left; padding-left: 5px;" src="../../../<?php echo $row[csf("image_location")]; ?>" />
                        
                        <?php
                    }else{
                        ?>
                          <textarea class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:370px; height:250px"  ><? echo $row[csf("remarks")]; ?></textarea>
                        
                        <?php
                    }
                }
            ?>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?php
    exit();
}



?>