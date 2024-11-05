<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$company_array=return_library_array( "select id, company_name from lib_company",'id','company_name');

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 100, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/aop_production_status_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/aop_production_status_report_controller', document.getElementById('cbo_company_id').value+'_'+this.value+'_'+0, 'load_drop_down_machine', 'machine_td' );" );	
}

if ($action=="load_drop_down_buyer_buyer")
{
    $data=explode("_",$data);
    if($data[1]==1)
    {
        echo create_drop_down( "txt_buyer_buyer_no", 125, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select Buyer --", "", "");
    }
    else
    {
       echo '<input name="txt_buyer_buyer_no" id="txt_buyer_buyer_no" class="text_boxes" style="width:115px"  placeholder="Write">';
    }   
    exit();  
} 

if ($action=="load_drop_down_buyer")
{
    $data=explode("_",$data);

    if($data[1]==1)
    {
        echo create_drop_down( "cbo_party_name", 125, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "$load_function");
    }
    else
    {
        echo create_drop_down( "cbo_party_name", 125, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
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
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num,  b.order_no, b.cust_style_ref from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.entry_form=278  and a.company_id='$company_id' $buyer_cond group by a.party_id, a.subcon_job, a.job_no_prefix_num,  b.order_no, b.cust_style_ref  order by a.subcon_job ASC";	
	
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
				<td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
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
if($action=="order_desc_popup")
{
	echo load_html_head_contents("Order Details", "../../../", 1, 1,$unicode,'','');
	//echo $order_id;//die;
	$expData=explode('_',$order_id);
	?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                	<tr>
                    	<th colspan="11">Order Qty Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="120">Order No</th>
                        <th width="300">Item Description </th>
                        <th width="120">Color</th>
                        <th width="80">Receive Date</th>
                        <th width="50">Rate</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');	
				$size_arr=return_library_array( "select id,size_name from  lib_size",'id','size_name');
				$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql = "SELECT a.id,a.job_no_prefix_num, a.subcon_job, a.party_id, b.order_no, b.order_quantity,b.amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,b.construction, b.composition, 
                b.gsm, b.grey_dia, b.gmts_color_id, 
                b.item_color_id, b.fin_dia, b.aop_color_id,a.receive_date,b.rate
                from subcon_ord_mst a, subcon_ord_dtls b
                where a.subcon_job=b.job_no_mst and b.id=$expData[0]   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278   order by  a.job_no_prefix_num, b.order_no";
               // echo $sql;
                $order_dtls_sql= sql_select($sql);
                foreach( $order_dtls_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30"><? echo $i; ?></td>
                        <td width="120"><? echo $row[csf("order_no")];?> </td>
                        <td width="300"><? echo $row[csf('composition')].', '.$row[csf("gsm")].', '.$row[csf("grey_dia")].', '.$row[csf("fin_dia")]; ?> </td> 
                        <td align="center" width="120"><p><? echo  $color_arr[$row[csf("gmts_color_id")]]; ?></p></td>
                        <td align="center" width="80"><? echo change_date_format($row[csf("receive_date")]); ?></td>
                        <td align="right" width="50"><? echo $row[csf("rate")]; ?> &nbsp; </td>
                        <td align="right"><? echo number_format($row[csf("order_quantity")]); ?> &nbsp;</td>
                    </tr>
                    <? 
                    $tot_qty+=$row[csf("order_quantity")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: &nbsp;</td>
                    <td align="right"><p><? echo number_format($tot_qty); ?> &nbsp; </p></td>
                </tr>
            </table>
        </div> 
	</fieldset>
 </div> 
	<?
	exit();
}
if($action=="order_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$buyer = str_replace("'","",$buyer_name);
	$job_no = str_replace("'","",$job_no);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	//echo $buyer;die;
	
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	 $sql="select distinct a.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b where b.job_no_mst=a.subcon_job and a.id=b.mst_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond and a.entry_form=278  and a.is_deleted =0 group by a.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
	//===============================================================start=====================================================================================
	$job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
	$txt_buyer_style=str_replace("'","",$txt_buyer_style);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$txt_reference_no=str_replace("'","",$txt_reference_no);
	$txt_buyer_style=str_replace("'","",$txt_buyer_style);
	$cbo_location_name=str_replace("'","",$cbo_location_name);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
	$txt_buyer_buyer_no=str_replace("'","",$txt_buyer_buyer_no);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	
	if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
	if($cbo_party_name!=0) $buyer_id_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
	if($cbo_location_name!=0) $location_id_cond=" and a.location_id='$cbo_location_name'"; else $location_id_cond="";
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	if ($txt_reference_no!='') $reference_no_cond=" and a.aop_reference like '%$txt_reference_no%'"; else $reference_no_cond="";
	if ($txt_buyer_style!='') $buyer_style_cond=" and b.buyer_style_ref like '%$txt_buyer_style%'"; else $buyer_style_cond="";
	if ($txt_buyer_po!='') $buyer_po_cond=" and b.buyer_po_no like '%$txt_buyer_po%'"; else $buyer_po_cond="";
	if ($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
	if ($cbo_process==0) $process_id_cond=""; else $process_id_cond=" and b.main_process_id=$cbo_process_id";
	if($cbo_within_group==1)
	{
		if($txt_buyer_buyer_no!=0) $buyer_buyer_cond=" and b.buyer_buyer='$txt_buyer_buyer_no'"; else $buyer_buyer_cond="";
	} 
	else
	{
		if ($txt_buyer_buyer_no!='') $buyer_buyer_cond=" and b.buyer_buyer like '%$txt_buyer_buyer_no%'"; else $buyer_buyer_cond="";
	}
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$buyer_arr=return_library_array( "SELECT id, buyer_name from lib_buyer where status_active =1 and is_deleted=0",'id','buyer_name');
	//==============================================================end========================================================================================
	
	
	
	//===============================================================start=====================================================================================
		$job_rece_issue_sql="select b.job_dtls_id,
		sum(case when a.trans_type=1 and a.entry_form=279 and a.subcon_date<='".$date_from."' then b.quantity else 0 end) as total_rec_pre_quantity,
		sum(case when a.trans_type=1 and a.entry_form=279 and a.subcon_date<'".$date_from."' then b.quantity else 0 end) as rec_pre_quantity,
		sum(case when a.trans_type=1 and a.entry_form=279 and a.subcon_date='".$date_from."' then b.quantity else 0 end) as rec_today_quantity,
		sum(case when a.trans_type=2 and a.entry_form=280 and a.subcon_date<='".$date_from."' then b.quantity else 0 end) as total_issue_pre_quantity,
		sum(case when a.trans_type=2 and a.entry_form=280 and a.subcon_date<'".$date_from."' then b.quantity else 0 end) as issue_pre_quantity,
		sum(case when a.trans_type=2 and a.entry_form=280 and a.subcon_date='".$date_from."' then b.quantity else 0 end) as issue_today_quantity
		from sub_material_mst a, sub_material_dtls b where 
		a.id=b.mst_id and a.trans_type in (1,2) and a.entry_form in (279,280) 
		and a.status_active=1 and b.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 
		group by b.job_dtls_id";
		$rece_issue_sql_result=sql_select($job_rece_issue_sql);
		$inventory_rece_issue_array=array();
		foreach ($rece_issue_sql_result as $row)
		{
			$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['rec_pre_quantity']=$row[csf('rec_pre_quantity')];
			$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['rec_today_quantity']=$row[csf('rec_today_quantity')];
			$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['issue_pre_quantity']=$row[csf('issue_pre_quantity')];
			$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['issue_today_quantity']=$row[csf('issue_today_quantity')];
			$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['total_rec_pre_quantity']=$row[csf('total_rec_pre_quantity')];
			$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['total_issue_pre_quantity']=$row[csf('total_issue_pre_quantity')];
		}
		unset($rece_issue_sql_result);
		
		$inventory_ret_array=array();
		$inv_ret_sql="select b.order_id, sum(b.quantity) as quantity from  sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.order_id";
		
		$inv_ret_sql_result=sql_select($inv_ret_sql);
		foreach ($inv_ret_sql_result as $row)
		{
			$inventory_ret_array[$row[csf('order_id')]]=$row[csf('quantity')];
		}
	
	//==============================================================end========================================================================================
	    	  		             
	//===============================================================start=====================================================================================
		$buyer_po_arr=array();
		$po_sql ="Select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
			
		}
		unset($po_sql_res);
	
	//==============================================================end========================================================================================
	
	//===============================================================start=====================================================================================
		$job_sql = "SELECT a.id,a.subcon_job,a.within_group, a.subcon_job, a.party_id, b.order_no, 
		b.order_quantity,b.amount,b.order_uom ,b.buyer_po_id,b.buyer_style_ref,a.delivery_date,
		b.id as job_dtls_id,b.buyer_buyer,a.aop_reference
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 
		and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278  
		$order_no_cond $style_ref_cond $job_no_cond $location_id_cond  $within_group_cond $buyer_id_cond $reference_no_cond $buyer_style_cond $buyer_po_cond $buyer_buyer_cond $machine_flooR_cond
		$date_cond order by a.subcon_job";		
			//echo $job_sql;
		$job_sql_result=sql_select($job_sql);
	
	//==============================================================end========================================================================================
	ob_start();
	$tbl_width=2400;
	$col_span=34;
	
    ?>
    
    <style>
	table th td{word-break: break-all;}
	
	</style>
    
    <div>
    
    
    
     
      <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" align="left">
         <tr style="border:none;">
                <td colspan="34" align="center" style="border:none; font-size:14px;">
                 <? echo "AOP Production Status Report"; ?>                                
             </td>
            </tr>
            <tr style="border:none;">
                <td colspan="34" align="center" style="border:none;font-size:12px; font-weight:bold">
                 <? echo change_date_format($date_from);?>
                </td>
            </tr>
     </table> 
     
      <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" align="left">
         <tr style="border:none;">
                <td colspan="34" align="center" style="border:none; font-size:14px;">&nbsp;   </td>
            </tr>
            <tr style="border:none;">
                <td colspan="34" align="center" style="border:none;font-size:12px; font-weight:bold">&nbsp; </td>
            </tr>
     </table> 
      <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" align="left">
         <tr>
            <td>
             <table width="600" border="1" cellpadding="0" cellspacing="0" rules="all" align="left" class="rpt_table">
                    <thead>
                        <th></th>	
                        <th>Month</th>
                        <th>In-House</th>
                        <th>Sample</th>
                        <th>Subcontract</th>
                        <th>Total Rcv</th>
                        <th>Total Issue</th>
                    </thead>
                    <tbody>
                    <?php
                    $formDate=$date_from;
                    $toDate=$date_to;
                    $noOfMonth = date('n',strtotime($toDate))- date('n',strtotime($formDate));
                   // echo $noOfMonth; die;
                    $i=0;
					?>
                    <?
                    for($i; $i <= $noOfMonth; $i++)
                    {
                        $month=date('F', strtotime("+$i months", strtotime($formDate)));
                        ?>
                        <tr>
                        <? if($i==0)
						  { ?>
                         	<td align="center"  rowspan="<? echo $noOfMonth+1; ?>">Summery</td>
                           <? }?>
                            <td align="center"><?php echo $month; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                        </tr>
                        <?php
						
                    }
					
                    ?>
                </tbody>
            </table>
            </td>
            <td>
             <table width="600" border="1" cellpadding="0" cellspacing="0" rules="all" align="left" class="rpt_table">
                    <thead>
                        <th>Buyer </th>	
                        <th>In-House</th>
                        <th>Sample</th>
                        <th>Total Rcv</th>
                        <th>Total Issue</th>
                    </thead>
                    <tbody>
                    <?php
                    $formDate=$date_from;
                    $toDate=$date_to;
                    $noOfMonth = date('n',strtotime($toDate))- date('n',strtotime($formDate));
                    //echo $noOfMonth; die;
                    $i=0;
                    for($i; $i <= $noOfMonth; $i++)
                    {
                        $month=date('F', strtotime("+$i months", strtotime($formDate)));
                        ?>
                        
                        <tr>
                            <td align="right"><?php echo "Gentle Park"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                            <td align="right"><?php echo "00"; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            </td>
         </tr>  
     </table> 
     
     <table width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" align="left">
         <tr style="border:none;">
                <td colspan="34" align="center" style="border:none; font-size:14px;">&nbsp;   </td>
            </tr>
            <tr style="border:none;">
                <td colspan="34" align="center" style="border:none;font-size:12px; font-weight:bold">&nbsp; </td>
            </tr>
     </table> 
     
        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" rules="all" align="left" class="rpt_table">
           <thead>
            <tr>
                <td colspan="34" align="left" style="font-size:18px;font-weight:bold;" bgcolor="#CCCCCC">Inhouse Bulk</td>
         	</tr>
            <tr>
               <th width="30">SL</th>
               <th width="100">Transaction Date</th>
               <th width="100">Company / Suppiler</th>
               <th width="100">Job NO</th>
               <th width="100">Buyer</th> 
               <th width="100">AOP Ref. No</th>
               <th width="100">AOP Order No / WO No</th>                            
               <th width="100">Style No</th>
               <th width="100">currency</th>
               <th width="100">AOP Color</th>
               <th width="60">Contraction</th>
               <th width="90">GSM</th>
               <th width="60">Dia</th>
               <th width="120">Order Qty</th>
               <th width="120">M / C Type</th>
               <th width="120" >Pre Receive</th>
               <th width="120">Today Receive</th>
               <th width="120">Total Receive</th>
               <th width="120">Receive %</th>
               <th width="120">Pre Issue</th>
               <th width="80">Today Issue</th>
               <th width="80">Total Issue</th>
               <th width="80">Issue %</th>
               <th width="80">Balance</th>
               </tr>
               <tr>
               </tr>
           </thead>
        </table>
        <div style="max-height:400px; overflow-y:scroll; width:<? echo $tbl_width+20; ?>px" id="scroll_body">
            <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" align="left" id="table_body">
            <tbody>
            <?
                $process_array=array();
                $i=1;
                foreach ($job_sql_result as $row)
                {
					
					//===============================================================start====================================
					
					  if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
						//------------------------------------------------------------------
						 $rec_qty=$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['total_rec_pre_quantity']-$inventory_ret_array[$row[csf('id')]];
						 $issue_qty=$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['total_issue_pre_quantity'];
						 $rec_pre_quantity=$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['rec_pre_quantity'];
						 $rec_today_quantity=$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['rec_today_quantity'];
						 $issue_pre_quantity=$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['issue_pre_quantity'];
						 $issue_today_quantity=$inventory_rece_issue_array[$row[csf('job_dtls_id')]]['issue_today_quantity'];
						//------------------------------------------------------------------
						$prod_balance=$total_prod_pre_quantity-$qc_total_prod_pre_quantity;
						$delv_balance=$total_prod_pre_quantity-$total_delv_quantity;
						 					 
						//------------------------------------------------------------------
						 if($row[csf('within_group')]==1) 
						 {
							$partyarr=$company_array;
						 }
						 else
						 {
							$partyarr=$party_arr;
						 }
					 //==============================================================end=======================================
					 
					 
                 ?>
                 <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">	
                    <td width="30" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
                    <td width="100" align="center"><p><? echo $row[csf('subcon_job')]; ?></p></td>
                    <td width="100"><p><? echo $partyarr[$row[csf('party_id')]]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('order_no')]; ?></p></td>
                    <td width="100"><?  echo $row[csf('aop_reference')]; ?></td>  
                    <td width="100"><? if($cbo_within_group==1){echo $buyer_arr[$row[csf('buyer_buyer')]];}else{echo $row[csf('buyer_buyer')];} ?></td>  
                    <td width="100"><? echo $buyer_po_arr[$row[csf('buyer_po_id')]]['po'] ; ?></td>                              
                    <td width="100"><p><? echo $buyer_po_arr[$row[csf('buyer_po_id')]]['style'] ?></p></td>
                    <td width="100" align="right"><p> <a href="##" onclick="show_progress_report_details('order_desc_popup','<? echo $row[csf("job_dtls_id")]; ?>','850px')"> <? echo number_format($row[csf('order_quantity')],2); ?> </a></p></td>
                    <td width="100" align="right"><p><? echo number_format($row[csf('amount')],2); ?></p></td>
                   <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
                    <td width="90"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                    <td width="60" align="center"><? $daysOnHand = datediff("d",date("Y-m-d"),$row[csf('delivery_date')]); echo $daysOnHand; ?> </td>
                    <td width="120" align="right"><p><? echo number_format($rec_pre_quantity,2); ?></p></td>
                    <td width="120" align="right"><p><? echo number_format($rec_today_quantity,2); ?></p></td>
                    <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_popup','<? echo $row[csf("job_dtls_id")]; ?>','850px')"><? echo number_format($rec_qty,2); ?></a></p></td>
                    <td width="120" align="right"><p><? echo number_format($issue_pre_quantity,2); ?></p></td>
                    <td width="120" align="right"><p><? echo number_format($issue_today_quantity,2); ?></p></td>
                    <td width="120" align="right"><p><a href="##" onclick="show_progress_report_details('material_desc_iss_popup','<? echo $row[csf("job_dtls_id")]; ?>','850px')"><? echo number_format($issue_qty,2); ?></a></p></td>
                    <td width="120" align="right"><?
                        $mat_blnce_qty=$rec_qty-$issue_qty;
                        echo number_format($mat_blnce_qty,2);
                        ?></td>
                    <td width="80" align="right"><p><? echo number_format($prod_pre_quantity,2); ?></p></td>
                    <td width="80" align="right"><p><? echo number_format($prod_today_quantity,2); ?></p></td>
                    <td width="80" align="right"><p><a href="##" onclick="show_progress_report_details('product_qty_pop_up','<? echo $row[csf("job_dtls_id")];?>','850px')"><? echo number_format($total_prod_pre_quantity,2); ?></a></p></td>
                    <td width="80" align="right"><p><? echo number_format($qc_prod_pre_quantity,2); ?></p></td>
                    
                </tr>
                <?
                $i++;
               
			////////////////////////////////////////////////////////////////
                $tot_total_order_qty+=$row[csf('order_quantity')];
                $tot_total_order_val+=$row[csf('amount')];
                $tot_total_rec_qty+=$rec_today_quantity;;
                $tot_total_issue_qty+=$issue_today_quantity;
				$tot_total_material_blce+=$mat_blnce_qty;
				$tot_total_prod_qty+=$prod_today_quantity;
				$tot_total_qc_qty+=$qc_prod_today_quantity;
				$tot_delv_today_quantity+=$delv_today_quantity;
				$tot_delv_balance+=$delv_balance;
				$total_reject_quantity+=$total_reject_qnty_quantity;
			////////////////////////////////////////////////////////////////////		
                }
                ?>
                </tbody>
                </table>
                <table width="<? echo $tbl_width; ?>" border="1" class="rpt_table" rules="all" align="left" id="table_footer">
                <tr class="tbl_bottom">
                	<td width="30"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>
                    <td width="100"></td>  
                    <td width="100"></td>  
                    <td width="100"></td>                              
                    <td width="100">Grand Total:</td>
                    <td width="100" align="right" id="value_tot_total_order_qty"><? echo number_format($tot_total_order_qty); ?></td>
                    <td width="100" align="right" id="value_tot_total_order_val"><? echo number_format($tot_total_order_val); ?></td>
                    <td width="60" align="center"></td>
                    <td width="90"></td>
                    <td width="60" align="center"></td>
                    <td width="120" align="right"></td>
                    <td width="120" align="right" id="value_tot_total_rec_qty"><? echo number_format($tot_total_rec_qty); ?></td>
                    <td width="120" align="right"></td>
                    <td width="120" align="right"></td>
                    <td width="120" align="right" id="value_tot_total_issue_qty"><? echo number_format($tot_total_issue_qty); ?></td>
                    <td width="120" align="right"></td>
                    <td width="120" align="right" id="value_tot_total_material_blce"><? echo number_format($tot_total_material_blce) ?></td>
                    <td width="80" align="right"></td>
                    <td width="80" align="right" id="value_tot_total_prod_qty"><? echo number_format($tot_total_prod_qty); ?></td>
                    <td width="80" align="right"></td>
                    <td width="80" align="right"></td>
            </tr>
        </table>        
        </div>
    </div>

    <?
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
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

?>