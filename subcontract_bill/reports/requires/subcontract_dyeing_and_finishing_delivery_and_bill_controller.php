<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	
	echo create_drop_down( "cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type ) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 ); 
	exit();
}


 
if($action=="report_generate")
{ 
	$process = array( &$_POST );

	
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	
	$location_arr = return_library_array("select id,location_name from lib_location where   status_active =1 and is_deleted=0 order by location_name","id","location_name");
	
	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	
	
	$cbo_company_id= str_replace("'","",$cbo_company_id);
	
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$cbo_party_name=str_replace("'","",trim($cbo_party_name));
	$cbo_date_type=str_replace("'","",trim($cbo_date_type));
	$cbo_year=str_replace("'","",trim($cbo_year));
	//$report_type=str_replace("'","",trim($report_type));
	$txt_order_id=str_replace("'","",trim($txt_order_id));
	$search_type=implode(",", array_unique(explode(",", str_replace("'","",trim($search_type)))));
	$supplier_con='';
	$parti_con='';
	$company_con='';
	$bill_cond='';
	
	$year_cond="";
	if(!empty($cbo_year))
	{
		$column_name="b.insert_date";
		if($search_type==3  )
		{
			$column_name="b.insert_date";
			
		}
		else if($search_type==1)
		{
			$column_name="c.insert_date";
			
		}
		else if($search_type==2)
		{
			$column_name="e.insert_date";
			
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR($column_name)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char($column_name,'YYYY')=$cbo_year";
		}
	}
	$company_cond="";
	if(!empty($cbo_company_id))
	{
		$company_cond=" and c.company_id='$cbo_company_id'";
	}
	$party_cond="";
	if(!empty($cbo_party_name))
	{
		$party_cond=" and c.party_id='$cbo_party_name'";
	}


	if($start_date!="" && $end_date!="" && empty($txt_order_id))
	{

		
		if($cbo_date_type==2)
		{
			if($db_type==0)
			{
				$date_cond=" and e.bill_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
			}
			else
			{
				$date_cond=" and e.bill_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
			
		}
		else
		{
			if($db_type==0)
			{
				$date_cond=" and c.delivery_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
			}
			else
			{
				$date_cond=" and c.delivery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
			$search_type=1;
			
		}
		
	}
	else
	{
		$date_cond="";
	}

	$search_con="";
	if(!empty($txt_order_id))
	{

		if($search_type==3)
		{
			 $search_con=where_con_using_array(explode(",", $txt_order_id),0,"b.id");
		}
		else if($search_type==2)
		{
			 $search_con=where_con_using_array(explode(",", $txt_order_id),0,"e.id");
		}
		else
		{
			$search_con=where_con_using_array(explode(",", $txt_order_id),0,"c.id");
		}
	}

	
	$with_bill=0;
	if($search_type==3 ||  $search_type==1)
	{
			$sql="SELECT   b.job_no_mst as subcon_job,
					       b.order_no,
					       b.cust_style_ref,
					       b.cust_buyer,
					       c.delivery_no,
					       c.delivery_date,
					       c.party_id,
					       d.id as delivery_id,
					       b.id as order_id,
					       c.delivery_prefix_num,
					       d.batch_id,
					       d.item_id,
					       c.challan_no
					  FROM 
					       subcon_ord_dtls b,
					       subcon_delivery_mst c,
					       subcon_delivery_dtls d
					 WHERE      c.id = d.mst_id
					       AND d.order_id = b.id
					       AND c.process_id = 4
					      
					       AND b.is_deleted = 0
					       AND c.is_deleted = 0
					       AND d.is_deleted = 0
					       $search_con
					       $company_cond
					       $party_cond
					       $year_cond
					       $date_cond
					       ";
		}
		else
		{
			$sql="
				SELECT b.job_no_mst as subcon_job,
				       b.order_no,
				       b.cust_style_ref,
				       b.cust_buyer,
				       c.delivery_no,
				       c.delivery_date,
				       c.party_id,
				       e.bill_date,
				       e.bill_no,
			           e.id  as bill_id,
				       b.id as order_id,
				       c.delivery_prefix_num,
				       d.batch_id,
				       d.item_id,
				       f.dia_width_type,
				       f.color_id,
				       f.color_range_id,
				       f.packing_qnty,
				       f.delivery_qty,
				       f.rate,
				       f.add_rate,
				       f.amount,
				       f.remarks,
				       f.currency_id,
				       f.add_process_name,
				       f.shade_percentage,
				       c.challan_no
				  FROM 
				       subcon_ord_dtls b,
				       subcon_delivery_mst c,
				       subcon_delivery_dtls d,
				       subcon_inbound_bill_mst e,
				       subcon_inbound_bill_dtls f
				 WHERE      c.id = d.mst_id
				       AND e.id = f.mst_id
				       AND d.order_id = b.id
				       AND c.challan_no = f.challan_no
			           AND c.delivery_date = f.delivery_date
				       AND f.order_id = b.id
				       AND c.process_id = 4
				       AND b.is_deleted = 0
				       AND c.is_deleted = 0
				       AND d.is_deleted = 0
				       AND f.delivery_id=to_char(d.id)
				      $search_con
					  $company_cond
					  $party_cond
					  $year_cond
					  $date_cond
				       ";

				       $with_bill=1;
		}
	
    //echo $sql;
		//-- AND f.process_id = 4
	    //--AND c.process_id = 4
	$res=sql_select($sql);

	$bill_data=array();
	if($with_bill==0)
	{
		$order_id_arr=array();
		foreach ($res as $row) 
		{
			array_push($order_id_arr, $row[csf('order_id')]);
		}
		$order_id_cond=where_con_using_array($order_id_arr,0,"b.order_id");
		$b_sql="SELECT a.bill_no,
				       a.prefix_no_num,
				       b.order_id,
				       b.delivery_date,
				       b.challan_no,
				       a.bill_date,
				       b.dia_width_type,
				       b.color_id,
				       b.color_range_id,
				       b.packing_qnty,
				       b.delivery_qty,
				       b.rate,
				       b.add_rate,
				       b.amount,
				       b.remarks,
				       b.currency_id,
				       b.add_process_name,
				       b.shade_percentage,
				       b.delivery_id
				  FROM subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b
				 WHERE a.id = b.mst_id  
				 $order_id_cond";
		$b_res=sql_select($b_sql);
		//echo $b_sql;
		
		foreach ($b_res as $row) 
		{
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_no']=$row[csf('bill_no')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_date']=$row[csf('bill_date')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['dia_width_type']=$row[csf('dia_width_type')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['color_id']=$row[csf('color_id')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['color_range_id']=$row[csf('color_range_id')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['packing_qnty']+=$row[csf('packing_qnty')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['rate']=$row[csf('rate')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['add_rate']=$row[csf('add_rate')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['amount']+=$row[csf('amount')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['remarks']=$row[csf('remarks')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['currency_id']=$row[csf('currency_id')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['add_process_name']=$row[csf('add_process_name')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['shade_percentage']=$row[csf('shade_percentage')];
		}
	}
	$table_width=2550; $colspan="24";
	 $party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	 $color_library=return_library_array( "select id,color_name from  lib_color where status_active=1 and is_deleted=0", "id","color_name");
	$color_id_arr=return_library_array( "select id, color_id from subcon_delivery_dtls",'id','color_id');
	$inv_item_arr=return_library_array( "select id,material_description from sub_material_dtls",'id','material_description');
	$prod_item_arr=return_library_array( "select id,fabric_description from subcon_production_dtls",'id','fabric_description');
	$prod_process_arr=return_library_array( "select cons_comp_id, process from subcon_production_dtls",'cons_comp_id','process');
	$prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
	$location_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	
	 $batch_array=array();
	$batch_sql="select a.id, a.batch_no, a.extention_no, b.id as item_id, b.fabric_from, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$batch_sql_result=sql_select($batch_sql);
	foreach($batch_sql_result as $row)
	{
		//$batch_array[$row[csf('batch_no')]][$row[csf('extention_no')]][$row[csf('id')]]['fabric_from']=$row[csf('fabric_from')];
		$batch_array[$row[csf('id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
		$batch_array[$row[csf('id')]][$row[csf('item_id')]]['batch_no']=$row[csf('batch_no')];
		
	}
	


	ob_start();
	?>
    <fieldset style="width:<?php echo $table_width+20;?>">	
    	<center>
	        <table width="<?php echo $table_width;?>" >
	        	<thead>
	        		<tr>
						<th  align="center" colspan="24"><?=$company_arr[$cbo_company_id];?></th>
					</tr>
					<tr>
						<th  align="center" colspan="24">In-Bound Subcontract Dyeing And Finishing Delivery and Bill Report</th>
					</tr>
					<tr>

						<th  align="center" colspan="24">From <?=change_date_format($start_date)?> To <?=change_date_format($end_date)?></th>
					</tr>
	        	</thead>
	        </table>
        </center>
		<table width="<?php echo $table_width;?>" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0"  id="list_view">
			
			<thead>
				
				<tr>
					<th width="35">Sl No</th>
					<th width="120">Party name</th>
					<th width="70">Delivery Date</th>
					<th width="120">Delivery Challan No</th>
					<th width="70">Bill Date</th>
					<th width="120">Bill No </th>
					<th width="120">Job No </th>
					<th width="120">Order </th>
					<th width="120">Style </th>
					<th width="120">Buyer </th>
					<th width="100">Batch No </th>
					<th width="170">Fabric Description </th>
					<th width="100">Color Range </th>
					<th width="120">Color/Process </th>
					<th width="70">Shade Percentage </th>
					<th width="200">Additional Process </th>
					<th width="100">D/W Type </th>
					<th width="70">No. Roll </th>
					<th width="80">Qty(Kg) </th>
					<th width="70">Rate (Main) </th>
					<th width="70">Rate (Add.) </th>
					<th width="80">Amount </th>
					<th width="90">Currency </th>
					<th width="150">Remarks </th>
				</tr>
			</thead>
			<tbody >
				<?
				$i=1;
				$total_delivery_qty=0;
				$total_amount=0;
				$total_packing_qnty=0;
				foreach ($res as $row) 
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$color_range_id='';
					if($with_bill==1)
					{
						$bill_no=$row[csf('bill_no')];
						$bill_date=$row[csf('bill_date')];
						$dia_width_type=$row[csf('dia_width_type')];
						$color_id=$row[csf('color_id')];
						$color_range_id=$row[csf('color_range_id')];
						$packing_qnty=$row[csf('packing_qnty')];
						$delivery_qty=$row[csf('delivery_qty')];
						$rate=$row[csf('rate')];
						$add_rate=$row[csf('add_rate')];
						$amount=$row[csf('amount')];
						$remarks=$row[csf('remarks')];
						$currency_id=$row[csf('currency_id')];
						$add_process_name=$row[csf('add_process_name')];
						$shade_percentage=$row[csf('shade_percentage')];
					}	
					else
					{
						$bill_no=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_no'];
						$bill_date=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_date'];
						
						
						$dia_width_type=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['dia_width_type'];
						$color_id=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['color_id'];
						$color_range_id=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['color_range_id'];
						$packing_qnty=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['packing_qnty'];
						$delivery_qty=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['delivery_qty'];
						$rate=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['rate'];
						$add_rate=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['add_rate'];
						$amount=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['amount'];
						$remarks=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['remarks'];
						$currency_id=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['currency_id'];
						$add_process_name=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['add_process_name'];
						//$shade_percentage=$row[csf('shade_percentage')];
						$shade_percentage=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['shade_percentage'];
					}
						
					$batch_no='';
					
					$item_description=$batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['item_description'];
					$batch_no=$batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['batch_no'];

					?>
					
					<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
						<td><p><?=$i;?>&nbsp;</p></td>
						<td><p><?=$party_arr[$row[csf('party_id')]];?></p></td>
						<td><p><?=change_date_format($row[csf('delivery_date')]);?>&nbsp;</p></td>
						<td><p><?=$row[csf('delivery_no')];?></p></td>
						<td><p><?=change_date_format($bill_date);?>&nbsp;</p></td>
						<td><p><?=$bill_no;?></p></td>
						<td><p><?=$row[csf('subcon_job')];?></p></td>
						<td><p><?=$row[csf('order_no')];?></p></td>
						<td><p><?=$row[csf('cust_style_ref')];?></p></td>
						<td><p><?=$row[csf('cust_buyer')];?></p></td>
						<td><p><?=$batch_no;?></p></td>
						<td><p><?=$item_description;?></p></td>
						<td><p><?=$color_range[$color_range_id];?></p></td>
						<td><p><?=$color_library[$color_id];?> </p></td>
						<td><p><?=$shade_percentage;?></p></td>
						<td><p><?=$add_process_name;?></p></td>
						<td><p><?=$fabric_typee[$dia_width_type];?></p></td>
						<td><p><?=number_format($packing_qnty);?></p></td>
						<td><p><?=number_format($delivery_qty,2);?></p></td>
						<td><p><?=number_format($rate);?></p></td>
						<td><p><?=number_format($add_rate,2);?></p></td>
						<td><p><?=number_format($amount,2);?></p></td>
						<td><p><?=$currency[$currency_id];?></p></td>
						<td><p><?=$remarks;?>&nbsp;</p></td>
						
						
						
					</tr>
					<?
					$i++;
					$total_delivery_qty+=$delivery_qty;
					$total_amount+=$amount;
					$total_packing_qnty+=$packing_qnty;
				}
				?>
				
			</tbody>

			<tfoot>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
					
					<td align="right" colspan="17"> <b>Total</b> </td>
					<td align="right"><b><p><?=number_format($total_packing_qnty);?></p></b></td>
					<td align="right"><b><p><?=number_format($total_delivery_qty,2);?></p></b></td>
					<td></td>
					<td></td>
					<td align="right"><b><p><?=number_format($total_amount,2);?></p></b></td>
					<td></td>
					<td></td>
					
					
					
				</tr>
			</tfoot>
			
		</table>
	</fieldset>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}


if($action=="order_search")
{

	echo load_html_head_contents("Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		var type_arr = new Array;
		var sl_id = new Array;
    	function check_all_data(total_row) {
    		if(document.querySelector('#list_view'))
    		{
    			//var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
    			//console.log(tbl_row_count);
    			var tbl_row_count = total_row - 0;
    			for( var i = 1; i < tbl_row_count; i++ ) {
    				
    				if($("#tr_"+i).css("display") !='none'){
    					document.getElementById("tr_"+i).click();
    				}

    			}
    		}
    		else{
    			alert('nothing');
    		}
			
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( strCon )
		{
			console.log(strCon);
			var splitSTR = strCon.split("***");
			var str_or = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			var type = splitSTR[3];
			var unique=selectID+'_'+selectDESC+'_'+type;
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			console.log(unique);
			toggle( document.getElementById( 'tr_' + str_or ), '#FFFFCC' );
			console.log(jQuery.inArray( unique, selected_no ));
			if( jQuery.inArray( unique, selected_no ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( unique );
				type_arr.push( type );
				
			}
			else {
				for( var i = 0; i < selected_no.length; i++ ) {
					if( selected_no[i] == unique ) return;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
				type_arr.splice( i, 1 );
			}
			var id = ''; var name = ''; var type = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
				type += type_arr[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );
			type 	= type.substr( 0, type.length - 1 );
			//alert(num);
			$('#txt_id_string').val( id );
			$('#txt_name_string').val( name );
			$('#txt_type').val( type );
		}
		function closepopup()
		{
			parent.emailwindow.hide();
		}
		
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset>
	            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Party Name</th>
	                    <th>Year</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up">Please Enter Delivery No</th>
	                    <th>Search Type</th>
	                    <th colspan="2">Date Range</th>
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th>
	                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
	                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
	                    <input type='hidden' id='txt_id_string' />
						<input type='hidden' id='txt_name_string' />
						<input type='hidden' id='txt_type' />
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <?
									

									echo create_drop_down( "cbo_party_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$company' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type ) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",5 )
								?>
	                        </td>
	                        <td><? echo create_drop_down( "cbo_year", 50, create_year_array(),"", 1,"-All-",0 , "",0,"" );?></td>
	                        <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Delivery No",2=>"Bill No",3=>"Job No",4=>"Order No",5=>"Style");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>
	                        <td align="center" id="search_by_td" width="130">
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
	                        </td>
	                        <td align="center">
	                    	<?
	                       		$search_by_arr=array(1=>"Bill No",2=>"Delivery No");
								
								echo create_drop_down( "cbo_date_type", 100, $search_by_arr,"",0, "--Select--", "","",0 );
							?>
	                        </td>
	                        <td align="center">
	                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>
	                        </td>
	                        <td>
	                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
	                        </td>
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_party_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('cbo_year_selection').value+'**'+document.getElementById('cbo_date_type').value, 'order_search_list_view', 'search_div', 'subcontract_dyeing_and_finishing_delivery_and_bill_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:80px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                        <td colspan="7" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="order_search_list_view")
{
	extract($_REQUEST);
	
	$data=explode("**", $data);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	$company=str_replace("'","",$data[0]);
	$party_id=str_replace("'","",$data[1]);
	$search_type=str_replace("'","",$data[2]);
	$search_value=str_replace("'","",$data[3]);
	$start_date=$data[4];
	$end_date=$data[5];
	$cbo_year=str_replace("'","",$data[6]);
	$cbo_date_type=str_replace("'","",$data[7]);
	$year_cond="";
	$js_set_value_type="";
	if(!empty($cbo_year) && !empty($search_value))
	{
		$column_name="";
		if($search_type==3 || $search_type==4 || $search_type==5 )
		{
			$column_name="b.insert_date";
			
		}
		else if($search_type==1)
		{
			$column_name="c.insert_date";
			
		}
		else if($search_type==2)
		{
			$column_name="e.insert_date";
			
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR($column_name)=$cbo_year";
		}
		else
		{
			$year_cond=" and to_char($column_name,'YYYY')=$cbo_year";
		}
	}
	$search_con="";
	if(!empty($search_value))
	{
		if($search_type==1 )
		{
			$search_con=" and c.delivery_no like('%$search_value')";
			$js_set_value_type=1;
		}
		else if($search_type==2 )
		{
			$search_con=" and e.bill_no like('%$search_value')";
			$js_set_value_type=2;
		}
		else if($search_type==3 )
		{
			$search_con=" and b.job_no_mst like('%$search_value')";
			$js_set_value_type=3;
		}
		else if($search_type==4 )
		{
			$search_con=" and b.order_no like('%$search_value')";
			$js_set_value_type=3;
		}
		else if($search_type==5 )
		{
			$search_con=" and b.cust_style_ref like('%$search_value')";
			$js_set_value_type=3;
		}
	}
	
	$company_cond="";
	if(!empty($company))
	{
		$company_cond=" and c.company_id='$company'";
	}
	$party_cond="";
	if(!empty($party_id))
	{
		$party_cond=" and c.party_id='$party_id'";
	}

	if($start_date!="" && $end_date!="")
	{

		
		if($cbo_date_type==1)
		{
			if($db_type==0)
			{
				$date_cond=" and e.bill_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
			}
			else
			{
				$date_cond=" and e.bill_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
			$js_set_value_type=2;
		}
		else
		{
			if($db_type==0)
			{
				$date_cond=" and c.delivery_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
			}
			else
			{
				$date_cond=" and c.delivery_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
			$js_set_value_type=1;
		}
		
	}
	else
	{
		$date_cond="";
	}

	$with_bill=0;
	if(!empty($search_value))
	{
		if($search_type==3 || $search_type==4 || $search_type==5 || $search_type==1)
		{
			$sql="SELECT b.job_no_mst as subcon_job,
					       b.order_no,
					       b.cust_style_ref,
					       c.delivery_no,
					       c.delivery_date,
					       c.party_id,
					       b.id as order_id,
					       c.challan_no,
					       d.id as delivery_id,
					       c.id as delivery_system_id
					  FROM 
					       subcon_ord_dtls b,
					       subcon_delivery_mst c,
					       subcon_delivery_dtls d
					 WHERE     c.id = d.mst_id
					       AND d.order_id = b.id
					       AND c.process_id = 4
					       AND b.is_deleted = 0
					       AND c.is_deleted = 0
					       AND d.is_deleted = 0
					       $search_con
					       $company_cond
					       $party_cond
					       $year_cond

					       group by b.job_no_mst ,
					       b.order_no,
					       b.cust_style_ref,
					       c.delivery_no,
					       c.delivery_date,
					       c.party_id,
					       b.id ,
					       c.challan_no,
					       d.id,
					       c.id
					order by c.id
					       ";
		}
		else
		{
			$sql="
				SELECT b.job_no_mst as subcon_job,
				       b.order_no,
				       b.cust_style_ref,
				       c.delivery_no,
				       c.delivery_date,
				       c.party_id,
				       e.bill_date,
				       e.bill_no,
				       b.id as order_id,
				       c.challan_no,
				       c.id as delivery_system_id,
				       e.id as bill_id
				  FROM 
				       subcon_ord_dtls b,
				       subcon_delivery_mst c,
				       subcon_delivery_dtls d,
				       subcon_inbound_bill_mst e,
				       subcon_inbound_bill_dtls f
				 WHERE      c.id = d.mst_id
				       AND e.id = f.mst_id
				       AND d.order_id = b.id
				       AND c.challan_no = f.challan_no
			           AND c.delivery_date = f.delivery_date
				       AND f.order_id = b.id
				       AND d.id=to_char(f.delivery_id)
				       AND c.process_id = 4
				       AND b.is_deleted = 0
				       AND c.is_deleted = 0
				       AND d.is_deleted = 0
				       
				      $search_con
					  $company_cond
					  $party_cond
					  $year_cond
					group by 
					  b.job_no_mst,
				       b.order_no,
				       b.cust_style_ref,
				       c.delivery_no,
				       c.delivery_date,
				       c.party_id,
				       e.bill_date,
				       e.bill_no,
				       b.id ,
				       c.challan_no,
				       c.id,
				       e.id
				order by c.id,e.id
				       ";

				       $with_bill=1;
		}
	}
	else if(empty($start_date) || empty($end_date))
	{
		echo "Select Date Range\n";
		die;
	}
	else if($cbo_date_type==2)
	{
		$sql="SELECT  b.job_no_mst as subcon_job,
				       b.order_no,
				       b.cust_style_ref,
				       c.delivery_no,
				       c.delivery_date,
				       c.party_id,
				       c.challan_no,
				       b.id as order_id,
				       d.id as delivery_id,
				       c.id as delivery_system_id

				  FROM 
				       subcon_ord_dtls b,
				       subcon_delivery_mst c,
				       subcon_delivery_dtls d
				 WHERE     c.id = d.mst_id
				       AND d.order_id = b.id
				       AND c.process_id = 4
				      
				       AND b.is_deleted = 0
				       AND c.is_deleted = 0
				       AND d.is_deleted = 0
				       $date_cond
				       $company_cond
				       $party_cond

				group by b.job_no_mst ,
				       b.order_no,
				       b.cust_style_ref,
				       c.delivery_no,
				       c.delivery_date,
				       c.party_id,
				       c.challan_no,
				       b.id ,
				       d.id ,
				       c.id
				order by c.id
				       ";
	}
	else
	{

		$sql="
			SELECT b.job_no_mst as subcon_job,
			       b.order_no,
			       b.cust_style_ref,
			       c.delivery_no,
			       c.delivery_date,
			       e.bill_date,
			       e.bill_no,
			       c.party_id,
			       b.id as order_id,
			       c.challan_no,
			       c.id as delivery_system_id,
			       e.id as bill_id
			  FROM 
			       subcon_ord_dtls b,
			       subcon_delivery_mst c,
			       subcon_delivery_dtls d,
			       subcon_inbound_bill_mst e,
			       subcon_inbound_bill_dtls f
			 WHERE     c.id = d.mst_id
			       AND e.id = f.mst_id
			       AND d.order_id = b.id
			       AND c.challan_no = f.challan_no
			       AND c.delivery_date = f.delivery_date
			       AND f.order_id = b.id
			       AND c.process_id = 4
			       
			       AND b.is_deleted = 0
			       AND c.is_deleted = 0
			       AND d.is_deleted = 0
			       AND d.id=to_char(f.delivery_id)
			      $date_cond
				  $company_cond
				  $party_cond

				  group by b.job_no_mst ,
			       b.order_no,
			       b.cust_style_ref,
			       c.delivery_no,
			       c.delivery_date,
			       e.bill_date,
			       e.bill_no,
			       c.party_id,
			       b.id,
			       c.challan_no,
			       c.id ,
			       b.id,e.id
			       order by c.id,e.id
			       ";

			       $with_bill=1;
	}

	//echo $sql;

	$res=sql_select($sql);

	$bill_data=array();
	if($with_bill==0)
	{
		$order_id_arr=array();
		foreach ($res as $row) 
		{
			array_push($order_id_arr, $row[csf('order_id')]);
		}
		$order_id_cond=where_con_using_array($order_id_arr,0,"b.order_id");
		$b_sql="SELECT a.bill_no,
				       a.prefix_no_num,
				       b.order_id,
				       b.delivery_date,
				       b.challan_no,
				       a.bill_date,
				       b.delivery_id
				  FROM subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b
				 WHERE a.id = b.mst_id  $order_id_cond";
		$b_res=sql_select($b_sql);
		//echo $b_sql;
		foreach ($b_res as $row) 
		{
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_no']=$row[csf('bill_no')];
			$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_date']=$row[csf('bill_date')];
		}
	}

	$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


	?>
	
	<table width="900" class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" style="text-decoration:none;cursor:pointer" id="list_view">
		<thead>
			<tr>
				<th width="35">Sl No</th>
				<th width="120">Party name</th>
				<th width="70">Delivery Date</th>
				<th width="120">Delivery Challan No</th>
				<th width="70">Bill Date</th>
				<th width="120">Bill No </th>
				<th width="120">Job No </th>
				<th width="120">Order </th>
				<th >Style </th>
			</tr>
		</thead>
		<tbody >
			<?
			$i=1;
			foreach ($res as $row) 
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($with_bill==1)
				{
					$bill_no=$row[csf('bill_no')];
					$bill_date=$row[csf('bill_date')];
				}	
				else
				{
					$bill_no=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_no'];
					$bill_date=$bill_data[$row[csf('challan_no')]][$row[csf('delivery_date')]][$row[csf('order_id')]][$row[csf('delivery_id')]]['bill_date'];
				}
				$data="";	
				if($js_set_value_type==1)
				{
					$data=$i."***".$row[csf('delivery_system_id')]."***".$row[csf('delivery_no')]."***1";
				}
				else if($js_set_value_type==2)
				{
					$data=$i."***".$row[csf('bill_id')]."***".$bill_no."***2";
				}
				else
				{
					if($search_type==3 )
					{
						$data=$i."***".$row[csf('order_id')]."***".$row[csf('subcon_job')]."***3";
					}
					else if($search_type==4 )
					{
						$data=$i."***".$row[csf('order_id')]."***".$row[csf('order_no')]."***3";
					}
					else if($search_type==5 )
					{
						$data=$i."***".$row[csf('order_id')]."***".$row[csf('cust_style_ref')]."***3";
					}
					
				}		
				?>
				<tr bgcolor="<?=$bgcolor; ?>" onClick="js_set_value('<? echo $data; ?>');" id="tr_<? echo $i;?>">
					<td><?=$i;?></td>
					<td><?=$party_arr[$row[csf('party_id')]];?></td>
					<td><?=change_date_format($row[csf('delivery_date')]);?></td>
					<td><p><?=$row[csf('delivery_no')];?></p></td>
					<td><?=change_date_format($bill_date);?></td>
					<td><p><?=$bill_no;?></p></td>
					<td><?=$row[csf('subcon_job')];?></td>
					<td><?=$row[csf('order_no')];?></td>
					<td><?=$row[csf('cust_style_ref')];?></td>
					
					
				</tr>
				<?
				$i++;
			}
			?>
			
		</tbody>
		
	</table>
	<table>
		<tfoot>
			<tr>
				<td align="right" colspan="4">Check All</td>
				<td colspan="5" align="left">
					<input type="checkbox" class="formbutton"  onclick="check_all_data(<?=$i;?>)" >
				</td>
			</tr>
			<tr>
				<td colspan="9"><input type="button" class="formbutton" onclick="closepopup()" value="Close"></td>
			</tr>
		</tfoot>
	</table>
	<?
	
	
	exit();
}

?>