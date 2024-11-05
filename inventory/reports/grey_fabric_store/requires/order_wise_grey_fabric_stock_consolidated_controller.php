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
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_id", 110, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(13)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
     
	<script>
	
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
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
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		} 

    </script>
</head>
<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    	<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th> 					
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_grey_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit(); 
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$buyer_array=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	
	if($db_type==0)
	{
		if($year_id!=0) $year_search_cond=" and year(insert_date)=$year_id"; else $year_search_cond="";
		$year_cond= "year(insert_date)as year";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_search_cond=" and TO_CHAR(insert_date,'YYYY')=$year_id"; else $year_search_cond="";
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
	}
		
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_cond from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_search_cond $month_cond order by job_no DESC";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,80,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
   exit(); 
} 

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data); 
?>	
    <script>
	 
	function js_set_value(str)
	{
		var splitData = str.split("_");
		//alert (splitData[1]);
		$("#order_no_id").val(splitData[0]); 
		$("#order_no_val").val(splitData[1]); 
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="order_no_id" />
     <input type="hidden" id="order_no_val" />
 <?
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and b.buyer_name=$data[1]";
	if ($data[2]=="") $order_no=""; else $order_no=" and a.po_number=$data[2]";
	$job_no=str_replace("'","",$txt_job_id);
	if ($data[2]=="") $job_no_cond=""; else $job_no_cond="  and b.job_no_prefix_num='$data[2]'";
	
	$sql="select a.id, a.po_number, b.job_no_prefix_num, b.job_no, b.buyer_name, b.style_ref_no from wo_po_details_master b, wo_po_break_down a  where b.job_no=a.job_no_mst and b.company_name=$data[0] and b.is_deleted=0 $buyer_name $job_no_cond ORDER BY b.job_no DESC";
	//echo $sql;
	$arr=array(1=>$buyer_arr);
	
	echo  create_list_view("list_view", "Job No,Buyer,Style Ref.,Order No", "110,110,150,180","610","350",0, $sql, "js_set_value", "id,po_number", "", 1, "0,buyer_name,0,0,0", $arr , "job_no_prefix_num,buyer_name,style_ref_no,po_number", "order_wise_grey_fabric_stock_controller",'setFilterGrid("list_view",-1);','0,0,0,0,0','') ;
	disconnect($con);
	exit();
}

$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$machine_arr=return_library_array( "select id, dia_width from lib_machine_name", "id", "dia_width"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$cbo_sock_for=str_replace("'","",$cbo_sock_for);
	$cbo_value_with=str_replace("'","",$cbo_value_with);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	
	//if($cbo_value_with==1) $zero_value=0;else  $zero_value=0;
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			
			
				$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
			
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				
				
				$constructionArr[$row[csf('id')]]=$row[csf('construction')];
				list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
				$copmpositionArr[$row[csf('id')]]=$cps;
				
			}
		}
	}
	
	
	$transaction_date_array=array();
	$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 and item_category=13 group by prod_id";
	$sql_date_result=sql_select($sql_date);
	foreach( $sql_date_result as $row )
	{
		$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
		$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
	}
	
	if($rpt_type==3)
	{
		$sql="select a.buyer_id,b.job_no,b.po_break_down_id as po_id,b.construction,b.fabric_color_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.buyer_id,b.job_no,b.po_break_down_id,b.construction,b.fabric_color_id";
		$sql_result=sql_select($sql);
		foreach( $sql_result as $row )
		{
			$key=$row[csf('buyer_id')].$row[csf('job_no')].$row[csf('po_id')].$row[csf('construction')].$row[csf('fabric_color_id')];//$color_arr[
			$grey_qnty_array[$key]+=$row[csf('grey_req_qnty')];
		}
	}
	else
	{
		$grey_qnty_array=return_library_array( "select b.po_break_down_id as po_id, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by b.po_break_down_id", "po_id", "grey_req_qnty" );
	}

	//if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";
	if(str_replace("'","",$cbo_buyer_id)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and b.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and b.buyer_name=$cbo_buyer_id";//.str_replace("'","",$cbo_buyer_name)
	}

	if ($cbo_store_id!=0) $store_cond=" and a.store_id=$cbo_store_id";
	
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and b.job_no_prefix_num in ($job_no) ";
	$year_id=str_replace("'","",$cbo_year);

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(b.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($year_id!=0) $year_cond=" and TO_CHAR(b.insert_date,'YYYY')=$year_id"; else $year_cond="";
	}
	
	/*$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond=" and a.id in ($order_no)";*/
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.style_ref_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.po_number LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.file_no LIKE '$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.grouping LIKE '$txt_search_comm%'";
	}
	else
	{
		$search_cond.="";
	}
	
	$order_cond="";
	if($cbo_sock_for==1)
	{
		$order_cond=" and a.shiping_status<>3 and a.status_active=1";
	}
	else if($cbo_sock_for==2)
	{
		$order_cond=" and a.status_active=3";
	}
	else if($cbo_sock_for==3)
	{
		$order_cond=" and a.shiping_status=3 and a.status_active=1";
	}
	else
	{
		$order_cond="";
	}
	
	if($rpt_type==1)
	{
		
		if(str_replace("'","",$cbo_presentation)==1)
		{
			$program_no_array=array();
			if($db_type==0)
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
			}
			else
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
			}
			
			foreach( $programData as $row )
			{
				$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
			}
			
			if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";
			
			$product_array=array();	
			$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
			$prod_query_sql=sql_select($prod_query);
			foreach( $prod_query_sql as $row )
			{
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
			}
			
			$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
			$sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,83,84) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
			$result_trans=sql_select( $sql_trans );
			foreach ($result_trans as $row)
			{
				$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
				$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			}
			
			/*$sql_transfer_in="select a.transaction_type, a.order_id, a.prod_id, sum(a.cons_quantity) as trans_qnty from inv_transaction a, inv_item_transfer_mst b where a.mst_id=b.id and b.transfer_criteria=4 and b.item_category=13 and a.item_category=13 and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.order_id=7175 $trans_date group by a.transaction_type,a.order_id,a.prod_id";
			echo $sql_transfer_in;
			$data_transfer_in_array=sql_select($sql_transfer_in);
			foreach( $data_transfer_in_array as $row )
			{
				$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
				$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
			}*/
			//print_r($trans_arr[3593]);
			
			ob_start();
			?>
			<fieldset style="width:1410px">
				<table cellpadding="0" cellspacing="0" width="1410">
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
					<thead>
						<tr>
							<th width="40" rowspan="2">SL</th>
							<th colspan="5">Fabric Details</th>
							<th colspan="4">Receive Details</th>
							<th colspan="4">Issue Details</th>
							<th colspan="2">Stock Details</th>
						</tr>
						<tr>
							<th width="100">Program No.</th>
							<th width="70">Product ID</th>
							<th width="150">Const. & Comp</th>
							<th width="70">GSM</th>
							<th width="60">F/Dia</th>
							<th width="90">Recv. Qty.</th>
							<th width="90">Issue Return Qty.</th>
							<th width="90">Transf. In Qty.</th>
							<th width="90">Total Recv.</th>
							<th width="90">Issue Qty.</th>
							<th width="90">Receive Return Qty.</th>
							<th width="90">Transf. Out Qty.</th>
							<th width="90">Total Issue</th>
							<th width="90">Stock Qty.</th>
							<th>DOH</th>
						</tr>
					</thead>
				</table>
				<div style="width:1430px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left"> 
					<?
						
						$sql="select b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, a.id, a.po_number, a.pub_shipment_date, a.grouping, a.file_no, a.po_quantity from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond order by a.id, a.pub_shipment_date";
						$result=sql_select( $sql );
						$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
						foreach($result as $row)
						{
							$dataProd=array_filter(array_unique(explode(",",substr($product_id_arr[$row[csf('id')]],0,-1))));
							if(count($dataProd)>0)
							{
							?>
								<tr><td colspan="16" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo "Order No: ".$row[csf('po_number')]."; Job No: ".$row[csf('job_no')]."; Style Ref: ".$row[csf('style_ref_no')]."; Buyer: ".$buyer_arr[$row[csf('buyer_name')]]."; File No: ".$row[csf('file_no')]."; Int Ref. No: ".$row[csf('grouping')]."; RMG Qty: ".number_format($row[csf('po_quantity')]*$row[csf('ratio')],0).";&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"."Order Id ".$row[csf('id')]; ?>;&nbsp;&nbsp;<a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $row[csf('id')]; ?>')"><? echo "Grey Fabric Qty(Kg): ".number_format($grey_qnty_array[$row[csf('id')]],2); ?></a></b></td></tr>
							<?
								$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;
								foreach($dataProd as $prodId)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$recv_qty=$recvIssue_array[$row[csf('id')]][$prodId][1];
									$iss_qty=$recvIssue_array[$row[csf('id')]][$prodId][2];
									$iss_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][4];
									$recv_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][3];
									//$trans_in_sam_qty=$recvIssue_array[$row[csf('id')]][$prodId][5];
									//$trans_out_sam_qty=$recvIssue_array[$row[csf('id')]][$prodId][6];
									//$trans_in_qty=$trans_arr[$row[csf('id')]][$prodId][5]+$recvIssue_array[$row[csf('id')]][$prodId][5];
									//$trans_out_qty=$trans_arr[$row[csf('id')]][$prodId][6]+$recvIssue_array[$row[csf('id')]][$prodId][6];
									
									$trans_in_qty=$recvIssue_array[$row[csf('id')]][$prodId][5];
									$trans_out_qty=$recvIssue_array[$row[csf('id')]][$prodId][6];
									
									$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
									$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
									$stock_qty=$recv_tot_qty-$iss_tot_qty;
									
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]][$prodId])));
									if($cbo_value_with==1 && $stock_qty>=0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td width="70"><p><? echo $prodId; ?></p></td>
											<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
											<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>
											<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
											<td width="90" align="right" title="<? echo $row[csf('id')]."**".$prodId; ?>"><? echo number_format($trans_in_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
											<?
												$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
											?>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
										</tr>
										<?
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;  
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty; 
										$order_trans_in_qty+=$trans_in_qty; 
										$order_trans_out_qty+=$trans_out_qty; 
										$order_tot_recv_qnty+=$recv_tot_qty; 
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;
										
										$tot_recv_qty+=$recv_qty; 
										$tot_iss_ret_qty+=$iss_ret_qty; 
										$tot_iss_qty+=$iss_qty; 
										$tot_rec_ret_qty+=$recv_ret_qty; 
										$tot_trans_in_qty+=$trans_in_qty; 
										$tot_trans_out_qty+=$trans_out_qty; 
										$grand_tot_recv_qty+=$recv_tot_qty; 
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;
										$i++;
										
									}
									else if($cbo_value_with==2 && $stock_qty>0) 
									{
										?>
                                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                            <td width="40"><? echo $i; ?></td>
                                            <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
                                            <td width="70"><p><? echo $prodId; ?></p></td>
                                            <td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
                                            <td width="70"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
                                            <td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>
                                            <td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
                                            <td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
                                            <td width="90" align="right" title="<? echo $row[csf('id')]."**".$prodId; ?>"><? echo number_format($trans_in_qty,2); ?></td>
                                            <td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
                                            <td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
                                            <td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
                                            <td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
                                            <td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
                                            <td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
                                            <?
                                                $daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
                                            ?>
                                            <td align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
                                        </tr>
                                        <?
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;  
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty; 
										$order_trans_in_qty+=$trans_in_qty; 
										$order_trans_out_qty+=$trans_out_qty; 
										$order_tot_recv_qnty+=$recv_tot_qty; 
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;
										
										$tot_recv_qty+=$recv_qty; 
										$tot_iss_ret_qty+=$iss_ret_qty; 
										$tot_iss_qty+=$iss_qty; 
										$tot_rec_ret_qty+=$recv_ret_qty; 
										$tot_trans_in_qty+=$trans_in_qty; 
										$tot_trans_out_qty+=$trans_out_qty; 
										$grand_tot_recv_qty+=$recv_tot_qty; 
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;
										$i++;
									}
									
									
									
								
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="6" align="right"><b>Order Total</b></td>
									<td align="right"><? echo number_format($order_recv_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_iss_ret_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_trans_in_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_tot_recv_qnty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_iss_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_rec_ret_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_trans_out_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_tot_iss_qnty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_stock_qnty,2,'.',''); ?></td>
									<td align="right">&nbsp;</td>
								</tr>
							<?
							}
						}
						?>
						<tfoot>
							<tr>
								<th colspan="6" align="right"><b>Grand Total</b></th>
								<th align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
								<th align="right">&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		<?
		}
		else if(str_replace("'","",$cbo_presentation)==2)
		{
			$date_from=str_replace("'","",$txt_date_from);
			if( $date_from=="") $receive_date=""; else $receive_date= " and e.receive_date <=".$txt_date_from."";
			//echo $cbo_presentation;
			//==========================================================
			//if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_trans=" and d.buyer_name=$cbo_buyer_id";
			if(str_replace("'","",$cbo_buyer_id)==0)
			{
				if ($_SESSION['logic_erp']["data_level_secured"]==1)
				{
					if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_trans="";
				}
				else
				{
					$buyer_id_cond_trans="";
				}
			}
			else
			{
				$buyer_id_cond_trans=" and d.buyer_name=$cbo_buyer_id";//.str_replace("'","",$cbo_buyer_name)
			}
			
			$program_no_array=array();
			if($db_type==0)
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
			}
			else
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
			}
			
			foreach( $programData as $row )
			{
				$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]=$row[csf('prog_no')];
			}
			
			$job_no=str_replace("'","",$txt_job_no);
			if ($job_no=="") $job_no_cond_trans=""; else $job_no_cond_trans=" and d.job_no_prefix_num in ($job_no) ";
			$year_id=str_replace("'","",$cbo_year);
			
			$variable_set_cond=" and e.entry_form in (2,22,58)";
			
			if($db_type==0)
			{
				if($year_id!=0) $year_cond_trans=" and year(d.insert_date)=$year_id"; else $year_cond_trans="";
			}
			else if($db_type==2)
			{
				if($year_id!=0) $year_cond_trans=" and TO_CHAR(d.insert_date,'YYYY')=$year_id"; else $year_cond_trans="";
			}
			
			
			$order_no=str_replace("'","",$txt_order_id);
			if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";
			
			$date_from=str_replace("'","",$txt_date_from);
			if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

			//=================Order/Rack & Shelf Wise================
			$trans_order_cond="";
			if($cbo_sock_for==1)
			{
				$trans_order_cond=" and c.shiping_status<>3 and c.status_active=1";
			}
			else if($cbo_sock_for==2)
			{
				$trans_order_cond=" and c.status_active=3";
			}
			else if($cbo_sock_for==3)
			{
				$trans_order_cond=" and c.shiping_status=3 and c.status_active=1";
			}
			else
			{
				$trans_order_cond="";
			}
			$transfer_in_arr=array(); $trans_arr=array();	
			$sql_transfer_in="select a.to_order_id, b.from_prod_id, b.to_rack, b.to_shelf, b.y_count, b.yarn_lot, sum(b.transfer_qnty) as transfer_in_qnty, sum(b.roll) as transfer_in_roll 
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_po_break_down c, wo_po_details_master d 
			where c.job_no_mst=d.job_no and a.to_order_id=c.id and a.company_id=$cbo_company_id and a.id=b.mst_id and a.transfer_criteria in(4,7) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond_trans $year_cond_trans $job_no_cond_trans $order_id_cond_trans $trans_order_cond 
			group by a.to_order_id, b.from_prod_id, b.y_count, b.yarn_lot, b.to_rack, b.to_shelf";
			
			$data_transfer_in_array=sql_select($sql_transfer_in);
			if(count($data_transfer_in_array)>0)
			{
				foreach( $data_transfer_in_array as $row )
				{
					if($row[csf('to_shelf')]=="") $row[csf('to_shelf')]=0;
					
					$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['qty']=$row[csf('transfer_in_qnty')];
					$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['roll']=$row[csf('transfer_in_roll')];
					
					$trans_data=$row[csf('to_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('to_rack')]."_".$row[csf('to_shelf')];
					//echo $trans_data."<br>";
					$trans_arr[]=$trans_data;
				}
			}
			
			//var_dump ($trans_arr);
			$product_array=array();	
			$prod_query="Select id, detarmination_id, gsm, dia_width, brand, yarn_count_id, lot, color from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
			$prod_query_sql=sql_select($prod_query);
			if(count($prod_query_sql)>0)
			{
				foreach( $prod_query_sql as $row )
				{
					$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
					$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
					$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
					$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
					$product_array[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
					$product_array[$row[csf('id')]]['lot']=$row[csf('lot')];
					$product_array[$row[csf('id')]]['color']=$row[csf('color')];
				}
			}
			
			//print_r($trans_arr);die;
			$transfer_out_arr=array(); 
			$sql_transfer_out="select a.from_order_id, b.from_prod_id, b.rack, b.shelf, b.y_count, b.yarn_lot, sum(b.transfer_qnty) as transfer_out_qnty, sum(b.roll) as transfer_out_roll from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.transfer_criteria in(4,6) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.from_order_id, b.from_prod_id, b.y_count, b.yarn_lot, b.rack, b.shelf";
			$data_transfer_out_array=sql_select($sql_transfer_out);
			if(count($data_transfer_out_array)>0)
			{
				foreach( $data_transfer_out_array as $row )
				{
					if($row[csf('shelf')]=="") $row[csf('shelf')]=0;
					
					$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['qty']=$row[csf('transfer_out_qnty')];
					$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['roll']=$row[csf('transfer_out_roll')];
					
					$trans_data=$row[csf('from_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('shelf')];
					//echo $trans_data."<br>";
					if(!in_array($trans_data,$trans_arr))
					{
						$trans_arr[]=$trans_data;
					}
				}
			}
			//print_r($transfer_out_arr[4401][5062]);
			//var_dump($transfer_out_arr);
			$transaction_date_array=array();
			$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 group by prod_id";
			$sql_date_result=sql_select($sql_date);
			foreach( $sql_date_result as $row )
			{
				$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
				$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
			}
			
			$retn_arr=array(); $retn_data_arr=array();
			
			$return_order_cond="";
			if($cbo_sock_for==1)
			{
				$return_order_cond=" and c.shiping_status<>3 and c.status_active=1";
			}
			else if($cbo_sock_for==2)
			{
				$return_order_cond=" and c.status_active=3";
			}
			else if($cbo_sock_for==3)
			{
				$return_order_cond=" and c.shiping_status=3 and c.status_active=1";
			}
			else
			{
				$return_order_cond="";
			}
			 
			 $sql_retn="select b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(84,51) then b.quantity end) as iss_rtn_qty, sum(case when a.transaction_type=3 and b.trans_type=3 and b.entry_form=45 then b.quantity end) as rcv_rtn_qty 
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c 
			where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(45,51,84) and a.company_id=$cbo_company_id and a.transaction_type in(3,4) and b.trans_type in (3,4) $return_order_cond 
			group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
			$data_retn_array=sql_select($sql_retn);
			foreach($data_retn_array as $row )
			{
				if($row[csf('self')]=="") $row[csf('self')]=0;
				$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss']=$row[csf('iss_rtn_qty')];
				$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv']=$row[csf('rcv_rtn_qty')];
				$rtn_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('batch_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
				$retn_data_arr[]=$rtn_data;
			}

			ob_start();
			?>
			<fieldset>
				<table cellpadding="0" cellspacing="0" width="2100">
					<tr  class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="28" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="28" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="28" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th colspan="8">Fabric Details</th>
							<th colspan="3">Used Yarn Details</th>
							<th width="100" rowspan="2">Receive Basis (BK/PLN/ PI/GPE)</th>
							<th colspan="5">Receive Details</th>
							<th colspan="5">Issue Details</th>
							<th colspan="5">Stock Details</th>
						</tr>
						<tr>
							<th width="100">Related Program No.</th>
							<th width="">Const. & Comp</th>
							<th width="60">GSM</th>
							<th width="60">F/Dia</th>
							<th width="60">M/Dia</th>
							<th width="60">Stich Length</th> 
							<th width="80">Dyeing Color</th>
							<th width="80">Color Type</th>
							<th width="60">Y. Count</th>
							<th width="80">Y. Brand</th>
							<th width="80">Y. Lot</th>
							<th width="80">Recv. Qty.</th>
                            <th width="80">Issue Return Qty.</th>
							<th width="80">Transf. In Qty.</th>
							<th width="80">Total Recv.</th>
							<th width="60">Recv. Roll</th>
							<th width="80">Issue Qty.</th>
                            <th width="80">Receive Return Qty.</th>
							<th width="80">Transf. Out Qty.</th>
							<th width="80">Total Issue</th>
							<th width="60">Issue Roll</th>
							<th width="80">Stock Qty.</th>
							<th width="60">Roll Qty.</th>
							<th width="50">Rack</th>
							<th width="50">Shelf</th>
							<th width="50">DOH</th>
						</tr>
					</thead>
				</table>
				<div style="width:2120px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="2100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left"> 
					<?
						$issue_qty_roll_array=array(); $isuue_data_arr=array();
						$issue_order_cond="";
						if($cbo_sock_for==1)
						{
							$issue_order_cond=" and p.shiping_status<>3 and p.status_active=1";
						}
						else if($cbo_sock_for==2)
						{
							$issue_order_cond=" and p.status_active=3";
						}
						else if($cbo_sock_for==3)
						{
							$issue_order_cond=" and p.shiping_status=3 and p.status_active=1";
						}
						else
						{
							$issue_order_cond="";
						}
							
						$sql_issue="Select a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, sum(a.quantity ) as issue_qnty, sum(b.no_of_roll) as issue_roll 
						from wo_po_break_down p, order_wise_pro_details a, inv_grey_fabric_issue_dtls b 
						where p.id=a.po_breakdown_id and a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(16,61) $issue_order_cond 
						group by a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self ";
						$result_sql_issue=sql_select( $sql_issue );
						foreach ($result_sql_issue as $row)
						{
							if($row[csf('self')]=="") $row[csf('self')]=0;
							
							$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty']=$row[csf('issue_qnty')];
							$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll']=$row[csf('issue_roll')];
							$issue_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
							//echo $issue_data."<br>";
							$isuue_data_arr[]=$issue_data;
						}
						
						if($db_type==0)
						{
							$sql_dtls="select a.po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
							sum(c.quantity) as quantity, c.po_breakdown_id, c.prod_id, group_concat(d.stitch_length) as stitch_length,
							group_concat(d.brand_id) as brand_id, group_concat(d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.brand_id, d.rack, d.self, d.machine_no_id, 
							sum(case when c.trans_type=1 then d.no_of_roll else 0 end) as rec_roll, e.booking_no
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
							where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 
							and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
							and c.po_breakdown_id=a.id 
							and c.dtls_id=d.id
							and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id and e.item_category=13 
							and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $year_cond $search_cond $receive_date $variable_set_cond $order_cond
							group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no order by a.id, a.po_number, c.prod_id";	//, d.color_id
						}
						else if($db_type==2)
						{
							$sql_dtls="select a.po_number as po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
							sum(c.quantity) as quantity, a.id as po_breakdown_id, c.prod_id,
							listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, listagg(d.brand_id,',') within group (order by d.brand_id) as brand_id, listagg(d.color_id,',') within group (order by d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.rack, d.self, max(d.machine_no_id) as machine_no_id, 
							sum(case when c.trans_type=1 then d.no_of_roll else 0 end) as rec_roll,
							listagg(e.booking_no,',') within group (order by e.booking_no) as booking_no
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c,  pro_grey_prod_entry_dtls d, inv_receive_master e
							where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 
							and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
							and c.po_breakdown_id=a.id 
							and c.dtls_id=d.id
							and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id
							and e.item_category=13 and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $receive_date $variable_set_cond $order_cond
							group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no order by a.id, a.po_number, c.prod_id";//, d.color_id
						}
						//echo $sql_dtls;//die;
						$nameArray=sql_select( $sql_dtls );
						$i=1; $k=1; $m=1; $order_arr=array(); $trnsfer_in_qty=0; $trans_in_array=array(); $issue_array=array(); $return_array=array();
						foreach ($nameArray as $row)
						{
							$prod_id=$row[csf("prod_id")];
							$order_id=$row[csf("po_breakdown_id")];
							$yarn_count=$row[csf('yarn_count')];//$product_array[$row[csf('prod_id')]]['yarn_count_id'];
							$yarn_lot=$row[csf('yarn_lot')];//$product_array[$row[csf('prod_id')]]['lot'];
							$rack=$row[csf("rack")];
							
							if($row[csf('self')]=="") $selfd==0;
							else $selfd=$row[csf("self")];
							
							$count_id=explode(',',$yarn_count); $count_val='';
							foreach ($count_id as $val)
							{
								if($val>0){ if($count_val=='') $count_val=$count_arr[$val]; else $count_val.=",".$count_arr[$val]; }
							}
	
							$color_id=array_unique(explode(',',$row[csf('color_id')])); $color_name='';
							foreach ($color_id as $val)
							{
								if($val>0){ if($color_name=='') $color_name=$color_arr[$val]; else $color_name.=",".$color_arr[$val]; }
							}
							
							$brand_id=array_unique(explode(',',$row[csf('brand_id')])); $brand_name="";
							foreach ($brand_id as $val)
							{
								if($val>0){ if($brand_name=='') $brand_name=$brand_arr[$val]; else $brand_name.=",".$brand_arr[$val]; }
							}
													
							//$trans_in_array=$trnsfer_in_qty;
							$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('po_breakdown_id')]][$prod_id][$yarn_count][$yarn_lot][$rack][$selfd])));
							//$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('po_breakdown_id')]][$prod_id][$yarn_count][$yarn_lot][$rack][$selfd])));
							$trans_data_in=$order_id."_".$prod_id."_".$yarn_count."_".$yarn_lot."_".$rack."_".$selfd;
							$trans_in_array[]=$trans_data_in;
							$issue_array[]=$trans_data_in;
							$return_array[]=$trans_data_in;
							
							if(!in_array($row[csf('po_breakdown_id')],$order_arr))
							{
								if($k!=1)
								{
									foreach($trans_arr as $key=>$val2)
									{
										$value=explode("_",$val2);
										$po_id=$value[0];
										
										$count=explode(',',$value[2]); $count_value='';
										foreach ($count as $count_id)
										{
											if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
										}
										
										if($po_id==$prev_order_id)
										{
											if(!in_array($val2,$trans_in_array))
											{
												$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												
												$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
												$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
												
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
												$rec_bal=$trnsfer_in_qty+$issue_retn;
												$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
												$stock=$rec_bal-$issue_bal; 
												if($cbo_value_with==1 && $stock>=0) 
												{
													
											?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
												</tr>
											<?	
												}
												else if($cbo_value_with==2 && $stock>0) 
												{ 
											
												?>
													
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
												</tr>	
												
                                               <? }
												$i++;
												$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_rec_roll+=$rec_roll;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;
												
												$issue_array[]=$val2;
												$return_array[]=$val2;
											}
										}
									}
									
									foreach($isuue_data_arr as $key=>$val2)
									{
										$value=explode("_",$val2);
										$po_id=$value[0];
										
										$count=explode(',',$value[2]); $count_value='';
										foreach ($count as $count_id)
										{
											if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
										}
										
										if($po_id==$prev_order_id)
										{
											if(!in_array($val2,$issue_array))
											{
												$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												
												$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
																							
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
												$rec_bal=$issue_retn;
												$issue_bal=$issue_qty+$trnsfer_out_qty;
												$stock=$rec_bal-$issue_bal;
												
												if($cbo_value_with==1 && $stock>=0) 
												{
											?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td> 
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                    <td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
												</tr>
											<?	 }
												else if($cbo_value_with==2 && $stock>0) 
												{
												 ?>
                                                 	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td> 
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                    <td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
												</tr>
                                                 <?	
												}
											
												$i++;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;
												
												$return_array[]=$val2;
											}
										}
									}
									
									foreach($retn_data_arr as $key=>$val3)
									{
										$value=explode("_",$val3);
										$po_id=$value[0];
										
										$count=explode(',',$value[2]); $count_value='';
										foreach ($count as $count_id)
										{
											if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
										}
										
										if($po_id==$prev_order_id)
										{
											if(!in_array($val3,$return_array))
											{
												$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
												$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
																							
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
												$rec_bal=$issue_retn;
												$issue_bal=$recv_retn;  $stock=$rec_bal-$issue_bal;
												//$stock=$rec_bal-$issue_bal;
												if($cbo_value_with==1 && $stock>=0) 
												{
											?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td> 
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right">&nbsp;</td>
													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
												</tr>
											<?	
												}
												else if($cbo_value_with==2 && $stock>0) 
												{
												 ?>
                                                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="100"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td> 
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right">&nbsp;</td>
													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
												</tr>
                                                 <?	
												}
												$i++;
												
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_stock+=$stock;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_issue_bal+=$issue_bal;
												
												$return_array[]=$val3;
											}
										}
									}
									
								?>
									<tr class="tbl_bottom">
										<td colspan="13" align="right"><b>Order Total</b></td>
										<td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
										<td align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?>&nbsp;</td>
										<td align="right"><? echo $tot_rec_roll; ?>&nbsp;</td>
										<td align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
                                        <td align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
										<td align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?>&nbsp;</td>
										<td align="right"><? echo $tot_issue_roll; ?>&nbsp;</td>
										<td align="right"><? echo number_format($tot_stock,2,'.',''); ?>&nbsp;</td>
										<td align="right"><? echo $tot_stock_roll_qty; ?>&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
										<td align="right">&nbsp;</td>
									</tr>
								<?
									unset($tot_req_qty);
									unset($tot_rec_qty);
									unset($tot_transfer_in_qty);
									unset($tot_rec_bal);
									unset($tot_rec_roll);
									unset($tot_issue_qty);
									unset($tot_transfer_out_qty);
									unset($tot_issue_bal);
									unset($tot_issue_roll);
									unset($tot_stock);
									unset($tot_stock_roll_qty);
									unset($tot_iss_retn_qty);
									unset($tot_recv_retn_qty);
								}	
							?>
								<tr><td colspan="28" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo "Order No: ".$row[csf('po_number')]."; Job No: ".$row[csf('job_no')]."; Style Ref: ".$row[csf('style_ref_no')]."; Buyer: ".$buyer_arr[$row[csf('buyer_name')]]."; File No: ".$row[csf('file_no')]."; Int Ref. No: ".$row[csf('grouping')]."; RMG Qty: ".number_format($row[csf('po_quantity')],2).";&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"."Order Id ".$row[csf('po_breakdown_id')]; ?>;&nbsp;&nbsp;<a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $row[csf('po_breakdown_id')]; ?>')"><? echo "Grey Fabric Qty(Kg): ".number_format($grey_qnty_array[$row[csf('po_breakdown_id')]],2); ?></a></b></td></tr>
							<?	
								$order_arr[]=$row[csf('po_breakdown_id')];
								$k++;
							}
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if($row[csf('self')]=="") $row[csf('self')]=0;
							$trnsfer_in_qty=$transfer_in_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$trnsfer_in_roll=$transfer_in_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];
							$trnsfer_out_qty=$transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$trnsfer_out_roll=$transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];
							$issue_qty=$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$issue_roll=$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];
							
							$issue_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss'];
							$recv_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv'];
							//print_r($transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]);
							$rec_bal=$row[csf('quantity')]+$trnsfer_in_qty+$issue_retn;
							$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
							$stock=$rec_bal-$issue_bal;
							if($cbo_value_with==1 && $stock>=0) 
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
									<td width=""><p><? echo $composition_arr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['gsm']; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['dia_width']; ?></p></td>
									<td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
									<td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?></p></td>
									<td width="80"><p><? echo $color_name;//$color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $color_range[$row[csf('color_range_id')]]; ?>&nbsp;</p></td>
									<td width="60"><p><? echo $count_val; ?>&nbsp;</p></td> 
									<td width="80"><p><? echo $brand_name;//$brand_arr[$product_array[$row[csf('prod_id')]]['brand']]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo implode(",",array_unique(explode(",",$row[csf('booking_no')]))); ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); $tot_rec_qty+=$row[csf('quantity')]; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
									<td width="60" align="right"><p><? $rec_roll=$row[csf('rec_roll')]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
									<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
									<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
									<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
									<?
										$daysOnHand = datediff("d",change_date_format($transaction_date_array[$row[csf('prod_id')]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
									?>
									<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
								</tr>
								<?
								$prev_order_id=$row[csf('po_breakdown_id')];
								$i++;
								$grand_tot_rec_qty+=$row[csf('quantity')];
								$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
								$grand_tot_rec_bal+=$rec_bal;
								//echo $rec_roll."**".$i."<br>";
								$grand_tot_rec_roll+=$rec_roll;
								$grand_tot_issue_qty+=$issue_qty;
								$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
								$grand_tot_issue_bal+=$issue_bal;
								$grand_tot_issue_roll+=$iss_roll;
								$grand_tot_stock+=$stock;
								$grand_tot_roll_qty+=$stock_roll_qty;
								$grand_tot_issue_retn_qty+=$issue_retn;
								$grand_tot_recv_retn_qty+=$recv_retn;
							}
							else if($cbo_value_with==2 && $stock>0) 
							{ 
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                    <td width="30"><? echo $i; ?></td>
                                    <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
                                    <td width=""><p><? echo $composition_arr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
                                    <td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['gsm']; ?></p></td>
                                    <td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['dia_width']; ?></p></td>
                                    <td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                                    <td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?></p></td>
                                    <td width="80"><p><? echo $color_name;//$color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $color_range[$row[csf('color_range_id')]]; ?>&nbsp;</p></td>
                                    <td width="60"><p><? echo $count_val; ?>&nbsp;</p></td> 
                                    <td width="80"><p><? echo $brand_name;//$brand_arr[$product_array[$row[csf('prod_id')]]['brand']]; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
                                    <td width="100"><p><? echo implode(",",array_unique(explode(",",$row[csf('booking_no')]))); ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); $tot_rec_qty+=$row[csf('quantity')]; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
                                    <td width="60" align="right"><p><? $rec_roll=$row[csf('rec_roll')]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
                                    <td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                                    <td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
                                    <td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
                                    <td width="50" align="center"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
                                    <td width="50" align="center"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
                                    <?
                                        $daysOnHand = datediff("d",change_date_format($transaction_date_array[$row[csf('prod_id')]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                                    ?>
                                    <td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                                </tr>
								<?
								$prev_order_id=$row[csf('po_breakdown_id')];
								$i++;
								$grand_tot_rec_qty+=$row[csf('quantity')];
								$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
								$grand_tot_rec_bal+=$rec_bal;
								//echo $rec_roll."**".$i."<br>";
								$grand_tot_rec_roll+=$rec_roll;
								$grand_tot_issue_qty+=$issue_qty;
								$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
								$grand_tot_issue_bal+=$issue_bal;
								$grand_tot_issue_roll+=$iss_roll;
								$grand_tot_stock+=$stock;
								$grand_tot_roll_qty+=$stock_roll_qty;
								$grand_tot_issue_retn_qty+=$issue_retn;
								$grand_tot_recv_retn_qty+=$recv_retn;
							}
							
						}
						//var_dump($trans_in_array);
						
						foreach($trans_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];
							
							if($po_id==$prev_order_id )
							{
								if(!in_array($val3,$trans_in_array))
								{
									$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									
									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
									$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
			
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$count=explode(',',$value[2]); $count_value='';
									foreach ($count as $count_id)
									{
										if($count_id>0) { if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
									}
									
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$trnsfer_in_qty+$issue_retn;$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn; 
									$stock=$rec_bal-$issue_bal; 
									if($cbo_value_with==1 && $stock>=0) 
									{ 
									?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;<? //echo $val3;?></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
											<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
											<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
											<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
											<?
												$daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
											?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
										</tr>
									<?
									 $i++;
									$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
									$grand_tot_rec_bal+=$rec_bal;
									$grand_tot_rec_roll+=$rec_roll;
									$grand_tot_issue_qty+=$issue_qty;
									$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
									$grand_tot_issue_bal+=$issue_bal;
									$grand_tot_issue_roll+=$iss_roll;
									$grand_tot_stock+=$stock;
									$grand_tot_roll_qty+=$stock_roll_qty;
									$grand_tot_issue_retn_qty+=$issue_retn;
									$grand_tot_recv_retn_qty+=$recv_retn;	
									}
									
									else if($cbo_value_with==2 && $stock>0) 
									{ 
									 ?>
									 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;<? //echo $val3;?></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
											<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
											<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
											<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
											<?
												$daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
											?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
										</tr>
									 <?
									 $i++;
									$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
									$grand_tot_rec_bal+=$rec_bal;
									$grand_tot_rec_roll+=$rec_roll;
									$grand_tot_issue_qty+=$issue_qty;
									$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
									$grand_tot_issue_bal+=$issue_bal;
									$grand_tot_issue_roll+=$iss_roll;
									$grand_tot_stock+=$stock;
									$grand_tot_roll_qty+=$stock_roll_qty;
									$grand_tot_issue_retn_qty+=$issue_retn;
									$grand_tot_recv_retn_qty+=$recv_retn;
									
									$issue_array[]=$val3;
									$return_array[]=$val3;
									}
									
								}
							}
						}
						
						foreach($isuue_data_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];
							
							$count=explode(',',$value[2]); $count_value='';
							foreach ($count as $count_id)
							{
								if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
							}
							
							if($po_id==$prev_order_id)
							{
								if(!in_array($val3,$issue_array))
								{
									$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									
									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
																				
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$issue_retn;  $issue_bal=$issue_qty+$trnsfer_out_qty; $stock=$rec_bal-$issue_bal;
									if($cbo_value_with==1 && $stock>=0) 
										{ 
								?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
										<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
										<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
										<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
										<td width="60"><p>&nbsp;</p></td>
										<td width="60"><p>&nbsp;</p></td>
										<td width="80"><p>&nbsp;</p></td>
										<td width="80"><p>&nbsp;</p></td>
										<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
										<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $value[3]; ?></p></td>
										<td width="100"><p>&nbsp;</p></td>
										<td width="80" align="right"><p>&nbsp;</p></td>
                                        <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
										<td width="80" align="right"><p>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
										<td width="60" align="right"><p>&nbsp;</p></td> 
										<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                        <td width="80" align="right"><p>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
										<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
										<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
										<td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
										<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
										<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
										<?
											$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
										?>
										<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
									</tr>
									<?
									$i++;
									
									$grand_tot_issue_qty+=$issue_qty;
									$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
									
									$grand_tot_issue_bal+=$issue_bal;
									$grand_tot_rec_bal+=$rec_bal;
									
									$grand_tot_issue_roll+=$iss_roll;
									$grand_tot_stock+=$stock;
									
									$grand_tot_roll_qty+=$stock_roll_qty;
									$grand_tot_issue_retn_qty+=$issue_retn;
									
									$return_array[]=$val3;
									}
								
									else if($cbo_value_with==2 && $stock>0) 
									{ 
									?>
                                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="30"><? echo $i; ?></td>
										<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
										<td width=""><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
										<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
										<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
										<td width="60"><p>&nbsp;</p></td>
										<td width="60"><p>&nbsp;</p></td>
										<td width="80"><p>&nbsp;</p></td>
										<td width="80"><p>&nbsp;</p></td>
										<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
										<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
										<td width="80"><p><? echo $value[3]; ?></p></td>
										<td width="100"><p>&nbsp;</p></td>
										<td width="80" align="right"><p>&nbsp;</p></td>
                                        <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
										<td width="80" align="right"><p>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
										<td width="60" align="right"><p>&nbsp;</p></td> 
										<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                        <td width="80" align="right"><p>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
										<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
										<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
										<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
										<td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
										<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
										<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
										<?
											$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
										?>
										<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
									</tr>
                                    <?
									$i++;
									
									$grand_tot_issue_qty+=$issue_qty;
									$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
									
									$grand_tot_issue_bal+=$issue_bal;
									$grand_tot_rec_bal+=$rec_bal;
									
									$grand_tot_issue_roll+=$iss_roll;
									$grand_tot_stock+=$stock;
									
									$grand_tot_roll_qty+=$stock_roll_qty;
									$grand_tot_issue_retn_qty+=$issue_retn;
									
									$return_array[]=$val3;
									}
									
								}
							}
						}
						
						foreach($retn_data_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];
							
							$count=explode(',',$value[2]); $count_value='';
							foreach ($count as $count_id)
							{
								if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
							}
							
							if($po_id==$prev_order_id)
							{
								if(!in_array($val3,$return_array))
								{
									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
									$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
																				
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$issue_retn;$issue_bal=$recv_retn;$stock=$rec_bal-$issue_bal;
									if($cbo_value_with==1 && $stock>=0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
											<td width="60" align="right"><p>&nbsp;</p></td> 
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right">&nbsp;</td>
											<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
											<td width="60" align="right"><p>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
											<?
												$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
											?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
										</tr>
										<?
										$i++;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										$grand_tot_stock+=$stock;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_issue_bal+=$issue_bal;
										
										$return_array[]=$val3;	
									}
									else if($cbo_value_with==2 && $stock>0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td><p><? echo $composition_arr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
											<td width="60" align="right"><p>&nbsp;</p></td> 
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right">&nbsp;</td>
											<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
											<td width="60" align="right"><p>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
											<?
												$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
											?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
										</tr>
										<?
										$i++;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										$grand_tot_stock+=$stock;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_issue_bal+=$issue_bal;
										$return_array[]=$val3;
									}
									
								}
							}
						}
						?>
						<tr class="tbl_bottom">
							<td colspan="13" align="right"><b>Order Total</b></td>
							<td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo $tot_rec_roll; ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
                            <td align="right"><? echo number_format($tot_recv_retn_qty ,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo $tot_issue_roll; ?>&nbsp;</td>
							<td align="right"><? echo number_format($tot_stock,2,'.',''); ?>&nbsp;</td>
							<td align="right"><? echo $tot_stock_roll_qty; ?>&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
						</tr>
						<tfoot>
							<tr>
								<th colspan="13" align="right"><b>Grand Total</b></th>
								<th align="right"><? echo number_format($grand_tot_rec_qty,2,'.',''); ?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_tot_issue_retn_qty,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_transfer_in_qty,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_rec_bal,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo $grand_tot_rec_roll; ?>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_issue_qty,2,'.',''); ?>&nbsp;</th>
                                <th align="right"><? echo number_format($grand_tot_recv_retn_qty,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_transfer_out_qty,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_issue_bal,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo $grand_tot_issue_roll; ?>&nbsp;</th>
								<th align="right"><? echo number_format($grand_tot_stock,2,'.',''); ?>&nbsp;</th>
								<th align="right"><? echo $grand_tot_roll_qty; ?>&nbsp;</th>
								<th align="right">&nbsp;</th>
								<th align="right">&nbsp;</th>
								<th align="right">&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		<?
		}
		else if(str_replace("'","",$cbo_presentation)==3)
		{
			$program_no_array=array();
			if($db_type==0)
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
			}
			else
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
			}
			
			foreach($programData as $row )
			{
				$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
			}
			
			if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";
			
			$product_array=array();	
			$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
			$prod_query_sql=sql_select($prod_query);
			foreach( $prod_query_sql as $row )
			{
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
			}
			
			$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
			$sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,16,45,51,58,61,80,81,83,84) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
			$result_trans=sql_select( $sql_trans );
			foreach ($result_trans as $row)
			{
				$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
				$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			}
			
			/*$sql_transfer_in="select a.transaction_type, a.order_id, a.prod_id, sum(a.cons_quantity) as trans_qnty from inv_transaction a, inv_item_transfer_mst b where a.mst_id=b.id and b.transfer_criteria=4 and b.item_category=13 and a.item_category=13 and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $trans_date group by a.transaction_type,a.order_id,a.prod_id";
			$data_transfer_in_array=sql_select($sql_transfer_in);
			foreach($data_transfer_in_array as $row )
			{
				$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
				$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
			}*/
			//print_r($trans_arr[3593]);
			ob_start();
			?>
			<fieldset style="width:1410px">
				<table cellpadding="0" cellspacing="0" width="1410">
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
					<thead>
						<tr>
							<th width="40" rowspan="2">SL</th>
							<th colspan="5">Fabric Details</th>
							<th colspan="4">Receive Details</th>
							<th colspan="4">Issue Details</th>
							<th colspan="2">Stock Details</th>
						</tr>
						<tr>
							<th width="100">Program No.</th>
							<th width="70">Product ID</th>
							<th width="150">Const. & Comp</th>
							<th width="70">GSM</th>
							<th width="60">F/Dia</th>
							<th width="90">Recv. Qty.</th>
							<th width="90">Issue Return Qty.</th>
							<th width="90">Transf. In Qty.</th>
							<th width="90">Total Recv.</th>
							<th width="90">Issue Qty.</th>
							<th width="90">Receive Return Qty.</th>
							<th width="90">Transf. Out Qty.</th>
							<th width="90">Total Issue</th>
							<th width="90">Stock Qty.</th>
							<th>DOH</th>
						</tr>
					</thead>
				</table>
				<div style="width:1430px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="1410" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left"> 
					<?
						if($db_type==0)
						{
							$sql="select b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, group_concat(a.id) as po_id, sum(a.po_quantity*b.total_set_qnty) as po_qty from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty order by b.id";
						}
						else
						{
							$sql="select b.id, b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as po_id, sum(a.po_quantity*b.total_set_qnty) as po_qty from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by b.id, b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty order by b.id";
						}
						//echo $sql;
						$result=sql_select( $sql );
						$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
						foreach($result as $row)
						{
							$grey_qty=0; $dataProdIds='';
							$poIds=explode(",",$row[csf('po_id')]);
							foreach($poIds as $id)
							{
								$grey_qty+=$grey_qnty_array[$id];
								$dataProdIds.=$product_id_arr[$id].",";
							}
							$dataProd=array_filter(array_unique(explode(",",substr($dataProdIds,0,-1))));
							if(count($dataProd)>0)
							{
							?>
								<tr><td colspan="16" style="font-size:14px" bgcolor="#CCCCAA">&nbsp;<b><?php echo "Job No: ".$row[csf('job_no')]."; Style Ref: ".$row[csf('style_ref_no')]."; Buyer: ".$buyer_arr[$row[csf('buyer_name')]]."; RMG Qty: ".number_format($row[csf('po_qty')],0); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="##" onClick="openpage_fabric_booking('fabric_booking_popup','<? echo $row[csf('po_id')]; ?>')"><? echo "Grey Fabric Qty(Kg): ".number_format($grey_qty,2); ?></a></b></td></tr>
							<?
								$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;
								foreach($dataProd as $prodId)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$recv_qty=0; $iss_qty=0; $iss_ret_qty=0; $recv_ret_qty=0; $trans_in_qty=0; $trans_out_qty=0;
									foreach($poIds as $id)
									{
										$recv_qty+=$recvIssue_array[$id][$prodId][1];
										$iss_qty+=$recvIssue_array[$id][$prodId][2];
										$iss_ret_qty+=$recvIssue_array[$id][$prodId][4];
										$recv_ret_qty+=$recvIssue_array[$id][$prodId][3];
										$trans_in_qty+=$recvIssue_array[$id][$prodId][5];
										$trans_out_qty+=$recvIssue_array[$id][$prodId][6];
									}
									
									$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
									$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
									$stock_qty=$recv_tot_qty-$iss_tot_qty;
									
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]][$prodId])));
									if($cbo_value_with==1 && $stock_qty>=0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td width="70"><p><? echo $prodId; ?></p></td>
											<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
											<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>
											<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
											<?
												$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
											?>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
										</tr>
										<?
										$i++;
										
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;  
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty; 
										$order_trans_in_qty+=$trans_in_qty; 
										$order_trans_out_qty+=$trans_out_qty; 
										$order_tot_recv_qnty+=$recv_tot_qty; 
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;
										
										$tot_recv_qty+=$recv_qty; 
										$tot_iss_ret_qty+=$iss_ret_qty; 
										$tot_iss_qty+=$iss_qty; 
										$tot_rec_ret_qty+=$recv_ret_qty; 
										$tot_trans_in_qty+=$trans_in_qty; 
										$tot_trans_out_qty+=$trans_out_qty; 
										$grand_tot_recv_qty+=$recv_tot_qty; 
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;	
                                    }
									else if($cbo_value_with==2 && $stock_qty>0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><? echo $i; ?></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td width="70"><p><? echo $prodId; ?></p></td>
											<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
											<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
											<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>
											<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
											<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
											<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
											<?
												$daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d"));
											?>
											<td align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
										</tr>
											
										<?
										$i++;
										
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;  
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty; 
										$order_trans_in_qty+=$trans_in_qty; 
										$order_trans_out_qty+=$trans_out_qty; 
										$order_tot_recv_qnty+=$recv_tot_qty; 
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;
										
										$tot_recv_qty+=$recv_qty; 
										$tot_iss_ret_qty+=$iss_ret_qty; 
										$tot_iss_qty+=$iss_qty; 
										$tot_rec_ret_qty+=$recv_ret_qty; 
										$tot_trans_in_qty+=$trans_in_qty; 
										$tot_trans_out_qty+=$trans_out_qty; 
										$grand_tot_recv_qty+=$recv_tot_qty; 
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;	
									}
									
								}
								?>
								<tr class="tbl_bottom">
									<td colspan="6" align="right"><b>Order Total</b></td>
									<td align="right"><? echo number_format($order_recv_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_iss_ret_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_trans_in_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_tot_recv_qnty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_iss_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_rec_ret_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_trans_out_qty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_tot_iss_qnty,2,'.',''); ?></td>
									<td align="right"><? echo number_format($order_stock_qnty,2,'.',''); ?></td>
									<td align="right">&nbsp;</td>
								</tr>
							<?
							}
						}
						?>
						<tfoot>
							<tr>
								<th colspan="6" align="right"><b>Grand Total</b></th>
								<th align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
								<th align="right"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
								<th align="right">&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</fieldset>
		<?
		}
		else if(str_replace("'","",$cbo_presentation)==4)
		{
			$program_no_array=array();
			if($db_type==0)
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
			}
			else
			{
				
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id");
			}
			
			foreach($programData as $row )
			{
				$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]=$row[csf('prog_no')];
			}
			
			if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";
			
			$product_array=array();	
			$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
			$prod_query_sql=sql_select($prod_query);
			foreach( $prod_query_sql as $row )
			{
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
				$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
			}
			
			$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
			 $sql_trans="select b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,16,45,51,58,61,80,81,83,84) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type in(1,2,3,4,5,6) $trans_date group by b.trans_type, b.po_breakdown_id, b.prod_id";
			$result_trans=sql_select( $sql_trans );
			foreach ($result_trans as $row)
			{
				$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]=$row[csf('qnty')];
				$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
			}

			ob_start();
			?>
			<fieldset style="width:630px">
				<table cellpadding="0" cellspacing="0" width="630">
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="16" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="630" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
					<thead>
						<tr>
							<th width="60">SL</th>
							<th width="100">Buyer name</th>
							<th width="100">Opening stock</th>
							<th width="60">Received</th>
							<th width="60">Total</th>
							<th width="60">Delivery</th>
							<th width="100" >Stock in KGS</th>
							<th width="150">Remarks</th>
						</tr>
					</thead>
					<?
						if($db_type==0)
						{
							$sql="select  b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_qty,b.remarks from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by  b.buyer_name,b.remarks order by b.buyer_name ";
						}
						else
						{
							echo "select  b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_qty,b.remarks from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by  b.buyer_name,b.buyer_name,b.remarks order by b.buyer_name ";
							$sql="select  b.buyer_name,sum(a.po_quantity*b.total_set_qnty) as po_qty,b.remarks from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by  b.buyer_name,b.remarks order by b.buyer_name ";
							//$sql="select b.id, b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, LISTAGG(a.id, ',') WITHIN GROUP (ORDER BY a.id) as po_id, sum(a.po_quantity*b.total_set_qnty) as po_qty from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst and b.company_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond group by b.id, b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty order by b.id";
						}
						//echo $sql;
						$result=sql_select( $sql );
						$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
						foreach($result as $row)
						{
							$grey_qty=0; $dataProdIds='';
							$poIds=explode(",",$row[csf('po_id')]);
							foreach($poIds as $id)
							{
								$grey_qty+=$grey_qnty_array[$id];
								$dataProdIds.=$product_id_arr[$id].",";
							}
							$dataProd=array_filter(array_unique(explode(",",substr($dataProdIds,0,-1))));
							if(count($dataProd)>0)
							{
							
								$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;
								foreach($dataProd as $prodId)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$recv_qty=0; $iss_qty=0; $iss_ret_qty=0; $recv_ret_qty=0; $trans_in_qty=0; $trans_out_qty=0;
									foreach($poIds as $id)
									{
										$recv_qty+=$recvIssue_array[$id][$prodId][1];
										$iss_qty+=$recvIssue_array[$id][$prodId][2];
										$iss_ret_qty+=$recvIssue_array[$id][$prodId][4];
										$recv_ret_qty+=$recvIssue_array[$id][$prodId][3];
										$trans_in_qty+=$recvIssue_array[$id][$prodId][5];
										$trans_out_qty+=$recvIssue_array[$id][$prodId][6];
									}
									
									$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
									$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
									$stock_qty=$recv_tot_qty-$iss_tot_qty;
									$total_re="";
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]][$prodId])));
									if($cbo_value_with==1 && $stock_qty>=0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="60"><? echo $i; ?></td>
											<td width="100"><p><? echo $buyer_array[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
											<td width="100"><p><? //echo number_format($recv_qty,2); ?></p></td>
											<td width="60"><p><? echo number_format($recv_tot_qty,2); $total_re+=$recv_tot_qty; ?></p></td>
											<td width="60"><p><? //echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
											<td width="100"><p><? echo number_format($iss_tot_qty,2); ?>&nbsp;</p></td>
											<td width="60" align="right"><? echo number_format($stock_qty,2); ?></td>
											<td width="150" align="right"><? echo $row[csf('remarks')]; ?></td>
										</tr>
										<?
										$i++;
									
										$order_recv_qty+=$recv_qty;
										$order_iss_ret_qty+=$iss_ret_qty;  
										$order_iss_qty+=$iss_qty;
										$order_rec_ret_qty+=$recv_ret_qty; 
										$order_trans_in_qty+=$trans_in_qty; 
										$order_trans_out_qty+=$trans_out_qty; 
										$order_tot_recv_qnty+=$recv_tot_qty; 
										$order_tot_iss_qnty+=$iss_tot_qty;
										$order_stock_qnty+=$stock_qty;
										
										$tot_recv_qty+=$recv_qty; 
										$tot_iss_ret_qty+=$iss_ret_qty; 
										$tot_iss_qty+=$iss_qty; 
										$tot_rec_ret_qty+=$recv_ret_qty; 
										$tot_trans_in_qty+=$trans_in_qty; 
										$tot_trans_out_qty+=$trans_out_qty; 
										$grand_tot_recv_qty+=$recv_tot_qty; 
										$grand_tot_iss_qty+=$iss_tot_qty;
										$grand_stock_qty+=$stock_qty;	
									}
									else if($cbo_value_with==2 && $stock_qty>0) 
									{}
									
								}
								?>
								
							<?
							}
						}
						?>
						<tfoot style="font-weight:bold">
							<td colspan="2">Grand Total</td>
                            <td></td>
                            <td><? echo $grand_tot_recv_qty;?></td>
                            <td></td>
                            <td><? echo $grand_tot_iss_qty;?></td>
                            <td></td>
                            <td></td>
						</tfoot>
					</table>
                    </div>
				
			</fieldset>
		<?
		}
	}
	else if($rpt_type==2)
	{
		if($db_type==0)
		{
			$program_no_array=return_library_array( "select po_id, group_concat(distinct(dtls_id)) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );
		}
		else
		{
			$program_no_array=return_library_array( "select po_id, LISTAGG(dtls_id, ',') WITHIN GROUP (ORDER BY dtls_id) as dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 group by po_id", "po_id", "dtls_id"  );	
		}
		
		if( str_replace("'","",$txt_date_from)=="") $trans_date=""; else $trans_date= " and a.transaction_date <=".$txt_date_from."";
		
		$product_array=array();	
		$prod_query="Select id, detarmination_id, gsm, dia_width, brand from product_details_master where item_category_id=13 and status_active=1 and is_deleted=0 ";
		$prod_query_sql=sql_select($prod_query);
		foreach( $prod_query_sql as $row )
		{
			$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
			$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
			$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
		}
		
		$product_id_arr=array(); $recvIssue_array=array(); $trans_arr=array();
		$sql_trans="SELECT b.trans_type, b.po_breakdown_id, b.prod_id, sum(b.quantity) as qnty, a.store_id
		from inv_transaction a, order_wise_pro_details b 
		where a.id=b.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.entry_form in(2,22,13,16,45,51,58,61,80,81,83,84) and a.item_category=13 and a.transaction_type in(1,2,3,4,5,6) and b.trans_type  in(1,2,3,4,5,6) $trans_date $store_cond
		group by b.trans_type, b.po_breakdown_id, b.prod_id, a.store_id";
		// echo $sql_trans;
		$result_trans=sql_select( $sql_trans );
		foreach ($result_trans as $row)
		{
			$recvIssue_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('trans_type')]]+=$row[csf('qnty')];
			$store_data_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['store_id']=$row[csf('store_id')];
			$product_id_arr[$row[csf('po_breakdown_id')]].=$row[csf('prod_id')].",";
		}
		
		/*$sql_transfer_in="select a.transaction_type, a.order_id, a.prod_id, sum(a.cons_quantity) as trans_qnty from inv_transaction a, inv_item_transfer_mst b where a.mst_id=b.id and b.transfer_criteria=4 and b.item_category=13 and a.item_category=13 and a.transaction_type in(5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $trans_date group by a.transaction_type,a.order_id,a.prod_id";
		$data_transfer_in_array=sql_select($sql_transfer_in);
		foreach( $data_transfer_in_array as $row )
		{
			$trans_arr[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('transaction_type')]]=$row[csf('trans_qnty')];
			$product_id_arr[$row[csf('order_id')]].=$row[csf('prod_id')].",";
		}*/
		//print_r($trans_arr[3593]);
		$store_name_arr = return_library_array("select id, store_name from  lib_store_location", 'id', 'store_name');
		ob_start();
		?>
		<fieldset style="width:2320px">
			<table cellpadding="0" cellspacing="0" width="1810">
				<tr  class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="22" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
                   	<td align="center" width="100%" colspan="22" style="font-size:16px">
                       <strong>
                           <? if(str_replace("'", "", $cbo_company_id) != 0) 
                           	{ 
                                echo $company_arr[str_replace("'","",$cbo_company_id)];
                           	}else { 
                                echo "All Company";
                           	} ?>
                       </strong>
                   	</td>
				</tr>
				<tr  class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="22" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="2320" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
				<thead>
					<tr>
                        <th width="40" rowspan="2">SL</th>
                        <? if(str_replace("'", "", $cbo_company_id) == 0) {?>
                        <th width="150" rowspan="2">Company</th>
                        <?}?>
                        <th width="100" rowspan="2">Order No</th>
                        <th width="100" rowspan="2">File No</th>
                        <th width="100" rowspan="2">Ref. No</th>
                        <th width="100" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Style No</th>
                        <th width="100" rowspan="2">Buyer</th>
                        <th width="100" rowspan="2">Booking Qnty</th>
                        <th colspan="5">Fabric Details</th>
                        <th colspan="4">Receive Details</th>
                        <th colspan="4">Issue Details</th>
                        <th colspan="3">Stock Details</th>
					</tr>
					<tr>
                        <th width="100">Program No.</th>
                        <th width="70">Product ID</th>
                        <th width="150">Const. & Comp</th>
                        <th width="70">GSM</th>
                        <th width="60">F/Dia</th>
                        <th width="90">Recv. Qty.</th>
                        <th width="90">Issue Return Qty.</th>
                        <th width="90">Transf. In Qty.</th>
                        <th width="90">Total Recv.</th>
                        <th width="90">Issue Qty.</th>
                        <th width="90">Receive Return Qty.</th>
                        <th width="90">Transf. Out Qty.</th>
                        <th width="90">Total Issue</th>
                        <th width="90">Stock Qty.</th>
                        <th width="90">DOH</th>
                        <th>Store</th>
					</tr>
				</thead>
			</table>
			<div style="width:2340px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="2320" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left"> 
					<?
					//and b.job_no in('D n C-17-00313','D n C-17-00314')
                    $company_cond ="";
                    if(str_replace("'", "", $cbo_company_id) != 0){ $company_cond= " and b.company_name=$cbo_company_id";}

					$sql="SELECT b.job_no, b.buyer_name, b.style_ref_no, b.total_set_qnty as ratio, a.id, a.po_number, a.file_no, a.grouping, a.pub_shipment_date, a.po_quantity,b.company_name from wo_po_details_master b, wo_po_break_down a where b.job_no=a.job_no_mst  and b.status_active=1 and b.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $order_cond $company_cond order by b.company_name,a.id, a.pub_shipment_date";
					$result=sql_select( $sql );

					$i=1; $tot_recv_qty=0; $tot_iss_qty=0; $tot_trans_in_qty=0; $tot_trans_out_qty=0; $grand_tot_recv_qty=0; $grand_tot_iss_qty=0; $grand_stock_qty=0;
					foreach($result as $row)
					{
						$dataProd=array_filter(array_unique(explode(",",substr($product_id_arr[$row[csf('id')]],0,-1))));
						if(count($dataProd)>0)
						{
							$order_recv_qty=0; $order_iss_ret_qty=0; $order_iss_qty=0; $order_rec_ret_qty=0; $order_trans_in_qty=0; $order_trans_out_qty=0; $order_tot_recv_qnty=0; $order_tot_iss_qnty=0; $order_stock_qnty=0;$p=1;
							foreach($dataProd as $prodId)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$recv_qty=$recvIssue_array[$row[csf('id')]][$prodId][1];
								$iss_qty=$recvIssue_array[$row[csf('id')]][$prodId][2];
								$iss_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][4];
								$recv_ret_qty=$recvIssue_array[$row[csf('id')]][$prodId][3];
								$trans_in_qty=$recvIssue_array[$row[csf('id')]][$prodId][5];
								$trans_out_qty=$recvIssue_array[$row[csf('id')]][$prodId][6];
								$recv_tot_qty=$recv_qty+$iss_ret_qty+$trans_in_qty;
								$iss_tot_qty=$iss_qty+$recv_ret_qty+$trans_out_qty;
								$stock_qty=$recv_tot_qty-$iss_tot_qty;
								//echo $trans_in_qty.', '.$trans_out_qty;
								$store=$store_data_array[$row[csf('id')]][$prodId]['store_id'];
								$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('id')]])));
								if($cbo_value_with==1 && $stock_qty>=0) 
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="40"><? echo $i; ?></td>
										<?
										if($p==1)
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p><? echo $company_arr[$row[csf('company_name')]]; ?>&nbsp;</p></td>
                                            <?}?>
											<td width="100"><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
											<td width="100" align="right"><? echo number_format($grey_qnty_array[$row[csf('id')]],2); ?></td>
											<?
											$tot_booking_qty+=$grey_qnty_array[$row[csf('id')]];
										}
										else
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p>&nbsp;</p></td>
                                            <? }?>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<?
										}
										$p++;
										?>
										<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $prodId; ?></p></td>
										<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
										<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
										<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>
										<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($trans_in_qty,2);?></td>
										<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
										<td width="90" align="right"><?echo number_format($recv_ret_qty,2);?></p></td>
										<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
										<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d")); ?>
										<td width="90" align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
										<td align="center"><? echo $store_name_arr[$store]; ?></td>
									</tr>
									<?	
									$i++;
									
									$order_recv_qty+=$recv_qty;
									$order_iss_ret_qty+=$iss_ret_qty;  
									$order_iss_qty+=$iss_qty;
									$order_rec_ret_qty+=$recv_ret_qty; 
									$order_trans_in_qty+=$trans_in_qty; 
									$order_trans_out_qty+=$trans_out_qty; 
									$order_tot_recv_qnty+=$recv_tot_qty; 
									$order_tot_iss_qnty+=$iss_tot_qty;
									$order_stock_qnty+=$stock_qty;									
									
									$tot_recv_qty+=$recv_qty; 
									$tot_iss_ret_qty+=$iss_ret_qty; 
									$tot_iss_qty+=$iss_qty; 
									$tot_rec_ret_qty+=$recv_ret_qty; 
									$tot_trans_in_qty+=$trans_in_qty; 
									$tot_trans_out_qty+=$trans_out_qty; 
									$grand_tot_recv_qty+=$recv_tot_qty; 
									$grand_tot_iss_qty+=$iss_tot_qty;
									$grand_stock_qty+=$stock_qty;
	                            }
								else if($cbo_value_with==2 && $stock_qty>0) 
								{
									?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
										<td width="40"><? echo $i; ?></td>
										<?
										if($p==1)
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p><? echo $company_arr[$row[csf('company_name')]]; ?>&nbsp;</p></td>
                                            <?}?>
											<td width="100"><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('job_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
											<td width="100" align="right"><? echo number_format($grey_qnty_array[$row[csf('id')]],2); ?></td>
											<?
											$tot_booking_qty+=$grey_qnty_array[$row[csf('id')]];
										}
										else
										{
                                            if($company_cond ==""){
											?>
                                            <td width="150"><p>&nbsp;</p></td>
                                            <?}?>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<td width="100"><p>&nbsp;</p></td>
											<?
										}
										$p++;
										?>
										<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
										<td width="70"><p><? echo $prodId; ?></p></td>
										<td width="150"><p><? echo $composition_arr[$product_array[$prodId]['detarmination_id']]; ?></p></td>
										<td width="70"><p><? echo $product_array[$prodId]['gsm']; ?>&nbsp;</p></td>
										<td width="60"><p><? echo $product_array[$prodId]['dia_width']; ?>&nbsp;</p></td>
										<td width="90" align="right"><? echo number_format($recv_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_ret_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($trans_in_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($recv_tot_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_qty,2); ?></p></td>
										<td width="90" align="right"><? echo number_format($recv_ret_qty,2); ?></p></td>
										<td width="90" align="right"><? echo number_format($trans_out_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($iss_tot_qty,2); ?></td>
										<td width="90" align="right"><? echo number_format($stock_qty,2); ?></td>
										<? $daysOnHand=datediff("d",change_date_format($transaction_date_array[$prodId]['max_date'],'','',1),date("Y-m-d")); ?>
										<td width="90" align="center"><? if($stock_qty>0) echo $daysOnHand; ?>&nbsp;</td>
										<td align="center"><? echo $store_name_arr[$store]; ?></td>
									</tr>
									
									<?
									$i++;
									
									$order_recv_qty+=$recv_qty;
									$order_iss_ret_qty+=$iss_ret_qty;  
									$order_iss_qty+=$iss_qty;
									$order_rec_ret_qty+=$recv_ret_qty; 
									$order_trans_in_qty+=$trans_in_qty; 
									$order_trans_out_qty+=$trans_out_qty; 
									$order_tot_recv_qnty+=$recv_tot_qty; 
									$order_tot_iss_qnty+=$iss_tot_qty;
									$order_stock_qnty+=$stock_qty;									
									
									$tot_recv_qty+=$recv_qty; 
									$tot_iss_ret_qty+=$iss_ret_qty; 
									$tot_iss_qty+=$iss_qty; 
									$tot_rec_ret_qty+=$recv_ret_qty; 
									$tot_trans_in_qty+=$trans_in_qty; 
									$tot_trans_out_qty+=$trans_out_qty; 
									$grand_tot_recv_qty+=$recv_tot_qty; 
									$grand_tot_iss_qty+=$iss_tot_qty;
									$grand_stock_qty+=$stock_qty;	
								}								
							}
						}
					}
					?>
					<tfoot>
						<tr>
                            <th colspan="<? if($company_cond == ""){echo "8";}else {echo "7";} ?>" align="right"><b>Grand Total</b></th>
                            <th align="right"><? echo number_format($tot_booking_qty,2,'.',''); ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_iss_ret_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_trans_in_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($grand_tot_recv_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_iss_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_rec_ret_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($tot_trans_out_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($grand_tot_iss_qty,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($grand_stock_qty,2,'.',''); ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	else if($rpt_type==3)
	{
		
		if(str_replace("'","",$cbo_presentation)==1 || str_replace("'","",$cbo_presentation)==2 ||str_replace("'","",$cbo_presentation)==3)
		{
			$date_from=str_replace("'","",$txt_date_from);
			if( $date_from=="") $receive_date=""; else $receive_date= " and e.receive_date <=".$txt_date_from."";
			
			//==========================================================
			//if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond_trans=" and d.buyer_name=$cbo_buyer_id";
			if(str_replace("'","",$cbo_buyer_id)==0)
			{
				if ($_SESSION['logic_erp']["data_level_secured"]==1)
				{
					if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond_trans="";
				}
				else
				{
					$buyer_id_cond_trans="";
				}
			}
			else
			{
				$buyer_id_cond_trans=" and d.buyer_name=$cbo_buyer_id";//.str_replace("'","",$cbo_buyer_name)
			}
			
			$program_no_array=array();
			if($db_type==0)
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, group_concat(a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
			}
			else
			{
				$programData=sql_select("select c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, LISTAGG(a.booking_id, ',') WITHIN GROUP (ORDER BY a.booking_id) as prog_no from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, b.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self");
			}
			
			foreach( $programData as $row )
			{
				$program_no_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]=$row[csf('prog_no')];
			}
			
			$job_no=str_replace("'","",$txt_job_no);
			if ($job_no=="") $job_no_cond_trans=""; else $job_no_cond_trans=" and d.job_no_prefix_num in ($job_no) ";
			$year_id=str_replace("'","",$cbo_year);
			
			$variable_set_cond=" and e.entry_form in (2,22,58)";
			
			if($db_type==0)
			{
				if($year_id!=0) $year_cond_trans=" and year(d.insert_date)=$year_id"; else $year_cond_trans="";
			}
			else if($db_type==2)
			{
				if($year_id!=0) $year_cond_trans=" and TO_CHAR(d.insert_date,'YYYY')=$year_id"; else $year_cond_trans="";
			}
			
			
			$order_no=str_replace("'","",$txt_order_id);
			if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";
			
			$date_from=str_replace("'","",$txt_date_from);
			if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";

			//=================Order/Rack & Shelf Wise================
			$trans_order_cond="";
			if($cbo_sock_for==1)
			{
				$trans_order_cond=" and c.shiping_status<>3 and c.status_active=1";
			}
			else if($cbo_sock_for==2)
			{
				$trans_order_cond=" and c.status_active=3";
			}
			else if($cbo_sock_for==3)
			{
				$trans_order_cond=" and c.shiping_status=3 and c.status_active=1";
			}
			else
			{
				$trans_order_cond="";
			}
			$transfer_in_arr=array(); $trans_arr=array();	
			$sql_transfer_in="select a.to_order_id, b.from_prod_id, b.to_rack, b.to_shelf, b.y_count, b.yarn_lot, sum(b.transfer_qnty) as transfer_in_qnty, sum(b.roll) as transfer_in_roll 
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_po_break_down c, wo_po_details_master d 
			where c.job_no_mst=d.job_no and a.to_order_id=c.id and a.company_id=$cbo_company_id and a.id=b.mst_id and a.transfer_criteria in(4,7) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_id_cond_trans $year_cond_trans $job_no_cond_trans $order_id_cond_trans $trans_order_cond 
			group by a.to_order_id, b.from_prod_id, b.y_count, b.yarn_lot, b.to_rack, b.to_shelf";
			
			$data_transfer_in_array=sql_select($sql_transfer_in);
			if(count($data_transfer_in_array)>0)
			{
				foreach( $data_transfer_in_array as $row )
				{
					if($row[csf('to_shelf')]=="") $row[csf('to_shelf')]=0;
					
					$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['qty']=$row[csf('transfer_in_qnty')];
					$transfer_in_arr[$row[csf('to_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('to_rack')]][$row[csf('to_shelf')]]['roll']=$row[csf('transfer_in_roll')];
					
					$trans_data=$row[csf('to_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('to_rack')]."_".$row[csf('to_shelf')];
					//echo $trans_data."<br>";
					$trans_arr[]=$trans_data;
				}
			}
			
			//var_dump ($trans_arr);
			$product_array=array();	
			$prod_query="Select id, detarmination_id, gsm, dia_width, brand, yarn_count_id, lot, color from product_details_master where item_category_id=13 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ";
			$prod_query_sql=sql_select($prod_query);
			if(count($prod_query_sql)>0)
			{
				foreach( $prod_query_sql as $row )
				{
					$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
					$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
					$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
					$product_array[$row[csf('id')]]['brand']=$row[csf('brand')];
					$product_array[$row[csf('id')]]['yarn_count_id']=$row[csf('yarn_count_id')];
					$product_array[$row[csf('id')]]['lot']=$row[csf('lot')];
					$product_array[$row[csf('id')]]['color']=$row[csf('color')];
				}
			}
			
			//print_r($trans_arr);die;
			$transfer_out_arr=array(); 
			$sql_transfer_out="select a.from_order_id, b.from_prod_id, b.rack, b.shelf, b.y_count, b.yarn_lot, sum(b.transfer_qnty) as transfer_out_qnty, sum(b.roll) as transfer_out_roll from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.transfer_criteria in(4,6) and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.from_order_id, b.from_prod_id, b.y_count, b.yarn_lot, b.rack, b.shelf";
			$data_transfer_out_array=sql_select($sql_transfer_out);
			if(count($data_transfer_out_array)>0)
			{
				foreach( $data_transfer_out_array as $row )
				{
					if($row[csf('shelf')]=="") $row[csf('shelf')]=0;
					
					$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['qty']=$row[csf('transfer_out_qnty')];
					$transfer_out_arr[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$row[csf('y_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('shelf')]]['roll']=$row[csf('transfer_out_roll')];
					
					$trans_data=$row[csf('from_order_id')]."_".$row[csf('from_prod_id')]."_".$row[csf('y_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('shelf')];
					//echo $trans_data."<br>";
					if(!in_array($trans_data,$trans_arr))
					{
						$trans_arr[]=$trans_data;
					}
				}
			}
			//print_r($transfer_out_arr[4401][5062]);
			//var_dump($transfer_out_arr);
			$transaction_date_array=array();
			$sql_date="Select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where status_active=1 and is_deleted=0 group by prod_id";
			$sql_date_result=sql_select($sql_date);
			foreach( $sql_date_result as $row )
			{
				$transaction_date_array[$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
				$transaction_date_array[$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
			}
			
			$retn_arr=array(); $retn_data_arr=array();
			
			$return_order_cond="";
			if($cbo_sock_for==1)
			{
				$return_order_cond=" and c.shiping_status<>3 and c.status_active=1";
			}
			else if($cbo_sock_for==2)
			{
				$return_order_cond=" and c.status_active=3";
			}
			else if($cbo_sock_for==3)
			{
				$return_order_cond=" and c.shiping_status=3 and c.status_active=1";
			}
			else
			{
				$return_order_cond="";
			}
			//pro_grey_prod_entry_dtls
			/* $sql_retn2="select b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(84) then b.quantity end) as iss_rtn_qty_roll
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c 
			where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(84) and a.company_id=$cbo_company_id and a.transaction_type in(4) and b.trans_type in (4) $return_order_cond 
			group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
			$data_retn_array2=sql_select($sql_retn2);
			$retn_arr2=array();
			foreach($data_retn_array2 as $row )
			{
				if($row[csf('self')]=="") $row[csf('self')]=0;
				//$retn_arr2[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['iss_roll']=$row[csf('iss_rtn_qty_roll')];
			}*/
			 //print_r($retn_arr2);
			 $sql_retn="select b.po_breakdown_id, a.prod_id, a.yarn_count, a.batch_lot, a.rack, a.self, sum(case when a.transaction_type=4 and b.trans_type=4 and b.entry_form in(51,84) then b.quantity end) as iss_rtn_qty, sum(case when a.transaction_type=3 and b.trans_type=3 and b.entry_form=45 then b.quantity end) as rcv_rtn_qty 
			from inv_transaction a, order_wise_pro_details b, wo_po_break_down c 
			where a.id=b.trans_id and b.po_breakdown_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=13 and b.entry_form in(45,51,84) and a.company_id=$cbo_company_id and a.transaction_type in(3,4) and b.trans_type in (3,4) $return_order_cond 
			group by b.po_breakdown_id, a.prod_id, a.batch_lot, a.yarn_count, a.rack, a.self";
			$data_retn_array=sql_select($sql_retn);
			foreach($data_retn_array as $row )
			{
				if($row[csf('self')]=="") $row[csf('self')]=0;
				$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss']=$row[csf('iss_rtn_qty')];
				$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('batch_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv']=$row[csf('rcv_rtn_qty')];
				$rtn_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('batch_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
				$retn_data_arr[]=$rtn_data;
			}

			ob_start();
			
			
			?>
			<fieldset>
				<table cellpadding="0" cellspacing="0" width="2770">
					<tr  class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="36" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="36" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="36" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="2750" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
					<thead>
						<tr>
							<th width="30" rowspan="2">SL</th>
							<th colspan="13">Fabric Details</th>
							<th colspan="3">Used Yarn Details</th>
							<th width="120" rowspan="2">Receive Basis (BK/PLN/ PI/GPE)</th>
							<th width="80" rowspan="2">Req. Qty.</th>
							<th colspan="5">Receive Details</th>
							<th colspan="5">Issue Details</th>
							<th colspan="7">Stock Details</th>
						</tr>
						<tr>
							
							<th width="80">Job No.</th>
							<th width="80">Buyer</th>
							<th width="80">Order No.</th>
							<th width="80">Style Ref</th>
                            
                            <th width="100">Program No.</th>
							<th width="">Constraction</th>
							<th width="100">Composition</th>
							<th width="60">GSM</th>
							<th width="60">F/Dia</th>
							<th width="60">M/Dia</th>
							<th width="60">Stich Length</th> 
							<th width="80">Dyeing Color</th>
							<th width="80">Color Range</th>
							<th width="60">Y. Count</th>
							<th width="80">Y. Brand</th>
							<th width="80">Y. Lot</th>
							<th width="80">Recv. Qty.</th>
                            <th width="80">Issue Ret. Qty.</th>
							<th width="80">Transf. In Qty.</th>
							<th width="80">Total Recv.</th>
							<th width="60">Recv. Roll</th>
							<th width="80">Issue Qty.</th>
                            <th width="80">Recv. Ret. Qty.</th>
							<th width="80">Transf. Out Qty.</th>
							<th width="80">Total Issue</th>
							<th width="60">Issue Roll</th>
							<th width="80">Stock Qty.</th>
							<th width="60">Roll Qty.</th>
							<th width="50">Rack</th>
							<th width="50">Shelf</th>
							<th width="50">DOH</th>
                            
							<th width="50">Recv. Balance</th>
							<th width="50">Issue Balance</th>
						</tr>
					</thead>
				</table>
				<div style="width:2770px; overflow-y: scroll; max-height:380px;" id="scroll_body">
					<table width="2750" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body"> 
					<?
						$issue_qty_roll_array=array(); $isuue_data_arr=array();
						$issue_order_cond="";
						if($cbo_sock_for==1)
						{
							$issue_order_cond=" and p.shiping_status<>3 and p.status_active=1";
						}
						else if($cbo_sock_for==2)
						{
							$issue_order_cond=" and p.status_active=3";
						}
						else if($cbo_sock_for==3)
						{
							$issue_order_cond=" and p.shiping_status=3 and p.status_active=1";
						}
						else
						{
							$issue_order_cond="";
						}
							
						$sql_issue="Select a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self, sum(a.quantity ) as issue_qnty, sum(b.no_of_roll) as issue_roll 
						from wo_po_break_down p, order_wise_pro_details a, inv_grey_fabric_issue_dtls b 
						where p.id=a.po_breakdown_id and a.dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(16,61) $issue_order_cond 
						group by a.po_breakdown_id, a.prod_id, b.yarn_count, b.yarn_lot, b.rack, b.self ";
						$result_sql_issue=sql_select( $sql_issue );
						foreach ($result_sql_issue as $row)
						{
							if($row[csf('self')]=="") $row[csf('self')]=0;
							
							$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty']=$row[csf('issue_qnty')];
							$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll']=$row[csf('issue_roll')];
							$issue_data=$row[csf('po_breakdown_id')]."_".$row[csf('prod_id')]."_".$row[csf('yarn_count')]."_".$row[csf('yarn_lot')]."_".$row[csf('rack')]."_".$row[csf('self')];
							//echo $issue_data."<br>";
							$isuue_data_arr[]=$issue_data;
						}
						
						if($db_type==0)
						{
							$sql_dtls="select a.po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
							sum(c.quantity) as quantity, c.po_breakdown_id, c.prod_id, group_concat(d.stitch_length) as stitch_length,
							group_concat(d.brand_id) as brand_id, group_concat(d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.brand_id, d.rack, d.self, d.machine_no_id, 
							sum(case when c.trans_type=1 then d.no_of_roll else 0 end) as rec_roll, e.booking_no
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c, pro_grey_prod_entry_dtls d, inv_receive_master e
							where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 
							and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
							and c.po_breakdown_id=a.id 
							and c.dtls_id=d.id
							and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id and e.item_category=13 
							and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $year_cond $search_cond $receive_date $variable_set_cond $order_cond
							group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no order by a.id,a.po_number, c.prod_id";	//, d.color_id
						}
						else if($db_type==2)
						{
							 $sql_dtls="select a.po_number as po_number, a.file_no, a.grouping, a.po_quantity as po_quantity, b.job_no, b.buyer_name, b.style_ref_no,
							sum(c.quantity) as quantity, a.id as po_breakdown_id, c.prod_id,
							listagg(d.stitch_length,',') within group (order by d.stitch_length) as stitch_length, listagg(d.brand_id,',') within group (order by d.brand_id) as brand_id, listagg(d.color_id,',') within group (order by d.color_id) as color_id, max(d.color_range_id) as color_range_id, d.yarn_lot, d.yarn_count, d.rack, d.self, max(d.machine_no_id) as machine_no_id, 
							sum(case when c.trans_type=1 then d.no_of_roll else 0 end) as rec_roll,
							listagg(e.booking_no,',') within group (order by e.booking_no) as booking_no
							from wo_po_break_down a, wo_po_details_master b, order_wise_pro_details c,  pro_grey_prod_entry_dtls d, inv_receive_master e
							where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 
							and c.trans_type=1 and c.trans_id!=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form in(2,22,58)
							and c.po_breakdown_id=a.id 
							and c.dtls_id=d.id
							and e.id=d.mst_id and d.status_active=1 and d.is_deleted=0 AND e.company_id=$cbo_company_id
							and e.item_category=13 and e.status_active=1 and e.is_deleted=0 $year_cond $buyer_id_cond $job_no_cond $search_cond $receive_date $variable_set_cond $order_cond
							group by a.id, a.po_number, a.file_no, a.grouping, a.po_quantity, c.prod_id, d.yarn_count, d.yarn_lot, d.rack, d.self, b.job_no, b.buyer_name, b.style_ref_no order by a.id,a.po_number, c.prod_id";//, d.color_id
						}
						  //echo $sql_dtls;//die;
						 
						 //echo $grey_qnty_array['266295OG-16-000661X1 RibWHITE'].'************';
						 
						$nameArray=sql_select( $sql_dtls ); $total_grey_qnty=0;
						$i=1; $k=1; $m=1; $order_arr=array(); $trnsfer_in_qty=0; $trans_in_array=array(); $issue_array=array(); $return_array=array();$ttt=1;
						foreach ($nameArray as $row)
						{
							$prod_id=$row[csf("prod_id")];
							$order_id=$row[csf("po_breakdown_id")];
							$yarn_count=$row[csf('yarn_count')];//$product_array[$row[csf('prod_id')]]['yarn_count_id'];
							$yarn_lot=$row[csf('yarn_lot')];//$product_array[$row[csf('prod_id')]]['lot'];
							$rack=$row[csf("rack")];
							
							if($row[csf('self')]=="") $selfd==0;
							else $selfd=$row[csf("self")];
							
							$count_id=explode(',',$yarn_count); $count_val='';
							foreach ($count_id as $val)
							{
								if($val>0){ if($count_val=='') $count_val=$count_arr[$val]; else $count_val.=",".$count_arr[$val]; }
							}
	
							$color_id=array_unique(explode(',',$row[csf('color_id')])); $color_name='';$colorId_string='';
							
							//$colorId_string=$colorId;
							//$colorId='';
							foreach ($color_id as $val)
							{ 
								if($val>0)
								{ 
									if($color_name=='')
									{
										$color_name=$color_arr[$val];
										$colorId=$val;
									}
									 else 
									 {
										 $color_name.=",".$color_arr[$val];
										 //$colorId.=",".$val;
									 }
								}
								
							}
							//echo $colorId;
							
							$brand_id=array_unique(explode(',',$row[csf('brand_id')])); $brand_name="";
							foreach ($brand_id as $val)
							{
								if($val>0){ if($brand_name=='') $brand_name=$brand_arr[$val]; else $brand_name.=",".$brand_arr[$val]; }
							}
													
							//$trans_in_array=$trnsfer_in_qty;
							$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('po_breakdown_id')]][$prod_id][$yarn_count][$yarn_lot][$rack][$selfd])));
							//$program_no=implode(",",array_unique(explode(",",$program_no_array[$row[csf('po_breakdown_id')]][$prod_id][$yarn_count][$yarn_lot][$rack][$selfd])));
							$trans_data_in=$order_id."_".$prod_id."_".$yarn_count."_".$yarn_lot."_".$rack."_".$selfd;
							$trans_in_array[]=$trans_data_in;
							$issue_array[]=$trans_data_in;
							$return_array[]=$trans_data_in;
							
							
							if(!in_array($row[csf('po_breakdown_id')],$order_arr))
							{ 
								if($k!=1)
								{
									
									foreach($trans_arr as $key=>$val2)
									{
										$value=explode("_",$val2);
										$po_id=$value[0];
										
										$count=explode(',',$value[2]); $count_value='';
										foreach ($count as $count_id)
										{
											if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
										}
										
										if($po_id==$prev_order_id)
										{
											if(!in_array($val2,$trans_in_array))
											{
												$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												
												$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
												$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
												
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
											$rec_bal=$trnsfer_in_qty+$issue_retn; $issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
											$stock=$rec_bal-$issue_bal;
											if($cbo_value_with==1 && $stock>=0) 
											{
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?></p></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="120"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                   
                                                    <td width="80" align="right"><p></p></td>
                                                   
                                                   
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													
                                                    
                                                    
                                                    <td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="50" align="center"></td>
												</tr>
												<?
												$i++;
												$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_rec_roll+=$rec_roll;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;
												
												$issue_array[]=$val2;
												$return_array[]=$val2;
												
												
												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];	
											}
											else if($cbo_value_with==2 && $stock>0) 
											{ 
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?></p></td>
													<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="120"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                   
                                                    <td width="80" align="right"><p></p></td>
                                                   
                                                   
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													
                                                    
                                                    
                                                    <td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2);$tot_transfer_in_qty+=$trnsfer_in_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="50" align="center"></td>
												</tr>
                                            	
												<? 
												$i++;
												$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_rec_roll+=$rec_roll;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;
												
												$issue_array[]=$val2;
												$return_array[]=$val2;
												
												
												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
											}
											
												
											}
										}
									}
									
									foreach($isuue_data_arr as $key=>$val2)
									{
										$value=explode("_",$val2);
										$po_id=$value[0];
										
										$count=explode(',',$value[2]); $count_value='';
										foreach ($count as $count_id)
										{
											if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
										}
										
										if($po_id==$prev_order_id)
										{
											if(!in_array($val2,$issue_array))
											{
												$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
												$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
												
												$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
																							
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
												$rec_bal=$issue_retn; $issue_bal=$issue_qty+$trnsfer_out_qty; 
												$stock=$rec_bal-$issue_bal; 
												if($cbo_value_with==1 && $stock>=0) 
												{
													?>
													<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
														<td width="30"><? echo $i; ?></td>
														<td width="80"><p><? echo $job_no;?></p></td>
														<td width="80"><p><? echo $buyer_name; ?></p></td>
														<td width="80"><p><? echo $po_number; ?></p></td>
														<td width="80"><p><? echo $style_ref_no; ?>&nbsp;</p></td>
														<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
														<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
														<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
														<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
														<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
														<td width="60"><p>&nbsp;</p></td>
														<td width="60"><p>&nbsp;</p></td>
														<td width="80"><p>&nbsp;</p></td>
														<td width="80"><p>&nbsp;</p></td>
														<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
														<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
														<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
														<td width="120"><p>&nbsp;</p></td>
														<td width="80"><p>&nbsp;</p></td>
														<td width="80" align="right"><p>&nbsp;</p></td>
														<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
														<td width="80" align="right"><p>&nbsp;</p></td>
														<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
														<td width="60" align="right"><p>&nbsp;</p></td> 
														<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
														<td width="80" align="right"><p>&nbsp;</p></td>
														<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
														<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
														<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
														<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
														<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
														<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
														<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
														<?
															$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
														?>
														<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
														<td width="50" align="center"></td>
														<td width="50" align="center"></td>
													</tr>
												<?
												$i++;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;
												
												$return_array[]=$val2;
												
												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];	
											}
											else if($cbo_value_with==2 && $stock>0) 
											{ 
												?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?>&nbsp;</p></td>
                                                    <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="120"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td> 
													<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                    <td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
													<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p><? $stock_roll_qty=$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="50" align="center"></td>
												</tr>
												<?	
                                                $i++;
												$grand_tot_issue_qty+=$issue_qty;
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
												$grand_tot_issue_bal+=$issue_bal;
												$grand_tot_issue_roll+=$iss_roll;
												$grand_tot_stock+=$stock;
												$grand_tot_roll_qty+=$stock_roll_qty;
												
												$return_array[]=$val2;
												
												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
										}
												
												//$dyeing_color_string=$color_name;
												//$colorId_string=$colorId;
												
												
											}
										}
									}
									
									foreach($retn_data_arr as $key=>$val3)
									{
										$value=explode("_",$val3);
										$po_id=$value[0];
										
										$count=explode(',',$value[2]); $count_value='';
										foreach ($count as $count_id)
										{
											if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
										}
										
										if($po_id==$prev_order_id)
										{
											if(!in_array($val3,$return_array))
											{
												$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
												$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
																							
												if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
												$rec_bal=$issue_retn; $issue_bal=$recv_retn; 
												$stock=$rec_bal-$issue_bal;
												if($cbo_value_with==1 && $stock>=0) 
												{
											?>
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?>&nbsp;</p></td>
                                                    <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="120"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td> 
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right">&nbsp;</td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="" align="center"></td>
												</tr>
											<?	}
											else if($cbo_value_with==2 && $stock>0) 
												{ ?>
											
												<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
													<td width="30"><? echo $i; ?></td>
													<td width="80"><p><? echo $job_no;?></p></td>
													<td width="80"><p><? echo $buyer_name; ?></p></td>
													<td width="80"><p><? echo $po_number; ?></p></td>
													<td width="80"><p><? echo $style_ref_no; ?>&nbsp;</p></td>
                                                    <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
													<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
													<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="60"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="80"><p>&nbsp;</p></td>
													<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
													<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
													<td width="120"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><?  echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td> 
													<td width="80" align="right"><p>&nbsp;</p></td>
                                                    <td width="80"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
													<td width="80" align="right"><p>&nbsp;</p></td>
													<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
													<td width="60" align="right">&nbsp;</td>
													<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
													<td width="60" align="right"><p>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
													<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
													<?
														$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
													?>
													<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
													<td width="50" align="center"></td>
													<td width="50" align="center"></td>
												</tr>
											<?	
												} 
										
												$i++;
												
												$grand_tot_issue_retn_qty+=$issue_retn;
												$grand_tot_recv_retn_qty+=$recv_retn;
												$grand_tot_stock+=$stock;
												$grand_tot_rec_bal+=$rec_bal;
												$grand_tot_issue_bal+=$issue_bal;
												$return_array[]=$val3;
												
												
												$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
												$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
												$gsm_string=$product_array[$value[1]]['gsm'];
												$fdia_string=$product_array[$value[1]]['dia_width'];
												$stich_length_string=implode(",",array_unique(explode(",",$row[csf('stitch_length')])));
												
											}
										}
									}
									
								
								}
								$order_arr[]=$row[csf('po_breakdown_id')];
								$k++;
							
							
							
							
							}
							
							
							
							//--------------------------------------------------------------------------start
								 
							$job_no=$row[csf('job_no')];	
							$style_ref_no=$row[csf('style_ref_no')];	
							$buyer_name=$buyer_arr[$row[csf('buyer_name')]];	
							$buyer_id=$row[csf('buyer_name')];	
							$po_number=$row[csf('po_number')];	
							$break_down_id=$row[csf('po_breakdown_id')];	
							$construction_data=$constructionArr[$product_array[$prod_id]['detarmination_id']];	
							$dyeing_color_string=$color_name;
							$colorId_string=$colorId;	
								
								 //echo $grey_qnty_array['26OG-16-000016147Single JerseyBLACK'].'*';
								/*$colorData_prv='';
								$colorDataArr=explode(',',$dyeing_color_string_prv);
								foreach($colorDataArr as $cd){if($colorData_prv=='')$colorData_prv=$cd;}
								
								$colorData='';
								$colorDataArr=explode(',',$color_name);
								foreach($colorDataArr as $cd){if($colorData=='')$colorData=$cd;}*/
								
								//$colorId_string_prv=$colorId;
								
								
								$groupKey = $buyer_id.$job_no.$break_down_id.$construction_data.$colorId_string;
								$groupKeyReq = $buyer_id_prv.$job_no_prv.$break_down_id_prv.$construction_data_prv.$colorId_string_prv;
								$grey_qnty=$grey_qnty_array[$groupKeyReq];	
								
							
								//echo $groupKeyReq.'<br>';
								//echo $buyer_id.'**'.$break_down_id.'**'.$job_no.'**'.$construction_data.'**'.$colorData.'<br>';
								if(!in_array($groupKey,$con_arr)){
									if($ttt!=1)
									{
										$total_grey_qnty+=$grey_qnty;
									?>
										<tr class="tbl_bottom">
											<td width="30"></td>
											<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $job_no_prv;?></p></td>
											<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $buyer_name_prv;?></p></td>
											<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $po_number_prv;?></p></td>
											<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $style_ref_no_prv;?></p></td>
											<td width="100"></td>
											<td width="" style="color:#E2E2E2;"><p><? echo $construction_data_prv;?></p></td>
											<td width="100" style="color:#E2E2E2;"><p><? echo $composition_string_prv;?></p></td>
											<td width="60" style="color:#E2E2E2;"><p><? echo $gsm_string;?></p></td>
											<td width="60" style="color:#E2E2E2;"><p><? echo $fdia_string;?></p></td>
											<td width="60"></td>
											<td width="60"></td>
											<td width="80" style="color:#E2E2E2;"><p><? echo $dyeing_color_string_prv;?></p></td>
											<td width="80"></td>
											<td width="60"></td>
											<td width="80"></td>
											<td width="80"></td>
											<td width="120"></td>
											<td width="80" align="right"><p><b><? echo number_format($grey_qnty,2,'.','');?></b></p></td>
											<td width="80" align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?>&nbsp;</td>
											<td width="80"  align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?>&nbsp;</td>
											<td width="80" align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
											<td width="80" align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?>&nbsp;</td>
											<td width="60" align="right"><? echo $tot_rec_roll; ?>&nbsp;</td>
											<td width="80" align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
											<td width="80" align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
											<td width="80" align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
											<td width="80" align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?>&nbsp;</td>
											<td width="60" align="right"><? echo $tot_issue_roll; ?>&nbsp;</td>
											<td width="80" align="right"><? //echo number_format($tot_stock,2,'.',''); ?>&nbsp;</td>
											<td width="60" align="right"><? //echo $tot_stock_roll_qty; ?>&nbsp;</td>
											<td width="50" align="right">&nbsp;</td>
											<td width="50" align="right">&nbsp;</td>
											<td width="50" align="right">&nbsp;</td>
											
											<td  width="50" align="right"><? echo number_format($tot_rec_bal-$grey_qnty,2,'.',''); //?></td>
											<td  width="50" align="right"><? echo number_format($tot_issue_bal-$grey_qnty,2,'.','');// ?></td>
										</tr>
									<?
										unset($colorData);
										unset($colorId_string);
										unset($grey_qnty);
										unset($dyeing_color_string);
										unset($colorId_string);
										unset($break_down_id);
										
										unset($tot_req_qty);
										unset($tot_rec_qty);
										unset($tot_transfer_in_qty);
										unset($tot_rec_bal);
										unset($tot_rec_roll);
										unset($tot_issue_qty);
										unset($tot_transfer_out_qty);
										unset($tot_issue_bal);
										unset($tot_issue_roll);
										unset($tot_stock);
										unset($tot_stock_roll_qty);
										unset($tot_iss_retn_qty);
										unset($tot_recv_retn_qty);
									
									}
									$ttt++;
									
								}
							$con_arr[]=$groupKey;
							//-----------------------------------------------------------------------------end
							
							
							
							
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							if($row[csf('self')]=="") $row[csf('self')]=0;
							$trnsfer_in_qty=$transfer_in_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$trnsfer_in_roll=$transfer_in_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];
							$trnsfer_out_qty=$transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$trnsfer_out_roll=$transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];
							$issue_qty=$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['qty'];
							$issue_roll=$issue_qty_roll_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['roll'];
							
							
							$issue_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['iss'];
							$recv_retn=$retn_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('yarn_count')]][$row[csf('yarn_lot')]][$row[csf('rack')]][$row[csf('self')]]['recv'];
							//print_r($transfer_out_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]);
							$rec_bal=$row[csf('quantity')]+$trnsfer_in_qty+$issue_retn;
							 $issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
							 $stock=$rec_bal-$issue_bal; 
							if($cbo_value_with==1 && $stock>=0) 
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i; ?></td>
									<td width="80"><p><? echo $job_no;?></p></td>
									<td width="80"><p><? echo $buyer_name; ?></p></td>
									<td width="80"><p><? echo $po_number; ?></p></td>
									<td width="80"><p><? echo $style_ref_no; ?></p></td>
									<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
									<td width=""><p><? echo $constructionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
									<td width="100"><p><? echo $copmpositionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['gsm']; ?></p></td>
									<td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['dia_width']; ?></p></td>
									<td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
									<td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?></p></td>
									<td width="80"><p><? echo $color_name;//$color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $color_range[$row[csf('color_range_id')]]; ?>&nbsp;</p></td>
									<td width="60"><p><? echo $count_val; ?>&nbsp;</p></td> 
									<td width="80"><p><? echo $brand_name;//$brand_arr[$product_array[$row[csf('prod_id')]]['brand']]; ?>&nbsp;</p></td>
									<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
									<td width="120"><p><? echo implode(",",array_unique(explode(",",$row[csf('booking_no')]))); ?>&nbsp;</p></td>
									<td width="80"><p>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); $tot_rec_qty+=$row[csf('quantity')]; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
									<td width="60" align="right"><p><? $rec_roll=$row[csf('rec_roll')]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
									<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
									<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
									<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
									<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
									<?
										$daysOnHand = datediff("d",change_date_format($transaction_date_array[$row[csf('prod_id')]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
									?>
									<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
									<td width="50" align="center"><p><? //echo $rec_bal; ?></p></td>
									<td width="50" align="center"><p><? //echo $issue_bal; ?></p></td>
								</tr>
								<?
								$prev_order_id=$row[csf('po_breakdown_id')];
                                $i++;
                                $grand_tot_rec_qty+=$row[csf('quantity')];
                                $grand_tot_transfer_in_qty+=$trnsfer_in_qty;
                                $grand_tot_rec_bal+=$rec_bal;
                                //echo $rec_roll."**".$i."<br>";
                                $grand_tot_rec_roll+=$rec_roll;
                                $grand_tot_issue_qty+=$issue_qty;
                                $grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                                $grand_tot_issue_bal+=$issue_bal;
                                $grand_tot_issue_roll+=$iss_roll;
                                $grand_tot_stock+=$stock;
                                $grand_tot_roll_qty+=$stock_roll_qty;
                                $grand_tot_issue_retn_qty+=$issue_retn;
                                $grand_tot_recv_retn_qty+=$recv_retn;
                                
                                $composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
                                $gsm_string=$product_array[$value[1]]['gsm'];
                                $fdia_string=$product_array[$value[1]]['dia_width'];
                                
                                
                                $colorId_string_prv=$colorId;
                                $dyeing_color_string_prv=$color_name;
                                $job_no_prv=$row[csf('job_no')];	
                                $style_ref_no_prv=$row[csf('style_ref_no')];	
                                $buyer_name_prv=$buyer_arr[$row[csf('buyer_name')]];	
                                $buyer_id_prv=$row[csf('buyer_name')];	
                                $po_number_prv=$row[csf('po_number')];	
                                $break_down_id_prv=$row[csf('po_breakdown_id')];	
                                $construction_data_prv=$constructionArr[$product_array[$prod_id]['detarmination_id']];
							}
							else if($cbo_value_with==2 && $stock>0) 
							{ 
								?>
                            	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                    <td width="30"><? echo $i; ?></td>
                                    <td width="80"><p><? echo $job_no;?></p></td>
                                    <td width="80"><p><? echo $buyer_name; ?></p></td>
                                    <td width="80"><p><? echo $po_number; ?></p></td>
                                    <td width="80"><p><? echo $style_ref_no; ?></p></td>
                                    <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
                                    <td width=""><p><? echo $constructionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
                                    <td width="100"><p><? echo $copmpositionArr[$product_array[$row[csf('prod_id')]]['detarmination_id']]; ?></p></td>
                                    <td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['gsm']; ?></p></td>
                                    <td width="60"><p><? echo $product_array[$row[csf('prod_id')]]['dia_width']; ?></p></td>
                                    <td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                                    <td width="60"><p><? echo implode(",",array_unique(explode(",",$row[csf('stitch_length')]))); ?></p></td>
                                    <td width="80"><p><? echo $color_name;//$color_arr[$row[csf('color_id')]]; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $color_range[$row[csf('color_range_id')]]; ?>&nbsp;</p></td>
                                    <td width="60"><p><? echo $count_val; ?>&nbsp;</p></td> 
                                    <td width="80"><p><? echo $brand_name;//$brand_arr[$product_array[$row[csf('prod_id')]]['brand']]; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
                                    <td width="120"><p><? echo implode(",",array_unique(explode(",",$row[csf('booking_no')]))); ?>&nbsp;</p></td>
                                    <td width="80"><p></p></td>
                                    <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); $tot_rec_qty+=$row[csf('quantity')]; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($issue_retn,2); $tot_iss_retn_qty+=$issue_retn; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
                                    <td width="60" align="right"><p><? $rec_roll=$row[csf('rec_roll')]+$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
                                    <td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($recv_retn,2); $tot_recv_retn_qty +=$recv_retn; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                                    <td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
                                    <td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
                                    <td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
                                    <td width="50" align="center"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
                                    <td width="50" align="center"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
                                    <?
                                        $daysOnHand = datediff("d",change_date_format($transaction_date_array[$row[csf('prod_id')]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                                    ?>
                                    <td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                                    <td width="50" align="center"><p><? //echo $rec_bal; ?></p></td>
                                    <td width="50" align="center"><p><? //echo $issue_bal; ?></p></td>
                                </tr>
                                <? 
                                $prev_order_id=$row[csf('po_breakdown_id')];
                                $i++;
                                $grand_tot_rec_qty+=$row[csf('quantity')];
                                $grand_tot_transfer_in_qty+=$trnsfer_in_qty;
                                $grand_tot_rec_bal+=$rec_bal;
                                //echo $rec_roll."**".$i."<br>";
                                $grand_tot_rec_roll+=$rec_roll;
                                $grand_tot_issue_qty+=$issue_qty;
                                $grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                                $grand_tot_issue_bal+=$issue_bal;
                                $grand_tot_issue_roll+=$iss_roll;
                                $grand_tot_stock+=$stock;
                                $grand_tot_roll_qty+=$stock_roll_qty;
                                $grand_tot_issue_retn_qty+=$issue_retn;
                                $grand_tot_recv_retn_qty+=$recv_retn;
                                
                                $composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
                                $gsm_string=$product_array[$value[1]]['gsm'];
                                $fdia_string=$product_array[$value[1]]['dia_width'];
                                
                                
                                $colorId_string_prv=$colorId;
                                $dyeing_color_string_prv=$color_name;
                                $job_no_prv=$row[csf('job_no')];	
                                $style_ref_no_prv=$row[csf('style_ref_no')];	
                                $buyer_name_prv=$buyer_arr[$row[csf('buyer_name')]];	
                                $buyer_id_prv=$row[csf('buyer_name')];	
                                $po_number_prv=$row[csf('po_number')];	
                                $break_down_id_prv=$row[csf('po_breakdown_id')];	
                                $construction_data_prv=$constructionArr[$product_array[$prod_id]['detarmination_id']];
							}
								
							
							
							
							//if($colorData=='RED'){echo $colorData;die;}
							
							
							
							
						}
						//var_dump($trans_in_array);
						//if($dyeing_color_string=='BLACK'){echo $dyeing_color_string.'='.$ttt;die;}
						
						foreach($trans_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];
							
							if($po_id==$prev_order_id )
							{
								if(!in_array($val3,$trans_in_array))
								{
									$trnsfer_in_qty=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_in_roll=$transfer_in_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									
									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
									$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
			
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
									$count=explode(',',$value[2]); $count_value='';
									foreach ($count as $count_id)
									{
										if($count_id>0) { if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
									}
									
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$trnsfer_in_qty+$issue_retn;
									$issue_bal=$issue_qty+$trnsfer_out_qty+$recv_retn;
									$stock=$rec_bal-$issue_bal;
									if($cbo_value_with==1 && $stock>=0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
											<td width="120"><p>&nbsp;</p></td>
											
											<td width="80" align="right"><p>&nbsp;</p></td>
											
											
											<td width="80" align="right"><p>&nbsp;<? //echo $val3;?></p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
											<td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
											<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
											
											
											<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
											<td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
											<?
												$daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
											?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											<td width="50" align="center"><p></p></td>
											<td width="50" align="center"><p></p></td>
										</tr>
										<?
										$i++;
										$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_rec_roll+=$rec_roll;
										$grand_tot_issue_qty+=$issue_qty;
										$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
										$grand_tot_issue_bal+=$issue_bal;
										$grand_tot_issue_roll+=$iss_roll;
										$grand_tot_stock+=$stock;
										$grand_tot_roll_qty+=$stock_roll_qty;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										
										$issue_array[]=$val3;
										$return_array[]=$val3;
										
										
										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];	
                                    }
									else if($cbo_value_with==2 && $stock>0) 
									{ 
										?>
                                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                            <td width="30"><? echo $i; ?></td>
                                            <td width="80"><p><? echo $job_no;?></p></td>
                                            <td width="80"><p><? echo $buyer_name; ?></p></td>
                                            <td width="80"><p><? echo $po_number; ?></p></td>
                                            <td width="80"><p><? echo $style_ref_no; ?></p></td>
                                            <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
                                            <td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                                            <td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                                            <td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                                            <td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?>&nbsp;</p></td>
                                            <td width="60"><p>&nbsp;</p></td>
                                            <td width="60"><p>&nbsp;</p></td>
                                            <td width="80"><p>&nbsp;</p></td>
                                            <td width="80"><p>&nbsp;</p></td>
                                            <td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
                                            <td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
                                            <td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
                                            <td width="120"><p>&nbsp;</p></td>
                                            
                                            <td width="80" align="right"><p>&nbsp;</p></td>
                                            
                                            
                                            <td width="80" align="right"><p>&nbsp;<? //echo $val3;?></p></td>
                                            <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
                                            <td width="80" align="right"><p><? echo number_format($trnsfer_in_qty,2); $tot_transfer_in_qty+=$trnsfer_in_qty; ?>&nbsp;</p></td>
                                            <td width="80" align="right"><p><?  echo number_format($rec_bal,2); $tot_rec_bal+=$rec_bal; ?>&nbsp;</p></td>
                                            <td width="60" align="right"><p><? $rec_roll=$trnsfer_in_roll; echo $rec_roll; $tot_rec_roll+=$rec_roll; ?>&nbsp;</p></td> 
                                            <td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                            <td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
                                            
                                            
                                            <td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty; ?>&nbsp;</p></td>
                                            <td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                                            <td width="60" align="right"><p><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</p></td>
                                            <td width="80" align="right"><p><?  echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
                                            <td width="60" align="right"><p><? $stock_roll_qty=$rec_roll-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty;?>&nbsp;</p></td>
                                            <td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
                                            <td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
                                            <?
                                                $daysOnHand=datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                                            ?>
                                            <td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                                            <td width="50" align="center"><p></p></td>
                                            <td width="50" align="center"><p></p></td>
										</tr>			
										<? 
										$i++;
										$grand_tot_transfer_in_qty+=$trnsfer_in_qty;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_rec_roll+=$rec_roll;
										$grand_tot_issue_qty+=$issue_qty;
										$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
										$grand_tot_issue_bal+=$issue_bal;
										$grand_tot_issue_roll+=$iss_roll;
										$grand_tot_stock+=$stock;
										$grand_tot_roll_qty+=$stock_roll_qty;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										
										$issue_array[]=$val3;
										$return_array[]=$val3;
										
										
										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
									
									//$dyeing_color_string=$color_name;
									//$colorId_string=$colorId;
									
								}
							}
						}
						
						foreach($isuue_data_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];
							
							$count=explode(',',$value[2]); $count_value='';
							foreach ($count as $count_id)
							{
								if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
							}
							
							if($po_id==$prev_order_id)
							{
								if(!in_array($val3,$issue_array))
								{
									$issue_qty=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$issue_roll=$issue_qty_roll_array[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									$trnsfer_out_qty=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['qty'];
									$trnsfer_out_roll=$transfer_out_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['roll'];
									
									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
																				
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									$rec_bal=$issue_retn; $issue_bal=$issue_qty+$trnsfer_out_qty;
									 $stock=$rec_bal-$issue_bal;
									if($cbo_value_with==1 && $stock>=0) 
									{
										?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="80"><p><? echo $job_no;?></p></td>
												<td width="80"><p><? echo $buyer_name; ?></p></td>
												<td width="80"><p><? echo $po_number; ?></p></td>
												<td width="80"><p><? echo $style_ref_no; ?></p></td>
												<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
												<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
												<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
												<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
												<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
												<td width="60"><p>&nbsp;</p></td>
												<td width="60"><p>&nbsp;</p></td>
												<td width="80"><p>&nbsp;</p></td>
												<td width="80"><p>&nbsp;</p></td>
												<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
												<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
												
												
												<td width="80"><p><? echo $value[3]; ?></p></td>
												<td width="120"><p>&nbsp;</p></td>
												<td width="80"><p>&nbsp;</p></td>
												<td width="80" align="right"><p>&nbsp;</p></td>
												<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
												<td width="80" align="right"><p>&nbsp;</p></td>
												<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
												<td width="60" align="right"><p>&nbsp;</p></td> 
												<td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
												<td width="80" align="right"><p>&nbsp;</p></td>
												<td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
												<td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
												<td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
												<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
												<td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
												<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
												<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
												<?
													$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
												?>
												<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
											
											
												<td width="50" align="center"><p><? //echo $grand_tot_rec_bal; ?></p></td>
												<td width="50" align="center"><p><? //echo $grand_tot_issue_bal; ?></p></td>
											
											
											</tr>
											<?
                                            $i++;
                                            $grand_tot_issue_qty+=$issue_qty;
                                            $grand_tot_transfer_out_qty+=$trnsfer_out_qty;
                                            
                                            $grand_tot_issue_bal+=$issue_bal;
                                            $grand_tot_rec_bal+=$rec_bal;
                                            
                                            $grand_tot_issue_roll+=$iss_roll;
                                            $grand_tot_stock+=$stock;
                                            
                                            $grand_tot_roll_qty+=$stock_roll_qty;
                                            $grand_tot_issue_retn_qty+=$issue_retn;
                                            
                                            $return_array[]=$val3;
                                            
                                            $constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
                                            $composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
                                            $gsm_string=$product_array[$value[1]]['gsm'];
                                            $fdia_string=$product_array[$value[1]]['dia_width']; 	
										}
										else if($cbo_value_with==2 && $stock>0) 
										{ 
											?>
												
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                                <td width="30"><? echo $i; ?></td>
                                                <td width="80"><p><? echo $job_no;?></p></td>
                                                <td width="80"><p><? echo $buyer_name; ?></p></td>
                                                <td width="80"><p><? echo $po_number; ?></p></td>
                                                <td width="80"><p><? echo $style_ref_no; ?></p></td>
                                                <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
                                                <td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                                                <td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                                                <td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                                                <td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                                                <td width="60"><p>&nbsp;</p></td>
                                                <td width="60"><p>&nbsp;</p></td>
                                                <td width="80"><p>&nbsp;</p></td>
                                                <td width="80"><p>&nbsp;</p></td>
                                                <td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
                                                <td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
                                                
                                                
                                                <td width="80"><p><? echo $value[3]; ?></p></td>
                                                <td width="120"><p>&nbsp;</p></td>
                                                <td width="80"><p>&nbsp;</p></td>
                                                <td width="80" align="right"><p>&nbsp;</p></td>
                                                <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
                                                <td width="80" align="right"><p>&nbsp;</p></td>
                                                <td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
                                                <td width="60" align="right"><p>&nbsp;</p></td> 
                                                <td width="80" align="right"><p><? echo number_format($issue_qty,2); $tot_issue_qty+=$issue_qty; ?>&nbsp;</p></td>
                                                <td width="80" align="right"><p>&nbsp;</p></td>
                                                <td width="80" align="right"><p><? echo number_format($trnsfer_out_qty,2); $tot_transfer_out_qty+=$trnsfer_out_qty;?>&nbsp;</p></td>
                                                <td width="80" align="right"><p><?  echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                                                <td width="60" align="right"><? $iss_roll=$issue_roll-$trnsfer_out_roll; echo $iss_roll; $tot_issue_roll+=$iss_roll; ?>&nbsp;</td>
                                                <td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
                                                <td width="60" align="right"><p><? $stock_roll_qty=-$iss_roll; echo $stock_roll_qty; $tot_stock_roll_qty+=$stock_roll_qty; ?>&nbsp;</p></td>
                                                <td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
                                                <td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
                                                <?
                                                    $daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                                                ?>
                                                <td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                                            
                                            
                                                <td width="50" align="center"><p><? //echo $grand_tot_rec_bal; ?></p></td>
                                                <td width="50" align="center"><p><? //echo $grand_tot_issue_bal; ?></p></td>
										</tr> 
										<?
										$i++;
										$grand_tot_issue_qty+=$issue_qty;
										$grand_tot_transfer_out_qty+=$trnsfer_out_qty;
										
										$grand_tot_issue_bal+=$issue_bal;
										$grand_tot_rec_bal+=$rec_bal;
										
										$grand_tot_issue_roll+=$iss_roll;
										$grand_tot_stock+=$stock;
										
										$grand_tot_roll_qty+=$stock_roll_qty;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_total_rec_qty+=$tot_rec_qty;
										
										$return_array[]=$val3;
										
										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width']; 
									}
									
									//$dyeing_color_string=$color_name;
									//$colorId_string=$colorId;
									
									
								}
							}
						}
						
						foreach($retn_data_arr as $key=>$val3)
						{
							$value=explode("_",$val3);
							$po_id=$value[0];
							
							$count=explode(',',$value[2]); $count_value='';
							foreach ($count as $count_id)
							{
								if($count_id>0){ if($count_value=='') $count_value=$count_arr[$count_id]; else $count_value.=",".$count_arr[$count_id]; }
							}
							
							if($po_id==$prev_order_id)
							{
								if(!in_array($val3,$return_array))
								{
									$issue_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['iss'];
									$recv_retn=$retn_arr[$po_id][$value[1]][$value[2]][$value[3]][$value[4]][$value[5]]['recv'];
																				
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$program_no=implode(",",array_unique(explode(",",$program_no_array[$po_id])));
									 $rec_bal=$issue_retn;  $issue_bal=$recv_retn;
									 $stock=$rec_bal-$issue_bal;
									 if($cbo_value_with==1 && $stock>=0) 
									{
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="30"><? echo $i; ?></td>
											<td width="80"><p><? echo $job_no;?></p></td>
											<td width="80"><p><? echo $buyer_name; ?></p></td>
											<td width="80"><p><? echo $po_number; ?></p></td>
											<td width="80"><p><? echo $style_ref_no; ?></p></td>
											<td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
											<td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
											<td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="60"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
											<td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
											<td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
											<td width="120"><p>&nbsp;</p></td>
											<td width="80"><p>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
											<td width="60" align="right"><p>&nbsp;</p></td> 
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
											<td width="80" align="right"><p>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
											<td width="60" align="right">&nbsp;</td>
											<td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
											<td width="60" align="right"><p>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
											<td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
											<?
												$daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
											?>
											<td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                                            <td width="50" align="center"></td>
										   <td width="50" align="center"></td>
										
										</tr>
										<?
										$i++;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										$grand_tot_stock+=$stock;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_issue_bal+=$issue_bal;
										
										$return_array[]=$val3;
										
										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];	
                                    }
									else if($cbo_value_with==2 && $stock>0)
									{ 
										?>
                                        <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
                                            <td width="30"><? echo $i; ?></td>
                                            <td width="80"><p><? echo $job_no;?></p></td>
                                            <td width="80"><p><? echo $buyer_name; ?></p></td>
                                            <td width="80"><p><? echo $po_number; ?></p></td>
                                            <td width="80"><p><? echo $style_ref_no; ?></p></td>
                                            <td width="100"><p><? echo $program_no; ?>&nbsp;</p></td>
                                            <td width=""><p><? echo $constructionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                                            <td width="100"><p><? echo $copmpositionArr[$product_array[$value[1]]['detarmination_id']]; ?></p></td>
                                            <td width="60"><p><? echo $product_array[$value[1]]['gsm']; ?></p></td>
                                            <td width="60"><p><? echo $product_array[$value[1]]['dia_width']; ?></p></td>
                                            <td width="60"><p>&nbsp;</p></td>
                                            <td width="60"><p>&nbsp;</p></td>
                                            <td width="80"><p>&nbsp;</p></td>
                                            <td width="80"><p>&nbsp;</p></td>
                                            <td width="60"><p><? echo $count_value; ?>&nbsp;</p></td> 
                                            <td width="80"><p><? echo $brand_arr[$product_array[$value[1]]['brand']]; ?>&nbsp;</p></td>
                                            <td width="80"><p><? echo $value[3]; ?>&nbsp;</p></td>
                                            <td width="80"><p>&nbsp;</p></td>
                                            <td width="120"><p>&nbsp;</p></td>
                                            <td width="80" align="right"><p>&nbsp;</p></td>
                                            <td width="80" align="right"><p><? echo number_format($issue_retn,2);$tot_iss_retn_qty+=$issue_retn;?>&nbsp;</p></td>
                                            <td width="80" align="right"><p>&nbsp;</p></td>
                                            <td width="80" align="right"><p><? echo number_format($rec_bal,2);$tot_rec_bal+=$rec_bal;?>&nbsp;</p></td>
                                            <td width="60" align="right"><p>&nbsp;</p></td> 
                                            <td width="80" align="right"><p>&nbsp;</p></td>
                                            <td width="80" align="right"><p><? echo number_format($recv_retn,2);$tot_recv_retn_qty+=$recv_retn;?>&nbsp;</p></td>
                                            <td width="80" align="right"><p>&nbsp;</p></td>
                                            <td width="80" align="right"><p><? echo number_format($issue_bal,2); $tot_issue_bal+=$issue_bal; ?></p></td>
                                            <td width="60" align="right">&nbsp;</td>
                                            <td width="80" align="right"><p><? echo number_format($stock,2); $tot_stock+=$stock; ?>&nbsp;</p></td>
                                            <td width="60" align="right"><p>&nbsp;</p></td>
                                            <td width="50" align="center"><p><? echo $value[4]; ?>&nbsp;</p></td>
                                            <td width="50" align="center"><p><? echo $value[5]; ?>&nbsp;</p></td>
                                            <?
                                                $daysOnHand = datediff("d",change_date_format($transaction_date_array[$value[1]]['max_date'],'','',1),change_date_format(date("Y-m-d"),'','',1));
                                            ?>
                                            <td width="50" align="center"><p><? if($stock>0) echo $daysOnHand; ?></p></td>
                                            <td width="50" align="center"></td>
											<td width="50" align="center"></td>
									
                                        </tr>
                                        <? 
										$i++;
										$grand_tot_issue_retn_qty+=$issue_retn;
										$grand_tot_recv_retn_qty+=$recv_retn;
										$grand_tot_stock+=$stock;
										$grand_tot_rec_bal+=$rec_bal;
										$grand_tot_issue_bal+=$issue_bal;
										
										$return_array[]=$val3;
										
										$constraction_string=$constructionArr[$product_array[$value[1]]['detarmination_id']];
										$composition_string=$copmpositionArr[$product_array[$value[1]]['detarmination_id']];
										$gsm_string=$product_array[$value[1]]['gsm'];
										$fdia_string=$product_array[$value[1]]['dia_width'];
									}
									
									
									
								}
							}
						}
						
							
							
							
							
							//$groupKey = $row[csf('buyer_name')].$row[csf('job_no')].$row[csf('po_breakdown_id')].$constructionArr[$product_array[$prod_id]['detarmination_id']].$colorData;
								/*$colorData='';
								$colorDataArr=explode(',',$dyeing_color_string_prv);
								foreach($colorDataArr as $cd){if($colorData=='')$colorData=$cd;}
								$groupKey = $buyer_id.$job_no.$break_down_id.$construction_data.$colorData;
								$groupKeyReq = $buyer_id_prv.$job_no_prv.$break_down_id_prv.$construction_data_prv.$colorData;
								$grey_qnty=$grey_qnty_array[$groupKeyReq];*/	
						
								$groupKeyReq = $buyer_id_prv.$job_no_prv.$break_down_id_prv.$construction_data_prv.$colorId_string_prv;
								$grey_qnty=$grey_qnty_array[$groupKeyReq];
								$total_grey_qnty+=$grey_qnty;
						?>
						<tr class="tbl_bottom">
										<td width="30">&nbsp;</td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $job_no;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $buyer_name;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $po_number;?></p></td>
										<td width="80" style="color:#E2E2E2;" align="center"><p><? echo $style_ref_no;?></p></td>
										<td width="100">&nbsp;</td>
                                        <td width="" style="color:#E2E2E2;"><p><? echo $construction_data_prv;?></p></td>
										<td width="100" style="color:#E2E2E2;"><p><? echo $composition_string;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? echo $gsm_string;?></p></td>
										<td width="60" style="color:#E2E2E2;"><p><? echo $fdia_string;?></p></td>
										<td width="60">&nbsp;</td>
										<td width="60"></td>
										<td width="80" style="color:#E2E2E2;"><p><? echo $dyeing_color_string_prv;?></p></td>
										<td width="80"></td>
										<td width="60"></td>
										<td width="80"></td>
										<td width="80"></td>
										<td width="120">&nbsp;</td>
										<td width="80" align="right"><p><? echo number_format($grey_qnty,2,'.','');?></p></td>
										<td width="80" align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?>&nbsp;</td>
                                        <td width="80" align="right"><? echo number_format($tot_iss_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_rec_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $tot_rec_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_issue_qty,2,'.',''); ?>&nbsp;</td>
                                        <td width="80" align="right"><? echo number_format($tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($tot_issue_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $tot_issue_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? //echo number_format($tot_stock,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? //echo $tot_stock_roll_qty; ?>&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
                                        
										<td width="50" align="right"><p><? echo number_format($tot_rec_bal-$grey_qnty,2,'.',''); //?></p></td>
										<td width="50" align="right"><p><? echo number_format($tot_issue_bal-$grey_qnty,2,'.','');// ?></p></td>
									</tr>
                                    
                                    <tr class="tbl_bottom">
										<td width="30" align="right" colspan="18">Grand Total</td>
										
										<td width="80" align="right"><p><? echo number_format($total_grey_qnty,2,'.','');?></p></td>
										<td width="80" align="right"><? echo number_format($grand_tot_rec_qty,2,'.',''); ?>&nbsp;</td>
                                        <td width="80" align="right"><? echo number_format($grand_tot_issue_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($grand_tot_transfer_in_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($grand_tot_rec_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $grand_tot_rec_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($grand_tot_issue_qty,2,'.',''); ?>&nbsp;</td>
                                        <td width="80" align="right"><? echo number_format($grand_tot_recv_retn_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($grand_tot_transfer_out_qty,2,'.',''); ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($grand_tot_issue_bal,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $grand_tot_issue_roll; ?>&nbsp;</td>
										<td width="80" align="right"><? echo number_format($grand_tot_stock,2,'.',''); ?>&nbsp;</td>
										<td width="60" align="right"><? echo $grand_tot_roll_qty; ?>&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
										<td width="50" align="right">&nbsp;</td>
                                        
										<td width="50" align="right"><p><? echo number_format($grand_tot_rec_bal-$total_grey_qnty,2,'.',''); //?></p></td>
										<td width="50" align="right"><p><? echo number_format($grand_tot_issue_qty-$total_grey_qnty,2,'.','');// ?></p></td>
									</tr>
                                   
						
					</table>
				</div>
			</fieldset>
		<?
		
		}
	}
	
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
    echo "$html####$filename####$rpt_type"; 
    exit();
}

if($action=="fabric_booking_popup")
{
 	echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
?>
	<fieldset style="width:890px">
        <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
            <thead>
                <th width="40">SL</th>
                <th width="60">Booking No</th>
                <th width="50">Year</th>
                <th width="60">Type</th>
                <th width="80">Booking Date</th>
                <th width="90">Color</th>
                <th width="110">Fabric</th>
                <th width="150">Composition</th>
                <th width="70">GSM</th>
                <th width="70">Dia</th>
                <th>Grey Req. Qty.</th>
            </thead>
        </table>
        <div style="width:100%; max-height:320px; overflow-y:scroll">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			<?
                if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
                else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
                else $year_field="";//defined Later
				
				$i=1; $tot_grey_qnty=0;
                $sql="select a.id, $year_field, a.booking_no_prefix_num, a.booking_type, a.is_short, a.booking_date, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width, sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id) and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.grey_fab_qnty>0 group by a.id, a.booking_type, a.is_short, a.booking_date, a.insert_date, a.booking_no_prefix_num, b.fabric_color_id, b.construction, b.copmposition, b.gsm_weight, b.dia_width order by a.id";
               //echo $sql;//die;
                $result= sql_select($sql);
                foreach($result as $row)
                {
                    if($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
					
					if($row[csf('booking_type')]==4) 
					{
						$booking_type="Sample";
					}
					else
					{
						if($row[csf('is_short')]==1) $booking_type="Short"; else $booking_type="Main"; 
					}
               	?>
                   <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40"><? echo $i; ?></td>
                        <td width="60">&nbsp;&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="50" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="60" align="center"><p><? echo $booking_type; ?></p></td>
                        <td width="80" align="center"><? if($row[csf('booking_date')]!="0000-00-00") echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
                        <td width="90"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?></p></td>
                        <td width="110"><p><? echo $row[csf('construction')]; ?></p></td>
                        <td width="150"><p><? echo $row[csf('copmposition')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('gsm_weight')]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('dia_width')]; ?></p></td>
                        <td align="right" style="padding-right:5px"><? echo number_format($row[csf('grey_fab_qnty')],2); ?></td>
                    </tr>
                <? 
					$tot_grey_qnty+=$row[csf('grey_fab_qnty')];
					$i++;
                } 
            ?>
            	<tfoot>
                	<th colspan="10">Total</th>
                    <th style="padding-right:5px"><? echo number_format($tot_grey_qnty,2); ?></th>
                </tfoot>
			</table>
		</div> 
    </fieldset>
<?
exit();
}

