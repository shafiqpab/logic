<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



$company_arr=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 



//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
    <script>
	var selected_id = new Array;
	var selected_name = new Array;
    function check_all_data()
	{
	var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
	tbl_row_count = tbl_row_count - 0;
	for( var i = 1; i <= tbl_row_count; i++ ) 
	{
		var onclickString = $('#tr_' + i).attr('onclick');
		var paramArr = onclickString.split("'");
		var functionParam = paramArr[1];
		js_set_value( functionParam );
	}
	}
		
	function toggle( x, origColor )
	{
		var newColor = 'yellow';
		if ( x.style ) 
		{ 
		x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
		
		function js_set_value( strCon ) 
		{
		var splitSTR = strCon.split("_");
		var str = splitSTR[0];
		var selectID = splitSTR[1];
		var selectDESC = splitSTR[2];
		toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
		if( jQuery.inArray( selectID, selected_id ) == -1 )
		{
		selected_id.push( selectID );
		selected_name.push( selectDESC );					
		}
		else
	    {
		for( var i = 0; i < selected_id.length; i++ )
		 {
		 if( selected_id[i] == selectID ) break;
		 }
		 selected_id.splice( i, 1 );
		 selected_name.splice( i, 1 ); 
		}
		var id = ''; var name = ''; var job = '';
		for( var i = 0; i < selected_id.length; i++ )
		 {
		 id += selected_id[i] + ',';
		 name += selected_name[i] + ','; 
		 }
		id 		= id.substr( 0, id.length - 1 );
		name 	= name.substr( 0, name.length - 1 ); 
		$('#txt_selected_id').val( id );
		$('#txt_selected').val( name ); 
		}
    </script>
<?
	extract($_REQUEST);
	//echo $job_no;die;
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	if(str_replace("'","",$job_id)!="")  $job_cond="and a.id in(".str_replace("'","",$job_id).")";
    else  if (str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and a.job_no_mst='".$job_no."'";
	
	if(str_replace("'","",$style_id)!="")  $style_cond="and b.id in(".str_replace("'","",$style_id).")";
    else  if (str_replace("'","",$style_no)=="") $style_cond=""; else $style_cond="and b.style_ref_no='".$style_no."'";
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name $buyer_name order by job_no";
	//echo $sql;die;
	echo create_list_view("list_view", "Order Number,Job No, Style Ref","150,100,250","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_no,style_ref_no", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$txt_order_no=str_replace("'","",trim($txt_order_no));
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		
		if($cbo_company_name==0) $company_con=""; else $company_con=" and a.company_id=$cbo_company_name";
		if($cbo_buyer_name==0) $buyer_con=""; else $buyer_con=" and b.buyer_id=$cbo_buyer_name";
		if(trim($txt_order_no)=="") $order_con=""; else $order_con="and d.po_number like('%$txt_order_no%')";
		
	
		if($txt_date_from!='' && $txt_date_to!=''){
			if($db_type==0){
				
				$from_date=change_date_format($txt_date_from);
				$to_date=change_date_format($txt_date_to);
			}
			else
			{
				$from_date=change_date_format($txt_date_from,'','',-1);
				$to_date=change_date_format($txt_date_to,'','',-1);
			}
			$date_con=" and d.pub_shipment_date BETWEEN '$from_date' and '$to_date'";
		}
		else
		{
			$date_con="";	
		}
		
	
	$sql = "
		select 
			a.company_id,
			a.rate_for,
			b.order_id,
			b.style_ref,
			b.item_id,
			b.uom,
			b.wo_qty as approve_qty,
			b.avg_rate as approve_rate,
			d.job_no_mst,
			d.po_quantity,
			d.po_number
		from 
			piece_rate_wo_mst a,
			piece_rate_wo_dtls b,
			wo_po_break_down d 
		where 
			a.id=b.mst_id 
			and b.order_id=d.id
			$company_con
			$buyer_con
			$order_con
			$date_con
			and a.status_active=1 
			and a.is_deleted=0
			and b.status_active=1 
			and b.is_deleted=0
			and d.status_active=1 
			and d.is_deleted=0
		order by a.id
			";
		 
	$result = sql_select($sql);
	 foreach($result as $rows){
		$key=$rows[csf('company_id')].$rows[csf('order_id')].$rows[csf('item_id')];
		
		if($rows[csf('rate_for')]==20){
			$cut_approve_qty_arr[$key]+=$rows[csf('approve_qty')];
			$cut_approve_rate_arr[$key]=$rows[csf('approve_rate')];
		}
		
		if($rows[csf('rate_for')]==30){
			$sew_approve_qty_arr[$key]+=$rows[csf('approve_qty')];
			$sew_approve_rate_arr[$key]=$rows[csf('approve_rate')];
		}
		
		if($rows[csf('rate_for')]==35){
			$iron_approve_qty_arr[$key]+=$rows[csf('approve_qty')];
			$iron_approve_rate_arr[$key]=$rows[csf('approve_rate')];
		}
		
		$company_arr[$key]=$rows[csf('company_id')];
		$order_qty_arr[$key]=$rows[csf('po_quantity')];
		$job_arr[$key]=$rows[csf('job_no_mst')];
		$order_id_arr[$key]=$rows[csf('order_id')];
		$order_no_arr[$key]=$rows[csf('po_number')];
		$style_arr[$key]=$rows[csf('style_ref')];
		$item_arr[$key]=$rows[csf('item_id')];
	 }

		
	$implode_po = implode(",",$order_id_arr);	
		
		
		
		
		
$sql="
	select
		a.bill_for,
		a.company_id,
		a.location,
		a.final_bill,
		b.emp_id,
		b.emp_name,
		b.designation,
		b.buyer_id,
		b.grade,
		b.salary,
		b.rate_variables,
		b.style_ref,
		b.cutting_bill_qty,
		b.wo_rate,
		b.amount,

		c.order_id,
		c.gmt_item_id,
		c.approved_wo_qty ,
		c.previous_bill_qty,
		c.bill_qty,
		c.bill_safty_qty
	from 
		pro_weekly_wages_bill_mst a,
		pro_weekly_wages_bill_dtls b,
		pro_weekly_wages_order_brk c
	where
		a.id=b.mst_id
		and b.id=c.weekly_wages_dtls_id
		and a.status_active=1
		and a.is_deleted=0
		and b.status_active=1
		and b.is_deleted=0
		and c.status_active=1
		and c.is_deleted=0
		and c.order_id in($implode_po)
		";	
	
$bill_result = sql_select($sql);
	 foreach($bill_result as $rows){
		$ik=$rows[csf('company_id')].$rows[csf('order_id')].$rows[csf('gmt_item_id')];
		if($rows[csf('bill_for')]==20){
			$cut_bill_qty_arr[$ik]+=$rows[csf('bill_qty')]; 
			$cut_bill_amount_arr[$ik]+=$rows[csf('bill_qty')]*$rows[csf('wo_rate')];
			$cut_bill_rate_arr[$ik]=$rows[csf('wo_rate')];
		}
		
		if($rows[csf('bill_for')]==30){
			$sew_bill_qty_arr[$ik]+=$rows[csf('bill_qty')];
			$sew_bill_amount_arr[$ik]+=$rows[csf('bill_qty')]*$rows[csf('wo_rate')];
			$sew_bill_rate_arr[$ik]=$rows[csf('wo_rate')];
		}
		
		if($rows[csf('bill_for')]==35){
			$iron_bill_qty_arr[$ik]+=$rows[csf('bill_qty')];
			$iron_bill_amount_arr[$ik]+=$rows[csf('bill_qty')]*$rows[csf('wo_rate')];
			$iron_bill_rate_arr[$ik]=$rows[csf('wo_rate')];
		}
	 }
		
$iron_sql="select production_type,production_quantity,po_break_down_id,serving_company,location,item_number_id from  pro_garments_production_mst where production_type in(1,5,7) and po_break_down_id in($implode_po) and status_active=1 and is_deleted=0";
$production_result = sql_select($iron_sql);
foreach($production_result as $row){
	$key=$row[csf('serving_company')].$row[csf('po_break_down_id')].$row[csf('item_number_id')];
	
	if($row[csf('production_type')]==1){
	$cut_pro_qty_arr[$key]+=$row[csf('production_quantity')];
	}
	if($row[csf('production_type')]==5){
	$sew_pro_qty_arr[$key]+=$row[csf('production_quantity')];
	}
	if($row[csf('production_type')]==7){
	$iron_pro_qty_arr[$key]+=$row[csf('production_quantity')];
	}
	
}
		
		
		
		
		ob_start();
		?>
    <div style="width:2850px;">
       	<fieldset style="width:2890px;"> 
       	<legend>Report Panel</legend>  
            	<table width="2850"  cellspacing="0">
                	<tr height="20">
                        <td colspan="27" align="center" >
                            <font size="3">
                                <strong>Company Name:<?php echo $company_arr[$cbo_company_name]; ?></strong>
                            </font>
                        </td>
                    </tr>
                    <tr height="20">
                        <td colspan="27" align="center">
                        	<font size="3"><strong>Address</strong></font>
                        </td>
                    </tr>
                </table>

                <table class="rpt_table" border="1" rules="all" width="2850" align="left">
                    <thead>
                        <tr height="20">
                            <th colspan="6" align="center"><strong>Order Details</strong></th>
                            <th colspan="7" align="center"><strong>Cutting</strong></th>
                            <th colspan="7" align="center"><strong>Sewing</strong></th>
                            <th colspan="7" align="center"><strong>Finishing</strong></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr align="center" height="20">
                            <th width="40" >SL</th>
                            <th width="110">Job Number</th>
                            <th width="100">Order No</th>
                            <th>Style Name</th>
                            <th width="130">Item Name</th>
                            <th width="100">PO Qnty.(Pcs.)</th>
                            
                            <th width="100">Approve Qnty.(Dzn.)</th>
                            <th width="100">Cutting  Approv Rate (TK)</th>
                            <th width="100">Bill Qnty.(Dzn.)</th>
                            <th width="100">Remaining Qnty.(Dzn.)</th>
                            <th width="100">Total Bill Amount(Tk)</th>
                            <th width="100">Cutting Production (Pcs.)</th>
                            <th width="100">Excess input Qnty.(Pcs.)</th>
                            
                            <th width="100">Approve Qnty.(Dzn.)</th>
                            <th width="100">Sewing Approv Rate (TK) </th>
                            <th width="100">Bill Qnty.(Dzn.)</th>
                            <th width="100">Remaining Qnty.(Dzn.)</th>
                            <th width="100">Total Bill Amount(Tk)</th>
                            <th width="100">Sewing Production (Pcs.)</th>
                            <th width="100">Excess sewing Output Qnty.(Pcs.)</th>
                            
                            <th width="100">Approve Qnty.(Dzn.)</th>
                            <th width="100">Finishing Approv Rate (Tk) </th>
                            <th width="100">Bill Qnty.(Dzn.)</th>

                            <th width="100">Remaining Qnty.(Dzn.)</th>
                            <th width="100">Total Bill Amount(Tk)</th>
                            <th width="100">Finishing Production (Pcs.)</th>
                            <th width="100">Excess Finishing Output Qnty.(Pcs.)</th>
                            
                            <th width="100">Grand Total (cut+ sew+ finis) in TK</th >
                        </tr>    
                    </thead>
                </table>
            <div style="width:2870px; max-height:300px; float:left; overflow-y:scroll;" id="scroll_body">
            	<table width="2850"  cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body" align="left">
                	<?
                    $i=1;
				   foreach($order_id_arr as $key_id=>$val){
					   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ; 
				   
				    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<td width="40" align="center"><?  echo $i ; ?></td>
                        <td width="110" ><? echo $job_arr[$key_id];?></td>
                        <td width="100"><p><? echo $order_no_arr[$key_id];?></p></td>
                        <td><p><? echo $style_arr[$key_id];?></p></td>
                        <td width="130"><p><? echo $garments_item[$item_arr[$key_id]];?></p></td>
                        <td width="100" align="right"><? echo round($order_qty_arr[$key_id]);?></td>
                        <td width="100" align="right"><? echo round($cut_approve_qty_arr[$key_id]);?></td>
                        <td width="100" align="right"><? echo $cut_approve_rate_arr[$key_id];?></td>
                        <td align="right" width="100"><? echo round($cut_bill_qty_arr[$key_id]);?></td>
                        <td align="right" width="100"><? echo round($cut_approve_qty_arr[$key_id]-$cut_bill_qty_arr[$key_id]);?></td>
                        <td align="right" width="100">
							<?
								echo "<a href='#report_details' style='color:#990000' onclick= \"openmypage_bill_info('$company_arr[$key_id]','$order_id_arr[$key_id]','".$item_arr[$key_id]."','cutting_bill_qty');\">".round($cut_bill_amount_arr[$key_id])."</a>";
							?>
                        </td>
                        <td align="right" width="100"><? echo $cut_pro_qty_arr[$key_id];?></td>
                        <td  align="right" width="100"><? echo ($order_qty_arr[$key_id]-$cut_pro_qty_arr[$key_id]); ?></td>
                        
                        
                        
                        
                        <td align="right" width="100"><? echo round($sew_approve_qty_arr[$key_id]);?></td>
                        <td align="right" width="100"><? echo $sew_approve_rate_arr[$key_id];?></td>
                        <td align="right" width="100"><? echo round($sew_bill_qty_arr[$key_id]);?></td>
                        <td align="right" width="100"><? echo round($sew_approve_qty_arr[$key_id]-$sew_bill_qty_arr[$key_id]);?></td>
                        <td  align="right" width="100">
							<? 
								echo "<a href='#report_details' style='color:#990000' onclick= \"openmypage_bill_info('$company_arr[$key_id]','$order_id_arr[$key_id]','".$item_arr[$key_id]."','sewing_bill_qty');\">".round($sew_bill_amount_arr[$key_id])."</a>";
								
							?>
                        </td>
                        <td align="right" width="100"><? echo $sew_pro_qty_arr[$key_id];?></td>
                        <td  align="right" width="100"><? echo ($order_qty_arr[$key_id]-$sew_pro_qty_arr[$key_id]); ?></td>
                        
                        
                        
                        <td width="100" align="right"><? echo round($iron_approve_qty_arr[$key_id]);?></td>
                        <td width="100" align="right"><? echo $iron_approve_rate_arr[$key_id];?></td>
                        <td align="right" width="100"><? echo round($iron_bill_qty_arr[$key_id]);?></td>
                        <td align="right" width="100"><? echo round($iron_approve_qty_arr[$key_id]-$iron_bill_qty_arr[$key_id]);?></td>
                        <td  align="right" width="100">
							<?
								echo "<a href='#report_details' style='color:#990000' onclick= \"openmypage_bill_info('$company_arr[$key_id]','$order_id_arr[$key_id]','".$item_arr[$key_id]."','iron_bill_qty');\">".round($iron_bill_amount_arr[$key_id])."</a>";
							?>
                        </td>
                        <td align="right" width="100"><? echo $iron_pro_qty_arr[$key_id];?></td>
                        <td  align="right" width="100"><? echo ($order_qty_arr[$key_id]-$iron_pro_qty_arr[$key_id]); ?></td>
                        
                        
                        
                        <td  align="right" width="100">
							<?
								$grand_total=$cut_bill_amount_arr[$key_id] + $sew_bill_amount_arr[$key_id] + $iron_bill_amount_arr[$key_id]; echo $grand_total;
							?>
                        </td >
                    </tr>
					<?
                    $i++;
                    } // end  foreach($order_id_arr as $key_id=>$val)
                    ?>
                </table>
            </div>
                
                <table width="2850" border="1" class="rpt_table" rules="all" align="left">
                    <tfoot>
                        <th colspan="5" align="right"><b>Total:</b></th>
                        <th width="100" align="right" id="total_po_qnty"></th>
                        
                        <th width="100" align="right" id="total_cut_appv_qty"></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right" id="total_cut_bill_qty"></th>
                        <th width="100" align="right" id="total_remain_qty_cut"></th>
                        <th width="100" align="right" id="total_cut_bill_amnt"></th>
                        <th width="100" align="right" id="total_cut_qnty"></th>
                        <th width="100" align="right" id="total_excess_cut_qty"></th>
                        
                        <th width="100" align="right" id="total_sew_appv_qty"></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right" id="total_sew_bill_qty"></th>
                        <th width="100" align="right" id="total_remain_qty_sew"></th>
                        <th width="100" align="right" id="total_sew_bill_amnt"></th>
                        <th width="100" align="right" id="total_sew_qnty"></th>
                        <th width="100" align="right" id="total_excess_sewingout_qty"></th>
                        
                        
                        <th width="100" align="right" id="total_iron_appv_qty"></th>
                        <th width="100">&nbsp;</th>
                        <th width="100" align="right" id="total_finish_bill_qty"></th>
                        <th width="100" align="right" id="total_remain_qty_iron"></th>
                        <th width="100" align="right" id="total_finish_bill_amnt"></th>
                        <th width="100" align="right" id="total_iron_input_qnty"></th>
                        <th width="100" align="right" id="total_excess_iron_input_qty"></th>
                        
                        <th width="100" align="right" id="total_grand_total">&nbsp;</th>
               		</tfoot>
                </table>
        </fieldset>
		
 </div>       

<?
	foreach (glob("$user_id*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id.'_'.time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name."####"."1";
	
exit();
}

if ($action=='sewing_short_report'){

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		$txt_order_no=str_replace("'","",trim($txt_order_no));
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		
		if($cbo_company_name==0) $company_con=""; else $company_con=" and a.company_id=$cbo_company_name";
		if($cbo_buyer_name==0) $buyer_con=""; else $buyer_con=" and b.buyer_id=$cbo_buyer_name";
		if(trim($txt_order_no)=="") $order_con=""; else $order_con="and d.po_number like('%$txt_order_no%')";
		
	
		if($txt_date_from!='' && $txt_date_to!=''){
			if($db_type==0){
				
				$from_date=change_date_format($txt_date_from);
				$to_date=change_date_format($txt_date_to);
			}
			else
			{
				$from_date=change_date_format($txt_date_from,'','',-1);
				$to_date=change_date_format($txt_date_to,'','',-1);
			}
			$date_con=" and d.pub_shipment_date BETWEEN '$from_date' and '$to_date'";
		}
		else
		{
			$date_con="";	
		}
		
	
	$sql = "
		select 
			a.company_id,
			a.rate_for,
			b.order_id,
			b.style_ref,
			b.item_id,
			b.uom,
			b.wo_qty as approve_qty,
			b.buyer_id,
			b.avg_rate as approve_rate,
			d.job_no_mst,
			d.po_quantity,
			d.po_number
		from 
			piece_rate_wo_mst a,
			piece_rate_wo_dtls b,
			wo_po_break_down d
		where 
			a.id=b.mst_id 
			and b.order_id=d.id
			$company_con
			$buyer_con
			$order_con
			$date_con
			and a.status_active=1 
			and a.is_deleted=0
			and b.status_active=1 
			and b.is_deleted=0
			and d.status_active=1 
			and d.is_deleted=0
		order by a.id
			";
		 
	$result = sql_select($sql);
	 foreach($result as $rows){
		$key=$rows[csf('company_id')].$rows[csf('order_id')].$rows[csf('item_id')];
		
		if($rows[csf('rate_for')]==20){
			$cut_approve_qty_arr[$key]+=$rows[csf('approve_qty')];
			$cut_approve_rate_arr[$key]=$rows[csf('approve_rate')];
		}
		
		if($rows[csf('rate_for')]==30){
			$sew_approve_qty_arr[$key]+=$rows[csf('approve_qty')];
			$sew_approve_rate_arr[$key]=$rows[csf('approve_rate')];
		}
		
		if($rows[csf('rate_for')]==35){
			$iron_approve_qty_arr[$key]+=$rows[csf('approve_qty')];
			$iron_approve_rate_arr[$key]=$rows[csf('approve_rate')];
		}
		
		$company_arr[$key]=$rows[csf('company_id')];
		$order_qty_arr[$key]=$rows[csf('po_quantity')];
		$job_arr[$key]=$rows[csf('job_no_mst')];
		$buyer_arr[$key]=$rows[csf('buyer_id')];
		$order_id_arr[$key]=$rows[csf('order_id')];
		$order_no_arr[$key]=$rows[csf('po_number')];
		$style_arr[$key]=$rows[csf('style_ref')];
		$item_arr[$key]=$rows[csf('item_id')];
	 }

		
	$implode_po = implode(",",$order_id_arr);	
		
		
		
		
		
$sql="
	select
		a.bill_for,
		a.company_id,
		a.location,
		a.final_bill,
		b.emp_id,
		b.emp_name,
		b.designation,
		b.buyer_id,
		b.grade,
		b.salary,
		b.buyer_id,
		b.rate_variables,
		b.style_ref,
		b.cutting_bill_qty,
		b.wo_rate,
		b.amount,

		c.order_id,
		c.gmt_item_id,
		c.approved_wo_qty ,
		c.previous_bill_qty,
		c.bill_qty,
		c.bill_safty_qty
	from 
		pro_weekly_wages_bill_mst a,
		pro_weekly_wages_bill_dtls b,
		pro_weekly_wages_order_brk c
	where
		a.id=b.mst_id
		and b.id=c.weekly_wages_dtls_id
		and a.status_active=1
		and a.is_deleted=0
		and b.status_active=1
		and b.is_deleted=0
		and c.status_active=1
		and c.is_deleted=0
		and c.order_id in($implode_po)
		";	
	
$bill_result = sql_select($sql);
	 foreach($bill_result as $rows){
		$ik=$rows[csf('company_id')].$rows[csf('order_id')].$rows[csf('gmt_item_id')];
		if($rows[csf('bill_for')]==20){
			$cut_bill_qty_arr[$ik]+=$rows[csf('bill_qty')]; 
			$cut_bill_amount_arr[$ik]+=$rows[csf('bill_qty')]*$rows[csf('wo_rate')];
			$cut_bill_rate_arr[$ik]=$rows[csf('wo_rate')];
		}
		
		if($rows[csf('bill_for')]==30){
			$sew_bill_qty_arr[$ik]+=$rows[csf('bill_qty')];
			$sew_bill_amount_arr[$ik]+=$rows[csf('bill_qty')]*$rows[csf('wo_rate')];
			$sew_bill_rate_arr[$ik]=$rows[csf('wo_rate')];
		}
		
		if($rows[csf('bill_for')]==35){
			$iron_bill_qty_arr[$ik]+=$rows[csf('bill_qty')];
			$iron_bill_amount_arr[$ik]+=$rows[csf('bill_qty')]*$rows[csf('wo_rate')];
			$iron_bill_rate_arr[$ik]=$rows[csf('wo_rate')];
		}
	 }
		
$iron_sql="select production_type,production_quantity,po_break_down_id,serving_company,location,item_number_id from  pro_garments_production_mst where production_type in(1,5,7) and po_break_down_id in($implode_po) and status_active=1 and is_deleted=0";
$production_result = sql_select($iron_sql);
foreach($production_result as $row){
	$key=$row[csf('serving_company')].$row[csf('po_break_down_id')].$row[csf('item_number_id')];
	
	if($row[csf('production_type')]==1){
	$cut_pro_qty_arr[$key]+=$row[csf('production_quantity')];
	}
	if($row[csf('production_type')]==5){
	$sew_pro_qty_arr[$key]+=$row[csf('production_quantity')];
	}
	if($row[csf('production_type')]==7){
	$iron_pro_qty_arr[$key]+=$row[csf('production_quantity')];
	}
	
}
		
		
		
		
		ob_start();
		?>
<div style="width:1150px;">
  <fieldset style="width:1150px;"> 
    <legend>Report Panel</legend>  
            <table width="100%"  cellspacing="0">
                <tr height="20">
                    <td colspan="11" align="center" >
                        <font size="3">
                            <strong>Company Name:<?php echo $company_arr[$cbo_company_name]; ?></strong>
                        </font>
                    </td>
                </tr>
                <tr height="20">
                    <td colspan="11" align="center">
                        <font size="3"><strong>Address</strong></font>
                    </td>
                </tr>
            </table>

            <table class="rpt_table" border="1" rules="all" width="1150" align="left">
                <thead>
                    <tr>
                        <th colspan="11" align="center"><strong>  Order Wise Sewing Bill Wages Statement </strong></th>
                    </tr>
                    <tr align="center" height="20">
                        <th width="40" >SL</th>
                        <th>Buyer</th>
                        <th width="110">Job Number</th>
                        <th width="100">Order No</th>
                        <th width="130">Style Name</th>
                        <th width="100">PO Qnty.(Pcs.)</th>
                        <th width="100">Approve Qnty.(Dzn.)</th>
                        <th width="100">Bill Qnty.(Dzn.)</th>
                        <th width="100">Remaining Qnty.(Dzn.)</th>
                        <th width="100">Sewing Production (Pcs.)</th>
                        <th width="140">Excess sewing Output Qnty.(Pcs.)</th>
                    </tr>    
                </thead>
            </table>
        <div style="width:1170px; max-height:300px; float:left; overflow-y:scroll;" id="scroll_body">
            <table width="1150"  cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body" align="left">
                <?
                $i=1;
               foreach($order_id_arr as $key_id=>$val){
                   $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ; 
               
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="40" align="center"><?  echo $i ; ?></td>
                    <td><p><? echo $buyer_short_library[$buyer_arr[$key_id]];?></p></td>
                    <td width="110" ><? echo $job_arr[$key_id];?></td>
                    <td width="100"><p><? echo $order_no_arr[$key_id];?></p></td>
                    <td width="130"><p><? echo $style_arr[$key_id];?></p></td>
                    <td width="100" align="right"><? echo round($order_qty_arr[$key_id]);?></td>

                    
                    
                    <td align="right" width="100"><? echo round($sew_approve_qty_arr[$key_id]);?></td>
                    <td align="right" width="100"><? echo round($sew_bill_qty_arr[$key_id]);?></td>
                    <td align="right" width="100"><? echo round($sew_approve_qty_arr[$key_id]-$sew_bill_qty_arr[$key_id]);?></td>
                    
                    <td align="right" width="100"><? echo $sew_pro_qty_arr[$key_id];?></td>
                    <td width="140"  align="right"><? echo ($order_qty_arr[$key_id]-$sew_pro_qty_arr[$key_id]); ?></td>
                </tr>
                <?
                $i++;
                } // end  foreach($order_id_arr as $key_id=>$val)
                ?>
            </table>
        </div>
            
            <table width="1150" border="1" class="rpt_table" rules="all" align="left">
                <tfoot>
                    <th colspan="5" align="right"><b>Total:</b></th>
                    <th width="100" align="right" id="total_po_qnty"></th>
                    <th width="100" align="right" id="total_sew_appv_qty"></th>
                    <th width="100" align="right" id="total_sew_bill_qty">&nbsp;</th>
                    <th width="100" align="right" id="total_remain_qty_sew"></th>
                    <th width="100" align="right" id="total_sew_qnty"></th>
                    <th width="140" align="right" id="total_excess_sewingout_qty"></th>
                </tfoot>
            </table>
    </fieldset>
  </div>
<? echo signature_table(96, $cbo_company_name, "1150px"); ?>
<?
	foreach (glob("$user_id*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id.'_'.time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$report_data=ob_get_contents();
	ob_clean();
	$is_created = fwrite($create_new_excel,$report_data);
	echo $report_data."####".$name."####"."2";
	
exit();
}//sewing short report;




if ($action=='cutting_bill_qty'){//cutting_bill_qty
echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>

<div style="width:650px">
<fieldset style="width:100%"  >
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
        <thead>
            <th width="30">SL</th>
            <th width="120">System ID</th>
            <th width="80">Week Start</th>
            <th width="80">Week End</th>
            <th width="80">Bill Qnty.</th>
            <th width="50">Rate</th>
            <th width="100">Amount</th>
            <th width="">Final Bill</th>
        </thead>
	</table>
	<div style="width:650px; max-height:280px; overflow-y:scroll">   
		<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
			<?
            $i=1;
			$total_cutting_bill_qty=0;
			$total_cutting_bill_amount=0;
			
			$sql="select a.sys_number,a.final_bill, a.week_from_date, a.week_to_date, c.bill_qty , b.amount, b.wo_rate from pro_weekly_wages_bill_mst a,pro_weekly_wages_bill_dtls b, pro_weekly_wages_order_brk c where a.id=b.mst_id and b.id=c.weekly_wages_dtls_id and a.id=c.weekly_wages_mst_id and a.company_id=$company and c.order_id='$po_id' and c.gmt_item_id='$item_id' and a.bill_for=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.sys_number,a.final_bill, a.week_from_date, a.week_to_date, c.bill_qty ,b.amount, b.wo_rate";
			$result_data= sql_select($sql);
            foreach ($result_data as $row)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td width="30" align="center"><? echo $i; ?></td>
                    <td width="120"><? echo $row[csf('sys_number')]; ?></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('week_from_date')]); ?></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('week_to_date')]); ?></td>
                    <td width="80" align="right">
						<?  
							echo round($row[csf('bill_qty')]); 
							$total_cutting_bill_qty += $row[csf('bill_qty')];
						?>
                    </td>
                    <td width="50" align="center"><?  echo $row[csf('wo_rate')];   ?></td>
                    <td width="100" align="right">
						<?  
							echo round($row[csf('bill_qty')]*$row[csf('wo_rate')]);
							$total_cutting_bill_amount += $row[csf('bill_qty')]*$row[csf('wo_rate')];   
						?>
                    </td>
                    <td width="" align="center"><? echo $yes_no[$row[csf('final_bill')]]; ?></td>
                </tr>
            <?
            $i++;
            }
            ?>
            <tr class="tbl_bottom">
                <td align="right" colspan="4">Total</td>
                <td align="right"><?php echo round($total_cutting_bill_qty); ?></td>
                <td></td>
                <td align="right"><?php echo round($total_cutting_bill_amount); ?></td>
                <td></td>
            </tr>
    	</table>
	</div>        
</fieldset>
</div>


<?
}

if ($action=='sewing_bill_qty'){//sewing_bill_qty
echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
extract($_REQUEST);

$sewing_line_lib=return_library_array( "select id,line_name from lib_sewing_line where status_active =1 and is_deleted=0", "id", "line_name"  );


	if($db_type==0)
	{
		$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 group by a.id");
	}
	else if($db_type==2 || $db_type==1)
	{	
		$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.line_number");
	}


foreach($line_data as $row)
{
	$line='';
	$line_number=explode(",",$row[csf('line_number')]);
	foreach($line_number as $val)
	{
		if($line=='') $line=$sewing_line_lib[$val]; else $line.=",".$sewing_line_lib[$val];
	}
	$line_array[$row[csf('id')]]=$line;
}




?>

<div style="width:680px">
<fieldset style="width:100%"  >
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="100%">
        <thead>
            <th width="30">SL</th>
            <th width="120">System ID</th>
            <th width="70">Week Start</th>
            <th width="70">Week End</th>
            <th width="80">Line No</th>
            <th width="70">Bill Qnty.</th>
            <th width="50">Rate</th>
            <th width="100">Amount</th>
            <th width="">Final Bill</th>
        </thead>
	</table>
	<div style="width:680px; max-height:280px; overflow-y:scroll">   
		<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="100%">
			<?
            $i=1;
			$total_sewing_bill_qty=0;
			$total_sewing_bill_amount=0;
			
			
            $sql="select a.sys_number,a.final_bill, a.week_from_date, a.week_to_date,c.bill_qty ,b.amount, b.wo_rate,b.line_id,b.prod_reso_allo from pro_weekly_wages_bill_mst a,pro_weekly_wages_bill_dtls b, pro_weekly_wages_order_brk c where a.id=b.mst_id and b.id=c.weekly_wages_dtls_id and a.id=c.weekly_wages_mst_id and a.company_id=$company and c.order_id='$po_id' and c.gmt_item_id='$item_id' and a.bill_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.sys_number,a.final_bill, a.week_from_date, a.week_to_date, c.bill_qty ,b.amount, b.wo_rate,b.line_id,b.prod_reso_allo";
			$result_data= sql_select($sql);
            foreach ($result_data as $row)
            {
                if($row[csf('prod_reso_allo')]==1) $lineArr=$line_array; else  $lineArr=$sewing_line_lib;
				
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td width="30" align="center"><? echo $i; ?></td>
                    <td width="120"><? echo $row[csf('sys_number')]; ?></td>
                    <td width="70" align="center"><? echo change_date_format($row[csf('week_from_date')]); ?></td>
                    <td width="70" align="center"><? echo change_date_format($row[csf('week_to_date')]); ?></td>
                    <td width="80"><? echo $lineArr[$row[csf('line_id')]]; ?></td>
                    <td width="70" align="right">
						<?  
							echo round($row[csf('bill_qty')]); 
							$total_sewing_bill_qty += $row[csf('bill_qty')];
						?>
                    </td>
                    <td width="50" align="right"><?  echo $row[csf('wo_rate')];   ?></td>
                    <td width="100" align="right">
						<?  
							echo round($row[csf('bill_qty')]*$row[csf('wo_rate')]);
							$total_sewing_bill_amount += $row[csf('bill_qty')]*$row[csf('wo_rate')];   
						?>
                    </td>
                    <td width="" align="center"><? echo $yes_no[$row[csf('final_bill')]]; ?></td>
                </tr>
            <?
            $i++;
            }
            ?>
            <tr class="tbl_bottom">
                <td align="right" colspan="5">Total</td>
                <td align="right"><?php echo round($total_sewing_bill_qty); ?></td>
                <td></td>
                <td align="right"><?php echo round($total_sewing_bill_amount); ?></td>
                <td></td>
            </tr>
    	</table>
	</div>        
</fieldset>
</div>
<?
}


if ($action=='iron_bill_qty'){//iron_bill_qty
echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
extract($_REQUEST);
?>

<div style="width:650px">
<fieldset style="width:100%"  >
	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
        <thead>
            <th width="30">SL</th>
            <th width="120">System ID</th>
            <th width="80">Week Start</th>
            <th width="80">Week End</th>
            <th width="80">Bill Qnty.</th>
            <th width="50">Rate</th>
            <th width="100">Amount</th>
            <th width="">Final Bill</th>
        </thead>
	</table>
	<div style="width:650px; max-height:280px; overflow-y:scroll">   
		<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
			<?
            $i=1;
			$total_iron_bill_qty=0;
			$total_iron_bill_amount=0;
			
			
            $sql="select a.sys_number,a.final_bill, a.week_from_date, a.week_to_date, c.bill_qty , b.amount, b.wo_rate from pro_weekly_wages_bill_mst a,pro_weekly_wages_bill_dtls b, pro_weekly_wages_order_brk c where a.id=b.mst_id and b.id=c.weekly_wages_dtls_id and a.id=c.weekly_wages_mst_id and a.company_id=$company and c.order_id='$po_id' and c.gmt_item_id='$item_id' and a.bill_for=35 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.sys_number,a.final_bill, a.week_from_date, a.week_to_date, c.bill_qty ,b.amount, b.wo_rate";
			$result_data= sql_select($sql);
            foreach ($result_data as $row)
            {
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td width="30" align="center"><? echo $i; ?></td>
                    <td width="120"><? echo $row[csf('sys_number')]; ?></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('week_from_date')]); ?></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('week_to_date')]); ?></td>
                    <td width="80" align="right">
						<?  
							echo round($row[csf('bill_qty')]); 
							$total_iron_bill_qty += $row[csf('bill_qty')];
						?>
                    </td>
                    <td width="50" align="right"><?  echo $row[csf('wo_rate')];   ?></td>
                    <td width="100" align="right">
						<?  
							echo round($row[csf('bill_qty')]*$row[csf('wo_rate')]);
							$total_iron_bill_amount += $row[csf('bill_qty')]*$row[csf('wo_rate')];   
						?>
                    </td>
                    <td width="" align="center"><? echo $yes_no[$row[csf('final_bill')]]; ?></td>
                </tr>
            <?
            $i++;
            }
            ?>
            <tr class="tbl_bottom">
                <td align="right" colspan="4">Total</td>
                <td align="right"><?php echo round($total_iron_bill_qty); ?></td>
                <td></td>
                <td align="right"><?php echo round($total_iron_bill_amount); ?></td>
                <td></td>
            </tr>
    	</table>
	</div>        
</fieldset>
</div>
<?
}

