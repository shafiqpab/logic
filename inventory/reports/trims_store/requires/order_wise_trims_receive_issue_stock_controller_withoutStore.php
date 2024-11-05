<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
$size_library=return_library_array("select id,size_name from lib_size", "id", "size_name");
$company_library=return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');
$costing_per_id_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");


if ($action=="load_drop_down_buyer")
{
	//$data=explode('_',$data);
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
	exit();
}

if ($action=="style_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
	?>
	 <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
		
	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
		  
	</script>
	

 <input type="text" id="txt_po_id" />
 <input type="text" id="txt_po_val" />
     <?
	if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";

	
	$sql ="select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name"; 
	echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();	 
}

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print ($data[1]);
	?>
	 <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
		
	function js_set_value(id)
	{ //alert(id);
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];
	
		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	} 
		  
	</script>
      <input type="hidden" id="txt_po_id" />
     <input type="hidden" id="txt_po_val" />
 <?
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	if ($data[2]==0) $style=""; else $style=" and b.id in($data[2])";
	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";

	$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_name  $buyer_name $style"; 
	echo create_list_view("list_view", "Order Number,Job No, Year","150,100,50","450","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();
}

if ($action=="report_generate_des")// Item Description wise Search.
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($db_type==0) $group_field="group by po_id,trim_group,description order by  po_id,trim_group"; 
	else if($db_type==2) $group_field="group by id, po_id,trim_group,description,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_id,po_number,po_quantity,brand_sup_ref,req_qnty,cons_uom,rate,grouping,file_no order by po_id,trim_group";
	
	if($db_type==0) $group_field2="group by d.id order by b.id"; 
	else if($db_type==2) $group_field2="group by d.id,a.job_no_prefix_num, a.job_no, a.company_name, b.pub_shipment_date, b.po_quantity,c.id,d.cons, e.costing_per, a.buyer_name, a.style_ref_no, b.id,b.po_number, c.trim_group, c.description, c.brand_sup_ref,b.plan_cut,cc.order_uom,c.rate,b.grouping,b.file_no order by b.id";
	
	//group by d.id,a.job_no_prefix_num, a.job_no, a.company_name, b.pub_shipment_date, b.po_quantity,c.id,d.cons, e.costing_per, a.buyer_name, a.style_ref_no, b.id,b.po_number, c.trim_group, c.description, c.brand_sup_ref,b.plan_cut,cc.order_uom,c.rate order by b.id
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$txt_style=str_replace("'","",$txt_style);
	//$txt_order_no=str_replace("'","",$txt_order_no);
	//$txt_order_id=str_replace("'","",$txt_order_no_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$order_arr = return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	$item_des= return_library_array( "select id, item_description from inv_trims_entry_dtls",'id','item_description');
	
	ob_start();	
	?>
    <div style="width:1490px; margin-left:5px;">
    
        <table width="1530" cellspacing="0" cellpadding="0" border="0" rules="all"  >
            <tr class="form_caption">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1467" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="70">Order No</th>
                <th width="60">Buyer Name</th>
                <th width="80">Style</th>
                <th width="50">RMG Qty.</th>
                <th width="65">RMG Qty(Dzn)</th>
				<th width="60">Prod. Id</th>
                <th width="130">Item Group</th>
				<th width="150">Descp.</th>
                <th width="40">UOM</th>
                <th width="70">Req. Qty</th>
                <th width="70">Recv. Qty</th>
                <th width="70">Recv. Value</th>
                <th width="70">Yet to Rev.</th>
                <th width="70">Issue Qty.</th>
                <th width="70">Left Over</th>
                <th width="30">Rate</th>
                <th width="70">Left Over Val.</th>
             	<th width="70">Int. Ref</th>
                <th>File No</th>
            </thead>
        </table>
 <div style="width:1467px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1450" class="rpt_table"  id="tbl_header" >
           <tbody>
		   <?	
		   		if($db_type==0) $recv_grpby="group by b.po_breakdown_id,a.item_group_id,c.item_description"; 
				else if($db_type==2) $recv_grpby="group by b.po_breakdown_id,a.item_group_id,c.item_description,c.id";
				
				if($db_type==0) $issue_grpby="group by b.po_breakdown_id,a.item_group_id,c.item_description"; 
				else if($db_type==2) $issue_grpby="group by b.po_breakdown_id,a.item_group_id,c.item_description,c.id,a.rate";
				
				if($db_type==0) $po_search="and FIND_IN_SET(po_number,'$txt_order_no')"; 
				else if($db_type==2) $po_search=" and po_number in ('$txt_order_no')";
				else $po_search="";
				if($db_type==0) $po_search="and FIND_IN_SET(b.id,$txt_order_id)"; 
				else if($db_type==2) $po_search=" and b.id in (".$txt_order_id.")";
				else $po_id_search="";
				
				if($db_type==2)
				{
				if(str_replace("'","",$txt_order_no_id)!="") $order_cond=" and b.id in(".str_replace("'","",$txt_order_no_id).")";
				else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and po_number in('".str_replace("'","",$txt_order_no)."')"; 
    			else $order_cond="";
				}
				else if($db_type==0)
				{
				if(str_replace("'","",$txt_order_no_id)!="") $order_cond=" and b.id in(".str_replace("'","",$txt_order_no_id).")";
				else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and po_number in('".str_replace("'","",$txt_order_no)."')"; 
    			else $order_cond="";
				}
				if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and buyer_name=$cbo_buyer";
				
				//txt_int_ref_no*txt_file_no
				if (str_replace("'","",$txt_int_ref_no)=="") $int_ref_no_con=""; else $int_ref_no_con="and b.grouping=$txt_int_ref_no";
				if (str_replace("'","",$txt_file_no)=="") $file_no_con=""; else $file_no_con="and b.file_no=$txt_file_no";
				//echo  $int_ref_no_con."====".$file_no_con;
				
				if(str_replace("'","",$txt_style)!="") $style=" and a.id in(".str_replace("'","",$txt_style).")"; else $style="";
				if($db_type==0) $pub_ship_date_from=change_date_format($date_from,'yyyy-mm-dd');
				if($db_type==2) $pub_ship_date_from=change_date_format($date_from,'','',1);
				if($db_type==0) $pub_ship_date_to=change_date_format($date_to,'yyyy-mm-dd');
				if($db_type==2) $pub_ship_date_to=change_date_format($date_to,'','',1);
				if( $date_from==0 && $date_to==0 ) $pub_date=""; else $pub_date= "  and pub_shipment_date between '".$pub_ship_date_from."' and '".$pub_ship_date_to."'";
				$i=1;
				$receive_qty_array=array();
				//$rate_qty_recv=array();
				$receive_prod_array=array();
				$issue_qty_array=array();
				$description_array=array();
				//$rate_qty_issue=array();
				$left_over=0;
				$receive_qty_data=sql_select("select b.po_breakdown_id,c.id as prod_id,c.item_description, a.item_group_id,sum(b.quantity) as quantity,avg(a.rate) as rate  from  inv_receive_master d,inv_trims_entry_dtls a ,order_wise_pro_details b,product_details_master c where d.id=a.mst_id and a.trans_id=b.trans_id and b.trans_type=1 and b.entry_form=24 and a.prod_id=c.id and d.company_id=$cbo_company and c.id=b.prod_id and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_grpby");
				$x=0;
				foreach($receive_qty_data as $row)
				{
					$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['receive_qty']+=$row[csf('quantity')];
					$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['rate']+=$row[csf('rate')];
					$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['product']=$row[csf('prod_id')];
					$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['desc'].=$row[csf('item_description')].",";
					$total_recv_value=0;
					$x++;
				}
				$issue_qty_data=sql_select("select b.po_breakdown_id,c.item_description, a.item_group_id,sum(b.quantity) as quantity  from inv_issue_master d, inv_trims_issue_dtls a , order_wise_pro_details b,product_details_master c where d.id=a.mst_id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and a.status_active=1 and a.prod_id=c.id and  d.company_id=$cbo_company and c.id=b.prod_id and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $recv_grpby");
				foreach($issue_qty_data as $row)
				{
					$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]['issue_qty']=$row[csf('quantity')];
					$descrp=$issue_qty_array[$row[csf('item_description')]];
				}
				 $sql_trim = "select b.po_breakdown_id,a.item_group_id,
		 			sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
		 		 	sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
					
					sum(case when b.entry_form in(78) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
		 		 	sum(case when b.entry_form in(78)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive
			from 			
				product_details_master a, order_wise_pro_details b, inv_transaction c
			where  
				a.id=b.prod_id and b.trans_id=c.id and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(78,73,49)  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id";	
			$data_array=sql_select($sql_trim);
			$trims_qty_array=array();
			foreach($data_array as $row)
				{
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['issue_return_qty']=$row[csf('issue_return_qty')];
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['recv_return_qty']=$row[csf('recv_return_qty')];
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_transfer_issue']=$row[csf('item_transfer_issue')];
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_transfer_receive']=$row[csf('item_transfer_receive')];
				}
				
				$sql="
				Select id,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_id,po_number,po_quantity,trim_group as trim_group,description,brand_sup_ref,req_qnty,cons_uom,rate,grouping,file_no 
				from (
				select  d.id as id,
				a.job_no_prefix_num,
				a.job_no,
				a.company_name,
				b.pub_shipment_date,
				b.grouping,
				b.file_no,
				b.po_quantity,
				c.id as wo_pre_cost_trim_cost_dtls,
				d.cons,
				e.costing_per,
				a.buyer_name,
				a.style_ref_no,
				b.id as po_id,
				b.po_number,
				c.trim_group,
				c.description,
				c.brand_sup_ref,
				CASE e.costing_per WHEN 1 
				THEN round(((d.cons/12)*b.plan_cut),4) WHEN 2 
				THEN round(((d.cons/1)*b.plan_cut),4)  WHEN 3 
				THEN round(((d.cons/24)*b.plan_cut),4) WHEN 4 
				THEN round(((d.cons/36)*b.plan_cut),4) WHEN 5 
				THEN round(((d.cons/48)*b.plan_cut),4) ELSE 0 END as req_qnty,
				cc.order_uom as cons_uom,
				
				round((c.rate),8) as rate 
				from wo_po_details_master a, 
				wo_po_break_down b ,
				wo_pre_cost_mst e,
				wo_pre_cost_trim_cost_dtls c,
				lib_item_group cc, 
				wo_pre_cost_trim_co_cons_dtls d 
				
				where a.job_no=b.job_no_mst and  
				a.job_no=c.job_no and 
				a.job_no=e.job_no and  
				a.job_no=d.job_no and 
				c.id=d.wo_pre_cost_trim_cost_dtls_id and 
				b.id=d.po_break_down_id and 
				cc.id=c.trim_group and  
				a.company_name=$cbo_company 
				$buyer_id 
				$pub_date 
				$int_ref_no_con
				$file_no_con
				$order_cond 
				$style  
				$group_field2
				) m  
				where  company_name=$cbo_company $pub_date  
				$group_field";
				//echo $sql;
				$sl=1; $i=1; $k=0;
				$total_left_value=0;
				$total_rec_value=0;$left_val=0;$total_left=0;
				$order_id_array=array();
				$nameArray=sql_select( $sql );$data_check=array();
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					//unset($data_check);
					$desc=explode(",",substr($receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['desc'],0,-1));
					foreach($desc as $description)
					{
						
						$issue_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_return_qty'];
						$recv_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['recv_return_qty'];
						$transfer_out_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_issue'];
						$transfer_in_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_receive'];
						if($data_check[$selectResult[csf('po_id')]][$selectResult[csf('po_id')]][$description]=="")
						{
							$data_check[$selectResult[csf('po_id')]][$selectResult[csf('po_id')]][$description]=$description;
							
							
							?> 
							<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								 <?
							if (!in_array($selectResult[csf('po_number')],$order_id_array) )
								{
									$k++;
							   ?><td width="30"> <? echo $k;?> </td>
								<td width="70"><p> <? echo $order_arr[$selectResult[csf('po_id')]];?></p></td>
								<td width="60"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
								<td width="80"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
								<td width="50" align="right"> <? echo number_format($selectResult[csf('po_quantity')]);?> </td>
								<? 
								$order_id_array[]=$selectResult[csf('po_number')];
								
								}
								else
								{
								?>
								<td width="30"> <? // echo $i;?> </td>
								<td width="70"><p> <? //echo $order_arr[$selectResult[csf('po_id')]];?></p></td>
								<td width="60"><p><? //echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
								<td width="80"><p><? //echo $selectResult[csf('style_ref_no')];?></p></td>
								<td width="50" align="right"> <? //echo $selectResult[csf('po_quantity')];?> </td>	
								<?
								
								} 
							?>
								<td width="65" align="right"><? echo number_format($selectResult[csf('po_quantity')]/12,0);?> </td>
								<td width="60" title="Prod. Id" align="center"><? echo $receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description][product]; //$receive_prod_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][prod_id]; ?></td>
								<td width="130" align="left"><p><? echo $trim_group[$selectResult[csf('trim_group')]]; ?></p></td>
								<td width="150" title="Item Description" align="center"><p> <? echo $description; //echo $selectResult[csf('description')];   ?></p></td>
								<td width="40" align="center"><p><? echo  $unit_of_measurement[$selectResult[csf('cons_uom')]]; ?></p></td>
								<td width="70" title="Req. Qty" align="right"><? echo number_format($selectResult[csf('req_qnty')],2); ?> </td>
								<td width="70"  align="right" title="Received Qty"><a href='#report_details' onClick="openmypage_des('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('trim_group')]; ?>','<? echo  $description;?>','receive_des_popup');"><? echo number_format($receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['receive_qty']+$transfer_in_qty+$issue_return_qty,2); ?> </a>  </td>
								<td width="70" title="<? echo "Rate: ".$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description][rate]; ?>" align="right"> <? $rec_value= ($receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['receive_qty']+$transfer_in_qty+$issue_return_qty)* $receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['rate']; echo $rec_value; ?>  </td>
								<td width="70" title="yet" align="right"> <? $yet_recv=$selectResult[csf('req_qnty')]-($receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['receive_qty']+$transfer_in_qty+$issue_return_qty); echo number_format($yet_recv,2); ?></td>
								<td width="70" align="right"><a href='#report_details' onClick="openmypage_des('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('trim_group')]; ?>','<? echo  $description ;?>','issue_des_popup');">
								<? echo number_format($issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['issue_qty']+$recv_return_qty+$transfer_out_qty,2); ?></a>   
								</td>
								<td width="70" title="Left Over"><? $left_over=($receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['receive_qty']+$transfer_in_qty+$issue_return_qty)-($issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['issue_qty']+$recv_return_qty+$transfer_out_qty); echo number_format($left_over,2); ?> </td>
								<td width="30" title="Rate" align="right" > <? echo  number_format($rate=$rec_value/($receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['receive_qty']+$transfer_in_qty+$issue_return_qty),2); ?></td>
								<td width="70" title="Left Over Value" align="right"> <?  $total_left=$left_over*$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['rate']; echo  number_format($total_left,2); $left_val+=$total_left;  ?> </td>
								
								<td width="70" title="Internal Ref/Grouping" align="left"><? echo $selectResult[csf('grouping')];?></td>
								<td title="File No" align="left"><? echo $selectResult[csf('file_no')];?></td>
							</tr>
							<?
                            $i++;
                            
                            $total_rec=($receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['receive_qty']+$transfer_in_qty+$issue_return_qty)* $receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$description]['rate'];
                            $total_rec_value+=$total_rec;
						}
					 
						
				 }
			}
			?>     
           <tr>
               <td colspan="12" align="right"><b> Sum </b></td>
               <td align="right"><b><?  echo number_format($total_rec_value,2); ?></b> &nbsp;</td>
               <td>&nbsp; </td>
               <td>&nbsp; </td>
               <td>&nbsp; </td>
               <td align="right"> <b> Sum </b></td>
               <td align="right"><b><? echo number_format($left_val,2); ?></b>&nbsp;</td>
               <td>&nbsp; </td>
               <td>&nbsp; </td>
           </tr>    
        </tbody>
    </table>
   </div>
  </div>
<?
exit();
}

if ($action=="report_generate")//Item Group Wise Search.
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	//$txt_style_id=str_replace("'","",$txt_style);
	$txt_style=str_replace("'","",$txt_style);

	//$txt_style=str_replace("'","",$txt_style);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_no_id);
	$cbo_search_date=str_replace("'","",$cbo_search_date);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	
	if($db_type==0) $group_field="group by po_id,trim_group order by  po_id,trim_group"; 
	else if($db_type==2) $group_field="group by  po_id,trim_group,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_id,po_number,po_quantity,brand_sup_ref,req_qnty,cons_uom order by po_id,trim_group";

	if($db_type==0) $group_field2="group by d.id order by b.id"; 
	else if($db_type==2) $group_field2="group by d.id,a.job_no_prefix_num, a.job_no, a.company_name, b.pub_shipment_date, b.po_quantity,d.cons, e.costing_per, a.buyer_name, a.style_ref_no, b.id,b.po_number, c.trim_group, c.brand_sup_ref,b.plan_cut,cc.order_uom order by b.id";
	

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$order_arr = return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	//print_r( $order_arr);die;
	ob_start();	
	?>
    <fieldset style="width:1670px;">
        <table width="1670">
            <tr class="form_caption">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1670" class="rpt_table" >
            <thead>
                <th width="30">SL </th>
                <th width="100">Order No</th>
                <th width="80">Buyer Name</th>
                <th width="80">Style</th>
                <th width="80">RMG Qty</th>
                <th width="80">RMG Qty(Dzn)</th>
                <th width="200">Item Group</th>
                <th width="40">UOM</th>
                <th width="80">Req. Qty</th>
                <th width="80">WO Qty</th>
                 <th width="80">WO Value</th>
                <th width="80">Recv. Qty</th>
                <th width="80">Recv. Value</th>
                <th width="80">Yet to Rev.</th>
                <th width="80">Issue Qty</th>
                 <th width="80">Issue Value</th>
                <th width="80">Left Over</th>
                <th width="30">Rate</th>
                <th width="60">Left Over Val.</th>
                <th width="60">Int. Ref</th>
                <th>File No</th>
            </thead>
        </table>
        <div style="width:1690px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1670px" class="rpt_table" id="tbl_issue_status" >
           <tbody>
		   <?
				// if ($cbo_company==0) $cbo_company1=""; else $cbo_company1="  company_name=$cbo_company";
				if($db_type==0) $po_search="and FIND_IN_SET(a.po_number,'$txt_order_no')"; 
				else if($db_type==2) $po_search=" and a.po_number in ('".$txt_order_no."')";
				else $po_search="";
				if($db_type==0) $po_id_search="and FIND_IN_SET(b.id,$txt_order_id)"; 
				else if($db_type==2) $po_id_search=" and a.id in ('".$txt_order_id."')";
				else $po_id_search="";
				if($db_type==2)
				{
				if(str_replace("'","",$txt_order_no_id)!="") $order_cond=" and a.id in(".str_replace("'","",$txt_order_no_id).")";
				else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and a.po_number in('".str_replace("'","",$txt_order_no)."')"; 
    			else $order_cond="";
				}
				else if($db_type==0)
				{
				if(str_replace("'","",$txt_order_no_id)!="") $order_cond=" and a.id in(".str_replace("'","",$txt_order_no_id).")";
				else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and a.po_number in('".str_replace("'","",$txt_order_no)."')"; 
    			else $order_cond="";
				}
				if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and b.buyer_name=$cbo_buyer";
				
				//txt_int_ref_no*txt_file_no
				if (str_replace("'","",$txt_int_ref_no)=="") $int_ref_no_con=""; else $int_ref_no_con="and a.grouping=$txt_int_ref_no";
				if (str_replace("'","",$txt_file_no)=="") $file_no_con=""; else $file_no_con="and a.file_no=$txt_file_no";
				//echo  $int_ref_no_con."====".$file_no_con;
				
				//if ($txt_order_no=="") $order_no=""; else $order_no=$po_search;
				//if($txt_order_id="") $order_cond=""; else  $order_cond="  and FIND_IN_SET(b.id,$txt_order_id)"; 
				//if ($txt_style_id=="") $style=""; else $style="  and  FIND_IN_SET( style_ref_no,'$txt_style_id' )";
				if(str_replace("'","",$txt_style)!="") $style=" and b.id in(".str_replace("'","",$txt_style).")"; else $style="";
				if($db_type==0) $pub_ship_date_from=change_date_format($date_from,'yyyy-mm-dd');
				if($db_type==2) $pub_ship_date_from=change_date_format($date_from,'','',1);
				if($db_type==0) $pub_ship_date_to=change_date_format($date_to,'yyyy-mm-dd');
				if($db_type==2) $pub_ship_date_to=change_date_format($date_to,'','',1);
				if( $date_from==0 && $date_to==0 ) $pub_date=""; else $pub_date= "  and a.pub_shipment_date between '".$pub_ship_date_from."' and '".$pub_ship_date_to."'";
				$i=1;
				$receive_qty_array=array();
				$issue_qty_array=array();
				$left_over=0;
				$wo_qty_array=array();
				 $req_qty_array=array();
				$r_sql="select d.cons,d.po_break_down_id as po_id,c.trim_group from wo_pre_cost_trim_cost_dtls c,wo_pre_cost_trim_co_cons_dtls d where  c.id=d.wo_pre_cost_trim_cost_dtls_id and c.status_active=1 and c.is_deleted=0 group by d.po_break_down_id,c.trim_group,d.cons ";
				$dataArray_req=sql_select($r_sql);
				foreach($dataArray_req as $row_req )
				{
					$req_qty_array[$row_req[csf('po_id')]][$row_req[csf('trim_group')]]['cons']=$row_req[csf('cons')];
				}   //var_dump($req_qty_array);die;
				
				$wo_sql="select  b.po_break_down_id as po_id, b.trim_group,sum(b.wo_qnty) as wo_qnty, sum(amount) as amount from 
				wo_booking_mst a, wo_booking_dtls b 
				where a.item_category=4 and a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and 
				a.company_id=$cbo_company group by b.po_break_down_id,b.trim_group";
				//echo $wo_sql;die;
				$dataArray=sql_select($wo_sql);
				foreach($dataArray as $row )
				{
					$wo_qty_array[$row[csf('po_id')]][$row[csf('trim_group')]]['wo_qnty']=$row[csf('wo_qnty')];
					$wo_qty_array[$row[csf('po_id')]][$row[csf('trim_group')]]['amount']=$row[csf('amount')];
				}   //var_dump($wo_qty_array);die;
							
				$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity , avg(a.cons_rate) as rate  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and c.company_id=$cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");
				foreach($receive_qty_data as $row)
				{
					//$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['receive_qty']=$row[csf('quantity')];
					$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
					//$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][value]+=$row[csf('rate')];
					$total_recv_value=0;
				}
				
				$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity , avg(a.rate) as rate from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and d.company_id=$cbo_company and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id");
				foreach($issue_qty_data as $row)
				{
					$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['issue_qty']=$row[csf('quantity')];
					$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
					//$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][value]+=$row[csf('rate')];
				}
				  $sql_trim = "select b.po_breakdown_id, a.item_group_id,avg(c.cons_rate) as cons_rate,
		 			sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
		 		 	sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
					
					sum(case when b.entry_form in(78) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
		 		 	sum(case when b.entry_form in(78)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive
			from 			
				product_details_master a, order_wise_pro_details b, inv_transaction c
			where  
				a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and b.entry_form in(78,73,49)  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id";	
			$data_array=sql_select($sql_trim);
			$trims_qty_array=array();
			foreach($data_array as $row)
				{
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['issue_return_qty']=$row[csf('issue_return_qty')];
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['recv_return_qty']=$row[csf('recv_return_qty')];
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_transfer_issue']=$row[csf('item_transfer_issue')];
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_transfer_receive']=$row[csf('item_transfer_receive')];
					$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['cons_rate']=$row[csf('cons_rate')];
				}
								
				/*$sql="
				Select buyer_name,job_no_prefix_num,job_no,style_ref_no,po_id,po_number,po_quantity,trim_group as trim_group,brand_sup_ref,req_qnty,cons_uom 
				from (
				select  d.id as id,a.job_no_prefix_num,a.job_no,a.company_name,b.pub_shipment_date,	b.po_quantity,c.id as 
				wo_pre_cost_trim_cost_dtls,d.cons,e.costing_per,a.buyer_name,a.style_ref_no,b.id as po_id,b.po_number,c.trim_group,c.brand_sup_ref,
				CASE e.costing_per WHEN 1 
				THEN round(((d.cons/12)*b.plan_cut),4) WHEN 2 
				THEN round(((d.cons/1)*b.plan_cut),4)  WHEN 3 
				THEN round(((d.cons/24)*b.plan_cut),4) WHEN 4 
				THEN round(((d.cons/36)*b.plan_cut),4) WHEN 5 
				THEN round(((d.cons/48)*b.plan_cut),4) ELSE 0 END as req_qnty,
				cc.order_uom as cons_uom
				from wo_po_details_master a, 
				wo_po_break_down b ,
				wo_pre_cost_mst e,
				wo_pre_cost_trim_cost_dtls c,
				lib_item_group cc, 
				wo_pre_cost_trim_co_cons_dtls d 
				
				where a.job_no=b.job_no_mst and  
				a.job_no=c.job_no and 
				a.job_no=e.job_no and  
				a.job_no=d.job_no and 
				c.id=d.wo_pre_cost_trim_cost_dtls_id and 
				b.id=d.po_break_down_id and 
				cc.id=c.trim_group and  
				a.company_name=$cbo_company 
				$buyer_id 
				$pub_date 
				$order_cond 
				$style  
				$group_field2
				) m  
				where  company_name=$cbo_company $pub_date  
				$group_field
				
				";
			*/
			
				$sql="select
				a.id as po_id,
				b.job_no,
				a.po_number,
				a.pub_shipment_date,
				sum(distinct a.po_quantity) as po_quantity,
				b.style_ref_no,
				b.buyer_name,
				p.item_group_id as trim_group,
				p.unit_of_measure as cons_uom,
				sum(CASE WHEN o.entry_form ='24'  THEN o.quantity ELSE 0 END) AS receive_qty,
				a.grouping,
				a.file_no
				from
						wo_po_break_down a,wo_po_details_master b, inv_transaction t,order_wise_pro_details o, product_details_master p,inv_receive_master r
				where 
						a.job_no_mst=b.job_no and o.po_breakdown_id=a.id and t.id=o.trans_id and o.entry_form in(24,78) and r.id=t.mst_id and t.prod_id=p.id 
						 and b.company_name=$cbo_company  $buyer_id  $style $order_cond $pub_date $int_ref_no_con $file_no_con
				 group by a.id,a.po_number, a.pub_shipment_date, b.style_ref_no, b.buyer_name, p.item_group_id,p.unit_of_measure,b.job_no,a.grouping,a.file_no
				order by a.po_number,p.item_group_id";
				//echo $sql;
				$sl=1; $i=1; $k=0;
				$total_left_value=0;
				$total_rec_value=0;$left_val=0;$total_left=$tot_issue_amount=$tot_wo_amount=0;
				$order_id_array=array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						$wo_qty=$wo_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['wo_qnty'];
						$wo_amount=$wo_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['amount'];
						
						$dzn_qnty=0;
					if($costing_per_id_library[$selectResult[csf('job_no')]]==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per_id_library[$selectResult[csf('job_no')]]==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per_id_library[$selectResult[csf('job_no')]]==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per_id_library[$selectResult[csf('job_no')]]==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					//$dzn_qnty=$row[csf('ratio')]*$dzn_qnty;
					
					$cons_data=$req_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['cons'];
					$req_qty=($selectResult[csf('po_quantity')]/$dzn_qnty)*$cons_data;
					 $issue_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_return_qty'];
					$recv_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['recv_return_qty'];
					$transfer_out_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_issue'];
					$transfer_in_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_receive'];
					
					$cons_rate=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['cons_rate'];
					$recv_return_amt=$recv_return_qty*$cons_rate;
					$transfer_out_amt=$transfer_out_qty*$cons_rate; 
				?> 
					<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						
                         <?
					if (!in_array($selectResult[csf('po_number')],$order_id_array) )
						{ $k++;
				  ?>	<td width="30"> <? echo $k;?> </td>
						<td width="100" align="center"><p> <? echo $order_arr[$selectResult[csf('po_id')]];?></p></td>
						<td width="80" align="center"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
						<td width="80" align="center"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
						<td width="80" align="right"> <? echo number_format($selectResult[csf('po_quantity')]);?></td>
                        <? 
						$order_id_array[]=$selectResult[csf('po_number')];
						}
						else
						{
						?>
						<td width="30"> <? // echo $i;?> </td>
						<td width="100"><p> <? //echo $order_arr[$selectResult[csf('po_id')]];?></p></td>
						<td width="80"><p><? //echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
						<td width="80"><p><? //echo $selectResult[csf('style_ref_no')];?></p></td>
						<td width="80" align="right"> <? //echo $selectResult[csf('po_quantity')];?> </td>	
						<?
                        }
                        					
					?>
						<td width="80" align="right"><? echo number_format($selectResult[csf('po_quantity')]/12,0); ?> </td>
						<td width="200" align="left"><p><? echo $trim_group[$selectResult[csf('trim_group')]]; ?></p></td>
						<td width="40" align="center"><p> <? echo $unit_of_measurement[$selectResult[csf('cons_uom')]];   ?></p></td>
						<td width="80" title="Req. Qnty" align="right"><? echo number_format($req_qty,2); //$total_req+=$selectResult[csf('req_qnty')];?> </td>
						<td width="80" title="WO Qnty" align="right"><? echo number_format($wo_qty,2); //$total_req+=$selectResult[csf('req_qnty')];?> </td>
                        <td width="80" title="WO Value" align="right"><? echo number_format($wo_amount,2); //$total_req+=$selectResult[csf('req_qnty')];?> </td>
                        <td width="80"  align="right"><a href='#report_details' onClick="openmypage('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('trim_group')]; ?>','receive_popup');"><?  echo number_format($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty,2); ?> </a>   </td>
						<td width="80" title="<? echo "Rate: ".$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate']; ?>" align="right"> <? $rec_value=($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty)*$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate']; echo number_format($rec_value,2); ?>  </td>
						<td width="80" title="yet" align="right"> <? $yet_recv=$req_qty-($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty); echo number_format($yet_recv,2); ?> </td>
						<td width="80" align="right">
                        <a href='#report_details' onClick="openmypage('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('trim_group')]; ?>','issue_popup');">
						<? echo number_format($issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_qty']+$recv_return_qty+$transfer_out_qty,2); ?></a>
						 
						</td>
                        <td width="80" align="right">
                        <a href='#report_details' onClick="openmypage('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('trim_group')]; ?>','issue_popup');">
						<? //echo number_format($issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_qty']+$recv_return_qty+$transfer_out_qty,2); ?></a> <? 
							$issue_rate=$issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate'];
							$issue_qty=$issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_qty'];
							$issue_amount=($issue_qty*$issue_rate)+$transfer_out_qty+$recv_return_amt;
						echo number_format($issue_amount,2);?>
						 
						</td>
						<td width="80" title="Left Over" align="right"><? $left_over=($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty)-($issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_qty']+$recv_return_qty+$transfer_out_qty); echo number_format($left_over,2); ?> </td>
						<td width="30" title="Rate" align="right" ><?
						$avg_rate=$rec_value/($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty);
						 echo number_format($rec_value/($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty),2); ?> </td>
						<td width="60" title="Left Over Value" align="right"> <?  $total_left=$left_over*$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate']; echo  number_format($total_left,2); $left_val+=$total_left;  ?> </td>
                        <td width="60" title="Internal Ref/Grouping" align="left"> <? echo $selectResult[csf('grouping')]; ?> </td>
                        <td  title="File No" align="left"> <? echo $selectResult[csf('file_no')]; ?> </td>
					</tr>
				  <?
				 $i++;
				 
				 $total_rec=($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty)*$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate'];
				 $total_rec_value+=$total_rec;
				  $tot_wo_amount+=$wo_amount;
				   $tot_issue_amount+=$issue_amount;
			}
			?>
           
        </tbody>
         <tfoot>     
           <tr>
               
               <th colspan="10" align="right"><b> Sum </b> </th>
                <th align="right"> <b><?  echo number_format($tot_wo_amount,2); ?> </b> &nbsp;</th>
               <th align="right"> <b><?  //echo number_format($total_rec_value,2); ?> </b> &nbsp;</th>
               <th align="right"> <b><?  echo number_format($total_rec_value,2); ?> </b> &nbsp;</th>
               <th>&nbsp; </th>
               <th>&nbsp; </th>
                 <th align="right"> <b><?  echo number_format($tot_issue_amount,2); ?> </b> &nbsp;</th>
               <th>&nbsp; </th>
               <th align="right"> <b> Sum </b></th>
               <th align="right" > <b><? echo number_format($left_val,2); ?> </b> &nbsp;</th>
               <th>&nbsp; </th>
               <th>&nbsp; </th>
           </tr> 
           </tfoot>   
    </table>
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

if ($action=="report_generate_color_size")//Item Color & Size Wise Search.
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	//$txt_style_id=str_replace("'","",$txt_style);
	$txt_style=str_replace("'","",$txt_style);
	
	//$txt_style=str_replace("'","",$txt_style);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_no_id);
	$cbo_search_date=str_replace("'","",$cbo_search_date);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	
	if($db_type==0) $group_field="group by po_id,trim_group,description order by  po_id,trim_group"; 
	else if($db_type==2) $group_field="group by  po_id,trim_group,buyer_name,job_no_prefix_num,job_no,style_ref_no,po_id,po_number,po_quantity,brand_sup_ref,req_qnty,cons_uom order by po_id,trim_group";

	if($db_type==0) $group_field2="group by d.id order by b.id"; 
	else if($db_type==2) $group_field2="group by d.id,a.job_no_prefix_num, a.job_no, a.company_name, b.pub_shipment_date, b.po_quantity,d.cons, e.costing_per, a.buyer_name, a.style_ref_no, b.id,b.po_number, c.trim_group, c.brand_sup_ref,b.plan_cut,cc.order_uom order by b.id";
	

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$order_arr = return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	//print_r( $order_arr);die;
	ob_start();	
	?>
    <fieldset style="width:1410px;">
        <table width="1410">
            <tr class="form_caption">
                <td colspan="18" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="18" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1410" class="rpt_table" >
            <thead>
                <th width="30">SL </th>
                <th width="100">Order No</th>
                <th width="80">Buyer Name</th>
                <th width="80">Style</th>
                <th width="80">RMG Qty</th>
                <th width="80">RMG Qty(Dzn)</th>
                <th width="200">Item Group</th>
                <th width="100">Item Color</th>
                <th width="50">Item Size</th>
                <th width="40">UOM</th>
              
                <th width="80">Recv. Qty</th>
                <th width="80">Recv. Value</th>
              
                <th width="80">Issue Qty.</th>
                <th width="80">Left Over</th>
                <th width="30">Rate</th>
                <th width="60">Left Over Val.</th>
                <th width="60">Int. Ref</th>
                <th>File No</th>
            </thead>
        </table>
        <div style="width:1430px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1410px" class="rpt_table" id="tbl_issue_status" >
           <tbody>
		   <?
				// if ($cbo_company==0) $cbo_company1=""; else $cbo_company1="  company_name=$cbo_company";
				if($db_type==0) $po_search="and FIND_IN_SET(a.po_number,'$txt_order_no')"; 
				else if($db_type==2) $po_search=" and a.po_number in ('".$txt_order_no."')";
				else $po_search="";
				if($db_type==0) $po_id_search="and FIND_IN_SET(a.id,$txt_order_id)"; 
				else if($db_type==2) $po_id_search=" and a.id in ('".$txt_order_id."')";
				else $po_id_search="";
				if($db_type==2)
				{
					if(str_replace("'","",$txt_order_no_id)!="") $order_cond=" and a.id in(".str_replace("'","",$txt_order_no_id).")";
					else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and a.po_number in('".str_replace("'","",$txt_order_no)."')"; 
					else $order_cond="";
				}
				else if($db_type==0)
				{
					if(str_replace("'","",$txt_order_no_id)!="") $order_cond=" and a.id in(".str_replace("'","",$txt_order_no_id).")";
					else if(str_replace("'","",$txt_order_no)!="") $order_cond=" and a.po_number in('".str_replace("'","",$txt_order_no)."')"; 
					else $order_cond="";
				}
				if ($cbo_buyer==0) $buyer_id=""; else $buyer_id=" and b.buyer_name=$cbo_buyer";
				
				//txt_int_ref_no*txt_file_no
				if (str_replace("'","",$txt_int_ref_no)=="") $int_ref_no_con=""; else $int_ref_no_con="and a.grouping=$txt_int_ref_no";
				if (str_replace("'","",$txt_file_no)=="") $file_no_con=""; else $file_no_con="and a.file_no=$txt_file_no";
				//echo  $int_ref_no_con."====".$file_no_con;
				
				//if ($txt_order_no=="") $order_no=""; else $order_no=$po_search;
				//if($txt_order_id="") $order_cond=""; else  $order_cond="  and FIND_IN_SET(b.id,$txt_order_id)"; 
				//if ($txt_style_id=="") $style=""; else $style="  and  FIND_IN_SET( style_ref_no,'$txt_style_id' )";
				if(str_replace("'","",$txt_style)!="") $style=" and b.id in(".str_replace("'","",$txt_style).")"; else $style="";
				if($db_type==0) $pub_ship_date_from=change_date_format($date_from,'yyyy-mm-dd');
				if($db_type==2) $pub_ship_date_from=change_date_format($date_from,'','',1);
			
				if($db_type==0) $pub_ship_date_to=change_date_format($date_to,'yyyy-mm-dd');
				if($db_type==2) $pub_ship_date_to=change_date_format($date_to,'','',1);
				if( $date_from==0 && $date_to==0 ) $pub_date=""; else $pub_date= "  and a.pub_shipment_date between '".$pub_ship_date_from."' and '".$pub_ship_date_to."'";
				$i=1;
				$receive_qty_array=array();
				$issue_qty_array=array();
				$left_over=0;
				
				$receive_qty_data=sql_select("select b.po_breakdown_id, a.item_group_id,sum(b.quantity) as quantity , avg(a.rate) as rate,a.order_uom,a.item_color,a.item_size  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and c.company_id=$cbo_company and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id,a.item_color,a.item_size,a.order_uom");
				foreach($receive_qty_data as $row)
				{
					$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['rate']=$row[csf('rate')];
					//$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_color']=$row[csf('item_color')];
					//$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_size']=$row[csf('item_size')];
					$receive_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['order_uom']=$row[csf('order_uom')];
				}
				
				$issue_qty_data=sql_select("select b.po_breakdown_id,a.item_color_id,a.item_size, a.item_group_id,sum(b.quantity) as quantity  from  inv_issue_master d,product_details_master p,inv_trims_issue_dtls a , order_wise_pro_details b where a.mst_id=d.id and a.prod_id=p.id and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and d.company_id=$cbo_company and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id, a.item_group_id,a.item_color_id,a.item_size");
				foreach($issue_qty_data as $row)
				{
					$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$row[csf('item_size')]]['issue_qty']=$row[csf('quantity')];
					//$issue_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['id']=$row[csf('id')];
				}
				$sql_trim = "select b.po_breakdown_id,a.item_group_id,
		 			sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
		 		 	sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
					
					sum(case when b.entry_form in(78) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
		 		 	sum(case when b.entry_form in(78)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive
			from 			
				product_details_master a, order_wise_pro_details b, inv_transaction c
			where  
				a.id=b.prod_id and b.trans_id=c.id and a.id=b.prod_id  and a.item_category_id=4 and b.entry_form in(78,73,49)  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id";	
			$data_array=sql_select($sql_trim);
			$trims_qty_array=array();
			foreach($data_array as $row)
			{
				$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['issue_return_qty']=$row[csf('issue_return_qty')];
				$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['recv_return_qty']=$row[csf('recv_return_qty')];
				$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_transfer_issue']=$row[csf('item_transfer_issue')];
				$trims_qty_array[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]['item_transfer_receive']=$row[csf('item_transfer_receive')];
			}
				
			 $sql="select
				a.id as po_id,
				a.po_number,
				a.pub_shipment_date,
				p.item_size,
				p.item_color,
				sum(distinct a.po_quantity) as po_quantity,
				b.style_ref_no,
				b.buyer_name,
				p.item_group_id as trim_group,
				sum(o.quantity)  AS receive_qty,
				a.grouping,
				a.file_no
				from
						wo_po_break_down a,wo_po_details_master b, inv_transaction t,order_wise_pro_details o, product_details_master p,inv_receive_master r
				where 
						a.job_no_mst=b.job_no and o.po_breakdown_id=a.id and t.id=o.trans_id and o.entry_form in(24,78) and r.id=t.mst_id and t.prod_id=p.id 
						 and b.company_name=$cbo_company  $buyer_id  $style $order_cond $pub_date $int_ref_no_con $file_no_con
				 group by a.id,a.po_number, a.pub_shipment_date, b.style_ref_no,p.item_size,p.item_color, b.buyer_name, p.item_group_id,a.grouping,a.file_no
				order by a.po_number,p.item_group_id"; 
				//echo $sql;
				$sl=1; $i=1; $k=0;
				$total_left_value=0;
				$total_rec_value=0;$left_val=0;$total_left=0;
				$order_id_array=array();
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						$item_color=$selectResult[csf('item_color')];
						//$item_size=$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_size'];
						$item_size=$selectResult[csf('item_size')];
						$order_uom=$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['order_uom'];
						$issue_qty=	$issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]][$item_color][$item_size]['issue_qty'];//$issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_qty'];
						$issue_proprotionat_id=$issue_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['id'];
						$recv_number=$selectResult[csf('recv_number')];
						
					$issue_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_return_qty'];
					$recv_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['recv_return_qty'];
					$transfer_out_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_issue'];
					$transfer_in_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_receive'];
				?> 
					<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						
                         <?
					if (!in_array($selectResult[csf('po_number')],$order_id_array) )
						{ $k++;
				  ?>	<td width="30"> <? echo $k;?> </td>
						<td width="100" align="center"><p> <? echo $order_arr[$selectResult[csf('po_id')]];?></p></td>
						<td width="80" align="center"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
						<td width="80" align="center"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
						<td width="80" align="right"> <? echo number_format($selectResult[csf('po_quantity')]);?> </td>
                        <td width="80" align="right"><? echo number_format($selectResult[csf('po_quantity')]/12,0); ?> </td>
                        <? 
						$order_id_array[]=$selectResult[csf('po_number')];
						}
						else
						{
						?>
						<td width="30"> <? // echo $i;?> </td>
						<td width="100"><p> <? //echo $order_arr[$selectResult[csf('po_id')]];?></p></td>
						<td width="80"><p><? //echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
						<td width="80"><p><? //echo $selectResult[csf('style_ref_no')];?></p></td>
						<td width="80" align="right"> <? //echo $selectResult[csf('po_quantity')];?> </td>	
                        <td width="80" align="right"><? //echo number_format($selectResult[csf('po_quantity')]/12,0); ?> </td>
						<?
                        }
					?>
						<td width="200" align="left"><p><? echo $trim_group[$selectResult[csf('trim_group')]]; ?></p></td>
                        <td width="100" align="left"><p><? echo $color_library[$item_color]; ?></p></td>
                         <td width="50" align="left"><p><? echo $item_size;  ?></p></td>
						<td width="40" align="center"><p> <? echo $unit_of_measurement[$order_uom];   ?></p></td>
						
						<td width="80"  align="right"><a href='#report_details' onClick="openmypage_color_size('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('trim_group')]; ?>','<? echo $item_color; ?>','<? echo $item_size; ?>','receive_item_color_size_popup');"><?  echo number_format($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty,2); ?> </a> </td>
						<td width="80" title="<? echo "Rate: ".$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate']; ?>" align="right"> <? $rec_value= ($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty)*$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate'];  echo number_format($rec_value,2); ?>  </td>
						
						<td width="80" align="right">
                        <a href='#report_details' onClick="openmypage_color_size_issue('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('trim_group')]; ?>','<? echo $item_color; ?>','<? echo $item_size; ?>','issue_color_size_popup');">
						<? echo number_format($issue_qty+$recv_return_qty+$transfer_out_qty,2); ?></a>
						 
						</td>
						<td width="80" title="Left Over" align="right"><? $left_over=($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty)-$issue_qty+$recv_return_qty+$transfer_out_qty; echo number_format($left_over,2); ?> </td>
						<td width="30" title="Rate" align="right" ><? echo number_format($rec_value/($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty),2); ?> </td>
						<td width="60" title="Left Over Value" align="right"> <?  $total_left=$left_over*$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate']; echo  number_format($total_left,2); $left_val+=$total_left;  ?>  </td>
                        
                        <td width="60" title="Internal Ref/Grouping" align="left"><? echo $selectResult[csf('grouping')];  ?></td>
                        <td title="File No" align="left"> <? echo $selectResult[csf('file_no')]; ?></td>
					</tr>
				  <?
				 $i++;
				 
				 $total_rec=($selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty)*$receive_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['rate'];
				 $total_rec_qty+=$selectResult[csf('receive_qty')]+$issue_return_qty+$transfer_in_qty;
				 $total_issue_qty+=$issue_qty+$recv_return_qty+$transfer_out_qty;
				 $tot_receve_val+=$rec_value;
				}
			?>     
               <tr>
                   <td colspan="10" align="right"><b> Total </b> </td>
                   <td align="right"> <b><?  echo number_format($total_rec_qty,2); ?> </b> &nbsp;</td>
                   <td align="right"><b><? echo  number_format($tot_receve_val,2);?> </b></td>
                   <td align="right"><b><? echo number_format($total_issue_qty,2) ?></b> </td>
                   <td>&nbsp; </td>
                   <td align="right"></td>
                   <td align="right" > <b><? echo number_format($left_val,2); ?> </b> &nbsp;</td>
                   <td>&nbsp; </td>
                   <td>&nbsp; </td>
               </tr>    
        </tbody>
    </table>
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

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption>Recevied Detail</caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Receive ID</th>
                    <th width="75">Receive Date</th>
                    <th width="80">Recv. Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$mrr_sql="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and c.trans_type=1  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,a.recv_number,a.id,a.receive_date";
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							if($row[csf('quantity')]>0)
							{	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
							}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<tr>
                <caption>Issue Return Detail</caption>
                </tr>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue Return ID</th>
                    <th width="75">Issue Return Date</th>
                    <th width="80">Issue Ret. Qty</th>
				</thead>
                <tbody>
                <?
				
				$issue_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_return_qty'];
					$recv_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['recv_return_qty'];
					$transfer_out_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_issue'];
					
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$j=1;
					  $mrr_sql2="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73  and b.id=c.trans_id and d.id=c.prod_id and c.trans_type=4  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0
					 group by    c.po_breakdown_id,d.item_group_id,a.recv_number,a.id,a.receive_date";
					
				/* $sql_trim2 = "select b.po_breakdown_id,a.item_group_id,
		 			sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty
			from 			
				product_details_master a, order_wise_pro_details b, inv_trims_entry_dtls c
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and b.entry_form in(73)  and b.po_breakdown_id='$po_id'  and a.item_group_id=$item_group and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id";	*/
					
					//echo $mrr_sql;
					$dtlsArray2=sql_select($mrr_sql2);
						$tot_qty_issue_ret=0;
					foreach($dtlsArray2 as $row)
					{
						if ($j%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $j;?>">
							<td width="30"><p><? echo $j; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_issue_ret+=$row[csf('quantity')];
						$j++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_issue_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<tr>
                <caption>Transfer In Detail</caption>
                </tr>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer Qty</th>
				</thead>
                <tbody>
                <?
				
				
					$k=1;
					  /*$mrr_sql2="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73  and b.id=c.dtls_id and c.trans_type=4  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,a.recv_number,a.id,a.receive_date";*/
					
					 $sql_transfer = "select b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78) and b.trans_type in(5)  then b.quantity else 0 end) as item_transfer_issue
			from 			
				product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78)   and  b.po_breakdown_id in($po_id)  and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					
				/* $sql_trim2 = "select b.po_breakdown_id,a.item_group_id,
		 			sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty
			from 			
				product_details_master a, order_wise_pro_details b, inv_trims_entry_dtls c
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and b.entry_form in(73)  and b.po_breakdown_id='$po_id'  and a.item_group_id=$item_group and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id";	*/
					
					//echo $mrr_sql;
					$dtlsArray3=sql_select($sql_transfer);
						$tot_qty_transfer_out=0;
					foreach($dtlsArray3 as $row)
					{
						if ($k%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							if($row[csf('item_transfer_issue')]>0)
							{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="30"><p><? echo $k; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_issue')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_transfer_out+=$row[csf('item_transfer_issue')];
						$k++;
							}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_transfer_out,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="receive_item_color_size_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
            <caption>Recevied Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Receive ID</th>
                    <th width="75">Receive Date</th>
                    <th width="80">Recv. Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.item_color=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					
					$i=1;
					
					$mrr_sql="select a.id, a.recv_number, a.receive_date, c.quantity as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and b.id=c.dtls_id and c.trans_type=1 and   c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' $itemcolor_cond $item_size_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,c.quantity,a.recv_number,a.id,a.receive_date";
					
					//echo $mrr_sql;
					
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
            <caption>Issue Return Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue Ret. ID</th>
                    <th width="75">Issue Return Date</th>
                    <th width="80">Issue Ret. Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.item_color=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					
					$i=1;
					
					$mrr_sql2="select a.id, a.recv_number, a.receive_date, c.quantity as quantity
					from inv_receive_master a,  product_details_master b, order_wise_pro_details c ,inv_transaction d
					where a.id=d.mst_id  and a.entry_form=73 and b.id=c.trans_id and  c.prod_id=b.id and c.trans_type=4 and   c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' $itemcolor_cond $item_size_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 
					group by c.po_breakdown_id,b.item_group_id,c.quantity,a.recv_number,a.id,a.receive_date";
					
					//echo $mrr_sql2;
					$tot_qty_issue_ret=0;
					$dtlsArray2=sql_select($mrr_sql2);
					
					foreach($dtlsArray2 as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_issue_ret+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_issue_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
            <caption>Tranfer In Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Receive ID</th>
                    <th width="75">Receive Date</th>
                    <th width="80">Recv. Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.item_color=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					
					$i=1;
					
					/*$mrr_sql="select a.id, a.recv_number, a.receive_date, c.quantity as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and b.id=c.dtls_id and c.trans_type=1 and   c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' $itemcolor_cond $item_size_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,c.quantity,a.recv_number,a.id,a.receive_date";*/
					$sql_transfer = "select b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78) and b.trans_type in(5)  then b.quantity else 0 end) as item_transfer_issue
			from 			
				product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78)   and  b.po_breakdown_id in($po_id)  and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemcolor_cond $item_size_cond group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";
				
				
					
					//echo $mrr_sql;
					$tot_qty_transfer=0;
					$dtlsArray2=sql_select($sql_transfer);
					
					foreach($dtlsArray2 as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							if($row[csf('item_transfer_issue')]>0)
							{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_issue')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_transfer+=$row[csf('item_transfer_issue')];
						$i++;
							}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_transfer,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}


if($action=="issue_color_size_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
             <caption>Received Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.item_color_id=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					
					$i=1;
					
					$mrr_sql="select a.id, a.issue_number, a.issue_date,c.quantity as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and  c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' $itemcolor_cond  $item_size_cond group by c.po_breakdown_id,p.item_group_id,c.quantity,a.issue_number,a.id,a.issue_date ";
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
             <caption>Issue Return Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.item_color_id=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					
					$i=1;
					
					$mrr_sql="select a.id, a.issue_number, a.issue_date,c.quantity as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=49 and p.id=b.prod_id and b.id=c.dtls_id and  c.trans_type=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' $itemcolor_cond  $item_size_cond group by c.po_breakdown_id,p.item_group_id,c.quantity,a.issue_number,a.id,a.issue_date ";
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
             <caption>Transfer Out Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.color_id=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					
					$k=1;$tot_qty_transfer_in=0;
					
				/*	$mrr_sql="select a.id, a.issue_number, a.issue_date,c.quantity as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and  c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' $itemcolor_cond  $item_size_cond group by c.po_breakdown_id,p.item_group_id,c.quantity,a.issue_number,a.id,a.issue_date ";*/
					 $sql_transfer = "select b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78) and b.trans_type in(5)  then b.quantity else 0 end) as item_transfer_recv
			from 			
				product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78)   and  b.po_breakdown_id in($po_id)  and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $itemcolor_cond  $item_size_cond group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					//echo $mrr_sql;
					$dtlsArray2=sql_select($sql_transfer);
					
					foreach($dtlsArray2 as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="30"><p><? echo $k; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_recv')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_transfer_in+=$row[csf('item_transfer_recv')];
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_transfer_in,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}


if($action=="receive_des_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
			<caption> Recevied Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Receive ID</th>
                    <th width="75">Receive Date</th>
                    <th width="80">Recv.Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$mrr_sql="select a.id, a.recv_number, a.receive_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and p.id=b.prod_id and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and c.trans_type=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and p.item_description='$des_prod' group by c.po_breakdown_id,b.item_group_id,p.item_description,a.id, a.recv_number, a.receive_date, b.prod_id ";
					//echo $mrr_sql;
					$tot_qty=0;
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
							<caption> Issue Return Details</caption>

				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue Return ID</th>
                    <th width="75">Issue Return Date</th>
                    <th width="80">Issue Ret.Qty</th>
				</thead>
                <tbody>
                <?
				//from inv_receive_master a,  product_details_master b, order_wise_pro_details c ,inv_transaction d
					$j=1;
				$mrr_sql2="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as issue_return
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c ,product_details_master d
					where a.id=b.mst_id and d.id=c.prod_id  and a.entry_form=73 and c.entry_form=73  
					and b.id=c.trans_id and c.trans_type=6  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID'
					 and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1
					  and b.is_deleted=0 group by    c.po_breakdown_id,d.item_group_id,a.recv_number,a.id,a.receive_date";
					$tot_qty_recv_ret=0;
					//echo $mrr_sql;
					$dtlsArray2=sql_select($mrr_sql2);
					foreach($dtlsArray2 as $row)
					{
						if ($j%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $j;?>">
							<td width="30"><p><? echo $j; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('issue_return')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_recv_ret+=$row[csf('issue_return')];
						$j++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_recv_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br>
			
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
							<caption> Transfer Out Details</caption>

				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer In.Qty</th>
				</thead>
                <tbody>
                <?
					$k=1;
					$tot_qty_in=0;
					 $sql_transfer = "select b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78) and b.trans_type in(5)  then b.quantity else 0 end) as item_transfer_recv
			from 			
				product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78) and e.company_id=$companyID  and  b.po_breakdown_id in($po_id)  and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					//echo $mrr_sql;
					$dtlsArray3=sql_select($sql_transfer);
					foreach($dtlsArray3 as $row)
					{
						if ($k%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="30"><p><? echo $k; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_recv')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_in+=$row[csf('item_transfer_recv')];
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_in,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
		 </div>
    </fieldset>
    <?
	exit();
}
if($action=="issue_des_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption> Issue Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' and p.item_description='$des_prod' group by c.po_breakdown_id,p.item_group_id,p.item_description,a.issue_number,a.id,a.issue_date, b.prod_id ";
					
					//echo $mrr_sql;
					$tot_qty=0;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br/>
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption> Recv Ret Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Recv ID</th>
                    <th width="75">Recv Date</th>
                    <th width="80">Recv. Ret Qty</th>
				</thead>
                <tbody>
                <?
					$i=1;
					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=49 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' and p.item_description='$des_prod' group by c.po_breakdown_id,p.item_group_id,p.item_description,a.issue_number,a.id,a.issue_date, b.prod_id ";
					
					//echo $mrr_sql;
					$tot_qty=0;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br/>
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption>Transfer In Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer Qty</th>
				</thead>
                <tbody>
                <?
					$k=1;
					 $sql_transfer = "select b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78) and b.trans_type in(6) then b.quantity else 0 end) as item_transfer_recv
			from 			
				product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78)   and  b.po_breakdown_id in($po_id)  and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					//echo $sql_transfer;
					$dtlsArray3=sql_select($sql_transfer);
					$tot_qty_recv_ret=0;
					foreach($dtlsArray3 as $row)
					{
						if ($k%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="30"><p><? echo $k; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_recv')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_recv_ret+=$row[csf('item_transfer_recv')];
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_recv_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			
        </div>
    </fieldset>
    <?
	exit();
}
if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	  
				
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption> Issue Details</caption> 
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					 $mrr_sql="select a.id, a.issue_number, a.issue_date,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=c.prod_id and b.id=c.dtls_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' group by c.po_breakdown_id, p.item_group_id,a.issue_number,a.id,a.issue_date ";
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
            <caption>  Recv Return Details </caption> 
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Recv Ret. Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					 $mrr_sql2="select a.id, a.issue_number, a.issue_date,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=49 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' group by c.po_breakdown_id,p.item_group_id,a.issue_number,a.id,a.issue_date ";
					//echo $mrr_sql;
					$dtlsArray2=sql_select($mrr_sql2);
					$tot_qty_recv_ret=0;
					foreach($dtlsArray2 as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_recv_ret+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_recv_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
              <caption>  Transfer Out Details </caption> 
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer. ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer In Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
						
					 $sql_transfer = "select b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_recv
			from 			
				product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78)   and  b.po_breakdown_id in($po_id)  and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					$dtlsArray=sql_select($sql_transfer);
					$tot_qty_transfer_recv=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							if($row[csf('item_transfer_recv')]>0)
							{	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_recv')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_transfer_recv+=$row[csf('item_transfer_recv')];
						$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_transfer_recv,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

?>