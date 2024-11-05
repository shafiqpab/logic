<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

if($action=="party_popup")
{
	echo load_html_head_contents("Party Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$exdata=explode("_",$data);
	$company_id=$exdata[0];
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hide_party_id').val( id );
			$('#hide_party_name').val( name );
		}
    </script>
        <input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
        <input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?
	
	if ($_SESSION['logic_erp']["data_level_secured"]==1)
	{
		if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
	}
	else
	{
		$buyer_id_cond="";
	}
	
	$sql="select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_id_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buyer_name";
	
	echo create_list_view("tbl_list_search", "Party Name", "370","370","260",0, $sql , "js_set_value", "id,buyer_name", "", 1, "0", $arr , "buyer_name", "",'setFilterGrid("tbl_list_search",-1);','0','',1) ;
	
   exit(); 
}

if ($action=="job_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('selected_order').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                        </tr>
                    	<tr>               	 
                            <th width="140">Company Name</th>
                            <th width="140">Party Name</th>
                            <th width="170">Date Range</th>
                            <th width="100">Search Job</th>
                            <th width="100">Search Order</th>
                            <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                        </tr>         
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="hidden" id="selected_order">  
								<?   
									$data=explode("_",$data);
									echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[1],"",1 );
                                ?>
                            </td>
                            <td id="buyer_td">
								<? echo create_drop_down( "cbo_party_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[1]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --","", "","" );   	 
								?>
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                            </td> 
                            <td>
								<input type="text" name="txt_search_job" id="txt_search_job" class="text_boxes" style="width:100px" placeholder="Job" />
                            </td>
                            <td align="center" id="search_by_td">
                                <input type="text" name="txt_search_order" id="txt_search_order" class="text_boxes" style="width:100px" placeholder="Order" />
                            </td>
                            <td align="center">
                                <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_job').value+'_'+document.getElementById('txt_search_order').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_job_search_list_view', 'search_div', 'party_wise_reconcilation_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center" height="40" valign="middle">
								<? echo load_month_buttons(1);  ?>
                                <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <br>
                <div id="search_div"></div>   
            </form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$search_job=str_replace("'","",$data[4]);
	$search_order=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else  $company="";
	if ($data[1]!=0) $buyer=" and party_id='$data[1]'"; else $buyer="";
	
	if($search_type==1)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num='$search_job'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no='$search_order'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job%'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order%'";
	}
	else if($search_type==2)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '$search_job%'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '$search_order%'";
	}
	else if($search_type==3)
	{
		if($search_job=='') $search_job_cond=""; else $search_job_cond="and a.job_no_prefix_num like '%$search_job'";  
		if($search_order=='') $search_order_cond=""; else $search_order_cond=" and b.order_no like '%$search_order'";
	}	
	
	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and b.order_rcv_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
	}

	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($db_type==0)	{	$date_sql="year(a.insert_date)"; }
	else if($db_type==2) {	$date_sql="TO_CHAR(a.insert_date,'YYYY')"; }

	$sql= "SELECT a.id, b.id as ord_id, a.subcon_job, a.job_no_prefix_num, $date_sql as year, a.company_id, a.location_id, a.party_id, a.status_active, b.id, b.order_no, b.main_process_id, b.order_rcv_date, b.delivery_date, b.status_active from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_rcv_date $company $buyer $search_job_cond $search_order_cond group by  a.id, b.id, a.subcon_job, a.job_no_prefix_num, $date_sql, a.company_id,a.location_id,a.party_id,a.status_active,b.id,b.order_no,b.order_rcv_date,b.delivery_date,b.status_active,b.main_process_id order by a.id DESC";

	//echo $sql;die;

	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="885">
        <thead>
            <th width="15">SL</th>
            <th width="60">Job No</th>
            <th width="60">Year</th>
            <th width="150">Order No</th>
            <th width="100">Process</th>
             <th width="150">Company</th>
            <th width="80">Ord Receive Date</th>
            <th width="80">Delivery Date</th>
        </thead>
        <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
             ?>
              <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('ord_id')]."_".$row[csf('order_no')]; ?>")' style="cursor:pointer" >
                      <td  width="25"><? echo $i; ?></td>
	                  <td  width="60"><? echo $row[csf('job_no_prefix_num')]; ?></td>
	                  <td  width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
	                  <td  width="150"><? echo $row[csf('order_no')]; ?></td>
	                  <td  width="100"><? echo $production_process[$row[csf('main_process_id')]]; ?></td>
	                  <td  width="150"><? echo $comp[$row[csf('company_id')]]; ?></td>
	                  <td  width="80" style="text-align:center;"><? echo $row[csf('order_rcv_date')]; ?></td>
	                  <td  width="80" style="text-align:center;"><? echo $row[csf('delivery_date')]; ?></td>
                </tr>
            <? 
            $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$item_desc_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');

	$batch_sql="select a.id, a.batch_no, a.extention_no, b.id as item_id, b.fabric_from, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$batch_sql_result=sql_select($batch_sql);
	foreach($batch_sql_result as $row)
	{
		$batch_array[$row[csf('id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
	}
	
	$company_id=str_replace("'","",$cbo_company_id);
	$party_ids=str_replace("'","",$txt_party_id);
	$item_category_id=str_replace("'","",$cbo_item_category);
	$order_no=str_replace("'","",$txt_order_no);
	$order_id=str_replace("'","",$order_no_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$recv_date_cond=""; $item_category_cond=""; $order_no_cond=""; $pary_cond=""; $recv_opening_blnc_cond=""; $dlvry_date_cond="";
	$dates_pass_pop_up_data=" * ";
	if($date_from && $date_to)
	{
		$dates_pass_pop_up_data=$date_from."*".$date_to;

		if($db_type==0)
		{
			$recv_date_cond= " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			$recv_opening_blnc_cond= " and a.subcon_date < '".change_date_format($date_from,'yyyy-mm-dd')."'";
			$dlvry_date_cond= " and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			$opening_dlvry_date_cond= " and a.delivery_date < '".change_date_format($date_from,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$recv_date_cond= " and a.subcon_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			$recv_opening_blnc_cond= " and a.subcon_date < '".change_date_format($date_from,'','',1)."'";
			$dlvry_date_cond= " and a.delivery_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			$opening_dlvry_date_cond= " and a.delivery_date < '".change_date_format($date_from,'','',1)."'";
		}	
	}

	if($item_category_id > 0) { $item_category_cond=" and b.item_category_id=$item_category_id"; }
	if($order_id) { $order_no_cond=" and b.order_id=$order_id"; }
	if($party_ids){ $pary_cond=" and a.party_id in ($party_ids)"; }
	
	ob_start();
	?>
	<div align="center">
	 <fieldset style="width:2000px;">
		<table cellpadding="0" cellspacing="0" width="470">
			<tr  class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="5" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="5" style="font-size:16px"><strong><? echo $company_arr[$company_id]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="5" style="font-size:12px">
					<? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
		</table>

		<!-- =================================== Report Body Part =========================================  -->

	 	<table width="2000" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" style="float: left; margin-top: 10px;">
			<thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="150">Customer</th>
                    <th width="100">Item Category</th>
                    <th width="100">Order No</th>
                    <th width="150">Item Description</th>
                    <th width="100">Order Qty</th>
                    <th width="70">UOM</th>
                    <th width="110">Rcv Opening Qty</th>
                    <th width="100">Receive Qty [Grey]</th>
                    <th width="100">Total Receive Qty [Grey]</th>
                    <th width="150">Issue Opening Qty [Finish]</th>
                    <th width="100">Issue Qty [Finish]</th>
                    <th width="100">Total Issue Qty [Finish]</th>
                    <th width="100">Issue Opening Qty [Grey]</th>
                    <th width="100">Issue Qty [Grey]</th>
                    <th width="100">Total Issue Qty [Grey]</th>
                    <th width="100">Total Return Qty</th>
                    <th width="100">Reject Qty</th>    
                    <th>Balance</th>                       
                </tr>
			</thead>
		</table>

		<div style="max-height:400px; overflow-y:scroll; width:2018px; float: left;" id="scroll_body_1">
			<table width="2000" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" id="list_views" style="float: left">
				<tbody>
					<?	
						$data_array=array();

						//Receive Grey Qty...
						$sql="SELECT a.party_id, b.item_category_id, b.order_id, b.material_description, SUM(b.quantity) AS rcv_quantity FROM sub_material_mst a, sub_material_dtls b WHERE a.company_id=$company_id $recv_date_cond $item_category_cond $order_no_cond $pary_cond AND a.id=b.mst_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0 GROUP BY a.party_id, b.item_category_id, b.order_id, b.material_description";
						foreach (sql_select($sql) as $row) 
						{
							$data_array[$row[csf("party_id")]][$row[csf("item_category_id")]][$row[csf("order_id")]][$row[csf("material_description")]]["rcv_grey_qty"] +=$row[csf("rcv_quantity")];

							$data_array[$row[csf("party_id")]][$row[csf("item_category_id")]][$row[csf("order_id")]][$row[csf("material_description")]]["rcv_opening_qty"]=0;

							$order_id_arr[$row[csf("order_id")]]=$row[csf("order_id")];
						}

						$all_order_ids = implode(",", array_filter($order_id_arr));
						if ($all_order_ids =="")  $all_order_ids=0;
						//Receive Opening Balance...
						if($date_from && $date_to)
						{
							$sql="SELECT a.party_id, b.item_category_id, b.order_id, b.material_description, SUM(b.quantity) AS rcv_quantity FROM sub_material_mst a, sub_material_dtls b WHERE a.company_id=$company_id $item_category_cond $order_no_cond $pary_cond $recv_opening_blnc_cond AND a.id=b.mst_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0 and b.order_id in ($all_order_ids) GROUP BY a.party_id, b.item_category_id, b.order_id, b.material_description";
							foreach (sql_select($sql) as $row) 
							{
								$data_array[$row[csf("party_id")]][$row[csf("item_category_id")]][$row[csf("order_id")]][$row[csf("material_description")]]["rcv_opening_qty"] +=$row[csf("rcv_quantity")];
							}
						}

						//Order data...
						$sql_ord="SELECT a.id, a.order_no, a.order_uom, a.main_process_id, b.item_id, b.qnty FROM subcon_ord_dtls a, subcon_ord_breakdown b WHERE a.id in ($all_order_ids) AND a.id=b.order_id AND a.status_active=1 AND a.is_deleted=0";
						foreach (sql_select($sql_ord) as $row) 
						{
							$order_data_arr[$row[csf("id")]]["order_no"]=$row[csf("order_no")];
							$order_data_arr[$row[csf("id")]]["order_uom"]=$row[csf("order_uom")];

							if($row[csf("main_process_id")]==2 || $row[csf("main_process_id")]==3 || $row[csf("main_process_id")]==4 || $row[csf("main_process_id")]==6)
							{
								$item_desc = $item_desc_arr[$row[csf("item_id")]];	
							}
							else
							{
								$item_desc = $garments_item[$row[csf("item_id")]];
							}

							$order_qty_arr[$row[csf("id")]][$item_desc]["order_qty"] +=$row[csf("qnty")]; 
						}

						//delivery_data...
						$sql_knitting_dlvry_sql="SELECT b.order_id, b.item_id, 0 AS issue_qty_finish, b.delivery_qty AS issue_qty_grey, b.reject_qty, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=2 AND b.process_id=2 AND b.order_id in ($all_order_ids) AND a.status_active=1 AND a.is_deleted=0 $dlvry_date_cond
							UNION 
							SELECT b.order_id, b.item_id, b.delivery_qty AS issue_qty_finish, b.gray_qty AS issue_qty_grey, b.reject_qty, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id in ($all_order_ids) AND a.status_active=1 AND a.is_deleted=0 $dlvry_date_cond";
						foreach (sql_select($sql_knitting_dlvry_sql) as $row) 
						{
							$item_desc = $item_desc_arr[$row[csf("item_id")]];
							if ($row[csf("process_id")] !=2 ) 
							{
								$item_desc = $batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['item_description'];
							}
							$knitting_qty_arr[$row[csf("order_id")]][$item_desc]["reject_qty"] +=$row[csf("reject_qty")];
							$knitting_qty_arr[$row[csf("order_id")]][$item_desc]["issue_qty_grey"] +=$row[csf("issue_qty_grey")];
							$knitting_qty_arr[$row[csf("order_id")]][$item_desc]["issue_qty_finish"] +=$row[csf("issue_qty_finish")];
							$knitting_qty_arr[$row[csf("order_id")]][$item_desc]["issue_opening_qty_grey"] =0;
						}
						
						//Opening_balance_data...
						if($date_from && $date_to)
						{
							$sql_knitting_dlvry_sql="SELECT b.order_id, b.item_id, 0 AS issue_opening_qty_finish, b.delivery_qty AS issue_opening_qty_grey, b.reject_qty, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=2 AND b.process_id=2 AND b.order_id in ($all_order_ids) AND a.status_active=1 AND a.is_deleted=0 $opening_dlvry_date_cond
								UNION ALL
								SELECT b.order_id, b.item_id, b.delivery_qty AS issue_opening_qty_finish, b.gray_qty AS issue_opening_qty_grey, b.reject_qty, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id in ($all_order_ids) AND a.status_active=1 AND a.is_deleted=0 $opening_dlvry_date_cond";
							foreach (sql_select($sql_knitting_dlvry_sql) as $row) 
							{
								$item_desc = $item_desc_arr[$row[csf("item_id")]];
								if ($row[csf("process_id")] !=2 ) 
								{
									$item_desc = $batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['item_description'];
								}
								$knitting_qty_arr[$row[csf("order_id")]][$item_desc]["issue_opening_qty_grey"] +=$row[csf("issue_opening_qty_grey")];
								$knitting_qty_arr[$row[csf("order_id")]][$item_desc]["issue_opening_qty_finish"] +=$row[csf("issue_opening_qty_finish")];
							}
						}

						$sql_rtn="SELECT b.order_id, b.material_description, SUM(b.quantity) AS rtn_quantity FROM sub_material_mst a, sub_material_dtls b WHERE a.id=b.mst_id AND a.trans_type=3 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 and b.order_id in ($all_order_ids) GROUP BY b.order_id, b.material_description";
						foreach (sql_select($sql_rtn) as $row) 
						{
							$return_qty_arr[$row[csf("order_id")]][$row[csf("material_description")]]["issue_return_qty"] +=$row[csf("rtn_quantity")];
						}


						$total_rcv_opening_blnce=$total_rcv_grey_qty=$total_sum_grey_qty=array();
						$total_issue_opening_grey=$total_issue_grey_qty=$total_issue_sum_grey_qty=array();
						$total_reject_qty=$total_return_qty=$total_balance=array();
						$total_issue_opening_finish=$total_issue_finish_qty=$total_issue_sum_finish_qty=array();

						$j=1;
						foreach($data_array as $party_id => $party_arr)
						{
							foreach ($party_arr as $item_catgry => $item_catgry_arr)
							{
								foreach ($item_catgry_arr as $ord => $ord_arr) 
								{
									foreach ($ord_arr as $material_desc => $value) 
									{
										?>
											<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $j; ?>">
												<td width="50"  align="center" style="word-wrap: break-word; word-break: break-all;">
													<p><? echo $j; ?></p> 
												</td>
												<td width="150" align="center" style="word-wrap: break-word; word-break: break-all;" >
													<p><? echo $buyer_arr[$party_id]; ?></p> 
												</td>
												<td width="100"  align="center" style="word-wrap: break-word; word-break: break-all;" >
													<p><? echo $item_category[$item_catgry];  ?></p> 
												</td>
												<td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" >
													<p><? echo $order_data_arr[$ord]["order_no"]; ?></p> 
												</td>
												<td width="150" align="center" style="word-wrap: break-word; word-break: break-all;" >
													<p><? echo $material_desc; ?></p> 
												</td>
												<td width="100" align="center" style="word-wrap: break-word; word-break: break-all;" >
													<p> 
														<?
															echo $order_qty_arr[$ord][$material_desc]["order_qty"];
														?>
													</p> 
												</td>
												<td width="70" align="center" style="word-wrap: break-word; word-break: break-all;" >
													<p> <? echo $unit_of_measurement[$order_data_arr[$ord]["order_uom"]]; ?> </p> 
												</td>
												<td width="110" align="right" style="word-wrap: break-word; word-break: break-all;" >
													<p>
														<? 
															$total_rcv_opening_blnce[]=$value["rcv_opening_qty"];
															echo number_format($value["rcv_opening_qty"],2); 
														?>
													</p>
												</td>
												<td width="100"  align="right" style="word-wrap: break-word; word-break: break-all;" >
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*rcv_grey_qty";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
														<? 
															$total_rcv_grey_qty[]=$value["rcv_grey_qty"];
															echo number_format($value["rcv_grey_qty"],2); 
														?>
													</a>
												</td>
												<td width="100" align="right" style="word-wrap: break-word; word-break: break-all;" >
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*tot_rcv_grey_qty";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
														<?
															$tot_rec_grey_qty=$value["rcv_opening_qty"]+$value["rcv_grey_qty"];
															$total_sum_grey_qty[]=$tot_rec_grey_qty;
															echo number_format($tot_rec_grey_qty,2);
														?>
													</a>
												</td>
												<td width="150"  align="right" >
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*issue_opening_qty_finish";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');"> 
														<?
															$issue_opening_qty_finish=$knitting_qty_arr[$ord][$material_desc]["issue_opening_qty_finish"];
															$total_issue_opening_finish[]=$issue_opening_qty_finish;
															echo number_format($issue_opening_qty_finish,2);
														?>
													</a>
												</td>
												<td width="100"  align="right" >
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*issue_qty_finish";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
														<?
															$issue_qty_finish=$knitting_qty_arr[$ord][$material_desc]["issue_qty_finish"];
															$total_issue_finish_qty[]=$issue_qty_finish;
															echo number_format($issue_qty_finish,2);
														?>
													</a>
												</td>
												<td width="100" align="right" >
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*total_issue_qty_finish";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
														<?
															$tot_issue_qty_finish=$issue_opening_qty_finish+$issue_qty_finish;
															$total_issue_sum_finish_qty[]=$tot_issue_qty_finish;
															echo number_format($tot_issue_qty_finish,2);
														?>
													</a>
												</td>
												<td width="100"  align="right" style="word-wrap: break-word; word-break: break-all;" >
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*issue_opening_qty_grey";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
														<?
															$issue_opening_qnty_grey=$knitting_qty_arr[$ord][$material_desc]["issue_opening_qty_grey"];
															$total_issue_opening_grey[]=$issue_opening_qnty_grey;
															echo number_format($issue_opening_qnty_grey,2);
														?>
													</a>
												</td>
												<td width="100"  align="right" style="word-wrap: break-word; word-break: break-all;" >
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*issue_qty_grey";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
														<?
															$issue_qty_grey=$knitting_qty_arr[$ord][$material_desc]["issue_qty_grey"];
															$total_issue_grey_qty[]=$issue_qty_grey;
															echo number_format($issue_qty_grey,2);
														?>
													</a>
												</td>
												<td width="100" align="right" style="word-wrap: break-word; word-break: break-all;" > 
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*tot_issue_qty_grey";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');"> 
														<?
															$tot_issue_qty_grey=$issue_opening_qnty_grey+$issue_qty_grey;
															$total_issue_sum_grey_qty[]=$tot_issue_qty_grey;
															echo number_format($tot_issue_qty_grey,2);
														?>
													</a>
												</td>
												<td width="100" align="right">
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*issue_return_qty";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
													<?
														$issue_return_qty=$return_qty_arr[$ord][$material_desc]["issue_return_qty"];
														$total_return_qty[]=$issue_return_qty;
														echo number_format($issue_return_qty,2);
													?>
													</a>
												</td>
												<td width="100" align="right">
													<?
														$pop_up_data = $ord."*".$dates_pass_pop_up_data."*".$material_desc."*reject_qnty";
													?>
													<a href="##" onclick="show_popup_report_details('pop_up_details','<? echo $pop_up_data; ?>','380px');">
													<?
														$reject_qnty=$knitting_qty_arr[$ord][$material_desc]["reject_qty"];
														$total_reject_qty[]=$reject_qnty;
														echo number_format($reject_qnty,2);
													?>
													</a>
												</td>
												<td align="right">
													<p>
														<?
															$balance = $tot_rec_grey_qty-$tot_issue_qty_grey-$issue_return_qty-$reject_qnty;
															$total_balance[]=$balance;
															echo number_format($balance,2);
														?>
													</p>
												</td>
											</tr>
										<?
											$j++;
									}
								}
							}
						}
						?>
				</tbody>
			</table>
		</div>
			<table width="2000" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" style="float: left">
				<tfoot>
					<tr class="tbl_bottom">
		                <td width="50"> &nbsp;</td>
		                <td width="150">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="150">&nbsp;</td>
		                <td width="100">&nbsp;</td>
		                <td width="70" align="right"> Total &nbsp;</td>
		                <td width="110" align="right"><? echo number_format(array_sum($total_rcv_opening_blnce),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_rcv_grey_qty),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_sum_grey_qty),2); ?></td>
		                <td width="150" align="right"><? echo number_format(array_sum($total_issue_opening_finish),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_issue_finish_qty),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_issue_sum_finish_qty),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_issue_opening_grey),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_issue_grey_qty),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_issue_sum_grey_qty),2); ?></td> 
		                <td width="100" align="right"><? echo number_format(array_sum($total_return_qty),2); ?></td>
		                <td width="100" align="right"><? echo number_format(array_sum($total_reject_qty),2); ?></td>
		                <td align="right"><? echo number_format(array_sum($total_balance),2); ?></td>
					</tr>
				</tfoot>
			</table>
    </fieldset>
    </div>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}

if($action=="pop_up_details")
{
	echo load_html_head_contents("Details","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);

	$expData=explode('*',$datas);
	$ord_id = $expData[0];
	$popup_date_from = trim(str_replace("'", "", $expData[1]));
	$popup_date_to = trim(str_replace("'", "", $expData[2]));
	$material_desc = $expData[3];
	$type =$expData[4];

	$item_desc_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
	$batch_sql="select a.id, a.batch_no, a.extention_no, b.id as item_id, b.fabric_from, b.item_description from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$batch_sql_result=sql_select($batch_sql);
	foreach($batch_sql_result as $row)
	{
		$batch_array[$row[csf('id')]][$row[csf('item_id')]]['item_description']=$row[csf('item_description')];
	}

	$recv_date_cond=$dlvry_date_cond=$opening_dlvry_date_cond="";
	if($popup_date_from && $popup_date_to)
	{
		if($db_type==0)
		{
			$recv_date_cond= " and a.subcon_date between '".change_date_format($popup_date_from,'yyyy-mm-dd')."' and '".change_date_format($popup_date_to,'yyyy-mm-dd')."'";
			$recv_opening_blnc_cond= " and a.subcon_date < '".change_date_format($popup_date_from,'yyyy-mm-dd')."'";
			$dlvry_date_cond= " and a.delivery_date between '".change_date_format($popup_date_from,'yyyy-mm-dd')."' and '".change_date_format($popup_date_to,'yyyy-mm-dd')."'";
			$opening_dlvry_date_cond= " and a.delivery_date < '".change_date_format($popup_date_from,'yyyy-mm-dd')."'";
			$total_dlvry_date_cond= " and a.delivery_date <= '".change_date_format($popup_date_to,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$recv_date_cond= " and a.subcon_date between '".change_date_format($popup_date_from,'','',1)."' and '".change_date_format($popup_date_to,'','',1)."'";
			$recv_opening_blnc_cond= " and a.subcon_date < '".change_date_format($popup_date_from,'','',1)."'";
			$dlvry_date_cond= " and a.delivery_date between '".change_date_format($popup_date_from,'','',1)."' and '".change_date_format($popup_date_to,'','',1)."'";
			$opening_dlvry_date_cond= " and a.delivery_date < '".change_date_format($popup_date_from,'','',1)."'";
			$total_dlvry_date_cond= " and a.delivery_date <= '".change_date_format($popup_date_to,'','',1)."'";
		}	
	}
		
		
		

				if($type=="issue_opening_qty_finish" || $type=="issue_qty_finish" || $type=="total_issue_qty_finish" || $type=="issue_opening_qty_grey" || $type=="issue_qty_grey" || $type=="tot_issue_qty_grey" || $type=="reject_qnty")
				{
					
					if($type=="issue_opening_qty_finish")
					{
						$th_title="Delivery Quantity";
						if($popup_date_from && $popup_date_to)
						{ 
							$sql="SELECT b.item_id, b.delivery_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $opening_dlvry_date_cond";		
						}
					}
					else if($type=="issue_qty_finish")
					{
						$th_title="Delivery Quantity";
						$sql="SELECT b.item_id, b.delivery_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $dlvry_date_cond";
					}
					else if($type=="total_issue_qty_finish")
					{
						$th_title="Delivery Quantity";
						$sql="SELECT b.item_id, b.delivery_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $total_dlvry_date_cond";
					}
					else if($type=="issue_opening_qty_grey")
					{
						$th_title="Delivery Quantity";
						if($popup_date_from && $popup_date_to)
						{
							$sql="SELECT b.item_id, b.delivery_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=2 AND b.process_id=2 AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $opening_dlvry_date_cond
							UNION ALL
							SELECT b.item_id, b.gray_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $opening_dlvry_date_cond";
						}
					}
					else if($type=="issue_qty_grey")
					{
						$th_title="Delivery Quantity";
						$sql="SELECT b.item_id, b.delivery_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=2 AND b.process_id=2 AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $dlvry_date_cond
							UNION ALL
							SELECT b.item_id, b.gray_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $dlvry_date_cond";
					}
					else if($type=="tot_issue_qty_grey")
					{
						$th_title="Delivery Quantity";
						$sql="SELECT b.item_id, b.delivery_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=2 AND b.process_id=2 AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $total_dlvry_date_cond
							UNION ALL
							SELECT b.item_id, b.gray_qty AS qnty, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $total_dlvry_date_cond";
					}
					else if($type=="reject_qnty")
					{
						$th_title="Reject Quantity";
						$sql="SELECT b.item_id, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, b.reject_qty AS qnty, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=2 AND b.process_id=2 AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $dlvry_date_cond
							UNION 
							SELECT b.item_id, a.delivery_no AS transaction_no, a.delivery_date AS trans_date, b.reject_qty AS qnty, a.process_id, b.batch_id FROM subcon_delivery_mst a, subcon_delivery_dtls b WHERE a.id=b.mst_id AND a.process_id=4 AND b.process_id in (3,4) AND b.order_id=$ord_id AND a.status_active=1 AND a.is_deleted=0 $dlvry_date_cond";
					}
						?>
			    	<fieldset style="width:370px; margin: 0 auto;">
			        <div style="width:100%;" align="center">
			            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			                <thead>
			                    <tr>
			                        <th width="30">SL</th>
			                        <th width="100">DATE</th>
			                        <th width="120">Transaction Ref. No</th>
			                        <th width=""><? echo $th_title; ?></th>
			                    </tr>
			                </thead>
			            </table>
			        </div>  
			        <div style="width:100%; max-height:310px; overflow-y:scroll" align="left">
			            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $tot_qty=0;
	                $i=0;

					$sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                	$item_desc = $item_desc_arr[$row[csf("item_id")]];
						if ($row[csf("process_id")] !=2 ) 
						{
							$item_desc = $batch_array[$row[csf('batch_id')]][$row[csf('item_id')]]['item_description'];
						}
						if($item_desc==$material_desc)
						{
		                    $i++;
		                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
			                ?>
			                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			                    <td width="30"><? echo $i; ?></td>
			                    <td width="100" align="center"><? echo change_date_format($row[csf("trans_date")]);?> </td> 
			                    <td width="120" align="center"><p><? echo $row[csf("transaction_no")]; ?></p></td>
			                    <td align="right">
			                    	<? 
			                    		$tot_qty += $row[csf("qnty")];
			              				echo number_format($row[csf("qnty")],2);
			                    	?>
			                    </td>
			                </tr>
			                <? 
			            } 
		            } 

				}
				else
				{
					if($type=="rcv_grey_qty")
					{
						$th_title="Received Quantity";
						$sql="SELECT a.sys_no AS transaction_no, a.subcon_date AS trans_date, b.id, b.quantity AS qnty FROM sub_material_mst a, sub_material_dtls b WHERE b.order_id=$ord_id AND b.material_description='$material_desc' $recv_date_cond  AND a.id=b.mst_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0 GROUP BY a.sys_no, a.subcon_date, b.id, b.quantity ORDER BY a.subcon_date DESC";
					}
					else if($type=="tot_rcv_grey_qty")
					{
						$th_title="Received Quantity";
						$sql="SELECT a.sys_no AS transaction_no, a.subcon_date AS trans_date, b.id, b.quantity AS qnty FROM sub_material_mst a, sub_material_dtls b WHERE b.order_id=$ord_id AND b.material_description='$material_desc' $recv_date_cond  AND a.id=b.mst_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0
							UNION
							SELECT a.sys_no AS transaction_no, a.subcon_date AS trans_date, b.id, b.quantity AS qnty FROM sub_material_mst a, sub_material_dtls b WHERE b.order_id=$ord_id AND b.material_description='$material_desc' $recv_opening_blnc_cond  AND a.id=b.mst_id AND a.trans_type=1 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=2 AND b.is_deleted=0 GROUP BY a.sys_no, a.subcon_date, b.id, b.quantity ORDER BY trans_date DESC";
					}
					else if($type=="issue_return_qty")
					{
						$th_title="Return Quantity";
						$sql="SELECT a.sys_no AS transaction_no, a.subcon_date AS trans_date, b.id, b.quantity AS qnty FROM sub_material_mst a, sub_material_dtls b WHERE a.id=b.mst_id AND a.trans_type=3 AND a.status_active=1 AND a.is_deleted= 0 AND b.status_active=1 AND b.is_deleted=0 and b.order_id=$ord_id AND b.material_description='$material_desc' GROUP BY a.sys_no, a.subcon_date, b.id, b.quantity ORDER BY a.subcon_date";
					}

						?>
			    	<fieldset style="width:370px; margin: 0 auto;">
			        <div style="width:100%;" align="center">
			            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
			                <thead>
			                    <tr>
			                        <th width="30">SL</th>
			                        <th width="100">DATE</th>
			                        <th width="120">Transaction Ref. No</th>
			                        <th width=""><? echo $th_title; ?></th>
			                    </tr>
			                </thead>
			            </table>
			        </div>  
			        <div style="width:100%; max-height:310px; overflow-y:scroll" align="left">
			        <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
	                <?
	                $tot_qty=0;
	                $i=0;

					$sql_arr= sql_select($sql);
	                foreach( $sql_arr as $row )
	                {
	                    $i++;
	                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
		               ?>
		                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                    <td width="30"><? echo $i; ?></td>
		                    <td width="100" align="center"><? echo change_date_format($row[csf("trans_date")]);?> </td> 
		                    <td width="120" align="center"><p><? echo $row[csf("transaction_no")]; ?></p></td>
		                    <td align="right">
		                    	<? 
		                    		$tot_qty += $row[csf("qnty")];
		              				echo number_format($row[csf("qnty")],2);
		                    	?>
		                    </td>
		                </tr>
		                <?  
		            } 
				}
					?>
                <tr class="tbl_bottom">
                    <td colspan="3" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
		</fieldset>
 		</div> 
		<?
	exit();
}

?>