<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" )
{
	header("location:login.php"); die;
}
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$cbo_company_name=str_replace("'","",$data[0]);
	$cbo_item_category_id=str_replace("'","",$data[1]);
	$txt_item_acc=str_replace("'","",$data[2]);
	$txt_product_id_des=str_replace("'","",$data[3]);
	$txt_product_id_no=str_replace("'","",$data[4]);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

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

			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 );
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 );
			num 	= num.substr( 0, num.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_no').val( num );
		}

	</script>

	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="100">Product Id</th>
						<th width="200">Item Description</th>
						<th width="100">Gsm</th>
						<th width="100">Dia</th>
						<th><input type="reset" id="" value="Reset" style="width:80px;" class="formbutton" /></th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_prod_id" id="txt_prod_id" /></td>
						<td align="center"><input type="text" style="width:160px" class="text_boxes"  name="txt_item_description" id="txt_item_description" /></td>
						<td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_gsm" id="txt_gsm" /></td>
						<td align="center"><input type="text" style="width:70px" class="text_boxes"  name="txt_dia" id="txt_dia" /></td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('txt_prod_id').value+'_'+document.getElementById('txt_item_description').value+'_'+document.getElementById('txt_gsm').value+'_'+document.getElementById('txt_dia').value+'_'+<? echo $cbo_company_name; ?>+'_'+<? echo $cbo_item_category_id; ?>+'_'+'<? echo $txt_item_acc; ?>'+'_'+'<? echo $txt_product_id_des; ?>'+'_'+'<? echo $txt_product_id_no; ?>', 'create_item_search_list_view', 'search_div', 'closing_stock_report_controller', '');" style="width:80px;" />
						</td>
					</tr>
				</tbody>
			</table>
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div>
		</form>

	</div>


	<?

}
if ($action=="load_drop_down_store")
{
	//$data=explode("**",$data);
	//if($data[1]==2) $disable=1; else $disable=0;

	//$store_cond = ($userCredential[0][csf("store_location_id")]) ? " and a.id in (".$userCredential[0][csf("store_location_id")].")" : "" ;
	echo create_drop_down( "cbo_store_id", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2,3)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}
if ($action=="create_item_search_list_view")
{
	$ex_data=explode("_",$data);
	$txt_prod_id=str_replace("'","",$ex_data[0]);
	$txt_item_description=str_replace("'","",$ex_data[1]);
	$txt_gsm=str_replace("'","",$ex_data[2]);
	$txt_dia=str_replace("'","",$ex_data[3]);
	$cbo_company_name=str_replace("'","",$ex_data[4]);
	$cbo_item_category_id=str_replace("'","",$ex_data[5]);
	$txt_item_acc=str_replace("'","",$ex_data[6]);
	$txt_product_id_des=str_replace("'","",$ex_data[7]);
	$txt_product_id_no=str_replace("'","",$ex_data[8]);

	$sql_cond_all="";

	if($txt_prod_id!="") $sql_cond_all=" and id=$txt_prod_id";
	if($txt_item_description!="") $sql_cond_all.=" and item_description like '%$txt_item_description'";
	if($txt_gsm!="") $sql_cond_all.=" and gsm='$txt_gsm'";
	if($txt_dia!="") $sql_cond_all.=" and dia_width='$txt_dia'";
	if($cbo_company_name!=0) $sql_cond_all.=" and company_id=$cbo_company_name";
	if($cbo_item_category_id!=0) $sql_cond_all.=" and item_category_id=$cbo_item_category_id";

	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	$sql="SELECT id,item_description,color,gsm,dia_width,supplier_id from  product_details_master where status_active=1 and is_deleted=0 $sql_cond_all";

	$arr=array(1=>$color_arr,4=>$supplierArr);
	echo  create_list_view("list_view", "Item Description,Color,Gsm,Dia,Supplier,Product ID", "150,120,70,70,130","680","300",0, $sql , "js_set_value", "id,item_description", "", 1, "0,color,0,0,supplier_id,0", $arr , "item_description,color,gsm,dia_width,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0','',1) ;

	echo "<input type='hidden' id='txt_selected_id' />";
	echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";
	exit();
}

if($action=="booking_no_popup")
	{
		echo load_html_head_contents("Booking Info", "../../../../", 1, 1,'','','');
		extract($_REQUEST);
		?>

		<script>

			var selected_id = new Array; var selected_name = new Array;

			function check_all_datas()
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


				$('#hide_job_id').val( id );
				$('#hide_job_no').val( name );
		}

		function show_list_details()
		{
			var booking_type = '<? echo $booking_type?>';
			if($('#txt_search_common').val() =="" || (booking_type==2 && $('#cbo_search_by').val()==2))
			{
				if( form_validation('txt_date_from*txt_date_to','From Date*To Date')==false )
				{
					return;
				}
			}

			show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $booking_type; ?>'+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'sample_finish_fabric_stock_report_controller', 'setFilterGrid(\'tbl_list_div\',-1)');
		}
	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:740px;">
				<table width="720" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="110">Buyer</th>
						<th width="100">Search By</th>
						<th id="search_by_td_up" width="120">Please Enter Booking No</th>
						<th width="140">Booking Date</th>
						<th colspan="2"><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />

					</thead>
					<tbody>
						<tr>
							<td align="center" width="110">
								<?
								echo create_drop_down( "cbo_buyer_name", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>

							<td align="center">
								<?
								$search_by_arr=array(1=>"Booking No",2=>"Job No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "1",$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td" >
								<input type="text" style="width:120px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td width="140">
                            	<input type="text" name="txt_date_from" id="txt_date_from" value="" class="datepicker" style="width:55px;" readonly/>To
                            	<input type="text" name="txt_date_to" id="txt_date_to" value="" class="datepicker" style="width:55px;" readonly/>
                        	</td>

							</td>
							<td align="center" width="100">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_details ();" style="width:100px;" />
							</td>
						</tr>
						<tr>
                    		<td colspan="5" align="center" ><? echo load_month_buttons(1); ?></td>
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
if($action=="create_booking_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$booking_type=$data[5];
	$date_from 	= trim($data[6]);
	$date_to 	= trim($data[7]);

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
		$buyer_id_cond2=" and a.buyer_id=$data[1]";
	}

	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_cond="and c.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($date_to), "yyyy-mm-dd", "-")."'";
		}else{
			$date_cond="and c.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($date_from),'','',1)."' and '".change_date_format(trim($date_to),'','',1)."'";
		}
	}




	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	$job_no_cond = "";

	if($search_by==2)
	{
		$job_no_cond =" and a.job_no_prefix_num like '".$search_string."'";
	}
	else
	{
		$booking_no_cond = " and b.booking_no like '".$search_string."'";
		$booking_no_cond2 = " and a.booking_no like '".$search_string."'";
	}

	if($db_type==0)
	{
		$year_field_by="and YEAR(c.insert_date)";
		$year_field_by2="and YEAR(a.insert_date)";
	}
	else if($db_type==2)
	{
		$year_field_by=" and to_char(c.insert_date,'YYYY')";
		$year_field_by2=" and to_char(a.insert_date,'YYYY')";
	}
	else {
		$year_field_by="";
		$year_field_by2="";
	}

	if($year_id!=0)
	{
		$year_cond=" $year_field_by=$year_id";
		$year_cond2=" $year_field_by2=$year_id";
	}
	else
	{
		$year_cond="";
	}

	if($booking_type==1)
	{
		 $sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, b.booking_no,c.booking_no_prefix_num,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(4) and c.is_short=2 and a.company_name=$company_id $job_no_cond $buyer_id_cond $year_cond $booking_no_cond $date_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num ";
	}
	else
	{
		$sql ="	select null as job_no, null as job_no_prefix_num, a.company_id as company_name, a.buyer_id, a.booking_no, a.booking_no_prefix_num, a.id as booking_id
		from wo_non_ord_samp_booking_mst a where a.item_category in (2,3) and a.status_active =1 and a.is_deleted =0 and a.company_id=$company_id $buyer_id_cond2 $booking_no_cond2 $year_cond2 $date_cond2";
	}

	/*$sql= "select a.job_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,b.booking_no,c.booking_no_prefix_num,c.id as booking_id  from wo_po_details_master a,wo_booking_dtls b ,wo_booking_mst c where a.job_no=b.job_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and b.booking_type in(4) and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond $month_cond group by a.job_no,b.booking_no,a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no,c.id,c.booking_no_prefix_num ";

	if($search_by != 2)
	{
		$sql .=" union all
		select null as job_no, null as job_no_prefix_num, a.company_id as company_name, a.buyer_id, a.style_desc as style_ref_no, a.booking_no, a.booking_no_prefix_num, a.id as booking_id
		from wo_non_ord_samp_booking_mst a where a.item_category in (2,13) and a.status_active =1 and a.is_deleted =0 and a.company_id=$company_id $buyer_id_cond2 and $search_field2 like '$search_string' ";
	}
	$sql .= " order by booking_no desc";*/

	//echo $sql;//die;
	$sqlResult=sql_select($sql);
	?>
	<div align="center">

		<fieldset style="width:650px;margin-left:10px">
			<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Company</th>
						<th width="110">Buyer</th>
						<th width="110">Job No</th>
						<th width="">Booking No</th>

					</thead>
				</table>
				<div style="width:670px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_div">
						<?
						$i=1;
						foreach($sqlResult as $row )
						{
							if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

							$data=$i.'_'.$row[csf('booking_id')].'_'.$row[csf('booking_no_prefix_num')];
						//echo $data;
							?>
							<tr id="tr_<?php echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onClick="js_set_value2('<? echo $data;?>')">
								<td width="30" align="center"><?php echo $i; ?>
									<td width="130"><p><? echo $company_arr[$row[csf('company_name')]]; ?></p></td>
									<td width="110"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
									<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
									<td width=""><p><? echo $row[csf('booking_no')]; ?></p></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
					<table width="650" cellspacing="0" cellpadding="0" style="border:none" align="center">
						<tr>
							<td align="center" height="30" valign="bottom">
								<div style="width:100%">
									<div style="width:50%; float:left" align="left">
										<input type="checkbox" name="check_all" id="check_all" onClick="check_all_datas()"/>
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


if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$booking_type=str_replace("'","",$booking_type);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_product_id_des=str_replace("'","",$txt_product_id_des);
	$txt_product_id=str_replace("'","",$txt_product_id);
	$report_type=str_replace("'","",$report_type);
	$cbo_store_id=str_replace("'","",$cbo_store_id);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	if($db_type==0)
	{
		$select_from_date=change_date_format($from_date,'yyyy-mm-dd');
		$select_from_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$select_from_date=change_date_format($from_date,'','',1);
		$select_from_to=change_date_format($to_date,'','',1);
	}
	else
	{
		$select_from_date="";
		$select_from_to="";
	}

	$sql_cond=$sql_cond_in=$store_cond_in="";
	if ($cbo_company_name!=0) $sql_cond =" and a.company_id=$cbo_company_name";
	if ($cbo_item_category_id!=0) $sql_cond.=" and a.item_category=$cbo_item_category_id";
	if ($txt_product_id_des!="") $sql_cond.=" and b.prod_id in ($txt_product_id_des)";
	if ($txt_product_id!="") $sql_cond.=" and b.prod_id in ($txt_product_id)";

	if ($cbo_uom!="")
	{
		$sql_cond_uom=" and b.uom in ($cbo_uom)";
		$sql_cond_uom2=" and b.cons_uom in ($cbo_uom)";
	}
	if($cbo_store_id!=0) $store_cond =" and a.store_id=$cbo_store_id";
	if($cbo_store_id!=0) $store_cond2 =" and b.store_id=$cbo_store_id";
	if($txt_booking_no!="") $sql_cond.=" and d.booking_no_prefix_num in ($txt_booking_no) and d.booking_no like '%-".substr($cbo_year, -2)."-%'";
	if($from_date != "" && $to_date != "") $sql_cond.=" and d.booking_date between '$select_from_date' and '$select_from_to'";

	if($txt_booking_no!="") $trans_booking_cond=" and c.booking_no_prefix_num in ($txt_booking_no) and c.booking_no like '%-".substr($cbo_year, -2)."-%'";
	if($from_date != "" && $to_date != "") $trans_booking_cond =" and c.booking_date between '$select_from_date' and '$select_from_to'";

	//==============For Transfer In Cond=======
	if ($cbo_company_name!=0) $sql_cond_in =" and a.company_id=$cbo_company_name";
	if ($cbo_item_category_id!=0) $sql_cond_in.=" and a.item_category=$cbo_item_category_id";
	if ($txt_product_id_des!="") $sql_cond_in.=" and b.to_prod_id in ($txt_product_id_des)";
	if ($txt_product_id!="") $sql_cond_in.=" and b.to_prod_id in ($txt_product_id)";
	if($cbo_store_id!=0) $store_cond_in =" and b.to_store_id=$cbo_store_id";
	if($txt_booking_no!="") $sql_cond_in.=" and c.booking_no_prefix_num in ($txt_booking_no) and c.booking_no like '%-".substr($cbo_year, -2)."-%'";
	if($from_date != "" && $to_date != "") $sql_cond_in.=" and c.booking_date between '$select_from_date' and '$select_from_to'";


	if($booking_type==1)
	{
		$sql_production_rcv = sql_select("SELECT a.id as rcv_id, a.recv_number,b.trans_id, b.prod_id, b.fabric_description_id, b.body_part_id, b.gsm,b.width,b.rack_no,b.shelf_no, b.id as dtls_id, b.receive_qnty, c.id as batch_id, c.booking_no, c.booking_without_order, d.buyer_id, e.style_ref_no, e.job_no, b.order_id, b.order_id as po_ids, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
		from  inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, wo_booking_mst d, wo_po_details_master e
		where  a.entry_form = 7 and a.id = b.mst_id and b.batch_id = c.id and c.batch_against = 3 $sql_cond $store_cond $sql_cond_uom and c.booking_no_id = d.id and d.booking_type = 4 and d.job_no = e.job_no and c.booking_without_order =0 and a.receive_basis = 5 and a.status_active  =1  and c.status_active=1 and b.trans_id<>0");
	}
	else
	{
		$sql_production_rcv = sql_select("SELECT a.id as rcv_id, a.recv_number,b.trans_id, b.prod_id, b.fabric_description_id, b.body_part_id, b.gsm, b.width,b.rack_no,b.shelf_no, b.id as dtls_id, b.receive_qnty, c.id as batch_id, c.booking_no, c.booking_without_order, d.buyer_id,null as style_ref_no, null as job_no, b.order_id, null as po_ids, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
		from  inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d
		where  a.entry_form = 7 and a.id = b.mst_id and b.batch_id = c.id  and c.batch_against = 3 $sql_cond $store_cond $sql_cond_uom and c.booking_no_id = d.id and  c.booking_without_order =1 and a.receive_basis = 5 and a.status_active  =1  and c.status_active=1 and b.trans_id <>0");
	}

	foreach ($sql_production_rcv as $val)
	{
		if($val[csf("trans_id")] != 0)
		{
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["receive_qnty"] += $val[csf("receive_qnty")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["fabric_description_id"] = $val[csf("fabric_description_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["body_part_id"] = $val[csf("body_part_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["job_no"] = $val[csf("job_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["order_id"] = $val[csf("order_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["width"] = $val[csf("width")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["rack_no"] .= $val[csf("rack_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["shelf_no"] .= $val[csf("shelf_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["store_id"] = $val[csf("store_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["batch_id"] .= $val[csf("batch_id")].",";
			$rcv_id_arr[$val[csf("id")]] = $val[csf("id")];
			$rcv_booking_no_ref[$val[csf("id")]] = $val[csf("booking_no")];

			$all_product_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];

			$job_buyer_style[$val[csf("booking_no")]]["is_approved"] = $val[csf("is_approved")];
			$job_buyer_style[$val[csf("booking_no")]]["item_category"] = $val[csf("item_category")];
			$job_buyer_style[$val[csf("booking_no")]]["fabric_source"] = $val[csf("fabric_source")];
			$job_buyer_style[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
			$job_buyer_style[$val[csf("booking_no")]]["po_ids"] .= $val[csf("po_ids")].",";
		}
		else
		{
			$production_ref_arr[$val[csf("rcv_id")]]["rcv_id"] =$val[csf("rcv_id")];
			$production_ref_arr[$val[csf("rcv_id")]]["book"] =$val[csf("booking_no")];
		}
	}

	if($booking_type==1)
	{
		$rcv_sql_order = sql_select("SELECT a.id, a.recv_number, c.id as batch_id, c.booking_no, b.receive_qnty,b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no,b.shelf_no,b.order_id, d.job_no, e.style_ref_no,d.buyer_id, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c , wo_booking_mst d ,wo_po_details_master e
			where a.id = b.mst_id and b.batch_id = c.id and c.booking_no_id = d.id and d.job_no = e.job_no and d.booking_type = 4 and c.batch_against = 3 and a.receive_basis = 9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.booking_without_order<>1
			and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom
			union all
			select a.id, a.recv_number, b.batch_id, a.booking_no, b.receive_qnty, b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id , b.rack_no, b.shelf_no, b.order_id, d.job_no, e.style_ref_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b,wo_booking_mst d ,wo_po_details_master e
			where a.id = b.mst_id and a.booking_no = d.booking_no and d.job_no = e.job_no and d.booking_type = 4 and a.item_category in (2) and a.receive_basis = 2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and d.status_active=1 and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom ");



		foreach ($rcv_sql_order as $val)
		{

			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["receive_qnty"] += $val[csf("receive_qnty")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["fabric_description_id"] = $val[csf("fabric_description_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["job_no"] = $val[csf("job_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["order_id"] = $val[csf("order_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["buyer_id"] = $val[csf("buyer_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["body_part_id"] = $val[csf("body_part_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["width"] = $val[csf("width")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["rack_no"] .= $val[csf("rack_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["shelf_no"] .= $val[csf("shelf_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["store_id"] = $val[csf("store_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["batch_id"] .= $val[csf("batch_id")].",";
			$rcv_id_arr[$val[csf("id")]] = $val[csf("id")];
			$rcv_booking_no_ref[$val[csf("id")]] = $val[csf("booking_no")];
			$all_product_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];

			$job_buyer_style[$val[csf("booking_no")]]["is_approved"] = $val[csf("is_approved")];
			$job_buyer_style[$val[csf("booking_no")]]["item_category"] = $val[csf("item_category")];
			$job_buyer_style[$val[csf("booking_no")]]["fabric_source"] = $val[csf("fabric_source")];
			$job_buyer_style[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
			$job_buyer_style[$val[csf("booking_no")]]["po_ids"] .= $val[csf("order_id")].",";
		}
	}
	else
	{
		$rcv_sql_non_order = sql_select("SELECT a.recv_number, c.id as batch_id, c.booking_no ,a.id, b.receive_qnty,b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no,b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c ,wo_non_ord_samp_booking_mst d
			where a.id = b.mst_id and b.batch_id = c.id and c.booking_no_id = d.id and c.batch_against = 3 and a.receive_basis = 9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1
			and d.status_active=1 and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom
			union all
			select a.recv_number, b.batch_id, a.booking_no ,a.id, b.receive_qnty, b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no, b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b,wo_non_ord_samp_booking_mst d
			where a.item_category in (2) and a.receive_basis = 2 and a.id = b.mst_id and a.booking_id = d.id and a.booking_without_order=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1
			and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom");

		foreach ($rcv_sql_non_order as $val)
		{

			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["receive_qnty"] += $val[csf("receive_qnty")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["fabric_description_id"] = $val[csf("fabric_description_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["buyer_id"] = $val[csf("buyer_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["body_part_id"] = $val[csf("body_part_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["width"] = $val[csf("width")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["rack_no"] .= $val[csf("rack_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["shelf_no"] .= $val[csf("shelf_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["grouping"] = $val[csf("grouping")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["store_id"] = $val[csf("store_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["batch_id"] .= $val[csf("batch_id")].",";


			$rcv_id_arr[$val[csf("id")]] = $val[csf("id")];
			$rcv_booking_no_ref[$val[csf("id")]] = $val[csf("booking_no")];
			$all_product_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];

			$job_buyer_style[$val[csf("booking_no")]]["is_approved"] = $val[csf("is_approved")];
			$job_buyer_style[$val[csf("booking_no")]]["item_category"] = $val[csf("item_category")];
			$job_buyer_style[$val[csf("booking_no")]]["fabric_source"] = $val[csf("fabric_source")];
		}

	}
	// echo "<pre>";print_r($knit_finish_rcv_arr);


	$sql_trans_in = "SELECT a.id,b.to_prod_id, a.to_order_id,c.booking_no, a.entry_form, b.transfer_qnty, b.no_of_roll, b.feb_description_id, b.buyer_id, b.to_body_part, b.gsm, b.to_rack, b.to_shelf,b.to_store, b.color_id, b.to_batch_id, c.grouping
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c
	where a.id = b.mst_id  and a.entry_form in (214,216) and a.to_order_id = c.id and b.status_active = 1 $sql_cond_in $store_cond_in"; //$trans_booking_cond
	//echo $sql_trans_in;
	$rslt_trans_in = sql_select($sql_trans_in);

	foreach($rslt_trans_in as $val)
	{
		//$trans_in_arr[$val[csf("booking_no")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];

		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["transfer_qnty"] += $val[csf("transfer_qnty")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["fabric_description_id"] = $val[csf("feb_description_id")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["buyer_id"] = $val[csf("buyer_id")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["body_part_id"] = $val[csf("to_body_part")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["gsm"] = $val[csf("gsm")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["rack_no"] .= $val[csf("to_rack")].",";
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["shelf_no"] .= $val[csf("to_shelf")].",";
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["grouping"] = $val[csf("grouping")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["store_id"] = $val[csf("to_store")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["color_id"] = $val[csf("color_id")];
		$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("to_prod_id")]]["batch_id"] .= $val[csf("to_batch_id")].",";
	}
	// echo "<pre>";print_r($knit_finish_rcv_arr);


	$sql_trans_out = sql_select("select a.id,b.from_prod_id, a.from_order_id,c.booking_no, a.entry_form, b.transfer_qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c
		where a.id = b.mst_id  and a.entry_form in (214,219) and a.from_order_id = c.id and b.status_active = 1 and a.company_id = $cbo_company_name $trans_booking_cond");

	foreach ($sql_trans_out as $val)
	{
		$trans_out_arr[$val[csf("booking_no")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];
	}

	$all_rcv_id_arr = array_filter(array_unique($rcv_id_arr));

	if(count($all_rcv_id_arr)==0)
	{
		echo "Data Not Found";die;
	}

	$rcv_return_arr = array();
	if(count($all_rcv_id_arr)>0)
	{
		$all_rcv_ids = implode(",", $all_rcv_id_arr);
		$rcvCond = $rcv_id_cond = "";

		if($db_type==2 && count($all_rcv_id_arr)>999)
		{
			$all_rcv_id_chunk=array_chunk($all_rcv_id_arr,999) ;
			foreach($all_rcv_id_chunk as $chunk_arr)
			{
				$rcvCond.=" received_id in(".implode(",",$chunk_arr).") or ";
			}

			$rcv_id_cond.=" and (".chop($rcvCond,'or ').")";

		}
		else
		{
			$rcv_id_cond=" and a.received_id in($all_rcv_ids)";
		}

		$sql_rcv_return = sql_select("select a.id, a.item_category, a.received_id,b.prod_id, b.cons_quantity, b.no_of_roll
		from inv_issue_master a, inv_transaction b
		where a.id = b.mst_id  and a.entry_form = 46 and b.status_active = 1 $rcv_id_cond ");

		foreach ($sql_rcv_return as $val)
		{
			$rcv_return_arr[$rcv_booking_no_ref[$val[csf("received_id")]]][$val[csf("prod_id")]]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_arr[$rcv_booking_no_ref[$val[csf("received_id")]]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
		}

	}


	if($booking_type==1)
	{
		$sql_issue = sql_select("select a.id as issue_id, c.booking_no, b.prod_id, b.cons_quantity, b.no_of_roll, d.buyer_id
		from  inv_issue_master a, inv_transaction b, pro_batch_create_mst c, wo_booking_mst d
		where a.id = b.mst_id and b.transaction_type = 2 and b.pi_wo_batch_no = c.id and c.booking_no_id = d.id and d.booking_type = 4
		and a.item_category = 2 and b.item_category = 2 and c.booking_without_order <>1 $sql_cond $store_cond2 $sql_cond_uom2");

	}
	else
	{
		$sql_issue = sql_select("select a.id as issue_id,c.booking_no, b.prod_id, b.cons_quantity, b.no_of_roll, d.buyer_id
		from  inv_issue_master a, inv_transaction b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d
		where a.id = b.mst_id and b.transaction_type = 2 and b.pi_wo_batch_no = c.id and c.booking_no_id = d.id
		and a.item_category = 2 and b.item_category = 2 and c.booking_without_order =1 $sql_cond $store_cond2 $sql_cond_uom2");
	}

	$issue_qnty_arr = array();

	foreach ($sql_issue as $val)
	{
		$issue_qnty_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["qnty"] += $val[csf("cons_quantity")];
		$issue_qnty_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];

		$issue_id_arr[$val[csf("issue_id")]] = $val[csf("issue_id")];
		$issue_book_ref[$val[csf("issue_id")]]["booking_no"] = $val[csf("booking_no")];
	}


	$issue_id_arr = array_filter($issue_id_arr);

	if(count($issue_id_arr)>0)
	{
		$issue_ids = implode(",", $issue_id_arr);
		$issueCond = $all_issue_id_cond = "";

		if($db_type==2 && count($issue_id_arr)>999)
		{
			$issue_id_arr_chunk=array_chunk($issue_id_arr,999) ;
			foreach($issue_id_arr_chunk as $chunk_arr)
			{
				$issueCond.=" a.issue_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_issue_id_cond.=" and (".chop($issueCond,'or ').")";

		}
		else
		{
			$all_issue_id_cond=" and a.issue_id in($issue_ids)";
		}
	}

	if(count($issue_id_arr)>0)
	{
		$sql_issue_return = sql_select("select a.id, a.issue_id, a.entry_form, b.prod_id, b.item_category, b.cons_quantity, b.no_of_roll from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.item_category= 2 and b.item_category=2 and b.transaction_type = 4 and a.status_active =1  and b.status_active =1 $all_issue_id_cond $store_cond2");
		foreach ($sql_issue_return as $val)
		{
			$issue_rtn_arr[$issue_book_ref[$val[csf("issue_id")]]["booking_no"]][$val[csf("prod_id")]]["qnty"] += $val[csf("cons_quantity")];
			$issue_rtn_arr[$issue_book_ref[$val[csf("issue_id")]]["booking_no"]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
		}
	}

	$all_booking_no = "'".implode("','", array_filter(array_unique(array_values($rcv_booking_no_ref))))."'";
		$bookCond = $all_booking_no_cond = "";
	$all_booking_no_arr=explode(",", $all_booking_no);

	if(count(array_filter(explode(",", str_replace("'", "", $all_booking_no))))>0)
	{
		if($db_type==2 && count($all_booking_no_arr)>999)
		{
			$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
			foreach($all_booking_no_arr_chunk as $chunk_arr)
			{
				$bookCond.=" a.booking_no in(".implode(",",$chunk_arr).") or ";
			}

			$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";

		}
		else
		{
			$all_booking_no_cond=" and a.booking_no in($all_booking_no)";
		}
	}


	if($booking_type ==1)
	{
		$req_qnty_sql = sql_select("SELECT a.booking_no,b.lib_yarn_count_deter_id, sum(a.grey_fab_qnty) as req_qnty
		from wo_booking_dtls a , wo_pre_cost_fabric_cost_dtls b
		where a.pre_cost_fabric_cost_dtls_id = b.id and a.booking_type = 4 and a.is_deleted = 0  $all_booking_no_cond
		group by a.booking_no ,b.lib_yarn_count_deter_id ");
	}
	else
	{
		$req_qnty_sql = sql_select("SELECT a.booking_no,a.lib_yarn_count_deter_id, sum(a.grey_fabric) as req_qnty
		from wo_non_ord_samp_booking_dtls a where a.is_deleted = 0  $all_booking_no_cond
		group by a.booking_no,a.lib_yarn_count_deter_id
		order by a.booking_no desc");
	}

	$req_qnty_arr=array();

	foreach ($req_qnty_sql as $value)
	{
		$req_qnty_arr[$value[csf("booking_no")]][$value[csf("lib_yarn_count_deter_id")]] +=  $value[csf("req_qnty")];

	}
	unset($req_qnty_sql);

	$all_product_arr =  array_filter($all_product_arr);

	if(count($all_product_arr)>0)
	{
		$all_product_ids = implode(",", $all_product_arr);
		$prodCond = $all_product_id_cond = "";

		if($db_type==2 && count($all_product_arr)>999)
		{
			$all_product_chunk_arr=array_chunk($all_product_arr,999) ;
			foreach($all_product_chunk_arr as $chunk_arr)
			{
				$prodCond.=" prod_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_product_id_cond.=" and (".chop($prodCond,'or ').")";

		}
		else
		{
			$all_product_id_cond=" and prod_id in($all_product_ids)";
		}
	}

	$days_doh=array();
	if($db_type==2)
	{
		$returnRes="select prod_id, min(transaction_date) || ',' || max(transaction_date )  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 $all_product_id_cond  group by prod_id ";
	}
	else
	{
		$returnRes="select prod_id, concat(min(transaction_date),',',max(transaction_date))  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 $all_product_id_cond group by prod_id ";
	}
	$returnRes_result= sql_select($returnRes);
	foreach($returnRes_result as $row_d)
	{
		$date_total=explode(",",$row_d[csf('trans_date')]);
		if($db_type==2)
		{
			$today= change_date_format(date("Y-m-d"),'','',1);
			$daysOnHand = datediff("d",change_date_format($date_total[1],'','',1),$today);
		}
		else
		{
			$today= change_date_format(date("Y-m-d"));
			$daysOnHand = datediff("d",change_date_format($date_total[1]),$today);
		}
		$days_doh[$row_d[csf('prod_id')]]['daysonhand']=$daysOnHand ;
	}
	$floor_room_rack_array=return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");


	ob_start();

	?>
	<div>
		<table style="width:2630px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" >
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none; font-size:14px;">
						<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table style="width:2630px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" >
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="100">Booking No D</th>
						<th rowspan="2" width="100">Job No</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="100">Order</th>
						<th rowspan="2" width="100">Style</th>
						<th rowspan="2" width="100">Body Part</th>
						<th rowspan="2" width="120">F.Construction</th>
						<th rowspan="2" width="180">F.Composition</th>
						<th rowspan="2" width="70">GSM</th>
						<th rowspan="2" width="80">Fab.Dia</th>
						<th rowspan="2" width="80">Req Qnty</th>
						<th colspan="5" width="400">Receive Details</th>
						<th colspan="5" width="400">Issue Details</th>
						<th colspan="7" width="560">Stock Details</th>
						<th rowspan="2" width="">Remarks</th>
					</tr>

					<tr>
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">Recv. Roll</th>

						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">Issue Roll</th>

						<th width="80">Stock Qty.</th>
						<th width="80">Roll Qty.</th>
						<th width="80">Rack</th>
						<th width="80">Shelf</th>
						<th width="80">DOH</th>
						<th width="80">Recv. Balance</th>
						<th width="80">Issue Balance</th>
					</tr>
				</thead>
			</table>
			<div style="width:2650px; max-height:280px; overflow-y:scroll" id="scroll_body" align="left">
				<table style="width:2630px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$composition_arr=array(); $i=1;
					$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					$data_array=sql_select($sql_deter);
					if(count($data_array)>0)
					{
						foreach( $data_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							$determinaArr[$row[csf('id')]] = $row[csf('construction')];
						}

					}

					//ksort($knit_finish_rcv_arr);
					foreach($knit_finish_rcv_arr as $booking_no =>$booking_data)
					{

						foreach ($booking_data as $prod_id => $row)
						{

							$issue_qnty = $issue_qnty_arr[$booking_no][$prod_id]["qnty"];
							$issue_roll_no = $issue_qnty_arr[$booking_no][$prod_id]["no_of_roll"];

							$issue_rtn_qnty = $issue_rtn_arr[$booking_no][$prod_id]["qnty"];
							$issue_rtn_roll_no = $issue_rtn_arr[$booking_no][$prod_id]["no_of_roll"];

							$trans_out_qnty = $trans_out_arr[$booking_no][$prod_id];
							//$trans_out_qnty = $trans_out_arr[$booking_no];
							//$trans_in_qnty = $trans_in_arr[$booking_no][$prod_id];

							$trans_in_qnty = $row["transfer_qnty"];

							$rcv_return_qnty = $rcv_return_arr[$booking_no][$prod_id]["qnty"];
							$rcv_return_roll_no = $rcv_return_arr[$booking_no][$prod_id]["no_of_roll"];

							$total_rcv_qnty = $row["receive_qnty"]+$issue_rtn_qnty+$trans_in_qnty;
							$total_issue_qnty = $issue_qnty+$rcv_return_qnty+$trans_out_qnty;

							$total_rcv_roll = $row["no_of_roll"]+$issue_rtn_roll_no;
							$total_iss_roll = $issue_roll_no+$rcv_return_roll_no;

							$stock_qnty = $total_rcv_qnty- $total_issue_qnty;
							$stock_roll_no = $total_rcv_roll- $total_iss_roll;
							//echo "<pre>";print_r($row["no_of_roll"]);
							// $rack_no = implode(",",array_filter(array_unique(explode(",", $knit_finish_rcv_arr[$booking_no][$prod_id]["rack_no"]))));
							// $shelf_no = implode(",",array_filter(explode(",", $knit_finish_rcv_arr[$booking_no][$prod_id]["shelf_no"])));

							$rack_no_id = array_filter(array_unique(explode(",", $knit_finish_rcv_arr[$booking_no][$prod_id]["rack_no"])));
							$rack_no='';
							foreach ($rack_no_id as $val)
							{
								if($val>0)
								{
									if($rack_no=='')
									{
										$rack_no=$floor_room_rack_array[$val];
									}
									else
									{
										$rack_no.=",".$floor_room_rack_array[$val];
									}
								}
							}
							// echo $rack_no;
							$shelf_no_id = array_filter(array_unique(explode(",", $knit_finish_rcv_arr[$booking_no][$prod_id]["shelf_no"])));
							$shelf_no='';
							foreach ($shelf_no_id as $val)
							{
								if($val>0)
								{
									if($shelf_no=='')
									{
										$shelf_no=$floor_room_rack_array[$val];
									}
									else
									{
										$shelf_no.=",".$floor_room_rack_array[$val];
									}
								}
							}
							// echo $shelf_no;
							$batch_id = implode(",",array_filter(array_unique(explode(",", chop($row["batch_id"],",")))));

							$req_qnty = $req_qnty_arr[$booking_no][$row["fabric_description_id"]];

							
							$receive_balance = $req_qnty-$total_rcv_qnty;
							
							
							$issue_balance = $req_qnty-$total_issue_qnty;

							if($stock_qnty > 0 || $cbo_value_with==0)
							{
								if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								$ref_no= $row["grouping"];
								?>

								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="100" title="Int.Ref No: <? echo $ref_no;?>">
										<?
										$production_po_ids = $job_buyer_style[$booking_no]["po_ids"];
										$production_po_ids = implode(",",array_filter(array_unique(explode(",",chop($production_po_ids,",")))));
										?>

										<a href="##" onClick="openpage_fabric_booking('<? echo $job_buyer_style[$booking_no]["is_approved"]."_".$job_buyer_style[$booking_no]["item_category"]."_".$booking_no."_".$job_buyer_style[$booking_no]["job_no"]."_".$production_po_ids."_".$job_buyer_style[$booking_no]["fabric_source"];?>');">
											<? echo $booking_no;?>
										</a>


										<?
										//echo $booking_no;



										?>

									</td>
									<td width="100"><? echo $row["job_no"]; ?></td>
									<td width="100"><? echo $buyer_arr[$row["buyer_id"]]; ?></td>
									<td width="100"><? echo $row["order_id"]; ?></td>
									<td width="100"><? echo $row["style_ref_no"]; ?></td>
									<td width="100"><? echo $body_part[$row["body_part_id"]]; ?></td>
									<td width="120">
										<p>
											<? 
												echo implode(",",array_unique(explode(",",$determinaArr[$row["fabric_description_id"]])));
											?>
										</p>
									</td>
									<td width="180" title="<? echo 'Prod Id='.$prod_id.'='.'fabric description id='.$row['fabric_description_id'].'='.'color Id='.$row['color_id'];?>">
										<p>
											<?
												echo implode(",",array_unique(explode(",",$composition_arr[$row["fabric_description_id"]])));
											?>
										</p>
									</td>
									<td width="70" align="center"><p><? echo $row["gsm"]; ?></p></td>
									<td width="80" align="center"><p><? echo $row["width"]; ?></p></td>
									<td width="80" align="center"><p><? echo number_format($req_qnty,2); $tot_req_qnty+=$req_qnty;?></p></td>

									<td width="80" align="right"><p><? echo number_format($row["receive_qnty"],2); $tot_recv_qnty += $row["receive_qnty"];?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_rtn_qnty,2,".",""); $tot_issue_rtn_qnty+=$issue_rtn_qnty;?></p></td>
									<td width="80" align="right"><p><? echo number_format($trans_in_qnty,2,".",""); $tot_trans_in_qnty+=$trans_in_qnty;?></p></td>
									<td width="80" align="right"><p><? echo number_format($total_rcv_qnty,2,".",""); $grand_total_rcv_qnty+=$total_rcv_qnty;?></p></td>
									<td width="80" align="right"><p><? echo $total_rcv_roll; $grand_total_rcv_roll +=$total_rcv_roll;?></p></td>

									<td width="80" align="right"><p><? echo number_format($issue_qnty,2,'.',''); $tot_issue_qnty+=$issue_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($rcv_return_qnty,2,'.',''); $tot_rcv_return_qnty+=$rcv_return_qnty;?></p></td>
									<td width="80" align="right"><p><? echo number_format($trans_out_qnty,2,'.',''); $tot_trans_out_qnty+=$trans_out_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($total_issue_qnty,2,'.',''); $grand_tot_issue+=$total_issue_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo $total_iss_roll; $grand_total_iss_roll +=$total_iss_roll;?></p></td>

									<td width="80" align="right">
										<p>
											<a href='#report_details' onClick="openmypage('<? echo $row[csf('order_id')]; ?>','<? echo $prod_id; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $row["body_part_id"]; ?>','<? echo $row["fabric_description_id"]; ?>','<? echo $row["gsm"]; ?>','<? echo $row["width"]; ?>','<? echo $row["store_id"]; ?>','<? echo $row["color_id"]; ?>','stock_popup','','<? echo $batch_id; ?>');">
												<? echo number_format($stock_qnty,2,'.','');$tot_stock_qnty+=$stock_qnty; ?>
											</a>

											<? //echo number_format($stock_qnty,2,'.',''); $tot_stock_qnty+=$stock_qnty; ?>
										</p>
									</td>
									<td width="80" align="right"><p><? echo $stock_roll_no; $grand_stock_roll_no +=$stock_roll_no;?></p></td>
									<td width="80" align="right"><? echo $rack_no;?></td>
									<td width="80" align="right"><? echo $shelf_no;?></td>
									<td width="80" align="right"><? echo $days_doh[$prod_id]['daysonhand'];?></td>
									<td width="80" align="right"><? echo number_format($receive_balance,2,".",""); $grand_receive_balance +=$receive_balance;?></td>
									<td width="80" align="right"><? echo number_format($issue_balance,2,".",""); $grand_issue_balance +=$issue_balance;?></td>
									<td width="" align="right"><? ?></td>
								</tr>
								<?
								$i++;
							}
						}
					}
					?>
				</table>
			</div>
			<table style="width:2630px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" >
				<tfoot>
					<tr>
						<th width="40">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80" align="right" id="value_req_qnty"><? echo number_format($tot_req_qnty,2);?></th>
						<th width="80" align="right" id="value_recv_qnty"><? echo $tot_recv_qnty;?></th>
						<th width="80" align="right" id="value_iss_ret_qnty"><? echo $tot_issue_rtn_qnty;?></th>
						<th width="80" align="right" id="value_trans_id_qnty"><? echo $tot_trans_in_qnty;?></th>
						<th width="80" align="right" id="value_tot_recv_qnty"><? echo number_format($grand_total_rcv_qnty,2);?></th>
						<th width="80" align="right" id="value_recv_roll"><? echo $grand_total_rcv_roll;?></th>

						<th width="80" align="right" id="value_issue_qnty"><? echo $tot_issue_qnty;?></th>
						<th width="80" align="right" id="value_recv_ret_qnty"><? echo $tot_rcv_return_qnty;?></th>
						<th width="80" align="right" id="value_trans_out_qnty"><? echo $tot_trans_out_qnty;?></th>
						<th width="80" align="right" id="value_grand_issue_qnty"><? echo number_format($grand_tot_issue,2);?></th>
						<th width="80" align="right" id="value_issue_roll"><? echo $grand_total_iss_roll;?></th>

						<th width="80" align="right" id="value_stock_qnty"><? echo $tot_stock_qnty;?></th>
						<th width="80" align="right" id="value_stock_roll"><? echo $grand_stock_roll_no;?></th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80" align="right" id="value_recv_balance"><? echo number_format($grand_receive_balance,2);?></th>
						<th width="80" align="right" id="value_issue_balance"><? echo number_format($grand_issue_balance,2);?></th>
						<th width="">&nbsp;</th>
					</tr>
				</tfoot>
			</table>

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
		echo "$html**$filename**$report_type";
		exit();
}

if($action=="generate_report_show2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$booking_type=str_replace("'","",$booking_type);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_product_id_des=str_replace("'","",$txt_product_id_des);
	$txt_product_id=str_replace("'","",$txt_product_id);
	$report_type=str_replace("'","",$report_type);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	//var_dump($booking_type);

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");

	if($db_type==0)
	{
		$select_from_date=change_date_format($from_date,'yyyy-mm-dd');
		$select_from_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$select_from_date=change_date_format($from_date,'','',1);
		$select_from_to=change_date_format($to_date,'','',1);
	}
	else
	{
		$select_from_date="";
		$select_from_to="";
	}

	$sql_cond="";
	if ($cbo_company_name!=0) $sql_cond =" and a.company_id=$cbo_company_name";
	if ($cbo_item_category_id!=0) $sql_cond.=" and a.item_category=$cbo_item_category_id";
	if ($txt_product_id_des!="") $sql_cond.=" and b.prod_id in ($txt_product_id_des)";
	if ($txt_product_id!="") $sql_cond.=" and b.prod_id in ($txt_product_id)";
	if ($cbo_uom!="")
	{
		$sql_cond_uom=" and b.uom in ($cbo_uom)";
		$sql_cond_uom2=" and b.cons_uom in ($cbo_uom)";
	}
	if($cbo_store_id!=0) $store_cond =" and a.store_id=$cbo_store_id";
	if($cbo_store_id!=0) $store_cond2 =" and b.store_id=$cbo_store_id";
	if($txt_booking_no!="") $sql_cond.=" and d.booking_no_prefix_num in ($txt_booking_no) and d.booking_no like '%-".substr($cbo_year, -2)."-%'";
	if($from_date != "" && $to_date != "") $sql_cond.=" and d.booking_date between '$select_from_date' and '$select_from_to'";

	if($txt_booking_no!="") $trans_booking_cond=" and c.booking_no_prefix_num in ($txt_booking_no) and c.booking_no like '%-".substr($cbo_year, -2)."-%'";
	if($from_date != "" && $to_date != "") $trans_booking_cond =" and c.booking_date between '$select_from_date' and '$select_from_to'";


	if($booking_type==1)
	{
		$sql_production_rcv = sql_select("select a.id as rcv_id, a.recv_number,b.trans_id, b.prod_id, b.fabric_description_id, b.body_part_id, b.gsm,b.width,b.rack_no,b.shelf_no, b.id as dtls_id, b.receive_qnty, c.booking_no, c.booking_without_order, d.buyer_id, e.style_ref_no, e.job_no, b.order_id, b.order_id as po_ids, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
		from  inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, wo_booking_mst d, wo_po_details_master e
		where  a.entry_form = 7 and a.id = b.mst_id and b.batch_id = c.id and c.batch_against = 3 $sql_cond $store_cond $sql_cond_uom and c.booking_no_id = d.id and d.booking_type = 4 and d.job_no = e.job_no and c.booking_without_order =0 and a.receive_basis = 5 and a.status_active  =1  and c.status_active=1 and b.trans_id<>0");
	}
	else
	{

		$sql_production_rcv = sql_select("select a.id as rcv_id, a.recv_number,b.trans_id, b.prod_id, b.fabric_description_id, b.body_part_id, b.gsm, b.width,b.rack_no,b.shelf_no, b.id as dtls_id, b.receive_qnty, c.booking_no, c.booking_without_order, d.buyer_id,null as style_ref_no, null as job_no, b.order_id, null as po_ids, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
		from  inv_receive_master a,pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d
		where  a.entry_form = 7 and a.id = b.mst_id and b.batch_id = c.id  and c.batch_against = 3 $sql_cond $store_cond $sql_cond_uom and c.booking_no_id = d.id and  c.booking_without_order =1 and a.receive_basis = 5 and a.status_active  =1  and c.status_active=1 and b.trans_id <>0");
	}

	foreach ($sql_production_rcv as $val)
	{
		if($val[csf("trans_id")] != 0)
		{
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["receive_qnty"] += $val[csf("receive_qnty")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["fabric_description_id"] = $val[csf("fabric_description_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["body_part_id"] = $val[csf("body_part_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["job_no"] = $val[csf("job_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["order_id"] = $val[csf("order_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["width"] = $val[csf("width")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["rack_no"] .= $val[csf("rack_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["shelf_no"] .= $val[csf("shelf_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["store_id"] = $val[csf("store_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];
			$rcv_id_arr[$val[csf("id")]] = $val[csf("id")];
			$rcv_booking_no_ref[$val[csf("id")]] = $val[csf("booking_no")];

			$all_product_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];

			$job_buyer_style[$val[csf("booking_no")]]["is_approved"] = $val[csf("is_approved")];
			$job_buyer_style[$val[csf("booking_no")]]["item_category"] = $val[csf("item_category")];
			$job_buyer_style[$val[csf("booking_no")]]["fabric_source"] = $val[csf("fabric_source")];
			$job_buyer_style[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
			$job_buyer_style[$val[csf("booking_no")]]["po_ids"] .= $val[csf("po_ids")].",";
		}
		else
		{
			$production_ref_arr[$val[csf("rcv_id")]]["rcv_id"] =$val[csf("rcv_id")];
			$production_ref_arr[$val[csf("rcv_id")]]["book"] =$val[csf("booking_no")];
		}
	}

	if($booking_type==1)
	{
		$rcv_sql_order = sql_select("select a.id, a.recv_number, c.booking_no, b.receive_qnty,b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no,b.shelf_no,b.order_id, d.job_no, e.style_ref_no,d.buyer_id, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c , wo_booking_mst d ,wo_po_details_master e
			where a.id = b.mst_id and b.batch_id = c.id and c.booking_no_id = d.id and d.job_no = e.job_no and d.booking_type = 4 and c.batch_against = 3 and a.receive_basis = 9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.booking_without_order<>1
			and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom
			union all
			select a.id, a.recv_number, a.booking_no, b.receive_qnty, b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id , b.rack_no, b.shelf_no, b.order_id, d.job_no, e.style_ref_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b,wo_booking_mst d ,wo_po_details_master e
			where a.id = b.mst_id and a.booking_no = d.booking_no and d.job_no = e.job_no and d.booking_type = 4 and a.item_category in (2) and a.receive_basis = 2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
			and d.status_active=1 and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom ");



		foreach ($rcv_sql_order as $val)
		{

			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["receive_qnty"] = $val[csf("receive_qnty")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["fabric_description_id"] = $val[csf("fabric_description_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["job_no"] = $val[csf("job_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["style_ref_no"] = $val[csf("style_ref_no")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["order_id"] = $val[csf("order_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["buyer_id"] = $val[csf("buyer_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["body_part_id"] = $val[csf("body_part_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["width"] = $val[csf("width")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["rack_no"] .= $val[csf("rack_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["shelf_no"] .= $val[csf("shelf_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["store_id"] = $val[csf("store_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];
			$rcv_id_arr[$val[csf("id")]] = $val[csf("id")];
			$rcv_booking_no_ref[$val[csf("id")]] = $val[csf("booking_no")];
			$all_product_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];

			$job_buyer_style[$val[csf("booking_no")]]["is_approved"] = $val[csf("is_approved")];
			$job_buyer_style[$val[csf("booking_no")]]["item_category"] = $val[csf("item_category")];
			$job_buyer_style[$val[csf("booking_no")]]["fabric_source"] = $val[csf("fabric_source")];
			$job_buyer_style[$val[csf("booking_no")]]["job_no"] = $val[csf("job_no")];
			$job_buyer_style[$val[csf("booking_no")]]["po_ids"] .= $val[csf("order_id")].",";
		}
	}
	else
	{
		// echo "select a.recv_number, c.booking_no ,a.id, b.receive_qnty,b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no,b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
		// from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c ,wo_non_ord_samp_booking_mst d
		// where a.id = b.mst_id and b.batch_id = c.id and c.booking_no_id = d.id and c.batch_against = 3 and a.receive_basis = 9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1
		// and d.status_active=1 and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom
		// union all
		// select a.recv_number, a.booking_no ,a.id, b.receive_qnty, b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no, b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
		// from inv_receive_master a, pro_finish_fabric_rcv_dtls b,wo_non_ord_samp_booking_mst d
		// where a.item_category in (2) and a.receive_basis = 2 and a.id = b.mst_id and a.booking_id = d.id and a.booking_without_order=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1
		// and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom";

		$rcv_sql_non_order = sql_select("select a.recv_number, c.booking_no ,a.id, b.receive_qnty,b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no,b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c ,wo_non_ord_samp_booking_mst d
			where a.id = b.mst_id and b.batch_id = c.id and c.booking_no_id = d.id and c.batch_against = 3 and a.receive_basis = 9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1
			and d.status_active=1 and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom
			union all
			select a.recv_number, a.booking_no ,a.id, b.receive_qnty, b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no, b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b,wo_non_ord_samp_booking_mst d
			where a.item_category in (2) and a.receive_basis = 2 and a.id = b.mst_id and a.booking_id = d.id and a.booking_without_order=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1
			and d.is_deleted=0 $sql_cond $store_cond $sql_cond_uom");

		foreach ($rcv_sql_non_order as $val)
		{

			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["receive_qnty"] = $val[csf("receive_qnty")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["fabric_description_id"] = $val[csf("fabric_description_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["buyer_id"] = $val[csf("buyer_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["body_part_id"] = $val[csf("body_part_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["gsm"] = $val[csf("gsm")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["width"] = $val[csf("width")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["rack_no"] .= $val[csf("rack_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["shelf_no"] .= $val[csf("shelf_no")].",";
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["grouping"] = $val[csf("grouping")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["store_id"] = $val[csf("store_id")];
			$knit_finish_rcv_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["color_id"] = $val[csf("color_id")];


			$rcv_id_arr[$val[csf("id")]] = $val[csf("id")];
			$rcv_booking_no_ref[$val[csf("id")]] = $val[csf("booking_no")];
			$all_product_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];

			$job_buyer_style[$val[csf("booking_no")]]["is_approved"] = $val[csf("is_approved")];
			$job_buyer_style[$val[csf("booking_no")]]["item_category"] = $val[csf("item_category")];
			$job_buyer_style[$val[csf("booking_no")]]["fabric_source"] = $val[csf("fabric_source")];
		}

	}

	$all_rcv_id_arr = array_filter(array_unique($rcv_id_arr));

	if(count($all_rcv_id_arr)==0)
	{
		echo "Data Not Found";die;
	}

	$rcv_return_arr = array();
	if(count($all_rcv_id_arr)>0)
	{
		$all_rcv_ids = implode(",", $all_rcv_id_arr);
		$rcvCond = $rcv_id_cond = "";

		if($db_type==2 && count($all_rcv_id_arr)>999)
		{
			$all_rcv_id_chunk=array_chunk($all_rcv_id_arr,999) ;
			foreach($all_rcv_id_chunk as $chunk_arr)
			{
				$rcvCond.=" received_id in(".implode(",",$chunk_arr).") or ";
			}

			$rcv_id_cond.=" and (".chop($rcvCond,'or ').")";

		}
		else
		{
			$rcv_id_cond=" and a.received_id in($all_rcv_ids)";
		}

		$sql_rcv_return = sql_select("select a.id, a.item_category, a.received_id,b.prod_id, b.cons_quantity, b.no_of_roll
		from inv_issue_master a, inv_transaction b
		where a.id = b.mst_id  and a.entry_form = 46 and b.status_active = 1 $rcv_id_cond ");

		foreach ($sql_rcv_return as $val)
		{
			$rcv_return_arr[$rcv_booking_no_ref[$val[csf("received_id")]]][$val[csf("prod_id")]]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_arr[$rcv_booking_no_ref[$val[csf("received_id")]]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
		}

	}


	if($booking_type==1)
	{
		$sql_issue = sql_select("select a.id as issue_id, c.booking_no, b.prod_id, b.cons_quantity, b.no_of_roll, d.buyer_id
		from  inv_issue_master a, inv_transaction b, pro_batch_create_mst c, wo_booking_mst d
		where a.id = b.mst_id and b.transaction_type = 2 and b.pi_wo_batch_no = c.id and c.booking_no_id = d.id and d.booking_type = 4
		and a.item_category = 2 and b.item_category = 2 and c.booking_without_order <>1 $sql_cond $store_cond2 $sql_cond_uom2");

	}
	else
	{
		$sql_issue = sql_select("select a.id as issue_id,c.booking_no, b.prod_id, b.cons_quantity, b.no_of_roll, d.buyer_id
		from  inv_issue_master a, inv_transaction b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d
		where a.id = b.mst_id and b.transaction_type = 2 and b.pi_wo_batch_no = c.id and c.booking_no_id = d.id
		and a.item_category = 2 and b.item_category = 2 and c.booking_without_order =1 $sql_cond $store_cond2 $sql_cond_uom2");
	}

	$issue_qnty_arr = array();

	foreach ($sql_issue as $val)
	{
		$issue_qnty_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["qnty"] += $val[csf("cons_quantity")];
		$issue_qnty_arr[$val[csf("booking_no")]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];

		$issue_id_arr[$val[csf("issue_id")]] = $val[csf("issue_id")];
		$issue_book_ref[$val[csf("issue_id")]]["booking_no"] = $val[csf("booking_no")];
	}


	$issue_id_arr = array_filter($issue_id_arr);

	if(count($issue_id_arr)>0)
	{
		$issue_ids = implode(",", $issue_id_arr);
		$issueCond = $all_issue_id_cond = "";

		if($db_type==2 && count($issue_id_arr)>999)
		{
			$issue_id_arr_chunk=array_chunk($issue_id_arr,999) ;
			foreach($issue_id_arr_chunk as $chunk_arr)
			{
				$issueCond.=" a.issue_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_issue_id_cond.=" and (".chop($issueCond,'or ').")";

		}
		else
		{
			$all_issue_id_cond=" and a.issue_id in($issue_ids)";
		}
	}

	if(count($issue_id_arr)>0)
	{
		$sql_issue_return = sql_select("select a.id, a.issue_id, a.entry_form, b.prod_id, b.item_category, b.cons_quantity, b.no_of_roll from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.item_category= 2 and b.item_category=2 and b.transaction_type = 4 and a.status_active =1  and b.status_active =1 $all_issue_id_cond $store_cond2");
		foreach ($sql_issue_return as $val)
		{
			$issue_rtn_arr[$issue_book_ref[$val[csf("issue_id")]]["booking_no"]][$val[csf("prod_id")]]["qnty"] += $val[csf("cons_quantity")];
			$issue_rtn_arr[$issue_book_ref[$val[csf("issue_id")]]["booking_no"]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
		}
	}

	$sql_trans_in = sql_select("select a.id,b.from_prod_id, a.to_order_id,c.booking_no, a.entry_form, b.transfer_qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c
		where a.id = b.mst_id  and a.entry_form in (214,216) and a.to_order_id = c.id and b.status_active = 1 and a.company_id = $cbo_company_name $trans_booking_cond ");

	foreach ($sql_trans_in as $val)
	{
		$trans_in_arr[$val[csf("booking_no")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];
	}

	$sql_trans_out = sql_select("select a.id,b.from_prod_id, a.from_order_id,c.booking_no, a.entry_form, b.transfer_qnty
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c
		where a.id = b.mst_id  and a.entry_form in (214,219) and a.from_order_id = c.id and b.status_active = 1 and a.company_id = $cbo_company_name $trans_booking_cond");

	foreach ($sql_trans_out as $val)
	{
		$trans_out_arr[$val[csf("booking_no")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];
	}


	$all_booking_no = "'".implode("','", array_filter(array_unique(array_values($rcv_booking_no_ref))))."'";
		$bookCond = $all_booking_no_cond = "";
	$all_booking_no_arr=explode(",", $all_booking_no);

	if(count(array_filter(explode(",", str_replace("'", "", $all_booking_no))))>0)
	{
		if($db_type==2 && count($all_booking_no_arr)>999)
		{
			$all_booking_no_arr_chunk=array_chunk($all_booking_no_arr,999) ;
			foreach($all_booking_no_arr_chunk as $chunk_arr)
			{
				$bookCond.=" a.booking_no in(".implode(",",$chunk_arr).") or ";
			}

			$all_booking_no_cond.=" and (".chop($bookCond,'or ').")";

		}
		else
		{
			$all_booking_no_cond=" and a.booking_no in($all_booking_no)";
		}
	}


	if($booking_type ==1)
	{
		$req_qnty_sql = sql_select("select a.booking_no,b.lib_yarn_count_deter_id, sum(a.grey_fab_qnty) as req_qnty
		from wo_booking_dtls a , wo_pre_cost_fabric_cost_dtls b
		where a.pre_cost_fabric_cost_dtls_id = b.id and a.booking_type = 4 and a.is_deleted = 0  $all_booking_no_cond
		group by a.booking_no ,b.lib_yarn_count_deter_id ");
	}
	else
	{
		$req_qnty_sql = sql_select("select a.booking_no,a.lib_yarn_count_deter_id, sum(a.grey_fabric) as req_qnty
		from wo_non_ord_samp_booking_dtls a where a.is_deleted = 0  $all_booking_no_cond
		group by a.booking_no,a.lib_yarn_count_deter_id
		order by a.booking_no desc");
	}

	$req_qnty_arr=array();

	foreach ($req_qnty_sql as $value)
	{
		$req_qnty_arr[$value[csf("booking_no")]][$value[csf("lib_yarn_count_deter_id")]] +=  $value[csf("req_qnty")];

	}
	unset($req_qnty_sql);

	$all_product_arr =  array_filter($all_product_arr);

	if(count($all_product_arr)>0)
	{
		$all_product_ids = implode(",", $all_product_arr);
		$prodCond = $all_product_id_cond = "";

		if($db_type==2 && count($all_product_arr)>999)
		{
			$all_product_chunk_arr=array_chunk($all_product_arr,999) ;
			foreach($all_product_chunk_arr as $chunk_arr)
			{
				$prodCond.=" prod_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_product_id_cond.=" and (".chop($prodCond,'or ').")";

		}
		else
		{
			$all_product_id_cond=" and prod_id in($all_product_ids)";
		}
	}

	$days_doh=array();
	if($db_type==2)
	{
		$returnRes="select prod_id, min(transaction_date) || ',' || max(transaction_date )  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 $all_product_id_cond  group by prod_id ";
	}
	else
	{
		$returnRes="select prod_id, concat(min(transaction_date),',',max(transaction_date))  as trans_date from inv_transaction where transaction_type in (1,3) and  item_category in (2,3) and status_active=1 and is_deleted=0 $all_product_id_cond group by prod_id ";
	}
	$returnRes_result= sql_select($returnRes);
	foreach($returnRes_result as $row_d)
	{
		$date_total=explode(",",$row_d[csf('trans_date')]);
		if($db_type==2)
		{
			$today= change_date_format(date("Y-m-d"),'','',1);
			$daysOnHand = datediff("d",change_date_format($date_total[1],'','',1),$today);
		}
		else
		{
			$today= change_date_format(date("Y-m-d"));
			$daysOnHand = datediff("d",change_date_format($date_total[1]),$today);
		}
		$days_doh[$row_d[csf('prod_id')]]['daysonhand']=$daysOnHand ;
	}


	$i=1;
	ob_start();

	?>
	<div>
		<table style="width:2730px" border="1" cellpadding="2" cellspacing="0" class="" id="table_header_1" >
			<thead>
				<tr class="form_caption" style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" > <? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none; font-size:14px;">
						<b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table style="width:2730px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" >
				<thead>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th rowspan="2" width="100">Booking No D</th>
						<th rowspan="2" width="100">Job No</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="100">Order</th>
						<th rowspan="2" width="100">Style</th>
						<th rowspan="2" width="100">Body Part</th>
						<th rowspan="2" width="120">F.Construction</th>
						<th rowspan="2" width="180">F.Composition</th>
						<th rowspan="2" width="100">Color</th>
						<th rowspan="2" width="70">GSM</th>
						<th rowspan="2" width="80">Fab.Dia</th>
						<th rowspan="2" width="80">Req Qnty</th>
						<th colspan="5" width="400">Receive Details</th>
						<th colspan="5" width="400">Issue Details</th>
						<th colspan="7" width="560">Stock Details</th>
						<th rowspan="2" width="">Remarks</th>
					</tr>

					<tr>
						<th width="80">Recv. Qty.</th>
						<th width="80">Issue Ret. Qty.</th>
						<th width="80">Transf. In Qty.</th>
						<th width="80">Total Recv.</th>
						<th width="80">Recv. Roll</th>

						<th width="80">Issue Qty.</th>
						<th width="80">Recv. Ret. Qty.</th>
						<th width="80">Transf. Out Qty.</th>
						<th width="80">Total Issue</th>
						<th width="80">Issue Roll</th>

						<th width="80">Stock Qty.</th>
						<th width="80">Roll Qty.</th>
						<th width="80">Rack</th>
						<th width="80">Shelf</th>
						<th width="80">DOH</th>
						<th width="80">Recv. Balance</th>
						<th width="80">Issue Balance</th>
					</tr>
				</thead>
			</table>
			<div style="width:2750px; max-height:280px; overflow-y:scroll" id="scroll_body" align="left">
				<table style="width:2730px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body2">
					<?
					$composition_arr=array(); $i=1;
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					if(count($data_array)>0)
					{
						foreach( $data_array as $row )
						{
							if(array_key_exists($row[csf('id')],$composition_arr))
							{
								$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							else
							{
								$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
							}
							$determinaArr[$row[csf('id')]] = $row[csf('construction')];
						}

					}

					//ksort($knit_finish_rcv_arr);
					foreach($knit_finish_rcv_arr as $booking_no =>$booking_data)
					{

						foreach ($booking_data as $prod_id => $row)
						{

							$issue_qnty = $issue_qnty_arr[$booking_no][$prod_id]["qnty"];
							$issue_roll_no = $issue_qnty_arr[$booking_no][$prod_id]["no_of_roll"];

							$issue_rtn_qnty = $issue_rtn_arr[$booking_no][$prod_id]["qnty"];
							$issue_rtn_roll_no = $issue_rtn_arr[$booking_no][$prod_id]["no_of_roll"];

							$trans_out_qnty = $trans_out_arr[$booking_no][$prod_id];
							$trans_in_qnty = $trans_in_arr[$booking_no][$prod_id];

							$rcv_return_qnty = $rcv_return_arr[$booking_no][$prod_id]["qnty"];
							$rcv_return_roll_no = $rcv_return_arr[$booking_no][$prod_id]["no_of_roll"];

							$total_rcv_qnty = $row["receive_qnty"]+$issue_rtn_qnty+$trans_in_qnty;
							$total_issue_qnty = $issue_qnty+$rcv_return_qnty+$trans_out_qnty;

							$total_rcv_roll = $row["no_of_roll"]+$issue_rtn_roll_no;
							$total_iss_roll = $issue_roll_no+$rcv_return_roll_no;

							$stock_qnty = $total_rcv_qnty- $total_issue_qnty;
							$stock_roll_no = $total_rcv_roll- $total_iss_roll;

							$rack_no = implode(",",array_filter(array_unique(explode(",", $knit_finish_rcv_arr[$booking_no][$prod_id]["rack_no"]))));
							$shelf_no = implode(",",array_filter(explode(",", $knit_finish_rcv_arr[$booking_no][$prod_id]["shelf_no"])));
							$req_qnty = $req_qnty_arr[$booking_no][$row["fabric_description_id"]];

							$receive_balance = $req_qnty-$total_rcv_qnty;
							$issue_balance = $req_qnty-$total_issue_qnty;

							if($stock_qnty > 0 || $cbo_value_with==0)
							{
								if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
								$ref_no= $row["grouping"];
								?>

								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="100" title="Int.Ref No: <? echo $ref_no;?>">
										<?
										$production_po_ids = $job_buyer_style[$booking_no]["po_ids"];
										$production_po_ids = implode(",",array_filter(array_unique(explode(",",chop($production_po_ids,",")))));
										?>

										<a href="##" onClick="openpage_fabric_booking('<? echo $job_buyer_style[$booking_no]["is_approved"]."_".$job_buyer_style[$booking_no]["item_category"]."_".$booking_no."_".$job_buyer_style[$booking_no]["job_no"]."_".$production_po_ids."_".$job_buyer_style[$booking_no]["fabric_source"];?>');">
											<? echo $booking_no;?>
										</a>


										<?
										//echo $booking_no;
										?>

									</td>
									<td width="100"><? echo $row["job_no"]; ?></td>
									<td width="100"><? echo $buyer_arr[$row["buyer_id"]]; ?></td>
									<td width="100"><? echo $row["order_id"]; ?></td>
									<td width="100"><? echo $row["style_ref_no"]; ?></td>
									<td width="100"><? echo $body_part[$row["body_part_id"]]; ?></td>
									<td width="120"><p><? echo $determinaArr[$row["fabric_description_id"]]; ?></p></td>
									<td width="180" title="<? echo $prod_id;?>"><p><? echo $composition_arr[$row['fabric_description_id']]; ?></p></td>
									<td width="100" align="center"><p><? echo $color_arr[$row['color_id']]; ?></p></td>
									<td width="70" align="center"><p><? echo $row["gsm"]; ?></p></td>
									<td width="80" align="center"><p><? echo $row["width"]; ?></p></td>
									<td width="80" align="center"><p><? echo number_format($req_qnty,2); $tot_req_qnty+=$req_qnty;?></p></td>

									<td width="80" align="right"><p><? echo number_format($row["receive_qnty"],2); $tot_recv_qnty += $row["receive_qnty"];?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_rtn_qnty,2,".",""); $tot_issue_rtn_qnty+=$issue_rtn_qnty;?></p></td>
									<td width="80" align="right"><p><? echo number_format($trans_in_qnty,2,".",""); $tot_trans_in_qnty+=$trans_in_qnty;?></p></td>
									<td width="80" align="right"><p><? echo number_format($total_rcv_qnty,2,".",""); $grand_total_rcv_qnty+=$total_rcv_qnty;?></p></td>
									<td width="80" align="right"><p><? echo $total_rcv_roll; $grand_total_rcv_roll +=$total_rcv_roll;?></p></td>

									<td width="80" align="right"><p><? echo number_format($issue_qnty,2,'.',''); $tot_issue_qnty+=$issue_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($rcv_return_qnty,2,'.',''); $tot_rcv_return_qnty+=$rcv_return_qnty;?></p></td>
									<td width="80" align="right"><p><? echo number_format($trans_out_qnty,2,'.',''); $tot_trans_out_qnty+=$trans_out_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($total_issue_qnty,2,'.',''); $grand_tot_issue+=$total_issue_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo $total_iss_roll; $grand_total_iss_roll +=$total_iss_roll;?></p></td>

									<td width="80" align="right">
										<p>
											<a href='#report_details' onClick="openmypage('<? echo $row[csf('order_id')]; ?>','<? echo $prod_id; ?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $row["body_part_id"]; ?>','<? echo $row["fabric_description_id"]; ?>','<? echo $row["gsm"]; ?>','<? echo $row["width"]; ?>','<? echo $row["store_id"]; ?>','<? echo $row["color_id"]; ?>','stock_popup');">
												<? echo number_format($stock_qnty,2,'.','');$tot_stock_qnty+=$stock_qnty; ?>
											</a>

											<? //echo number_format($stock_qnty,2,'.',''); $tot_stock_qnty+=$stock_qnty; ?>
										</p>
									</td>
									<td width="80" align="right"><p><? echo $stock_roll_no; $grand_stock_roll_no +=$stock_roll_no;?></p></td>
									<td width="80" align="right"><? echo $rack_no;?></td>
									<td width="80" align="right"><? echo $shelf_no;?></td>
									<td width="80" align="right"><? echo $days_doh[$prod_id]['daysonhand'];?></td>
									<td width="80" align="right"><? echo number_format($receive_balance,2,".",""); $grand_receive_balance +=$receive_balance;?></td>
									<td width="80" align="right"><? echo number_format($issue_balance,2,".",""); $grand_issue_balance +=$issue_balance;?></td>
									<td width="" align="right"><? ?></td>
								</tr>
								<?
								$i++;
							}
						}
					}
					?>
				</table>
			</div>
			<table style="width:2730px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" >
				<tfoot>
					<tr>
						<th width="40">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80" align="right" id="value_req_qnty"><? echo number_format($tot_req_qnty,2);?></th>
						<th width="80" align="right" id="value_recv_qnty"><? echo $tot_recv_qnty;?></th>
						<th width="80" align="right" id="value_iss_ret_qnty"><? echo $tot_issue_rtn_qnty;?></th>
						<th width="80" align="right" id="value_trans_id_qnty"><? echo $tot_trans_in_qnty;?></th>
						<th width="80" align="right" id="value_tot_recv_qnty"><? echo number_format($grand_total_rcv_qnty,2);?></th>
						<th width="80" align="right" id="value_recv_roll"><? echo $grand_total_rcv_roll;?></th>

						<th width="80" align="right" id="value_issue_qnty"><? echo $tot_issue_qnty;?></th>
						<th width="80" align="right" id="value_recv_ret_qnty"><? echo $tot_rcv_return_qnty;?></th>
						<th width="80" align="right" id="value_trans_out_qnty"><? echo $tot_trans_out_qnty;?></th>
						<th width="80" align="right" id="value_grand_issue_qnty"><? echo number_format($grand_tot_issue,2);?></th>
						<th width="80" align="right" id="value_issue_roll"><? echo $grand_total_iss_roll;?></th>

						<th width="80" align="right" id="value_stock_qnty"><? echo $tot_stock_qnty;?></th>
						<th width="80" align="right" id="value_stock_roll"><? echo $grand_stock_roll_no;?></th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80" align="right" id="value_recv_balance"><? echo number_format($grand_receive_balance,2);?></th>
						<th width="80" align="right" id="value_issue_balance"><? echo number_format($grand_issue_balance,2);?></th>
						<th width="">&nbsp;</th>
					</tr>
				</tfoot>
			</table>

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
		echo "$html**$filename**$report_type";
		exit();
}

if($action=="stock_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	/*$color_ex = explode("__", $color);
	$color = $color_ex[0];
	$detarmination_id = $color_ex[1];*/
	//prod_id,job,style,body_part,fabric_desc,gsm,width,store
	// echo $fabric_desc.'==';
	if($fabric_desc=="")
	{
		?>
		<fieldset style="width:570px; margin-left:3px">
			<div id="scroll_body" align="center">
				<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
					<thead>
						<tr>
							<th width="50">Sl</th>
							<th width="80">Color</th>
							<th width="100">Batch No</th>
							<th width="200">Fabric Des.</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody>
						<?
						if($store_id){
							$store_cond = " and b.store_id = $store_id";
						}else{
							$store_cond = "";
						}
						$mrr_sql = "SELECT  a.batch_no, d.product_name_details,e.color_name, sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e WHERE a.id=b.pi_wo_batch_no and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and c.color_id='$color' and c.entry_form in (7,37,66,68,14,15,18,71,46,52) $store_cond and b.prod_id = d.id and d.color = e.id GROUP BY a.batch_no, d.product_name_details,e.color_name";

						$dtlsArray=sql_select($mrr_sql);
						$i=1;
						foreach($dtlsArray as $row)
						{
							if ($i%2==0)
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $row[csf('color_name')]; ?></p></td>
								<td><p><? echo $row[csf('batch_no')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('balance')];
							$i++;
						}
						?>
					</tbody>
					<tfoot>
						<tr class="tbl_bottom">
							<td colspan="4" align="right">Total</td>
							<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	else
	{
		?>
		<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="200">Fabric Des.</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($store_id)
					{
						$store_cond = " and b.store_id = $store_id";
					}else{
						$store_cond = "";
					}
					if ($po_id) {$poCond="and c.po_breakdown_id in($po_id)"; }
					if ($body_part) {$bodyPartCond="and b.body_part_id in($body_part)"; }
					if ($batch_id) {$batch_idCond="and B.PI_WO_BATCH_NO in($batch_id)";}
					if ($gsm) {$gsmCond="and d.gsm in($gsm)"; }
					if ($width) {$widthCond="and b.width in($width)"; }
					if ($fabric_desc) {$fabric_descCond="and b.fabric_description_id in($fabric_desc)"; }

					/*$rcv_sql_non_order = sql_select("select a.recv_number, c.booking_no ,a.id, b.receive_qnty,b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no,b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c ,wo_non_ord_samp_booking_mst d
					where a.id = b.mst_id and b.batch_id = c.id and c.booking_no_id = d.id and c.batch_against = 3 and a.receive_basis = 9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1
					and d.status_active=1 and d.is_deleted=0  $store_cond $gsmCond $widthCond $bodyPartCond $fabric_descCond and b.color_id='$color'
					union all
					select a.recv_number, a.booking_no ,a.id, b.receive_qnty, b.no_of_roll, b.prod_id,b.body_part_id, b.gsm, b.width,b.fabric_description_id, b.rack_no, b.shelf_no, d.buyer_id, d.is_approved, d.item_category,d.fabric_source,d.grouping,a.store_id,b.color_id
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b,wo_non_ord_samp_booking_mst d
					where a.item_category in (2) and a.receive_basis = 2 and a.id = b.mst_id and a.booking_id = d.id and a.booking_without_order=1 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1
					and d.is_deleted=0 $store_cond and b.color_id='$color'");

					$sql_issue_return = sql_select("select a.id, a.issue_id, a.entry_form, b.prod_id, b.item_category, b.cons_quantity, b.no_of_roll from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.item_category= 2 and b.item_category=2 and b.transaction_type = 4 and a.status_active =1  and b.status_active =1 and b.store_id='$store_id'");
					foreach ($sql_issue_return as $val)
					{
						$issue_rtn_arr[$issue_book_ref[$val[csf("issue_id")]]["booking_no"]][$val[csf("prod_id")]]["qnty"] += $val[csf("cons_quantity")];
						$issue_rtn_arr[$issue_book_ref[$val[csf("issue_id")]]["booking_no"]][$val[csf("prod_id")]]["no_of_roll"] += $val[csf("no_of_roll")];
					}

					$sql_trans_in = sql_select("select a.id,b.from_prod_id, a.to_order_id,c.booking_no, a.entry_form, b.transfer_qnty
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, wo_non_ord_samp_booking_mst c
					where a.id = b.mst_id  and a.entry_form in (214,216) and a.to_order_id = c.id and b.status_active = 1 and a.company_id = $companyID ");
					foreach ($sql_trans_in as $val)
					{
						$trans_in_arr[$val[csf("booking_no")]][$val[csf("from_prod_id")]] += $val[csf("transfer_qnty")];
					}*/

					//and c.color_id='$color'
					/*$mrr_sql = "SELECT  a.batch_no, d.product_name_details,e.color_name, sum((case when c.trans_type in(1,4,5) and c.entry_form in (7,37,66,68,14,15,52) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) and c.entry_form in (18,71,14,15,46) then c.quantity else 0 end)) as balance FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c ,product_details_master d, lib_color e WHERE a.id=b.pi_wo_batch_no and b.id=c.trans_id and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and c.entry_form in (7,37,66,68,14,15,18,71,46,52) $poCond $gsmCond $store_cond and b.prod_id = d.id and d.color = e.id and d.detarmination_id=$fabric_desc $bodyPartCond GROUP BY a.batch_no, d.product_name_details,e.color_name";*/
					$mrr_sql = "SELECT a.batch_no, d.product_name_details,e.color_name,
					sum((case when b.TRANSACTION_TYPE in(1,4,5)  then b.CONS_QUANTITY else 0 end) - (case when b.TRANSACTION_TYPE in(2,3,6) then b.CONS_QUANTITY else 0 end)) as balance
					FROM pro_batch_create_mst a, inv_transaction b, product_details_master d, lib_color e
					WHERE a.id=b.pi_wo_batch_no  and b.prod_id = d.id and d.color = e.id
					and b.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $batch_idCond --and B.PI_WO_BATCH_NO in(197541)
					 $poCond $gsmCond $store_cond and d.detarmination_id=$fabric_desc GROUP BY a.batch_no, d.product_name_details,e.color_name";
					// echo $mrr_sql;die;

					$dtlsArray=sql_select($mrr_sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center"><p><? echo $row[csf('color_name')]; ?></p></td>
							<td><p><? echo $row[csf('batch_no')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('product_name_details')]; ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('balance')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('balance')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="4" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		</fieldset>
		<?
	}
	exit();
}
	?>