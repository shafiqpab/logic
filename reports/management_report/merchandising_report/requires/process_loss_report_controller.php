<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');
require_once('../../../../includes/class3/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$lib_trims_group_name = return_library_array("select id,item_name from lib_item_group", "id", "item_name");

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

if($action=="order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
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
				name += selected_name[i] + '*';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hide_order_id').val( id );
			$('#hide_order_no').val( name );
		}

    </script>

</head>

<body>
<div align="center">
	<form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:780px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
                    <th>Shipment Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
                    <input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
                    <input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Order No",2=>"Style Ref",3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'process_loss_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    	</td>
                    </tr>
                    <tr>
                        <td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
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

if($action=="create_order_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1)
		$search_field="b.po_number";
	else if($search_by==2)
		$search_field="a.style_ref_no";
	else
		$search_field="a.job_no";

	$start_date =$data[4];
	$end_date =$data[5];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),"yyyy-mm-dd")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd")."'";
		}
		else
		{
			$date_cond="and b.pub_shipment_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}

	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);

	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$sql= "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "70,70,50,70,150,180","760","220",0, $sql , "js_set_value", "id,po_number","",1,"company_name,buyer_name,0,0,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date","",'','0,0,0,0,0,0,3','',1) ;
   exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_name=str_replace("'","",$cbo_company_name);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_file_no=str_replace("'","",$txt_file_no);

	if($type==1){

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";

	if(trim($txt_file_no)!="") $file_no_cond=" and b.file_no in('$txt_file_no')"; else $file_no_cond="";
	if(trim($txt_ref_no)!="") $ref_no_cond="and b.grouping='$txt_ref_no'"; else $ref_no_cond="";

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";



	//var_dump($country_ship_qnty_arr[4516]);die;

	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_id_cond="";
	}
	else
	{
		if(str_replace("'","",$hide_order_id)!="")
		{
			$po_id=str_replace("'","",$hide_order_id);
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_id_cond="and b.po_number like '$po_number' ";
			/*if($db_type==0)
			{
				$po_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","po_number like '$po_number' and status_active=1 and is_deleted=0","po_id");
			}
			else
			{
				$po_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","po_number like '$po_number' and status_active=1 and is_deleted=0","po_id");
				echo "select LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id from wo_po_break_down po_number like '$po_number' and status_active=1 and is_deleted=0";
				echo $po_number.'DDDDDDDD';
			}
			if($po_id=="") $po_id=0;*/
		}

		//$po_id_cond="and b.id in(".$po_id.")";
	}
	//echo $po_id_cond;

	$shipping_status_cond='';
	if(str_replace("'","",$shipping_status)!=0)
	{
		$shipping_status_cond=" and b.shiping_status=$shipping_status";
	}
	$date_cond='';$date_cond2='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($cbo_date_type==1) //Ship Date
		{
			if($db_type==0)
			{
				$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
				$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			   // $dates_com="and  b.pub_shipment_date BETWEEN '$date_from' AND '$date_to'";
				$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
				$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
			}
			if($db_type==2)
			{
				$date_from=change_date_format($txt_date_from,'','',1);
				$date_to=change_date_format($txt_date_to,'','',1);
			   // $dates_com="and  b.pub_shipment_date BETWEEN '$date_from' AND '$date_to'";
				$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
				$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
			}
		}
		else
		{
			if($db_type==0)
			{
				$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
				$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
			   // $dates_com="and  b.pub_shipment_date BETWEEN '$date_from' AND '$date_to'";
				$date_cond=" and c.ex_factory_date between $txt_date_from and $txt_date_to";
				$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
				$date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
			}
			if($db_type==2)
			{
				$date_from=change_date_format($txt_date_from,'','',1);
				$date_to=change_date_format($txt_date_to,'','',1);
			 	 $date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
				$date_cond=" and c.ex_factory_date between $txt_date_from and $txt_date_to";
				$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
			}
		}
		//$date_cond=" and c.ex_factory_date between $txt_date_from and $txt_date_to";
			//$date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
	}
	if($template==1)
	{
		$booking_arr=array();
		/*if($db_type==0)
		{
			$booking_arr=return_library_array( "select po_break_down_id, group_concat(distinct(booking_no)) as booking_no from wo_booking_dtls where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "booking_no");
		}
		else
		{
			$booking_arr=return_library_array( "select po_break_down_id, LISTAGG(cast(booking_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as booking_no from wo_booking_dtls where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "booking_no");
		}*/

		/*$ex_factory_arr=return_library_array( "select po_break_down_id,
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as qnty
		 from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "qnty");*/
	if($cbo_date_type==1) //Ship Date
	{
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		//$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
		$contry_ship_sql=sql_select("select b.po_break_down_id, b.country_ship_date, sum(b.order_quantity) as order_quantity from wo_po_color_size_breakdown b where  b.status_active=1 and b.is_deleted=0 $date_country_cond  group by b.po_break_down_id,b.country_ship_date");
		$country_ship_qnty_arr=array();
		foreach($contry_ship_sql as $row)
		{
			$country_ship_qnty_arr[$row[csf("po_break_down_id")]][change_date_format($row[csf("country_ship_date")],'dd-mm-yyyy')]=$row[csf("order_quantity")];
		}
		//echo $contry_ship_sql;die;
	}
	}
	else
	{

		$contry_ship_sql=sql_select("select b.po_break_down_id, b.country_ship_date, sum(b.order_quantity) as order_quantity from wo_po_color_size_breakdown b,pro_ex_factory_mst c where  b.po_break_down_id=c.po_break_down_id and  b.status_active=1 and b.is_deleted=0 $date_country_cond group by b.po_break_down_id,b.country_ship_date");
		$country_ship_qnty_arr=array();
		foreach($contry_ship_sql as $row)
		{
			$country_ship_qnty_arr[$row[csf("po_break_down_id")]][change_date_format($row[csf("country_ship_date")],'dd-mm-yyyy')]=$row[csf("order_quantity")];
		}
	}

		if($db_type==0) $year_field="YEAR(a.insert_date) as year";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		else $year_field="";//defined Later
		//ex_factory_date
		if($cbo_date_type==1) //Ship Date
		{
		$sql="select  a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $file_no_cond $ref_no_cond $shipping_status_cond $year_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
		}
		else
		{
			 $sql="select a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $date_cond $buyer_id_cond $po_id_cond $file_no_cond $ref_no_cond $shipping_status_cond $year_cond group by a.id ,a.job_no_prefix_num, a.job_no,a.insert_date, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id,
               a.total_set_qnty, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
		}
		//echo $sql;
		$result=sql_select($sql);
		$all_job_ids="";
		foreach($result as $row )
		{
			if($all_po_ids=="") $all_po_ids=$row[csf('id')];else $all_po_ids.=",".$row[csf('id')];
			if($all_job_ids=="") $all_job_ids=$row[csf('job_id')];else $all_job_ids.=",".$row[csf('job_id')];
		}
		$poIds=chop($all_po_ids,','); $po_cond_for_in="";$po_cond_for_in2="";
		$po_ids=count(array_unique(explode(",",$all_po_ids)));
		if($db_type==2 && $po_ids>1000)
		{
			$po_cond_for_in=" and (";
			$po_cond_for_in2=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$po_cond_for_in.=" b.po_break_down_id in($ids) or";
				$po_cond_for_in2.=" po_breakdown_id in($ids) or";
			}
			$po_cond_for_in=chop($po_cond_for_in,'or ');
			$po_cond_for_in.=")";
			$po_cond_for_in2=chop($po_cond_for_in2,'or ');
			$po_cond_for_in2.=")";
		}
		else
		{
			$po_cond_for_in=" and b.po_break_down_id in($poIds)";
			$po_cond_for_in2=" and po_breakdown_id in($poIds)";

		}
		$jobIds=chop($all_job_ids,','); $job_cond_for_in="";
		$job_ids=count(array_unique(explode(",",$all_job_ids)));
		if($db_type==2 && $job_ids>1000)
		{
			$job_cond_for_in=" and (";

			$jobIdsArr=array_chunk(explode(",",$jobIds),999);
			foreach($jobIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$job_cond_for_in.=" a.job_id in($ids) or";

			}
			$job_cond_for_in=chop($job_cond_for_in,'or ');
			$job_cond_for_in.=")";

		}
		else
		{
			$job_cond_for_in=" and a.job_id in($jobIds)";

		}



	unset($contry_ship_sql);
			$sql_wo="select b.po_break_down_id, a.id, a.booking_no_prefix_num as booking_no, a.insert_date from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in group by b.po_break_down_id, a.id, a.booking_no_prefix_num, a.insert_date order by a.insert_date";
		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$year=date('Y', strtotime($woRow[csf('insert_date')]));
			$booking_arr[$woRow[csf('po_break_down_id')]].=$woRow[csf('booking_no')]." (".$year."),";
		}
		unset($resultWo);
		$yarn_used_arr=array(); $grey_produced_arr=array(); $grey_used_arr=array(); $fin_produced_arr=array(); $fin_used_arr=array();
		$dataArrayTrans=sql_select("select po_breakdown_id,
								sum(CASE WHEN entry_form ='3' and issue_purpose!=2 THEN quantity ELSE 0 END) AS yarn_issue_qnty,
								sum(CASE WHEN entry_form ='9' and issue_purpose!=2 THEN quantity ELSE 0 END) AS yarn_issue_return_qnty,
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_production,
								sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
								sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_roll_wise,
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_prodcution,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_prodcution_roll_wise,
								sum(CASE WHEN entry_form ='18' THEN quantity ELSE 0 END) AS finish_issue,
								sum(CASE WHEN entry_form ='71' THEN quantity ELSE 0 END) AS finish_issue_roll_wise,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_fin,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_fin
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,3,7,9,11,13,15,16,18,61,66,71) $po_cond_for_in2 group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$yarn_used_arr[$row[csf('po_breakdown_id')]]=$row[csf('yarn_issue_qnty')]-$row[csf('yarn_issue_return_qnty')]+$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
			$grey_produced_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_production')];
			$grey_used_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_roll_wise')]+$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			$fin_produced_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_prodcution')]+$row[csf('finish_prodcution_roll_wise')];
			$fin_used_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')]+$row[csf('transfer_in_qnty_fin')]-$row[csf('transfer_out_qnty_fin')];
		}
		unset($dataArrayTrans);
		//$fin_fab_cons_arr=return_library_array( "select job_no, fab_knit_fin_req_kg from wo_pre_cost_sum_dtls", "job_no", "fab_knit_fin_req_kg");
		//$costing_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
		$sql_precost=sql_select("select a.costing_per,a.job_no,b.fab_knit_fin_req_kg from wo_pre_cost_mst a ,wo_pre_cost_sum_dtls b where b.job_no=a.job_no and a.status_active=1 $job_cond_for_in");
		//echo "select a.costing_per,a.job_no,b.fab_knit_fin_req_kg from wo_pre_cost_mst a ,wo_pre_cost_sum_dtls b where b.job_no=a.job_no and a.status_active=1 $job_cond_for_in";
		foreach($sql_precost as $row)
		{
			$fin_fab_cons_arr[$row[csf('job_no')]]=$row[csf('fab_knit_fin_req_kg')];
			$costing_library[$row[csf('job_no')]]=$row[csf('costing_per')];
		}

		$prod_sql= "SELECT b.po_break_down_id,
					sum(CASE WHEN b.production_type ='1' THEN b.production_quantity END) AS cutting,
					sum(CASE WHEN b.production_type ='8' THEN b.production_quantity END) AS finishcompleted
					from pro_garments_production_mst b
					where b.status_active=1 and b.is_deleted=0 $po_cond_for_in group by b.po_break_down_id";


		$prodSQLresult = sql_select($prod_sql);
		$prodArr = array();
		foreach($prodSQLresult as $row)
		{
			$prodArr[$row[csf('po_break_down_id')]]['cutting']=$row[csf('cutting')];
			$prodArr[$row[csf('po_break_down_id')]]['finishcompleted']=$row[csf('finishcompleted')];
		}
		unset($prodSQLresult);

	 $ex_factory_arr=return_library_array( "select b.po_break_down_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty
		 from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 $po_cond_for_in  $date_cond2  group by b.po_break_down_id", "po_break_down_id", "qnty");

		//echo $all_po_ids.'DDDD';die;
		$i=1; $tot_yarn_used=0; $tot_grey_used=0; $tot_grey_prod=0; $tot_fin_used=0; $tot_fin_prod=0; $tot_gmts_left_over=0; $tot_grey_left_over=0; $tot_fin_left_over=0;
		$tot_effective_fab_uses=0; $tot_opportunity_lost=0;
		ob_start();
		$txt_date_from=str_replace("'","",$txt_date_from);
		$txt_date_to=str_replace("'","",$txt_date_to);
		foreach( $result as $row )
		{
			$country_ship_qnty=0;
			if($txt_date_from!="" && $txt_date_to!="")
			{
				$datedifferenc=datediff("d",$txt_date_from, $txt_date_to);
				for($m=1;$m<=$datedifferenc;$m++)
				{
					if($m==1)
					{
						$con_day=change_date_format($txt_date_from,'dd-mm-yyyy');
					}
					else
					{
						$con_day=change_date_format($con_day,'dd-mm-yyyy');
					}
					$country_ship_qnty +=$country_ship_qnty_arr[$row[csf("id")]][$con_day];
					$con_day=add_date($txt_date_from,$m);
				}
				if($country_ship_qnty==0)
				{
					$country_ship_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
				}
			}
			else
			{
				$country_ship_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
			}

			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$gmts_item='';
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}

			$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
			$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
			$booking_no=explode(",",substr($booking_arr[$row[csf('id')]],0,-1));
			$knit_proc_loss=$yarn_used_arr[$row[csf('id')]]-$grey_produced_arr[$row[csf('id')]];
			$knit_proc_loss_perc=($knit_proc_loss/$yarn_used_arr[$row[csf('id')]])*100;
			$fab_proc_loss=$yarn_used_arr[$row[csf('id')]]-$fin_produced_arr[$row[csf('id')]];
			$fab_proc_loss_perc=($fab_proc_loss/$yarn_used_arr[$row[csf('id')]])*100;
			$dye_fin_proc_loss=$grey_used_arr[$row[csf('id')]]-$fin_produced_arr[$row[csf('id')]];
			$dye_fin_proc_loss_perc=($dye_fin_proc_loss/$grey_used_arr[$row[csf('id')]])*100;

			$dzn_qnty=0;
			$costing_per_id=$costing_library[$row[csf('job_no')]];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;

			$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
			$fin_fab_cons=$fin_fab_cons_arr[$row[csf('job_no')]];
			$possible_cut=($dzn_qnty/$fin_fab_cons)*$fin_used_arr[$row[csf('id')]];
			$actual_cut=$prodArr[$row[csf('id')]]['cutting'];
			$finishcompleted=$prodArr[$row[csf('id')]]['finishcompleted'];
			$ex_factory_qnty=$ex_factory_arr[$row[csf('id')]];
			$effective_fab_uses=($fin_fab_cons/$dzn_qnty)*$finishcompleted;
			$gmts_proc_loss=$fin_used_arr[$row[csf('id')]]-$effective_fab_uses;
			$gmts_proc_loss_perc=($gmts_proc_loss/$fin_used_arr[$row[csf('id')]])*100;
			//(Knit Pros. Loss+Dye Pros. Loss+Gmts Pros. Loss)
			$tot_fab_loss=$knit_proc_loss+$dye_fin_proc_loss+$gmts_proc_loss;
			$tot_fab_loss_perc=$fab_proc_loss_perc+$gmts_proc_loss_perc;
			$gmts_left_over=$finishcompleted-$ex_factory_qnty;
			$grey_left_over=$grey_produced_arr[$row[csf('id')]]-$grey_used_arr[$row[csf('id')]];
			$fin_left_over=$fin_produced_arr[$row[csf('id')]]-$fin_used_arr[$row[csf('id')]];
			$avg_process_loss=($dye_fin_proc_loss_perc*$grey_left_over)/100;
			$opportunity_lost=(floor((($grey_left_over-$avg_process_loss+$fin_left_over)/$fin_fab_cons)*12)+$gmts_left_over)*$unit_price;

			$tot_yarn_used+=$yarn_used_arr[$row[csf('id')]];
			$tot_grey_used+=$grey_used_arr[$row[csf('id')]];
			$tot_grey_prod+=$grey_produced_arr[$row[csf('id')]];
			$tot_fin_used+=$fin_used_arr[$row[csf('id')]];
			$tot_fin_prod+=$fin_produced_arr[$row[csf('id')]];
			$tot_effective_fab_uses+=$effective_fab_uses;
			$tot_gmts_left_over+=$gmts_left_over;
			$tot_grey_left_over+=$grey_left_over;
			$tot_fin_left_over+=$fin_left_over;
			$tot_opportunity_lost+=$opportunity_lost;
			$color="";


			if(str_replace("'","",$shipping_status) !=3){
				$fin_left_over=$grey_left_over=$gmts_left_over=	0;
				$tot_fin_left_over=$tot_grey_left_over=$tot_gmts_left_over=	0;
			}

			if($possible_cut > $actual_cut)
			{
				$color="red";
			}

			$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
			$html.='<td width="40">'.$i.'</td>
					<td width="98" style="padding-left:2px"><p>'.implode(",<br>",array_unique($booking_no)).'&nbsp;</p></td>
					<td width="70"><p>'.$row[csf('job_no_prefix_num')].'</p></td>
					<td width="50" align="center"><p>'.$row[csf('year')].'</p></td>
					<td width="70"><p>'.$buyer_arr[$row[csf('buyer_name')]].'</p></td>
					<td width="110"><p>'.$row[csf('style_ref_no')].'</p></td>
					<td width="130"><p>'.$row[csf('po_number')].'</p></td>
					<td width="70"><p>'.$row[csf('file_no')].'&nbsp;</p></td>
					<td width="80"><p>'.$row[csf('grouping')].'&nbsp;</p></td>
					<td width="130"><p>'.$gmts_item.'</p></td>
					<td width="50" align="center">'.$unit_of_measurement[$row[csf('order_uom')]].'&nbsp;</td>
					<td width="88" style="padding-right:2px" align="right">'.$order_qnty_in_pcs.'</td>
					<td width="80" align="center">'.change_date_format($row[csf('pub_shipment_date')]).'&nbsp;</td>
					<td width="88" style="padding-right:2px" align="right">'.fn_number_format($country_ship_qnty,0,'','').'</td>
					<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','yarn_issue_popup'".')">'.fn_number_format($yarn_used_arr[$row[csf('id')]],2,'.','').'</a></td>
					<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','grey_produced_popup'".')">'.fn_number_format($grey_produced_arr[$row[csf('id')]],2,'.','').'</a></td>
					<td width="78" align="right" style="padding-right:2px">'.fn_number_format($knit_proc_loss_perc,2,'.','').'</td>
					<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','grey_used_popup'".')">'.fn_number_format($grey_used_arr[$row[csf('id')]],2,'.','').'</a></td>
					<td width="98" align="right" style="padding-right:2px">'.fn_number_format($fin_produced_arr[$row[csf('id')]],2,'.','').'</td>
					<td width="80" align="right">'.fn_number_format($dye_fin_proc_loss_perc,2,'.','').'</td>
					<td width="80" align="right">'.fn_number_format($fab_proc_loss_perc,2,'.','').'</td>
					<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','issue_toCut_popup'".')">'.fn_number_format($fin_used_arr[$row[csf('id')]],2,'.','').'</a></td>
					<td width="100" align="right">'.fn_number_format($possible_cut,0,'.','').'</td>
					<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($actual_cut,0,'.','').'</p></td>
					<td width="100" align="right">'.fn_number_format($finishcompleted,0,'.','').'</td>
					<td width="100" align="right">'.fn_number_format($effective_fab_uses,2,'.','').'</td>
					<td width="80" align="right">'.fn_number_format($gmts_proc_loss_perc,2,'.','').'</td>
					<td width="100" align="right">'.fn_number_format($tot_fab_loss,2,'.','').'</td>
					<td width="80" align="right">'.fn_number_format($tot_fab_loss_perc,2,'.','').'</td>
					<td width="100" align="right">
					<a href="##" onclick="generate_ex_factory_popup('.$company_name.",'".$row[csf('id')]."','ex_factory_popup'".')">'.fn_number_format($ex_factory_qnty,0,'.','').'</a>
					</td>
					<td width="100" align="right">'.fn_number_format($actual_cut-$ex_factory_qnty,0,'.','').'</td>

					<td width="60" align="right">'.fn_number_format(($ex_factory_qnty/$actual_cut)*100,0,'.','').'</td>
					<td width="100" align="right">'.fn_number_format($order_qnty_in_pcs-$ex_factory_qnty,0,'.','').'</td>
					<td width="60" align="right">'.fn_number_format(($ex_factory_qnty/$order_qnty_in_pcs)*100,0,'.','').'</td>


					<td width="100" align="right">'.fn_number_format($gmts_left_over,0,'.','').'</td>
					<td width="100" align="right">'.fn_number_format($grey_left_over,2,'.','').'</td>
					<td width="100" align="right">'.fn_number_format($fin_left_over,2,'.','').'</td>
					<td width="100" align="right">'.fn_number_format($opportunity_lost,2,'.','').'</td>
					<td><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
				</tr>';
        	$i++;
		}

		$tot_knit_proc_loss=$tot_yarn_used-$tot_grey_prod;
		$tot_knit_proc_loss_perc=($tot_knit_proc_loss/$tot_yarn_used)*100;
		$tot_dye_fin_proc_loss=$tot_grey_used-$tot_fin_prod;
		$tot_dye_fin_proc_loss_perc=($tot_dye_fin_proc_loss/$tot_grey_used)*100;
		$tot_fab_proc_loss=$tot_yarn_used-$tot_fin_prod;
		$tot_fab_proc_loss_perc=($tot_fab_proc_loss/$tot_yarn_used)*100;
		$tot_gmts_proc_loss=$tot_fin_used-$tot_effective_fab_uses;
		$tot_gmts_proc_loss_perc=($tot_gmts_proc_loss/$tot_fin_used)*100;
		$grand_tot_fab_loss=$tot_knit_proc_loss+$tot_dye_fin_proc_loss+$tot_gmts_proc_loss;
		$grand_fab_loss_perc=$tot_fab_proc_loss_perc+$tot_gmts_proc_loss_perc;
		$gmts_prod_in_kg=$tot_fin_used-$tot_effective_fab_uses;
		$bgcolor1='#FFFFFF';
		$bgcolor2='#E9F3FF';

		?>
		<div style="width:3000px">
			<fieldset style="width:100%;">
				<table width="2080">
					<tr class="form_caption">
						<td colspan="20" align="center">Process Loss Report</td>
					</tr>
					<tr class="form_caption">
						<td colspan="20" align="center"><? echo $company_arr[$company_name]; ?></td>
					</tr>
				</table>
                <b><u>Summary</u></b>
                <table id="table_header" class="rpt_table" width="530" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <th width="40">SL</th>
                        <th width="120">Particulars</th>
                        <th width="140">Fabric Loss Qty. (Kg)</th>
                        <th width="100">Loss %</th>
                    	<th>Left Over</th>
                    </thead>
                    <tbody>
                        <tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
                        	<td>1</td>
                       		<td>Knitting</td>
                           	<td align="right"><? echo fn_number_format($tot_knit_proc_loss,2); ?></td>
                           	<td align="right"><? echo fn_number_format($tot_knit_proc_loss_perc,2); ?></td>
                           	<td align="right"><? echo fn_number_format($grey_left_over,2)." (Kg)"; ?></td>
                        </tr>
                        <tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
                        	<td>2</td>
                           	<td>Dyeing + Finishing</td>
                           	<td align="right"><? echo fn_number_format($tot_dye_fin_proc_loss,2); ?></td>
                           	<td align="right"><? echo fn_number_format($tot_dye_fin_proc_loss_perc,2); ?></td>
                           	<td align="right"><? echo fn_number_format($tot_fin_left_over,2)." (Kg)"; ?></td>
                        </tr>
                        <tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('tr1st_3','<? echo $bgcolor1; ?>')" id="tr1st_3">
                        	<td>3</td>
                           	<td>Gmts. Production</td>
                           	<td align="right"><? echo fn_number_format($gmts_prod_in_kg,2); ?></td>
                           	<td align="right"><? echo fn_number_format($tot_gmts_proc_loss_perc,2); ?></td>
                           	<td align="right"><? echo fn_number_format($tot_gmts_left_over,0)." (Pcs)"; ?></td>
                        </tr>
                	</tbody>
                   	<tfoot>
                    	<th width="40"></th>
                        <th width="120" align="right"></th>
                        <th width="110" align="right"><? echo fn_number_format($tot_knit_proc_loss+$tot_dye_fin_proc_loss+$gmts_prod_in_kg,2); ?></th>
                        <th width="100" align="right"><? echo fn_number_format($tot_knit_proc_loss_perc+$tot_dye_fin_proc_loss_perc+$tot_gmts_proc_loss_perc,2); ?></th>
                    	<th>&nbsp;</th>
                    </tfoot>
                </table>
                <br>
                <b>Total Opportunity Loss (USD): <? echo fn_number_format($tot_opportunity_lost,2); ?></b>
				<table style="margin-top:10px" id="table_header_1" class="rpt_table" width="3540" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">SL</th>
						<th width="100">Fabric Booking No</th>
						<th width="70">Job No</th>
						<th width="50">Year</th>
						<th width="70">Buyer</th>
						<th width="110">Style Ref.</th>
						<th width="130">Order No</th>
                        <th width="70">File No</th>
                        <th width="80">Ref. No</th>
						<th width="130">Garments Item</th>
						<th width="50">UOM</th>
						<th width="90">Qnty (Pcs)</th>
						<th width="80">Shipment Date</th>
                        <th width="90">Contry Ship Qnty</th>
						<th width="100">Yarn Used</th>
						<th width="100">Grey Produced</th>
						<th width="80" title="((Yarn Used - Grey Produced)/Yarn Used)*100">Knit Pros. Loss %</th>
						<th width="100">Grey Used</th>
						<th width="100">Finish Fabric Produced</th>
						<th width="80" title="((Grey Used - Finish Fabric Produced)/Grey Used)*100">Dye/Fin Pros. Loss %</th>
						<th width="80" title="((Yarn Used - Finish Fabric Produced)/Yarn Used)*100">Fab. Pros. Loss%</th>
						<th width="100">Fab. Issue to Cut</th>
						<th width="100" title="(Costing Per Pcs/Finish Fab Cons)*Fab. Issue to Cut">Possible Cut Pcs</th>
						<th width="100">Actual Cut Pcs</th>
						<th width="100" title="Finish Garments Production">Fin GMT- Pcs</th>
						<th width="100" title="(Finish Fab Cons/Costing Per Pcs)*Fin GMT- Pcs">Effective Fab Uses</th>
						<th width="80" title="((Fab. Issue to Cut - Effective Fab Uses)/Fab. Issue to Cut)*100">Gmts Pros. Loss %</th>
						<th width="100" title="(Knit Pros. Loss+Dye Pros. Loss+Gmts Pros. Loss)">Total Pros. Loss (Kg)</th>
						<th width="80" title="(Fab. Pros. Loss% + Gmts Pros. Loss %)">Total Pros. Loss %</th>
						<th width="100">Ex-Fact Qty.</th>
                        <th width="100">Cut to Ship</th>

                        <th width="60">Cut to Ship Percentage</th>
                        <th width="100">Order to Ship Qty</th>
                        <th width="60">Order to Ship Percentage</th>


						<th width="100">GMT Left Over</th>
						<th width="100">Grey Left Over</th>
						<th width="100">Fin Fab Left Over</th>
						<th width="100" title="(((Grey left over-Dye process loss+Finish left over)/Finished fabric cons)*12)+Gmts left over)*Unit Price">Opportunity Loss (USD)</th>
						<th>Shipment Status</th>
					</thead>
				</table>
				<div style="width:3562px; max-height:400px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="3540" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<? echo $html; ?>
					</table>
					<table class="rpt_table" width="3540" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
						<tfoot>
							<th width="40"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="50"></th>
							<th width="70"></th>
							<th width="110"></th>
							<th width="130"></th>
                            <th width="70"></th>
                            <th width="80"></th>
							<th width="130"></th>
							<th width="50"></th>
							<th width="90" id="total_order_qnty_in_pcs" align="right"></th>
							<th width="80"></th>
                            <th width="90" id="total_country_qnty_in_pcs" align="right"></th>
							<th width="100" id="value_yarn_used" align="right"></th>
							<th width="100" id="value_grey_produced" align="right"></th>
							<th width="80" align="right"><? //echo fn_number_format($tot_knit_proc_loss_perc,2); ?></th>
							<th width="100" id="value_grey_used" align="right"></th>
							<th width="100" id="value_fin_produced" align="right"></th>
							<th width="80" align="right"><? //echo fn_number_format($tot_dye_fin_proc_loss_perc,2); ?></th>
							<th width="80" align="right"><? //echo fn_number_format($tot_fab_proc_loss_perc,2); ?></th>
							<th width="100" id="value_fin_used" align="right"></th>
							<th width="100" id="possible_cut_pcs" align="right"></th>
							<th width="100" id="actual_cut_pcs" align="right"></th>
							<th width="100" id="fin_gmts_pcs" align="right"></th>
							<th width="100" id="value_effec_fab_uses" align="right"></th>
							<th width="80" align="right"><? //echo fn_number_format($tot_gmts_proc_loss_perc,2); ?></th>
							<th width="100" id="value_tot_fab_loss" align="right"></th>
							<th width="80" align="right"><? //echo fn_number_format($grand_fab_loss_perc,2); ?></th>
							<th width="100" id="ex_factory" align="right"></th>
                            <th width="100" id="td_cut_to_ship" align="right"></th>

                            <th width="60"></th>
                            <th width="100" id="td_order_to_ship"></th>
                            <th width="60"></th>

							<th width="100" id="gmts_left_over" align="right"></th>
							<th width="100" id="value_grey_left_over" align="right"></th>
							<th width="100" id="value_fin_left_over" align="right"></th>
							<th width="100" id="value_opportunity_loss" align="right"></th>
							<th></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
		</div>
<?
	}

	// foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	// {
	// 	if( @filemtime($filename) < (time()-$seconds_old) )
	// 	@unlink($filename);
	// }
}
	//---------end------------//
	// ------------For Show 2 Button-Arnab----------//
	if($type==2){
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		}

		$txt_job_no=str_replace("'","",$txt_job_no);
		if(trim($txt_job_no)!="") $job_no=trim($txt_job_no); else $job_no="%%";

		if(trim($txt_file_no)!="") $file_no_cond=" and b.file_no in('$txt_file_no')"; else $file_no_cond="";
		if(trim($txt_ref_no)!="") $ref_no_cond="and b.grouping='$txt_ref_no'"; else $ref_no_cond="";

		$cbo_year=str_replace("'","",$cbo_year);
		if(trim($cbo_year)!=0)
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond="";
		}
		else $year_cond="";



		//var_dump($country_ship_qnty_arr[4516]);die;

		if(str_replace("'","",trim($txt_order_no))=="")
		{
			$po_id_cond="";
		}
		else
		{
			if(str_replace("'","",$hide_order_id)!="")
			{
				$po_id=str_replace("'","",$hide_order_id);
			}
			else
			{
				$po_number=trim(str_replace("'","",$txt_order_no))."%";
				$po_id_cond="and b.po_number like '$po_number' ";
				/*if($db_type==0)
				{
					$po_id=return_field_value("group_concat(id) as po_id","wo_po_break_down","po_number like '$po_number' and status_active=1 and is_deleted=0","po_id");
				}
				else
				{
					$po_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","po_number like '$po_number' and status_active=1 and is_deleted=0","po_id");
					echo "select LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id from wo_po_break_down po_number like '$po_number' and status_active=1 and is_deleted=0";
					echo $po_number.'DDDDDDDD';
				}
				if($po_id=="") $po_id=0;*/
			}

			//$po_id_cond="and b.id in(".$po_id.")";
		}
		//echo $po_id_cond;

		$shipping_status_cond='';
		if(str_replace("'","",$shipping_status)!=0)
		{
			$shipping_status_cond=" and b.shiping_status=$shipping_status";
		}
		$date_cond='';$date_cond2='';
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($cbo_date_type==1) //Ship Date
			{
				if($db_type==0)
				{
					$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
					$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
				   // $dates_com="and  b.pub_shipment_date BETWEEN '$date_from' AND '$date_to'";
					$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
					$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
				}
				if($db_type==2)
				{
					$date_from=change_date_format($txt_date_from,'','',1);
					$date_to=change_date_format($txt_date_to,'','',1);
				   // $dates_com="and  b.pub_shipment_date BETWEEN '$date_from' AND '$date_to'";
					$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
					$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
				}
			}
			else
			{
				if($db_type==0)
				{
					$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
					$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
				   // $dates_com="and  b.pub_shipment_date BETWEEN '$date_from' AND '$date_to'";
					$date_cond=" and c.ex_factory_date between $txt_date_from and $txt_date_to";
					$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
					$date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
				}
				if($db_type==2)
				{
					$date_from=change_date_format($txt_date_from,'','',1);
					$date_to=change_date_format($txt_date_to,'','',1);
					  $date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
					$date_cond=" and c.ex_factory_date between $txt_date_from and $txt_date_to";
					$date_country_cond=" and b.country_ship_date between $txt_date_from and $txt_date_to";
				}
			}
			//$date_cond=" and c.ex_factory_date between $txt_date_from and $txt_date_to";
				//$date_cond2=" and b.ex_factory_date between $txt_date_from and $txt_date_to";
		}
		if($template==1)
		{
			$booking_arr=array();
			/*if($db_type==0)
			{
				$booking_arr=return_library_array( "select po_break_down_id, group_concat(distinct(booking_no)) as booking_no from wo_booking_dtls where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "booking_no");
			}
			else
			{
				$booking_arr=return_library_array( "select po_break_down_id, LISTAGG(cast(booking_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as booking_no from wo_booking_dtls where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "booking_no");
			}*/

			/*$ex_factory_arr=return_library_array( "select po_break_down_id,
			sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as qnty
			 from pro_ex_factory_mst where status_active=1 and is_deleted=0 group by po_break_down_id", "po_break_down_id", "qnty");*/
		if($cbo_date_type==1) //Ship Date
		{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			//$date_cond=" and b.pub_shipment_date between $txt_date_from and $txt_date_to";
			$contry_ship_sql=sql_select("select b.po_break_down_id, b.country_ship_date, sum(b.order_quantity) as order_quantity from wo_po_color_size_breakdown b where  b.status_active=1 and b.is_deleted=0 $date_country_cond  group by b.po_break_down_id,b.country_ship_date");
			$country_ship_qnty_arr=array();
			foreach($contry_ship_sql as $row)
			{
				$country_ship_qnty_arr[$row[csf("po_break_down_id")]][change_date_format($row[csf("country_ship_date")],'dd-mm-yyyy')]=$row[csf("order_quantity")];
			}
			//echo $contry_ship_sql;die;
		}
		}
		else
		{

			$contry_ship_sql=sql_select("select b.po_break_down_id, b.country_ship_date, sum(b.order_quantity) as order_quantity from wo_po_color_size_breakdown b,pro_ex_factory_mst c where  b.po_break_down_id=c.po_break_down_id and  b.status_active=1 and b.is_deleted=0 $date_country_cond group by b.po_break_down_id,b.country_ship_date");
			$country_ship_qnty_arr=array();
			foreach($contry_ship_sql as $row)
			{
				$country_ship_qnty_arr[$row[csf("po_break_down_id")]][change_date_format($row[csf("country_ship_date")],'dd-mm-yyyy')]=$row[csf("order_quantity")];
			}
		}

			if($db_type==0) $year_field="YEAR(a.insert_date) as year";
			else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
			else $year_field="";//defined Later
			//ex_factory_date
			if($cbo_date_type==1) //Ship Date
			{
				$sql="select  a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number,b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $po_id_cond $file_no_cond $ref_no_cond $shipping_status_cond $year_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
			}
			else
			{
				 $sql="select a.id as job_id,a.job_no_prefix_num, a.job_no, $year_field, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where a.id=b.job_id and b.id=c.po_break_down_id and a.company_name='$company_name' and a.job_no_prefix_num like '$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0  $date_cond $buyer_id_cond $po_id_cond $file_no_cond $ref_no_cond $shipping_status_cond $year_cond group by a.id ,a.job_no_prefix_num, a.job_no,a.insert_date, a.company_name, a.buyer_name, a.style_ref_no, a.order_uom, a.gmts_item_id,
	             a.total_set_qnty, b.id, b.po_number,b.grouping,b.file_no, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price, b.shiping_status order by b.pub_shipment_date, a.job_no_prefix_num, b.id";
			}
			//  echo $sql;
			$result=sql_select($sql);
			$all_job_ids="";
			foreach($result as $row )
			{
				if($all_po_ids=="") $all_po_ids=$row[csf('id')];else $all_po_ids.=",".$row[csf('id')];
				if($all_job_ids=="") $all_job_ids=$row[csf('job_id')];else $all_job_ids.=",".$row[csf('job_id')];
			}
			$poIds=chop($all_po_ids,','); $po_cond_for_in="";$po_cond_for_in2="";
			$po_ids=count(array_unique(explode(",",$all_po_ids)));

			// ================================= get grey and finish req qty ==========================
			$condition= new condition();
			$condition->po_id_in($poIds);

			$condition->company_name("=$company_name");
			if(str_replace("'","",$cbo_buyer_name)>0){
				$condition->buyer_name("=$cbo_buyer_name");

			}
			$condition->init();

			$fabric= new fabric($condition);
			//  echo $fabric->getQuery(); die;
			$fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			//  print_r($fabric_costing_arr);
			$yarn= new yarn($condition);
			//  echo $yarn->getQuery(); die;
			$yarn_des_data=$yarn->getOrderWiseYarnQtyArray();
            //   print_r($yarn_des_data);
			// $yarn_descrip_data=$yarn_des_data[$poIds];
		//   echo '<pre>';print_r($yarn_descrip_data);

			if($db_type==2 && $po_ids>1000)
			{
				$po_cond_for_in=" and (";
				$po_cond_for_in2=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_cond_for_in.=" b.po_break_down_id in($ids) or";
					$po_cond_for_in2.=" po_breakdown_id in($ids) or";
				}
				$po_cond_for_in=chop($po_cond_for_in,'or ');
				$po_cond_for_in.=")";
				$po_cond_for_in2=chop($po_cond_for_in2,'or ');
				$po_cond_for_in2.=")";
			}
			else
			{
				$po_cond_for_in=" and b.po_break_down_id in($poIds)";
				$po_cond_for_in2=" and po_breakdown_id in($poIds)";

			}
			$jobIds=chop($all_job_ids,','); $job_cond_for_in="";
			$job_ids=count(array_unique(explode(",",$all_job_ids)));
			if($db_type==2 && $job_ids>1000)
			{
				$job_cond_for_in=" and (";

				$jobIdsArr=array_chunk(explode(",",$jobIds),999);
				foreach($jobIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_cond_for_in.=" a.job_id in($ids) or";

				}
				$job_cond_for_in=chop($job_cond_for_in,'or ');
				$job_cond_for_in.=")";

			}
			else
			{
				$job_cond_for_in=" and a.job_id in($jobIds)";

			}

			// ============================== data store to gbl table ==================================
			/* $con = connect();
			execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and ref_from in (1,2) and ENTRY_FORM=20");
			oci_commit($con); */

		unset($contry_ship_sql);
			$sql_wo="select b.po_break_down_id, a.id, a.booking_no_prefix_num as booking_no, a.insert_date from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in  order by a.insert_date";

			//  echo $sql_wo;

			$resultWo=sql_select($sql_wo);
			foreach($resultWo as $woRow)
			{
				$year=date('Y', strtotime($woRow[csf('insert_date')]));
				$booking_arr[$woRow[csf('po_break_down_id')]].=$woRow[csf('booking_no')]." (".$year."),";


			}
			// print_r($grey_fab_arr);
			unset($resultWo);
			//===================== Query For the Accessories Status================/
					$sql = "SELECT a.job_no,b.trim_group,b.CONS_UOM
					from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
					where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.status_active=1 and c.status_active=1 and  b.status_active=1 and c.cons>0    $job_cond_for_in group by a.job_no,b.trim_group,b.CONS_UOM";
	                //  echo $sql;
						$res = sql_select($sql);
						$trims_data_array = array();
						foreach ($res as $v)
						{
							// $trims_data_array[$v['TRIM_GROUP']]['req_qty'] += $trim_qty_arr[$v['JOB_NO']][$v['TRIM_GROUP']];
							$trims_data_array[$v['TRIM_GROUP']]['uom'] = $v['CONS_UOM'];
							$trims_data_array[$v['TRIM_GROUP']]['req_qty'] = $v['TRIM_GROUP'];
						}
						// print_r($trims_data_array);
				/*======================================================================================/
				/                                    trims booking qty                                  /
				/======================================================================================*/

				// $sql = "SELECT b.trim_group as TRIM_GROUP, sum(case when e.is_short=2 then c.requirment else 0 end) as QTY, sum(case when e.is_short=1 then c.requirment else 0 end) as SHOR_QTY FROM wo_po_break_down a,wo_booking_dtls b,wo_trim_book_con_dtls c,wo_po_details_master d,wo_booking_mst e WHERE e.booking_no=b.booking_no and b.booking_no=c.booking_no and b.id=c.wo_trim_booking_dtls_id and d.id=a.job_id and c.po_break_down_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.entry_form in(252)  and e.item_category=4 and e.booking_type=2 $job_cond_for_in group by b.trim_group ";
				// //   echo $sql;
				// $trimsRes = sql_select($sql);
				// $tbArray = array();
				// $trims_wo_array = array();
				// foreach ($trimsRes as  $v)
				// {
				// 	$trims_wo_array[$v['TRIM_GROUP']]['book_qty'] += $v['QTY'];
				// 	$trims_wo_array[$v['TRIM_GROUP']]['short_book_qty'] += $v['SHOR_QTY'];
				// }
				//===============================wo_qty=========================//
				$sqlwoqty="Select b.trim_group, b.wo_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in ";
				// echo $sqlwoqty;

				 $mainsqlwoqty=sql_select($sqlwoqty);
				 $trims_wo_array = array();
				 foreach ($mainsqlwoqty as  $v)
				 {
				 	$trims_wo_array[$v['TRIM_GROUP']]['wo_qnty'] += $v['WO_QNTY'];

				 }
				 //===============================Grey Fab qnty=========================//
				$fabsqlqty="Select b.po_break_down_id, b.grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond_for_in ";
				// echo $fabsqlqty;

				 $greyfabsqlqty=sql_select($fabsqlqty);
				 $grey_fab_arr = array();
				 foreach ($greyfabsqlqty as  $v)
				 {
				 	$grey_fab_arr[$v[csf('po_break_down_id')]]['grey_fab_qnty'] += $v['GREY_FAB_QNTY'];

				 }
				//  print_r($grey_fab_arr);


				/*=========================================End here=================================================*/
				/*=======================================TRIM Receive Qty======================================= */
				$sqlacc = "SELECT  B.ITEM_GROUP_ID as TRIM_GROUP,c.quantity AS QNTY,c.reject_qty as REJ_QTY FROM  wo_po_break_down a,inv_trims_entry_dtls b,order_wise_pro_details c,wo_po_details_master d,inv_receive_master e where e.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.entry_form=24 AND c.entry_form = 24 AND c.entry_form = 24 and e.item_category=4 and d.id=a.job_id and c.po_breakdown_id=a.id $job_cond_for_in";
				// echo $sqlacc;die();
				$accRes = sql_select($sqlacc);
				$trimsDataArray = array();
				foreach ($accRes as $v)
				{
					$trimsDataArray[$v['TRIM_GROUP']]['rcv_qty'] += $v['QNTY'];
					$trimsDataArray[$v['TRIM_GROUP']]['rej_qty'] += $v['REJ_QTY'];
				}
					/*======================================================================================/
					/                               trims issue and rcv return                              /
					/======================================================================================*/
					$sqlacc = "SELECT e.entry_form, B.ITEM_GROUP_ID as TRIM_GROUP,C.QUANTITY AS QNTY FROM wo_po_break_down a,inv_trims_issue_dtls b,order_wise_pro_details c,wo_po_details_master d,inv_issue_master e where e.id=b.mst_id and b.id=c.dtls_id and c.trans_type in(2,3) and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.entry_form in(25,49) AND c.entry_form in(25,49) AND c.entry_form in(25,49) and e.item_category=4 and d.id=a.job_id and c.po_breakdown_id=a.id  $job_cond_for_in";
					//  echo $sqlacc;die();
					$accRes = sql_select($sqlacc);
					foreach ($accRes as $v)
					{
						if($v['ENTRY_FORM']==49)
						{
							$trimsDataArray[$v['TRIM_GROUP']]['rcv_rtn_qty'] += $v['QNTY'];
						}
						if($v['ENTRY_FORM']==25)
						{
							$trimsDataArray[$v['TRIM_GROUP']]['issue_qty'] += $v['QNTY'];
						}
					}
						$yarn_used_arr=array(); $grey_produced_arr=array(); $grey_used_arr=array(); $fin_produced_arr=array(); $fin_used_arr=array();$rcv_quantity_arr=array();$knit_product_arr=array();
						$dataArrayTrans=sql_select("SELECT po_breakdown_id,
									sum(CASE WHEN entry_form ='3' and issue_purpose!=2 THEN quantity ELSE 0 END) AS yarn_issue_qnty,
									sum(CASE WHEN entry_form ='9' and issue_purpose!=2 THEN quantity ELSE 0 END) AS yarn_issue_return_qnty,
									sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_production,
									sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
									sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_roll_wise,
									sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_prodcution,
									sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS rcv_quantity,
									sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_prodcution_roll_wise,
									sum(CASE WHEN entry_form ='18' THEN quantity ELSE 0 END) AS finish_issue,
									sum(CASE WHEN entry_form ='71' THEN quantity ELSE 0 END) AS finish_issue_roll_wise,
									sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
									sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
									sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
									sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
									sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_fin,
									sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_fin
									from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,3,7,9,11,13,15,16,18,37,61,66,71) $po_cond_for_in2 group by po_breakdown_id");

			foreach($dataArrayTrans as $row)
			{
					$yarn_used_arr[$row[csf('po_breakdown_id')]]=$row[csf('yarn_issue_qnty')]-$row[csf('yarn_issue_return_qnty')]+$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
					$grey_produced_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_production')];
					$grey_used_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_roll_wise')]+$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
					$fin_produced_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_prodcution')]+$row[csf('finish_prodcution_roll_wise')];
					$fin_used_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')]+$row[csf('transfer_in_qnty_fin')]-$row[csf('transfer_out_qnty_fin')];
					$rcv_quantity_arr[$row[csf('po_breakdown_id')]]=$row[csf('rcv_quantity')];

			}


			unset($dataArrayTrans);
			//$fin_fab_cons_arr=return_library_array( "select job_no, fab_knit_fin_req_kg from wo_pre_cost_sum_dtls", "job_no", "fab_knit_fin_req_kg");
			//$costing_library=return_library_array( "select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per"  );
			$sql_precost=sql_select("select a.costing_per,a.job_no,b.fab_knit_fin_req_kg from wo_pre_cost_mst a ,wo_pre_cost_sum_dtls b where b.job_no=a.job_no and a.status_active=1 $job_cond_for_in");
			//echo "select a.costing_per,a.job_no,b.fab_knit_fin_req_kg from wo_pre_cost_mst a ,wo_pre_cost_sum_dtls b where b.job_no=a.job_no and a.status_active=1 $job_cond_for_in";
			foreach($sql_precost as $row)
			{
				$fin_fab_cons_arr[$row[csf('job_no')]]=$row[csf('fab_knit_fin_req_kg')];
				$costing_library[$row[csf('job_no')]]=$row[csf('costing_per')];
			}




		   $print_embro_sql= "SELECT b.po_break_down_id,
		                     (CASE WHEN b.production_type ='1' THEN a.production_qnty END) AS cutting,
		        			 (CASE WHEN b.production_type ='8' THEN a.production_qnty END)  AS finishcompleted,
		    	    		 (CASE WHEN b.production_type ='4' THEN a.production_qnty END) AS sewinginput,
		        			 (CASE WHEN b.production_type ='5' THEN a.production_qnty END) AS sewingoutput,
			                 (CASE WHEN b.production_type='2' and b.embel_name='3' THEN a.production_qnty ELSE 0 END) AS wash_issue_qnty,
			                 (CASE WHEN a.production_type ='3' and b.embel_name='3' THEN a.production_qnty ELSE 0 END) AS wash_rcv_qty
					         from pro_garments_production_dtls a, pro_garments_production_mst b
						     where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.production_type in(1,8,4,5,2,3) $po_cond_for_in
					         ";
					    //  echo $print_embro_sql;die();
			    $prodEmbrosql=sql_select($print_embro_sql);
			    $embroarr=array();$prodArr = array();
			    foreach($prodEmbrosql as $row){
						$embroarr[$row[csf('po_break_down_id')]]['wash_issue_qnty']+=$row['wash_issue_qnty'];
						$embroarr[$row[csf('po_break_down_id')]]['wash_rcv_qnty']+=$row['wash_rcv_qnty'];
						$prodArr[$row[csf('po_break_down_id')]]['cutting']+=$row[csf('cutting')];
						$prodArr[$row[csf('po_break_down_id')]]['finishcompleted']+=$row[csf('finishcompleted')];
						$prodArr[$row[csf('po_break_down_id')]]['sewinginput']+=$row[csf('sewinginput')];
						$prodArr[$row[csf('po_break_down_id')]]['sewingoutput']+=$row[csf('sewingoutput')];

			   }



				$ex_factory_arr=return_library_array( "select b.po_break_down_id,
					sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as qnty
					from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 $po_cond_for_in  $date_cond2  group by b.po_break_down_id", "po_break_down_id", "qnty");

			//  ============================Leftover Query Start From Here ======================//

			    $sql = "SELECT b.po_break_down_id ,b.TOTAL_LEFT_OVER_RECEIVE as qty from PRO_LEFTOVER_GMTS_RCV_MST a, PRO_LEFTOVER_GMTS_RCV_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.production_type=1 $po_cond_for_in";
	        //  echo $sql; die();
          	     $res = sql_select($sql);
	             $leftover_data_array = array();
	            foreach ($res as $v)
	            {
		     	    $leftover_data_array[$v[csf('po_break_down_id')]]['qty'] += $v[csf('qty')];
	            }
            //  =============================Gate Entry Start from here======================//
                 $gatepasssql="SELECT b.po_break_down_id,c.sample_id,c.quantity from inv_gate_pass_mst a,inv_gate_pass_dtls c,
				 pro_ex_factory_mst b where a.id=c.mst_id and c.buyer_order_id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.sample_id >0 $po_cond_for_in";
				//  echo $gatepasssql;
				$gatepassres = sql_select($gatepasssql);
				$gatepassarr=array();
				foreach ($gatepassres as $value)
	            {
		     	    $gatepassarr[$value[csf('po_break_down_id')]]['quantity'] += $value[csf('quantity')];
	            }






			//echo $all_po_ids.'DDDD';die;
			$i=1; $tot_yarn_used=0; $tot_grey_used=0; $tot_grey_prod=0; $tot_fin_used=0; $tot_fin_prod=0; $tot_gmts_left_over=0; $tot_grey_left_over=0; $tot_fin_left_over=0;
			$tot_effective_fab_uses=0; $tot_opportunity_lost=0;
			ob_start();
			$txt_date_from=str_replace("'","",$txt_date_from);
			$txt_date_to=str_replace("'","",$txt_date_to);
			foreach( $result as $row )
			{
				$country_ship_qnty=0;
				if($txt_date_from!="" && $txt_date_to!="")
				{
					$datedifferenc=datediff("d",$txt_date_from, $txt_date_to);
					for($m=1;$m<=$datedifferenc;$m++)
					{
						if($m==1)
						{
							$con_day=change_date_format($txt_date_from,'dd-mm-yyyy');
						}
						else
						{
							$con_day=change_date_format($con_day,'dd-mm-yyyy');
						}
						$country_ship_qnty +=$country_ship_qnty_arr[$row[csf("id")]][$con_day];
						$con_day=add_date($txt_date_from,$m);
					}
					if($country_ship_qnty==0)
					{
						$country_ship_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
					}
				}
				else
				{
					$country_ship_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
				}

				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$gmts_item='';
				$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
				foreach($gmts_item_id as $item_id)
				{
					if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
				}

				$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
				$plan_cut_qnty=$row[csf('plan_cut')];
			    $grey_fab_qnty=$grey_fab_arr[$row[csf('id')]]['grey_fab_qnty'];
				$gr_grey_fab_qnty+=$grey_fab_qnty;
				$totplancutqnty+=$plan_cut_qnty;
				$grey_cons=$fabric_costing_arr['knit']['grey'][$row['ID']]/$plan_cut_qnty;
				// echo $poIds;die();
				$fin_cons=$fabric_costing_arr['knit']['finish'][$row['ID']]/$plan_cut_qnty;
			    $total_receive_qnty=$row[csf('id')];
				$total_required_qty=$yarn_des_data[$row[csf('id')]];
				$grtotal+=$total_required_qty;

				$yarn_issue_bal=$grey_fab_qnty-$yarn_used_arr[$row[csf('id')]];

				$totgreycons+=$grey_cons;
				$totfincons+=$fin_cons;
				$unit_price=$row[csf('unit_price')]/$row[csf('ratio')];
				$booking_no=explode(",",substr($booking_arr[$row[csf('id')]],0,-1));
				$knit_proc_loss=$yarn_used_arr[$row[csf('id')]]-$grey_produced_arr[$row[csf('id')]];
				$kniting_balance=$yarn_used_arr[$row[csf('id')]]-$grey_produced_arr[$row[csf('id')]];
				$knit_proc_loss_perc=($knit_proc_loss/$yarn_used_arr[$row[csf('id')]])*100;
				$fab_proc_loss=$yarn_used_arr[$row[csf('id')]]-$fin_produced_arr[$row[csf('id')]];
				$fab_proc_loss_perc=($fab_proc_loss/$yarn_used_arr[$row[csf('id')]])*100;
				$dye_fin_proc_loss=$grey_used_arr[$row[csf('id')]]-$fin_produced_arr[$row[csf('id')]];
				$dye_fin_proc_loss_perc=($dye_fin_proc_loss/$grey_used_arr[$row[csf('id')]])*100;
			     $grey_stock_in_hand=$grey_produced_arr[$row[csf('id')]]-$grey_used_arr[$row[csf('id')]];

				$dzn_qnty=0;
				$costing_per_id=$costing_library[$row[csf('job_no')]];
				if($costing_per_id==1) $dzn_qnty=12;
				else if($costing_per_id==3) $dzn_qnty=12*2;
				else if($costing_per_id==4) $dzn_qnty=12*3;
				else if($costing_per_id==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;

				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
				$fin_fab_cons=$fin_fab_cons_arr[$row[csf('job_no')]];
				$possible_cut=($dzn_qnty/$fin_fab_cons)*$fin_used_arr[$row[csf('id')]];
				$actual_cut=$prodArr[$row[csf('id')]]['cutting'];
				$tot_actual_cut+=$prodArr[$row[csf('id')]]['cutting'];
				$finishcompleted=$prodArr[$row[csf('id')]]['finishcompleted'];
				$sewinginput=$prodArr[$row[csf('id')]]['sewinginput'];
				$sewingoutput=$prodArr[$row[csf('id')]]['sewingoutput'];
				$totalsewinginput+=$prodArr[$row[csf('id')]]['sewinginput'];
				$totalsewingoutput+=$prodArr[$row[csf('id')]]['sewingoutput'];
				$washsent=$embroarr[$row[csf('id')]]['wash_issue_qnty'];
				$washrcv=$embroarr[$row[csf('id')]]['wash_rcv_qnty'];
				$leftover=$leftover_data_array[$row[csf('id')]]['qty'];

				$washbalance=$prodArr[$row[csf('id')]]['sewingoutput']-$embroarr[$row[csf('id')]]['wash_issue_qnty'];
				$gatepassresult= $gatepassarr[$row[csf('id')]]['quantity'];
				$totgatepassresult+=$gatepassresult;
				$washrcvbalance=$washsent-$washrcv;
				$inputbalance=$actual_cut-$sewinginput;
				$outputbalance=$sewinginput-$sewingoutput;
				$totaloutputbalance+=$outputbalance;
				$totinputbalance+=$inputbalance;
				$totalwashsent+=$washsent;
				$totalwashrcv+=$washrcv;
				$totalwashbalance+=$washbalance;
				$totalleftover+=$leftover;
				$totalwashrcvbalance+=$washrcvbalance;
				$fingmtbalance=$sewingoutput-$finishcompleted;
				$totgmtbalance+=$fingmtbalance;
				$ex_factory_qnty=$ex_factory_arr[$row[csf('id')]];
				$ex_factory_balance=$order_qnty_in_pcs-$ex_factory_qnty;
				$totorderinpcs+=$order_qnty_in_pcs;
				$totexfactorybalance+=$ex_factory_balance;
				$effective_fab_uses=($fin_fab_cons/$dzn_qnty)*$finishcompleted;
				$gmts_proc_loss=$fin_used_arr[$row[csf('id')]]-$effective_fab_uses;
				$gmts_proc_loss_perc=($gmts_proc_loss/$fin_used_arr[$row[csf('id')]])*100;
				//(Knit Pros. Loss+Dye Pros. Loss+Gmts Pros. Loss)
				$tot_fab_loss=$knit_proc_loss+$dye_fin_proc_loss+$gmts_proc_loss;
				$tot_fab_loss_perc=$fab_proc_loss_perc+$gmts_proc_loss_perc;
				$grtotfabloss+=$tot_fab_loss;
				$gmts_left_over=$finishcompleted-$ex_factory_qnty;
				$grey_left_over=$grey_produced_arr[$row[csf('id')]]-$grey_used_arr[$row[csf('id')]];

				$fin_left_over=$fin_produced_arr[$row[csf('id')]]-$fin_used_arr[$row[csf('id')]];
				$avg_process_loss=($dye_fin_proc_loss_perc*$grey_left_over)/100;
				$opportunity_lost=(floor((($grey_left_over-$avg_process_loss+$fin_left_over)/$fin_fab_cons)*12)+$gmts_left_over)*$unit_price;
				$totfinishcomplete+=$finishcompleted;
				$totexfactoryqty+=$ex_factory_qnty;
				$gryarnissuebal+=$yarn_issue_bal;

				$tot_yarn_used+=$yarn_used_arr[$row[csf('id')]];
				$tot_grey_used+=$grey_used_arr[$row[csf('id')]];
				$tot_kniting_balance+=$kniting_balance;
				$tot_grey_stock_in_hand+=$grey_stock_in_hand;
				$totcuttoship+=$actual_cut-$ex_factory_qnty;
				$grandordertoshipqnty+=$order_qnty_in_pcs-$ex_factory_qnty;
				$grandpercentage+=(($ex_factory_qnty/$order_qnty_in_pcs)*100.0);
				$totleftoveroutsanding=($actual_cut-$ex_factory_qnty)-$gatepassresult-$leftover;
				$grandtotoveroutstanding+=$totleftoveroutsanding;
				$totrcvquantityarr+=$rcv_quantity_arr[$row[csf('id')]];
				$fin_left_over=$rcv_quantity_arr[$row[csf('id')]]-$fin_used_arr[$row[csf('id')]];
				$totfinleftover+=$fin_left_over;


				$tot_grey_prod+=$grey_produced_arr[$row[csf('id')]];
				$tot_fin_used+=$fin_used_arr[$row[csf('id')]];
				$tot_fin_prod+=$fin_produced_arr[$row[csf('id')]];
				$tot_effective_fab_uses+=$effective_fab_uses;
				$tot_gmts_left_over+=$gmts_left_over;
				$tot_grey_left_over+=$grey_left_over;
				$tot_fin_left_over+=$fin_left_over;
				$tot_opportunity_lost+=$opportunity_lost;
				$color="";


				if(str_replace("'","",$shipping_status) !=3){
					$fin_left_over=$grey_left_over=$gmts_left_over=	0;
					$tot_fin_left_over=$tot_grey_left_over=$tot_gmts_left_over=	0;
				}

				if($possible_cut > $actual_cut)
				{
					$color="red";
				}

				$html.="<tr bgcolor='$bgcolor' onclick=change_color('tr_$i','$bgcolor') id=tr_$i>";
				$html.='<td width="40">'.$i.'</td>
				        <td width="70"><p>'.$buyer_arr[$row[csf('buyer_name')]].'</p></td>
						<td width="110"><p>'.$row[csf('style_ref_no')].'</p></td>
						<td width="130"><p>'.$row[csf('po_number')].'</p></td>
						<td width="70"><p>'.$row[csf('job_no_prefix_num')].'</p></td>
						<td width="50" align="center"><p>'.$row[csf('year')].'</p></td>
						<td width="130"><p>'.$gmts_item.'</p></td>
						<td width="50" align="center">'.$unit_of_measurement[$row[csf('order_uom')]].'&nbsp;</td>
						<td width="88" style="padding-right:2px" align="right">'.$order_qnty_in_pcs.'</td>
						<td width="88" style="padding-right:2px" align="right">'.$plan_cut_qnty.'</td>
						<td width="80" align="center">'.change_date_format($row[csf('pub_shipment_date')]).'&nbsp;</td>
						<td width="80" align="right">'.fn_number_format($fin_cons,2,'.','').'&nbsp;</td>
						<td width="80" align="right">'.fn_number_format($grey_cons,2,'.','').'&nbsp;</td>
						<td width="80" align="center">'.fn_number_format($total_required_qty,2,'.','').'&nbsp;</td>
						<td width="80" align="center">'.fn_number_format($grey_fab_qnty,2,'.','').'&nbsp;</td>
						<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','yarn_issue_popup'".')">'.fn_number_format($yarn_used_arr[$row[csf('id')]],2,'.','').'</a></td>
						<td width="98" align="right" style="padding-right:2px">'.fn_number_format($yarn_issue_bal,2,'.','').'&nbsp;</td>
						<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','grey_produced_popup'".')">'.fn_number_format($grey_produced_arr[$row[csf('id')]],2,'.','').'</a></td>
						<td width="98" align="right" style="padding-right:2px">'.fn_number_format($kniting_balance,2,'.','').'</td>
						<td width="78" align="right" style="padding-right:2px">'.fn_number_format($knit_proc_loss_perc,2,'.','').'</td>
						<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','grey_used_popup'".')">'.fn_number_format($grey_used_arr[$row[csf('id')]],2,'.','').'</a></td>
						<td width="98" align="right" style="padding-right:2px">'.fn_number_format($grey_stock_in_hand,2,'.','').'</td>

						<td width="98" align="right" style="padding-right:2px">'.fn_number_format($fin_produced_arr[$row[csf('id')]],2,'.','').'</td>
						<td width="80" align="right">'.fn_number_format($dye_fin_proc_loss_perc,2,'.','').'</td>
						<td width="80" align="right">'.fn_number_format($fab_proc_loss_perc,2,'.','').'</td>
						<td width="80" align="right">'.fn_number_format($rcv_quantity_arr[$row[csf('id')]],2,'.','').'</td>
						<td width="98" align="right" style="padding-right:2px"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','issue_toCut_popup'".')">'.fn_number_format($fin_used_arr[$row[csf('id')]],2,'.','').'</a></td>
						<td width="98" align="right" style="padding-right:2px">'.fn_number_format($fin_left_over,2,'.','').'</td>
						<td width="100" align="right">'.fn_number_format($possible_cut,0,'.','').'</td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($actual_cut,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($sewinginput,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($inputbalance,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($sewingoutput,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($outputbalance,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($washsent,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($washbalance,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($washrcv,0,'.','').'</p></td>
						<td width="100" align="right" bgcolor="'.$color.'">'.fn_number_format($washrcvbalance,0,'.','').'</p></td>

						<td width="100" align="right">'.fn_number_format($finishcompleted,0,'.','').'</td>
						<td width="100" align="right">'.fn_number_format($fingmtbalance,0,'.','').'</td>
						<td width="100" align="right">
						<a href="##" onclick="generate_ex_factory_popup('.$company_name.",'".$row[csf('id')]."','ex_factory_popup'".')">'.fn_number_format($ex_factory_qnty,0,'.','').'</a>
						</td>
						<td width="100" align="right">
						'.fn_number_format($ex_factory_balance,0,'.','').'
						</td>
						<td width="100" align="right">'.fn_number_format($actual_cut-$ex_factory_qnty,0,'.','').'</td>

						<td width="60" align="right">'.fn_number_format(($ex_factory_qnty/$actual_cut)*100,0,'.','').'</td>
						<td width="100" align="right">'.fn_number_format($order_qnty_in_pcs-$ex_factory_qnty,0,'.','').'</td>
						<td width="60" align="right">'.fn_number_format(($ex_factory_qnty/$order_qnty_in_pcs)*100,0,'.','').'</td>
						<td width="60" align="right"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','gatepass_popup'".')">'.fn_number_format($gatepassresult,0,'.','').'</a></td>
						<td width="60" align="right"><a href="##" onclick="fnc_generate_popup('.$company_name.",'".$row[csf('id')]."','leftover_popup'".')">'.fn_number_format($leftover,0,'.','').'</a></td>
						<td width="60" align="right">'.fn_number_format($totleftoveroutsanding,0,'.','').'</td>

						<td><p>'.$shipment_status[$row[csf('shiping_status')]].'</p></td>
					</tr>';
				$i++;
			}

			$tot_knit_proc_loss=$tot_yarn_used-$tot_grey_prod;
			$tot_knit_proc_loss_perc=($tot_knit_proc_loss/$tot_yarn_used)*100;
			$tot_dye_fin_proc_loss=$tot_grey_used-$tot_fin_prod;
			$tot_dye_fin_proc_loss_perc=($tot_dye_fin_proc_loss/$tot_grey_used)*100;
			$tot_fab_proc_loss=$tot_yarn_used-$tot_fin_prod;

			$tot_fab_proc_loss_perc=($tot_fab_proc_loss/$tot_yarn_used)*100;
			$tot_gmts_proc_loss=$tot_fin_used-$tot_effective_fab_uses;

			$tot_gmts_proc_loss_perc=($tot_gmts_proc_loss/$tot_fin_used)*100;
			$grand_tot_fab_loss=$tot_knit_proc_loss+$tot_dye_fin_proc_loss+$tot_gmts_proc_loss;
			$grand_fab_loss_perc=$tot_fab_proc_loss_perc+$tot_gmts_proc_loss_perc;
			$gmts_prod_in_kg=$tot_fin_used-$tot_effective_fab_uses;
			$bgcolor1='#FFFFFF';
			$bgcolor2='#E9F3FF';

			?>
			<div style="width:3000px">
				<fieldset style="width:100%;">
					<table width="2080">
						<tr class="form_caption">
							<td colspan="20" align="center">Process Loss Report</td>
						</tr>
						<tr class="form_caption">
							<td colspan="20" align="center"><? echo $company_arr[$company_name]; ?></td>
						</tr>
					</table>
					<b><u>Summary</u></b>
					<table id="table_header" class="rpt_table" width="530" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th width="40">SL</th>
							<th width="120">Particulars</th>
							<th width="140">Fabric Loss Qty. (Kg)</th>
							<th width="100">Loss %</th>
							<th>Left Over</th>
						</thead>
						<tbody>
							<tr bgcolor="<? echo $bgcolor1; ?>" onClick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
								<td>1</td>
								   <td>Knitting</td>
								   <td align="right"><? echo fn_number_format($tot_knit_proc_loss,2); ?></td>
								   <td align="right"><? echo fn_number_format($tot_knit_proc_loss_perc,2); ?></td>
								   <td align="right"><? echo fn_number_format($grey_left_over,2)." (Kg)"; ?></td>
							</tr>
							<tr bgcolor="<? echo $bgcolor2; ?>" onClick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
								<td>2</td>
								   <td>Dyeing + Finishing</td>
								   <td align="right"><? echo fn_number_format($tot_dye_fin_proc_loss,2); ?></td>
								   <td align="right"><? echo fn_number_format($tot_dye_fin_proc_loss_perc,2); ?></td>
								   <td align="right"><? echo fn_number_format($tot_fin_left_over,2)." (Kg)"; ?></td>
							</tr>
							<tr bgcolor="<? echo $bgcolor1; ?>"  onclick="change_color('tr1st_3','<? echo $bgcolor1; ?>')" id="tr1st_3">
								<td>3</td>
								   <td>Gmts. Production</td>
								   <td align="right"><? echo fn_number_format($gmts_prod_in_kg,2); ?></td>
								   <td align="right"><? echo fn_number_format($tot_gmts_proc_loss_perc,2); ?></td>
								   <td align="right"><? echo fn_number_format($tot_gmts_left_over,0)." (Pcs)"; ?></td>
							</tr>
						</tbody>
						   <tfoot>
							<th width="40"></th>
							<th width="120" align="right"></th>
							<th width="110" align="right"><? echo fn_number_format($tot_knit_proc_loss+$tot_dye_fin_proc_loss+$gmts_prod_in_kg,2); ?></th>
							<th width="100" align="right"><? echo fn_number_format($tot_knit_proc_loss_perc+$tot_dye_fin_proc_loss_perc+$tot_gmts_proc_loss_perc,2); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<br>
					<b>Total Opportunity Loss (USD): <? echo fn_number_format($tot_opportunity_lost,2); ?></b>
					<table style="margin-top:10px" id="table_header_1" class="rpt_table" width="4510" cellpadding="0" cellspacing="0" border="1" rules="all">
						<thead>
							<th width="40">SL</th>
							<th width="70">Buyer</th>
							<th width="110">Style Ref.</th>
							<th width="130">Order No</th>
							<th width="70">Job No</th>
							<th width="50">Year</th>
							<th width="130">Garments Item</th>
							<th width="50">UOM</th>
							<th width="90">Qnty (Pcs)</th>
							<th width="90">Exess Cut Qty</th>
							<th width="80">Shipment Date</th>
							<th width="80">Finish Consumption</th>
							<th width="80">Grey Consumption</th>
							<th width="80">Fabric Require Grey(Pre-Cost)</th>
							<th width="80">Fabric Booking Qty-Grey</th>
							<th width="100">Yarn Issued</th>
							<th width="100">Yarn Issue Balance</th>
							<th width="100">Grey Produced</th>
							<th width="100">Knitting Balance</th>
							<th width="80" title="((Yarn Used - Grey Produced)/Yarn Used)*100">Knit Pros. Loss %</th>
							<th width="100">Grey Issued</th>
							<th width="100">Grey Fabric Stock In Hand</th>
							<th width="100">Finish Fabric Produced</th>
							<th width="80" title="((Grey Used - Finish Fabric Produced)/Grey Used)*100">Dye/Fin Pros. Loss %</th>
							<th width="80" title="((Yarn Used - Finish Fabric Produced)/Yarn Used)*100">Fab. Pros. Loss%</th>
							<th width="80">Fabric Rcvd.Store</th>
							<th width="100">Fab. Issue to Cut</th>
							<th width="100">Fin Stock In Hand</th>

							<th width="100" title="(Costing Per Pcs/Finish Fab Cons)*Fab. Issue to Cut">Possible Cut Pcs</th>
							<th width="100">Actual Cut Pcs</th>
							<th width="100">Sewing Input</th>
							<th width="100">Input Balance</th>
							<th width="100">Sewing Output</th>
							<th width="100">Output Balance</th>
							<th width="100">Wash Sent</th>
							<th width="100">Wash Sent Balance</th>
							<th width="100">Wash Rcvd</th>
							<th width="100">Wash Rcvd Balance.</th>

							<th width="100" title="Finish Garments Production">Fin GMT- Pcs</th>
							<th width="100">Fin GMT Balance</th>

							<th width="100">Ex-Fact Qty.</th>
							<th width="100">Ex-Factory Balance</th>

							<th width="100">Cut to Ship</th>

							<th width="60">Cut to Ship Percentage</th>
							<th width="100">Order to Ship Qty</th>
							<th width="60">Order to Ship Percentage</th>
							<th width="60">Gate Pass</th>
							<th width="60">Leftover Received</th>
							<th width="60">Leftover Outsanding</th>
							<th>Shipment Status</th>
						</thead>
					</table>
					<div style="width:4532px; max-height:400px; overflow-y:scroll" id="scroll_body">
						<table class="rpt_table" width="4510" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
							<? echo $html; ?>
						</table>
						<table class="rpt_table" width="4510" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
							<tfoot>
								<th width="40"></th>
								<th width="70"></th>
								<th width="110"></th>
								<th width="130"></th>
								<th width="70"></th>
								<th width="50"></th>
								<th width="130"></th>
								<th width="50"></th>
								<th width="90" align="right"><? echo $totorderinpcs; ?></th>
								<th width="90" align="right"><? echo $totplancutqnty; ?></th>
								<th width="80" align="right"></th>
								<th width="80" align="right"><?  echo fn_number_format($totfincons,2);   ?></th>
								<th width="80" align="right"><?  echo fn_number_format($totgreycons,2);   ?></th>
								<th width="80"><?  echo fn_number_format($grtotal,2);?></th>
								<th width="80"><?  echo fn_number_format($gr_grey_fab_qnty,2);?></th>
								<th width="100" align="right"><? echo $tot_yarn_used;?></th>
								<th width="100" align="right"><?echo fn_number_format($gryarnissuebal,2);?></th>
								<th width="100"  align="right"></th>
								<th width="100"  align="right"><?echo fn_number_format($tot_kniting_balance,2); ?></th>

								<th width="80" align="right"><? //echo fn_number_format($tot_knit_proc_loss_perc,2); ?></th>
								<th width="100"  align="right"></th>
								<th width="100"  align="right"><?echo fn_number_format($tot_grey_stock_in_hand,2); ?></th>
								<th width="100"  align="right"></th>
								<th width="80" align="right"><? //echo fn_number_format($tot_dye_fin_proc_loss_perc,2); ?></th>
								<th width="80" align="right"><? //echo fn_number_format($tot_fab_proc_loss_perc,2); ?></th>
								<th width="80" align="right"><? echo fn_number_format($totrcvquantityarr,2);  ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totfinleftover,2);  ?></th>
								<th width="100" align="right"></th>
								<th width="100" align="right"></th>
								<th width="100"  align="right"><? echo fn_number_format($tot_actual_cut,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totalsewinginput,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totinputbalance,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totalsewingoutput,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totaloutputbalance,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totalwashsent,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totalwashbalance,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totalwashrcv,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totalwashrcvbalance,2); ?></th>

								<th width="100"  align="right"><? echo fn_number_format($totfinishcomplete,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totgmtbalance,2); ?></th>

								<th width="100"  align="right"><? echo fn_number_format($totexfactoryqty,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totexfactorybalance,2); ?></th>
								<th width="100"  align="right"><? echo fn_number_format($totcuttoship,2); ?></th>

								<th width="60"></th>
								<th width="100" ><? echo fn_number_format($grandordertoshipqnty,2); ?></th>
								<th width="60"><? echo fn_number_format($grandpercentage,2); ?></th>
								<th width="60"><? echo fn_number_format($totgatepassresult,2); ?></th>
								<th width="60"><? echo fn_number_format($totalleftover,2); ?></th>
								<th width="60"><? echo fn_number_format($grandtotoveroutstanding,2); ?></th>

								<th></th>
							</tfoot>
						</table>
					</div>
			<br><br>
			<!-- Accessories -->
			<div class="summary-container" style="float:left;width:1420px;margin-bottom:10px; margin-left:18px;">
			<div style="float:left;width:640px;">
								<table cellspacing="0" border="1" class="rpt_table"  width="640" rules="all">
									<thead>
										<tr>
											<th colspan="8">Accessories Status</th>
										</tr>
										<tr>
											<th width="150">Item</th>
											<th width="70">UOM</th>
											<th width="70">Req. Qty.</th>
											<th width="70">WO Qty</th>
											<th width="70">Received</th>
											<th width="70">Recv. Balance</th>
											<th width="70">Issued</th>
											<th width="70">Left Over</th>
										</tr>
									</thead>
									<tbody>
										<?
										$tot_req_qty = 0;
										$tot_wo_qty = 0;
										$tot_rcv_qty = 0;
										$tot_rcv_bal = 0;
										$tot_issue_qty = 0;
										$tot_leftovr_qty = 0;
										foreach ($trims_data_array as $trims_id => $r)
										{
											$wo_qty = $trims_wo_array[$trims_id]['wo_qnty'];
											$rcv_qty = $trimsDataArray[$trims_id]['rcv_qty'];
											$issue_qty = $trimsDataArray[$trims_id]['issue_qty'];
											$rcv_bal = $wo_qty - $rcv_qty;
											$lftover = $rcv_qty - $issue_qty;
											?>
											<tr>
												<td><?=$lib_trims_group_name[$trims_id];?></td>
												<td><?=$unit_of_measurement[$r['uom']];?></td>
												<td align="right"><?=$r['req_qty'];?></td>
												<td align="right"><?=number_format($wo_qty,2);?></td>
												<td align="right"><?=number_format($rcv_qty,2);?></td>
												<td align="right"><?=number_format($rcv_bal,2);?></td>
												<td align="right"><?=number_format($issue_qty,2);?></td>
												<td align="right"><?=number_format($lftover,2);?></td>
											</tr>
											<?
											$tot_req_qty += $r['req_qty'];
											$tot_wo_qty += $wo_qty;
											$tot_rcv_qty += $rcv_qty;
											$tot_rcv_bal += $rcv_bal;
											$tot_issue_qty += $issue_qty;
											$tot_leftovr_qty += $lftover;
										}
										?>
									</tbody>
									<tfoot>
										<tr>
											<th>Total</th>
											<th></th>
											<th><?=number_format($tot_req_qty,2) ;?></th>
											<th><?=number_format($tot_wo_qty,2) ;?></th>
											<th><?=number_format($tot_rcv_qty,2) ;?></th>
											<th><?=number_format($tot_rcv_bal,2) ;?></th>
											<th><?=number_format($tot_issue_qty,2) ;?></th>
											<th><?=number_format($tot_leftovr_qty,2) ;?></th>
										</tr>
									</tfoot>
								</table>
							</div>
							<br>
							<td valign="top">
							<!-- ================================ Fabric Summary part =========================== -->
							<div style="float:left;width:400px;margin-left:10px;">
								<table cellspacing="0" border="1" class="rpt_table"  width="400" rules="all">
									<thead>
										<tr>
											<th colspan="4">Fabric Summary-KG/YDS/MTR </th>
										</tr>
										<tr>
											<th width="100">SL</th>
											<th width="130">Particulars</th>
											<th width="70">Fabric Qty</th>
											<th width="100">Loss%</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>1</td>
											<td>Yarn Issued</td>
											<td align="right"><?=fn_number_format($tot_yarn_used,0);?></td>
											<td></td>
										</tr>

										<tr>
											<td>2</td>
											<td>Grey Fabric Production</td>
											<td align="right"><?=fn_number_format($tot_grey_prod,0);?></td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Knitting Balance</b></td>
											<td align="right"><?$totalknititng=$tot_yarn_used-$tot_grey_prod;
											echo fn_number_format($totalknititng,2);?></td>
											<td align="right"><?
											$yarnKniting=$tot_yarn_used;
											$divideKniting=$totalknititng/($yarnKniting*100);echo fn_number_format($divideKniting,2); ?></td>
										</tr>
										<tr>
											<td>3</td>
											<td>Grey Fabric Issued</td>
											<td align="right"><?=fn_number_format($tot_grey_used,0);?></td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Grey Fabric Stock In Hand</b></td>
											<td align="right"><?=fn_number_format($tot_grey_stock_in_hand,2);?></td>
											<td align="right"><?$totgrey=$tot_grey_stock_in_hand;
	                                              $totgreyproduced=$tot_grey_prod;

												$totgreyprecent=$totgrey/$totgreyproduced*100;
												echo fn_number_format($totgreyprecent,2);
											?></td>
										</tr>
										<tr>
											<td>4</td>
											<td>Finish Fabric Produced</td>
											<td align="right"><?=fn_number_format($tot_fin_prod,0);?></td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Grey Fabric Stock In Hand</b></td>
											<td align="right"><?
											  $l16=$tot_grey_used;
											  $l18=$tot_fin_prod;
											  $l19=$l16-$l18;
											  echo fn_number_format($l19,0);
											?></td>
											<td align="right"><?$l20=$l19/$l16*100; echo fn_number_format($l20,0);?></td>
										</tr>
										<tr>
											<td>5</td>
											<td>Finish Fabric Receive</td>
											<td align="right"><?=fn_number_format($totrcvquantityarr,0);?></td>
											<td></td>
										</tr>
										<tr>
											<td>6</td>
											<td>Fabric Issue to Cutting</td>
											<td align="right"><?=fn_number_format($tot_fin_used,0);?></td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Grey Fabric Stock In Hand </b></td>
											<td align="right"><?php
											  $l20=$totrcvquantityarr;
											  $l21=$tot_fin_used;
											  $l22=$l20-$l21;
                                              echo fn_number_format($l22,0);
											?></td>
											<td></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Yarn To Fabric Proess Loss </b></td>
											<td align="right"><?php
											$l23=$tot_grey_stock_in_hand+$totalknititng+$l19+$l22;
											echo $l23;
											?></td>
											<td align="right"><? $l24=$l23/($totalknititng*100); echo fn_number_format($l24,0); ?></td>
										</tr>
										<tr>
											<td></td>
											<td><b> Fabric Leftover </b></td>
											<td align="right"><?php
											$l25=$tot_grey_stock_in_hand+$l22;
											echo fn_number_format($l25,0);
											?></td>
											<td align="right"><??></td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<br>
						<td valign="top">
							<!-- ============================= garments part ================================ -->
							<div style="float:left;width:270px;margin-left:10px;">
								<table cellspacing="0" border="1" class="rpt_table"  width="270" rules="all">
									<thead>
										<tr>
											<th colspan="3">Garments Summary-Pcs</th>
										</tr>
										<tr>
											<th width="30">Sl</th>
											<th width="170">Particulars</th>
											<th width="70">Left Over Qty</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>1</td>
											<td>Sewing Input  Balance</td>
											<td align="right"><?=number_format($totinputbalance,0);?></td>
										</tr>
										<tr>
											<td>2</td>
											<td>Sewing Output Balance</td>
											<td align="right"><?=number_format($totaloutputbalance,0);?></td>
										</tr>
										<tr>
											<td>3</td>
											<td>Wash Sent Balance</td>
											<td align="right"><?=number_format($totalwashbalance,0);?></td>
										</tr>
										<tr>
											<td>4</td>
											<td>Wash Rcv Balance</td>
											<td align="right"><?=number_format($totalwashrcvbalance,0);?></td>
										</tr>
										<tr>
											<td>5</td>
											<td>Finish Gmts Balance</td>
											<td align="right"><?=number_format($totgmtbalance,0);?></td>
										</tr>
										<tr>
											<td>6</td>
											<td>Ex-Factory Balance</td>
											<td align="right"><?=number_format($totexfactorybalance,0);?></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Total Qty (Cut ot Ship)</b></td>
											<td align="right"><?=number_format(($totinputbalance+$totaloutputbalance+$totalwashbalance+$totalwashrcvbalance+$totgmtbalance+$totexfactorybalance),0);?></td>
										</tr>
										<tr>
											<td>10</td>
											<td>Gate Pass</td>
											<td align="right"><?=number_format($totgatepassresult,0);?></td>
										</tr>
										<tr>
											<td>11</td>
											<td>Leftover Received</td>
											<td align="right"><?=number_format($totalleftover,0);?></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Leftover Outstanding</b></td>
											<td align="right"><?$totalcut=$totinputbalance+$totaloutputbalance+$totalwashbalance+$totalwashrcvbalance+$totgmtbalance+$totexfactorybalance;$totleftoveroutstanding=$totalcut-$totgatepassresult-$totalleftover;echo $totleftoveroutstanding;?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</fieldset>
				</div>


	<?
		}

	// 	foreach (glob("../../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	// 	{
	// 		if( @filemtime($filename) < (time()-$seconds_old) )
	// 		@unlink($filename);
	// 	}
	}


}

if($action=="trims_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
    <div>
        <fieldset style="width:600px;">
        <div style="width:600px" align="center">
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                	<th width="40">SL</th>
                    <th width="130">Item Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="80">Rate</th>
                    <th width="110">Trims Cost/Dzn</th>
                    <th>Total Trims Cost</th>
                </thead>
            </table>
            </div>
            <div style="width:620px; max-height:250px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="600" cellpadding="0" cellspacing="0" border="1" rules="all">
                	<?
					$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");

					$dzn_qnty=0;
					if($costing_per==1)
					{
						$dzn_qnty=12;
					}
					else if($costing_per==3)
					{
						$dzn_qnty=12*2;
					}
					else if($costing_per==4)
					{
						$dzn_qnty=12*3;
					}
					else if($costing_per==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}

					$sql="select a.trim_group, a.rate, b.cons from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id='$po_id' and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0";
					$trimsArray=sql_select($sql);
					$i=1;
					foreach($trimsArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td width="40"><? echo $i; ?></td>
							<td width="130"><div style="width:130px; word-wrap:break-word"><? echo $item_library[$row[csf('trim_group')]]; ?></div></td>
							<td width="90" align="right"><? echo fn_number_format($row[csf('cons')],2); ?></td>
							<td width="80" align="right"><? echo fn_number_format($row[csf('rate')],2); ?></td>
							<td width="110" align="right">
								<?
                                    $trims_cost_per_dzn=$row[csf('cons')]*$row[csf('rate')];
                                    echo fn_number_format($trims_cost_per_dzn,2);
									$tot_trims_cost_per_dzn+=$trims_cost_per_dzn;
                                ?>
                            </td>
							<td align="right">
								<?
                                	$trims_cost=($po_qnty/$dzn_qnty)*$trims_cost_per_dzn;
									echo fn_number_format($trims_cost,2);
									$tot_trims_cost+=$trims_cost;
                                ?>
                            </td>
						</tr>
					<?
					$i++;
					}
					?>
                	<tfoot>
                        <th colspan="4">Total</th>
                        <th><? echo fn_number_format($tot_trims_cost_per_dzn,2); ?></th>
                        <th><? echo fn_number_format($tot_trims_cost,2); ?></th>
                    </tfoot>
                </table>
            </div>
        </fieldset>
    </div>
<?
}

if($action=="other_cost")
{
	echo load_html_head_contents("Report Info","../../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
?>
    <div align="center">
        <fieldset style="width:600px;">
            <table class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="120">Job No</th>
                    <th width="200">Order No</th>
                    <th>Order Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                	<td align="center"><? echo $job_no; ?></td>
                    <td><? echo $po_no; ?></td>
                    <td align="right"><? echo fn_number_format($po_qnty,0); ?></td>
                </tr>
            </table>
            <table style="margin-top:10px" class="rpt_table" width="470" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <th width="200">Particulars</th>
                    <th width="90">Cost/Dzn</th>
                    <th>Total Cost</th>
                </thead>
				<?
                $costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='$job_no' and status_active=1 and is_deleted=0");

                $dzn_qnty=0;
                if($costing_per==1)
                {
                    $dzn_qnty=12;
                }
                else if($costing_per==3)
                {
                    $dzn_qnty=12*2;
                }
                else if($costing_per==4)
                {
                    $dzn_qnty=12*3;
                }
                else if($costing_per==5)
                {
                    $dzn_qnty=12*4;
                }
                else
                {
                    $dzn_qnty=1;
                }

                $sql="select common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='$job_no' and status_active=1 and is_deleted=0";
                $fabriccostArray=sql_select($sql);
                ?>
                <tr bgcolor="#E9F3FF">
                    <td>Commercial Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('comm_cost')],2); ?></td>
                    <td align="right">
                        <?
                            $comm_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('comm_cost')];
                            echo fn_number_format($comm_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Lab Test Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('lab_test')],2); ?></td>
                    <td align="right">
                        <?
                            $lab_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('lab_test')];
                            echo fn_number_format($lab_cost,2);
                        ?>
                    </td>
                </tr>
                 <tr bgcolor="#E9F3FF">
                    <td>Inspection Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('inspection')],2); ?></td>
                    <td align="right">
                        <?
                            $inspection_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('inspection')];
                            echo fn_number_format($inspection_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#FFFFFF">
                    <td>Freight Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('freight')],2); ?></td>
                    <td align="right">
                        <?
                            $freight_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('freight')];
                            echo fn_number_format($freight_cost,2);
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#E9F3FF">
                    <td>Common OH Cost</td>
                    <td align="right"><? echo fn_number_format($fabriccostArray[0][csf('common_oh')],2); ?></td>
                    <td align="right">
                        <?
                            $common_oh_cost=($po_qnty/$dzn_qnty)*$fabriccostArray[0][csf('common_oh')];
                            echo fn_number_format($common_oh_cost,2);

							$tot_cost_per_dzn=$fabriccostArray[0][csf('comm_cost')]+$fabriccostArray[0][csf('lab_test')]+$fabriccostArray[0][csf('inspection')]+$fabriccostArray[0][csf('freight')]+$fabriccostArray[0][csf('common_oh')];
							$tot_cost=$comm_cost+$lab_cost+$inspection_cost+$freight_cost+$common_oh_cost;
                        ?>
                    </td>
                </tr>
                <tfoot>
                    <th>Total</th>
                    <th><? echo fn_number_format($tot_cost_per_dzn,2); ?></th>
                    <th><? echo fn_number_format($tot_cost,2); ?></th>
                </tfoot>
            </table>
        </fieldset>
    </div>
<?
}

//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>

                     </tr>
                </thead>
            </table>
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
                //$ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";
                //echo $ex_fac_sql;
				$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date,
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");


                $sql_dtls=sql_select($exfac_sql);

                foreach($sql_dtls as $row_real)
                {
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td>
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <?
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo fn_number_format($rec_qnty,2); ?></th>
                    <th><? echo fn_number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo fn_number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div>
		</fieldset>
	</div>
	<?
    exit();
}

if($action=="yarn_issue_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );

	?>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

					$issue_to="";
					if($row[csf('knit_dye_source')]==1)
					{
						$issue_to=$company_library[$row[csf('knit_dye_company')]];
					}
					else
					{
						$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					}


                    $yarn_issued=$row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<?
								if($row[csf('knit_dye_source')]!=3)
								{
									echo fn_number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<?
								if($row[csf('knit_dye_source')]==3)
								{
									echo fn_number_format($yarn_issued,2);
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo fn_number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo fn_number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo fn_number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
                $sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$return_from="";
					if($row[csf('knitting_source')]==1)
					{
						$return_from=$company_library[$row[csf('knitting_company')]];
					}
					else
					{
						$return_from=$supplier_details[$row[csf('knitting_company')]];
					}

                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<?
								if($row[csf('knitting_source')]!=3)
								{
									echo fn_number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<?
								if($row[csf('knitting_source')]==3)
								{
									echo fn_number_format($yarn_returned,2);
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo fn_number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo fn_number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tfoot>
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo fn_number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>
		</div>
	</fieldset>
	<?
    exit();
}

if($action=="grey_produced_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1037px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="125">Receive Id</th>
                    <th width="95">Receive Basis</th>
                     <th width="150">Product Details</th>
                    <th width="110">Booking/PI/ Production No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="65">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details');

                   $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis in (9,10) and a.entry_form in (22,58) and c.entry_form in (22,58) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        $total_receive_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<?
                                	if($row[csf('knitting_source')]!=3)
									{
										echo fn_number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<?
                                	if($row[csf('knitting_source')]==3)
									{
										echo fn_number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo fn_number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?>&nbsp;</p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo fn_number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo fn_number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo fn_number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
	<?
    exit();
}

if($action=="grey_used_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:970px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="10"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="100">Issue To</th>
                        <th width="115">Booking No</th>
                        <th width="90">Batch No</th>
                        <th width="90">Batch Color</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qnty (In)</th>
                        <th>Issue Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:967px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
                    <?
					$batch_color_details=return_library_array( "select  id,color_id from pro_batch_create_mst", "id", "color_id");

                    $i=1; $issue_to='';
                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        if($row[csf('knit_dye_source')]==1)
                        {
                            $issue_to=$company_library[$row[csf('knit_dye_company')]];
                        }
                        else if($row['knit_dye_source']==3)
                        {
                            $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
                        }
                        else
                            $issue_to="&nbsp;";

                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="100"><p><? echo $issue_to; ?></p></td>
                            <td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
                            <td width="90"><p><? echo $color_array[$batch_color_details[$row[csf('batch_no')]]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knit_dye_source')]!=3)
                                    {
                                        echo fn_number_format($row[csf('quantity')],2);
                                        $total_issue_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knit_dye_source')]==3)
                                    {
                                        echo fn_number_format($row[csf('quantity')],2);
                                        $total_issue_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="8" align="right">Total</th>
                            <th align="right"><? echo fn_number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo fn_number_format($total_issue_qnty_out,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="8" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo fn_number_format($total_issue_qnty+$total_issue_qnty_out,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
	</fieldset>
	<?
    exit();
}
if($action=="gatepass_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<?
	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";
	$sample_arr=return_library_array(" select id,sample_name from lib_sample where status_active=1 order by sample_name",'id','sample_name');


    ?>
       <div style="width:1200px;padding: 10px;" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">

			</table>
		</div>

		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
				<thead>
					<tr>
						<th width="110">Buyer</th>
						<th width="100">Style Ref.</th>
						<th width="100">Order No</th>
						<th width="110">Job No </th>
						<th width="110">Year</th>

					</tr>
				</thead>
				<?
				$sqlcolorsize="SELECT a.buyer_name,a.style_ref_no,b.po_number,to_char(a.insert_date,'YYYY') as insert_year,a.job_no from wo_po_details_master a,wo_po_break_down
				b where a.id=b.job_id and b.id in($order_id) and a.status_active=1 and b.status_active=1";

				$res=sql_select($sqlcolorsize);
				$colorsizearr=array();


				 ?>
				<tbody>
					<?
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<?=$bgcolor;?>" >
						<td><p><? echo $buyer_arr[$res[0][csf('buyer_name')]]; ?></p></td>
						<td><p><? echo $res[0][csf('style_ref_no')]; ?></p></td>
						<td><p><? echo $res[0][csf('po_number')]; ?></p></td>
						<td><p><? echo $res[0][csf('job_no')];?></p></td>
						<td><p><? echo $res[0][csf('insert_year')]; ?></p></td>
					</tr>
				</tbody>
			</table>
			<br><br>
			<table border="1" class="rpt_table" rules="all" width="1080" cellpadding="0" cellspacing="0" align="center">
				<thead>

					<tr>
						<th width="30">Sl</th>
						<th width="110">Sample Type</th>
						<th width="110">Gate Pass No</th>
						<th width="70">Prepared date & Time</th>
						<th width="100">Description</th>
						<th width="80">Quantity</th>
						<th width="80">Uom</th>
						<th width="200">Sent to</th>
						<th width="50">Sent By</th>
						<th width="50">Carried By</th>
						<th width="80">Get Pass User</th>
						<th width="">Remarks</th>
					</tr>
				</thead>
				<?

                $inventorysql="SELECT a.sample_id,a.quantity,a.insert_date,a.uom,a.item_description,b.get_pass_no,b.sent_by,b.sent_to,b.carried_by,a.inserted_by from inv_gate_pass_mst b,inv_gate_pass_dtls a where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.buyer_order_id in($order_id) and a.sample_id>0";

				// echo $inventorysql;
				$inventoryarr=array();
				$mainsql=sql_select($inventorysql);
				?>
				<tbody>

						<?
						$i=1;
						foreach($mainsql as $value){

							if ($i%2==0)
							$bgcolor="#E9F3FF";
							else
							$bgcolor="#FFFFFF";
							?>
                            <tr bgcolor="<?=$bgcolor;?>">
							<td><p><? echo $i; ?></p></td>
							<td><p><? echo $sample_arr[$value[csf('sample_id')]]; ?></p></td>
							<td><p><?  echo $value[csf("gate_pass_no")]; ?></p></td>
							<td><p><? echo $value[csf('insert_date')];?></p></td>
							<td><p><? echo $value[csf("item_description")];?></p></td>
							<td><p><? echo  number_format($value[csf('quantity')]); ?></p></td>
							<td><p><? echo $unit_of_measurement[$value[csf('uom')]]; ?></p></td>
							<td align="center"><p><? echo $value[csf('sent_to')];   ?></p></td>
							<td align="center"><p><?  echo $value[csf('sent_by')]; ?></p></td>
							<td align="center"><p><? echo $value[csf('carried_by')]; ?></p></td>
							<td align="right" ><p><? echo $value[csf('inserted_by')]; ?> &nbsp;</p></td>
							<td align="left"><p><??></p></td>

							</tr>
							<?
							$i++;

						}

			           ?>
				</tbody>

			</table>
		</table>

	<?
    exit();
}
if($action=="leftover_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$cbo_year=str_replace("'","",$cbo_year);
	if(trim($cbo_year)!=0)
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		else $year_cond="";
	}
	else $year_cond="";

	$sqlcolorsize="SELECT a.buyer_name,a.style_ref_no,b.po_number,to_char(a.insert_date,'YYYY') as insert_year,a.job_no from wo_po_details_master a,wo_po_break_down
					b where a.id=b.job_id and b.id in($order_id) and a.status_active=1 and b.status_active=1";

	$res=sql_select($sqlcolorsize);
	// echo "<pre>";print_r($res);
	$colorsizearr=array();
	//=======================Query For Leftover Popup=====================//
	$leftoverpopupsql="SELECT a.sys_number,a.leftover_date, a.order_type,b.po_break_down_id, b.category_id,(case when a.goods_type=1 then b.total_left_over_receive else 0 end) as good_gmts,(case when a.goods_type=2 then b.total_left_over_receive else 0 end) as damage_gmts, b.total_left_over_receive
	from pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b
	where a.id=b.mst_id and b.po_break_down_id in($order_id) and a.status_active=1 and a.is_deleted=0 ";
	//  echo $leftoverpopupsql;
	$leftsql=sql_select($leftoverpopupsql);
	$leftpopuparr=array();$categoryarr=array();

	foreach($leftsql as $value)
	{
		$categoryarr[$value[csf('sys_number')]][$value[csf('leftover_date')]][$value[csf('order_type')]][$value[csf('category_id')]]['receive']+=$value[csf('total_left_over_receive')];
		$categoryarr[$value[csf('sys_number')]][$value[csf('leftover_date')]][$value[csf('order_type')]]['good_gmts']+=$value[csf('good_gmts')];
		$categoryarr[$value[csf('sys_number')]][$value[csf('leftover_date')]][$value[csf('order_type')]]['damage_gmts']+=$value[csf('damage_gmts')];
		$totalreject+=$categoryarr[$value[csf('sys_number')]][$value[csf('leftover_date')]][$value[csf('order_type')]][$value[csf('category_id')]]['receive'];

	}
	$categories = array(1 => 'A', 2 => 'B',3 => 'C');

	//   echo "<pre>";  print_r($categoryarr);  echo "</pre>";
	$tbl_width = 510+(count($categories)*50);

    ?>


	<div id="scroll_body" align="center">
		<table border="1" class="rpt_table" rules="all" width="550" cellpadding="0" cellspacing="0" align="center" style="padding: 10px 0;">
			<thead>
				<tr>
					<th width="110">Buyer</th>
					<th width="100">Style Ref.</th>
					<th width="100">Order No</th>
					<th width="110">Job No </th>
					<th width="110">Year</th>

				</tr>
			</thead>


			<tbody>
				<tr>
					<td><p><? echo $buyer_arr[$res[0][csf('buyer_name')]]; ?></p></td>
					<td><p><? echo $res[0][csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $res[0][csf('po_number')]; ?></p></td>
					<td><p><? echo $res[0][csf('job_no')];?></p></td>
					<td><p><? echo $res[0][csf('insert_year')]; ?></p></td>
				</tr>
			</tbody>
		</table>
	</div>
	<br><br>
	<table width="<?=$tbl_width;?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th rowspan="2" width="30">SL</th>
				<th rowspan="2" width="60">Receive Date</th>
				<th rowspan="2" width="100">Receive Id</th>
				<th rowspan="2" width="80">Order Type</th>
				<th colspan="<?=count($categories);?>">Category</th>
				<th rowspan="2" width="80">Good Gmt Total</th>
				<th rowspan="2" width="80">Damage Gmt</th>
				<th rowspan="2" width="80">Total Rcv Qty</th>
			</tr>
			<tr>
				<?
				foreach ($categories as $key => $val)
				{
					?>
					<th width="50"><?=$val;?></th>
					<?
				}
				?>
			</tr>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($categoryarr as $sys_number=>$sys_data)
			{
				foreach($sys_data as $leftover_date=>$leftover_data)
				{
					foreach($leftover_data as $order_type=>$row)
					{
						$total=0;
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?=$bgcolor;?>">
							<td><? echo $i;?></td>
							<td><? echo $leftover_date?></td>
							<td><? echo $sys_number; ?></td>
							<td><? echo $order_source[$order_type];?></td>
							<?
							foreach ($categories as $cat_id => $v)
							{
								?>
								<td><?=$row[$cat_id]['receive']; $total+=$row[$cat_id]['receive'];?></td>
								<?
							}
							?>
							<td><?=$row['good_gmts'];?></td>
							<td><?=$row['damage_gmts'];?></td>
							<td><?=$total;?></td>
						</tr>
						<?
						$i++;
					}
				}
			}
			?>
		</tbody>
	</table>
	<?
    exit();
}


if($action=="issue_toCut_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<fieldset style="width:740px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Issue No</th>
                    <th width="100">Challan No</th>
                    <th width="80">Issue Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_issue_to_cut_qnty=0;
                    $sql="select a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";

                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="120"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo fn_number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total Issue</th>
                        <th align="right"><? echo fn_number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>
           <!-- <table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue Return Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Issue Rtn No</th>
                    <th width="100">Challan No</th>
                    <th width="80">Return Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Return Qty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">-->
                    <?
					/*$j=1; $total_ret_qnty=0;
                    $sql_ret="select a.recv_number, a.receive_date, b.batch_id_from_fissuertn, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46,52) and c.entry_form in (46,52) and b.transaction_type in (3,4) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.challan_no, b.batch_id_from_fissuertn, b.prod_id";
                    $result_ret=sql_select($sql_ret);
        			foreach($result_ret as $row)
                    {
                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                        $total_ret_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="trr_<? echo $j;?>">
                            <td width="50"><? echo $j; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="120"><p><? echo $batch_details[$row[csf('batch_id_from_fissuertn')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }*/
                    ?>
                    <!--<tfoot>
                        <tr>
                            <th colspan="5" align="right">Total Return</th>
                            <th align="right"><?// echo number_format($total_ret_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="5" align="right">Total Issue to Cut</th>
                            <th align="right"><? //$tot_iss_to_cut=$total_issue_to_cut_qnty-$total_ret_qnty; echo number_format($tot_iss_to_cut,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>-->
            </div>
        </div>
	</fieldset>
	<?
    exit();
}

disconnect($con);
?>