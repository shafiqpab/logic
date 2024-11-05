<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if($action=="load_drop_down_buyer")
{
    $sql="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name";
	//echo $sql;
 	echo create_drop_down( "cbo_buyer_name", 140,$sql,"id,buyer_name", 1, "-- Select Buyer--", $selected, "","","","","","");
	
}


if($db_type==0) $year_cond="SUBSTRING_INDEX(insert_date, '-', 1)";
if($db_type==0) $year_cond="to_char(insert_date,'YYYY')";

//item style------------------------------//
if($action=="style_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	?>
<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
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
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
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
	if($company==0) $company_name=""; else $company_name="company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and buyer_name=$buyer";
	if($db_type==0) $year_cond="year(insert_date)";
    if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
	$sql = "select id,style_ref_no,job_no_prefix_num,$year_cond as year from wo_po_details_master where $company_name $buyer_name"; 
	//echo $sql;
	echo create_list_view("list_view", "Style Refference,Job no,Year","190,100,100","440","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}



//order wise browse------------------------------//
if($action=="order_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	?>
<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
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
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
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
	if($company==0) $company_name=""; else $company_name=" and b.company_name=$company";
	if($buyer==0) $buyer_name=""; else $buyer_name="and b.buyer_name=$buyer";
	if(str_replace("'","",$style_id)==0) $style_cond=""; else $style_cond="and b.id in(".str_replace("'","",$style_id).")";
	if($db_type==0) $year_cond="year(b.insert_date)";
    if($db_type==2) $year_cond="to_char(b.insert_date,'YYYY')";
	$sql = "select distinct a.id,a.po_number,b.style_ref_no,b.job_no_prefix_num,$year_cond as year from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name  $buyer_name $style_cond"; 
	echo create_list_view("list_view", "Style Ref,Order Number,Job No, Year","150,150,100,100,","550","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "style_ref_no,po_number,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}


//Item wise browse------------------------------//
if($action=="item_wise_search")
{		  
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	?>
<script>
		
		var selected_id = new Array;
		var selected_name = new Array;
		
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
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
				var splitSTR = strCon.split("_");
				var str = splitSTR[0];
				var selectID = splitSTR[1];
				var selectDESC = splitSTR[2];
				//$('#txt_individual_id' + str).val(splitSTR[1]);
				//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
				
				toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
				
				if( jQuery.inArray( selectID, selected_id ) == -1 ) {
					selected_id.push( selectID );
					selected_name.push( selectDESC );					
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == selectID ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 ); 
				}
				var id = ''; var name = ''; var job = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					id += selected_id[i] + ',';
					name += selected_name[i] + ','; 
				}
				id 		= id.substr( 0, id.length - 1 );
				name 	= name.substr( 0, name.length - 1 ); 
				
				$('#txt_selected_id').val( id );
				$('#txt_selected').val( name ); 
				//alert(name );
		}
    </script>
<?
	extract($_REQUEST);
	if(str_replace("'","",$company_name)!=0) $company_cond=" and b.company_id=".str_replace("'","",$company_name)."";  else $company_cond='';

	if(str_replace("'","",$orderno)!="")
	{
	    $sql = "select  b.item_group_id,g.item_name from  order_wise_pro_details a,product_details_master b,lib_item_group g where a.prod_id=b.id and b.item_group_id=g.id and b.item_group_id!=0  and a.po_breakdown_id in (".str_replace("'","",$orderno).")  group by   b.item_group_id, g.item_name"; 
	}
	else if(str_replace("'","",$order_entry)!="")
	{
    	$sql = "select  b.item_group_id, g.item_name from  order_wise_pro_details a,product_details_master b,wo_po_break_down w,lib_item_group g where w.po_number= '".str_replace("'","",$order_entry)."'  and w.id=a.po_breakdown_id  and a.prod_id=b.id and b.item_group_id=g.id  and b.item_group_id!=0  group by   b.item_group_id,g.item_name";	
	}
	else
	{
		$sql = "select  b.item_group_id,g.item_name from  product_details_master b,lib_item_group g where  b.item_group_id=g.id  and b.item_group_id!=0 $company_cond group by   b.item_group_id,g.item_name";
	}

	echo create_list_view("list_view", "Item Group,Item Name","150,200","400","310",0, $sql , "js_set_value", "item_group_id,item_name", "", 1, "0", $arr, "item_group_id,item_name", "","setFilterGrid('list_view',-1)","0","",1) ;	
	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	exit();
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if(str_replace("'","",$cbo_company_name)!=0) $company_cond=" and b.company_name=".str_replace("'","",$cbo_company_name).""; else $company_cond="";
	
	if(str_replace("'","",$cbo_buyer_name)!=0) $buyer_cond=" and b.buyer_name =".str_replace("'","",$cbo_buyer_name).""; else $buyer_cond="";
	if(str_replace("'","",$txt_style_id)!="") $style_cond=" and b.id in(".str_replace("'","",$txt_style_id).")"; else $style_cond="";

	if(str_replace("'","",$txt_order_id)!="") $order_cond=" and a.id in(".str_replace("'","",$txt_order_id).")";
	else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and a.po_number in(".str_replace("'","",$txt_order_no).")"; 
    else $order_cond="";
	
	if(str_replace("'","",$txt_item_id)!="") $item_cond=" and p.item_group_id in(".str_replace("'","",$txt_item_id).")"; else $item_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $date_diff=" and a.pub_shipment_date between ".$txt_date_from." and ".$txt_date_to.""; else     $date_diff="";
	
	$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
	$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
    $order_arr = return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$trim_group_arr =array();
	$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$order_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
	}
	
	$styleRef_arr=return_library_array( "select job_no,style_ref_no from wo_po_details_master", "job_no", "style_ref_no" );// where company_name=$cbo_company_name
	
	$planning_arr=return_library_array( "select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id", "id", "booking_no" );
	
	$requisition_arr=array();
	$datareqsnArray=sql_select( "select requisition_no, knit_id, sum(yarn_qnty) as qnty from ppl_yarn_requisition_entry group by requisition_no" );
	foreach($datareqsnArray as $row)
	{
		$requisition_arr[$row[csf('requisition_no')]]['qnty']=$row[csf('qnty')];
		$requisition_arr[$row[csf('requisition_no')]]['knit_id']=$row[csf('knit_id')];
	}

	ob_start();
	?>

	<fieldset style="width:1320px;">
	<table cellpadding="0" cellspacing="0" width="1320">
	    <tr>
	        <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
	    </tr>
	    <tr>
	        <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	    </tr>
	</table>
	<table width="1320" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
 	  	<thead>
        	<th width="30">SL</th>
            <th width="100">Order NO</th>
            <th width="100">Buyer Name</th>
            <th width="80">Style</th>
            <th width="100">RMG Qnty Pcs</th>
            <th width="100">RMG Qnty/Dzn</th>
            <th width="80">Item Group</th>
            <th width="100">Receive Date</th>
            <th width="100">Challan No</th>
            <th width="120">MRR No</th>
            <th width="80">UOM</th>
            <th width="100">Receive Qnty</th>
            <th width="100">Issue Date</th>
            <th width="120">GIN No</th>
            <th width="100">Issue Challan</th>
            <th width="100">Issue Qty.</th>
            <th width="100">Stock Qty.</th>
      	</thead>
	</table>
	<div style="width:1320px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    	<table width="1320" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
		    <?
			if($db_type==0)
			{
				$sql_re="SELECT a.po_number, a.pub_shipment_date, a.po_quantity, b.style_ref_no, b.buyer_name, p.item_group_id, t.transaction_date, r.recv_number, r.challan_no, IFNULL(CASE WHEN o.entry_form =24 THEN  o.quantity ELSE null END,null) AS receive_qty 
				from wo_po_break_down a,wo_po_details_master b, inv_transaction t,order_wise_pro_details o, product_details_master p,inv_receive_master r 
				where a.job_no_mst=b.job_no and o.po_breakdown_id=a.id and t.id=o.trans_id and o.entry_form=24 and r.id=t.mst_id and t.prod_id=p.id $company_cond $buyer_cond  $style_cond $order_cond $item_cond $date_diff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and o.status_active=1 and o.is_deleted=0 
				and p.status_active=1 and p.is_deleted=0 and r.status_active=1 and r.is_deleted=0  
				group by a.po_number,b.buyer_name,b.style_ref_no,p.item_group_id,r.recv_number order by a.po_number,p.item_group_id,t.transaction_date"; 
			}
			if($db_type==2)
			{
				$sql_re="SELECT a.po_number, a.pub_shipment_date, a.po_quantity, b.style_ref_no, b.buyer_name, p.item_group_id, t.transaction_date, r.recv_number, r.challan_no, nvl(CASE WHEN o.entry_form =24 THEN  o.quantity ELSE null END,null) AS receive_qty 
				from wo_po_break_down a,wo_po_details_master b, inv_transaction t,order_wise_pro_details o, product_details_master p,inv_receive_master r 
				where a.job_no_mst=b.job_no and o.po_breakdown_id=a.id and t.id=o.trans_id and o.entry_form=24 and r.id=t.mst_id and t.prod_id=p.id $company_cond $buyer_cond  $style_cond $order_cond $item_cond $date_diff and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and o.status_active=1 and o.is_deleted=0 
				and p.status_active=1 and p.is_deleted=0 and r.status_active=1 and r.is_deleted=0  
				group by a.po_number, a.pub_shipment_date, a.po_quantity, b.style_ref_no, b.buyer_name, p.item_group_id, t.transaction_date, r.recv_number, r.challan_no,o.entry_form,o.quantity order by a.po_number,p.item_group_id,t.transaction_date"; 
			}
			// echo $sql_re;

			$result_re=sql_select($sql_re);
			$i=1;
		    $n=0; 
			$check_arr=array();
			$issue_num_arr=array();
		    $current_stock=0;
		    $flag=0;
		    $sl_no=0;
            foreach($result_re as $value)
	        {
				$item_id=$value[csf("item_group_id")];

				if($item_id==10)
				{
					$rmg_quantity=$value[csf("po_quantity")]/12;	
				}
				else if($item_id==61)
				{
					$rmg_quantity=$value[csf("po_quantity")]/24; 
				}
				else
				{
					$rmg_quantity=$value[csf("po_quantity")];
				}

				if( !in_array($value[csf("po_number")],$check_arr))
				{
					$current_stock=$value[csf("receive_qty")]; 
				}
				else
				{
					if( !in_array($value[csf("item_group_id")],$check_arr_item))
					{
						$current_stock=$value[csf("receive_qty")]; 
					}
					else
					{
						$current_stock=$current_stock+$value[csf("receive_qty")];  
					}
				}
				$check_arr[]=$value[csf("po_number")];
				$check_arr_item[]=$value[csf("item_group_id")];
				$sl_no++;
			    if ($sl_no%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $sl_no; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl_no; ?>">
					<td width="30" align="center"><p><? echo $sl_no; ?></p></td>
					<td width="100" align="center"><p><? echo $value[csf("po_number")]; ?></p></td>
					<td width="100" align="center"><p><? echo $buyer_arr[$value[csf("buyer_name")]]; ?></p></td>
					<td width="80" align="center"><p><? echo $value[csf("style_ref_no")];?></p></td>
					<td width="100" align="center"><p><? echo $value[csf("po_quantity")]; ?></p></td>
					<td width="100" align="center"><p><? echo round($rmg_quantity) ?></p></td>
					<td width="100" align="center"><p><? echo $trim_group_arr[$value[csf("item_group_id")]]['name']; ?></p></td>
					<td width="100" align="center"><p><? echo $value[csf("transaction_date")]; ?></p></td>
					<td width="100" align="center"><p><? echo $value[csf("challan_no")]; ?></p></td>
					<td width="120" align="center"><p><? echo $value[csf("recv_number")]; ?></p></td>
					<td width="80" align="center"><p><? echo   $unit_of_measurement[$order_group_arr[$value[csf('item_group_id')]]['uom']]; ?></p></td>
					<td width="100" align="center"><p><? echo $value[csf("receive_qty")]; ?></p></td>
					<td width="100" align="center"><p><?   ?></p></td>
					<td width="120" align="center" ><p></p></td>
					<td width="100" align="center"><p></p></td>
					<td width="100" align="center"><p></p></td>
					<td width="100" align="center"><p><? echo $current_stock;?></p></td>
				</tr>
				<?                         
			    if($flag==1)
				{
				    $sl_no++;
					if($current_stock>=$new_issue)
					{
						$current_stock=$current_stock-$new_issue;
						
					    if ($sl_no%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $sl_no; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl_no; ?>">
							<td width="30" align="center"><p><? echo $sl_no; ?></p></td>
							<td width="100" align="center"><p><? echo $val[csf("po_number")]; ?></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="80" align="center"><p><? echo $val[csf("style_ref_no")];?></p></td>
							<td width="100" align="center"><p><? echo $val[csf("po_quantity")]; ?></p></td>
							<td width="100" align="center"><p><? echo round($rmg_quantity) ?></p></td>
							<td width="100" align="center"><p><? echo $trim_group_arr[$val[csf("item_group_id")]]['name']; ?></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="120" align="center"><p></p></td>
							<td width="80" align="center"><p><? echo   $unit_of_measurement[$order_group_arr[$val[csf('item_group_id')]]['uom']]; ?></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="100" align="center"><p><? echo $new_date; ?></p></td>
							<td width="120" align="center" <p>><? echo $new_gin; ?></td></td>
							<td width="100" align="center"><p><? echo $val[csf("challan_no")]; ?></p></td>
							<td width="100" align="center"><p><?  echo $new_issue; ?></p></td>
							<td width="100" align="center"><p><?  echo $current_stock;?></p></td>
						</tr>
                 		<?	 
				     	$flag=0; 
						$new_issue=0;
					}//flat end

					if($current_stock<$new_issue)
					{
						$issue_qty=$current_stock;
						
				    	$current_stock=0;
						
					    if ($sl_no%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $sl_no; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl_no; ?>">
							<td width="30" align="center"><p><? echo $sl_no; ?></p></td>
							<td width="100" align="center"><p><? echo $val[csf("po_number")]; ?></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="80" align="center"><p><? echo $val[csf("style_ref_no")];?></p></td>
							<td width="100" align="center"><p><? echo $val[csf("po_quantity")]; ?></p></td>
							<td width="100" align="center"><p><? echo round($rmg_quantity) ?></p></td>
							<td width="100" align="center"><p><? echo $trim_group_arr[$val[csf("item_group_id")]]['name']; ?></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="120" align="center"><p></p></td>
							<td width="80" align="center"><p><? echo   $unit_of_measurement[$order_group_arr[$val[csf('item_group_id')]]['uom']]; ?></p></td>
							<td width="100" align="center"><p></p></td>
							<td width="100" align="center"><p><? echo $new_date; ?></p></td>
							<td width="120" align="center" <p>><? echo $new_gin; ?></td></td>
							<td width="100" align="center"><p><? echo $val[csf("challan_no")]; ?></p></td>
							<td width="100" align="center"><p><? echo $issue_qty; ?></p></td>
							<td width="100" align="center"><p><? echo 0;?></p></td>
						</tr>
                 		<?	 
				     	
						$new_issue=$new_issue-$issue_qty; 
						$flag=1;
					}//flat end
				}

	            //issue start
			    if( $current_stock >0 && $flag==0)
			    { 
					if( !in_array($value[csf("po_number")],$check_arr))
					{
						$current_stock=0; 
					}
						   
					$sql="SELECT o.id as owp_id, a.po_number, a.pub_shipment_date, a.po_quantity, b.style_ref_no, b.buyer_name, p.item_group_id, t.transaction_date, r.issue_number, r.challan_no, o.quantity   AS iss_qty 
					from wo_po_break_down a,wo_po_details_master b, inv_transaction t,order_wise_pro_details o, product_details_master p,inv_issue_master r 
					where a.job_no_mst=b.job_no and o.po_breakdown_id=a.id and o.trans_id=t.id and o.entry_form=25 and t.mst_id=r.id and t.prod_id=p.id and a.po_number='".$value[csf("po_number")]."' and p.item_group_id=".$value[csf("item_group_id")]."order by a.po_number,p.item_group_id, b.style_ref_no, b.buyer_name,t.transaction_date  ASC";

					$result=sql_select($sql);
					$m=1;
					$issue_check_arr=array();
					foreach( $result as $val)
					{ 
						if( in_array($val[csf("po_number")],$check_arr))
						{
							if($val[csf("item_group_id")]==$value[csf("item_group_id")])
							{
								if($current_stock > 0)
								{
									if( !in_array($val[csf("owp_id")],$issue_num_arr))
									{
										if($current_stock-$val[csf("iss_qty")]>0) 
										{
											$issue_qty=$val[csf("iss_qty")];
											$current_stock=$current_stock-$val[csf("iss_qty")]; 
										}
										else
										{
											$issue_qty=$current_stock;
											$current_stock=0;
											$flag=1;
											$new_issue=$val[csf("iss_qty")]-$issue_qty;
											$new_order=$val[csf("po_number")];
											$new_date=$val[csf("transaction_date")];
											$new_gin=$val[csf("issue_number")];
										}
										$sl_no++;
										if ($sl_no%2==0)$bgcolor="#E9F3FF";	else $bgcolor="#FFFFFF"; 
										?>
                                        <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $sl_no; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl_no; ?>">
											<td width="30" align="center"><p><? echo $sl_no; ?></p></td>
											<td width="100" align="center"><p><?  echo $val[csf("po_number")]; ?></p></td>
											<td width="100" align="center"><p><? echo $buyer_arr[$val[csf("buyer_name")]]; ?></p></td>
											<td width="80" align="center"><p><? echo $val[csf("style_ref_no")];?></p></td>
											<td width="100" align="center"><p><? echo $val[csf("po_quantity")]; ?></p></td>
											<td width="100" align="center"><p><? echo round($rmg_quantity) ?></p></td>
											<td width="100" align="center"><p><? echo $trim_group_arr[$val[csf("item_group_id")]]['name']; ?></p></td>
											<td width="100" align="center"><p></p></td>
											<td width="100" align="center"><p></p></td>
											<td width="120" align="center"><p></p></td>
											<td width="80" align="center"><p><? echo   $unit_of_measurement[$order_group_arr[$val[csf('item_group_id')]]['uom']]; ?></p></td>
											<td width="100" align="center"><p></p></td>
											<td width="100" align="center"><p><? echo $val[csf("transaction_date")]; ?></p></td>
											<td width="120" align="center" <p>><? echo $val[csf("issue_number")]; ?></td></td>
											<td width="100" align="center"><p><? echo $val[csf("challan_no")]; ?></p></td>
											<td width="100" align="center"><p><? echo $issue_qty; ?></p></td>
											<td width="100" align="center"><p><? echo $current_stock;?></p></td>
                                        </tr>
                                        <?	                                  
										$issue_check_arr[]=$val[csf("item_group_id")];
										$issue_num_arr[]=$val[csf("owp_id")];
										$m++;
								    }
							    }						
					        }
						}
					}
			    }  //issue end	   
		        $i++;
			}
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
			?>
		</table>
	</div>
	</fieldset>	
	<?
}
?>
