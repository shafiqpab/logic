<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}
if($action=="work_no_popup")
{
	echo load_html_head_contents("WO No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
			var selected_id = new Array; var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_div' ).rows.length;
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

		function js_set_value2( str ) {

			if (str!="") str=str.split("_");
			//alert(str);
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


			$('#hide_wo_id').val( id );
			$('#hide_wo_no').val( name );


		}
    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:970px;">
            <table width="960" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Supplier</th>
					<th >Year</th>
                    <th>WO No</th>
                    <th>Booking No</th>
                    <th>Style Des</th>
					

                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                    <input type="hidden" name="hide_wo_no" id="hide_wo_no" value="" />
                    <input type="hidden" name="hide_wo_id" id="hide_wo_id" value="" />

                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$cbo_buyer_name,"",0 );
							?>
                        </td>

                       <td align="center">
                        	 <?
								echo create_drop_down("cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
							?>
                        </td>
						<td> 
                            <?
								$selected_year=date("Y");
                                echo create_drop_down( "cbo_year", 110, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                            ?>
                        </td>
						<td align="center">
                             <input type="text" name="txt_wo_no" id="txt_wo_no" class="text_boxes_numeric" style="width:80px" placeholder="Write" >
                        </td>
						<td align="center">
                             <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes_numeric" style="width:80px" placeholder="Write" >
                        </td>
						<td align="center">
                             <input type="text" name="txt_stl_des" id="txt_stl_des" class="text_boxes" style="width:100px" placeholder="Write" >
                        </td>
						
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_supplier').value+'**'+document.getElementById('txt_wo_no').value+'**'+document.getElementById('cbo_year').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_stl_des').value, 'create_wo_no_search_list_view', 'search_div', 'non_order_dyed_yarn_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if($action=="create_wo_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$buyer_name=$data[1];
	$cbo_supplier=$data[2];
	
	
	
	$year_id=str_replace("'","",$cbo_year);;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and buyer_po in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
		$buyer_id_cond2=" and buyer_po=$data[1]";
	}

	if($data[3] !='')
	{
		$wo_no_cond = " and yarn_dyeing_prefix_num=$data[3]";
		$wo_no_cond1 = " and wo_number_prefix_num=$data[3]";
		$wo_no_cond2 = " and a.yarn_dyeing_prefix_num=$data[3]";
	}
	else
	{
		$wo_no_cond = "";
		$wo_no_cond1 = "";
		$wo_no_cond2 = "";
	}

	if($db_type==0)
	{
		if($data[3]!=0) $year_cond=" and year(insert_date)=$data[4]"; else $year_cond="";
		if($data[3]!=0) $year_cond1=" and year(a.insert_date)=$data[4]"; else $year_cond1="";
	}
	if($db_type==2)
	{
		if($data[3]!=0) $year_cond=" and to_char(insert_date,'yyyy')=$data[4]"; else $year_cond="";
		if($data[3]!=0) $year_cond1=" and to_char(a.insert_date,'yyyy')=$data[4]"; else $year_cond1="";
	}
	
	// $search_by=$data[2];
	// $search_string="%".trim($data[3])."%";

	if($db_type==0) 
	{ 
		$year_field=" YEAR(insert_date) as year";
		$year_field1=" YEAR(a.insert_date) as year";
	}
	else if($db_type==2) 
	{ 
		$year_field="  to_char(insert_date,'YYYY') as year";
		$year_field1="  to_char(a.insert_date,'YYYY') as year";
	}
	else 
	{ 
		$year_field="";
		$year_field1="";
	}

	if($cbo_supplier!=0) $supplier_cond=" and supplier_id=$cbo_supplier"; else $supplier_cond="";
	if($cbo_supplier!=0) $supplier_cond1=" and a.supplier_id=$cbo_supplier"; else $supplier_cond1="";
	
	if($data[5] !='' || $data[6] !='')
	{
		if($data[5] !='') $booking_no_cond=" and c.booking_no_prefix_num =$data[5]"; else $booking_no_cond="";
		if($data[6] !='') $stl_des_cond=" and d.style_des LIKE '%$data[6]%'"; else $stl_des_cond="";

		$sql ="SELECT a.id, $year_field1,a.company_id as company_id,a.supplier_id,a.currency as currency_id,
		a.yarn_dyeing_prefix_num as wopi_prefix,a.booking_date as wo_date, a.delivery_date, b.BOOKING_NO, d.style_des 
		from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c,wo_non_ord_samp_booking_dtls d
		where a.id=b.mst_id and b.booking_no = c.booking_no and c.booking_no = d.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id='$company_id' $supplier_cond1 $wo_no_cond2 $year_cond1 $booking_no_cond $stl_des_cond
		group by a.id,a.insert_date,a.company_id,a.supplier_id,a.currency,a.yarn_dyeing_prefix_num,a.booking_date,a.delivery_date, b.BOOKING_NO, d.style_des";

	}
	else
	{
		$sql ="(SELECT id,$year_field,company_id as company_id,supplier_id,currency as currency_id, yarn_dyeing_prefix_num as wopi_prefix, booking_date as wo_date, delivery_date
		from wo_yarn_dyeing_mst
		where status_active=1 and is_deleted=0 and company_id='$company_id' $supplier_cond $wo_no_cond $year_cond group by id,insert_date,company_id,supplier_id,currency,yarn_dyeing_prefix_num,booking_date,delivery_date)
		union
		(
			SELECT id,$year_field,company_name as company_id,null  as supplier_id,currency_id as currency_id,wo_number_prefix_num as wopi_prefix,wo_date as wo_date,delivery_date
		from wo_non_order_info_mst
		where status_active=1 and is_deleted=0 and entry_form=144 and company_name='$company_id' and pay_mode!=2 $supplier_cond $wo_no_cond1 $year_cond group by id,insert_date,company_name,currency_id,wo_number_prefix_num,wo_date,delivery_date ) order by id";
	}

	//echo $sql;
	$sqlResult=sql_select($sql);
	?>
    <div>
     		 <fieldset>
            <form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
                    <thead>
                    <th width="30">SL</th>
                    <th width="50">Year</th>
                    <th width="130">Company</th>
                    <th width="60">WO No</th>
                    <th width="80">WO Date.</th>
                    <th width="80">Delivery Date </th>
                    <th width="130">Supplier </th>
                    <th width="">Currecny</th>
                    </thead>
                </table>
                    <div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search">
                    <?
					$i=1;
                    foreach($sqlResult as $row )
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

						$data=$i.'_'.$row[csf('id')].'_'.$row[csf('wopi_prefix')];
						//echo $data;
					?>
                    	<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
                          <td width="30" align="center"><?php echo $i; ?>
                          <td width="50"><p><? echo $row[csf('year')]; ?></p></td>
                          <td width="130"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                          <td width="60"><p><? echo $row[csf('wopi_prefix')]; ?></p></td>
                          <td width="80"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
                          <td width="80"><p><? echo change_date_format($row[csf('delivery_date')]); ?></p></td>
                          <td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                          <td width=""><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
                       </tr>
                       <?
					   $i++;
					}
					   ?>
                    </table>
                     </div>
                     <table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
                                    Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px"/>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                    </form>
                     </fieldset>
                    </div>

    <?

   exit();
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier where status_active=1 and is_deleted=0",'id','short_name');

	$txt_process_loss=str_replace("'","",$txt_process_loss);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)>0)
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	//print_r($order_arr);
	$color_array=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$hide_wo_id=str_replace("'","",$hide_wo_id);
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);

	if($db_type == 0)
	{
		$booking_year = " and year(c.booking_date) = '$cbo_year_selection' ";
	}else{

		$booking_year = " and to_char(c.booking_date,'YYYY') = '$cbo_year_selection' ";
	}
	$cbo_year_selection = substr($cbo_year_selection, -2);

	$wo_no_cond="";
	if($txt_wo_no!='')
	{
		$wo_no_cond="and c.yarn_dyeing_prefix_num in($txt_wo_no) $booking_year";
		if($hide_wo_id!='') $wo_id_cond="and c.id in($hide_wo_id)";else  $wo_id_cond="";
	}

	$issReturnWoCond = "";

	if($txt_wo_no!='')
	{
		//$issReturnWoCond .= " and b.booking_no like '%".trim($txt_wo_no)."' and b.booking_no like '%-$cbo_year_selection-%'";
		if($hide_wo_id!='') $issReturnWoCond .=  " and b.booking_id  in (".$hide_wo_id.")";
	}


	if($db_type==0)
	{
		$date_from=change_date_format($from_date,'yyyy-mm-dd');
		$date_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$date_from=change_date_format($from_date,'','',1);
		$date_to=change_date_format($to_date,'','',1);
	}
	else
	{
		$date_from="";
		$date_to="";
	}

	if($date_from!="" && $date_to!="") $date_cond=" and c.booking_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";

	if($type==1)
	{
		$issue_arr=array(); $color_wise_qnty_array=array();

		$issueDataArr=sql_select("select b.booking_no, a.dyeing_color_id, sum(a.cons_quantity) as issue_qnty, b.booking_id from inv_transaction a, inv_issue_master b where a.mst_id=b.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.job_no is null and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 group by  b.booking_no, b.booking_id, a.dyeing_color_id");

		foreach($issueDataArr as $row)
		{
			$issue_arr[$row[csf('booking_id')]]+=$row[csf('issue_qnty')];
			$color_wise_qnty_array[$row[csf('booking_id')]][$row[csf('dyeing_color_id')]]=$row[csf('issue_qnty')];
		}

		$issueReturnArr = sql_select("select (a.cons_quantity) as iss_ret_qnty, b.booking_id, c.color, b.booking_no
		from inv_transaction a, inv_receive_master b, product_details_master c, inv_issue_master d
		where a.mst_id=b.id and a.prod_id=c.id and a.issue_id=d.id and b.entry_form=9 and b.receive_basis=1 and a.company_id=$cbo_company_name $issReturnWoCond
		and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=4 and d.issue_basis=1 and d.issue_purpose=2 and d.entry_form=3");

		/*echo "select (a.cons_quantity) as iss_ret_qnty, b.booking_id, c.color, b.booking_no
		from inv_transaction a, inv_receive_master b, product_details_master c, inv_issue_master d
		where a.mst_id=b.id and a.prod_id=c.id and a.issue_id=d.id and b.entry_form=9 and b.receive_basis=1 and a.company_id=$cbo_company_name $issReturnWoCond
		and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=4 and d.issue_basis=1 and d.issue_purpose=2 and d.entry_form=3";*/
		foreach($issueReturnArr as $val)
		{
			$issue_ret_arr[$val[csf('booking_id')]]+=$val[csf('iss_ret_qnty')];
			//$color_wise_iss_ret_qnty_array[$val[csf('booking_id')]][$val[csf('color')]]=$val[csf('iss_ret_qnty')];
		}

		//var_dump($issue_arr);
		if($db_type==0)
	   	{
			$BookingColorArray=return_library_array("select mst_id, group_concat(distinct(yarn_color)) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','color');
	   	}
  		else if($db_type==2)
	   	{
			$BookingColorArray=return_library_array("select mst_id, listagg(yarn_color,',') within group (order by yarn_color) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','color');
	   	}

		$recv_arr=array();
		$recvDataArr=sql_select("select b.booking_no, sum(a.cons_quantity) as recv_qnty, b.booking_id, c.color from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.job_no is null and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1 group by b.booking_no, b.booking_id, c.color");

		foreach($recvDataArr as $row)
		{
			$recv_arr[$row[csf('booking_id')]][$row[csf('color')]]=$row[csf('recv_qnty')];
		}

		$recv_ret_dataArr=sql_select("select b.booking_id, sum(c.cons_quantity) as quantity, d.color from inv_issue_master a, inv_receive_master b, inv_transaction c, product_details_master d where a.received_id = b.id and a.id = c.mst_id and c.prod_id = d.id and a.entry_form = 8 and a.company_id = $cbo_company_name and c.item_category = 1 and b.entry_form = 1 and b.receive_basis = 2 and b.receive_purpose = 2  and c.is_deleted = 0 and c.status_active = 1 and a.status_active = 1 and a.is_deleted = 0 group by b.booking_id, d.color");

		foreach ($recv_ret_dataArr as $val)
		{
			$recv_ret_arr[$val[csf('booking_id')]][$val[csf('color')]] +=$val[csf('quantity')];
		}

		?>
		<fieldset style="width:1360px;">
			<table cellpadding="0" cellspacing="0" width="1360">
				<tr class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><? echo $report_title; ?> &nbsp;(Color Wise)</strong></td>
				</tr>
				<tr class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="13" style="font-size:14px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
			</table>
			<table width="1350" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="80" rowspan="2">Buyer Name</th>
						<th width="110" rowspan="2">Style No</th>
						<th width="120" rowspan="2">WO No</th>
						<th colspan="4">Grey Yarn</th>
                        <th width="100" rowspan="2">Color</th>
						<th colspan="4">Dyed Yarn</th>
						<th rowspan="2">Party Name</th>
					</tr>
					<tr>
						<th width="100">Total Required</th>
						<th width="100">Delivery</th>
						<th width="100">Balance</th>
                        <th width="100">Grey (Color-wise) Issue Qty.</th>
						<th width="100">Less <? echo $txt_process_loss; ?>% process loss</th>
						<th width="100">Received Qty.</th>
						<th width="100">Balance</th>
						<th width="100">Actual Process Loss(%)</th>
					</tr>
				</thead>
			</table>
			<div style="width:1368px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1350" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
						$i=1; $z=1; $total_req_qnty=0; $tot_delivery_qnty=0; $total_grey_bl_qnty=0; $total_process_loss_qnty=0; $total_recv_qnty=0; $total_dyed_bl_qnty=0;
						if($db_type==0)
						{
							//$sql="select a.buyer_name, group_concat(a.style_ref_no) as style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from sample_development_mst a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $buyer_id_cond $date_cond $wo_id_cond $wo_no_cond group by c.id order by c.ydw_no";

							$sql="select a.buyer_name, group_concat(a.style_ref_no) as style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_dtls b left join sample_development_mst a on b.job_no_id=a.id, wo_yarn_dyeing_mst c where b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $buyer_id_cond $date_cond $wo_id_cond $wo_no_cond group by c.id, c.ydw_no, c.supplier_id,a.buyer_name order by c.ydw_no";

						}
						else if($db_type==2)
						{

							//$sql="select a.buyer_name, listagg(cast(a.style_ref_no as varchar(4000)),',') within group (order by a.style_ref_no) style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from sample_development_mst a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $buyer_id_cond $date_cond $wo_id_cond $wo_no_cond group by c.id, a.buyer_name, c.ydw_no, c.supplier_id order by c.ydw_no";

							$sql="select a.buyer_name, listagg(cast(a.style_ref_no as varchar(4000)),',') within group (order by a.style_ref_no) style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_dtls b left join sample_development_mst a on b.job_no_id=a.id, wo_yarn_dyeing_mst c where b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $buyer_id_cond $date_cond $wo_id_cond $wo_no_cond group by c.id, c.ydw_no, c.supplier_id,a.buyer_name order by c.ydw_no";

						}
						//echo $sql;
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$style_ref_no = implode(", ",array_filter(array_unique(explode(",", $row[csf('style_ref_no')]))));

							$delivery_ret_qnty = $issue_ret_arr[$row[csf('id')]];
							$delivery_qnty=$issue_arr[$row[csf('id')]] - $delivery_ret_qnty;


							$grey_balance=$row[csf('qnty')]-$delivery_qnty ;//+ $delivery_ret_qnty;

							$colorArray=array_unique(explode(",",$BookingColorArray[$row[csf('id')]]));
							if($color_wise_qnty_array[$row[csf('id')]][0]>0)
							{
								array_unshift($colorArray, "0");
							}
							//print_r($colorArray);
							$s=0; $row_span=count($colorArray);
							foreach($colorArray as $color_id)
							{

								$grey_color_qnty=$color_wise_qnty_array[$row[csf('id')]][$color_id];
								$process_loss_qnty=$grey_color_qnty-($grey_color_qnty*$txt_process_loss/100);
								$recv_qnty=$recv_arr[$row[csf('id')]][$color_id] - $recv_ret_arr[$row[csf('id')]][$color_id];
								$dyed_balance=$process_loss_qnty-$recv_qnty;
								$actual_process_loss=($grey_color_qnty-$recv_qnty)/$grey_color_qnty*100;
							?>
								<tr valign="middle" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $z;?>">
                                	<?
									if($s==0)
									{
									?>
                                        <td width="40" rowspan="<? echo $row_span; ?>" title="<? echo $row[csf('id')].'_'.$color_id;?>"><? echo $i; ?></td>
                                        <td width="80" rowspan="<? echo $row_span; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                        <td width="110" rowspan="<? echo $row_span; ?>"><p><? echo $style_ref_no;//$row[csf('style_ref_no')]; ?>&nbsp;</p></td>
                                        <td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('ydw_no')]; ?></p></td>
                                        <td width="100" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
                                        <td width="100" align="right" rowspan="<? echo $row_span; ?>"><a href="##" onclick="openmypage_issue(<? echo $row[csf('id')];?>)"><? echo number_format($delivery_qnty,2,'.',''); ?>&nbsp;</a></td>
                                        <td width="100" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($grey_balance,2,'.',''); ?>&nbsp;</td>
                                    <?
										$total_req_qnty+=$row[csf('qnty')];
										$tot_delivery_qnty+=$delivery_qnty;
										$total_grey_bl_qnty+=$grey_balance;
									}
									?>
									<td width="100" align="right"><? echo number_format($grey_color_qnty,2,'.',''); ?>&nbsp;</td>
									<td width="100" align="center"><p><? if($color_id==0) echo "Select"; else echo $color_array[$color_id]; ?></p></td>
									<td width="100" align="right"><? echo number_format($process_loss_qnty,2,'.','');  ?>&nbsp;</td>
									<td width="100" align="right"><a href="##" onclick="openmypage_rcv(<? echo $row[csf('id')];?>,<? echo $color_id;?>)"><? echo number_format($recv_qnty,2,'.','');  ?>&nbsp;</a></td>
									<td width="100" align="right"><? echo number_format($dyed_balance,2,'.','');  ?>&nbsp;</td>
									<td width="100" align="right"><? echo number_format($actual_process_loss,2,'.',''); ?>&nbsp;</td>
                                    <?
									if($s==0)
									{
									?>
										<td rowspan="<? echo $row_span; ?>"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
                                    <?
									}
									?>
								</tr>
							<?
								$total_color_wise_qnty+=$grey_color_qnty;
								$total_process_loss_qnty+=$process_loss_qnty;
								$total_recv_qnty+=$recv_qnty;
								$total_dyed_bl_qnty+=$dyed_balance;

								$s++;
								$z++;
							}
							$i++;
						}
					?>
					<tfoot>
						<th colspan="4" align="right">Total</th>
						<th align="right"><? echo number_format($total_req_qnty,2); ?>&nbsp;</th>
						<th align="right"><? echo number_format($tot_delivery_qnty,2); ?>&nbsp;</th>
						<th align="right"><? echo number_format($total_grey_bl_qnty,2); ?>&nbsp;</th>
                        <th align="right"><? echo number_format($total_color_wise_qnty,2); ?>&nbsp;</th>
                        <th>&nbsp;</th>
						<th align="right"><? echo number_format($total_process_loss_qnty,2); ?>&nbsp;</th>
						<th align="right"><? echo number_format($total_recv_qnty,2); ?>&nbsp;</th>
						<th align="right"><? echo number_format($total_dyed_bl_qnty,2); ?>&nbsp;</th>
						<th colspan="2">&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	else if($type==2)
	{
		$color_prod_arr=return_library_array( "select id, color from product_details_master",'id','color');
		$lot_prod_arr=return_library_array( "select id, lot from product_details_master",'id','lot');
		$brand_name_arr=return_library_array( "select id, brand_name from  lib_brand",'id','brand_name');
		$company_short_arr=return_library_array( "select id, company_short_name from  lib_company",'id','company_short_name');
		$issue_arr=array();  $color_wise_qnty=array(); $lot_wise_qnty_array=array(); $wo_wise_qnty=array();

		/*echo "select  a.prod_id, c.ydw_no, c.id, sum(a.cons_quantity) as issue_qnty, b.knit_dye_source, b.knit_dye_company
			from inv_transaction a, inv_issue_master b, wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d, wo_non_ord_samp_booking_mst e, wo_non_ord_samp_booking_dtls f
			where a.mst_id=b.id and a.job_no is null and a.company_id=$cbo_company_name and b.issue_purpose=8

			and b.booking_no=e.booking_no and f.style_id=d.job_no_id and c.id=d.mst_id and e.booking_no=f.booking_no

			and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0
			and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0

			and a.item_category=1 and a.transaction_type=2
			group by a.prod_id, c.ydw_no, c.id, b.knit_dye_source, b.knit_dye_company";*/


		//$issueDatalotArr=sql_select("select a.dyeing_color_id, a.prod_id, c.booking_no, c.booking_id, sum(a.cons_quantity) as issue_qnty, c.knit_dye_source, c.knit_dye_company from inv_transaction a, inv_issue_master c where a.mst_id=c.id and a.job_no is null and a.company_id=$cbo_company_name and c.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=1 and a.transaction_type=2 group by a.dyeing_color_id, a.prod_id, c.booking_no, c.booking_id, c.knit_dye_source, c.knit_dye_company");
		$wo_yarn_dyeing_arr=array();
		$wo_yarn_dyeing_sql=sql_select("select a.id, b.job_no_id from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		foreach($wo_yarn_dyeing_sql as $row)
		{
			$wo_yarn_dyeing_arr[$row[csf('booking_no')]]=$row[csf('id')];
		}
		unset($wo_yarn_dyeing_sql);

$issueDatalotArr=sql_select("select distinct a.prod_id, (a.cons_quantity) as issue_qnty, b.knit_dye_source, b.knit_dye_company, d.style_id from inv_transaction a, inv_issue_master b, wo_non_ord_samp_booking_mst c, wo_non_ord_samp_booking_dtls d where a.mst_id=b.id and a.job_no is null and a.company_id=$cbo_company_name and b.issue_purpose=8 and b.booking_no=c.booking_no and c.booking_no=d.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b is_deleted=0  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.item_category=1 and a.transaction_type=2");// and c.ydw_no='FAL-YDW-16-00002'
		foreach($issueDatalotArr as $row)
		{
			//echo $lot_prod_arr[$row[csf('prod_id')]].'<br>';
			$wo_id=$wo_yarn_dyeing_arr[$row[csf('booking_no')]];
			$wo_wise_qnty[$wo_id]+=$row[csf('issue_qnty')];
			$color_wise_qnty[$wo_id][$color_prod_arr[$row[csf('prod_id')]]]+=$row[csf('issue_qnty')];
			$lot_wise_qnty_array[$wo_id][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['lot']+=$row[csf('issue_qnty')];
			$lot_wise_qnty_array[$wo_id][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['source']=$row[csf('knit_dye_source')];
			$lot_wise_qnty_array[$wo_id][$color_prod_arr[$row[csf('prod_id')]]][$lot_prod_arr[$row[csf('prod_id')]]]['party']=$row[csf('knit_dye_company')];
		}
		unset($issueDatalotArr);
		//$sql="select a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from sample_development_mst a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and c.entry_form=42 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $buyer_id_cond $date_cond group by c.id, a.buyer_name, a.style_ref_no, c.ydw_no, c.supplier_id order by c.ydw_no";
		//print_r($lot_wise_qnty_array);
		/*$req_booking_qty_arr=array();
		$sql_req_booking="select mst_id, sum(grey_fab_qnty) as req_qty from wo_booking_dtls where color_type in (2,3,4,6) and status_active=1 and is_deleted=0 group by mst_id";
		$sql_req_booking_result=sql_select($sql_req_booking);
		foreach($sql_req_booking_result as $row)
		{
			$req_booking_qty_arr[$row[csf('mst_id')]]=$row[csf('req_qty')];
		}*/
		//var_dump($req_booking_qty_arr);
		if($db_type==0)
	   	{
			$BookingColorArray=return_library_array("select mst_id, group_concat(distinct(yarn_color)) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','color');
	   	}
  		else if($db_type==2)
	   	{
			$BookingColorArray=return_library_array("select mst_id, listagg(yarn_color,',') within group (order by yarn_color) as color from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','color');
			//$BookingColorArray=array_unique($BookingColorArray);
	   	}

		$recv_arr=array(); $color_recv_arr=array();
		$recvDataArr=sql_select("select sum(a.cons_quantity) as recv_qnty, b.booking_id, c.color, c.lot from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and a.job_no is null and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1 group by b.booking_id, c.color, c.lot");

		foreach($recvDataArr as $row)
		{
			$recv_arr[$row[csf('booking_id')]][$row[csf('color')]][$row[csf('lot')]]=$row[csf('recv_qnty')];
			$color_recv_arr[$row[csf('booking_id')]][$row[csf('color')]]+=$row[csf('recv_qnty')];
		}
		//echo "<pre>";
		//print_r($recv_arr);
		if($db_type==0)
	   	{
			$lot_cond="group_concat(c.lot)";
		}
		else if($db_type==2)
		{
			$lot_cond="listagg(cast(c.lot as varchar2(4000)),',') within group (order by c.lot)";
		}

		$lot_arr=array(); $brand_arr=array();
		$lotDataArr=sql_select("select b.booking_id, c.color, a.brand_id, $lot_cond as lot from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and a.job_no is null and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=1 group by b.booking_id, c.color, a.brand_id");

		foreach($lotDataArr as $row)
		{
			$lot_arr[$row[csf('booking_id')]][$row[csf('color')]]=$row[csf('lot')];
			$brand_arr[$row[csf('booking_id')]][$row[csf('color')]]['brand_id']=$row[csf('brand_id')];
		}

		//var_dump($lot_arr);

		?>
		<fieldset style="width:1700px;">
			<table cellpadding="0" cellspacing="0" width="1700">
				<tr class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo $report_title; ?>&nbsp;(Lot Wise)</strong></td>
				</tr>
				<tr class="form_caption" style="border:none;">
				   <td align="center" width="100%" colspan="17" style="font-size:14px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
				</tr>
			</table>
			<table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="40" rowspan="2">SL</th>
						<th width="80" rowspan="2">Buyer Name</th>
						<th width="110" rowspan="2">Style No</th>
                        <th width="120" rowspan="2">WO No</th>
						<th colspan="4">Grey Yarn</th>
                        <th width="100" rowspan="2">Color</th>
						<th colspan="5">Dyed Yarn</th>
                        <th colspan="4">YD Delivery for Knitting</th>
					</tr>
					<tr>
						<th width="100">Total Required</th>
						<th width="100">Delivery</th>
						<th width="100">Balance</th>
                        <th width="100">Grey (Color-wise) Issue Qty.</th>
						<th width="100">WO Qty.</th><!--Order Qty. (Less <? //echo $txt_process_loss; ?>% process loss)-->
                        <th width="100">Brand</th>
                        <th width="100">Lot/Batch</th>
						<th width="100">Received Qty.</th>
						<th width="100">Color Total Rec. Balance</th>
						<th width="100">Issue To Knitting</th>
                        <th width="100">Issue Balance</th>
                        <th>Knitting Party</th>
					</tr>
				</thead>
			</table>
			<div style="width:1718px; overflow-y: scroll; max-height:380px;" id="scroll_body">
				<table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
						$wo_qty_arr=array();
						$sqlCol=sql_select("select c.id, b.yarn_color, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $buyer_id_cond $wo_id_cond $wo_no_cond group by c.id, b.yarn_color");

						foreach($sqlCol as $row)
						{
							$wo_qty_arr[$row[csf('id')]][$row[csf('yarn_color')]]=$row[csf('qnty')];
						}

						//var_dump($wo_qty_arr);

						$i=1; $z=1;  $total_req_qnty=0; $tot_delivery_qnty=0; $total_grey_bl_qnty=0; $total_process_loss_qnty=0; $total_recv_qnty=0; $total_dyed_bl_qnty=0;

						if($db_type==0)
						{
							$sql="select a.buyer_name, group_concat(a.style_ref_no) as style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from sample_development_mst a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $buyer_id_cond $date_cond $wo_id_cond $wo_no_cond group by c.id order by c.ydw_no";
						}
						else if($db_type==2)
						{
							//$sql="select a.buyer_name, a.style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from sample_development_mst a, wo_yarn_dyeing_dtls b, wo_yarn_dyeing_mst c where a.id=b.job_no_id and b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_id=$cbo_company_name $buyer_id_cond $date_cond $wo_id_cond $wo_no_cond group by c.id, a.buyer_name, a.style_ref_no, c.ydw_no, c.supplier_id order by c.ydw_no";
							$sql="select a.buyer_name, listagg(cast(a.style_ref_no as varchar(4000)),',') within group (order by a.style_ref_no) style_ref_no, c.id, c.ydw_no, c.supplier_id, sum(b.yarn_wo_qty) as qnty from wo_yarn_dyeing_dtls b left join sample_development_mst a on b.job_no_id=a.id,  wo_yarn_dyeing_mst c where  b.mst_id=c.id and c.entry_form in(42,114) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$cbo_company_name $buyer_id_cond $date_cond $wo_id_cond $wo_no_cond group by c.id, a.buyer_name, c.ydw_no, c.supplier_id order by c.ydw_no";
						}
						//echo $sql;
						$result=sql_select($sql);  $rowspan_array=array();
						foreach($result as $row)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

							$style_ref_no = implode(", ",array_filter(array_unique(explode(",", $row[csf('style_ref_no')]))));

							$col_rec_qty=array();
							$colorArray=array_unique(explode(",",$BookingColorArray[$row[csf('id')]]));
							if($color_wise_qnty_array[$row[csf('id')]][0][0]>0)
							{
								array_unshift($colorArray, "0");
							}
							//print_r($colorArray);
							$rowspan_color_array=array();
							$s=0; $k=0; $row_span=count($colorArray); $count_lot=0;
							$x=0;
							foreach($colorArray as $color_id)
							{
								$lotspan=array_unique(explode(",",$lot_arr[$row[csf('id')]][$color_id]));
								$count_lot=count($lotspan);
								foreach($lotspan as $lot_span)
								{
									$rowspan_array[$lot_span]+=1;
									$rowspan_color_array[$color_id][$x]+=1;
									$k++;
								}
								$x++;
							}
							//print_r ($rowspan_color_array);
							$x=0;
							foreach($colorArray as $color_id)
							{
								//echo $color_id.'<br>';
								//$delivery_qnty=0;
								$delivery_qnty=$wo_wise_qnty[$row[csf('id')]];//$issue_arr[$row[csf('id')]];
								$grey_balance=$row[csf('qnty')]-$delivery_qnty;

								$lot=array_unique(explode(",",$lot_arr[$row[csf('id')]][$color_id]));
								$lot_span=count($lot);
								//echo $lot_span;
								$brand_val=$brand_arr[$row[csf('id')]][$color_id]['brand_id'];
								$color_rec_qty=$color_recv_arr[$row[csf('id')]][$color_id];
								$grey_color_qnty=$color_wise_qnty[$row[csf('id')]][$color_id];
								$m=0;
								foreach($lot as $lot_val)
								{
									$issue_qnty_lot=$lot_wise_qnty_array[$row[csf('id')]][$color_id][$lot_val]['lot'];

									//echo $lot_val.'<br>';
									$source=$lot_wise_qnty_array[$row[csf('id')]][$color_id][$lot_val]['source'];
									$party=$lot_wise_qnty_array[$row[csf('id')]][$color_id][$lot_val]['party'];
									$wo_qty_color=$wo_qty_arr[$row[csf('id')]][$color_id];
									//$process_loss_qnty=$grey_color_qnty-($grey_color_qnty*$txt_process_loss/100);
									$recv_qnty=$recv_arr[$row[csf('id')]][$color_id][$lot_val];
									$col_rec_qty[$color_id]+=$recv_qnty;
									$dyed_balance=$wo_qty_color-$color_rec_qty;
									$actual_process_loss=($grey_color_qnty-$recv_qnty)/$grey_color_qnty*100;
									$knittinig_party="";
									if ($source==1)
									{
										$knittinig_party=$company_short_arr[$party];
									}
									else if($source==3)
									{
										$knittinig_party=$supplier_arr[$party];
									}

								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $z; ?>','<? echo $bgcolor; ?>')" id="tr<? echo $z;?>">
                                	<?
									if($s==0)
									{
									?>
                                        <td width="40" rowspan="<? echo $k; ?>"><? echo $i; ?></td>
                                        <td width="80" rowspan="<? echo $k; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                                        <td width="110" rowspan="<? echo $k; ?>"><p><? echo $style_ref_no; ?>&nbsp;</p></td>
                                        <td width="120" rowspan="<? echo $k; ?>"><p><? echo $row[csf('ydw_no')]; ?></p></td>
                                        <td width="100" align="right" rowspan="<? echo $k; ?>"><? echo number_format($row[csf('qnty')],2,'.',''); ?>&nbsp;</td>
                                        <td width="100" align="right" rowspan="<? echo $k; ?>"><? echo number_format($delivery_qnty,2,'.',''); ?>&nbsp;</td>
                                        <td width="100" align="right" rowspan="<? echo $k; ?>"><? echo number_format($grey_balance,2,'.',''); ?>&nbsp;</td>
                                    <?
										$total_req_qnty+=$row[csf('qnty')];
										$tot_delivery_qnty+=$delivery_qnty;
										$total_grey_bl_qnty+=$grey_balance;
									}
									if($m==0)
									{
										?>
										<td width="100" align="right" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><? echo number_format($grey_color_qnty,2,'.',''); ?>&nbsp;</td>
										<td width="100" align="center" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><p><? if($color_id==0) echo "Select"; else echo $color_array[$color_id]; ?></p></td>
										<td width="100" align="right" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><? echo number_format($wo_qty_color,2,'.','');  ?>&nbsp;</td>
                                        <td width="100" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><p><? echo $brand_name_arr[$brand_val];  ?></p></td>
										<?
										$total_wo_qty_color+=$wo_qty_color;
									}
									?>
                                    <td width="100"><p><? echo $lot_val; ?></p></td>
									<td width="100" align="right"><? echo number_format($recv_qnty,2,'.',''); ?>&nbsp;</td>
                                    <?
                                    if($m==0)
									{
									?>
                                        <td width="100" align="right" rowspan="<? echo $rowspan_color_array[$color_id][$x]; ?>"><p><? echo number_format($dyed_balance,2,'.','');  ?></p></td>
                                    <?
									}
									?>
									<td width="100" align="right"><? echo number_format($issue_qnty_lot,2,'.',''); ?>&nbsp;</td>
                                    <td width="100" align="right"><? $issue_balance=$recv_qnty-$issue_qnty_lot; echo number_format($issue_balance,2,'.',''); ?>&nbsp;</td>
                                    <td><p><? echo $knittinig_party; ?></p></td>
								</tr>
							<?
									$total_color_wise_qnty+=$grey_color_qnty;
									$total_recv_qnty+=$recv_qnty;
									$total_issue+=$issue_qnty_lot;
									$total_issue_balance+=$issue_balance;

									$m++;
									$s++;
									$z++;
								}
								$x++;
								$total_dyed_bl_qnty+=$dyed_balance;
							}
							$i++;
						}
					?>
					<tfoot>
						<th colspan="4" align="right">Total</th>
						<th align="right"><?php echo number_format($total_req_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($tot_delivery_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_grey_bl_qnty,2); ?>&nbsp;</th>
                        <th align="right"><?php echo number_format($total_color_wise_qnty,2); ?>&nbsp;</th>
                        <th>&nbsp;</th>
						<th align="right"><?php echo number_format($total_wo_qty_color,2); ?>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
						<th align="right"><?php echo number_format($total_recv_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_dyed_bl_qnty,2); ?>&nbsp;</th>
						<th align="right"><?php echo number_format($total_issue,2); ?></th>
                        <th align="right"><?php echo number_format($total_issue_balance,2); ?></th>
                        <th>&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
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
}

if($action == "issue_details_popup")
{
	echo load_html_head_contents("WO No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	//$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
?>
</head>
<?

	$issue_data = sql_select("select b.issue_number, b.booking_no, b.booking_id , b.issue_date, c.lot, c.yarn_count_id, sum(a.cons_quantity) as issue_qnty from inv_transaction a, inv_issue_master b , product_details_master c where a.mst_id=b.id and a.prod_id = c.id and b.entry_form=3 and b.issue_basis=1 and b.issue_purpose=2 and a.job_no is null and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=2 and b.booking_id = $booking_id group by b.issue_number, b.booking_no, b.booking_id , b.issue_date, c.lot, c.yarn_count_id");

	$return_data = sql_select("select sum(a.cons_quantity) as iss_ret_qnty, b.booking_id,  b.booking_no, b.receive_date, b.recv_number, c.lot, c.yarn_count_id 		  from inv_transaction a, inv_receive_master b, product_details_master c, inv_issue_master d where a.mst_id=b.id and a.prod_id=c.id and a.issue_id=d.id and b.entry_form=9 and b.receive_basis=1 and a.company_id=$companyID and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.transaction_type=4 and d.issue_basis=1 and d.issue_purpose=2 and d.entry_form=3 and b.booking_id = $booking_id group by b.booking_id,  b.booking_no, b.receive_date, b.recv_number, c.lot, c.yarn_count_id");

?>
<body>
<div align="center">
	<fieldset style="width:680px;">
        <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
        	<thead>
        		<tr>
                   	<th colspan="7">Issue Details</th>
                </tr>
                <tr>
                	<th>Sl.</th>
	                <th>Issue Number</th>
	                <th>Issue Date</th>
	                <th>Booking No</th>
	                <th>Count</th>
	                <th>Lot</th>
	                <th>Issue Qty</th>
	            </tr>
            </thead>
            <tbody>
            	<? $i=1;
            	foreach($issue_data as $val){
            		?>
            	<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tri_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tri_<? echo $i;?>">
            		<td width="20"><p><? echo $i; ?></p></td>
                    <td align="center"><? echo $val[csf("issue_number")];?></td>
                    <td width="80"><p><? echo change_date_format($val[csf('issue_date')]); ?></p></td>
                    <td align="center"><? echo $val[csf("booking_no")];?></td>
                    <td align="center"><? echo $count_arr[$val[csf("yarn_count_id")]];?></td>
                    <td align="center"><? echo $val[csf("lot")];?></td>
                    <td align="right"><? echo $val[csf("issue_qnty")];?></td>
                </tr>
                <?
                $i++;
                $total_issue += $val[csf("issue_qnty")];
            }

            ?>
        	</tbody>
        	<tfoot>
            	<tr class="tbl_bottom">
                	<td colspan="6" align="right">Total Issue</td>
                    <td align="right">&nbsp;<? echo number_format($total_issue,2); ?>&nbsp;</td>
                </tr>
            </tfoot>
       	</table>
       	<br>
       	<table border="1" class="rpt_table" rules="all" width="670" cellpadding="0" cellspacing="0" align="center">
       		<thead>
       			<tr>
       				<th colspan="7">Issue Return Details</th>
       			</tr>
       			<tr>
       				<th width="30" align="right">Sl</th>
       				<th width="100" align="right">Issue. Ret. Number</th>
       				<th align="right">Ret. Date</th>
       				<th align="right">Booking No</th>
       				<th align="right">Count</th>
       				<th align="right">Lot</th>
       				<th>Ret. Qty</th>
       			</tr>
       		</thead>
       		<tbody>
       			<? $k=1;

   				foreach($return_data as $row)
   				{
   					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
   					?>
   					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
   						<td width=""><p><? echo $k; ?></p></td>
   						<td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
   						<td><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
   						<td><p><? echo $row[csf('booking_no')]; ?></p></td>
   						<td align="center"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
   						<td><p><? echo $row[csf('lot')]; ?></p></td>
   						<td align="right"><p><? echo number_format($row[csf('iss_ret_qnty')],2); ?></p></td>
   					</tr>
   					<?
   					$tot_issueRet_qnty+=$row[csf('iss_ret_qnty')];
   					$k++;
   				}


       			?>
       		</tbody>
       		<tfoot>
       			<tr class="tbl_bottom">
       				<td colspan="6" align="right">Total Issue Return</td>
       				<td align="right">&nbsp;<? echo number_format($tot_issueRet_qnty,2); ?>&nbsp;</td>
       			</tr>
       		</tfoot>
       	</table>
	</fieldset>
</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action == "receive_details_popup")
{
	echo load_html_head_contents("WO No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$booking_no = return_field_value("ydw_no", "wo_yarn_dyeing_mst", "id=" . $booking_id . "");

	?>
	</head>
	<?
	$rcv_data = sql_select("select a.receive_date, a.recv_number, sum(b.cons_quantity) as quantity, c.lot , c.yarn_count_id from inv_receive_master a, inv_transaction b, product_details_master c where a.id = b.mst_id and b.prod_id = c.id and a.company_id = $companyID and a.entry_form = 1 and a.item_category = 1 and a.receive_basis = 2 and a.receive_purpose = 2 and a.booking_id = $booking_id and c.color = $color and b.status_active = 1 and b.is_deleted =0 and b.status_active = 1 and b.is_deleted =0 group by a.receive_date, a.recv_number,  c.lot , c.yarn_count_id");
	$rcv_ret_data = sql_select("SELECT a.issue_number, a.issue_date, sum(c.cons_quantity) as quantity, d.lot, d.yarn_count_id FROM inv_issue_master a, inv_receive_master b, inv_transaction c, product_details_master d where a.received_id = b.id and a.id = c.mst_id and c.prod_id = d.id and a.entry_form = 8 and a.company_id = $companyID and c.item_category = 1 and b.entry_form = 1 and b.receive_basis = 2 and b.receive_purpose = 2 and b.booking_id = $booking_id and d.color = $color and c.is_deleted = 0 and c.status_active = 1 and a.status_active = 1 and a.is_deleted = 0 group by a.issue_number, a.issue_date,  d.lot, d.yarn_count_id");
	?>
	<body>
		<div align="center">
			<fieldset style="width:680px;">
				<table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<tr>
							<th colspan="7">Receive Details</th>
						</tr>
						<tr>
							<th>Sl.</th>
							<th>Receive Number</th>
							<th>Receive Date</th>
							<th>Booking No</th>
							<th>Lot</th>
							<th>Count</th>
							<th>Receive Qty</th>
						</tr>
					</thead>
					<tbody>
						<? $i=1;
						foreach($rcv_data as $val){
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tri_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tri_<? echo $i;?>">
								<td width="20"><p><? echo $i; ?></p></td>
								<td align="center"><? echo $val[csf("recv_number")];?></td>
								<td width="80"><p><? echo change_date_format($val[csf('receive_date')]); ?></p></td>
								<td align="center"><? echo $booking_no;?></td>
								<td align="center"><? echo $val[csf("lot")];?></td>
								<td align="center"><? echo $count_arr[$val[csf("yarn_count_id")]];?></td>
								<td align="right"><? echo $val[csf("quantity")];?></td>
							</tr>
							<?
							$i++;
							$total_rcv += $val[csf("quantity")];
						}

						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="6" align="right">Total Receive</td>
							<td align="right">&nbsp;<? echo number_format($total_rcv,2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
				<br>
	       	 <table border="1" class="rpt_table" rules="all" width="670" cellpadding="0" cellspacing="0" align="center">
	       		<thead>
	       			<tr>
	       				<th colspan="7">Receive Return Details</th>
	       			</tr>
	       			<tr>
	       				<th width="30" align="right">Sl</th>
	       				<th width="100" align="right">Receive Ret. Number</th>
	       				<th align="right">Ret. Date</th>
	       				<th align="right">Booking No</th>
	       				<th align="right">Lot</th>
	       				<th align="right">Count</th>
	       				<th>Ret. Qty</th>
	       			</tr>
	       		</thead>
	       		<tbody>
	       			<? $k=1;

	       	   				foreach($rcv_ret_data as $row)
	       	   				{
	       	   					if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	       	   					?>
	       	   					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
	       	   						<td width=""><p><? echo $k; ?></p></td>
	       	   						<td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
	       	   						<td><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
	       	   						<td><p><? echo $booking_no; ?></p></td>
	       	   						<td align="center"><p><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></p></td>
	       	   						<td><p><? echo $row[csf('lot')]; ?></p></td>
	       	   						<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
	       	   					</tr>
	       	   					<?
	       	   					$tot_rcv_ret_qnty+=$row[csf('quantity')];
	       	   					$k++;
	       	   				}


	       			?>
	       		</tbody>
	       		<tfoot>
	       			<tr class="tbl_bottom">
	       				<td colspan="6" align="right">Total Issue Return</td>
	       				<td align="right">&nbsp;<? echo number_format($tot_rcv_ret_qnty,2); ?>&nbsp;</td>
	       			</tr>
	       		</tfoot>
	       	</table>
	       </fieldset>
	   </div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
?>
