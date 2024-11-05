<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id = $_SESSION['logic_erp']["user_id"];
//--------------------------- Start-------------------------------------------
$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
//$sample_arr=return_library_array( "select id, sample_name from lib_sample",'id','sample_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$count_arr=return_library_array( "Select id, yarn_count from  lib_yarn_count where  status_active=1",'id','yarn_count');

function get_spacial_terms_con($width = "100%", $entry_form = '') {
	$html = '
	<table  width=' . $width . ' class="rpt_table"   border="1" cellpadding="0" cellspacing="0" rules="all">
	<thead>
	<tr style="border:1px solid black;">
	<th width="3%" style="border:1px solid black;">Sl</th><th width="97%" style="border:1px solid black;">Terms and Condition</th>
	</tr>
	</thead>
	<tbody>';

	//if ($entry_form != '') {$entry_form_con = " and entry_form=$entry_form";}
	$data_array = sql_select("select id, terms from  lib_terms_condition where is_default=1 and page_id='" . str_replace("'", "", $entry_form) . "'");
	if (count($data_array) > 0) {
		$i = 0;
		foreach ($data_array as $row) {
			$i++;
			$html .= '
			<tr id="settr_1" align="" style="border:1px solid black;">
			<td style="border:1px solid black;">' . $i . '</td>
			<td style="border:1px solid black;">' . $row[csf('terms')] . '</td>
			</tr>';
		}
	}

	$html .= '
	</tbody>
	</table>';
	if ($entry_form) {return $html;} else {return "";}
}

if($action=="load_drop_down_supplier")
{


	if($data==5 || $data==3 || $data==20){
		echo create_drop_down( "cbo_supplier_name", 160, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "",0,"" );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_tag_company c where a.id=c.supplier_id  and a.status_active =1 and a.id in(select supplier_id from lib_supplier_party_type where party_type=2) and a.id in(select supplier_id from lib_supplier_party_type where party_type=21) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "--Select Supplier--",$selected,"","");
	}
	exit();

}

if($action=="load_drop_down_inhouse_company")
{
	list($comapny,$party_type,$paymode_type)=explode('_',$data);
	if ($paymode_type==3 || $paymode_type==5) {
		echo create_drop_down( "cbo_supplier_name", 160, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 1, "-- Select Company --", $selected, "",0 );
	}
	else
	{
		if($party_type==15 || $party_type==50 || $party_type==51){$party_type=' and b.party_type in(93)';}//Twisting
		else if($party_type==38){$party_type=' and b.party_type in(94)';}//Re-Waxing
		else{$party_type=' and b.party_type in(93,94)';}

		echo create_drop_down( "cbo_supplier_name", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$comapny and a.status_active =1 $party_type group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "get_php_form_data(this.value, 'set_attention', 'requires/yarn_service_work_order_controller' );",0 );
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_withInGroup")
{
	list($within_group,$company_id)=explode('_',$data);
	if($within_group == 2)
	{
		echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 145, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name",1, "-- Select Buyer --", $company_id,"",0);
	}

	exit();
}

if($action=="set_attention")
{
	$sql="select contact_person from lib_supplier where id=$data";
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_attention').val('".$row[csf("contact_person")]."');\n";
	}
	exit();
}




if ($action=="job_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
		$("#hidden_job_no").val(str); // wo/pi id
		parent.emailwindow.hide();
	}

</script>

<div align="center" style="width:615px;" >
	<form name="searchjob"  id="searchjob" autocomplete="off">
		<table width="500" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" id="table1">
			<thead>
            	<tr>
                    <th colspan="<? if($is_sales_order == 1) echo 6; else echo 5; ?> "><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
            	<tr>
                    <th width="145">Company</th>
                    <?
                    if($is_sales_order == 1)
                    {
                        ?>
                        <th width="100">Within Group</th>
                        <?
                    }
                    ?>
                    <th width="145">Buyer</th>
                    <th width="100">
                        <?
                        $orderTitle= ($is_sales_order == 1)? "Sales Order No." : "Order No";
                        echo $orderTitle;
                        ?>
                    </th>
                    <?
                    if($is_sales_order == 1)
                    {
                        ?>
                        <th width="100">Booking No</th>
                        <?
                    }else{
                        ?>
                        <th width="100">Job No</th>
                        <?
                    }
                    ?>
                    <th>
                        <input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton" onClick="reset_form('searchjob','search_div','')" />
                        <input type="hidden" name="txt_is_sales" id="txt_is_sales" value="<? echo $is_sales_order;?>">
                    </th>
                </tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<?
						echo create_drop_down( "cbo_company_name", 145, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", str_replace("'","",$company), "load_drop_down( 'yarn_service_work_order_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
						?>&nbsp;
					</td>
					<?
					if($is_sales_order == 1)
					{
						?>
						<td><? echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 1, "-- Select --","","");?></td>
						<?
					}
					?>
					<td align="center" id="buyer_td">
						<?
						$buyer_qrery="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name";
						echo create_drop_down( "cbo_buyer_name", 145, $buyer_qrery,"id,buyer_name", 1, "-- Select Buyer --",0);
						?>
					</td>
					<td align="center">
						<input type="text" name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px" />
					</td>
					<?
					if($is_sales_order == 1)
					{
						?>
						<td><input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:100px" /></td>
						<?
					}else{
						?>
						<td><input type="text" name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:100px" /></td>
						<?
					}
					?>
					<td align="center">
						<?
						if($is_sales_order == 1)
						{
							?>
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_is_sales').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_job_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
							<?
						}else
						{
							?>

							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_is_sales').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_job_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
							<?
						}
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div align="center" valign="top" id="search_div"> </div>
		<input type="hidden" id="hidden_job_no">
	</form>
</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
exit();
}

if ($action=="create_job_search_list_view")
{
	$data=explode('_',$data);
	//print_r($data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_buyer_name=str_replace("'","",$data[1]);
	$txt_order_no=str_replace("'","",$data[2]);
	$is_sales_order=str_replace("'","",$data[3]);

	if($is_sales_order == 2)
	{
		//list($cbo_company_name,$cbo_buyer_name,$txt_order_no)=explode("_",$data);
		if($cbo_company_name!=0) $cbo_company_name="and a.company_name='$cbo_company_name'"; else $cbo_company_name="";
		if($cbo_buyer_name!=0) $cbo_buyer_name="and a.buyer_name='$cbo_buyer_name'"; else $cbo_buyer_name="";
		
		$string_search_type=$data[5];
		$order_cond="";
		
		if($string_search_type==1)
		{
			if($txt_order_no!="") $order_cond="and b.po_number='$txt_order_no'"; else $order_cond="";
		}
		else if($string_search_type==2)
		{
			if($txt_order_no!="") $order_cond="and b.po_number like '$txt_order_no%'"; else $order_cond="";
		}
		else if($string_search_type==3)
		{
			if($txt_order_no!="") $order_cond="and b.po_number like '%$txt_order_no'"; else $order_cond="";
		}
		else if($string_search_type==4 || $string_search_type==0)
		{
			if($txt_order_no!="") $order_cond="and b.po_number like '%$txt_order_no%'"; else $order_cond="";
		}
		
		if($data[4]!="") $job_cond="and a.job_no_prefix_num='".$data[4]."'"; else $job_cond="";

		$sql="select a.id,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.order_uom,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst  and b.shiping_status not in(3) $cbo_company_name $cbo_buyer_name $order_cond $job_cond group by a.id,a.job_no, a.company_name, a.buyer_name, a.style_ref_no,a.order_uom,b.po_number order by a.id desc";

		?>
		<div style="width:615px;" align="left">

			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="595" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="80">Job No</th>
					<th width="100">Buyer</th>
					<th>Style Ref.No</th>
					<th width="100">Order No</th>
					<th width="50">Order Uom</th>
				</thead>
			</table>
			<div style="width:615px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="595" class="rpt_table" id="list_view" >
					<?
					$i=1;
					$job_sql=sql_select( $sql );
					foreach ($job_sql as $rows)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $rows[csf('id')].','.$rows[csf('job_no')]; ?>'); ">

							<td width="40" align="center"><p> <? echo $i; ?></p></td>
							<td width="80" align="center"><? echo $rows[csf("job_no")]; ?></td>
							<td width="100"><p><?  echo  $buyer_arr[$rows[csf('buyer_name')]]; ?></p></td>
							<td><p><? echo $rows[csf('style_ref_no')]; ?></p></td>
							<td width="100"><p><? echo $rows[csf('po_number')]; ?></p></td>
							<td width="50" align="center"><? echo $unit_of_measurement[$rows[csf('order_uom')]]; ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</table>
			</div>
		</div>
		<?
	}
	if($is_sales_order==1)
	{
    	//list($cbo_company_name,$cbo_buyer_name,$txt_order_no,$txt_booking_no,$cbo_within_group,$txt_is_sales)=explode("_",$data);
		$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
		$txt_booking_no=str_replace("'","",$data[4]);
		$cbo_within_group=str_replace("'","",$data[5]);

		if($cbo_company_name!=0) $cbo_company_name="and a.company_id='$cbo_company_name'"; else $cbo_company_name="";

		if($cbo_buyer_name!=0)
		{
			$cbo_buyer_cond_1=" and c.buyer_id='$cbo_buyer_name'";
			$cbo_buyer_cond_2=" and a.buyer_id='$cbo_buyer_name'";
		}
		else
		{
			$cbo_buyer_cond_1="";
			$cbo_buyer_cond_2="";
		}
		
		$string_search_type=$data[6];
		
		if($string_search_type==1)
		{
			if($txt_order_no!="") $order_cond="and a.job_no='".trim($txt_order_no)."'"; else $order_cond="";
			if($txt_booking_no!="") $booking_cond="and a.sales_booking_no='".trim($txt_booking_no)."'"; else $booking_cond="";
		}
		else if($string_search_type==2)
		{
			if($txt_order_no!="") $order_cond="and a.job_no like '".trim($txt_order_no)."%'"; else $order_cond="";
			if($txt_booking_no!="") $booking_cond="and a.sales_booking_no like '".trim($txt_booking_no)."%'"; else $booking_cond="";
		}
		else if($string_search_type==3)
		{
			if($txt_order_no!="") $order_cond="and a.job_no like '%".trim($txt_order_no)."'"; else $order_cond="";
			if($txt_booking_no!="") $booking_cond="and a.sales_booking_no like '%".trim($txt_booking_no)."'"; else $booking_cond="";
		}
		else if($string_search_type==4 || $string_search_type==0)
		{
			if($txt_order_no!="") $order_cond="and a.job_no like '%".trim($txt_order_no)."%'"; else $order_cond="";
			if($txt_booking_no!="") $booking_cond="and a.sales_booking_no like '%".trim($txt_booking_no)."%'"; else $booking_cond="";
		}
		
		//if($cbo_within_group!=0) $within_group_cond=" and a.within_group = '$cbo_within_group'"; else $within_group_cond="";

		if($db_type == 1) $select_uom = " group_concat(b.order_uom) as order_uom"; else $select_uom = " listagg(b.order_uom,',' ) within group (order by b.order_uom) order_uom";
		$sql1= "select a.id, a.company_id, c.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, $select_uom
		from fabric_sales_order_mst a,fabric_sales_order_dtls b, wo_booking_mst c
		where a.id = b.mst_id and a.booking_id = c.id and a.status_active=1 and b.status_active=1 and b.status_active=1 $cbo_company_name $cbo_buyer_cond_1 $order_cond $booking_cond and a.within_group = 1
		group by a.id, a.company_id, c.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no order by a.id desc";

		$sql2= "select a.id, a.company_id, a.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no, $select_uom
		from fabric_sales_order_mst a,fabric_sales_order_dtls b
		where a.id = b.mst_id and a.status_active=1 and b.status_active=1 and b.status_active=1 $cbo_company_name $cbo_buyer_cond_2 $order_cond $booking_cond and a.within_group = 2
		group by a.id, a.company_id, a.buyer_id, a.style_ref_no, a.job_no, a.within_group, a.sales_booking_no
		order by id DESC";
		if($cbo_within_group==1){
			$sql = $sql1;
		}
		else if($cbo_within_group==2)
		{
			$sql = $sql2;
		}else{
			$sql = $sql1. " union all ". $sql2;
		}

		 //echo $sql;
		?>
		<div style="width:650px;"; align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="100">Buyer</th>
					<th width="140">Sales Order No</th>
					<th width="140">Booking No</th>
					<th width="100">Within Group</th>
					<th width="">Order Uom</th>
				</thead>
			</table>
			<div style="width:650px; overflow-y:scroll; max-height:250px;" id="buyer_list_view">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="630" class="rpt_table" id="list_view" >
					<?
					$i=1;
					$job_sql=sql_select( $sql );
					foreach ($job_sql as $rows)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $rows[csf('id')].','.$rows[csf('job_no')]; ?>'); ">
							<td width="40" align="center"><p> <? echo $i; ?></p></td>
							<td width="100">
								<p>
									<?
									/*if($rows[csf("within_group")] == 1)
									{
										echo $company_arr[$rows[csf('buyer_id')]];
									}
									else
									{*/
										echo  $buyer_arr[$rows[csf('buyer_id')]];
									//}
										?>
									</p>
								</td>
								<td width="140" align="center"><? echo $rows[csf("job_no")]; ?></td>
								<td width="140"><p><? echo $rows[csf('sales_booking_no')]; ?></p></td>
								<td width="100" align="center"><p><? if ($rows[csf('within_group')] == 1) echo "Yes"; else echo "No"; ?></p></td>
								<td width="" align="center">
									<?
									$uom= ""; $uom_arr = array();
									$uom_arr =  array_unique(explode(",", $rows[csf('order_uom')]));
									foreach ($uom_arr as $val)
									{
										$uom .= $unit_of_measurement[$val].",";
									}
									echo chop($uom,",");
									?>
								</td>
							</tr>
							<?
							$i++;
						}
						?>
					</table>
				</div>
			</div>
			<?
		}
		exit();
	}


	if($action=="lot_search_popup")
	{
		echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$company=str_replace("'","",$company);
		$job_no=str_replace("'","",$job_no);
		$is_sales_order=str_replace("'","",$is_sales_order);
		$cbo_service_type=str_replace("'","",$cbo_service_type);

		$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$supplier_arr 	= return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
		$prod_data 		= sql_select("select id, supplier_id, yarn_count_id, yarn_type from product_details_master where item_category_id=1 and company_id=$company and status_active=1 and is_deleted=0");

		foreach ($prod_data as $row) {
			$supplierArr[$row[csf('supplier_id')]] = $supplier_arr[$row[csf('supplier_id')]];
			$countArr[$row[csf('yarn_count_id')]]  = $count_arr[$row[csf('yarn_count_id')]];
			$yarn_type_arr[$row[csf('yarn_type')]] = $yarn_type[$row[csf('yarn_type')]];
		}
		?>
		<script>
			var selected_id = new Array();
			function js_set_value2(str)
			{
				$("#hidden_product").val(str);
				parent.emailwindow.hide();
			}

			function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

			function js_set_value( str, i )
			{
				toggle( document.getElementById( 'search' + i ), '#FFFFCC' );

				if( jQuery.inArray( str, selected_id ) == -1 ) {
					selected_id.push(str);
				}
				else {
					for( var i = 0; i < selected_id.length; i++ ) {
						if( selected_id[i] == str ) break;
					}
					selected_id.splice( i, 1 );
				}

				var product_id = '';
				for( var i = 0; i < selected_id.length; i++ ) {
					product_id += selected_id[i] + '#';
				}

				product_id = product_id.substr( 0, product_id.length - 1 );

				$("#hidden_product").val(product_id);
				return;
			}
		</script>
	</head>
	<body>
		<div style="" align="center" >
			<fieldset>
				<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
					<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" style="">
						<thead>
                        	<tr>
                                <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                            </tr>
							<tr>
								<th width="">Supplier</th>
								<th width="">Count</th>
								<th width="">Yarn Description</th>
								<th width="">Type</th>
								<th width="">Lot</th>
								<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;"></th>
							</tr>
						</thead>
						<tr>
							<td><? echo create_drop_down("cbo_supplier", 120, $supplier_arr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td><? echo create_drop_down("cbo_count", 100, $count_arr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td><input type="text" name="txt_desc" id="txt_desc" class="text_boxes" style="width:130"></td>
							<td><? echo create_drop_down("cbo_type", 100, $yarn_type_arr, "", 1, "-- Select --", '', "", 0); ?></td>
							<td>
								<input name="txt_lot_no" id="txt_lot_no" class="text_boxes" style="width:100px" placeholder="Write">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"   onClick="show_list_view (document.getElementById('cbo_supplier').value+'**'+document.getElementById('cbo_count').value+'**'+document.getElementById('txt_desc').value+'**'+document.getElementById('cbo_type').value+'**'+document.getElementById('txt_lot_no').value+'**'+'<? echo $company; ?>'+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $is_sales_order; ?>'+'**'+'<? echo $cbo_service_type; ?>'+'**'+'<? echo $cbo_with_order; ?>'+'**'+document.getElementById('cbo_string_search_type').value, 'create_lot_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'table_charge\',-1)');" style="width:70px;"/>
							</td>
						</tr>
					</table>
					<br>
					<table align="center">
						<tr>
							<td align="center" valign="top" id="search_div"></td>
						</tr>
					</table>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		$("#flt13_table_charge").css("display","none");
	</script>
	</html>
	<?
	exit();
}

if($action=="create_lot_search_list_view")
{
	$data=explode('**',$data);

	if ($data[0] == 0) $supp_cond = ""; else $supp_cond = " and a.supplier_id='" . trim($data[0]) . "' ";
	if ($data[1] == 0) $yarn_count_cond = ""; else $yarn_count_cond = " and a.yarn_count_id='" . trim($data[1]) . "' ";
	if ($data[3] == 0) $yarn_type_cond = ""; else $yarn_type_cond = " and a.yarn_type='" . trim($data[3]) . "' ";

	$string_search_type=$data[10];
	$yarn_desc_cond=""; $lot_no_cond="";
	
	if($string_search_type==1)
	{
		$yarn_desc_cond = " and a.product_name_details='" . trim($data[2]) . "'";
		$lot_no_cond 	= " and a.lot='" . trim($data[4]) . "'";
	}
	else if($string_search_type==2)
	{
		$yarn_desc_cond = " and a.product_name_details like '" . trim($data[2]) . "%'";
		$lot_no_cond 	= " and a.lot like '" . trim($data[4]) . "%'";
	}
	else if($string_search_type==3)
	{
		$yarn_desc_cond = " and a.product_name_details like '%" . trim($data[2]) . "'";
		$lot_no_cond 	= " and a.lot like '%" . trim($data[4]) . "'";
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		$yarn_desc_cond = " and a.product_name_details like '%" . trim($data[2]) . "%'";
		$lot_no_cond 	= " and a.lot like '%" . trim($data[4]) . "%'";
	}
	
	$companyID 		= $data[5];
	$job_no 		= $data[6];
	$is_sales_order = $data[7];
	$service_type 	= $data[8];
	$cbo_with_order = $data[9];

	$color_library 	= return_library_array("select id, color_name from lib_color", "id", "color_name");
	$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$supplier_arr 	= return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$buyer_arr 		= return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	?>
</head>
<body>
	<div>
		<fieldset>
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="99%" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center"  rules="all">
					<thead>
						<tr>
							<th width="40">Sl No</th>
							<th width="70">Buyer ID</th>
							<th width="70">Count</th>
							<th width="170">Composition</th>
							<th width="80">Type</th>
							<th width="80">Color</th>
							<th width="80">Lot No</th>
							<th width="140">Supplier</th>
							<th width="80">Current Stock</th>
							<!-- <th width="80">Allocated Qnty</th> -->
							<th width="80">Available For Req.</th>
							<th width="80">Age (Days)</th>
							<th width="80">DOH</th>
						</tr>
					</thead>
				</table>
				<?
					if($is_sales_order==1) //Sales Yes
					{
						//$union_query="(select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,sum(c.qnty) yarn_qnty,b.buyer_id from inv_material_allocation_mst c,fabric_sales_order_mst b,product_details_master a where c.booking_no=b.sales_booking_no and c.item_id=a.id and b.job_no='$job_no' and c.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.qnty>0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,b.buyer_id)";// and c.is_dyied_yarn=1
						$union_query="(select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,b.buyer_id, sum(c.order_qnty) as yarn_qnty from fabric_sales_order_mst b,product_details_master a, inv_transaction c  where b.job_no = c.job_no and b.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.item_category=1 group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,b.buyer_id)";
					}
					else
					{
						//$union_query="(select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,sum(c.qnty) yarn_qnty,b.buyer_name as buyer_id from inv_material_allocation_mst c,wo_po_details_master b,product_details_master a where c.job_no=b.job_no and c.item_id=a.id and b.job_no='$job_no' and c.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.qnty>0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,b.buyer_name)";
						$union_query="(select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,sum(c.order_qnty) yarn_qnty,b.buyer_name as buyer_id
						from product_details_master a, inv_transaction c, wo_po_details_master b
						where a.id=c.prod_id and c.job_no=b.job_no and  b.job_no='$job_no' and c.item_category=1 and c.transaction_type=1 and c.entry_form=248 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond
						group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,b.buyer_name)";
					}
					//echo $union_query; die;
					if($cbo_with_order==1){
						$sql = $union_query;
					}else{
						$sql = "(select a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty,a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id, a.yarn_type,a.color,sum(b.yarn_qnty) yarn_qnty,d.buyer_id from product_details_master a left join ppl_yarn_requisition_entry b on a.id=b.prod_id left join inv_transaction d on (a.id=d.prod_id and d.receive_basis=4)  where a.company_id=$companyID and a.current_stock>0 and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $supp_cond $yarn_count_cond $yarn_type_cond $yarn_desc_cond $lot_no_cond group by a.id, a.supplier_id,a.lot,a.current_stock,a.allocated_qnty,a.available_qnty, a.yarn_comp_type1st,a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd,a.yarn_count_id,a.yarn_type,a.color,d.buyer_id)";//union $union_query
					}

					$sql_result=sql_select($sql);
					foreach($sql_result as $row)
					{
						$product_id_arr[$row[csf("id")]] = $row[csf("id")];
					}

					$date_array = array();
					if(!empty($product_id_arr)){
						$returnRes_date = "select prod_id, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category=1 and prod_id in(".implode(",",$product_id_arr).") group by prod_id";
						$result_returnRes_date = sql_select($returnRes_date);
						foreach ($result_returnRes_date as $row) {
							$date_array[$row[csf("prod_id")]]['min_date'] = $row[csf("min_date")];
							$date_array[$row[csf("prod_id")]]['max_date'] = $row[csf("max_date")];
						}
					}
					?>
					<div style=" overflow-y:scroll; max-height:250px;font-size:12px;">
						<table width="99%" cellspacing="0" cellpadding="0" border="0" class="rpt_table"  style="cursor:pointer" rules="all" id="table_charge">
							<tbody>
								<?
								if(!empty($sql_result)){
									$i=1;
									foreach($sql_result as $row)
									{
										//$yarn_qnty=($cbo_with_order==1)?$row[csf("allocated_qnty")]:$row[csf("available_qnty")];
										$yarn_qnty = $row[csf("available_qnty")];
										if($yarn_qnty > 0)
										{
											if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

											$compos = '';
											if ($row[csf('yarn_comp_percent2nd')] != 0) {
												$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
											} else {
												$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%";
											}
											$available_qnty = $row[csf('available_qnty')];
											$ageOfDays = datediff("d", $date_array[$row[csf("id")]]['min_date'], date("Y-m-d"));
											$daysOnHand = datediff("d", $date_array[$row[csf("id")]]['max_date'], date("Y-m-d"));
											$js_function=($service_type==15 || $service_type==50 || $service_type==51)?"":"2";
											//$yarn_qnty=($cbo_with_order==1)?$row[csf("allocated_qnty")]:$row[csf("available_qnty")];
											$yarn_qnty = $row[csf("available_qnty")];

											?>
											<input type="hidden" name="txt_product_id" id="txt_product_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value<? echo $js_function;?>('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>*<? echo $row[csf("yarn_count_id")]; ?>*<? echo $row[csf("lot")]; ?>*<? echo $row[csf("id")]; ?>*<? echo $yarn_qnty; ?>*<? echo $row[csf("color")]; ?>',<? echo $i;?>)" id="search<? echo $i;?>">
												<td width="40" align="center"><? echo $i; ?></td>
												<td width="70" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
												<td width="70" align="center"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
												<td width="170" align="center"><p><? echo $compos; ?></p></td>
												<td width="80" align="center"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
												<td width="80" align="center"><p><? echo $color_library[$row[csf('color')]]; ?></p></td>
												<td width="80" align="center"><p><? echo $row[csf('lot')]; ?></p></td>
												<td width="140" align="center"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
												<td width="80" align="right"><? echo number_format($row[csf('current_stock')], 2); ?></td>
												<!-- <td width="80" align="right"><? echo number_format($row[csf('allocated_qnty')], 2); ?></td> -->
												<td width="80" align="right"><? echo number_format($available_qnty, 2); ?></td>
												<td width="80" align="center"><? echo $ageOfDays; ?></td>
												<td width="80" align="center"><? echo $daysOnHand; ?></td>
											</tr>
											<?
											$i++;
										}
									}
								}else{
									$msg = ($cbo_with_order==1)?"No Receive  Yarn Found":"No Yarn Found";
									echo "<tr><td colspan='13' align='center'>".$msg."</td></tr>";
								}
								?>
								<input type="hidden" id="hidden_product" style="width:100px;" />
							</tbody>
						</table>
						<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
							<tr>
								<td align="center" height="30" valign="bottom">
									<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px;" />
								</td>
							</tr>
						</table>
					</div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, tna_integrated, copy_quotation, publish_shipment_date, po_update_period, po_current_date, season_mandatory, excut_source, cost_control_source, color_from_library from variable_order_tracking where company_name=$data and variable_list in (23) and status_active=1 and is_deleted=0 order by variable_list ASC");
    $color_from_lib=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
	}
	echo $color_from_lib;
 	exit();
}
if($action=="color_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <?
            $sql="select id, color_name FROM lib_color  WHERE status_active=1 and is_deleted=0";
            echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "",'setFilterGrid("list_view",-1);','0') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id)!="") //update
		{
			//check sys id for update or insert
			$id= return_field_value("id"," wo_yarn_dyeing_mst","id=$update_id");
			$field_array="company_id*service_type*supplier_id*booking_date*delivery_date*currency*ecchange_rate*pay_mode*source*attention*updated_by*update_date*status_active*is_deleted*booking_without_order*ref_no";
			$data_array="".$cbo_company_name."*".$cbo_service_type."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*'".$user_id."'*'".$pc_date_time."'*1*0*".$cbo_with_order."*".$txt_ref_no;
			$return_no=str_replace("'",'',$txt_booking_no);
		}
		else // new insert
		{
			$id=return_next_id("id", "wo_yarn_dyeing_mst", 1);
			$new_sys_number = explode("*", return_next_id_by_sequence("WO_YARN_DYEING_MST_YSW_PK_SEQ", "wo_yarn_dyeing_mst",$con,1,$cbo_company_name,'SYSW',335,date("Y",time()),0 ));

			$field_array="id,yarn_dyeing_prefix,yarn_dyeing_prefix_num,ydw_no,entry_form,company_id,service_type,supplier_id,booking_date,delivery_date,currency,ecchange_rate,pay_mode,source,attention,is_sales,ref_no,inserted_by,insert_date,status_active,is_deleted,booking_without_order";
			$data_array="(".$id.",'".$new_sys_number[1]."','".$new_sys_number[2]."','".$new_sys_number[0]."',335,".$cbo_company_name.",".$cbo_service_type.",".$cbo_supplier_name.",".$txt_booking_date.",".$txt_delivery_date.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_pay_mode.",".$cbo_source.",".$txt_attention.",".$cbo_is_sales_order.",".$txt_ref_no.",'".$user_id."','".$pc_date_time."',1,0,".$cbo_with_order.")";
			$return_no=str_replace("'",'',$new_sys_number[0]);
		}

		$dtlsid=return_next_id("id","wo_yarn_dyeing_dtls", 1);
		$field_array_dts="id,mst_id,job_no,job_no_id,entry_form,product_id,count,color,yarn_description,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,remarks,status_active,is_deleted";
		$dtls_ids="";
		$job_no=$dyeing_charge="";
		for($j=0; $j<$tot_row; $j++)
		{
			$txt_job_no 		= "txt_job_no_".$j;
			$txt_job_id 		= "txt_job_id_".$j;
			$txt_lot 			= "txt_lot_".$j;
			$txt_pro_id 		= "txt_pro_id_".$j;
			$cbo_count 			= "cbo_count_".$j;
			$cbo_color 			= "cbo_color_".$j;
			$txt_item_des 		= "txt_item_des_".$j;
			$cbo_uom 			= "cbo_uom_".$j;
			$txt_wo_qty 		= "txt_wo_qty_".$j;
			$txt_rate 			= "txt_rate_".$j;
			$txt_amount 		= "txt_amount_".$j;
			$txt_bag 			= "txt_bag_".$j;
			$txt_cone 			= "txt_cone_".$j;
			$txt_min_req_cone 	= "txt_min_req_cone_".$j;
			$txt_remarks 		= "txt_remarks_".$j;

			if($data_array_dts!="") $data_array_dts.=",";
			$data_array_dts.="(".$dtlsid.",".$id.",'".$$txt_job_no."','".$$txt_job_id."',335,'".$$txt_pro_id."','".$$cbo_count."',".$$cbo_color.",'".$$txt_item_des."',".$$cbo_uom.",'".$$txt_wo_qty."','".$$txt_rate."','".$$txt_amount."','".$$txt_bag."','".$$txt_cone."','".$$txt_min_req_cone."','".$$txt_remarks."',1,0)";
			$dtls_ids.=$dtlsid.",";
			$dtlsid=$dtlsid+1;
			$job_no = $$txt_job_no;
			$dyeing_charge = $$txt_rate;
		}

		$data_array_fin_prod="";
		if(str_replace("'",'',$cbo_service_type) == 15 || str_replace("'",'',$cbo_service_type) == 50 || str_replace("'",'',$cbo_service_type) == 51){
			$fin_prod_id=return_next_id("id", "wo_yarn_dyeing_dtls_fin_prod", 1);

			/*if (str_replace("'", "", trim($txt_fin_color)) != "") {
				$color_id = return_id(str_replace("'", "", trim($txt_fin_color)), $color_library, "lib_color", "id,color_name");
			} else {
				$color_id = 0;
			}*/
			
			if(str_replace("'", "", trim($txt_fin_color))!="")
			{
				if (!in_array(str_replace("'", "", trim($txt_fin_color)),$new_array_color)){
					$color_id = return_id( str_replace("'", "", trim($txt_fin_color)), $color_library, "lib_color", "id,color_name","335");  
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_fin_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_fin_color)), $new_array_color);
			}
			else $color_id =0;

			$field_array_fin_prod="id,mst_id,dtls_id,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color,status_active,is_deleted,job_no,yarn_rate";
			$data_array_fin_prod="(".$fin_prod_id.",".$id.",'".trim($dtls_ids,", ")."',".$cbo_fin_count.",".$cbo_fin_composition.",".$txt_fin_perc.",".$cbo_fin_type.",".$color_id.",1,0,'".$job_no."',".$dyeing_charge.")";
		}

		if(str_replace("'","",$update_id)!="") //update
		{
			$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$id,1);
		}
		else
		{
			//echo "10**"."insert into wo_yarn_dyeing_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("wo_yarn_dyeing_mst",$field_array,$data_array,0);
		}

		$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,1);

		if($data_array_fin_prod!="")
		{
			//echo "10**"."insert into wo_yarn_dyeing_dtls_fin_prod (".$field_array_fin_prod.") values ".$data_array_fin_prod;die;
			$finProdrID=sql_insert("wo_yarn_dyeing_dtls_fin_prod",$field_array_fin_prod,$data_array_fin_prod,1);
		}else{
			$finProdrID=1;
		}
		//echo '10**'.$rID.'**'.$dtlsrID.'**'.$finProdrID;die;
		//echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);die;

		if($db_type==0)
		{
			if($rID && $dtlsrID && $finProdrID)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		if($db_type==1 || $db_type==2)
		{
			if($rID && $dtlsrID && $finProdrID)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$return_no)."**".str_replace("'",'',$id);
			}
		}

		disconnect($con);
		die;
	}

	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		//check update id
		if( str_replace("'","",$update_id) == "")
		{
			echo "15";disconnect($con);exit();
		}


		/*$duplicate = is_duplicate_field("id","wo_yarn_dyeing_dtls"," id!=$dtls_update_id and job_no_id='$txt_sample_id' and product_id='$txt_pro_id' and count='$cbo_count' and yarn_description='$txt_item_des' and yarn_color='$color_id' and color_range='$cbo_color_range' and referance_no='$txt_ref_no' and entry_form=94");

		if($duplicate==1)
		{
			echo "11**Duplicate is Not Allow in Same Job Number.";
			die;
		}*/

		//mst part.......................
		$field_array="company_id*service_type*supplier_id*booking_date*delivery_date*currency*ecchange_rate*pay_mode*source*attention*is_sales*updated_by*update_date*status_active*is_deleted*booking_without_order*ref_no";
		$data_array="".$cbo_company_name."*".$cbo_service_type."*".$cbo_supplier_name."*".$txt_booking_date."*".$txt_delivery_date."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_pay_mode."*".$cbo_source."*".$txt_attention."*".$cbo_is_sales_order."*'".$user_id."'*'".$pc_date_time."'*1*0*".$cbo_with_order."*".$txt_ref_no;

		$dtlsid=return_next_id("id","wo_yarn_dyeing_dtls", 1);
		$field_array_dts="id,mst_id,job_no,job_no_id,entry_form,product_id,count,color,yarn_description,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag,no_of_cone,min_require_cone,remarks,status_active,is_deleted";

		$dtls_ids=$job_no=$dyeing_charge=$product_ids="";
		$total_wo_qnty=0;
		for($j=0; $j<$tot_row; $j++)
		{
			$txt_job_no 		= "txt_job_no_".$j;
			$txt_job_id 		= "txt_job_id_".$j;
			$txt_lot 			= "txt_lot_".$j;
			$txt_pro_id 		= "txt_pro_id_".$j;
			$cbo_count 			= "cbo_count_".$j;
			$cbo_color 			= "cbo_color_".$j;
			$txt_item_des 		= "txt_item_des_".$j;
			$cbo_uom 			= "cbo_uom_".$j;
			$txt_wo_qty 		= "txt_wo_qty_".$j;
			$txt_rate 			= "txt_rate_".$j;
			$txt_amount 		= "txt_amount_".$j;
			$txt_bag 			= "txt_bag_".$j;
			$txt_cone 			= "txt_cone_".$j;
			$txt_min_req_cone 	= "txt_min_req_cone_".$j;
			$txt_remarks 		= "txt_remarks_".$j;

			if($data_array_dts!="") $data_array_dts.=",";
			$data_array_dts.="(".$dtlsid.",".$update_id.",'".$$txt_job_no."','".$$txt_job_id."',335,'".$$txt_pro_id."','".$$cbo_count."',".$$cbo_color.",'".$$txt_item_des."',".$$cbo_uom.",'".$$txt_wo_qty."','".$$txt_rate."','".$$txt_amount."','".$$txt_bag."','".$$txt_cone."','".$$txt_min_req_cone."','".$$txt_remarks."',1,0)";

			$dtls_ids.=$dtlsid.",";
			$dtlsid=$dtlsid+1;
			$job_no = $$txt_job_no;
			$dyeing_charge = $$txt_rate;
			$product_ids .= $$txt_pro_id.",";
			$total_wo_qnty += $$txt_wo_qty;

		}
		//echo "10**"."insert into wo_yarn_dyeing_dtls (".$field_array_dts.") values ".$data_array_dts;die;

		$product_ids = rtrim($product_ids,", ");
		$total_issue_qty = return_field_value("sum(c.cons_quantity) as cons_quantity", "wo_yarn_dyeing_mst a,inv_issue_master b, inv_transaction c"," b.booking_id=a.id and b.id=c.mst_id and a.ydw_no=$txt_booking_no and b.booking_no=$txt_booking_no and c.prod_id in($product_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=2","cons_quantity");

		if($total_wo_qnty<$total_issue_qty)
		{
			echo "22**Yarn Service Work Order quantity can not be less than issue quantity.\nIssue quantity = ".$total_issue_qty;disconnect($con);die;
		}

		$data_array_fin_prod="";
		if(str_replace("'",'',$cbo_service_type) == 15 || str_replace("'",'',$cbo_service_type) == 50 || str_replace("'",'',$cbo_service_type) == 51){
			if(str_replace("'", "", trim($txt_fin_color))!="")
			{
				if (!in_array(str_replace("'", "", trim($txt_fin_color)),$new_array_color)){
					$color_id = return_id( str_replace("'", "", trim($txt_fin_color)), $color_library, "lib_color", "id,color_name","335");  
					$new_array_color[$color_id]=str_replace("'", "", trim($txt_fin_color));
				}
				else $color_id =  array_search(str_replace("'", "", trim($txt_fin_color)), $new_array_color);
			}
			else $color_id =0;

			$field_array_fin_prod="dtls_id*yarn_count*yarn_comp*yarn_perc*yarn_type*yarn_color*job_no*yarn_rate";
			$data_array_fin_prod="'".trim($dtls_ids,", ")."'*".$cbo_fin_count."*".$cbo_fin_composition."*".$txt_fin_perc."*".$cbo_fin_type."*".$color_id."*'".$job_no."'*".$dyeing_charge;
		}

		$rID=sql_update("wo_yarn_dyeing_mst",$field_array,$data_array,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		if($data_array_dts!="")
		{
			//echo "10**"."insert into wo_yarn_dyeing_dtls (".$field_array_dts.") values ".$data_array_dts;die;
			$dtlsrID=sql_insert("wo_yarn_dyeing_dtls",$field_array_dts,$data_array_dts,1);
			//echo "10**";
			//echo "delete from wo_yarn_dyeing_dtls where dtls_id in(".str_replace("'","",$update_dtls_ids).")";die;
			$delete_previous_dtls_rows = execute_query("delete from wo_yarn_dyeing_dtls where id in(".str_replace("'","",$update_dtls_ids).")", 0);
		}
		if($dtlsrID) $flag=1; else $flag=0;
		if($delete_previous_dtls_rows) $flag=1; else $flag=0;

		if($data_array_fin_prod!="")
		{
			$finProdrID=sql_update("wo_yarn_dyeing_dtls_fin_prod",$field_array_fin_prod,$data_array_fin_prod,"id",$hdn_fin_update_id,1);
		}else{
			$finProdrID=1;
		}
		if($finProdrID) $flag=1; else $flag=0;



		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_booking_no)."**".str_replace("'","",$update_id);
			}
		}
		disconnect($con);
		die;
	}

	else if ($operation==2) // Delete Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$update_id=str_replace("'","",$update_id);
		$dtls_update_id=str_replace("'","",$dtls_update_id);
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		// master table delete here---------------------------------------
		if($update_id=="" || $update_id==0){ echo "15**0";disconnect($con); die;}

		$product_ids="";
		for($j=0; $j<$tot_row; $j++)
		{
			$dtls_update_id 	= "dtls_update_id_".$j;
			$txt_pro_id 		= "txt_pro_id_".$j;

			$dtlsrID = sql_update("wo_yarn_dyeing_dtls",'status_active*is_deleted','0*1',"id",$$dtls_update_id,1);

			$product_ids .= $$txt_pro_id.",";
		}

		$product_ids = rtrim($product_ids,", ");
		$total_issue_qty = return_field_value("sum(c.cons_quantity) as cons_quantity", "wo_yarn_dyeing_mst a,inv_issue_master b, inv_transaction c"," b.booking_id=a.id and b.id=c.mst_id and a.ydw_no='$txt_booking_no' and b.booking_no='$txt_booking_no' and c.prod_id in($product_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.item_category=1 and c.item_category=1 and c.transaction_type=2","cons_quantity");
		if($total_issue_qty!="" || $total_issue_qty>0)
		{
			echo "22**Issue Found. Yarn Service Work Order can not be deleted.";disconnect($con);die;
		}

		if((str_replace("'",'',$cbo_service_type) == 15 || str_replace("'",'',$cbo_service_type) == 50 || str_replace("'",'',$cbo_service_type) == 51) && str_replace("'",'',$hdn_fin_update_id)!=""){
			$finProductID = sql_update("wo_yarn_dyeing_dtls_fin_prod",'status_active*is_deleted','0*1',"id",$hdn_fin_update_id,1);
		}else{
			$finProductID=1;
		}
		if($db_type==0 )
		{
			if($dtlsrID && $finProductID)
			{
				mysql_query("COMMIT");
				echo "2**".$txt_booking_no."**".$update_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($dtlsrID && $finProductID)
			{
				oci_commit($con);
				echo "2**".$txt_booking_no."**".$update_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}


}


if($action=="show_dtls_list_view")
{
	$data = explode("_",$data);
	?>
	<table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all">
		<thead>
			<tr>
				<th width="40">SL</th>
				<th width="70">Job No</th>
				<th width="60">Count</th>
				<th width="200">Description</th>
				<th width="60">UOM</th>
				<th width="80">WO QTY</th>
				<th width="80">Rate</th>
				<th width="100">Amount</th>
				<th width="80">No of Bag</th>
				<th width="80">No of Cone</th>
				<th width="100">Minimum Require Cone</th>
				<th >Remarks</th>
				<th width="100">Finish Product</th>
			</tr>
		</thead>
		<tbody>
			<?
			$sql_yarn_production=sql_select("select mst_id,dtls_id from wo_yarn_dyeing_dtls_fin_prod where mst_id=".$data[0]." and status_active=1 and is_deleted=0");

			if($data[1]==15 || $data[1]==50 || $data[1]==51){
				$composition_array=return_library_array( "select id, composition_name from  lib_composition_array where  status_active=1", "id", "composition_name"  );
				$sql = sql_select("select a.service_type, b.id, b.job_no,b.job_no_id,b.dyeing_charge,b.count,
					b.yarn_description,
					b.color_range,
					b.yarn_wo_qty,b.amount,
					b.no_of_bag,
					b.no_of_cone,
					b.min_require_cone,
					b.remarks
					from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b
					where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.id=".$data[0]."");

			}else{
				$sql = sql_select("select id, job_no,job_no_id,count,yarn_description,yarn_color,color_range,uom,yarn_wo_qty,dyeing_charge,amount,no_of_bag, no_of_cone,min_require_cone,referance_no,remarks from wo_yarn_dyeing_dtls where status_active=1 and is_deleted=0 and mst_id='".$data[0]."'");
			}
			foreach ($sql as $row) {
				$dtls_data_arr[$row[csf("id")]]["id"]				=$row[csf("id")];
				$dtls_data_arr[$row[csf("id")]]["id"]				=$row[csf("id")];
				$dtls_data_arr[$row[csf("id")]]["service_type"]		=$row[csf("service_type")];
				$dtls_data_arr[$row[csf("id")]]["job_no"]			=$row[csf("job_no")];
				$dtls_data_arr[$row[csf("id")]]["job_no_id"]		=$row[csf("job_no_id")];
				$dtls_data_arr[$row[csf("id")]]["dyeing_charge"]	=$row[csf("dyeing_charge")];
				$dtls_data_arr[$row[csf("id")]]["count"]			=$row[csf("count")];
				$dtls_data_arr[$row[csf("id")]]["yarn_description"]	=$row[csf("yarn_description")];
				$dtls_data_arr[$row[csf("id")]]["color_range"]		=$row[csf("color_range")];
				$dtls_data_arr[$row[csf("id")]]["yarn_wo_qty"]		+=$row[csf("yarn_wo_qty")];
				$dtls_data_arr[$row[csf("id")]]["amount"]			+=$row[csf("amount")];
				$dtls_data_arr[$row[csf("id")]]["no_of_bag"]		=$row[csf("no_of_bag")];
				$dtls_data_arr[$row[csf("id")]]["no_of_cone"]		=$row[csf("no_of_cone")];
				$dtls_data_arr[$row[csf("id")]]["min_require_cone"]	=$row[csf("min_require_cone")];
				$dtls_data_arr[$row[csf("id")]]["remarks"]			=$row[csf("remarks")];
			}

			$i=1;
			foreach($sql_yarn_production as $row)
			{

				$dtls_ids=explode(",",$row[csf("dtls_id")]);
				$dtlsIDs=$job_no=$remarks=$count_names=$desc=$yarn_dtlsids=$service_type="";
				$amount=$yarn_wo_qty=$no_of_bag=$no_of_cone=$min_require_cone=0;
				foreach ($dtls_ids as $dtlsIds) {
					$dtlsIDs.=$dtlsIds.",";
					$job_no.=$dtls_data_arr[$dtlsIds]["job_no"].",";

					$yarn_wo_qty+=$dtls_data_arr[$dtlsIds]["yarn_wo_qty"];
					$dyeing_charge=$dtls_data_arr[$dtlsIds]["dyeing_charge"];
					$amount+=$dtls_data_arr[$dtlsIds]["amount"];

					$no_of_bag+=$dtls_data_arr[$dtlsIds]["no_of_bag"];
					$no_of_cone+=$dtls_data_arr[$dtlsIds]["no_of_cone"];
					$min_require_cone+=$dtls_data_arr[$dtlsIds]["min_require_cone"];
					$remarks.=$dtls_data_arr[$dtlsIds]["remarks"];
					$service_type=$dtls_data_arr[$dtlsIds]["service_type"];


					$counts=explode("*",$dtls_data_arr[$dtlsIds]["count"]);
					foreach ($counts as $count) {
						$count_names.=$count_arr[$count].",";
					}
					$yarn_description = explode("*",$dtls_data_arr[$dtlsIds]["yarn_description"]);
					foreach ($yarn_description as $description) {
						$desc.=$description."<br />";
					}
					$yarn_dtls_ids=explode("_",$row[csf("id")]);
					foreach ($yarn_dtls_ids as $dtls_id) {
						$yarn_dtlsids.=$dtls_id.",";
					}
				}
				//echo rtrim($job_no,',');; die;
				$job_no=chop($job_no,",");
				$remarks=chop($remarks,",");
				$dtlsIDs=chop($dtlsIDs,",");
				//print_r($dtls_ids);

				$sql_fin_prod=sql_select("select id,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color from wo_yarn_dyeing_dtls_fin_prod where dtls_id in('".rtrim($yarn_dtlsids,", ")."')");
				$fin_prod = $count_arr[$sql_fin_prod[0][csf("yarn_count")]]." " . $composition_array[$sql_fin_prod[0][csf("yarn_comp")]]." " . $sql_fin_prod[0][csf("yarn_perc")]."% " . $yarn_type[$count_arr[$sql_fin_prod[0][csf("yarn_type")]]]." " . $color_arr[$sql_fin_prod[0][csf("yarn_color")]];
				$is_twisting = ($service_type==15 || $service_type==50 || $service_type==51)? "create_row('".$dtlsIDs."');":"";

				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor;?>" onClick="<? echo $is_twisting; ?>get_php_form_data('<? echo $dtlsIDs; ?>', 'child_form_input_data', 'requires/yarn_service_work_order_controller')" style="cursor:pointer;">
					<td align="center"><p><? echo $i; ?></p></td>
					<td align="center"><p><? echo $job_no; ?></p></td>
					<td><p><? echo rtrim($count_names,", "); ?></p></td>
					<td><p><? echo $desc; ?></p></td>
					<td align="center"><p><? echo $unit_of_measurement[15]; ?></p></td>
					<td align="right"><p><? echo number_format($yarn_wo_qty,2); ?></p></td>
					<td align="right"><p><? echo number_format($dyeing_charge,2); ?></p></td>
					<td align="right"><p><? echo number_format($amount,2); ?></p></td>
					<td align="center"><p><? echo $no_of_bag; ?></p></td>
					<td align="center"><p><? echo $no_of_cone; ?></p></td>
					<td align="center"><p><? echo $min_require_cone; ?></p></td>
					<td><p><? echo $remarks; ?></p></td>
					<td align="center"><p><? echo ($data[1]==15 || $data[1]==50 || $data[1]==51)?$fin_prod:"" ; ?></p></td>
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

if($action=="populate_master_from_data")
{
	$sql="select  a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.is_sales,booking_without_order,ref_no from wo_yarn_dyeing_mst a where  a.ydw_no='".$data."'";
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "load_drop_down( 'requires/yarn_service_work_order_controller', '".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td');\n";
		echo "$('#txt_booking_no').val('".$row[csf("ydw_no")]."');\n";
		echo "$('#cbo_company_name').val('".$row[csf("company_id")]."');\n";
		echo "$('#cbo_service_type').val('".$row[csf("service_type")]."');\n";
		echo "$('#cbo_supplier_name').val('".$row[csf("supplier_id")]."');\n";
		echo "$('#txt_booking_date').val('".change_date_format($row[csf("booking_date")])."');\n";
		echo "$('#txt_delivery_date').val('".change_date_format($row[csf("delivery_date")])."');\n";
		echo "$('#cbo_currency').val('".$row[csf("currency")]."');\n";
		echo "$('#txt_exchange_rate').val('".$row[csf("ecchange_rate")]."');\n";
		echo "$('#cbo_pay_mode').val('".$row[csf("pay_mode")]."');\n";
		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#cbo_source').val('".$row[csf("source")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_is_sales_order').val('".$row[csf("is_sales")]."');\n";
		echo "$('#txt_ref_no').val('".$row[csf("ref_no")]."');\n";
		echo "$('#cbo_is_sales_order').attr('disabled','disabled');\n";
		echo "$('#cbo_with_order').attr('disabled','disabled');\n";
		echo "$('#cbo_with_order').val('".$row[csf("booking_without_order")]."');\n";
		echo "reset_form('yarn_service_work_order','','','','','txt_booking_no*cbo_company_name*cbo_service_type*cbo_pay_mode*txt_booking_date*txt_attention*cbo_currency*txt_exchange_rate*cbo_supplier_name*cbo_source*txt_delivery_date*cbo_is_sales_order*cbo_with_order*txt_ref_no*update_id');\n";
	}
	exit();
}



if ($action=="yern_service_wo_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	if($db_type==0) $select_field_grp="group by a.id order by supplier_name";
	else if($db_type==2) $select_field_grp="group by a.id,a.supplier_name order by supplier_name";
	?>
	<script>
		function js_set_value(id)
		{
			$("#hidden_sys_number").val(id);
			parent.emailwindow.hide();
		}
		var permission= '<? echo $permission; ?>';
	</script>
</head>
<body>
	<div align="center" style="width:830px;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="6">
							<?
							echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" );
							?>
						</th>
					</tr>
					<tr>
						<th width="170"> Service Type</th>
						<th width="170">Supplier Name</th>
						<th width="100">WO No</th>
						<th width="150" colspan="2">Booking  Date</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<? echo create_drop_down( "cbo_service_type", 160, $yarn_issue_purpose,"", 1, "-- Select --", $selected, "",0,'12,15,38,46,50,51');?>
						</td>
						<td>
							<?

							echo create_drop_down( "cbo_supplier_name", 160, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company=$company and a.status_active =1 and b.party_type in(2) group by a.id,a.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "",0 );
							?>
						</td>
						<td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
						<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" /></td>
						<td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" /></td>
						<td>
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_service_type').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value, 'create_sys_search_list_view', 'search_div', 'yarn_service_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" valign="middle" colspan="6">
							<? echo load_month_buttons(1);  ?>
							<input type="hidden" id="hidden_sys_number" value="hidden_sys_number" />
							<input type="hidden" id="hidden_id" value="hidden_id" />
							<!--END-->
						</td>
					</tr>
				</tbody>
			</table>
			<div id="search_div"></div>
		</form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_sys_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$service_type=$ex_data[4];
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if($db_type==0)
	{
		$booking_year_cond=" and year(a.insert_date)=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
	}
	if($db_type==2)
	{
		$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[5]";
		if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";
	}
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $service_type!=0 )  $service_type_cond="and a.service_type='$service_type'"; else  $service_type_cond="";


	if($ex_data[7]==4 || $ex_data[7]==0)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($ex_data[7]==1)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num ='$ex_data[6]' "; else $booking_cond="";
	}
	if($ex_data[7]==2)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '$ex_data[6]%'  $booking_year_cond  "; else $booking_cond="";
	}
	if($ex_data[7]==3)
	{
		if (str_replace("'","",$ex_data[6])!="") $booking_cond=" and a.yarn_dyeing_prefix_num like '%$ex_data[6]'  $booking_year_cond  "; else $booking_cond="";
	}


	if($db_type==0)
	{
		$sql = "select
		a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,year(a.insert_date) as year
		from
		wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		where
		a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=335 and b.entry_form=335 $company $supplier  $sql_cond  $service_type_cond  $booking_cond
		group by a.id order by a.id DESC";
	}

	else if($db_type==2)
	{
		$sql = "select
		a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,TO_CHAR(a.insert_date,'YYYY') as year
		from
		wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b left join   sample_development_mst d on  b.job_no_id=d.id
		where
		a.id=b.mst_id  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.entry_form=335 and b.entry_form=335 $company $supplier  $sql_cond  $service_type_cond  $booking_cond
		group by
		a.id, a.yarn_dyeing_prefix_num, a.ydw_no, a.company_id,a.service_type, a.supplier_id, a.booking_date, a.delivery_date, a.currency, a.ecchange_rate, a.pay_mode,a.source, a.attention,a.insert_date order by a.id DESC";
	}
	//echo $sql;

	?>	<div style="width:860px; "  align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="50">Wo No</th>
				<th width="40">Year</th>
				<th width="100"> Service Type</th>
				<th width="100">Currency</th>
				<th width="50">Exchange Rate</th>
				<th width="100">Pay Mode</th>
				<th width="170">Supplier Name</th>
				<th width="70">Booking Date</th>
				<th>Delevary Date</th>
			</thead>
		</table>
		<div style="width:860px; margin-left:3px; overflow-y:scroll; max-height:300px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_list_search" >
				<?

				$i=1;
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>+'_'+'<? echo $selectResult[csf('ydw_no')]; ?>'+'_'+<? echo $selectResult[csf('service_type')]; ?>); ">

						<td width="30" align="center"> <p><? echo $i; ?></p></td>
						<td width="50" align="center"><p> <? echo $selectResult[csf('yarn_dyeing_prefix_num')]; ?></p></td>
						<td width="40" align="center"><p> <? echo $selectResult[csf('year')]; ?></p></td>
						<td width="100"><p><? echo $yarn_issue_purpose[$selectResult[csf('service_type')]]; ?></p></td>
						<td width="100"><? echo $currency[$selectResult[csf('currency')]]; ?></td>
						<td width="50"><? echo $selectResult[csf('ecchange_rate')]; ?></td>
						<td width="100"><p> <? echo $pay_mode[$selectResult[csf('pay_mode')]]; ?></p></td>
						<td width="170"> <p><? if($selectResult[csf('pay_mode')]==3 || $selectResult[csf('pay_mode')]==5){echo $company_library[$selectResult[csf('supplier_id')]];}else{echo $supplier_arr[$selectResult[csf('supplier_id')]];} ?></p></td>
						<td width="70"><p><? echo change_date_format($selectResult[csf('booking_date')]); ?></p></td>
						<td><p><? echo change_date_format($selectResult[csf('delivery_date')]); ?></p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
	</div>
	<?
	exit();
}


if($action=="child_form_input_row")
{
	$data = implode(",",array_unique(explode("_",$data)));
	$sql = "select a.id,a.job_no,a.product_id,b.lot,job_no_id,a.count,a.yarn_type,a.yarn_description,a.color_range,a.uom,a.yarn_wo_qty,a.dyeing_charge,a.amount,a.no_of_bag, a.no_of_cone,a.min_require_cone,a.remarks,a.referance_no,a.sample_name,a.color from wo_yarn_dyeing_dtls a,product_details_master b where a.product_id=b.id and a.id in($data)";
	//echo $sql;die;
	$sql_re=sql_select($sql);
	$i=0;$data="";
	foreach($sql_re as $row)
	{
		$data .= $row[csf("yarn_description")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $row[csf("count")]."*". $row[csf("lot")]."*".$row[csf("id")]."*".$row[csf("color")]."#";
	}
	echo rtrim($data,"# ");

	exit();
}

if($action=="child_form_input_data")
{
	$data = implode(",",array_unique(explode("_",$data)));
	$sql_fin_prod=sql_select("select id,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color from wo_yarn_dyeing_dtls_fin_prod where dtls_id in('$data')");

	$sql = "select b.booking_without_order, a.id,a.job_no,a.product_id,a.job_no_id,a.count,a.yarn_description,a.color_range,a.uom,a.yarn_wo_qty,a.dyeing_charge,a.amount,a.no_of_bag, a.no_of_cone,a.min_require_cone,a.remarks,a.referance_no,a.sample_name,c.allocated_qnty,c.available_qnty,a.color from wo_yarn_dyeing_dtls a,wo_yarn_dyeing_mst b,product_details_master c where a.mst_id=b.id and a.product_id=c.id and a.id in($data)";
	//echo $sql;die;
	$sql_re=sql_select($sql);
	$i=0;
	foreach($sql_re as $row)
	{
		$product_id = $row[csf('product_id')];
		/*if($row[csf('booking_without_order')]==1){
			$available_qnty = $row[csf('allocated_qnty')];
		}else{*/
			$available_qnty = $row[csf('available_qnty')];
		//}


		echo "$('#txt_wo_qty_$i').attr('placeholder', '".$available_qnty."');\n";

		if($row[csf('booking_without_order')]==1){
			echo "$('#txt_job_id_$i').val('".$row[csf("job_no_id")]."');\n";
			echo "$('#txt_job_no_$i').val('".$row[csf("job_no")]."');\n";
		}else{
			echo "$('#txt_job_id_$i').val('".$row[csf("job_no_id")]."');\n";
			echo "$('#txt_job_no_$i').val('".$row[csf("job_no")]."');\n";
		}

		echo "$('#txt_pro_id_$i').val(".$row[csf("product_id")].");\n";
		$lot=return_field_value("lot"," product_details_master","id=".$row[csf("product_id")]."","lot");
		if($row[csf("product_id")]>0)
		{
			echo "$('#txt_lot_$i').val('$lot');\n";
			echo "$('#cbo_count_$i').val(".$row[csf("count")].").attr('disabled',true);\n";
			echo "$('#txt_item_des_$i').val('".$row[csf("yarn_description")]."').attr('disabled',true);\n";
		}
		else
		{
			echo "$('#txt_lot_$i').val('$lot');\n";
			echo "$('#cbo_count_$i').val(".$row[csf("count")].");\n";
			echo "$('#txt_item_des_$i').val('".$row[csf("yarn_description")]."');\n";
		}
		$job_ref=$row[csf("job_no")];

		echo "$('#cbo_uom_$i').val(".$row[csf("uom")].");\n";
		echo "$('#cbo_color_$i').val(".$row[csf("color")].");\n";
		echo "$('#txt_wo_qty_$i').val(".$row[csf("yarn_wo_qty")].");\n";
		echo "$('#txt_rate_$i').val(".$row[csf("dyeing_charge")].");\n";
		echo "$('#txt_amount_$i').val(".$row[csf("amount")].");\n";
		echo "$('#txt_bag_$i').val(".$row[csf("no_of_bag")].");\n";
		echo "$('#txt_cone_$i').val(".$row[csf("no_of_cone")].");\n";
		echo "$('#txt_min_req_cone_$i').val(".$row[csf("min_require_cone")].");\n";
		echo "$('#txt_remarks_$i').val('".$row[csf("remarks")]."');\n";
		echo "$('#txt_ref_no_$i').val('".$row[csf("referance_no")]."');\n";
		//update id here
		echo "$('#dtls_update_id_$i').val(".$row[csf("id")].");\n";
		echo "set_button_status(1, permission, 'fnc_yarn_service_wo',1,0);\n";
		$i++;
	}
	echo "$('#update_dtls_ids').val('".$data."');\n";
	if(!empty($sql_fin_prod)){

		echo "$('#is_twisting').css('display','block');\n";
		echo "$('#cbo_fin_count').val('".$sql_fin_prod[0][csf("yarn_count")]."');\n";
		echo "$('#cbo_fin_composition').val('".$sql_fin_prod[0][csf("yarn_comp")]."');\n";
		echo "$('#txt_fin_perc').val('".$sql_fin_prod[0][csf("yarn_perc")]."');\n";
		echo "$('#cbo_fin_type').val('".$sql_fin_prod[0][csf("yarn_type")]."');\n";
		echo "$('#txt_fin_color').val('".$color_arr[$sql_fin_prod[0][csf("yarn_color")]]."');\n";
		echo "$('#hdn_fin_update_id').val('".$sql_fin_prod[0][csf("id")]."');\n";
	}
	exit();
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Order Search","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		var permission='<? echo $permission; ?>';
		function add_break_down_tr(i)
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				i++;

				$("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
					$(this).attr({
						'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						'name': function(_, name) { return name + i },
						'value': function(_, value) { return value }
					});
				}).end().appendTo("#tbl_termcondi_details");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
				$('#termscondition_'+i).val("");
				$( "#tbl_termcondi_details tr:last" ).find( "td:first" ).html(i);
			}

		}

		function fn_deletebreak_down_tr(rowNo)
		{

			var numRow = $('table#tbl_termcondi_details tbody tr').length;
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_termcondi_details tbody tr:last').remove();
			}

		}

		function fnc_fabric_booking_terms_condition( operation )
		{
			var row_num=$('#tbl_termcondi_details tr').length-1;
			var data_all="";
			for (var i=1; i<=row_num; i++)
			{

				if (form_validation('termscondition_'+i,'Term Condition')==false)
				{
					return;
				}

				data_all=data_all+get_submitted_data_string('txt_booking_no*termscondition_'+i,"../../../");
			//alert(data_all);
		}
		var data="action=save_update_delete_fabric_booking_terms_condition&operation="+operation+'&total_row='+row_num+data_all;
	//	alert(data);
		//freeze_window(operation);
		http.open("POST","yarn_service_work_order_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_booking_terms_condition_reponse;
	}
	function fnc_fabric_booking_terms_condition_reponse()
	{
		if(http.readyState == 4)
		{
	   // alert(http.responseText);
	   var reponse=trim(http.responseText).split('**');
	   if (reponse[0].length>2) reponse[0]=10;
	   if(reponse[0]==0 || reponse[0]==1)
	   {
				//$('#txt_terms_condision_book_con').val(reponse[1]);
				parent.emailwindow.hide();
				set_button_status(1, permission, 'fnc_fabric_booking_terms_condition',1,1);
			}
		}
	}
</script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<fieldset>
			<form id="termscondi_1" autocomplete="off">
				<input type="text" id="txt_booking_no" name="txt_booking_no" value="<? echo str_replace("'","",$txt_booking_no) ?>" class="text_boxes" readonly />
				<input type="hidden" id="txt_terms_condision_book_con" name="txt_terms_condision_book_con" >

				<table width="650" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
					<thead>
						<tr>
							<th width="50">Sl</th><th width="530">Terms</th><th ></th>
						</tr>
					</thead>
					<tbody>
						<?
					//echo "select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no";
					$data_array=sql_select("select id, terms from  wo_booking_terms_condition where booking_no=$txt_booking_no");// quotation_id='$data'
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
							<tr id="settr_1" align="center">
								<td>
									<? echo $i;?>
								</td>
								<td>
									<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
								</td>
								<td>
									<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
									<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
								</td>
							</tr>
							<?
						}
					}
					else
					{

					$data_array=sql_select("select id, terms from  lib_yarn_dyeing_terms_con where is_default=1");// quotation_id='$data'
					foreach( $data_array as $row )
					{
						$i++;
						?>
						<tr id="settr_1" align="center">
							<td>
								<? echo $i;?>
							</td>
							<td>
								<input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:95%"  class="text_boxes"  value="<? echo $row[csf('terms')]; ?>" />
							</td>
							<td>
								<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />
								<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?> );" />
							</td>
						</tr>
						<?
					}
				}
				?>
			</tbody>
		</table>

		<table width="650" cellspacing="0" class="" border="0">
			<tr>
				<td align="center" height="15" width="100%"> </td>
			</tr>
			<tr>
				<td align="center" width="100%" class="button_container">
					<?
					echo load_submit_buttons( $permission, "fnc_fabric_booking_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ;
					?>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

exit();
}

if($action=="save_update_delete_fabric_booking_terms_condition")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$id=return_next_id( "id", "wo_booking_terms_condition", 1 ) ;
		$field_array="id,booking_no,terms";
		for ($i=1;$i<=$total_row;$i++)
		{
			$termscondition="termscondition_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_booking_no.",".$$termscondition.")";
			$id=$id+1;
		}
		$rID_de3=execute_query( "delete from wo_booking_terms_condition where  booking_no =".$txt_booking_no."",0);

		$rID=sql_insert("wo_booking_terms_condition",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$txt_booking_no;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$txt_booking_no;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID_de3)
			{
				oci_commit($con);
				echo "0**".$txt_booking_no;
			}

		}
		else{
			oci_rollback($con);
			echo "10**".$txt_booking_no;
		}
		disconnect($con);
		die;
	}
}


if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$currency_rate=set_conversion_rate( $data[0], $conversion_date );
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="show_trim_booking_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$cbo_service_type=str_replace("'","",$cbo_service_type);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$pay_mode = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);

	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

	$yern_count=return_library_array("select id,yarn_count from  lib_yarn_count", "id", "yarn_count");
	$fin_composition=return_library_array("select id,composition_name from  lib_composition_array", "id", "composition_name");


	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<style type="text/css">
		table.mainTable tr td {
			border: 1px solid #000;
		}

	</style>
	<div style="width:950px; margin-left: 5px">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:23px;">
								<?php
echo "<b>$company_library[$cbo_company_name]</b>";
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:16px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:21px">
								<strong style="font-size: 18px"><? echo "Yarn ". $yarn_issue_purpose[$cbo_service_type];?> Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,a.ref_no,b.job_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
			$job_no=$result[csf('job_no')];
			$ref_no=$result[csf('ref_no')];
		}
		$internal_ref_arr=array();
		if ($job_no!="") { $internal_ref_arr=return_library_array( "select job_no_mst,grouping from  wo_po_break_down where job_no_mst='$job_no'",'job_no_mst','grouping');}
		$varcode_work_order_no=$work_order;
		?>
		<table width="970" style="" align="center">
			<tr>
				<td width="360"  style="font-size:16px">
					<table width="360" style="" align="left">
						<tr  >
							<td   width="120" style="font-size:16px">To</td>
							<td width="230" style="font-size:16px">:&nbsp;&nbsp;

								<?

								if($pay_mode==3 || $pay_mode==5)
								{
									echo $company_library[$new_supplier_name];
								}
								else
								{
									echo $supplier_name[$new_supplier_name];
								}

								?></td>
							</tr>

							<tr>
								<td style="font-size:16px">Wo No.</td>
								<td style="font-size:16px">:&nbsp;&nbsp;<? echo $work_order;?></td>
							</tr>
							<tr>
								<td style="font-size:16px">Internal Ref. No</td>
								<td style="font-size:16px">:&nbsp;&nbsp;<? echo $internal_ref_arr[$job_no]; ?></td>
							</tr>
							<tr>
								<td style="font-size:16px">Attention</td>
								<td style="font-size:16px">:&nbsp;&nbsp;<? echo $attention; ?></td>
							</tr>
						</table>
					</td>
					<td width="365" style="font-size:16px">
						<table width="365" style="" align="left">
							<tr>
								<td width="120" style="font-size:16px">Job No.</td>
								<td width="120" style="font-size:16px">:&nbsp;&nbsp;<? echo $job_no;?></td>
								<td width="140" style="font-size:16px">Ref. No</td>
								<td width="120" colspan="2" style="font-size:16px">:&nbsp;<? echo $ref_no;?></td>
							</tr>

							<tr>
								<td style="font-size:16px">Currency</td>
								<td style="font-size:16px">:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
							</tr>
							<tr>
								<td style="font-size:16px">Booking Date</td>
								<td style="font-size:16px">:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
							</tr>
							<tr>
								<td  width="120" style="font-size:16px">Delivery Date</td>
								<td width="230" style="font-size:16px">:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
							</tr>

						</table>
					</td>


				</tr>
			</table>
		</br>

		<?
			$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
			$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
			$sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
			foreach($sql_result as $row)
			{
				if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

				if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

				if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

				if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

			}
		//var_dump($total_dtls_id);
		//die;

			?>




			<table class="mainTable" width="1070" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
				<tr>
					<td width="30" align="center"><strong>Sl</strong></td>
					<td width="60" align="center"><strong>Lot</strong></td>
					<td width="30" align="center"><strong>Yarn Count</strong></td>
					<td width="160" align="center"><strong>Yarn Description</strong></td>
					<td width="60" align="center"><strong>Brand</strong></td>

					<td width="60" align="center"><strong>WO Qty</strong></td>
					<td width="50" align="center"><strong>Rate</strong></td>
					<td width="80" align="center"><strong>Amount</strong></td>
					<td width="80" align="center"><strong>No of Bag</strong></td>
					<td width="80" align="center"><strong>No of Cone</strong></td>
					<td align="center" width="60" ><strong>Min Req. Cone</strong></td>
					<td align="center" width="100" ><strong>Remarks</strong></td>
					<td align="center" ><strong>Swatch</strong></td>
				</tr>
				<?

				$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
				foreach($sql_brand as $row_barand)
				{
					$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
					$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
				}

				if($db_type==0) $select_f_grp="group by mst_id,yarn_color, color_range,
				id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";
				else if($db_type==2) $select_f_grp="group by mst_id,yarn_color, color_range,
				id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";

				$sql_color="select id,mst_id,product_id,job_no,no_of_bag,no_of_cone,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
				from
				wo_yarn_dyeing_dtls
				where
				status_active=1 and id in($total_dtls_id) $select_f_grp ";
			//echo $sql_color;die;
				$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
				$mstID="";
				foreach($sql_result as $row)
				{
					$mstID=$row[csf("mst_id")];
					$product_id=$row[csf("product_id")];
				//var_dump($product_id);
					if($row[csf("product_id")]!="")
					{
						$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
						$brand=$product_lot[$row[csf("product_id")]]['brand'];
					}

			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";


					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"  height="90"><? echo $i; ?></td>
						<td align="center"><? echo $lot_amt; ?></td>
						<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
						<td>
							<?
							echo $row[csf("yarn_description")];
							?>
						</td>
						<td><? echo $brand_arr[$brand]; ?></td>

						<td align="center"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
						<td align="center"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
						<td align="center"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
						<td align="center"><? echo $row[csf("no_of_bag")]; ?></td>
						<td align="center"><? echo $row[csf("no_of_cone")]; ?></td>
						<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
						<td align="center"><? echo $row[csf("remarks")]; ?></td>
						<td align="center">&nbsp;</td>
					</tr>
					<?
					$i++;
					$yarn_count_des="";
					$style_no="";
				}
				?>
				<tr>
					<td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
					<td align="center" ><b><? echo $total_qty; ?></b></td>
					<td align="center">&nbsp;</td>
					<td align="center"><b><? echo number_format($total_amount,2); ?></b></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<?
				$mcurrency="";
				$dcurrency="";
				if($currency_id==1)
				{
					$mcurrency='Taka';
					$dcurrency='Paisa';
				}
				if($currency_id==2)
				{
					$mcurrency='USD';
					$dcurrency='CENTS';
				}
				if($currency_id==3)
				{
					$mcurrency='EURO';
					$dcurrency='CENTS';
				}
				?>
				<tr>
					<td colspan="5">&nbsp;&nbsp;</td>
					<td colspan="8" align="left" style="font-size:16px">Total Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td>
				</tr>
			</table>
			<br><br>
			<table class="mainTable" width="600" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
				<caption><b> Finish  Product Details</b> </caption>
				<?
				$sql_finish_product_dtls = "select id,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color from wo_yarn_dyeing_dtls_fin_prod where mst_id = $update_id";
				$dtls_result=sql_select($sql_finish_product_dtls);
				?>
				<tr>
					<td width="30" align="center"><strong>Sl</strong></td>
					<td width="180" align="center"><strong>Yarn Composition</strong></td>
					<td width="100" align="center"><strong>Count</strong></td>
					<td width="100" align="center"><strong>Type</strong></td>
					<td width="100" align="center"><strong>Color</strong></td>
					<td width="80" align="center"><strong>Qnty</strong></td>
				</tr>
				<?
				$total_color_qty=0; $d=1;
				foreach($dtls_result as $row)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $d; ?></td>
						<td align="center"><? echo $fin_composition[$row[csf("yarn_comp")]];?></td>
						<td align="center"><? echo $yern_count[$row[csf("yarn_count")]];?></td>
						<td align="center"><? echo $yarn_type[$row[csf("yarn_type")]];?></td>
						<td><? echo $color_arr[$row[csf("yarn_color")]];?></td>
						<td align="right"><? echo $total_qty; ?></td>

					</tr>
					<?
					$d++;
				}
				?>
				<tr>
					<td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
					<td align="right" ><b><? echo $total_qty; ?></b></td>
				</tr>

			</table>
			<br>
			<br>


			<!--==============================================AS PER GMTS COLOR START=========================================  -->
			<!-- <table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table> -->

			<? echo get_spacial_instruction($work_order,"100%",335);?>

		</div>
		<div>
			<?
			echo signature_table(122, $cbo_company_name, "970px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
			?>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
		</script>
		<?
		exit();
	}

	if($action=="show_with_multiple_job_without_rate")
	{



	//echo "uuuu";die;
		extract($_REQUEST);
		$form_name=str_replace("'","",$form_name);
		$cbo_company_name=str_replace("'","",$cbo_company_name);
		$update_id=str_replace("'","",$update_id);
		$txt_booking_no=str_replace("'","",$txt_booking_no);
		$pay_mode = str_replace("'","",$cbo_pay_mode);
		$new_supplier_name = str_replace("'","",$cbo_supplier_name);

		$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
		$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
		$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
		$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

	//echo $new_supplier_name;
	//echo $pay_mode;
	//echo $company_library[$new_supplier_name];
	//die;
	//select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name

	// $company_library=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id", "company_name"  );
	// $supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

	// $buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	// $brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
		?>
		<div style="width:950px" align="center">
			<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
				<tr>
					<td width="750">
						<table width="100%" cellpadding="0" cellspacing="0" >
							<tr>
								<td align="center" style="font-size:20px;">
									<?php
echo $company_library[$cbo_company_name];
?>
								</td>
							</tr>
							<tr>
								<td align="center" style="font-size:14px">
									<?
									$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
									foreach ($nameArray as $result)
									{
										?>
										Email Address: <? echo $result[csf('email')];?>
										Website No: <? echo $result[csf('website')];
									}
									?>
								</td>
							</tr>
							<tr>
								<td align="center" style="font-size:20px">
									<strong>Yarn Dyeing Work Order </strong>
								</td>
							</tr>
						</table>
					</td>
					<td width="250" id="barcode_img_id">
					</td>
				</tr>
			</table>
			<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
			$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
			foreach ($nameArray as $result)
			{
				$work_order=$result[csf('ydw_no')];
				$supplier_id=$result[csf('supplier_id')];
				$booking_date=$result[csf('booking_date')];
				$currency=$result[csf('currency')];
				$attention=$result[csf('attention')];
				$delivery_date=$result[csf('delivery_date')];
				$delivery_date_end=$result[csf('delivery_date_end')];
				$dy_delivery_start=$result[csf('dy_delivery_date_start')];
				$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			}
			$varcode_work_order_no=$work_order;

			?>
			<table width="950" style="" align="center">
				<tr>
					<td width="350"  style="font-size:12px">
						<table width="350" style="" align="left">
							<tr  >
								<td   width="120"><b>To</b>   </td>
								<td width="230">:&nbsp;&nbsp;
									<?

									if($pay_mode==3 || $pay_mode==5)
									{
										echo $company_library[1];
					            		//echo $company_library[$new_supplier_name];
									}
									else
									{
										echo $supplier_name[$new_supplier_name];
									}
                                	 // if($pay_mode==3 || $pay_mode==5)
                                	 // {
                                	 // 	 echo $company_library[$supplier_id];
                                	 // }
                                	 // else
                                	 // {
                                	 // 	 echo $supplier_arr[$supplier_id];
                                	 // }
					            	//echo $supplier_arr[$supplier_id];


									?>

								</td>

							</tr>

							<tr>
								<td  ><b>Wo No.</b>   </td>
								<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
							</tr>

							<tr>
								<td style="font-size:12px"><b>Attention</b></td>
								<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
							</tr>

							<tr>
								<td style="font-size:12px"><b>Booking Date</b></td>
								<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>Currency</b></td>
								<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
							</tr>
						</table>
					</td>
					<td width="350"  style="font-size:12px">
						<table width="350" style="" align="left">
							<tr>
								<td  width="120"><b>G/Y Issue Start</b>   </td>
								<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>G/Y Issue End</b></td>
								<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
								<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
							</tr>
							<tr>
								<td style="font-size:12px"><b>D/Y Delivery End</b></td>
								<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
							</tr>
						</table>
					</td>
					<td width="250"  style="font-size:12px">
						<?
						$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
						?>
						<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
					</td>
				</tr>
			</table>
		</br>

		<table width="950" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
			<tr>
				<td width="30" align="center"><strong>Sl</strong></td>
				<td width="65" align="center"><strong>Color</strong></td>
				<td width="80" align="center"><strong>Color Range</strong></td>
				<td  align="center" width="50"><strong>Ref No.</strong></td>
				<td  align="center" width="70"><strong>Style Ref.No.</strong></td>
				<td width="30" align="center"><strong>Yarn Count</strong></td>
				<td width="140" align="center"><strong>Yarn Description</strong></td>
				<td width="60" align="center"><strong>Brand</strong></td>
				<td width="50" align="center"><strong>Lot</strong></td>
				<td width="60" align="center"><strong>WO Qty</strong></td>
				<td  align="center" width="50"><strong>Min Req. Cone</strong></td>
				<td  align="center" width="80"><strong>Sample Develop Id</strong></td>
				<td  align="center" width="80"><strong>Buyer</strong></td>
				<td  align="center" ><strong>Sample Name</strong></td>
			</tr>
			<?
			$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
			foreach($sql_brand as $row_barand)
			{
				$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
				$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
			}
			$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
			$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

			if($db_type==0)
			{
				$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
				from
				wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
				where
				a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
				group by b.count, b.job_no_id, b.yarn_color, b.color_range
				order by b.id";
			}
			else if($db_type==2)
			{
				$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id, listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
				from
				wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
				where
				a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
				group by b.job_no_id, b.yarn_color, b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no, a.ydw_no,b.id
				order by b.id";
			}

			$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				if($product_id)
				{
					$sql_brand=sql_select("select lot,brand from product_details_master where id in($product_id)");
					foreach($sql_brand as $row_barand)
					{
						$lot_amt=$row_barand[csf("lot")];
						$brand=$row_barand[csf("brand")];
					}

				}

			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);
				$all_style_arr[]=$style_ref_sample[$row[csf("job_no_id")]];
				$all_job_arr[]=$row[csf("job_no")];

				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
					<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
					<td align="center"><? echo $row[csf("referance_no")]; ?></td>
					<td align="center">
						<?
						echo $style_ref_sample[$row[csf("job_no_id")]];
						?>
					</td>
					<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?
						echo $row[csf("yarn_description")];
						?>
					</td>
					<td><? echo $brand_arr[$brand]; ?></td>
					<td align="center"><? echo $lot_amt; ?></td>
					<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
					<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
					<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
					<td align="center">
						<?
						$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
						$sample_name_group="";
						foreach($sample_name_arr as $val)
						{
							if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
						}
						echo $sample_name_group;
						?>
					</td>
				</tr>
				<?
				$i++;
				$yarn_count_des="";
				$style_no="";
			}
			?>
			<tr>
				<td colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo $total_qty; ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>


		<!--==============================================AS PER GMTS COLOR START=========================================  -->
		<table width="950" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($work_order,"100%",335);?>

	</div>
	<div>
		<?
		echo signature_table(122, $cbo_company_name, "950px");
		echo "****".custom_file_name($txt_booking_no,implode(',',$all_style_arr),implode(',',$all_job_arr));
		?>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

if($action=="sales_order_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$cbo_service_type=str_replace("'","",$cbo_service_type);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	if($show_val_column == 1)
	{
		$colspan = "13";
	}else{
		$colspan = "12";
	}
	?>
	<div style="width:950px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo "<b>$company_library[$cbo_company_name]</b>";
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong style="font-size: 16px"><? echo "Yarn ". $yarn_issue_purpose[$cbo_service_type];?> Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,b.job_no, c.sales_booking_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b, fabric_sales_order_mst c where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.job_no_id = c.id and a.id=$update_id group by a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency,b.job_no, c.sales_booking_no");

		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
			$job_no=$result[csf('job_no')];
			//$booking_no=$result[csf('sales_booking_no')];
		}
		$varcode_work_order_no=$work_order;
		?>
		<table width="950" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<b><? echo $supplier_arr[$supplier_id];?></b></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<b><? echo $work_order;?></b></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>
					</table>
				</td>
				<td width="350" style="font-size:12px">
					<table width="350" style="" align="left">
                			<!-- <tr>
                			                                <td  width="120"><b>Sales Order No.</b>   </td>
                			                                <td width="120" >:&nbsp;&nbsp;<? echo $job_no;?></td>
                			                            </tr> -->

                			                            <tr>
                			                            	<td style="font-size:12px"><b>Currency</b></td>
                			                            	<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
                			                            </tr>
                            <!-- <tr>
                                <td style="font-size:12px"><b>Booking No</b></td>
                                <td >:&nbsp;&nbsp;<? //echo $booking_no; ?></td>
                            </tr>  -->
                            <tr>
                            	<td style="font-size:12px"><b>Booking Date</b></td>
                            	<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
                            </tr>
                            <tr>
                            	<td  width="120"><b>Delivery Date</b>   </td>
                            	<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
                            </tr>

                        </table>
                    </td>


                </tr>
            </table>
        </br>

        <?
        $buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
        $sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id,b.job_no
        from
        wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
        where
        a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
        $sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
        foreach($sql_result as $row)
        {
        	if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

        	if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

        	if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

        	if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

        }

        ?>


        <table width="1050" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
        		<tr>
        			<th width="30" align="center"><strong>Sl</strong></th>
        			<th width="100" align="center"><strong>Sales Order</strong></th>
        			<th width="100" align="center"><strong>Booking No.</strong></th>
        			<th width="60" align="center"><strong>Lot</strong></th>
        			<th width="30" align="center"><strong>Yarn Count</strong></th>
        			<th width="160" align="center"><strong>Yarn Description</strong></th>
        			<th width="60" align="center"><strong>Brand</strong></th>

        			<th width="60" align="center"><strong>WO Qty</strong></th>
        			<?
        			if($show_val_column == 1)
        			{
        				?>
        				<th width="50" align="right"><strong>Rate</strong></th>
        				<?
        			}
        			?>
        			<th width="80" align="right"><strong>Amount</strong></th>
        			<th width="80" align="right"><strong>No of Bag</strong></th>
        			<th width="80" align="right"><strong>No of Cone</strong></th>
        			<th  align="center" width="60" ><strong>Min Req. Cone</strong></th>
        			<th  align="left" ><strong>Remarks</strong></td>
        			</tr>
        		</thead>
        		<?

        		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
        		foreach($sql_brand as $row_barand)
        		{
        			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
        			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
        		}

        		if($db_type==0) $select_f_grp="group by yarn_color, color_range,
        		id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";
        		else if($db_type==2) $select_f_grp="group by yarn_color, color_range,
        		id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";

			/* $sql_color="select id,product_id,job_no,no_of_bag,no_of_cone,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
			from
					wo_yarn_dyeing_dtls, fabric_sales_order_mst c
			where
			b.job_no_id = c.id and status_active=1 and id in($total_dtls_id) $select_f_grp ";*/

			$sql_color="select a.id,a.product_id,a.job_no,a.no_of_bag,a.no_of_cone,a.job_no_id,a.yarn_color,a.yarn_description,a.count,a.color_range,sum(a.yarn_wo_qty) as yarn_wo_qty,a.dyeing_charge,sum(a.amount) as amount,
			a.min_require_cone,a.referance_no, a.remarks, b.sales_booking_no from wo_yarn_dyeing_dtls a, fabric_sales_order_mst b
			where a.job_no_id = b.id and a.status_active=1 and a.id in($total_dtls_id) group by a.yarn_color, a.color_range, a.id,a.product_id,a.job_no,a.job_no_id,a.yarn_description,
			a.count,a.dyeing_charge,a.min_require_cone,a.referance_no,a.no_of_cone,a.no_of_bag, a.remarks ,b.sales_booking_no
			order by a.id";



			//echo $sql_color;die;
			$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
			foreach($sql_result as $row)
			{
				$product_id=$row[csf("product_id")];
				//var_dump($product_id);
				if($row[csf("product_id")]!="")
				{
					$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
					$brand=$product_lot[$row[csf("product_id")]]['brand'];
				}

			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";


				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center"><? echo $row[csf("job_no")]; ?></td>
					<td align="center"><? echo $row[csf("sales_booking_no")]; ?></td>
					<td align="center"><? echo $lot_amt; ?></td>
					<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
					<td>
						<?
						echo $row[csf("yarn_description")];
						?>
					</td>
					<td><? echo $brand_arr[$brand]; ?></td>

					<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
					<?
					if($show_val_column == 1)
					{
						?>
						<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
						<?
					}
					?>
					<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?></td>
					<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
					<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
					<td align="right"><? echo $row[csf("min_require_cone")]; ?></td>
					<td align="left"><? echo $row[csf("remarks")]; ?></td>
				</tr>
				<?
				$i++;
				$yarn_count_des="";
				$style_no="";
			}
			?>
			<tr>
				<td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
				<td align="right" ><b><? echo $total_qty; ?></b></td>
				<?
				if($show_val_column == 1)
				{
					?>
					<td align="right">&nbsp;</td>
					<?
				}
				?>
				<td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<?
			$mcurrency="";
			$dcurrency="";
			if($currency_id==1)
			{
				$mcurrency='Taka';
				$dcurrency='Paisa';
			}
			if($currency_id==2)
			{
				$mcurrency='USD';
				$dcurrency='CENTS';
			}
			if($currency_id==3)
			{
				$mcurrency='EURO';
				$dcurrency='CENTS';
			}
			?>
			<tr>
				<td colspan="14" align="center">Total Dyeing Amount (in word): &nbsp;<?  echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency);?> </td>
			</tr>
		</table>


		<!--==============================================AS PER GMTS COLOR START=========================================  -->
		<table width="1050" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>

		<? echo get_spacial_instruction($work_order,"100%",335);?>

	</div>
	<div>
		<?
		echo signature_table(122, $cbo_company_name, "950px");
		echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
		?>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
	</script>
	<?
	exit();
}

//==============================================end============


if ($action=="load_drop_down_sample")
{
	//echo create_drop_down( "cbo_buyer_name", 145, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );

	$sql="Select b.id, b.sample_name from  lib_sample b,  sample_development_dtls a where a.sample_name=b.id and b.status_active=1 and a.sample_mst_id=$data";
	echo create_drop_down( "cbo_sample_name", 70, $sql,"id,sample_name", 1, "-select-", $selected,"","0" );
	exit();
}




if($action=="dyeing_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_rate").val(str);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:590px;" >
		<fieldset>
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="40">Sl No.</th>
							<th width="170">Const. Compo.</th>
							<th width="100">Process Name</th>
							<th width="100">Color</th>
							<th width="90">Rate</th>
							<th>UOM</th>
						</tr>
					</thead>
				</table>
				<?
				$sql="select id,comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,status_active from lib_subcon_charge where comapny_id=$company and process_id=30 and status_active=1";
				//echo $sql;
				$sql_result=sql_select($sql);
				?>
				<div style="width:570px; overflow-y:scroll; max-height:240px;font-size:12px; overflow-x:hidden; cursor:pointer;">
					<table width="570" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" id="table_charge">
						<tbody>
							<?
							$i=1;
							foreach($sql_result as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<?  echo $bgcolor; ?>" onClick="js_set_value(<? echo $row[csf("in_house_rate")]; ?>)">
									<td width="40" align="center"><? echo $i;  ?></td>
									<td width="170"><? echo $row[csf("const_comp")]; ?></td>
									<td width="100" align="center"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?></td>
									<td width="100"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
									<td width="90" align="right"><? echo number_format($row[csf("in_house_rate")],2); ?></td>
									<td><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" id="hidden_rate" />
						</tbody>
					</table>
				</div>
			</form>
		</fieldset>
	</div>
</body>
<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?
exit();
}
if($action=="lot_search_popup23")//Old
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	$brand_arr=return_library_array( "Select id, brand_name from  lib_brand where  status_active=1",'id','brand_name');
	extract($_REQUEST);
	$company=str_replace("'","",$company);
	$job_no=str_replace("'","",$job_no);
	//echo $job_no;die;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hidden_product").val(str);
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div style="width:595px;" >
		<fieldset>
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<tr>
							<th width="40">Sl No.</th>
							<th width="100">Lot No</th>
							<th width="90">Brand</th>
							<th width="200">Product Name Details</th>
							<th width="80">Stock</th>
							<th>UOM</th>
						</tr>
					</thead>
				</table>
				<?

				$sql="select id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,brand,current_stock,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color from product_details_master where company_id='$company' and item_category_id=1";

					//echo $sql;
				$sql_result=sql_select($sql);
				?>
				<div style="width:595px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;">
					<table width="595" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="table_charge" style="cursor:pointer" rules="all">
						<tbody>
							<?
							$i=1;
							foreach($sql_result as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $composition[$row[csf("yarn_comp_type1st")]]." ". $row[csf("yarn_comp_percent1st")]." ". $composition[$row[csf("yarn_comp_type2nd")]]." ". $row[csf("yarn_comp_percent2nd")]." ". $yarn_type[$row[csf("yarn_type")]]." ". $color_arr[$row[csf("color")]]; ?>,<? echo $row[csf("yarn_count_id")]; ?>,<? echo $row[csf("lot")]; ?>,<? echo $row[csf("id")]; ?>')">
									<td width="40" align="center"><p><? echo $i;  ?></p></td>
									<td width="100" align="center"><p><? echo $row[csf("lot")]; ?></p></td>
									<td width="90"><p><? echo $brand_arr[$row[csf("brand")]]; ?></p></td>
									<td width="200">v<? echo $row[csf("product_name_details")]; ?></p></td>
									<td width="80" align="right"><p><? echo $row[csf("current_stock")]; ?></p></td>
									<td><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" id="hidden_product" style="width:200px;" />
						</tbody>
					</table>
				</div>
			</form>
		</fieldset>
	</div>
</body>
<script  type="text/javascript">setFilterGrid("table_charge",-1)</script>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	//}

exit();
}

//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );



if($action=="show_with_multiple_job")
{
	//echo "xxxx";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<div style="width:900px" align="center">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="750">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:20px;">
								<?php
echo $company_library[$cbo_company_name];
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:20px">
								<strong>Yarn Dyeing Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id";
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.currency from wo_yarn_dyeing_mst a where a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];

		}
		$varcode_work_order_no=$work_order;

		?>
		<table width="900" style="" align="center">
			<tr>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr  >
							<td   width="120"><b>To</b>   </td>
							<td width="230">:&nbsp;&nbsp;<? echo $supplier_arr[$supplier_id];?></td>
						</tr>

						<tr>
							<td  ><b>Wo No.</b>   </td>
							<td  >:&nbsp;&nbsp;<? echo $work_order;?>    </td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Attention</b></td>
							<td >:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>

						<tr>
							<td style="font-size:12px"><b>Booking Date</b></td>
							<td >:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>Currency</b></td>
							<td >:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
					</table>
				</td>
				<td width="350"  style="font-size:12px">
					<table width="350" style="" align="left">
						<tr>
							<td  width="120"><b>G/Y Issue Start</b>   </td>
							<td width="230" >:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>G/Y Issue End</b></td>
							<td >:&nbsp;&nbsp;<? if($delivery_date_end!="0000-00-00" || $delivery_date_end!="") echo change_date_format($delivery_date_end);  else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery Start </b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_start!="0000-00-00" || $dy_delivery_start!="") echo change_date_format($dy_delivery_start); else echo ""; ?></td>
						</tr>
						<tr>
							<td style="font-size:12px"><b>D/Y Delivery End</b></td>
							<td >:&nbsp;&nbsp;<? if($dy_delivery_end!="0000-00-00" || $dy_delivery_end!="") echo change_date_format($dy_delivery_end); else echo "";?></td>
						</tr>
					</table>
				</td>
				<td width="200"  style="font-size:12px">
					<?
					$image_location=return_field_value("image_location","common_photo_library","master_tble_id='$update_id' and form_name='$form_name'","image_location");
					?>
					<img src="<? echo '../../'.$image_location; ?>" width="120" height="100" border="2" />
				</td>
			</tr>
		</table>
	</br>

	<table width="1080" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
		<tr>
			<td width="30" align="center"><strong>Sl</strong></td>
			<td width="65" align="center"><strong>Color</strong></td>
			<td width="80" align="center"><strong>Color Range</strong></td>
			<td  align="center" width="50"><strong>Ref No.</strong></td>
			<td  align="center" width="70"><strong>Style Ref.No.</strong></td>
			<td width="30" align="center"><strong>Yarn Count</strong></td>
			<td width="140" align="center"><strong>Yarn Description</strong></td>
			<td width="60" align="center"><strong>Brand</strong></td>
			<td width="50" align="center"><strong>Lot</strong></td>
			<td width="60" align="center"><strong>WO Qty</strong></td>
			<td width="50" align="center"><strong>Dyeing Rate</strong></td>
			<td width="80" align="center"><strong>Amount</strong></td>
			<td  align="center" width="50"><strong>Min Req. Cone</strong></td>
			<td  align="center" width="80"><strong>Sample Develop Id</strong></td>
			<td  align="center" width="80"><strong>Buyer</strong></td>
			<td  align="center" ><strong>Sample Name</strong></td>
		</tr>
		<?
		$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
		foreach($sql_brand as $row_barand)
		{
			$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
			$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
		}
		$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
		$style_ref_sample=return_library_array( "select id, style_ref_no from  sample_development_mst",'id','style_ref_no');

		if($db_type==0)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id,b.sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.count, b.job_no_id, b.yarn_color, b.color_range
			order by b.id";
		}
		else if($db_type==2)
		{
			$sql="select a.id, a.ydw_no,b.id as dtls_id,b.product_id,b.job_no,b.job_no_id, listagg(CAST(b.sample_name AS VARCHAR(4000)),',')  within group (order by b.sample_name) as sample_name,b.yarn_color,b.yarn_description,b.count,b.color_range,sum(b.yarn_wo_qty) as yarn_wo_qty,b.dyeing_charge,sum(b.amount) as amount,b.min_require_cone,b.referance_no
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id
			group by b.job_no_id, b.yarn_color, b.color_range,a.id,b.product_id,b.job_no,b.job_no_id,b.yarn_color,b.yarn_description,b.count,b.color_range,b.dyeing_charge,b.min_require_cone,b.referance_no, a.ydw_no,b.id
			order by b.id";
		}

			//echo $sql;die;
		$sql_result=sql_select($sql);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
		foreach($sql_result as $row)
		{
			$product_id=$row[csf("product_id")];
			if($row[csf("product_id")]!="")
			{
				$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
				$brand=$product_lot[$row[csf("product_id")]]['brand'];
			}

			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";

			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
				<td><? echo $color_range[$row[csf("color_range")]]; ?></td>
				<td align="center"><? echo $row[csf("referance_no")]; ?></td>
				<td align="center">
					<?
					echo $style_ref_sample[$row[csf("job_no_id")]];
					?>
				</td>
				<td align="center"><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
				<td>
					<?
					echo $row[csf("yarn_description")];
					?>
				</td>
				<td><? echo $brand_arr[$brand]; ?></td>
				<td align="center"><? echo $lot_amt; ?></td>
				<td align="right"><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>
				<td align="right"><? echo $row[csf("dyeing_charge")]; ?>&nbsp;</td>
				<td align="right"><? echo number_format($row[csf("amount")],2);  $total_amount+=$row[csf("amount")];?> &nbsp;</td>
				<td align="center"><? echo $row[csf("min_require_cone")]; ?></td>
				<td align="center"><? echo $row[csf("job_no_id")]; ?></td>
				<td align="center"><? echo $buyer_arr[$buyer_sample[$row[csf("job_no_id")]]]; ?> </td>
				<td align="center">
					<?
					$sample_name_arr=array_unique(explode(",",$row[csf('sample_name')]));
					$sample_name_group="";
					foreach($sample_name_arr as $val)
					{
						if($sample_name_group=="") $sample_name_group=$sample_arr[$val]; else $sample_name_group=$sample_name_group.",".$sample_arr[$val];
					}
					echo $sample_name_group;
					?>
				</td>
			</tr>
			<?
			$i++;
		}
		?>
		<tr>
			<td colspan="9" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
			<td align="right" ><b><? echo $total_qty; ?></b></td>
			<td align="right">&nbsp;</td>
			<td align="right"><b><? echo number_format($total_amount,2); ?></b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?
		$mcurrency="";
		$dcurrency="";
		if($currency_id==1)
		{
			$mcurrency='Taka';
			$dcurrency='Paisa';
		}
		if($currency_id==2)
		{
			$mcurrency='USD';
			$dcurrency='CENTS';
		}
		if($currency_id==3)
		{
			$mcurrency='EURO';
			$dcurrency='CENTS';
		}
		?>
		<tr>
			<td colspan="16" align="center">Total Dyeing Amount (in word): &nbsp;<? echo number_to_words(def_number_format($total_amount,2,""),$mcurrency, $dcurrency); //echo number_to_words($total_amount,"USD", "CENTS");?> </td>
		</tr>
	</table>


	<!--==============================================AS PER GMTS COLOR START=========================================  -->
	<table width="1080" style="" align="center"><tr><td><strong>Note:</strong></td></tr></table>
	<? echo get_spacial_instruction($work_order,"100%",335);?>

</div>
<div>
	<?
	echo signature_table(122, $cbo_company_name, "1080px");
	echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
	?>
</div>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
<script>
	fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
</script>
<?
exit();
}



if($action=="show_without_rate_booking_report")
{
	//echo "uuuu";die;
	extract($_REQUEST);
	$form_name=str_replace("'","",$form_name);
	$cbo_service_type=str_replace("'","",$cbo_service_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$update_id=str_replace("'","",$update_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id,brand_name from  lib_brand",'id','brand_name');
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$supplier_name=return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$yern_count=return_library_array("select id,yarn_count from  lib_yarn_count", "id", "yarn_count");
	$fin_composition=return_library_array("select id,composition_name from  lib_composition_array", "id", "composition_name");
	$pay_mode = str_replace("'","",$cbo_pay_mode);
	$new_supplier_name = str_replace("'","",$cbo_supplier_name);


	//$po_qnty_tot=return_field_value( "sum(plan_cut)", "wo_po_break_down","id in(".str_replace("'","",$txt_order_no_id).")");
	?>
	<style type="text/css">
		table.mainTable tr td {
			border: 1px solid #000;
		}

	</style>
	<div style="width:900px; margin-left: 5px">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="700">
					<table width="100%" cellpadding="0" cellspacing="0" >
						<tr>
							<td align="center" style="font-size:23px;">
								<?php
echo "<b>$company_library[$cbo_company_name]</b>";
?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:18px">
								<?
								$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$cbo_company_name");
								foreach ($nameArray as $result)
								{
									?>

									Email Address: <? echo $result[csf('email')];?>
									Website No: <? echo $result[csf('website')];
								}

								?>
							</td>
						</tr>
						<tr>
							<td align="center" style="font-size:22px">
								<strong style="font-size: 16px"><? echo "Yarn ". $yarn_issue_purpose[$cbo_service_type];?> Work Order </strong>
							</td>
						</tr>
					</table>
				</td>
				<td width="250" id="barcode_img_id">

				</td>
			</tr>
		</table>
		<?
		//echo "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention  from wo_yarn_dyeing_mst a where a.id=$update_id";die;
		$nameArray=sql_select( "select a.id, a.ydw_no,a.booking_date,a.supplier_id,a.attention,a.delivery_date,a.delivery_date_end,a.dy_delivery_date_start,a.dy_delivery_date_end,a.ref_no,a.currency,b.job_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.id=$update_id");
		foreach ($nameArray as $result)
		{
			$work_order=$result[csf('ydw_no')];
			$supplier_id=$result[csf('supplier_id')];
			$booking_date=$result[csf('booking_date')];
			$currency_val=$result[csf('currency')];
			$attention=$result[csf('attention')];
			$delivery_date=$result[csf('delivery_date')];
			$delivery_date_end=$result[csf('delivery_date_end')];
			$dy_delivery_start=$result[csf('dy_delivery_date_start')];
			$dy_delivery_end=$result[csf('dy_delivery_date_end')];
			$currency_id=$result[csf('currency')];
			$job_no=$result[csf('job_no')];
			$ref_no=$result[csf('ref_no')];
		}
		if ($job_no!="") { $internal_ref_arr=return_library_array( "select job_no_mst,grouping from  wo_po_break_down where job_no_mst='$job_no'",'job_no_mst','grouping');}

		$varcode_work_order_no=$work_order;
		?>
		<table width="920" style="" align="center">
			<tr>
				<td width="350"  style="font-size:16px">
					<table width="350" style="" align="left" >
						<tr  >
							<td   width="120" style="font-size:16px">To</td>
							<td width="230" style="font-size:16px">:&nbsp;&nbsp;<?
							if($pay_mode==3 || $pay_mode==5)
							{
								echo $company_library[$new_supplier_name];
							}
							else
							{
								echo $supplier_name[$new_supplier_name];
							}

                               // echo $supplier_arr[$supplier_id];

							?></td>
						</tr>

						<tr>
							<td style="font-size:16px">Wo No.</td>
							<td style="font-size:16px">:&nbsp;&nbsp;<? echo $work_order;?></td>
						</tr>
						<tr>
							<td style="font-size:16px">Internal Ref. No</td>
							<td style="font-size:16px">:&nbsp;&nbsp;<? echo $internal_ref_arr[$job_no]; ?></td>
						</tr>
						<tr>
							<td style="font-size:16px">Attention</td>
							<td style="font-size:16px">:&nbsp;&nbsp;<? echo $attention; ?></td>
						</tr>
					</table>
				</td>
				<td width="370" style="font-size:16px">
					<table width="370" style="" align="left">
						<tr>
							<td  width="120" style="font-size:16px">Job No.</td>
							<td width="120" style="font-size:16px">:&nbsp;&nbsp;<? echo $job_no;?></td>

							<td  width="140" style="font-size:16px">Ref. No</td>
							<td width="240" colspan="2" style="font-size:16px">:&nbsp;&nbsp;<? echo $ref_no;?></td>
						</tr>

						<tr>
							<td style="font-size:16px">Currency</td>
							<td style="font-size:16px">:&nbsp;&nbsp;<? echo $currency[$currency_val]; ?></td>
						</tr>
						<tr>
							<td style="font-size:16px">Booking Date</td>
							<td style="font-size:16px">:&nbsp;&nbsp;<? if($booking_date!="0000-00-00" || $booking_date!="") echo change_date_format($booking_date); else echo ""; ?></td>
						</tr>
						<tr>
							<td  width="120" style="font-size:16px">Delivery Date</td>
							<td width="230" style="font-size:16px">:&nbsp;&nbsp;<? if($delivery_date!="0000-00-00" || $delivery_date!="") echo change_date_format($delivery_date); else echo "";?>    </td>
						</tr>

					</table>
				</td>


			</tr>
		</table>
	</br>

	<?
			$buyer_sample=return_library_array( "select id, buyer_name from  sample_development_mst",'id','buyer_name');
			$sql="select a.id, a.ydw_no,b.job_no_id,b.sample_name,b.id as dtls_id
			from
			wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b
			where
			a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.id=b.mst_id and a.id=$update_id";
					//echo $sql;
			$sql_result=sql_select($sql);$total_samp_deve_id="";$total_buyer="";$total_dtls_id='';$total_sample_name='';
			foreach($sql_result as $row)
			{
				if($total_dtls_id=='') $total_dtls_id=$row[csf("dtls_id")]; else $total_dtls_id=$total_dtls_id.",".$row[csf("dtls_id")];

				if($total_samp_deve_id=="") $total_samp_deve_id=$row[csf("job_no_id")]; else $total_samp_deve_id=$total_samp_deve_id.",".$row[csf("job_no_id")];

				if($total_buyer=="") $total_buyer=$buyer_sample[$row[csf('job_no_id')]]; else $total_buyer=$total_buyer.",".$buyer_sample[$row[csf('job_no_id')]];

				if($total_sample_name=="") $total_sample_name=$row[csf("sample_name")]; else $total_sample_name=$total_sample_name.",".$row[csf("sample_name")];

			}
		//var_dump($total_dtls_id);
		//die;

			?>




			<table class="mainTable" width="920" style="" align="center" border="1"  cellpadding="0" cellspacing="0" rules="all">
				<tr>
					<td width="30" align="center"><strong>Sl</strong></td>
					<td width="60" align="center"><strong>Lot</strong></td>
					<td width="60" align="center"><strong>Yarn Count</strong></td>
					<td width="160" align="center"><strong>Yarn Description</strong></td>
					<td width="60" align="center"><strong>Brand</strong></td>
					<td width="60" align="center"><strong>WO Qty</strong></td>
					<td width="80" align="center"><strong>No of Bag</strong></td>
					<td width="80" align="center"><strong>No of Cone</strong></td>
					<td  align="center" width="60"><strong>Min Req. Cone</strong></td>
					<td  align="center" width="100" ><strong>Remarks</strong></td>
					<td  align="center"><strong>Swatch</strong></td>
				</tr>
				<?

				$sql_brand=sql_select("select id,lot,brand from product_details_master where status_active=1");
				foreach($sql_brand as $row_barand)
				{
					$product_lot[$row_barand[csf("id")]]['lot']=$row_barand[csf("lot")];
					$product_lot[$row_barand[csf("id")]]['brand']=$row_barand[csf("brand")];
				}

				if($db_type==0) $select_f_grp="group by mst_id,yarn_color, color_range,
				id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";
				else if($db_type==2) $select_f_grp="group by mst_id,yarn_color, color_range,
				id,product_id,job_no,job_no_id,yarn_description,count,dyeing_charge,min_require_cone,referance_no,no_of_cone,no_of_bag, remarks order by id ";

				$sql_color="select id,mst_id,product_id,job_no,no_of_bag,no_of_cone,job_no_id,yarn_color,yarn_description,count,color_range,sum(yarn_wo_qty) as yarn_wo_qty,dyeing_charge,sum(amount) as amount,min_require_cone,referance_no, remarks
				from
				wo_yarn_dyeing_dtls
				where
				status_active=1 and id in($total_dtls_id) $select_f_grp ";
			//echo $sql_color;die;
				$sql_result=sql_select($sql_color);$total_qty=0;$total_amount=0;$i=1;$buyer=0;$order_no="";
				$mstID="";
				foreach($sql_result as $row)
				{
					$mstID=$row[csf("mst_id")];
					$product_id=$row[csf("product_id")];
				//var_dump($product_id);
					if($row[csf("product_id")]!="")
					{
						$lot_amt=$product_lot[$row[csf("product_id")]]['lot'];
						$brand=$product_lot[$row[csf("product_id")]]['brand'];
					}

			//$yarn_count_des=explode(" ",$row[csf("yarn_description")]);

					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";


					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center" height="90"><? echo $i; ?></td>
						<td><? echo $lot_amt; ?></td>
						<td><? echo $count_arr[$row[csf("count")]]; //$count_arr[$row[csf("count")]]; ?></td>
						<td>
							<?
							echo $row[csf("yarn_description")];
							?>
						</td>
						<td><? echo $brand_arr[$brand]; ?></td>

						<td><? echo $row[csf("yarn_wo_qty")]; $total_qty+=$row[csf("yarn_wo_qty")]; ?>&nbsp;</td>

						<td><? echo $row[csf("no_of_bag")]; ?></td>
						<td><? echo $row[csf("no_of_cone")]; ?></td>
						<td><? echo $row[csf("min_require_cone")]; ?></td>
						<td><? echo $row[csf("remarks")]; ?></td>
						<td>&nbsp;</td>
					</tr>
					<?
					$i++;
					$yarn_count_des="";
					$style_no="";
				}
				?>
				<tr>
					<td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
					<td align="right" ><b><? echo $total_qty; ?></b></td>
					<td align="right">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<?
				$mcurrency="";
				$dcurrency="";
				if($currency_id==1)
				{
					$mcurrency='Taka';
					$dcurrency='Paisa';
				}
				if($currency_id==2)
				{
					$mcurrency='USD';
					$dcurrency='CENTS';
				}
				if($currency_id==3)
				{
					$mcurrency='EURO';
					$dcurrency='CENTS';
				}
				?>
			</table>
			<br/>
			<table class="mainTable" width="600" style="" border="1"  cellpadding="0" cellspacing="0" rules="all">
				<caption><b> Finish  Product Details</b> </caption>
				<?
				$sql_finish_product_dtls = "select id,yarn_count,yarn_comp,yarn_perc,yarn_type,yarn_color from wo_yarn_dyeing_dtls_fin_prod where mst_id = $update_id";
				$dtls_result=sql_select($sql_finish_product_dtls);
				?>
				<tr>
					<td width="30" align="center"><strong>Sl</strong></td>
					<td width="180" align="center"><strong>Yarn Composition</strong></td>
					<td width="100" align="center"><strong>Count</strong></td>
					<td width="100" align="center"><strong>Type</strong></td>
					<td width="100" align="center"><strong>Color</strong></td>
					<td width="80" align="center"><strong>Qnty</strong></td>
				</tr>
				<?
				$total_color_qty=0; $d=1;
				foreach($dtls_result as $row)
				{
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $d; ?></td>
						<td align="center"><? echo $fin_composition[$row[csf("yarn_comp")]];?></td>
						<td align="center"><? echo $yern_count[$row[csf("yarn_count")]];?></td>
						<td align="center"><? echo $yarn_type[$row[csf("yarn_type")]];?></td>
						<td><? echo $color_arr[$row[csf("yarn_color")]];?></td>
						<td align="right"><? echo $total_qty; ?></td>

					</tr>
					<?
					$d++;
				}
				?>
				<tr>
					<td colspan="5" align="right"><strong>Total:</strong>&nbsp;&nbsp;</td>
					<td align="right" ><b><? echo $total_qty; ?></b></td>
				</tr>

			</table>


			<!--==============================================AS PER GMTS COLOR START=========================================  -->
			<br>
			<br>

			<? echo get_spacial_instruction($work_order,"100%",335);?>
		</div>
		<div>
			<?
			echo signature_table(122, $cbo_company_name, "920px");
			echo "****".custom_file_name($txt_booking_no,$style_sting,$txt_job_no);
			?>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
		<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			fnc_generate_Barcode('<? echo $varcode_work_order_no; ?>','barcode_img_id');
		</script>
		<?
		exit();
	}



	if($action == "populate_field_level_access_data")
	{
		list($page_id,$company_id) = explode("_", $data);

		$sql = "select a.field_name,a.is_disable,a.defalt_value from field_level_access a where a.page_id = '$page_id' and a.is_deleted = 0 and a.company_id = '$company_id' and a.user_id = '$user_id' " ;
		$res = sql_select($sql);
		if(count($res) > 0)
		{
			foreach($res as $row)
			{
				echo "$('#".$row[csf("field_name")]."').val(".$row[csf("defalt_value")].");\n";
				if($row[csf("is_disable")] == 1)
				{
					echo "$('#".$row[csf("field_name")]."').attr('disabled','disabled');\n";
					echo "$('#cbo_with_order').val(1).attr('disabled','disabled');\n";
				}
				echo "change_job_title(".$row[csf("defalt_value")].");\n";
			}
		}else
		{
			echo "$('#cbo_is_sales_order').removeAttr('disabled','disabled');\n";
			echo "$('#cbo_is_sales_order').val('2');\n";
			echo "change_job_title(2);\n";
			echo "$('#cbo_with_order').removeAttr('disabled','disabled');\n";
		}

		exit();
	}


	?>
